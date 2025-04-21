<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    label {
        margin-top: -4.5px;
    }
</style>
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($dataTandaTerimaFaktur) ? "Update Tanda Terima Faktur Penerimaan Lokal Bahan Penolong" : "Tambah Tanda Terima Faktur Penerimaan Lokal Bahan Penolong" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("tanda-terima-faktur-lokal-bp"); ?>">
                Kembali
            </a>
            <?php if (!empty($dataTandaTerimaFaktur)) : ?>
                <?php if (!$isUsed) : ?>

                    <?php if (can('T. Terima Supplier', 'P. Lokal Bahan Penolong', 'd')) : ?>
                        <button onclick="remove('<?= encrypt($dataTandaTerimaFaktur['id']) ?>')" class="btn btn-hapus delete-parent float-right">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('T. Terima Supplier', 'P. Lokal Bahan Penolong', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("tanda-terima-faktur-lokal-bp/print/" . encrypt($dataTandaTerimaFaktur['id'])) ?>')">
                            Print
                        </button>
                    <?php endif; ?>
                    <?php if (can('T. Terima Supplier', 'P. Lokal Bahan Penolong', 'u')) : ?>
                        <button id="btn-submit-form" class="btn btn-show-form btn-save float-right btn-submit-form">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if (can('T. Terima Supplier', 'P. Lokal Bahan Penolong', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("tanda-terima-faktur-lokal-bp/print/" . encrypt($dataTandaTerimaFaktur['id'])) ?>')">
                            Print
                        </button>
                    <?php endif; ?>

                <?php endif; ?>
            <?php else : ?>
                <?php if (can('T. Terima Supplier', 'P. Lokal Bahan Penolong', 'c')) : ?>
                    <button id="btn-submit-form" class="btn btn-show-form btn-save float-right btn-submit-form">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Faktur Penerimaan Barang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Pengenaan Pajak</a>
                </li>

            </ul>
            <div class="tab-content" id="myTabContent">
                <br><br>
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" value="<?= !empty($dataTandaTerimaFaktur) ? encrypt($dataTandaTerimaFaktur['id']) : '' ?>" name="id" class="id" id="id">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <div class="input-group input-group-password">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" <?= !empty($dataTandaTerimaFaktur) ? ($isUsed ? 'readonly' : '') : '' ?> type="text" class="form-control no_tanda_terima_faktur" id="no_tanda_terima_faktur" name="no_tanda_terima_faktur" placeholder="No Tanda Terima Faktur" value="<?= !empty($dataTandaTerimaFaktur) ? $dataTandaTerimaFaktur['faktur_no'] : $noTandaTerima ?>">
                                            <label for="floatingInput">No Terima Faktur</label>
                                        </div>
                                        <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                            <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px; <?= !empty($dataTandaTerimaFaktur) ? 'display:none' : '' ?> " class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <div class="input-group input-group-password">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" <?= !empty($dataTandaTerimaFaktur) ? ($isUsed ? 'readonly' : '') : '' ?> type="text" class="form-control no_tanda_keluar_faktur" id="no_tanda_keluar_faktur" name="no_tanda_keluar_faktur" placeholder="No Tanda Keluar Faktur" value="<?= !empty($dataTandaTerimaFaktur) ? $dataTandaTerimaFaktur['faktur_keluar_no'] : $noTandaKeluar ?>">
                                            <label for="floatingInput">No Keluar Faktur</label>
                                        </div>
                                        <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                            <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px; <?= !empty($dataTandaTerimaFaktur) ? 'display:none' : '' ?> " class="auto_generate" id="auto_generate2" name="auto_generate2" type="checkbox" onchange="changeStatusKeluar()">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-password">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input <?= $isUsed ? 'disabled' : '' ?> autocomplete="one-time-code" value="<?= !empty($dataTandaTerimaFaktur) ? date('d/m/Y', strtotime($dataTandaTerimaFaktur['receive_date'])) : date('d/m/Y') ?>" class="form-control input-picker datepicker" id="tanggal_terima" name="tanggal_terima" placeholder="Tanggal Terima Faktur">
                                        <label for="floatingInput">Tanggal Terima</label>
                                    </div>
                                    <div class="input-group-prepend group-prepend-password align-items-center">
                                        <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <?php if (!empty($dataTandaTerimaFaktur)) : ?>
                                    <input autocomplete="one-time-code" name="supplier_id" value="<?= $dataTandaTerimaFaktur['supplier_id'] ?>" type="hidden" class="form-control ">
                                <?php endif; ?>
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select <?= $isUsed ? 'disabled' : '' ?> class="form-select supplier_id" name="supplier_id" id="supplier_id">
                                        <option value=""></option>
                                        <option value="all">All</option>
                                        <?php foreach ($dataSupplier as $supplier) : ?>
                                            <option value="<?= $supplier['id'] ?>"><?= $supplier['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Supplier</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input type="text" readonly autocomplete="one-time-code" value="" class="form-control nominal_faktur" id="nominal_faktur" name="nominal_faktur" placeholder="Nominal Faktur">
                                    <label for="floatingInput">Nominal Faktur</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <div class="input-group input-group-password">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input <?= $isUsed ? 'disabled' : '' ?> autocomplete="one-time-code" value="<?= !empty($dataTandaTerimaFaktur) ? ($dataTandaTerimaFaktur['jatuh_tempo'] ? date("d/m/Y", strtotime($dataTandaTerimaFaktur['jatuh_tempo'])) : "") : ""; ?>" class="form-control input-picker datepicker" id="jatuh_tempo" name="jatuh_tempo" placeholder="Jatuh Tempo">
                                            <label for="floatingInput">Jatuh Tempo</label>
                                        </div>
                                        <div class="input-group-prepend group-prepend-password align-items-center">
                                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select <?= $isUsed ? 'disabled' : '' ?> class="form-select divisi_id" name="divisi_id" id="divisi_id">
                                        <option value=""></option>
                                        <option value="all">All</option>
                                        <?php foreach ($divisi as $d) : ?>
                                            <option <?= !empty($dataTandaTerimaFaktur) ? ($dataTandaTerimaFaktur['divisi_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>"><?= $d['divisi'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Departemen</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label font-weight-bold lable-title">Daftar Penerimaan Barang</label>
                            </div>
                            <div class="col-md-12 col-table-button-tts">
                                <div class="table-responsive">
                                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-form-tts" id="dataTable" width="100%" cellspacing="0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th style="text-align: center;">No</th>
                                                <th style="text-align: center;"><input type="checkbox" id="parent"></th>
                                                <th style="text-align: center;" class="sort">No PO</th>
                                                <th style="text-align: center;" class="sort">Tgl LPB</th>
                                                <th style="text-align: center;" class="sort">No LPB</th>
                                                <th style="text-align: center;" class="sort">Supplier</th>
                                                <th style="text-align: center;" class="sort">Nama Barang</th>
                                                <th style="text-align: center;" class="sort">Qty LPB</th>
                                                <th style="text-align: center;" class="sort">Qty Retur</th>
                                                <th style="text-align: center;" class="sort">Qty Telah Terima</th>
                                                <th style="text-align: center;" class="sort">Qty Akan Diterima</th>
                                                <th style="text-align: center;" class="sort">Satuan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="body-table" id="body-table">

                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" <?= ($isUsed) ? 'disabled' : '' ?> class="btn btn-primary" id="select-item-btn">Pilih</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label font-weight-bold lable-title">Daftar penerimaan barang yang akan dibuat tanda terima</label>
                            </div>
                            <div class="col-md-12 mb-5">
                                <div class="table-responsive">
                                    <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts" id="selectedItemTable" width="100%" cellspacing="0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th style="text-align: center;">No</th>
                                                <th style="text-align: center;">No PO</th>
                                                <th style="text-align: center;">Tgl LPB</th>
                                                <th style="text-align: center;">No LPB</th>
                                                <th style="text-align: center;">Supplier</th>
                                                <th style="text-align: center;">Nama Barang</th>
                                                <th style="text-align: center;">Qty</th>
                                                <th style="text-align: center;">Satuan</th>
                                                <th style="text-align: center;">Total</th>
                                                <th style="text-align: center;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="body-table">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-floating mb-3">
                                    <input <?= $isUsed ? 'disabled' : '' ?> onkeyup="this.value = greatFormatRupiah(this.value); hitungPotonganTambahan();" autocomplete="one-time-code" type="text" class="form-control potongan" name="potongan" id="potongan" value="<?= !empty($dataTandaTerimaFaktur) ? number_format($dataTandaTerimaFaktur['potongan'], 2) : '' ?> " placeholder="Keterangan">
                                    <label for="floatingInput">Potongan (Opsional)</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating mb-3">
                                    <input <?= $isUsed ? 'disabled' : '' ?> onkeyup="this.value = greatFormatRupiah(this.value); hitungPotonganTambahan();" autocomplete="one-time-code" type="text" class="form-control tambahan" name="tambahan" id="tambahan" value="<?= !empty($dataTandaTerimaFaktur) ? number_format($dataTandaTerimaFaktur['tambahan'], 2) : '' ?> " placeholder="Keterangan">
                                    <label for="floatingInput">Penambahan (Opsional)</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" readonly type="text" class="form-control total_pph_21" id="total_pph_21" name="total_pph_21" value="0" />
                                    <label for="floatingInput">Total Potongan PPH Pasal 21</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" readonly type="text" class="form-control total_ppn_11" id="total_ppn_11" name="total_ppn_11" value="0" />
                                    <label for="floatingInput">Total PPN Masukan 11%</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= $isUsed ? 'disabled' : '' ?> autocomplete="one-time-code" readonly type="text" class="form-control total_tambahan_potongan" id="total_tambahan_potongan" name="total_tambahan_potongan" value="0" />
                                    <label for="floatingInput">Nominal Faktur Final</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= $isUsed ? 'disabled' : '' ?> autocomplete="one-time-code" value="<?= !empty($dataTandaTerimaFaktur) ? $dataTandaTerimaFaktur['recipient'] : session()->get("login")->name ?>" type="text" class="form-control penerima" name="penerima" id="penerima" placeholder="Penerima">
                                    <label for="floatingInput">Penerima</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <textarea <?= $isUsed ? 'disabled' : '' ?> style="height: auto;" autocomplete="one-time-code" class="form-control information text-area-all" name="keterangan_tambahan" id="keterangan_tambahan" placeholder="Keterangan"><?= $dataTandaTerimaFaktur['information_tambahan'] ?? ""; ?></textarea>
                                    <label for="floatingInput">Keterangan Tambahan (Opsional)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <textarea <?= $isUsed ? 'disabled' : '' ?> style="height: auto;" autocomplete="one-time-code" class="form-control information text-area-all" name="keterangan_potongan" id="keterangan_potongan" placeholder="Keterangan Potongan"><?= $dataTandaTerimaFaktur['information_potongan'] ?? ""; ?></textarea>
                                    <label for="floatingInput">Keterangan Potongan (Opsional)</label>
                                </div>
                            </div>
                        </div>


                    </form>
                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <form id="pajak-form" class="pajak-form">
                        <input type="hidden" name="tax_id" class="tax_id" id="tax_id">
                        <?php if (!$isUsed) : ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group input-group-password">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" class="form-control input-picker datepicker" id="tax_inv_date" name="tax_inv_date" placeholder="Tanggal Faktur Pajak">
                                            <label for="floatingInput">Tanggal Faktur Pajak</label>
                                        </div>
                                        <div class="input-group-prepend group-prepend-password align-items-center">
                                            <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control" id="tax_inv_no" name="tax_inv_no" placeholder="No Faktur Pajak (Opsional)">
                                        <label for="floatingInput">No Faktur Pajak (Opsional)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select" name="tax_type" id="tax_type">
                                            <option value="" disabled selected></option>
                                            <option value="PPN Masukan">PPN Masukan</option>
                                            <option value="PPN Masukan 11%">PPN Masukan 11%</option>
                                            <option value="PPh Pasal 21">PPh Pasal 21</option>
                                            <option value="PPh Pasal 23">PPh Pasal 23</option>
                                            <option value="PPh Pasal 4 (2)">PPh Pasal 4 (2)</option>
                                        </select>
                                        <label for="floatingInput" style="z-index: 1;">Pilih Pajak</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input onkeyup="this.value = greatFormatRupiah(this.value);" autocomplete="one-time-code" type="text" class="form-control" id="tax_amt" name="tax_amt" placeholder="Jumlah">
                                        <label for="floatingInput">Jumlah</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select" name="tax_status" id="tax_status">
                                            <option value="" disabled selected></option>
                                            <option value="Pajak dipungut oleh negara">Pajak dipungut oleh negara</option>
                                            <option value="Pajak dikembalikan lagi">Pajak dikembalikan lagi</option>
                                        </select>
                                        <label for="floatingInput" style="z-index: 1;">Status</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <textarea style="height: auto;" autocomplete="one-time-code" class="form-control information text-area-all" id="tax_note" name="tax_note" placeholder="Keterangan"></textarea>
                                        <label for="floatingInput">Keterangan (Opsional)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-table-button-tts">
                                    <button type="button" <?= ($isUsed) ? 'disabled' : '' ?> class="btn btn-primary" id="add-tax-btn">Tambah Pengenaan Pajak</button>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts" id="taxTable" width="100%" cellspacing="0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th style="text-align: center;">No</th>
                                                <th style="text-align: center;">Tgl Faktur Pajak</th>
                                                <th style="text-align: center;">No Faktur Pajak</th>
                                                <th style="text-align: center;">Pajak</th>
                                                <th style="text-align: center;">Jumlah</th>
                                                <th style="text-align: center;">Status</th>
                                                <th style="text-align: center;">Keterangan</th>
                                                <th style="text-align: center;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="body-table">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</section>
<div class="modal detail-barang-modal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Update Data Barang</h5>
            </div>
            <div class="modal-body">
                <form class="detail-barang-form" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="penerimaan_barang_detail_id" name="penerimaan_barang_detail_id" id="penerimaan_barang_detail_id" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control nama_barang_dok" id="nama_barang_dok" name="nama_barang_dok" placeholder="Nama Barang">
                                <label for="floatingInput">Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control po_no" id="po_no" name="po_no" placeholder="Nomor PO">
                                <label for="floatingInput">Nomor PO</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Lpb">
                                <label for="floatingInput">Tanggal LPB</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control supplier_name" id="supplier_name" name="supplier_name" placeholder="Nama Supplier">
                                <label for="floatingInput">Supplier</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control no_penerimaan_barang" id="no_penerimaan_barang" name="no_penerimaan_barang" placeholder="No Penerimaan Barang">
                                <label for="floatingInput">No LPB</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="number" min="0" class="form-control qty_akan_diterima" id="qty_akan_diterima" name="qty_akan_diterima" placeholder="Qty Akan Diterima">
                                <label for="floatingInput">Qty Akan Diterima</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control kode_satuan" id="kode_satuan" name="kode_satuan" placeholder="Kode Satuan">
                                <label for="floatingInput">Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" data-harga_satuan="0" readonly="true" class="form-control total_harga" id="total_harga" name="total_harga" placeholder="Total Harga">
                                <label for="floatingInput">Total Harga</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-detail btn-discard mr-2">Kembali</button>
                <button type="submit" id="btn-submit-detail-barang" class="btn btn-submit-form btn-submit-detail-barang">Simpan</button>
            </div>
        </div>
    </div>
</div>
<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    var list_penerimaan_selected = [];
    var list_penerimaan_barang = [];
    var list_pajak = [];

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
        pageLength: 50,
        lengthMenu: [
            [50, 100, 200, -1],
            [50, 100, 200, "All"]
        ],
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
        pageLength: 50,
        lengthMenu: [
            [50, 100, 200, -1],
            [50, 100, 200, "All"]
        ],
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    var taxTable = $('#taxTable').DataTable({

        lengthChange: true,
        info: false,
        paging: false,
        searching: false,
        ordering: false,
        order: [],
        fixedHeader: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        pageLength: 50,
        lengthMenu: [
            [50, 100, 200, -1],
            [50, 100, 200, "All"]
        ],
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    $("#tanggal_terima,#tax_inv_date,#jatuh_tempo").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#tax_type').select2({
        placeholder: "Pilih Pajak",
        theme: "bootstrap-5"
    });

    $('#tax_status').select2({
        placeholder: "Pilih Status Pajak",
        theme: "bootstrap-5"
    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5"
    }).change(function() {
        list_penerimaan_selected = [];
        drawTableSelected(list_penerimaan_selected); // Perbarui tabel selected
        listDaftarPenerimaanBarang();
    });

    $('#supplier_id').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5"
    }).change(function() {
        list_penerimaan_selected = [];
        drawTableSelected(list_penerimaan_selected); // Perbarui tabel selected
        listDaftarPenerimaanBarang();
    });

    $("#supplier_id,#tax_status,#tax_type,#divisi_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('#select-item-btn').click(function() {
        // Jangan reset langsung list_penerimaan_selected
        var selectedSupplierId = null;

        if (list_penerimaan_selected.length > 0) {
            // Ambil supplier_id dari data yang sudah dipilih
            selectedSupplierId = list_penerimaan_selected[0].supplier_id;
        }

        var checkedCheckboxes = $(".child:checked"); // Ambil checkbox yang dipilih
        var dataIds = checkedCheckboxes.map(function() {
            return $(this).data("id");
        }).get();

        // PenerimaanBarangId
        if (dataIds.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Checklist minimal satu data penerimaan!',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
            return;
        }

        // Get Semua SupplierId yang di checklist
        var supplierIds = checkedCheckboxes.map(function() {
            return $(this).data("supplier_id"); // Ambil supplier_id dari atribut data
        }).get();

        // Cek Validasi Supplier Saat Checklist
        if (supplierIds.every(val => val == supplierIds[0]) == false) {
            Swal.fire({
                icon: 'error',
                title: 'Semua data penerimaan harus dari supplier yang sama!',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
            return;
        } else if (list_penerimaan_selected.length > 0) {

            if (supplierIds.every(val => val == selectedSupplierId) == false) {
                Swal.fire({
                    icon: 'error',
                    title: 'Semua data penerimaan harus dari supplier yang sama!',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
                return;
            }
        }

        // Proses data berdasarkan input yang dipilih
        var updatedList = []; // Array untuk menyimpan data yang masih ada
        $.each(list_penerimaan_barang, function(i, v) {
            if (v) { // Pastikan elemen valid
                if ($.inArray(Number(v.penerimaan_barang_detail_id), dataIds) !== -1) {
                    var targetInputElement = $('input[data-id_input_diterima="' + v.penerimaan_barang_detail_id + '"]');
                    var targetValue = Number(targetInputElement.val()); // Ambil nilai input

                    if (!isNaN(targetValue) && targetValue > 0) {
                        var sisaDiterima = Math.min(v.qty_akan_diterima, targetValue); // Hitung sisa yang diterima

                        // Kurangi qty_akan_diterima
                        v.qty_akan_diterima -= sisaDiterima;

                        // Tambahkan ke list_penerimaan_selected
                        list_penerimaan_selected.push({
                            penerimaan_barang_detail_id: v.penerimaan_barang_detail_id,
                            po_no: v.po_no,
                            tanggal: v.tanggal,
                            no_penerimaan_barang: v.no_penerimaan_barang,
                            nama_barang_dok: v.nama_barang_dok,
                            qty_lpb: v.qty_lpb,
                            qty_retur: v.qty_retur,
                            qty_telah_diterima: v.qty_telah_diterima,
                            qty_akan_diterima: sisaDiterima,
                            kode_satuan: v.kode_satuan,
                            harga: v.harga,
                            divisi_id: v.divisi_id,
                            supplier_id: v.supplier_id,
                            supplier_name: v.supplier_name,
                        });

                        // Simpan elemen ke daftar baru jika qty_akan_diterima masih ada
                        if (v.qty_akan_diterima > 0) {
                            updatedList.push(v);
                        }
                    }
                } else {
                    // Simpan data yang tidak terpengaruh
                    updatedList.push(v);
                }
            }
        });

        list_penerimaan_barang = updatedList; // Perbarui array utama

        // Refresh tampilan
        drawTableSelected(list_penerimaan_selected); // Tampilkan data yang dipilih
        drawTableDaftarPenerimaanBarang(list_penerimaan_barang); // Perbarui tabel utama
    });


    // VALIDATION PAJAK
    var validatorPajak = $(".pajak-form").validate({
        rules: {
            tax_inv_date: {
                required: true
            },
            tax_type: {
                required: true
            },
            tax_amt: {
                required: true
            },
            tax_status: {
                required: true,
            },
        },
        messages: {
            tax_inv_date: {
                required: "Tanggal faktur pajak wajib diisi"
            },
            tax_type: {
                required: "Pilih tipe pajak"
            },
            tax_amt: {
                required: "Nominal pajak wajib diisi"
            },
            tax_status: {
                required: "Pilih status pajak",
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

    // VALIDATOR PARENT FORM
    var validatorPajak = $(".create-form").validate({
        rules: {
            no_tanda_terima_faktur: {
                required: true
            },
            no_tanda_keluar_faktur: {
                required: true
            },
            tanggal_terima: {
                required: true
            },
            supplier_id: {
                required: true
            },
            nominal_faktur: {
                required: true
            },
            jatuh_tempo: {
                required: true,
            },
            total_tambahan_potongan: {
                required: true
            },
            penerima: {
                required: true
            },
            divisi_id: {
                required: true
            }
        },
        messages: {
            no_tanda_terima_faktur: {
                required: "No terima faktur wajib diisi"
            },
            no_tanda_keluar_faktur: {
                required: "No Keluar faktur wajib diisi"
            },
            tanggal_terima: {
                required: "Tanggal faktur wajib diisi"
            },
            supplier_id: {
                required: "Pilih supplier"
            },
            nominal_faktur: {
                required: "Nominal faktur wajib diisi"
            },
            jatuh_tempo: {
                required: "Jatuh tempo wajib diisi",
            },
            total_tambahan_potongan: {
                required: "Total tambahan potongan wajib diisi"
            },
            penerima: {
                required: "Penerima wajib diisi"
            },
            divisi_id: {
                required: "Departemen wajib diisi"
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

    $('#btn-submit-form').click(function() {
        if ($('.create-form').valid()) {
            if (list_penerimaan_selected.length == 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Penerimaan barang yang akan dibuat tanda terima masih kosong!',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            } else {
                var id = $('#id').val();
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
                        let data = new FormData(document.querySelector(".create-form"));
                        let totalTambahanPotongan = destroyFormatRupiah($('.total_tambahan_potongan').val());
                        let tambahanForm = destroyFormatRupiah($('.tambahan').val());
                        let potonganForm = destroyFormatRupiah($('.potongan').val());
                        data.set('total_tambahan_potongan', totalTambahanPotongan)
                        data.set('potongan', potonganForm)
                        data.set('tambahan', tambahanForm)
                        const cleanListPajak = list_pajak.map(item => ({
                            ...item,
                            tax_amt: destroyFormatRupiah(item.tax_amt),
                        }));
                        data.append("listPajak", JSON.stringify(cleanListPajak));
                        data.append("listPenerimaanBarang", JSON.stringify(list_penerimaan_selected));

                        if (id) {
                            // UPDATE
                            $.ajax({
                                url: "<?= base_url("tanda-terima-faktur-lokal-bp/update"); ?>",
                                data: data,
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                                            window.location.href = "<?= base_url('tanda-terima-faktur-lokal-bp') ?>"
                                        }
                                    });
                                },
                            });
                        } else {
                            // CREATE
                            $.ajax({
                                url: "<?= base_url("tanda-terima-faktur-lokal-bp/create"); ?>",
                                data: data,
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                },
                                method: "POST",
                                dataType: "json",
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    if (response.status) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                            confirmButtonText: 'Ok'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = "<?= base_url('tanda-terima-faktur-lokal-bp') ?>"
                                            }
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

                    }
                })
            }
        }
    });

    $('#add-tax-btn').click(function() {
        if ($('.pajak-form').valid()) {
            var taxId = $('#tax_id').val();
            if (taxId == '' || taxId == null) {
                // Create
                list_pajak.push({
                    id: getID(),
                    tax_inv_date: $('#tax_inv_date').val(),
                    tax_inv_no: $('#tax_inv_no').val(),
                    tax_type: $('#tax_type').val(),
                    tax_amt: destroyFormatRupiah($('#tax_amt').val()),
                    tax_status: $('#tax_status').val(),
                    tax_note: $('#tax_note').val()
                });
            } else {
                // Update
                var indexSelected = -1;
                for (var i = 0; i < list_pajak.length; i++) {
                    if (list_pajak[i].id == taxId) {
                        indexSelected = i;
                        break;
                    }
                }

                list_pajak[indexSelected].tax_inv_date = $('#tax_inv_date').val();
                list_pajak[indexSelected].tax_inv_no = $('#tax_inv_no').val();
                list_pajak[indexSelected].tax_type = $('#tax_type').val();
                list_pajak[indexSelected].tax_amt = destroyFormatRupiah($('#tax_amt').val());
                list_pajak[indexSelected].tax_status = $('#tax_status').val();
                list_pajak[indexSelected].tax_note = $('#tax_note').val();

            }
            drawTablePengenaanPajak(list_pajak);
            // reset
            $('#tax_id').val(null);
            $('#tax_inv_date').val(null);
            $('#tax_inv_no').val(null);
            $('#tax_type').val(null).change();
            $('#tax_amt').val(null);
            $('#tax_status').val(null).change();
            $('#tax_note').val(null);
        }
    });

    $('.btn-hide-detail').click(function() {
        $('.detail-barang-modal').modal('hide');
    });

    $('#qty_akan_diterima').keyup(function() {
        var qty = $(this).val();
        var hargaSatuan = $('#total_harga').attr('harga_satuan');
        $('#total_harga').val(greatFormatRupiah(qty * hargaSatuan));
    });

    $('#btn-submit-detail-barang').click(function() {
        var qtyAkanDiterima = $('#qty_akan_diterima').val();
        var id = $('#penerimaan_barang_detail_id').val();
        var indexSelected = 0;
        var itemSelected = null;
        // Cari item yang dihapus dari list_penerimaan_selected
        for (var i = 0; i < list_penerimaan_selected.length; i++) {
            if (list_penerimaan_selected[i].penerimaan_barang_detail_id === id) {
                itemSelected = list_penerimaan_selected[i];
                indexSelected = i;
                break;
            }
        }

        var qtySisa = Number(itemSelected.qty_lpb) - Number(itemSelected.qty_telah_diterima);
        var cekQtySisa = qtySisa - qtyAkanDiterima; // jika hasilnya minus maka invalid

        if (qtyAkanDiterima <= 0 || qtyAkanDiterima == '') {
            Swal.fire({
                icon: 'error',
                title: 'Qty yang akan diterima tidak boleh kosong',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
            return;
        } else if (cekQtySisa < 0 && qtySisa != 0) {
            console.log(cekQtySisa);
            Swal.fire({
                icon: 'error',
                title: 'Qty yang akan diterima tidak boleh lebih dari ' + qtySisa,
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
            return;
        } else {
            list_penerimaan_selected[indexSelected].qty_akan_diterima = qtyAkanDiterima;
            drawTableSelected(list_penerimaan_selected);
            $('.detail-barang-modal').modal('hide');
        }
    })

    function hitungPotonganTambahan() {
        var potongan = Number(destroyFormatRupiah($('.potongan').val()) || 0);
        var tambahan = Number(destroyFormatRupiah($('.tambahan').val()) || 0);
        var harga = 0;
        var total_ppn_11 = 0;
        var total_pph_21 = 0;

        $.each(list_penerimaan_selected, function(i, v) {
            harga += (Number(v.qty_akan_diterima) * Number(destroyFormatRupiah(v.harga)));
        });
        $.each(list_pajak, function(i, v) {
            if (v.tax_type == 'PPN Masukan 11%') {
                total_ppn_11 += destroyFormatRupiah(v.tax_amt);
                harga += Number(destroyFormatRupiah(v.tax_amt));
            }

            if (v.tax_type == 'PPh Pasal 21') {
                total_pph_21 += destroyFormatRupiah(v.tax_amt);
                harga -= destroyFormatRupiah(v.tax_amt);
            }
        })
        var total = harga - potongan + tambahan;
        $('.nominal_faktur').val(greatFormatRupiah(harga));
        $('.total_tambahan_potongan').val(greatFormatRupiah(total));
        $('.total_ppn_11').val(greatFormatRupiah(total_ppn_11));
        $('.total_pph_21').val(greatFormatRupiah(total_pph_21));
    }

    // UPDATE
    function editDetailRow(id) {
        event.preventDefault();
        var itemSelected = null;
        // Cari item yang dihapus dari list_penerimaan_selected
        for (var i = 0; i < list_penerimaan_selected.length; i++) {
            if (list_penerimaan_selected[i].penerimaan_barang_detail_id === id) {
                itemSelected = list_penerimaan_selected[i];
                break;
            }
        }
        var hargaTotal = itemSelected.harga * itemSelected.qty_akan_diterima;

        $('#penerimaan_barang_detail_id').val(id);
        $('#nama_barang_dok').val(itemSelected.nama_barang_dok);
        $('#po_no').val(itemSelected.po_no);
        $('#tanggal').val(itemSelected.tanggal);
        $('#supplier_name').val(itemSelected.supplier_name);
        $('#no_penerimaan_barang').val(itemSelected.no_penerimaan_barang);
        $('#qty_akan_diterima').val(itemSelected.qty_akan_diterima);
        $('#kode_satuan').val(itemSelected.kode_satuan);
        $('#total_harga').val(greatFormatRupiah(hargaTotal));
        $('#total_harga').attr('harga_satuan', itemSelected.harga); // Harga Satuan
        $('.detail-barang-modal').modal('show');
    }

    function getID() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let randomString = '';

        for (let i = 0; i < 10; i++) {
            randomString += characters.charAt(Math.floor(Math.random() * characters.length));
        }

        return randomString;
    };


    <?php if (empty($dataTandaTerimaFaktur)) : ?>
        // CREATE
        function deleteDetailRow(id) {
            var indexToRemove = -1;
            var removedItem = null;

            // Cari item yang dihapus dari list_penerimaan_selected
            for (var i = 0; i < list_penerimaan_selected.length; i++) {
                if (list_penerimaan_selected[i].penerimaan_barang_detail_id === id) {
                    indexToRemove = i;
                    removedItem = list_penerimaan_selected[i];
                    break;
                }
            }

            if (indexToRemove !== -1) {
                list_penerimaan_selected.splice(indexToRemove, 1); // Hapus dari selected list
            }

            // Jika ada item yang dihapus, kembalikan ke list_penerimaan_barang
            if (removedItem) {
                var itemFound = false;

                // Periksa apakah item sudah ada di tabel utama (list_penerimaan_barang)
                for (var j = 0; j < list_penerimaan_barang.length; j++) {
                    if (list_penerimaan_barang[j].penerimaan_barang_detail_id === removedItem.penerimaan_barang_detail_id) {
                        // Tambahkan qty_akan_diterima kembali ke item yang sesuai
                        list_penerimaan_barang[j].qty_akan_diterima += removedItem.qty_akan_diterima;
                        itemFound = true;
                        break;
                    }
                }

                // Jika item belum ada di list_penerimaan_barang, tambahkan item ke dalam daftar
                if (!itemFound) {
                    list_penerimaan_barang.push({
                        penerimaan_barang_detail_id: removedItem.penerimaan_barang_detail_id,
                        po_no: removedItem.po_no,
                        tanggal: removedItem.tanggal,
                        no_penerimaan_barang: removedItem.no_penerimaan_barang,
                        nama_barang_dok: removedItem.nama_barang_dok,
                        qty_lpb: removedItem.qty_lpb,
                        qty_retur: removedItem.qty_retur,
                        qty_telah_diterima: removedItem.qty_telah_diterima,
                        qty_akan_diterima: removedItem.qty_akan_diterima,
                        kode_satuan: removedItem.kode_satuan,
                        harga: removedItem.harga,
                        divisi_id: removedItem.divisi_id,
                        supplier_id: removedItem.supplier_id,
                        supplier_name: removedItem.supplier_name,
                    });
                }
            }

            // Refresh tampilan
            drawTableSelected(list_penerimaan_selected); // Perbarui tabel selected
            drawTableDaftarPenerimaanBarang(list_penerimaan_barang); // Perbarui tabel utama
        }

    <?php else : ?>
        // UPDATE (Jika update langsung delete ke server)
        function deleteDetailRow(id) {
            event.preventDefault();
            Swal.fire({
                icon: 'question',
                title: 'Hapus Daftar Penerimaan Barang ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    var data = new FormData();
                    data.append("penerimaan_barang_detail_id", id);
                    data.append("tanda_terima_faktur_id", "<?= $dataTandaTerimaFaktur['id'] ?>")
                    $.ajax({
                        url: "<?= base_url("tanda-terima-faktur-lokal-bp/delete/daftar-penerimaan"); ?>",
                        data: data,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        method: "POST",
                        dataType: "json",
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            location.reload()
                        },
                    });
                }
            })
        }
    <?php endif; ?>

    function deletePajak(id) {
        var indexToRemove = -1;
        for (var i = 0; i < list_pajak.length; i++) {
            if (list_pajak[i].id == id) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            list_pajak.splice(indexToRemove, 1);
        }
        drawTablePengenaanPajak(list_pajak);

    }

    function editPajak(id) {
        event.preventDefault();
        var indexSelected = -1;
        var taxSelected = null;
        for (var i = 0; i < list_pajak.length; i++) {
            if (list_pajak[i].id == id) {
                indexSelected = i;
                taxSelected = list_pajak[i];
                break;
            }
        }
        $('#tax_id').val(taxSelected.id);
        $('#tax_inv_date').val(taxSelected.tax_inv_date);
        $('#tax_inv_no').val(taxSelected.tax_inv_no);
        $('#tax_type').val(taxSelected.tax_type).change();
        $('#tax_amt').val(greatFormatRupiah(taxSelected.tax_amt));
        $('#tax_status').val(taxSelected.tax_status).change();
        $('#tax_note').val(taxSelected.tax_note);
    }

    function drawTablePengenaanPajak(data) {
        if ($.fn.DataTable.isDataTable('#taxTable')) {
            $('#taxTable').DataTable().clear().draw();
            taxTable.destroy();
        }

        const table = $('#taxTable');
        const tbody = table.find('tbody');
        var no = 1;

        $.each(data, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').text(no++));
            newRow.append($('<td style="text-align: center;">').text(v.tax_inv_date));
            newRow.append($('<td style="text-align: center;">').text(v.tax_inv_no));
            newRow.append($('<td style="text-align: center;">').text(v.tax_type));
            newRow.append($('<td style="text-align: center;">').text(greatFormatRupiah(v.tax_amt)));
            newRow.append($('<td style="text-align: center;">').text(v.tax_status));
            newRow.append($('<td style="text-align: center;">').text(v.tax_note));
            <?php if ($isUsed) : ?>
                newRow.append($('<td style="text-align: center;">').html(
                    `
                    -
                `
                ));
            <?php else : ?>
                newRow.append($('<td style="text-align: center;">').html(
                    `
                     <button class="btn btn-warning mr-1" onclick="editPajak('${v.id}')">
                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                    </button>
                    <button class="btn btn-default mr-1" onclick="deletePajak('${v.id}')" style="background-color:red;">
                        <i class="fa fa-trash fa-sm text-white" aria-hidden="true"></i>                    
                    </button>
                `
                ));
            <?php endif; ?>
            table.find('tbody').append(newRow);
        });

        taxTable = $('#taxTable').DataTable({

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
            pageLength: 50,
            lengthMenu: [
                [50, 100, 200, -1],
                [50, 100, 200, "All"]
            ],
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });

        taxTable.draw();
        hitungPotonganTambahan();

    }

    function drawTableSelected(data) {
        if ($.fn.DataTable.isDataTable('#selectedItemTable')) {
            $('#selectedItemTable').DataTable().clear().draw();
            selectedItemTable.destroy();
        }

        const table = $('#selectedItemTable');
        const tbody = table.find('tbody');
        var no = 1;
        $.each(data, function(i, v) {
            var harga = Math.round(Number(v.qty_akan_diterima) * Number(v.harga));
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').text(no++));
            newRow.append($('<td style="text-align: center;">').text(v.po_no));
            newRow.append($('<td style="text-align: center;">').text(v.tanggal));
            newRow.append($('<td style="text-align: center;">').text(v.no_penerimaan_barang));
            newRow.append($('<td style="text-align: center;">').text(v.supplier_name));
            newRow.append($('<td style="text-align: center;">').text(v.nama_barang_dok));
            newRow.append($('<td style="text-align: center;">').text(v.qty_akan_diterima));
            newRow.append($('<td style="text-align: center;">').text(v.kode_satuan));
            newRow.append($('<td style="text-align: center;">').text(greatFormatRupiah(harga)));
            <?php if ($isUsed) : ?>
                newRow.append($('<td style="text-align: center;">').html(
                    `
                    -
                `
                ));
            <?php else : ?>
                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <button class="btn btn-warning mr-1" onclick="editDetailRow('${v.penerimaan_barang_detail_id}')">
                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                    </button>
                    <button class="btn btn-default mr-1" onclick="deleteDetailRow('${v.penerimaan_barang_detail_id}')" style="background-color:red;">
                        <i class="fa fa-trash fa-sm text-white" aria-hidden="true"></i>                    
                    </button>
                `
                ));
            <?php endif; ?>

            table.find('tbody').append(newRow);
        });

        selectedItemTable = $('#selectedItemTable').DataTable({

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
            pageLength: 50,
            lengthMenu: [
                [50, 100, 200, -1],
                [50, 100, 200, "All"]
            ],
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
        hitungPotonganTambahan();
    }

    function drawTableDaftarPenerimaanBarang(data) {
        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().clear().draw();
            dataTable.destroy();
        }
        const table = $('#dataTable');
        var no = 1;
        $.each(data, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').html(
                `
                    <div class="form-check">
                        <input data-supplier_id="${v.supplier_id}" data-id="${v.penerimaan_barang_detail_id}" autocomplete="one-time-code" class="form-check-input child" type="checkbox">
                    </div>
                `
            ));
            newRow.append($('<td style="text-align: center;">').text(no++));
            newRow.append($('<td style="text-align: center;">').text(v.po_no));
            newRow.append($('<td style="text-align: center;">').text(v.tanggal));
            newRow.append($('<td style="text-align: center;">').text(v.no_penerimaan_barang));
            newRow.append($('<td style="text-align: center;">').text(v.supplier_name));
            newRow.append($('<td style="text-align: center;">').text(v.nama_barang_dok));
            newRow.append($('<td style="text-align: center;">').text(v.qty_lpb));
            newRow.append($('<td style="text-align: center;">').text(v.qty_retur));
            newRow.append($('<td style="text-align: center;">').text(v.qty_telah_diterima));
            <?php if ($isUsed) : ?>
                newRow.append($('<td style="text-align: center;">').html(
                    `
                    -
                `
                ));
            <?php else : ?>
                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <input autocomplete="one-time-code" data-id_input_diterima="${v.penerimaan_barang_detail_id}" class="form-control" type="number" min="0" value="${v.qty_akan_diterima}">
                `
                ));
            <?php endif ?>

            newRow.append($('<td style="text-align: center;">').text(v.kode_satuan));
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
            display: "stripe",
            searching: true,
            pageLength: 50,
            lengthMenu: [
                [50, 100, 200, -1],
                [50, 100, 200, "All"]
            ],
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

    $('#parent').click(function() {
        $('.child:not(:disabled)').prop('checked', this.checked);
    });

    $('.child').click(function() {
        if ($('.child:checked').length == $('.child').length) {
            $('#parent').prop('checked', true);
        } else {
            $('#parent').prop('checked', false);
        }
    });

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $(".no_tanda_terima_faktur").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("tanda-terima-faktur-lokal-bp/generate-tanda-terima-no"); ?>`,
                method: "GET",
                data: {
                    warehouseID: $('#warehouse_id').val(),
                    tanggal_terima: $('#tanggal_terima').val()
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".no_tanda_terima_faktur").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".no_tanda_terima_faktur").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".no_tanda_terima_faktur").val("");
                    }
                }
            })
        } else {
            $(".no_tanda_terima_faktur").attr("readonly", false);
            $(".no_tanda_terima_faktur").val("");
        }
    }


    function changeStatusKeluar() {
        let value = document.getElementById('auto_generate2').checked ? true : false;
        if (value) {
            $(".no_tanda_keluar_faktur").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("tanda-terima-faktur-lokal-bp/generate-tanda-keluar-no"); ?>`,
                method: "GET",
                data: {
                    warehouseID: $('#warehouse_id').val(),
                    tanggal_terima: $('#tanggal_terima').val()
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".no_tanda_keluar_faktur").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".no_tanda_keluar_faktur").attr("readonly", false);
                        $("#auto_generate2").prop("checked", false);
                        $(".no_tanda_keluar_faktur").val("");
                    }
                }
            })
        } else {
            $(".no_tanda_keluar_faktur").attr("readonly", false);
            $(".no_tanda_keluar_faktur").val("");
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

    function print(url) {
        window.open(url, "_blank");
    }

    function listDaftarPenerimaanBarang() {
        <?php if (empty($dataTandaTerimaFaktur)) : ?>

            $.ajax({
                url: `<?= base_url("tanda-terima-faktur-lokal-bp/daftar-penerimaan-barang"); ?>`,
                method: "GET",
                data: {
                    supplierID: $('#supplier_id').val(),
                    divisiID: $('#divisi_id').val()
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        list_penerimaan_barang = res.data;
                        drawTableDaftarPenerimaanBarang(res.data);
                    }
                }
            });
        <?php endif; ?>
    }
</script>


<?php if (!empty($dataTandaTerimaFaktur)) : ?>
    <script>
        $('#supplier_id').val("<?= $dataTandaTerimaFaktur['supplier_id'] ?>").change();
        // $('#supplier_id').attr('disabled', true);

        function daftarPenerimaanFromDB() {
            <?php foreach ($dataDetailTandaTerimaFaktur as $d) : ?>
                list_penerimaan_selected.push({
                    divisi_id: "<?= $d['divisi_id'] ?>",
                    penerimaan_barang_detail_id: "<?= $d['penerimaan_barang_detail_id'] ?>",
                    po_no: "<?= $d['po_no'] ?>",
                    tanggal: "<?= $d['tanggal'] ?>",
                    no_penerimaan_barang: "<?= $d['no_penerimaan_barang'] ?>",
                    nama_barang_dok: "<?= str_replace('"', '\"', $d['nama_barang_dok']) ?>",
                    qty_lpb: "<?= $d['qty_lpb'] ?>",
                    qty_retur: "<?= $d['qty_retur'] ?>",
                    qty_telah_diterima: "<?= $d['qty_telah_diterima'] ?>",
                    qty_akan_diterima: "<?= $d['qty_akan_diterima'] ?>",
                    kode_satuan: "<?= $d['kode_satuan'] ?>",
                    supplier_name: "<?= $d['supplier_name'] ?>",
                    supplier_id: "<?= $d['supplier_id'] ?>",
                    harga: "<?= $d['harga'] ?>"
                });
            <?php endforeach; ?>
        }
        daftarPenerimaanFromDB();
        drawTableSelected(list_penerimaan_selected);

        <?php foreach ($dataPenerimaanBarang as $d) : ?>
            list_penerimaan_barang.push({
                penerimaan_barang_detail_id: "<?= $d['penerimaan_barang_detail_id'] ?>",
                po_no: "<?= $d['po_no'] ?>",
                tanggal: "<?= $d['tanggal'] ?>",
                no_penerimaan_barang: "<?= $d['no_penerimaan_barang'] ?>",
                nama_barang_dok: "<?= str_replace('"', '\"', $d['nama_barang_dok']) ?>",
                qty_lpb: "<?= $d['qty_lpb'] ?>",
                supplier_name: "<?= $d['supplier_name'] ?>",
                qty_retur: "<?= $d['qty_retur'] ?>",
                qty_telah_diterima: "<?= $d['qty_telah_diterima'] ?>",
                qty_akan_diterima: "<?= $d['qty_akan_diterima'] ?>",
                kode_satuan: "<?= $d['kode_satuan'] ?>",
                supplier_id: "<?= $d['supplier_id'] ?>",
                harga: "<?= $d['harga'] ?>"
            });
        <?php endforeach; ?>
        drawTableDaftarPenerimaanBarang(list_penerimaan_barang);

        <?php foreach ($dataPajak as $d) : ?>
            list_pajak.push({
                id: getID(),
                tax_inv_date: "<?= date('d/m/Y', strtotime($d['tax_inv_date']))  ?>",
                tax_inv_no: "<?= $d['tax_inv_no'] ?>",
                tax_type: "<?= $d['tax_type'] ?>",
                tax_amt: "<?= $d['tax_amt'] ?>",
                tax_status: "<?= $d['tax_status'] ?>",
                tax_note: "<?= $d['tax_note'] ?>"
            });
            drawTablePengenaanPajak(list_pajak);
        <?php endforeach; ?>

        function remove(id) {
            const csrfToken = '<?= csrf_token() ?>';
            const csrf = $(`[name="${csrfToken}"]`);
            Swal.fire({
                icon: 'question',
                title: 'Hapus Tanda Terima Faktur Ini ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    formData.append('id', id);
                    $.ajax({
                        url: "<?= base_url("tanda-terima-faktur-lokal-bp/delete"); ?>",
                        data: formData,
                        method: "POST",
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            }).then((result) => {
                                window.location.href = "<?= base_url('tanda-terima-faktur-lokal-bp') ?>"
                            });

                        },
                    });
                }
            });
        }
    </script>
<?php else : ?>
    <script>
        $('#tanggal_terima').change(function() {
            changeStatus();
            changeStatusKeluar();
        });
        changeStatus();
        changeStatusKeluar();
    </script>
<?php endif; ?>
<?= $this->endSection(); ?>