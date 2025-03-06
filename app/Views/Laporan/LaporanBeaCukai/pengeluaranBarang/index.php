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
                            <input placeholder="" value="<?= date('d/m/Y') ?>" class="form-control dateEnd" id="dateEnd" name="dateEnd" aria-label="Floating label select example" />
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
                    <div class="input-group">
                        <div class="form-floating mb-3">
                            <input type="text" name="no_daftar" id="no_daftar" class="form-control no_daftar" placeholder="No Daftar">
                            <label style="z-index: 1;">No Daftar</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button class="btn btn-secondary" onclick="handleFilter()" type="button">
                                <i class="fas fa-search"></i>
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
                                <th>No</th>
                                <th>Tipe Barang</th>
                                <th>Jenis Dokumen</th>
                                <th>Nomor Aju</th>
                                <th>No Daftar</th>
                                <th>Tgl Daftar</th>
                                <th>No Stuffing / No Pengeluaran</th>
                                <th>Tgl Pengeluaran</th>
                                <th>Surat Jalan</th>
                                <th>Jenis Order</th>
                                <th>No Order</th>
                                <th>No Invoice</th>
                                <th>Departemen</th>
                                <th>Warehouse</th>
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
                                <th>Keterangan</th>
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

    var dataTable = $('#dataTable').DataTable({

        processing: false,
        serverSide: false,
        ordering: true,
        order: [],
        fixedHeader: true,
        pageLength: 25, // Default jumlah data per halaman
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ], // Opsi jumlah data per halaman
        initComplete: function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: true,
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

    $('.tipe_barang, .dateStart, .dateEnd, .supplier_id, .bc_id, .sumber, .divisi_id, .warehouse_id').change(function() {});

    $('.nama_barang,.no_aju,.no_daftar').change(function() {});

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

    const handleFilter = () => {
        let date_start = $(".dateStart").val();
        let date_end = $(".dateEnd").val();

        if (!date_start || !date_end) {
            Swal.fire({
                icon: 'error',
                text: 'Tanggal mulai dan tanggal akhir wajib diisi!',
            });
            return;
        }

        const datePattern = /^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/([0-9]{4})$/;
        if (!datePattern.test(date_start) || !datePattern.test(date_end)) {
            Swal.fire({
                icon: 'error',
                text: 'Format tanggal harus DD/MM/YYYY!',
            });
            return;
        }

        const startDateParts = date_start.split('/');
        const endDateParts = date_end.split('/');
        const startDate = new Date(`${startDateParts[2]}-${startDateParts[1]}-${startDateParts[0]}`);
        const endDate = new Date(`${endDateParts[2]}-${endDateParts[1]}-${endDateParts[0]}`);

        const oneYearInMillis = 365 * 24 * 60 * 60 * 1000;
        if (endDate - startDate > oneYearInMillis) {
            Swal.fire({
                icon: 'error',
                text: 'Rentang tanggal tidak boleh lebih dari satu tahun!',
            });
            return;
        }

        showData();
    }

    function showData() {
        $.ajax({
            url: `<?= base_url("laporan-bea-cukai/all-keluar"); ?>`,
            method: "GET",
            data: {
                status: "Out",
                tipe_barang: $(".tipe_barang").val(),
                date_start: $(".dateStart").val(),
                date_end: $(".dateEnd").val(),
                supplier_id: $('.supplier_id').val(),
                bc_id: $('.bc_id').val(),
                sumber: $('.sumber').val(),
                divisi_id: $('.divisi_id').val(),
                warehouse_id: $('.warehouse_id').val(),
                nama_barang: $('.nama_barang').val(),
                no_aju: $(".no_aju").val(),
                no_daftar: $('.no_daftar').val(),
                sort: "DESC",
                sortType: "stock_details2.createdAt",
            },
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            dataType: "json",
            success: function(res) {
                if ($.fn.DataTable.isDataTable('#dataTable')) {
                    $('#dataTable').DataTable().clear().draw();
                    dataTable.destroy();
                }
                const table = $('#dataTable');
                table.find('tbody').empty();
                table.find('tfoot').empty();

                $.each(res.data.data, function(i, v) {
                    var newRow = $('<tr style="color:whitesmoke;">');
                    newRow.append($('<td>').text(v.no));
                    newRow.append($('<td>').text(v.tipeBarang));
                    newRow.append($('<td>').text(v.jenisDokumen));
                    newRow.append($('<td>').text(v.noAju));
                    newRow.append($('<td>').text(v.noDaftar));
                    newRow.append($('<td>').text(v.tglDaftar));
                    newRow.append($('<td>').text(v.noPengeluaran));
                    newRow.append($('<td>').text(v.tglPengeluaran));
                    newRow.append($('<td>').text(v.suratJalan));
                    newRow.append($('<td>').text(v.jenisSumber));
                    newRow.append($('<td>').text(v.noOrder));
                    newRow.append($('<td>').text(v.noInvoice));
                    newRow.append($('<td>').text(v.divisi));
                    newRow.append($('<td>').text(v.warehouse));
                    newRow.append($('<td>').text(v.penerima));
                    newRow.append($('<td>').text(v.kodeBarang));
                    newRow.append($('<td>').text(v.barang));
                    newRow.append($('<td>').text(v.spesifikasi));
                    newRow.append($('<td>').text(v.jumlahBarang));
                    newRow.append($('<td>').text(v.satuanName));
                    newRow.append($('<td>').text(v.valas));
                    newRow.append($('<td>').text(v.hargaBarang));
                    newRow.append($('<td>').text(v.nilaiPenyerahan));
                    newRow.append($('<td>').text(v.jumlahPenerimaan));
                    newRow.append($('<td>').text(v.selisih));
                    newRow.append($('<td>').text(v.keterangan));
                    table.find('tbody').append(newRow);
                });
                dataTable = $('#dataTable').DataTable({

                    processing: false,
                    serverSide: false,
                    ordering: true,
                    order: [],
                    fixedHeader: true,
                    pageLength: 25, // Default jumlah data per halaman
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"]
                    ], // Opsi jumlah data per halaman
                    initComplete: function(settings, json) {
                        $('.dataTables_length').empty();
                        $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                        $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
                    },
                    display: "stripe",
                    searching: true,
                    language: {
                        emptyTable: "Tidak Ada Data",
                        lengthMenu: "Show _MENU_ entries",
                        paginate: {
                            previous: '<i class="fa fa-angle-left"></i>',
                            next: '<i class="fa fa-angle-right"></i>'
                        }
                    }
                });

                dataTable.draw();
            }
        })
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

    $(document).ready(function() {
        showData();
    })
</script>

<?= $this->endSection(); ?>