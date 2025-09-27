<form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($dataPenerimaanBarang) ? encrypt($dataPenerimaanBarang['id']) : ""; ?>" />
    <?= csrf_field() ?>

    <div class="row">
        <div class="col-md-4">
            <div class="form-floating mb-3" style="height: 50px;">
                <div class="input-group input-group-password">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang['no_penerimaan_barang'] : "" ?>" type="text" class="form-control no_penerimaan_barang" id="no_penerimaan_barang" name="no_penerimaan_barang" placeholder="No. Penerimaan">
                        <label for="floatingInput">No. Penerimaan</label>
                    </div>
                    <div style="<?= !empty($dataPenerimaanBarang) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                        <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating mb-3" style="height: 50px;">
                <select <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> class="form-select multiple_spp_id" id="multiple_spp_id" name="multiple_spp_id" aria-label="Floating label select example">
                    <option value=""></option>
                    <?php if (!empty($dataPenerimaanBarang)) : ?>
                        <?php foreach ($dataSPPSelected as $d) : ?>
                            <option data-divisi_id="<?= $dataPenerimaanBarang['divisi_id'] ?>" selected value="<?= $d["id"]; ?>"><?= strtoupper($d["spp_no"]); ?></option>
                        <?php endforeach; ?>
                        <?php foreach ($dataSPP as $d) : ?>
                            <option data-divisi_id="<?= $d['divisi_id'] ?>" value="<?= $d["id"]; ?>"><?= strtoupper($d["spp_no"]); ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">Pilih Nomor SPP</option>
                        <?php foreach ($dataSPP as $d) : ?>
                            <option data-divisi_id="<?= $d['divisi_id'] ?>" value="<?= $d["id"]; ?>"><?= strtoupper($d["spp_no"]); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <label for="floatingInput">Pilih No SPP</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating mb-3" style="height: 50px;">
                <select <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : 'disabled=true') : ''; ?> class="form-select supplier_id" id="supplier_id" name="supplier_id" aria-label="Floating label select example">
                    <option value=""></option>
                    <?php if (!empty($dataPenerimaanBarang)): ?>
                        <?php foreach ($dataSupplier as $supplier) : ?>
                            <option value="<?= $supplier["id"]; ?>" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['supplier_id'] === $supplier["id"] ? "selected" : "") : ""; ?>><?= strtoupper($supplier["name"]); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <label for="floatingInput">Supplier</label>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-floating" style="height: 50px;">
                <select disabled <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : 'disabled=true') : ''; ?> class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                    <option value=""></option>
                    <?php if (!empty($dataPenerimaanBarang)) : ?>
                        <?php foreach ($dataDivisi as $divisi) : ?>
                            <option value="<?= $divisi["id"]; ?>" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['divisi_id'] === $divisi["id"] ? "selected" : "") : ""; ?>><?= strtoupper($divisi["divisi"]); ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php foreach ($dataDivisi as $divisi) : ?>
                            <option value="<?= $divisi["id"]; ?>" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['divisi_id'] === $divisi["id"] ? "selected" : "") : ""; ?>><?= strtoupper($divisi["divisi"]); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <label for="floatingInput">Departemen</label>
            </div>
            <small class="mb-3"><i>Hanya menampilkan departemen yang nomor PO nya belum sepenuhnya diterima</i></small>
        </div>
        <div class="col-md-4" style="display: none;">
            <div class="form-floating mb-3" style="height: 50px;">
                <select <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : 'disabled=true') : ''; ?> multiple class="form-select multiple_po_id" name="multiple_po_id[]" id="multiple_po_id[]">
                    <option value=""></option>
                    <?php if (!empty($dataPenerimaanBarang)) : ?>
                        <?php foreach (json_decode(($dataPenerimaanBarang['multiple_po_id'])) as $i => $id) : ?>
                            <option selected value="<?= $id ?>"><?= json_decode(($dataPenerimaanBarang['multiple_po_no']))[$i] ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating mb-3" style="height: 50px;">
                <select <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">
                    <option value=""></option>
                    <?php if (!empty($dataPenerimaanBarang)) : ?>
                        <?php foreach ($dataWarehouse as $warehouse) : ?>
                            <option value="<?= $warehouse["id"]; ?>" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['warehouse_id'] === $warehouse["id"] ? "selected" : "") : ""; ?>><?= strtoupper($warehouse["warehouse_name"]); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <label for="floatingInput">Warehouse</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating mb-3" style="height: 50px;">
                <select <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> class="form-select kemasan_id" id="kemasan_id" name="kemasan_id" aria-label="Floating label select example">
                    <option value=""></option>
                    <?php foreach ($dataKemasan as $kemasan) : ?>
                        <option value="<?= $kemasan["id"]; ?>" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['kemasan_id'] === $kemasan["id"] ? "selected" : "") : ""; ?>><?= strtoupper($kemasan["name"]); ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="floatingInput">Jenis Kemasan</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating mb-3" style="height: 50px;">
                <input <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> autocomplete="one-time-code" value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang['jumlah_kemasan'] : ""; ?>" type="text" class="form-control kemasan" id="jumlah_kemasan" name="jumlah_kemasan" placeholder="Jumlah Kemasan">
                <label for="floatingInput">Jumlah Kemasan</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="input-group input-group-password">
                <div class="form-floating mb-3" style="height: 50px;">
                    <input <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> autocomplete="one-time-code" value="<?= !empty($dataPenerimaanBarang) ?  date('d/m/Y', strtotime($dataPenerimaanBarang['tanggal'])) : date('d/m/Y'); ?>" onchange="changeStatus()" type="text" class="form-control tanggal_penerimaan_lpb" name="tanggal_penerimaan_lpb" id="tanggal_penerimaan_lpb" placeholder="Tanggal Barang Diterima">
                    <label for="floatingInput">Tanggal Barang Diterima</label>
                </div>
                <div class="input-group-prepend group-prepend-password align-items-center">
                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating" style="height: 50px;">
                <select <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> class="form-select aju_document_type" id="aju_document_type" name="aju_document_type" aria-label="Floating label select example">
                    <option value="">Pilih Dokumen Pabean</option>
                    <?php foreach ($dataAJU as $aju) : ?>
                        <option <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['bc_type'] === $aju["id"] ? "selected" : "") : ""; ?> value="<?= $aju["id"]; ?>"><?= $aju["value"]; ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="floatingInput">Jenis Dokumen Pabean (Opsional)</label>
            </div>
            <small class="mb-3"><i>Kosongkan jika non pabean</i></small>
        </div>

        <div class="col-md-4">
            <div class="form-floating mb-3" style="height: 50px;">
                <input <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> autocomplete="one-time-code" value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang['kemasan'] : ""; ?>" type="text" class="form-control kemasan" id="kemasan" name="kemasan" placeholder="Kemasan">
                <label for="floatingInput">Keterangan Kemasan (Opsional)</label>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-floating mb-3" style="height: 50px;">
                <input autocomplete="one-time-code" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang['no_surat_jalan'] : ""; ?>" type="text" class="form-control no_surat_jalan" id="no_surat_jalan" name="no_surat_jalan" placeholder="Nomor Surat Jalan">
                <label for="floatingInput">Nomor Surat Jalan (Opsional)</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating mb-3" style="height: 50px;">
                <input autocomplete="one-time-code" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang['no_invoice'] : ""; ?>" type="text" class="form-control no_invoice" id="no_invoice" name="no_invoice" placeholder="Nomor Invoice">
                <label for="floatingInput">Nomor Invoice (Opsional)</label>
            </div>
        </div>


        <div class="col-md-4">
            <div class="form-floating mb-3" style="height: 50px;">
                <input autocomplete="one-time-code" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['status_post'] === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['ongkos_kirim'] == 0 ? "" : number_format($dataPenerimaanBarang['ongkos_kirim'])) : ""; ?>" class="form-control ongkos_kirim" type="text" oninput="this.value=greatFormatRupiah(this.value)" id="ongkos_kirim" name="ongkos_kirim" placeholder="Ongkos Kirim">
                <label for="floatingInput">Ongkos Kirim (Opsional)</label>
            </div>
        </div>
    </div>
