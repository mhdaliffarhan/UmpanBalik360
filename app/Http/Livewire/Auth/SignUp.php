<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SignUp extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $confirmPassword = '';

    protected $rules = [
        'name' => 'required|min:3',
        'password' => 'required|min:6'
    ];

    public function mount()
    {
        if (auth()->user()) {
            redirect('/dasbor');
        }
    }

    public function register()
    {
        $this->validate([
            'name' => 'required|min:3',
            'password' => 'required|min:6',
            'confirmPassword' => 'required|same:password', // Memastikan confirmPassword sama dengan password
        ]);

        $email = User::where('email', $this->email)->first();
        // dd($email);

        if ($email && ($email->status == 'unregistered') && ($email->password == null)) {
            $user = User::findOrFail($email->id);
            $user->name = $this->name;
            $user->password = Hash::make($this->password);
            $user->status = 'registered';
            $user->save();
        } else {
            $this->validate([
                'email' => 'required|email:rfc,dns|unique:users',
            ]);
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'status' => 'registered',
            ]);
        }


        auth()->login($user);

        return redirect('/dasbor');
    }

    public function render()
    {
        return view('livewire.auth.sign-up');
    }
}
