<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>
<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tambah PO Lokal Bahan Baku</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("po-lokal-bahan-baku"); ?>">
                Batal
            </a>
            <?php if (!empty($dataPOLokal)) : ?>

                <?php if ($dataPOLokal->is_posted === "0") : ?>
                    <?php if (can('Pembelian', 'PO Lokal BB', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right">
                            Hapus
                        </button>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (can('Pembelian', 'PO Lokal BB', 'p')) : ?>
                    <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("po-lokal-bahan-baku/print/"); ?><?= encrypt($dataPOLokal->id) ?>')">
                        Print
                    </button>
                <?php endif; ?>

                <?php if ($dataPOLokal->is_posted === "0") : ?>
                    <?php if (can('Pembelian', 'PO Lokal BB', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-po" data-status_posting="1">
                            Posting
                        </button>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($dataPOLokal->is_posted === "1") : ?>
                    <?php if (can('Pembelian', 'PO Lokal BB', 'ua') && $dataPOLokal->status_penerimaan === "0") : ?>
                        <button class="btn btn-success posting-spp float-right posting-po" data-status_posting="0">
                            Un Posting
                        </button>
                    <?php endif; ?>
                    <?php if ($dataPOLokal->status_penerimaan === "0") : ?>
                        <button class="btn btn-hapus close-parent float-right">
                            Close PO
                        </button>
                    <?php endif; ?>
                <?php endif; ?>

            <?php endif; ?>

            <?php if (!empty($dataPOLokal)) : ?>
                <?php if ($dataPOLokal->is_posted === "0") : ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php else : ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data PO</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($dataPOLokal) ? encrypt($dataPOLokal->id) : ""; ?>" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-control input-picker po_date" id="po_date" name="po_date" placeholder="Tanggal Dibuat" value="<?= !empty($dataPOLokal) ? ($dataPOLokal->po_date ? date("d/m/Y", strtotime($dataPOLokal->po_date)) : "") : $today; ?>">
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> type="text" class="form-control po_no" id="po_no" name="po_no" placeholder="No. PO" value="<?= !empty($dataPOLokal) ? $dataPOLokal->po_no : ""; ?>">
                                    <label for="floatingInput">No. PO</label>
                                </div>
                                <div style="<?= !empty($dataPOLokal) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-select supplier_id" id="supplier_id" name="supplier_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php
                                if (!empty($dataSupplier)) : ?>
                                    <?php foreach ($dataSupplier as $supplier) : ?>
                                        <option <?= !empty($dataPOLokal) ? ($dataPOLokal->supplier_id === $supplier->id ? "selected" : "") : ""; ?> value="<?= $supplier->id; ?>" data-name="<?= $supplier->name; ?>"><?= strtoupper($supplier->name); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput">Supplier</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php
                                if (!empty($dataDivisi)) {
                                    foreach ($dataDivisi as $d) {
                                ?>
                                        <option value="<?= $d["id"]; ?>" <?= !empty($dataPOLokal) ? ($dataPOLokal->divisi_id === $d["id"] ? "selected" : "") : ""; ?>><?= strtoupper($d["divisi"]); ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating " style="height: 50px;">
                            <select <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-select spp_id" id="spp_id" name="spp_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($dataListSPP)) : ?>
                                    <?php foreach ($dataListSPP as $d) : ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['spp_no'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput">SPP (Opsional)</label>
                        </div>
                        <small class="mb-3"><i><?= !empty($dataPOLokal) ? 'Nomor SPP: ' . ($dataSPP != null ? $dataSPP['spp_no'] : '-')  : '' ?></i></small>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-select barang_id" id="barang_id" name="barang_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php
                                if (!empty($dataBarang)) {
                                    foreach ($dataBarang as $b) {
                                ?>
                                        <option value="<?= $b["id"]; ?>" <?= !empty($dataPOLokal) ? ($dataPOLokal->barang_id === $b["id"] ? "selected" : "") : ""; ?>><?= strtoupper($b["barang_name"]); ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Barang Bahan Baku</label>
                        </div>
                    </div>


                </div>
                <div class="row mt-2">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-select pph" id="pph" name="pph" aria-label="Floating label select example">
                                <option <?= !empty($dataPOLokal) ? ($dataPOLokal->pph === "None" ? "selected" : "") : ""; ?> value="None">Pph tidak ditanggung</option>
                                <option <?= !empty($dataPOLokal) ? ($dataPOLokal->pph === "Supplier" ? "selected" : "") : ""; ?> value="Supplier">Pph ditanggung supplier</option>
                                <option <?= !empty($dataPOLokal) ? ($dataPOLokal->pph === "Company" ? "selected" : "") : ""; ?> value="Company">Pph ditanggung perusahaan</option>
                            </select>
                            <label for="floatingInput">PPH</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="<?= !empty($dataPOLokal) ? $dataPOLokal->cong_sebenarnya : ""; ?>" <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> type="number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control cong_sebenarnya" name="cong_sebenarnya" id="cong_sebenarnya" placeholder="Cong Sebenarnya (Opsional)">
                            <label for="floatingInput">Cong Sebenarnya (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="<?= !empty($dataPOLokal) ? $dataPOLokal->cong_batasan : ""; ?>" <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> type="number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control cong_batasan" name="cong_batasan" id="cong_batasan" placeholder="Cong Batasan (Opsional)">
                            <label for="floatingInput">Cong Batasan (Opsional)</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="<?= !empty($dataPOLokal) ? $dataPOLokal->subsidi_langsung : ""; ?>" <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> type="number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control subsidi_langsung" name="subsidi_langsung" id="subsidi_langsung" placeholder="Subsidi Langsung (Opsional)">
                            <label for="floatingInput">Tambahan Langsung (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">

                    </div>
                    <div class="col-md-4">

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold modal-sub-title" style="font-size: 14px;">Buatkan LPB Otomatis</label>
                        <div class="form-control border-0 custom-toggle-switch" style="margin-top: -15px;">
                            <div class="form-check form-switch form-switch-lg">
                                <input <?= !empty($dataPOLokal) ? ($dataPOLokal->warehouse_id == null ? "checked" : "") : 'checked' ?> class="form-check-input" type="checkbox" name="lpb_otomatis" id="lpb_otomatis">
                                <label class="form-check-label" for="lpb_otomatis"></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3 form-lpb" style="height: 50px;">
                            <select class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataWarehouse as $warehouse) : ?>
                                    <option <?= !empty($dataPOLokal) ? ($dataPOLokal->warehouse_id == $warehouse['id'] ? 'checked' : "") : '' ?> value="<?= $warehouse["id"]; ?>" <?= !empty($dataPOLokal) ? ($dataPOLokal->warehouse_id === $warehouse["id"] ? "selected" : "") : ""; ?>><?= $warehouse["warehouse_name"]; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Warehouse</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-lpb" style="height: 50px;">
                            <select class="form-select bc_type" id="bc_type" name="bc_type" aria-label="Floating label select example">
                                <option value="">Pilih Dokumen Pabean</option>
                                <?php foreach ($dataBCType as $aju) : ?>
                                    <option value="<?= $aju["id"]; ?>" <?= (!empty($dataPOLokal) ? ($aju["id"] === $dataPOLokal->bc_type ? "selected" : "") : ""); ?>><?= $aju["value"]; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Jenis Dokumen Pabean (Opsional)</label>
                        </div>
                        <small class="mb-3 form-lpb"><i>Kosongkan jika PO tidak memerlukan dokumen pabean</i></small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3 form-lpb" style="height: 50px;">
                            <select class="form-select kemasan_id" id="kemasan_id" name="kemasan_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataKemasan as $kemasan) : ?>
                                    <option <?= !empty($dataPOLokal) ? ($dataPOLokal->kemasan_id == $kemasan['id'] ? 'checked' : "") : '' ?> value="<?= $kemasan["id"]; ?>" <?= !empty($dataPOLokal) ? ($dataPOLokal->kemasan_id === $kemasan["id"] ? "selected" : "") : ""; ?>><?= $kemasan["name"]; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Jenis Kemasan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3 form-lpb" style="height: 50px;">
                            <input autocomplete="one-time-code" value="<?= !empty($dataPOLokal) ? $dataPOLokal->jumlah_kemasan : ""; ?>" <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> type="number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control jumlah_kemasan" name="jumlah_kemasan" id="jumlah_kemasan" placeholder="Jumlah Kemasan">
                            <label for="floatingInput">Jumlah Kemasan</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3 form-lpb" style="height: 50px;">
                            <input value="<?= !empty($dataPOLokal) ? $dataPOLokal->kemasan_tambahan : ""; ?>" <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> autocomplete="one-time-code" type="kemasan_tambahan" class="form-control kemasan_tambahan" name="kemasan_tambahan" id="kemasan_tambahan" placeholder="Kemasan Tambahan">
                            <label for="floatingInput">Keterangan Kemasan (Opsional)</label>
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
            <form class="detail-form" role="form" method="POST" enctype="multipart/form-data" style="<?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? "display: none;" : "") : ""; ?>">
                <input type="hidden" name="id_detail" class="id_detail" id="id_detail">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select onchange="changeSpesifikasi()" class="form-select spesifikasi" name="spesifikasi" id="spesifikasi" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php
                                if (!empty($dataSpesifikasi)) {
                                    foreach ($dataSpesifikasi as $spesifikasi) {
                                ?>
                                        <option data-spesifikasi_id="<?= $spesifikasi['spesifikasi_id'] ?>" data-nama_satuan="<?= $spesifikasi['nama_satuan'] ?>" data-kode_satuan="<?= $spesifikasi['kode_satuan'] ?>" data-satuan_id="<?= $spesifikasi['satuan_id'] ?>" data-umum="<?= $spesifikasi['harga_umum'] ?>" data-harian="<?= $spesifikasi['harga_harian'] ?>" data-bulanan="<?= $spesifikasi['harga_bulanan'] ?>" value="<?= $spesifikasi["id"]; ?>"><?= $spesifikasi["spesifikasi"]; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Spesifikasi</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="satuan" class="form-control satuan" name="satuan" id="satuan" placeholder="Satuan Barang" readonly>
                            <label for="floatingInput">Satuan Barang</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="number" class="form-control harga" name="harga" id="harga" placeholder="Harga Umum">
                            <label for="floatingInput">Harga Umum</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="number" class="form-control daily_price" name="daily_price" id="daily_price" placeholder="Harga Harian">
                            <label for="floatingInput">Harga Harian</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="number" class="form-control monthly_price" name="monthly_price" id="monthly_price" placeholder="Harga Bulanan">
                            <label for="floatingInput">Harga Bulanan</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="number" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                            <label for="floatingInput">QTY</label>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" readonly="true" class="form-control total" name="total" id="total" placeholder="Total">
                            <label for="floatingInput">Total Harga</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control peti" name="peti" id="peti" placeholder="Peti">
                            <label for="floatingInput">Peti / Tong</label>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select quality" name="quality" id="quality" aria-label="Floating label select example">
                                <option value="Baik">Baik</option>
                                <option value="Jelek">Jelek</option>
                            </select>
                            <label for="floatingInput">Kualitas</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <textarea autocomplete="one-time-code" class="form-control keterangan text-area-all" name="keterangan" id="keterangan" placeholder="Keterangan (Opsional)"></textarea>
                            <label for="floatingInput">Keterangan (Opsional)</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-subtitle-modal" style="<?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? "display: none;" : "") : ""; ?>">
                <div class="row mt-3">
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-add btn-block float-right btn-submit-detail">
                            <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                        </button>
                        <button style="border-color: #e7323a !important; background-color: #e7323a !important; margin-right: 10px !important;" class="btn btn-add btn-block float-right" onclick="resetDetailForm()">
                            <i class="fa-solid fa-rotate-right mr-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Spesifikasi</th>
                                <th>Satuan</th>
                                <th>Umum</th>
                                <th>Harian</th>
                                <th>Bulanan</th>
                                <th>QTY</th>
                                <th>Peti</th>
                                <th>Kualitas</th>
                                <th style="<?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? "display: none;" : "") : ""; ?>">Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">
                        </tbody>
                        <tfoot class="foot-detail-table" id="foot-detail-table">
                            <tr>
                                <td colspan="2"></td>
                                <td><b>TOTAL</b></td>
                                <td><b>0.00</b></td>
                                <td><b>0.00</b></td>
                                <td><b>0.00</b></td>
                                <td><b>0.00</b></td>
                                <td colspan="2"></td>
                                <td style="<?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? "display: none;" : "") : ""; ?>"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($dataPOLokal)) : ?>
    <?php if ($dataPOLokal->warehouse_id == 0) : ?>
        <script>
            $('#lpb_otomatis').attr('checked', false);
            $('.form-lpb').hide();
        </script>
    <?php else : ?>
        <script>
            $('#lpb_otomatis').attr('checked', true);
        </script>
    <?php endif ?>
    <?php if ($dataPOLokal->is_posted === "1") : ?>
        <script>
            $('#warehouse_id').attr('disabled', true);
            $('#kemasan_id').attr('disabled', true);
            $('#bc_type').attr('disabled', true);
            $('#lpb_otomatis').attr('disabled', true);
        </script>
    <?php endif; ?>

<?php else : ?>
    <script>
        $(document).ready(function() {
            changeStatus();
        });
    </script>
<?php endif; ?>



<script>
    const csrfToken = '<?= csrf_token() ?>';
    let list_items = [];
    let total = 0;

    var validator_detail = $(".detail-form").validate({
        rules: {
            spesifikasi: {
                required: true
            },
            bagian: {
                required: true
            },
            satuan: {
                required: true
            },
            peti: {
                required: true
            },
            quality: {
                required: true
            },
            qty: {
                required: true
            },
            harga: {
                required: true
            },
            daily_price: {
                required: true
            },
            monthly_price: {
                required: true
            }
        },
        messages: {
            spesifikasi: {
                required: "Spesifikasi wajib diisi"
            },
            bagian: {
                required: "Bagian wajib diisi"
            },
            satuan: {
                required: "Satuan wajib diisi"
            },
            peti: {
                required: "Peti wajib diisi"
            },
            quality: {
                required: "Kualitas wajib diisi"
            },
            qty: {
                required: "Qty wajib diisi"
            },
            harga: {
                required: "Harga Umum wajib diisi"
            },
            daily_price: {
                required: "Harga Harian wajib diisi"
            },
            monthly_price: {
                required: "Harga Bulanan wajib diisi"
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

    const changeSpesifikasi = function() {
        if ($(".spesifikasi option:selected").val()) {
            let nama_satuan = $(".spesifikasi option:selected").data("nama_satuan") ? $(".spesifikasi option:selected").data("nama_satuan") : "";
            let kode_satuan = $(".spesifikasi option:selected").data("kode_satuan") ? $(".spesifikasi option:selected").data("kode_satuan") : "";
            let satuan_id = $(".spesifikasi option:selected").data("satuan_id") ? $(".spesifikasi option:selected").data("satuan_id") : "";
            let umum = $(".spesifikasi option:selected").data("umum") ? $(".spesifikasi option:selected").data("umum") : "";
            let harian = $(".spesifikasi option:selected").data("harian") ? $(".spesifikasi option:selected").data("harian") : "";
            let bulanan = $(".spesifikasi option:selected").data("bulanan") ? $(".spesifikasi option:selected").data("bulanan") : "";
            let satuan = $(".spesifikasi option:selected").data("wq") ? $(".spesifikasi option:selected").data("satuan") : "";

            $(".satuan").val(nama_satuan);
            $(".satuan").attr("satuan_id", satuan_id);
            $(".satuan").attr("nama_satuan", nama_satuan);
            $(".satuan").attr("kode_satuan", kode_satuan);
            $(".harga").val(parseInt(umum.toString().replaceAll(",", "")));
            $(".daily_price").val(parseInt(harian.toString().replaceAll(",", "")));
            $(".monthly_price").val(parseInt(bulanan.toString().replaceAll(",", "")));

            $(".qty").val("");
            $(".total").val("");
        } else {
            $(".satuan").val("");
            $(".satuan").attr("satuan_id", "");
            $(".satuan").attr("nama_satuan", "");
            $(".harga").val("");
            $(".daily_price").val("");
            $(".monthly_price").val("");
            $(".qty").val("");
            $(".total").val("");
        }

    }

    $(document).ready(function() {
        //
        $(".po_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        // WAREHOUSE
        $('.warehouse_id').select2({
            placeholder: "Pilih Warehouse (Departemen Required)",
            theme: "bootstrap-5"
        });

        // BC Type
        // $('.bc_type').select2({
        //     placeholder: "Pilih Dokumen Pabean",
        //     theme: "bootstrap-5"
        // })

        // JENIS KEMASAN
        $('.kemasan_id').select2({
            placeholder: "Pilih Jenis Kemasan",
            theme: "bootstrap-5"
        })

        $('.spp_id').select2({
            placeholder: "Pilih Nomor SPP",
            theme: "bootstrap-5",
            allowClear: true
        }).change(function() {
            var supplier_id = $('.supplier_id').val();
            var spp_id = $('.spp_id').val();
            // reset list
            list_items = [];
            drawTable();

            if (supplier_id !== '') {
                getDetailSPP();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: "Pilih nama supplier",
                    confirmButtonColor: '#4e73df',
                });
            }
        });

        // COMPANY ID
        $('.divisi_id').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5"
        }).change(function() {
            // GET SPP
            getListSPP();
            // GET DEPARTEMEN
            getListDepartemen();
            var spp = $(".spp_id").val();
            list_items = [];
            drawTable();
            $('.barang_id').val(null).change();
            $(".barang_id").attr("disabled", false);
        });

        // BARANG ID
        $('.barang_id').select2({
            placeholder: "Pilih Bahan Baku",
            theme: "bootstrap-5"
        }).change(function() {

        });

        // SPESIFIKASI
        $('.spesifikasi').select2({
            placeholder: "Pilih Spesifikasi (Supplier & Barang Bahan Baku required)",
            theme: "bootstrap-5",
            allowClear: true
        })

        // SUPPLIER
        $('.supplier_id').select2({
            placeholder: "Pilih Supplier",
            theme: "bootstrap-5"
        }).change(function() {
            list_items = [];
            drawTable();
            getDetailSPP();
        });

        // BAGIAN
        $('.bagian').select2({
            placeholder: "Pilih Bagian",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.form-select')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.form-select')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.form-select')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $('.icon-po-date').click(function() {
            $(".po_date").focus();
        });

        $('#lpb_otomatis').click(function() {
            var isChecked = $(this).prop('checked');
            if (isChecked) {
                $('.form-lpb').show();
            } else {
                $('#warehouse_id').val("");
                $('#bc_type').val("");
                $("#kemasan_id").val(null).change();
                $('#jumlah_kemasan').val(null);
                $('#kemasan_tambahan').val(null);

                $('#warehouse_id').change();
                $('#bc_type').change();
                $('.form-lpb').hide();
            }

        });

        var validator = $(".create-form").validate({
            rules: {
                po_date: {
                    required: true
                },
                supplier_id: {
                    required: true
                },
                barang_id: {
                    required: true
                },
                divisi_id: {
                    required: true
                },
                po_no: {
                    required: true
                },
                company_id: {
                    required: true
                },
                pph: {
                    required: true
                },
            },
            messages: {
                po_date: {
                    required: "Tanggal Dibuat wajib diisi"
                },
                supplier_id: {
                    required: "Supplier wajib diisi"
                },
                barang_id: {
                    required: "Barang wajib diisi"
                },
                po_no: {
                    required: "No PO wajib diisi"
                },
                company_id: {
                    required: "Company wajib diisi"
                },
                divisi_id: {
                    required: "Departemen wajib diisi"
                },
                pph: {
                    required: "PPH wajib diisi"
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

        $(".barang_id,.supplier_id").change(function() {
            $.ajax({
                url: `<?= base_url("po-lokal-bahan-baku/get-spesifikasi-barang-supplier"); ?>`,
                method: "GET",
                beforeSend: function() {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                data: {
                    barang_id: $(".barang_id option:selected").val(),
                    supplier_id: $(".supplier_id option:selected").val()
                },
                dataType: "json",
                success: function(res) {
                    $(".spesifikasi").empty();

                    $(".spesifikasi").append(`<option data-nama_satuan="" data-kode_satuan="" data-satuan_id="" data-umum="" data-harian="" data-bulanan="" value=""></option>`);

                    res.data.forEach(function(item) {
                        $(".spesifikasi").append(`<option
                        data-spesifikasi_id="${item.spesifikasi_id}" 
                        data-nama_satuan="${item.nama_satuan}" 
                        data-kode_satuan="${item.kode_satuan}" 
                        data-satuan_id="${item.satuan_id}" 
                        data-umum="${Number(item.harga_umum).toLocaleString(undefined, {minimumFractionDigits: 2, maximumSignificantDigits: 2})}" 
                        data-harian=" ${Number(item.harga_harian).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}" 
                        data-bulanan=" ${Number(item.harga_bulanan).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}" 
                        value="${item.id}">
                        ${item.spesifikasi}
                        </option>`);
                    })

                    $(".spesifikasi").val("").change();

                    drawTable();
                }
            })
        })

        $(".qty, .harga, .daily_price, .monthly_price").keyup(function() {
            var qty = $(".qty").val() ? Number($(".qty").val()) : 0;
            var gabungan_harga = Number($(".harga").val()) + Number($(".daily_price").val()) + Number($(".monthly_price").val());
            let total = (gabungan_harga * qty);
            $(".total").val(formatRupiah(total));
        })

        $(document).on('click', '.edit-table-detail', function(evt) {
            var id_detail = $(this).data("id");
            for (let i = 0; i < list_items.length; i++) {
                if (list_items[i].id_detail === id_detail) {
                    $(".id_detail").val(list_items[i].id_detail);
                    $(".spesifikasi").val(list_items[i].supplier_harga_id).change();
                    $(".harga").val(list_items[i].harga);
                    $(".daily_price").val(list_items[i].daily_price);
                    $(".monthly_price").val(list_items[i].monthly_price).keyup();
                    $(".qty").val(list_items[i].qty).keyup();
                    $(".peti").val(list_items[i].peti);
                    $(".quality").val(list_items[i].quality).change();
                    $(".keterangan").val(list_items[i].keterangan);
                    break;
                }
            }

        })

        $(".btn-submit-parent").click(function() {
            $(".detail-modal").modal("hide")

            // CHECK IF NO BARANG
            if (list_items.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Barang Tidak Boleh Kosong",
                    confirmButtonColor: '#4e73df',
                })
            } else {
                // validate input
                let validate_item = false;
                let lpb_otomatis = true;
                let kemasan_id = true;
                let jumlah_kemasan = true;

                if (list_items.length === 0) {
                    validate_item = true;
                }

                // validate if lpb otomatis
                if ($('#lpb_otomatis').prop('checked')) {
                    if ($('#warehouse_id').val() == "") {
                        lpb_otomatis = false;
                    } else if ($('#kemasan_id').val() == "") {
                        kemasan_id = false;
                    } else if ($('#jumlah_kemasan').val() == "" || $('#jumlah_kemasan').val() == "0") {
                        jumlah_kemasan = false;
                    }
                }

                if (!lpb_otomatis) {
                    Swal.fire({
                        icon: 'error',
                        title: "Lokasi warehouse wajib diisi",
                        confirmButtonColor: '#4e73df',
                    })
                } else if (!kemasan_id) {
                    Swal.fire({
                        icon: 'error',
                        title: "Jenis kemasan wajib diisi",
                        confirmButtonColor: '#4e73df',
                    })
                } else if (!jumlah_kemasan) {
                    Swal.fire({
                        icon: 'error',
                        title: "Jumlah kemasan wajib diisi",
                        confirmButtonColor: '#4e73df',
                    })
                } else if (validate_item) {
                    Swal.fire({
                        icon: 'error',
                        title: "Barang Tidak Boleh Kosong",
                        confirmButtonColor: '#4e73df',
                    })
                } else {
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
                                let data = new FormData(document.querySelector(".create-form"));
                                data.append("items", JSON.stringify(list_items))
                                data.append("barang_id", $('.barang_id').val());
                                data.append("total", total);
                                let id = $(".id").val();

                                // UPDATE
                                if (id) {
                                    $.ajax({
                                        url: "<?= base_url("po-lokal-bahan-baku/update"); ?>",
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
                                            if (response.status) {
                                                Swal.fire({
                                                        icon: 'success',
                                                        title: response.message,
                                                        confirmButtonColor: '#4e73df',
                                                    })
                                                    .then(() => {
                                                        location.reload();
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

                                    });
                                }
                                // CREATE
                                else {
                                    $.ajax({
                                        url: "<?= base_url("po-lokal-bahan-baku/save"); ?>",
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
                                            if (response.status) {
                                                Swal.fire({
                                                        icon: 'success',
                                                        title: response.message,
                                                        confirmButtonColor: '#4e73df',
                                                    })
                                                    .then(() => {
                                                        window.open('<?= base_url("po-lokal-bahan-baku/print") ?>/' + response.id, "_blank")
                                                        window.location.href = "<?= base_url("po-lokal-bahan-baku/id"); ?>/" + response.id;
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

                                    });
                                }
                            }
                        })
                    }
                }
            }
        })

        $(".posting-po").click(function() {
            // validate input
            let validate_item = false;

            if (list_items.length === 0) {
                validate_item = true;
            }

            if (validate_item) {
                Swal.fire({
                    icon: 'error',
                    title: "Barang Tidak Boleh Kosong",
                    confirmButtonColor: '#4e73df',
                })
            } else {
                var status_posting = $(this).data('status_posting');
                Swal.fire({
                    icon: 'question',
                    title: status_posting == "1" ? "Posting PO ?" : "Unposting PO ?",
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: status_posting == '0' ? 'Unposting' : 'Posting',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const csrf = $(`[name="${csrfToken}"]`);
                        $.ajax({
                            url: "<?= base_url("po-lokal-bahan-baku/update-status"); ?>",
                            data: {
                                id: $(".id").val(),
                                status_posting: status_posting
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
                                            location.reload();
                                        })
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
                })
            }
        });

        $(".close-parent").click(function() {
            Swal.fire({
                icon: 'question',
                title: "Close PO ?",
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Iya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    $.ajax({
                        url: "<?= base_url("po-lokal-bahan-baku/close-po"); ?>",
                        data: {
                            id: $(".id").val(),
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
                                        location.reload();
                                    })
                            }
                        },

                    });
                }
            })
        });

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
                        url: "<?= base_url("po-lokal-bahan-baku/delete"); ?>",
                        data: {
                            id: id
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
                                        window.location.href = "<?= base_url("po-lokal-bahan-baku"); ?>"
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
            let id_detail = $('.id_detail').val();
            let supplier_harga_id = $(".spesifikasi option:selected").val();
            let spesifikasi_id = $(".spesifikasi option:selected").data("spesifikasi_id");
            let spesifikasi_name = $(".spesifikasi option:selected").text().trim();
            let satuan_id = $(".spesifikasi option:selected").data("satuan_id");
            let kode_satuan = $(".spesifikasi option:selected").data("kode_satuan");
            let peti = $(".peti").val()
            let quality = $(".quality").val()
            let harga = $(".harga").val()
            let daily_price = $(".daily_price").val()
            let qty = $(".qty").val()
            let total = $(".total").val()
            let monthly_price = $(".monthly_price").val()
            let keterangan = $(".keterangan").val()
            let validate_same = false;

            if ($(".detail-form").valid()) {
                list_items.map((item, index) => {
                    if (item.supplier_harga_id === supplier_harga_id && id_detail === "") {
                        validate_same = true;
                    }
                });

                if (validate_same) {
                    Swal.fire({
                        icon: 'error',
                        title: "Spesifikasi Sudah Ada",
                        confirmButtonColor: '#4e73df',
                    })
                } else {
                    if (id_detail) {
                        list_items.map((item, index) => {
                            //UPDATE
                            if (item.id_detail === id_detail) {
                                list_items[index].supplier_harga_id = supplier_harga_id;
                                list_items[index].spesifikasi_id = spesifikasi_id;
                                list_items[index].nama_spesifikasi = spesifikasi_name;
                                list_items[index].satuan_id = satuan_id;
                                list_items[index].kode_satuan = kode_satuan;
                                list_items[index].peti = peti;
                                list_items[index].quality = quality;
                                list_items[index].harga = harga;
                                list_items[index].daily_price = daily_price;
                                list_items[index].qty = qty;
                                list_items[index].total = total;
                                list_items[index].monthly_price = monthly_price;
                                list_items[index].keterangan = keterangan;
                            }
                        });

                    } else {
                        //CREATE
                        list_items.push({
                            id_detail: getID(),
                            supplier_harga_id: supplier_harga_id,
                            spesifikasi_id: spesifikasi_id,
                            nama_spesifikasi: spesifikasi_name,
                            satuan_id: satuan_id,
                            kode_satuan: kode_satuan,
                            peti: peti,
                            quality: quality,
                            harga: harga,
                            daily_price: daily_price,
                            qty: qty,
                            total: total,
                            monthly_price: monthly_price,
                            keterangan: keterangan
                        });
                    }

                }

                drawTable();
                resetDetailForm();
            }

        })
    })

    const deleteRowDetail = function(id) {
        const indexToRemove = list_items.findIndex(item => item.id_detail === id);
        if (indexToRemove !== -1) {
            list_items.splice(indexToRemove, 1);
        }
        drawTable();
    }

    const changeStatus = function() {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if (value) {
            $(".po_no").attr("readonly", true);
            $(".po_no").val("AUTO GENERATE");
        } else {
            $(".po_no").attr("readonly", false);
            $(".po_no").val("");
        }
    }

    const resetDetailForm = function() {
        $(".id_detail").val('');
        $(".spesifikasi").val("").change()
        $(".satuan").val("").change()
        $(".harga").val('')
        $(".daily_price").val('')
        $(".monthly_price").val('')
        $(".qty").val('')
        $(".total").val('')
        $(".peti").val('')
        $(".quality").val('Baik')
        $(".keterangan").val('')

        validator_detail.resetForm();
        validator_detail.reset();
    }

    const getDetailSPP = function() {
        var spp_id = $('.spp_id').val();
        var supplier_id = $('.supplier_id').val();

        if (spp_id !== '' && supplier_id !== '') {
            $.ajax({
                url: "<?= base_url("po-lokal-bahan-baku/dropdown/get-detail-barang-spp"); ?>",
                data: {
                    spp_id: spp_id,
                    supplier_id: supplier_id,
                },
                method: "GET",
                beforeSend: function() {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                success: function(response) {
                    $('.barang_id').val(response.barang_master_id).change();
                    list_items = [];
                    $.each(response.sppDetail, function(i, v) {
                        list_items.push({
                            id_detail: getID(),
                            supplier_harga_id: v.supplier_harga_id,
                            spesifikasi_id: v.barang2_id,
                            nama_spesifikasi: v.spesifikasi,
                            satuan_id: v.satuan_id,
                            kode_satuan: v.satuan_name,
                            peti: v.peti,
                            quality: v.kualitas,
                            harga: v.harga_umum,
                            daily_price: v.harga_harian,
                            qty: v.qty,
                            total: v.total,
                            monthly_price: v.harga_bulanan,
                            keterangan: v.keterangan
                        });
                    });
                    $(".barang_id").attr("disabled", true);
                    drawTable();
                },
                onError: function(response) {
                    alert("ERROR")
                }
            });
        }

    }

    const getListDepartemen = function() {
        $.ajax({
            url: "<?= base_url("po-lokal-bahan-baku/dropdown/warehouse"); ?>",
            data: {
                divisi_id: $('.divisi_id').val()
            },
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            success: function(response) {
                var warehouseSelect = $("select[name='warehouse_id']");
                warehouseSelect.empty();

                var emptyOption = $("<option></option>")
                    .attr("value", "")
                    .text("Pilih Warehouse");
                warehouseSelect.append(emptyOption);
                $.each(response.data, function(index, data) {
                    var option = $("<option data-nip=" + data.id + "></option>")
                        .attr("value", data.id)
                        .text(data.warehouse_name.toUpperCase());
                    warehouseSelect.append(option);
                });

            },
            onError: function(response) {
                alert("ERROR")
            }
        });
    }

    const getListSPP = function() {
        $.ajax({
            url: "<?= base_url("po-lokal-bahan-baku/dropdown/get-spp"); ?>",
            data: {
                divisi_id: $('.divisi_id').val(),
                spp_type: "Lokal BB"
            },
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            method: "GET",
            success: function(response) {
                var sppSelect = $("select[name='spp_id']");
                sppSelect.empty();

                var emptyOption = $("<option></option>")
                    .attr("value", "")
                    .text("Pilih Nomor SPP");
                sppSelect.append(emptyOption);
                $.each(response.data, function(index, data) {
                    var option = $("<option></option>")
                        .attr("value", data.id)
                        .text(data.spp_no.toUpperCase());
                    sppSelect.append(option);
                });

            },
            onError: function(response) {
                alert("ERROR")
            }
        });
    }

    const getListSpesifikasiBarang = function() {
        $.ajax({
            url: "<?= base_url("po-lokal-bahan-baku/dropdown/get-spp"); ?>",
            data: {
                divisi_id: $('.divisi_id').val(),
                spp_type: "Lokal BB"
            },
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            method: "GET",
            success: function(response) {
                var sppSelect = $("select[name='spp_id']");
                sppSelect.empty();

                var emptyOption = $("<option></option>")
                    .attr("value", "")
                    .text("Pilih Nomor SPP");
                sppSelect.append(emptyOption);
                $.each(response.data, function(index, data) {
                    var option = $("<option></option>")
                        .attr("value", data.id)
                        .text(data.spp_no.toUpperCase());
                    sppSelect.append(option);
                });

            },
            onError: function(response) {
                alert("ERROR")
            }
        });
    }

    const drawTable = function() {
        $('.body-detail-table').empty();
        $('.foot-detail-table').empty();

        var row = '';
        var row_detail = '';
        var no = 1;
        var umumTotal = 0;
        var harianTotal = 0;
        var bulananTotal = 0;
        var qtyTotal = 0;

        // LIST
        list_items.map(item => {
            row += '<tr style="color:whitesmoke;">';
            row += '<td>' + no + '</td>';
            row += '<td>' + item.nama_spesifikasi + '</td>';
            row += '<td>' + item.kode_satuan + '</td>';
            row += '<td>' + formatRupiah(item.harga) + '</td>';
            row += '<td>' + formatRupiah(item.daily_price) + '</td>';
            row += '<td>' + formatRupiah(item.monthly_price) + '</td>';
            row += '<td>' + item.qty + '</td>';
            row += '<td>' + item.peti + '</td>';
            row += '<td>' + item.quality + '</td>';
            <?php if (!empty($dataPOLokal)) : ?>
                <?php if ($dataPOLokal->is_posted == '0') : ?>
                    row += '<td>' + `
                    <button class="btn btn-warning posting-spp mr-1 edit-table-detail" data-id="${item.id_detail}" >
                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                    </button><button class="btn btn-danger" onclick="deleteRowDetail('${item.id_detail}')">
                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                    </button>` +
                        '</td>';
                <?php endif; ?>
            <?php else : ?>
                row += '<td>' + `
                    <button class="btn btn-warning posting-spp mr-1 edit-table-detail" data-id="${item.id_detail}" >
                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                    </button><button class="btn btn-danger" onclick="deleteRowDetail('${item.id_detail}')">
                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                    </button>` +
                    '</td>';
            <?php endif; ?>

            umumTotal += Number(item.harga.replace(",", ""));
            harianTotal += Number(item.daily_price.replace(",", ""));
            bulananTotal += Number(item.monthly_price.replace(",", ""));
            qtyTotal += Number(item.qty);
            no++;
        });

        // FOOTER
        row_detail += `
                    <tr>
                        <td colspan="2"></td>
                        <td><b>TOTAL</b></td>
                        <td><b>${formatRupiah(umumTotal)}</b></td>
                        <td><b>${formatRupiah(harianTotal)}</b></td>
                        <td><b>${formatRupiah(bulananTotal)}</b></td>
                        <td><b>${formatRupiah(qtyTotal)}</b></td>
                        <td colspan="2"></td>
                        <td style="<?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? "display: none;" : "") : ""; ?>"></td>
                    </tr>
                `;
        total = umumTotal + harianTotal + bulananTotal;

        $('.body-detail-table').append(row);
        $('.foot-detail-table').append(row_detail);

    }

    const getID = function() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let randomString = '';

        for (let i = 0; i < 10; i++) {
            randomString += characters.charAt(Math.floor(Math.random() * characters.length));
        }

        return randomString;
    };


    const print = function(url) {
        window.open(url, "_blank");
    }

    const formatRupiah = function(number) {
        return number ? Number(number).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) : "0.00";
    }

    const reformatRupiah = function(number) {
        return number ? Number(number.replaceAll(",", "")) : 0;
    }

    <?php if (!empty($dataPOLokal)) : ?>
        <?php foreach ($dataPOLokal->rm_purchase_order_details as $detail) : ?>
            list_items.push({
                id_detail: getID(),
                supplier_harga_id: "<?= $detail->supplier_harga_id ?>",
                spesifikasi_id: "<?= $detail->barang2_id ?>",
                nama_spesifikasi: "<?= $detail->spesifikasi ?>",
                satuan_id: "<?= $detail->satuan_id ?>",
                kode_satuan: "<?= $detail->kode_satuan ?>",
                peti: "<?= $detail->peti ?>",
                quality: "<?= $detail->quality ?>",
                harga: "<?= $detail->general_price ?>",
                daily_price: "<?= $detail->daily_price ?>",
                qty: "<?= $detail->qty ?>",
                total: "<?= ($detail->general_price + $detail->daily_price + $detail->monthly_price) * $detail->qty ?>",
                monthly_price: "<?= $detail->monthly_price ?>",
                keterangan: "<?= $detail->note ?>",
            });
        <?php endforeach; ?>
        drawTable();
    <?php endif; ?>
</script>
<?= $this->endSection(); ?>