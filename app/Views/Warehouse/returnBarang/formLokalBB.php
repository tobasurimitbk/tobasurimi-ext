<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1 class="title-name">Retur Purchase Lokal Barang Bahan Baku</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("retur-po-lokal-bb"); ?>">
                Kembali
            </a>
            <?php if (!empty($dataPengembalianBarang)) : ?>
                <?php if (can('Retur Pembelian', 'Retur Lokal BB', 'p')) : ?>
                    <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("retur-po-lokal-bb/print/"); ?><?= encrypt($dataPengembalianBarang['id']); ?>')">
                        Print
                    </button>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (!empty($dataPengembalianBarang)) : ?>
                <?php if ($dataPengembalianBarang['status_post'] === "WAITING") : ?>
                    <?php if (can('Retur Pembelian', 'Retur Lokal BB', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="remove(' <?= encrypt($dataPengembalianBarang['id']) ?>')">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Retur Pembelian', 'Retur Lokal BB', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-lpb" onclick="posting(' <?= encrypt($dataPengembalianBarang['id']) ?>')">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Retur Pembelian', 'Retur Lokal BB', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
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
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="id" value="<?= !empty($dataPengembalianBarang) ? encrypt($dataPengembalianBarang['id']) : "" ?>">
                <div class="row mb-1">
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold">Data Retur Barang</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" <?= !empty($dataPengembalianBarang) ? "readonly" : ""; ?> value="<?= !empty($dataPengembalianBarang) ? $dataPengembalianBarang['no_surat_jalan'] : ""; ?>" type="text" class="form-control no_surat_jalan" id="no_surat_jalan" name="no_surat_jalan" placeholder="No. Surat Jalan">
                                    <label for="floatingInput">No. Surat Jalan</label>
                                </div>
                                <div class="input-generate input-group-prepend group-prepend-password align-items-center" style="<?= !empty($dataPengembalianBarang) ?  "display: none" : ""; ?>">
                                    <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="generateNumber()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" <?= !empty($dataPengembalianBarang) ? (($dataPengembalianBarang['status_post'] !== "WAITING") ? "readonly" : "") : ""; ?> value="<?= !empty($dataPengembalianBarang) ? date('d/m/Y', strtotime($dataPengembalianBarang['tanggal_surat_jalan'])) : ""; ?>" type="text" class="form-control tanggal_retur_barang" name="tanggal_retur_barang" id="tanggal_retur_barang" placeholder="Tanggal Retur Barang">
                                <label for="floatingInput">Tanggal Retur Barang</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= isset($dataPengembalianBarang) ? ($dataPengembalianBarang['status_post'] == "FINISH" ? 'disabled' : '') : ""  ?> class="form-select divisi_id" id="divisi_id" name="divisi_id" onchange="generateNumber();getPenerimaanBarang()">
                                <option value=""></option>
                                <?php foreach ($dataDivisi as $divisi) : ?>
                                    <option value="<?= $divisi["id"]; ?>" <?= !empty($dataPengembalianBarang) ? ($dataPenerimaanBarang[0]['divisi_id'] === $divisi["id"] ? "selected" : "") : ""; ?>><?= strtoupper($divisi["divisi"]); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= isset($dataPengembalianBarang) ? ($dataPengembalianBarang['status_post'] == "FINISH" ? 'disabled' : '') : ""  ?> class="form-select penerimaan_barang_id" id="penerimaan_barang_id" name="penerimaan_barang_id" onchange="getDetailPenerimaanBarang()">
                                <option value=""></option>
                                <?php if (isset($dataPenerimaanBarang)): ?>
                                    <?php foreach ($dataPenerimaanBarang as $d): ?>
                                        <option value="<?= $d['id'] ?>" selected>
                                            <?= $d['no_penerimaan_barang'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">No Penerimaan Barang</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select disabled <?= isset($dataPengembalianBarang) ? ($dataPengembalianBarang['status_post'] == "FINISH" ? 'disabled' : '') : ""  ?> class="form-select bc_pengeluaran_id" id="bc_pengeluaran_id" name="bc_pengeluaran_id">
                                <option value=""></option>
                                <option <?= isset($dataPengembalianBarang) ? ($dataPengembalianBarang['bc_pengeluaran_id'] == 0 ? 'selected' : '') : '' ?> value="0">NON PABEAN</option>
                                <?php foreach ($dataDokumenPabean as $d): ?>
                                    <option <?= isset($dataPengembalianBarang) ? ($dataPengembalianBarang['bc_pengeluaran_id'] != 0 ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>"><?= $d['value'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Dokumen Pengeluaran</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= isset($dataPenerimaanBarang) ? $dataPenerimaanBarang[0]['warehouse_name'] : "" ?>" readonly type="text" class="form-control warehouse" id="warehouse" name="warehouse" placeholder="Warehouse">
                            <label for="floatingInput">Warehouse</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= isset($dataPenerimaanBarang) ? $dataPenerimaanBarang[0]['supplier_name'] : "" ?>" readonly type="text" class="form-control supplier" id="supplier" name="supplier" placeholder="Supplier">
                            <label for="floatingInput">Supplier</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= isset($dataPengembalianBarang) ? ($dataPengembalianBarang['status_post'] == "FINISH" ? 'disabled' : '') : ""  ?> type="text" value="<?= isset($dataPengembalianBarang) ? $dataPengembalianBarang['keterangan'] : "" ?>" class="form-control keterangan" id="keterangan" name="keterangan" placeholder="Keterangan (Opsional)">
                            <label for="floatingInput">Keterangan (Opsional)</label>
                        </div>
                    </div>
                </div>

            </form>

            <div class="col-subtitle-modal">
                <div class="row mt-1">
                    <div class="col-md-12">
                        <label class="form-label font-weight-bold modal-sub-title">List Penerimaan Barang</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th colspan="4"></th>
                                <th colspan="3" style="text-align: center;">Dokumen Pemasukan</th>
                                <th colspan="8"></th>
                            </tr>
                            <tr>
                                <th style="text-align: center;">No</th>
                                <th style="text-align: center;">Asal Barang</th>
                                <th style="text-align: center;">Kode Barang</th>
                                <th style="text-align: center;">Nama Barang</th>
                                <th style="text-align: center;">No Aju</th>
                                <th style="text-align: center;">No Daftar</th>
                                <th style="text-align: center;">Tgl Daftar</th>
                                <th style="text-align: center;">No PO</th>
                                <th style="text-align: center;">Jml Diterima</th>
                                <th style="text-align: center;">Satuan</th>
                                <th style="text-align: center;">Harga</th>
                                <th style="text-align: center;">Sub Total</th>
                                <th style="text-align:center;">Jml Retur</th>
                                <th style="text-align:center;">Ket Retur</th>
                                <th style="text-align:center;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot class="foot-detail-table" id="foot-detail-table">
                            <tr>
                                <td colspan="8" style="text-align: right;"><b>GRAND TOTAL</b></td>
                                <td style="text-align: center;"><b>0</td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"><b>0</td>
                                <td style="text-align: center;"><b>0</b></td>
                                <td style="text-align: center;"><b>0</b></td>
                                <td style="text-align: center;"></td>
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
    const csrfToken = '<?= csrf_token() ?>';
    var listBarang = [];

    <?php if (isset($dataPengembalianBarangDetail)): ?>
        <?php foreach ($dataPengembalianBarangDetail as $d): ?>
            listBarang.push({
                harga: <?= $d['harga'] ?>,
                sub_total: <?= $d['sub_total'] ?>,
                id: "<?= $d['id'] ?>",
                jml_diterima: <?= $d['jml_diterima'] ?>,
                jml_retur: <?= $d['jml_retur'] ?>,
                ket_retur: "<?= $d['ket_retur'] ?>",
                kode_barang: "<?= $d['kode_barang'] ?>",
                kode_satuan: "<?= $d['kode_satuan'] ?>",
                nama_barang: "<?= $d['nama_barang'] ?>",
                no_po: "<?= $d['no_po'] ?>",
                sumber: "<?= $d['sumber'] ?>",
                no_aju: "<?= $d['no_aju'] ?>",
                no_daftar: "<?= $d['no_daftar'] ?>",
                stock_date: "<?= $d['stock_date'] ?>",
            });
        <?php endforeach; ?>
        drawTable(listBarang);
    <?php endif; ?>

    $("#tanggal_retur_barang").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true,
        enableOnReadonly: false
    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {});

    $('#penerimaan_barang_id').select2({
        placeholder: "Pilih Nomor Penerimaan Barang",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {});

    $('#bc_pengeluaran_id').select2({
        placeholder: "Pilih Dokumen Pengeluaran",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {});

    $("#divisi_id,#penerimaan_barang_id,#bc_pengeluaran_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    var validator = $(".create-form").validate({
        rules: {
            no_surat_jalan: {
                required: true,
            },
            tanggal_retur_barang: {
                required: true,
            },
            divisi_id: {
                required: true
            },
            penerimaan_barang_id: {
                required: true
            },
            bc_pengeluaran_id: {
                required: true,
            },
        },
        messages: {
            no_surat_jalan: {
                required: "No Surat Jalan Wajib Diisi",
            },
            tanggal_retur_barang: {
                required: "Tanggal Retur Wajib Diisi",
            },
            divisi_id: {
                required: "Departemen Wajib Diisi"
            },
            penerimaan_barang_id: {
                required: "Pilih No Penerimaan Barang"
            },
            bc_pengeluaran_id: {
                required: "Pilih Dokumen Pengeluaran",
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
            if (listBarang.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Barang yang diretur masih kosong !",
                    confirmButtonColor: '#4e73df',
                })
            } else {
                // APPEND
                var isValidData = true;
                var barangError = null;
                $.each(listBarang, function(i, v) {
                    var element_jumlah_retur = $('input[data-id="' + v.id + '"].jumlah_retur');
                    var element_keterangan_retur = $('input[data-id="' + v.id + '"].keterangan_retur');
                    var jumlah_retur = parseFloat(element_jumlah_retur.val());

                    if (isNaN(jumlah_retur) || jumlah_retur == undefined || jumlah_retur > v.jml_diterima) {
                        isValidData = false;
                        barangError = v;
                    } else {
                        listBarang[i].jml_retur = jumlah_retur;
                        listBarang[i].ket_retur = element_keterangan_retur.val();
                    }
                })

                if (isValidData == false) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Qty retur untuk barang ' + barangError.nama_barang + ' (' + barangError.kode_barang + '), tidak valid !',
                        confirmButtonColor: '#4e73df',
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
                        cancelButtonText: 'Kembali',
                    }).then((result) => {
                        const csrf = $(`[name="${csrfToken}"]`);
                        if (result.isConfirmed) {
                            let id = $('#id').val();
                            let data = new FormData(document.querySelector(".create-form"));
                            data.append('listBarang', JSON.stringify(listBarang));
                            data.append('bc_pengeluaran_id', $('#bc_pengeluaran_id').val());

                            if (id) {
                                // UPDATE
                                $.ajax({
                                    url: "<?= base_url("retur-po-lokal-bb/update"); ?>",
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
                                                    window.location.href = "<?= base_url("retur-po-lokal-bb") ?>";
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
                                    url: "<?= base_url("retur-po-lokal-bb/save"); ?>",
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
                                                    window.location.href = "<?= base_url("retur-po-lokal-bb") ?>";
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
    })

    function sumTotalRetur() {
        var total_retur = 0;
        $('.jumlah_retur').each(function() {
            var value = parseFloat($(this).val()) || 0;
            total_retur += value;
        });

        $('#txt_total_retur').text(total_retur);

    }

    function getDetailPenerimaanBarang() {
        var penerimaan_barang_id_element = $('#penerimaan_barang_id option:selected');
        $('#warehouse').val(penerimaan_barang_id_element.data('warehouse_name'));
        $('#supplier').val(penerimaan_barang_id_element.data('supplier_name'));
        // GET DETAIL
        getPenerimaanBarangDetail()
    }

    function getPenerimaanBarangDetail() {
        var bc_pengeluaran_id = $("#penerimaan_barang_id option:selected").data('bc_pengeluaran_id');
        $('#bc_pengeluaran_id').val(bc_pengeluaran_id).change();
        $.ajax({
            url: `<?= base_url('retur-po-lokal-bb/retur-detail'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                id: $('#id').val(),
                penerimaan_barang_id: $("#penerimaan_barang_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                listBarang = res.data;
                drawTable(listBarang);
            }
        });
    }

    function getPenerimaanBarang() {
        $.ajax({
            url: `<?= base_url('retur-po-lokal-bb/dropdown-penerimaan-barang'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $(".divisi_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $("#penerimaan_barang_id").empty()
                $("#penerimaan_barang_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $("#penerimaan_barang_id").append(`<option data-bc_pengeluaran_id="${item.bc_pengeluaran_id}" data-warehouse_name="${item.warehouse_name}" data-supplier_name="${item.supplier_name}" value="${item.id}">${item.no_penerimaan_barang}</option>`)
                })
                $("#penerimaan_barang_id").val();
            }
        });
    }

    function generateNumber() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $("#no_surat_jalan").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("retur-po-lokal-bb/generate-number"); ?>`,
                method: "GET",
                data: {
                    divisi_id: $('#divisi_id option:selected').val()
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $("#no_surat_jalan").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $("#no_surat_jalan").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $("#no_surat_jalan").val("");
                    }
                }
            })
        } else {
            $("#no_surat_jalan").attr("readonly", false);
            $("#no_surat_jalan").val("");
        }
    }

    function drawTable(listBarang) {
        const table = $('#dataTable');
        table.find('tbody').empty();
        if (listBarang.length == 0) {
            table.find('tfoot').empty();
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align:right;" colspan="8"><b>GRAND TOTAL</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>0</b></td>'));
            newRow.append($('<td style="text-align:left;"></td>'));
            newRow.append($('<td style="text-align:left;"><b>0</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>0</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>0</b></td>'));
            newRow.append($('<td></td>'));
            newRow.append($('<td></td>'));
            table.find('tfoot').append(newRow);

        } else {
            var no = 1;
            var totalJmlDiterima = 0;
            var totalHarga = 0;
            var totalSubTotal = 0;
            var totalJmlRetur = 0;
            var sumberBarang = "-";

            $.each(listBarang, function(i, v) {
                var newRow = $('<tr style="border:0;border-color:whitesmoke;">');
                newRow.append($('<td>').text(no++));
                newRow.append($('<td>').text(v.sumber));
                newRow.append($('<td>').text(v.kode_barang));
                newRow.append($('<td>').text(v.nama_barang));
                newRow.append($('<td>').text(v.no_aju));
                newRow.append($('<td>').text(v.no_daftar));
                newRow.append($('<td>').text(v.stock_date));
                newRow.append($('<td>').text(v.no_po));
                newRow.append($('<td>').text(v.jml_diterima));
                newRow.append($('<td>').text(v.kode_satuan));
                newRow.append($('<td>').text(greatFormatRupiah(v.harga)));
                newRow.append($('<td>').text(greatFormatRupiah(v.sub_total)));
                newRow.append($('<td style="text-align: center;">').html(
                    `
                        <input <?= isset($dataPengembalianBarang) ? ($dataPengembalianBarang['status_post'] == "FINISH" ? 'disabled' : '') : ""  ?> data-id="${v.id}" oninput="preventNegativeInput(this);sumTotalRetur();" style="height: 39px;" class="form-control jumlah_retur" type="text" value="${v.jml_retur}">
                    `
                ));
                newRow.append($('<td style="text-align: center;">').html(
                    `
                        <input <?= isset($dataPengembalianBarang) ? ($dataPengembalianBarang['status_post'] == "FINISH" ? 'disabled' : '') : ""  ?> data-id="${v.id}"  style="height: 39px;" class="form-control keterangan_retur" type="text" value="${v.ket_retur}">
                    `
                ));
                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <button type="button" <?= isset($dataPengembalianBarang) ? ($dataPengembalianBarang['status_post'] == "FINISH" ? 'disabled' : '') : ""  ?> class="btn btn-danger" onclick="deleteDetail(${v.id})" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                `
                ));

                table.find('tbody').append(newRow);
                totalJmlDiterima += Number(v.jml_diterima) || 0;
                totalHarga += Number(v.harga) || 0;
                totalSubTotal += Number(v.sub_total) || 0;
                totalJmlRetur += Number(v.jml_retur) || 0;
                sumberBarang = v.sumber;
            });
            table.find('tfoot').empty();
            var newRow = $('<tr style="border:0;border-color:whitesmoke;">');
            newRow.append($('<td style="text-align:right;" colspan="8"><b>GRAND TOTAL</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>' + totalJmlDiterima + '</b></td>'));
            newRow.append($('<td style="text-align:left;"></td>'));
            newRow.append($('<td style="text-align:left;"><b>' + greatFormatRupiah(totalHarga) + '</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>' + greatFormatRupiah(totalSubTotal) + '</b></td>'));
            newRow.append($('<td style="text-align:left;" id="txt_total_retur"><b>' + greatFormatRupiah(totalJmlRetur) + '</b></td>'));
            newRow.append($('<td></td>'));
            newRow.append($('<td></td>'));
            table.find('tfoot').append(newRow);

            // VALIDASI
            if (sumberBarang === "-") {
                Swal.fire({
                    icon: 'error',
                    title: "LPB yang anda pilih untuk retur belum dibuatkan dokumen pemasukan barang, silahkan dicek kembali",
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
                $('.btn-submit-parent').attr('disabled', true);
            } else {
                $('.btn-submit-parent').attr('disabled', false);
            }
        }
    }

    function deleteDetail(id) {
        var indexToRemove = -1;
        for (var i = 0; i < listBarang.length; i++) {
            if (Number(listBarang[i].id) == id) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listBarang.splice(indexToRemove, 1);
        }
        drawTable(listBarang);
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

    function posting(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Retur Pembelian ?',
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
                    url: "<?= base_url("retur-po-lokal-bb/posting"); ?>",
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
                                location.href = "<?= base_url('retur-po-lokal-bb') ?>"
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

    function unposting(id) {
        Swal.fire({
            icon: 'question',
            title: 'Unposting Retur Pembelian ?',
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
                    url: "<?= base_url("retur-po-lokal-bb/unposting"); ?>",
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
                                location.href = "<?= base_url('retur-po-lokal-bb') ?>"
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

    function remove(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Retur Pembelian ?',
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
                    url: "<?= base_url("retur-po-lokal-bb/delete"); ?>",
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
                                table.ajax.reload()
                            });
                        }
                    },
                });
            }
        })
    }

    function print(url) {
        window.open(url, "_blank");
    }
</script>


<?= $this->endSection(); ?>