<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>


<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($data) ? "Ubah" : "Tambah"; ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("order-form-lokal"); ?>">
                Batal
            </a>

            <?php if (!empty($data)): ?>
            <!-- <a class="btn btn-save float-right" href="#"> -->
            <a class="btn btn-warning btn-print float-right" href="<?= base_url("order-form-lokal/print/{$data->id}"); ?>">
                Print
            </a>
            <?php endif; ?>

            <button class="btn btn-show-form btn-save float-right btn-submit">
                Simpan
            </button>

        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-order-form-lokal" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($data) ? $data->id : ""; ?>" />
                <input autocomplete="one-time-code" type="hidden" class="tipe_sales_order" name="tipe_sales_order" id="tipe_sales_order" value="LOKAL" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control sales_name" disabled=true value="<?= $data->no_sales_order ?? '' ?>">
                            <label for="floatingInput">No. SO</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" class="form-control input-picker order_date" id="order_date" name="order_date" <?= !empty($data) ? ($data->order_date === true ? 'disabled=true' : '') : ''; ?> value="<?= !empty($data) ? $data->order_date : ""; ?>" placeholder="Tanggal Pemesanan">
                            <label for="floatingInput">Tanggal Pemesanan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control sales_name" id="sales_name" name="sales_name" disabled=true value="<?= $data->salesName ?? '' ?>">
                            <input autocomplete="one-time-code" type="hidden" class="form-control sales_name" id="id_user" name="id_user" value="<?= $id_user ?>">
                            <label for="floatingInput">Nama Sales</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select id_customer" name="id_customer" id="id_customer" <?= !empty($data) ? ($data->id_customer === true ? 'disabled=true' : '') : ''; ?>>
                                <option value=""></option>
                                <?php foreach ($dataCustomers ?? [] as $customer): ?>
                                <option value="<?= $customer['id']; ?>" data-address="<?= rawurlencode($customer['address']) ?>" data-termin="<?= rawurlencode($customer['termin']) ?>" data-salesname="<?= rawurlencode($customer['salesName']); ?>" <?= !empty($data) ? ($data->id_customer === $customer['id'] ? "selected" : "") : ""; ?>><?= $customer['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Nama Customer</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control" id="termin" name="termin" disabled value="<?= $data->termin ?? '' ?>">
                            <label for="floatingInput">Termin</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control" id="tagihan_ke" name="tagihan_ke" disabled value="<?= $data->address ?? '' ?>">
                            <label for="floatingInput">Tagihan Ke</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" class="form-control input-picker shipping_date" id="shipping_date" name="shipping_date" <?= !empty($data) ? ($data->shipping_date === true ? 'disabled=true' : '') : ''; ?> value="<?= !empty($data) ? $data->shipping_date : ""; ?>" placeholder="End of time">
                            <label for="floatingInput">Tanggal Pengiriman</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" class="form-control input-picker" id="estimated_freight" name="estimated_freight" onkeyup="formatNumber(this)" value="<?= number_format($data->estimated_freight ?? 0); ?>" placeholder="End of time">
                            <label for="floatingInput">Biaya Kirim</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control payment_terms" id="payment_terms" name="payment_terms" <?= !empty($data) ? ($data->payment_terms === true ? 'disabled=true' : '') : ''; ?> placeholder="Terms" value="<?= !empty($data) ? $data->payment_terms : ""; ?>">
                            <label for="floatingInput">Terms</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating ff-ket mb-3" style="height: 80px;">
                            <textarea autocomplete="one-time-code" <?= !empty($data) ? ($data->keterangan === true ? 'disabled=true' : '') : ''; ?> class="form-control parent_keterangan text-area-all" style="height: 100%" id="parent_keterangan" name="parent_keterangan" placeholder="keterangan"><?= !empty($data) ? $data->keterangan : ""; ?></textarea>
                            <label for="floatingInput">Keterangan</label>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="mb-3" style="height: 50px;">
                            <label for="floatingInput">Pajak</label>
                            <div class="switch-form-pinjaman-karyawan">
                                <label class="switch">
                                    <input autocomplete="one-time-code" class="tax_status" <?= !empty($data) ? ($data->tax_status === true ? 'disabled=true' : '') : ''; ?> name="tax_status" id="tax_status" type="checkbox" <?= !empty($data) ? ($data->tax_status == 'true' ? 'checked' : '') : ''; ?>>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3" style="height: 50px;">
                            <label for="floatingInput">Include Pajak</label>
                            <div class="switch-form-pinjaman-karyawan">
                                <label class="switch">
                                    <input autocomplete="one-time-code" class="include_tax" <?= !empty($data) ? ($data->include_pa === true ? 'disabled=true' : '') : ''; ?> name="include_tax" id="include_tax" type="checkbox" <?= !empty($data) ? ($data->include_pa == 'true' ? 'checked' : '') : ''; ?>>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

            </form>

            <!-- list barang -->
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Barang</label>
                    </div>

                    <div class="col-md-6">
                        <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal">
                            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-tambah-spp" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Qty</th>
                                <th>Satuan</th>
                                <th>Harga Satuan</th>
                                <th>Discount (%)</th>
                                <th>Amount</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-borderless" width="100%" cellspacing="0">
                        <tr>
                            <td class="font-weight-bold" style="height: 40px;">Sub Total</td>
                            <td class="font-weight-bold text-right" style="height: 40px;">Rp. <span id="itemSubTotal">0</span></td>
                        </tr>
                        <tr>
                            <td style="height: 40px;">Discount</td>
                            <td class="text-right" style="height: 40px;">Rp. <span id="discTotal">0</span></td>
                        </tr>
                        <tr>
                            <td style="height: 40px;">PPn (11%)</td>
                            <td class="text-right" style="height: 40px;">Rp. <span id="taxTotal">0</span></td>
                        </tr>
                        <tr>
                            <td style="height: 40px;">Biaya Kirim</td>
                            <td class="text-right" style="height: 40px;">Rp. <span id="freightCost"><?= number_format($data->estimated_freight ?? 0); ?></span></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold" style="border-top: 1px solid #929292; height:40px;">Total Order <span id="includeTaxText">(Termasuk Pajak)</span></td>
                            <td style="border-top: 1px solid #929292; height: 40px;" class="text-right font-weight-bold">Rp. <span id="grandTotal">0</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- modal barang -->
<div class="modal detail-modal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-detail-name"></label> Barang</h5>
            </div>
            <div class="modal-body">
                <form class="detail-form" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id_detail" name="id_detail" id="id_detail" />
                    <!-- <input autocomplete="one-time-code" type="hidden" class="id_barang" name="id_barang" id="id_barang" /> -->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select id_barang" name="id_barang" id="id_barang" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" onkeyup="formatNumber(this)" type="text" class="form-control harga" name="harga" id="harga" placeholder="Harga Barang">
                                <label for="floatingInput">Harga Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                                <label for="floatingInput">Qty</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control amount" name="amount" id="amount" placeholder="Total Harga">
                                <label for="floatingInput">Total Harga</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control keterangan" name="keterangan" id="keterangan" placeholder="Keterangan">
                                <label for="floatingInput">Keterangan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control discount_percentage" name="discount_percentage" id="discount_percentage" placeholder="discount">
                                <label for="floatingInput">disc%</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select warehouse" name="warehouse" id="warehouse" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">warehouse</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-detail btn-discard mr-3">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
                <!-- <button type="button" class="btn btn-discard delete-btn delete-detail delete-form">Hapus</button> -->
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



    $(document).ready(function() {

        <?php if (!empty($data)) {
            foreach ($data->detail as $payload) {
        ?>


                list_items.push({
                    id: <?= $payload['id'] ?>,
                    id_barang: <?= $payload['id_barang'] ?>,
                    nama_barang: "<?= $payload['nama_barang'] ?>",
                    harga: "<?= $payload['harga_barang'] ?>",
                    qty: "<?= $payload['qty'] ?>",
                    amount: "<?= $payload['amount'] ?>",
                    keterangan: "<?= $payload['keterangan'] ?>",
                    tax: <?= $payload['tax'] ?>,
                    discount_percentage: <?= $payload['discount_percentage'] ?>,
                    dept: <?= $payload['dept'] ?? 0 ?>,
                    warehouse_id: <?= $payload['id_warehouse'] ?>,
                    warehouse_name: "<?= $payload['warehouse_name'] ?>"
                });
            <?php
            }
            ?>
        <?php
        } ?>

        const table = $('.dataTable').DataTable({
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
                data: "satuan",
                className: "text-center"
            },
            {
                data: "harga_barang",
                className: "text-center"
            },
            {
                data: "disc",
                className: "text-center"
            },
            {
                data: "amount",
                className: "text-center"
            },
            {
                data: "status",
                className: "text-center actions",
                render: function(data, type, row) {
                    let id = row?.id;
                    return `
                    <div class="mt-2">
                        <button>X</button>
                    </div>
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

        $('.dataTable tbody').on('click', 'button', function () { // delete datatable row
            table.row($(this).parents('tr')).remove().draw();

            reCountTotal();
        });

        $(".order_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $(".shipping_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        // Customer
        $('.id_customer').select2({
            placeholder: "",
            theme: "bootstrap-5"
        }).change(function() {
            const customerAddress = $(this).find(':selected').data('address');
            const termin = $(this).find(':selected').data('termin');
            const salesName = $(this).find(':selected').data('salesname');

            $('#tagihan_ke').val(decodeURIComponent(customerAddress));
            $('#termin').val(decodeURIComponent(termin));
            $('#sales_name').val(decodeURIComponent(salesName));
        });

        //CSS SELECT2 FLOATING LABEL
        $('.id_customer')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.id_customer')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.id_customer')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // warehouse
        $('.warehouse').select2({
            placeholder: "Pilih warehouse",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content"),
            tags: true,
            allowClear: true
        })

        //CSS SELECT2 FLOATING LABEL
        $('.warehouse')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.warehouse')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.warehouse')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // BARANG
        $('.id_barang').select2({
            placeholder: "Pilih Barang",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content"),
            tags: true,
            allowClear: true
        })

        //CSS SELECT2 FLOATING LABEL
        $('.id_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.id_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.id_barang')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        var validator = $(".create-form").validate({
            rules: {
                id_customer: {
                    required: true
                },
                order_date: {
                    required: true
                },
                shipping_date: {
                    required: true
                },
                payment_terms: {
                    // required: true
                },
            },
            messages: {
                id_customer: {
                    required: "Nama Customer wajib diisi"
                },
                order_date: {
                    required: "tanggal pemesanan wajib diisi"
                },
                shipping_date: {
                    required: "tanggal pengiriman wajib diisi"
                },
                payment_terms: {
                    required: "terms wajib diisi"
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

        // MODAL
        var validator_detail = $(".detail-form").validate({
            rules: {
                id_barang: {
                    required: true
                },
                harga: {
                    required: true
                },
                qty: {
                    required: true
                },
                // amount: {
                //     required: true
                // },
                harga: {
                    required: true
                },
                warehouse: {
                    required: true
                }
            },
            messages: {
                id_barang: {
                    required: "Nama barang wajib diisi"
                },
                harga: {
                    required: "Harga wajib diisi"
                },
                qty: {
                    required: "Qty wajib diisi"
                },
                // amount: {
                //     required: "Satuan wajib diisi"
                // },
                warehouse: {
                    required: "warehouse wajib diisi"
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

        $(".id_barang").change(function() {
            if ($(".id_barang").val()) {
                let nama = $(".id_barang option:selected").data("nama") ? $(".id_barang option:selected").data("nama") : "";
                let idBarang = $(".id_barang option:selected").data("id_item") ? $(".id_barang option:selected").data("id_item") : "";
                let satuan = $(".id_barang option:selected").data("satuan") ? $(".id_barang option:selected").data("satuan") : "";
                let warehouseId = $(".id_barang option:selected").data("warehouse_id") ? $(".id_barang option:selected").data("warehouse_id") : "";
                let warehouseName = $(".id_barang option:selected").data("warehouse_name") ? $(".id_barang option:selected").data("warehouse_name") : "";
                let harga = $(".id_barang option:selected").data("harga") ? $(".id_barang option:selected").data("harga") : "";

                $(".nama_barang").attr("readonly", nama ? true : false);
                $.ajax({
                    url: "<?= base_url('/order-form-lokal/warehouseAll'); ?>" + "/" + idBarang,
                    method: "GET",
                    dataType: "json",
                    success: function(res) {
                        $(".warehouse").empty();
                        $(".warehouse").append(`<option value=""></option>`);

                        // console.log(res.dataWarehouse)
                        res.dataWarehouse.forEach(function(item) {
                            $(".warehouse").append(`<option  value="${item.warehouse_id}" ${warehouseId==item.id?"selected":""}>${item.warehouse_name}</option>`);
                        })
                    }
                })

                $(".nama_barang").val(nama);
                $(".harga").val(harga ? +harga.toLocaleString('en-EN') : "");
                // $(".id_warehouse").val(warehouseId);
                // $(".warehouse").val(warehouseName);
            } else {
                $(".nama_barang").attr("readonly", false)
                $(".harga").val("0");
                $(".qty").val("");
                $(".amount").val("0");
                $(".keterangan").val("").change();
                $(".tax").val("");
                $(".discount_percentage").val("0");
                $(".dept").val("");
                $(".warehouse").val("");
                $(".id_warehouse").val("");
            }
        });


        $(document).on('click', '.edit-table-detail', function() {
            $(".title-detail-name").text("Update")
            $(".delete-detail").css('display', '');

            let id_barang = $(this).data('id_barang')
            let nama_barang = $(this).data('nama_barang')
            let harga = $(this).data('harga')
            let qty = $(this).data('qty')
            let amount = $(this).data('amount')
            let warehouseName = $(this).data('warehouse_name')
            let keterangan = $(this).data('keterangan')
            let dept = $(this).data('dept')
            let idWarehouse = $(this).data('id_warehouse')
            let tax = $(this).data('tax')
            let discount = $(this).data('discount_percentage')
            let row = $(this).data('row')
            let id = $(this).data('id')
            let valData = 0

            validator_detail.resetForm();
            validator_detail.reset();

            $(".id_detail").val(id)
            $(".qty").val(qty)
            // $(".harga").val(parseInt(harga))
            $(".amount").val(amount)
            $(".keterangan").val(keterangan)
            $(".tax").val(tax)
            $(".discount_percentage").val(discount)
            $(".dept").val(dept)
            $("#warehouse").val(idWarehouse)
            $("#warehouse").text(warehouseName)

            console.log(id)

            $.ajax({
                url: `<?= base_url("order-form-lokal/barangAll"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {

                    $(".id_barang").empty();

                    $(".id_barang").append(`<option data-satuan="" data-warehouse_id="" data-harga="" data-warehouse_name="" data-id_item="" value=""></option>`);

                    res.dataBarang.forEach(function(item) {
                        if (item.id == id_barang) {
                            valData = item.id
                        }
                        $(".id_barang").append(`<option data-code="${item.kode_barang}" data-satuan="${item?.nama_satuan}" data-warehouse_id="${idWarehouse}" data-harga="${item.harga_barang}" data-warehouse_name="${item.warehouse_name}" data-id_item="${item.id}" value="${item.id}" ${item.id==id_barang?'selected':''}>${item.nama_barang}</option>`);
                    })

                    $(".id_barang").val(valData).change();
                    $(".detail-modal").modal("show");



                }
            })

        });

        $(".btn-submit").click(function() {
            // console.log('test')
            $(".detail-modal").modal("hide")

            // CHECK IF NO BARANG

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
                        var total = 0;
                        let taxAmt = 0;
                        const tableData = table.rows().data().toArray();
                        console.log(tableData)

                        let update_list_items = [];

                        if (list_delete.length !== 0) {
                            list_delete.map(obj => {
                                update_list_items.push({
                                    id: obj.id ? Number(obj.id) : 0,
                                    id_barang: obj.id_barang ? Number(obj.id_barang) : 0,
                                    nama_barang: obj.nama_barang,
                                    harga: obj.harga,
                                    qty: obj.qty ? Number(obj.qty) : 0,
                                    amount: obj.amount ? Number(obj.amount.replaceAll(",", "")) : 0,
                                    keterangan: obj.keterangan,
                                    tax: obj.tax ? Number(obj.tax) : 0,
                                    discount_percentage: obj.discount_percentage ? Number(obj.discount_percentage) : 0,
                                    dept: obj.dept ? Number(obj.dept) : 0,
                                    warehouse_id: obj.warehouse_id ? Number(obj.warehouse_id) : 0,
                                    warhouse_name: obj.warhouse_name,
                                    isDeleted: true
                                })
                            })
                        }

                        const taxStatus = $('#tax_status').is(':checked');
                        const includeTaxStatus = $('#include_tax').is(':checked');
                        tableData.map(obj => {
                            console.log(obj)
                            // total = total + (obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0) * (obj.qty ? Number(obj.qty) : 0);
                            total += obj.amount || 0;
                            
                            if (taxStatus && !includeTaxStatus) {
                                taxAmt += obj.taxAmt ?? 0;
                                total += obj.taxAmt ?? 0;
                            }

                            /* if (obj.id) {
                                update_list_items.push({
                                    id: obj.id ? Number(obj.id) : 0,
                                    id_barang: obj.id_barang ? Number(obj.id_barang) : 0,
                                    nama_barang: obj.nama_barang,
                                    harga: obj.harga,
                                    qty: obj.qty ? Number(obj.qty) : 0,
                                    amount: obj.amount ? Number(obj.amount.replaceAll(",", "")) : 0,
                                    keterangan: obj.keterangan,
                                    tax: obj.tax ? Number(obj.tax) : 0,
                                    discount_percentage: obj.discount_percentage ? Number(obj.discount_percentage) : 0,
                                    dept: obj.dept ? Number(obj.dept) : 0,
                                    warehouse_id: obj.warehouse_id ? Number(obj.warehouse_id) : 0,
                                    warhouse_name: obj.warhouse_name,
                                    isDeleted: false
                                })
                            } else {
                                update_list_items.push({
                                    id: "",
                                    id_barang: obj.id_barang ? Number(obj.id_barang) : 0,
                                    nama_barang: obj.nama_barang,
                                    harga: obj.harga,
                                    qty: obj.qty ? Number(obj.qty) : 0,
                                    amount: obj.amount ? Number(obj.amount.replaceAll(",", "")) : 0,
                                    keterangan: obj.keterangan,
                                    tax: obj.tax ? Number(obj.tax) : 0,
                                    discount_percentage: obj.discount_percentage ? Number(obj.discount_percentage) : 0,
                                    dept: obj.dept ? Number(obj.dept) : 0,
                                    warehouse_id: obj.warehouse_id ? Number(obj.warehouse_id) : 0,
                                    warhouse_name: obj.warhouse_name,
                                    isDeleted: false
                                })
                            } */
                        })

                        data.append("total", total)
                        data.append('taxAmt', taxAmt)
                        data.append("tax_status", taxStatus)
                        data.append("include_tax", includeTaxStatus)
                        data.append("items", JSON.stringify(tableData))
                        // data.append("items", update_list_items)

                        // var object = {};
                        // data.forEach((value, key) => object[key] = value);
                        // var json = JSON.stringify(object);
                        // console.log(json, JSON.stringify(update_list_items))



                        let id = $(".id").val();
                        // UPDATE
                        if (id) {
                            $.ajax({
                                url: "<?= base_url("order-form-lokal/update"); ?>",
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
                                                window.location.href = "<?= base_url("order-form-lokal"); ?>" + "/id/" + id;
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
                        else {
                            if (!tableData.length) {
                                Swal.fire({
                                    icon: 'error',
                                    title: "Barang Tidak Boleh Kosong",
                                    confirmButtonColor: '#4e73df',
                                });
                                return;
                            }

                            $.ajax({
                                url: "<?= base_url("order-form-lokal/save"); ?>",
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
                                                window.location.href = "<?= base_url("order-form-lokal"); ?>" + "/id/" + +response.id;
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
        });

        $(".btn-hide-detail").click(function() {
            $(".detail-modal").modal("hide")
        });

        $(".btn-show-detail").click(function() {
            $(".title-detail-name").text("Tambah");

            $(".id_detail").val('')
            $(".id_barang").empty('')
            $(".id_barang").val('').change()
            $("#warehouse").val('').change()
            $(".harga").val('')
            $(".qty").val('')
            $(".amount").val('')
            $(".keterangan").val('')

            $(".id_barang").val('')

            $.ajax({
                url: `<?= base_url("order-form-lokal/barangAll"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".id_barang").empty();

                    $(".id_barang").append(`<option data-satuan="" data-warehouse_id="" data-harga="" data-warehouse_name="" data-id_item="" value=""></option>`);

                    res.dataBarang.forEach(function(item) {
                        $(".id_barang").append(`<option data-code="${item.kode_barang}" data-tax="${+item.tax}" data-satuan="${item?.nama_satuan}" data-warehouse_id="${item.warehouse_id}" data-harga="${item.harga_barang}" data-warehouse_name="${item.warehouse_name}" data-id_item="${item.id}" value="${item.id}">${item.nama_barang}</option>`);
                    })

                    $(".id_barang").val("").change();
                    $(".detail-modal").modal("show");
                }
            })
        });

        $(".harga, .qty").keyup(function() {
            let harga = $(".harga").val() ? $(".harga").val().replaceAll(",", "") : 0;
            let qty = $(".qty").val() ? parseInt($(".qty").val()) : 0;

            let amount = (harga * qty).toLocaleString();
            $(".amount").val(amount);
        });

        $(".btn-submit-detail").click(function() {
            let row_detail = $(".id_detail").val() ? Number($(".id_detail").val()) : 0;
            let id_barang = $(".id_barang option:selected").val()
            let nama_barang = $(".id_barang option:selected").text()
            const selectedData = $(".id_barang option:selected").data();
            let harga = $(".harga").val()
            let qty = $(".qty").val()
            let amount = $(".amount").val().replace(/\,/g, '');
            let keterangan = $(".keterangan").val()
            // const tax = $(".tax").val()
            const tax = selectedData.tax;
            let discountPercentage = $(".discount_percentage").val() || 0;
            let dept = $(".dept").val()
            let warehouseId = $(".warehouse").val()
            let warhouseName = $(".warehouse").text()

            const discAmt = amount * (discountPercentage / 100);
            const discountedAmt = amount - discAmt;

            const currentItemList = table.rows().data().toArray();console.log('hehoo')
            let validate_same = currentItemList.findIndex((obj) => obj.id_barang == id_barang && obj.warehouse_id == warehouseId);

            if (validate_same >= 0) {
                /* Swal.fire({
                    icon: 'error',
                    title: "Barang Sudah Ada",
                    confirmButtonColor: '#4e73df',
                }) */

                // table.row.add({
                //     id_barang: id_barang,
                //     kode_barang: selectedData.code,
                //     nama_barang: nama_barang,
                //     qty: qty,
                //     satuan: selectedData.satuan,
                //     harga_barang: harga,
                //     barangTotal: amount,
                //     disc: discountPercentage,
                //     tax: tax,
                //     taxAmt: amount * (tax / 100),
                //     keterangan: keterangan,
                //     discAmt: discAmt,
                //     amount: discountedAmt,
                //     dept: dept,
                //     warehouse_id: warehouseId,
                //     warehouse_name: warhouseName
                // }).draw(false);
                const currentData = table.row(validate_same).data();
                const newQty = +currentData.qty + +qty;
                const newBarangTotal = +currentData.barangTotal + +amount;
                const newTax = +currentData.tax + +tax;
                const aasd = {
                    ...currentData,
                    qty: newQty,
                    harga_barang: Math.ceil(newBarangTotal / newQty),
                    barangTotal: newBarangTotal,
                    disc: 0, // ganti nanti
                    tax: newTax,
                    taxAmt: currentData.taxAmt + (amount * (tax / 100)),
                    discAmt: currentData.discAmt + discAmt,
                    amount: currentData.amount + discountedAmt,
                };
                table.row(0).data(aasd).draw(false);

                reCountTotal();

            } else {
                // update detail
                if (row_detail) {
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
                                let new_list_items = []
                                let tag_html = "";
                                let tag_total = "";

                                row = 0;

                                $(".body-detail-table").empty()

                                total_harga_barang = 0;
                                total_qty = 0;
                                total_harga = 0;

                                /* list_items.map(item => {
                                    if (item.row == row_detail) {
                                        tag_html += `<tr>`;
                                        tag_html += `<td class="edit-table-detail" data-warehouse_name="${warhouseName}" data-dept="${dept}"  data-id_warehouse="${warehouseId}" data-tax="${tax}" data-discount_percentage="${discountPercentage}" data-id_barang="${id_barang}" data-nama_barang="${nama_barang}" data-harga="${harga}" data-qty="${qty}" data-amount="${amount}" data-keterangan="${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += row + 1;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail" data-warehouse_name="${warhouseName}" data-dept="${dept}"  data-id_warehouse="${warehouseId}" data-tax="${tax}" data-discount_percentage="${discountPercentage}" data-id_barang="${id_barang}" data-nama_barang="${nama_barang}" data-harga="${harga}" data-qty="${qty}" data-amount="${amount}" data-keterangan="${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += nama_barang;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail" data-warehouse_name="${warhouseName}" data-dept="${dept}"  data-id_warehouse="${warehouseId}" data-tax="${tax}" data-discount_percentage="${discountPercentage}" data-id_barang="${id_barang}" data-nama_barang="${nama_barang}" data-harga="${harga}" data-qty="${qty}" data-amount="${amount}" data-keterangan="${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += harga;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail" data-warehouse_name="${warhouseName}" data-dept="${dept}"  data-id_warehouse="${warehouseId}" data-tax="${tax}" data-discount_percentage="${discountPercentage}" data-id_barang="${id_barang}" data-nama_barang="${nama_barang}" data-harga="${harga}" data-qty="${qty}" data-amount="${amount}" data-keterangan="${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += qty;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail" data-warehouse_name="${warhouseName}" data-dept="${dept}"  data-id_warehouse="${warehouseId}" data-tax="${tax}" data-discount_percentage="${discountPercentage}" data-id_barang="${id_barang}" data-nama_barang="${nama_barang}" data-harga="${harga}" data-qty="${qty}" data-amount="${amount}" data-keterangan="${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += amount;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail" data-warehouse_name="${warhouseName}" data-dept="${dept}"  data-id_warehouse="${warehouseId}" data-tax="${tax}" data-discount_percentage="${discountPercentage}" data-id_barang="${id_barang}" data-nama_barang="${nama_barang}" data-harga="${harga}" data-qty="${qty}" data-amount="${amount}" data-keterangan="${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += keterangan;
                                        tag_html += "</td>";
                                        tag_html += "<td>";
                                        tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                                        tag_html += "</td>";
                                        tag_html += "</tr>";

                                        new_list_items.push({
                                            id: item.id,
                                            row: row + 1,
                                            id_barang: id_barang,
                                            nama_barang: nama_barang,
                                            harga: harga,
                                            qty: qty,
                                            amount: amount,
                                            keterangan: keterangan,
                                            tax: tax,
                                            discount_percentage: discountPercentage,
                                            dept: dept,
                                            warehouse_id: warehouseId,
                                            warhouse_name: warhouseName
                                        });

                                        row = row + 1;

                                        total_harga_barang = total_harga_barang + Number(harga.replaceAll(",", ""));
                                        total_qty = total_qty + Number(qty);
                                        total_harga = total_harga + Number(amount.replaceAll(",", ""));
                                    } else {
                                        tag_html += `<tr>`;
                                        tag_html += `<td class="edit-table-detail"data-warehouse_name="${warhouseName}" data-dept="${dept}"  data-id_warehouse="${warehouseId}" data-tax="${tax}" data-discount_percentage="${discountPercentage}" data-id_barang="${item.id_barang}" data-nama_barang="${item.nama_barang}" data-harga="${item.harga}" data-qty="${item.qty}" data-amount="${item.amount}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += row + 1;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"data-warehouse_name="${warhouseName}" data-dept="${dept}"  data-id_warehouse="${warehouseId}" data-tax="${tax}" data-discount_percentage="${discountPercentage}" data-id_barang="${item.id_barang}" data-nama_barang="${item.nama_barang}" data-harga="${item.harga}" data-qty="${item.qty}" data-amount="${item.amount}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += nama_barang;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"data-warehouse_name="${warhouseName}" data-dept="${dept}"  data-id_warehouse="${warehouseId}" data-tax="${tax}" data-discount_percentage="${discountPercentage}" data-id_barang="${item.id_barang}" data-nama_barang="${item.nama_barang}" data-harga="${item.harga}" data-qty="${item.qty}" data-amount="${item.amount}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += harga;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"data-warehouse_name="${warhouseName}" data-dept="${dept}"  data-id_warehouse="${warehouseId}" data-tax="${tax}" data-discount_percentage="${discountPercentage}" data-id_barang="${item.id_barang}" data-nama_barang="${item.nama_barang}" data-harga="${item.harga}" data-qty="${item.qty}" data-amount="${item.amount}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += qty;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"data-warehouse_name="${warhouseName}" data-dept="${dept}"  data-id_warehouse="${warehouseId}" data-tax="${tax}" data-discount_percentage="${discountPercentage}" data-id_barang="${item.id_barang}" data-nama_barang="${item.nama_barang}" data-harga="${item.harga}" data-qty="${item.qty}" data-amount="${item.amount}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += amount;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"data-warehouse_name="${warhouseName}" data-dept="${dept}"  data-id_warehouse="${warehouseId}" data-tax="${tax}" data-discount_percentage="${discountPercentage}" data-id_barang="${item.id_barang}" data-nama_barang="${item.nama_barang}" data-harga="${item.harga}" data-qty="${item.qty}" data-amount="${item.amount}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += keterangan;
                                        tag_html += "</td>";
                                        tag_html += "<td>";
                                        tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                                        tag_html += "</td>";
                                        tag_html += "</tr>";

                                        new_list_items.push(item);
                                        row = row + 1;
                                        console.log(typeof item.harga)
                                        total_harga_barang = total_harga_barang + Number(item.harga.replaceAll(",", ""));
                                        total_qty = total_qty + Number(item.qty);
                                        total_harga = total_harga + Number(item.amount.replaceAll(",", ""));
                                    }
                                }) */


                                list_items = [];
                                list_items = new_list_items;

                                // $(".body-detail-table").append(tag_html)
                                $(".foot-detail-table").empty()

                                tag_total += `<tr>`;
                                tag_total += "<td colspan='1'>";
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
                                tag_total += "<td colspan='3'>";
                                tag_total += "</td>";
                                tag_total += "</tr>";

                                $(".foot-detail-table").append(tag_total);
                                $(".detail-modal").modal("hide")
                            }
                        })
                        // create
                    }
                } else {
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
                        }).then(result => {
                            if (result.isConfirmed) {
                                list_items.push({
                                    id: "",
                                    row: row + 1,
                                    id_barang: id_barang,
                                    nama_barang: nama_barang,
                                    harga: harga,
                                    qty: qty,
                                    amount: amount,
                                    discountedAmt: discountedAmt,
                                    keterangan: keterangan,
                                    tax: tax,
                                    taxAmt: amount * (tax / 100),
                                    discount_percentage: discountPercentage,
                                    dept: dept,
                                    warehouse_id: warehouseId,
                                    warhouse_name: warhouseName
                                });

                                table.row.add({
                                    id_barang: id_barang,
                                    kode_barang: selectedData.code,
                                    nama_barang: nama_barang,
                                    qty: qty,
                                    satuan: selectedData.satuan,
                                    harga_barang: harga,
                                    barangTotal: amount,
                                    disc: discountPercentage,
                                    tax: tax,
                                    taxAmt: amount * (tax / 100),
                                    keterangan: keterangan,
                                    discAmt: discAmt,
                                    amount: discountedAmt,
                                    dept: dept,
                                    warehouse_id: warehouseId,
                                    warehouse_name: warhouseName
                                }).draw(false);

                                reCountTotal();

                                total_harga_barang = total_harga_barang + Number(harga.replaceAll(",", ""));
                                total_qty = total_qty + Number(qty);
                                total_harga = total_harga + Number(amount.replaceAll(",", ""));

                                let tag_html = "";
                                let tag_total = "";

                                /* tag_html += `<tr>`;
                                tag_html += `<td class="edit-table-detail" data-warehouse_name="${warhouseName}" data-dept="${dept}"  data-id_warehouse="${warehouseId}" data-tax="${tax}" data-discount_percentage="${discountPercentage}" data-id_barang="${id_barang}" data-nama_barang="${nama_barang}" data-harga="${harga}" data-qty="${qty}" data-amount="${amount}" data-keterangan="${keterangan}" data-id="" data-row="${row + 1}">`;
                                tag_html += row + 1;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-warehouse_name="${warhouseName}" data-dept="${dept}"  data-id_warehouse="${warehouseId}" data-tax="${tax}" data-discount_percentage="${discountPercentage}" data-id_barang="${id_barang}" data-nama_barang="${nama_barang}" data-harga="${harga}" data-qty="${qty}" data-amount="${amount}" data-keterangan="${keterangan}" data-id="" data-row="${row + 1}">`;
                                tag_html += nama_barang;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-warehouse_name="${warhouseName}" data-dept="${dept}"  data-id_warehouse="${warehouseId}" data-tax="${tax}" data-discount_percentage="${discountPercentage}" data-id_barang="${id_barang}" data-nama_barang="${nama_barang}" data-harga="${harga}" data-qty="${qty}" data-amount="${amount}" data-keterangan="${keterangan}" data-id="" data-row="${row + 1}">`;
                                tag_html += harga;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-warehouse_name="${warhouseName}" data-dept="${dept}"  data-id_warehouse="${warehouseId}" data-tax="${tax}" data-discount_percentage="${discountPercentage}" data-id_barang="${id_barang}" data-nama_barang="${nama_barang}" data-harga="${harga}" data-qty="${qty}" data-amount="${amount}" data-keterangan="${keterangan}" data-id="" data-row="${row + 1}">`;
                                tag_html += qty;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-warehouse_name="${warhouseName}" data-dept="${dept}"  data-id_warehouse="${warehouseId}" data-tax="${tax}" data-discount_percentage="${discountPercentage}" data-id_barang="${id_barang}" data-nama_barang="${nama_barang}" data-harga="${harga}" data-qty="${qty}" data-amount="${amount}" data-keterangan="${keterangan}" data-id="" data-row="${row + 1}">`;
                                tag_html += amount;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-warehouse_name="${warhouseName}" data-dept="${dept}"  data-id_warehouse="${warehouseId}" data-tax="${tax}" data-discount_percentage="${discountPercentage}" data-id_barang="${id_barang}" data-nama_barang="${nama_barang}" data-harga="${harga}" data-qty="${qty}" data-amount="${amount}" data-keterangan="${keterangan}" data-id="" data-row="${row + 1}">`;
                                tag_html += keterangan;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                                tag_html += "</td>";
                                tag_html += "</tr>";
                                $(".body-detail-table").append(tag_html) */

                                $(".foot-detail-table").empty()

                                tag_total += `<tr>`;
                                tag_total += "<td colspan='1'>";
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
                                tag_total += "<td colspan='3'>";
                                tag_total += "</td>";
                                tag_total += "</tr>";

                                $(".foot-detail-table").append(tag_total);

                                $(".detail-modal").modal("hide")
                                row = row + 1;
                            }
                        })
                    }
                }
            }
        });

        $('#estimated_freight').keyup(function () {
            const estimatedFreight = $(this).val();
            $('#freightCost').html(estimatedFreight);
            reCountTotal();
        });

        const reCountTotal = () => {
            const taxStatus = $('#tax_status').is(':checked');
            const includeTax = $('#include_tax').is(':checked');
            const itemList = table.rows().data();

            let itemSubTotal = 0;
            let discTotal = 0;
            let taxTotal = 0;
            let taxTotalHtml = 0;
            const estimatedFreightVal = $('#estimated_freight').val() || '0';
            const estimatedFreight = +estimatedFreightVal.replace(/\,/g, '');

            itemList.map((obj) => {
                itemSubTotal += +obj.barangTotal;
                discTotal += +obj.discAmt;

                if (taxStatus && !includeTax) {
                    taxTotal += +obj.taxAmt;
                    taxTotalHtml += +obj.taxAmt;
                } else if (taxStatus && includeTax) {
                    taxTotalHtml += +obj.taxAmt;
                }
            });

            if (taxStatus || includeTax) {
                $('#includeTaxText').html('(Termasuk Pajak)');
            } else {
                $('#includeTaxText').html('');
            }

            $('#itemSubTotal').html(itemSubTotal.toLocaleString());
            $('#discTotal').html(discTotal.toLocaleString());
            $('#taxTotal').html(taxTotalHtml.toLocaleString());

            // if (includeTax) taxTotal = 0;

            const grandTotal = itemSubTotal + estimatedFreight + taxTotal - discTotal; 
            $('#grandTotal').html(grandTotal.toLocaleString());
        };

        $('#tax_status').change(function() {

            if (!this.checked) {
                $('#include_tax').prop('checked', false);
            }

            reCountTotal();
        });
        $('#include_tax').change(function() {

            const taxStatus = $('#tax_status').is(':checked');

            if (this.checked && !taxStatus) {
                $(this).prop('checked', false);
            }

            reCountTotal()
        });

        <?php if (!empty($data)): ?>
        const dataHaciu = <?= json_encode($data->detail) ?>;
        table.rows.add(dataHaciu).draw(false);
        reCountTotal();
        <?php endif; ?>

    })

    const changeStatus = function() {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if (value) {
            $(".no_order").attr("readonly", true);
            $(".no_order").val("AUTO GENERATE");
        } else {
            $(".no_order").attr("readonly", false);
            $(".no_order").val("");
        }
    }

    // change data model jika sudah ada datanya di pilih
</script>

<?= $this->endSection(); ?>