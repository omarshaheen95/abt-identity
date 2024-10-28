<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Http\Controllers\Manager;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\ImportQuestionFileRequest;
use App\Imports\QuestionsImport;
use App\Models\QuestionFile;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class QuestionFileController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:show imported questions')->only('index');
        $this->middleware('permission:import questions')->only(['create', 'store']);
        $this->middleware('permission:edit imported questions')->only(['show']);
        $this->middleware('permission:delete imported questions')->only('destroy');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $rows = QuestionFile::query()->filter()->with(['term','level', 'author'])->latest();
            return DataTables::make($rows)
                ->escapeColumns([])
                ->addColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('Y-m-d H:i');
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 'Completed') {
                        return '<a><span class="badge badge-success">' . t($row->status) . '</span></a>';
                    } else {
                        return '<a><span class="badge badge-info">' . t($row->status) . '</span></a>';
                    }
                })
                ->addColumn('original_file_name', function ($row) {
                    return '<a href="' . route(getGuard().'.question-file.download_file', $row->id) . '" target="_blank"><span class="font-weight-bold">' . $row->original_file_name . '</span></a>';
                })
                ->addColumn('author', function ($row) {
                    return '<span class="badge badge-info">' . t('Type') . '</span> : '. class_basename($row->author->getMorphClass()) . '<br>' . '<span class="badge badge-info">' . t('Name') . '</span> : '. $row->author->name;
                })
                ->addColumn('info', function ($row) {
                    $html = '<div class="d-flex flex-column gap-1">';
                    $html .=  '<div><span class="badge badge-info">' . t('Assessment') . '</span> : '. $row->term->name.'</div>';
                    $html .= '<div><span class="badge badge-info">' . t('Year') . '</span> : '. $row->level->year->name.'</div>';
                    $html .= '<div><span class="badge badge-info">' . t('Level') . '</span> : '. $row->level->name.'</div>';
                    $html .= '<div class="mb-1"><span >' . t('File') . ':</span> ' . '<span class="link link-dark"><a href="' . route(getGuard().'.question-file.download_file', $row->id) . '" target="_blank"><span class="font-weight-bold">' . $row->original_file_name . '</span> <i class="bi bi-download text-dark fs-3"></i> </a></span></div>';
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('rows', function ($row) {
                    $html = '<div class="d-flex flex-column">';
                    $html .= '<div class="mb-1"><span >' . t('Created') . ':</span> ' . '<span class="badge badge-success">' . $row->created_rows_count . '</span></div>';
                    $html .= '<div class="mb-1"><span >' . t('Failed') . ':</span> ' . '<span class="badge badge-danger">' . $row->failed_rows_count . '</span></div>';
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('actions', function ($row) {
                    return $row->action_buttons;
                })
                ->make();
        }
        $title = t("Import questions");
        $container_type = 'container-fluid';

        return view('manager.import-question.index', compact('title', 'container_type'));
    }

    public function create()
    {
        $title = t('Import Questions');
        $years = Year::all();
        return view('manager.import-question.edit', compact('title','years'));
    }

    public function store(ImportQuestionFileRequest $request)
    {
        $data = $request->validated();
        $data['author_type'] = auth()->guard(getGuard())->user()->getMorphClass();
        $data['author_id'] = auth()->guard(getGuard())->id();
        $file = $request->file('file');
        //upload file
        $upload_file = uploadFile($file, '/questions-files-imported');
        //save file data
        $create_file = QuestionFile::query()->create([
            'author_type' => $data['author_type'],
            'author_id' => $data['author_id'],
            'original_file_name' => $file->getClientOriginalName(),
            'file_name' => $upload_file['new_name'],
            'file_path' => $upload_file['path'],
            'status' => 'New',
            'process_type' => 'Create',
            'term_id' => $data['term_id'],
            'level_id' => $data['level_id'],
        ]);

        //import students
        $student_import = new QuestionsImport($request, $create_file);
        \Maatwebsite\Excel\Facades\Excel::import($student_import, public_path($create_file->file_path));

        $file_data = [
            'created_rows_count' => $student_import->getRowsCount(),
            'updated_rows_count' => 0,
            'deleted_rows_count' => 0,
            'failed_rows_count' => $student_import->getFailedRowCount(),
            'failures' => $student_import->getFailures(),
        ];
        if ($student_import->getError()) {
            $file_data['status'] = 'Failed';
            $file_data['error'] = $student_import->getError();
            $create_file->update($file_data);
            return redirect()->route(getGuard() . '.question-file.index')->withErrors([$student_import->getExceptionMessage()]);
        }

        if ($student_import->getFailures()) {
            $file_data['status'] = 'Failed'; //Failures
        } else {
            $file_data['status'] = 'Completed'; //Complete
        }

        $create_file->update($file_data);
        return redirect()->route(getGuard() . '.question-file.index')->with('message', t('File Imported Successfully'));
    }

    public function show(QuestionFile $questionFile): \Illuminate\View\View
    {
        $title = t('Show File Errors');
        return view('manager.import-question.show', compact('title', 'questionFile'));
    }
    public function destroy(Request $request)
    {
        $request->validate(['row_id' => 'required']);
        $rows = QuestionFile::query()->whereIn('id', $request->get('row_id'))->get();
        foreach ($rows as $row) {
            if ($request->get('delete_with_rows', 0))
            {
                $row->questions()->delete();
            }else{
                $row->questions()->update(['question_file_id' => null]);
            }
            $row->delete();
        }
        return Response::respondSuccess(t('Deleted Successfully'));
    }

    public function downloadFile($id): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $file = QuestionFile::query()->findOrFail($id);
        return response()->download(public_path($file->file_path), $file->original_file_name);
    }
}
