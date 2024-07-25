<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>


<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($data) ? "Ubah" : "Tambah"; ?> Return Barang</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("return-barang-sales"); ?>">Kembali</a>
            <?php if (!empty($data)) : ?>
                <button class="btn btn-hapus delete-parent float-right btn-delete">
                    Hapus
                </button>
            <?php endif; ?>
            <button class="btn btn-show-form btn-save float-right btn-submit">Simpan</button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form " role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($data) ? $data->id : ""; ?>" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly autocomplete="one-time-code" type="text" class="form-control no_surat_jalan" id="no_surat_retur" name="no_surat_retur" value="<?= !empty($data) ? $data->no_return : $noReturn; ?>" placeholder="Nomor surat Return">
                                <label for="floatingInput">Nomor Surat Return</label>
                            </div>
                            <div style="<?= !empty($data) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" " class=" form-control input-picker shipping_date" id="return_date" name="return_date" value="<?= $data->return_date ?? ""; ?>" <?= !empty($data) ? 'readonly'  : ''; ?>>
                            <label for="floatingInput">Tanggal Return</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select id_invoice" name="id_invoice" id="id_invoice" <?= !empty($data) ? 'disabled'  : ''; ?>>
                                <option value=""></option>
                                <?php foreach ($dataInvoice ?? [] as $invoice) : ?>
                                    <option data-customer_id="<?= $invoice->customer_id; ?>" data-customer_name="<?= $invoice->customer_name; ?>" data-customer_address="<?= $invoice->customer_address; ?>" value="<?= $invoice->id; ?>" <?= !empty($data) ? ($data->id_invoice === $invoice->id ? "selected" : "") : ""; ?>><?= $invoice->no_faktur; ?> - <?= $invoice->customer_kode; ?> <?= $invoice->customer_name; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">No. Invoice - Customer</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select readonly class="form-select id_customer" name="id_customer" id="id_customer" <?= !empty($data) ? ($data->customer_id === true ? 'disabled=true' : '') : ''; ?>>
                                <option value=""></option>
                                <?php foreach ($dataCustomers ?? [] as $customer) : ?>
                                    <option value="<?= $customer->id; ?>" <?= !empty($data) ? ($data->customer_id === $customer->id ? "selected" : "") : ""; ?>><?= $customer->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Nama Customer</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" " class=" form-control" id="customerAddress" name="customerAddress" disabled value="<?= $data->customerAddress ?? ''; ?>">
                            <label for="floatingInput">Alamat</label>
                        </div>
                    </div>
                    <!-- <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control" id="nama_sales" name="nama_sales" disabled value=" <?= $data->no_surat_retur ?? ""; ?>" placeholder="Nomor surat Return">
                            <label for="floatingInput">Nama Sales</label>
                        </div>
                    </div> -->
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <textarea autocomplete="one-time-code" class="form-control" id="note" name="note" placeholder="Keterangan"><?= $data->note ?? ""; ?></textarea>
                            <label for="floatingInput">Keterangan</label>
                        </div>
                    </div>
                </div>

                <!-- list barang -->
                <div class="col-subtitle-modal">
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label font-weight-bold modal-sub-title">List Barang Return</label>
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
                                    <th>Qty Invoice</th>
                                    <th>Qty Return</th>
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
        </div>
        </form>
    </div>
    </div>
</section>


