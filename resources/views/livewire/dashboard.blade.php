<x-slot name="breadcumb">
  <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
      {{ str_replace('-', ' ', Route::currentRouteName()) }}
  </li>
</x-slot>

 <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-0">
              <div class="row">
                <div class="col-auto mx-2">
                  <div class="avatar avatar-xl position-relative">
                    @if($user->photo_path)
                    <img src="{{ Str::startsWith($user->photo_path, 'http') ? $user->photo_path : asset('storage/' . $user->photo_path) }}" alt="..." class="w-100 border-radius-lg shadow-sm">
                    @else
                    <img src="../assets/img/team-4.jpg" alt="Default Image" class="avatar avatar-sm me-3">
                    @endif
                  </div>
                </div>
                <div class="col-auto my-auto">
                  <div class="h-100">
                    <h5 class="mb-1">{{$user->name}}</h5>
                    <p class="mb-0 font-weight-bold text-sm">{{$user->email}}</p>
                  </div>
              </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-4 text-start mx-0 px-3">
                  <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                    <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
                <div class="col-5 px-0">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Penilaian Berlangsung</p>
                  </div>
                </div>
                <div class="col-3">
                  <div class="number">
                    <h3 class="font-weight-bolder text-capitalize mb-0 text-center">
                    {{count($daftarPenilaian)}}
                    </h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-4 text-start">
                  <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                    <i class="ni ni-check-bold text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
                <div class="col-5 px-0">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Kuesioner Diisi</p>
                  </div>
                </div>
                <div class="col-3">
                  <div class="number">
                    <h3 class="font-weight-bolder text-capitalize mb-0 text-center">
                      {{ $jumlahKuesionerDiisi }}
                    </h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @if ($daftarTimKerja->isEmpty() )
      <div class="row mt-4">
        <div class="col-lg-12 mb-10">
            <div class="card h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title text-center mb-4">Anda tidak tergabung kedalam tim kerja manapun.</h5>
                    <p class="card-text text-center mb-4">Buat tim kerja Anda sekarang!</p>
                    <a href="{{ route('buat-tim-kerja')}}" class="btn bg-gradient-danger">Buat Tim Kerja</a>
                </div>
            </div>
        </div>
      </div>
      @else
      <div class="row mt-4">
      @if($daftarPenilaianSelesai && !($daftarPenilaianSelesai == null ))
      <div class="col-lg-auto mb-4">
        <div class="card">
          <div class="card-header pb-0 text-center">
            <h6>Hasil Penilaian</h6>
            <select wire:model="selectedData" id="selectData" wire:change="changeData($event.target.value)" class="form-select">
              <option value="0" selected>Pilih penilaian...</option>
              @foreach ($daftarPenilaianSelesai as $penilaian)
                  <option value="{{ $penilaian['id'] }}">{{ $penilaian['nama_penilaian'] }}</option>
              @endforeach
            </select>

          </div>
          <div class="card-body p-3">
            <div class="chart">
              <canvas id="chart-radar" class="chart-canvas" height="300px"></canvas>
            </div>
          </div>
        </div>
      </div>
      @endif
      @if ($daftarPenilaian && !($daftarPenilaian == null ))
      <div class="col-lg-8 mb-4">
        <div class="card">
          <div class="card-header pb-0 text-center">
            <h6>Penilaian Berlangsung</h6>
          </div>
          <div class="card-body p-3">
            <div class="row">
              <div class="col-12 col-md-12 mb-4">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Penilaian</th>  
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Proses</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Batas Waktu</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                          @foreach ($daftarPenilaian as $penilaian)
                            <tr>
                              <td class="align-middle">
                                  <p class="text-center text-xs font-weight-bold mb-0">{{$loop->iteration}}</p>
                              </td>
                              <td>
                                <div class="d-flex px-2 py-1">
                                  <a href="/penilaian/{{$penilaian['id']}}">
                                  <div>
                                  <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-xs">{{$penilaian['nama_penilaian']}}</h6>
                                    <p class="text-xs text-secondary mb-0">{{$penilaian['struktur']['timkerja']['nama_tim']}}</p>
                                  </div>
                                </div>
                                </a>
                              </td>
                              <td class="align-middle">
                                <div class="col-12 col-md-12"> 
                                  <div class="progress-wrapper">
                                      <div class="progress-info">
                                        <div class="progress-percentage mb-2 text-center">
                                          <p class="text-xs text-secondary mb-0">{{$penilaian['dataNilaiUser']['sudah']}} dari {{$penilaian['dataNilaiUser']['total']}} sudah dinilai ({{$penilaian['dataNilaiUser']['persen']}}%)</p>
                                        </div>
                                      </div>
                                      <x-progress-bar :percentage="$penilaian['dataNilaiUser']['persen']" class="w-100" />
                                  </div>
                              </div>
                              </td>
                              <td class="algin middle">
                                  <p class="text-center text-xs font-weight-bold mb-0">{{$penilaian['deadlinePenilaian']}}</p>
                              </td>
                              <td class="d-flex justify-content-center align-items-center mb-0 py-3">
                                <a href="/penilaian/{{$penilaian['id']}}" class="btn bg-gradient-danger btn-sm disable mb-0" role="button" aria-pressed="true">Nilai</a>
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
      @endif
      </div>
      @endif
    </div>
  </main>

  <!--   Core JS Files   -->
  <script src="/assets/js/plugins/chartjs.min.js"></script>
  <script src="/assets/js/plugins/Chart.extension.js"></script>
  <script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('dataChartUpdated', dataChart => {
            var ctx = document.getElementById('chart-radar').getContext('2d');
            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: dataChart.labels,
                    datasets: [{
                        label: 'Orang Lain',
                        data: dataChart.nilai_orang_lain,
                        fill: true,
                        backgroundColor: 'rgba(37,47,64, 0.2)',
                        borderColor: 'rgb(37,47,64)',
                        pointBackgroundColor: 'rgb(37,47,64)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgb(37,47,64)'
                    }, {
                        label: 'Diri Sendiri',
                        data: dataChart.nilai_diri_sendiri,
                        fill: true,
                        backgroundColor: 'rgba(255,49,49, 0.2)',
                        borderColor: 'rgb(255,49,49)',
                        pointBackgroundColor: 'rgb(255,49,49)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgb(255,49,49)'
                    }]
                },
                options: {
                  responsive: true,
                  elements: {
                      line: {
                          borderWidth: 3
                      }
                  },
                  scales: {
                      r: {
                          angleLines: {
                              display: false
                          },
                          min: 0,
                          max: 10
                      }
                  }
                }
            });
        });
    });
</script>

