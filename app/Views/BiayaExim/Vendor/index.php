<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>


<div class="modal" id="modalAddVendor" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label id="title-name"></label> Vendor / Pelayaran</h5>
            </div>
            <div class="modal-body">
                <form class="create-form">
                    <input type="hidden" name="id" class="id" id="id">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control nama_vendor" id="nama_vendor" name="nama_vendor" placeholder="Nama Vendor">
                                <label for="floatingInput">Nama Vendor</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 90px;">
                                <textarea autocomplete="one-time-code" class="form-control full-textarea alamat" id="alamat" name="alamat"></textarea>
                                <label for="floatingInput">Alamat (Opsional)</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2" id="btnHideModal">Kembali</button>
                <button type="submit" class="btn btn-submit-form btnSubmit" id="btnSubmit">Simpan</button>
            </div>
        </div>
    </div>
</div>


<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Vendor / Pelayaran</h1>
        <?php if (can('Biaya Exim', 'Vendor', 'c')) : ?>
            <button class="btn btn-show-form btn-add float-right btnShowModal" id="btnShowModal" data-btn="create-modal">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </button>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row  justify-content-end ">
                <div class="col-sm-3 mb-3" style="float: right;">
                    <input autocomplete="one-time-code" class="form-control search form-out-search mr-3 search" placeholder="Search Data" value="" />
                </div>
            </div>
            <div class="table-responsive">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTableVendor" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th onclick="changeSort('id')" style="width: 10px;">No</th>
                                <th onclick="changeSort('nama_vendor')" class="sort">Vendor / Pelayaran</th>
                                <th onclick="changeSort('alamat')" class="sort">Alamat</th>
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
    let sort = "nama_vendor";
    let sortType = "asc";

    const dataTableVendor = $('#dataTableVendor').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("vendor-pelayaran/all"); ?>",
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
            width: "5%"
        }, {
            data: "nama_vendor",
            className: "text-left"
        }, {
            data: "alamat",
            className: "text-left"
        }, {
            data: "id",
            className: "text-center actions sticky-col",
            searchable: false,
            sortable: false,
            render: function(data, type, row) {
                var id = row.id;
                return `
                    <div class="mt-0">
                        <?php if (can('Biaya Exim', 'Vendor', 'u')) : ?>
                            <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (can('Biaya Exim', 'Vendor', 'd')) : ?>
                            <button data-toggle="tooltip" title="Hapus" onclick="destroy('${id}')" class="btn btn-danger delete-parent">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                `
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

    var validator = $(".create-form").validate({
        rules: {
            nama_vendor: {
                required: true
            },
        },
        messages: {
            nama_vendor: {
                required: "Nama Vendor Wajib Diisi"
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


    $(".search").keyup(function() {
        dataTableVendor.ajax.reload();
    });

    $(".dataTable_info").addClass("pt-0");


    $('#btnShowModal').click(function() {
        $('#modalAddVendor').modal('show');
        $('#title-name').text("Tambah");
        resetForm();
    });

    $('#btnHideModal').click(function() {
        $('#modalAddVendor').modal('hide');
    });

    $("#btnSubmit").click(function(e) {
        e.preventDefault();
        var id = $('#id').val();
        if ($(".create-form").valid()) {
            Swal.fire({
                icon: 'question',
                title: id == '' ? "Simpan Data ?" : "Update Data ?",
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    var data = new FormData(document.querySelector(".create-form"));
                    var id = $("#id").val();
                    var url = id == '' ? "<?= base_url('vendor-pelayaran/create') ?>" : "<?= base_url('vendor-pelayaran/update') ?>"

                    $.ajax({
                        url: url,
                        data: data,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            setLoading();
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
                                        $("#modalAddVendor").modal("hide")
                                        dataTableVendor.ajax.reload()
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
    })

    function resetForm() {
        $('#id').val(null);
        $('#nama_vendor').val(null);
        $('#alamat').val(null);
    }

    function destroy(id) {
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
                    url: "<?= base_url("vendor-pelayaran/delete"); ?>",
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
                                    dataTableVendor.ajax.reload()
                                    $(".add-modal-internasional").modal("hide")
                                })
                        }
                    },
                });
            }
        })
    }

    function edit(id) {
        $.ajax({
            url: "<?= base_url("vendor-pelayaran/get"); ?>",
            method: "GET",
            dataType: "json",
            data: {
                id: id,
            },
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            success: function(res) {
                if (res.status) {
                    $('#title-name').text("Update");
                    $("#id").val(id);
                    $("#nama_vendor").val(res.data.nama_vendor);
                    $("#alamat").val(res.data.alamat);
                    validator.resetForm();
                    validator.reset();
                    $('#modalAddVendor').modal('show');
                }
            }
        });
    }

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }
</script>


<?= $this->endSection(); ?>