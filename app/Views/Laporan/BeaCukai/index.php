<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1>Bea Cukai</h1>
    <a class="btn btn-show-form btn-add float-right" href="<?= base_url("bea-cukai/create"); ?>">
        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
    </a>
</div>
<div class="card">
    <div class="card-body">
        <?= csrf_field() ?>
        <div class="row justify-content-end row-col-spp">
            <div class="col-md-3">
                <select class="form-select status" name="status" id="status" aria-label="Floating label select example">
                    <option value="waiting">WAITING</option>
                    <option value="finish">FINISH</option>
                </select>
            </div>
            <div class="col-md-3">
                <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari No. Bea Cukai" value="" />
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No.</th>
                            <th onclick="changeSort('no_bea_cukai')" class="sort">No. Dokumen</th>
                            <th onclick="changeSort('status_po')" class="sort">Jenis PO</th>
                            <th onclick="changeSort('tipe_bahan')" class="sort">Tipe PO</th>
                            <th>No. PO</th>
                            <th onclick="changeSort('createdAt')" class="sort">Tanggal</th>
                            <th>Status</th>
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
    let sort = "no_bea_cukai";
    let sortType = "desc";

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [[1, 'desc']],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("bea-cukai/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
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
            orderable: false,
            width: "5%"
        },
        {
            data: "no_bea_cukai",
            className: "text-center"
        },
        {
            data: "status_po",
            className: "text-center"
        },
        {
            data: "tipe_bahan",
            className: "text-center"
        },
        {
            data: "po_no",
            className: "text-center",
            searchable: false,
            sortable: false
        },
        {
            data: "createdAt",
            className: "text-center"
        },
        {
            data: "status_post",
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

                    if (status == "WAITING") {
                        return `
                            <div class="mt-0">
                            <button onclick="posting('${id}')" class="btn btn-success posting-spp">
                                <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                            </button>
                            <button onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                            </div>
                        `
                    } else {
                        return `
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
        $(".dataTable_info").addClass("pt-0");

        $(".search").keyup(function () {
            table.ajax.reload();
        })

        $(".status").change(function () {
            table.ajax.reload();
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            location.replace(`<?= base_url("bea-cukai/id"); ?>/${data.id}`);
        })
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
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("bea-cukai/update-status"); ?>",
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

    const remove = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Yakin akan di hapus?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("bea-cukai/delete"); ?>",
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