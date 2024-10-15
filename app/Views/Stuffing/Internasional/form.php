<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($stuffingInternasional) ? "Tambah Pengeluaran Internasional" : "Update Pengeluaran Internasional" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("pengeluaran-internasional"); ?>">
                Kembali
            </a>
            <?php if (!empty($stuffingInternasional)) : ?>
                <?php if ($stuffingInternasional['status_posting'] == "0") : ?>
                    <?php if (can('Stuffing', 'Pengeluaran Internasional', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="remove('<?= encrypt($stuffingInternasional['id']); ?>')">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Stuffing', 'Pengeluaran Internasional', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="posting('<?= encrypt($stuffingInternasional['id']); ?>')">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Stuffing', 'Pengeluaran Internasional', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("pengeluaran-internasional/print/"); ?><?= encrypt($stuffingInternasional['id']); ?>')">
                            Print
                        </button>
                    <?php endif; ?>
                    <?php if (can('Stuffing', 'Pengeluaran Internasional', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if (can('Stuffing', 'Pengeluaran Internasional', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("pengeluaran-internasional/print/"); ?><?= encrypt($stuffingInternasional['id']); ?>')">
                            Print
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
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Order Form</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id" value="<?= !empty($stuffingInternasional) ? encrypt($stuffingInternasional['id']) : '' ?>" class="id">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" disabled class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime(!empty($stuffingInternasional) ? $stuffingInternasional['tanggal'] : $tanggal)); ?>">
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" <?= !empty($stuffingInternasional) ? 'disabled=true' : ''; ?> value="<?= !empty($stuffingInternasional) ? $stuffingInternasional['no_stuffing'] : ""; ?>" type="text" class="form-control <?= !empty($stuffingInternasional) ? '' : 'no_stuffing'; ?>" id="no_stuffing" name="no_stuffing" placeholder="No. Stuffing Internasional">
                                    <label for="floatingInput">No. Stuffing Internasional</label>
                                </div>
                                <div style="<?= !empty($stuffingInternasional) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($stuffingInternasional) ? ($stuffingInternasional['status_posting'] ? 'disabled' : 'disabled') : '' ?> class="form-select sales_order_id" id="sales_order_id" name="sales_order_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($orderForm as $v) : ?>
                                    <option <?= !empty($stuffingInternasional) ? ($stuffingInternasional['sales_order_export_id'] == $v['sales_order_export_id'] ? 'selected' : '') : '' ?> value="<?= $v['sales_order_export_id'] ?>" data-id_customer="<?= $v['customer_id'] ?>" data-name_customer="<?= $v['customer_name'] ?>" data-bc_type="<?= $v['bc_type'] ?>">
                                        <?= strtoupper($v['sales_order_export_no']); ?> - <?= strtoupper($v['customer_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Sales Order</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="hidden" value="<?= !empty($stuffingInternasional) ? $stuffingInternasional['customer_id'] : '' ?>" class="form-control customer_id" id="customer_id" name="customer_id" aria-label="Floating label select example" />
                            <input type="text" <?= !empty($stuffingInternasional) ? ($stuffingInternasional['status_posting'] ? 'readonly' : 'readonly') : 'readonly' ?> placeholder="Nama Customer" value="<?= !empty($stuffingInternasional) ? $stuffingInternasional['customer_name'] : '' ?>" class="form-control customer_name" id="customer_name" name="customer_name" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Nama Customer</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating" style="height: 50px;">
                            <select disabled <?= !empty($stuffingInternasional) ? 'disabled' : ''; ?> class="form-select aju_document_type" id="aju_document_type" name="aju_document_type" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataAJU as $aju) : ?>
                                    <option <?= !empty($stuffingInternasional) ? ($stuffingInternasional['bc_type'] === $aju["id"] ? "selected" : "") : ""; ?> value="<?= $aju["id"]; ?>"><?= $aju["value"]; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Dokumen Pabean</label>
                        </div>
                    </div>
                </div>
            </form>

            <div class="detail-form-layout">
                <div class="col-subtitle-modal">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold modal-sub-title">Data Barang Sales</label>
                        </div>

                        <div class="col-md-6">
                            <button class="btn btn-show-detail btn-add btn-block float-right  <?= !empty($stuffingInternasional) ? (($stuffingInternasional['status_posting'] == "1") ? 'disabled' : '') : '' ?>" data-btn="kemasan-modal">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Kemasan
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTableSalesOrder" id="dataTableSalesOrder" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 10px;">#</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Qty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Data Inventori Barang Internal</label>
                    </div>
                    <form class="detail-form">
                        <input type="hidden" name="id_detail" class="id_detail" id="id_detail">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select <?= !empty($stuffingInternasional) ? ($stuffingInternasional['status_posting'] ? 'disabled' : '') : '' ?> class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($divisi as $d) : ?>
                                            <option value="<?= $d['id'] ?>">
                                                <?= $d['divisi']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Departemen</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select <?= !empty($stuffingInternasional) ? ($stuffingInternasional['status_posting'] ? 'disabled' : '') : '' ?> class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php if (!empty($warehouse)) : ?>
                                            <?php foreach ($warehouse as $w) : ?>
                                                <option value="<?= $w['id'] ?>">
                                                    <?= $w['warehouse_name'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Warehouse</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select <?= !empty($mutasi) ? ($mutasi['status_posting'] ? 'disabled' : '') : '' ?> class="form-select type_pengambilan_stock" id="type_pengambilan_stock" name="type_pengambilan_stock" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <option <?= !empty($mutasi) ? ($mutasi['tipe_pengambilan_stock'] == "PABEAN" ? 'selected' : '') : '' ?> value="PABEAN">PABEAN</option>
                                        <option <?= !empty($mutasi) ? ($mutasi['tipe_pengambilan_stock'] == "FIFO" ? 'selected' : '') : '' ?> value="FIFO">FIFO</option>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Tipe Pengambilan Stok</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating" style="height: 50px;">
                                    <input placeholder="Qty" readonly oninput="preventNegativeInput(this)" class="form-control qty_mutasi_fifo" id="qty_mutasi_fifo" name="qty_mutasi_fifo" aria-label="Floating label select example" />
                                    <label for="floatingInput" style="z-index: 1;">Qty Barang Keluar</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select type_barang" id="type_barang" name="type_barang" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($tipeBarang as $t) : ?>
                                            <?php if ($t['description'] == "bahan_jadi" || $t['description'] == "bahan_penolong") : ?>
                                                <option value="<?= $t['description'] ?>">
                                                    <?= strtoupper($t['value']); ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Tipe Barang</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select spesifikasi_id" id="spesifikasi_id" name="spesifikasi_id" aria-label="Floating label select example">
                                        <option value=""></option>

                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Barang - Spesifikasi (Dari Warehouse)</label>
                                </div>
                            </div>
                            <!-- <div class="col-md-4">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select spesifikasi_in_id" id="spesifikasi_in_id" name="spesifikasi_in_id" aria-label="Floating label select example">
                                        <option value=""></option>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Barang - Spesifikasi (Dari Order Form)</label>
                                </div>
                            </div> -->
                        </div>
                    </form>
                    <div class="col-md-12 col-table-button-tts">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center; width:10px">#</th>
                                        <th style="text-align: center;">Tipe Barang</th>
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
                    <label class="form-label font-weight-bold lable-title">Daftar Barang Yang Akan Dikeluarkan</label>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive" style="margin-top: -10px;">
                        <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts" id="selectedItemTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center; width:10px">#</th>
                                    <th style="text-align: center;">Tipe Barang</th>
                                    <th style="text-align: center;">Dokumen Pabean</th>
                                    <th style="text-align: center;">No Aju</th>
                                    <th style="text-align: center;">Tanggal Penerimaan</th>
                                    <th style="text-align: center;">Barang - Spesifikasi</th>
                                    <th style="text-align: center;">Satuan</th>
                                    <th style="text-align: center;">Qty</th>
                                    <th style="text-align: center;">Qty Dikeluarkan</th>
                                    <th style="text-align: center;">Nama Barang Order</th>
                                    <th style="text-align: center;">Qty Barang Order</th>
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
<!-- modal kemasan -->
<div class="modal kemasan-modal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-detail-name"></label> Kemasan</h5>
            </div>
            <div class="modal-body">
                <?= csrf_field() ?>
                <form class="kemasan-form" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id_detail" name="id_detail" id="id_detail" />
                    <!-- <input autocomplete="one-time-code" type="hidden" class="id_barang" name="id_barang" id="id_barang" /> -->

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select id_barang" name="id_barang" id="id_barang" aria-label="Floating label select example">
                                        <option value=""></option>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Nama Kemasan</label>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control satuan" name="satuan" id="satuan" placeholder="Satuan">
                                <label for="floatingInput">Satuan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" oninput="this.value=this.value.replace(/[^0-9.]/g,'');" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                                <label for="floatingInput">Qty</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control keteranganppn" name="keteranganppn" id="keteranganppn" placeholder="keteranganppn">
                                <label for="floatingInput">Status PPN</label>
                            </div>
                        </div>

                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-kemasan btn-discard mr-3">Kembali</button>
                <button type="submit" class="btn btn-submit-form btn-submit-kemasan">Simpan</button>
                <!-- <button type="button" class="btn btn-discard delete-btn delete-detail delete-form">Hapus</button> -->
            </div>
        </div>
    </div>
</div>
<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    changeStatus();

    var listStockAsal = [];
    var listStockOrder = [];
    var listStockSelected = [];

    const table = $('.dataTableSalesOrder').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        info: false,
        paging: false,
        fixedHeader: true,
        display: "stripe",
        searching: false,
        ordering: false,
        columns: [{
                data: "no",
                className: "text-center",
            },
            {
                data: "kode_barang",
                className: "text-center"
            },
            {
                data: "nama_barang",
                className: "text-center"
            },
            {
                data: "qty",
                className: "text-center"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    return id; // Return only the id value without any HTML tags
                }
            }
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

    $('#type_pengambilan_stock').select2({
        placeholder: "Pilih Tipe Ambil Stok",
        theme: "bootstrap-5",
    }).change(function() {
        // FIFO
        if ($(this).val() == "FIFO") {
            $('.qty_mutasi_fifo').removeAttr('readonly');
        } else {
            $('.qty_mutasi_fifo').attr('readonly', 'readonly');
        }
        $('#select_tipe_bahan').val(null).change();
        $("#select_tipe_bahan").prop('disabled', false);
        $('#select_nama_barang').val(null).change();
        $("#select_nama_barang").prop('disabled', false);
        listStockAsal = [];
        drawTableAsalBarang();
    });

    <?php if (!empty($stuffingInternasional)) : ?>
        // GET LIST BARANG 
        $.ajax({
            url: `<?= base_url('pengeluaran-internasional/list-barang-stock-init'); ?>`,
            method: "GET",
            beforeSend: function() {},
            complete: function() {},
            data: {
                type_barang: $(".type_barang option:selected").val(),
                divisi_id: $(".divisi_id option:selected").val(),
                warehouse_id: $(".warehouse_id option:selected").val()
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
        // APPEND 
        <?php foreach ($stuffingInternasionalDetail as $m) : ?>
            listStockSelected.push({
                id_stuffing_detail: "<?= $m['id_stuffing_detail'] ?>",
                id: "<?= $m['id'] ?>",
                bc_id: "<?= $m['bc_id'] ?>",
                stock_detail_id: "<?= $m['stock_detail_id'] ?>",
                no_aju: "<?= $m['no_aju'] ?>",
                stock_id: "<?= $m['stock_id'] ?>",
                stok_total: "<?= $m['stok_total'] ?>",
                stock_dokumen: "<?= $m['stock_dokumen'] ?>",
                no_dokumen_1: "<?= $m['no_dokumen_1'] ?>",
                no_dokumen_2: "<?= $m['no_dokumen_2'] ?>",
                bc_type: "<?= $m['bc_type'] ?>",
                satuan: "<?= $m['satuan'] ?>",
                barang: "<?= $m['barang'] ?>",
                type_barang: "<?= $m['type_barang'] ?>",
                type_barang_text: "<?= $m['type_barang_text'] ?>",
                stock_date: "<?= $m['stock_date'] ?>",
                qty: "<?= $m['qty'] ?>",
                divisi_id: "<?= $m['divisi_id'] ?>",
                warehouse_id: "<?= $m['warehouse_id'] ?>",
                output: {
                    id_barang: "<?= $m['output']['id_barang'] ?>",
                    barang: "<?= $m['output']['barang'] ?>",
                    qty: "<?= $m['output']['qty'] ?>"
                }
            });
        <?php endforeach; ?>
        drawTableSelectedItem(listStockSelected);
        // <?php if ($stuffingInternasional['status_posting']) : ?>
        //     $('.detail-form-layout').hide()
        // <?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($salesOrder)) : ?>
        $.ajax({
            url: `<?= base_url('pengeluaran-internasional/list-barang-output'); ?>`,
            method: "GET",
            data: {
                sales_order_id: $(".sales_order_id option:selected").val()
            },
            dataType: "json",
            success: function(res) {
                listStockOrder = [];
                listStockOrder = res.data;
                drawTableOrderBarang(res.data);
            }
        });
    <?php endif; ?>

    $('#sales_order_id').select2({
        placeholder: "Pilih Sales Order",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        let customer_id = $('#sales_order_id option:selected').data('id_customer');
        let customer_name = $('#sales_order_id option:selected').data('name_customer');
        let bc_type = $('#sales_order_id option:selected').data('bc_type');
        $('#customer_id').val(customer_id);
        $('#customer_name').val(customer_name);
        $('#aju_document_type').val(bc_type).change();
        getListBarangOutput();
    });

    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        listStockAsal = [];
        // listStockSelected = [];
        drawTableAsalBarang(listStockAsal);
        drawTableSelectedItem(listStockSelected);
        // GET BARANG
        getListBarang();
    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // GET WAREHOUSES
        getListWarehouse();
        listStockAsal = [];
        // listStockSelected = [];
        drawTableAsalBarang(listStockAsal);
        drawTableSelectedItem(listStockSelected);
        // GET BARANG
        getListBarang();
    });

    $('#type_barang').select2({
        placeholder: "Pilih Tipe Barang",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function() {
        // GET LIST BARANG
        getListBarang();
        listStockAsal = [];
        drawTableAsalBarang(listStockAsal);
    });

    $('#spesifikasi_id').select2({
        placeholder: "Pilih Barang - Spesifikasi (Dari Warehouse)",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // LIST DOKUMEN PABEAN
        getListDokumenPabean();
    });


    $('#spesifikasi_in_id').select2({
        placeholder: "Pilih Barang - Spesifikasi (Dari Order Form)",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {

    });

    // VALIDATOR
    var validator = $(".create-form").validate({
        rules: {
            no_stuffing: {
                required: true
            },
            sales_order_id: {
                required: true
            },
        },
        messages: {
            no_stuffing: {
                required: "No Stuffing wajib diisi"
            },
            sales_order_id: {
                required: "Sales Order wajib diisi"
            },
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


    // $('#select-item-btn').click(function() {
    //     var checkedCheckboxes = $(".child:checked");
    //     var dataIds = checkedCheckboxes.map(function() {
    //         return $(this).data("id");
    //     }).get();
    //     var id_selected = getIDListDataSelected();
    //     var barangIn = $('#spesifikasi_in_id option:selected');

    //     var checkedCheckboxesorder = $(".childOrder:checked");
    //     var dataIdBarangOrder = checkedCheckboxesorder.map(function() {
    //         return $(this).data("id_barang");
    //     }).get().toString();
    //     var dataQtyBarangOrder = checkedCheckboxesorder.map(function() {
    //         return $(this).data("qty_barang");
    //     }).get().toString();
    //     var dataNamaBarangOrder = checkedCheckboxesorder.map(function() {
    //         return $(this).data("nama_barang");
    //     }).get().toString();

    //     if (dataIdBarangOrder == "" || dataIdBarangOrder == undefined) {
    //         Swal.fire({
    //             icon: 'error',
    //             title: 'Barang Output Wajib Dipilih !',
    //             confirmButtonColor: '#4e73df',
    //             confirmButtonText: 'Ok'
    //         });
    //     } else {
    //         // console.log(listStockSelected);
    //         $.each(listStockAsal, function(i, v) {
    //             var currentID = Number(v.id);

    //             if ($.inArray(currentID, dataIds) !== -1) {
    //                 var isIDSelected = $.grep(listStockSelected, function(item) {
    //                     return item.id == Number(currentID);
    //                 }).length > 0;
    //                 let divisi_id = $('#divisi_id option:selected').val();
    //                 let warehouse_id = $('#warehouse_id option:selected').val();
    //                 let tipe_barang = $('#type_barang option:selected').val();

    //                 var qty = v.qty ? v.qty : 0;

    //                 if (!isIDSelected) {
    //                     var isDuplicateOutput = listStockSelected.some(function(item) {
    //                         return item.output.id_barang == dataIdBarangOrder && parseFloat(item.qty) + parseFloat(qty) >= parseFloat(dataQtyBarangOrder);
    //                     });

    //                     if (!isDuplicateOutput) {
    //                         listStockAsal[i].stok_total = parseFloat(listStockAsal[i].stok_total);
    //                         listStockAsal[i].divisi_id = $('#divisi_id option:selected').val();
    //                         listStockAsal[i].warehouse_id = $('#warehouse_id option:selected').val();
    //                         listStockAsal[i].tipe_barang = $('#type_barang option:selected').val();
    //                         listStockAsal[i].qty = 0;
    //                         listStockAsal[i].output = {
    //                             id_barang: dataIdBarangOrder,
    //                             barang: dataNamaBarangOrder,
    //                             qty: parseFloat(dataQtyBarangOrder)
    //                         }
    //                         listStockSelected.push(listStockAsal[i]);
    //                     } else {
    //                         Swal.fire({
    //                             icon: 'error',
    //                             title: 'Qty barang diorder sudah terpenuhi',
    //                             confirmButtonColor: '#4e73df',
    //                             confirmButtonText: 'Ok'
    //                         });
    //                     }
    //                 }
    //             }
    //         });
    //         console.log(listStockSelected);

    //         drawTableSelectedItem(listStockSelected);
    //     }
    // });

    $('.btn-submit-parent').click(function() {
        if (listStockSelected.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Barang yang akan dikirimkan ke vendor tidak boleh kosong !',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        } else {
            if ($('.create-form').valid()) {
                var isValid = true;
                var dataError = null;

                $.each(listStockSelected, function(i, v) {
                    var element = $('input[data-id="' + v.id + '"].stok-out');
                    var input_user = parseFloat(element.val());
                    var stok_max = parseFloat(element.data('stok_total'));

                    if (isNaN(input_user) || input_user == undefined || input_user == 0) {
                        dataError = listStockSelected[i];
                        isValid = false;
                    }
                });

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Stok keluar, barang ' + dataError.barang + ' dengan dokumen ' + dataError.bc_type + ' / ' + dataError.no_aju + ' tidak valid!',
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
                        cancelButtonText: 'Kembali',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let id = $('#id').val();
                            let data = new FormData(document.querySelector(".create-form"));
                            data.append('listBarang', JSON.stringify(listStockSelected));

                            if (id) {
                                // UPDATE
                                $.ajax({
                                    url: "<?= base_url("pengeluaran-internasional/update"); ?>",
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
                                                location.reload();
                                            }
                                        });
                                    },
                                });
                            } else {
                                // INSERT
                                $.ajax({
                                    url: "<?= base_url("pengeluaran-internasional/save"); ?>",
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
                                                window.location.href = "<?= base_url('pengeluaran-internasional/id/') ?>" + response.id
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


    function getIDListDataSelected() {
        var id_selected = [];
        $.each(listStockSelected, function(i, v) {
            id_selected.push(v.id);
        })
        return id_selected;
    }

    function definisiQtyInput() {
        $.each(listStockSelected, function(i, v) {
            var element = $('input[data-id="' + v.id + '"].stok-out');
            var input_user = parseFloat(element.val());
            var stok_max = parseFloat(element.data('stok_total'));

            if (input_user > stok_max && input_user > listStockSelected[i].output.qty) {
                listStockSelected[i].qty = stok_max;
            } else if (input_user > stok_max && input_user < listStockSelected[i].output.qty) {
                listStockSelected[i].qty = stok_max;
            } else if (input_user < stok_max && input_user > listStockSelected[i].output.qty) {
                listStockSelected[i].qty = listStockSelected[i].output.qty;
            } else if (input_user < stok_max && input_user < listStockSelected[i].output.qty) {
                listStockSelected[i].qty = input_user;
            } else {
                listStockSelected[i].qty = input_user;
            }
        });
    }

    function getListWarehouse() {
        $.ajax({
            url: `<?= base_url('pengeluaran-internasional/warehouse'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $(".divisi_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_id").empty()
                $(".warehouse_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
                $(".warehouse_id").val();
            }
        });
    }

    function getListBarang() {
        // GET LIST BARANG
        $.ajax({
            url: `<?= base_url('pengeluaran-internasional/list-barang-stock-init'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                type_barang: $(".type_barang option:selected").val(),
                divisi_id: $(".divisi_id option:selected").val(),
                warehouse_id: $(".warehouse_id option:selected").val()
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
            url: `<?= base_url('pengeluaran-internasional/list-stock-dokumen-bc'); ?>`,
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

    function getListBarangOutput() {
        $.ajax({
            url: `<?= base_url('pengeluaran-internasional/list-barang-output'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                sales_order_id: $(".sales_order_id option:selected").val()
            },
            dataType: "json",
            success: function(res) {
                // $(".spesifikasi_in_id").empty()
                // $(".spesifikasi_in_id").append(`<option value=""></option>`)
                // res.data.forEach(function(item) {
                //     $(".spesifikasi_in_id").append(`<option data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-qty_barang="${item.qty}" value="${item.id_barang}">(${item.kode_barang}) ${item.nama_barang} Qty : ${item.qty}</option>`)
                // })
                // $(".spesifikasi_in_id").val();

                listStockOrder = [];
                listStockOrder = res.data;
                drawTableOrderBarang(res.data);
            }
        });
    }

    function drawTableAsalBarang(data) {
        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().clear().draw();
            dataTable.destroy();
        }
        const table = $('#dataTable');
        var typePengambilanStok = $('#type_pengambilan_stock option:selected').val();
        $.each(data, function(i, v) {
            var newRow = $('<tr>');
            if (typePengambilanStok == "FIFO" || parseFloat(v.stok_total) == 0) {
                newRow.append($('<td style="text-align: center;">').html(
                    `
                `
                ));
            } else {
                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <div class="form-check">
                        <input data-id="${v.id}" data-stok_total="${v.stok_total}" autocomplete="one-time-code" class="form-check-input child" type="checkbox">
                    </div>
                `
                ));
            }

            newRow.append($('<td style="text-align: center;">').text(v.type_barang_text));
            newRow.append($('<td style="text-align: center;">').text(v.bc_type));
            newRow.append($('<td style="text-align: center;">').text(v.no_aju));
            newRow.append($('<td style="text-align: center;">').text(v.stock_date));
            newRow.append($('<td style="text-align: center;">').text(v.barang));
            newRow.append($('<td style="text-align: center;">').text(v.satuan));
            newRow.append($('<td style="text-align: center;">').text(v.stok_total));
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

    $('#select-item-btn').click(function() {
        var typePengambilanStock = $('#type_pengambilan_stock option:selected').val();
        if (typePengambilanStock == "FIFO") {
            insertListFifo();
        } else {
            insertListPabean();
        }

        // Disable the delete buttons after item selection
        var checkedCheckboxesOrder = $(".childOrder:checked");
        checkedCheckboxesOrder.each(function() {
            var salesOrderExportDetailId = $(this).data("id");
            $('button[data-sales_order_export_detail_id="' + salesOrderExportDetailId + '"]').prop('disabled', true);
        });
        // console.log(listStockAsal);


    });

    function insertListPabean() {
        var checkedCheckboxes = $(".child:checked");
        var dataIds = checkedCheckboxes.map(function() {
            return $(this).data("id");
        }).get();
        var id_selected = getIDListDataSelected();
        var barangIn = $('#spesifikasi_in_id option:selected');

        var checkedCheckboxesorder = $(".childOrder:checked");
        var dataIdBarangOrder = checkedCheckboxesorder.map(function() {
            return $(this).data("id_barang");
        }).get().toString();
        var dataQtyBarangOrder = checkedCheckboxesorder.map(function() {
            return $(this).data("qty_barang");
        }).get().toString();
        var dataNamaBarangOrder = checkedCheckboxesorder.map(function() {
            return $(this).data("nama_barang");
        }).get().toString();

        if (dataIdBarangOrder == "" || dataIdBarangOrder == undefined) {
            Swal.fire({
                icon: 'error',
                title: 'Barang Output Wajib Dipilih !',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        } else {
            // console.log(listStockSelected);
            $.each(listStockAsal, function(i, v) {
                var currentID = Number(v.id);

                if ($.inArray(currentID, dataIds) !== -1) {
                    var isIDSelected = $.grep(listStockSelected, function(item) {
                        return item.id == Number(currentID);
                    }).length > 0;
                    let divisi_id = $('#divisi_id option:selected').val();
                    let warehouse_id = $('#warehouse_id option:selected').val();
                    let tipe_barang = $('#type_barang option:selected').val();

                    var qty = v.qty ? v.qty : 0;

                    if (!isIDSelected) {
                        getItemQty = 0
                        listStockSelected.some(function(item) {
                            if (item.output.id_barang == dataIdBarangOrder) {
                                getItemQty += parseFloat(item.qty);
                            }

                        });
                        var isDuplicateOutput = listStockSelected.some(function(item) {

                            // console.log(parseFloat(dataQtyBarangOrder));
                            // console.log(parseFloat(getItemQty));

                            return item.output.id_barang == dataIdBarangOrder && parseFloat(getItemQty) >= parseFloat(dataQtyBarangOrder);
                        });

                        // console.log(isDuplicateOutput);

                        if (!isDuplicateOutput) {
                            listStockAsal[i].stok_total = parseFloat(listStockAsal[i].stok_total);
                            listStockAsal[i].divisi_id = $('#divisi_id option:selected').val();
                            listStockAsal[i].warehouse_id = $('#warehouse_id option:selected').val();
                            listStockAsal[i].tipe_barang = $('#type_barang option:selected').val();
                            listStockAsal[i].qty = 0;
                            listStockAsal[i].output = {
                                id_barang: dataIdBarangOrder,
                                barang: dataNamaBarangOrder,
                                qty: parseFloat(dataQtyBarangOrder)
                            }
                            listStockSelected.push(listStockAsal[i]);

                            // Disable the corresponding delete button
                            var salesOrderDetailId = checkedCheckboxesorder.data("sales_order_export_detail_id");
                            $(`[data-sales_order_export_detail_id="${salesOrderDetailId}"]`).prop('disabled', true);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Qty barang diorder sudah terpenuhi',
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            });
                        }
                    }
                }
            });

            drawTableSelectedItem(listStockSelected);
        }
    }

    function insertListFifo() {
        var divisi_id = $("#divisi_id").val();
        var warehouse_id = $("#warehouse_id").val();
        var qtyMutasiFifo = parseFloat($('#qty_mutasi_fifo').val());
        var checkedCheckboxesOrder = $(".childOrder:checked");

        if (isNaN(qtyMutasiFifo)) {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan : Qty Keluar Wajib Diisi',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                reverseButtons: true,
                confirmButtonText: 'Oke',
            });
            return;
        }

        if (checkedCheckboxesOrder.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Barang Output Wajib Dipilih !',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
            return;
        }

        var totalStokTotal = 0;
        $.each(listStockAsal, function(i, v) {
            totalStokTotal += parseFloat(v.stok_total);
        });

        if (qtyMutasiFifo > totalStokTotal) {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan : Stok barang tidak cukup !',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                reverseButtons: true,
                confirmButtonText: 'Oke',
            });
            return;
        }

        var dataIdBarangOrder = checkedCheckboxesOrder.map(function() {
            return $(this).data("id_barang");
        }).get().toString();

        var dataQtyBarangOrder = checkedCheckboxesOrder.map(function() {
            return $(this).data("qty_barang");
        }).get().toString();

        var dataNamaBarangOrder = checkedCheckboxesOrder.map(function() {
            return $(this).data("nama_barang");
        }).get().toString();

        var getItemQty = 0;
        listStockSelected.some(function(item) {
            if (item.output.id_barang == dataIdBarangOrder) {
                getItemQty += parseFloat(item.qty);
            }
        });

        var isDuplicateOutput = listStockSelected.some(function(item) {
            return item.output.id_barang == dataIdBarangOrder && parseFloat(getItemQty) >= parseFloat(dataQtyBarangOrder);
        });

        if (isDuplicateOutput) {
            Swal.fire({
                icon: 'error',
                title: 'Qty barang diorder sudah terpenuhi',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
            return;
        }

        $.each(listStockAsal, function(i, v) {
            var currentID = Number(v.id);
            if (qtyMutasiFifo == 0) return false; // Exit loop when qtyMutasiFifo is fully allocated

            var isIDSelected = $.grep(listStockSelected, function(item) {
                return item.id == currentID;
            }).length > 0;

            if (!isIDSelected && parseFloat(v.stok_total) > 0) {
                var mutasiQty = Math.min(qtyMutasiFifo, parseFloat(v.stok_total));
                v.stok_total = parseFloat(v.stok_total);
                v.qty = 0;
                v.qty_isi = 0;
                v.divisi_id = divisi_id;
                v.warehouse_id = warehouse_id;
                v.tipe_barang = $('#type_barang option:selected').val();

                v.qty = parseFloat(mutasiQty.toFixed(4));

                var barangOrder = {
                    id_barang: dataIdBarangOrder,
                    barang: dataNamaBarangOrder,
                    qty: parseFloat(dataQtyBarangOrder)
                };

                v.output = barangOrder;

                listStockSelected.push(v);
                qtyMutasiFifo -= mutasiQty;

                // Disable the delete button for the selected order
                var salesOrderDetailId = checkedCheckboxesOrder.data("sales_order_export_detail_id");
                $(`[data-sales_order_export_detail_id="${salesOrderDetailId}"]`).prop('disabled', true);
            }
        });

        drawTableSelectedItem(listStockSelected);
    }

    function drawTableOrderBarang(data) {
        const table = $('#dataTableSalesOrder').DataTable();
        // Clear the existing rows
        table.clear().draw();

        // Add new rows
        data.forEach(function(v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').html(`
                <div class="form-check">
                    <input data-id="${v.sales_order_export_detail_id}" data-kode_barang="${v.kode_barang}" data-nama_barang="${v.nama_barang}" data-qty_barang="${v.qty}" data-id_barang="${v.barang_id}" autocomplete="one-time-code" class="form-check-input childOrder" type="radio" name="selectedItem">
                </div>
            `));
            newRow.append($('<td style="text-align: center;">').text(v.kode_barang));
            newRow.append($('<td style="text-align: center;">').text(v.nama_barang));
            newRow.append($('<td style="text-align: center;">').text(v.qty));

            if (v.tipe_input === 'stuffing') {
                newRow.append($('<td style="text-align: center;">').html(`
            <button type="button" data-sales_order_export_detail_id = "${v.sales_order_export_detail_id}" onclick="handleDelete('${v.sales_order_export_detail_id}')" class="btn btn-discard delete-btn btn-trash <?= !empty($stuffingInternasional) ? (($stuffingInternasional['status_posting'] == "1") ? 'disabled' : '') : '' ?>"">
                <i class="fa fa-trash"></i>
            </button>
        `));
            } else {
                newRow.append($('<td style="text-align: center;">').html(''));
            }
            table.row.add(newRow).draw(false);
        });
    }

    function drawTableSelectedItem(data) {
        if ($.fn.DataTable.isDataTable('#selectedItemTable')) {
            $('#selectedItemTable').DataTable().clear().draw();
            selectedItemTable.destroy();
        }
        const table = $('#selectedItemTable');
        var no = 1;
        $.each(data, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').html(
                `
                   ${no++} 
                `
            ));
            newRow.append($('<td style="text-align: center;">').text(v.type_barang_text));
            newRow.append($('<td style="text-align: center;">').text(v.bc_type));
            newRow.append($('<td style="text-align: center;">').text(v.no_aju));
            newRow.append($('<td style="text-align: center;">').text(v.stock_date));
            newRow.append($('<td style="text-align: center;">').text(v.barang));
            newRow.append($('<td style="text-align: center;">').text(v.satuan));
            newRow.append($('<td style="text-align: center;">').text(v.stok_total));
            newRow.append($('<td style="text-align: center;">').html(
                `
                    <input onchange="definisiQtyInput()" <?= !empty($stuffingInternasional) ? (($stuffingInternasional['status_posting'] == "1") ? 'disabled' : '') : '' ?> class="form-control stok-out" oninput="preventNegativeInput(this);updateOrder($(this));" autocomplete="one-time-code" data-id="${v.id}" data-index="${i}"  data-stok_total="${v.stok_total}" class="form-control" type="text" value="${v.qty}">
                `
            ));
            newRow.append($('<td style="text-align: center;">').text(v.output.barang));
            newRow.append($('<td style="text-align: center;">').text(v.output.qty));
            newRow.append($('<td style="text-align: center;">').html(
                `
                    <button <?= !empty($stuffingInternasional) ? (($stuffingInternasional['status_posting'] == "1") ? 'disabled' : '') : '' ?> type="button" class="btn btn-danger" onclick="deleteDetail(${v.id}, '${v.sales_order_export_detail_id}')" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
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

    function deleteDetail(id, salesOrderDetailId) {
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
        console.log(salesOrderDetailId);
        // Aktifkan kembali tombol hapus di dataTableSalesOrder
        $(`[data-sales_order_export_detail_id="${salesOrderDetailId}"]`).removeAttr('disabled');;
    }

    function updateOrder(input) {
        var index = input.data('index');
        var qtyInput = input.val() == "" ? 0.0 : parseFloat(input.val());
        var item = listStockSelected[index];
        var maxQty = parseFloat(item.stok_total);
        var maxOutputQty = parseFloat(item.output.qty);
        var qty = Math.min(maxQty, qtyInput, maxOutputQty);
        input.val(qty);
    }

    function preventNegativeInput(inputElement) {
        var inputValue = inputElement.value;
        var numericValue = inputValue.replace(/[^0-9.]/g, '');
        if (parseFloat(numericValue) <= 0) {
            inputElement.value = 0;
        } else {
            inputElement.value = numericValue;
        }
    }

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $(".no_stuffing").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("pengeluaran-internasional/get-pengeluaran-internasional-no"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".no_stuffing").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".no_stuffing").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".no_stuffing").val("");
                    }
                }
            })
        } else {
            $(".no_stuffing").attr("readonly", false);
            $(".no_stuffing").val("");
        }
    }

    const print = function(url) {
        window.open(url, "_blank");
    }

    const posting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Stuffing Pengeluaran Internasional ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("pengeluaran-internasional/posting"); ?>",
                    data: {
                        id: id
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
                                location.reload()
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
    }

    const remove = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Stuffing Pengeluaran Internasional ?',
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
                    url: "<?= base_url("pengeluaran-internasional/delete"); ?>",
                    data: {
                        id: id
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
                                window.location.replace("<?= base_url("pengeluaran-internasional"); ?>");
                            });
                        }
                    },
                });
            }
        })
    }

    //start handel kemasan
    $(".btn-show-detail").click(function() {
        let salesOrderId = $('#sales_order_id').val();
        if (!salesOrderId) {
            Swal.fire({
                icon: 'error',
                title: 'Sales Order tidak boleh kosong',
                confirmButtonText: 'OK'
            });
        } else {

            $(".title-detail-name").text("Tambah");

            $(".id_detail").val('')
            $(".id_barang").empty('')
            $(".id_barang").val('').change()
            $("#warehouse").val('').change()
            $("#warehouse").empty()
            $(".harga").val('')
            $(".qty").val('')
            $(".statusppn").val('')
            $(".amount").val('')
            $(".keterangan").val('')

            $(".id_barang").val('')

            getBarang();
            $(".kemasan-modal").modal("show");
        }

    });

    $(".btn-hide-kemasan").click(function() {
        $(".kemasan-modal").modal("hide")
    });

    var validator_kemasan = $(".kemasan-form").validate({
        rules: {
            id_barang: {
                required: true
            },
            qty: {
                required: true
            }
        },
        messages: {
            id_barang: {
                required: "Nama kemasan wajib diisi"
            },
            qty: {
                required: "Qty wajib diisi"
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

    // BARANG
    $('.id_barang').select2({
        placeholder: "Pilih Barang",
        theme: "bootstrap-5",
        dropdownParent: $(".kemasan-modal .modal-content"),
        allowClear: true
    })

    function getBarang() {
        $.ajax({
            url: `<?= base_url("/pengeluaran-internasional/kemasanAll"); ?>`,
            method: "GET",
            dataType: "json",
            success: function(res) {
                $(".id_barang").empty();

                $(".id_barang").append(`<option data-satuan="" data-harga="" data-statusppn="" data-id_item="" value=""></option>`);



                res.dataBarang.forEach(function(item) {
                    $(".id_barang").append(`<option data-code="${item.kode_barang}" data-harga="${item.harga_jual}" data-statusppn="${item.statusppn}" data-satuan="${item.nama_satuan}"  data-harga="${item.harga_barang}" data-id_item="${item.id}" value="${item.id}">${item.nama_barang}</option>`);
                })
                // console.log(res.dataBarang);

                $(".id_barang").val("").change();
            }
        })
    }

    $(".id_barang").change(function() {
        if ($(".id_barang").val()) {
            let nama = $(".id_barang option:selected").data("nama") ? $(".id_barang option:selected").data("nama") : "";
            let idBarang = $(".id_barang option:selected").data("id_item") ? $(".id_barang option:selected").data("id_item") : "";
            let satuan = $(".id_barang option:selected").data("satuan") ? $(".id_barang option:selected").data("satuan") : "";
            let statusppn = $(".id_barang option:selected").data("statusppn");

            let harga = $(".id_barang option:selected").data("harga") ? $(".id_barang option:selected").data("harga") : 0;

            // let stok = $(".id_barang option:selected").data("stok") ? $(".id_barang option:selected").data("stok") : "";

            $(".nama_barang").attr("readonly", nama ? true : false);

            if (statusppn == 1) {
                $(".keteranganppn").val("Barang PPN");

            } else {
                $(".keteranganppn").val("Barang Tidak PPN");
            }

            $(".statusppn").val(statusppn);
            $(".nama_barang").val(nama);

            $(".satuan").val(satuan);
            $(".harga").val(harga);

        } else {
            $(".nama_barang").attr("readonly", false)
            // $(".harga").val("0");
            $(".qty").val("");
            $(".amount").val("0");
            $(".keterangan").val("").change();
            $(".statusppn").val("");
            $(".tax").val("");
            $(".discount_percentage").val("0");
            $(".dept").val("");

        }
    });

    function reloadTableData() {
        getListBarangOutput();
    }


    $(".btn-submit-kemasan").click(function() {
        if ($('.kemasan-form').valid()) {

            let data = new FormData(document.querySelector('.kemasan-form'));
            // Append sales_order_id to FormData
            data.append('sales_order_id', $(".sales_order_id option:selected").val());
            Swal.fire({
                icon: 'question',
                title: 'Simpan Data ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url("/pengeluaran-internasional/save-kemasan"); ?>",
                        data: data,
                        method: "POST",
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            setLoading();
                        },
                        complete: function() {
                            stopLoading();
                        },
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                    reverseButtons: true,
                                    confirmButtonText: 'Oke',
                                }).then((result) => {
                                    reloadTableData();
                                    $('.kemasan-modal').modal('hide');
                                    resetForm();
                                })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                    cancelButtonColor: '#d33',
                                    reverseButtons: true,
                                    confirmButtonText: 'Oke',
                                })
                            }
                        }
                    });
                }
            })

        }
    });

    // delete
    function handleDelete(id) {
        console.log(id)
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
                setLoading()
                $.ajax({
                    url: "<?= base_url("pengeluaran-internasional/delete-kemasan"); ?>",
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

                                    reloadTableData();
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
                            title: 'Data Gagal Dihapus, coba Lagi',
                            confirmButtonColor: '#4e73df',
                        })
                        stopLoading()
                    }
                });
            }
        })
    }


    $("#id_barang,#type_pengambilan_stock,#sales_order_id,#warehouse_id,#divisi_id,#type_barang,#spesifikasi_id,#spesifikasi_in_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');
</script>

<?= $this->endSection(); ?>