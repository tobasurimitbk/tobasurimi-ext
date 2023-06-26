<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1 class="title-name">Tambah</h1>
    <div class="col-button-tambah-spp">
        <?php if(!empty($dataSPP)){ ?> 

        <?php if($dataSPP->is_posted === false){ ?> 
            <button class="btn btn-hapus delete-parent float-right">
                Hapus
            </button>
            <?php } ?> 

            <button class="btn btn-warning btn-print float-right">
                Print
            </button>

            <?php if($dataSPP->is_posted === false && $dataSPP->approved_by_director !== 0){ 
                if($dataSPP->approved_by_head_of_purchasing !== 0 && $dataSPP->approved_by_headwarehouse !== 0){ 
            ?> 

            <button class="btn btn-success posting-spp">
                <i class="fa fa-paper-plane" aria-hidden="true"></i>
            </button>

            <?php } 
            }
            ?> 

            <?php } ?> 

            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("spp"); ?>">
                Batal
            </a>

            <?php if(!empty($dataSPP)){ 
                if($dataSPP->is_posted === false){ ?> 
            <button class="btn btn-show-form btn-save float-right">
                Simpan
            </button>
            <?php }
            } else { ?> 
            <button class="btn btn-show-form btn-save float-right">
                Simpan
            </button>
        <?php } ?> 
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col mb-3">
                <label class="form-label font-weight-bold lable-title">Data SPP</label>
            </div>
        </div>
        <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
            <input type="hidden" class="id" name="id" id="id" value="<?= !empty($dataSPP) ? $dataSPP->id : ""; ?>" />
            <?= csrf_field() ?>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= !empty($dataSPP) ? ($dataSPP->is_posted === true ? 'disabled=true' : '') : ''; ?> class="form-control input-picker request_date" value="<?= !empty($dataSPP) ? $dataSPP->request_date : ""; ?>" id="request_date" name="request_date" placeholder="Tanggal Order">
                                <label for="floatingInput">Tanggal Order</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-request-date"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" readonly="true" class="form-control" placeholder="Order Oleh" value="<?= !empty($dataSPP) ? $dataSPP->createdByName : session()->get("login")->name; ?>">
                        <label for="floatingInput">Order Oleh</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">

                        <select <?= !empty($dataSPP) ? ($dataSPP->is_posted === true ? 'disabled=true' : '') : ''; ?> class="form-select spp_type" name="spp_type" id="spp_type" aria-label="Floating label select example">
                            <option value="lokal" <?= !empty($dataSPP) ? ($dataSPP->spp_type === "Lokal" ? "selected" : "") : ""; ?>>Lokal</option>
                            <option value="import" <?= !empty($dataSPP) ? ($dataSPP->spp_type === "Import" ? "selected" : "") : ""; ?>>Import</option>
                        </select>

                        <label for="floatingInput">Tipe SPP</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= !empty($dataSPP) ? ($dataSPP->is_posted === true ? 'readonly=true' : '') : ''; ?> type="text" class="form-control spp_no" id="spp_no" name="spp_no" placeholder="No. SPP" value="<?= !empty($dataSPP) ? $dataSPP->spp_no : ""; ?>">
                                <label for="floatingInput">No. SPP</label>
                            </div>
                            <div style="<?= !empty($dataSPP) ? ($dataSPP->is_posted === true ? "display: none" : "") : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                <input style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select <?= !empty($dataSPP) ? ($dataSPP->is_posted === true ? 'disabled=true' : '') : ''; ?> class="form-select order_type" id="order_type" name="order_type" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php
                            if (!empty($dataOrderType)) {
                                foreach ($dataOrderType as $orderType) {
                            ?>
                                    <option value="<?= $orderType->id; ?>" <?= !empty($dataSPP) ? ($dataSPP->order_type === $orderType->id ? "selected" : "") : "" ?>><?= $orderType->value; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <label for="floatingInput">Jenis Order</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select <?= !empty($dataSPP) ? ($dataSPP->is_posted === true ? 'disabled=true' : '') : ''; ?> class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php
                            if (!empty($dataWarehouse)) {
                                foreach ($dataWarehouse as $warehouse) {
                            ?>
                                    <option value="<?= $warehouse->id; ?>" <?= !empty($dataSPP) ? ($dataSPP->warehouse_id === $warehouse->id ? "selected" : "") : ""; ?>><?= $warehouse->warehouse_name; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <label for="floatingInput">Departemen</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input <?= !empty($dataSPP) ? ($dataSPP->is_posted === true ? 'readonly=true' : '') : ''; ?> type="text" class="form-control note" id="note" name="note" placeholder="Catatan (Opsional)">
                        <label for="floatingInput">Catatan (Opsional)</label>
                    </div>
                </div>
            </div>
        </form>
        <div class="col-subtitle-modal">
            <div class="row mt-4">
                <div class="col-md-6">
                    <label class="form-label font-weight-bold modal-sub-title">List Barang</label>
                </div>
                <div class="col-md-6">
                <?php if(!empty($dataSPP)){ 
                    if($dataSPP->is_posted === false){ ?> 
                <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal">
                    <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                </button>
                <?php }
                } else { ?> 
                <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal">
                    <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                </button>
                <?php } ?> 
                    
                </div>
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table nowrap table-hover-tobasurimi dataTable table-tambah-spp" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            <th>Spesifikasi</th>
                            <th>Harga Barang</th>
                            <th>Qty</th>
                            <th>Total Harga</th>
                            <th>Keterangan</th>
                            <th>Hapus</th>
                        </tr>
                    </thead>
                    <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">
                    <?php 
                        $no = 1;
                        $total_harga_barang = 0;
                        $total_qty = 0;
                        $total_harga = 0;
                        if(!empty($dataSPP)){ 
                        foreach($dataSPP->purchase_request_details as $details){  
                            $total_harga_barang = $total_harga_barang + formatter(str_replace(",", "", $details->price), "STR_TO_INT");
                            $total_qty = $total_qty + $details->qty;
                            $total_harga = $total_harga + formatter(str_replace(",", "", $details->totalPrice), "STR_TO_INT");
                    ?> 

                        <tr>
                            <?php if($dataSPP->is_posted === false){ ?> 

                                    <td class="edit-table-detail" data-category="" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-satuan="<?= $details->unit; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $no; ?></td>
                                    <td class="edit-table-detail" data-category="" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-satuan="<?= $details->unit; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->kodeBarang; ?></td>
                                    <td class="edit-table-detail" data-category="" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-satuan="<?= $details->unit; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->barangName; ?></td>
                                    <td class="edit-table-detail" data-category="" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-satuan="<?= $details->unit; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->satuanName; ?></td>
                                    <td class="edit-table-detail" data-category="" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-satuan="<?= $details->unit; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->spec; ?></td>
                                    <td class="edit-table-detail" data-category="" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-satuan="<?= $details->unit; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->price; ?></td>
                                    <td class="edit-table-detail" data-category="" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-satuan="<?= $details->unit; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->qty; ?></td>
                                    <td class="edit-table-detail" data-category="" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-satuan="<?= $details->unit; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->totalPrice; ?></td>
                                    <td class="edit-table-detail" data-category="" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-satuan="<?= $details->unit; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->note; ?></td>
                                    <td><button class="btn-trash" onclick='deleteRow("<?= $no; ?>")'>X</button></td>

                            <?php } else { ?>

                                    <td><?= $no; ?></td>
                                    <td><?= $details->kodeBarang; ?></td>
                                    <td><?= $details->barangName; ?></td>
                                    <td><?= $details->satuanName; ?></td>
                                    <td><?= $details->spec; ?></td>
                                    <td><?= $details->price; ?></td>
                                    <td><?= $details->qty; ?></td>
                                    <td><?= $details->totalPrice; ?></td>
                                    <td><?= $details->note; ?></td>
                                    <td></td> 

                            <?php } ?>
                        
                        </tr>
                    <?php 
                        $no++;
                        }
                    } ?> 
                    </tbody>
                    <tfoot class="foot-detail-table" id="foot-detail-table">
                        <tr>
                            <td colspan="4"></td>
                            <td><b>TOTAL</b></td>
                            <td><b><?= number_format($total_harga_barang); ?></b></td>
                            <td><b><?= $total_qty; ?></b></td>
                            <td><b><?= number_format($total_harga); ?></b></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
</section>

<div class="modal detail-modal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-detail-name"></label> Barang</h5>
            </div>
            <div class="modal-body">
                <form class="detail-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id_detail" name="id_detail" id="id_detail" />
                    <input type="hidden" class="barang_id" name="barang_id" id="barang_id" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="hidden" class="kode" name="kode" id="kode" />
                                <select class="form-select kode_barang" name="kode_barang" id="kode_barang" aria-label="Floating label select example">
                                    <option data-barang_id= "" data-nama="" data-satuan="" data-stok="" data-harga="" value=""></option>
                                </select>
                                <label for="floatingInput">Kode Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control nama_barang" id="nama_barang" name="nama_barang" placeholder="Nama Barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row new-barang" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select category" name="category" id="category" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Kategori</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select satuan" name="satuan" id="satuan" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control spesifikasi" name="spesifikasi" id="spesifikasi" placeholder="Spesifikasi">
                                <label for="floatingInput">Spesitifikasi</label>
                            </div>
                        </div>
                    </div> 
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onkeyup="formatNumber(this)" type="text" class="form-control harga" name="harga" id="harga" placeholder="Harga Barang">
                                <label for="floatingInput">Harga Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                                <label for="floatingInput">Qty</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" readonly="true" class="form-control total" name="total" id="total" placeholder="Total Harga">
                                <label for="floatingInput">Total Harga</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control keterangan" name="keterangan" id="keterangan" placeholder="Keterangan">
                                <label for="floatingInput">Keterangan</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <div class="d-flex">
                    <button type="button" class="btn btn-discard delete-btn delete-form">Hapus</button>
                </div>
                <label>&nbsp;</label>
                <div class="d-flex">
                    <button type="button" class="btn btn-hide-detail btn-discard mr-3">Batal</button>
                    <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let list_items = [];
    let list_delete = [];
    var row = 0;
    var total_harga_barang = 0;
    var total_qty = 0;
    var total_harga = 0;
    var priceEdit = 0;
    var totalPriceEdit = 0;

    <?php if(!empty($dataSPP)){ 
        foreach($dataSPP->purchase_request_details as $details){  
    ?>

    priceEdit = Number('<?= $details->price; ?>'.replaceAll(",", ""));
    totalPriceEdit = Number('<?= $details->totalPrice; ?>'.replaceAll(",", ""));
    row = row + 1;

    total_harga_barang = total_harga_barang + priceEdit;
    total_qty = total_qty + <?= $details->qty; ?>;
    total_harga = total_harga + totalPriceEdit;

    list_items.push({
        id: <?= $details->id; ?>,
        row: row,
        category: '',
        barang_id: '<?= $details->barang_id; ?>',
        kode_barang: '<?= $details->kodeBarang; ?>',
        nama_barang: '<?= $details->barangName; ?>',
        nama_satuan: '<?= $details->satuanName; ?>',
        satuan: <?= $details->unit; ?>,
        spesifikasi: '<?= $details->spec; ?>',
        harga: '<?= $details->price; ?>',
        qty: <?= $details->qty; ?>,
        total: '<?= $details->totalPrice; ?>',
        keterangan: '<?= $details->note; ?>'
    })
    <?php 
        }
    ?>
    <?php
    } ?>

    console.log(list_items)
    
    var validator_detail = $(".detail-form").validate({
        rules: {
            kode_barang: {
                required: true
            },
            nama_barang: {
                required: true
            },
            qty: {
                required: true
            },
            satuan: {
                required: true
            },
            spesifikasi: {
                required: true
            },
            harga: {
                required: true
            }
        },
        messages: {
            kode_barang: {
                required: "Kode wajib diisi"
            },
            category: {
                required: "Kategori wajib diisi"
            },
            nama_barang: {
                required: "Nama wajib diisi"
            },
            qty: {
                required: "Qty wajib diisi"
            },
            satuan: {
                required: "Satuan wajib diisi"
            },
            spesifikasi: {
                required: "Spesifikasi wajib diisi"
            },
            harga: {
                required: "Harga wajib diisi"
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

    $(document).ready(function() {
        $(".request_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        // KATEGORI
        $('.category').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $('.category')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.category')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.category')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // SPP TYPE
        $('.order_type').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.order_type')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.order_type')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.order_type')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // WAREHOUSE
        $('.warehouse_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.warehouse_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.warehouse_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.warehouse_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // KODE BARANG
        $('.kode_barang').select2({
            placeholder: "Pilih Kode Barang / Buat Baru",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content"),
            tags: true,
            allowClear: true
        })

        //CSS SELECT2 FLOATING LABEL
        $('.kode_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.kode_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.kode_barang')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // SATUAN
        $('.satuan').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $('.satuan')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.satuan')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.satuan')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $('.icon-request-date').click(function() {
            $(".request_date").focus();
        });

        var validator = $(".create-form").validate({
            rules: {
                request_date: {
                    required: true
                },
                order_type: {
                    required: true
                },
                spp_no: {
                    required: true
                },
                order_type: {
                    required: true
                },
                warehouse_id: {
                    required: true,
                }
            },
            messages: {
                request_date: {
                    required: "Tanggal Order wajib diisi"
                },
                order_type: {
                    required: "Jenis Order wajib diisi"
                },
                spp_no: {
                    required: "No. SPP wajib diisi"
                },
                order_type: {
                    required: "Tipe SPP wajib diisi"
                },
                warehouse_id: {
                    required: "Departemen wajib diisi"
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
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
                $(element).addClass('select-class');                      

            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
                $(element).removeClass('select-class');   
            },
        });

        $(".posting-spp").click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Yakin akan di Posting?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Posting',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    $.ajax({
                        url: "<?= base_url("spp/update-status"); ?>",
                        data: {
                            id: $(".id").val()
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
                                    window.location.href = "<?= base_url("spp"); ?>" + "/id/" + $(".id").val()
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
            $(".detail-modal").modal("hide")

            // CHECK IF NO BARANG
            if(list_items.length === 0)
            {
                Swal.fire({
                    icon: 'error',
                    title: "Barang Tidak Boleh Kosong",
                    confirmButtonColor: '#4e73df',
                })
            }
            else
            {
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
                            setLoading()
                            let data = new FormData(document.querySelector(".create-form"));

                            let update_list_items = [];

                            if(list_delete.length !== 0)
                            {
                                list_delete.map(obj => {
                                    update_list_items.push(
                                        {
                                            id: obj.id ? Number(obj.id) : 0,
                                            category: obj.category ? Number(obj.category) : 0,
                                            item_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                            item_code: obj.kode_barang,
                                            item_name: obj.nama_barang,
                                            qty: obj.qty ? Number(obj.qty) : 0,
                                            unit: obj.satuan ? Number(obj.satuan) : 0,
                                            price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                            note: obj.keterangan,
                                            spec: obj.spesifikasi,
                                            isDeleted: true
                                        }
                                    )
                                })
                            }
                            
                            list_items.map(obj => {
                                if (obj.id) {
                                    update_list_items.push(
                                        {
                                            id: obj.id ? Number(obj.id) : 0,
                                            category: obj.category ? Number(obj.category) : 0,
                                            item_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                            item_code: obj.kode_barang,
                                            item_name: obj.nama_barang,
                                            qty: obj.qty ? Number(obj.qty) : 0,
                                            unit: obj.satuan ? Number(obj.satuan) : 0,
                                            price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                            note: obj.keterangan,
                                            spec: obj.spesifikasi
                                        }
                                    )
                                }
                                else
                                {
                                    update_list_items.push(
                                        {
                                            category: obj.category ? Number(obj.category) : 0,
                                            item_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                            item_code: obj.kode_barang,
                                            item_name: obj.nama_barang,
                                            qty: obj.qty ? Number(obj.qty) : 0,
                                            unit: obj.satuan ? Number(obj.satuan) : 0,
                                            price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                            note: obj.keterangan,
                                            spec: obj.spesifikasi
                                        }
                                    )
                                }
                            })

                            data.append("items", JSON.stringify(update_list_items))

                            let id = $(".id").val();
                            // UPDATE
                            if(id)
                            {
                                $.ajax({
                                    url: "<?= base_url("spp/update"); ?>",
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
                                                window.location.href = "<?= base_url("spp"); ?>" + "/id/" + id;
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
                            // CREATE
                            else
                            {
                                $.ajax({
                                    url: "<?= base_url("spp/save"); ?>",
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
                                                window.location.href = "<?= base_url("spp"); ?>" + "/id/" + response.id;
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
                        }
                    })
                }
            }
        })

        $(".btn-show-detail").click(function() {
            $(".new-barang").css('display', 'none');
            $('.category').rules('remove', 'required');
            $(".delete-detail").css('display', 'none');

            $(".title-detail-name").text("Tambah");
            $(".id_detail").val('');
            
            $(".kode").val('')
            $(".nama_barang").val('')
            $(".qty").val('')
            $(".satuan").val('')
            $(".spesifikasi").val('')
            $(".harga").val('')
            $(".total").val('')
            $(".keterangan").val('')

            validator_detail.resetForm();
            validator_detail.reset();

            $.ajax({
                url: `<?= base_url("metadata/dropdown"); ?>`,
                method: "GET",
                data: {
                    name: 'kategori_barang'
                },
                dataType: "json",
                success: function(res) {
                    $(".category").empty()
                    $(".category").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".category").append(`<option value="${item.id}">${item.value}</option>`)
                    })

                    $(".category").val("").change();
                }
            })

            $.ajax({
                url: `<?= base_url("barang/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".kode_barang").empty();

                    $(".kode_barang").append(`<option data-barang_id="" data-nama="" data-satuan="" data-stok="" data-harga="" value=""></option>`);

                    res.data.forEach(function(item) {
                        $(".kode_barang").append(`<option data-barang_id="${item.id}" data-nama="${item.nama_barang}" data-satuan="${item.satuan_id}" data-stok="${item.stok}" data-harga="${item.harga_barang}" value="${item.kode_barang}">${item.kode_barang} - ${item.nama_barang}</option>`);
                    })

                    $(".kode_barang").val("").change();
                }
            })

            $.ajax({
                url: `<?= base_url("satuan/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".satuan").empty();

                    $(".satuan").append(`<option value=""></option>`);

                    res.data.forEach(function(item) {
                        $(".satuan").append(`<option value="${item.id}">${item.nama_satuan}</option>`);
                    })

                    $(".satuan").val("").change();
                    $(".detail-modal").modal("show");
                }
            })
        })

        $(".btn-hide-detail").click(function() {
            $(".detail-modal").modal("hide")
        })

        $(".kode_barang").change(function() {
            if($(".kode_barang option:selected").val())
            {
                let nama = $(".kode_barang option:selected").data("nama") ? $(".kode_barang option:selected").data("nama") : "";
                let satuan = $(".kode_barang option:selected").data("satuan") ? $(".kode_barang option:selected").data("satuan") : "";
                let stok = $(".kode_barang option:selected").data("stok") ? $(".kode_barang option:selected").data("stok") : "";
                let harga = $(".kode_barang option:selected").data("harga") ? $(".kode_barang option:selected").data("harga") : "";
                let barang_id = $(".kode_barang option:selected").data("barang_id") ? $(".kode_barang option:selected").data("barang_id") : "";

                $(".nama_barang").attr("readonly", nama ? true : false);
                $(".new-barang").css("display", nama ? "none" : "");
                $(".category").val("").change();
                if(nama)
                {
                    $('.category').rules('remove', 'required');
                }
                else
                {
                    $('.category').rules('add', {
                        required: true
                    });
                }

                $(".kode").val($(".kode_barang option:selected").val());
                $(".nama_barang").val(nama);
                $(".barang_id").val(barang_id);
                $(".satuan").val(satuan).change();
                $(".qty").val(stok);
                $(".harga").val(harga ? harga.toLocaleString() : "");
                $(".total").val(harga || stok ? (harga * stok).toLocaleString() : "");
            }
            else
            {
                $(".new-barang").css("display", "none");
                $(".category").val("").change();
                $('.category').rules('remove', 'required');
                $(".nama_barang").attr("readonly", false)
                $(".kode").val("");
                $(".nama_barang").val("");
                $(".barang_id").val("");
                $(".satuan").val("").change();
                $(".qty").val("");
                $(".harga").val("");
                $(".total").val("");
            }
        })

        $(".harga, .qty").keyup(function () {
            let harga = $(".harga").val() ? $(".harga").val().replaceAll(",", "") : 0;
            let qty = $(".qty").val() ? parseInt($(".qty").val()) : 0;

            let total = (harga * qty).toLocaleString();
            $(".total").val(total);
        })

        // delete
        $(".delete-parent").click(function() {
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
                        url: "<?= base_url("spp/delete"); ?>",
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
                                        window.location.href = "<?= base_url("spp"); ?>"
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

        $(".btn-submit-detail").click(function() {
            let row_detail = $(".id_detail").val();
            let barang_id = $(".barang_id").val()
            let kode_barang = $(".kode").val()
            let category = $(".category option:selected").val()
            let nama_barang = $(".nama_barang").val()
            let nama_satuan = $(".satuan option:selected").text()
            let satuan = $(".satuan option:selected").val()
            let spesifikasi = $(".spesifikasi").val()
            let harga = $(".harga").val()
            let qty = $(".qty").val()
            let total = $(".total").val()
            let keterangan = $(".keterangan").val()

            // update detail
            if(row_detail)
            {
                if ($(".detail-form").valid()) {
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
                            console.log(id)
                            let new_list_items = []
                            let tag_html = "";
                            let tag_total = "";

                            row = 0;

                            $(".body-detail-table").empty()

                            total_harga_barang = 0;
                            total_qty = 0;
                            total_harga = 0;

                            list_items.map(item => {
                                if(item.row == row_detail)
                                {
                                    tag_html += `<tr>`;
                                    tag_html += `<td class="edit-table-detail" data-category="${category}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += row + 1;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-category="${category}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += kode_barang;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-category="${category}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += nama_barang;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-category="${category}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += nama_satuan;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-category="${category}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += spesifikasi;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-category="${category}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += harga;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-category="${category}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += qty;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-category="${category}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += total;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-category="${category}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += keterangan;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                                    tag_html += "</td>";
                                    tag_html += "</tr>";

                                    new_list_items.push({
                                        id: item.id,
                                        row: row + 1,
                                        category: category,
                                        barang_id: barang_id,
                                        kode_barang: kode_barang,
                                        nama_barang: nama_barang,
                                        nama_satuan: nama_satuan,
                                        satuan: satuan,
                                        spesifikasi: spesifikasi,
                                        harga: harga,
                                        qty: qty,
                                        total: total,
                                        keterangan: keterangan
                                    });

                                    row = row + 1;

                                    total_harga_barang = total_harga_barang + Number(harga.replaceAll(",", ""));
                                    total_qty = total_qty + Number(qty);
                                    total_harga = total_harga + Number(total.replaceAll(",", ""));
                                }
                                else
                                {
                                    tag_html += `<tr>`;
                                    tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += row + 1;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += item.kode_barang;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += item.nama_barang;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += item.nama_satuan;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += item.spesifikasi;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += item.harga;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += item.qty;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += item.total;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += item.keterangan;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                                    tag_html += "</td>";
                                    tag_html += "</tr>";

                                    new_list_items.push(item);

                                    row = row + 1;

                                    total_harga_barang = total_harga_barang + Number(item.harga.replaceAll(",", ""));
                                    total_qty = total_qty + Number(item.qty);
                                    total_harga = total_harga + Number(item.total.replaceAll(",", ""));
                                }
                            })

                            list_items = [];

                            list_items = new_list_items;

                            $(".body-detail-table").append(tag_html)

                            $(".foot-detail-table").empty()

                            tag_total += `<tr>`;
                            tag_total += "<td colspan='4'>";
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += "<b>TOTAL</b>";
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>${total_harga_barang.toLocaleString()}</b>`;
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>${total_qty}</b>`;
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                            tag_total += "</td>";
                            tag_total += "<td colspan='2'>";
                            tag_total += "</td>";
                            tag_total += "</tr>";

                            $(".foot-detail-table").append(tag_total);

                            $(".detail-modal").modal("hide")
                        }
                    })
                }
            }
            // create detail
            else
            {
                if ($(".detail-form").valid()) {
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
                            list_items.push({
                                id: '',
                                row: row + 1,
                                category: category,
                                barang_id: barang_id,
                                kode_barang: kode_barang,
                                nama_barang: nama_barang,
                                nama_satuan: nama_satuan,
                                satuan: satuan,
                                spesifikasi: spesifikasi,
                                harga: harga,
                                qty: qty,
                                total: total,
                                keterangan: keterangan
                            })

                            total_harga_barang = total_harga_barang + Number(harga.replaceAll(",", ""));
                            total_qty = total_qty + Number(qty);
                            total_harga = total_harga + Number(total.replaceAll(",", ""));

                            let tag_html = "";
                            let tag_total = "";

                            tag_html += `<tr>`;
                            tag_html += `<td class="edit-table-detail" data-category="${category}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="" data-row="${row + 1}">`;
                            tag_html += row + 1;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-category="${category}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="" data-row="${row + 1}">`;
                            tag_html += kode_barang;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-category="${category}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="" data-row="${row + 1}">`;
                            tag_html += nama_barang;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-category="${category}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="" data-row="${row + 1}">`;
                            tag_html += nama_satuan;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-category="${category}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="" data-row="${row + 1}">`;
                            tag_html += spesifikasi;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-category="${category}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="" data-row="${row + 1}">`;
                            tag_html += harga;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-category="${category}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="" data-row="${row + 1}">`;
                            tag_html += qty;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-category="${category}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="" data-row="${row + 1}">`;
                            tag_html += total;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-category="${category}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="" data-row="${row + 1}">`;
                            tag_html += keterangan;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                            tag_html += "</td>";
                            tag_html += "</tr>";
                            $(".body-detail-table").append(tag_html)

                            $(".foot-detail-table").empty()

                            tag_total += `<tr>`;
                            tag_total += "<td colspan='4'>";
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += "<b>TOTAL</b>";
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>${total_harga_barang.toLocaleString()}</b>`;
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>${total_qty}</b>`;
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                            tag_total += "</td>";
                            tag_total += "<td colspan='2'>";
                            tag_total += "</td>";
                            tag_total += "</tr>";

                            $(".foot-detail-table").append(tag_total);

                            $(".detail-modal").modal("hide")
                            row = row + 1;
                        }
                    })
                }
            }
        })
    })

    const deleteRow = function(id) {
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
                console.log(id)
                let new_list_items = []
                let tag_html = "";
                let tag_total = "";

                $(".body-detail-table").empty()

                row = 0;

                console.log(list_items)

                total_harga_barang = 0;
                total_qty = 0;
                total_harga = 0;

                list_items.map(item => {
                    if(item.row != id)
                    {
                        tag_html += `<tr>`;
                        tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += row + 1;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.kode_barang;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.nama_barang;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.nama_satuan;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.spesifikasi;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.harga;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.qty;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.total;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.keterangan;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_list_items.push({...item, row: row + 1});

                        row = row + 1;

                        total_harga_barang = total_harga_barang + Number(item.harga.replaceAll(",", ""));
                        total_qty = total_qty + Number(item.qty);
                        total_harga = total_harga + Number(item.total.replaceAll(",", ""));
                    }
                    else
                    {
                        // sent parameter isDelete if have customer id and id
                        if(item.id)
                        {
                            list_delete.push(item)
                        }
                    }
                })

                list_items = [];

                list_items = new_list_items;

                $(".body-detail-table").append(tag_html)

                $(".foot-detail-table").empty()

                tag_total += `<tr>`;
                tag_total += "<td colspan='4'>";
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += "<b>TOTAL</b>";
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += `<b>${total_harga_barang.toLocaleString()}</b>`;
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += `<b>${total_qty}</b>`;
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                tag_total += "</td>";
                tag_total += "<td colspan='2'>";
                tag_total += "</td>";
                tag_total += "</tr>";

                $(".foot-detail-table").append(tag_total);

                $(".detail-modal").modal("hide")
            }
        })
    }

    $(document).on('click', '.delete-detail', function() {
        let id = $(".id_detail").val()
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
                console.log(id)
                let new_list_items = []
                let tag_html = "";
                let tag_total = "";

                $(".body-detail-table").empty()

                total_harga_barang = 0;
                total_qty = 0;
                total_harga = 0;

                row = 0;

                console.log(list_items)

                list_items.map(item => {
                    if(item.row != id)
                    {
                        tag_html += `<tr>`;
                        tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += row + 1;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.kode_barang;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.nama_barang;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.nama_satuan;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.spesifikasi;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.harga;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.qty;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.total;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-category="${item.category}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.keterangan;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_list_items.push({...item, row: row + 1});

                        row = row + 1;

                        total_harga_barang = total_harga_barang + Number(item.harga.replaceAll(",", ""));
                        total_qty = total_qty + Number(item.qty);
                        total_harga = total_harga + Number(item.total.replaceAll(",", ""));
                    }
                    else
                    {
                        // sent parameter isDelete if have customer id and id
                        if(item.id)
                        {
                            list_delete.push(item)
                        }
                    }
                })

                list_items = [];

                list_items = new_list_items;

                $(".body-detail-table").append(tag_html)

                $(".foot-detail-table").empty()

                tag_total += `<tr>`;
                tag_total += "<td colspan='4'>";
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += "<b>TOTAL</b>";
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += `<b>${total_harga_barang.toLocaleString()}</b>`;
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += `<b>${total_qty}</b>`;
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                tag_total += "</td>";
                tag_total += "<td colspan='2'>";
                tag_total += "</td>";
                tag_total += "</tr>";

                $(".foot-detail-table").append(tag_total);

                $(".detail-modal").modal("hide")
            }
        })
    })

    $(document).on('click', '.edit-table-detail', function(evt) {
        $(".title-detail-name").text("Update")
        $(".delete-detail").css('display', '');
        let category = $(this).data('category')
        let barang_id = $(this).data('barang_id')
        let kode_barang = $(this).data('kode_barang')
        let nama_barang = $(this).data('nama_barang')
        let satuan = $(this).data('satuan')
        let spesifikasi = $(this).data('spesifikasi')
        let harga = $(this).data('harga')
        let qty = $(this).data('qty')
        let keterangan = $(this).data('keterangan')
        let rowid = $(this).data('row')
        let id = $(this).data('id')

        validator_detail.resetForm();
        validator_detail.reset();

        $(".id_detail").val(rowid)
        $(".kode").val(kode_barang)
        $(".spesifikasi").val(spesifikasi)
        $(".keterangan").val(keterangan)

        $.ajax({
            url: `<?= base_url("barang/dropdown"); ?>`,
            method: "GET",
            dataType: "json",
            success: function(res) {
                $(".kode_barang").empty();

                $(".kode_barang").append(`<option data-barang_id="" data-nama="" data-satuan="" data-stok="" data-harga="" value=""></option>`);

                if(category)
                {
                    $(".kode_barang").append(`<option selected data-barang_id="" data-nama="" data-satuan="" data-stok="" data-harga="" value="${kode_barang}">${kode_barang}</option>`);
                }

                res.data.forEach(function(item) {
                    if(kode_barang === item.kode_barang)
                    {
                        $(".kode_barang").append(`<option selected data-barang_id="${item.id}" data-nama="${item.nama_barang}" data-satuan="${item.satuan_id}" data-stok="${item.stok}" data-harga="${item.harga_barang}" value="${item.kode_barang}">${item.kode_barang} - ${item.nama_barang}</option>`);
                    }   
                    else
                    {
                        $(".kode_barang").append(`<option data-barang_id="${item.id}" data-nama="${item.nama_barang}" data-satuan="${item.satuan_id}" data-stok="${item.stok}" data-harga="${item.harga_barang}" value="${item.kode_barang}">${item.kode_barang} - ${item.nama_barang}</option>`);
                    }
                })
            }
        })

        $(".barang_id").val(barang_id)
        $(".nama_barang").val(nama_barang)

        if(category)
        {
            $(".nama_barang").attr("readonly", false);
            $('.category').rules('add', {
                required: true
            });
            $(".new-barang").css('display', '');
        }
        else
        {
            $(".nama_barang").attr("readonly", true);
            $('.category').rules('remove', 'required');
            $(".new-barang").css('display', 'none');
        }
        $.ajax({
            url: `<?= base_url("metadata/dropdown"); ?>`,
            method: "GET",
            data: {
                name: 'kategori_barang'
            },
            dataType: "json",
            success: function(res) {
                $(".category").empty()
                $(".category").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".category").append(`<option value="${item.id}">${item.value}</option>`)
                })

                $(".category").val(category).change();
            }
        })

        $.ajax({
            url: `<?= base_url("satuan/dropdown"); ?>`,
            method: "GET",
            dataType: "json",
            success: function(res) {
                $(".satuan").empty();

                $(".satuan").append(`<option value=""></option>`);

                res.data.forEach(function(item) {
                    $(".satuan").append(`<option value="${item.id}">${item.nama_satuan}</option>`);
                })

                $(".satuan").val(satuan).change();
                $(".harga").val(harga)
                $(".qty").val(qty)
                $(".total").val((harga.replaceAll(",", "") * parseInt(qty)).toLocaleString())
                $(".detail-modal").modal("show");
            }
        })
    })

    const changeStatus = function()
    {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if(value)
        {
            $(".spp_no").attr("readonly", true);
            $(".spp_no").val("AUTO GENERATE");
        }
        else
        {
            $(".spp_no").attr("readonly", false);
            $(".spp_no").val("");
        }
    }
</script>
<?= $this->endSection(); ?>