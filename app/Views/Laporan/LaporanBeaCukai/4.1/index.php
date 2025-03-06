<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Laporan Pungutan Bea Cukai 4.1</h1>
        <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false" style="margin-right: -1px;">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <li><button class="dropdown-item pdf" onclick="pdfExcel('<?= base_url("/laporan-bea-cukai/laporan-4.1/print"); ?>')">PDF</button></li>
            <li><button class="dropdown-item pdf" onclick="pdfExcel('<?= base_url("/laporan-bea-cukai/laporan-4.1/excel"); ?>')">Excel</button></li>
        </ul>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("laporan-bea-cukai"); ?>">
                Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="row justify-content-start row-col-spp">
                <div class="col-md-4 mb-3">
                    <?= csrf_field() ?>
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker mulaiTanggalBC25" id="mulaiTanggalBC25" name="mulaiTanggalBC25" placeholder="Mulai Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-mulaiTanggalBC25"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker selesaiTanggalBC25" id="selesaiTanggalBC25" name="selesaiTanggalBC25" placeholder="Selesai Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-selesaiTanggalBC25"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <input autocomplete="one-time-code" class="form-control noAju search form-out-search" placeholder="Cari No Aju / No Daftar" value="" />
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('bc_41.createdAt')" class="sort" style="text-align: center;">Tanggal</th>
                                <th onclick="changeSort('bc_41.no_aju')" class="sort" style="text-align: center;">No Aju</th>
                                <th onclick="changeSort('bc_41.daftar')" class="sort" style="text-align: center;">No Daftar</th>
                                <th style="text-align: center;">No Bukti Bayar</th>
                                <th style="text-align: center;">Tanggal Bukti Bayar</th>
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
    let sort = "bc41.id";
    let sortType = "desc";

    var table = $('.dataTable').DataTable({

        serverSide: true,
        ordering: true,
        searching: false, // Menghilangkan fitur pencarian
        order: [
            [1, 'asc'] // Urutan default berdasarkan kolom kedua
        ],
        fixedHeader: true,
        lengthMenu: [
            [41],
            [41],
        ],
        pageLength: 41,
        ajax: {
            url: "<?= base_url('laporan-bea-cukai/all-4.1'); ?>",
            dataSrc: "data",
            data: function(data) {
                data.mulaiTanggalBC41 = $('.mulaiTanggalBC41').val();
                data.selesaiTanggalBC41 = $('.selesaiTanggalBC41').val();
                data.noAju = $('.noAju').val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        columns: [{
                data: null,
                className: "text-center",
                sortable: false
            }, // Nomor urut
            {
                data: "date",
                className: "text-center",
            },
            {
                data: "no_aju",
                className: "text-center",
            },
            {
                data: "no_daftar",
                className: "text-center"
            },
            {
                data: "no_bayar",
                className: "text-center"
            },
            {
                data: "tanggal_bayar",
                className: "text-center"
            },
        ],
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        rowCallback: function(row, data, displayNum, displayIndex, dataIndex) {
            var pageInfo = table.page.info();
            var no = pageInfo.start + displayIndex + 1; // Menentukan nomor urut berdasarkan halaman dan posisi data
            $('td:eq(0)', row).html(no); // Menampilkan nomor urut di kolom pertama
        },
        drawCallback: function(settings) {
            var api = this.api();
            var pageInfo = api.page.info();
            api.column(0, {
                page: 'current'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = pageInfo.start + i + 1;
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


    $('.mulaiTanggalBC41, .selesaiTanggalBC41').change(function() {
        table.ajax.reload();
    });

    $('.searchData').keyup(function() {
        table.ajax.reload();
    });

    $(".mulaiTanggalBC41, .selesaiTanggalBC41").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
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