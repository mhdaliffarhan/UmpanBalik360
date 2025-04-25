<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;
use App\Http\Livewire\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class Logout extends Component
{
    public function confirmLogout()
    {
        $this->dispatchBrowserEvent('confirm-logout');
    }
    public function logout()
    {
        if (auth()->check()) {
            auth()->logout();
            Alert::success('Berhasil', 'Berhasil Keluar');
            return redirect('/login');
        } else {
            // Jika tidak ada pengguna yang login, langsung redirect ke halaman login
            return redirect('/login');
        }
    }

    public function render()
    {
        return view('livewire.auth.logout');
    }
}
