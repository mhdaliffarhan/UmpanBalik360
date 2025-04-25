<?php

namespace App\Http\Livewire\LaravelExamples;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;

class UserProfile extends Component
{
    use WithFileUploads;

    public User $user;
    public $image;
    public $showSuccesNotification = false;
    public $notificationMessage = '';
    public $isHavePassword = false;
    public $password = [
        'lama' => '',
        'baru' => '',
        'konfirmasi' => '',
    ];

    protected $rules = [
        'user.name' => 'required|string|max:255',
        'user.email' => 'email:rfc,dns',
    ];

    public function mount()
    {
        $this->user = auth()->user();

        if (!is_null($this->user->password)) {
            $this->isHavePassword = true;
        }
    }

    public function updatedImage()
    {
        $this->validate([
            'image' => 'nullable|image|max:1024', // Validasi gambar
        ]);

        $path = $this->image->store('profile-photos', 'public');
        $this->user->photo_path = $path;
        $this->user->save();

        $this->notificationMessage = 'Foto profil berhasil diperbarui';
        $this->showSuccesNotification = true;
    }

    public function changeProfile()
    {
        $this->validate([
            'user.name' => 'required|string|max:255',
            'user.email' => 'required|string|email|max:255|unique:users,email,' . auth()->user()->id,
        ]);

        $this->user->save();
        toast('Data profil berhasil diperbaharui', 'success');
        $this->notificationMessage = 'Informasi Profil berhasil diperbarui';
        $this->showSuccesNotification = true;
    }

    public function changePassword()
    {

        // Jika password lama tidak kosong, tambahkan aturan validasi untuk password lama
        if ($this->password['lama'] !== null) {
            $rules['password.lama'] = 'required|min:6';
        }


        $this->validate([
            'password.baru' => 'required|min:6',
            'password.konfirmasi' => 'required|min:6|same:password.baru',
        ]);

        // Jika password sebelumnya belum diatur, gunakan metode create
        if (!$this->isHavePassword) {
            // Buat password baru
            auth()->user()->update([
                'password' => bcrypt($this->password['baru']),
            ]);
        } else {
            // Jika password sebelumnya sudah diatur, gunakan metode update

            // Validasi password lama hanya jika tidak kosong
            if ($this->password['lama'] !== null) {
                // Validasi password lama
                if (!Hash::check($this->password['lama'], auth()->user()->password)) {
                    $this->addError('password.lama', 'Password lama tidak sesuai.');
                    return;
                }
            }

            // Perbarui password
            auth()->user()->update([
                'password' => bcrypt($this->password['baru']),
            ]);
        }

        // Tampilkan notifikasi
        session()->flash('message', 'Password berhasil diperbarui.');

        // Refresh halaman
        $this->emit('passwordUpdated');
        $this->notificationMessage = 'Password berhasil diperbarui';
        $this->showSuccesNotification = true;
    }




    public function render()
    {
        return view('livewire.laravel-examples.user-profile');
    }
}
