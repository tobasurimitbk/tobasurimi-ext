<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Bahan Scrap</h1>
        <button class="btn btn-show-form btn-add float-right">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp row-form-select-master-barang-index">
                <div class="col-md-3 col mb-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Ketik Nama Barang / Kode Barang" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th onclick="changeSort('barang_master.kode_barang')" class="sort">Kode Barang</th>
                                <th onclick="changeSort('barang_master.barang_name')" class="sort">Nama Barang</th>
                                <th onclick="changeSort('satuans.nama_satuan')" class="sort">Satuan</th>
                                <th onclick="changeSort('barang_master.stok')" class="sort">Stok</th>
                                <th class="sort" style="text-align: center;">Histori</th>
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
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="type" value="<?= $type ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <div class="input-group input-group-password">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control" name="kode_barang" placeholder="Kode Barang">
                                        <label for="floatingInput">Kode Barang</label>
                                    </div>
                                    <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                        <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" id="generate_new_code" name="generate_new_code" type="checkbox" onchange="generateNewCode()">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" name="barang_name" id="barang_name">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select" name="satuan_id" id="satuan_id">
                                    <option value=""></option>
                                    <?php foreach ($satuanBarang as $sb) : ?>
                                        <option value="<?= $sb['id'] ?>"><?= $sb['kode_satuan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Satuan Barang</label>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Batal</button>
                <button type="submit" class="btn btn-submit-form">Simpan</button>
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
                url: "<?= base_url("barang-master/all"); ?>",
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
                },
                {
                    data: "kode_barang",
                    className: "text-center",
                },
                {
                    data: "barang_name",
                    className: "text-center",
                },
                {
                    data: "satuan",
                    className: "text-center",
                },
                {
                    data: "stok",
                    className: "text-center",
                    render: function(data, type, row) {
                        return `
                            <div class="text-danger">
                                ${data}
                            </div>
                        `
                    }
                },
                {
                    data: "id",
                    className: "text-center",
                    searchable: false,
                    sortable: false,
                    render: function(data, type, row) {
                        return `
                        <div class="mt-0">
                            <button onclick="alert('Belum Tersedia')" class="btn btn-success posting-spp">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            </button>
                        </div>
                    `
                    }
                }

            ],
            columnDefs: [{
                defaultContent: "-",
                targets: "_all"
            }],
            language: {
                emptyTable: "Master Data Bahan Scrap Masih Kosong",
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

        $('.btn-add').click(function() {
            $('.title-name').text("Tambah Bahan Scrap");
            $(".create-form :input:not([name='type'])").val('');
            $('input[name="parent_type_id"]').val(null).change();
            $('input[name="satuan_id"]').val(null).change();

            $('.delete-btn').hide();
            $('input[name="kode_barang"]').attr('readonly', false);
            $('#generate_new_code').prop('checked', false).show();
            $('.add-modal').modal('show');
        });

        $('.btn-discard').click(function() {
            $('.add-modal').modal('hide');
        });

        var validator = $(".create-form").validate({
            rules: {
                barang_name: {
                    required: true
                },
                kode_barang: {
                    required: true
                },
                satuan_id: {
                    required: true
                }
            },
            messages: {
                barang_name: {
                    required: "Nama Barang Wajib Diisi"
                },
                kode_barang: {
                    required: "Kode Barang Wajib Diisi"
                },
                satuan_id: {
                    required: "Satuan Barang Wajib Diisi"
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

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            let data = table.row(this).data();
            let id = data.id;
            let csrf = $(`[name="${csrfToken}"]`);
            $(".create-form :input:not([name='type'])").val('');
            $.ajax({
                url: "<?= base_url('barang-master/get') ?>",
                data: {
                    id: id
                },
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                method: "POST",
                dataType: "json",
                success: function(res) {
                    $('.delete-btn').show();
                    $('.title-name').text("Update Bahan Scrap");
                    $('input[name="kode_barang"]').attr('readonly', true);
                    $('#generate_new_code').hide();
                    $('input[name="kode_barang"]').val(res.data.kode_barang);
                    $('select[name="parent_type_id"]').val(res.data.parent_type_id).change();
                    $('select[name="satuan_id"]').val(res.data.satuan_id).change();
                    $('input[name="barang_name"]').val(res.data.barang_name);
                    $('input[name="id"]').val(res.data.id);

                    $('.add-modal').modal('show');
                }
            })
        })
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
                                url: "<?= base_url("barang-master/update"); ?>",
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
                                                $(".add-modal").modal("hide");
                                            })
                                    }
                                },
                                onError: function(response) {
                                    csrf.val(response.token);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    })
                                }
                            });
                        } else {
                            $.ajax({
                                url: "<?= base_url("barang-master/save"); ?>",
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
                                                $(".add-modal").modal("hide");
                                            })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Data Gagal Disimpan, coba Lagi',
                                            confirmButtonColor: '#4e73df',
                                        }).then(() => {
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
                                    })
                                }
                            });
                        }
                    }
                })

            }
        });
        $(".delete-btn").click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Hapus Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    let id = $("#id").val();
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("barang-master/delete"); ?>",
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
                            }
                        },
                        onError: function(response) {
                            csrf.val(response.token);
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Gagal Disimpan, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    });
                }
            })
        });
    });

    function generateNewCode() {
        let csrfToken = '<?= csrf_token() ?>';
        let value = document.getElementById('generate_new_code').checked ? true : false;
        let csrf = $(`[name="${csrfToken}"]`);
        if (value) {
            $("input[name='kode_barang']").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("barang-master/generate-new-code"); ?>`,
                data: {
                    type: "<?= $type ?>"
                },
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                method: "POST",
                success: function(res) {
                    csrf.val(res.token);
                    $("input[name='kode_barang']").attr("readonly", true);
                    $("input[name='kode_barang']").val(res.codeNew);
                }
            })
        } else {
            $("input[name='kode_barang']").attr("readonly", false);
            $("input[name='kode_barang']").val("");
        }
    }
</script>
<script>
    $("select[name='parent_type_id']")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $("select[name='parent_type_id']")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $("select[name='parent_type_id']")
        .parent('div')
        .find('label')
        .css('z-index', '1');
    $("select[name='parent_type_id']").select2({
        placeholder: "Pilih Kelompok Barang",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $(".add-modal .modal-content")
    });

    $("select[name='satuan_id']")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $("select[name='satuan_id']")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $("select[name='satuan_id']")
        .parent('div')
        .find('label')
        .css('z-index', '1');
    $("select[name='satuan_id']").select2({
        placeholder: "Pilih Satuan Barang",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $(".add-modal .modal-content")
    });
</script>

<?= $this->endSection(); ?>