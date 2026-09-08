<?php

namespace App\Http\Controllers\Manager;

use App\Exports\LoginSessionExport;
use App\Http\Controllers\Controller;
use App\Services\LoginActivityService;
use App\Models\Manager;
use App\Models\School;
use App\Models\Student;
use App\Models\Inspection;
use App\Models\LoginSession;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LoginSessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show login sessions')->only('index');
        $this->middleware('permission:export login sessions')->only('export');
    }

    public function index(Request $request)
    {
        $title = __('auth_log.Login Session');

        if (request()->ajax()) {
            $sessions = LoginSession::query()->with(['model'])->filter($request)->latest();

            return DataTables::make($sessions)
                ->escapeColumns([])
                ->addColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d/m/Y h:i A');
                })
                ->addColumn('user', function ($row) {
                    // A failed attempt against an unknown account has no model,
                    // only the identifier that was typed in the login form.
                    if (! $row->model) {
                        return '<div class="d-flex flex-column" style="min-width: 280px">'.
                            '<div><span class="fw-bold">'.__('auth_log.Entered Identifier').': </span>'.e($row->identifier ?: '-').'</div>'.
                            '<div><span class="text-muted">'.__('auth_log.No matching account').'</span></div>'.
                            '</div>';
                    }

                    return '<div class="d-flex flex-column" style="min-width: 280px">'.
                        '<div><span class="fw-bold">'.__('auth_log.ID').': </span>'.$row->model->id.'</div>'.
                        '<div><span class="fw-bold">'.__('auth_log.Name').': </span>'.e($row->account_name ?: '-').'</div>'.
                        '<div><span class="fw-bold">'.__('auth_log.Email').': </span>'.e($row->account_identifier ?: '-').'</div>'.
                        '</div>';
                })
                ->addColumn('model_type', function ($row) {
                    $types = [
                        Manager::class => __('auth_log.Manager'),
                        School::class => __('auth_log.School'),
                        Student::class => __('auth_log.Student'),
                        Inspection::class => __('auth_log.Inspection'),
                    ];

                    $label = $types[$row->model_type] ?? __('auth_log.Unknown');
                    $guard = $row->guard ? '<div class="text-muted fs-8">'.e($row->guard).'</div>' : '';

                    return '<span class="badge badge-secondary">'.$label.'</span>'.$guard;
                })
                ->addColumn('status', function ($row) {
                    $badges = [
                        LoginActivityService::STATUS_SUCCESS => ['badge-light-success', __('auth_log.Success')],
                        LoginActivityService::STATUS_FAILED => ['badge-light-danger', __('auth_log.Failed')],
                        LoginActivityService::STATUS_LOCKOUT => ['badge-light-warning', __('auth_log.Lockout')],
                        LoginActivityService::STATUS_LOGOUT => ['badge-light-info', __('auth_log.Logout')],
                    ];

                    [$class, $label] = $badges[$row->status] ?? ['badge-light-secondary', $row->status];

                    $reasons = [
                        LoginActivityService::REASON_INVALID_CREDENTIALS => __('auth_log.Invalid credentials'),
                        LoginActivityService::REASON_USER_NOT_FOUND => __('auth_log.Student not found'),
                        LoginActivityService::REASON_THROTTLED => __('auth_log.Too many attempts'),
                        'account_disabled' => __('auth_log.Account disabled'),
                        'account_archived' => __('auth_log.Account archived'),
                        'school_suspended' => __('auth_log.School suspended'),
                    ];

                    $reason = $row->reason
                        ? '<div class="text-muted fs-8">'.($reasons[$row->reason] ?? e($row->reason)).'</div>'
                        : '';

                    return '<span class="badge '.$class.'">'.e($label).'</span>'.$reason;
                })
                ->addColumn('ip', function ($row) {
                    $agent = $row->user_agent
                        ? '<div class="text-muted fs-8 text-truncate" style="max-width: 260px" title="'.e($row->user_agent).'">'.e($row->user_agent).'</div>'
                        : '';

                    return '<div style="min-width: 160px">'.e($row->ip ?: '-').$agent.'</div>';
                })
                ->addColumn('data', function ($row) {
                    return e($row->data);
                })
                ->make();
        }

        return view('manager.login_sessions.index', compact('title'));
    }

    public function export(Request $request)
    {
        return (new LoginSessionExport($request))->download('Login Sessions.xlsx');
    }
}
