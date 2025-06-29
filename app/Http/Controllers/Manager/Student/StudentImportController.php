<?php

namespace App\Http\Controllers\Manager\Student;

use App\Exports\StudentExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\ImportStudentFileRequest;
use App\Http\Requests\Manager\ImportStudentRequest;
use App\Http\Requests\Manager\StudentImportLogDataRequest;
use App\Imports\OldStudentImport;
use App\Imports\StudentImport;
use App\Models\ImportStudentFile;
use App\Models\Level;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentImportFile;
use App\Models\StudentImportFileLog;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class StudentImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show students import')->only('index');
        $this->middleware('permission:import students')->only(['create', 'store']);
        $this->middleware('permission:delete students import')->only(['delete']);
    }

    //Import Student
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $rows = StudentImportFile::with(['school'])->withCount(['logs', 'logErrors'])->search($request)->latest();

            // Helper function to generate status badge
            $getStatusBadge = function($status) {
                $statusColors = [
                    'New' => 'badge-primary',
                    'Uploading' => 'badge-warning',
                    'Completed' => 'badge-success',
                    'Failures' => 'badge-danger',
                    'Errors' => 'badge-danger',
                ];

                $statusIcons = [
                    'New' => 'fas fa-clock text-white',
                    'Uploading' => 'fas fa-upload text-white',
                    'Completed' => 'fas fa-check-circle text-white',
                    'Failures' => 'fas fa-times-circle text-white',
                    'Errors' => 'fas fa-exclamation-circle text-white',
                ];

                $badgeClass = $statusColors[$status] ?? 'badge-secondary';
                $icon = $statusIcons[$status] ?? 'fas fa-question-circle';

                return '<span class="badge ' . $badgeClass . '">' .
                    '<i class="' . $icon . ' me-1"></i>' .
                    t($status) . '</span>';
            };

            // Helper function to generate process type badge
            $getProcessTypeBadge = function($processType) {
                $processColors = [
                    'create' => 'badge-success',
                    'update' => 'badge-warning',
                    'delete' => 'badge-danger',
                ];

                $processIcons = [
                    'create' => 'fas fa-plus-circle text-white',
                    'update' => 'fas fa-edit text-white',
                    'delete' => 'fas fa-trash text-white',
                ];

                $badgeClass = $processColors[$processType] ?? 'badge-secondary';
                $icon = $processIcons[$processType] ?? 'fas fa-cog';

                return '<span class="badge ' . $badgeClass . ' mb-1">' .
                    '<i class="' . $icon . ' text-white me-1"></i>' .
                    t(ucfirst($processType ?? 'N/A')) . '</span>';
            };

            // Helper function to generate count badge
            $getCountBadge = function($label, $count, $badgeClass, $icon) {
                return '<span class="badge ' . $badgeClass . ' mb-1">' .
                    '<i class="' . $icon . ' text-white me-1"></i>' .
                    t($label) . ': ' . ($count ?? 0) . '</span>';
            };

            // Helper function to generate link badge
            $getLinkBadge = function($count, $route, $label, $icon, $activeClass, $inactiveClass = 'badge-secondary') {
                if ($count > 0) {
                    return '<a href="' . $route . '" class="badge ' . $activeClass . ' text-decoration-none mb-1">' .
                        '<i class="' . $icon . ' me-1"></i>' .
                        t($label) . ': ' . $count . '</a>';
                } else {
                    return '<span class="badge ' . $inactiveClass . ' mb-1">' .
                        '<i class="' . $icon . ' text-dark me-1"></i>' .
                        t($label) . ': ' . $count . '</span>';
                }
            };

            return DataTables::make($rows)
                ->escapeColumns([])
                ->addColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('Y-m-d H:i:s');
                })
                ->addColumn('file_info', function ($row) {
                    $schoolName = $row->school ? $row->school->name : t('Unknown');
                    $fileIcon = '<i class="fas fa-file-download"></i>';
                    return '<span class="font-weight-bold">' . $schoolName . '</span><br>' .
                        '<a href="' . asset($row->file_path) . '" target="_blank" class="text-primary">' .
                        $fileIcon . ' ' . t('Download File') . '</a>';
                })
                ->addColumn('statistics', function ($row) use ($getCountBadge) {
                    $createdBadge = $getCountBadge('Created', $row->row_count, 'badge-success', 'fas fa-plus-circle');
                    $updatedBadge = $getCountBadge('Updated', $row->updated_row_count, 'badge-info', 'fas fa-edit');
                    $deletedBadge = $getCountBadge('Deleted', $row->deleted_row_count, 'badge-danger', 'fas fa-trash');

                    return '<div class="text-nowrap">' .
                        $createdBadge . '<br>' .
                        $updatedBadge . '<br>' .
                        $deletedBadge . '</div>';
                })
                ->addColumn('process', function ($row) use ($getProcessTypeBadge, $getStatusBadge) {
                    $processTypeBadge = $getProcessTypeBadge($row->process_type);
                    $statusBadge = $getStatusBadge($row->status);

                    return '<div class="text-nowrap">' . $processTypeBadge . '<br>' . $statusBadge . '</div>';
                })
                ->addColumn('logs_info', function ($row) use ($getLinkBadge) {
                    $showLogRoute = route('manager.students_files_import.show_logs', [$row->id]);
                    $showErrorRoute = route('manager.students_files_import.show', [$row->id]);

                    $logsLink = $getLinkBadge(
                        $row->logs_count,
                        $showLogRoute,
                        'Logs',
                        'fas fa-list text-white',
                        'badge-warning'
                    );

                    $errorsLink = $getLinkBadge(
                        $row->log_errors_count,
                        $showErrorRoute,
                        'Errors',
                        'fas fa-exclamation-triangle text-white',
                        'badge-danger'
                    );

                    return '<div class="text-nowrap">' . $logsLink . '<br>' . $errorsLink . '</div>';
                })
                ->addColumn('actions', function ($row) {
                    return $row->action_buttons;
                })
                ->make();
        }
        $title = t('List Import Students File');
        $schools = School::query()->where('active', 1)->get();
        $statuses = [
            'New',
            'Uploading',
            'Completed',
            'Failures',
            'Errors',
        ];
        $process_types = [
            'create' ,
            'update' ,
            'delete' ,
        ];
        $container_type = 'container-fluid';
        return view('manager.student.import.index', compact('title', 'schools', 'statuses', 'container_type', 'process_types'));
    }

    public function create()
    {
        $title = t('Import Students');
        $note = t('The student file format must be one of these formats') . ' : xlsx';
        $schools = School::query()->active()->get();
        $years = Year::query()->get();

        return view('manager.student.import.create', compact('title', 'note', 'schools', 'years'));
    }

