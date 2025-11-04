<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($penerimaanMutasiGlobal) ? "Tambah Penerimaan Mutasi BC 2.7" : "Update Penerimaan Mutasi BC 2.7" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("penerimaan-mutasi/global"); ?>">
                Kembali
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
                                    <input readonly autocomplete="one-time-code" <?= !empty($penerimaanMutasiGlobal) ? ($penerimaanMutasiGlobal['status_posting'] == "1" ? "disabled" : 'disabled') : ''; ?> value="<?= !empty($penerimaanMutasiGlobal) ? $penerimaanMutasiGlobal['penerimaan_mutasi_no'] : "" ?>" type="text" class="form-control penerimaan_mutasi_no" id="penerimaan_mutasi_no" name="penerimaan_mutasi_no" placeholder="No. Penerimaan Mutasi">
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
                                    <th>No</th>
                                    <th>Kode Barang (Asal)</th>
                                    <th>Barang - Spesifikasi (Asal)</th>
                                    <th>Dokumen Mutasi</th>
                                    <th>Dept Pengirim</th>
                                    <th>Warehouse Pengirim</th>
                                    <th>Qty Mutasi</th>
                                    <th>Satuan Mutasi</th>
                                    <th>Kode Barang (Diterima)</th>
                                    <th>Barang - Spesifikasi (Diterima)</th>
                                    <th>Qty (Diterima)</th>
                                    <th>Satuan (Diterima)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="body-table">
                            </tbody>
                            <tfoot class="foot-detail-table" id="foot-detail-table">
                                <tr>
                                    <td colspan="15">
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
                            <div class="form-floating" style="height: 50px;">
                                <select class="form-select spesifikasi_hasil_id" name="spesifikasi_hasil_id" id="spesifikasi_hasil_id">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Pilih Barang Masuk</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating" style="height: 50px;">
                                <select disabled class="form-select unit_hasil_id" name="unit_hasil_id" id="unit_hasil_id">
                                    <option value=""></option>
                                    <?php foreach ($satuan as $s): ?>
                                        <option value="<?= $s['id'] ?>">
                                            <?= $s['kode_satuan'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Pilih Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" type="text" class="form-control qty_diterima_current" id="qty_diterima_current" name="qty_diterima_current" placeholder="Qty Diterima Sekarang">
                                <label for="floatingInput">Qty Barang Masuk</label>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-barang-masuk mr-2">Kembali</button>
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
    });

    $('#tanggal').change(function(e) {
        e.preventDefault();
        changeStatus();
    });

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

    $('#spesifikasi_hasil_id').select2({
        placeholder: "Pilih Barang Masuk",
        theme: "bootstrap-5",
        allowClear: false,
        dropdownParent: $('#update_barang_masuk'),
        ajax: {
            url: '<?= base_url('penerimaan-mutasi/list-barang-masuk'); ?>',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    company_tujuan_id: "<?= session()->get('login')->this_company_id ?>"
                };
            },
            processResults: function(data) {
                // Pastikan server mengembalikan data dengan struktur yang lengkap
                return {
                    results: $.map(data.data, function(item) {
                        return {
                            id: item.id,
                            text: item.text,
                            satuan_1: item.satuan_1,
                            kode_barang: item.kode_barang,
                            barang_name: item.barang_name,
                            spesifikasi: item.spesifikasi
                        };
                    })
                };
            },
            cache: false
        },
        minimumInputLength: 1
    });

    $('#unit_hasil_id').select2({
        placeholder: "Pilih Satuan Masuk",
        theme: "bootstrap-5",
        allowClear: false,
        dropdownParent: $('#update_barang_masuk')
    });

    $('#spesifikasi_hasil_id').on('select2:select', function(e) {
        e.preventDefault();
        var data = e.params.data;
        var selectedOption = $(this).find('option:selected');
        selectedOption.data('satuan_1', data.satuan_1);
        selectedOption.data('kode_barang', data.kode_barang);
        selectedOption.data('barang_name', data.barang_name);
        selectedOption.data('spesifikasi', data.spesifikasi);

        // Trigger change event manual
        $(this).trigger('change');
    });

    $('#spesifikasi_hasil_id').change(function(e) {
        e.preventDefault();
        var satuan_id = $('#spesifikasi_hasil_id option:selected').data('satuan_1');
        $('#unit_hasil_id').val(satuan_id).change();
    });


    $('.btn-discard-barang-masuk').click(function() {
        $('#update_barang_masuk').modal('hide');
    });

    $("#company_pengirim_id,.multiple_mutasi_id,#divisi_penerima_id,#warehouse_penerima_id,#spesifikasi_hasil_id,#unit_hasil_id")
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
            spesifikasi_hasil_id: {
                required: true
            },
            unit_hasil_id: {
                required: true
            },
            qty_diterima_current: {
                required: true
            },
        },
        messages: {
            spesifikasi_hasil_id: {
                required: "Barang masuk wajib diisi"
            },
            unit_hasil_id: {
                required: "Satuan wajib diisi"
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

                    var error = false;
                    var namaBarang = '';
                    for (let i = 0; i < listBarang.length; i++) {
                        if (listBarang[i].penerimaan.spesifikasi_hasil_id == '' || listBarang[i].penerimaan.spesifikasi_hasil_id == null) {
                            namaBarang = listBarang[i].barang_name + " " + listBarang[i].spesifikasi;
                            error = true;
                        }
                    }

                    if (error == true) {
                        Swal.fire({
                            icon: 'error',
                            title: "Barang " + namaBarang + ", Belum Dibuatkan Hasil Mutasi Output Barang Di company Tujuan",
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
                            cancelButtonText: 'Kembali',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                let id = $('#id').val();
                                let url = id == '' ? '<?= base_url("penerimaan-mutasi/save-global"); ?>' : '<?= base_url("penerimaan-mutasi/update-global"); ?>';
                                let data = new FormData(document.querySelector(".create-form"));
                                data.append('listBarang', JSON.stringify(listBarang));

                                $.ajax({
                                    url: url,
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
                        });
                    }


                }
            }
        }
    });

    $('.btn-submit-detail').click(function() {
        if ($('.create-form-barang-masuk').valid()) {
            var id = $('#mutasi_global_detail_id').val();
            var index = 0;
            var qtyBarangDiterima = parseFloat($('#qty_diterima_current').val());
            $.each(listBarang, function(i, v) {
                if (v.mutasi_global_detail_id == id) {
                    index = i;
                }
            });

            listBarang[index].penerimaan.kode_barang = $('#spesifikasi_hasil_id option:selected').data('kode_barang');
            listBarang[index].penerimaan.barang_name = $('#spesifikasi_hasil_id option:selected').data('barang_name');
            listBarang[index].penerimaan.spesifikasi = $('#spesifikasi_hasil_id option:selected').data('spesifikasi');
            listBarang[index].penerimaan.unit_hasil_id = $('#spesifikasi_hasil_id option:selected').data('satuan_1');
            listBarang[index].penerimaan.kode_satuan = $('#unit_hasil_id option:selected').text();
            listBarang[index].penerimaan.spesifikasi_hasil_id = $('#spesifikasi_hasil_id option:selected').val();
            listBarang[index].penerimaan.qty = qtyBarangDiterima;
            drawTable(listBarang);
            $('#update_barang_masuk').modal('hide');
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
            newRow.append($('<td colspan="14" style="text-align:center">Tidak Ada Barang</td>'));
            table.find('tfoot').append(newRow);
        } else {
            var no = 1;
            $.each(listBarang, function(i, v) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td>').text(no++));
                newRow.append($('<td>').text(v.kode_barang));
                newRow.append($('<td>').text(v.barang_name + " - " + v.spesifikasi));
                newRow.append($('<td>').text(v.no_aju == null ? "" : v.no_aju + ' / ' + v.no_daftar));
                newRow.append($('<td>').text(v.divisi_asal));
                newRow.append($('<td>').text(v.warehouse_asal));
                newRow.append($('<td>').text(greatFormatRupiah(v.qty_mutasi)));
                newRow.append($('<td>').text(v.satuan_mutasi));
                newRow.append($('<td>').text(v.penerimaan.kode_barang == null ? "" : v.penerimaan.kode_barang));
                newRow.append($('<td>').text(v.penerimaan.barang_name == null ? "" : v.penerimaan.barang_name + " - " + v.penerimaan.spesifikasi));
                newRow.append($('<td>').text(v.penerimaan.qty == null ? "" : greatFormatRupiah(v.penerimaan.qty)));
                newRow.append($('<td>').text(v.penerimaan.kode_satuan == null ? "" : v.penerimaan.kode_satuan));

                newRow.append($('<td >').html(
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

        var divisiPenerima = $('#divisi_penerima_id option:selected').text();
        var warehousePenerima = $('#warehouse_penerima_id option:selected').text();

        if (divisiPenerima == '' || warehousePenerima == '') {
            Swal.fire({
                icon: 'error',
                title: "Departemen & Warehouse Penerimaan Barang Mutasi Wajib Diisi",
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
            return;
        } else {
            var barangFirst = null;
            $.each(listBarang, function(i, v) {
                if (v.mutasi_global_detail_id == id) {
                    barangFirst = v;
                }
            });

            var companyAsalName = $('#company_pengirim_id option:selected').text();
            var divisiAsalName = barangFirst.divisi_asal;
            var warehouseAsalName = barangFirst.warehouse_asal;
            var barangDikirim = barangFirst.kode_barang + ' / ' + barangFirst.barang_name + ' - ' + barangFirst.spesifikasi;
            var dokumenMutasi = barangFirst.no_aju + ' / ' + barangFirst.no_daftar;
            var qtyDiterimaCurrent = barangFirst.penerimaan.qty;
            var unitHasilId = barangFirst.penerimaan.unit_hasil_id;

            $('#company_asal_name').val(companyAsalName.trim());
            $('#divisi_asal_name').val(divisiAsalName);
            $('#warehouse_asal_name').val(warehouseAsalName);
            $('#barang_keluar_name').val(barangDikirim);
            $('#dokumen_mutasi_name').val(dokumenMutasi);
            $('#divisi_penerima_name').val(divisiPenerima.trim());
            $('#warehouse_penerima_name').val(warehousePenerima.trim());
            $('#qty_diterima_current').val(qtyDiterimaCurrent);
            $('#mutasi_global_detail_id').val(id);
            $('#unit_hasil_id').val(unitHasilId).change();
            // Reset dan isi spesifikasi
            const $spesifikasi = $("#spesifikasi_hasil_id");
            $spesifikasi.empty()
                .append('<option value=""></option>')
                .append(`
                <option 
                    data-satuan_1="${barangFirst.penerimaan.unit_hasil_id}"
                    data-kode_barang="${barangFirst.penerimaan.kode_barang}" 
                    data-barang_name="${barangFirst.penerimaan.barang_name}" 
                    data-spesifikasi="${barangFirst.penerimaan.spesifikasi}" 
                    selected 
                    value="${barangFirst.penerimaan.spesifikasi_hasil_id}">
                    (${barangFirst.penerimaan.kode_barang}) ${barangFirst.penerimaan.barang_name} - ${barangFirst.penerimaan.spesifikasi}
                </option>
            `)
                .val(barangFirst.penerimaan.spesifikasi_hasil_id)
                .trigger('change');
            $('#spesifikasi_hasil_id').val(barangFirst.penerimaan.spesifikasi_hasil_id).change();
            $('#update_barang_masuk').modal('show');
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
                    tanggal: $('#tanggal').val()
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
                penerimaan_mutasi_global_id: $('.id').val(),
                is_edit: true
            },
            dataType: "json",
            success: function(res) {
                listBarang = [];
                listBarang = res.data;
                drawTable(listBarang);
            }
        })
    <?php else: ?>
        changeStatus();
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
            cancelButtonText: 'Kembali',
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
            cancelButtonText: 'Kembali',
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