<?php

namespace App\Exports;

use App\Models\HasilPenilaian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class HasilPenilaianExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $nilai;
    protected $daftarIndikator;
    protected $infoNilai;

    public function __construct($nilai, $daftarIndikator, $infoNilai)
    {
        $this->nilai = $nilai;
        $this->daftarIndikator = $daftarIndikator;
        $this->infoNilai = $infoNilai;
    }

    public function collection()
    {
        // dd($this->nilai);
        $data = [];

        // Add header row
        $headerRow = ['No', 'Email', 'Nama'];
        foreach ($this->daftarIndikator as $indikator) {
            $headerRow[] = $indikator['nama'];
        }
        $headerRow[] = 'Rata-rata';
        $data[] = $headerRow;
        // dd($data);

        // Add data rows
        foreach ($this->nilai as $index => $dinilai) {
            // dd($dinilai);
            $rowData = [
                $index + 1,
                $dinilai['dinilai']['email'],
                $dinilai['dinilai']['name'],
            ];

            // dd($rowData);
            foreach ($this->daftarIndikator as $indikator) {
                $rowData[] = $dinilai['nilai'][$indikator['nama']]['nilai_akhir'];
            }
            $rowData[] = $dinilai['rata_rata_total'];
            // dd($rowData);
            $data[] = $rowData;
        }

        // dd($data);

        // Add total row
        $totalRow = ['', 'Rata-rata', ''];
        foreach ($this->daftarIndikator as $indikator) {
            $totalRow[] = $this->infoNilai['rerata_indikator'][$indikator['nama']];
        }
        $totalRow[] = $this->infoNilai['rerata_total'];
        // dd($totalRow);
        $data[] = $totalRow;
        // dd($data);

        return collect($data);
    }

    public function headings(): array
    {
        return [];
    }
}
