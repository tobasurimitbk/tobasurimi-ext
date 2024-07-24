<div class="col-subtitle-modal">
    <div class="row mt-3">
        <div class="col-md-6">
            <label class="form-label font-weight-bold modal-sub-title">Barang Jadi Awal</label>
        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="selectedItemTableRasio" width="100%" border="1" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th style="text-align: center;">No</th>
                        <th style="text-align: center;">Kode Barang</th>
                        <th style="text-align: center;">Nama Barang</th>
                        <th style="text-align: center;">Satuan</th>
                        <th style="text-align: center;">Jumlah Barang</th>
                        <th style="text-align: center;">Harga Satuan</th>
                        <th style="text-align: center;">Total</th>
                    </tr>
                </thead>
                <tbody class="body-table-rasio">
                </tbody>
                <tfoot style="background: #ffffff !important;" class="tfoot-rasio" id="tfoot-rasio">
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
    <div class="col-sm-2">
        <button class="btn btn-show-detail btn-add btn-submit-barang" data-btn="detail-modal" id="select-item-btn" type="button">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah Barang
        </button>
    </div>
</div>
<div class="col-subtitle-modal">
    <div class="row mt-3">
        <div class="col-md-6">
            <label class="form-label font-weight-bold modal-sub-title">Rasio Barang Jadi</label>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="selectedItemTableRasioAkhir" width="100%" border="1" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th style="text-align: center;">No</th>
                        <th style="text-align: center;">Kode Barang</th>
                        <th style="text-align: center;">Nama Barang</th>
                        <th style="text-align: center;">Satuan</th>
                        <th style="text-align: center;">Jumlah Barang </th>
                        <th style="text-align: center;">Jumlah Barang (KG)</th>
                        <th style="text-align: center;">Bhn Tersedia</th>
                        <th style="text-align: center;">Filling Weight</th>
                        <th style="text-align: center;">Rasio</th>
                        <th style="text-align: center;">Harga Satuan</th>
                        <th style="text-align: center;">Total</th>
                        <th style="text-align: center;">Harga BB</th>
                    </tr>
                </thead>
                <tbody class="body-table-rasio-akhir">
                </tbody>
                <tfoot style="background: #ffffff !important;" class="tfoot-rasio-akhir" id="tfoot-rasio-akhir">
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
<div class="col-subtitle-modal">
    <div class="row mt-3">
        <div class="col-md-6">
            <label class="form-label font-weight-bold modal-sub-title">Rasio Barang Jadi Terhadap Bahan Baku</label>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="selectedItemTableRasioAkhir" width="100%" border="1" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th style="text-align: center;">No</th>
                        <th style="text-align: center;">Kode Barang</th>
                        <th style="text-align: center;">Nama Barang</th>
                        <th style="text-align: center;">Satuan</th>
                        <th style="text-align: center;">Jumlah Barang</th>
                        <th style="text-align: center;">Rasio</th>
                        <th style="text-align: center;">Total</th>
                    </tr>
                </thead>
                <tbody class="body-table-rasio-terhadap-bahan-baku">
                </tbody>
                <tfoot style="background: #ffffff !important;" class="tfoot-rasio-terhadap-bahan-baku" id="tfoot-rasio-terhadap-bahan-baku">
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
<script>
    $('#select-item-btn').click(function() {
        if (list_items_barang_digunakan_alokasi.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Data Barang Setelah Alokasi Harus Terisi',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        } else {
            drawTableRasioAkhir(list_items_barang_jadi);
            drawTableRasioTerhadapBahanBaku(list_items_barang_jadi);
        }
    });
</script>