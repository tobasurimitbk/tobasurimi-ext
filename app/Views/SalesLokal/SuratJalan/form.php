<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>


<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($data) ? "Ubah" : "Tambah"; ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("surat-jalan"); ?>">
                Batal
            </a>

            <?php if (!empty($data)): ?>
            <a class="btn btn-save float-right" href="<?= base_url("surat-jalan/print/{$data->id}"); ?>">
                Print
            </a>
            <?php endif; ?>

            <button class="btn btn-show-form btn-save float-right btn-submit">
                Simpan
            </button>
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
                            <select class="form-select id_customer" name="id_customer" id="id_customer" <?= !empty($data) ? ($data->id_customer === true ? 'disabled=true' : '') : ''; ?>>
                                <option value=""></option>
                                <?php
                                if (!empty($dataCustomers)) {
                                    foreach ($dataCustomers as $customer) {
                                ?>
                                        <option value="<?= $customer->id; ?>" <?= !empty($data) ? ($data->id_customer === $customer->id ? "selected" : "") : ""; ?>><?= $customer->name; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Nama Customer</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select id_so" name="id_so[]" id="id_so[]" <?= !empty($data) ? ($data->multiple_id_so === true ? 'disabled=true' : '') : ''; ?> multiple>
                                <option value=""></option>
                                <?php
                                if (!empty($dataSo)) {
                                    foreach ($dataSo as $so) {
                                ?>
                                        <option value="<?= $so->id; ?>,<?= $so->no_sales_order ?>" <?= !empty($data) ? (in_array($so->id, $data->multiple_id_so) ? "selected" : "") : ""; ?>><?= $so->no_sales_order; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">SO</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" " class=" form-control" id="no_po" name="no_po" placeholder="Nomor PO" value="<?= $data->no_po ?? ''; ?>">
                            <label for="floatingInput">No. PO</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" " class=" form-control" id="tagihan_ke" name="tagihan_ke" value="<?= $data->customerAddress ?? ''; ?>" disabled>
                            <label for="floatingInput">Tagihan ke</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" " class=" form-control" id="no_telp" name="no_telp" value="<?= $data->customerPhone ?? ''; ?>" disabled>
                            <label for="floatingInput">No. Telp</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" " class=" form-control" id="termin" name="termin" value="<?= $data->customerTermin ?? ''; ?>" disabled>
                            <label for="floatingInput">Termin</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" " class=" form-control" id="salesName" name="salesName" value="<?= $data->customerSales ?? ''; ?>" disabled>
                            <label for="floatingInput">Nama sales</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" " class=" form-control input-picker shipping_date" id="shipping_date" name="shipping_date" <?= !empty($data) ? ($data->shipping_date === true ? 'disabled=true' : '') : ''; ?> placeholder="Tanggal Pengiriman" value="<?= !empty($data) ? $data->shipping_date : ""; ?>">
                            <label for="floatingInput">Tanggal Pengiriman</label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control no_surat_jalan" id="no_surat_jalan" name="no_surat_jalan" disabled="true" value=" <?= !empty($data) ? $data->no_surat_jalan : ""; ?>" placeholder="No surat jalan">
                            <label for="floatingInput">No Surat Jalan</label>
                        </div>
                    </div>
                    <input autocomplete="one-time-code" type="hidden" class="form-control id_user" id="id_user" name="id_user" value="<?= $id_user ?>">
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <textarea autocomplete="one-time-code" class="form-control" id="note" name="note" placeholder="Keterangan"><?= $data->note ?? ""; ?></textarea>
                            <label for="floatingInput">Keterangan</label>
                        </div>
                    </div>
                    <input autocomplete="one-time-code" type="hidden" class="form-control id_user" id="id_user" name="id_user" value="<?= $id_user ?>">
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
                data: "qty",
                className: "text-center"
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
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.id_customer')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // SO
        $('.id_so').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.id_so')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.id_so')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.id_so')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $('.id_so')
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
                let customerId = $(".id_customer").val();
                $.ajax({
                    url: "<?= base_url('/surat-jalan/sales-order'); ?>" + "/" + customerId,
                    method: "GET",
                    dataType: "json",
                    success: function(res) {
                        $(".id_so").empty();
                        $(".id_So").append(`<option value=""></option>`);

                        // console.log(res.dataWarehouse)
                        res.soList.forEach(function(item) {
                            $(".id_so").append(`<option  value="${item.id}">${item.no_sales_order}</option>`);
                        });

                        $('#tagihan_ke').val(res.customerData.address);
                        $('#no_telp').val(res.customerData.phone);
                        $('#termin').val(res.customerData.termin);
                        $('#salesName').val(res.customerData.salesName);
                    }
                })

            } else {
                $(".id_customer").attr("readonly", false)
                $(".id_so").val("");
            }
        });

        $(".id_so").change(function () {

            $.ajax({
                url: `<?= base_url('/order-form-lokal/getItemList'); ?>`,
                method: "GET",
                data: {
                    ids: $(this).val()
                },
                dataType: "json",
                success: function(res) {
                    table.clear();
                    console.log(res)
                    table.rows.add(res).draw(false);
                }
            })
        });

        <?php if (!empty($data)): ?>
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
            'id_so[]': {
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
            'id_so[]': {
                required: "SO wajib diisi"
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
                    setLoading()
                    let data = new FormData(document.querySelector(".create-form"));

                    let id = $(".id").val();

                    console.log(data.entries());
                    console.log(id)

                    // // UPDATE
                    if (id) {
                        $.ajax({
                            url: "<?= base_url("surat-jalan/update"); ?>",
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
                                            window.location.href = "<?= base_url("surat-jalan"); ?>";
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
                            url: "<?= base_url("surat-jalan/save"); ?>",
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
                                            window.location.href = "<?= base_url("surat-jalan"); ?>";
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
</script>

<?= $this->endSection(); ?>