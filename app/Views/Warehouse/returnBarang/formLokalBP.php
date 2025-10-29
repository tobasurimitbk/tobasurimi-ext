<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1 class="title-name">Retur Purchase Lokal Barang Bahan Penolong</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("retur-po-lokal-bp"); ?>">
                Kembali
            </a>
            <?php if (!empty($dataPengembalianBarang)) : ?>
                <?php if (can('Retur Pembelian', 'Retur Lokal BP', 'p')) : ?>
                    <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("retur-po-lokal-bp/print/"); ?><?= encrypt($dataPengembalianBarang['id']); ?>')">
                        Print
                    </button>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (!empty($dataPengembalianBarang)) : ?>
                <?php if ($dataPengembalianBarang['status_post'] === "WAITING") : ?>
                    <?php if (can('Retur Pembelian', 'Retur Lokal BP', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="remove(' <?= encrypt($dataPengembalianBarang['id']) ?>')">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Retur Pembelian', 'Retur Lokal BP', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-lpb" onclick="posting(' <?= encrypt($dataPengembalianBarang['id']) ?>')">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Retur Pembelian', 'Retur Lokal BP', 'u')) : ?>
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
                <input type="hidden" name="type_return" id="type_return" value="LOKAL PENOLONG">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" <?= !empty($dataPengembalianBarang) ? (($dataPengembalianBarang['status_post'] !== "WAITING") ? "readonly" : "") : ""; ?> value="<?= !empty($dataPengembalianBarang) ? $dataPengembalianBarang['no_surat_jalan'] : ""; ?>" type="text" class="form-control no_surat_jalan" id="no_surat_jalan" name="no_surat_jalan" placeholder="No. Surat Jalan">
                                    <label for="floatingInput">No. Surat Jalan</label>
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
                            <select <?= isset($dataPengembalianBarang) ? ($dataPengembalianBarang['status_post'] == "FINISH" ? 'disabled' : '') : ""  ?> class="form-select supplier_id" id="supplier_id" name="supplier_id">
                                <option value=""></option>
                                <?php foreach ($dataSupplier as $d) : ?>
                                    <option value="<?= $d["id"]; ?>" <?= !empty($dataPengembalianBarang) ? ($dataPengembalianBarang['supplier_id'] === $d["id"] ? "selected" : "") : ""; ?>><?= $d["name"]; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Supplier</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select multiple <?= isset($dataPengembalianBarang) ? ($dataPengembalianBarang['status_post'] == "FINISH" ? 'disabled' : '') : ""  ?> class="form-select penerimaan_barang_id" id="penerimaan_barang_id">
                                <?php if (isset($dataPenerimaanBarang)): ?>
                                    <?php foreach ($dataPenerimaanBarang as $d): ?>
                                        <option value="<?= $d['id'] ?>" <?= in_array($d['id'], json_decode($dataPengembalianBarang['multiple_lpb_id'])) ? 'selected' : '' ?>>
                                            <?= $d['no_penerimaan_barang'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;"></label>
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
                        <label class="form-label font-weight-bold modal-sub-title">List Barang yang Diretur</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>No SPP</th>
                                <th>No LPB</th>
                                <th>Kode</th>
                                <th>Barang</th>
                                <th>Spesifikasi</th>
                                <th>Jml Diterima</th>
                                <th>Satuan</th>
                                <th>Harga</th>
                                <th>Sub Total</th>
                                <th>Jml Retur</th>
                                <th>Total Harga Retur</th>
                                <th>Ket Retur</th>
                                <th style="text-align:center;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot class="foot-detail-table" id="foot-detail-table">
                            <tr>
                                <td colspan="10" style="text-align: right;"><b>GRAND TOTAL</b></td>
                                <td>0</td>
                                <td>0</td>
                                <td></td>
                                <td></td>
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
        listBarang = <?= json_encode($dataPengembalianBarangDetail) ?>;
        drawTable(listBarang);
    <?php endif; ?>

    $("#tanggal_retur_barang").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true,
        enableOnReadonly: false
    });

    $('#supplier_id').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function() {
        getPenerimaanBarang();
    });

    $('#penerimaan_barang_id').select2({
        placeholder: "Pilih Nomor Penerimaan Barang",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function(e) {
        e.preventDefault();
        getPenerimaanBarangDetail();
    });

    $("#supplier_id,#penerimaan_barang_id")
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

    $(document).on('select2:open', function(e) {
        // Pastikan ini adalah elemen yang kita inginkan (form-select)
        const target = e.target;
        if ($(target).hasClass('form-select')) {
            // Cari input search yang baru saja dibuka
            let searchInput = document.querySelector('.select2-container--open .select2-search__field');
            if (searchInput) {
                searchInput.focus();
            }
        }
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
                        let url = id == '' ? '<?= base_url("retur-po-lokal-bp/save"); ?>' : '<?= base_url("retur-po-lokal-bp/update"); ?>';
                        data.append('listBarang', JSON.stringify(listBarang));
                        $.ajax({
                            url: url,
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
                                            window.location.href = "<?= base_url("retur-po-lokal-bp") ?>";
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
                });
            }

        }
    });


    // =============================
    // 💰 Fungsi Hitung Total Retur
    // =============================
    function sumTotalRetur() {
        var total_jml_retur = 0;
        var total_harga_retur = 0;

        $('.jumlah_retur').each(function() {
            var $row = $(this).closest('tr');
            var id = $(this).data('id'); // ambil id barang
            var jumlah = destroyFormatRupiah($(this).val()) || 0;

            // Ambil kolom jumlah diterima (7) dan harga satuan (9)
            var jumlahDiterimaText = $row.find('td:nth-child(7)').text().trim();
            var hargaText = $row.find('td:nth-child(9)').text().trim();

            var jumlahDiterima = destroyFormatRupiah(jumlahDiterimaText) || 0;
            var harga = destroyFormatRupiah(hargaText) || 0;

            // Validasi: jumlah retur tidak boleh melebihi jumlah diterima
            if (jumlah > jumlahDiterima) {
                jumlah = jumlahDiterima;
                $(this).val(greatFormatRupiah(jumlah));
            }

            // Hitung total harga retur per baris
            var total = jumlah * harga;

            // Update kolom total harga retur
            $row.find('.total_harga_return').text(greatFormatRupiah(total));

            // Update array listBarang sesuai input
            var keterangan = $row.find('.keterangan_return').val() || '';
            for (var i = 0; i < listBarang.length; i++) {
                if (listBarang[i].id == id) {
                    listBarang[i].jumlah_return = jumlah;
                    listBarang[i].total_harga_return = total;
                    listBarang[i].keterangan_return = keterangan;
                    break;
                }
            }

            // Akumulasi total
            total_jml_retur += jumlah;
            total_harga_retur += total;
        });

        // Update GRAND TOTAL di footer
        $('#txt_total_retur').html('<b>' + greatFormatRupiah(total_jml_retur) + '</b>');
        $('#txt_sub_total_retur').html('<b>' + greatFormatRupiah(total_harga_retur) + '</b>');
    }

    // Pasang event listener untuk update realtime
    $(document).on('keyup change', '.jumlah_retur, .keterangan_return', function(e) {
        e.preventDefault();
        sumTotalRetur();
    });

    // =============================
    // 🧾 Fungsi Render Tabel Barang
    // =============================
    function drawTable(listBarang) {
        const table = $('#dataTable');
        table.find('tbody').empty();

        if (listBarang.length == 0) {
            table.find('tfoot').empty();
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align:right;" colspan="10"><b>GRAND TOTAL</b></td>'));
            newRow.append($('<td style="text-align:left;" id="txt_total_retur"><b>0</b></td>'));
            newRow.append($('<td style="text-align:left;" id="txt_sub_total_retur"><b>0</b></td>'));
            newRow.append($('<td></td>'));
            newRow.append($('<td></td>'));
            table.find('tfoot').append(newRow);
            return;
        }

        var no = 1;
        var totalJmlRetur = 0;
        var totalHargaRetur = 0;

        $.each(listBarang, function(i, v) {
            var newRow = $('<tr style="border:0;border-color:whitesmoke;">');

            newRow.append($('<td>').text(no++));
            newRow.append($('<td>').text(v.spp_no));
            newRow.append($('<td>').text(v.no_penerimaan_barang));
            newRow.append($('<td>').text(v.kode_barang));
            newRow.append($('<td>').text(v.barang_name));
            newRow.append($('<td>').text(v.spesifikasi));
            newRow.append($('<td>').text(greatFormatRupiah(v.jml_masuk))); // Kolom 7
            newRow.append($('<td>').text(v.kode_satuan));
            newRow.append($('<td>').text(greatFormatRupiah(v.harga_lpb))); // Kolom 9
            newRow.append($('<td>').text(greatFormatRupiah(v.sub_total_lpb)));
            newRow.append($('<td>').html(`
            <input
                <?= isset($dataPengembalianBarang) ? ($dataPengembalianBarang['status_post'] == "FINISH" ? 'disabled' : '') : "" ?>
                data-id="${v.id}"
                style="height: 39px;"
                class="form-control jumlah_retur"
                type="text"
                value="${greatFormatRupiah(v.jumlah_return)}"
            >
        `));
            newRow.append($('<td class="total_harga_return" data-id="' + v.id + '">')
                .text(greatFormatRupiah(v.total_harga_return)));
            newRow.append($('<td>').html(`
            <input
                <?= isset($dataPengembalianBarang) ? ($dataPengembalianBarang['status_post'] == "FINISH" ? 'disabled' : '') : "" ?>
                data-id="${v.id}"
                style="height: 39px;"
                class="form-control keterangan_return"
                type="text"
                value="${v.keterangan_return}"
            >
        `));
            newRow.append($('<td>').html(`
            <button type="button"
                <?= isset($dataPengembalianBarang) ? ($dataPengembalianBarang['status_post'] == "FINISH" ? 'disabled' : '') : "" ?>
                class="btn btn-danger"
                onclick="deleteDetail(${v.id})">
                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
            </button>
        `));

            table.find('tbody').append(newRow);

            totalHargaRetur += Number(v.total_harga_return);
            totalJmlRetur += Number(v.jumlah_return) || 0;
        });

        // Footer total
        table.find('tfoot').empty();
        var newRow = $('<tr style="border:0;border-color:whitesmoke;">');
        newRow.append($('<td style="text-align:right;" colspan="10"><b>GRAND TOTAL</b></td>'));
        newRow.append($('<td style="text-align:left;" id="txt_total_retur"><b>' + greatFormatRupiah(totalJmlRetur) + '</b></td>'));
        newRow.append($('<td style="text-align:left;" id="txt_sub_total_retur"><b>' + greatFormatRupiah(totalHargaRetur) + '</b></td>'));
        newRow.append($('<td></td>'));
        newRow.append($('<td></td>'));
        table.find('tfoot').append(newRow);
    }



    function getPenerimaanBarangDetail() {
        var penerimaan_barang_id = $('#penerimaan_barang_id').val();
        console.log(penerimaan_barang_id);
        var penerimaan_barang_ids = penerimaan_barang_id == "" ? "" : JSON.stringify(penerimaan_barang_id);
        $.ajax({
            url: `<?= base_url('retur-po-lokal-bp/retur-detail'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                id: $('#id').val(),
                penerimaan_barang_id: penerimaan_barang_ids,
            },
            dataType: "json",
            success: function(res) {
                if (res.status == false) {
                    Swal.fire({
                        icon: 'error',
                        title: res.message,
                        confirmButtonColor: '#4e73df',
                    });
                    return;
                } else {
                    listBarang = res.data;
                    drawTable(listBarang);
                }

            }
        });
    }

    function getPenerimaanBarang() {
        $.ajax({
            url: `<?= base_url('retur-po-lokal-bp/dropdown-penerimaan-barang'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                supplier_id: $("#supplier_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $("#penerimaan_barang_id").empty()
                res.data.forEach(function(item) {
                    $("#penerimaan_barang_id").append(`
                    <option
                    value="${item.id}">${item.no_penerimaan_barang}</option>`)
                })
                $("#penerimaan_barang_id").val(null).change();
            }
        });
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
        removeLpbList(listBarang);
        drawTable(listBarang);
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
                    url: "<?= base_url("retur-po-lokal-bp/posting"); ?>",
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
                                location.href = "<?= base_url('retur-po-lokal-bp') ?>"
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
                    url: "<?= base_url("retur-po-lokal-bp/unposting"); ?>",
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
                                location.href = "<?= base_url('retur-po-lokal-bp') ?>"
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
                    url: "<?= base_url("retur-po-lokal-bp/delete"); ?>",
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
                                location.href = "<?= base_url('retur-po-lokal') ?>"
                            });
                        }
                    },
                });
            }
        })
    }

    function removeLpbList(listBarang) {
        var penerimaanBarangId = $('#penerimaan_barang_id option:selected').val();
        var penerimaanBarangIdList = [];
        for (let i = 0; i < listBarang.length; i++) {
            penerimaanBarangIdList.push(listBarang[i].penerimaan_barang_id);
        }

        penerimaanBarangIdListUnique = [...new Set(listBarang.map(item => item.penerimaan_barang_id))];
        $('#penerimaan_barang_id').val(penerimaanBarangIdListUnique).trigger('change.select2');

    }

    function print(url) {
        window.open(url, "_blank");
    }
</script>


<?= $this->endSection(); ?>