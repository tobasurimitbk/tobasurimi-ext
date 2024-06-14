<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<section class="section section-form">

    <div class="section-header">
        <h1 class="title-name"><?= !empty($ppbkb) ? "Update Dokumen PPBKB" : "Tambah Dokumen PPBKB" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right root-form-view" href="<?= base_url("bea-cukai-ppbkb"); ?>">
                Batal
            </a>
            <?php if (!empty($ppbkb)) : ?>
                <?php if ($ppbkb['status_posting'] == "0") : ?>
                    <?php if (can('Bea Cukai', 'PPBKB', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="deleteAction()">
                            Hapus
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (can('Bea Cukai', 'PPBKB', 'p')) : ?>
                    <button class="btn btn-warning btn-print float-right" onclick="printAction()">
                        Print
                    </button>
                <?php endif; ?>
                <?php if ($ppbkb['status_posting'] == "0") : ?>
                    <?php if (can('Bea Cukai', 'PPBKB', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right" onclick="postingAction()">
                            Posting
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($ppbkb['status_posting'] == "0") : ?>
                    <?php if (can('Bea Cukai', 'PPBKB', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else : ?>
                <?php if (can('Bea Cukai', 'PPBKB', 'c')) : ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <form class="create-form">
        <div class="card">
            <div class="card-header" style="font-weight: bold; color:black;">
                DATA MUTASI PPBKB
            </div>
            <div class="card-body">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="id" class="id" value="<?= !empty($ppbkb) ? encrypt($ppbkb['id']) : '' ?>">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-floating mb-3 mt-1" style="height: 50px;">
                            <select <?= !empty($ppbkb) ? 'disabled' : '' ?> class="form-select divisi_asal_id" id="divisi_asal_id" name="divisi_asal_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= !empty($ppbkb) ? ($ppbkb['divisi_asal_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>"><?= strtoupper($d['divisi']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            </select>
                            <label style="z-index: 1;">Pilih Departemen Asal</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3 mt-1" style="height: 50px;">
                            <select <?= !empty($ppbkb) ? 'disabled' : '' ?> class="form-select mutasi_id" id="mutasi_id" name="mutasi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($ppbkb)) : ?>
                                    <option selected value="<?= $ppbkb['mutasi_id'] ?>">
                                        <?= $ppbkb['no_mutasi'] ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                            <label style="z-index: 1;">Pilih Nomor Mutasi</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled placeholder="Warehouse Asal" value="<?= !empty($ppbkb) ? $ppbkb['warehouse_asal_name'] : '' ?>" class="form-control warehouse_asal_name" id="warehouse_asal_name" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Warehouse Asal</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled placeholder="Departemen Tujuan" value="<?= !empty($ppbkb) ? ($divisiTujuan != null ? $divisiTujuan['divisi'] : '') : '' ?>" class="form-control divisi_tujuan_name" id="divisi_tujuan_name" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Departemen Tujuan</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled placeholder="Warehouse Tujuan" value="<?= !empty($ppbkb) ? ($warehouseTujuan != null ? $warehouseTujuan['warehouse_name'] : '') : '' ?>" class="form-control warehouse_tujuan_name" id="warehouse_tujuan_name" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Warehouse Tujuan</label>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-header" style="font-weight: bold; color:black;margin-top:-20px;">
                HEADER DOKUMEN PPBKB
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" <?= !empty($ppbkb) ? 'disabled=true' : ''; ?> value="<?= !empty($ppbkb) ? $ppbkb['no_ppbkb'] : $noPPBKB; ?>" type="text" class="form-control no_ppbkb" id="no_ppbkb" name="no_ppbkb" placeholder="No. PPBKB">
                                    <label for="floatingInput">Nomor PPBKB</label>
                                </div>
                                <div style="<?= !empty($ppbkb) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 22px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($ppbkb) ? ($ppbkb['status_posting'] == "1" ? "disabled" : "") : '' ?> autocomplete="one-time-code" class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime(!empty($ppbkb) ? $ppbkb['tanggal'] : date('Y-m-d'))); ?>">
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($ppbkb) ? ($ppbkb['status_posting'] == "1" ? "disabled" : "") : '' ?> class="form-select npwp" id="npwp" name="npwp" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($pengusahaTPB as $p) : ?>
                                    <option <?= !empty($ppbkb) ? ($ppbkb['npwp'] == $p['npwp'] ? 'selected' : '') : '' ?> data-id="<?= $p['id'] ?>" data-lokasi_asal_barang="<?= $p['alamat'] ?>" data-nama_perusahaan="<?= $p['nama_pengusaha'] ?>" value="<?= $p['npwp'] ?>">
                                        <?= $p['npwp'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            </select>
                            <label style="z-index: 1;">Pilih NPWP</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly placeholder="Nama Perusahaan" value="<?= !empty($ppbkb) ? $ppbkb['nama_perusahaan'] : '' ?>" class="form-control nama_perusahaan" id="nama_perusahaan" name="nama_perusahaan" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Nama Perusahaan</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($ppbkb) ? ($ppbkb['status_posting'] == "1" ? "disabled" : "") : '' ?> class="form-select no_ijin_tpb" id="no_ijin_tpb" name="no_ijin_tpb" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($ppbkb)) : ?>
                                    <option selected value="<?= $ppbkb['no_ijin_tpb'] ?>">
                                        <?= $ppbkb['no_ijin_tpb'] ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                            </select>
                            <label style="z-index: 1;">Pilih No Ijin TPB</label>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-floating mb-3">
                            <textarea name="lokasi_asal_barang" id="lokasi_asal_barang" class="form-control lokasi_asal_barang" style="height: 100px;"><?= !empty($ppbkb) ? $ppbkb['lokasi_asal_barang'] : '' ?></textarea>
                            <label>Asal Barang</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3">
                            <textarea name="lokasi_tujuan_barang" id="lokasi_tujuan_barang" class="form-control lokasi_tujuan_barang" style="height: 100px;"><?= !empty($ppbkb) ? $ppbkb['lokasi_tujuan_barang'] : '' ?></textarea>
                            <label>Tujuan Pemindahan Barang</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-header" style="font-weight: bold; color:black;margin-top:-20px;">
                BARANG YANG DIPINDAHKAN
            </div>
            <div class="card-body">
                <div class="row mt-2">
                    <div class="col-md-12 col-table-button-tts">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" border="1" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center;">No</th>
                                        <th style="text-align: center;">Tipe Barang</th>
                                        <th style="text-align: center;">Kode Barang</th>
                                        <th style="text-align: center;">Kode HS</th>
                                        <th style="text-align: center;">Barang</th>
                                        <th style="text-align: center;">Qty Mutasi</th>
                                        <th style="text-align: center;">Satuan</th>
                                        <th style="text-align: center;">Dokumen Pemasukan</th>
                                        <th style="text-align: center;">No Aju</th>
                                        <th style="text-align: center;">Tanggal Masuk</th>
                                        <th style="text-align: center;">Action</th>

                                    </tr>
                                </thead>
                                <tbody class="body-table">
                                </tbody>
                                <tfoot class="foot-detail-table" id="foot-detail-table">
                                    <tr>
                                        <td colspan="11" style="text-align: center;">
                                            Tidak Ada Barang
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-header" style="font-weight: bold; color:black;margin-top:-20px;">
                PENANGGUNG JAWAB
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input value="<?= !empty($ppbkb) ? $ppbkb['tempat'] : $akunCeisa['tempat'] ?>" id="tempat" name="tempat" type="text" class="form-control tempat" placeholder="">
                                <label>Tempat</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input value="<?= !empty($ppbkb) ? $ppbkb['nama'] :  $akunCeisa['nama'] ?>" id="nama" name="nama" type="text" class="form-control nama" placeholder="">
                                <label>Nama</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input value="<?= !empty($ppbkb) ? $ppbkb['jabatan'] :  $akunCeisa['jabatan'] ?>" id="jabatan" name="jabatan" type="text" class="form-control jabatan" placeholder="">
                                <label>Jabatan</label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</section>

<div class="modal add-modal" id="update_hs_code_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update HS Code Barang</h5>
            </div>
            <div class="modal-body">
                <form class="form-update-hs-code" role="form" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="stock_detail2_id" id="stock_detail2_id" class="stock_detail2_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control kode_barang_name_modal" id="kode_barang_name_modal" name="kode_barang_name_modal" placeholder="Kode Barang">
                                <label for="floatingInput">Kode Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control barang_name_modal" id="barang_name_modal" name="barang_name_modal" placeholder="Nama Barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select <?= !empty($ppbkb) ? ($ppbkb['status_posting'] == "1" ? "disabled" : "") : '' ?> class="form-select hs_code_id" id="hs_code_id" name="hs_code_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($hsCode as $h) : ?>
                                        <option value="<?= $h['id'] ?>">
                                            <?= $h['code'] . " - " . strtoupper($h['uraian_barang']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                </select>
                                <label style="z-index: 1;">Pilih HS Code</label>
                            </div>
                        </div>

                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-modal-hs-code mr-2">Batal</button>
                <?php if (!empty($ppbkb)) : ?>
                    <?php if ($ppbkb['status_posting'] != '1') : ?>
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

    var listData = [];

    // INIT PAS UPDATE
    <?php if (!empty($ppbkb)) : ?>
        $.ajax({
            url: `<?= base_url('bea-cukai-ppbkb/list-barang-mutasi'); ?>`,
            method: "GET",
            data: {
                mutasi_id: $("#mutasi_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                listData = [];
                listData = res.data;
                drawTable(listData);
            }
        });
    <?php endif; ?>

    $(".tanggal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#divisi_asal_id').select2({
        placeholder: "Pilih Departemen Asal",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        getListMutasi();
    });

    $('#mutasi_id').select2({
        placeholder: "Pilih Nomor Mutasi",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        var warehouseAsalName = $('#mutasi_id option:selected').data('warehouse_asal_name');
        var divisiTujuanName = $('#mutasi_id option:selected').data('divisi_tujuan_name');
        var warehouseTujuanName = $('#mutasi_id option:selected').data('warehouse_tujuan_name');

        $('#warehouse_asal_name').val(warehouseAsalName);
        $('#divisi_tujuan_name').val(divisiTujuanName);
        $('#warehouse_tujuan_name').val(warehouseTujuanName);

        // LIST BARANG
        getListMutasiDetail();
    });

    $('#npwp').select2({
        placeholder: "Pilih NPWP Pemilik Barang",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // LIS NO IJIN TPB
        getListNoIjinTPB();
        // APPEND NAMA PERUSAHAAN & LOKASI ASAL BARANG
        var selected = $('#npwp option:selected');
        $('#nama_perusahaan').val(selected.data('nama_perusahaan'));
        $('#lokasi_asal_barang').val(selected.data('lokasi_asal_barang'));
    });

    $('#no_ijin_tpb').select2({
        placeholder: "Pilih Nomor Ijin TPB",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // APPEND LOKASI TUJUAN BARANG
        var selected = $('#no_ijin_tpb option:selected');
        $('#lokasi_tujuan_barang').val(selected.data('lokasi_tujuan_barang'));
    });

    $('#hs_code_id').select2({
        placeholder: "Pilih HS Code Barang",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#update_hs_code_modal')
    });


    $('.btn-discard-modal-hs-code').click(function() {
        $('#update_hs_code_modal').modal('hide');
    })

    $("#divisi_asal_id,#mutasi_id,#npwp,#no_ijin_tpb, #hs_code_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');


    var validator = $(".create-form").validate({
        rules: {
            divisi_asal_id: {
                required: true
            },
            mutasi_id: {
                required: true
            },
            no_ppbkb: {
                required: true
            },
            tanggal: {
                required: true
            },
            npwp: {
                required: true
            },
            no_ijin_tpb: {
                required: true
            },
            lokasi_asal_barang: {
                required: true
            },
            lokasi_tujuan_barang: {
                required: true
            },
            tempat: {
                required: true
            },
            nama: {
                required: true
            },
            jabatan: {
                required: true
            },
        },
        messages: {
            divisi_asal_id: {
                required: "Pilih Departemen Asal"
            },
            mutasi_id: {
                required: "Pilih Nomor Mutasi"
            },
            no_ppbkb: {
                required: "No PPBKB Wajib Diisi"
            },
            tanggal: {
                required: "Tanggal Wajib Diisi"
            },
            npwp: {
                required: "Pilih NPWP Pengusaha TPB"
            },
            no_ijin_tpb: {
                required: "Pilih Nomor Ijin TPB"
            },
            lokasi_asal_barang: {
                required: "Lokasi Asal Barang Wajib Diisi"
            },
            lokasi_tujuan_barang: {
                required: "Lokasi Tujuan Barang Wajib Diisi"
            },
            tempat: {
                required: "Tempat Wajib Diisi"
            },
            nama: {
                required: "Nama Wajib Diisi"
            },
            jabatan: {
                required: "Jabatan Wajib Diisi"
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

    var validatorDetail = $(".form-update-hs-code").validate({
        rules: {
            hs_code_id: {
                required: true
            },
        },
        messages: {
            hs_code_id: {
                required: "Kode HS wajib diisi"
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

    $('.btn-submit-parent').click(function(e) {
        e.preventDefault();
        if (listData.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Barang yang akan dikirim tidak boleh kosong !',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        } else {
            if ($('.create-form').valid()) {


                var isValidHSCode = true;
                var firstError = null;

                $.each(listData, function(i, v) {
                    if (v.hs_code_id == null) {
                        isValidHSCode = false;
                        firstError = v;
                    }
                })

                if (!isValidHSCode) {
                    // GAK VALID
                    Swal.fire({
                        icon: 'error',
                        title: 'Barang ' + firstError.barang + ', HS Code nya belum ada !',
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                } else {
                    // IS VALID
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
                        var id = $('#id').val();
                        var data = new FormData(document.querySelector(".create-form"));
                        data.append('listData', JSON.stringify(listData));

                        if (id) {
                            // UPDATE
                            $.ajax({
                                url: "<?= base_url("bea-cukai-ppbkb/update"); ?>",
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
                                                window.location.href = "<?= base_url("bea-cukai-ppbkb") ?>";
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                            cancelButtonColor: '#d33',
                                            reverseButtons: true,
                                            confirmButtonText: 'Oke',
                                        })
                                    }

                                },
                            });
                        } else {
                            // INSERT
                            $.ajax({
                                url: "<?= base_url("bea-cukai-ppbkb/save"); ?>",
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
                                                window.location.href = "<?= base_url("bea-cukai-ppbkb") ?>";
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                            cancelButtonColor: '#d33',
                                            reverseButtons: true,
                                            confirmButtonText: 'Oke',
                                        })
                                    }
                                },
                            });
                        }
                    })
                }

            }
        }
    })

    $('.btn-submit-detail').click(function(e) {
        e.preventDefault();
        if ($('.form-update-hs-code').valid()) {
            var id = $('#stock_detail2_id').val();
            var hsCodeId = $('#hs_code_id option:selected').val();
            var hsCodeName = $('#hs_code_id option:selected').text();
            var index = null;

            $.each(listData, function(i, v) {
                if (v.id == id) {
                    index = i;
                }
            });

            listData[index].hs_code_id = hsCodeId;
            listData[index].hs_code = hsCodeName;

            drawTable(listData);
            $('#update_hs_code_modal').modal('hide');
        }
    })

    function getListMutasiDetail() {
        $.ajax({
            url: `<?= base_url('bea-cukai-ppbkb/list-barang-mutasi'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                mutasi_id: $("#mutasi_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                listData = [];
                listData = res.data;
                drawTable(listData);
            }
        });
    }

    function drawTable(listData) {
        var no = 1;
        const table = $('#dataTable');
        table.find('tbody').empty();
        table.find('tfoot').empty();

        if (listData.length === 0) {
            var newRow = $('<tr>');
            newRow.append($('<td colspan="11" style="text-align:center">Tidak Ada Barang</td>'));
            table.find('tfoot').append(newRow);
        } else {
            $.each(listData, function(i, v) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align: center;">').html(
                    `
                            ${no++} 
                        `
                ));
                newRow.append($('<td style="text-align: center;">').text(v.type_barang_text));
                newRow.append($('<td style="text-align: center;">').text(v.kode_barang));
                newRow.append($('<td style="text-align: center;">').text(v.hs_code == null ? "-" : v.hs_code));
                newRow.append($('<td style="text-align: center;">').text(v.barang));
                newRow.append($('<td style="text-align: center;">').text(v.qty));
                newRow.append($('<td style="text-align: center;">').text(v.satuan));
                newRow.append($('<td style="text-align: center;">').text(v.bc_type));
                newRow.append($('<td style="text-align: center;">').text(v.no_aju));
                newRow.append($('<td style="text-align: center;">').text(v.stock_date));
                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <button <?= !empty($ppbkb) ? (($ppbkb['status_posting'] == "1") ? 'disabled' : '') : '' ?> type="button" class="btn btn-primary" onclick="updateHsCodeModal(${v.id})" ><i class="fas fa-pencil-alt"></i></button>
                `
                ));
                table.find('tbody').append(newRow);
            });
        }
    }

    function updateHsCodeModal(id) {
        var first = null;

        $.each(listData, function(i, v) {
            if (v.id == id) {
                first = v;
            }
        });

        $('#stock_detail2_id').val(first.id);
        $('#kode_barang_name_modal').val(first.kode_barang);
        $('#barang_name_modal').val(first.barang);
        $('#hs_code_id').val(first.hs_code_id).change();

        $('#update_hs_code_modal').modal('show');
    }

    function getListMutasi() {
        $.ajax({
            url: `<?= base_url('bea-cukai-ppbkb/list-mutasi'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_asal_id: $(".divisi_asal_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".mutasi_id").empty()
                $(".mutasi_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".mutasi_id").append(`<option 
                        data-warehouse_asal_name="${item.warehouse_asal_name}" 
                        data-divisi_tujuan_name="${item.divisi_tujuan_name}"
                        data-warehouse_tujuan_name="${item.warehouse_tujuan_name}" 
                        value="${item.id}">${item.no_mutasi}
                    </option>`)
                })
                $(".mutasi_id").val();
            }
        });
    }

    function getListNoIjinTPB() {
        $.ajax({
            url: `<?= base_url('bea-cukai-ppbkb/list-no-ijin-tpb'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                id: $(".npwp option:selected").data('id'),
            },
            dataType: "json",
            success: function(res) {
                $(".no_ijin_tpb").empty()
                $(".no_ijin_tpb").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".no_ijin_tpb").append(`<option 
                        data-lokasi_tujuan_barang="${item.alamat_pemilik_barang}"  
                        value="${item.no_ijin_tpb}">${item.no_ijin_tpb}
                    </option>`)
                })
                $(".no_ijin_tpb").val();
            }
        });
    }

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $(".no_ppbkb").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("bea-cukai-ppbkb/get-no"); ?>`,
                method: "GET",
                beforeSend: function() {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".no_ppbkb").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".no_ppbkb").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".no_ppbkb").val("");
                    }
                }
            })
        } else {
            $(".no_ppbkb").attr("readonly", false);
            $(".no_ppbkb").val("");
        }
    }

    function deleteAction() {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Dokumen PPBKB ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $('#id').val();
                var formData = new FormData();
                formData.append("id", id);
                $.ajax({
                    url: `<?= base_url("bea-cukai-ppbkb/delete"); ?>`,
                    method: "POST",
                    data: formData,
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            csrf.val(res.token);
                            Swal.fire({
                                icon: 'success',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.href = "<?= base_url('bea-cukai-ppbkb') ?>"
                                }
                            });
                        }
                    }
                })
            }
        })
    }

    function postingAction() {
        Swal.fire({
            icon: 'question',
            title: 'Posting Dokumen PPBKB ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $('#id').val();
                var formData = new FormData();
                formData.append("id", id);
                $.ajax({
                    url: `<?= base_url("bea-cukai-ppbkb/posting"); ?>`,
                    method: "POST",
                    data: formData,
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.href = "<?= base_url('bea-cukai-ppbkb') ?>"
                                }
                            });
                        }
                    }
                })
            }
        })
    }

    function printAction() {
        var id = $('#id').val();
        window.open("<?= base_url('bea-cukai-ppbkb/print/') ?>" + id, '_blank');
    }
</script>


<?= $this->endSection(); ?>