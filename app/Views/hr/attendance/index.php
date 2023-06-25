<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>


<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Attendance</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-center mb-3">
                <div class="row">
                    <div class="col-md-6">
                        Web Cam
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                NIP
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select ar_id" name="ar_id" id="ar_id">
                                        <option value=""></option>
                                    </select>
                                    <label for="floatingInput">NIP</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" style="height: 50px;">
                                Nama
                            </div>
                            <div class="col-md-6" style="height: 50px;">
                                :
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" style="height: 50px;">
                                Jabatan
                            </div>
                            <div class="col-md-6" style="height: 50px;">
                                :
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-6" style="height: 50px;">
                                Jam Kerja
                            </div>
                            <div class="col-md-6" style="height: 50px;">
                                :
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" style="height: 50px;">
                                Jam Istirahat
                            </div>
                            <div class="col-md-6" style="height: 50px;">
                                :
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" style="height: 50px;">
                                Jam Masuk
                            </div>
                            <div class="col-md-6" style="height: 50px;">
                                :
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" style="height: 50px;">
                                Istirahat Mulai
                            </div>
                            <div class="col-md-6" style="height: 50px;">
                                :
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" style="height: 50px;">
                                Istirahat Selesai
                            </div>
                            <div class="col-md-6" style="height: 50px;">
                                :
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" style="height: 50px;">
                                Jam Pulang
                            </div>
                            <div class="col-md-6" style="height: 50px;">
                                :
                            </div>
                        </div>


                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <button class="btn form-control btn-warning " data-btn="create-modal">
                            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Absen Istirahat
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn form-control btn-primary " data-btn="create-modal">
                            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Absen Kehadiran
                        </button>
                    </div>
                </div>

            </div>

            <!--
            <div class="row justify-content-end mb-3">
                <div class="col-md-2">
                    <input class="form-control search form-out-search" placeholder="Search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th onclick="changeSort('nip')" class="sort">NIP</th>
                                <th onclick="changeSort('name')" class="sort">Nama Lengkap</th>
                                <th onclick="changeSort('divisionName')" class="sort">Divisi</th>
                                <th onclick="changeSort('email')" class="sort">Email</th>
                                <th onclick="changeSort('phone_no')" class="sort">No. Telepon</th>
                                <th onclick="changeSort('address')" class="sort">Alamat</th>
                                <th onclick="changeSort('dob')" class="sort">Tanggal Lahir</th>
                                <th onclick="changeSort('gender')" class="sort">Jenis Kelamin</th>
                                <th onclick="changeSort('acc_no')" class="sort">No. Rekening</th>
                                <th onclick="changeSort('status')" class="sort">Status</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table" style="cursor: pointer;">

                        </tbody>
                    </table>
                </div>
            </div>

-->
        </div>
    </div>
</section>

<?= $this->endSection(); ?>