</form>

<div class="col-subtitle-modal">
    <div class="row mt-3">
        <div class="col-md-6">
            <label class="form-label font-weight-bold modal-sub-title">List Barang <?= !empty($dataPenerimaanBarang) ? "(Hanya Menampilkan Barang berdasarkan data yang sudah disimpan sebelumnya)" : "(Hanya Menampilkan Barang yang Belum Diterima Full)" ?></label>
        </div>
    </div>
</div>
<div class="row">
    <div class="table-responsive">
        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
            <thead class="thead-dark">
                <tr>
                    <th style="text-align: center;">No</th>
                    <th style="text-align: center;">Kode Barang</th>
                    <th style="text-align: center;">Nama Barang</th>
                    <th style="text-align: center;">No SPP</th>
                    <!-- <th style="text-align: center;">No PO</th> -->
                    <th style="text-align: center;">Satuan</th>
                    <th style="text-align: center;">Jml. Order</th>
                    <th style="text-align: center;">Jml. Diterima LPB ini</th>
                    <th style="text-align: center;">Jml. Diterima LPB ini (Konversi)</th>
                    <th style="text-align: center;">Jml. Diterima Total</th>
                    <th style="text-align: center;">Sisa Total</th>
                    <th class="label-harga" style="text-align: center;">Harga</th>
                    <th style="text-align: center;">Sub Total</th>
                    <th style="text-align: center;">Keterangan</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody class="body-detail-table" id="body-detail-table">

            </tbody>
            <tfoot class="foot-detail-table" id="foot-detail-table">
                <tr>
                    <td></td>
                    <td></td>
                    <td colspan="3" style="text-align: right;">GRAND TOTAL</td>
                    <td style="text-align: center;"><b>0</b></td>
                    <td style="text-align: center;"><b>0</b></td>
                    <td style="text-align: center;"><b>0</b></td>
                    <td style="text-align: center;"><b>0</b></td>
                    <td style="text-align: center;"><b>0</b></td>
                    <td style="text-align: center;"><b>0.00</b></td>
                    <td style="text-align: center;"><b>0.00</b></td>
                    <td style="text-align: center;"><b></b></td>
                    <td style="text-align: center;"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php if (empty($dataPenerimaanBarang)): ?>
    <script>
        $(document).ready(function() {
            changeStatus();
        });
    </script>
