<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Detail Stok</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("stock-list"); ?>">
                Batal
            </a>

        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">DETAIL BARANG</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= strtoupper(str_replace('_', ' ', $detail['barang']['parent_type']))  ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">Tipe Barang</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= strtoupper($detail['barang']['parent_name']) ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">Kategori Barang</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= strtoupper($divisi['divisi']) ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">Departemen</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= strtoupper($warehouse['warehouse_name']) ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">Warehouse</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= "(" . strtoupper($detail['barang']['kode']) . ")" . "  " . ($detail['barang']['parent_type'] != "kemasan" ? strtoupper($detail['barang']['barang_name']) : strtoupper($detail['barang']['barang'])) .  ($detail['barang']['parent_type'] != "kemasan" ? " - " . strtoupper($detail['barang']['spesifikasi']) : "") ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">(Kode) Nama Barang <?= $detail['barang']['parent_type'] == "kemasan" ? "" : "- Spesifikasi" ?></label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= strtoupper($detail['barang']['kode_satuan']) ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">Satuan</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">DATA STOK SEKARANG</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= ($detail['stokInisiasi'] == 0 ? 0 : $detail['stokInisiasi']['qty']) . " " . $detail['barang']['kode_satuan'] ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">Qty Awal</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= ($detail['stok']['stokMasuk']) . " " . $detail['barang']['kode_satuan'] ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">Qty Masuk</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= ($detail['stok']['stokKeluar']) . " " . $detail['barang']['kode_satuan'] ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">Qty Keluar</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= ($detail['stok']['stokSekarang']) . " " . $detail['barang']['kode_satuan'] ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">Qty Akhir</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">DATA STOK PER DOKUMEN</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select bc_id_stok_per_dokumen" id="bc_id_stok_per_dokumen" name="bc_id_stok_per_dokumen" aria-label="Floating label select example">
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
                <div class="col-md-4 mb-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search_no_aju_stok_per_dokumen" id="search_no_aju_stok_per_dokumen" name="search_no_aju_stok_per_dokumen" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Nomor Aju </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable stok-dokumen-bc-table" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSortStokPerDokumen('bc_id')">Dokumen Pabean</th>
                                <th onclick="changeSortStokPerDokumen('no_aju')">No Aju</th>
                                <th>Barang - Spesifikasi</th>
                                <th onclick="changeSortStokPerDokumen('stok_total')">Qty Satuan 1</th>
                                <th>Qty Satuan 2</th>
                                <th>Qty Satuan 3</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3"></td>
                                <td style="float: right;"><b>TOTAL</b></td>
                                <td><b><?= ($total['totalPerDokumen']) . ' ' . $detail['barang']['kode_satuan'] ?></b></td>
                                <td><b></b></td>
                                <td><b></b></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col mb-3">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">DATA INISIASI STOK (STOK AWAL)</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select bc_id_stok_inisasi" id="bc_id_stok_inisasi" name="bc_id_stok_inisasi" aria-label="Floating label select example">
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
                <div class="col-md-4 mb-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search_no_aju_stok_inisasi" id="search_no_aju_stok_inisasi" name="search_no_aju_stok_inisasi" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Nomor Aju </label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable stok-inisasi-table" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSortStokInisasi('bc_id')">Dokumen Pabean</th>
                                <th onclick="changeSortStokInisasi('no_aju')">No Aju</th>
                                <th onclick="changeSortStokInisasi('tanggal')">Tanggal</th>
                                <th>Barang - Spesifikasi</th>
                                <th onclick="changeSortStokInisasi('stok_total')">Qty Satuan 1</th>
                                <th>Qty Satuan 2</th>
                                <th>Qty Satuan 3</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4"></td>
                                <td style="float: right;"><b>TOTAL</b></td>
                                <td><b><?= ($total['totalPerInit']) . ' ' . $detail['barang']['kode_satuan'] ?></b></td>
                                <td><b></b></td>
                                <td><b></b></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col mb-3">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">DATA PEMASUKKAN BARANG PER DOKUMEN (PURCHASE ORDER)</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select bc_id_stok_pemasukkan_barang" id="bc_id_stok_pemasukkan_barang" name="bc_id_stok_inisasi" aria-label="Floating label select example">
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
                <div class="col-md-4 mb-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search_no_aju_stok_pemasukkan_barang" id="search_no_aju_stok_pemasukkan_barang" name="search_no_aju_stok_pemasukkan_barang" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Nomor PO / LPB / No Aju </label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable stok-pemasukkan-barang-table" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSortPemasukkanBarang('stock_details2.no_dokumen')">Purchase Order</th>
                                <th onclick="changeSortPemasukkanBarang('stock_details.tanggal')">Tanggal</th>
                                <th onclick="changeSortPemasukkanBarang('stock_details.no_dokumen')">Dokumen</th>
                                <th>Supplier</th>
                                <th onclick="changeSortPemasukkanBarang('stock.barang1_id')">Barang - Spesifikasi</th>
                                <th onclick="changeSortPemasukkanBarang('stock_details2.qty')">Qty Satuan 1</th>
                                <th>Qty Satuan 2</th>
                                <th>Qty Satuan 3</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5"></td>
                                <td style="float: right;"><b>TOTAL</b></td>
                                <td><b><?= ($total['totalPerPemasukkan']) . ' ' . $detail['barang']['kode_satuan'] ?></b></td>
                                <td><b></b></td>
                                <td><b></b></td>
                                <td><b></b></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col mb-3">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">DATA PEMASUKAN ATAU PENGELUARAN DARI ADJUSMENT</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select bc_id_stok_pemasukkan_barang" id="bc_id_stok_adjusment" name="bc_id_stok_adjusment" aria-label="Floating label select example">
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
                <div class="col-md-4 mb-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search_no_aju_stok_adjusment" id="search_no_aju_stok_adjusment" name="search_no_aju_stok_pemasukkan_barang" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Nomor Adjusment </label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable stok-adjusment" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSortStokAdjusment('stock_details2.no_aju')">Dokumen</th>
                                <th onclick="changeSortStokAdjusment('stock_details.tanggal')">Tanggal</th>
                                <th>Barang - Spesifikasi</th>
                                <th onclick="changeSortStokAdjusment('stock_details.no_dokumen')">No Adjusment</th>
                                <th onclick="changeSortStokAdjusment('stock_details2.qty')">Qty Satuan 1</th>
                                <th>Qty Satuan 2</th>
                                <th>Qty Satuan 3</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4"></td>
                                <td style="float: right;"><b>TOTAL</b></td>
                                <td><b><?= ($total['totalPerAdjusment']) . ' ' . $detail['barang']['kode_satuan'] ?></b></td>
                                <td><b></b></td>
                                <td><b></b></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col mb-3">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">DATA PEMASUKAN ATAU PENGELUARAN BARANG DARI MUTASI</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select bc_id_stok_mutasi" id="bc_id_stok_mutasi" name="bc_id_stok_mutasi" aria-label="Floating label select example">
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
                <div class="col-md-4 mb-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search_no_aju_stok_mutasi" id="search_no_aju_stok_mutasi" name="search_no_aju_stok_mutasi" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari No Aju / No Mutasi / No Penerimaan Mutasi </label>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable stok-mutasi" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSortMutasi('stock_details2.no_aju')">Dokumen</th>
                                <th onclick="changeSortMutasi('stock_details.tanggal')">Tanggal</th>
                                <th>Barang - Spesifikasi</th>
                                <th onclick="changeSortMutasi('stock_details.no_dokumen')">No Penerimaan Mutasi</th>
                                <th onclick="changeSortMutasi('stock_details2.no_dokumen')">No Mutasi</th>
                                <th onclick="changeSortMutasi('stock_details2.qty')">Qty Satuan 1</th>
                                <th>Qty Satuan 2</th>
                                <th>Qty Satuan 3</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5"></td>
                                <td style="float: right;"><b>TOTAL</b></td>
                                <td><b><?= ($total['totalMutasi']) . ' ' . $detail['barang']['kode_satuan'] ?></b></td>
                                <td><b></b></td>
                                <td><b></b></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col mb-3">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">DATA PEMASUKKAN ATAU PENGELUARAN BARANG DARI VENDOR</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select bc_id_stok_jasa_vendor" id="bc_id_stok_jasa_vendor" name="bc_id_stok_jasa_vendor" aria-label="Floating label select example">
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
                <div class="col-md-4 mb-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search_no_aju_stok_jasa_vendor" id="search_no_aju_stok_jasa_vendor" name="search_no_aju_stok_jasa_vendor" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari No Aju / No SJ / No Penerimaan SJ </label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable stok-jasa-vendor" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSortJasaVendor('stock_details2.no_aju')">Dokumen</th>
                                <th onclick="changeSortJasaVendor('stock_details.tanggal')">Tanggal</th>
                                <th>Barang - Spesifikasi</th>
                                <th onclick="changeSortJasaVendor('stock_details.no_dokumen')">No Penerimaan Surat Jalan</th>
                                <th onclick="changeSortJasaVendor('stock_details2.no_dokumen')">No Surat Jalan</th>
                                <th onclick="changeSortJasaVendor('stock_details2.qty')">Qty Satuan 1</th>
                                <th>Qty Satuan 2</th>
                                <th>Qty Satuan 3</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5"></td>
                                <td style="float: right;"><b>TOTAL</b></td>
                                <td><b><?= ($total['totalJasaVendor']) . ' ' . $detail['barang']['kode_satuan'] ?></b></td>
                                <td><b></b></td>
                                <td><b></b></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col mb-3">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">DATA PENGELUARAN BARANG KE PRODUKSI</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select bc_id_stok_produksi_out" id="bc_id_stok_produksi_out" name="bc_id_stok_produksi_out" aria-label="Floating label select example">
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
                <div class="col-md-4 mb-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search_no_aju_stok_produksi_out" id="search_no_aju_stok_produksi_out" name="search_no_aju_stok_produksi_out" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Kode Produksi </label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable stok-out-produksi" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSortProduksiOut('stock_details2.no_aju')">Dokumen</th>
                                <th onclick="changeSortProduksiOut('stock_details.tanggal')">Tanggal</th>
                                <th>Barang - Spesifikasi</th>
                                <th onclick="changeSortProduksiOut('stock_details.no_dokumen')">Kode Produksi</th>
                                <th onclick="changeSortProduksiOut('stock_details2.qty')">Qty Satuan 1</th>
                                <th>Qty Satuan 2</th>
                                <th>Qty Satuan 3</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4"></td>
                                <td style="float: right;"><b>TOTAL</b></td>
                                <td><b><?= ($total['totalProduksiOut']) . ' ' . $detail['barang']['kode_satuan'] ?></b></td>
                                <td><b></b></td>
                                <td><b></b></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col mb-3">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">DATA PENERIMAAN BARANG KE PRODUKSI</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select bc_id_stok_produksi_in" id="bc_id_stok_produksi_in" name="bc_id_stok_produksi_in" aria-label="Floating label select example">
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
                <div class="col-md-4 mb-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search_no_aju_stok_produksi_in" id="search_no_aju_stok_produksi_in" name="search_no_aju_stok_produksi_in" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Kode Penerimaan Produksi </label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable stok-in-produksi" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSortProduksiIn('stock_details2.no_aju')">Dokumen</th>
                                <th onclick="changeSortProduksiIn('stock_details.tanggal')">Tanggal</th>
                                <th>Barang - Spesifikasi</th>
                                <th onclick="changeSortProduksiIn('stock_details.no_dokumen')">Kode Produksi</th>
                                <th onclick="changeSortProduksiIn('stock_details2.qty')">Qty Satuan 1</th>
                                <th>Qty Satuan 2</th>
                                <th>Qty Satuan 3</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4"></td>
                                <td style="float: right;"><b>TOTAL</b></td>
                                <td><b><?= ($total['totalProduksiIn']) . ' ' . $detail['barang']['kode_satuan'] ?></b></td>
                                <td><b></b></td>
                                <td><b></b></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    let sortStokPerDokumen = "createdAt";
    let sortTypeStokPerDokumen = "desc";

    let sortStokInisasi = "createdAt";
    let sortTypeInisasi = "DESC";

    let sortStokPemasukkanBarang = "createdAt";
    let sortTypePemasukkanBarang = "DESC";

    let sortStokAdjusment = "createdAt";
    let sortTypeAdjusment = "DESC";

    let sortStokMutasi = "createdAt";
    let sortTypeMutasi = "DESC";

    let sortStokJasaVendor = "createdAt";
    let sortTypeJasaVendor = "DESC";

    let sortStokProduksiOut = "createdAt";
    let sortTypeProduksiOut = "DESC";

    let sortStokProduksiIn = "createdAt";
    let sortTypeProduksiIn = "DESC";

    const stokTableDokumenBC = $('.stok-dokumen-bc-table').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [4, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("stock-list/stock-dokumen-bc"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.bc_id = $("#bc_id_stok_per_dokumen option:selected").val();
                data.no_aju = $("#search_no_aju_stok_per_dokumen").val();
                data.stok_id = "<?= encrypt($stok['id']) ?>"
                data.sort = sortStokPerDokumen;
                data.sortType = sortTypeStokPerDokumen;
            },
            beforeSend: function() {
                $.LoadingOverlay("show", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
            complete: function() {
                $.LoadingOverlay("hide", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "bc_type",
                className: "text-center"
            },
            {
                data: "no_aju",
                className: "text-center"
            },
            {
                data: "barang",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "stok_1",
                className: "text-center"
            },
            {
                data: "stok_2",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "stok_3",
                className: "text-center",
                searchable: false,
                sortable: false
            },

        ],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        },
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

    const stokTableInisiasi = $('.stok-inisasi-table').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [4, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("stock-list/stock-filtered"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.bc_id = $("#bc_id_stok_inisasi option:selected").val();
                data.no_aju = $("#search_no_aju_stok_inisasi").val();
                data.stok_id = "<?= encrypt($stok['id']) ?>";
                data.sumber = "INISIASI";
                data.sort = sortStokInisasi;
                data.sortType = sortTypeInisasi;
            },
            beforeSend: function() {
                $.LoadingOverlay("show", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
            complete: function() {
                $.LoadingOverlay("hide", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "bc_type",
                className: "text-center"
            },
            {
                data: "no_aju",
                className: "text-center"
            },
            {
                data: "tanggal",
                className: "text-center"
            },
            {
                data: "barang",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "stok_1",
                className: "text-center"
            },
            {
                data: "stok_2",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "stok_3",
                className: "text-center",
                searchable: false,
                sortable: false
            },

        ],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        },
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

    const stokTablePemasukkanBarang = $('.stok-pemasukkan-barang-table').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [4, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("stock-list/stock-log-pemasukkan-barang-lpb"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.bc_id = $("#bc_id_stok_pemasukkan_barang option:selected").val();
                data.search = $("#search_no_aju_stok_pemasukkan_barang").val();
                data.stok_id = "<?= encrypt($stok['id']) ?>";
                data.sumber = "LPB";

                data.sort = sortStokPemasukkanBarang;
                data.sortType = sortTypePemasukkanBarang;
            },
            beforeSend: function() {
                $.LoadingOverlay("show", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
            complete: function() {
                $.LoadingOverlay("hide", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
        },

        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "po",
                className: "text-center"
            },
            {
                data: "tanggal",
                className: "text-center"
            },
            {
                data: "dokumen",
                className: "text-center"
            },
            {
                data: "supplier",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "barang",
                className: "text-center",
            },
            {
                data: "stok_1",
                className: "text-center"
            },
            {
                data: "stok_2",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "stok_3",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "harga",
                className: "text-center",
                searchable: false,
                sortable: false
            },

        ],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        },
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

    const stokTableAdjusment = $('.stok-adjusment').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [4, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("stock-list/stock-log-adjusment"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.bc_id = $("#bc_id_stok_adjusment option:selected").val();
                data.search = $("#search_no_aju_stok_adjusment").val();
                data.stok_id = "<?= encrypt($stok['id']) ?>";
                data.sumber = "ADJUSMENT";

                data.sort = sortStokAdjusment;
                data.sortType = sortTypeAdjusment;
            },
            beforeSend: function() {
                $.LoadingOverlay("show", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
            complete: function() {
                $.LoadingOverlay("hide", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
        },

        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "dokumen",
                className: "text-center"
            },
            {
                data: "tanggal",
                className: "text-center"
            },
            {
                data: "barang",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "no_adjusment",
                className: "text-center",
            },
            {
                data: "stok_1",
                className: "text-center"
            },
            {
                data: "stok_2",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "stok_3",
                className: "text-center",
                searchable: false,
                sortable: false
            },

        ],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        },
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

    const stokTableMutasi = $('.stok-mutasi').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [4, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("stock-list/stock-log-mutasi"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.bc_id = $("#bc_id_stok_mutasi option:selected").val();
                data.search = $("#search_no_aju_stok_mutasi").val();
                data.stok_id = "<?= encrypt($stok['id']) ?>";
                data.sumber = "MUTASI";

                data.sort = sortStokMutasi
                data.sortType = sortTypeMutasi;
            },
            beforeSend: function() {
                $.LoadingOverlay("show", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
            complete: function() {
                $.LoadingOverlay("hide", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
        },

        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "dokumen",
                className: "text-center"
            },
            {
                data: "tanggal",
                className: "text-center"
            },
            {
                data: "barang",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "no_penerimaan_mutasi",
                className: "text-center",
            },
            {
                data: "no_mutasi",
                className: "text-center",
            },
            {
                data: "stok_1",
                className: "text-center"
            },
            {
                data: "stok_2",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "stok_3",
                className: "text-center",
                searchable: false,
                sortable: false
            },

        ],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        },
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

    const stokTableJasaVendor = $('.stok-jasa-vendor').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [4, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("stock-list/stock-log-jasa-vendor"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.bc_id = $("#bc_id_stok_jasa_vendor option:selected").val();
                data.search = $("#search_no_aju_stok_jasa_vendor").val();
                data.stok_id = "<?= encrypt($stok['id']) ?>";
                data.sumber = "JASA VENDOR";

                data.sort = sortStokJasaVendor
                data.sortType = sortTypeJasaVendor;
            },
            beforeSend: function() {
                $.LoadingOverlay("show", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
            complete: function() {
                $.LoadingOverlay("hide", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
        },

        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "dokumen",
                className: "text-center"
            },
            {
                data: "tanggal",
                className: "text-center"
            },
            {
                data: "barang",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "no_penerimaan_surat_jalan",
                className: "text-center",
            },
            {
                data: "no_surat_jalan",
                className: "text-center",
            },
            {
                data: "stok_1",
                className: "text-center"
            },
            {
                data: "stok_2",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "stok_3",
                className: "text-center",
                searchable: false,
                sortable: false
            },

        ],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        },
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

    const stokTableProduksiOut = $('.stok-out-produksi').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [4, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("stock-list/stock-log-produksi"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.bc_id = $("#bc_id_stok_produksi_out option:selected").val();
                data.search = $("#search_no_aju_stok_produksi_out").val();
                data.stok_id = "<?= encrypt($stok['id']) ?>";
                data.sumber = "PRODUKSI";
                data.status = "Out"

                data.sort = sortStokProduksiOut
                data.sortType = sortTypeProduksiOut;
            },
            beforeSend: function() {
                $.LoadingOverlay("show", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
            complete: function() {
                $.LoadingOverlay("hide", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
        },

        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "dokumen",
                className: "text-center"
            },
            {
                data: "tanggal",
                className: "text-center"
            },
            {
                data: "barang",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "no_dokumen1",
                className: "text-center",
            },
            {
                data: "stok_1",
                className: "text-center"
            },
            {
                data: "stok_2",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "stok_3",
                className: "text-center",
                searchable: false,
                sortable: false
            },

        ],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        },
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

    const stokTableProduksiIn = $('.stok-in-produksi').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [4, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("stock-list/stock-log-produksi"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.bc_id = $("#bc_id_stok_produksi_out option:selected").val();
                data.search = $("#search_no_aju_stok_produksi_out").val();
                data.stok_id = "<?= encrypt($stok['id']) ?>";
                data.sumber = "PRODUKSI";
                data.status = "In"

                data.sort = sortStokProduksiIn
                data.sortType = sortTypeProduksiIn;
            },
            beforeSend: function() {
                $.LoadingOverlay("show", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
            complete: function() {
                $.LoadingOverlay("hide", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
        },

        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "dokumen",
                className: "text-center"
            },
            {
                data: "tanggal",
                className: "text-center"
            },
            {
                data: "barang",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "no_dokumen1",
                className: "text-center",
            },
            {
                data: "stok_1",
                className: "text-center"
            },
            {
                data: "stok_2",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "stok_3",
                className: "text-center",
                searchable: false,
                sortable: false
            },

        ],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        },
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

    $('#bc_id_stok_per_dokumen').select2({
        placeholder: "Pilih Dokumen Pabean",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        stokTableDokumenBC.ajax.reload();
    });

    $('#bc_id_stok_inisasi').select2({
        placeholder: "Pilih Dokumen Pabean",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        stokTableInisiasi.ajax.reload();
    });

    $('#bc_id_stok_pemasukkan_barang').select2({
        placeholder: "Pilih Dokumen Pabean",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        stokTablePemasukkanBarang.ajax.reload();
    });

    $('#bc_id_stok_adjusment').select2({
        placeholder: "Pilih Dokumen Pabean",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        stokTableAdjusment.ajax.reload();
    });

    $('#bc_id_stok_mutasi').select2({
        placeholder: "Pilih Dokumen Pabean",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        stokTableMutasi.ajax.reload();
    });

    $('#bc_id_stok_jasa_vendor').select2({
        placeholder: "Pilih Dokumen Pabean",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        stokTableJasaVendor.ajax.reload();
    });

    $('#bc_id_stok_produksi_out').select2({
        placeholder: "Pilih Dokumen Pabean",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        stokTableProduksiOut.ajax.reload();
    });

    $('#bc_id_stok_produksi_in').select2({
        placeholder: "Pilih Dokumen Pabean",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        stokTableProduksiIn.ajax.reload();
    });

    $('#search_no_aju_stok_per_dokumen').change(function() {
        stokTableDokumenBC.ajax.reload();
    });

    $('#search_no_aju_stok_inisasi').change(function() {
        stokTableInisiasi.ajax.reload();
    });

    $('#search_no_aju_stok_pemasukkan_barang').change(function() {
        stokTablePemasukkanBarang.ajax.reload();
    });

    $('#search_no_aju_stok_adjusment').change(function() {
        stokTableAdjusment.ajax.reload();
    });

    $('#search_no_aju_stok_mutasi').change(function() {
        stokTableMutasi.ajax.reload();
    });

    $('#search_no_aju_stok_jasa_vendor').change(function() {
        stokTableJasaVendor.ajax.reload();
    });

    $('#search_no_aju_stok_produksi_in').change(function() {
        stokTableProduksiIn.ajax.reload();
    });

    $('#search_no_aju_stok_produksi_out').change(function() {
        stokTableProduksiOut.ajax.reload();
    });

    $("#bc_id_stok_mutasi,#bc_id_stok_per_dokumen, #bc_id_stok_inisasi, #bc_id_stok_pemasukkan_barang, #bc_id_stok_adjusment,#bc_id_stok_jasa_vendor,#bc_id_stok_produksi_in,#bc_id_stok_produksi_out")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    const changeSortStokAdjusment = function(val) {
        if (sortTypeAdjusment !== val) {
            sortTypeAdjusment = "asc";
            sortStokAdjusment = val;
        } else {
            sortTypeAdjusment = sortTypeAdjusment === "asc" ? "desc" : "asc";
        }
        stokTableAdjusment.ajax.reload();
    }

    const changeSortStokPerDokumen = function(val) {
        if (sortTypeStokPerDokumen !== val) {
            sortTypeStokPerDokumen = "asc";
            sortStokPerDokumen = val;
        } else {
            sortTypeStokPerDokumen = sortTypeStokPerDokumen === "asc" ? "desc" : "asc";
        }
        stokTableDokumenBC.ajax.reload();
    }

    const changeSortStokInisasi = function(val) {
        if (sortStokInisasi !== val) {
            sortTypeInisasi = "asc";
            sortStokInisasi = val;
        } else {
            sortTypeInisasi = sortTypeInisasi === "asc" ? "desc" : "asc";
        }
        stokTableInisiasi.ajax.reload();
    }

    const changeSortPemasukkanBarang = function(val) {
        if (sortStokPemasukkanBarang !== val) {
            sortTypePemasukkanBarang = "asc";
            sortStokPemasukkanBarang = val;
        } else {
            sortTypePemasukkanBarang = sortTypePemasukkanBarang === "asc" ? "desc" : "asc";
        }
        stokTableInisiasi.ajax.reload();
    }

    const changeSortMutasi = function(val) {
        if (sortStokMutasi !== val) {
            sortTypeMutasi = "asc";
            sortStokMutasi = val;
        } else {
            sortTypeMutasi = sortTypeMutasi === "asc" ? "desc" : "asc";
        }
        stokTableMutasi.ajax.reload();
    }

    const changeSortJasaVendor = function(val) {
        if (sortStokJasaVendor !== val) {
            sortTypeJasaVendor = "asc";
            sortStokJasaVendor = val;
        } else {
            sortTypeJasaVendor = sortTypeJasaVendor === "asc" ? "desc" : "asc";
        }
        sortTypeJasaVendor.ajax.reload();
    }

    const changeSortProduksiOut = function(val) {
        if (sortStokProduksiOut !== val) {
            sortTypeProduksiOut = "asc";
            sortStokProduksiOut = val;
        } else {
            sortTypeProduksiOut = sortTypeProduksiOut === "asc" ? "desc" : "asc";
        }
        sortTypeProduksiOut.ajax.reload();
    }

    const changeSortProduksiIn = function(val) {
        if (sortStokProduksiIn !== val) {
            sortTypeProduksiIn = "asc";
            sortStokProduksiIn = val;
        } else {
            sortTypeProduksiIn = sortTypeProduksiIn === "asc" ? "desc" : "asc";
        }
        sortTypeProduksiIn.ajax.reload();
    }
</script>



<?= $this->endSection(); ?>