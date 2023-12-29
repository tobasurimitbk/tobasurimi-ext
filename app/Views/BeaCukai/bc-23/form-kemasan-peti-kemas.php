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
    <div class="card">
        <div class="card-header" style="font-weight: bold; color:black;">
            BC 2.3 - PEMBERITAHUAN IMPOR BARANG UNTUK DITIMBUN DI TEMPAT PENIMBUNAN BERIKAT
        </div>
        <div class="card-body">
            <?php include_once('nav.php') ?>
            <div class="row mt-1">
                <div class="col-sm-6 mt-1">
                    <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                        Kemasan
                    </label>
                    <div class="mt-1">
                        <div class="form-floating mb-3">
                            <input id="kemasan_seri_kemasan" name="kemasan_seri_kemasan" type="number" class="form-control kemasan_seri_kemasan" placeholder="">
                            <label>Seri Kemasan</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3">
                            <input id="kemasan_jumlah_kemasan" name="kemasan_jumlah_kemasan" type="number" class="form-control kemasan_jumlah_kemasan" placeholder="">
                            <label>Jumlah Kemasan</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select kemasan_jenis_kemasan" id="kemasan_jenis_kemasan" name="kemasan_jenis_kemasan" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($kodeJenisKemasan as $k) : ?>
                                    <option value="<?= encrypt($k['description']) ?>">
                                        <?= $k['description'] . " - " . strtoupper($k['value']) . "" ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label style="z-index: 1;">Pilih Jenis Kemasan</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3">
                            <input id="kemasan_merk_kemasan" name="kemasan_merk_kemasan" type="text" class="form-control kemasan_merk_kemasan" placeholder="">
                            <label>Merk Kemasan</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="row" style="float: right; margin-bottom:5px;">
                                <div class="col-sm" style="margin-right: -20px;">
                                    <button type="button" class="btn btn-add btn-block float-right btn-submit-kemasan" style="float: right;">
                                        <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-kemasan" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center; width:10px;">No</th>
                                    <th style="text-align: center;">Seri Kemasan</th>
                                    <th style="text-align: center;">Jumlah Kemasan</th>
                                    <th style="text-align: center;">Jenis Kemasan</th>
                                    <th style="text-align: center;">Merk Kemasan</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="col-sm-6 mt-1">
                    <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                        Kontainer / Peti Kemas
                    </label>
                    <div class="mt-1">
                        <div class="form-floating mb-3">
                            <input id="kontainer_seri" name="kontainer_seri" type="number" class="form-control kontainer_seri" placeholder="">
                            <label>Seri</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3">
                            <input id="kontainer_nomor" name="kontainer_nomor" type="text" class="form-control kontainer_nomor" placeholder="">
                            <label>Nomor</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select kontainer_ukuran" id="kontainer_ukuran" name="kontainer_ukuran" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($kodeUkuranKontainer as $k) : ?>
                                    <option value="<?= encrypt($k['value']) ?>">
                                        <?= $k['value'] . " - " . strtoupper($k['description']) . "" ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label style="z-index: 1;">Pilih Ukuran Peti Kemas</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select kontainer_jenis" id="kontainer_jenis" name="kontainer_jenis" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($kodeJenisKontainer as $k) : ?>
                                    <option value="<?= encrypt($k['description']) ?>">
                                        <?= $k['description'] . " - " . strtoupper($k['value']) . "" ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label style="z-index: 1;">Pilih Jenis Peti Kemas</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select kontainer_tipe" id="kontainer_tipe" name="kontainer_tipe" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($kodeTipeKontainer as $k) : ?>
                                    <option value="<?= encrypt($k['value']) ?>">
                                        <?= $k['value'] . " - " . strtoupper($k['description']) . "" ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label style="z-index: 1;">Pilih Tipe Peti Kemas</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="row" style="float: right; margin-bottom:5px;">
                                <div class="col-sm" style="margin-right: -20px;">
                                    <button type="button" class="btn btn-add btn-block float-right btn-submit-peti-kemas" style="float: right;">
                                        <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-peti-kemas" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center; width:10px;">No</th>
                                    <th style="text-align: center;">Seri Peti Kemas</th>
                                    <th style="text-align: center;">Nomor Peti Kemas</th>
                                    <th style="text-align: center;">Ukuran Peti Kemas</th>
                                    <th style="text-align: center;">Jenis Peti Kemas</th>
                                    <th style="text-align: center;">Tipe Peti Kemas</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
    $('#kemasan_jenis_kemasan').select2({
        placeholder: "Pilih Jenis Kemasan",
        theme: "bootstrap-5",
    });

    $('#kontainer_ukuran').select2({
        placeholder: "Pilih Ukuran Peti Kemas",
        theme: "bootstrap-5",
    });

    $('#kontainer_jenis').select2({
        placeholder: "Pilih Jenis Peti Kemas",
        theme: "bootstrap-5",
    });

    $('#kontainer_tipe').select2({
        placeholder: "Pilih Tipe Peti Kemas",
        theme: "bootstrap-5",
    });


    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    var tableListInformasiKemasan = $('.table-list-informasi-kemasan').DataTable({
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
    var tableListPetiKemas = $('.table-list-informasi-peti-kemas').DataTable({
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