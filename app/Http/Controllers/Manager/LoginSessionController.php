<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use App\Models\LoginSession;
use App\Models\Manager;
use App\Models\School;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LoginSessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show login sessions')->only('index');
    }

    public function index(Request $request)
    {
        $title = t('Login Session');

        if (request()->ajax()) {
            $sessions = LoginSession::query()->with(['model'])->filter($request)->latest();

            return DataTables::make($sessions)
                ->escapeColumns([])
                ->addColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d/m/Y h:i A');
                })
                ->addColumn('user', function ($row) {
                    return '<div class="d-flex flex-column" style="min-width: 280px">'.
                            '<div><span class="fw-bold">'.t('ID').': </span>'.$row->model->id.'</div>'.
                            '<div><span class="fw-bold">'.t('Name').': </span>'.$row->model->name.'</div>'.
                            '<div><span class="fw-bold">'.t('Email').': </span>'.$row->model->email.'</div>'.
                            '</div>';
                })->addColumn('model_type', function ($row) {
                    $type = null;
                    if ($row->model_type == Manager::class){
                        $type = '<span class="badge badge-secondary">'.t('Manager').'</span>';
                    }elseif ($row->model_type == School::class){
                        $type = '<span class="badge badge-secondary">'.t('School').'</span>';
                    }elseif ($row->model_type == Student::class){
                        $type = '<span class="badge badge-secondary">'.t('Student').'</span>';
                    }elseif ($row->model_type == Inspection::class){
                        $type = '<span class="badge badge-secondary">'.t('Inspection').'</span>';
                    }
                    return $type;
                })
                ->make();
        }

        return view('manager.login_sessions.index', compact('title'));
    }
}
