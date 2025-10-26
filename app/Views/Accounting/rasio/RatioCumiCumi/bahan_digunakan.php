<div class="row justify-content-end">
    <div class="col mb-3">
        <label class="form-label font-weight-bold lable-title">Data Rasio Raw Material I</label>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="selectedItemTable" width="100%" border="1" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th style="text-align: center;" rowspan="2">No</th>
                        <th style="text-align: center;" colspan="5">Data Pembelian</th>
                        <th style="text-align: center;" colspan="4">Data Penerimaan</th>
                    </tr>
                    <tr>
                        <th style="text-align: center;">Spesifikasi</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: center;">Harga Total</th>
                        <th style="text-align: center;">Harga Satuan</th>
                        <th style="text-align: center;">Satuan</th>

                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: center;">Harga Total</th>
                        <th style="text-align: center;">Harga Satuan</th>
                        <th style="text-align: center;">Satuan</th>
                    </tr>
                </thead>
                <tbody class="body-detail-table">
                </tbody>
                <tfoot style="background: #ffffff !important;" class="tfoot-detail-table" id="tfoot-detail-table">
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
<div class="row">
    <div class="col mb-3">
        <label class="form-label font-weight-bold lable-title">Data Total Pembelian Barang</label>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-4">
        <div class="form-floating mb-3" style="height: 50px;">
            <input readonly placeholder="Qty" value="" class="form-control qtyTotalPembelian" id="qtyTotalPembelian" name="qtyTotalPembelian" aria-label="Floating label select example" />
            <label for="floatingInput" style="z-index: 1;">Qty</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-floating mb-3" style="height: 50px;">
            <input readonly placeholder="Harga Total" value="" class="form-control hargaTotalPembelian" id="hargaTotalPembelian" name="hargaTotalPembelian" aria-label="Floating label select example" />
            <label for="floatingInput" style="z-index: 1;">Harga Total</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-floating mb-3" style="height: 50px;">
            <input readonly placeholder="Rata-rata Harga Satuan" value="" class="form-control hargaSatuanPembelian" id="hargaSatuanPembelian" name="hargaSatuanPembelian" aria-label="Floating label select example" />
            <label for="floatingInput" style="z-index: 1;">Rata-rata Harga Satuan</label>
        </div>
    </div>
</div>
<div class="row">
    <div class="col mb-3">
        <label class="form-label font-weight-bold lable-title">Data Total Penerimaan Barang</label>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-4">
        <div class="form-floating mb-3" style="height: 50px;">
            <input readonly placeholder="Qty" value="" class="form-control qtyTotalPenerimaan" id="qtyTotalPenerimaan" name="qtyTotalPenerimaan" aria-label="Floating label select example" />
            <label for="floatingInput" style="z-index: 1;">Qty</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-floating mb-3" style="height: 50px;">
            <input readonly placeholder="Harga Total" value="" class="form-control hargaTotalPenerimaan" id="hargaTotalPenerimaan" name="hargaTotalPenerimaan" aria-label="Floating label select example" />
            <label for="floatingInput" style="z-index: 1;">Harga Total</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-floating mb-3" style="height: 50px;">
            <input readonly placeholder="Rata-rata Harga Satuan" value="" class="form-control hargaSatuanPenerimaan" id="hargaSatuanPenerimaan" name="hargaSatuanPenerimaan" aria-label="Floating label select example" />
            <label for="floatingInput" style="z-index: 1;">Rata-rata Harga Satuan</label>
        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="selectedItemTableDigunakan" width="100%" border="1" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th style="text-align: center;" rowspan="2">No</th>
                        <th style="text-align: center;" colspan="5">Data Bahan Digunakan</th>
                    </tr>
                    <tr>
                        <th style="text-align: center;">Spesifikasi</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: center;">Harga Total</th>
                        <th style="text-align: center;">Harga Satuan</th>
                        <th style="text-align: center;">Satuan</th>
                    </tr>
                </thead>
                <tbody class="body-detail-table-digunakan">
                </tbody>
                <tfoot style="background: #ffffff !important;" class="tfoot-detail-table-digunakan" id="tfoot-detail-table-digunakan">
                    <tr>
                        <td colspan="7" style="text-align: center;">
                            Tidak Ada Barang
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <div class="col mb-3">
        <label class="form-label font-weight-bold lable-title">Data Total Barang Digunakan</label>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-4">
        <div class="form-floating mb-3" style="height: 50px;">
            <input readonly placeholder="Qty" value="" class="form-control qtyTotalDigunakan" id="qtyTotalDigunakan" name="qtyTotalDigunakan" aria-label="Floating label select example" />
            <label for="floatingInput" style="z-index: 1;">Qty</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-floating mb-3" style="height: 50px;">
            <input readonly placeholder="Harga Total" value="" class="form-control hargaTotalDigunakan" id="hargaTotalDigunakan" name="hargaTotalDigunakan" aria-label="Floating label select example" />
            <label for="floatingInput" style="z-index: 1;">Harga Total</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-floating mb-3" style="height: 50px;">
            <input readonly placeholder="Rata-rata Harga Satuan" value="" class="form-control hargaSatuanDigunakan" id="hargaSatuanDigunakan" name="hargaSatuanDigunakan" aria-label="Floating label select example" />
            <label for="floatingInput" style="z-index: 1;">Rata-rata Harga Satuan</label>
        </div>
    </div>
