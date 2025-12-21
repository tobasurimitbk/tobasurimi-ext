<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>
<section class="section section-form">

    <div class="section-header">
        <h1 class="title-name"><?= !empty($bc27) ? "Update Dokumen BC 2.7 Out" : "Tambah Dokumen BC 2.7 Out" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right root-form-view" href="<?= base_url("bea-cukai-bc-27"); ?>">
                Kembali
            </a>
            <?php if (!empty($bc27)) : ?>
                <button disabled class="btn btn-info btn-print float-right text-white" href="<?= base_url('bea-cukai-bc-27/id/header/' . encrypt($bc27['id'])) ?>">
                    Form Ceisa (Tahap Pengembangan)
                </button>
                <?php if ($bc27['status_posting'] == "0") : ?>
                    <?php if (can('Bea Cukai', 'BC 2.7 Out', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="deleteAction()">
                            Hapus
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($bc27['status_posting'] == "0") : ?>
                    <?php if (can('Bea Cukai', 'BC 2.7 Out', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="postingAction()">
                            Posting
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($bc27['status_posting'] == "0") : ?>
                    <?php if (can('Bea Cukai', 'BC 2.7 Out', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else : ?>
                <?php if (can('Bea Cukai', 'BC 2.7 Out', 'c')) : ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="font-weight: bold;">
            DATA BARANG UNTUK PEMBUATAN DOKUMEN BEA CUKAI 2.7
        </div>
        <div class="card-body">
            <form class="create-form">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="id" class="id" value="<?= !empty($bc27) ? encrypt($bc27['id']) : '' ?>">

                <div class="row">
                    <div class="col-sm-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3">
                                <input <?= !empty($bc27) ? ($bc27['status_posting'] == "1" ? "disabled" : "") : '' ?> value="<?= !empty($bc27) ? date('d/m/Y', strtotime($bc27['createdAt'])) : date('d/m/Y') ?>" autocomplete="one-time-code" type="text" class="form-control tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dokumen">
                                <label for="floatingInput">Tanggal Dokumen</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 21px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <div class="form-floating mb-3">
                                <input value="<?= !empty($bc27) ? $bc27['no_aju'] : $noAju ?>" name="no_aju" readonly type="text" id="no_aju" class="form-control no_aju" placeholder="">
                                <label>Nomor Pengajuan</label>
                            </div>
                            <div class="input-group-append" style="height:50px;">
                                <button <?= !empty($bc27) ? ($bc27['status_posting'] == "1" ? "disabled" : "") : '' ?> class="btn btn-success btn-customer-add" id="btn-customer-add" data-toggle="modal" type="button" onclick="noAjuShowModal()">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3 mt-1" style="height: 50px;">
                            <select <?= !empty($bc27) ? ($bc27['status_posting'] == "1" ? "disabled" : "") : '' ?> class="form-select company_tujuan_id" id="company_tujuan_id" name="company_tujuan_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dropdownCompanyExcept as $d) : ?>
                                    <option <?= !empty($bc27) ? ($bc27['company_tujuan_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= strtoupper($d['company']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            </select>
                            <label style="z-index: 1;">Pilih Company Tujuan</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3 mt-1" style="height: 50px;">
                            <select <?= !empty($bc27) ? ($bc27['status_posting'] == "1" ? "disabled" : "") : '' ?> class="form-select mutasi_global_id" id="mutasi_global_id" name="mutasi_global_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($bc27)) : ?>
                                    <option selected value="<?= $bc27['mutasi_global_id'] ?>">
                                        <?= $bc27['no_mutasi'] ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                            <label style="z-index: 1;">Pilih Nomor Mutasi</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled placeholder="Company Asal" value="<?= $companyAsalName ?>" class="form-control" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Company Asal</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled placeholder="Company Asal" value="<?= !empty($bc27) ? $bc27['divisi'] : '' ?>" class="form-control divisi_asal_name" id="divisi_asal_name" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Departemen Asal</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled placeholder="Company Asal" value="<?= !empty($bc27) ? $bc27['warehouse_name'] : '' ?>" class="form-control warehouse_asal_name" id="warehouse_asal_name" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Warehouse Asal</label>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-floating mb-3">
                            <input <?= !empty($bc27) ? ($bc27['status_posting'] == "1" ? "disabled" : "") : '' ?> value="<?= !empty($bc27) ? $bc27['no_daftar'] : $noAju ?>" autocomplete="one-time-code" type="number" class="form-control no_daftar" id="no_daftar" name="no_daftar" placeholder="No Daftar">
                            <label for="floatingInput">Nomor Daftar</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold modal-sub-title" style="font-size: 14px;">Buatkan Penerimaan Barang Otomatis</label>
                        <div class="form-control border-0 custom-toggle-switch" style="margin-top: -15px;">
                            <div class="form-check form-switch form-switch-lg">
                                <input <?= !empty($bc27) ? ($bc27['penerimaan_otomatis'] == 1 ? 'checked' : '') : '' ?> class="form-check-input" value="1" type="checkbox" name="penerimaan_otomatis" id="penerimaan_otomatis">
                                <label class="form-check-label" for="penerimaan_otomatis"></label>
                            </div>
                        </div>
                        <small>
                            <i class="text-dark">
                                Ketika dichecklist, Barang yang akan dipindahkan akan secara otomatis diterima oleh company tujuan
                            </i>
                        </small>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3 form-penerimaan-mutasi" style="height: 50px;">
                            <select class="form-select divisi_tujuan_id" id="divisi_tujuan_id" name="divisi_tujuan_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (isset($divisi)): ?>
                                    <?php foreach ($divisi as $d) : ?>
                                        <option <?= !empty($bc27) ? ($bc27['divisi_tujuan_id'] == $d['id'] ? 'checked' : "") : '' ?> value="<?= $d["id"]; ?>" <?= !empty($bc27) ? ($bc27['divisi_tujuan_id'] === $d["id"] ? "selected" : "") : ""; ?>><?= $d["divisi"]; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen Tujuan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3 form-penerimaan-mutasi" style="height: 50px;">
                            <select class="form-select warehouse_tujuan_id" id="warehouse_tujuan_id" name="warehouse_tujuan_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (isset($warehouse)): ?>
                                    <?php foreach ($warehouse as $w) : ?>
                                        <option <?= !empty($bc27) ? ($bc27['warehouse_tujuan_id'] == $w['id'] ? 'checked' : "") : '' ?> value="<?= $w["id"]; ?>" <?= !empty($bc27) ? ($bc27['warehouse_tujuan_id'] === $w["id"] ? "selected" : "") : ""; ?>><?= $w["warehouse_name"]; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen Tujuan</label>
                        </div>
                    </div>
                </div>

            </form>

            <div class="row mt-2">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Daftar Barang Yang Akan Dipindahkan</label>
                </div>
                <div class="col-md-12 col-table-button-tts">


                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" border="1" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Kode Barang</th>
                                    <th>Barang</th>
                                    <th>Spesifikasi</th>
                                    <th>Qty Mutasi</th>
                                    <th>Satuan</th>
                                    <th>Dokumen Pemasukan</th>
                                    <th>No Aju / No Daftar</th>
                                    <th>Tgl Dokumen</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="body-table">
                            </tbody>
                            <tfoot class="foot-detail-table" id="foot-detail-table">
                                <tr>
                                    <td colspan="10">
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
<div class="modal fade" id="modalUpdateNoAju" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Ubah Nomor Pengajuan</h5>
            </div>
            <form id="form-update">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input id="tanggal_pengajuan" value="" name="tanggal_pengajuan" type="text" class="tanggal_pengajuan form-control" placeholder="">
                                    <label>Tanggal</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="no_urut_dokumen" name="no_urut_dokumen" type="number" class="no_urut_dokumen form-control" placeholder="" oninput="event.target.value = /^\d{0,6}$/.test(event.target.value) ? event.target.value : ''">
                                <label>Nomor Urut</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="kode_kantor" name="kode_kantor" type="number" class="kode_kantor form-control" placeholder="" oninput="event.target.value = /^\d{0,6}$/.test(event.target.value) ? event.target.value : ''">
                                <label>Kode Kantor</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="no_pengajuan" value="<?= !empty($bc27) ? $bc27['no_aju'] : $noAju ?>" name="no_pengajuan" type="text" readonly class="no_pengajuan form-control" placeholder="">
                                <label>Preview Nomor Pengajuan</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3 btn-discard-modal" data-bs-dismiss="modal">Kembali</button>
                    <?php if (!empty($bc27)) : ?>
                        <?php if ($bc27['status_posting'] === "0") : ?>
                            <button type="button" class="btn btn-submit-form" id="ubahNoAjuButton">Simpan</button>
                        <?php endif; ?>
                    <?php else : ?>
                        <button type="button" class="btn btn-submit-form" id="ubahNoAjuButton">Simpan</button>
                    <?php endif; ?>
                </div>
            </form>

        </div>
    </div>
</div>

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
                                <input autocomplete="one-time-code" readonly type="text" class="form-control company_tujuan_name" id="company_tujuan_name" name="company_tujuan_name" placeholder="Company Tujuan">
                                <label for="floatingInput">Company Tujuan</label>
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
                            <div class="form-floating" style="height: 50px;">
                                <select <?= !empty($bc27) ? ($bc27['status_posting'] == '1' ? 'disabled' : '') : '' ?> class="form-select spesifikasi_hasil_id" name="spesifikasi_hasil_id" id="spesifikasi_hasil_id">
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
                                <input readonly <?= !empty($bc27) ? ($bc27['status_posting'] == '1' ? 'disabled' : '') : '' ?> autocomplete="one-time-code" type="text" class="form-control qty_diterima_current" id="qty_diterima_current" name="qty_diterima_current" placeholder="Qty Diterima Sekarang" oninput="preventNegativeInput(this)">
                                <label for="floatingInput">Qty Barang Masuk</label>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-barang-masuk mr-2">Kembali</button>
                <?php if (!empty($bc27)) : ?>
                    <?php if ($bc27['status_posting'] != '1') : ?>
                        <button type="submit" class="btn btn-submit-form btn-submit-form-barang-masuk">Simpan</button>
                    <?php else : ?>

                    <?php endif; ?>
                <?php else : ?>
                    <button type="submit" class="btn btn-submit-form btn-submit-form-barang-masuk">Simpan</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    var listData = [];
    $('.form-penerimaan-mutasi').hide();

    // INIT PAS EDIT
    <?php if (!empty($bc27)) : ?>
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-27/list-barang-mutasi'); ?>`,
            method: "GET",
            data: {
                mutasi_global_id: "<?= $bc27['mutasi_global_id'] ?>",
            },
            dataType: "json",
            success: function(res) {
                listData = [];
                listData = res.data;
                drawTable(listData);
            }
        });

        <?php if ($bc27['penerimaan_otomatis'] == 1): ?>
            $('.form-penerimaan-mutasi').show();
        <?php else: ?>
            $('.form-penerimaan-mutasi').hide();
        <?php endif; ?>
    <?php endif; ?>

    $('#company_tujuan_id').select2({
        placeholder: "Pilih Company Tujuan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        getListMutasiGobal();
        dropdownDivisi();
    });

    $('#divisi_tujuan_id').select2({
        placeholder: "Pilih Departemen Tujuan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        dropdownWarehouse();
    });

    $('#warehouse_tujuan_id').select2({
        placeholder: "Pilih Warehouse Tujuan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {

    });

    $('#spesifikasi_hasil_id').select2({
        placeholder: "Pilih Barang Masuk",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#update_barang_masuk'),
        ajax: {
            url: '<?= base_url('penerimaan-mutasi/list-barang-masuk'); ?>',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    company_tujuan_id: $('#company_tujuan_id option:selected').val()
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

    $('#mutasi_global_id').select2({
        placeholder: "Pilih Nomor Mutasi",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        var selected = $('#mutasi_global_id option:selected');
        var divisiName = selected.data('divisi_name');
        var warehouseName = selected.data('warehouse_name');
        $('#divisi_asal_name').val(divisiName);
        $('#warehouse_asal_name').val(warehouseName);
        // DRAWTABLE MUTASI DETAIL
        getListMutasiDetail();
    });

    $("#company_tujuan_id,#mutasi_global_id,#divisi_tujuan_id,#warehouse_tujuan_id,#mutasi_global_id,#spesifikasi_hasil_id,#unit_hasil_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('#penerimaan_otomatis').on('change', function() {
        var checked = $(this).is(':checked');
        if (checked) {
            dropdownDivisi();
            $('.form-penerimaan-mutasi').show();
        } else {
            $('#divisi_tujuan_id').val(null).change();
            $('#warehouse_tujuan_id').val(null).change();
            $('.form-penerimaan-mutasi').hide();
        }
        drawTable(listData);
    });


    // NO AJU ACTION
    $('#no_urut_dokumen').keyup(function() {
        var noAju = $('#no_pengajuan').val();
        var splitValues = noAju.split("-");
        splitValues[3] = $(this).val();
        $('#no_pengajuan').val(splitValues[0] + '-' + splitValues[1] + '-' + splitValues[2] + '-' + splitValues[3]);
    });
    $('#kode_kantor').keyup(function() {
        var noAju = $('#no_pengajuan').val();
        var splitValues = noAju.split("-");
        splitValues[1] = $(this).val();
        $('#no_pengajuan').val(splitValues[0] + '-' + splitValues[1] + '-' + splitValues[2] + '-' + splitValues[3]);
    });

    $("#tanggal_pengajuan").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    }).change(function() {
        var tanggalPengajuan = $(this).val();
        var noAju = $('#no_pengajuan').val();
        var tanggalPengajuanSplit = tanggalPengajuan.split("/");
        var noPengajuanSplit = noAju.split("-");
        $('#no_pengajuan,#no_aju').val(noPengajuanSplit[0] + '-' + noPengajuanSplit[1] + '-' + tanggalPengajuanSplit[2] + '' + tanggalPengajuanSplit[1] + '' + tanggalPengajuanSplit[0] + '-' + noPengajuanSplit[3]);
    });

    $('#ubahNoAjuButton').click(function(e) {
        e.preventDefault();
        var no_pengajuan = $('#no_pengajuan').val();
        $('#no_aju').val(no_pengajuan);
        $('#modalUpdateNoAju').modal('hide');
    });

    $('#tanggal').datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#tanggal').change(function(e) {
        e.preventDefault();
        var tanggal = $('#tanggal').val();
        // GENERATE NO AJU
        generateNomorAju(tanggal);
    });

    // VALIDATOR
    var validator = $(".create-form").validate({
        rules: {
            company_tujuan_id: {
                required: true
            },
            mutasi_global_id: {
                required: true
            },
            no_aju: {
                required: true
            },
            no_daftar: {
                required: true
            },
            tanggal: {
                required: true
            }
        },
        messages: {
            company_tujuan_id: {
                required: "Company tujuan wajib diisi"
            },
            mutasi_global_id: {
                required: "Pilih nomor mutasi"
            },
            no_daftar: {
                required: "No Daftar wajib diisi"
            },
            tanggal: {
                required: "Tanggal dokumen wajib diisi"
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
        if ($('.create-form').valid()) {
            var divisiTujuanId = $('#divisi_tujuan_id option:selected').val();
            var warehouseTujuanId = $('#warehouse_tujuan_id option:selected').val();
            var checked = $('#penerimaan_otomatis').is(':checked');

            if ((divisiTujuanId == '' || divisiTujuanId == undefined || warehouseTujuanId == '' || warehouseTujuanId == '') && checked) {
                Swal.fire({
                    icon: 'error',
                    title: "Departemen & Warehouse Penerimaan Barang Mutasi Wajib Diisi",
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                })
            } else {

                // Validasi Apakah spesifikasi_hasil_id sudah diterapkan jikalau checked true
                var error = false;
                var noAju = '';
                var bcType = '';
                var namaBarang = '';
                if (checked) {
                    for (let i = 0; i < listData.length; i++) {
                        if (listData[i].mutasi.spesifikasi_hasil_id == '' || listData[i].mutasi.spesifikasi_hasil_id == null) {
                            noAju = listData[i].dokumen_asal.no_aju;
                            bcType = listData[i].type_bc;
                            namaBarang = listData[i].barang_name + " " + listData[i].spesifikasi;
                            error = true;
                        }
                    }
                }

                if (error == true) {
                    Swal.fire({
                        icon: 'error',
                        title: "Barang " + namaBarang + ", Dengan Asal Barang " + bcType + " / " + noAju + ", Belum Dibuatkan Hasil Mutasi Output Barang Di company Tujuan",
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
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Kembali',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var id = $('#id').val();
                            var url = id == '' ? `<?= base_url("bea-cukai-bc-27/save"); ?>` : `<?= base_url("bea-cukai-bc-27/update"); ?>`;
                            var formData = new FormData(document.querySelector(".create-form"));
                            formData.append('barang', JSON.stringify(listData))
                            $.ajax({
                                url: url,
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
                                            location.href = "<?= base_url('bea-cukai-bc-27') ?>"
                                        });

                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: res.message,
                                            confirmButtonColor: '#4e73df',
                                            confirmButtonText: 'Ok'
                                        })
                                    }
                                }
                            })
                        }
                    })

                }
            }
        }
    });

    $('.btn-discard-modal').click(function(e) {
        e.preventDefault();
        $('#modalUpdateNoAju').modal('hide');
    })

    $('.btn-discard-barang-masuk ').click(function(e) {
        e.preventDefault();
        $('#update_barang_masuk').modal('hide');
    })

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

    $('.btn-submit-form-barang-masuk').click(function(e) {
        e.preventDefault();
        if ($('.create-form-barang-masuk').valid()) {
            var id = $('#mutasi_global_detail_id').val();
            var spesifikasiHasilId = $('#spesifikasi_hasil_id option:selected').val();
            var unitHasilId = $('#spesifikasi_hasil_id option:selected').data('satuan_1');
            var kodeBarang = $('#spesifikasi_hasil_id option:selected').data('kode_barang');
            var barangName = $('#spesifikasi_hasil_id option:selected').data('barang_name');
            var spesifikasi = $('#spesifikasi_hasil_id option:selected').data('spesifikasi');

            var divisiTujuanId = $('#divisi_tujuan_id option:selected').val();
            var warehouseTujuanId = $('#warehouse_tujuan_id option:selected').val();
            var companyTujuanId = $('#company_tujuan_id option:selected').val();

            var index = 0;
            $.each(listData, function(i, v) {
                if (v.id == id) {
                    index = i;
                }
            });

            console.log(unitHasilId);

            listData[index].mutasi.spesifikasi_hasil_id = spesifikasiHasilId;
            listData[index].mutasi.kode_barang = kodeBarang;
            listData[index].mutasi.barang_name = barangName;
            listData[index].mutasi.spesifikasi = spesifikasi;
            listData[index].mutasi.unit_hasil_id = unitHasilId;
            listData[index].mutasi.divisi_tujuan_id = divisiTujuanId;
            listData[index].mutasi.warehouse_tujuan_id = warehouseTujuanId;
            listData[index].mutasi.company_tujuan_id = companyTujuanId;

            $('#update_barang_masuk').modal('hide');
            drawTable(listData);
        }
    })

    function displayDetailModal(id) {
        // RESET VALIDATOR
        var divisiTujuanId = $('#divisi_tujuan_id option:selected').val();
        var warehouseTujuanId = $('#warehouse_tujuan_id option:selected').val();

        if (divisiTujuanId == '' || warehouseTujuanId == '') {
            Swal.fire({
                icon: 'error',
                title: "Departemen & Warehouse Penerimaan Barang Mutasi Wajib Diisi",
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            })
        } else {
            validatorBarangMasuk.resetForm();
            validatorBarangMasuk.reset();

            var barangFirst = null;
            $.each(listData, function(i, v) {
                if (v.id == id) {
                    barangFirst = v;
                }
            });

            var nomorAju = $('#no_aju').val();
            var companyAsalName = $('#company_tujuan_id option:selected').text();
            var barangDikirim = barangFirst.kode_barang + ' / ' + barangFirst.barang_name + " - " + barangFirst.spesifikasi;
            var dokumenMutasi = 'BC 2.7 / ' + nomorAju;
            var dokumenAsal = barangFirst.type_bc == "NON PABEAN" ? "" : (barangFirst.dokumen_asal.no_aju == null ? "" : barangFirst.dokumen_asal.no_daftar + ' / ' + barangFirst.dokumen_asal.no_aju);
            var divisiTujuan = $('#divisi_tujuan_id option:selected').text();
            var warehouseTujuan = $('#warehouse_tujuan_id option:selected').text();
            var qtyDiterimaCurrent = barangFirst.mutasi.qty_konversi;
            var unitHasilId = barangFirst.mutasi.unit_hasil_id;

            $('#company_tujuan_name').val(companyAsalName.trim());
            $('#barang_keluar_name').val(barangDikirim);
            $('#dokumen_mutasi_name').val(dokumenMutasi);
            $('#dokumen_asal_name').val(dokumenAsal);
            $('#divisi_penerima_name').val(divisiTujuan.trim());
            $('#warehouse_penerima_name').val(warehouseTujuan.trim());
            $('#qty_diterima_current').val(qtyDiterimaCurrent);
            $('#mutasi_global_detail_id').val(id);
            $('#unit_hasil_id').val(unitHasilId).change();
            // Reset dan isi spesifikasi
            const $spesifikasi = $("#spesifikasi_hasil_id");
            $spesifikasi.empty()
                .append('<option value=""></option>')
                .append(`
                <option 
                    data-satuan_1="${barangFirst.mutasi.unit_hasil_id}"
                    data-kode_barang="${barangFirst.mutasi.kode_barang}" 
                    data-barang_name="${barangFirst.mutasi.barang_name}" 
                    data-spesifikasi="${barangFirst.mutasi.spesifikasi}" 
                    selected 
                    value="${barangFirst.mutasi.spesifikasi_hasil_id}">
                    (${barangFirst.mutasi.kode_barang}) ${barangFirst.mutasi.barang_name} - ${barangFirst.mutasi.spesifikasi}
                </option>
            `)
                .val(barangFirst.mutasi.spesifikasi_hasil_id)
                .trigger('change');
            $('#spesifikasi_hasil_id').val(barangFirst.mutasi.spesifikasi_hasil_id).change();
            $('#update_barang_masuk').modal('show');

        }
    }


    function noAjuShowModal() {
        var noAju = $('#no_pengajuan').val();
        var splitValues = noAju.split("-");

        var year = splitValues[2].substring(0, 4);
        var month = splitValues[2].substring(4, 6);
        var day = splitValues[2].substring(6, 8);

        var formattedDate = day + '/' + month + '/' + year;

        $('#tanggal_pengajuan').val(formattedDate);
        $('#no_pengajuan').val(noAju);
        $('#no_urut_dokumen').val(splitValues[3]);
        $('#kode_kantor').val(splitValues[1]);
        $('#modalUpdateNoAju').modal('show');
    }

    function generateNomorAju(tanggal) {
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-27/generate-no-aju'); ?>`,
            method: "GET",
            data: {
                tanggal: tanggal
            },
            dataType: "json",
            success: function(res) {
                if (res.status == false) {
                    Swal.fire({
                        icon: 'error',
                        title: res.message,
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                } else {
                    // APPEND
                    if (res.data != '') {
                        $('#no_aju').val(res.data).change();
                        $('#no_pengajuan').val(res.data).change();
                    }
                }
            }
        });
    }

    function getListMutasiDetail() {
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-27/list-barang-mutasi'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                mutasi_global_id: $("#mutasi_global_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                listData = [];
                listData = res.data;
                drawTable(listData);
            }
        });
    }

    function dropdownDivisi() {
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-27/divisi'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                company_tujuan_id: $("#company_tujuan_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $("#divisi_tujuan_id").empty()
                $("#divisi_tujuan_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $("#divisi_tujuan_id").append(`<option value="${item.id}">${item.divisi}</option>`)
                })
                $("#divisi_tujuan_id").val();
            }
        });
    }

    function dropdownWarehouse() {
        $.ajax({
            url: `<?= base_url('jasa-vendor-out/warehouse'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $("#divisi_tujuan_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $("#warehouse_tujuan_id").empty()
                $("#warehouse_tujuan_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $("#warehouse_tujuan_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
                $("#warehouse_tujuan_id").val();
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
            newRow.append($('<td colspan="10" >Tidak Ada Barang</td>'));
            table.find('tfoot').append(newRow);
        } else {
            var checked = $('#penerimaan_otomatis').is(':checked');
            $.each(listData, function(i, v) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td >').html(
                    `
                            ${no++} 
                        `
                ));
                newRow.append($('<td >').text(v.kode_barang));
                newRow.append($('<td >').text(v.barang_name));
                newRow.append($('<td >').text(v.spesifikasi));
                newRow.append($('<td >').text(greatFormatRupiah(v.mutasi.qty_konversi)));
                newRow.append($('<td >').text(v.mutasi.unit_name_konversi));
                newRow.append($('<td >').text(v.type_bc));
                newRow.append($('<td >').text(v.dokumen_asal.no_aju == null ? "" : v.dokumen_asal.no_aju + ' / ' + v.dokumen_asal.no_daftar));
                newRow.append($('<td >').text(v.dokumen_asal.no_aju == null ? "" : v.dokumen_asal.tanggal_dokumen));
                if (checked) {
                    newRow.append($('<td >').html(`
                    <button type="button" class="btn btn-primary" onclick="displayDetailModal(${v.id})" data-toggle="tooltip"><i class="fas fa-pencil-alt"></i></button>
                `));
                } else {
                    newRow.append($('<td >').text(''));
                }

                table.find('tbody').append(newRow);
            });
        }
    }

    function getListMutasiGobal() {
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-27/list-mutasi-global'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                company_tujuan_id: $(".company_tujuan_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".mutasi_global_id").empty()
                $(".mutasi_global_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".mutasi_global_id").append(`<option 
                        data-divisi_name="${item.divisiName}" 
                        data-warehouse_name="${item.warehouseName}" 
                        value="${item.id}">${item.no_mutasi}
                    </option>`)
                })
                $(".mutasi_global_id").val();
            }
        });
    }

    function deleteAction() {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Dokumen BC 2.7 ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $('#id').val();
                var formData = new FormData();
                formData.append("id", id);
                $.ajax({
                    url: `<?= base_url("bea-cukai-bc-27/delete"); ?>`,
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
                                    location.href = "<?= base_url('bea-cukai-bc-27') ?>"
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
            title: 'Posting Dokumen BC 2.7 ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $('#id').val();
                var formData = new FormData();
                formData.append("id", id);
                $.ajax({
                    url: `<?= base_url("bea-cukai-bc-27/posting"); ?>`,
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
                                    location.href = "<?= base_url('bea-cukai-bc-27') ?>"
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            });
                        }
                    }
                })
            }
        })
    }
</script>

<?= $this->endSection(); ?>