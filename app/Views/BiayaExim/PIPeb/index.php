<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Performance Invoice & PEB</h1>
    </div>
    <?= csrf_field() ?>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp mb-3">
                <div class="col mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Start Date" value="">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="End Date">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select status_invoice" name="status_invoice" id="status_invoice" aria-label="Floating label select example">
                        <option value="ALL" selected>STATUS : ALL</option>
                        <option value="TERBIT">STATUS : INVOICE TERBIT</option>
                        <option value="BELUM TERBIT">STATUS : INVOICE BELUM TERBIT</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Search Data" id="search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th onclick="changeSort('sales_order_export.sales_order_export_id')" style="width: 10px;">No</th>
                                <th onclick="changeSort('sales_order_export.no_invoice')" class="sort">No Invoice</th>
                                <th onclick="changeSort('sales_order_export.tanggal_invoice')" class="sort">Tgl Invoice</th>
                                <th onclick="changeSort('sales_contract.customer_id')" class="sort">Customer</th>
                                <th onclick="changeSort('sales_order_export.sales_order_export_no')" class="sort">No OF</th>
                                <th onclick="changeSort('sales_contract.dicharge_port')" class="sort">Destination</th>
                                <th onclick="changeSort('sales_order_export.nilai_pi')" class="sort">Nilai PI</th>
                                <th onclick="changeSort('sales_order_export.nilai_peb')" class="sort">Nilai PEB</th>
                                <th onclick="changeSort('sales_order_export.exchange_rate_peb')" class="sort">Exchange Rate</th>
                                <th onclick="changeSort('sales_order_export.nilai_peb_idr')" class="sort">Nilai PEB (IDR)</th>
                                <th style="width: 120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</section>
