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
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control letter_no" id="letter_no" name="letter_no" placeholder="No. Penerimaan">
                                <label for="floatingInput">No. Penerimaan</label>
                            </div>
                            <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                <input style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select acceptance_type" id="acceptance_type" name="acceptance_type" aria-label="Floating label select example">
                            <option value="SINGLE ORDER">Single Order</option>
                            <option value="MULTIPLE ORDER">Multiple Order</option>
                        </select>
                        <label for="floatingInput">Penerimaan</label>
                    </div>
                </div>
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select disabled="true" multiple class="form-select multiple_po_id" id="multiple_po_id[]" name="multiple_po_id[]" aria-label="Floating label select example">
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
                            <option value=""></option>
                        </select>
                        <label for="floatingInput">Jenis Dokumen</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" class="form-control invoice_no" name="invoice_no" id="invoice_no" placeholder="No. Invoice">
                        <label for="floatingInput">No. Invoice</label>
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
    </div>
</div>
</section>

<script>
    $(document).ready(function() {
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

    const changeStatus = function()
    {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if(value)
        {
            $(".letter_no").attr("readonly", true);
            $(".letter_no").val("AUTO GENERATE");
        }
        else
        {
            $(".letter_no").attr("readonly", false);
            $(".letter_no").val("");
        }
    }
</script>

<?= $this->endSection(); ?>