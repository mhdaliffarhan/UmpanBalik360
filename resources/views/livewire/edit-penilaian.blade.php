<x-slot name="breadcumb">
    <li class="breadcrumb-item text-md">
        <a class="opacity-5 text-dark" href="{{Route('penilaian')}}">Penilaian</a>
    </li>
    <li class="breadcrumb-item text-md">
        <a class="opacity-5 text-dark" href="/penilaian/{{$idPenilaian}}">Detail Penilaian</a>
    </li>
    <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
        {{ str_replace('-', ' ', Route::currentRouteName()) }}
    </li>
</x-slot>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4 px-4">
                <div class="card-header pb-0">
                    <div class="container text-center">
                        <h5>Edit Penilaian</h5>
                    </div>
                </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="namaTimKerja" class="form-label">Nama Penilaian</label>
                                <input wire:model="penilaian.namaPenilaian" type="text" class="form-control" id="deskripsiTimKerja" ></input>
                            </div>
                            <div class="col-6 col-md-12 px-3 mb-4">
                              <h6 class="text-center">Waktu Penilaian</h6>
                              <div class="row">
                                <div class="col-md-6">
                                    <label for="mulai" class="form-label">Mulai</label>
                                    <input wire:model="penilaian.mulai" type="date" class="form-control" id="mulai">
                                </div>
                                <div class="col-md-6">
                                    <label for="selesai" class="form-label">Selesai</label>
                                    <input wire:model="penilaian.selesai" type="date" class="form-control" id="selesai">
                                </div>
                              </div>
                            </div>
                            <div class="d-flex flex-row-reverse">
                                <button wire:click="simpan" class="btn bg-gradient-primary">Simpan perubahan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
