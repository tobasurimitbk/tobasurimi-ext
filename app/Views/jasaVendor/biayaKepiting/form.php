<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($biayaKepiting) ? "Tambah Biaya Kepiting" : "Update Biaya Kepiting" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("biaya-kepiting"); ?>">
                Kembali
            </a>
            <?php if (!empty($biayaKepiting)) : ?>
                <?php if ($biayaKepiting['status_posting'] == "0") : ?>
                    <?php if (can('Jasa Vendor', 'Biaya Kepiting', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="remove('<?= encrypt($biayaKepiting['id']); ?>')">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Biaya Kepiting', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="posting('<?= encrypt($biayaKepiting['id']); ?>')">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Biaya Kepiting', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("biaya-kepiting/print/"); ?><?= encrypt($biayaKepiting['id']); ?>')">
                            Print
                        </button>
                    <?php endif; ?>

                    <?php if (can('Jasa Vendor', 'Biaya Kepiting', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if (can('Jasa Vendor', 'Biaya Kepiting', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("biaya-kepiting/print/"); ?><?= encrypt($biayaKepiting['id']); ?>')">
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
                    <label class="form-label font-weight-bold lable-title">Data Barang Masuk Vendor</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id" value="<?= !empty($biayaKepiting) ? encrypt($biayaKepiting['id']) : '' ?>" class="id">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" <?= !empty($biayaKepiting) ? 'disabled=true' : ''; ?> value="<?= !empty($biayaKepiting) ? $biayaKepiting['no_pembayaran'] : "PAY-KPT/" . date('m') . "/1/" . date('Y'); ?>" type="text" class="form-control no_pembayaran" id="no_pembayaran" name="no_pembayaran" placeholder="No. Rebus">
                                    <label for="floatingInput">No. Pembayaran</label>
                                </div>
                                <div style="<?= !empty($biayaKepiting) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($biayaKepiting) ? 'disabled' : '' ?> autocomplete="one-time-code" class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime(!empty($biayaKepiting) ? $biayaKepiting['tanggal'] : $tanggal)); ?>">
                                    <label for="floatingInput">Tanggal Pembayaran</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3 form-add-spp" style="height: 50px;">
                            <select <?= !empty($biayaKepiting) ? 'disabled' : '' ?> class="form-select jasa_vendor_in_id" id="jasa_vendor_in_id" name="jasa_vendor_in_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                <?php if (!empty($jasaVendorIn)) : ?>
                                    <?php foreach ($jasaVendorIn as $j) : ?>
                                        <option data-warehouse_id="<?= $j['warehouse_id'] ?>" data-vendor="<?= strtoupper($j['name']) ?>" data-divisi="<?= strtoupper($j['divisi']) ?>" value="<?= $j['id'] ?>">
                                            <?= $j['no_penerimaan_surat_jalan'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <?php if (!empty($jasaVendorInDetail)) : ?>
                                        <option selected value="<?= $jasaVendorInDetail['id'] ?>">
                                            <?= $jasaVendorInDetail['no_penerimaan_surat_jalan'] ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Nomor Penerimaan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($biayaKepiting) ? $jasaVendorInDetail['name'] : '' ?>" autocomplete="one-time-code" disabled type="text" class="form-control vendor" id="vendor" name="vendor" placeholder="Vendor">
                            <label for="floatingInput">Vendor</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($biayaKepiting) ? $jasaVendorInDetail['divisi'] : '' ?>" autocomplete="one-time-code" disabled type="text" class="form-control divisi" id="divisi" name="divisi" placeholder="Departemen">
                            <label for="floatingInput">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($biayaKepiting) ? ($biayaKepiting['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Keterangan" value="<?= !empty($biayaKepiting) ? $biayaKepiting['keterangan'] : '' ?>" class="form-control keterangan" id="keterangan" name="keterangan" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Keterangan (Opsional)</label>
                        </div>
                    </div>
                </div>

            </form>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">List Barang Masuk</label>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" border="1" cellspacing="0"=>
                            <thead class="thead-dark" id="dynamicHeader">
                                <!-- Header akan diisi secara dinamis -->
                            </thead>
                            <tbody class="body-table">
                            </tbody>
                            <tfoot class="foot-detail-table" id="foot-detail-table">
                                <tr>
                                    <td colspan="13" style="text-align: center;">
                                        Tidak Ada Barang
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Perhitungan Perolehan Gaji</label>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable2" width="100%" border="1" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;">JENIS</th>
                                    <th style="text-align: center;">JUMBO</th>
                                    <th style="text-align: center;">EX LUMP</th>
                                    <th style="text-align: center;">LUMP</th>
                                    <th style="text-align: center;">SPESIAL</th>
                                    <th style="text-align: center;">CLAW</th>
                                    <th style="text-align: center;">MH</th>
                                    <th style="text-align: center;">CF</th>
                                    <th style="text-align: center;">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody class="body-table-2">
                            </tbody>
                            <tfoot class="foot-detail-table-2" id="foot-detail-table">
                                <tr>
                                    <td colspan="9" style="text-align: center;">
                                        Tidak Ada Barang
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Perhitungan Bonus Khusus</label>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable3" width="100%" border="1" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;" colspan="6" class="thead-bonus"><?= !empty($biayaKepiting) ? "Bonus Khusus Untuk Vendor " . $jasaVendorInDetail['name'] : "Bonus Khusus Untuk Vendor" ?></th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">Tanggal Masuk</th>
                                    <th style="text-align: center;">Barang </th>
                                    <th style="text-align: center;">Kg</th>
                                    <th style="text-align: center;">Bonus</th>
                                    <th style="text-align: center;">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody class="body-table-3">
                            </tbody>
                            <tfoot class="foot-detail-table-3" id="foot-detail-table">
                                <tr>
                                    <td colspan="6" style="text-align: center;">
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

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    var request;
    var listBarang = [];
    var listPerolehanGaji = [];
    var listBonus = [];
    var listDataVendor= [];

    <?php if (!empty($biayaKepiting)) : ?>
        $.ajax({
            url: `<?= base_url('biaya-kepiting/list-barang'); ?>`,
            method: "GET",
            data: {
                jasa_vendor_in_id: $(".jasa_vendor_in_id option:selected").val(),
                id: $('.id').val()
            },
            dataType: "json",
            success: function(res) {
                csrf.val(res.token);
                listBarang = res.data;
                listPerolehanGaji = res.dataPerolehanGaji;
                listDataVendor = res.dataVendor;
                listBonus = res.dataBonus;
                drawTable();
            }
        });
    <?php endif; ?>

    $(".tanggal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('.jasa_vendor_in_id').select2({
        placeholder: "Pilih Surat Jalan",
        theme: "bootstrap-5",
    }).change(function() {
        var selected = $('.jasa_vendor_in_id option:selected');
        $('.vendor').val(selected.data('vendor'));
        $('.divisi').val(selected.data('divisi'));
        $('.thead-bonus').text("Bonus Khusus Untuk " + selected.data('vendor'));

        listDataBarang();
        changeStatus();
    })
    
    
    // $('.jasa_vendor_in_id').select2({
    //     placeholder: "Pilih Barang Dan Spesifikasi",
    //     theme: "bootstrap-5",
    //     minimumInputLength: 3, // ngetik min 3 huruf baru jalanin ajax
    //     ajax: {
    //         url: "<?= base_url('biaya-kepiting/search-barang'); ?>", // endpoint buat search
    //         dataType: 'json',
    //         delay: 250, // kasih jeda biar ga nembak server tiap huruf
    //         data: function (params) {
    //             return {
    //                 q: params.term // keyword pencarian
    //             };
    //         },
    //         processResults: function (data) {
    //             return {
    //                 results: data.map(function(item) {
    //                     return {
    //                         id: item.id, // value select
    //                         text: item.barang_name, // label yang tampil
    //                     }
    //                 })
    //             };
    //         }
    //     }
    // }).on("change", function () {
    //     var selected = $('.jasa_vendor_in_id').select2('data')[0];

    //     listDataBarang();
    //     changeStatus();
    // });

    $(".jasa_vendor_in_id")
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
            jasa_vendor_in_id: {
                required: true
            },
        },
        messages: {
            tanggal: {
                required: "Tanggal wajib diisi"
            },
            jasa_vendor_in_id: {
                required: "Penerimaan surat jalan wajib diisi"
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

    // $('.btn-submit-parent').click(function() {
    //     if (listBarang.length == 0) {
    //         Swal.fire({
    //             icon: 'error',
    //             title: 'Barang masuk tidak boleh kosong !',
    //             confirmButtonColor: '#4e73df',
    //             confirmButtonText: 'Ok'
    //         });
    //     } else {
    //         if ($('.create-form').valid()) {
    //             // VALIDASI FORM 1 & FORM 3
    //             var isValidBarangJumbo = true;
    //             var dataErrorBarangJumbo = null;

    //             var isValidBarangExLump = true;
    //             var dataErrorBarangExLump = null;

    //             var isValidBarangLump = true;
    //             var dataErrorBarangLump = null;

    //             var isValidBarangSpecial = true;
    //             var dataErrorBarangSpecial = null;

    //             var isValidBarangClaw = true;
    //             var dataErrorBarangClaw = null;

    //             var isValidBarangMh = true;
    //             var dataErrorBarangMh = null;

    //             var isValidBarangCf = true;
    //             var dataErrorBarangCf = null;

    //             var isValidBonusKg = true;
    //             var dataErrorBonusKg = null;

    //             var isValidBonusNominal = true;
    //             var dataErrorBonusNominal = null;

    //             $.each(listBarang, function(i, v) {
    //                 var barangJumboElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].jumbo');
    //                 var barangExLumpElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].ex_lump');
    //                 var barangLumpElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].lump');
    //                 var barangSpecialElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].special');
    //                 var barangClawElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].claw');
    //                 var barangMhElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].mh');
    //                 var barangCfElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].cf');

    //                 if (barangJumboElement.val() === undefined || barangJumboElement.val() === '') {
    //                     isValidBarangJumbo = false;
    //                     dataErrorBarangJumbo = listBarang[i];
    //                 } else {
    //                     listBarang[i].jumbo = barangJumboElement.val();
    //                 }

    //                 if (barangExLumpElement.val() === undefined || barangExLumpElement.val() === '') {
    //                     isValidBarangExLump = false;
    //                     dataErrorBarangExLump = listBarang[i];
    //                 } else {
    //                     listBarang[i].ex_lump = barangExLumpElement.val();
    //                 }

    //                 if (barangLumpElement.val() === undefined || barangLumpElement.val() === '') {
    //                     isValidBarangLump = false;
    //                     dataErrorBarangLump = listBarang[i];
    //                 } else {
    //                     listBarang[i].lump = barangLumpElement.val();
    //                 }

    //                 if (barangSpecialElement.val() === undefined || barangSpecialElement.val() === '') {
    //                     isValidBarangSpecial = false;
    //                     dataErrorBarangSpecial = listBarang[i];
    //                 } else {
    //                     listBarang[i].special = barangSpecialElement.val();
    //                 }

    //                 if (barangClawElement.val() === undefined || barangClawElement.val() === '') {
    //                     isValidBarangClaw = false;
    //                     dataErrorBarangClaw = listBarang[i];
    //                 } else {
    //                     listBarang[i].claw = barangClawElement.val();
    //                 }

    //                 if (barangMhElement.val() === undefined || barangMhElement.val() === '') {
    //                     isValidBarangMh = false;
    //                     dataErrorBarangMh = listBarang[i];
    //                 } else {
    //                     listBarang[i].mh = barangMhElement.val();
    //                 }

    //                 if (barangCfElement.val() === undefined || barangCfElement.val() === '') {
    //                     isValidBarangCf = false;
    //                     dataErrorBarangCf = listBarang[i];
    //                 } else {
    //                     listBarang[i].cf = barangCfElement.val();
    //                 }
    //             });
    //         }

    //         // VALIDASI FORM 2
    //         var isValidPerolehanGaji = true;

    //         $.each(listPerolehanGaji, function(i, v) {
    //             var hargaJumboElement = $('input.' + v.value + '_jumbo');
    //             var hargaExLumpElement = $('input.' + v.value + '_ex_lump');
    //             var hargaLumpElement = $('input.' + v.value + '_lump');
    //             var hargaSpecialElement = $('input.' + v.value + '_special');
    //             var hargaClawElement = $('input.' + v.value + '_claw');
    //             var hargaMhElement = $('input.' + v.value + '_mh');
    //             var hargaCfElement = $('input.' + v.value + '_cf');

    //             if (hargaJumboElement.val() === undefined || hargaJumboElement.val() === '') {
    //                 isValidPerolehanGaji = false;
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: v.description + ' Jumbo tidak valid !',
    //                     confirmButtonColor: '#4e73df',
    //                     confirmButtonText: 'Ok'
    //                 });
    //             } else {
    //                 listPerolehanGaji[i].jumbo = hargaJumboElement.val();
    //             }

    //             if (hargaExLumpElement.val() === undefined || hargaExLumpElement.val() === '') {
    //                 isValidPerolehanGaji = false;
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: v.description + ' Ex Lump tidak valid !',
    //                     confirmButtonColor: '#4e73df',
    //                     confirmButtonText: 'Ok'
    //                 });
    //             } else {
    //                 listPerolehanGaji[i].ex_lump = hargaExLumpElement.val();
    //             }

    //             if (hargaLumpElement.val() === undefined || hargaLumpElement.val() === '') {
    //                 isValidPerolehanGaji = false;
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: v.description + ' Lump tidak valid !',
    //                     confirmButtonColor: '#4e73df',
    //                     confirmButtonText: 'Ok'
    //                 });
    //             } else {
    //                 listPerolehanGaji[i].lump = hargaLumpElement.val();
    //             }

    //             if (hargaSpecialElement.val() === undefined || hargaSpecialElement.val() === '') {
    //                 isValidPerolehanGaji = false;
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: v.description + ' Special tidak valid !',
    //                     confirmButtonColor: '#4e73df',
    //                     confirmButtonText: 'Ok'
    //                 });
    //             } else {
    //                 listPerolehanGaji[i].special = hargaSpecialElement.val();
    //             }

    //             if (hargaClawElement.val() === undefined || hargaClawElement.val() === '') {
    //                 isValidPerolehanGaji = false;
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: v.description + ' Claw tidak valid !',
    //                     confirmButtonColor: '#4e73df',
    //                     confirmButtonText: 'Ok'
    //                 });
    //             } else {
    //                 listPerolehanGaji[i].claw = hargaClawElement.val();
    //             }

    //             if (hargaMhElement.val() === undefined || hargaMhElement.val() === '') {
    //                 isValidPerolehanGaji = false;
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: v.description + ' Mh tidak valid !',
    //                     confirmButtonColor: '#4e73df',
    //                     confirmButtonText: 'Ok'
    //                 });
    //             } else {
    //                 listPerolehanGaji[i].mh = hargaMhElement.val();
    //             }

    //             if (hargaCfElement.val() === undefined || hargaCfElement.val() === '') {
    //                 isValidPerolehanGaji = false;
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: v.description + ' Cf tidak valid !',
    //                     confirmButtonColor: '#4e73df',
    //                     confirmButtonText: 'Ok'
    //                 });
    //             } else {
    //                 listPerolehanGaji[i].cf = hargaCfElement.val();
    //             }
    //         });

    //         // VALIDASI BONUS
    //         $.each(listBonus, function(i, v) {
    //             var barangBonusKgElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].kg_bonus');
    //             var barangBonusNominalElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].bonus_nominal');

    //             if (barangBonusKgElement.val() === undefined || barangBonusKgElement.val() === '') {
    //                 isValidBonusKg = false;
    //                 dataErrorBonusKg = listBonus[i];
    //             } else {
    //                 listBonus[i].kg_bonus = barangBonusKgElement.val();
    //             }

    //             if (barangBonusNominalElement.val() === undefined || barangBonusNominalElement.val() === '') {
    //                 isValidBonusNominal = false;
    //                 dataErrorBonusNominal = listBonus[i];
    //             } else {
    //                 listBonus[i].bonus_nominal = barangBonusNominalElement.val();
    //             }
    //         });

    //         // ALERT FORM 1
    //         if (!isValidBarangJumbo) {
    //             Swal.fire({
    //                 icon: 'error',
    //                 title: dataErrorBarangJumbo.nama_barang + ' Jumbo tidak valid !',
    //                 confirmButtonColor: '#4e73df',
    //                 confirmButtonText: 'Ok'
    //             });
    //         } else if (!isValidBarangExLump) {
    //             Swal.fire({
    //                 icon: 'error',
    //                 title: dataErrorBarangExLump.nama_barang + ' Ex Lump tidak valid !',
    //                 confirmButtonColor: '#4e73df',
    //                 confirmButtonText: 'Ok'
    //             });
    //         } else if (!isValidBarangLump) {
    //             Swal.fire({
    //                 icon: 'error',
    //                 title: dataErrorBarangLump.nama_barang + ' Lump tidak valid !',
    //                 confirmButtonColor: '#4e73df',
    //                 confirmButtonText: 'Ok'
    //             });
    //         } else if (!isValidBarangSpecial) {
    //             Swal.fire({
    //                 icon: 'error',
    //                 title: dataErrorBarangSpecial.nama_barang + ' Special tidak valid !',
    //                 confirmButtonColor: '#4e73df',
    //                 confirmButtonText: 'Ok'
    //             });
    //         } else if (!isValidBarangClaw) {
    //             Swal.fire({
    //                 icon: 'error',
    //                 title: dataErrorBarangClaw.nama_barang + ' Claw tidak valid !',
    //                 confirmButtonColor: '#4e73df',
    //                 confirmButtonText: 'Ok'
    //             });
    //         } else if (!isValidBarangMh) {
    //             Swal.fire({
    //                 icon: 'error',
    //                 title: dataErrorBarangMh.nama_barang + ' Mh tidak valid !',
    //                 confirmButtonColor: '#4e73df',
    //                 confirmButtonText: 'Ok'
    //             });
    //         } else if (!isValidBarangCf) {
    //             Swal.fire({
    //                 icon: 'error',
    //                 title: dataErrorBarangCf.nama_barang + ' Cf tidak valid !',
    //                 confirmButtonColor: '#4e73df',
    //                 confirmButtonText: 'Ok'
    //             });
    //         } else if (!isValidBonusKg) {
    //             Swal.fire({
    //                 icon: 'error',
    //                 title: dataErrorBonusKg.nama_barang + ' Bonus KG tidak valid !',
    //                 confirmButtonColor: '#4e73df',
    //                 confirmButtonText: 'Ok'
    //             });
    //         } else if (!isValidBonusNominal) {
    //             Swal.fire({
    //                 icon: 'error',
    //                 title: dataErrorBonusNominal.nama_barang + ' Bonus Nominal tidak valid !',
    //                 confirmButtonColor: '#4e73df',
    //                 confirmButtonText: 'Ok'
    //             });
    //         } else if (!isValidPerolehanGaji) {
    //             console.log("Validasi perolehan gaji");
    //         } else {
    //             Swal.fire({
    //                 icon: 'question',
    //                 title: 'Simpan Data ?',
    //                 confirmButtonColor: '#4e73df',
    //                 cancelButtonColor: '#d33',
    //                 showCancelButton: true,
    //                 reverseButtons: true,
    //                 confirmButtonText: 'Simpan',
    //                 cancelButtonText: 'Kembali',
    //             }).then((result) => {
    //                 if (result.isConfirmed) {
    //                     let id = $('#id').val();
    //                     let data = new FormData(document.querySelector(".create-form"));
    //                     data.append('listBarang', JSON.stringify(listBarang));
    //                     data.append('listPerolehanGaji', JSON.stringify(listPerolehanGaji));
    //                     data.append("listBonus", JSON.stringify(listBonus));
    //                     data.append("listDataVendor", JSON.stringify(listDataVendor));

    //                     if (id) {
    //                         // UPDATE
    //                         data.append("jasa_vendor_in_id", $('#jasa_vendor_in_id option:selected').val());
    //                         $.ajax({
    //                             url: "<?= base_url("biaya-kepiting/update"); ?>",
    //                             data: data,
    //                             beforeSend: function(xhr) {
    //                                 xhr.setRequestHeader('X-CSRF-Token', csrf.val());
    //                                 setLoading();
    //                             },
    //                             complete: function() {
    //                                 stopLoading()
    //                             },
    //                             method: "POST",
    //                             dataType: "json",
    //                             processData: false,
    //                             contentType: false,
    //                             success: function(response) {
    //                                 if (response.status) {
    //                                     Swal.fire({
    //                                         icon: 'success',
    //                                         title: response.message,
    //                                         confirmButtonColor: '#4e73df',
    //                                         confirmButtonText: 'Ok'
    //                                     }).then((result) => {
    //                                         if (result.isConfirmed) {
    //                                             window.location.href = "<?= base_url("biaya-kepiting") ?>";
    //                                         }
    //                                     });
    //                                 } else {
    //                                     Swal.fire({
    //                                         icon: 'error',
    //                                         title: response.message,
    //                                         confirmButtonColor: '#4e73df',
    //                                         confirmButtonText: 'Ok'
    //                                     });
    //                                 }

    //                             },
    //                         });
    //                     } else {
    //                         // INSERT
    //                         $.ajax({
    //                             url: "<?= base_url("biaya-kepiting/save"); ?>",
    //                             data: data,
    //                             beforeSend: function(xhr) {
    //                                 xhr.setRequestHeader('X-CSRF-Token', csrf.val());
    //                                 setLoading();
    //                             },
    //                             complete: function() {
    //                                 stopLoading()
    //                             },
    //                             method: "POST",
    //                             dataType: "json",
    //                             processData: false,
    //                             contentType: false,
    //                             success: function(response) {
    //                                 if (response.status) {
    //                                     Swal.fire({
    //                                         icon: 'success',
    //                                         title: response.message,
    //                                         confirmButtonColor: '#4e73df',
    //                                         confirmButtonText: 'Ok'
    //                                     }).then((result) => {
    //                                         if (result.isConfirmed) {
    //                                             window.location.href = "<?= base_url("biaya-kepiting") ?>";
    //                                         }
    //                                     });
    //                                 } else {
    //                                     Swal.fire({
    //                                         icon: 'error',
    //                                         title: response.message,
    //                                         confirmButtonColor: '#4e73df',
    //                                         confirmButtonText: 'Ok'
    //                                     });
    //                                 }

    //                             },
    //                         });
    //                     }
    //                 }
    //             });
    //         }
    //     }
    // });




    changeStatus();

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $(".no_pembayaran").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("biaya-kepiting/get-no"); ?>`,
                method: "GET",
                data: {
                    warehouse_id: $('#jasa_vendor_in_id option:selected').data('warehouse_id')
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".no_pembayaran").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".no_pembayaran").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".no_pembayaran").val("");
                    }
                }
            })
        } else {
            $(".no_pembayaran").attr("readonly", false);
            $(".no_pembayaran").val("");
        }
    }

    function listDataBarang() {
        $.ajax({
            url: `<?= base_url('biaya-kepiting/list-barang'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                jasa_vendor_in_id: $(".jasa_vendor_in_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                csrf.val(res.token);
                listBarang = res.data;
                listPerolehanGaji = res.dataPerolehanGaji;
                listBonus = res.dataBonus;
                listDataVendor = res.dataVendor;
                drawTable();
            }
        });
    }

   // Fungsi untuk membuat header tabel secara dinamis
    function buildDynamicHeader() {
        const thead = $('#dynamicHeader');
        thead.empty();
        
        // Baris pertama header
        let firstRow = `<tr>
            <th style="text-align: center;" colspan="3"></th>
            <th style="text-align: center;" colspan="2">Kg Bahan Baku</th>
            <th style="text-align: center;" colspan="${listBarang.thead ? listBarang.thead.length : 7}">Hasil Kopek</th>
            <th style="text-align: center;" colspan="1"></th>
        </tr>`;
        
        // Baris kedua header
        let secondRow = `<tr>
            <th style="text-align: center;">No</th>
            <th style="text-align: center;">Tanggal Masuk</th>
            <th style="text-align: center;">Supplier - Keterangan</th>
            <th style="text-align: center;">Qty Sebelum Kopek</th>
            <th style="text-align: center;">Rasio (%)</th>`;
        
        // Tambahkan kolom untuk setiap spesifikasi
        if (listBarang.thead && listBarang.thead.length > 0) {
            listBarang.thead.forEach(spec => {
                secondRow += `<th style="text-align: center;">${spec}</th>`;
            });
        } else {
            // Fallback jika tidak ada data thead
            secondRow += `
                <th style="text-align: center;">JUMBO</th>
                <th style="text-align: center;">EX LUMP</th>
                <th style="text-align: center;">LUMP</th>
                <th style="text-align: center;">SPESIAL</th>
                <th style="text-align: center;">CLAW</th>
                <th style="text-align: center;">MH</th>
                <th style="text-align: center;">CF</th>
            `;
        }
        
        secondRow += `<th style="text-align: center;">TOTAL</th></tr>`;
        
        thead.append(firstRow);
        thead.append(secondRow);
    }

    // Fungsi utama untuk menggambar tabel
    function drawTable() {
        const table = $('#dataTable');
        $('.foot-detail-table').empty();
        $('.body-table').empty();
        
        // Bangun header dinamis
        buildDynamicHeader();
        
        var no = 1;
        let row = null;
        
        if (listBarang.data && listBarang.data.length == 0) {
            row = `
                <tr>
                    <td colspan="${5 + (listBarang.thead ? listBarang.thead.length : 0)}" style="text-align: center;">
                        Tidak Ada Barang
                    </td>
                </tr>
            `;
            $('.foot-detail-table').append(row);
        } else {
            var totalPerSpek = {}; // simpan total tiap spek
            var qtyKopekTotal = 0;
            var totalTotal = 0;

            // Proses data
            $.each(listBarang.data, function(i, v) {
                // Hitung total berdasarkan spesifikasi yang ada
                let total = 0;
                if (v.spek) {
                    for (const [key, value] of Object.entries(v.spek)) {
                        total += parseFloat(value || 0);
                    }
                }
                
                var rasio = total == 0.00 ? 0 : ((total / v.qty_sebelum_kopek) * 100).toFixed(2);
                
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align: center;">').html(`${no++}`));
                newRow.append($('<td style="text-align: center;">').text(v.tanggal_masuk));
                newRow.append($('<td style="text-align: center;">').text(v.supplier + ' - ' + v.keterangan));
                newRow.append($('<td>').text(parseFloat(v.qty_sebelum_kopek).toFixed(2)));
                newRow.append($('<td>').text(rasio + ' %'));
                
                // Tambahkan input untuk setiap spesifikasi (dinamis)
                if (listBarang.thead && listBarang.thead.length > 0) {
                    listBarang.thead.forEach(spec => {
                        const value = v.spek && v.spek[spec] ? v.spek[spec] : 0;

                        newRow.append($('<td style="text-align: center;">').html(`
                            <input id="${i+'_'+spec}" 
                                style="height: 40px; padding-bottom: 10px; min-width: 100px;" 
                                class="form-control ${spec.toLowerCase().replace(/\s+/g, '_')}" 
                                oninput="preventNegativeInput(this)" 
                                autocomplete="one-time-code" 
                                type="text" 
                                value="${value}">
                        `));
                        
                        // Akumulasi total spek
                        if (!totalPerSpek[spec]) totalPerSpek[spec] = 0;
                        totalPerSpek[spec] += parseFloat(value || 0);
                    });
                }

                newRow.append($('<td>').text(total.toFixed(3)));
                table.find('tbody').append(newRow);

                totalTotal += total;
                qtyKopekTotal += parseFloat(v.qty_sebelum_kopek);
            });

            var totalRasio = qtyKopekTotal == 0 ? 0 : ((totalTotal / qtyKopekTotal) * 100);

            // GRAND TOTAL (dinamis)
            var newRow = $('<tr class="bg-total">');
            newRow.append($('<td style="text-align: center;">').html("<b>TOTAL</b>"));
            newRow.append($('<td style="text-align: center;">')); // kosong biar balance
            newRow.append($('<td style="text-align: center;">')); // kosong biar balance
            newRow.append($('<td>').text(qtyKopekTotal.toFixed(2)));
            newRow.append($('<td>').text(totalRasio.toFixed(2) + ' %'));
            
            if (listBarang.thead && listBarang.thead.length > 0) {
                listBarang.thead.forEach(spec => {
                    let totalSpec = totalPerSpek[spec] ? totalPerSpek[spec] : 0;
                    newRow.append($('<td>').text(totalSpec.toFixed(2)));
                });
            }
            
            newRow.append($('<td>').text(totalTotal.toFixed(3)));
            table.find('tbody').append(newRow);
        }
        
        // Inisialisasi DataTable
        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().destroy();
        }
        $('#dataTable').DataTable({
            scrollX: true,          // ⬅️ aktifin scroll horizontal
            autoWidth: false,       // ⬅️ biar gak dipaksa balance kolom
            responsive: false,      // ⬅️ jangan pakai responsive kalau mau scrollX
            ordering: false,
            paging: false,
            searching: false,
            info: false
        });

        // ---------- DATA TABLE 2 ----------
        const table2 = $('#dataTable2');
        $('.foot-detail-table-2').empty();
        $('.body-table-2').empty();

        // Definisikan expectedCols2
        const headerSpecs = (listBarang.thead && listBarang.thead.length > 0) ? listBarang.thead : ["JB", "SP LUMP", "BF", "SPL", "CLAW", "MH", "CF"];
        const expectedCols2 = 1 + headerSpecs.length + 1; // 1 kolom deskripsi + n kolom spesifikasi + 1 kolom total

        if (listPerolehanGaji.length == 0) {
            let emptyRow = $('<tr>');
            for (let c = 0; c < expectedCols2; c++) {
                emptyRow.append($('<td>').css('text-align', 'center').text(c === 0 ? 'Tidak Ada Data' : ''));
            }
            $('.body-table-2').append(emptyRow);
        } else {
            var gajiTotal = 0;
            var hargaPerKategori = {}; 
            var vendorData = listDataVendor || {};

            // definisikan specs fix (selalu sama di semua tempat)
            const specs = ["JB", "SP LUMP", "BF", "SPL", "CLAW", "MH", "CF"];

            // init hargaPerKategori
            listPerolehanGaji.forEach(item => {
                hargaPerKategori[item.value] = {};
                if (item.value === 'upah_kopek') {
                    hargaPerKategori[item.value]['JB']      = vendorData.upah_jb || item.jumbo;
                    hargaPerKategori[item.value]['SP LUMP'] = vendorData.upah_xl || item.ex_lump;
                    hargaPerKategori[item.value]['BF']      = vendorData.upah_lp || item.lump;
                    hargaPerKategori[item.value]['SPL']     = vendorData.upah_sp || item.special;
                    hargaPerKategori[item.value]['CLAW']    = vendorData.upah_cl || item.claw;
                    hargaPerKategori[item.value]['MH']      = vendorData.upah_mh || item.mh;
                    hargaPerKategori[item.value]['CF']      = vendorData.upah_cf || item.cf;
                } else if (item.value === 'komisi_kg_daging') {
                    const komisiValue = vendorData.komisi_vendor || item.jumbo;
                    specs.forEach(spec => {
                        hargaPerKategori[item.value][spec] = komisiValue;
                    });
                } else if (item.value === 'bonus_kg_daging') {
                    hargaPerKategori[item.value]['JB']      = vendorData.bonus_jb || item.jumbo;
                    hargaPerKategori[item.value]['SP LUMP'] = vendorData.bonus_xl || item.ex_lump;
                    hargaPerKategori[item.value]['BF']      = vendorData.bonus_lp || item.lump;
                    hargaPerKategori[item.value]['SPL']     = vendorData.bonus_sp || item.special;
                    hargaPerKategori[item.value]['CLAW']    = vendorData.bonus_cl || item.claw;
                    hargaPerKategori[item.value]['MH']      = vendorData.bonus_mh || item.mh;
                    hargaPerKategori[item.value]['CF']      = vendorData.bonus_cf || item.cf;
                } else if (item.value === 'tamb_upah_kopek') {
                    hargaPerKategori[item.value]['JB']      = vendorData.tambahan_upah_kopek_jb || item.jumbo;
                    hargaPerKategori[item.value]['SP LUMP'] = vendorData.tambahan_upah_kopek_xl || item.ex_lump;
                    hargaPerKategori[item.value]['BF']      = vendorData.tambahan_upah_kopek_lp || item.lump;
                    hargaPerKategori[item.value]['SPL']     = vendorData.tambahan_upah_kopek_sp || item.special;
                    hargaPerKategori[item.value]['CLAW']    = vendorData.tambahan_upah_kopek_cl || item.claw;
                    hargaPerKategori[item.value]['MH']      = vendorData.tambahan_upah_kopek_mh || item.mh;
                    hargaPerKategori[item.value]['CF']      = vendorData.tambahan_upah_kopek_cf || item.cf;
                }
            });

            // ---------------- ROW DATA ----------------
            $.each(listPerolehanGaji, function(i, v) {
                var total = 0;
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td>').text(v.description || ''));

                specs.forEach(spec => {
                    const value = (hargaPerKategori[v.value] && hargaPerKategori[v.value][spec]) ? hargaPerKategori[v.value][spec] : 0;
                    newRow.append($('<td style="text-align: center;">').html(`
                        <input id="${i+'_2_'+spec.replace(/\s+/g, '_')}" 
                            class="form-control ${v.value}_${spec.replace(/\s+/g, '_')}" 
                            type="text" 
                            value="${value}">
                    `));
                    total += parseFloat(value || 0);
                });

                newRow.append($('<td>').text(formatRupiah1(total.toFixed(2))));
                fillRowToCols(newRow, expectedCols2);
                table2.find('tbody').append(newRow);
                gajiTotal += total;
            });

            // ---------------- TOTAL PER KATEGORI ----------------
            const totalPerKategori = {};

            Object.keys(hargaPerKategori).forEach(kategori => {
                const kategoriName = listPerolehanGaji.find(item => item.value === kategori)?.description || kategori;
                var newRow = $('<tr style="color:whitesmoke; background-color:#f2c996;">');
                newRow.append($('<td style="text-align: center;">').html(`<b>TOTAL ${kategoriName.toUpperCase()}</b>`));

                let totalKategori = 0;
                specs.forEach(spec => {
                    const harga = parseFloat(hargaPerKategori[kategori][spec] || 0);
                    const qty   = parseFloat(totalPerSpek?.[spec] || 0);
                    const totalSpec = harga * qty;
                    totalKategori += totalSpec;
                    newRow.append($('<td>').text(totalSpec !== 0 ? formatRupiah1(totalSpec.toFixed(2)) : '0'));
                });

                newRow.append($('<td>').text(formatRupiah1(totalKategori.toFixed(2))));
                fillRowToCols(newRow, expectedCols2);
                table2.find('tbody').append(newRow);

                totalPerKategori[kategori] = totalKategori;
            });

            // ---------------- PRESENTASE KOPEK ----------------
            var newRow2 = $('<tr style="color:whitesmoke; background-color:#f2c996;">');
            newRow2.append($('<td style="text-align: center;">').html("<b>PRESENTASE KOPEK</b>"));

            let totalPresentase = 0;
            specs.forEach(spec => {
                const qty = parseFloat(totalPerSpek?.[spec] || 0);

                if (!qty || !totalTotal) {
                    newRow2.append($('<td>').text('0 %'));
                    return;
                }

                const presentase = (qty * 100 / totalTotal);
                totalPresentase += presentase;
                newRow2.append($('<td>').text(presentase.toFixed(2) + ' %'));
            });

            newRow2.append($('<td>').text(totalPresentase.toFixed(2) + ' %'));
            fillRowToCols(newRow2, expectedCols2);
            table2.find('tbody').append(newRow2);

            // ---------------- GRAND TOTAL ----------------
            const grandTotalPerSpek = {};
            specs.forEach(spec => { grandTotalPerSpek[spec] = 0; });

            // jumlahkan semua kategori per spec
            Object.keys(hargaPerKategori).forEach(kategori => {
                specs.forEach(spec => {
                    const harga = parseFloat(hargaPerKategori[kategori][spec] || 0);
                    const qty   = parseFloat(totalPerSpek?.[spec] || 0);
                    grandTotalPerSpek[spec] += (harga * qty);
                });
            });

            const grandTotalUpahKopek = Object.values(grandTotalPerSpek)
                .reduce((sum, value) => sum + value, 0);

            var newRow1 = $('<tr style="color:whitesmoke; background-color:#c7922f; font-weight:bold;">');
            newRow1.append($('<td style="text-align:center;">').html("<b>GRAND TOTAL UPAH KOPEK</b>"));

            specs.forEach(spec => {
                newRow1.append($('<td>').text(formatRupiah1(grandTotalPerSpek[spec].toFixed(2))));
            });

            newRow1.append($('<td>').text(formatRupiah1(grandTotalUpahKopek.toFixed(2))));
            fillRowToCols(newRow1, expectedCols2);
            table2.find('tbody').append(newRow1);

        }

        // ---------- DATA TABLE 3 ----------
        const table3 = $('#dataTable3');
        $('.foot-detail-table-3').empty();
        $('.body-table-3').empty();
        var no = 1;
        var totalBonusResult = 0;
        var vendorData = listDataVendor || {};

        if (listBonus.length == 0) {
            let emptyRow = $('<tr>');
            for (let c=0; c<expectedCols3; c++) {
                emptyRow.append($('<td>').css('text-align','center').text(c===0 ? 'Tidak Ada Data Bonus' : ''));
            }
            $('.body-table-3').append(emptyRow);
        } else {
            $.each(listBonus, function(i, v) {
                // safe assignment
                if (v.spesifikasi === "JB") v.bonus_nominal = vendorData.bonus_karyawan_jb || v.bonus_nominal;
                else if (v.spesifikasi === "SP LUMP") v.bonus_nominal = vendorData.bonus_karyawan_xl || v.bonus_nominal;
                else if (v.spesifikasi === "BF") v.bonus_nominal = vendorData.bonus_karyawan_lp || v.bonus_nominal;
                else if (v.spesifikasi === "SPL") v.bonus_nominal = vendorData.bonus_karyawan_sp || v.bonus_nominal;
                else if (v.spesifikasi === "CLAW") v.bonus_nominal = vendorData.bonus_karyawan_cl || v.bonus_nominal;
                else if (v.spesifikasi === "MH") v.bonus_nominal = vendorData.bonus_karyawan_mh || v.bonus_nominal;
                else if (v.spesifikasi === "CF") v.bonus_nominal = vendorData.bonus_karyawan_cf || v.bonus_nominal;
                
                var totalBonus = parseFloat(v.kg_bonus || 0) * parseFloat(v.bonus_nominal || 0);
                totalBonusResult += totalBonus;

                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align: center;">').html(`${no++}`));
                newRow.append($('<td style="text-align: center;">').text(v.tanggal_masuk));
                newRow.append($('<td style="text-align: center;">').text(v.nama_barang));
                newRow.append($('<td style="text-align: center;">').html(`<input id="${i+'_3_kg'}" class="form-control kg_bonus" ... value="${v.kg_bonus || 0}">`));
                newRow.append($('<td style="text-align: center;">').html(`<input id="${i+'_3_bonus'}" class="form-control bonus_nominal" ... value="${v.bonus_nominal || 0}">`));
                newRow.append($('<td>').text(formatRupiah1(totalBonus.toFixed(2))));
                fillRowToCols(newRow, expectedCols3);
                table3.find('tbody').append(newRow);
            });

            var newRow = $('<tr style="color:whitesmoke; background-color:#f2c996;">');
            // isi 5 td kosong kecuali terakhir berisi grand total
            for (let i=0; i<expectedCols3-1; i++){
                if (i===0) newRow.append($('<td style="text-align:center;">').html("<b>GRAND TOTAL</b>"));
                else newRow.append($('<td>'));
            }
            newRow.append($('<td>').text(formatRupiah1(totalBonusResult.toFixed(2))));
            table3.find('tbody').append(newRow);

            // Recalculate total bonus kalau input Kg / Bonus berubah
            $(document).off("input", ".kg_bonus, .bonus_nominal").on("input", ".kg_bonus, .bonus_nominal", function () {
                var totalBonusResult = 0;

                $("#dataTable3 tbody tr").each(function () {
                    let kg = parseFloat($(this).find(".kg_bonus").val() || 0);
                    let bonus = parseFloat($(this).find(".bonus_nominal").val() || 0);
                    let total = kg * bonus;

                    // update kolom TOTAL di row ini
                    $(this).find("td:last").text(formatRupiah1(total.toFixed(2)));

                    // tambahin ke akumulasi
                    totalBonusResult += total;
                });

                // update GRAND TOTAL (row terakhir)
                $("#dataTable3 tbody tr:last td:last").text(formatRupiah1(totalBonusResult.toFixed(2)));
            });

        }
    }

    // helper: pastikan row punya jumlah kolom yang sama dengan header akhir
    function fillRowToCols($row, expectedCols) {
        let curr = $row.find('td, th').length;
        while (curr < expectedCols) {
            $row.append($('<td>').html('')); // tambahin td kosong
            curr++;
        }
        return $row;
    }

    // hitung berapa kolom final (ambil baris terakhir thead)
    const expectedCols2 = $('#dataTable2 thead tr').last().find('th').length || 9;
    const expectedCols3 = $('#dataTable3 thead tr').last().find('th').length || 6;


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

    function formatRupiah1(angka) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(angka);

    }

    const print = function(url) {
        window.open(url, "_blank");
    }


    const posting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Biaya Kepiting ?',
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
                    url: "<?= base_url("biaya-kepiting/posting"); ?>",
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
                                window.location.href = "<?= base_url("biaya-kepiting") ?>";
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
            title: 'Hapus Biaya Kepiting ?',
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
                    url: "<?= base_url("biaya-kepiting/delete"); ?>",
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
                                location.reload();
                            });
                        }
                    },
                });
            }
        })
    }

    function autoComplete() {
        $.each(listBarang, function(i, v) {
            var barangJumboElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].jumbo');
            var barangExLumpElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].ex_lump');
            var barangLumpElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].lump');
            var barangSpecialElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].special');
            var barangClawElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].claw');
            var barangMhElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].mh');
            var barangCfElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].cf');

            listBarang[i].jumbo = barangJumboElement.val();
            listBarang[i].ex_lump = barangExLumpElement.val();
            listBarang[i].lump = barangLumpElement.val();
            listBarang[i].special = barangSpecialElement.val();
            listBarang[i].claw = barangClawElement.val();
            listBarang[i].mh = barangMhElement.val();
            listBarang[i].cf = barangCfElement.val();
        });

        $.each(listPerolehanGaji, function(i, v) {
            var hargaJumboElement = $('input.' + v.value + '_jumbo');
            var hargaExLumpElement = $('input.' + v.value + '_ex_lump');
            var hargaLumpElement = $('input.' + v.value + '_lump');
            var hargaSpecialElement = $('input.' + v.value + '_special');
            var hargaClawElement = $('input.' + v.value + '_claw');
            var hargaMhElement = $('input.' + v.value + '_mh');
            var hargaCfElement = $('input.' + v.value + '_cf');

            listPerolehanGaji[i].jumbo = hargaJumboElement.val();
            listPerolehanGaji[i].ex_lump = hargaExLumpElement.val();
            listPerolehanGaji[i].lump = hargaLumpElement.val();
            listPerolehanGaji[i].special = hargaSpecialElement.val();
            listPerolehanGaji[i].claw = hargaClawElement.val();
            listPerolehanGaji[i].mh = hargaMhElement.val();
            listPerolehanGaji[i].cf = hargaCfElement.val();
        });

        $.each(listBonus, function(i, v) {
            var barangBonusKgElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].kg_bonus');
            var barangBonusNominalElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].bonus_nominal');

            listBonus[i].kg_bonus = barangBonusKgElement.val();
            listBonus[i].bonus_nominal = barangBonusNominalElement.val();
        });

        let data = new FormData();
        data.append('listBarang', JSON.stringify(listBarang));
        data.append('listPerolehanGaji', JSON.stringify(listPerolehanGaji));
        data.append("listBonus", JSON.stringify(listBonus));
        data.append("listDataVendor", JSON.stringify(listDataVendor));

        if (request) {
            request.abort();
        }

        var focusedElementId = document.activeElement.id;
        request = $.ajax({
            url: "<?= base_url('biaya-kepiting/autocomplete'); ?>",
            data: data,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            method: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            success: function(res) {
                csrf.val(res.token);
                listBarang = res.data;
                listPerolehanGaji = res.dataPerolehanGaji;
                listBonus = res.dataBonus;
                listDataVendor = res.dataVendor;
                drawTable();

                if (focusedElementId) {
                    var newFocusedElement = document.getElementById(focusedElementId);
                    if (newFocusedElement) {
                        newFocusedElement.focus();
                        var val = newFocusedElement.value;
                        newFocusedElement.value = '';
                        newFocusedElement.value = val;
                    }
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (textStatus !== 'abort') {
                    // Handle error selain abort
                    console.error('Error:', textStatus, errorThrown);
                }
            }
        });
    }





    // Fungsi untuk mengumpulkan data dan submit
