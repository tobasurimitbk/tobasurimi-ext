<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1 class="title-name">Tambah</h1>
    <div class="col-button-tambah-spp">
        <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("terima-faktur-import"); ?>">
            Batal
        </a>
        <?php if(!empty($dataTerimaFaktur)){ ?> 
            <?php if($statusUpdate){ ?> 
            <button class="btn btn-hapus delete-parent float-right">
                Hapus
            </button>
            <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("terima-faktur-import/print/{$dataTerimaFaktur->id}")?>')">
                Print
            </button>
            <?php } ?> 

            <?php if(!$statusUpdate){ ?> 
            <button class="btn btn-show-form btn-save float-right btn-submit-form">
                Simpan
            </button>
            <button class="btn btn-show-form btn-save float-right btn-submit-cetak bsc">
                Simpan dan Cetak
            </button>
            <?php
            }
            ?> 

            <?php } else { ?> 
            <button class="btn btn-show-form btn-save float-right btn-submit-form">
                Simpan
            </button>
            <?php } ?> 
    </div>
</div>
<div class="card">
    <div class="card-body">
        <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
            <input autocomplete="off" value="<?= !empty($dataTerimaFaktur) ? $dataTerimaFaktur->id : ""; ?>" type="hidden" class="id" name="id" id="id" />
            <input autocomplete="off" value="<?= !empty($dataTerimaFaktur) ? $dataTerimaFaktur->sender : ""; ?>" type="hidden" class="sender" name="sender" id="sender" />
            <?= csrf_field() ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="off" <?= !empty($dataTerimaFaktur) ? ($dataTerimaFaktur->status_update === 2 ? "disabled=true" : "") : ""; ?> value="<?= $dataTerimaFaktur->invoice_date ?? ""; ?>" class="form-control input-picker datepicker" id="invoice_date" name="invoice_date" placeholder="Tanggal Faktur">
                        <label for="floatingInput">Tanggal Faktur</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="off" value="<?=  $dataTerimaFaktur->faktur_no ?? ""; ?>" type="text" class="form-control no" id="no" name="no" placeholder="No. Terima Faktur" disabled readonly>
                                <label for="floatingInput">No. Terima Faktur</label>
                            </div>
                            <!-- <div style="<?= !empty($dataTerimaFaktur) ? "display:none;" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                <input autocomplete="off" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select <?= !empty($dataTerimaFaktur) ? ($dataTerimaFaktur->status_update === 2 ? "disabled=true" : "") : ""; ?> onchange="changeTipeBahan()" class="form-select tipe_bahan" id="tipe_bahan" name="tipe_bahan" aria-label="Floating label select example">
                            <option value="" disabled <?= empty($dataTerimaFaktur) ? 'selected' : '' ?>></option>
                            <option value="BAKU" <?= !empty($dataTerimaFaktur) ? ($dataTerimaFaktur->tipe_bahan === "BAKU" ? "selected" : "") : ""; ?>>Bahan Baku</option>
                            <option value="PENOLONG" <?= !empty($dataTerimaFaktur) ? ($dataTerimaFaktur->tipe_bahan === "PENOLONG" ? "selected" : "") : ""; ?>>Bahan Penolong</option>
                        </select>
                        <label for="floatingInput">Tipe</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select <?= !empty($dataTerimaFaktur) ? ($dataTerimaFaktur->status_update === 2 ? "disabled=true" : "") : ""; ?> class="form-select supplier_id" name="supplier_id" id="supplier_id">
                            <option value=""></option>
                            <?php
                            if (!empty($dataSupplier)) {
                                foreach ($dataSupplier as $supplier) {
                            ?>
                                    <option value="<?= $supplier->id; ?>" data-name="<?= $supplier->name; ?>" <?= !empty($dataTerimaFaktur) ? ($dataTerimaFaktur->supplier_id === $supplier->id ? "selected" : "") : ""; ?>><?= "$supplier->kode - $supplier->name"; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <label for="floatingInput">Supplier</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="off" <?= ($isUpdate ?? false) ? "disabled=true" : ""; ?> value="<?= $dataTerimaFaktur->nominal_faktur ?? ''; ?>" class="form-control" id="nominal_faktur" name="nominal_faktur" onkeyup="formatNumber(this)" placeholder="Nominal Faktur">
                        <label for="floatingInput">Nominal Faktur</label>
                    </div>
                </div>
            </div>

            <?php if ($isUpdate ?? false): ?>
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Daftar Penerimaan Barang</label>
                </div>
                <div class="col-md-12 col-table-button-tts">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-form-tts" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No.</th>
                                    <th onclick="changeSort('faktur_no')" class="sort">No. PO</th>
                                    <th onclick="changeSort('sender')" class="sort">Tgl. LPB</th>
                                    <th onclick="changeSort('nominal_faktur')" class="sort">No. LPB</th>
                                    <th onclick="changeSort('due_date')" class="sort">Nama Barang</th>
                                    <th onclick="changeSort('date_of')" class="sort">Qty LPB</th>
                                    <th onclick="changeSort('date_of')" class="sort">Qty Retur</th>
                                    <th onclick="changeSort('date_of')" class="sort">Qty telah terima</th>
                                    <th onclick="changeSort('date_of')" class="sort">Qty akan diterima</th>
                                    <th onclick="changeSort('recipient')" class="sort">satuan</th>
                                </tr>
                            </thead>
                            <tbody class="body-table" id="body-table" style="cursor: pointer;">

                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-primary" id="select-item-btn">Pilih</button>
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Daftar penerimaan barang yang akan dibuat tanda terima</label>
                </div>
                <div class="col-md-12 mb-5">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts" id="selectedItemTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No. PO</th>
                                    <th>Tgl. LPB</th>
                                    <th>No. LPB</th>
                                    <th>Nama Barang</th>
                                    <th>Qty</th>
                                    <th>satuan</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="body-table">
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="off" <?= !empty($dataTerimaFaktur) ? ($dataTerimaFaktur->status_update === 2 ? "disabled=true" : "") : ""; ?> value="<?= $dataTerimaFaktur->receive_date ?? ""; ?>" class="form-control input-picker datepicker" id="receive_date" name="receive_date" placeholder="Tanggal Penerimaan">
                        <label for="floatingInput">Tanggal Penerimaan</label>
                    </div>
                </div>
                <!-- <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="off" value="<?= $dataTerimaFaktur->item_total ?? 0; ?>" type="text" class="form-control nominal_faktur" name="nominal_faktur" id="nominal_faktur" disabled readonly>
                        <label for="floatingInput">Total Nominal Faktur</label>
                    </div>
                </div> -->
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input autocomplete="off" type="text" <?= !empty($dataTerimaFaktur) ? ($dataTerimaFaktur->status_update === 2 ? "disabled=true" : "") : ""; ?> class="form-control information" name="potongan" id="potongan" value="<?= $dataTerimaFaktur->potongan ?? ""; ?>" onkeyup="formatNumber(this)" placeholder="Keterangan">
                        <label for="floatingInput">Potongan</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input autocomplete="off" type="text" <?= !empty($dataTerimaFaktur) ? ($dataTerimaFaktur->status_update === 2 ? "disabled=true" : "") : ""; ?> class="form-control information" name="tambahan" id="tambahan" value="<?= $dataTerimaFaktur->tambahan ?? ""; ?>" onkeyup="formatNumber(this)" placeholder="Keterangan">
                        <label for="floatingInput">Tambahan</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="off" readonly disabled type="text" class="form-control recipient" id="InvFinalAmt" value="<?= $dataTerimaFaktur->nominal_faktur ?? 0; ?>" />
                        <label for="floatingInput">Total Setelah Potongan dan Tambahan</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="off" readonly="true" value="<?= !empty($dataTerimaFaktur) ? $dataTerimaFaktur->recipient : session()->get("login")->name; ?>" type="text" class="form-control recipient" name="recipient" id="recipient" placeholder="Penerima">
                        <label for="floatingInput">Penerima</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <textarea <?= !empty($dataTerimaFaktur) ? ($dataTerimaFaktur->status_update === 2 ? "disabled=true" : "") : ""; ?> class="form-control information text-area-all" name="information" id="information" placeholder="Keterangan"><?= $dataTerimaFaktur->information ?? ""; ?></textarea>
                        <label for="floatingInput">Keterangan</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Pengenaan Pajak</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="off" class="form-control input-picker datepicker" id="tax_inv_date" name="tax_inv_date" placeholder="Tanggal Faktur Pajak">
                        <label for="floatingInput">Tanggal Faktur Pajak</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="off" type="text" class="form-control" id="tax_inv_no" name="tax_inv_no" placeholder="No. Faktur Pajak">
                        <label for="floatingInput">No. Faktur Pajak</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select" name="tax_type" id="tax_type">
                            <option value="" disabled selected></option>
                            <option value="PPN Masukan">PPN Masukan</option>
                            <option value="PPN Masukan 11%">PPN Masukan 11%</option>
                            <option value="PPh Pasal 21">PPh Pasal 21</option>
                            <option value="PPh Pasal 23">PPh Pasal 23</option>
                            <option value="PPh Pasal 4 (2)">PPh Pasal 4 (2)</option>
                        </select>
                        <label for="floatingInput">Pilih Pajak</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="off" class="form-control" id="tax_amt" name="tax_amt" placeholder="Jumlah">
                        <label for="floatingInput">Jumlah</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select" name="tax_status" id="tax_status">
                            <option value="" disabled selected></option>
                            <option value="Pajak dipungut oleh negara">Pajak dipungut oleh negara</option>
                            <option value="Pajak dikembalikan lagi">Pajak dikembalikan lagi</option>
                        </select>
                        <label for="floatingInput">Status</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <textarea class="form-control information text-area-all" id="tax_note" name="tax_note" placeholder="Keterangan"></textarea>
                        <label for="floatingInput">Keterangan</label>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-md-12 col-table-button-tts">
                    <button type="button" class="btn btn-primary" id="add-tax-btn">Tambah Pengenaan Pajak</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts" id="taxTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>Tgl. Faktur Pajak</th>
                                    <th>No. Faktur Pajak</th>
                                    <th>Pajak</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="body-table">
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>
</section>

<script>
    const id = $(".id").val();
    let sort = "faktur_no";
    let sortType = "asc";
    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        <?= ($statusUpdate ?? true) ? 'deferLoading: true,' : '' ?>
        ordering: false,
        order: [
            [1, 'asc']
        ],

        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 10,
        ajax: {
            url: `<?= base_url("penerimaan-barang-lokal/receivedItemsBySupplier/"); ?>${$(".supplier_id").val()}`,
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        // scrollX: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        //responsive: true,
        display: "stripe",
        searching: false,
        columns: [{
            data: "id",
            className: "text-center",
            orderable: false
        },
        {
            data: "no_po",
            className: "text-center"
        },
        {
            data: "lpb_date",
            className: "text-center"
        },
        {
            data: "no_lpb",
            className: "text-center"
        },
        {
            data: "item_name",
            className: "text-center"
        },
        {
            data: "lpb_qty",
            className: "text-center"
        },
        {
            data: "return_qty",
            className: "text-center"
        },
        {
            data: "received_qty",
            className: "text-center"
        },
        {
            data: "qty_will_be_received",
            className: "text-center"
        },
        {
            data: "unit",
            className: "text-center"
        }],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        },
        {
            render: function(data, type, row) {
                return `<div class="form-check"><input autocomplete="off" class="form-check-input" type="checkbox" ></div>`
            },
            targets: 0
        },
        {
            render: function(data, type, row) {
                return `<input autocomplete="off" class="form-control" type="text" value="${data}">`
            },
            targets: 8
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

    const selectedItemTable = $('#selectedItemTable').DataTable({
        lengthChange: true,
        info: false,
        paging: false,
        searching: false,
        ordering: false,
        language: {
            emptyTable: "Tidak Ada Data"
        },
        columns: [{
            data: "no_po",
            className: "text-center"
        },
        {
            data: "lpb_date",
            className: "text-center"
        },
        {
            data: "no_lpb",
            className: "text-center"
        },
        {
            data: "item_name",
            className: "text-center"
        },
        {
            data: "qty",
            className: "text-center"
        },
        {
            data: "unit",
            className: "text-center"
        },
        {
            data: "total",
            className: "text-center"
        },
        {
            className: "text-center"
        }],
        columnDefs: [{
            render: function(data, type, row) {
                return `<button type="button" class="btn btn-danger" data-action="delete-item">Delete</button>`;
            },
            targets: -1
        }]
    });

    const taxTable = $('#taxTable').DataTable({
        lengthChange: true,
        info: false,
        paging: false,
        searching: false,
        ordering: false,
        language: {
            emptyTable: "Tidak Ada Data"
        },
        columns: [{
            data: "nol",
            className: "text-center",
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }
        },
        {
            data: "taxInvDate",
            className: "text-center"
        },
        {
            data: "taxInvNo",
            className: "text-center"
        },
        {
            data: "taxType",
            className: "text-center"
        },
        {
            data: "taxAmt",
            className: "text-center"
        },
        {
            data: "taxStatus",
            className: "text-center"
        },
        {
            data: "taxNote",
            className: "text-center"
        },
        {
            className: "text-center"
        }],
        columnDefs: [{
            render: function(data, type, row) {
                return `<button type="button" class="btn btn-danger" data-action="delete-tax-item">Delete</button>`;
            },
            targets: -1
        }]
    });

$(document).ready(function() {
    const csrfToken = '<?= csrf_token() ?>';

    var validator = $(".create-form").validate({
        rules: {
            no: {
                required: true
            },
            supplier_id: {
                required: true
            },
            nominal_faktur: {
                required: true
            },
            due_date: {
                required: true
            },
            date_of_receipt: {
                required: true
            },
            "multiple_po_id[]": {
                required: true
            }
        },
        messages: {
            no: {
                required: "No. Terima Faktur wajib diisi"
            },
            supplier_id: {
                required: "Supplier wajib diisi"
            },
            nominal_faktur: {
                required: "Nominal Faktur wajib diisi"
            },
            due_date: {
                required: "Tanggal Jatuh Tempo wajib diisi"
            },
            date_of_receipt: {
                required: "Tanggal Penerimaan wajib diisi"
            },
            "multiple_po_id[]": {
                required: "No. PO wajib diisi"
            },
        },
        errorElement: 'span',
        errorClass: 'text-danger',
        errorPlacement: function(error, element) {
            var elem = $(element);
            console.log(elem);
            if (elem.hasClass("multiple_po_id")) {
                element = $(".select2-selection--multiple").parent();
                error.insertAfter(element);
            } else if (elem.hasClass("select2-hidden-accessible")) {
                element = $("#select2-" + elem.attr("id") + "-container").parent(); 
                error.insertAfter(element);
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function(element) {
            $(element).closest('.col-md-6').addClass('has-error');
            $(element).addClass('select-class');

        },
        unhighlight: function(element) {
            $(element).closest('.col-md-6').removeClass('has-error');
            $(element).removeClass('select-class');
        },
    });

    $(".datepicker").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    // MULTIPLE PO ID
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

    // SUPPLIER ID
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

    $(".btn-submit-form").click(function() {
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

                    const nominalFaktur = $('#nominal_faktur').val().replace(/\D/g, '');
                    data.set('nominal_faktur', nominalFaktur);

                    const selectedItems = selectedItemTable.rows().data().toArray();
                    data.append("penerimaan_barang", JSON.stringify(selectedItems));

                    const selectedTaxes = taxTable.rows().data().toArray();
                    data.append("pengenaan_pajak", JSON.stringify(selectedTaxes));

                    // UPDATE
                    if(id)
                    {
                        $.ajax({
                            url: "<?= base_url("terima-faktur-import/update"); ?>",
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
                                        window.location.href = `<?= base_url("terima-faktur-import/"); ?>${id}`;
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
                    else
                    {
                        $.ajax({
                            url: "<?= base_url("terima-faktur-import/create"); ?>",
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
                                        window.location.href = `<?= base_url("terima-faktur-import"); ?>/${response.id}`;
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

    $(".btn-submit-cetak").click(function() {
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

                    data.append("multiple_po_id", JSON.stringify($('.multiple_po_id').val()));
                    var arr_no = $('.multiple_po_id').select2('data').map(function(elem){ 
                        return elem.text 
                    });
                    data.append("multiple_po_no", JSON.stringify(arr_no));

                    let id = $(".id").val();
                    // UPDATE
                    if(id)
                    {
                        $.ajax({
                            url: "<?= base_url("terima-faktur-import/update"); ?>",
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
                                        window.open("<?= getenv('apiURL'); ?>" + "/tandaTerimaFaktur/print/" + id, "_blank");
                                        window.location.href = `<?= base_url("terima-faktur-import"); ?>/${id}`;
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
                    else
                    {
                        $.ajax({
                            url: "<?= base_url("terima-faktur-import/save"); ?>",
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
                                        window.open("<?= getenv('apiURL'); ?>" + "/tandaTerimaFaktur/print/" + response.id, "_blank");
                                        window.location.href = `<?= base_url("terima-faktur-import"); ?>/${response.id}`;
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
                    url: "<?= base_url("terima-faktur-import/delete"); ?>",
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
                                    window.location.href = "<?= base_url("terima-faktur-import"); ?>"
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

    $(".supplier_id").change(function() {
        let name = $(".supplier_id option:selected").data("name") ? $(".supplier_id option:selected").data("name") : "";
        $(".sender").val(name);
        if($(".supplier_id option:selected").val())
        {
            /* if($(".tipe_bahan").val() === "BAKU")
            {
                $.ajax({
                    url: `<?= base_url("penerimaan-barang-import/dropdown"); ?>`,
                    method: "GET",
                    data: {
                        id: $(".supplier_id option:selected").val(),
                        tipe: "BAKU"
                    },
                    dataType: "json",
                    success: function(res) {
                        console.log(res.data)
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
            if($(".tipe_bahan").val() === "PENOLONG")
            {
                $.ajax({
                    url: `<?= base_url("penerimaan-barang-import/dropdown"); ?>`,
                    method: "GET",
                    data: {
                        id: $(".supplier_id option:selected").val(),
                        tipe: "PENOLONG"
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
            } */
            // console.log(table.settings())
            // table.settings.ordering = true;
            table.ajax.url(`<?= base_url("penerimaan-barang-lokal/receivedItemsBySupplier/"); ?>${$(this).val()}`);
            table.ajax.reload();
        }
        else
        {
            $(".multiple_po_id").attr("disabled", true)
            $(".multiple_po_id").empty()
            $(".multiple_po_id").append(`<option value=""></option>`)
            $(".multiple_po_id").val([]);
        }
    });

    $('#select-item-btn').click(() => {
        const selectedRow = $('#dataTable tbody tr td input:checked').closest("tr");
        /* const data = table.row(selectedRow[0]).data();
        console.log(data) */
        
        if (selectedRow.length) {
            selectedRow.each((i, el) => {
                const qtyVal = $(el).find('input[type="text"]').first().val();

                const data = table.row(el).data();
                const selectedData = selectedItemTable.rows().data().toArray().map((obj) => {
                    return obj.id;
                });

                if (selectedData.includes(data.id)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Data Sudah dipilih!',
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                    return;
                }

                if (qtyVal > 0) {
                // if (qtyVal > 0) {
                    const qty = (+qtyVal > +data.qty_will_be_received) ? data.qty_will_be_received : qtyVal;
                    const elColumn = $(el).find('td');

                    const obj = {
                        id: data.id,
                        no_po: data.no_po,
                        lpb_date: data.lpb_date,
                        no_lpb: data.no_lpb,
                        item_name: data.item_name,
                        qty: qty,
                        unit: data.unit,
                        total: data.price * qty,
                    };
                    selectedItemTable.row.add(obj).draw(false);
                }

            });

            const selectedItemTotal = selectedItemTable.rows().data().toArray().reduce((total, obj) => {
                return total += +obj.total;
            }, 0);
            $('#nominal_faktur').val(selectedItemTotal);
            $('#nominal_faktur').trigger('change');
        }
        
    });

    $('#selectedItemTable').on('click', '[data-action="delete-item"]', function() {
        const data = selectedItemTable.row($(this).parent().parent()).data();
        selectedItemTable.row($(this).parent().parent()).remove().draw();

        const selectedItemTotal = selectedItemTable.rows().data().toArray().reduce((total, obj) => {
            return total += +obj.total;
        }, 0);
        $('#nominal_faktur').val(selectedItemTotal);
        $('#nominal_faktur').trigger('change');
    });

    $('#taxTable').on('click', '[data-action="delete-tax-item"]', function() {
        // const data = taxTable.row($(this).parent().parent()).data();
        taxTable.row($(this).parent().parent()).remove().draw();
    });

    $('#add-tax-btn').click(() => {
        const taxInvDate = $('#tax_inv_date');
        const taxInvNo = $('#tax_inv_no');
        const taxType = $('#tax_type');
        const taxAmt = $('#tax_amt');
        const taxStatus = $('#tax_status');
        const taxNote = $('#tax_note');

        if (!taxInvDate.val() || !taxInvNo.val() || !taxType.val() || !taxAmt.val() || !taxStatus.val()) {
            Swal.fire({
                icon: 'error',
                title: 'Data harus diisi!',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
            return;
        }

        const obj = {
            no: 1,
            taxInvDate: taxInvDate.val(),
            taxInvNo: taxInvNo.val(),
            taxType: taxType.val(),
            taxAmt: taxAmt.val(),
            taxStatus: taxStatus.val(),
            taxNote: taxNote.val()
        };
        taxTable.row.add(obj).draw(false);
        
        taxInvDate.val('');
        taxInvNo.val('');
        taxType.val('');
        taxAmt.val('');
        taxStatus.val('');
        taxNote.val('');
    });

    $('#nominal_faktur').change(recalculateInvAmt);
    $('#potongan,#tambahan').keyup(recalculateInvAmt);

    function recalculateInvAmt() {
        const invAmt = $('#nominal_faktur').val() || 0;
        const potongan = $('#potongan').val() || 0;
        const tambahan = $('#tambahan').val() || 0;

        const invAmtNumber = invAmt.replace(/\D/g, '');
        const potonganNumber = potongan.replace(/\D/g, '');
        const tambahanNumber = tambahan.replace(/\D/g, '');

        const total = +invAmtNumber + +potonganNumber - +tambahanNumber;
        $('#InvFinalAmt').val(total);
    }

    if (id) {
        const selectedItemData = <?= json_encode($selectedItems ?? []) ?>;
        selectedItemTable.rows.add(selectedItemData).draw(false);

        const taxList = <?= json_encode($taxData ?? []); ?>;
        console.log(taxList)
        taxTable.rows.add(taxList).draw(false);
    }
});

const changeTipeBahan = function()
{
    $(".sender").val("");
    if($(".tipe_bahan").val() === "BAKU")
    {
        $.ajax({
            url: `<?= base_url("supplier-bahan-baku/dropdown"); ?>`,
            method: "GET",
            dataType: "json",
            success: function(res) {
                $(".supplier_id").empty();

                $(".supplier_id").append(`<option value=""></option>`);

                res.data.forEach(function(item) {
                    $(".supplier_id").append(`<option value="${item.id}" data-name="${item.name}">${item.name}</option>`);
                })

                $(".supplier_id").val("").change();
            }
        })
    }
    if($(".tipe_bahan").val() === "PENOLONG")
    {
        $.ajax({
            url: `<?= base_url("supplier-bahan-penolong/dropdown"); ?>`,
            method: "GET",
            dataType: "json",
            success: function(res) {
                $(".supplier_id").empty();

                $(".supplier_id").append(`<option value=""></option>`);

                res.data.forEach(function(item) {
                    $(".supplier_id").append(`<option value="${item.id}" data-name="${item.name}">${item.name}</option>`);
                })

                $(".supplier_id").val("").change();
            }
        })
    }
}

const changeStatus = function()
{
    let value = document.getElementById('auto_generate').checked ? true : false;

    if(value)
    {
        $(".no").attr("readonly", true);
        $(".no").val("AUTO GENERATE");
    }
    else
    {
        $(".no").attr("readonly", false);
        $(".no").val("");
    }
}

const print = function(url) 
{
    window.open(url, "_blank");
}
</script>

<?= $this->endSection(); ?>