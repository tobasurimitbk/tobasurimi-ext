<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1 class="title-name">Tambah Rencana Produksi</h1>
    <div class="col-button-tambah-spp">
        <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("penerimaan-penjualan-lokal"); ?>">
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
            <input type="hidden" value="<?= !empty($dataWorkOrders) ? $dataWorkOrders->id : ""; ?>" class="id" name="id" id="id" />
            <?= csrf_field() ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" value="<?= !empty($dataWorkOrders) ? $dataWorkOrders->wo_no : ""; ?>" class="form-control wo_no" id="wo_no" name="wo_no" placeholder="Kode Produksi">
                                <label for="floatingInput">Kode Produksi</label>
                            </div>
                            <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                <input style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select barang_id" name="customer_id" id="customer_id" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php foreach ($customerList as $customer):?>
                                <option value="<?= $customer->id; ?>"><?= $customer->kode; ?> - <?= $customer->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Customer</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" value="<?= '' ?>" class="form-control" name="payment_date" id="payment_date" placeholder="Payment Date">
                        <label for="floatingInput">Payment Date</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" value="<?= '' ?>" class="form-control" name="rate" id="rate" placeholder="Rate">
                        <label for="floatingInput">Rate</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" value="<?= '' ?>" class="form-control" name="cheque_no" id="cheque_no" placeholder="Cheque No.">
                        <label for="floatingInput">Cheque No.</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" value="<?= '' ?>" class="form-control" name="cheque_date" id="cheque_date" placeholder="Cheque Date">
                        <label for="floatingInput">Cheque Date</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" value="<?= '' ?>" class="form-control" name="cheque_date" id="cheque_date" placeholder="Cheque Date">
                        <label for="floatingInput">Dept</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <textarea class="form-control" id="" name="" ></textarea>
                        <label for="floatingInput">Memo</label>
                    </div>
                </div>
            </div>

            <!-- list barang -->
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">Invoice List</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-tambah-spp" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Invoice No.</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Owing</th>
                                <th>Payment Amount</th>
                                <th>Total Disc.</th>
                                <th>Bayar</th>
                                <th>Tanggal Diskon</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">
                            
                        </tbody>
                    </table>
                </div>
            </div>

        </form>
    </div>
</div>
</section>

<script>
const csrfToken = '<?= csrf_token() ?>';

$(document).ready(function() {
    $('#customer_id').select2({
        placeholder: "",
        theme: "bootstrap-5",
    }).change(function() {
        const customerId = $(this).val();
        
        table.ajax.url(`<?= base_url('order-form-lokal/getByCustomer/') ?>${customerId}`);
        table.ajax.reload();
    });

    $("#payment_date,#cheque_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        info: false,
        serverSide: true,
        deferLoading: true,
        ordering: false,
        order: [
            [1, 'asc']
        ],
        fixedHeader: true,
        paging: false,
        ajax: {
            url: '',
        },
        display: "stripe",
        searching: false,
        columns: [{
            data: "invoice_no",
            className: "text-center",
            orderable: false
        },
        {
            data: "date",
            className: "text-center"
        },
        {
            data: "amt",
            className: "text-center"
        },
        {
            data: "owing",
            className: "text-center",
            render: function (data, type, full, meta) {
                return +data;
            }
        },
        {
            data: "payment_amt",
            className: "text-center"
        },
        {
            data: "total_disc",
            className: "text-center"
        },
        {
            data: "bayar",
            className: "text-center"
        },
        {
            data: "diskon_date",
            className: "text-center"
        }],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        },
        {
            render: function(data, type, row) {
                return `<input class="form-control" type="text" value="">`
            },
            targets: 4
        },
        {
            render: function(data, type, row) {
                return `<div class="form-check"><input class="form-check-input" type="checkbox" ></div>`
            },
            targets: 6
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

    var validator = $(".create-form").validate({
        rules: {
            barang_id: {
                required: true
            },
            production_amt: {
                required: true
            },
            satuan_id: {
                required: true
            },
            target: {
                required: true
            }
        },
        messages: {
            barang_id: {
                required: "Barang wajib diisi"
            },
            production_amt: {
                required: "Hasil wajib diisi"
            },
            satuan_id: {
                required: "Satuan wajib diisi"
            },
            target: {
                required: "Target wajib diisi"
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

                    let id = $(".id").val();
                    // UPDATE
                    if(id)
                    {
                        $.ajax({
                            url: "<?= base_url("work-order/update"); ?>",
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
                                        window.location.href = "<?= base_url("work-order"); ?>" + "/id/" + id;
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
                            url: "<?= base_url("work-order/save"); ?>",
                            data: data,
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            },
                            method: "POST",
                            dataType: "json",
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                console.log(response)
                                csrf.val(response.token);
                                if (response.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                    .then(() => {
                                        window.location.href = "<?= base_url("work-order"); ?>" + "/id/" + response.id;
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
})

const changeStatus = function() {
    let value = document.getElementById('auto_generate').checked ? true : false;

    if(value)
    {
        $(".wo_no").attr("readonly", true);
        $(".wo_no").val("AUTO GENERATE");
    }
    else
    {
        $(".wo_no").attr("readonly", false);
        $(".wo_no").val("");
    }
}
</script>

<?= $this->endSection(); ?>