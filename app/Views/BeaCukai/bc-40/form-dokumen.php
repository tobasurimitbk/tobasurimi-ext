<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>

<section class="section section-form">
    <?php include('header.php') ?>
    <div class="card">
        <div class="card-header" style="font-weight: bold;">
            BC 4.0 - PEMBERITAHUAN PEMASUKAN BARANG ASAL TEMPAT LAIN DALAM DAERAH PABEAN KE TEMPAT PENIMBUNAN BERIKAT
        </div>
        <?= csrf_field() ?>
        <form id="form-dokumen">
            <div class="card-body">
                <?php include_once('nav.php') ?>
                <div class="row mt-3">
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select dokumen_jenis_dokumen" id="dokumen_jenis_dokumen" name="dokumen_jenis_dokumen" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($kodeDokumen as $k) : ?>
                                    <option value="<?= encrypt($k['description']) ?>">
                                        <?= strtoupper($k['description']) . " - " . strtoupper($k['value']) . "" ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label style="z-index: 1;">Pilih Jenis Dokumen</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3">
                            <input value="" id=" dokumen_nomor_dokumen" name="dokumen_nomor_dokumen" type="text" class="form-control dokumen_nomor_dokumen" placeholder="">
                            <label>Nomor Dokumen</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input value="" autocomplete="one-time-code" name="dokumen_tanggal" type="text" placeholder="" class="form-control dokumen_tanggal" id="dokumen_tanggal">
                                <label>Tanggal Dokumen</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <div class="row" style="float: right; margin-bottom:5px;">
                            <div class="col-sm" style="margin-right: -30px;">
                                <button onclick="generateDokumen('<?= encrypt($bcPo['id']) ?>')" style="border-color: #FFA426 !important; background-color: #FFA426 !important; margin-right: 10px !important;" type="button" class="btn btn-add btn-block float-right">
                                    <i class="fas fa-download mr-1"></i> Ambil Dokumen
                                </button>
                            </div>
                            <div class="col-sm">
                                <button type="button" class="btn btn-add btn-block float-right btn-submit-dokumen" style="float: right;">
                                    <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-dokumen" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;">Seri Dokumen</th>
                                <th style="text-align: center;">Jenis Dokumen</th>
                                <th style="text-align: center;">Nomor Dokumen</th>
                                <th style="text-align: center;">Tanggal</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>

    </div>

</section>

<script>
    // CSRF
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    $('#dokumen_jenis_dokumen').select2({
        placeholder: "Pilih Jenis Dokumen",
        theme: "bootstrap-5",
        allowClear: true
    });

    $("#dokumen_tanggal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    // TABEL LIST DOKUMEN
    const tableListInformasiDokumen = $('.table-list-informasi-dokumen').DataTable({

        processing: true,
        serverSide: true,
        ordering: false,
        order: [
            [1, 'asc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("bea-cukai-bc-40/id/dokumen/data/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.bc_purchase_order_id = "<?= encrypt($bcPo['id']) ?>";
                data.sort = "bc_dokumen.createdAt";
                data.sortType = "DESC";
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [

            {
                data: "seri_dokumen",
                searchable: false,
                sortable: false,
                className: "text-center",
            },
            {
                data: "kode_dokumen",
                searchable: false,
                sortable: false,
                className: "text-center",
            },
            {
                data: "nomor_dokumen",
                searchable: false,
                sortable: false,
                className: "text-center"
            },
            {
                data: "tanggal_dokumen",
                className: "text-center",
                searchable: false,
                sortable: false,
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    return `<button type="button" class="btn btn-danger" onclick="removeDokumen('${row.id}')" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>`;
                }
            }


        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Tidak ada dokumen",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    var validatorDokumen = $("#form-dokumen").validate({
        rules: {
            dokumen_jenis_dokumen: {
                required: true
            },
            dokumen_nomor_dokumen: {
                required: true
            },
            dokumen_tanggal: {
                required: true
            },
        },
        messages: {
            dokumen_jenis_dokumen: {
                required: "Pilih jenis dokumen"
            },
            dokumen_nomor_dokumen: {
                required: "Nomor dokumen wajib diisi"
            },
            dokumen_tanggal: {
                required: "Tanggal dokumen wajib diisi"
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

    $('.btn-submit-dokumen').click(function() {
        if ($('#form-dokumen').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Dokumen ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector("#form-dokumen"));
                    formData.append("bc_purchase_order_id", "<?= encrypt($bcPo['id']) ?>");
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-40/id/dokumen/create"); ?>",
                        data: formData,
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
                                csrf.val(response.token);
                                tableListInformasiDokumen.ajax.reload();
                                $('#dokumen_jenis_dokumen').val(null).change();
                                $('#dokumen_nomor_dokumen').val('');
                                $('#dokumen_tanggal').val('');
                            }
                        },
                    });
                }
            })
        }
    });

    function removeDokumen(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Dokumen ?',
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
                    url: "<?= base_url("bea-cukai-bc-40/id/dokumen/delete"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        setLoading();
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        csrf.val(response.token);
                        if (response.status) {
                            tableListInformasiDokumen.ajax.reload();
                        }
                    },
                });
            }
        })
    }

    function generateDokumen(bc_purchase_order_id) {
        const csrf = $(`[name="${csrfToken}"]`);
        $.ajax({
            url: "<?= base_url("bea-cukai-bc-40/id/dokumen-generate"); ?>",
            data: {
                bc_purchase_order_id: bc_purchase_order_id
            },
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                setLoading();
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
                    });

                    csrf.val(response.token);
                    tableListInformasiDokumen.ajax.reload();

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: response.message,
                        confirmButtonColor: '#4e73df',
                    });
                }

            },
        });
    }
</script>


<?= $this->endSection(); ?>