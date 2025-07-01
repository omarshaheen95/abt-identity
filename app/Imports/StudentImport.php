<?php

namespace App\Imports;

use App\Http\Requests\Manager\ImportStudentFileRequest;
use App\Models\Level;
use App\Models\StudentImportFile;
use App\Rules\StudentNameRule;
use App\Models\Student;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

HeadingRowFormatter::default('none');

class StudentImport implements ToModel, SkipsOnFailure, SkipsOnError, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    // Import operation modes
    const MODE_CREATE = 'create';
    const MODE_UPDATE = 'update';
    const MODE_DELETE = 'delete';

    // Gender constants
    const GENDER_MALE = [1, 'b', 'boy', 'Boy', 'BOY', 'm', 'male', 'Male', 'MALE'];
    const GENDER_FEMALE = [2, 'g', 'girl', 'Girl', 'GIRL', 'f', 'female', 'Female', 'FEMALE'];
    const GENDER = [1, 'b', 'boy', 'Boy', 'BOY', 'm', 'male', 'Male', 'MALE', 2, 'g', 'girl', 'Girl', 'GIRL', 'f', 'female', 'Female', 'FEMALE'];

    // Boolean constants for clarity
    const YES = [1, 'yes', 'true', 'on', '1', 'y', 'Y', 'Yes', 'TRUE', 'ON', 'YES'];
    const NO = [0, 'no', 'false', 'off', '0', 'n', 'N', 'No', 'FALSE', 'OFF', 'NO'];
    const YesAndNo = [1, 'yes', 'true', 'on', '1', 'y', 'Y', 'Yes', 'TRUE', 'ON', 'YES', 0, 'no', 'false', 'off', '0', 'n', 'N', 'No', 'FALSE', 'OFF', 'NO'];

    // Instance properties
    private $file;
    private $abt_id;
    private $mode;
    private $request;
    private $row_num = 1;

    // Counters
    private $created_rows_count = 0;
    private $updated_rows_count = 0;
    private $deleted_rows_count = 0;
    private $failed_rows_count = 0;

    // Error tracking
    private $error = null;
    private $failures = [];

    // Cached data
    private $levels = [];

    //Default search column in findStudent method
    private $searchColumn = 'student_id';

    /**
     * Constructor
     *
     * @param StudentImportFile $importStudentFile
     * @param int|null $abt_id
     * @param array $request
     * @param string $mode Operation mode: 'add', 'update', or 'delete'
     */
    public function __construct(
        StudentImportFile $importStudentFile, Request $request
    )
    {
        $this->file = $importStudentFile;
        $this->abt_id = $this->file->with_abt_id ? Student::query()->max('abt_id'):null;
        $this->request = $request;
        $this->mode = $this->file->process_type;

        if ($this->mode != self::MODE_DELETE) {
            $this->loadLevels();
        }
        if ($this->mode != self::MODE_CREATE) {
            $this->searchColumn = $request->get('search_by_column', 'student_id');
        }
    }

    /**
     * Load levels based on the year from request
     */
    private function loadLevels()
    {
        $year = $this->file->year_id;
        $this->levels = Level::query()->when($year, function ($query) use ($year) {
            $query->where('year_id', $year);
        })->get();
    }

    /**
     * Prepare the headers and values by trimming spaces
     *
     * @param array $row
     * @return array
     */
    public function prepareForValidation(array $row)
    {
        $trimmedKeys = array_map('trim', array_keys($row));
        $trimmedValues = array_map('trim', array_values($row));
        return array_combine($trimmedKeys, $trimmedValues);
    }

    /**
     * Main method to process each row
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->row_num++;

        try {
            switch ($this->mode) {
                case self::MODE_DELETE:
                    return $this->deleteStudent($row);
                case self::MODE_UPDATE:
                    return $this->updateStudent($row);
                case self::MODE_CREATE:
                    return $this->createStudent($row);
                default:
                    throw new Exception("Invalid import mode: {$this->mode}");
            }
        } catch (Exception $e) {
            $this->logFailure("StudentImport error at row {$this->row_num}: {$e->getMessage()}", $row);
            return null;
        }
    }

    /**
     * Delete student operation
     *
     * @param array $row
     * @return Student|null
     */
    private function deleteStudent(array $row)
    {
        if ($this->searchColumn == 'username') {
            if (!$this->hasValue($row, 'Username')){
                $this->logFailure("Username is required for update operation", $row);
                return null;
            }
            $key = $row['Username'];
        }else{
            if (!$this->hasValue($row, 'Student ID')) {
                $this->logFailure("Student ID is required for update operation", $row);
                return null;
            }
            $key = $row['Student ID'];
        }
        $student = $this->findStudent($key);

        if ($student) {
            if ($this->request->get('delete_type') == 'delete_assessments')
            {
                $terms = $student->student_terms()->whereHas('term', function ($query) use ($key) {
                    $query->whereIn('round', $this->request->get('rounds_deleted_assessments', []));
                })->get();
                $terms->each(function ($item) {
                    $item->delete();
                });
                $this->deleted_rows_count++;
                return $student;
            } else {
                $student->delete();
                $this->deleted_rows_count++;
                return $student;
            }
        }

        $this->logFailure("Student not found for deletion", $row);
        return null;
    }

    /**
     * Update student operation
     *
     * @param array $row
     * @return Student|null
     */
    private function updateStudent(array $row)
    {
        if ($this->searchColumn == 'username') {
            if (!$this->hasValue($row, 'Username')){
                $this->logFailure("Username is required for update operation", $row);
                return null;
            }
            $key = $row['Username'];
        }else{
            if (!$this->hasValue($row, 'Student ID')) {
                $this->logFailure("Student ID is required for update operation", $row);
                return null;
            }
            $key = $row['Student ID'];
        }
        $student = $this->findStudent($key);

        if (!$student) {
            $this->logFailure("Student not found for update", $row);
            return null;
        }

        $data = $this->prepareStudentData($row, true, $student);

        // Validate level if grade information is provided
        if ($this->hasGradeInfo($row)) {
            $level = $this->findLevel($row);
            if (!$level) {
                $this->logFailure("Assessment not found, check grade and arab status", $row);
                return null;
            }
            $data['level_id'] = $level->id;
        } else {
            $data['level_id'] = $student->level_id;
        }

        //Delete student terms if level has changed
        if ($data['level_id'] != $student->level_id) {
            $student->student_terms()->each(function ($item){
                $item->delete();
            });
        }

        $student->update($data);
        $this->updated_rows_count++;

        return $student;
    }

    /**
     * Create student operation
     *
     * @param array $row
     * @return Student|null
     */
    private function createStudent(array $row)
    {
        // Check if student already exists
        $student = $this->findStudent($row['Student ID']);
        if ($student) {
            //Update the existing student with import file ID
            $student->update([
                'file_id' => $this->file->id
            ]);
            $this->updated_rows_count++;
            // Log failure if student already exists
            $this->logFailure("Student already exists with ID: {$row['Student ID']}", $row, true);
            return null;
        }

        // Find required level
        $level = $this->findLevel($row);
        if (!$level) {
            $this->logFailure("Assessment not found, check grade and arab status", $row);
            return null;
        }
        $this->abt_id = $this->abt_id ? $this->abt_id++ : null;
        $data = $this->prepareStudentData($row, false);
        $data['level_id'] = $level->id;
        $data['email'] = $this->generateUsername($row);
        $data['password'] = bcrypt('123456');
        $data['school_id'] = $this->file->school_id;
        $data['year_id'] = $this->file->year_id;
        $data['abt_id'] = $this->abt_id;
        $data['file_id'] = $this->file->id;

        $student = Student::create($data);
        $this->created_rows_count++;

        return $student;
    }

    /**
     * Find student by ID
     *
     * @param string $studentId
     * @return Student|null
     */
    private function findStudent($key)
    {
        $year = $this->file->year_id;

        return Student::query()
            ->when($this->searchColumn == 'student_id', function ($query) use ($key) {
                $query->where('id_number', $key);
            })
            ->when($this->searchColumn == 'username', function ($query) use ($key) {
                $query->where('email', $key);
            })
            ->where('school_id', $this->file->school_id)
            ->when($year, function ($query) use ($year) {
                $query->whereRelation('level', 'year_id', $year);
            })
            ->latest()
            ->first();
    }

    /**
     * Find level based on grade and arab status
     *
     * @param array $row
     * @return Level|null
     */
    private function findLevel(array $row)
    {
        $grade = isset($row['Grade']) ? $row['Grade'] : null;
        $arab = isset($row['Arab']) ? $row['Arab'] : 0;

        $levels = is_array($this->levels) ? collect($this->levels) : $this->levels;

        return $levels
            ->where('arab', $arab)
            ->where('grade', $grade)
            ->first();
    }

    /**
     * Check if row has grade information
     *
     * @param array $row
     * @return bool
     */
    private function hasGradeInfo(array $row)
    {
        return !empty($row['Grade']);
    }

    /**
     * Prepare student data from row
     *
     * @param array $row
     * @param bool $isUpdate
     * @return array
     */
    private function prepareStudentData(array $row, $isUpdate = false, Student $student = null)
    {
        $data = ['file_id' => $this->file->id];

        // Name processing
        if ($this->hasValue($row, 'Name')) {
            $fullName = $this->processName($row['Name']);
            $data['name'] = $this->generateEnglishName($fullName);
        }

        //id_number processing
        if (!$isUpdate && $this->hasValue($row, 'Student ID')) {
            $data['id_number'] = $row['Student ID'];
        }


        // Other fields
        $fieldMappings = [
            'Nationality' => 'nationality',
            'Grade Name' => 'grade_name',
//            'Grade' => 'grade',
        ];

        foreach ($fieldMappings as $source => $target) {
            if ($this->hasValue($row, $source)) {
                $data[$target] = $row[$source];
            }
        }

        //yes or no fields
        $yesNoFields = [
            'Citizen' => 'citizen',
            'SEN' => 'sen',
            'G&T' => 'g_t',
            'Arab' => 'arab',
        ];
        foreach ($yesNoFields as $source => $target) {
            if ($this->hasValue($row, $source)) {
                $data[$target] = $this->prepareYesOrNoValue($row[$source]);
            }
        }

        //gender processing
        if ($this->hasValue($row, 'Gender')) {
            if (in_array($row['Gender'], self::GENDER_MALE)) {
                $data['gender'] = 'boy';
            } elseif (in_array($row['Gender'], self::GENDER_FEMALE)) {
                $data['gender'] = 'girl';
            } else {
                $data['gender'] = null;
            }
        }

        // Date of birth
        if ($this->hasValue($row, 'Date Of Birth')) {
            $data['dob'] = filled($row['Date Of Birth']) ? $this->transformDate($row['Date Of Birth']):null;
        }


        return $data;
    }

    /**
     * Check if field has a valid value
     *
     * @param array $row
     * @param string $field
     * @return bool
     */
    private function hasValue(array $row, $field)
    {
        return isset($row[$field]) && !is_null($row[$field]) && $row[$field] !== "";
    }

    /**
     * Process and clean name
     *
     * @param string $name
     * @return string
     */
    private function processName($name)
    {
        return trim(preg_replace('/\s+/', ' ', $name));
    }

    /**
     * Generate English name (shortened if needed)
     *
     * @param string $fullName
     * @return string
     */
    private function generateEnglishName($fullName)
    {
        if (strlen($fullName) <= 25) {
            return $fullName;
        }

        $nameParts = explode(' ', $fullName);
        $count = count($nameParts);

        if ($count >= 4) {
            return implode(' ', [
                $nameParts[0],
                $nameParts[1],
                $nameParts[$count - 2],
                $nameParts[$count - 1]
            ]);
        } elseif ($count === 3) {
            return implode(' ', [
                $nameParts[0],
                $nameParts[1],
                $nameParts[2]
            ]);
        } elseif ($count >= 2) {
            $twoNames = $nameParts[0] . ' ' . $nameParts[1];
            return strlen($twoNames) <= 25 ? $twoNames : $nameParts[0];
        } else {
            return $nameParts[0];
        }
    }

    /**
     * Generate username
     *
     * @param array $row
     * @return string
     */
    private function generateUsername(array $row)
    {
        if ($this->hasValue($row, 'Username')) {
            return $this->processName($row['Username']);
        }

        $usernameType = isset($this->request['username_type']) ? $this->request['username_type'] : 'student_name';

        if ($usernameType === 'student_name') {
            return $this->generateNameBasedUsername($row['Name']);
        }

        return $this->generateIdBasedUsername($row['Student ID']);
    }

    /**
     * Generate name-based username
     *
     * @param string $name
     * @return string
     */
    private function generateNameBasedUsername($name)
    {
        $firstName = explode(' ', $name)[0];
        $number = date('Y') . rand(99, 99999);
        $username = $firstName . $number . '@identity';

        while (Student::where('email', $username)->withTrashed()->exists()) {
            $number = date('Y') . rand(999, 999999);
            $username = $firstName . $number . '@identity';
        }

        return $username;
    }

    /**
     * Generate ID-based username
     *
     * @param string $studentId
     * @return string
     */
    private function generateIdBasedUsername($studentId)
    {
        $username = $studentId . '@identity';

        while (Student::where('email', $username)->withTrashed()->exists()) {
            $username = $studentId . '-' . rand(9, 9999) . '@identity';
        }

        return $username;
    }

    /**
     * Transform date from various formats to Y-m-d
     *
     * @param string $value
     * @return string
     */
    private function transformDate($value)
    {
        try {
            if (str_contains($value, '/')) {
                return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            }
            return Carbon::createFromFormat('d-F-Y', $value)->format('Y-m-d');
        } catch (Exception $e) {
            Log::warning("Date transformation failed for value: {$value}", [
                'error' => $e->getMessage()
            ]);
            return Carbon::now()->format('Y-m-d');
        }
    }

    /**
     * Log failure with detailed information
     *
     * @param string $message
     * @param array $row
     */
    private function logFailure($message, array $row, $updated = false)
    {
        $logData = [
            'errors' => [$message],
            'inputs' => $this->formatRowInputs($row)
        ];

        $this->file->logErrors()->create([
            'row_num' => $this->row_num,
            'data' => $logData,
            'updated' => $updated
        ]);

        $this->failed_rows_count++;
    }

    /**
     * Format row inputs for logging
     *
     * @param array $row
     * @return array
     */
    private function formatRowInputs(array $row)
    {
        $inputs = [];
        foreach ($row as $key => $value) {
            if (!is_numeric($key)) {
                $inputs[] = ['key' => $key, 'value' => $value];
            }
        }
        return $inputs;
    }

    /**
     * Handle validation failures
     *
     * @param Failure ...$failures
     */
    public function onFailure(Failure ...$failures)
    {
        $rowNum = 0;
        $data_errors = [];
        $data = [];
        $row = null;
        foreach ($failures as $failure) {
            $rowNum = $failure->row();
            $data['inputs'] = $this->formatRowInputs($failure->values());
            $data_errors[] = $failure->errors();
        }
        if ($this->mode === self::MODE_CREATE) {
            $logData = [
                'errors' => $data_errors,
                'inputs' => $data['inputs']
            ];
            $this->file->logs()->create([
                'row_num' => $rowNum,
                'data' => $logData,
            ]);

        } else {
            $this->file->logErrors()->create([
                'row_num' => $rowNum,
                'data' => [
                    'errors' => $data_errors,
                    'inputs' => $row
                ]
            ]);
            $this->failed_rows_count++;
        }
    }

    /**
     * Handle general errors
     *
     * @param Throwable $e
     */
    public function onError(Throwable $e)
    {
        $this->error = $e->getMessage();
        Log::error('StudentImport general error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'file_id' => $this->file->id
        ]);
    }

    /**
     * Validation rules based on operation mode
     *
     * @return array
     */
    public function rules(): array
    {
        switch ($this->mode) {
            case self::MODE_DELETE:
                return $this->getDeleteValidationRules();
            case self::MODE_UPDATE:
                return $this->getUpdateValidationRules();
            case self::MODE_CREATE:
                return $this->getCreateValidationRules();
            default:
                return [];
        }
    }

    /**
     * Validation rules for delete operation
     *
     * @return array
     */
    private function getDeleteValidationRules()
    {
        return [
            'Student ID' => $this->searchColumn == 'student_id' ? 'required' : 'nullable',
            'Username' => $this->searchColumn == 'username' ? 'required' : 'nullable',
        ];
    }

    /**
     * Validation rules for update operation
     *
     * @return array
     */
    private function getUpdateValidationRules()
    {
        return [
            'Name' => ['nullable', new StudentNameRule()],
            'Student ID' => $this->searchColumn == 'student_id' ? 'required' : 'nullable',
            'Username' => $this->searchColumn == 'username' ? 'required' : 'nullable',
            'Grade' => 'nullable|in:1,2,3,4,5,6,7,8,9,10,11,12',
            'Grade Name' => 'nullable',
            'Gender' => 'nullable|in:' . implode(',', self::GENDER),
            'Nationality' => 'nullable',
            'Date Of Birth' => 'nullable',
            'Citizen' => 'nullable|in:' . implode(',', self::YesAndNo),
            'SEN' => 'nullable|in:' . implode(',', self::YesAndNo),
            'G&T' => 'nullable|in:' . implode(',', self::YesAndNo),
            'Arab' => 'nullable|in:' . implode(',', self::YesAndNo),
        ];
    }

    /**
     * Validation rules for create operation
     *
     * @return array
     */
    private function getCreateValidationRules()
    {
        $usernameType = isset($this->request['username_type']) ? $this->request['username_type'] : 'student_name';

        $baseRules = [
            'Student ID' => 'required',
            'Grade' => 'required|in:1,2,3,4,5,6,7,8,9,10,11,12',
            'Grade Name' => 'required',
            'Gender' => 'required|in:' . implode(',', self::GENDER),
            'Nationality' => 'required',
            'Date Of Birth' => 'nullable',
            'Citizen' => 'required|in:' . implode(',', self::YesAndNo),
            'SEN' => 'required|in:' . implode(',', self::YesAndNo),
            'G&T' => 'required|in:' . implode(',', self::YesAndNo),
            'Arab' => 'required|in:' . implode(',', self::YesAndNo),
        ];

        if ($usernameType === 'student_name') {
            $baseRules['Name'] = ['required', new StudentNameRule()];
        } else {
            $baseRules['Name'] = ['required'];
        }

        return $baseRules;
    }

    private function prepareYesOrNoValue($sourceValue)
    {
        $value = strtolower(trim($sourceValue));
        if (in_array($value, self::YES)) {
            return 1;
        } elseif (in_array($value, self::NO)) {
            return 0;
        } else {
            return null; // or throw an exception if needed
        }
    }

    // Getters for statistics
    public function getCreatedRowsCount()
    {
        return $this->created_rows_count;
    }

    public function getUpdatedRowsCount()
    {
        return $this->updated_rows_count;
    }

    public function getDeletedRowsCount()
    {
        return $this->deleted_rows_count;
    }

    public function getFailedRowsCount()
    {
        return $this->failed_rows_count;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getFailures()
    {
        return $this->failures;
    }

    // Legacy methods for backward compatibility
    public function getRowsCount()
    {
        return $this->getCreatedRowsCount() + $this->getUpdatedRowsCount() + $this->getDeletedRowsCount();
    }

}
