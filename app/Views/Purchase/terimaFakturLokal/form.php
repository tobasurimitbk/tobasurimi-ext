<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1 class="title-name">Tambah</h1>
    <div class="col-button-tambah-spp">
        <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("terima-faktur-lokal"); ?>">
            Batal
        </a>
        <button class="btn btn-show-form btn-save float-right btn-submit-form">
            Simpan
        </button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
            <input type="hidden" class="id" name="id" id="id" />
            <?= csrf_field() ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input class="form-control input-picker date_of_receipt" id="date_of_receipt" name="date_of_receipt" placeholder="Tanggal Penerimaan">
                        <label for="floatingInput">Tanggal Penerimaan</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control no" id="no" name="no" placeholder="No. Terima Faktur">
                                <label for="floatingInput">No. Terima Faktur</label>
                            </div>
                            <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                <input style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select onchange="changeTipeBahan()" class="form-select tipe_bahan" id="tipe_bahan" name="tipe_bahan" aria-label="Floating label select example">
                            <option value="BAKU">Bahan Baku</option>
                            <option value="PENOLONG">Bahan Penolong</option>
                        </select>
                        <label for="floatingInput">Tipe</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select supplier_id" name="supplier_id" id="supplier_id">
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
                        <label for="floatingInput">Supplier</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                    <select multiple disabled="true" class="form-select multiple_po_id" name="multiple_po_id[]" id="multiple_po_id[]">
                            <option value=""></option>
                        </select>
                        <label for="floatingInput">No. PO</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input class="form-control input-picker due_date" id="due_date" name="due_date" placeholder="Tanggal Jatuh Tempo">
                        <label for="floatingInput">Tanggal Jatuh Tempo</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input onkeyup="formatNumber(this)" type="text" class="form-control nominal_faktur" name="nominal_faktur" id="nominal_faktur" placeholder="Nominal Faktur">
                        <label for="floatingInput">Nominal Faktur</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" class="form-control sender" name="sender" id="sender" placeholder="Sender">
                        <label for="floatingInput">Dari</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input value="<?= session()->get("login")->name; ?>" type="text" readonly="true" class="form-control recipient" name="recipient" id="recipient" placeholder="Penerima">
                        <label for="floatingInput">Penerima</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" class="form-control information" name="information" id="information" placeholder="Keterangan">
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
            sender: {
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
            sender: {
                required: "Dari wajib diisi"
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
                        url: "<?= base_url("terima-faktur-lokal/save"); ?>",
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
                                    window.location.href = "<?= base_url("terima-faktur-lokal"); ?>" + "/id/" + response.id;
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
                title: 'Simpan dan Cetak Data?',
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
    })

    $(".supplier_id").change(function() {
        if($(".supplier_id option:selected").val())
        {
            $.ajax({
                url: `<?= base_url("po-lokal-bahan-baku/dropdown"); ?>`,
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

const changeTipeBahan = function()
{
    if($(".tipe_bahan").val() === "BAKU")
    {
        $.ajax({
            url: `<?= base_url("supplier-bahan-baku-lokal/dropdown"); ?>`,
            method: "GET",
            dataType: "json",
            success: function(res) {
                $(".supplier_id").empty();

                $(".supplier_id").append(`<option value=""></option>`);

                res.data.forEach(function(item) {
                    $(".supplier_id").append(`<option value="${item.id}">${item.name}</option>`);
                })

                $(".supplier_id").val("").change();
            }
        })
    }
    if($(".tipe_bahan").val() === "PENOLONG")
    {
        $.ajax({
            url: `<?= base_url("supplier-bahan-penolong-lokal/dropdown"); ?>`,
            method: "GET",
            dataType: "json",
            success: function(res) {
                $(".supplier_id").empty();

                $(".supplier_id").append(`<option value=""></option>`);

                res.data.forEach(function(item) {
                    $(".supplier_id").append(`<option value="${item.id}">${item.name}</option>`);
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
</script>

<?= $this->endSection(); ?>