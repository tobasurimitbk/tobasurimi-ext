<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Form Request Action</h1>
        <div class="col-button-tambah-spp">

            <?php if (can('Personalia', 'Form Request Action', 'p')): ?>
                <a class="btn btn-warning btn-print float-right" href="#" id="btnExport">
                    <i class="fa fa-download"></i> Export
                </a>
            <?php endif; ?>

            <?php if (can('Personalia', 'Form Request Action', 'c')): ?>
                <a class="btn btn-show-form btn-success float-right btn-submit" id="btnAddFormRequestModal" href="#">
                    <i class="fa fa-plus fa-sm me-1"></i> Tambah
                </a>
            <?php endif; ?>
        </div>

    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end">
                <?= csrf_field() ?>
                <div class="col-sm-3">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" value="<?= date('Y-m') ?>" class="form-control filter_year_month"
                                id="filter_year_month" name="filter_year_month" />
                            <label style="z-index: 1;" style="z-index: 1;">Pilih Tahun</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="form-floating">
                        <input autocomplete="one-time-code" class="form-control search" id="search"
                            placeholder="Cari Data" value="" />
                        <label for="floatingInput">Cari Data</label>
                    </div>

                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable"
                        width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width:10px;">No</th>
                                <th onclick="changeSort('statusAudit')" class="sort" style="width:10px;">
                                    <input type="checkbox" class="checked-parent">
                                </th>
                                <th onclick="changeSort('nomor')" class="sort">Nomor</th>
                                <th onclick="changeSort('judul_form')" class="sort">Judul Form</th>
                                <th onclick="changeSort('last_update_by')" class="sort">Dibuat Oleh</th>
                                <th onclick="changeSort('status_print')" style="width: 70px;" class="sort">
                                    Print
                                </th>
                                <th class="sort" style="width: 100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal formRequestActionModal" id="formRequestActionModal" tabindex="1">
    <div class="modal-dialog modal-lg" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">
                    <label id="title-modal-form-request"></label>
                </h5>
            </div>
            <form class="form-request-action">
                <input type="hidden" name="id" id="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" name="year_month" type="text"
                                    class="form-control year_month" id="year_month" placeholder="Tahun">
                                <label for="floatingInput">Tahun</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control nomor"
                                        placeholder="Nomor" id="nomor" name="nomor" value="" readonly>
                                    <label for="floatingInput">Nomor</label>
                                </div>
                                <div class="input-generate input-group-prepend group-prepend-password align-items-center"
                                    id="form_auto_generate">
                                    <input checked autocomplete="one-time-code"
                                        style="z-index: 99;  margin-left: -30px; margin-top: -20px;"
                                        class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox"
                                        onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" name="judul_form" type="text"
                                    class="form-control judul_form" id="judul_form" placeholder="Judul Form">
                                <label for="floatingInput">Judul Form</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-detail btn-discard mr-3"
                        id="btnHideFormRequest">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitFormRequest">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';

    let sort = "id";
    let sortType = "desc";

    const table = $('.dataTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [4, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("form-request-action/all"); ?>",
            dataSrc: "data",
            data: function (data) {
                data.year_month = $("#filter_year_month").val();
                data.search = $("#search").val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        "initComplete": function (settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
            data: "no",
            className: "text-center",
        },
        {
            data: null,
            className: "text-center actions",
            sortable: false,
            render: function (data, type, row) {
                let id = row.id;
                var htmlRes = `
                            <input 
                                value="${id}"
                                type="checkbox"
                                class="checked-child"
                            >
                        `;

                return htmlRes;
            }
        },
        {
            data: "nomor",
            className: "text-left"
        },
        {
            data: "judul_form",
            className: "text-left"
        },
        {
            data: "last_update",
            className: "text-left",
        },
        {
            data: "status_print",
            className: "text-center",
            render: function (data, type, row) {
                let status_print = row.status_print;
                let htmlRes = '';

                if (row.status_print == "yes") {
                    htmlRes += `
                            <div class="text-success">
                               <i class="fa-solid fa-check"></i>
                            </div>`
                } else {
                    htmlRes += `
                            <div class="text-danger">
                               <i class="fa-solid fa-x"></i>
                            </div>`
                }

                return htmlRes;

            }
        },
        {
            data: "id",
            className: "text-center actions",
            searchable: false,
            sortable: false,
            render: function (data, type, row) {
                let id = row.id;
                return `
                        <div class="mt-0">
                            <?php if (can('Personalia', 'Form Request Action', 'd')): ?>
                                <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Detail" class="btn btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (can('Personalia', 'Form Request Action', 'd')): ?>
                                <button data-toggle="tooltip" title="Hapus" onclick="destroy('${id}')" class="btn btn-danger delete-parent">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    `

            }
        }
        ],
        drawCallback: function () {

            $('[data-toggle="tooltip"]').each(function () {
                if ($(this).data('bs.tooltip')) {
                    $(this).tooltip('dispose');
                }
            });

            $('[data-toggle="tooltip"]').tooltip({
                container: 'body',
                boundary: 'window'
            });
        },
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

    $("#filter_year_month,#year_month").datepicker({
        format: "yyyy-mm",
        startView: "months", // langsung tampilin bulan
        minViewMode: "months", // cuma bisa pilih bulan
        autoclose: false,
        todayHighlight: true,
        orientation: "bottom auto",
        dropdownParent: $('#formRequestActionModal')

    });

    $("#formRequestActionModal").on("hidden.bs.modal", function () {
        $("#year_month").datepicker("destroy");
    });

    $('#filter_year_month').change(function (e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('#search').keyup(function (e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $(".dataTable_info").addClass("pt-0");

    $('#year_month').change(function (e) {
        e.preventDefault();
        changeStatus();
    });

    // VALIDATOR SURAT  
    var validator = $(".form-request-action").validate({
        rules: {
            year_month: {
                required: true
            },
            nomor: {
                required: true
            },
            judul_form: {
                required: true
            },
        },
        messages: {
            year_month: {
                required: "wajib diisi"
            },
            nomor: {
                required: "wajib diisi"
            },
            judul_form: {
                required: "wajib diisi"
            },
        },
        errorElement: 'span',
        errorClass: 'text-danger',
        errorPlacement: function (error, element) {
            var elem = $(element);
            if (elem.hasClass("select2-hidden-accessible")) {
                element = $("#select2-" + elem.attr("id") + "-container").parent();
                error.insertAfter(element);
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function (element) {
            $(element).closest('.form-group').addClass('has-error');
            $(element).addClass('select-class');

        },
        unhighlight: function (element) {
            $(element).closest('.form-group').removeClass('has-error');
            $(element).removeClass('select-class');
        },
    });

    $('#btnAddFormRequestModal').click(function (e) {
        e.preventDefault();
        resetForm();
        changeStatus();
        $('#title-modal-form-request').text('Tambah Nomor');
        $('#formRequestActionModal').modal('show');
    });

    $('#btnHideFormRequest').click(function (e) {
        e.preventDefault();
        $('#formRequestActionModal').modal('hide');
    });

    $('#btnSubmitFormRequest').click(function (e) {
        e.preventDefault();
        if ($('.form-request-action').valid()) {
            var csrf = $(`[name="${csrfToken}"]`);
            var id = $('#id').val();
            var url = id == '' ? "<?= base_url('form-request-action/save') ?>" : "<?= base_url('form-request-action/update') ?>";
            var data = new FormData(document.querySelector(".form-request-action"));
            $.ajax({
                url: url,
                data: data,
                beforeSend: function (xhr) {
                    setLoading();
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                complete: function () {
                    stopLoading();
                },
                method: "POST",
                dataType: "json",
                processData: false,
                contentType: false,
                success: function (response) {
                    csrf.val(response.token);
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        })
                            .then(() => {
                                table.ajax.reload();
                                $('#formRequestActionModal').modal('hide');
                            })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            });
        }
    });

    $(document).on('change', '.checked-parent', function () {
        let isChecked = $(this).is(':checked');

        $('.checked-child').prop('checked', isChecked);
    });

    $(document).on('change', '.checked-child', function () {
        let total = $('.checked-child').length;
        let checked = $('.checked-child:checked').length;

        $('.checked-parent').prop('checked', total === checked);
    });

    $('#btnExport').click(function (e) {
        let idSelected = [];

        $('.checked-child:checked').each(function () {
            idSelected.push($(this).val());
        });

        if (idSelected.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'checklist minimal satu data !',
                confirmButtonColor: '#4e73df',
            });
            return;
        } else {
            var url = "<?= base_url("form-request-action/print"); ?>?id=" + JSON.stringify(idSelected);
            window.open(url, "_blank");
        }
    })

    function edit(id) {
        $.ajax({
            url: "<?= base_url("form-request-action/get"); ?>",
            data: {
                id: id,
            },
            beforeSend: function (xhr) {
                setLoading()
            },
            complete: function () {
                stopLoading();
            },
            method: "GET",
            success: function (response) {
                if (response.status) {
                    var data = response.data;
                    $('#id').val(data.id);
                    $('#year_month').val(data.year_month);
                    $('#judul_form').val(data.judul_form);
                    $('#nomor').val(data.nomor);
                    $('#nomor').prop('readonly', false);

                    $('#auto_generate').hide();
                    $('#title-modal-form-request').text('Update Nomor');
                    $('#formRequestActionModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: response.message,
                        confirmButtonColor: '#4e73df',
                    });
                    return;
                }
            },
        });

    }

    function destroy(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                const formData = new FormData();
                formData.set('id', id);

                $.ajax({
                    url: "<?= base_url("form-request-action/delete"); ?>",
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        setLoading();
                    },
                    complete: function () {
                        stopLoading();
                    },
                    data: formData,
                    method: "POST",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        csrf.val(response.token);
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                                .then(() => {
                                    table.ajax.reload();
                                })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    }
                });
            }
        })
    }

    function resetForm() {
        $('#id').val(null);
        $('#year_month').val("<?= date('Y-m') ?>");
        $('#no_surat').val(null);
        $('#judul_form').val('REQUEST ACTION');
        $('#auto_generate').show();
    }

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        let year_month = $('#year_month').val();
        if (value && year_month) {
            $.ajax({
                url: `<?= base_url("form-request-action/get-no"); ?>`,
                method: "GET",
                data: {
                    year_month: year_month
                },
                dataType: "json",
                success: function (res) {
                    var data = res.data;
                    if (res.status == true) {
                        $("#nomor").val(data);
                        $("#nomor").attr("readonly", true);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $("#nomor").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $("#nomor").val("");
                    }
                }
            })
        } else {
            $("#auto_generate").prop("checked", false);
            $("#nomor").attr("readonly", false);
            $("#nomor").val("");
        }
    }

    const changeSort = function (val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }
</script>

<?= $this->endSection(); ?>