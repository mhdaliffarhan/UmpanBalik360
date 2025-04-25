<x-slot name="breadcumb">
    <li class="breadcrumb-item text-md">
        <a class="opacity-5 text-dark" href="{{Route('tim-kerja')}}">Tim Kerja</a>
    </li>
    <li class="breadcrumb-item text-md">
        <a class="opacity-5 text-dark" href="/tim-kerja/{{$idTimKerja}}">Detail Tim Kerja</a>
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
                        <h5>Edit Tim Kerja</h5>
                    </div>
                </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="row">
                            @include('components.form-deskripsi-tim')
                            <div class="d-flex flex-row-reverse">
                                <button wire:click="simpan" class="btn bg-gradient-primary">Simpan Perubahan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>