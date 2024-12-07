<?php

namespace App\Imports;

use App\Models\Level;
use App\Models\Student;
use App\Models\StudentImportFile;
use App\Rules\StudentNameRule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
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
class StudentImport implements ToModel,SkipsOnFailure,SkipsOnError,WithHeadingRow,SkipsEmptyRows,WithValidation
{
    //1=>New  2=>Uploading  3=>Completed  4=>Failure  5=>Error

    public $file;
    public $update;

    public $row_num = 1;
    private $rows_count = 0;
    private $updated_rows_count = 0;
    private $failed_row_count = 0;
    private $error = null;
    private $failures = [];

    public $levels;


    public function __construct(StudentImportFile $file, $update = false)
    {
        $this->file = $file;
        $this->update = $update;
        $this->levels = Level::query()->latest()->get();
    }

    public function transformDate($value, $format = 'd-M-Y')
    {
        if (str_contains($value, '/')) {
            return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        } else {
            return Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
        }
    }

    /**
    * @param array $row
    *
    * @return Model|null
    */
    public function model(array $row)
    {
        $this->row_num++;
        if (!$this->update) {
            $full_name = trim(str_replace('  ', ' ', str_replace(' ', ' ', $row['Name'])));
            if (strlen($full_name) > 25) {
                $array_name = explode(' ', $full_name);
                if (count($array_name) >= 3) {
                    $full_name = $array_name[0] . ' ' . $array_name[1] . ' ' . $array_name[count($array_name) - 1];
                } else {
                    $full_name = $array_name[0] . ' ' . $array_name[1];
                    if (strlen($full_name) > 25) {
                        $full_name = $array_name[0];
                    }
                }
            }
            $names = explode(' ', $full_name);
            $number = date('Y') . '' . rand(1, 999);
            $username = $names[0] . '' . $number . '@identity';
            $pre_username = Student::query()->where('email', $username)->first();
            while (!is_null($pre_username)) {
                $number = date('Y') . rand(1, 99999);
                $username = $names[0] . '' . $number . '@identity';
                $pre_username = Student::query()->where('email', $username)->first();
            }
            if ($row['Gender'] == 1) {
                $gender = 'boy';
            } else {
                $gender = 'girl';
            }

            $assessment = $this->levels->where('grade', $row['Grade'])->where('arab', $row['Arab'])->first();

            if(!$assessment){
                $this->failures[$this->row_num][] = 'Assessment Not Found, check Grade and Arab';
                $data_errors = ['Assessment Not Found, check Grade and Arab'];
                $data = [];
                $data['inputs'] = [];
                foreach ($row as $key => $value) {
                    //check if not number
                    if (!is_numeric($key)) {
                        array_push($data['inputs'], [
                            'key' => $key,
                            'value' => $value,
                        ]);
                    }
                }
                $data['errors'] = $data_errors;
                $this->file->logs()->create([
                    'row_num' => $this->row_num,
                    'data' => $data,
                ]);

                $this->failed_row_count++;
                return null;
            }else{
                $row['Assessment'] = $assessment->id;
            }

            $student = Student::query()->create([
                'id_number' => $row['Student ID'],
                'name' => $full_name,
                'email' => $username,
                'password' => bcrypt('123456'),
                'level_id' => $row['Assessment'],
                'nationality' => isset($row['Nationality']) ? $row['Nationality']:null,
                'grade_name' => isset($row['Grade Name']) ? $row['Grade Name']:null,
                'sen' => $row['SEN'],
                'g_t' => $row['G&T'],
                'arab' => $row['Arab'],
                'dob' => isset($row['Date Of Birth']) ? $row['Date Of Birth']:null,
                'gender' => $gender,
                'citizen' => $row['Citizen'],
                'file_id' => $this->file['id'],
                'school_id' => $this->file['school_id'],
                'year_id' => $this->file['year_id']
            ]);
            $this->rows_count++;
        } else {
            $data = [];
            if(isset($row['Nationality']) && !is_null($row['Nationality']))
            {
                $data['nationality'] = $row['Nationality'];
            }
            if(isset($row['Grade Name']) && !is_null($row['Grade Name']))
            {
                $data['grade_name'] = $row['Grade Name'];
            }
            if(isset($row['Date Of Birth']) && !is_null($row['Date Of Birth']))
            {
                try {
                    $dob = $this->transformDate($row['Date Of Birth']);
                } catch (\Exception $e) {
                    $dob = Carbon::now()->format('Y-m-d');
                }
                $data['dob'] = $dob;
            }
            if((isset($row['Grade']) && !is_null($row['Grade']) && $row['Grade'] != "") && (isset($row['Arab']) && !is_null($row['Arab']) && $row['Arab'] != ""))
            {
                $assessment = $this->levels->where('grade', $row['Grade'])->where('arab', $row['Arab'])->first();
                if(!$assessment) {
                    $this->failures[$this->row_num][] = 'Assessment Not Found, check Grade and Arab';
                    $data_errors = ['Assessment Not Found, check Grade and Arab'];
                    $data = [];
                    $data['inputs'] = [];
                    foreach ($row as $key => $value) {
                        //check if not number
                        if (!is_numeric($key)) {
                            array_push($data['inputs'], [
                                'key' => $key,
                                'value' => $value,
                            ]);
                        }
                    }
                    $data['errors'] = $data_errors;
                    $this->file->logs()->create([
                        'row_num' => $this->row_num,
                        'data' => $data,
                    ]);

                    $this->failed_row_count++;
                    return null;
                }else{
                    $data['level_id'] = $assessment->id;
                }

            }
            if(isset($row['Citizen']) && !is_null($row['Citizen']))
            {
                $data['citizen'] = $row['Citizen'];
            }
            if(isset($row['SEN']) && !is_null($row['SEN']))
            {
                $data['sen'] = $row['SEN'];
            }
            if(isset($row['G&T']) && !is_null($row['G&T']))
            {
                $data['g_t'] = $row['G&T'];
            }
            if(isset($row['Arab']) && !is_null($row['Arab']))
            {
                $data['arab'] = $row['Arab'];
            }
            if(isset($row['Gender']) && !is_null($row['Gender']))
            {
                if ($row['Gender'] == 1) {
                    $gender = 'boy';
                } else {
                    $gender = 'girl';
                }
                $data['gender'] = $gender;
            }
            $student = Student::query()
                ->where('school_id', $this->file->school_id)
                ->where('id_number', $row['Student ID'])
                ->first();
            if($student)
            {
                $student->update($data);
                $this->updated_rows_count ++;
            }
        }
        return $student;
    }

