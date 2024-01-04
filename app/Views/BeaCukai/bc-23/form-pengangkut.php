<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>

<section class="section section-form">
    <?php include('header.php') ?>
    <div class="root-form-view">
        <div class="card">
            <div class="card-header" style="font-weight: bold; color:black;">
                BC 2.3 - PEMBERITAHUAN IMPOR BARANG UNTUK DITIMBUN DI TEMPAT PENIMBUNAN BERIKAT
            </div>
            <div class="card-body">
                <?php include_once('nav.php') ?>
                <div class="row mt-1">
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            BC 1.1
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="bc_11_no_bc_11" name="bc_11_no_bc_11" type="text" maxlength="6" class="form-control bc_11_no_bc_11" placeholder="">
                                <label>Nomor BC 1.1</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" name="bc_11_tanggal_bc_11" type="text" placeholder="" class="form-control bc_11_tanggal_bc_11" id="bc_11_tanggal_bc_11">
                                    <label>Tanggal BC 1.1</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="bc_11_pos_bc_11" name="bc_11_pos_bc_11" type="text" maxlength="4" class="form-control bc_11_pos_bc_11" placeholder="">
                                <label>Nomor Pos BC 1.1</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="bc_11_sub_pos_bc_11" name="bc_11_sub_pos_bc_11" type="text" maxlength="4" class="form-control bc_11_sub_pos_bc_11" placeholder="">
                                <label>Nomor Sub Pos BC 1.1</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="bc_11_sub_sub_pos_bc_11" name="bc_11_sub_sub_pos_bc_11" type="text" maxlength="4" class="form-control bc_11_sub_sub_pos_bc_11" placeholder="">
                                <label>Nomor Sub Sub Pos BC 1.1</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Pengangkutan
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select pengangkutan_cara_pengangkutan" id="pengangkutan_cara_pengangkutan" name="pengangkutan_cara_pengangkutan" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodePengangkutan as $k) : ?>
                                        <option value="<?= encrypt($k['description']) ?>">
                                            <?= $k['description'] . " - " . strtoupper($k['value']) . "" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Cara Pengangkutan</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="pengangkutan_nama_sarana_pengangkut" name="pengangkutan_nama_sarana_pengangkut" type="text" class="form-control pengangkutan_nama_sarana_pengangkut" placeholder="">
                                <label>Nama Sarana Pengangkut</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="pengangkutan_nomor_pengangkut" name="pengangkutan_nomor_pengangkut" type="text" class="form-control pengangkutan_nomor_pengangkut" placeholder="">
                                <label>Nomor Voy/Flight/No.Pol</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select pengangkutan_kode_bendera" id="pengangkutan_kode_bendera" name="pengangkutan_kode_bendera" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeBendera as $k) : ?>
                                        <option value="<?= encrypt($k['code']) ?>">
                                            <?= $k['code'] . " - " . strtoupper($k['country_name']) . "" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Bendera</label>
                            </div>
                        </div>

                    </div>

                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Pelabuhan & Tempat Penimbunan
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="pengangkutan_pelabuhan_muat" name="pengangkutan_pelabuhan_muat" type="text" class="form-control pengangkutan_pelabuhan_muat" placeholder="">
                                <label>Kode Pelabuhan Muat</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="pengangkutan_pelabuhan_transit" name="pengangkutan_pelabuhan_transit" type="text" class="form-control pengangkutan_pelabuhan_transit" placeholder="">
                                <label>Kode Pelabuhan Transit</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="pengangkutan_pelabuhan_bongkar" name="pengangkutan_pelabuhan_bongkar" type="text" class="form-control pengangkutan_pelabuhan_bongkar" placeholder="">
                                <label>Kode Pelabuhan Bongkar</label>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="#" class="btn btn-primary mt-4" style="float: right;">
                    Simpan Perubahan
                </a>
            </div>
        </div>
</section>

<script>
    $('#pengangkutan_cara_pengangkutan').select2({
        placeholder: "Pilih Cara Pengangkutan",
        theme: "bootstrap-5",
    });

    $('#pengangkutan_kode_bendera').select2({
        placeholder: "Pilih Bendera",
        theme: "bootstrap-5",
    });

    $("#bc_11_tanggal_bc_11").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    // TABEL LIST DOKUMEN
    var tableListInformasiDokumen = $('.table-list-informasi-dokumen').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        lengthChange: true,
        info: false,
        paging: false,
        searching: false,
        ordering: false,
        order: [],
        fixedHeader: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });
</script>


<?= $this->endSection(); ?>