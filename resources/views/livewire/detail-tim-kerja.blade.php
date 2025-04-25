<x-slot name="breadcumb">
    <li class="breadcrumb-item text-md">
        <a class="opacity-5 text-dark" href="{{ Route('tim-kerja') }}">Tim Kerja</a>
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
                    <div class="d-flex flex-row justify-content-between position-relative">
                        <div class="container-fluid text-center">
                            <h4 class=" font-weight-bolder">{{ $infoTimKerja['nama_tim'] }}</h4>
                        </div>
                        @if ($userRole == 'admin')
                            <a href="/edit-tim-kerja/{{ $idTimKerja }}"
                                class="btn bg-gradient-primary btn-sm position-absolute top-0 end-0 d-none d-md-inline-block"
                                type="button">Edit Tim</a>
                        @endif
                    </div>
                </div>
                <div class="card-body px-2 pt-0 pb-2">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <p class="text-center">{{ $infoTimKerja['deskripsi_tim'] }}</p>
                        </div>
                        @include('components.card-daftar-penilaian')
                        @if ($userRole == 'admin')
                            <div class="nav-wrapper position-relative mb-4 mt-4">
                                <ul class="nav nav-pills nav-fill p-1" role="tablist">
                                    <li class="nav-item">
                                        <button
                                            class="nav-link mb-0 px-0 py-1 {{ $activeTab == 'struktur-tim-kerja' ? 'active btn btn-white h-100' : '' }}"
                                            wire:click="changeTab('struktur-tim-kerja')" role="tab"
                                            aria-controls="struktur-tim-kerja"
                                            aria-selected="{{ $activeTab == 'struktur-tim-kerja' ? 'true' : 'false' }}">
                                            Struktur Tim Kerja
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button
                                            class="nav-link mb-0 px-0 py-1 {{ $activeTab == 'indikator-penilaian' ? 'active btn btn-white h-100' : '' }}"
                                            wire:click="changeTab('indikator-penilaian')" role="tab"
                                            aria-controls="indikator-penilaian"
                                            aria-selected="{{ $activeTab == 'indikator-penilaian' ? 'true' : 'false' }}">
                                            Indikator Penilaian
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        @endif

                        <!-- ISI NAV STRUKTUR TIM KERJA  -->
                        <div class="col-md-12 tab-content">
                            <div class="tab-pane fade show {{ $activeTab == 'struktur-tim-kerja' ? 'active' : '' }}"
                                id="struktur-tim-kerja" role="tabpanel" aria-labelledby="struktur-tim-kerja-tab">
                                <!-- Konten Struktur Tim Kerja -->
                                <div class="d-flex flex-row justify-content-between position-relative mb-4">
                                    <div class="container-fluid text-center">
                                        <h5>Struktur Tim Kerja</h5>
                                    </div>
                                    @if ($userRole == 'admin')
                                        <a href="/buat-struktur/{{ $idTimKerja }}"
                                            class="btn bg-gradient-primary btn-sm position-absolute top-0 end-0"
                                            type="button">Buat Struktur Baru</a>
                                    @endif
                                </div>
                                <div class="col-md-12 mb-4">
                                    <select wire:model="selectedStruktur" class="w-100 form-select">
                                        @foreach ($daftarStruktur as $item)
                                            <option value="{{ $item }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Menampilkan Struktur yang Dipilih -->
                                <div class="row">
                                    @if (!empty($jabatanStruktur))
                                        @foreach ($jabatanStruktur as $struktur)
                                            @if ($struktur['nama_struktur'] == $selectedStruktur)
                                                <div class="col-md-12 mb-4">
                                                    <h5 class="text-center">{{ $struktur['nama_struktur'] }}</h5>
                                                    @php
                                                        $currentLevel = null;
                                                    @endphp
                                                    @foreach ($struktur['jabatan'] as $jabatan)
                                                        @if ($jabatan['level'] != $currentLevel)
                                                            <!-- Baris baru untuk setiap level yang berbeda -->
                                                            @if ($currentLevel !== null)
                                                </div>
                                            @endif
                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                    <h6>Level {{ $currentLevel + 1 }}</h6>
                                                </div>
                                            </div>
                                            <div class="row justify-content-center mb-4">
                                                @php
                                                    $currentLevel = $jabatan['level'];
                                                @endphp
                                        @endif
                                        <div class="col-md-4 mb-4 mx-auto">
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <h6 class="card-subtitle mb-2 text-muted">
                                                        {{ $jabatan['nama_jabatan'] }}</h6>
                                                    <p class="card-text mt-2">
                                                        @foreach ($jabatan['pejabat'] as $key => $pejabat)
                                                            {{ $pejabat }}
                                                            @if (!$loop->last)
                                                                <br>
                                                            @endif
                                                        @endforeach
                                                    </p>
                                                    @if ($currentLevel > 2)
                                                        <p class="card-text">Atasan: {{ $jabatan['atasan'] }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @endif
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- ISI NAV INDIKATOR PENILAIAN  -->
                    <div class="tab-pane fade show {{ $activeTab == 'indikator-penilaian' ? 'active' : '' }}"
                        id="indikator-penilaian" role="tabpanel" aria-labelledby="indikator-penilaian-tab">
                        <!-- Konten Indikator Penilaian -->
                        <div class="col-md-12 mb-3 text-center">
                            <h6>Indikator Penilaian</h6>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="row">
                                @foreach ($indikatorPenilaian as $key => $indikator)
                                    <div class="col-md-12 mb-3">
                                        <label for="indikator{{ $key }}" class="form-label">Indikator
                                            {{ $key + 1 }}</label>
                                        <input wire:model="indikatorPenilaian.{{ $key }}.indikator"
                                            type="text" class="form-control" id="indikator{{ $key }}">
                                    </div>
                                    @foreach ($indikator['pertanyaan'] as $index => $pertanyaan)
                                        <div class="col-12 col-md-2 mb-2 text-center">
                                            <label for="indikator{{ $key }}pertanyaan{{ $index }}"
                                                class="form-label">Pertanyaan {{ $index + 1 }}</label>
                                        </div>
                                        <div class="col-12 col-md-10 mb-3">
                                            <div class="input-group">
                                                <input
                                                    wire:model="indikatorPenilaian.{{ $key }}.pertanyaan.{{ $index }}.pertanyaan"
                                                    type="text" class="form-control"
                                                    id="indikator{{ $key }}pertanyaan{{ $index }}">
                                                <button
                                                    wire:click="hapusPertanyaan({{ $key }}, {{ $index }})"
                                                    class="btn btn-danger mb-0">X</button>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="row justify-content-md-end">
                                        <div class="col-12 col-md-10">
                                            <button wire:click="tambahPertanyaan({{ $key }})"
                                                class="btn bg-gradient-info btn-sm w-100 mb-3">Tambah
                                                Pertanyaan</button>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="col-12 col-md-12">
                                    <button wire:click="hapusIndikator({{ $key }})"
                                        class="btn bg-gradient-danger btn-sm w-100 mb-1">Hapus Indikator</button>
                                    <button wire:click="tambahIndikator"
                                        class="btn bg-gradient-info btn-sm w-100 mb-3">Tambah Indikator</button>
                                </div>
                            </div>
                            <div class="d-flex flex-row-reverse">
                                <!-- buatkan sweetalert konfimarsi perubahan -->
                                <button wire:click="simpanPerubahanIndikator"
                                    class="btn bg-gradient-warning mb-0">Simpan Perubahan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
