<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Golongan</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" id="golonganName" name="golonganName" placeholder="Nama Golongan">
                                <label for="floatingInput">Nama Golongan</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input name="nominalPinjaman" onkeyup="this.value = greatFormatRupiah(this.value);" autocomplete="one-time-code" type="text" id="nominalPinjaman" class="form-control target input-picker" placeholder="Nominal Pinjaman" value="">
                                <label for="nominalPinjaman" id="nominalGajiModal">Nominal Pinjaman</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Kembali</button>
                <button type="submit" class="btn btn-submit-form">Simpan</button>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="section-header">
        <h1>Golongan Karyawan</h1>
        <?php if (can('Personalia', 'Golongan', 'c')): ?>
            <button class="btn btn-show-form btn-add float-right" data-btn="create-modal">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </button>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end mb-3">
                <div class="col-md-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 10px;">No</th>
                                <th onclick="changeSort('golongan.golongan_name')" class="sort">Golongan</th>
                                <th onclick="changeSort('golongan.nominal_pinjaman')" class="sort">Nominal Pinjaman</th>
                                <th style="width: 100px;">Action</th>
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

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "golongan_name";
    let sortType = "desc";

    $('.btn-hide-form').click(function() {
        $(".add-modal").modal("hide")
    });
    $('.btn-add').click(function() {
        $('.add-modal').modal('show');
        $('.title-name').text('Tambah ');
        $('.delete-btn').hide();
        $(".create-form")[0].reset();
    });

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
            url: "<?= base_url("golongan/all"); ?>",
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
                width: "3%"
            }, {
                data: "golonganName",
                className: "text-left",
            },
            {
                data: "nominalPinjaman",
                className: "text-left",
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    let res = '';

                    res += `
                    <?php if (can('Personalia', 'Golongan', 'u')): ?>
                        <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                    <?php endif ?>
                    <?php if (can('Personalia', 'Golongan', 'd')): ?>
                        <button data-toggle="tooltip" title="Hapus" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                        </button>
                    <?php endif ?>
                    `;

                    return res;
                }
            }
        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        },
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });


    var validator = $(".create-form").validate({
        rules: {
            golonganName: {
                required: true
            },
            nominalPinjaman: {
                required: true
            }
        },
        messages: {
            golonganName: {
                required: "Golongan wajib diisi"
            },
            nominalPinjaman: {
                required: "Nominal pinjaman wajib diisi"
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
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    let data = new FormData(document.querySelector(".create-form"));
                    let id = $(".id").val();
                    let url = id == '' ? '<?= base_url('golongan/create') ?>' : '<?= base_url('golongan/update') ?>';

                    var nominalPinjaman = destroyFormatRupiah($('#nominalPinjaman').val());
                    data.set('nominalPinjaman', nominalPinjaman);

                    $.ajax({
                        url: url,
                        data: data,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            setLoading()
                        },
                        complete: function() {
                            stopLoading();
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
    });

    function edit(id) {
        $(".create-form")[0].reset()
        $(".delete-btn").show();
        $(".title-name").text("Update");

        validator.resetForm();
        validator.reset();

        $.ajax({
            url: "<?= base_url("golongan/id"); ?>" + "/" + id,
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            method: "GET",
            dataType: "json",
            success: function(res) {
                if (res.status) {
                    $(".id").val(id);
                    $("#golonganName").val(res.data.golongan_name);
                    $("#nominalPinjaman").val(greatFormatRupiah(res.data.nominal_pinjaman));

                    $(".add-modal").modal("show")
                }
            }
        })
    }

    function remove(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("golongan/delete"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
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
                        }
                    },

                });
            }
        })
    }

    $(".search").keyup(function() {
        table.ajax.reload();
    });

    function changeSort(val) {
        if (sort !== val) {
            sortType = "ASC";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }
</script>

<?= $this->endSection(); ?>