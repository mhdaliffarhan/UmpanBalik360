<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use App\Models\AnggotaTimKerja;
use RealRashid\SweetAlert\Facades\Alert;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember_me = false;

    protected $rules = [
        'email' => 'required|email:rfc,dns',
        'password' => 'required',
    ];

    public function mount()
    {
        if (auth()->user()) {
            redirect('/dasbor');
        }
        // $this->fill(['email' => 'mhdaliffarhan22@gmail.com', 'password' => 'password']);
    }

    public function login()
    {
        $credentials = $this->validate();
        if (auth()->attempt(['email' => $this->email, 'password' => $this->password], $this->remember_me)) {
            $user = User::where(["email" => $this->email])->first();
            auth()->login($user, $this->remember_me);

            $userRole = AnggotaTimKerja::where('user_id', $user->id)
                ->where('role', 'admin')
                ->first();
            if ($userRole) {
                session(['role' => 'admin']);
            } else {
                session(['role' => 'anggota']);
            }

            // dd(session('role '));

            toast('Berhasil Login', 'success');
            return redirect()->intended('/dasbor');
        } else {
            return $this->addError('email', trans('auth.failed'));
        }
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
