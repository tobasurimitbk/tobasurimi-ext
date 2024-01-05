<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Kelompok Barang</h1>
        <button class="btn btn-show-form btn-add float-right">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </button>
    </div>
    <div class="card">
        <div class="card-body">

            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link <?= $type == "" || $type == "bahan_baku" ? "active" : "" ?> " href="<?= base_url('parent-barang?type=bahan_baku') ?>">Bahan Baku</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $type == "bahan_penolong" ? "active" : "" ?>" href="<?= base_url('parent-barang?type=bahan_penolong') ?>">Bahan Penolong</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $type == "bahan_jadi" ? "active" : "" ?>" href="<?= base_url('parent-barang?type=bahan_jadi') ?>">Barang Jadi</a>
                </li>
            </ul>
            <div class="row justify-content-end mt-4">
                <div class="col-md-2">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari Kelompok Barang" value="" />
                </div>
            </div>
            <div class="row mt-3">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th onclick="changeSort('parent_name')" class="sort">Kelompok Barang</th>
                                <?php if ($type == "bahan_penolong") : ?>
                                    <th onclick="" class="sort">Kategori</th>
                                <?php endif; ?>
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
                    <input type="hidden" name="type" id="type" value="<?= $type ?>">
                    <?= csrf_field() ?>
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" type="text" class="form-control" placeholder="Nama Kelompok" id="parentName" name="parentName">
                        <label for="floatingInput">Nama Kelompok</label>
                    </div>
                    <?php if ($type == "bahan_penolong") : ?>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select" id="kategori" name="kategori">
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($kategoriBP as $k) : ?>
                                    <option value="<?= encrypt($k['value']) ?>">
                                        <?= $k['value'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pilih Kategori (Opsional)</label>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Batal</button>
                <button type="button" class="btn btn-submit-form">Simpan</button>
                <button type="button" class="btn btn-discard delete-btn">Hapus</button>
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
                url: "<?= base_url("parent-barang/all"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.search = $(".search").val();
                    data.sort = sort;
                    data.sortType = sortType;
                    data.parent_type = "<?= $type ?>";
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
                },
                <?php if ($type == "bahan_penolong") : ?> {
                        data: "kategori",
                        className: "text-center"
                    },
                <?php endif; ?>
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

        $(".search").keyup(function() {
            table.ajax.reload();
        });
        // create modal show
        $('.btn-show-form').click(function() {
            var type = $("input[name='type']").val();
            $('.title-name').text("Tambah Kategori " + repairStr(type));
            $('.delete-btn').hide();
            $('.add-modal').modal('show');

        });
        // hide modal
        $('.btn-discard').click(function() {
            $('#id').val('');
            $('.add-modal').modal('hide');
        });
        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            let csrf = $(`[name="${csrfToken}"]`);
            let id = data.id;
            let type = $("input[name='type']").val();
            let formData = new FormData();
            $('#parentName').val(null);
            $('#kategori').val('').trigger("change");
            formData.append("id", id);

            $('.title-name').text("Update Kategori " + repairStr(type));
            $('.delete-btn').show();
            $.ajax({
                url: "<?= base_url("parent-barang/get"); ?>",
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
                        $("#id").val(id);
                        $("#parentName").val(res?.data?.parent_name);
                        $("#kategori").val(res?.data?.kategori);
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
                title: 'Hapus Kelompok Barang ' + parentName + '?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    let csrf = $(`[name="${csrfToken}"]`);
                    let id = $("#id").val();

                    $.ajax({
                        url: "<?= base_url("parent-barang/delete"); ?>",
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
                                        table.ajax.reload();
                                        $(".add-modal").modal("hide");
                                        $('#parentName').val(null);
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
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        let id = $('input[name="id"]').val();
                        let csrf = $(`[name="${csrfToken}"]`);
                        let data = new FormData(document.querySelector(".create-form"));

                        if (id) {
                            $.ajax({
                                url: "<?= base_url("parent-barang/update"); ?>",
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
                                    $('#id').val('');
                                    if (response.status) {
                                        Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                table.ajax.reload();
                                                $('#parentName').val(null);
                                                $('#kategori').val('').trigger("change");
                                                $(".add-modal").modal("hide");
                                            })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        }).then(() => {
                                            $('#parentName').val(null);
                                            $('#kategori').val('').trigger("change");
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
                                url: "<?= base_url("parent-barang/save"); ?>",
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
                                                $('#kategori').val('').trigger("change");
                                                $(".add-modal").modal("hide")
                                            })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        }).then(() => {
                                            $('#parentName').val(null);
                                            $('#kategori').val('').trigger("change");
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

        // repair string $type
        // bahan_penolong -> Bahan Penolong
        function repairStr(text) {
            var words = text.split("_");
            var capitalizedWords = words.map(function(word) {
                return word.charAt(0).toUpperCase() + word.slice(1);
            });
            var result = capitalizedWords.join(" ");
            return result;
        }
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
</script>

<?= $this->endSection(); ?>