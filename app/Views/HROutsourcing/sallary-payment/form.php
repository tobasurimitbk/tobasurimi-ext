<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1 class="title-name">Set Data Karyawan - PT. SENTRA ALTIA ALTH DAYA</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="#">
                Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table width="100%" class="mb-3">
                <tbody>
                    <tr style="color: black;">
                        <td width="150px">Nama company</td>
                        <td width="5px">:</td>
                        <td>PT. SENTRA ALTIA ALTH DAYA</td>
                    </tr>
                    <tr style="color: black; height: 20px;">
                        <td colspan="3"></td>
                    </tr>
                    <tr style="color: black;">
                        <td width="150px">Alamat</td>
                        <td width="25px">:</td>
                        <td>[Alamat Perusahaan]</td>
                    </tr>
                </tbody>
            </table>
            <br>
            <form class="karyawan-form" role="form" method="POST">
                <input type="hidden" name="id" id="id">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control badge_karyawan" id="badge_karyawan" name="badge_karyawan" placeholder="No Badge" required>
                            <label for="badge_karyawan">No Badge</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control nama" id="nama" name="nama" placeholder="Nama" required>
                            <label for="nama">Nama</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control tanggal_masuk_kerja" id="tanggal_masuk_kerja" name="tanggal_masuk_kerja" required>
                            <label for="tanggal_masuk_kerja">Tanggal Masuk Kerja</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <select class="form-control departemen" id="departemen" name="departemen" required>
                                <option value="">Pilih Departemen</option>
                                <option value="BOR. CANNING">BOR. CANNING</option>
                                <!-- Tambahkan departemen lain jika ada -->
                            </select>
                            <label for="departemen">Departemen</label>
                        </div>
                    </div>
                </div>
                
                <h4>Data Produksi</h4>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input type="number" step="0.01" class="form-control" id="udang_ac_2850" name="udang_ac_2850" placeholder="UDANG AC 2,850">
                            <label for="udang_ac_2850">UDANG AC 2,850</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input type="number" step="0.01" class="form-control" id="kepahi_sk_1800" name="kepahi_sk_1800" placeholder="KEPAHI SK 1,800">
                            <label for="kepahi_sk_1800">KEPAHI SK 1,800</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input type="number" step="0.01" class="form-control" id="kpt6_mb_9000" name="kpt6_mb_9000" placeholder="KPT6 MB 9,000">
                            <label for="kpt6_mb_9000">KPT6 MB 9,000</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input type="number" step="0.01" class="form-control" id="ml_10500" name="ml_10500" placeholder="ML 10,500">
                            <label for="ml_10500">ML 10,500</label>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input type="number" step="0.01" class="form-control" id="ml_10_ang_ac_350" name="ml_10_ang_ac_350" placeholder="ML 10,ANG AC 3.50">
                            <label for="ml_10_ang_ac_350">ML 10,ANG AC 3.50</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input type="number" step="0.01" class="form-control" id="kepahi_sk_450" name="kepahi_sk_450" placeholder="KEPAHI SK 4.50">
                            <label for="kepahi_sk_450">KEPAHI SK 4.50</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input type="number" step="0.01" class="form-control" id="kpt6_mb_500" name="kpt6_mb_500" placeholder="KPT6 MB 5.00">
                            <label for="kpt6_mb_500">KPT6 MB 5.00</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input type="number" step="0.01" class="form-control" id="ml_700" name="ml_700" placeholder="ML 7.00">
                            <label for="ml_700">ML 7.00</label>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input type="number" step="0.01" class="form-control" id="kpt6_mb_200" name="kpt6_mb_200" placeholder="KPT6 MB 2.00">
                            <label for="kpt6_mb_200">KPT6 MB 2.00</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input type="number" step="0.01" class="form-control" id="ml_800" name="ml_800" placeholder="ML 8.00">
                            <label for="ml_800">ML 8.00</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input type="number" step="0.01" class="form-control" id="kpt6_mb_100" name="kpt6_mb_100" placeholder="KPT6 MB 1.00">
                            <label for="kpt6_mb_100">KPT6 MB 1.00</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input type="number" step="0.01" class="form-control" id="ml_900" name="ml_900" placeholder="ML 9.00">
                            <label for="ml_900">ML 9.00</label>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input type="number" step="0.01" class="form-control" id="kpt6_mb_200_2" name="kpt6_mb_200_2" placeholder="KPT6 MB 2.00">
                            <label for="kpt6_mb_200_2">KPT6 MB 2.00</label>
                        </div>
                    </div>
                </div>
            </form>
            
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <button class="btn btn-add btn-block float-right btn-submit">
                            <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                        </button>
                        <button style="border-color: #e7323a !important; background-color: #e7323a !important; margin-right: 10px !important;" class="btn btn-add btn-block float-right" onclick="resetForm()">
                            <i class="fa-solid fa-rotate-right mr-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="row justify-content-end mb-3">
                    <div class="col-md-4">
                        <input class="form-control search form-out-search" placeholder="Cari Nama / No Badge" />
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>NO</th>
                                <th>TMA</th>
                                <th>NO BADGE</th>
                                <th>NAMA</th>
                                <th>UDANG AC 2850</th>
                                <th>KEPAHI SK 1,800</th>
                                <th>KPT6 MB 9,000</th>
                                <th>ML 10,500</th>
                                <th>ML 10,ANG AC 3.50</th>
                                <th>KEPAHI SK 4.50</th>
                                <th>KPT6 MB 5.00</th>
                                <th>ML 7.00</th>
                                <th>KPT6 MB 2.00</th>
                                <th>ML 8.00</th>
                                <th>KPT6 MB 1.00</th>
                                <th>ML 9.00</th>
                                <th>KPT6 MB 2.00</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table">
                            <!-- Data akan diisi secara dinamis atau statis -->
                            <tr>
                                <td>01</td>
                                <td>01/09/2012</td>
                                <td>0398</td>
                                <td>SETT & AMIDA</td>
                                <td>8.37</td>
                                <td>21.08</td>
                                <td>3.30</td>
                                <td>4.50</td>
                                <td>29.45</td>
                                <td>8.00</td>
                                <td>1</td>
                                <td>61.78%</td>
                                <td>44.62%</td>
                                <td>4.70</td>
                                <td>13.64%</td>
                                <td>0.65</td>
                                <td>0.66</td>
                            </tr>
                            <!-- Tambahkan baris data lainnya sesuai contoh di gambar -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function resetForm() {
    document.querySelector('.karyawan-form').reset();
}

// Fungsi untuk menambahkan data ke tabel
function tambahData() {
    // Implementasi logika untuk menambahkan data dari form ke tabel
    // Anda bisa menggunakan JavaScript atau jQuery untuk ini
}
</script>

<?= $this->endSection(); ?>