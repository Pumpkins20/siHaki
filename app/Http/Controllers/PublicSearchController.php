<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HkiSubmission;
use App\Models\SubmissionMember;
use App\Models\User;

class PublicSearchController extends Controller
{
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
                    'search_by' => 'program_studi',
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
            return view('beranda');
        }

        // Get approved submissions for public display
        $submissions = HkiSubmission::where('status', 'approved')
            ->with(['user', 'members'])
            ->where(function($q) use ($query, $filter) {
                if ($filter === 'nama') {
                    // Search by creator name
                    $q->whereHas('members', function($memberQuery) use ($query) {
                        $memberQuery->where('name', 'like', '%' . $query . '%');
                    });
                } elseif ($filter === 'institusi') {
                    // Search by department/jurusan
                    $q->whereHas('user', function($userQuery) use ($query) {
                        $userQuery->whereHas('department', function($deptQuery) use ($query) {
                            $deptQuery->where('name', 'like', '%' . $query . '%');
                        });
                    });
                } elseif ($filter === 'judul') {
                    // Search by title
                    $q->where('title', 'like', '%' . $query . '%');
                } elseif ($filter === 'tipe') {
                    // Search by creation type
                    $q->where('creation_type', 'like', '%' . $query . '%');
                } else {
                    // General search across all fields
                    $q->where('title', 'like', '%' . $query . '%')
                      ->orWhere('creation_type', 'like', '%' . $query . '%')
                      ->orWhereHas('members', function($memberQuery) use ($query) {
                          $memberQuery->where('name', 'like', '%' . $query . '%');
                      });
                }
            })
            ->paginate(10);

        // Transform data for display
        $ciptaans = $submissions->map(function($submission) {
            return (object) [
                'id' => $submission->id,
                'judul' => $submission->title,
                'pencipta' => $submission->members->where('is_leader', true)->first()->name ?? 'N/A',
                'pencipta_id' => $submission->user_id,
                'jurusan' => $submission->user->department->name ?? 'N/A',
                'tipe' => ucfirst(str_replace('_', ' ', $submission->creation_type)),
                'tahun' => $submission->created_at->format('Y')
            ];
        });

        return view('beranda', compact('ciptaans'));
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
                // Search by creator name
                $members = SubmissionMember::where('name', 'like', '%' . $query . '%')
                    ->whereHas('submission', function($q) {
                        $q->where('status', 'approved');
                    })
                    ->with(['submission.user.department'])
                    ->get()
                    ->groupBy('name');

                foreach ($members as $name => $memberGroup) {
                    $member = $memberGroup->first();
                    $results->push((object) [
                        'id' => $member->id,
                        'nama' => $member->name,
                        'institusi' => 'STMIK AMIKOM Surakarta',
                        'jurusan' => $member->submission->user->department->name ?? 'N/A',
                        'total_hki' => $memberGroup->count(),
                        'submissions' => $memberGroup->pluck('submission')
                    ]);
                }
                
            } elseif ($searchBy === 'program_studi') {
                // Search by department
                $users = User::whereHas('department', function($q) use ($query) {
                        $q->where('name', 'like', '%' . $query . '%');
                    })
                    ->whereHas('submissions', function($q) {
                        $q->where('status', 'approved');
                    })
                    ->with(['department', 'submissions' => function($q) {
                        $q->where('status', 'approved')->with('members');
                    }])
                    ->get();

                foreach ($users as $user) {
                    $leaderMember = $user->submissions->flatMap->members->where('is_leader', true)->first();
                    if ($leaderMember) {
                        $results->push((object) [
                            'id' => $user->id,
                            'nama' => $leaderMember->name,
                            'institusi' => 'STMIK AMIKOM Surakarta',
                            'jurusan' => $user->department->name ?? 'N/A',
                            'total_hki' => $user->submissions->count(),
                            'submissions' => $user->submissions
                        ]);
                    }
                }
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
            'poster' => 'Karya cipta berupa desain poster atau media visual',
            'fotografi' => 'Karya cipta berupa foto atau karya fotografi',
            'seni_gambar' => 'Karya cipta berupa lukisan, ilustrasi, atau seni rupa',
            'karakter_animasi' => 'Karya cipta berupa desain karakter untuk animasi',
            'alat_peraga' => 'Karya cipta berupa alat bantu pembelajaran atau demonstrasi',
            'basis_data' => 'Karya cipta berupa database atau sistem basis data'
        ];

        return $descriptions[$type] ?? 'Karya cipta dalam bidang ' . str_replace('_', ' ', $type);
    }
}