<?php endif; ?>

<!-- Untuk Placeholder Aja Soalnya ini Ngeload Html dari Ajax -->
<script>
    $(document).ready(function() {
        $('.multiple_po_id').select2({
            placeholder: "Pilih Nomor PO",
            theme: "bootstrap-5",
            allowClear: false
        });

    })
</script>

<script>
    var listData = []; // UNTUK FRONT END
    var listDataServer = []; // YANG DIKIRIM KE PHP

    $(".tanggal_penerimaan_lpb").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    // SELECT2
    $('.multiple_po_id').select2({
        placeholder: "Pilih Nomor PO",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function() {
        let arr = $('.multiple_po_id').val();
        $.ajax({
            url: `<?= base_url("penerimaan-barang-lokal-bp/list-barang"); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading()
            },
            complete: function() {
                stopLoading()
            },
            data: {
                am_purchase_order_id: JSON.stringify(arr),
                penerimaan_barang_id: $('.id').val()
            },
            dataType: "json",
            success: function(res) {
                // INIT KOSONG
                listData = [];
                listDataServer = [];
                // ISI DATA
                listData = res;
                listDataServer = res;
                drawTable(listData);
            }
        })
    });

    $('.supplier_id').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        getPoBySpp();
    });

    $('.warehouse_id').select2({
        placeholder: "Pilih Warehouse Penerimaan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // changeStatus();
    });


    $('.divisi_id').select2({
        placeholder: "Pilih Departemen Purchase Order",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {

        // GET WAREHOUSES
        $.ajax({
            url: `<?= base_url('penerimaan-barang-lokal-bp/warehouse'); ?>`,
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
        listData = [];
        drawTable(listData);
    });

    $('.kemasan_id').select2({
        placeholder: "Pilih Jenis Kemasan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {

    });

    $('.btn-discard').click(function() {
        $('.detail-modal').modal('hide');
    })

    // $('.aju_document_type').select2({
    //     placeholder: "Pilih Dokumen Bea Cukai",
    //     theme: "bootstrap-5",
    //     allowClear: true
    // });

    $('.kemasan_id').select2({
        placeholder: "Pilih Jenis Kemasan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {

    });

    $(".multiple_spp_id").select2({
        placeholder: "Pilih Nomor SPP",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        let arr = $('.multiple_spp_id').val();
        // GET SIPPLIER
        <?php if (empty($dataPenerimaanBarang)): ?>
            $.ajax({
                url: `<?= base_url('penerimaan-barang-lokal-bp/get-supplier-by-spp'); ?>`,
                method: "GET",
                beforeSend: function() {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                data: {
                    spp_id: arr
                },
                dataType: "json",
                success: function(res) {
                    $(".supplier_id").empty()
                    $(".supplier_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".supplier_id").append(`<option selected value="${item.id}">${item.name}</option>`)
                    })
                    $(".supplier_id").change();

                }
            });
        <?php else: ?>
            $('.multiple_po_id').change();
        <?php endif; ?>

    });

    function getPoBySpp() {
        let arr = $('.multiple_spp_id').val();
        // GET PO
        $.ajax({
            url: `<?= base_url('penerimaan-barang-lokal-bp/get-po'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                id: $(".supplier_id option:selected").val(),
                divisi_id: $(".divisi_id option:selected").val(),
                spp_id: `[${arr}]`
            },
            dataType: "json",
            success: function(res) {
                $(".multiple_po_id").empty()
                $(".multiple_po_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".multiple_po_id").append(`<option selected value="${item.id}">${item.po_no}</option>`)
                })
                $(".multiple_po_id").change();

                // Set Divisi Id
                var divisi_id = $(".multiple_spp_id option:selected").data('divisi_id');
                $('.divisi_id').val(divisi_id);
                $('.divisi_id').change();
            }
        });
    }


    $(document).on('select2:open', function(e) {
        // Pastikan ini adalah elemen yang kita inginkan (form-select)
        const target = e.target;
        if ($(target).hasClass('form-select')) {
            // Cari input search yang baru saja dibuka
            let searchInput = document.querySelector('.select2-container--open .select2-search__field');
            if (searchInput) {
                searchInput.focus();
            }
        }
    });

    $('.supplier_id, .warehouse_id, .aju_document_type, .multiple_po_id, .divisi_id, .kemasan_id, .multiple_spp_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.supplier_id, .warehouse_id, .aju_document_type, .multiple_po_id, .divisi_id, .kemasan_id, .multiple_spp_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.supplier_id, .warehouse_id, .aju_document_type, .multiple_po_id, .divisi_id, .kemasan_id, .multiple_spp_id')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    // Validator Detail
    // var validator_detail = $(".detail-form").validate({
    //     rules: {
    //         jml_diterima_lpb: {
    //             required: true,
    //             number: true,
    //             min: -1
    //         },
    //         sisa_total: {
    //             number: true,
    //             min: -1
    //         }
    //     },
    //     messages: {
    //         jml_diterima_lpb: {
    //             required: "Jumlah diterima wajib diisi",
    //             number: "Masukkan hanya angka",
    //             min: "Tidak boleh minus"
    //         },
    //         sisa_total: {
    //             min: "Sisa total tidak boleh minus"
    //         }
    //     },
    //     errorElement: 'span',
    //     errorClass: 'text-danger',
    //     errorPlacement: function(error, element) {
    //         var elem = $(element);
    //         if (elem.hasClass("select2-hidden-accessible")) {
    //             element = $("#select2-" + elem.attr("id") + "-container").parent();
    //             error.insertAfter(element);
    //         } else {
    //             error.insertAfter(element);
    //         }
    //     },
    //     highlight: function(element) {
    //         $(element).closest('.form-group').addClass('has-error');
    //         $(element).addClass('select-class');

    //     },
    //     unhighlight: function(element) {
    //         $(element).closest('.form-group').removeClass('has-error');
    //         $(element).removeClass('select-class');
    //     },
    // });

    // Validator Parent
    var validator = $(".create-form").validate({
        rules: {
            no_penerimaan_barang: {
                required: true,
            },
            supplier_id: {
                required: true,
            },
            warehouse_id: {
                required: true
            },
            kemasan_id: {
                required: true
            },
            jumlah_kemasan: {
                required: true,
                number: true,
                min: 0
            },
            divisi_id: {
                required: true
            },
            tanggal_penerimaan_lpb: {
                required: true
            }
        },
        messages: {
            no_penerimaan_barang: {
                required: "Nomor penerimaan wajib diisi"
            },
            supplier_id: {
                required: "Supplier wajib diisi"
            },
            warehouse_id: {
                required: "Warehouse wajib diisi"
            },
            kemasan_id: {
                required: "Jenis kemasan wajib diisi"
            },
            jumlah_kemasan: {
                required: "Jumlah kemasan wajib diisi",
                number: "Masukkan hanya angka",
                min: "Tidak boleh minus"
            },
            divisi_id: {
                required: "Departemen wajib diisi"
            },
            tanggal_penerimaan_lpb: {
                required: "Tanggal barang diterima wajib diisi"
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

    $('.btn-submit-parent').click(function(e) {
        e.preventDefault();
        if ($('.create-form').valid()) {
            if (listData.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Pilih nomor PO dahulu",
                    confirmButtonColor: '#4e73df',
                })
            } else {
                Swal.fire({
                    icon: 'question',
                    title: 'Simpan Data?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Kembali',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const csrf = $(`[name="${csrfToken}"]`);
                        var id = $('#id').val();
                        var divisiId = $('#divisi_id').val();
                        var warehouseId = $('#warehouse_id').val();
                        var supplierId = $('#supplier_id').val();
                        var po_no = $('.multiple_po_id').select2('data').map(function(elem) {
                            return elem.text;
                        });
                        var ongkosKirim = destroyFormatRupiah($('#ongkos_kirim').val() || 0);

                        var formData = new FormData(document.querySelector(".create-form"));
                        formData.append("acceptance_type", po_no.length > 1 ? "MULTIPLE ORDER" : "SINGLE ORDER");
                        formData.append("multiple_po_id", JSON.stringify($('.multiple_po_id').val()));
                        formData.append("multiple_po_no", JSON.stringify(po_no));
                        formData.append("divisi_id", divisiId);
                        formData.append("supplier_id", supplierId);
                        formData.append("warehouse_id", warehouseId);

                        formData.append("barangs", JSON.stringify(listData.result));
                        formData.set("ongkos_kirim", ongkosKirim);

                        if (id) {
                            formData.append("id", id);
                            $.ajax({
                                url: "<?= base_url("penerimaan-barang-lokal-bp/update"); ?>",
                                data: formData,
                                method: "POST",
                                dataType: "json",
                                processData: false,
                                contentType: false,
                                beforeSend: function(xhr) {
                                    setLoading();
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                },
                                complete: function() {
                                    stopLoading();
                                },
                                success: function(response) {
                                    csrf.val(response.token);
                                    if (response.status) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        }).then(() => {
                                            window.location.href = "<?= base_url('penerimaan-barang-lokal-bp') ?>"
                                        });
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
                            $.ajax({
                                url: "<?= base_url("penerimaan-barang-lokal-bp/insert"); ?>",
                                data: formData,
                                method: "POST",
                                dataType: "json",
                                processData: false,
                                contentType: false,
                                beforeSend: function(xhr) {
                                    setLoading();
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                },
                                complete: function() {
                                    stopLoading();
                                },
                                success: function(response) {
                                    csrf.val(response.token);
                                    if (response.status) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        }).then(() => {
                                            window.location.href = "<?= base_url("penerimaan-barang-lokal-bp"); ?>";
                                        });
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

                })
            }

        }
    })

    $('.btn-submit-detail').click(function(e) {
        e.preventDefault();
        var indexToRemove = -1;
        var jmlDiterimaLpb = Number(destroyFormatRupiah($('.jml_diterima_lpb').val()));
        var jmlDiterimaTotal = Number(destroyFormatRupiah($('.jml_diterima_total').val()));
        var sisaTotal = Number(destroyFormatRupiah($('.sisa_total').val()));

        if (jmlDiterimaLpb < 0) {
            Swal.fire({
                icon: 'error',
                title: "Qty Diterima Saat Ini Tidak Boleh Minus",
                confirmButtonColor: '#4e73df',
            })

        } else if (jmlDiterimaTotal < 0) {
            Swal.fire({
                icon: 'error',
                title: "Qty Diterima Total Tidak Boleh Minus",
                confirmButtonColor: '#4e73df',
            })
        } else if (sisaTotal < 0) {
            Swal.fire({
                icon: 'error',
                title: "Qty Sisa Tidak Boleh Minus",
                confirmButtonColor: '#4e73df',
            })
        } else {
            for (var i = 0; i < listData.result.length; i++) {
                if (Number(listData.result[i].am_purchase_order_details_id) == Number($('.am_purchase_order_details_id').val()) && Number(listData.result[i].am_purchase_order_id) == Number($('.am_purchase_order_id').val())) {
                    var jml_diterima_lpb = Number(destroyFormatRupiah($('.jml_diterima_lpb').val()));
                    var nilai_konversi = listData.result[i].nilai_konversi;

                    listData.result[i].jml_diterima_lpb = Number(destroyFormatRupiah($('.jml_diterima_lpb').val()));
                    listData.result[i].jml_diterima_total = Number(destroyFormatRupiah($('.jml_diterima_total').val()));
                    listData.result[i].sisa_total = Number(destroyFormatRupiah($('.sisa_total').val()));
                    listData.result[i].sub_total = Number(destroyFormatRupiah($('.sub_total').val()));
                    listData.result[i].jml_diterima_lpb_konversi = Number(jml_diterima_lpb * nilai_konversi);
                    drawTable(listData);
                    $('.detail-modal').modal('hide');
                    break;
                }
            }
        }
    });

    $('.jml_diterima_lpb').keyup(function() {
        var item = null;
        var jml_order = destroyFormatRupiah($('.jml_order').val());
        var sub_total_po = destroyFormatRupiah($('.sub_total_po').val());
        var jml_diterima_lpb = Number(destroyFormatRupiah($(this).val())) || 0;
        var jml_diterima_lpb_last = Number($('.jml_diterima_lpb_last').val()) || 0;
        if (jml_diterima_lpb == 0) {
            for (var i = 0; i < listData.result.length; i++) {
                if (Number(listData.result[i].am_purchase_order_details_id) == Number($('.am_purchase_order_details_id').val()) && Number(listData.result[i].am_purchase_order_id) == Number($('.am_purchase_order_id').val())) {
                    item = listData.result[i];
                    var jml_diterima_total_now = Number(item.jml_diterima_total - jml_diterima_lpb_last);
                    var sisa_total_now = Math.floor(Number(item.sisa_total + jml_diterima_lpb_last) * 1000) / 1000;
                    // var sub_total = Math.floor(Number(jml_diterima_lpb) * Number(item.harga) * 1000) / 1000;
                    // $('.sub_total').val('' +
                    //     greatFormatRupiah(Math.round(sub_total)));
                    // $('.jml_diterima_total').val(greatFormatRupiah(jml_diterima_total_now));
                    // $('.sisa_total').val(greatFormatRupiah(sisa_total_now));

                    if (jml_diterima_lpb == jml_order) {
                        // Total
                        var sub_total = sub_total_po;
                    } else {
                        // Parsial
                        var sub_total = (Number(jml_diterima_lpb) * Number(item.harga));
                    }
                    $('.sub_total').val('' +
                        greatFormatRupiah((sub_total.toFixed(2))));
                    $('.jml_diterima_total').val(greatFormatRupiah(jml_diterima_total_now));
                    $('.sisa_total').val(greatFormatRupiah(sisa_total_now));

                    break;
                }
            }


        } else {
            for (var i = 0; i < listData.result.length; i++) {
                if (Number(listData.result[i].am_purchase_order_details_id) == Number($('.am_purchase_order_details_id').val()) && Number(listData.result[i].am_purchase_order_id) == Number($('.am_purchase_order_id').val())) {
                    item = listData.result[i];
                    var jml_diterima_total_now = (Number(item.jml_diterima_total) + Number(jml_diterima_lpb) - jml_diterima_lpb_last);
                    var sisa_total_now = Math.floor((item.jml_order - jml_diterima_total_now) * 1000) / 1000;

                    if (jml_diterima_lpb == jml_order) {
                        // Total
                        var sub_total = sub_total_po;

                    } else {
                        // Parsial
                        var sub_total = (Number(jml_diterima_lpb) * Number(item.harga));
                    }

                    $('.sub_total').val('' +
                        greatFormatRupiah(sub_total.toFixed(2)));
                    $('.jml_diterima_total').val(greatFormatRupiah(jml_diterima_total_now));
                    $('.sisa_total').val(greatFormatRupiah(sisa_total_now));
                    break;
                }
            }
        }
    });

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

    function drawTable(listData) {
        const table = $('#dataTable');
        table.find('tbody').empty();
        if (listData.length == 0) {
            table.find('tfoot').empty();
            var newRow = $('<tr>');
            newRow.append($('<td></td>'));
            newRow.append($('<td></td>'));
            newRow.append($('<td style="text-align:right;" colspan="3"><b>GRAND TOTAL</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>0</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>0</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>0</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>0</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>0</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>0.0</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>0.0</b></td>'));
            newRow.append($('<td></td>'));
            newRow.append($('<td></td>'));
            table.find('tfoot').append(newRow);
        } else {
            var no = 1;
            var jmlDiterimaLPBTotal = 0;
            var jmlDiterimaTotal = 0;
            var sisaTotal = 0;
            var subTotal = 0;
            var jmlOrderTotal = 0;
            var hargaTotal = 0;
            var jmlDiterimaLpbKonversi = 0;

            $.each(listData.result, function(i, v) {
                var newRow = $('<tr>');
                newRow.append($('<td>').text(no++));
                newRow.append($('<td>').text(v.kode_barang));
                newRow.append($('<td>').text(v.nama_barang));
                newRow.append($('<td>').text(v.spp_no));
                // newRow.append($('<td>').text(v.po_no));
                newRow.append($('<td>').text(v.satuan));
                newRow.append($('<td>').text(greatFormatRupiah(parseFloat(v.jml_order))));
                newRow.append($('<td>').text(greatFormatRupiah(parseFloat(v.jml_diterima_lpb))));
                newRow.append(
                    $('<td>').text(greatFormatRupiah(parseFloat(v.jml_diterima_lpb_konversi)) + " (" + v.satuan_konversi + ")")
                );
                newRow.append($('<td>').text(greatFormatRupiah(parseFloat(v.jml_diterima_total))));
                newRow.append($('<td>').text(greatFormatRupiah(parseFloat(v.sisa_total))));
                newRow.append($('<td>').text(greatFormatRupiah(v.harga.toFixed(2))));
                newRow.append($('<td>').text(greatFormatRupiah(v.sub_total.toFixed(2))));
                newRow.append($('<td>').text(v.keterangan));
                newRow.append($('<td>').html(
                    <?php if (!empty($dataPenerimaanBarang)) : ?> <?php if ($dataPenerimaanBarang['status_post'] === "FINISH") : ?> `-`
                        <?php else : ?> `
                <button class="btn btn-warning posting-spp mr-1" onclick="editModal('${v.am_purchase_order_id}', '${v.am_purchase_order_details_id}')">
                    <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                </button>
                  <button class="btn btn-danger posting-spp mr-1" onclick="deleteDetail('${v.am_purchase_order_id}', '${v.am_purchase_order_details_id}')">
                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                </button>
                `
                        <?php endif; ?> <?php else : ?> `
                <button class="btn btn-warning posting-spp mr-1" onclick="editModal('${v.am_purchase_order_id}', '${v.am_purchase_order_details_id}')">
                    <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                </button>
                 <button class="btn btn-danger posting-spp mr-1" onclick="deleteDetail('${v.am_purchase_order_id}', '${v.am_purchase_order_details_id}')">
                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                </button>
                `
                    <?php endif; ?>
                ));
                table.find('tbody').append(newRow);

                jmlDiterimaLPBTotal += Number(v.jml_diterima_lpb) || 0;
                jmlDiterimaTotal += Number(v.jml_diterima_total) || 0;
                sisaTotal += Number(v.sisa_total) || 0;
                subTotal += Number(v.sub_total) || 0;
                jmlOrderTotal += Number(v.jml_order) || 0;
                hargaTotal += Number(v.harga) || 0;
                jmlDiterimaLpbKonversi += Number(v.jml_diterima_lpb_konversi) || 0;
            });
            table.find('tfoot').empty();
            var newRow = $('<tr>');
            newRow.append($('<td></td>'));
            newRow.append($('<td></td>'));
            newRow.append($('<td style="text-align:right;" colspan="3"><b>GRAND TOTAL</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>' + greatFormatRupiah(Math.floor(jmlOrderTotal * 1000) / 1000) + '</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>' + greatFormatRupiah(Math.floor(jmlDiterimaLPBTotal * 1000) / 1000) + '</b></td>'));
            newRow.append($('<td style="text-align:left;">' + greatFormatRupiah(Math.floor(jmlDiterimaLpbKonversi * 1000) / 1000) + '</td>'));
            newRow.append($('<td style="text-align:left;"><b>' + greatFormatRupiah(Math.floor(jmlDiterimaTotal * 1000) / 1000) + '</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>' + greatFormatRupiah(Math.floor(sisaTotal * 1000) / 1000) + '</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>' + greatFormatRupiah(parseFloat(hargaTotal).toFixed(2) || 0) + '</b></td>'));
            newRow.append($('<td style="text-align:left;"><b>' + greatFormatRupiah(parseFloat(subTotal).toFixed(2) || 0) + '</b></td>'));
            newRow.append($('<td></td>'));
            newRow.append($('<td></td>'));
            table.find('tfoot').append(newRow);
        }
    }

    function editModal(am_purchase_order_id, am_purchase_order_details_id) {
        $('.detail-modal').modal('show');
        $('.title-detail-name').text("Update Penerimaan ");

        var item = null;
        for (var i = 0; i < listData.result.length; i++) {
            if (Number(listData.result[i].am_purchase_order_details_id) == Number(am_purchase_order_details_id) && Number(listData.result[i].am_purchase_order_id) == Number(am_purchase_order_id)) {
                item = listData.result[i];
                break;
            }
        }

        $('.am_purchase_order_id').val(item.am_purchase_order_id);
        $('.am_purchase_order_details_id').val(item.am_purchase_order_details_id);
        $('.po_no').val(item.po_no);
        $('.kode_barang').val(item.kode_barang);
        $('.nama_barang').val(item.nama_barang);
        $('.satuan_order').val(item.satuan);
        $('.jml_order').val(greatFormatRupiah(item.jml_order));
        $('.keterangan').val(item.keterangan);
        $('.jml_diterima_lpb').val(item.jml_diterima_lpb == 0 ? '' : greatFormatRupiah(item.jml_diterima_lpb));
        $('.jml_diterima_total').val(greatFormatRupiah(parseFloat(item.jml_diterima_total)));
        $('.sisa_total').val(greatFormatRupiah(parseFloat(item.sisa_total)));
        $('.nama_barang_dokumen').val(item.nama_barang_master);
        $('.harga_satuan').val("" + greatFormatRupiah(Number(item.harga) || 0));
        $('.sub_total').val("" + greatFormatRupiah(Number(item.sub_total) || 0));
        $('.jml_diterima_lpb_last').val(item.jml_diterima_lpb);
        $('.sub_total_po').val(item.sub_total_po);
    }

    function deleteDetail(am_purchase_order_id, am_purchase_order_details_id) {
        var indexToRemove = -1;
        for (var i = 0; i < listData.result.length; i++) {
            if (Number(listData.result[i].am_purchase_order_details_id) == Number(am_purchase_order_details_id) && Number(listData.result[i].am_purchase_order_id) == Number(am_purchase_order_id)) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listData.result.splice(indexToRemove, 1);
        }
        drawTable(listData);
    }

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $.ajax({
                url: `<?= base_url("/penerimaan-barang-lokal-bp/generate-lpb-no"); ?>`,
                method: "GET",
                dataType: "json",
                data: {
                    tanggal: $('#tanggal_penerimaan_lpb').val()
                },
                success: function(res) {
                    if (res.status) {
                        $(".no_penerimaan_barang").val(res.data);
                        $(".no_penerimaan_barang").attr("readonly", true);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })

                    }
                }
            })
        } else {
            $(".no_penerimaan_barang").attr("readonly", false);
            $(".no_penerimaan_barang").val("");
        }
    }
</script>

<?php if (!empty($dataPenerimaanBarang)) : ?>
    <script>
        // $('.multiple_po_id').change();
        let arr = $('.multiple_po_id').val();
        $.ajax({
            url: `<?= base_url("penerimaan-barang-lokal-bp/list-barang"); ?>`,
            method: "GET",
            data: {
                am_purchase_order_id: JSON.stringify(arr),
                penerimaan_barang_id: $('.id').val(),
                is_init_edit: 1
            },
            dataType: "json",
            success: function(res) {
                // INIT KOSONG
                listData = [];
                listDataServer = [];
                // ISI DATA
                listData = res;
                listDataServer = res;
                drawTable(listData);
            }
        });

        function print(url) {
            window.open(url, "_blank");
        }

        $('.posting-lpb').click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Posting LPB ini?',
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
                        url: "<?= base_url("penerimaan-barang-lokal-bp/posting"); ?>",
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
                                    window.location.href = "<?= base_url('penerimaan-barang-lokal-bp') ?>"
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                csrf.val(response.token);
                            }
                        },
                    });
                }
            })
        })

        $('.delete-parent').click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Hapus LPB ini?',
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
                        url: "<?= base_url("penerimaan-barang-lokal-bp/delete"); ?>",
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
                                    window.location.href = "<?= base_url('penerimaan-barang-lokal-bp') ?>"
                                });
                            }
                        },
                    });
                }
            })
        });
    </script>
<?php endif; ?>