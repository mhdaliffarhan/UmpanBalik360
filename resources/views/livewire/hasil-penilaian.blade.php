<x-slot name="breadcumb">
    <li class="breadcrumb-item text-md">
        <a class="opacity-5 text-dark" href="{{ Route('hasil-penilaian') }}">Hasil Penilaian</a>
    </li>
    <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
        {{ str_replace('-', ' ', Route::currentRouteName()) }}
    </li>
</x-slot>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4 px-4">
                <div class="card-header pb-0 mb-4">
                    <div class="container text-center">
                        <h4 class=" font-weight-bolder">{{ $infoPenilaian['nama_penilaian'] }}</h4>
                    </div>
                </div>
                <div class="card-body px-2 pt-0 pb-2">
                    <div class="row">
                        <div class="col-12 col-md-12 mb-4">
                            <h5 class="text-center">Hasil Penilaian</h5>
                        </div>
                        <!-- FITUR METODE -->
                        <div class="col-12 col-md-12 px-3 mb-5">
                            <h6 class="text-center">Metode Penentuan Nilai Akhir</h6>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="metode" class="form-label">Metode</label>
                                    <select wire:model="infoPenilaian.metode" class="form-select" id="metode">
                                        <option value="aritmatika">Aritmatika</option>
                                        <option value="proporsional">Proporsional</option>
                                    </select>
                                </div>
                            </div>
                            @if ($infoPenilaian['metode'] == 'proporsional')
                                <div class="row mb-2">
                                    <div class="col-md-3">
                                        <label for="atasan" class="form-label">Atasan</label>
                                        <input wire:model="infoPenilaian.atasan" type="number" min="0"
                                            max="100" class="form-control" id="atasan">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="sebaya" class="form-label">Rekan Sejawat</label>
                                        <input wire:model="infoPenilaian.sebaya" type="number" min="0"
                                            max="100" class="form-control" id="sebaya">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="bawahan" class="form-label">Bawahan</label>
                                        <input wire:model="infoPenilaian.bawahan" type="number" min="0"
                                            max="100" class="form-control" id="bawahan">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="diriSendiri" class="form-label">Diri Sendiri</label>
                                        <input wire:model="infoPenilaian.diriSendiri" type="number" min="0"
                                            max="100" class="form-control" id="diriSendiri">
                                    </div>
                                </div>
                            @endif
                            <div class="col-12 col-md-12">
                                <a wire:click='nilaiAkhir' class="btn bg-gradient-warning mb-0">Perbarui</a>
                            </div>
                        </div>
                        <!-- AKHIR FITUR METODE NILAI AKHIR -->
                        <div class="col-12 col-md-12 mb-2">
                            <h6 class="text-center">Tabel Nilai Akhir</h6>
                        </div>
                        <div class="d-flex flex-row justify-content-between position-relative mb-4">
                            <div class="d-flex justify-content-start">
                                <select wire:model="infoPenilaian.filter" class="form-select" id="metode"
                                    min="0" max="100">
                                    <option value="semua">Semua</option>
                                    <option value="atasan">Atasan</option>
                                    <option value="sebaya">Rekan sejawat</option>
                                    <option value="bawahan">Bawahan</option>
                                </select>
                            </div>
                            <button wire:click="exportExcel"
                                class="btn bg-gradient-success btn-sm position-absolute top-0 end-0">Export
                                Data</button>

                        </div>
                        <div class="col-12 col-md-12 mb-4">
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th rowspan="2"
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                No</th>
                                            <th rowspan="2"
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Nama</th>
                                            <th colspan="{{ $infoPenilaian['jumlahIndikator'] }}"
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Indkator </th>

                                            <th rowspan="2"
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Rata-rata</th>
                                        </tr>
                                        <tr>
                                            @foreach ($daftarIndikator as $indikator)
                                                <th
                                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    {{ $indikator['nama'] }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($nilai as $Dinilai)
                                            <tr>
                                                <td class="align-middle">
                                                    <p class="text-center text-xs font-weight-bold mb-0">
                                                        {{ $loop->iteration }}</p>
                                                </td>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div>
                                                            @if ($Dinilai['dinilai']['photo_path'])
                                                                <img src="{{ Str::startsWith($Dinilai['dinilai']['photo_path'], 'http') ? $Dinilai['dinilai']['photo_path'] : asset('storage/' . $Dinilai['dinilai']['photo_path']) }}"
                                                                    alt="..." class="avatar avatar-sm me-3">
                                                            @else
                                                                <img src="../assets/img/team-4.jpg"
                                                                    alt="Default Image" class="avatar avatar-sm me-3">
                                                            @endif
                                                        </div>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-xs">{{ $Dinilai['dinilai']['name'] }}
                                                            </h6>
                                                            <p class="text-xs text-secondary mb-0">
                                                                {{ $Dinilai['dinilai']['email'] }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                @foreach ($daftarIndikator as $indikator)
                                                    <td class="text-center">
                                                        <p class="text-xs text-secondary mb-0">
                                                            {{ $Dinilai['nilai'][$indikator['nama']]['nilai_akhir'] }}
                                                        </p>
                                                    </td>
                                                @endforeach
                                                <td class="text-center">
                                                    <p class="text-xs text-secondary mb-0">
                                                        {{ $Dinilai['rata_rata_total'] }}</p>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td></td>
                                            <td
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Rata-rata</td>
                                            @foreach ($daftarIndikator as $indikator)
                                                <td class="text-center">
                                                    <p class="text-xs text-secondary mb-0">
                                                        {{ $infoNilai['rerata_indikator'][$indikator['nama']] }}</p>
                                                </td>
                                            @endforeach
                                            <td class="text-center">
                                                <p class="text-xs text-secondary mb-0">
                                                    {{ $infoNilai['rerata_total'] }}</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('livewire:load', function() {
        window.addEventListener('swal:warning', event => {
            Swal.fire("Warning", event.detail.message, "warning");
        });
        window.addEventListener('swal:success', event => {
            Swal.fire("Success", event.detail.message, "success");
        });
    });
</script>
