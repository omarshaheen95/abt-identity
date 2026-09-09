<?php

namespace App\Exports;

use App\Services\LoginActivityService;
use App\Models\Manager;
use App\Models\School;
use App\Models\Student;
use App\Models\Inspection;
use App\Models\LoginSession;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;

class LoginSessionExport implements WithMapping, Responsable, WithHeadings, FromQuery, WithEvents, ShouldAutoSize
{
    use Exportable;

    public $request;
    public $last_cell;
    public $last_row;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function headings(): array
    {
        $headers = [
            'ID',
            'Type',
            'Guard',
            'User ID',
            'Name',
            'Email',
            'Entered Identifier',
            'Status',
            'Reason',
            'IP Address',
            'Browser Info',
            'Data',
            'Time',
        ];

        return array_map(function ($header) {
            return __('auth_log.'.$header);
        }, $headers);
    }

    /**
     * Chunked by FromQuery so a large log does not have to be held in memory.
     */
    public function query()
    {
        return LoginSession::query()->with(['model'])->filter($this->request)->latest('id');
    }

    public function map($row): array
    {
        return [
            $row->id,
            $this->typeLabel($row->model_type),
            $row->guard,
            $row->model_id,
            $row->account_name,
            $row->account_identifier,
            $row->identifier,
            $this->statusLabel($row->status),
            $this->reasonLabel($row->reason),
            $row->ip,
            $row->user_agent,
            $row->data,
            $row->created_at ? Carbon::parse($row->created_at)->format('d/m/Y h:i A') : '',
        ];
    }

    protected function typeLabel(?string $type): string
    {
        $types = [
                        Manager::class => __('auth_log.Manager'),
                        School::class => __('auth_log.School'),
                        Student::class => __('auth_log.Student'),
                        Inspection::class => __('auth_log.Inspection'),
                    ];

        return $types[$type] ?? __('auth_log.Unknown');
    }

    protected function statusLabel(?string $status): string
    {
        $statuses = [
            LoginActivityService::STATUS_SUCCESS => __('auth_log.Success'),
            LoginActivityService::STATUS_FAILED => __('auth_log.Failed'),
            LoginActivityService::STATUS_LOCKOUT => __('auth_log.Lockout'),
            LoginActivityService::STATUS_LOGOUT => __('auth_log.Logout'),
        ];

        return $statuses[$status] ?? (string) $status;
    }

    protected function reasonLabel(?string $reason): string
    {
        $reasons = [
            LoginActivityService::REASON_INVALID_CREDENTIALS => __('auth_log.Invalid credentials'),
            LoginActivityService::REASON_USER_NOT_FOUND => __('auth_log.Student not found'),
            LoginActivityService::REASON_THROTTLED => __('auth_log.Too many attempts'),
            'account_disabled' => __('auth_log.Account disabled'),
            'account_archived' => __('auth_log.Account archived'),
            'school_suspended' => __('auth_log.School suspended'),
        ];

        return $reasons[$reason] ?? (string) $reason;
    }

    public function registerEvents(): array
    {
        Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
            if (app()->getLocale() == 'ar') {
                $sheet->setRightToLeft(true);
            }
        });

        return [
            AfterSheet::class => function (AfterSheet $event) {
                $this->last_cell = $event->sheet->getHighestColumn();
                $this->last_row = $event->sheet->getHighestRow();
                $cellRange = 'A1:'.$this->last_cell.'1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold('bold')->setSize(12);
                $event->sheet->styleCells(
                    "A1:$this->last_cell$this->last_row",
                    [
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                    ]
                );
            },
        ];
    }
}
