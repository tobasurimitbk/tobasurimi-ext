<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Account Supplier</h1>
        <button class="btn btn-show-form btn-add float-right">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end mt-3">
                <div class="col-md-4">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari Supplier" value="" />
                </div>
            </div>
            <div class="row mt-3">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Supplier</th>
                                <th>Akun Pembelian</th>
                                <th>Akun Penjualan</th>
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

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label></h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data" onSubmit="return false">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select supplier_id" name="supplier_id" id="supplier_id">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($supplierModel)) {
                                        foreach ($supplierModel as $supplier) {
                                    ?>
                                            <option value="<?= $supplier['id']; ?>"><?= $supplier['kode']; ?> <?= $supplier['name']; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Nama Supplier</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select akun_ap_id" name="akun_ap_id" id="akun_ap_id">
                                            <option value=""></option>
                                            <?php
                                            if (!empty($subAkuns)) {
                                                foreach ($subAkuns as $sub) {
                                            ?>
                                                    <option value="<?= $sub->id; ?>"><?= $sub->no_sub; ?> <?= $sub->nama_sub; ?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <label for="floatingInput">AP</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select akun_ar_id" name="akun_ar_id" id="akun_ar_id">
                                            <option value="" data-code=""></option>
                                            <?php
                                            if (!empty($subAkuns)) {
                                                foreach ($subAkuns as $sub_ar) {
                                            ?>
                                                    <option value="<?= $sub_ar->id; ?>"><?= $sub_ar->no_sub; ?> <?= $sub_ar->nama_sub; ?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <label for="floatingInput">AR</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Kembali</button>
                <button type="button" class="btn btn-submit-form">Simpan</button>
                <button type="button" class="btn btn-discard delete-btn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    let sort = "nomor";
    let sortType = "asc";
    $(document).ready(function() {
        const csrfToken = '<?= csrf_token() ?>';
        const table = $('.dataTable').DataTable({

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
                url: "<?= base_url("akun-supplier/all"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.search = $(".search").val();
                    data.sort = sort;
                    data.sortType = sortType;
                }
            },
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            display: "stripe",
            searching: false,
            columns: [{
                data: "no",
                className: "text-center",
                sortable: false,
                width: "5%"
            }, {
                data: "customer_name",
                className: "text-center",
            }, {
                data: "ap_id",
                className: "text-center",
            }, {
                data: "ar_id",
                className: "text-center",
            }, ],
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

        $(".search").keyup(function() {
            table.ajax.reload();
        });
        // create modal show
        $('.btn-show-form').click(function() {
            resetVal();
            $('.title-name').text("Tambah Account Supplier");
            $('.delete-btn').hide();
            $('.add-modal').modal('show');
        });
        // hide modal
        $('.btn-discard').click(function() {
            $('.add-modal').modal('hide');
        });
        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            resetVal();
            const data = table.row(this).data();
            let csrf = $(`[name="${csrfToken}"]`);
            let id = data.id;
            let formData = new FormData();
            formData.append("id", id);
            $('.delete-btn').show();
            $('.title-name').text("Update Account Supplier");

            $.ajax({
                url: "<?= base_url("akun-supplier/get"); ?>",
                data: formData,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                method: "POST",
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(res) {
                    csrf.val();
                    if (res.status) {
                        $("#id").val(id).change();
                        if (res.data.supplier_id != 0) {
                            $('.delete-btn').show();
                            $("#supplier_id").prop("disabled", false);

                            $("#supplier_id").val(res.data.supplier_id).change();
                        } else {
                            $('.delete-btn').hide();
                            $("#supplier_id").prop("disabled", true);
                            $("#supplier_id").val("").change()
                        }
                        $("#akun_ap_id").val(res.data.ap_id).change();
                        $("#akun_ar_id").val(res.data.ar_id).change();
                        $('.add-modal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        });
                    }
                }
            })
        });

        // delete
        $(".delete-btn").click(function() {
            var parentName = $('#parentName').val();
            Swal.fire({
                icon: 'question',
                title: 'Hapus Account Supplier ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    let csrf = $(`[name="${csrfToken}"]`);
                    let id = $("#id").val();

                    // console.log(id);

                    $.ajax({
                        url: "<?= base_url("akun-supplier/delete"); ?>",
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
                                        table.ajax.reload()
                                        $(".add-modal").modal("hide")
                                    });
                            } else {
                                $('#parentName').val(null);
                                $(".add-modal").modal("hide")
                            }
                        },
                    });
                }
            })
        })
        // init validation
        var validator = $(".create-form").validate({
            rules: {
                parentName: {
                    required: true
                },
            },
            messages: {
                parentName: {
                    required: "Kelompok Barang Wajib Diisi"
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
        // action save or update
        $('.btn-submit-form').click(function(e) {
            e.preventDefault();
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
                        let id = $('input[name="id"]').val();
                        let csrf = $(`[name="${csrfToken}"]`);
                        let data = new FormData(document.querySelector(".create-form"));

                        if (id) {
                            $.ajax({
                                url: "<?= base_url("akun-supplier/update"); ?>",
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
                                                table.ajax.reload();
                                                resetVal();
                                                $(".add-modal").modal("hide");
                                            })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        }).then(() => {
                                            resetVal();
                                            $(".add-modal").modal("hide")
                                        });
                                    }
                                },
                                onError: function(response) {
                                    csrf.val(response.token);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    }).then(() => {
                                        $(".add-modal").modal("hide")
                                    });
                                }
                            });
                        } else {
                            $.ajax({
                                url: "<?= base_url("akun-supplier/save"); ?>",
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
                                                table.ajax.reload();
                                                resetVal();
                                                $(".add-modal").modal("hide")
                                            })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        }).then(() => {
                                            resetVal();
                                            $(".add-modal").modal("hide")
                                        });
                                    }
                                },
                                onError: function(response) {
                                    csrf.val(response.token);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    }).then(() => {
                                        $(".add-modal").modal("hide")
                                    });
                                }
                            });
                        }
                    }
                })

            }
        });
    });
    // sort
    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }
    const resetVal = function() {
        $("#supplier_id").val("").change();
        $("#akun_ap_id").val("").change();
        $("#akun_ar_id").val("").change();
    }


    //CSS SELECT2 FLOATING LABEL
    $('.akun_ar_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.akun_ar_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.akun_ar_id')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    //CSS SELECT2 FLOATING LABEL
    $('.akun_ap_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.akun_ap_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.akun_ap_id')
        .parent('div')
        .find('label')
        .css('z-index', '1');


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

    // Akun AP
    $('.akun_ap_id').select2({
        placeholder: "Pilih Akun AP",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    }).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    })

    // Akun AR
    $('.akun_ar_id').select2({
        placeholder: "Pilih Akun AP",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    }).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    })

    // Akun AP
    $('.supplier_id').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    }).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    })
</script>

<?= $this->endSection(); ?>