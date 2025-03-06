<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Laporan Pungutan Bea Cukai 2.7</h1>
        <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false" style="margin-right: -1px;">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <li><button class="dropdown-item pdf" onclick="pdfExcel('<?= base_url("/laporan-bea-cukai/laporan-2.7/print"); ?>')">PDF</button></li>
            <li><button class="dropdown-item pdf" onclick="pdfExcel('<?= base_url("/laporan-bea-cukai/laporan-2.7/excel"); ?>')">Excel</button></li>
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
                        <input autocomplete="one-time-code" class="form-control input-picker mulaiTanggalBC27" id="mulaiTanggalBC27" name="mulaiTanggalBC27" placeholder="Mulai Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -27px; border: 0px" class="fa fa-calendar icon-form icon-mulaiTanggalBC27"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker selesaiTanggalBC27" id="selesaiTanggalBC27" name="selesaiTanggalBC27" placeholder="Selesai Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -27px; border: 0px" class="fa fa-calendar icon-form icon-selesaiTanggalBC27"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <input autocomplete="one-time-code" class="form-control noAju search form-out-search" placeholder="Cari No Aju / No Daftar" value="" />
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table id="dataTable" class="table table-bordered nowrap table-hover dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Tanggal</th>
                                <th rowspan="2">No Aju</th>
                                <th rowspan="2">No Daftar</th>

                                <!-- Kolom untuk PPN -->
                                <th colspan="7" class="text-center">PPN</th>
                                <!-- Kolom untuk PPH -->
                                <th colspan="7" class="text-center">PPH</th>
                                <!-- Kolom untuk BM -->
                                <th colspan="7" class="text-center">BM</th>
                            </tr>
                            <tr>
                                <!-- PPN -->
                                <th class="text-center">Di Bebaskan</th>
                                <th class="text-center">Di Bayar</th>
                                <th class="text-center">Di Lunasi</th>
                                <th class="text-center">Di Tanggung Pemerintah</th>
                                <th class="text-center">Tidak Dipungut</th>
                                <th class="text-center">Di Tunda</th>
                                <th class="text-center">Di Tangguhkan</th>

                                <!-- PPH -->
                                <th class="text-center">Di Bebaskan</th>
                                <th class="text-center">Di Bayar</th>
                                <th class="text-center">Di Lunasi</th>
                                <th class="text-center">Di Tanggung Pemerintah</th>
                                <th class="text-center">Tidak Dipungut</th>
                                <th class="text-center">Di Tunda</th>
                                <th class="text-center">Di Tangguhkan</th>

                                <!-- BM -->
                                <th class="text-center">Di Bebaskan</th>
                                <th class="text-center">Di Bayar</th>
                                <th class="text-center">Di Lunasi</th>
                                <th class="text-center">Di Tanggung Pemerintah</th>
                                <th class="text-center">Tidak Dipungut</th>
                                <th class="text-center">Di Tunda</th>
                                <th class="text-center">Di Tangguhkan</th>
                            </tr>
                        </thead>

                        <tbody class="body-table" id="body-table" style="cursor: pointer;">
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "bc27.id";
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
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url('laporan-bea-cukai/all-2.7'); ?>",
            dataSrc: "data",
            data: function(data) {
                data.mulaiTanggalBC27 = $('.mulaiTanggalBC27').val();
                data.selesaiTanggalBC27 = $('.selesaiTanggalBC27').val();
                data.statusPosting = $('.statusPosting').val();
                data.noAju = $('.noAju').val();
                data.tipeSalesOrder = $('.tipeSalesOrder').val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        "columns": [{
                data: "no",
                className: "text-center"
            },
            {
                data: "tanggal_bc_27",
                className: "text-center"
            },
            {
                data: "no_aju",
                className: "text-center"
            },
            {
                data: "no_daftar",
                className: "text-center"
            },

            // Kolom PPN
            {
                data: "pungutan.PPN.di_bebaskan",
                className: "text-center",
                render: formatRupiah
            },
            {
                data: "pungutan.PPN.di_bayar",
                className: "text-center",
                render: formatRupiah
            },
            {
                data: "pungutan.PPN.di_lunasi",
                className: "text-center",
                render: formatRupiah
            },
            {
                data: "pungutan.PPN.di_tanggung_pemerintah",
                className: "text-center",
                render: formatRupiah
            },
            {
                data: "pungutan.PPN.tidak_dipungut",
                className: "text-center",
                render: formatRupiah
            },
            {
                data: "pungutan.PPN.di_tunda",
                className: "text-center",
                render: formatRupiah
            },
            {
                data: "pungutan.PPN.di_tangguhkan",
                className: "text-center",
                render: formatRupiah
            },

            // Kolom PPH
            {
                data: "pungutan.PPH.di_bebaskan",
                className: "text-center",
                render: formatRupiah
            },
            {
                data: "pungutan.PPH.di_bayar",
                className: "text-center",
                render: formatRupiah
            },
            {
                data: "pungutan.PPH.di_lunasi",
                className: "text-center",
                render: formatRupiah
            },
            {
                data: "pungutan.PPH.di_tanggung_pemerintah",
                className: "text-center",
                render: formatRupiah
            },
            {
                data: "pungutan.PPH.tidak_dipungut",
                className: "text-center",
                render: formatRupiah
            },
            {
                data: "pungutan.PPH.di_tunda",
                className: "text-center",
                render: formatRupiah
            },
            {
                data: "pungutan.PPH.di_tangguhkan",
                className: "text-center",
                render: formatRupiah
            },

            // Kolom BM
            {
                data: "pungutan.BM.di_bebaskan",
                className: "text-center",
                render: formatRupiah
            },
            {
                data: "pungutan.BM.di_bayar",
                className: "text-center",
                render: formatRupiah
            },
            {
                data: "pungutan.BM.di_lunasi",
                className: "text-center",
                render: formatRupiah
            },
            {
                data: "pungutan.BM.di_tanggung_pemerintah",
                className: "text-center",
                render: formatRupiah
            },
            {
                data: "pungutan.BM.tidak_dipungut",
                className: "text-center",
                render: formatRupiah
            },
            {
                data: "pungutan.BM.di_tunda",
                className: "text-center",
                render: formatRupiah
            },
            {
                data: "pungutan.BM.di_tangguhkan",
                className: "text-center",
                render: formatRupiah
            }
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

    function formatRupiah(value) {
        if (value == null || value == 0) {
            return 'Rp 0';
        }
        return 'Rp ' + parseInt(value).toLocaleString('id-ID');
    }

    $('.mulaiTanggalBC27, .selesaiTanggalBC27, .tipeSalesOrder').change(function() {
        table.ajax.reload();
    });

    $(".mulaiTanggalBC27, .selesaiTanggalBC27").datepicker({
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