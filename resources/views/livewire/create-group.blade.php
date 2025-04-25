<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0">
                    <div class="container text-center">
                        <h5>Buat Grup</h5>
                        <h6 class="mb-5">(Langkah {{$formStep}}/2) </h6>
                    </div>
                </div>

                @if ($formStep == 1)
                    <!-- Formulir Langkah 1 -->
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="row">
                            <div class="container px-5">
                                <div class="col-12 mb-3">
                                    <label for="groupName" class="form-label">Nama Grup</label>
                                    <input wire:model="groupName" type="text" class="form-control" id="groupName">
                                </div>
                                <div class="col-12 mb-4">
                                    <label for="groupDesc" class="form-label">Deskripsi Grup</label>
                                    <textarea wire:model="groupDesc" class="form-control" id="groupDesc" aria-label="Dengan textarea"></textarea>
                                </div>
                     
                                <div class="col-12 mb-3">
                                    <h6>Anggota Grup</h6>
                                </div>
                                <!-- File upload section -->
                                <div class="col-12 mb-3">
                                    <label for="file" class="form-label">Impor Data Anggota dari Excel (Opsional)</label>
                                    <div class="row">
                                        <div class="col-md-10 mb-3">
                                            <input wire:model="file" type="file" class="form-control" id="file">
                                            {{-- @error('file') <span class="text-danger">{{ $message }}</span> @enderror --}}
                                        </div>
                                        <div class="col-md-auto mb-3">
                                            <button wire:click="importExcel" class="btn btn-primary">Impor</button>
                                        </div>
                                    </div>
                                </div>
                            
                                <!-- Loop untuk anggota grup yang sudah ada -->
                                @foreach($groupMembers as $key => $member)
                                    <div class="col-12 mb-3">
                                        <input wire:model="groupMembers.{{$key}}.email" type="email" class="form-control" id="email{{$key + 1}}" value="{{ $groupMembers[$key]['email'] ?? '' }}">
                                    </div>
                                    {{-- Buatkan button untuk menghapus field inputan diatas --}}
                                @endforeach
                            
                                <!-- Tombol untuk Menambah Anggota Baru -->
                                <div class="d-flex flex-row">
                                    <button wire:click="addMemberField" class="btn btn-secondary mb-3">+ Tambah Anggota Lainnya</button>
                                </div>
                                <div class="d-flex flex-row-reverse">
                                    <button wire:click="nextStep" class="btn btn-primary">Selanjutnya</button>
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif ($formStep == 2)
                    <!-- Formulir Langkah 2 -->
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="row">
                            <div class="container px-5">
                                <!-- Loop untuk jabatan -->
                                @foreach($positions as $positionIndex => $position)
                                    <div class="col-12 mb-3">
                                        <div class="row">
                                            <h6 class="mb-2">Level {{$positionIndex + 1}}</h6>
                                            <div class="col-12">
                                                {{-- <label for="level{{$positionIndex + 1}}" class="form-label">Level (Ketua = 0)</label>
                                                <input wire:model="positions.{{$positionIndex}}.level" type="number" min="0" value="{{$positionIndex}}" class="form-control" id="level{{$positionIndex + 1}}"> --}}
                                                <label for="position{{$positionIndex + 1}}" class="form-label">Nama Jabatan</label>
                                                <input wire:model="positions.{{$positionIndex}}.position" type="text" class="form-control" id="position{{$positionIndex + 1}}" placeholder="jabatan">
                                                
                                                <label for="position{{$positionIndex + 1}}.member1" class="form-label">Anggota</label>
                                                
                                                
                                                {{-- Use foreach loop for members --}}
                                                @foreach($position['selectedMembers'] as $memberIndex => $member)
                                                    <select wire:model="positions.{{$positionIndex}}.selectedMembers.{{$memberIndex}}" class="form-select mt-2">
                                                        @foreach($groupMembers as $groupMember)
                                                            <option value="{{$groupMember['email']}}">{{$groupMember['email']}}</option>
                                                        @endforeach
                                                    </select>
                                                @endforeach

                                                <!-- Button to add more members to the position -->
                                                @if($positionIndex + 1 < count($positions))
                                                    <button wire:click="addMemberToPosition({{$positionIndex}})" class="btn btn-secondary mt-2">+ Tambah Anggota Lainnya</button>
                                                @endif
                                                
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Tombol untuk menambah jabatan baru -->
                                <div class="d-flex flex-row justify-content-between">
                                    <button wire:click="backStep" class="btn btn-info">Kembali</button>
                                    <button wire:click="addPositionField" class="btn btn-success">+ Tambah Jabatan Baru</button>
                                    <button wire:click="saveGroup" class="btn btn-primary">Buat Grup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
