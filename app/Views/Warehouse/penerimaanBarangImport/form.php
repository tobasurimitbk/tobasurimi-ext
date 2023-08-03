<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1 class="title-name">Tambah</h1>
    <div class="col-button-tambah-spp">
        <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("penerimaan-barang-import"); ?>">
            Batal
        </a>
        <?php if(!empty($dataPenerimaanBarang)){ ?> 

            <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("penerimaan-barang-import/print/"); ?><?= $dataPenerimaanBarang->id; ?>')">
                Print
            </button>
            <?php 
            }
            ?> 
            <?php if(!empty($dataPenerimaanBarang)){ 
                if($dataPenerimaanBarang->status_post === "WAITING"){ 
            ?> 
            <button class="btn btn-show-form btn-save float-right btn-submit-parent-and-close">
                Close (PO & Penerimaan)
            </button>
            <?php }
            } else { ?> 
            <button class="btn btn-show-form btn-save float-right btn-submit-parent-and-close">
                Simpan & Close (PO & Penerimaan)
            </button>
            <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                Simpan
            </button>
        <?php } ?> 
    </div>
</div>
<div class="card">
    <div class="card-body">
        <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
            <input type="hidden" class="id" name="id" id="id" value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang->id : ""; ?>" />
            <?= csrf_field() ?>
            <div class="row mb-1">
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">Data PO</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= !empty($dataPenerimaanBarang) ? 'disabled=true' : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang->no_penerimaan_barang : ""; ?>" type="text" class="form-control no_penerimaan_barang" id="no_penerimaan_barang" name="no_penerimaan_barang" placeholder="No. Penerimaan">
                                <label for="floatingInput">No. Penerimaan</label>
                            </div>
                            <div style="<?= !empty($dataPenerimaanBarang) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                <input style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="hidden" name="tipe" value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang->tipe_bahan : ""; ?>" />
                        <select <?= !empty($dataPenerimaanBarang) ? 'disabled=true' : ''; ?> onchange="changeTipeBahan()" class="form-select tipe_bahan" id="tipe_bahan" name="tipe_bahan" aria-label="Floating label select example">
                            <option value="BAKU" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->tipe_bahan === "BAKU" ? "selected" : "") : ""; ?>>Bahan Baku</option>
                            <option value="PENOLONG" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->tipe_bahan === "PENOLONG" ? "selected" : "") : ""; ?>>Bahan Penolong</option>
                        </select>
                        <label for="floatingInput">Tipe</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select <?= !empty($dataPenerimaanBarang) ? 'disabled=true' : ''; ?> class="form-select supplier_id" id="supplier_id" name="supplier_id" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php
                            if (!empty($dataSupplier)) {
                                foreach ($dataSupplier as $supplier) {
                            ?>
                                    <option value="<?= $supplier["id"]; ?>" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->supplier_id === $supplier["id"] ? "selected" : "") : ""; ?>><?= $supplier["name"]; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <label for="floatingInput">Supplier</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <?php if(!empty($dataPenerimaanBarang)){ 
                        ?>
                            <select multiple disabled="true" class="form-select multiple_po_id" name="multiple_po_id[]" id="multiple_po_id[]">
                                <option value=""></option>
                                <?php
                                    $dataLoop = json_decode($dataPenerimaanBarang->multiple_po_no);
                                    foreach ($dataLoop as $no) {
                                ?>
                                        <option value="<?= $no; ?>" selected><?= $no; ?></option>
                                <?php
                                }
                                ?>
                            </select> 
                        <?php } else {
                        ?>
                            <select multiple class="form-select multiple_po_id" name="multiple_po_id[]" id="multiple_po_id[]">
                                <option value=""></option>
                                <?php
                                if (!empty($dataNo)) {
                                    foreach ($dataNo as $no) {
                                ?>
                                        <option value="<?= $no["id"]; ?>" <?= (!empty($dataPenerimaanBarang) ? (in_array($no["id"], ($dataPenerimaanBarang->multiple_po_id ? json_decode($dataPenerimaanBarang->multiple_po_id) : [])) ? "selected" : "") : ""); ?>><?= $no["po_no"]; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                        <?php } 
                        ?>
                        <label for="floatingInput">No. PO</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select <?= !empty($dataPenerimaanBarang) ? 'disabled=true' : ''; ?> class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php
                            if (!empty($dataWarehouse)) {
                                foreach ($dataWarehouse as $warehouse) {
                            ?>
                                    <option value="<?= $warehouse["id"]; ?>" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->warehouse_id === $warehouse["id"] ? "selected" : "") : ""; ?>><?= $warehouse["warehouse_name"]; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <label for="floatingInput">Warehouse</label>
                    </div>
                </div>
            </div>
            <div class="row mb-1">
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">Data Dokumen</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select <?= !empty($dataPenerimaanBarang) ? 'disabled=true' : ''; ?> class="form-select aju_document_type" id="aju_document_type" name="aju_document_type" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php
                            if (!empty($dataAJU)) {
                                foreach ($dataAJU as $aju) {
                            ?>
                                    <option value="<?= $aju["id"]; ?>" <?= (!empty($dataPenerimaanBarang) ? ($aju["id"] === $dataPenerimaanBarang->aju_document_type ? "selected" : "") : ""); ?>><?= $aju["value"]; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <label for="floatingInput">Jenis Dokumen</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input <?= !empty($dataPenerimaanBarang) ? 'disabled=true' : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang->aju_no : ""; ?>" type="text" class="form-control aju_no" name="aju_no" id="aju_no" placeholder="No. Invoice">
                        <label for="floatingInput">No. AJU</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($dataPenerimaanBarang) ? 'disabled=true' : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->validation_date ? date("d/m/Y", strtotime($dataPenerimaanBarang->validation_date)) : "") : ""; ?>" type="text" class="form-control input-picker validation_date" id="validation_date" name="validation_date" placeholder="Tanggal Pendaftaran">
                            <label for="floatingInput">Tanggal Pendaftaran</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($dataPenerimaanBarang) ? 'disabled=true' : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang->no_registration : ""; ?>" type="text" class="form-control no_registration" id="no_registration" name="no_registration" placeholder="No. Pendaftaran">
                            <label for="floatingInput">No. Pendaftaran</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-1">
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">No. Invoice</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input <?= !empty($dataPenerimaanBarang) ? 'disabled=true' : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang->invoice_no : ""; ?>" type="text" class="form-control invoice_no" id="invoice_no" name="invoice_no" placeholder="No. Invoice">
                        <label for="floatingInput">No. Invoice</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input <?= !empty($dataPenerimaanBarang) ? 'disabled=true' : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang->packaging : ""; ?>" type="text" class="form-control packaging" id="packaging" name="packaging" placeholder="Kemasan">
                        <label for="floatingInput">Kemasan</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input <?= !empty($dataPenerimaanBarang) ? 'disabled=true' : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang->total_weight : ""; ?>" type="number" class="form-control total_weight" id="total_weight" name="total_weight" placeholder="Berat">
                        <label for="floatingInput">Berat</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input <?= !empty($dataPenerimaanBarang) ? 'disabled=true' : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? number_format($dataPenerimaanBarang->shipping_cost) : ""; ?>" onkeyup="formatNumber(this)" type="text" class="form-control shipping_cost" name="shipping_cost" id="shipping_cost" placeholder="Biaya Ongkos Kirim (Opsional)">
                        <label for="floatingInput">Biaya Ongkos Kirim (Opsional)</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input <?= !empty($dataPenerimaanBarang) ? 'disabled=true' : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? number_format($dataPenerimaanBarang->biaya_masuk) : ""; ?>" onkeyup="formatNumber(this)" type="text" class="form-control biaya_masuk" name="biaya_masuk" id="biaya_masuk" placeholder="Biaya Masuk">
                        <label for="floatingInput">Biaya Masuk</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input value="<?= !empty($dataPenerimaanBarang) ? formatter($dataPenerimaanBarang->ppn, "STR_TO_INT") : ""; ?>" <?= !empty($dataPenerimaanBarang) ? 'disabled=true' : ''; ?> type="text" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control ppn" name="ppn" id="ppn" placeholder="PPN % (Opsional)">
                        <label for="floatingInput">PPN % (Opsional)</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input value="<?= !empty($dataPenerimaanBarang) ? formatter($dataPenerimaanBarang->pph, "STR_TO_INT") : ""; ?>" <?= !empty($dataPenerimaanBarang) ? 'disabled=true' : ''; ?> type="text" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control pph" name="pph" id="pph" placeholder="PPH % (Opsional)">
                        <label for="floatingInput">PPH % (Opsional)</label>
                    </div>
                </div>
            </div>
        </form>
        <div class="col-subtitle-modal">
            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="form-label font-weight-bold modal-sub-title">List Barang</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table nowrap table-hover-tobasurimi dataTable table-tambah-spp" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No.</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            <th>Jml. Order</th>
                            <th>Jml. Diterima</th>
                            <th>Sisa</th>
                            <th>Jml. Masuk</th>
                            <th>Harga</th>
                            <th>Sub Total</th>
                            <th>Keterangan</th>
                            <!-- <th>Hapus</th> -->
                        </tr>
                    </thead>
                    <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">
                    <?php 
                        $no = 1;
                        $total_jml_order = 0;
                        $total_jml_masuk = 0;
                        $total_qty_diterima = 0;
                        $total_remaining_qty = 0;
                        $total_harga = 0;
                        $total_sub_total = 0;

                        if(!empty($dataPenerimaanBarang) && !empty($dataPenerimaanBarangDetail)){ 
                            foreach($dataPenerimaanBarangDetail as $details){  
                                $total_jml_order = $total_jml_order + $details["qty"];
                                $total_jml_masuk = $total_jml_masuk + $details["jml_masuk"];
                                $total_qty_diterima = $total_qty_diterima + $details["qty_diterima"];
                                $total_remaining_qty = $total_remaining_qty + $details["remaining_qty"];
                                $total_harga = $total_harga + ($details["harga"] ? (int)$details["harga"] : 0);
                                $total_sub_total = $total_sub_total + ($details["sub_total"] ? (int)$details["sub_total"] : 0);
                        ?> 
    
                            <tr>
                                    <td><?= $no; ?></td>
                                    <td><?= $details["kode_barang"]; ?></td>
                                    <td><?= $details["nama_barang"]; ?></td>
                                    <td><?= $details["nama_satuan"]; ?></td>
                                    <td><?= $details["qty"]; ?></td>
                                    <td><?= $details["qty_diterima"] ? formatter($details["qty_diterima"], "CURR_TO_INT") : 0; ?></td>
                                    <td><?= $details["remaining_qty"] ? formatter($details["remaining_qty"], "CURR_TO_INT") : 0; ?></td>
                                    <td><?= $details["jml_masuk"]; ?></td>
                                    <td><?= $details["harga"] ? number_format($details["harga"]) : 0; ?></td>
                                    <td><?= $details["sub_total"] ? number_format($details["sub_total"]) : 0; ?></td>
                                    <td><?= $details["keterangan"]; ?></td>
                            </tr>
                        <?php 
                            $no++;
                            }
                        } ?> 
                    </tbody>
                    <tfoot class="foot-detail-table" id="foot-detail-table">
                        <tr>
                            <td></td>
                            <td>GRAND TOTAL</td>
                            <td colspan="2"></td>
                            <td><b><?= $total_jml_order; ?></b></td>
                            <td><b><?= $total_qty_diterima; ?></b></td>
                            <td><b><?= $total_remaining_qty; ?></b></td>
                            <td><b><?= $total_jml_masuk; ?></b></td>
                            <td><b><?= number_format($total_harga); ?></b></td>
                            <td><b><?= number_format($total_sub_total); ?></b></td>
                            <td colspan="1"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
</section>

<div class="modal detail-modal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-detail-name"></label> Barang</h5>
            </div>
            <div class="modal-body">
                <form class="detail-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id_detail" name="id_detail" id="id_detail" />
                    <input type="hidden" class="purchase_order_details_id" name="purchase_order_details_id" id="purchase_order_details_id" />
                    <div class="row">
                        <div class="col mb-3">
                            <h5 class="title-tambah-barang">Data Barang</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="hidden" class="kode" name="kode" id="kode" />
                                <input type="hidden" class="barang_id" name="barang_id" id="barang_id" />
                                <input type="hidden" class="unit" name="unit" id="unit" />
                                <input type="text" readonly="true" class="form-control kode_barang" id="kode_barang" name="kode_barang" placeholder="Kode Barang">
                                <label for="floatingInput">Kode Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" readonly="true" class="form-control nama_barang" id="nama_barang" name="nama_barang" placeholder="Nama Barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" readonly="true" class="form-control satuan_order" id="satuan_order" name="satuan_order" placeholder="Satuan Order">
                                <label for="floatingInput">Satuan Order</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" readonly="true" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control qty" id="qty" name="qty" placeholder="Jumlah Order">
                                <label for="floatingInput">Jumlah Order</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" readonly="true" class="form-control qty_diterima" id="qty_diterima" name="qty_diterima" placeholder="Qty Diterima">
                                <label for="floatingInput">Qty Diterima</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" readonly="true" class="form-control remaining_qty" id="remaining_qty" name="remaining_qty" placeholder="Sisa">
                                <label for="floatingInput">Sisa</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control jml_masuk" id="jml_masuk" name="jml_masuk" placeholder="Jumlah Diterima">
                                <label for="floatingInput">Jumlah Masuk</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <textarea readonly="true" class="form-control keterangan text-area-all" id="keterangan" name="keterangan" placeholder="Keterangan"></textarea>
                                <label for="floatingInput">Keterangan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col mb-3">
                            <h5 class="title-tambah-barang">Detail Barang di Dokumen</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control nama_barang_dokumen" id="nama_barang_dokumen" name="nama_barang_dokumen" placeholder="Nama Barang di dokumen">
                                <label for="floatingInput">Nama Barang di dokumen</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onkeyup="formatNumber(this)" type="text" class="form-control harga_barang_jasa" name="harga_barang_jasa" id="harga_barang_jasa" placeholder="Harga Satuan">
                                <label for="floatingInput">Harga Satuan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onkeyup="formatNumber(this)" type="text" class="form-control nilai_sub_total" name="nilai_sub_total" id="nilai_sub_total" placeholder="Total Harga">
                                <label for="floatingInput">Total Harga</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-hide-detail btn-discard mr-2">Batal</button>
                    <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
                    <!-- <button type="button" class="btn btn-discard delete-detail delete-btn">Hapus</button> -->
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let list_items = [];
    let list_delete = [];
    var row = 0;
    let total_jml_order = 0;
    let total_jml_masuk = 0;
    let total_qty_diterima = 0;
    let total_remaining_qty = 0;
    let total_jml_harga = 0;
    let total_jml_sub_total = 0;
    var priceEdit = 0;
    var sub_totalEdit = 0;

    <?php if(!empty($dataPenerimaanBarangDetail)){ 
        foreach($dataPenerimaanBarangDetail as $details){  
    ?>

    priceEdit = Number('<?= $details["harga"]; ?>');
    sub_totalEdit = Number('<?= $details["sub_total"]; ?>');
    row = row + 1;

    total_jml_order = total_jml_order + Number(<?= $details["qty"]; ?>);
    total_jml_masuk = total_jml_masuk + Number(<?= $details["jml_masuk"]; ?>);
    total_qty_diterima = total_qty_diterima + Number(<?= $details["qty_diterima"]; ?>);
    total_remaining_qty = total_remaining_qty + Number(<?= $details["remaining_qty"]; ?>);
    total_jml_harga = total_jml_harga + priceEdit;
    total_jml_sub_total = total_jml_sub_total + sub_totalEdit;

    list_items.push({
        id: <?= $details["id"]; ?>,
        row: row,
        purchase_order_details_id: Number(<?= $details["purchase_order_details_id"]; ?>),
        barang_id: Number(<?= $details["barang_id"]; ?>),
        unit: Number(<?= $details["unit"]; ?>),
        kode_barang: '<?= $details["kode_barang"]; ?>',
        nama_barang: '<?= $details["nama_barang"]; ?>',
        nama_barang_dokumen: '<?= $details["nama_barang_dok"]; ?>',
        qty: Number(<?= $details["qty"]; ?>),
        qty_diterima: Number(<?= $details["qty_diterima"]; ?>),
        remaining_qty: Number(<?= $details["remaining_qty"]; ?>),
        satuan: '<?= $details["nama_satuan"]; ?>',
        jml_masuk: Number(<?= $details["jml_masuk"]; ?>),
        harga: Number('<?= $details["harga"] ? $details["harga"] : 0; ?>').toLocaleString(),
        sub_total: Number('<?= $details["sub_total"] ? $details["sub_total"] : 0; ?>').toLocaleString(),
        keterangan: '<?= $details["keterangan"]; ?>',
    })
    <?php 
        }
    ?>
    <?php
    } ?>

var validator_detail = $(".detail-form").validate({
        rules: {
            kode_barang: {
                required: true
            },
            nilai_jml_masuk: {
                required: true
            },
            qty: {
                required: true
            },
            nilai_jml_masuk: {
                required: true
            },
            harga: {
                required: true
            },
            sub_total: {
                required: true
            },
        },
        messages: {
            kode_barang: {
                required: "Kode wajib diisi"
            },
            nilai_jml_masuk: {
                required: "Jumlah diterima wajib diisi"
            },
            qty: {
                required: "Qty wajib diisi"
            },
            nilai_jml_masuk: {
                required: "Jumlah Masuk wajib diisi"
            },
            harga: {
                required: "Harga wajib diisi"
            },
            sub_total: {
                required: "sub_total wajib diisi"
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

    $(document).ready(function() {
        var validator = $(".create-form").validate({
            rules: {
                no_penerimaan_barang: {
                    required: true
                },
                supplier_id: {
                    required: true
                },
                "multiple_po_id[]": {
                    required: true
                },
                warehouse_id: {
                    required: true
                },
                aju_document_type: {
                    required: true
                },
                aju_no: {
                    required: true,
                },
                validation_date: {
                    required: true,
                },
                no_registration: {
                    required: true,
                },
                invoice_no: {
                    required: true,
                },
                packaging: {
                    required: true,
                },
                total_weight: {
                    required: true,
                },
                biaya_masuk: {
                    required: true,
                },
                status_post: {
                    required: true,
                },
                status_penerimaan: {
                    required: true,
                }
            },
            messages: {
                no_penerimaan_barang: {
                    required: "No. Penerimaan wajib diisi"
                },
                supplier_id: {
                    required: "Supplier wajib diisi"
                },
                "multiple_po_id[]": {
                    required: "No. PO wajib diisi"
                },
                warehouse_id: {
                    required: "Warehouse wajib diisi"
                },
                aju_document_type: {
                    required: "Jenis Dokumen wajib diisi"
                },
                aju_no: {
                    required: "No. AJU wajib diisi"
                },
                validation_date: {
                    required: "Tanggal wajib diisi"
                },
                no_registration: {
                    required: "No. Registrasi wajib diisi"
                },
                invoice_no: {
                    required: "No. Invoice wajib diisi"
                },
                packaging: {
                    required: "Packaging wajib diisi"
                },
                total_weight: {
                    required: "Weight wajib diisi"
                },
                biaya_masuk: {
                    required: "Biaya Masuk wajib diisi"
                },
                status_post: {
                    required: "Status Post wajib diisi"
                },
                status_penerimaan: {
                    required: "Status Penerimaan wajib diisi"
                },
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
                var elem = $(element);
                console.log(elem);
                if (elem.hasClass("multiple_po_id")) {
                    element = $(".select2-selection--multiple").parent();
                    error.insertAfter(element);
                } else if (elem.hasClass("select2-hidden-accessible")) {
                    element = $("#select2-" + elem.attr("id") + "-container").parent(); 
                    error.insertAfter(element);
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).closest('.col-md-6').addClass('has-error');
                $(element).addClass('select-class');

            },
            unhighlight: function(element) {
                $(element).closest('.col-md-6').removeClass('has-error');
                $(element).removeClass('select-class');
            },
        });

        $(".validation_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        // AJU DOCUMENT TYPE
        $('.aju_document_type').select2({
            placeholder: "",
            theme: "bootstrap-5",
        })

        //CSS SELECT2 FLOATING LABEL
        $('.aju_document_type')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.aju_document_type')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.aju_document_type')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // WAREHOUSE
        $('.warehouse_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
        })

        //CSS SELECT2 FLOATING LABEL
        $('.warehouse_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.warehouse_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.warehouse_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // PO NO
        $('.multiple_po_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
        })

        //CSS SELECT2 FLOATING LABEL
        $('.multiple_po_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.multiple_po_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.multiple_po_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $('.multiple_po_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // SUPPLIER
        $('.supplier_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.supplier_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.supplier_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.supplier_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // delete
        $(".delete-parent").click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Hapus Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    let id = $(".id").val();
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("penerimaan-barang-import/delete"); ?>",
                        data: {
                            id: id
                        },
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                                    })
                                    .then(() => {
                                        window.location.href = "<?= base_url("penerimaan-barang-import"); ?>"
                                    })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                stopLoading()
                            }
                        },
                        onError: function(response) {
                            csrf.val(response.token);
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Gagal Dihapus, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            })
                            stopLoading()
                        }
                    });
                }
            })
        })

        $(".btn-submit-detail").click(function() {
            let row_detail = $(".id_detail").val() ? Number($(".id_detail").val()) : 0;
            let kode_barang = $(".kode").val()
            let barang_id = $(".barang_id").val() ? Number($(".barang_id").val()) : 0
            let nama_barang = $(".nama_barang").val()
            let nama_barang_dokumen = $(".nama_barang_dokumen").val()
            let satuan = $(".satuan_order").val()
            let qty = $(".qty").val() ? Number($(".qty").val()) : 0
            let keterangan = $(".keterangan").val()
            let unit = $(".unit").val() ? Number($(".unit").val()) : 0
            let nilai_sub_total = $(".nilai_sub_total").val() ? $(".nilai_sub_total").val() : 0
            let purchase_order_details_id = $(".purchase_order_details_id").val()
            let harga = $(".harga_barang_jasa").val()
            let jml_masuk = $(".jml_masuk").val() ? Number($(".jml_masuk").val()) : 0
            let qty_diterima = $(".qty_diterima").val() ? Number($(".qty_diterima").val()) : 0
            let remaining_qty = $(".remaining_qty").val() ? Number($(".remaining_qty").val()) : 0
            var all_qty = 0;

                let validate_same = false;
                let validate_jml_masuk = false;

                if(jml_masuk > remaining_qty)
                {
                    validate_jml_masuk = true;
                }

                if(validate_jml_masuk)
                {
                    Swal.fire({
                        icon: 'error',
                        title: "Qty masuk sudah melebihi jumlah yang sudah diterima",
                        confirmButtonColor: '#4e73df',
                    })
                }
                else
                {
                    // gk boleh kosong
                    if(jml_masuk === 0)
                    {
                        Swal.fire({
                            icon: 'error',
                            title: "Jumlah Masuk tidak boleh kosong",
                            confirmButtonColor: '#4e73df',
                        })
                    }
                    else
                    {
                    // update detail
                    if(row_detail)
                    {
                        if ($(".detail-form").valid()) {
                            Swal.fire({
                                icon: 'question',
                                title: 'Simpan Data?',
                                confirmButtonColor: '#4e73df',
                                cancelButtonColor: '#d33',
                                showCancelButton: true,
                                reverseButtons: true,
                                confirmButtonText: 'Simpan',
                                cancelButtonText: 'Batal',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    let new_list_items = []
                                    let tag_html = "";
                                    let tag_total = "";

                                    row = 0;

                                    total_jml_order = 0;
                                    total_qty_diterima = 0;
                                    total_remaining_qty = 0;
                                    total_jml_masuk = 0;
                                    total_jml_harga = 0;
                                    total_jml_sub_total = 0;

                                    $(".body-detail-table").empty()

                                    list_items.map(item => {
                                        if(item.row == row_detail)
                                        {
                                            tag_html += `<tr>`;
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="${jml_masuk}" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}"  data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += row + 1;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="${jml_masuk}" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}"  data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += kode_barang;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="${jml_masuk}" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}"  data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += nama_barang;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="${jml_masuk}" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}"  data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += satuan;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="${jml_masuk}" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}"  data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += qty;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="${jml_masuk}" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}"  data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += qty_diterima;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="${jml_masuk}" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}"  data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += remaining_qty;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="${jml_masuk}" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}"  data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += jml_masuk;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="${jml_masuk}" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}"  data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += harga;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="${jml_masuk}" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}"  data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += nilai_sub_total;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="${jml_masuk}" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}"  data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += keterangan;
                                            tag_html += "</td>";
                                            tag_html += "</tr>";

                                            new_list_items.push({
                                                id: item.id,
                                                purchase_order_details_id: purchase_order_details_id,
                                                row: row + 1,
                                                barang_id: barang_id,
                                                unit: unit,
                                                kode_barang: kode_barang,
                                                nama_barang: nama_barang,
                                                nama_barang_dokumen: nama_barang_dokumen,
                                                qty: qty,
                                                qty_diterima: qty_diterima,
                                                remaining_qty: remaining_qty,
                                                satuan: satuan,
                                                jml_masuk: jml_masuk,
                                                harga: harga,
                                                sub_total: nilai_sub_total,
                                                keterangan: keterangan
                                            });

                                        }
                                        else
                                        {
                                            tag_html += `<tr>`;
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${item.qty_diterima}" data-remaining_qty="${item.remaining_qty}" data-jml_masuk="${item.jml_masuk}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}"  data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += row + 1;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${item.qty_diterima}" data-remaining_qty="${item.remaining_qty}" data-jml_masuk="${item.jml_masuk}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}"  data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += item.kode_barang;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${item.qty_diterima}" data-remaining_qty="${item.remaining_qty}" data-jml_masuk="${item.jml_masuk}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}"  data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += item.nama_barang;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${item.qty_diterima}" data-remaining_qty="${item.remaining_qty}" data-jml_masuk="${item.jml_masuk}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}"  data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += item.satuan;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${item.qty_diterima}" data-remaining_qty="${item.remaining_qty}" data-jml_masuk="${item.jml_masuk}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}"  data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += item.qty;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${item.qty_diterima}" data-remaining_qty="${item.remaining_qty}" data-jml_masuk="${item.jml_masuk}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}"  data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += item.qty_diterima;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${item.qty_diterima}" data-remaining_qty="${item.remaining_qty}" data-jml_masuk="${item.jml_masuk}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}"  data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += item.remaining_qty;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${item.qty_diterima}" data-remaining_qty="${item.remaining_qty}" data-jml_masuk="${item.jml_masuk}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}"  data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += item.jml_masuk;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${item.qty_diterima}" data-remaining_qty="${item.remaining_qty}" data-jml_masuk="${item.jml_masuk}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}"  data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += item.harga;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${item.qty_diterima}" data-remaining_qty="${item.remaining_qty}" data-jml_masuk="${item.jml_masuk}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}"  data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += item.sub_total;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-qty_diterima="${item.qty_diterima}" data-remaining_qty="${item.remaining_qty}" data-jml_masuk="${item.jml_masuk}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}"  data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                            tag_html += item.keterangan;
                                            tag_html += "</td>";
                                            tag_html += "</tr>";

                                            new_list_items.push(item);
                                        }
                                        row = row + 1;

                                        total_jml_order = total_jml_order + qty;
                                        total_jml_masuk = total_jml_masuk + jml_masuk;
                                        total_qty_diterima = total_qty_diterima + qty_diterima;
                                        total_remaining_qty = total_remaining_qty + remaining_qty;
                                        total_jml_harga = total_jml_harga + (harga ? Number(harga.replaceAll(",", "")) : 0);
                                        total_jml_sub_total = total_jml_sub_total + (nilai_sub_total ? Number(nilai_sub_total.replaceAll(",", "")) : 0);
                                    
                                    })

                                    list_items = [];

                                    list_items = new_list_items;

                                    $(".body-detail-table").append(tag_html)

                                    $(".foot-detail-table").empty()
                                    tag_total += `<tr>`;
                                    tag_total += `<td>`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += `GRAND TOTAL`;
                                    tag_total += "</td>";
                                    tag_total += `<td colspan='2'>`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += total_jml_order;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += total_qty_diterima;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += total_remaining_qty;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += total_jml_masuk;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += total_jml_harga.toLocaleString();
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += total_jml_sub_total.toLocaleString();
                                    tag_total += "</td>";
                                    tag_total += `<td colspan="1">`;
                                    tag_total += "</td>";
                                    tag_total += "</tr>";

                                    $(".foot-detail-table").append(tag_total);

                                    $(".detail-modal").modal("hide")
                                }
                            })
                        }
                    }
                    }
                }
        })

        $(".btn-submit-parent-and-close").click(function() {
            $(".detail-modal").modal("hide")
            let validate = false;
            if(list_items.length === 0)
            {
                validate = true;
            }

            // CHECK IF NO BARANG
            if(validate)
            {
                Swal.fire({
                    icon: 'error',
                    title: "Barang Tidak Boleh Kosong",
                    confirmButtonColor: '#4e73df',
                })
            }
            else
            {
                if ($(".create-form").valid()) {
                    Swal.fire({
                        icon: 'question',
                        title: 'Simpan Data?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const csrf = $(`[name="${csrfToken}"]`);
                            setLoading()
                            let data = new FormData(document.querySelector(".create-form"));

                            let update_list_items = [];

                            data.append("multiple_po_id", JSON.stringify($('.multiple_po_id').val()));
                            var arr_no = $('.multiple_po_id').select2('data').map(function(elem){ 
                                return elem.text 
                            });
                            console.log(arr_no)
                            data.append("acceptance_type", ($('.multiple_po_id').val().length > 1) ? "MULTIPLE ORDER" : "SINGLE ORDER")
                            data.append("multiple_po_no", JSON.stringify(arr_no));

                            let id = $(".id").val();
                            // UPDATE
                            if(id)
                            {
                                data.append("status_post", "FINISH");

                                $.ajax({
                                    url: "<?= base_url("penerimaan-barang-import/update"); ?>",
                                    data: data,
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                    },
                                    method: "POST",
                                    dataType: "json",
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        csrf.val(response.token);
                                        if (response.status) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                window.location.href = "<?= base_url("penerimaan-barang-import"); ?>" + "/id/" + id;
                                            })
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            stopLoading()
                                        }
                                    },
                                    onError: function(response) {
                                        csrf.val(response.token);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Data Gagal Diubah, coba Lagi',
                                            confirmButtonColor: '#4e73df',
                                        })
                                        stopLoading()
                                    }
                                });
                            }
                            // CREATE
                            else
                            {
                                let validate_jml_masuk = false;
                                list_items.map(obj => {
                                    update_list_items.push(
                                        {
                                            purchase_order_details_id: obj.purchase_order_details_id ? Number(obj.purchase_order_details_id) : 0,
                                            barang_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                            unit: obj.unit ? Number(obj.unit) : 0,
                                            nama_barang_dok: obj.nama_barang_dokumen,
                                            qty: obj.qty ? Number(obj.qty) : 0,
                                            remaining_qty: (Number(obj.remaining_qty) - Number(obj.jml_masuk)),
                                            qty_diterima: (Number(obj.qty_diterima) + Number(obj.jml_masuk)),
                                            jml_masuk: obj.jml_masuk ? Number(obj.jml_masuk) : 0,
                                            harga: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                            sub_total: obj.sub_total ? Number(obj.sub_total.replaceAll(",", "")) : 0,
                                            keterangan: obj.keterangan,
                                            ppn: 0,
                                            pph: 0
                                        }
                                    )

                                    let jml_masuk = obj.jml_masuk ? Number(obj.jml_masuk) : 0;
                                    if(jml_masuk === 0)
                                    {
                                        validate_jml_masuk = true;
                                    }
                                })

                                data.append("items", JSON.stringify(update_list_items))

                                data.append("status_post", "FINISH");

                                // validasi jumlah masuk kosong
                                if(validate_jml_masuk)
                                {
                                    Swal.fire({
                                        icon: 'error',
                                        title: "Jumlah masuk tidak boleh kosong",
                                        confirmButtonColor: '#4e73df',
                                    })
                                    stopLoading()
                                }
                                else
                                {
                                    $.ajax({
                                        url: "<?= base_url("penerimaan-barang-import/save"); ?>",
                                        data: data,
                                        beforeSend: function(xhr) {
                                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                        },
                                        method: "POST",
                                        dataType: "json",
                                        processData: false,
                                        contentType: false,
                                        success: function(response) {
                                            csrf.val(response.token);
                                            if (response.status) {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: response.message,
                                                    confirmButtonColor: '#4e73df',
                                                })
                                                .then(() => {
                                                    window.location.href = "<?= base_url("penerimaan-barang-import"); ?>" + "/id/" + response.id;
                                                })
                                            } else {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: response.message,
                                                    confirmButtonColor: '#4e73df',
                                                })
                                                stopLoading()
                                            }
                                        },
                                        onError: function(response) {
                                            csrf.val(response.token);
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Data Gagal Disimpan, coba Lagi',
                                                confirmButtonColor: '#4e73df',
                                            })
                                            stopLoading()
                                        }
                                    });
                                }
                            }
                        }
                    })
                }
            }
        })

        $(".btn-submit-parent").click(function() {
            $(".detail-modal").modal("hide")
            let validate = false;
            if(list_items.length === 0)
            {
                validate = true;
            }

            // CHECK IF NO BARANG
            if(validate)
            {
                Swal.fire({
                    icon: 'error',
                    title: "Barang Tidak Boleh Kosong",
                    confirmButtonColor: '#4e73df',
                })
            }
            else
            {
                if ($(".create-form").valid()) {
                    Swal.fire({
                        icon: 'question',
                        title: 'Simpan Data?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const csrf = $(`[name="${csrfToken}"]`);
                            setLoading()
                            let data = new FormData(document.querySelector(".create-form"));

                            let update_list_items = [];

                            data.append("multiple_po_id", JSON.stringify($('.multiple_po_id').val()));
                            var arr_no = $('.multiple_po_id').select2('data').map(function(elem){ 
                                return elem.text 
                            });
                            console.log(arr_no)
                            data.append("acceptance_type", ($('.multiple_po_id').val().length > 1) ? "MULTIPLE ORDER" : "SINGLE ORDER")
                            data.append("multiple_po_no", JSON.stringify(arr_no));

                            let id = $(".id").val();
                            // UPDATE
                            if(id)
                            {

                            }
                            // CREATE
                            else
                            {
                                let validate_jml_masuk = false;
                                list_items.map(obj => {
                                    update_list_items.push(
                                        {
                                            purchase_order_details_id: obj.purchase_order_details_id ? Number(obj.purchase_order_details_id) : 0,
                                            barang_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                            unit: obj.unit ? Number(obj.unit) : 0,
                                            nama_barang_dok: obj.nama_barang_dokumen,
                                            qty: obj.qty ? Number(obj.qty) : 0,
                                            remaining_qty: (Number(obj.remaining_qty) - Number(obj.jml_masuk)),
                                            qty_diterima: (Number(obj.qty_diterima) + Number(obj.jml_masuk)),
                                            jml_masuk: obj.jml_masuk ? Number(obj.jml_masuk) : 0,
                                            harga: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                            sub_total: obj.sub_total ? Number(obj.sub_total.replaceAll(",", "")) : 0,
                                            keterangan: obj.keterangan,
                                            ppn: 0,
                                            pph: 0
                                        }
                                    )

                                    let jml_masuk = obj.jml_masuk ? Number(obj.jml_masuk) : 0;
                                    if(jml_masuk === 0)
                                    {
                                        validate_jml_masuk = true;
                                    }
                                })

                                data.append("items", JSON.stringify(update_list_items))

                                data.append("status_post", "FINISH");

                                // validasi jumlah masuk kosong
                                if(validate_jml_masuk)
                                {
                                    Swal.fire({
                                        icon: 'error',
                                        title: "Jumlah masuk tidak boleh kosong",
                                        confirmButtonColor: '#4e73df',
                                    })
                                    stopLoading()
                                }
                                else
                                {
                                    $.ajax({
                                        url: "<?= base_url("penerimaan-barang-import/save"); ?>",
                                        data: data,
                                        beforeSend: function(xhr) {
                                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                        },
                                        method: "POST",
                                        dataType: "json",
                                        processData: false,
                                        contentType: false,
                                        success: function(response) {
                                            csrf.val(response.token);
                                            if (response.status) {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: response.message,
                                                    confirmButtonColor: '#4e73df',
                                                })
                                                .then(() => {
                                                    window.location.href = "<?= base_url("penerimaan-barang-import"); ?>" + "/id/" + response.id;
                                                })
                                            } else {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: response.message,
                                                    confirmButtonColor: '#4e73df',
                                                })
                                                stopLoading()
                                            }
                                        },
                                        onError: function(response) {
                                            csrf.val(response.token);
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Data Gagal Disimpan, coba Lagi',
                                                confirmButtonColor: '#4e73df',
                                            })
                                            stopLoading()
                                        }
                                    });
                                }
                            }
                        }
                    })
                }
            }
        })

        $(".btn-hide-detail").click(function() {
            $(".detail-modal").modal("hide")
        })

        $(".ppn").keyup(function() {
            if ($(".ppn").val()) {
                if ($(".ppn").val() > 100) {
                    $(".ppn").val(100)
                }
                if ($(".ppn").val() < 0) {
                    $(".ppn").val();
                }
            } else {
                $(".ppn").val();
            }
        })

        $(".pph").keyup(function() {
            if ($(".pph").val()) {
                if ($(".pph").val() > 100) {
                    $(".pph").val(100)
                }
                if ($(".pph").val() < 0) {
                    $(".pph").val();
                }
            } else {
                $(".pph").val();
            }
        })

        $(".harga_barang_jasa").keyup(function() {
            let qty = $(".jml_masuk").val() !== "" ? Number($(".jml_masuk").val()) : 0;
            let harga = $(".harga_barang_jasa").val() !== "" ? Number($(".harga_barang_jasa").val().replaceAll(",", "")) : 0;
            $(".nilai_sub_total").val((qty * harga).toLocaleString())
        })

        $(".jml_masuk").keyup(function() {
            let qty = $(".jml_masuk").val() !== null ? Number($(".jml_masuk").val()) : 0;
            let harga = $(".harga_barang_jasa").val() !== null ? Number($(".harga_barang_jasa").val().replaceAll(",", "")) : 0;
            $(".nilai_sub_total").val((qty * harga).toLocaleString())
        })

        $(".nilai_sub_total").keyup(function() {
            let qty = $(".jml_masuk").val() !== "" ? Number($(".jml_masuk").val()) : 0;
            let harga = $(".nilai_sub_total").val() !== "" ? Number($(".nilai_sub_total").val().replaceAll(",", "")) : 0;
            let result = harga / qty;
            $(".harga_barang_jasa").val(qty ? (parseInt(result).toLocaleString()) : 0)
        })

        $(".multiple_po_id").change(function() {
            total_jml_order = 0;
            total_jml_masuk = 0;
            total_jml_diterima = 0;
            total_remaining_qty = 0;
            total_jml_harga = 0;
            total_jml_sub_total = 0;

            list_items.push((item) => {
                list_delete.push(item);
            })

            row = 0;
            list_items = [];

            let arr = $('.multiple_po_id').val();

            let tag_html = "";
            let tag_total = "";

            $(".body-detail-table").empty();
            $(".foot-detail-table").empty();

            tag_total += `<tr>`;
            tag_total += `<td>`;
            tag_total += "</td>";
            tag_total += `<td>`;
            tag_total += `GRAND TOTAL`;
            tag_total += "</td>";
            tag_total += `<td colspan='2'>`;
            tag_total += "</td>";
            tag_total += `<td>`;
            tag_total += 0;
            tag_total += "</td>";
            tag_total += `<td>`;
            tag_total += 0;
            tag_total += "</td>";
            tag_total += `<td>`;
            tag_total += 0;
            tag_total += "</td>";
            tag_total += `<td>`;
            tag_total += 0;
            tag_total += "</td>";
            tag_total += `<td>`;
            tag_total += 0;
            tag_total += "</td>";
            tag_total += `<td>`;
            tag_total += 0;
            tag_total += "</td>";
            tag_total += `<td colspan="1">`;
            tag_total += "</td>";
            tag_total += "</tr>";

            $(".foot-detail-table").append(tag_total);

            if($(".tipe_bahan").val() === "BAKU")
            {
                arr?.forEach((items) => {
                    $.ajax({
                        url: `<?= base_url("po-import-bahan-baku/multi/dropdown"); ?>`,
                        method: "GET",
                        data: {
                            id: items
                        },
                        dataType: "json",
                        success: function(res) {
                            console.log(res)
                            
                            res.data.forEach(function(item) {
                                let sub_total_sementara = item.price && item.qty ? (Number(item.price) * Number(item.qty)).toLocaleString() : 0;
                                let harga_sementara = item.price ? Number(item.price).toLocaleString() : 0;
                                let qty_sementara = item.qty ? Number(item.qty) : 0;
                                let qty_diterima = item.qty_diterima ? Number(item.qty_diterima) : 0;
                                let remaining_qty = item.remaining_qty ? Number(item.remaining_qty) : 0;

                                // penerimaan not done
                                if(remaining_qty !== 0)
                                {
                                    list_items.push({
                                        id: '',
                                        purchase_order_details_id: item.id,
                                        row: row + 1,
                                        barang_id: item.barang_id,
                                        unit: item.unit,
                                        kode_barang: item.kode_barang,
                                        nama_barang: item.nama_barang,
                                        nama_barang_dokumen: item.nama_barang,
                                        qty: qty_sementara,
                                        qty_diterima: qty_diterima,
                                        remaining_qty: remaining_qty,
                                        satuan: item.nama_satuan,
                                        jml_masuk: 0,
                                        harga: harga_sementara,
                                        sub_total: sub_total_sementara,
                                        keterangan: item.note
                                    })

                                    tag_html += `<tr>`;
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += row + 1;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.kode_barang;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.nama_barang;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.nama_satuan;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += qty_sementara;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += qty_diterima;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += remaining_qty;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += 0;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += harga_sementara;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += sub_total_sementara;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.note;
                                    tag_html += "</td>";
                                    tag_html += "</tr>";

                                    total_jml_order = total_jml_order + qty_sementara;
                                    total_qty_diterima = total_qty_diterima + qty_diterima;
                                    total_remaining_qty = total_remaining_qty + remaining_qty;
                                    total_jml_harga = total_jml_harga + Number(harga_sementara.replaceAll(",", ""));
                                    total_jml_sub_total = total_jml_sub_total + (sub_total_sementara ? Number(sub_total_sementara.replaceAll(",", "")) : 0);


                                    row = row + 1;

                                    $(".body-detail-table").empty();
                                    $(".body-detail-table").append(tag_html)

                                    tag_total = "";
                                    tag_total += `<tr>`;
                                    tag_total += `<td>`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += `GRAND TOTAL`;
                                    tag_total += "</td>";
                                    tag_total += `<td colspan='2'>`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += total_jml_order;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += total_qty_diterima;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += total_remaining_qty;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += total_jml_masuk;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += total_jml_harga.toLocaleString();
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += total_jml_sub_total.toLocaleString();
                                    tag_total += "</td>";
                                    tag_total += `<td colspan="1">`;
                                    tag_total += "</td>";
                                    tag_total += "</tr>";
                                    
                                    $(".foot-detail-table").empty();
                                    $(".foot-detail-table").append(tag_total);
                                }
                            })
                        }
                    })
                })
            }
            if($(".tipe_bahan").val() === "PENOLONG")
            {
                arr?.forEach((items) => {
                    $.ajax({
                        url: `<?= base_url("po-import-bahan-penolong/multi/dropdown"); ?>`,
                        method: "GET",
                        data: {
                            id: items
                        },
                        dataType: "json",
                        success: function(res) {
                            console.log(res)
                            
                            res.data.forEach(function(item) {
                                let sub_total_sementara = item.totalPrice ? item.totalPrice : 0;
                                let harga_sementara = item.price ? item.price : 0;
                                let qty_sementara = item.qty ? Number(item.qty.replaceAll(",", "")) : 0;
                                let qty_diterima = item.qty_diterima ? Number(item.qty_diterima) : 0;
                                let remaining_qty = item.remaining_qty ? Number(item.remaining_qty) : 0;

                                // penerimaan not done
                                if(remaining_qty !== 0)
                                {
                                    list_items.push({
                                        id: '',
                                        purchase_order_details_id: item.id,
                                        row: row + 1,
                                        barang_id: item.barang_id,
                                        unit: item.unit,
                                        kode_barang: item.kode_barang,
                                        nama_barang: item.nama_barang,
                                        nama_barang_dokumen: item.nama_barang,
                                        qty: qty_sementara,
                                        qty_diterima: qty_diterima,
                                        remaining_qty: remaining_qty,
                                        satuan: item.nama_satuan,
                                        jml_masuk: 0,
                                        harga: harga_sementara,
                                        sub_total: sub_total_sementara,
                                        keterangan: item.note
                                    })

                                    tag_html += `<tr>`;
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += row + 1;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.kode_barang;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.nama_barang;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.nama_satuan;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += qty_sementara;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += qty_diterima;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += remaining_qty;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += 0;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += harga_sementara;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += sub_total_sementara;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-qty_diterima="${qty_diterima}" data-remaining_qty="${remaining_qty}" data-jml_masuk="0" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${sub_total_sementara}" data-harga="${harga_sementara}" data-nama_barang_dokumen="${item.nama_barang}" data-qty="${qty_sementara}" data-satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.note;
                                    tag_html += "</td>";
                                    tag_html += "</tr>";

                                    total_jml_order = total_jml_order + qty_sementara;
                                    total_qty_diterima = total_qty_diterima + qty_diterima;
                                    total_remaining_qty = total_remaining_qty + remaining_qty;
                                    total_jml_harga = total_jml_harga + Number(harga_sementara.replaceAll(",", ""));
                                    total_jml_sub_total = total_jml_sub_total + (sub_total_sementara ? Number(sub_total_sementara.replaceAll(",", "")) : 0);


                                    row = row + 1;

                                    $(".body-detail-table").empty();
                                    $(".body-detail-table").append(tag_html)

                                    tag_total = "";
                                    tag_total += `<tr>`;
                                    tag_total += `<td>`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += `GRAND TOTAL`;
                                    tag_total += "</td>";
                                    tag_total += `<td colspan='2'>`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += total_jml_order;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += total_qty_diterima;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += total_remaining_qty;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += total_jml_masuk;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += total_jml_harga.toLocaleString();
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += total_jml_sub_total.toLocaleString();
                                    tag_total += "</td>";
                                    tag_total += `<td colspan="1">`;
                                    tag_total += "</td>";
                                    tag_total += "</tr>";
                                    
                                    $(".foot-detail-table").empty();
                                    $(".foot-detail-table").append(tag_total);
                                }
                            })
                        }
                    })
                })
            }
        })
        
        $(".supplier_id").change(function() {
            let tag_total = "";

            $(".body-detail-table").empty();
            $(".foot-detail-table").empty();

            tag_total += `<tr>`;
            tag_total += `<td>`;
            tag_total += "</td>";
            tag_total += `<td>`;
            tag_total += `GRAND TOTAL`;
            tag_total += "</td>";
            tag_total += `<td colspan='2'>`;
            tag_total += "</td>";
            tag_total += `<td>`;
            tag_total += total_jml_order;
            tag_total += "</td>";
            tag_total += `<td>`;
            tag_total += 0;
            tag_total += "</td>";
            tag_total += `<td>`;
            tag_total += 0;
            tag_total += "</td>";
            tag_total += `<td>`;
            tag_total += 0;
            tag_total += "</td>";
            tag_total += `<td>`;
            tag_total += 0;
            tag_total += "</td>";
            tag_total += `<td>`;
            tag_total += 0;
            tag_total += "</td>";
            tag_total += `<td colspan="1">`;
            tag_total += "</td>";
            tag_total += "</tr>";

            $(".foot-detail-table").append(tag_total);

            if($(".supplier_id option:selected").val())
            {
                if($(".tipe_bahan").val() === "BAKU")
                {
                    $.ajax({
                        url: `<?= base_url("po-import-bahan-baku/dropdown"); ?>`,
                        method: "GET",
                        data: {
                            id: $(".supplier_id option:selected").val()
                        },
                        dataType: "json",
                        success: function(res) {
                            console.log(res)
                            $(".multiple_po_id").attr("disabled", true)
                            $(".multiple_po_id").empty()
                            $(".multiple_po_id").append(`<option value=""></option>`)
                            res.data.forEach(function(item) {
                                $(".multiple_po_id").append(`<option value="${item.id}">${item.po_no}</option>`)
                            })
                            $(".multiple_po_id").attr("disabled", false)
                            $(".multiple_po_id").val([]);
                        }
                    })
                }
                if($(".tipe_bahan").val() === "PENOLONG")
                {
                    $.ajax({
                        url: `<?= base_url("po-import-bahan-penolong/dropdown"); ?>`,
                        method: "GET",
                        data: {
                            id: $(".supplier_id option:selected").val()
                        },
                        dataType: "json",
                        success: function(res) {
                            console.log(res)
                            $(".multiple_po_id").attr("disabled", true)
                            $(".multiple_po_id").empty()
                            $(".multiple_po_id").append(`<option value=""></option>`)
                            res.data.forEach(function(item) {
                                $(".multiple_po_id").append(`<option value="${item.id}">${item.po_no}</option>`)
                            })
                            $(".multiple_po_id").attr("disabled", false)
                            $(".multiple_po_id").val([]);
                        }
                    })
                }
            }
            else
            {
                $(".multiple_po_id").attr("disabled", true)
                $(".multiple_po_id").empty()
                $(".multiple_po_id").append(`<option value=""></option>`)
                $(".multiple_po_id").val([]);
            }
        })
    })

    const changeStatus = function()
    {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if(value)
        {
            $(".no_penerimaan_barang").attr("readonly", true);
            $(".no_penerimaan_barang").val("AUTO GENERATE");
        }
        else
        {
            $(".no_penerimaan_barang").attr("readonly", false);
            $(".no_penerimaan_barang").val("");
        }
    }

    $(document).on('click', '.edit-table-detail', function(evt) {
        $(".title-detail-name").text("Update")
        // $(".delete-detail").css('display', '');
        let sub_total = $(this).data('sub_total')
        let harga = $(this).data('harga')
        let qty = $(this).data('qty')
        let nama_barang_dokumen = $(this).data('nama_barang_dokumen')
        let satuan = $(this).data('satuan')
        let nama_barang = $(this).data('nama_barang')
        let kode = $(this).data('kode')
        let keterangan = $(this).data('keterangan')
        let purchase_order_details_id = $(this).data('purchase_order_details_id')
        let unit = $(this).data('unit')
        let rowid = $(this).data('row')
        let id = $(this).data('id')
        let barang_id = $(this).data('barang_id')
        let jml_masuk = $(this).data('jml_masuk')
        let qty_diterima = $(this).data('qty_diterima')
        let remaining_qty = $(this).data('remaining_qty')

        validator_detail.resetForm();
        validator_detail.reset();

        $(".id_detail").val(rowid)
        $(".kode").val(kode)
        $(".unit").val(unit)

        $(".jml_masuk").val(jml_masuk)
        $(".nilai_sub_total").val(sub_total)
        $(".harga_barang_jasa").val(harga)
        $(".keterangan").val(keterangan)
        $(".barang_id").val(barang_id)
        $(".qty").val(qty)
        $(".qty_diterima").val(qty_diterima)
        $(".remaining_qty").val(remaining_qty)
        $(".nama_barang_dokumen").val(nama_barang_dokumen)
        $(".satuan_order").val(satuan)
        $(".nama_barang").val(nama_barang)
        $(".purchase_order_details_id").val(purchase_order_details_id)
        $(".kode_barang").val(kode)

        $(".detail-modal").modal("show");
    })

    
    const changeTipeBahan = function()
    {
        total_jml_order = 0;
        total_jml_masuk = 0;
        total_qty_diterima = 0;
        total_remaining_qty = 0;
        total_jml_harga = 0;
        total_jml_sub_total = 0;

        list_items.map(item => {
            list_delete.push(item)
        })

        row = 0;
        list_items = [];
        $(".body-detail-table").empty();

        let tag_total = "";
        $(".foot-detail-table").empty()
        tag_total += `<tr>`;
        tag_total += `<td>`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `GRAND TOTAL`;
        tag_total += "</td>";
        tag_total += `<td colspan='2'>`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += 0;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += 0;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += 0;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += 0;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += 0;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += 0;
        tag_total += "</td>";
        tag_total += `<td colspan="1">`;
        tag_total += "</td>";
        tag_total += "</tr>";

        $(".foot-detail-table").append(tag_total);

        $(".supplier_id").attr("disabled", "true");

        if($(".tipe_bahan").val() === "BAKU")
        {
            $.ajax({
                url: `<?= base_url("supplier-bahan-baku-import/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".supplier_id").empty();

                    $(".supplier_id").append(`<option value=""></option>`);

                    res.data.forEach(function(item) {
                        $(".supplier_id").append(`<option value="${item.id}">${item.name}</option>`);
                    })
                    $(".supplier_id").removeAttr("disabled");

                    $(".supplier_id").val("").change();
                }
            })
        }
        if($(".tipe_bahan").val() === "PENOLONG")
        {
            $.ajax({
                url: `<?= base_url("supplier-bahan-penolong-import/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".supplier_id").empty();

                    $(".supplier_id").append(`<option value=""></option>`);

                    res.data.forEach(function(item) {
                        $(".supplier_id").append(`<option value="${item.id}">${item.name}</option>`);
                    })
                    $(".supplier_id").removeAttr("disabled");

                    $(".supplier_id").val("").change();
                }
            })
        }
    }

    const print = function(url) 
    {
        window.open(url, "_blank");
    }
</script>

<?= $this->endSection(); ?>