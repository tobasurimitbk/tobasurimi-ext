<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" id="add_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label></h5>
            </div>
            <div class="modal-body">
                <?= csrf_field() ?>
                <form class="create-form" role="form" method="POST">
                    <input type="hidden" class="id" name="id" id="id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control no_pembayaran" name="no_pembayaran" id="no_pembayaran" placeholder="No Pembayaran">
                                <label for="floatingInput">No Pembayaran</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Jatuh Tempo" <?= !empty($detail) ? 'disabled value="' . date('d/m/Y', strtotime($detail['pembayaranDetail']['payment_date']))  . '"' : '' ?>>
                                    <label for="floatingInput">Tanggal Pembayaran</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 6px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($divisi as $d) : ?>
                                        <option value="<?= $d['id'] ?>">
                                            <?= $d['divisi']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Departemen</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control bayar_ke" name="bayar_ke" id="bayar_ke" placeholder="Pembayaran Ke">
                                <label for="floatingInput">Pembayaran Kepada</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select valas" id="valas" name="valas" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($dataValuta as $valuta) : ?>
                                        <option value="<?= $valuta["id"]; ?>"><?= $valuta["value"]; ?> - <?= $valuta["description"]; ?></option>
                                    <?php endforeach ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Valas</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select " name="metode_pembayaran" id="metode_pembayaran">
                                    <option selected value="">Pilih Metode Pembayaran</option>
                                    <option value="Bank">Bank</option>
                                    <option value="Cash">Cash</option>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Metode Pembayaran</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" class="form-control nominal_pembayaran" name="nominal_pembayaran" id="nominal_pembayaran" placeholder="Nominal Pembayaran">
                                <label for="floatingInput">Nominal Pembayaran</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control pembayaran_oleh" name="pembayaran_oleh" id="pembayaran_oleh" placeholder="Pembayaran Oleh" value="<?= session()->get("login")->name; ?>">
                                <label for="floatingInput">Pembayaran Oleh</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating  mb-3" style="height: 50px;">
                                <select class="form-select" name="akun_kas" id="akun_kas">
                                    <option disabled selected value=""></option>
                                    <?php foreach ($subsAkuns as $subs) : ?>
                                        <option value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                    <?php endforeach ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Debit</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating  mb-3" style="height: 50px;">
                                <select class="form-select" name="akun_selisih" id="akun_selisih">
                                    <option disabled selected value=""></option>
                                    <?php foreach ($subsAkuns as $subs) : ?>
                                        <option value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                    <?php endforeach ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Kredit (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control keterangan" name="keterangan" id="keterangan" placeholder="Keterangan" value="">
                                <label for="floatingInput">Keterangan (Opsional)</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Batal</button>
                <button type="submit" class="btn btn-submit-form">Simpan</button>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="section-header">
        <h1>Pembayaran Lain - Lain</h1>
        <?php if (can('Pembayaran', 'Lain - Lain', 'c')) : ?>
            <a class="btn btn-show-form btn-add float-right" href="#">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <?= csrf_field() ?>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end mb-3">
                <div class="col-md-2">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>No. Pembayaran</th>
                                <th>Departemen</th>
                                <th>Tanggal</th>
                                <th>Metode Pembayaran</th>
                                <th>Valas</th>
                                <th>Nominal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table" style="cursor: pointer;">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    let sort = "id";
    let sortType = "desc";

    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [1, 'asc']
        ],

        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("pembayaran-lain/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        // scrollX: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        //responsive: true,
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                orderable: false,
                width: "5%"
            },
            {
                data: "no_pembayaran",
                className: "text-center"
            },
            {
                data: "divisi",
                className: "text-center"
            },
            {
                data: "tanggal",
                className: "text-center"
            },
            {
                data: "metode_pembayaran",
                className: "text-center"
            },
            {
                data: "valas",
                className: "text-center"
            },
            {
                data: "nominal",
                className: "text-center"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row?.id;
                    let form = '';
                    <?php if (can('Pembayaran', 'Lain - Lain', 'd')) : ?>
                        form += `
                        <div class="mt-0">
                            <button onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                        </div>
                    
                        `;
                    <?php endif; ?>
                    return form;
                }
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

    $(document).ready(function() {
        var validator = $(".create-form").validate({
            rules: {
                no_pembayaran: {
                    required: true
                },
                tanggal: {
                    required: true
                },
                divisi_id: {
                    required: true
                },
                bayar_ke: {
                    required: true
                },
                valas: {
                    required: true
                },
                metode_pembayaran: {
                    required: true
                },
                nominal_pembayaran: {
                    required: true
                },
                pembayaran_oleh: {
                    required: true
                },
                akun_kas: {
                    required: true
                }
            },
            messages: {
                no_pembayaran: {
                    required: "No pembayaran wajib diisi"
                },
                tanggal: {
                    required: "Tanggal pembayaran wajib diisi"
                },
                divisi_id: {
                    required: "Departemen wajib diisi"
                },
                bayar_ke: {
                    required: "Pembayaran oleh wajib diisi"
                },
                valas: {
                    required: "Mata uang wajib diisi"
                },
                metode_pembayaran: {
                    required: "Metode pembayaran wajib diisi"
                },
                nominal_pembayaran: {
                    required: "Nominal pembayaran wajib diisi"
                },
                pembayaran_oleh: {
                    required: "Pembayaran oleh wajib diisi"
                },
                akun_kas: {
                    required: "Debit wajib diisi"
                }
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

        $("#tanggal").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        $('#divisi_id').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5"
        });

        $('#valas').select2({
            placeholder: "Pilih Mata Uang",
            theme: "bootstrap-5"
        });

        $('#akun_kas').select2({
            placeholder: "Pilih Debit",
            theme: "bootstrap-5"
        });

        $('#akun_selisih').select2({
            placeholder: "Pilih Kredit (Opsional)",
            theme: "bootstrap-5"
        });

        $(".dataTable_info").addClass("pt-0");

        $(".btn-show-form").click(function() {
            $(".id").val("");
            $(".title-name").text("Tambah Pembayaran Lain");
            validator.resetForm();
            validator.reset();
            resetForm();
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', 'none');
            $(".add-modal").modal("show")
        })

        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
        })

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            $(".create-form")[0].reset()
            $(".delete-btn").css('display', '');
            let id = data.id;
            $(".title-name").text("Update");

            validator.resetForm();
            validator.reset();

            $.ajax({
                url: "<?= base_url("pembayaran-lain/get"); ?>",
                data: {
                    id: id
                },
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $("#no_pembayaran").val(res?.data?.no_pembayaran);
                        $('#tanggal').val(res?.data?.tanggal);
                        $('#divisi_id').val(res?.data?.divisi_id).change();
                        $('#bayar_ke').val(res?.data?.bayar_ke);
                        $('#valas').val(res?.data?.valas).change();
                        $('#metode_pembayaran').val(res?.data?.metode_pembayaran).change();
                        $('#nominal_pembayaran').val(res?.data?.nominal);
                        $('#pembayaran_oleh').val(res?.data?.pembayaran_oleh);
                        $('#akun_kas').val(res?.data?.akun_kas).change();
                        $('#akun_selisih').val(res?.data?.akun_selisih).change();
                        $('#keterangan').val(res?.data?.keterangan);
                        disabledForm();
                        $(".add-modal").modal("show")
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            })
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
                        let data = new FormData(document.querySelector(".create-form"));
                        $.ajax({
                            url: "<?= base_url("pembayaran-lain/save"); ?>",
                            data: data,
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                setLoading();
                            },
                            complete: function() {
                                stopLoading()
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
                                            table.ajax.reload()
                                            $(".add-modal").modal("hide")
                                        })

                                    resetForm();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                }
                            },

                        });

                    }
                })
            }
        })
    });

    const remove = function(id) {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        Swal.fire({
            icon: 'question',
            title: 'Hapus Pembayaran Ini ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append('id', id);
                $.ajax({
                    url: "<?= base_url("pembayaran-lain/delete"); ?>",
                    data: formData,
                    method: "POST",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            }).then((result) => {
                                // update table
                                table.ajax.reload();
                            });
                        }
                    },
                });
            }
        });
    }

    function disabledForm() {
        $("#no_pembayaran").attr('disabled', true);
        $('#tanggal').attr('disabled', true);
        $('#divisi_id').attr('disabled', true);
        $('#bayar_ke').attr('disabled', true);
        $('#valas').attr('disabled', true);
        $('#metode_pembayaran').attr('disabled', true);
        $('#nominal_pembayaran').attr('disabled', true);
        $('#pembayaran_oleh').attr('disabled', true);
        $('#akun_kas').attr('disabled', true);
        $('#akun_selisih').attr('disabled', true);
        $('#keterangan').attr('disabled', true);

        $('.btn-submit-form').hide();
    }

    function resetForm() {
        $("#no_pembayaran").attr('disabled', false);
        $('#tanggal').attr('disabled', false);
        $('#divisi_id').attr('disabled', false);
        $('#bayar_ke').attr('disabled', false);
        $('#valas').attr('disabled', false);
        $('#metode_pembayaran').attr('disabled', false);
        $('#nominal_pembayaran').attr('disabled', false);
        $('#pembayaran_oleh').attr('disabled', false);
        $('#akun_kas').attr('disabled', false);
        $('#akun_selisih').attr('disabled', false);
        $('#keterangan').attr('disabled', false);

        $("#no_pembayaran").val(null).change();
        $('#tanggal').val(null).change();
        $('#divisi_id').val(null).change();
        $('#bayar_ke').val(null).change();
        $('#valas').val(null).change();
        $('#metode_pembayaran').val(null).change();
        $('#nominal_pembayaran').val(null).change();
        $('#pembayaran_oleh').val(null).change();
        $('#akun_kas').val(null).change();
        $('#akun_selisih').val(null).change();
        $('#keterangan').val(null).change();

        $('.btn-submit-form').show();
    }

    function preventNegativeInput(inputElement) {
        var inputValue = inputElement.value;
        var numericValue = inputValue.replace(/[^0-9.]/g, '');
        numericValue = numericValue.replace(/^0+/g, '');
        numericValue = numericValue.replace(/^\./g, '0.');
        if (parseFloat(numericValue) < 0 || isNaN(parseFloat(numericValue))) {
            inputElement.value = '0';
        } else {
            inputElement.value = numericValue;
        }
    }
</script>
<?= $this->endSection(); ?>