<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($jasaVendorIn) ? "Tambah Jasa Vendor Barang Masuk" : "Update Jasa Vendor Barang Masuk" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("jasa-vendor-in"); ?>">
                Kembali
            </a>
            <?php if (!empty($jasaVendorIn)) : ?>
                <?php if ($jasaVendorIn['status_posting'] == "0") : ?>
                    <?php if (can('Jasa Vendor', 'Barang Masuk', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="remove('<?= encrypt($jasaVendorIn['id']); ?>')">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Barang Masuk', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="posting('<?= encrypt($jasaVendorIn['id']); ?>')">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Barang Masuk', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("jasa-vendor-in/print/"); ?><?= encrypt($jasaVendorIn['id']); ?>')">
                            Print
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Barang Masuk', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if (can('Jasa Vendor', 'Barang Masuk', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("jasa-vendor-in/print/"); ?><?= encrypt($jasaVendorIn['id']); ?>')">
                            Print
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else : ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Pemasukkan Barang</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id" value="<?= !empty($jasaVendorIn) ? encrypt($jasaVendorIn['id']) : '' ?>" class="id">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($jasaVendorIn) ? 'readonly' : '' ?> autocomplete="one-time-code" class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime(!empty($jasaVendorIn) ? $jasaVendorIn['tanggal'] : $tanggal)); ?>">
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" <?= !empty($jasaVendorIn) ? 'disabled=true' : ''; ?> value="<?= !empty($jasaVendorIn) ? $jasaVendorIn['no_penerimaan_surat_jalan'] : "TOBA-VBM//" . date('m') . "/1/" . date('Y'); ?>" type="text" class="form-control no_penerimaan_surat_jalan" id="no_penerimaan_surat_jalan" name="no_penerimaan_surat_jalan" placeholder="No. Surat Jalan">
                                    <label for="floatingInput">No Penerimaan Surat Jalan</label>
                                </div>
                                <div style="<?= !empty($jasaVendorIn) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($jasaVendorIn) ? ($jasaVendorIn['status_posting'] ? 'disabled' : '') : '' ?> class="form-select vendor_id" id="vendor_id" name="vendor_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($vendor as $v) : ?>
                                    <option <?= !empty($jasaVendorIn) ? ($jasaVendorIn['vendor_id'] == $v['id'] ? 'selected' : '') : '' ?> value="<?= $v['id'] ?>">
                                        <?= strtoupper($v['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Vendor</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($jasaVendorIn) ? ($jasaVendorIn['status_posting'] ? 'disabled' : '') : '' ?> class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($divisi)) : ?>
                                    <?php foreach ($divisi as $d) : ?>
                                        <option <?= $jasaVendorIn['divisi_id'] == $d['id'] ? 'selected' : '' ?> value="<?= $d['id'] ?>">
                                            <?= $d['divisi'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($jasaVendorIn) ? ($jasaVendorIn['status_posting'] ? 'disabled' : '') : '' ?> class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($warehouse)) : ?>
                                    <?php foreach ($warehouse as $w) : ?>
                                        <option <?= $jasaVendorIn['warehouse_id'] == $w['id'] ? 'selected' : '' ?> value="<?= $w['id'] ?>">
                                            <?= $w['warehouse_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Warehouse</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($jasaVendorIn) ? ($jasaVendorIn['status_posting'] ? 'disabled' : '') : '' ?> multiple class="form-select multiple_jasa_vendor_out_id" name="multiple_jasa_vendor_out_id[]" id="multiple_jasa_vendor_out_id[]">
                                <option value=""></option>
                                <?php if (!empty($jasaVendorIn)) : ?>
                                    <?php foreach (json_decode($jasaVendorIn['multiple_jasa_vendor_out_id']) as $i => $p) : ?>
                                        <option selected value="<?= $p ?>"><?= json_decode($jasaVendorIn['multiple_jasa_vendor_out_no'])[$i] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">No. Surat Jalan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating" style="height: 50px;">
                            <select <?= !empty($jasaVendorIn) ? ($jasaVendorIn['status_posting'] ? 'disabled' : '') : '' ?> class="form-select status_closed_jasa_vendor_out" name="status_closed_jasa_vendor_out" id="status_closed_jasa_vendor_out">
                                <option value=""></option>
                                <option <?= !empty($jasaVendorIn) ? ($jasaVendorIn['status_closed_jasa_vendor_out'] == "0" ? 'selected' : '') : '' ?> value="0">OPEN SURAT JALAN</option>
                                <option <?= !empty($jasaVendorIn) ? ($jasaVendorIn['status_closed_jasa_vendor_out'] == "1" ? 'selected' : '') : '' ?> value="1">CLOSE SURAT JALAN</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tutup No Surat Jalan</label>
                        </div>
                        <small class="mb-3 mt-1"><i>Status Open Berarti Surat Jalan Masih Bisa Digunakan Kembali, Status Close Berarti Surat Jalan Tidak Dapat Digunakan Kembali</i></small>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($jasaVendorIn) ? ($jasaVendorIn['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Keterangan" value="<?= !empty($jasaVendorIn) ? $jasaVendorIn['no_surat_jalan_vendor'] : '' ?>" class="form-control no_surat_jalan_vendor" id="no_surat_jalan_vendor" name="no_surat_jalan_vendor" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">No Surat Jalan Vendor (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($jasaVendorIn) ? ($jasaVendorIn['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Keterangan" value="<?= !empty($jasaVendorIn) ? $jasaVendorIn['keterangan'] : '' ?>" class="form-control keterangan" id="keterangan" name="keterangan" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Keterangan (Opsional)</label>
                        </div>
                    </div>
                </div>

            </form>
            <br>
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Daftar Detail Barang Keluar Ke Vendor</label>
                </div>
                <div class="col-md-12 col-table-button-tts">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" border="1" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;" colspan="6">Detail Dokumen Pabean</th>
                                    <th style="text-align: center;" colspan="10">Daftar Barang Keluar</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">Tipe Barang</th>
                                    <th style="text-align: center;">Asal Barang</th>
                                    <th style="text-align: center;">No Dokumen</th>
                                    <th style="text-align: center;">Dokumen Pabean</th>
                                    <th style="text-align: center;">Tgl Penerimaan</th>
                                    <th style="text-align: center;">Supplier</th>


                                    <th style="text-align: center;">Barang - Spesifikasi</th>
                                    <th style="text-align: center;">Qty Keluar</th>
                                    <th style="text-align: center;">Satuan Keluar</th>

                                </tr>
                            </thead>
                            <tbody class="body-table">
                            </tbody>
                            <tfoot class="foot-detail-table" id="foot-detail-table">
                                <tr>
                                    <td colspan="10" style="text-align: center;">
                                        Tidak Ada Barang
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Form Input Daftar Barang Masuk Dari Vendor</label>
                </div>

                <div class="col-md-12 col-table-button-tts">
                    <div class="table-responsive">

                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable3">
                            <thead>
                                <tr>
                                    <th style="text-align: center;" colspan="7">Daftar Barang Keluar</th>
                                    <th style="text-align: center;" colspan="1">Input Barang Masuk</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center; width:10px;" scope="col">No</th>
                                    <th style="text-align: center;" scope="col">Tipe Barang</th>
                                    <th style="text-align: center;" scope="col">Kode</th>
                                    <th style="text-align: center;" scope="col">Barang-Spesifikasi</th>
                                    <th style="text-align: center;" scope="col">Qty Keluar</th>
                                    <th style="text-align: center;" scope="col">Satuan Keluar</th>
                                    <th style="text-align: center;" scope="col">Total Masuk</th>
                                    <th style="text-align: center; width:10px;" scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot class="foot-detail-table" id="foot-detail-table">
                                <tr>
                                    <td colspan="8" style="text-align: center;">
                                        Tidak Ada Barang
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal add-modal" id="update_detail_barang" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail List Barang Masuk</h5>
            </div>
            <div class="modal-body">
                <form class="create-form-barang-masuk" role="form" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="barang1_id" id="barang1_id" class="barang1_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control barang_keluar_name" id="barang_keluar_name" name="barang_keluar_name" placeholder="Barang Keluar">
                                <label for="floatingInput">Barang Keluar</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select <?= !empty($jasaVendorIn) ? ($jasaVendorIn['status_posting'] == '1' ? 'disabled' : '') : '' ?> class="form-select spesifikasi_in_id" name="spesifikasi_in_id" id="spesifikasi_in_id">
                                        <option value=""></option>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Pilih Barang Masuk</label>
                                </div>
                                <div class="input-group-append" style="height:50px;">
                                    <button <?= !empty($jasaVendorIn) ? ($jasaVendorIn['status_posting'] == '1' ? 'disabled' : '') : '' ?> class="btn btn-success btn-stock-in-add" id="btn-stock-in-add" data-toggle="modal" type="button">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control qty_barang_keluar" id="qty_barang_keluar" name="qty_barang_keluar" placeholder="Qty Barang Keluar">
                                <label for="floatingInput">Qty Barang Keluar</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control satuan_barang_keluar" id="satuan_barang_keluar" name="satuan_barang_keluar" placeholder="Satuan Barang Keluar">
                                <label for="floatingInput">Satuan Barang Keluar</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-table-button-tts">
                            <div class="table-responsive">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable2">
                                    <thead>
                                        <tr style="text-align: center;">
                                            <th scope="col">No</th>
                                            <th scope="col">Kode Barang</th>
                                            <th scope="col">Barang</th>
                                            <th scope="col">Satuan</th>
                                            <th scope="col">Qty Kotor</th>
                                            <th scope="col">Qty Bersih</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot class="foot-detail-table" id="foot-detail-table">
                                        <tr>
                                            <td colspan="7" style="text-align: center;">
                                                Tidak ada barang masuk </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-barang-masuk mr-2">Kembali</button>
                <?php if (!empty($jasaVendorIn)) : ?>
                    <?php if ($jasaVendorIn['status_posting'] != '1') : ?>
                        <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
                    <?php else : ?>

                    <?php endif; ?>
                <?php else : ?>
                    <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    var listBarang = [];
    var listBarangGroup = [];

    <?php if (!empty($jasaVendorIn)) : ?>
        let arr = $('.multiple_jasa_vendor_out_id').val();
        $.ajax({
            url: `<?= base_url("jasa-vendor-in/list-barang"); ?>`,
            method: "GET",
            data: {
                multiple_jasa_vendor_out_id: JSON.stringify(arr),
                id: $('.id').val()
            },

            dataType: "json",
            success: function(res) {
                listBarang = [];
                listBarangGroup = [];
                listBarang = res.data.dataDetail;
                listBarangGroup = res.data.dataGroup;
                drawTable(listBarang);
                drawTable3(listBarangGroup);
            }
        })
    <?php endif; ?>

    $('#vendor_id').select2({
        placeholder: "Pilih Vendor",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // LIST DIVISI
        getListDivisi();
        listBarang = [];
    });

    $("#tanggal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // LIST WAREHOUSE
        getListWarehouse();
        listBarang = [];
    });

    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // LIST SURAT JALAN
        getListJasaVendorOut();
        changeStatus();
        listBarang = [];
    });

    $('.multiple_jasa_vendor_out_id').select2({
        placeholder: "Pilih Surat Jalan",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function() {
        let arr = $('.multiple_jasa_vendor_out_id').val();
        $.ajax({
            url: `<?= base_url("jasa-vendor-in/list-barang"); ?>`,
            method: "GET",
            data: {
                multiple_jasa_vendor_out_id: JSON.stringify(arr),
                id: $('.id').val()
            },
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            dataType: "json",
            success: function(res) {
                listBarang = [];
                listBarangGroup = [];
                listBarang = res.data.dataDetail;
                listBarangGroup = res.data.dataGroup;
                drawTable(listBarang);
                drawTable3(listBarangGroup);
            }
        })
    });

    // VALIDATOR
    var validator = $(".create-form").validate({
        rules: {
            no_penerimaan_surat_jalan: {
                required: true
            },
            vendor_id: {
                required: true
            },
            divisi_id: {
                required: true
            },
            warehouse_id: {
                required: true
            },
            status_closed_jasa_vendor_out: {
                required: true
            },
        },
        messages: {
            no_penerimaan_surat_jalan: {
                required: "No penerimaan surat jalan wajib diisi"
            },
            vendor_id: {
                required: "Vendor wajib diisi"
            },
            divisi_id: {
                required: "Departemen wajib diisi"
            },
            warehouse_id: {
                required: "Warehouse wajib diisi"
            },
            status_closed_jasa_vendor_out: {
                required: "Pilih Status Surat Jalan"
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

    var validatorBarangMasuk = $(".create-form-barang-masuk").validate({
        rules: {
            spesifikasi_in_id: {
                required: true
            },
        },
        messages: {
            spesifikasi_in_id: {
                required: "Barang masuk wajib diisi"
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

    $('.btn-submit-parent').click(function() {
        if (listBarang.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Barang yang akan diterima dari vendor tidak boleh kosong !',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        } else {
            if ($('.create-form').valid()) {
                var isValidBarang = true;
                var barangError = null;

                $.each(listBarangGroup, function(i, v) {
                    if (v.list_barang_masuk.length == 0) {
                        isValidBarang = false;
                        barangError = v;
                    }
                });

                if (!isValidBarang) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Barang keluar ' + barangError.barang_out + ', output barang nya belum ada !',
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                } else {

                    // APPEND LIST BARANG GROUP KE LIST BARANG
                    // LOOP LIST BARANG GROUP
                    $.each(listBarangGroup, function(i, v) {
                        var totalDetailBarangKeluar = 0;
                        var qtyKotorRes = 0;
                        var qtyBersihRes = 0;

                        // LOOP LIST BARANG
                        $.each(listBarang, function(j, k) {

                            if (v.barang1_id == k.barang1_id) {
                                totalDetailBarangKeluar++;
                            }
                        });

                        $.each(listBarang, function(j, k) {

                            if (v.barang1_id == k.barang1_id) {

                                listBarang[j].list_barang_masuk = [];

                                $.each(v.list_barang_masuk, function(y, z) {

                                    qtyKotorRes = z.qty_kotor / totalDetailBarangKeluar;
                                    qtyBersihRes = z.qty_bersih / totalDetailBarangKeluar;
                                    qtyKotorRes = qtyKotorRes.toFixed(2);
                                    qtyBersihRes = qtyBersihRes.toFixed(2);

                                    listBarang[j].list_barang_masuk = listBarang[j].list_barang_masuk.filter(item => item.spesifikasi_in_id !== z.spesifikasi_in_id);

                                    listBarang[j].list_barang_masuk.push({
                                        jasa_vendor_out_detail_id: k.jasa_vendor_out_detail_id,
                                        barang1_id: z.barang1_id,
                                        spesifikasi_in_id: z.spesifikasi_in_id,
                                        kode_barang_in: z.kode_barang_in,
                                        barang_name_in: z.barang_name_in,
                                        kode_satuan_in: z.kode_satuan_in,
                                        qty_kotor: qtyKotorRes,
                                        qty_bersih: qtyBersihRes

                                    });

                                })

                            }

                        });

                    });

                    Swal.fire({
                        icon: 'question',
                        title: 'Simpan Data ?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Kembali',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let id = $('#id').val();
                            let data = new FormData(document.querySelector(".create-form"));
                            data.append('listBarang', JSON.stringify(listBarang));

                            if (id) {
                                // UPDATE
                                $.ajax({
                                    url: "<?= base_url("jasa-vendor-in/update"); ?>",
                                    data: data,
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                        setLoading();
                                    },
                                    complete: function() {
                                        stopLoading()
                                    },
                                    method: "POST",
                                    dataType: "json",
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        if (response.status) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                                confirmButtonText: 'Ok'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = "<?= base_url("jasa-vendor-in") ?>";
                                                }
                                            });
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                                confirmButtonText: 'Ok'
                                            });
                                        }

                                    },
                                });
                            } else {
                                // INSERT
                                $.ajax({
                                    url: "<?= base_url("jasa-vendor-in/save"); ?>",
                                    data: data,
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                        setLoading();
                                    },
                                    complete: function() {
                                        stopLoading()
                                    },
                                    method: "POST",
                                    dataType: "json",
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        if (response.status) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                                confirmButtonText: 'Ok'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = "<?= base_url("jasa-vendor-in") ?>";
                                                }
                                            });
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                                confirmButtonText: 'Ok'
                                            });
                                        }

                                    },
                                });
                            }
                        }
                    });
                }

            }
        }
    });

    $('#status_closed_jasa_vendor_out').select2({
        placeholder: "Pilih Status Surat Jalan",
        theme: "bootstrap-5",
        allowClear: true,
    }).change(function() {

    });

    $('#spesifikasi_in_id').select2({
        placeholder: "Pilih Barang Masuk",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#update_detail_barang')
    }).change(function() {
        // LIST STOK BARANG MASUK

    });

    $('#btn-stock-in-add').click(function() {
        if ($('.create-form-barang-masuk').valid()) {
            var barang1_id = $('#barang1_id').val();
            var spesifikasi_in_id = $('#spesifikasi_in_id option:selected').data('spesifikasi_id');
            var kode_barang_in = $('#spesifikasi_in_id option:selected').data('kode_barang');
            var barang_name_in = $('#spesifikasi_in_id option:selected').data('barang');
            var kode_satuan_in = $('#spesifikasi_in_id option:selected').data('kode_satuan');
            var barangFirst = null;
            var index = null;

            $.each(listBarangGroup, function(i, v) {
                if (v.barang1_id == barang1_id) {
                    index = i;
                    barangFirst = v;
                }
            });

            // EACH 
            var isAdd = false;
            $.each(barangFirst.list_barang_masuk, function(i, v) {
                if (v.spesifikasi_in_id == spesifikasi_in_id) {
                    isAdd = true;
                }
            });

            if (!isAdd) {
                listBarangGroup[index].list_barang_masuk.push({
                    barang1_id: barangFirst.barang1_id,
                    spesifikasi_in_id: spesifikasi_in_id,
                    kode_barang_in: kode_barang_in,
                    barang_name_in: barang_name_in,
                    kode_satuan_in: kode_satuan_in,
                    qty_kotor: 0,
                    qty_bersih: 0
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Barang masuk sudah ada !',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            }

            // DRAW BARANG MASUK
            drawTable2(barang1_id, listBarangGroup)

        }
    });

    $('.btn-submit-detail').click(function() {
        var barang1_id = $('#barang1_id').val();
        var index = null;
        var barangError = null;
        var isValidKotor = true;
        var isValidBersih = true;

        $.each(listBarangGroup, function(i, v) {
            if (v.barang1_id == barang1_id) {
                index = i;
            }
        });

        $.each(listBarangGroup[index].list_barang_masuk, function(i, v) {
            var element_qty_kotor = $('input[data-spesifikasi_in_id="' + v.spesifikasi_in_id + '"].qty_kotor');
            var element_qty_bersih = $('input[data-spesifikasi_in_id="' + v.spesifikasi_in_id + '"].qty_bersih');

            var input_qty_kotor = parseFloat(element_qty_kotor.val());
            var input_qty_bersih = parseFloat(element_qty_bersih.val());

            if (isNaN(input_qty_kotor) || input_qty_kotor == undefined || input_qty_kotor == 0) {
                isValidKotor = false;
                barangError = v;
            } else {
                listBarangGroup[index].list_barang_masuk[i].qty_kotor = input_qty_kotor;
                listBarangGroup[index].list_barang_masuk[i].qty_bersih = input_qty_bersih;
            }

            if (isNaN(input_qty_bersih) || input_qty_bersih == undefined || input_qty_bersih == 0) {
                isValidBersih = false;
                barangError = v;
            } else {
                listBarangGroup[index].list_barang_masuk[i].qty_kotor = input_qty_kotor;
                listBarangGroup[index].list_barang_masuk[i].qty_bersih = input_qty_bersih;
            }
        });


        if (isValidKotor == false || isValidBersih == false) {
            Swal.fire({
                icon: 'error',
                title: 'Qty kotor atau Qty bersih barang ' + barangError.barang_name_in + ' wajib diisi !',
                confirmButtonColor: '#4e73df',
            });
        } else {
            drawTable3(listBarangGroup);
            $('#update_detail_barang').modal('hide');
        }

    });

    $("#vendor_id,#divisi_id,#warehouse_id,.multiple_jasa_vendor_out_id,#spesifikasi_in_id,#status_closed_jasa_vendor_out")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.btn-discard-barang-masuk').click(function() {
        $('#update_detail_barang').modal('hide');
        drawTable3(listBarangGroup);

    });

    function displayDetailModal(barang1_id) {
        // RESET VALIDATOR
        validatorBarangMasuk.resetForm();
        validatorBarangMasuk.reset();

        var barangFirst = null;
        $.each(listBarangGroup, function(i, v) {
            if (v.barang1_id == barang1_id) {
                barangFirst = v;
            }
        });
        $('#barang1_id').val(barang1_id);
        $('#barang_keluar_name').val('(' + barangFirst.kode_barang_out + ') ' + barangFirst.barang_out);
        $('#satuan_barang_keluar').val(barangFirst.satuan_out);
        $('#qty_barang_keluar').val(barangFirst.qty_out);
        if (barangFirst != null) {
            $.ajax({
                url: `<?= base_url('jasa-vendor-in/list-barang-masuk'); ?>`,
                method: "GET",
                beforeSend: function() {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                data: {
                    barang1_id: barangFirst.barang1_id,
                    type_barang: "bahan_baku",
                },
                dataType: "json",
                success: function(res) {
                    $('#update_detail_barang').modal('show')

                    $("#spesifikasi_in_id").empty()
                    $("#spesifikasi_in_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $("#spesifikasi_in_id").append(`<option data-spesifikasi_id="${item.spesifikasi_id}" data-kode_barang="${item.kode_barang}" data-barang="${item.barang}" data-kode_satuan="${item.kode_satuan}" value="${item.spesifikasi_id}">(${item.kode_barang}) ${item.barang}</option>`)
                    })
                    $("#spesifikasi_in_id").val(null);

                    drawTable2(barang1_id, listBarangGroup);
                }
            });
        } else {
            console.log("System error ");
        }
    }


    function getListDivisi() {
        // GET LIST DIVISI
        $.ajax({
            url: `<?= base_url('jasa-vendor-in/divisi'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                vendor_id: $(".vendor_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".divisi_id").empty()
                $(".divisi_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".divisi_id").append(`<option value="${item.id}">${item.divisi}</option>`)
                })
                $(".divisi_id").val();
            }
        });
    }

    function drawTable(listBarang) {
        const table = $('#dataTable');
        table.find('tbody').empty();
        table.find('tfoot').empty();

        if (listBarang.length == 0) {
            var newRow = $('<tr>');
            newRow.append($('<td colspan="10" style="text-align:center">Tidak Ada Barang</td>'));
            table.find('tfoot').append(newRow);
        } else {
            var no = 1;
            $.each(listBarang, function(i, v) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td>').text(no++));
                newRow.append($('<td>').text(v.tipe_barang));
                newRow.append($('<td>').text(v.sumber));
                newRow.append($('<td>').text(v.stock_dokumen));
                newRow.append($('<td>').text(v.bc_name + '/' + v.no_aju));
                newRow.append($('<td>').text(v.stock_date));
                newRow.append($('<td>').text(v.supplier_name));
                newRow.append($('<td>').text(v.barang_out));
                newRow.append($('<td>').text(v.qty_out));
                newRow.append($('<td>').text(v.satuan_out));
                table.find('tbody').append(newRow);
            });
        }
    }

    function drawTable2(barang1_id, listBarangGroup) {
        var listBarangFirst = null;
        const table = $('#dataTable2');
        table.find('tbody').empty();
        table.find('tfoot').empty();

        $.each(listBarangGroup, function(i, v) {
            if (v.barang1_id == barang1_id) {
                listBarangFirst = v;
            }
        });

        if (listBarangFirst.list_barang_masuk.length == 0) {
            var newRow = $('<tr>');
            newRow.append($('<td colspan="7" style="text-align:center">Tidak Ada Barang</td>'));
            table.find('tfoot').append(newRow);
        } else {
            var no = 1;
            $.each(listBarangFirst.list_barang_masuk, function(i, v) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td>').text(no++));
                newRow.append($('<td>').text(v.kode_barang_in));
                newRow.append($('<td>').text(v.barang_name_in));
                newRow.append($('<td>').text(v.kode_satuan_in));
                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <input <?= !empty($jasaVendorIn) ? (($jasaVendorIn['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control qty_kotor" oninput="preventNegativeInput(this)" autocomplete="one-time-code" data-spesifikasi_in_id="${v.spesifikasi_in_id}" class="form-control qty_kotor" type="text" value="${v.qty_kotor.toFixed(2)}">
                `
                ));
                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <input <?= !empty($jasaVendorIn) ? (($jasaVendorIn['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control qty_bersih" oninput="preventNegativeInput(this)" autocomplete="one-time-code" data-spesifikasi_in_id="${v.spesifikasi_in_id}" class="form-control qty_bersih" type="text" value="${v.qty_bersih.toFixed(2)}">
                `
                ));
                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <button <?= !empty($jasaVendorIn) ? (($jasaVendorIn['status_posting'] == "1") ? 'disabled' : '') : '' ?> type="button" class="btn btn-danger" onclick="deleteDetail(${v.barang1_id}, '${v.spesifikasi_in_id}')" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                `
                ));
                table.find('tbody').append(newRow);
            });
        }
    }

    function drawTable3(listBarangGroup) {
        const table = $('#dataTable3');
        table.find('tbody').empty();
        table.find('tfoot').empty();

        if (listBarangGroup.length == 0) {
            var newRow = $('<tr>');
            newRow.append($('<td colspan="8" style="text-align:center">Tidak Ada Barang</td>'));
            table.find('tfoot').append(newRow);
        } else {
            var no = 1;
            $.each(listBarangGroup, function(i, v) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td>').text(no++));
                newRow.append($('<td>').text(v.tipe_barang));
                newRow.append($('<td>').text(v.kode_barang_out));
                newRow.append($('<td>').text(v.barang_out));
                newRow.append($('<td>').text(v.qty_out.toFixed(2)));
                newRow.append($('<td>').text(v.satuan_out));
                newRow.append($('<td>').text(v.list_barang_masuk.length + " Barang"));
                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <button type="button" class="btn btn-primary" onclick="displayDetailModal(${v.barang1_id})" data-toggle="tooltip"><i class="fas fa-pencil-alt"></i></button>
                `
                ));

                table.find('tbody').append(newRow);
            });
        }
    }

    function deleteDetail(barang1_id, spesifikasi_in_id) {
        var index = null;
        var indexToRemove = -1;

        for (let i = 0; i < listBarangGroup.length; i++) {
            if (listBarangGroup[i].barang1_id == barang1_id) {
                index = i;
                break;
            }
        }

        for (let i = 0; i < listBarangGroup[index].list_barang_masuk.length; i++) {
            if (listBarangGroup[index].list_barang_masuk[i].spesifikasi_in_id == spesifikasi_in_id) {
                indexToRemove = i;
                break;
            }
        }

        if (indexToRemove !== -1) {
            listBarangGroup[index].list_barang_masuk.splice(indexToRemove, 1);
            drawTable2(barang1_id, listBarangGroup);
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

    function getListWarehouse() {
        // GET LIST DIVISI
        $.ajax({
            url: `<?= base_url('jasa-vendor-in/warehouse'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                vendor_id: $(".vendor_id option:selected").val(),
                divisi_id: $('.divisi_id option:selected').val()
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_id").empty()
                $(".warehouse_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
                $(".warehouse_id").val();
            }
        });
    }

    function getListJasaVendorOut() {
        $.ajax({
            url: `<?= base_url('jasa-vendor-in/list-jasa-vendor-out'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                vendor_id: $(".vendor_id option:selected").val(),
                divisi_id: $('.divisi_id option:selected').val(),
                warehouse_id: $(".warehouse_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".multiple_jasa_vendor_out_id").empty()
                $(".multiple_jasa_vendor_out_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".multiple_jasa_vendor_out_id").append(`<option value="${item.id}">${item.no_surat_jalan}</option>`)
                })
                $(".multiple_jasa_vendor_out_id").val();

            }
        });
    }

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $(".no_penerimaan_surat_jalan").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("jasa-vendor-in/get-jasa-vendor-in-no"); ?>`,
                method: "GET",
                data: {
                    warehouse_id: $('#warehouse_id option:selected').val()
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".no_penerimaan_surat_jalan").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".no_penerimaan_surat_jalan").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".no_penerimaan_surat_jalan").val("");
                    }
                }
            })
        } else {
            $(".no_penerimaan_surat_jalan").attr("readonly", false);
            $(".no_penerimaan_surat_jalan").val("");
        }
    }

    const print = function(url) {
        window.open(url, "_blank");
    }

    const posting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Jasa Vendor Barang Masuk ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("jasa-vendor-in/posting"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        csrf.val(response.token);
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            }).then((result) => {
                                window.location.href = "<?= base_url("jasa-vendor-in") ?>";
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    },
                });
            }
        })
    }

    const remove = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Jasa Vendor Barang Masuk ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("jasa-vendor-in/delete"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            }).then((result) => {
                                window.location.href = "<?= base_url("jasa-vendor-in") ?>";
                            });
                        }
                    },
                });
            }
        })
    }
</script>

<?= $this->endSection(); ?>