<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1 class="title-name">Tambah</h1>
    <div class="col-button-tambah-spp">
        <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("penerimaan-barang-lokal"); ?>">
            Batal
        </a>
        <?php if(!empty($dataPenerimaanBarang)){ ?> 

        <?php if($dataPenerimaanBarang->status_post === "WAITING"){ ?> 
            <button class="btn btn-hapus delete-parent float-right">
                Hapus
            </button>
            <?php } ?> 

            <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("penerimaan-barang-lokal/print/"); ?><?= $dataPenerimaanBarang->id; ?>')">
                Print
            </button>

            <?php if($dataPenerimaanBarang->status_post === "WAITING"){ ?> 

            <!-- <button class="btn btn-success posting-penerimaan">
                Posting
            </button> -->

            <?php } 
            }
            ?> 
            <?php if(!empty($dataPenerimaanBarang)){ 
                if($dataPenerimaanBarang->status_post === "WAITING"){ 
            ?> 
            <button class="btn btn-show-form btn-save float-right btn-submit-parent-and-close">
                Simpan & Close PO
            </button>
            <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                Simpan
            </button>
            <?php }
            } else { ?> 
            <button class="btn btn-show-form btn-save float-right btn-submit-parent-and-close">
                Simpan & Close PO
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
                                <input <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang->no_penerimaan_barang : ""; ?>" type="text" class="form-control no_penerimaan_barang" id="no_penerimaan_barang" name="no_penerimaan_barang" placeholder="No. Penerimaan">
                                <label for="floatingInput">No. Penerimaan</label>
                            </div>
                            <div style="<?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->status_post === "FINISH" ? "display: none" : "") : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                <input style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> onchange="changeTipeBahan()" class="form-select tipe_bahan" id="tipe_bahan" name="tipe_bahan" aria-label="Floating label select example">
                            <option value="BAKU" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->tipe_bahan === "BAKU" ? "selected" : "") : ""; ?>>Bahan Baku</option>
                            <option value="PENOLONG" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->tipe_bahan === "PENOLONG" ? "selected" : "") : ""; ?>>Bahan Penolong</option>
                        </select>
                        <label for="floatingInput">Tipe</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> class="form-select supplier_id" id="supplier_id" name="supplier_id" aria-label="Floating label select example">
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
                            if($dataPenerimaanBarang->status_post === "FINISH"){
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
                        <?php } else { ?>
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
                        <?php } ?>
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
            <div class="row mb-1">
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">Data Dokumen</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> class="form-select aju_document_type" id="aju_document_type" name="aju_document_type" aria-label="Floating label select example">
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
                        <input <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang->aju_no : ""; ?>" type="text" class="form-control aju_no" name="aju_no" id="aju_no" placeholder="No. Invoice">
                        <label for="floatingInput">No. AJU</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->validation_date ? date("d/m/Y", strtotime($dataPenerimaanBarang->validation_date)) : "") : ""; ?>" type="text" class="form-control input-picker validation_date" id="validation_date" name="validation_date" placeholder="Tanggal Pendaftaran">
                            <label for="floatingInput">Tanggal Pendaftaran</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang->no_registration : ""; ?>" type="text" class="form-control no_registration" id="no_registration" name="no_registration" placeholder="No. Pendaftaran">
                            <label for="floatingInput">No. Pendaftaran</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-1">
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">No. Surat Jalan</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang->letter_no : ""; ?>" type="text" class="form-control letter_no" id="letter_no" name="letter_no" placeholder="No. Surat Jalan">
                        <label for="floatingInput">No. Surat Jalan</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang->invoice_no : ""; ?>" type="text" class="form-control invoice_no" id="invoice_no" name="invoice_no" placeholder="No. Invoice">
                        <label for="floatingInput">No. Invoice</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang->packaging : ""; ?>" type="text" class="form-control packaging" id="packaging" name="packaging" placeholder="Kemasan">
                        <label for="floatingInput">Kemasan</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? $dataPenerimaanBarang->total_weight : ""; ?>" type="number" class="form-control total_weight" id="total_weight" name="total_weight" placeholder="Berat">
                        <label for="floatingInput">Berat</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? number_format($dataPenerimaanBarang->shipping_cost) : ""; ?>" onkeyup="formatNumber(this)" type="text" class="form-control shipping_cost" name="shipping_cost" id="shipping_cost" placeholder="Biaya Ongkos Kirim">
                        <label for="floatingInput">Biaya Ongkos Kirim</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? number_format($dataPenerimaanBarang->biaya_masuk) : ""; ?>" onkeyup="formatNumber(this)" type="text" class="form-control biaya_masuk" name="biaya_masuk" id="biaya_masuk" placeholder="Biaya Masuk">
                        <label for="floatingInput">Biaya Masuk</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPenerimaanBarang) ? number_format($dataPenerimaanBarang->ppnbm) : ""; ?>" onkeyup="formatNumber(this)" type="text" class="form-control ppnbm" id="ppnbm" name="ppnbm" placeholder="PPNBM">
                        <label for="floatingInput">PPNBM</label>
                    </div>
                </div>
            </div>
        </form>
        <div class="col-subtitle-modal">
            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="form-label font-weight-bold modal-sub-title">List Barang</label>
                </div>
                <div class="col-md-6">
                <?php if(!empty($dataPenerimaanBarang)){ 
                    if($dataPenerimaanBarang->status_post === "WAITING"){ ?> 
                <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal">
                    <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                </button>
                <?php }
                } else { ?> 
                <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal">
                    <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                </button>
                <?php } ?> 
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
                            <th>Jml. Masuk</th>
                            <th>Selisih</th>
                            <th>Harga</th>
                            <th>Sub Total</th>
                            <th>Keterangan</th>
                            <?php if(!empty($dataPenerimaanBarang)){ 
                            if($dataPenerimaanBarang->status_post === "WAITING"){ ?> 
                            <th>Hapus</th>
                            <?php }
                            if($dataPenerimaanBarang->status_post === "FINISH"){ ?> 
                            <th>Aktual Penerimaan</th>
                            <?php }
                            } else { ?> 
                            <th>Hapus</th>
                            <?php } ?> 
                        </tr>
                    </thead>
                    <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">
                    <?php 
                        $no = 1;
                        $total_jml_order = 0;
                        $total_jml_masuk = 0;
                        $total_selisih = 0;
                        $total_harga = 0;
                        $total_sub_total = 0;

                        if(!empty($dataPenerimaanBarang) && !empty($dataPenerimaanBarangDetail)){ 
                            foreach($dataPenerimaanBarangDetail as $details){  
                                $total_jml_order = $total_jml_order + $details["qty"];
                                $total_jml_masuk = $total_jml_masuk + $details["jml_masuk"];
                                $total_selisih = $total_selisih + $details["selisih"];
                                $total_harga = $total_harga + ($details["harga"] ? (int)$details["harga"] : 0);
                                $total_sub_total = $total_sub_total + ($details["sub_total"] ? (int)$details["sub_total"] : 0);
                        ?> 
    
                            <tr>
                                <?php if($dataPenerimaanBarang->status_post === "WAITING"){ ?> 
                                    <td class="edit-table-detail" data-barang_id="<?= $details["barang_id"]; ?>" data-unit="<?= $details["unit"]; ?>" data-keterangan="<?=  $details["keterangan"]; ?>" data-ppn="<?=  $details["id_ppn"]; ?>" data-pph="<?=  $details["id_pph"]; ?>" data-sub_total="<?=  $details["sub_total"] ?  number_format($details["sub_total"]) : 0; ?>" data-harga="<?=  $details["harga"] ?  number_format($details["harga"]) : 0; ?>" data-nama_barang_dokumen="<?=  $details["nama_barang_dok"]; ?>" data-qty="<?=  $details["qty"]; ?>" data-satuan="<?=  $details["nama_satuan"]; ?>" data-nama_barang="<?=  $details["nama_barang"]; ?>" data-kode="<?=  $details["kode_barang"]; ?>" data-purchase_order_details_id="<?=  $details["purchase_order_details_id"]; ?>" data-id="<?=  $details["id"]; ?>" data-row="<?= $no; ?>"><?= $no; ?></td>
                                    <td class="edit-table-detail" data-barang_id="<?= $details["barang_id"]; ?>" data-unit="<?= $details["unit"]; ?>" data-keterangan="<?=  $details["keterangan"]; ?>" data-ppn="<?=  $details["id_ppn"]; ?>" data-pph="<?=  $details["id_pph"]; ?>" data-sub_total="<?=  $details["sub_total"] ?  number_format($details["sub_total"]) : 0; ?>" data-harga="<?=  $details["harga"] ?  number_format($details["harga"]) : 0; ?>" data-nama_barang_dokumen="<?=  $details["nama_barang_dok"]; ?>" data-qty="<?=  $details["qty"]; ?>" data-satuan="<?=  $details["nama_satuan"]; ?>" data-nama_barang="<?=  $details["nama_barang"]; ?>" data-kode="<?=  $details["kode_barang"]; ?>" data-purchase_order_details_id="<?=  $details["purchase_order_details_id"]; ?>" data-id="<?=  $details["id"]; ?>" data-row="<?= $no; ?>"><?=  $details["kode_barang"]; ?></td>
                                    <td class="edit-table-detail" data-barang_id="<?= $details["barang_id"]; ?>" data-unit="<?= $details["unit"]; ?>" data-keterangan="<?=  $details["keterangan"]; ?>" data-ppn="<?=  $details["id_ppn"]; ?>" data-pph="<?=  $details["id_pph"]; ?>" data-sub_total="<?=  $details["sub_total"] ?  number_format($details["sub_total"]) : 0; ?>" data-harga="<?=  $details["harga"] ?  number_format($details["harga"]) : 0; ?>" data-nama_barang_dokumen="<?=  $details["nama_barang_dok"]; ?>" data-qty="<?=  $details["qty"]; ?>" data-satuan="<?=  $details["nama_satuan"]; ?>" data-nama_barang="<?=  $details["nama_barang"]; ?>" data-kode="<?=  $details["kode_barang"]; ?>" data-purchase_order_details_id="<?=  $details["purchase_order_details_id"]; ?>" data-id="<?=  $details["id"]; ?>" data-row="<?= $no; ?>"><?=  $details["nama_barang"]; ?></td>
                                    <td class="edit-table-detail" data-barang_id="<?= $details["barang_id"]; ?>" data-unit="<?= $details["unit"]; ?>" data-keterangan="<?=  $details["keterangan"]; ?>" data-ppn="<?=  $details["id_ppn"]; ?>" data-pph="<?=  $details["id_pph"]; ?>" data-sub_total="<?=  $details["sub_total"] ?  number_format($details["sub_total"]) : 0; ?>" data-harga="<?=  $details["harga"] ?  number_format($details["harga"]) : 0; ?>" data-nama_barang_dokumen="<?=  $details["nama_barang_dok"]; ?>" data-qty="<?=  $details["qty"]; ?>" data-satuan="<?=  $details["nama_satuan"]; ?>" data-nama_barang="<?=  $details["nama_barang"]; ?>" data-kode="<?=  $details["kode_barang"]; ?>" data-purchase_order_details_id="<?=  $details["purchase_order_details_id"]; ?>" data-id="<?=  $details["id"]; ?>" data-row="<?= $no; ?>"><?=  $details["nama_satuan"]; ?></td>
                                    <td class="edit-table-detail" data-barang_id="<?= $details["barang_id"]; ?>" data-unit="<?= $details["unit"]; ?>" data-keterangan="<?=  $details["keterangan"]; ?>" data-ppn="<?=  $details["id_ppn"]; ?>" data-pph="<?=  $details["id_pph"]; ?>" data-sub_total="<?=  $details["sub_total"] ?  number_format($details["sub_total"]) : 0; ?>" data-harga="<?=  $details["harga"] ?  number_format($details["harga"]) : 0; ?>" data-nama_barang_dokumen="<?=  $details["nama_barang_dok"]; ?>" data-qty="<?=  $details["qty"]; ?>" data-satuan="<?=  $details["nama_satuan"]; ?>" data-nama_barang="<?=  $details["nama_barang"]; ?>" data-kode="<?=  $details["kode_barang"]; ?>" data-purchase_order_details_id="<?=  $details["purchase_order_details_id"]; ?>" data-id="<?=  $details["id"]; ?>" data-row="<?= $no; ?>"><?=  $details["qty"]; ?></td>
                                    <td class="edit-table-detail" data-barang_id="<?= $details["barang_id"]; ?>" data-unit="<?= $details["unit"]; ?>" data-keterangan="<?=  $details["keterangan"]; ?>" data-ppn="<?=  $details["id_ppn"]; ?>" data-pph="<?=  $details["id_pph"]; ?>" data-sub_total="<?=  $details["sub_total"] ?  number_format($details["sub_total"]) : 0; ?>" data-harga="<?=  $details["harga"] ?  number_format($details["harga"]) : 0; ?>" data-nama_barang_dokumen="<?=  $details["nama_barang_dok"]; ?>" data-qty="<?=  $details["qty"]; ?>" data-satuan="<?=  $details["nama_satuan"]; ?>" data-nama_barang="<?=  $details["nama_barang"]; ?>" data-kode="<?=  $details["kode_barang"]; ?>" data-purchase_order_details_id="<?=  $details["purchase_order_details_id"]; ?>" data-id="<?=  $details["id"]; ?>" data-row="<?= $no; ?>"><?= $details["jml_masuk"]; ?></td>
                                    <td class="edit-table-detail" data-barang_id="<?= $details["barang_id"]; ?>" data-unit="<?= $details["unit"]; ?>" data-keterangan="<?=  $details["keterangan"]; ?>" data-ppn="<?=  $details["id_ppn"]; ?>" data-pph="<?=  $details["id_pph"]; ?>" data-sub_total="<?=  $details["sub_total"] ?  number_format($details["sub_total"]) : 0; ?>" data-harga="<?=  $details["harga"] ?  number_format($details["harga"]) : 0; ?>" data-nama_barang_dokumen="<?=  $details["nama_barang_dok"]; ?>" data-qty="<?=  $details["qty"]; ?>" data-satuan="<?=  $details["nama_satuan"]; ?>" data-nama_barang="<?=  $details["nama_barang"]; ?>" data-kode="<?=  $details["kode_barang"]; ?>" data-purchase_order_details_id="<?=  $details["purchase_order_details_id"]; ?>" data-id="<?=  $details["id"]; ?>" data-row="<?= $no; ?>"><?= $details["selisih"]; ?></td>
                                    <td class="edit-table-detail" data-barang_id="<?= $details["barang_id"]; ?>" data-unit="<?= $details["unit"]; ?>" data-keterangan="<?=  $details["keterangan"]; ?>" data-ppn="<?=  $details["id_ppn"]; ?>" data-pph="<?=  $details["id_pph"]; ?>" data-sub_total="<?=  $details["sub_total"] ?  number_format($details["sub_total"]) : 0; ?>" data-harga="<?=  $details["harga"] ?  number_format($details["harga"]) : 0; ?>" data-nama_barang_dokumen="<?=  $details["nama_barang_dok"]; ?>" data-qty="<?=  $details["qty"]; ?>" data-satuan="<?=  $details["nama_satuan"]; ?>" data-nama_barang="<?=  $details["nama_barang"]; ?>" data-kode="<?=  $details["kode_barang"]; ?>" data-purchase_order_details_id="<?=  $details["purchase_order_details_id"]; ?>" data-id="<?=  $details["id"]; ?>" data-row="<?= $no; ?>"><?=  $details["harga"] ?  number_format($details["harga"]) : 0; ?></td>
                                    <td class="edit-table-detail" data-barang_id="<?= $details["barang_id"]; ?>" data-unit="<?= $details["unit"]; ?>" data-keterangan="<?=  $details["keterangan"]; ?>" data-ppn="<?=  $details["id_ppn"]; ?>" data-pph="<?=  $details["id_pph"]; ?>" data-sub_total="<?=  $details["sub_total"] ?  number_format($details["sub_total"]) : 0; ?>" data-harga="<?=  $details["harga"] ?  number_format($details["harga"]) : 0; ?>" data-nama_barang_dokumen="<?=  $details["nama_barang_dok"]; ?>" data-qty="<?=  $details["qty"]; ?>" data-satuan="<?=  $details["nama_satuan"]; ?>" data-nama_barang="<?=  $details["nama_barang"]; ?>" data-kode="<?=  $details["kode_barang"]; ?>" data-purchase_order_details_id="<?=  $details["purchase_order_details_id"]; ?>" data-id="<?=  $details["id"]; ?>" data-row="<?= $no; ?>"><?=  $details["sub_total"] ?  number_format($details["sub_total"]) : 0; ?></td>
                                    <td class="edit-table-detail" data-barang_id="<?= $details["barang_id"]; ?>" data-unit="<?= $details["unit"]; ?>" data-keterangan="<?=  $details["keterangan"]; ?>" data-ppn="<?=  $details["id_ppn"]; ?>" data-pph="<?=  $details["id_pph"]; ?>" data-sub_total="<?=  $details["sub_total"] ?  number_format($details["sub_total"]) : 0; ?>" data-harga="<?=  $details["harga"] ?  number_format($details["harga"]) : 0; ?>" data-nama_barang_dokumen="<?=  $details["nama_barang_dok"]; ?>" data-qty="<?=  $details["qty"]; ?>" data-satuan="<?=  $details["nama_satuan"]; ?>" data-nama_barang="<?=  $details["nama_barang"]; ?>" data-kode="<?=  $details["kode_barang"]; ?>" data-purchase_order_details_id="<?=  $details["purchase_order_details_id"]; ?>" data-id="<?=  $details["id"]; ?>" data-row="<?= $no; ?>"><?=  $details["keterangan"]; ?></td>
                                    <td><button class="btn-trash" onclick='deleteRow("<?= $no; ?>")'>X</button></td>
                                <?php } else { ?>
                                    <td><?= $no; ?></td>
                                    <td><?= $details["kode_barang"]; ?></td>
                                    <td><?= $details["nama_barang"]; ?></td>
                                    <td><?= $details["nama_satuan"]; ?></td>
                                    <td><?= $details["qty"]; ?></td>
                                    <td><?= $details["jml_masuk"]; ?></td>
                                    <td><?= $details["selisih"]; ?></td>
                                    <td><?= $details["harga"] ? number_format($details["harga"]) : 0; ?></td>
                                    <td><?= $details["sub_total"] ? number_format($details["sub_total"]) : 0; ?></td>
                                    <td><?= $details["keterangan"]; ?></td>
                                    <td><button class="btn-view" onclick='view("<?= $no; ?>")'>View</button></td>
                                <?php } ?>
                            
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
                            <td><b><?= $total_jml_masuk; ?></b></td>
                            <td><b><?= $total_selisih; ?></b></td>
                            <td><b><?= number_format($total_harga); ?></b></td>
                            <td><b><?= number_format($total_sub_total); ?></b></td>
                            <td colspan="2"></td>
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
                                <select class="form-select kode_barang" name="kode_barang" id="kode_barang" aria-label="Floating label select example">
                                    <option data-barang_id= "" data-nama="" data-satuan="" data-unit="" data-stok="" data-harga="" value=""></option>
                                </select>
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
                                <input onkeyup="formatNumber(this)" type="text" class="form-control harga_barang_jasa" name="harga_barang_jasa" id="harga_barang_jasa" placeholder="Harga barang/jasa">
                                <label for="floatingInput">Harga barang/jasa</label>
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
                    <div class="row">
                        <div class="col mb-3">
                            <h5 class="title-tambah-barang">Data Tax</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select ppn" name="ppn" id="ppn" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">PPN</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select pph" name="pph" id="pph" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">PPH</label>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="col-subtitle-modal">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h5 class="modal-sub-title">Aktual Penerimaan</h5>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-add-row btn-add btn-block float-right" style="width: 106px;">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive mt-2">
                    <table class="table-inside nowrap table-hover-tobasurimi table-add-modal-master-barang" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Warehouse</th>
                                <th>Satuan</th>
                                <th>Qty</th>
                                <th>Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-warehouse" id="body-detail-warehouse" style="cursor: pointer;">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-hide-detail btn-discard mr-2">Batal</button>
                    <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
                    <button type="button" class="btn btn-discard delete-detail delete-btn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<div class="modal view-modal" tabindex="1">
    <div class="modal-dialog" style="max-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Aktual Penerimaan Barang</h5>
            </div>
            <div class="modal-body">
                <div class="table-responsive mt-2">
                    <table class="table-inside nowrap table-hover-tobasurimi table-add-modal-master-barang" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Warehouse</th>
                                <th>Satuan</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody class="body-view-warehouse" id="body-view-warehouse" style="cursor: pointer;">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-discard hide-view-detail">Batal</button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let list_items = [];
    let list_delete = [];
    let list_warehouse = [];
    var row = 0;
    var row_detail = 0;
    let trigger = true;
    let total_jml_order = 0;
    let total_jml_masuk = 0;
    let total_jml_selisih = 0;
    let total_jml_harga = 0;
    let total_jml_sub_total = 0;
    var priceEdit = 0;
    var sub_totalEdit = 0;
    let data_satuan = [];
    let data_warehouse = [];

    <?php if(!empty($dataPenerimaanBarangDetail)){ 
        foreach($dataPenerimaanBarangDetail as $details){  
    ?>

    priceEdit = Number('<?= $details["harga"]; ?>');
    sub_totalEdit = Number('<?= $details["sub_total"]; ?>');
    row = row + 1;

    total_jml_order = total_jml_order + Number(<?= $details["qty"]; ?>);
    total_jml_masuk = total_jml_masuk + Number(<?= $details["jml_masuk"]; ?>);
    total_jml_selisih = total_jml_selisih + Number(<?= $details["selisih"]; ?>);
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
        selisih: Number(<?= $details["selisih"]; ?>),
        satuan: '<?= $details["nama_satuan"]; ?>',
        jml_masuk: Number(<?= $details["jml_masuk"]; ?>),
        harga: Number('<?= $details["harga"] ? $details["harga"] : 0; ?>').toLocaleString(),
        sub_total: Number('<?= $details["sub_total"] ? $details["sub_total"] : 0; ?>').toLocaleString(),
        keterangan: '<?= $details["keterangan"]; ?>',
        ppn: Number(<?= $details["id_ppn"] ? $details["id_ppn"] : 0; ?>),
        nilai_ppn: '<?= $details["ppn"]; ?>',
        pph: Number(<?= $details["id_pph"] ? $details["id_pph"] : 0; ?>),
        nilai_pph: '<?= $details["pph"]; ?>',
        warehouse: <?= $details["warehouse"]; ?>,
    })
    <?php 
        }
    ?>
    <?php
    } ?>

    console.log(list_items)

    var validator_detail = $(".detail-form").validate({
        rules: {
            kode_barang: {
                required: true
            },
            qty: {
                required: true
            },
            selisih: {
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
            qty: {
                required: "Qty wajib diisi"
            },
            selisih: {
                required: "Selisih wajib diisi"
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
                letter_no: {
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
                shipping_cost: {
                    required: true,
                },
                biaya_masuk: {
                    required: true,
                },
                ppnbm: {
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
                letter_no: {
                    required: "No. Surat wajib diisi"
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
                shipping_cost: {
                    required: "Biaya Pengiriman wajib diisi"
                },
                biaya_masuk: {
                    required: "Biaya Masuk wajib diisi"
                },
                ppnbm: {
                    required: "PPNBM wajib diisi"
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

        $('.aju_document_type')
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

        // KODE BARANG
        $('.kode_barang').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content"),
            allowClear: true
        })

        //CSS SELECT2 FLOATING LABEL
        $('.kode_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.kode_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.kode_barang')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // PPN
        $('.ppn').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content"),
            allowClear: true
        })

        //CSS SELECT2 FLOATING LABEL
        $('.ppn')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.ppn')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.ppn')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // PPH
        $('.pph').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content"),
            allowClear: true
        })

        //CSS SELECT2 FLOATING LABEL
        $('.pph')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.pph')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.pph')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // $.ajax({
        //     url: `<?= base_url("warehouse/dropdown"); ?>`,
        //     method: "GET",
        //     dataType: "json",
        //     success: function(res) {
        //         data_warehouse = res?.data;
        //     }
        // })
        data_warehouse = <?= json_encode($dataWarehouse); ?>;
        data_satuan = <?= json_encode($dataSatuan); ?>;

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
                        url: "<?= base_url("penerimaan-barang-lokal/delete"); ?>",
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
                                        window.location.href = "<?= base_url("penerimaan-barang-lokal"); ?>"
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
            let ppn = $(".ppn option:selected").val()
            let pph = $(".pph option:selected").val()
            let unit = $(".unit").val() ? Number($(".unit").val()) : 0
            let nilai_ppn = $(".ppn option:selected").text()
            let nilai_pph = $(".pph option:selected").text()
            let nilai_sub_total = $(".nilai_sub_total").val() ? $(".nilai_sub_total").val() : 0
            let purchase_order_details_id = $(".purchase_order_details_id").val()
            let harga = $(".harga_barang_jasa").val()
            var jml_masuk = 0;
            var selisih = 0;
            var all_qty = 0;
            let validate_required = false;
            var total_masuk_sementara = 0;

            let new_list_warehouse = []
            list_warehouse.forEach((item) => {
                if(item.display !== "none")
                {
                    all_qty = all_qty + ($(".qty_warehouse_" + item.row).val() ? Number($(".qty_warehouse_" + item.row).val()) : 0) 
                    jml_masuk = jml_masuk + Number($(".qty_warehouse_" + item.row).val() ? $(".qty_warehouse_" + item.row).val() : 0)
                    new_list_warehouse.push(
                        {
                            warehouse_name: $(".warehouse_id_" + item.row + " option:selected").text(),
                            warehouse_id: $(".warehouse_id_" + item.row + " option:selected").val() ? Number($(".warehouse_id_" + item.row + " option:selected").val()): 0,
                            qty: $(".qty_warehouse_" + item.row).val(),
                            satuan: $(".satuan_" + item.row + " option:selected").val() ? Number($(".satuan_" + item.row + " option:selected").val()) : 0,
                            satuan_name: $(".satuan_" + item.row + " option:selected").text()
                        }
                    )

                    if($(".warehouse_id_" + item.row + " option:selected").val() === "" || $(".qty_warehouse_" + item.row).val() === "")
                    {
                        validate_required = true;
                    }
                    if($(".satuan_" + item.row + " option:selected").val() === "")
                    {
                        validate_required = true;
                    }

                    total_masuk_sementara = total_masuk_sementara + ($(".qty_warehouse_" + item.row).val() ? Number($(".qty_warehouse_" + item.row).val()) : 0);
                }
            })

            if(list_warehouse.length === 0)
            {
                Swal.fire({
                    icon: 'error',
                    title: "Aktual Penerimaan Tidak Boleh Kosong",
                    confirmButtonColor: '#4e73df',
                })
            }
            else
            {
                let validate_same = false;
                let validate_jml_masuk = false;

                // list_items.map(item => {
                //     if(barang_id !== '')
                //     {
                //         if(item.barang_id == barang_id)
                //         {
                //             // kalau edit barang, barang tidak ganti tidak kena validasi
                //             if(row_detail === item.row)
                //             {
                //                 validate_same = false;
                //             }
                //             else
                //             {
                //                 validate_same = true;
                //             }
                //         }
                //     }
                // })

                if(total_masuk_sementara > qty)
                {
                    validate_jml_masuk = true;
                }

                // if(validate_same)
                // {
                //     Swal.fire({
                //         icon: 'error',
                //         title: "Barang Sudah Ada",
                //         confirmButtonColor: '#4e73df',
                //     })
                // }
                if(validate_jml_masuk)
                {
                    Swal.fire({
                        icon: 'error',
                        title: "Qty sudah melebihi jumlah dokumen",
                        confirmButtonColor: '#4e73df',
                    })
                }
                else
                {
                    if(validate_required)
                    {
                        Swal.fire({
                            icon: 'error',
                            title: "Warehouse, Qty, Satuan Wajib Diisi",
                            confirmButtonColor: '#4e73df',
                        })
                    }
                    else
                    {
                        // if(all_qty > doc_qty)
                        // {
                        //     Swal.fire({
                        //         icon: 'error',
                        //         title: "Qty Aktual Penerimaan Tidak Bisa Lebih Besar Dari Dokumen Qty",
                        //         confirmButtonColor: '#4e73df',
                        //     })
                        // }
                        // else
                        // {
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
                                            total_jml_selisih = 0;
                                            total_jml_masuk = 0;
                                            total_jml_harga = 0;
                                            total_jml_sub_total = 0;

                                            $(".body-detail-table").empty()

                                            list_items.map(item => {
                                                if(item.row == row_detail)
                                                {
                                                    tag_html += `<tr>`;
                                                    tag_html += `<td class="edit-table-detail" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}" data-ppn="${ppn}" data-pph="${pph}" data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                                    tag_html += row + 1;
                                                    tag_html += "</td>";
                                                    tag_html += `<td class="edit-table-detail" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}" data-ppn="${ppn}" data-pph="${pph}" data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                                    tag_html += kode_barang;
                                                    tag_html += "</td>";
                                                    tag_html += `<td class="edit-table-detail" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}" data-ppn="${ppn}" data-pph="${pph}" data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                                    tag_html += nama_barang;
                                                    tag_html += "</td>";
                                                    tag_html += `<td class="edit-table-detail" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}" data-ppn="${ppn}" data-pph="${pph}" data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                                    tag_html += satuan;
                                                    tag_html += "</td>";
                                                    tag_html += `<td class="edit-table-detail" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}" data-ppn="${ppn}" data-pph="${pph}" data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                                    tag_html += qty;
                                                    tag_html += "</td>";
                                                    tag_html += `<td class="edit-table-detail" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}" data-ppn="${ppn}" data-pph="${pph}" data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                                    tag_html += jml_masuk;
                                                    tag_html += "</td>";
                                                    tag_html += `<td class="edit-table-detail" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}" data-ppn="${ppn}" data-pph="${pph}" data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                                    tag_html += selisih;
                                                    tag_html += "</td>";
                                                    tag_html += `<td class="edit-table-detail" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}" data-ppn="${ppn}" data-pph="${pph}" data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                                    tag_html += harga;
                                                    tag_html += "</td>";
                                                    tag_html += `<td class="edit-table-detail" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}" data-ppn="${ppn}" data-pph="${pph}" data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                                    tag_html += nilai_sub_total;
                                                    tag_html += "</td>";
                                                    tag_html += `<td class="edit-table-detail" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}" data-ppn="${ppn}" data-pph="${pph}" data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                                    tag_html += keterangan;
                                                    tag_html += "</td>";
                                                    tag_html += `<td>`;
                                                    tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
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
                                                        selisih: selisih,
                                                        satuan: satuan,
                                                        jml_masuk: jml_masuk,
                                                        harga: harga,
                                                        sub_total: nilai_sub_total,
                                                        keterangan: keterangan,
                                                        ppn: ppn,
                                                        nilai_ppn: nilai_ppn,
                                                        pph: pph,
                                                        nilai_pph: nilai_pph,
                                                        warehouse: new_list_warehouse
                                                    });

                                                }
                                                else
                                                {
                                                    tag_html += `<tr>`;
                                                    tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                                    tag_html += row + 1;
                                                    tag_html += "</td>";
                                                    tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                                    tag_html += item.kode_barang;
                                                    tag_html += "</td>";
                                                    tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                                    tag_html += item.nama_barang;
                                                    tag_html += "</td>";
                                                    tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                                    tag_html += item.satuan;
                                                    tag_html += "</td>";
                                                    tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                                    tag_html += item.qty;
                                                    tag_html += "</td>";
                                                    tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                                    tag_html += item.jml_masuk;
                                                    tag_html += "</td>";
                                                    tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                                    tag_html += item.selisih;
                                                    tag_html += "</td>";
                                                    tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                                    tag_html += item.harga;
                                                    tag_html += "</td>";
                                                    tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                                    tag_html += item.sub_total;
                                                    tag_html += "</td>";
                                                    tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                                                    tag_html += item.keterangan;
                                                    tag_html += "</td>";
                                                    tag_html += `<td>`;
                                                    tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                                                    tag_html += "</td>";
                                                    tag_html += "</tr>";

                                                    new_list_items.push(item);
                                                }
                                                row = row + 1;

                                                total_jml_order = total_jml_order + qty;
                                                total_jml_masuk = total_jml_masuk + jml_masuk;
                                                total_jml_selisih = total_jml_selisih + selisih;
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
                                            tag_total += total_jml_masuk;
                                            tag_total += "</td>";
                                            tag_total += `<td>`;
                                            tag_total += total_jml_selisih;
                                            tag_total += "</td>";
                                            tag_total += `<td>`;
                                            tag_total += total_jml_harga.toLocaleString();
                                            tag_total += "</td>";
                                            tag_total += `<td>`;
                                            tag_total += total_jml_sub_total.toLocaleString();
                                            tag_total += "</td>";
                                            tag_total += `<td colspan="2">`;
                                            tag_total += "</td>";
                                            tag_total += "</tr>";

                                            $(".foot-detail-table").append(tag_total);

                                            $(".detail-modal").modal("hide")
                                        }
                                    })
                                }
                            }
                            // create detail
                            else
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
                                            selisih = jml_masuk - qty;
                                            total_jml_order = total_jml_order + qty;
                                            total_jml_masuk = total_jml_masuk + jml_masuk;
                                            total_jml_selisih = total_jml_selisih + selisih;
                                            total_jml_harga = total_jml_harga + (harga ? Number(harga.replaceAll(",", "")) : 0);
                                            total_jml_sub_total = total_jml_sub_total + (nilai_sub_total ? Number(nilai_sub_total.replaceAll(",", "")) : 0);

                                            list_items.push({
                                                id: '',
                                                purchase_order_details_id: purchase_order_details_id,
                                                row: row + 1,
                                                barang_id: barang_id,
                                                unit: unit,
                                                kode_barang: kode_barang,
                                                nama_barang: nama_barang,
                                                nama_barang_dokumen: nama_barang_dokumen,
                                                qty: qty,
                                                selisih: selisih,
                                                satuan: satuan,
                                                jml_masuk: jml_masuk,
                                                harga: harga,
                                                sub_total: nilai_sub_total,
                                                keterangan: keterangan,
                                                ppn: ppn,
                                                nilai_ppn: nilai_ppn,
                                                pph: pph,
                                                nilai_pph: nilai_pph,
                                                warehouse: new_list_warehouse
                                            })

                                            console.log(list_items)

                                            let tag_html = "";
                                            let tag_total = "";

                                            console.log(keterangan);
                                            tag_html += `<tr>`;
                                            tag_html += `<td class="edit-table-detail" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}" data-ppn="${ppn}" data-pph="${pph}" data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="" data-row="${row + 1}">`;
                                            tag_html += row + 1;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}" data-ppn="${ppn}" data-pph="${pph}" data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="" data-row="${row + 1}">`;
                                            tag_html += kode_barang;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}" data-ppn="${ppn}" data-pph="${pph}" data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="" data-row="${row + 1}">`;
                                            tag_html += nama_barang;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}" data-ppn="${ppn}" data-pph="${pph}" data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="" data-row="${row + 1}">`;
                                            tag_html += satuan;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}" data-ppn="${ppn}" data-pph="${pph}" data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="" data-row="${row + 1}">`;
                                            tag_html += qty;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}" data-ppn="${ppn}" data-pph="${pph}" data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="" data-row="${row + 1}">`;
                                            tag_html += jml_masuk;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}" data-ppn="${ppn}" data-pph="${pph}" data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="" data-row="${row + 1}">`;
                                            tag_html += selisih;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}" data-ppn="${ppn}" data-pph="${pph}" data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="" data-row="${row + 1}">`;
                                            tag_html += harga;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}" data-ppn="${ppn}" data-pph="${pph}" data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="" data-row="${row + 1}">`;
                                            tag_html += nilai_sub_total;
                                            tag_html += "</td>";
                                            tag_html += `<td class="edit-table-detail" data-barang_id="${barang_id}" data-unit="${unit}" data-keterangan="${keterangan}" data-ppn="${ppn}" data-pph="${pph}" data-sub_total="${nilai_sub_total}" data-harga="${harga}" data-nama_barang_dokumen="${nama_barang_dokumen}" data-qty="${qty}" data-satuan="${satuan}" data-nama_barang="${nama_barang}" data-kode="${kode_barang}" data-purchase_order_details_id="${purchase_order_details_id}" data-id="" data-row="${row + 1}">`;
                                            tag_html += keterangan;
                                            tag_html += "</td>";
                                            tag_html += `<td>`;
                                            tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                                            tag_html += "</td>";
                                            tag_html += "</tr>";
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
                                            tag_total += total_jml_masuk;
                                            tag_total += "</td>";
                                            tag_total += `<td>`;
                                            tag_total += total_jml_selisih;
                                            tag_total += "</td>";
                                            tag_total += `<td>`;
                                            tag_total += total_jml_harga.toLocaleString();
                                            tag_total += "</td>";
                                            tag_total += `<td>`;
                                            tag_total += total_jml_sub_total.toLocaleString();
                                            tag_total += "</td>";
                                            tag_total += `<td colspan="2">`;
                                            tag_total += "</td>";
                                            tag_total += "</tr>";

                                            $(".foot-detail-table").append(tag_total);

                                            $(".detail-modal").modal("hide")
                                            row = row + 1;
                                        }
                                    })
                                }
                            }
                        // }
                    }
                }
            }
        })

        $(".btn-submit-parent-and-close").click(function() {
            $(".detail-modal").modal("hide")

            // CHECK IF NO BARANG
            if(list_items.length === 0)
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
                                if(list_delete.length !== 0)
                                {
                                    list_delete.map(obj => {
                                        update_list_items.push(
                                            {
                                                id: obj.id ? Number(obj.id) : "",
                                                purchase_order_details_id: obj.purchase_order_details_id ? Number(obj.purchase_order_details_id) : 0,
                                                warehouse: JSON.stringify(obj.warehouse),
                                                barang_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                                unit: obj.unit ? Number(obj.unit) : 0,
                                                nama_barang_dok: obj.nama_barang_dokumen,
                                                qty: obj.qty ? Number(obj.qty) : 0,
                                                selisih: obj.selisih ? Number(obj.selisih) : 0,
                                                jml_masuk: obj.jml_masuk ? Number(obj.jml_masuk) : 0,
                                                harga: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                                sub_total: obj.sub_total ? Number(obj.sub_total.replaceAll(",", "")) : 0,
                                                keterangan: obj.keterangan,
                                                ppn: obj.ppn ? Number(obj.ppn) : 0,
                                                pph: obj.pph ? Number(obj.pph) : 0,
                                                is_delete: true
                                            }
                                        )
                                    })
                                }
                                
                                list_items.map(obj => {
                                    if (obj.id) {
                                        update_list_items.push(
                                            {
                                                id: obj.id ? Number(obj.id) : "",
                                                purchase_order_details_id: obj.purchase_order_details_id ? Number(obj.purchase_order_details_id) : 0,
                                                warehouse: JSON.stringify(obj.warehouse),
                                                barang_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                                unit: obj.unit ? Number(obj.unit) : 0,
                                                nama_barang_dok: obj.nama_barang_dokumen,
                                                qty: obj.qty ? Number(obj.qty) : 0,
                                                selisih: obj.selisih ? Number(obj.selisih) : 0,
                                                jml_masuk: obj.jml_masuk ? Number(obj.jml_masuk) : 0,
                                                harga: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                                sub_total: obj.sub_total ? Number(obj.sub_total.replaceAll(",", "")) : 0,
                                                keterangan: obj.keterangan,
                                                ppn: obj.ppn ? Number(obj.ppn) : 0,
                                                pph: obj.pph ? Number(obj.pph) : 0,
                                                is_delete: false
                                            }
                                        )
                                    }
                                    else
                                    {
                                        update_list_items.push(
                                            {
                                                id: obj.id ? Number(obj.id) : "",
                                                purchase_order_details_id: obj.purchase_order_details_id ? Number(obj.purchase_order_details_id) : 0,
                                                warehouse: JSON.stringify(obj.warehouse),
                                                barang_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                                unit: obj.unit ? Number(obj.unit) : 0,
                                                nama_barang_dok: obj.nama_barang_dokumen,
                                                qty: obj.qty ? Number(obj.qty) : 0,
                                                selisih: obj.selisih ? Number(obj.selisih) : 0,
                                                jml_masuk: obj.jml_masuk ? Number(obj.jml_masuk) : 0,
                                                harga: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                                sub_total: obj.sub_total ? Number(obj.sub_total.replaceAll(",", "")) : 0,
                                                keterangan: obj.keterangan,
                                                ppn: obj.ppn ? Number(obj.ppn) : 0,
                                                pph: obj.pph ? Number(obj.pph) : 0,
                                                is_delete: false
                                            }
                                        )
                                    }
                                })

                                data.append("items", JSON.stringify(update_list_items))

                                data.append("status_post", "FINISH");

                                $.ajax({
                                    url: "<?= base_url("penerimaan-barang-lokal/update"); ?>",
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
                                                window.location.href = "<?= base_url("penerimaan-barang-lokal"); ?>" + "/id/" + id;
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
                                list_items.map(obj => {
                                    update_list_items.push(
                                        {
                                            purchase_order_details_id: obj.purchase_order_details_id ? Number(obj.purchase_order_details_id) : 0,
                                            warehouse: JSON.stringify(obj.warehouse),
                                            barang_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                            unit: obj.unit ? Number(obj.unit) : 0,
                                            nama_barang_dok: obj.nama_barang_dokumen,
                                            qty: obj.qty ? Number(obj.qty) : 0,
                                            selisih: obj.selisih ? Number(obj.selisih) : 0,
                                            jml_masuk: obj.jml_masuk ? Number(obj.jml_masuk) : 0,
                                            harga: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                            sub_total: obj.sub_total ? Number(obj.sub_total.replaceAll(",", "")) : 0,
                                            keterangan: obj.keterangan,
                                            ppn: obj.ppn ? Number(obj.ppn) : 0,
                                            pph: obj.pph ? Number(obj.pph) : 0
                                        }
                                    )
                                })

                                data.append("items", JSON.stringify(update_list_items))

                                data.append("status_post", "FINISH");

                                $.ajax({
                                    url: "<?= base_url("penerimaan-barang-lokal/save"); ?>",
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
                                                window.location.href = "<?= base_url("penerimaan-barang-lokal"); ?>" + "/id/" + response.id;
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
                    })
                }
            }
        })

        $(".btn-submit-parent").click(function() {
            $(".detail-modal").modal("hide")

            // CHECK IF NO BARANG
            if(list_items.length === 0)
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
                                if(list_delete.length !== 0)
                                {
                                    list_delete.map(obj => {
                                        update_list_items.push(
                                            {
                                                id: obj.id ? Number(obj.id) : "",
                                                purchase_order_details_id: obj.purchase_order_details_id ? Number(obj.purchase_order_details_id) : 0,
                                                warehouse: JSON.stringify(obj.warehouse),
                                                barang_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                                unit: obj.unit ? Number(obj.unit) : 0,
                                                nama_barang_dok: obj.nama_barang_dokumen,
                                                qty: obj.qty ? Number(obj.qty) : 0,
                                                selisih: obj.selisih ? Number(obj.selisih) : 0,
                                                jml_masuk: obj.jml_masuk ? Number(obj.jml_masuk) : 0,
                                                harga: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                                sub_total: obj.sub_total ? Number(obj.sub_total.replaceAll(",", "")) : 0,
                                                keterangan: obj.keterangan,
                                                ppn: obj.ppn ? Number(obj.ppn) : 0,
                                                pph: obj.pph ? Number(obj.pph) : 0,
                                                is_delete: true
                                            }
                                        )
                                    })
                                }
                                
                                list_items.map(obj => {
                                    if (obj.id) {
                                        update_list_items.push(
                                            {
                                                id: obj.id ? Number(obj.id) : "",
                                                purchase_order_details_id: obj.purchase_order_details_id ? Number(obj.purchase_order_details_id) : 0,
                                                warehouse: JSON.stringify(obj.warehouse),
                                                barang_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                                unit: obj.unit ? Number(obj.unit) : 0,
                                                nama_barang_dok: obj.nama_barang_dokumen,
                                                qty: obj.qty ? Number(obj.qty) : 0,
                                                selisih: obj.selisih ? Number(obj.selisih) : 0,
                                                jml_masuk: obj.jml_masuk ? Number(obj.jml_masuk) : 0,
                                                harga: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                                sub_total: obj.sub_total ? Number(obj.sub_total.replaceAll(",", "")) : 0,
                                                keterangan: obj.keterangan,
                                                ppn: obj.ppn ? Number(obj.ppn) : 0,
                                                pph: obj.pph ? Number(obj.pph) : 0,
                                                is_delete: false
                                            }
                                        )
                                    }
                                    else
                                    {
                                        update_list_items.push(
                                            {
                                                id: obj.id ? Number(obj.id) : "",
                                                purchase_order_details_id: obj.purchase_order_details_id ? Number(obj.purchase_order_details_id) : 0,
                                                warehouse: JSON.stringify(obj.warehouse),
                                                barang_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                                unit: obj.unit ? Number(obj.unit) : 0,
                                                nama_barang_dok: obj.nama_barang_dokumen,
                                                qty: obj.qty ? Number(obj.qty) : 0,
                                                selisih: obj.selisih ? Number(obj.selisih) : 0,
                                                jml_masuk: obj.jml_masuk ? Number(obj.jml_masuk) : 0,
                                                harga: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                                sub_total: obj.sub_total ? Number(obj.sub_total.replaceAll(",", "")) : 0,
                                                keterangan: obj.keterangan,
                                                ppn: obj.ppn ? Number(obj.ppn) : 0,
                                                pph: obj.pph ? Number(obj.pph) : 0,
                                                is_delete: false
                                            }
                                        )
                                    }
                                })

                                data.append("items", JSON.stringify(update_list_items))

                                data.append("status_post", "WAITING");

                                $.ajax({
                                    url: "<?= base_url("penerimaan-barang-lokal/update"); ?>",
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
                                                window.location.href = "<?= base_url("penerimaan-barang-lokal"); ?>" + "/id/" + id;
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
                                list_items.map(obj => {
                                    update_list_items.push(
                                        {
                                            purchase_order_details_id: obj.purchase_order_details_id ? Number(obj.purchase_order_details_id) : 0,
                                            warehouse: JSON.stringify(obj.warehouse),
                                            barang_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                            unit: obj.unit ? Number(obj.unit) : 0,
                                            nama_barang_dok: obj.nama_barang_dokumen,
                                            qty: obj.qty ? Number(obj.qty) : 0,
                                            selisih: obj.selisih ? Number(obj.selisih) : 0,
                                            jml_masuk: obj.jml_masuk ? Number(obj.jml_masuk) : 0,
                                            harga: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                            sub_total: obj.sub_total ? Number(obj.sub_total.replaceAll(",", "")) : 0,
                                            keterangan: obj.keterangan,
                                            ppn: obj.ppn ? Number(obj.ppn) : 0,
                                            pph: obj.pph ? Number(obj.pph) : 0
                                        }
                                    )
                                })

                                data.append("items", JSON.stringify(update_list_items))

                                data.append("status_post", "WAITING");

                                $.ajax({
                                    url: "<?= base_url("penerimaan-barang-lokal/save"); ?>",
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
                                                window.location.href = "<?= base_url("penerimaan-barang-lokal"); ?>" + "/id/" + response.id;
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
                        }
                    })
                }
            }
        })

        $(".btn-add-row").click(function() {
            row_detail++;
            list_warehouse.push(
            {
                row: row_detail,
                display: "",
                warehouse_id: "",
                qty_warehouse: "",
                satuan: "",
                warehouse_name: "",
                satuan_name: ""
            })
            
            let tag_html = "";
            tag_html += `<tr class="table_${row_detail}">`;
            tag_html += `<td>`;
            tag_html += `<select class="warehouse_id_${row_detail} form-select" id="warehouse_id_${row_detail}" name="warehouse_id_${row_detail}">`;
            tag_html += '<option value=""></option>';
            data_warehouse.forEach(function(item) {
                tag_html += `<option value="${item.id}">${item.warehouse_name}</option>`;
            })
            tag_html += `</select>`;
            tag_html += `</td>`;
            tag_html += `<td>`;
            tag_html += `<select class="satuan_${row_detail} form-select" id="satuan_${row_detail}" name="satuan_${row_detail}">`;
            tag_html += '<option value=""></option>';
            data_satuan.forEach(function(item) {
                tag_html += `<option value="${item.id}">${item.nama_satuan}</option>`;
            })
            tag_html += `</select>`;
            tag_html += `<td>`;
            tag_html += `<input oninput="this.value=this.value.replace(/[^0-9]/g,'');" type="text" class="form-control qty_warehouse_${row_detail}" id="qty_warehouse_${row_detail}" name="qty_warehouse_${row_detail}">`;
            tag_html += `</td>`;
            tag_html += `<td>`;
            tag_html += `<button onclick='deleteChildRow(${row_detail})'>X</button>`;
            tag_html += `</td>`;
            tag_html += `</tr>`;


            $(".body-detail-warehouse").append(tag_html)

            // WAREHOUSE
            $(".warehouse_id_" + row_detail).select2({
                placeholder: "",
                theme: "bootstrap-5",
                dropdownParent: $(".detail-modal .modal-content")
            })

            // satuan
            $(".satuan_" + row_detail).select2({
                placeholder: "",
                theme: "bootstrap-5",
                dropdownParent: $(".detail-modal .modal-content")
            })
        })

        $(".btn-show-detail").click(function() {
            if($('.multiple_po_id option:selected').length !== 0)
            {
                $(".delete-detail").css('display', 'none');

                $(".title-detail-name").text("Tambah");

                validator_detail.resetForm();
                validator_detail.reset();

                $(".id_detail").val('');
                $(".nama_barang_dokumen").val('')
                $(".unit").val('')
                $(".harga_barang_jasa").val('')
                $(".nilai_sub_total").val('')
                list_warehouse = [];
                row_detail = 0;
                $(".body-detail-warehouse").empty()

                console.log($('.multiple_po_id').val())

                let arr = $('.multiple_po_id').val();

                if($(".tipe_bahan").val() === "BAKU")
                {
                    $(".kode_barang").empty()
                    $(".kode_barang").append(`<option value=""></option>`)
                    arr?.forEach((items) => {
                        $.ajax({
                            url: `<?= base_url("po-lokal-bahan-baku/multi/dropdown"); ?>`,
                            method: "GET",
                            data: {
                                id: items
                            },
                            dataType: "json",
                            success: function(res) {
                                console.log(res)
                                
                                res.data.forEach(function(item) {
                                    $(".kode_barang").append(`<option data-unit="${item.id_satuan}" data-harga="${item.general_price}" data-nama="${item.nama_barang}" data-barang_id="${item.barang_id}" data-note="${item.note}" data-id="${item.id}" data-qty="${Number(item.qty ? item.qty.replaceAll(",", "") : 0)}" data-satuan="${item.nama_satuan}" value="${item.kode_barang}">${item.kode_barang} - ${item.nama_barang}</option>`)
                                })
                            }
                        })
                    })

                    $(".kode_barang").val("").change();
                }
                if($(".tipe_bahan").val() === "PENOLONG")
                {
                    $(".kode_barang").empty()
                    $(".kode_barang").append(`<option value=""></option>`)
                    arr?.forEach((items) => {
                        $.ajax({
                            url: `<?= base_url("po-lokal-bahan-penolong/multi/dropdown"); ?>`,
                            method: "GET",
                            data: {
                                id: items
                            },
                            dataType: "json",
                            success: function(res) {
                                console.log(res)
                                
                                res.data.forEach(function(item) {
                                    $(".kode_barang").append(`<option data-unit="${item.id_satuan}" data-harga="${item.price}" data-nama="${item.nama_barang}" data-note="${item.note}" data-id="${item.id}" data-barang_id="${item.barang_id}" data-qty="${Number(item.qty ? item.qty.replaceAll(",", "") : 0)}" data-satuan="${item.nama_satuan}" value="${item.kode_barang}">${item.kode_barang} - ${item.nama_barang}</option>`)
                                })
                            }
                        })
                    })

                    $(".kode_barang").val("").change();
                }

                $.ajax({
                    url: `<?= base_url("tax/dropdown"); ?>`,
                    method: "GET",
                    data: {
                        type: 'ppn'
                    },
                    dataType: "json",
                    success: function(res) {
                        $(".ppn").empty()
                        $(".ppn").append(`<option value=""></option>`)
                        res.data.forEach(function(item) {
                            $(".ppn").append(`<option value="${item.id}">${item.tax_value}</option>`)
                        })

                        $(".ppn").val("").change();
                    }
                })

                $.ajax({
                    url: `<?= base_url("tax/dropdown"); ?>`,
                    method: "GET",
                    data: {
                        type: 'pph'
                    },
                    dataType: "json",
                    success: function(res) {
                        $(".pph").empty()
                        $(".pph").append(`<option value=""></option>`)
                        res.data.forEach(function(item) {
                            $(".pph").append(`<option value="${item.id}">${item.tax_value}</option>`)
                        })

                        $(".pph").val("").change();
                        $(".detail-modal").modal("show")
                    }
                })
            }
            else
            {
                Swal.fire({
                    icon: 'error',
                    title: "No. PO Wajib Diisi",
                    confirmButtonColor: '#4e73df',
                })
            }
        })

        $(".harga_barang_jasa").keyup(function() {
            let qty = $(".qty").val() ? Number($(".qty").val()) : 0;
            let harga = $(".harga_barang_jasa").val() ? Number($(".harga_barang_jasa").val().replaceAll(",", "")) : 0;
            $(".nilai_sub_total").val((qty * harga).toLocaleString())
        })

        $(".nilai_sub_total").keyup(function() {
            let qty = $(".qty").val() ? Number($(".qty").val()) : 0;
            let harga = $(".nilai_sub_total").val() ? Number($(".nilai_sub_total").val().replaceAll(",", "")) : 0;
            $(".harga_barang_jasa").val(parseInt(harga / qty).toLocaleString())
        })

        $(".multiple_po_id").change(function() {
            total_jml_order = 0;
            total_jml_masuk = 0;
            total_jml_selisih = 0;
            total_jml_harga = 0;
            total_jml_sub_total = 0;

            list_items.push((item) => {
                list_delete.push(item);
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
            tag_total += `<td>`;
            tag_total += 0;
            tag_total += "</td>";
            tag_total += `<td colspan="2">`;
            tag_total += "</td>";
            tag_total += "</tr>";

            $(".foot-detail-table").append(tag_total);
        })

        $(".btn-hide-detail").click(function() {
            $(".detail-modal").modal("hide")
        })

        $(".hide-view-detail").click(function() {
            $(".view-modal").modal("hide")
        })

        $(".kode_barang").change(function() {
            if(trigger) {
                list_warehouse = [];
                row_detail = 0;
                $(".body-detail-warehouse").empty()

                if($(".kode_barang option:selected").val())
                {
                    let nama = $(".kode_barang option:selected").data("nama") ? $(".kode_barang option:selected").data("nama") : "";
                    let barang_id = $(".kode_barang option:selected").data("barang_id") ? $(".kode_barang option:selected").data("barang_id") : "";
                    let po_id = $(".kode_barang option:selected").data("id") ? $(".kode_barang option:selected").data("id") : "";
                    let satuan = $(".kode_barang option:selected").data("satuan") ? $(".kode_barang option:selected").data("satuan") : "";
                    let note = $(".kode_barang option:selected").data("note") ? $(".kode_barang option:selected").data("note") : "";
                    let qty = $(".kode_barang option:selected").data("qty") ? $(".kode_barang option:selected").data("qty") : 0;
                    let harga = $(".kode_barang option:selected").data("harga") ? $(".kode_barang option:selected").data("harga") : "";
                    let unit = $(".kode_barang option:selected").data("unit") ? $(".kode_barang option:selected").data("unit") : 0;


                    console.log(barang_id)
                    $(".kode").val($(".kode_barang option:selected").val());
                    $(".nama_barang").val(nama);
                    $(".unit").val(unit);
                    $(".barang_id").val(barang_id);
                    $(".purchase_order_details_id").val(po_id);
                    $(".satuan_order").val(satuan);
                    $(".qty").val(qty);
                    $(".keterangan").val(note);
                    
                    $(".nama_barang_dokumen").val(nama);
                    $(".harga_barang_jasa").val(harga);
                }
                else
                {
                    $(".kode").val("");
                    $(".nama_barang").val("");
                    $(".unit").val("");
                    $(".barang_id").val("");
                    $(".purchase_order_details_id").val("");
                    $(".satuan_order").val("");
                    $(".qty").val("");
                    $(".keterangan").val("");

                    $(".nama_barang_dokumen").val("");
                    $(".harga_barang_jasa").val("");
                }
            }
        })

        // $(".posting-penerimaan").click(function() {
        //     Swal.fire({
        //         icon: 'question',
        //         title: 'Yakin akan di Posting?',
        //         confirmButtonColor: '#4e73df',
        //         cancelButtonColor: '#d33',
        //         showCancelButton: true,
        //         reverseButtons: true,
        //         confirmButtonText: 'Posting',
        //         cancelButtonText: 'Batal',
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             const csrf = $(`[name="${csrfToken}"]`);
        //             $.ajax({
        //                 url: "<?= base_url("penerimaan-barang-lokal/update-status"); ?>",
        //                 data: {
        //                     id: $(".id").val()
        //                 },
        //                 beforeSend: function(xhr) {
        //                     xhr.setRequestHeader('X-CSRF-Token', csrf.val());
        //                 },
        //                 method: "POST",
        //                 dataType: "json",
        //                 success: function(response) {
        //                     csrf.val(response.token);
        //                     if (response.status) {
        //                         stopLoading()
        //                         Swal.fire({
        //                             icon: 'success',
        //                             title: response.message,
        //                             confirmButtonColor: '#4e73df',
        //                         })
        //                         .then(() => {
        //                             window.location.href = "<?= base_url("penerimaan-barang-lokal"); ?>" + "/id/" + $(".id").val()
        //                         })
        //                     } else {
        //                         Swal.fire({
        //                             icon: 'error',
        //                             title: response.message,
        //                             confirmButtonColor: '#4e73df',
        //                         })
        //                         stopLoading()
        //                     }
        //                 },
        //                 onError: function(response) {
        //                     csrf.val(response.token);
        //                     Swal.fire({
        //                         icon: 'error',
        //                         title: 'Data Gagal Disimpan, coba Lagi',
        //                         confirmButtonColor: '#4e73df',
        //                     })
        //                     stopLoading()
        //                 }
        //             });
        //         }
        //     })
        // })

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
            tag_total += `<td>`;
            tag_total += 0;
            tag_total += "</td>";
            tag_total += `<td colspan="2">`;
            tag_total += "</td>";
            tag_total += "</tr>";

            $(".foot-detail-table").append(tag_total);
            
            if($(".supplier_id option:selected").val())
            {
                if($(".tipe_bahan").val() === "BAKU")
                {
                    $.ajax({
                        url: `<?= base_url("po-lokal-bahan-baku/dropdown"); ?>`,
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
                        url: `<?= base_url("po-lokal-bahan-penolong/dropdown"); ?>`,
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

    const deleteChildRow = function(id) {
        $(".table_" + id).css("display", "none")
        let new_list_warehouse = []
        list_warehouse.forEach((item) => {
            if(item.row !== id)
            {
                new_list_warehouse.push(item)
            }
            else
            {
                new_list_warehouse.push({warehouse_name: item.warehouse_name, satuan_name: item.satuan_name, satuan: item.satuan, warehouse_id: item.warehouse_id, qty_warehouse: item.qty_warehouse, display: "none"})
            }
        })

        list_warehouse = new_list_warehouse;
    }

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

    const deleteRow = function(id) {
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
                console.log(id)
                let new_list_items = []
                let tag_html = "";
                let tag_total = "";

                $(".body-detail-table").empty()

                row = 0;

                console.log(list_items)

                total_jml_order = 0;
                total_jml_masuk = 0;
                total_jml_selisih = 0;
                total_jml_harga = 0;
                total_jml_sub_total = 0;


                list_items.map(item => {
                    if(item.row != id)
                    {
                        tag_html += `<tr>`;
                       tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += row + 1;
                        tag_html += "</td>";
                       tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.kode_barang;
                        tag_html += "</td>";
                       tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.nama_barang;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.satuan;
                        tag_html += "</td>";
                       tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.qty;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.jml_masuk;
                        tag_html += "</td>";
                       tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.selisih;
                        tag_html += "</td>";
                       tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.harga;
                        tag_html += "</td>";
                       tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.sub_total;
                        tag_html += "</td>";
                       tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.keterangan;
                        tag_html += "</td>";
                       tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_list_items.push({...item, row: row + 1});

                        row = row + 1;

                        total_jml_order = total_jml_order + item.qty;
                        total_jml_masuk = total_jml_masuk + item.jml_masuk;
                        total_jml_selisih = total_jml_selisih + item.selisih;
                        total_jml_harga = total_jml_harga + (item.harga ? Number(item.harga.replaceAll(",", "")) : 0);
                        total_jml_sub_total = total_jml_sub_total + (item.sub_total ? Number(item.sub_total.replaceAll(",", "")) : 0);
                    }
                    else
                    {
                        // sent parameter isDelete if have customer id and id
                        if(item.id)
                        {
                            list_delete.push(item)
                        }
                    }
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
                tag_total += total_jml_masuk;
                tag_total += "</td>";
                tag_total += `<td>`;
                tag_total += total_jml_selisih;
                tag_total += "</td>";
                tag_total += `<td>`;
                tag_total += total_jml_harga.toLocaleString();
                tag_total += "</td>";
                tag_total += `<td>`;
                tag_total += total_jml_sub_total.toLocaleString();
                tag_total += "</td>";
                tag_total += `<td colspan="2">`;
                tag_total += "</td>";
                tag_total += "</tr>";

                $(".foot-detail-table").append(tag_total);

                $(".detail-modal").modal("hide")
            }
        })
    }

    $(document).on('click', '.edit-table-detail', function(evt) {
        $(".title-detail-name").text("Update")
        $(".delete-detail").css('display', '');
        let ppn = $(this).data('ppn') ? Number($(this).data('ppn')) : ""
        let pph = $(this).data('pph') ? Number($(this).data('pph')) : ""
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

        row_detail = 0;
        list_warehouse = [];

        validator_detail.resetForm();
        validator_detail.reset();

        $(".id_detail").val(rowid)
        $(".kode").val(kode)
        $(".unit").val(unit)
        trigger = false;

        $(".body-detail-warehouse").empty()

        let last_warehouse = [];
        // get warehouse list by row
        list_items.forEach((item) => {
            if(item.row === rowid)
            {
                last_warehouse = item.warehouse;
            }
        })

        last_warehouse.forEach((item) => {
            row_detail++;
            list_warehouse.push(
            {
                row: row_detail,
                display: "",
                warehouse_id: item.warehouse_id,
                qty_warehouse: item.qty,
                satuan: item.satuan,
                warehouse_name: item.warehouse_name,
                satuan_name: item.satuan_name
            })
            
            let tag_html = "";
            tag_html += `<tr class="table_${row_detail}">`;
            tag_html += `<td>`;
            tag_html += `<select class="warehouse_id_${row_detail} form-select" id="warehouse_id_${row_detail}" name="warehouse_id_${row_detail}">`;
            tag_html += '<option value=""></option>';
            console.log(data_warehouse);
            data_warehouse.forEach(function(items) {
                console.log(Number(items.id), Number(item.warehouse_id))
                if(Number(items.id) === Number(item.warehouse_id))
                {
                    tag_html += `<option selected value="${items.id}">${items.warehouse_name}</option>`;
                }
                else
                {
                    tag_html += `<option value="${items.id}">${items.warehouse_name}</option>`;
                }
            })
            tag_html += `</select>`;
            tag_html += `</td>`;
            tag_html += `<td>`;
            tag_html += `<select class="satuan_${row_detail} form-select" id="satuan_${row_detail}" name="satuan_${row_detail}">`;
            tag_html += '<option value=""></option>';

            data_satuan.forEach(function(items) {
                if(Number(items.id) === Number(item.satuan))
                {
                    tag_html += `<option selected value="${items.id}">${items.nama_satuan}</option>`;
                }
                else
                {
                    tag_html += `<option value="${items.id}">${items.nama_satuan}</option>`;
                }
            })
            tag_html += `</select>`;
            tag_html += `<td>`;
            tag_html += `<input value="${item.qty}" ="this.value=this.value.replace(/[^0-9]/g,'');" type="text" class="form-control qty_warehouse_${row_detail}" id="qty_warehouse_${row_detail}" name="qty_warehouse_${row_detail}">`;
            tag_html += `</td>`;
            tag_html += `<td>`;
            tag_html += `<button onclick='deleteChildRow(${row_detail})'>X</button>`;
            tag_html += `</td>`;
            tag_html += `</tr>`;

            $(".body-detail-warehouse").append(tag_html)

            // WAREHOUSE
            $(".warehouse_id_" + row_detail).select2({
                placeholder: "",
                theme: "bootstrap-5",
                dropdownParent: $(".detail-modal .modal-content")
            })

            // satuan
            $(".satuan_" + row_detail).select2({
                placeholder: "",
                theme: "bootstrap-5",
                dropdownParent: $(".detail-modal .modal-content")
            })
        })

        $(".nilai_sub_total").val(sub_total)
        $(".harga_barang_jasa").val(harga)
        $(".keterangan").val(keterangan)
        $(".barang_id").val(barang_id)
        $(".qty").val(qty)
        $(".nama_barang_dokumen").val(nama_barang_dokumen)
        $(".satuan_order").val(satuan)
        $(".nama_barang").val(nama_barang)
        $(".purchase_order_details_id").val(purchase_order_details_id)

        let arr = $('.multiple_po_id').val();

        console.log(arr);

        if($(".tipe_bahan").val() === "BAKU")
        {
            $(".kode_barang").empty()
            $(".kode_barang").append(`<option value=""></option>`)
            arr?.forEach((items) => {
                $.ajax({
                    url: `<?= base_url("po-lokal-bahan-baku/multi/dropdown"); ?>`,
                    method: "GET",
                    data: {
                        id: items
                    },
                    dataType: "json",
                    success: function(res) {
                        console.log("ini kode", kode)
                        console.log(res)
                        
                        res.data.forEach(function(item) {
                            if(Number(barang_id) === Number(item.barang_id))
                            {
                                $(".kode_barang").append(`<option selected data-unit="${item.id_satuan}" data-harga="${item.general_price}" data-nama="${item.nama_barang}" data-barang_id="${item.barang_id}" data-note="${item.note}" data-id="${item.id}" data-qty="${Number(item.qty ? item.qty.replaceAll(",", "") : 0)}" data-satuan="${item.nama_satuan}" value="${item.kode_barang}">${item.kode_barang} - ${item.nama_barang}</option>`)
                            }
                            else
                            {
                                $(".kode_barang").append(`<option data-unit="${item.id_satuan}" data-harga="${item.general_price}" data-nama="${item.nama_barang}" data-barang_id="${item.barang_id}" data-note="${item.note}" data-id="${item.id}" data-qty="${Number(item.qty ? item.qty.replaceAll(",", "") : 0)}" data-satuan="${item.nama_satuan}" value="${item.kode_barang}">${item.kode_barang} - ${item.nama_barang}</option>`)
                            }
                        })
                    }
                })
            })
        }
        if($(".tipe_bahan").val() === "PENOLONG")
        {
            $(".kode_barang").empty()
            $(".kode_barang").append(`<option value=""></option>`)
            arr?.forEach((items) => {
                $.ajax({
                    url: `<?= base_url("po-lokal-bahan-penolong/multi/dropdown"); ?>`,
                    method: "GET",
                    data: {
                        id: items
                    },
                    dataType: "json",
                    success: function(res) {
                        console.log(res)

                        res.data.forEach(function(item) {     
                            if(Number(barang_id) === Number(item.barang_id))
                            {
                                $(".kode_barang").append(`<option selected data-unit="${item.id_satuan}" data-harga="${item.general_price}" data-nama="${item.nama_barang}" data-barang_id="${item.barang_id}" data-note="${item.note}" data-id="${item.id}" data-qty="${Number(item.qty ? item.qty.replaceAll(",", "") : 0)}" data-satuan="${item.nama_satuan}" value="${item.kode_barang}">${item.kode_barang} - ${item.nama_barang}</option>`)
                            }
                            else
                            {
                                $(".kode_barang").append(`<option data-unit="${item.id_satuan}" data-harga="${item.general_price}" data-nama="${item.nama_barang}" data-barang_id="${item.barang_id}" data-note="${item.note}" data-id="${item.id}" data-qty="${Number(item.qty ? item.qty.replaceAll(",", "") : 0)}" data-satuan="${item.nama_satuan}" value="${item.kode_barang}">${item.kode_barang} - ${item.nama_barang}</option>`)
                            }
                        })
                    }
                })
            })
        }

        $.ajax({
            url: `<?= base_url("tax/dropdown"); ?>`,
            method: "GET",
            data: {
                type: 'ppn'
            },
            dataType: "json",
            success: function(res) {
                $(".ppn").empty()
                $(".ppn").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".ppn").append(`<option value="${item.id}">${item.tax_value}</option>`)
                })

                $(".ppn").val(ppn).change();
            }
        })

        $.ajax({
            url: `<?= base_url("tax/dropdown"); ?>`,
            method: "GET",
            data: {
                type: 'pph'
            },
            dataType: "json",
            success: function(res) {
                $(".pph").empty()
                $(".pph").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".pph").append(`<option value="${item.id}">${item.tax_value}</option>`)
                })

                $(".pph").val(pph).change();
                trigger = true;
                $(".detail-modal").modal("show")
            }
        })
    })

    $(document).on('click', '.delete-detail', function() {
        let id = $(".id_detail").val()
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
                console.log(id)
                let new_list_items = []
                let tag_html = "";
                let tag_total = "";

                $(".body-detail-table").empty()

                row = 0;

                console.log(list_items)

                total_jml_order = 0;
                total_jml_masuk = 0;
                total_jml_selisih = 0;
                total_jml_harga = 0;
                total_jml_sub_total = 0;

                list_items.map(item => {
                    if(item.row != id)
                    {
                        tag_html += `<tr>`;
                        tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += row + 1;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.kode_barang;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.nama_barang;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.satuan;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.qty;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.jml_masuk;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.selisih;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.harga;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.sub_total;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.keterangan;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.keterangan}" data-ppn="${item.ppn}" data-pph="${item.pph}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-nama_barang_dokumen="${item.nama_barang_dokumen}" data-qty="${item.qty}" data-satuan="${item.satuan}" data-nama_barang="${item.nama_barang}" data-kode="${item.kode_barang}" data-purchase_order_details_id="${item.purchase_order_details_id}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_list_items.push({...item, row: row + 1});

                        row = row + 1;

                        total_jml_order = total_jml_order + item.qty;
                        total_jml_masuk = total_jml_masuk + item.jml_masuk;
                        total_jml_selisih = total_jml_selisih + item.selisih;
                        total_jml_harga = total_jml_harga + (item.harga ? Number(item.harga.replaceAll(",", "")) : 0);
                        total_jml_sub_total = total_jml_sub_total + (item.sub_total ? Number(item.sub_total.replaceAll(",", "")) : 0);
                    }
                    else
                    {
                        // sent parameter isDelete if have customer id and id
                        if(item.id)
                        {
                            list_delete.push(item)
                        }
                    }
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
                tag_total += total_jml_masuk;
                tag_total += "</td>";
                tag_total += `<td>`;
                tag_total += total_jml_selisih;
                tag_total += "</td>";
                tag_total += `<td>`;
                tag_total += total_jml_harga.toLocaleString();
                tag_total += "</td>";
                tag_total += `<td>`;
                tag_total += total_jml_sub_total.toLocaleString();
                tag_total += "</td>";
                tag_total += `<td colspan="2">`;
                tag_total += "</td>";
                tag_total += "</tr>";

                $(".foot-detail-table").append(tag_total);

                $(".detail-modal").modal("hide")
            }
        })
    })

    const changeTipeBahan = function()
    {
        total_jml_order = 0;
        total_jml_masuk = 0;
        total_jml_selisih = 0;
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
        tag_total += `<td>`;
        tag_total += 0;
        tag_total += "</td>";
        tag_total += `<td colspan="2">`;
        tag_total += "</td>";
        tag_total += "</tr>";

        $(".foot-detail-table").append(tag_total);

        $(".supplier_id").attr("disabled", "true");

        if($(".tipe_bahan").val() === "BAKU")
        {
            $.ajax({
                url: `<?= base_url("supplier-bahan-baku/dropdown"); ?>`,
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
                url: `<?= base_url("supplier-bahan-penolong/dropdown"); ?>`,
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

    const view = function(row)
    {
        let tag_html = ""
        $(".body-view-warehouse").empty()
        let last_warehouse = [];
        // get warehouse list by row
        list_items.forEach((item) => {
            if(item.row === Number(row))
            {
                last_warehouse = item.warehouse;
            }
        })

        last_warehouse.forEach((item) => {
            tag_html += `<tr>`;
            tag_html += `<td>`;
            tag_html += item.warehouse_name;
            tag_html += `</td>`;
            tag_html += `<td>`; 
            tag_html += item.satuan_name;
            tag_html += `<td>`;
            tag_html += item.qty;
            tag_html += `</td>`;
            tag_html += `</tr>`;
        })

        $(".body-view-warehouse").append(tag_html)
        $(".view-modal").modal("show")
    }
</script>

<?= $this->endSection(); ?>