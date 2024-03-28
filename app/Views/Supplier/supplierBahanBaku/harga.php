<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Set Harga Barang</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("supplier-bahan-baku"); ?>">
                Batal
            </a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <?= csrf_field() ?>
            <table width="100%" class="mb-3">
                <tbody>
                    <tr style="color: black;">
                        <td width="150px">Nama Supplier</td>
                        <td width="5px">:</td>
                        <td><?= empty($dataSupplier) ? "" : strtoupper($dataSupplier->name); ?></td>
                    </tr>
                    <tr style="color: black; height: 20px;">
                        <td colspan="3"></td>
                    </tr>
                    <tr style="color: black;">
                        <td width="150px">Alamat</td>
                        <td width="25px">:</td>
                        <td><?= empty($dataSupplier) ? "" : $dataSupplier->address; ?></td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" name="spesifikasi_id_hidden" id="spesifikasi_id_hidden" class="spesifikasi_id_hidden">
            <input autocomplete="one-time-code" value="<?= empty($dataSupplier) ? "" : $dataSupplier->id; ?>" type="hidden" class="id_supplier" name="id_supplier" id="id_supplier" />
            <br>
            <form class="harga-form" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id" class="id">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select bahan_baku" name="bahan_baku" id="bahan_baku" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataBarang as $b) : ?>
                                    <option value="<?= $b["id"]; ?>">
                                        <?= $b["barang_name"]; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Nama Barang</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select spesifikasi_id" name="spesifikasi_id" id="spesifikasi_id" aria-label="Floating label select example">
                                <option value=""></option>
                            </select>
                            <label for="floatingInput">Pilih Spesifikasi</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="0" autocomplete="one-time-code" type="number" class="form-control harga_umum" id="harga_umum" name="harga_umum" placeholder="Harga Umum">
                            <label for="floatingInput">Harga Umum</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="0" autocomplete="one-time-code" type="number" class="form-control harga_harian" id="harga_harian" name="harga_harian" placeholder="Harga Harian">
                            <label for="floatingInput">Harga Harian</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="0" autocomplete="one-time-code" type="number" class="form-control harga_bulanan" id="harga_bulanan" name="harga_bulanan" placeholder="Harga Bulanan">
                            <label for="floatingInput">Harga Bulanan</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-add btn-block float-right btn-submit-harga">
                            <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                        </button>
                        <button style="border-color: #e7323a !important; background-color: #e7323a !important; margin-right: 10px !important;" class="btn btn-add btn-block float-right" onclick="resetForm()">
                            <i class="fa-solid fa-rotate-right mr-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="row justify-content-end mb-3">
                    <div class="col-md-4">
                        <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari Barang / Spesifikasi" value="" />
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th width="10">No</th>
                                <th>Barang</th>
                                <th>Spesifikasi</th>
                                <th>Harga Umum</th>
                                <th>Harga Harian</th>
                                <th>Harga Bulanan</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "createdAt";
    let sortType = "desc";

    var table = $('.dataTable').DataTable({
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
            url: "<?= base_url("supplier-bahan-baku/all-harga"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.supplier_id = "<?= $dataSupplier->id ?>";
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
                data: "bahan_baku_name",
                className: "text-center"
            }, {
                data: "spesifikasi",
                className: "text-center"
            }, {
                data: "harga_umum",
                className: "text-center"
            },
            {
                data: "harga_harian",
                className: "text-center"
            },
            {
                data: "harga_bulanan",
                className: "text-center"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row?.id;
                    let bahan_baku = row?.bahan_baku;
                    let spesifikasi_id = row?.spesifikasi_id;
                    let harga_umum = row?.harga_umum_normal;
                    let harga_bulanan = row?.harga_bulanan_normal;
                    let harga_harian = row?.harga_harian_normal;

                    return `
                        <button class="btn btn-warning posting-spp mr-1 edit-table-detail" 
                        data-id="${id}" 
                        data-bahan_baku="${bahan_baku}"
                        data-spesifikasi_id="${spesifikasi_id}"
                        data-harga_umum="${harga_umum}"
                        data-harga_bulanan="${harga_bulanan}"
                        data-harga_harian="${harga_harian}"
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

    $(document).ready(function() {
        $(document).on('click', '.edit-table-detail', function() {
            var id = $(this).data('id');
            var bahan_baku = $(this).data('bahan_baku');
            var harga_umum = $(this).data('harga_umum');
            var harga_bulanan = $(this).data('harga_bulanan');
            var harga_harian = $(this).data('harga_harian');
            var spesifikasi_id = $(this).data('spesifikasi_id');

            $('.id').val(id);
            $('.bahan_baku').val(bahan_baku).change();
            $('.spesifikasi_id_hidden').val(spesifikasi_id);
            $('.harga_umum').val(harga_umum);
            $('.harga_bulanan').val(harga_bulanan);
            $('.harga_harian').val(harga_harian);
        });

    });

    $('.bahan_baku').select2({
        placeholder: "Pilih Bahan Baku",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        var id = $(this).val();
        getSpesifikasiBarang(id);
    });

    $('.spesifikasi_id').select2({
        placeholder: "Pilih Spesifikasi",
        theme: "bootstrap-5",
        allowClear: true
    })

    $('.search').keyup(function() {
        table.ajax.reload();
    });

    //CSS SELECT2 FLOATING LABEL
    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.form-select')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    var validator = $(".harga-form").validate({
        rules: {
            bahan_baku: {
                required: true
            },
            spesifikasi_id: {
                required: true
            },
            harga_umum: {
                required: true
            },
            harga_harian: {
                required: true
            },
            harga_bulanan: {
                required: true
            }
        },
        messages: {
            bahan_baku: {
                required: "Bahan Baku wajib diisi"
            },
            spesifikasi_id: {
                required: "Spesifikasi wajib diisi"
            },
            harga_umum: {
                required: "Harga Umum wajib diisi"
            },
            harga_harian: {
                required: "Harga Harian wajib diisi"
            },
            harga_bulanan: {
                required: "Harga Bulanan wajib diisi"
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

    $(".btn-submit-harga").click(function() {
        if ($('.harga-form').valid()) {
            const csrf = $(`[name="${csrfToken}"]`);
            let data = new FormData(document.querySelector(".harga-form"));
            let id = $('.id').val();
            data.append("supplier_id", $(".id_supplier").val())

            if (id) {
                // UPDATE
                $.ajax({
                    url: "<?= base_url("supplier-bahan-baku/harga/update"); ?>",
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
                        if (response.status) {
                            resetForm();
                            csrf.val(response.token);
                            table.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    }
                });
            } else {
                // CREATE
                $.ajax({
                    url: "<?= base_url("supplier-bahan-baku/harga/save"); ?>",
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
                        if (response.status) {
                            resetForm();
                            csrf.val(response.token)
                            table.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    }
                });
            }
        }
    });

    const getSpesifikasiBarang = function(id) {
        $.ajax({
            url: `<?= base_url("supplier-bahan-baku/spesifikasi-barang"); ?>`,
            method: "GET",
            dataType: "json",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                id: id
            },
            success: function(res) {
                $(".spesifikasi_id").empty();
                $(".spesifikasi_id").append(`<option value=""></option>`);
                res.data.forEach(function(item) {
                    $(".spesifikasi_id").append(`<option ${spesifikasi_id === item.id ? 'selected' : ''} value="${item.id}">${item.spesifikasi}</option>`);
                });
                var spesifikasi_id_hidden = $('.spesifikasi_id_hidden').val();
                console.log(spesifikasi_id_hidden);
                if (spesifikasi_id_hidden) {
                    $('.spesifikasi_id').val(spesifikasi_id_hidden).change();
                }
            }
        })
    }

    const deleteForm = function(id) {
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
                $.ajax({
                    url: "<?= base_url("supplier-bahan-baku/harga/delete"); ?>",
                    data: {
                        id: id,
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
                            table.ajax.reload();
                        }
                    },

                });
            }
        })
    }

    const resetForm = function() {
        validator.resetForm();
        validator.reset();

        spesifikasi_id = null;
        $('.id').val(null);
        $('.divisi_id').val(null).change();
        $('.bahan_baku').val(null).change();
        $('.spesifikasi_id').val(null).change();
        $('.harga_umum').val('0');
        $('.harga_harian').val('0');
        $('.harga_bulanan').val('0');
        $('.spesifikasi_id_hidden').val(null);
    }
</script>

<?= $this->endSection(); ?>