</div>
<div class="row">
    <div class="col mb-3">
        <label class="form-label font-weight-bold lable-title">Data Biaya Tambahan</label>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-floating mb-3" style="height: 50px;">
            <select class="form-select akun_coa_subsidi" name="akun_coa_subsidi" id="akun_coa_subsidi" onchange="getDataJurnalSubsidi()">
                <option value=""></option>
                <?php
                if (!empty($subAkuns)) {
                    foreach ($subAkuns as $sub) {
                ?>
                        <option value="<?= $sub->id; ?>" <?= !empty($rasio) && $rasio->subsidi_coa_id == $sub->id ? "selected" : "" ?>><?= $sub->no_sub; ?> <?= $sub->nama_sub; ?></option>
                <?php
                    }
                }
                ?>
            </select>
            <label for="floatingInput" style="z-index: 1;">Akun COA Subsidi</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-floating mb-3" style="height: 50px;">
            <select class="form-select akun_coa_biaya" name="akun_coa_biaya" id="akun_coa_biaya" onchange="getDataJurnalLain()">
                <option value=""></option>
                <?php
                if (!empty($subAkuns)) {
                    foreach ($subAkuns as $sub) {
                ?>
                        <option value="<?= $sub->id; ?>" <?= !empty($rasio) && $rasio->biaya_coa_id == $sub->id ? "selected" : "" ?>><?= $sub->no_sub; ?> <?= $sub->nama_sub; ?></option>
                <?php
                    }
                }
                ?>
            </select>
            <label for="floatingInput" style="z-index: 1;">Akun COA Biaya Lain-lain</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-floating mb-3" style="height: 50px;">
            <select class="form-select akun_coa_kopek" name="akun_coa_kopek" id="akun_coa_kopek" onchange="getDataJurnalKopek()">
                <option value=""></option>
                <?php
                if (!empty($subAkuns)) {
                    foreach ($subAkuns as $sub) {
                ?>
                        <option value="<?= $sub->id; ?>" <?= !empty($rasio) && $rasio->kopek_coa_id == $sub->id ? "selected" : "" ?>><?= $sub->no_sub; ?> <?= $sub->nama_sub; ?></option>
                <?php
                    }
                }
                ?>
            </select>
            <label for="floatingInput" style="z-index: 1;">Akun COA Kopek</label>
        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-4">
        <div class="form-floating mb-3" style="height: 50px;">
            <input placeholder="Jumlah Biaya Subsidi" value="" class="form-control biayaSubsidi" id="biayaSubsidi" name="biayaSubsidi" aria-label="Floating label select example" />
            <label for="floatingInput" style="z-index: 1;">Jumlah Biaya Subsidi</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-floating mb-3" style="height: 50px;">
            <input placeholder="Jumlah Biaya Lain-lain" value="" class="form-control biayaLain" id="biayaLain" name="biayaLain" aria-label="Floating label select example" />
            <label for="floatingInput" style="z-index: 1;">Jumlah Biaya Lain-lain</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-floating mb-3" style="height: 50px;">
            <input placeholder="Jumlah Biaya Kopek" value="" class="form-control biayaKopek" id="biayaKopek" name="biayaKopek" aria-label="Floating label select example" />
            <label for="floatingInput" style="z-index: 1;">Jumlah Biaya Kopek</label>
        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-sm-2">
        <button class="btn btn-show-detail btn-add" data-btn="detail-modal" id="select-item-btn-alokasi-biaya" type="button">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Hitung Alokasi Biaya
        </button>
    </div>
