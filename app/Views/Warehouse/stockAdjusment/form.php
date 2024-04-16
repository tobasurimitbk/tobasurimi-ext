<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($adjusment) ? "Tambah Stok Adjusment" : "Update Stok Adjusment" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("stock-adjusment"); ?>">
                Batal
            </a>
            <?php if (!empty($adjusment)) : ?>
                <?php if ($adjusment['status_posting'] == "0") : ?>
                    <?php if (can('Inventori', 'Stok Adjusment', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Inventori', 'Stok Adjusment', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-adjusment">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Inventori', 'Stok Adjusment', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else : ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Adjusment</label>
                </div>
            </div>
            <form class="create-form">
                <input type="hidden" name="id" id="id" value="<?= !empty($adjusment) ? encrypt($adjusment['id']) : '' ?>" class="id">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" disabled class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime(!empty($adjusment) ? $adjusment['tanggal'] : $tanggal)); ?>">
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" <?= !empty($adjusment) ? 'disabled=true' : ''; ?> value="<?= !empty($adjusment) ? $adjusment['no_adjusment'] : "ADJ//" . date('m') . "/1/" . date('Y'); ?>" type="text" class="form-control no_adjusment" id="no_adjusment" name="no_adjusment" placeholder="No. Adjusment">
                                    <label for="floatingInput">No. Adjusment</label>
                                </div>
                                <div style="<?= !empty($adjusment) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 20px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($adjusment) ? ($adjusment['status_posting'] ? 'disabled' : '') : '' ?> class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= !empty($adjusment) ? ($adjusment['divisi_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($adjusment) ? ($adjusment['status_posting'] ? 'disabled' : '') : '' ?> class="form-select tipe_adjusment" id="tipe_adjusment" name="tipe_adjusment" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($tipeAdjusment as $t) : ?>
                                    <option <?= !empty($adjusment) ? ($adjusment['tipe_adjusment'] == $t['value'] ? 'selected' : '') : '' ?> value="<?= $t['value'] ?>">
                                        <?= $t['value']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tipe Adjusment</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($adjusment) ? ($adjusment['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Keterangan" value="<?= !empty($adjusment) ? $adjusment['keterangan'] : '' ?>" class="form-control keterangan" id="keterangan" name="keterangan" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Keterangan</label>
                        </div>
                    </div>
                </div>

            </form>

            <div class="detail-form-component">
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Data Barang</label>
                    </div>
                </div>
                <form class="detail-form">
                    <input type="hidden" name="id_detail" class="id_detail" id="id_detail">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select type_barang" id="type_barang" name="type_barang" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($tipeBarang as $t) : ?>
                                        <option value="<?= $t['description'] ?>">
                                            <?= strtoupper($t['value']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Tipe Barang</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php if (!empty($warehouse)) : ?>
                                        <?php foreach ($warehouse as $w) : ?>
                                            <option value="<?= $w['id'] ?>">
                                                <?= $w['warehouse_name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Warehouse</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select spesifikasi_id" id="spesifikasi_id" name="spesifikasi_id" aria-label="Floating label select example">
                                    <option value=""></option>

                                </select>
                                <label for="floatingInput" style="z-index: 1;">Barang - Spesifikasi</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input placeholder="" class="form-control satuan_name" disabled id="satuan_name" name="satuan_name" aria-label="Floating label select example" />
                                <label for="floatingInput" style="z-index: 1;">Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select bc_id" id="bc_id" name="bc_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Dokumen Pabean</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select operasi" id="operasi" name="operasi" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <option value="PLUS">PENAMBAHAN STOK</option>
                                    <option value="MINUS">PENGURANGAN STOK</option>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Operasi</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input disabled placeholder="Stok Warehouse" class="form-control stok_warehouse" id="stok_warehouse" name="stok_warehouse" aria-label="Floating label select example" />
                                <label for="floatingInput" style="z-index: 1;">Stok Warehouse</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating" style="height: 50px;">
                                <input placeholder="Qty" oninput="preventNegativeInput(this)" class="form-control qty" id="qty" name="qty" aria-label="Floating label select example" />
                                <label for="floatingInput" style="z-index: 1;">Qty Adjusment</label>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="col-subtitle-modal">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold modal-sub-title">List Barang</label>
                        </div>
                        <div class="col-md-6">

                            <button class="btn btn-show-detail btn-add btn-block btn-submit-detail float-right" data-btn="detail-modal">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                            <button style="border-color: #e7323a !important; background-color: #e7323a !important; margin-right: 10px !important;" class="btn btn-add btn-block float-right" onclick="resetFormDetail()">
                                <i class="fa-solid fa-rotate-right mr-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Tipe Barang</th>
                                <th>Kode</th>
                                <th>Barang - Spesifikasi</th>
                                <th>Satuan</th>
                                <th>Warehouse</th>
                                <th>Dokumen</th>
                                <th>QTY</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="9" style="text-align: center;">Tidak Ada Data</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>



<script>
    var listStock = [];
    var qtyTotal = 0;
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    <?php if (!empty($adjusment)) : ?>
        <?php if ($adjusment['status_posting']) : ?>
            $('.detail-form-component').hide();
        <?php endif; ?>
        <?php foreach ($listBarang as $l) : ?>
            listStock.push({
                id: "<?= $l['id'] ?>",
                stock_id: "<?= $l['stock_id'] ?>",
                spesifikasi_id: "<?= $l['spesifikasi_id'] ?>",
                bc_id: "<?= $l['bc_id'] ?>",
                no_aju: "<?= $l['no_aju'] ?>",
                dokumen_text: "<?= $l['dokumen_text'] ?>",
                barang: "<?= $l['barang'] ?>",
                kode_barang: "<?= $l['kode_barang'] ?>",
                type_barang: "<?= $l['type_barang'] ?>",
                type_barang_text: "<?= $l['type_barang_text'] ?>",
                satuan_name: "<?= $l['satuan_name'] ?>",
                warehouse: "<?= $l['warehouse'] ?>",
                warehouse_id: "<?= $l['warehouse_id'] ?>",
                operasi: "<?= $l['operasi'] ?>",
                qty: "<?= $l['qty'] ?>"
            })
        <?php endforeach; ?>
        drawTable();
    <?php endif; ?>

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // GET WAREHOUSES
        $.ajax({
            url: `<?= base_url('stock-adjusment/warehouse'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $(".divisi_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_id").empty()
                $(".warehouse_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
                $(".warehouse_id").val();
            }
        });
        // GET NO ADJUSMENT
        changeStatus();
        listStock = [];
        drawTable();
    });

    $('#type_barang').select2({
        placeholder: "Pilih Tipe Barang",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        getListBarang();
    });

    $('#tipe_adjusment').select2({
        placeholder: "Pilih Tipe Adjusment",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {});

    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        getListBarang();
    });

    $('#spesifikasi_id').select2({
        placeholder: "Pilih Barang (Hanya Barang Yang Sudah Inisiasi Stok)",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        var selected = $('#spesifikasi_id option:selected');
        $('#satuan_name').val(selected.data('kode_satuan'));
        getListDokumenPabean();
    });

    $('#bc_id').select2({
        placeholder: "Pilih Dokumen Pabean",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        var selected = $('#bc_id option:selected');
        var satuan_name = $('#satuan_name').val();
        var stock = selected.data('stock') == undefined ? "" : selected.data('stock');
        // $('#qty').val(selected.data('stock'));
        $('.stok_warehouse').val(stock + ' ' + satuan_name);
    });

    $('#operasi').select2({
        placeholder: "Pilih Operasi",
        theme: "bootstrap-5",
        allowClear: true
    });

    // VALIDATOR HEADER
    var validator = $(".create-form").validate({
        rules: {
            divisi_id: {
                required: true
            },
            keterangan: {
                required: true
            },
        },
        messages: {
            divisi_id: {
                required: "Pilih departemen"
            },
            keterangan: {
                required: "Keterangan wajib diisi"
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


    // VALIDATOR DETAIL
    var validatorDetail = $(".detail-form").validate({
        rules: {
            type_barang: {
                required: true
            },
            warehouse_id: {
                required: true
            },
            spesifikasi_id: {
                required: true
            },
            bc_id: {
                required: true
            },
            operasi: {
                required: true
            },
            qty: {
                required: true,
                number: true,
                min: -1
            },
        },
        messages: {
            type_barang: {
                required: "Pilih tipe barang"
            },
            warehouse_id: {
                required: "Pilih warehouse"
            },
            spesifikasi_id: {
                required: "Pilih barang"
            },
            bc_id: {
                required: "Pilih dokumen pabean"
            },
            operasi: {
                required: "Pilih operasi"
            },
            qty: {
                required: "Qty wajib diisi",
                number: "Qty harus berupa angka",
                min: "Qty harus diisi lebih dari 0"
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


    $("#type_barang,#divisi_id,#warehouse_id,#spesifikasi_id,#bc_id,#no_aju,#operasi,#tipe_adjusment")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    // SUBMIT HEADER
    $('.btn-submit-parent').click(function() {
        if (listStock.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Barang masih kosong',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                reverseButtons: true,
                confirmButtonText: 'Oke',
            })
        } else {
            if ($('.create-form').valid()) {
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
                        var id = $('.id').val();
                        var formData = new FormData(document.querySelector('.create-form'));
                        formData.append("no_adjusment", $('#no_adjusment').val());
                        formData.append("listBarang", JSON.stringify(listStock));
                        if (id) {
                            // UPDATE
                            $.ajax({
                                url: "<?= base_url("stock-adjusment/update"); ?>",
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
                                            })
                                            .then(() => {
                                                location.reload();
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
                        } else {
                            // INSERT
                            $.ajax({
                                url: "<?= base_url("stock-adjusment/save"); ?>",
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
                                            })
                                            .then(() => {
                                                location.replace('<?= base_url("stock-adjusment/id") ?>/' + response.id, "_blank")
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
                    }
                })
            }
        }
    })

    // SUBMIT DETAIL
    $('.btn-submit-detail').click(function() {
        if ($('.detail-form').valid()) {
            handleMinusAdjusment();

        }
    });

    $('.posting-adjusment').click(function() {
        Swal.fire({
            icon: 'question',
            title: 'Posting Adjusment ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("stock-adjusment/posting"); ?>",
                    data: {
                        id: $('.id').val()
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
                            }).then((result) => {
                                location.reload();
                            });
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
    })

    $('.delete-parent').click(function() {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Adjusment ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("stock-adjusment/delete"); ?>",
                    data: {
                        id: $('.id').val()
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
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            }).then((result) => {
                                window.location.href = "<?= base_url('stock-adjusment') ?>"
                            });
                        }
                    },
                });
            }
        })
    });

    function insertList() {
        listStock.push({
            id: $('#bc_id option:selected').val(), // stock detail 2
            stock_id: $('#spesifikasi_id option:selected').data('stock_id'),
            spesifikasi_id: $('#spesifikasi_id option:selected').val(),
            bc_id: $('#bc_id option:selected').data('bc_id'),
            no_aju: $('#bc_id option:selected').data('no_aju'),
            dokumen_text: $('#bc_id option:selected').text(),
            barang: $('#spesifikasi_id option:selected').data('barang'),
            kode_barang: $('#spesifikasi_id option:selected').data('kode_barang'),
            type_barang: $('#type_barang option:selected').val(),
            type_barang_text: $('#type_barang option:selected').text(),
            satuan_name: $('#satuan_name').val(),
            warehouse: $('#warehouse_id option:selected').text(),
            warehouse_id: $('#warehouse_id option:selected').val(),
            operasi: $('#operasi option:selected').val(),
            qty: $('#qty').val()
        });

        drawTable(listStock);
    }

    function updateList(id) {
        for (var i = 0; i < listStock.length; i++) {
            if (listStock[i].id == id) {
                listStock[i].id = $('#bc_id option:selected').val();
                listStock[i].stock_id = $('#spesifikasi_id option:selected').data('stock_id');
                listStock[i].spesifikasi_id = $('#spesifikasi_id option:selected').val();
                listStock[i].bc_id = $('#bc_id option:selected').data('bc_id');
                listStock[i].no_aju = $('#bc_id option:selected').data('no_aju');
                listStock[i].dokumen_text = $('#bc_id option:selected').text();
                listStock[i].barang = $('#spesifikasi_id option:selected').data('barang');
                listStock[i].kode_barang = $('#spesifikasi_id option:selected').data('kode_barang');
                listStock[i].type_barang = $('#type_barang option:selected').val();
                listStock[i].type_barang_text = $('#type_barang option:selected').text();
                listStock[i].satuan_name = $('#satuan_name').val();
                listStock[i].warehouse = $('#warehouse_id option:selected').text();
                listStock[i].warehouse_id = $('#warehouse_id option:selected').val();
                listStock[i].operasi = $('#operasi option:selected').val();
                listStock[i].qty = $('#qty').val();
            }
        }

        drawTable(listStock);
    }

    function deleteDetail(id) {
        var indexToRemove = -1;
        for (var i = 0; i < listStock.length; i++) {
            if (listStock[i].id == id) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listStock.splice(indexToRemove, 1);
            drawTable();
        }
    }

    function editDetail(id) {
        var item = null;
        for (var i = 0; i < listStock.length; i++) {
            if (listStock[i].id == id) {
                item = listStock[i];
                break;
            }
        }
        $('#id_detail').val(item.id);
        $('#type_barang').val(item.type_barang).change();
        $('#warehouse_id').val(item.warehouse_id).change();

        // SPESIFIKASI ID
        $.ajax({
            url: `<?= base_url('stock-adjusment/list-barang-stock-init'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                type_barang: $(".type_barang option:selected").val(),
                divisi_id: $(".divisi_id option:selected").val(),
                warehouse_id: $(".warehouse_id option:selected").val()
            },
            dataType: "json",
            success: function(res) {
                $(".spesifikasi_id").empty()
                $(".spesifikasi_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".spesifikasi_id").append(`<option data-stock_id="${item.stock_id}" data-kode_barang="${item.kode_barang}" data-barang="${item.barang}" data-kode_satuan="${item.kode_satuan}" value="${item.spesifikasi_id}">(${item.kode_barang}) ${item.barang}</option>`)
                })
                $('#spesifikasi_id').val(item.spesifikasi_id).change();


                // DOKUMEN PABEAN
                $.ajax({
                    url: `<?= base_url('stock-adjusment/list-stock-dokumen-bc'); ?>`,
                    method: "GET",
                    beforeSend: function() {
                        setLoading();
                    },
                    complete: function() {
                        stopLoading();
                    },
                    data: {
                        stock_id: $(".spesifikasi_id option:selected").data('stock_id'),
                    },
                    dataType: "json",
                    success: function(res) {
                        $(".bc_id").empty()
                        $(".bc_id").append(`<option value=""></option>`)
                        res.data.forEach(function(item) {
                            $(".bc_id").append(`<option data-stock="${item.stok_total}" data-bc_id="${item.bc_id}" data-no_aju="${item.no_aju}" data-dokumen="${'('+item.bc_type+') '+item.no_aju}"  value="${item.id}">(${item.bc_type}) ${item.no_aju}</option>`)
                        })
                        $('#bc_id').val(item.id).change();
                    }
                });
            }
        });

        $('#satuan_name').val(item.satuan_name);
        $('#operasi').val(item.operasi).change();
        // $('#qty').val(item.qty);
    }

    function drawTable() {
        const table = $('#dataTable');
        table.find('tbody').empty();
        table.find('tfoot').empty();
        var no = 1;
        if (listStock.length == 0) {
            var newRow = $('<tr>');
            newRow.append($('<td  colspan="9" style="text-align: center;">').text("Tidak Ada Data"));
            table.find('tfoot').append(newRow);
        } else {
            var no = 1;
            $.each(listStock, function(i, v) {
                var iconPlusMinus = (v.operasi == "PLUS" ? "+" : "-");
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td>').text(no++));
                newRow.append($('<td>').text(v.type_barang_text));
                newRow.append($('<td>').text(v.kode_barang));
                newRow.append($('<td>').text(v.barang));
                newRow.append($('<td>').text(v.satuan_name));
                newRow.append($('<td>').text(v.warehouse));
                newRow.append($('<td>').text(v.dokumen_text));
                newRow.append($('<td>').text("(" + iconPlusMinus + ") " + parseFloat(v.qty) || 0));
                <?php if (!empty($adjusment)) : ?>
                    <?php if (($adjusment['status_posting'])) : ?>
                        newRow.append($('<td>').html(
                            `
                            -
                `
                        ));
                    <?php else : ?>
                        newRow.append($('<td>').html(
                            `
                <button class="btn btn-warning posting-spp mr-1" onclick="editDetail('${v.id}')">
                    <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                </button>
                
                <button class="btn btn-danger posting-spp mr-1" onclick="deleteDetail('${v.id}')">
                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                </button>
                `
                        ));
                    <?php endif; ?>
                <?php else : ?>
                    newRow.append($('<td>').html(
                        `
                <button class="btn btn-warning posting-spp mr-1" onclick="editDetail('${v.id}')">
                    <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                </button>
                
                <button class="btn btn-danger posting-spp mr-1" onclick="deleteDetail('${v.id}')">
                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                </button>
                `
                    ));
                <?php endif; ?>

                table.find('tbody').append(newRow);
            });
        }

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

    function getListDokumenPabean() {
        // GET LIST STOCK PER DOKUMEN PABEAN
        $.ajax({
            url: `<?= base_url('stock-adjusment/list-stock-dokumen-bc'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                stock_id: $(".spesifikasi_id option:selected").data('stock_id'),
            },
            dataType: "json",
            success: function(res) {
                $(".bc_id").empty()
                $(".bc_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    item.no_aju = (item.no_aju == "-") ? "" : item.no_aju;
                    $(".bc_id").append(`<option data-stock="${item.stok_total}" data-bc_id="${item.bc_id}" data-no_aju="${item.no_aju}" data-dokumen="${'('+item.bc_type+') '+item.no_aju}"  value="${item.id}">(${item.bc_type}) ${item.no_aju}</option>`)
                })
            }
        });
    }

    function getListBarang() {
        // GET LIST BARANG
        $.ajax({
            url: `<?= base_url('stock-adjusment/list-barang-stock-init'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                type_barang: $(".type_barang option:selected").val(),
                divisi_id: $(".divisi_id option:selected").val(),
                warehouse_id: $(".warehouse_id option:selected").val()
            },
            dataType: "json",
            success: function(res) {
                $(".spesifikasi_id").empty()
                $(".spesifikasi_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".spesifikasi_id").append(`<option data-stock_id="${item.stock_id}" data-kode_barang="${item.kode_barang}" data-barang="${item.barang}" data-kode_satuan="${item.kode_satuan}" value="${item.spesifikasi_id}">(${item.kode_barang}) ${item.barang}</option>`)
                })
                $(".spesifikasi_id").val();
            }
        });
    }

    function handleMinusAdjusment() {
        $.ajax({
            url: `<?= base_url('stock-adjusment/handle-minus-adjusment'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                stock_id: $(".spesifikasi_id option:selected").data('stock_id'),
                qty: $(".qty").val(),
                bc_id: $(".bc_id option:selected").data('bc_id'),
                no_aju: $(".bc_id option:selected").data('no_aju'),
                operasi: $('#operasi').val(),
            },
            dataType: "json",
            success: function(res) {
                if (res.status) {
                    var id_detail = $('#id_detail').val();
                    var id = $('#bc_id option:selected').val();

                    if (id_detail) {
                        // UPDATE
                        updateList(id_detail);
                    } else {
                        // INSERT
                        var is_add = false;
                        for (var i = 0; i < listStock.length; i++) {
                            if (listStock[i].id == id) {
                                item = listStock[i];
                                is_add = true;
                                break;
                            }
                        }

                        if (is_add) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Barang sudah ada',
                                confirmButtonColor: '#4e73df',
                                cancelButtonColor: '#d33',
                                reverseButtons: true,
                                confirmButtonText: 'Oke',
                            })
                        } else {
                            insertList();
                            resetFormDetail()
                        }
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: res.message,
                        confirmButtonColor: '#4e73df',
                    })
                    $('#qty').val(null).change()
                }
            }
        });
    }

    function resetFormDetail() {
        $('#id_detail').val(null);
        $('#type_barang').val(null).change();
        $('#warehouse_id').val(null).change();
        $('#spesifikasi_id').val(null).change();
        $('#bc_id').val(null).change();
        $('#no_aju').val(null).change();
        $('#operasi').val(null).change();
        $('#qty').val(null);
        $('#stok_warehouse').val(null);
        validatorDetail.resetForm();
    }

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $(".no_adjusment").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("stock-adjusment/get-adjusment-no"); ?>`,
                method: "GET",
                data: {
                    divisi_id: $('#divisi_id option:selected').val()
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".no_adjusment").val(res?.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".no_adjusment").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".no_adjusment").val("");
                    }
                }
            })
        } else {
            $(".no_adjusment").attr("readonly", false);
            $(".no_adjusment").val("");
        }
    }
</script>



<?= $this->endSection(); ?>