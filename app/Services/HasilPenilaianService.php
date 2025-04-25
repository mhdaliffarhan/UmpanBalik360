<?php

namespace App\Services;

use App\Models\IndikatorPenilaian;

class HasilPenilaianService
{
    protected $daftarPenilaian;
    protected $infoPenilaian;
    protected $daftarIndikator;

    public function __construct(array $daftarPenilaian, array $infoPenilaian, array $daftarIndikator)
    {
        $this->daftarPenilaian = $daftarPenilaian;
        $this->infoPenilaian = $infoPenilaian;
        $this->daftarIndikator = $daftarIndikator;
    }

    public function hitungNilaiAkhir()
    {
        if ($this->infoPenilaian['metode'] === 'aritmatika') {
            return $this->hitungAritmatika();
        } else {
            return $this->hitungProporsional();
        }
    }

    protected function hitungProporsional()
    {
        $Nilai = [];

        foreach ($this->daftarPenilaian as $dinilai) {
            $dataDinilai = [
                'dinilai' => $dinilai[0]['dinilai'],
                'nilai' => []
            ];

            $indikatorData = [];

            foreach ($dinilai as $daftarNilai) {
                foreach ($daftarNilai['log_nilai'] as $logNilai) {
                    $indikatorId = $logNilai['pertanyaan']['daftar_pertanyaan']['indikator_penilaian_id'];
                    $indikator = IndikatorPenilaian::find($indikatorId)->indikator ?? 'Unknown';
                    $role = $daftarNilai['role_penilai'];
                    $nilai = $logNilai['nilai'];

                    if (!isset($indikatorData[$indikator])) {
                        $indikatorData[$indikator] = [
                            'id' => $indikatorId,
                            'total_nilai' => 0,
                            'total_inputan' => 0,
                            'rata_rata' => 0,
                            'atasan' => ['total_nilai' => 0, 'jumlah_penilai' => 0, 'rata_rata' => 0],
                            'sebaya' => ['total_nilai' => 0, 'jumlah_penilai' => 0, 'rata_rata' => 0],
                            'bawahan' => ['total_nilai' => 0, 'jumlah_penilai' => 0, 'rata_rata' => 0],
                            'diri sendiri' => ['total_nilai' => 0, 'jumlah_penilai' => 0, 'rata_rata' => 0]
                        ];
                    }

                    $indikatorData[$indikator][$role]['total_nilai'] += $nilai;
                    $indikatorData[$indikator][$role]['jumlah_penilai']++;
                }
            }

            foreach ($indikatorData as $indikator => &$data) {
                foreach (['atasan', 'sebaya', 'bawahan', 'diri sendiri'] as $role) {
                    if ($data[$role]['jumlah_penilai'] > 0) {
                        $data[$role]['rata_rata'] = $data[$role]['total_nilai'] / $data[$role]['jumlah_penilai'];
                    }
                }

                $bobotAtasan = $this->infoPenilaian['atasan'];
                $bobotSebaya = $this->infoPenilaian['sebaya'];
                $bobotBawahan = $this->infoPenilaian['bawahan'];
                $bobotDiriSendiri = $this->infoPenilaian['diriSendiri'];

                $jumlahInputanAtasan = $data['atasan']['jumlah_penilai'];
                $jumlahInputanSebaya = $data['sebaya']['jumlah_penilai'];
                $jumlahInputanBawahan = $data['bawahan']['jumlah_penilai'];
                $jumlahInputanDiriSendiri = $data['diri sendiri']['jumlah_penilai'];

                $nilaiAkhir = 0;
                $totalBobot = 0;

                if ($jumlahInputanAtasan > 0) {
                    $nilaiAkhir += $data['atasan']['rata_rata'] * $bobotAtasan;
                    $totalBobot += $bobotAtasan;
                }

                if ($jumlahInputanSebaya > 0) {
                    $nilaiAkhir += $data['sebaya']['rata_rata'] * $bobotSebaya;
                    $totalBobot += $bobotSebaya;
                }

                if ($jumlahInputanBawahan > 0) {
                    $nilaiAkhir += $data['bawahan']['rata_rata'] * $bobotBawahan;
                    $totalBobot += $bobotBawahan;
                }

                if ($jumlahInputanDiriSendiri > 0) {
                    $nilaiAkhir += $data['diri sendiri']['rata_rata'] * $bobotDiriSendiri;
                    $totalBobot += $bobotDiriSendiri;
                }

                if ($totalBobot > 0) {
                    $nilaiAkhir /= $totalBobot;
                } else {
                    $nilaiAkhir = 0;
                }

                $data['nilai_akhir'] = round($nilaiAkhir, 1);
                $data['total_nilai'] = array_sum(array_column($data, 'total_nilai'));
                $data['total_inputan'] = array_sum(array_column($data, 'jumlah_penilai'));
                $data['rata_rata'] = $data['total_inputan'] > 0 ? $data['total_nilai'] / $data['total_inputan'] : 0;

                $dataDinilai['nilai'][$indikator] = $data;
            }

            if ($this->infoPenilaian['jumlahIndikator'] > 0) {
                $rataRataTotal = array_sum(array_column($dataDinilai['nilai'], 'nilai_akhir')) / $this->infoPenilaian['jumlahIndikator'];
            } else {
                $rataRataTotal = 0;
            }
            $dataDinilai['rata_rata_total'] = round($rataRataTotal, 1);

            if (!empty($dataDinilai['nilai'])) {
                $Nilai[] = $dataDinilai;
            }
        }

        return $Nilai;
    }

