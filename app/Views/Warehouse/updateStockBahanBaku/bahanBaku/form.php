<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($po) ? "Tambah Update Data Stock Pembelian" : "Update Update Data Stock Pembelian" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("update-stock-bahan-baku"); ?>">
                Kembali
            </a>
            <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                Simpan
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Po</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id" value="<?= !empty($po) ? encrypt($po['id']) : '' ?>" class="id">
                <?= csrf_field() ?>
                <div class="row">

                    <!-- DIVISI -->
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= !empty($po) && $po['divisi_id'] == $d['id'] ? 'selected' : '' ?>
                                            value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>

                    <!-- WAREHOUSE -->
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">
                                <option value=""></option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Warehouse</label>
                        </div>
                    </div>

                    <!-- TANGGAL AWAL               -->
                    <div class="col-md-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control tanggal_po_awal" name="tanggal_po_awal" id="tanggal_po_awal" placeholder="Tanggal Awal">
                                <label for="floatingInput">Tanggal Awal</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>

                     <!-- TANGGAL AKHIR              -->
                    <div class="col-md-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control tanggal_po_akhir" name="tanggal_po_akhir" id="tanggal_po_akhir" placeholder="Tanggal Awal">
                                <label for="floatingInput">Tanggal Akhir</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>

                    <!-- SUPPLIER -->
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select supplier_id" id="supplier_id" name="supplier_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($supplier as $s): ?>
                                    <option <?= !empty($po) && $po['supplier_id'] == $s['id'] ? 'selected' : '' ?>
                                            value="<?= $s['id'] ?>">
                                        <?= $s['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Supplier</label>
                        </div>
                    </div>
                </div>

                 <div class="row mt-3">
                    <div class="col mb-0">
                        <label class="form-label font-weight-bold lable-title">Pilih Inventori Barang Yang Akan Di Update Stock</label>
                    </div>
                    <div class="col-md-12 col-table-button-tts" style="margin-top: -10px;">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: center;">Asal Barang</th>
                                        <th style="text-align: center;">No PO</th>
                                        <th style="text-align: center;">Supplier</th>
                                        <th style="text-align: center;">Dokumen Pabean</th>
                                        <th style="text-align: center;">Tgl PO</th>
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

            </form>


            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Daftar Barang PO Yang Akan Di Perbarui</label>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="selectedItemTable" width="100%" border="1" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;" colspan="12">Detail PO & LPB</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">Asal Barang</th>
                                    <th style="text-align: center;">No Dokumen</th>
                                    <th style="text-align: center;">Supplier</th>
                                    <th style="text-align: center;">Tgl PO</th>
                                    <th style="text-align: center;">Barang - Spesifikasi</th>
                                    <th style="text-align: center;">Satuan</th>
                                    <th style="text-align: center;">Qty PO</th>
                                    <th style="text-align: center;">Qty Kotor (selisih)</th>
                                    <th style="text-align: center;">Qty Di Terima</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="body-table">
                            </tbody>
                            <tfoot class="foot-detail-table" id="foot-detail-table">
                                <tr>
                                    <td colspan="16" style="text-align: center;">
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

    var listStockAsal = [];
    var listStockSelected = [];

    $("#tanggal_po_awal, #tanggal_po_akhir").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    <?php if (empty($po)): ?>
        $('.tanggal').change(function() {
            changeStatus();
        })
    <?php endif; ?>


    $(document).ready(function() {

        // init select2
        $('#warehouse_id').select2({
            placeholder: "Pilih Warehouse",
            theme: "bootstrap-5",
            allowClear: true
        }).change(function() {
            // drawTableSelectedItem(listStockSelected);
            getListPo();
        });

        $('#divisi_id').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5",
            allowClear: true
        }).change(function() {
            // GET WAREHOUSES
            getListWarehouse();
        });

        $('#supplier_id').select2({
            placeholder: "Pilih Supplier",
            theme: "bootstrap-5",
            allowClear: true
        })

        // $('#no_po').select2({
        //     placeholder: "Pilih No Po",
        //     theme: "bootstrap-5",
        //     allowClear: false
        // }).change(function() {
        //     getListBarangPo();
        // });


        // kalau edit mode, trigger change supaya ajax ke-load
        <?php if (!empty($po)) : ?>
            $('#divisi_id').trigger('change');     // biar getListWarehouse() ke-load
            $('#warehouse_id').trigger('change');  // kalau perlu refresh warehouse
            $('#supplier_id').trigger('change');   // sync supplier
            // $('#no_po').trigger('change');         // biar getListBarangPo() jalan
        <?php endif; ?>

    });



    var dataTable = $('#dataTable').DataTable({

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

    <?php if (!empty($po)) : ?>
        // GET LIST BARANG 
        $.ajax({
            url: `<?= base_url('proses-rebus/list-barang-stock-init'); ?>`,
            method: "GET",
            data: {
                type_barang: $(".type_barang option:selected").val(),
                divisi_id: $(".divisi_id option:selected").val(),
                warehouse_id: $(".warehouse_id option:selected").val()
            },
            dataType: "json",
            success: function(res) {
                $(".spesifikasi_rebus_id").empty()
                $(".spesifikasi_rebus_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".spesifikasi_rebus_id").append(`<option data-stock_id="${item.stock_id}" data-kode_barang="${item.kode_barang}" data-barang="${item.barang}" data-kode_satuan="${item.kode_satuan}" value="${item.spesifikasi_rebus_id}">(${item.kode_barang}) ${item.barang}</option>`)
                })
                $(".spesifikasi_rebus_id").val();
            }
        });
        <?php if ($po) : ?>
            $('.detail-form-layout').hide()
        <?php endif; ?>
    <?php endif; ?>


    $('#type_pengambilan_stock').select2({
        placeholder: "Pilih Tipe Ambil Stok",
        theme: "bootstrap-5",
    }).change(function() {
        // FIFO
        if ($(this).val() == "FIFO") {
            $('.form-fifo').show();
        } else {
            $('.form-fifo').hide();
        }
        // RESET
        $('#spesifikasi_rebus_id').val(null).change();
        $('#spesifikasi_hasil_rebus_id').val(null).change();
        $('#qty_rebus_fifo').val(null);
        $('#qty_hasil_rebus_fifo').val(null);
        listStockAsal = [];
        listStockSelected = [];
        drawTableAsalBarang(listStockAsal);
        drawTableSelectedItem(listStockSelected);
    });

    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // drawTableSelectedItem(listStockSelected);
        // getPoList()
    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // GET WAREHOUSES
        getListWarehouse();
    });

    // $('#no_po').select2({
    //     placeholder: "Pilih No Po",
    //     theme: "bootstrap-5",
    //     allowClear: false
    // }).change(function() {
    //    getListBarangPo()
    // });

    $('#spesifikasi_rebus_id').select2({
        placeholder: "Pilih Barang - Spesifikasi (Udang / Kepiting Kulit)",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // LIST DOKUMEN PABEAN
        getListDokumenPabean();
        getListBarangHasilRebus();
    });


    $('#spesifikasi_hasil_rebus_id').select2({
        placeholder: "Pilih Barang - Spesifikasi (Udang / Kepiting Rebus)",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {

    });


    $('#supplier_id').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // LIST DOKUMEN PABEAN
        // getListDokumenPabean();
        getListBarangPo();
        <?php if (!empty($po)) : ?>
             getListBarangPoAlreadyHaveKotor();
        <?php endif; ?>
    });


    // VALIDATOR
    var validator = $(".create-form").validate({
        rules: {
            no_rebus: {
                required: true
            },
            tanggal: {
                required: true
            },
            tanggal_selesai: {
                required: true
            },
            divisi_id: {
                required: true
            },
            warehouse_id: {
                required: true
            },
        },
        messages: {
            no_rebus: {
                required: "No surat jalan wajib diisi"
            },
            tanggal: {
                required: "Tanggal wajib diisi"
            },
            tanggal: {
                required: "Tanggal selesai rebus wajib diisi"
            },
            divisi_id: {
                required: "Pilih Departemen"
            },
            warehouse_id: {
                required: "Pilih Warehouse"
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
        var typePengambilanStock = $('#type_pengambilan_stock option:selected').val();
        if (typePengambilanStock == "FIFO") {
            var spesifikasiHasilRebus = $('.spesifikasi_hasil_rebus_id option:selected');
            if (spesifikasiHasilRebus.val() == "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Barang hasil rebus wajib diisi',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            } else {
                insertListFifo();
            }
        } else {
            insertListPabean();
        }
    });

    function insertListPabean() {
        var checkedCheckboxes = $(".child:checked");
        var dataIds = checkedCheckboxes.map(function() {
            return $(this).data("id");
        }).get();
        var id_selected = getIDListDataSelected();

            $.each(listStockAsal, function(i, v) {
                var currentID = Number(v.id);

                if ($.inArray(currentID, dataIds) !== -1) {
                    var isIDSelected = $.grep(listStockSelected, function(item) {
                        return item.id == Number(currentID);
                    }).length > 0;

                    if (!isIDSelected) {
                        listStockAsal[i].stok_total = parseFloat(listStockAsal[i].stok_total);
                        listStockAsal[i].qty = 0;
                        listStockSelected.push(listStockAsal[i]);
                    }
                }
            });
            drawTableSelectedItem(listStockSelected);
    }

    function insertListFifo() {
        var dataIds = getIDListDataSelected();
        var qtyRebusFifo = parseFloat($('#qty_rebus_fifo').val());
        var qtyHasilRebusFifo = parseFloat($('#qty_hasil_rebus_fifo').val());
        var stokRebusID = $(".spesifikasi_rebus_id option:selected").data('stock_id');
        var barangIn = $('#spesifikasi_hasil_rebus_id option:selected');

        if (isNaN(qtyRebusFifo) || isNaN(qtyHasilRebusFifo) || qtyRebusFifo == 0 || qtyHasilRebusFifo == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan : Qty Rebus dan Qty Hasil Rebus Wajib Diisi',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                reverseButtons: true,
                confirmButtonText: 'Oke',
            })
        } else {
            var totalStokTotal = 0;
            $.each(listStockAsal, function(i, v) {
                totalStokTotal += parseFloat(v.stok_total);
            });

            if (qtyRebusFifo > totalStokTotal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan : Stok barang yang akan direbus tidak cukup !',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    reverseButtons: true,
                    confirmButtonText: 'Oke',
                })
            } else if (qtyHasilRebusFifo > totalStokTotal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan : Stok barang hasil rebus tidak valid !',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    reverseButtons: true,
                    confirmButtonText: 'Oke',
                })
            } else {
                deleteByStockID(stokRebusID);
                // STOK REBUS OUT
                var idStockInserted = [];
                $.each(listStockAsal, function(i, v) {
                    var currentID = Number(v.id);
                    if ($.inArray(currentID, dataIds) == -1) {
                        var isIDSelected = $.grep(listStockSelected, function(item) {
                            return item.id == Number(currentID);
                        }).length > 0;
                        if (!isIDSelected && qtyRebusFifo != 0 && parseFloat(listStockAsal[i].stok_total) != 0) {
                            var rebusQty = Math.min(qtyRebusFifo, parseFloat(listStockAsal[i].stok_total));
                            listStockAsal[i].stok_total = parseFloat(listStockAsal[i].stok_total);
                            listStockAsal[i].qty = parseFloat(rebusQty);

                            listStockSelected.push(listStockAsal[i]);
                            qtyRebusFifo = qtyRebusFifo - rebusQty;
                            // MASUKKAN YANG BARU
                            idStockInserted.push(listStockAsal[i].id);
                        }
                    }
                });
                // STOK HASIL REBUS
                var lengthStockSelected = idStockInserted.length;
                var qtyHasilBagi = qtyHasilRebusFifo / lengthStockSelected;
                qtyHasilBagi = qtyHasilBagi;

                $.each(listStockSelected, function(i, v) {
                    if ($.inArray(v.id, idStockInserted) !== -1) { // Cek apakah elemen ditemukan dalam array
                        listStockSelected[i].output = {
                            barang: barangIn.data('barang'),
                            barang_id: barangIn.data('barang_id'),
                            kode_satuan: barangIn.data('kode_satuan'),
                            stock_id: barangIn.data('stock_id'),
                            qty: qtyHasilBagi
                        };
                    }
                });


            }

            // console.log(listStockSelected);
            drawTableSelectedItem(listStockSelected);
        }

    }


    function deleteByStockID(stockID) {
        listStockSelected = listStockSelected.filter(function(item) {
            return item.stock_id != stockID;
        });
    }

    $('.btn-submit-parent').click(function() {
        if (listStockSelected.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Barang yang akan direbus tidak boleh kosong !',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        } else {
            var typePengambilanStock = $('#type_pengambilan_stock option:selected').val();
            if ($('.create-form').valid()) {
                var isValidRebus = true;
                var isValidHasilRebus = true;
                var dataErrorRebus = null;
                var dataErrorHasilRebus = null;

                    // Reset status validasi
                    isValidRebus = true;
                    isValidHasilRebus = true;
                    dataErrorRebus = null;
                    dataErrorHasilRebus = null;
                    
                    // Reset semua class error terlebih dahulu
                    $('.qty-diterima').removeClass('is-invalid');
                    
                    // Validasi per item
                    $.each(listStockSelected, function(i, v) {
                        var qty_diterima = $('input.qty-diterima[data-id="' + v.id + '"]');

                        var input_user_diterima = destroyFormatRupiah(qty_diterima.val()) || 0;

                        // Always set the qty value regardless of validation
                        listStockSelected[i].qty_diterima = input_user_diterima;
                    });


                    
                    if (!isValidHasilRebus) {
                        const errorItem = dataErrorHasilRebus || listStockSelected[0];
                        Swal.fire({
                            icon: 'error',
                            title: 'Qty Hasil Rebus Tidak Valid',
                            html: `Barang: <strong>${errorItem.output?.barang || 'Tidak Diketahui'}</strong><br>
                                Dokumen: ${errorItem.bc_type || '-'} / ${errorItem.no_aju || '-'}<br>
                                <span class="text-danger">Qty hasil rebus harus lebih dari 0</span>`,
                            confirmButtonColor: '#4e73df',
                            confirmButtonText: 'Ok'
                        });
                        return false;
                    }
                

                if (!isValidRebus) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Qty Barang Rebus, barang ' + dataErrorRebus.barang + ' dengan dokumen ' + dataErrorRebus.bc_type + ' / ' + dataErrorRebus.no_aju + ' tidak valid!',
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                } else if (!isValidHasilRebus) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Qty Barang Hasil Rebus, barang ' + dataErrorHasilRebus.output.barang + ' dengan dokumen ' + dataErrorHasilRebus.bc_type + ' / ' + dataErrorHasilRebus.no_aju + ' tidak valid!',
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
                        cancelButtonText: 'Kembali',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let id = $('#id').val();
                            let data = new FormData(document.querySelector(".create-form"));
                            data.append('listBarang', JSON.stringify(listStockSelected));

                            if (id) {
                                // UPDATE
                                $.ajax({
                                    url: "<?= base_url("update-stock-bahan-baku/update"); ?>",
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
                                                window.location.href = "<?= base_url("update-stock-bahan-baku") ?>";
                                            }
                                        });
                                    },
                                });
                            } else {
                                // INSERT
                                $.ajax({
                                    url: "<?= base_url("update-stock-bahan-baku/save"); ?>",
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
                                        if (response.status == false) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                        } else {
                                            Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                                confirmButtonText: 'Ok'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = "<?= base_url("update-stock-bahan-baku") ?>";
                                                }
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


    $("#vendor_id,#warehouse_id,#divisi_id,#type_barang,#spesifikasi_rebus_id,#spesifikasi_hasil_rebus_id,#type_pengambilan_stock,#supplier_id")
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

    function getListWarehouse() {
        $.ajax({
            url: `<?= base_url('proses-rebus/warehouse'); ?>`,
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

                <?php if (!empty($po)) : ?>
                    $(".warehouse_id").val("<?= $po['warehouse_id'] ?>").trigger('change');
                <?php endif; ?>
                $(".warehouse_id").val();
            }
        });
    }

    function getListBarang() {
        // GET LIST BARANG
        $.ajax({
            url: `<?= base_url('proses-rebus/list-barang-stock-init'); ?>`,
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
                $(".spesifikasi_rebus_id").empty()
                $(".spesifikasi_rebus_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".spesifikasi_rebus_id").append(`<option data-stock_id="${item.stock_id}" data-kode_barang="${item.kode_barang}" data-barang="${item.barang}" data-kode_satuan="${item.kode_satuan}" value="${item.spesifikasi_id}">(${item.kode_barang}) ${item.barang}</option>`)
                })
                $(".spesifikasi_rebus_id").val();
            }
        });
    }

    function getListDokumenPabean() {
        // GET LIST STOCK PER DOKUMEN PABEAN
        $.ajax({
            url: `<?= base_url('proses-rebus/list-stock-dokumen-bc'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                stock_id: $(".spesifikasi_rebus_id option:selected").data('stock_id'),
                spesifikasi_id: $(".spesifikasi_rebus_id option:selected").val(),
                supplier_id: $(".supplier_id option:selected").val()
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

    function getListPo() {
        $.ajax({
            url: `<?= base_url('update-stock-bahan-baku/list-po'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $("#divisi_id option:selected").val(),
                supplier_id: $("#supplier_id option:selected").val(),
                warehouse_id: $("#warehouse_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                let $select = $("#no_po");
                $select.empty(); // kosongkan dulu biar ga double
                $select.append(`<option value=""></option>`); // default kosong

                if (res && res.data && res.data.data && res.data.data.length > 0) {
                    res.data.data.forEach(function(item) {
                        // asumsinya res ada field id & no_po
                        $select.append(`<option value="${item.id}">${item.po_no}</option>`);
                    });

                    <?php if (!empty($po)) : ?>
                        // terakhir banget: pilih no_po, lalu trigger change
                        $("#no_po").val("<?= $po['id'] ?>").trigger('change');
                    <?php endif; ?>
                }
            }
        });
    }


    function getListBarangPo() {
        // GET LIST STOCK PER DOKUMEN PABEAN
        $.ajax({
            url: `<?= base_url('update-stock-bahan-baku/list-barang-po'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $("#divisi_id option:selected").val(),
                supplier_id: $("#supplier_id option:selected").val(),
                warehouse_id: $("#warehouse_id option:selected").val(),
                tanggal_po_awal: $("#tanggal_po_awal").val(),
                tanggal_po_akhir: $("#tanggal_po_akhir").val(),
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


    function getListBarangPoAlreadyHaveKotor() {
        // GET LIST STOCK PER DOKUMEN PABEAN
        $.ajax({
            url: `<?= base_url('update-stock-bahan-baku/list-barang-po-kotor'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $("#divisi_id option:selected").val(),
                supplier_id: $("#supplier_id option:selected").val(),
                warehouse_id: $("#warehouse_id option:selected").val(),
                tanggal_po_awal: $("#tanggal_po_awal").val(),
                tanggal_po_akhir: $("#tanggal_po_akhir").val(),
            },
            dataType: "json",
            success: function(res) {
                // LIST STOK PER BC
                listStockSelected = [];
                listStockSelected = res.data;
                drawTableSelectedItem(res.data);
               
            }
        });
    }

    function getListBarangHasilRebus() {
        $.ajax({
            url: `<?= base_url('jasa-vendor-in/list-barang-masuk'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                type_barang: $(".type_barang option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".spesifikasi_hasil_rebus_id").empty()
                $(".spesifikasi_hasil_rebus_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".spesifikasi_hasil_rebus_id").append(`<option data-barang_id="${item.spesifikasi_id}" data-stock_id="${item.stock_id}" data-kode_barang="${item.kode_barang}" data-barang="${item.barang}" data-kode_satuan="${item.kode_satuan}" value="${item.spesifikasi_rebus_id}">(${item.kode_barang}) ${item.barang}</option>`)
                })
                $(".spesifikasi_hasil_rebus_id").val();
            }
        });
    }


    function drawTableAsalBarang(data) {
        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().clear().draw();
            dataTable.destroy();
        }
        const table = $('#dataTable');
        var typePengambilanStok = $('#type_pengambilan_stock option:selected').val();

        $.each(data, function(i, v) {
            var newRow = $('<tr>');
           
                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <div class="form-check">
                        <input data-id="${v.id}" data-stok_total="${v.stok_total}" autocomplete="one-time-code" class="form-check-input child" type="checkbox">
                    </div>
                `
                ));

            newRow.append($('<td style="text-align:center;">').text(v.sumber));
            newRow.append($('<td style="text-align:center;">').text(v.stock_dokumen));
            newRow.append($('<td style="text-align:center;">').text(v.supplier_name));
            newRow.append($('<td style="text-align:center;">').text(v.bc_type));
            // newRow.append($('<td style="text-align:center;">').text(v.no_aju));
            newRow.append($('<td style="text-align:center;">').text(v.stock_date));
            newRow.append($('<td style="text-align:center;">').text(v.barang));
            newRow.append($('<td style="text-align:center;">').text(v.satuan));
            newRow.append($('<td style="text-align:center;">').text(greatFormatRupiah(v.total_penerimaan)));
            table.find('tbody').append(newRow);
        });

        dataTable = $('#dataTable').DataTable({

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
            lengthMenu: [
                [100],
                [100]
            ],
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

    function drawTableSelectedItem(data) {
        console.log(data)
        const $tbody = $('#selectedItemTable .body-table');
        $tbody.empty();

        if (data.length === 0) {
            $('#foot-detail-table').show();
            return;
        } else {
            $('#foot-detail-table').hide();
        }

        // Group by stock_id hasil rebus
        const grouped = {};
        console.log(data)
        data.forEach(item => {
            const key = item.output?.stock_id || 'undefined';
            if (!grouped[key]) grouped[key] = [];
            grouped[key].push(item);
        });

        let rowIndex = 1;

        for (const stockID in grouped) {
            const group = grouped[stockID];
            const groupName = group[0].output?.barang || 'Tidak Diketahui';
            const satuan = group[0].output?.kode_satuan || '-';

            // Hitung total hasil rebus per group
            let totalHasilGroup = 0;
            group.forEach(item => {
                totalHasilGroup += parseFloat(item.output?.qty || 0);
            });

            // --- Baris Data ---
            group.forEach((item, i) => {
                const tr = $(`
                    <tr class="data-row" data-group="${stockID}" data-index="${i}">
                        <td style="text-align: center;">${rowIndex++}</td>
                        <td>${item.sumber || '-'}</td>
                        <td>${item.stock_dokumen || '-'}</td>
                        <td>${item.supplier_name || '-'}</td>
                        <td>${item.stock_date || '-'}</td>
                        <td>${item.barang || '-'}</td>
                        <td>${item.satuan || '-'}</td>
                        <td style="text-align: right;">${greatFormatRupiah(item.total_penerimaan)}</td>
                        <td style="text-align: right;">${greatFormatRupiah(item.stok_total_kotor)}</td>
                        <td>
                            <input type="text" step="0.001" min="0" 
                                class="form-control qty-diterima" 
                                name="qty_diterima" 
                                data-id="${item.id}"
                                data-index="${i}"
                                value="${greatFormatRupiah(item.stok_total_diterima) || 0}" />
                        </td>
                        <td><button type="button" class="btn btn-danger btn-sm btn-remove-row">Hapus</button></td>
                    </tr>
                `);
                $tbody.append(tr);
            });
        }

        // --- Tambah Total Keseluruhan Rebus & Hasil Rebus ---
        updateTotalRow();

        // --- Logic Ubah Qty Rebus Manual ---
        $tbody.on('input', '.qty-rebus-input, .qty-kotor, .total-hasil-input', function() {
            updateTotalRow();
        });


        function updateTotalRow() {
            let totalQtyRebus = 0;
            let totalQtyHasilRebus = 0;

            // Hitung total rebus dari masing-masing baris input qty rebus
            $('.qty-rebus-input').each(function () {
                const val = destroyFormatRupiah($(this).val());
                if (!isNaN(val)) totalQtyRebus += val;
            });

            // Hitung total hasil rebus dari input qty hasil rebus per item
            $('.qty-kotor').each(function () {
                const val = destroyFormatRupiah($(this).val());
                if (!isNaN(val)) totalQtyHasilRebus += val;
            });

            // Hapus semua total row yang sudah ada
            $('.grand-total-row').remove();

            // Buat hanya satu grand total row di bagian paling bawah
            const totalRow = $(`
                <tr class="grand-total-row" style="background-color: #d1ecf1; font-weight: bold;">
                    <td colspan="8" style="text-align: right;">GRAND TOTAL</td>
                    <td style="text-align: right;">${greatFormatRupiah(totalQtyRebus)}</td>
                    <td style="text-align: right;">${greatFormatRupiah(totalQtyHasilRebus)}</td>
                    <td></td>
                </tr>
            `);

            $tbody.append(totalRow);
            
            // Panggil validasi setelah update total
            // validateRebusInputs();
        }

        // Fungsi validasi
        // function validateRebusInputs() {
        //     let isValidRebus = true;
        //     let isValidHasilRebus = true;
            
        //     $('.qty-rebus-input').each(function() {
        //         const inputVal = parseFloat($(this).val());
        //         const stokMax = parseFloat($(this).data('stok_total'));
                
        //         if (isNaN(inputVal) || inputVal <= 0 || inputVal > stokMax) {
        //             isValidRebus = false;
        //             $(this).addClass('is-invalid');
        //         } else {
        //             $(this).removeClass('is-invalid');
        //         }
        //     });
            
        //     $('.qty-hasil-input, .total-hasil-input').each(function() {
        //         const inputVal = parseFloat($(this).val());
                
        //         if (isNaN(inputVal) || inputVal <= 0) {
        //             isValidHasilRebus = false;
        //             $(this).addClass('is-invalid');
        //         } else {
        //             $(this).removeClass('is-invalid');
        //         }
        //     });
            
        //     // Update status validasi untuk digunakan di form submit
        //     window.isValidRebus = isValidRebus;
        //     window.isValidHasilRebus = isValidHasilRebus;
        // }

        // --- Tombol Hapus ---
        $('.btn-remove-row').click(function () {
            const $tr = $(this).closest('tr');
            const index = $tr.data('index');
            const stockID = $tr.data('group');

            // Hapus dari list
            listStockSelected.splice(index, 1);

            // Redraw
            drawTableSelectedItem(listStockSelected);
        });
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
            $(".no_rebus").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("proses-rebus/get-no"); ?>`,
                method: "GET",
                data: {
                    warehouse_id: $('#warehouse_id option:selected').val(),
                    tanggal: $('#tanggal').val()
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".no_rebus").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".no_rebus").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".no_rebus").val("");
                    }
                }
            })
        } else {
            $(".no_rebus").attr("readonly", false);
            $(".no_rebus").val("");
        }
    }

    const print = function(url) {
        window.open(url, "_blank");
    }

    const posting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Update Data Stock Pembelian ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("proses-rebus/posting"); ?>",
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
                                window.location.href = "<?= base_url("update-stock-bahan-baku") ?>";
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
            title: 'Hapus Update Data Stock Pembelian ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("proses-rebus/delete"); ?>",
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
                                window.location.href = "<?= base_url("update-stock-bahan-baku") ?>";
                            });
                        }
                    },
                });
            }
        })
    }
</script>

<?= $this->endSection(); ?>