function submitBiayaKepiting() {
    // Validasi dasar
    if (!validateForm()) {
        return;
    }

    // Kumpulkan data dari semua tabel
    const data = collectAllData();
    
    // Tampilkan konfirmasi sebelum submit
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin menyimpan data biaya kepiting ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Simpan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Kirim data ke server
            sendDataToServer(data);
        }
    });
}

// Fungsi validasi form
function validateForm() {
    const noPembayaran = $('#no_pembayaran').val();
    const tanggal = $('#tanggal').val();
    const jasaVendorInId = $('#jasa_vendor_in_id').val();
    
    if (!noPembayaran) {
        Swal.fire('Peringatan', 'No. Pembayaran harus diisi', 'warning');
        return false;
    }
    
    if (!tanggal) {
        Swal.fire('Peringatan', 'Tanggal harus diisi', 'warning');
        return false;
    }
    
    if (!jasaVendorInId) {
        Swal.fire('Peringatan', 'Jasa Vendor harus dipilih', 'warning');
        return false;
    }
    
    // Validasi data barang
    if (!listBarang.data || listBarang.data.length === 0) {
        Swal.fire('Peringatan', 'Data barang tidak boleh kosong', 'warning');
        return false;
    }
    
    return true;
}

// Fungsi untuk mengumpulkan semua data dari tabel
function collectAllData() {
    return {
        listBarang: collectBarangData(),
        listPerolehanGaji: collectGajiData(),
        listBonus: collectBonusData(),
        no_pembayaran: $('#no_pembayaran').val(),
        tanggal: $('#tanggal').val(),
        keterangan: $('#keterangan').val(),
        jasa_vendor_in_id: $('#jasa_vendor_in_id').val()
    };
}

