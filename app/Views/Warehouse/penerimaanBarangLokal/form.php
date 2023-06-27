<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1 class="title-name">Tambah</h1>
    <a class="btn btn-hide-form" href="<?= base_url("penerimaan-barang-lokal"); ?>">
        Batal
    </a>
    <button class="btn btn-submit-form btn-submit-parent">
        Simpan
    </button>
</div>
<div class="card">
    <div class="card-body">
        <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
            <input type="hidden" class="id" name="id" id="id" />
            <?= csrf_field() ?>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">Data PO</label>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control no_penerimaan_barang" id="no_penerimaan_barang" name="no_penerimaan_barang" placeholder="No. Penerimaan">
                                <label for="floatingInput">No. Penerimaan</label>
                            </div>
                            <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                <input style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select supplier_id" id="supplier_id" name="supplier_id" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php
                            if (!empty($dataSupplier)) {
                                foreach ($dataSupplier as $supplier) {
                            ?>
                                    <option value="<?= $supplier->id; ?>"><?= $supplier->name; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <label for="floatingInput">Supplier</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select disabled="true" multiple class="form-select multiple_po_id" name="multiple_po_id[]" aria-label="Floating label select example">
                            <option value=""></option>
                        </select>
                        <label for="floatingInput">No. PO</label>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">Data Dokumen</label>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select aju_document_type" id="aju_document_type" name="aju_document_type" aria-label="Floating label select example">
                            <option value="NON PABEAN">NON PABEAN</option>
                        </select>
                        <label for="floatingInput">Jenis Dokumen</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" class="form-control aju_no" name="aju_no" id="aju_no" placeholder="No. Invoice">
                        <label for="floatingInput">No. AJU</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control input-picker validation_date" id="validation_date" name="validation_date" placeholder="Tanggal Pendaftaran">
                            <label for="floatingInput">Tanggal Pendaftaran</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control no_registration" id="no_registration" name="no_registration" placeholder="No. Pendaftaran">
                            <label for="floatingInput">No. Pendaftaran</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">No. Surat Jalan</label>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" class="form-control letter_no" id="letter_no" name="letter_no" placeholder="No. Surat Jalan">
                        <label for="floatingInput">No. Surat Jalan</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" class="form-control invoice_no" id="invoice_no" name="invoice_no" placeholder="No. Invoice">
                        <label for="floatingInput">No. Invoice</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" class="form-control packaging" id="packaging" name="packaging" placeholder="Kemasan">
                        <label for="floatingInput">Kemasan</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="number" class="form-control total_weight" id="total_weight" name="total_weight" placeholder="Berat">
                        <label for="floatingInput">Berat</label>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input onkeyup="formatNumber(this)" type="text" class="form-control shipping_cost" name="shipping_cost" id="shipping_cost" placeholder="Biaya Ongkos Kirim">
                        <label for="floatingInput">Biaya Ongkos Kirim</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input onkeyup="formatNumber(this)" type="text" class="form-control biaya_masuk" name="biaya_masuk" id="biaya_masuk" placeholder="Biaya Masuk">
                        <label for="floatingInput">Biaya Masuk</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input onkeyup="formatNumber(this)" type="text" class="form-control ppn" id="ppn" name="ppn" placeholder="PPNBM">
                        <label for="floatingInput">PPNBM</label>
                    </div>
                </div>
            </div>
        </form>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label font-weight-bold">List Barang</label>
            </div>
            <div class="col-md-6">
                <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal">
                    <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                </button>
            </div>
        </div>
        <div class="table-responsive mt-2">
            <table class="table-inside nowrap table-hover-tobasurimi" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Spesifikasi</th>
                        <th>Satuan</th>
                        <th>Jml. Order</th>
                        <th>Jml. Dokumen</th>
                        <th>Selisih</th>
                        <th>Konversi</th>
                        <th>Harga</th>
                        <th>Penyerahan</th>
                        <th>Keterangan</th>
                        <th>Hapus</th>
                    </tr>
                </thead>
                <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">
                <?php 
                    $no = 1;
                    $total_jml_order = 0;
                    $total_jml_dokumen = 0;
                    $total_selisih = 0;
                    $total_konversi = 0;
                    $total_harga = 0;
                    $total_penyerahan = 0;
                ?>
                </tbody>
                <tfoot class="foot-detail-table" id="foot-detail-table">
                    <tr>
                        <td align="center" colspan="5">TOTAL</td>
                        <td><b><?= number_format($total_jml_order); ?></b></td>
                        <td><b><?= $total_jml_dokumen; ?></b></td>
                        <td><b><?= number_format($total_selisih); ?></b></td>
                        <td><b><?= number_format($total_konversi); ?></b></td>
                        <td><b><?= number_format($total_harga); ?></b></td>
                        <td><b><?= number_format($total_penyerahan); ?></b></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
</section>

<div class="modal detail-modal" tabindex="1">
    <div class="modal-dialog" style="max-width: 1200px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-detail-name"></label> Barang</h5>
            </div>
            <div class="modal-body">
                <form class="detail-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id_detail" name="id_detail" id="id_detail" />
                    <input type="hidden" class="purchase_order_details_id" name="purchase_order_details_id" id="purchase_order_details_id" />
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Data Barang</h5>
                        </div>
                    </div>
                    <div class="row mb-3">
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
                                <input type="text" readonly="true" class="form-control nama_barang" id="nama_barang" name="nama_barang" placeholder="Nama Barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" readonly="true" class="form-control satuan_order" id="satuan_order" name="satuan_order" placeholder="Satuan Order">
                                <label for="floatingInput">Satuan Order</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" readonly="true" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control jumlah_order" id="jumlah_order" name="jumlah_order" placeholder="Jumlah Order">
                                <label for="floatingInput">Jumlah Order</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" readonly="true" class="form-control keterangan" id="keterangan" name="keterangan" placeholder="Keterangan">
                                <label for="floatingInput">Keterangan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Detail Barang di Dokumen</h5>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control nama_barang_dokumen" id="nama_barang_dokumen" name="nama_barang_dokumen" placeholder="Nama Barang di dokumen">
                                <label for="floatingInput">Nama Barang di dokumen</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control jumlah_di_dokumen" id="jumlah_di_dokumen" name="jumlah_di_dokumen" placeholder="Jumlah di dokumen">
                                <label for="floatingInput">Jumlah di dokumen</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onkeyup="formatNumber(this)" type="text" class="form-control harga_barang_jasa" name="harga_barang_jasa" id="harga_barang_jasa" placeholder="Harga barang/jasa">
                                <label for="floatingInput">Harga barang/jasa</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onkeyup="formatNumber(this)" type="text" class="form-control nilai_penyerahan" name="nilai_penyerahan" id="nilai_penyerahan" placeholder="Nilai Penyerahan">
                                <label for="floatingInput">Nilai Penyerahan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Data Tax</h5>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select ppn" name="ppn" id="ppn" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">PPN</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select pph" name="pph" id="pph" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">PPH</label>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="col-subtitle-modal">
                    <div class="row mt-5">
                        <div class="col-md-6">
                            <h5 class="modal-sub-title">Aktual Penerimaan</h5>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-add-row btn-add btn-block float-right" style="width: 106px;">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive mt-2">
                    <table class="table-inside nowrap table-hover-tobasurimi" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Warehouse</th>
                                <th>Qty</th>
                                <th>Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-warehouse" id="body-detail-warehouse" style="cursor: pointer;">

                        </tbody>
                    </table>
                </div>
                <!-- <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control jumlah_masuk" id="jumlah_masuk" name="jumlah_masuk" placeholder="Jumlah Masuk">
                            <label for="floatingInput">Jumlah Masuk</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control konversi" id="konversi" name="konversi" placeholder="Konversi">
                            <label for="floatingInput">Konversi</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input onkeyup="formatNumber(this)" type="text" class="form-control jumlah_penerimaan" name="jumlah_penerimaan" id="jumlah_penerimaan" placeholder="Jumlah Penerimaan">
                            <label for="floatingInput">Jumlah Penerimaan</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" readonly="true" class="form-control satuan" id="satuan" name="satuan" placeholder="Satuan">
                            <label for="floatingInput">Satuan </label>
                        </div>
                    </div>
                </div> -->
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-discard delete-detail">Hapus</button>
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
    let list_warehouse = [];
    var row = 0;
    var row_detail = 0;
    let total_jml_order = 0;
    let total_jml_dokumen = 0;
    let total_jml_selisih = 0;
    let total_jml_konversi = 0;
    let total_jml_harga = 0;
    let total_jml_penyerahan = 0;

    var validator_detail = $(".detail-form").validate({
        rules: {
            kode_barang: {
                required: true
            },
            doc_qty: {
                required: true
            },
            qty: {
                required: true
            },
            selisih: {
                required: true
            },
            konversi: {
                required: true
            },
            harga: {
                required: true
            },
            penyerahan: {
                required: true
            },
        },
        messages: {
            kode_barang: {
                required: "Kode wajib diisi"
            },
            doc_qty: {
                required: "Document Qty wajib diisi"
            },
            qty: {
                required: "Qty wajib diisi"
            },
            selisih: {
                required: "Selisih wajib diisi"
            },
            konversi: {
                required: "Konversi wajib diisi"
            },
            harga: {
                required: "Harga wajib diisi"
            },
            penyerahan: {
                required: "Penyerahan wajib diisi"
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

    $(document).ready(function() {
        var validator = $(".create-form").validate({
            rules: {
                supplier_id: {
                    required: true
                },
                "multiple_po_id[]": {
                    required: true
                },
                aju_document_type: {
                    required: true
                },
                aju_no: {
                    required: true,
                },
                validation_date: {
                    required: true,
                },
                no_registration: {
                    required: true,
                },
                letter_no: {
                    required: true,
                },
                invoice_no: {
                    required: true,
                },
                packaging: {
                    required: true,
                },
                total_weight: {
                    required: true,
                },
                shipping_cost: {
                    required: true,
                },
                biaya_masuk: {
                    required: true,
                },
                ppn: {
                    required: true,
                },
                status_post: {
                    required: true,
                },
                status_penerimaan: {
                    required: true,
                }
            },
            messages: {
                supplier_id: {
                    required: "Supplier wajib diisi"
                },
                aju_document_type: {
                    required: "Jenis Dokumen wajib diisi"
                },
                aju_no: {
                    required: "No. AJU wajib diisi"
                },
                validation_date: {
                    required: "Tanggal wajib diisi"
                },
                no_registration: {
                    required: "No. Registrasi wajib diisi"
                },
                letter_no: {
                    required: "No. Surat wajib diisi"
                },
                invoice_no: {
                    required: "No. Invoice wajib diisi"
                },
                packaging: {
                    required: "Packaging wajib diisi"
                },
                total_weight: {
                    required: "Weight wajib diisi"
                },
                shipping_cost: {
                    required: "Biaya Pengiriman wajib diisi"
                },
                biaya_masuk: {
                    required: "Biaya Masuk wajib diisi"
                },
                ppn: {
                    required: "PPN wajib diisi"
                },
                status_post: {
                    required: "Status Post wajib diisi"
                },
                status_penerimaan: {
                    required: "Status Penerimaan wajib diisi"
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
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
                $(element).addClass('select-class');                      

            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
                $(element).removeClass('select-class');   
            },
        });

        $(".validation_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        // PO NO
        $('.multiple_po_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.multiple_po_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.multiple_po_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.multiple_po_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // SUPPLIER
        $('.supplier_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.supplier_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.supplier_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.supplier_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // KODE BARANG
        $('.kode_barang').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content"),
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

        // PPN
        $('.ppn').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content"),
            allowClear: true
        })

        //CSS SELECT2 FLOATING LABEL
        $('.ppn')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.ppn')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.ppn')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // PPH
        $('.pph').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content"),
            allowClear: true
        })

        //CSS SELECT2 FLOATING LABEL
        $('.pph')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.pph')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.pph')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $(".btn-submit-detail").click(function() {
            let row_detail = $(".id_detail").val();
            let barang_id = $(".barang_id").val()

            // update detail
            if(row_detail)
            {

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
                                barang_id: barang_id,
                                doc_qty: 0,
                                qty: 0,
                                selisih: 0,
                                konversi: 0,
                                harga: 0,
                                penyerahan: 0,
                                keterangan: 0,
                                ppn: 0,
                                pph: 0,
                                warehouse: []
                            })

                            let tag_html = "";
                            let tag_total = "";

                            tag_html += `<tr>`;
                            tag_html += `<td class="edit-table-detail" data-id="" data-row="${row + 1}">`;
                            tag_html += row + 1;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-id="" data-row="${row + 1}">`;
                            tag_html += row + 1;
                            tag_html += "</td>";
                            tag_html += "</tr>";
                            $(".body-detail-table").append(tag_html)

                            $(".foot-detail-table").empty()

                            $(".foot-detail-table").append(tag_total);

                            $(".detail-modal").modal("hide")
                            row = row + 1;
                        }
                    })
                }
            }
        })

        $(".btn-submit-parent").click(function() {
            $(".detail-modal").modal("hide")

            // CHECK IF NO BARANG
            // if(list_items.length === 0)
            // {
            //     Swal.fire({
            //         icon: 'error',
            //         title: "Barang Tidak Boleh Kosong",
            //         confirmButtonColor: '#4e73df',
            //     })
            // }
            // else
            // {
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
                            
                            list_items.map(obj => {
                                update_list_items.push(
                                    {

                                    }
                                )
                            })
                            data.append("multiple_po_id", JSON.stringify($('.multiple_po_id').val()));
                            var arr_no = $('.multiple_po_id').select2('data').map(function(elem){ 
                                return elem.text 
                            });
                            console.log(arr_no)
                            data.append("multiple_po_no", JSON.stringify(arr_no));
                            data.append("items", JSON.stringify(update_list_items))

                            let id = $(".id").val();
                            // UPDATE
                            if(id)
                            {
                                $.ajax({
                                    url: "<?= base_url("penerimaan-barang-lokal/update"); ?>",
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
                                                window.location.href = "<?= base_url("po-lokal"); ?>" + "/id/" + id;
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
                                    url: "<?= base_url("penerimaan-barang-lokal/save"); ?>",
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
                                                window.location.href = "<?= base_url("penerimaan-barang-lokal"); ?>";
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
            // }
        })

        $(".btn-add-row").click(function() {
            row_detail++;
            list_warehouse.push(
            {
                row: row_detail,
                display: "",
                warehouse_id: "",
                qty_warehouse: ""
            })
            
            let tag_html = "";
            tag_html += `<tr class="table_${row_detail}">`;
            tag_html += `<td>`;
            tag_html += `<select onchange="changeWarehouse(${row_detail})" class="warehouse_id_${row_detail} form-select" id="warehouse_id_${row_detail}" name="warehouse_id_${row_detail}">`;
            tag_html += `</td>`;
            tag_html += `<td>`;
            tag_html += `<input onkeyup="changeWarehouse(${row_detail})" oninput="this.value=this.value.replace(/[^0-9]/g,'');" type="text" class="form-control qty_warehouse_${row_detail}" id="qty_warehouse_${row_detail}" name="qty_warehouse_${row_detail}">`;
            tag_html += `</td>`;
            tag_html += `<td>`;
            tag_html += `<button onclick='deleteChildRow(${row_detail})'>X</button>`;
            tag_html += `</td>`;
            tag_html += `</tr>`;

            $.ajax({
                url: `<?= base_url("warehouse/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".warehouse_id_" + row_detail).empty()
                    $(".warehouse_id_" + row_detail).append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".warehouse_id_" + row_detail).append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                    })
                }
            })

            $(".body-detail-warehouse").append(tag_html)

            // WAREHOUSE
            $(".warehouse_id_" + row_detail).select2({
                placeholder: "",
                theme: "bootstrap-5",
                dropdownParent: $(".detail-modal .modal-content")
            })
        })

        $(".btn-show-detail").click(function() {
            if($('.multiple_po_id option:selected').length !== 0)
            {
            $(".delete-detail").css('display', 'none');

            $(".title-detail-name").text("Tambah");
            $(".id_detail").val('');

            $.ajax({
                url: `<?= base_url("po-lokal/multi/dropdown"); ?>`,
                method: "GET",
                data: {
                    id: JSON.stringify($('.multiple_po_id').val())
                },
                dataType: "json",
                success: function(res) {
                    console.log(res)
                    $(".kode_barang").empty()
                    $(".kode_barang").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".kode_barang").append(`<option data-nama="${item.barang.nama_barang}" data-note="${item.note}" data-id="${item.id}" data-qty="${item.qty}" data-satuan="${item.barang.nama_satuan}" value="${item.barang.id}">${item.barang.kode_barang} - ${item.barang.nama_barang}</option>`)
                    })

                    $(".kode_barang").val("").change();
                }
            })

            $.ajax({
                url: `<?= base_url("tax/dropdown"); ?>`,
                method: "GET",
                data: {
                    type: 'ppn'
                },
                dataType: "json",
                success: function(res) {
                    $(".ppn").empty()
                    $(".ppn").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".ppn").append(`<option value="${item.id}">${item.tax_value}</option>`)
                    })

                    $(".ppn").val("").change();
                }
            })

            $.ajax({
                url: `<?= base_url("tax/dropdown"); ?>`,
                method: "GET",
                data: {
                    type: 'pph'
                },
                dataType: "json",
                success: function(res) {
                    $(".pph").empty()
                    $(".pph").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".pph").append(`<option value="${item.id}">${item.tax_value}</option>`)
                    })

                    $(".pph").val("").change();
                    $(".detail-modal").modal("show")
                }
            })
            }
            else
            {
                Swal.fire({
                    icon: 'error',
                    title: "No. PO Wajib Diisi",
                    confirmButtonColor: '#4e73df',
                })
            }
        })

        $(".btn-hide-detail").click(function() {
            $(".detail-modal").modal("hide")
        })

        $(".kode_barang").change(function() {
            if($(".kode_barang option:selected").val())
            {
                let nama = $(".kode_barang option:selected").data("nama") ? $(".kode_barang option:selected").data("nama") : "";
                let barang_id = $(".kode_barang option:selected").val() ? $(".kode_barang option:selected").val() : "";
                let po_id = $(".kode_barang option:selected").data("id") ? $(".kode_barang option:selected").data("id") : "";
                let satuan = $(".kode_barang option:selected").data("satuan") ? $(".kode_barang option:selected").data("satuan") : "";
                let note = $(".kode_barang option:selected").data("note") ? $(".kode_barang option:selected").data("note") : "";
                let qty = $(".kode_barang option:selected").data("qty") ? $(".kode_barang option:selected").data("qty") : 0;

                $(".kode").val($(".kode_barang option:selected").val());
                $(".nama_barang").val(nama);
                $(".barang_id").val(barang_id);
                $(".purchase_order_details_id").val(po_id);
                $(".satuan_order").val(satuan);
                $(".jumlah_order").val(qty);
                $(".keterangan").val(note);
            }
            else
            {
                $(".kode").val("");
                $(".nama_barang").val("");
                $(".barang_id").val("");
                $(".purchase_order_details_id").val("");
                $(".satuan_order").val("");
                $(".jumlah_order").val("");
                $(".keterangan").val("");
            }
        })

        $(".supplier_id").change(function() {
            if($(".supplier_id option:selected").val())
            {
                $.ajax({
                    url: `<?= base_url("po-lokal/dropdown"); ?>`,
                    method: "GET",
                    data: {
                        id: $(".supplier_id option:selected").val()
                    },
                    dataType: "json",
                    success: function(res) {
                        console.log(res)
                        $(".multiple_po_id").attr("disabled", true)
                        $(".multiple_po_id").empty()
                        $(".multiple_po_id").append(`<option value=""></option>`)
                        res.data.forEach(function(item) {
                            $(".multiple_po_id").append(`<option value="${item.id}">${item.po_no}</option>`)
                        })
                        $(".multiple_po_id").attr("disabled", false)
                        $(".multiple_po_id").val([]);
                    }
                })
            }
            else
            {
                $(".multiple_po_id").attr("disabled", true)
                $(".multiple_po_id").empty()
                $(".multiple_po_id").append(`<option value=""></option>`)
                $(".multiple_po_id").val([]);
            }
        })
    })

    const changeWarehouse = function(id)
    {
        let new_list_warehouse = []
        list_warehouse.forEach((item) => {
            if(item.row !== id)
            {
                new_list_warehouse.push(item)
            }
            else
            {
                new_list_warehouse.push({display: "", warehouse_id: $(".warehouse_id_" + id + " option:selected").val(), qty_warehouse: $(".qty_" + id).val()})
            }
        })

        list_warehouse = new_list_warehouse;
    } 

    const deleteChildRow = function(id) {
        $(".table_" + id).css("display", "none")
        let new_list_warehouse = []
        list_warehouse.forEach((item) => {
            if(item.row !== id)
            {
                new_list_warehouse.push(item)
            }
            else
            {
                new_list_warehouse.push({warehouse_id: item.warehouse_id, qty_warehouse: item.qty_warehouse, display: "none"})
            }
        })

        list_warehouse = new_list_warehouse;
    }

    const changeStatus = function()
    {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if(value)
        {
            $(".no_penerimaan_barang").attr("readonly", true);
            $(".no_penerimaan_barang").val("AUTO GENERATE");
        }
        else
        {
            $(".no_penerimaan_barang").attr("readonly", false);
            $(".no_penerimaan_barang").val("");
        }
    }
</script>

<?= $this->endSection(); ?>