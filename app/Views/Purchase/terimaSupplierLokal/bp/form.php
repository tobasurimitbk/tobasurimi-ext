<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tanda Terima Faktur Lokal</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("terima-faktur-import"); ?>">
                Batal
            </a>
            <?php if (!empty($dataTerimaFaktur)) { ?>
                <?php if ($statusUpdate) { ?>
                    <button class="btn btn-hapus delete-parent float-right">
                        Hapus
                    </button>
                    <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("terima-faktur-import/print/{$dataTerimaFaktur->id}") ?>')">
                        Print
                    </button>
                <?php } ?>

                <?php if (!$statusUpdate) { ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit-form">
                        Simpan
                    </button>
                    <button class="btn btn-show-form btn-save float-right btn-submit-cetak bsc">
                        Simpan dan Cetak
                    </button>
                <?php
                }
                ?>

            <?php } else { ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-form">
                    Simpan
                </button>
            <?php } ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" value="<?= !empty($dataTerimaFaktur) ? $dataTerimaFaktur->id : ""; ?>" type="hidden" class="id" name="id" id="id" />
                <input autocomplete="one-time-code" value="<?= !empty($dataTerimaFaktur) ? $dataTerimaFaktur->sender : ""; ?>" type="hidden" class="sender" name="sender" id="sender" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control no_tanda_terima_faktur" id="no_tanda_terima_faktur" name="no_tanda_terima_faktur" placeholder="No Tanda Terima Faktur" value="">
                                    <label for="floatingInput">No Terima Faktur</label>
                                </div>
                                <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="<?= date('d/m/Y') ?>" class="form-control input-picker datepicker" id="tanggal_terima" name="tanggal_terima" placeholder="Tanggal Terima sFaktur">
                            <label for="floatingInput">Tanggal Terima</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <?php if (!empty($dataTerimaFaktur)) : ?>
                                <input autocomplete="one-time-code" value="<?= $dataTerimaFaktur->supplierName ?? ""; ?>" type="text" class="form-control " placeholder="Nama Supplier " disabled readonly>
                                <label for="floatingInput">Supplier</label>
                            <?php else : ?>
                                <select class="form-select supplier_id" name="supplier_id" id="supplier_id">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($dataSupplier)) {
                                        foreach ($dataSupplier as $supplier) {
                                    ?>
                                            <option value="<?= $supplier->id; ?>" data-name="<?= $supplier->name; ?>" <?= !empty($dataTerimaFaktur) ? ($dataTerimaFaktur->supplier_id === $supplier->id ? "selected" : "") : ""; ?>><?= "$supplier->kode - $supplier->name"; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            <?php endif; ?>
                            <label for="floatingInput">Supplier</label>
                        </div>
                    </div>
                </div>
                <div class="row">

                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="<?= !empty($dataTerimaFaktur) ? "text" : "number"; ?>" autocomplete="one-time-code" <?= ($isUpdate ?? false) ? "disabled=true" : ""; ?> value="<?= !empty($dataTerimaFaktur) ? ($dataTerimaFaktur == null ? 0 : toRupiah($dataTerimaFaktur->nominal_faktur)) : ''; ?>" class="form-control" id="nominal_faktur" name="nominal_faktur" placeholder="Nominal Faktur">
                            <label for="floatingInput">Nominal Faktur</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="<?= !empty($dataTerimaFaktur) ? ($dataTerimaFaktur->jatuh_tempo ? date("d/m/Y", strtotime($dataTerimaFaktur->jatuh_tempo)) : "") : ""; ?>" class="form-control input-picker datepicker" id="jatuh_tempo" name="jatuh_tempo" placeholder="Jatuh Tempo">
                            <label for="floatingInput">Jatuh Tempo</label>
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
                                        <th>No.</th>
                                        <th onclick="changeSort('faktur_no')" class="sort">No. PO</th>
                                        <th onclick="changeSort('sender')" class="sort">Tgl. LPB</th>
                                        <th onclick="changeSort('nominal_faktur')" class="sort">No. LPB</th>
                                        <th onclick="changeSort('due_date')" class="sort">Nama Barang</th>
                                        <th onclick="changeSort('date_of')" class="sort">Qty LPB</th>
                                        <th onclick="changeSort('date_of')" class="sort">Qty Retur</th>
                                        <th onclick="changeSort('date_of')" class="sort">Qty Telah Terima</th>
                                        <th onclick="changeSort('date_of')" class="sort">Qty Akan Diterima</th>
                                        <th onclick="changeSort('recipient')" class="sort">Satuan</th>
                                    </tr>
                                </thead>
                                <tbody class="body-table" id="body-table" style="cursor: pointer;">

                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-primary" id="select-item-btn">Pilih</button>
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
                                        <th>No. PO</th>
                                        <th>Tgl. LPB</th>
                                        <th>No. LPB</th>
                                        <th>Nama Barang</th>
                                        <th>Qty</th>
                                        <th>satuan</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="body-table">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <input type="hidden" name="receive_date" value="<?= date('d/m/Y') ?>">
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="number" class="form-control information" name="tambahan" id="tambahan" value="" placeholder="Keterangan">
                            <label for="floatingInput">Potongan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="number" class="form-control information" name="potongan" id="potongan" value="" placeholder="Keterangan">
                            <label for="floatingInput">Penambahan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" readonly disabled type="text" class="form-control recipient" id="InvFinalAmt" value="" />
                            <label for="floatingInput">Total Setelah Potongan dan Tambahan</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="" type="text" class="form-control recipient" name="recipient" id="recipient" placeholder="Penerima">
                            <label for="floatingInput">Penerima</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <textarea style="height: 10px;" autocomplete="one-time-code" class="form-control information text-area-all" name="information" id="information" placeholder="Keterangan"><?= $dataTerimaFaktur->information ?? ""; ?></textarea>
                            <label for="floatingInput">Keterangan</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Pengenaan Pajak</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" class="form-control input-picker datepicker" id="tax_inv_date" name="tax_inv_date" placeholder="Tanggal Faktur Pajak">
                            <label for="floatingInput">Tanggal Faktur Pajak</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control" id="tax_inv_no" name="tax_inv_no" placeholder="No. Faktur Pajak">
                            <label for="floatingInput">No. Faktur Pajak</label>
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
                            <label for="floatingInput">Pilih Pajak</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="number" class="form-control" id="tax_amt" name="tax_amt" placeholder="Jumlah">
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
                            <label for="floatingInput">Status</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <textarea style="height: 10px;" autocomplete="one-time-code" class="form-control information text-area-all" id="tax_note" name="tax_note" placeholder="Keterangan"></textarea>
                            <label for="floatingInput">Keterangan</label>
                        </div>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-md-12 col-table-button-tts">
                        <button type="button" class="btn btn-primary" id="add-tax-btn">Tambah Pengenaan Pajak</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts" id="taxTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No.</th>
                                        <th>Tgl. Faktur Pajak</th>
                                        <th>No. Faktur Pajak</th>
                                        <th>Pajak</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                        <th>Action</th>
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
</section>
<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    $("#tanggal_terima").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });
</script>
<?= $this->endSection(); ?>