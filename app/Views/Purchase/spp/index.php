<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Surat Permintaan Pembelian</h1>

        <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <li><button class="dropdown-item" onclick="pdf('<?= base_url("spp/print-table"); ?>')">PDF</button></li>
        </ul>

        <a class="btn btn-show-form btn-add float-right" href="<?= base_url("spp/create"); ?>">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </a>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col mb-3">
                    <?= csrf_field() ?>
                    <div class="input-group input-group-password">
                        <input class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal Awal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <div class="input-group input-group-password">
                        <input class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal Akhir">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select kategori spp_type form-out-search" name="spp_type" id="spp_type" aria-label="Floating label select example">
                            <option value="">Pilih Tipe SPP</option>
                            <option value="Bahan Penolong Lokal">Bahan Penolong Lokal</option>
                            <option value="Bahan Penolong Import">Bahan Penolong Import</option>
                            <option value="Bahan Baku Lokal">Bahan Baku Lokal</option>
                            <option value="Bahan Baku Import">Bahan Baku Import</option>
                        </select>
                        <label for="floatingInput">Tipe SPP</label>
                    </div>
                </div>
                <div class="col mb-3">
                    <input class="form-control search form-out-search" placeholder="Ketik No SPP" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th onclick="changeSort('sppType')" class="sort">Tipe SPP</th>
                                <th onclick="changeSort('sppNo')" class="sort">No. SPP</th>
                                <th onclick="changeSort('warehouse')" class="sort">Departemen</th>
                                <th onclick="changeSort('total')" class="sort">Total Harga</th>
                                <th onclick="changeSort('requestDate')" class="sort">Tanggal Order</th>
                                <th onclick="changeSort('createdAt')" class="sort">Tanggal Dibuat</th>
                                <th>Posting</th>
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
    let sort = "createdAt";
    let sortType = "desc";

    let search = $('.search').val();
    // let spp_type = $('.spp_type').val();
    let currentPage = 1;

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [6, 'desc']
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
                data.spp_type = $(".spp_type").val();;
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
                data: "total",
                className: "text-center"
            },
            {
                data: "request_date",
                className: "text-center"
            },
            {
                data: "createdAt",
                className: "text-center"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row?.id;
                    let status = row?.is_posted
                    if (status !== "1") {
                        return `
                            <div class="mt-0">
                                <button onclick="postingSpp(${id})" class="btn btn-success posting-spp">
                                    Posting
                                </button>
                            </div>
                        `
                    } else {
                        return `
                            <div class="mt-0">
                                <label>
                                    Posted
                                </label>
                            </div>
                        `
                    }
                }
            },
            // {
            //     data: "approvedByHeadwarehouseName",
            //     className: "text-center actions",
            //     orderable: false,
            //     render: function(data, type, row) {
            //         if (row.isApproveWarehouse) {
            //             return `<input onchange="approveHeadWarehouse('${row.id}')" type="checkbox" ${data !== "false" ? "checked" : ""} id="approved_by_headwarehouse_${row.id}"/>`
            //         }
            //         if (data !== "false") {
            //             return data;
            //         }
            //     }
            // },
            // {
            //     data: "approvedByDirectorName",
            //     className: "text-center actions",
            //     orderable: false,
            //     render: function(data, type, row) {
            //         if (row.isApproveDirector) {
            //             return `<input onchange="approveDirector('${row.id}')" type="checkbox" ${data !== "false" ? "checked" : ""} id="approved_by_director_${row.id}"/>`
            //         }
            //         if (data !== "false") {
            //             return data;
            //         }
            //     }
            // },
            // {
            //     data: "approvedByHeadofPurchasingName",
            //     className: "text-center actions",
            //     orderable: false,
            //     render: function(data, type, row) {
            //         if (row.isApprovePurchasing) {
            //             return `<input onchange="approveHeadPurchasing('${row.id}')" type="checkbox" ${data !== "false" ? "checked" : ""} id="approved_by_head_of_purchasing_${row.id}"/>`
            //         }
            //         if (data !== "false") {
            //             return data;
            //         }
            //     }
            // }
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

        $(".spp_type").change(function() {
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

    const postingSpp = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Yakin akan di Posting?',
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
                    url: "<?= base_url("spp/update-status"); ?>",
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

    // const approveHeadWarehouse = function(id) {
    //     const csrf = $(`[name="${csrfToken}"]`);
    //     let value = document.getElementById('approved_by_headwarehouse_' + id).checked ? true : false;

    //     let data = {
    //         id: id
    //     }

    //     if (value) {
    //         data["status"] = true;
    //     }

    //     $.ajax({
    //         url: "<?= base_url("spp/approve"); ?>",
    //         data: data,
    //         beforeSend: function(xhr) {
    //             xhr.setRequestHeader('X-CSRF-Token', csrf.val());
    //         },
    //         method: "POST",
    //         dataType: "json",
    //         success: function(response) {
    //             csrf.val(response.token);
    //             if (response.status) {
    //                 Swal.fire({
    //                         icon: 'success',
    //                         title: response.message,
    //                         confirmButtonColor: '#4e73df',
    //                     })
    //                     .then(() => {
    //                         table.ajax.reload()
    //                     })
    //             } else {
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: response.message,
    //                     confirmButtonColor: '#4e73df',
    //                 })
    //             }
    //         },
    //         onError: function(response) {
    //             csrf.val(response.token);
    //             Swal.fire({
    //                 icon: 'error',
    //                 title: 'Approve Gagal Diubah, coba Lagi',
    //                 confirmButtonColor: '#4e73df',
    //             })
    //         }
    //     });
    // }

    // const approveHeadPurchasing = function(id) {
    //     const csrf = $(`[name="${csrfToken}"]`);
    //     let value = document.getElementById('approved_by_head_of_purchasing_' + id).checked ? true : false;

    //     let data = {
    //         id: id
    //     }

    //     if (value) {
    //         data["status"] = true;
    //     }

    //     $.ajax({
    //         url: "<?= base_url("spp/approve"); ?>",
    //         data: data,
    //         beforeSend: function(xhr) {
    //             xhr.setRequestHeader('X-CSRF-Token', csrf.val());
    //         },
    //         method: "POST",
    //         dataType: "json",
    //         success: function(response) {
    //             csrf.val(response.token);
    //             if (response.status) {
    //                 Swal.fire({
    //                         icon: 'success',
    //                         title: response.message,
    //                         confirmButtonColor: '#4e73df',
    //                     })
    //                     .then(() => {
    //                         table.ajax.reload()
    //                     })
    //             } else {
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: response.message,
    //                     confirmButtonColor: '#4e73df',
    //                 })
    //             }
    //         },
    //         onError: function(response) {
    //             csrf.val(response.token);
    //             Swal.fire({
    //                 icon: 'error',
    //                 title: 'Approve Gagal Diubah, coba Lagi',
    //                 confirmButtonColor: '#4e73df',
    //             })
    //         }
    //     });
    // }

    // const approveDirector = function(id) {
    //     const csrf = $(`[name="${csrfToken}"]`);
    //     let value = document.getElementById('approved_by_director_' + id).checked ? true : false;

    //     let data = {
    //         id: id
    //     }

    //     if (value) {
    //         data["status"] = true;

    //         $.ajax({
    //             url: "<?= base_url("spp/approve"); ?>",
    //             data: data,
    //             beforeSend: function(xhr) {
    //                 xhr.setRequestHeader('X-CSRF-Token', csrf.val());
    //             },
    //             method: "POST",
    //             dataType: "json",
    //             success: function(response) {
    //                 csrf.val(response.token);
    //                 if (response.status) {
    //                     Swal.fire({
    //                             icon: 'success',
    //                             title: response.message,
    //                             confirmButtonColor: '#4e73df',
    //                         })
    //                         .then(() => {
    //                             table.ajax.reload()
    //                         })
    //                 } else {
    //                     Swal.fire({
    //                         icon: 'error',
    //                         title: response.message,
    //                         confirmButtonColor: '#4e73df',
    //                     })
    //                 }
    //             },
    //             onError: function(response) {
    //                 csrf.val(response.token);
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: 'Approve Gagal Diubah, coba Lagi',
    //                     confirmButtonColor: '#4e73df',
    //                 })
    //             }
    //         });
    //     }
    // }

    const pdf = function(url) {
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