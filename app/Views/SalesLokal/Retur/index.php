<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Return Barang Sales</h1>
        <?= csrf_field() ?>
        <?php if (can('Penjualan Lokal', 'Return Barang', 'c')) : ?>
            <a class="btn btn-show-form btn-add float-right" href="<?= base_url("return-barang-sales/create"); ?>">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Nomor Return</th>
                                <th>Nama Customer</th>
                                <th>No. Invoice</th>
                                <th>Tanggal Return</th>
                                <th>Status Pembayaran</th>
                                <th>Approved</th>
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

    let sort = "";
    let sortType = "desc";
    let trigger = true;

    let list_address = [];
    let list_delete = [];
    var row = 0;

    $(document).ready(function() {
        $(".search").keyup(function() {
            table.ajax.reload();
        })
    })

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
            url: "<?= base_url("return-barang-sales/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
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
            sortable: false
        }, {
            data: "returnNo",
            className: "text-center"
        }, {
            data: "customerName",
            className: "text-center"
        }, {
            data: "invNo",
            className: "text-center"
        }, {
            data: "returnDate",
            className: "text-center"
        }, {
            data: "already_paid",
            className: "text-center",
            render: function(data, type, row) {
                return data == 1 ? "Sudah Dibayar" : "Belum Dibayar";
            }
        }, {
            data: "is_approved",
            className: "text-center actions",
            searchable: false,
            sortable: false,
            render: function(data, type, row) {
                let id = row.id;
                let status = row.is_approved;
                let buttonHtml = ''; // Inisialisasi buttonHtml kosong

                if (status != 1) {
                    buttonHtml = `
                                    <button type="button" class="btn btn-primary" onclick="approve('${id}', 1)">
                                        <i class="fa fa-paper-plane"></i> Approve
                                    </button>
                                    <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                `;
                } else {
                    buttonHtml = `
                                    <button type="button" class="btn btn-success" disabled>
                                        <i class="fa fa-check"></i> Approved
                                    </button>
                                    <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                `;
                }

                return buttonHtml; // Harus return buttonHtml
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

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    const approve = function(id, status_approve) {
        Swal.fire({
            icon: 'question',
            title: status_approve == "1" ? "Yakin Akan Diapprove ?" : "Yakin Akan di Unapprove ?",
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Approved',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url("return-barang-sales/approve"); ?>",
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
                        // Update CSRF token
                        csrf.val(response.token);

                        // Check if the status in the response is true
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            }).then(() => {
                                table.ajax.reload(); // Reload the table data
                            });
                        } else {
                            // If status is false, display an error message
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            });
                        }
                    },
                    error: function(xhr) {
                        // Handle any AJAX error (e.g., network issues, server errors)
                        csrf.val(xhr.responseJSON.token); // Update CSRF token if available
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Gagal Di Approved, coba Lagi',
                            confirmButtonColor: '#4e73df',
                        });
                    }
                });
            }
        })
    }

    function edit(id){
        location.replace(`<?= base_url("return-barang-sales/details/"); ?>/${id}`);
    }
</script>
<?= $this->endSection(); ?>