<div class="container">
  <div class="col-md-12 mb-3 text-center">
      <h6>Indikator Penilaian</h6>
  </div>
  <div class="col-md-12 mb-3">
    <div class="row">
      @foreach ($indikatorPenilaian as $key => $indikator)
        <div class="col-md-12 mb-3">
          <label for="indikator{{ $key }}" class="form-label">Indikator {{ $key + 1 }}</label>
          <div class="input-group">
            <input wire:model="indikatorPenilaian.{{ $key }}.indikator" type="text" class="form-control" id="indikator{{ $key }}">
            <button wire:click="hapusIndikator({{ $key }})" class="btn btn-danger mb-0 w-10">X</button>
          </div>
        </div>
          @foreach ($indikator['pertanyaan'] as $index => $pertanyaan)
            <div class="col-12 col-md-2 mb-2 text-center">
              <label for="indikator{{ $key }}pertanyaan{{ $index }}" class="form-label">Pertanyaan {{ $index + 1 }}</label>
            </div>
            <div class="col-12 col-md-10 mb-3">
              <div class="input-group">
                <input wire:model="indikatorPenilaian.{{ $key }}.pertanyaan.{{ $index }}" type="text" class="form-control" id="indikator{{ $key }}pertanyaan{{ $index }}">
                <button wire:click="hapusPertanyaan({{ $key }}, {{ $index }})" class="btn btn-danger mb-0">X</button>
              </div>
            </div>
          @endforeach
          <div class="row justify-content-md-end">
            <div class="col-12 col-md-10">
              <button wire:click="tambahPertanyaan({{ $key }})" class="btn bg-gradient-info btn-sm w-100 mb-3">Tambah Pertanyaan</button>
            </div>
          </div>
      @endforeach
        <div class="col-12 col-md-12">
          {{-- <button wire:click="hapusIndikator({{ $key }})" class="btn bg-gradient-danger btn-sm w-100 mb-1">Hapus Indikator</button> --}}
          <button wire:click="tambahIndikator" class="btn bg-gradient-info btn-sm w-100 mb-3">Tambah Indikator</button>
        </div>
    </div>
    {{-- <button wire:click="testingIndikator" class="btn btn-danger mb-0">Testing Indikator</button> --}}
  </div>
</div>
