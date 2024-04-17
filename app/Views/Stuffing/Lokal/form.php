<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($stuffingLokal) ? "Tambah Pengeluaran Lokal" : "Update Pengeluaran Lokal" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("pengeluaran-lokal"); ?>">
                Batal
            </a>
            <?php if (!empty($stuffingLokal)) : ?>
                <?php if ($stuffingLokal['status_posting'] == "0") : ?>
                    <?php if (can('Stuffing', 'Pengeluaran Lokal', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="remove('<?= encrypt($stuffingLokal['id']); ?>')">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Stuffing', 'Pengeluaran Lokal', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="posting('<?= encrypt($stuffingLokal['id']); ?>')">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Stuffing', 'Pengeluaran Lokal', 'p')) : ?>
                        <!-- <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("pengeluaran-lokal/print/"); ?><?= encrypt($stuffingLokal['id']); ?>')">
                            Print
                        </button> -->
                    <?php endif; ?>
                    <?php if (can('Stuffing', 'Pengeluaran Lokal', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if (can('Stuffing', 'Pengeluaran Lokal', 'p')) : ?>
                        <!-- <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("pengeluaran-lokal/print/"); ?><?= encrypt($stuffingLokal['id']); ?>')">
                            Print
                        </button> -->
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
                    <label class="form-label font-weight-bold lable-title">Data Order Form</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id" value="<?= !empty($stuffingLokal) ? encrypt($stuffingLokal['id']) : '' ?>" class="id">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" disabled class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime(!empty($stuffingLokal) ? $stuffingLokal['tanggal'] : $tanggal)); ?>">
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" <?= !empty($stuffingLokal) ? 'disabled=true' : ''; ?> value="<?= !empty($stuffingLokal) ? $stuffingLokal['no_stuffing'] : ""; ?>" type="text" class="form-control no_stuffing" id="no_stuffing" name="no_stuffing" placeholder="No. Stuffing Lokal">
                                    <label for="floatingInput">No. Stuffing Lokal</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($stuffingLokal) ? ($stuffingLokal['status_posting'] ? 'disabled' : 'disabled') : '' ?> class="form-select sales_order_id" id="sales_order_id" name="sales_order_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($orderForm as $v) : ?>
                                    <option <?= !empty($stuffingLokal) ? ($stuffingLokal['sales_order_id'] == $v['id'] ? 'selected' : '') : '' ?> value="<?= $v['id'] ?>" data-id_customer="<?= $v['id_customer'] ?>" data-name_customer="<?= $v['customer_name'] ?>">
                                        <?= strtoupper($v['no_sales_order']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Sales Order</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="hidden" value="<?= !empty($stuffingLokal) ? $stuffingLokal['customer_id'] : '' ?>" class="form-control customer_id" id="customer_id" name="customer_id" aria-label="Floating label select example" />
                            <input type="text" <?= !empty($stuffingLokal) ? ($stuffingLokal['status_posting'] ? 'readonly' : 'readonly') : 'readonly' ?> placeholder="Nama Customer" value="<?= !empty($stuffingLokal) ? $stuffingLokal['customer_name'] : '' ?>" class="form-control customer_name" id="customer_name" name="customer_name" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Nama Customer</label>
                        </div>
                    </div>
                </div>
            </form>

            <div class="detail-form-layout">
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Data Barang</label>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTableSalesOrder" id="dataTableSalesOrder" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col mb-0">
                        <label class="form-label font-weight-bold lable-title">List Inventori Barang</label>
                    </div>
                    <form class="detail-form">
                        <input type="hidden" name="id_detail" class="id_detail" id="id_detail">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select <?= !empty($stuffingLokal) ? ($stuffingLokal['status_posting'] ? 'disabled' : '') : '' ?> class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
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
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select <?= !empty($stuffingLokal) ? ($stuffingLokal['status_posting'] ? 'disabled' : '') : '' ?> class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">
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
                        </div>
                        <div class="row">
                            <div class="col-md-6">
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
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select spesifikasi_id" id="spesifikasi_id" name="spesifikasi_id" aria-label="Floating label select example">
                                        <option value=""></option>

                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Barang - Spesifikasi (Dari Warehouse)</label>
                                </div>
                            </div>
                            <!-- <div class="col-md-4">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select spesifikasi_in_id" id="spesifikasi_in_id" name="spesifikasi_in_id" aria-label="Floating label select example">
                                        <option value=""></option>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Barang - Spesifikasi (Dari Order Form)</label>
                                </div>
                            </div> -->
                        </div>
                    </form>
                    <div class="col-md-12 col-table-button-tts">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: center;">Tipe Barang</th>
                                        <th style="text-align: center;">Dokumen Pabean</th>
                                        <th style="text-align: center;">No Aju</th>
                                        <th style="text-align: center;">Tanggal Penerimaan</th>
                                        <th style="text-align: center;">Barang - Spesifikasi</th>
                                        <th style="text-align: center;">Satuan</th>
                                        <th style="text-align: center;">Qty</th>
                                    </tr>
                                </thead>
                                <tbody class="body-table">
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary" id="select-item-btn">Pilih</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Daftar Barang Yang Akan Dikeluarkan</label>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive" style="margin-top: -10px;">
                        <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts" id="selectedItemTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">Tipe Barang</th>
                                    <th style="text-align: center;">Dokumen Pabean</th>
                                    <th style="text-align: center;">No Aju</th>
                                    <th style="text-align: center;">Tanggal Penerimaan</th>
                                    <th style="text-align: center;">Barang - Spesifikasi</th>
                                    <th style="text-align: center;">Satuan</th>
                                    <th style="text-align: center;">Qty</th>
                                    <th style="text-align: center;">Qty Dikeluarkan</th>
                                    <th style="text-align: center;">Nama Barang Order</th>
                                    <th style="text-align: center;">Qty Barang Order</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="body-table">
                            </tbody>
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

    changeStatus();

    var listStockAsal = [];
    var listStockOrder = [];
    var listStockSelected = [];

    const table = $('.dataTableSalesOrder').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        info: false,
        paging: false,
        fixedHeader: true,
        display: "stripe",
        searching: false,
        ordering: false,
        columns: [{
                data: "no",
                className: "text-center",
            },
            {
                data: "kode_barang",
                className: "text-center"
            },
            {
                data: "nama_barang",
                className: "text-center"
            },
            {
                data: "qty",
                className: "text-center"
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

    var dataTable = $('#dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: false,
        serverSide: false,
        ordering: true,
        order: [],
        fixedHeader: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: true,
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    var selectedItemTable = $('#selectedItemTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: false,
        serverSide: false,
        ordering: true,
        order: [],
        fixedHeader: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    <?php if (!empty($stuffingLokal)) : ?>
        // GET LIST BARANG 
        $.ajax({
            url: `<?= base_url('pengeluaran-lokal/list-barang-stock-init'); ?>`,
            method: "GET",
            beforeSend: function() {},
            complete: function() {},
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
        // APPEND 
        <?php foreach ($stuffingLokalDetail as $m) : ?>
            listStockSelected.push({
                id_stuffing_detail: "<?= $m['id_stuffing_detail'] ?>",
                id: "<?= $m['id'] ?>",
                bc_id: "<?= $m['bc_id'] ?>",
                stock_detail_id: "<?= $m['stock_detail_id'] ?>",
                no_aju: "<?= $m['no_aju'] ?>",
                stock_id: "<?= $m['stock_id'] ?>",
                stok_total: "<?= $m['stok_total'] ?>",
                bc_type: "<?= $m['bc_type'] ?>",
                satuan: "<?= $m['satuan'] ?>",
                barang: "<?= $m['barang'] ?>",
                type_barang: "<?= $m['type_barang'] ?>",
                type_barang_text: "<?= $m['type_barang_text'] ?>",
                stock_date: "<?= $m['stock_date'] ?>",
                qty: "<?= $m['qty'] ?>",
                divisi_id: "<?= $m['divisi_id'] ?>",
                warehouse_id: "<?= $m['warehouse_id'] ?>",
                output: {
                    id_barang: "<?= $m['output']['id_barang'] ?>",
                    barang: "<?= $m['output']['barang'] ?>",
                    qty: "<?= $m['output']['qty'] ?>"
                }
            });
        <?php endforeach; ?>
        drawTableSelectedItem(listStockSelected);
        // <?php if ($stuffingLokal['status_posting']) : ?>
        //     $('.detail-form-layout').hide()
        // <?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($salesOrder)) : ?>
        $.ajax({
            url: `<?= base_url('pengeluaran-lokal/list-barang-output'); ?>`,
            method: "GET",
            data: {
                sales_order_id: $(".sales_order_id option:selected").val()
            },
            dataType: "json",
            success: function(res) {
                listStockOrder = [];
                listStockOrder = res.data;
                drawTableOrderBarang(res.data);
            }
        });
    <?php endif; ?>

    $('#sales_order_id').select2({
        placeholder: "Pilih Sales Order",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        let customer_id = $('#sales_order_id option:selected').data('id_customer');
        let customer_name = $('#sales_order_id option:selected').data('name_customer');
        $('#customer_id').val(customer_id);
        $('#customer_name').val(customer_name);
        getListBarangOutput();
    });

    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        listStockAsal = [];
        // listStockSelected = [];
        drawTableAsalBarang(listStockAsal);
        drawTableSelectedItem(listStockSelected);
        // GET BARANG
        getListBarang();
    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // GET WAREHOUSES
        getListWarehouse();
        listStockAsal = [];
        // listStockSelected = [];
        drawTableAsalBarang(listStockAsal);
        drawTableSelectedItem(listStockSelected);
        // GET BARANG
        getListBarang();
    });

    $('#type_barang').select2({
        placeholder: "Pilih Tipe Barang",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function() {
        // GET LIST BARANG
        getListBarang();
        listStockAsal = [];
        drawTableAsalBarang(listStockAsal);
    });

    $('#spesifikasi_id').select2({
        placeholder: "Pilih Barang - Spesifikasi (Dari Warehouse)",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // LIST DOKUMEN PABEAN
        getListDokumenPabean();
    });


    $('#spesifikasi_in_id').select2({
        placeholder: "Pilih Barang - Spesifikasi (Dari Order Form)",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {

    });

    // VALIDATOR
    var validator = $(".create-form").validate({
        rules: {
            no_stuffing: {
                required: true
            },
            sales_order_id: {
                required: true
            },
        },
        messages: {
            no_stuffing: {
                required: "No Stuffing wajib diisi"
            },
            sales_order_id: {
                required: "Sales Order wajib diisi"
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


    $('#select-item-btn').click(function() {
        var checkedCheckboxes = $(".child:checked");
        var dataIds = checkedCheckboxes.map(function() {
            return $(this).data("id");
        }).get();
        var id_selected = getIDListDataSelected();
        var barangIn = $('#spesifikasi_in_id option:selected');

        var checkedCheckboxesorder = $(".childOrder:checked");
        var dataIdBarangOrder = checkedCheckboxesorder.map(function() {
            return $(this).data("id_barang");
        }).get().toString();
        var dataQtyBarangOrder = checkedCheckboxesorder.map(function() {
            return $(this).data("qty_barang");
        }).get().toString();
        var dataNamaBarangOrder = checkedCheckboxesorder.map(function() {
            return $(this).data("nama_barang");
        }).get().toString();

        if (dataIdBarangOrder == "" || dataIdBarangOrder == undefined) {
            Swal.fire({
                icon: 'error',
                title: 'Barang Output Wajib Dipilih !',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        } else {
            console.log(listStockSelected);
            $.each(listStockAsal, function(i, v) {
                var currentID = Number(v.id);

                if ($.inArray(currentID, dataIds) !== -1) {
                    var isIDSelected = $.grep(listStockSelected, function(item) {
                        return item.id == Number(currentID);
                    }).length > 0;
                    let divisi_id = $('#divisi_id option:selected').val();
                    let warehouse_id = $('#warehouse_id option:selected').val();
                    let tipe_barang = $('#type_barang option:selected').val();

                    var qty = v.qty ? v.qty : 0;

                    if (!isIDSelected) {
                        var isDuplicateOutput = listStockSelected.some(function(item) {
                            return item.output.id_barang == dataIdBarangOrder && parseFloat(item.qty) + parseFloat(qty) <= parseFloat(dataQtyBarangOrder);
                        });

                        if (!isDuplicateOutput) {
                            listStockAsal[i].stok_total = parseFloat(listStockAsal[i].stok_total);
                            listStockAsal[i].divisi_id = $('#divisi_id option:selected').val();
                            listStockAsal[i].warehouse_id = $('#warehouse_id option:selected').val();
                            listStockAsal[i].tipe_barang = $('#type_barang option:selected').val();
                            listStockAsal[i].qty = 0;
                            listStockAsal[i].output = {
                                id_barang: dataIdBarangOrder,
                                barang: dataNamaBarangOrder,
                                qty: parseFloat(dataQtyBarangOrder)
                            }
                            listStockSelected.push(listStockAsal[i]);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Qty barang diorder sudah terpenuhi',
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            });
                        }
                    }
                }
            });
            console.log(listStockSelected);

            drawTableSelectedItem(listStockSelected);
        }
    });

    $('.btn-submit-parent').click(function() {
        if (listStockSelected.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Barang yang akan dikirimkan ke vendor tidak boleh kosong !',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        } else {
            if ($('.create-form').valid()) {
                var isValid = true;
                var dataError = null;

                $.each(listStockSelected, function(i, v) {
                    var element = $('input[data-id="' + v.id + '"].stok-out');
                    var input_user = parseFloat(element.val());
                    var stok_max = parseFloat(element.data('stok_total'));

                    if (input_user > stok_max || isNaN(input_user) || input_user == undefined || input_user == 0) {
                        dataError = listStockSelected[i];
                        isValid = false;
                    } else {
                        listStockSelected[i].qty = input_user;
                    }
                });

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Stok keluar, barang ' + dataError.barang + ' dengan dokumen ' + dataError.bc_type + ' / ' + dataError.no_aju + ' tidak valid!',
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
                            data.append('listBarang', JSON.stringify(listStockSelected));

                            if (id) {
                                // UPDATE
                                $.ajax({
                                    url: "<?= base_url("pengeluaran-lokal/update"); ?>",
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
                                    },
                                });
                            } else {
                                // INSERT
                                $.ajax({
                                    url: "<?= base_url("pengeluaran-lokal/save"); ?>",
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
                                        Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                            confirmButtonText: 'Ok'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = "<?= base_url('pengeluaran-lokal/id/') ?>" + response.id
                                            }
                                        });
                                    },
                                });
                            }
                        }
                    });
                }

            }
        }
    });

    $("#sales_order_id,#warehouse_id,#divisi_id,#type_barang,#spesifikasi_id,#spesifikasi_in_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    function getIDListDataSelected() {
        var id_selected = [];
        $.each(listStockSelected, function(i, v) {
            id_selected.push(v.id);
        })
        return id_selected;
    }

    function definisiQtyInput() {
        $.each(listStockSelected, function(i, v) {
            var element = $('input[data-id="' + v.id + '"].stok-out');
            var input_user = parseFloat(element.val());
            var stok_max = parseFloat(element.data('stok_total'));

            if (input_user > stok_max || isNaN(input_user) || input_user == undefined || input_user == 0) {
                dataError = listStockSelected[i];
                isValid = false;
            } else {
                listStockSelected[i].qty = input_user;
            }
        });
    }

    function getListWarehouse() {
        $.ajax({
            url: `<?= base_url('pengeluaran-lokal/warehouse'); ?>`,
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

    function getListBarang() {
        // GET LIST BARANG
        $.ajax({
            url: `<?= base_url('pengeluaran-lokal/list-barang-stock-init'); ?>`,
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

    function getListDokumenPabean() {
        // GET LIST STOCK PER DOKUMEN PABEAN
        $.ajax({
            url: `<?= base_url('pengeluaran-lokal/list-stock-dokumen-bc'); ?>`,
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
                // LIST STOK PER BC
                listStockAsal = [];
                listStockAsal = res.data;
                drawTableAsalBarang(res.data);
            }
        });
    }

    function getListBarangOutput() {
        $.ajax({
            url: `<?= base_url('pengeluaran-lokal/list-barang-output'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                sales_order_id: $(".sales_order_id option:selected").val()
            },
            dataType: "json",
            success: function(res) {
                // $(".spesifikasi_in_id").empty()
                // $(".spesifikasi_in_id").append(`<option value=""></option>`)
                // res.data.forEach(function(item) {
                //     $(".spesifikasi_in_id").append(`<option data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-qty_barang="${item.qty}" value="${item.id_barang}">(${item.kode_barang}) ${item.nama_barang} Qty : ${item.qty}</option>`)
                // })
                // $(".spesifikasi_in_id").val();

                listStockOrder = [];
                listStockOrder = res.data;
                drawTableOrderBarang(res.data);
            }
        });
    }

    function drawTableAsalBarang(data) {
        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().clear().draw();
            dataTable.destroy();
        }
        const table = $('#dataTable');
        $.each(data, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').html(
                `
                    <div class="form-check">
                        <input data-id="${v.id}" data-stok_total="${v.stok_total}" autocomplete="one-time-code" class="form-check-input child" type="checkbox">
                    </div>
                `
            ));
            newRow.append($('<td style="text-align: center;">').text(v.type_barang_text));
            newRow.append($('<td style="text-align: center;">').text(v.bc_type));
            newRow.append($('<td style="text-align: center;">').text(v.no_aju));
            newRow.append($('<td style="text-align: center;">').text(v.stock_date));
            newRow.append($('<td style="text-align: center;">').text(v.barang));
            newRow.append($('<td style="text-align: center;">').text(v.satuan));
            newRow.append($('<td style="text-align: center;">').text(v.stok_total));
            table.find('tbody').append(newRow);
        });

        dataTable = $('#dataTable').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
            processing: false,
            serverSide: false,
            ordering: true,
            order: [],
            fixedHeader: true,
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            display: "stripe",
            searching: true,
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });

        dataTable.draw();
    }

    function drawTableOrderBarang(data) {
        if ($.fn.DataTable.isDataTable('#dataTableSalesOrder')) {
            $('#dataTableSalesOrder').DataTable().clear().draw();
            table.destroy();
        }
        const tableSales = $('#dataTableSalesOrder');
        $.each(data, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').html(
                `
                <div class="form-check">
                    <input data-id="${v.id}" data-kode_barang="${v.kode_barang}" data-nama_barang="${v.nama_barang}" data-qty_barang="${v.qty}" data-id_barang="${v.id_barang}" autocomplete="one-time-code" class="form-check-input childOrder" type="checkbox">
                </div>
            `
            ));
            newRow.append($('<td style="text-align: center;">').text(v.kode_barang));
            newRow.append($('<td style="text-align: center;">').text(v.nama_barang));
            newRow.append($('<td style="text-align: center;">').text(v.qty));
            tableSales.find('tbody').append(newRow);
        });

        dataTables = $('#dataTableSalesOrder').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
            processing: false,
            serverSide: false,
            ordering: true,
            order: [],
            fixedHeader: true,
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            display: "stripe",
            searching: false,
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });

        dataTables.draw();
    }

    function drawTableSelectedItem(data) {
        if ($.fn.DataTable.isDataTable('#selectedItemTable')) {
            $('#selectedItemTable').DataTable().clear().draw();
            selectedItemTable.destroy();
        }
        const table = $('#selectedItemTable');
        var no = 1;
        $.each(data, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').html(
                `
                   ${no++} 
                `
            ));
            newRow.append($('<td style="text-align: center;">').text(v.type_barang_text));
            newRow.append($('<td style="text-align: center;">').text(v.bc_type));
            newRow.append($('<td style="text-align: center;">').text(v.no_aju));
            newRow.append($('<td style="text-align: center;">').text(v.stock_date));
            newRow.append($('<td style="text-align: center;">').text(v.barang));
            newRow.append($('<td style="text-align: center;">').text(v.satuan));
            newRow.append($('<td style="text-align: center;">').text(v.stok_total));
            newRow.append($('<td style="text-align: center;">').html(
                `
                    <input onchange="definisiQtyInput()" <?= !empty($stuffingLokal) ? (($stuffingLokal['status_posting'] == "1") ? 'disabled' : '') : '' ?> class="form-control stok-out" oninput="preventNegativeInput(this);updateOrder($(this));" autocomplete="one-time-code" data-id="${v.id}" data-index="${i}"  data-stok_total="${v.stok_total}" class="form-control" type="text" value="${v.qty}">
                `
            ));
            newRow.append($('<td style="text-align: center;">').text(v.output.barang));
            newRow.append($('<td style="text-align: center;">').text(v.output.qty));
            newRow.append($('<td style="text-align: center;">').html(
                `
                    <button <?= !empty($stuffingLokal) ? (($stuffingLokal['status_posting'] == "1") ? 'disabled' : '') : '' ?> type="button" class="btn btn-danger" onclick="deleteDetail(${v.id})" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                `
            ));
            table.find('tbody').append(newRow);
        });

        selectedItemTable = $('#selectedItemTable').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
            processing: false,
            serverSide: false,
            ordering: true,
            order: [],
            fixedHeader: true,
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            display: "stripe",
            searching: true,
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });

        selectedItemTable.draw();
    }

    function deleteDetail(id) {
        var indexToRemove = -1;
        for (var i = 0; i < listStockSelected.length; i++) {
            if (listStockSelected[i].id == id) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listStockSelected.splice(indexToRemove, 1);
            drawTableSelectedItem(listStockSelected);
        }
    }

    function updateOrder(input) {
        var index = input.data('index');
        var qtyInput = input.val() == "" ? 0.0 : parseFloat(input.val()); // Ambil nilai qty yang diinputkan
        var item = listStockSelected[index];
        var maxQty = parseFloat(item.output.qty);
        var qty = Math.min(maxQty, qtyInput);
        input.val(qty);
    }

    function preventNegativeInput(inputElement) {
        var inputValue = inputElement.value;
        var numericValue = inputValue.replace(/[^0-9.]/g, '');
        if (parseFloat(numericValue) <= 0) {
            inputElement.value = 0;
        } else {
            inputElement.value = numericValue;
        }
    }

    function changeStatus() {
        $(".no_stuffing").attr("readonly", true);
        $.ajax({
            url: `<?= base_url("pengeluaran-lokal/get-pengeluaran-lokal-no"); ?>`,
            method: "GET",
            dataType: "json",
            success: function(res) {
                if (res.status) {
                    $(".no_stuffing").val(res?.data);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: res.message,
                        confirmButtonColor: '#4e73df',
                    })
                    $(".no_stuffing").attr("readonly", false);
                    $(".no_stuffing").val("");
                }
            }
        })
    }

    const print = function(url) {
        window.open(url, "_blank");
    }

    const posting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Stuffing Pengeluaran Lokal ?',
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
                    url: "<?= base_url("pengeluaran-lokal/posting"); ?>",
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
            title: 'Hapus Stuffing Pengeluaran Lokal ?',
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
                    url: "<?= base_url("pengeluaran-lokal/delete"); ?>",
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
                                window.location.replace("<?= base_url("pengeluaran-lokal"); ?>");
                            });
                        }
                    },
                });
            }
        })
    }
</script>

<?= $this->endSection(); ?>