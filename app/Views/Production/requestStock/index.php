<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Request Stock</h1>
        <?= csrf_field() ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-4 mb-3">
                    <div class="form-floating">
                        <input style="height: 50px" autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Ketik Kode Request / Nama Barang" value="" />
                        <label style="z-index: 1;" style="z-index: 1;">Ketik Kode Request / Nama Barang </label>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="form-floating">
                        <select class="form-select material_type" id="material_type" name="material_type" aria-label="Floating label select example">
                            <option value="material_request">Material Request</option>
                            <option value="material_kimia">Material Kimia</option>
                            <option value="material_penolong">Material Penolong</option>
                        </select>
                        <label style="z-index: 1;">Material Type</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th onclick="changeSort('type')" class="sort">Material Type</th>
                                <th onclick="changeSort('wo_no')" class="sort">Kode Work Order</th>
                                <th onclick="changeSort('req_no')" class="sort">Kode Request</th>
                                <th onclick="changeSort('nama_barang')" class="sort">Nama Barang</th>
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
    let sort = "req_no";
    let sortType = "desc";

    let search = $('.search').val();
    let currentPage = 1;

    $('#material_type').select2({
        placeholder: "Pilih material_type",
        theme: "bootstrap-5",
    }).change(function() {
        table.ajax.reload();
    });

    $("#material_type")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-left', '-7px');

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
            url: "<?= base_url("request-stock/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.material_type = $("#material_type").val();
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
                data: "type",
                className: "text-center"
            },
            {
                data: "wo_no",
                className: "text-center"
            },
            {
                data: "req_no",
                className: "text-center"
            },
            {
                data: "nama_barang",
                className: "text-center"
            },
            {
                    data: "id",
                    className: "text-center actions",
                    searchable: false,
                    sortable: false,
                    render: function(data, type, row) {
                        let id = row.id;
                        let status = row.is_approve;
                        let buttonHtml = '';  // Inisialisasi buttonHtml kosong
                        
                        if ($("#material_type").val() === 'material_request' || $("#material_type").val() == null) {
                            if (status != 1) {
                                buttonHtml = `
                                    <button type="button" class="btn btn-primary" onclick="approve('${id}', 1)">
                                        <i class="fa fa-paper-plane"></i> Approve
                                    </button>
                                `;
                            } else {
                                buttonHtml = `
                                    <button type="button" class="btn btn-success" disabled>
                                        <i class="fa fa-check"></i> Approved
                                    </button>
                                `;
                            }
                        }
                        
                        if ($("#material_type").val() === 'material_kimia') {
                            if (status != 1) {
                                buttonHtml = `
                                    <button type="button" class="btn btn-primary" onclick="approve_penolong('${id}', 1)">
                                        <i class="fa fa-paper-plane"></i> Approve
                                    </button>
                                `;
                            } else {
                                buttonHtml = `
                                    <button type="button" class="btn btn-success" disabled>
                                        <i class="fa fa-check"></i> Approved
                                    </button>
                                `;
                            }
                        }

                        if ($("#material_type").val() === 'material_penolong') {
                            if (status != 1) {
                                buttonHtml = `
                                    <button type="button" class="btn btn-primary" onclick="approve_penolong('${id}', 1)">
                                        <i class="fa fa-paper-plane"></i> Approve
                                    </button>
                                `;
                            } else {
                                buttonHtml = `
                                    <button type="button" class="btn btn-success" disabled>
                                        <i class="fa fa-check"></i> Approved
                                    </button>
                                `;
                            }
                        }

                        return buttonHtml;  // Harus return buttonHtml
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
            // Get the data associated with the clicked row
            const data = table.row(this).data();

            // Redirect to the detail page using the data ID
            if (data) {
                location.replace(`<?= base_url("request-stock/details"); ?>/${data.id}`);
            }
        });
    })

    const approve = function(id, status_approve) {
        Swal.fire({
            icon: 'question',
            title: status_approve == "1" ? "Yakin Akan Diapprove ?" : "Yakin Akan di Unapprove ?",
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Posting',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url("request-stock/update-approve"); ?>",
                    data: {
                        id: id,
                        status_approve: status_approve
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
                        if (response) {
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

    const approve_penolong = function(id, status_approve) {
        Swal.fire({
            icon: 'question',
            title: status_approve == "1" ? "Yakin Akan Diapprove ?" : "Yakin Akan di Unapprove ?",
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Posting',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url("request-stock/update-approve-penolong"); ?>",
                    data: {
                        id: id,
                        status_approve: status_approve
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

    // const remove = function(id) {
    //     Swal.fire({
    //         icon: 'question',
    //         title: 'Yakin akan di hapus?',
    //         confirmButtonColor: '#4e73df',
    //         cancelButtonColor: '#d33',
    //         showCancelButton: true,
    //         reverseButtons: true,
    //         confirmButtonText: 'Hapus',
    //         cancelButtonText: 'Kembali',
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             const csrf = $(`[name="${csrfToken}"]`);
    //             $.ajax({
    //                 url: "<?= base_url("po-lokal-bahan-baku/delete"); ?>",
    //                 data: {
    //                     id: id
    //                 },
    //                 beforeSend: function(xhr) {
    //                     xhr.setRequestHeader('X-CSRF-Token', csrf.val());
    //                     setLoading();
    //                 },
    //                 complete: function() {
    //                     stopLoading();
    //                 },
    //                 method: "POST",
    //                 dataType: "json",
    //                 success: function(response) {
    //                     csrf.val(response.token);
    //                     if (response.status) {
    //                         Swal.fire({
    //                                 icon: 'success',
    //                                 title: response.message,
    //                                 confirmButtonColor: '#4e73df',
    //                             })
    //                             .then(() => {
    //                                 table.ajax.reload()
    //                             })
    //                     } else {
    //                         Swal.fire({
    //                             icon: 'error',
    //                             title: response.message,
    //                             confirmButtonColor: '#4e73df',
    //                         })
    //                     }
    //                 },
    //                 onError: function(response) {
    //                     csrf.val(response.token);
    //                     Swal.fire({
    //                         icon: 'error',
    //                         title: 'Data Gagal Dihapus, coba Lagi',
    //                         confirmButtonColor: '#4e73df',
    //                     })
    //                 }
    //             });
    //         }
    //     })
    // }

    // // delete
    // function handleDelete(id) {
    //     Swal.fire({
    //         icon: 'question',
    //         title: 'Hapus Data?',
    //         confirmButtonColor: '#4e73df',
    //         cancelButtonColor: '#d33',
    //         showCancelButton: true,
    //         reverseButtons: true,
    //         confirmButtonText: 'Hapus',
    //         cancelButtonText: 'Kembali',
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             const csrf = $(`[name="${csrfToken}"]`);

    //             setLoading()
    //             $.ajax({
    //                 url: "<?= base_url("material-request/delete"); ?>",
    //                 data: {
    //                     id: id
    //                 },
    //                 beforeSend: function(xhr) {
    //                     xhr.setRequestHeader('X-CSRF-Token', csrf.val());
    //                 },
    //                 method: "POST",
    //                 dataType: "json",
    //                 success: function(response) {
    //                     csrf.val(response.token);
    //                     if (response.status) {
    //                         stopLoading()
    //                         Swal.fire({
    //                                 icon: 'success',
    //                                 title: response.message,
    //                                 confirmButtonColor: '#4e73df',
    //                             })
    //                             .then(() => {
    //                                 table.ajax.reload()
    //                             })
    //                     } else {
    //                         Swal.fire({
    //                             icon: 'error',
    //                             title: response.message,
    //                             confirmButtonColor: '#4e73df',
    //                         })
    //                         stopLoading()
    //                     }
    //                 },
    //                 onError: function(response) {
    //                     csrf.val(response.token);
    //                     Swal.fire({
    //                         icon: 'error',
    //                         title: 'Data Gagal Dihapus, coba Lagi',
    //                         confirmButtonColor: '#4e73df',
    //                     })
    //                     stopLoading()
    //                 }
    //             });
    //         }
    //     })
    // }

    // const print = function(url) {
    //     window.open(url, "_blank");
    // }

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