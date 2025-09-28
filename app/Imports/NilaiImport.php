<?php

namespace App\Imports;

use App\Models\Nilai;
use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NilaiImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * Import data from Excel collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['nama']) || empty($row['kelas']) || empty($row['mapel'])) {
                continue;
            }

            $siswa = Siswa::firstOrCreate(
                [
                    'nama' => $row['nama'],
                    'kelas' => $row['kelas']
                ],
                [
                    'nama' => $row['nama'],
                    'kelas' => $row['kelas']
                ]
            );

            $nilai = $this->validateNilai($row['nilai'] ?? 0);

            // Update atau create data nilai
            Nilai::updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'mapel' => $row['mapel']
                ],
                [
                    'kelas' => $row['kelas'],
                    'nilai' => $nilai
                ]
            );
        }
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            '*.nama' => 'required|string|max:255',
            '*.kelas' => 'required|string|max:10',
            '*.mapel' => 'required|string|max:100',
            '*.nilai' => 'nullable|integer|min:0|max:100',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            '*.nama.required' => 'Kolom nama wajib diisi',
            '*.kelas.required' => 'Kolom kelas wajib diisi',
            '*.mapel.required' => 'Kolom mapel wajib diisi',
            '*.nilai.integer' => 'Kolom nilai harus berupa angka',
            '*.nilai.min' => 'Nilai minimal 0',
            '*.nilai.max' => 'Nilai maksimal 100',
        ];
    }

    /**
     * Validate and convert nilai
     */
    private function validateNilai($nilai): int
    {
        // Convert to integer
        $nilai = intval($nilai);
        
        // Ensure nilai is between 0-100
        return max(0, min(100, $nilai));
    }

    /**
     * Specify heading row (1-based index)
     */
    public function headingRow(): int
    {
        return 1;
    }
}