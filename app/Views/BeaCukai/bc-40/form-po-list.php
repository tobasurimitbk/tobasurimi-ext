<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<section class="section section-form">

    <div class="section-header">
        <h1 class="title-name">Dokumen BC 4.0</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right root-form-view" href="<?= base_url("bea-cukai-bc-40"); ?>">
                Kembali
            </a>
            <a class="btn btn-info btn-print float-right text-white" href="<?= base_url('bea-cukai-bc-40/id/header/' . encrypt($bcPo['id'])) ?>">
                Form Ceisa
            </a>
            <?php if ($bcPo['status_posting'] === "0") : ?>
                <button class="btn btn-hapus delete-parent float-right" onclick="deleteAction()">
                    Hapus
                </button>
                <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="posting()">
                    Posting
                </button>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="font-weight: bold;  <?= session()->get('theme') == 'dark' ? 'color:white;' : 'color:black;' ?>">
            DATA BARANG UNTUK PEMBUATAN DOKUMEN BEA CUKAI 4.0
        </div>
        <div class="card-body">
            <form class="create-form">
                <input type="hidden" name="bc_purchase_order_id" value="<?= $bcPo['id'] ?>">
                <input type="hidden" name="po_type" value="<?= $bcPo['po_type']; ?>">
                <input type="hidden" name="supplier_id" value="<?= $bcPo['supplier_id']; ?>">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-floating mb-3 mt-1" style="height: 50px;">
                            <select disabled class="form-select" id="po_type" name="" aria-label="Floating label select example">
                                <option value=""></option>
                                <option <?= $bcPo['po_type'] == "LOKAL BAKU" ? 'selected' : '' ?> value="LOKAL BAKU">PO LOKAL BAHAN BAKU</option>
                                <option <?= $bcPo['po_type'] == "LOKAL PENOLONG" ? 'selected' : '' ?> value="LOKAL PENOLONG">PO LOKAL BAHAN PENOLONG</option>

                            </select>
                            <label style="z-index: 1;">Tipe Purchase Order</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3 mt-1" style="height: 50px;">
                            <select disabled class="form-select" id="supplier_id" name="" aria-label="Floating label select example">
                                <option selected value="<?= $bcPo['supplier_id'] ?>">
                                    <?= $bcPo['supplier_name'] ?>
                                </option>
                            </select>
                            <label style="z-index: 1;">Supplier</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <div class="form-floating mb-3">
                                <input value="<?= $noAju ?>" readonly type="text" class="form-control" placeholder="">
                                <label>Nomor Pengajuan</label>
                            </div>
                            <div class="input-group-append" style="height:50px;">
                                <button <?= $bcPo['status_posting'] === "1" ? 'disabled' : '' ?> class="btn btn-success btn-customer-add" id="btn-customer-add" data-toggle="modal" type="button" onclick="noAjuShowModal()">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3">
                            <input <?= $bcPo['status_posting'] === "1" ? 'disabled' : '' ?> value="<?= $bcPo['no_daftar'] ?>" autocomplete="one-time-code" type="number" class="form-control no_daftar" id="no_daftar" name="no_daftar" placeholder="No Daftar">
                            <label for="floatingInput">Nomor Daftar</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3">
                                <input <?= !empty($bcPo) ? ($bcPo['status_posting'] == "1" ? "disabled" : "") : '' ?> value="<?= !empty($bcPo) ? date('d/m/Y', strtotime($bcPo['createdAt'])) : "" ?>" autocomplete="one-time-code" type="text" class="form-control tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dokumen">
                                <label for="floatingInput">Tanggal Dokumen</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 21px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <?php if ($bcPo['status_posting'] === "0") : ?>
                <div class="row mt-3">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Daftar Purchase Order yang Belum Dibuat Dokumen Bea Cukai</label>
                        <div class="row mt-3">
                            <div class="col-sm-3">
                                <div class="form-floating mb-2 mt-1" style="height: 50px;">
                                    <div class="input-group input-group-password">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" class="form-control input-picker start_date" id="start_date" name="start_date" placeholder="Tanggal Mulai PO" value="">
                                            <label for="floatingInput">Filter Tanggal Mulai PO</label>
                                        </div>
                                        <div class="input-group-prepend group-prepend-password align-items-center">
                                            <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-floating mt-1" style="height: 50px;">
                                    <div class="input-group input-group-password">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" class="form-control input-picker end_date" id="end_date" name="end_date" placeholder="Tanggal Selesai PO" value="<?= date('d/m/Y') ?>">
                                            <label for="floatingInput">Filter Tanggal Selesai PO</label>
                                        </div>
                                        <div class="input-group-prepend group-prepend-password align-items-center">
                                            <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-table-button-tts">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable dataTable1" id="dataTable1" width="100%" border="1" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center; width:5px;">No</th>
                                        <th style="text-align: center; width:5px;">#</th>
                                        <th style="text-align: center;">Tgl PO</th>
                                        <th style="text-align: center;">Tgl LPB</th>
                                        <th style="text-align: center;">No LPB</th>
                                        <th style="text-align: center;">No PO</th>
                                        <th style="text-align: center;">Kode</th>
                                        <th style="text-align: center;">Barang</th>
                                        <th style="text-align: center;">Qty PO</th>
                                        <th style="text-align: center;">Qty Diterima</th>
                                        <th style="text-align: center;">Harga</th>
                                    </tr>
                                </thead>
                                <tbody class="body-table">
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary mt-3" id="select-item-btn">Pilih</button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row mt-2">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Daftar Purchase Order yang Akan Dibuat Dokumen Bea Cukai</label>
                </div>
                <div class="col-md-12 col-table-button-tts">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable dataTable2" id="dataTable2" width="100%" border="1" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center; width:5px;">No</th>
                                    <th style="text-align: center;">Tgl PO</th>
                                    <th style="text-align: center;">Tgl LPB</th>
                                    <th style="text-align: center;">No LPB</th>
                                    <th style="text-align: center;">No PO</th>
                                    <th style="text-align: center;">Kode</th>
                                    <th style="text-align: center;">Barang</th>
                                    <th style="text-align: center;">Qty PO</th>
                                    <th style="text-align: center;">Qty Diterima</th>
                                    <th style="text-align: center;">Qty Diterima (Konversi)</th>
                                    <th style="text-align: center;">Harga</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="body-table">
                            </tbody>
                            <tfoot class="foot-detail-table" id="foot-detail-table">
                                <tr>
                                    <td colspan="11" style="text-align: center;">
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
                <input type="hidden" name="bc_purchase_order_id" class="bc_purchase_order_id" id="bc_purchase_order_id" value="<?= encrypt($bcPo['id']) ?>">
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
                        <div class="col-sm-6 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="no_pengajuan" value="<?= $noAju ?>" name="no_pengajuan" type="text" readonly class="no_pengajuan form-control" placeholder="">
                                <label>Preview Nomor Pengajuan</label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3" data-bs-dismiss="modal">Kembali</button>
                    <?php if ($bcPo['status_posting'] === "0") : ?>
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
    var listDataSelected = [];

    getListPurchaseOrderNotUsed();

    <?php foreach ($daftarPoUsed as $d) : ?>
        listDataSelected.push({
            penerimaan_barang_id: "<?= $d['penerimaan_barang_id'] ?>",
            penerimaan_barang_detail_id: "<?= $d['penerimaan_barang_detail_id'] ?>",
            barang1_id: "<?= $d['barang1_id'] ?>",
            lpb_date: "<?= $d['lpb_date'] ?>",
            lpb_no: "<?= $d['lpb_no'] ?>",
            purchase_order_id: "<?= $d['purchase_order_id'] ?>",
            qty_lpb: "<?= ($d['qty_lpb']) ?>",
            qty_lpb_konversi: "<?= ($d['qty_lpb_konversi']) ?>",
            qty_po: "<?= ($d['qty_po']) ?>",
            barang_id: "<?= $d['barang_id'] ?>",
            po_no: "<?= $d['po_no'] ?>",
            po_date: "<?= $d['po_date'] ?>",
            barang_name: "<?= $d['barang_name'] ?>",
            kode_barang: "<?= $d['kode_barang'] ?>",
            harga_number: "<?= $d['harga'] ?>",
        })
        drawTablePurchaseOrderUsed(listDataSelected);
    <?php endforeach; ?>

    $('#po_type').select2({
        placeholder: "Pilih Tipe Purchase Order",
        theme: "bootstrap-5",
    }).change(function() {});

    $('#supplier_id').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5",
    }).change(function() {});

    $(".start_date,.end_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#start_date,#end_date').change(function() {
        getListPurchaseOrderNotUsed();

    })

    var dataTable1 = $('#dataTable1').DataTable({

        processing: false,
        serverSide: false,
        ordering: true,
        order: [],
        fixedHeader: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: true,
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('#select-item-btn').click(function() {
        var checkedCheckboxes = $(".child:checked");
        var dataIds = checkedCheckboxes.map(function() {
            return $(this).data("penerimaan_barang_id");
        }).get();

        var id_selected = getIDListDataSelected();

        $.each(listData, function(i, v) {
            var currentID = Number(v.penerimaan_barang_id);

            if ($.inArray(currentID, dataIds) !== -1) {
                var isIDSelected = $.grep(listDataSelected, function(item) {
                    return item.penerimaan_barang_id == Number(currentID);
                }).length > 0;

                if (!isIDSelected) {
                    listDataSelected.push(listData[i]);
                }
            }
        });
        drawTablePurchaseOrderUsed(listDataSelected);
    });

    var validator = $(".create-form").validate({
        rules: {
            no_daftar: {
                required: true
            },
            tanggal: {
                required: true
            },
        },
        messages: {
            no_daftar: {
                required: "Nomor Daftar Wajib Diisi"
            },
            tanggal: {
                required: "Tanggal Wajib Diisi"
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
        if (listDataSelected.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Data Purhase Order Masih Kosong !',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        } else {
            if ($('.create-form').valid()) {
                let data = new FormData(document.querySelector(".create-form"));
                data.append('listBarang', JSON.stringify(listDataSelected));

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
                    if (result.isConfirmed) {
                        let data = new FormData(document.querySelector(".create-form"));
                        data.append('listData', JSON.stringify(listDataSelected));

                        $.ajax({
                            url: "<?= base_url("bea-cukai-bc-40/po/update"); ?>",
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

                            },
                        });
                    }
                })
            }
        }
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

    $('#tanggal').datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#ubahNoAjuButton').click(function(e) {
        e.preventDefault();
        if ($('#form-update').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Ubah Nomor Aju ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector("#form-update"));
                    $.ajax({
                        url: `<?= base_url("bea-cukai-bc-40/id/update-no-aju"); ?>`,
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
                                    location.reload();
                                });

                            }
                        }
                    })
                }
            })
        }
    });

    // FUNCTION HELPER
    function deleteDetail(id) {
        var indexToRemove = -1;
        for (var i = 0; i < listDataSelected.length; i++) {
            if (listDataSelected[i].penerimaan_barang_id == id) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listDataSelected.splice(indexToRemove, 1);
            drawTablePurchaseOrderUsed(listDataSelected);
        }
    }


    function getIDListDataSelected() {
        var id_selected = [];
        $.each(listDataSelected, function(i, v) {
            id_selected.push(v.penerimaan_barang_id);
        })
        return id_selected;
    }

    function getListPurchaseOrderNotUsed() {
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-40/list-po'); ?>`,
            method: "GET",
            data: {
                po_type: "<?= $bcPo['po_type'] ?>",
                supplier_id: "<?= $bcPo['supplier_id'] ?>",
                start_date: $(".start_date").val(),
                end_date: $('#end_date').val()
            },
            dataType: "json",
            success: function(res) {
                // DRAW LIST PO TABLE
                var data = res.data;
                listData = [];
                listData = data;

                if ($.fn.DataTable.isDataTable('#dataTable1')) {
                    $('#dataTable1').DataTable().clear().draw();
                    dataTable1.destroy();
                }

                const table = $('#dataTable1');
                table.find('tbody').empty();
                table.find('tfoot').empty();

                var no = 1;
                $.each(data, function(i, v) {
                    var newRow = $('<tr>');
                    newRow.append($('<td style="text-align:center;">').text(no++));
                    newRow.append($('<td style="text-align: center;">').html(
                        `
                                <div class="form-check">
                                    <input data-penerimaan_barang_id="${v.penerimaan_barang_id}" autocomplete="one-time-code" class="form-check-input child" type="checkbox">
                                </div>
                            `
                    ));
                    newRow.append($('<td style="text-align:center;">').text(v.po_date));
                    newRow.append($('<td style="text-align:center;">').text(v.lpb_date));
                    newRow.append($('<td style="text-align:center;">').text(v.lpb_no));
                    newRow.append($('<td style="text-align:center;">').text(v.po_no));
                    newRow.append($('<td style="text-align:center;">').text(v.kode_barang));
                    newRow.append($('<td style="text-align:center;">').text(v.barang_name));
                    newRow.append($('<td style="text-align:center;">').text(parseFloat(v.qty_po)));
                    newRow.append($('<td style="text-align:center;">').text(parseFloat(v.qty_lpb)));
                    newRow.append($('<td style="text-align:center;">').text(v.harga));
                    table.find('tbody').append(newRow);
                });

                dataTable1 = $('#dataTable1').DataTable({

                    processing: false,
                    serverSide: false,
                    ordering: true,
                    order: [],
                    fixedHeader: true,
                    "initComplete": function(settings, json) {
                        $('.dataTables_length').empty();
                        $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                        $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
                    },
                    lengthMenu: [
                        [100],
                        [100]
                    ],
                    display: "stripe",
                    searching: true,
                    language: {
                        emptyTable: "Tidak Ada Data",
                        lengthMenu: "Show _MENU_ entries",
                        paginate: {
                            previous: '<i class="fa fa-angle-left"></i>',
                            next: '<i class="fa fa-angle-right"></i>'
                        }
                    }
                });

                dataTable1.draw();
            }
        });
    }

    function drawTablePurchaseOrderUsed(listDataSelected) {
        const table = $('#dataTable2');
        table.find('tbody').empty();
        table.find('tfoot').empty();

        if (listDataSelected.length == 0) {
            var newRow = $('<tr>');
            newRow.append($('<td colspan="11" style="text-align:center">Tidak Ada Barang</td>'));
            table.find('tfoot').append(newRow);
        } else {
            var no = 1;
            $.each(listDataSelected, function(i, v) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align: center;">').html(
                    `
                   ${no++} 
                `
                ));
                newRow.append($('<td style="text-align: center;">').text(v.po_date));
                newRow.append($('<td style="text-align: center;">').text(v.lpb_date));
                newRow.append($('<td style="text-align: center;">').text(v.lpb_no));
                newRow.append($('<td style="text-align: center;">').text(v.po_no));
                newRow.append($('<td style="text-align: center;">').text(v.kode_barang));
                newRow.append($('<td style="text-align: center;">').text(v.barang_name));
                newRow.append($('<td style="text-align: center;">').text(parseFloat(v.qty_po)));
                newRow.append($('<td style="text-align: center;">').text(parseFloat(v.qty_lpb)));
                newRow.append($('<td style="text-align: center;">').text(parseFloat(v.qty_lpb_konversi)));
                newRow.append($('<td style="text-align: center;">').text(formatRupiah(v.harga_number)));

                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <button <?= !empty($bcPo) ? (($bcPo['status_posting'] == "1") ? 'disabled' : '') : '' ?> type="button" class="btn btn-danger" onclick="deleteDetail(${v.penerimaan_barang_id})" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                `
                ));
                table.find('tbody').append(newRow);
            });
        }
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

    function deleteAction() {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Dokumen BC 4.0 ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append("bc_purchase_order_id", "<?= encrypt($bcPo['id']) ?>");
                $.ajax({
                    url: `<?= base_url("bea-cukai-bc-40/id/delete"); ?>`,
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
                                location.replace("<?= base_url('bea-cukai-bc-40') ?>")
                            });
                        }
                    }
                })
            }
        })
    }

    function posting() {
        Swal.fire({
            icon: 'question',
            title: 'Posting Dokumen BC 4.0 Lokal ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append("bc_purchase_order_id", "<?= encrypt($bcPo['id']) ?>");
                $.ajax({
                    url: `<?= base_url("bea-cukai-bc-40/posting"); ?>`,
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
                                location.replace("<?= base_url('bea-cukai-bc-40') ?>")
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            })
                        }
                    }
                })
            }
        })
    }

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
</script>
<?= $this->endSection(); ?>