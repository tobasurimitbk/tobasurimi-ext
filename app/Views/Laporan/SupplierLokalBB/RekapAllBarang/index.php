<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Rekap All Barang (Summary)</h1>
        <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <!-- <li><button class="dropdown-item pdf" onclick="exportData('<?= base_url("/laporan-supplier-lokal-bb/rekap-all-barang/print"); ?>')">PDF</button></li> -->
            <li><button class="dropdown-item excel" onclick="exportData('<?= base_url("/laporan-supplier-lokal-bb/rekap-all-barang/export-excel"); ?>')">Excel</button></li>
        </ul>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("laporan-supplier-lokal-bb"); ?>">
                Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-md-3">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateStart" id="dateStart" name="dateStart" aria-label="Floating label select example" value="01/<?= date("m/Y") ?>" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Awal</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateEnd" id="dateEnd" name="dateEnd" aria-label="Floating label select example" value="<?= date('t/m/Y') ?>" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Akhir</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_divisi" name="filter_divisi" id="filter_divisi">
                            <option value="" data-code=""></option>

                            <?php foreach ($getDivisi as $row) : ?>
                                <option value="<?= $row['id']; ?>" data-code=""><?= $row['divisi'] ?></option>
                            <?php endforeach; ?>


                        </select>
                        <label for="floatingInput">Filter Departemen</label>
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_barang" name="filter_barang" id="filter_barang">
                            <option value="" data-code=""></option>

                            <?php foreach ($getBarang as $row) : ?>
                                <option value="<?= $row['id']; ?>" data-code=""><?= strtoupper($row["barang_name"]); ?></option>
                            <?php endforeach; ?>


                        </select>
                        <label for="floatingInput">Filter Barang</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th rowspan="2">No</th>
                                <th onclick="changeSort('barangName')" class="sort" rowspan="2">Barang</th>
                                <th onclick="changeSort('spekName')" class="sort" rowspan="2">Spesifikasi</th>
                                <th onclick="changeSort('bagianName')" class="sort" rowspan="2">Departemen</th>
                                <th rowspan="2">Qty</th>
                                <th rowspan="2">Satuan</th>
                                <th colspan="3">Umum</th>
                                <th colspan="3">Tambahan Harian</th>
                                <th colspan="3">Tambahan Bulanan</th>
                                <th colspan="3">Tambahan Langsung</th>
                                <th onclick="changeSort('bagianName')" class="sort" rowspan="2">Total</th>
                            </tr>
                            <tr>
                                <!-- Umum -->
                                <th>DPP</th>
                                <th>PPh</th>
                                <th>Dibayarkan</th>
                                <!-- Harian -->
                                <th>DPP</th>
                                <th>PPh</th>
                                <th>Dibayarkan</th>
                                <!-- Bulanan -->
                                <th>DPP</th>
                                <th>PPh</th>
                                <th>Dibayarkan</th>
                                <!-- Langsung -->
                                <th>DPP</th>
                                <th>PPh</th>
                                <th>Dibayarkan</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table">
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-center">Total</th>
                                <th class="text-center total-qty"></th>
                                <th></th>
                                <th class="text-center total-dpp-umum"></th>
                                <th class="text-center total-pph-umum"></th>
                                <th class="text-center total-total-umum"></th>
                                <th class="text-center total-dpp-harian"></th>
                                <th class="text-center total-pph-harian"></th>
                                <th class="text-center total-total-harian"></th>
                                <th class="text-center total-dpp-bulanan"></th>
                                <th class="text-center total-pph-bulanan"></th>
                                <th class="text-center total-total-bulanan"></th>
                                <th class="text-center total-dpp-subsidi"></th>
                                <th class="text-center total-pph-subsidi"></th>
                                <th class="text-center total-total-subsidi"></th>
                                <th class="text-center total-total-row"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "barangName";
    let sortType = "desc";
    var row = 0;

    var table = $('.dataTable').DataTable({

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
            url: "<?= base_url("/laporan-supplier-lokal-bb/rekap-all-barang/all-rekap-all-barang"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.sort = sort;
                data.sortType = sortType;

                data.filter_divisi = $(".filter_divisi").val();
                data.filter_barang = $(".filter_barang").val();
            },
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
            },
            {
                data: "barangName",
                className: "text-center",

            },

            {
                data: "spekName",
                className: "text-center",
            },

            {
                data: "divisiName",
                className: "text-center",
            },

            {
                data: "qtyPO",
                className: "text-center",
            },

            {
                data: "satuanName",
                className: "text-center",
            },

            {
                data: "dppUmum",
                className: "text-center",
            },
            {
                data: "pphUmum",
                className: "text-center",
            },
            {
                data: "totalUmum",
                className: "text-center",
            },
            {
                data: "dppHarian",
                className: "text-center",
            },
            {
                data: "pphHarian",
                className: "text-center",
            },
            {
                data: "totalHarian",
                className: "text-center",
            },
            {
                data: "dppBulanan",
                className: "text-center",
            },
            {
                data: "pphBulanan",
                className: "text-center",
            },
            {
                data: "totalBulanan",
                className: "text-center",
            },

            {
                data: "subsidi",
                className: "text-center",
            },
            {
                data: "pphSubsidi",
                className: "text-center",
            },
            {
                data: "totalSubsidi",
                className: "text-center",
            },
            {
                data: "totalRow",
                className: "text-center",
            },
        ],
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
        },
        footerCallback: function(row, data, start, end, display) {
            var api = this.api();

            function parseNumber(value) {
                return typeof value === 'string' ?
                    parseFloat(value.replace(/,/g, '')) || 0 :
                    typeof value === 'number' ?
                    value :
                    0;
            }

            function totalCol(index) {
                return api
                    .column(index, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return parseNumber(a) + parseNumber(b);
                    }, 0);
            }

            // urutan index kolom (sesuai columns array):
            const indexes = {
                qty: 4,
                dppUmum: 6,
                pphUmum: 7,
                totalUmum: 8,
                dppHarian: 9,
                pphHarian: 10,
                totalHarian: 11,
                dppBulanan: 12,
                pphBulanan: 13,
                totalBulanan: 14,
                dppSubsidi: 15,
                pphSubsidi: 16,
                totalSubsidi: 17,
                totalRow: 18,
            };

            $('.total-qty').html(totalCol(indexes.qty).toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));
            $('.total-dpp-umum').html(totalCol(indexes.dppUmum).toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));
            $('.total-pph-umum').html(totalCol(indexes.pphUmum).toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));
            $('.total-total-umum').html(totalCol(indexes.totalUmum).toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));

            $('.total-dpp-harian').html(totalCol(indexes.dppHarian).toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));
            $('.total-pph-harian').html(totalCol(indexes.pphHarian).toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));
            $('.total-total-harian').html(totalCol(indexes.totalHarian).toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));

            $('.total-dpp-bulanan').html(totalCol(indexes.dppBulanan).toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));
            $('.total-pph-bulanan').html(totalCol(indexes.pphBulanan).toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));
            $('.total-total-bulanan').html(totalCol(indexes.totalBulanan).toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));

            $('.total-dpp-subsidi').html(totalCol(indexes.dppSubsidi).toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));
            $('.total-pph-subsidi').html(totalCol(indexes.pphSubsidi).toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));
            $('.total-total-subsidi').html(totalCol(indexes.totalSubsidi).toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));

            $('.total-total-row').html(totalCol(indexes.totalRow).toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));
        },
    })

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

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

    $(".search").keyup(function() {
        table.ajax.reload();
    })

    $(".dateStart, .dateEnd, .filter_divisi ,.filter_barang").change(function() {
        table.ajax.reload();
    });

    $('.filter_divisi').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true,
    })

    $('.filter_barang').select2({
        placeholder: "Pilih Barang",
        theme: "bootstrap-5",
        allowClear: true,
    })


    $(' .filter_divisi , .filter_barang')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $(' .filter_divisi , .filter_barang')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $(' .filter_divisi , .filter_barang')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    const exportData = function(url) {

        let dateStart = $(".dateStart").val();
        let dateEnd = $(".dateEnd").val();

        let filter_divisi = $(".filter_divisi").val();
        let filter_barang = $(".filter_barang").val();

        window.open(url + `?filter_divisi=${filter_divisi}&filter_barang=${filter_barang}&dateStart=${dateStart}&dateEnd=${dateEnd}&sort=${sort}&sortType=${sortType}`, "_blank");
    }
</script>

<?= $this->endSection(); ?>