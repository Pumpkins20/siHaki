<?php
// filepath: app/Exports/ReviewHistoryExport.php

namespace App\Exports;

use App\Models\HkiSubmission;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReviewHistoryExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    use Exportable;

    protected $filters;
    protected $rowCount = 0;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = HkiSubmission::with(['user', 'reviewer'])
            ->whereNotNull('reviewed_at')
            ->whereIn('status', ['approved', 'rejected']);

        // Apply filters
        if (isset($this->filters['reviewer_id']) && !empty($this->filters['reviewer_id'])) {
            $query->where('reviewer_id', $this->filters['reviewer_id']);
        }

        if (isset($this->filters['status']) && !empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (isset($this->filters['date_from']) && !empty($this->filters['date_from'])) {
            $query->whereDate('reviewed_at', '>=', $this->filters['date_from']);
        }
        
        if (isset($this->filters['date_to']) && !empty($this->filters['date_to'])) {
            $query->whereDate('reviewed_at', '<=', $this->filters['date_to']);
        }

        if (isset($this->filters['search']) && !empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('nama', 'like', "%{$search}%");
                  });
            });
        }
        
        return $query->orderBy('reviewed_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID Submission',
            'Judul HKI',
            'Jenis Ciptaan',
            'Status Review',
            'Nama User',
            'NIDN',
            'Email User',
            'Program Studi',
            'Nama Reviewer',
            'Tanggal Submit',
            'Tanggal Review',
            'Catatan Review',
        ];
    }

    public function map($submission): array
    {
        $this->rowCount++;
        
        return [
            str_pad($submission->id, 4, '0', STR_PAD_LEFT),
            $submission->title,
            ucfirst(str_replace('_', ' ', $submission->creation_type)),
            $submission->status === 'approved' ? 'Approved' : 'Rejected',
            $submission->user->nama,
            $submission->user->nidn,
            $submission->user->email,
            $submission->user->program_studi,
            $submission->reviewer->nama ?? 'Unknown',
            $submission->submission_date ? $submission->submission_date->format('d/m/Y H:i') : '-',
            $submission->reviewed_at ? $submission->reviewed_at->format('d/m/Y H:i') : '-',
            $submission->review_notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A1:L1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4472C4']
                ],
                'font' => ['color' => ['argb' => 'FFFFFFFF']]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // ID
            'B' => 40, // Judul
            'C' => 20, // Jenis Ciptaan
            'D' => 15, // Status
            'E' => 25, // Nama User
            'F' => 15, // NIDN
            'G' => 25, // Email
            'H' => 20, // Program Studi
            'I' => 20, // Reviewer
            'J' => 18, // Tanggal Submit
            'K' => 18, // Tanggal Review
            'L' => 40, // Catatan Review
        ];
    }
}