<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($jasaVendorIn) ? "Tambah Jasa Vendor Barang Masuk" : "Update Jasa Vendor Barang Masuk" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("jasa-vendor-in"); ?>">
                Batal
            </a>
            <?php if (!empty($jasaVendorIn)) : ?>
                <?php if ($jasaVendorIn['status_posting'] == "0") : ?>
                    <?php if (can('Jasa Vendor', 'Barang Masuk', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="remove('<?= encrypt($jasaVendorIn['id']); ?>')">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Barang Masuk', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="posting('<?= encrypt($jasaVendorIn['id']); ?>')">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Barang Masuk', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("jasa-vendor-in/print/"); ?><?= encrypt($jasaVendorIn['id']); ?>')">
                            Print
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Barang Masuk', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if (can('Jasa Vendor', 'Barang Masuk', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("jasa-vendor-in/print/"); ?><?= encrypt($jasaVendorIn['id']); ?>')">
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
                    <label class="form-label font-weight-bold lable-title">Data Pemasukkan Barang</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id" value="<?= !empty($jasaVendorIn) ? encrypt($jasaVendorIn['id']) : '' ?>" class="id">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" disabled class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime(!empty($jasaVendorIn) ? $jasaVendorIn['tanggal'] : $tanggal)); ?>">
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
                                    <input readonly autocomplete="one-time-code" <?= !empty($jasaVendorIn) ? 'disabled=true' : ''; ?> value="<?= !empty($jasaVendorIn) ? $jasaVendorIn['no_penerimaan_surat_jalan'] : "TOBA-VBM//" . date('m') . "/1/" . date('Y'); ?>" type="text" class="form-control no_penerimaan_surat_jalan" id="no_penerimaan_surat_jalan" name="no_penerimaan_surat_jalan" placeholder="No. Surat Jalan">
                                    <label for="floatingInput">No Penerimaan Surat Jalan</label>
                                </div>
                                <div style="<?= !empty($jasaVendorIn) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($jasaVendorIn) ? ($jasaVendorIn['status_posting'] ? 'disabled' : '') : '' ?> class="form-select vendor_id" id="vendor_id" name="vendor_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($vendor as $v) : ?>
                                    <option <?= !empty($jasaVendorIn) ? ($jasaVendorIn['vendor_id'] == $v['id'] ? 'selected' : '') : '' ?> value="<?= $v['id'] ?>">
                                        <?= strtoupper($v['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Vendor</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($jasaVendorIn) ? ($jasaVendorIn['status_posting'] ? 'disabled' : '') : '' ?> class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($divisi)) : ?>
                                    <?php foreach ($divisi as $d) : ?>
                                        <option <?= $jasaVendorIn['divisi_id'] == $d['id'] ? 'selected' : '' ?> value="<?= $d['id'] ?>">
                                            <?= $d['divisi'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($jasaVendorIn) ? ($jasaVendorIn['status_posting'] ? 'disabled' : '') : '' ?> class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($warehouse)) : ?>
                                    <?php foreach ($warehouse as $w) : ?>
                                        <option <?= $jasaVendorIn['warehouse_id'] == $w['id'] ? 'selected' : '' ?> value="<?= $w['id'] ?>">
                                            <?= $w['warehouse_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Warehouse</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($jasaVendorIn) ? ($jasaVendorIn['status_posting'] ? 'disabled' : '') : '' ?> multiple class="form-select multiple_jasa_vendor_out_id" name="multiple_jasa_vendor_out_id[]" id="multiple_jasa_vendor_out_id[]">
                                <option value=""></option>
                                <?php if (!empty($jasaVendorIn)) : ?>
                                    <?php foreach (json_decode($jasaVendorIn['multiple_jasa_vendor_out_id']) as $i => $p) : ?>
                                        <option selected value="<?= $p ?>"><?= json_decode($jasaVendorIn['multiple_jasa_vendor_out_no'])[$i] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">No. Surat Jalan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($jasaVendorIn) ? ($jasaVendorIn['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Keterangan" value="<?= !empty($jasaVendorIn) ? $jasaVendorIn['no_surat_jalan_vendor'] : '' ?>" class="form-control no_surat_jalan_vendor" id="no_surat_jalan_vendor" name="no_surat_jalan_vendor" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">No Surat Jalan Vendor</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($jasaVendorIn) ? ($jasaVendorIn['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Keterangan" value="<?= !empty($jasaVendorIn) ? $jasaVendorIn['keterangan'] : '' ?>" class="form-control keterangan" id="keterangan" name="keterangan" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Keterangan</label>
                        </div>
                    </div>
                </div>

            </form>
            <div class="row">
                <div class="col-md-12 col-table-button-tts">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" border="1" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;" colspan="6">Detail Dokumen Pabean</th>
                                    <th style="text-align: center;" colspan="3">Daftar Barang Keluar</th>
                                    <th style="text-align: center;" colspan="4">Daftar Barang Masuk</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">Tipe Barang</th>
                                    <th style="text-align: center;">Dokumen Pabean</th>
                                    <th style="text-align: center;">No Aju</th>
                                    <th style="text-align: center;">Tgl Penerimaan</th>
                                    <th style="text-align: center;">Supplier</th>


                                    <th style="text-align: center;">Barang - Spesifikasi</th>
                                    <th style="text-align: center;">Satuan Output</th>
                                    <th style="text-align: center;">Qty Keluar</th>

                                    <th style="text-align: center;">Barang - Spesifikasi</th>
                                    <th style="text-align: center;">Satuan Masuk</th>
                                    <th style="text-align: center;">Qty Kotor</th>
                                    <th style="text-align: center;">Qty Bersih</th>

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


        </div>

    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    var listBarang = [];

    <?php if (!empty($jasaVendorIn)) : ?>
        let arr = $('.multiple_jasa_vendor_out_id').val();
        $.ajax({
            url: `<?= base_url("jasa-vendor-in/list-barang"); ?>`,
            method: "GET",
            data: {
                multiple_jasa_vendor_out_id: JSON.stringify(arr),
                id: $('.id').val()
            },

            dataType: "json",
            success: function(res) {
                listBarang = [];
                listBarang = res.data;
                drawTable(listBarang);
            }
        })
    <?php endif; ?>

    $('#vendor_id').select2({
        placeholder: "Pilih Vendor",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // LIST DIVISI
        getListDivisi();
        listBarang = [];
    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // LIST WAREHOUSE
        getListWarehouse();
        listBarang = [];
    });

    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // LIST SURAT JALAN
        getListJasaVendorOut();
        changeStatus();
        listBarang = [];
    });

    $('.multiple_jasa_vendor_out_id').select2({
        placeholder: "Pilih Surat Jalan",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function() {
        let arr = $('.multiple_jasa_vendor_out_id').val();
        $.ajax({
            url: `<?= base_url("jasa-vendor-in/list-barang"); ?>`,
            method: "GET",
            data: {
                multiple_jasa_vendor_out_id: JSON.stringify(arr),
                id: $('.id').val()
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

    // VALIDATOR
    var validator = $(".create-form").validate({
        rules: {
            no_penerimaan_surat_jalan: {
                required: true
            },
            vendor_id: {
                required: true
            },
            divisi_id: {
                required: true
            },
            warehouse_id: {
                required: true
            },
            no_surat_jalan_vendor: {
                required: true
            },
            keterangan: {
                required: true
            },
        },
        messages: {
            no_penerimaan_surat_jalan: {
                required: "No penerimaan surat jalan wajib diisi"
            },
            vendor_id: {
                required: "Vendor wajib diisi"
            },
            divisi_id: {
                required: "Departemen wajib diisi"
            },
            warehouse_id: {
                required: "Warehouse wajib diisi"
            },
            no_surat_jalan_vendor: {
                required: "No surat jalan vendor wajib diisi"
            },
            keterangan: {
                required: "Keterangan wajib diisi"
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
                title: 'Barang yang akan diterima dari vendor tidak boleh kosong !',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        } else {
            if ($('.create-form').valid()) {
                var isValid = true;
                var dataError = null;

                $.each(listBarang, function(i, v) {
                    var qtyKotorElement = $('input[data-id="' + v.jasa_vendor_out_detail_id + '"].qty-kotor');
                    var qtyBersihElement = $('input[data-id="' + v.jasa_vendor_out_detail_id + '"].qty-bersih');
                    console.log(Number(qtyKotorElement.val()), Number(qtyBersihElement.val()))
                    if (Number(qtyKotorElement.val()) < Number(qtyBersihElement.val())) {
                        dataError = listBarang[i];
                        isValid = false;
                    } else {
                        listBarang[i].qty_bersih = qtyBersihElement.val();
                        listBarang[i].qty_kotor = qtyKotorElement.val();
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

    $("#vendor_id,#divisi_id,#warehouse_id,.multiple_jasa_vendor_out_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    function getListDivisi() {
        // GET LIST DIVISI
        $.ajax({
            url: `<?= base_url('jasa-vendor-in/divisi'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                vendor_id: $(".vendor_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".divisi_id").empty()
                $(".divisi_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".divisi_id").append(`<option value="${item.id}">${item.divisi}</option>`)
                })
                $(".divisi_id").val();
            }
        });
    }

    function drawTable(listBarang) {
        const table = $('#dataTable');
        table.find('tbody').empty();
        table.find('tfoot').empty();

        if (listBarang.length == 0) {
            var newRow = $('<tr>');
            newRow.append($('<td colspan="13" style="text-align:center">Tidak Ada Barang</td>'));
            table.find('tfoot').append(newRow);
        } else {
            var no = 1;
            $.each(listBarang, function(i, v) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td>').text(no++));
                newRow.append($('<td>').text(v.tipe_barang));
                newRow.append($('<td>').text(v.bc_name));
                newRow.append($('<td>').text(v.no_aju));
                newRow.append($('<td>').text(v.stock_date));
                newRow.append($('<td>').text(v.supplier_name));
                newRow.append($('<td>').text(v.barang_out));
                newRow.append($('<td>').text(v.satuan_out));
                newRow.append($('<td>').text(v.qty_out));
                newRow.append($('<td>').text(v.barang_in));
                newRow.append($('<td>').text(v.satuan_in));
                newRow.append($('<td>').html(
                    `
                        <input <?= !empty($jasaVendorIn) ? (($jasaVendorIn['status_posting'] == "1") ? 'disabled' : '') : '' ?> class="form-control qty-kotor" style="height: 40px; padding-bottom: 10px;" oninput="preventNegativeInput(this)" autocomplete="one-time-code" data-id="${v.jasa_vendor_out_detail_id}" class="form-control" type="text" value="${v.qty_kotor}">
                    `
                ));

                newRow.append($('<td>').html(
                    `
                        <input <?= !empty($jasaVendorIn) ? (($jasaVendorIn['status_posting'] == "1") ? 'disabled' : '') : '' ?> class="form-control qty-bersih" style="height: 40px; padding-bottom: 10px;" oninput="preventNegativeInput(this)" autocomplete="one-time-code" data-id="${v.jasa_vendor_out_detail_id}" class="form-control" type="text" value="${v.qty_bersih}">
                    `
                ));
                table.find('tbody').append(newRow);
            });
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

    function getListWarehouse() {
        // GET LIST DIVISI
        $.ajax({
            url: `<?= base_url('jasa-vendor-in/warehouse'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                vendor_id: $(".vendor_id option:selected").val(),
                divisi_id: $('.divisi_id option:selected').val()
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_id").empty()
                $(".warehouse_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
                $(".warehouse_id").val();
            }
        });
    }

    function getListJasaVendorOut() {
        $.ajax({
            url: `<?= base_url('jasa-vendor-in/list-jasa-vendor-out'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                vendor_id: $(".vendor_id option:selected").val(),
                divisi_id: $('.divisi_id option:selected').val(),
                warehouse_id: $(".warehouse_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".multiple_jasa_vendor_out_id").empty()
                $(".multiple_jasa_vendor_out_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".multiple_jasa_vendor_out_id").append(`<option value="${item.id}">${item.no_surat_jalan}</option>`)
                })
                $(".multiple_jasa_vendor_out_id").val();

            }
        });
    }

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $(".no_penerimaan_surat_jalan").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("jasa-vendor-in/get-jasa-vendor-in-no"); ?>`,
                method: "GET",
                data: {
                    warehouse_id: $('#warehouse_id option:selected').val()
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".no_penerimaan_surat_jalan").val(res?.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".no_penerimaan_surat_jalan").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".no_penerimaan_surat_jalan").val("");
                    }
                }
            })
        } else {
            $(".no_penerimaan_surat_jalan").attr("readonly", false);
            $(".no_penerimaan_surat_jalan").val("");
        }
    }

    const print = function(url) {
        window.open(url, "_blank");
    }

    const posting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Jasa Vendor Barang Masuk ?',
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
                    url: "<?= base_url("jasa-vendor-in/posting"); ?>",
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
            title: 'Hapus Jasa Vendor Barang Masuk ?',
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
                    url: "<?= base_url("jasa-vendor-in/delete"); ?>",
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