<div class="modal" id="modalPiPeb" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary" id="label-update-pi-peb"></h5>
            </div>
            <form class="form-pi-peb">
                <div class="modal-body">
                    <input type="hidden" name="id" class="id" id="id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly autocomplete="one-time-code" type="text" class="form-control no_invoice" name="no_invoice" id="no_invoice" placeholder="Nomor Invoice">
                                <label for="floatingInput">Nomor Invoice</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly autocomplete="one-time-code" type="text" class="form-control tanggal_invoice" name="tanggal_invoice" id="tanggal_invoice" placeholder="Tanggal Invoice">
                                <label for="floatingInput">Tanggal Invoice</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select
                                    class="form-select valas_id_peb"
                                    aria-label="Floating label select example"
                                    name="valas_id_peb"
                                    id="valas_id_peb">
                                    <option value=""></option>
                                    <?php foreach ($dataValuta as $d) : ?>
                                        <option value="<?= $d['id'] ?>">
                                            <?= $d['value'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Pilih Valas PEB</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" onkeyup="this.value = greatFormatRupiah(this.value)" class="form-control nilai_peb" name="nilai_peb" id="nilai_peb" placeholder="Nilai PEB">
                                <label for="floatingInput">Nilai PEB</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" onkeyup="this.value = greatFormatRupiah(this.value)" class="form-control exchange_rate_peb" name="exchange_rate_peb" id="exchange_rate_peb" placeholder="Exchange Rate PEB">
                                <label for="floatingInput">Exchange Rate PEB</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly autocomplete="one-time-code" type="text" class="form-control nilai_peb_idr" name="nilai_peb_idr" id="nilai_peb_idr" placeholder="Nilai PEB (IDR)">
                                <label for="floatingInput">Nilai PEB (IDR)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select
                                    disabled
                                    class="form-select valas_id_pi"
                                    aria-label="Floating label select example"
                                    name="valas_id_pi"
                                    id="valas_id_pi">
                                    <option value=""></option>
                                    <?php foreach ($dataValuta as $d) : ?>
                                        <option value="<?= $d['id'] ?>">
                                            <?= $d['value'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Pilih Valas PI</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input disabled autocomplete="one-time-code" type="text" onkeyup="this.value = greatFormatRupiah(this.value)" class="form-control nilai_pi" name="nilai_pi" id="nilai_pi" placeholder="Nilai Peformance Invoice">
                                <label for="floatingInput">Nilai Peformance Invoice</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-detail btn-discard mr-3">Kembali</button>
                    <button type="button" class="btn btn-submit-form btn-submit-detail">Update PEB</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "sales_order_export.createdAt";
    let sortType = "desc";

    const table = $('.dataTable').DataTable({

        processing: true,
        serverSide: true,
        ordering: true,
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
            url: "<?= base_url("pi-peb/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.status_invoice = $(".status_invoice").val();
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        initComplete: function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-left",
            }, {
                data: "no_invoice",
                className: "text-left"
            }, {
                data: "tanggal_invoice",
                className: "text-left"
            }, {
                data: "customer_name",
                className: "text-left",
            }, {
                data: "sales_order_export_no",
                className: "text-left",
            },
            {
                data: "dicharge_port",
                className: "text-left",
            },
            {
                data: "nilai_pi",
                className: "text-left",
            },
            {
                data: "nilai_peb",
                className: "text-left",
            },
            {
                data: "exchange_rate_peb",
                className: "text-left",
                render: function(data) {
                    return greatFormatRupiah(data)
                }
            },
            {
                data: "nilai_peb_idr",
                className: "text-left",
                render: function(data) {
                    return greatFormatRupiah(data)
                }
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let status_invoice = row.status_invoice;
                    let id = row.id;
                    if (status_invoice == "TERBIT") {
                        return `
                            <button data-toggle="tooltip" title="Update PEB" onclick="terbitkanInvoice('${id}')" class="btn btn-success">
                                Update PEB
                            </button>
                        `
                    } else {
                        return `
                          <button data-toggle="tooltip" title="Create PEB" onclick="terbitkanInvoice('${id}')" class="btn btn-danger">
                            Create PEB
                        </button>
                    `
                    }


                    return `
                    <div class="mt-0">
                        ${res}
                    </div>
                    `;
                }
            }
        ],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        },
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Tidak ada data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    $(".dateStart,.dateEnd,.tanggal_invoice").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $('.valas_id_peb').select2({
        placeholder: "Pilih Valas PEB",
        theme: "bootstrap-5",
        dropdownParent: $('#modalPiPeb')
    }).change(function() {});

    $('.icon-dateStart').click(function() {
        $(".dateStart").focus();
    });

    $('.icon-dateEnd').click(function() {
        $(".dateEnd").focus();
    });

    $(".dataTable_info").addClass("pt-0");

    $(".status_invoice, .dateStart, .dateEnd").change(function() {
        table.ajax.reload();
    })

    $(".search").keyup(function() {
        table.ajax.reload();
    })

    $('.btn-hide-detail').click(function(e) {
        e.preventDefault();
        $('#modalPiPeb').modal('hide');
    });

    var validator = $(".form-pi-peb").validate({
        rules: {
            no_invoice: {
                required: true
            },
            tanggal_invoice: {
                required: true
            },
            valas_id_peb: {
                required: true
            },
            nilai_peb: {
                required: true
            },
            exchange_rate_peb: {
                required: true
            },
            nilai_peb_idr: {
                required: true
            },
            nilai_pi: {
                required: true
            },
        },
        messages: {
            no_invoice: {
                required: "No invoice wajib diisi"
            },
            tanggal_invoice: {
                required: "Tanggal invoice wajib diisi"
            },
            valas_id_peb: {
                required: "Pilih Valas PEB"
            },
            nilai_peb: {
                required: "Nilai PEB wajib diisi"
            },
            exchange_rate_peb: {
                required: "Exchange Rate PEB wajib diisi"
            },
            nilai_peb_idr: {
                required: "Nilai PEB IDR wajib diisi"
            },
            nilai_pi: {
                required: "Nilai PI wajib diisi"
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

    $('.btn-submit-form').click(function(e) {
        e.preventDefault();
        if ($('.form-pi-peb').valid()) {
            const csrf = $(`[name="${csrfToken}"]`);
            let data = new FormData(document.querySelector(".form-pi-peb"));
            let nilaiPeb = destroyFormatRupiah($('#nilai_peb').val());
            let exchangeRatePeb = destroyFormatRupiah($('#exchange_rate_peb').val());
            let nilaiPebIdr = destroyFormatRupiah($('#nilai_peb_idr').val());

            data.set("nilai_peb", nilaiPeb);
            data.set("exchange_rate_peb", exchangeRatePeb);
            data.set("nilai_peb_idr", nilaiPebIdr);

            Swal.fire({
                icon: 'question',
                title: 'Terbitkan Invoice ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url("pi-peb/update"); ?>",
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
                                    confirmButtonText: 'Ok'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $('#modalPiPeb').modal('hide');
                                        table.ajax.reload()
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
    })

    function terbitkanInvoice(id) {
        $.ajax({
            url: `<?= base_url("pi-peb/get"); ?>`,
            method: "GET",
            dataType: "json",
            data: {
                id: id
            },
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            success: function(res) {
                $('#id').val(res.data.id);
                $('#no_invoice').val(res.data.no_invoice);
                $('#tanggal_invoice').val(res.data.tanggal_invoice);
                $('#valas_id_peb').val(res.data.valas_id_peb).change();
                $('#nilai_peb').val(greatFormatRupiah(res.data.nilai_peb));
                $('#exchange_rate_peb').val(greatFormatRupiah(res.data.exchange_rate_peb));
                $('#nilai_peb_idr').val(greatFormatRupiah(res.data.nilai_peb_idr));
                $('#valas_id_pi').val(res.data.valas_id_pi);
                $('#nilai_pi').val(greatFormatRupiah(res.data.nilai_pi));

                if (res.data.no_invoice == null) {
                    $('.btn-submit-detail').text('Tambah PEB');
                    $('#label-update-pi-peb').text("Tambah PEB");
                } else {
                    $('.btn-submit-detail').text('Update PEB');
                    $('#label-update-pi-peb').text("Update PEB");

                }

                $('#modalPiPeb').modal('show');
            }
        })
    }

    $('.valas_id_peb')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.valas_id_peb')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.valas_id_peb')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    $('#nilai_peb,#exchange_rate_peb').keyup(function(e) {
        e.preventDefault();
        var nilaiPeb = destroyFormatRupiah($('#nilai_peb').val());
        var exchangeRatePeb = destroyFormatRupiah($('#exchange_rate_peb').val());
        var nilaiPebIdr = parseFloat(parseFloat(nilaiPeb) * parseFloat(exchangeRatePeb)).toFixed(2);
        $('#nilai_peb_idr').val(greatFormatRupiah(nilaiPebIdr));
    });

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }
</script>

<?= $this->endSection(); ?>