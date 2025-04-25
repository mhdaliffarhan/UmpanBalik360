@foreach ($daftarPenilaian as $penilaian)
<div class="col-md-3 px-2 pt-1 pb-1 sidenav-footer">
  <div class="card card-background shadow-none card-background-mask-warning" id="sidenavCard">
      <div class="full-background" style="background-image: url('../assets/img/curved-images/white-curved.jpeg')">
      </div>
      <div class="card-body pt-2">
          <div class="row">
              <div class="col-md-12 d-flex flex-row justify-content-between">
                  <span class="text-white text-uppercase text-sm font-weight-bold my-2">{{ $penilaian->struktur->nama_struktur }}</span>
                  {{-- <span class="text-white text-uppercase text-sm text-uppercase font-weight-bold my-2">
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
                  </span> --}}
              </div>
              <a href='/penilaian/{{$penilaian->id}}' class="card-title h4 d-block text-white mb-3">
                {{ $penilaian->nama_penilaian }}
              </a>
                <p class="text-white text-sm font-weight-bold mb-3">
                    {{$penilaian->telahDinilai}} dari {{$penilaian->totalDinilai}} telah dinilai  
                </p>
                <p class="text-white text-sm font-weight-bold">
                    {{$penilaian->jarakDeadline}} hari lagi  
                </p>
          </div>
      </div>
  </div>
</div>
@endforeach