    /**
     * @return int
     */
    public function getRowsCount(): int
    {
        return $this->rows_count;
    }

    /**
     * @return int
     */
    public function getUpdatedRowsCount(): int
    {
        return $this->updated_rows_count;
    }

    /**
     * @return int
     */
    public function getFailedRowCount(): int
    {
        return $this->failed_row_count;
    }

    /**
     * @return null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return array
     */
    public function getFailures(): array
    {
        return $this->failures;
    }

    public function onFailure(Failure ...$failures)
    {
        if (!$this->update) {
            $row_num = 0;
            $data_errors = [];
            $data = [];
            foreach ($failures as $failure) {
                $row_num = $failure->row();
                $this->log_errors[] = "Row " . $failure->row() . ' : ' . $failure->errors()[0];
                $data['inputs'] = [];

                foreach ($failure->values() as $key => $value) {
                    //check if not number
                    if (!is_numeric($key)) {
                        $data['inputs'][] = [
                            'key' => $key,
                            'value' => $value,
                        ];
                    }
                }
                $data_errors[] = $failure->errors();
            }
            $data['errors'] = $data_errors;
            $this->file->logs()->create([
                'row_num' => $row_num,
                'data' => $data,
            ]);
            $this->failed_row_count++;
        } else{
            foreach ($failures as $failure) {
                $this->failures[$failure->row()][] = $failure->errors()[0];
            }
            $this->failed_row_count++;
        }
    }

    public function onError(Throwable $e)
    {
       $this->error = $e->getMessage();
    }

    public function prepareForValidation(array $row)
    {
        // Trim both keys (headers) and values (cell data)
        $trimmedKeys = array_map('trim', array_keys($row));
        $trimmedValues = array_map(function($value) {
            return $value === null ? null : trim($value);
        }, array_values($row));
        // Rebuild the row with the trimmed keys and values
        return array_combine($trimmedKeys, $trimmedValues);
    }
    public function rules(): array
    {
        if (!$this->update)
        {
            return [
                'Student ID' => 'required',
                'Name' =>['required', new StudentNameRule()],
//                'Assessment' => 'required',
                'Grade' => 'required',
                'Nationality' => 'nullable',
                'Grade Name' => 'nullable',
                'SEN' => 'required|in:1,0',
                'G&T' => 'required|in:1,0',
                'Arab' => 'required|in:1,0',
                'Date Of Birth' => 'nullable',
                'Gender' => 'required|in:1,2',
                'Citizen' => 'required|in:1,0',
            ];
        }else{
            return [
                'Student ID' => 'required',
                'Name' =>['nullable', new StudentNameRule()],
//                'Assessment' => 'nullable',
                'Grade' => 'nullable',
                'Nationality' => 'nullable',
                'Grade Name' => 'nullable',
                'SEN' => 'nullable|in:1,0',
                'G&T' => 'nullable|in:1,0',
                'Arab' => 'nullable|in:1,0',
                'Date Of Birth' => 'nullable',
                'Gender' => 'nullable|in:1,2',
                'Citizen' => 'nullable|in:1,0',
            ];
        }
    }
}
