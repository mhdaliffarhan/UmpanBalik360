<x-slot name="breadcumb">
    <li class="breadcrumb-item text-md">
        <a class="opacity-5 text-dark" href="{{Route('dasbor')}}">Beranda</a>
    </li>
    <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
        {{ str_replace('-', ' ', Route::currentRouteName()) }}
    </li>
</x-slot>

<div>
    <div class="container-fluid pb-4">
        <div class="page-header min-height-300 border-radius-xl mt-4"
            style="background-image: url('../assets/img/back-ground/login.jpg'); background-position-y: 50%;">
            <span class="mask bg-gradient-primary opacity-3"></span>
        </div>
        <div class="card card-body blur shadow-blur mx-4 mt-n6">
            <div class="row gx-4">
                <div class="col-auto">
                    <div class="avatar avatar-xl position-relative">
                        @if($user->photo_path)
                            <img src="{{ Str::startsWith($user->photo_path, 'http') ? $user->photo_path : asset('storage/' . $user->photo_path) }}" alt="..." class="w-100 border-radius-lg shadow-sm">
                        @else
                            <img src="../assets/img/team-4.jpg" alt="Default Image" class="w-100 border-radius-lg shadow-sm">
                        @endif
                        {{-- <input type="file" id="image" wire:model="image" class="d-none">
                        <label for="image" class="btn btn-sm btn-icon-only bg-gradient-light position-absolute bottom-0 end-0 mb-n2 me-n2">
                            <i class="fa fa-pen top-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Ganti Foto"></i>
                        </label> --}}
                    </div>
                </div>
                <div class="col-auto my-auto">
                    <div class="h-100">
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <p class="mb-0 font-weight-bold text-sm">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if ($showSuccesNotification)
        <div class="container-fluid">
            <div wire:model="showSuccessNotification" class="mt-3 alert alert-primary alert-dismissible fade show" role="alert">
                <span class="alert-icon text-white"><i class="ni ni-like-2"></i></span>
                <span class="alert-text text-white">{{ $notificationMessage }}</span>
                <button wire:click="$set('showSuccessNotification', false)" type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header pb-0 px-4">
                        <h6 class="mb-0">{{ __('Edit Profil') }}</h6>
                    </div>
                    <div class="card-body pt-4 p-4">
                        <form wire:submit.prevent="changeProfile" action="#" method="POST" role="form text-left">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="user-name" class="form-control-label">{{ __('Nama') }}</label>
                                        <div class="@error('user.name')border border-danger rounded-3 @enderror">
                                            <input wire:model="user.name" class="form-control" type="text"
                                                placeholder="Name" id="user-name">
                                        </div>
                                        @error('user.name') <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="user-email"
                                            class="form-control-label">{{ __('Email') }}</label>
                                        <div class="@error('user.email')border border-danger rounded-3 @enderror">
                                            <input wire:model="user.email" class="form-control" type="email"
                                                placeholder="@example.com" id="user-email"  disabled>
                                        </div>
                                        @error('user.email') <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit"
                                    class="btn bg-gradient-warning w-100 mt-4 mb-4">{{ 'Simpan Perubahan' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header pb-0 px-4">
                        <h6 class="mb-0">{{ $isHavePassword ? __('Ganti Password') : __('Buat Password') }}</h6>
                    </div>
                    <div class="card-body pt-4 p-4">
                        <form wire:submit.prevent="changePassword" action="#" method="POST"
                            role="form text-left">
                            <div class="row">
                                @if ($isHavePassword)
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="password-lama" class="form-control-label">{{ __('Password Lama') }}</label>
                                        <div class="@error('password.lama') border border-danger rounded-3 @enderror">
                                            <input wire:model="password.lama" class="form-control" type="password" placeholder="Password Lama" id="password-lama">
                                        </div>
                                        @error('password.lama') <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="password-baru" class="form-control-label">{{ __('Password Baru') }}</label>
                                        <div class="@error('password.baru') border border-danger rounded-3 @enderror">
                                            <input wire:model="password.baru" class="form-control" type="password" placeholder="Password Baru" id="password-baru">
                                        </div>
                                        @error('password.baru') <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="password-verifikasi" class="form-control-label">{{ __('Konfirmasi Password Baru') }}</label>
                                        <div class="@error('password.konfirmasi') border border-danger rounded-3 @enderror">
                                            <input wire:model="password.konfirmasi" class="form-control" type="password" placeholder="Konfirmasi Password Baru" id="password-verifikasi">
                                        </div>
                                        @error('password.konfirmasi') <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn bg-gradient-warning w-100 mt-4 mb-4">
                                        {{ $isHavePassword ? 'Ganti Password' : 'Buat Password' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        Livewire.on('passwordUpdated', () => {
            window.location.reload();
        });
    </script>
@endpush