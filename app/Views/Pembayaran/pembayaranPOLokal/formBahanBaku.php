<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($detail) ? "Update Pembayaran PO Lokal Bahan Baku" : "Tambah Pembayaran PO Lokal Bahan Baku" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("pembayaran-po-lokal-bb"); ?>">
                Kembali
            </a>
            <?php if (!empty($detail)) : ?>
                <?php if ($detail['pembayaranDetail']['status_posting'] == "0") : ?>
                    <?php if (can('Pembayaran', 'Lokal BB', 'd')) : ?>
                        <button onclick="remove('<?= encrypt($detail['pembayaranDetail']['id']) ?>')" class="btn btn-hapus delete-parent float-right">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Pembayaran', 'Lokal BB', 'a')) : ?>
                        <button onclick="posting('<?= encrypt($detail['pembayaranDetail']['id']) ?>')" class="btn btn-success posting-spp float-right posting">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Pembayaran', 'Lokal BB', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-form">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php endif; ?>


                <?php if (can('Pembayaran', 'Lokal BB', 'p')) : ?>
                    <a class="btn btn-warning btn-print float-right text-white" target="_blank" href="<?= base_url('pembayaran-po-lokal-bb/print/' . encrypt($detail['pembayaranDetail']['id']) ?? '') ?>">
                        Print
                    </a>
                <?php endif; ?>

            <?php else : ?>
                <?php if (can('Pembayaran', 'Lokal BB', 'c')) : ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit-form">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Pembayaran</label>

                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <?php  ?>
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($detail) ? encrypt($detail['pembayaranDetail']['id']) : ""; ?>" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control no_bukti_pembayaran" id="no_bukti_pembayaran" name="no_bukti_pembayaran" placeholder="No. Pembayaran" required readonly <?= !empty($detail) ? ' value="' . $detail['pembayaranDetail']['payment_no'] . '"' : '' ?>>
                                    <label for="floatingInput">No. Pembayaran</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : ""; ?> name="bank_id" id="bank_id">
                                <option disabled selected value=""></option>
                                <?php foreach ($bankList as $b) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['bank_id'] == $b->id ? 'selected' : '') : '' ?> value="<?= $b->id ?>"><?= strtoupper($b->kode_bank) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Kode Bank (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" 
                                    <?= !empty($detail) && $detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '' ?> 
                                    name="jenis_pembayaran" 
                                    id="jenis_pembayaran"
                                    required>
                                <?php if (empty($detail['pembayaranDetail']['jenis_bayar'])): ?>
                                    <option value="" selected disabled>Pilih Jenis Pembayaran</option>
                                <?php endif; ?>
                                <option value="MERAH" <?= !empty($detail['pembayaranDetail']['jenis_bayar']) && $detail['pembayaranDetail']['jenis_bayar'] == 'MERAH' ? 'selected' : '' ?>>MERAH</option>
                                <option value="PUTIH" <?= !empty($detail['pembayaranDetail']['jenis_bayar']) && $detail['pembayaranDetail']['jenis_bayar'] == 'PUTIH' ? 'selected' : '' ?>>PUTIH</option>
                            </select>
                            <label  for="floatingInput" style="z-index: 1;">Jenis Pembayaran</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : "" ?> class="form-control input-picker payment_date" id="payment_date" name="payment_date" placeholder="Tanggal Jatuh Tempo" <?= !empty($detail) ? 'value="' . date('d/m/Y', strtotime($detail['pembayaranDetail']['payment_date']))  . '"' : '' ?>>
                                    <label for="floatingInput">Tanggal Pembayaran</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating  form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : "" ?> class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['divisi_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : "" ?> name="supplier_id" id="supplier_id">
                                <option disabled selected value=""></option>
                                <?php foreach ($suppliers as $supplier) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['supplier_id'] == $supplier['id'] ? 'selected' : '') : '' ?> value="<?= $supplier['id'] ?>"><?= strtoupper($supplier['name']) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Supplier</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : "" ?> name="tipe_pembayaran" id="tipe_pembayaran">
                                <option selected value="<?= !empty($detail['pembayaranDetail']['type_bayar']) ? $detail['pembayaranDetail']['type_bayar'] : '';  ?>"></option>
                                <option value="BULANAN">BULANAN</option>
                                <option value="HARIAN">HARIAN</option>
                                <option value="BULANAN_PER_PO">BULANAN PER PO</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tipe Bayar</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bulanan-form">
                           <div class="bulanan-form">
                                <?php if (!empty($detail)) : ?>
                                    <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                        <input 
                                            <?= $detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '' ?>  
                                            value="<?= !empty($detail['pembayaranDetail']['bulan']) ? date('Y-m', strtotime($detail['pembayaranDetail']['bulan'])) : '' ?>" 
                                            name="bulan" 
                                            id="bulan" 
                                            class="form-control input-picker">
                                        <label for="bulan" style="z-index: 1;">Pilih Bulan</label>
                                    </div>
                                <?php else : ?>
                                    <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                        <input 
                                            value="" 
                                            name="bulan" 
                                            id="bulan" 
                                            class="form-control input-picker">
                                        <label for="bulan" style="z-index: 1;">Pilih Bulan</label>
                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>
                        <div class="harian-form">
                            <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                <select <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : "" ?> class="form-select" name="po[]" id="po">
                                    <option disabled selected value="">Pilih No PO</option>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">No PO</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <input type="text" name="jenis_dokumen" class="form-control" id="jenis_dokumen" readonly>
                            <label for="floatingInput" style="z-index: 1;">Jenis Dokumen</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : "" ?> class="form-select " name="payment_method" id="payment_method">
                                <option disabled selected value="">Pilih Metode Pembayaran</option>
                                <option <?= !empty($detail) ? ($detail['pembayaranDetail']['payment_method'] == "Cash" ? 'selected' : '') : '' ?> value="Cash">Cash</option>
                                <option <?= !empty($detail) ? ($detail['pembayaranDetail']['payment_method'] == "Bank" ? 'selected' : '') : '' ?> value="Bank">Bank</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Metode Pembayaran</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : "" ?> name="pembayaran_oleh" id="pembayaran_oleh" autocomplete="one-time-code" value="<?= !empty($detail) ? $detail['pembayaranDetail']['pembayaran_oleh'] : session()->get("login")->name; ?>" type="text" class="form-control" placeholder="Pembayaran Oleh">
                            <label for="floatingInput">Pembayaran Oleh</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : "" ?> name="akun_kas" id="akun_kas">
                                <option disabled selected value=""></option>
                                <?php foreach ($subsAkuns as $subs) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['akun_kas'] == $subs->id ? 'selected' : '') : '' ?> value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Debit (Opsional)</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : "" ?> name="akun_selisih" id="akun_selisih">
                                <option disabled selected value=""></option>
                                <?php foreach ($subsAkuns as $subs) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['akun_selisih'] == $subs->id ? 'selected' : '') : '' ?> value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Kredit</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code"
                                    class="form-control input-picker due_date"
                                    id="payment_panjar_date"
                                    name="payment_panjar_date"
                                    placeholder="Tanggal Pembayaran Panjar (Opsional)"
                                    <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : "" ?>
                                    value="<?= !empty($detail['pembayaranDetail']['payment_panjar_date']) && $detail['pembayaranDetail']['payment_panjar_date'] != '1970-01-01' ?
                                                date('d/m/Y', strtotime($detail['pembayaranDetail']['payment_panjar_date'])) : '' ?>">
                                <label for="payment_panjar_date">Tanggal Pembayaran Panjar (Opsional)</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px"
                                    class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <textarea <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : "" ?> name="keterangan" class="form-control keterangan" id="keterangan" placeholder="Keterangan Transaksi Pembayaran" style="height: 100px;"><?= !empty($detail) ? $detail['pembayaranDetail']['keterangan'] : '' ?></textarea>
                    </div>
                </div>

                <div id="coa-panjar-section" style="display: none;">
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label font-weight-bold lable-title">Akun Coa Panjar Supplier</label>
                        </div>
                    </div>
                    <div class="row">
                        <input type="hidden" id="id_panjar">

                        <div class="col-md-3">
                            <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                <input type="text" class="form-control" id="tgl_panjar" readonly>
                                <label for="no_panjar" style="z-index: 1;">Tgl Panjar</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                <select class="form-select" name="akun_kas_panjar" id="akun_kas_panjar"></select>
                                <label for="akun_kas_panjar" style="z-index: 1;">Debit (Opsional)</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                <select class="form-select" name="akun_selisih_panjar" id="akun_selisih_panjar"></select>
                                <label for="akun_selisih_panjar" style="z-index: 1;">Kredit</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                <input class="form-control" name="keterangan_panjar" id="keterangan_panjar">
                                <label for="keterangan_panjar" style="z-index: 1;">Keterangan</label>
                            </div>
                        </div>
                    </div>
                    <br>
                </div>

                <div id="coa-panjar-tb-section" style="display: none;">
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label font-weight-bold lable-title">Akun Coa Panjar TB Supplier</label>
                        </div>
                    </div>

                    <div class="row">
                        <input type="hidden" id="id_panjar_tb">

                        <div class="col-md-3">
                            <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                <input type="text" class="form-control" id="tgl_panjar_tb" readonly>
                                <label for="no_panjar_tb" style="z-index: 1;">Tgl Panjar</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                <select class="form-select" name="akun_kas_panjar_tb" id="akun_kas_panjar_tb"></select>
                                <label for="akun_kas_panjar_tb" style="z-index: 1;">Debit (Opsional)</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                <select class="form-select" name="akun_selisih_panjar_tb" id="akun_selisih_panjar_tb"></select>
                                <label for="akun_selisih_panjar_tb" style="z-index: 1;">Kredit</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                <input class="form-control" name="keterangan_panjar_tb" id="keterangan_panjar_tb">
                                <label for="keterangan_panjar_tb" style="z-index: 1;">Keterangan</label>
                            </div>
                        </div>
                    </div>
                    <br>
                </div>


                <div id="coa-pinjaman-section" style="display: none;">
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label font-weight-bold lable-title">Akun Coa Pinjaman Supplier</label>
                        </div>
                    </div>
                    <div class="row">
                        <input type="hidden" id="id_pinjaman">

                        <div class="col-md-3">
                            <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                <input type="text" class="form-control" id="tgl_pinjaman" readonly>
                                <label for="tgl_pinjaman" style="z-index: 1;">Tgl Pinjaman</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                <select class="form-select" name="akun_kas_pinjaman" id="akun_kas_pinjaman"></select>
                                <label for="akun_kas_pinjaman" style="z-index: 1;">Debit (Opsional)</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                <select class="form-select" name="akun_selisih_pinjaman" id="akun_selisih_pinjaman"></select>
                                <label for="akun_selisih_pinjaman" style="z-index: 1;">Kredit</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                <input class="form-control" name="keterangan_pinjaman" id="keterangan_pinjaman">
                                <label for="keterangan_pinjaman" style="z-index: 1;">Keterangan</label>
                            </div>
                        </div>
                    </div>
                    <br>
                </div>
                <br>
                <br>
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Detail Pembayaran</label>
                    </div>
                </div>
                <div class="row">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Pembayaran</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Potongan Panjar</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="potongan-pinjaman" data-bs-toggle="tab" data-bs-target="#potongan-pinjaman-pane" type="button" role="tab" aria-controls="potongan-pinjaman-pane" aria-selected="false">Pinjaman Supplier</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="potongan-panjar-tb-tab" data-bs-toggle="tab" data-bs-target="#potongan-panjar-tb-tab-pane" type="button" role="tab" aria-controls="potongan-panjar-tb-tab-pane" aria-selected="false">Panjar TB Supplier</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0" style="border-color: #f7f6f5;">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th style="text-align: center;">No</th>
                                                    <th style="text-align: center;">Tanggal PO</th>
                                                    <th style="text-align: center;">No PO</th>
                                                    <th style="text-align: center;">Barang</th>
                                                    <th style="text-align: center;">Qty (Kg)</th>
                                                    <th style="text-align: center;">Total PPH</th>
                                                    <th style="text-align: center;">Total</th>
                                                    <th style="text-align: center;">Total Di bayar PPH</th>
                                                    <th style="text-align: center;">Total Di bayar</th>
                                                    <!-- <th style="text-align: center;">Sisa Bayar</th> -->
                                                    <th style="text-align: center;">Input Harga PPH</th>
                                                    <th style="text-align: center; min-width: 150px;">Input Harga</th>

                                                </tr>
                                            </thead>
                                            <tbody id="body-table" style="text-align: center;">
                                                <tr style="color: whitesmoke;">
                                                    <td colspan="11">Tidak Ada Pembayaran</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable-panjar" width="100%" cellspacing="0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No</th>
                                                <!-- <th onclick="changeSort('no_panjar')">No. Panjar</th> -->
                                                <th onclick="changeSort('payment_date')">Tgl Panjar</th>
                                                <th onclick="changeSort('payment_amount')">Total Panjar</th>
                                                <th>Sisa Panjar</th>
                                                <th>Bayar Panjar </th>
                                            </tr>

                                        </thead>
                                        <tbody class="body-table" id="body-table-panjar" style="cursor: pointer;">
                                            <tr style="color: whitesmoke;">
                                                <td colspan="5" style="text-align: center;">Tidak ada Panjar</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>



                        <div class="tab-pane fade" id="potongan-pinjaman-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable-pinjaman" width="100%" cellspacing="0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No</th>
                                                <!-- <th onclick="changeSort('no_pinjaman')">No. Pinjaman</th> -->
                                                <th onclick="changeSort('payment_date')">Tgl Pinjaman</th>
                                                <th onclick="changeSort('payment_amount')">Total Pinjaman</th>
                                                <th>Sisa Pinjaman</th>
                                                <th>Bayar Pinjaman </th>
                                            </tr>

                                        </thead>
                                        <tbody class="body-table" id="body-table-pinjaman" style="cursor: pointer;">
                                            <tr style="color: whitesmoke;">
                                                <td colspan="5" style="text-align: center;">Tidak ada pinjaman</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>


                        <div class="tab-pane fade" id="potongan-panjar-tb-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable-panjar-TB" width="100%" cellspacing="0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No</th>
                                                <!-- <th onclick="changeSort('no_panjar')">No. Panjar TB</th> -->
                                                <th onclick="changeSort('payment_date')">Tgl Panjar TB</th>
                                                <th onclick="changeSort('payment_amount')">Total Panjar TB</th>
                                                <th>Sisa Panjar TB</th>
                                                <th>Bayar Panjar TB </th>
                                            </tr>

                                        </thead>
                                        <tbody class="body-table" id="body-table-panjar-TB" style="cursor: pointer;">
                                            <tr style="color: whitesmoke;">
                                                <td colspan="5" style="text-align: center;">Tidak ada Panjar TB</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>


                <!-- <div class="col-subtitle-modal mt-3">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label font-weight-bold modal-sub-title">Data pembelian yang sudah diterima</label>
                        </div>
                    </div>
                </div> -->
            </form>

        </div>
    </div>
