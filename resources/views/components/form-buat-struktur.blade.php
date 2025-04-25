<div class="container">
  <div class="col-12 mb-4">
      <div class="form-floating">
          <input wire:model="namaStruktur" type="text" class="form-control" id="namaStruktur" placeholder="nama struktur">
          <label for="namaStruktur" class="form-label">Nama Struktur</label>
      </div>
  </div>  
  @foreach ($jabatan as $i => $level)
      @switch($i)
          @case(0)
              {{-- KETUA  --}}
              <div class="container px-0 mb-4">
                  <div class="col-12 mb-2 text-center">
                      <h6 class="font-weight-bolder mb-0">Level {{$i+1}}</h6>
                      <span>Jabatan Tertinggi</span>
                  </div>
                  <div class="col-12 mb-0">
                      <div class="form-floating mb-1">
                          <input wire:model="jabatan.{{$i}}.0.nama_jabatan" type="text" class="form-control" id="jabatan{{$i+1}}" placeholder=" ">
                          <label for="jabatan{{$i+1}}" class="form-label">Nama Jabatan</label>
                      </div>
                  </div> 
                  {{-- Use foreach loop for members --}}
                  <select wire:model="jabatan.{{$i}}.0.pejabat" class="form-select">
                      <option value="">Pilih pejabat...</option>
                      @foreach($anggotaTimKerja as $anggota)
                          <option value="{{$anggota['email']}}">{{$anggota['nama']}}</option>
                      @endforeach
                  </select>
              </div>
              @break
          @case(1)
              <div class="container px-0 mb-4">
                  <div class="col-12 mb-2 text-center">
                      <h6 class="font-weight-bolder mb-0">Level {{$i+1}}</h6>
                  </div>
                  <div class="row text-center">
                      @foreach ($level as $key => $jabatann)
                          <div class="col-md-3 mb-4">
                              <div class="form-floating mb-1">
                                  <input wire:model="jabatan.{{$i}}.{{$key}}.nama_jabatan" type="text" class="form-control text-sm" id="jabatan{{$i+1}}.{{$key}}" placeholder=" ">
                                  <label for="jabatan{{$i+1}}.{{$key}}" class="form-label">Nama Jabatan</label>
                              </div>
                              <div class="form-floating mb-1">
                                  <input wire:model="jabatan.{{$i-1}}.0.nama_jabatan" type="text"  class="form-control" id="atasan{{$i+1}}.{{$key}}" placeholder=" " disabled>
                                  <label for="atasan{{$i+1}}.{{$key}}" class="form-label">Atasan</label>
                              </div>
                              <select wire:model="jabatan.{{$i}}.{{$key}}.pejabat" multiple class="form-select">
                                  @foreach($anggotaTimKerja as $anggota)
                                      <option value="{{$anggota['email']}}">{{$anggota['nama']}}</option>
                                  @endforeach
                              </select>
                          </div>
                      @endforeach
                      <div class="col-md-1 mb-4" >
                            <div class="d-flex flex-row justify-content-between" >
                                <button wire:click="hapusJabatan({{$i}})" class="btn bg-gradient-danger h-100 mb-3">
                                    <i class="ni ni-fat-remove"></i>
                                </button>
                            </div>
                            <div class="d-flex flex-row justify-content-between" >
                                <button wire:click="tambahJabatan({{$i}})" class="btn bg-gradient-info h-100 mb-3">
                                    <i class="ni ni-fat-add"></i>
                                </button>
                            </div>
                        </div>
                  </div>
              </div>
              @break
      @default
          <div class="container px-0 mb-4">
              <div class="col-12 mb-2 text-center">
                  <h6 class="font-weight-bolder mb-0">Level {{$i+1}}</h6>
              </div>
              <div class="row text-center">
                  @foreach ($level as $key => $jabatann)
                      <div class="col-md-3 mb-2">
                          <div class="form-floating mb-1">
                              <input wire:model="jabatan.{{$i}}.{{$key}}.nama_jabatan" type="text" class="form-control" id="jabatan{{$i+1}}.{{$key}}" placeholder=" ">
                              <label for="jabatan{{$i+1}}.{{$key}}" class="form-label">Nama Jabatan</label>
                          </div>
                          <select wire:model="jabatan.{{$i}}.{{$key}}.atasan" class="form-select mb-1">
                                  <option value="">pilih atasan...</option>
                              @foreach($jabatan[$i-1] as $anu => $item)
                                  <option value="{{$item['nama_jabatan']}}">{{$item['nama_jabatan']}}</option> 
                              @endforeach
                          </select>
                          <select wire:model="jabatan.{{$i}}.{{$key}}.pejabat" multiple class="form-select">
                              @foreach($anggotaTimKerja as $anggota)
                                  <option value="{{$anggota['email']}}">{{$anggota['nama']}}</option>
                              @endforeach
                          </select>
                      </div>
                  @endforeach
                  <div class="col-md-1 mb-4">
                      <div class="d-flex flex-row justify-content-between" >
                          <button wire:click="hapusJabatan({{$i}})" class="btn bg-gradient-danger h-100 mb-3">
                            <i class="ni ni-fat-remove"></i></button>
                      </div>
                      <div class="d-flex flex-row justify-content-between" >
                          <button wire:click="tambahJabatan({{$i}})" class="btn bg-gradient-info h-100 mb-3">
                            <i class="ni ni-fat-add"></i>
                        </button>
                      </div>
                  </div>
                  
              </div>
          </div>
      @endswitch
  @endforeach

  <div class="col-12 col-md-12 mb-4">
      @if ($i > 1)
          <button wire:click="hapusLevel" class="btn bg-gradient-danger w-100 mb-1">Hapus Level</button>
      @endif
      <button wire:click="tambahLevel" class="btn bg-gradient-info w-100 mb-3">
        <i class="ni ni-fat-add"></i>
        <span>Tambah Level</span>
    </button>
  </div>

  
</div>