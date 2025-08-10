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
     * ✅ FIXED: Beranda method to prevent infinite loops
     */
    public function beranda()
    {
        try {
            // ✅ FIXED: Get statistics with proper error handling
            $statistics = $this->getOverallStatistics();
            
            // ✅ FIXED: Ensure we return the view only once
            return view('beranda', compact('statistics'));
            
        } catch (\Exception $e) {
            Log::error('Error in beranda method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // ✅ FIXED: Return view with empty statistics on error
            $statistics = $this->getEmptyStatistics();
            return view('beranda', compact('statistics'));
        }
    }

    /**
     * ✅ FIXED: Get overall statistics - prevent infinite loops
     */
    private function getOverallStatistics()
    {
        try {
            // ✅ FIXED: Basic counts with proper queries
            $totalSubmissions = HkiSubmission::count();
            $approvedSubmissions = HkiSubmission::where('status', 'approved')->count();
            
            // ✅ FIXED: Count unique users with approved submissions
            $totalUsers = User::where('role', 'user')
                ->whereExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('hki_submissions')
                          ->whereColumn('hki_submissions.user_id', 'users.id')
                          ->where('hki_submissions.status', 'approved');
                })
                ->count();

            // ✅ FIXED: Submissions by creation type (limit to prevent memory issues)
            $submissionsByType = HkiSubmission::where('status', 'approved')
                ->select('creation_type', DB::raw('COUNT(*) as count'))
                ->groupBy('creation_type')
                ->orderBy('count', 'desc')
                ->limit(10) // ✅ LIMIT to prevent infinite loops
                ->get()
                ->map(function($item) {
                    return [
                        'type' => $item->creation_type,
                        'name' => $this->getCreationTypeDisplayName($item->creation_type),
                        'count' => $item->count,
                        'color' => $this->getTypeColor($item->creation_type)
                    ];
                });

            // ✅ FIXED: Submissions by year (limit to last 5 years only)
            $submissionsByYear = HkiSubmission::where('status', 'approved')
                ->select(DB::raw('YEAR(created_at) as year'), DB::raw('COUNT(*) as count'))
                ->groupBy('year')
                ->orderBy('year', 'desc')
                ->limit(5) // ✅ LIMIT to prevent infinite data
                ->get();

            // ✅ FIXED: Recent approved submissions (limit to 6)
            $recentSubmissions = HkiSubmission::with(['user:id,nama'])
                ->where('status', 'approved')
                ->orderBy('reviewed_at', 'desc')
                ->limit(6) // ✅ STRICT LIMIT
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

            // ✅ FIXED: Top contributors (limit to 5)
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
            ->limit(5) // ✅ STRICT LIMIT
            ->get();

            // ✅ FIXED: Return structured array
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

    /**
     * ✅ NEW: Get empty statistics structure
     */
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

    /**
     * ✅ FIXED: Get creation type display name
     */
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

    /**
     * ✅ FIXED: Get type color for charts
     */
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

    /**
     * Handle search from beranda
     */
    public function search(Request $request)
    {
        $filter = $request->get('filter');
        $query = $request->get('q');
        
        if (empty($query)) {
            return back()->with('error', 'Silakan masukkan kata kunci pencarian');
        }

        // Redirect based on filter type
        switch ($filter) {
            case 'nama':
                return redirect()->route('pencipta', [
                    'search_by' => 'nama_pencipta',
                    'q' => $query
                ]);
                
            case 'institusi':
                return redirect()->route('pencipta', [
                    'search_by' => 'jurusan',
                    'q' => $query
                ]);
                
            case 'judul':
                return redirect()->route('jenis_ciptaan', [
                    'search_by' => 'judul_ciptaan',
                    'q' => $query
                ]);
                
            case 'tipe':
                return redirect()->route('jenis_ciptaan', [
                    'search_by' => 'jenis_ciptaan',
                    'q' => $query
                ]);
                
            default:
                // Default search on beranda
                return $this->searchOnBeranda($request);
        }
    }

    /**
     * Search directly on beranda page
     */
    public function searchOnBeranda(Request $request)
    {
        $query = $request->get('q');
        $filter = $request->get('filter');
        
        if (empty($query)) {
            return redirect()->route('beranda');
        }

        // Get approved submissions for public display
        $submissions = HkiSubmission::where('status', 'approved')
            ->with(['user:id,nama,jurusan,institusi', 'members:id,submission_id,name,is_leader'])
            ->where(function($q) use ($query, $filter) {
                if ($filter === 'nama') {
                    // Search by creator name
                    $q->whereHas('user', function($userQuery) use ($query) {
                        $userQuery->where('nama', 'like', '%' . $query . '%');
                    })
                    ->orWhereHas('members', function($memberQuery) use ($query) {
                        $memberQuery->where('name', 'like', '%' . $query . '%');
                    });
                } elseif ($filter === 'institusi') {
                    // Search by department/institution
                    $q->whereHas('user', function($userQuery) use ($query) {
                        $userQuery->where('jurusan', 'like', '%' . $query . '%')
                               ->orWhere('institusi', 'like', '%' . $query . '%');
                    });
                } elseif ($filter === 'judul') {
                    // Search by title
                    $q->where('title', 'like', '%' . $query . '%');
                } elseif ($filter === 'tipe') {
                    // Search by creation type
                    $q->where('creation_type', 'like', '%' . $query . '%');
                } else {
                    // Default: search all fields
                    $q->where('title', 'like', '%' . $query . '%')
                      ->orWhere('creation_type', 'like', '%' . $query . '%')
                      ->orWhereHas('members', function($memberQuery) use ($query) {
                          $memberQuery->where('name', 'like', '%' . $query . '%');
                      });
                }
            })
            ->limit(50) // ✅ LIMIT search results
            ->get();

        // Transform data for display
        $ciptaans = $submissions->map(function($submission) {
            $leader = $submission->members->where('is_leader', true)->first();
            
            return (object) [
                'id' => $submission->id,
                'judul' => $submission->title,
                'pencipta' => $leader->name ?? $submission->user->nama ?? 'N/A',
                'pencipta_id' => $submission->user_id,
                'jurusan' => $submission->user->jurusan ?? 'N/A',
                'tipe' => $this->getCreationTypeDisplayName($submission->creation_type),
                'tahun' => $submission->created_at->format('Y')
            ];
        });

        // Get statistics but don't create infinite loop
        $statistics = $this->getOverallStatistics();

        return view('beranda', compact('ciptaans', 'statistics'));
    }

    /**
     * Handle pencipta page search
     */
    public function searchPencipta(Request $request)
    {
        $searchBy = $request->get('search_by', 'nama_pencipta');
        $query = $request->get('q');
        
        $results = collect();
        
        if (!empty($query)) {
            if ($searchBy === 'nama_pencipta') {
                // ✅ UPDATED: Search by leader/creator name (from users who submitted HKI)
                $users = User::where('role', 'user')
                    ->where('nama', 'like', '%' . $query . '%')
                    ->whereHas('submissions', function($q) {
                        $q->where('status', 'approved');
                    })
                    ->with([
                        'submissions' => function($q) {
                            $q->where('status', 'approved')
                              ->with(['members', 'documents']);
                        },
                        'department'
                    ])
                    ->get();

                foreach ($users as $user) {
                    $results->push((object) [
                        'id' => $user->id,
                        'user_id' => $user->id, // ✅ ADD: For total submission tracking
                        'nama' => $user->nama,
                        'foto' => $user->foto ?? 'default.png',
                        'institusi' => 'STMIK AMIKOM Surakarta',
                        'jurusan' => $user->program_studi ?? ($user->department->name ?? 'N/A'),
                        'total_hki' => $user->submissions->count(),
                        'submissions' => $user->submissions
                    ]);
                }
                
            } elseif ($searchBy === 'jurusan') {
                // ✅ UPDATED: Search by program_studi or department
                $users = User::where('role', 'user')
                    ->where(function($q) use ($query) {
                        $q->where('program_studi', 'like', '%' . $query . '%')
                          ->orWhereHas('department', function($deptQuery) use ($query) {
                              $deptQuery->where('name', 'like', '%' . $query . '%');
                          });
                    })
                    ->whereHas('submissions', function($q) {
                        $q->where('status', 'approved');
                    })
                    ->with([
                        'submissions' => function($q) {
                            $q->where('status', 'approved')
                              ->with(['members', 'documents']);
                        },
                        'department'
                    ])
                    ->get();

                foreach ($users as $user) {
                    $results->push((object) [
                        'id' => $user->id,
                        'user_id' => $user->id,
                        'nama' => $user->nama,
                        'foto' => $user->foto ?? 'default.png',
                        'institusi' => 'STMIK AMIKOM Surakarta',
                        'jurusan' => $user->program_studi ?? ($user->department->name ?? 'N/A'),
                        'total_hki' => $user->submissions->count(),
                        'submissions' => $user->submissions
                    ]);
                }
            }
        } else {
            // ✅ Show all users with approved submissions when no search
            $users = User::where('role', 'user')
                ->whereHas('submissions', function($q) {
                    $q->where('status', 'approved');
                })
                ->with([
                    'submissions' => function($q) {
                        $q->where('status', 'approved')
                          ->with(['members', 'documents']);
                    },
                    'department'
                ])
                ->get();

            foreach ($users as $user) {
                $results->push((object) [
                    'id' => $user->id,
                    'user_id' => $user->id,
                    'nama' => $user->nama,
                    'foto' => $user->foto ?? 'default.png',
                    'institusi' => 'STMIK AMIKOM Surakarta',
                    'jurusan' => $user->program_studi ?? ($user->department->name ?? 'N/A'),
                    'total_hki' => $user->submissions->count(),
                    'submissions' => $user->submissions
                ]);
            }
        }

        return view('pencipta', compact('results', 'searchBy', 'query'));
    }


    /**
     * Handle jenis ciptaan page search
     */
    public function searchJenisCiptaan(Request $request)
    {
        $searchBy = $request->get('search_by', 'jenis_ciptaan');
        $query = $request->get('q');
        
        $results = collect();
        
        if (!empty($query)) {
            if ($searchBy === 'jenis_ciptaan') {
                // Search by creation type
                $submissions = HkiSubmission::where('status', 'approved')
                    ->where('creation_type', 'like', '%' . $query . '%')
                    ->with(['user.department', 'members'])
                    ->get()
                    ->groupBy('creation_type');

                foreach ($submissions as $type => $submissionGroup) {
                    $results->push((object) [
                        'type' => $type,
                        'type_name' => ucfirst(str_replace('_', ' ', $type)),
                        'count' => $submissionGroup->count(),
                        'submissions' => $submissionGroup,
                        'description' => $this->getCreationTypeDescription($type)
                    ]);
                }
                
            } elseif ($searchBy === 'judul_ciptaan') {
                // Search by title
                $submissions = HkiSubmission::where('status', 'approved')
                    ->where('title', 'like', '%' . $query . '%')
                    ->with(['user.department', 'members'])
                    ->get();

                // Group by creation type for display
                $groupedSubmissions = $submissions->groupBy('creation_type');
                
                foreach ($groupedSubmissions as $type => $submissionGroup) {
                    $results->push((object) [
                        'type' => $type,
                        'type_name' => ucfirst(str_replace('_', ' ', $type)),
                        'count' => $submissionGroup->count(),
                        'submissions' => $submissionGroup,
                        'description' => $this->getCreationTypeDescription($type),
                        'search_results' => $submissionGroup->map(function($submission) {
                            return (object) [
                                'title' => $submission->title,
                                'creator' => $submission->members->where('is_leader', true)->first()->name ?? 'N/A',
                                'year' => $submission->created_at->format('Y'),
                                'department' => $submission->user->department->name ?? 'N/A'
                            ];
                        })
                    ]);
                }
            }
        }

        return view('jenis_ciptaan', compact('results', 'searchBy', 'query'));
    }

    /**
     * Get creation type description
     */
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

    /**
     * ✅ NEW: Show detail pencipta with all their HKI submissions
     */
    public function detailPencipta(Request $request, $id)
    {
        $user = User::where('id', $id)
            ->where('role', 'user')
            ->with([
                'submissions' => function($q) {
                    // ✅ FIXED: Show ALL approved submissions, not just with certificates
                    $q->where('status', 'approved')
                    ->with(['members', 'documents']); // Remove certificate filter
                },
                'department'
            ])
            ->first();

        if (!$user) {
            abort(404, 'Pencipta tidak ditemukan');
        }

        // ✅ DEBUG: Log submissions
        Log::info('User submissions count:', [
            'user_id' => $user->id,
            'total_submissions' => $user->submissions->count(),
            'approved_submissions' => $user->submissions->where('status', 'approved')->count()
        ]);

        // Transform submissions for detail view
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
                'has_certificate' => $certificate ? true : false, // ✅ FIXED: Check if certificate exists
                'certificate_path' => $certificate ? $certificate->file_path : null,
                'created_at' => $submission->created_at,
                'status' => $submission->status,
                'creation_type' => $submission->creation_type ?? 'unknown'
            ];
        });

        // Generate statistics - only for approved submissions
        $statistics = $this->generateStatistics($user->submissions->where('status', 'approved'));

        $pencipta = (object) [
            'id' => $user->id,
            'nama' => $user->nama ?? 'Tidak diketahui',
            'foto' => $user->foto ?? null, 
            'institusi' => 'STMIK AMIKOM Surakarta',
            'jurusan' => $user->program_studi ?? ($user->department->name ?? 'N/A'),
            'total_hki' => $submissions->count(),
            'submissions' => $submissions
        ];

        return view('detail_pencipta', compact('pencipta', 'submissions', 'statistics'));
    }

    /**
     * ✅ NEW: Generate statistics for chart
     */
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

        // Count by creation type
        $typeStats = $submissions->groupBy('creation_type')->map(function($group, $type) {
            return [
                'type' => $type,
                'name' => $this->getCreationTypeDisplayName($type),
                'count' => $group->count(),
                'color' => $this->getCreationTypeColor($type)
            ];
        })->values();

        // Count by year
        $yearStats = $submissions->groupBy(function($submission) {
            return $submission->created_at ? $submission->created_at->format('Y') : 'Unknown';
        })->map(function($group, $year) {
            return [
                'year' => $year,
                'count' => $group->count()
            ];
        })->sortBy('year')->values();

        // Get date ranges safely
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

    /**
     * ✅ NEW: Get color for creation type
     */
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
            // ✅ ENHANCED: Return error page instead of 404
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

    /**
     * ✅ NEW: Show detail ciptaan
     */
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

        // Transform data for view
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

    /**
     * ✅ NEW: Show detail jenis with all submissions of that type
     */
    public function detailJenis(Request $request, $type = null)
    {
        $searchBy = $request->get('search_by', 'jenis_ciptaan');
        $query = $request->get('q');
        
        $submissions = collect();
        
        if ($type) {
            // Show all submissions of specific type with full relations
            $submissions = HkiSubmission::where('status', 'approved')
                ->where('creation_type', $type)
                ->with([
                    'user.department', 
                    'members',
                    'documents' => function($q) {
                        $q->where('document_type', 'certificate');
                    }
                ])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
                
        } elseif (!empty($query)) {
            // Search functionality with full relations
            if ($searchBy === 'jenis_ciptaan') {
                $submissions = HkiSubmission::where('status', 'approved')
                    ->where('creation_type', 'like', '%' . $query . '%')
                    ->with([
                        'user.department', 
                        'members',
                        'documents' => function($q) {
                            $q->where('document_type', 'certificate');
                        }
                    ])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
            } else {
                $submissions = HkiSubmission::where('status', 'approved')
                    ->where('title', 'like', '%' . $query . '%')
                    ->with([
                        'user.department', 
                        'members',
                        'documents' => function($q) {
                            $q->where('document_type', 'certificate');
                        }
                    ])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
            }
        }
        
        Log::info('Detail Jenis Query Result:', [
            'type' => $type,
            'query' => $query,
            'searchBy' => $searchBy,
            'total_submissions' => $submissions->count(),
            'submissions_with_certs' => $submissions->where('documents.count', '>', 0)->count()
        ]);
        
        return view('detail_jenis', compact('submissions', 'type', 'searchBy', 'query'));
    }

    /**
     * ✅ FIXED: Pencipta method with proper search and show all functionality
     */
    public function pencipta(Request $request)
    {
        try {
            $perPage = 6; // 6 pencipta per page
            $query = $request->get('q');
            $searchBy = $request->get('search_by', 'nama_pencipta');

            Log::info('Pencipta search request', [
                'query' => $query,
                'search_by' => $searchBy,
                'per_page' => $perPage
            ]);

            // ✅ FIXED: Base query for users with approved HKI submissions
            $usersQuery = User::select([
                'users.id',
                'users.nama',
                'users.email',
                'users.institusi',
                'users.jurusan',
                'users.foto',
                DB::raw('COUNT(DISTINCT hki_submissions.id) as total_hki')
            ])
            ->join('hki_submissions', 'users.id', '=', 'hki_submissions.user_id') // Use inner join to ensure only users with submissions
            ->where('users.role', 'user') // Only show users with role 'user'
            ->where('hki_submissions.status', 'approved') // Only count approved submissions
            ->groupBy([
                'users.id',
                'users.nama', 
                'users.email',
                'users.institusi',
                'users.jurusan',
                'users.foto'
            ])
            ->having('total_hki', '>', 0); // Only users with approved HKI

            // ✅ FIXED: Apply search filters only if query is provided
            if (!empty($query)) {
                switch ($searchBy) {
                    case 'nama_pencipta':
                        $usersQuery->where('users.nama', 'like', '%' . $query . '%');
                        break;
                    case 'jurusan':
                        $usersQuery->where('users.jurusan', 'like', '%' . $query . '%');
                        break;
                    default:
                        // Default to nama_pencipta if invalid search_by
                        $usersQuery->where('users.nama', 'like', '%' . $query . '%');
                        break;
                }
            }

            // ✅ FIXED: Get paginated results
            $results = $usersQuery->orderBy('total_hki', 'desc')
                                 ->orderBy('users.nama')
                                 ->paginate($perPage)
                                 ->appends($request->query()); // Preserve query parameters

            // ✅ FIXED: Load recent submissions for each paginated user
            $results->getCollection()->transform(function ($user) {
                $user->submissions = HkiSubmission::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(['id', 'title', 'created_at']);
                return $user;
            });

            Log::info('Pencipta search completed', [
                'total_results' => $results->total(),
                'current_page' => $results->currentPage(),
                'per_page' => $results->perPage(),
                'last_page' => $results->lastPage(),
                'query' => $query,
                'search_by' => $searchBy
            ]);

            return view('pencipta', compact('results', 'query', 'searchBy'));

        } catch (\Exception $e) {
            Log::error('Error in pencipta search', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // ✅ FIXED: Return empty paginated results on error
            $results = User::whereRaw('1 = 0')->paginate($perPage ?? 6);
            return view('pencipta', compact('results'))->with('error', 'Terjadi kesalahan saat mencari data pencipta.');
        }
    }

    /**
     * ✅ ENHANCED: Show all pencipta (same as pencipta method but clearer intent)
     */
    public function showAllPencipta(Request $request)
    {
        // Just redirect to main pencipta route without search parameters
        return redirect()->route('pencipta');
    }
}