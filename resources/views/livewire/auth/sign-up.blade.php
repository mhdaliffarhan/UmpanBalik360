  <section class="h-100-vh mb-8">
      <div class="page-header align-items-start section-height-50 pt-5 pb-11 m-3 border-radius-lg"
          style="background-image: url('../assets/img/back-ground/login.jpg');">
          <span class="mask bg-gradient-primary opacity-3"></span>
          <div class="container">
              <div class="row justify-content-center">
                  <div class="col-lg-5 text-center mx-auto">
                      <h1 class="text-white mb-2 mt-2">{{ __('Selamat Datang!') }}</h1>
                      {{-- <p class="text-lead text-white">
                          {{ __('Use these awesome forms to login or create new account in your
                          project for free.') }}
                      </p> --}}
                  </div>
              </div>
          </div>
      </div>
      <div class="container">
          <div class="row mt-lg-n11 mt-md-n12 mt-n11">
              <div class="col-xl-4 col-lg-5 col-md-7 mx-auto">
                  <div class="card z-index-0 mb-0">
                      <div class="card-header text-center pb-0">
                          <h4 class="text-warning text-gradient font-weight-bolder">{{ __('Daftar') }}</h4>
                      </div>
                      <div class="card-body">

                          <form wire:submit.prevent="register" action="#" method="POST" role="form text-left">
                              <div class="mb-3">
                                  <div class="@error('name') border border-danger rounded-3  @enderror">
                                      <input wire:model="name" type="text" class="form-control" placeholder="Nama"
                                          aria-label="Name" aria-describedby="email-addon">
                                  </div>
                                  @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                              </div>
                              <div class="mb-3">
                                  <div class="@error('email') border border-danger rounded-3 @enderror">
                                      <input wire:model="email" type="email" class="form-control" placeholder="Email"
                                          aria-label="Email" aria-describedby="email-addon">
                                  </div>
                                  @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                              </div>
                              <div class="mb-3">
                                  <div class="@error('password') border border-danger rounded-3 @enderror">
                                      <input wire:model="password" type="password" class="form-control"
                                          placeholder="Password" aria-label="Password"
                                          aria-describedby="password-addon">
                                  </div>
                                  @error('password') <div class="text-danger">{{ $message }}</div> @enderror
                              </div>
                              <div class="mb-3">
                                <div class="@error('confirm-password') border border-danger rounded-3 @enderror">
                                    <input wire:model="confirmPassword" type="password" class="form-control"
                                        placeholder="Konfirmasi Password" aria-label="Password"
                                        aria-describedby="password-addon">
                                </div>
                                @error('confirmPassword') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                              <div class="form-check form-check-info text-left">
                                  <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"
                                      checked>
                                  <label class="form-check-label" for="flexCheckDefault">
                                      {{ __('saya setuju dengan') }} <a href="javascript:;"
                                          class="text-warning text-gradient font-weight-bold">{{ __('syarat dan ketentuan') }}</a>
                                  </label>
                              </div>
                              <div class="text-center">
                                  <button type="submit" class="btn bg-gradient-warning w-100 my-4 mb-2">Daftar</button>
                              </div>
                              <div class="text-center">
                                <p class="text-sm mt-3 mb-0">{{ __('Sudah punya akun? ') }}<a
                                        href="{{ route('login') }}"
                                        class="text-warning text-gradient font-weight-bold">{{ __('Masuk') }}</a>
                                </p>
                              </div>
                          </form>

                      </div>
                  </div>
              </div>
          </div>
      </div>
  </section>
