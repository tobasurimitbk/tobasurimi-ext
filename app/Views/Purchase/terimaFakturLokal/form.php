<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tambah</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("terima-faktur-lokal"); ?>">
                Batal
            </a>
            <?php if (!empty($dataTerimaFaktur)) { ?>

                <button class="btn btn-hapus delete-parent float-right">Hapus</button>
                <button class="btn btn-warning btn-print float-right" onclick="print('<?= getenv('apiURL'); ?>/tandaTerimaFaktur/print/<?= $dataTerimaFaktur->id ?>')">Print</button>

                <button class="btn btn-show-form btn-save float-right btn-submit-form">Simpan</button>
                <button class="btn btn-show-form btn-save float-right btn-submit-cetak bsc">Simpan dan Cetak</button>

            <?php } else { ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-form">Simpan</button>
                <button class="btn btn-show-form btn-save float-right btn-submit-cetak bsc">Simpan dan Cetak</button>
            <?php } ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" value="<?= !empty($dataTerimaFaktur) ? $dataTerimaFaktur->id : ""; ?>" type="hidden" class="id" name="id" id="id" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="<?= $dataTerimaFaktur->receive_date ?? ""; ?>" class="form-control input-picker receive_date" id="receive_date" name="receive_date" placeholder="Tanggal Penerimaan">
                            <label for="floatingInput">Tanggal Penerimaan</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" value="<?= $dataTerimaFaktur->inv_no ?? "AUTO GENERATE"; ?>" type="text" class="form-control no" id="no" name="no" placeholder="No. Terima Faktur" disabled>
                                    <label for="floatingInput">No. Terima Faktur</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select supplier_id" name="supplier_id" id="supplier_id">
                                <option value=""></option>
                                <?php foreach ($dataSupplier as $supplier) : ?>
                                    <option value="<?= $supplier->id; ?>" data-name="<?= $supplier->name; ?>" <?= !empty($dataTerimaFaktur) ? ($dataTerimaFaktur->supplier_id === $supplier->id ? "selected" : "") : ""; ?>><?= $supplier->kode; ?> - <?= $supplier->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Supplier</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="<?= $dataTerimaFaktur->inv_total ?? ""; ?>" onkeyup="formatNumber(this)" type="text" class="form-control" name="inv_total" id="inv_total" placeholder="Total Nominal Faktur">
                            <label for="floatingInput">Total Nominal Faktur</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="<?= !empty($dataTerimaFaktur) ? $dataTerimaFaktur->due_date : ""; ?>" class="form-control input-picker due_date" id="due_date" name="due_date" placeholder="Tanggal Jatuh Tempo">
                            <label for="floatingInput">Tanggal Jatuh Tempo</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" readonly="true" value="<?= $dataTerimaFaktur->createdBy ?? session()->get("login")->name; ?>" type="text" class="form-control recipient" name="recipient" id="recipient" placeholder="Penerima">
                            <label for="floatingInput">Penerima</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <textarea autocomplete="one-time-code" class="form-control information text-area-all" name="information" id="information" placeholder="Keterangan"><?= $dataTerimaFaktur->information ?? ""; ?></textarea>
                            <label for="floatingInput">Keterangan</label>
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

        var validator = $(".create-form").validate({
            rules: {
                no: {
                    required: true
                },
                supplier_id: {
                    required: true
                },
                nominal_faktur: {
                    required: true
                },
                due_date: {
                    required: true
                },
                date_of_receipt: {
                    required: true
                },
                "multiple_po_id[]": {
                    required: true
                }
            },
            messages: {
                no: {
                    required: "No. Terima Faktur wajib diisi"
                },
                supplier_id: {
                    required: "Supplier wajib diisi"
                },
                nominal_faktur: {
                    required: "Nominal Faktur wajib diisi"
                },
                due_date: {
                    required: "Tanggal Jatuh Tempo wajib diisi"
                },
                date_of_receipt: {
                    required: "Tanggal Penerimaan wajib diisi"
                },
                "multiple_po_id[]": {
                    required: "No. PO wajib diisi"
                },
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

        $("#receive_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $("#due_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        // MULTIPLE PO ID
        $('.multiple_po_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        });

        // SUPPLIER ID
        $('.supplier_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.supplier_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.supplier_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.supplier_id')
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
                        const csrf = $(`[name="${csrfToken}"]`);
                        setLoading()
                        const data = new FormData(document.querySelector(".create-form"));

                        const invTotalAmt = $('#inv_total').val().replace(/\D/g, '');
                        data.append('inv_total', invTotalAmt);

                        const id = $(".id").val();
                        // UPDATE
                        if (id) {
                            $.ajax({
                                url: "<?= base_url("terima-faktur-lokal/update"); ?>",
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
                                                window.location.href = "<?= base_url("terima-faktur-lokal/"); ?>" + id;
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
                        } else {
                            $.ajax({
                                url: "<?= base_url("terima-faktur-lokal/save"); ?>",
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
                                                window.location.href = "<?= base_url("terima-faktur-lokal/"); ?>" + response.id;
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

        $(".btn-submit-cetak").click(function() {
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

                        data.append("multiple_po_id", JSON.stringify($('.multiple_po_id').val()));
                        var arr_no = $('.multiple_po_id').select2('data').map(function(elem) {
                            return elem.text
                        });
                        data.append("multiple_po_no", JSON.stringify(arr_no));

                        let id = $(".id").val();
                        // UPDATE
                        if (id) {
                            $.ajax({
                                url: "<?= base_url("terima-faktur-lokal/update"); ?>",
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
                                                window.open("<?= getenv('apiURL'); ?>" + "/tandaTerimaFaktur/print/" + id, "_blank");
                                                window.location.href = "<?= base_url("terima-faktur-lokal/"); ?>" + id;
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
                        } else {
                            $.ajax({
                                url: "<?= base_url("terima-faktur-lokal/save"); ?>",
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
                                                window.open("<?= getenv('apiURL'); ?>" + "/tandaTerimaFaktur/print/" + response.id, "_blank");
                                                window.location.href = "<?= base_url("terima-faktur-lokal/"); ?>" + response.id;
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

        // delete
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
                        url: "<?= base_url("terima-faktur-lokal/delete"); ?>",
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
                                        window.location.href = "<?= base_url("terima-faktur-lokal"); ?>"
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
        })

    })

    const changeStatus = function() {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if (value) {
            $(".no").attr("readonly", true);
            $(".no").val("AUTO GENERATE");
        } else {
            $(".no").attr("readonly", false);
            $(".no").val("");
        }
    }

    const print = function(url) {
        window.open(url, "_blank");
    }
</script>

<?= $this->endSection(); ?>