<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Laporan Bea Cukai 4.0</h1>
        <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false" style="margin-right: -1px;">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <li><button class="dropdown-item pdf" onclick="pdfExcel('<?= base_url("/laporan-bea-cukai/laporan-4.0/print"); ?>')">PDF</button></li>
            <li><button class="dropdown-item pdf" onclick="pdfExcel('<?= base_url("/laporan-bea-cukai/laporan-4.0/excel"); ?>')">Excel</button></li>
        </ul>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("laporan-bea-cukai"); ?>">
                Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="row ">
                <div class="col-md-4">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateStart" id="dateStart" name="dateStart" aria-label="Floating label select example" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Awal Produksi</label>
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
                            <input placeholder="" class="form-control dateEnd" id="dateEnd" name="dateEnd" aria-label="Floating label select example" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Akhir Produksi</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Nama Supplier</th>
                        <th rowspan="2">Tanggal</th>
                        <th rowspan="2">No Aju</th>
                        <th rowspan="2">No Daftar</th>
                        <th rowspan="2">Tipe PO</th>
                        <th colspan="3" class="text-center">PPN</th>
                    </tr>
                    <tr>
                        <th class="text-center">Tidak Dipungut</th>
                        <th class="text-center">Di Bebaskan</th>
                        <th class="text-center">Di Tangguuhkan</th>
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
    let sort = "bc24.createdAt";
    let sortType = "desc";
    var row = 0;

    var table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [1, 'asc']
        ],
        ajax: {
            url: "<?= base_url('laporan-bea-cukai/all-4.0'); ?>",
            dataSrc: "data",
            data: function(data) {
                data.date_start = $(".dateStart").val();
                data.date_end = $(".dateEnd").val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        columns: [
            { data: "no", className: "text-center", sortable: false },
            { data: "supplier_name", className: "text-center" },
            { data: "date", className: "text-center" },
            { data: "no_aju", className: "text-center" },
            { data: "no_daftar", className: "text-center" },
            { data: "po_type", className: "text-center" },
            
           // PPN columns
            { data: "dataBCTarif.PPN.tidak_dipungut", className: "text-center", render: function(data) {
                return data > 0 ? data : '0';
            }},
            { data: "dataBCTarif.PPN.di_bebaskan", className: "text-center", render: function(data) {
                return data > 0 ? data : '0';
            }},
            { data: "dataBCTarif.PPN.di_tangguhkan", className: "text-center", render: function(data) {
                return data > 0 ? data : '0';
            }},

        ],
        rowCallback: function(row, data) {
            var rowSpan = 1; // Mengatur rowSpan
            // Mengetahui berapa banyak baris yang memiliki kode jenis pungutan yang sama
            var lastCode = $(row).prev().find('td').eq(5).text(); // Mengambil kode jenis pungutan dari baris sebelumnya
            if (lastCode == data.dataBCTarif.kode_jenis_pungutan) {
                rowSpan += $(row).prev().attr('rowspan') ? parseInt($(row).prev().attr('rowspan')) : 0;
                $(row).prev().attr('rowspan', rowSpan);
                $(row).hide();
            } else {
                $(row).find('td').eq(5).text(data.dataBCTarif.kode_jenis_pungutan); // Menampilkan kode jenis pungutan
            }
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

    $('.dateStart, .dateEnd').change(function() {
        table.ajax.reload();
    });

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    const pdfExcel = function(url) {
       
        let date_start = $(".dateStart").val();
        let date_end = $(".dateEnd").val();
        // let sort = "stock_details2.createdAt";
        // let sortType = "desc";
        window.open(url + `?date_start=${date_start}&date_end=${date_end}&sort=${sort}&sortType=${sortType}`, "_blank");
    }
</script>

<?= $this->endSection(); ?>