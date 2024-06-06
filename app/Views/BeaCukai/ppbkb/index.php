<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Dokumen PPBKB</h1>
        <?php if ($akunCeisa != null) : ?>
            <?php if ($akunCeisa['status_integrasi']) : ?>
                <?php if (can("Bea Cukai", "PPBKB", "c")) : ?>
                    <a href="<?= base_url('bea-cukai-ppbkb/create') ?>" type="button" class="btn btn-show-form btn-add float-right">
                        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="card">
        <?= csrf_field() ?>
        <div class="card-body">
            <div class="row justify-content-start row-col-spp">
                <div class="col-md-3 mb-3">
                    <?= csrf_field() ?>
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker mulaiTanggalPPBKB" id="mulaiTanggalPPBKB" name="mulaiTanggalPPBKB" placeholder="Mulai Tanggal PPBKB Dibuat">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-mulaiTanggalPPBKB"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker selesaiTanggalPPBKB" id="selesaiTanggalPPBKB" name="selesaiTanggalPPBKB" placeholder="Selesai Tanggal PPBKB Dibuat">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-selesaiTanggalPPBKB"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <select name="statusPosting" class="form-select statusPosting" id="statusPosting">
                        <option value="ALL">STATUS POSTING : SEMUA</option>
                        <option value="SUDAH POSTING">STATUS POSTING : SUDAH POSTING</option>
                        <option value="BELUM POSTING">STATUS POSTING : BELUM POSTING</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <input autocomplete="one-time-code" class="form-control noPPBKB search form-out-search" placeholder="Cari Nomor PPBKB / Mutasi" value="" />
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
                                <th onclick="changeSort('mutasi.divisi_asal_id')" class="sort" style="text-align: center;">Departemen Asal</th>
                                <th onclick="changeSort('mutasi.warehouse_asal_id')" class="sort" style="text-align: center;">Warehouse Asal</th>
                                <th onclick="changeSort('mutasi.divisi_tujuan_id')" class="sort" style="text-align: center;">Departemen Tujuan</th>
                                <th onclick="changeSort('mutasi.warehouse_tujuan_id')" class="sort" style="text-align: center;">Warehouse Tujuan</th>
                                <th onclick="changeSort('mutasi.no_mutasi')" class="sort" style="text-align: center;">No Mutasi</th>
                                <th onclick="changeSort('ppbkb.no_ppbkb')" class="sort" style="text-align: center;">No PPBKB</th>
                                <th onclick="changeSort('ppbkb.createdAt')" class="sort" style="text-align: center;">Tanggal PPBKB</th>
                                <th onclick="changeSort('ppbkb.status_posting')" style="text-align: center;">Status Posting</th>
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
    let sort = "ppbkb.id";
    let sortType = "desc";

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
            url: "<?= base_url("bea-cukai-ppbkb/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.mulaiTanggalPPBKB = $('.mulaiTanggalPPBKB').val();
                data.selesaiTanggalPPBKB = $('.selesaiTanggalPPBKB').val();
                data.statusPosting = $('.statusPosting').val();
                data.noPPBKB = $('.noPPBKB').val();
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
                data: "divisi_asal_name",
                className: "text-center",
            },
            {
                data: "warehouse_asal_name",
                className: "text-center"
            },
            {
                data: "divisi_tujuan_name",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "warehouse_tujuan_name",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "no_mutasi",
                className: "text-center",
            },
            {
                data: "no_ppbkb",
                className: "text-center",
            },
            {
                data: "tanggal",
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
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let htmlRes = '';

                    if (row.status_posting === "0") {
                        <?php if (can('Bea Cukai', 'PPBKB', 'd')) : ?>
                            htmlRes += `
                                <button data-toggle="tooltip" title="Hapus" onclick="deleteAction('${row.id}')" class="btn btn-danger delete-parent">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>
                            `;
                        <?php endif; ?>
                        <?php if (can('Bea Cukai', 'PPBKB', 'a')) : ?>
                            htmlRes += `
                                <button data-toggle="tooltip" title="Posting" onclick="postingAction('${row.id}')" class="btn btn-success posting-spp">
                                    <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                                </button>
                            `;
                        <?php endif; ?>
                        <?php if (can('Bea Cukai', 'PPBKB', 'p')) : ?>
                            htmlRes += `
                                 <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="printAction('${row.id}')" style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </button>
                            `;
                        <?php endif; ?>
                    } else {
                        <?php if (can('Bea Cukai', 'PPBKB', 'p')) : ?>
                            htmlRes += `
                                 <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="printAction('${row.id}')" style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </button>
                            `;
                        <?php endif; ?>
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
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        location.replace(`<?= base_url("bea-cukai-ppbkb/id/"); ?>${data.id}`);
    });

    $('.mulaiTanggalPPBKB, .selesaiTanggalPPBKB,.statusPosting').change(function() {
        table.ajax.reload();
    });

    $('.noPPBKB').keyup(function() {
        table.ajax.reload();
    });

    $(".mulaiTanggalPPBKB, .selesaiTanggalPPBKB").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    function deleteAction(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Dokumen PPBKB ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append("id", id);
                $.ajax({
                    url: `<?= base_url("bea-cukai-ppbkb/delete"); ?>`,
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
            title: 'Posting Dokumen PPBKB ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append("id", id);
                $.ajax({
                    url: `<?= base_url("bea-cukai-ppbkb/posting"); ?>`,
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

    function printAction(id) {
        window.open("<?= base_url('bea-cukai-ppbkb/print/') ?>" + id, '_blank');
    }
</script>
<?= $this->endSection(); ?>