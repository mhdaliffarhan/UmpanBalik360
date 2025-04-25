<x-slot name="breadcumb">
    <li class="breadcrumb-item text-md">
        <a class="opacity-5 text-dark" href="{{ Route('tim-kerja') }}">Tim Kerja</a>
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
                        <h5>Buat Tim Kerja</h5>
                        <h6 class="mb-4">(Langkah {{ $formStep }}/3) </h6>
                    </div>
                </div>
                <div class="card-body pt-0">
                    @if ($formStep == 1)
                        <!-- Formulir Langkah 1 -->
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="row">
                                @error('*')
                                    <div class="col-12">
                                        @include('components.error-notification')
                                    </div>
                                @enderror
                                @include('components.form-deskripsi-tim')
                                <div class="d-flex flex-row-reverse">
                                    <button wire:click="stepSelanjutnya"
                                        class="btn bg-gradient-primary">Selanjutnya</button>
                                </div>
                            </div>
                        </div>
                    @elseif ($formStep == 2)
                        <!-- Formulir Langkah 2 -->
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="row">
                                @error('*')
                                    <div class="col-12">
                                        @include('components.error-notification')
                                    </div>
                                @enderror
                                @include('components.form-buat-struktur')
                            </div>
                            <div class="d-flex flex-row justify-content-between">
                                <button wire:click="stepSebelumnya" class="btn bg-gradient-primary">Kembali</button>
                                <button wire:click="stepSelanjutnya"
                                    class="btn bg-gradient-primary">Selanjutnya</button>
                            </div>
                        </div>
                    @elseif ($formStep == 3)
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="row">
                                @error('*')
                                    <div class="col-12">
                                        @include('components.error-notification')
                                    </div>
                                @enderror
                                @include('components.form-buat-pertanyaan')
                            </div>
                            <div class="d-flex flex-row justify-content-between">
                                <button wire:click="stepSebelumnya" class="btn bg-gradient-primary">Kembali</button>
                                <button wire:click="saveTimKerja" class="btn bg-gradient-primary">Buat Tim</button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- livewire/buat-tim-kerja.blade.php -->


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Listening for the "swal:success" event
        window.addEventListener('swal:success', event => {
            swal("Success", event.detail.message, "success");
        });

        // Listening for the "swal:warning" event
        window.addEventListener('swal:warning', event => {
            swal("Warning", event.detail.message, "warning");
        });
    });
</script>
