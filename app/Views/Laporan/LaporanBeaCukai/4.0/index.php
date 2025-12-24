<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Laporan Pungutan Bea Cukai 4.0</h1>
        <?php if (can('Laporan', 'Bea Cukai', 'p')): ?>
            <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false" style="margin-right: -1px;">
                Export
            </button>
            <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                <li><button class="dropdown-item pdf" onclick="pdfExcel('<?= base_url("/laporan-bea-cukai/pungutan-bc-40/export-pdf"); ?>')">PDF</button></li>
                <li><button class="dropdown-item pdf" onclick="pdfExcel('<?= base_url("/laporan-bea-cukai/pungutan-bc-40/export-excel"); ?>')">Excel</button></li>
            </ul>
        <?php endif; ?>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("laporan-bea-cukai"); ?>">
                Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="row">
                <div class="col-md-2">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input value="01/<?= date('m/Y') ?>" placeholder="" class="form-control mulaiTanggalBC40" id="mulaiTanggalBC40" name="mulaiTanggalBC40" />
                            <label style="z-index: 1;" style="z-index: 1;">Tgl Awal Dokumen</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input value="<?= date('d/m/Y') ?>" placeholder="" class="form-control selesaiTanggalBC40" id="selesaiTanggalBC40" name="selesaiTanggalBC40" />
                            <label style="z-index: 1;" style="z-index: 1;">Tgl Akhir Dokumen</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input type="text" name="search" id="search" class="form-control search" placeholder="Cari Data">
                            <label style="z-index: 1;">Cari Data</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-lg" style="height: 45px; background-color:#B8522A; color:whitesmoke;" onclick="handleFilter()">
                        <span style="font-size: 15px;">
                            <i class="fas fa-search"></i> Filter
                        </span>
                    </button>

                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2" onclick="changeSort('bc_purchase_order.supplier_id')">Nama Supplier</th>
                                <th rowspan="2" onclick="changeSort('bc_40.createdAt')">Tanggal</th>
                                <th rowspan="2" onclick="changeSort('bc_40.no_aju')">No Aju</th>
                                <th rowspan="2" onclick="changeSort('bc_purchase_order.no_daftar')">No Daftar</th>
                                <th rowspan="2" onclick="changeSort('bc_purchase_order.po_type')">Tipe PO</th>

                                <th colspan="2" class="text-left">PPN</th>
                            </tr>
                            <tr>
                                <th class="text-left">Nilai</th>
                                <th class="text-left">Status</th>
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
    let sort = "bc40.id";
    let sortType = "desc";

    var table = $('.dataTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        fixedHeader: true,
        searching: false,
        order: [
            [1, 'asc']
        ],
        lengthMenu: [
            [25],
            [25]
        ],
        pageLength: 25,

        ajax: {
            url: "<?= base_url('laporan-bea-cukai/all-pungutan-bc-40'); ?>",
            dataSrc: "data",
            data: function(data) {
                data.mulaiTanggalBC40 = $('.mulaiTanggalBC40').val();
                data.selesaiTanggalBC40 = $('.selesaiTanggalBC40').val();
                data.search = $('.search').val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },

        columns: [{
                data: "no",
                className: "text-left",
                orderable: false
            },
            {
                data: "supplier_name",
                className: "text-left"
            },
            {
                data: "date",
                className: "text-left"
            },
            {
                data: "no_aju",
                className: "text-left"
            },
            {
                data: "no_daftar",
                className: "text-left"
            },
            {
                data: "po_type",
                className: "text-left"
            },
            {
                data: "ppn_nilai",
                className: "text-right",
                render: function(data) {
                    return data > 0 ? greatFormatRupiah(parseFloat(data).toFixed(2)) : '';
                }
            },
            {
                data: "ppn_status",
                className: "text-left",
                render: function(data) {
                    return data;
                }
            }
        ],

        initComplete: function() {
            $('.dataTables_length').empty();
            $('.dataTables_length').html(
                "<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>"
            );
            $('.dataTable').wrap("<div style='overflow:auto; width:100%; position:relative;'></div>");
        },

        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],

        language: {
            emptyTable: "Tidak Ada Data",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });


    $(".mulaiTanggalBC40, .selesaiTanggalBC40").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    function handleFilter() {
        table.ajax.reload();
    }

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    const pdfExcel = function(url) {
        let date_start = $(".mulaiTanggalBC40").val();
        let date_end = $(".selesaiTanggalBC40").val();
        window.open(url + `?mulaiTanggalBC40=${date_start}&selesaiTanggalBC40=${date_end}&sort=${sort}&sortType=${sortType}`, "_blank");
    }
</script>

<?= $this->endSection(); ?>