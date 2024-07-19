<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Laporan Materal Request</h1>
        <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <li><button class="dropdown-item pdf" onclick="pdf('<?= base_url("/laporan-warehouse/stock-kartu/print"); ?>')">PDF</button></li>
        </ul>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("laporan-warehouse"); ?>">
                Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-4">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_divisi_department" name="filter_divisi_department" id="filter_divisi_department">

                        </select>
                        <label for="floatingInput">Filter Tipe Barang</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>



            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('wo_no')" class="sort">Sumber Barang</th>
                                <th onclick="changeSort('req_no')" class="sort">Asal Barang</th>
                                <th onclick="changeSort('request_date')" class="sort">Lokasi Barang</th>
                                <th onclick="changeSort('divisi')" class="sort">Tanggal Penerimaan</th>
                                <th onclick="changeSort('kode_barang')" class="sort">Nomor</th>
                                <th onclick="changeSort('nama_barang')" class="sort">File Number</th>
                                <th onclick="changeSort('qty2')" class="sort">Kode Barang</th>
                                <th onclick="changeSort('satuan')" class="sort">Nama Barang</th>
                                <th onclick="changeSort('ref_no')" class="sort">Spesifikasi</th>
                                <th onclick="changeSort('ref_no')" class="sort">Satuan</th>
                                <th onclick="changeSort('ref_no')" class="sort">Saldo</th>
                            </tr>

                        </thead>
                        <tbody class="body-table" id="body-table">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>

</script>

<?= $this->endSection(); ?>