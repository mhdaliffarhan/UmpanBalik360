
<div class="container">
    <div class="col-12 mb-3">
        <label for="namaTimKerja" class="form-label">Nama Tim</label>
        <input wire:model="namaTimKerja" type="text" class="form-control" id="namaTimKerja">
    </div>
    <div class="col-12 mb-4">
        <label for="deskripsiTimKerja" class="form-label">Deskripsi Tim <span>(opsional)</span></label>
        <textarea wire:model="deskripsiTimKerja" class="form-control" id="deskripsiTimKerja" aria-label="Dengan textarea"></textarea>
    </div>
    <div class="col-12 mb-3">
        <h6>Anggota Tim Kerja</h6>
    </div>

    <!-- Upload EXCEL -->
    @include('components.impor')
    

    <!-- Looping untuk mengambil anggota tim kerja yang diimpor -->
    @foreach ($anggotaTimKerja as $key => $anggota)
        @if ($key == 0)
            <div class="row text-center">
                <div class="col-12 col-md-2 mb-2">                                            
                    <label for="anggota{{$key}}" class="form-label">Anggota {{$key + 1}}</label>
                </div>
                <div class="col-12 col-md-10 mb-3">
                    <div class="input-group">
                        <input wire:model="anggotaTimKerja.{{$key}}.email" type="text" class="form-control " id="anggota{{$key}}" disabled>
                        <input wire:model="anggotaTimKerja.{{$key}}.nama" type="text" class="form-control" id="namaAnggota{{$key}}" disabled>
                        <select wire:model="anggotaTimKerja.{{$key}}.role" class="form-select">
                            <option value="admin" selected>Admin</option>
                        </select>
                        <button class="btn btn-danger mb-0">X</button>
                    </div>
                </div>
            </div>
        @else
            <div class="row text-center">
                <div class="col-12 col-md-2 mb-2">                                            
                    <label for="anggota{{$key}}" class="form-label">Anggota {{$key + 1}}</label>
                </div>
                <div class="col-12 col-md-10 mb-3">
                    <div class="input-group">
                        <input wire:model="anggotaTimKerja.{{$key}}.email" type="text" class="form-control " id="anggota{{$key}}">
                        <input wire:model="anggotaTimKerja.{{$key}}.nama" type="text" class="form-control" id="namaAnggota{{$key}}">
                        <select wire:model="anggotaTimKerja.{{$key}}.role" class="form-select">
                            <option value="anggota" selected>Anggota</option>
                            <option value="admin">Admin</option>
                        </select>
                        <button wire:click="hapusAnggota({{$key}})" class="btn btn-danger mb-0">X</button>
                    </div>
                </div>  
            </div>
        @endif
    @endforeach

    <div class="row justify-content-md-end">
        <div class="col-12 col-md-10">
            <button wire:click="tambahInputAnggota" class="btn bg-gradient-info w-100 mb-3">Tambah Anggota</button>
        </div>
    </div>
</div>