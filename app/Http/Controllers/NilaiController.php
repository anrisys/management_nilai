<?php
// app/Http/Controllers/NilaiController.php

namespace App\Http\Controllers;

use App\Models\Nilai;
use App\Models\Siswa;
use App\Exports\NilaiExport;
use App\Imports\NilaiImport;
use App\Http\Requests\StoreNilaiRequest;
use App\Http\Requests\UpdateNilaiRequest;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;

class NilaiController extends Controller
{
    /**
     * Display a listing of the resource.
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

        return Inertia::render('nilai/Index', [
            'nilais' => $nilais,
            'siswas' => Siswa::all()->map(fn($siswa) => [
                'id' => $siswa->id,
                'nama' => $siswa->nama,
                'kelas' => $siswa->kelas,
            ]),
            'filters' => $request->only(['search', 'kelas', 'mapel']),
            'kelasList' => $kelasList,
            'mapelList' => $mapelList,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('nilai/Create', [
            'siswas' => Siswa::all()->map(fn($siswa) => [
                'id' => $siswa->id,
                'nama' => $siswa->nama,
                'kelas' => $siswa->kelas,
            ]),
            'mapelList' => ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'IPA', 'IPS', 'PKn', 'Seni Budaya', 'PJOK'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNilaiRequest $request)
    {
        try {
            Nilai::create($request->validated());
            
            return redirect()->route('dashboard')
                ->with('success', 'Data nilai berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan data nilai: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Nilai $nilai)
    {
        $nilai->load('siswa');
        
        return Inertia::render('nilai/Show', [
            'nilai' => $nilai,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Nilai $nilai)
    {
        $nilai->load('siswa');
        
        $nilai->load('siswa');
        
        return Inertia::render('nilai/Edit', [
            'nilai' => $nilai,
            'mapelList' => ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'IPA', 'IPS', 'PKn', 'Seni Budaya', 'PJOK'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNilaiRequest $request, Nilai $nilai)
    {
        try {
            $nilai->update([
                'nilai' => $request->nilai
            ]);
            
            return redirect()->route('dashboard')
                ->with('success', 'Data nilai berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data nilai: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nilai $nilai)
    {
        try {
            $nilai->delete();
            
            return redirect()->route('dashboard')
                ->with('success', 'Data nilai berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data nilai: ' . $e->getMessage());
        }
    }

    /**
     * Get nilai by siswa
     */
    public function bySiswa(Siswa $siswa)
    {
        $nilais = Nilai::with('siswa')
            ->where('siswa_id', $siswa->id)
            ->orderBy('mapel')
            ->get();

        return Inertia::render('nilai/BySiswa', [
            'siswa' => $siswa,
            'nilais' => $nilais,
        ]);
    }

    /**
     * Export data to Excel
     */
    public function export()
    {
        return Excel::download(new NilaiExport, 'data-nilai-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Import data from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:10240' // 10MB max
        ]);

        try {
            Excel::import(new NilaiImport, $request->file('file'));
            
            return redirect()->route('nilai.index')
                ->with('success', 'Data berhasil diimport!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel
     */
    public function downloadTemplate()
    {
        $templateData = [
            ['No', 'nama', 'kelas', 'mapel', 'nilai'],
            [1, 'Andi', '7A', 'Matematika', 80],
            [2, 'Andi', '7A', 'Bahasa Indonesia', 70],
            [3, 'Budi', '7A', 'Matematika', 60],
            [4, 'Budi', '7A', 'Bahasa Indonesia', 75],
            [5, 'Citra', '7B', 'Matematika', 90],
            [6, 'Citra', '7B', 'Bahasa Indonesia', 85],
        ];

        $export = new class($templateData) implements FromArray, WithHeadings {
            private $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }

            public function headings(): array
            {
                return $this->data[0];
            }
        };

        return Excel::download($export, 'template-import-nilai.xlsx');
    }
}