<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Stok Adjusment</h1>
        <?php if (can("Inventori", "Stok Adjusment", "c")) : ?>
            <a class="btn btn-show-form btn-add btn-dropdown-export dropdown-toggle float-right" href="#" id="dropdownMenuButtonExport2" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #4E8A00 !important; border-color:#4E8A00 !important;">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButtonExport2">
                <li><a href="<?= base_url('stock-adjusment/create-tambah') ?>" class="dropdown-item"><b>ADJ TAMBAH</b></a></li>
                <li><a href="<?= base_url("stock-adjusment/create"); ?>" class="dropdown-item"><b>ADJ KURANG</b></a></li>
            </ul>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <?= csrf_field() ?>
            <div class="row mb-4">
                <div class="col-sm-3 mt-1">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" value="01/<?= date('m/Y') ?>" class="form-control dateStart" id="dateStart" name="dateStart" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Awal</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 mt-1">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateEnd" id="dateEnd" name="dateEnd" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Akhir</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 mt-1">
                    <div class="form-floating">
                        <select class="form-select tipe_adjusment" id="tipe_adjusment" name="tipe_adjusment">
                            <option value=""></option>
                            <?php foreach ($tipeAdjusment as $t) : ?>
                                <option value="<?= $t['id'] ?>">
                                    <?= $t['value']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Tipe Adjusment</label>
                    </div>
                </div>
                <div class="col-sm-3 mt-1">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search" id="search" name="search" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Data </label>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th onclick="changeSort('adjusment.divisi_id')">Dept</th>
                            <th onclick="changeSort('adjusment.no_adjusment')">No Adjusment</th>
                            <th onclick="changeSort('adjusment.tanggal')">Tgl</th>
                            <th onclick="changeSort('adjusment.keterangan')">Keterangan</th>
                            <th onclick="changeSort('adjusment.tipe_adjusment')">Tipe</th>
                            <th onclick="changeSort('adjusment.createdBy')">Dibuat Oleh</th>
                            <th onclick="changeSort('adjusment.status_posting')">Posting</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="body-table" id="body-table">
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</section>



<script>
    let sort = "adjusment.no_adjusment";
    let sortType = "desc";
    const csrfToken = '<?= csrf_token() ?>';

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
        pageLength: 25,
        ajax: {
            url: "<?= base_url("stock-adjusment/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.tipe_adjusment = $(".tipe_adjusment").val();
                data.search = $(".search").val();
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
                className: "text-left",
                orderable: false
            },
            {
                data: "divisi",
                className: "text-left"
            },
            {
                data: "no_adjusment",
                className: "text-left"
            },
            {
                data: "tanggal",
                className: "text-left",
            },
            {
                data: "keterangan",
                className: "text-left"
            },
            {
                data: "tipe_adjusment",
                className: "text-left",
            },
            {
                data: "created_by",
                className: "text-left"
            },
            {
                data: "status_posting",
                className: "text-center",
                searchable: false,
                width: "5%",
                render: function(data, type, row) {
                    let htmlRes = '';

                    if (row.status_posting == 1) {
                        htmlRes += `
                            <div class="text-success">
                               <i class="fa-solid fa-check"></i>
                            </div>`
                    } else {
                        htmlRes += `
                            <div class="text-danger">
                               <i class="fa-solid fa-x"></i>
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
                    let status_posting = row.status_posting;
                    let jenis_adjusment = row.jenis_adjusment;

                    if (status_posting == 0) {
                        return `
                        <div class="mt-0">
                             <a href="javascript:void(0)" onclick="edit('${id}','${jenis_adjusment}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if (can('Inventori', 'Stok Adjusment', 'a')) : ?>
                                <button data-toggle="tooltip" title="Posting" onclick="posting('${id}','${jenis_adjusment}')" class="btn btn-success posting-spp">
                                    <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                            <?php if (can('Inventori', 'Stok Adjusment', 'd')) : ?>
                                <button data-toggle="tooltip" title="Hapus" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    `
                    } else {
                        return `
                        <a href="javascript:void(0)" onclick="edit('${id}','${jenis_adjusment}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
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
    });

    $('#tipe_adjusment').select2({
        placeholder: "Pilih Tipe Adjusment",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        table.ajax.reload();
    });

    $("#divisi_id,#tipe_adjusment")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $(".search").keyup(function() {
        table.ajax.reload();
    });

    function edit(id, jenis_adjusment) {
        if (jenis_adjusment == "UPDATE") {
            window.location.href = "<?= base_url('stock-adjusment/id') ?>" + '/' + id
        } else {
            window.location.href = "<?= base_url('stock-adjusment/id-tambah') ?>" + '/' + id
        }
    }

    $('#dateStart,#dateEnd').change(function() {
        table.ajax.reload();
    });

    function posting(id, jenis_adjusment) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Adjusment ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                const url = jenis_adjusment == "UPDATE" ? "<?= base_url("stock-adjusment/posting"); ?>" : "<?= base_url("stock-adjusment/posting-tambah"); ?>";
                $.ajax({
                    url: url,
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

    function remove(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Adjusment ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("stock-adjusment/delete"); ?>",
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