<?php

namespace App\Exports;

use App\Models\HkiSubmission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SubmissionsSummaryExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return HkiSubmission::selectRaw('
                status,
                creation_type,
                COUNT(*) as total,
                AVG(DATEDIFF(reviewed_at, submission_date)) as avg_review_days
            ')
            ->whereNotNull('submission_date')
            ->groupBy('status', 'creation_type')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Status',
            'Jenis Ciptaan', 
            'Total Submission',
            'Rata-rata Hari Review'
        ];
    }

    public function map($row): array
    {
        return [
            ucfirst(str_replace('_', ' ', $row->status)),
            ucfirst(str_replace('_', ' ', $row->creation_type)),
            $row->total,
            $row->avg_review_days ? round($row->avg_review_days, 1) . ' hari' : '-'
        ];
    }
}
