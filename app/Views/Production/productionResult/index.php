<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Hasil Produksi</h1>
        <?= csrf_field() ?>
        <?php if (can('Produksi', 'Hasil Produksi', 'c')): ?>
            <a class="btn btn-show-form btn-add" href="<?= base_url("production-result/create"); ?>">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
        <button class="btn btn-discard btn-show-modal mr-4 float-end" type="button" aria-haspopup="true" aria-expanded="false">
            Export
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-4 mb-3">
                    <div class="input-group">
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal Awal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="input-group">
                        <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal Akhir" value="">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Ketik Kode Produksi / Kode Penerimaan / Nama Barang" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th onclick="changeSort('wo_no')" class="sort">Kode Hasil Produksi</th>
                                <th onclick="changeSort('kode_barang')" class="sort">Kode Work Order</th>
                                <th onclick="changeSort('tanggal_hasil_barang')" class="sort">Tanggal Hasil Produksi</th>
                                <th onclick="changeSort('kode_barang')" class="sort">Kode Barang</th>
                                <th onclick="changeSort('nama_barang')" class="sort">Nama Barang</th>
                                <th onclick="changeSort('qty_barang')" class="sort">Qty Produksi</th>
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

<div class="modal" id="export_hasil_produksi" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <form class="form-excel" method="post" action="<?= base_url("production-result/export"); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Export Hasil Produksi</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select tipe_export" name="tipe_export" id="tipe_export" required>
                                    <option value="excel" selected>Excel</option>
                                    <option value="pdf">PDF</option>
                                </select>
                                <label for="floatingInput">Tipe Export</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input autocomplete="one-time-code" class="form-control input-picker date_production" id="date_production" name="date_production" placeholder="Tanggal Produksi" required>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-2">Kembali</button>
                    <button type="submit" class="btn btn-submit-form">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "wo_no";
    let sortType = "desc";

    let search = $('.search').val();
    let currentPage = 1;

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
            url: "<?= base_url("production-result/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.sort = sort;
                data.sortType = sortType;
            },
        },
        "drawCallback": function(settings) {
            //for set current page print
            currentPage = settings.json.currentPage;
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
                orderable: false
            },
            {
                data: "pr_no",
                className: "text-center"
            },
            {
                data: "wo_no",
                className: "text-center"
            },
            {
                data: "receive_date",
                className: "text-center"
            },
            {
                data: "barangCode",
                className: "text-center"
            },
            {
                data: "barangName",
                className: "text-center"
            },
            {
                data: "qty_hasil",
                className: "text-center"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    let status = row.is_posted
                    if (status != 1) {
                        return `
                                <div class="mt-0">
                                    <button type="button" onclick="handleDelete('${id}')" class="btn btn-discard delete-btn btn-trash">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    <button class="btn btn-warning">
                                        <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="posting('${id}', 1)">
                                        <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                    </button>
                                </div>
                            `
                    } else {
                        return `
                                <div class="mt-0">
                                    <button class="btn btn-warning">
                                        <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                    </button>
                                </div>
                            `
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

    $(document).ready(function() {

        $(".dateStart, .dateEnd, .date_production").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        $(".dataTable_info").addClass("pt-0");

        $(".search").keyup(function() {
            table.ajax.reload();
        });

        $(".dateStart, .dateEnd").change(function() {
            table.ajax.reload();
        })

        $('.btn-show-modal').on('click', function() {
            $('#export_hasil_produksi').modal('show');
        });

        $('.btn-hide-form').on('click', function() {
            $('#export_hasil_produksi').modal('hide');
        });

        $('.dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            if (data) {
                location.replace(`<?= base_url("production-result/details/"); ?>${data.id}`);
            }
        });
    })

    const spp = function() {
        location.replace(`<?= base_url("spp/create"); ?>`);
    }

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    const posting = function(id, status_posting) {
        const csrf = $(`[name="${csrfToken}"]`);
        console.log(id);
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
                $.ajax({
                    url: "<?= base_url("production-result/update-status"); ?>",
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
                    url: "<?= base_url("production-result/delete"); ?>",
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
</script>
<?= $this->endSection(); ?>