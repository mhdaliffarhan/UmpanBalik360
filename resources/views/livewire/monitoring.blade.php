<x-slot name="breadcumb">
    <li class="breadcrumb-item text-md">
        <a class="opacity-5 text-dark" href="{{Route('monitoring')}}">Monitoring</a>
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
                        <h4 class=" font-weight-bolder">{{$infoPenilaian->nama_penilaian}}</h4>
                    </div>
                </div>
                <div class="card-body px-2 pt-0 pb-2">
                    <div class="row">
                        <div class="col-12 col-md-12 mb-4">
                            <h5 class="text-center">Progres Penilaian</h5>
                        </div>
                        <div class="col-12  col-md-6 mb-4">
                            <div class="chart">
                                <canvas id="chart-pie" class="chart-canvas" height="150px"></canvas>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-4">
                            <div class="row">
                                <div class="col-12 col-md-12 px-6 mb-5"> 
                                    <div class="progress-wrapper">
                                        <div class="progress-info">
                                            <div class="progress-percentage">
                                                <span class="text-sm font-weight-bold">{{$dataNilai['persen']}}%</span>
                                            </div>
                                        </div>
                                        <x-progress-bar :percentage="$dataNilai['persen']" class=""/>
                                    </div>
                                </div>
                                <div class="col-12 col-md-12 px-6 mb-4">
                                    <div class="table-responsive">
                                        <table class="table align-items-center mb-0">
                                            <thead>
                                                <tr>
                                                    <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                        Status Nilai
                                                    </td>
                                                    <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                        Jumlah
                                                    </td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="align-middle">
                                                        <p class="text-center text-xs font-weight-bold mb-0">Belum</p>
                                                    </td>
                                                    <td class="algin middle">
                                                        <p class="text-center text-xs font-weight-bold mb-0">{{ $dataNilai['belum']}}</p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="align-middle">
                                                        <p class="text-center text-xs font-weight-bold mb-0">Sudah</p>
                                                    </td>
                                                    <td class="algin middle">
                                                        <p class="text-center text-xs font-weight-bold mb-0">{{ $dataNilai['sudah']}}</p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-12 mb-4">
                            <h5 class="text-center">Data Penilaian</h5>
                        </div>
                        <div class="col-12 col-md-12 mb-4">
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                No
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Penilai</th>
                                            
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Completion
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Progress
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($daftarPenilaian as $Penilai)
                                        <tr>
                                            <td class="align-middle ">
                                                <p class="text-center text-xs font-weight-bold mb-0">{{$loop->iteration}}</p>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                  <div>
                                                    @if($Penilai['penilai']->photo_path)
                                                    <img src="{{ Str::startsWith($Penilai['penilai']->photo_path, 'http') ? $Penilai['penilai']->photo_path : asset('storage/' . $Penilai['penilai']->photo_path) }}" alt="..." class="avatar avatar-sm me-3">
                                                    @else
                                                    <img src="../assets/img/team-4.jpg" alt="Default Image" class="avatar avatar-sm me-3">
                                                    @endif
                                                  </div>
                                                  <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-xs">{{$Penilai['penilai']->name}}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{$Penilai['penilai']->email}}</p>
                                                  </div>
                                                </div>
                                              </td>
                                            <td class="algin middle">
                                                <p class="text-center text-xs font-weight-bold mb-0">{{ $Penilai->sudah}} dari {{ $Penilai->total}} telah dinilai</p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="d-flex align-items-center">
                                                    <x-progress-bar :percentage="$Penilai->persen" class="" />
                                                    <span class="me-2 text-xs px-2">{{ $Penilai->persen }} %</span>
                                                </div>
                                              </td>
                                        </tr>
                                        @endforeach
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

<!--   Core JS Files   -->
<script src="/assets/js/plugins/chartjs.min.js"></script>
<script src="/assets/js/plugins/Chart.extension.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Ambil data dari atribut Livewire
        var jumlahBelumMenilai = @json($dataNilai['belum']);
        var jumlahSudahMenilai = @json($dataNilai['sudah']);

        // Inisialisasi pie chart
        var ctx = document.getElementById('chart-pie').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Belum', 'Sudah'],
                datasets: [{
                    data: [jumlahBelumMenilai, jumlahSudahMenilai],
                    backgroundColor: ['rgb(255,49,49)', 'rgb(130,214,22)'],
                    hoverBackgroundColor: ['rgba(255,49,49,0.8)', 'rgba(130,214,22,0.8)'],
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.raw;
                                return label;
                            }
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });
    });
</script>
