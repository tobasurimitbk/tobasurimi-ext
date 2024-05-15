<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($jasaVendorOut) ? "Tambah Rasio" : "Update Rasio" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("rasio"); ?>">
                Batal
            </a>
            <button class="btn btn-show-form btn-save float-right btn-submit-parent">
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
            </ul>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataDivisi as $d) : ?>
                                    <option value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="">
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- card raw material I -->
                <div id="rawMaterialICard">
                    <div class="row justify-content-end">
                        <div class="col mb-3">
                            <label class="form-label font-weight-bold lable-title">Data Rasio Raw Material I</label>
                        </div>
                    </div>
                    <input type="hidden" name="id" id="id" value="" class="id">
                    <?= csrf_field() ?>
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="selectedItemTable" width="100%" border="1" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="text-align: center;" rowspan="2">No</th>
                                            <th style="text-align: center;" colspan="5">Data Pembelian</th>
                                            <th style="text-align: center;" colspan="4">Data Penerimaan</th>
                                        </tr>
                                        <tr>
                                            <th style="text-align: center;">Spesifikasi</th>
                                            <th style="text-align: center;">Qty</th>
                                            <th style="text-align: center;">Harga Total</th>
                                            <th style="text-align: center;">Harga Satuan</th>
                                            <th style="text-align: center;">Satuan</th>

                                            <th style="text-align: center;">Qty</th>
                                            <th style="text-align: center;">Harga Total</th>
                                            <th style="text-align: center;">Harga Satuan</th>
                                            <th style="text-align: center;">Satuan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="body-detail-table">
                                    </tbody>
                                    <tfoot style="background: #ffffff !important;" class="tfoot-detail-table" id="tfoot-detail-table">
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
                                <input readonly placeholder="Qty" value="" class="form-control qtyTotalPembelian" id="qtyTotalPembelian" name="qtyTotalPembelian" aria-label="Floating label select example" />
                                <label for="floatingInput" style="z-index: 1;">Qty</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly placeholder="Harga Total" value="" class="form-control hargaTotalPembelian" id="hargaTotalPembelian" name="hargaTotalPembelian" aria-label="Floating label select example" />
                                <label for="floatingInput" style="z-index: 1;">Harga Total</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly placeholder="Rata-rata Harga Satuan" value="" class="form-control hargaSatuanPembelian" id="hargaSatuanPembelian" name="hargaSatuanPembelian" aria-label="Floating label select example" />
                                <label for="floatingInput" style="z-index: 1;">Rata-rata Harga Satuan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label font-weight-bold lable-title">Data Total Penerimaan Barang</label>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly placeholder="Qty" value="" class="form-control qtyTotalPenerimaan" id="qtyTotalPenerimaan" name="qtyTotalPenerimaan" aria-label="Floating label select example" />
                                <label for="floatingInput" style="z-index: 1;">Qty</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly placeholder="Harga Total" value="" class="form-control hargaTotalPenerimaan" id="hargaTotalPenerimaan" name="hargaTotalPenerimaan" aria-label="Floating label select example" />
                                <label for="floatingInput" style="z-index: 1;">Harga Total</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly placeholder="Rata-rata Harga Satuan" value="" class="form-control hargaSatuanPenerimaan" id="hargaSatuanPenerimaan" name="hargaSatuanPenerimaan" aria-label="Floating label select example" />
                                <label for="floatingInput" style="z-index: 1;">Rata-rata Harga Satuan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label font-weight-bold lable-title">Data Biaya Tambahan</label>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <div class="form-floating " style="height: 50px;">
                                <select class="form-select akun_coa_subsidi" name="akun_coa_subsidi" id="akun_coa_subsidi">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($subAkuns)) {
                                        foreach ($subAkuns as $sub) {
                                    ?>
                                            <option value="<?= $sub->id; ?>"><?= $sub->no_sub; ?> <?= $sub->nama_sub; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Akun COA Subsidi</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select akun_coa_biaya" name="akun_coa_biaya" id="akun_coa_biaya">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($subAkuns)) {
                                        foreach ($subAkuns as $sub) {
                                    ?>
                                            <option value="<?= $sub->id; ?>"><?= $sub->no_sub; ?> <?= $sub->nama_sub; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Akun COA BIaya Lain-lain</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select akun_coa_kopek" name="akun_coa_kopek" id="akun_coa_kopek">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($subAkuns)) {
                                        foreach ($subAkuns as $sub) {
                                    ?>
                                            <option value="<?= $sub->id; ?>"><?= $sub->no_sub; ?> <?= $sub->nama_sub; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Akun COA Kopek</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-subtitle-modal">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold modal-sub-title">Rasio Barang Jadi</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="selectedItemTableRasio" width="100%" border="1" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="text-align: center;">No</th>
                                            <th style="text-align: center;">Kode Barang</th>
                                            <th style="text-align: center;">Nama Barang</th>
                                            <th style="text-align: center;">Satuan</th>
                                            <th style="text-align: center;">Jumlah Barang</th>
                                            <th style="text-align: center;">Rasio</th>
                                            <th style="text-align: center;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="body-table-rasio">
                                    </tbody>
                                    <tfoot style="background: #ffffff !important;" class="tfoot-rasio" id="tfoot-rasio">
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
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="selectedItemTableRasioMaterialII" width="100%" border="1" cellspacing="0">
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
            </form>
        </div>
    </div>
</section>
<script>
    const csrfToken = '<?= csrf_token() ?>';

    let list_items_barang_jadi = [];
    let list_items_barang_jadi_material_2 = [];
    let list_items_barang_digunakan = [];
    let list_items_barang_digunakan_material_2 = [];

    $("#tanggal").datepicker({
        todayHighlight: true,
        format: "mm/yyyy",
        orientation: "bottom auto",
        autoclose: true,
        startView: "months",
        minViewMode: 1
    }).change(function() {
        getDataRawMaterialI();
        getDataRawMaterialII();
    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        getDataRawMaterialI()
        getDataRawMaterialII();
    });

    $("#divisi_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    // Akun AR
    $('#akun_coa_subsidi, #akun_coa_biaya, #akun_coa_kopek').select2({
        placeholder: "Pilih Akun COA",
        theme: "bootstrap-5",
        allowClear: true
    })

    //CSS SELECT2 FLOATING LABEL
    $('#akun_coa_subsidi, #akun_coa_biaya, #akun_coa_kopek')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    const getDataRawMaterialI = function() {
        var department_id = $('#divisi_id').val();
        var bulan = $('#tanggal').val();
        if (department_id && bulan) {
            setLoading();
            $.ajax({
                url: `<?= base_url('rasio/get-barang-digunakan'); ?>`,
                method: "GET",
                data: {
                    department: department_id,
                    bulan: bulan,
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Produksi Tidak Ada',
                            confirmButtonColor: '#4e73df',
                        })
                    }
                },
            });
            $.ajax({
                url: `<?= base_url('rasio/get-barang-jadi'); ?>`,
                method: "GET",
                data: {
                    department: department_id,
                    bulan: bulan,
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Produksi Tidak Ada',
                            confirmButtonColor: '#4e73df',
                        })
                    }
                },
            });
        }
    }

    const getDataRawMaterialII = function() {
        var department_id = $('#divisi_id').val();
        var bulan = $('#tanggal').val();
        if (department_id && bulan) {
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Produksi Tidak Ada',
                            confirmButtonColor: '#4e73df',
                        })
                    }
                },
            });
            $.ajax({
                url: `<?= base_url('rasio/get-barang-jadi'); ?>`,
                method: "GET",
                data: {
                    department: department_id,
                    bulan: bulan,
                },
                dataType: "json",
                success: function(res) {
                    stopLoading()
                    if (res.status) {
                        list_items_barang_jadi_material_2 = [];
                        let no = 0;
                        // Iterate over each item in the response data
                        res.data.forEach(function(item) {
                            list_items_barang_jadi_material_2.push(item);
                        });
                        drawTableRasioMaterialII();
                    } else {
                        stopLoading()
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Produksi Tidak Ada',
                            confirmButtonColor: '#4e73df',
                        })
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
                totalQtyPO += item.totalQtyPO;
                totalHargaPO += item.totalHargaPO;
                hargaSatuanPO += item.hargaSatuanPO;
                totalQtyLPB += item.totalQtyLPB;
                totalHargaLPB += item.totalHargaLPB;
                hargaSatuanLPB += item.hargaSatuanLPB;
                // end counting
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.barang_name + ' - ' + item.spesifikasi + '</td>';
                row += '<td>' + item.totalQtyPO.toLocaleString() + '</td>';
                row += '<td>' + formatRupiah(item.totalHargaPO) + '</td>';
                row += '<td>' + formatRupiah(item.hargaSatuanPO) + '</td>';
                row += '<td>' + item.satuanPO + '</td>';
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
        if (list_items_barang_digunakan.length === 0) {
            row += '<tr><td colspan="10" class="text-center">Data Barang Tidak Ada</td></tr>';
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
                totalQtyPO += item.totalQtyPO;
                totalHargaPO += item.totalHargaPO;
                hargaSatuanPO += item.hargaSatuanPO;
                totalQtyLPB += item.totalQtyLPB;
                totalHargaLPB += item.totalHargaLPB;
                hargaSatuanLPB += item.hargaSatuanLPB;
                // end counting
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.barang_name + ' - ' + item.spesifikasi + '</td>';
                row += '<td>' + item.totalQtyPO.toLocaleString() + '</td>';
                row += '<td>' + formatRupiah(item.totalHargaPO) + '</td>';
                row += '<td>' + formatRupiah(item.hargaSatuanPO) + '</td>';
                row += '<td>' + item.satuanPO + '</td>';
                row += '<td>' + item.totalQtyLPB.toLocaleString() + '</td>';
                row += '<td>' + formatRupiah(item.totalHargaLPB) + '</td>';
                row += '<td>' + formatRupiah(item.hargaSatuanLPB) + '</td>';
                row += '<td>' + item.satuanLPB + '</td>';
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

    const drawTableRasioMaterialII = function() {
        $('.head-table-rasio-material2').empty();
        $('.body-table-rasio-material2').empty();
        $('.tfoot-rasio-material2').empty();
        var row = '';
        var rowDigunakan = '';
        var no = 1;
        if (list_items_barang_jadi_material_2.length === 0) {
            row += '<tr><td colspan="7" class="text-center">Data Barang Tidak Ada</td></tr>';
            $('.tfoot-rasio').append(row);
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
            var banyakBarangJadi = list_items_barang_jadi_material_2.length;
            list_items_barang_digunakan_material_2.map((item, index) => {
                rowDigunakan += '<tr style="color:whitesmoke;text-align: center;">';
                rowDigunakan += '<td>' + no + '</td>';
                rowDigunakan += '<td>' + item.parent_name + '</td>';
                rowDigunakan += '<td>' + item.barang_name + ' - ' + item.spesifikasi + '</td>';
                rowDigunakan += '<td>' + item.satuanPO + '</td>';
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
                var hargaSatuan = parseFloat($('input.harga-satuan-material2[data-index="' + rowIndex + '"][data-index2="' + colIndex + '"]').val().replace(/Rp|\./g, ""));
                var totalHarga = qty * hargaSatuan;
                $('input.harga-material2[data-index="' + rowIndex + '"][data-index2="' + colIndex + '"]').val(formatRupiah(totalHarga));

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
        var amount = $('#hargaTotalPenerimaan').val();
        var hargaTotalPenerimaan = parseFloat(amount.replace(/Rp|\./g, ""));
        if (list_items_barang_jadi.length === 0) {
            row += '<tr><td colspan="7" class="text-center">Data Barang Tidak Ada</td></tr>';
            $('.tfoot-rasio').append(row);
        } else {
            var rasio = 0;
            var rasioTotal = 0;
            var total = 0;
            var totalHargaRasio = 0;
            var totalTotalHargaRasio = 0;
            list_items_barang_jadi.map((item, index) => {
                total = item.totalQtyAll // hitung total barang
                rasio = (item.qtyTotal / item.totalQtyAll) * 100; // Perhitungan rasio
                totalHargaRasio = (hargaTotalPenerimaan * rasio.toFixed(2)) / 100;
                rasioTotal += rasio;
                totalTotalHargaRasio += totalHargaRasio;
                row += '<tr style="color:whitesmoke;text-align: center;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.kode_barang + '</td>';
                row += '<td>' + item.barang_name + ' - ' + item.spesifikasi + '</td>';
                row += '<td>' + item.kode_satuan + '</td>';
                row += '<td>' +
                    '<input class="form-control jumlah-barang text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="' + index + '" value="' + item.qtyTotal + '">' +
                    '</td>';
                row += '<td>' +
                    '<input class="form-control rasio text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="' + index + '" value="' + rasio.toFixed(2) + '%">' + // Ubah nilai rasio menjadi persentase dengan dua angka di belakang koma
                    '</td>';
                row += '<td>' +
                    '<input class="form-control harga text-center" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" data-index="' + index + '" value="' + formatRupiah(totalHargaRasio) + '">' +
                    '</td>';
                row += '</tr>';
                no++;
            });
            rowFooter += '<tr>';
            rowFooter += '<td colspan="4"></td>';
            rowFooter += '<td>' +
                '<input class="form-control jumlah-barang-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" value="' + total + '">' +
                '</td>';
            rowFooter += '<td>' +
                '<input class="form-control rasio-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" value="' + rasioTotal.toFixed(2) + '%">' + // Ubah nilai rasio menjadi persentase dengan dua angka di belakang koma
                '</td>';
            rowFooter += '<td>' +
                '<input class="form-control harga-total text-center" readonly oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control" type="text" value="' + formatRupiah(totalTotalHargaRasio) + '">' +
                '</td>';
            rowFooter += '</tr>';
            $('.tfoot-rasio').append(rowFooter);
            $('.body-table-rasio').append(row);

            // Update total harga jika ada perubahan pada input dengan kelas harga
            $('.harga').on('input', function() {
                var totalHarga = 0;
                $('.harga').each(function() {
                    var harga = parseFloat($(this).val().replace(/Rp|\./g, ""));
                    totalHarga += isNaN(harga) ? 0 : harga;
                    $(this).val(formatRupiah(harga));
                });
                $('.harga-total').val(formatRupiah(totalHarga));
            });
        }
    }

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

            $(this).addClass('active');

            $('#rawMaterialICard').show();
            $('#rawMaterialIICard').hide();
        });

        $('#rawMaterialIITab').click(function(event) {
            event.preventDefault();
            $('#rawMaterialITab').removeClass('active');

            $(this).addClass('active');

            $('#rawMaterialICard').hide();
            $('#rawMaterialIICard').show();
        });
        // end fungsi tab

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
            console.log(list_items_barang_jadi_material_2);
            console.log(list_items_barang_digunakan_material_2);
            if ($(".create-form").valid()) {
                var isValid = true;

                var hargaTotal = $(".harga-total").val();
                var hargaTotalPenerimaan = $(".hargaTotalPenerimaan").val();

                var formatedHargaTotal = parseFloat(hargaTotal.replace(/Rp|\./g, ""));
                var formatedHargaTotalPenerimaan = parseFloat(hargaTotalPenerimaan.replace(/Rp|\./g, ""));

                if (formatedHargaTotal != formatedHargaTotalPenerimaan) {
                    isValid = false;
                }

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
                            data.append("items_digunakan_material_2", JSON.stringify(list_items_barang_digunakan_material_2));

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