<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($dataSalesKontrak) ? "Update Sales Kontrak" : "Tambah Sales Kontrak" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("sales-kontrak"); ?>">
                Kembali
            </a>
            <?php if (!empty($dataSalesKontrak)) { ?>
                <?php if (!$dataSalesKontrak['status_posting']) { ?>
                    <?php ?>
                    <?php if (can('Penjualan Ekspor', 'Sales Kontrak', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right">
                            Hapus
                        </button>
                    <?php endif; ?>
                <?php } ?>
                <?php if (!$dataSalesKontrak['status_posting']) { ?>
                    <?php if (can('Penjualan Ekspor', 'Sales Kontrak', 'a')) : ?>
                        <button class="btn btn-success posting-spp posting-so float-right" onclick="updateStatusPosting('1')">
                            Posting
                        </button>
                    <?php endif; ?>
                <?php } ?>
                <?php if ($dataSalesKontrak['status_posting'] && !$isClosed) { ?>
                    <?php if (can('Penjualan Ekspor', 'Sales Kontrak', 'ua')) : ?>
                        <button class="btn btn-success posting-spp unposting-so float-right" onclick="updateStatusPosting('0')">
                            Un Posting
                        </button>
                    <?php endif; ?>
                <?php } ?>
                <?php if (can('Penjualan Ekspor', 'Sales Kontrak', 'p')) : ?>
                    <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("sales-kontrak/print/"); ?><?= encrypt($dataSalesKontrak['id']); ?>')">
                        Print
                    </button>
                <?php endif; ?>
                <?php if (!$dataSalesKontrak['status_posting']) { ?>
                    <?php if (can('Penjualan Ekspor', 'Sales Kontrak', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php } ?>
            <?php } else { ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php } ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Header</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($dataSalesKontrak) ? encrypt($dataSalesKontrak['id']) : ""; ?>" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting'] == "1" ? 'readonly' : '') : 'readonly'; ?> value="<?= !empty($dataSalesKontrak) ? $dataSalesKontrak['sales_contract_no'] : ""; ?>" type="text" class="form-control sales_contract_no" id="sales_contract_no" name="sales_contract_no" placeholder="No. Sales Contract">
                                    <label for="floatingInput">No Sales Kontrak</label>
                                </div>
                                <div style="<?= !empty($dataSalesKontrak)  ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting'] ? 'disabled=true' : '') : ''; ?>
                                class="form-select divisi_id"
                                aria-label="Floating label select example"
                                name="divisi_id"
                                id="divisi_id">
                                <option value=""></option>
                                <?php foreach ($dataDivisi as $d) : ?>
                                    <option value="<?= $d['id'] ?>"
                                        <?= !empty($dataSalesKontrak['divisi_id']) && $dataSalesKontrak['divisi_id'] == $d['id'] ? 'selected' : '' ?>>
                                        <?= $d['divisi'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Department</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting'] ? 'disabled=true' : '') : ''; ?> class="form-select customer_id" id="customer_id" name="customer_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($dataCustomer)) {
                                        foreach ($dataCustomer as $customer) {
                                    ?>
                                            <option <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['customer_id'] === $customer["id"] ? "selected" : "") : ""; ?> value="<?= $customer["id"]; ?>"><?= strtoupper("(" . $customer['kode'] . ") " . $customer["name"]); ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Customer</label>
                            </div>
                            <?php if (can('Penjualan Ekspor', 'Customer', 'c')) : ?>
                                <div class="input-group-append" style="height:50px;">
                                    <?php if (!empty($dataSalesKontrak)) : ?>
                                        <?php if ($dataSalesKontrak['status_posting']) : ?>

                                        <?php else : ?>
                                            <button class="btn btn-success btn-customer-add" id="btn-customer-add" data-toggle="modal" type="button">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <button class="btn btn-success btn-customer-add" id="btn-customer-add" data-toggle="modal" type="button">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting']  ? 'readonly=true' : '') : ''; ?> value="<?= !empty($dataSalesKontrak) ? $dataSalesKontrak['customer_po_no'] : ""; ?>" type="text" class="form-control customer_po_no" id="customer_po_no" name="customer_po_no" placeholder="No. PO">
                            <label for="floatingInput">No. PO (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting']  ? 'readonly=true' : '') : ''; ?> value="<?= !empty($dataSalesKontrak) ? $dataSalesKontrak['loading_port'] : ""; ?>" type="text" class="form-control loading_port" id="loading_port" name="loading_port" placeholder="Loading Port">
                            <label for="floatingInput">Loading Port</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting']  ? 'readonly=true' : '') : ''; ?> value="<?= !empty($dataSalesKontrak) ? $dataSalesKontrak['dicharge_port'] : ""; ?>" type="text" class="form-control dicharge_port" id="dicharge_port" name="dicharge_port" placeholder="Dicharge Port">
                            <label for="floatingInput">Dicharge Port</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting']  ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['due_date'] ? date("d/m/Y", strtotime($dataSalesKontrak['due_date'])) : "") : ""; ?>" class="form-control input-picker due_date" id="due_date" name="due_date" placeholder="Due Date (Opsional)">
                                    <label for="floatingInput">Due Date</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-due-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting']  ? 'readonly=true' : '') : ''; ?> value="<?= !empty($dataSalesKontrak) ? $dataSalesKontrak['payment_term'] : ""; ?>" type="text" class="form-control payment_term" id="payment_term" name="payment_term" placeholder="Payment Term (Opsional)">
                            <label for="floatingInput">Payment Term (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting']  ? 'readonly=true' : '') : ''; ?> value="<?= !empty($dataSalesKontrak) ? $dataSalesKontrak['tolerance'] : ""; ?>" type="text" class="form-control tolerance" id="tolerance" name="tolerance" placeholder="Tolerance">
                            <label for="floatingInput">Tolerance</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting']  ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['shipment_date'] ? date("d/m/Y", strtotime($dataSalesKontrak['shipment_date'])) : "") : ""; ?>" class="form-control input-picker shipment_date" id="shipment_date" name="shipment_date" placeholder="Shipment Date">
                                    <label for="floatingInput">Shipment Date</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-shipment-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting']  ? 'disabled=true' : '') : ''; ?> class="form-select currency" id="currency" name="currency" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataValuta as $valuta) : ?>
                                    <option <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['currency'] == $valuta['id'] ? 'selected' : '') : '' ?> value="<?= $valuta["id"]; ?>"><?= $valuta["value"]; ?> - <?= strtoupper($valuta["description"]); ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput">Currency</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting']  ? 'disabled=true' : '') : ''; ?> class="form-select tipe_harga" id="tipe_harga" name="tipe_harga" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataTipeHarga as $v) : ?>
                                    <option <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['tipe_harga'] == $v['value'] ? 'selected' : '') : '' ?> value="<?= $v["value"]; ?>"><?= $v["value"]; ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput">Price Type</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input onkeyup="this.value = greatFormatRupiah(this.value)" oninput="preventNegativeInput(this)" autocomplete="one-time-code" <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting']  ? 'readonly=true' : '') : ''; ?> value="<?= !empty($dataSalesKontrak) ? $dataSalesKontrak['potongan_harga'] : ""; ?>" type="text" class="form-control potongan_harga" id="potongan_harga" name="potongan_harga" placeholder="Potongan Harga (Opsional)">
                            <label for="floatingInput">Potongan Harga (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting']  ? 'readonly=true' : '') : ''; ?> value="<?= !empty($dataSalesKontrak) ? $dataSalesKontrak['keterangan'] : ""; ?>" type="text" class="form-control keterangan" id="keterangan" name="keterangan" placeholder="Keterangan (Opsional)">
                            <label for="floatingInput">Keterangan (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting']  ? 'disabled=true' : '') : ''; ?> class="form-select bank_id" id="bank_id" name="bank_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataBank as $d) : ?>
                                    <option <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['bank_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d["id"]; ?>"><?= $d["name"] . " - " . $d['atas_nama'] . " - " . $d['no_rekening']; ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput">Pilih Bank (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting']  ? 'readonly=true' : '') : ''; ?> value="<?= !empty($dataSalesKontrak) ? $dataSalesKontrak['no_container'] : ""; ?>" type="text" class="form-control no_container" id="no_container" name="no_container" placeholder="Nomor Container (Opsional)">
                            <label for="floatingInput">No Container (Opsional)</label>
                        </div>
                    </div>
                </div>
                <!-- <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Broker</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting']  ? 'readonly=true' : '') : ''; ?> value="<?= !empty($dataSalesKontrak) ? $dataSalesKontrak['broker'] : ""; ?>" type="text" class="form-control broker" id="broker" name="broker" placeholder="Agency / Broker (Opsional)">
                            <label for="floatingInput">Agency / Broker (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input onkeyup="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting']  ? 'readonly=true' : '') : ''; ?> value="<?= !empty($dataSalesKontrak) ? $dataSalesKontrak['komisi'] : ""; ?>" type="text" class="form-control komisi" id="komisi" name="komisi" placeholder="Komisi Broker (Opsional)">
                            <label for="floatingInput">Komisi Broker (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting']  ? 'disabled=true' : '') : ''; ?> class="form-select print_out_broker" id="print_out_broker" name="print_out_broker" aria-label="Floating label select example">
                                <option value=""></option>
                                <option <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['print_out_broker'] ? 'selected' : '') : '' ?> value="1">TAMPILKAN BROKER DI PRINT OUT</option>
                                <option <?= !empty($dataSalesKontrak) ? (!$dataSalesKontrak['print_out_broker'] ? 'selected' : '') : '' ?> value="0">JANGAN TAMPILKAN BROKER DI PRINT OUT</option>
                            </select>
                            <label for="floatingInput">Print Out Sales Kontak</label>
                        </div>
                    </div>
                </div> -->
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Dokumen & Special Instructions</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <textarea autocomplete="one-time-code" <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting']  ? 'readonly=true' : '') : ''; ?> class="full-textarea form-control documents_required" id="documents_required" name="documents_required" placeholder="Document Required"><?= !empty($dataSalesKontrak) ? $dataSalesKontrak['documents_required'] : ""; ?></textarea>
                            <label for="floatingInput">Document Required (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <textarea autocomplete="one-time-code" <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting']  ? 'readonly=true' : '') : ''; ?> class="full-textarea form-control special_instructions" id="special_instructions" name="special_instructions" placeholder="Special Instructions"><?= !empty($dataSalesKontrak) ? $dataSalesKontrak['special_instructions'] : ""; ?></textarea>
                            <label for="floatingInput">Special Instructions (Opsional)</label>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Spesifikasi Detail</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <textarea autocomplete="one-time-code" <?= !empty($dataSalesKontrak) ? ($dataSalesKontrak['status_posting']  ? 'readonly=true' : '') : ''; ?> class="full-textarea form-control spesifikasi" id="spesifikasi" name="spesifikasi" placeholder="Document Required"><?= !empty($dataSalesKontrak) ? $dataSalesKontrak['spesifikasi'] : ""; ?></textarea>
                            <label for="floatingInput">Spesifikasi Detail (Opsional)</label>
                        </div>
                    </div>

                </div>
            </form>
            <div class="col-subtitle-modal mt-5">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Barang</label>
                    </div>
                    <div class="col-md-6">
                        <?php if (!empty($dataSalesKontrak)) { ?>
                            <?php if (!$dataSalesKontrak['status_posting']) { ?>
                                <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal">
                                    <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                                </button>
                            <?php } ?>
                        <?php } else { ?>
                            <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode Barang</th>
                                <th>Barang</th>
                                <th>Size</th>
                                <th>Satuan Order</th>
                                <th>Kemasan</th>
                                <th>Remark</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Total Harga</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot class="foot-detail-table" id="foot-detail-table">
                            <tr>
                                <td colspan="6"></td>
                                <td><b>TOTAL</b></td>
                                <td><b>0,00</b></td>
                                <td><b>0,00</b></td>
                                <td><b>0,00</b></td>
                                <td></td>
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
                    <input type="hidden" name="id_detail" class="id_detail" id="id_detail">
                    <div class="row">
                        <div class="col mb-3">
                            <h5 class="title-tambah-barang">Data Barang</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select barang_master_sales_id" name="barang_master_sales_id" id="barang_master_sales_id" aria-label="Floating label select example">
                                        <option value=""></option>
                                    </select>
                                    <label for="floatingInput">Barang</label>
                                </div>
                                <?php if (can('Penjualan Ekspor', 'Master Barang', 'c')) : ?>
                                    <div class="input-group-append" style="height:50px;">
                                        <button class="btn btn-success btn-barang-add" id="btn-barang-add" data-toggle="modal" type="button">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" type="text" class="form-control size" id="size" name="size" placeholder="Size">
                                <label for="floatingInput">Size (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select satuan_order_id" name="satuan_order_id" id="satuan_order_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($dataSatuan as $d) : ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['kode_satuan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Satuan Order</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" type="text" class="form-control kemasan" id="kemasan" name="kemasan" placeholder="Kemasan" oninput="capitalize()">
                                <label for="floatingInput">Kemasan (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col mb-3">
                            <h5 class="title-tambah-barang">Data Harga</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                                <label for="floatingInput">Qty</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onkeyup="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control harga" name="harga" id="harga" placeholder="Harga Satuan">
                                <label for="floatingInput">Harga</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" type="text" class="form-control remark" id="remark" name="remark" placeholder="Remark" oninput="capitalize()">
                                <label for="floatingInput">Remark (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control total" name="total" id="total" placeholder="Total">
                                <label for="floatingInput">Total</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-detail btn-discard mr-3">Kembali</button>
                <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal addCustomerModal" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Customer</h5>
            </div>
            <div class="modal-body">
                <form class="create-form-customer" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="tipe_customer" class="tipe_customer" id="tipe_customer" value="INTERNASIONAL">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control name" id="name" name="name" placeholder="Nama">
                                <label for="floatingInput">Nama Customer</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select country_id" name="country_id" id="country_id">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($dataCountry)) {
                                        foreach ($dataCountry as $dc) {
                                    ?>
                                            <option value="<?= $dc["id"]; ?>">(<?= $dc["code"]; ?>) <?= $dc['country_name'] ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Negara</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <textarea autocomplete="one-time-code" class="form-control address" id="address" name="address"></textarea>
                                <label for="floatingInput">Alamat (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select sales_id" name="sales_id" id="sales_id">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($dataSales)) {
                                        foreach ($dataSales as $sales) {
                                    ?>
                                            <option value="<?= $sales["id"]; ?>"><?= strtoupper($sales["name"]); ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Nama Sales</label>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2 btn-discard-customer" id="btn-discard-customer">Kembali</button>
                <button type="submit" class="btn btn-submit-form btn-submit-customer">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal add-modal" id="addMasterBarangModal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Master Barang</h5>
            </div>
            <div class="modal-body">
                <?= csrf_field() ?>
                <form class="create-form-master-barang" role="form" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <div class="input-group input-group-password">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" id="kode_barang" class="form-control kode_barang" name="kode_barang" placeholder="Kode Barang">
                                        <label for="floatingInput">Kode Barang</label>
                                    </div>
                                    <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                        <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 5px; margin-left: -30px;" id="generate_new_code" name="generate_new_code" type="checkbox" onchange="generateCodeMasterBarang()">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select type_barang" name="type_barang" id="type_barang">
                                    <option value="">Pilih Tipe Barang</option>
                                    <option value="bahan_jadi">BARANG JADI</option>
                                    <option value="kemasan">KEMASAN</option>

                                </select>
                                <label for="floatingInput" style="z-index: 1;">Tipe Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select spesifikasi_id" name="spesifikasi_id" id="spesifikasi_id">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Pilih Dari Master Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control barang_name" name="barang_name" id="barang_name" placeholder="Nama Kemasan">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select satuan_id" name="satuan_id" id="satuan_id">
                                    <option value=""></option>
                                    <?php foreach ($dataSatuan as $d) : ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['kode_satuan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Satuan Default (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onkeyup="this.value=greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control harga_jual" name="harga_jual" id="harga_jual" placeholder="Harga Jual">
                                <label for="floatingInput">Harga Jual</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-master-barang mr-2">Kembali</button>
                <button type="submit" class="btn btn-submit-form btn-submit-form-master-barang">Simpan</button>
            </div>
        </div>
    </div>
</div>


<script>
    const csrfToken = '<?= csrf_token() ?>';
    var listBarang = [];
    var totalAmount = 0;

    <?php if (!empty($dataSalesKontrakDetail)) : ?>
        $('#potongan_harga').keyup();
        $('#komisi').keyup();
        <?php foreach ($dataSalesKontrakDetail as $s) : ?>
            listBarang.push({
                id_detail: "<?= $s['id_detail'] ?>",
                barang_master_sales_id: "<?= $s['barang_master_sales_id'] ?>",
                kode_barang: "<?= $s['kode_barang'] ?>",
                barang_name: "<?= $s['barang_name'] ?>",
                satuan_order_id: "<?= $s['satuan_order_id'] ?>",
                satuan_order_name: "<?= $s['satuan_order_name'] ?>",
                kemasan: "<?= $s['kemasan'] ?>",
                qty: "<?= floatval($s['qty']) ?>",
                harga: "<?= floatval($s['harga']) ?>",
                remark: "<?= $s['remark'] ?>",
                total: <?= floatval($s['total']) ?>,
                size: "<?= $s['size'] ?>"
            });
        <?php endforeach; ?>
        drawTable();

    <?php else: ?>
        changeStatus();
    <?php endif; ?>

    $(".due_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $(".shipment_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $('.icon-due-date').click(function() {
        $(".due_date").focus();
    });

    $('.icon-shipment-date').click(function() {
        $(".shipment_date").focus();
    });

    // CUSTOMER
    $('.customer_id').select2({
        placeholder: "Pilih Customer",
        theme: "bootstrap-5"
    })

    $('#sales_id').select2({
        placeholder: "Pilih Sales (Optional)",
        theme: "bootstrap-5",
        dropdownParent: $('#addCustomerModal')
    })

    // BARANG 
    $('.barang_master_sales_id').select2({
        placeholder: "Pilih Barang",
        theme: "bootstrap-5",
        dropdownParent: $(".detail-modal .modal-content"),
        allowClear: true
    });

    // TYPE BARANG
    $("#type_barang").select2({
        theme: "bootstrap-5",
        placeholder: 'Pilih Tipe Barang',
        allowClear: true,
        dropdownParent: $(".add-modal .modal-content")
    }).change(function() {
        let value = document.getElementById('generate_new_code').checked ? true : false;
        if (value) {
            generateCodeMasterBarang();
        }
    });


    // COUNTRY
    $('.country_id').select2({
        placeholder: "Pilih Negara",
        theme: "bootstrap-5",
        dropdownParent: $("#addCustomerModal")
    });

    // CURRENCY
    $('.currency').select2({
        placeholder: "Pilih Mata Uang",
        theme: "bootstrap-5",
    });

    // TIPE HARGA
    $('.tipe_harga').select2({
        placeholder: "Price Type",
        theme: "bootstrap-5",
    });

    // SATUAN ORDER
    $('.satuan_order_id').select2({
        placeholder: "Pilih Satuan Order",
        theme: "bootstrap-5",
        dropdownParent: $('.detail-modal')
    });

    // SATUAN
    $('.satuan_id').select2({
        placeholder: "Pilih Satuan",
        theme: "bootstrap-5",
    });

    $('.bank_id').select2({
        placeholder: "Pilih Bank (Opsional)",
        theme: "bootstrap-5",
    });


    // BARANG MASTER
    $('.barang_master_sales_id').select2({
        placeholder: "Pilih Barang",
        theme: "bootstrap-5",
        dropdownParent: $('.detail-modal')
    }).change(function() {
        var selected = $('.barang_master_sales_id option:selected');
        if (selected.val() != "") {
            $('.satuan_order_id').val(selected.data('satuan_id')).change();
            $('.harga').val(greatFormatRupiah(selected.data('harga_jual'))).keyup();
        }
    });

    // BROKER
    $('.print_out_broker').select2({
        placeholder: "Pilih Print Out Broker",
        theme: "bootstrap-5",
    })

    // DEPARTMENT
    $('.divisi_id').select2({
        placeholder: "Pilih Department",
        theme: "bootstrap-5",
    })

    $("#spesifikasi_id").select2({
        theme: "bootstrap-5",
        placeholder: 'Pilih Dari Master Barang',
        allowClear: true,
        dropdownParent: $(".add-modal .modal-content"),
    }).change(function() {
        var id = $('#spesifikasi_id option:selected').val();
        var selectedText = $('#spesifikasi_id option:selected').text();

        if (id != '') {
            $('#barang_name').val(selectedText);

        } else {
            $('#barang_name').val(null);

        }
    });

    //CSS SELECT2 FLOATING LABEL
    $('.bank_id,.print_out_broker,.barang_master_sales_id,.satuan_id,.satuan_order_id,.tipe_harga,.currency,.country_id,.barang_master_sales_id,.customer_id,.type_barang,#sales_id,#divisi_id,#spesifikasi_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.bank_id,.print_out_broker,.barang_master_sales_id,.satuan_id,.satuan_order_id,.tipe_harga,.currency,.country_id,.barang_master_sales_id,.customer_id,.type_barang,#sales_id,#divisi_id,#spesifikasi_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.bank_id,.satuan_id,#divisi_id,#spesifikasi_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.bank_id,.print_out_broker,.barang_master_sales_id,.satuan_id,.satuan_order_id,.tipe_harga,.currency,.country_id,.barang_master_sales_id,.customer_id,#sales_id,#divisi_id,#spesifikasi_id')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    // QTY KEYUP
    $('.qty,.harga').keyup(function() {
        var qty = parseFloat($('.qty').val()) || 0;
        var harga = destroyFormatRupiah($('.harga').val()) || 0;
        var total = qty * harga;
        $('.total').val(greatFormatRupiah(total.toFixed(2)));
    });

    // CUSTOMER 
    $('.btn-customer-add').click(function() {
        $('.name').val(null);
        $('.country_id').val(null).change();
        $('.address').val(null);
        $('#addCustomerModal').modal('show');
    });

    $('.btn-discard-customer').click(function() {
        $('#addCustomerModal').modal('hide');
    });

    // VALIDATOR SALES KONTRAK FORM
    var validator = $(".create-form").validate({
        rules: {
            sales_contract_no: {
                required: true
            },
            customer_id: {
                required: true
            },
            loading_port: {
                required: true
            },
            dicharge_port: {
                required: true
            },
            tolerance: {
                required: true
            },
            shipment_date: {
                required: true
            },
            currency: {
                required: true
            },
            tipe_harga: {
                required: true
            },
            print_out_broker: {
                required: true
            },
            divisi_id: {
                required: true
            },
            // special_instructions: {
            //     required: true
            // },
        },
        messages: {
            sales_contract_no: {
                required: "No sales kontrak wajib diisi"
            },
            customer_id: {
                required: "Customer wajib diisi"
            },
            loading_port: {
                required: "Loading port wajib diisi"
            },
            dicharge_port: {
                required: "Dicharge port wajib diisi"
            },
            tolerance: {
                required: "Tolerance wajib diisi"
            },
            shipment_date: {
                required: "Shipment date wajib diisi"
            },
            currency: {
                required: "Currency wajib diisi"
            },
            tipe_harga: {
                required: "Price type wajib diisi"
            },
            print_out_broker: {
                required: "Print out boker wajib diisi"
            },
            divisi_id: {
                required: "Pilih Departemen"
            },
            // documents_required: {
            //     required: "Dokumen wajib diisi"
            // },
            // special_instructions: {
            //     required: "Special instructions wajib diisi"
            // },
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

    // VALIDATOR DETAIL BARANG
    var validatorDetail = $(".detail-form").validate({
        rules: {
            barang_master_sales_id: {
                required: true
            },
            satuan_order_id: {
                required: true
            },
            qty: {
                required: true
            },
            harga: {
                required: true
            },
            // remark: {
            //     required: true
            // },
            total: {
                required: true
            }
        },
        messages: {
            barang_master_sales_id: {
                required: "Pilih barang"
            },
            satuan_order_id: {
                required: "Pilih satuan order"
            },
            qty: {
                required: "Qty wajib diisi"
            },
            harga: {
                required: "Harga wajib diisi"
            },
            // remark: {
            //     required: "Remark wajib diisi"
            // },
            total: {
                required: "Total wajib diisi"
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

    // VALIDATOR MASTER BARANG
    var validatorMasterBarang = $(".create-form-master-barang").validate({
        rules: {
            kode_barang: {
                required: true
            },
            barang_name: {
                required: true
            },
            type_barang: {
                required: true
            },
            // satuan_id: {
            //     required: true
            // },
            // harga_pokok: {
            //     required: true
            // },
            // harga_jual: {
            //     required: true
            // }
        },
        messages: {
            kode_barang: {
                required: "Kode barang wajib diisi"
            },
            barang_name: {
                required: "Nama barang wajib diisi"
            },
            type_barang: {
                required: "Tipe barang wajib diisi"
            },
            // satuan_id: {
            //     required: "Satuan wajib diisi"
            // },
            // harga_pokok: {
            //     required: "Harga pokok wajib diisi"
            // },
            // harga_jual: {
            //     required: "Harga jual wajib diisi"
            // }
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

    // VALIDATOR CUSTOMER INTERNASIONAL
    var validatorCustomer = $(".create-form-customer").validate({
        rules: {
            name: {
                required: true
            },
            country_id: {
                required: true
            },
        },
        messages: {
            name: {
                required: "Nama customer wajib diisi"
            },
            country_id: {
                required: "Pilih negara"
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

    $('.btn-submit-customer').click(function() {
        if ($('.create-form-customer').valid()) {
            const csrf = $(`[name="${csrfToken}"]`);
            const data = new FormData(document.querySelector(".create-form-customer"));
            Swal.fire({
                icon: 'question',
                title: 'Simpan Customer?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url("customer-ekspor/save"); ?>",
                        data: data,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            setLoading();
                        },
                        complete: function() {
                            stopLoading();
                        },
                        method: "POST",
                        dataType: "json",
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            csrf.val(response.token);
                            $("#addCustomerModal").modal("hide");
                            if (response.status) {
                                Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                    .then(() => {
                                        $('#addCustomerModal').modal('hide');

                                    })
                                getListCustomer();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                            }
                        },
                        onError: function(response) {
                            csrf.val(response.token);
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Gagal Disimpan, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    });
                }
            })

        }
    });

    $('.btn-submit-form-master-barang').click(function() {
        if ($('.create-form-master-barang').valid()) {
            const csrf = $(`[name="${csrfToken}"]`);
            let data = new FormData(document.querySelector(".create-form-master-barang"));
            let hargaPokok = destroyFormatRupiah($('#harga_pokok').val());
            let hargaJual = destroyFormatRupiah($('#harga_jual').val());
            let divisiId = $("#divisi_id").val();
            data.set('harga_jual', hargaJual);
            data.set('harga_pokok', hargaPokok);
            data.set('divisi_id', divisiId);

            Swal.fire({
                icon: 'question',
                title: 'Simpan Master Barang ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url("master-barang-internasional/save"); ?>",
                        data: data,
                        method: "POST",
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            setLoading();
                        },
                        complete: function() {
                            stopLoading();
                        },
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                    reverseButtons: true,
                                    confirmButtonText: 'Oke',
                                }).then((result) => {
                                    // jika sukses
                                    $(".detail-modal").modal("show")
                                    $('#addMasterBarangModal').modal('hide');
                                    // update list data barang
                                    getListMasterBarang();
                                })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                    cancelButtonColor: '#d33',
                                    reverseButtons: true,
                                    confirmButtonText: 'Oke',
                                })
                            }
                        }
                    });
                }
            })

        }
    });

    $('.btn-submit-detail').click(function() {
        if ($('.detail-form').valid()) {
            var id_detail = $('.id_detail').val();
            var barang_master_sales_id = $('.barang_master_sales_id option:selected').val();
            var kode_barang = $('.barang_master_sales_id option:selected').data('kode_barang');
            var barang_name = $('.barang_master_sales_id option:selected').data('barang_name');
            var satuan_order_id = $('.satuan_order_id option:selected').val();
            var satuan_order_name = $('.satuan_order_id option:selected').text();
            var kemasan = $('.kemasan').val();
            var qty = $('.qty').val();
            var harga = destroyFormatRupiah($('.harga').val());
            var remark = $('.remark').val();
            var total = (qty * harga);
            var size = $('.size').val();

            if (id_detail) {
                // UPDATE
                $.each(listBarang, function(i, v) {
                    if (v.id_detail === id_detail) {
                        listBarang[i].barang_master_sales_id = barang_master_sales_id;
                        listBarang[i].kode_barang = kode_barang;
                        listBarang[i].barang_name = barang_name;
                        listBarang[i].satuan_order_id = satuan_order_id;
                        listBarang[i].satuan_order_name = satuan_order_name;
                        listBarang[i].kemasan = kemasan;
                        listBarang[i].qty = qty;
                        listBarang[i].harga = harga;
                        listBarang[i].remark = remark;
                        listBarang[i].total = total;
                        listBarang[i].size = size;
                    }
                });
            } else {
                // CREATE
                // CEK DUPLIKASI
                var isAdd = false;
                for (var i = 0; i < listBarang.length; i++) {
                    if (listBarang[i].barang_master_sales_id == barang_master_sales_id) {
                        indexToRemove = i;
                        isAdd = true;
                        break;
                    }
                }

                if (isAdd) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Barang Sudah Ada',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        reverseButtons: true,
                        confirmButtonText: 'Oke',
                    })
                } else {
                    var id_detail = getID();
                    listBarang.push({
                        id_detail: id_detail,
                        barang_master_sales_id: barang_master_sales_id,
                        kode_barang: kode_barang,
                        barang_name: barang_name,
                        satuan_order_id: satuan_order_id,
                        satuan_order_name: satuan_order_name,
                        kemasan: kemasan,
                        qty: qty,
                        harga: harga,
                        remark: remark,
                        total: total,
                        size: size
                    });
                }
            }
            resetFormDetail();
            drawTable();
            $('.detail-modal').modal('hide');
        }
    });

    $('.btn-submit-parent').click(function() {
        if (listBarang.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'List Barang Belum Ada',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                reverseButtons: true,
                confirmButtonText: 'Oke',
            })
        } else {
            if ($('.create-form').valid()) {
                let id = $('.id').val();
                const csrf = $(`[name="${csrfToken}"]`);
                const listDataBarang = JSON.stringify(listBarang);
                let data = new FormData(document.querySelector(".create-form"));
                let potonganHarga = destroyFormatRupiah($('#potongan_harga').val());
                let komisi = destroyFormatRupiah($('#komisi').val());
                data.set('potongan_harga', potonganHarga);
                data.set('komisi', komisi);
                data.append("total_amount", totalAmount);
                data.append("listBarang", listDataBarang);

                if (id) {
                    // UPDATE
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
                            $.ajax({
                                url: "<?= base_url("sales-kontrak/update"); ?>",
                                data: data,
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                    setLoading();
                                },
                                complete: function() {
                                    stopLoading();
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
                                                window.location.href = "<?= base_url('sales-kontrak') ?>"
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                            confirmButtonText: 'Ok'
                                        });
                                    }
                                },
                            });
                        }
                    });
                } else {
                    // CREATE
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
                            $.ajax({
                                url: "<?= base_url("sales-kontrak/save"); ?>",
                                data: data,
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                    setLoading();
                                },
                                complete: function() {
                                    stopLoading();
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
                                                window.location.href = "<?= base_url('sales-kontrak') ?>"
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                            confirmButtonText: 'Ok'
                                        });
                                    }
                                },
                            });
                        }
                    });
                }
            }
        }
    })

    // MASTER BARANG
    $('.btn-barang-add').click(function() {

        $('#addMasterBarangModal').modal('show');
        $('.detail-modal').modal('hide');
        // reset form master barang
        validatorMasterBarang.resetForm();
        validatorMasterBarang.reset();
        $('#generate_new_code').attr('checked', true);
        generateCodeMasterBarang();
        $('.barang_name').val(null);
        $('.satuan_id').val(null).change();
        $('.harga_pokok').val(null);
        $('.harga_jual').val(null);
        getListMasterBarang()
        fetchBarangData();
    })

    $('.btn-discard-master-barang').click(function() {
        $(".detail-modal").modal("show")
        $('#addMasterBarangModal').modal('hide');
    });

    // MODAL DETAIL
    $('.btn-show-detail').click(function() {
        $('.detail-modal').modal('show');
        $('.title-detail-name').text('Tambah');
        validatorDetail.resetForm();
        validatorDetail.reset();
        resetFormDetail();
        getListMasterBarang()
    });

    $(".btn-hide-detail").click(function() {
        $(".detail-modal").modal("hide")
    })


    generateCodeMasterBarang();
    getListMasterBarang();

    function print(url) {
        window.open(url, "_blank");
    }

    function detailRow(id) {
        var item = null;
        for (var i = 0; i < listBarang.length; i++) {
            if (listBarang[i].id_detail == id) {
                item = listBarang[i];
                break;
            }
        }
        $('.id_detail').val(item.id_detail);
        $('.barang_master_sales_id').val(item.barang_master_sales_id).change();
        $('.satuan_order_id').val(item.satuan_order_id).change();
        $('.kemasan').val(item.kemasan);
        $('.qty').val(item.qty);
        $('.harga').val(greatFormatRupiah(item.harga));
        $('.remark').val(item.remark);
        $('.total').val(greatFormatRupiah(item.total.toFixed(2)));
        $('.size').val(item.size);
        $(".detail-modal").modal("show")
    }

    function deleteRow(id) {
        var indexToRemove = -1;
        for (var i = 0; i < listBarang.length; i++) {
            if (listBarang[i].id_detail == id) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listBarang.splice(indexToRemove, 1);
        }
        drawTable();
    }

    function drawTable() {
        $('.body-detail-table').empty();
        $('.tfoot').empty();
        var row = '';
        var no = 1;
        const table = $('.dataTable');
        if (listBarang.length === 0) {
            row += `
                    <tr>
                        <td colspan="5"></td>
                        <td><b>TOTAL</b></td>
                        <td><b>0.00</b></td>
                        <td><b>0.00</b></td>
                        <td><b>0.00</b></td>
                        <td></td>
                    </tr>
                `;
            $('.tfoot').append(row);
        } else {
            var totalQty = 0;
            var totalHarga = 0;
            var totalTotalHarga = 0;

            listBarang.map(item => {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align:center;">').text(no++));
                newRow.append($('<td>').text(item.kode_barang));
                newRow.append($('<td>').text(item.barang_name));
                newRow.append($('<td>').text(item.size));
                newRow.append($('<td>').text(item.satuan_order_name));
                newRow.append($('<td>').text(item.kemasan));
                newRow.append($('<td>').text(item.remark));
                newRow.append($('<td>').text(greatFormatRupiah(item.qty)));
                newRow.append($('<td>').text(greatFormatRupiah(item.harga)));
                newRow.append($('<td>').text(greatFormatRupiah(item.total.toFixed(2))));
                newRow.append($('<td>').html(
                    <?php if (!empty($dataSalesKontrak)) : ?> <?php if ($dataSalesKontrak['status_posting']) : ?> `-`
                        <?php else : ?> `
                        <button class="btn btn-warning posting-spp mr-1" onclick="detailRow('${item.id_detail}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button class="btn btn-danger" onclick="deleteRow('${item.id_detail}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                        <?php endif; ?>

                    <?php else : ?> `
                        <button class="btn btn-warning posting-spp mr-1" onclick="detailRow('${item.id_detail}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button class="btn btn-danger" onclick="deleteRow('${item.id_detail}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                    <?php endif; ?>
                ));

                totalQty += parseFloat(item.qty);
                totalHarga += destroyFormatRupiah(item.harga);
                totalTotalHarga += destroyFormatRupiah(item.total);

                table.find('tbody').append(newRow);
            });
            $('.body-detail-table').append(row);
            table.find('tfoot').empty();
            var newRow = $('<tr>');
            newRow.append($('<td colspan="6"></td>'));
            newRow.append($('<td><b>TOTAL</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalQty) + '</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalHarga.toFixed(2)) + '</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalTotalHarga.toFixed(2)) + '</b></td>'));
            newRow.append($('<td></td>'));
            table.find('tfoot').append(newRow);

            totalAmount = totalTotalHarga;
        }
    }

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $(".sales_contract_no").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("sales-kontrak/get-sales-kontrak-no"); ?>`,
                method: "GET",
                data: {},
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".sales_contract_no").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".sales_contract_no").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".sales_contract_no").val("");
                    }
                }
            })
        } else {
            $(".sales_contract_no").attr("readonly", false);
            $(".sales_contract_no").val("");
        }

    }

    function getListCustomer() {
        $.ajax({
            url: `<?= base_url('sales-kontrak/customer'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {},
            dataType: "json",
            success: function(res) {
                $(".customer_id").empty()
                $(".customer_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".customer_id").append(`<option value="${item.id}">(${item.kode}) ${item.name}</option>`)
                })
                $(".customer_id").val();
            }
        });
    }

    function getListMasterBarang() {
        // Get the selected divisi_id value
        const csrf = $(`[name="${csrfToken}"]`);
        const divisiId = $("#divisi_id").val();

        $.ajax({
            url: `<?= base_url('sales-kontrak/master-barang'); ?>`,
            method: "POST",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                // setLoading();
            },
            complete: function() {
                // stopLoading();
            },
            dataType: "json",
            success: function(res) {
                if (res.data && res.data.length > 0) {
                    $(".barang_master_sales_id").empty();
                    $(".barang_master_sales_id").append(`<option value=""></option>`);

                    res.data.forEach(function(item) {
                        $(".barang_master_sales_id").append(
                            `<option value="${item.id}" 
                                data-harga_jual="${item.harga_jual}" 
                                data-satuan_id="${item.satuan_id}" 
                                data-kode_barang="${item.kode_barang}" 
                                data-barang_name="${item.barang_name}">
                                (${item.kode_barang}) ${item.barang_name}
                            </option>`
                        );
                    });

                    $(".barang_master_sales_id").val('').trigger('change');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching master barang:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat data barang. Silakan coba lagi.',
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    function generateCodeMasterBarang() {
        let csrfToken = '<?= csrf_token() ?>';
        let value = document.getElementById('generate_new_code').checked ? true : false;
        let csrf = $(`[name="${csrfToken}"]`);
        let type_barang = $('#type_barang option:selected').val();
        if (value) {
            $("input[name='kode_barang']").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("master-barang-internasional/generate-new-code"); ?>`,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                complete: function() {},
                data: {
                    type_barang: type_barang
                },
                method: "POST",
                success: function(res) {
                    csrf.val(res.token);
                    $("input[name='kode_barang']").attr("readonly", true);
                    $("input[name='kode_barang']").val(res.codeNew);

                }
            })
        } else {
            $("input[name='kode_barang']").attr("readonly", false);
            $("input[name='kode_barang']").val("");
        }
    }


    function resetFormDetail() {
        $('.id_detail').val(null);
        $('.barang_master_sales_id').val(null).change();
        $('.kemasan').val(null);
        $('.qty').val(null);
        $('.remark').val(null);
        $('.total').val(null);
        $('#satuan_order_id').val(null).change();
    }

    function capitalize() {
        var input = document.getElementById("kemasan");
        input.value = input.value.toUpperCase();
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

    function getID() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let randomString = '';

        for (let i = 0; i < 10; i++) {
            randomString += characters.charAt(Math.floor(Math.random() * characters.length));
        }

        return randomString;
    }

    function updateStatusPosting(status) {
        Swal.fire({
            icon: 'question',
            title: status == '0' ? 'Un Posting Sales Kontrak ?' : 'Posting Sales Kontrak ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: status == '0' ? 'Unposting' : 'Posting',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("sales-kontrak/update-status"); ?>",
                    data: {
                        id: "<?= !empty($dataSalesKontrak) ? encrypt($dataSalesKontrak['id']) : '0' ?>",
                        status: status
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
                                })
                                .then(() => {
                                    location.reload()
                                })
                        }
                    },

                });
            }
        })
    }

    $(".delete-parent").click(function() {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                let id = $(".id").val();
                $.ajax({
                    url: "<?= base_url("sales-kontrak/delete"); ?>",
                    data: {
                        id: id,
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
                            stopLoading()
                            Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    window.location.href = "<?= base_url("sales-kontrak"); ?>"
                                })
                        }
                    },

                });
            }
        })
    })

    //handle print
    function print(url) {
        window.open(url, "_blank");
    }

    // // Function to fetch barang data
    function fetchBarangData() {
        let csrfToken = '<?= csrf_token() ?>';
        let csrf = $(`[name="${csrfToken}"]`);

        $.ajax({
            url: "<?= base_url('master-barang-internasional/get-barang-jadi-master') ?>",
            method: 'POST',
            data: {
                type_barang: $('#type_barang option:selected').val()
            },
            dataType: 'json',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            success: function(response) {
                var typeBarang = $('#type_barang option:selected').val();
                var spesifikasiIdSelect = $("select[name='spesifikasi_id']");
                spesifikasiIdSelect.empty();

                var emptyOption = $("<option></option>")
                    .attr("value", "")
                    .text("Pilih Dari Master Barang");
                // Replace with select element
                if (typeBarang == 'kemasan') {
                    spesifikasiIdSelect.append(emptyOption);
                    $.each(response.data, function(index, data) {
                        var option = $("<option></option>")
                            .attr("value", data.id)
                            .text(data.barang_name_master);
                        spesifikasiIdSelect.append(option);
                    });
                } else {
                    spesifikasiIdSelect.append(emptyOption);
                    $.each(response.data, function(index, data) {
                        var option = $("<option></option>")
                            .attr("value", data.id)
                            .text(data.barang_name_master + " - " + data.spesifikasi);
                        spesifikasiIdSelect.append(option);
                    });
                }

            },
        });
    }
</script>
<?= $this->endSection(); ?>