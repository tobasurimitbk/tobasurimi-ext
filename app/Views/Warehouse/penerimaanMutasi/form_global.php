<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($penerimaanMutasiGlobal) ? "Tambah Penerimaan Mutasi BC 2.7" : "Update Penerimaan Mutasi BC 2.7" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("penerimaan-mutasi/global"); ?>">
                Batal
            </a>
            <?php if (!empty($penerimaanMutasiGlobal)) : ?>
                <?php if ($penerimaanMutasiGlobal['status_posting'] == "0") : ?>
                    <?php if (can('Inventori', 'Penerimaan Mutasi', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="remove('<?= encrypt($penerimaanMutasiGlobal['id']); ?>')">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Inventori', 'Penerimaan Mutasi', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="posting('<?= encrypt($penerimaanMutasiGlobal['id']); ?>')">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Inventori', 'Penerimaan Mutasi', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("penerimaan-mutasi/print-global/"); ?><?= encrypt($penerimaanMutasiGlobal['id']); ?>')">
                            Print
                        </button>
                    <?php endif; ?>
                    <?php if (can('Inventori', 'Penerimaan Mutasi', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>

                <?php else : ?>
                    <?php if (can('Inventori', 'Penerimaan Mutasi', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("penerimaan-mutasi/print-global/"); ?><?= encrypt($penerimaanMutasiGlobal['id']); ?>')">
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
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">

                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('penerimaan-mutasi/create') ?>">Penerimaan Mutasi PPBKB</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Penerimaan Mutasi BC 2.7 (BC 2.7 IN)</a>
                    </li>
                </ul>

                <input type="hidden" name="id" id="id" value="<?= !empty($penerimaanMutasiGlobal) ? encrypt($penerimaanMutasiGlobal['id']) : '' ?>" class="id">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($penerimaanMutasiGlobal) ? ($penerimaanMutasiGlobal['status_posting'] == "1" ? "disabled" : '') : '' ?> autocomplete="one-time-code" class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime(!empty($penerimaanMutasiGlobal) ? $penerimaanMutasiGlobal['tanggal'] : $tanggal)); ?>">
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
                                    <input readonly autocomplete="one-time-code" <?= !empty($penerimaanMutasiGlobal) ? ($penerimaanMutasiGlobal['status_posting'] == "1" ? "disabled" : 'disabled') : ''; ?> value="<?= !empty($penerimaanMutasiGlobal) ? $penerimaanMutasiGlobal['penerimaan_mutasi_no'] : "PMG//" . date('m') . "/1/" . date('Y'); ?>" type="text" class="form-control penerimaan_mutasi_no" id="penerimaan_mutasi_no" name="penerimaan_mutasi_no" placeholder="No. Penerimaan Mutasi">
                                    <label for="floatingInput">No Penerimaan Mutasi</label>
                                </div>
                                <div style="<?= !empty($penerimaanMutasiGlobal) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($penerimaanMutasiGlobal) ?  ($penerimaanMutasiGlobal['status_posting'] == "1" ? "disabled" : 'disabled') : '' ?> class="form-select company_pengirim_id" id="company_pengirim_id" name="company_pengirim_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dropdownCompanyExcept as $d) : ?>
                                    <option <?= !empty($penerimaanMutasiGlobal) ? ($penerimaanMutasiGlobal['company_pengirim_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= strtoupper($d['company']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Company Pengirim</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($penerimaanMutasiGlobal) ? ($penerimaanMutasiGlobal['status_posting'] ? 'disabled' : '') : '' ?> class="form-select divisi_penerima_id" id="divisi_penerima_id" name="divisi_penerima_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= !empty($penerimaanMutasiGlobal) ? ($penerimaanMutasiGlobal['divisi_penerima_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen Penerima</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($penerimaanMutasiGlobal) ? ($penerimaanMutasiGlobal['status_posting'] ? 'disabled' : '') : '' ?> class="form-select warehouse_penerima_id" id="warehouse_penerima_id" name="warehouse_penerima_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($penerimaanMutasiGlobal)) :  ?>
                                    <?php foreach ($warehouse as $w) : ?>
                                        <option <?= $penerimaanMutasiGlobal['warehouse_penerima_id'] == $w['id'] ? 'selected' : '' ?> value="<?= $w['id'] ?>">
                                            <?= $w['warehouse_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Warehouse Penerima</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($penerimaanMutasiGlobal) ?  ($penerimaanMutasiGlobal['status_posting'] == "1" ? "disabled" : 'disabled') : ''; ?> multiple class="form-select multiple_mutasi_id" name="multiple_mutasi_id[]" id="multiple_mutasi_id[]">
                                <option value=""></option>
                                <?php if (!empty($penerimaanMutasiGlobal)) : ?>
                                    <?php foreach (json_decode($penerimaanMutasiGlobal['multiple_mutasi_id']) as $i => $p) : ?>
                                        <option selected value="<?= $p ?>"><?= json_decode($penerimaanMutasiGlobal['multiple_no_mutasi'])[$i] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">No. Mutasi</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($penerimaanMutasiGlobal) ? ($penerimaanMutasiGlobal['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Keterangan" value="<?= !empty($penerimaanMutasiGlobal) ? $penerimaanMutasiGlobal['keterangan'] : '' ?>" class="form-control keterangan" id="keterangan" name="keterangan" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Keterangan (Opsional)</label>
                        </div>
                    </div>
                </div>

            </form>
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Barang Yang Dikirim</label>
                    </div>
                </div>
            </div>
            <div class="row mt-3">


                <div class="col-md-12 col-table-button-tts">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">Tipe Barang</th>
                                    <th style="text-align: center;">Kode Barang (Asal)</th>
                                    <th style="text-align: center;">Barang - Spesifikasi (Asal)</th>
                                    <th style="text-align: center;">Dokumen Mutasi</th>
                                    <th style="text-align: center;">Dokumen Asal</th>
                                    <th style="text-align: center;">Departemen / Warehouse Pengirim</th>
                                    <th style="text-align: center;">Supplier</th>
                                    <th style="text-align: center;">No Purchase Order</th>
                                    <th style="text-align: center;">Qty Mutasi</th>
                                    <th style="text-align: center;">Qty Diterima Total</th>
                                    <th style="text-align: center;">Qty Sisa</th>
                                    <th style="text-align: center;">Satuan (Asal)</th>
                                    <th style="text-align: center;">Kode Barang (Diterima)</th>
                                    <th style="text-align: center;">Barang - Spesifikasi (Diterima)</th>
                                    <th style="text-align: center;">Qty (Diterima)</th>
                                    <th style="text-align: center;">Satuan (Diterima)</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="body-table">
                            </tbody>
                            <tfoot class="foot-detail-table" id="foot-detail-table">
                                <tr>
                                    <td colspan="18" style="text-align: center;">
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
<div class="modal add-modal" id="update_barang_masuk" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Barang Masuk (BC 2.7 Incoming)</h5>
            </div>
            <div class="modal-body">
                <form class="create-form-barang-masuk" role="form" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="tipe_barang" id="tipe_barang" class="tipe_barang">
                    <input type="hidden" name="mutasi_global_detail_id" id="mutasi_global_detail_id" class="mutasi_global_detail_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control company_asal_name" id="company_asal_name" name="company_asal_name" placeholder="Company Pengirim">
                                <label for="floatingInput">Company Pengirim</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control divisi_asal_name" id="divisi_asal_name" name="divisi_asal_name" placeholder="Departemen Asal">
                                <label for="floatingInput">Departemen Pengirim</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control warehouse_asal_name" id="warehouse_asal_name" name="warehouse_asal_name" placeholder="Warehouse Pengirim">
                                <label for="floatingInput">Warehouse Pengirim</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control barang_keluar_name" id="barang_keluar_name" name="barang_keluar_name" placeholder="Barang Keluar">
                                <label for="floatingInput">Barang Dikirim</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control dokumen_mutasi_name" id="dokumen_mutasi_name" name="dokumen_mutasi_name" placeholder="Dokumen Mutasi">
                                <label for="floatingInput">Dokumen Mutasi</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control dokumen_asal_name" id="dokumen_asal_name" name="dokumen_asal_name" placeholder="Dokumen Asal">
                                <label for="floatingInput">Dokumen Asal</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control divisi_penerima_name" id="divisi_penerima_name" name="divisi_penerima_name" placeholder="Departemen Penerima">
                                <label for="floatingInput">Departemen Penerima</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control warehouse_penerima_name" id="warehouse_penerima_name" name="warehouse_penerima_name" placeholder="Warehouse Penerima">
                                <label for="floatingInput">Warehouse Penerima</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control tipe_barang_name" id="tipe_barang_name" name="tipe_barang_name" placeholder="Tipe Barang">
                                <label for="floatingInput">Tipe Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating" style="height: 50px;">
                                <select <?= !empty($penerimaanMutasiGlobal) ? ($penerimaanMutasiGlobal['status_posting'] == '1' ? 'disabled' : '') : '' ?> class="form-select stock_mutasi_id" name="stock_mutasi_id" id="stock_mutasi_id">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Pilih Barang Masuk</label>
                            </div>
                            <small class=" mb-3">
                                <i>
                                    Hanya muncul barang yang ada di inventori sesuai dengan departemen dan warehouse penerima
                                </i>
                            </small>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input <?= !empty($penerimaanMutasiGlobal) ? ($penerimaanMutasiGlobal['status_posting'] == '1' ? 'disabled' : '') : '' ?> autocomplete="one-time-code" type="text" class="form-control qty_diterima_current" id="qty_diterima_current" name="qty_diterima_current" placeholder="Qty Diterima Sekarang" oninput="preventNegativeInput(this)">
                                <label for="floatingInput">Qty Barang Masuk</label>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-barang-masuk mr-2">Batal</button>
                <?php if (!empty($penerimaanMutasiGlobal)) : ?>
                    <?php if ($penerimaanMutasiGlobal['status_posting'] != '1') : ?>
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

    $(".tanggal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $('#company_pengirim_id').select2({
        placeholder: "Pilih Company Pengirim",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // NO MUTASI DROPDOWN
        getListMutasiGlobal();
    });

    $('#divisi_penerima_id').select2({
        placeholder: "Pilih Departemen Penerima",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // WAREHOUSE PENERIMA DROPDOWN DAN NO MUTASI
        getListWarehousePenerima();
        getListMutasiGlobal();
        changeStatus();

    });

    $('#warehouse_penerima_id').select2({
        placeholder: "Pilih Warehouse Penerima",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // WAREHOUSE PENERIMA DROPDOWN DAN NO MUTASI
        getListMutasiGlobal();

    });

    $('.multiple_mutasi_id').select2({
        placeholder: "Pilih Nomor Mutasi",
        theme: "bootstrap-5",
        allowClear: false,
    }).change(function() {
        let arr = $('.multiple_mutasi_id').val();
        $.ajax({
            url: `<?= base_url("penerimaan-mutasi/list-barang-global"); ?>`,
            method: "GET",
            data: {
                mutasi_global_id: JSON.stringify(arr),
                penerimaan_mutasi_global_id: $('.id').val()
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
                listBarang = res.data;
                drawTable(listBarang);
            }
        })
    });

    $('#stock_mutasi_id').select2({
        placeholder: "Pilih Barang Masuk (Hanya yang ada di inventori)",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {});


    $('.btn-discard-barang-masuk').click(function() {
        $('#update_barang_masuk').modal('hide');
    });

    $("#company_pengirim_id,.multiple_mutasi_id,#divisi_penerima_id,#warehouse_penerima_id,#stock_mutasi_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');


    // VALIDATOR
    var validator = $(".create-form").validate({
        rules: {
            tanggal: {
                required: true
            },
            penerimaan_mutasi_no: {
                required: true
            },
            company_pengirim_id: {
                required: true
            },

        },
        messages: {
            tanggal: {
                required: "Tanggal wajib diisi"
            },
            penerimaan_mutasi_no: {
                required: "No penerimaan wajib diisi"
            },
            company_pengirim_id: {
                required: "Departemen wajib diisi"
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
            stock_mutasi_id: {
                required: true
            },
            qty_diterima_current: {
                required: true
            },
        },
        messages: {
            stock_mutasi_id: {
                required: "Barang masuk wajib diisi"
            },
            qty_diterima_current: {
                required: "Qty diterima masuk wajib diisi"
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
                title: 'Barang yang akan diterima tidak boleh kosong !',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        } else {
            if ($('.create-form').valid()) {

                if (listBarang.length == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: "Barang yang diterima tidak boleh kosong",
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    })

                } else {
                    Swal.fire({
                        icon: 'question',
                        title: 'Simpan Data ?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let id = $('#id').val();
                            let data = new FormData(document.querySelector(".create-form"));
                            data.append('listBarang', JSON.stringify(listBarang));
                            if (id) {
                                // UPDATE
                                $.ajax({
                                    url: "<?= base_url("penerimaan-mutasi/update-global"); ?>",
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
                                                    window.location.href = "<?= base_url("penerimaan-mutasi/global") ?>";
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
                                    url: "<?= base_url("penerimaan-mutasi/save-global"); ?>",
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
                                                    window.location.href = "<?= base_url("penerimaan-mutasi/global") ?>";
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

    $('.btn-submit-detail').click(function() {

        if ($('.create-form-barang-masuk').valid()) {
            var id = $('#mutasi_global_detail_id').val();
            var index = 0;
            var qtyBarangDiterima = parseFloat($('#qty_diterima_current').val());
            var kodeSatuanDiterima = $('#stock_mutasi_id option:selected').data('kode_satuan');
            $.each(listBarang, function(i, v) {
                if (v.mutasi_global_detail_id == id) {
                    index = i;
                }
            });

            $('#qty_diterima_current').val(listBarang[index].qty_diterima_current);

            if (parseFloat(listBarang[index].qty_sisa) < qtyBarangDiterima) {
                Swal.fire({
                    icon: 'error',
                    title: 'Qty diterima harus kurang dari ' + listBarang[index].qty_sisa + ' ' + kodeSatuanDiterima,
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            } else {
                listBarang[index].stock_mutasi_id = $('#stock_mutasi_id option:selected').val();
                listBarang[index].kode_barang_diterima = $('#stock_mutasi_id option:selected').data('kode_barang');
                listBarang[index].barang_diterima = $('#stock_mutasi_id option:selected').data('barang');
                listBarang[index].qty_diterima_current = qtyBarangDiterima
                listBarang[index].satuan_diterima = kodeSatuanDiterima;

                $('#update_barang_masuk').modal('hide');
                drawTable(listBarang);
            }

        }

    });

    // GET LIST MUTASI GLOBAL NOMOR
    function getListMutasiGlobal() {
        $.ajax({
            url: `<?= base_url('penerimaan-mutasi/list-mutasi-global'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                company_pengirim_id: $(".company_pengirim_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".multiple_mutasi_id").empty()
                $(".multiple_mutasi_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".multiple_mutasi_id").append(`<option value="${item.id}">${item.no_mutasi}</option>`)
                })
                $(".multiple_mutasi_id").val();
            }
        });
    }

    // DRAWTABLE RESULT
    function drawTable(listBarang) {
        const table = $('#dataTable');
        table.find('tbody').empty();
        table.find('tfoot').empty();

        if (listBarang.length == 0) {
            var newRow = $('<tr>');
            newRow.append($('<td colspan="15" style="text-align:center">Tidak Ada Barang</td>'));
            table.find('tfoot').append(newRow);
        } else {
            var no = 1;
            $.each(listBarang, function(i, v) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td>').text(no++));
                newRow.append($('<td>').text(v.tipe_barang_text));
                newRow.append($('<td>').text(v.kode_barang));
                newRow.append($('<td>').text(v.barang));
                newRow.append($('<td>').text(v.bc_mutasi_name + ' / ' + v.no_aju_mutasi));
                newRow.append($('<td>').text(v.bc_asal_name + ' / ' + v.no_aju_asal));
                newRow.append($('<td>').text(v.divisi_asal_name + ' / ' + v.warehouse_asal_name));
                newRow.append($('<td>').text(v.supplier_name));
                newRow.append($('<td>').text(v.no_po));
                newRow.append($('<td>').text(v.qty));
                newRow.append($('<td>').text(v.qty_diterima_all));
                newRow.append($('<td>').text(v.qty_sisa));
                newRow.append($('<td>').text(v.satuan));
                newRow.append($('<td>').text(v.kode_barang_diterima));
                newRow.append($('<td>').text(v.barang_diterima));
                newRow.append($('<td>').text(v.qty_diterima_current));
                newRow.append($('<td>').text(v.satuan_diterima));
                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <button type="button" class="btn btn-primary" onclick="displayDetailModal(${v.mutasi_global_detail_id})" data-toggle="tooltip"><i class="fas fa-pencil-alt"></i></button>
                `
                ));

                table.find('tbody').append(newRow);
            });
        }
    }

    function displayDetailModal(id) {
        // RESET VALIDATOR
        validatorBarangMasuk.resetForm();
        validatorBarangMasuk.reset();

        var barangFirst = null;
        $.each(listBarang, function(i, v) {
            if (v.mutasi_global_detail_id == id) {
                barangFirst = v;
            }
        });

        var companyAsalName = $('#company_pengirim_id option:selected').text();
        var divisiAsalName = barangFirst.divisi_asal_name;
        var warehouseAsalName = barangFirst.warehouse_asal_name;
        var barangDikirim = barangFirst.kode_barang + ' / ' + barangFirst.barang;
        var dokumenMutasi = barangFirst.bc_mutasi_name + ' / ' + barangFirst.no_aju_mutasi;
        var dokumenAsal = barangFirst.bc_asal_name + ' / ' + barangFirst.no_aju_asal;
        var divisiPenerima = $('#divisi_penerima_id option:selected').text();
        var warehousePenerima = $('#warehouse_penerima_id option:selected').text();
        var tipeBarangText = barangFirst.tipe_barang_text;
        var tipeBarang = barangFirst.tipe_barang;
        var qtyDiterimaCurrent = barangFirst.qty_diterima_current;

        $('#company_asal_name').val(companyAsalName.trim());
        $('#divisi_asal_name').val(divisiAsalName);
        $('#warehouse_asal_name').val(warehouseAsalName);
        $('#barang_keluar_name').val(barangDikirim);
        $('#dokumen_mutasi_name').val(dokumenMutasi);
        $('#dokumen_asal_name').val(dokumenAsal);
        $('#divisi_penerima_name').val(divisiPenerima.trim());
        $('#warehouse_penerima_name').val(warehousePenerima.trim());
        $('#tipe_barang_name').val(tipeBarangText);
        $('#qty_diterima_current').val(qtyDiterimaCurrent);
        // APPEND TO HIDDEN ELEMENT
        $('#mutasi_global_detail_id').val(id);
        $('#tipe_barang').val(tipeBarang);
        // AJAX DROPDOWN BARANG
        $.ajax({
            url: `<?= base_url('penerimaan-mutasi/list-barang-masuk'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                stock_out_id: barangFirst.stock_out_id,
                tipe_barang: tipeBarang,
                divisi_id: $(".divisi_penerima_id option:selected").val(),
                warehouse_id: $(".warehouse_penerima_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $('#update_detail_barang').modal('show')

                $("#stock_mutasi_id").empty()
                $("#stock_mutasi_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $("#stock_mutasi_id").append(`<option data-stock_mutasi_id="${item.stock_id}" data-kode_barang="${item.kode_barang}" data-barang="${item.barang}" data-kode_satuan="${item.kode_satuan}" value="${item.stock_id}">(${item.kode_barang}) ${item.barang}</option>`)
                })
                $("#stock_mutasi_id").val(barangFirst.stock_mutasi_id).change();

                $('#update_barang_masuk').modal('show');
            }
        });

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

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $(".penerimaan_mutasi_no").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("penerimaan-mutasi/get-penerimaan-mutasi-global-no"); ?>`,
                method: "GET",
                data: {
                    divisi_penerima_id: $('#divisi_penerima_id option:selected').val()
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".penerimaan_mutasi_no").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".penerimaan_mutasi_no").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".penerimaan_mutasi_no").val("");
                    }
                }
            })
        } else {
            $(".penerimaan_mutasi_no").attr("readonly", false);
            $(".penerimaan_mutasi_no").val("");
        }
    }

    <?php if (!empty($penerimaanMutasiGlobal)) : ?>
        let arr = $('.multiple_mutasi_id').val();
        $.ajax({
            url: `<?= base_url("penerimaan-mutasi/list-barang-global"); ?>`,
            method: "GET",
            data: {
                mutasi_global_id: JSON.stringify(arr),
                penerimaan_mutasi_global_id: $('.id').val()
            },
            dataType: "json",
            success: function(res) {
                listBarang = [];
                listBarang = res.data;
                drawTable(listBarang);
            }
        })
    <?php endif; ?>
    const print = function(url) {
        window.open(url, "_blank");
    }

    // GET LIST WAREHOUSE PENERIMA
    function getListWarehousePenerima() {
        $.ajax({
            url: `<?= base_url('mutasi/warehouse'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $(".divisi_penerima_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_penerima_id").empty()
                $(".warehouse_penerima_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_penerima_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
            }
        });
    }

    const posting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Penerimaan Mutasi ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("penerimaan-mutasi/posting-global"); ?>",
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
                                window.location.href = "<?= base_url("penerimaan-mutasi/global") ?>";
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
            title: 'Hapus Penerimaan Mutasi ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("penerimaan-mutasi/delete-global"); ?>",
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
                                window.location.href = "<?= base_url("penerimaan-mutasi/global") ?>";
                            });
                        }
                    },
                });
            }
        })
    }
</script>


<?= $this->endSection(); ?>