// Fungsi untuk mengumpulkan data barang dari tabel pertama
function collectBarangData() {
    const data = [];
    
    // Loop melalui setiap baris data (kecuali baris total)
    $('#dataTable tbody tr').not('.bg-total').each(function() {
        const row = $(this);
        // Skip baris kosong
        if (row.find('td').first().text().includes('Tidak Ada Barang')) {
            return;
        }
        
        // Ambil data dari kolom tetap
        const barangData = {
            tanggal_masuk: row.find('td:eq(1)').text(),
            supplier_keterangan: row.find('td:eq(2)').text(),
            qty_sebelum_kopek: parseFloat(row.find('td:eq(3)').text()) || 0,
            rasio: parseFloat(row.find('td:eq(4)').text()) || 0,
            spek: {}
        };
        
        // Ambil data spesifikasi dinamis
        if (listBarang.thead && listBarang.thead.length > 0) {
            listBarang.thead.forEach((spec, index) => {
                const inputValue = row.find(`td:eq(${5 + index}) input`).val();
                barangData.spek[spec] = parseFloat(inputValue) || 0;
            });
        }
        
        // Ambil ID dari data asli jika ada
        const rowIndex = row.index();
        if (listBarang.data && listBarang.data[rowIndex]) {
            barangData.barang_master_id = listBarang.data[rowIndex].barang_master_id;
            barangData.barang_master_spesifikasi_id = listBarang.data[rowIndex].barang_master_spesifikasi_id;
        }
        
        data.push(barangData);
    });
    
    return data;
}

