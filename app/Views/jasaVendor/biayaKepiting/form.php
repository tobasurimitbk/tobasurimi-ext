<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($biayaKepiting) ? "Tambah Biaya Kepiting" : "Update Biaya Kepiting" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("biaya-kepiting"); ?>">
                Batal
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
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($biayaKepiting) ? 'disabled' : '' ?> class="form-select jasa_vendor_in_id" id="jasa_vendor_in_id" name="jasa_vendor_in_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($jasaVendorIn)) : ?>
                                    <?php foreach ($jasaVendorIn as $j) : ?>
                                        <option data-warehouse_id="<?= $j['warehouse_id'] ?>" data-vendor="<?= strtoupper($j['name']) ?>" data-divisi="<?= strtoupper($j['divisi']) ?>" value="<?= $j['id'] ?>">
                                            <?= $j['no_penerimaan_surat_jalan'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <option selected value="<?= $jasaVendorInDetail['id'] ?>">
                                        <?= $jasaVendorInDetail['no_penerimaan_surat_jalan'] ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih No Surat Jalan</label>
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
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" border="1" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;" colspan="3"></th>
                                    <th style="text-align: center;" colspan="2">Kg Bahan Baku</th>
                                    <th style="text-align: center;" colspan="8"></th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">Tanggal Masuk</th>
                                    <th style="text-align: center;">Barang</th>


                                    <th style="text-align: center;">Qty Sebelum Kopek</th>
                                    <th style="text-align: center;">Rasio (%)</th>

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
        </div>

    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    var listBarang = [];
    var listPerolehanGaji = [];


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
        listDataBarang();
        changeStatus();
    });

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

    $('.btn-submit-parent').click(function() {
        if (listBarang.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Barang masuk tidak boleh kosong !',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        } else {
            if ($('.create-form').valid()) {
                // VALIDASI FORM 1
                var isValidBarangJumbo = true;
                var dataErrorBarangJumbo = null;

                var isValidBarangExLump = true;
                var dataErrorBarangExLump = null;

                var isValidBarangLump = true;
                var dataErrorBarangLump = null;

                var isValidBarangSpecial = true;
                var dataErrorBarangSpecial = null;

                var isValidBarangClaw = true;
                var dataErrorBarangClaw = null;

                var isValidBarangMh = true;
                var dataErrorBarangMh = null;

                var isValidBarangCf = true;
                var dataErrorBarangCf = null;

                $.each(listBarang, function(i, v) {
                    var barangJumboElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].jumbo');
                    var barangExLumpElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].ex_lump');
                    var barangLumpElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].lump');
                    var barangSpecialElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].special');
                    var barangClawElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].claw');
                    var barangMhElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].mh');
                    var barangCfElement = $('input[data-spesifikasi_id="' + v.barang_master_spesifikasi_id + '"].cf');

                    if (barangJumboElement.val() === undefined || barangJumboElement.val() === '') {
                        isValidBarangJumbo = false;
                        dataErrorBarangJumbo = listBarang[i];
                    } else {
                        listBarang[i].jumbo = barangJumboElement.val();
                    }

                    if (barangExLumpElement.val() === undefined || barangExLumpElement.val() === '') {
                        isValidBarangExLump = false;
                        dataErrorBarangExLump = listBarang[i];
                    } else {
                        listBarang[i].ex_lump = barangExLumpElement.val();
                    }

                    if (barangLumpElement.val() === undefined || barangLumpElement.val() === '') {
                        isValidBarangLump = false;
                        dataErrorBarangLump = listBarang[i];
                    } else {
                        listBarang[i].lump = barangLumpElement.val();
                    }

                    if (barangSpecialElement.val() === undefined || barangSpecialElement.val() === '') {
                        isValidBarangSpecial = false;
                        dataErrorBarangSpecial = listBarang[i];
                    } else {
                        listBarang[i].special = barangSpecialElement.val();
                    }

                    if (barangClawElement.val() === undefined || barangClawElement.val() === '') {
                        isValidBarangClaw = false;
                        dataErrorBarangClaw = listBarang[i];
                    } else {
                        listBarang[i].claw = barangClawElement.val();
                    }

                    if (barangMhElement.val() === undefined || barangMhElement.val() === '') {
                        isValidBarangMh = false;
                        dataErrorBarangMh = listBarang[i];
                    } else {
                        listBarang[i].mh = barangMhElement.val();
                    }

                    if (barangCfElement.val() === undefined || barangCfElement.val() === '') {
                        isValidBarangCf = false;
                        dataErrorBarangCf = listBarang[i];
                    } else {
                        listBarang[i].cf = barangCfElement.val();
                    }
                });
            }

            // VALIDASI FORM 2
            var isValidPerolehanGaji = true;

            $.each(listPerolehanGaji, function(i, v) {
                var hargaJumboElement = $('input.' + v.value + '_jumbo');
                var hargaExLumpElement = $('input.' + v.value + '_ex_lump');
                var hargaLumpElement = $('input.' + v.value + '_lump');
                var hargaSpecialElement = $('input.' + v.value + '_special');
                var hargaClawElement = $('input.' + v.value + '_claw');
                var hargaMhElement = $('input.' + v.value + '_mh');
                var hargaCfElement = $('input.' + v.value + '_cf');

                if (hargaJumboElement.val() === undefined || hargaJumboElement.val() === '') {
                    isValidPerolehanGaji = false;
                    Swal.fire({
                        icon: 'error',
                        title: v.description + ' Jumbo tidak valid !',
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                } else {
                    listPerolehanGaji[i].jumbo = hargaJumboElement.val();
                }

                if (hargaExLumpElement.val() === undefined || hargaExLumpElement.val() === '') {
                    isValidPerolehanGaji = false;
                    Swal.fire({
                        icon: 'error',
                        title: v.description + ' Ex Lump tidak valid !',
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                } else {
                    listPerolehanGaji[i].ex_lump = hargaExLumpElement.val();
                }

                if (hargaLumpElement.val() === undefined || hargaLumpElement.val() === '') {
                    isValidPerolehanGaji = false;
                    Swal.fire({
                        icon: 'error',
                        title: v.description + ' Lump tidak valid !',
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                } else {
                    listPerolehanGaji[i].lump = hargaLumpElement.val();
                }

                if (hargaSpecialElement.val() === undefined || hargaSpecialElement.val() === '') {
                    isValidPerolehanGaji = false;
                    Swal.fire({
                        icon: 'error',
                        title: v.description + ' Special tidak valid !',
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                } else {
                    listPerolehanGaji[i].special = hargaSpecialElement.val();
                }

                if (hargaClawElement.val() === undefined || hargaClawElement.val() === '') {
                    isValidPerolehanGaji = false;
                    Swal.fire({
                        icon: 'error',
                        title: v.description + ' Claw tidak valid !',
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                } else {
                    listPerolehanGaji[i].claw = hargaClawElement.val();
                }

                if (hargaMhElement.val() === undefined || hargaMhElement.val() === '') {
                    isValidPerolehanGaji = false;
                    Swal.fire({
                        icon: 'error',
                        title: v.description + ' Mh tidak valid !',
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                } else {
                    listPerolehanGaji[i].mh = hargaMhElement.val();
                }

                if (hargaCfElement.val() === undefined || hargaCfElement.val() === '') {
                    isValidPerolehanGaji = false;
                    Swal.fire({
                        icon: 'error',
                        title: v.description + ' Cf tidak valid !',
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                } else {
                    listPerolehanGaji[i].cf = hargaCfElement.val();
                }
            });


            // ALERT FORM 1
            if (!isValidBarangJumbo) {
                Swal.fire({
                    icon: 'error',
                    title: dataErrorBarangJumbo.nama_barang + ' Jumbo tidak valid !',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            } else if (!isValidBarangExLump) {
                Swal.fire({
                    icon: 'error',
                    title: dataErrorBarangExLump.nama_barang + ' Ex Lump tidak valid !',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            } else if (!isValidBarangLump) {
                Swal.fire({
                    icon: 'error',
                    title: dataErrorBarangLump.nama_barang + ' Lump tidak valid !',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            } else if (!isValidBarangSpecial) {
                Swal.fire({
                    icon: 'error',
                    title: dataErrorBarangSpecial.nama_barang + ' Special tidak valid !',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            } else if (!isValidBarangClaw) {
                Swal.fire({
                    icon: 'error',
                    title: dataErrorBarangClaw.nama_barang + ' Claw tidak valid !',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            } else if (!isValidBarangMh) {
                Swal.fire({
                    icon: 'error',
                    title: dataErrorBarangMh.nama_barang + ' Mh tidak valid !',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            } else if (!isValidBarangCf) {
                Swal.fire({
                    icon: 'error',
                    title: dataErrorBarangCf.nama_barang + ' Cf tidak valid !',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            } else if (!isValidPerolehanGaji) {
                console.log("Validasi perolehan gaji");
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
                        data.append('listPerolehanGaji', JSON.stringify(listPerolehanGaji));

                        if (id) {
                            // UPDATE
                            $.ajax({
                                url: "<?= base_url("biaya-kepiting/update"); ?>",
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
                                                location.reload();
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
                                url: "<?= base_url("biaya-kepiting/save"); ?>",
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
                                                window.location.href = "<?= base_url('biaya-kepiting/id/') ?>" + response.id
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
    });


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
                        $(".no_pembayaran").val(res?.data);
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
                drawTable();
            }
        });
    }

    function drawTable() {
        const table = $('#dataTable');
        $('.foot-detail-table').empty();
        $('.body-table').empty();
        var no = 1;

        if (listBarang.length == 0) {
            row += `
                    <tr>
                        <td colspan="13" style="text-align: center;">
                            Tidak Ada Barang
                        </td>
                    </tr>
                `;
            $('.foot-detail-table').append(row);
        } else {

            var qtyKopekTotal = 0;
            var jumboTotal = 0;
            var exLumpTotal = 0;
            var lumpTotal = 0;
            var specialTotal = 0;
            var clawTotal = 0;
            var mhTotal = 0;
            var cfTotal = 0;
            var totalTotal = 0;
            var totalRasio = 0;

            // DATATABLE 1
            $.each(listBarang, function(i, v) {
                var total = parseFloat(v.jumbo) + parseFloat(v.ex_lump) + parseFloat(v.lump) + parseFloat(v.special) + parseFloat(v.claw) + parseFloat(v.mh) + parseFloat(v.cf)
                var rasio = total == 0.00 ? 0 : ((v.qty_kopek / total) * 10).toFixed(2);
                var newRow = $('<tr  style="color:whitesmoke;">');
                newRow.append($('<td style="text-align: center;">').html(
                    `
                            ${no++} 
                        `
                ));
                newRow.append($('<td style="text-align: center;">').text(v.tanggal_masuk));
                newRow.append($('<td style="text-align: center;">').text(v.nama_barang));
                newRow.append($('<td>').text(v.qty_kopek));
                newRow.append($('<td>').text(rasio + ' %'));
                newRow.append($('<td style="text-align: center;">').html(
                    `
                            <input <?= !empty($biayaKepiting) ? (($biayaKepiting['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control jumbo" oninput="preventNegativeInput(this)" data-spesifikasi_id="${v.barang_master_spesifikasi_id}"  autocomplete="one-time-code" class="form-control jumbo" type="text" value="${v.jumbo}">
                        `
                ));
                newRow.append($('<td style="text-align: center;">').html(
                    `
                            <input <?= !empty($biayaKepiting) ? (($biayaKepiting['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control ex_lump" oninput="preventNegativeInput(this)" data-spesifikasi_id="${v.barang_master_spesifikasi_id}" autocomplete="one-time-code" class="form-control ex_lump" type="text" value="${v.ex_lump}">
                        `
                ));
                newRow.append($('<td style="text-align: center;">').html(
                    `
                            <input <?= !empty($biayaKepiting) ? (($biayaKepiting['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control lump" oninput="preventNegativeInput(this)" data-spesifikasi_id="${v.barang_master_spesifikasi_id}" autocomplete="one-time-code" class="form-control lump" type="text" value="${v.lump}">
                        `
                ));
                newRow.append($('<td style="text-align: center;">').html(
                    `
                            <input <?= !empty($biayaKepiting) ? (($biayaKepiting['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control special" oninput="preventNegativeInput(this)" data-spesifikasi_id="${v.barang_master_spesifikasi_id}" autocomplete="one-time-code" class="form-control special" type="text" value="${v.special}">
                        `
                ));
                newRow.append($('<td style="text-align: center;">').html(
                    `
                            <input <?= !empty($biayaKepiting) ? (($biayaKepiting['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control claw" oninput="preventNegativeInput(this)" data-spesifikasi_id="${v.barang_master_spesifikasi_id}" autocomplete="one-time-code" class="form-control claw" type="text" value="${v.claw}">
                        `
                ));
                newRow.append($('<td style="text-align: center;">').html(
                    `
                            <input <?= !empty($biayaKepiting) ? (($biayaKepiting['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control mh" oninput="preventNegativeInput(this)" data-spesifikasi_id="${v.barang_master_spesifikasi_id}" autocomplete="one-time-code" class="form-control mh" type="text" value="${v.mh}">
                        `
                ));
                newRow.append($('<td style="text-align: center;">').html(
                    `
                            <input <?= !empty($biayaKepiting) ? (($biayaKepiting['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control cf" oninput="preventNegativeInput(this)" data-spesifikasi_id="${v.barang_master_spesifikasi_id}" autocomplete="one-time-code" class="form-control cf" type="text" value="${v.cf}">
                        `
                ));

                newRow.append($('<td>').text(total.toFixed(2)));
                table.find('tbody').append(newRow);

                jumboTotal += parseFloat(v.jumbo);
                exLumpTotal += parseFloat(v.ex_lump);
                lumpTotal += parseFloat(v.lump);
                specialTotal += parseFloat(v.special);
                clawTotal += parseFloat(v.claw);
                mhTotal += parseFloat(v.mh);
                cfTotal += parseFloat(v.cf);
                totalTotal += total;
                qtyKopekTotal += parseFloat(v.qty_kopek);
                totalRasio += parseFloat(rasio);

            });

            // GRAND TOTAL 1
            var newRow = $('<tr style="color:whitesmoke; background-color:#f2c996;">');
            newRow.append($('<td style="text-align: center;" colspan="3">').html("<b>TOTAL</b>"));
            newRow.append($('<td>').text(qtyKopekTotal.toFixed(2)));
            newRow.append($('<td>').text(totalRasio.toFixed(2) + ' %'));
            newRow.append($('<td>').text(jumboTotal.toFixed(2)));
            newRow.append($('<td>').text(exLumpTotal.toFixed(2)));
            newRow.append($('<td>').text(lumpTotal.toFixed(2)));
            newRow.append($('<td>').text(specialTotal.toFixed(2)));
            newRow.append($('<td>').text(clawTotal.toFixed(2)));
            newRow.append($('<td>').text(mhTotal.toFixed(2)));
            newRow.append($('<td>').text(cfTotal.toFixed(2)));
            newRow.append($('<td>').text(totalTotal.toFixed(2)));
            table.find('tbody').append(newRow);

            // DATATABLE 2
            const table2 = $('#dataTable2');
            $('.foot-detail-table-2').empty();
            $('.body-table-2').empty();

            if (listPerolehanGaji.length == 0) {
                row2 += `
                    <tr>
                        <td colspan="9" style="text-align: center;">
                            Tidak Ada Barang
                        </td>
                    </tr>
                `;
                $('.foot-detail-table-2').append(row2);
            } else {
                var gajiTotal = 0;

                $.each(listPerolehanGaji, function(i, v) {
                    var total = parseFloat(v.jumbo) + parseFloat(v.ex_lump) + parseFloat(v.lump) + parseFloat(v.special) + parseFloat(v.claw) + parseFloat(v.mh) + parseFloat(v.cf);
                    gajiTotal += total;

                    var newRow = $('<tr  style="color:whitesmoke;">');
                    newRow.append($('<td>').text(v.description));
                    newRow.append($('<td style="text-align: center;">').html(
                        `
                            <input <?= !empty($biayaKepiting) ? (($biayaKepiting['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control ${v.value}_jumbo" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control ${v.value}_jumbo" type="text" value="${v.jumbo}">
                        `
                    ));
                    newRow.append($('<td style="text-align: center;">').html(
                        `
                            <input <?= !empty($biayaKepiting) ? (($biayaKepiting['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control ${v.value}_ex_lump" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control ${v.value}_ex_lump" type="text" value="${v.ex_lump}">
                        `
                    ));
                    newRow.append($('<td style="text-align: center;">').html(
                        `
                            <input <?= !empty($biayaKepiting) ? (($biayaKepiting['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control ${v.value}_lump" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control ${v.value}_lump" type="text" value="${v.lump}">
                        `
                    ));
                    newRow.append($('<td style="text-align: center;">').html(
                        `
                            <input <?= !empty($biayaKepiting) ? (($biayaKepiting['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control ${v.value}_special" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control ${v.value}_special" type="text" value="${v.special}">
                        `
                    ));
                    newRow.append($('<td style="text-align: center;">').html(
                        `
                            <input <?= !empty($biayaKepiting) ? (($biayaKepiting['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control ${v.value}_claw" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control ${v.value}_claw" type="text" value="${v.claw}">
                        `
                    ));
                    newRow.append($('<td style="text-align: center;">').html(
                        `
                            <input <?= !empty($biayaKepiting) ? (($biayaKepiting['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control ${v.value}_mh" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control ${v.value}_mh" type="text" value="${v.mh}">
                        `
                    ));
                    newRow.append($('<td style="text-align: center;">').html(
                        `
                            <input <?= !empty($biayaKepiting) ? (($biayaKepiting['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control ${v.value}_cf" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control ${v.value}_cf" type="text" value="${v.cf}">
                        `
                    ));
                    newRow.append($('<td>').text(total.toFixed(2)));
                    table2.find('tbody').append(newRow);



                });

                var newRow2 = $('<tr style="color:whitesmoke; background-color:#f2c996;">');
                newRow2.append($('<td style="text-align: center;">').html("<b>PRESENTASE KOPEK</b>"));
                newRow2.append($('<td>').text((jumboTotal != 0 ? (jumboTotal * 100 / totalTotal).toFixed(2) : '0') + ' %'));
                newRow2.append($('<td>').text((exLumpTotal != 0 ? (exLumpTotal * 100 / totalTotal).toFixed(2) : '0') + ' %'));
                newRow2.append($('<td>').text((lumpTotal != 0 ? (lumpTotal * 100 / totalTotal).toFixed(2) : '0') + ' %'));
                newRow2.append($('<td>').text((specialTotal != 0 ? (specialTotal * 100 / totalTotal).toFixed(2) : '0') + ' %'));
                newRow2.append($('<td>').text((clawTotal != 0 ? (clawTotal * 100 / totalTotal).toFixed(2) : '0') + ' %'));
                newRow2.append($('<td>').text((mhTotal != 0 ? (mhTotal * 100 / totalTotal).toFixed(2) : '0') + ' %'));
                newRow2.append($('<td>').text((cfTotal != 0 ? (cfTotal * 100 / totalTotal).toFixed(2) : '0') + ' %'));

                newRow2.append($('<td>').text('100%'));
                table2.find('tbody').append(newRow2);

                // GRAND TOTAL 2    
                var newRow1 = $('<tr style="color:whitesmoke; background-color:#f2c996;">');
                newRow1.append($('<td style="text-align: center;" colspan="8">').html("<b>GRAND TOTAL UPAH KOPEK</b>"));
                newRow1.append($('<td>').text(gajiTotal.toFixed(2)));
                table2.find('tbody').append(newRow1);
            }
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
            cancelButtonText: 'Batal',
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
                                location.reload()
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
            cancelButtonText: 'Batal',
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
</script>

<?= $this->endSection(); ?>