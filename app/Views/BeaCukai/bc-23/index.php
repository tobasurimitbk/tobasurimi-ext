<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Dokumen BC 2.3</h1>
    </div>
    <div class="card">
        <?= csrf_field() ?>
        <div class="card-body">
            <div class="row justify-content-start row-col-spp">
                <div class="col-md-3 mb-3">
                    <?= csrf_field() ?>
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker mulaiTanggalBC23" id="mulaiTanggalBC23" name="mulaiTanggalBC23" placeholder="Mulai Tanggal BC 2.3 Dibuat">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-mulaiTanggalBC23"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker selesaiTanggalBC23" id="selesaiTanggalBC23" name="selesaiTanggalBC23" placeholder="Selesai Tanggal BC 2.3 Dibuat">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-selesaiTanggalBC23"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <select name="statusBC" class="form-select statusBC" id="statusBC">
                        <option value="Belum Dibuat">STATUS BC : BELUM DIBUAT</option>
                        <option value="Belum Posting">STATUS BC : BELUM POSTING</option>
                        <option value="Sudah Posting">STATUS BC : SUDAH POSTING</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <select name="statusLPB" class="form-select statusLPB" id="statusLPB">
                        <option value="SEMUA">JENIS LPB : SEMUA</option>
                        <option value="LOKAL BAKU">JENIS LPB : LOKAL BB</option>
                        <option value="LOKAL PENOLONG">JENIS LPB : LOKAL BP</option>
                        <option value="IMPORT BAKU">JENIS LPB : IMPORT BB</option>
                        <option value="IMPORT PENOLONG">JENIS LPB : IMPORT BP</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <input autocomplete="one-time-code" class="form-control noBC23 search form-out-search" placeholder="Cari Nomor BC 2.3" value="" />
                </div>
                <div class="col-md-3 mb-3">
                    <input autocomplete="one-time-code" class="form-control noPenerimaanBarang search form-out-search" placeholder="Cari Nomor LPB" value="" />
                </div>
                <div class="col-md-3 mb-3">
                    <input autocomplete="one-time-code" class="form-control noAju search form-out-search" placeholder="Cari Nomor Aju BC 2.3" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;">No.</th>
                                <th onclick="changeSort('bc_23.bc_no_lokal')" class="sort" style="text-align: center;">No BC 2.3</th>
                                <th onclick="changeSort('bc_23.createdAt')" class="sort" style="text-align: center;">Tanggal BC 2.3</th>
                                <th onclick="changeSort('bc_23.no_aju')" class="sort" style="text-align: center;">No Aju BC 2.3</th>
                                <th style="text-align: center;">Jenis LPB</th>
                                <th onclick="changeSort('penerimaan_barang.no_penerimaan_barang')" class="sort" style="text-align: center;">No LPB</th>
                                <th onclick="changeSort('penerimaan_barang.warehouse_id')" class="sort" style="text-align: center;">Warehouse</th>
                                <th style="text-align: center;">Status BC 2.3</th>
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
    let sort = "bea_cukai.id";
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
            url: "<?= base_url("bea-cukai-bc-23/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.mulaiTanggalBC23 = $('.mulaiTanggalBC23').val();
                data.selesaiTanggalBC23 = $('.selesaiTanggalBC23').val();
                data.noBC23 = $('.noBC23').val();
                data.statusLPB = $('.statusLPB').val();
                data.statusBC = $('.statusBC').val();
                data.noPenerimaanBarang = $('.noPenerimaanBarang').val();
                data.noAju = $('.noAju').val();
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
        columns: [

            {
                data: "no",
                className: "text-center",
                sortable: false,
                width: "5%"
            },
            {
                data: "bc_no_lokal",
                className: "text-center",
            },
            {
                data: "tanggal_bc_23",
                className: "text-center",
            },
            {
                data: "no_aju",
                className: "text-center"
            },
            {
                data: "jenis_lpb",
                className: "text-center",
                searchable: false,
                sortable: false,
            },
            {
                data: "no_penerimaan_barang",
                className: "text-center"
            },
            {
                data: "warehouse_name",
                className: "text-center"
            },
            {
                data: "status",
                className: "text-center",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let htmlRes = '';

                    if (row.status == "BELUM DIBUAT") {
                        htmlRes += `
                            <div class="text-danger">
                                BELUM DIBUAT
                            </div>`
                    } else {
                        if (row.status == "BELUM POSTING") {
                            htmlRes += `
                            <div class="text-primary">
                                BELUM POSTING
                            </div>`
                        } else {
                            htmlRes += `
                            <div class="text-success">
                                SUDAH POSTING
                            </div>`
                        }
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

                    if (row.status == "BELUM DIBUAT") {
                        htmlRes += `
                            -
                        `;
                    } else {
                        if (row.status == "BELUM POSTING") {
                            htmlRes += `
                            <button onclick="postingAction('${row.id}')" class="btn btn-success posting-spp">
                                <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                            </button>
                            <button onclick="deleteAction('${row.id}')" class="btn btn-danger delete-parent">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                            `

                        } else {
                            htmlRes += `
                            <button class="btn btn-warning btn-print" onclick="alert('Hello')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>`
                        }
                    }

                    return htmlRes;

                }
            }


        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Tidak ada riwayat dokumen BC 2.3", // Change this line
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        if (data.status == 'BELUM DIBUAT') {
            location.replace(`<?= base_url("bea-cukai-bc-23/id"); ?>/${data.penerimaan_barang_id}`);
        } else {
            location.replace(`<?= base_url("bea-cukai-bc-23/id"); ?>/${data.penerimaan_barang_id}/bc-23-id/${data.id}`);
        }
    });


    $('.mulaiTanggalBC23, .selesaiTanggalBC23').change(function() {
        table.ajax.reload();
    });

    $('.noBC23, .noPenerimaanBarang, .noAju').keyup(function() {
        table.ajax.reload();
    });

    $('.statusBC, .statusLPB').change(function() {
        table.ajax.reload();
    });

    $(".mulaiTanggalBC23, .selesaiTanggalBC23").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    function changeSort(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    function deleteAction(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Dokumen BC 2.3 ?',
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
                    url: `<?= base_url("bea-cukai-bc-23/delete"); ?>`,
                    method: "POST",
                    data: formData,
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    table.ajax.reload();
                                }
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
            title: 'Posting BC 2.3 ke aplikasi Ceisa Bea Cukai ?',
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
                    url: `<?= base_url("bea-cukai-bc-23/posting"); ?>`,
                    method: "POST",
                    data: formData,
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                                if (result.isConfirmed) {
                                    table.ajax.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    table.ajax.reload();
                                }
                            });
                        }
                    }
                })
            }
        })

    }
</script>
<?= $this->endSection(); ?>