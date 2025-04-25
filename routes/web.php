<?php

use App\Models\User;

use App\Http\Livewire\Rtl;
use App\Http\Livewire\Group;
use App\Http\Livewire\Nilai;
use Illuminate\Http\Request;
use App\Http\Livewire\Tables;
use App\Http\Livewire\Billing;
use App\Http\Livewire\Profile;
use App\Models\AnggotaTimKerja;
use App\Http\Livewire\Dashboard;

use App\Http\Livewire\Auth\Login;
use App\Http\Livewire\Monitoring;
use App\Http\Livewire\Auth\SignUp;
use App\Http\Livewire\CreateGroup;
use App\Http\Livewire\BuatStruktur;
use App\Http\Livewire\BuatTimKerja;
use App\Http\Livewire\EditTimKerja;
use App\Http\Livewire\StaticSignIn;
use App\Http\Livewire\StaticSignUp;
use App\Http\Livewire\BuatPenilaian;
use App\Http\Livewire\EditPenilaian;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\DaftarTimKerja;
use App\Http\Livewire\DetailTimKerja;
use App\Http\Livewire\HasilPenilaian;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\DaftarPenilaian;
use App\Http\Livewire\DetailPenilaian;
use App\Http\Livewire\DaftarMonitoring;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Livewire\Auth\ResetPassword;
use App\Http\Livewire\Auth\ForgotPassword;
use App\Http\Livewire\DaftarHasilPenilaian;
use App\Http\Livewire\LaravelExamples\UserProfile;
use App\Http\Livewire\LaravelExamples\UserManagement;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/sign-up', SignUp::class)->name('sign-up');
Route::get('/login', Login::class)->name('login');


Route::middleware('auth')->group(function () {
    Route::get('/dasbor', Dashboard::class)->name('dasbor');
    Route::get('/profil', UserProfile::class)->name('user-profile');
    Route::get('/tim-kerja', DaftarTimKerja::class)->name('tim-kerja');
    Route::get('/buat-tim-kerja', BuatTimKerja::class)->name('buat-tim-kerja');
    Route::get('/tim-kerja/{id}', DetailTimKerja::class)->name('detail-tim-kerja');
    Route::get('/edit-tim-kerja/{id}', EditTimKerja::class)->name('edit-tim-kerja');
    Route::get('/buat-struktur/{id}', BuatStruktur::class)->name('buat-stuktur');
    Route::get('/penilaian', DaftarPenilaian::class)->name('penilaian');
    Route::get('/buat-penilaian', BuatPenilaian::class)->name('buat-penilaian');
    Route::get('/penilaian/{id}', DetailPenilaian::class)->name('detail-penilaian');
    Route::get('/nilai/{id}', Nilai::class)->name('nilai');
    Route::get('/monitoring', DaftarMonitoring::class)->name('monitoring');
    Route::get('/monitoring/{id}', Monitoring::class)->name('detail-monitoring');
    Route::get('/edit-penilaian/{id}', EditPenilaian::class)->name('edit-penilaian');
    Route::get('/hasil-penilaian', DaftarHasilPenilaian::class)->name('hasil-penilaian');
    Route::get('/hasil-penilaian/{id}', HasilPenilaian::class)->name('detail-hasil-penilaian');
});

Route::get('/login/forgot-password', ForgotPassword::class)->name('forgot-password');
Route::get('/reset-password/{id}', ResetPassword::class)->name('reset-password')->middleware('signed');

Route::get('/auth/redirect', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/auth/callback', function (Request $request) {
    // Periksa apakah pengguna sudah login
    if (Auth::check()) {
        return redirect('/dasbor');
    }

    // Coba ambil data pengguna dari OAuth provider
    try {
        $googleUser = Socialite::driver('google')->user();
    } catch (\Exception $e) {
        // Tangani kesalahan yang terjadi saat mengambil data pengguna
        return redirect('/login')->with('error', 'Gagal mengambil data pengguna dari OAuth provider.');
    }

    // Periksa ketersediaan sesi pengguna
    if (!$request->hasSession()) {
        // Redirect pengguna ke halaman instruksi cookie
        return redirect('/instruksi-cookie')->with('warning', 'Sesi tidak tersedia. Silakan aktifkan cookie di browser Anda.');
    }

    // Lakukan pengecekan apakah pengguna sudah ada di basis data Anda
    $user = User::where('email', $googleUser->email)->first();

    if (!$user) {
        // Buat pengguna baru
        $user = User::create([
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'status' => 'registered',
            'photo_path' => $googleUser->avatar,
        ]);
    } else {
        // Perbarui status jika pengguna ada tetapi statusnya 'unregistered'
        if ($user->status == 'unregistered') {
            $user->status = 'registered';
            $user->name = $googleUser->name;
        }

        // Update foto profil jika berubah
        if ($user->photo_path !== $googleUser->avatar) {
            $user->photo_path = $googleUser->avatar;
        }

        $user->save();
    }

    // Autentikasi pengguna
    Auth::login($user);


    $userRole = AnggotaTimKerja::where('user_id', $user->id)
        ->where('role', 'admin')
        ->first();
    if ($userRole) {
        session(['role' => 'admin']);
    } else {
        session(['role' => 'anggota']);
    }

    return redirect('/dasbor');
});