// Fungsi untuk mengumpulkan data gaji dari tabel kedua
function collectGajiData() {
    const data = [];
    const specs = ["JB", "SP LUMP", "BF", "SPL", "CLAW", "MH", "CF"];
    
    // Loop melalui setiap baris data (kecuali baris total)
    $('#dataTable2 tbody tr').each(function() {
        const row = $(this);
        const firstCell = row.find('td:first').text().trim();
        
        // Skip baris total dan baris kosong
        if (firstCell.includes('TOTAL') || firstCell.includes('PRESENTASE') || 
            firstCell.includes('GRAND TOTAL') || firstCell === 'Tidak Ada Data') {
            return;
        }
        
        // Ambil data gaji
        const gajiData = {
            description: firstCell,
            value: getValueFromDescription(firstCell), // Helper function untuk mapping
            jumbo: 0,
            ex_lump: 0,
            lump: 0,
            special: 0,
            claw: 0,
            mh: 0,
            cf: 0
        };
        
        // Ambil nilai untuk setiap spesifikasi
        specs.forEach((spec, index) => {
            const inputValue = row.find(`td:eq(${index + 1}) input`).val();
            const value = parseFloat(inputValue) || 0;
            
            // Assign ke property yang sesuai
            switch(spec) {
                case "JB": gajiData.jumbo = value; break;
                case "SP LUMP": gajiData.ex_lump = value; break;
                case "BF": gajiData.lump = value; break;
                case "SPL": gajiData.special = value; break;
                case "CLAW": gajiData.claw = value; break;
                case "MH": gajiData.mh = value; break;
                case "CF": gajiData.cf = value; break;
            }
        });
        
        data.push(gajiData);
    });
    
    return data;
}

