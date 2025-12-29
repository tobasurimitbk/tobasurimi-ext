<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Tambah Stok Inisiasi</h1>
        <?php if (can("Inventori", "Stok List", "c")) : ?>
            <div class="col-button-tambah-spp">
                <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("stock-list"); ?>">
                    Kembali
                </a>
                <a class="btn btn-hide-form btn-discard float-right" style="background-color: #628a2dff !important; color:white !important; border:none !important;" href="<?= base_url("stock-list/import"); ?>">
                    Import Stok Awal
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
                <input type="hidden" class="id_detail" name="id_detail" id="id_detail">
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
                            <label for="floatingInput" style="z-index: 1;">Cari Barang / Kode Barang</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select disabled class="form-select satuan_id" name="satuan_id" id="satuan_id">
                                <option value=""></option>
                                <?php foreach ($satuan as $s): ?>
                                    <option value="<?= $s['id'] ?>">
                                        <?= $s['kode_satuan'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Satuan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control tanggal" id="tanggal" name="tanggal" placeholder="Tanggal">
                                <label for="floatingInput">Tanggal Stok</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input oninput="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control qty_inisiasi" id="qty_inisiasi" name="qty_inisiasi" placeholder="Qty Inisiasi">
                                <label for="floatingInput">Qty Inisiasi</label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Inisiasi Stok</label>
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
                                <th>Tipe Barang</th>
                                <th>Dept</th>
                                <th>Warehouse</th>
                                <th>Kode</th>
                                <th>Barang</th>
                                <th>Spesifikasi</th>
                                <th>Qty</th>
                                <th>Satuan</th>
                                <th>Tgl Stok</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot class="foot-detail-table" id="foot-detail-table">
                            <tr>
                                <td style="text-align: left;" colspan="11">
                                    Tidak ada data
                                </td>
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
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    $('#type_barang').select2({
        placeholder: "Pilih Tipe Barang",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        dropdownDivisi();
    });

    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    });

    $("#tanggal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#spesifikasi_id').select2({
        placeholder: "Cari Kode / Nama Barang",
        theme: "bootstrap-5",
        allowClear: true,
        ajax: {
            url: '<?= base_url('stock-list/get-barang-not-init'); ?>',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    warehouse_id: $("#warehouse_id option:selected").val(),
                    divisi_id: $("#divisi_id option:selected").val(),
                    type_barang: $("#type_barang option:selected").val()
                };
            },
            processResults: function(data) {
                // Pastikan server mengembalikan data dengan struktur yang lengkap
                return {
                    results: $.map(data.results, function(item) {
                        return {
                            id: item.id,
                            text: item.text,
                            satuan_1: item.satuan_1,
                            kode_barang: item.kode_barang,
                            barang_name: item.barang_name,
                            spesifikasi: item.spesifikasi
                        };
                    })
                };
            },
            cache: false
        },
        minimumInputLength: 1
    });

    $('#spesifikasi_id').on('select2:select', function(e) {
        e.preventDefault();
        var data = e.params.data;
        var selectedOption = $(this).find('option:selected');
        selectedOption.data('satuan_1', data.satuan_1);
        selectedOption.data('kode_barang', data.kode_barang);
        selectedOption.data('barang_name', data.barang_name);
        selectedOption.data('spesifikasi', data.spesifikasi);

        // Trigger change event manual
        $(this).trigger('change');
    });

    $('#spesifikasi_id').change(function(e) {
        e.preventDefault();
        var satuan_id = $('#spesifikasi_id option:selected').data('satuan_1');
        $('#satuan_id').val(satuan_id).change();
    });

    $('#satuan_id').select2({
        placeholder: "Pilih Satuan",
        theme: "bootstrap-5",
        allowClear: true
    });


    $("#type_barang,#divisi_id,#warehouse_id,#spesifikasi_id,#satuan_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    // VALIDATOR DETAIL
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
            satuan_id: {
                required: true
            },
            tanggal: {
                required: true,

            },
            qty_inisiasi: {
                required: true,

            },
        },
        messages: {
            type_barang: {
                required: "Tipe barang wajib diisi"
            },
            divisi_id: {
                required: "Departemen wajib diisi"
            },
            warehouse_id: {
                required: "Warehouse wajib diisi"
            },
            spesifikasi_id: {
                required: "Spesifikasi wajib diisi"
            },
            satuan_id: {
                required: "Satuan wajib diisi"
            },
            tanggal: {
                required: "Tanggal wajib diisi"
            },
            qty_inisiasi: {
                required: "Qty wajib diisi",
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
            Swal.fire({
                icon: 'question',
                title: 'Inisiasi Stok ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    var data = new FormData();
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
                                    window.location.href = "<?= base_url("stock-list"); ?>";
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
    });

    $('.btn-submit-detail').click(function() {
        if ($('.create-form').valid()) {
            var id_detail = $('#id_detail').val();
            var type_barang = $('#type_barang option:selected').val();
            var type_barang_txt = $('#type_barang option:selected').text();
            var divisi_id = $('#divisi_id option:selected').val();
            var divisi = $('#divisi_id option:selected').text();
            var warehouse_id = $('#warehouse_id option:selected').val();
            var warehouse = $('#warehouse_id option:selected').text();
            var spesifikasi_id = $('#spesifikasi_id option:selected').val();
            var kode_barang = $('#spesifikasi_id option:selected').data('kode_barang');
            var barang_name = $('#spesifikasi_id option:selected').data('barang_name');
            var spesifikasi = $('#spesifikasi_id option:selected').data('spesifikasi');
            var satuan_id = $('#satuan_id option:selected').val();
            var tanggal = $('#tanggal').val();
            var kode_satuan = $('#satuan_id option:selected').text();
            var qty_inisiasi = destroyFormatRupiah($('#qty_inisiasi').val());

            if (id_detail) {
                // UPDATE
                var index = null;
                for (var i = 0; i < listStock.length; i++) {
                    if (listStock[i].id == id) {
                        index = i;
                        break;
                    }
                }

                listStock[index].type_barang = type_barang;
                listStock[index].type_barang_txt = type_barang_txt;
                listStock[index].divisi_id = divisi_id;
                listStock[index].divisi = divisi;
                listStock[index].warehouse_id = warehouse_id;
                listStock[index].spesifikasi_id = spesifikasi_id;
                listStock[index].kode_barang = kode_barang;
                listStock[index].barang_name = barang_name;
                listStock[index].spesifikasi = spesifikasi;
                listStock[index].satuan_id = satuan_id;
                listStock[index].kode_satuan = kode_satuan;
                listStock[index].tanggal = tanggal;
                listStock[index].qty_inisiasi = qty_inisiasi;
            } else {
                // CREATE
                var isAdd = false;
                for (var i = 0; i < listStock.length; i++) {
                    if (
                        listStock[i].divisi_id == divisi_id &&
                        listStock[i].warehouse_id == warehouse_id &&
                        listStock[i].spesifikasi_id == spesifikasi_id
                    ) {
                        isAdd = true;
                        break;
                    }
                }

                if (isAdd) {
                    Swal.fire({
                        icon: 'error',
                        title: "Barang sudah ada",
                        confirmButtonColor: '#4e73df',
                    });
                    return;
                } else {
                    listStock.push({
                        id: getID(),
                        type_barang: type_barang,
                        type_barang_txt: type_barang_txt,
                        divisi_id: divisi_id,
                        divisi: divisi,
                        warehouse_id: warehouse_id,
                        warehouse: warehouse,
                        spesifikasi_id: spesifikasi_id,
                        kode_barang: kode_barang,
                        barang_name: barang_name,
                        spesifikasi: spesifikasi,
                        satuan_id: satuan_id,
                        kode_satuan: kode_satuan,
                        tanggal: tanggal,
                        qty_inisiasi: qty_inisiasi
                    });
                    drawTable(listStock);
                }
            }
            resetFormAfterInsertUpdate();

        }
    })

    function detailRow(id) {
        const item = listStock.find(i => i.id == id);

        if (!item) {
            console.warn(`Item dengan ID ${id} tidak ditemukan`);
            return;
        }


        $('#id_detail').val(item.id);
        $('#type_barang').val(item.type_barang).trigger('change');
        $('#divisi_id').val(item.divisi_id).trigger('change.select2');
        $('#satuan_id').val(item.satuan_id).trigger('change');
        $('#qty_inisiasi').val(item.qty_inisiasi);
        $('#tanggal').val(item.tanggal);

        // Reset dan isi warehouse
        const $warehouse = $("#warehouse_id");
        $warehouse.empty()
            .append('<option value=""></option>')
            .append(`<option selected value="${item.warehouse_id}">${item.warehouse}</option>`)
            .val(item.warehouse_id)
            .trigger('change');

        $('#warehouse_id').val(item.warehouse_id).change();

        // Reset dan isi spesifikasi
        const $spesifikasi = $("#spesifikasi_id");
        $spesifikasi.empty()
            .append('<option value=""></option>')
            .append(`
            <option 
                data-satuan_1="${item.satuan_id}"
                data-kode_barang="${item.kode_barang}" 
                data-barang_name="${item.barang_name}" 
                data-spesifikasi="${item.spesifikasi}" 
                selected 
                value="${item.spesifikasi_id}">
                (${item.kode_barang}) ${item.barang_name} - ${item.spesifikasi}
            </option>
        `)
            .val(item.spesifikasi_id)
            .trigger('change');
        $('#spesifikasi_id').val(item.spesifikasi_id).change();

    }


    function resetFormDetail() {
        $('#id_detail').val(null);
        $('#type_barang').val(null).change();
        $('#divisi_id').val(null).change();
        $('#warehouse_id').val(null).change();
        $('#spesifikasi_id').val(null).change();
        $('#satuan_id').val(null).change();
        $('#tanggal').val(null).change();
        $('#qty_inisiasi').val(null).change();
    }

    function resetFormAfterInsertUpdate() {
        $('#id_detail').val(null);
        $('#spesifikasi_id').val(null).change();
        $('#satuan_id').val(null).change();
        $('#qty_inisiasi').val(null).change();
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
        table.find('tbody').empty();
        table.find('tfoot').empty();
        var no = 1;
        if (listStock.length == 0) {
            var newRow = $('<tr>');
            newRow.append($('<td  colspan="10">').text("Tidak Ada Data"));
            table.find('tfoot').append(newRow);
        } else {
            $.each(listStock, function(i, v) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td>').text(no++));
                newRow.append($('<td>').text(v.type_barang_txt));
                newRow.append($('<td>').text(v.divisi));
                newRow.append($('<td>').text(v.warehouse));
                newRow.append($('<td>').text(v.kode_barang));
                newRow.append($('<td>').text(v.barang_name));
                newRow.append($('<td>').text(v.spesifikasi));
                newRow.append($('<td>').text(greatFormatRupiah(v.qty_inisiasi)));
                newRow.append($('<td>').text(v.kode_satuan));
                newRow.append($('<td>').text(v.tanggal));
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
            });
        }

    }

    function dropdownDivisi() {
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