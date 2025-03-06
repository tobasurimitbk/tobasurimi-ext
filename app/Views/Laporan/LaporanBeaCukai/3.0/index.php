<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Laporan Pungutan Bea Cukai 3.0</h1>
        <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false" style="margin-right: -1px;">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <li><button class="dropdown-item pdf" onclick="pdfExcel('<?= base_url("/laporan-bea-cukai/laporan-3.0/print"); ?>')">PDF</button></li>
            <li><button class="dropdown-item pdf" onclick="pdfExcel('<?= base_url("/laporan-bea-cukai/laporan-3.0/excel"); ?>')">Excel</button></li>
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
                <div class="col-md-3 mb-3">
                    <?= csrf_field() ?>
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker mulaiTanggalBC30" id="mulaiTanggalBC30" name="mulaiTanggalBC30" placeholder="Mulai Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-mulaiTanggalBC30"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker selesaiTanggalBC30" id="selesaiTanggalBC30" name="selesaiTanggalBC30" placeholder="Selesai Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-selesaiTanggalBC30"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <select name="tipeSalesOrder" class="form-select tipeSalesOrder" id="tipeSalesOrder">
                        <option value="ALL" selected>TIPE PENGELUARAN : SEMUA</option>
                        <option value="LOKAL">LOKAL</option>
                        <option value="INTERNASIONAL">INTERNASIONAL</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <input autocomplete="one-time-code" class="form-control noAju search form-out-search" placeholder="Cari Nomor Aju / No Daftar" value="" />
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2" onclick="changeSort('bc_40.createdAt')" class="sort" style="text-align: center;">Tanggal</th>
                                <th rowspan="2" onclick="changeSort('bc_40.no_aju')" class="sort" style="text-align: center;">No Aju</th>
                                <th rowspan="2" onclick="changeSort('bc_40.no_daftar')" class="sort" style="text-align: center;">No Daftar</th>
                                <th rowspan="2" onclick="changeSort('stuffing_lokal.no_stuffing')" class="sort" style="text-align: center;">No Stuffing</th>

                                <th colspan="4" class="text-center">PPN</th>
                                <th colspan="4" class="text-center">PPH</th>
                                <th colspan="4" class="text-center">BM</th>
                            </tr>
                            <tr>
                                <th class="text-center">Di Bebaskan</th>
                                <th class="text-center">Di Bayar</th>
                                <th class="text-center">Di Lunasi</th>
                                <th class="text-center">Di Tanggung Pemerintah</th>

                                <th class="text-center">Di Bebaskan</th>
                                <th class="text-center">Di Bayar</th>
                                <th class="text-center">Di Lunasi</th>
                                <th class="text-center">Di Tanggung Pemerintah</th>

                                <th class="text-center">Di Bebaskan</th>
                                <th class="text-center">Di Bayar</th>
                                <th class="text-center">Di Lunasi</th>
                                <th class="text-center">Di Tanggung Pemerintah</th>
                            </tr>
                        </thead>

                        <tbody class="body-table" id="body-table">
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "bc30.id";
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
            url: "<?= base_url('laporan-bea-cukai/all-3.0'); ?>",
            dataSrc: "data",
            data: function(data) {
                data.mulaiTanggalBC30 = $('.mulaiTanggalBC30').val();
                data.selesaiTanggalBC30 = $('.selesaiTanggalBC30').val();
                data.statusPosting = $('.statusPosting').val();
                data.noAju = $('.noAju').val();
                data.tipeSalesOrder = $('.tipeSalesOrder').val();
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
                data: "tanggal_bc_30",
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
            {
                data: "no_stuffing",
                className: "text-center"
            },

            {
                data: "dataBCTarif.PPN.di_bebaskan",
                className: "text-center",
                render: function(data) {
                    return formatRupiah(data > 0 ? data : '0');
                }
            },
            {
                data: "dataBCTarif.PPN.di_bayar",
                className: "text-center",
                render: function(data) {
                    return formatRupiah(data > 0 ? data : '0');
                }
            },
            {
                data: "dataBCTarif.PPN.di_lunasi",
                className: "text-center",
                render: function(data) {
                    return formatRupiah(data > 0 ? data : '0');
                }
            },
            {
                data: "dataBCTarif.PPN.di_tanggung_pemerintah",
                className: "text-center",
                render: function(data) {
                    return formatRupiah(data > 0 ? data : '0');
                }
            },

            {
                data: "dataBCTarif.PPH.di_bebaskan",
                className: "text-center",
                render: function(data) {
                    return formatRupiah(data > 0 ? data : '0');
                }
            },
            {
                data: "dataBCTarif.PPH.di_bayar",
                className: "text-center",
                render: function(data) {
                    return formatRupiah(data > 0 ? data : '0');
                }
            },
            {
                data: "dataBCTarif.PPH.di_lunasi",
                className: "text-center",
                render: function(data) {
                    return formatRupiah(data > 0 ? data : '0');
                }
            },
            {
                data: "dataBCTarif.PPH.di_tanggung_pemerintah",
                className: "text-center",
                render: function(data) {
                    return formatRupiah(data > 0 ? data : '0');
                }
            },

            {
                data: "dataBCTarif.BM.di_bebaskan",
                className: "text-center",
                render: function(data) {
                    return formatRupiah(data > 0 ? data : '0');
                }
            },
            {
                data: "dataBCTarif.BM.di_bayar",
                className: "text-center",
                render: function(data) {
                    return formatRupiah(data > 0 ? data : '0');
                }
            },
            {
                data: "dataBCTarif.BM.di_lunasi",
                className: "text-center",
                render: function(data) {
                    return formatRupiah(data > 0 ? data : '0');
                }
            },
            {
                data: "dataBCTarif.BM.di_tanggung_pemerintah",
                className: "text-center",
                render: function(data) {
                    return formatRupiah(data > 0 ? data : '0');
                }
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


    $('.mulaiTanggalBC30, .selesaiTanggalBC30, .tipeSalesOrder').change(function() {
        table.ajax.reload();
    });

    $(".mulaiTanggalBC30, .selesaiTanggalBC30").datepicker({
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