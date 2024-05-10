<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($penerimaanMutasi) ? "Tambah Penerimaan Mutasi" : "Update Penerimaan Mutasi" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("penerimaan-mutasi"); ?>">
                Batal
            </a>
            <?php if (!empty($penerimaanMutasi)) : ?>
                <?php if ($penerimaanMutasi['status_posting'] == "0") : ?>
                    <?php if (can('Inventori', 'Penerimaan Mutasi', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="remove('<?= encrypt($penerimaanMutasi['id']); ?>')">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Inventori', 'Penerimaan Mutasi', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="posting('<?= encrypt($penerimaanMutasi['id']); ?>')">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Inventori', 'Penerimaan Mutasi', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("penerimaan-mutasi/print/"); ?><?= encrypt($penerimaanMutasi['id']); ?>')">
                            Print
                        </button>
                    <?php endif; ?>
                    <?php if (can('Inventori', 'Penerimaan Mutasi', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>

                <?php else : ?>
                    <?php if (can('Inventori', 'Penerimaan Mutasi', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("penerimaan-mutasi/print/"); ?><?= encrypt($penerimaanMutasi['id']); ?>')">
                            Print
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
                    <label class="form-label font-weight-bold lable-title">Data Penerimaan Mutasi</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id" value="<?= !empty($penerimaanMutasi) ? encrypt($penerimaanMutasi['id']) : '' ?>" class="id">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" disabled class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime(!empty($penerimaanMutasi) ? $penerimaanMutasi['tanggal'] : $tanggal)); ?>">
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" <?= !empty($penerimaanMutasi) ? 'disabled=true' : ''; ?> value="<?= !empty($penerimaanMutasi) ? $penerimaanMutasi['penerimaan_mutasi_no'] : "PMU//" . date('m') . "/1/" . date('Y'); ?>" type="text" class="form-control penerimaan_mutasi_no" id="penerimaan_mutasi_no" name="penerimaan_mutasi_no" placeholder="No. Adjusment">
                                    <label for="floatingInput">No. Penerimaan Mutasi</label>
                                </div>
                                <div style="<?= !empty($penerimaanMutasi) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($penerimaanMutasi) ? 'disabled'  : '' ?> class="form-select jenis_mutasi" id="jenis_mutasi" name="jenis_mutasi" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($jenisMutasi as $j) : ?>
                                    <option <?= !empty($penerimaanMutasi) ? ($penerimaanMutasi['jenis_mutasi'] == $j['value'] ? 'selected' : '') : '' ?> value="<?= $j['value'] ?>">
                                        <?= strtoupper($j['description']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Jenis Mutasi</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating" style="height: 50px;">
                            <select <?= !empty($penerimaanMutasi) ?  'disabled'  : '' ?> class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($warehouse)) : ?>
                                    <option selected value="<?= $warehouse['id'] ?>">(<?= strtoupper($warehouse['divisi']) ?>) <?= strtoupper($warehouse['warehouse_name']) ?></option>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen - Warehouse Tujuan</label>
                        </div>
                        <small class="mb-3"><i>Hanya menampilkan penerimaan barang yang belum lengkap</i></small>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($penerimaanMutasi) ?  'disabled=true' : ''; ?> multiple class="form-select multiple_mutasi_id" name="multiple_mutasi_id[]" id="multiple_mutasi_id[]">
                                <option value=""></option>
                                <?php if (!empty($penerimaanMutasi)) : ?>
                                    <?php foreach (json_decode($penerimaanMutasi['multiple_mutasi_id']) as $i => $p) : ?>
                                        <option selected value="<?= $p ?>"><?= json_decode($penerimaanMutasi['multiple_no_mutasi'])[$i] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">No. Penerimaan Mutasi</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($penerimaanMutasi) ? ($penerimaanMutasi['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Keterangan" value="<?= !empty($penerimaanMutasi) ? $penerimaanMutasi['keterangan'] : '' ?>" class="form-control keterangan" id="keterangan" name="keterangan" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Keterangan</label>
                        </div>
                    </div>
                </div>

            </form>
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Barang <?= !empty($penerimaanMutasi) ? "(Hanya Menampilkan Barang berdasarkan data yang sudah disimpan sebelumnya)" : "(Hanya Menampilkan Barang yang Belum Diterima Full)" ?></label>
                    </div>
                </div>
            </div>
            <div class="row mt-3">

                <div class="col-md-12 col-table-button-tts">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">No Mutasi</th>
                                    <th style="text-align: center;">Tipe Barang</th>
                                    <th style="text-align: center;">Dokumen Pabean</th>
                                    <th style="text-align: center;">No Aju</th>
                                    <th style="text-align: center;">Tgl Penerimaan</th>
                                    <th style="text-align: center;">Barang - Spesifikasi</th>
                                    <th style="text-align: center;">Satuan</th>
                                    <th style="text-align: center;">Qty Mutasi</th>
                                    <th style="text-align: center;">Qty Diterima Total</th>
                                    <th style="text-align: center;">Qty Diterima Sekarang</th>
                                    <th style="text-align: center;">Qty Sisa</th>
                                </tr>
                            </thead>
                            <tbody class="body-table">
                            </tbody>
                            <tfoot class="foot-detail-table" id="foot-detail-table">
                                <tr>
                                    <td colspan="12" style="text-align: center;">
                                        Tidak Ada Barang
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>


        </div>

    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    var listBarang = [];

    $('#jenis_mutasi').select2({
        placeholder: "Pilih Jenis Mutasi",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        getListWarehouse();
        getListMutasi();
        listBarang = [];
        drawTable(listBarang);
    });

    $('#warehouse_id').select2({
        placeholder: "Pilih Departemen - Warehouse Tujuan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        getListMutasi();
        listBarang = [];
        drawTable(listBarang);
        changeStatus();

    });

    $('.multiple_mutasi_id').select2({
        placeholder: "Pilih Nomor Mutasi",
        theme: "bootstrap-5",
        allowClear: false,
    }).change(function() {
        let arr = $('.multiple_mutasi_id').val();
        $.ajax({
            url: `<?= base_url("penerimaan-mutasi/list-barang"); ?>`,
            method: "GET",
            data: {
                mutasi_id: JSON.stringify(arr),
                penerimaan_mutasi_id: $('.id').val()
            },
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            dataType: "json",
            success: function(res) {
                listBarang = [];
                listBarang = res.data;
                drawTable(listBarang);
            }
        })
    });

    $("#jenis_mutasi,#warehouse_id,.multiple_mutasi_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');


    // VALIDATOR
    var validator = $(".create-form").validate({
        rules: {
            penerimaan_mutasi_no: {
                required: true
            },
            jenis_mutasi: {
                required: true
            },
            warehouse_id: {
                required: true
            },
            keterangan: {
                required: true
            },
        },
        messages: {
            penerimaan_mutasi_no: {
                required: "No penerimaan wajib diisi"
            },
            jenis_mutasi: {
                required: "Jenis mutasi wajib diisi"
            },
            warehouse_id: {
                required: "Warehouse wajib diisi"
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

    $('.btn-submit-parent').click(function() {
        if (listBarang.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Barang yang akan dimutasi tidak boleh kosong !',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        } else {
            if ($('.create-form').valid()) {
                var isValid = true;
                var dataError = null;

                $.each(listBarang, function(i, v) {
                    var element = $('input[data-id="' + v.mutasi_detail_id + '"].stok-mutasi');
                    var input_user = parseFloat(element.val());
                    var qty_sisa = parseFloat(element.data('qty_sisa'));

                    if (input_user > qty_sisa || isNaN(input_user) || input_user == undefined) {
                        dataError = listBarang[i];
                        isValid = false;
                    } else {
                        listBarang[i].qty_diterima_current = input_user;
                    }
                });

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Stok mutasi barang ' + dataError.barang + ' dengan dokumen ' + dataError.bc_name + ' / ' + dataError.no_aju + ' tidak valid!',
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
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
                            let id = $('#id').val();
                            let data = new FormData(document.querySelector(".create-form"));
                            data.append('listBarang', JSON.stringify(listBarang));
                            if (id) {
                                // UPDATE
                                $.ajax({
                                    url: "<?= base_url("penerimaan-mutasi/update"); ?>",
                                    data: data,
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                        setLoading();
                                    },
                                    complete: function() {
                                        stopLoading()
                                    },
                                    method: "POST",
                                    dataType: "json",
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        if (response.status) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                                confirmButtonText: 'Ok'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    location.reload();
                                                }
                                            });
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                                confirmButtonText: 'Ok'
                                            });
                                        }

                                    },
                                });
                            } else {
                                // INSERT
                                $.ajax({
                                    url: "<?= base_url("penerimaan-mutasi/save"); ?>",
                                    data: data,
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                        setLoading();
                                    },
                                    complete: function() {
                                        stopLoading()
                                    },
                                    method: "POST",
                                    dataType: "json",
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        if (response.status) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                                confirmButtonText: 'Ok'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = "<?= base_url('penerimaan-mutasi/id/') ?>" + response.id
                                                }
                                            });
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                                confirmButtonText: 'Ok'
                                            });
                                        }

                                    },
                                });
                            }
                        }
                    });
                }
            }
        }
    });


    function getListBarang() {
        $.ajax({
            url: `<?= base_url('penerimaan-mutasi/list-barang'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                jenis_mutasi: $(".jenis_mutasi option:selected").val(),

            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_id").empty()
                $(".warehouse_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_id").append(`<option value="${item.id}">(${item.divisi.toUpperCase()}) ${item.warehouse_name.toUpperCase()}</option>`)
                })
                $(".warehouse_id").val();

            }
        });
    }

    function getListMutasi() {
        $.ajax({
            url: `<?= base_url('penerimaan-mutasi/list-mutasi'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                warehouse_id: $(".warehouse_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".multiple_mutasi_id").empty()
                $(".multiple_mutasi_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".multiple_mutasi_id").append(`<option value="${item.id}">${item.no_mutasi}</option>`)
                })
                $(".multiple_mutasi_id").val();

            }
        });
    }

    function getListWarehouse() {
        $.ajax({
            url: `<?= base_url('penerimaan-mutasi/list-warehouse'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                jenis_mutasi: $(".jenis_mutasi option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_id").empty()
                $(".warehouse_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_id").append(`<option value="${item.id}">(${item.divisi.toUpperCase()}) ${item.warehouse_name.toUpperCase()}</option>`)
                })
                $(".warehouse_id").val();

            }
        });
    }

    function drawTable(listBarang) {
        const table = $('#dataTable');
        table.find('tbody').empty();
        table.find('tfoot').empty();

        if (listBarang.length == 0) {
            var newRow = $('<tr>');
            newRow.append($('<td colspan="12" style="text-align:center">Tidak Ada Barang</td>'));
            table.find('tfoot').append(newRow);
        } else {
            var no = 1;
            $.each(listBarang, function(i, v) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td>').text(no++));
                newRow.append($('<td>').text(v.no_mutasi));
                newRow.append($('<td>').text(v.tipe_barang));
                newRow.append($('<td>').text(v.bc_name));
                newRow.append($('<td>').text(v.no_aju));
                newRow.append($('<td>').text(v.stock_date));
                newRow.append($('<td>').text(v.barang));
                newRow.append($('<td>').text(v.satuan));
                newRow.append($('<td>').text(v.qty));
                newRow.append($('<td>').text(v.qty_diterima_all));
                newRow.append($('<td>').html(
                    `
                        <input <?= !empty($penerimaanMutasi) ? (($penerimaanMutasi['status_posting'] == "1") ? 'disabled' : '') : '' ?> class="form-control stok-mutasi" style="height: 40px; padding-bottom: 10px;" oninput="preventNegativeInput(this)" autocomplete="one-time-code" data-id="${v.mutasi_detail_id}" data-qty_sisa="${v.qty_sisa}" class="form-control" type="text" value="${v.qty_diterima_current}">
                    `
                ));
                newRow.append($('<td>').text(v.qty_sisa));
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

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $(".penerimaan_mutasi_no").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("penerimaan-mutasi/get-penerimaan-mutasi-no"); ?>`,
                method: "GET",
                data: {
                    warehouse_id: $('#warehouse_id option:selected').val()
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".penerimaan_mutasi_no").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".penerimaan_mutasi_no").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".penerimaan_mutasi_no").val("");
                    }
                }
            })
        } else {
            $(".penerimaan_mutasi_no").attr("readonly", false);
            $(".penerimaan_mutasi_no").val("");
        }
    }

    <?php if (!empty($penerimaanMutasi)) : ?>
        let arr = $('.multiple_mutasi_id').val();
        $.ajax({
            url: `<?= base_url("penerimaan-mutasi/list-barang"); ?>`,
            method: "GET",
            data: {
                mutasi_id: JSON.stringify(arr),
                penerimaan_mutasi_id: $('.id').val()
            },
            dataType: "json",
            success: function(res) {
                listBarang = [];
                listBarang = res.data;
                drawTable(listBarang);
            }
        })
    <?php endif; ?>
    const print = function(url) {
        window.open(url, "_blank");
    }

    const posting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Penerimaan Mutasi ?',
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
                    url: "<?= base_url("penerimaan-mutasi/posting"); ?>",
                    data: {
                        id: id
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
                                location.reload()
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
    }

    const remove = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Penerimaan Mutasi ?',
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
                    url: "<?= base_url("penerimaan-mutasi/delete"); ?>",
                    data: {
                        id: id
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
                                location.reload();
                            });
                        }
                    },
                });
            }
        })
    }
</script>


<?= $this->endSection(); ?>