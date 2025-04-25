<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">Daftar Tim Kerja</h5>
                        </div>
                        <a href="{{ route('create-group')}}" class="btn bg-gradient-primary btn-sm mb-0" type="button">+&nbsp; Buat grup</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card">
                            <div class="row">
                                @for ($i = 0; $i < 3; $i++)
                                    <div class="col-md-4  px-3 pt-3 pb-3">
                                        <div class="card">
                                            <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1">
                                            <a href="javascript:;" class="d-block">
                                                <img src="../assets/img/home-decor-3.jpg" class="img-fluid border-radius-lg">
                                            </a>
                                            </div>
                                        
                                            <div class="card-body pt-2">
                                            <span class="text-gradient text-primary text-uppercase text-xs font-weight-bold my-2">House</span>
                                            <a href="javascript:;" class="card-title h5 d-block text-darker">
                                                Shared Coworking
                                            </a>
                                            <p class="card-description mb-4">
                                                Use border utilities to quickly style the border and border-radius of an element. Great for images, buttons.
                                            </p>
                                            <div class="author align-items-center">
                                                <img src="../assets/img/team-2.jpg" alt="..." class="avatar shadow">
                                                <div class="name ps-3">
                                                <span>Mathew Glock</span>
                                                <div class="stats">
                                                    <small>Posted on 28 February</small>
                                                </div>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            
                        {{-- <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                            <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Group Name</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="align-middle">
                                    <a href="$">1</a>
                                </td>
                                <td>
                                <div class="d-flex px-2 py-1">
                                    <div>
                                    <img src="../assets/img/team-2.jpg" class="avatar avatar-sm me-3">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                    <a href="#" class="mb-0 text-sm font-wight-bold">Politeknik Statistika STIS</a>
                                    </div>
                                </div>
                                </td>
                                <td class="align-middle">
                                <a href="/dashboard" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user">
                                    Edit
                                </a>
                                </td>
                            </tr>
                            <tr>
                            </tbody>
                        </table>
                        </div> --}}
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</main>
