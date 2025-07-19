<?php

namespace App\Exports;

use App\Models\HkiSubmission;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SubmissionsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $filters;
    protected $rowCount = 0;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Query untuk data yang akan diexport
     */
    public function query()
    {
        $query = HkiSubmission::with(['user.department', 'reviewer']);
        
        // Apply filters berdasarkan request
        if (isset($this->filters['status']) && !empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        
        if (isset($this->filters['type']) && !empty($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
        }
        
        if (isset($this->filters['creation_type']) && !empty($this->filters['creation_type'])) {
            $query->where('creation_type', $this->filters['creation_type']);
        }
        
        if (isset($this->filters['assignment']) && !empty($this->filters['assignment'])) {
            if ($this->filters['assignment'] === 'unassigned') {
                $query->whereNull('reviewer_id');
            } elseif ($this->filters['assignment'] === 'my_reviews') {
                $query->where('reviewer_id', auth()->id());
            }
        }

        if (isset($this->filters['search']) && !empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('nama', 'like', "%{$search}%")
                               ->orWhere('nidn', 'like', "%{$search}%");
                  });
            });
        }
        
        // Untuk bulk export dengan IDs spesifik
        if (isset($this->filters['ids']) && is_array($this->filters['ids'])) {
            $query->whereIn('id', $this->filters['ids']);
        }
        
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Header kolom
     */
    public function headings(): array
    {
        return [
            'ID Submission',
            'Judul HKI',
            'Jenis HKI',
            'Jenis Ciptaan',
            'Status',
            'Tanggal Publikasi Pertama', // ✅ Add this
            'Nama User',
            'NIDN',
            'Email',
            'Program Studi',
            'Departemen',
            'Jumlah Anggota',
            'Tanggal Submit',
            'Reviewer',
            'Tanggal Review',
            'Catatan Review',
            'Dibuat Pada'
        ];
    }

    /**
     * Mapping data untuk setiap row
     */
    public function map($submission): array
    {
        $this->rowCount++;
        
        return [
            str_pad($submission->id, 4, '0', STR_PAD_LEFT),
            $submission->title,
            $this->formatType($submission->type),
            $this->formatCreationType($submission->creation_type),
            $this->formatStatus($submission->status),
            $submission->first_publication_date ? $submission->first_publication_date->format('d/m/Y') : '-', // ✅ Add this
            $submission->user->nama,
            $submission->user->nidn,
            $submission->user->email,
            $submission->user->program_studi,
            $submission->user->department->name ?? 'Tidak ada',
            $submission->member_count,
            $submission->submission_date ? $submission->submission_date->format('d/m/Y H:i') : '-',
            $submission->reviewer ? $submission->reviewer->nama : 'Belum di-assign',
            $submission->reviewed_at ? $submission->reviewed_at->format('d/m/Y H:i') : '-',
            $submission->review_notes ?? '-',
            $submission->created_at->format('d/m/Y H:i')
        ];
    }

    /**
     * Styling untuk Excel
     */
    public function styles(Worksheet $sheet)
    {
        $highestRow = $this->rowCount + 1; // +1 untuk header
        $highestColumn = 'P'; // Kolom terakhir (ke-16)
        
        return [
            // Style untuk header (row 1)
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],

            // Style untuk semua data
            "A1:{$highestColumn}{$highestRow}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ]
            ],
            
            // Style untuk data rows (zebra striping)
            "A2:{$highestColumn}{$highestRow}" => [
                'font' => [
                    'size' => 10
                ]
            ]
        ];
    }

    /**
     * Lebar kolom
     */
    public function columnWidths(): array
    {
        return [
            'A' => 12, // ID
            'B' => 35, // Judul
            'C' => 15, // Jenis HKI
            'D' => 20, // Jenis Ciptaan
            'E' => 15, // Status
            'F' => 25, // Nama User
            'G' => 15, // NIDN
            'H' => 25, // Email
            'I' => 20, // Program Studi
            'J' => 20, // Departemen
            'K' => 12, // Jumlah Anggota
            'L' => 18, // Tanggal Submit
            'M' => 20, // Reviewer
            'N' => 18, // Tanggal Review
            'O' => 40, // Catatan Review
            'P' => 18  // Dibuat Pada
        ];
    }

    /**
     * Title untuk worksheet
     */
    public function title(): string
    {
        return 'Data Submission HKI';
    }

    /**
     * Format jenis HKI
     */
    private function formatType($type): string
    {
        return match($type) {
            'copyright' => 'Hak Cipta',
            'patent' => 'Paten',
            default => ucfirst($type)
        };
    }

    /**
     * Format jenis ciptaan
     */
    private function formatCreationType($creationType): string
    {
        return match($creationType) {
            'program_komputer' => 'Program Komputer',
            'sinematografi' => 'Sinematografi',
            'buku' => 'Buku',
            'poster_fotografi' => 'Poster/Fotografi/Seni Gambar',
            'alat_peraga' => 'Alat Peraga',
            'basis_data' => 'Basis Data',
            default => ucfirst(str_replace('_', ' ', $creationType))
        };
    }

    /**
     * Format status
     */
    private function formatStatus($status): string
    {
        return match($status) {
            'submitted' => 'Submitted',
            'under_review' => 'Under Review',
            'revision_needed' => 'Revision Needed',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'draft' => 'Draft',
            default => ucfirst(str_replace('_', ' ', $status))
        };
    }
}