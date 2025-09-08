<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StandardsStudentsExport implements WithMapping, ShouldAutoSize, Responsable, WithHeadings, FromArray, WithEvents, WithDefaultStyles, WithStyles
{
    use Exportable;

    public $length;
    public $school;
    public $students;
    public $students_terms;
    public $standards;
    public $standards_marks;
    public $header;
    public $subjects;
    public $req;
    public $last_cell;
    public $last_row;

    // Constants for improved readability and performance
    const COLOR_RED = 'FF0000';
    const COLOR_GREEN = '00FF00';
    const COLOR_ORANGE = 'FFC107';
    const COLOR_BLUE = '0000FF';
    const ALIGNMENT_CENTER = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;

    public function __construct(Request $request,
                                        $school,
                                        $students,
                                        $students_terms,
                                        $standards,
                                        $standards_marks, $subjects)
    {
        $this->req = $request;
        $this->school = $school;
        $this->students = $students;
        $this->standards = $standards;
        $this->students_terms = $students_terms;
        $this->standards_marks = $standards_marks;
        $this->header = 'AP';
        $this->subjects = $subjects;

        // Record memory usage for debugging
        Log::info('Excel export memory usage: ' . $this->formatBytes(memory_get_usage(true)));
    }

    /**
     * Format bytes to human-readable format
     */
    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function headings(): array
    {
        $headers = [
            re('Student'),
            re('School'),
            re('Grade Name'),
            re('Nationality'),
        ];

        // Add standards to headers
        foreach ($this->standards as $standard) {
            $headers[] = $standard;
        }

        return $headers;
    }

    public function map($row): array
    {
        return $row;
    }

    public function array(): array
    {
        // Create array with question marks and standards
        $data[] = ['Question Mark'] + $this->standards_marks;

        // Add student data
        foreach ($this->students as $student) {
            $data[] = [$student];
        }

        return $data;
    }

    public function registerEvents(): array
    {
        Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                'font' => [
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ]);

            if (app()->getLocale() == 'ar') {
                $sheet->setRightToLeft(true);
            }
        });

        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Start timing for performance monitoring
                $startTime = microtime(true);

                $this->last_cell = $event->sheet->getHighestColumn();
                $this->last_row = $event->sheet->getHighestRow();

                // Apply styles in batches rather than individual cells when possible
                $this->applyStyles($event);

                // Apply conditional formatting
                $this->applyConditionalFormatting($event);

                // Log styling performance
                $duration = microtime(true) - $startTime;
                Log::info("Excel styling completed in {$duration} seconds");
            },
        ];
    }

    /**
     * Apply styles to the sheet in batches
     */
    private function applyStyles(AfterSheet $event)
    {
        // Style student names column
        $event->sheet->styleCells(
            "A1:A$this->last_row",
            [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                'font' => [
                    'name' => 'Century Gothic',
                    'color' => ['argb' => '000000'],
                ],
            ]
        );

        // Style the last row
        $event->sheet->styleCells(
            "A".$this->last_row.":".$this->last_cell.$this->last_row,
            [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                'font' => [
                    'name' => 'Century Gothic',
                    'color' => ['argb' => self::COLOR_RED],
                ],
            ]
        );

        // Style row 2
        $event->sheet->styleCells(
            "A2:".$this->last_cell."2",
            [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                'font' => [
                    'name' => 'Century Gothic',
                    'color' => ['argb' => self::COLOR_RED],
                ],
            ]
        );

        // Style column B
        $event->sheet->styleCells(
            "B2:B$this->last_row",
            [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                'font' => [
                    'name' => 'Century Gothic',
                    'color' => ['argb' => self::COLOR_RED],
                    'bold' => true,
                    'size' => 12,
                ],
            ]
        );
    }

    /**
     * Apply conditional formatting to the sheet
     */
    private function applyConditionalFormatting(AfterSheet $event)
    {
        $redStyle = new Style(false, true);
        $redStyle->getFont()->setColor(new Color(Color::COLOR_RED));

        $greenStyle = new Style(false, true);
        $greenStyle->getFont()->setColor(new Color(Color::COLOR_GREEN));

        $orangeStyle = new Style(false, true);
        $orangeStyle->getFont()->setColor(new Color("FFC107"));

        $blueStyle = new Style(false, true);
        $blueStyle->getFont()->setColor(new Color(Color::COLOR_BLUE));

        $conditionalStyles = [];
        $wizardFactory = new Wizard("A1:$this->last_cell"."$this->last_row");
        /** @var Wizard\TextValue $textWizard */
        $textWizard = $wizardFactory->newRule(Wizard::TEXT_VALUE);

        // Define text patterns and their styles
        $patterns = [
            "September Round" => $greenStyle,
            "February Round" => $greenStyle,
            "May Round" => $greenStyle,
            "Total" => $orangeStyle,
            "Question Mark" => $blueStyle,
            "Average Standard Benchmark" => $blueStyle
        ];

        foreach ($this->subjects as $subject) {
            $patterns[$subject->name] = $blueStyle;
        }

        // Apply all patterns in a single loop
        foreach ($patterns as $pattern => $style) {
            /** @var Wizard\Expression $expressionWizard */
            $expressionWizard = $wizardFactory->newRule(Wizard::EXPRESSION);
            $expressionWizard->expression('A1="'.$pattern.'"')->setStyle($style);
            $conditionalStyles[] = $expressionWizard->getConditional();
        }

        $event->sheet
            ->getStyle($textWizard->getCellRange())
            ->setConditionalStyles($conditionalStyles);
    }

    public function drawings()
    {
        return new Drawing();
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text with background
            1 => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'D9D9D9']
                ],
                'font' => ['bold' => true]
            ],
        ];
    }
}