    protected function hitungAritmatika()
    {
        $Nilai = [];

        foreach ($this->daftarPenilaian as $dinilai) {
            $dataDinilai = [
                'dinilai' => $dinilai[0]['dinilai'],
                'nilai' => []
            ];

            $indikatorData = [];

            foreach ($dinilai as $daftarNilai) {
                foreach ($daftarNilai['log_nilai'] as $logNilai) {
                    $indikatorId = $logNilai['pertanyaan']['daftar_pertanyaan']['indikator_penilaian_id'];
                    $indikator = IndikatorPenilaian::find($indikatorId)->indikator ?? 'Unknown';
                    $role = $daftarNilai['role_penilai'];
                    $nilai = $logNilai['nilai'];

                    if (!isset($indikatorData[$indikator])) {
                        $indikatorData[$indikator] = [
                            'id' => $indikatorId,
                            'total_nilai' => 0,
                            'total_inputan' => 0,
                            'rata_rata' => 0,
                            'atasan' => ['total_nilai' => 0, 'jumlah_penilai' => 0, 'rata_rata' => 0],
                            'sebaya' => ['total_nilai' => 0, 'jumlah_penilai' => 0, 'rata_rata' => 0],
                            'bawahan' => ['total_nilai' => 0, 'jumlah_penilai' => 0, 'rata_rata' => 0],
                            'diri sendiri' => ['total_nilai' => 0, 'jumlah_penilai' => 0, 'rata_rata' => 0]
                        ];
                    }

                    if ($role !== 'diri sendiri') {
                        $indikatorData[$indikator]['total_nilai'] += $nilai;
                        $indikatorData[$indikator]['total_inputan']++;
                    }

                    $indikatorData[$indikator][$role]['total_nilai'] += $nilai;
                    $indikatorData[$indikator][$role]['jumlah_penilai']++;
                }
            }

            foreach ($indikatorData as $indikator => &$data) {
                foreach (['atasan', 'sebaya', 'bawahan', 'diri sendiri'] as $role) {
                    if ($data[$role]['jumlah_penilai'] > 0) {
                        $data[$role]['rata_rata'] = round($data[$role]['total_nilai'] / $data[$role]['jumlah_penilai'], 1);
                    }
                }

                $data['rata_rata'] = $data['total_inputan'] > 0 ? $data['total_nilai'] / $data['total_inputan'] : 0;

                $dataDinilai['nilai'][$indikator] = [
                    'id' => $data['id'],
                    'nilai_akhir' => round($data['rata_rata'], 1),
                    'total_nilai' => $data['total_nilai'],
                    'jumlah_penilai' => $data['total_inputan'],
                    'atasan' => $data['atasan'],
                    'sebaya' => $data['sebaya'],
                    'bawahan' => $data['bawahan'],
                    'diri sendiri' => $data['diri sendiri']
                ];
            }

            if ($this->infoPenilaian['jumlahIndikator'] > 0) {
                $rataRataTotal = array_sum(array_column($dataDinilai['nilai'], 'nilai_akhir')) / $this->infoPenilaian['jumlahIndikator'];
            } else {
                $rataRataTotal = 0;
            }
            $dataDinilai['rata_rata_total'] = round($rataRataTotal, 1);

            if (!empty($dataDinilai['nilai'])) {
                $Nilai[] = $dataDinilai;
            }
        }

        return $Nilai;
    }
}
