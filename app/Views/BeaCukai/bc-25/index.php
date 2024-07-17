<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Dokumen BC 2.5</h1>

        <div class="col-button-tambah-spp">
            <?php if ($akunCeisa != null) : ?>
                <?php if ($akunCeisa['status_integrasi']) : ?>
                    <a href="<?= base_url('bea-cukai-bc-25/online') ?>" class="btn btn-discard btn-dropdown-export float-right" type="button">
                        <i class="fa fa-upload fa-sm" aria-hidden="true"></i>
                        Status Respon
                    </a>
                    <a href="<?= base_url('bea-cukai-bc-25/bc-25-outstanding') ?>" class="btn btn-save float-right" type="button">
                        <i class="fa fa-ship fa-sm" aria-hidden="true"></i>
                        Outstanding
                    </a>
                    <?php if (can("Bea Cukai", "BC 2.5", "c")) : ?>
                        <a href="<?= base_url('bea-cukai-bc-25/create') ?>" type="button" class="btn btn-success float-right">
                            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <?= csrf_field() ?>
        <div class="card-body">
            <div class="row justify-content-start row-col-spp">
                <div class="col-md-3 mb-3">
                    <?= csrf_field() ?>
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker selesaiTanggalBC25" id="mulaiTanggalBC25" name="mulaiTanggalBC25" placeholder="Mulai Tanggal BC 2.5 Dibuat">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-mulaiTanggalBC25"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker selesaiTanggalBC25" id="selesaiTanggalBC25" name="selesaiTanggalBC25" placeholder="Selesai Tanggal BC 2.5 Dibuat">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-selesaiTanggalBC25"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <select name="statusPosting" class="form-select statusPosting" id="statusPosting">
                        <option selected value="ALL">STATUS POSTING : SEMUA</option>
                        <option value="SUDAH POSTING">STATUS POSTING : SUDAH POSTING</option>
                        <option value="BELUM POSTING">STATUS POSTING : BELUM POSTING</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <input autocomplete="one-time-code" class="form-control noAju search form-out-search" placeholder="Cari Nomor Aju BC 2.5 / No Daftar" value="" />
                </div>
            </div>
            <?php if ($akunCeisa == null) : ?>
                <div class="alert alert-danger mt-3 mb-3" role="alert">
                    SILAHKAN HUBUNGKAN AKUN CEISA BEA CUKAI TERLEBIH DAHULU SEBELUM MENGGUNAKAN MODUL INI
                </div>
            <?php else : ?>
                <?php if ($akunCeisa['status_integrasi'] === "0") : ?>
                    <div class="alert alert-danger mt-3 mb-3" role="alert">
                        SILAHKAN HUBUNGKAN AKUN CEISA BEA CUKAI TERLEBIH DAHULU SEBELUM MENGGUNAKAN MODUL INI
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;">No</th>
                                <th onclick="changeSort('sales_order_lain.divisi_id')" style="text-align: center;">Departemen</th>
                                <th onclick="changeSort('sales_order_lain.warehouse_id')" style="text-align: center;">Warehouse</th>
                                <th onclick="changeSort('sales_order_lain.no_sales_order')" class="sort" style="text-align: center;">No Order Form</th>
                                <th onclick="changeSort('customers.name')" style="text-align: center;">Customer</th>
                                <th onclick="changeSort('bc_25.no_aju')" class="sort" style="text-align: center;">No Aju / Daftar</th>
                                <th onclick="changeSort('bc_25.createdAt')" class="sort" style="text-align: center;">Tanggal BC 2.5</th>
                                <th onclick="changeSort('bc_25.status_posting')" style="text-align: center;">Status Posting</th>
                                <th onclick="changeSort('bc_25.status_dokumen')" style="text-align: center;">Status Dokumen</th>
                                <th style="text-align: center;">Action</th>
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
    let sort = "bc_25.id";
    let sortType = "desc";

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
            url: "<?= base_url("bea-cukai-bc-25/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.mulaiTanggalBC25 = $('.mulaiTanggalBC25').val();
                data.selesaiTanggalBC25 = $('.selesaiTanggalBC25').val();
                data.statusPosting = $('.statusPosting').val();
                data.noAju = $('.noAju').val();
                data.tipeSalesOrder = $('.tipeSalesOrder').val();
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
                sortable: false,
                width: "5%"
            },
            {
                data: "divisi",
                className: "text-center"
            },
            {
                data: "warehouse_name",
                className: "text-center",
            },
            {
                data: "no_sales_order",
                className: "text-center",
            },
            {
                data: "customer_name",
                className: "text-center",
            },
            {
                data: "no_aju",
                className: "text-center",
            },
            {
                data: "tanggal_bc_25",
                className: "text-center"
            },
            {
                data: "status_posting",
                className: "text-center",
                render: function(data, type, row) {
                    let htmlRes = '';

                    if (row.status_posting == "1") {
                        htmlRes += `
                            <div class="text-success">
                                SUDAH POSTING
                            </div>`
                    } else {
                        htmlRes += `
                            <div class="text-danger">
                                BELUM POSTING
                            </div>`
                    }

                    return htmlRes;
                }
            },
            {
                data: "status_dokumen",
                className: "text-center",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let htmlRes = '';

                    if (row.status_dokumen == "BELUM DIBUAT") {
                        htmlRes += `
                        <div class="text-danger">
                            BELUM DIBUAT
                        </div>`
                    } else if (row.status_dokumen == "BELUM LENGKAP") {
                        htmlRes += `
                        <div class="text-warning">
                            BELUM LENGKAP
                        </div>`
                    } else if (row.status_dokumen == "SUDAH KIRIM") {
                        htmlRes += `
                        <div class="text-success">
                            SUDAH KIRIM
                        </div>`
                    } else if (row.status_dokumen == "SIAP KIRIM") {
                        htmlRes += `
                        <div class="text-primary">
                            SIAP KIRIM
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
                    let htmlRes = '';

                    if (row.status_posting == "0") {
                        <?php if (can('Bea Cukai', 'BC 2.5', 'd')) : ?>
                            htmlRes += `
                                <button data-toggle="tooltip" title="Hapus" onclick="deleteAction('${row.id}')" class="btn btn-danger delete-parent">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>
                            `;
                        <?php endif; ?>
                        <?php if (can('Bea Cukai', 'BC 2.5', 'a')) : ?>
                            htmlRes += `
                                <button data-toggle="tooltip" title="Posting" onclick="postingAction('${row.id}')" class="btn btn-success posting-spp">
                                    <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                                </button>
                            `;
                        <?php endif; ?>
                    } else {
                        htmlRes += `

                            `;
                    }

                    if (row.status_dokumen === "SIAP KIRIM") {
                        htmlRes += `
                                <button data-toggle="tooltip" title="Kirim Ke Ceisa" onclick="kirimCeisaAction('${row.id}')" class="btn btn-info kirim-ceisa-parent">
                                    <i class="fa fa-upload fa-sm" aria-hidden="true"></i>
                                </button>
                            `;
                    }

                    return htmlRes;
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
            emptyTable: "Tidak ada riwayat dokumen BC 2.5", // Change this line
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        location.replace(`<?= base_url("bea-cukai-bc-25/id/"); ?>${data.id}`);
    });

    $(".mulaiTanggalBC25, .selesaiTanggalBC25").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });


    $('.mulaiTanggalBC25, .selesaiTanggalBC25,.statusPosting').change(function() {
        table.ajax.reload();
    });

    $('.noAju').keyup(function() {
        table.ajax.reload();
    });

    function deleteAction(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Dokumen BC 2.5 ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append("id", id);
                $.ajax({
                    url: `<?= base_url("bea-cukai-bc-25/delete"); ?>`,
                    method: "POST",
                    data: formData,
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            csrf.val(res.token);
                            Swal.fire({
                                icon: 'success',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                table.ajax.reload();
                            });
                        }
                    }
                })
            }
        })
    }

    function postingAction(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Dokumen BC 2.5 ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append("id", id);
                $.ajax({
                    url: `<?= base_url("bea-cukai-bc-25/posting"); ?>`,
                    method: "POST",
                    data: formData,
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                table.ajax.reload();
                            });
                        }
                    }
                })
            }
        })
    }

    function kirimCeisaAction(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting BC 4.0 ke aplikasi Ceisa Bea Cukai ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= base_url("bea-cukai-bc-25/api/kirim-dokumen/"); ?>` + id,
                    method: "GET",
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                table.ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            })
                        }
                    }
                })
            }
        })
    }

    function changeSort(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }
</script>
<?= $this->endSection(); ?>