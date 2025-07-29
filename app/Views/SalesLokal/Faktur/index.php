<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .sticky-col {
        position: sticky;
        right: 0;
        background-color: white;
        z-index: 100;
        border-left: 1px solid #ddd;
    }
</style>
<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Faktur Penjualan</h1>
        <?php if (can('Penjualan Lokal', 'Faktur Penjualan', 'c')) : ?>
            <a class="btn btn-show-form btn-add float-right" href="<?= base_url("faktur-sales/create"); ?>">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card">
        <?php if (session()->getFlashdata('error') != null) : ?>
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <strong>Error : <?= session()->getFlashdata('error'); ?></strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        <div class="card-body">
            <div class="row justify-content-end ">
                <div class="col-md-4">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateStart" id="dateStart" name="dateStart" aria-label="Floating label select example" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Awal</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateEnd" id="dateEnd" name="dateEnd" aria-label="Floating label select example" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Akhir</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_customer" name="filter_customer" id="filter_customer">
                            <option value="" data-code=""></option>
                            <?php foreach ($getCustomers as $row) : ?>
                                <option value="<?= $row['id']; ?>" data-code=""><?= $row['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Pilih Customer</label>
                    </div>
                </div>

            </div>
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-4">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_surat_jalan" name="filter_surat_jalan" id="filter_surat_jalan">
                            <option value="" data-code=""></option>
                            <option value="belum" data-code="">Belum Digunakan Surat Jalan</option>
                            <option value="sudah" data-code="">Sudah Digunakan Surat Jalan</option>
                        </select>
                        <label for="floatingInput">Pilih Surat Jalan</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_invoice" name="filter_invoice" id="filter_invoice">
                            <option value="" data-code=""></option>
                            <option value="belum" data-code="">BELUM DIGUNAKAN INVOICE</option>
                            <option value="sudah" data-code="">SUDAH DIGUNAKAN INVOICE</option>
                        </select>
                        <label for="floatingInput">Pilih Invoice</label>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search" id="search" name="search" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Nomor Order </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <?= csrf_field() ?>
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>

                                <th onclick="changeSort('no_sales_order')" class="sort">No Order</th>
                                <th onclick="changeSort('order_date')" class="sort">Tanggal Order</th>
                                <th onclick="changeSort('shipping_date')" class="sort">Tanggal Dikirim</th>
                                <th onclick="changeSort('company')" class="sort">Company</th>
                                <th onclick="changeSort('nama_customer')" class="sort">Nama Customer</th>
                                <th onclick="changeSort('destination')" class="sort">Destinasi</th>
                                <th onclick="changeSort('salesName')" class="sort">Nama Sales</th>
                                <th onclick="changeSort('qty_barang')" class="sort">QTY Barang</th>
                                <th onclick="changeSort('total_harga')" class="sort">Total Harga</th>
                                <th onclick="changeSort('keterangan')" class="sort">Keterangan</th>
                                <th onclick="changeSort('surat_jalan_so_id')" class="sort">Surat Jalan</th>
                                <th onclick="changeSort('sales_order_invoice_id')" class="sort">Invoice</th>
                                <th onclick="changeSort('counter_print')" class="sort">Print</th>
                                <th class="sort sticky-col">Action</th>
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
<div class="modal fade" id="historiModal" tabindex="-1" role="dialog" aria-labelledby="historiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historiModalLabel">Histori Harga Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" type="text" class="form-control order_no" name="order_no" id="order_no">
                            <label for="floatingInput">Nomor Order</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" type="text" class="form-control customer_name" name="customer_name" id="customer_name">
                            <label for="floatingInput">Customer</label>
                        </div>
                    </div>
                    <!-- <div class="col-sm-12">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" type="text" class="form-control lpb_no" name="lpb_no" id="lpb_no">
                            <label for="floatingInput">Nomor LPB</label>
                        </div>
                    </div> -->
                </div>
                <table class="table table-inside table-borderd nowrap table-hover-tobasurimi dataTable2" style="width: 100%;" id="tableHistori">
                    <thead>
                        <tr>
                            <td style="width: 10px;text-align: center;color:#E7323A;font-weight:bold;">No</td>
                            <td style="text-align: center;color:#E7323A;font-weight:bold;">Kode Barang</td>
                            <td style="text-align: center;color:#E7323A;font-weight:bold;">Nama Barang</td>
                            <td style="text-align: center;color:#E7323A;font-weight:bold;">Qty Order</td>
                            <td style="text-align: center;color:#E7323A;font-weight:bold;">Total Harga</td>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "";
    let sortType = "desc";
    let trigger = true;

    let list_address = [];
    let list_delete = [];
    var row = 0;

    $('.filter_customer').select2({
        placeholder: "Pilih Customer",
        theme: "bootstrap-5",
        allowClear: true,
    });

    $('.filter_surat_jalan').select2({
        placeholder: "Pilih Surat Jalan",
        theme: "bootstrap-5",
        allowClear: true,
    });

    $('.filter_invoice').select2({
        placeholder: "Pilih Invoice",
        theme: "bootstrap-5",
        allowClear: true,
    });

    //CSS SELECT2 FLOATING LABEL
    $('.filter_customer, .filter_surat_jalan, .filter_invoice')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.filter_customer, .filter_surat_jalan, .filter_invoice')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.filter_customer, .filter_surat_jalan, .filter_invoice')
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

        $(".dateStart, .dateEnd, .filter_customer, .filter_surat_jalan, .filter_invoice").change(function() {
            table.ajax.reload();
        });
    })

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
            url: "<?= base_url("faktur-sales/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.sort = sort;
                data.sortType = sortType;
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.filter_customer = $(".filter_customer").val();
                data.filter_surat_jalan = $(".filter_surat_jalan").val();
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
                data: "no_sales_order",
                className: "text-center"
            }, {
                data: "order_date",
                className: "text-center"
            }, {
                data: "shipping_date",
                className: "text-center"
            }, {
                data: "company_name",
                className: "text-center"
            }, {
                data: "nama_customer",
                className: "text-center"
            }, {
                data: "destination",
                className: "text-center"
            }, {
                data: "salesName",
                className: "text-center"
            }, {
                data: "qty_barang",
                className: "text-center"
            }, {
                data: "total_harga",
                className: "text-center",
                render: function(data, type, row) {
                    return greatFormatRupiah(destroyFormatRupiah(data));
                }
            }, {
                data: "keterangan",
                className: "text-center"
            },
            {
                data: "surat_jalan_so_id",
                className: "text-center",
                render: function(data, type, row) {
                    if (data && data !== "") {
                        return "<i class='fa fa-check' aria-hidden='true' style='color:green;'></i>";
                    } else { // Otherwise, display a dash "-"
                        return "<i class='fa fa-minus' aria-hidden='true' style='color:red;'></i>";
                    }
                }
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
                data: "counter_print",
                className: "text-center",
                render: function(data, type, row) {
                    if (data && data != 0) {
                        return "<i class='fa fa-check' aria-hidden='true' style='color:green;'></i>";
                    } else { // Otherwise, display a dash "-"
                        return "<i class='fa fa-minus' aria-hidden='true' style='color:red;'></i>";
                    }
                }
            }, {
                data: "id",
                className: "text-center actions sticky-col",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    let posting = row.posting;

                    if (posting == 0) {
                        return `
                            <?php if (can('Penjualan Lokal', 'Faktur Penjualan', 'u')) : ?>
                                <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            <?php endif; ?>
                            <button data-toggle="tooltip" title="Histori Harga Barang" onclick="displayHistory('${id}')" class="btn btn-success posting-spp">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </button>
                            <button type="button" onclick="handleDelete('${id}')" class="btn btn-discard delete-btn btn-trash">
                                <i class="fa fa-trash"></i>
                            </button>
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("faktur-sales/print/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                            <button data-toggle="tooltip" title="posting" onclick="posting('${id}', '1')" class="btn btn-success posting-btn">
                                <i class="fa fa-paper-plane"></i>
                            </button>
                        `;
                    } else {
                        return `
                            <?php if (can('Penjualan Lokal', 'Faktur Penjualan', 'u')) : ?>
                                <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            <?php endif; ?>
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("faktur-sales/print/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                            <button data-toggle="tooltip" title="Unposting" onclick="posting('${id}', '0')" class="btn btn-danger unposting-btn">
                                <i class="fa fa-undo"></i>
                            </button>
                        `;
                    }
                }
            }
        ],
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
                    url: "<?= base_url("faktur-sales/delete"); ?>",
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

    function displayHistory(id) {
        $.ajax({
            url: "<?= base_url("faktur-sales/histori-harga"); ?>",
            data: {
                id: id
            },
            method: "GET",
            success: function(response) {
                // console.log(response);
                $('#order_no').val(response.sales_order.no_sales_order);
                $('#customer_name').val(response.sales_order.name);
                const table = $('#tableHistori');
                var no = 1;
                table.find('tbody').empty();
                $.each(response.list_barang, function(i, v) {
                    var newRow = $('<tr>');
                    newRow.append($('<td style="text-align:center;">').text(no++));
                    newRow.append($('<td style="text-align:center;">').text(v.kode_barang));
                    newRow.append($('<td style="text-align:center;">').text(v.nama_barang));
                    newRow.append($('<td style="text-align:center;">').text(destroyFormatRupiah(v.qty)));
                    newRow.append($('<td style="text-align:center;">').text(greatFormatRupiah(destroyFormatRupiah(v.harga_barang))));
                    table.find('tbody').append(newRow);
                });
                $('#historiModal').modal('show');

            },
        });
    }

    const posting = function(id, status_posting) {
        Swal.fire({
            icon: 'question',
            title: status_posting == "1" ? "Yakin Akan Diposting ?" : "Yakin Akan di Unposting ?",
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Posting',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("faktur-sales/posting"); ?>",
                    data: {
                        id: id,
                        status_posting: status_posting
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
                        }
                    },
                    onError: function(response) {
                        csrf.val(response.token);
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Gagal Disimpan, coba Lagi',
                            confirmButtonColor: '#4e73df',
                        })
                    }
                });
            }
        })
    }

    const print = function(url) {
        window.open(url, "_blank");
    }

    function edit(id) {
        location.replace(`<?= base_url("faktur-sales/id"); ?>/${id}`);
    }
</script>
<?= $this->endSection(); ?>