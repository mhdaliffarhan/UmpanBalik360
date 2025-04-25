<x-layouts.base>
    {{-- If the user is authenticated --}}
    @auth()
        @include('layouts.navbars.auth.sidebar')
        @include('layouts.navbars.auth.nav')
        {{ $slot }}
        <main>
            <div class="container-fluid">
                <div class="row">
                    @include('layouts.footers.auth.footer')
                </div>
            </div>
        </main>
    @endauth

    {{-- If the user is not authenticated (if the user is a guest) --}}
    @guest
        {{-- If the user is on the login page --}}
        @if (!auth()->check() && in_array(request()->route()->getName(),['login'],))
            {{-- @include('layouts.navbars.guest.login') --}}
            {{ $slot }}
            <div class="mt-5">
                @include('layouts.footers.auth.footer')
            </div>

            {{-- If the user is on the sign up page --}}
        @elseif (!auth()->check() && in_array(request()->route()->getName(),['sign-up'],))
            <div>
                @include('layouts.navbars.guest.sign-up')
                {{ $slot }}
                @include('layouts.footers.auth.footer')
            </div>
        @endif
    @endguest

</x-layouts.base>
