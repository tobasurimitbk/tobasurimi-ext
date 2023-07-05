<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>


<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($data) ? "Ubah" : "Tambah"; ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("order-form-lokal"); ?>">
                Batal
            </a>

            <button class="btn btn-show-form btn-save float-right btn-submit">
                Simpan
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-order-form-lokal" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" class="id" name="id" id="id" value="<?= !empty($data) ? $data->id : ""; ?>" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select id_customer" name="id_customer" id="id_customer" <?= !empty($data) ? ($data->id_customer === true ? 'disabled=true' : '') : ''; ?>>
                                <option value=""></option>
                                <?php
                                if (!empty($dataCustomers)) {
                                    foreach ($dataCustomers as $customer) {
                                ?>
                                        <option value="<?= $customer->id; ?>" <?= !empty($data) ? ($data->customer_id === $customer->id ? "selected" : "") : ""; ?>><?= $customer->name; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Nama Customer</label>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select id_barang" name="id_barang" id="id_barang" <?= !empty($data) ? ($data->id_barang === true ? 'disabled=true' : '') : ''; ?>>
                                <option value=""></option>
                                <?php
                                if (!empty($dataBarang)) {
                                    foreach ($dataBarang as $barang) {
                                ?>
                                        <option value="<?= $barang->id; ?>" <?= !empty($data) ? ($data->barang_id === $barang->id ? "selected" : "") : ""; ?>><?= $barang->nama_barang; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Nama Barang</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control sales_name" id="sales_name" name="sales_name" disabled=true>
                            <label for="floatingInput">Nama Sales</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($data) ? ($data->no_order === true ? 'readonly=true' : '') : ''; ?> type="text" class="form-control no_order" id="no_order" name="no_order" placeholder="No. Order" value="<?= !empty($data) ? $data->no_order : ""; ?>">
                                    <label for="floatingInput">No. SPP</label>
                                </div>
                                <div style="<?= !empty($data) ? ($data->no_order === true ? "display: none" : "") : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="time" class="form-control order_date" id="order_date" name="order_date" <?= !empty($data) ? ($data->order_date === true ? 'disabled=true' : '') : ''; ?> value="<?= !empty($data) ? $data->order_date : ""; ?>" placeholder="Tanggal Pemesanan">
                            <label for="floatingInput">Tanggal Pemesanan</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="time" class="form-control shipping_date" id="shipping_date" name="shipping_date" <?= !empty($data) ? ($data->shipping_date === true ? 'disabled=true' : '') : ''; ?> value="<?= !empty($data) ? $data->shipping_date : ""; ?>" placeholder="End of time">
                            <label for="floatingInput">Tanggal Pengiriman</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control estimated_freight" id="estimated_freight" name="estimated_freight" <?= !empty($data) ? ($data->estimated_freight === true ? 'disabled=true' : '') : ''; ?> placeholder="estimated_freight" value="<?= !empty($data) ? $data->term : ""; ?>">
                            <label for="floatingInput">Estimated Freight</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control terms" id="terms" name="terms" <?= !empty($data) ? ($data->is_posted === true ? 'disabled=true' : '') : ''; ?> placeholder="Terms" value="<?= !empty($data) ? $data->term : ""; ?>">
                            <label for="floatingInput">Terms</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="number" class="form-control ppn" id="ppn" name="ppn" <?= !empty($data) ? ($data->ppn === true ? 'disabled=true' : '') : ''; ?> placeholder="ppn" value="<?= !empty($data) ? $data->term : ""; ?>">
                            <label for="floatingInput">PPN</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control description" id="description" name="description" <?= !empty($data) ? ($data->description === true ? 'disabled=true' : '') : ''; ?> placeholder="description" value="<?= !empty($data) ? $data->term : ""; ?>">
                            <label for="floatingInput">Deskripsi</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3" style="height: 50px;">
                            <label for="floatingInput">Pajak</label>
                            <div class="switch-form-pinjaman-karyawan">
                                <label class="switch">
                                    <input class="tax_status" <?= !empty($data) ? ($data->tax_status === true ? 'disabled=true' : '') : ''; ?> name="tax_status" id="tax_status" type="checkbox" <?= !empty($data) ? ($data->tax_status === true ? 'checked' : '') : ''; ?>>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3" style="height: 50px;">
                            <label for="floatingInput">Include pa</label>
                            <div class="switch-form-pinjaman-karyawan">
                                <label class="switch">
                                    <input class="include_pa" <?= !empty($data) ? ($data->include_pa === true ? 'disabled=true' : '') : ''; ?> name="include_pa" id="include_pa" type="checkbox" <?= !empty($data) ? ($data->include_pa === true ? 'checked' : '') : ''; ?>>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>
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

        // Barang
        $('.id_barang').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.id_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.id_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.id_barang')
            .parent('div')
            .find('label')
            .css('z-index', '1');
    })

    var validator = $(".create-form").validate({
        rules: {
            nama_karyawan: {
                required: true
            },
            total_pinjaman: {
                required: true
            },
            termin_pembayaran: {
                required: true
            },
        },
        messages: {
            nama_karyawan: {
                required: "Nama Karyawan wajib diisi"
            },
            total_pinjaman: {
                required: "Total Pinjaman wajib diisi"
            },
            termin_pembayaran: {
                required: "Termin Pembayaran wajib diisi"
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

                    // UPDATE
                    if (id) {
                        $.ajax({
                            url: "<?= base_url("pinjaman-karyawan/update"); ?>",
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
                                            window.location.href = "<?= base_url("pinjaman-karyawan"); ?>";
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
                            url: "<?= base_url("pinjaman-karyawan/save"); ?>",
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
                                            window.location.href = "<?= base_url("pinjaman-karyawan"); ?>";
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

    const changeStatus = function() {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if (value) {
            $(".no_order").attr("readonly", true);
            $(".no_order").val("AUTO GENERATE");
        } else {
            $(".no_order").attr("readonly", false);
            $(".no_order").val("");
        }
    }
</script>

<?= $this->endSection(); ?>