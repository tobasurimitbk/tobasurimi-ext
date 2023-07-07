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

            <button class="btn btn-warning btn-print float-right" onclick="print('<?= getenv('apiURL'); ?>/tandaTerimaFaktur/print/<?= $dataTerimaFaktur->id ?>')">
                Print
            </button>

        <?php } else { ?> 
            <button class="btn btn-show-form btn-save float-right btn-submit-form">
                Simpan
            </button>
            <button class="btn btn-show-form btn-save float-right btn-submit-cetak">
                Simpan dan Cetak
            </button>
        <?php } ?> 
    </div>
</div>
<div class="card">
    <div class="card-body">
        <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
            <input value="<?= !empty($dataTerimaFaktur) ? $dataTerimaFaktur->id : ""; ?>" type="hidden" class="id" name="id" id="id" />
            <input value="<?= !empty($dataTerimaFaktur) ? $dataTerimaFaktur->sender : ""; ?>" type="hidden" class="sender" name="sender" id="sender" placeholder="Sender">
            <input value="<?= !empty($dataTerimaFaktur) ? $dataTerimaFaktur->recipient : ""; ?>" type="hidden" class="recipient" name="recipient" id="recipient" placeholder="Penerima">
            <?= csrf_field() ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input <?= !empty($dataTerimaFaktur) ? "disabled=true" : ""; ?> value="<?= !empty($dataTerimaFaktur) ? $dataTerimaFaktur->date_of_receipt : ""; ?>" class="form-control input-picker date_of_receipt" id="date_of_receipt" name="date_of_receipt" placeholder="Tanggal Penerimaan">
                        <label for="floatingInput">Tanggal Penerimaan</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= !empty($dataTerimaFaktur) ? "disabled=true" : ""; ?> value="<?= !empty($dataTerimaFaktur) ? $dataTerimaFaktur->faktur_no : ""; ?>" type="text" class="form-control no" id="no" name="no" placeholder="No. Terima Faktur">
                                <label for="floatingInput">No. Terima Faktur</label>
                            </div>
                            <div style="<?= !empty($dataTerimaFaktur) ? "display:none;" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                <input style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select <?= !empty($dataTerimaFaktur) ? "disabled=true" : ""; ?> onchange="changeTipeBahan()" class="form-select tipe_bahan" id="tipe_bahan" name="tipe_bahan" aria-label="Floating label select example">
                            <option value="BAKU" <?= !empty($dataTerimaFaktur) ? ($dataTerimaFaktur->tipe_bahan === "BAKU" ? "selected" : "") : ""; ?>>Bahan Baku</option>
                            <option value="PENOLONG" <?= !empty($dataTerimaFaktur) ? ($dataTerimaFaktur->tipe_bahan === "PENOLONG" ? "selected" : "") : ""; ?>>Bahan Penolong</option>
                        </select>
                        <label for="floatingInput">Tipe</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select <?= !empty($dataTerimaFaktur) ? "disabled=true" : ""; ?> class="form-select supplier_id" name="supplier_id" id="supplier_id">
                            <option value=""></option>
                            <?php
                            if (!empty($dataSupplier)) {
                                foreach ($dataSupplier as $supplier) {
                            ?>
                                    <option value="<?= $supplier->id; ?>" data-name="<?= $supplier->name; ?>" <?= !empty($dataTerimaFaktur) ? ($dataTerimaFaktur->supplier_id === $supplier->id ? "selected" : "") : ""; ?>><?= $supplier->kode; ?> - <?= $supplier->name; ?></option>
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
                        <select multiple disabled="true" class="form-select multiple_po_id" name="multiple_po_id[]" id="multiple_po_id[]">
                            <option value=""></option>
                            <?php
                            if (!empty($dataNo)) {
                                foreach ($dataNo as $no) {
                            ?>
                                    <option value="<?= $no->id; ?>" <?= (!empty($dataTerimaFaktur) ? (in_array($no->id, $dataTerimaFaktur->multiple_po_id) ? "selected" : "") : ""); ?>><?= $no->po_no; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <label for="floatingInput">No. PO</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input <?= !empty($dataTerimaFaktur) ? "disabled=true" : ""; ?> value="<?= !empty($dataTerimaFaktur) ? $dataTerimaFaktur->due_date : ""; ?>" class="form-control input-picker due_date" id="due_date" name="due_date" placeholder="Tanggal Jatuh Tempo">
                        <label for="floatingInput">Tanggal Jatuh Tempo</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input <?= !empty($dataTerimaFaktur) ? "disabled=true" : ""; ?> value="<?= !empty($dataTerimaFaktur) ? $dataTerimaFaktur->nominal_faktur : ""; ?>" onkeyup="formatNumber(this)" type="text" class="form-control nominal_faktur" name="nominal_faktur" id="nominal_faktur" placeholder="Nominal Faktur">
                        <label for="floatingInput">Nominal Faktur</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select <?= !empty($dataTerimaFaktur) ? "disabled=true" : ""; ?> class="form-select customer_id" name="customer_id" id="customer_id">
                            <option value=""></option>
                            <?php
                            if (!empty($dataCustomer)) {
                                foreach ($dataCustomer as $customer) {
                            ?>
                                    <option value="<?= $customer->id; ?>" data-name="<?= $customer->name; ?>" <?= !empty($dataTerimaFaktur) ? ($dataTerimaFaktur->customer_id === $customer->id ? "selected" : "") : ""; ?>><?= $customer->name; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <label for="floatingInput">Penerima</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input <?= !empty($dataTerimaFaktur) ? "disabled=true" : ""; ?> type="text" class="form-control information" name="information" id="information" placeholder="Keterangan">
                        <label for="floatingInput">Keterangan</label>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</section>

<script>
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

    $(".date_of_receipt").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $(".due_date").datepicker({
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

    // CUSTOMER ID
    $('.customer_id').select2({
        placeholder: "",
        theme: "bootstrap-5"
    })

    //CSS SELECT2 FLOATING LABEL
    $('.customer_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.customer_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.customer_id')
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

                    data.append("multiple_po_id", JSON.stringify($('.multiple_po_id').val()));
                    var arr_no = $('.multiple_po_id').select2('data').map(function(elem){ 
                        return elem.text 
                    });
                    data.append("multiple_po_no", JSON.stringify(arr_no));

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
                                    window.location.href = "<?= base_url("terima-faktur-import"); ?>" + "/id/" + response.id;
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
                                    window.location.href = "<?= base_url("terima-faktur-import"); ?>" + "/id/" + response.id;
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
        }
    })

    $(".customer_id").change(function() {
        let name = $(".customer_id option:selected").data("name") ? $(".customer_id option:selected").data("name") : "";
        $(".recipient").val(name);
    })

    $(".supplier_id").change(function() {
        let name = $(".supplier_id option:selected").data("name") ? $(".supplier_id option:selected").data("name") : "";
        $(".sender").val(name);
        if($(".supplier_id option:selected").val())
        {
            if($(".tipe_bahan").val() === "BAKU")
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
            }
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