</section>

<?php if (!empty($detail)) : ?>
    <?php if ($detail['pembayaranDetail']['type_bayar'] == "Bulanan") : ?>
        <script>
            $('.bulanan-form').show();
            $('.harian-form').hide();
            $('#jenis_dokumen').val("KWITANSI TB");
        </script>
    <?php else : ?>
        <script>
            $('.harian-form').show();
            $('.bulanan-form').hide();
            $('#jenis_dokumen').val("LPB");
        </script>
    <?php endif; ?>
    <script>
        $('#tipe_pembayaran').val("<?= strtoupper($detail['pembayaranDetail']['type_bayar']) ?>");
        // $('#tipe_pembayaran').attr('disabled', true);
    </script>
<?php else : ?>
    <script>
        $('.bulanan-form,.harian-form').hide();
    </script>
<?php endif; ?>
<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    const table = $('#dataTable');
    var listPoNo = [];
    var listPoID = [];
    var listPoDetailID = [];
    var listPenerimaanDetailID = [];
    var listPenerimaanDetailHarga = [];
    var listPanjar = [];
    var listPanjarTB = [];
    var listPinjaman = [];
    var listPembayaran = [];

    <?php if (!empty($detail)) : ?>
        getPanjarSupplier();
        generateLPBNo();
    <?php endif; ?>


    var validator = $(".create-form").validate({
        rules: {
            no_bukti_pembayaran: {
                required: true
            },
            divisi_id: {
                required: true
            },
            pembayaran_oleh: {
                required: true
            },
            status_lunas: {
                required: true
            },
            payment_date: {
                required: true
            },
            supplier_id: {
                required: true
            },
            tipe_pembayaran: {
                required: true
            },
            jenis_dokumen: {
                required: true
            },
            nominal_pembayaran: {
                required: true
            },
            payment_method: {
                required: true
            },
            akun_selisih: {
                required: true
            },
            jenis_pembayaran: {
                required: true
            },
            keterangan: {
                required: true
            }
        },
        messages: {
            no_bukti_pembayaran: {
                required: "No. Pembayaran wajib diisi"
            },
            divisi_id: {
                required: "Departemen wajib diisi"
            },
            pembayaran_oleh: {
                required: "Pembayaran oleh wajib diisi"
            },
            status_lunas: {
                required: "Status pelunasan wajib diisi"
            },
            payment_date: {
                required: "Tanggal pembayaran wajib diisi"
            },
            supplier_id: {
                required: "Supplier wajib diisi"
            },
            jenis_dokumen: {
                required: "Jenis dokumen wajib diisi"
            },
            nominal_pembayaran: {
                required: "Nominal pembayaran wajib diisi"
            },
            payment_method: {
                required: "Metode pembayaran wajib diisi"
            },
            akun_selisih: {
                required: "Akun kredit wajib diisi"
            },
            jenis_pembayaran: {
                required: "Jenis Pembayaran wajib diisi"
            },
            // bayar_panjar: {
            //     digits: "harus berupa angka"
            // },
            keterangan: {
                required: "Keterangan Transaksi Wajib Diisi"
            }
        },
        errorElement: 'span',
        errorClass: 'text-danger',
        errorPlacement: function(error, element) {
            var elem = $(element);
            if (elem.hasClass("multiple_lpb_id")) {
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


    $(document).ready(function() {

        $("#akun_kas_panjar, #akun_selisih_panjar, #akun_kas_panjar_tb, #akun_selisih_panjar_tb, #akun_selisih_pinjaman, #akun_kas_pinjaman").select2({
            theme: "bootstrap-5",
            placeholder: "Pilih Akun",
            allowClear: true,
            ajax: {
                url: "<?= base_url('/sub-account/dropdownData'); ?>",
                dataType: "json",
                delay: 250, // Hindari spam request
                data: function(params) {
                    return {
                        search: params.term // Kirim kata kunci pencarian
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.id,
                                text: item.nama_sub
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 3
        });
    });


    $("#payment_date,#jatuh_tempo, #payment_panjar_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $("#bulan").datepicker({
        format: "mm/yyyy",        // format output
        startView: "months",      // mulai dari tampilan bulan
        minViewMode: "months",    // biar cuma bisa pilih bulan
        autoclose: true,
        orientation: "bottom auto"
    });

    $('#payment_date').change(function() {
        generatePaymentNumber();
    });

    $('#bank_id').select2({
        placeholder: "Pilih kode bank",
        theme: "bootstrap-5"
    }).change(function() {
        generatePaymentNumber();
    });

    $('#jenis_pembayaran').select2({
        placeholder: "Pilih Jenis Pembayaran",
        theme: "bootstrap-5"
    }).change(function() {
        generatePaymentNumber();
    });

    $('#po').select2({
        placeholder: "Pilih No Penerimaan Barang",
        theme: "bootstrap-5",
        multiple: true
    });

    $('#akun_kas').select2({
        placeholder: "Pilih akun debit",
        theme: "bootstrap-5"
    });

    $('#akun_selisih').select2({
        placeholder: "Pilih akun Kredit",
        theme: "bootstrap-5"
    });

    $('#jenis_pembayaran').select2({
        placeholder: "Pilih Jenis Pembayaran",
        theme: "bootstrap-5"
    });

    $('#payment_method').select2({
        placeholder: "Pilih metode pembayaran",
        theme: "bootstrap-5"
    }).change(function() {
        generatePaymentNumber();
    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5"
    }).change(function() {
        generatePaymentNumber();
        generateLPBNo();
        resetTable();
    });

    $('#bulan').change(function() {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);

        var poID = $(this).val();
        var supplierID = $('#supplier_id').val();
        var formData = new FormData();
        formData.append("supplierID", $('#supplier_id').val());
        formData.append("divisiID", $('#divisi_id').val());
        formData.append("bulan", $(this).val());
        formData.append("tipeBayar", $('#tipe_pembayaran').val());


        if (poID != "[]") {
            $.ajax({
                url: "<?= base_url("pembayaran-po-lokal-bb/get-list-po-no-paid"); ?>",
                data: formData,
                method: "POST",
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                processData: false,
                contentType: false,
                success: function(response) {

                    listPembayaran = [];
                    listPembayaran = response.data;
                    drawTable(listPembayaran)

                    csrf.val(response.token);
                    $('#nominal_pembayaran').val(response.data.sisaNumber);
                }
            });
        }
    });

    <?php if (!empty($detail['pembayaranDetail']) && $detail['pembayaranDetail']['type_bayar'] == "Bulanan") : ?>
        
                <?php if (!empty($detail['pembayaranDetail']) && $detail['pembayaranDetail']['type_bayar'] == "Bulanan") : ?>
                $('#bulan')
                    .datepicker('update', "<?= !empty($detail['pembayaranDetail']['bulan']) ? date('m/Y', strtotime($detail['pembayaranDetail']['bulan'])) : '' ?>")
                    .trigger('change');
    <?php endif; ?>

    <?php endif; ?>

    $('#po').change(function() {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);

        var poID = JSON.stringify($(this).val());
        var supplierID = $('#supplier_id').val();
        var formData = new FormData();

        formData.append("supplierID", $('#supplier_id').val());
        formData.append("poID", poID);
        formData.append("tipeBayar", $('#tipe_pembayaran').val());


        if (poID != "[]") {
            $.ajax({
                url: "<?= base_url("pembayaran-po-lokal-bb/get-list-po-no-paid"); ?>",
                data: formData,
                method: "POST",
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                processData: false,
                contentType: false,
                success: function(response) {
                    listPembayaran = [];
                    listPembayaran = response.data;
                    csrf.val(response.token);
                    drawTable(listPembayaran);
                    $('#nominal_pembayaran').val(response.data.sisaNumber);
                    generateKeteranganPembayaran(response.data)
                }
            });
        }

    });

    $(".btn-submit-form").click(function() {
        var id = $('.id').val();
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);


        //APPEND BAYAR PANJAR TO listPanjar
        $.each(listPanjar, function(i, v) {
            var element = $('input[data-id="' + v.panjar_id + '"].bayar_panjar');
            var akun_kas_panjar = $("#akun_kas_panjar").val();
            var akun_selisih_panjar = $("#akun_selisih_panjar").val();
            var keterangan_panjar = $("#keterangan_panjar").val();
            var input_user = (element.val());
            listPanjar[i].bayar_panjar = input_user;
            listPanjar[i].akun_kas_panjar = akun_kas_panjar;
            listPanjar[i].akun_selisih_panjar = akun_selisih_panjar;
            listPanjar[i].keterangan_panjar = keterangan_panjar;
        });


        $.each(listPanjarTB, function(i, v) {
            var element = $('input[data-id="' + v.panjar_tb_id + '"].bayar_panjar_tb');
            var akun_kas_panjar_tb = $("#akun_kas_panjar_tb").val();
            var akun_selisih_panjar_tb = $("#akun_selisih_panjar_tb").val();
            var keterangan_panjar_tb = $("#keterangan_panjar_tb").val();
            var input_user = (element.val());
            listPanjarTB[i].bayar_panjar_tb = input_user;
            listPanjarTB[i].akun_kas_panjar_tb = akun_kas_panjar_tb;
            listPanjarTB[i].akun_selisih_panjar_tb = akun_selisih_panjar_tb;
            listPanjarTB[i].keterangan_panjar_tb = keterangan_panjar_tb;
        });


        $.each(listPinjaman, function(i, v) {
            var element = $('input[data-id="' + v.pinjaman_id + '"].bayar_pinjaman');
            var akun_kas_pinjaman = $("#akun_kas_pinjaman").val();
            var akun_selisih_pinjaman = $("#akun_selisih_pinjaman").val();
            var keterangan_pinjaman = $("#keterangan_pinjaman").val();
            var input_user = (element.val());
            listPinjaman[i].bayar_pinjaman = input_user;
            listPinjaman[i].akun_kas_pinjaman = akun_kas_pinjaman;
            listPinjaman[i].akun_selisih_pinjaman = akun_selisih_pinjaman;
            listPinjaman[i].keterangan_pinjaman = keterangan_pinjaman;
        });


        $.each(listPembayaran, function(i, v) {
            var element = $('input[data-id="' + v.group_key + '"].total_po_dibayar');
            var input_user = destroyFormatRupiahPayment(element.val());

            listPembayaran[i].total_paid = parseFloat(input_user) || 0;
        });

        $.each(listPembayaran, function(i, v) {
            var element = $('input[data-id="' + v.group_key + '"].total_pph_po_dibayar');
            var input_user = destroyFormatRupiahPayment(element.val());

            listPembayaran[i].total_paid_pph = parseFloat(input_user) || 0;
        });


        if (id) {
            // UPDATE
            if ($(".create-form").valid()) {
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
                        let formData = new FormData(document.querySelector(".create-form"));
                        // Format angka sebelum dimasukkan ke formData
                        let nominalPembayaran = destroyFormatRupiahPayment($('.nominal_pembayaran').val());
                        let totalPembayaran = destroyFormatRupiahPayment($('[name="total_pembayaran"]').val());
                        let totalPembayaranPPH = destroyFormatRupiahPayment($('[name="total_pembayaran_pph"]').val());
                        let totalPembayaranPanjar = destroyFormatRupiahPayment($('[name="total_pembayaran_panjar"]').val());
                        let totalPembayaranPanjarTB = destroyFormatRupiahPayment($('[name="total_pembayaran_panjar_tb"]').val());
                        let totalPembayaranPinjaman = destroyFormatRupiahPayment($('[name="total_pembayaran_pinjaman"]').val());
                        formData.set('nominal_pembayaran', nominalPembayaran);
                        formData.set('total_pembayaran', totalPembayaran);
                        formData.set('total_pembayaran_pph', totalPembayaranPPH);
                        formData.set('total_pembayaran_panjar', totalPembayaranPanjar);
                        formData.set('total_pembayaran_panjar_tb', totalPembayaranPanjarTB);
                        formData.set('total_pembayaran_pinjaman', totalPembayaranPinjaman);

                        // Format list data sebelum dimasukkan
                        const formatList = (list) => {
                            return list.map(item => {
                                for (const key in item) {
                                    if (typeof item[key] === 'string' && item[key].includes(',')) {
                                        item[key] = destroyFormatRupiahPayment(item[key]);
                                    }
                                }
                                return item;
                            });
                        };

                        // Format semua list
                        let formattedPanjarList = formatList(listPanjar);
                        let formattedPinjamanList = formatList(listPinjaman);
                        let formattedPanjarTBList = formatList(listPanjarTB);

                        // Format pembayaranList
                        let formattedPembayaranList = {
                            ...listPembayaran,
                            detail: formatList(listPembayaran.detail || []),
                            panjar: formatList(listPembayaran.panjar || []),
                            panjar_tb: formatList(listPembayaran.panjar_tb || []),
                            pinjaman: formatList(listPembayaran.pinjaman || [])
                        };

                        // Append formatted lists to formData
                        formData.append("poIDList", JSON.stringify(listPoID));
                        formData.append("poDetailIDList", JSON.stringify(listPoDetailID));
                        formData.append("penerimaanDetailIDList", JSON.stringify(listPenerimaanDetailID));
                        formData.append("penerimaanDetailHargaList", JSON.stringify(listPenerimaanDetailHarga));
                        formData.append("poNoList", JSON.stringify(listPoNo));
                        formData.append("panjarList", JSON.stringify(formattedPanjarList));
                        formData.append("pinjamanList", JSON.stringify(formattedPinjamanList));
                        formData.append("panjarTBList", JSON.stringify(formattedPanjarTBList));
                        formData.append("pembayaranList", JSON.stringify(formattedPembayaranList));

                        $.ajax({

                            url: "<?= base_url("pembayaran-po-lokal-bb/update"); ?>",
                            data: formData,
                            beforeSend: function(xhr) {
                                setLoading();
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                                            window.location.href = `<?= base_url("pembayaran-po-lokal-bb/id/"); ?>` + response.id;
                                        })
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    });
                                }
                            },
                        });

                    }
                })
            }

        } else {
            // CREATE
            // VALIDASI BARANG LIST
            if (listPoID.length == 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'PO List Masih kosong',
                    confirmButtonColor: '#4e73df',
                });
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
                        cancelButtonText: 'Kembali',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let formData = new FormData(document.querySelector(".create-form"));
                            // Format angka sebelum dimasukkan ke formData
                            let nominalPembayaran = destroyFormatRupiahPayment($('.nominal_pembayaran').val());
                            let totalPembayaran = destroyFormatRupiahPayment($('[name="total_pembayaran"]').val());
                            let totalPembayaranPPH = destroyFormatRupiahPayment($('[name="total_pembayaran_pph"]').val());
                            let totalPembayaranPanjar = destroyFormatRupiahPayment($('[name="total_pembayaran_panjar"]').val());
                            let totalPembayaranPanjarTB = destroyFormatRupiahPayment($('[name="total_pembayaran_panjar_tb"]').val());
                            let totalPembayaranPinjaman = destroyFormatRupiahPayment($('[name="total_pembayaran_pinjaman"]').val());
                            formData.set('nominal_pembayaran', nominalPembayaran);
                            formData.set('total_pembayaran', totalPembayaran);
                            formData.set('total_pembayaran_pph', totalPembayaranPPH);
                            formData.set('total_pembayaran_panjar', totalPembayaranPanjar);
                            formData.set('total_pembayaran_panjar_tb', totalPembayaranPanjarTB);
                            formData.set('total_pembayaran_pinjaman', totalPembayaranPinjaman);

                            const formatList = (list) => {
                                if (!Array.isArray(list)) return []; // Pastikan `list` adalah array
                                return list.map(item => {
                                    for (const key in item) {
                                        if (typeof item[key] === 'string' && item[key].includes(',')) {
                                            item[key] = destroyFormatRupiahPayment(item[key]);
                                        }
                                    }
                                    return item;
                                });
                            };

                            // Pastikan pembayaranList berbentuk array
                            let formattedPembayaranList = Array.isArray(listPembayaran) ?
                                listPembayaran :
                                Object.values(listPembayaran);

                            // Format semua list
                            let formattedPanjarList = formatList(listPanjar);
                            let formattedPinjamanList = formatList(listPinjaman);
                            let formattedPanjarTBList = formatList(listPanjarTB);

                            // Append formatted lists to formData
                            formData.append("no_bukti_pembayaran", $('#no_bukti_pembayaran').val());
                            formData.append("poIDList", JSON.stringify(listPoID));
                            formData.append("poNoList", JSON.stringify(listPoNo));
                            formData.append("poDetailIDList", JSON.stringify(listPoDetailID));
                            formData.append("penerimaanDetailIDList", JSON.stringify(listPenerimaanDetailID));
                            formData.append("penerimaanDetailHargaList", JSON.stringify(listPenerimaanDetailHarga));
                            formData.append("panjarList", JSON.stringify(formattedPanjarList));
                            formData.append("pinjamanList", JSON.stringify(formattedPinjamanList));
                            formData.append("panjarTBList", JSON.stringify(formattedPanjarTBList));
                            formData.append("pembayaranList", JSON.stringify(formattedPembayaranList));

                            $.ajax({
                                url: "<?= base_url("pembayaran-po-lokal-bb/create"); ?>",
                                data: formData,
                                beforeSend: function(xhr) {
                                    setLoading();
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                                                window.location.href = `<?= base_url("pembayaran-po-lokal-bb/id/"); ?>` + response.id;
                                            })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        });
                                    }
                                },
                            });

                        }
                    })
                }
            }
        }
    })



    $('#tipe_pembayaran').select2({
        placeholder: "Pilih Tipe Bayar",
        theme: "bootstrap-5"
    }).change(function() {
        var tipeBayar = $(this).val();
        if (tipeBayar == "BULANAN") {
            $('.bulanan-form').show();
            $('.harian-form').hide();
            $('#bulan').val('');
            $('#jenis_dokumen').val("KWITANSI TB");
        if (tipeBayar == "BULANAN_PER_PO") {
            $('.harian-form').show();
            $('.bulanan-form').hide();
            $('#jenis_dokumen').val("LPB");
            generateLPBNoPerBulan();
        }} else {
            $('.harian-form').show();
            $('.bulanan-form').hide();
            $('#jenis_dokumen').val("LPB");
            generateLPBNo();
        }
        // clear res
        resetTable();
    });

    $('#supplier_id').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5"
    }).change(function(e) {
        getPanjarSupplier();
        if ($('#tipe_pembayaran').val() == "HARIAN") {
            generateLPBNo();
        } else {
            $('#bulan').val("");
        }
        resetTable();
    });


    function resetTable() {
        // clear res
        listPoID.length = 0;
        listPoNo.length = 0;
        const table = $('#dataTable');
        table.find('tbody').empty();
        var newRow = $('<tr>');
        table.find('tbody').append(newRow);
    }

    function remove(id) {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        Swal.fire({
            icon: 'question',
            title: 'Hapus Pembayaran Ini ?',
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
                    url: "<?= base_url("pembayaran-po-lokal-bb/delete"); ?>",
                    data: formData,
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
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        }).then((result) => {
                            location.reload();
                        });

                    },
                });
            }
        });
    }

    function posting(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Pembayaran ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Posting',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("pembayaran-po-lokal-bb/posting"); ?>",
                    data: {
                        id: id,
                        divisi_id: $('#divisi_id option:selected').val(),
                        status: 1,
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

    function generateLPBNo() {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        var formData = new FormData();
        formData.append("supplierID", $('#supplier_id').val());
        formData.append("divisiID", $('#divisi_id').val());
        formData.append("tipe_pembayaran", $('#tipe_pembayaran option:selected').val());

        // Ambil data multiple_po_id dari BE untuk auto-select
        let selectedPo = <?= json_encode($detail['pembayaranDetail']['multiple_po_id'] ?? []) ?>;
        formData.append("selectedPo", JSON.stringify(selectedPo));

        $.ajax({
            url: "<?= base_url('pembayaran-po-lokal-bb/get-po-not-paid'); ?>",
            data: formData,
            method: "POST",
            dataType: "json",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            processData: false,
            contentType: false,
            success: function(response) {

                csrf.val(response.token);

                // Kosongkan dan tambahkan opsi default
                $("#po").empty().append(`<option value=""></option>`);

                // Iterasi data PO yang belum dibayar
                response.data.forEach(function(item) {
                    let itemPoID = Number(item.poID); // Pastikan item.poID jadi angka
                    let isSelected = selectedPo.includes(itemPoID) ? "selected" : "";
                    $("#po").append(`<option value="${itemPoID}" ${isSelected}>${item.po_no}</option>`);
                });

                // Refresh select2 jika dipakai
                $("#po").trigger("change");
            }
        });
    }


    function generateLPBNoPerBulan() {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        var formData = new FormData();
        formData.append("supplierID", $('#supplier_id').val());
        formData.append("divisiID", $('#divisi_id').val());
        formData.append("tipe_pembayaran", $('#tipe_pembayaran option:selected').val());

        // Ambil data multiple_po_id dari BE untuk auto-select
        let selectedPo = <?= json_encode($detail['pembayaranDetail']['multiple_po_id'] ?? []) ?>;
        formData.append("selectedPo", JSON.stringify(selectedPo));

        $.ajax({
            url: "<?= base_url('pembayaran-po-lokal-bb/get-po-not-paid'); ?>",
            data: formData,
            method: "POST",
            dataType: "json",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            processData: false,
            contentType: false,
            success: function(response) {

                csrf.val(response.token);

                // Kosongkan dan tambahkan opsi default
                $("#po").empty().append(`<option value=""></option>`);

                // Iterasi data PO yang belum dibayar
                response.data.forEach(function(item) {
                    let itemPoID = Number(item.poID); // Pastikan item.poID jadi angka
                    let isSelected = selectedPo.includes(itemPoID) ? "selected" : "";
                    $("#po").append(`<option value="${itemPoID}" ${isSelected}>${item.po_no}</option>`);
                });

                // Refresh select2 jika dipakai
                $("#po").trigger("change");
            }
        });
    }
    

    function drawPaidTable(data) {
        var no = 1;
        const table = $('#dataTable');
        table.find('tbody').empty();

        $.each(data.detail, function(i, v) {
            listPoID.push({
                poID: v.rm_purchase_order_id
            });

            listPoNo.push({
                poNo: v.no_po
            });

            var newRow = $('<tr>');
            newRow.append($('<td style="text-align:center;">').text(no++));
            newRow.append($('<td style="text-align:center;">').text(v.tanggal_PO));
            newRow.append($('<td style="text-align:center;">').text(v.no_po));
            newRow.append($('<td style="text-align:center;">').text(v.barang));
            newRow.append($('<td style="text-align:center;">').text(v.total_qty_diterima));
            newRow.append($('<td style="text-align:center;">').text(greatFormatRupiahPayment(v.total_tagihan_pph)));
            newRow.append($('<td style="text-align:center;">').text(greatFormatRupiahPayment(v.total_tagihan)));
            newRow.append($('<td style="text-align:center;">').text(greatFormatRupiahPayment(v.total_paid_pph)));
            newRow.append($('<td style="text-align:center;">').text(greatFormatRupiahPayment(v.total_paid)));
            newRow.append($('<td class="hidden" style="display:none; width:150px !important;">').html(
                `
                        <input  <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : "" ?> oninput="limitInputBayar(this, ${v.total_number + v.sisa_tagihan})" autocomplete="one-time-code" data-id="${v.penerimaan_barang_detail_id}"  class="form-control pembayaran" type="text" value="${greatFormatRupiah (v.total_tagihan)}" name = "pembayaran" style="height:40px">
                                `
            ));

            newRow.append($('<td style="text-align:center;">').html(
                `
                        <input  oninput="this.value = greatFormatRupiahPayment(this.value)"  oninput="limitInputBayar(this, ${v.total_tagihan})" autocomplete="one-time-code" data-id="${v.group_key}" class="form-control total_po_dibayar" type="text" value="${greatFormatRupiahPayment(v.total_tagihan)}" name = "total_po_dibayar" style="height:40px">
                                `
            ));

            table.find('tbody').append(newRow);
        });
        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="10"><b>TOTAL PEMBAYARAN  </b></td>'));
        newRow.append($('<td style="text-align:right;" ><b>' +
            '<input autocomplete="one-time-code" data-id="" onkeyup="validateTotalPayment()" onblur="validateTotalPayment()"  class="form-control total-pembayaran trigger-input" onchange="this.value = greatFormatRupiahPayment(this.value)" onkeyup="preventNegativeInput(this)" type="text" value="' + greatFormatRupiahPayment(data.total_pembayaran) + '" name = "total_pembayaran" readonly>' +
            '</b></td>'));
        table.find('tbody').append(newRow);

        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="10"><b>POTONGAN/DISKON</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>' +
            '  <input <?= !empty($detail) ? 'disabled' : '' ?> oninput="preventNegativeInput(this)" onkeyup="this.value = greatFormatRupiahPayment(this.value);" name="potongan" id="potongan" autocomplete="one-time-code" value="<?= !empty($detail) ? number_format($detail['pembayaranDetail']['potongan_harga'], 2) : '0' ?>" type="text" class="form-control trigger-input" placeholder="Nominal Pembayaran">' +
            '</b></td>'));

        table.find('tbody').append(newRow);
        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="10"><b>TOTAL PEMBAYARAN PANJAR</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>' +
            '<input autocomplete="one-time-code" data-id=""  class="form-control total-bayar-panjar trigger-input" type="text" value="' + greatFormatRupiahPayment(data.total_bayar_panjar) + ' " name = "total_pembayaran_panjar" readonly>' +
            '</b></td>'));

        table.find('tbody').append(newRow);

        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="10"><b>TOTAL PEMBAYARAN PANJAR TB</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>' +
            '<input autocomplete="one-time-code" data-id=""  class="form-control total-bayar-panjar-tb trigger-input" type="text" value="' + greatFormatRupiahPayment(data.total_bayar_panjar_tb) + ' " name = "total_pembayaran_panjar_tb" readonly>' +
            '</b></td>'));

        table.find('tbody').append(newRow);


        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="10"><b>TOTAL PEMBAYARAN PINJAMAN</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>' +
            '<input autocomplete="one-time-code" data-id=""  class="form-control total-bayar-pinjaman trigger-input" type="text" value="' + greatFormatRupiahPayment(data.total_bayar_pinjaman) + ' " name = "total_pembayaran_pinjaman" readonly>' +
            '</b></td>'));

        table.find('tbody').append(newRow);

        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="10"><b>GRAND TOTAL</b></td>'));
        // newRow.append($('<td style="text-align:center;"><b>' + greatFormatRupiahPayment(data.total_tagihan) + '</b></td>'));
        newRow.append($('<td style="text-align:center; min-width: 200px; width: 200px; max-width: 250px;"><b>' +
            '<input autocomplete="one-time-code" data-id="" class="form-control grand-total" type="text" value="' + greatFormatRupiahPayment(data.total_akhir) + '" name="grand_total" readonly>' +
            '</b></td>'));

        table.find('tbody').append(newRow);

        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="10"><b>GRAND TOTAL</b></td>'));
        // newRow.append($('<td style="text-align:center;"><b>' + greatFormatRupiahPayment(data.total_tagihan) + '</b></td>'));
        newRow.append($('<td style="text-align:center; min-width: 200px; width: 200px; max-width: 250px;"><b>' +
            '<input autocomplete="one-time-code" data-id="" class="form-control grand-total" type="text" value="' + greatFormatRupiahPayment(data.total_akhir) + '" name="grand_total" readonly>' +
            '</b></td>'));

        table.find('tbody').append(newRow);

    }

    function drawTable(data) {
        var no = 1;

        var TotalOrder = 0;
        var TotalDiterima = 0;
        var TotalDibayar = 0;
        var TotalHarga = 0;
        var TotalHargaPPH = 0;

        var TotalPanjar = 0;
        var TotalPanjarTB = 0;
        var TotalPinjaman = 0;

        const table = $('#dataTable');
        table.find('tbody').empty();
        // clear res
        listPoID.length = 0;
        listPoNo.length = 0;
        listPoDetailID.length = 0;
        listPenerimaanDetailID.length = 0;

        $.each(listPanjar, function(i, v) {
            TotalPanjar += v.bayar_panjar
        });

        $.each(listPanjarTB, function(i, v) {
            TotalPanjarTB += v.bayar_panjar_tb
        });

        $.each(listPinjaman, function(i, v) {
            TotalPinjaman += v.bayar_pinjaman
        });

        $.each(data, function(i, v) {
            listPoID.push({
                poID: v.rm_purchase_order_id
            });

            listPoNo.push({
                poNo: v.no_po
            });
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align:center;">').text(no++));
            newRow.append($('<td style="text-align:center;">').text(v.tanggal_PO));
            newRow.append($('<td style="text-align:center;">').text(v.no_po));
            newRow.append($('<td style="text-align:center;">').text(v.barang));
            newRow.append($('<td style="text-align:center;">').text(v.total_qty_diterima));
            newRow.append($('<td style="text-align:center;">').text(greatFormatRupiahPayment(v.total_tagihan_pph)));
            newRow.append($('<td style="text-align:center;">').text(greatFormatRupiahPayment(v.total_tagihan)));
            newRow.append($('<td style="text-align:center;">').text(greatFormatRupiahPayment(v.total_paid_pph)));
            newRow.append($('<td style="text-align:center;">').text(greatFormatRupiahPayment(v.total_paid)));
            newRow.append($('<td class="hidden" style="display:none;">').html(
                `
                        <input <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : ""  ?>  onchange="this.value = greatFormatRupiahPayment(this.value)"  oninput="limitInputBayar(this, ${v.sisa_tagihan})" autocomplete="one-time-code" data-id="${v.penerimaan_barang_detail_id}"  class="form-control pembayaran" type="text" value="${greatFormatRupiahPayment(v.sisa_tagihan)}" name = "pembayaran" style="height:40px">
                                `
            ));

            newRow.append($('<td style="text-align:center;">').html(
                `
                        <input <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : ""  ?>  onchange="this.value = greatFormatRupiahPayment(this.value)"  oninput="limitInputBayar(this, ${v.total_tagihan_pph})" autocomplete="one-time-code" data-id="${v.group_key}" class="form-control total_pph_po_dibayar" type="text" value="${greatFormatRupiahPayment(v.sisa_tagihan_pph)}" name = "total_pph_po_dibayar" style="height:40px">
                                `
            ));
            newRow.append($('<td style="text-align:center;">').html(
                `
                        <input <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : ""  ?>  onchange="this.value = greatFormatRupiahPayment(this.value)"  oninput="limitInputBayar(this, ${v.total_tagihan})" autocomplete="one-time-code" data-id="${v.group_key}" class="form-control total_po_dibayar" type="text" value="${greatFormatRupiahPayment(v.sisa_tagihan)}" name = "total_po_dibayar" style="height:40px">
                                `
            ));

            table.find('tbody').append(newRow);

            TotalOrder += parseFloat(v.total_order);
            TotalDiterima += parseFloat(v.total_diterima);
            TotalHarga += parseFloat(v.sisa_tagihan);
            TotalHargaPPH += parseFloat(v.sisa_tagihan_pph);

        });

        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="10"><b>TOTAL PEMBAYARAN PPH</b></td>'));
        newRow.append($('<td style="text-align:right;" ><b>' +
            '<input autocomplete="one-time-code" data-id="" onkeyup="validateTotalPPHPayment()" onblur="validateTotalPPHPayment()"  class="form-control total-pembayaran-pph trigger-input" onchange="this.value = greatFormatRupiahPayment(this.value)" onkeyup="preventNegativeInput(this)" type="text" value="' + greatFormatRupiahPayment(TotalHargaPPH) + '" name = "total_pembayaran_pph" readonly>' +
            '</b></td>'));
        table.find('tbody').append(newRow);

        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="10"><b>TOTAL PEMBAYARAN</b></td>'));
        newRow.append($('<td style="text-align:right;" ><b>' +
            '<input autocomplete="one-time-code" data-id="" onkeyup="validateTotalPayment()" onblur="validateTotalPayment()"  class="form-control total-pembayaran trigger-input" onchange="this.value = greatFormatRupiahPayment(this.value)" onkeyup="preventNegativeInput(this)" type="text" value="' + greatFormatRupiahPayment(TotalHarga) + '" name = "total_pembayaran" readonly>' +
            '</b></td>'));
        table.find('tbody').append(newRow);

        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="10"><b>POTONGAN/DISKON</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>' +
            '  <input <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'readonly' : '') : ""  ?> onchange="this.value = greatFormatRupiahPayment(this.value)" oninput="preventNegativeInput(this)" name="potongan" id="potongan" autocomplete="one-time-code" type="text" class="form-control trigger-input" placeholder="Nominal Pembayaran">' +
            '</b></td>'));

        table.find('tbody').append(newRow);

        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="10"><b>TOTAL PEMBAYARAN PANJAR</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>' +
            '<input autocomplete="one-time-code" data-id=""  class="form-control total-bayar-panjar trigger-input" type="text"  value="' + greatFormatRupiahPayment(TotalPanjar) + '" name = "total_pembayaran_panjar" readonly>' +
            '</b></td>'));

        table.find('tbody').append(newRow);


        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="10"><b>TOTAL PEMBAYARAN PANJAR TB</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>' +
            '<input autocomplete="one-time-code" data-id=""  class="form-control total-bayar-panjar-tb trigger-input" type="text"  value="' + greatFormatRupiahPayment(TotalPanjarTB) + '" name = "total_pembayaran_panjar_tb" readonly>' +
            '</b></td>'));

        table.find('tbody').append(newRow);


        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="10"><b>TOTAL PEMBAYARAN PINJAMAN</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>' +
            '<input autocomplete="one-time-code" data-id=""  class="form-control total-bayar-pinjaman trigger-input" type="text"  value="' + greatFormatRupiahPayment(TotalPinjaman) + '" name = "total_pembayaran_pinjaman" readonly>' +
            '</b></td>'));

        table.find('tbody').append(newRow);

        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="10"><b>GRAND TOTAL PPH</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>' +
            '<input autocomplete="one-time-code" data-id=""  class="form-control grand-total-pph" value="' + greatFormatRupiahPayment(TotalHargaPPH) + '" type="text"  value="" name = "grand_total_pph" readonly>' +
            '</b></td>'));

        table.find('tbody').append(newRow);

        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="10"><b>GRAND TOTAL</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>' +
            '<input autocomplete="one-time-code" data-id=""  class="form-control grand-total" type="text"  value="" name = "grand_total" readonly>' +
            '</b></td>'));

        table.find('tbody').append(newRow);

        if (TotalHarga > 0) {
            updateGrandTotal(TotalPanjarTB, TotalPinjaman, TotalPanjar)
        }

        if (TotalHargaPPH > 0) {
            updateGrandTotalPPH()
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

    function getPanjarSupplier() {
        var supplierId = $('#supplier_id option:selected').val();
        var pembayaranId = $("#id").val();
        $.ajax({
            url: `<?= base_url('/pembayaran-po-lokal/get-panjar'); ?>`,
            method: "GET",
            data: {
                supplier_id: supplierId,
                pembayaran_id: pembayaranId,
            },
            dataType: "json",
            success: function(res) {
                //after getting the data
                listPanjar = [];
                listPanjarTB = [];
                listPinjaman = [];
                listPanjar = res.data.panjarList; // Mengakses "panjarList"
                listPanjarTB = res.data.panjarTBList; // Mengakses "panjarTBList"
                listPinjaman = res.data.pinjamList; // Mengakses "pinjamList"

                // drawPaidPanjarTable(listPanjar);
                // drawPaidPinjamanTable(listPinjaman);
                // drawPaidPanjarTBTable(listPanjarTB);
                appendPanjarNo(listPanjar);
                appendPanjarTBNo(listPanjarTB);
                appendPinjamanNo(listPinjaman);

            }
        });
    }

    // append the panjar TB data
    function appendPanjarTBNo(data) {
        const tablePanjar = $('#dataTable-panjar-TB');
        tablePanjar.find('tbody').empty();
        tablePanjar.find('tfoot').empty();

        if (data.length > 0) {
            let no = 1;
            let found = false;
            $("#no_panjar").empty();
            tablePanjar.find('tbody').empty();

            $.each(data, function(i, v) {
                if (v.sisa_panjar_tb_number > 0 || v.pembayaran_id != "NULL") {
                    found = true;

                    var newRow = $('<tr style="color:whitesmoke;">');
                    newRow.append($('<td style="width: 10px;">').text(no++));
                    // newRow.append($('<td>').text(v.no_panjar_tb));
                    newRow.append($('<td>').text(formatDate(v.payment_date)));
                    newRow.append($('<td>').text((v.total_panjar_tb)));
                    newRow.append($('<td>').text((v.sisa_panjar_tb)));

                    newRow.append($('<td>').html(
                        `
                        <input  class="form-control bayar_panjar_tb" onchange="this.value = greatFormatRupiahPayment(this.value)" oninput="limitInputBayar(this, ${v.sisa_panjar_tb_number})" autocomplete="one-time-code" data-tgl_panjar_tb="${v.payment_date}" data-id="${v.panjar_tb_id}"  type="text" value="${v.bayar_panjar_tb}" name = "bayar_panjar_tb" style="height:40px">
                            `
                    ));
                    tablePanjar.find('tbody').append(newRow);
                }

                if (v.bayar_panjar_tb && v.pembayaran_id != "NULL") {
                    $("#coa-panjar-tb-section").show();

                    $("#id_panjar_tb").val(v.panjar_tb_id);
                    $("#no_panjar_tb").val(v.no_panjar_tb);

                    setTimeout(() => {
                        if (v.akun_kas_name) {
                            if ($("#akun_kas_panjar_tb").find("option[value='" + v.akun_kas_name + "']").length === 0) {
                                let newOptionKas = new Option(v.akun_kas_name, v.akun_kas_name, true, true);
                                $("#akun_kas_panjar_tb").append(newOptionKas).trigger("change");
                            } else {
                                $("#akun_kas_panjar_tb").val(v.akun_kas_name).trigger("change");
                            }
                        }

                        if (v.akun_selisih_name) {
                            if ($("#akun_selisih_panjar_tb").find("option[value='" + v.akun_selisih_name + "']").length === 0) {
                                let newOptionSelisih = new Option(v.akun_selisih_name, v.akun_selisih_name, true, true);
                                $("#akun_selisih_panjar_tb").append(newOptionSelisih).trigger("change");
                            } else {
                                $("#akun_selisih_panjar_tb").val(v.akun_selisih_name).trigger("change");
                            }
                        }

                    }, 1000);
                }

            });
            if (!found) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td colspan="8" style="text-align:center">Tidak Ada Panjar TB</td>'));
                tablePanjar.find('tbody').append(newRow);
            }
        } else {
            var newRow = $('<tr style="color:whitesmoke;">');
            newRow.append($('<td colspan="8" style="text-align:center">Tidak Ada Panjar TB</td>'));
            tablePanjar.find('tbody').append(newRow);
        }
    }

    // append the pinjaman data
    function appendPinjamanNo(data) {
        const tablePinjaman = $('#dataTable-pinjaman');
        tablePinjaman.find('tbody').empty();
        tablePinjaman.find('tfoot').empty();


        if (data.length > 0) {
            let no = 1;
            let found = false;
            $("#no_pinjaman").empty();
            tablePinjaman.find('tbody').empty();

            $.each(data, function(i, v) {
                console.log(v)
                if (v.sisa_pinjaman_number > 0 || v.pembayaran_id != "NULL") {
                    found = true;

                    var newRow = $('<tr style="color:whitesmoke;">');
                    newRow.append($('<td style="width: 10px;">').text(no++));
                    // newRow.append($('<td>').text(v.no_pinjaman));
                    newRow.append($('<td>').text(formatDate(v.payment_date)));
                    newRow.append($('<td>').text((v.total_pinjaman)));
                    newRow.append($('<td>').text((v.sisa_pinjaman)));

                    newRow.append($('<td>').html(
                        `
                        <input  class="form-control bayar_pinjaman" onchange="this.value = greatFormatRupiahPayment(this.value)" oninput="limitInputBayar(this, ${v.sisa_pinjaman_number})" autocomplete="one-time-code" data-tgl_pinjaman="${v.payment_date}" data-id="${v.pinjaman_id}"  type="text" value="${v.bayar_pinjaman}" name = "bayar_pinjaman" style="height:40px">
                            `
                    ));
                    tablePinjaman.find('tbody').append(newRow);
                }

                if (v.bayar_pinjaman && v.pembayaran_id != "NULL") {
                    $("#coa-pinjaman-section").show();

                    $("#id_pinjaman").val(v.pinjaman_id);
                    $("#no_pinjaman").val(v.no_pinjaman);
                    setTimeout(() => {
                        if (v.akun_kas_name) {
                            if ($("#akun_kas_pinjaman").find("option[value='" + v.akun_kas_name + "']").length === 0) {
                                let newOptionKas = new Option(v.akun_kas_name, v.akun_kas_name, true, true);
                                $("#akun_kas_pinjaman").append(newOptionKas).trigger("change");
                            } else {
                                $("#akun_kas_pinjaman").val(v.akun_kas_name).trigger("change");
                            }
                        }

                        if (v.akun_selisih_name) {
                            if ($("#akun_selisih_pinjaman").find("option[value='" + v.akun_selisih_name + "']").length === 0) {
                                let newOptionSelisih = new Option(v.akun_selisih_name, v.akun_selisih_name, true, true);
                                $("#akun_selisih_pinjaman").append(newOptionSelisih).trigger("change");
                            } else {
                                $("#akun_selisih_pinjaman").val(v.akun_selisih_name).trigger("change");
                            }
                        }

                    }, 1000);
                }

            });
            if (!found) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td colspan="8" style="text-align:center">Tidak Ada Pinjaman</td>'));
                tablePinjaman.find('tbody').append(newRow);
            }
        } else {
            var newRow = $('<tr style="color:whitesmoke;">');
            newRow.append($('<td colspan="8" style="text-align:center">Tidak Ada Pinjaman</td>'));
            tablePinjaman.find('tbody').append(newRow);
        }

    }


    //append the panjar data
    function appendPanjarNo(data) {
        const tablePanjar = $('#dataTable-panjar');
        tablePanjar.find('tbody').empty();
        tablePanjar.find('tfoot').empty();

        if (data.length > 0) {
            let no = 1;
            let found = false;
            $("#no_panjar").empty();
            tablePanjar.find('tbody').empty();

            $.each(data, function(i, v) {

                if (v.sisa_panjar_number > 0 || v.pembayaran_id != "NULL") {
                    found = true;

                    var newRow = $('<tr style="color:whitesmoke;">');
                    newRow.append($('<td style="width: 10px;">').text(no++));
                    // newRow.append($('<td>').text(v.no_panjar));
                    newRow.append($('<td>').text(formatDate(v.payment_date)));
                    newRow.append($('<td>').text((v.total_panjar)));
                    newRow.append($('<td>').text((v.sisa_panjar)));

                    newRow.append($('<td>').html(
                        `
                        <input  class="form-control bayar_panjar" onchange="this.value = greatFormatRupiahPayment(this.value)" oninput="limitInputBayar(this, ${v.sisa_panjar_number})" autocomplete="one-time-code" data-tgl_panjar="${v.payment_date}" data-id="${v.panjar_id}"  type="text" value="${v.bayar_panjar}" name = "bayar_panjar" style="height:40px">
                            `
                    ));
                    tablePanjar.find('tbody').append(newRow);
                }

                if (v.bayar_panjar && v.pembayaran_id != "NULL") {
                    $("#coa-panjar-section").show();

                    $("#id_panjar").val(v.panjar_id);
                    $("#no_panjar").val(v.no_panjar);

                    setTimeout(() => {
                        if (v.akun_kas_name) {
                            if ($("#akun_kas_panjar").find("option[value='" + v.akun_kas_name + "']").length === 0) {
                                let newOptionKas = new Option(v.akun_kas_name, v.akun_kas_name, true, true);
                                $("#akun_kas_panjar").append(newOptionKas).trigger("change");
                            } else {
                                $("#akun_kas_panjar").val(v.akun_kas_name).trigger("change");
                            }
                        }

                        if (v.akun_selisih_name) {
                            if ($("#akun_selisih_panjar").find("option[value='" + v.akun_selisih_name + "']").length === 0) {
                                let newOptionSelisih = new Option(v.akun_selisih_name, v.akun_selisih_name, true, true);
                                $("#akun_selisih_panjar").append(newOptionSelisih).trigger("change");
                            } else {
                                $("#akun_selisih_panjar").val(v.akun_selisih_name).trigger("change");
                            }
                        }

                    }, 1000);
                }
            });
            if (!found) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td colspan="8" style="text-align:center">Tidak Ada Panjar</td>'));
                tablePanjar.find('tbody').append(newRow);
            }
        } else {
            var newRow = $('<tr style="color:whitesmoke;">');
            newRow.append($('<td colspan="8" style="text-align:center">Tidak Ada Panjar</td>'));
            tablePanjar.find('tbody').append(newRow);
        }

    }


    //get PanjarID
    $("#no_panjar").change(function() {
        var panjar_id = [];
        $("select option:selected").each(function() {
            panjar_id.push($(this).val());
        });
        $.ajax({
            url: `<?= base_url('pembayaran-po-lokal/get-panjar-amount'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                panjar_id: panjar_id
            },
            dataType: "json",
            success: function(res) {
                // APPEND TO DROPDOWN
                appendPanjarAmount(res.data);
            },
            error: function(jqXHR, textStatus, errorThrown) {

            }
        });
    });

    function limitInputBayar(input, maxAmount) {
        var inputValue = input.value;
        var numericValue = inputValue.replace(/[^0-9.]/g, '');
        numericValue = numericValue.replace(/^0+/g, '');
        numericValue = numericValue.replace(/^\./g, '0.');

        // If numericValue is not a valid number, set it to '0'
        if (isNaN(parseFloat(numericValue))) {
            input.value = '0';
        } else {
            input.value = numericValue;
        }

        // Convert numericValue to a float for comparison
        if (parseFloat(numericValue) > maxAmount) {
            input.value = maxAmount;
        }
    }



    function formatDate(dateString) {

        let parts = dateString.split("-");
        let reversedParts = parts.reverse();
        let formattedDate = reversedParts.join("/");
        return formattedDate;
    }


    $(document).on("input", ".total_po_dibayar", function() {
        var sum = 0;
        $(".total_po_dibayar").each(function() {
            sum += destroyFormatRupiahPayment($(this).val());
        });
        $(".total-pembayaran").val(greatFormatRupiahPayment(sum));
        updateGrandTotal()
    });

    $(document).on("input", ".total_pph_po_dibayar", function() {
        var sum = 0;
        $(".total_pph_po_dibayar").each(function() {
            sum += destroyFormatRupiahPayment($(this).val());
        });
        $(".total-pembayaran-pph").val(greatFormatRupiahPayment(sum));
        updateGrandTotalPPH()
    });

    $(document).on("input", ".bayar_panjar", function() {
        var sum = 0;
        $(".bayar_panjar").each(function() {
            sum += destroyFormatRupiahPayment($(this).val());
        });
        $(".total-bayar-panjar").val(greatFormatRupiahPayment(sum));

        if ($(this).val() > 0) {
            $("#coa-panjar-section").show();

            $("#id_panjar").val($(this).data("id"));
            $("#tgl_panjar").val($(this).data("tgl_panjar"));
        } else {
            $("#coa-panjar-section").hide();
        }

        updateGrandTotal()
    });

    $(document).on("input", ".bayar_pinjaman", function() {
        var sum = 0;
        $(".bayar_pinjaman").each(function() {
            sum += destroyFormatRupiahPayment($(this).val());
        });
        $(".total-bayar-pinjaman").val(greatFormatRupiahPayment(sum));

        if ($(this).val() > 0) {
            $("#coa-pinjaman-section").show();

            $("#id_pinjaman").val($(this).data("id"));
            $("#tgl_pinjaman").val($(this).data("tgl_pinjaman"));
        } else {
            $("#coa-pinjaman-section").hide();
        }

        updateGrandTotal()
    });

    $(document).on("input", ".bayar_panjar_tb", function() {
        var sum = 0;
        $(".bayar_panjar_tb").each(function() {
            sum += destroyFormatRupiahPayment($(this).val());
        });
        $(".total-bayar-panjar-tb").val(greatFormatRupiahPayment(sum));

        if ($(this).val() > 0) {
            $("#coa-panjar-tb-section").show();

            $("#id_panjar_tb").val($(this).data("id"));
            $("#tgl_panjar_tb").val($(this).data("tgl_panjar_tb"));
        } else {
            $("#coa-panjar-tb-section").hide();
        }

        updateGrandTotal()
    });

    $(document).on("input", "#potongan", function() {
        var totalBayarPanjar = 0;
        if ($(".total-bayar-panjar").length) {
            totalBayarPanjar = destroyFormatRupiahPayment($(".total-bayar-panjar").val()) || 0;
        }

        var totalPembayaran = destroyFormatRupiahPayment($(".total-pembayaran").val()) || 0;
        var maxPotongan = totalPembayaran - totalBayarPanjar;
        limitInputBayar(this, maxPotongan);

        updateGrandTotal();
    });

    function updateGrandTotal(TotalPanjarTB, TotalPinjaman, TotalPanjar) {
        var totalBayarPanjar = 0;
        var totalBayarPinjaman = 0;
        var totalBayarPanjarTB = 0;

        if ($(".total-bayar-panjar").length) {
            totalBayarPanjar = destroyFormatRupiahPayment($(".total-bayar-panjar").val()) || 0;
        }

        if ($(".total-bayar-pinjaman").length) {
            totalBayarPinjaman = destroyFormatRupiahPayment($(".total-bayar-pinjaman").val()) || 0;
        }

        if ($(".total-bayar-panjar-tb").length) {
            totalBayarPanjarTB = destroyFormatRupiahPayment($(".total-bayar-panjar-tb").val()) || 0;
        }

        var totalPembayaran = destroyFormatRupiahPayment($(".total-pembayaran").val()) || 0;
        var potongan = destroyFormatRupiahPayment($("#potongan").val()) || 0;
        var total = totalPembayaran - (totalBayarPanjar + totalBayarPinjaman + totalBayarPanjarTB + potongan);

        $(".grand-total").val(greatFormatRupiahPayment(total));
    }

    function updateGrandTotalPPH() {
        var totalPembayaran = destroyFormatRupiahPayment($(".total-pembayaran-pph").val()) || 0;
        $(".grand-total-pph").val(greatFormatRupiahPayment(totalPembayaran));
    }

    function generateKeteranganPembayaran(data) {
        let totalQty = 0;
        let poNo = '';
        let barang = '';
        let supplier = $('#supplier_id option:selected').text().trim();

        $.each(data, function(i, v) {
            totalQty += parseFloat(v.total_qty_diterima);
            poNo = v.no_po;     
            barang = v.barang;
        });

        let keterangan = `Pembayaran ${barang} ${supplier} sebanyak ${totalQty.toFixed(2)} KGM (No: ${poNo})`;

        $('#keterangan').val(keterangan);
    }




    function generatePaymentNumber() {
        // Get selected divisi and bank values
        let id = $("#id").val();
        let jenisPembayaran = $("#jenis_pembayaran option:selected").text();
        let divisiId = $("#divisi_id option:selected").text();
        let divisiIdInt = $("#divisi_id option:selected").val();
        let bankId = $("#bank_id option:selected").val();
        let paymentMethod = $("#payment_method option:selected").val();
        let tanggalPembayaran = $("#payment_date").val();
        let noTransaksi = $("#no_bukti_pembayaran").val();

        // Only generate if this is a new record (empty detail)
            const csrfToken = '<?= csrf_token() ?>';
            const csrf = $(`[name="${csrfToken}"]`);

            // Build URL with query parameters
            let url = "<?= base_url('pembayaran-po-lokal-bb/generate-no-pembayaran'); ?>";
            url += `?jenisPembayaran=${encodeURIComponent(jenisPembayaran)}&paymentMethod=${encodeURIComponent(paymentMethod)}&divisiId=${encodeURIComponent(divisiId)}&bankId=${encodeURIComponent(bankId)}&tanggalPembayaran=${encodeURIComponent(tanggalPembayaran)}&id=${encodeURIComponent(id)}&noTransaksi=${encodeURIComponent(noTransaksi)}&divisiIdInt=${encodeURIComponent(divisiIdInt)}`;

            // Additional data if needed
            var formData = new FormData();
            formData.append("type", "Bahan Penolong");
            formData.append("payment_date", $("#payment_date").val());

            $(".no_bukti_pembayaran").attr("readonly", true);

            $.ajax({
                url: url,
                method: "GET",
                data: formData,
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                processData: false,
                contentType: false,
                success: function(response) {
                    csrf.val(response.token);
                    $(".no_bukti_pembayaran").val(response.paymentNo);
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan pada sistem',
                        text: 'Gagal menghasilkan nomor pembayaran otomatis',
                        confirmButtonColor: '#4e73df',
                    });
                    $(".no_bukti_pembayaran").attr("readonly", false);
                }
            });
    }


    function validateTotalPayment() {
        // Get the input values
        const totalPembayaran = parseFloat(document.getElementById('total_pembayaran').value) || 0;
        const grandTotal = parseFloat(document.getElementById('grand_total').value) || 0;

        // Check if payment exceeds grand total
        if (totalPembayaran > grandTotal) {
            // Show error message
            Swal.fire({
                icon: 'error',
                title: 'Jumlah pembayaran melebihi total',
                text: 'Total pembayaran tidak boleh melebihi ' + grandTotal.toLocaleString(),
                confirmButtonColor: '#4e73df',
            });

            // Reset the value to grand total
            document.getElementById('total_pembayaran').value = grandTotal;
        }
    }


    function validateTotalPPHPayment() {
        // Get the input values
        const totalPembayaran = parseFloat(document.getElementById('total_pembayaran_pph').value) || 0;
        const grandTotal = parseFloat(document.getElementById('grand_total_pph').value) || 0;

        // Check if payment exceeds grand total
        if (totalPembayaran > grandTotal) {
            // Show error message
            Swal.fire({
                icon: 'error',
                title: 'Jumlah pembayaran melebihi total',
                text: 'Total pembayaran tidak boleh melebihi ' + grandTotal.toLocaleString(),
                confirmButtonColor: '#4e73df',
            });

            // Reset the value to grand total
            document.getElementById('total_pembayaran_pph').value = grandTotal;
        }
    }
</script>

<?= $this->endSection(); ?>