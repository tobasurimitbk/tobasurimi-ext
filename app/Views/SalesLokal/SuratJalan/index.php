<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Surat Jalan</h1>
        <a class="btn btn-show-form btn-add float-right" href="<?= base_url("surat-jalan/create"); ?>">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </a>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-2">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_customer" name="filter_customer" id="filter_customer">
                            <option value="" data-code=""></option>

                            <?php foreach ($getCustomers as $row) : ?>
                                <option value="<?= $row['id']; ?>" data-code=""><?= $row['name'] ?></option>
                            <?php endforeach; ?>


                        </select>
                        <label for="floatingInput">Filter Customer</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_invoice" name="filter_invoice" id="filter_invoice">
                            <option value="" data-code=""></option>
                            <option value="belum" data-code="">Belum Digunakan Invoice</option>
                            <option value="sudah" data-code="">Sudah Digunakan Invoice</option>
                        </select>
                        <label for="floatingInput">Filter Invoice</label>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari No. Surat Jalan" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <?= csrf_field() ?>
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th onclick="changeSort('no')" class="sort">No.</th>
                                <th onclick="changeSort('kode_pelanggan')" class="sort">Kode Pelanggan</th>
                                <th onclick="changeSort('nama_pelanggan')" class="sort">Nama Pelanggan</th>
                                <th onclick="changeSort('customerSales')" class="sort">Nama Sales</th>
                                <th onclick="changeSort('no_surat_jalan')" class="sort">No Surat Jalan</th>
                                <th onclick="changeSort('tipe_sales_order')" class="sort">Tipe</th>
                                <th onclick="changeSort('shipping_date')" class="sort">Shipping Date</th>
                                <th onclick="changeSort('sales_order_invoice_id')" class="sort">Invoice</th>
                                <th onclick="changeSort('print')" class="sort">Print</th>
                                <th onclick="changeSort('total_harga')" class="sort">Total Harga</th>
                                <th class="sort">Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table" style="cursor: pointer;">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';

    let sort = "";
    let sortType = "desc";
    let trigger = true;

    let list_address = [];
    let list_delete = [];
    var row = 0;

    $('.filter_customer, .filter_invoice').select2({
        placeholder: "",
        theme: "bootstrap-5",
        allowClear: true,
    })

    //CSS SELECT2 FLOATING LABEL
    $('.filter_customer, .filter_invoice')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.filter_customer, .filter_invoice')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.filter_customer, .filter_invoice')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    $(document).ready(function() {

        $(".dateStart").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $(".dateEnd").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $('.icon-dateStart').click(function() {
            $(".dateStart").focus();
        });

        $('.icon-dateEnd').click(function() {
            $(".dateEnd").focus();
        });

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            location.replace(`<?= base_url("surat-jalan/id"); ?>/${data.id}`);
        })

        $(".dateStart, .dateEnd, .filter_customer, .filter_invoice").change(function() {
            table.ajax.reload();
        });
    })

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
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
            url: "<?= base_url("surat-jalan/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.sort = sort;
                data.sortType = sortType;
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.filter_customer = $(".filter_customer").val();
                data.filter_invoice = $(".filter_invoice").val();
            }
        },
        // scrollX: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        //responsive: true,
        display: "stripe",
        searching: false,
        columns: [{
            data: "no",
            className: "text-center",
            sortable: false
        }, {
            data: "kode_pelanggan",
            className: "text-center"
        }, {
            data: "nama_pelanggan",
            className: "text-center"
        }, {
            data: "customerSales",
            className: "text-center"
        }, {
            data: "no_surat_jalan",
            className: "text-center"
        }, {
            data: "tipe_sales_order",
            className: "text-center"
        }, {
            data: "shipping_date",
            className: "text-center"
        }, {
            data: "sales_order_invoice_id",
            className: "text-center",
            render: function(data, type, row) {
                if (data && data !== "") {
                    return "<i class='fa fa-check' aria-hidden='true' style='color:green;'></i>";
                } else { // Otherwise, display a dash "-"
                    return "<i class='fa fa-minus' aria-hidden='true' style='color:red;'></i>";
                }
            }
        }, {
            data: "print",
            className: "text-center",
            render: function(data, type, row) {
                if (data && data !== "0") {
                    return "<i class='fa fa-check' aria-hidden='true' style='color:green;'></i>";
                } else { // Otherwise, display a dash "-"
                    return "<i class='fa fa-minus' aria-hidden='true' style='color:red;'></i>";
                }
            }
        }, {
            data: "total_harga",
            className: "text-center"
        }, {
            data: "id",
            className: "text-center actions",
            searchable: false,
            sortable: false,
            render: function(data, type, row) {
                let id = row.id;
                return `<button type="button" onclick="handleDelete('${id}')" class="btn btn-discard delete-btn btn-trash"><i class="fa fa-trash"></i></button>

                <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("surat-jalan/print/"); ?>${id}')" style="box-shadow: none !important;">
                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                </button>`
            }
        }],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    // delete
    function handleDelete(id) {
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

                setLoading()
                $.ajax({
                    url: "<?= base_url("surat-jalan/delete"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        csrf.val(response.token);
                        if (response.status) {
                            stopLoading()
                            Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    table.ajax.reload()
                                })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                            stopLoading()
                        }
                    },
                    onError: function(response) {
                        csrf.val(response.token);
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Gagal Dihapus, coba Lagi',
                            confirmButtonColor: '#4e73df',
                        })
                        stopLoading()
                    }
                });
            }
        })
    }


    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    const print = function(url) {
        window.open(url, "_blank");
    }
</script>
<?= $this->endSection(); ?>