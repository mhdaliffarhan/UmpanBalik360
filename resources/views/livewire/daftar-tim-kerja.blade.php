<x-slot name="breadcumb">
    <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
        {{ str_replace('-', ' ', Route::currentRouteName()) }}
    </li>
</x-slot>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between position-relative">
                            <div class="container-fluid text-center">
                                <h5 class="mb-0 font-weight-bolder">Daftar Tim Kerja</h5>
                            </div>
                            {{-- <a href="{{ route('buat-tim-kerja')}}" class="btn bg-gradient-primary btn-sm position-absolute top-0 end-0" type="button">+&nbsp; Buat Tim</a> --}}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- <div class="col-md-12 px-3 pt-3 pb-3 sidenav-footer">
                                <a href="{{ route('buat-tim-kerja')}}">
                                    <div class="card card-background move-on-hover h-100 card-background-mask-secondary">
                                        <div class="card-body pt-4">
                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                    <div class="col-md-12 d-flex justify-content-center align-items-center text-center">
                                                        <h5 class="text-white mb-0">Buat tim kerja baru</h5>
                                                        <i class="ni ni-fat-add fa-2x mb-0" style="color: #ffffff; margin-left: 10px;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div> --}}
                            <div class="col-md-3 px-3 pt-3 pb-3 sidenav-footer d-flex">
                                <a href="{{ route('buat-tim-kerja')}}" class="w-100">
                                    <div class="card card-background move-on-hover flex-grow-1">
                                        <div class="card-body pt-5 d-flex flex-column">
                                            <div class="row pt-7 pb-7 mb-auto">
                                                <div class="col-md-12 text-center">
                                                    <h5 class="text-white">Buat tim kerja baru</h5>
                                                </div>
                                                <div class="col-md-12 text-center">
                                                    <i class="ni ni-fat-add fa-2x mb-0 text-center justify-content-center align-items-center" style="color: #ffffff;"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @foreach ($daftarTimKerja as $timKerja)
                            @php
                                $deskripsi = $timKerja->deskripsi_tim;
                                $words = explode(' ', $deskripsi);
                                if (count($words) > 8) {
                                    $deskripsi = implode(' ', array_slice($words, 0, 6)) . ' ...';
                                }
                            @endphp
                            <div class="col-md-3 px-3 pt-3 pb-3 sidenav-footer d-flex">
                                <div class="card card-background move-on-hover shadow-none card-background-mask-danger flex-grow-1" id="sidenavCard">
                                    <div class="full-background" style="background-image: url('../assets/img/curved-images/white-curved.jpeg')">
                                    </div>
                                    <div class="card-body pt-2 d-flex flex-column">
                                        <div class="row mb-auto">
                                            <span class="text-white text-uppercase text-sm font-weight-bold my-2">Tim Kerja</span>
                                            <a href="/tim-kerja/{{$timKerja->id}}" class="card-title h4 d-block text-white mb-3">
                                                {{ $timKerja->nama_tim }}
                                            </a>
                                            <p class="text-white card-description mb-4">{{ $deskripsi }}</p>
                                        </div>
                                        <div class="mt-auto">
                                            <p class="text-white text-sm text-uppercase font-weight-bold">
                                                <small>{{$timKerja->anggota_tim_kerja_count}} Orang Anggota</small>   
                                            </p>
                                            <a href="/tim-kerja/{{$timKerja->id}}" class="btn btn-white btn-sm w-100" type="button">Detail</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>