<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1 class="title-name">Tambah</h1>
    <?php if(!empty($dataPOLokal)){ ?> 
    <button class="btn btn-primary delete-parent">
        <i class="fa fa-trash" aria-hidden="true"></i>
    </button>
    <button class="btn btn-warning">
        <i class="fa fa-print" aria-hidden="true"></i>
    </button>
    <button class="btn btn-success posting-spp">
        <i class="fa fa-paper-plane" aria-hidden="true"></i>
    </button>
    <?php } ?> 
    <a class="btn btn-hide-form" href="<?= base_url("po-lokal"); ?>">
        Batal
    </a>
    <button class="btn btn-submit-form btn-submit-parent">
        Simpan
    </button>
</div>
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label font-weight-bold">Data PO</label>
            </div>
        </div>
        <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
            <input type="hidden" class="id" name="id" id="id" value="<?= !empty($dataPOLokal) ? $dataPOLokal->id : ""; ?>" />
            <?= csrf_field() ?>
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select purchase_request_id" id="purchase_request_id" name="purchase_request_id" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php
                            if (!empty($dataSPP)) {
                                foreach ($dataSPP as $spp) {
                            ?>
                                    <option value="<?= $spp->id; ?>"><?= $spp->spp_no; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <label for="floatingInput">No. SPP</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= !empty($dataPOLokal) ? 'readonly=true' : ''; ?> type="text" class="form-control po_no" id="po_no" name="po_no" placeholder="No. PO">
                                <label for="floatingInput">No. PO</label>
                            </div>
                            <div style="<?= !empty($dataPOLokal) ? "display: none;" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                <input style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input class="form-control input-picker po_date" id="po_date" name="po_date" placeholder="Tanggal Dibuat">
                                <label for="floatingInput">Tanggal Dibuat</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <input type="hidden" class="form-control warehouse_id" id="warehouse_id" name="warehouse_id">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" readonly="true" class="form-control warehouse" id="warehouse" name="warehouse" placeholder="Gudang">
                        <label for="floatingInput">Gudang</label>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" readonly="true" class="form-control order_type" id="order_type" name="order_type" placeholder="Jenis Order">
                        <label for="floatingInput">Jenis Order</label>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input onkeyup="formatNumber(this)" type="text" class="form-control dpp" id="dpp" name="dpp" placeholder="DPP">
                        <label for="floatingInput">DPP</label>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">Data Supplier</label>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select supplier_id" id="supplier_id" name="supplier_id" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php
                            if (!empty($dataSupplier)) {
                                foreach ($dataSupplier as $supplier) {
                            ?>
                                    <option value="<?= $supplier->id; ?>" data-name="<?= $supplier->name; ?>"><?= $supplier->kode; ?> - <?= $supplier->name; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <label for="floatingInput">Kode Supplier</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" readonly="true" class="form-control supplier" id="supplier" name="supplier" placeholder="Nama Supplier">
                        <label for="floatingInput">Nama Supplier</label>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">Data Pembayaran</label>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select foreign_exchange" id="foreign_exchange" name="foreign_exchange" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php
                            if (!empty($dataValuta)) {
                                foreach ($dataValuta as $valuta) {
                            ?>
                                    <option value="<?= $valuta->id; ?>"><?= $valuta->value; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <label for="floatingInput">Valas</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control payment_term" name="payment_term" id="payment_term" placeholder="Termin Pembayaran / Bulan">
                        <label for="floatingInput">Termin Pembayaran / Bulan</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input class="form-control input-picker payment_date" id="payment_date" name="payment_date" placeholder="Tanggal Pembayaran">
                                <label for="floatingInput">Tanggal Pembayaran</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-payment-date"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" class="form-control note" id="note" name="note" placeholder="Catatan (Opsional)">
                        <label for="floatingInput">Catatan (Opsional)</label>
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
                        <th>Harga Barang</th>
                        <th>Qty</th>
                        <th>Disc %</th>
                        <th>Biaya Tambahan</th>
                        <th>Total Harga</th>
                        <th>PPN</th>
                        <th>PPH</th>
                        <th>Hapus</th>
                    </tr>
                </thead>
                <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">

                </tbody>
                <tfoot class="foot-detail-table" id="foot-detail-table">
                    
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
                    <input type="hidden" class="barang_id" name="barang_id" id="barang_id" />
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
                                <input type="text" class="form-control nama_barang" id="nama_barang" name="nama_barang" placeholder="Nama Barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control spesifikasi" name="spesifikasi" id="spesifikasi" placeholder="Spesifikasi">
                                <label for="floatingInput">Spesitifikasi</label>
                            </div>
                            <div class="col-md-6 new-barang" style="display: none;">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select category" name="category" id="category" aria-label="Floating label select example">
                                        <option value=""></option>
                                    </select>
                                    <label for="floatingInput">Kategori</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Data Harga</h5>
                        </div>
                    </div>
                    <div class="row mb-3">
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
                                <input type="number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                                <label for="floatingInput">Qty</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onkeyup="formatNumber(this)" type="text" class="form-control harga" name="harga" id="harga" placeholder="Harga Satuan">
                                <label for="floatingInput">Harga Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control disc" name="disc" id="disc" placeholder="Diskon %">
                                <label for="floatingInput">Diskon %</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onkeyup="formatNumber(this)" type="text" class="form-control additional_cost" name="additional_cost" id="additional_cost" placeholder="Biaya Tambahan">
                                <label for="floatingInput">Biaya Tambahan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" readonly="true" class="form-control total" name="total" id="total" placeholder="Total">
                                <label for="floatingInput">Total</label>
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
            disc: {
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
            disc: {
                required: "Diskon % wajib diisi"
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
        $(".po_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $(".payment_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        // PURCHASE REQUEST ID
        $('.purchase_request_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.purchase_request_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.purchase_request_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.purchase_request_id')
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

        // FOREIGN EXHANGE
        $('.foreign_exchange').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.foreign_exchange')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.foreign_exchange')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.foreign_exchange')
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

        $('.icon-po-date').click(function() {
            $(".po_date").focus();
        });

        $('.icon-payment-date').click(function() {
            $(".payment_date").focus();
        });

        var validator = $(".create-form").validate({
            rules: {
                purchase_request_id: {
                    required: true
                },
                po_date: {
                    required: true
                },
                supplier_id: {
                    required: true
                },
                payment_term: {
                    required: true,
                },
                foreign_exchange: {
                    required: true,
                },
                payment_date: {
                    required: true,
                }
            },
            messages: {
                purchase_request_id: {
                    required: "No. SPP wajib diisi"
                },
                po_date: {
                    required: "Tanggal Dibuat wajib diisi"
                },
                supplier_id: {
                    required: "Supplier wajib diisi"
                },
                payment_term: {
                    required: "Termin Pembayaran / Bulan wajib diisi"
                },
                foreign_exchange: {
                    required: "Valas wajib diisi"
                },
                payment_date: {
                    required: "Tanggal Pembayaran wajib diisi"
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

        $(".disc").keyup(function() {
            if($(".disc").val())
            {
                if($(".disc").val() > 100)
                {
                    $(".disc").val(100)
                }
                if($(".disc").val() < 0)
                {
                    $(".disc").val();
                }
            }
            else
            {
                $(".disc").val();
            }
        })

        $(".supplier_id").change(function() {
            if($(".supplier_id option:selected").val())
            {
                let name = $(".supplier_id option:selected").data("name") ? $(".supplier_id option:selected").data("name") : "";
                $(".supplier").val(name);
            }
            else
            {
                $(".supplier").val("");
            }
        })

        $(".purchase_request_id").change(function() {
            if($(".purchase_request_id option:selected").val())
            {
                $.ajax({
                    url: `<?= base_url("spp/ajax"); ?>`,
                    method: "GET",
                    data: {
                        id: $(".purchase_request_id option:selected").val()
                    },
                    dataType: "json",
                    success: function(res) {
                        if(res.status)
                        {
                            $(".warehouse_id").val(res?.data?.warehouse_id)
                            $(".warehouse").val(res?.data?.warehouseName)
                            $(".order_type").val(res?.data?.order_type)
                        }
                        else
                        {
                            $(".warehouse_id").val()
                            $(".warehouse").val()
                            $(".order_type").val()

                            Swal.fire({
                                icon: 'error',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    }
                })
            }
            else
            {

            }
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

        $(".btn-submit-detail").click(function() {
            let row_detail = $(".id_detail").val();

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

                        }
                    })
                }
            }
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

                            data.append("items", JSON.stringify(update_list_items))

                            let id = $(".id").val();
                            // UPDATE
                            if(id)
                            {
                                $.ajax({
                                    url: "<?= base_url("po-lokal/update"); ?>",
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
                                    url: "<?= base_url("po-lokal/save"); ?>",
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
                                                window.location.href = "<?= base_url("po-lokal"); ?>";
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
    })

    const changeStatus = function()
    {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if(value)
        {
            $(".po_no").attr("readonly", true);
            $(".po_no").val("AUTO GENERATE");
        }
        else
        {
            $(".po_no").attr("readonly", false);
            $(".po_no").val("");
        }
    }
</script>
<?= $this->endSection(); ?>