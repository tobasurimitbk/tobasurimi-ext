<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($mutasiGlobal) ? "Tambah Mutasi BC 2.7" : "Update Mutasi BC 2.7" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("mutasi/global"); ?>">
                Batal
            </a>
            <?php if (!empty($mutasiGlobal)) : ?>
                <?php if ($mutasiGlobal['status_posting'] == "0") : ?>
                    <?php if (can('Inventori', 'Mutasi', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Inventori', 'Mutasi', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-mutasi">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Inventori', 'Mutasi', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else : ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">

            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('mutasi/create') ?>">Mutasi PPBKB</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#">Mutasi BC 2.7</a>
                </li>
            </ul>

            <div class="row mt-3">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Mutasi</label>
                </div>
            </div>
            <form class="create-form">
                <input type="hidden" name="id" id="id" value="<?= !empty($mutasiGlobal) ? encrypt($mutasiGlobal['id']) : '' ?>" class="id">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" disabled class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime(!empty($mutasiGlobal) ? $mutasiGlobal['tanggal'] : $tanggal)); ?>">
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" <?= !empty($mutasiGlobal) ? 'disabled=true' : ''; ?> value="<?= !empty($mutasiGlobal) ? $mutasiGlobal['no_mutasi'] : "BC27//" . date('m') . "/1/" . date('Y'); ?>" type="text" class="form-control no_mutasi" id="no_mutasi" name="no_mutasi" placeholder="No. Adjusment">
                                    <label for="floatingInput">No. Mutasi</label>
                                </div>
                                <div style="<?= !empty($mutasiGlobal) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 20px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled placeholder="Company Asal" value="<?= $companyAsalName ?>" class="form-control" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Company Asal</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($mutasiGlobal) ? ($mutasiGlobal['status_posting'] ? 'disabled' : '') : '' ?> class="form-select divisi_asal_id" id="divisi_asal_id" name="divisi_asal_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= !empty($mutasiGlobal) ? ($mutasiGlobal['divisi_asal_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen Asal</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($mutasiGlobal) ? ($mutasiGlobal['status_posting'] ? 'disabled' : '') : '' ?> class="form-select warehouse_asal_id" id="warehouse_asal_id" name="warehouse_asal_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($warehouseAsal)) : ?>
                                    <?php foreach ($warehouseAsal as $w) : ?>
                                        <option <?= !empty($mutasiGlobal) ? ($mutasiGlobal['warehouse_asal_id'] == $w['id'] ? 'selected' : '') : '' ?> value="<?= $w['id'] ?>">
                                            <?= $w['warehouse_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Warehouse Asal</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($mutasiGlobal) ? ($mutasiGlobal['status_posting'] ? 'disabled' : '') : '' ?> class="form-select company_tujuan_id" id="company_tujuan_id" name="company_tujuan_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dropdownCompanyExcept as $d) : ?>
                                    <option <?= !empty($mutasiGlobal) ? ($mutasiGlobal['company_tujuan_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= strtoupper($d['company']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Company Tujuan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($mutasiGlobal) ? ($mutasiGlobal['status_posting'] ? 'disabled' : '') : '' ?> class="form-select type_pengambilan_stock" id="type_pengambilan_stock" name="type_pengambilan_stock" aria-label="Floating label select example">
                                <option value=""></option>
                                <option <?= !empty($mutasiGlobal) ? ($mutasiGlobal['tipe_pengambilan_stock'] == "PABEAN" ? 'selected' : '') : '' ?> value="PABEAN">PABEAN</option>
                                <option <?= !empty($mutasiGlobal) ? ($mutasiGlobal['tipe_pengambilan_stock'] == "FIFO" ? 'selected' : '') : '' ?> value="FIFO">FIFO</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tipe Pengambilan Stok</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($mutasiGlobal) ? ($mutasiGlobal['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Keterangan" value="<?= !empty($mutasiGlobal) ? $mutasiGlobal['keterangan'] : '' ?>" class="form-control keterangan" id="keterangan" name="keterangan" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Keterangan (Opsional)</label>
                        </div>
                    </div>
                </div>

            </form>
            <div class="detail-form-layout">
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Data Barang</label>
                    </div>
                </div>
                <form class="detail-form">
                    <input type="hidden" name="id_detail" class="id_detail" id="id_detail">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select type_barang" id="type_barang" name="type_barang" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($tipeBarang as $t) : ?>
                                        <option value="<?= $t['description'] ?>">
                                            <?= strtoupper($t['value']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Tipe Barang</label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select spesifikasi_id" id="spesifikasi_id" name="spesifikasi_id" aria-label="Floating label select example">
                                    <option value=""></option>

                                </select>
                                <label for="floatingInput" style="z-index: 1;">Barang - Spesifikasi</label>
                            </div>
                        </div>
                        <div class="col-md-4 form-fifo">
                            <div class="form-floating" style="height: 50px;">
                                <input placeholder="Qty" oninput="preventNegativeInput(this)" class="form-control qty_mutasi_fifo" id="qty_mutasi_fifo" name="qty_mutasi_fifo" aria-label="Floating label select example" />
                                <label for="floatingInput" style="z-index: 1;">Qty Mutasi Keluar</label>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="row mt-3">
                    <div class="col mb-0">
                        <label class="form-label font-weight-bold lable-title">List Inventori Asal Barang</label>
                    </div>
                    <div class="col-md-12 col-table-button-tts">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: center;">Asal Barang</th>
                                        <th style="text-align: center;">No Dokumen</th>
                                        <th style="text-align: center;">Supplier</th>
                                        <th style="text-align: center;">Dokumen Pabean</th>
                                        <th style="text-align: center;">No Aju</th>
                                        <th style="text-align: center;">Tanggal Penerimaan</th>
                                        <th style="text-align: center;">Barang - Spesifikasi</th>
                                        <th style="text-align: center;">Satuan</th>
                                        <th style="text-align: center;">Qty</th>
                                    </tr>
                                </thead>
                                <tbody class="body-table">
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary" id="select-item-btn">Pilih</button>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Daftar Barang Yang Akan Dipindahkan</label>
                </div>
                <div class="col-md-12 mb-5">
                    <div class="table-responsive" style="margin-top: -10px;">
                        <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts" id="selectedItemTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">Tipe Barang</th>
                                    <th style="text-align: center;">Asal Barang</th>
                                    <th style="text-align: center;">No Dokumen</th>
                                    <th style="text-align: center;">Supplier</th>
                                    <th style="text-align: center;">Dokumen Pabean</th>
                                    <th style="text-align: center;">Tgl Penerimaan</th>
                                    <th style="text-align: center;">Barang - Spesifikasi</th>
                                    <th style="text-align: center;">Satuan</th>
                                    <th style="text-align: center;">Qty Asal</th>
                                    <th style="text-align: center;">Qty Mutasi</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="body-table">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>

    </div>
</section>



<script>
    var listStockAsal = [];
    var listStockSelected = [];

    $('.form-fifo').hide();

    <?php if (!empty($mutasiGlobalDetail)) : ?>
        <?php foreach ($mutasiGlobalDetail as $m) :  ?>
            listStockSelected.push({
                id: "<?= $m['id'] ?>",
                bc_id: "<?= $m['bc_id'] ?>",
                stock_detail_id: "<?= $m['stock_detail_id'] ?>",
                no_aju: "<?= $m['no_aju'] ?>",
                stock_id: "<?= $m['stock_id'] ?>",
                stok_total: "<?= $m['stok_total'] ?>",
                bc_type: "<?= $m['bc_type'] ?>",
                satuan: "<?= $m['satuan'] ?>",
                barang: "<?= $m['barang'] ?>",
                type_barang: "<?= $m['type_barang'] ?>",
                type_barang_text: "<?= $m['type_barang_text'] ?>",
                stock_date: "<?= $m['stock_date'] ?>",
                qty: "<?= $m['qty'] ?>",
                supplier_name: "<?= $m['supplier_name'] ?>",
                stock_dokumen: "<?= $m['stock_dokumen'] ?>",
                sumber: "<?= $m['sumber'] ?>"
            });
        <?php endforeach; ?>
        <?php if ($mutasiGlobal['status_posting']) : ?>
            $('.detail-form-layout').hide()
        <?php endif; ?>
        <?php if ($mutasiGlobal['tipe_pengambilan_stock'] == "FIFO") : ?>
            $('.form-fifo').show();
        <?php endif; ?>
    <?php endif; ?>

    var qtyTotal = 0;
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    var dataTable = $('#dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: false,
        serverSide: false,
        ordering: true,
        order: [],
        fixedHeader: true,
        "initComplete": function(settings, json) {
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

    <?php if (!empty($mutasiGlobal)) : ?>
        if ($.fn.DataTable.isDataTable('#selectedItemTable')) {
            $('#selectedItemTable').DataTable().clear().draw();
            selectedItemTable.destroy();
        }
        var selectedItemTable = $('#selectedItemTable').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
            processing: false,
            serverSide: false,
            ordering: true,
            order: [],
            fixedHeader: true,
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            display: "stripe",
            searching: false,
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });
        drawTableSelectedItem(listStockSelected);

    <?php else : ?>
        var selectedItemTable = $('#selectedItemTable').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
            processing: false,
            serverSide: false,
            ordering: true,
            order: [],
            fixedHeader: true,
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            display: "stripe",
            searching: false,
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });
    <?php endif; ?>

    $('#type_pengambilan_stock').select2({
        placeholder: "Pilih Tipe Ambil Stok",
        theme: "bootstrap-5",
    }).change(function() {
        // FIFO
        if ($(this).val() == "FIFO") {
            $('.form-fifo').show();
        } else {
            $('.form-fifo').hide();
        }
        $('#spesifikasi_id').val(null).change();
        listStockAsal = [];
        listStockSelected = [];
        drawTableAsalBarang();
        drawTableSelectedItem();
    });

    $('#company_tujuan_id').select2({
        placeholder: "Pilih Company Tujuan",
        theme: "bootstrap-5",
        allowClear: true
    })

    $('#warehouse_tujuan_id').select2({
        placeholder: "Pilih Warehouse Tujuan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {});


    $('#warehouse_asal_id').select2({
        placeholder: "Pilih Warehouse Asal",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // RESET TYPE BARANG
        $('#type_barang').val(null).change();
        // RESET SEMUA LIST
        listStockAsal = [];
        listStockSelected = [];
        drawTableAsalBarang(listStockAsal);
        drawTableSelectedItem(listStockSelected);
    });

    $('#divisi_asal_id').select2({
        placeholder: "Pilih Departemen Asal",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // CARI DIVISI TUJUAN
        getListDivisiTujuan();
        // CARI WAREHOUSE ASAL
        getListWarehouseAsal()
        // RESET TYPE BARANG
        $('#type_barang').val(null).change();
        // GET NO MUTASI
        changeStatus();
        // RESET SEMUA LIST
        listStockAsal = [];
        listStockSelected = [];
        drawTableAsalBarang(listStockAsal);
        drawTableSelectedItem(listStockSelected);
    });

    $('#divisi_tujuan_id').select2({
        placeholder: "Pilih Departemen Tujuan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // CARI WAREHOUSE TUJUAN
        getListWarehouseTujuan();
    });

    $('#type_barang').select2({
        placeholder: "Pilih Tipe Barang",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        //CARI BARANG
        getListBarang();
        // RESET STOK LIST
        listStockAsal = [];
        drawTableAsalBarang(listStockAsal);
    });

    $('#spesifikasi_id').select2({
        placeholder: "Pilih Barang (Hanya Barang Yang Sudah Inisiasi Stok)",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        getListDokumenPabean();
    });


    $("#type_barang,#divisi_asal_id,#divisi_tujuan_id,#warehouse_asal_id,#warehouse_tujuan_id,#spesifikasi_id,#bc_id,#no_aju,#operasi,#type_pengambilan_stock,#company_tujuan_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('#select-item-btn').click(function() {
        var typePengambilanStock = $('#type_pengambilan_stock option:selected').val();
        if (typePengambilanStock == "FIFO") {
            insertListFifo();
        } else {
            insertListPabean();
        }

    });

    function insertListPabean() {
        var checkedCheckboxes = $(".child:checked");
        var dataIds = checkedCheckboxes.map(function() {
            return $(this).data("id");
        }).get();
        var id_selected = getIDListDataSelected();

        $.each(listStockAsal, function(i, v) {
            var currentID = Number(v.id);

            if ($.inArray(currentID, dataIds) !== -1) {
                var isIDSelected = $.grep(listStockSelected, function(item) {
                    return item.id == Number(currentID);
                }).length > 0;

                if (!isIDSelected) {
                    listStockAsal[i].stok_total = parseFloat(listStockAsal[i].stok_total);
                    listStockAsal[i].qty = 0;
                    listStockSelected.push(listStockAsal[i]);
                }
            }
        });
        drawTableSelectedItem(listStockSelected);
    }

    function insertListFifo() {
        var dataIds = getIDListDataSelected();
        var qtyMutasiFifo = parseFloat($('#qty_mutasi_fifo').val());
        var stockID = $(".spesifikasi_id option:selected").data('stock_id');

        if (isNaN(qtyMutasiFifo)) {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan : Qty Mutasi Keluar Wajib Diisi',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                reverseButtons: true,
                confirmButtonText: 'Oke',
            })
        } else {
            var totalStokTotal = 0;
            $.each(listStockAsal, function(i, v) {
                totalStokTotal += parseFloat(v.stok_total);
            });
            deleteByStockID(stockID);

            if (qtyMutasiFifo > totalStokTotal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan : Stok barang tidak cukup !',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    reverseButtons: true,
                    confirmButtonText: 'Oke',
                })
            } else {
                $.each(listStockAsal, function(i, v) {
                    var currentID = Number(v.id);
                    if ($.inArray(currentID, dataIds) == -1) {
                        var isIDSelected = $.grep(listStockSelected, function(item) {
                            return item.id == Number(currentID);
                        }).length > 0;
                        if (!isIDSelected && qtyMutasiFifo != 0 && parseFloat(listStockAsal[i].stok_total) != 0) {
                            var mutasiQty = Math.min(qtyMutasiFifo, parseFloat(listStockAsal[i].stok_total));
                            listStockAsal[i].stok_total = parseFloat(listStockAsal[i].stok_total);
                            listStockAsal[i].qty = parseFloat(mutasiQty.toFixed(2));
                            listStockSelected.push(listStockAsal[i]);
                            qtyMutasiFifo = qtyMutasiFifo - mutasiQty;
                        }
                    }
                });
            }
        }
        drawTableSelectedItem(listStockSelected);
    }

    function deleteByStockID(stockID) {
        listStockSelected = listStockSelected.filter(function(item) {
            return item.stock_id != stockID;
        });
    }

    // VALIDATOR
    var validator = $(".create-form").validate({
        rules: {
            no_mutasi: {
                required: true
            },
            divisi_asal_id: {
                required: true
            },
            warehouse_asal_id: {
                required: true
            },
            divisi_tujuan_id: {
                required: true
            },
            warehouse_tujuan_id: {
                required: true,
            },
            type_pengambilan_stock: {
                required: true
            },
            company_tujuan_id: {
                required: true
            }
        },
        messages: {
            no_mutasi: {
                required: "No mutasi wajib diisi"
            },
            divisi_asal_id: {
                required: "Departemen asal wajib diisi"
            },
            warehouse_asal_id: {
                required: "Warehouse asal wajib diisi"
            },
            divisi_tujuan_id: {
                required: "Departemen tujuan wajib diisi"
            },
            warehouse_tujuan_id: {
                required: "Warehouse tujuan wajib diisi",
            },
            type_pengambilan_stock: {
                required: "Tipe ambil stok wajib diisi"
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


    $('.btn-submit-parent').click(function() {
        if (listStockSelected.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Barang yang akan dimutasi tidak boleh kosong !',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        } else {
            var typePengambilanStock = $('#type_pengambilan_stock option:selected').val();
            if ($('.create-form').valid()) {
                var isValid = true;
                var dataError = null;

                if (typePengambilanStock == "PABEAN") {
                    $.each(listStockSelected, function(i, v) {
                        var element = $('input[data-id="' + v.id + '"].stok-mutasi');
                        var input_user = parseFloat(element.val());
                        var stok_max = parseFloat(element.data('stok_total'));

                        if (input_user > stok_max || isNaN(input_user) || input_user == undefined || input_user == 0) {
                            dataError = listStockSelected[i];
                            isValid = false;
                        } else {
                            listStockSelected[i].qty = input_user;
                        }
                    });
                }

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Stok mutasi barang ' + dataError.barang + ' dengan dokumen ' + dataError.bc_type + ' / ' + dataError.no_aju + ' tidak valid!',
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                } else {
                    Swal.fire({
                        icon: 'question',
                        title: 'Simpan Data ?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let id = $('#id').val();
                            let data = new FormData(document.querySelector(".create-form"));
                            data.append('listMutasi', JSON.stringify(listStockSelected));
                            if (id) {
                                // UPDATE
                                $.ajax({
                                    url: "<?= base_url("mutasi/update-global"); ?>",
                                    data: data,
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                        setLoading();
                                    },
                                    complete: function() {
                                        stopLoading()
                                    },
                                    method: "POST",
                                    dataType: "json",
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                            confirmButtonText: 'Ok'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = "<?= base_url("mutasi/global"); ?>";
                                            }
                                        });
                                    },
                                });
                            } else {
                                // INSERT
                                $.ajax({
                                    url: "<?= base_url("mutasi/save-global"); ?>",
                                    data: data,
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                        setLoading();
                                    },
                                    complete: function() {
                                        stopLoading()
                                    },
                                    method: "POST",
                                    dataType: "json",
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                            confirmButtonText: 'Ok'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = "<?= base_url("mutasi/global"); ?>";
                                            }
                                        });
                                    },
                                });
                            }
                        }
                    });
                }

            }
        }
    });


    function deleteDetail(id) {
        var indexToRemove = -1;
        for (var i = 0; i < listStockSelected.length; i++) {
            if (listStockSelected[i].id == id) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listStockSelected.splice(indexToRemove, 1);
            drawTableSelectedItem(listStockSelected);
        }
    }

    function preventNegativeInput(inputElement) {
        var inputValue = inputElement.value;
        var numericValue = inputValue.replace(/[^0-9.]/g, '');
        numericValue = numericValue.replace(/^0+/g, '');
        numericValue = numericValue.replace(/^\./g, '0.');
        if (parseFloat(numericValue) < 0 || isNaN(parseFloat(numericValue))) {
            inputElement.value = '0';
        } else {
            inputElement.value = numericValue;
        }
    }

    function getListDivisiTujuan() {
        // GET LIST DIVISI TUJUAN
        $.ajax({
            url: `<?= base_url('mutasi/list-divisi-except'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $(".divisi_asal_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".divisi_tujuan_id").empty()
                $(".divisi_tujuan_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".divisi_tujuan_id").append(`<option value="${item.id}">${item.divisi}</option>`)
                })
            }
        });
    }

    function getListWarehouseAsal() {
        // GET LIST WAREHOUSE ASAL
        $.ajax({
            url: `<?= base_url('mutasi/warehouse'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $(".divisi_asal_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_asal_id").empty()
                $(".warehouse_asal_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_asal_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
            }
        });
    }

    function getListWarehouseTujuan() {
        // GET LIST WAREHOUSE TUJUAN
        $.ajax({
            url: `<?= base_url('mutasi/warehouse'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $(".divisi_tujuan_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_tujuan_id").empty()
                $(".warehouse_tujuan_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_tujuan_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
            }
        });
    }

    function getListBarang() {
        // GET LIST BARANG
        $.ajax({
            url: `<?= base_url('mutasi/list-barang-stock-init'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                type_barang: $(".type_barang option:selected").val(),
                divisi_id: $(".divisi_asal_id option:selected").val(),
                warehouse_id: $(".warehouse_asal_id option:selected").val()
            },
            dataType: "json",
            success: function(res) {
                $(".spesifikasi_id").empty()
                $(".spesifikasi_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".spesifikasi_id").append(`<option data-stock_id="${item.stock_id}" data-kode_barang="${item.kode_barang}" data-barang="${item.barang}" data-kode_satuan="${item.kode_satuan}" value="${item.spesifikasi_id}">(${item.kode_barang}) ${item.barang}</option>`)
                })
                $(".spesifikasi_id").val();
            }
        });
    }

    function getListDokumenPabean() {
        // GET LIST STOCK PER DOKUMEN PABEAN
        $.ajax({
            url: `<?= base_url('mutasi/list-stock-dokumen-bc'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                stock_id: $(".spesifikasi_id option:selected").data('stock_id'),
            },
            dataType: "json",
            success: function(res) {
                // LIST STOK PER BC
                listStockAsal = [];
                listStockAsal = res.data;
                drawTableAsalBarang(res.data);
            }
        });
    }

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $(".no_mutasi").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("mutasi/get-mutasi-no-global"); ?>`,
                method: "GET",
                data: {
                    divisi_id: $('#divisi_asal_id option:selected').val()
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".no_mutasi").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".no_mutasi").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".no_mutasi").val("");
                    }
                }
            })
        } else {
            $(".no_mutasi").attr("readonly", false);
            $(".no_mutasi").val("");
        }
    }

    function drawTableAsalBarang(data) {
        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().clear().draw();
            dataTable.destroy();
        }
        const table = $('#dataTable');
        var typePengambilanStok = $('#type_pengambilan_stock option:selected').val();

        $.each(data, function(i, v) {
            var newRow = $('<tr style="color:whitesmoke;">');
            if (typePengambilanStok == "FIFO" || parseFloat(v.stok_total) == 0) {
                newRow.append($('<td style="text-align: center;">').html(
                    `
                `
                ));
            } else {
                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <div class="form-check">
                        <input  data-id="${v.id}" data-stok_total="${v.stok_total}" autocomplete="one-time-code" class="form-check-input child" type="checkbox">
                    </div>
                `
                ));
            }

            newRow.append($('<td style="text-align:center;">').text(v.sumber));
            newRow.append($('<td style="text-align:center;">').text(v.stock_dokumen));
            newRow.append($('<td style="text-align:center;">').text(v.supplier_name));
            newRow.append($('<td style="text-align:center;">').text(v.bc_type));
            newRow.append($('<td style="text-align:center;">').text(v.no_aju));
            newRow.append($('<td style="text-align:center;">').text(v.stock_date));
            newRow.append($('<td style="text-align:center;">').text(v.barang));
            newRow.append($('<td style="text-align:center;">').text(v.satuan));
            newRow.append($('<td style="text-align:center;">').text(v.stok_total));
            table.find('tbody').append(newRow);
        });

        dataTable = $('#dataTable').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
            processing: false,
            serverSide: false,
            ordering: true,
            order: [],
            fixedHeader: true,
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            lengthMenu: [
                [100],
                [100]
            ],
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

    function drawTableSelectedItem(data) {
        if ($.fn.DataTable.isDataTable('#selectedItemTable')) {
            $('#selectedItemTable').DataTable().clear().draw();
            selectedItemTable.destroy();
        }
        const table = $('#selectedItemTable');
        var typePengambilanStok = $('#type_pengambilan_stock option:selected').val();
        var no = 1;
        $.each(data, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').html(
                `
                   ${no++} 
                `
            ));
            newRow.append($('<td style="text-align: center;">').text(v.type_barang_text));
            newRow.append($('<td style="text-align: center;">').text(v.sumber));
            newRow.append($('<td style="text-align: center;">').text(v.stock_dokumen));
            newRow.append($('<td style="text-align: center;">').text(v.supplier_name));
            newRow.append($('<td style="text-align: center;">').text(v.bc_type + " / " + v.no_aju));
            newRow.append($('<td style="text-align: center;">').text(v.stock_date));
            newRow.append($('<td style="text-align: center;">').text(v.barang));
            newRow.append($('<td style="text-align: center;">').text(v.satuan));
            newRow.append($('<td style="text-align: center;">').text(v.stok_total));

            if (typePengambilanStok == "FIFO") {
                newRow.append($('<td>').text(
                    v.qty
                ));
            } else {
                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <input <?= !empty($mutasiGlobal) ? (($mutasiGlobal['status_posting'] == "1") ? 'disabled' : '') : '' ?> class="form-control stok-mutasi" oninput="preventNegativeInput(this)" autocomplete="one-time-code" data-id="${v.id}" data-stok_total="${v.stok_total}" class="form-control" type="text" value="${v.qty}">
                `
                ));
            }
            newRow.append($('<td style="text-align: center;">').html(
                `
                    <button <?= !empty($mutasiGlobal) ? (($mutasiGlobal['status_posting'] == "1") ? 'disabled' : '') : '' ?> type="button" class="btn btn-danger" onclick="deleteDetail(${v.id})" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                `
            ));
            table.find('tbody').append(newRow);
        });

        selectedItemTable = $('#selectedItemTable').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
            processing: false,
            serverSide: false,
            ordering: true,
            order: [],
            fixedHeader: true,
            "initComplete": function(settings, json) {
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

        selectedItemTable.draw();
    }

    function getIDListDataSelected() {
        var id_selected = [];
        $.each(listStockSelected, function(i, v) {
            id_selected.push(v.id);
        })
        return id_selected;
    }

    $('.posting-mutasi').click(function() {
        Swal.fire({
            icon: 'question',
            title: 'Posting Mutasi ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("mutasi/posting-global"); ?>",
                    data: {
                        id: $('.id').val()
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
                            }).then((result) => {
                                window.location.href = "<?= base_url("mutasi/global"); ?>";
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    },
                });
            }
        })
    })

    $('.delete-parent').click(function() {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Mutasi ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("mutasi/delete-global"); ?>",
                    data: {
                        id: $('.id').val()
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
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            }).then((result) => {
                                window.location.href = "<?= base_url('mutasi/global') ?>"
                            });
                        }
                    },
                });
            }
        })
    });
</script>



<?= $this->endSection(); ?>