<script>
    const csrfToken = '<?= csrf_token() ?>';

    $(document).ready(function() {

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
                    className: "text-center",
                },
                {
                    data: null,
                    className: "text-center",
                    render: function(data, type, row) {
                        var qty = row.qtyReturn ? row.qtyReturn : row.qty;
                        return `<input type="text" style="height: 40px; padding-bottom: 12px;" class="form-control" value="${qty}">`
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

        // Customer
        $('.id_customer').select2({
            placeholder: "",
            theme: "bootstrap-5",
            disabled: true
        })
        // invoice
        $('.id_invoice').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true
        }).change(function() {
            const customerAddress = $(this).find(':selected').data('customer_address') ? $(this).find(':selected').data('customer_address') : "";
            const customerId = $(this).find(':selected').data('customer_id') ? $(this).find(':selected').data('customer_id') : "";
            // const customerName = $(this).find(':selected').data('customer_name') ? $(this).find(':selected').data('customer_name') : "";
            // const salesName = $(this).find(':selected').data('tipepelanggan') ? $(this).find(':selected').data('tipepelanggan') : "";

            $('#id_customer').val(customerId).change();
            $('#customerAddress').val(customerAddress);
        });

        //CSS SELECT2 FLOATING LABEL
        $('.id_customer, .id_invoice')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.id_customer, .id_invoice')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.id_customer, .id_invoice')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $(".shipping_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        $(".id_invoice").change(function() {
            if ($(".id_invoice").val()) {
                const id = $(this).val();

                $.ajax({
                    url: `<?= base_url('return-barang-sales/get-detail-invoice/'); ?>${id}`,
                    method: "GET",
                    dataType: "json",
                    success: function(res) {

                        table.clear();
                        table.rows.add(res).draw(false);
                    }
                })

            } else {
                $(".id_customer").attr("readonly", false)
            }
        });

        $(".btn-delete").click(function() {
            const csrf = $(`[name="${csrfToken}"]`);
            var dataId = $(".id").val();
            console.log(dataId);
            Swal.fire({
                icon: 'question',
                title: 'Yakin akan di hapus?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url("return-barang-sales/delete"); ?>",
                        data: {
                            id: dataId,
                        },
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            setLoading();
                        },
                        complete: function() {
                            stopLoading();
                        },
                        method: "POST",
                        dataType: "json",
                        success: function(response) {
                            csrf.val(response.token);
                            if (response.status) {
                                Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                    .then(() => {
                                        location.reload();
                                    })
                            }
                        },
                    });
                }
            })
        });
        $(".btn-submit").click(function() {
            var isValid = true;
            var dataError = null;
            $(".id_customer").attr("disabled", false)
            $(".id_invoice").attr("disabled", false)
            const dataTab = table.rows().every(function(rowIdx) {
                const inputVal = $(this.node()).first().find('input').val();
                const rowData = table.row(rowIdx).data();
                if (parseFloat(rowData.qty) < parseFloat(inputVal)) {
                    isValid = false;
                } else {
                    rowData.qtyReturn = inputVal;
                }

                table.row(rowIdx).data(rowData);
            });
            const newTableData = table.rows().data().toArray();
            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Qty return tidak boleh lebih besar dari Qty Invoice',
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
                            setLoading();

                            const id = $(".id").val();
                            let data = new FormData(document.querySelector(".create-form"));
                            data.append('returnedItems', JSON.stringify(newTableData));

                            // // UPDATE
                            if (id) {
                                $.ajax({
                                    url: "<?= base_url("return-barang-sales/update"); ?>",
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
                                                    window.location.href = "<?= base_url("return-barang-sales"); ?>";
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
                                    url: "<?= base_url("return-barang-sales/save"); ?>",
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
                                                    window.location.href = "<?= base_url("return-barang-sales"); ?>";
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

        <?php if (!empty($dataDetail)) : ?>
            var itemList = [];
            <?php foreach ($dataDetail as $value) : ?>
                itemList.push(<?= json_encode($value); ?>);
            <?php endforeach; ?>
            table.rows.add(itemList).draw(false);
        <?php endif; ?>

    })

    var validator = $(".create-form").validate({
        rules: {
            id_customer: {
                required: true
            },
            shipping_date: {
                required: true
            },
            id_invoice: {
                required: true
            },
            no_surat_retur: {
                required: true
            },
        },
        messages: {
            id_customer: {
                required: "Customer wajib diisi"
            },
            shipping_date: {
                required: "Tanggal return wajib diisi"
            },
            id_invoice: {
                required: "No invoice wajib diisi"
            },
            no_surat_retur: {
                required: "No surat return wajib diisi"
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

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $.ajax({
                url: `<?= base_url("/return-barang-sales/get-nomor-surat-return"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res) {
                        $("#no_surat_retur").val(res);
                        $("#no_surat_retur").attr("readonly", true);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $("#no_surat_retur").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $("#no_surat_retur").val("");
                    }
                }
            })
        } else {
            $("#no_surat_retur").attr("readonly", false);
            $("#no_surat_retur").val("");
        }
    }
</script>

<?= $this->endSection(); ?>