<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($data) ? "Ubah" : "Tambah"; ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("invoice-penjualan-lokal"); ?>">
                Batal
            </a>

            <button class="btn btn-show-form btn-save float-right btn-submit">
                Simpan
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-pinjaman-karyawan" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" class="id" name="id" id="id" value="<?= !empty($data) ? $data->id : ""; ?>" />
                <input type="hidden" class="tipe_invoice" name="tipe_invoice" id="tipe_invoice" value="LOKAL" />
                <?= csrf_field() ?>
                <div class="row">

                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control no_faktur" id="no_faktur" name="no_faktur" disabled="true" value="<?= !empty($data) ? $data->no_faktur : ""; ?>" placeholder="Auto Generate">
                            <label for="floatingInput">No Faktur</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control tanggal_faktur" id="tanggal_faktur" name="tanggal_faktur" disabled="true" value="<?= !empty($data) ? $data->tanggal_faktur : ""; ?>" placeholder="Tanggal Faktur"></input>
                            <label for="floatingInput">Tanggal Faktur</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
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
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input type="hidden" class="form-control id_user" id="id_user" name="id_user" value="<?= $id_user ?>">
                                    <input type="text" class="form-control user" id="user" name="user" disabled="true" placeholder="penjual" value="<?= $seller_name  ?>">
                                    <label for="floatingInput">Penjual</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3" style="height: 50px;">
                                            <label for="floatingInput">Pajak</label>
                                            <div class="switch-form-pinjaman-karyawan">
                                                <label class="switch">
                                                    <input class="tax_status" <?= !empty($data) ? ($data->status_tax === true ? 'disabled=true' : '') : ''; ?> name="tax_status" id="tax_status" type="checkbox" <?= !empty($data) ? ($data->status_tax == 'true' ? 'checked' : '') : ''; ?> value="<?= !empty($data) ? ($data->status_tax == 'true' ? 'true' : 'false') : 'false'; ?>">
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
                                                    <input class="include_pa" <?= !empty($data) ? ($data->termasuk_pa === true ? 'disabled=true' : '') : 'disabled=true'; ?> name="include_pa" id="include_pa" type="checkbox" <?= !empty($data) ? ($data->termasuk_pa == 'true' ? 'checked' : '') : ''; ?> value="<?= !empty($data) ? ($data->termasuk_pa == 'true' ? 'true' : 'false') : 'false'; ?>">
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select id_surat_jalan" name="id_surat_jalan" id="id_surat_jalan" <?= !empty($data) ? ($data->id_surat_jalan === true ? 'disabled=true' : '') : ''; ?>>
                                <option value=""></option>
                                <?php
                                if (!empty($dataSuratJalan)) {
                                    foreach ($dataSuratJalan as $surat) {
                                ?>
                                        <option value="<?= $surat->id; ?>" <?= !empty($data) ? ($data->id_surat_jalan === $surat->id ? "selected" : "") : ""; ?>><?= $surat->no_surat_jalan; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Surat Jalan</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select id_so" name="id_so[]" id="id_so[]" disabled="true" multiple>
                                <option value=""></option>
                                <?php
                                if (!empty($dataSo)) {
                                    foreach ($dataSo as $so) {
                                ?>
                                        <option value="<?= $so['id']; ?>" selected><?= $so['no_so']; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">SO</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select ship_via" name="ship_via" id="ship_via" <?= !empty($data) ? ($data->ship_via_id === true ? 'disabled=true' : '') : ''; ?>>
                                <option value=""></option>
                                <?php
                                if (!empty($via)) {
                                    foreach ($via as $payload) {
                                ?>
                                        <option value="<?= $payload->id; ?>" <?= !empty($data->ship_via_id) ? ($payload->id === $data->ship_via_id ? "selected" : "") : ""; ?>><?= $payload->value; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Pengiriman via</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <textarea <?= !empty($data) ? ($data->keterangan === true ? 'disabled=true' : '') : ''; ?> class="form-control Keterangan text-area-all" id="Keterangan" name="Keterangan" placeholder="Keterangan"><?= !empty($data) ? $data->keterangan : ""; ?></textarea>
                            <label for="floatingInput">Keterangan</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="hidden" class="form-control dpp" id="dpp" name="dpp" disabled="true" placeholder="Total Invoice" value="<?= !empty($data) ? floatval($data->dpp)  : ""; ?>">
                            <input type="hidden" class="form-control ppn" id="ppn" name="ppn" disabled="true" placeholder="Total Invoice" value="<?= !empty($data) ? floatval($data->ppn) : ""; ?>">
                            <input type="number" class="form-control total_invoice" id="total_invoice" name="total_invoice" disabled="true" placeholder="Total Invoice" value="<?= !empty($data) ? floatval($data->total_invoice) : ""; ?>">
                            <label for="floatingInput">Total Invoice</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control terms" id="terms" name="terms" <?= !empty($data) ? ($data->terms === true ? 'disabled=true' : '') : ''; ?> value="<?= !empty($data) ? $data->terms : ""; ?>" placeholder="terms">
                            <label for="floatingInput">Terms</label>
                        </div>
                    </div>

                </div>
        </div>
        </form>

        <div class="row">
            <div class="table-responsive">
                <table class="table nowrap table-hover-tobasurimi dataTable table-detail-barang" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No.</th>
                            <th>Nama Barang</th>
                            <th>Qty</th>
                            <th>satuan</th>
                            <th>Harga Barang</th>
                            <th>Disc %</th>
                            <th>Total Harga</th>
                            <th>dept</th>
                            <th>Gudang</th>
                            <th>Keterangan</th>
                            <th>No So</th>
                            <th>No Surat Jalan</th>
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
                                $total_harga_barang = $total_harga_barang + formatter(str_replace(",", "", $d['harga_barang']), "STR_TO_INT");
                                $total_qty = $total_qty + $d['qty'];
                                $total_harga = $total_harga + formatter(str_replace(",", "", $d['amount']), "STR_TO_INT");
                        ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td><?= $d['nama_barang'] ?></td>
                                    <td><?= $d['qty'] ?></td>
                                    <td><?= $d['kode_satuan'] ?></td>
                                    <td><?= number_format($d['harga_barang']) ?></td>
                                    <td><?= $d['discount_percentage'] ?></td>
                                    <td><?= number_format($d['amount']) ?></td>
                                    <td><?= $d['dept'] ?></td>
                                    <td><?= $d['warehouse_name'] ?></td>
                                    <td><?= $d['keterangan'] ?></td>
                                    <td><?= $d['no_so'] ?></td>
                                    <td><?= $d['no_surat_jalan'] ?></td>
                                </tr>

                        <?php
                                $no++;
                            }
                        } ?>
                    </tbody>
                    <tfoot class="foot-detail-table" id="foot-detail-table">
                        <tr>
                            <td colspan="5"></td>
                            <td><b>DPP</b></td>
                            <td><b><?= number_format($data->dpp)  ?></b></td>
                            <td colspan="5"></td>
                        </tr>
                        <tr>
                            <td colspan="5"></td>
                            <td><b>PPN</b></td>
                            <td><b><?= number_format($data->ppn) ?></b></td>
                            <td colspan="5"></td>
                        </tr>
                        <tr>
                            <td colspan="5"></td>
                            <td><b>Total Invoice</b></td>
                            <td><b><?= number_format($data->total_invoice) ?></b></td>
                            <td colspan="5"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    </div>
</section>


<script>
    const csrfToken = '<?= csrf_token() ?>';
    // Get the current date
    var currentDate = new Date();

    // Format the date to your desired representation
    // var formattedDate = currentDate.toLocaleString().slice(0, 9); // You can use other formatting methods if needed
    var formattedDateFront = moment(currentDate).format("DD/MM/YYYY")
    var tanggalFaktur = moment(currentDate).format("YYYY-MM-DD")
    // Display the date on the webpage
    $(document).ready(function() {

        $(".tanggal_faktur").val(formattedDateFront);

        // via
        $('.ship_via').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.ship_via')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.ship_via')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.ship_via')
            .parent('div')
            .find('label')
            .css('z-index', '1');
        // Customer
        $('.ship_via').select2({
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

        // SO
        $('.id_so').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.id_so')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.id_so')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.id_so')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // Surat jalan
        $('.id_surat_jalan').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.id_surat_jalan')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.id_surat_jalan')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.id_surat_jalan')
            .parent('div')
            .find('label')
            .css('z-index', '1');


    })

    var validator = $(".create-form").validate({
        rules: {
            id_customer: {
                required: true
            },
            id_po: {
                required: true
            },
            "id_so[]": {
                required: true
            },
            no_po: {
                required: true
            },
            no_surat_jalan: {
                required: true
            },
            shipping_date: {
                required: true
            },
        },
        messages: {
            id_customer: {
                required: "Customer wajib diisi"
            },
            id_po: {
                required: "PO wajib diisi"
            },
            "id_so[]": {
                required: "SO wajib diisi"
            },
            no_po: {
                required: "No PO wajib diisi"
            },
            no_surat_jalan: {
                required: "No Surat jalan wajib diisi"
            },
            shipping_date: {
                required: "Tanggal pengiriman wajib diisi"
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

    $(".id_customer").change(function() {
        const id = $(".id_customer").val()
        if (id) {
            $.ajax({
                url: "<?= base_url('/surat-jalan/sales-invoice/'); ?>" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".id_surat_jalan").empty();
                    $(".id_so").empty();
                    $('.body-detail-table').empty()
                    $(".foot-detail-table").empty()
                    $("#dpp").val('')
                    $("#ppn").val('')
                    $("#total_invoice").val('')

                    $(".id_surat_jalan").append(`<option value=""></option>`);

                    // console.log(res.dataWarehouse)
                    res.forEach(function(item) {
                        $(".id_surat_jalan").append(`<option  value="${item.id}" >${item.no_surat_jalan}</option>`);
                    })
                }
            })
        }
    })
    $(".id_surat_jalan").change(function() {
        const id = $(".id_surat_jalan").val()
        if (id) {
            $.ajax({
                url: "<?= base_url('/surat-jalan/detail/'); ?>" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".id_so").empty();
                    $(".id_so").append(`<option value=""></option>`);

                    // console.log(res.dataWarehouse)
                    res.detail_so.forEach(function(item) {
                        $(".id_so").append(`<option  value="${item.id}" selected>${item.no_so}</option>`);
                    })

                    let no = 1
                    let html = ''
                    let totalHarga = 0
                    res.detail_barang.forEach(function(item) {
                        totalHarga += parseFloat(item.amount)
                        html += `
                                <tr>
                                    <td >${no}</td>
                                    <td >${item.nama_barang}</td>
                                    <td >${item.qty}</td>
                                    <td >${item.kode_satuan}</td>
                                    <td >${item.harga_barang}</td>
                                    <td >${item.discount_percentage}</td>
                                    <td >${item.amount}</td>
                                    <td >${item.dept}</td>
                                    <td >${item.warehouse_name}</td>
                                    <td >${item.keterangan}</td>
                                    <td >${item.no_so}</td>
                                    <td >${item.no_surat_jalan}</td>
                                </tr>
                                 `
                        no++
                    })
                    $('.body-detail-table').empty()
                    $('.body-detail-table').append(html)
                    $(".foot-detail-table").empty()
                    const statusTax = $('.tax_status').val() === 'true'
                    const includePa = $('.include_pa').val() === 'true'

                    let hargaDpp = totalHarga
                    let ppn = 0
                    if (statusTax && includePa) {
                        ppn = totalHarga * 11 / 100
                        hargaDpp = totalHarga - ppn
                    } else if (statusTax && !includePa) {
                        ppn = totalHarga * 11 / 100
                        totalHarga = totalHarga + ppn
                    }

                    $("#dpp").val(hargaDpp)
                    $("#ppn").val(ppn)
                    $("#total_invoice").val(totalHarga)

                    let tag_total =
                        `
                        <tr>
                            <td colspan="5"></td>
                            <td><b>DPP</b></td>
                            <td><b>${hargaDpp.toLocaleString()}</b></td>
                            <td colspan="5"></td>
                        </tr>
                        <tr>
                            <td colspan="5"></td>
                            <td><b>PPN</b></td>
                            <td><b>${ppn.toLocaleString()}</b></td>
                            <td colspan="5"></td>
                        </tr>
                        <tr>
                            <td colspan="5"></td>
                            <td><b>Total Invoice</b></td>
                            <td><b>${totalHarga.toLocaleString()}</b></td>
                            <td colspan="5"></td>
                        </tr>
                        `
                    $(".foot-detail-table").append(tag_total);
                }
            })
        }
    })

    $('.tax_status').change(function() {
        let dataDpp = parseFloat($("#dpp").val())
        let dataPPn = parseFloat($("#ppn").val())
        let dataHargaInvoice = parseFloat($("#total_invoice").val())
        let includePa = $('.include_pa').val() === 'true'

        if ($('.tax_status').val() === 'false') {
            $('.tax_status').val('true')
            $('.include_pa').prop("disabled", false)

            if (dataHargaInvoice && includePa) {
                dataPPn = dataHargaInvoice * 11 / 100
                dataDpp = dataHargaInvoice - dataPPn
                dataHargaInvoice = dataHargaInvoice
                $(".foot-detail-table").empty()
                let tag_total =
                    `
                            <tr>
                                <td colspan="5"></td>
                                <td><b>DPP</b></td>
                                <td><b>${dataDpp.toLocaleString()}</b></td>
                                <td colspan="5"></td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td><b>PPN</b></td>
                                <td><b>${dataPPn.toLocaleString()}</b></td>
                                <td colspan="5"></td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td><b>Total Invoice</b></td>
                                <td><b>${dataHargaInvoice.toLocaleString()}</b></td>
                                <td colspan="5"></td>
                            </tr>
                            `
                $(".foot-detail-table").append(tag_total);
            } else if (dataHargaInvoice && !includePa) {
                dataPPn = dataHargaInvoice * 11 / 100
                dataDpp = dataHargaInvoice
                dataHargaInvoice = dataHargaInvoice + dataPPn
                $(".foot-detail-table").empty()
                let tag_total =
                    `
                            <tr>
                                <td colspan="5"></td>
                                <td><b>DPP</b></td>
                                <td><b>${dataDpp.toLocaleString()}</b></td>
                                <td colspan="5"></td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td><b>PPN</b></td>
                                <td><b>${dataPPn.toLocaleString()}</b></td>
                                <td colspan="5"></td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td><b>Total Invoice</b></td>
                                <td><b>${dataHargaInvoice.toLocaleString()}</b></td>
                                <td colspan="5"></td>
                            </tr>
                            `
                $(".foot-detail-table").append(tag_total);
            }


        } else {
            $('.tax_status').val('false')
            $(".foot-detail-table").empty()
            $('.include_pa').prop("disabled", true)


            if (dataHargaInvoice && includePa) {
                dataPPn = 0
                dataDpp = dataHargaInvoice
                $(".foot-detail-table").empty()
                let tag_total =
                    `
                            <tr>
                                <td colspan="5"></td>
                                <td><b>DPP</b></td>
                                <td><b>${dataDpp.toLocaleString()}</b></td>
                                <td colspan="5"></td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td><b>PPN</b></td>
                                <td><b>${dataPPn.toLocaleString()}</b></td>
                                <td colspan="5"></td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td><b>Total Invoice</b></td>
                                <td><b>${dataHargaInvoice.toLocaleString()}</b></td>
                                <td colspan="5"></td>
                            </tr>
                            `
                $(".foot-detail-table").append(tag_total);
            } else if (dataHargaInvoice && !includePa) {
                dataPPn = 0
                dataHargaInvoice = dataDpp
                $(".foot-detail-table").empty()
                let tag_total =
                    `
                            <tr>
                                <td colspan="5"></td>
                                <td><b>DPP</b></td>
                                <td><b>${dataDpp.toLocaleString()}</b></td>
                                <td colspan="5"></td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td><b>PPN</b></td>
                                <td><b>${dataPPn.toLocaleString()}</b></td>
                                <td colspan="5"></td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td><b>Total Invoice</b></td>
                                <td><b>${dataHargaInvoice.toLocaleString()}</b></td>
                                <td colspan="5"></td>
                            </tr>
                            `
                $(".foot-detail-table").append(tag_total);
            }
        }

        $("#dpp").val(dataDpp)
        $("#ppn").val(dataPPn)
        $("#total_invoice").val(dataHargaInvoice)
    })

    $('.include_pa').change(function() {
        let dataDpp = parseFloat($("#dpp").val())
        let dataPPn = parseFloat($("#ppn").val())
        let dataHargaInvoice = parseFloat($("#total_invoice").val())
        let statusTax = $('.tax_status').val() === 'true'


        if ($('.include_pa').val() === 'false') {
            $('.include_pa').val('true')
            if (statusTax && dataHargaInvoice) {
                dataHargaInvoice = dataDpp
                dataDpp = dataHargaInvoice - dataPPn
                $(".foot-detail-table").empty()
                let tag_total =
                    `
                            <tr>
                                <td colspan="5"></td>
                                <td><b>DPP</b></td>
                                <td><b>${dataDpp.toLocaleString()}</b></td>
                                <td colspan="5"></td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td><b>PPN</b></td>
                                <td><b>${dataPPn.toLocaleString()}</b></td>
                                <td colspan="5"></td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td><b>Total Invoice</b></td>
                                <td><b>${dataHargaInvoice.toLocaleString()}</b></td>
                                <td colspan="5"></td>
                            </tr>
                            `
                $(".foot-detail-table").append(tag_total);
            }
        } else {
            $('.include_pa').val('false')
            if (statusTax && dataHargaInvoice) {
                dataDpp = dataHargaInvoice
                dataHargaInvoice = dataHargaInvoice + dataPPn
                $(".foot-detail-table").empty()
                let tag_total =
                    `
                            <tr>
                                <td colspan="5"></td>
                                <td><b>DPP</b></td>
                                <td><b>${dataDpp.toLocaleString()}</b></td>
                                <td colspan="5"></td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td><b>PPN</b></td>
                                <td><b>${dataPPn.toLocaleString()}</b></td>
                                <td colspan="5"></td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td><b>Total Invoice</b></td>
                                <td><b>${dataHargaInvoice.toLocaleString()}</b></td>
                                <td colspan="5"></td>
                            </tr>
                            `
                $(".foot-detail-table").append(tag_total);
            }
        }
        $("#dpp").val(dataDpp)
        $("#ppn").val(dataPPn)
        $("#total_invoice").val(dataHargaInvoice)
    })

    $(".btn-submit").click(function() {
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

                    const ppn = $('#ppn').val()
                    const dpp = $('#dpp').val()
                    const totalInvoice = $('#total_invoice').val()
                    const noSuratJalan = $('.id_surat_jalan').find(":selected").text()
                    let id = $(".id").val();

                    data.append("total_invoice", totalInvoice)
                    data.append("ppn", ppn)
                    data.append("dpp", dpp)
                    data.append("no_surat_jalan", noSuratJalan)
                    if (!id) {
                        data.append("tanggal_faktur", tanggalFaktur)
                    }

                    // UPDATE
                    if (id) {
                        $.ajax({
                            url: "<?= base_url("invoice-penjualan-lokal/update"); ?>",
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
                                            window.location.href = "<?= base_url("invoice-penjualan-lokal/id/"); ?>" + res.id;
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
                        $.ajax({
                            url: "<?= base_url("invoice-penjualan-lokal/save"); ?>",
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
                                            window.location.href = "<?= base_url("invoice-penjualan-lokal/id/"); ?>" + res.id;
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
    })
</script>

<?= $this->endSection(); ?>