// Helper function untuk mapping description ke value
function getValueFromDescription(description) {
    const mapping = {
        'Upah Kopek': 'upah_kopek',
        'Komisi per Kg Daging': 'komisi_kg_daging',
        'Bonus per Kg Daging': 'bonus_kg_daging',
        'Tambahan Upah Kopek': 'tamb_upah_kopek'
    };
    
    return mapping[description] || description.toLowerCase().replace(/\s+/g, '_');
}

// Fungsi untuk mengumpulkan data bonus dari tabel ketiga
function collectBonusData() {
    const data = [];
    
    // Loop melalui setiap baris data (kecuali baris total)
    $('#dataTable3 tbody tr').not(':last').each(function() {
        const row = $(this);
        
        // Skip baris kosong
        if (row.find('td').first().text().includes('Tidak Ada Data Bonus')) {
            return;
        }
        
        // Ambil data bonus
        const bonusData = {
            tanggal_masuk: row.find('td:eq(1)').text(),
            nama_barang: row.find('td:eq(2)').text(),
            kg_bonus: parseFloat(row.find('td:eq(3) input').val()) || 0,
            bonus_nominal: parseFloat(row.find('td:eq(4) input').val()) || 0
        };
        
        // Ambil ID dari data asli jika ada
        const rowIndex = row.index();
        if (listBonus && listBonus[rowIndex]) {
            bonusData.barang_master_id = listBonus[rowIndex].barang_master_id;
            bonusData.barang_master_spesifikasi_id = listBonus[rowIndex].barang_master_spesifikasi_id;
            bonusData.spesifikasi = listBonus[rowIndex].spesifikasi;
        }
        
        data.push(bonusData);
    });
    
    return data;
}

