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
                                if (!empty($dataCustomer)) {
                                    foreach ($dataCustomer as $customer) {
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
                            <input type="text" class="form-control no_surat_jalan" id="no_surat_jalan" name="no_surat_jalan" <?= !empty($data) ? ($data->no_surat_jalan === true ? 'disabled=true' : '') : ''; ?> value="<?= !empty($data) ? $data->no_surat_jalan : ""; ?>" placeholder="Surat Jalan">
                            <label for="floatingInput">Surat Jalan</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select id_po" name="id_po" id="id_po" <?= !empty($data) ? ($data->id_po === true ? 'disabled=true' : '') : ''; ?>>
                                <option value=""></option>
                                <?php
                                if (!empty($dataPo)) {
                                    foreach ($dataPo as $po) {
                                ?>
                                        <option value="<?= $po->id; ?>" <?= !empty($data) ? ($data->po_id === $po->id ? "selected" : "") : ""; ?>><?= $po->name; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">PO</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select id_surat_jalan" name="id_surat_jalan" id="id_surat_jalan" <?= !empty($data) ? ($data->id_surat_jalan === true ? 'disabled=true' : '') : ''; ?>>
                                <option value=""></option>
                                <?php
                                if (!empty($dataSuratJalan)) {
                                    foreach ($dataSuratJalan as $surat) {
                                ?>
                                        <option value="<?= $surat->id; ?>" <?= !empty($data) ? ($data->surat_id === $surat->id ? "selected" : "") : ""; ?>><?= $surat->name; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Surat Jalan</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control ship_via" id="ship_via" name="ship_via" <?= !empty($data) ? ($data->ship_via === true ? 'disabled=true' : '') : ''; ?> placeholder="Tanggal Pengiriman" value="<?= !empty($data) ? $data->ship_via : ""; ?>">
                            <label for="floatingInput">Pengiriman via</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <textarea <?= !empty($data) ? ($data->Keterangan === true ? 'disabled=true' : '') : ''; ?> class="form-control Keterangan" id="Keterangan" name="Keterangan" placeholder="Keterangan"><?= !empty($data) ? $data->Keterangan : ""; ?></textarea>
                            <label for="floatingInput">Keterangan</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="number" class="form-control total_invoice" id="total_invoice" name="total_invoice" <?= !empty($data) ? ($data->total_invoice === true ? 'disabled=true' : '') : ''; ?> placeholder="Total Invoice" value="<?= !empty($data) ? $data->total_invoice : ""; ?>">
                            <label for="floatingInput">Total Invoice</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control terms" id="terms" name="terms" <?= !empty($data) ? ($data->terms === true ? 'disabled=true' : '') : ''; ?> value="<?= !empty($data) ? $data->terms : ""; ?>" placeholder="terms">
                            <label for="floatingInput">Terms</label>
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

        // PO
        $('.id_po').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.id_po')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.id_po')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.id_po')
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

        // SO
        $('.id_surat_jalan').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.id_surat_jalan')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.id_surat_jalan')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.id_surat_jalan')
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
</script>

<?= $this->endSection(); ?>