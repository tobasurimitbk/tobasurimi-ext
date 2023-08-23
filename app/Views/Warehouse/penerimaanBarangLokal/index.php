<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1>Penerimaan Barang Lokal</h1>

    <!-- <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
        Export
    </button>
    <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
        <li><button class="dropdown-item" onclick="excel('<?= base_url("penerimaan-barang-lokal/export-table"); ?>')">Excel</button></li>
    </ul> -->

    <a class="btn btn-show-form btn-add float-right" href="<?= base_url("penerimaan-barang-lokal/create"); ?>">
        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
    </a>
</div>
<div class="card">
    <div class="card-body">
        <div class="row justify-content-end mb-3 row-col-spp">
            <div class="col">
            <?= csrf_field() ?>
                <div class="input-group input-group-password">
                    <input autocomplete="off" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal">
                    <div class="input-group-prepend group-prepend-password align-items-center">
                        <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="input-group input-group-password">
                    <input autocomplete="off" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal">
                    <div class="input-group-prepend group-prepend-password align-items-center">
                        <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                    </div>
                </div>
            </div>
            <div class="col">
                <select class="form-select status" name="status" id="status" aria-label="Floating label select example">
                    <option value="waiting">WAITING</option>
                    <option value="finish">FINISH</option>
                </select>
            </div>
            <div class="col">
                <input autocomplete="off" class="form-control search form-out-search" placeholder="Ketik No Penerimaan" value="" />
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No.</th>
                            <th onclick="changeSort('tipe_bahan')" class="sort">Jenis PO</th>
                            <th onclick="changeSort('no_penerimaan_barang')" class="sort">No. Penerimaan</th>
                            <th onclick="changeSort('warehouse_name')" class="sort">Gudang</th>
                            <th onclick="changeSort('validation_date')" class="sort">Tanggal Daftar</th>
                            <th onclick="changeSort('supplier_name')" class="sort">Supplier</th>
                            <th>Jumlah Item</th>
                            <th>Actions</th>
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
    let sort = "validation_date";
    let sortType = "desc";

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [[4, 'desc']],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("penerimaan-barang-lokal/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.status = $(".status").val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        // scrollX: true,
        "initComplete": function (settings, json) {    
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
            orderable: false
        },
        {
            data: "tipe_bahan",
            className: "text-center"
        },
        {
            data: "no_penerimaan_barang",
            className: "text-center"
        },
        {
            data: "warehouse_name",
            className: "text-center"
        },
        {
            data: "validation_date",
            className: "text-center"
        },
        {
            data: "supplier_name",
            className: "text-center"
        },
        {
            data: "itemCount",
            className: "text-center",
            searchable: false,
            sortable: false
        },
        {
            data: "id",
            className: "text-center actions",
            searchable: false,
            sortable: false,
            render: function(data, type, row) {
                let id = row?.id;
                let status = row?.status_post
                let tipe_bahan = row?.tipe_bahan

                if (status == "WAITING") {
                    return `
                        <div class="mt-0">
                        <button class="btn btn-warning btn-print" onclick="print('<?= base_url("penerimaan-barang-lokal/print/"); ?>${id}')" style="box-shadow: none !important;">
                            <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                        </button>
                        <button onclick="posting(${id}, '${tipe_bahan}')" class="btn btn-success posting-spp">
                            <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                        </button>
                        <button onclick="remove(${id})" class="btn btn-danger delete-parent">
                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                        </button>
                        </div>
                    `
                } else {
                    return `
                        <div class="mt-0">
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("penerimaan-barang-lokal/print/"); ?>${id}')" style="box-shadow: none !important;">
                            <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                        </button>
                        </div>
                    `
                }

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

        $(".dataTable_info").addClass("pt-0");

        $(".search").keyup(function () {
            table.ajax.reload();
        })

        $(".dateStart, .dateEnd, .status").change(function () {
            table.ajax.reload();
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            location.replace(`<?= base_url("penerimaan-barang-lokal/id"); ?>/${data.id}`);
        })
    })

    const posting = function(id, tipe_bahan) {
        Swal.fire({
            icon: 'question',
            title: 'Yakin akan di posting?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Posting',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("penerimaan-barang-lokal/update-status"); ?>",
                    data: {
                        id: id,
                        tipe_bahan: tipe_bahan
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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

    const remove = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Yakin akan di hapus?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Posting',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("penerimaan-barang-lokal/delete"); ?>",
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

    const print = function(url) 
    {
        window.open(url, "_blank");
    }

    const excel = function(url) {
        let search = $(".search").val();
        let status = $(".status").val();
        let dateStart = $(".dateStart").val();
        let dateEnd = $(".dateEnd").val();

        window.open(url + `?search=${search}&status=${status}&dateStart=${dateStart}&dateEnd=${dateEnd}&sort=${sort}&sortType=${sortType}`, "_blank");
    }

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