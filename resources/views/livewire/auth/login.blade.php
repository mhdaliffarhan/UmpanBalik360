<section>
    <div class="page-header section-height-75">
        <div class="container">
            <div class="row">
                <div class="col-xl-4 col-lg-5 col-md-6 d-flex flex-column mx-auto">
                    <div class="card card-plain mt-8">
                        <div class="card-header pb-0 text-left bg-transparent">
                            <h3 class="font-weight-bolder text-warning text-gradient">{{ __('Umpan Balik 360 Derajat') }}</h3>
                            <p class="mb-0">{{ __('Evaluasi kinerja tim secara menyeluruh dengan satu aplikasi, solusi mudah untuk penilaian akurat dan pengembangan profesional yang lebih baik!')}}
                            </p>
                            
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="login" action="#" method="POST" role="form text-left">
                                <div class="mb-2">
                                    <label for="email">{{ __('Email') }}</label>
                                    <div class="@error('email')border border-danger rounded-3 @enderror">
                                        <input wire:model="email" id="email" type="email" class="form-control"
                                            placeholder="Email" aria-label="Email" aria-describedby="email-addon">
                                    </div>
                                    @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-2">
                                    <label for="password">{{ __('Password') }}</label>
                                    <div class="@error('password')border border-danger rounded-3 @enderror">
                                        <input wire:model="password" id="password" type="password" class="form-control"
                                            placeholder="Password" aria-label="Password"
                                            aria-describedby="password-addon">
                                    </div>
                                    @error('password') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-check form-switch">
                                    <input wire:model="remember_me" class="form-check-input" type="checkbox"
                                        id="rememberMe">
                                    <label class="form-check-label" for="rememberMe">{{ __('Ingat Saya') }}</label>
                                </div>
                                <div class="text-center">
                                    <button type="submit"
                                        class="btn bg-gradient-warning w-100 mt-2 mb-0">{{ __('Masuk') }}</button>
                                </div>
                                <hr class="my-2">

                                <div class="text-center">
                                    <a class="btn btn-primary btn-block w-100" style="background-color: #dd4b39" href="/auth/redirect"><i class="fab fa-google fa-lg me-2"></i> Masuk Dengan Google</a>
                                </div>
                            </form>

                            
                            {{-- <small class="text-muted">{{ __('Lupa password? Reset pasword') }} <a
                                    href="{{ route('forgot-password') }}"
                                    class="text-warning text-gradient font-weight-bold">{{ __('disini') }}</a></small> --}}
                            <p class="mb-4 text-sm mx-auto text-center">
                                {{ __(' Belum punya akun?') }}
                                <a href="{{ route('sign-up') }}"
                                    class="text-warning text-gradient font-weight-bold">{{ __('Daftar di sini') }}</a>
                            </p>
                        </div>
                        {{-- <div class="card-footer text-center mt-0 pt-0 px-lg-2 px-1">
                            <p class="mb-4 text-sm mx-auto">
                                {{ __(' Belum punya akun?') }}
                                <a href="{{ route('sign-up') }}"
                                    class="text-warning text-gradient font-weight-bold">{{ __('Daftar') }}</a>
                            </p>
                        </div> --}}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="oblique position-absolute top-0 h-100 d-md-block d-none me-n8">
                        <div class="oblique-image bg-cover position-absolute fixed-top ms-auto h-100 z-index-0 ms-n6"
                            style="background-image:url('../assets/img/back-ground/login.jpg')">
                            <span class="mask bg-gradient-primary opacity-3"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
