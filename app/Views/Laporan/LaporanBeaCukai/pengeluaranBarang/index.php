<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Laporan Pengeluaran Barang</h1>
        <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false" style="margin-right: -1px;">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <li><button class="dropdown-item pdf" onclick="pdfExcel('<?= base_url("/laporan-bea-cukai/laporan-pengeluaran-barang/print"); ?>')">PDF</button></li>
            <li><button class="dropdown-item pdf" onclick="pdfExcel('<?= base_url("/laporan-bea-cukai/laporan-pengeluaran-barang/excel"); ?>')">Excel</button></li>
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
                <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <select class="form-select tipe_barang" name="tipe_barang" id="tipe_barang">
                            <option disabled selected value=""></option>
                            <?php foreach ($tipeBarang as $t) : ?>
                                <option value="<?= $t['description'] ?>">
                                    <?= strtoupper($t['value']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Pilih Tipe Barang</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateStart" id="dateStart" name="dateStart" aria-label="Floating label select example" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Awal Dokumen</label>
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
                            <input placeholder="" class="form-control dateEnd" id="dateEnd" name="dateEnd" aria-label="Floating label select example" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Akhir Dokumen</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <select class="form-select supplier_id" name="supplier_id" id="supplier_id">
                            <option disabled selected value=""></option>
                            <?php foreach ($dataSupplier as $s) : ?>
                                <option value="<?= $s['id'] ?>">
                                    <?= $s['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Pilih Supplier</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <select class="form-select bc_id" name="bc_id" id="bc_id">
                            <option disabled selected value=""></option>
                            <option value="0">NON PABEAN</option>
                            <?php foreach ($dataDokumen as $d) : ?>
                                <option value="<?= $d['id'] ?>">
                                    <?= $d['value']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Pilih Dokumen Pabean</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <select class="form-select sumber" name="sumber" id="sumber">
                            <option disabled selected value=""></option>
                            <?php foreach ($dataPemasukan as $p) : ?>
                                <option value="<?= $p ?>">
                                    <?= $p == "LPB" ? "PEMBELIAN" : $p; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Pilih Sumber Pemasuukan</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <select class="form-select divisi_id" name="divisi_id" id="divisi_id" onchange="getListWarehouse()">
                            <option disabled selected value=""></option>
                            <?php foreach ($dataDivisi as $d) : ?>
                                <option value="<?= $d['id'] ?>">
                                    <?= $d['divisi']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Pilih Departemen</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <select class="form-select warehouse_id" name="warehouse_id" id="warehouse_id">
                            <option disabled selected value=""></option>
                        </select>
                        <label style="z-index: 1;">Pilih Warehouse</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <input type="text" name="nama_barang" id="nama_barang" class="form-control nama_barang" placeholder="Kode / Nama Barang">
                        <label style="z-index: 1;">Kode / Nama Barang</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <input type="text" name="no_aju" id="no_aju" class="form-control no_aju" placeholder="No Pengajuan">
                        <label style="z-index: 1;">No Pengajuan</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <input type="text" name="no_daftar" id="no_daftar" class="form-control no_daftar" placeholder="No Daftar">
                        <label style="z-index: 1;">No Daftar</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('stock.tipe_barang')" class="sort">Tipe Barang</th>
                                <th onclick="changeSort('stock_details2.bc_id')" class="sort">Jenis Dokumen</th>
                                <th onclick="changeSort('stock_details2.no_aju')" class="sort">Nomor Aju</th>
                                <th>No Daftar</th>
                                <th>Tgl Daftar</th>
                                <th>No Stuffing / No Pengeluaran</th>
                                <th>Tgl Pengeluaran</th>
                                <th>Surat Jalan</th>
                                <th onclick="changeSort('stock_details.sumber')" class="sort">Jenis Order</th>
                                <th onclick="changeSort('stock_details2.no_po')" class="sort">No Order</th>
                                <th>No Invoice</th>
                                <th onclick="changeSort('stock.divisi_id')" class="sort">Departemen</th>
                                <th onclick="changeSort('stock.warehouse_id')" class="sort">Warehouse</th>
                                <th>Penerima / Customer</th>
                                <th>Kode Barang</th>
                                <th>Barang</th>
                                <th>Spesifikasi</th>
                                <th>Jumlah Barang</th>
                                <th>Satuan</th>
                                <th>Valas</th>
                                <th>Harga Barang / Jasa</th>
                                <th>Nilai Penyerahan</th>
                                <th>Jumlah Penerimaan</th>
                                <th>Selisih</th>
                                <th onclick="changeSort('stock_details.keterangan')" class="sort">Keterangan</th>
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
    let sort = "stock_details2.createdAt";
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
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("laporan-bea-cukai/all-keluar"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.status = "Out";
                data.tipe_barang = $(".tipe_barang").val();
                data.date_start = $(".dateStart").val();
                data.date_end = $(".dateEnd").val();
                data.supplier_id = $('.supplier_id').val();
                data.bc_id = $('.bc_id').val();
                data.sumber = $('.sumber').val();
                data.divisi_id = $('.divisi_id').val();
                data.warehouse_id = $('.warehouse_id').val();
                data.nama_barang = $('.nama_barang').val();
                data.no_aju = $(".no_aju").val();
                data.no_daftar = $('.no_daftar').val();
                data.sort = sort;
                data.sortType = sortType;
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
                data: "tipeBarang",
                className: "text-center",

            },
            {
                data: "jenisDokumen",
                className: "text-center",

            },
            {
                data: "noAju",
                className: "text-center",
            },
            {
                data: "noDaftar",
                className: "text-center",
            },
            {
                data: "tglDaftar",
                className: "text-center",
            },
            {
                data: "noPengeluaran",
                className: "text-center",
            },
            {
                data: "tglPengeluaran",
                className: "text-center",
            },
            {
                data: "suratJalan",
                className: "text-center",
            },
            {
                data: "jenisSumber",
                className: "text-center",
            },
            {
                data: "noOrder",
                className: "text-center",
            },
            {
                data: "noInvoice",
                className: "text-center",
            },
            {
                data: "divisi",
                className: "text-center",
            },
            {
                data: "warehouse",
                className: "text-center",
            },
            {
                data: "penerima",
                className: "text-center",
            },
            {
                data: "kodeBarang",
                className: "text-center",
            },
            {
                data: "barang",
                className: "text-center",
            },
            {
                data: "spesifikasi",
                className: "text-center",
            },
            {
                data: "jumlahBarang",
                className: "text-center",
            },

            {
                data: "satuanName",
                className: "text-center",
            },
            {
                data: "valas",
                className: "text-center",
            },
            {
                data: "hargaBarang",
                className: "text-center",
            },
            {
                data: "nilaiPenyerahan",
                className: "text-center",
            },
            {
                data: "jumlahPenerimaan",
                className: "text-center",
            },
            {
                data: "selisih",
                className: "text-center",
            },
            {
                data: "keterangan",
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
    $('#supplier_id').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#tipe_barang').select2({
        placeholder: "Pilih Tipe Barang",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#bc_id').select2({
        placeholder: "Pilih Dokumen Pabean",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#sumber').select2({
        placeholder: "Pilih Sumber Pemasukkan",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('.icon-dateStart').click(function() {
        $(".dateStart").focus();
    });

    $('.icon-dateEnd').click(function() {
        $(".dateEnd").focus();
    });

    $('.tipe_barang, .dateStart, .dateEnd, .supplier_id, .bc_id, .sumber, .divisi_id, .warehouse_id').change(function() {
        table.ajax.reload();
    });

    $('.nama_barang,.no_aju,.no_daftar').change(function() {
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

    $("#supplier_id,#tipe_barang,#divisi_id,#bc_id,#sumber,#warehouse_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    const pdfExcel = function(url) {
        let tipe_barang = $(".tipe_barang").val();
        let date_start = $(".dateStart").val();
        let date_end = $(".dateEnd").val();
        let supplier_id = $(".supplier_id").val();
        let bc_id = $(".bc_id").val();
        let sumber = $(".sumber").val();
        let divisi_id = $(".divisi_id").val();
        let warehouse_id = $(".warehouse_id").val();
        let nama_barang = $(".nama_barang").val();
        let no_aju = $(".no_aju").val();
        let no_daftar = $('.no_daftar').val();
        // let sort = "stock_details2.createdAt";
        // let sortType = "desc";
        window.open(url + `?tipe_barang=${tipe_barang}&date_start=${date_start}&date_end=${date_end}&supplier_id=${supplier_id}&bc_id=${bc_id}&sumber=${sumber}&divisi_id=${divisi_id}&warehouse_id=${warehouse_id}&nama_barang=${nama_barang}&no_aju=${no_aju}&no_daftar=${no_daftar}&sort=${sort}&sortType=${sortType}`, "_blank");
    }


    function getListWarehouse() {
        // GET WAREHOUSES
        $.ajax({
            url: `<?= base_url('proses-rebus/warehouse'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $("#divisi_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $("#warehouse_id").empty()
                $("#warehouse_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $("#warehouse_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
                $("#warehouse_id").val();
            }
        });
    }
</script>

<?= $this->endSection(); ?>