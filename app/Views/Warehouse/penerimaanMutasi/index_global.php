<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Penerimaan Mutasi BC 2.7</h1>
        <?php if (can("Inventori", "Penerimaan Mutasi", "c")) : ?>
            <a href="<?= base_url('penerimaan-mutasi/create-global') ?>" type="button" class="btn btn-show-form btn-add float-right">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('penerimaan-mutasi') ?>">Penerimaan Mutasi PPBKB</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#">Penerimaan Mutasi BC 2.7 (BC 2.7 IN)</a>
                </li>
            </ul>
            <?= csrf_field() ?>
            <div class="row mb-4">
                <div class="col-sm-4 mt-2">
                    <div class="form-floating">
                        <select class="form-select company_pengirim_id" id="company_pengirim_id" name="company_pengirim_id" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php foreach ($dropdownCompanyExcept as $d) : ?>
                                <option value="<?= $d['id'] ?>">
                                    <?= strtoupper($d['company']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Company Pengirim</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
                    <div class="form-floating">
                        <select class="form-select status" id="status" name="status" aria-label="Floating label select example">
                            <option value="">SEMUA</option>
                            <option value="1">POSTED</option>
                            <option value="0">WAITING</option>
                        </select>
                        <label style="z-index: 1;">Status Penerimaan Mutasi</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search penerimaan_mutasi_no" id="search" name="search" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Nomor Penerimaan Mutasi </label>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search multiple_no_mutasi" id="multiple_no_mutasi" name="multiple_no_mutasi" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Nomor Mutasi </label>
                    </div>
                </div>
                <div class="col-md-4 mt-2">
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
                <div class="col-md-4 mt-2">
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
            </div>
            <div class="table-responsive">
                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th onclick="changeSort('penerimaan_mutasi_no')">No Penerimaan Mutasi</th>
                            <th onclick="changeSort('penerimaan_mutasi_global.multiple_mutasi_no')">Nomor Mutasi</th>
                            <th onclick="changeSort('penerimaan_mutasi_global.tanggal')">Tanggal Penerimaan</th>
                            <th onclick="changeSort('penerimaan_mutasi_global.divisi_penerima_id')">Departemen Penerima</th>
                            <th onclick="changeSort('penerimaan_mutasi_global.warehouse_penerima_id')">Warehouse Penerima</th>
                            <th onclick="changeSort('penerimaan_mutasi_global.company_pengirim_id')">Company Pengirim</th>
                            <th>Departemen Pengirim</th>
                            <th>Dokumen Mutasi</th>
                            <th>Total Item</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="body-table" id="body-table" style="cursor: pointer;">
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</section>



<script>
    let sort = "createdAt";
    let sortType = "desc";
    const csrfToken = '<?= csrf_token() ?>';

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
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
        pageLength: 25,
        ajax: {
            url: "<?= base_url("penerimaan-mutasi/all-global"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.company_pengirim_id = $(".company_pengirim_id").val();
                data.status = $(".status").val();
                data.penerimaan_mutasi_no = $(".penerimaan_mutasi_no").val();
                data.multiple_mutasi_no = $(".multiple_no_mutasi").val();
                data.dateStart = $('#dateStart').val();
                data.dateEnd = $('#dateEnd').val();

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
                data: "penerimaan_mutasi_no",
                className: "text-center",

            },
            {
                data: "multiple_no_mutasi",
                className: "text-center",
                searchable: false,
                sortable: false,
            },
            {
                data: "tanggal",
                className: "text-center"
            },
            {
                data: "divisi_penerima",
                className: "text-center"
            },
            {
                data: "warehouse_penerima",
                className: "text-center"
            },
            {
                data: "company_pengirim",
                className: "text-center"
            },
            {
                data: "divisi_pengirim",
                className: "text-center",
                searchable: false,
                sortable: false,
            },

            {
                data: "dokumen_mutasi_barang",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "total_item",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "status_posting",
                className: "text-center",
                render: function(data, type, row) {
                    let htmlRes = '';

                    if (row.status_posting == "1") {
                        htmlRes += `
                        <div class="text-success">
                            <b>SUDAH POSTING</b>
                        </div>`
                    } else {
                        htmlRes += `
                        <div class="text-danger">
                           <b>BELUM POSTING<b/>
                        </div>`
                    }

                    return htmlRes;

                }
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    let status = row.status_posting

                    if (status === "0") {
                        return `
                        <div class="mt-0">
                        <?php if (can('Inventori', 'Penerimaan Mutasi', 'a')) : ?>
                            <button data-toggle="tooltip" title="Posting" onclick="posting('${id}')" class="btn btn-success posting-spp">
                                <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                        <?php if (can('Inventori', 'Penerimaan Mutasi', 'p')) : ?>
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("penerimaan-mutasi/print-global/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                        <?php if (can('Inventori', 'Penerimaan Mutasi', 'd')) : ?>
                            <button data-toggle="tooltip" title="Hapus" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                        </div>
                    `
                    } else {
                        return `
                        <?php if (can('Inventori', 'Penerimaan Mutasi', 'p')) : ?>
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("penerimaan-mutasi/print-global/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>

                    `
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

    $('#company_pengirim_id').select2({
        placeholder: "Pilih Company Pengirim",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        table.ajax.reload();
    });

    $('#status').select2({
        placeholder: "Pilih Status",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        table.ajax.reload();
    });

    $('#multiple_no_mutasi').keyup(function() {
        table.ajax.reload();
    })


    $('#dateStart,#dateEnd').change(function() {
        table.ajax.reload();
    });

    $("#company_pengirim_id,#status")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $(".penerimaan_mutasi_no").keyup(function() {
        table.ajax.reload();
    })

    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        location.replace(`<?= base_url("penerimaan-mutasi/id-global"); ?>/${data.id}`);
    });


    const posting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Penerimaan Mutasi ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("penerimaan-mutasi/posting-global"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                            }).then((result) => {
                                table.ajax.reload()
                            });
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
            title: 'Hapus Penerimaan Mutasi ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("penerimaan-mutasi/delete-global"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                            }).then((result) => {
                                table.ajax.reload()
                            });
                        }
                    },
                });
            }
        })
    }

    const print = function(url) {
        window.open(url, "_blank");
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