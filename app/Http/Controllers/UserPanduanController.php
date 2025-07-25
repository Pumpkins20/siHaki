<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class UserPanduanController extends Controller
{
    private function getCategoryColor($category)
    {
        $colors = [
            'umum' => 'primary',
            'pengajuan' => 'success', 
            'dokumen' => 'warning',
            'anggota' => 'info',
            'status' => 'secondary',
            'sertifikat' => 'danger'
        ];
        return $colors[$category] ?? 'primary';
    }

    public function index(Request $request)
    {
        // FAQ Data - bisa dipindah ke database nanti
        $faqs = [
            [
                'id' => 1,
                'category' => 'umum',
                'question' => 'Apa itu HKI (Hak Kekayaan Intelektual)?',
                'answer' => 'HKI adalah hak eksklusif yang diberikan atas suatu kreasi intelektual manusia. HKI melindungi karya-karya seperti penemuan, karya tulis, desain, dan tanda yang digunakan dalam perdagangan.',
                'is_popular' => true
            ],
            [
                'id' => 2,
                'category' => 'umum',
                'question' => 'Mengapa perlu mendaftarkan HKI?',
                'answer' => 'Pendaftaran HKI memberikan perlindungan hukum atas karya Anda, mencegah penggunaan tanpa izin, dan memberikan hak eksklusif untuk mengkomersialkan karya tersebut.',
                'is_popular' => true
            ],
            [
                'id' => 3,
                'category' => 'pengajuan',
                'question' => 'Bagaimana cara mengajukan HKI melalui SiHaki?',
                'answer' => 'Login ke sistem, pilih "Pengajuan HKI", isi formulir lengkap dengan data pencipta (minimal 2 orang), upload dokumen yang diperlukan, lalu submit untuk review.',
                'is_popular' => true
            ],
            [
                'id' => 4,
                'category' => 'pengajuan',
                'question' => 'Berapa lama proses review pengajuan HKI?',
                'answer' => 'Proses review biasanya memakan waktu 7-14 hari kerja. Anda akan mendapat notifikasi melalui email dan sistem jika ada update status pengajuan.',
                'is_popular' => false
            ],
            [
                'id' => 5,
                'category' => 'dokumen',
                'question' => 'Dokumen apa saja yang diperlukan untuk pengajuan HKI?',
                'answer' => 'Dokumen yang diperlukan berbeda tergantung jenis ciptaan: Program Komputer (cover, screenshot, manual, link), Sinematografi (file video), Buku (file PDF), dll. Lihat panduan detail di setiap jenis ciptaan.',
                'is_popular' => true
            ],
            [
                'id' => 6,
                'category' => 'dokumen',
                'question' => 'Berapa ukuran maksimal file yang bisa diupload?',
                'answer' => 'Ukuran maksimal berbeda per jenis: Video/PDF besar (20MB), Gambar (1MB), Dokumen umum (5MB). Pastikan file dalam format yang didukung.',
                'is_popular' => false
            ],
            [
                'id' => 7,
                'category' => 'anggota',
                'question' => 'Berapa minimal anggota pencipta yang diperlukan?',
                'answer' => 'Minimal 2 orang pencipta, maksimal 5 orang. Jika lebih dari 5 orang, silakan hubungi LPPM di hki@amikom.ac.id untuk penanganan khusus.',
                'is_popular' => true
            ],
            [
                'id' => 8,
                'category' => 'anggota',
                'question' => 'Bisakah mengganti data anggota setelah submit?',
                'answer' => 'Data anggota hanya bisa diubah saat status masih Draft atau Revision Needed. Setelah status Under Review, data tidak dapat diubah.',
                'is_popular' => false
            ],
            [
                'id' => 9,
                'category' => 'status',
                'question' => 'Apa arti dari berbagai status pengajuan?',
                'answer' => 'Draft (belum submit), Submitted (menunggu review), Under Review (sedang direview), Revision Needed (perlu perbaikan), Approved (disetujui), Rejected (ditolak).',
                'is_popular' => true
            ],
            [
                'id' => 10,
                'category' => 'status',
                'question' => 'Bagaimana jika pengajuan ditolak atau perlu revisi?',
                'answer' => 'Jika ditolak/perlu revisi, Anda akan menerima catatan dari reviewer. Perbaiki sesuai saran, lalu submit ulang. Proses ini bisa diulang hingga pengajuan disetujui.',
                'is_popular' => false
            ],
            [
                'id' => 11,
                'category' => 'sertifikat',
                'question' => 'Kapan sertifikat HKI bisa didownload?',
                'answer' => 'Sertifikat dapat didownload setelah pengajuan berstatus Approved. Anda akan mendapat notifikasi via email dan bisa download melalui halaman riwayat pengajuan.',
                'is_popular' => true
            ],
            [
                'id' => 12,
                'category' => 'sertifikat',
                'question' => 'Apakah sertifikat yang diterbitkan memiliki kekuatan hukum?',
                'answer' => 'Ya, sertifikat yang diterbitkan melalui sistem ini memiliki kekuatan hukum dan diakui secara resmi sebagai bukti kepemilikan HKI.',
                'is_popular' => false
            ]
        ];

        // Filter FAQ berdasarkan kategori
        $category = $request->get('category', 'all');
        $search = $request->get('search', '');

        $filteredFaqs = collect($faqs);

        if ($category !== 'all') {
            $filteredFaqs = $filteredFaqs->filter(function($faq) use ($category) {
                return $faq['category'] === $category;
            });
        }

        if ($search) {
            $filteredFaqs = $filteredFaqs->filter(function($faq) use ($search) {
                return stripos($faq['question'], $search) !== false || 
                       stripos($faq['answer'], $search) !== false;
            });
        }

        // Kategorikan FAQ
        $categories = [
            'umum' => 'Umum',
            'pengajuan' => 'Pengajuan',
            'dokumen' => 'Dokumen',
            'anggota' => 'Anggota Pencipta',
            'status' => 'Status & Review',
            'sertifikat' => 'Sertifikat'
        ];

        // FAQ populer
        $popularFaqs = collect($faqs)->where('is_popular', true)->take(6);

        // Panduan download
        $guides = [
            [
                'title' => 'Panduan Lengkap Pengajuan HKI',
                'description' => 'Panduan step-by-step untuk mengajukan HKI melalui SiHaki',
                'file' => 'panduan-pengajuan-hki.pdf',
                'size' => '2.5 MB',
                'pages' => 15,
                'updated' => '2024-01-15'
            ],
           /* [
                'title' => 'Template Dokumen HKI',
                'description' => 'Template dan contoh dokumen yang diperlukan untuk pengajuan',
                'file' => 'template-dokumen-hki.pdf',
                'size' => '1.8 MB',
                'pages' => 10,
                'updated' => '2024-01-10'
            ],
            [
                'title' => 'FAQ Lengkap SiHaki',
                'description' => 'Kumpulan pertanyaan yang sering diajukan beserta jawabannya',
                'file' => 'faq-sihaki.pdf',
                'size' => '1.2 MB',
                'pages' => 8,
                'updated' => '2024-01-20'
            ] */
        ];

        return view('user.panduan.index', compact(
            'filteredFaqs', 
            'categories', 
            'popularFaqs', 
            'guides', 
            'category', 
            'search'
        ));
    }

    public function downloadGuide($filename)
    {
        $allowedFiles = [
            'panduan-pengajuan-hki.pdf',
            'template-dokumen-hki.pdf',
            'faq-sihaki.pdf'
        ];

        if (!in_array($filename, $allowedFiles)) {
            abort(404, 'File tidak ditemukan');
        }

        $filePath = storage_path('app/public/guides/' . $filename);
        
        if (!file_exists($filePath)) {
            // Jika file tidak ada, buat file dummy untuk demo
            $this->createDummyGuide($filename);
        }

        return Response::download($filePath, $filename);
    }

    private function createDummyGuide($filename)
    {
        $guidesPath = storage_path('app/public/guides');
        
        if (!file_exists($guidesPath)) {
            mkdir($guidesPath, 0755, true);
        }

        $filePath = $guidesPath . '/' . $filename;
        
        // Buat PDF dummy sederhana
        $content = "%PDF-1.4\n1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\n2 0 obj\n<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>\nendobj\n3 0 obj\n<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 612 792]\n/Contents 4 0 R\n>>\nendobj\n4 0 obj\n<<\n/Length 44\n>>\nstream\nBT\n/F1 12 Tf\n72 720 Td\n(Panduan SiHaki - " . $filename . ") Tj\nET\nendstream\nendobj\nxref\n0 5\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \n0000000209 00000 n \ntrailer\n<<\n/Size 5\n/Root 1 0 R\n>>\nstartxref\n317\n%%EOF";
        
        file_put_contents($filePath, $content);
    }

    public function exportFaq(Request $request)
    {
        // Export FAQ ke PDF (implementasi sederhana)
        $category = $request->get('category', 'all');
        $filename = 'faq-sihaki-' . ($category !== 'all' ? $category : 'semua') . '-' . date('Y-m-d') . '.pdf';
        
        // Untuk demo, redirect ke download file yang sudah ada
        return $this->downloadGuide('faq-sihaki.pdf');
    }

    // Method untuk mendapatkan warna kategori dari view
    public function getCategoryColorForView($category)
    {
        return $this->getCategoryColor($category);
    }
}
