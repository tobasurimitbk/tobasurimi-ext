<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Work Order</h1>
        <?php if (can('Produksi', 'Work Order', 'c')): ?>
            <a class="btn btn-show-form btn-add float-right" href="<?= base_url("work-order/create"); ?>">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
        <?= csrf_field() ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-4 mb-2 mt-2">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal Mulai">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-2 mt-2">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal Akhir">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-2 mt-2">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari Data" value="" />
                </div>

            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 10px;">No</th>
                                <th onclick="changeSort('request_date')" class="sort">Tanggal</th>
                                <th onclick="changeSort('wo_no')" class="sort">Kode Produksi</th>
                                <th onclick="changeSort('barang1_id')" class="sort">Barang Jadi</th>
                                <th onclick="changeSort('department')" class="sort">Departemen</th>
                                <th onclick="changeSort('warehouse_id')" class="sort">Warehouse</th>
                                <th onclick="changeSort('work_order_details.qty')" class="sort">Qty Target</th>
                                <th style="width: 100px;">Action</th>
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

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    let sort = "work_orders.id";
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
            url: "<?= base_url("work-order/all"); ?>",
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
                data: "request_date",
                className: "text-left"
            },
            {
                data: "wo_no",
                className: "text-left"
            },
            {
                data: "barang_name",
                className: "text-left"
            },
            {
                data: "divisi",
                className: "text-left"
            },
            {
                data: "warehouse_name",
                className: "text-left"
            },
            {
                data: "qty",
                className: "text-left",
                render: function(data) {
                    return greatFormatRupiah(data);
                }
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;

                    let buttonsHTML = `
                        <div class="mt-0">
                    `;

                    <?php if (can('Produksi', 'Work Order', 'u')): ?>
                        buttonsHTML += `
                            <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                        `;
                    <?php endif; ?>

                    // Tombol hapus selalu ditampilkan
                    <?php if (can('Produksi', 'Work Order', 'd')): ?>
                        buttonsHTML += `
                            <button data-toggle="tooltip" title="Hapus" class="btn btn-danger" onclick="remove('${id}')" >
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                        `;
                    <?php endif; ?>

                    buttonsHTML += `
                        </div>
                    `;
                    return buttonsHTML;
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

        $(".dataTable_info").addClass("pt-0");

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $(".dateStart, .dateEnd").change(function() {
            table.ajax.reload();
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            location.replace(`<?= base_url("work-order/details"); ?>/${data.id}`);
        })

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

    const remove = function(id, tipe) {
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
                $.ajax({
                    url: "<?= base_url("work-order/delete"); ?>",
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
                        }
                    },
                });
            }
        })
    }

    const edit = function(id) {
        location.replace(`<?= base_url("work-order/details"); ?>/${id}`);
    }

    const toMaterialRequest = function(id) {
        // console.log(id);
        // if (id != 0) {
        //     window.location.href = "<?= base_url('material-request/details'); ?>/" + id;
        // } else {
        window.location.href = "<?= base_url('material-request'); ?>";
        // }
    }
    const toProductionResult = function(id) {
        console.log(id);
        if (id != 0) {
            window.location.href = "<?= base_url('production-result/details'); ?>/" + id;
        } else {
            window.location.href = "<?= base_url('production-result'); ?>";
        }
    }
</script>
<?= $this->endSection(); ?>