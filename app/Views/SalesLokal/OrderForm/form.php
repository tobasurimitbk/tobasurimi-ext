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

            <button class="btn btn-show-form btn-save float-right btn-submit">
                Simpan
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-order-form-lokal" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" class="id" name="id" id="id" value="<?= !empty($data) ? $data->id : ""; ?>" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select id_customer" name="id_customer" id="id_customer" <?= !empty($data) ? ($data->id_customer === true ? 'disabled=true' : '') : ''; ?>>
                                <option value=""></option>
                                <?php
                                if (!empty($dataCustomers)) {
                                    foreach ($dataCustomers as $customer) {
                                ?>
                                        <option value="<?= $customer->id; ?>" <?= !empty($data) ? ($data->id_customer === $customer->id ? "selected" : "") : ""; ?>><?= $customer->name; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Nama Customer</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control sales_name" id="sales_name" name="sales_name" disabled=true>
                            <label for="floatingInput">Nama Sales</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($data) ? ($data->no_po === true ? 'readonly=true' : '') : ''; ?> type="text" class="form-control no_order" id="no_order" name="no_order" placeholder="No. Order" value="<?= !empty($data) ? $data->no_po : ""; ?>">
                                    <label for="floatingInput">No. SPP</label>
                                </div>
                                <div style="<?= !empty($data) ? ($data->no_po === true ? "display: none" : "") : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input type="time" class="form-control order_date" id="order_date" name="order_date" <?= !empty($data) ? ($data->order_date === true ? 'disabled=true' : '') : ''; ?> value="<?= !empty($data) ? $data->order_date : ""; ?>" placeholder="Tanggal Pemesanan">
                                    <label for="floatingInput">Tanggal Pemesanan</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input type="time" class="form-control shipping_date" id="shipping_date" name="shipping_date" <?= !empty($data) ? ($data->shipping_date === true ? 'disabled=true' : '') : ''; ?> value="<?= !empty($data) ? $data->shipping_date : ""; ?>" placeholder="End of time">
                                    <label for="floatingInput">Tanggal Pengiriman</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control estimated_freight" id="estimated_freight" name="estimated_freight" <?= !empty($data) ? ($data->estimated_freight === true ? 'disabled=true' : '') : ''; ?> placeholder="estimated_freight" value="<?= !empty($data) ? $data->estimated_freight : ""; ?>">
                            <label for="floatingInput">Estimated Freight</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control payment_terms" id="payment_terms" name="payment_terms" <?= !empty($data) ? ($data->payment_terms === true ? 'disabled=true' : '') : ''; ?> placeholder="Terms" value="<?= !empty($data) ? $data->payment_terms : ""; ?>">
                            <label for="floatingInput">Terms</label>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="number" class="form-control ppn" id="ppn" name="ppn" <?= !empty($data) ? ($data->ppn === true ? 'disabled=true' : '') : ''; ?> placeholder="ppn" value="<?= !empty($data) ? $data->ppn : ""; ?>">
                            <label for="floatingInput">PPN</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <textarea <?= !empty($data) ? ($data->Keterangan === true ? 'disabled=true' : '') : ''; ?> class="form-control Keterangan text-area-address-company" id="Keterangan" name="Keterangan" placeholder="Keterangan"><?= !empty($data) ? $data->Keterangan : ""; ?></textarea>
                            <label for="floatingInput">Deskripsi</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3" style="height: 50px;">
                                    <label for="floatingInput">Pajak</label>
                                    <div class="switch-form-pinjaman-karyawan">
                                        <label class="switch">
                                            <input class="tax_status" <?= !empty($data) ? ($data->tax_status === true ? 'disabled=true' : '') : ''; ?> name="tax_status" id="tax_status" type="checkbox" <?= !empty($data) ? ($data->tax_status === true ? 'checked' : '') : ''; ?>>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3" style="height: 50px;">
                                    <label for="floatingInput">Include pa</label>
                                    <div class="switch-form-pinjaman-karyawan">
                                        <label class="switch">
                                            <input class="include_pa" <?= !empty($data) ? ($data->include_pa === true ? 'disabled=true' : '') : ''; ?> name="include_pa" id="include_pa" type="checkbox" <?= !empty($data) ? ($data->include_pa === true ? 'checked' : '') : ''; ?>>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
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
                    <table class="table nowrap table-hover-tobasurimi dataTable table-tambah-spp" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Qty</th>
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
                            if (!empty($data)) {
                                foreach ($data->detail as $d) {
                                    $total_harga_barang = $total_harga_barang + formatter(str_replace(",", "", $d->harga_barang), "STR_TO_INT");
                                    $total_qty = $total_qty + $d->qty;
                                    $total_harga = $total_harga + formatter(str_replace(",", "", $d->amount), "STR_TO_INT");
                            ?>


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

<!-- modal barang -->
<div class="modal detail-modal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-detail-name"></label> Barang</h5>
            </div>
            <div class="modal-body">
                <form class="detail-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id_detail" name="id_detail" id="id_detail" />
                    <input type="hidden" class="id_barang" name="id_barang" id="id_barang" />

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
                                <input type="text" readonly="true" class="form-control amount" name="amount" id="amount" placeholder="Total Harga" disabled>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-detail btn-discard mr-3">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
                <button type="button" class="btn btn-discard delete-btn delete-detail delete-form">Hapus</button>
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
        // Customer
        $('.id_customer').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })
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
    })

    var validator = $(".create-form").validate({
        rules: {
            id_customer: {
                required: true
            },
            total_pinjaman: {
                required: true
            },
            termin_pembayaran: {
                required: true
            },
        },
        messages: {
            id_customer: {
                required: "Nama Customer wajib diisi"
            },
            total_pinjaman: {
                required: "Total Pinjaman wajib diisi"
            },
            termin_pembayaran: {
                required: "Termin Pembayaran wajib diisi"
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

    $(".btn-hide-detail").click(function() {
        $(".detail-modal").modal("hide")
    })

    $(".btn-show-detail").click(function() {
        $(".id_detail").val('')
        $(".id_barang").empty('')
        $(".id_barang").val('').change()
        $(".harga").val('')
        $(".qty").val('')
        $(".amount").val('')
        $(".Keterangan").val('')

        $(".id_barang").val('')

        $.ajax({
            url: `<?= base_url("order-form-lokal/barangAll"); ?>`,
            method: "GET",
            dataType: "json",
            success: function(res) {
                $(".id_barang").empty();

                $(".id_barang").append(`<option data-satuan="" data-warehouse="" value=""></option>`);

                res.dataBarang.forEach(function(item) {
                    $(".id_barang").append(`<option data-satuan="${item?.nama_satuan}" data-warehouse_id="${item.warehouse_id}" value="${item.id}">${item.nama_barang}</option>`);
                })

                $(".id_barang").val("").change();
                $(".detail-modal").modal("show");
            }
        })
    })

    $(".harga, .qty").keyup(function() {
        let harga = $(".harga").val() ? $(".harga").val().replaceAll(",", "") : 0;
        let qty = $(".qty").val() ? parseInt($(".qty").val()) : 0;

        let amount = (harga * qty).toLocaleString();
        $(".amount").val(amount);
    })

    $(".btn-submit-detail").click(function() {
        let row_detail = $(".id_detail").val() ? Number($(".id_detail").val()) : 0;
        let id_barang = $(".id_barang option:selected").val()
        let harga = $(".harga").val()
        let qty = $(".qty").val()
        let amount = $(".amount").val()
        let Keterangan = $(".keterangan").val()

        let validate_same = false;

        if (validate_same) {
            Swal.fire({
                icon: 'error',
                title: "Barang Sudah Ada",
                confirmButtonColor: '#4e73df',
            })
        } else {
            // update detail
            if (row_detail) {

                // create
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
                                harga: harga,
                                qty: qty,
                                amount: amount,
                                keterangan: keterangan
                            });

                            total_harga_barang = total_harga_barang + Number(harga.replaceAll(",", ""));
                            total_qty = total_qty + Number(qty);
                            total_harga = total_harga + Number(amount.replaceAll(",", ""));

                            let tag_html = "";
                            let tag_total = "";

                            tag_html += `<tr>`;
                            tag_html += `<td class="edit-table-detail">`;
                            tag_html += row + 1;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail">`;
                            tag_html += id_barang;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail">`;
                            tag_html += ""
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail">`;
                            tag_html += ""
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail">`;
                            tag_html += ""
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail">`;
                            tag_html += harga;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail">`;
                            tag_html += qty;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail">`;
                            tag_html += amount;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail">`;
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
        }
    })

    $(".btn-submit").click(function() {
        if ($(".create-form").valid()) {
            console.log("Submit")
        }
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
</script>

<?= $this->endSection(); ?>