<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Nomor Ijin TPB</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("setting-akun-bc/pengusaha-tpb"); ?>">
                Kembali
            </a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <?= csrf_field() ?>
            <table width="100%" class="mb-3">
                <tbody>
                    <tr style="color: black;">
                        <td width="150px">NPWP</td>
                        <td width="5px">:</td>
                        <td><?= empty($pengusahaTPB) ? "" : strtoupper($pengusahaTPB['npwp']); ?></td>
                    </tr>
                    <tr style="color: black; height: 20px;">
                        <td colspan="3"></td>
                    </tr>
                    <tr style="color: black;">
                        <td width="150px">Pengusaha TPB</td>
                        <td width="5px">:</td>
                        <td><?= empty($pengusahaTPB) ? "" : strtoupper($pengusahaTPB['nama_pengusaha']); ?></td>
                    </tr>
                    <tr style="color: black; height: 20px;">
                        <td colspan="3"></td>
                    </tr>
                    <tr style="color: black;">
                        <td width="150px">Alamat</td>
                        <td width="25px">:</td>
                        <td><?= empty($pengusahaTPB) ? "" : $pengusahaTPB['alamat']; ?></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <form class="create-form">
                <input type="hidden" name="id" id="id" class="id">
                <input type="hidden" value="<?= !empty($pengusahaTPB) ? $pengusahaTPB['id'] : '' ?>" name="pengusaha_tpb_id" class="pengusaha_tpb_id" id="pengusaha_tpb_id">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input id="no_ijin_tpb" name="no_ijin_tpb" type="text" class="form-control no_ijin_tpb" placeholder="">
                            <label>Nomor Izin TPB</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" name="tanggal_skep_tpb" type="text" placeholder="" class="form-control tanggal_skep_tpb" id="tanggal_skep_tpb">
                                <label>Tanggal Skep TPB</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 17px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating" style="height: 50px;">
                            <select class="form-select status" name="status" id="status">
                                <option value=""></option>
                                <option value="1">AKTIF</option>
                                <option value="0">TIDAK AKTIF</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tutup No Surat Jalan</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <textarea rows="5" name="alamat_pemilik_barang" id="alamat_pemilik_barang" class="form-control alamat_pemilik_barang" placeholder="Alamat Pemilik Barang"></textarea>
                            <label>Alamat Pemilik Barang</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-add btn-block float-right btn-submit-form">
                            <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                        </button>
                        <button style="border-color: #e7323a !important; background-color: #e7323a !important; margin-right: 10px !important;" class="btn btn-add btn-block float-right" onclick="resetForm()">
                            <i class="fa-solid fa-rotate-right mr-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
            <div class="row justify-content-end mb-3">
                <div class="col-md-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari No Izin TPB" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('no_ijin_tpb')" class="sort">Nomor Ijin TPB</th>
                                <th onclick="changeSort('tanggal_skep_tpb')" class="sort">Tanggal Skep TPB</th>
                                <th onclick="changeSort('alamat_pemilik_barang')" class="sort">Alamat Pemilik Barang</th>
                                <th onclick="changeSort('status')" class="sort">Status</th>
                                <th>Action</th>
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
            url: "<?= base_url("setting-akun-bc/no-ijin-tpb-all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.sort = sort;
                data.sortType = sortType;
                data.pengusaha_tpb_id = $('.pengusaha_tpb_id').val()
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
                data: "no_ijin_tpb",
                className: "text-center"
            }, {
                data: "tanggal_skep_tpb",
                className: "text-center"
            }, {
                data: "alamat_pemilik_barang",
                className: "text-center"
            },
            {
                data: "status",
                className: "text-center",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let htmlRes = '';
                    if (row.status == "TIDAK AKTIF") {
                        htmlRes += `
                            <div class="badge badge-danger">
                                TIDAK AKTIF
                            </div>`
                    } else {
                        htmlRes += `
                            <div class="badge badge-primary">
                                AKTIF
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
                render: function(data, type, row) {
                    let id = row.id;
                    return `
                        <button class="btn btn-warning posting-spp mr-1 edit-table-detail" 
                        onclick="updateForm('${id}')" 
                        >
                            <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                        </button><button class="btn btn-danger" onclick="deleteForm('${id}')">
                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                        </button>
                    `
                }
            }
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

    $('.tanggal_skep_tpb').datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#status').select2({
        placeholder: "Pilih Status",
        theme: "bootstrap-5",
        allowClear: true,
    }).change(function() {

    });

    var validator = $(".create-form").validate({
        rules: {
            no_ijin_tpb: {
                required: true
            },
            tanggal_skep_tpb: {
                required: true
            },
            alamat_pemilik_barang: {
                required: true
            },
            status: {
                required: true
            }
        },
        messages: {
            no_ijin_tpb: {
                required: "Nomor Ijin TPB wajib diisi"
            },
            tanggal_skep_tpb: {
                required: "Tanggal skep TPB wajib diisi"
            },
            alamat_pemilik_barang: {
                required: "Alamat pemilik barang wajib diisi"
            },
            status: {
                required: "Status wajib diisi"
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


    $("#status")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

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
                            url: "<?= base_url("setting-akun-bc/no-ijin-tpb-update"); ?>",
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
                                    resetForm();
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
                            url: "<?= base_url("setting-akun-bc/no-ijin-tpb-create"); ?>",
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
                                    resetForm();
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

    function updateForm(id) {
        $.ajax({
            url: "<?= base_url("setting-akun-bc/no-ijin-tpb-get"); ?>",
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
                    $('#no_ijin_tpb').val(res.data.no_ijin_tpb);
                    $('#tanggal_skep_tpb').val(res.data.tanggal_skep_tpb);
                    $('#alamat_pemilik_barang').val(res.data.alamat_pemilik_barang);
                    $('#status').val(res.data.status).change();
                }
            }
        })

    }

    function deleteForm(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url("setting-akun-bc/no-ijin-tpb-delete"); ?>",
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
                    },
                });
            }
        })

    }

    function resetForm() {
        $('#id').val();
        $('#no_ijin_tpb').val('');
        $('#tanggal_skep_tpb').val('');
        $('#status').val(null).change();
        $('#alamat_pemilik_barang').val('');
        validator.resetForm();
        validator.reset();
    }
</script>

<?= $this->endSection(); ?>