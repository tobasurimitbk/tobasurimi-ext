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

            <button class="btn btn-show-form btn-save float-right btn-submit">
                Simpan
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-pinjaman-karyawan" role="form" method="POST" enctype="multipart/form-data">
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
                            <input type="text" class="form-control no_po" id="no_po" name="no_po" <?= !empty($data) ? ($data->no_po === true ? 'disabled=true' : '') : ''; ?> value="<?= !empty($data) ? $data->no_po : ""; ?>" placeholder="No PO">
                            <label for="floatingInput">No PO</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select id_so" name="id_so" id="id_so" <?= !empty($data) ? ($data->id_so === true ? 'disabled=true' : '') : ''; ?> multiple>
                                <option value=""></option>
                                <?php
                                if (!empty($dataSo)) {
                                    foreach ($dataSo as $so) {
                                ?>
                                        <option value="<?= $so->id; ?>" <?= !empty($data) ? ($data->so_id === $so->id ? "selected" : "") : ""; ?>><?= $so->name; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">SO</label>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="date" class="form-control shipping_date" id="shipping_date" name="shipping_date" <?= !empty($data) ? ($data->shipping_date === true ? 'disabled=true' : '') : ''; ?> placeholder="Tanggal Pengiriman" value="<?= !empty($data) ? $data->shipping_date : ""; ?>">
                            <label for="floatingInput">Tanggal Pengiriman</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control no_surat_jalan" id="no_surat_jalan" name="no_surat_jalan" <?= !empty($data) ? ($data->no_surat_jalan === true ? 'disabled=true' : '') : ''; ?> value="<?= !empty($data) ? $data->no_surat_jalan : ""; ?>" placeholder="No surat jalan">
                            <label for="floatingInput">No Surat Jalan</label>
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
    })

    var validator = $(".create-form").validate({
        rules: {
            id_customer: {
                required: true
            },
            id_po: {
                required: true
            },
            id_so: {
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
            id_so: {
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
                    res.forEach(function(item) {
                        $(".id_so").append(`<option  value="${item.id}">${item.no_sales_order}</option>`);
                    })
                }
            })

        } else {
            $(".id_customer").attr("readonly", false)
            $(".id_so").val("");
        }
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

                    // // UPDATE
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
</script>

<?= $this->endSection(); ?>