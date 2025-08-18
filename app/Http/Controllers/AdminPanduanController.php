<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class AdminPanduanController extends Controller
{
    public function index(Request $request)
    {
        // FAQ Data - same as UserPanduanController but for admin context
        $faqs = [
            [
                'id' => 1,
                'category' => 'umum',
                'question' => 'Apa itu HKI (Hak Kekayaan Intelektual)?',
                'answer' => 'HKI adalah hak eksklusif yang diberikan atas karya intelektual dalam bidang teknologi, seni, dan sastra. Meliputi hak cipta, paten, merek, desain industri, dll.',
                'is_popular' => true
            ],
            [
                'id' => 2,
                'category' => 'umum', 
                'question' => 'Mengapa perlu mendaftarkan HKI?',
                'answer' => 'Mendaftarkan HKI memberikan perlindungan hukum, hak eksklusif untuk menggunakan karya, dan dapat menjadi aset komersial yang bernilai.',
                'is_popular' => true
            ],
            [
                'id' => 3,
                'category' => 'review',
                'question' => 'Bagaimana cara meninjau pengajuan HKI?',
                'answer' => 'Login sebagai admin, masuk ke menu "Tinjau Pengajuan", pilih submission dengan status "Submitted", review dokumen, berikan feedback, dan ubah status menjadi Approved/Rejected/Revision Needed.',
                'is_popular' => true
            ],
            [
                'id' => 4,
                'category' => 'review',
                'question' => 'Berapa lama batas waktu review pengajuan?',
                'answer' => 'Idealnya pengajuan direview dalam 7-14 hari kerja. Sistem akan mengirim reminder jika ada pengajuan yang terlalu lama pending.',
                'is_popular' => false
            ],
            [
                'id' => 5,
                'category' => 'dokumen',
                'question' => 'Dokumen apa saja yang perlu diperiksa saat review?',
                'answer' => 'Periksa kelengkapan dokumen sesuai jenis ciptaan: cover letter, screenshot/file utama, manual penggunaan, kelengkapan data anggota, dan validitas KTP yang diupload.',
                'is_popular' => true
            ],
            [
                'id' => 6,
                'category' => 'sertifikat',
                'question' => 'Bagaimana cara mengirim sertifikat ke user?',
                'answer' => 'Setelah pengajuan di-approve, masuk ke menu "Kirim Sertifikat", pilih submission yang sudah approved, generate sertifikat, dan kirim melalui sistem.',
                'is_popular' => true
            ],
            [
                'id' => 7,
                'category' => 'users',
                'question' => 'Bagaimana cara mengelola user dosen?',
                'answer' => 'Masuk ke menu "Kelola Pengguna", disana bisa menambah user baru, edit informasi user, reset password, atau menonaktifkan akun user.',
                'is_popular' => false
            ],
            [
                'id' => 8,
                'category' => 'users',
                'question' => 'Apa yang harus dilakukan jika user lupa password?',
                'answer' => 'Admin dapat mereset password user melalui halaman edit user, centang opsi "Reset Password ke NIDN", user akan mendapat password default sesuai NIDN mereka.',
                'is_popular' => true
            ],
            [
                'id' => 9,
                'category' => 'sistem',
                'question' => 'Bagaimana cara backup data pengajuan?',
                'answer' => 'Gunakan fitur export di halaman "Tinjau Pengajuan" untuk backup data. Data akan diekspor dalam format Excel dengan semua informasi lengkap.',
                'is_popular' => false
            ],
            [
                'id' => 10,
                'category' => 'sistem',
                'question' => 'Apa yang harus dilakukan jika sistem error?',
                'answer' => 'Pertama cek log error di sistem, lalu hubungi tim IT melalui email it@amikom.ac.id atau WhatsApp support. Sertakan screenshot error untuk mempercepat penanganan.',
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

        // Kategorikan FAQ untuk admin
        $categories = [
            'umum' => 'Umum',
            'review' => 'Review & Peninjauan',
            'dokumen' => 'Validasi Dokumen',
            'sertifikat' => 'Sertifikat',
            'users' => 'Kelola User',
            'sistem' => 'Sistem & Teknis'
        ];

        // FAQ populer
        $popularFaqs = collect($faqs)->where('is_popular', true)->take(6);

        // Panduan download untuk admin
        $guides = [
            [
                'title' => 'Panduan Admin HKI',
                'description' => 'Panduan lengkap untuk admin dalam mengelola sistem HKI',
                'file' => 'Panduan Admin HKI.pdf',
                'size' => '1 MB',
                'pages' => 6,
                'updated' => '2024-08-19'
            ]
            /*,
            [
                'title' => 'Checklist Review Pengajuan',
                'description' => 'Daftar poin-poin yang harus diperiksa saat review',
                'file' => 'Checklist Review HKI.pdf',
                'size' => '850 KB',
                'pages' => 6,
                'updated' => '2024-01-10'
            ],
            [
                'title' => 'Template Surat Resmi',
                'description' => 'Template surat untuk keperluan administrasi HKI',
                'file' => 'Template Surat HKI.pdf',
                'size' => '1.2 MB',
                'pages' => 8,
                'updated' => '2024-01-05'
            ]*/
        ];

        return view('admin.panduan.index', compact(
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
            'Panduan Admin HKI.pdf',       ];

        if (!in_array($filename, $allowedFiles)) {
            abort(404, 'File tidak ditemukan');
        }

        $filePath = storage_path('app/public/guides/admin/' . $filename);
        
        if (!file_exists($filePath)) {
            // Create dummy file for demo
            $this->createDummyGuide($filename);
        }

        return Response::download($filePath, $filename);
    }

    private function createDummyGuide($filename)
    {
        $guidesPath = storage_path('app/public/guides/admin');
        
        if (!file_exists($guidesPath)) {
            mkdir($guidesPath, 0755, true);
        }

        $filePath = $guidesPath . '/' . $filename;
        
        // Create simple PDF dummy content
        $content = "%PDF-1.4\n1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\n2 0 obj\n<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>\nendobj\n3 0 obj\n<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 612 792]\n/Contents 4 0 R\n>>\nendobj\n4 0 obj\n<<\n/Length 44\n>>\nstream\nBT\n/F1 12 Tf\n72 720 Td\n(Panduan Admin - " . $filename . ") Tj\nET\nendstream\nendobj\nxref\n0 5\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \n0000000209 00000 n \ntrailer\n<<\n/Size 5\n/Root 1 0 R\n>>\nstartxref\n309\n%%EOF";
        
        file_put_contents($filePath, $content);
    }

    public function exportFaq(Request $request)
    {
        // Export FAQ khusus admin
        $category = $request->get('category', 'all');
        $filename = 'faq-admin-sihaki-' . ($category !== 'all' ? $category : 'semua') . '-' . date('Y-m-d') . '.pdf';
        
        return $this->downloadGuide('Panduan Admin SiHaki.pdf');
    }
}
