<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Reports\NewReports;

use App\Models\StudentTerm;
use App\Models\Subject;
use App\Models\Year;
use App\Models\School;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class YearToYearReport
{
    private $school;
    private $schools;
    private $request;
    private $years;
    private $reversedYears;
    private $selectedGrades;
    private $round;
    private $student_type;
    private $lastYear;
    private $yearCollection;
    private $reportType;
    private $markData;
    private $subjects;

    private $isCombined = false; // Flag for combined report

    private const CHUNK_SIZE = 1000; // Process students in chunks
    private const CACHE_TTL = 30; // Cache for 30 minutes

    public function __construct(Request $request, $schools = [])
    {
        $schools = School::query()->whereIn('id', $schools)->get();
        $this->school = $schools->first();
        $this->schools = $schools;
        $this->request = $request;
        $this->subjects = Subject::query()->get();

        $this->initializeReportParameters();
    }

    private function calculateNormalReport()
    {
//        if ($this->student_type == 2)
//        {
//            $student_types = [1, 0]; // For combined report, consider both sections
//        } else {
//            $student_types = [$this->student_type]; // Single section report
//        }
        $arab_attainmentGrades = [];
        $non_arab_attainmentGrades = [];
        $arab_progressGrades = [];
        $non_arab_progressGrades = [];
//        foreach($student_types as $student_type)
//        {
//            $this->student_type = $student_type;
        $studentsAbtIdsByGrade = $this->getEligibleStudentsOptimized();
        $attainmentGrades = [];
        $progressGrades = [];

        foreach ($studentsAbtIdsByGrade as $grade => $abtIds) {
            if (empty($abtIds)) {
                continue;
            }

            $yearGrade = $grade;
            $attainmentGrades[$grade] = [];
            $progressGrades[$grade] = [];

            $lastYear = null;
            $lastGrade = null;

            foreach ($this->reversedYears as $year) {
                $year_name = $this->yearCollection->where('id', $year)->first()->name ?? 'Unknown Year';
                $this->markData = getMarksRanges($grade);

                // Optimized attainment calculation
                $attainmentGrades[$grade][$year_name] = $this->calculateAttainmentOptimized($abtIds, $year, $yearGrade);

                if (is_null($lastYear)) {
                    $lastYear = $year;
                    $lastGrade = $yearGrade;
                } else {
                    // Optimized progress calculation
                    $progressGrades[$grade][] = $this->calculateProgressOptimized($abtIds, $year, $lastYear, $yearGrade, $lastGrade);
                    $lastYear = $year;
                    $lastGrade = $yearGrade;
                }
                $yearGrade--;
            }
            //reverse $attainmentGrades[$grade]
            $attainmentGrades[$grade] = array_reverse($attainmentGrades[$grade]);
            //reverse $progressGrades[$grade]
            $progressGrades[$grade] = array_reverse($progressGrades[$grade]);


//                if ($student_type == 1) {
//                    $arab_attainmentGrades[$grade] = $attainmentGrades[$grade];
//                    $arab_progressGrades[$grade] = $progressGrades[$grade];
//                } else {
//                    $non_arab_attainmentGrades[$grade] = $attainmentGrades[$grade];
//                    $non_arab_progressGrades[$grade] = $progressGrades[$grade];
//                }
        }
//        }
        $statKeys = [
            'total',
            'boys',
            'girls',
            'sen_students',
            'g_t_students',
            'citizen_students',
            'citizen_boys_students',
            'citizen_girls_students',
        ];

        $statistics = array_fill_keys($statKeys, 0);
        $arab_statistics = array_fill_keys($statKeys, 0);
        $non_arab_statistics = array_fill_keys($statKeys, 0);

        $this->accumulateStatistics($statistics, $attainmentGrades);
//        $this->accumulateStatistics($arab_statistics, $arab_attainmentGrades);
//        $this->accumulateStatistics($non_arab_statistics, $non_arab_attainmentGrades);

        return [
            'pages' => [
                'attainment' => $attainmentGrades,
                'progress' => $progressGrades,
                'statistics' => (object)$statistics,
                'pages_type_text' => $this->getStudentTypeText($this->student_type),
                'pages_type' => 1,
                'title' => 'Grade :grade for '.$this->getSubTitle($this->student_type),
            ],
        ];
    }

    private function accumulateStatistics(&$statistics, $attainmentGrades)
    {
        foreach ($attainmentGrades as $grade) {
            $year = array_first($grade);
            if ($year) {
                $statistics['total'] += $year->total;
                $statistics['boys'] += $year->boys->total;
                $statistics['girls'] += $year->girls->total;
                $statistics['sen_students'] += $year->sen_students->total;
                $statistics['g_t_students'] += $year->g_t_students->total;
                $statistics['citizen_students'] += $year->citizen_students->total;
                $statistics['citizen_boys_students'] += $year->citizen_boys_students->total;
                $statistics['citizen_girls_students'] += $year->citizen_girls_students->total;
            }
        }
    }

    private function calculateCombinedReport()
    {
        $result = $this->calculateNormalReport();
        $pagesResults = $result['pages'];
//        $arabsResults = $result['arabs'];
//        $nonArabsResults = $result['non_arabs'];


//        // Combine non-Arabs data (all grades into one)
//        $nonArabsCombinedData = $this->combineAllGrades($nonArabsResults);
//        // Combine Arabs data (all grades into one)
//        $arabsCombinedData = $this->combineAllGrades($arabsResults);
        $pagesCombinedData = $this->combineAllGrades($pagesResults);

//        $arabsCombinedData['title'] = 'Combined Grades for Arabs';
//        $nonArabsCombinedData['title'] = 'Combined Grades for Non-Arabs';
        $pagesCombinedData['title'] = 'Combined Grades for ' . $this->getSubTitle($this->student_type);
        return [
            'pages' => $pagesCombinedData,
//            'arabs' => $arabsCombinedData,
//            'non_arabs' => $nonArabsCombinedData
        ];
    }

    /**
     * Combine all grades data into a single result for a section (Arabs or non-Arabs)
     */
    private function combineAllGrades($sectionResults)
    {
        $combinedAttainment = [];
        $combinedProgress = [];

        // Get all unique years from attainment data
        $allYears = [];
        foreach ($sectionResults['attainment'] as $grade => $years) {
            $allYears = array_merge($allYears, array_keys($years));
        }
        $allYears = array_unique($allYears);

        // Process attainment data - combine all grades for each year
        foreach ($allYears as $yearKey) {
            $yearCombinedData = $this->createEmptyStatisticsStructure();

            // Aggregate data for this year across all grades
            foreach ($sectionResults['attainment'] as $grade => $years) {
                if (isset($years[$yearKey])) {
                    $yearCombinedData = $this->addStatisticsData($yearCombinedData, $years[$yearKey]);
                }
            }

            // Recalculate percentages after combining
            $yearCombinedData = $this->recalculatePercentages($yearCombinedData);
            $yearCombinedData->id = $years[$yearKey]->id ?? null; // Preserve year ID

            $combinedAttainment[0][$yearKey] = $yearCombinedData;

        }

        // Process progress data - combine all grades
        $allProgressData = [];

        // Collect all progress data from this section
        foreach ($sectionResults['progress'] as $grade => $progressArray) {
            $allProgressData = array_merge($allProgressData, $progressArray);
        }
        // Group progress data by comparison periods and combine
        $progressGroups = [];
        foreach ($allProgressData as $progressItem) {
            $key = $progressItem->id; // This contains the year comparison like "2024 - 2023"
            if (!isset($progressGroups[0])) {
                $progressGroups[0] = [];
            }
            if (!isset($progressGroups[0][$key])) {
                $progressGroups[0][$key] = $this->createEmptyProgressData();
                $progressGroups[0][$key]->id = $progressItem->id;
                $progressGroups[0][$key]->last_year = $progressItem->last_year;
                $progressGroups[0][$key]->current_year = $progressItem->current_year;
                $progressGroups[0][$key]->last_grade = $progressItem->last_grade;
                $progressGroups[0][$key]->current_grade = $progressItem->current_grade;
            }

            $progressGroups[0][$key]->total += $progressItem->total;
            $progressGroups[0][$key]->below += $progressItem->below;
            $progressGroups[0][$key]->inline += $progressItem->inline;
            $progressGroups[0][$key]->above += $progressItem->above;

            foreach ($progressItem->categories as $cat_key => $category) {
                if (!isset($progressGroups[0][$key]->categories[$cat_key])) {
                    $progressGroups[0][$key]->categories[$cat_key] = $this->createEmptyCategoryProgressData();
                }
                $progressGroups[0][$key]->categories[$cat_key]->total += $category->total;
                $progressGroups[0][$key]->categories[$cat_key]->below += $category->below;
                $progressGroups[0][$key]->categories[$cat_key]->inline += $category->inline;
                $progressGroups[0][$key]->categories[$cat_key]->above += $category->above;
            }
        }

        // Recalculate percentages for progress data
        if (count($progressGroups) > 0) {
            foreach ($progressGroups[0] as $key => $progressData) {
                $progressGroups[0][$key]->per_below = $this->calculatePercentage($progressData->below, $progressData->total);
                $progressGroups[0][$key]->per_inline = $this->calculatePercentage($progressData->inline, $progressData->total);
                $progressGroups[0][$key]->per_above = $this->calculatePercentage($progressData->above, $progressData->total);
                foreach ($progressGroups[0][$key]->categories as $cat_key => $category) {
                    $progressGroups[0][$key]->categories[$cat_key]->per_below = $this->calculatePercentage($category->below, $category->total);
                    $progressGroups[0][$key]->categories[$cat_key]->per_inline = $this->calculatePercentage($category->inline, $category->total);
                    $progressGroups[0][$key]->categories[$cat_key]->per_above = $this->calculatePercentage($category->above, $category->total);
                }
            }
            $combinedProgress = array_values($progressGroups);
        } else {
            $combinedProgress = []; // No progress data available
        }


        return [
            'attainment' => $combinedAttainment,
            'progress' => $combinedProgress,
            'pages_type_text' => $sectionResults['pages_type_text'],
            'pages_type' => $sectionResults['pages_type'],
            'title' => 'Combined Grades for ' . ($sectionResults['pages_type_text'] == 'arabs' ? 'Arabs' : 'Non-Arabs'),
        ];
    }

    /**
     * Create empty statistics structure for combining
     */
    private function createEmptyStatisticsStructure()
    {
        return (object)[
            'total' => 0,
            'below' => 0,
            'inline' => 0,
            'above' => 0,
            'boys' => (object)[
                'total' => 0,
                'below' => 0,
                'inline' => 0,
                'above' => 0,
            ],
            'girls' => (object)[
                'total' => 0,
                'below' => 0,
                'inline' => 0,
                'above' => 0,
            ],
            'sen_students' => (object)[
                'total' => 0,
                'below' => 0,
                'inline' => 0,
                'above' => 0,
            ],
            'g_t_students' => (object)[
                'total' => 0,
                'below' => 0,
                'inline' => 0,
                'above' => 0,
            ],
            'citizen_students' => (object)[
                'total' => 0,
                'below' => 0,
                'inline' => 0,
                'above' => 0,
            ],
            'citizen_boys_students' => (object)[
                'total' => 0,
                'below' => 0,
                'inline' => 0,
                'above' => 0,
            ],
            'citizen_girls_students' => (object)[
                'total' => 0,
                'below' => 0,
                'inline' => 0,
                'above' => 0,
            ],
            'mark_step1' => (object)[
                'total' => 0,
                'below' => 0,
                'inline' => 0,
                'above' => 0,
            ],
            'mark_step2' => (object)[
                'total' => 0,
                'below' => 0,
                'inline' => 0,
                'above' => 0,
            ],
            'mark_step3' => (object)[
                'total' => 0,
                'below' => 0,
                'inline' => 0,
                'above' => 0,
            ],
            'mark_step4' => (object)[
                'total' => 0,
                'below' => 0,
                'inline' => 0,
                'above' => 0,
            ],
            'mark_step5' => (object)[
                'total' => 0,
                'below' => 0,
                'inline' => 0,
                'above' => 0,
            ],
            'mark_step6' => (object)[
                'total' => 0,
                'below' => 0,
                'inline' => 0,
                'above' => 0,
            ],
        ];
    }

    /**
     * Add statistics data to existing combined data
     */
    private function addStatisticsData($combinedData, $newData)
    {
        // Add main statistics
        $combinedData->total += $newData->total ?? 0;
        $combinedData->below += $newData->below ?? 0;
        $combinedData->inline += $newData->inline ?? 0;
        $combinedData->above += $newData->above ?? 0;

        // Add detailed statistics
        $categories = ['boys', 'girls', 'sen_students', 'g_t_students', 'citizen_students',
            'citizen_boys_students', 'citizen_girls_students'];

        foreach ($this->subjects as $subject) {
            $categories[] = 'mark_step' . $subject->id;
        }

        foreach ($categories as $category) {
            if (isset($newData->$category)) {
                $combinedData->$category->total += $newData->$category->total ?? 0;
                $combinedData->$category->below += $newData->$category->below ?? 0;
                $combinedData->$category->inline += $newData->$category->inline ?? 0;
                $combinedData->$category->above += $newData->$category->above ?? 0;
            }
        }

        return $combinedData;
    }

    /**
     * Recalculate percentages for combined data
     */
    private function recalculatePercentages($data)
    {
        // Calculate main percentages
        $data->per_below = $this->calculatePercentage($data->below, $data->total);
        $data->per_inline = $this->calculatePercentage($data->inline, $data->total);
        $data->per_above = $this->calculatePercentage($data->above, $data->total);

        // Calculate percentages for detailed categories
        $categories = ['boys', 'girls', 'sen_students', 'g_t_students', 'citizen_students',
            'citizen_boys_students', 'citizen_girls_students'];

        foreach ($this->subjects as $subject) {
            $categories[] = 'mark_step' . $subject->id;
        }

        foreach ($categories as $category) {
            $data->$category->per_below = $this->calculatePercentage($data->$category->below, $data->$category->total);
            $data->$category->per_inline = $this->calculatePercentage($data->$category->inline, $data->$category->total);
            $data->$category->per_above = $this->calculatePercentage($data->$category->above, $data->$category->total);
        }

        return $data;
    }

    /**
     * Create empty progress data structure
     */
    private function createEmptyProgressData()
    {
        return (object)[
            'id' => '',
            'last_year' => '',
            'current_year' => '',
            'last_grade' => '',
            'current_grade' => '',
            'total' => 0,
            'below' => 0,
            'inline' => 0,
            'above' => 0,
            'per_below' => 0,
            'per_inline' => 0,
            'per_above' => 0,
        ];
    }

    private function createEmptyCategoryProgressData()
    {
        return (object)[
            'total' => 0,
            'below' => 0,
            'inline' => 0,
            'above' => 0,
            'per_below' => 0,
            'per_inline' => 0,
            'per_above' => 0,
        ];
    }
    public function report()
    {
        $result = $this->calculateNormalReport();
//        dd($result);
        return $this->renderReport($result['pages']);
    }

    public function combinedReport()
    {
        $this->isCombined = true;
        $result = $this->calculateCombinedReport();
//        dd($result);
        return $this->renderReport($result['pages']);
    }

    private function getEligibleStudentsOptimized()
    {
        $studentsAbtIdsByGrade = [];

        foreach ($this->selectedGrades as $grade) {
            if ((count($this->years) == 2 && $grade == 1) || (count($this->years) == 3 && $grade == 2)) {
                continue;
            }

            $abtIds = $this->findEligibleStudentsForGrade($grade);
            if (!empty($abtIds)) {
                $studentsAbtIdsByGrade[$grade] = $abtIds;
            }
        }

        return $studentsAbtIdsByGrade;
    }

    private function findEligibleStudentsForGrade($targetGrade)
    {
        $schoolIds = $this->schools->pluck('id')->toArray();
        $yearCount = count($this->years);

        // Build year-grade pairs for this target grade
        $yearGradePairs = [];
        $currentGrade = $targetGrade;
        foreach ($this->reversedYears as $year) {
            $yearGradePairs[] = ['year_id' => $year, 'grade' => $currentGrade];
            $currentGrade--;
        }

        // Find students who have completed assessments for all required year-grade combinations
        $studentAbtIds = [];

        foreach ($yearGradePairs as $index => $pair) {
            $query = DB::table('students as s')
                ->select('s.abt_id')
                ->join('student_terms as st', 's.id', '=', 'st.student_id')
                ->join('terms as t', 'st.term_id', '=', 't.id')
                ->join('levels as l', 't.level_id', '=', 'l.id')
                ->whereIn('s.school_id', $schoolIds)
                ->whereNotNull('s.abt_id')
                ->where('l.year_id', $pair['year_id'])
                ->where('l.grade', $pair['grade'])
                ->where('t.round', $this->round)
                ->where('st.corrected', 1)
                ->whereNull('s.deleted_at')
                ->whereNull('st.deleted_at')
                ->whereNull('t.deleted_at')
                ->whereNull('l.deleted_at');

            // Add section filter if specified
            if ($this->student_type != 2) {
                $query->where('l.arab', $this->student_type);
            }

            $currentYearAbtIds = $query->pluck('s.abt_id')->toArray();

            if ($index === 0) {
                $studentAbtIds = $currentYearAbtIds;
            } else {
                // Keep only students who appear in all years
                $studentAbtIds = array_intersect($studentAbtIds, $currentYearAbtIds);
            }

            // If no students left, no point continuing
            if (empty($studentAbtIds)) {
                break;
            }
        }

        return array_values(array_unique($studentAbtIds));
    }

    private function calculateAttainmentOptimized($abtIds, $yearId, $grade)
    {
        if (empty($abtIds)) {
            return $this->createEmptyStatisticsWithDetails($yearId);
        }

        $schoolIds = $this->schools->pluck('id')->toArray();

        $steps_raw_sql = '';
        $bindings = [];
        $stepNumber = 1;

        foreach ($this->subjects as $subject) {
            $subject_id = $subject->id;

            $steps_raw_sql .= "
        SUM(CASE WHEN (
            SELECT COALESCE(j.mark, 0)
            FROM JSON_TABLE(
                st.subjects_marks,
                '$[*]' COLUMNS (
                    subject_id INT PATH '$.subject_id',
                    mark DECIMAL(10,2) PATH '$.mark'
                )
            ) AS j
            WHERE j.subject_id = {$subject_id}
            LIMIT 1
        ) < ? THEN 1 ELSE 0 END) AS mark_step{$stepNumber}_below,

        SUM(CASE WHEN (
            SELECT COALESCE(j.mark, 0)
            FROM JSON_TABLE(
                st.subjects_marks,
                '$[*]' COLUMNS (
                    subject_id INT PATH '$.subject_id',
                    mark DECIMAL(10,2) PATH '$.mark'
                )
            ) AS j
            WHERE j.subject_id = {$subject_id}
            LIMIT 1
        ) >= ? AND (
            SELECT COALESCE(j.mark, 0)
            FROM JSON_TABLE(
                st.subjects_marks,
                '$[*]' COLUMNS (
                    subject_id INT PATH '$.subject_id',
                    mark DECIMAL(10,2) PATH '$.mark'
                )
            ) AS j
            WHERE j.subject_id = {$subject_id}
            LIMIT 1
        ) < ? THEN 1 ELSE 0 END) AS mark_step{$stepNumber}_inline,

        SUM(CASE WHEN (
            SELECT COALESCE(j.mark, 0)
            FROM JSON_TABLE(
                st.subjects_marks,
                '$[*]' COLUMNS (
                    subject_id INT PATH '$.subject_id',
                    mark DECIMAL(10,2) PATH '$.mark'
                )
            ) AS j
            WHERE j.subject_id = {$subject_id}
            LIMIT 1
        ) >= ? THEN 1 ELSE 0 END) AS mark_step{$stepNumber}_above,
    ";

            $range = $subject->marks_range;
            // Add bindings for each placeholder
            $bindings[] = $range['below']['from'];
            $bindings[] = $range['inline']['from'];
            $bindings[] = $range['inline']['to'];
            $bindings[] = $range['above']['from'];

            $stepNumber++;
        }

        // Remove last comma
        $steps_raw_sql = rtrim($steps_raw_sql, ',');


        $result = DB::table('student_terms as st')
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN st.total < ? THEN 1 ELSE 0 END) as below,
                SUM(CASE WHEN st.total >= ? AND st.total < ? THEN 1 ELSE 0 END) as inline,
                SUM(CASE WHEN st.total >= ? THEN 1 ELSE 0 END) as above,

                -- Boys statistics
                SUM(CASE WHEN s.gender = "boy" THEN 1 ELSE 0 END) as boys_total,
                SUM(CASE WHEN s.gender = "boy" AND st.total < ? THEN 1 ELSE 0 END) as boys_below,
                SUM(CASE WHEN s.gender = "boy" AND st.total >= ? AND st.total < ? THEN 1 ELSE 0 END) as boys_inline,
                SUM(CASE WHEN s.gender = "boy" AND st.total >= ? THEN 1 ELSE 0 END) as boys_above,

                -- Girls statistics
                SUM(CASE WHEN s.gender = "girl" THEN 1 ELSE 0 END) as girls_total,
                SUM(CASE WHEN s.gender = "girl" AND st.total < ? THEN 1 ELSE 0 END) as girls_below,
                SUM(CASE WHEN s.gender = "girl" AND st.total >= ? AND st.total < ? THEN 1 ELSE 0 END) as girls_inline,
                SUM(CASE WHEN s.gender = "girl" AND st.total >= ? THEN 1 ELSE 0 END) as girls_above,

                -- SEN Students statistics
                SUM(CASE WHEN s.sen = 1 THEN 1 ELSE 0 END) as sen_total,
                SUM(CASE WHEN s.sen = 1 AND st.total < ? THEN 1 ELSE 0 END) as sen_below,
                SUM(CASE WHEN s.sen = 1 AND st.total >= ? AND st.total < ? THEN 1 ELSE 0 END) as sen_inline,
                SUM(CASE WHEN s.sen = 1 AND st.total >= ? THEN 1 ELSE 0 END) as sen_above,

                -- GT Students statistics
                SUM(CASE WHEN s.g_t = 1 THEN 1 ELSE 0 END) as g_t_total,
                SUM(CASE WHEN s.g_t = 1 AND st.total < ? THEN 1 ELSE 0 END) as g_t_below,
                SUM(CASE WHEN s.g_t = 1 AND st.total >= ? AND st.total < ? THEN 1 ELSE 0 END) as g_t_inline,
                SUM(CASE WHEN s.g_t = 1 AND st.total >= ? THEN 1 ELSE 0 END) as g_t_above,

                -- Citizen Students statistics
                SUM(CASE WHEN s.citizen = 1 THEN 1 ELSE 0 END) as citizen_total,
                SUM(CASE WHEN s.citizen = 1 AND st.total < ? THEN 1 ELSE 0 END) as citizen_below,
                SUM(CASE WHEN s.citizen = 1 AND st.total >= ? AND st.total < ? THEN 1 ELSE 0 END) as citizen_inline,
                SUM(CASE WHEN s.citizen = 1 AND st.total >= ? THEN 1 ELSE 0 END) as citizen_above,

                -- Citizen boys Students statistics
                SUM(CASE WHEN s.citizen = 1 AND s.gender = "boy" THEN 1 ELSE 0 END) as citizen_boys_total,
                SUM(CASE WHEN s.citizen = 1 AND s.gender = "boy" AND st.total < ? THEN 1 ELSE 0 END) as citizen_boys_below,
                SUM(CASE WHEN s.citizen = 1 AND s.gender = "boy" AND st.total >= ? AND st.total < ? THEN 1 ELSE 0 END) as citizen_boys_inline,
                SUM(CASE WHEN s.citizen = 1 AND s.gender = "boy" AND st.total >= ? THEN 1 ELSE 0 END) as citizen_boys_above,

                -- Citizen girls Students statistics
                SUM(CASE WHEN s.citizen = 1 AND s.gender = "girl" THEN 1 ELSE 0 END) as citizen_girls_total,
                SUM(CASE WHEN s.citizen = 1 AND s.gender = "girl" AND st.total < ? THEN 1 ELSE 0 END) as citizen_girls_below,
                SUM(CASE WHEN s.citizen = 1 AND s.gender = "girl" AND st.total >= ? AND st.total < ? THEN 1 ELSE 0 END) as citizen_girls_inline,
                SUM(CASE WHEN s.citizen = 1 AND s.gender = "girl" AND st.total >= ? THEN 1 ELSE 0 END) as citizen_girls_above,

            '.$steps_raw_sql,array_merge( [
                // Overall calculations
                $this->markData->below->to,
                $this->markData->inline->from,
                $this->markData->inline->to,
                $this->markData->above->from,

                // Boys calculations
                $this->markData->below->to,
                $this->markData->inline->from,
                $this->markData->inline->to,
                $this->markData->above->from,

                // Girls calculations
                $this->markData->below->to,
                $this->markData->inline->from,
                $this->markData->inline->to,
                $this->markData->above->from,

                // SEN calculations
                $this->markData->below->to,
                $this->markData->inline->from,
                $this->markData->inline->to,
                $this->markData->above->from,

                // Non-SEN calculations
                $this->markData->below->to,
                $this->markData->inline->from,
                $this->markData->inline->to,
                $this->markData->above->from,

                // Citizen calculations
                $this->markData->below->to,
                $this->markData->inline->from,
                $this->markData->inline->to,
                $this->markData->above->from,

                // Citizen Girls calculations
                $this->markData->below->to,
                $this->markData->inline->from,
                $this->markData->inline->to,
                $this->markData->above->from,

                // Citizen Boys calculations
                $this->markData->below->to,
                $this->markData->inline->from,
                $this->markData->inline->to,
                $this->markData->above->from,
            ],$bindings))
            ->join('students as s', 'st.student_id', '=', 's.id')
            ->join('terms as t', 'st.term_id', '=', 't.id')
            ->join('levels as l', 't.level_id', '=', 'l.id')
            ->whereIn('s.school_id', $schoolIds)
            ->whereIn('s.abt_id', $abtIds)
            ->where('l.year_id', $yearId)
            ->where('l.grade', $grade)
            ->when($this->student_type != 2, function ($query) {
                return $query->where('l.arab', $this->student_type);
            })
            ->where('t.month', $this->round)
            ->where('st.corrected', 1)
            ->whereNull('st.deleted_at')
            ->whereNull('s.deleted_at')
            ->whereNull('t.deleted_at')
            ->whereNull('l.deleted_at')
            ->first();

        if (!$result || $result->total == 0) {
            return $this->createEmptyStatisticsWithDetails($yearId);
        }
        return $this->formatStatisticsDataWithDetails($yearId, $result);
    }

    private function createEmptyStatisticsWithDetails($yearId)
    {
        $emptyData = [
            'total' => 0, 'below' => 0, 'inline' => 0, 'above' => 0,
            'boys_total' => 0, 'boys_below' => 0, 'boys_inline' => 0, 'boys_above' => 0,
            'girls_total' => 0, 'girls_below' => 0, 'girls_inline' => 0, 'girls_above' => 0,
            'sen_total' => 0, 'sen_below' => 0, 'sen_inline' => 0, 'sen_above' => 0,
            'g_t_total' => 0, 'g_t_below' => 0, 'g_t_inline' => 0, 'g_t_above' => 0,
            'citizen_total' => 0, 'citizen_below' => 0, 'citizen_inline' => 0, 'citizen_above' => 0,
            'citizen_boys_total' => 0, 'citizen_boys_below' => 0, 'citizen_boys_inline' => 0, 'citizen_boys_above' => 0,
            'citizen_girls_total' => 0, 'citizen_girls_below' => 0, 'citizen_girls_inline' => 0, 'citizen_girls_above' => 0,
        ];

        foreach ($this->subjects as $subject) {
            $emptyData['mark_step'.$subject->id.'_below'] = 0;
            $emptyData['mark_step'.$subject->id.'_inline'] = 0;
            $emptyData['mark_step'.$subject->id.'_above'] = 0;
        }

        $emptyData = (object)$emptyData;

        return $this->formatStatisticsDataWithDetails($yearId, $emptyData);
    }

    private function calculateProgressOptimized($abtIds, $yearId, $lastYearId, $grade, $lastGrade)
    {
        if (empty($abtIds)) {
            return $this->formatProgressData($lastYearId, $yearId, $lastGrade, $grade, 0, 0, 0);
        }

        $schoolIds = $this->schools->pluck('id')->toArray();

        // Get current year marks
        $currentMarksData = DB::table('student_terms as st')
            ->select('s.abt_id', 'st.total as mark', 's.gender', 's.citizen', 's.sen', 's.g_t')
            ->join('students as s', 'st.student_id', '=', 's.id')
            ->join('terms as t', 'st.term_id', '=', 't.id')
            ->join('levels as l', 't.level_id', '=', 'l.id')
            ->whereIn('s.school_id', $schoolIds)
            ->whereIn('s.abt_id', $abtIds)
            ->where('l.year_id', $yearId)
            ->where('l.grade', $grade)
            ->where('t.round', $this->round)
            ->where('st.corrected', 1)
            ->whereNull('st.deleted_at')
            ->whereNull('s.deleted_at')
            ->whereNull('t.deleted_at')
            ->whereNull('l.deleted_at')
            ->get();

        $currentMarks = $currentMarksData->pluck('mark', 'abt_id')
            ->toArray();

        // Get last year marks
        $lastMarksData = DB::table('student_terms as st')
            ->select('s.abt_id', 'st.total as mark', 's.gender', 's.citizen', 's.sen', 's.g_t')
            ->join('students as s', 'st.student_id', '=', 's.id')
            ->join('terms as t', 'st.term_id', '=', 't.id')
            ->join('levels as l', 't.level_id', '=', 'l.id')
            ->whereIn('s.school_id', $schoolIds)
            ->whereIn('s.abt_id', $abtIds)
            ->where('l.year_id', $lastYearId)
            ->where('l.grade', $lastGrade)
            ->where('t.round', $this->round)
            ->where('st.corrected', 1)
            ->whereNull('st.deleted_at')
            ->whereNull('s.deleted_at')
            ->whereNull('t.deleted_at')
            ->whereNull('l.deleted_at')
            ->get();

        $lastMarks = $lastMarksData
            ->pluck('mark', 'abt_id')
            ->toArray();

        $below = $inline = $above = 0;
        $boys = ['below' => 0, 'inline' => 0, 'above' => 0, 'total' => 0];
        $girls = ['below' => 0, 'inline' => 0, 'above' => 0, 'total' => 0];
        $sen_students = ['below' => 0, 'inline' => 0, 'above' => 0, 'total' => 0];
        $g_t_students = ['below' => 0, 'inline' => 0, 'above' => 0, 'total' => 0];
        $citizen_students = ['below' => 0, 'inline' => 0, 'above' => 0, 'total' => 0];
        $citizen_boys_students = ['below' => 0, 'inline' => 0, 'above' => 0, 'total' => 0];
        $citizen_girls_students = ['below' => 0, 'inline' => 0, 'above' => 0, 'total' => 0];


        // Calculate progress for students who have both marks
        foreach ($abtIds as $abtId) {
            if (isset($currentMarks[$abtId]) && isset($lastMarks[$abtId])) {
                $current = $currentMarksData->where('abt_id', $abtId)->first();
                $progressDiff = $lastMarks[$abtId] - $currentMarks[$abtId];
                $progress = progressOverTime($currentMarks[$abtId], $progressDiff);

                switch ($progress) {
                    case 1:
                        $below++;
                        break;
                    case 2:
                        $inline++;
                        break;
                    case 3:
                        $above++;
                        break;
                }
                //check currentMarks for student details
                if ($current->gender == 1) {
                    $this->updateProgressCounters($boys, $progress);
                }

                if ($current->gender == 2) {
                    $this->updateProgressCounters($girls, $progress);
                }

                if ($current->uae_student == 1) {
                    $this->updateProgressCounters($citizen_students, $progress);

                    // UAE citizen boys
                    if ($current->gender == 1) {
                        $this->updateProgressCounters($citizen_boys_students, $progress);
                    }
                    // UAE citizen girls
                    if ($current->gender == 2) {
                        $this->updateProgressCounters($citizen_girls_students, $progress);
                    }
                }

                if ($current->sen_student == 1) {
                    $this->updateProgressCounters($sen_students, $progress);
                }

                if ($current->g_t == 1) {
                    $this->updateProgressCounters($g_t_students, $progress);
                }

            }
        }

        $categories = [
            'boys' => (object)$boys,
            'girls' => (object)$girls,
            'sen_students' => (object)$sen_students,
            'g_t_students' => (object)$g_t_students,
            'citizen_students' => (object)$citizen_students,
            'citizen_boys_students' => (object)$citizen_boys_students,
            'citizen_girls_students' => (object)$citizen_girls_students,
        ];

        return $this->formatProgressData($lastYearId, $yearId, $lastGrade, $grade, $below, $inline, $above, $categories);
    }

    // Helper function to update progress counters
    private function updateProgressCounters(&$counters, $progress) {
        $progressMap = [1 => 'below', 2 => 'inline', 3 => 'above'];

        $counters['total']++;
        if (isset($progressMap[$progress])) {
            $counters[$progressMap[$progress]]++;
        }
    }

    private function initializeReportParameters()
    {
        $this->round = $this->request->get('round', false);
        $this->student_type = $this->request->get('student_type', 0);
        $this->selectedGrades = $this->request->get('grades', []);
        $this->years = $this->request->get('years', []);
        $examsYear = [];
        // Adjust years for Indian/Pakistan/Bangladeshi schools
        if (in_array($this->school->school_type, ['Indian', 'Pakistan', 'Bangladeshi'])) {
            if (in_array($this->round, ['may'])) {
                foreach ($this->years as $year) {
                    $examsYear[] = $year - 1;
                }
            } else {
                // For other rounds, use the years as they are
                $examsYear = $this->years;
            }
        } else {
            // For other school types, use the years as they are
            $examsYear = $this->years;
        }
        $this->reversedYears = array_reverse($examsYear);
        $this->reportType = $this->request->get('ranges_type', 1);
        $this->yearCollection = Year::query()->whereIn('id', $examsYear)->get();
        $this->lastYear = count($this->years) == 3 ? $this->years[2] : $this->years[1];


//        dd($this->years, $this->reversedYears);
    }

    private function formatStatisticsDataWithDetails($yearId, $data)
    {
//        dd($data);
        $data_array = [
            'id' => $yearId,

            // Overall statistics
            'total' => $data->total,
            'below' => $data->below,
            'inline' => $data->inline,
            'above' => $data->above,
            'per_below' => $this->calculatePercentage($data->below, $data->total),
            'per_inline' => $this->calculatePercentage($data->inline, $data->total),
            'per_above' => $this->calculatePercentage($data->above, $data->total),

            // Boys statistics
            'boys' => $this->createCategoryStats($data->boys_total, $data->boys_below, $data->boys_inline, $data->boys_above),

            // Girls statistics
            'girls' => $this->createCategoryStats($data->girls_total, $data->girls_below, $data->girls_inline, $data->girls_above),

            // SEN Students statistics
            'sen_students' => $this->createCategoryStats($data->sen_total, $data->sen_below, $data->sen_inline, $data->sen_above),

            // Non-SEN Students statistics
            'g_t_students' => $this->createCategoryStats($data->g_t_total, $data->g_t_below, $data->g_t_inline, $data->g_t_above),

            // Citizen Students statistics
            'citizen_students' => $this->createCategoryStats($data->citizen_total, $data->citizen_below, $data->citizen_inline, $data->citizen_above),

            // Citizen Boys Students statistics
            'citizen_boys_students' => $this->createCategoryStats($data->citizen_boys_total, $data->citizen_boys_below, $data->citizen_boys_inline, $data->citizen_boys_above),

            // Citizen Girls Students statistics
            'citizen_girls_students' => $this->createCategoryStats($data->citizen_girls_total, $data->citizen_girls_below, $data->citizen_girls_inline, $data->citizen_girls_above),
        ];

        foreach ($this->subjects as $subject) {
            $data_array['mark_step'.$subject->id] = $this->createCategoryStats($data->total, $data->{"mark_step".$subject->id."_below"}, $data->{"mark_step".$subject->id."_inline"}, $data->{"mark_step".$subject->id."_above"});
        }

        return(object)$data;
    }

    /**
     * helper method for category statistics (with total)
     */
    private function createCategoryStats($total, $below, $inline, $above)
    {
        return (object)[
            'total' => $total,
            'below' => $below,
            'inline' => $inline,
            'above' => $above,
            'per_below' => $this->calculatePercentage($below, $total),
            'per_inline' => $this->calculatePercentage($inline, $total),
            'per_above' => $this->calculatePercentage($above, $total),
        ];
    }


    private function formatStatisticsData($yearId, $total, $below, $inline, $above)
    {
        return (object)[
            'id' => $yearId,
            'total' => $total,
            'below' => $below,
            'inline' => $inline,
            'above' => $above,
            'per_below' => $this->calculatePercentage($below, $total),
            'per_inline' => $this->calculatePercentage($inline, $total),
            'per_above' => $this->calculatePercentage($above, $total),
        ];
    }

    private function formatProgressData($lastYearId, $yearId, $lastGrade, $grade, $below, $inline, $above, $categories = [])
    {
        $total = $below + $inline + $above;
        $lastYearIdName = $this->yearCollection->where('id', $lastYearId)->first()->name ?? 'Unknown Year';
        $yearIdName = $this->yearCollection->where('id', $yearId)->first()->name ?? 'Unknown Year';

        $processedCategories = [];
        foreach ($categories as $categoryName => $categoryData) {
            $processedCategories[$categoryName] = (object)[
                'below' => $categoryData->below,
                'inline' => $categoryData->inline,
                'above' => $categoryData->above,
                'total' => $categoryData->total,
                'per_below' => $this->calculatePercentage($categoryData->below, $categoryData->total),
                'per_inline' => $this->calculatePercentage($categoryData->inline, $categoryData->total),
                'per_above' => $this->calculatePercentage($categoryData->above, $categoryData->total),
            ];
        }
        return (object)[
            'id' => "$yearIdName - $lastYearIdName",
            'last_year' => "$lastYearId",
            'current_year' => "$yearId",
            'last_grade' => "$lastGrade",
            'current_grade' => "$grade",
            'total' => $total,
            'below' => $below,
            'inline' => $inline,
            'above' => $above,
            'per_below' => $this->calculatePercentage($below, $total),
            'per_inline' => $this->calculatePercentage($inline, $total),
            'per_above' => $this->calculatePercentage($above, $total),
            'categories' => $processedCategories,
        ];
    }

    private function calculatePercentage($value, $total)
    {
        if ($value > 0 && $total > 0) {
            return round(($value / $total) * 100, 2);
        }
        return 0;
    }

    private function generateCacheKey()
    {
        return 'year_to_year_report_' . md5(serialize([
                'schools' => $this->schools->pluck('id')->toArray(),
                'round' => $this->round,
                'years' => $this->years,
                'grades' => $this->selectedGrades,
                'section' => $this->student_type,
                'ranges_type' => $this->reportType
            ]));
    }

    private function renderReport($pages = [], $data = [])
    {
        $school = $this->school;
        $schools = $this->schools;
        $student_type = $this->request->get('student_type', 0);
        $student_type_title = $this->getSubTitle($this->request->get('student_type'));
        $years = $this->yearCollection->sortBy('id');
        $rangesType = $this->request->get('ranges_type', 1);
        $isCombined = $this->isCombined;
        $grades = $this->selectedGrades;
        $round = $this->round;
        $title = $this->generateReportTitle();
        $reportTitleGroup = $this->reportTitleGroup();
        $subjects = $this->subjects;
        return view('general.new_reports.year_to_year.year_to_year_report',
            compact('school', 'schools', 'pages', 'rangesType', 'data', 'isCombined', 'years',
                'student_type', 'title', 'subjects', 'student_type_title', 'grades', 'round', 'reportTitleGroup'));
    }

    private function generateReportTitle()
    {
        $school = null;
        if (count($this->schools) == 1) {
            $school = ' - ' . $this->school->name;
        }
        if (count($this->years) == 2) {
            $type = 'Year to Year';
        } else {
            $type = 'Trend Over Time';
        }
        $combinedText = $this->isCombined ? 'Combined ' : '';
        return $type . ' ' . $combinedText . 'Progress Report' . $school;
    }

    private function getSubTitle($type)
    {
        switch ($type) {
            case 0:
                return 'Non-Arabs';
            case 1:
                return 'Arabs';
            case 2:
                return 'Arabs & Non-arabs';
            default:
                return '';
        }
    }

    private function getStudentTypeText($type)
    {
        switch ($type) {
            case 0:
                return 'non_arabs';
            case 1:
                return 'arabs';
            case 2:
                return 'arabs_non_arabs';
            default:
                return '';
        }
    }

    public function reportTitleGroup()
    {
        $titleGroup = [];
        if (count($this->years) == 2) {
            if ($this->isCombined) {
                $titleGroup['ar'] = 'تقرير التقدم المدمج <br /> من عام إلى عام';
                $titleGroup['en'] = 'Combined Year to Year <br /> Progress Report';
            } else {
                $titleGroup['ar'] = 'تقرير التقدم من عام إلى عام';
                $titleGroup['en'] = 'Year to Year Progress Report';
            }
        } else {
            if ($this->isCombined) {
                $titleGroup['ar'] = 'تقرير التقدم المدمج <br /> على مر الزمن';
                $titleGroup['en'] = 'Combined Trend Over Time <br /> Progress Report';
            } else {
                $titleGroup['ar'] = 'تقرير التقدم على مر الزمن';
                $titleGroup['en'] = 'Trend Over Time Progress Report';
            }
        }
        return $titleGroup;
    }


}
