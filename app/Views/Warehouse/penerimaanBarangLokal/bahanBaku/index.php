<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Penerimaan Barang Lokal Baku</h1>
        <?php if (can('Warehouse', 'P. Barang Lokal BB', 'p')) : ?>
            <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
                Export
            </button>
            <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                <li><button class="dropdown-item" onclick="pdf('<?= base_url("penerimaan-barang-lokal-bb/print-table"); ?>')">PDF</button></li>
                <li><button class="dropdown-item" onclick="pdf('<?= base_url("penerimaan-barang-lokal-bb/export-excel"); ?>')">EXCEL</button></li>
            </ul>
        <?php endif; ?>
        <?php if (can('Warehouse', 'P. Barang Lokal BB', 'c')) : ?>
            <a class="btn btn-show-form btn-add float-right" href="<?= base_url("penerimaan-barang-lokal-bb/create"); ?>">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end mb-3 row-col-spp">
                <div class="col">
                    <?= csrf_field() ?>
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart mt-2" id="dateStart" name="dateStart" placeholder="Tanggal" disabled value="01<?= date('/m/Y') ?>">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateEnd mt-2" id="dateEnd" name="dateEnd" placeholder="Tanggal" disabled>
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <select class="form-select status mt-2" name="status" id="status" aria-label="Floating label select example">
                        <option value="waiting">STATUS LPB:WAITING</option>
                        <option value="finish">STATUS LPB:FINISH</option>
                    </select>
                </div>
                <div class="col">
                    <input autocomplete="one-time-code" id="search" class="form-control search form-out-search mt-2" placeholder="Cari Data LPB" value="" />
                </div>
            </div>
            <?php if (session()->get('login')->this_role_name === "ACCOUNTING"): ?>
                <div class="row justify-content-start mb-3 row-col-spp">
                    <div class="col-sm-3">
                        <input autocomplete="one-time-code" class="form-control search nama_barang form-out-search mt-2" placeholder="Cari Kode / Nama Barang " value="" />
                    </div>
                    <div class="col-sm-3">
                        <input autocomplete="one-time-code" class="form-control search note form-out-search mt-2" placeholder="Cari Keterangan" value="" />
                    </div>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('divisi')" class="sort">Departemen</th>
                                <th onclick="changeSort('no_penerimaan_barang')" class="sort">No. Penerimaan</th>
                                <th>No. PO</th>
                                <th onclick="changeSort('warehouse_name')" class="sort">Gudang</th>
                                <th onclick="changeSort('createdAt')">Tanggal</th>
                                <th onclick="changeSort('supplier_name')" class="sort">Supplier</th>
                                <th onclick="changeSort('metadata.value')" class="sort">Dokumen</th>
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
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "createdAt";
    let sortType = "desc";

    const table = $('.dataTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [4, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        "stateSave": true,
        "stateDuration": -1,
        "stateSaveCallback": function(settings, data) {
            // data.searchValue = $("#search").val();
            // data.dateStart = $(".dateStart").val();
            // data.dateEnd = $(".dateEnd").val();
            // data.status = $(".status").val();
            localStorage.setItem('DataTables_' + settings.sInstance, JSON.stringify(data));
        },
        "stateLoadCallback": function(settings) {
            const data = JSON.parse(localStorage.getItem('DataTables_' + settings.sInstance));
            if (data) {
                // $(".search").val(data.searchValue);
                // $(".dateStart").val(data.dateStart);
                // $(".dateEnd").val(data.dateEnd);
                // $(".status").val(data.status);
            }
            return data;
        },
        pageLength: 25,
        ajax: {
            url: "<?= base_url("penerimaan-barang-lokal-bb/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $("#search").val();
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.status = $(".status").val();
                data.nama_barang = $(".nama_barang").val();
                data.note = $(".note").val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                orderable: false
            },
            {
                data: "divisi",
                className: "text-center"
            },
            {
                data: "no_penerimaan_barang",
                className: "text-center"
            },
            {
                data: "multiple_po_no",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "warehouse_name",
                className: "text-center"
            },
            {
                data: "createdAt",
                className: "text-center"
            },
            {
                data: "supplier_name",
                className: "text-center"
            },
            {
                data: "bc_type_name",
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
                    let id = row.id;
                    let status = row.status_post;
                    let tipe_bahan = row.tipe_bahan;
                    let status_post = row.status_post;
                    let bc_type = row.bc_type;
                    let in_bc = row.in_bc;
                    let retur_status = row.retur_status;


                    if (status == "WAITING") {
                        return `
                        <div class="mt-0">
                            <?php if (can('Warehouse', 'P. Barang Lokal BB', 'p')) : ?>
                                <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("penerimaan-barang-lokal-bb/print/"); ?>${id}')" style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                            <?php if (can('Warehouse', 'P. Barang Lokal BB', 'a')) : ?>
                                <button data-toggle="tooltip" title="Posting" onclick="posting('${id}')" class="btn btn-success posting-spp">
                                    <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                            <?php if (can('Warehouse', 'P. Barang Lokal BB', 'd')) : ?>
                                <button data-toggle="tooltip" title="Hapus" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    `
                    } else {
                        buttonUnpost = `<button data-toggle="tooltip" title="Unpost" class="btn btn-danger btn-print" onclick="unposting('${id}')" style="box-shadow: none !important;">
                                    <i class="fa fa-ban fa-sm" aria-hidden="true"></i>
                                </button>`;

                        string = `
                        <div class="mt-0" >
                            <?php if (can('Warehouse', 'P. Barang Lokal BB', 'p')) : ?>
                                <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("penerimaan-barang-lokal-bb/print/"); ?>${id}')" style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                           `;
                        if (bc_type !== '0' && in_bc === 'out') {
                            string += buttonUnpost;
                        }

                        if (bc_type === '0') {
                            string += buttonUnpost;
                        }

                        return string + `</div>`;
                    }

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

        $("#search,.note,.nama_barang").keyup(function() {
            table.ajax.reload();
        })

        $(".dateStart, .dateEnd, .status").change(function() {
            table.ajax.reload();
        })

        $(".status").change(function() {
            var status = $(this).val();
            if (status == "finish") {
                // Ubah Status Disbled StartDate dan EndDate menjadi false
                $(".dateStart, .dateEnd").attr('disabled', false);
            } else {
                // Ubah Status Disbled StartDate dan EndDate menjadi false
                $(".dateStart, .dateEnd").attr('disabled', true);

            }
        });

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            location.replace(`<?= base_url("penerimaan-barang-lokal-bb/id"); ?>/${data.id}`);
        })

        $('#dataTable tbody').on('click', '.return-out', function() {
            // Use the closest 'tr' element to get the data
            const data = table.row($(this).closest('tr')).data();
            location.replace(`<?= base_url("penerimaan-barang-lokal-bb/return-barang/id"); ?>/${data.id}`);
        });
    })

    const posting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Yakin akan di posting?',
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
                    url: "<?= base_url("penerimaan-barang-lokal-bb/posting"); ?>",
                    data: {
                        id: id,
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

                });
            }
        })
    }

    const unposting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Yakin akan di unposting?',
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
                    url: "<?= base_url("penerimaan-barang-lokal-bb/unposting"); ?>",
                    data: {
                        id: id,
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
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("penerimaan-barang-lokal-bb/delete"); ?>",
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
                            Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    table.ajax.reload()
                                })
                        }
                    },

                });
            }
        })
    }

    const print = function(url) {
        window.open(url, "_blank");
    }

    const pdf = function(url) {
        let search = $(".search").val();
        let status = $(".status").val();
        let dateStart = $(".dateStart").val();
        let dateEnd = $(".dateEnd").val();

        window.open(url + `?search=${search}&status=${status}&dateStart=${dateStart}&dateEnd=${dateEnd}&sort=${sort}&sortType=${sortType}`, "_blank");
    }

    const excel = function(url) {
        let search = $(".search").val();
        let status = $(".status").val();
        let dateStart = $(".dateStart").val();
        let dateEnd = $(".dateEnd").val();

        window.open(url + `?search=${search}&status=${status}&dateStart=${dateStart}&dateEnd=${dateEnd}&sort=${sort}&sortType=${sortType}`, "");
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