<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\HkiSubmission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PublicSearchController extends Controller
{
    /**
     * ✅ ENHANCED: Beranda method with filters
     */
    public function beranda()
    {
        try {
            $statistics = $this->getOverallStatistics();
            $programStudiList = $this->getProgramStudiList();
            $availableYears = $this->getAvailableYears();
            
            return view('beranda', compact('statistics', 'programStudiList', 'availableYears'));
        } catch (\Exception $e) {
            Log::error('Error in beranda method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $statistics = $this->getEmptyStatistics();
            $programStudiList = collect();
            $availableYears = collect();
            
            return view('beranda', compact('statistics', 'programStudiList', 'availableYears'));
        }
    }

    /**
     * ✅ ENHANCED: Search with additional filters
     */
    public function search(Request $request)
    {
        $filter = $request->get('filter');
        $query = $request->get('q');
        $programStudi = $request->get('program_studi');
        $tahunPublikasi = $request->get('tahun_publikasi');
        
        if (empty($query) && empty($programStudi) && empty($tahunPublikasi)) {
            return back()->with('error', 'Silakan masukkan minimal satu kriteria pencarian');
        }

        // Enhanced search with all filters
        return $this->searchOnBeranda($request);
    }

    /**
     * ✅ ENHANCED: Search directly on beranda page with all filters
     */
    public function searchOnBeranda(Request $request)
    {
        $query = $request->get('q');
        $filter = $request->get('filter');
        $programStudi = $request->get('program_studi');
        $tahunPublikasi = $request->get('tahun_publikasi');
        
        if (empty($query) && empty($programStudi) && empty($tahunPublikasi)) {
            return redirect()->route('beranda');
        }

        $submissionsQuery = HkiSubmission::where('status', 'approved')
            ->with(['user:id,nama,program_studi', 'members:id,submission_id,name,is_leader']);

        // Apply text-based search filter
        if (!empty($query)) {
            $submissionsQuery->where(function($q) use ($query, $filter) {
                if ($filter === 'nama') {
                    $q->whereHas('user', function($userQuery) use ($query) {
                        $userQuery->where('nama', 'like', '%' . $query . '%');
                    })
                    ->orWhereHas('members', function($memberQuery) use ($query) {
                        $memberQuery->where('name', 'like', '%' . $query . '%');
                    });
                } elseif ($filter === 'institusi') {
                    $q->whereHas('user', function($userQuery) use ($query) {
                        $userQuery->where('program_studi', 'like', '%' . $query . '%');
                    });
                } elseif ($filter === 'judul') {
                    $q->where('title', 'like', '%' . $query . '%');
                } elseif ($filter === 'tipe') {
                    $q->where('creation_type', 'like', '%' . $query . '%');
                } else {
                    // Search all fields if no specific filter
                    $q->where('title', 'like', '%' . $query . '%')
                      ->orWhere('creation_type', 'like', '%' . $query . '%')
                      ->orWhereHas('user', function($userQuery) use ($query) {
                          $userQuery->where('nama', 'like', '%' . $query . '%');
                      })
                      ->orWhereHas('members', function($memberQuery) use ($query) {
                          $memberQuery->where('name', 'like', '%' . $query . '%');
                      });
                }
            });
        }

        // Apply program studi filter
        if (!empty($programStudi)) {
            $submissionsQuery->whereHas('user', function($userQuery) use ($programStudi) {
                $userQuery->where('program_studi', $programStudi);
            });
        }

        // Apply year filter
        if (!empty($tahunPublikasi)) {
            $submissionsQuery->where(function($q) use ($tahunPublikasi) {
                $q->whereYear('first_publication_date', $tahunPublikasi)
                  ->orWhere(function($subQ) use ($tahunPublikasi) {
                      $subQ->whereNull('first_publication_date')
                           ->whereYear('created_at', $tahunPublikasi);
                  });
            });
        }

        $submissions = $submissionsQuery->orderBy('created_at', 'desc')->limit(50)->get();

        $ciptaans = $submissions->map(function($submission) {
            $leader = $submission->members->where('is_leader', true)->first();
            
            return (object) [
                'id' => $submission->id,
                'judul' => $submission->title,
                'pencipta' => $leader->name ?? $submission->user->nama ?? 'N/A',
                'pencipta_id' => $submission->user_id,
                'jurusan' => $submission->user->program_studi ?? 'N/A',
                'tipe' => $this->getCreationTypeDisplayName($submission->creation_type),
                'tahun' => $submission->first_publication_date 
                    ? $submission->first_publication_date->format('Y')
                    : $submission->created_at->format('Y'),
                'tanggal_publikasi' => $submission->first_publication_date 
                    ? $submission->first_publication_date->format('d M Y')
                    : $submission->created_at->format('d M Y')
            ];
        });

        $statistics = $this->getOverallStatistics();
        $programStudiList = $this->getProgramStudiList();
        $availableYears = $this->getAvailableYears();
        
        // Add search summary
        $searchSummary = $this->generateSearchSummary($request, $ciptaans->count());

        return view('beranda', compact('ciptaans', 'statistics', 'programStudiList', 'availableYears', 'searchSummary'));
    }

    /**
     * ✅ NEW: Get list of all program studi from database
     */
    private function getProgramStudiList()
    {
        try {
            return User::select('program_studi')
                ->where('role', 'user')
                ->whereNotNull('program_studi')
                ->where('program_studi', '!=', '')
                ->distinct()
                ->orderBy('program_studi')
                ->pluck('program_studi');
        } catch (\Exception $e) {
            Log::error('Error getting program studi list', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * ✅ NEW: Get available publication years
     */
    private function getAvailableYears()
    {
        try {
            $years = collect();
            
            // Get years from first_publication_date
            $publicationYears = HkiSubmission::where('status', 'approved')
                ->whereNotNull('first_publication_date')
                ->select(DB::raw('YEAR(first_publication_date) as year'))
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');
            
            // Get years from created_at for submissions without publication date
            $createdYears = HkiSubmission::where('status', 'approved')
                ->whereNull('first_publication_date')
                ->select(DB::raw('YEAR(created_at) as year'))
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');
            
            // Merge and sort years
            $allYears = $publicationYears->merge($createdYears)->unique()->sort()->reverse()->values();
            
            return $allYears;
        } catch (\Exception $e) {
            Log::error('Error getting available years', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * ✅ NEW: Generate search summary
     */
    private function generateSearchSummary(Request $request, $resultCount)
    {
        $summary = [
            'count' => $resultCount,
            'filters' => []
        ];

        if ($request->filled('q')) {
            $summary['filters'][] = [
                'type' => 'keyword',
                'label' => 'Kata kunci',
                'value' => $request->get('q'),
                'filter_type' => $request->get('filter', 'semua')
            ];
        }

        if ($request->filled('program_studi')) {
            $summary['filters'][] = [
                'type' => 'program_studi',
                'label' => 'Program Studi',
                'value' => $request->get('program_studi')
            ];
        }

        if ($request->filled('tahun_publikasi')) {
            $summary['filters'][] = [
                'type' => 'tahun',
                'label' => 'Tahun Publikasi',
                'value' => $request->get('tahun_publikasi')
            ];
        }

        return $summary;
    }

    /**
     * ✅ ENHANCED: Pencipta method with program studi filter
     */
    public function pencipta(Request $request)
    {
        try {
            $perPage = 6;
            $query = $request->get('q');
            $searchBy = $request->get('search_by', 'nama_pencipta');
            $programStudi = $request->get('program_studi');

            Log::info('Pencipta search request', [
                'query' => $query,
                'search_by' => $searchBy,
                'program_studi' => $programStudi
            ]);

            $usersQuery = User::select([
                'users.id',
                'users.nama',
                'users.email',
                'users.program_studi',
                'users.foto',
                DB::raw('COUNT(DISTINCT hki_submissions.id) as total_hki')
            ])
            ->join('hki_submissions', 'users.id', '=', 'hki_submissions.user_id')
            ->where('users.role', 'user')
            ->where('hki_submissions.status', 'approved')
            ->groupBy([
                'users.id',
                'users.nama', 
                'users.email',
                'users.program_studi',
                'users.foto'
            ])
            ->having('total_hki', '>', 0);

            // Apply text search filter
            if (!empty(trim($query))) {
                switch ($searchBy) {
                    case 'nama_pencipta':
                        $usersQuery->where('users.nama', 'LIKE', '%' . trim($query) . '%');
                        break;
                    case 'program_studi':
                        $usersQuery->where('users.program_studi', 'LIKE', '%' . trim($query) . '%');
                        break;
                }
            }

            // Apply program studi filter
            if (!empty($programStudi)) {
                $usersQuery->where('users.program_studi', $programStudi);
            }

            $results = $usersQuery->orderBy('total_hki', 'desc')
                                 ->orderBy('users.nama', 'asc')
                                 ->paginate($perPage)
                                 ->appends($request->query());

            $results->getCollection()->transform(function ($user) {
                $user->submissions = HkiSubmission::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(['id', 'title', 'created_at']);
                
                $user->institusi = $user->institusi ?? 'STMIK AMIKOM Surakarta';
                $user->jurusan = $user->jurusan ?? 'N/A';
                
                return $user;
            });

            $programStudiList = $this->getProgramStudiList();

            Log::info('Search results', [
                'total_found' => $results->total(),
                'current_page' => $results->currentPage(),
                'query' => $query,
                'search_by' => $searchBy,
                'program_studi' => $programStudi
            ]);

            return view('pencipta', compact('results', 'query', 'searchBy', 'programStudi', 'programStudiList'));

        } catch (\Exception $e) {
            Log::error('Error in pencipta search', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            $results = User::whereRaw('1 = 0')->paginate($perPage ?? 6);
            $programStudiList = collect();
            return view('pencipta', compact('results', 'programStudiList'))->with('error', 'Terjadi kesalahan saat mencari data.');
        }
    }

    /**
     * ✅ ENHANCED: Jenis Ciptaan search with filters
     */
    public function searchJenisCiptaan(Request $request)
    {
        $searchBy = $request->get('search_by', 'jenis_ciptaan');
        $query = $request->get('q');
        $programStudi = $request->get('program_studi');
        $tahunPublikasi = $request->get('tahun_publikasi');
        
        $results = collect();
        
        if (!empty($query) || !empty($programStudi) || !empty($tahunPublikasi)) {
            $submissionsQuery = HkiSubmission::where('status', 'approved')
                ->with(['user', 'members']);

            // Apply text search
            if (!empty($query)) {
                if ($searchBy === 'jenis_ciptaan') {
                    $submissionsQuery->where('creation_type', 'like', '%' . $query . '%');
                } elseif ($searchBy === 'judul_ciptaan') {
                    $submissionsQuery->where('title', 'like', '%' . $query . '%');
                }
            }

            // Apply program studi filter
            if (!empty($programStudi)) {
                $submissionsQuery->whereHas('user', function($userQuery) use ($programStudi) {
                    $userQuery->where('program_studi', $programStudi);
                });
            }

            // Apply year filter
            if (!empty($tahunPublikasi)) {
                $submissionsQuery->where(function($q) use ($tahunPublikasi) {
                    $q->whereYear('first_publication_date', $tahunPublikasi)
                      ->orWhere(function($subQ) use ($tahunPublikasi) {
                          $subQ->whereNull('first_publication_date')
                               ->whereYear('created_at', $tahunPublikasi);
                      });
                });
            }

            $submissions = $submissionsQuery->get();
            $groupedSubmissions = $submissions->groupBy('creation_type');
            
            foreach ($groupedSubmissions as $type => $submissionGroup) {
                $results->push((object) [
                    'type' => $type,
                    'type_name' => $this->getCreationTypeDisplayName($type),
                    'count' => $submissionGroup->count(),
                    'submissions' => $submissionGroup,
                    'description' => $this->getCreationTypeDescription($type)
                ]);
            }
        }

        $programStudiList = $this->getProgramStudiList();
        $availableYears = $this->getAvailableYears();

        return view('jenis_ciptaan', compact('results', 'searchBy', 'query', 'programStudi', 'tahunPublikasi', 'programStudiList', 'availableYears'));
    }

    // ... (keep all other existing methods unchanged)

    private function getOverallStatistics()
    {
        try {
            $totalSubmissions = HkiSubmission::count();
            $approvedSubmissions = HkiSubmission::where('status', 'approved')->count();
            
            $totalUsers = User::where('role', 'user')
                ->whereExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('hki_submissions')
                          ->whereColumn('hki_submissions.user_id', 'users.id')
                          ->where('hki_submissions.status', 'approved');
                })
                ->count();

            $submissionsByType = HkiSubmission::where('status', 'approved')
                ->select('creation_type', DB::raw('COUNT(*) as count'))
                ->groupBy('creation_type')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
                ->map(function($item) {
                    return [
                        'type' => $item->creation_type,
                        'name' => $this->getCreationTypeDisplayName($item->creation_type),
                        'count' => $item->count,
                        'color' => $this->getTypeColor($item->creation_type)
                    ];
                });

            $submissionsByYear = HkiSubmission::where('status', 'approved')
                ->select(DB::raw('YEAR(created_at) as year'), DB::raw('COUNT(*) as count'))
                ->groupBy('year')
                ->orderBy('year', 'desc')
                ->limit(5)
                ->get();

            $recentSubmissions = HkiSubmission::with(['user:id,nama'])
                ->where('status', 'approved')
                ->orderBy('reviewed_at', 'desc')
                ->limit(6)
                ->get()
                ->map(function($submission) {
                    return [
                        'id' => $submission->id,
                        'title' => $submission->title,
                        'user_name' => $submission->user->nama ?? 'Unknown',
                        'creation_type' => $this->getCreationTypeDisplayName($submission->creation_type),
                        'creation_type_color' => $this->getTypeColor($submission->creation_type),
                        'year' => $submission->created_at->format('Y'),
                        'reviewed_at' => $submission->reviewed_at
                    ];
                });

            $topContributors = User::select([
                'users.id',
                'users.nama',
                'users.foto',
                'users.program_studi',
                DB::raw('COUNT(hki_submissions.id) as total_submissions')
            ])
            ->join('hki_submissions', 'users.id', '=', 'hki_submissions.user_id')
            ->where('users.role', 'user')
            ->where('hki_submissions.status', 'approved')
            ->groupBy(['users.id', 'users.nama', 'users.foto', 'users.program_studi'])
            ->orderBy('total_submissions', 'desc')
            ->limit(5)
            ->get();

            return [
                'total_submissions' => $totalSubmissions,
                'approved_submissions' => $approvedSubmissions,
                'total_users' => $totalUsers,
                'approval_rate' => $totalSubmissions > 0 ? round(($approvedSubmissions / $totalSubmissions) * 100, 1) : 0,
                'by_type' => $submissionsByType,
                'by_year' => $submissionsByYear,
                'recent_submissions' => $recentSubmissions,
                'top_contributors' => $topContributors
            ];

        } catch (\Exception $e) {
            Log::error('Error getting overall statistics', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return $this->getEmptyStatistics();
        }
    }

    private function getEmptyStatistics()
    {
        return [
            'total_submissions' => 0,
            'approved_submissions' => 0,
            'total_users' => 0,
            'approval_rate' => 0,
            'by_type' => collect(),
            'by_year' => collect(),
            'recent_submissions' => collect(),
            'top_contributors' => collect()
        ];
    }

    private function getCreationTypeDisplayName($type)
    {
        $types = [
            'program_komputer' => 'Program Komputer',
            'sinematografi' => 'Sinematografi',
            'buku' => 'Buku',
            'poster' => 'Poster',
            'fotografi' => 'Fotografi',
            'seni_gambar' => 'Seni Gambar',
            'karakter_animasi' => 'Karakter Animasi',
            'alat_peraga' => 'Alat Peraga',
            'basis_data' => 'Basis Data'
        ];

        return $types[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }

    private function getTypeColor($type)
    {
        $colors = [
            'program_komputer' => '#667eea',
            'sinematografi' => '#764ba2',
            'buku' => '#f093fb',
            'poster' => '#f5576c',
            'fotografi' => '#4facfe',
            'seni_gambar' => '#43e97b',
            'karakter_animasi' => '#fa709a',
            'alat_peraga' => '#feca57',
            'basis_data' => '#ff9ff3'
        ];

        return $colors[$type] ?? '#6c757d';
    }

    private function getCreationTypeDescription($type)
    {
        $descriptions = [
            'program_komputer' => 'Karya cipta berupa aplikasi, software, atau sistem komputer',
            'sinematografi' => 'Karya cipta berupa film, video, atau karya audiovisual',
            'buku' => 'Karya cipta berupa buku, jurnal, atau publikasi tertulis',
            'poster' => 'Karya cipta berupa desain poster atau media visual promosi',
            'fotografi' => 'Karya cipta berupa foto atau karya fotografi artistik',
            'seni_gambar' => 'Karya cipta berupa lukisan, ilustrasi, atau seni rupa',
            'karakter_animasi' => 'Karya cipta berupa desain karakter untuk animasi atau game',
            'alat_peraga' => 'Karya cipta berupa alat bantu pembelajaran atau demonstrasi',
            'basis_data' => 'Karya cipta berupa database atau sistem basis data'
        ];

        return $descriptions[$type] ?? 'Karya cipta dalam bidang ' . str_replace('_', ' ', $type);
    }

    // Keep all other existing methods...
    public function detailPencipta(Request $request, $id)
    {
        $user = User::where('id', $id)
            ->where('role', 'user')
            ->with([
                'submissions' => function($q) {
                    $q->where('status', 'approved')
                      ->with(['members', 'documents']);
                }
            ])
            ->first();

        if (!$user) {
            abort(404, 'Pencipta tidak ditemukan');
        }

        $submissions = $user->submissions->map(function($submission) {
            $certificate = $submission->documents->where('document_type', 'certificate')->first();
            
            return (object) [
                'id' => $submission->id,
                'judul' => $submission->title ?? 'Tidak ada judul',
                'tipe_hki' => ucfirst($submission->type ?? 'Unknown'),
                'jenis_hki' => $this->getCreationTypeDisplayName($submission->creation_type ?? 'unknown'),
                'uraian_singkat' => $submission->description ?? 'Tidak ada deskripsi',
                'tanggal_publikasi' => $submission->first_publication_date 
                    ? $submission->first_publication_date->format('d M Y') 
                    : ($submission->created_at ? $submission->created_at->format('d M Y') : 'Tidak diketahui'),
                'pencipta_utama' => $submission->members->where('is_leader', true)->first()->name ?? ($submission->user->nama ?? 'Tidak diketahui'),
                'anggota_pencipta' => $submission->members->where('is_leader', false)->pluck('name')->toArray(),
                'has_certificate' => $certificate ? true : false,
                'certificate_path' => $certificate ? $certificate->file_path : null,
                'created_at' => $submission->created_at,
                'status' => $submission->status,
                'creation_type' => $submission->creation_type ?? 'unknown'
            ];
        });

        $statistics = $this->generateStatistics($user->submissions->where('status', 'approved'));

        $pencipta = (object) [
            'id' => $user->id,
            'nama' => $user->nama ?? 'Tidak diketahui',
            'foto' => $user->foto ?? null, 
            'institusi' => 'STMIK AMIKOM Surakarta',
            'jurusan' => $user->program_studi ?? 'N/A',
            'total_hki' => $submissions->count(),
            'submissions' => $submissions
        ];

        return view('detail_pencipta', compact('pencipta', 'submissions', 'statistics'));
    }

    private function generateStatistics($submissions)
    {
        if (!$submissions || $submissions->isEmpty()) {
            return [
                'by_type' => collect(),
                'by_year' => collect(),
                'by_status' => collect(),
                'total_approved' => 0,
                'latest_submission' => null,
                'oldest_submission' => null
            ];
        }

        $typeStats = $submissions->groupBy('creation_type')->map(function($group, $type) {
            return [
                'type' => $type,
                'name' => $this->getCreationTypeDisplayName($type),
                'count' => $group->count(),
                'color' => $this->getCreationTypeColor($type)
            ];
        })->values();

        $yearStats = $submissions->groupBy(function($submission) {
            return $submission->created_at ? $submission->created_at->format('Y') : 'Unknown';
        })->map(function($group, $year) {
            return [
                'year' => $year,
                'count' => $group->count()
            ];
        })->sortBy('year')->values();

        $latestSubmission = $submissions->sortByDesc('created_at')->first();
        $oldestSubmission = $submissions->sortBy('created_at')->first();

        return [
            'by_type' => $typeStats,
            'by_year' => $yearStats,
            'by_status' => collect([
                [
                    'status' => 'approved',
                    'name' => 'Disetujui',
                    'count' => $submissions->count(),
                    'color' => '#28a745'
                ]
            ]),
            'total_approved' => $submissions->count(),
            'latest_submission' => $latestSubmission,
            'oldest_submission' => $oldestSubmission
        ];
    }

    private function getCreationTypeColor($type)
    {
        $colors = [
            'program_komputer' => '#007bff',
            'sinematografi' => '#6f42c1',
            'buku' => '#28a745',
            'poster' => '#fd7e14',
            'fotografi' => '#e83e8c',
            'seni_gambar' => '#20c997',
            'karakter_animasi' => '#6610f2',
            'alat_peraga' => '#ffc107',
            'basis_data' => '#dc3545'
        ];

        return $colors[$type] ?? '#6c757d';
    }

    public function viewCertificate($submissionId)
    {
        Log::info('Viewing certificate for submission:', ['id' => $submissionId]);
        
        $submission = HkiSubmission::where('id', $submissionId)
            ->where('status', 'approved')
            ->with(['documents' => function($q) {
                $q->where('document_type', 'certificate');
            }])
            ->first();

        if (!$submission) {
            Log::warning('Submission not found:', ['id' => $submissionId]);
            abort(404, 'Submission tidak ditemukan atau belum disetujui');
        }

        $certificate = $submission->documents->where('document_type', 'certificate')->first();
        
        if (!$certificate) {
            Log::warning('Certificate not found for submission:', ['id' => $submissionId]);
            return response()->view('errors.certificate-not-found', [
                'submission' => $submission
            ], 404);
        }

        $filePath = storage_path('app/public/' . $certificate->file_path);
        
        if (!file_exists($filePath)) {
            Log::error('Certificate file not found:', [
                'submission_id' => $submissionId,
                'file_path' => $certificate->file_path,
                'full_path' => $filePath
            ]);
            abort(404, 'File sertifikat tidak ditemukan');
        }

        Log::info('Certificate found, serving file:', [
            'submission_id' => $submissionId,
            'file_path' => $certificate->file_path
        ]);

        return response()->file($filePath, [
            'Content-Type' => $certificate->mime_type ?? 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . ($certificate->file_name ?? 'sertifikat.pdf') . '"'
        ]);
    }

    public function detailCiptaan(Request $request, $id)
    {
        $submission = HkiSubmission::where('id', $id)
            ->where('status', 'approved')
            ->with([
                'user',
                'members',
                'documents' => function($q) {
                    $q->where('document_type', 'certificate');
                }
            ])
            ->first();

        if (!$submission) {
            abort(404, 'Ciptaan tidak ditemukan');
        }

        $ciptaan = (object) [
            'id' => $submission->id,
            'judul' => $submission->title,
            'tipe_hki' => ucfirst($submission->type),
            'jenis_hki' => $this->getCreationTypeDisplayName($submission->creation_type),
            'uraian_singkat' => $submission->description,
            'tanggal_publikasi' => $submission->first_publication_date 
                ? $submission->first_publication_date->format('d M Y') 
                : $submission->created_at->format('d M Y'),
            'pencipta_utama' => $submission->members->where('is_leader', true)->first()->name ?? $submission->user->nama,
            'anggota_pencipta' => $submission->members->where('is_leader', false)->pluck('name')->toArray(),
            'has_certificate' => $submission->documents->where('document_type', 'certificate')->count() > 0,
            'certificate_path' => $submission->documents->where('document_type', 'certificate')->first()->file_path ?? null,
            'created_at' => $submission->created_at,
            'user' => $submission->user
        ];

        return view('detail_ciptaan', compact('ciptaan'));
    }

    public function detailJenis(Request $request, $type = null)
    {
        $searchBy = $request->get('search_by', 'jenis_ciptaan');
        $query = $request->get('q');
        $programStudi = $request->get('program_studi');
        $tahunPublikasi = $request->get('tahun_publikasi');
        
        $submissionsQuery = HkiSubmission::where('status', 'approved')
            ->with([
                'user', 
                'members',
                'documents' => function($q) {
                    $q->where('document_type', 'certificate');
                }
            ]);
        
        if ($type) {
            $submissionsQuery->where('creation_type', $type);
        } elseif (!empty($query)) {
            if ($searchBy === 'jenis_ciptaan') {
                $submissionsQuery->where('creation_type', 'like', '%' . $query . '%');
            } else {
                $submissionsQuery->where('title', 'like', '%' . $query . '%');
            }
        }

        // Apply program studi filter
        if (!empty($programStudi)) {
            $submissionsQuery->whereHas('user', function($userQuery) use ($programStudi) {
                $userQuery->where('program_studi', $programStudi);
            });
        }

        // Apply year filter
        if (!empty($tahunPublikasi)) {
            $submissionsQuery->where(function($q) use ($tahunPublikasi) {
                $q->whereYear('first_publication_date', $tahunPublikasi)
                  ->orWhere(function($subQ) use ($tahunPublikasi) {
                      $subQ->whereNull('first_publication_date')
                           ->whereYear('created_at', $tahunPublikasi);
                  });
            });
        }
        
        $submissions = $submissionsQuery->orderBy('created_at', 'desc')->paginate(10);
        $programStudiList = $this->getProgramStudiList();
        $availableYears = $this->getAvailableYears();
        
        return view('detail_jenis', compact('submissions', 'type', 'searchBy', 'query', 'programStudi', 'tahunPublikasi', 'programStudiList', 'availableYears'));
    }
}