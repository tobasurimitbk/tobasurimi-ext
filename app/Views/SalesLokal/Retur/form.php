<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>


<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($data) ? "Ubah" : "Tambah"; ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("retur"); ?>">Batal</a>
            <button class="btn btn-show-form btn-save float-right btn-submit">Simpan</button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-spp form-add-pinjaman-karyawan" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($data) ? $data->id : ""; ?>" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control no_surat_jalan" id="no_surat_retur" name="no_surat_retur" disabled value=" <?= $data->return_no ?? ""; ?>" placeholder="Nomor surat Return">
                            <label for="floatingInput">Nomor Surat Return</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" " class=" form-control input-picker shipping_date" id="return_date" name="return_date" value="<?= $data->return_date ?? ""; ?>">
                            <label for="floatingInput">Tanggal Return</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select id_customer" name="id_customer" id="id_customer" <?= !empty($data) ? ($data->customer_id === true ? 'disabled=true' : '') : ''; ?>>
                                <option value=""></option>
                                <?php foreach ($dataCustomers ?? [] as $customer) : ?>
                                    <option value="<?= $customer->id; ?>" <?= !empty($data) ? ($data->customer_id === $customer->id ? "selected" : "") : ""; ?>><?= $customer->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Nama Customer</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" " class=" form-control" id="customerAddress" name="customerAddress" disabled value="<?= $data->customerAddress ?? ''; ?>">
                            <label for="floatingInput">Alamat</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control" id="nama_sales" name="nama_sales" disabled value=" <?= $data->no_surat_retur ?? ""; ?>" placeholder="Nomor surat Return">
                            <label for="floatingInput">Nama Sales</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select id_inv" name="id_inv" id="id_inv" <?= !empty($data) ? ($data->sales_order_inv_id === true ? 'disabled=true' : '') : ''; ?>>
                                <option value=""></option>
                                <?php if (!empty($invData)) : ?>
                                    <option value="<?= $invData->id ?>" selected><?= $invData->no_faktur ?></option>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput">SO</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <textarea autocomplete="one-time-code" class="form-control" id="note" name="note" placeholder="Keterangan"><?= $data->note ?? ""; ?></textarea>
                            <label for="floatingInput">Keterangan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3" style="height: 50px;">
                                    <label for="floatingInput">Pajak</label>
                                    <div class="switch-form-pinjaman-karyawan">
                                        <label class="switch">
                                            <input autocomplete="one-time-code" class="tax_status" disabled name="tax_status" id="tax_status" type="checkbox" <?= ($invData->tax_status ?? false) ? 'checked' : ''; ?>>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="mb-3" style="height: 50px;">
                                    <label for="floatingInput">Include Pajak</label>
                                    <div class="switch-form-pinjaman-karyawan">
                                        <label class="switch">
                                            <input autocomplete="one-time-code" class="include_tax" disabled name="include_tax" id="include_tax" type="checkbox" <?= ($invData->include_pa ?? false) ? 'checked' : ''; ?>>
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
                                    <th>No.</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Qty</th>
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
                    data: `<?= empty($data) ? "qty" : "returnQty" ?>`,
                    className: "text-center",
                    render: function(data, type, row) {
                        return `<input type="text" style="height: 40px; padding-bottom: 12px;" class="form-control" value="${data}">`
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
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.id_customer')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.id_customer')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px')

        $('.id_customer')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // INV
        $('.id_inv').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.id_inv')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.id_inv')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px')

        $('.id_inv')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $('.id_inv')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $(".shipping_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        $(".id_customer").change(function() {
            if ($(".id_customer").val()) {
                const customerId = $(".id_customer").val();
                $.ajax({
                    url: `<?= base_url('/customer/getLocalInvoiceList/'); ?>${customerId}`,
                    method: "GET",
                    dataType: "json",
                    success: function(res) {
                        $(".id_inv").empty();
                        $(".id_inv").prepend(`<option value=""></option>`);

                        $('#customerAddress').val(res.address);

                        res.invList.forEach(function(item) {
                            $(".id_inv").append(`<option  value="${item.id}">${item.no_faktur}</option>`);
                        });

                        $(".id_inv").val('').trigger('select2.change');
                        $('#nama_sales').val(res.salesName)
                    }
                })

            } else {
                $(".id_customer").attr("readonly", false)
                $(".id_inv").val("");
            }
        });

        $(".id_inv").change(function() {
            const id = $(this).val();

            $.ajax({
                url: `<?= base_url('/invoice-penjualan-lokal/getItemList/'); ?>${id}`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    // $('#tax_status').prop('checked', res.SOData.taxStatus);
                    // $('#include_tax').prop('checked', res.SOData.includeTax);

                    table.clear();
                    table.rows.add(res.itemList).draw(false);
                }
            })
        });

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
                        const csrf = $(`[name="${csrfToken}"]`);
                        setLoading();

                        const id = $(".id").val();
                        let data = new FormData(document.querySelector(".create-form"));
                        const dataTab = table.rows().every(function(rowIdx) {
                            const inputVal = $(this.node()).first().find('input').val();
                            const rowData = table.row(rowIdx).data();
                            rowData.returnQty = inputVal;

                            table.row(rowIdx).data(rowData);
                        });
                        const newTableData = table.rows().data().toArray();
                        data.append('returnedItems', JSON.stringify(newTableData));

                        // // UPDATE
                        if (id) {
                            $.ajax({
                                url: "<?= base_url("retur/update"); ?>",
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
                                                window.location.href = "<?= base_url("retur"); ?>";
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
                                url: "<?= base_url("retur/save"); ?>",
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
                                                window.location.href = "<?= base_url("retur"); ?>";
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

        <?php if (!empty($data)) : ?>
            const itemList = <?= json_encode($data->itemList); ?>;
            table.rows.add(itemList).draw(false);
        <?php endif; ?>

    })

    var validator = $(".create-form").validate({
        rules: {
            id_customer: {
                required: true
            },
            id_po: {
                required: true
            },
            id_inv: {
                required: true
            },
            no_po: {
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
            id_inv: {
                required: "SO wajib diisi"
            },
            no_po: {
                required: "No PO wajib diisi"
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
</script>

<?= $this->endSection(); ?>