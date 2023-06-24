<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Surat Permintaan Pembelian</h1>
        <a class="btn btn-show-form btn-add float-right" href="<?= base_url("spp/create"); ?>">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </a>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end mb-3 row-col-spp">
                <div class="col">
                    <?= csrf_field() ?>
                    <div class="input-group input-group-password">
                        <input class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group input-group-password">
                        <input class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <input class="form-control search form-out-search" placeholder="Search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th onclick="changeSort('sppType')" class="sort">Tipe SPP</th>
                                <th onclick="changeSort('sppNo')" class="sort">No. SPP</th>
                                <th onclick="changeSort('warehouse')" class="sort">Departemen</th>
                                <th onclick="changeSort('orderType')" class="sort">Jenis Order</th>
                                <th onclick="changeSort('total')" class="sort">Total Harga</th>
                                <th onclick="changeSort('requestDate')" class="sort">Tanggal Order</th>
                                <th>Order Oleh</th>
                                <th>Disetujui</th>
                                <th>Penerima</th>
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
    let sort = "sppType";
    let sortType = "asc";
    let role_id = '<?= session()->get("login")->this_role_id; ?>';

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
            url: "<?= base_url("spp/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.sort = sort;
                data.sortType = sortType;
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
                orderable: false
            },
            {
                data: "spp_type",
                className: "text-center"
            },
            {
                data: "spp_no",
                className: "text-center"
            },
            {
                data: "warehouseName",
                className: "text-center"
            },
            {
                data: "orderTypeName",
                className: "text-center"
            },
            {
                data: "total",
                className: "text-center"
            },
            {
                data: "request_date",
                className: "text-center"
            },
            {
                data: "approvedByHeadwarehouseName",
                className: "text-center actions",
                orderable: false,
                render: function(data, type, row) {
                    console.log(data);
                    if (data === "false" && role_id === '22') {
                        return `<input onchange="approveHeadWarehouse('${row.id}')" type="checkbox" ${data !== "false" ? "checked" : ""} id="approved_by_headwarehouse_${row.id}"/>`
                    }
                    if (data !== "false") {
                        return data;
                    }
                }
            },
            {
                data: "approvedByDirectorName",
                className: "text-center actions",
                orderable: false,
                render: function(data, type, row) {
                    if (data === "false" && role_id === '21') {
                        return `<input onchange="approveDirector('${row.id}')" type="checkbox" ${data !== "false" ? "checked" : ""} id="approved_by_director_${row.id}"/>`
                    }
                    if (data !== "false") {
                        return data;
                    }
                }
            },
            {
                data: "approvedByHeadofPurchasingName",
                className: "text-center actions",
                orderable: false,
                render: function(data, type, row) {
                    if (data === "false" && role_id === '23') {
                        return `<input onchange="approveHeadPurchasing('${row.id}')" type="checkbox" ${data !== "false" ? "checked" : ""} id="approved_by_head_of_purchasing_${row.id}"/>`
                    }
                    if (data !== "false") {
                        return data;
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

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $(".dateStart, .dateEnd").change(function() {
            table.ajax.reload();
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            location.replace(`<?= base_url("spp/id"); ?>/${data.id}`);
        })
    })

    const approveHeadWarehouse = function(id) {
        const csrf = $(`[name="${csrfToken}"]`);
        let value = document.getElementById('approved_by_headwarehouse_' + id).checked ? true : false;

        let data = {
            id: id
        }

        if (value) {
            data["status"] = true;
        }

        $.ajax({
            url: "<?= base_url("spp/approve"); ?>",
            data: data,
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
                    title: 'Approve Gagal Diubah, coba Lagi',
                    confirmButtonColor: '#4e73df',
                })
            }
        });
    }

    const approveHeadPurchasing = function(id) {
        const csrf = $(`[name="${csrfToken}"]`);
        let value = document.getElementById('approved_by_head_of_purchasing_' + id).checked ? true : false;

        let data = {
            id: id
        }

        if (value) {
            data["status"] = true;
        }

        $.ajax({
            url: "<?= base_url("spp/approve"); ?>",
            data: data,
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
                    title: 'Approve Gagal Diubah, coba Lagi',
                    confirmButtonColor: '#4e73df',
                })
            }
        });
    }

    const approveDirector = function(id) {
        const csrf = $(`[name="${csrfToken}"]`);
        let value = document.getElementById('approved_by_director_' + id).checked ? true : false;

        let data = {
            id: id
        }

        if (value) {
            data["status"] = true;
        }

        $.ajax({
            url: "<?= base_url("spp/approve"); ?>",
            data: data,
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
                    title: 'Approve Gagal Diubah, coba Lagi',
                    confirmButtonColor: '#4e73df',
                })
            }
        });
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