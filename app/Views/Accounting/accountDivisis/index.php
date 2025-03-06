<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Akun Department</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end mt-3">
                <div class="col-md-2">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari Department" value="" />
                </div>
            </div>
            <div class="row mt-3">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Department</th>
                                <th>Akun Kas</th>
                                <th>Akun Piutang</th>
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
                                <input readonly autocomplete="one-time-code" type="text" class="form-control" placeholder="Nama Department" id="parentName" name="parentName">
                                <label for="floatingInput">Nama Department</label>
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
                                        <label for="floatingInput">Akun Kas</label>
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
                                        <label for="floatingInput">Akun Piutang</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select akun_gaji_id" name="akun_gaji_id" id="akun_gaji_id">
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
                                        <label for="floatingInput">Akun Gaji</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select akun_hpp_id" name="akun_hpp_id" id="akun_hpp_id">
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
                                        <label for="floatingInput">Akun HPP</label>
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
            </div>
        </div>
    </div>
</div>

<script>
    let sort = "nomor";
    let sortType = "desc";
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
                url: "<?= base_url("akun-department/all"); ?>",
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
                data: "parent_name",
                className: "text-center",
            }, {
                data: "coa_kas_id",
                className: "text-center",
            }, {
                data: "coa_piutang_id",
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
        // hide modal
        $('.btn-discard').click(function() {
            $('.add-modal').modal('hide');
        });
        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            let csrf = $(`[name="${csrfToken}"]`);
            let id = data.id;
            let type = $("input[name='type']").val();
            let formData = new FormData();
            $('#parentName').val(null);
            console.log(id);
            formData.append("id", id);

            $('.title-name').text("Update Akun Department");
            $('.delete-btn').show();
            $.ajax({
                url: "<?= base_url("akun-department/get"); ?>",
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
                        console.log(res);
                        $("#id").val(id);
                        $("#parentName").val(res.data.divisi);
                        $("#akun_ap_id").val(res.data.coa_kas_id).change();
                        $("#akun_ar_id").val(res.data.coa_piutang_id).change();
                        $("#akun_gaji_id").val(res.data.coa_gaji_id).change();
                        $("#akun_hpp_id").val(res.data.coa_hpp_id).change();
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
        // init validation
        var validator = $(".create-form").validate({
            rules: {
                parentName: {
                    required: true
                },
            },
            messages: {
                parentName: {
                    required: "Nama Department Wajib Diisi"
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
                        $.ajax({
                            url: "<?= base_url("akun-department/save"); ?>",
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
                                            $('#parentName').val(null);
                                            $(".add-modal").modal("hide")
                                        })
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    }).then(() => {
                                        $('#parentName').val(null);
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

    // Akun AR
    $('.akun_ar_id, .akun_ap_id, .akun_gaji_id, .akun_hpp_id').select2({
        placeholder: "",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    }).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    })

    //CSS SELECT2 FLOATING LABEL
    $('.akun_ar_id, .akun_ap_id, .akun_gaji_id, .akun_hpp_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.akun_ar_id, .akun_ap_id, .akun_gaji_id, .akun_hpp_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.akun_ar_id, .akun_ap_id, .akun_gaji_id, .akun_hpp_id')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    // Akun AP
    // $('.akun_ap_id').select2({
    //     placeholder: "",
    //     theme: "bootstrap-5",
    //     dropdownParent: $(".add-modal .modal-content")
    // })

    // //CSS SELECT2 FLOATING LABEL
    // $('.akun_ap_id')
    //     .parent('div')
    //     .children('span')
    //     .children('span')
    //     .children('span')
    //     .css('height', ' calc(3.5rem + 2px)');

    // $('.akun_ap_id')
    //     .parent('div')
    //     .children('span')
    //     .children('span')
    //     .children('span')
    //     .children('span')
    //     .css('margin-top', '22px').css('margin-left', '-7px');

    // $('.akun_ap_id')
    //     .parent('div')
    //     .find('label')
    //     .css('z-index', '1');
</script>

<?= $this->endSection(); ?>