<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" id="add_modal">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Supplier Internasional</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control kode" id="kode" name="kode" value="<?= !empty($dataSPP) ? $dataSPP->spp_no : ""; ?>">
                                    <label for="floatingInput">Kode Supplier</label>
                                </div>
                                <div style="<?= !empty($biayaKepiting) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control name" id="name" name="name" placeholder="Nama">
                                <label for="floatingInput">Nama Supplier</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <textarea autocomplete="one-time-code" class="form-control address text-area-all" name="address" id="address" placeholder="Alamat (Opsional)"></textarea>
                                <label for="floatingInput">Alamat (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control fax" id="fax" name="fax" placeholder="Email Address (Opsional)">
                                <label for="floatingInput">Email Address (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <textarea name="phone" id="phone" class="form-control phone"></textarea>
                                <label for="floatingInput">No. Telepon (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control contact_person" id="contact_person" name="contact_person" placeholder="Contact Person (Opsional)">
                                <label for="floatingInput">Contact Person (Opsional)</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Kembali</button>
                <button type="submit" class="btn btn-submit-form btn-submit-parent">Simpan</button>
                <button type="button" class="btn btn-discard delete-form delete-btn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Supplier Internasional</h1>
        <?php if (can('Supplier', 'Internasional', 'p')): ?>
            <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false" style="margin-right: 20px;">
                Import / Export
            </button>
            <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                <li><button class="dropdown-item btn-upload-excel">Import Excel</button></li>
                <li><button class="dropdown-item" onclick="exportExcel('INTERNASIONAL')">Export Excel</button></li>
            </ul>
        <?php endif; ?>
        <?php if (can('Supplier', 'Internasional', 'c')): ?>
            <button class="btn btn-show-form btn-add float-right" data-btn="create-modal">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </button>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end mb-3">
                <div class="col-md-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari Kode / Nama" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th onclick="changeSort('kode')" class="sort">Kode</th>
                                <th onclick="changeSort('name')" class="sort">Nama</th>
                                <th onclick="changeSort('fax')" class="sort">Email Address</th>
                                <th onclick="changeSort('address')" class="sort">Alamat</th>
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
<div class="modal" id="import_excel_modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Supplier</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-secondary text-black" role="alert">
                    UNDUH TEMPLEATE EXCEL <a href="<?= base_url('assets/import/IMPORT_EXCEL_SUPPLIER.xlsx') ?>" style="text-decoration: none;"><b style="color: black;">DISINI</b></a>
                </div>
                <form class="form-excel" method="post">
                    <input type="hidden" name="type" value="INTERNASIONAL">
                    <div class="form-floating" style="height: 50px;">
                        <input type="file" name="file" id="file" accept=".xlsx" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-import-excel mr-2">Kembali</button>
                <button type="submit" class="btn btn-submit-form btn-submit-excel">Simpan</button>
            </div>
        </div>
    </div>
</div>
<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "kode";
    let sortType = "desc";
    let trigger = true;

    // Init changeStatus
    changeStatus();

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
            url: "<?= base_url("supplier-internasional/all"); ?>",
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
            sortable: false
        }, {
            data: "kode",
            className: "text-center"
        }, {
            data: "name",
            className: "text-center"
        }, {
            data: "fax",
            className: "text-center"
        }, {
            data: "address",
            className: "text-center"
        }],
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
                kode: {
                    required: true
                },
                name: {
                    required: true
                }
            },
            messages: {
                kode: {
                    required: "Kode wajib diisi"
                },
                name: {
                    required: "Nama wajib diisi"
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

        $(".fax").mask("0000000000")

        // $(".phone").mask("0000000000000")

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $(".dataTable_info").addClass("pt-0");

        $(".btn-show-form").click(function() {
            $(".id").val("");
            $(".title-name").text("Tambah");

            validator.resetForm();
            validator.reset();

            $(".create-form")[0].reset()
            $(".delete-form").css('display', 'none');

            $.ajax({
                url: "<?= base_url("supplier/generate/I"); ?>",
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".kode").val(res.data)
                        $(".add-modal").modal("show");
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            })
        })

        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            $(".create-form")[0].reset()
            $(".delete-form").css('display', '');
            let id = data.id;
            $(".title-name").text("Update");

            $.ajax({
                url: "<?= base_url("supplier/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        validator.resetForm();
                        validator.reset();

                        $(".id").val(id);
                        $(".kode").val(res.data.kode);
                        $(".name").val(res.data.name);
                        $(".address").val(res.data.address);
                        $(".fax").val(res.data.fax);
                        $(".phone").val(res.data.phone);
                        $(".contact_person").val(res.data.contact_person);
                        $('#auto_generate').css('display', 'none');
                        $("#kode").prop("readonly", false);
                        $('.modal').on('hidden.bs.modal', function() {
                            $('#auto_generate').css('display', '');

                        });

                        $(".add-modal").modal("show");

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            })
        })

        // delete
        $(".delete-form").click(function() {
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
                    let id = $(".id").val();
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("supplier/delete"); ?>",
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

        $(".btn-submit-parent").click(function() {
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

                        $.ajax({
                            url: id ? "<?= base_url("supplier-internasional/update"); ?>" : "<?= base_url("supplier-internasional/save"); ?>",
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
                                            $(".add-modal").modal("hide")
                                            table.ajax.reload()
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

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $.ajax({
                url: `<?= base_url("/supplier-internasional/generate-kode-supplier-internasional"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res) {
                        $("#kode").val(res);
                        $("#kode").attr("readonly", true);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $("#kode").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $("#kode").val("");
                    }
                }
            })
        } else {
            $("#kode").attr("readonly", false);
            $("#kode").val("");
        }
    }

    $('.btn-upload-excel').click(function() {
        $('#file').val(null);
        $('#import_excel_modal').modal('show');

    });

    $('.btn-discard-import-excel').click(function() {
        $('#import_excel_modal').modal('hide');
    });

    $('.btn-submit-excel').click(function() {
        if ($('.form-excel').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Import Excel?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    let csrf = $(`[name="${csrfToken}"]`);
                    let formData = new FormData(document.querySelector(".form-excel"));
                    $.ajax({
                        url: "<?= base_url("supplier/import-excel"); ?>",
                        data: formData,
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
                                }).then(() => {
                                    table.ajax.reload();
                                    $('#import_excel_modal').modal('hide');
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                });
                            }
                        },
                    });

                }
            })
        }
    });

    const exportExcel = function(type) {
        var url = "<?= base_url('supplier/export-excel') ?>";
        window.open(url + `?type=${type}&sort=${sort}&sortType=${sortType}&`, "_blank");
    }
</script>


<?= $this->endSection(); ?>