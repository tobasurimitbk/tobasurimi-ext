<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Tanda Terima Faktur Lokal</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <div class="input-group input-group-password">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input class="form-control input-picker date_of_receipt" id="date_of_receipt" name="date_of_receipt" placeholder="Tanggal Penerimaan">
                                        <label for="floatingInput">Tanggal Penerimaan</label>
                                    </div>
                                    <div class="input-group-prepend group-prepend-password align-items-center">
                                        <span style="border: 0px" class="input-group-text bg-white" id="basic-addon2">
                                            <i class="fa fa-calendar icon-form icon-dateOfReceipt"></i>
                                        </span>
                                    </div>
                                </div>
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
                                <select multiple class="form-select multiple_po_id" name="multiple_po_id[]" id="multiple_po_id[]">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">No. PO</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <div class="input-group input-group-password">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input class="form-control input-picker due_date" id="due_date" name="due_date" placeholder="Tanggal Jatuh Tempo">
                                        <label for="floatingInput">Tanggal Jatuh Tempo</label>
                                    </div>
                                    <div class="input-group-prepend group-prepend-password align-items-center">
                                        <span style="border: 0px" class="input-group-text bg-white" id="basic-addon2">
                                            <i class="fa fa-calendar icon-form icon-dueDate"></i>
                                        </span>
                                    </div>
                                </div>
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
                                <input type="text" class="form-control information" name="information" id="information" placeholder="Keterangan">
                                <label for="floatingInput">Keterangan</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-2">Batal</button>
                    <button type="submit" class="btn btn-submit-form btn-submit">Simpan</button>
                    <button type="submit" class="btn btn-submit-form btn-submit-cetak">Simpan dan Cetak</button>
                    <button type="button" class="btn btn-discard delete-btn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1>Tanda Terima Faktur Lokal</h1>
    <button class="btn btn-show-form btn-add float-right" data-btn="create-modal">
        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
    </button>
</div>
<div class="card">
    <div class="card-body">
        <div class="row justify-content-end row-col-spp">
            <div class="col mb-3">
                <div class="input-group input-group-password">
                    <input class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal">
                    <div class="input-group-prepend group-prepend-password align-items-center">
                        <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                    </div>
                </div>
            </div>
            <div class="col mb-3">
                <div class="input-group input-group-password">
                    <input class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal">
                    <div class="input-group-prepend group-prepend-password align-items-center">
                        <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                    </div>
                </div>
            </div>
            <div class="col mb-3">
                <input class="form-control search form-out-search" placeholder="Search" value="" />
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>No. Terima Faktur</th>
                            <th>Dari</th>
                            <th>Nominal Faktur</th>
                            <th>Tanggal Jatuh Tempo</th>
                            <th>Tanggal Penerimaan</th>
                            <th>Penerima</th>
                        </tr>
                    </thead>
                    <tbody class="body-table" id="body-table" style="cursor: pointer;">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</section>

<script>
$(document).ready(function() {
    var validator = $(".create-form").validate({
        rules: {
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
            if (elem.hasClass("select2-hidden-accessible")) {
                element = $(".select2-container").parent();
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

    $(".dateStart").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $(".dateEnd").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

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
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
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

    $('.icon-dateStart').click(function() {
        $(".dateStart").focus();
    });

    $('.icon-dateEnd').click(function() {
        $(".dateEnd").focus();
    });

    $('.icon-dateOfReceipt').click(function() {
        $(".date_of_receipt").focus();
    });

    $('.icon-dueDate').click(function() {
        $(".due_date").focus();
    });
    
    $(".btn-show-form").click(function() {
        $(".id").val("");
        $(".title-name").text("Tambah");

        validator.resetForm();
        validator.reset();

        $(".create-form")[0].reset()
        $(".delete-btn").css('display', 'none');

        $.ajax({
            url: `<?= base_url("divisi/dropdown"); ?>`,
            method: "GET",
            dataType: "json",
            success: function(res) {
                $(".multiple_po_id").empty()
                $(".multiple_po_id").val("").change()
                $(".multiple_po_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".multiple_po_id").append(`<option value="${item.id}">${item.divisi}</option>`)
                })

                $(".multiple_po_id").val([]);
                $(".add-modal").modal("show")
            }
        })
    })

    $(".btn-hide-form").click(function() {
        $(".add-modal").modal("hide")
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
})

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