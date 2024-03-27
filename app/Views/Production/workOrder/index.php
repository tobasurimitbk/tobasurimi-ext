<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Work Order</h1>

        <a class="btn btn-show-form btn-add float-right" href="<?= base_url("work-order/create"); ?>">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </a>
        <?= csrf_field() ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-4 mb-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Ketik Kode Produksi / Nama Barang" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th onclick="changeSort('wo_no')" class="sort">Kode Produksi</th>
                                <th onclick="changeSort('nama_barang')" class="sort">Nama Barang</th>
                                <th onclick="changeSort('department')" class="sort">Department</th>
                                <th>Action</th>
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
    const csrf = $(`[name="${csrfToken}"]`);
    let sort = "wo_no";
    let sortType = "desc";

    let search = $('.search').val();
    let currentPage = 1;

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
            url: "<?= base_url("work-order/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
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
                data: "wo_no",
                className: "text-center"
            },
            {
                data: "nama_barang",
                className: "text-center"
            },
            {
                data: "nama_divisi",
                className: "text-center"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row?.id;
                    let id_material_request = row?.id_material_request;
                    let id_production_result = row?.id_production_result;

                    let buttonsHTML = `
                        <div class="mt-0">
                    `;
                    buttonsHTML += `
                        <button class="btn btn-primary to-material-request" onclick="toMaterialRequest('${id_material_request}')" >
                            <i class="fa fa-phone fa-sm" aria-hidden="true"></i>
                        </button>
                    `;
                    if (id_production_result) {
                        buttonsHTML += `
                            <button class="btn btn-warning to-production-result" onclick="toProductionResult('${id_production_result}')" >
                                <i class="fa fa-folder fa-sm" aria-hidden="true"></i>
                            </button>
                        `;
                    }
                    // Tombol hapus selalu ditampilkan
                    buttonsHTML += `
                        <button class="btn btn-danger" onclick="remove('${id}')" >
                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                        </button>
                    `;
                    buttonsHTML += `
                        </div>
                    `;
                    return buttonsHTML;
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

        $(".dataTable_info").addClass("pt-0");

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            location.replace(`<?= base_url("work-order/details"); ?>/${data.id}`);
        })
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
            cancelButtonText: 'Batal',
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
    const toMaterialRequest = function(id) {
        console.log(id);
        if (id != 0) {
            window.location.href = "<?= base_url('material-request/details'); ?>/" + id;
        } else {
            window.location.href = "<?= base_url('material-request'); ?>";
        }
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