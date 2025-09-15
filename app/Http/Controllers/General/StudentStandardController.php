<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Controllers\General;

use App\Exports\StandardsStudentsExport;
use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\QuestionStandard;
use App\Models\Student;
use App\Models\StudentTerm;
use App\Models\StudentTermStandard;
use App\Models\Subject;
use App\Models\Term;
use App\Models\Year;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class StudentStandardController extends Controller
{
    private $subjects;
    public function __construct()
    {
        $this->subjects = Subject::all();
    }

    public function exportStudentStandards(Request $request)
    {
        $rules = [];
        if (guardIs('manager')){
            $rules['school_id'] = 'required|exists:schools,id';
        }
        $rules = array_merge($rules, [
            'year_id' => 'required',
            'level_id' => 'required|array|min:1|max:1',
        ]);

        // Start timing the execution
        $startTime = microtime(true);

        $selected_level = Level::query()->find($request->get('level_id')[0]);
        $school = Auth::guard('school')->user();
        $year_id = $request->get('year_id');

        if (in_array($school->curriculum_type , ['Indian','Pakistan', 'Bangladeshi'])) {
            $months = ['may', 'september', 'february'];
            $custom_year = Year::query()->where('id', $year_id - 1)->first();
        } else {
            $months = ['september', 'february', 'may'];
            $custom_year = Year::query()->find($year_id);
        }
        $year = Year::query()->find($year_id);

        $section = $selected_level->arab;
        $selected_grade = $selected_level->grade;

        // Retrieve all students with eager loading
        $students = Student::query()
            ->search($request)
            ->select(['id', 'name', 'grade_name', 'nationality'])
            ->get();

        $student_ids = $students->pluck('id')->toArray();

        // Fetch all student terms with a single query using whereIn
        $students_terms = StudentTerm::query()
            ->with(['term'])
            ->where('corrected', 1)
            ->whereIn('student_id', $student_ids)
            ->get();

        $term_ids = $students_terms->pluck('term_id')->unique()->toArray();
        $terms = Term::query()->whereIn('id', $term_ids)->get();
        $term_months = $terms->pluck('round')->toArray();

        // Prepare phases based on section
        $phases = $this->subjects->pluck('name')->toArray();

        // Fetch all required standards in one query with proper eager loading
        $all_standards = $this->getAllStandards($months, $year, $custom_year, $selected_level, $term_months);

        // Group standards by month
        $standards_september = $all_standards->filter(function($standard) use ($months) {
            return $standard->question->term->round == $months[0];
        });
        $standards_february = $all_standards->filter(function($standard) use ($months) {
            return $standard->question->term->round == $months[1];
        });
        $standards_may = $all_standards->filter(function($standard) use ($months) {
            return $standard->question->term->round == $months[2];
        });


        // Get all standard IDs
        $standards_september_id = $standards_september->pluck('id')->toArray();
        $standards_february_id = $standards_february->pluck('id')->toArray();
        $standards_may_id = $standards_may->pluck('id')->toArray();

        // Fetch all student standards in a single query
        $all_student_standards = StudentTermStandard::query()
            ->whereHas('studentTerm.student',function (Builder $query) use ($student_ids) {
                $query->whereIn('id', $student_ids);
            })
            ->whereIn('question_standard_id', array_merge(
                $standards_september_id,
                $standards_february_id,
                $standards_may_id
            ))
            ->get();

        // Group student standards by month
        $september_students_standards = $all_student_standards->filter(function($item) use ($standards_september_id) {
            return in_array($item->question_standard_id, $standards_september_id);
        });

        $february_students_standards = $all_student_standards->filter(function($item) use ($standards_february_id) {
            return in_array($item->question_standard_id, $standards_february_id);
        });

        $may_students_standards = $all_student_standards->filter(function($item) use ($standards_may_id) {
            return in_array($item->question_standard_id, $standards_may_id);
        });

        // Initialize data arrays
        $standards_data = [];
        $standards_marks = [];
        $standards_marks[] = '';
        $standards_marks[] = '';
        $standards_marks[] = '';
        $standards_marks[] = '';

        $standards_avg_results = [];
        $standards_avg_results[] = 'Average Standard Benchmark';
        $standards_avg_results[] = '';
        $standards_avg_results[] = '';
        $standards_avg_results[] = '';

        // Pre-calculate standard results to avoid repetitive calculations
        $september_standard_results = $this->calculateStandardResults($standards_september, $september_students_standards);
        $february_standard_results = $this->calculateStandardResults($standards_february, $february_students_standards);
        $may_standard_results = $this->calculateStandardResults($standards_may, $may_students_standards);

        // Process standards for September
        if ($standards_september->count() > 0) {
            $this->processMonthStandards(
                $standards_data,
                $standards_marks,
                $standards_avg_results,
                $standards_september,
                $months[0],
                $year->name,
                $phases,
                $september_standard_results
            );
        }

        // Process standards for February
        if ($standards_february->count() > 0) {
            $this->processMonthStandards(
                $standards_data,
                $standards_marks,
                $standards_avg_results,
                $standards_february,
                $months[1],
                $year->name,
                $phases,
                $february_standard_results
            );
        }

        // Process standards for May
        if ($standards_may->count() > 0) {
            $this->processMonthStandards(
                $standards_data,
                $standards_marks,
                $standards_avg_results,
                $standards_may,
                $months[2],
                $year->name,
                $phases,
                $may_standard_results
            );
        }

        // Build student data
        $students_data = $this->buildStudentData(
            $students,
            $school,
            $standards_september,
            $standards_february,
            $standards_may,
            $september_students_standards,
            $february_students_standards,
            $may_students_standards,
            $students_terms,
            $phases
        );

        // Add average results
        $students_data[] = $standards_avg_results;

        // Log execution time
        $executionTime = microtime(true) - $startTime;
        \Log::info("Export performance: Processed {$students->count()} students in {$executionTime} seconds");
//        dd($students_data);

        return (new StandardsStudentsExport($request, $school, $students_data, $students_terms, $standards_data, $standards_marks,$this->subjects))
            ->download('Standards Tracking Sheet.xlsx');
    }

    /**
     * Get all standards for all months in a single query
     */
    private function getAllStandards(array $months, $year, $custom_year, $selected_level, array $term_months)
    {
        $standardsQuery = QuestionStandard::query()
            ->with(['question' => function($q) {
                $q->with('term.level');
            }])
            ->whereHas('question', function (Builder $query) use ($months, $year, $custom_year, $selected_level, $term_months) {
                $query->whereHas('term', function (Builder $query) use ($months, $year, $custom_year, $selected_level, $term_months) {
                    $query->whereIn('round', array_intersect($months, $term_months))
                        ->whereHas('level', function (Builder $query) use ($year, $custom_year, $selected_level) {
                            $query->where('grade', $selected_level->grade)
                                ->where('arab', $selected_level->arab)
                                ->whereIn('year_id', [$year->id, $custom_year->id]);
                        });
                });
            });

        return $standardsQuery->get();
    }

    /**
     * Pre-calculate results for all standards to avoid repetitive calculations
     */
    private function calculateStandardResults(Collection $standards, Collection $student_standards)
    {
        $results = [];

        foreach ($standards as $standard) {
            $standard_mark = $standard->mark;
            $total_result = $student_standards->where('question_standard_id', $standard->id)->count();
            $f_result = $student_standards->where('question_standard_id', $standard->id)
                ->where('mark', '>=', $standard_mark)
                ->count();

            if ($total_result == 0 || $f_result == 0) {
                $results[$standard->id] = "0 %";
            } else {
                $final_result = round($f_result / $total_result * 100, 1);
                $results[$standard->id] = "$final_result %";
            }
        }

        return $results;
    }

    /**
     * Process standards for a specific month
     */
    private function processMonthStandards(
        array &$standards_data,
        array &$standards_marks,
        array &$standards_avg_results,
        Collection $standards_month,
        string $month,
        string $year,
        array $phases,
        array $standard_results
    ) {
        $standards_data[] = '';
        $standards_marks[] = '';
        $standards_data[] = $month . ' Round ' . $year;
        $standards_marks[] = '';
        $standards_avg_results[] = '';

        foreach ($this->subjects as $index=>$subject) {


            $standards_data[] = $subject->name;
            if ($index == 0) {
                $standards_avg_results[] = '';
            }
            $standards_avg_results[] = '';
            $standards_marks[] = '';

            $p_standard = $standards_month->filter(function ($value) use ($subject) {
                return $value->question->subject_id == $subject->id;
            });

            foreach ($p_standard as $standard) {
                $standards_data[] = $standard->standard;
                $standards_marks[] = $standard->mark;
                $standards_avg_results[] = $standard_results[$standard->id] ?? "0 %";
            }
        }

        $standards_data[] = 'Total';
        $standards_avg_results[] = '-';
        $standards_marks[] = '-';
    }

    /**
     * Build the student data array
     */
    private function buildStudentData(
        Collection $students,
                   $school,
        Collection $standards_september,
        Collection $standards_february,
        Collection $standards_may,
        Collection $september_students_standards,
        Collection $february_students_standards,
        Collection $may_students_standards,
        Collection $students_terms,
        array $phases
    ) {
        $students_data = [];

        foreach ($students as $student) {
            $student_data = [];
            $student_data[] = $student->name;
            $student_data[] = $school->name;
            $student_data[] = $student->grade_name;
            $student_data[] = $student->nationality;

            // September data
            if ($standards_september->count() > 0) {
                $student_data = $this->addStudentMonthData(
                    $student_data,
                    $student,
                    $standards_september,
                    $september_students_standards,
                    $students_terms,
                    'september'
                );
            }

            // February data
            if ($standards_february->count() > 0) {
                $student_data = $this->addStudentMonthData(
                    $student_data,
                    $student,
                    $standards_february,
                    $february_students_standards,
                    $students_terms,
                    'february'
                );
            }

            // May data
            if ($standards_may->count() > 0) {
                $student_data = $this->addStudentMonthData(
                    $student_data,
                    $student,
                    $standards_may,
                    $may_students_standards,
                    $students_terms,
                    'may'
                );
            }

            $students_data[] = $student_data;
        }

        return $students_data;
    }

    /**
     * Add student data for a specific month
     */
    private function addStudentMonthData(
        array $student_data,
              $student,
        Collection $standards_month,
        Collection $month_students_standards,
        Collection $students_terms,
        string $month
    ) {
        $student_data[] = '';
        $student_data[] = '';
        $student_data[] = '';

        // Add term total
        $term_result = $students_terms->where('student_id', $student->id)
            ->filter(function ($value) use ($month) {
                return $value->term->round == $month;
            })->first();

        if (!$term_result){
            return $student_data;
        }

        // Filter student standards once
        $student_standards = $month_students_standards->where('student_term_id', $term_result->id);

        // Process subjects 1-3 (and 4 if section is true)
        foreach ($this->subjects as $index => $subject) {
            $p_standard = $standards_month->filter(function ($value) use ($subject) {
                return $value->question->subject_id == $subject->id;
            });

            if ($index > 0) {
                $student_data[] = '';
            }

            foreach ($p_standard as $standard) {
                $mark = $student_standards->where('question_standard_id', $standard->id)->sortByDesc('mark')->first();
                $student_data[] = $mark ? "$mark->mark" : '-';
            }
        }


        $student_data[] = $term_result ? "$term_result->total" : '-';

        return $student_data;
    }
}
