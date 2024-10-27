<?php

namespace App\Imports;

use App\Exceptions\GeneralException;
use App\Models\MatchQuestion;
use App\Models\OptionQuestion;
use App\Models\Question;
use App\Models\FillBlankQuestion;
use App\Models\QuestionFile;
use App\Models\SortQuestion;
use App\Models\Subject;
use App\Models\TFQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;

HeadingRowFormatter::default('none');

class QuestionsImport implements ToModel, WithHeadingRow, SkipsOnFailure, SkipsOnError, WithValidation
{
    private $created_rows_count = 0;
    private $updated_rows_count = 0;
    private $deleted_rows_count = 0;
    private $failed_rows_count = 0;
    private $error = null;
    private $failures = [];
    private $log_errors = [];

    private $request;

    private $questionFile;

    private $rowNumber = 1;

    public function __construct(Request $request, QuestionFile $questionFile)
    {
        $this->request = $request;
        $this->questionFile = $questionFile;
    }

    public function model(array $row)
    {
        $this->rowNumber++; // Increment row number for tracking

        // Check if 'Question Title' is present and valid for all types
        if (empty($row['Question Title'])) {
            $this->failures[] = [
                'row' => $this->rowNumber,
                'error' => 'Question Title is required.'
            ];
            return null; // Skip this row
        }

        // Additional constraint: For "Fill blank", check if the title has '[blank]'
        if ($row['Question Type'] === 'Fill blank' && !preg_match('/\[blank\]/', $row['Question Title'])) {
            $this->failures[$this->rowNumber] = ['Question Title must contain at least one [blank] for Fill blank type.'];
            $this->failed_rows_count++;
            return null; // Skip this row
        }

        //check count of '[blank]' count in question title and count options
        if ($row['Question Type'] === 'Fill blank') {
            $question_blankCount = substr_count($row['Question Title'], '[blank]');
            $options_blankCount = 0;
            foreach (range(1, 8) as $i) {
                if (isset($row['Option ' . $i]) && !empty($row['Option ' . $i])) {
                    $options_blankCount += 1;
                }
            }

            if ($question_blankCount != $options_blankCount) {
                $this->failures[$this->rowNumber] = ['Question Title must contain same number of [blank] as options for Fill blank type.'];
                $this->failed_rows_count++;
                return null; // Skip this row
            }
        }

        // check if question type is Multiple choice and there are 4 options and at least one has '(Yes)'
        if ($row['Question Type'] === 'Multiple choice') {
            $yesCount = 0;
            foreach (range(1, 4) as $i) {
                if (!empty($row['Option ' . $i])) {
                    if (strpos($row['Option ' . $i], '(Yes)') !== false) {
                        $yesCount++;
                    }
                }
            }
            if ($yesCount === 0) {
                $this->failures[$this->rowNumber] = [
                    'At least one option must have (Yes) for Multiple choice type.'
                ];
                $this->failed_rows_count++;
                return null; // Skip this row
            }
        }

        //check if question type is Matching and there are at least 3 options with '<=>'
        if ($row['Question Type'] === 'Matching') {
            $matchingCount = 0;
            foreach (range(1, 8) as $i) {
                if (isset($row['Option ' . $i]) && !empty($row['Option ' . $i])) {
                    if (strpos($row['Option ' . $i], '<=>') !== false) {
                        $matchingCount++;
                    }
                }
            }
            if ($matchingCount < 3) {
                $this->failures[$this->rowNumber] = [
                    'At least 3 options must have "<=>" for Matching type.'
                ];
                $this->failed_rows_count++;
                return null; // Skip this row
            }
        }

        //check if question type is Sorting and there are at least 3 options
        if ($row['Question Type'] === 'Sorting') {
            $sortingCount = 0;
            foreach (range(1, 8) as $i) {
                if (isset($row['Option ' . $i]) && !empty($row['Option ' . $i])) {
                    $sortingCount++;
                }
            }
            if ($sortingCount < 3) {
                $this->failures[$this->rowNumber] = [
                    'At least 3 options must be provided for Sorting type.'
                ];
                $this->failed_rows_count++;
                return null; // Skip this row
            }
        }

        // Create the question based on the validated data
        $question = Question::create([
            'content' => $row['Question Title'],
            'term_id' => $this->questionFile->term_id,
            'subject_id' => $row['Subject ID'],
            'type' => snake_case($row['Question Type']),
            'author_id' => auth()->guard(getGuard())->user()->id,
            'author_type' => auth()->guard(getGuard())->user()->getMorphClass(),
            'mark' => $row['Mark'], // Assuming 1 mark for each question
            'question_file_id' => $this->questionFile->id,
        ]);

        // Process options and create related models (True/False, Multiple Choice, etc.)
        $options_count = $this->processOptions($row, $question);
//
//
//        $question->update(['min_mark' => $options_count]); // Update min_mark based on the number of options

        $this->created_rows_count++;
    }

    public function prepareForValidation(array $row)
    {
        // Trim both keys (headers) and values (cell data)
        $trimmedKeys = array_map('trim', array_keys($row));
        $trimmedValues = array_map('trim', array_values($row));

        //replace '[Blank]' with '[blank]'
        $trimmedValues = array_map(function ($value) {
            return str_replace('[Blank]', '[blank]', $value);
        }, $trimmedValues);

        // Rebuild the row with the trimmed keys and values
        return array_combine($trimmedKeys, $trimmedValues);
    }


