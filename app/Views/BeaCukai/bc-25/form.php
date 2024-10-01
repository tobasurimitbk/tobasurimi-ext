<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<section class="section section-form">

    <div class="section-header">
        <h1 class="title-name"><?= !empty($bc25) ? "Update Dokumen BC 2.5" : "Tambah Dokumen BC 2.5" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right root-form-view" href="<?= base_url("bea-cukai-bc-25"); ?>">
                Kembali
            </a>
            <?php if (!empty($bc25)) : ?>
                <a class="btn btn-info btn-print float-right text-white" href="<?= base_url('bea-cukai-bc-25/id/header/' . encrypt($bc25['id'])) ?>">
                    Form Ceisa
                </a>
                <?php if ($bc25['status_posting'] == "0") : ?>
                    <?php if (can('Bea Cukai', 'BC 2.5', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="deleteAction()">
                            Hapus
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($bc25['status_posting'] == "0") : ?>
                    <?php if (can('Bea Cukai', 'BC 2.5', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="postingAction()">
                            Posting
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($bc25['status_posting'] == "0") : ?>
                    <?php if (can('Bea Cukai', 'BC 2.5', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else : ?>
                <?php if (can('Bea Cukai', 'BC 2.5', 'c')) : ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="font-weight: bold; color:black;">
            DATA BARANG UNTUK PEMBUATAN DOKUMEN BEA CUKAI 2.5
        </div>
        <div class="card-body">
            <form class="create-form">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="id" class="id" value="<?= !empty($bc25) ? encrypt($bc25['id']) : '' ?>">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-floating mb-3 mt-1" style="height: 50px;">
                            <select <?= !empty($bc25) ? ($bc25['status_posting'] == "1" ? "disabled" : "") : '' ?> class="form-select asal_pengeluaran" id="asal_pengeluaran" name="asal_pengeluaran">
                                <option value=""></option>
                                <option <?= !empty($bc25) ? ($bc25['pengembalian_barang_id'] != null ? 'selected' : '') : '' ?> value="RETUR">RETUR</option>
                                <option <?= !empty($bc25) ? ($bc25['sales_order_lain_id'] != null ? 'selected' : '') : '' ?> value="PENJUALAN">PENJUALAN</option>
                            </select>
                            <label style="z-index: 1;">Pilih Asal Pengeluaran</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3 mt-1" style="height: 50px;">
                            <select <?= !empty($bc25) ? ($bc25['status_posting'] == "1" ? "disabled" : "") : '' ?> class="form-select reference_id" id="reference_id" name="reference_id">
                                <option value=""></option>
                                <?php if (isset($reference)): ?>
                                    <?php foreach ($reference as $s) : ?>
                                        <option <?= (!empty($bc25) ? ($bc25['reference_id'] == $s['id'] ? 'selected' : '') : '') ?> data-nama_penerima="<?= $s['nama_penerima'] ?>" data-alamat_penerima="<?= $s['alamat_penerima'] ?>" data-tanggal_reference="<?= $s['tanggal_reference'] ?>" data-divisi="<?= $s['divisi'] ?>" data-warehouse_name="<?= $s['warehouse_name'] ?>" data-keterangan="<?= $s['keterangan'] ?>" value="<?= $s['id'] ?>">
                                            <?= $s['no_reference'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label style="z-index: 1;">Pilih Nomor Reference</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled placeholder="Nama Penerima" value="<?= !empty($bc25) ? $bc25['nama_penerima'] : '' ?>" class="form-control nama_penerima" id="nama_penerima" />
                            <label for="floatingInput" style="z-index: 1;">Nama Penerima</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled placeholder="Company Asal" value="<?= !empty($bc25) ? $bc25['alamat_penerima'] : '' ?>" class="form-control alamat_penerima" id="alamat_penerima" />
                            <label for="floatingInput" style="z-index: 1;">Alamat</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <div class="form-floating mb-3">
                                <input value="<?= !empty($bc25) ? $bc25['no_aju'] : $noAju ?>" name="no_aju" readonly type="text" id="no_aju" class="form-control no_aju" placeholder="">
                                <label>Nomor Pengajuan</label>
                            </div>
                            <div class="input-group-append" style="height:50px;">
                                <button <?= !empty($bc25) ? ($bc25['status_posting'] == "1" ? "disabled" : "") : '' ?> class="btn btn-success btn-customer-add" id="btn-customer-add" data-toggle="modal" type="button" onclick="noAjuShowModal()">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3">
                            <input <?= !empty($bc25) ? ($bc25['status_posting'] == "1" ? "disabled" : "") : '' ?> value="<?= !empty($bc25) ? $bc25['no_daftar'] : "" ?>" autocomplete="one-time-code" type="number" class="form-control no_daftar" id="no_daftar" name="no_daftar" placeholder="No Daftar">
                            <label for="floatingInput">Nomor Daftar</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3">
                                <input disabled <?= !empty($bc25) ? ($bc25['status_posting'] == "1" ? "disabled" : "") : '' ?> value="<?= !empty($bc25) ? date('d/m/Y', strtotime($bc25['tanggal_reference'])) : "" ?>" autocomplete="one-time-code" type="text" class="form-control tanggal_reference" id="tanggal_reference" name="tanggal_reference" placeholder="Tanggal Reference">
                                <label for="floatingInput">Tanggal Reference</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3">
                            <input disabled <?= !empty($bc25) ? ($bc25['status_posting'] == "1" ? "disabled" : "") : '' ?> value="<?= !empty($bc25) ? $bc25['divisi'] : "" ?>" autocomplete="one-time-code" type="text" class="form-control divisi" id="divisi" name="divisi" placeholder="Departemen">
                            <label for="floatingInput">Departemen</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3">
                            <input disabled <?= !empty($bc25) ? ($bc25['status_posting'] == "1" ? "disabled" : "") : '' ?> value="<?= !empty($bc25) ? $bc25['warehouse_name'] : "" ?>" autocomplete="one-time-code" type="text" class="form-control warehouse_name" id="warehouse_name" name="warehouse_name" placeholder="Warehouse">
                            <label for="floatingInput">Warehouse</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3">
                            <input disabled <?= !empty($bc25) ? ($bc25['status_posting'] == "1" ? "disabled" : "") : '' ?> value="<?= !empty($bc25) ? $bc25['keterangan'] : "" ?>" autocomplete="one-time-code" type="text" class="form-control keterangan" id="keterangan" name="keterangan" placeholder="Keterangan">
                            <label for="floatingInput">Keterangan</label>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Daftar Barang yang Akan Keluar</label>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable1" width="100%" border="1" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;" colspan="8">Detail Barang yang Keluar</th>
                                    <th style="text-align: center;" colspan="1">Data Harga</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">Asal Barang</th>
                                    <th style="text-align: center;">No Dokumen</th>
                                    <th style="text-align: center;">Supplier</th>
                                    <th style="text-align: center;">Dokumen Asal</th>
                                    <th style="text-align: center;">Tgl Penerimaan</th>
                                    <th style="text-align: center;">Barang - Spesifikasi</th>
                                    <th style="text-align: center;">Qty Keluar</th>
                                    <th style="text-align: center;">Total Harga</th>
                                </tr>
                            </thead>
                            <tbody class="body-table">
                            </tbody>
                            <tfoot class="foot-detail-table" id="foot-detail-table">
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
<div class="modal fade" id="modalUpdateNoAju" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Ubah Nomor Pengajuan</h5>
            </div>
            <form id="form-update">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6 mt-1">
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
                        <div class="col-sm-6 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="no_urut_dokumen" name="no_urut_dokumen" type="number" class="no_urut_dokumen form-control" placeholder="" oninput="event.target.value = /^\d{0,6}$/.test(event.target.value) ? event.target.value : ''">
                                <label>Nomor Urut</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="kode_kantor" name="kode_kantor" type="number" class="kode_kantor form-control" placeholder="" oninput="event.target.value = /^\d{0,6}$/.test(event.target.value) ? event.target.value : ''">
                                <label>Kode Kantor</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="no_pengajuan" value="<?= !empty($bc25) ? $bc25['no_aju'] : $noAju ?>" name="no_pengajuan" type="text" readonly class="no_pengajuan form-control" placeholder="">
                                <label>Preview Nomor Pengajuan</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3 btn-discard-modal" data-bs-dismiss="modal">Kembali</button>
                    <?php if (!empty($bc25)) : ?>
                        <?php if ($bc25['status_posting'] === "0") : ?>
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

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    var listData = [];

    // INIT PAS EDIT
    <?php if (!empty($bc25)) : ?>
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-25/list-reference-detail'); ?>`,
            method: "GET",
            data: {
                reference_id: "<?= $bc25['reference_id'] ?>",
                asal_pengeluaran: $('#asal_pengeluaran option:selected').val(),
            },
            dataType: "json",
            success: function(res) {
                listData = [];
                listData = res.data;
                // DRAWTABLE
                drawTable(listData);
            }
        });
    <?php endif; ?>

    $('#asal_pengeluaran').select2({
        placeholder: "Pilih Asal Pengeluaran",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        var asal_pengeluaran = $('#asal_pengeluaran option:selected').val();
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-25/list-reference'); ?>`,
            method: "GET",
            data: {
                asal_pengeluaran: asal_pengeluaran,
            },
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            dataType: "json",
            success: function(res) {
                $("#reference_id").empty()
                $("#reference_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $("#reference_id").append(`<option data-nama_penerima="${item.nama_penerima}" data-alamat_penerima="${item.alamat_penerima}" data-tanggal_reference="${item.tanggal_reference}" data-divisi="${item.divisi}" data-warehouse_name="${item.warehouse_name}" data-keterangan="${item.keterangan}" value="${item.id}">${item.no_reference}</option>`)
                })
                $("#reference_id").val();
            }
        });
    });

    $('#reference_id').select2({
        placeholder: "Pilih Nomor Reference",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        var selected = $('#reference_id option:selected');
        $('#nama_penerima').val(selected.data('nama_penerima'));
        $('#alamat_penerima').val(selected.data('alamat_penerima'));
        $('#tanggal_reference').val(selected.data('tanggal_reference'));
        $('#divisi').val(selected.data('divisi'));
        $('#warehouse_name').val(selected.data('warehouse_name'));
        $('#keterangan').val(selected.data('keterangan'));
        // GET BARANG
        getListBarang();
    });

    // VALIDATOR
    var validator = $(".create-form").validate({
        rules: {
            reference_id: {
                required: true
            },
            no_aju: {
                required: true
            },
            no_daftar: {
                required: true
            },
        },
        messages: {
            reference_id: {
                required: "Pilih sales order"
            },
            no_aju: {
                required: "No aju wajib diisi"
            },
            no_daftar: {
                required: "No daftar wajib diisi"
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

    $("#reference_id,#asal_pengeluaran")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.btn-submit-parent').click(function(e) {
        e.preventDefault();
        if ($('.create-form').valid()) {
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
                    var formData = new FormData(document.querySelector(".create-form"));
                    var id = $('#id').val();
                    if (id) {
                        // UPDATE
                        $.ajax({
                            url: `<?= base_url("bea-cukai-bc-25/update"); ?>`,
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
                                        location.href = "<?= base_url('bea-cukai-bc-25') ?>"
                                    });

                                }
                            }
                        })
                    } else {
                        // CREATE
                        $.ajax({
                            url: `<?= base_url("bea-cukai-bc-25/save"); ?>`,
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
                                        location.href = "<?= base_url('bea-cukai-bc-25') ?>"
                                    });

                                }
                            }
                        })
                    }
                }
            })

        }
    });

    $('.btn-discard-modal').click(function(e) {
        e.preventDefault();
        $('#modalUpdateNoAju').modal('hide');
    })

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
        $('#no_pengajuan').val(noPengajuanSplit[0] + '-' + noPengajuanSplit[1] + '-' + tanggalPengajuanSplit[2] + '' + tanggalPengajuanSplit[1] + '' + tanggalPengajuanSplit[0] + '-' + noPengajuanSplit[3]);
    });

    $('#ubahNoAjuButton').click(function(e) {
        e.preventDefault();
        checkNoAju();
    });

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


    function checkNoAju() {
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-25/check-no-aju'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                id: $("#id").val(),
                no_aju: $('#no_pengajuan').val()
            },
            dataType: "json",
            success: function(res) {
                if (res.status == false) {
                    Swal.fire({
                        icon: 'error',
                        title: res.message,
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // ERROR
                        }
                    })
                } else {
                    // APPEND
                    var noPengajuan = $('#no_pengajuan').val();
                    $('#no_aju').val(noPengajuan);
                    $('#modalUpdateNoAju').modal('hide');
                }
            }
        });
    }

    function getListBarang() {
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-25/list-reference-detail'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                asal_pengeluaran: $('#asal_pengeluaran option:selected').val(),
                reference_id: $('#reference_id option:selected').val()
            },
            dataType: "json",
            success: function(res) {
                listData = [];
                listData = res.data;
                // DRAWTABLE
                drawTable(listData);
            }
        });
    }

    function drawTable(listData) {
        var no = 1;
        const table = $('#dataTable1');
        table.find('tbody').empty();
        table.find('tfoot').empty();

        if (listData.length == 0) {
            var newRow = $('<tr>');
            newRow.append($('<td colspan="9" style="text-align:center">Tidak Ada Barang</td>'));
            table.find('tfoot').append(newRow);
        } else {
            var totalHarga = 0;
            var sumberBarang = "-";
            $.each(listData, function(i, v) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align: center;">').html(
                    `
                   ${no++} 
                `
                ));
                newRow.append($('<td style="text-align: center;">').text(v.sumber));
                newRow.append($('<td style="text-align: center;">').text(v.stock_dokumen));
                newRow.append($('<td style="text-align: center;">').text(v.supplier_name));
                newRow.append($('<td style="text-align: center;">').text(v.bc_type + '/' + v.no_aju));
                newRow.append($('<td style="text-align: center;">').text(v.stock_date));
                newRow.append($('<td style="text-align: center;">').text(v.kode_barang + " " + v.barang));
                newRow.append($('<td style="text-align: center;">').text(v.qty_konversi + " " + v.satuan));
                newRow.append($('<td style="text-align: center;">').text(formatRupiah(v.total_harga)));
                table.find('tbody').append(newRow);

                totalHarga = totalHarga + parseFloat(v.total_harga);
                sumberBarang = v.sumber;
            });
            // GRAND TOTAL
            var newRow = $('<tr style="color:whitesmoke; background-color:#f2c996">');
            newRow.append($('<td style="text-align: right;" colspan="8">').html("<b>GRAND TOTAL</b>"));
            newRow.append($('<td style="text-align: center;">').text(formatRupiah(totalHarga)));
            table.find('tbody').append(newRow);

            if (sumberBarang === "-") {
                Swal.fire({
                    icon: 'error',
                    title: "LPB yang anda pilih belum dibuatkan dokumen pemasukan barang, silahkan dicek kembali",
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
                $('.btn-submit-parent').attr('disabled', true);
            } else {
                $('.btn-submit-parent').attr('disabled', false);
            }
        }
    }

    function deleteAction() {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Dokumen BC 2.5 ?',
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
                    url: `<?= base_url("bea-cukai-bc-25/delete"); ?>`,
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
                                    location.href = "<?= base_url('bea-cukai-bc-25') ?>"
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
            title: 'Posting Dokumen BC 2.5 ?',
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
                    url: `<?= base_url("bea-cukai-bc-25/posting"); ?>`,
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
                                    location.href = "<?= base_url('bea-cukai-bc-25') ?>"
                                }
                            });
                        }
                    }
                })
            }
        })
    }

    function formatRupiah(angka) {
        var formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        });
        var parsedNumber = parseFloat(angka);
        if (isNaN(parsedNumber)) {
            return "0,00";
        }
        return formatter.format(parsedNumber).replace('Rp', '').trim();
    }
</script>

<?= $this->endSection(); ?>