<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Kemasan</h1>
        <?php if (can('Master Barang', 'Kemasan', 'c')) : ?>
            <button class="btn btn-show-form btn-add float-right">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </button>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp row-form-select-master-barang-index">
                <div class="col-md-3 col mb-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Ketik Kode / Kategori / Kemasan" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('kemasan.kode')" class="sort">Kode</th>
                                <th onclick="changeSort('kemasan.name')" class="sort">Kemasan</th>
                                <th onclick="changeSort('parent_barang.parent_name')" class="sort">Kategori</th>
                                <th onclick="changeSort('satuans.kode_satuan')" class="sort">Satuan</th>
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
                                <div class="input-group input-group-password">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" id="kode" class="form-control kode" name="kode" placeholder="Kode Kemasan">
                                        <label for="floatingInput">Kode Kemasan</label>
                                    </div>
                                    <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                        <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 5px; margin-left: -30px;" id="generate_new_code" name="generate_new_code" type="checkbox" onchange="generateNewCode()">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control name" name="name" id="name" placeholder="Nama Kemasan">
                                <label for="floatingInput">Nama Kemasan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select parent_type_id" name="parent_type_id" id="parent_type_id">
                                    <option value=""></option>
                                    <?php foreach ($kelompokBarang as $kb) : ?>
                                        <option value="<?= ($kb['id']) ?>"><?= $kb['parent_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Kategori Kemasan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select satuan_id" name="satuan_id" id="satuan_id">
                                    <option value=""></option>
                                    <?php foreach ($satuan as $kb) : ?>
                                        <option value="<?= ($kb['id']) ?>"><?= $kb['kode_satuan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Kode Satuan</label>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Batal</button>
                <button type="submit" class="btn btn-submit-form">Simpan</button>
                <?php if (can('Master Barang', 'Kemasan', 'd')) : ?>
                    <button type="button" class="btn btn-discard delete-btn">Hapus</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

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
            url: "<?= base_url("kemasan/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.sort = sort;
                data.sortType = sortType;
            },
            beforeSend: function() {
                $.LoadingOverlay("show", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
            complete: function() {
                $.LoadingOverlay("hide", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
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
            },
            {
                data: "kode",
                className: "text-center",
            },
            {
                data: "name",
                className: "text-center",
            },
            {
                data: "parent_name",
                className: "text-center",
            },
            {
                data: "kode_satuan",
                className: "text-center",
            },

        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Master Data Kemasan Masih Kosong",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    $(".search").change(function() {
        table.ajax.reload();
    });

    $('.btn-add').click(function() {
        resetForm();
        $('.input-generate').show();
        $('#add_modal').modal('show');
        $('.delete-btn').hide();
        $('.title-name').text('Tambah Kemasan')
    });

    $('.btn-hide-form').click(function() {
        resetForm();
        $('.add-modal').modal('hide');
    });

    $("#parent_type_id,#satuan_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('#parent_type_id').select2({
        placeholder: "Pilih Kategori Kemasan",
        theme: "bootstrap-5",
        allowClear: false
    });


    $('#satuan_id').select2({
        placeholder: "Pilih Satuan",
        theme: "bootstrap-5",
        allowClear: false
    });

    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        resetForm();
        const data = table.row(this).data();
        let id = data.id;
        let formData = new FormData();
        formData.append("id", id);

        $.ajax({
            url: "<?= base_url("kemasan/get"); ?>",
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
            success: function(res) {
                csrf.val();
                $('.id').val(res.data.id);
                $('.kode').val(res.data.kode);
                $('.name').val(res.data.name);
                $('.parent_type_id').val(res.data.parent_type_id).change();
                $('.satuan_id').val(res.data.satuan_id).change();

                $('.input-generate').hide();
                $('.kode').attr('readonly', true);
                $('#add_modal').modal('show');
                $('.delete-btn').show();
                $('.title-name').text('Update Kemasan')
            }
        })
    });


    var validator = $(".create-form").validate({
        rules: {
            parent_type_id: {
                required: true
            },
            satuan_id: {
                required: true
            },
            kode: {
                required: true
            },
            name: {
                required: true
            },
        },
        messages: {
            satuan_id: {
                required: "Satuan wajib diisi"
            },
            parent_type_id: {
                required: "Kategori wajib diisi"
            },
            kode: {
                required: "Kode wajib diisi"
            },
            name: {
                required: "Nama kemasan wajib diisi"
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

    $('.btn-submit-form').click(function() {
        if ($('.create-form').valid()) {
            let id = $('.id').val();
            let data = new FormData(document.querySelector('.create-form'));
            if (id) {
                <?php if (can('Master Barang', 'Kemasan', 'u')) : ?>
                    Swal.fire({
                        icon: 'question',
                        title: 'Update Data ?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "<?= base_url("kemasan/update"); ?>",
                                data: data,
                                method: "POST",
                                dataType: "json",
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                    setLoading();
                                },
                                complete: function() {
                                    stopLoading();
                                },
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    if (response.status) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                            reverseButtons: true,
                                            confirmButtonText: 'Oke',
                                        }).then((result) => {
                                            table.ajax.reload();
                                            $('#add_modal').modal('hide');
                                            resetForm();
                                        })
                                    }
                                }
                            });
                        }
                    })
                <?php else : ?>
                    Swal.fire({
                        icon: 'error',
                        title: "Anda tidak punya hak akses update",
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        reverseButtons: true,
                        confirmButtonText: 'Oke',
                    })
                <?php endif; ?>
            } else {
                Swal.fire({
                    icon: 'question',
                    title: 'Simpan Data ?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "<?= base_url("kemasan/save"); ?>",
                            data: data,
                            method: "POST",
                            dataType: "json",
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                setLoading();
                            },
                            complete: function() {
                                stopLoading();
                            },
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                        reverseButtons: true,
                                        confirmButtonText: 'Oke',
                                    }).then((result) => {
                                        table.ajax.reload();
                                        $('#add_modal').modal('hide');
                                        resetForm();
                                    })
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                        cancelButtonColor: '#d33',
                                        reverseButtons: true,
                                        confirmButtonText: 'Oke',
                                    })
                                }
                            }
                        });
                    }
                })
            }

        }
    });

    $(".delete-btn").click(function() {
        var parentName = $('#parentName').val();

        Swal.fire({
            icon: 'question',
            title: 'Hapus Kemasan ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                let csrf = $(`[name="${csrfToken}"]`);
                let id = $('input[name="id"]').val();
                $.ajax({
                    url: "<?= base_url("kemasan/delete"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        setLoading();
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        csrf.val(response.token);

                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                            reverseButtons: true,
                            confirmButtonText: 'Oke',
                        }).then((result) => {
                            table.ajax.reload();
                            $('#add_modal').modal('hide');
                            resetForm();
                        })

                    },
                });
            }
        })
    });

    function resetForm() {
        $(".id").val(null);
        $(".parent_type_id").val(null).change();
        $('.kode_satuan').val(null).change();
        $('.satuan_id').val(null).change();
        $("#generate_new_code").attr('checked', false).change();
        $('.kode').attr('readonly', false);
        $(".kode").val(null);
        $(".name").val(null);

    }

    function generateNewCode() {
        let csrfToken = '<?= csrf_token() ?>';
        let value = document.getElementById('generate_new_code').checked ? true : false;
        let csrf = $(`[name="${csrfToken}"]`);
        if (value) {
            $("input[name='kode']").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("kemasan/generate-new-code"); ?>`,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                method: "POST",
                success: function(res) {
                    csrf.val(res.token);
                    $("input[name='kode']").attr("readonly", true);
                    $("input[name='kode']").val(res.codeNew);
                }
            })
        } else {
            $("input[name='kode']").attr("readonly", false);
            $("input[name='kode']").val("");
        }
    }

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