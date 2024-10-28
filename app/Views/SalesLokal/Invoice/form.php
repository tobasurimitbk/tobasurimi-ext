<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($data) ? "Update Invoice" : "Tambah Invoice"; ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("invoice-penjualan-lokal"); ?>">
                Kembali
            </a>

            <?php if (!empty($data)) : ?>
                <a class="btn btn-warning btn-print float-right" href="<?= base_url("invoice-penjualan-lokal/print/{$invoice_id}"); ?>" target="_blank">
                    Print
                </a>
            <?php endif; ?>

            <?php if (!empty($documentData)) : ?>
                <?php if ($data->status_posting == "0") : ?>

                    <button class="btn btn-success posting-spp float-right posting-invoice">
                        Posting
                    </button>
                    <button class="btn btn-show-form btn-save float-right btn-submit">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php else : ?>
                <button class="btn btn-show-form btn-save float-right btn-submit">
                    Simpan
                </button>
            <?php endif; ?>

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
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly autocomplete="one-time-code" type="text" class="form-control no_faktur" id="no_faktur" name="no_faktur" value="<?= !empty($data) ? $data->no_faktur : ""; ?>" placeholder="Auto Generate">
                                <label for="floatingInput">No Faktur</label>
                            </div>
                            <div style="<?= !empty($data) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 20px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control tanggal_faktur" id="tanggal_faktur" name="tanggal_faktur" value="<?= $data->tanggal_faktur ?? ""; ?>" placeholder="Tanggal Faktur" <?= !empty($documentData) && $data->status_posting != "0" ? 'disabled' : ''; ?>></input>
                            <label for="floatingInput">Tanggal Faktur</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select" name="doc_type" id="doc_type" <?= !empty($data) ? 'disabled' : ''; ?>>
                                <option value=""></option>
                                <option value="pesanan" <?= !empty($data) ? ($data->document_type == 'pesanan' ? 'selected' : "") : ""; ?>>Pesanan</option>
                                <option value="pengiriman" <?= !empty($data) ? ($data->document_type == 'pengiriman' ? 'selected' : "") : ""; ?>>Pengiriman</option>
                            </select>
                            <label for="floatingInput">Jenis Dokumen</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">


                            <select class="form-select id_customer" name="id_customer" id="id_customer" <?= !empty($data) ? 'disabled' : ''; ?>>
                                <option value=""></option>
                                <?php
                                if (!empty($dataCustomers)) {
                                    foreach ($dataCustomers as $customer) {
                                ?>
                                        <option value="<?= $customer['id']; ?>" <?= !empty($data) ? ($data->id_customer === $customer['id'] ? "selected" : "") : ""; ?>><?= $customer['name']; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Pilih Customer</label>
                        </div>
                    </div>



                    <?php
                    $selectedDocIds = !empty($data) ? json_decode($data->document_id) : [];
                    $selectedDocNos = !empty($data) ? json_decode($data->document_no) : [];
                    ?>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select doc_id" multiple name="doc_id[]" id="doc_id" <?= !empty($documentData) && $data->status_posting != "0" ? 'disabled' : ''; ?>>
                                <option value=""></option>
                                <?php if (!empty($data)) : ?>
                                    <?php if (!empty($selectedDocIds)) : ?>
                                        <?php foreach ($selectedDocIds as $i => $id) : ?>
                                            <option selected value="<?= $id ?>"><?= $selectedDocNos[$i] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <?php foreach ($documentList as $row) : ?>
                                        <?php if (!in_array($row->id, $selectedDocIds)) : ?>
                                            <option value="<?= $row->id ?>"><?= $row->doc_no ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>


                                <?php endif; ?>
                            </select>
                            <label for="floatingInput">Nomor Dokumen</label>
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

                            <select class="form-select termin" id="termin" name="termin" <?= !empty($documentData) && $data->status_posting != "0" ? 'disabled' : ''; ?>>
                                <?php if ($termin != "") : ?>
                                    <option value=""></option>
                                    <?php foreach ($termin as $row) : ?>
                                        <option value="<?= $row['id'] ?>" <?= $data->terms == $row['id'] ? 'selected' : '' ?>><?= $row['value'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                            </select>
                            <label for="floatingInput">Termin</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select jenis_penjualan" name="jenis_penjualan" id="jenis_penjualan">
                                <option value=""></option>
                                <option value="1" <?= !empty($data) ? ($data->jenis_penjualan == 1 ? "selected" : "") : ""; ?>>By Sales</option>
                                <option value="2" <?= !empty($data) ? ($data->jenis_penjualan == 2 ? "selected" : "") : ""; ?>>By Office</option>
                                <option value="3" <?= !empty($data) ? ($data->jenis_penjualan == 3 ? "selected" : "") : ""; ?>>By Ecommerce</option>
                            </select>
                            <label for="floatingInput">Jenis Penjualan</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control" id="salesName" value="<?= $documentData->salesName ?? '' ?>" disabled>
                            <label for="floatingInput">Nama Sales</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 nama_ecommerce_div">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" type="text" class="form-control nama_ecommerce" id="nama_ecommerce" name="nama_ecommerce" value="<?= $data->nama_ecommerce ?? ''; ?>">
                            <label for="floatingInput">Nama Ecommerce</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" class=" form-control" id="no_po" name="no_po" placeholder="Nomor PO" value="<?= $data->no_po ?? ''; ?>">
                            <label for="floatingInput">No. PO</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select ship_via" name="ship_via" id="ship_via" <?= !empty($documentData) && $data->status_posting != "0" ? 'disabled' : ''; ?>>
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
                            <textarea autocomplete="one-time-code" style="height: 100%;" <?= !empty($documentData) && $data->status_posting != "0" ? 'disabled' : ''; ?> class="form-control Keterangan text-area-all" id="keterangan" name="keterangan" placeholder="Keterangan"><?= $data->keterangan ?? ""; ?></textarea>
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
                                            <input autocomplete="one-time-code" class="tax_status" name="tax_status" id="tax_status" type="checkbox" <?= !empty($documentData) && $data->status_posting != "0" ? 'disabled' : ''; ?> <?= !empty($data->status_tax) ? ($data->status_tax == 'true') ? 'checked' : '' : ''; ?>>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-taxes" style="display: none;">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select taxes" name="taxes" id="taxes" <?= !empty($data) ? 'disabled' : ''; ?>>
                                        <option value=""></option>
                                        <?php foreach ($taxData as $value) : ?>
                                            <option value="<?= $value['id']; ?>" data-tax_value="<?= $value['tax_value']; ?>" <?= !empty($data->tax_id) ? ($value['id'] === $data->tax_id ? "selected" : "") : ($value['tax_value'] == 11 ? "selected" : ""); ?>><?= $value['name']; ?>(<?= $value['tax_value']; ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="floatingInput">Pajak</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3" style="height: 50px;">
                                    <label for="floatingInput">Include Pajak</label>
                                    <div class="switch-form-pinjaman-karyawan">
                                        <label class="switch">
                                            <input autocomplete="one-time-code" class="include_tax" name="include_tax" id="include_tax" type="checkbox" <?= !empty($data->termasuk_pa) ? ($data->termasuk_pa == 'true') ? 'checked' : '' : ''; ?> <?= !empty($documentData) && $data->status_posting != "0" ? 'disabled' : ''; ?>>
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

                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Qty Awal</th>
                                    <th>Qty Sekarang</th>
                                    <th>Qty Invoice</th>
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
                                <td style="height: 40px;">PPn (<span id="taxValue"></span>%)</td>
                                <td class="text-right" style="height: 40px;">Rp. <span id="taxTotal">0</span></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold" style="border-top: 1px solid #929292; height: 40px;">Total Invoice <span id="includeTaxText"></span></td>
                                <td style="border-top: 1px solid #929292; height: 40px;" class="text-right font-weight-bold">Rp. <span id="grandTotal">0</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </form>
        </div>

    </div>
    </div>
</section>


<script>
    const csrfToken = '<?= csrf_token() ?>';
    // Get the current date
    var currentDate = new Date();

    let list_items = [];

    // Format the date to your desired representation
    // var formattedDate = currentDate.toLocaleString().slice(0, 9); // You can use other formatting methods if needed
    // var formattedDateFront = moment(currentDate).format("DD/MM/YYYY")
    var tanggalFaktur = moment(currentDate).format("YYYY-MM-DD")

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
                data: "qty_sekarang",
                className: "text-center"
            },
            {
                data: null,
                className: "text-center",
                render: function(data, type, row, meta) {

                    var qtyValue = row.qty_input !== undefined ? row.qty_input : row.qty_sekarang;
                    <?php if (!empty($documentData) && ($data->status_posting != "0")) : ?>
                        if (type === 'display') {
                            return '<input onchange="definisiQtyInput()" type="text" data-id="' + row.id + '" class="form-control input-qty" readonly value="' + qtyValue + '">';
                        } else {
                            return qtyValue;
                        }
                    <?php else : ?>
                        if (type === 'display') {
                            return '<input onchange="definisiQtyInput()" type="text" data-id="' + row.id + '" class="form-control input-qty" value="' + qtyValue + '">';
                        } else {
                            return qtyValue;
                        }

                    <?php endif; ?>


                }
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
    // Display the date on the webpage
    $(document).ready(function() {
        <?php if ($termin == "") : ?>
            getTerminList(this.value);
        <?php endif; ?>

        <?php if (empty($data)) : ?>
            changeStatus()
        <?php endif; ?>

        <?php if (!empty($data->dpp) || !empty($data->ppn) || !empty($data->total_invoice)) : ?>
            $('#taxTotal').html(<?= $data->ppn; ?>.toLocaleString());
            $('#itemSubTotal').html(<?= $data->dpp; ?>.toLocaleString());
            $('#grandTotal').html(<?= $data->total_invoice; ?>.toLocaleString());
            <?php if (!empty($data->termasuk_pa) && ($data->termasuk_pa == 'true')) : ?>
                $('#includeTaxText').html('(Termasuk Pajak)');
            <?php endif; ?>
        <?php endif; ?>

        $('.ship_via, .termin').select2({
            placeholder: "",
            theme: "bootstrap-5"
        });

        $('#doc_type').select2({
            minimumResultsForSearch: -1,
            placeholder: "",
            theme: "bootstrap-5"
        }).change(function() {

            let customerId = $('.id_customer option:selected').val();
            let docType = $('#doc_type option:selected').val();

            getDocumentList(docType, customerId);
        });

        $(".id_customer").select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: false
        }).change(function() {

            let customerId = $('.id_customer option:selected').val();
            let docType = $('#doc_type option:selected').val();

            getDocumentList(docType, customerId);
        });

        $("#tanggal_faktur").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        //CSS SELECT2 FLOATING LABEL
        $('.ship_via, .id_customer, .id_surat_jalan, .doc_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.ship_via, .id_customer, .id_surat_jalan, .doc_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.ship_via, .id_customer, .id_surat_jalan, .doc_id, #doc_type, .termin')
            .parent('div')
            .find('label')
            .css('z-index', '1');
        // Customer
        $('.ship_via').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })
        // Surat jalan
        $('.id_surat_jalan').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //nomor faktur
        function getDocumentList(docType, idCustomer) {

            $.ajax({
                url: `<?= base_url('/invoice-penjualan-lokal/getDocNumber/'); ?>${docType}/${idCustomer}`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $("#doc_id").empty();
                    res.data.forEach(function(item) {
                        $("#doc_id").append(`<option value="${item.id}">${item.doc_no}</option>`);
                    });
                    $("#doc_id").trigger('change');
                }
            });
        }

        function getTerminList() {
            $.ajax({
                url: `<?= base_url("metadata/dropdown"); ?>`,
                method: "GET",
                data: {
                    name: 'termin'
                },
                beforeSend: function() {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                dataType: "json",
                success: function(result) {
                    $(".termin").empty()
                    $(".termin").append(`<option value=""></option>`)
                    result.data.forEach(function(item) {
                        $(".termin").append(`<option value="${item.id}">${item.value.toUpperCase()}</option>`)
                    })

                    $(".termin").val("").change();
                }
            })
        }

        // doc
        $('.doc_id').select2({
            placeholder: "",
            // theme: "bootstrap-5",
            allowClear: false,
        }).change(function() {

            var selectedDocs = $(this).val();

            <?php if (!empty($data)) : ?>

                var array1 = (<?= ($data->document_id) ?>);
                var difference = selectedDocs.filter(item => !array1.includes(Number(item)));

                // console.log(array1);
                // console.log(difference);
                // Fetch data for newly selected documents
                if (difference && difference.length > 0) {
                    difference.forEach(docId => getDocumentData(docId));
                }
            <?php else : ?>

                if (selectedDocs && selectedDocs.length > 0) {
                    selectedDocs.forEach(docId => getDocumentData(docId));
                }

            <?php endif; ?>


            // Remove items for documents that are no longer selected
            updateItemList(selectedDocs);

        });


        function getDocumentData(docId) {
            <?php if (empty($data)) : ?>
                table.clear();
                list_items = [];
            <?php endif; ?>

            const docType = $('#doc_type').val();
            $.ajax({
                url: `<?= base_url('/invoice-penjualan-lokal/getDocumentData/'); ?>${docType}/${docId}`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    // Populate the form fields with document data
                    $('#salesName').val(res.salesName);
                    $('#customerName').val(res.customerName);
                    $('#customerAddress').val(res.customerAddress);
                    $('#nama_ecommerce').val(res.nama_ecommerce);
                    $('#no_po').val(res.no_po);
                    $('#termin').val(res.termin).change();
                    $('#jenis_penjualan').val(res.jenis_penjualan).change();
                    // Add items to list_items array
                    res.itemList.forEach(function(item) {
                        list_items.push(item);
                    });

                    // Update DataTable
                    table.rows.add(res.itemList).draw(false);
                    // Update totals
                    // Recount totals
                    reCountTotal();
                },
                error: function(xhr, status, error) {
                    console.error(`Error fetching data for document ID ${docId}:`, error);
                }
            });
        }

        <?php if (!empty($documentData)) : ?>
            var itemList = [];
            <?php if ($data->status_posting == "0") : ?>
                itemList = <?= json_encode($documentData->itemList) ?>;
            <?php else : ?>
                itemList = <?= json_encode($documentData->itemListPosting) ?>;
            <?php endif; ?>
            console.log(<?= json_encode($documentData) ?>);
            table.rows.add(itemList).draw(false);
            itemList.forEach(function(item) {
                list_items.push(item);
            });
            // drawTableItem(itemList);

        <?php endif; ?>
        $('#tax_status').on('input change paste', function() {
            if (!this.checked) {
                $('#include_tax').prop('checked', false);
                $('.col-taxes').css('display', 'none');
            } else {
                $('.col-taxes').css('display', '');
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

        $('#taxes').on('change', function() {
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
            "doc_id[]": {
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
            "doc_id[]": {
                required: "SO wajib diisi"
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

    $(".btn-submit").click(function() {
        // console.log(list_items);
        var noDocument = $('#doc_id option:selected').text()
        isValid = true;

        $.each(list_items, function(i, v) {
            var element = $('input[data-id="' + v.id + '"].input-qty');
            var input_user = parseFloat(element.val());
            var stok_max = parseFloat(v.qty_sekarang);

            if (input_user > stok_max || isNaN(input_user) || input_user == undefined) {
                dataError = list_items[i];
                isValid = false;
            } else {
                list_items[i].qty_sekarang = stok_max;
                list_items[i].qty_input = input_user;
            }
        });

        // Check if list_items is empty after the loop
        if (list_items.length === 0) {
            isValid = false;
        }

        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Stok barang tidak valid!, Silahkan cek kembali',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        } else {
            if ($(".create-form").valid()) {
                Swal.fire({
                    icon: 'question',
                    title: 'Simpan Data?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Kembali',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const csrf = $(`[name="${csrfToken}"]`);
                        setLoading()
                        var noDocument = $('#doc_id').select2('data').map(function(elem) {
                            return elem.text;
                        });
                        let data = new FormData(document.querySelector(".create-form"));
                        data.append("noDocument", JSON.stringify(noDocument))
                        data.append("items", JSON.stringify(list_items));

                        const ppn = $('#taxTotal').html();
                        const dpp = $('#itemSubTotal').html()
                        const totalInvoice = $('#grandTotal').html();
                        const noSuratJalan = $('.id_surat_jalan').find(":selected").text()
                        const idCustomer = $('.id_customer').find(":selected").val()
                        let id = $(".id").val();


                        data.append("total_invoice", totalInvoice)
                        data.append("ppn", ppn)
                        data.append("dpp", dpp)
                        data.append("no_surat_jalan", noSuratJalan)
                        data.append("id_customer", idCustomer)
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
                                                window.location.href = `<?= base_url("invoice-penjualan-lokal/print"); ?>/${response.id}`;
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
    });

    function updateItemList(selectedDocs) {
        // Remove items for documents that are no longer selected

        list_items = list_items.filter(item => selectedDocs.includes(item.id_sales_order));


        // Clear and redraw DataTable with the updated item list
        table.clear().rows.add(list_items).draw(false);

        // Recalculate totals
        reCountTotal();
    }

    const reCountTotal = () => {
        const taxStatus = $('#tax_status').is(':checked');
        const includeTax = $('#include_tax').is(':checked');
        let taxes = parseFloat($('#taxes option:selected').data('tax_value'));

        const itemList = table.rows().data();

        let itemSubTotal = 0;
        let itemSubTotalTermasukPajak = 0;
        let discTotal = 0;
        let dummyGrandTotal = 0;
        let taxTotalHtml = 0;
        let dummyTax = 0;

        list_items.map((obj) => {
            const itemAmt = parseFloat(obj.amount.replaceAll(',', ''));
            let taxAmt = 0;
            discTotal += ((+obj.disc) / 100) * itemAmt;
            if (taxStatus) {
                taxAmt = itemAmt * (taxes / 100);
            } else {
                taxAmt = itemAmt * ((+obj.tax) / 100);
            }

            if (taxStatus && !includeTax) {
                itemSubTotal += itemAmt;
                taxTotalHtml += taxAmt;
            } else if (taxStatus && includeTax) {
                if (taxes == 11) {
                    itemSubTotal += itemAmt / (1 + (taxes / 100));
                } else if (taxes == 10) {
                    itemSubTotal += itemAmt / (1 + (taxes / 100));
                } else {
                    itemSubTotal += (itemAmt - taxAmt);
                }
                dummyGrandTotal += itemAmt;
                taxTotalHtml += itemAmt - itemSubTotal;
            } else if (!taxStatus && !includeTax) {
                itemSubTotal += itemAmt;
                taxTotalHtml += taxAmt;
            } else {
                itemSubTotal += itemAmt;
                taxTotalHtml += taxAmt;
            }

           


        });
       
        $('#itemSubTotal').html(itemSubTotal.toLocaleString());
        $('#taxTotal').html(taxTotalHtml.toLocaleString());
        $('#taxValue').html(taxes);


        if (taxStatus && includeTax) {
            $('#includeTaxText').html('(Termasuk Pajak)');
            grandTotal = itemSubTotal + taxTotalHtml;
        } else if (taxStatus && !includeTax) {
            $('#includeTaxText').html('');
            grandTotal = itemSubTotal + taxTotalHtml;
        } else {
            $('#includeTaxText').html('');
            grandTotal = itemSubTotal;
        }

        $('#grandTotal').html(grandTotal.toLocaleString());
    };

    function definisiQtyInput() {

        $.each(list_items, function(i, v) {
            var element = $('input[data-id="' + v.id + '"].input-qty');
            var input_user = parseFloat(element.val());
            var stok_max = parseFloat(v.qty_sekarang);


            if (input_user > stok_max || isNaN(input_user) || input_user == undefined) {
                dataError = list_items[i];
                isValid = false;
            } else {
                list_items[i].qty_sekarang = stok_max.toFixed(4);
                list_items[i].qty_input = input_user.toFixed(4);


                // Calculate the amount and format it using .toLocaleString()
                var amount = parseFloat(v.disc.replace(',', '')) != 0 ? (parseFloat(v.harga_barang.replace(',', '')) -
                        ((parseFloat(v.harga_barang.replace(',', '')) * parseFloat(v.disc.replace(',', ''))) / 100)) *
                    parseFloat(v.qty_input) : parseFloat(v.harga_barang.replace(',', '')) * parseFloat(v.qty_input);
                list_items[i].amount = amount.toLocaleString();
            }
        });
        table.clear();
        table.rows.add(list_items).draw(false);
        reCountTotal();
    }

    //posting
    const posting = function(id) {
        // console.log(list_items);
        var noDocument = $('#doc_id option:selected').text()
        $.each(list_items, function(i, v) {
            var element = $('input[data-id="' + v.id + '"].input-qty');
            var input_user = parseFloat(element.val());
            var stok_max = parseFloat(v.qty_sekarang);

            if (input_user > stok_max || isNaN(input_user) || input_user == undefined) {
                dataError = list_items[i];
                isValid = false;
            } else if (input_user === 0) {
                list_items.splice(i, 1); // Remove the item from list_items if input_user is 0
            } else {
                list_items[i].qty_sekarang = stok_max;
                list_items[i].qty_input = input_user;
            }
        });
        // console.log(list_items);

        if ($(".create-form").valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    setLoading()
                    var noDocument = $('#doc_id').select2('data').map(function(elem) {
                        return elem.text;
                    });
                    let data = new FormData(document.querySelector(".create-form"));
                    data.append("noDocument", JSON.stringify(noDocument))
                    data.append("items", JSON.stringify(list_items));

                    const ppn = $('#taxTotal').html();
                    const dpp = $('#itemSubTotal').html()
                    const totalInvoice = $('#grandTotal').html();
                    const noSuratJalan = $('.id_surat_jalan').find(":selected").text()
                    const idCustomer = $('.id_customer').find(":selected").val()
                    let id = $(".id").val();


                    data.append("total_invoice", totalInvoice)
                    data.append("ppn", ppn)
                    data.append("dpp", dpp)
                    data.append("no_surat_jalan", noSuratJalan)
                    data.append("id_customer", idCustomer)
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


                }
            })
        }
    }

    $(".posting-invoice").click(function() {
        // console.log(list_items);
        var noDocument = $('#doc_id option:selected').text()

        if ($(".create-form").valid()) {
            $.each(list_items, function(i, v) {
                var element = $('input[data-id="' + v.id + '"].input-qty');
                var input_user = parseFloat(element.val());
                var stok_max = parseFloat(v.qty_sekarang);

                if (input_user > stok_max || isNaN(input_user) || input_user == undefined || input_user == 0) {
                    dataError = list_items[i];
                    isValid = false;
                } else {
                    list_items[i].qty_sekarang = stok_max;
                    list_items[i].qty_input = input_user;
                }
            });
            Swal.fire({
                icon: 'question',
                title: 'Posting Invoice?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    setLoading()
                    var noDocument = $('#doc_id').select2('data').map(function(elem) {
                        return elem.text;
                    });
                    let data = new FormData(document.querySelector(".create-form"));
                    data.append("noDocument", JSON.stringify(noDocument))
                    data.append("items", JSON.stringify(list_items));

                    const ppn = $('#taxTotal').html();
                    const dpp = $('#itemSubTotal').html()
                    const totalInvoice = $('#grandTotal').html();
                    const noSuratJalan = $('.id_surat_jalan').find(":selected").text()
                    const idCustomer = $('.id_customer').find(":selected").val()
                    let id = $(".id").val();


                    data.append("total_invoice", totalInvoice)
                    data.append("ppn", ppn)
                    data.append("dpp", dpp)
                    data.append("no_surat_jalan", noSuratJalan)
                    data.append("id_customer", idCustomer)
                    if (!id) {
                        // data.append("tanggal_faktur", tanggalFaktur)
                    }

                    // UPDATE
                    if (id) {
                        $.ajax({
                            url: "<?= base_url("invoice-penjualan-lokal/posting"); ?>",
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
                }
            })
        }
    });


    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $.ajax({
                url: `<?= base_url("/invoice-penjualan-lokal/get-nomor-faktur"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $("#no_faktur").val(res.data);
                        $("#no_faktur").attr("readonly", true);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $("#no_faktur").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $("#no_faktur").val("");
                    }
                }
            })
        } else {
            $("#no_faktur").attr("readonly", false);
            // $("#no_faktur").val("");
        }
    }
</script>

<?= $this->endSection(); ?>