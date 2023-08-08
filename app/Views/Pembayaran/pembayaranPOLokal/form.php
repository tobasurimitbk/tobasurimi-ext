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
            <input type="hidden" class="id" name="id" id="id" value="<?= $dataPembayaranPOLokal->id ?? '' ?>" />
            <?= csrf_field() ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control no_bukti_pembayaran" id="no_bukti_pembayaran" name="no_bukti_pembayaran" placeholder="No. Pembayaran" value="<?= $dataPembayaranPOLokal->payment_no ?? '' ?>" <?= (!empty($dataPembayaranPOLokal)) ? 'disabled' : '' ?>>
                                <label for="floatingInput">No. Pembayaran</label>
                            </div>
                            <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                <input style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()" <?= (!empty($dataPembayaranPOLokal)) ? 'disabled' : '' ?>>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input class="form-control input-picker due_date" id="payment_date" name="payment_date" placeholder="Tanggal Jatuh Tempo" value="<?= $dataPembayaranPOLokal->payment_date ?? '' ?>">
                        <label for="floatingInput">Tanggal Pembayaran</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select " name="supplier_id" id="supplier">
                            <option disabled selected value=""></option>
                            <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?= $supplier->id ?>" <?= (!empty($dataPembayaranPOLokal) && $dataPembayaranPOLokal->supplier_id == $supplier->id) ? 'selected' : '' ?>><?= $supplier->name ?></option>
                            <?php endforeach ?>
                        </select>
                        <label for="floatingInput" style="z-index: 1;">Supplier</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select" name="summary_id" id="supplier-faktur">
                            <option value=""></option>
                            <?php foreach ($summaryList ?? [] as $summary): ?>
                            <option value="<?= $summary->id ?>" data-amount="<?= floatval($summary->total) ?>" <?= ($summary->id === $dataPembayaranPOLokal->local_po_inv_summary_id) ? 'selected' : '' ?>><?= $summary->summary_no ?></option>
                            <?php endforeach ?>
                        </select>
                        <label for="floatingInput" style="z-index: 1;">Rekap Faktur</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input onkeyup="formatNumber(this)" type="text" class="form-control nominal_pembayaran" name="nominal_pembayaran" id="nominal_pembayaran" value="<?= $dataPembayaranPOLokal->amount ?? 0 ?>" readonly disabled>
                        <label for="floatingInput">Nominal Pembayaran</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input class="form-control input-picker due_date" id="due_date" name="due_date" placeholder="Tanggal Jatuh Tempo" value="<?= $dataPembayaranPOLokal->due_date ?? '' ?>" readonly>
                        <label for="floatingInput">Tanggal Jatuh Tempo</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select " name="payment_method" id="payment_method">
                            <option disabled selected value=""></option>
                            <option value="Cash" <?= !empty($dataPembayaranPOLokal) && $dataPembayaranPOLokal->payment_method == 'Cash' ? 'selected' : '' ?>>Cash</option>
                            <option value="Debit" <?= !empty($dataPembayaranPOLokal) && $dataPembayaranPOLokal->payment_method == 'Debit' ? 'selected' : '' ?>>Debit</option>
                        </select>
                        <label for="floatingInput" style="z-index: 1;">Metode Pembayaran</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input value="<?= session()->get("login")->name; ?>" type="text" readonly="true" class="form-control" placeholder="Pembayaran Oleh">
                        <label for="floatingInput">Pembayaran Oleh</label>
                    </div>
                </div>
            </div>
            
            <div class="col-subtitle-modal mt-3">
                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label font-weight-bold modal-sub-title">Item List</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Tanggal LPB</th>
                                    <th>No. LPB</th>
                                    <th>Item Name</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody class="body-table" id="body-table">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </form>
    </div>
</div>
</section>

<script>
$(document).ready(function() {
    const id = $(".id").val();
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
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

    // data table start
    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        ordering: true,
        order: [
            [1, 'asc']
        ],
        info: false,
        fixedHeader: true,
        lengthChange: false,
        paging: false,
        //responsive: true,
        display: "stripe",
        searching: false,
        columns: [{
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
        }],
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
    // data table end

    $("#payment_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    // MULTIPLE PO ID
    $('#supplier').select2({
        placeholder: "",
        theme: "bootstrap-5"
    }).change(function(e) {
        table.clear().draw();
        $("#supplier-faktur").empty();
        $("#supplier-faktur").select2({
            // placeholder: "Pilih Bro",
            theme: "bootstrap-5",
            ajax: {
                url: '<?= base_url() . 'rekap-faktur/supplier/' ?>' + $(this).val(),
                dataType: 'json',
                processResults: function (res) {
                    return {
                        results: $.map(res.data, function (item) {
                            return {
                                id: item.id,
                                text: item.summary_no,
                                amount: +item.total,
                                dueDate: item.due_date
                            }
                        })
                    };
                }
            },
            templateSelection: function(container) {
                $(container.element).attr("data-amount", container.amount);
                $(container.element).attr("data-dueDate", container.dueDate);
                return container.text;
            }
        });
    });

    $("#supplier-faktur").change(async function(e) {
        let total = 0;
        let dueDate = '';
        
        $(this).select2('data').map(function(data) {
            total += data.amount;
            dueDate = data.dueDate;
        });
        
        $('#nominal_pembayaran').val(total);
        $('#due_date').val(dueDate);

        // populate data table here
        const itemList = await getItemList($(this).val());
        table.clear();
        table.rows.add(itemList.data).draw();
    });

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
                    $.ajax({
                        url: "<?= base_url("pembayaran-po-lokal/create"); ?>",
                        data: $(".create-form").serialize(),
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
                                    window.location.href = `<?= base_url("pembayaran-po-lokal"); ?>/${response.id}`;
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

    if (id) {
        const itemList = <?= json_encode($itemList ?? []) ?>;
        table.rows.add(itemList).draw(false);
    }

    function getItemList(invId) {
        return $.ajax({
            url: `<?= base_url("rekap-faktur/getItemList/"); ?>${invId}`,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            method: "GET",
            dataType: "json",
            success: function(response) {
                return response.data;
            },
            onError: function(response) {
                csrf.val(response.token);
                Swal.fire({
                    icon: 'error',
                    title: 'Data Gagal Diambil, coba Lagi',
                    confirmButtonColor: '#4e73df',
                })
                stopLoading()
            }
        });
    }
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