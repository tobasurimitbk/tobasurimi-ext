<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Alias Barang</h1>
    </div>
    <div class="card">
        <?= csrf_field() ?>
        <div class="card-body">
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link <?= @$_GET['type'] == 'barang_sales' || @$_GET['type'] == '' ? 'active' : '' ?>" href="<?= base_url('alias-barang?type=barang_sales') ?>">Barang Sales & Inventori Barang Jadi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= @$_GET['type'] == 'barang_inventori' ? 'active' : '' ?>" href="<?= base_url('alias-barang?type=barang_inventori') ?>">Barang Inventori Impor</a>
                </li>
            </ul>
            <div class="row mb-4 justify-content-end">
                <div class="col-sm-3 mt-2">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search" id="search" name="search" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Data </label>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 10px;">No</th>
                            <th onclick="changeSort('type_barang_sales')">Tipe Barang</th>
                            <th onclick="changeSort('type_barang_sales')">Barang Impor</th>
                            <th onclick="changeSort('barang_sales_name')">Nama Alias</th>
                            <th style="width: 70px;">Action</th>
                        </tr>
                    </thead>
                    <tbody class="body-table" id="body-table">
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</section>
<div class="modal updateAliasModal" id="updateAliasModal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Alias Barang</h5>
            </div>
            <div class="modal-body">
                <form class="update-alias-form" id="update-alias-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="id" class="id">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control barang_name_alias" id="barang_name_alias" name="barang_name_alias" placeholder="Nama Barang Master Alias">
                                <label for="floatingInput">Nama Barang Master Alias</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control spesifikasi_alias" id="spesifikasi_alias" name="spesifikasi_alias" placeholder="Nama Spesifikasi Alias">
                                <label for="floatingInput">Nama Spesifikasi Alias</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2 btn-discard-alias" id="btn-discard-alias">Kembali</button>
                <button type="submit" class="btn btn-submit-form btn-submit-alias">Update Data</button>
            </div>
        </div>
    </div>
</div>


<script>
    let sort = "barang_sales_name";
    let sortType = "asc";

    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

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
            url: "<?= base_url("alias-barang/all-impor"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.sort = sort;
                data.sortType = sortType;
            },
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
                className: "text-left",
                orderable: false
            },
            {
                data: "type_barang",
                className: "text-left"
            },
            {
                data: "barang_impor",
                className: "text-left"
            },
            {
                data: "barang_alias",
                className: "text-left"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    let barang_name_alias = row.barang_name_alias;
                    let spesifikasi_alias = row.spesifikasi_alias;

                    return `
                        <div class="mt-0 actions">
                            <?php if (can('Inventori', 'Alias Barang', 'u')): ?>
                                <a href="javascript:void(0)" onclick="edit('${id}','${barang_name_alias}', '${spesifikasi_alias}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    `
                }
            }
        ],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
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

    $('.search').change(function(e) {
        table.ajax.reload();
    });

    var validator = $("#update-alias-form").validate({
        rules: {
            barang_name_alias: {
                required: true
            },
            spesifikasi_alias: {
                required: true
            },
        },
        messages: {
            barang_name_alias: {
                required: "ketik master barang alias"
            },
            spesifikasi_alias: {
                required: "ketik spesifikasi alias"
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


    $('.btn-submit-alias').click(function(e) {
        e.preventDefault();
        if ($('#update-alias-form').valid()) {
            var id = $('#id').val();
            var barangNameAlias = $('#barang_name_alias').val();
            var spesifikasiAlias = $('#spesifikasi_alias').val();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('barang_name_alias', barangNameAlias);
            formData.append('spesifikasi_alias', spesifikasiAlias);

            $.ajax({
                url: "<?= base_url("alias-barang/update-impor"); ?>",
                data: formData,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                processData: false,
                contentType: false,
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
                                table.ajax.reload();
                                $('#updateAliasModal').modal('hide');
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
    });

    $('.btn-discard-alias').click(function(e) {
        e.preventDefault();
        $('#updateAliasModal').modal('hide');

    });

    function edit(id, barang_name_alias, spesifikasi_alias) {
        $('#id').val(id);
        $('#barang_name_alias').val(barang_name_alias).change();
        $('#spesifikasi_alias').val(spesifikasi_alias).change();
        $('#updateAliasModal').modal('show');
    }

    function changeSort(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }
</script>

<?= $this->endSection(); ?>