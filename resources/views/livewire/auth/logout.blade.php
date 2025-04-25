

<a class="dropdown-item border-radius-md" wire:click="confirmLogout">
  <i  class="fa fa-sign-out me-sm-1">
  </i>
  <span class="d-sm-inline d-none">Keluar</span>
</a>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('livewire:load', function () {
        window.addEventListener('confirm-logout', event => {
            Swal.fire({
                title: 'Keluar?',
                text: "Apakah anda yakin ingin keluar?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea0606',
                cancelButtonColor: '#252f40',
                confirmButtonText: 'Ya, keluar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.logout();
                }
            });
        });
    });
    </script>