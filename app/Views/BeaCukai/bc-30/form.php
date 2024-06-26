<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<section class="section section-form">

    <div class="section-header">
        <h1 class="title-name"><?= !empty($bc30) ? "Update Dokumen BC 3.0" : "Tambah Dokumen BC 3.0" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right root-form-view" href="<?= base_url("bea-cukai-bc-30"); ?>">
                Batal
            </a>
            <?php if (!empty($bc30)) : ?>
                <?php if ($bc30['status_posting'] == "0") : ?>
                    <?php if (can('Bea Cukai', 'BC 3.0', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="deleteAction()">
                            Hapus
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($bc30['status_posting'] == "0") : ?>
                    <?php if (can('Bea Cukai', 'BC 3.0', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="postingAction()">
                            Posting
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($bc30['status_posting'] == "0") : ?>
                    <?php if (can('Bea Cukai', 'BC 3.0', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else : ?>
                <?php if (can('Bea Cukai', 'BC 3.0', 'c')) : ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="font-weight: bold; color:black;">
            DATA BARANG UNTUK PEMBUATAN DOKUMEN BEA CUKAI 3.0
        </div>
        <div class="card-body">
            <form class="create-form">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="id" class="id" value="<?= !empty($bc30) ? encrypt($bc30['id']) : '' ?>">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-floating mb-3 mt-1" style="height: 50px;">
                            <select <?= !empty($bc30) ? ($bc30['status_posting'] == "1" ? "disabled" : "") : '' ?> class="form-select tipe_sales_order" id="tipe_sales_order" name="tipe_sales_order" aria-label="Floating label select example">
                                <option value=""></option>
                                <option <?= !empty($bc30) ? ($bc30['tipe_sales_order'] == "INTERNASIONAL" ? "selected" : "") : '' ?> value="INTERNASIONAL">SALES ORDER EKSPOR</option>
                                <option <?= !empty($bc30) ? ($bc30['tipe_sales_order'] == "LOKAL" ? "selected" : "") : '' ?> value="LOKAL">SALES ORDER LOKAL</option>
                            </select>
                            </select>
                            <label style="z-index: 1;">Pilih Tipe Sales Order</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3 mt-1" style="height: 50px;">
                            <select <?= !empty($bc30) ? ($bc30['status_posting'] == "1" ? "disabled" : "") : '' ?> class="form-select sales_order_id" id="sales_order_id" name="sales_order_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($bc30)) : ?>
                                    <option selected data-sales_order_id="<?= $bc30['sales_order_id'] ?>" data-nama_customer="<?= $bc30['nama_customer'] ?>" data-country_name="<?= $bc30['country_name'] ?>" data-alamat_customer="<?= $bc30['alamat'] ?>" value="<?= $bc30['sales_order_id'] ?>">
                                        <?= $bc30['no_sales_order'] ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                            </select>
                            <label style="z-index: 1;">Pilih Order Form</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled placeholder="Company Asal" value="<?= !empty($bc30) ? $bc30['nama_customer'] : '' ?>" class="form-control nama_customer" id="nama_customer" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Nama Customer</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled placeholder="Negara" value="<?= !empty($bc30) ? $bc30['country_name'] : '' ?>" class="form-control country_name" id="country_name" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Negara</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled placeholder="Company Asal" value="<?= !empty($bc30) ? $bc30['alamat'] : '' ?>" class="form-control alamat_customer" id="alamat_customer" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Alamat</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <div class="form-floating mb-3">
                                <input value="<?= !empty($bc30) ? $bc30['no_aju'] : $noAju ?>" name="no_aju" readonly type="text" id="no_aju" class="form-control no_aju" placeholder="">
                                <label>Nomor Pengajuan</label>
                            </div>
                            <div class="input-group-append" style="height:50px;">
                                <button <?= !empty($bc30) ? ($bc30['status_posting'] == "1" ? "disabled" : "") : '' ?> class="btn btn-success btn-customer-add" id="btn-customer-add" data-toggle="modal" type="button" onclick="noAjuShowModal()">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3">
                            <input <?= !empty($bc30) ? ($bc30['status_posting'] == "1" ? "disabled" : "") : '' ?> value="<?= !empty($bc30) ? $bc30['no_daftar'] : "" ?>" autocomplete="one-time-code" type="number" class="form-control no_daftar" id="no_daftar" name="no_daftar" placeholder="No Daftar">
                            <label for="floatingInput">Nomor Daftar</label>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row mt-2">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Daftar Barang Yang Akan Di Ekspor</label>
                </div>
                <div class="col-md-12 col-table-button-tts">


                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" border="1" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">Tanggal Keluar</th>
                                    <th style="text-align: center;">Tipe Barang (Internal)</th>
                                    <th style="text-align: center;">Dokumen Asal</th>
                                    <th style="text-align: center;">Kode Barang (Internal)</th>
                                    <th style="text-align: center;">Barang - Spesifikasi (Internal)</th>
                                    <th style="text-align: center;">Kode Barang (Sales)</th>
                                    <th style="text-align: center;">Barang - Spesifikasi (Sales)</th>
                                    <th style="text-align: center;">No Stuffing / Pengeluaran Barang</th>
                                    <th style="text-align: center;">Departemen / Warehouse Pengeluaran</th>
                                    <th style="text-align: center;">Qty Keluar</th>
                                    <th style="text-align: center;">Satuan (Sales)</th>
                                    <th style="text-align: center;">Satuan (Internal)</th>
                                    <th style="text-align: center;">Harga</th>
                                </tr>
                            </thead>
                            <tbody class="body-table">
                            </tbody>
                            <tfoot class="foot-detail-table" id="foot-detail-table">
                                <tr>
                                    <td colspan="14" style="text-align: center;">
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
                        <div class="col-sm-12 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="no_pengajuan" value="<?= !empty($bc30) ? $bc30['no_aju'] : $noAju ?>" name="no_pengajuan" type="text" readonly class="no_pengajuan form-control" placeholder="">
                                <label>Preview Nomor Pengajuan</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3 btn-discard-modal" data-bs-dismiss="modal">Batal</button>
                    <?php if (!empty($bc30)) : ?>
                        <?php if ($bc30['status_posting'] === "0") : ?>
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
    <?php if (!empty($bc30)) : ?>
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-30/list-barang'); ?>`,
            method: "GET",
            data: {
                tipe_sales_order: "<?= $bc30['tipe_sales_order'] ?>",
                sales_order_id: "<?= $bc30['sales_order_id'] ?>"
            },
            dataType: "json",
            success: function(res) {
                listData = [];
                listData = res.data;
                drawTable(listData);
            }
        });
    <?php endif; ?>

    $('#tipe_sales_order').select2({
        placeholder: "Pilih Tipe Sales Order",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        getListSalesOrder();
        // RESET FORM
        $('#nama_customer').val('');
        $('#country_name').val('');
        $('#alamat_customer').val('');
    });

    $('#sales_order_id').select2({
        placeholder: "Pilih Order Form",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        var selected = $('#sales_order_id option:selected');
        $('#nama_customer').val(selected.data('nama_customer'));
        $('#country_name').val(selected.data('country_name'));
        $('#alamat_customer').val(selected.data('alamat_customer'));
        // GET BARANG
        getListBarang();
    });

    // VALIDATOR
    var validator = $(".create-form").validate({
        rules: {
            tipe_sales_order: {
                required: true
            },
            sales_order_id: {
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
            tipe_sales_order: {
                required: "Pilih tipe sales order"
            },
            sales_order_id: {
                required: "Pilih order form"
            },
            no_daftar: {
                required: "No Aju wajib diisi"
            },
            no_daftar: {
                required: "No Daftar wajib diisi"
            }
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

    $("#sales_order_id,#tipe_sales_order")
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
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector(".create-form"));
                    var id = $('#id').val();
                    if (id) {
                        // UPDATE
                        $.ajax({
                            url: `<?= base_url("bea-cukai-bc-30/update"); ?>`,
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
                                        location.href = "<?= base_url('bea-cukai-bc-30') ?>"
                                    });

                                }
                            }
                        })
                    } else {
                        // CREATE
                        $.ajax({
                            url: `<?= base_url("bea-cukai-bc-30/save"); ?>`,
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
                                        location.href = "<?= base_url('bea-cukai-bc-30') ?>"
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
        $('#modalUpdateNoAju').modal('show');
    }


    function checkNoAju() {
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-30/check-no-aju'); ?>`,
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

    function getListSalesOrder() {
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-30/list-sales-order'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                tipe_sales_order: $(".tipe_sales_order option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".sales_order_id").empty()
                $(".sales_order_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".sales_order_id").append(`<option
                        data-sales_order_id="${item.sales_order_id}"
                        data-nama_customer="${item.nama_customer}"
                        data-country_name="${item.country_name}" 
                        data-alamat_customer="${item.alamat_customer}"
                        value="${item.sales_order_id}">${item.no_sales_order}
                    </option>`)
                })
                $(".sales_order_id").val();
            }
        });
    }

    function getListBarang() {
        $.ajax({
            url: `<?= base_url('bea-cukai-bc-30/list-barang'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                tipe_sales_order: $(".tipe_sales_order option:selected").val(),
                sales_order_id: $(".sales_order_id option:selected").val()
            },
            dataType: "json",
            success: function(res) {
                listData = [];
                listData = res.data;
                drawTable(listData);
            }
        });
    }

    function drawTable(listData) {
        var no = 1;
        const table = $('#dataTable');
        table.find('tbody').empty();
        table.find('tfoot').empty();

        if (listData.length === 0) {
            var newRow = $('<tr>');
            newRow.append($('<td colspan="14" style="text-align:center">Tidak Ada Barang</td>'));
            table.find('tfoot').append(newRow);
        } else {
            $.each(listData, function(i, v) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align: center;">').html(
                    `
                            ${no++} 
                        `
                ));
                newRow.append($('<td style="text-align: center;">').text(v.tanggal_keluar));
                newRow.append($('<td style="text-align: center;">').text(v.tipe_barang));
                newRow.append($('<td style="text-align: center;">').text(v.dokumen_asal));
                newRow.append($('<td style="text-align: center;">').text(v.kode_barang_internal));
                newRow.append($('<td style="text-align: center;">').text(v.nama_barang_internal));
                newRow.append($('<td style="text-align: center;">').text(v.kode_barang_sales));
                newRow.append($('<td style="text-align: center;">').text(v.nama_barang_sales));
                newRow.append($('<td style="text-align: center;">').text(v.no_stuffing));
                newRow.append($('<td style="text-align: center;">').text(v.divisi + ' / ' + v.warehouse_name));
                newRow.append($('<td style="text-align: center;">').text(v.qty_keluar));
                newRow.append($('<td style="text-align: center;">').text(v.kode_satuan_sales));
                newRow.append($('<td style="text-align: center;">').text(v.kode_satuan_internal));
                newRow.append($('<td style="text-align: center;">').text(v.harga));
                table.find('tbody').append(newRow);
            });
        }
    }

    function deleteAction() {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Dokumen BC 3.0 ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $('#id').val();
                var formData = new FormData();
                formData.append("id", id);
                $.ajax({
                    url: `<?= base_url("bea-cukai-bc-30/delete"); ?>`,
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
                                    location.href = "<?= base_url('bea-cukai-bc-30') ?>"
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
            title: 'Posting Dokumen BC 3.0 ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $('#id').val();
                var formData = new FormData();
                formData.append("id", id);
                $.ajax({
                    url: `<?= base_url("bea-cukai-bc-30/posting"); ?>`,
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
                                    location.href = "<?= base_url('bea-cukai-bc-30') ?>"
                                }
                            });
                        }
                    }
                })
            }
        })
    }
</script>

<?= $this->endSection(); ?>