    public function rules(): array
    {
        return [
            'Question Title' => [
                'required',
            ],
            'Mark' => [
                'required','integer'
            ],
            'Question Type' => ['required', Rule::in(['True False', 'Multiple choice', 'Matching', 'Sorting', 'Fill blank', 'Article'])],

            // True/False validation
            'Option 1' => 'required_if:Question Type,True False|in:True (Yes),True',
            'Option 2' => 'required_if:Question Type,True False|in:False (Yes),False',

            // Multiple choice validation (4 options are required)
            'Option 1' => 'required_if:Question Type,Multiple choice',
            'Option 2' => 'required_if:Question Type,Multiple choice',
            'Option 3' => 'required_if:Question Type,Multiple choice',
            'Option 4' => 'required_if:Question Type,Multiple choice',

            // Matching validation (at least 3 options with "<=>" separator)
            'Option 1' => 'required_if:Question Type,Matching|regex:/<=>/',
            'Option 2' => 'required_if:Question Type,Matching|regex:/<=>/',
            'Option 3' => 'required_if:Question Type,Matching|regex:/<=>/',

            // Sorting validation (at least 3 options)
            'Option 1' => 'required_if:Question Type,Sorting',
            'Option 2' => 'required_if:Question Type,Sorting',
            'Option 3' => 'required_if:Question Type,Sorting',

            // Fill the blank validation (at least one option, title must contain [blank])
            'Option 1' => 'required_if:Question Type,Fill blank',

            'Subject ID' => 'required|exists:subjects,id',

        ];
    }

    /**
     * @return int
     */
    public function getUpdatedRowsCount(): int
    {
        return $this->updated_rows_count;
    }

    public function getDeletedRowsCount(): int
    {
        return $this->deleted_rows_count;
    }

    /**
     * @return int
     */
    public function getRowsCount(): int
    {
        return $this->created_rows_count;
    }

    /**
     * @return int
     */
    public function getFailedRowCount(): int
    {
        return $this->failed_rows_count;
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

    protected function processOptions(array $row, Question $question)
    {
        switch ($row['Question Type']) {
            case 'True False':
                TFQuestion::create([
                    'question_id' => $question->id,
                    'result' => strpos($row['Option 1'], '(Yes)') !== false ? 1 : 0,
                ]);
                return 1;

            case 'Multiple choice':
                foreach (range(1, 4) as $i) {
                    if (!empty($row['Option ' . $i])) {
                        OptionQuestion::create([
                            'question_id' => $question->id,
                            'content' => trim(Str::replace('(Yes)','',$row['Option ' . $i])),
                            'result' => strpos($row['Option ' . $i], '(Yes)') !== false,
                        ]);
                    }
                }
                return 1;

            case 'Matching':
                $options_count = 0;
                foreach (range(1, 8) as $i) {
                    if (isset($row['Option ' . $i]) && !empty($row['Option ' . $i])) {
                        list($left, $right) = explode('<=>', $row['Option ' . $i]);
                        MatchQuestion::create([
                            'uid' => Str::uuid(),
                            'question_id' => $question->id,
                            'content' => trim($left),
                            'result' => trim($right),
                        ]);
                        $options_count++;
                    }
                }
                return $options_count;

            case 'Sorting':
                foreach (range(1, 8) as $i) {
                    if (isset($row['Option ' . $i]) && !empty($row['Option ' . $i])) {
                        SortQuestion::create([
                            'uid' => Str::uuid(),
                            'question_id' => $question->id,
                            'content' => $row['Option ' . $i],
                            'ordered' => $i,
                        ]);
                    }
                }
                return 1;

            case 'Fill blank':
                $options_count = 0;
                foreach (range(1, 8) as $i) {
                    if (isset($row['Option ' . $i]) && !empty($row['Option ' . $i])) {
                        FillBlankQuestion::create([
                            'uid' => Str::uuid(),
                            'question_id' => $question->id,
                            'content' => $row['Option ' . $i],
                        ]);
                        $options_count++;
                    }
                }
                return $options_count;
                default:
                return 1;
        }
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->log_errors[] = "Row " . $failure->row() . ' : ' . $failure->errors()[0];
            $this->failures[$failure->row()] = $failure->errors();
            $this->failed_rows_count++;
        }
    }

    public function onError(\Throwable $e)
    {
        $this->error = $e->getMessage();
    }
//    public function collection(Collection $rows)
//    {
//        $subjects = Subject::all();
//        $subject_rows = $rows->groupBy('Subject ID');
//        foreach ($subject_rows as $subject_id=>$s_rows) {
//            $subject = $subjects->where('id', $subject_id)->first();
//            if (!$subject){
//                 throw new GeneralException('subject not found');
//            }
//            $s_rows_sum = collect($s_rows)->sum('Mark');
//            if ($s_rows_sum != $subject->mark){
//                 throw new GeneralException('subject ('.$subject->name.') marks sum not equal '.$subject->mark);
//            }
//
//        }
//        return $rows;
//    }
}
