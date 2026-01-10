<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Bahan Penolong</h1>
        <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
            Import / Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <?php if (can('Master Barang', 'Bahan Penolong', 'c')) : ?>
                <li><button class="dropdown-item btn-upload-excel">Import Excel</button></li>
            <?php endif; ?>
            <li><button class="dropdown-item" onclick="excel('<?= base_url("barang-master/export-excel"); ?>')">Export Excel</button></li>
        </ul>
        <?php if (can('Master Barang', 'Bahan Penolong', 'c')) : ?>
            <button class="btn btn-show-form btn-add btn-add-barang float-right">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </button>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end">
                <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_coa" name="filter_coa" id="filter_coa">
                            <option value="" data-code=""></option>
                            <option value="belum" data-code="">BELUM PUNYA COA</option>
                            <option value="sudah" data-code="">SUDAH PUNYA COA</option>
                        </select>
                        <label for="floatingInput">Filter Akun</label>
                    </div>
                </div>
                <div class="col-md-3 col mb-3">
                    <div class="form-group mb-3">
                        <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari Data" value="" style="height: 50px;" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable dataTable-barang" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width:10px;">No</th>
                                <th onclick="changeSort('kelompok_barang')" class="sort" style="text-align:left;">Kategori</th>
                                <th onclick="changeSort('kode_barang')" class="sort" style="text-align:left;">Kode Barang</th>
                                <th onclick="changeSort('barang_name')" class="sort" style="text-align:left;">Nama Barang</th>
                                <th class="sort" onclick="changeSort('satuan_1')">Satuan</th>
                                <th class="sort">Satuan 2</th>
                                <th class="sort">Satuan 3</th>
                                <th class="sort" onclick="changeSort('supplier_terakhir')" style="text-align:left;">Harga Terakhir</th>
                                <th class="sort" onclick="changeSort('harga_terakhir')" style="text-align:left;">Supplier Terakhir</th>
                                <th class="sort" style="text-align:center;">Akun COA</th>
                                <th class="sort" style="text-align: center;">Action</th>
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

