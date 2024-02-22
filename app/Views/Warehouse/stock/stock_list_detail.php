<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Detail Stok</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("stock-list"); ?>">
                Batal
            </a>

        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Barang</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= strtoupper(str_replace('_', ' ', $detail['barang']['parent_type']))  ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">Tipe Barang</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= $detail['barang']['parent_name'] ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">Kategori Barang</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= $detail['barang']['divisi'] ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">Departemen</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= $detail['barang']['warehouse'] ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">Warehouse</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= $detail['barang']['kode'] . " / " . $detail['barang']['barang'] ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">Kode / Nama Barang</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= $detail['barang']['kode_satuan'] ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">Satuan</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Stok Sekarang</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= number_format($detail['stokInisiasi']['qty']) ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">Stok Awal</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= number_format($detail['stok']['stokMasuk']) ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">Stok Masuk</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= number_format($detail['stok']['stokKeluar']) ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">Stok Keluar</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= number_format($detail['stok']['stokSekarang']) ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">Stok Akhir</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Stok Per Dokumen</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select bc_id_stok_per_dokumen" id="bc_id_stok_per_dokumen" name="bc_id_stok_per_dokumen" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php foreach ($jenisDokAju as $j) : ?>
                                <option value="<?= $j->id ?>">
                                    <?= $j->value ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="0">NON PABEAN</option>

                        </select>
                        <label for="floatingInput" style="z-index: 1;">Dokumen Pabean</label>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search_no_aju_stok_per_dokumen" id="search_no_aju_stok_per_dokumen" name="search_no_aju_stok_per_dokumen" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Nomor Aju </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable stok-dokumen-bc-table" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('bc_id')">Dokumen Pabean</th>
                                <th onclick="changeSort('no_aju')">No Aju</th>
                                <th onclick="changeSort('stok_total')">Stok Satuan 1</th>
                                <th>Stok Satuan 2</th>
                                <th>Stok Satuan 3</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Inisiasi Stok</label>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select bc_id_stok_inisasi" id="bc_id_stok_inisasi" name="bc_id_stok_inisasi" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php foreach ($jenisDokAju as $j) : ?>
                                <option value="<?= $j->id ?>">
                                    <?= $j->value ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="0">NON PABEAN</option>

                        </select>
                        <label for="floatingInput" style="z-index: 1;">Dokumen Pabean</label>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search_no_aju_stok_inisasi" id="search_no_aju_stok_inisasi" name="search_no_aju_stok_inisasi" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Nomor Aju </label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable stok-dokumen-bc-table" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('bc_id')">Dokumen Pabean</th>
                                <th onclick="changeSort('no_aju')">No Aju</th>
                                <th onclick="changeSort('stok_total')">Stok Awal Satuan 1</th>
                                <th>Stok Awal Satuan 2</th>
                                <th>Stok Awal Satuan 3</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Pemasukkan Barang</label>
                </div>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Dari Adjusment</label>
                </div>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Pemasukkan Barang Dari Produksi</label>
                </div>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Pengeluaran Barang Ke Produksi</label>
                </div>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Pengeluaran Barang</label>
                </div>
            </div>


        </div>
    </div>
</section>

<script>
    let sort = "createdAt";
    let sortType = "desc";

    const stokTableDokumenBC = $('.stok-dokumen-bc-table').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
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
            url: "<?= base_url("stock-list/stock-dokumen-bc"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.bc_id = $("#bc_id_stok_per_dokumen option:selected").val();
                data.no_aju = $("#search_no_aju_stok_per_dokumen").val();
                data.stok_id = "<?= encrypt($stok['id']) ?>"
                data.sort = sort;
                data.sortType = sortType;
            },
            beforeSend: function() {
                $.LoadingOverlay("show", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
            complete: function() {
                $.LoadingOverlay("hide", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
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
                searchable: false,
                sortable: false
            },
            {
                data: "bc_type",
                className: "text-center"
            },
            {
                data: "no_aju",
                className: "text-center"
            },
            {
                data: "stok_1",
                className: "text-center"
            },
            {
                data: "stok_2",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "stok_3",
                className: "text-center",
                searchable: false,
                sortable: false
            },

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


    $('#bc_id_stok_per_dokumen').select2({
        placeholder: "Pilih Dokumen Pabean",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        stokTableDokumenBC.ajax.reload();
    });

    $('#bc_id_stok_inisasi').select2({
        placeholder: "Pilih Dokumen Pabean",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {});

    $('#search_no_aju_stok_per_dokumen').change(function() {
        stokTableDokumenBC.ajax.reload();
    });

    $('#search_no_aju_stok_inisasi').change(function() {});


    $("#bc_id_stok_per_dokumen, #bc_id_stok_inisasi")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }
</script>



<?= $this->endSection(); ?>