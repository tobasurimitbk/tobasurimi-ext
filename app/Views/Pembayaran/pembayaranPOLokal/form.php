<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1 class="title-name">Tambah</h1>
    <div class="col-button-tambah-spp">
        <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("pembayaran-po-lokal"); ?>">
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
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control no_bukti_pembayaran" id="no_bukti_pembayaran" name="no_bukti_pembayaran" placeholder="No. Pembayaran">
                                <label for="floatingInput">No. Pembayaran</label>
                            </div>
                            <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                <input style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select disabled="true" readonly="true" class="form-select multiple_faktur_id" name="multiple_faktur_id[]" id="multiple_faktur_id[]">
                            <option value=""></option>
                        </select>
                        <label for="floatingInput">No. Terima Faktur</label>
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
                        <input class="form-control input-picker due_date" id="due_date" name="due_date" placeholder="Tanggal Jatuh Tempo">
                        <label for="floatingInput">Tanggal Jatuh Tempo</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input value="<?= session()->get("login")->name; ?>" type="text" readonly="true" class="form-control" placeholder="Pembayaran Oleh">
                        <label for="floatingInput">Pembayaran Oleh</label>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</section>

<script>
$(document).ready(function() {
    var validator = $(".create-form").validate({
        rules: {
            no_bukti_pembayaran: {
                required: true
            },
            "multiple_faktur_id[]": {
                required: true
            },
            nominal_faktur: {
                required: true
            },
            due_date: {
                required: "Tanggal Pembayaran wajib diisi"
            }
        },
        messages: {
            no_bukti_pembayaran: {
                required: "No. Pembayaran wajib diisi"
            },
            "multiple_faktur_id[]": {
                required: "No. Terima Faktur wajib diisi"
            },
            nominal_faktur: {
                required: "Nominal Faktur wajib diisi"
            },
            due_date: {
                required: "Tanggal Pembayaran wajib diisi"
            }
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

    $(".due_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    // MULTIPLE PO ID
    $('.multiple_faktur_id').select2({
        placeholder: "",
        theme: "bootstrap-5"
    })

    //CSS SELECT2 FLOATING LABEL
    $('.multiple_faktur_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.multiple_faktur_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.multiple_faktur_id')
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
                
                }
            })
        }
    })
})

const changeStatus = function()
{
    let value = document.getElementById('auto_generate').checked ? true : false;

    if(value)
    {
        $(".no_bukti_pembayaran").attr("readonly", true);
        $(".no_bukti_pembayaran").val("AUTO GENERATE");
    }
    else
    {
        $(".no_bukti_pembayaran").attr("readonly", false);
        $(".no_bukti_pembayaran").val("");
    }
}
</script>

<?= $this->endSection(); ?>