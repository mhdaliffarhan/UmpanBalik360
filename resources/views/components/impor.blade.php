<div class="col-12 mb-3">
  <label for="file" class="form-label">Impor Data Anggota dari Excel</label>
  <div class="row">
      <div class="col-12 col-md-8 mb-3">
          <input wire:model="file" type="file" class="form-control" id="file">
          @error('file') <span class="text-danger">{{ $message }}</span> @enderror
      </div>
      <div class="col-12 col-md-2 mb-3">
          <button wire:click="imporExcel" class="btn bg-gradient-success w-100">Impor Excel</button>
      </div>
      <div class="col-12 col-md-2 mb-3">
        <button wire:click="exportTemplate" class="btn bg-gradient-danger w-100">Unduh Template</button>
    </div>
  </div>
</div>