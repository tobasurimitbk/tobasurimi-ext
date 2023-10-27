<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" id="add_modal">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Supplier Bahan Baku</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" readonly="true" type="text" class="form-control kode" id="kode" name="kode" value="<?= !empty($dataSPP) ? $dataSPP->spp_no : ""; ?>">
                                    <label for="floatingInput">Kode Supplier</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control name" id="name" name="name" placeholder="Nama">
                                <label for="floatingInput">Nama Supplier</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <textarea autocomplete="one-time-code" class="form-control address text-area-all" name="address" id="address" placeholder="Alamat (Opsional)"></textarea>
                                <label for="floatingInput">Alamat (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select country_code" name="country_code" id="country_code">
                                    <option value=""></option>
                                    <?php foreach ($country as $c) : ?>
                                        <option value="<?= $c->code; ?>">
                                            <?= $c->country_name; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Negara (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select province_parent_id" name="province_parent_id" id="province_parent_id" onchange="getCityParent()">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($dataProvinces)) {
                                        foreach ($dataProvinces as $province) {
                                    ?>
                                            <option value="<?= $province->id; ?>"><?= $province->province_name; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Provinsi (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select city_parent_id" name="city_parent_id" id="city_parent_id" onchange="getPostalCodeParent()">
                                    <option value="" data-code=""></option>
                                </select>
                                <label for="floatingInput">Kota (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control parent_postal_code" id="parent_postal_code" name="postal_code" placeholder="Kode Pos (Opsional)">
                                <label for="floatingInput">Kode Pos (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control no_npwp" id="no_npwp" name="no_npwp" placeholder="NPWP (Opsional)">
                                <label for="floatingInput">NPWP (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control phone" id="phone" name="phone" placeholder="No. Telepon (Opsional)">
                                <label for="floatingInput">No. Telepon (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control contact_person" id="contact_person" name="contact_person" placeholder="Contact Person (Opsional)">
                                <label for="floatingInput">Contact Person (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control account_receivable" id="account_receivable" name="account_receivable" placeholder="Akun Receivable (Opsional)">
                                <label for="floatingInput">Akun Receivable (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control account_payable" id="account_payable" name="account_payable" placeholder="Akun Payable (Opsional)">
                                <label for="floatingInput">Akun Payable (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="email" class="form-control email" id="email" name="email" placeholder="Email">
                                <label for="floatingInput">Email (Opsional)</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-hide-parent btn-discard mr-2">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-parent">Simpan</button>
                <button type="button" class="btn btn-discard delete-form delete-btn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<div class="modal laporan-modal" id="laporan-modal">
    <div class="modal-dialog" style="min-width: 500px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Laporan</h5>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select laporan_id" name="laporan_id" id="laporan_id">
                            <option value="" data-code=""></option>
                            <option value="laporan-pendapatan-supplier" data-code="">Pendapatan Supplier</option>
                            <option value="laporan-rincian-per-barang" data-code="">Rincian Per Barang</option>
                            <option value="laporan-rekap-all-supplier" data-code="">Rekap All Supplier</option>
                            <option value="laporan-rekap-per-supplier" data-code="">Rekap Per Supplier</option>
                            <option value="laporan-rekap-all-barang" data-code="">Rekap All Barang</option>
                            <option value="laporan-rekap-per-barang" data-code="">Rekap Per Barang</option>
                            <option value="laporan-bukti-penerimaaan-barang" data-code="">Bukti Penerimaan Barang</option>
                            <option value="laporan-kwitansi-tb" data-code="">Kwitansi TB</option>
                        </select>
                        <label for="floatingInput">Pilih Laporan</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="laporan-pendapatan-supplier">
                        <h6>Laporan Pendapatan Supplier</h6>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input autocomplete="one-time-code" class="form-control input-picker awal_date" id="awal_date" name="awal_date" placeholder="Tanggal Pemesanan">
                                            <label for="floatingInput">Tanggal Awal</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input autocomplete="one-time-code" class="form-control input-picker akhir_date" id="akhir_date" name="akhir_date" placeholder="Tanggal Pemesanan">
                                            <label for="floatingInput">Tanggal Akhir</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <select class="form-select supplier_id" id="supplier_id" name="supplier_id" aria-label="Floating label select example">
                                                <option value=""></option>
                                                <?php
                                                if (!empty($dataSuppliers)) : ?>
                                                    <?php foreach ($dataSuppliers as $supplier) : ?>
                                                        <option value="<?= $supplier->id; ?>" data-name="<?= $supplier->name; ?>"><?= $supplier->name; ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <label for="floatingInput">Supplier</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <select class="form-select barang_id" id="barang_id" name="barang_id" aria-label="Floating label select example">
                                                <option value=""></option>
                                                <?php
                                                if (!empty($dataBarangMasters)) : ?>
                                                    <?php foreach ($dataBarangMasters as $barang) : ?>
                                                        <option value="<?= $barang->id; ?>" data-name="<?= $barang->barang_name; ?>"><?= $barang->barang_name; ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <label for="floatingInput">Barang</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <select class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">
                                                <option value=""></option>
                                                <?php
                                                if (!empty($dataWarehouses)) : ?>
                                                    <?php foreach ($dataWarehouses as $warehouses) : ?>
                                                        <option value="<?= $warehouses->id; ?>" data-name="<?= $warehouses->warehouse_name; ?>"><?= $warehouses->warehouse_name; ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <label for="floatingInput">Warehouse</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="laporan-rincian-per-barang">
                        Laporan Rincian Per Barang
                    </div>
                    <div class="laporan-rekap-all-supplier">
                        Laporan Rekap All Supplier
                    </div>
                    <div class="laporan-rekap-per-supplier">
                        Laporan Rekap Per Supplier
                    </div>
                    <div class="laporan-rekap-all-barang">
                        Laporan Rekap All Barang
                    </div>
                    <div class="laporan-rekap-per-barang">
                        Laporan Rekap Per Barang
                    </div>
                    <div class="laporan-bukti-penerimaaan-barang">
                        Laporan Bukti Penerimaan Barang
                    </div>
                    <div class="laporan-kwitansi-tb">
                        Laporan Kwitansi TB
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div id="laporan-buttons">

                </div>
                <button type="button" class="btn btn-hide-form btn-hide-laporan btn-discard mr-2">Batal</button>
            </div>
        </div>
    </div>
</div>

<div class="modal harga-modal" id="harga_modal">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">History Bahan Baku</h5>
            </div>
            <div class="modal-body">
                <div class="row justify-content-end mb-3">
                    <div class="col-md-3">
                        <input autocomplete="one-time-code" class="form-control search-harga form-out-search" placeholder="Cari Nama Barang" value="" />
                    </div>
                </div>
                <div class="table-responsive mt-3 mb-3">
                    <table class="table-inside table-borderd nowrap table-hover-tobasurimi secondDataTable" width="100%" cellspacing="0" id="secondDataTable">
                        <thead class="thead-dark">
                            <tr>
                                <th width="10">No</th>
                                <th onclick="changeSortHarga('bahan_baku_name')" class="sort">Barang</th>
                                <th onclick="changeSortHarga('createdAt')" class="sort">Tgl</th>
                                <th onclick="changeSortHarga('spesifikasi')" class="sort">Spesifikasi</th>
                                <th onclick="changeSortHarga('bagian')" class="sort">Bagian</th>
                                <th onclick="changeSortHarga('harga_umum')" class="sort">Umum</th>
                                <th onclick="changeSortHarga('harga_harian')" class="sort">Harian</th>
                                <th onclick="changeSortHarga('harga_bulanan')" class="sort">Bulanan</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-hide-harga btn-discard mr-2">Batal</button>
            </div>
        </div>
    </div>
</div>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Supplier Bahan Baku</h1>
        <div class="col-button-tambah-spp">
            <button class="btn btn-show-form-laporan btn-warning btn-print float-right" target="_blank" style="color: #ffffff;" data-btn="laporan-modal">
                <i class="fa fa-print mr-2"></i>Print
            </button>
            <button class="btn btn-show-form btn-save float-right" data-btn="create-modal">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end mb-3">
                <div class="col-md-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari Kode / Nama" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable firstDataTable" id="firstDataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th onclick="changeSort('kode')" class="sort">Kode</th>
                                <th onclick="changeSort('name')" class="sort">Nama</th>
                                <th onclick="changeSort('no_npwp')" class="sort">NPWP</th>
                                <th onclick="changeSort('address')" class="sort">Alamat</th>
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

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "kode";
    let sortType = "desc";
    let sortHarga = "createdAt";
    let sortTypeHarga = "desc";
    let trigger = true;
    let id_supplier = "";

    $(document).ready(function() {
        $("#laporan_id").change(function() {
            var selectedOption = $(this).val();
            var dynamicButtons = $("#laporan-buttons");

            dynamicButtons.empty();

            if (selectedOption) {
                dynamicButtons.append('<a href="<?= base_url("/supplier-bahan-baku/print"); ?>" target="_blank" class="btn btn-submit-form btn-submit-' + selectedOption + ' mr-2">Tampil</a>');
            }

            // Sembunyikan semua div yang terkait dengan laporan
            $(".col-md-12 > div[class^='laporan-']").hide();

            // Tampilkan div yang sesuai dengan pilihan saat ini
            if (selectedOption) {
                $("." + selectedOption).show();
                $(".btn-submit-" + selectedOption).show();
            }
        });
    });

    $(".awal_date, .akhir_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })


    $('.laporan_id').select2({
        placeholder: "",
        theme: "bootstrap-5"
    })

    $('.province_parent_id').select2({
        placeholder: "",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    })

    $('.city_parent_id').select2({
        placeholder: "",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    })

    $('.country_code').select2({
        placeholder: "",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    });

    $('.supplier_id').select2({
        placeholder: "",
        theme: "bootstrap-5",
    });

    $('.barang_id').select2({
        placeholder: "",
        theme: "bootstrap-5",
    });

    $('.warehouse_id').select2({
        placeholder: "",
        theme: "bootstrap-5",
    });

    $('.country_code').select2({
        placeholder: "",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    });

    $('.country_code').select2({
        placeholder: "",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    });

    //CSS SELECT2 FLOATING LABEL
    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.form-select')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    let table = $('#firstDataTable').DataTable({
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
            url: "<?= base_url("supplier-bahan-baku/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.sort = sort;
                data.sortType = sortType;
            }
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
        }, {
            data: "kode",
            className: "text-center"
        }, {
            data: "name",
            className: "text-center"
        }, {
            data: "no_npwp",
            className: "text-center"
        }, {
            data: "address",
            className: "text-center"
        }, {
            data: "id",
            className: "text-center actions",
            searchable: false,
            sortable: false,
            render: function(data, type, row) {
                let id = row?.id;
                return `
                        <a class="btn btn-warning" href="<?= base_url(); ?>supplier-bahan-baku/harga/${id}" style="box-shadow: none !important;">
                            Set Harga
                        </a>
                        <button class="btn btn-success" onclick="History(${id})" style="box-shadow: none !important;">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </a>
                    `
            }
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

    let secondTable = $('#secondDataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [2, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("supplier-harga/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.id = id_supplier;
                data.search = $(".search-harga").val();
                data.sort = sortHarga;
                data.sortType = sortTypeHarga;
            }
        },
        // scrollX: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.secondDataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        //responsive: true,
        display: "stripe",
        searching: false,
        columns: [{
            data: "no",
            className: "text-center",
            sortable: false
        }, {
            data: "bahan_baku_name",
            className: "text-center"
        }, {
            data: "createdAt",
            className: "text-center"
        }, {
            data: "spesifikasi",
            className: "text-center"
        }, {
            data: "bagian",
            className: "text-center"
        }, {
            data: "harga_umum",
            className: "text-center"
        }, {
            data: "harga_harian",
            className: "text-center"
        }, {
            data: "harga_bulanan",
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

    $(document).ready(function() {
        var validator = $(".create-form").validate({
            rules: {
                kode: {
                    required: true
                },
                name: {
                    required: true
                },
                no_npwp: {
                    minlength: 15,
                    maxlength: 15,
                }
            },
            messages: {
                kode: {
                    required: "Kode wajib diisi"
                },
                name: {
                    required: "Nama wajib diisi"
                },
                no_npwp: {
                    minlength: "Nomor NPWP minimal 15 angka",
                    maxlength: "Nomor NPWP maksimal 15 angka",
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

        $(".no_npwp").mask("000000000000000")

        $(".phone").mask("0000000000000")

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $(".search-harga").keyup(function() {
            secondTable.ajax.reload();
        })

        $(".dataTable_info").addClass("pt-0");

        $(".btn-show-form-laporan").click(function() {
            $(".laporan-modal").show();
            $(".col-md-12 > div[class^='laporan-']").hide();
            $(".modal-footer .btn-submit-form").hide();
            // $('.laporan-rincian-per-barang').hide();

        })


        $(".btn-show-form").click(function() {
            $(".id").val("");
            $(".title-name").text("Tambah");

            validator.resetForm();
            validator.reset();

            $(".create-form")[0].reset()
            $(".delete-form").css('display', 'none');

            $(".province_parent_id").val('').change()
            $(".city_parent_id").val('').change()
            $(".city_parent_id").empty()
            $(".city_parent_id").append(`<option value=""></option>`)
            $(".country_code").val('').change()

            $.ajax({
                url: "<?= base_url("supplier/generate"); ?>",
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res?.status) {
                        $(".kode").val(res?.data)
                        $(".add-modal").modal("show");
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            })
        })

        $(".btn-hide-parent").click(function() {
            $(".add-modal").modal("hide")
        })

        $(".btn-hide-harga").click(function() {
            $(".harga-modal").modal("hide")
        })

        $(".btn-hide-laporan").click(function() {
            $(".laporan-modal").hide()
            $(".laporan_id").val("").change()
        })

        $('#firstDataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            $(".create-form")[0].reset()
            $(".delete-form").css('display', '');
            let id = data.id;
            $(".title-name").text("Update");

            $.ajax({
                url: "<?= base_url("supplier/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        validator.resetForm();
                        validator.reset();

                        $(".id").val(id);
                        $(".kode").val(res?.data?.kode);
                        $(".name").val(res?.data?.name);
                        $(".address").val(res?.data?.address);
                        $(".no_npwp").val(res?.data?.no_npwp);
                        $(".phone").val(res?.data?.phone);
                        $(".contact_person").val(res?.data?.contact_person);
                        $(".email").val(res?.data?.email);
                        $(".province_parent_id").val(res?.data?.province_id).change();
                        $(".country_code").val(res?.data?.country_code).change();
                        $(".account_receivable").val(res?.data?.account_receivable);
                        $(".account_payable").val(res?.data?.account_payable);

                        // AJAX GET CITY
                        $.ajax({
                            url: `<?= base_url("city"); ?>/${res?.data?.province_id}`,
                            method: "GET",
                            dataType: "json",
                            success: function(result) {
                                $(".city_parent_id").empty()
                                $(".city_parent_id").val("").change()
                                $(".city_parent_id").append(`<option value=""></option>`)
                                result.data.forEach(function(item) {
                                    $(".city_parent_id").append(`<option value="${item.id}" data-code="${item.postal_code}">${item.city_name}</option>`)
                                })

                                $(".city_parent_id").val(res?.data?.city_id).change();
                                $(".parent_postal_code").val(res?.data?.postal_code);
                            }
                        })

                        $(".add-modal").modal("show");

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            })
        })

        $(".delete-form").click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Hapus Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    let id = $(".id").val();
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("supplier/delete"); ?>",
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
                                    })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                stopLoading()
                            }
                        },
                        onError: function(response) {
                            csrf.val(response.token);
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Gagal Disimpan, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            })
                            stopLoading()
                        }
                    });
                }
            })
        })

        $(".btn-submit-parent").click(function() {
            if ($(".create-form").valid()) {
                Swal.fire({
                    icon: 'question',
                    title: 'Simpan Data?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const csrf = $(`[name="${csrfToken}"]`);
                        let data = new FormData(document.querySelector(".create-form"));
                        let id = $(".id").val();

                        $.ajax({
                            url: id ? "<?= base_url("supplier-bahan-baku/update"); ?>" : "<?= base_url("supplier-bahan-baku/save"); ?>",
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
                                    stopLoading()
                                    Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        .then(() => {
                                            $(".add-modal").modal("hide")
                                            table.ajax.reload()
                                        })
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                    stopLoading()
                                }
                            },
                            onError: function(response) {
                                csrf.val(response.token);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Data Gagal Disimpan, coba Lagi',
                                    confirmButtonColor: '#4e73df',
                                })
                                stopLoading()
                            }
                        });
                    }
                })
            }
        })
    })

    const getCityParent = function() {
        const id = $(".province_parent_id option:selected").val()

        if (id) {
            $.ajax({
                url: `<?= base_url("city"); ?>/${id}`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".city_parent_id").empty()
                    $(".city_parent_id").val("").change()
                    $(".city_parent_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".city_parent_id").append(`<option value="${item.id}" data-code="${item.postal_code}">${item.city_name}</option>`)
                    })
                }
            })
        }
    }

    const getPostalCodeParent = function() {
        $(".parent_postal_code").val($(".city_parent_id option:selected").attr("data-code"))
    }

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    const changeSortHarga = function(val) {
        if (sortHarga !== val) {
            sortTypeHarga = "asc";
            sortHarga = val;
        } else {
            sortTypeHarga = sortTypeHarga === "asc" ? "desc" : "asc";
        }
    }

    let History = function(id) {
        id_supplier = id;
        $(".search-harga").val('')
        sortHarga = "createdAt";
        sortTypeHarga = "desc";
        secondTable.ajax.reload()
        $(".harga-modal").modal("show")
    }
</script>

<?= $this->endSection(); ?>