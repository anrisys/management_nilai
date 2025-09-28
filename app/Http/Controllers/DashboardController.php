<?php

namespace App\Http\Controllers;

use App\Models\Nilai;
use App\Models\Siswa;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with nilai data
     */
    public function index(Request $request)
    {
        $query = Nilai::with('siswa')
            ->orderBy('updated_at', 'desc')
            ->orderBy('id', 'desc');

        // Filter berdasarkan search
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('kelas', 'like', '%' . $request->search . '%')
                  ->orWhere('mapel', 'like', '%' . $request->search . '%')
                  ->orWhere('nilai', 'like', '%' . $request->search . '%')
                  ->orWhereHas('siswa', function($q) use ($request) {
                      $q->where('nama', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Filter berdasarkan kelas
        if ($request->has('kelas') && $request->kelas != '') {
            $query->where('kelas', $request->kelas);
        }

        // Filter berdasarkan mapel
        if ($request->has('mapel') && $request->mapel != '') {
            $query->where('mapel', $request->mapel);
        }

        $nilais = $query->paginate(15);

        // Data untuk filters
        $kelasList = Nilai::distinct()->pluck('kelas')->sort();
        $mapelList = Nilai::distinct()->pluck('mapel')->sort();

        // Statistics for dashboard
        $totalSiswa = Siswa::count();
        $totalNilai = Nilai::count();
        $averageNilai = Nilai::avg('nilai');
        $topStudents = Nilai::with('siswa')
            ->select('siswa_id')
            ->selectRaw('AVG(nilai) as average_nilai')
            ->groupBy('siswa_id')
            ->orderBy('average_nilai', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'nilais' => $nilais,
            'siswas' => Siswa::all()->map(fn($siswa) => [
                'id' => $siswa->id,
                'nama' => $siswa->nama,
                'kelas' => $siswa->kelas,
            ]),
            'filters' => $request->only(['search', 'kelas', 'mapel']),
            'kelasList' => $kelasList,
            'mapelList' => $mapelList,
            'statistics' => [
                'totalSiswa' => $totalSiswa,
                'totalNilai' => $totalNilai,
                'averageNilai' => round($averageNilai, 2),
                'topStudents' => $topStudents,
            ]
        ]);
    }
}