<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($transaksiJurnal) ? "Update" : "Tambah" ?> Jurnal Umum</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("jurnal"); ?>">
                Kembali
            </a>
            <?php if (!empty($transaksiJurnal)) : ?>
                <?php if (can('Accounting', 'Jurnal', 'p')) : ?>
                    <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("jurnal/print/"); ?><?= encrypt($transaksiJurnal['id']); ?>')">
                        Print
                    </button>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (!empty($transaksiJurnal)) : ?>
                <?php if ($tutupBuku == 0) : ?>
                    <?php if (can('Accounting', 'Jurnal', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="destroy('<?= encrypt($transaksiJurnal['id']) ?>')">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Accounting', 'Jurnal', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent" id="btn_submit_parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else : ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent" id="btn_submit_parent">
                    Simpan
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">

            <form class="create-form form-add-spp create-parent" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($transaksiJurnal) ? encrypt($transaksiJurnal['id']) : ""; ?>" />
                <?= csrf_field() ?>
                <div class="row mb-1">
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold mb-3" style="font-size: 14px;">Header Transaksi</label>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= isset($tutupBuku) ? ($tutupBuku ? 'readonly' : '') : '' ?> <?= isset($transaksiJurnal) ?: 'readonly' ?> autocomplete="one-time-code" value="<?= !empty($transaksiJurnal) ? $transaksiJurnal['no_bukti'] : "" ?>" type="text" class="form-control no_bukti" id="no_bukti" name="no_bukti" placeholder="No Bukti">
                                    <label for="floatingInput">No Bukti</label>
                                </div>
                                <div class="input-generate input-group-prepend group-prepend-password align-items-center" style="<?= isset($transaksiJurnal) ? "display:none;" : '' ?>">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="generateNewCode()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= isset($tutupBuku) ? ($tutupBuku ? 'readonly' : '') : '' ?> autocomplete="one-time-code" value="<?= !empty($transaksiJurnal) ?  date('d/m/Y', strtotime($transaksiJurnal['tanggal_transaksi'])) : date('d/m/Y'); ?>" type="text" class="form-control tanggal_transaksi" name="tanggal_transaksi" id="tanggal_transaksi" placeholder="">
                                <label for="floatingInput">Tanggal Transaksi</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 0px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= isset($tutupBuku) ? ($tutupBuku ? 'disabled' : '') : '' ?> class="form-select divisi_id" id="divisi_id" name="divisi_id">
                                <option value="ALL">ALL</option>
                                <?php foreach ($divisi as $d): ?>
                                    <option <?= isset($divisiId) ? ($divisiId == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>"><?= $d['divisi'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= isset($tutupBuku) ? ($tutupBuku ? 'disabled' : '') : '' ?> class="form-select type_transaksi" id="type_transaksi" name="type_transaksi" onchange="generateNewCode()">
                                <option value=""></option>
                                <?php foreach ($tipeTransaksi as $t): ?>
                                    <option <?= isset($transaksiJurnal) ? ($transaksiJurnal['type_transaksi'] == $t['id'] ? 'selected' : '') : '' ?> value="<?= $t['id'] ?>"><?= $t['value'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Tipe Transaksi</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= isset($tutupBuku) ? ($tutupBuku ? 'readonly' : '') : '' ?> autocomplete="one-time-code" value="<?= !empty($transaksiJurnal) ? $transaksiJurnal['uraian_transaksi'] : ""; ?>" type="text" class="form-control uraian_transaksi" id="uraian_transaksi" name="uraian_transaksi" placeholder="Nomor Invoice">
                            <label for="floatingInput">Uraian Transaksi</label>
                        </div>
                    </div>

                </div>

            </form>

            <form class="craete-child" id="create-child" style="<?= isset($tutupBuku) ? ($tutupBuku ? "display:none;" : '') : '' ?>">
                <input type="hidden" name="id_detail" id="id_detail">
                <div class="row mb-1">
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold mb-3" style="font-size: 14px;">Detail Transaksi</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select jenis_transaksi" id="jenis_transaksi" name="jenis_transaksi">
                                <option value="debit">DEBIT</option>
                                <option value="kredit">KREDIT</option>
                            </select>
                            <label for="floatingInput">Jenis Transaksi</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control keterangan" id="keterangan" name="keterangan" placeholder="Keterangan">
                            <label for="floatingInput">Uraian (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select id_coa" id="id_coa" name="id_coa">
                                <option value=""></option>
                                <?php foreach ($subAkun as $s): ?>
                                    <option data-nama_sub="<?= $s->nama_sub ?>" data-no_sub="<?= $s->no_sub ?>" value="<?= $s->id ?>"><?= $s->no_sub . " " . $s->nama_sub ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pilih Akun</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select valas_id" id="valas_id" name="valas_id">
                                <option value=""></option>
                                <?php foreach ($valuta as $v): ?>
                                    <option <?= trim($v['value']) == "IDR" ? 'selected' : '' ?> data-valas="<?= $v['value'] ?>" value="<?= $v['id'] ?>"><?= $v['value'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Valas</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control jumlah" onkeyup="this.value = greatFormatRupiah(this.value)" id="jumlah" name="jumlah" placeholder="Jumlah Transaksi">
                            <label for="floatingInput">Jumlah Transaksi</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" value="1" class="form-control kurs" onkeyup="this.value = greatFormatRupiah(this.value)" id="kurs" name="kurs" placeholder="Kurs">
                            <label for="floatingInput">Kurs</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" onkeyup="this.value = greatFormatRupiah(this.value)" class="form-control jumlah_idr" id="jumlah_idr" name="jumlah_idr" placeholder="Jumlah IDR">
                                <label for="floatingInput">Jumlah IDR</label>
                            </div>
                            <div class="input-group-append" style="height:50px;">
                                <button class="btn btn-primary" type="button" id="btn_submit_child" style="background-color: #E22C37;">
                                    <i class="fas fa-plus-square"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>


            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;">No</th>
                                <th style="text-align: center;">No Akun</th>
                                <th style="text-align: center;">Nama Akun</th>
                                <th style="text-align: center;">Uraian</th>
                                <th style="text-align: center;">Jumlah</th>
                                <th style="text-align: center;">Valas</th>
                                <th style="text-align: center;">Kurs</th>
                                <th style="text-align: center;">Debet (IDR)</th>
                                <th style="text-align: center;">Kredit (IDR)</th>
                                <th style="text-align:center;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot class="foot-detail-table" id="foot-detail-table">
                            <tr>
                                <td colspan="7" style="text-align: right;"><b>GRAND TOTAL</b></td>
                                <td style="text-align: center;"><b>0.00</b></td>
                                <td style="text-align: center;"><b>0.00</b></td>
                                <td style="text-align: center;"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>


        </div>
    </div>
</section>

<script>
    var csrfToken = '<?= csrf_token() ?>';
    var csrf = $(`[name="${csrfToken}"]`);
    var listJurnal = [];

    <?php if (isset($jurnalUmumList)): ?>
        listJurnal = <?= json_encode($jurnalUmumList) ?>;
        drawTable(listJurnal);

        function greatFormatRupiah(x) {
            var min = false;
            x = x.toString();
            if (x.includes("-")) {
                min = true;
            } else {
                min = false;
            }
            x = x.replace(/-/g, "");
            var parts = x.toString().split(".");
            parts[0] = parts[0].replace(/,/g, "");
            var bilangan = parts[0];

            var number_string = bilangan.toString(),
                sisa = number_string.length % 3,
                rupiah = number_string.substr(0, sisa),
                ribuan = number_string.substr(sisa).match(/\d{3}/g);

            if (ribuan) {
                var separator = sisa ? "," : "";
                rupiah += separator + ribuan.join(",");
            }
            parts[0] = rupiah;
            if (min) {
                return "-" + parts.join(".");
            } else {
                return parts.join(".");
            }

        }

    <?php endif; ?>

    $('#type_transaksi').select2({
        placeholder: "Pilih Tipe Transaksi",
        theme: "bootstrap-5",
    }).change(function() {

    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
    }).change(function() {

    });

    $('#jenis_transaksi').select2({
        placeholder: "Pilih Jenis Transaksi",
        theme: "bootstrap-5",
    }).change(function() {

    });

    $('#id_coa').select2({
        placeholder: "Pilih Akun COA",
        theme: "bootstrap-5",
    }).change(function() {

    });

    $('#valas_id').select2({
        placeholder: "Pilih Valas",
        theme: "bootstrap-5",
    }).change(function() {

    });

    $('#type_transaksi,#divisi_id,#jenis_transaksi,#id_coa,#valas_id')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    $('#type_transaksi,#divisi_id,#jenis_transaksi,#id_coa,#valas_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');


    // Validator Parent
    var validator = $(".create-parent").validate({
        rules: {
            no_bukti: {
                required: true,
            },
            tanggal_transaksi: {
                required: true,
            },
            type_transaksi: {
                required: true
            },
            uraian_transaksi: {
                required: true
            },

        },
        messages: {
            no_bukti: {
                required: "No Bukti Wajib Diisi",
            },
            tanggal_transaksi: {
                required: "Tanggal Transaksi Wajib Diisi",
            },
            type_transaksi: {
                required: "Tipe Transaksi Wajib Diisi"
            },
            uraian_transaksi: {
                required: "Uraian Transaksi Wajib Diisi"
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

    // validator child
    var validatorChild = $(".craete-child").validate({
        rules: {
            jenis_transaksi: {
                required: true,
            },
            id_coa: {
                required: true
            },
            valas_id: {
                required: true,
            },
            jumlah: {
                required: true,
            },
            kurs: {
                required: true
            },
            jumlah_idr: {
                required: true
            },

        },
        messages: {
            jenis_transaksi: {
                required: "Jumlah Transaksi Wajib Diisi",
            },
            id_coa: {
                required: "Akun Wajib Diisi"
            },
            valas_id: {
                required: "Valuta Wajib Diisi",
            },
            jumlah: {
                required: "Jumlah Transaksi Wajib Diisi",
            },
            kurs: {
                required: "Kurs Wajib Diisi"
            },
            jumlah_idr: {
                required: "Jumlah Dalam Format (IDR) Wajib Diisi"
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

    $('#jumlah').keyup(function() {
        var jumlah = destroyFormatRupiah($('#jumlah').val() || 0);
        var kurs = destroyFormatRupiah($('#kurs').val() || 1);
        var jumlahIdr = destroyFormatRupiah($('#jumlah_idr').val() || 0);

        $('#jumlah_idr').val(greatFormatRupiah(jumlah * kurs));
    });

    $('#kurs').keyup(function() {
        var jumlah = destroyFormatRupiah($('#jumlah').val() || 0);
        var kurs = destroyFormatRupiah($('#kurs').val() || 1);
        var jumlahIdr = destroyFormatRupiah($('#jumlah_idr').val() || 0);

        // $('#jumlah').val(greatFormatRupiah(jumlahIdr / kurs));
        $('#jumlah_idr').val(greatFormatRupiah(jumlah * kurs));

    });

    $('#jumlah_idr').keyup(function() {
        var jumlah = destroyFormatRupiah($('#jumlah').val() || 0);
        var kurs = destroyFormatRupiah($('#kurs').val() || 1);
        var jumlahIdr = destroyFormatRupiah($('#jumlah_idr').val() || 0);

        $('#jumlah').val(greatFormatRupiah(jumlahIdr / kurs));
    });

    $('#btn_submit_parent').click(function(e) {
        if ($('.create-parent').valid()) {
            if (listJurnal.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: "List Jurnal Tidak Boleh Kosong",
                    confirmButtonColor: '#4e73df',
                })
            } else {
                var totalKredit = 0;
                var totalDebit = 0;

                for (let i = 0; i < listJurnal.length; i++) {
                    if (listJurnal[i].jenis_transaksi == "debit") {
                        totalDebit += listJurnal[i].jumlah_idr;
                    } else {
                        totalKredit += listJurnal[i].jumlah_idr;
                    }
                }

                if (totalKredit == totalDebit) {
                    Swal.fire({
                        icon: 'question',
                        title: 'Simpan Data?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Kembali',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const csrf = $(`[name="${csrfToken}"]`);
                            var data = new FormData(document.querySelector(".create-parent"));
                            var id = $(".id").val();
                            data.append("listJurnal", JSON.stringify(listJurnal));
                            data.append("totalDebit", totalDebit);
                            data.append("totalKredit", totalKredit);

                            if (id) {
                                // UPDATE
                                $.ajax({
                                    url: "<?= base_url("jurnal/update"); ?>",
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
                                        if (response.status) {
                                            Swal.fire({
                                                    icon: 'success',
                                                    title: response.message,
                                                    confirmButtonColor: '#4e73df',
                                                })
                                                .then(() => {
                                                    window.location.href = "<?= base_url("jurnal"); ?>";
                                                })
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                        }
                                    },

                                });
                            } else {
                                // CREATE
                                $.ajax({
                                    url: "<?= base_url("jurnal/save"); ?>",
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
                                        if (response.status) {
                                            Swal.fire({
                                                    icon: 'success',
                                                    title: response.message,
                                                    confirmButtonColor: '#4e73df',
                                                })
                                                .then(() => {
                                                    window.location.href = "<?= base_url("jurnal"); ?>";
                                                })
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
                        }
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: "Total Debit dan Total Kredit Harus Balance",
                        confirmButtonColor: '#4e73df',
                    })
                }

            }

        }
    });

    $('#btn_submit_child').click(function(e) {
        if ($('.craete-child').valid()) {
            if ($('#id_detail').val() == "") {
                // CREATE
                addJurnalUmum();
            } else {
                // UPDATE
                var id_detail = $('#id_detail').val();
                updateJurnalUmum(id_detail);
            }

            resetDetailForm();
            drawTable(listJurnal);
        }
    });

    function addJurnalUmum() {

        listJurnal.push({
            id: getID(),
            jenis_transaksi: $('#jenis_transaksi').val(),
            keterangan: $('#keterangan').val(),
            id_coa: $('#id_coa option:selected').val(),
            valas_id: $('#valas_id option:selected').val(),
            valas: $('#valas_id option:selected').data('valas'),
            jumlah: destroyFormatRupiah($('#jumlah').val() || 0),
            kurs: destroyFormatRupiah($('#kurs').val() || 0),
            jumlah_idr: destroyFormatRupiah($('#jumlah_idr').val() || 0),
            nama_sub: $('#id_coa option:selected').data('nama_sub'),
            no_sub: $('#id_coa option:selected').data('no_sub'),
        });

    }

    function updateJurnalUmum(id) {
        var index = null;
        for (var i = 0; i < listJurnal.length; i++) {
            if (listJurnal[i].id == id) {
                index = i;
                break;
            }
        }

        listJurnal[index].jenis_transaksi = $('#jenis_transaksi').val();
        listJurnal[index].keterangan = $('#keterangan').val();
        listJurnal[index].id_coa = $('#id_coa option:selected').val();
        listJurnal[index].valas_id = $('#valas_id option:selected').val();
        listJurnal[index].valas = $('#valas_id option:selected').data('valas');
        listJurnal[index].jumlah = destroyFormatRupiah($('#jumlah').val() || 0);
        listJurnal[index].kurs = destroyFormatRupiah($('#kurs').val() || 0);
        listJurnal[index].jumlah_idr = destroyFormatRupiah($('#jumlah_idr').val() || 0);
        listJurnal[index].nama_sub = $('#id_coa option:selected').data('nama_sub');
        listJurnal[index].no_sub = $('#id_coa option:selected').data('no_sub');

    }

    function getById(id) {
        var item = null;
        for (var i = 0; i < listJurnal.length; i++) {
            if (listJurnal[i].id == id) {
                item = listJurnal[i];
                break;
            }
        }

        $('#id_detail').val(item.id);
        $('#jenis_transaksi').val(item.jenis_transaksi).change();
        $('#keterangan').val(item.keterangan);
        $('#id_coa').val(item.id_coa).change();
        $('#valas_id').val(item.valas_id).change();
        $('#jumlah').val(greatFormatRupiah(item.jumlah));
        $('#kurs').val(greatFormatRupiah(item.kurs));
        $('#jumlah_idr').val(greatFormatRupiah(item.jumlah_idr));
    }

    function deleteRowDetail(id) {
        const indexToRemove = listJurnal.findIndex(item => item.id === id);
        if (indexToRemove !== -1) {
            listJurnal.splice(indexToRemove, 1);
        }
        drawTable(listJurnal);
    }

    function resetDetailForm() {
        $('#id_detail').val("");
        $('#jenis_transaksi').val('debit').change();
        $('#keterangan').val("");
        $('#id_coa').val(null).change();
        $('#valas_id').val(30).change(); //idr
        $('#jumlah').val("");
        $('#kurs').val(1);
        $('#jumlah_idr').val("");
    }

    function drawTable(listJurnal) {
        $('.body-detail-table').empty();
        $('.foot-detail-table').empty();

        var row = '';
        var row_detail = '';
        var no = 1;
        var debitTotal = 0;
        var kreditTotal = 0;
        // LIST
        listJurnal.map(item => {
            row += '<tr style="color:whitesmoke;">';
            row += '<td>' + no + '</td>';
            row += '<td>' + item.no_sub + '</td>';
            row += '<td>' + item.nama_sub + '</td>';
            row += '<td>' + item.keterangan + '</td>';
            row += '<td>' + greatFormatRupiah(item.jumlah) + '</td>';
            row += '<td>' + item.valas + '</td>';
            row += '<td>' + greatFormatRupiah(item.kurs) + '</td>';
            row += '<td>' + (item.jenis_transaksi == "debit" ? greatFormatRupiah(item.jumlah) : greatFormatRupiah(0)) + '</td>';
            row += '<td>' + (item.jenis_transaksi == "kredit" ? greatFormatRupiah(item.jumlah) : greatFormatRupiah(0)) + '</td>';

            <?php if (isset($tutupBuku)) : ?>
                <?php if ($tutupBuku == 0) : ?>
                    row += '<td>' + `
                    <button class="btn btn-warning posting-spp mr-1 edit-table-detail" onclick="getById('${item.id}')">
                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                    </button>
                    <button class="btn btn-danger" onclick="deleteRowDetail('${item.id}')">
                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                    </button>` +
                        '</td>';
                <?php endif; ?>
            <?php else : ?>
                row += '<td>' + `
                    <button class="btn btn-warning posting-spp mr-1 edit-table-detail" onclick="getById('${item.id}')">
                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                    </button>
                    <button class="btn btn-danger" onclick="deleteRowDetail('${item.id}')">
                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                    </button>` +
                    '</td>';
            <?php endif; ?>


            debitTotal += item.jenis_transaksi == "debit" ? item.jumlah_idr : 0;
            kreditTotal += item.jenis_transaksi == "kredit" ? item.jumlah_idr : 0;
            no++;
        });

        // FOOTER
        row_detail += `
                    <tr>
                        <td colspan="7" style="text-align: right;"><b>GRAND TOTAL</b></td>
                        <td style="text-align: center;"><b>${greatFormatRupiah(debitTotal)}</b></td>
                        <td style="text-align: center;"><b>${greatFormatRupiah(kreditTotal)}</b></td>
                        <td></td>
                    </tr>
                `;

        $('.body-detail-table').append(row);
        $('.foot-detail-table').append(row_detail);

    }

    function destroy(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("jurnal/delete"); ?>",
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
                                })
                                .then(() => {
                                    window.location.href = "<?= base_url("jurnal"); ?>"
                                })
                        }
                    },
                    onError: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Gagal Dihapus, coba Lagi',
                            confirmButtonColor: '#4e73df',
                        })
                    }
                });
            }
        })
    }

    function generateNewCode() {
        var value = document.getElementById('auto_generate').checked ? true : false;
        var typeTransaksi = document.getElementById('type_transaksi').value;

        if (typeTransaksi == "" && value) {
            $("input[name='no_bukti']").attr("readonly", true);
            $("input[name='no_bukti']").val("");
        } else if (value && typeTransaksi != "") {
            $("input[name='kode_barang']").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("jurnal/generate-no-bukti"); ?>`,
                data: {
                    transaksi: typeTransaksi
                },
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                method: "POST",
                success: function(res) {
                    csrf.val(res.token);
                    $("input[name='no_bukti']").attr("readonly", true);
                    $("input[name='no_bukti']").val(res.codeNew);
                    $("#tgl_transaksi").focus();
                }
            })
        } else if (value == false) {
            $("input[name='no_bukti']").attr("readonly", false);
            $("input[name='no_bukti']").val("");
        }
    }


    function getID() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let randomString = '';

        for (let i = 0; i < 10; i++) {
            randomString += characters.charAt(Math.floor(Math.random() * characters.length));
        }

        return randomString;
    };

    function print(url) {
        window.open(url, "_blank");
    }
</script>


<?= $this->endSection(); ?>