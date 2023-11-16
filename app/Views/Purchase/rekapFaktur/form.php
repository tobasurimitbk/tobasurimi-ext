<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tambah</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("rekap-faktur"); ?>">
                Batal
            </a>
            <?php if (!empty($rekapData) && $rekapData->is_posted === false) : ?>
                <button class="btn btn-success posting-rekap">Posting</button>
                <button class="btn btn-hapus delete-parent float-right">Hapus</button>
            <?php endif; ?>

            <?php if (empty($rekapData)) : ?>
                <button class="btn btn-success posting-rekap">Simpan & Posting</button>
                <button class="btn btn-show-form btn-save float-right btn-submit-form">Simpan</button>
            <?php endif; ?>

        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= $rekapData->id ?? '' ?>" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating ff-supplier mb-3" style="height: 50px;">
                            <select class="form-select " name="supplier_id" id="supplier">
                                <option disabled selected value=""></option>
                                <?php foreach ($supplierList as $supplier) : ?>
                                    <option value="<?= $supplier->id ?>" <?= (!empty($rekapData) && $supplier->id == $rekapData->supplier_id) ? 'selected' : '' ?>><?= $supplier->name ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Supplier</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select" name="invoices[]" id="supplier-faktur" multiple>
                                <?php foreach ($fakturList ?? [] as $faktur) : ?>
                                    <option value="<?= $faktur->id ?>" <?= (in_array($faktur->id, $selectedFaktur)) ? 'selected' : '' ?>><?= $faktur->no_penerimaan_barang ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">No. LPB</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" class="form-control input-picker due_date" id="due_date" name="due_date" value="<?= $rekapData->due_date ?? '' ?>" placeholder="Tanggal Jatuh Tempo">
                            <label for="floatingInput">Tanggal Jatuh Tempo</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="<?= session()->get("login")->name; ?>" type="text" readonly="true" class="form-control" placeholder="Pembayaran Oleh">
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

        $(".due_date").datepicker({
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
            $("#supplier-faktur").empty();
            $("#supplier-faktur").select2({
                multiple: true,
                theme: "bootstrap-5",
                ajax: {
                    // url: '<?= base_url() . 'terima-faktur-lokal/getBySupplier/' ?>' + $(this).val(),
                    url: '<?= base_url() . 'penerimaan-barang-lokal/dropdown/bySupplier/' ?>' + $(this).val(),
                    dataType: 'json',
                    processResults: function(res) {
                        return {
                            results: $.map(res.data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.no_penerimaan_barang
                                }
                            })
                        };
                    }
                }
            });
        });

        $('#supplier-faktur').select2({
            // placeholder: "Pilih Bro",
            theme: "bootstrap-5"
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

        // posting button action
        $(".posting-rekap").click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Yakin akan di Posting?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Posting',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // const csrf = $(`[name="${csrfToken}"]`);
                    const id = $(".id").val();
                    const ajaxUrl = id ? `<?= base_url("rekap-faktur/update"); ?>` : '<?= base_url("rekap-faktur/create"); ?>';
                    const formData = $(".create-form").serializeArray();
                    formData.push({
                        name: 'is_posted',
                        value: 1
                    });

                    $.ajax({
                        url: ajaxUrl,
                        data: formData,
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
                                        if (response.isPosted) {
                                            window.location.replace("<?= base_url('rekap-faktur') ?>")
                                        } else {
                                            window.location.reload()
                                        }
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

        });
        // posting button action end

        // delete rekap
        $(".delete-parent").click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Hapus Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    let id = $(".id").val();
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("rekap-faktur/delete"); ?>",
                        data: {
                            id: id
                        },
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
                                        window.location.href = "<?= base_url("rekap-faktur"); ?>"
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
        });
        // delete rekap end

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
                        const id = $(".id").val();
                        const ajaxUrl = id ? `<?= base_url("rekap-faktur/update"); ?>` : '<?= base_url("rekap-faktur/create"); ?>';
                        $.ajax({
                            url: ajaxUrl,
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
                                            window.location.href = `<?= base_url("rekap-faktur"); ?>/${response.id}`;
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
    })

    const changeStatus = function() {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if (value) {
            $(".no_bukti_pembayaran").attr("readonly", true);
            $(".no_bukti_pembayaran").val("AUTO GENERATE");
        } else {
            $(".no_bukti_pembayaran").attr("readonly", false);
            $(".no_bukti_pembayaran").val("");
        }
    }
</script>

<?= $this->endSection(); ?>