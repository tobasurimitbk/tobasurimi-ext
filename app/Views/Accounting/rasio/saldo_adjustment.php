<div class="row justify-content-end">
    <div class="col mb-3">
        <label class="form-label font-weight-bold lable-title">Data Saldo Adjustment</label>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="selectedItemTableSaldoAdjusment" width="100%" border="1" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th style="text-align: center;" rowspan="2">No</th>
                        <th style="text-align: center;" colspan="5">Data Saldo Adjustment</th>
                    </tr>
                    <tr>
                        <th style="text-align: center;">Spesifikasi</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: center;">Harga Total</th>
                        <th style="text-align: center;">Harga Satuan</th>
                        <th style="text-align: center;">Satuan</th>
                    </tr>
                </thead>
                <tbody class="body-detail-table-saldo-adjust">
                </tbody>
                <tfoot style="background: #ffffff !important;" class="tfoot-detail-table-saldo-adjust" id="tfoot-detail-table-saldo-adjust">
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
        <label class="form-label font-weight-bold lable-title">Data Total Proses Ulang Barang</label>
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