<div class="modal add-modal m-t-bahan-baku" id="add_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 1300px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label></h5>
            </div>
            <div class="modal-body">
                <?= csrf_field() ?>
                <form class="create-form" role="form" method="POST">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="type" value="<?= $type ?>">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select" name="parent_type_id" id="parent_type_id">
                                    <option value=""></option>
                                    <?php foreach ($kelompokBarang as $kb) : ?>
                                        <option value="<?= encrypt($kb['id']) ?>"><?= $kb['parent_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Kategori Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <div class="input-group input-group-password">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control" name="kode_barang" placeholder="Kode Barang">
                                        <label for="floatingInput">Kode Barang</label>
                                    </div>
                                    <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                        <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" id="generate_new_code" name="generate_new_code" type="checkbox" onchange="generateNewCode()">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" name="barang_name" id="barang_name">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                </form>
                <form class="spek-form" role="form">
                    <div class="col-subtitle-modal">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold modal-sub-title">Tambah Spesifikasi</label>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-add float-right" onclick="resetFormDetail()" style="background: orange;">
                                    <i class="fa fa-refresh fa-sm mr-2" aria-hidden="true"></i>Reset
                                </button>
                                <button type="button" class="btn btn-add float-right mr-1 title-detail-name" onclick="submitSpekForm()">
                                    <i class="title-detail-icon fa fa-plus fa-sm mr-2" aria-hidden="true"></i><span class="btn-text">Tambah</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <input autocomplete="one-time-code" type="hidden" class="spek_id" name="spek_id" id="spek_id" />
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" name="spek" id="spek">
                                <label for="floatingInput">Spesifikasi</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-floating">
                                <select class="form-select" name="satuan1_id" id="satuan1_id" title="Satuan terkecil dari produk. Cth: PCS" onchange="changeSpanText()">
                                    <option value=""></option>
                                    <?php foreach ($satuanBarang as $sb) : ?>
                                        <option value="<?= ($sb['id']); ?>"><?= $sb['kode_satuan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Satuan 1</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-floating">
                                <select class="form-select" name="satuan2_id" id="satuan2_id" title="Satuan yang lebih besar dari Satuan 1. Cth: LUSIN (12 Pcs)" onchange="checkSatuan2()">
                                    <option value=""></option>
                                    <?php foreach ($satuanBarang as $sb) : ?>
                                        <option value="<?= ($sb['id']); ?>"><?= $sb['kode_satuan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Satuan 2</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group ">
                                <input type="text" name="konversi_satuan_2" onchange="checkValueKonversi()" id="konversi_satuan_2" class="form-control" onkeypress="return isNumberKey(event)">
                                <div class="input-group-append">
                                    <span class="input-group-text satuan1">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-floating">
                                <select class="form-select" name="satuan3_id" id="satuan3_id" title="Satuan terbesar dari produk. Cth: DUS (konversi 48 PCS)" onchange="checkSatuan3()">
                                    <option value=""></option>
                                    <?php foreach ($satuanBarang as $sb) : ?>
                                        <option value="<?= ($sb['id']); ?>"><?= $sb['kode_satuan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Satuan 3</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group ">
                                <input type="text" name="konversi_satuan_3" onchange="checkValueKonversi()" id="konversi_satuan_3" class="form-control" onkeypress="return isNumberKey(event)">
                                <div class="input-group-append">
                                    <span class="input-group-text satuan1">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row mt-2">
                    <div class="col-md-12" style="font-size: 12px;">
                        Note : <br>
                        <ul>
                            <li style="height: 15px;">PCS, Lusin 12 PCS, Dus 24 PCS</li>
                            <li style="height: 15px;">Satuan 1 adalah satuan terkecil</li>
                            <li style="height: 15px;">Satuan 2 harus lebih besar daripada satuan 1</li>
                            <li style="height: 15px;">Saturan 3 harus lebih besar dari satuan 2</li>
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row justify-content-end row-col-spp">
                            <div class="col-md-3">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control" name="cari_spesifikasi" id="cari_spesifikasi" onkeyup="cariSpesifikasi(this.value)">
                                    <label for="floatingInput">Cari Spesifikasi</label>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div class="overflow-auto" style="max-height: 500px;">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="" width="100%" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col" style="width: 10px;">No</th>
                                            <th scope="col">Spesifikasi</th>
                                            <th scope="col">Satuan 1</th>
                                            <th scope="col">Satuan 2</th>
                                            <th scope="col">Satuan 3</th>
                                            <th scope="col" style="width:90px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="body-detail-table" id="body-detail-table" id="tbody2" style="cursor: pointer;">
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Kembali</button>
                <button type="submit" class="btn btn-submit-form btn-submit-master-barang">Simpan</button>
                <button type="button" class="btn btn-discard delete-btn">Hapus</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="historiModal" tabindex="-1" role="dialog" aria-labelledby="historiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="min-width: 1600px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historiModalLabel">Histori Purchase Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pembelianLokal-tab" data-toggle="tab" href="#pembelianLokal" role="tab" aria-controls="pembelianLokal" aria-selected="true">Purchase Order Lokal</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pembelianImport-tab" data-toggle="tab" href="#pembelianImport" role="tab" aria-controls="pembelianImport" aria-selected="false">Purchase Order Import</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="pembelianLokal" role="tabpanel" aria-labelledby="pembelianLokal">
                        <?php if (can('Pembelian', 'PO Lokal BP', 'r')) : ?>
                            <div class="row justify-content-end">
                                <div class="col-md-3">
                                    <button class="btn btn-success btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
                                        Export
                                    </button>
                                    <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                                        <li><button class="dropdown-item" onclick="excelHsitory('<?= base_url("barang-master/export-excel-history"); ?>')">Export Excel</button></li>
                                    </ul>
                                </div>
                                <div class="col-md-3">
                                    <input autocomplete="one-time-code" style="height: 40px;" value="" type="text" placeholder="Tanggal Awal PO" class="form-control form-control-lg po-start-date-lokal">
                                </div>
                                <div class="col-md-3">
                                    <input autocomplete="one-time-code" style="height: 40px;" value="" type="text" placeholder="Tanggal Akhir PO" class="form-control form-control-lg po-end-date-lokal" disabled>
                                </div>
                                <div class="col-md-3">
                                    <input autocomplete="one-time-code" style="height: 40px;" placeholder="Cari Data" value="" type="text" class="form-control form-control-lg search-po-lokal">
                                </div>

                            </div>
                            <table class="table-inside table-borderd nowrap table-hover-tobasurimi tablePoLokal" id="tablePoLokal" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col" onclick="changeShortPoLokal('am_purchase_orders.purchase_request_id')" class="sort">No SPP</th>
                                        <th scope="col" onclick="changeShortPoLokal('am_purchase_orders.po_no')" class="sort">No Po</th>
                                        <th scope="col" onclick="changeShortPoLokal('penerimaan_barang.no_penerimaan_barang')" class="sort">No LPB</th>
                                        <th scope="col" onclick="changeShortPoLokal('penerimaan_barang.tanggal')" class="sort">Tgl PO</th>
                                        <th scope="col" onclick="changeShortPoLokal('suppliers.name')" class="sort">Supplier</th>
                                        <th scope="col" onclick="changeShortPoLokal('barang_master_spesifikasi.spesifikasi')" class="sort">Barang</th>
                                        <th scope="col" onclick="changeShortPoLokal('penerimaan_barang_detail.qty')" class="sort">Qty</th>
                                        <th scope="col" onclick="changeShortPoLokal('penerimaan_barang_detail.unit')" class="sort">Satuan</th>
                                        <th scope="col" onclick="changeShortPoLokal('penerimaan_barang_detail.harga')" class="sort">Harga</th>
                                        <th scope="col" onclick="changeShortPoLokal('penerimaan_barang_detail.sub_total')" class="sort">Total Harga</th>
                                        <th scope="col" onclick="changeShortPoLokal('am_purchase_orders.division_id')" class="sort">Departemen</th>
                                        <th scope="col" onclick="changeShortPoLokal('am_purchase_orders.note')" class="sort">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <div class="alert alert-secondary text-black" role="alert">
                                <b>Maaf Anda Tidak Punya Akses</b>
                            </div>
                        <?php endif; ?>
                    </div>


                    <div class="tab-pane fade" id="pembelianImport" role="tabpanel" aria-labelledby="pembelianImport">
                        <?php if (can('Pembelian', 'PO Import BP', 'r')) : ?>
                            <div class="row justify-content-end">
                                <div class="col-md-3">
                                    <input autocomplete="one-time-code" style="height: 40px;" value="" placeholder="Cari Nomor PO" type="text" class="form-control form-control-lg search-po-import">
                                </div>
                            </div>
                            <table class="table-inside table-borderd nowrap table-hover-tobasurimi tablePoImport" id="tablePoImport" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col" onclick="changeShortPoImport('am_purchase_orders.po_no')" class="sort">No PO</th>
                                        <th scope="col" onclick="changeShortPoImport('am_purchase_orders.po_date')" class="sort">Tanggal</th>
                                        <th scope="col" onclick="changeShortPoImport('suppliers.name')" class="sort">Supplier</th>
                                        <th scope="col" onclick="changeShortPoImport('barang_master_spesifikasi.spesifikasi')" class="sort">Barang</th>
                                        <th scope="col" onclick="changeShortPoImport('am_purchase_order_details.price')" class="sort">Harga Terakhir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <div class="alert alert-secondary text-black" role="alert">
                                <b>Maaf Anda Tidak Punya Akses</b>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="import_excel_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Master Barang Bahan Penolong</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-secondary text-black" role="alert">
                    UNDUH FORMAT EXCEL <a href="<?= base_url('assets/import/IMPORT_EXCEL_MASTER_BP.xlsx') ?>" style="text-decoration: none;"><b style="color: black;">DISINI</b></a>
                </div>
                <form class="form-excel" method="post">
                    <div class="form-floating" style="height: 50px;">
                        <input type="file" name="file" id="file" accept=".xlsx" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-import-excel mr-2">Kembali</button>
                <button type="submit" class="btn btn-submit-form btn-submit-excel" id="btn-submit-excel">Simpan</button>
            </div>
        </div>
    </div>
</div>


<script>
    let sort = "createdAt";
    let sortType = "desc";
    let id_barang = "";
    let sortTypePoLokal = "desc";
    let sortTypePoImport = "desc";
    let sortPoLokal = "am_purchase_orders.id";
    let sortPoImport = "am_purchase_orders.id";
    let tablePoImport = null;
    let tablePoLokal = null;
    const csrfToken = '<?= csrf_token() ?>';

    let list_items = [];

    const table = $('.dataTable-barang').DataTable({

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
            url: "<?= base_url("barang-master/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.sort = sort;
                data.sortType = sortType;
                data.parent_type = "<?= $type ?>";
                data.filter_coa = $(".filter_coa").val();
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                sortable: false,
                width: "5%"
            }, {
                data: "kelompok_barang",
                className: "text-left",
            },
            {
                data: "kode_barang",
                className: "text-left",
            },
            {
                data: "barang_name",
                className: "text-left",
                render: function(data) {
                    if (!data) return '';

                    let words = data.split(' ');
                    let result = [];

                    for (let i = 0; i < words.length; i += 5) {
                        result.push(words.slice(i, i + 5).join(' '));
                    }

                    return result.join('<br>');
                }
            },
            {
                data: "satuan",
                className: "text-center",
            },
            {
                data: "satuan2",
                className: "text-center",
                sortable: false,
            },
            {
                data: "satuan3",
                className: "text-center",
                sortable: false,
            },
            {
                data: "harga_terakhir",
                className: "text-left",
                render: function(data, type, row) {
                    if (data == "") {
                        return "-";
                    } else {
                        var satuan_terakhir = row.satuan_terakhir;
                        return satuan_terakhir == null ? greatFormatRupiah(data) : greatFormatRupiah(data) + " / " + row.satuan_terakhir;
                    }
                }
            },
            {
                data: "supplier_terakhir",
                className: "text-left",
                render: function(data) {
                    if (!data) return '';

                    let words = data.split(' ');
                    let result = [];

                    for (let i = 0; i < words.length; i += 5) {
                        result.push(words.slice(i, i + 5).join(' '));
                    }

                    return result.join('<br>');
                }
            },
            {
                data: "akun_coa", // Assuming "akun_coa" is the field name in your data source
                className: "text-center",
                render: function(data, type, row) {
                    // If "akun_coa" exists and is not empty, display a checkbox
                    if (data && data !== "") {
                        return "<i class='fa fa-check' aria-hidden='true' style='color:green;'></i>";
                    } else { // Otherwise, display a dash "-"
                        return "<i class='fa fa-minus' aria-hidden='true' style='color:red;'></i>";
                    }
                }
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    let spesifikasi_id = row.spesifikasi_id;

                    return `
                        <?php if (can('Master Barang', 'Bahan Penolong', 'u')) : ?>
                            <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                        <?php endif; ?>
                         <?php if (can('Master Barang', 'Bahan Penolong', 'd')) : ?>
                            <button data-toggle="tooltip" title="Hapus" onclick="removeSpek('${spesifikasi_id}')" class="btn btn-danger delete-parent">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                           <button onclick="displayHistory('${row.spesifikasi_id}')" class="btn btn-success posting-spp actions" data-toggle="tooltip" title="Histori">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </button>
                    `
                }
            }

        ],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        },
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Master Data Bahan Penolong Masih Kosong",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    $(document).ready(function() {
        function debounce(func, delay) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        }

        $(".po-start-date-lokal").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $(".po-end-date-lokal").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $(".search").keyup(
            debounce(function() {
                let query = $(this).val();

                if (query.length >= 2) {
                    table.ajax.reload(null, false);
                } else if (query.length === 0) {
                    table.ajax.reload(); // Reload untuk menghapus filter jika input kosong
                }
            }, 400)
        )

        $(".filter_coa").change(function() {
            table.ajax.reload();
        });

        $('.btn-add-barang').click(function() {
            $('.title-name').text("Tambah Bahan Penolong");
            $(".create-form :input:not([name='type'])").val('');
            $('select[name="parent_type_id"]').val(null).change();
            list_items.splice(0, list_items.length);
            drawTable();
            validator_spek.resetForm();
            validator_spek.reset();
            validator.resetForm();
            validator.reset();

            $('.delete-btn').hide();
            $('input[name="kode_barang"]').attr('readonly', false);
            $('#generate_new_code').prop('checked', true).change().show();
            resetFormDetail();
            $('.add-modal').modal('show');
        });

        $('.btn-discard').click(function() {
            $('.add-modal').modal('hide');
        });

        // upload excel
        $('.btn-upload-excel').click(function() {
            $('#file').val(null);
            $('#import_excel_modal').modal('show');

        });

        $('.btn-discard-import-excel').click(function() {
            $('#import_excel_modal').modal('hide');
        });


        // $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        //     let data = table.row(this).data();
        //     let id = data.id;
        //     let csrf = $(`[name="${csrfToken}"]`);
        //     $(".create-form :input:not([name='type'])").val('');
        //     $.ajax({
        //         url: "<?= base_url('barang-master/get') ?>",
        //         data: {
        //             id: id
        //         },
        //         beforeSend: function(xhr) {
        //             xhr.setRequestHeader('X-CSRF-Token', csrf.val());
        //         },
        //         method: "POST",
        //         dataType: "json",
        //         success: function(res) {
        //             $('.delete-btn').show();
        //             $('.title-name').text("Update Bahan Penolong");
        //             <?php if (!can('Master Barang', 'Bahan Penolong', 'u')) : ?>
        //                 $('.btn-submit-master-barang').hide();
        //             <?php endif; ?>
        //             // $('input[name="kode_barang"]').attr('readonly', true);
        //             $('#generate_new_code').hide();
        //             $('input[name="kode_barang"]').val(res.data.kode_barang);
        //             $('select[name="parent_type_id"]').val(res.data.parent_type_id).change();
        //             $('input[name="barang_name"]').val(res.data.barang_name);
        //             $('input[name="id"]').val(res.data.id);


        //             // Iterate through dataSpekDetail and append rows to the table
        //             if (res.dataSpekDetail && res.dataSpekDetail.length > 0) {
        //                 list_items.splice(0, list_items.length);
        //                 res.dataSpekDetail.forEach(function(item) {
        //                     list_items.push({
        //                         'spek_id': getID(),
        //                         'spesifikasi_id': item.id,
        //                         'spesifikasi': item.spesifikasi,
        //                         'satuan_1': item.satuan_1,
        //                         'satuan_1_text': item.satuan1_text,
        //                         'satuan_2': item.satuan_2,
        //                         'satuan_2_text': item.satuan2_text,
        //                         'konversi_satuan_2': item.konversi_satuan_2,
        //                         'satuan_3': item.satuan_3,
        //                         'satuan_3_text': item.satuan3_text,
        //                         'konversi_satuan_3': item.konversi_satuan_3
        //                     });
        //                     resetFormDetail();
        //                     drawTable();
        //                 });
        //             }
        //             validator_spek.resetForm();
        //             validator_spek.reset();
        //             validator.resetForm();
        //             validator.reset();
        //             $('.add-modal').modal('show');
        //         }
        //     })
        // })
        $('.btn-submit-master-barang').click(function(e) {
            e.preventDefault();
            if (list_items.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Masukkan Spesifikasi Barang Dahulu',
                    confirmButtonColor: '#4e73df',
                });
            } else {
                if ($(".create-form").valid()) {
                    Swal.fire({
                        icon: 'question',
                        title: 'Simpan Data?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Kembali',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let id = $('input[name="id"]').val();
                            let csrf = $(`[name="${csrfToken}"]`);
                            let data = new FormData(document.querySelector(".create-form"));
                            data.append("items", JSON.stringify(list_items));

                            if (id) {
                                $.ajax({
                                    url: "<?= base_url("barang-master/update"); ?>",
                                    data: data,
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                    },
                                    method: "POST",
                                    dataType: "json",
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        csrf.val(response.token);
                                        if (response.status) {
                                            Swal.fire({
                                                    icon: 'success',
                                                    title: response.message,
                                                    confirmButtonColor: '#4e73df',
                                                })
                                                .then(() => {
                                                    table.ajax.reload();
                                                    $(".add-modal").modal("hide");
                                                })
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            }).then(() => {

                                            });
                                        }
                                    },
                                    onError: function(response) {
                                        csrf.val(response.token);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Data Gagal Disimpan, coba Lagi',
                                            confirmButtonColor: '#4e73df',
                                        })
                                    }
                                });
                            } else {
                                $.ajax({
                                    url: "<?= base_url("barang-master/save"); ?>",
                                    data: data,
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                    },
                                    method: "POST",
                                    dataType: "json",
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        csrf.val(response.token);
                                        if (response.status) {
                                            Swal.fire({
                                                    icon: 'success',
                                                    title: response.message,
                                                    confirmButtonColor: '#4e73df',
                                                })
                                                .then(() => {
                                                    table.ajax.reload();
                                                    $(".add-modal").modal("hide");
                                                })
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            }).then(() => {

                                            });
                                        }
                                    },
                                    onError: function(response) {
                                        csrf.val(response.token);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Data Gagal Disimpan, coba Lagi',
                                            confirmButtonColor: '#4e73df',
                                        })
                                    }
                                });
                            }
                        }
                    })
                }
            }
        });
        $(".delete-btn").click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Hapus Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    let id = $("#id").val();
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("barang-master/delete"); ?>",
                        data: {
                            id: id
                        },
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        method: "POST",
                        dataType: "json",
                        success: function(response) {
                            csrf.val(response.token);
                            if (response.status) {
                                stopLoading()
                                Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                    .then(() => {
                                        table.ajax.reload()
                                        $(".add-modal").modal("hide")
                                    });
                            }
                        },
                        onError: function(response) {
                            csrf.val(response.token);
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Gagal Disimpan, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    });
                }
            })
        });

        tablePoLokal = $('#tablePoLokal').DataTable({

            processing: true,
            serverSide: true,
            ordering: true,
            order: [
                [2, 'desc']
            ],
            fixedHeader: true,
            lengthMenu: [
                [10],
                [10],
            ],
            pageLength: 10,
            ajax: {
                url: "<?= base_url("barang-bahan-penolong/histori"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.id = id_barang;
                    data.search = $(".search-po-lokal").val();
                    data.start_date = $(".po-start-date-lokal").val();
                    data.end_date = $(".po-end-date-lokal").val();
                    data.sort = sortPoLokal;
                    data.sortType = sortTypePoLokal;
                    data.po_type = "Lokal";
                }
            },
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.tablePoLokal').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            display: "stripe",
            searching: false,
            columns: [{
                data: "no",
                className: "text-center",
                sortable: false
            }, {
                data: "spp_no",
                className: "text-center"
            }, {
                data: "po_no",
                className: "text-center"
            }, {
                data: "no_lpb",
                className: "text-center",
                sortable: false
            }, {
                data: "po_date",
                className: "text-center"
            }, {
                data: "nama_supplier",
                className: "text-center"
            }, {
                data: "nama_barang",
                className: "text-center"
            }, {
                data: "qty",
                className: "text-center",
                render: function(data) {
                    return greatFormatRupiah(data);
                }
            }, {
                data: "kode_satuan",
                className: "text-center"
            }, {
                data: "price",
                className: "text-center",
                render: function(data) {
                    return greatFormatRupiah(data);
                }
            }, {
                data: "sub_total",
                className: "text-center",
                render: function(data) {
                    return greatFormatRupiah(data);
                }
            }, {
                data: "divisi",
                className: "text-center"
            }, {
                data: "note",
                className: "text-center"
            }, ],
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

        tablePoImport = $('#tablePoImport').DataTable({

            processing: true,
            serverSide: true,
            ordering: true,
            order: [
                [2, 'desc']
            ],
            fixedHeader: true,
            lengthMenu: [
                [15],
                [15],
            ],
            pageLength: 15,
            ajax: {
                url: "<?= base_url("barang-bahan-penolong/histori"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.id = id_barang;
                    data.po_type = "Import";
                    data.search = $(".search-po-import").val();
                    data.sort = sortPoImport;
                    data.sortType = sortTypePoImport;
                }
            },
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.tablePoLokal').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            display: "stripe",
            searching: false,
            columns: [{
                data: "no",
                className: "text-center",
                sortable: false
            }, {
                data: "po_no",
                className: "text-center"
            }, {
                data: "po_date",
                className: "text-center"
            }, {
                data: "nama_supplier",
                className: "text-center"
            }, {
                data: "nama_barang",
                className: "text-center"
            }, {
                data: "price",
                className: "text-center"
            }],
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

        $(".search-po-import").keyup(function() {
            tablePoImport.ajax.reload();
        })

        $(".search-po-lokal").keyup(function() {
            tablePoLokal.ajax.reload();
        })

        $(".po-start-date-lokal").change(function() {
            // tablePoLokal.ajax.reload();
            $(".po-end-date-lokal").prop('disabled', false); // Enable the end date field
        });

        $(".po-end-date-lokal").change(function() {
            tablePoLokal.ajax.reload();
        });
    });

    $('#btn-submit-excel').click(function() {
        if ($('.form-excel').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Import Excel?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    let csrf = $(`[name="${csrfToken}"]`);
                    let formData = new FormData(document.querySelector(".form-excel"));
                    formData.append('type_barang', "bahan_penolong");
                    $.ajax({
                        url: "<?= base_url("barang-master/import"); ?>",
                        data: formData,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            setLoading();
                        },
                        complete: function() {
                            stopLoading();
                        },
                        method: "POST",
                        dataType: "json",
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            csrf.val(response.token);
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                });
                            }
                        },
                    });

                }
            })
        }
    });

    function edit(id) {
        let csrf = $(`[name="${csrfToken}"]`);
        $(".create-form :input:not([name='type'])").val('');
        $.ajax({
            url: "<?= base_url('barang-master/get') ?>",
            data: {
                id: id
            },
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            method: "POST",
            dataType: "json",
            success: function(res) {
                $('.delete-btn').show();
                $('.title-name').text("Update Bahan Penolong");
                <?php if (!can('Master Barang', 'Bahan Penolong', 'u')) : ?>
                    $('.btn-submit-master-barang').hide();
                <?php endif; ?>
                // $('input[name="kode_barang"]').attr('readonly', true);
                $('#generate_new_code').hide();
                $('input[name="kode_barang"]').val(res.data.kode_barang);
                $('select[name="parent_type_id"]').val(res.data.parent_type_id).change();
                $('input[name="barang_name"]').val(res.data.barang_name);
                $('input[name="id"]').val(res.data.id);


                // Iterate through dataSpekDetail and append rows to the table
                if (res.dataSpekDetail && res.dataSpekDetail.length > 0) {
                    list_items.splice(0, list_items.length);
                    res.dataSpekDetail.forEach(function(item) {
                        list_items.push({
                            'spek_id': getID(),
                            'spesifikasi_id': item.id,
                            'spesifikasi': item.spesifikasi,
                            'satuan_1': item.satuan_1,
                            'satuan_1_text': item.satuan1_text,
                            'satuan_2': item.satuan_2,
                            'satuan_2_text': item.satuan2_text,
                            'konversi_satuan_2': item.konversi_satuan_2,
                            'satuan_3': item.satuan_3,
                            'satuan_3_text': item.satuan3_text,
                            'konversi_satuan_3': item.konversi_satuan_3
                        });
                        resetFormDetail();
                        drawTable();
                    });
                }
                validator_spek.resetForm();
                validator_spek.reset();
                validator.resetForm();
                validator.reset();
                $('.add-modal').modal('show');
            }
        })
    }

    function removeSpek(spesifikasi_id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("barang-master/delete-spek"); ?>",
                    data: {
                        id: spesifikasi_id
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        csrf.val(response.token);
                        if (response.status) {
                            Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    table.ajax.reload()
                                });
                        }
                    },
                    onError: function(response) {
                        csrf.val(response.token);
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Gagal Dihapus, coba Lagi',
                            confirmButtonColor: '#4e73df',
                        })
                    }
                });
            }
        })
    }

    var validator_excel = $(".form-excel").validate({
        rules: {
            file: {
                required: true
            },
        },
        messages: {
            file: {
                required: "File wajib diisi"
            },
        },
    });

    var validator_spek = $(".spek-form").validate({
        rules: {
            spek: {
                required: true
            },
            satuan1_id: {
                required: true
            }
        },
        messages: {
            spek: {
                required: "Spesifikasi wajib diisi"
            },
            satuan1_id: {
                required: "Satuan 1 wajib diisi"
            }
        },
        errorElement: 'span',
        errorClass: 'text-danger',
        errorPlacement: function(error, element) {
            var elem = $(element);
            if (elem.hasClass("select2-hidden-accessible")) {
                element = $("#select2-" + elem.attr("id") + "-container").parent();
                error.insertAfter(element);
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
            $(element).addClass('select-class');

        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
            $(element).removeClass('select-class');
        },
    });

    var validator = $(".create-form").validate({
        rules: {
            parent_type_id: {
                required: true
            },
            barang_name: {
                required: true
            },
            kode_barang: {
                required: true
            },
            'satuan1_id[]': {
                required: true
            }
        },
        messages: {
            parent_type_id: {
                required: "Kategori Barang Wajib Diisi"
            },
            barang_name: {
                required: "Nama Barang Wajib Diisi"
            },
            kode_barang: {
                required: "Kode Barang Wajib Diisi"
            },
            'satuan1_id[]': {
                required: "Satuan 1 Wajib Diisi"
            }
        },
        errorElement: 'span',
        errorClass: 'text-danger',
        errorPlacement: function(error, element) {
            var elem = $(element);
            if (elem.hasClass("select2-hidden-accessible")) {
                element = $("#select2-" + elem.attr("id") + "-container").parent();
                error.insertAfter(element);
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
            $(element).addClass('select-class');

        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
            $(element).removeClass('select-class');
        },
    });

    function changeSpanText(counter = null) {
        var selectedText, selectedSatuan1Val, selectedSatuan2Val, selectedSatuan3Val, spanText;

        if (counter) {
            selectedText = $(`#satuan1_id_${counter}`).find('option:selected').text();
            selectedSatuan1Val = $(`#satuan1_id_${counter}`).val();
            selectedSatuan2Val = $(`#satuan2_id_${counter}`).val();
            selectedSatuan3Val = $(`#satuan3_id_${counter}`).val();
            spanText = $(`.satuan_${counter}`);
        } else {
            selectedText = $('#satuan1_id').find('option:selected').text();
            selectedSatuan1Val = $('#satuan1_id').val();
            selectedSatuan2Val = $('#satuan2_id').val();
            selectedSatuan3Val = $('#satuan3_id').val();
            spanText = $('.satuan1');
        }

        if (selectedSatuan1Val && selectedSatuan2Val) {
            if (selectedSatuan1Val === selectedSatuan2Val) {
                Swal.fire({
                    icon: 'error',
                    title: 'Satuan 2 tidak boleh sama dengan satuan 1',
                    confirmButtonColor: '#4e73df',
                }).then(() => {
                    if (counter) {
                        $(`#satuan2_id_${counter}`).val('').change();
                        $(`#satuan3_id_${counter}`).val('').change();
                    } else {
                        $('#satuan2_id').val('').change();
                        $('#satuan3_id').val('').change();
                    }
                });
            }
        }
        if (selectedSatuan1Val && selectedSatuan3Val) {
            if (selectedSatuan1Val === selectedSatuan3Val) {
                Swal.fire({
                    icon: 'error',
                    title: 'Satuan 3 tidak boleh sama dengan satuan 1',
                    confirmButtonColor: '#4e73df',
                }).then(() => {
                    if (counter) {
                        $(`#satuan3_id_${counter}`).val('').change();
                    } else {
                        $('#satuan3_id').val('').change();
                    }
                });
            }
        }
        if (selectedSatuan2Val && selectedSatuan3Val) {
            if (selectedSatuan2Val === selectedSatuan3Val) {
                Swal.fire({
                    icon: 'error',
                    title: 'Satuan 3 tidak boleh sama dengan satuan 2 1',
                    confirmButtonColor: '#4e73df',
                }).then(() => {
                    if (counter) {

                        $(`#satuan3_id_${counter}`).val('').change();
                    } else {
                        $('#satuan3_id').val('').change();
                    }
                });
            }
        } else {
            if (selectedSatuan1Val == "") {
                if (counter) {

                    $('#satuan2_id_' + counter + '').val('').change();
                    $(`#konversi_satuan_2_${counter}`).val('');
                    $(`#satuan3_id_${counter}`).val('').change();
                    $(`#konversi_satuan_3_${counter}`).val('');
                } else {
                    $('#satuan2_id').val('').change();
                    $(`#konversi_satuan_2`).val('');
                    $('#satuan3_id').val('').change();
                    $(`#konversi_satuan_3`).val('');
                }
            }
        }
        // Ubah konten span sesuai dengan nilai yang dipilih
        spanText.text(selectedText ? selectedText : '-');
    }

    function checkSatuan2(counter = null) {
        var selectedText, selectedSatuan1Val, selectedSatuan2Val, selectedSatuan3Val, spanText;

        if (counter) {
            selectedSatuan1Val = $(`#satuan1_id_${counter}`).val();
            selectedSatuan2Val = $(`#satuan2_id_${counter}`).val();
            selectedSatuan3Val = $(`#satuan3_id_${counter}`).val();
            spanText = $(`.satuan_${counter}`);
        } else {
            selectedSatuan1Val = $('#satuan1_id').val();
            selectedSatuan2Val = $('#satuan2_id').val();
            selectedSatuan3Val = $('#satuan3_id').val();
            spanText = $('.satuan1');
        }

        if (selectedSatuan2Val && selectedSatuan1Val) {
            if (selectedSatuan2Val === selectedSatuan1Val) {
                Swal.fire({
                    icon: 'error',
                    title: 'Satuan 2 tidak boleh sama dengan satuan 1',
                    confirmButtonColor: '#4e73df',
                }).then(() => {
                    if (counter) {
                        $(`#satuan2_id_${counter}`).val('').change();
                    } else {
                        $('#satuan2_id').val('').change();
                    }
                });
            }
        } else if (selectedSatuan2Val && selectedSatuan3Val) {
            if (selectedSatuan2Val === selectedSatuan3Val) {
                Swal.fire({
                    icon: 'error',
                    title: 'Satuan 2 tidak boleh sama dengan satuan 3',
                    confirmButtonColor: '#4e73df',
                }).then(() => {
                    if (counter) {
                        $(`#satuan2_id_${counter}`).val('').change();
                    } else {
                        $('#satuan2_id').val('').change();
                    }
                });
            }
        } else {
            if (selectedSatuan2Val == "") {
                if (counter) {
                    $(`#konversi_satuan_2_${counter}`).val('');
                    $(`#satuan3_id_${counter}`).val('').change();
                    $(`#konversi_satuan_3_${counter}`).val('');
                } else {
                    $(`#konversi_satuan_2`).val('');
                    $('#satuan3_id').val('').change();
                    $(`#konversi_satuan_3`).val('');
                }
            }
        }
    }

    function checkSatuan3(counter = null) {
        var selectedText, selectedSatuan1Val, selectedSatuan2Val, selectedSatuan3Val, spanText;

        if (counter) {
            selectedSatuan1Val = $(`#satuan1_id_${counter}`).val();
            selectedSatuan2Val = $(`#satuan2_id_${counter}`).val();
            selectedSatuan3Val = $(`#satuan3_id_${counter}`).val();
            spanText = $(`.satuan_${counter}`);
        } else {
            selectedSatuan1Val = $('#satuan1_id').val();
            selectedSatuan2Val = $('#satuan2_id').val();
            selectedSatuan3Val = $('#satuan3_id').val();
            spanText = $('.satuan1');
        }

        if (selectedSatuan3Val && selectedSatuan1Val) {
            if (selectedSatuan3Val === selectedSatuan1Val) {
                Swal.fire({
                    icon: 'error',
                    title: 'Satuan 3 tidak boleh sama dengan satuan 1',
                    confirmButtonColor: '#4e73df',
                }).then(() => {
                    if (counter) {
                        $(`#satuan3_id_${counter}`).val('').change();
                    } else {
                        $('#satuan3_id').val('').change();
                    }
                });
            }
        } else if (selectedSatuan3Val && selectedSatuan2Val) {
            if (selectedSatuan3Val === selectedSatuan2Val) {
                Swal.fire({
                    icon: 'error',
                    title: 'Satuan 3 tidak boleh sama dengan satuan 2 2',
                    confirmButtonColor: '#4e73df',
                }).then(() => {
                    if (counter) {
                        $(`#satuan3_id_${counter}`).val('').change();
                    } else {
                        $('#satuan3_id').val('').change();
                    }
                });
            }
        }
    }

    function checkValueKonversi(counter = null) {
        var selectedSatuan2Val, selectedSatuan3Val;

        if (counter) {
            selectedSatuan2Val = $(`#konversi_satuan_2_${counter}`).val();
            selectedSatuan3Val = $(`#konversi_satuan_3_${counter}`).val();
        } else {
            selectedSatuan2Val = $('#konversi_satuan_2').val();
            selectedSatuan3Val = $('#konversi_satuan_3').val();
        }

        if (Number(selectedSatuan3Val) <= Number(selectedSatuan2Val) && selectedSatuan3Val) {
            Swal.fire({
                icon: 'error',
                title: 'Satuan 3 harus lebih besar dari satuan 2',
                confirmButtonColor: '#4e73df',
            }).then(() => {
                if (counter) {
                    $(`#konversi_satuan_3_${counter}`).val('').change();
                } else {
                    $('#konversi_satuan_3').val('').change();
                }
            });
        }
    }

    function generateNewCode() {
        let csrfToken = '<?= csrf_token() ?>';
        let value = document.getElementById('generate_new_code').checked ? true : false;
        let csrf = $(`[name="${csrfToken}"]`);
        if (value) {
            $("input[name='kode_barang']").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("barang-master/generate-new-code"); ?>`,
                data: {
                    type: "<?= $type ?>"
                },
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                method: "GET",
                success: function(res) {
                    csrf.val(res.token);
                    $("input[name='kode_barang']").attr("readonly", true);
                    $("input[name='kode_barang']").val(res.codeNew);
                }
            })
        } else {
            $("input[name='kode_barang']").attr("readonly", false);
            $("input[name='kode_barang']").val("");
        }
    }

    const submitSpekForm = function() {
        let spek = $("#spek").val().trim();
        let satuan1_id = $("#satuan1_id").val();
        let satuan1_text = $("#satuan1_id option:selected").text(); // Mendapatkan teks dari opsi yang dipilih
        let satuan2_id = $("#satuan2_id").val();
        let satuan2_text = $("#satuan2_id option:selected").text(); // Mendapatkan teks dari opsi yang dipilih
        let konversi_satuan_2 = $("#konversi_satuan_2").val();
        let satuan3_id = $("#satuan3_id").val();
        let satuan3_text = $("#satuan3_id option:selected").text(); // Mendapatkan teks dari opsi yang dipilih
        let konversi_satuan_3 = $("#konversi_satuan_3").val();
        let spek_id = $("#spek_id").val();
        let spesifikasi_id = $("#spesifikasi_id").val();

        if ($(".create-form").valid()) {
            if ($(".spek-form").valid()) {
                if (spek_id) {
                    $.each(list_items, function(i, v) {
                        if (v.spek_id === spek_id) {
                            list_items[i].spesifikasi = spek;
                            list_items[i].satuan_1 = satuan1_id;
                            list_items[i].satuan_1_text = satuan1_text;
                            list_items[i].satuan_2 = satuan2_id;
                            list_items[i].satuan_2_text = satuan2_text;
                            list_items[i].konversi_satuan_2 = konversi_satuan_2;
                            list_items[i].satuan_3 = satuan3_id;
                            list_items[i].satuan_3_text = satuan3_text;
                            list_items[i].konversi_satuan_3 = konversi_satuan_3;
                        }
                    });
                    resetFormDetail();
                    drawTable();
                } else {
                    let isDuplicate = list_items.some(function(item) {
                        return item.spesifikasi === spek;
                    });

                    if (!isDuplicate) {
                        list_items.push({
                            'spek_id': getID(),
                            'spesifikasi_id': "",
                            'spesifikasi': spek,
                            'satuan_1': satuan1_id,
                            'satuan_1_text': satuan1_text,
                            'satuan_2': satuan2_id,
                            'satuan_2_text': satuan2_text,
                            'konversi_satuan_2': konversi_satuan_2,
                            'satuan_3': satuan3_id,
                            'satuan_3_text': satuan3_text,
                            'konversi_satuan_3': konversi_satuan_3
                        });
                        resetFormDetail();
                        drawTable();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Spesifikasi sudah ada!',
                            confirmButtonColor: 'red',
                        })
                    }
                }
            }
        }
    }

    $(document).on('click', '.edit-table-detail', function(evt) {
        resetFormDetail();
        $(".title-detail-name .btn-text").text("Update");
        $(".title-detail-icon").removeClass("fa-plus").addClass("fa-pencil");

        validator_spek.resetForm();
        validator_spek.reset();
        let spek_id = $(this).data('spek_id');

        $.each(list_items, function(i, v) {
            if (v.spek_id === spek_id) {
                $("#spek_id").val(v.spek_id);
                $("#spesifikasi_id").val(v.id);
                $("#spek").val(v.spesifikasi);
                $("#satuan1_id").val(v.satuan_1).change();
                $("#satuan2_id").val(v.satuan_2).change();
                $("#konversi_satuan_2").val(v.konversi_satuan_2);
                $("#satuan3_id").val(v.satuan_3).change();
                $("#konversi_satuan_3").val(v.konversi_satuan_3);
            }
        });
    });

    const drawTable = function() {
        $('.body-detail-table').empty();
        $('.tfoot').empty();
        var row = '';
        var no = 1;
        if (list_items.length === 0) {
            row += `
                    <tr>
                        <td colspan="7" class="text-center">Data Barang Tidak Ada</td>
                    </tr>
                `;
            $('.tfoot').append(row);
        } else {
            list_items.map(item => {
                row += '<tr style="color:whitesmoke;">';
                row += '<td>' + no + '</td>';
                row += '<td>' + item.spesifikasi + '</td>';
                row += '<td>' + item.satuan_1_text + '</td>';
                row += '<td>' + item.satuan_2_text + '(' + item.konversi_satuan_2 + ' ' + item.satuan_1_text + ')</td>';
                row += '<td>' + item.satuan_3_text + '(' + item.konversi_satuan_3 + ' ' + item.satuan_1_text + ')</td>';

                row += '<td>' + `
                    <button class="btn btn-warning edit-table-detail mr-2" data-spek_id="${item.spek_id}">
                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                    </button><button class="btn btn-danger" onclick="deleteRowDetail('${item.spek_id}', '${item.spesifikasi_id}')">
                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                    </button>` +
                    '</td>';

                no++;
            });
            $('.body-detail-table').append(row);
        }
    }
    const getID = function() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let randomString = '';

        for (let i = 0; i < 10; i++) {
            randomString += characters.charAt(Math.floor(Math.random() * characters.length));
        }

        return randomString;
    }
    const resetFormDetail = function() {
        $(".title-detail-name .btn-text").text("Tambah");
        $(".title-detail-icon").removeClass("fa-update").addClass("fa-plus");
        $("#spek_id").val('');
        $("#spek").val('');
        $("#satuan1_id").val('').val(null).change()
        $("#satuan2_id").val('').val(null).change()
        $("#konversi_satuan_2").val('');
        $("#satuan3_id").val('').val(null).change()
        $("#konversi_satuan_3").val('');
    }
    const deleteRowDetail = function(id, spesifikasi_id) {
        if (spesifikasi_id) {
            console.log(list_items.length);
            if (list_items.length <= 1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Spesifikasi minimal 1 (satu)',
                    confirmButtonColor: '#4e73df',
                });
            } else {
                Swal.fire({
                    icon: 'question',
                    title: 'Hapus Data Spesifikasi?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Kembali',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const csrf = $(`[name="${csrfToken}"]`);
                        setLoading()
                        $.ajax({
                            url: "<?= base_url("barang-master/delete-spek"); ?>",
                            data: {
                                id: spesifikasi_id
                            },
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            },
                            method: "POST",
                            dataType: "json",
                            success: function(response) {
                                csrf.val(response.token);
                                if (response.status) {
                                    stopLoading()
                                    Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        .then(() => {
                                            table.ajax.reload();
                                            $(".add-modal").modal("hide")
                                        });
                                }
                            },
                            onError: function(response) {
                                csrf.val(response.token);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Data Gagal Disimpan, coba Lagi',
                                    confirmButtonColor: '#4e73df',
                                })
                            }
                        });
                    }
                })
            }
        } else {
            const indexToRemove = list_items.findIndex(item => item.spek_id === id);
            if (indexToRemove !== -1) {
                list_items.splice(indexToRemove, 1);
            }
        }
        drawTable();
    }

    function displayHistory(id) {
        id_barang = id;
        $('.search-po-lokal').val();
        $('.po-start-date-lokal').val();
        $('.po-end-date-lokal').val();
        $('.search-po-import').val();
        tablePoLokal.ajax.reload();
        tablePoImport.ajax.reload();
        $('#historiModal').modal('show');
    }

    $("select[name='parent_type_id']")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $("select[name='parent_type_id']")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $("select[name='parent_type_id']")
        .parent('div')
        .find('label')
        .css('z-index', '1');
    $("select[name='parent_type_id']").select2({
        placeholder: "Pilih Kategori Barang",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $(".add-modal .modal-content")
    });

    $('.filter_coa').select2({
        placeholder: "Filter Akun",
        theme: "bootstrap-5",
        allowClear: true,
    })

    //CSS SELECT2 FLOATING LABEL
    $('.filter_coa')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.filter_coa')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.filter_coa')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    $("#satuan1_id, #satuan2_id, #satuan3_id")
        .parent('div')
        .find('label')
        .css('z-index', '1');
    $("#satuan1_id, #satuan2_id, #satuan3_id").select2({
        theme: "bootstrap-5",
        allowClear: true,
        placeholder: 'Pilih Satuan',
        dropdownParent: $(".add-modal .modal-content")
    });
    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    function changeShortPoImport(val) {
        if (sortPoImport !== val) {
            sortTypePoImport = "asc";
            sortPoImport = val;
        } else {
            sortTypePoImport = sortTypePoImport === "asc" ? "desc" : "asc";
        }
        tablePoImport.ajax.reload();
    }

    function changeShortPoLokal(val) {
        if (sortPoLokal !== val) {
            sortTypePoLokal = "asc";
            sortPoLokal = val;
        } else {
            sortTypePoLokal = sortTypePoLokal === "asc" ? "desc" : "asc";
        }
        tablePoLokal.ajax.reload();
    }

    //export excel
    const excel = function(url) {
        let search = $(".search").val();
        let parent_type = "<?= $type ?>";
        let filter_coa = $(".filter_coa").val();

        window.open(url + `?search=${search}&parent_type=${parent_type}&filter_coa=${filter_coa}&sort=${sort}&sortType=${sortType}`, "_blank");
    }

    function excelHsitory(url) {
        // Ambil parameter yang diperlukan dari halaman
        const id = id_barang;
        const search = $(".search-po-lokal").val();
        const start_date = $(".po-start-date-lokal").val();
        const end_date = $(".po-end-date-lokal").val();
        const sort = sortPoLokal;
        const sortType = sortTypePoLokal;
        const po_type = "Lokal";

        // Redirect ke URL export dengan parameter
        window.location.href = url + '?id=' + id + '&po_type=' + po_type +
            '&search=' + search + '&start_date=' + start_date + '&end_date=' + end_date;
    }

    const cariSpesifikasi = function(value) {
        let val = value.toLowerCase().trim();

        $('#body-detail-table tr').filter(function() {
            let rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.indexOf(val) !== -1);
        });
    };
</script>

<?= $this->endSection(); ?>