</div>
<div class="row">
    <div class="col mb-3">
        <label class="form-label font-weight-bold lable-title">Data Total Setelah Alokasi</label>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-4">
        <div class="form-floating mb-3" style="height: 50px;">
            <input readonly placeholder="Qty" value="" class="form-control qtyTotalSetelahAlokasi" id="qtyTotalSetelahAlokasi" name="qtyTotalSetelahAlokasi" aria-label="Floating label select example" />
            <label for="floatingInput" style="z-index: 1;">Qty</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-floating mb-3" style="height: 50px;">
            <input readonly placeholder="Harga Total" value="" class="form-control hargaTotalSetelahAlokasi" id="hargaTotalSetelahAlokasi" name="hargaTotalSetelahAlokasi" aria-label="Floating label select example" />
            <label for="floatingInput" style="z-index: 1;">Harga Total</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-floating mb-3" style="height: 50px;">
            <input readonly placeholder="Rata-rata Harga Satuan" value="" class="form-control hargaSatuanSetelahAlokasi" id="hargaSatuanSetelahAlokasi" name="hargaSatuanSetelahAlokasi" aria-label="Floating label select example" />
            <label for="floatingInput" style="z-index: 1;">Rata-rata Harga Satuan</label>
        </div>
    </div>
</div>
<div class="row">
    <div class="col mb-3">
        <label class="form-label font-weight-bold lable-title">Data Barang Setelah Alokasi</label>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="selectedItemTableAlokasi" width="100%" border="1" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th style="text-align: center;" rowspan="2">No</th>
                        <th style="text-align: center;" colspan="5">Data Barang</th>
                    </tr>
                    <tr>
                        <th style="text-align: center;">Spesifikasi</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: center;">Harga Total</th>
                        <th style="text-align: center;">Harga Satuan</th>
                        <th style="text-align: center;">Satuan</th>
                    </tr>
                </thead>
                <tbody class="body-detail-table-alokasi">
                </tbody>
                <tfoot style="background: #ffffff !important;" class="tfoot-detail-table-alokasi" id="tfoot-detail-table-alokasi">
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
<script>
    // Akun AR
    $('#akun_coa_subsidi, #akun_coa_biaya, #akun_coa_kopek').select2({
        placeholder: "Pilih Akun COA",
        theme: "bootstrap-5",
        allowClear: true
    })

    //CSS SELECT2 FLOATING LABEL
    $('#akun_coa_subsidi, #akun_coa_biaya, #akun_coa_kopek')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('#select-item-btn-alokasi-biaya').click(function() {
        list_items_barang_digunakan_alokasi = [];

        var amount = $('#hargaTotalDigunakan').val() ? parseFloat($('#hargaTotalDigunakan').val().replace(/[Rp.]/g, '')) : 0;
        var qtyTotalPenerimaan = $('#qtyTotalDigunakan').val() ? parseFloat($('#qtyTotalDigunakan').val().replace(/[Rp.]/g, '')) : 0;
        var biayaSubsidi = $('#biayaSubsidi').val() ? parseFloat($('#biayaSubsidi').val().replace(/[Rp.]/g, '')) : 0;
        var biayaLain = $('#biayaLain').val() ? parseFloat($('#biayaLain').val().replace(/[Rp.]/g, '')) : 0;
        var biayaKopek = $('#biayaKopek').val() ? parseFloat($('#biayaKopek').val().replace(/[Rp.]/g, '')) : 0;

        var hargaTotalBiaya = biayaSubsidi + biayaLain + biayaKopek;

        var hargaSatuan = 0;
        list_items_barang_digunakan.forEach((item, index) => {
            hargaSatuan = parseFloat(item.hargaSatuanPO) + (parseFloat(hargaTotalBiaya) / parseFloat(list_items_barang_digunakan.length));
            totalHarga = (parseFloat(hargaSatuan) * parseFloat(item.totalQtyPO));
            list_items_barang_digunakan_alokasi.push({
                'barang1_id': item.barang1_id,
                'barang2_id': item.barang2_id,
                'barang_name': item.barang_name,
                'hargaSatuan': hargaSatuan,
                'no_dokumen': item.no_dokumen,
                'satuanLPB': item.satuanPO,
                'satuanPO': item.satuanPO,
                'spesifikasi': item.spesifikasi,
                'stock_dokumen': item.stock_dokumen,
                'stock_dokumen2': item.stock_dokumen2,
                'totalHarga': totalHarga,
                'totalQty': item.totalQtyPO,
                'stock_dokumen2': item.stock_dokumen2,
            });
        });

        drawTableDigunakanAlokasi(list_items_barang_digunakan_alokasi);
    });
</script>