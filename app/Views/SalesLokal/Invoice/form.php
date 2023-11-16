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

            <?php if (!empty($data)) : ?>
                <a class="btn btn-warning btn-print float-right" href="<?= base_url("invoice-penjualan-lokal/print/{$data->id}"); ?>" target="_blank">
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
            <form class="create-form form-add-pinjaman-karyawan" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($data) ? $data->id : ""; ?>" />
                <input autocomplete="one-time-code" type="hidden" class="tipe_invoice" name="tipe_invoice" id="tipe_invoice" value="LOKAL" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control no_faktur" id="no_faktur" name="no_faktur" value="<?= !empty($data) ? $data->no_faktur : $noFaktur; ?>" placeholder="Auto Generate">
                            <label for="floatingInput">No Faktur</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control tanggal_faktur" id="tanggal_faktur" name="tanggal_faktur" value="<?= $data->tanggal_faktur ?? ""; ?>" placeholder="Tanggal Faktur"></input>
                            <label for="floatingInput">Tanggal Faktur</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select" name="doc_type" id="doc_type">
                                <option value=""></option>
                                <option value="pesanan" <?= ($data->document_type ?? '') == 'pesanan' ? 'selected' : '' ?>>Pesanan</option>
                                <option value="pengiriman" <?= ($data->document_type ?? '') == 'pengiriman' ? 'selected' : '' ?>>Pengiriman</option>
                            </select>
                            <label for="floatingInput">Jenis Dokumen</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select" name="doc_id" id="doc_id">
                                <option value=""></option>
                                <?php foreach ($documentList ?? [] as $document) : ?>
                                    <option value="<?= $document->id ?>" <?= $data->document_id == $document->id ? 'selected' : '' ?>><?= $document->doc_no ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Nomor Dokumen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control" id="customerName" value="<?= $documentData->customerName ?? '' ?>" disabled>
                            <label for="floatingInput">Nama Konsumen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control" id="customerAddress" value="<?= $documentData->customerAddress ?? '' ?>" disabled>
                            <label for="floatingInput">Alamat Konsumen</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control" id="salesName" value="<?= $documentData->salesName ?? '' ?>" disabled>
                            <label for="floatingInput">Nama Sales</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control" id="termin" name="termin" value="<?= $documentData->termin ?? '' ?>" disabled>
                            <label for="floatingInput">Termin</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select ship_via" name="ship_via" id="ship_via" <?= !empty($data) ? ($data->ship_via_id === true ? 'disabled=true' : '') : ''; ?>>
                                <option value=""></option>
                                <?php foreach ($via as $payload) : ?>
                                    <option value="<?= $payload->id; ?>" <?= !empty($data->ship_via_id) ? ($payload->id === $data->ship_via_id ? "selected" : "") : ""; ?>><?= $payload->value; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Ship Via (Opsional)</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating ff-ket mb-3" style="height: 70px;">
                            <textarea autocomplete="one-time-code" style="height: 100%;" <?= !empty($data->keterangan) ? ($data->keterangan === true ? 'disabled=true' : '') : ''; ?> class="form-control Keterangan text-area-all" id="keterangan" name="keterangan" placeholder="Keterangan"><?= $data->keterangan ?? ""; ?></textarea>
                            <label for="floatingInput">Keterangan</label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="mb-3" style="height: 50px;">
                                    <label for="floatingInput">Pajak</label>
                                    <div class="switch-form-pinjaman-karyawan">
                                        <label class="switch">
                                            <input autocomplete="one-time-code" class="tax_status" name="tax_status" id="tax_status" type="checkbox" <?= !empty($data->status_tax) ? ($data->status_tax == 'true') ? 'checked' : '' : ''; ?>>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="mb-3" style="height: 50px;">
                                    <label for="floatingInput">Include Pajak</label>
                                    <div class="switch-form-pinjaman-karyawan">
                                        <label class="switch">
                                            <input autocomplete="one-time-code" class="include_tax" name="include_tax" id="include_tax" type="checkbox" <?= !empty($data->termasuk_pa) ? ($data->termasuk_pa == 'true') ? 'checked' : '' : ''; ?>>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- list barang -->
                <div class="col-subtitle-modal">
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label font-weight-bold modal-sub-title">List Barang</label>
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
                                <td class="font-weight-bold" style="height: 40px;">DPP</td>
                                <td class="font-weight-bold text-right" style="height: 40px;">Rp. <span id="itemSubTotal">0</span></td>
                            </tr>
                            <tr>
                                <td style="height: 40px;">PPn (11%)</td>
                                <td class="text-right" style="height: 40px;">Rp. <span id="taxTotal">0</span></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold" style="border-top: 1px solid #929292; height: 40px;">Total Invoice <span id="includeTaxText"></span></td>
                                <td style="border-top: 1px solid #929292; height: 40px;" class="text-right font-weight-bold">Rp. <span id="grandTotal">0</span></td>
                            </tr>
                        </table>
                    </div>
                </div>

        </div>
        </form>

    </div>
    </div>
</section>


