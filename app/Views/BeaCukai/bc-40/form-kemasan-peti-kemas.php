<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>

<section class="section section-form">
    <?php include('header.php') ?>
    <div class="card">
        <div class="card-header" style="font-weight: bold; color:black;">
            BC 4.0 - PEMBERITAHUAN PEMASUKAN BARANG ASAL TEMPAT LAIN DALAM DAERAH PABEAN KE TEMPAT PENIMBUNAN BERIKAT
        </div>
        <div class="card-body">
            <?php include_once('nav.php') ?>
            <?= csrf_field() ?>
            <div class="alert alert-secondary alert-dismissible fade show mt-3 text-black" role="alert">
                <b>Wajib</b> mencantumkan data kemasan minimal 1 (satu) jenis kemasan
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="row mt-1">
                <div class="col-sm-6 mt-1">
                    <form id="form-kemasan">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Kemasan
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input readonly value="<?= $seriKemasan ?>" id="kemasan_seri_kemasan" name="kemasan_seri_kemasan" type="number" class="form-control kemasan_seri_kemasan" placeholder="">
                                <label>Seri Kemasan</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kemasan_jenis_kemasan" id="kemasan_jenis_kemasan" name="kemasan_jenis_kemasan" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeJenisKemasan as $k) : ?>
                                        <option value="<?= encrypt($k['description']) ?>">
                                            <?= $k['description'] . " - " . strtoupper($k['value']) . "" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Jenis Kemasan (Bea Cukai)</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kemasan_kemasan_id" id="kemasan_kemasan_id" name="kemasan_kemasan_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($dropdownKemasan as $d) : ?>
                                        <option data-jumlah_kemasan="<?= $d['jumlah_kemasan'] ?>" value="<?= $d['kemasan_id'] ?>">
                                            <?= $d['kode_kemasan'] . " - " . $d['kemasan_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Jenis Kemasan (Inventori)</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="kemasan_jumlah_kemasan" name="kemasan_jumlah_kemasan" type="number" class="form-control kemasan_jumlah_kemasan" placeholder="">
                                <label>Jumlah Kemasan</label>
                            </div>
                        </div>

                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="kemasan_merk_kemasan" name="kemasan_merk_kemasan" type="text" class="form-control kemasan_merk_kemasan" placeholder="">
                                <label>Merk Kemasan</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="row" style="float: right; margin-bottom:5px;">
                                    <div class="col-sm" style="margin-right: -20px;">
                                        <button type="button" class="btn btn-add btn-block float-right btn-submit-kemasan" style="float: right;">
                                            <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-kemasan" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center;">Seri Kemasan</th>
                                        <th style="text-align: center;">Jumlah Kemasan</th>
                                        <th style="text-align: center;">Jenis Kemasan (Bea Cukai)</th>
                                        <th style="text-align: center;">Jenis Kemasan (Inventori)</th>
                                        <th style="text-align: center;">Merk Kemasan</th>
                                        <th style="text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </form>

                </div>

                <div class="col-sm-6 mt-1">
                    <form id="form-kontainer">

                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Kontainer / Peti Kemas
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input value="<?= $seriKontainer ?>" readonly id="kontainer_seri" name="kontainer_seri" type="number" class="form-control kontainer_seri" placeholder="">
                                <label>Seri Kontainer</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="kontainer_nomor" name="kontainer_nomor" type="text" class="form-control kontainer_nomor" placeholder="">
                                <label>Nomor</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kontainer_ukuran" id="kontainer_ukuran" name="kontainer_ukuran" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeUkuranKontainer as $k) : ?>
                                        <option value="<?= encrypt($k['value']) ?>">
                                            <?= $k['value'] . " - " . strtoupper($k['description']) . "" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Ukuran Peti Kemas</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kontainer_jenis" id="kontainer_jenis" name="kontainer_jenis" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeJenisKontainer as $k) : ?>
                                        <option value="<?= encrypt($k['description']) ?>">
                                            <?= $k['description'] . " - " . strtoupper($k['value']) . "" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Jenis Peti Kemas</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kontainer_tipe" id="kontainer_tipe" name="kontainer_tipe" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeTipeKontainer as $k) : ?>
                                        <option value="<?= encrypt($k['value']) ?>">
                                            <?= $k['value'] . " - " . strtoupper($k['description']) . "" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Tipe Peti Kemas</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="row" style="float: right; margin-bottom:5px;">
                                    <div class="col-sm" style="margin-right: -10px;">
                                        <button type="button" class="btn btn-add btn-block float-right btn-submit-peti-kemas" style="float: right;">
                                            <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-peti-kemas" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center;">Seri Peti Kemas</th>
                                        <th style="text-align: center;">Nomor Peti Kemas</th>
                                        <th style="text-align: center;">Ukuran Peti Kemas</th>
                                        <th style="text-align: center;">Jenis Peti Kemas</th>
                                        <th style="text-align: center;">Tipe Peti Kemas</th>
                                        <th style="text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>
</section>

<script>
    // CSRF
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    $('#kemasan_jenis_kemasan').select2({
        placeholder: "Pilih Kemasan (Bea Cukai)",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#kontainer_ukuran').select2({
        placeholder: "Pilih Ukuran Peti Kemas",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#kontainer_jenis').select2({
        placeholder: "Pilih Jenis Peti Kemas",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#kontainer_tipe').select2({
        placeholder: "Pilih Tipe Peti Kemas",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#kemasan_kemasan_id').select2({
        placeholder: "Pilih Kemasan (Inventori)",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        var selected = $('#kemasan_kemasan_id option:selected');
        $('#kemasan_jumlah_kemasan').val(selected.data('jumlah_kemasan'));
    });


    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    const tableListInformasiKemasan = $('.table-list-informasi-kemasan').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: false,
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
            url: "<?= base_url("bea-cukai-bc-40/id/kemasan-peti-kemas/kemasan/data/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.bc_purchase_order_id = "<?= encrypt($bcPo['id']) ?>";
                data.sort = "bc_kemasan.createdAt";
                data.sortType = "DESC";
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [

            {
                data: "seri_kemasan",
                searchable: false,
                sortable: false,
                className: "text-center",
            },
            {
                data: "jumlah_kemasan",
                searchable: false,
                sortable: false,
                className: "text-center",
            },
            {
                data: "kemasan_name",
                searchable: false,
                sortable: false,
                className: "text-center",
            },
            {
                data: "kode_jenis_kemasan",
                searchable: false,
                sortable: false,
                className: "text-center"
            },
            {
                data: "merk_kemasan",
                className: "text-center",
                searchable: false,
                sortable: false,
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    return `<button type="button" class="btn btn-danger" onclick="removeKemasan('${row.id}')" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>`;
                }
            }


        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Tidak ada kemasan",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });


    const tableListInformasiPetiKemas = $('.table-list-informasi-peti-kemas').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: false,
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
            url: "<?= base_url("bea-cukai-bc-40/id/kemasan-peti-kemas/kontainer/data/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.bc_purchase_order_id = "<?= encrypt($bcPo['id']) ?>";
                data.sort = "bc_kontainer.createdAt";
                data.sortType = "DESC";
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [

            {
                data: "seri_kontainer",
                searchable: false,
                sortable: false,
                className: "text-center",
            },
            {
                data: "nomor_kontainer",
                searchable: false,
                sortable: false,
                className: "text-center",
            },
            {
                data: "kode_ukuran_kontainer",
                searchable: false,
                sortable: false,
                className: "text-center"
            },
            {
                data: "kode_jenis_kontainer",
                className: "text-center",
                searchable: false,
                sortable: false,
            },
            {
                data: "kode_tipe_kontainer",
                className: "text-center",
                searchable: false,
                sortable: false,
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    return `<button type="button" class="btn btn-danger" onclick="removeKontainer('${row.id}')" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>`;
                }
            }


        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Tidak ada peti kemas",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    var validatorKemasan = $("#form-kemasan").validate({
        rules: {
            kemasan_seri_kemasan: {
                required: true
            },
            kemasan_jumlah_kemasan: {
                required: true
            },
            kemasan_jenis_kemasan: {
                required: true
            },
            kemasan_kemasan_id: {
                required: true
            },
            kemasan_merk_kemasan: {
                required: true
            },
        },
        messages: {
            kemasan_seri_kemasan: {
                required: "Seri kemasan wajib diisi"
            },
            kemasan_jumlah_kemasan: {
                required: "Jumlah kemasan wajib diisi"
            },
            kemasan_jenis_kemasan: {
                required: "Pilih kemasan (Bea Cukai)"
            },
            kemasan_kemasan_id: {
                required: "Pilih kemasan (Inventori)"
            },
            kemasan_merk_kemasan: {
                required: "Merk kemasan wajib diisi"
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

    var validatorKontainer = $("#form-kontainer").validate({
        rules: {
            kontainer_seri: {
                required: true
            },
            kontainer_nomor: {
                required: true
            },
            kontainer_ukuran: {
                required: true
            },
            kontainer_jenis: {
                required: true
            },
            kontainer_tipe: {
                required: true
            },
        },
        messages: {
            kontainer_seri: {
                required: "Seri peti kemas wajib diisi"
            },
            kontainer_nomor: {
                required: "Nomor peti kemas wajib diisi"
            },
            kontainer_ukuran: {
                required: "Pilih ukuran peti kemas"
            },
            kontainer_jenis: {
                required: "Pilih jenis peti kemas"
            },
            kontainer_tipe: {
                required: "Pilih tipe peti kemas"
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


    $('.btn-submit-kemasan').click(function() {
        if ($('#form-kemasan').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Kemasan ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector("#form-kemasan"));
                    formData.append("bc_purchase_order_id", "<?= encrypt($bcPo['id']) ?>");
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-40/id/kemasan-peti-kemas/kemasan/create"); ?>",
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
                            if (response.status) {
                                csrf.val(response.token);
                                tableListInformasiKemasan.ajax.reload();
                                $('#kemasan_seri_kemasan').val(response.kemasan_seri_kemasan);
                                $('#kemasan_jenis_kemasan').val(null).change();
                                $('#kemasan_kemasan_id').val(null).change();
                                $('#kemasan_jumlah_kemasan').val('');
                                $('#kemasan_merk_kemasan').val('');
                            }
                        },
                    });
                }
            })
        }
    });

    $('.btn-submit-peti-kemas').click(function() {
        if ($('#form-kontainer').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Peti Kemas ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector("#form-kontainer"));
                    formData.append("bc_purchase_order_id", "<?= encrypt($bcPo['id']) ?>");
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-40/id/kemasan-peti-kemas/kontainer/create"); ?>",
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
                            if (response.status) {
                                csrf.val(response.token);
                                tableListInformasiPetiKemas.ajax.reload();
                                $('#kontainer_seri').val(response.kontainer_seri);
                                $('#kontainer_nomor').val('');
                                $('#kontainer_ukuran').val(null).change();
                                $('#kontainer_jenis').val(null).change();
                                $('#kontainer_tipe').val(null).change();
                            }
                        },
                    });
                }
            })
        }
    });

    $('#btn-ambil-manifest').click(function(e) {
        e.preventDefault();
        $.ajax({
            url: `<?= base_url("bea-cukai-bc-40/api/get-kontainer-peti-kemas"); ?>`,
            method: "GET",
            data: {
                bc_purchase_order_id: "<?= encrypt($bcPo['id']) ?>"
            },
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            dataType: "json",
            success: function(res) {
                csrf.val(res.token);
                tableListInformasiPetiKemas.ajax.reload();
                if (res.status) {
                    Swal.fire({
                        icon: 'success',
                        title: res.message,
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: res.message,
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                }
            }
        });
    });


    function removeKemasan(id) {
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
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("bea-cukai-bc-40/id/kemasan-peti-kemas/kemasan/delete"); ?>",
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
                        if (response.status) {
                            tableListInformasiKemasan.ajax.reload();
                            $('#kemasan_seri_kemasan').val(response.kemasan_seri_kemasan);
                        }
                    },
                });
            }
        })
    }

    function removeKontainer(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Kontainer/Peti Kemas ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("bea-cukai-bc-40/id/kemasan-peti-kemas/kontainer/delete"); ?>",
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
                        if (response.status) {
                            tableListInformasiPetiKemas.ajax.reload();
                            $('#kontainer_seri').val(response.kontainer_seri);
                        }
                    },
                });
            }
        })
    }
</script>


<?= $this->endSection(); ?>