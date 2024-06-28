<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($jasaVendorOut) ? "Tambah Rasio" : "Update Rasio" ?></h1>
        <div class="col-button-tambah-spp text-right">
            <a class="btn btn-hide-form btn-discard" href="<?= base_url("rasio"); ?>">
                Batal
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
                                    <input autocomplete="one-time-code" class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= !empty($rasio) ? date('m/Y', strtotime($rasio->bulan)) : "" ?>">
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
                        <!-- <li class="nav-item">
                            <a class="nav-link nav-link-raw-material-i" id="rawISaldoAdjustment" href="#saldo_adjustment">Saldo Adjustment</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-raw-material-i" id="rawISaldoJual" href="#saldo_jual">Saldo Jual</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-raw-material-i" id="rawISaldoTrimming" href="#saldo_trimming">Saldo Trimming</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-raw-material-i" id="rawISaldoKopek" href="#saldo_kopek">Saldo Kopek</a>
                        </li> -->
                        <li class="nav-item">
                            <a class="nav-link nav-link-raw-material-i" id="rawIBahanJadi" href="#bahan_jadi">Bahan Jadi</a>
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
                                            <th style="text-align: center;" colspan="5">Data Pembelian</th>
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
                            <label class="form-label font-weight-bold lable-title">Data Total Pembelian Barang</label>
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

    let list_items_barang_jadi = [];
    let list_items_barang_digunakan = [];
    let list_items_barang_digunakan_ulang = [];
    let list_items_barang_digunakan_alokasi = [];
    let list_items_saldo_awal = [];
    let list_items_saldo_akhir = [];

    let list_items_barang_jadi_material_2 = [];
    let list_items_barang_digunakan_material_2 = [];

    let list_items_labor_cost = [];
    let list_items_title_cost = [];
    let list_items_overhead_cost = [];
    let list_items_fixed_cost = [];

    $("#tanggal").datepicker({
        todayHighlight: true,
        format: "mm/yyyy",
        orientation: "bottom auto",
        autoclose: true,
        startView: "months",
        minViewMode: 1
    }).change(function() {
        list_items_barang_jadi = [];
        list_items_barang_jadi_material_2 = [];
        list_items_barang_digunakan = [];
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
        list_items_barang_jadi = [];
        list_items_barang_jadi_material_2 = [];
        list_items_barang_digunakan = [];
        list_items_barang_digunakan_material_2 = [];
        list_items_labor_cost = [];
        list_items_title_cost = [];
        list_items_overhead_cost = [];
        list_items_fixed_cost = [];

        getDataRawMaterialI();
        getDataRawMaterialII();
        getDataCost();
        getListWarehouseAsal()
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
        list_items_barang_jadi = [];
        list_items_barang_jadi_material_2 = [];
        list_items_barang_digunakan = [];
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


    function getListWarehouseAsal() {
        setLoading();
        // GET LIST WAREHOUSE ASAL
        $.ajax({
            url: `<?= base_url('mutasi/warehouse'); ?>`,
            method: "GET",
            data: {
                divisi_id: $(".divisi_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_id").empty()
                $(".warehouse_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
                stopLoading();
            }
        });
    }

    const getDataJurnalSubsidi = function() {
        var department_id = $('#divisi_id').val();
        var bulan = $('#tanggal').val();
        var coa_id = $('#akun_coa_subsidi').val();
        if (department_id && bulan && coa_id) {
            setLoading();
            $.ajax({
                url: `<?= base_url('rasio/get-jurnal'); ?>`,
                method: "GET",
                data: {
                    department: department_id,
                    bulan: bulan,
                    id_coa: coa_id,
                },
                dataType: "json",
                success: function(res) {
                    stopLoading()
                    if (res.status) {
                        $('#biayaSubsidi').val(formatRupiah(res.data));
                        drawTableRasio();
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
        var bulan = $('#tanggal').val();
        var coa_id = $('#akun_coa_biaya').val();
        if (department_id && bulan && coa_id) {
            setLoading();
            $.ajax({
                url: `<?= base_url('rasio/get-jurnal'); ?>`,
                method: "GET",
                data: {
                    department: department_id,
                    bulan: bulan,
                    id_coa: coa_id,
                },
                dataType: "json",
                success: function(res) {
                    stopLoading()
                    if (res.status) {
                        $('#biayaLain').val(formatRupiah(res.data));
                        drawTableRasio();
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
        var bulan = $('#tanggal').val();
        var coa_id = $('#akun_coa_kopek').val();
        if (department_id && bulan && coa_id) {
            setLoading();
            $.ajax({
                url: `<?= base_url('rasio/get-jurnal'); ?>`,
                method: "GET",
                data: {
                    department: department_id,
                    bulan: bulan,
                    id_coa: coa_id,
                },
                dataType: "json",
                success: function(res) {
                    stopLoading()
                    if (res.status) {
                        $('#biayaKopek').val(formatRupiah(res.data));
                        drawTableRasio();
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
        var bulan = $('#tanggal').val();
        var kategori = $('#kategori').val();
        if (department_id && bulan && kategori) {
            setLoading();
            $.ajax({
                url: `<?= base_url('rasio/get-barang-digunakan'); ?>`,
                method: "GET",
                data: {
                    department: department_id,
                    bulan: bulan,
                    kategori: kategori,
                },
                dataType: "json",
                success: function(res) {
                    stopLoading()
                    if (res.status) {
                        list_items_barang_digunakan = [];
                        let no = 0;
                        // Iterate over each item in the response data
                        res.data.forEach(function(item) {
                            list_items_barang_digunakan.push(item);
                        });
                        drawTableDigunakan();
                    } else {
                        stopLoading()
                        list_items_barang_digunakan = [];
                        drawTableDigunakan();
                    }
                },
            });
            $.ajax({
                url: `<?= base_url('rasio/get-barang-digunakan-jadi'); ?>`,
                method: "GET",
                data: {
                    department: department_id,
                    bulan: bulan,
                    kategori: kategori,
                },
                dataType: "json",
                success: function(res) {
                    stopLoading()
                    if (res.status) {
                        list_items_barang_digunakan = [];
                        let no = 0;
                        // Iterate over each item in the response data
                        res.data.forEach(function(item) {
                            list_items_barang_digunakan_ulang.push(item);
                        });
                        drawTableDigunakanJadi();
                    } else {
                        stopLoading()
                        list_items_barang_digunakan_ulang = [];
                        drawTableDigunakanJadi();
                    }
                },
            });
            $.ajax({
                url: `<?= base_url('rasio/get-barang-jadi'); ?>`,
                method: "GET",
                data: {
                    department: department_id,
                    bulan: bulan,
                    kategori: kategori,
                },
                dataType: "json",
                success: function(res) {
                    stopLoading()
                    if (res.status) {
                        list_items_barang_jadi = [];
                        let no = 0;
                        // Iterate over each item in the response data
                        res.data.forEach(function(item) {
                            list_items_barang_jadi.push(item);
                        });
                        drawTableRasio();
                    } else {
                        stopLoading()
                        list_items_barang_jadi = [];
                        list_items_barang_digunakan = [];

                        drawTableRasio();
                        drawTableDigunakan();
                    }
                },
            });
            $.ajax({
                url: `<?= base_url('rasio/get-saldo-akhir'); ?>`,
                method: "GET",
                data: {
                    divisi_id: department_id,
                },
                dataType: "json",
                success: function(res) {
                    stopLoading()
                    if (res.status) {
                        list_items_saldo_akhir = [];
                        list_items_saldo_akhir = res.data;
                        drawTableSaldoAkhir();
                    } else {
                        stopLoading()
                        list_items_saldo_akhir = [];
                        drawTableSaldoAkhir();
                    }
                },
            });
            $.ajax({
                url: `<?= base_url('rasio/get-saldo-awal'); ?>`,
                method: "GET",
                data: {
                    divisi_id: department_id,
                    bulan: bulan,
                    kategori: kategori,
                },
                dataType: "json",
                success: function(res) {
                    stopLoading()
                    if (res.status) {
                        list_items_saldo_awal = [];
                        list_items_saldo_awal = res.data;
                        drawTableSaldoAwal();
                    } else {
                        stopLoading()
                        list_items_saldo_awal = [];
                        drawTableSaldoAwal();
                    }
                },
            });
        }
    }

    const getDataRawMaterialII = function() {
        var department_id = $('#divisi_id').val();
        var bulan = $('#tanggal').val();
        var kategori = $('#kategori').val();
        if (department_id && bulan && kategori) {
            setLoading();
            $.ajax({
                url: `<?= base_url('rasio/get-barang-digunakan-penolong'); ?>`,
                method: "GET",
                data: {
                    department: department_id,
                    bulan: bulan,
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
                    bulan: bulan,
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
        var bulan = $('#tanggal').val();
        var kategori = $('#kategori').val();
        if (department_id && bulan && kategori) {
            setLoading();
            $.ajax({
                url: `<?= base_url('rasio/get-cost'); ?>`,
                method: "GET",
                data: {
                    department: department_id,
                    bulan: bulan,
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
                row += '<td>' + (item.totalQtyPO !== undefined ? parseFloat(item.totalQtyPO).toLocaleString() : formatRupiah(0)) + '</td>';
                row += '<td>' + (item.totalHargaPO !== undefined ? formatRupiah(parseFloat(item.totalHargaPO)) : formatRupiah(0)) + '</td>';
                row += '<td>' + (item.hargaSatuanPO !== undefined ? formatRupiah(item.hargaSatuanPO) : formatRupiah(0)) + '</td>';
                row += '<td>' + (item.satuanPO !== undefined ? item.satuanPO : strip) + '</td>';
                row += '</tr>';
                no++;
            });
            $('.qtyTotalPembelian_material_2').val(totalQtyPO.toLocaleString());
            $('.hargaTotalPembelian_material_2').val(formatRupiah(totalHargaPO));
            $('.hargaSatuanPembelian_material_2').val(formatRupiah(hargaSatuanPO));

            $('.body-detail-table-digunakan-material-2').append(row);
        }
    }

    const drawTableDigunakan = function() {
        $('.body-detail-table').empty();
        $('.tfoot-detail-table').empty();
        var row = '';
        var rowFooter = '';
        var no = 1;
        var strip = "-";
        if (list_items_barang_digunakan.length === 0) {
            var totalQtyPO = 0;
            var totalHargaPO = 0;
            var hargaSatuanPO = 0;
            var totalQtyLPB = 0;
            var totalHargaLPB = 0;
            var hargaSatuanLPB = 0;
            row += '<tr><td colspan="10" class="text-center">Data Barang Tidak Ada</td></tr>';
            $('.qtyTotalPembelian').val(totalQtyPO.toLocaleString());
            $('.hargaTotalPembelian').val(formatRupiah(totalHargaPO));
            $('.hargaSatuanPembelian').val(formatRupiah(hargaSatuanPO));

            $('.qtyTotalPenerimaan').val(totalQtyLPB.toLocaleString());
            $('.hargaTotalPenerimaan').val(formatRupiah(totalHargaLPB));
            $('.hargaSatuanPenerimaan').val(formatRupiah(hargaSatuanLPB));
            $('.tfoot-detail-table').append(row);
        } else {
            var totalQtyPO = 0;
            var totalHargaPO = 0;
            var hargaSatuanPO = 0;
            var totalQtyLPB = 0;
            var totalHargaLPB = 0;
            var hargaSatuanLPB = 0;
            list_items_barang_digunakan.map((item, index) => {
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
                row += '<td>' + (item.totalQtyPO !== undefined ? parseFloat(item.totalQtyPO).toLocaleString() : formatRupiah(0)) + '</td>';
                row += '<td>' + (item.totalHargaPO !== undefined ? formatRupiah(parseFloat(item.totalHargaPO)) : formatRupiah(0)) + '</td>';
                row += '<td>' + (item.hargaSatuanPO !== undefined ? formatRupiah(item.hargaSatuanPO) : formatRupiah(0)) + '</td>';
                row += '<td>' + (item.satuanPO !== undefined ? item.satuanPO : strip) + '</td>';
                row += '<td>' + (item.totalQtyLPB !== undefined ? item.totalQtyLPB.toLocaleString() : formatRupiah(0)) + '</td>';
                row += '<td>' + (item.totalHargaLPB !== undefined ? formatRupiah(item.totalHargaLPB) : formatRupiah(0)) + '</td>';
                row += '<td>' + (item.hargaSatuanLPB !== undefined ? formatRupiah(item.hargaSatuanLPB) : formatRupiah(0)) + '</td>';
                row += '<td>' + (item.satuanLPB !== undefined ? item.satuanLPB : strip) + '</td>';
                row += '</tr>';
                no++;
            });
            $('.qtyTotalPembelian').val(totalQtyPO.toLocaleString());
            $('.hargaTotalPembelian').val(formatRupiah(totalHargaPO));
            $('.hargaSatuanPembelian').val(formatRupiah(hargaSatuanPO));

            $('.qtyTotalPenerimaan').val(totalQtyLPB.toLocaleString());
            $('.hargaTotalPenerimaan').val(formatRupiah(totalHargaLPB));
            $('.hargaSatuanPenerimaan').val(formatRupiah(hargaSatuanLPB));

            $('.body-detail-table').append(row);
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
                row += '<td>' + (item.totalQty !== undefined ? parseFloat(item.totalQty).toLocaleString() : formatRupiah(0)) + '</td>';
                row += '<td>' + (item.totalHarga !== undefined ? formatRupiah(parseFloat(item.totalHarga)) : formatRupiah(0)) + '</td>';
                row += '<td>' + (item.hargaSatuan !== undefined ? formatRupiah(item.hargaSatuan) : formatRupiah(0)) + '</td>';
                row += '<td>' + (item.satuanPO !== undefined ? item.satuanPO : strip) + '</td>';
                row += '</tr>';
                no++;
            });

            $('.body-detail-table-alokasi').append(row);
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
                row += '<td>' + (item.totalQtyPO !== undefined ? parseFloat(item.totalQtyPO).toLocaleString() : formatRupiah(0)) + '</td>';
                row += '<td>' + (item.totalHargaPO !== undefined ? formatRupiah(parseFloat(item.totalHargaPO)) : formatRupiah(0)) + '</td>';
                row += '<td>' + (item.hargaSatuanPO !== undefined ? formatRupiah(item.hargaSatuanPO) : formatRupiah(0)) + '</td>';
                row += '<td>' + (item.satuanPO !== undefined ? item.satuanPO : strip) + '</td>';
                row += '<td>' + (item.totalQtyLPB !== undefined ? item.totalQtyLPB.toLocaleString() : formatRupiah(0)) + '</td>';
                row += '<td>' + (item.totalHargaLPB !== undefined ? formatRupiah(item.totalHargaLPB) : formatRupiah(0)) + '</td>';
                row += '<td>' + (item.hargaSatuanLPB !== undefined ? formatRupiah(item.hargaSatuanLPB) : formatRupiah(0)) + '</td>';
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
            list_items_saldo_akhir.map((item, index) => {
                // counting total
                hargaUmum += item.harga_umum !== null ? parseFloat(item.harga_umum) : 0;
                hargaHarian += item.harga_harian !== null ? parseFloat(item.harga_harian) : 0;
                hargaBulanan += item.harga_bulanan !== null ? parseFloat(item.harga_bulanan) : 0;
                stok = item.stok_total !== null ? parseFloat(item.stok_total) : 0;
                totalHarga = (hargaUmum + hargaHarian + hargaBulanan) * stok;
                // end counting
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.barang + '</td>';
                row += '<td>' + (stok !== 0 ? parseFloat(stok).toLocaleString() : formatRupiah(0)) + '</td>';
                row += '<td>' + (item.satuan !== undefined ? item.satuan : strip) + '</td>';
                row += '<td>' + (totalHarga !== 0 ? formatRupiah(parseFloat(totalHarga)) : formatRupiah(0)) + '</td>';
                row += '</tr>';
                no++;
            });
            $('.body-detail-table-saldo-akhir').append(row);
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
            list_items_saldo_awal.map((item, index) => {
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
                row += '<td>' + (totalStok !== 0 ? parseFloat(totalStok).toLocaleString() : formatRupiah(0)) + '</td>';
                row += '<td>' + (item.satuan !== undefined ? item.satuan : strip) + '</td>';
                row += '<td>' + (totalHarga !== 0 ? formatRupiah(parseFloat(totalHarga)) : formatRupiah(0)) + '</td>';
                row += '</tr>';
                no++;
            });
            $('.body-detail-table-saldo-awal').append(row);
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
                        '<input class="form-control harga-material2 text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-id_production="' + item2.production_result_id + '" data-id_production_detail="' + item2.production_result_detail_id + '" data-barang1_id_production="' + item2.barang1_id + '"data-barang2_id_production="' + item2.barang2_id + '" data-index="' + index + '" data-index2="' + index2 + '" value="' + formatRupiah(hargaPO) + '">' +
                        '</td>';
                    rowDigunakan += '<td>' +
                        '<input class="form-control harga-satuan-material2 text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-id_production="' + item2.production_result_id + '" data-id_production_detail="' + item2.production_result_detail_id + '" data-barang1_id_production="' + item2.barang1_id + '"data-barang2_id_production="' + item2.barang2_id + '" data-index="' + index + '" data-index2="' + index2 + '" value="' + formatRupiah(hargaPOSatuan) + '">' +
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

                $('input.harga-material2[data-index="' + rowIndex + '"][data-index2="' + colIndex + '"]').val(formatRupiah(hargaTotal));
                $('input.harga-satuan-material2[data-index="' + rowIndex + '"][data-index2="' + colIndex + '"]').val(formatRupiah(hargaSatuan));

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
                rowDigunakan += '<td>' + formatRupiah(item.jmlhJurnal) + '</td>';
                list_items_title_cost.map((item2, index2) => {
                    qtyJadi = parseFloat(item2.qty);
                    hargaSatuan = hargaTotal / qtyJadi;
                    rowDigunakan += '<td>' +
                        '<input class="form-control qty-labor-cost text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-id_production="' + item2.production_result_id + '" data-id_production_detail="' + item2.production_result_detail_id + '" data-barang1_id_production="' + item2.barang1_id + '"data-barang2_id_production="' + item2.barang2_id + '" data-index="' + index + '" data-index2="' + index2 + '" value="' + qtyJadi.toLocaleString().replaceAll(',', '.') + '">' +
                        '</td>';
                    rowDigunakan += '<td>' +
                        '<input class="form-control harga-labor-cost text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-id_production="' + item2.production_result_id + '" data-id_production_detail="' + item2.production_result_detail_id + '" data-barang1_id_production="' + item2.barang1_id + '"data-barang2_id_production="' + item2.barang2_id + '" data-index="' + index + '" data-index2="' + index2 + '" value="' + formatRupiah(hargaTotal) + '">' +
                        '</td>';
                    rowDigunakan += '<td>' +
                        '<input class="form-control harga-satuan-labor-cost text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-id_production="' + item2.production_result_id + '" data-id_production_detail="' + item2.production_result_detail_id + '" data-barang1_id_production="' + item2.barang1_id + '"data-barang2_id_production="' + item2.barang2_id + '" data-index="' + index + '" data-index2="' + index2 + '" value="' + formatRupiah(hargaSatuan) + '">' +
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
                $('input.harga-labor-cost[data-index="' + rowIndex + '"][data-index2="' + colIndex + '"]').val(formatRupiah(totalHarga));

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
                rowDigunakan += '<td>' + formatRupiah(item.jmlhJurnal) + '</td>';
                list_items_title_cost.map((item2, index2) => {
                    qtyJadi = parseFloat(item2.qty);
                    hargaSatuan = hargaTotal / qtyJadi;
                    rowDigunakan += '<td>' +
                        '<input class="form-control qty-overhead-cost text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-id_production="' + item2.production_result_id + '" data-id_production_detail="' + item2.production_result_detail_id + '" data-barang1_id_production="' + item2.barang1_id + '"data-barang2_id_production="' + item2.barang2_id + '" data-index="' + index + '" data-index2="' + index2 + '" value="' + qtyJadi.toLocaleString().replaceAll(',', '.') + '">' +
                        '</td>';
                    rowDigunakan += '<td>' +
                        '<input class="form-control harga-overhead-cost text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-id_production="' + item2.production_result_id + '" data-id_production_detail="' + item2.production_result_detail_id + '" data-barang1_id_production="' + item2.barang1_id + '"data-barang2_id_production="' + item2.barang2_id + '" data-index="' + index + '" data-index2="' + index2 + '" value="' + formatRupiah(hargaTotal) + '">' +
                        '</td>';
                    rowDigunakan += '<td>' +
                        '<input class="form-control harga-satuan-overhead-cost text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-id_production="' + item2.production_result_id + '" data-id_production_detail="' + item2.production_result_detail_id + '" data-barang1_id_production="' + item2.barang1_id + '"data-barang2_id_production="' + item2.barang2_id + '" data-index="' + index + '" data-index2="' + index2 + '" value="' + formatRupiah(hargaSatuan) + '">' +
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

    const drawTableRasio = function() {
        $('.body-table-rasio').empty();
        $('.tfoot-rasio').empty();
        var row = '';
        var rowFooter = '';
        var no = 1;

        if (list_items_barang_jadi.length === 0) {
            row += '<tr><td colspan="7" class="text-center">Data Barang Tidak Ada</td></tr>';
            $('.tfoot-rasio').append(row);
        } else {
            var total_qty = 0;
            var total_harga_satuan = 0;
            var total_harga_total = 0;
            list_items_barang_jadi.map((item, index) => {
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
                    '<input class="form-control jumlah-barang text-center readonly" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="' + index + '" value="' + qty + '">' +
                    '</td>';
                row += '<td>' +
                    '<input class="form-control harga-satuan text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="' + index + '" value="' + (harga_satuan) + '">' +
                    '</td>';
                row += '<td>' +
                    '<input class="form-control harga-total-awal text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="' + index + '" value="' + formatRupiah(harga_total) + '">' +
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
            $('.tfoot-rasio').append(rowFooter);
            $('.body-table-rasio').append(row);

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
                        list_items_barang_jadi[index].harga_satuan = harga;
                        list_items_barang_jadi[index].harga_total = totalHargaQty;
                    }

                    $('.harga-total-awal[data-index="' + index + '"]').val(formatRupiah(parseFloat(totalHargaQty)));
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

        list_items_barang_digunakan.map((item, index) => {
            // counting total
            // totalQtyPO += item.totalQtyPO !== undefined ? item.totalQtyPO : 0;
            // totalHargaPO += item.totalHargaPO !== undefined ? item.totalHargaPO : 0;
            // hargaSatuanPO += item.hargaSatuanPO !== undefined ? item.hargaSatuanPO : 0;
            qtyTotalPenerimaan += item.totalQtyLPB !== undefined ? item.totalQtyLPB : 0;
            amount += item.totalHargaLPB !== undefined ? item.totalHargaLPB : 0;
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

                console.log(item);

                let calculatedHargaTotal = 0;
                let itemHargaTotal = 0;
                const totalQtyAll = parseFloat(item.totalQtyAll);
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
                        <input class="form-control jumlah-barang text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-index="${index}" value="${item.qtyTotal}">
                    </td>
                    <td>
                        <input class="form-control rasio text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-index="${index}" value="${rasio.toFixed(2)}%">
                    </td>
                    <td>
                        <input class="form-control harga text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-index="${index}" value="${item.harga_total == 0 || item.harga_total == undefined ? formatRupiah(calculatedHargaTotal) : formatRupiah(item.harga_total)}">
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

        list_items_barang_digunakan.map((item, index) => {
            // counting total
            // totalQtyPO += item.totalQtyPO !== undefined ? item.totalQtyPO : 0;
            // totalHargaPO += item.totalHargaPO !== undefined ? item.totalHargaPO : 0;
            // hargaSatuanPO += item.hargaSatuanPO !== undefined ? item.hargaSatuanPO : 0;
            qtyTotalPenerimaan += item.totalQtyLPB !== undefined ? item.totalQtyLPB : 0;
            amount += item.totalHargaLPB !== undefined ? item.totalHargaLPB : 0;
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
                        <input class="form-control jumlah-barang text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-index="${index}" value="${item.qtyTotal}">
                    </td>
                    <td>
                        <input class="form-control rasio text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-index="${index}" value="${rasio.toFixed(2)}%">
                    </td>
                    <td>
                        <input class="form-control harga text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" data-index="${index}" value="${item.harga_total == 0 || item.harga_total == undefined ? formatRupiah(calculatedHargaTotal) : formatRupiah(item.harga_total)}">
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


    <?php if (!empty($rasio)) : ?>
        $("#qtyTotalPembelian").val(<?= !empty($rasio) ? $rasio->total_qty_po : "" ?>.toLocaleString());
        $("#hargaTotalPembelian").val(formatRupiah(<?= !empty($rasio) ? $rasio->harga_total_po : "" ?>));
        $("#hargaSatuanPembelian").val(formatRupiah(<?= !empty($rasio) ? $rasio->harga_average_po : "" ?>));

        $("#qtyTotalPenerimaan").val(<?= !empty($rasio) ? $rasio->total_qty_lpb : "" ?>.toLocaleString());
        $("#hargaTotalPenerimaan").val(formatRupiah(<?= !empty($rasio) ? $rasio->harga_total_lpb : "" ?>));
        $("#hargaSatuanPenerimaan").val(formatRupiah(<?= !empty($rasio) ? $rasio->harga_average_lpb : "" ?>));

        $("#biayaSubsidi").val(formatRupiah(<?= !empty($rasio) ? $rasio->total_subsidi : "" ?>));
        $("#biayaLain").val(formatRupiah(<?= !empty($rasio) ? $rasio->total_biaya : "" ?>));
        $("#biayaKopek").val(formatRupiah(<?= !empty($rasio) ? $rasio->total_kopek : "" ?>));

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

        <?php foreach ($rasioBarangJadi as $value) : ?>
            list_items_barang_jadi.push({
                'rasio_barang_jadi_id': <?= json_encode($value->id) ?>,
                'barang_name': <?= json_encode($value->barang_name) ?>,
                'spesifikasi': <?= json_encode($value->spesifikasi) ?>,
                'kode_barang': <?= json_encode($value->kode_barang) ?>,
                'kode_satuan': <?= json_encode($value->kode_satuan) ?>,
                'qtyTotal': parseFloat(<?= json_encode($value->qty_barang) ?>),
                'rasio': parseFloat(<?= json_encode($value->rasio_barang) ?>),
                'hargaTotal': parseFloat(<?= json_encode($value->harga_barang) ?>),
                'totalQtyAll': parseFloat(<?= json_encode($value->totalQtyAll) ?>),
            });
        <?php endforeach; ?>
        drawTableRasio();

    <?php endif; ?>

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
        var number_string = angka.toFixed(2).replace('.', ',');
        // Memisahkan angka ribuan dengan tanda titik
        var parts = number_string.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        // Menggabungkan kembali angka ribuan dan desimal
        return 'Rp ' + parts.join(",");
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
                    if (list_items_barang_digunakan_alokasi.length != 0) {
                        drawTableDigunakanAlokasi(list_items_barang_digunakan_alokasi);
                    }
                    if (list_items_barang_jadi.length != 0) {
                        drawTableRasio();
                    }
                    if (list_items_saldo_akhir.length != 0) {
                        drawTableSaldoAkhir();
                    }
                    if (list_items_saldo_awal.length != 0) {
                        drawTableSaldoAwal();
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


                console.log(hargaTotal);
                console.log(hargaTotalPenerimaan);
                // if (formatedHargaTotal != formatedHargaTotalPenerimaan) {
                //     isValid = false;
                // }

                $.each(list_items_barang_jadi, function(i, v) {
                    var qtyBarang = $('input[data-index="' + i + '"].jumlah-barang');
                    var rasioBarang = $('input[data-index="' + i + '"].rasio');
                    var hargaBarang = $('input[data-index="' + i + '"].harga');
                    var qtyBarangVal = parseFloat(qtyBarang.val());
                    var rasioBarangVal = parseFloat(rasioBarang.val());
                    var hargaBarangVal = parseFloat(hargaBarang.val().replace(/Rp|\./g, ""));

                    list_items_barang_jadi[i].qty = qtyBarangVal;
                    list_items_barang_jadi[i].rasio = rasioBarangVal;
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
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const csrf = $(`[name="${csrfToken}"]`);
                            setLoading()
                            let data = new FormData(document.querySelector(".create-form"));
                            data.append("items_digunakan", JSON.stringify(list_items_barang_digunakan));
                            data.append("items_jadi", JSON.stringify(list_items_barang_jadi));
                            data.append("items_digunakan_alokasi", JSON.stringify(list_items_barang_digunakan_alokasi));
                            data.append("items_digunakan_material_2", JSON.stringify(list_items_barang_digunakan_material_2));
                            data.append("saldo_awal", JSON.stringify(list_items_saldo_awal));
                            data.append("saldo_akhir", JSON.stringify(list_items_saldo_akhir));
                            data.append("labor_cost", JSON.stringify(list_items_labor_cost));
                            data.append("overhead_cost", JSON.stringify(list_items_overhead_cost));
                            data.append("fixed_cost", JSON.stringify(list_items_fixed_cost));

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
</script>

<?= $this->endSection(); ?>