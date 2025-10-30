<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($penerimaanBarangBroken) ? "Tambah Update Data Stock Pembelian" : "Update Update Data Stock Pembelian" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("penerimaan-barang-broken"); ?>">
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
                    <label class="form-label font-weight-bold lable-title">Data Po Barang Broken</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id" value="<?= !empty($penerimaanBarangBroken) ? encrypt($penerimaanBarangBroken['id']) : '' ?>" class="id">
                <?= csrf_field() ?>
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" <?= !empty($penerimaanBarangBroken) ? ($penerimaanBarangBroken['status_posting'] === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($penerimaanBarangBroken) ? $penerimaanBarangBroken['no_penerimaan_barang'] : ""; ?>" type="text" class="form-control no_penerimaan_barang" id="no_penerimaan_barang" name="no_penerimaan_barang" placeholder="No. Penerimaan">
                                    <label for="floatingInput">No. Penerimaan</label>
                                </div>
                                <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input <?= !empty($penerimaanBarangBroken) ? ($penerimaanBarangBroken['no_penerimaan_barang']  ? 'checked' : '') : ''; ?> autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DIVISI -->
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= !empty($penerimaanBarangBroken) && $penerimaanBarangBroken['divisi_id'] == $d['id'] ? 'selected' : '' ?>
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
                                    <option <?= !empty($penerimaanBarangBroken) && $penerimaanBarangBroken['supplier_id'] == $s['id'] ? 'selected' : '' ?>
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
                                        <th style="text-align: center;">No PO</th>
                                        <th style="text-align: center;">Supplier</th>
                                        <th style="text-align: center;">Dokumen Pabean</th>
                                        <th style="text-align: center;">Tgl PO</th>
                                    </tr>
                                </thead>
                                <tbody class="body-table">
                                </tbody>
                            </table>

                            <div class="row">
                                <!-- MASTER BARANG -->
                                <div class="col-md-4">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select barang_id" id="barang_id" name="barang_id">
                                            <option value=""></option>
                                        </select>
                                        <label for="floatingInput" style="z-index: 1;">Pilih Barang</label>
                                    </div>
                                </div>

                                <!-- SPESIFIKASI BARANG -->
                                <div class="col-md-4">
                                    <div class="form-spp form-floating mb-3">
                                            <select class="form-select spesifikasi_id" id="spesifikasi_id" name="spesifikasi_id[]">
                                            </select>
                                            <label for="spesifikasi_id">Pilih Spesifikasi Barang</label>
                                    </div>
                                </div>
                            </div>
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
                                    <th style="text-align: center;" colspan="12">Detail Penerimaan Barang Broken</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">No Dokumen</th>
                                    <th style="text-align: center;">Supplier</th>
                                    <th style="text-align: center;">Tgl PO</th>
                                    <th style="text-align: center;">Barang - Spesifikasi</th>
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
    let abortController = null;

    $("#tanggal_po_awal, #tanggal_po_akhir").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $(document).ready(function () {
        // init datepicker
        $("#tanggal_po_awal, #tanggal_po_akhir").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        <?php if (!empty($penerimaanBarangBroken)) : ?>
            // convert YYYY-MM-DD -> DD/MM/YYYY
            function formatDate(dateString) {
                if (!dateString) return "";
                const parts = dateString.split("-");
                return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : "";
            }

            // set value ke datepicker
            $("#tanggal_po_awal").datepicker("setDate", formatDate("<?= $penerimaanBarangBroken['tanggal_awal'] ?>"));
            $("#tanggal_po_akhir").datepicker("setDate", formatDate("<?= $penerimaanBarangBroken['tanggal_akhir'] ?>"));

            // trigger change supaya kalau ada listener ikut ke-execute
            $("#tanggal_po_awal").trigger("change");
            $("#tanggal_po_akhir").trigger("change");

            // select2 reload
            $('#divisi_id').trigger('change');     
            $('#warehouse_id').trigger('change');  
            $('#supplier_id').trigger('change');   


            const penerimaanBarangBrokenDetail = <?= json_encode($penerimaanBarangBrokenDetail ?? []) ?>;

            if (Array.isArray(penerimaanBarangBrokenDetail) && penerimaanBarangBrokenDetail.length > 0) {
                // Reset list global biar clean
                listStockSelected = [];

                penerimaanBarangBrokenDetail.forEach(item => {
                    listStockSelected.push({
                        po_id: item.po_id ?? null,
                        po_no: item.po_no ?? "-",
                        po_date: item.po_date ?? "-",
                        supplier_name: item.supplier_name ?? "-",
                        barang_id: item.barang_id,
                        barang_name: item.barang_name,
                        spesifikasi_id: item.spesifikasi_id,
                        spesifikasi_name: item.spesifikasi_name,
                        qty_diterima: item.qty_diterima ?? 0
                    });
                });

                // Draw tabel langsung
                drawTableSelectedItem(listStockSelected);
            }


        <?php endif; ?>
    });


    $('#divisi_id').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5",
            allowClear: true
        }).change(function() {
            // GET WAREHOUSES
            getListWarehouse();
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


    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    })

    $("#barang_id").select2({
        placeholder: "Pilih Barang",
        theme: "bootstrap-5",
        minimumInputLength: 3,
        width: '100%',
        ajax: {
            delay: 300,
            transport: function(params, success, failure) {
                if (abortController) {
                    abortController.abort();
                }
                abortController = new AbortController();

                fetch("<?= base_url('jasa-vendor-out-kepiting-kukus/search-master-barang'); ?>?" + new URLSearchParams({
                    q: params.data.term
                }), {
                    signal: abortController.signal
                })
                .then(res => res.json())
                .then(success)
                .catch(err => {
                    if (err.name !== "AbortError") failure(err);
                });
            },
            processResults: function(data) {
                return {
                    results: data.data.map(item => ({
                        id: item.id,
                        text: `${item.master_barang}`
                    }))
                };
            }
        }
    });

    $("#spesifikasi_id").select2({
        placeholder: "Pilih Spesifikasi Barang",
        theme: "bootstrap-5",
        multiple: true,
        ajax: {
            delay: 300,
            transport: function(params, success, failure) {
                if (abortController) {
                    abortController.abort();
                }
                abortController = new AbortController();

                fetch("<?= base_url('jasa-vendor-out-kepiting-kukus/search-barang'); ?>?" + new URLSearchParams({
                    barang_id: $('#barang_id option:selected').val(),
                    q: params.data.term
                }), {
                    signal: abortController.signal
                })
                .then(res => res.json())
                .then(success)
                .catch(err => {
                    if (err.name !== "AbortError") failure(err);
                });
            },
            processResults: function(data) {
                return {
                    results: data.data.map(item => ({
                        id: item.spesifikasi_id,
                        text: `${item.master_barang} - ${item.spesifikasi}`,
                        master_barang: item.master_barang,
                        spesifikasi: item.spesifikasi,
                        satuan: item.kode_satuan,
                    }))
                };
            }
        }
    }).change(function() {
        // getListBarangPo();
    });

    $('#supplier_id').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        getListPo();
    });


    // VALIDATOR
    var validator = $(".create-form").validate({
        rules: {
            no_penerimaan_barang: {
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
            no_penerimaan_barang: {
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
        insertListPabean();
    });

    function insertListPabean() {
        const checkedRadio = $(".child:checked");
        if (checkedRadio.length === 0) {
            Swal.fire('Pilih salah satu PO dulu', '', 'warning');
            return;
        }

        const poId = checkedRadio.data("id");
        const poData = listStockAsal.find(v => Number(v.id) === Number(poId));

        const barangId = $('#barang_id').val();
        const barangName = $('#barang_id option:selected').text();
        const spesifikasiIds = $('#spesifikasi_id').val(); // bisa multiple
        const spesifikasiNames = $('#spesifikasi_id option:selected').map(function() {
            return $(this).text();
        }).get();

        if (!spesifikasiIds || spesifikasiIds.length === 0) {
            Swal.fire('Pilih spesifikasi dulu', '', 'warning');
            return;
        }

        spesifikasiIds.forEach((spekId, i) => {
            const spekName = spesifikasiNames[i];

            // Cek duplikasi
            const alreadyExist = listStockSelected.some(item =>
                item.po_id == poId &&
                item.barang_id == barangId &&
                item.spesifikasi_id == spekId
            );

            if (alreadyExist) return; // skip kalau sudah ada

            listStockSelected.push({
                po_id: poData.id,
                po_no: poData.po_no,
                po_date: poData.po_date,
                supplier_name: poData.supplier_name,
                barang_id: barangId,
                barang_name: barangName,
                spesifikasi_id: spekId,
                spesifikasi_name: spekName,
                stok_total: 0,
                stok_total_kotor: 0,
                stok_total_diterima: 0,
                satuan: '-',
                qty_diterima: 0
            });
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
        if (!listStockSelected.length) {
            return Swal.fire({
                icon: 'error',
                title: 'Barang yang akan direbus tidak boleh kosong!',
                confirmButtonColor: '#4e73df',
            });
        }

        if (!$('.create-form').valid()) return;

        // ambil dan set ulang qty diterima
        $('.qty-diterima').each(function() {
            let id = $(this).data('id');
            let val = parseFloat($(this).val()) || 0;
            let item = listStockSelected.find(x => Number(x.id) === Number(id));
            if (item) item.qty_diterima = val;
        });

        // validasi sederhana qty diterima > 0
        const invalidItem = listStockSelected.find(x => (x.qty_diterima || 0) <= 0);
        if (invalidItem) {
            return Swal.fire({
                icon: 'error',
                title: 'Qty Diterima Tidak Valid!',
                html: `Barang: <strong>${invalidItem.barang_name || '-'}</strong><br>
                    Qty diterima harus lebih dari 0`,
                confirmButtonColor: '#4e73df',
            });
        }

        Swal.fire({
            icon: 'question',
            title: 'Simpan Data?',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali'
        }).then((result) => {
            if (!result.isConfirmed) return;

            let id = $('#id').val();
            let data = new FormData(document.querySelector(".create-form"));
            data.append('listBarang', JSON.stringify(listStockSelected));

            const url = id 
                ? "<?= base_url('penerimaan-barang-broken/update'); ?>"
                : "<?= base_url('penerimaan-barang-broken/save'); ?>";

            $.ajax({
                url: url,
                method: "POST",
                data: data,
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: xhr => {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    setLoading();
                },
                complete: stopLoading,
                success: (res) => {
                    Swal.fire({
                        icon: res.status === false ? 'error' : 'success',
                        title: res.message,
                        confirmButtonColor: '#4e73df',
                    }).then((ok) => {
                        if (ok.isConfirmed && res.status !== false)
                            window.location.href = "<?= base_url('penerimaan-barang-broken'); ?>";
                    });
                }
            });
        });
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

                <?php if (!empty($penerimaanBarangBroken)) : ?>
                    $(".warehouse_id").val("<?= $penerimaanBarangBroken['warehouse_id'] ?>").trigger('change');
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

    function getListPo() {
        $.ajax({
            url: `<?= base_url('penerimaan-barang-broken/get-po'); ?>`,
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
                listStockAsal = [];
                listStockAsal = res.data.data;
                drawTableAsalBarang(res.data.data);
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
                        <input data-id="${v.id}" data-stok_total="${v.stok_total}" autocomplete="one-time-code" class="form-check-input child" type="radio">
                    </div>
                `
                ));

            newRow.append($('<td style="text-align:center;">').text(v.po_no));
            newRow.append($('<td style="text-align:center;">').text(v.supplier_name));
            newRow.append($('<td style="text-align:center;">').text(v.bc_type));
            newRow.append($('<td style="text-align:center;">').text(v.po_date));
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
        const $tbody = $('#selectedItemTable .body-table');
        $tbody.empty();

        if (!data || data.length === 0) {
            $('#foot-detail-table').show();
            return;
        } else {
            $('#foot-detail-table').hide();
        }

        let rowIndex = 1;
        data.forEach((item, i) => {
            const tr = $(`
                <tr data-index="${i}">
                    <td style="text-align:center;">${rowIndex++}</td>
                    <td>${item.po_no || '-'}</td>
                    <td>${item.supplier_name || '-'}</td>
                    <td>${item.po_date || '-'}</td>
                    <td>${item.spesifikasi_name || '-'}</td>
                    <td style="text-align:right;">
                        <input type="text" class="form-control qty-diterima" 
                            data-index="${i}" value="${greatFormatRupiah(item.qty_diterima) || 0}" />
                    </td>
                    <td><button type="button" class="btn btn-danger btn-sm btn-remove-row">Hapus</button></td>
                </tr>
            `);
            $tbody.append(tr);
        });

        // event hapus
        $tbody.off('click', '.btn-remove-row').on('click', '.btn-remove-row', function () {
            const index = $(this).closest('tr').data('index');
            listStockSelected.splice(index, 1);
            drawTableSelectedItem(listStockSelected);
        });

        $tbody.off('input', '.qty-diterima').on('input', '.qty-diterima', function() {
            const index = $(this).data('index');
            const diterimaVal = destroyFormatRupiah($(`.qty-diterima[data-index="${index}"]`).val());
            listStockSelected[index].qty_diterima = diterimaVal;
        });
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
            $(".no_penerimaan_barang").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("penerimaan-barang-broken/generate"); ?>`,
                method: "GET",
                data: {
                    warehouse_id: $('#warehouse_id option:selected').val(),
                    tanggal: $('#tanggal').val()
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".no_penerimaan_barang").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".no_penerimaan_barang").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".no_penerimaan_barang").val("");
                    }
                }
            })
        } else {
            $(".no_penerimaan_barang").attr("readonly", false);
            $(".no_penerimaan_barang").val("");
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
                    url: "<?= base_url("penerimaan-barang-broken/posting"); ?>",
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
                                window.location.href = "<?= base_url("penerimaan-barang-broken") ?>";
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
                    url: "<?= base_url("penerimaan-barang-broken/delete"); ?>",
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
                                window.location.href = "<?= base_url("penerimaan-barang-broken") ?>";
                            });
                        }
                    },
                });
            }
        })
    }
</script>

<?= $this->endSection(); ?>