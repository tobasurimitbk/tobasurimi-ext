<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($biayaUdang) ? "Tambah Biaya Udang" : "Update Biaya Udang" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("biaya-udang"); ?>">
                Batal
            </a>
            <?php if (!empty($biayaUdang)) : ?>
                <?php if ($biayaUdang['status_posting'] == "0") : ?>
                    <?php if (can('Jasa Vendor', 'Biaya Udang', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="remove('<?= encrypt($biayaUdang['id']); ?>')">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Biaya Udang', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="posting('<?= encrypt($biayaUdang['id']); ?>')">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Biaya Udang', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php else : ?>

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
                <input type="hidden" name="id" id="id" value="<?= !empty($biayaUdang) ? encrypt($biayaUdang['id']) : '' ?>" class="id">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" <?= !empty($biayaUdang) ? 'disabled=true' : ''; ?> value="<?= !empty($biayaUdang) ? $biayaUdang['no_rebus'] : "PAY-UDG/" . date('m') . "/1/" . date('Y'); ?>" type="text" class="form-control no_rebus" id="no_rebus" name="no_rebus" placeholder="No. Rebus">
                                    <label for="floatingInput">No. Rebus</label>
                                </div>
                                <div style="<?= !empty($biayaUdang) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($biayaUdang) ? ($biayaUdang['status_posting'] ? 'disabled' : '') : '' ?> autocomplete="one-time-code" class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime(!empty($biayaUdang) ? $biayaUdang['tanggal'] : $tanggal)); ?>">
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
                            <select <?= !empty($biayaUdang) ? ($biayaUdang['status_posting'] ? 'disabled' : '') : '' ?> class="form-select jasa_vendor_in_id" id="jasa_vendor_in_id" name="jasa_vendor_in_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($jasaVendorIn)) : ?>
                                    <?php foreach ($jasaVendorIn as $j) : ?>
                                        <option data-vendor="<?= strtoupper($j['name']) ?>" data-divisi="<?= strtoupper($j['divisi']) ?>" value="<?= $j['id'] ?>">
                                            <?= $j['no_penerimaan_surat_jalan'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih No Surat Jalan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" disabled type="text" class="form-control vendor" id="vendor" name="vendor" placeholder="Vendor">
                            <label for="floatingInput">Vendor</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" disabled type="text" class="form-control divisi" id="divisi" name="divisi" placeholder="Departemen">
                            <label for="floatingInput">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($biayaUdang) ? ($biayaUdang['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Keterangan" value="<?= !empty($biayaUdang) ? $biayaUdang['keterangan'] : '' ?>" class="form-control keterangan" id="keterangan" name="keterangan" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Keterangan (Opsional)</label>
                        </div>
                    </div>
                </div>

            </form>


            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Daftar Barang Yang Akan Dibayar</label>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" border="1" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;" colspan="4"></th>
                                    <th style="text-align: center;" colspan="1">Size</th>
                                    <th style="text-align: center;" colspan="3"></th>
                                    <th style="text-align: center;" colspan="4">Upah Kopek Yang Dibayar</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">Tanggal Masuk</th>
                                    <th style="text-align: center;">Tanggal Keluar</th>
                                    <th style="text-align: center;">Jenis</th>


                                    <th style="text-align: center;">Mentah</th>

                                    <th style="text-align: center;">KG REBUS</th>
                                    <th style="text-align: center;">KG DAGING FAUZY</th>
                                    <th style="text-align: center;">KG DAGING CN</th>

                                    <th style="text-align: center;">Kg Daging</th>
                                    <th style="text-align: center;">Ratio</th>
                                    <th style="text-align: center;">TB Harga</th>
                                    <th style="text-align: center;">Total Harga</th>
                                </tr>
                            </thead>
                            <tbody class="body-table">
                            </tbody>
                            <tfoot class="foot-detail-table" id="foot-detail-table">
                                <tr>
                                    <td colspan="12" style="text-align: center;">
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
                title: 'Barang yang akan dibayar tidak boleh kosong !',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        } else {
            if ($('.create-form').valid()) {
                var isValidTbHarga = true;
                var dataErrorTbHarga = null;

                var isValidDagingFauzy = true;
                var dataErrorDagingFauzy = null;

                var isValidDagingCn = true;
                var dataErrorDagingCn = null;

                var isValidKgDaging = true;
                var dataErrorKgDaging = null;

                $.each(listBarang, function(i, v) {
                    var tbHargaElement = $('input[data-id="' + v.jasa_vendor_out_detail_id + '"].tb_harga');
                    var kgFauzyElement = $('input[data-id="' + v.jasa_vendor_out_detail_id + '"].kg_fauzy');
                    var kgCnElement = $('input[data-id="' + v.jasa_vendor_out_detail_id + '"].kg_cn');
                    var kgDagingElement = $('input[data-id="' + v.jasa_vendor_out_detail_id + '"].kg_daging');

                    // asign value tb harga
                    if (Number(tbHargaElement.val()) == 0 || tbHargaElement.val() == undefined || tbHargaElement.val() == '') {
                        dataErrorTbHarga = listBarang[i];
                        isValidTbHarga = false;
                    } else {
                        // assign
                        listBarang[i].tb_harga = tbHargaElement.val();
                    }

                    // asign value kg fauzy
                    if (Number(kgFauzyElement.val()) == 0 || kgFauzyElement.val() == undefined || kgFauzyElement.val() == '') {
                        dataErrorDagingFauzy = listBarang[i];
                        isValidDagingFauzy = false;
                    } else {
                        listBarang[i].kg_fauzy = kgFauzyElement.val();
                    }

                    // asign value cn
                    if (Number(kgCnElement.val()) == 0 || kgCnElement.val() == undefined || kgCnElement.val() == '') {
                        dataErrorDagingCn = listBarang[i];
                        isValidDagingCn = false;
                    } else {
                        listBarang[i].kg_cn = kgCnElement.val();
                    }

                    // asign value daging
                    if (Number(kgDagingElement.val()) == 0 || kgDagingElement.val() == undefined || kgDagingElement.val() == '') {
                        dataErrorDagingCn = listBarang[i];
                        isValidKgDaging = false;
                    } else {
                        listBarang[i].kg_daging = kgDagingElement.val();
                    }

                });

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Qty kotor harus lebih besar dari Qty bersih untuk barang masuk : ' + dataError.barang_in + ' dengan dokumen ' + dataError.bc_name + ' / ' + dataError.no_aju + ' tidak valid!',
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
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
                                                    window.location.href = "<?= base_url('jasa-vendor-in/id/') ?>" + response.id
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

    function listDataBarang() {
        $.ajax({
            url: `<?= base_url('biaya-udang/list-barang'); ?>`,
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
                const table = $('#dataTable');
                $('.foot-detail-table').empty();
                $('.body-table').empty();
                var no = 1;

                if (res.data.length == 0) {
                    row += `
                    <tr>
                        <td colspan="12" style="text-align: center;">
                            Tidak Ada Barang
                        </td>
                    </tr>
                `;
                    $('.foot-detail-table').append(row);
                    console.log("asas");
                } else {
                    $.each(res.data, function(i, v) {
                        var barang_master_id_last = 0;

                        if (i == 0) {
                            barang_master_id_last = v.barang_master_id;
                        } else {
                            barang_master_id_last = res.data[i - 1].barang_master_id;
                        }

                        // SUB TOTAL ATAS
                        if (barang_master_id_last != v.barang_master_id) {
                            var newRow = $('<tr  style="color:whitesmoke; background-color:#fadfbe">');
                            newRow.append($('<td style="text-align: center;" colspan="5">').html("<b>SUB TOTAL</b>"));
                            newRow.append($('<td>').text("0"));
                            newRow.append($('<td >').text("0"));
                            newRow.append($('<td>').text("0"));
                            newRow.append($('<td>').text("0"));
                            newRow.append($('<td>').text("0"));
                            newRow.append($('<td style="text-align: center;">').html(
                                `
                            <input <?= !empty($biayaUdang) ? (($biayaUdang['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control tb_harga" oninput="preventNegativeInput(this)" data-barang_master_id="${v.barang_master_id}" autocomplete="one-time-code" class="form-control kg_rebus" type="text" value="0">
                        `
                            ));
                            newRow.append($('<td >').text("-"));
                            table.find('tbody').append(newRow);
                        }

                        // KONTEN
                        var newRow = $('<tr  style="color:whitesmoke;">');
                        newRow.append($('<td style="text-align: center;">').html(
                            `
                            ${no++} 
                        `
                        ));
                        newRow.append($('<td style="text-align: center;">').text(v.tanggal_masuk));
                        newRow.append($('<td style="text-align: center;">').text(v.tanggal_keluar));
                        newRow.append($('<td style="text-align: center;">').text(v.barang_name));
                        newRow.append($('<td style="text-align: center;">').text(v.spesifikasi));
                        newRow.append($('<td>').text(v.qty_rebus));
                        newRow.append($('<td style="text-align: center;">').html(
                            `
                            <input <?= !empty($biayaUdang) ? (($biayaUdang['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control kg_fauzy" oninput="preventNegativeInput(this)" data-spesifikasi_id="${v.barang_master_spesifikasi_id}"  autocomplete="one-time-code" class="form-control kg_fauzy" type="text" value="0">
                        `
                        ));
                        newRow.append($('<td style="text-align: center;">').html(
                            `
                            <input <?= !empty($biayaUdang) ? (($biayaUdang['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control kg_cn" oninput="preventNegativeInput(this)" data-spesifikasi_id="${v.barang_master_spesifikasi_id}" autocomplete="one-time-code" class="form-control kg_cn" type="text" value="0">
                        `
                        ));
                        newRow.append($('<td style="text-align: center;">').html(
                            `
                            <input <?= !empty($biayaUdang) ? (($biayaUdang['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control kg_daging" oninput="preventNegativeInput(this)" data-spesifikasi_id="${v.barang_master_spesifikasi_id}" autocomplete="one-time-code" class="form-control kg_daging" type="text" value="0">
                        `
                        ));
                        newRow.append($('<td>').text('-'));
                        newRow.append($('<td>').text('-'));
                        newRow.append($('<td>').text('-'));
                        table.find('tbody').append(newRow);

                        // SUB TOTAL BAWAH
                        if ((res.data.length - 1) == i) {
                            var newRow = $('<tr  style="color:whitesmoke; background-color:#fadfbe">');
                            newRow.append($('<td style="text-align: center;" colspan="5">').html("<b>SUB TOTAL</b>"));
                            newRow.append($('<td>').text("0"));
                            newRow.append($('<td >').text("0"));
                            newRow.append($('<td>').text("0"));
                            newRow.append($('<td>').text("0"));
                            newRow.append($('<td>').text("0"));
                            newRow.append($('<td style="text-align: center;">').html(
                                `
                                <input <?= !empty($biayaUdang) ? (($biayaUdang['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control tb_harga" data-barang_master_id="${v.barang_master_id}" oninput="preventNegativeInput(this)" autocomplete="one-time-code" class="form-control tb_harga" type="text" value="0">
                        `
                            ));
                            newRow.append($('<td >').text("-"));
                            table.find('tbody').append(newRow);
                        }

                    });

                    // GRAND TOTAL
                    var newRow = $('<tr style="color:whitesmoke; background-color:#f2c996">');
                    newRow.append($('<td style="text-align: center;" colspan="5">').html("<b>GRAND TOTAL</b>"));
                    newRow.append($('<td>').text("0"));
                    newRow.append($('<td >').text("0"));
                    newRow.append($('<td>').text("0"));
                    newRow.append($('<td>').text("0"));
                    newRow.append($('<td>').text("-"));
                    newRow.append($('<td >').text("0"));
                    newRow.append($('<td >').text(""));
                    table.find('tbody').append(newRow);
                }


            }
        });
    }

    function preventNegativeInput(inputElement) {
        var inputValue = inputElement.value;
        var numericValue = inputValue.replace(/[^0-9.]/g, '');
        if (parseFloat(numericValue) <= 0) {
            inputElement.value = 0;
        } else {
            inputElement.value = numericValue;
        }
    }
</script>

<?= $this->endSection(); ?>