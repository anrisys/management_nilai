<?php

namespace App\Exports;

use App\Models\Nilai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NilaiExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Nilai::with('siswa')
            ->orderBy('siswa_id')
            ->orderBy('mapel')
            ->get();
    }

    /**
     * Define the headings
     */
    public function headings(): array
    {
        return [
            'No',
            'nama',
            'kelas',
            'mapel',
            'nilai'
        ];
    }

    /**
     * Map the data for each row
     */
    public function map($nilai): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            $nilai->siswa->nama,
            $nilai->kelas,
            $nilai->mapel,
            $nilai->nilai
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFE0E0E0']
                ]
            ],
        ];
    }
}