<div class="container mb-3">
    <div class="row">
        <div class="col-md-12 mb-3">
            <h6>Daftar Pertanyaan</h6>
        </div>
        <div class="col-md-12">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Indikator
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                Pertanyaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($indikatorPenilaian as $key => $indikator)
                            <tr>
                                <td class="align-middle">{{ $indikator['indikator'] }}</td>
                                <td>
                                    @foreach ($indikator['pertanyaan'] as $index => $pertanyaan)
                                        <div class="form-check ms-3">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="indikatorPenilaian.{{ $key }}.pertanyaan.{{ $index }}.status_check"
                                                value="checked">
                                            <label class="form-check-label">{{ $pertanyaan['pertanyaan'] }}</label>
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
