<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($rasio) ? "Tambah Rasio" : "Update Rasio" ?></h1>
        <div class="col-button-tambah-spp text-right">
            <a class="btn btn-hide-form btn-discard" href="<?= base_url("rasio"); ?>">
                Kembali
            </a>
            <button class="btn btn-show-form btn-save btn-submit-parent">
                Simpan
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" id="rawMaterialITab">Raw Material I</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="rawMaterialIITab">Raw Material II</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="costTab">Cost</a>
                </li>
            </ul>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataDivisi as $d) : ?>
                                    <option value="<?= $d['id'] ?>" <?= !empty($rasio) && $rasio->divisi_id == $d['id'] ? "selected" : "" ?>>
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <!-- <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Warehouse</label>
                        </div>
                    </div> -->
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" class="form-control input-picker tanggal_awal" id="tanggal_awal" name="tanggal_awal" placeholder="Tanggal Awal Dibuat" value="<?= !empty($rasio) ? date('d/m/Y', strtotime($rasio->tanggal_awal)) : "" ?>">
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" class="form-control input-picker tanggal_akhir" id="tanggal_akhir" name="tanggal_akhir" placeholder="Tanggal Akhir Dibuat" value="<?= !empty($rasio) ? date('d/m/Y', strtotime($rasio->tanggal_akhir)) : "" ?>">
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select kategori" name="kategori" id="kategori">
                                <option value="" data-code=""></option>
                                <?php
                                if (!empty($kategoriBarangAkun)) {
                                    foreach ($kategoriBarangAkun as $kategoriBarang) {
                                ?>
                                        <option value="<?= $kategoriBarang->id; ?>" <?= !empty($rasio) && $rasio->kategori_barang_id == $kategoriBarang->id ? "selected" : "" ?>><?= $kategoriBarang->description; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Kategori Barang</label>
                        </div>
                    </div>
                </div>
                <!-- card raw material I -->
                <div id="rawMaterialICard">
                    <input type="hidden" name="id" id="id" value="" class="id">
                    <?= csrf_field() ?>
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link nav-link-raw-material-i active" id="rawIBahanDigunakan" href="#bahan_digunakan">Bahan Digunakan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-raw-material-i" id="rawIBahanProsesUlang" href="#bahan_proses_ulang">Bahan Proses Ulang</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-raw-material-i" id="rawIBahanFilling" href="#bahan_filling">Bahan Filling dan Ditapak</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-raw-material-i" id="rawISaldoAwal" href="#saldo_awal">Saldo Stock Awal</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-raw-material-i" id="rawISaldoAkhir" href="#saldo_akhir">Saldo Stock Akhir</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-raw-material-i" id="rawISaldoAdjustment" href="#saldo_adjustment">Saldo Adjustment</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-raw-material-i" id="rawISaldoJual" href="#saldo_jual">Saldo Jual</a>
                        </li>
                        <!-- <li class="nav-item">
                            <a class="nav-link nav-link-raw-material-i" id="rawISaldoTrimming" href="#saldo_trimming">Saldo Trimming</a>
                        </li> -->
                        <li class="nav-item">
                            <a class="nav-link nav-link-raw-material-i" id="rawISaldoKopek" href="#saldo_kopek">Saldo Kopek</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-raw-material-i" id="rawIHasilTrimming" href="#hasil_trimming">Hasil Trimming</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-raw-material-i" id="rawIHasilKaleng" href="#hasil_kaleng">Hasil Kaleng</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-raw-material-i" id="rawIHasilFrozen" href="#hasil_frozen">Hasil Frozen</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="content" role="tabpanel">
                            <!-- Content will be loaded here -->
                        </div>
                    </div>
                </div>
                <!-- end card raw material I -->
                <!-- card raw material II -->
                <div id="rawMaterialIICard" style="display: none;">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select akun_pemakaian" name="akun_pemakaian" id="akun_pemakaian">
                                    <option value="" data-code=""></option>
                                    <?php
                                    if (!empty($kategoriBarangAkun)) {
                                        foreach ($kategoriBarangAkun as $kategoriBarang) {
                                    ?>
                                            <option value="<?= $kategoriBarang->id; ?>" <?= !empty($rasio) && $rasio->kategori_barang_id == $kategoriBarang->id ? "selected" : "" ?>><?= $kategoriBarang->description; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Akun Pemakaian</label>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-end">
                        <div class="col mb-3">
                            <label class="form-label font-weight-bold lable-title">Data Raw Material II</label>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="selectedItemTable" width="100%" border="1" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="text-align: center;" rowspan="2">No</th>
                                            <th style="text-align: center;" colspan="5">Data Pemakaian</th>
                                        </tr>
                                        <tr>
                                            <th style="text-align: center;">Spesifikasi</th>
                                            <th style="text-align: center;">Qty</th>
                                            <th style="text-align: center;">Harga Total</th>
                                            <th style="text-align: center;">Harga Satuan</th>
                                            <th style="text-align: center;">Satuan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="body-detail-table-digunakan-material-2">
                                    </tbody>
                                    <tfoot style="background: #ffffff !important;" class="tfoot-detail-table-digunakan-material-2" id="tfoot-detail-table-digunakan-material-2">
                                        <tr>
                                            <td colspan="12" style="text-align: center;">
                                                Tidak Ada Barang
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label font-weight-bold lable-title">Data Total Pemakaian Barang</label>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly placeholder="Qty" value="" class="form-control qtyTotalPembelian_material_2" id="qtyTotalPembelian_material_2" name="qtyTotalPembelian_material_2" aria-label="Floating label select example" />
                                <label for="floatingInput" style="z-index: 1;">Qty</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly placeholder="Harga Total" value="" class="form-control hargaTotalPembelian_material_2" id="hargaTotalPembelian_material_2" name="hargaTotalPembelian_material_2" aria-label="Floating label select example" />
                                <label for="floatingInput" style="z-index: 1;">Harga Total</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly placeholder="Rata-rata Harga Satuan" value="" class="form-control hargaSatuanPembelian_material_2" id="hargaSatuanPembelian_material_2" name="hargaSatuanPembelian_material_2" aria-label="Floating label select example" />
                                <label for="floatingInput" style="z-index: 1;">Rata-rata Harga Satuan</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-subtitle-modal">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold modal-sub-title">Data Barang Jadi</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover-tobasurimi dataTable" id="selectedItemTableRasioMaterialII" width="100%" border="1" cellspacing="0">
                                    <thead class="thead-dark head-table-rasio-material2">
                                        <tr>
                                            <th style="text-align: center;" rowspan="2">No</th>
                                            <th style="text-align: center;" rowspan="2">Kode Barang Digunakan</th>
                                            <th style="text-align: center;" rowspan="2">Nama Barang Digunakan</th>
                                            <th style="text-align: center;" rowspan="2">Satuan Barang Digunakan</th>
                                            <th style="text-align: center;" rowspan="2">Qty Digunakan</th>
                                            <th style="text-align: center;" colspan="3">Nama Barang Jadi</th>
                                        </tr>
                                        <tr>
                                            <th style="text-align: center;">Qty</th>
                                            <th style="text-align: center;">Total Harga</th>
                                            <th style="text-align: center;">Harga </th>
                                        </tr>
                                    </thead>
                                    <tbody class="body-table-rasio-material2">
                                    </tbody>
                                    <tfoot style="background: #ffffff !important;" class="tfoot-rasio-material2" id="tfoot-rasio">
                                        <tr>
                                            <td colspan="7" style="text-align: center;">
                                                Tidak Ada Barang
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end card raw material II -->
                <!-- card raw material I -->
                <div id="costCard" style="display: none;">
                    <div class="col-subtitle-modal">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold modal-sub-title">DIRECT LABOR COST</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="selectedItemTableLaborCost" width="100%" border="1" cellspacing="0">
                                    <thead class="thead-dark head-table-labor-cost">
                                    </thead>
                                    <tbody class="body-table-labor-cost">
                                    </tbody>
                                    <tfoot class="tfoot-labor-cost" id="tfoot-labor-cost">
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-subtitle-modal">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold modal-sub-title">OVERHEAD COST</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="selectedItemTableLaborCost" width="100%" border="1" cellspacing="0">
                                    <thead class="thead-dark head-table-overhead-cost">
                                    </thead>
                                    <tbody class="body-table-overhead-cost">
                                    </tbody>
                                    <tfoot class="tfoot-overhead-cost" id="tfoot-overhead-cost">
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-subtitle-modal">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold modal-sub-title">FIXED OVERHEAD COST</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="selectedItemTableLaborCost" width="100%" border="1" cellspacing="0">
                                    <thead class="thead-dark head-table-fixed-overhead-cost">
                                    </thead>
                                    <tbody class="body-table-fixed-overhead-cost">
                                    </tbody>
                                    <tfoot class="tfoot-fixed-overhead-cost" id="tfoot-fixed-overhead-cost">
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end card raw material I -->
            </form>
        </div>
    </div>
</section>
<script>
    const csrfToken = '<?= csrf_token() ?>';

    let list_items_barang_jadi_trimming = [];
    let list_items_barang_jadi_kaleng = [];
    let list_items_barang_jadi_frozen = [];
    let list_items_barang_filling = [];
    let list_items_barang_digunakan = [];
    let list_items_barang_pembelian = [];
    let list_items_barang_digunakan_ulang = [];
    let list_items_barang_digunakan_alokasi = [];
    let list_items_saldo_awal = [];
    let list_items_saldo_akhir = [];
    let list_items_saldo_adjusment = [];
    let list_items_saldo_jual = [];
    let list_items_saldo_trimming = [];
    let list_items_saldo_kopek = [];
    let list_items_barang_jadi = [];
    let tipe_bahan = "";

    let list_items_barang_jadi_material_2 = [];
    let list_items_barang_digunakan_material_2 = [];

    let list_items_labor_cost = [];
    let list_items_title_cost = [];
    let list_items_overhead_cost = [];
    let list_items_fixed_cost = [];

    $("#tanggal_awal, #tanggal_akhir").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    }).change(function() {
        list_items_barang_jadi_trimming = [];
        list_items_barang_jadi_kaleng = [];
        list_items_barang_jadi_frozen = [];
        list_items_barang_filling = [];
        list_items_barang_jadi_material_2 = [];
        list_items_barang_digunakan = [];
        list_items_barang_pembelian = [];
        list_items_barang_digunakan_material_2 = [];
        list_items_labor_cost = [];
        list_items_title_cost = [];
        list_items_overhead_cost = [];
        list_items_fixed_cost = [];

        getDataRawMaterialI();
        getDataRawMaterialII();
        getDataCost();
    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        list_items_barang_jadi_trimming = [];
        list_items_barang_jadi_kaleng = [];
        list_items_barang_jadi_frozen = [];
        list_items_barang_filling = [];
        list_items_barang_jadi_material_2 = [];
        list_items_barang_digunakan = [];
        list_items_barang_pembelian = [];
        list_items_barang_digunakan_material_2 = [];
        list_items_labor_cost = [];
        list_items_title_cost = [];
        list_items_overhead_cost = [];
        list_items_fixed_cost = [];

        getDataRawMaterialI();
        getDataRawMaterialII();
        getDataCost();
        // getListWarehouseAsal()
    });

    $('#akun_pemakaian').select2({
        placeholder: "Pilih Akun",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        list_items_barang_jadi_trimming = [];
        list_items_barang_jadi_frozen = [];
        list_items_barang_filling = [];
        list_items_barang_jadi_material_2 = [];
        list_items_barang_digunakan = [];
        list_items_barang_pembelian = [];
        list_items_barang_digunakan_material_2 = [];
        list_items_labor_cost = [];
        list_items_title_cost = [];
        list_items_overhead_cost = [];
        list_items_fixed_cost = [];

        getDataRawMaterialI();
        getDataRawMaterialII();
        getDataCost();
        // getListWarehouseAsal()
    });

    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    });

    $("#divisi_id, #warehouse_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('#kategori').select2({
        placeholder: "Pilih Kategori Barang",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        list_items_barang_jadi_trimming = [];
        list_items_barang_jadi_kaleng = [];
        list_items_barang_jadi_frozen = [];
        list_items_barang_filling = [];
        list_items_barang_jadi_material_2 = [];
        list_items_barang_digunakan = [];
        list_items_barang_pembelian = [];
        list_items_barang_digunakan_material_2 = [];
        list_items_labor_cost = [];
        list_items_title_cost = [];
        list_items_overhead_cost = [];
        list_items_fixed_cost = [];

        getDataRawMaterialI();
        getDataRawMaterialII();
        getDataCost();
    });

    $("#kategori")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');


    // function getListWarehouseAsal() {
    //     setLoading();
    //     // GET LIST WAREHOUSE ASAL
    //     $.ajax({
    //         url: `<?= base_url('mutasi/warehouse'); ?>`,
    //         method: "GET",
    //         data: {
    //             divisi_id: $(".divisi_id option:selected").val(),
    //         },
    //         dataType: "json",
    //         success: function(res) {
    //             $(".warehouse_id").empty()
    //             $(".warehouse_id").append(`<option value=""></option>`)
    //             res.data.forEach(function(item) {
    //                 $(".warehouse_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
    //             })
    //             stopLoading();
    //         }
    //     });
    // }

    const getDataJurnalSubsidi = function() {
        var department_id = $('#divisi_id').val();
        var tanggal_awal = $('#tanggal_awal').val();
        var tanggal_akhir = $('#tanggal_akhir').val();
        var coa_id = $('#akun_coa_subsidi').val();
        if (department_id && tanggal_awal && tanggal_akhir && coa_id) {
            setLoading();
            $.ajax({
                url: `<?= base_url('rasio/get-jurnal'); ?>`,
                method: "GET",
                data: {
                    department: department_id,
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir,
                    id_coa: coa_id,
                },
                dataType: "json",
                success: function(res) {
                    stopLoading()
                    if (res.status) {
                        let qtyTotalDigunakan = parseFloat($('#qtyTotalDigunakan').val()) || 0;
                        let hargaTotalDigunakan = parseFloat($('#hargaTotalDigunakan').val().replace(/[Rp.]/g, '')) || 0;
                        let biayaSubsidi = parseFloat(res.data);
                        let biayaLain = parseFloat($('#biayaLain').val().replace(/[Rp.]/g, '')) || 0;
                        let biayaKopek = parseFloat($('#biayaKopek').val().replace(/[Rp.]/g, '')) || 0;
                        $('#biayaSubsidi').val(formatRupiah(biayaSubsidi));

                        $('#qtyTotalSetelahAlokasi').val(qtyTotalDigunakan.toLocaleString());
                        $('#hargaTotalSetelahAlokasi').val(formatRupiah(hargaTotalDigunakan + biayaSubsidi + biayaLain + biayaKopek));
                        $('#hargaSatuanSetelahAlokasi').val(formatRupiah((hargaTotalDigunakan + biayaSubsidi + biayaLain + biayaKopek) / qtyTotalDigunakan));
                    } else {
                        stopLoading()
                        $('#biayaSubsidi').val("");
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Jurnal Tidak Ada',
                            confirmButtonColor: '#4e73df',
                        })
                    }
                },
            });
        } else {
            $('#biayaSubsidi').val("");
        }
    }
    const getDataJurnalLain = function() {
        var department_id = $('#divisi_id').val();
        var tanggal_awal = $('#tanggal_awal').val();
        var tanggal_akhir = $('#tanggal_akhir').val();
        var coa_id = $('#akun_coa_biaya').val();
        if (department_id && tanggal_awal && tanggal_akhir && coa_id) {
            setLoading();
            $.ajax({
                url: `<?= base_url('rasio/get-jurnal'); ?>`,
                method: "GET",
                data: {
                    department: department_id,
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir,
                    id_coa: coa_id,
                },
                dataType: "json",
                success: function(res) {
                    stopLoading()
                    if (res.status) {
                        let qtyTotalDigunakan = parseFloat($('#qtyTotalDigunakan').val()) || 0;
                        let hargaTotalDigunakan = parseFloat($('#hargaTotalDigunakan').val().replace(/[Rp.]/g, '')) || 0;
                        let biayaSubsidi = parseFloat($('#biayaSubsidi').val().replace(/[Rp.]/g, '')) || 0;
                        let biayaLain = parseFloat(res.data);
                        let biayaKopek = parseFloat($('#biayaKopek').val().replace(/[Rp.]/g, '')) || 0;
                        $('#biayaLain').val(formatRupiah(biayaLain));

                        $('#qtyTotalSetelahAlokasi').val(qtyTotalDigunakan.toLocaleString());
                        $('#hargaTotalSetelahAlokasi').val(formatRupiah(hargaTotalDigunakan + biayaSubsidi + biayaLain + biayaKopek));
                        $('#hargaSatuanSetelahAlokasi').val(formatRupiah((hargaTotalDigunakan + biayaSubsidi + biayaLain + biayaKopek) / qtyTotalDigunakan));
                    } else {
                        stopLoading()
                        $('#biayaLain').val("");
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Jurnal Tidak Ada',
                            confirmButtonColor: '#4e73df',
                        })
                    }
                },
            });
        } else {
            $('#biayaLain').val("");
        }
    }
    const getDataJurnalKopek = function() {
        var department_id = $('#divisi_id').val();
        var tanggal_awal = $('#tanggal_awal').val();
        var tanggal_akhir = $('#tanggal_akhir').val();
        var coa_id = $('#akun_coa_kopek').val();
        if (department_id && tanggal_awal && tanggal_akhir && coa_id) {
            setLoading();
            $.ajax({
                url: `<?= base_url('rasio/get-jurnal'); ?>`,
                method: "GET",
                data: {
                    department: department_id,
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir,
                    id_coa: coa_id,
                },
                dataType: "json",
                success: function(res) {
                    stopLoading()
                    if (res.status) {
                        let qtyTotalDigunakan = parseFloat($('#qtyTotalDigunakan').val()) || 0;
                        let hargaTotalDigunakan = parseFloat($('#hargaTotalDigunakan').val().replace(/[Rp.]/g, '')) || 0;
                        let biayaSubsidi = parseFloat($('#biayaSubsidi').val().replace(/[Rp.]/g, '')) || 0;
                        let biayaLain = parseFloat($('#biayaLain').val().replace(/[Rp.]/g, '')) || 0;
                        let biayaKopek = parseFloat(res.data);
                        $('#biayaKopek').val(formatRupiah(biayaKopek));

                        $('#qtyTotalSetelahAlokasi').val(qtyTotalDigunakan.toLocaleString());
                        $('#hargaTotalSetelahAlokasi').val(formatRupiah(hargaTotalDigunakan + biayaSubsidi + biayaLain + biayaKopek));
                        $('#hargaSatuanSetelahAlokasi').val(formatRupiah((hargaTotalDigunakan + biayaSubsidi + biayaLain + biayaKopek) / qtyTotalDigunakan));
                    } else {
                        stopLoading()
                        $('#biayaKopek').val("");
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Jurnal Tidak Ada',
                            confirmButtonColor: '#4e73df',
                        })
                    }
                },
            });
        } else {
            $('#biayaKopek').val("");
        }
    }
    const getDataRawMaterialI = function() {
        var department_id = $('#divisi_id').val();
        var tanggal_awal = $('#tanggal_awal').val();
        var tanggal_akhir = $('#tanggal_akhir').val();
        var kategori = $('#kategori').val();
        if (department_id && tanggal_awal && tanggal_akhir && kategori) {
            setLoading();
            $.ajax({
                url: `<?= base_url('rasio/get-material-i'); ?>`,
                method: "GET",
                data: {
                    department: department_id,
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir,
                    kategori: kategori,
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        list_items_barang_digunakan = [];
                        list_items_barang_pembelian = [];
                        list_items_barang_digunakan_ulang = [];
                        list_items_barang_filling = [];
                        list_items_barang_jadi_trimming = [];
                        list_items_barang_jadi_kaleng = [];
                        list_items_barang_jadi_frozen = [];
                        list_items_saldo_awal = [];
                        list_items_saldo_akhir = [];
                        list_items_saldo_adjusment = [];
                        list_items_saldo_jual = [];
                        let no = 0;
                        let barang1IdQtyMap = {};
                        // Iterate over each item in the response data
                        // bahan digunakan pembelian
                        res.dataBahanDigunakanPO.forEach(function(item) {
                            list_items_barang_pembelian.push(item);
                        });
                        // bahan digunakan produksi
                        res.dataProduksiBahanDigunakan.forEach(function(item) {
                            if (item.barang_name != undefined) {
                                list_items_barang_digunakan.push(item);
                            }
                        });
                        // bahan digunakan proses ulang
                        res.dataProduksiBahanDigunakanProsesUlang.forEach(function(item) {
                            list_items_barang_digunakan_ulang.push(item);
                        });
                        // saldo awal
                        list_items_saldo_awal = res.dataSaldoAwal;
                        // saldo akhir
                        list_items_saldo_akhir = res.dataSaldoAkhir;
                        // saldo adjustment
                        list_items_saldo_adjusment = res.dataSaldoAdjusment;
                        // saldo jual
                        list_items_saldo_jual = res.dataSaldoMutasi;
                        // bahan jadi produksi
                        res.dataBarangJadi.forEach(function(item) {
                            let tipe_bahan;

                            if (item.barang_type == "bahan_setengah_jadi") {
                                tipe_bahan = "trimming";
                                list_items_barang_jadi_trimming.push({
                                    ...item,
                                    tipe_bahan: tipe_bahan
                                });
                            } else if (item.barang_type == "bahan_jadi" && item.kode_satuan == "CAN") {
                                tipe_bahan = "kaleng";
                                list_items_barang_jadi_kaleng.push({
                                    ...item,
                                    tipe_bahan: tipe_bahan
                                });
                            } else {
                                tipe_bahan = "frozen";
                                let barang1Id = item.barang1_id;
                                let barang2Id = item.barang2_id;

                                if (!barang1IdQtyMap[barang1Id]) {
                                    barang1IdQtyMap[barang1Id] = {};
                                }

                                if (barang1IdQtyMap[barang1Id][barang2Id]) {
                                    barang1IdQtyMap[barang1Id][barang2Id].qty = parseFloat(item.qty);
                                    barang1IdQtyMap[barang1Id][barang2Id].qty2 = parseFloat(item.qty2);
                                    barang1IdQtyMap[barang1Id][barang2Id].qty_isi = parseFloat(item.qty_isi);
                                    barang1IdQtyMap[barang1Id][barang2Id].banyakData = parseFloat(res.dataBarangJadi.length);
                                    barang1IdQtyMap[barang1Id][barang2Id].hasilWithPersentase = parseFloat(item.hasilWithPersentase);
                                } else {
                                    // Initialize a new entry for this barang1_id
                                    barang1IdQtyMap[barang1Id][barang2Id] = {
                                        barang1_id: barang1Id,
                                        tipe_bahan: tipe_bahan,
                                        barang2_id: barang2Id,
                                        barang_name: item.barang_name,
                                        kode_barang: item.kode_barang,
                                        kode_satuan: item.kode_satuan,
                                        spesifikasi: item.spesifikasi,
                                        production_result_detail_id: item.production_result_detail_id,
                                        production_result_id: item.production_result_id,
                                        satuan_id: item.satuan_id,
                                        bc_id: item.bc_id,
                                        stock_id: item.stock_id,
                                        no_aju: item.no_aju,
                                        stock_dokumen: item.stock_dokumen,
                                        qty: parseFloat(item.qty),
                                        qty2: parseFloat(item.qty2),
                                        qty_isi: parseFloat(item.qty_isi),
                                        banyakData: parseFloat(1),
                                        hasilWithPersentase: parseFloat(item.hasilWithPersentase),
                                    };
                                }
                            }
                        });

                        // Step 2: Convert the map to a list
                        for (let key in barang1IdQtyMap) {
                            if (barang1IdQtyMap.hasOwnProperty(key)) {
                                let group = barang1IdQtyMap[key];
                                let barang1IdArr = Object.values(barang1IdQtyMap[key]);
                                list_items_barang_jadi_frozen.push(barang1IdArr);

                            }
                        }

                        drawTablePembelian();
                        drawTableDigunakan();
                        drawTableDigunakanJadi();
                        drawTableSaldoAwal();
                        drawTableSaldoAkhir();
                        drawTableSaldoAdjusmentAnalisa();
                        drawTableSaldoAdjusmentSample();
                        drawTableSaldoAdjusmentBonus();
                        drawTableSaldoAdjusmentLainnya();
                        drawTableSaldoJual();
                        drawTableRasioTrimming();
                        drawTableRasioKaleng();
                        drawTableRasioFrozen();

                        hideShowTab()
                        stopLoading()

                    } else {
                        stopLoading()
                        list_items_barang_digunakan = [];
                        list_items_barang_pembelian = [];
                        list_items_barang_digunakan_ulang = [];
                        list_items_barang_jadi_trimming = [];
                        list_items_barang_jadi_kaleng = [];
                        list_items_barang_jadi_frozen = [];
                        list_items_saldo_awal = [];
                        list_items_saldo_akhir = [];
                        list_items_saldo_adjusment = [];
                        list_items_saldo_jual = [];

                        drawTablePembelian();
                        drawTableDigunakan();
                        drawTableDigunakanJadi();
                        drawTableSaldoAwal();
                        drawTableSaldoAkhir();
                        drawTableSaldoAdjusmentAnalisa();
                        drawTableSaldoAdjusmentSample();
                        drawTableSaldoAdjusmentBonus();
                        drawTableSaldoAdjusmentLainnya();
                        drawTableSaldoJual();
                        drawTableRasioTrimming();
                        drawTableRasioKaleng();
                        drawTableRasioFrozen();
                    }
                },
            });
        }
    }

    const getDataRawMaterialII = function() {
        var department_id = $('#divisi_id').val();
        var tanggal_awal = $('#tanggal_awal').val();
        var tanggal_akhir = $('#tanggal_akhir').val();
        var kategori = $('#kategori').val();
        var akun_pemakaian = $('#akun_pemakaian').val();
        if (department_id && tanggal_awal && tanggal_akhir && kategori && akun_pemakaian) {
            setLoading();
            $.ajax({
                url: `<?= base_url('rasio/get-barang-digunakan-penolong'); ?>`,
                method: "GET",
                data: {
                    department: department_id,
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir,
                },
                dataType: "json",
                success: function(res) {
                    stopLoading()
                    if (res.status) {
                        list_items_barang_digunakan_material_2 = [];
                        let no = 0;
                        // Iterate over each item in the response data
                        res.data.forEach(function(item) {
                            list_items_barang_digunakan_material_2.push(item);
                        });
                        drawTableDigunakanMaterialII();
                    } else {
                        stopLoading()
                        list_items_barang_digunakan_material_2 = [];
                        drawTableDigunakanMaterialII();
                    }
                },
            });
            $.ajax({
                url: `<?= base_url('rasio/get-barang-jadi'); ?>`,
                method: "GET",
                data: {
                    department: department_id,
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir,
                    kategori: kategori,
                },
                dataType: "json",
                success: function(res) {
                    stopLoading()
                    if (res.status) {
                        list_items_barang_jadi_material_2 = [];
                        // Iterate over each item in the response data
                        res.data.forEach(function(item) {
                            list_items_barang_jadi_material_2.push(item);
                            list_items_title_cost.push(item);
                        });
                        drawTableRasioMaterialII();
                        drawTableLaborCost();
                        drawTableOverheadCost();
                        drawTableFixedOverheadCost();
                    } else {
                        stopLoading()
                        list_items_barang_jadi_material_2 = [];
                        list_items_barang_digunakan_material_2 = [];
                        list_items_labor_cost = [];
                        list_items_title_cost = [];
                        list_items_overhead_cost = [];
                        list_items_fixed_cost = [];

                        drawTableDigunakanMaterialII();
                        drawTableLaborCost();
                        drawTableOverheadCost();
                        drawTableFixedOverheadCost();
                    }
                },
            });
        }
    }

    const getDataCost = function() {
        var department_id = $('#divisi_id').val();
        var tanggal_awal = $('#tanggal_awal').val();
        var tanggal_akhir = $('#tanggal_akhir').val();
        var kategori = $('#kategori').val();
        if (department_id && tanggal_awal && tanggal_akhir && kategori) {
            setLoading();
            $.ajax({
                url: `<?= base_url('rasio/get-cost'); ?>`,
                method: "GET",
                data: {
                    department: department_id,
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir,
                },
                dataType: "json",
                success: function(res) {
                    stopLoading()
                    if (res.status) {
                        list_items_labor_cost = [];
                        let no = 0;
                        // Iterate over each item in the response data
                        res.data.forEach(function(item) {
                            if (item.parent_id == 5) {
                                list_items_labor_cost.push(item);
                            } else if (item.parent_id == 6) {
                                list_items_overhead_cost.push(item);
                            } else if (item.parent_id == 7) {
                                list_items_fixed_cost.push(item);
                            }
                        });
                        drawTableLaborCost();
                        drawTableOverheadCost();
                        drawTableFixedOverheadCost();
                    } else {
                        stopLoading();
                        list_items_labor_cost = [];
                        drawTableLaborCost();
                        drawTableOverheadCost();
                        drawTableFixedOverheadCost();
                    }
                },
            });
        }
    }

    const drawTableDigunakanMaterialII = function() {
        $('.body-detail-table-digunakan-material-2').empty();
        $('.tfoot-detail-table-digunakan-material-2').empty();
        var row = '';
        var rowFooter = '';
        var no = 1;
        var strip = "-";
        if (list_items_barang_digunakan_material_2.length === 0) {
            row += '<tr><td colspan="10" class="text-center">Data Barang Tidak Ada</td></tr>';
            $('.tfoot-detail-table-digunakan-material-2').append(row);
        } else {
            var totalQtyPO = 0;
            var totalHargaPO = 0;
            var hargaSatuanPO = 0;
            var totalQtyLPB = 0;
            var totalHargaLPB = 0;
            var hargaSatuanLPB = 0;
            list_items_barang_digunakan_material_2.map((item, index) => {
                // counting total
                totalQtyPO += item.totalQtyPO !== undefined ? item.totalQtyPO : 0;
                totalHargaPO += item.totalHargaPO !== undefined ? item.totalHargaPO : 0;
                hargaSatuanPO += item.hargaSatuanPO !== undefined ? item.hargaSatuanPO : 0;
                totalQtyLPB += item.totalQtyLPB !== undefined ? item.totalQtyLPB : 0;
                totalHargaLPB += item.totalHargaLPB !== undefined ? item.totalHargaLPB : 0;
                hargaSatuanLPB += item.hargaSatuanLPB !== undefined ? item.hargaSatuanLPB : 0;
                // end counting
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.barang_name + ' - ' + item.spesifikasi + '</td>';
                row += '<td>' + (item.totalQtyPO !== undefined ? parseFloat(item.totalQtyPO).toLocaleString() : 0) + '</td>';
                row += '<td>' + (item.totalHargaPO !== undefined ? formatRupiah(parseFloat(item.totalHargaPO)) : formatRupiah(parseFloat(0))) + '</td>';
                row += '<td>' + (item.hargaSatuanPO !== undefined ? formatRupiah(item.hargaSatuanPO) : formatRupiah(parseFloat(0))) + '</td>';
                row += '<td>' + (item.satuanPO !== undefined ? item.satuanPO : strip) + '</td>';
                row += '</tr>';
                no++;
            });
            $('.qtyTotalPembelian_material_2').val(totalQtyPO.toLocaleString());
            $('.hargaTotalPembelian_material_2').val(formatRupiah(parseFloat(totalHargaPO)));
            $('.hargaSatuanPembelian_material_2').val(formatRupiah(parseFloat(hargaSatuanPO)));

            $('.body-detail-table-digunakan-material-2').append(row);
        }
    }

    const drawTablePembelian = function() {
        $('.body-detail-table').empty();
        $('.tfoot-detail-table').empty();
        var row = '';
        var rowFooter = '';
        var no = 1;
        var strip = "-";
        if (list_items_barang_pembelian.length === 0) {
            var totalQtyPO = 0;
            var totalHargaPO = 0;
            var hargaSatuanPO = 0;
            var totalQtyLPB = 0;
            var totalHargaLPB = 0;
            var hargaSatuanLPB = 0;
            row += '<tr><td colspan="10" class="text-center">Data Barang Tidak Ada</td></tr>';
            $('.qtyTotalPembelian').val(totalQtyPO.toLocaleString());
            $('.hargaTotalPembelian').val(formatRupiah(parseFloat(totalHargaPO)));
            $('.hargaSatuanPembelian').val(formatRupiah(parseFloat(hargaSatuanPO)));

            $('.qtyTotalPenerimaan').val(totalQtyLPB.toLocaleString());
            $('.hargaTotalPenerimaan').val(formatRupiah(parseFloat(totalHargaLPB)));
            $('.hargaSatuanPenerimaan').val(formatRupiah(parseFloat(hargaSatuanLPB)));
            $('.tfoot-detail-table').append(row);
        } else {
            var totalQtyPO = 0;
            var totalHargaPO = 0;
            var hargaSatuanPO = 0;
            var totalQtyLPB = 0;
            var totalHargaLPB = 0;
            var hargaSatuanLPB = 0;
            list_items_barang_pembelian.map((item, index) => {
                // counting total
                totalQtyPO += item.totalQtyPO !== undefined ? item.totalQtyPO : 0;
                totalHargaPO += item.totalHargaPO !== undefined ? item.totalHargaPO : 0;
                hargaSatuanPO += item.hargaSatuanPO !== undefined ? item.hargaSatuanPO : 0;
                totalQtyLPB += item.totalQtyLPB !== undefined ? item.totalQtyLPB : 0;
                totalHargaLPB += item.totalHargaLPB !== undefined ? item.totalHargaLPB : 0;
                hargaSatuanLPB += item.hargaSatuanLPB !== undefined ? item.hargaSatuanLPB : 0;
                // end counting
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.barang_name + ' - ' + item.spesifikasi + '</td>';
                row += '<td>' + (item.totalQtyPO !== undefined ? parseFloat(item.totalQtyPO).toLocaleString() : 0) + '</td>';
                row += '<td>' + (item.totalHargaPO !== undefined ? formatRupiah(parseFloat(item.totalHargaPO)) : formatRupiah(parseFloat(0))) + '</td>';
                row += '<td>' + (item.hargaSatuanPO !== undefined ? formatRupiah(parseFloat(item.hargaSatuanPO)) : formatRupiah(parseFloat(0))) + '</td>';
                row += '<td>' + (item.satuanPO !== undefined ? item.satuanPO : strip) + '</td>';
                row += '<td>' +
                    '<input class="form-control qty-lpb text-center" oninput="preventNegativeInput(this)" type="text" data-index="' + index + '" value="' + (item.totalQtyLPB !== undefined ? item.totalQtyLPB.toLocaleString() : 0) + '">' +
                    '</td>';
                row += '<td>' + (item.totalHargaLPB !== undefined ? formatRupiah(parseFloat(item.totalHargaLPB)) : formatRupiah(parseFloat(0))) + '</td>';
                row += '<td class="totalHargaLPB">' + (item.hargaSatuanLPB !== undefined ? formatRupiah(parseFloat(item.hargaSatuanLPB)) : formatRupiah(parseFloat(0))) + '</td>';
                row += '<td>' + (item.satuanLPB !== undefined ? item.satuanLPB : strip) + '</td>';
                row += '</tr>';
                no++;
            });
            $('.qtyTotalPembelian').val(totalQtyPO.toLocaleString());
            $('.hargaTotalPembelian').val(formatRupiah(parseFloat(totalHargaPO)));
            $('.hargaSatuanPembelian').val(formatRupiah(parseFloat(hargaSatuanPO)));

            $('.qtyTotalPenerimaan').val(totalQtyLPB.toLocaleString());
            $('.hargaTotalPenerimaan').val(formatRupiah(parseFloat(totalHargaLPB)));
            $('.hargaSatuanPenerimaan').val(formatRupiah(parseFloat(hargaSatuanLPB)));

            $('.body-detail-table').append(row);

            $('.qty-lpb').on('input', function() {
                var rowIndex = $(this).data('index');
                var newQty = parseFloat($(this).val().replace(/,/g, '')) || 0;
                var totalHargaLPB = list_items_barang_pembelian[rowIndex].totalHargaLPB;
                var newHargaSatuan = totalHargaLPB / newQty;

                // Update the item in the list
                list_items_barang_pembelian[rowIndex].totalQtyLPB = newQty;
                list_items_barang_pembelian[rowIndex].hargaSatuanLPB = newHargaSatuan;

                // Update the totalHargaLPB cell in the table
                $(this).closest('tr').find('.totalHargaLPB').text(formatRupiah(parseFloat(newHargaSatuan)));

                // Recalculate the totals and update the footer
                totalQtyLPB = list_items_barang_pembelian.reduce((acc, item) => acc + (item.totalQtyLPB || 0), 0);
                totalhargaSatuanLPB = list_items_barang_pembelian.reduce((acc, item) => acc + (item.hargaSatuanLPB || 0), 0);

                $('.qtyTotalPenerimaan').val(totalQtyLPB.toLocaleString());
                $('.hargaSatuanPenerimaan').val(formatRupiah(parseFloat(totalhargaSatuanLPB)));
            });
        }
    }

    const drawTableDigunakan = function() {
        $('.body-detail-table-digunakan').empty();
        $('.tfoot-detail-table-digunakan').empty();
        var row = '';
        var rowFooter = '';
        var no = 1;
        var strip = "-";
        if (list_items_barang_digunakan.length === 0) {
            row += '<tr><td colspan="6" class="text-center">Data Barang Tidak Ada</td></tr>';
            $('.tfoot-detail-table-digunakan').append(row);
        } else {
            var totalQtyPO = 0;
            var totalHargaPO = 0;
            var hargaSatuanPO = 0;
            var totalQtySummary = 0;
            var totalHargaSummary = 0;
            var hargaSatuanSummary = 0;
            list_items_barang_digunakan.map((item, index) => {
                // counting total
                totalQtyPO = item.totalQtyPO !== undefined ? parseFloat(item.totalQtyPO) : 0;
                totalHargaPO = item.totalHargaPO !== undefined ? parseFloat(item.totalHargaPO) : 0;
                hargaSatuanPO = item.hargaSatuanPO !== undefined ? parseFloat(item.hargaSatuanPO) : 0;

                totalQtySummary += totalQtyPO;
                totalHargaSummary += totalHargaPO;
                hargaSatuanSummary += hargaSatuanPO;
                // end counting
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.barang_name + ' - ' + item.spesifikasi + '</td>';
                row += '<td>' + (item.totalQtyPO !== undefined ? parseFloat(item.totalQtyPO).toLocaleString() : 0) + '</td>';
                row += '<td>' + (item.totalHargaPO !== undefined ? formatRupiah(parseFloat(item.totalHargaPO)) : formatRupiah(parseFloat(0))) + '</td>';
                row += '<td>' + (item.hargaSatuanPO !== undefined ? formatRupiah(parseFloat(item.hargaSatuanPO)) : formatRupiah(parseFloat(0))) + '</td>';
                row += '<td>' + (item.satuanPO !== undefined ? item.satuanPO : strip) + '</td>';
                row += '</tr>';
                no++;
            });

            $('.body-detail-table-digunakan').append(row);
            $('.qtyTotalDigunakan').val(parseFloat(totalQtySummary).toLocaleString());
            $('.hargaTotalDigunakan').val(formatRupiah(parseFloat(totalHargaSummary)));
            $('.hargaSatuanDigunakan').val(formatRupiah(parseFloat(hargaSatuanSummary)));
        }
    }

    const drawTableDigunakanAlokasi = function(data) {
        $('.body-detail-table-alokasi').empty();
        $('.tfoot-detail-table-alokasi').empty();
        var row = '';
        var rowFooter = '';
        var no = 1;
        var strip = "-";

        if (data.length === 0) {
            var totalQty = 0;
            var totalHarga = 0;
            var hargaSatuan = 0;
            row += '<tr><td colspan="6" class="text-center">Data Barang Tidak Ada</td></tr>';
            $('.tfoot-detail-table-alokasi').append(row);
        } else {
            var totalQty = 0;
            var totalHarga = 0;
            var hargaSatuan = 0;
            data.map((item, index) => {
                // counting total
                totalQty += item.totalQty !== undefined ? item.totalQty : 0;
                totalHarga += item.totalHarga !== undefined ? item.totalHarga : 0;
                hargaSatuan += item.hargaSatuan !== undefined ? item.hargaSatuan : 0;
                // end counting
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.barang_name + ' - ' + item.spesifikasi + '</td>';
                row += '<td>' + (item.totalQty !== undefined ? parseFloat(item.totalQty).toLocaleString() : 0) + '</td>';
                row += '<td>' + (item.totalHarga !== undefined ? formatRupiah(parseFloat(item.totalHarga)) : formatRupiah(parseFloat(0))) + '</td>';
                row += '<td>' + (item.hargaSatuan !== undefined ? formatRupiah(parseFloat(item.hargaSatuan)) : formatRupiah(parseFloat(0))) + '</td>';
                row += '<td>' + (item.satuanPO !== undefined ? item.satuanPO : strip) + '</td>';
                row += '</tr>';
                no++;
            });
            $('.body-detail-table-alokasi').append(row);

            let qtyTotalDigunakan = parseFloat($('#qtyTotalDigunakan').val()) || 0;
            let hargaTotalDigunakan = parseFloat(($('#hargaTotalDigunakan').val() || '').replace(/[Rp.]/g, '')) || 0;
            let biayaSubsidi = parseFloat(($('#biayaSubsidi').val() || '').replace(/[Rp.]/g, '')) || 0;
            let biayaLain = parseFloat(($('#biayaLain').val() || '').replace(/[Rp.]/g, '')) || 0;
            let biayaKopek = parseFloat(($('#biayaKopek').val() || '').replace(/[Rp.]/g, '')) || 0;

            $('#qtyTotalSetelahAlokasi').val(qtyTotalDigunakan.toLocaleString());
            $('#hargaTotalSetelahAlokasi').val(formatRupiah(hargaTotalDigunakan + biayaSubsidi + biayaLain + biayaKopek));
            $('#hargaSatuanSetelahAlokasi').val(formatRupiah((hargaTotalDigunakan + biayaSubsidi + biayaLain + biayaKopek) / qtyTotalDigunakan));
        }
    }

    const drawTableDigunakanJadi = function() {
        $('.body-detail-table-barang-proses-ulang').empty();
        $('.tfoot-detail-table-barang-proses-ulang').empty();
        var row = '';
        var rowFooter = '';
        var no = 1;
        var strip = "-";
        if (list_items_barang_digunakan_ulang.length === 0) {
            var totalQtyPO = 0;
            var totalHargaPO = 0;
            var hargaSatuanPO = 0;
            var totalQtyLPB = 0;
            var totalHargaLPB = 0;
            var hargaSatuanLPB = 0;
            row += '<tr><td colspan="10" class="text-center">Data Barang Tidak Ada</td></tr>';

            $('.tfoot-detail-table-barang-proses-ulang').append(row);
        } else {
            var totalQtyPO = 0;
            var totalHargaPO = 0;
            var hargaSatuanPO = 0;
            var totalQtyLPB = 0;
            var totalHargaLPB = 0;
            var hargaSatuanLPB = 0;
            list_items_barang_digunakan_ulang.map((item, index) => {
                // counting total
                totalQtyPO += item.totalQtyPO !== undefined ? item.totalQtyPO : 0;
                totalHargaPO += item.totalHargaPO !== undefined ? item.totalHargaPO : 0;
                hargaSatuanPO += item.hargaSatuanPO !== undefined ? item.hargaSatuanPO : 0;
                totalQtyLPB += item.totalQtyLPB !== undefined ? item.totalQtyLPB : 0;
                totalHargaLPB += item.totalHargaLPB !== undefined ? item.totalHargaLPB : 0;
                hargaSatuanLPB += item.hargaSatuanLPB !== undefined ? item.hargaSatuanLPB : 0;
                // end counting
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.barang_name + ' - ' + item.spesifikasi + '</td>';
                row += '<td>' + (item.totalQtyPO !== undefined ? parseFloat(item.totalQtyPO).toLocaleString() : 0) + '</td>';
                row += '<td>' + (item.totalHargaPO !== undefined ? formatRupiah(parseFloat(item.totalHargaPO)) : formatRupiah(parseFloat(0))) + '</td>';
                row += '<td>' + (item.hargaSatuanPO !== undefined ? formatRupiah(parseFloat(item.hargaSatuanPO)) : formatRupiah(parseFloat(0))) + '</td>';
                row += '<td>' + (item.satuanPO !== undefined ? item.satuanPO : strip) + '</td>';
                row += '<td>' + (item.totalQtyLPB !== undefined ? item.totalQtyLPB.toLocaleString() : 0) + '</td>';
                row += '<td>' + (item.totalHargaLPB !== undefined ? formatRupiah(parseFloat(item.totalHargaLPB)) : formatRupiah(parseFloat(0))) + '</td>';
                row += '<td>' + (item.hargaSatuanLPB !== undefined ? formatRupiah(parseFloat(item.hargaSatuanLPB)) : formatRupiah(parseFloat(0))) + '</td>';
                row += '<td>' + (item.satuanLPB !== undefined ? item.satuanLPB : strip) + '</td>';
                row += '</tr>';
                no++;
            });
            $('.body-detail-table-barang-proses-ulang').append(row);
        }
    }

    const drawTableSaldoAkhir = function() {
        $('.body-detail-table-saldo-akhir').empty();
        $('.tfoot-detail-table-saldo-akhir').empty();
        var row = '';
        var rowFooter = '';
        var no = 1;
        var strip = "-";
        if (list_items_saldo_akhir.length === 0) {
            row += '<tr><td colspan="6" class="text-center">Data Barang Tidak Ada</td></tr>';

            $('.tfoot-detail-table-saldo-akhir').append(row);
        } else {
            var hargaUmum = 0;
            var hargaHarian = 0;
            var hargaBulanan = 0;
            var stok = 0;
            var totalHarga = 0;
            var totalHargaSatuan = 0;

            var summaryStok = 0;
            var summaryTotalHarga = 0;
            var summaryTotalHargaSatuan = 0;
            list_items_saldo_akhir.map((item, index) => {
                // counting total
                hargaUmum += item.harga_umum !== null ? parseFloat(item.harga_umum) : 0;
                hargaHarian += item.harga_harian !== null ? parseFloat(item.harga_harian) : 0;
                hargaBulanan += item.harga_bulanan !== null ? parseFloat(item.harga_bulanan) : 0;
                stok = item.stok_total !== null ? parseFloat(item.stok_total) : 0;
                totalHarga = (hargaUmum + hargaHarian + hargaBulanan) * stok;
                totalHargaSatuan = (hargaUmum + hargaHarian + hargaBulanan);

                summaryStok += stok;
                summaryTotalHargaSatuan += totalHargaSatuan;
                summaryTotalHarga += totalHarga;
                // end counting
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.barang + '</td>';
                row += '<td>' + (stok !== 0 ? parseFloat(stok).toLocaleString() : 0) + '</td>';
                row += '<td>' + (item.satuan !== undefined ? item.satuan : strip) + '</td>';
                row += '<td>' + (totalHargaSatuan !== 0 ? formatRupiah(parseFloat(totalHargaSatuan)) : formatRupiah(parseFloat(0))) + '</td>';
                row += '<td>' + (totalHarga !== 0 ? formatRupiah(parseFloat(totalHarga)) : formatRupiah(parseFloat(0))) + '</td>';
                row += '</tr>';
                no++;
            });

            rowFooter += '<tr style="text-align: center;">';
            rowFooter += '<td style="text-align: center; font-weight: bold;" colspan="2">Total</td>';
            rowFooter += '<td style="text-align: center; font-weight: bold;">' + (summaryStok !== 0 ? parseFloat(summaryStok).toLocaleString() : 0) + '</td>';
            rowFooter += '<td style="text-align: center; font-weight: bold;"></td>';
            rowFooter += '<td style="text-align: center; font-weight: bold;">' + (summaryTotalHargaSatuan !== 0 ? formatRupiah(parseFloat(summaryTotalHargaSatuan)) : formatRupiah(parseFloat(0))) + '</td>';
            rowFooter += '<td style="text-align: center; font-weight: bold;">' + (summaryTotalHarga !== 0 ? formatRupiah(parseFloat(summaryTotalHarga)) : formatRupiah(parseFloat(0))) + '</td>';
            rowFooter += '</tr>';

            $('.body-detail-table-saldo-akhir').append(row);
            $('.tfoot-detail-table-saldo-akhir').append(rowFooter);
        }
    }

    const drawTableSaldoAwal = function() {
        $('.body-detail-table-saldo-awal').empty();
        $('.tfoot-detail-table-saldo-awal').empty();
        var row = '';
        var rowFooter = '';
        var no = 1;
        var strip = "-";
        if (list_items_saldo_awal.length === 0) {
            row += '<tr><td colspan="6" class="text-center">Data Barang Tidak Ada</td></tr>';

            $('.tfoot-detail-table-saldo-awal').append(row);
        } else {
            var hargaUmum = 0;
            var hargaHarian = 0;
            var hargaBulanan = 0;
            var stok = 0;
            var stokProduksi = 0;
            var totalHarga = 0;
            var totalHargaSatuan = 0;

            var summaryStok = 0;
            var summaryTotalHarga = 0;
            var summaryTotalHargaSatuan = 0;

            list_items_saldo_awal.map((item, index) => {

                // counting total
                hargaUmum = item.harga_umum !== null ? parseFloat(item.harga_umum) : 0;
                hargaHarian = item.harga_harian !== null ? parseFloat(item.harga_harian) : 0;
                hargaBulanan = item.harga_bulanan !== null ? parseFloat(item.harga_bulanan) : 0;
                stok = item.stok_total !== null ? parseFloat(item.stok_total) : 0;
                totalHarga = (hargaUmum + hargaHarian + hargaBulanan) * stok;
                totalHargaSatuan = (hargaUmum + hargaHarian + hargaBulanan);

                summaryStok += stok;
                summaryTotalHargaSatuan += totalHargaSatuan;
                summaryTotalHarga += totalHarga;
                // end counting
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.barang + '</td>';
                row += '<td>' + (stok !== 0 ? parseFloat(stok).toLocaleString() : 0) + '</td>';
                row += '<td>' + (item.satuan !== undefined ? item.satuan : strip) + '</td>';
                row += '<td>' + (totalHargaSatuan !== 0 ? formatRupiah(parseFloat(totalHargaSatuan)) : formatRupiah(parseFloat(0))) + '</td>';
                row += '<td>' + (totalHarga !== 0 ? formatRupiah(parseFloat(totalHarga)) : formatRupiah(parseFloat(0))) + '</td>';
                row += '</tr>';
                no++;
            });

            rowFooter += '<tr style="text-align: center;">';
            rowFooter += '<td style="text-align: center; font-weight: bold;" colspan="2">Total</td>';
            rowFooter += '<td style="text-align: center; font-weight: bold;">' + (summaryStok !== 0 ? parseFloat(summaryStok).toLocaleString() : 0) + '</td>';
            rowFooter += '<td style="text-align: center; font-weight: bold;"></td>';
            rowFooter += '<td style="text-align: center; font-weight: bold;">' + (summaryTotalHargaSatuan !== 0 ? formatRupiah(parseFloat(summaryTotalHargaSatuan)) : formatRupiah(parseFloat(0))) + '</td>';
            rowFooter += '<td style="text-align: center; font-weight: bold;">' + (summaryTotalHarga !== 0 ? formatRupiah(parseFloat(summaryTotalHarga)) : formatRupiah(parseFloat(0))) + '</td>';
            rowFooter += '</tr>';

            $('.body-detail-table-saldo-awal').append(row);
            $('.tfoot-detail-table-saldo-awal').append(rowFooter);
        }
    }

    const drawTableSaldoAdjusmentAnalisa = function() {
        $('.body-detail-table-saldo-adjust-analisa').empty();
        $('.tfoot-detail-table-saldo-adjust-analisa').empty();
        var row = '';
        var rowFooter = '';
        var no = 1;
        var strip = "-";
        if (list_items_saldo_adjusment.length === 0) {
            row += '<tr><td colspan="6" class="text-center">Data Barang Tidak Ada</td></tr>';

            $('.tfoot-detail-table-saldo-adjust-analisa').append(row);
        } else {
            var hargaUmum = 0;
            var hargaHarian = 0;
            var hargaBulanan = 0;
            var stok = 0;
            var stokProduksi = 0;
            var totalHarga = 0;
            list_items_saldo_adjusment.map((item, index) => {
                // counting total
                if (item.tipe_adjusment == "ANALISA") {
                    hargaUmum += item.harga_umum !== null ? parseFloat(item.harga_umum) : 0;
                    hargaHarian += item.harga_harian !== null ? parseFloat(item.harga_harian) : 0;
                    hargaBulanan += item.harga_bulanan !== null ? parseFloat(item.harga_bulanan) : 0;
                    stok = item.qty !== null ? parseFloat(item.qty) : 0;
                    // stokProduksi = item.stok_produksi !== null ? parseFloat(item.stok_produksi) : 0;
                    totalStok = stok;
                    totalHarga = (hargaUmum + hargaHarian + hargaBulanan) * totalStok;
                    // end counting
                    row += '<tr style="color:whitesmoke;text-align: center;">';
                    row += '<td>' + no + '</td>';
                    row += '<td>' + item.barang + '</td>';
                    row += '<td>' + (totalStok !== 0 ? parseFloat(totalStok).toLocaleString() : 0) + '</td>';
                    row += '<td>' + (item.satuan !== undefined ? item.satuan : strip) + '</td>';
                    row += '<td>' + (totalHarga !== 0 ? formatRupiah(parseFloat(totalHarga)) : formatRupiah(parseFloat(0))) + '</td>';
                    row += '</tr>';
                    no++;
                } else {
                    row += '<tr style="color:whitesmoke;text-align: center;"><td colspan="6" class="text-center">Data Barang Tidak Ada</td></tr>';
                }
            });
            $('.body-detail-table-saldo-adjust-analisa').append(row);
        }
    }

    const drawTableSaldoAdjusmentSample = function() {
        $('.body-detail-table-saldo-adjust-sample').empty();
        $('.tfoot-detail-table-saldo-adjust-sample').empty();
        var row = '';
        var rowFooter = '';
        var no = 1;
        var strip = "-";
        if (list_items_saldo_adjusment.length === 0) {
            row += '<tr><td colspan="6" class="text-center">Data Barang Tidak Ada</td></tr>';

            $('.tfoot-detail-table-saldo-adjust-sample').append(row);
        } else {
            var hargaUmum = 0;
            var hargaHarian = 0;
            var hargaBulanan = 0;
            var stok = 0;
            var stokProduksi = 0;
            var totalHarga = 0;
            list_items_saldo_adjusment.map((item, index) => {
                // counting total
                if (item.tipe_adjusment == "SAMPLE") {
                    hargaUmum += item.harga_umum !== null ? parseFloat(item.harga_umum) : 0;
                    hargaHarian += item.harga_harian !== null ? parseFloat(item.harga_harian) : 0;
                    hargaBulanan += item.harga_bulanan !== null ? parseFloat(item.harga_bulanan) : 0;
                    stok = item.qty !== null ? parseFloat(item.qty) : 0;
                    // stokProduksi = item.stok_produksi !== null ? parseFloat(item.stok_produksi) : 0;
                    totalStok = stok;
                    totalHarga = (hargaUmum + hargaHarian + hargaBulanan) * totalStok;
                    // end counting
                    row += '<tr style="color:whitesmoke;text-align: center;">';
                    row += '<td>' + no + '</td>';
                    row += '<td>' + item.barang + '</td>';
                    row += '<td>' + (totalStok !== 0 ? parseFloat(totalStok).toLocaleString() : 0) + '</td>';
                    row += '<td>' + (item.satuan !== undefined ? item.satuan : strip) + '</td>';
                    row += '<td>' + (totalHarga !== 0 ? formatRupiah(parseFloat(totalHarga)) : formatRupiah(parseFloat(0))) + '</td>';
                    row += '</tr>';
                    no++;
                } else {
                    row += '<tr style="color:whitesmoke;text-align: center;"><td colspan="6" class="text-center">Data Barang Tidak Ada</td></tr>';
                }
            });
            $('.body-detail-table-saldo-adjust-sample').append(row);
        }
    }

    const drawTableSaldoAdjusmentBonus = function() {
        $('.body-detail-table-saldo-adjust-bonus').empty();
        $('.tfoot-detail-table-saldo-adjust-bonus').empty();
        var row = '';
        var rowFooter = '';
        var no = 1;
        var strip = "-";
        if (list_items_saldo_adjusment.length === 0) {
            row += '<tr><td colspan="6" class="text-center">Data Barang Tidak Ada</td></tr>';

            $('.tfoot-detail-table-saldo-adjust-bonus').append(row);
        } else {
            var hargaUmum = 0;
            var hargaHarian = 0;
            var hargaBulanan = 0;
            var stok = 0;
            var stokProduksi = 0;
            var totalHarga = 0;
            list_items_saldo_adjusment.map((item, index) => {
                // counting total
                if (item.tipe_adjusment == "BONUS") {
                    hargaUmum += item.harga_umum !== null ? parseFloat(item.harga_umum) : 0;
                    hargaHarian += item.harga_harian !== null ? parseFloat(item.harga_harian) : 0;
                    hargaBulanan += item.harga_bulanan !== null ? parseFloat(item.harga_bulanan) : 0;
                    stok = item.qty !== null ? parseFloat(item.qty) : 0;
                    // stokProduksi = item.stok_produksi !== null ? parseFloat(item.stok_produksi) : 0;
                    totalStok = stok;
                    totalHarga = (hargaUmum + hargaHarian + hargaBulanan) * totalStok;
                    // end counting
                    row += '<tr style="color:whitesmoke;text-align: center;">';
                    row += '<td>' + no + '</td>';
                    row += '<td>' + item.barang + '</td>';
                    row += '<td>' + (totalStok !== 0 ? parseFloat(totalStok).toLocaleString() : 0) + '</td>';
                    row += '<td>' + (item.satuan !== undefined ? item.satuan : strip) + '</td>';
                    row += '<td>' + (totalHarga !== 0 ? formatRupiah(parseFloat(totalHarga)) : formatRupiah(parseFloat(0))) + '</td>';
                    row += '</tr>';
                    no++;
                } else {
                    row += '<tr style="color:whitesmoke;text-align: center;"><td colspan="6" class="text-center">Data Barang Tidak Ada</td></tr>';
                }
            });
            $('.body-detail-table-saldo-adjust-bonus').append(row);
        }
    }

    const drawTableSaldoAdjusmentLainnya = function() {
        $('.body-detail-table-saldo-adjust-lainnya').empty();
        $('.tfoot-detail-table-saldo-adjust-lainnya').empty();
        var row = '';
        var rowFooter = '';
        var no = 1;
        var strip = "-";
        if (list_items_saldo_adjusment.length === 0) {
            row += '<tr><td colspan="6" class="text-center">Data Barang Tidak Ada</td></tr>';

            $('.tfoot-detail-table-saldo-adjust-lainnya').append(row);
        } else {
            var hargaUmum = 0;
            var hargaHarian = 0;
            var hargaBulanan = 0;
            var stok = 0;
            var stokProduksi = 0;
            var totalHarga = 0;
            list_items_saldo_adjusment.map((item, index) => {
                // counting total
                if (item.tipe_adjusment == "LAINNYA") {
                    hargaUmum += item.harga_umum !== null ? parseFloat(item.harga_umum) : 0;
                    hargaHarian += item.harga_harian !== null ? parseFloat(item.harga_harian) : 0;
                    hargaBulanan += item.harga_bulanan !== null ? parseFloat(item.harga_bulanan) : 0;
                    stok = item.qty !== null ? parseFloat(item.qty) : 0;
                    // stokProduksi = item.stok_produksi !== null ? parseFloat(item.stok_produksi) : 0;
                    totalStok = stok;
                    totalHarga = (hargaUmum + hargaHarian + hargaBulanan) * totalStok;
                    // end counting
                    row += '<tr style="color:whitesmoke;text-align: center;">';
                    row += '<td>' + no + '</td>';
                    row += '<td>' + item.barang + '</td>';
                    row += '<td>' + (totalStok !== 0 ? parseFloat(totalStok).toLocaleString() : 0) + '</td>';
                    row += '<td>' + (item.satuan !== undefined ? item.satuan : strip) + '</td>';
                    row += '<td>' + (totalHarga !== 0 ? formatRupiah(parseFloat(totalHarga)) : formatRupiah(parseFloat(0))) + '</td>';
                    row += '</tr>';
                    no++;
                } else {
                    row += '<tr style="color:whitesmoke;text-align: center;"><td colspan="6" class="text-center">Data Barang Tidak Ada</td></tr>';
                }
            });
            $('.body-detail-table-saldo-adjust-lainnya').append(row);
        }
    }

    const drawTableSaldoJual = function() {
        $('.body-detail-table-saldo-jual').empty();
        $('.tfoot-detail-table-saldo-jual').empty();
        var row = '';
        var rowFooter = '';
        var no = 1;
        var strip = "-";
        if (list_items_saldo_jual.length === 0) {
            row += '<tr><td colspan="6" class="text-center">Data Barang Tidak Ada</td></tr>';

            $('.tfoot-detail-table-saldo-jual').append(row);
        } else {
            var hargaUmum = 0;
            var hargaHarian = 0;
            var hargaBulanan = 0;
            var stok = 0;
            var stokProduksi = 0;
            var totalHarga = 0;
            list_items_saldo_jual.map((item, index) => {
                // counting total
                hargaUmum += item.harga_umum !== null ? parseFloat(item.harga_umum) : 0;
                hargaHarian += item.harga_harian !== null ? parseFloat(item.harga_harian) : 0;
                hargaBulanan += item.harga_bulanan !== null ? parseFloat(item.harga_bulanan) : 0;
                stok = item.qty !== null ? parseFloat(item.qty) : 0;
                // stokProduksi = item.stok_produksi !== null ? parseFloat(item.stok_produksi) : 0;
                totalStok = stok + stokProduksi;
                totalHarga = (hargaUmum + hargaHarian + hargaBulanan) * totalStok;
                // end counting
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.barang + '</td>';
                row += '<td>' + (totalStok !== 0 ? parseFloat(totalStok).toLocaleString() : 0) + '</td>';
                row += '<td>' + (item.satuan !== undefined ? item.satuan : strip) + '</td>';
                row += '<td>' + (totalHarga !== 0 ? formatRupiah(parseFloat(totalHarga)) : formatRupiah(parseFloat(0))) + '</td>';
                row += '</tr>';
                no++;
            });
            $('.body-detail-table-saldo-jual').append(row);
        }
    }

    const drawTableSaldoTrimming = function() {
        $('.body-detail-table-saldo-trimming').empty();
        $('.tfoot-detail-table-saldo-trimming').empty();
        var row = '';
        var rowFooter = '';
        var no = 1;
        var strip = "-";
        if (list_items_saldo_trimming.length === 0) {
            row += '<tr><td colspan="6" class="text-center">Data Barang Tidak Ada</td></tr>';

            $('.tfoot-detail-table-saldo-trimming').append(row);
        } else {
            var hargaUmum = 0;
            var hargaHarian = 0;
            var hargaBulanan = 0;
            var stok = 0;
            var stokProduksi = 0;
            var totalHarga = 0;
            list_items_saldo_trimming.map((item, index) => {
                // counting total
                hargaUmum += item.harga_umum !== null ? parseFloat(item.harga_umum) : 0;
                hargaHarian += item.harga_harian !== null ? parseFloat(item.harga_harian) : 0;
                hargaBulanan += item.harga_bulanan !== null ? parseFloat(item.harga_bulanan) : 0;
                stok = item.stok_total !== null ? parseFloat(item.stok_total) : 0;
                stokProduksi = item.stok_produksi !== null ? parseFloat(item.stok_produksi) : 0;
                totalStok = stok + stokProduksi;
                totalHarga = (hargaUmum + hargaHarian + hargaBulanan) * totalStok;
                // end counting
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.barang + '</td>';
                row += '<td>' + (totalStok !== 0 ? parseFloat(totalStok).toLocaleString() : 0) + '</td>';
                row += '<td>' + (item.satuan !== undefined ? item.satuan : strip) + '</td>';
                row += '<td>' + (totalHarga !== 0 ? formatRupiah(parseFloat(totalHarga)) : formatRupiah(parseFloat(0))) + '</td>';
                row += '</tr>';
                no++;
            });
            $('.body-detail-table-saldo-trimming').append(row);
        }
    }

    const drawTableRasioMaterialII = function() {
        $('.head-table-rasio-material2').empty();
        $('.body-table-rasio-material2').empty();
        $('.tfoot-rasio-material2').empty();
        var row = '';
        var rowDigunakan = '';
        var no = 1;
        if (list_items_barang_jadi_material_2.length === 0) {
            row += '<tr><td colspan="7" class="text-center">Data Barang Tidak Ada</td></tr>';
            $('.tfoot-rasio-material2').append(row);
        } else {
            row += '<tr>';
            row += '<th style="text-align: center;" rowspan="2">No</th>';
            row += '<th style="text-align: center;" rowspan="2">Kategori Barang</th>';
            row += '<th style="text-align: center;" rowspan="2">Nama Barang</th>';
            row += '<th style="text-align: center;" rowspan="2">Satuan Barang</th>';
            row += '<th style="text-align: center;" rowspan="2">Qty</th>';
            list_items_barang_jadi_material_2.map((item, index) => {
                row += '<th style="text-align: center;" colspan="3">' + item.barang_name + ' - ' + item.spesifikasi + '</th>';
            });
            row += '</tr>';
            row += '<tr>';
            list_items_barang_jadi_material_2.map((item, index) => {
                row += '<th style="text-align: center;">Qty</th>';
                row += '<th style="text-align: center;">Total Harga</th>';
                row += '<th style="text-align: center;">Harga</th>';
            });
            row += '</tr>';
            $('.head-table-rasio-material2').append(row);
            var qtyProduksi = 0;
            var hargaPO = 0;
            var hargaPOSatuan = 0;
            var strip = "-";
            var banyakBarangJadi = list_items_barang_jadi_material_2.length;
            list_items_barang_digunakan_material_2.map((item, index) => {
                rowDigunakan += '<tr style="color:whitesmoke;text-align: center;">';
                rowDigunakan += '<td>' + no + '</td>';
                rowDigunakan += '<td>' + item.parent_name + '</td>';
                rowDigunakan += '<td>' + item.barang_name + ' - ' + item.spesifikasi + '</td>';
                rowDigunakan += '<td>' + (item.satuanPO !== undefined ? item.satuanPO : item.satuan_request) + '</td>';
                rowDigunakan += '<td>' + item.qty_produksi + '</td>';
                list_items_barang_jadi_material_2.map((item2, index2) => {
                    if (item.production_result_id == item2.production_result_id) {
                        qtyProduksi = item.qty_produksi / banyakBarangJadi;
                        hargaPOSatuan = item.hargaSatuanPO;
                        hargaPO = hargaPOSatuan * qtyProduksi;
                    }
                    rowDigunakan += '<td>' +
                        '<input class="form-control qty-material2 text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-id_production="' + item2.production_result_id + '" data-id_production_detail="' + item2.production_result_detail_id + '" data-barang1_id_production="' + item2.barang1_id + '"data-barang2_id_production="' + item2.barang2_id + '" data-index="' + index + '" data-index2="' + index2 + '" value="' + qtyProduksi.toLocaleString() + '">' +
                        '</td>';
                    rowDigunakan += '<td>' +
                        '<input class="form-control harga-material2 text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-id_production="' + item2.production_result_id + '" data-id_production_detail="' + item2.production_result_detail_id + '" data-barang1_id_production="' + item2.barang1_id + '"data-barang2_id_production="' + item2.barang2_id + '" data-index="' + index + '" data-index2="' + index2 + '" value="' + formatRupiah(parseFloat(hargaPO)) + '">' +
                        '</td>';
                    rowDigunakan += '<td>' +
                        '<input class="form-control harga-satuan-material2 text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-id_production="' + item2.production_result_id + '" data-id_production_detail="' + item2.production_result_detail_id + '" data-barang1_id_production="' + item2.barang1_id + '"data-barang2_id_production="' + item2.barang2_id + '" data-index="' + index + '" data-index2="' + index2 + '" value="' + formatRupiah(parseFloat(hargaPOSatuan)) + '">' +
                        '</td>';
                });
                rowDigunakan += '</tr>';
                no++;
            });
            $('.body-table-rasio-material2').append(rowDigunakan);

            // Update total harga jika ada perubahan pada input dengan kelas harga
            $('.qty-material2').on('input', function() {
                var rowIndex = $(this).data('index');
                var colIndex = $(this).data('index2');
                var id_production = $(this).data('id_production');
                var id_production_detail = $(this).data('id_production_detail');
                var barang1_id_production = $(this).data('barang1_id_production');
                var barang2_id_production = $(this).data('barang2_id_production');
                var qty = parseFloat($(this).val().replace(/Rp|\./g, ""));
                var hargaTotal = parseFloat($('#hargaTotalPembelian_material_2').val().replace(/Rp|\./g, ""));
                var hargaSatuan = hargaTotal / qty;

                $('input.harga-material2[data-index="' + rowIndex + '"][data-index2="' + colIndex + '"]').val(formatRupiah(parseFloat(hargaTotal)));
                $('input.harga-satuan-material2[data-index="' + rowIndex + '"][data-index2="' + colIndex + '"]').val(formatRupiah(parseFloat(hargaSatuan)));

                // Update the data in list_items_barang_digunakan_material_2
                if (!list_items_barang_digunakan_material_2[rowIndex].inputData) {
                    list_items_barang_digunakan_material_2[rowIndex].inputData = {};
                }
                list_items_barang_digunakan_material_2[rowIndex].inputData[colIndex] = {
                    id_production: id_production,
                    id_production_detail: id_production_detail,
                    barang1_id_production: barang1_id_production,
                    barang2_id_production: barang2_id_production,
                    qty_input: qty,
                    totalHarga_input: hargaTotal,
                    hargaSatuan_input: hargaSatuan
                };
            });
        }
    }

    const drawTableLaborCost = function() {
        // Kosongkan tabel terlebih dahulu
        $('.head-table-labor-cost').empty();
        $('.body-table-labor-cost').empty();
        $('.tfoot-labor-cost').empty();

        var row = '';
        var rowDigunakan = '';
        var no = 1;

        // Cek apakah list_items_labor_cost kosong
        if (list_items_labor_cost.length === 0) {
            row += '<tr><td colspan="7" class="text-center">Data Barang Tidak Ada</td></tr>';
            $('.tfoot-labor-cost').append(row);
        } else {
            row += '<tr>';
            row += '<th style="text-align: center;" rowspan="2">No</th>';
            row += '<th style="text-align: center;" rowspan="2">Keterangan</th>';
            row += '<th style="text-align: center;" rowspan="2">Total Biaya</th>';

            // Menggunakan forEach untuk iterasi
            list_items_title_cost.map((item, index) => {
                row += '<th style="text-align: center;" colspan="3">' + item.barang_name + ' - ' + item.spesifikasi + '</th>';
            });

            row += '</tr>';
            row += '<tr>';
            list_items_title_cost.map((item, index) => {
                row += '<th style="text-align: center;">Qty</th>';
                row += '<th style="text-align: center;">Total Harga</th>';
                row += '<th style="text-align: center;">Harga</th>';
            });
            row += '</tr>';
            $('.head-table-labor-cost').append(row);

            var hargaTotal = 0;
            var hargaSatuan = 0;
            var banyakBarangJadi = list_items_title_cost.length;

            list_items_labor_cost.forEach((item, index) => {
                hargaTotal = parseFloat(item.jmlhJurnal) / parseFloat(banyakBarangJadi);
                rowDigunakan += '<tr style="color:whitesmoke;text-align: center;">';
                rowDigunakan += '<td>' + no + '</td>';
                rowDigunakan += '<td>' + item.name + '</td>';
                rowDigunakan += '<td>' + formatRupiah(parseFloat(item.jmlhJurnal)) + '</td>';
                list_items_title_cost.map((item2, index2) => {
                    qtyJadi = parseFloat(item2.qty);
                    hargaSatuan = hargaTotal / qtyJadi;
                    rowDigunakan += '<td>' +
                        '<input class="form-control qty-labor-cost text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-id_production="' + item2.production_result_id + '" data-id_production_detail="' + item2.production_result_detail_id + '" data-barang1_id_production="' + item2.barang1_id + '"data-barang2_id_production="' + item2.barang2_id + '" data-index="' + index + '" data-index2="' + index2 + '" value="' + qtyJadi.toLocaleString().replaceAll(',', '.') + '">' +
                        '</td>';
                    rowDigunakan += '<td>' +
                        '<input class="form-control harga-labor-cost text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-id_production="' + item2.production_result_id + '" data-id_production_detail="' + item2.production_result_detail_id + '" data-barang1_id_production="' + item2.barang1_id + '"data-barang2_id_production="' + item2.barang2_id + '" data-index="' + index + '" data-index2="' + index2 + '" value="' + formatRupiah(parseFloat(hargaTotal)) + '">' +
                        '</td>';
                    rowDigunakan += '<td>' +
                        '<input class="form-control harga-satuan-labor-cost text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-id_production="' + item2.production_result_id + '" data-id_production_detail="' + item2.production_result_detail_id + '" data-barang1_id_production="' + item2.barang1_id + '"data-barang2_id_production="' + item2.barang2_id + '" data-index="' + index + '" data-index2="' + index2 + '" value="' + formatRupiah(parseFloat(hargaSatuan)) + '">' +
                        '</td>';
                });
                rowDigunakan += '</tr>';
                no++;
            });
            $('.body-table-labor-cost').append(rowDigunakan);

            // Event listener untuk input perubahan
            $('.qty-material2').on('input', function() {
                var rowIndex = $(this).data('index');
                var colIndex = $(this).data('index2');
                var id_production = $(this).data('id_production');
                var id_production_detail = $(this).data('id_production_detail');
                var barang1_id_production = $(this).data('barang1_id_production');
                var barang2_id_production = $(this).data('barang2_id_production');
                var qty = parseFloat($(this).val().replace(/Rp|\./g, ""));
                var hargaSatuan = parseFloat($('input.harga-satuan-labor-cost[data-index="' + rowIndex + '"][data-index2="' + colIndex + '"]').val().replace(/Rp|\./g, ""));
                var totalHarga = qty * hargaSatuan;
                $('input.harga-labor-cost[data-index="' + rowIndex + '"][data-index2="' + colIndex + '"]').val(formatRupiah(parseFloat(totalHarga)));

                // Update the data in list_items_barang_digunakan_material_2
                if (!list_items_labor_cost[rowIndex].inputData) {
                    list_items_labor_cost[rowIndex].inputData = {};
                }
                list_items_labor_cost[rowIndex].inputData[colIndex] = {
                    id_production: id_production,
                    id_production_detail: id_production_detail,
                    barang1_id_production: barang1_id_production,
                    barang2_id_production: barang2_id_production,
                    qty_input: qty,
                    totalHarga_input: totalHarga,
                    hargaSatuan_input: hargaSatuan
                };
            });
        }
    }

    const drawTableOverheadCost = function() {
        // Kosongkan tabel terlebih dahulu
        $('.head-table-overhead-cost').empty();
        $('.body-table-overhead-cost').empty();
        $('.tfoot-overhead-cost').empty();

        var row = '';
        var rowDigunakan = '';
        var no = 1;

        // Cek apakah list_items_labor_cost kosong
        if (list_items_overhead_cost.length === 0) {
            row += '<tr><td colspan="7" class="text-center">Data Barang Tidak Ada</td></tr>';
            $('.tfoot-overhead-cost').append(row);
        } else {
            row += '<tr>';
            row += '<th style="text-align: center;" rowspan="2">No</th>';
            row += '<th style="text-align: center;" rowspan="2">Keterangan</th>';
            row += '<th style="text-align: center;" rowspan="2">Total Biaya</th>';

            // Menggunakan forEach untuk iterasi
            list_items_title_cost.map((item, index) => {
                row += '<th style="text-align: center;" colspan="3">' + item.barang_name + ' - ' + item.spesifikasi + '</th>';
            });

            row += '</tr>';
            row += '<tr>';
            list_items_title_cost.map((item, index) => {
                row += '<th style="text-align: center;">Qty</th>';
                row += '<th style="text-align: center;">Total Harga</th>';
                row += '<th style="text-align: center;">Harga</th>';
            });
            row += '</tr>';
            $('.head-table-overhead-cost').append(row);

            var hargaTotal = 0;
            var hargaSatuan = 0;
            var banyakBarangJadi = list_items_title_cost.length;

            list_items_overhead_cost.forEach((item, index) => {
                hargaTotal = parseFloat(item.jmlhJurnal) / parseFloat(banyakBarangJadi);
                rowDigunakan += '<tr style="color:whitesmoke;text-align: center;">';
                rowDigunakan += '<td>' + no + '</td>';
                rowDigunakan += '<td>' + item.name + '</td>';
                rowDigunakan += '<td>' + formatRupiah(parseFloat(item.jmlhJurnal)) + '</td>';
                list_items_title_cost.map((item2, index2) => {
                    qtyJadi = parseFloat(item2.qty);
                    hargaSatuan = hargaTotal / qtyJadi;
                    rowDigunakan += '<td>' +
                        '<input class="form-control qty-overhead-cost text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-id_production="' + item2.production_result_id + '" data-id_production_detail="' + item2.production_result_detail_id + '" data-barang1_id_production="' + item2.barang1_id + '"data-barang2_id_production="' + item2.barang2_id + '" data-index="' + index + '" data-index2="' + index2 + '" value="' + qtyJadi.toLocaleString().replaceAll(',', '.') + '">' +
                        '</td>';
                    rowDigunakan += '<td>' +
                        '<input class="form-control harga-overhead-cost text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-id_production="' + item2.production_result_id + '" data-id_production_detail="' + item2.production_result_detail_id + '" data-barang1_id_production="' + item2.barang1_id + '"data-barang2_id_production="' + item2.barang2_id + '" data-index="' + index + '" data-index2="' + index2 + '" value="' + formatRupiah(parseFloat(hargaTotal)) + '">' +
                        '</td>';
                    rowDigunakan += '<td>' +
                        '<input class="form-control harga-satuan-overhead-cost text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-id_production="' + item2.production_result_id + '" data-id_production_detail="' + item2.production_result_detail_id + '" data-barang1_id_production="' + item2.barang1_id + '"data-barang2_id_production="' + item2.barang2_id + '" data-index="' + index + '" data-index2="' + index2 + '" value="' + formatRupiah(parseFloat(hargaSatuan)) + '">' +
                        '</td>';
                });
                rowDigunakan += '</tr>';
                no++;
            });
            $('.body-table-overhead-cost').append(rowDigunakan);

            // Event listener untuk input perubahan
            $('.qty-overhead-cost').on('input', function() {
                var rowIndex = $(this).data('index');
                var colIndex = $(this).data('index2');
                var id_production = $(this).data('id_production');
                var id_production_detail = $(this).data('id_production_detail');
                var barang1_id_production = $(this).data('barang1_id_production');
                var barang2_id_production = $(this).data('barang2_id_production');
                var qty = parseFloat($(this).val().replace(/Rp|\./g, ""));
                var hargaSatuan = parseFloat($('input.harga-satuan-overhead-cost[data-index="' + rowIndex + '"][data-index2="' + colIndex + '"]').val().replace(/Rp|\./g, ""));
                var totalHarga = qty * hargaSatuan;
                $('input.harga-overhead-cost[data-index="' + rowIndex + '"][data-index2="' + colIndex + '"]').val(formatRupiah(totalHarga));

                // Update the data in list_items_barang_digunakan_material_2
                if (!list_items_overhead_cost[rowIndex].inputData) {
                    list_items_overhead_cost[rowIndex].inputData = {};
                }
                list_items_overhead_cost[rowIndex].inputData[colIndex] = {
                    id_production: id_production,
                    id_production_detail: id_production_detail,
                    barang1_id_production: barang1_id_production,
                    barang2_id_production: barang2_id_production,
                    qty_input: qty,
                    totalHarga_input: totalHarga,
                    hargaSatuan_input: hargaSatuan
                };
            });
        }
    }

    const drawTableFixedOverheadCost = function() {
        // Kosongkan tabel terlebih dahulu
        $('.head-table-fixed-overhead-cost').empty();
        $('.body-table-fixed-overhead-cost').empty();
        $('.tfoot-fixed-overhead-cost').empty();

        var row = '';
        var rowDigunakan = '';
        var no = 1;

        // Cek apakah list_items_labor_cost kosong
        if (list_items_fixed_cost.length === 0) {
            row += '<tr><td colspan="7" class="text-center">Data Barang Tidak Ada</td></tr>';
            $('.tfoot-fixed-overhead-cost').append(row);
        } else {
            row += '<tr>';
            row += '<th style="text-align: center;" rowspan="2">No</th>';
            row += '<th style="text-align: center;" rowspan="2">Keterangan</th>';
            row += '<th style="text-align: center;" rowspan="2">Total Biaya</th>';

            // Menggunakan forEach untuk iterasi
            list_items_title_cost.map((item, index) => {
                row += '<th style="text-align: center;" colspan="3">' + item.barang_name + ' - ' + item.spesifikasi + '</th>';
            });

            row += '</tr>';
            row += '<tr>';
            list_items_title_cost.map((item, index) => {
                row += '<th style="text-align: center;">Qty</th>';
                row += '<th style="text-align: center;">Total Harga</th>';
                row += '<th style="text-align: center;">Harga</th>';
            });
            row += '</tr>';
            $('.head-table-fixed-overhead-cost').append(row);

            var hargaTotal = 0;
            var hargaSatuan = 0;
            var banyakBarangJadi = list_items_title_cost.length;

            list_items_fixed_cost.forEach((item, index) => {
                hargaTotal = parseFloat(item.jmlhJurnal) / parseFloat(banyakBarangJadi);
                rowDigunakan += '<tr style="color:whitesmoke;text-align: center;">';
                rowDigunakan += '<td>' + no + '</td>';
                rowDigunakan += '<td>' + item.name + '</td>';
                rowDigunakan += '<td>' + formatRupiah(item.jmlhJurnal) + '</td>';
                list_items_title_cost.map((item2, index2) => {
                    qtyJadi = parseFloat(item2.qty);
                    hargaSatuan = hargaTotal / qtyJadi;
                    rowDigunakan += '<td>' +
                        '<input class="form-control qty-fixed-cost text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-id_production="' + item2.production_result_id + '" data-id_production_detail="' + item2.production_result_detail_id + '" data-barang1_id_production="' + item2.barang1_id + '"data-barang2_id_production="' + item2.barang2_id + '" data-index="' + index + '" data-index2="' + index2 + '" value="' + qtyJadi.toLocaleString().replaceAll(',', '.') + '">' +
                        '</td>';
                    rowDigunakan += '<td>' +
                        '<input class="form-control harga-fixed-cost text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-id_production="' + item2.production_result_id + '" data-id_production_detail="' + item2.production_result_detail_id + '" data-barang1_id_production="' + item2.barang1_id + '"data-barang2_id_production="' + item2.barang2_id + '" data-index="' + index + '" data-index2="' + index2 + '" value="' + formatRupiah(hargaTotal) + '">' +
                        '</td>';
                    rowDigunakan += '<td>' +
                        '<input class="form-control harga-satuan-fixed-cost text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-id_production="' + item2.production_result_id + '" data-id_production_detail="' + item2.production_result_detail_id + '" data-barang1_id_production="' + item2.barang1_id + '"data-barang2_id_production="' + item2.barang2_id + '" data-index="' + index + '" data-index2="' + index2 + '" value="' + formatRupiah(hargaSatuan) + '">' +
                        '</td>';
                });
                rowDigunakan += '</tr>';
                no++;
            });
            $('.body-table-fixed-overhead-cost').append(rowDigunakan);

            // Event listener untuk input perubahan
            $('.qty-fixed-cost').on('input', function() {
                var rowIndex = $(this).data('index');
                var colIndex = $(this).data('index2');
                var id_production = $(this).data('id_production');
                var id_production_detail = $(this).data('id_production_detail');
                var barang1_id_production = $(this).data('barang1_id_production');
                var barang2_id_production = $(this).data('barang2_id_production');
                var qty = parseFloat($(this).val().replace(/Rp|\./g, ""));
                var hargaSatuan = parseFloat($('input.harga-satuan-fixed-cost[data-index="' + rowIndex + '"][data-index2="' + colIndex + '"]').val().replace(/Rp|\./g, ""));
                var totalHarga = qty * hargaSatuan;
                $('input.harga-fixed-cost[data-index="' + rowIndex + '"][data-index2="' + colIndex + '"]').val(formatRupiah(totalHarga));

                // Update the data in list_items_barang_digunakan_material_2
                if (!list_items_fixed_cost[rowIndex].inputData) {
                    list_items_fixed_cost[rowIndex].inputData = {};
                }
                list_items_fixed_cost[rowIndex].inputData[colIndex] = {
                    id_production: id_production,
                    id_production_detail: id_production_detail,
                    barang1_id_production: barang1_id_production,
                    barang2_id_production: barang2_id_production,
                    qty_input: qty,
                    totalHarga_input: totalHarga,
                    hargaSatuan_input: hargaSatuan
                };
            });
        }
    }

    const drawTableRasioTrimming = function() {
        $('.body-table-rasio-trimming').empty();
        $('.tfoot-rasio-trimming').empty();
        var row = '';
        var rowFooter = '';
        var no = 1;

        if (list_items_barang_jadi_trimming.length === 0) {
            row += '<tr><td colspan="7" class="text-center">Data Barang Tidak Ada</td></tr>';
            $('.tfoot-rasio-trimming').append(row);
        } else {
            var total_qty = 0;
            var total_harga_satuan = 0;
            var total_harga_total = 0;
            list_items_barang_jadi_trimming.map((item, index) => {
                var qty = parseFloat(item.qtyTotal);
                var harga_satuan = 0;
                var harga_total = 0;

                total_qty += qty;
                total_harga_satuan += harga_satuan;
                total_harga_total += harga_total;

                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.kode_barang + '</td>';
                row += '<td>' + item.barang_name + ' - ' + item.spesifikasi + '</td>';
                row += '<td>' + item.kode_satuan + '</td>';
                row += '<td>' +
                    '<input style="width: 350px;" class="form-control jumlah-barang text-center readonly" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="' + index + '" value="' + qty + '">' +
                    '</td>';
                row += '<td>' +
                    '<input style="width: 350px;" class="form-control harga-satuan text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="' + index + '" value="' + (harga_satuan) + '">' +
                    '</td>';
                row += '<td>' +
                    '<input style="width: 350px;" class="form-control harga-total-awal text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="' + index + '" value="' + formatRupiah(harga_total) + '">' +
                    '</td>';
                row += '</tr>';
                no++;
            });
            rowFooter += '<tr>';
            rowFooter += '<td colspan="4"></td>';
            rowFooter += '<td>' +
                '<input class="form-control jumlah-barang-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" value="' + total_qty + '">' +
                '</td>';
            rowFooter += '<td>' +
                '<input class="form-control harga-satuan-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" value="' + formatRupiah(total_harga_satuan) + '">' +
                '</td>';
            rowFooter += '<td>' +
                '<input class="form-control harga-total-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" value="' + formatRupiah(total_harga_total) + '">' +
                '</td>';
            rowFooter += '</tr>';
            $('.tfoot-rasio-trimming').append(rowFooter);
            $('.body-table-rasio-trimming').append(row);

            // Update total harga jika ada perubahan pada input dengan kelas harga
            $('.harga-satuan').on('input', function() {
                var totalHarga = 0;
                var totalHargaQty = 0;
                var totalTotalHargaQty = 0;
                $('.harga-satuan').each(function() {
                    var harga = parseFloat($(this).val().replace(/Rp|\./g, ""));
                    var index = $(this).data('index');
                    var qty = ($('.jumlah-barang[data-index="' + index + '"]').val());

                    totalHarga += isNaN(harga) ? 0 : harga;
                    totalHargaQty = harga * (isNaN(qty) ? 0 : qty);
                    totalTotalHargaQty += totalHargaQty;

                    // Update the array with the new harga_satuan value
                    if (!isNaN(harga)) {
                        list_items_barang_jadi_trimming[index].harga_satuan = harga;
                        list_items_barang_jadi_trimming[index].harga_total = totalHargaQty;
                    }

                    $('.harga-total-awal[data-index="' + index + '"]').val(formatRupiah(parseFloat(totalHargaQty)));
                    $(this).val(harga);
                });

                $('.harga-satuan-total').val(formatRupiah(parseFloat(totalHarga)));
                $('.harga-total-total').val(formatRupiah(parseFloat(totalTotalHargaQty)));
            });
        }
    }

    const drawTableRasioKaleng = function() {
        $('.body-table-rasio-kaleng').empty();
        $('.tfoot-rasio-kaleng').empty();
        var row = '';
        var rowFooter = '';
        var no = 1;

        if (list_items_barang_jadi_kaleng.length === 0) {
            row += '<tr><td colspan="7" class="text-center">Data Barang Tidak Ada</td></tr>';
            $('.tfoot-rasio-kaleng').append(row);
        } else {
            var total_qty = 0;
            var total_harga_satuan = 0;
            var total_harga_total = 0;
            list_items_barang_jadi_kaleng.map((item, index) => {
                var qty = parseFloat(item.qtyTotal);
                var harga_satuan = 0;
                var harga_total = 0;

                total_qty += qty;
                total_harga_satuan += harga_satuan;
                total_harga_total += harga_total;

                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.kode_barang + '</td>';
                row += '<td>' + item.barang_name + ' - ' + item.spesifikasi + '</td>';
                row += '<td>' + item.kode_satuan + '</td>';
                row += '<td>' +
                    '<input style="width: 350px;" class="form-control jumlah-barang text-center readonly" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="' + index + '" value="' + qty + '">' +
                    '</td>';
                row += '<td>' +
                    '<input style="width: 350px;" class="form-control harga-satuan text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="' + index + '" value="' + (harga_satuan) + '">' +
                    '</td>';
                row += '<td>' +
                    '<input style="width: 350px;" class="form-control harga-total-awal text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="' + index + '" value="' + formatRupiah(harga_total) + '">' +
                    '</td>';
                row += '</tr>';
                no++;
            });
            rowFooter += '<tr>';
            rowFooter += '<td colspan="4"></td>';
            rowFooter += '<td>' +
                '<input class="form-control jumlah-barang-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" value="' + total_qty + '">' +
                '</td>';
            rowFooter += '<td>' +
                '<input class="form-control harga-satuan-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" value="' + formatRupiah(total_harga_satuan) + '">' +
                '</td>';
            rowFooter += '<td>' +
                '<input class="form-control harga-total-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" value="' + formatRupiah(total_harga_total) + '">' +
                '</td>';
            rowFooter += '</tr>';
            $('.tfoot-rasio-kaleng').append(rowFooter);
            $('.body-table-rasio-kaleng').append(row);

            // Update total harga jika ada perubahan pada input dengan kelas harga
            $('.harga-satuan').on('input', function() {
                var totalHarga = 0;
                var totalHargaQty = 0;
                var totalTotalHargaQty = 0;
                $('.harga-satuan').each(function() {
                    var harga = parseFloat($(this).val().replace(/Rp|\./g, ""));
                    var index = $(this).data('index');
                    var qty = ($('.jumlah-barang[data-index="' + index + '"]').val());

                    totalHarga += isNaN(harga) ? 0 : harga;
                    totalHargaQty = harga * (isNaN(qty) ? 0 : qty);
                    totalTotalHargaQty += totalHargaQty;

                    // Update the array with the new harga_satuan value
                    if (!isNaN(harga)) {
                        list_items_barang_jadi_kaleng[index].harga_satuan = harga;
                        list_items_barang_jadi_kaleng[index].harga_total = totalHargaQty;
                    }

                    $('.harga-total-awal[data-index="' + index + '"]').val(formatRupiah(parseFloat(totalHargaQty)));
                    $(this).val(harga);
                });

                $('.harga-satuan-total').val(formatRupiah(parseFloat(totalHarga)));
                $('.harga-total-total').val(formatRupiah(parseFloat(totalTotalHargaQty)));
            });
        }
    }

    const drawTableRasioFrozen = function() {
        $('.body-table-rasio-frozen').empty();
        $('.tfoot-rasio-frozen').empty();
        var row = '';
        var rowFooter = '';
        var no = 1;

        if (list_items_barang_jadi_frozen.length === 0) {
            row += '<tr><td colspan="7" class="text-center">Data Barang Tidak Ada</td></tr>';
            $('.tfoot-rasio-frozen').append(row);

        } else {

            var total_qty = 0;
            var total_harga_satuan = 0;
            var total_harga_total = 0;

            list_items_barang_jadi_frozen.forEach(function(item, outerIndex) {
                item.forEach(function(items, innerIndex) {
                    var harga_satuan = 0;
                    var harga_total = 0;
                    row += '<tr style="color:whitesmoke;text-align: center;">';
                    row += '<td>' + no + '</td>';
                    row += '<td>' + items.kode_barang + '</td>';
                    row += '<td>' + items.barang_name + " - " + items.spesifikasi + '</td>';
                    row += '<td>' + items.kode_satuan + '</td>';
                    row += '<td>' +
                        '<input readonly class="form-control jumlah-barang text-center readonly" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-outerIndex = "' + outerIndex + '" data-innerIndex="' + innerIndex + '" value="' + items.qty + '">' +
                        '</td>';
                    row += '<td>' +
                        '<input  class="form-control harga-satuan text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-outerIndex = "' + outerIndex + '" data-innerIndex="' + innerIndex + '" value="' + (harga_satuan) + '">' +
                        '</td>';
                    row += '<td>' +
                        '<input  class="form-control harga-total-awal text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-outerIndex = "' + outerIndex + '" data-innerIndex="' + innerIndex + '" value="' + formatRupiah(harga_total) + '">' +
                        '</td>';
                    row += '</tr>';
                    no++;
                    total_qty += items.qty;
                    total_harga_satuan += harga_satuan;
                    total_harga_total += harga_total;
                });
            });

            // list_items_barang_jadi_frozen.map((item, index) => {
            //     var qty = parseFloat(item.qtyTotal);
            //     var harga_satuan = 0;
            //     var harga_total = 0;

            //     total_qty += qty;
            //     total_harga_satuan += harga_satuan;
            //     total_harga_total += harga_total;

            //     row += '<tr style="color:whitesmoke;text-align: center;">';
            //     row += '<td>' + no + '</td>';
            //     row += '<td>' + item.kode_barang + '</td>';
            //     row += '<td>' + item.barang_name + '</td>';
            //     row += '<td>' + item.kode_satuan + '</td>';
            //     row += '<td>' +
            //         '<input style="width: 350px;" class="form-control jumlah-barang text-center readonly" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="' + index + '" value="' + qty + '">' +
            //         '</td>';
            //     row += '<td>' +
            //         '<input style="width: 350px;" class="form-control harga-satuan text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="' + index + '" value="' + (harga_satuan) + '">' +
            //         '</td>';
            //     row += '<td>' +
            //         '<input style="width: 350px;" class="form-control harga-total-awal text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="' + index + '" value="' + formatRupiah(harga_total) + '">' +
            //         '</td>';
            //     row += '</tr>';
            //     no++;
            // });
            rowFooter += '<tr>';
            rowFooter += '<td colspan="4"></td>';
            rowFooter += '<td>' +
                '<input class="form-control jumlah-barang-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" value="' + total_qty + '">' +
                '</td>';
            rowFooter += '<td>' +
                '<input class="form-control harga-satuan-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" value="' + formatRupiah(total_harga_satuan) + '">' +
                '</td>';
            rowFooter += '<td>' +
                '<input class="form-control harga-total-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" value="' + formatRupiah(total_harga_total) + '">' +
                '</td>';
            rowFooter += '</tr>';
            $('.tfoot-rasio-frozen').append(rowFooter);
            $('.body-table-rasio-frozen').append(row);

            // Update total harga jika ada perubahan pada input dengan kelas harga
            $('.harga-satuan').on('input', function() {
                var totalHarga = 0;
                var totalHargaQty = 0;
                var totalTotalHargaQty = 0;
                $('.harga-satuan').each(function() {
                    var harga = parseFloat($(this).val().replace(/Rp|\./g, ""));
                    var outerIndex = $(this).data('outerindex');
                    var innerIndex = $(this).data('innerindex');
                    var qty = ($('.jumlah-barang[data-outerindex="' + outerIndex + '"][data-innerindex="' + innerIndex + '"]').val());
                    totalHarga += isNaN(harga) ? 0 : harga;
                    totalHargaQty = harga * (isNaN(qty) ? 0 : qty);
                    totalTotalHargaQty += totalHargaQty;

                    // Update the array with the new harga_satuan value
                    if (!isNaN(harga)) {
                        list_items_barang_jadi_frozen[outerIndex][innerIndex].harga_satuan = harga;
                        list_items_barang_jadi_frozen[outerIndex][innerIndex].harga_total = totalHargaQty;
                    }

                    $('.harga-total-awal[data-outerindex="' + outerIndex + '"][data-innerindex="' + innerIndex + '"]').val(formatRupiah(parseFloat(totalHargaQty)));
                    $(this).val(harga);
                });

                $('.harga-satuan-total').val(formatRupiah(parseFloat(totalHarga)));
                $('.harga-total-total').val(formatRupiah(parseFloat(totalTotalHargaQty)));
            });
        }
    }

    const drawTableRasioAkhir = function(data) {

        $('.body-table-rasio-akhir').empty();
        $('.tfoot-rasio-akhir').empty();

        let row = '';
        let rowFooter = '';
        let no = 1;

        var amount = 0;
        var qtyTotalPenerimaan = 0;
        var biayaSubsidi = 0;
        var biayaLain = 0;
        var biayaKopek = 0;

        list_items_barang_digunakan_alokasi.map((item, index) => {
            qtyTotalPenerimaan += item.totalQty !== undefined ? item.totalQty : 0;
            amount += item.totalHarga !== undefined ? item.totalHarga : 0;
        });

        const hargaTotalPenerimaan = amount + biayaSubsidi + biayaLain + biayaKopek;

        if (data.length === 0) {
            row += '<tr><td colspan="12" class="text-center">Data Barang Tidak Ada</td></tr>';
            $('.tfoot-rasio-akhir').append(row);
        } else {
            let rasioTotal = 0;
            let totalHargaRasio = 0;
            let totalTotalHargaRasio = 0;
            let totalTotalHargaSatuan = 0;

            let totalHargaTotalManual = 0;
            let totalQtyTanpaManual = 0;
            let totalBhnTersedia = 0;

            data.forEach(item => {
                totalHargaTotalManual += parseFloat(item.harga_total);
                if (item.harga_satuan == 0) {
                    totalQtyTanpaManual += parseFloat(item.qtyTotal);
                }
            });

            data.forEach((item, index) => {
                let calculatedHargaTotal = 0;
                let itemHargaTotal = 0;
                let hargaSatuan = 0;
                let totalQtyAll = 0;
                let rasio = 0;
                let rasioTanpaManual = 0;

                if (item.barang_type == "bahan_setengah_jadi") {
                    calculatedHargaTotal = 0;
                    itemHargaTotal = 0;
                    hargaSatuan = 0;
                    totalQtyAll = parseFloat(item.totalQtyAll);
                    rasio = item.rasio ? parseFloat(item.rasio) : (parseFloat(item.qtyTotal) / totalQtyAll) * 100;
                    rasioTanpaManual = item.rasio ? parseFloat(item.rasio) : (parseFloat(item.qtyTotal) / totalQtyTanpaManual) * 100;
                } else {
                    calculatedHargaTotal = 0;
                    itemHargaTotal = 0;
                    hargaSatuan = 0;
                    totalQtyAll = parseFloat(item.totalQtyAll);
                    rasio = item.rasio ? parseFloat(item.rasio) : (parseFloat(item.qtyTotal) / parseFloat(item.hasilWithPersentase));
                    rasioTanpaManual = item.rasio ? parseFloat(item.rasio) : (parseFloat(item.qtyTotal) / parseFloat(item.hasilWithPersentase));
                }

                if (isNaN(totalHargaTotalManual)) {
                    calculatedHargaTotal = (parseFloat(hargaTotalPenerimaan)) * (rasio / 100);
                    itemHargaTotal = item.hargaTotal ? parseFloat(item.hargaTotal) : parseFloat(calculatedHargaTotal);
                } else {
                    if (item.harga_total == 0) {
                        calculatedHargaTotal = (parseFloat(hargaTotalPenerimaan) - parseFloat(totalHargaTotalManual)) * (rasioTanpaManual / 100);
                        itemHargaTotal = item.harga_total == 0 ? parseFloat(calculatedHargaTotal) : parseFloat(item.harga_total);
                    } else {
                        calculatedHargaTotal = (parseFloat(hargaTotalPenerimaan) - parseFloat(totalHargaTotalManual)) * (rasio / 100);
                        itemHargaTotal = item.harga_total == 0 ? parseFloat(calculatedHargaTotal) : parseFloat(item.harga_total);
                    }
                }

                if (item.harga_satuan == 0 || item.harga_satuan == undefined) {
                    hargaSatuan = (parseFloat(calculatedHargaTotal) / parseFloat(item.qtyTotal));
                } else {
                    hargaSatuan = (parseFloat(item.harga_satuan));
                }

                rasioTotal += rasio;
                totalTotalHargaRasio += itemHargaTotal;
                totalBhnTersedia += item.hasilWithPersentase;

                row += `
                <tr style="color:whitesmoke;text-align: center;">
                    <td>${no}</td>
                    <td>${item.kode_barang}</td>
                    <td>${item.barang_name} - ${item.spesifikasi}</td>
                    <td>${item.kode_satuan}</td>
                    <td>
                        <input style="width: 150px;" class="form-control jumlah-barang text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-index="${index}" value="${item.qtyTotal}">
                    </td>
                    <td>
                        <input style="width: 150px;" class="form-control jumlah-barang-berat text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-index="${index}" value="${item.qty_isi}">
                    </td>
                    <td>
                        <input style="width: 150px;" class="form-control text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-index="${index}" value="${item.hasilWithPersentase}">
                    </td>
                    <td>
                        <input style="width: 150px;" class="form-control filling-weight-barang text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-index="${index}" value="${item.qty2}">
                    </td>
                    <td>
                        <input style="width: 150px;" class="form-control rasio text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-index="${index}" value="${rasio.toFixed(2)}%">
                    </td>
                    <td>
                        <input style="width: 200px;" class="form-control harga-satuan text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-index="${index}" value="${formatRupiah(hargaSatuan)}">
                    </td>
                    <td>
                        <input style="width: 200px;" class="form-control harga text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-index="${index}" value="${item.harga_total == 0 || item.harga_total == undefined ? formatRupiah(calculatedHargaTotal) : formatRupiah(item.harga_total)}">
                    </td>
                    <td>
                        <input style="width: 200px;" class="form-control text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-index="${index}" value="${item.harga_total == 0 || item.harga_total == undefined ? formatRupiah(calculatedHargaTotal / item.hasilWithPersentase) : formatRupiah(item.harga_total / item.hasilWithPersentase)}">
                    </td>
                </tr>`;
                no++;
            });

            rowFooter += `
            <tr>
                <td colspan="4">GrandTotal</td>
                <td>
                    <input class="form-control jumlah-barang-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" value="${data.reduce((sum, item) => sum + parseFloat(item.qtyTotal), 0)}">
                </td>
                <td>
                    <input class="form-control jumlah-barang-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" value="${data.reduce((sum, item) => sum + parseFloat(item.qty_isi), 0)}">
                </td>
                <td>
                    <input class="form-control text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" value="${totalBhnTersedia.toFixed(2)}">
                </td>
                <td></td>
                <td>
                    
                </td>
                <td></td>
                <td>
                    <input class="form-control harga-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" value="${formatRupiah(totalTotalHargaRasio)}">
                </td>
                <td></td>
            </tr>`;
            $('.tfoot-rasio-akhir').append(rowFooter);
            $('.body-table-rasio-akhir').append(row);

            // Update total harga if any input with class 'harga' changes
            $('.harga').on('change', function() {
                let totalHarga = 0;
                $('.harga').each(function() {
                    const harga = parseFloat($(this).val().replace(/Rp|\./g, ""));
                    var index = $(this).data('index');
                    totalHarga += isNaN(harga) ? 0 : harga;
                    $(this).val(formatRupiah(harga));
                });
                $('.harga-total').val(formatRupiah(totalHarga));
            });
        }
    };
    const drawTableRasioAkhirFrozen = function(data) {
        $('.body-table-rasio-akhir').empty();
        $('.tfoot-rasio-akhir').empty();

        let row = '';
        let rowSubTotal = ``;

        let rowFooter = ``;

        let no = 1;

        var amount = 0;
        var qtyTotalPenerimaan = 0;
        var biayaSubsidi = 0;
        var biayaLain = 0;
        var biayaKopek = 0;

        list_items_barang_digunakan_alokasi.map((item, index) => {
            qtyTotalPenerimaan += item.totalQty !== undefined ? item.totalQty : 0;
            amount += item.totalHarga !== undefined ? item.totalHarga : 0;
        });

        const hargaTotalPenerimaan = amount + biayaSubsidi + biayaLain + biayaKopek;

        if (data.length === 0) {
            row += '<tr><td colspan="12" class="text-center">Data Barang Tidak Ada</td></tr>';
            $('.tfoot-rasio-akhir').append(row);
        } else {
            let rasioTotal = 0;
            let totalHargaRasio = 0;
            let totalTotalHargaRasio = 0;
            let totalTotalHargaSatuan = 0;

            let totalHargaTotalManual = 0;
            let totalQtyTanpaManual = 0;

            data.forEach(item => {
                item.forEach(items => {
                    totalHargaTotalManual += parseFloat(items.harga_total);
                    if (items.harga_satuan == 0) {
                        totalQtyTanpaManual += parseFloat(items.qtyTotal);
                    }
                })
            });

            let grandTotalQty = 0;
            let grandTotalQtyIsi = 0;
            let grandTotalHasil = 0;;
            let grandTotalHargaSatuan = 0;
            let grandTotalHarga = 0;

            data.forEach((item, outerIndex) => {
                let calculatedHargaTotal = 0;
                let itemHargaTotal = 0;
                let hargaSatuan = 0;
                let totalQtyAll = 0;
                let rasioTanpaManual = 0;
                let totalQtyPerBarangMaster = 0;
                let totalQtyIsiPerBarangMaster = 0;
                let totalHasilPerBarangMaster = 0;
                let totalHargaSatuanPerBarangMaster = 0;
                let totalHargaPerBarangMaster = 0;


                item.forEach((items, innerIndex) => {
                    totalQtyPerBarangMaster += parseFloat(items.qty);
                    grandTotalQty += parseFloat(items.qty);
                    totalQtyIsiPerBarangMaster += parseFloat(items.qty_isi);
                    grandTotalQtyIsi += parseFloat(items.qty_isi);
                    totalHasilPerBarangMaster += parseFloat(items.hasilWithPersentase);
                    grandTotalHasil += parseFloat(items.hasilWithPersentase);
                    let rasioPerBarangMaster = parseFloat(items.qty) / items.hasilWithPersentase;
                    //update rasio barang to the array
                    list_items_barang_jadi_frozen[outerIndex][innerIndex].rasio = rasioPerBarangMaster.toFixed(2);
                    let calculatedHargaTotal = 0;
                    if (isNaN(totalHargaTotalManual)) {
                        calculatedHargaTotal = (parseFloat(hargaTotalPenerimaan)) * (rasioPerBarangMaster / 100);
                        // itemHargaTotal = item.hargaTotal ? parseFloat(item.hargaTotal) : parseFloat(calculatedHargaTotal);
                    } else {
                        if (item.harga_total == 0) {
                            calculatedHargaTotal = (parseFloat(hargaTotalPenerimaan) - parseFloat(totalHargaTotalManual)) * (rasioPerBarangMaster / 100);
                            // itemHargaTotal = item.harga_total == 0 ? parseFloat(calculatedHargaTotal) : parseFloat(item.harga_total);
                        } else {
                            calculatedHargaTotal = (parseFloat(hargaTotalPenerimaan) - parseFloat(totalHargaTotalManual)) * (rasioPerBarangMaster / 100);
                            // itemHargaTotal = item.harga_total == 0 ? parseFloat(calculatedHargaTotal) : parseFloat(item.harga_total);
                        }
                    }

                    if (items.harga_satuan == 0 || items.harga_satuan == undefined) {
                        hargaSatuan = (parseFloat(calculatedHargaTotal) / parseFloat(items.qty));
                        list_items_barang_jadi_frozen[outerIndex][innerIndex].harga_satuan = hargaSatuan;
                    } else {
                        hargaSatuan = (parseFloat(items.harga_satuan));
                    }
                    totalHargaSatuanPerBarangMaster += hargaSatuan;
                    totalHargaPerBarangMaster += calculatedHargaTotal;
                    grandTotalHargaSatuan += hargaSatuan;
                    grandTotalHarga += calculatedHargaTotal;

                    row = `
                    <tr style="color:whitesmoke;text-align: center;">
                            <td>${no}</td>
                            <td>${items.kode_barang}</td>
                            <td>${items.barang_name} - ${items.spesifikasi}</td>
                            <td>${items.kode_satuan}</td>
                                        <td>
                                            <input readonly style="width: 150px;" class="form-control jumlah-barang text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-outerindex="${outerIndex}"  data-innerindex="${innerIndex}" value="${items.qty}">
                                        </td>
                                        <td>
                                            <input readonly style="width: 150px;" class="form-control jumlah-barang-berat text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-outerindex="${outerIndex}"  data-innerindex="${innerIndex}" value="${items.qty_isi}">
                                        </td>
                                        <td>
                                            <input style="width: 150px;" class="form-control text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-outerindex="${outerIndex}"  data-innerindex="${innerIndex}" value="${items.hasilWithPersentase}">
                                        </td>
                                        <td>
                                            <input style="width: 150px;" class="form-control filling-weight-barang text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-outerindex="${outerIndex}"  data-innerindex="${innerIndex}" value="${items.qty2}">
                                        </td>
                                        <td>
                                            <input style="width: 150px;" class="form-control rasio text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-outerindex="${outerIndex}"  data-innerindex="${innerIndex}" value="-">
                                        </td>
                                        <td>
                                            <input style="width: 200px;" class="form-control harga-satuan text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text"  data-outerindex="${outerIndex}"  data-innerindex="${innerIndex}" value="${formatRupiah(hargaSatuan)}"  >
                                        </td>
                                        <td>
                                            <input style="width: 200px;" class="form-control harga text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text"   data-outerindex="${outerIndex}"  data-innerindex="${innerIndex}" value="${items.harga_total == 0 || items.harga_total == undefined ? formatRupiah(calculatedHargaTotal) : formatRupiah(items.harga_total)}"  >
                                        </td>
                                        <td>
                                            <input style="width: 200px;" class="form-control text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text"   data-outerindex="${outerIndex}"  data-innerindex="${innerIndex}" value="${items.harga_total == 0 || items.harga_total == undefined ? formatRupiah(calculatedHargaTotal / items.hasilWithPersentase) : formatRupiah(items.harga_total / items.hasilWithPersentase)}">
                                        </td>
                                    </tr>
                                     
                    `;
                    $('.body-table-rasio-akhir').append(row);

                    no++;
                });

                rowSubTotal = `
                   <tr style="color:whitesmoke;text-align: center; background-color:#fadfbe"">
                        <td colspan="4"> Sub Total </td>
                        <td>
                            <input class="form-control sub-barang-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" value="${totalQtyPerBarangMaster}">
                        </td>
                        <td>
                            <input class="form-control sub-barang-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" value="${totalQtyIsiPerBarangMaster}">
                        </td>
                        <td>
                            <input class="form-control text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" value="${totalHasilPerBarangMaster}">
                        </td>
                
                        <td></td>
                        <td>
                            <input class="form-control rasio-sub-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" value="${((parseFloat(totalQtyPerBarangMaster)/parseFloat(totalHasilPerBarangMaster)) * 100).toFixed(2)}%">
                        </td>
                           <td>
                            <input class="form-control harga-satuan-sub-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" data-index="${outerIndex}"  type="text" value="${formatRupiah(totalHargaSatuanPerBarangMaster) }">
                        </td>
                    
                        <td>
                            <input class="form-control harga-sub-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" data-index="${outerIndex}" type="text" value="${formatRupiah(totalHargaPerBarangMaster)}">
                        </td>
                        <td></td>
                     </tr>
                `;
                $('.body-table-rasio-akhir').append(rowSubTotal);
                totalQtyPerBarangMaster = 0;
                totalQtyIsiPerBarangMaster = 0;
                totalHasilPerBarangMaster = 0;
                totalHargaPerBarangMaster = 0;
            });



            rowFooter += `
            <tr style="background-color:#f2c996;">
                <td colspan="4">GrandTotal</td>
                <td>
                    <input class="form-control jumlah-barang-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" value="${grandTotalQty}">
                </td>
                <td>
                    <input class="form-control jumlah-barang-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" value="${grandTotalQtyIsi}">
                </td>
                <td>
                    <input class="form-control text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" value="${ grandTotalHasil}">
                </td>
                <td></td>
                <td>
                    <input class="form-control rasio-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" value="-">
                </td>
                <td></td>
                <td>
                    <input class="form-control harga-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" value="${formatRupiah(grandTotalHarga)}">
                </td>
                <td></td>
            </tr>`;

            $('.tfoot-rasio-akhir').append(rowFooter);



            $('.harga-satuan').on('change', function() {
                var sum = 0;
                var innerindex = $(this).data('innerindex');
                var outerindex = $(this).data('outerindex');
                var harga_satuan = parseFloat($(this).val().replace(/Rp|\./g, ""));
                // Update the array with the new harga_satuan value
                if (!isNaN(harga_satuan)) {
                    list_items_barang_jadi_frozen[outerindex][innerindex].harga_satuan = harga_satuan;
                }
                // var index = $(this).data('index');
                // var hargaSatuanDiGanti = $(`.harga-satuan[data-id="${id}"][data-index="${index}"]`).val();
                // var jumlahBarang = $(`.jumlah-barang[data-id="${id}"][data-index="${index}"]`).val()
                // $(`.harga[data-index="${index}"]`).val(parseFloat(hargaSatuanDiGanti) * jumlahBarang);
                $(`.harga-satuan[data-outerindex="${outerindex}"]`).each(function() {
                    const harga = parseFloat($(this).val().replace(/Rp|\./g, ""));
                    $(this).val(formatRupiah(harga));
                    sum += harga;
                });
                $(`.harga-satuan-sub-total[data-index="${outerindex}"]`).val(formatRupiah(sum));

            });

            // Update total harga if any input with class 'harga' changes
            $('.harga').on('change', function() {
                var innerindex = $(this).data('innerindex');
                var outerindex = $(this).data('outerindex');
                let subTotalHarga = 0;
                let hargaTotal = 0;
                $(`.harga[data-outerindex="${outerindex}"]`).each(function() {
                    const harga = parseFloat($(this).val().replace(/Rp|\./g, ""));
                    $(this).val(formatRupiah(harga));
                    subTotalHarga += isNaN(harga) ? 0 : harga;
                });
                $(`.harga-sub-total[data-index="${outerindex}"]`).val(formatRupiah(subTotalHarga));
                recalculateTotalHarga();
            });

            function recalculateTotalHarga() {
                let totalHarga = 0;
                // Sum all sub-totals
                $('.harga-sub-total').each(function() {
                    const harga = parseFloat($(this).val().replace(/Rp|\./g, ""));
                    totalHarga += isNaN(harga) ? 0 : harga;
                });

                // Update the total harga
                $('.harga-total').val(formatRupiah(totalHarga));
            }
        }

    };
    const drawTableRasioTerhadapBahanBaku = function(data) {
        $('.body-table-rasio-terhadap-bahan-baku').empty();
        $('.tfoot-rasio-terhadap-bahan-baku').empty();

        let row = '';
        let rowFooter = '';
        let no = 1;

        // Retrieve and parse input values
        // const amount = $('#hargaTotalPenerimaan').val() ? parseFloat($('#hargaTotalPenerimaan').val().replace(/Rp|\./g, "")) : 0;
        // const qtyTotalPenerimaan = $('#qtyTotalPenerimaan').val() ? parseFloat($('#qtyTotalPenerimaan').val().replace(/Rp|\./g, "")) : 0;
        // const biayaSubsidi = $('#biayaSubsidi').val() ? parseFloat($('#biayaSubsidi').val().replace(/Rp|\./g, "")) : 0;
        // const biayaLain = $('#biayaLain').val() ? parseFloat($('#biayaLain').val().replace(/Rp|\./g, "")) : 0;
        // const biayaKopek = $('#biayaKopek').val() ? parseFloat($('#biayaKopek').val().replace(/Rp|\./g, "")) : 0;



        var amount = 0;
        var qtyTotalPenerimaan = 0;
        var biayaSubsidi = 0;
        var biayaLain = 0;
        var biayaKopek = 0;

        list_items_barang_digunakan_alokasi.map((item, index) => {
            // counting total
            // totalQtyPO += item.totalQtyPO !== undefined ? item.totalQtyPO : 0;
            // totalHargaPO += item.totalHargaPO !== undefined ? item.totalHargaPO : 0;
            // hargaSatuanPO += item.hargaSatuanPO !== undefined ? item.hargaSatuanPO : 0;
            qtyTotalPenerimaan += item.totalQty !== undefined ? item.totalQty : 0;
            amount += item.totalHarga !== undefined ? item.totalHarga : 0;
            // hargaSatuanLPB += item.hargaSatuanLPB !== undefined ? item.hargaSatuanLPB : 0;
        });

        const hargaTotalPenerimaan = amount + biayaSubsidi + biayaLain + biayaKopek;

        if (data.length === 0) {
            row += '<tr><td colspan="7" class="text-center">Data Barang Tidak Ada</td></tr>';
            $('.tfoot-rasio-akhir').append(row);
        } else {
            let rasioTotal = 0;
            let totalHargaRasio = 0;
            let totalTotalHargaRasio = 0;

            let totalHargaTotalManual = 0;
            let totalQtyTanpaManual = 0;

            data.forEach(item => {
                totalHargaTotalManual += parseFloat(item.harga_total);
                if (item.harga_satuan == 0) {
                    totalQtyTanpaManual += parseFloat(item.qtyTotal);
                }
            });

            data.forEach((item, index) => {

                let calculatedHargaTotal = 0;
                let itemHargaTotal = 0;
                const totalQtyAll = parseFloat(qtyTotalPenerimaan);
                const rasio = item.rasio ? parseFloat(item.rasio) : (parseFloat(item.qtyTotal) / totalQtyAll) * 100;
                const rasioTanpaManual = item.rasio ? parseFloat(item.rasio) : (parseFloat(item.qtyTotal) / totalQtyTanpaManual) * 100;


                if (isNaN(totalHargaTotalManual)) {
                    calculatedHargaTotal = (parseFloat(hargaTotalPenerimaan)) * (rasio / 100);
                    itemHargaTotal = item.hargaTotal ? parseFloat(item.hargaTotal) : parseFloat(calculatedHargaTotal);
                } else {
                    if (item.harga_total == 0) {
                        calculatedHargaTotal = (parseFloat(hargaTotalPenerimaan) - parseFloat(totalHargaTotalManual)) * (rasioTanpaManual / 100);
                        itemHargaTotal = item.harga_total == 0 ? parseFloat(calculatedHargaTotal) : parseFloat(item.harga_total);
                    } else {
                        calculatedHargaTotal = (parseFloat(hargaTotalPenerimaan) - parseFloat(totalHargaTotalManual)) * (rasio / 100);
                        itemHargaTotal = item.harga_total == 0 ? parseFloat(calculatedHargaTotal) : parseFloat(item.harga_total);
                    }
                }

                rasioTotal += rasio;
                totalTotalHargaRasio += itemHargaTotal;

                row += `
                <tr style="color:whitesmoke;text-align: center;">
                    <td>${no}</td>
                    <td>${item.kode_barang}</td>
                    <td>${item.barang_name} - ${item.spesifikasi}</td>
                    <td>${item.kode_satuan}</td>
                    <td>
                        <input style="width: 150px;" class="form-control jumlah-barang text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-index="${index}" value="${item.qtyTotal}">
                    </td>
                    <td>
                        <input style="width: 150px;" class="form-control rasio text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-index="${index}" value="${rasio.toFixed(2)}%">
                    </td>
                    <td>
                        <input style="width: 150px;" class="form-control harga text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-index="${index}" value="${item.harga_total == 0 || item.harga_total == undefined ? formatRupiah(calculatedHargaTotal) : formatRupiah(item.harga_total)}">
                    </td>
                </tr>`;
                no++;
            });

            rowFooter += `
            <tr>
                <td colspan="4"></td>
                <td>
                    <input class="form-control jumlah-barang-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" value="${data.reduce((sum, item) => sum + parseFloat(item.qtyTotal), 0)}">
                </td>
                <td>
                    <input class="form-control rasio-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" value="${rasioTotal.toFixed(2)}%">
                </td>
                <td>
                    <input class="form-control harga-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" value="${formatRupiah(totalTotalHargaRasio)}">
                </td>
            </tr>`;

            $('.tfoot-rasio-terhadap-bahan-baku').append(rowFooter);
            $('.body-table-rasio-terhadap-bahan-baku').append(row);

            // Update total harga if any input with class 'harga' changes
            $('.harga').on('change', function() {
                let totalHarga = 0;
                $('.harga').each(function() {
                    const harga = parseFloat($(this).val().replace(/Rp|\./g, ""));
                    var index = $(this).data('index');
                    totalHarga += isNaN(harga) ? 0 : harga;
                    $(this).val(formatRupiah(harga));
                });
                $('.harga-total').val(formatRupiah(totalHarga));
            });
        }
    };

    function preventNegativeInput(inputElement) {
        var inputValue = inputElement.value;
        var numericValue = inputValue.replace(/[^0-9.]/g, '');
        numericValue = numericValue.replace(/^0+/g, '');
        numericValue = numericValue.replace(/^\./g, '0.');
        if (parseFloat(numericValue) < 0 || isNaN(parseFloat(numericValue))) {
            inputElement.value = '0';
        } else {
            inputElement.value = numericValue;
        }
    }

    function formatRupiah(angka) {
        // Menggunakan toFixed(2) untuk membulatkan menjadi 2 angka di belakang koma

        var angkaFloat = parseFloat(angka);
        var number_string = angkaFloat.toFixed(2).replace('.', ',');
        // Memisahkan angka ribuan dengan tanda titik
        var parts = number_string.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        // Menggabungkan kembali angka ribuan dan desimal
        return 'Rp ' + parts.join(",");
    }

    function hideShowTab() {
        if (list_items_barang_digunakan.length == 0) {
            $("#rawIBahanDigunakan").hide();
        }

        if (list_items_barang_digunakan_ulang.length == 0) {
            $("#rawIBahanProsesUlang").hide();
        }

        if (list_items_barang_filling.length == 0) {
            $("#rawIBahanFilling").hide();
        }

        if (list_items_barang_jadi_trimming.length == 0) {
            $("#rawIHasilTrimming").hide();
        }

        if (list_items_barang_jadi_kaleng.length == 0) {
            $("#rawIHasilKaleng").hide();
        }

        if (list_items_barang_jadi_frozen.length == 0) {
            $("#rawIHasilFrozen").hide();
        }

        if (list_items_saldo_awal.length == 0) {
            $("#rawISaldoAwal").hide();
        }

        if (list_items_saldo_akhir.length == 0) {
            $("#rawISaldoAkhir").hide();
        }

        if (list_items_saldo_adjusment.length == 0) {
            $("#rawISaldoAdjustment").hide();
        }

        if (list_items_saldo_jual.length == 0) {
            $("#rawISaldoJual").hide();
        }

        if (list_items_saldo_kopek.length == 0) {
            $("#rawISaldoKopek").hide();
        }

        if (list_items_saldo_trimming.length == 0) {
            $("#rawISaldoTrimming").hide();
        }
    }

    $(document).ready(function() {
        // fungsi tab
        $('#rawMaterialITab').click(function(event) {
            event.preventDefault();
            $('#rawMaterialIITab').removeClass('active');
            $('#costTab').removeClass('active');

            $(this).addClass('active');

            $('#rawMaterialICard').show();
            $('#rawMaterialIICard').hide();
            $('#costCard').hide();
        });

        $('#rawMaterialIITab').click(function(event) {
            event.preventDefault();
            $('#rawMaterialITab').removeClass('active');
            $('#costTab').removeClass('active');

            $(this).addClass('active');

            $('#rawMaterialICard').hide();
            $('#rawMaterialIICard').show();
            $('#costCard').hide();
        });

        $('#costTab').click(function(event) {
            event.preventDefault();
            $('#rawMaterialITab').removeClass('active');
            $('#rawMaterialIITab').removeClass('active');

            $(this).addClass('active');

            $('#rawMaterialICard').hide();
            $('#rawMaterialIICard').hide();
            $('#costCard').show();
        });
        // end fungsi tab

        function loadContent(page) {
            $.ajax({
                url: '<?= base_url('/rasio/load_content') ?>',
                type: 'GET',
                data: {
                    page: page
                },
                beforeSend: function() {
                    $('#content').html('<div class="text-center my-5"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');
                },
                success: function(response) {
                    $('#content').html(response);
                    if (list_items_barang_digunakan.length != 0) {
                        drawTableDigunakan();
                    }
                    if (list_items_barang_pembelian.length != 0) {
                        drawTablePembelian();
                    }
                    if (list_items_barang_digunakan_alokasi.length != 0) {
                        drawTableDigunakanAlokasi(list_items_barang_digunakan_alokasi);
                    }
                    if (list_items_barang_jadi_trimming.length != 0) {
                        drawTableRasioTrimming();
                        <?php if (!empty($rasio)) { ?>
                            drawTableRasioAkhir(list_items_barang_jadi_trimming);
                            drawTableRasioTerhadapBahanBaku(list_items_barang_jadi_trimming);
                        <?php } ?>
                        $("#rawIHasilKaleng").hide();
                        $("#rawIHasilFrozen").hide();
                    }
                    if (list_items_barang_jadi_kaleng.length != 0) {
                        drawTableRasioKaleng();
                        <?php if (!empty($rasio)) { ?>
                            drawTableRasioAkhir(list_items_barang_jadi_kaleng);
                        <?php } ?>
                        $("#rawIHasilTrimming").hide();
                        $("#rawIHasilFrozen").hide();
                    }
                    if (list_items_barang_jadi_frozen.length != 0) {
                        drawTableRasioFrozen();
                        <?php if (!empty($rasio)) { ?>
                            drawTableRasioAkhirFrozen(list_items_barang_jadi_frozen)
                        <?php } ?>
                        $("#rawIHasilTrimming").hide();
                        $("#rawIHasilKaleng").hide();
                    }
                    if (list_items_saldo_akhir.length != 0) {
                        drawTableSaldoAkhir();
                    }
                    if (list_items_saldo_awal.length != 0) {
                        drawTableSaldoAwal();
                    }
                    if (list_items_saldo_adjusment.length != 0) {
                        drawTableSaldoAdjusmentAnalisa();
                        drawTableSaldoAdjusmentSample();
                        drawTableSaldoAdjusmentBonus();
                        drawTableSaldoAdjusmentLainnya();
                    }
                    if (list_items_saldo_jual.length != 0) {
                        drawTableSaldoJual();
                    }
                    if (list_items_saldo_trimming.length != 0) {
                        drawTableSaldoTrimming();
                    }
                },
                error: function() {
                    $('#content').html('<div class="alert alert-danger" role="alert">Failed to load content.</div>');
                }
            });
        }

        $('.nav-link-raw-material-i').click(function(event) {
            event.preventDefault();
            var page = $(this).attr('href').substring(1);
            $('.nav-link-raw-material-i').removeClass('active');
            $(this).addClass('active');
            loadContent(page);
        });

        // Trigger click on the active tab to load its content on page load
        loadContent($('.nav-link-raw-material-i.active').attr('href').substring(1));

        // init validation
        var validator = $(".create-form").validate({
            rules: {
                divisi_id: {
                    required: true
                },
                tanggal: {
                    required: true
                },
            },
            messages: {
                divisi_id: {
                    required: "Nama Department Wajib Diisi"
                },
                tanggal: {
                    required: "Bulan Rasio Wajib Diisi"
                },
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
                var elem = $(element);
                if (elem.hasClass("select2-hidden-accessible")) {
                    element = $("#select2-" + elem.attr("id") + "-container").parent();
                    error.insertAfter(element);
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).closest('.form-group').addClass('has-error');
                $(element).addClass('select-class');

            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error');
                $(element).removeClass('select-class');
            },
        });

        $(".btn-submit-parent").click(function() {
            if ($(".create-form").valid()) {
                var isValid = true;

                var hargaTotal = $(".harga-total").val();
                var hargaTotalPenerimaan = $(".hargaTotalPenerimaan").val();

                // var formatedHargaTotal = parseFloat(hargaTotal.replace(/Rp|\./g, ""));
                // var formatedHargaTotalPenerimaan = parseFloat(hargaTotalPenerimaan.replace(/Rp|\./g, ""));
                // if (formatedHargaTotal != formatedHargaTotalPenerimaan) {
                //     isValid = false;
                // }

                $.each(list_items_barang_jadi, function(i, v) {
                    var qtyBarang = $('input[data-index="' + i + '"].jumlah-barang');
                    var rasioBarang = $('input[data-index="' + i + '"].rasio');
                    var hargaSatuanBarang = $('input[data-index="' + i + '"].harga-satuan');
                    var hargaBarang = $('input[data-index="' + i + '"].harga');
                    var qtyBarangVal = parseFloat(qtyBarang.val());
                    var rasioBarangVal = parseFloat(rasioBarang.val());
                    var hargaSatuanBarangVal = parseFloat(hargaSatuanBarang.val().replace(/Rp|\./g, ""));
                    var hargaBarangVal = parseFloat(hargaBarang.val().replace(/Rp|\./g, ""));

                    list_items_barang_jadi[i].qty = qtyBarangVal;
                    list_items_barang_jadi[i].rasio = rasioBarangVal;
                    list_items_barang_jadi[i].harga_satuan = hargaSatuanBarangVal;
                    list_items_barang_jadi[i].harga = hargaBarangVal;
                });

                // $.each(list_items_barang_digunakan_material_2, function(i, v) {
                //     list_items_barang_jadi_material_2.forEach((item2, index2) => {
                //         var qtyBarang = $('input[data-index="' + i + '"][data-index2="' + index2 + '"].qty-material2');
                //         var hargaSatuan = $('input[data-index="' + i + '"][data-index2="' + index2 + '"].harga-satuan-material2');
                //         var hargaTotal = $('input[data-index="' + i + '"][data-index2="' + index2 + '"].harga-material2');

                //         var qtyBarangVal = parseFloat(qtyBarang.val().replace(/\./g, ""));
                //         var hargaSatuanVal = parseFloat(hargaSatuan.val().replace(/Rp|\./g, ""));
                //         var hargaTotalVal = parseFloat(hargaTotal.val().replace(/Rp|\./g, ""));

                //         var id_production = qtyBarang.data('id_production');
                //         var id_production_detail = qtyBarang.data('id_production_detail');
                //         var barang1_id_production = qtyBarang.data('barang1_id_production');
                //         var barang2_id_production = qtyBarang.data('barang2_id_production');

                //         if (!list_items_barang_digunakan_material_2[i].inputData) {
                //             list_items_barang_digunakan_material_2[i].inputData = {};
                //         }

                //         list_items_barang_digunakan_material_2[i].inputData[index2] = {
                //             id_production: id_production,
                //             id_production_detail: id_production_detail,
                //             barang1_id_production: barang1_id_production,
                //             barang2_id_production: barang2_id_production,
                //             qty_input: qtyBarangVal,
                //             hargaSatuan_input: hargaSatuanVal,
                //             totalHarga_input: hargaTotalVal
                //         };
                //     });
                // });

                $.each(list_items_labor_cost, function(i, v) {
                    list_items_title_cost.forEach((item2, index2) => {
                        var qtyBarang = $('input[data-index="' + i + '"][data-index2="' + index2 + '"].qty-labor-cost');
                        var hargaSatuan = $('input[data-index="' + i + '"][data-index2="' + index2 + '"].harga-satuan-labor-cost');
                        var hargaTotal = $('input[data-index="' + i + '"][data-index2="' + index2 + '"].harga-labor-cost');

                        var qtyBarangVal = parseFloat(qtyBarang.val().replace(/\./g, ""));
                        var hargaSatuanVal = parseFloat(hargaSatuan.val().replace(/Rp|\./g, ""));
                        var hargaTotalVal = parseFloat(hargaTotal.val().replace(/Rp|\./g, ""));

                        var id_production = qtyBarang.data('id_production');
                        var id_production_detail = qtyBarang.data('id_production_detail');
                        var barang1_id_production = qtyBarang.data('barang1_id_production');
                        var barang2_id_production = qtyBarang.data('barang2_id_production');

                        if (!list_items_labor_cost[i].inputData) {
                            list_items_labor_cost[i].inputData = {};
                        }

                        list_items_labor_cost[i].inputData[index2] = {
                            id_production: id_production,
                            id_production_detail: id_production_detail,
                            barang1_id_production: barang1_id_production,
                            barang2_id_production: barang2_id_production,
                            qty_input: qtyBarangVal,
                            hargaSatuan_input: hargaSatuanVal,
                            totalHarga_input: hargaTotalVal
                        };
                    });
                });

                $.each(list_items_overhead_cost, function(i, v) {
                    list_items_title_cost.forEach((item2, index2) => {
                        var qtyBarang = $('input[data-index="' + i + '"][data-index2="' + index2 + '"].qty-overhead-cost');
                        var hargaSatuan = $('input[data-index="' + i + '"][data-index2="' + index2 + '"].harga-satuan-overhead-cost');
                        var hargaTotal = $('input[data-index="' + i + '"][data-index2="' + index2 + '"].harga-overhead-cost');

                        var qtyBarangVal = parseFloat(qtyBarang.val().replace(/\./g, ""));
                        var hargaSatuanVal = parseFloat(hargaSatuan.val().replace(/Rp|\./g, ""));
                        var hargaTotalVal = parseFloat(hargaTotal.val().replace(/Rp|\./g, ""));

                        var id_production = qtyBarang.data('id_production');
                        var id_production_detail = qtyBarang.data('id_production_detail');
                        var barang1_id_production = qtyBarang.data('barang1_id_production');
                        var barang2_id_production = qtyBarang.data('barang2_id_production');

                        if (!list_items_overhead_cost[i].inputData) {
                            list_items_overhead_cost[i].inputData = {};
                        }

                        list_items_overhead_cost[i].inputData[index2] = {
                            id_production: id_production,
                            id_production_detail: id_production_detail,
                            barang1_id_production: barang1_id_production,
                            barang2_id_production: barang2_id_production,
                            qty_input: qtyBarangVal,
                            hargaSatuan_input: hargaSatuanVal,
                            totalHarga_input: hargaTotalVal
                        };
                    });
                });

                $.each(list_items_fixed_cost, function(i, v) {
                    list_items_title_cost.forEach((item2, index2) => {
                        var qtyBarang = $('input[data-index="' + i + '"][data-index2="' + index2 + '"].qty-fixed-cost');
                        var hargaSatuan = $('input[data-index="' + i + '"][data-index2="' + index2 + '"].harga-satuan-fixed-cost');
                        var hargaTotal = $('input[data-index="' + i + '"][data-index2="' + index2 + '"].harga-fixed-cost');

                        var qtyBarangVal = parseFloat(qtyBarang.val().replace(/\./g, ""));
                        var hargaSatuanVal = parseFloat(hargaSatuan.val().replace(/Rp|\./g, ""));
                        var hargaTotalVal = parseFloat(hargaTotal.val().replace(/Rp|\./g, ""));

                        var id_production = qtyBarang.data('id_production');
                        var id_production_detail = qtyBarang.data('id_production_detail');
                        var barang1_id_production = qtyBarang.data('barang1_id_production');
                        var barang2_id_production = qtyBarang.data('barang2_id_production');

                        if (!list_items_fixed_cost[i].inputData) {
                            list_items_fixed_cost[i].inputData = {};
                        }

                        list_items_fixed_cost[i].inputData[index2] = {
                            id_production: id_production,
                            id_production_detail: id_production_detail,
                            barang1_id_production: barang1_id_production,
                            barang2_id_production: barang2_id_production,
                            qty_input: qtyBarangVal,
                            hargaSatuan_input: hargaSatuanVal,
                            totalHarga_input: hargaTotalVal
                        };
                    });
                });

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Total Harga Barang rasio tidak sama dengan Total Harga Barang penerimaan, coba cek kembali',
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                } else {
                    Swal.fire({
                        icon: 'question',
                        title: 'Simpan Data?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Kembali',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const csrf = $(`[name="${csrfToken}"]`);
                            setLoading()
                            let data = new FormData(document.querySelector(".create-form"));
                            data.append("items_digunakan", JSON.stringify(list_items_barang_digunakan));
                            data.append("items_digunakan_pembelian", JSON.stringify(list_items_barang_pembelian));
                            data.append("items_digunakan_alokasi", JSON.stringify(list_items_barang_digunakan_alokasi));
                            data.append("items_digunakan_material_2", JSON.stringify(list_items_barang_digunakan_material_2));
                            data.append("saldo_awal", JSON.stringify(list_items_saldo_awal));
                            data.append("saldo_akhir", JSON.stringify(list_items_saldo_akhir));
                            data.append("saldo_adjustment", JSON.stringify(list_items_saldo_adjusment));
                            data.append("saldo_jual", JSON.stringify(list_items_saldo_jual));
                            data.append("saldo_trimming", JSON.stringify(list_items_saldo_trimming));
                            data.append("labor_cost", JSON.stringify(list_items_labor_cost));
                            data.append("overhead_cost", JSON.stringify(list_items_overhead_cost));
                            data.append("fixed_cost", JSON.stringify(list_items_fixed_cost));
                            data.append("items_jadi", JSON.stringify(list_items_barang_jadi));
                            data.append("item_jadi_frozen", JSON.stringify(list_items_barang_jadi_frozen));
                            data.append("tipe_bahan", tipe_bahan);

                            let id = $(".id").val();
                            // UPDATE
                            if (id) {
                                $.ajax({
                                    url: "<?= base_url("rasio/update"); ?>",
                                    data: data,
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                        setLoading();
                                    },
                                    complete: function() {
                                        stopLoading();
                                    },
                                    method: "POST",
                                    dataType: "json",
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        csrf.val(response.token);
                                        if (response.status) {
                                            Swal.fire({
                                                    icon: 'success',
                                                    title: response.message,
                                                    confirmButtonColor: '#4e73df',
                                                })
                                                .then(() => {
                                                    window.location.href = "<?= base_url("rasio"); ?>";
                                                })
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            stopLoading()
                                        }
                                    },
                                    onError: function(response) {
                                        csrf.val(response.token);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Data Gagal Disimpan, coba Lagi',
                                            confirmButtonColor: '#4e73df',
                                        })
                                        stopLoading()
                                    }
                                });
                            }
                            // CREATE
                            else {
                                $.ajax({
                                    url: "<?= base_url("rasio/save"); ?>",
                                    data: data,
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                        setLoading();
                                    },
                                    complete: function() {
                                        stopLoading();
                                    },
                                    method: "POST",
                                    dataType: "json",
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        csrf.val(response.token);
                                        if (response.status) {
                                            Swal.fire({
                                                    icon: 'success',
                                                    title: response.message,
                                                    confirmButtonColor: '#4e73df',
                                                })
                                                .then(() => {
                                                    window.location.href = "<?= base_url("rasio"); ?>";
                                                })
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            stopLoading()
                                        }
                                    },
                                    onError: function(response) {
                                        csrf.val(response.token);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Data Gagal Disimpan, coba Lagi',
                                            confirmButtonColor: '#4e73df',
                                        })
                                        stopLoading()
                                    }
                                });
                            }
                        }
                    })
                }
            }
        })
    });

    <?php if (!empty($rasio)) : ?>
        let barang1IdQtyMap = {};

        $("#qtyTotalPembelian").val(<?= !empty($rasio) ? $rasio->total_qty_po : "" ?>.toLocaleString());
        $("#hargaTotalPembelian").val(formatRupiah(parseFloat(<?= !empty($rasio) ? $rasio->harga_total_po : 0 ?>)));
        $("#hargaSatuanPembelian").val(formatRupiah(<?= !empty($rasio) ? $rasio->harga_average_po : "" ?>));

        $("#qtyTotalPenerimaan").val(<?= !empty($rasio) ? $rasio->total_qty_lpb : "" ?>.toLocaleString());
        $("#hargaTotalPenerimaan").val(formatRupiah(parseFloat(<?= !empty($rasio) ? $rasio->harga_total_lpb : 0 ?>)));
        $("#hargaSatuanPenerimaan").val(formatRupiah(parseFloat(<?= !empty($rasio) ? $rasio->harga_average_lpb : 0 ?>)));

        $("#biayaSubsidi").val(formatRupiah(parseFloat(<?= !empty($rasio) ? $rasio->total_subsidi : 0 ?>)));
        $("#biayaLain").val(formatRupiah(parseFloat(<?= !empty($rasio) ? $rasio->total_biaya : 0 ?>)));
        $("#biayaKopek").val(formatRupiah(parseFloat(<?= !empty($rasio) ? $rasio->total_kopek : 0 ?>)));

        <?php foreach ($rasioBarangPembelian as $value) : ?>
            list_items_barang_pembelian.push({
                'rasio_barang_digunakan_id': <?= json_encode($value->id) ?>,
                'barang_name': <?= json_encode($value->barang_name) ?>,
                'spesifikasi': <?= json_encode($value->spesifikasi) ?>,
                'totalQtyPO': parseFloat(<?= json_encode($value->qty_po) ?>),
                'totalHargaPO': parseFloat(<?= json_encode($value->harga_po_total) ?>),
                'hargaSatuanPO': parseFloat(<?= json_encode($value->harga_po_satuan) ?>),
                'satuanPO': <?= json_encode($value->satuan_po) ?>,
                'totalQtyLPB': parseFloat(<?= json_encode($value->qty_lpb) ?>),
                'totalHargaLPB': parseFloat(<?= json_encode($value->harga_lpb_total) ?>),
                'hargaSatuanLPB': parseFloat(<?= json_encode($value->harga_lpb_satuan) ?>),
                'satuanLPB': <?= json_encode($value->satuan_lpb) ?>,
            });
        <?php endforeach; ?>
        drawTablePembelian();

        <?php foreach ($rasioBarangDigunakan as $value) : ?>
            list_items_barang_digunakan.push({
                'rasio_barang_digunakan_id': <?= json_encode($value->id) ?>,
                'barang_name': <?= json_encode($value->barang_name) ?>,
                'spesifikasi': <?= json_encode($value->spesifikasi) ?>,
                'totalQtyPO': parseFloat(<?= json_encode($value->qty_po) ?>),
                'totalHargaPO': parseFloat(<?= json_encode($value->harga_po_total) ?>),
                'hargaSatuanPO': parseFloat(<?= json_encode($value->harga_po_satuan) ?>),
                'satuanPO': <?= json_encode($value->satuan_po) ?>,
                'totalQtyLPB': parseFloat(<?= json_encode($value->qty_lpb) ?>),
                'totalHargaLPB': parseFloat(<?= json_encode($value->harga_lpb_total) ?>),
                'hargaSatuanLPB': parseFloat(<?= json_encode($value->harga_lpb_satuan) ?>),
                'satuanLPB': <?= json_encode($value->satuan_lpb) ?>,
            });
        <?php endforeach; ?>
        drawTableDigunakan();

        <?php foreach ($rasioBarangDigunakanAlokasi as $value) : ?>
            list_items_barang_digunakan_alokasi.push({
                'barang1_id': <?= json_encode($value->barang1_id) ?>,
                'barang2_id': <?= json_encode($value->barang2_id) ?>,
                'barang_name': <?= json_encode($value->barang_name) ?>,
                'hargaSatuan': <?= json_encode($value->harga_satuan) ?>,
                'satuanPO': <?= json_encode($value->satuan) ?>,
                'spesifikasi': <?= json_encode($value->spesifikasi) ?>,
                'totalHarga': parseFloat(<?= json_encode($value->harga_total) ?>),
                'totalQty': parseFloat(<?= json_encode($value->qty) ?>),
            });
        <?php endforeach; ?>
        drawTableDigunakanAlokasi(list_items_barang_digunakan_alokasi);

        <?php foreach ($rasioSaldoAwalModel as $value) : ?>
            list_items_saldo_awal.push(<?= json_encode($value) ?>);
        <?php endforeach; ?>
        drawTableSaldoAwal();

        <?php foreach ($rasioSaldoAkhirModel as $value) : ?>
            list_items_saldo_akhir.push(<?= json_encode($value) ?>);
        <?php endforeach; ?>
        drawTableSaldoAkhir();

        let barang1Id = "";
        let barang2Id = "";
        <?php foreach ($rasioBarangJadi as $value) : ?>
            <?php if ($value->tipe_bahan == "frozen") { ?>

                barang1Id = <?= json_encode($value->barang1_id) ?>;
                barang2Id = <?= json_encode($value->barang2_id) ?>;

                if (!barang1IdQtyMap[barang1Id]) {
                    barang1IdQtyMap[barang1Id] = {};
                }

                if (barang1IdQtyMap[barang1Id][barang2Id]) {
                    barang1IdQtyMap[barang1Id][barang2Id].qty = parseFloat(<?= json_encode($value->qty_barang) ?>);
                    barang1IdQtyMap[barang1Id][barang2Id].qty2 = parseFloat(<?= json_encode($value->qty2) ?>);
                    barang1IdQtyMap[barang1Id][barang2Id].qty_isi = parseFloat(<?= json_encode($value->qty_isi) ?>);
                    barang1IdQtyMap[barang1Id][barang2Id].banyakData = parseFloat(<?= json_encode($value->banyakData) ?>);
                    barang1IdQtyMap[barang1Id][barang2Id].hasilWithPersentase = parseFloat(<?= json_encode($value->hasilWithPersentase) ?>);
                } else {
                    // Initialize a new entry for this barang1_id
                    barang1IdQtyMap[barang1Id][barang2Id] = {
                        barang1_id: barang1Id,
                        tipe_bahan: <?= json_encode($value->tipe_bahan) ?>,
                        barang2_id: barang2Id,
                        barang_name: <?= json_encode($value->barang_name) ?>,
                        kode_barang: <?= json_encode($value->kode_barang) ?>,
                        kode_satuan: <?= json_encode($value->kode_satuan) ?>,
                        spesifikasi: <?= json_encode($value->spesifikasi) ?>,
                        production_result_detail_id: <?= json_encode($value->production_result_detail_id) ?>,
                        production_result_id: <?= json_encode($value->production_result_id) ?>,
                        satuan_id: <?= json_encode($value->satuan_id) ?>,
                        bc_id: <?= json_encode($value->bc_id) ?>,
                        stock_id: <?= json_encode($value->stock_id) ?>,
                        no_aju: <?= json_encode($value->no_aju) ?>,
                        stock_dokumen: <?= json_encode($value->stock_dokumen) ?>,
                        qty: parseFloat(<?= json_encode($value->qty_barang) ?>),
                        qty2: parseFloat(<?= json_encode($value->qty2) ?>),
                        qty_isi: parseFloat(<?= json_encode($value->qty_isi) ?>),
                        banyakData: parseFloat(<?= json_encode($value->banyakData) ?>),
                        hasilWithPersentase: parseFloat(<?= json_encode($value->hasilWithPersentase) ?>),
                    };
                }
            <?php }
            if ($value->tipe_bahan == "trimming") { ?>
                list_items_barang_jadi_trimming.push(<?= json_encode($value) ?>);
            <?php }
            if ($value->tipe_bahan == "kaleng") { ?>
                list_items_barang_jadi_kaleng.push(<?= json_encode($value) ?>);
            <?php } ?>
        <?php endforeach; ?>

        for (let key in barang1IdQtyMap) {
            if (barang1IdQtyMap.hasOwnProperty(key)) {
                let group = barang1IdQtyMap[key];
                let barang1IdArr = Object.values(barang1IdQtyMap[key]);
                list_items_barang_jadi_frozen.push(barang1IdArr);
            }
        }
        if (list_items_barang_jadi_frozen.length != 0) {
            drawTableRasioAkhirFrozen(list_items_barang_jadi_frozen);
        }
        if (list_items_barang_jadi_kaleng.length != 0) {
            drawTableRasioAkhir(list_items_barang_jadi_kaleng);
        }
        if (list_items_barang_jadi_trimming.length != 0) {
            drawTableRasioAkhir(list_items_barang_jadi_trimming);
            drawTableRasioTerhadapBahanBaku(list_items_barang_jadi_trimming);
        }
        hideShowTab();
    <?php endif; ?>
</script>

<?= $this->endSection(); ?>