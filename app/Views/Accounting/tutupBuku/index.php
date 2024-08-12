<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Tutup Buku</h1>
        <button class="btn btn-show-form btn-add float-right">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row mt-3">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Department</th>
                                <th>Bulan</th>
                                <th>Stock</th>
                                <th>Saldo</th>
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
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($dataDivisi as $d) : ?>
                                        <option value="<?= $d['id'] ?>" <?= !empty($rasio) && $rasio->divisi_id == $d['id'] ? "selected" : "" ?>>
                                            <?= $d['divisi']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Departemen</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <div class="input-group input-group-password">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" class="form-control input-picker bulan_closing" id="bulan_closing" name="bulan_closing" placeholder="Bulan Closing" value="">
                                        <label for="floatingInput">Bulan Closing</label>
                                    </div>
                                    <div class="input-group-prepend group-prepend-password align-items-center">
                                        <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
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
                url: "<?= base_url("tutup-buku/all"); ?>",
                dataSrc: "data",
                data: function(data) {
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
                data: "divisi",
                className: "text-center",
            }, {
                data: "bulan",
                className: "text-center",
            }, {
                data: "stock",
                className: "text-center",
                render: function(data, type, row) {
                    console.log(data);

                    // If "akun_coa" exists and is not empty, display a checkbox
                    if (data == 1) {
                        return "<i class='fa fa-check' aria-hidden='true' style='color:green;'></i>";
                    } else { // Otherwise, display a dash "-"
                        return "<i class='fa fa-minus' aria-hidden='true' style='color:red;'></i>";
                    }
                }
            }, {
                data: "saldo",
                className: "text-center",
                render: function(data, type, row) {
                    // If "akun_coa" exists and is not empty, display a checkbox
                    if (data == 1) {
                        return "<i class='fa fa-check' aria-hidden='true' style='color:green;'></i>";
                    } else { // Otherwise, display a dash "-"
                        return "<i class='fa fa-minus' aria-hidden='true' style='color:red;'></i>";
                    }
                }
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
        // create modal show
        $('.btn-show-form').click(function() {
            resetVal();
            $('.title-name').text("Tambah Tutup Buku");
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
            $('.title-name').text("Update Tutup Buku");

            $.ajax({
                url: "<?= base_url("tutup-buku/get"); ?>",
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
                        $("#divisi_id").val(res.data.divisi_id).change();
                        $("#bulan_closing").val(res.data.bulan).change();
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
                title: 'Hapus Tutup Buku ?',
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
                        url: "<?= base_url("tutup-buku/delete"); ?>",
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
                divisi_id: {
                    required: true
                },
                bulan_closing: {
                    required: true
                },
            },
            messages: {
                divisi_id: {
                    required: "Department Wajib Diisi"
                },
                bulan_closing: {
                    required: "Bulan Closing Wajib Diisi"
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
                                url: "<?= base_url("tutup-buku/update"); ?>",
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
                                url: "<?= base_url("tutup-buku/save"); ?>",
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
        $("#divisi_id").val("").change();
        $("#bulan_closing").val("").change();
    }

    $("#bulan_closing").datepicker({
        todayHighlight: true,
        format: "mm/yyyy",
        orientation: "bottom auto",
        autoclose: true,
        startView: "months",
        minViewMode: 1
    })

    //CSS SELECT2 FLOATING LABEL
    $('.divisi_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.divisi_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.divisi_id')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    // Akun divisi
    $('.divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    }).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    })
</script>

<?= $this->endSection(); ?>