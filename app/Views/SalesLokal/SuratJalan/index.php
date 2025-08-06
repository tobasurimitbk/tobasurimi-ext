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
        <h1>Surat Jalan</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-warning btn-print float-right" href="#" target="_blank" id="btn-export">
                <i class="fa fa-download"></i> Export
            </a>

            <?php if (can('Penjualan Lokal', 'Surat Jalan', 'c')): ?>
                <a class="btn btn-show-form btn-success float-right btn-submit" href="<?= base_url("surat-jalan/create"); ?>">
                    <i class="fa fa-plus fa-sm me-1"></i> Tambah
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end">
                <div class="col-md-2">
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
                <div class="col-md-2">
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
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_invoice" name="filter_invoice" id="filter_invoice">
                            <option value="" data-code=""></option>
                            <option value="belum" data-code="">BELUM DIGUNAKAN INVOICE</option>
                            <option value="sudah" data-code="">SUDAH DIGUNAKAN INVOICE</option>
                        </select>
                        <label for="floatingInput">Pilih Invoice</label>
                    </div>
                </div>
                <div class="col-md-2 mb-2">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search" id="search" name="search" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Nomor Surat Jalan </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <?= csrf_field() ?>
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th onclick="changeSort('no')" class="sort">No.</th>
                                <th onclick="changeSort('no_surat_jalan')" class="sort">No Surat Jalan</th>
                                <th onclick="changeSort('nama_pelanggan')" class="sort">Nama Pelanggan</th>
                                <th onclick="changeSort('customerSales')" class="sort">Nama Sales</th>
                                <th onclick="changeSort('tipe_sales_order')" class="sort">Tipe</th>
                                <th onclick="changeSort('shipping_date')" class="sort">Shipping Date</th>
                                <th onclick="changeSort('sales_order_invoice_id')" class="sort">Invoice</th>
                                <th onclick="changeSort('print')" class="sort">Print</th>
                                <th onclick="changeSort('total_harga')" class="sort">Total Harga</th>
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

    $('.filter_invoice').select2({
        placeholder: "Pilih Invoice",
        theme: "bootstrap-5",
        allowClear: true,
    });
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

        $(".dateStart, .dateEnd, .filter_customer, .filter_invoice").change(function() {
            table.ajax.reload();
        });

        $('#btn-export').on('click', function(e) {
            e.preventDefault();

            let search = $(".search").val();
            let dateStart = $(".dateStart").val();
            let dateEnd = $(".dateEnd").val();
            let filter_customer = $(".filter_customer").val();
            let filter_invoice = $(".filter_invoice").val();

            let exportUrl = `/surat-jalan/export-excel?search=${encodeURIComponent(search)}&sort=${sort}&sortType=${sortType}&dateStart=${encodeURIComponent(dateStart)}&dateEnd=${encodeURIComponent(dateEnd)}&filter_customer=${filter_customer}&filter_invoice=${filter_invoice}`;

            window.open(exportUrl, '_blank');
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
            data: "no_surat_jalan",
            className: "text-center"
        }, {
            data: "nama_pelanggan",
            className: "text-center"
        }, {
            data: "customerSales",
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
            className: "text-center",
            render: function(data, type, row) {
                return greatFormatRupiah(destroyFormatRupiah(data));
            }
        }, {
            data: "id",
            className: "text-center actions sticky-col",
            searchable: false,
            sortable: false,
            render: function(data, type, row) {
                let id = row.id;
                let btn_delete = ``;
                let btn_print = ``;
                let btn_posting = ``;

                <?php if (can('Penjualan Lokal', 'Surat Jalan', 'p')): ?>
                    btn_print = `
                        <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("surat-jalan/print/"); ?>${id}')" style="box-shadow: none !important;">
                            <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                        </button>
                        `;
                <?php endif; ?>

                <?php if (can('Penjualan Lokal', 'Surat Jalan', 'd')): ?>
                    btn_delete = `
                        <button type="button" onclick="handleDelete('${id}')" class="btn btn-discard delete-btn btn-trash"><i class="fa fa-trash"></i></button>
                        `;
                <?php endif; ?>

                // Cek nilai kolom posting
                if (row.posting == 0) {
                    btn_posting = `
                        <button data-toggle="tooltip" title="posting" onclick="posting('${id}', '1')" class="btn btn-success posting-btn">
                            <i class="fa fa-paper-plane"></i>
                        </button>
                    `;
                } else {
                    btn_posting = `
                         <button data-toggle="tooltip" title="Unposting" onclick="posting('${id}', '0')" class="btn btn-danger unposting-btn">
                            <i class="fa fa-undo"></i>
                        </button>
                    `;
                }

                <?php if (can('Penjualan Lokal', 'Surat Jalan', 'u')) : ?>
                    btn_edit = `
                    <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                    </a>`;
                <?php endif; ?>

                return `${btn_edit}${btn_print}${btn_delete}${btn_posting}`;
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
                    url: "<?= base_url("surat-jalan/posting"); ?>",
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

    function edit(id) {
        location.replace(`<?= base_url("surat-jalan/id"); ?>/${id}`);
    }
</script>
<?= $this->endSection(); ?>