// Fungsi untuk mengirim data ke server
function sendDataToServer(data) {
    // Tampilkan loading
    Swal.fire({
        title: 'Menyimpan Data',
        text: 'Sedang memproses data, harap tunggu...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Kirim data ke server menggunakan AJAX
    $.ajax({
        url: '<?= base_url("biaya-kepiting/save"); ?>', // Ganti dengan URL yang sesuai
        type: 'POST',
        dataType: 'json',
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
        },
        data: {
            listBarang: JSON.stringify(data.listBarang),
            listPerolehanGaji: JSON.stringify(data.listPerolehanGaji),
            listBonus: JSON.stringify(data.listBonus),
            no_pembayaran: data.no_pembayaran,
            tanggal: data.tanggal,
            keterangan: data.keterangan,
            jasa_vendor_in_id: data.jasa_vendor_in_id,
        },
        success: function(response) {
            Swal.close();
            
            if (response.status) {
                // Berhasil
                Swal.fire({
                    title: 'Sukses',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Redirect atau lakukan sesuatu setelah sukses
                    window.location.href = '/biaya-kepiting/edit/' + response.id;
                });
            } else {
                // Gagal
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data: ' + error, 'error');
        }
    });
}

// Event handler untuk tombol submit
$(document).on('click', '.btn-submit-parent', function(e) {
    e.preventDefault();
    submitBiayaKepiting();
});

</script>

<?= $this->endSection(); ?>