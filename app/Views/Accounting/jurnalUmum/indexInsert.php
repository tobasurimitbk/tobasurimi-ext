<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header d-flex justify-content-end">
        <h1 class="me-auto">Tambah Nilai Barang</h1>
        <button style="margin-right: -100px!important;" class="btn btn-discard btn-dropdown-export dropdown-toggle" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
            Import / Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <li><button class="dropdown-item" id="btn_import_excel">Import Excel</button></li>
            <!-- <li><button class="dropdown-item" onclick="pdfExcel('<?= base_url("jurnal/print-pdf"); ?>')">Export Pdf (By Filter)</button></li>
            <li><button class="dropdown-item" onclick="pdfExcel('<?= base_url("jurnal/print-excel"); ?>')">Export Excel (By Filter)</button></li> -->
        </ul>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row row-col-spp mb-3">
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select kode_department" name="kode_department" id="kode_department">
                            <option value="" data-code=""></option>
                        </select>
                        <label for="floatingInput">Pilih Department</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select type_barang" name="type_barang" id="type_barang">
                            <option value="" data-code=""></option>
                            <option value="bahan_penolong" data-code="">Bahan Penolong</option>
                            <option value="bahan_jadi" data-code="">Bahan Jadi</option>
                            <option value="bahan_baku" data-code="">Bahan Baku</option>
                            <option value="bahan_scrap" data-code="">Bahan Scrap</option>
                            <option value="bahan_modal" data-code="">Bahan Modal</option>
                            <option value="bahan_setengah_jadi" data-code="">Bahan Setengah Jadi</option>
                        </select>
                        <label for="floatingInput">Pilih Type Barang</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select kode_barang" name="kode_barang" id="kode_barang">
                            <option value="" data-code=""></option>
                        </select>
                        <label for="floatingInput">Pilih Barang</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group input-group-password">
                        <input style="height: 50px;" autocomplete="one-time-code" class="form-control input-picker transaksi_date" id="transaksi_date" name="transaksi_date" placeholder="Tanggal Akhir">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-end_date"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="button" class="btn btn-primary w-100" id="search">
                        <i class="fas fa-search me-2"></i>Cari
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('barang_master_spesifikasi.spesifikasi')" class="sort">Nama Spesifikasi</th>
                                <th>Akun Persedian/Pembelian</th>
                                <th>Saldo Akun</th>
                                <th>Akun Penjualan</th>
                                <th>Saldo Akun</th>
                                <th>Akun Pemakaian</th>
                                <th>Saldo Akun</th>
                                <th>Action</th>
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

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name">Edit/Add COA Barang</label></h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data" onSubmit="return false">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <input type="hidden" name="divisi_id" class="divisi_id" id="divisi_id">
                    <input type="hidden" name="barang_id" class="barang_id" id="barang_id">
                    <input type="hidden" name="type" id="type" value="">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input disabled autocomplete="one-time-code" type="text" class="form-control" placeholder="Nama Barang" id="parentName" name="parentName">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input disabled autocomplete="one-time-code" type="text" class="form-control divisis_name" placeholder="Departemen" id="divisi_name" name="divisi_name">
                                <label for="floatingInput">Departemen</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select akun_ap_id" name="akun_ap_id" id="akun_ap_id">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($subAkuns)) {
                                        foreach ($subAkuns as $sub) {
                                    ?>
                                            <option value="<?= $sub->id; ?>"><?= $sub->no_sub; ?> <?= $sub->nama_sub; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Akun Persediaan/Pembelian</label>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select akun_ar_id" name="akun_ar_id" id="akun_ar_id">
                                    <option value="" data-code=""></option>
                                    <?php
                                    if (!empty($subAkuns)) {
                                        foreach ($subAkuns as $sub_ar) {
                                    ?>
                                            <option value="<?= $sub_ar->id; ?>"><?= $sub_ar->no_sub; ?> <?= $sub_ar->nama_sub; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Akun Penjualan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select akun_pemakaian_id" name="akun_pemakaian_id" id="akun_pemakaian_id">
                                    <option value="" data-code=""></option>
                                    <?php
                                    if (!empty($subAkuns)) {
                                        foreach ($subAkuns as $sub_ar) {
                                    ?>
                                            <option value="<?= $sub_ar->id; ?>"><?= $sub_ar->no_sub; ?> <?= $sub_ar->nama_sub; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Akun Pemakaian</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kategori" name="kategori" id="kategori">
                                    <option value="" data-code=""></option>
                                    <?php
                                    if (!empty($kategoriBarangAkun)) {
                                        foreach ($kategoriBarangAkun as $kategoriBarang) {
                                    ?>
                                            <option value="<?= $kategoriBarang->id; ?>"><?= $kategoriBarang->description; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Kategori Barang</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-submit-form btn-submit-form-akun">Simpan</button>
                <button type="button" class="btn btn-discard delete-btn delete-btn-akun">Hapus</button>
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-akun mr-2">Kembali</button>
            </div>
        </div>
    </div>
</div>

<div class="modal add-saldo-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name">Add/Edit Saldo Barang</label></h5>
            </div>
            <div class="modal-body">
                <form class="create-saldo-form" role="form" method="POST" enctype="multipart/form-data" onSubmit="return false">
                    <input autocomplete="one-time-code" type="hidden" class="id_saldo_barang" name="id_saldo_barang" id="id_saldo_barang" />
                    <input type="hidden" name="divisi_id_saldo_barang" class="divisi_id_saldo_barang" id="divisi_id_saldo_barang">
                    <input type="hidden" name="barang_id_saldo_barang" class="barang_id_saldo_barang" id="barang_id_saldo_barang">
                    <input type="hidden" name="type_saldo_barang" id="type_saldo_barang" value="">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input disabled autocomplete="one-time-code" type="text" class="form-control" placeholder="Nama Barang" id="parentName_saldo_barang" name="parentName_saldo_barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input disabled autocomplete="one-time-code" type="text" class="form-control divisis_name_saldo_barang" placeholder="Departemen" id="divisi_name_saldo_barang" name="divisi_name_saldo_barang">
                                <label for="floatingInput">Departemen</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" value="" type="date" class="form-control tanggal_transaksi" name="tanggal_transaksi" id="tanggal_transaksi" placeholder="">
                                    <label for="floatingInput">Tanggal Transaksi</label>
                                </div>
                                <!-- <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 0px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div> -->
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select akun_ap_id_saldo_barang" name="akun_ap_id_saldo_barang" id="akun_ap_id_saldo_barang">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($subAkuns)) {
                                        foreach ($subAkuns as $sub) {
                                    ?>
                                            <option value="<?= $sub->id; ?>"><?= $sub->no_sub; ?> <?= $sub->nama_sub; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Akun Persediaan/Pembelian</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" onkeyup="this.value = greatFormatRupiah(this.value)" placeholder="Saldo Akun Persedian/Pembelian" id="saldo_akun_pembelian" name="saldo_akun_pembelian">
                                <label for="floatingInput">Saldo Akun Persedian/Pembelian</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select akun_ar_id_saldo_barang" name="akun_ar_id_saldo_barang" id="akun_ar_id_saldo_barang">
                                    <option value="" data-code=""></option>
                                    <?php
                                    if (!empty($subAkuns)) {
                                        foreach ($subAkuns as $sub_ar) {
                                    ?>
                                            <option value="<?= $sub_ar->id; ?>"><?= $sub_ar->no_sub; ?> <?= $sub_ar->nama_sub; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Akun Penjualan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" onkeyup="this.value = greatFormatRupiah(this.value)" placeholder="Saldo Akun Penjualan" id="saldo_akun_penjualan" name="saldo_akun_penjualan">
                                <label for="floatingInput">Saldo Akun Penjualan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select akun_pemakaian_id_saldo_barang" name="akun_pemakaian_id_saldo_barang" id="akun_pemakaian_id_saldo_barang">
                                    <option value="" data-code=""></option>
                                    <?php
                                    if (!empty($subAkuns)) {
                                        foreach ($subAkuns as $sub_ar) {
                                    ?>
                                            <option value="<?= $sub_ar->id; ?>"><?= $sub_ar->no_sub; ?> <?= $sub_ar->nama_sub; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Akun Pemakaian</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" onkeyup="this.value = greatFormatRupiah(this.value)" placeholder="Saldo Akun Pemakaian" id="saldo_akun_pemakaian" name="saldo_akun_pemakaian">
                                <label for="floatingInput">Saldo Akun Pemakaian</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-submit-form btn-submit-form-saldo-barang">Simpan</button>
                <button type="button" class="btn btn-discard delete-btn delete-btn-saldo">Hapus</button>
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-saldo-barang mr-2">Kembali</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="import_excel_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Saldo Barang</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-secondary text-black" role="alert">
                    UNDUH TEMPLATE EXCEL <a href="<?= base_url('jurnal/template-nilai-barang') ?>" style="text-decoration: none;"><b style="color: black;">DISINI</b></a>
                </div>
                <form class="form-excel" method="post">
                    <div class="form-floating" style="height: 50px;">
                        <input type="file" name="file" id="excelFileInput" accept=".xlsx" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-discard-import-excel" class="btn btn-hide-form btn-discard btn-discard-import-excel mr-2">Kembali</button>
                <button type="submit" onclick="importExcel()" class="btn btn-submit-form btn-submit-excel">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "createdAt";
    let sortType = "desc";

    const table = $('.dataTable').DataTable({

        processing: true,
        serverSide: true,
        ordering: true,
        deferLoading: 0,
        order: [
            [4, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("jurnal/get-data"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.kode_department = $("#kode_department").val();
                data.type_transaksi = $('#type_barang').val();
                data.kode_barang = $("#kode_barang").val();
                data.transaksi_date = $("#transaksi_date").val();
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
        columns: [{
                data: null,
                className: "text-center",
                orderable: false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: "spesifikasi",
                className: "text-left"
            },
            {
                data: "no_sub_pembelian",
                className: "text-left"
            },
            {
                data: "saldo_awal_barang_pembelian",
                className: "text-left",
                render: function(data, type, row) {
                    return greatFormatRupiah(row.saldo_awal_barang_pembelian);
                },
                searchable: false,
                sortable: false
            },
            {
                data: "no_sub_penjualan",
                className: "text-left",
            },
            {
                data: "saldo_awal_barang_penjualan",
                className: "text-left",
                render: function(data, type, row) {
                    return greatFormatRupiah(row.saldo_awal_barang_penjualan);
                },
                searchable: false,
                sortable: false
            },
            {
                data: "no_sub_pemakaian",
                className: "text-left"
            },
            {
                data: "saldo_awal_barang_pemakaian",
                className: "text-left",
                render: function(data, type, row) {
                    return greatFormatRupiah(row.saldo_awal_barang_pemakaian);
                },
                searchable: false,
                sortable: false
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;

                    return `
                        <div class="mt-0">
                            <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit/Add Account COA" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="javascript:void(0)" onclick="detail('${id}')" data-toggle="tooltip" title="Insert Saldo Barang" class="btn btn-success posting-spp actions">
                                <i class="fas fa-plus"></i>
                            </a>
                        </div>
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
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    function edit(id) {
        let rowData = table.rows().data().toArray();
        let selectedData = rowData.find(row => row.id == id);
        let account_barang_id = selectedData.account_barang_id;
        let parentName = selectedData.spesifikasi;
        let ap_id = selectedData.ap_id;
        let ar_id = selectedData.ar_id;
        let pemakaian_id = selectedData.pemakaian_id;
        let kategori_id = selectedData.kategori_id;
        let divisiId = $("#kode_department").val();
        let divisiName = $("#kode_department option:selected").text();
        let typeBarang = $("#type_barang").val();

        $('#id').val(account_barang_id);
        $('#divisi_id').val(divisiId);
        $('#barang_id').val(id);
        $('#type').val(typeBarang);
        $("#parentName").val(parentName);
        $('#divisi_name').val(divisiName);
        $("#akun_ap_id").val(ap_id).change();
        $('#akun_ar_id').val(ar_id).change();
        $("#akun_pemakaian_id").val(pemakaian_id).change();
        $('#kategori').val(kategori_id).change();

        if (!ap_id && !ar_id && !pemakaian_id) {
            $('.delete-btn-akun').hide();
        } else {
            $('.delete-btn-akun').show();
        }

        $('.add-modal').modal('show');
    }

    function convertDMYtoYMD(dateStr) {
        if (!dateStr) return "";
        const parts = dateStr.split("/");
        return `${parts[2]}-${parts[1]}-${parts[0]}`;
    }

    function detail(id) {
        let rowData = table.rows().data().toArray();
        let selectedData = rowData.find(row => row.id == id);
        let account_barang_id = selectedData.account_barang_id;
        let parentName = selectedData.spesifikasi;
        let ap_id = selectedData.ap_id;
        let ar_id = selectedData.ar_id;
        let pemakaian_id = selectedData.pemakaian_id;
        let kategori_id = selectedData.kategori_id;
        let saldo_awal_barang_penjualan = selectedData.saldo_awal_barang_penjualan;
        let saldo_awal_barang_pembelian = selectedData.saldo_awal_barang_pembelian;
        let saldo_awal_barang_pemakaian = selectedData.saldo_awal_barang_pemakaian;
        let divisiId = $("#kode_department").val();
        let divisiName = $("#kode_department option:selected").text();
        let typeBarang = $("#type_barang").val();
        let transaksi_date = $("#transaksi_date").val();

        if (!ap_id && !ar_id && !pemakaian_id) {
            Swal.fire({
                icon: 'warning',
                title: 'Akun harus terisi dahulu!',
                confirmButtonText: 'Oke'
            });
            return;
        } else {
            let transaksi2 = convertDMYtoYMD(transaksi_date);

            $('#id_saldo_barang').val(account_barang_id);
            $('#divisi_id_saldo_barang').val(divisiId);
            $('#barang_id_saldo_barang').val(id);
            $('#type_saldo_barang').val(typeBarang);
            $("#parentName_saldo_barang").val(parentName);
            $('#divisi_name_saldo_barang').val(divisiName);
            $('#tanggal_transaksi').val(transaksi2).trigger('change');
            $("#akun_ap_id_saldo_barang").val(ap_id).change();
            $("#saldo_akun_penjualan").val(greatFormatRupiah(saldo_awal_barang_penjualan)).change();
            $('#akun_ar_id_saldo_barang').val(ar_id).change();
            $('#saldo_akun_pembelian').val(greatFormatRupiah(saldo_awal_barang_pembelian)).change();
            $("#akun_pemakaian_id_saldo_barang").val(pemakaian_id).change();
            $("#saldo_akun_pemakaian").val(greatFormatRupiah(saldo_awal_barang_pemakaian)).change();

            console.log(saldo_awal_barang_pembelian, saldo_awal_barang_penjualan, saldo_awal_barang_pemakaian);

            if (saldo_awal_barang_pembelian > 0 || saldo_awal_barang_penjualan > 0 || saldo_awal_barang_pemakaian > 0) {
                $('.delete-btn-saldo').show();
            } else {
                $('.delete-btn-saldo').hide();
            }

            $('.add-saldo-modal').modal('show');
        }
    }

    // Function to handle the Excel file upload using AJAX
    function importExcel() {
        Swal.fire({
            icon: 'question',
            title: 'Import Saldo Barang ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Import Data',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const fileInput = document.getElementById('excelFileInput');
                const file = fileInput.files[0];
                const csrf = $(`[name="${csrfToken}"]`);

                if (file) {
                    const formData = new FormData();
                    formData.append('file', file);

                    $.ajax({
                        url: '<?= base_url("jurnal/import-nilai-barang"); ?>',
                        type: 'POST',
                        data: formData,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            setLoading();
                        },
                        complete: function() {
                            stopLoading();
                        },
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        table.ajax.reload();
                                    }
                                })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error importing file: ' + errorThrown,
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Please select a file to import.',
                        confirmButtonColor: '#4e73df',
                    })
                }
            }
        })
    }

    $(document).ready(function() {

        // ==========================
        // INIT SELECT2 (tanpa AJAX)
        // ==========================
        $('.kode_department').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5",
            allowClear: true,
            width: '100%'
        });

        $('.type_barang').select2({
            placeholder: "Pilih Type Barang",
            theme: "bootstrap-5",
            allowClear: true,
            width: '100%'
        });

        $('.kode_barang').select2({
            placeholder: "Pilih Barang",
            theme: "bootstrap-5",
            allowClear: true,
            width: '100%'
        });

        //CSS SELECT2 FLOATING LABEL
        $('.akun_ar_id, .akun_ap_id, .kategori, .akun_pemakaian_id, .akun_ar_id_saldo_barang, .akun_ap_id_saldo_barang, .akun_pemakaian_id_saldo_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.akun_ar_id, .akun_ap_id, .kategori, .akun_pemakaian_id, .akun_ar_id_saldo_barang, .akun_ap_id_saldo_barang, .akun_pemakaian_id_saldo_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.akun_ar_id, .akun_ap_id, .kategori, .akun_pemakaian_id, .akun_ar_id_saldo_barang, .akun_ap_id_saldo_barang, .akun_pemakaian_id_saldo_barang')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // Akun AR
        $('.akun_ar_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        }).on("select2:open", () => {
            document.querySelector(".select2-container--open .select2-search__field").focus()
        })

        $('.akun_pemakaian_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        }).on("select2:open", () => {
            document.querySelector(".select2-container--open .select2-search__field").focus()
        })

        // Akun AP
        $('.akun_ap_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        }).on("select2:open", () => {
            document.querySelector(".select2-container--open .select2-search__field").focus()
        })

        // kategori
        $('.kategori').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        }).on("select2:open", () => {
            document.querySelector(".select2-container--open .select2-search__field").focus()
        })

        // Akun AR
        $('.akun_ar_id_saldo_barang').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-saldo-modal .modal-content")
        }).on("select2:open", () => {
            document.querySelector(".select2-container--open .select2-search__field").focus()
        })

        $('.akun_pemakaian_id_saldo_barang').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-saldo-modal .modal-content")
        }).on("select2:open", () => {
            document.querySelector(".select2-container--open .select2-search__field").focus()
        })

        // Akun AP
        $('.akun_ap_id_saldo_barang').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-saldo-modal .modal-content")
        }).on("select2:open", () => {
            document.querySelector(".select2-container--open .select2-search__field").focus()
        })

        // $("#tanggal_transaksi").datepicker({
        //     todayHighlight: true,
        //     format: "dd/mm/yyyy",
        //     orientation: "bottom auto",
        //     autoclose: true
        // });

        $("#transaksi_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        // ==========================
        // AJAX FUNCTIONS
        // ==========================
        function loadDivisi() {
            return $.ajax({
                url: '<?= base_url("jurnal/get-divisi"); ?>',
                dataType: 'json'
            });
        }

        function loadBarang(typeBarang) {
            return $.ajax({
                url: '<?= base_url("jurnal/get-barang"); ?>',
                data: {
                    type_barang: typeBarang
                },
                dataType: 'json'
            });
        }

        // ==========================
        // POPULATE FUNCTIONS
        // ==========================
        function populateDivisi(data) {
            let $el = $('.kode_department');

            $el.empty().append(`<option value=""></option>`);

            data.data.forEach(item => {
                $el.append(`<option value="${item.id}">${item.divisi}</option>`);
            });

            $el.trigger('change.select2');
        }

        function populateBarang(data) {
            let $el = $('.kode_barang');

            $el.empty().append(`<option value=""></option>`);

            data.data.forEach(item => {
                $el.append(`<option value="${item.id}">(${item.kode_barang}) ${item.barang_name}</option>`);
            });

            $el.trigger('change.select2');
        }

        // ==========================
        // LOAD DIVISI (awal)
        // ==========================
        loadDivisi().then(res => {
            populateDivisi(res);
        });

        // ==========================
        // ON CHANGE TYPE BARANG → LOAD BARANG
        // ==========================
        $('.type_barang').on('change', function() {

            let typeBarang = $(this).val();

            $('.kode_barang').empty().append(`<option value=""></option>`).trigger('change');

            if (!typeBarang) return;

            loadBarang(typeBarang).then(res => {
                populateBarang(res);
                $('.kode_barang').select2('open'); // auto open
            });
        });


        // ==========================
        // FIX HEIGHT & LABEL
        // ==========================
        $('.kode_department, .type_barang, .kode_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.kode_department, .type_barang, .kode_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px').css('z-index', '1');

        $('.kode_department, .type_barang, .kode_barang')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $("#search").on('click', function() {
            let departmentId = $('#kode_department').val();
            let typeBarang = $('#type_barang').val();
            let kodeBarang = $('#kode_barang').val();
            let transaksi_date = $('#transaksi_date').val();

            if (departmentId && typeBarang && kodeBarang && transaksi_date) {
                table.ajax.reload();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih filter untuk pencarian!',
                    confirmButtonText: 'Oke'
                });
                return;
            }
        })

        $('#btn_import_excel').click(function() {
            $('#file').val(null);
            $('#import_excel_modal').modal('show');
        });

        $('#btn-discard-import-excel').click(function() {
            $('#file').val(null);
            $('#import_excel_modal').modal('hide');
        })

        // hide modal
        $('.btn-discard-akun').click(function() {
            $('.add-modal').modal('hide');
        });

        // action save or update
        $('.btn-submit-form-akun').click(function(e) {
            e.preventDefault();
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
                        let divisi_id = $('#divisi_id').val();
                        let csrf = $(`[name="${csrfToken}"]`);
                        let data = new FormData(document.querySelector(".create-form"));
                        data.append("divisi_id", divisi_id);
                        $.ajax({
                            url: "<?= base_url("tipe-barang/save"); ?>",
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
                                            $('#parentName').val(null);
                                            $(".add-modal").modal("hide")
                                        })
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    }).then(() => {
                                        $('#parentName').val(null);
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
                                }).then(() => {
                                    $(".add-modal").modal("hide")
                                });
                            }
                        });
                    }
                })

            }
        });

        $(".delete-btn-akun").click(function() {
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
                        url: "<?= base_url("tipe-barang/delete"); ?>",
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
                                        $(".add-modal").modal("hide")
                                        table.ajax.reload()
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

        // hide modal
        $('.btn-discard-saldo-barang').click(function() {
            $('.add-saldo-modal').modal('hide');
        });

        // action save or update
        $('.btn-submit-form-saldo-barang').click(function(e) {
            e.preventDefault();
            // Abaikan tanggal
            if ($(".create-saldo-form").valid()) {
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
                        let id = $('input[name="id_saldo_barang"]').val();
                        let divisi_id = $('#divisi_id_saldo_barang').val();
                        let saldo_akun_pembelian = $('#saldo_akun_pembelian').val();
                        let saldo_akun_penjualan = $('#saldo_akun_penjualan').val();
                        let saldo_akun_pemakaian = $('#saldo_akun_pemakaian').val();
                        let csrf = $(`[name="${csrfToken}"]`);
                        let data = new FormData(document.querySelector(".create-saldo-form"));
                        data.append("saldo_akun_pembelian", destroyFormatRupiah(saldo_akun_pembelian));
                        data.append("saldo_akun_penjualan", destroyFormatRupiah(saldo_akun_penjualan));
                        data.append("saldo_akun_pemakaian", destroyFormatRupiah(saldo_akun_pemakaian));
                        $.ajax({
                            url: "<?= base_url("jurnal/save-nilai-barang"); ?>",
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
                                            $(".add-saldo-modal").modal("hide")
                                        })
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    }).then(() => {
                                        $(".add-saldo-modal").modal("hide")
                                    });
                                }
                            },
                            onError: function(response) {
                                csrf.val(response.token);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Data Gagal Disimpan, coba Lagi',
                                    confirmButtonColor: '#4e73df',
                                }).then(() => {
                                    $(".add-saldo-modal").modal("hide")
                                });
                            }
                        });
                    }
                })

            }
        });

        $(".delete-btn-saldo").click(function() {
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
                        url: "<?= base_url("tipe-barang/delete"); ?>",
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
                                        $(".add-modal").modal("hide")
                                        table.ajax.reload()
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
    });
</script>
<?= $this->endSection(); ?>