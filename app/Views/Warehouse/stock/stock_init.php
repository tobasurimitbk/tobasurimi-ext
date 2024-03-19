<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Tambah Stok Inisiasi</h1>
        <?php if (can("Inventori", "Stok List", "c")) : ?>
            <div class="col-button-tambah-spp">
                <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("stock-list"); ?>">
                    Batal
                </a>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            </div>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Barang</label>
                </div>
            </div>
            <form class="create-form">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" disabled class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime($tanggal)); ?>">
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
                            <select class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">
                                <option value=""></option>

                            </select>
                            <label for="floatingInput" style="z-index: 1;">Warehouse</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select spesifikasi_id" id="spesifikasi_id" name="spesifikasi_id" aria-label="Floating label select example">
                                <option value=""></option>

                            </select>
                            <label for="floatingInput" style="z-index: 1;">Nama Barang</label>
                        </div>
                    </div>
                    <!-- <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input placeholder="" class="form-control spesifikasi_name" disabled id="spesifikasi_name" name="spesifikasi_name" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Spesifikasi</label>
                        </div>
                    </div> -->
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input placeholder="" class="form-control kode_barang" disabled id="kode_barang" name="kode_barang" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Kode Barang</label>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input placeholder="" class="form-control nama_satuan" disabled id="nama_satuan" name="nama_satuan" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Satuan</label>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Stok</label>
                </div>
            </div>
            <form class="detail-form">
                <input type="hidden" name="id_detail" class="id_detail" id="id_detail">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select bc_id" id="bc_id" name="bc_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <option value="0">NON PABEAN</option>
                                <?php foreach ($jenisDokAju as $j) : ?>
                                    <option value="<?= $j->id ?>">
                                        <?= $j->value ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                            <label for="floatingInput" style="z-index: 1;">Dokumen Pabean</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating" style="height: 50px;">
                            <input class="form-control no_aju" id="no_aju" placeholder="" name="no_aju" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Nomor Aju</label>
                        </div>
                        <small class="mb-3"><i>Contoh Nomor Aju : 000023-017189-20240212-000012</i></small>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input placeholder="Qty" class="form-control qty" id="qty" name="qty" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Qty</label>
                        </div>
                    </div>
                </div>
            </form>

            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Stok</label>
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
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Barang</th>
                                <th>Dokumen Pabean</th>
                                <th>No Aju</th>
                                <th>QTY</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot class="foot-detail-table" id="foot-detail-table">
                            <tr>
                                <td style="text-align: right;" colspan="2"></td>
                                <td><b></b></td>
                                <td><b></b></td>
                                <td><b>TOTAL</b></td>
                                <td><b>0.00</b></td>
                                <td></td>
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

    $('#type_barang').select2({
        placeholder: "Pilih Tipe Barang",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        listStock = [];
        drawTable();
        resetFormDetail();
        getBarangDropdown();
    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // GET WAREHOUSES
        $.ajax({
            url: `<?= base_url('stock-list/warehouse'); ?>`,
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
        // RESET LIST 
        listStock = [];
        drawTable();
        resetFormDetail();
        getBarangDropdown();
    });



    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        listStock = [];
        drawTable();
        resetFormDetail();
        getBarangDropdown();
    });

    $('#spesifikasi_id').select2({
        placeholder: "Pilih Barang",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        $('#spesifikasi_name').val($('#spesifikasi_id option:selected').data('spesifikasi_name'));
        $('#kode_barang').val($('#spesifikasi_id option:selected').data('kode_barang'));
        $('#nama_satuan').val($('#spesifikasi_id option:selected').data('nama_satuan'));
        listStock = [];
        drawTable();
        resetFormDetail();
    });

    $('#bc_id').select2({
        placeholder: "Pilih Dokumen Pabean",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        var bc_id = $(this).val();
        if (bc_id == '0') {
            $('#no_aju').val(null);
            $('#no_aju').attr('disabled', true);
        } else {
            $('#no_aju').attr('disabled', false);
        }
    });


    $("#type_barang,#divisi_id,#warehouse_id,#spesifikasi_id,#bc_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    // VALIDATOR DETAIL
    var validatorDetail = $(".detail-form").validate({
        rules: {
            bc_id: {
                required: true
            },
            qty: {
                required: true,
                number: true,
                min: -1
            },
        },
        messages: {
            bc_id: {
                required: "Pilih dokumen pabean"
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

    // VALIDATOR
    var validator = $(".create-form").validate({
        rules: {
            type_barang: {
                required: true
            },
            divisi_id: {
                required: true
            },
            warehouse_id: {
                required: true
            },
            spesifikasi_id: {
                required: true
            },
        },
        messages: {
            type_barang: {
                required: "Pilih tipe barang"
            },
            divisi_id: {
                required: "Pilih departemen"
            },
            warehouse_id: {
                required: "Pilih warehouse"
            },
            spesifikasi_id: {
                required: "Pilih barang"
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
        if (listStock.length == 0) {
            Swal.fire({
                icon: 'error',
                title: "Stok masih kosong",
                confirmButtonColor: '#4e73df',
            })
        } else {
            if ($('.create-form').valid()) {
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
                        var data = new FormData(document.querySelector(".create-form"));
                        data.append("qty_total", qtyTotal);
                        data.append("list_stock", JSON.stringify(listStock));

                        $.ajax({
                            url: "<?= base_url("stock-list/create"); ?>",
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
                                        location.reload();
                                    })
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
                })
            }
        }
    });

    $('.btn-submit-detail').click(function() {
        var spesifikasi_id = $('#spesifikasi_id').val();
        if (spesifikasi_id.length == 0) {
            Swal.fire({
                icon: 'error',
                title: "Barang wajib diisi",
                confirmButtonColor: '#4e73df',
            })
        } else {

            if ($('.detail-form').valid()) {
                if (validateNoAju() == false) {
                    Swal.fire({
                        icon: 'error',
                        title: "Nomor aju tidak valid",
                        confirmButtonColor: '#4e73df',
                    })
                } else {
                    var bc_id = $('.bc_id option:selected').val();
                    var no_aju = $('.no_aju').val();
                    var id_detail = $('.id_detail').val();

                    if (id_detail) {
                        // UPDATE
                        updateList(id_detail);
                    } else {
                        // CREATE
                        var isAdd = false;
                        for (var i = 0; i < listStock.length; i++) {

                            if (listStock[i].bc_id == '0' && listStock[i].no_aju == '-' && bc_id == '0') {
                                isAdd = true;
                                break;
                            }

                            if (listStock[i].bc_id == bc_id && listStock[i].no_aju == no_aju) {
                                isAdd = true;
                                break;
                            }
                        }

                        if (isAdd) {
                            Swal.fire({
                                icon: 'error',
                                title: "Stok sudah ada",
                                confirmButtonColor: '#4e73df',
                            })
                        } else {
                            insertList();
                        }

                    }
                }

            }
        }

    })

    function updateList(id) {
        var item = null;
        for (var i = 0; i < listStock.length; i++) {
            if (listStock[i].id == id) {
                item = listStock[i];
                listStock[i].bc_id = $('.bc_id').val();
                listStock[i].no_aju = $('.no_aju').val() || "-";
                listStock[i].qty = $('.qty').val();
                listStock[i].kode_barang = $('.kode_barang').val();
                listStock[i].barang = $('.spesifikasi_id option:selected').data('barang');
                listStock[i].dokumen_pabean = $('.bc_id option:selected').text().trim();
                break;
            }
        }
        console.log(item, id);
        resetFormDetail();
        drawTable(listStock);
    }

    function detailRow(id) {
        var item = null;
        for (var i = 0; i < listStock.length; i++) {
            if (listStock[i].id == id) {
                item = listStock[i];
                break;
            }
        }

        $('.id_detail').val(item.id);
        $('.bc_id').val(item.bc_id).change();
        $('.no_aju').val(item.no_aju);
        $('.qty').val(item.qty);

    }

    function insertList() {
        listStock.push({
            id: getID(),
            bc_id: $('.bc_id').val(),
            no_aju: $('.no_aju').val() || "-",
            qty: $('.qty').val(),
            kode_barang: $('.kode_barang').val(),
            barang: $('.spesifikasi_id option:selected').data('barang'),
            dokumen_pabean: $('.bc_id option:selected').text().trim(),
        });
        resetFormDetail();
        drawTable(listStock);
    }

    function resetFormDetail() {
        $('.id_detail').val(null);
        $('.bc_id').val(null).change();
        $('.no_aju').val(null);
        $('.qty').val(null);
    }

    function deleteRow(id) {
        var indexToRemove = -1;
        for (var i = 0; i < listStock.length; i++) {
            if (listStock[i].id == id) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listStock.splice(indexToRemove, 1);
        }
        drawTable(listStock);
        resetFormDetail();
    }

    function drawTable(listStock) {
        const table = $('#dataTable');
        var no = 1;
        qtyTotal = 0;

        table.find('tbody').empty();
        $.each(listStock, function(i, v) {
            var newRow = $('<tr style="color:whitesmoke;">');
            newRow.append($('<td>').text(no++));
            newRow.append($('<td>').text(v.kode_barang));
            newRow.append($('<td>').text(v.barang));
            newRow.append($('<td>').text(v.dokumen_pabean));
            newRow.append($('<td>').text(v.no_aju));
            newRow.append($('<td>').text(v.qty));

            newRow.append($('<td>').html(
                `
                <button class="btn btn-warning posting-spp mr-1" onclick="detailRow('${v.id}')">
                    <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                </button><button class="btn btn-danger" onclick="deleteRow('${v.id}')">
                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                </button>
            `
            ));
            table.find('tbody').append(newRow);
            qtyTotal += parseFloat(v.qty);
        });
        table.find('tfoot').empty();
        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="2"></td>'));
        newRow.append($('<td><b></b></td>'));
        newRow.append($('<td><b></b></td>'));
        newRow.append($('<td><b>TOTAL</b></td>'));
        newRow.append($('<td><b>' + qtyTotal.toFixed(2) + '</b></td>'));
        newRow.append($('<td></td>'));
        table.find('tfoot').append(newRow);
    }

    function validateNoAju() {
        var bc_id = $('.bc_id').val();
        var no_aju = $('.no_aju').val();
        if (bc_id == "0") {
            return true;
        } else {
            var regex = /^\d{6}-\d{6}-\d{8}-\d{6}$/;
            return regex.test(no_aju);
        }
    }

    function getBarangDropdown() {
        $.ajax({
            url: `<?= base_url('stock-list/get-barang-not-init'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                warehouse_id: $(".warehouse_id option:selected").val(),
                divisi_id: $(".divisi_id option:selected").val(),
                type_barang: $(".type_barang option:selected").val()
            },
            dataType: "json",
            success: function(res) {
                $(".spesifikasi_id").empty()
                $(".spesifikasi_id").append(`<option 
                    data-barang_id="" 
                    data-spesifikasi_id=""
                    data-type_barang=""
                    data-barang=""
                    data-spesifikasi_name=""
                    data-kode_barang=""
                    data-nama_satuan=""
                    value=""
                    >
                    </option>`)
                res.data.forEach(function(item) {
                    $(".spesifikasi_id").append(`
                    <option 
                    data-barang_id="${item.barang_id}" 
                    data-spesifikasi_id="${item.spesifikasi_id}"
                    data-type_barang="${item.type_barang}"
                    data-barang="${item.barang}"
                    data-spesifikasi_name="${item.spesifikasi_name}"
                    data-kode_barang="${item.kode_barang}"
                    data-nama_satuan="${item.nama_satuan}"
                    value="${item.spesifikasi_id}">
                    ${item.barang}
                    </option>`)
                })
                $(".spesifikasi_id").val();
            }
        });
    }

    function getID() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let randomString = '';

        for (let i = 0; i < 10; i++) {
            randomString += characters.charAt(Math.floor(Math.random() * characters.length));
        }

        return randomString;
    };
</script>

<?= $this->endSection(); ?>