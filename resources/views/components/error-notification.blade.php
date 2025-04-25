@if ($showSuccesNotification)
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <span class="alert-text text-white"> 
        <strong>Error! </strong>
        {{ $message }}
    </span>
    <button wire:click="$set('showSuccesNotification', false)" type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
  </div>
@endif