//    public function store(ImportStudentRequest $request)
//    {
//        $data = $request->validated();
//
//        $file = $request->file('students_file');
//
//        //upload file
//        $upload_file = uploadNewFile($file, '/student-import-files');
//
//        $update = isset($data['update']);
//        $delete = isset($data['delete']);
//
//        if ($update) {
//            $update = true;
//            $delete = false;
//        } elseif ($delete) {
//            $update = false;
//            $delete = true;
//        } else {
//            $update = false;
//            $delete = false;
//        }
//
//        //save file data
//        $create_file = StudentImportFile::query()->create([
//            'file_name' => $upload_file['name'],
//            'original_file_name' => $file->getClientOriginalName(),
//            'path' => $upload_file['path'],
//            'school_id' => $data['school_id'],
//            'year_id' => $data['year_id'],
//            'status' => 1, //1=>New
//            'update' => $update, //1=>New
//        ]);
//
//        //import students
//        $student_import = new OldStudentImport($create_file, $update,$delete);
//        Excel::import($student_import, public_path($create_file->path));
//
//
//        //update
//        $file_data = [
//            'row_count' => $student_import->getRowsCount(),
//            'updated_row_count' => $student_import->getUpdatedRowsCount(),
//            'deleted_row_count' => $student_import->getDeletedRowsCount(),
//            'failed_row_count' => $student_import->getFailedRowCount(),
//        ];
//
//
//        //1=>New  2=>Uploading  3=>Completed  4=>Failure  5=>Error
//
//        if ($student_import->getError()) {
//            $file_data['status'] = 5; //Error
//            $file_data['error'] = $student_import->getError();
//            StudentImportFile::query()->where('id', $create_file['id'])->update($file_data);
//            return redirect()->route('manager.students_files_import.index')->withErrors([$student_import->getExceptionMessage()]);
//        }
//
//        if ($student_import->getFailures()) {
//            $file_data['status'] = 4; //Failures
//            $file_data['failures'] = json_encode($student_import->getFailures());
//        } else {
//            $file_data['status'] = 3; //Complete
//        }
//
//        StudentImportFile::query()->where('id', $create_file['id'])->update($file_data);
//        return redirect()->route('manager.students_files_import.index');
//
//    }
    public function store(ImportStudentFileRequest $request)
    {
        $data = $request->validated();
        //upload file
        $file = $request->file('file');
        $upload_file = uploadFile($file, '/student-import-files');
        //save file data
        $create_file = StudentImportFile::query()->create([
            'school_id' => $data['school_id'],
            'original_file_name' => $file->getClientOriginalName(),
            'file_name' => $upload_file['name'],
            'path' => $upload_file['path'],
            'status' => 'New',
            'process_type' => $data['process_type'],
            'with_abt_id' => $data['with_abt_id'], //1=>New
            'year_id' => $data['year_id'],
            'data' => [
                'username_type' => $data['username_type'],
                'delete_type' => $data['delete_type'] ?? null,
                'rounds_deleted_assessments' => $data['rounds_deleted_assessments'] ?? [],
                'search_by_column' => $data['search_by_column'] ?? null,
            ]
        ]);
        $student_import = new StudentImport($create_file, $request);
        \Maatwebsite\Excel\Facades\Excel::import($student_import, public_path($create_file->path));

        $file_data = [
            'row_count' => $student_import->getRowsCount(),
            'updated_row_count' => $student_import->getUpdatedRowsCount(),
            'deleted_row_count' => $student_import->getDeletedRowsCount(),
            'failed_row_count' => $student_import->getFailedRowsCount(),
        ];
        if ($student_import->getError()) {
            $file_data['status'] = 'Errors'; //Error
            $file_data['error'] = $student_import->getError();
            $create_file->update($file_data);
            return redirect()->route('manager.students_files_import.index')->withErrors([$file_data['error']]);
        }

        if (StudentImportFileLog::query()->where('student_import_file_id', $create_file->id)->count() > 0) {
            $file_data['status'] = 'Failures'; //Failures
            $file_data['failures'] = $student_import->getFailures();
        } else {
            $file_data['status'] = 'Completed'; //Complete
        }

        $create_file->update($file_data);
        return redirect()->route('manager.students_files_import.index')->with('message', t('Students Successfully imported'))->with('m-class', 'success');
    }

    public function show($id)
    {
        $file = StudentImportFile::query()->findOrFail($id);
        $title = 'Show File Errors';
        return view('manager.student.import.show', compact('file', 'title'));
    }

    public function delete(Request $request)
    {
        $deleteStudents = request('delete_students', false);
        if ($deleteStudents) {
            StudentImportFile::query()->whereIn('id', $request->get('row_id'))->update([
                'delete_with_user' => 1,
            ]);
            Student::query()->whereIn('file_id', $request->get('row_id'))->delete();
        } else {
            Student::query()->whereIn('file_id', $request->get('row_id'))->update(['file_id' => null]);
        }
        StudentImportFile::query()->whereIn('id', $request->get('row_id'))->delete();
        return $this->sendResponse(null, t('Successfully Deleted'));
    }

    public function showError($id)
    {
        $file = StudentImportFile::query()->where('id', $id)->first();
        if ($file->status == 4) {
            $title = t('Failures');
            $failures = json_decode($file->failures);
            return view('manager.student.import.error', compact('title', 'failures'));

        } else if ($file->status == 5) {
            $title = t('Error');
            $error = $file->error;
            return view('manager.student.import.error', compact('title', 'error'));

        }

    }
    public function exportCards($id)
    {
        $students = Student::query()->where('file_id',$id)->orderBy('level_id')->orderBy('grade_name')->get();
        $school = count($students)>0? $students->first()->school: 0;
        $title = $school ? $school->name . ' | ' . t('Students Cards') : t('Students Cards');
        $students= $students->chunk(6);
        return view('general.cards_and_qr', compact('students', 'school', 'title'));
    }
    public function exportExcel(Request $request,$id)
    {
        $request['file_id'] = $id;
        $request['orderBy'] = 'level';
        $request['orderBy2'] = 'section';
        return (new StudentExport($request))->download('Students Information.xlsx');
    }

    //Error Logs
    public function showFromErrors($id)
    {
        if (request()->ajax()) {
            $rows = StudentImportFileLog::query()
                ->filter()
                ->where('student_import_file_id', $id)
                ->latest();
            $levels = Level::get();
            $datatable = DataTables::make($rows)
                ->escapeColumns([])
                ->addColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->toDateString();
                })
                ->addColumn('data', function ($row) use ($levels) {
                    $inputs  = ['Name', 'Student ID','Grade', 'Nationality', 'Grade Name', 'SEN', 'G&T', 'Arab', 'Date Of Birth', 'Gender', 'Citizen', 'Assessment'];
                    $inputs_with_values = [];
                    foreach ($inputs as $input) {
                        $row_input_value = array_filter($row->data['inputs'], function ($item) use ($input) {
                            return $item['key'] === $input;
                        });
                        $row_input_value = collect($row_input_value)->first()['value']??null;
                        $inputs_with_values[$input] = ['id'=>$row->id,'key'=>$input,'value'=>$row_input_value];
                    }
                    return view('manager.student.import.logs.student_data_form', compact('row','inputs_with_values','levels'));
                })
                ->addColumn('errors', function ($row) {
                    $html = '';
                    foreach ($row->data['errors'] as $errors) {
                        $html .= '<ul><li class="text-danger">';
                        if (is_array($errors))
                            $html .= '<ul><li class="text-danger">' . implode('</li><li class="text-danger">', $errors) . '</li></ul>';
                        else
                            $html .= $errors;

                        $html .= '</li></ul>';
                    }
                    return $html;
                })
                ->addColumn('row_number', function ($row) {
                    $num = $row->row_num;
                    return "<span data-num='$num' class='row_num'>" . $num . "</span>";
                });
            return $datatable->make();
        }
        $studentDataFile = StudentImportFile::query()->findOrFail($id);
        $title = t('Show Student Data File');
        $container_type = 'container-fluid';
        return view('manager.student.import.logs.show_errors', compact('title', 'studentDataFile', 'container_type'));
    }

    public function saveLogs(StudentImportLogDataRequest $request)
    {
        $data = $request->validated();
        $student_data_file = StudentImportFile::query()->findOrFail($data['student_data_file_id']);
        $counts = 0;
        $rows_num = [];
        $levels = Level::query()->where('year_id', $student_data_file->year_id)->get();
        foreach ($data['student'] as $std) {
            $student = new Student($std);

            //create username
            $full_name = trim(str_replace('  ', ' ', str_replace(' ', ' ', $std['name'])));
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


            $student->name = $std['name'];
            $student->id_number = $std['student_id'];
            $student->password = bcrypt(123456);
            $student->grade_name = $std['grade_name'];
            $student->arab = $std['arab'];
            $student->sen = $std['sen'];
            $student->citizen = $std['citizen'];
            $student->gender = $std['gender']==1?'boy':'girl';
            $student->dob = $std['date_of_birth'];
            $student->nationality = $std['nationality'];
            if (isset($std['grade']) && !is_null($std['grade']) && $std['grade'] != '' && isset($std['arab']) && $std['arab'] != '') {
                $assessment = $levels->where('grade', $std['grade'])->where('arab', $std['arab'])->first();
                if ($assessment) {
                    $student->level_id = $assessment->id;
                }else{
                    continue;
                }
            }else{
                continue;
            }


            $student->g_t = $std['g&t'];
            $student->email = $username;

            $student->school_id = $student_data_file->school_id;
            $student->year_id = $student_data_file->year_id;
            $student->file_id = $student_data_file->id;

            $student->save();
            $rows_num[] = $std['row_num'];
            $counts++;
        }
        StudentImportFileLog::query()->where('student_import_file_id', $student_data_file->id)->whereIn('row_num', $rows_num)->delete();
        $logs = StudentImportFileLog::query()->where('student_import_file_id', $student_data_file->id)->count();
        $student_data_file->update([
            'status' => $logs ? 'Failures' : 'Completed',
            'row_count' => $student_data_file->row_count + $counts
        ]);
        return redirect()->route('manager.students_files_import.index')->with('message', t('Data Saved Successfully'));

    }

    public function deleteLogs(Request $request)
    {
        $id = $request->get('row_id', []);
        $logs = StudentImportFileLog::query()->whereIn('id', $id)->get();
        $file = $logs->first()->studentImportFile;
        foreach ($logs as $log) {
            $log->delete();
        }
        $logs_count = StudentImportFileLog::query()->where('student_import_file_id', $file->id)->count();
        $file->update([
            'status' => $logs_count > 0 ? 'Failures' : 'Completed',
        ]);
        return $this->sendResponse(null, 'Deleted Successfully');
    }

}
