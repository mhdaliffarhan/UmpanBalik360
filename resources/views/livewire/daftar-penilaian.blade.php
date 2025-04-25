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
                                <h5 class="mb-0 font-weight-bolder">Daftar Penilaian</h5>
                            </div>
                            {{-- <a href="{{ route('buat-penilaian')}}" class="btn bg-gradient-primary btn-sm position-absolute top-0 end-0" type="button">+&nbsp; Buat Penilaian</a> --}}
                        </div>
                    </div>
                    <div class="card-body ">
                        <div class="row">
                            {{-- <div class="col-md-8 px-3 pt-3 pb-3 sidenav-footer">
                                
                            <a href="{{ route('buat-penilaian')}}" class="btn btn-sm bg-gradient-warning btn-lg active w-100" role="button" aria-pressed="true">
                                <span class="btn-inner--icon"><i class="ni ni-fat-add"></i></span>
                                <span class="btn-inner--text">Buat Penilaian</span>
                            </a>
                                <a href="{{ route('buat-penilaian')}}">
                                  <div class="card card-background move-on-hover h-100 card-background-mask-secondary">
                                    <div class="card-body pt-4">
                                        <div class="row">
                                            <div class="col-md-12 d-flex justify-content-center align-items-center text-center">
                                                <h5 class="text-white mb-0">Buat penilaian baru</h5>
                                                <i class="ni ni-fat-add fa-2x mb-0" style="color: #ffffff; margin-left: 10px;"></i>
                                            </div>
                                        </div>
                                    </div>
                                  </div>
                                </a>
                            </div> --}}
                            <div class="col-md-6 px-3 pt-3 pb-3 sidenav-footer">
                                <a href="{{ route('buat-penilaian')}}">
                                  <div class="card card-background move-on-hover h-100">
                                    <div class="card-body pt-6">
                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                                <h5 class="text-white">Buat penilaian baru</h5>
                                            </div>
                                            <div class="col-md-12 text-center">
                                                <i class="ni ni-fat-add fa-2x mb-0 text-center justify-content-center align-items-center" style="color: #ffffff;"></i>
                                            </div>
                                        </div>
                                    </div>
                                  </div>
                                </a>
                              </div>
                            @foreach ($daftarPenilaian as $penilaian)
                            @if ($penilaian->jarakDeadline >= 0)
                                <div class="col-md-6 px-3 pt-3 pb-3 sidenav-footer">
                                    <div class="card card-background move-on-hover shadow-none card-background-mask-warning" id="sidenavCard">
                                        <div class="full-background" style="background-image: url('../assets/img/curved-images/white-curved.jpeg')">
                                        </div>
                                        <div class="card-body pt-2">
                                            <div class="row">
                                                <div class="col-md-12 d-flex flex-row justify-content-between">
                                                    <span class="text-white text-uppercase text-sm font-weight-bold my-2">{{ $penilaian->struktur->timKerja->nama_tim }}</span>
                                                    <span class="text-white text-uppercase text-sm text-uppercase font-weight-bold my-2">
                                                        @php
                                                            $today = now();
                                                            if ($penilaian->jarakDeadline == 0) {
                                                                $status = "Hari Ini";
                                                            } else {
                                                                $status = $penilaian->jarakDeadline . " Hari Lagi";
                                                            }
                                                            echo $status;
                                                        @endphp
                                                    </span>
                                                </div>
                                                <a href="/penilaian/{{$penilaian->id}}" class="card-title h4 d-block text-white mb-3">
                                                    {{ $penilaian->nama_penilaian }}
                                                </a>
                                                <p class="text-white text-sm text-uppercase font-weight-bold">
                                                    {{$penilaian->telahDinilai}} DARI {{$penilaian->totalDinilai}} DINILAI   
                                                </p>
                                                <a href="/penilaian/{{$penilaian->id}}" class="btn btn-white btn-sm w-100" type="button">Detail</a>        
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-md-6 px-3 pt-3 pb-3 sidenav-footer">
                                    <div class="card card-background shadow-none card-background-mask-dark" id="sidenavCard">
                                        <div class="full-background" style="background-image: url('../assets/img/curved-images/white-curved.jpeg')">
                                        </div>
                                        <div class="card-body pt-2">
                                            <div class="row">
                                                <div class="col-md-12 d-flex flex-row justify-content-between">
                                                    <span class="text-white text-uppercase text-sm font-weight-bold my-2">{{ $penilaian->struktur->timKerja->nama_tim }}</span>
                                                    <span class="text-white text-uppercase text-sm text-uppercase font-weight-bold my-2">
                                                        @php
                                                            $today = now();
                                                            if ($today < $penilaian->waktu_mulai) {
                                                                $status = "Belum Mulai";
                                                            } elseif ($today >= $penilaian->waktu_mulai && $today <= $penilaian->waktu_selesai) {
                                                                $status = "Berlangsung";
                                                            } else {
                                                                $status = "Selesai";
                                                            }
                                                            echo $status;
                                                        @endphp
                                                    </span>
                                                </div>
                                                <a href="/penilaian/{{$penilaian->id}}" class="card-title h4 d-block text-white mb-3">
                                                    {{ $penilaian->nama_penilaian }}
                                                </a>
                                                <p class="text-white text-sm text-uppercase font-weight-bold">
                                                    {{$penilaian->telahDinilai}} DARI {{$penilaian->totalDinilai}} DINILAI   
                                                </p>
                                                <a href="/penilaian/{{$penilaian->id}}" class="btn btn-white btn-sm w-100" type="button">Detail</a>        
                                            </div>
                                        </div>
                                    </div>
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
