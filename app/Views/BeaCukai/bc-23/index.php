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
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Mulai Tanggal LPB">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Selesai Tanggal LPB">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <select name="status_bc" class="form-select status_bc" id="status_bc">
                        <option value="WAITING">STATUS BC : WAITING</option>
                        <option value="FINISH">STATUS BC : FINISH</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <select name="status_bc" class="form-select status_bc" id="jenis_po">
                        <option value="SEMUA">JENIS PO : SEMUA</option>
                        <option value="LOKAL BB">JENIS PO : LOKAL BB</option>
                        <option value="LOKAL BP">JENIS PO : LOKAL BP</option>
                        <option value="IMPORT BB">JENIS PO : IMPORT BB</option>
                        <option value="IMPORT BP">JENIS PO : IMPORT BP</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <input autocomplete="one-time-code" class="form-control no_registrasi search form-out-search" placeholder="Cari Nomor Registrasi" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;">No.</th>
                                <th style="text-align: center;">Jenis PO</th>
                                <th onclick="" class="sort" style="text-align: center;">No LPB</th>
                                <th onclick="" style="text-align: center;">No PO</th>
                                <th onclick="" class="sort" style="text-align: center;">Warehouse</th>
                                <th onclick="" class="sort" style="text-align: center;">Tanggal LPB</th>
                                <th onclick="" class="sort" style="text-align: center;">No Aju</th>
                                <th onclick="" class="sort" style="text-align: center;">No Daftar</th>
                                <th onclick="" class="sort" style="text-align: center;">Tanggal Daftar</th>
                                <th onclick="" class="sort" style="text-align: center;">Status BC</th>
                                <th style="text-align: center;">Actions</th>
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
                data.dateStart = $('.dateStart').val();
                data.dateFinish = $('.dateEnd').val();
                data.noRegistrasi = $('.no_registrasi').val();
                data.status = $('.status_bc').val();
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
                data: "jenis_po",
                className: "text-center",
                sortable: false,
                width: "10%"
            },
            {
                data: "lpb_no",
                className: "text-center"
            },
            {
                data: "po_no",
                className: "text-center",
                sortable: false,
                width: "10%"
            },
            {
                data: "warehouse_name",
                className: "text-center"
            },
            {
                data: "lpb_date",
                className: "text-center"
            },
            {
                data: "aju_no",
                className: "text-center"
            },
            {
                data: "no_registration",
                className: "text-center"
            },
            {
                data: "validation_date",
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
                            <div class="text-warning">
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
                            <button  class="btn btn-success posting-spp">
                                <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                            </button>`
                        } else {
                            htmlRes += `
                            <button class="btn btn-warning btn-print" onclick="print('<?= base_url("po-lokal-bahan-penolong/print/"); ?>${id}')" style="box-shadow: none !important;">
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