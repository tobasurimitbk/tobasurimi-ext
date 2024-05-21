<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Pengusaha TPB</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data" onSubmit="return false">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input id="npwp" name="npwp" type="number" class="form-control npwp" placeholder="">
                                <label>NPWP Pengusaha TPB</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input id="nama_pengusaha" name="nama_pengusaha" type="text" class="form-control nama_pengusaha" placeholder="">
                                <label>Nama Pengusaha TPB</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <textarea name="alamat" id="alamat" class="form-control alamat" placeholder="Alamat TPB"></textarea>
                                <label>Alamat Pengusaha TPB</label>
                            </div>
                        </div>
                    </div>

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

<section class="section">
    <div class="section-header">
        <h1>Pengusaha TPB</h1>
        <button class="btn btn-show-form btn-add float-right">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end mb-3">
                <div class="col-md-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari Data Pengusaha TPB" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('npwp')" class="sort">NPWP</th>
                                <th onclick="changeSort('nama_pengusaha')" class="sort">Nama Pengusaha</th>
                                <th onclick="changeSort('alamat')" class="sort">Alamat</th>
                                <th class="sort" style="width: 10px;">Action</th>
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

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    let sort = "id";
    let sortType = "desc";

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
            url: "<?= base_url("setting-akun-bc/pengusaha-tpb/all"); ?>",
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
                data: "npwp",
                className: "text-center"
            }, {
                data: "nama_pengusaha",
                className: "text-center"
            }, {
                data: "alamat",
                className: "text-center"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    return `
                        <div class="mt-0">
                            <a data-toggle="tooltip" title="NO TPB" href="<?= base_url("setting-akun-bc/no-ijin-tpb/"); ?>${id}" class="btn btn-primary posting-spp">
                            <i class="fas fa-cog"></i>
                            </a>
                        </div>
                    `

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

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    $('.search').keyup(function() {
        table.ajax.reload();
    });

    var validator = $(".create-form").validate({
        rules: {
            npwp: {
                required: true
            },
            nama_pengusaha: {
                required: true
            },
            alamat: {
                required: true
            },
        },
        messages: {
            npwp: {
                required: "Masukkan Nomor NPWP"
            },
            nama_pengusaha: {
                required: "Masukkan Nama Pengusaha TPB"
            },
            alamat: {
                required: "Masukkan Alamat Pengusaha TPB"
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

    $('.btn-add').click(function() {
        clearForm();
        validator.reset();
        $('.add-modal').modal('show');
        $('.title-name').text('Tambah ');
        $('.delete-btn').hide();

    });

    $('.btn-hide-form').click(function() {
        $('.add-modal').modal('hide');
        $('#id').val('');
        clearForm();
    });

    $('.btn-submit-form').click(function() {
        if ($('.create-form').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Data ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector(".create-form"));
                    var id = $('#id').val();
                    if (id) {
                        // EDIT
                        $.ajax({
                            url: "<?= base_url("setting-akun-bc/pengusaha-tpb/update"); ?>",
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
                                $('.add-modal').modal('hide');
                                if (response.status) {
                                    clearForm();
                                    $('#id').val(null);
                                    Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                        confirmButtonText: 'Ok'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            table.ajax.reload();
                                        }
                                    });
                                }
                            },
                        });
                    } else {
                        // CREATE
                        $.ajax({
                            url: "<?= base_url("setting-akun-bc/pengusaha-tpb/create"); ?>",
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
                                    clearForm();
                                    Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                        confirmButtonText: 'Ok'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            table.ajax.reload();
                                            $('.add-modal').modal('hide');
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                        confirmButtonText: 'Ok'
                                    }).then((result) => {

                                    });
                                }
                            },
                        });
                    }
                }
            })
        }
    });

    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        $(".title-name").text("Update ");
        $('.delete-btn').show();
        validator.reset();
        let id = data.id;

        $.ajax({
            url: "<?= base_url("setting-akun-bc/pengusaha-tpb/get"); ?>",
            method: "GET",
            data: {
                id: id,
            },
            beforeSend: function(xhr) {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            dataType: "json",
            success: function(res) {
                if (res.status) {
                    csrf.val(res.token);
                    $('#id').val(res.data.id);
                    $('#npwp').val(res.data.npwp);
                    $('#nama_pengusaha').val(res.data.nama_pengusaha);
                    $('#alamat').val(res.data.alamat);
                    $('.add-modal').modal('show');
                }
            }
        })
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
                let id = $(".id").val();
                $.ajax({
                    url: "<?= base_url("setting-akun-bc/pengusaha-tpb/delete"); ?>",
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
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            table.ajax.reload();
                        });
                        $('.add-modal').modal('hide');

                    },
                });
            }
        })
    });

    const noIjinTPB = function(url) {
        window.open(url);
    }

    function clearForm() {
        $('#id').val('');
        $('#npwp').val('');
        $('#nama_pengusaha').val('');
        $('#alamat').val('');
    }

    function preventNegativeInput(inputElement) {
        var inputValue = inputElement.value;
        var numericValue = inputValue.replace(/[^0-9.]/g, '');
        numericValue = numericValue.replace(/^0+/g, '');
        numericValue = numericValue.replace(/^\./g, '0.');
        if (parseFloat(numericValue) < 0 || isNaN(parseFloat(numericValue))) {
            inputElement.value = '0';
        } else {
            inputElement.value = numericValue;
        }
    }
</script>

<?= $this->endSection(); ?>