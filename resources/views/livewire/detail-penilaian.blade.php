<x-slot name="breadcumb">
    <li class="breadcrumb-item text-md">
        <a class="opacity-5 text-dark" href="{{Route('penilaian')}}">Penilaian</a>
    </li>
    <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
        {{ str_replace('-', ' ', Route::currentRouteName()) }}
    </li>
</x-slot>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4 px-4">
                    <div class="card-header pb-0 mb-4">
                        <div class="d-flex flex-row justify-content-between position-relative">
                            <div class="container-fluid text-center">
                                <h4 class=" font-weight-bolder">{{$infoPenilaian['nama_penilaian']}}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-2 pt-0 pb-2">
                        <div class="row justify-content-center">
                            @if ($userRole == 'admin')   
                            <div class="col-12 col-md-4">
                                <a href="/monitoring/{{$idPenilaian}}"class="btn bg-gradient-info w-100" type="button">Monitoring</a>
                            </div>
                            <div class="col-12 col-md-4">
                                <a href="/hasil-penilaian/{{$idPenilaian}}"class="btn bg-gradient-success w-100" type="button">Hasil Penilaian</a>
                            </div>      
                            <div class="col-12 col-md-4 mb-5">
                                <a href="/edit-penilaian/{{$idPenilaian}}"class="btn bg-gradient-primary w-100" type="button">Edit Penilaian</a>
                            </div>
                            @endif
                            <div class="col-12 col-md-8 px-3 mb-5"> 
                                <div class="progress-wrapper">
                                    <div class="progress-info">
                                    <div class="progress-percentage mb-2">
                                        <span class="text-sm font-weight-bold">{{$dataNilaiUser['sudah']}} dari {{$dataNilaiUser['total']}} sudah dinilai ({{$dataNilaiUser['persen']}}%)</span>
                                    </div>
                                    </div>
                                    <x-progress-bar :percentage="$dataNilaiUser['persen']" class="" />
                                </div>
                            </div>

                            <!-- Konten Daftar Penilaian -->
                            <div class="d-flex flex-row justify-content-between position-relative mb-4">
                                <div class="container-fluid text-center">
                                    <h5>Daftar Penilaian</h5>
                                </div>
                            </div>
                            @php
                                $roles = ['atasan', 'sebaya', 'bawahan', 'diri sendiri'];
                            @endphp

                            @foreach ($roles as $role)
                                @php
                                // Filter berdasarkan peran
                                if ($role == 'atasan') {
                                    $roleFilter = 'bawahan'; // Atasan mengarahkan ke bawahan
                                } elseif ($role == 'bawahan') {
                                    $roleFilter = 'atasan'; // Bawahan mengarahkan ke atasan
                                } else {
                                    $roleFilter = $role;
                                }
                    
                                $dinilaiByRole = $daftarDinilai->filter(function ($item) use ($roleFilter) {
                                    return $item['role_penilai'] == $roleFilter;
                                });
                                @endphp

                                @if ($dinilaiByRole->isNotEmpty())
                                    <div class="col-md-12 mb-4">
                                        <h6 class="text-center">{{ ucfirst($role) }}</h6>
                                    </div>

                                    <div class="row justify-content-center text-center">
                                        @foreach ($dinilaiByRole as $dinilai)
                                            <div class="col-md-2 mb-4">
                                                <div class="card move-on-hover">
                                                    <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1">
                                                        @if($dinilai->status == 'belum')
                                                            <a href="{{ route('nilai', ['id' => $dinilai->id]) }}" class="d-block">
                                                        @else
                                                            <div class="d-block">
                                                        @endif
                                                            @if($dinilai->dinilai->photo_path)
                                                                <img src="{{ Str::startsWith($dinilai->dinilai->photo_path, 'http') ? $dinilai->dinilai->photo_path : asset('storage/' . $dinilai->dinilai->photo_path) }}" alt="..." class="w-100 border-radius-lg shadow-sm">
                                                            @else
                                                                <img src="../assets/img/team-4.jpg" alt="Default Image" class="w-100 border-radius-lg shadow-sm">
                                                            @endif
                                                        @if($dinilai->status == 'belum')
                                                            </a>
                                                        @else
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="card-body pt-2">
                                                        <div class="row">
                                                            <div class="col-12 col-md-12">
                                                                @if ($dinilai->status == 'belum')
                                                                    <span class="text-gradient text-danger text-uppercase text-xs font-weight-bold my-2">belum dinilai</span>
                                                                @else
                                                                    <span class="text-gradient text-success text-uppercase text-xs font-weight-bold my-2">sudah dinilai</span>
                                                                @endif
                                                            </div>
                                                            <div class="col-12 col-md-12">
                                                                @if($dinilai->status == 'belum')
                                                                    <a href="{{ route('nilai', ['id' => $dinilai->id]) }}" class="card-description text-darker">
                                                                        <small> {{ $dinilai->dinilai->name }} </small>
                                                                    </a>
                                                                @else
                                                                <a class="card-description text-darker">
                                                                    <small> {{ $dinilai->dinilai->name }} </small>
                                                                </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>