<script>
    const csrfToken = '<?= csrf_token() ?>';
    // Get the current date
    var currentDate = new Date();

    // Format the date to your desired representation
    // var formattedDate = currentDate.toLocaleString().slice(0, 9); // You can use other formatting methods if needed
    // var formattedDateFront = moment(currentDate).format("DD/MM/YYYY")
    var tanggalFaktur = moment(currentDate).format("YYYY-MM-DD")
    // Display the date on the webpage
    $(document).ready(function() {

        <?php if (!empty($data->dpp) || !empty($data->ppn) || !empty($data->total_invoice)) : ?>

            $('#taxTotal').html(<?= $data->ppn; ?>.toLocaleString());
            $('#itemSubTotal').html(<?= $data->dpp; ?>.toLocaleString());
            $('#grandTotal').html(<?= $data->total_invoice; ?>.toLocaleString());
            <?php if (!empty($data->termasuk_pa) && ($data->termasuk_pa == 'true')) : ?>
                $('#includeTaxText').html('(Termasuk Pajak)');
            <?php endif; ?>
        <?php endif; ?>

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

        // $(".tanggal_faktur").val(formattedDateFront);

        // via
        $('.ship_via, #doc_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        });

        $('#doc_type').select2({
            minimumResultsForSearch: -1,
            placeholder: "",
            theme: "bootstrap-5"
        }).change(function() {

            // clear datatable here

            getDocumentList(this.value);
        });

        $("#doc_id").change(function() {
            getDocumentData(this.value);
        });

        $("#tanggal_faktur").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

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

        $('.ship_via, #doc_type, #doc_id')
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

        function getDocumentList(docType) {
            table.clear();

            $.ajax({
                url: `<?= base_url('/invoice-penjualan-lokal/getDocNumber/'); ?>${docType}`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $("#doc_id").empty();
                    $("#doc_id").append(`<option value=""></option>`);

                    res.data.forEach(function(item) {
                        $("#doc_id").append(`<option  value="${item.id}">${item.doc_no}</option>`);
                    })
                }
            });
        }

        function getDocumentData(docId) {
            const docType = $('#doc_type').val();

            table.clear();

            $.ajax({
                url: `<?= base_url('/invoice-penjualan-lokal/getDocumentData/'); ?>${docType}/${docId}`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $('#salesName').val(res.salesName);
                    $('#customerName').val(res.customerName);
                    $('#customerAddress').val(res.customerAddress);
                    $('#termin').val(res.termin);
                    // $('#salesName').val();
                    // $('#tax_status').prop('checked', res.taxStatus);
                    // $('#include_tax').prop('checked', res.includeTax)

                    // add datatable data here
                    table.rows.add(res.itemList).draw(false);

                    // add total here
                    $('#itemSubTotal').html(res.dpp);
                    $('#taxTotal').html(res.tax);
                    $('#grandTotal').html(res.total);
                },
                onError: function(response) {
                    Swal.fire({
                        icon: 'error',
                        title: response.message,
                        confirmButtonColor: '#4e73df',
                    })
                    stopLoading()
                }
            });
        }

        <?php if (!empty($documentData)) : ?>
            const itemList = <?= json_encode($documentData->itemList) ?>;
            table.rows.add(itemList).draw(false);
            // $('#itemSubTotal').html('<?= $documentData->dpp ?>');
            // $('#taxTotal').html('<?= $documentData->tax ?>');
            // $('#grandTotal').html('<?= $documentData->total ?>');
        <?php endif; ?>

        const reCountTotal = () => {
            const taxStatus = $('#tax_status').is(':checked');
            const includeTax = $('#include_tax').is(':checked');
            const itemList = table.rows().data();

            let itemSubTotal = 0;
            let discTotal = 0;
            let taxTotal = 0;
            let taxTotalHtml = 0;
            let dummyTax = 0;

            itemList.map((obj) => {
                const itemAmt = +(obj.amount.replace(/\D/g, ''));
                itemSubTotal += itemAmt;
                discTotal += ((+obj.disc) / 100) * itemAmt;
                if (taxStatus) {
                    dummyTax += (+obj.taxChecked);
                } else {
                    dummyTax += (+obj.tax);
                }
                const taxAmt = itemAmt * (dummyTax / 100);

                if (taxStatus && !includeTax) {
                    taxTotal += taxAmt;
                    // taxTotalHtml += taxAmt;
                } else if (taxStatus && includeTax) {
                    taxTotal += taxAmt;
                }
                taxTotalHtml += taxAmt;
            });

            $('#itemSubTotal').html(itemSubTotal.toLocaleString());
            $('#taxTotal').html(taxTotalHtml.toLocaleString());

            let grandTotal = 0;

            if (taxStatus && includeTax) {
                $('#includeTaxText').html('(Termasuk Pajak)');
                grandTotal = itemSubTotal - discTotal;
            } else if (taxStatus && !includeTax) {
                $('#includeTaxText').html('');
                grandTotal = itemSubTotal + taxTotalHtml - discTotal;
            } else {
                $('#includeTaxText').html('');
                grandTotal = itemSubTotal - discTotal;
            }

            $('#grandTotal').html(grandTotal.toLocaleString());
        };

        $('#tax_status').on('input change paste', function() {

            if (!this.checked) {
                $('#include_tax').prop('checked', false);
            }

            reCountTotal();
        });
        $('#include_tax').on('input change paste', function() {

            const taxStatus = $('#tax_status').is(':checked');

            if (this.checked && !taxStatus) {
                $(this).prop('checked', false);
            }

            reCountTotal();
        });
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

    /* $(".id_customer").change(function() {
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
    }) */

    /* $(".id_surat_jalan").change(function() {
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
    }) */

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

                    const ppn = $('#taxTotal').html();
                    const dpp = $('#itemSubTotal').html()
                    const totalInvoice = $('#grandTotal').html();
                    const noSuratJalan = $('.id_surat_jalan').find(":selected").text()
                    let id = $(".id").val();


                    data.append("total_invoice", totalInvoice)
                    data.append("ppn", ppn)
                    data.append("dpp", dpp)
                    data.append("no_surat_jalan", noSuratJalan)
                    if (!id) {
                        // data.append("tanggal_faktur", tanggalFaktur)
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
                                            window.location.href = "<?= base_url("invoice-penjualan-lokal"); ?>";
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
                                            window.location.href = `<?= base_url("invoice-penjualan-lokal"); ?>`;
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
</script>

<?= $this->endSection(); ?>