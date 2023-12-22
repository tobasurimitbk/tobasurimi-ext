<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>

<section class="section section-form">
    <div class="section-header">
        <h1 class="title-name">Dokumen BC 4.0</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right root-form-view" href="<?= base_url("bea-cukai-bc-23"); ?>">
                Batal
            </a>
            <a class="btn btn-hide-form btn-discard float-right detail-barang-form-view" id="btn-batal-detail-barang-form-view" href="#">
                Batal
            </a>
            <?php if (!empty($bc40Detail)) : ?>
                <?php if ($bc40Detail['status_posting'] === "Belum Posting") : ?>
                    <button onclick="deleteAction()" class="btn btn-hapus delete-parent float-right root-form-view">
                        Hapus
                    </button>
                    <button onclick="postingAction()" class="btn btn-success posting-bc-23 float-right root-form-view">
                        Posting
                    </button>
                    <button class="btn btn-show-form btn-save float-right btn-submit-parent root-form-view btn-submit-root-form-view">
                        Simpan
                    </button>
                <?php else : ?>
                    <button class="btn btn-warning btn-print float-right" onclick="alert('Hello')">
                        Print
                    </button>
                <?php endif; ?>
            <?php else : ?>

                <button class="btn btn-show-form btn-save float-right btn-submit-parent root-form-view btn-submit-root-form-view">
                    Simpan
                </button>
            <?php endif; ?>
            <?php if (!empty($bc40Detail)) : ?>
                <?php if ($bc40Detail['status_posting'] != "Sudah Posting") : ?>
                    <button class="btn btn-show-form btn-save float-right detail-barang-form-view btn-simpan-detail-barang-form-view">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php else : ?>
                <button class="btn btn-show-form btn-save float-right detail-barang-form-view btn-simpan-detail-barang-form-view">
                    Simpan
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="root-form-view">
        <div class="card">
            <div class="card-body">
                <?php if (!empty($bc40Detail)) : ?>
                    <table width="100%" class="mb-3">
                        <tbody>
                            <tr style="color: black;">
                                <td width="150px"><b>Nomor BC 4.0</b></td>
                                <td width="10px">:</td>
                                <td><?= $bc40Detail['bc_no_lokal'] ?></td>
                            </tr>
                            <tr style="color: black; height: 20px;">
                                <td colspan="3"></td>
                            </tr>
                            <tr style="color: black;">
                                <td width="150px"><b>Nomor Aju</b></td>
                                <td width="30px">:</td>
                                <td><?= $bc40Detail['no_aju'] ?></td>
                            </tr>
                            <tr style="color: black; height: 20px;">
                                <td colspan="3"></td>
                            </tr>
                            <tr style="color: black;">
                                <td width="150px"><b>Status Dokumen</b></td>
                                <td width="30px">:</td>
                                <td><?= $bc40Detail['status_posting'] ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                <?php endif; ?>
                <form class="form-root">
                    <label class="form-label font-weight-bold lable-title mt-3">
                        Informasi Barang
                    </label>
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" class="id" id="id" value="<?= !empty($bc40Detail) ? encrypt($bc40Detail['id']) : '' ?>">
                    <input type="hidden" name="penerimaan_barang_id" class="penerimaan_barang_id" id="penerimaan_barang_id" value="<?= encrypt($lpb->id) ?>">
                    <div class="row mt-2">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input value="<?= $lpb->no_penerimaan_barang ?>" readonly autocomplete="one-time-code" type="text" disabled placeholder="" class="form-control">
                                <label>Nomor LPB</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input value="<?= $lpb->status_penerimaan ?> <?= $lpb->tipe_bahan == "BAKU" ? "BB" : "BP" ?>" readonly disabled autocomplete="one-time-code" type="text" placeholder="" class="form-control">
                                <label>Jenis PO</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> value="<?= date('d/m/Y', strtotime($lpb->tanggal)) ?>" autocomplete="one-time-code" type="text" placeholder="" name="tanggal_penerimaan_lpb" class="form-control tanggal_penerimaan_lpb" id="tanggal_penerimaan_lpb">
                                    <label>Tanggal Penerimaan</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-lpb" width="100%" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="text-align: center;width:10px;">No</th>
                                            <th style="text-align: center;">No PO</th>
                                            <th style="text-align: center;">Kode Barang</th>
                                            <th style="text-align: center;">Nama Barang</th>
                                            <th style="text-align: center;">Jmlh Diterima</th>
                                            <th style="text-align: center;">Harga</th>
                                            <th style="text-align: center;">Status </th>
                                            <th style="text-align: center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; ?>
                                        <?php foreach ($lpbDetail as $l) : ?>
                                            <?php $sudahDiisi = false; ?>
                                            <tr>
                                                <td style="text-align: center;"><?= $no++; ?></td>
                                                <td style="text-align: center;"><?= $l['po_no'] ?></td>
                                                <td style="text-align: center;"><?= $l['kode_barang'] ?></td>
                                                <td style="text-align: center;"><?= $l['nama_barang_dok'] ?></td>
                                                <td style="text-align: center;"><?= $l['jml_masuk'] ?></td>
                                                <td style="text-align: center;"><?= str_replace('Rp', '', toRupiah($l['sub_total'])) ?></td>
                                                <td style="text-align: center;" class="body-table-info-status-barang-root-view" data-id="<?= encrypt($l['id']) ?>">
                                                    <?php if (!empty($bc40Detail)) : ?>
                                                        <?php foreach ($bc23Json['detailBarangDok'] as $bj) : ?>
                                                            <?php if ($bj['penerimaan_barang_detail_id'] == encrypt($l['id'])) : ?>
                                                                <span class="badge badge-success">
                                                                    DOKUMEN SUDAH DIISI
                                                                </span>
                                                                <?php $sudahDiisi = true; ?>
                                                                <?php break; ?>
                                                            <?php endif; ?>

                                                        <?php endforeach; ?>
                                                        <?php if (!$sudahDiisi) : ?>
                                                            <span class="badge badge-danger">
                                                                DOKUMEN BELUM DIISI
                                                            </span>
                                                        <?php endif; ?>
                                                    <?php else : ?>
                                                        <span class="badge badge-danger">
                                                            DOKUMEN BELUM DIISI
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="text-align: center;">
                                                    <button type="button" data-id="<?= encrypt($l['id']) ?>" class="btn btn-warning btn-edit-dokumen-barang">
                                                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <label class="form-label font-weight-bold lable-title mt-3 mb-2">
                        Informasi Dokumen
                    </label>

                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="root_asal_data" value="Host to Host: S" name="root_asal_data" type="text" readonly class="root_asal_data form-control" placeholder="">
                                <label>Asal Pengiriman</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="root_asuransi" <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'readonly' : '') : '' ?> value="<?= !empty($bc40Detail) ? $bc40Detail['asuransi'] : '' ?>" name="root_asuransi" type="number" class="form-control root_asuransi" placeholder="">
                                <label>Asuransi</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input type="number" <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'readonly' : '') : '' ?> value="<?= !empty($bc40Detail) ? $bc40Detail['bruto'] : '' ?>" class="form-control root_bruto" name="root_bruto" id="root_bruto" placeholder="">
                                <label>Bruto</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'readonly' : '') : '' ?> value="<?= !empty($bc40Detail) ? $bc40Detail['cif'] : '' ?>" id="root_cif" name="root_cif" type="number" class="root_cif form-control" placeholder="">
                                <label>CIF</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '' ?> class="form-select root_kode_tujuan_tpb" id="root_kode_tujuan_tpb" name="root_kode_tujuan_tpb" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeTujunTpb as $k) : ?>
                                        <option <?= !empty($bc40Detail) ? (encrypt($bc40Detail['kode_tujuan_tpb']) == encrypt($k['description']) ? 'selected' : '') : '' ?> value="<?= encrypt($k['description']) ?>">
                                            <?= strtoupper($k['description']) . " ( " . strtoupper($k['value']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Tujuan TPB</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'readonly' : '') : '' ?> value="<?= !empty($bc40Detail) ? $bc40Detail['freight'] : '' ?>" type="number" class="form-control root_freight" name="root_freight" id="root_freight" placeholder="">
                                <label>Freight</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'readonly' : '') : '' ?> value="<?= !empty($bc40Detail) ? $bc40Detail['harga_penyerahan'] : '' ?>" id="root_harga_penyerahan" name="root_harga_penyerahan" type="number" class="root_harga_penyerahan form-control" placeholder="">
                                <label>Harga Penyerahan</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'readonly' : '') : '' ?> value="<?= !empty($bc40Detail) ? $bc40Detail['id_pengguna'] : '' ?>" id="root_id_pengguna" name="root_id_pengguna" type="text" class="form-control root_id_pengguna" placeholder="">
                                <label>Identitas Pengguna</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'readonly' : '') : '' ?> value="<?= !empty($bc40Detail) ? $bc40Detail['jabatan_pengusaha_ttd'] : '' ?>" id="root_jabatan_pengusaha_ttd" name="root_jabatan_pengusaha_ttd" type="text" class="form-control root_jabatan_pengusaha_ttd" placeholder="">
                                <label>Jabatan Pengusaha TPB</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'readonly' : '') : '' ?> value="<?= !empty($bc40Detail) ? $bc40Detail['jumlah_kontainer'] : '' ?>" type="number" class="form-control root_jumlah_kontainer" name="root_jumlah_kontainer" id="root_jumlah_kontainer" placeholder="">
                                <label>Jumlah Kontainer (Peti Kemas)</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="root_kode_dokumen" name="root_kode_dokumen" readonly value="40" type="text" class="form-control root_kode_dokumen" placeholder="">
                                <label>Kode Dokumen</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '' ?> class="form-select root_kode_kantor" id="root_kode_kantor" name="root_kode_kantor" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeKantor as $k) : ?>
                                        <option <?= !empty($bc40Detail) ? (encrypt($bc40Detail['kode_kantor']) == encrypt($k['kode']) ? 'selected' : '') : ''   ?> value="<?= encrypt($k['kode']) ?>">
                                            <?= strtoupper($k['kode']) . " ( " . strtoupper($k['kantor_name']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Kantor</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '' ?> class="form-select root_kode_tujuan_pengiriman" id="root_kode_tujuan_pengiriman" name="root_kode_tujuan_pengiriman" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeTujuanPengiriman as $k) : ?>
                                        <option <?= !empty($bc40Detail) ? (encrypt($bc40Detail['kode_tujuan_pengiriman']) == encrypt(json_decode($k['value'])[1]) ? 'selected' : '') : '' ?> value="<?= encrypt(json_decode($k['value'])[1]) ?>">
                                            <?= strtoupper($k['description']) . " ( " . json_decode($k['value'])[1] . " )" . " ( " . json_decode($k['value'])[0] . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Tujuan Pengiriman (Kode Tujuan Pengiriman) (Kode Dokumen)</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '' ?> value="<?= !empty($bc40Detail) ? $bc40Detail['kota_ttd'] : '' ?>" id="root_kota_ttd" name="root_kota_ttd" type="text" class="form-control root_kota_ttd" placeholder="">
                                <label>Kota Pembuatan Dokumen BC 4.0</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '' ?> value="<?= !empty($bc40Detail) ? $bc40Detail['nama_ttd'] : '' ?>" id="root_nama_ttd" name="root_nama_ttd" type="text" class="form-control root_nama_ttd" placeholder="">
                                <label>Nama Pengguna Pembuat Dokumen BC 4.0</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '' ?> value="<?= !empty($bc40Detail) ? $bc40Detail['netto'] : '' ?>" id="root_netto" name="root_netto" type="number" class="form-control root_netto" placeholder="">
                                <label>Netto</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '' ?> value="<?= !empty($bc40Detail) ? $bc40Detail['nik'] : '' ?>" id="root_nik" name="root_nik" type="number" class="form-control root_nik" placeholder="">
                                <label>NIK</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input readonly value="<?= !empty($bc40Detail) ? $bc40Detail['no_aju'] : '' ?>" id="root_no_aju" name="root_no_aju" type="text" class="form-control root_no_aju" placeholder="">
                                <label>Nomor Aju</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '' ?> value="<?= !empty($bc40Detail) ? $bc40Detail['seri'] : '' ?>" id="root_seri" name="root_seri" type="number" class="form-control root_seri" placeholder="">
                                <label>Seri</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '' ?> value="<?= !empty($bc40Detail) ? date('d/m/Y', strtotime($bc40Detail['tanggal_aju'])) : '' ?>" autocomplete="one-time-code" name="root_tanggal_aju" type="text" placeholder="" class="form-control root_tanggal_aju" id="root_tanggal_aju">
                                    <label>Tanggal Pengajuan Dokumen TPB</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '' ?> value="<?= !empty($bc40Detail) ? date('d/m/Y', strtotime($bc40Detail['tanggal_ttd'])) : '' ?>" autocomplete="one-time-code" name="root_tanggal_ttd" type="text" placeholder="" class="form-control root_tanggal_ttd" id="root_tanggal_ttd">
                                    <label>Tanggal Penanda-Tanganan Dokumen</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '' ?> value="<?= !empty($bc40Detail) ? $bc40Detail['user_portal'] : 'FEBRY SINTOSO' ?>" readonly id="root_user_portal" name="root_user_portal" type="text" class="form-control root_user_portal" placeholder="">
                                <label>Nama User Portal Ceisa 4.0</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '' ?> value="<?= !empty($bc40Detail) ? $bc40Detail['volume'] : '' ?>" id="root_volume" name="root_volume" type="number" maxlength="18" class="form-control root_volume" placeholder="">
                                <label>Volume</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '' ?> value="<?= !empty($bc40Detail) ? $bc40Detail['biaya_tambahan'] : '' ?>" id="root_biaya_tambahan" maxlength="18" name="root_biaya_tambahan" type="number" class="form-control root_biaya_tambahan" placeholder="">
                                <label>Biaya Tambahan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '' ?> value="<?= !empty($bc40Detail) ? $bc40Detail['biaya_pengurang'] : '' ?>" id="root_biaya_pengurang" maxlength="18" name="root_biaya_pengurang" type="number" class="form-control root_biaya_pengurang" placeholder="">
                                <label>Biaya Pengurang</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '' ?> value="<?= !empty($bc40Detail) ? $bc40Detail['vd'] : '' ?>" id="root_vd" maxlength="18" name="root_vd" type="number" class="form-control root_vd" placeholder="">
                                <label>Nilai VD</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= !empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '' ?> value="<?= !empty($bc40Detail) ? $bc40Detail['uang_muka'] : '' ?>" id="root_uang_muka" maxlength="18" name="root_uang_muka" type="number" class="form-control root_uang_muka" placeholder="">
                                <label>Uang Muka</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input readonly value="<?= !empty($bc40Detail) ? $bc40Detail['nilai_jasa'] : '' ?>" id="root_nilai_jasa" maxlength="18" name="root_nilai_jasa" type="number" class="form-control root_nilai_jasa" placeholder="">
                                <label>Nilai Jasa</label>
                            </div>
                        </div>
                    </div>
                </form>

                <label class="form-label font-weight-bold lable-title mt-3 mb-2">
                    Informasi Entitas
                </label>
                <form class="form-informasi-entitas">
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="entitas_alamat_entitas" name="entitas_alamat_entitas" type="text" class="form-control entitas_alamat_entitas" placeholder="">
                                <label>Alamat Importir/Pengusaha TPB</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input readonly id="entitas_kode_entitas" name="entitas_kode_entitas" type="text" class="form-control entitas_kode_entitas" value="Pengusaha (3)" placeholder="">
                                <label>Kode Entitas</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select entitas_kode_jenis_identitas" id="entitas_kode_jenis_identitas" name="entitas_kode_jenis_identitas" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeJenisIdentas as $k) : ?>
                                        <option value="<?= encrypt($k['description']) ?>">
                                            <?= strtoupper($k['description']) . " ( " . strtoupper($k['value']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Kode Jenis Identitas</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="entitas_nama_entitas" name="entitas_nama_entitas" type="text" class="form-control entitas_nama_entitas" placeholder="">
                                <label>Nama Importir / Pengusaha TPB</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="entitas_nib_entitas" name="entitas_nib_entitas" type="text" class="form-control entitas_nib_entitas" placeholder="">
                                <label>NIB Importir (Angka Pengenal Impor)</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="entitas_nomor_identitas" name="entitas_nomor_identitas" type="text" class="form-control entitas_nomor_identitas" placeholder="">
                                <label>Nomor Identitas Importir</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="entitas_nomor_ijin_entitas" name="entitas_nomor_ijin_entitas" type="text" class="form-control entitas_nomor_ijin_entitas" placeholder="">
                                <label>Nomor Ijin TPB</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" name="entitas_tanggal_ijin_entitas" type="text" placeholder="" class="form-control entitas_tanggal_ijin_entitas" id="entitas_tanggal_ijin_entitas">
                                    <label>Tanggal ijin TPB</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="entitas_seri_entitas" name="entitas_seri_entitas" type="number" class="form-control entitas_seri_entitas" placeholder="">
                                <label>Seri Entitas</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="row" style="float: right; margin-bottom:5px;">
                                <div class="col-sm" style="margin-right: -20px;">
                                    <button type="button" class="btn btn-add btn-block float-right btn-submit-informasi-entitas" style="float: right;">
                                        <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                                    </button>
                                </div>
                                <div class="col-sm">
                                    <button type="button" style="border-color: #e7323a !important; background-color: #e7323a !important; float: right;" onclick="resetFormInformasiEntitas()" class="btn btn-add btn-block float-right btn-reset-informasi-entitas">
                                        <i class="fa-solid fa-rotate-right mr-1"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-entitas" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center; width:10px;">No</th>
                                <th style="text-align: center;">Alamat Entitas</th>
                                <th style="text-align: center;">Kode Jenis Entitas</th>
                                <th style="text-align: center;">Nama Pengusaha TPB</th>
                                <th style="text-align: center;">Angka Pengenal Impor</th>
                                <th style="text-align: center;">Nomor Identitas Importir</th>
                                <th style="text-align: center;">Nomor Ijin TPB</th>
                                <th style="text-align: center;">Tanggal Ijin TPB</th>
                                <th style="text-align: center;">Seri Entitas</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <label class="form-label font-weight-bold lable-title mt-3 mb-2">
                    Informasi Kemasan
                </label>
                <form class="form-informasi-kemasan">
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="kemasan_jumlah_kemasan" name="kemasan_jumlah_kemasan" type="number" class="form-control kemasan_jumlah_kemasan" placeholder="">
                                <label>Jumlah Kemasan</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kemasan_kode_jenis_kemasan" id="kemasan_kode_jenis_kemasan" name="kemasan_kode_jenis_kemasan" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeJenisKemasan as $k) : ?>
                                        <option value="<?= encrypt($k['description']) ?>">
                                            <?= $k['description'] . " ( " . strtoupper($k['value']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Jenis Kemasan</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="kemasan_seri_kemasan" name="kemasan_seri_kemasan" type="number" class="form-control kemasan_seri_kemasan" placeholder="">
                                <label>Seri Kemasan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="kemasan_merk_kemasan" name="kemasan_merk_kemasan" type="text" class="form-control kemasan_merk_kemasan" placeholder="">
                                <label>Merk Kemasan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="row" style="float: right; margin-bottom:5px;">
                                <div class="col-sm" style="margin-right: -20px;">
                                    <button type="button" class="btn btn-add btn-block float-right btn-submit-informasi-kemasan" style="float: right;">
                                        <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                                    </button>
                                </div>
                                <div class="col-sm">
                                    <button type="button" style="border-color: #e7323a !important; background-color: #e7323a !important; float: right;" onclick="resetFormInformasiKemasan()" class="btn btn-add btn-block float-right btn-reset-informasi-kemasan">
                                        <i class="fa-solid fa-rotate-right mr-1"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-kemasan" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center; width:10px;">No</th>
                                <th style="text-align: center;">Jumlah Kemasan</th>
                                <th style="text-align: center;">Kode Jenis Kemasan</th>
                                <th style="text-align: center;">Seri Kemasan</th>
                                <th style="text-align: center;">Merk Kemasan</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <label class="form-label font-weight-bold lable-title mt-3 mb-2">
                    Informasi Kontainer (Opsional)
                </label>
                <form class="form-informasi-kontainer">
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kontainer_kode_tipe_kontainer" id="kontainer_kode_tipe_kontainer" name="kontainer_kode_tipe_kontainer" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeTipeKontainer as $k) : ?>
                                        <option value="<?= encrypt($k['value']) ?>">
                                            <?= $k['value'] . " ( " . strtoupper($k['description']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Tipe Kontainer</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kontainer_kode_ukuran_kontainer" id="kontainer_kode_ukuran_kontainer" name="kontainer_kode_ukuran_kontainer" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeUkuranKontainer as $k) : ?>
                                        <option value="<?= encrypt($k['value']) ?>">
                                            <?= $k['value'] . " ( " . strtoupper($k['description']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Ukuran Kontainer</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="kontainer_nomor_kontainer" name="kontainer_nomor_kontainer" type="text" class="form-control kontainer_nomor_kontainer" placeholder="">
                                <label>Nomor Kontainer</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="kontainer_seri_kontainer" name="kontainer_seri_kontainer" type="number" class="form-control kontainer_seri_kontainer" placeholder="">
                                <label>Seri Kontainer (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kontainer_kode_jenis_kontainer" id="kontainer_kode_jenis_kontainer" name="kontainer_kode_jenis_kontainer" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeJenisKontainer as $k) : ?>
                                        <option value="<?= encrypt($k['description']) ?>">
                                            <?= $k['description'] . " ( " . strtoupper($k['value']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Jenis Kontainer (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="row" style="float: right; margin-bottom:5px;">
                                <div class="col-sm" style="margin-right: -20px;">
                                    <button type="button" class="btn btn-add btn-block float-right btn-submit-informasi-kontainer" style="float: right;">
                                        <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                                    </button>
                                </div>
                                <div class="col-sm">
                                    <button type="button" style="border-color: #e7323a !important; background-color: #e7323a !important; float: right;" onclick="resetFormInformasiKontainer()" class="btn btn-add btn-block float-right btn-reset-informasi-kontainer">
                                        <i class="fa-solid fa-rotate-right mr-1"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-kontainer" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center; width:10px;">No</th>
                                <th style="text-align: center;">Tipe Kontainer</th>
                                <th style="text-align: center;">Ukuran Kontainer</th>
                                <th style="text-align: center;">Nomor Kontainer</th>
                                <th style="text-align: center;">Seri Kontainer</th>
                                <th style="text-align: center;">Jenis Kontainer</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <label class="form-label font-weight-bold lable-title mt-3 mb-2">
                    Informasi Dokumen Pelengkap
                </label>
                <form class="form-dokumen-pelengkap">
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="dokumen_pelengkap_kode_dokumen" name="dokumen_pelengkap_kode_dokumen" type="text" class="form-control dokumen_pelengkap_kode_dokumen" placeholder="" readonly value="Dokumen Invoice (380)">
                                <label>Kode Dokumen</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="dokumen_pelengkap_nomor_dokumen" name="dokumen_pelengkap_nomor_dokumen" type="text" class="form-control dokumen_pelengkap_nomor_dokumen" placeholder="">
                                <label>Nomor Dokumen</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="dokumen_pelengkap_seri_dokumen" name="dokumen_pelengkap_seri_dokumen" type="number" class="form-control dokumen_pelengkap_seri_dokumen" placeholder="">
                                <label>Seri Dokumen</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" name="dokumen_pelengkap_tanggal_dokumen" type="text" placeholder="" class="form-control dokumen_pelengkap_tanggal_dokumen" id="dokumen_pelengkap_tanggal_dokumen">
                                    <label>Tanggal Dokumen</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="row" style="float: right; margin-bottom:5px;">
                                <div class="col-sm" style="margin-right: -20px;">
                                    <button type="button" class="btn btn-add btn-block float-right btn-submit-informasi-dokumen-pelengkap" style="float: right;">
                                        <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                                    </button>
                                </div>
                                <div class="col-sm">
                                    <button type="button" style="border-color: #e7323a !important; background-color: #e7323a !important; float: right;" onclick="resetFormInformasiDokumenPelengkap()" class="btn btn-add btn-block float-right btn-reset-informasi-dokumen-pelengkap">
                                        <i class="fa-solid fa-rotate-right mr-1"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-dokumen-pelengkap" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center; width:10px;">No</th>
                                <th style="text-align: center;">Nomor Dokumen</th>
                                <th style="text-align: center;">Seri Dokumen</th>
                                <th style="text-align: center;">Tanggal Dokumen</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <label class="form-label font-weight-bold lable-title mt-3 mb-2">
                    Informasi Pengangkut
                </label>
                <form class="form-informasi-pengangkut">
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="pengangkut_kode_bendera" name="pengangkut_kode_bendera" type="text" class="form-control pengangkut_kode_bendera" placeholder="">
                                <label>Kode Bendera</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="pengangkut_nama_sarana_pengangkut" name="pengangkut_nama_sarana_pengangkut" type="text" class="form-control pengangkut_nama_sarana_pengangkut" placeholder="">
                                <label>Nama Sarana Pengangkut</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="pengangkut_nomor_pengangkut" name="pengangkut_nomor_pengangkut" type="text" class="form-control pengangkut_nomor_pengangkut" placeholder="">
                                <label>Nomor Pengangkut (Voy Flight)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select pengangkut_kode_cara_angkut" id="pengangkut_kode_cara_angkut" name="pengangkut_kode_cara_angkut" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodePengangkutan as $k) : ?>
                                        <option value="<?= encrypt($k['description']) ?>">
                                            <?= $k['description'] . " ( " . strtoupper($k['value']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Cara Pengangkutan</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="pengangkut_seri_pengangkut" name="pengangkut_seri_pengangkut" type="number" class="form-control pengangkut_seri_pengangkut" placeholder="">
                                <label>Seri Data Pengangkut</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="row" style="float: right; margin-bottom:5px;">
                                <div class="col-sm" style="margin-right: -20px;">
                                    <button type="button" class="btn btn-add btn-block float-right btn-submit-informasi-pengangkut" style="float: right;">
                                        <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                                    </button>
                                </div>
                                <div class="col-sm">
                                    <button type="button" style="border-color: #e7323a !important; background-color: #e7323a !important; float: right;" onclick="resetFormInformasiPengangkut()" class="btn btn-add btn-block float-right btn-reset-informasi-pengangkut">
                                        <i class="fa-solid fa-rotate-right mr-1"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-pengangkut" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center; width:10px;">No</th>
                                <th style="text-align: center;">Kode Bendera</th>
                                <th style="text-align: center;">Nama Pengangkut</th>
                                <th style="text-align: center;">Nomor Pengangkut</th>
                                <th style="text-align: center;">Cara Angkut</th>
                                <th style="text-align: center;">Seri Pengangkut</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <br />
            <!-- <label class="form-label mt-2">
                Dengan ini saya menyatakan bertanggung jawab atas kebenaran hal-hal yang diberitahukan dalam pemberitahuan pabean ini.
            </label> -->
        </div>
    </div>

    <!-- DETAIL BARANG VIEW -->
    <div class="detail-barang-form-view">
        <div class="card">
            <div class="card-body">
                <table width="100%" class="mb-3">
                    <tbody>
                        <tr style="color: black;">
                            <td width="150px"><b>Nama Barang</b></td>
                            <td width="10px">:</td>
                            <td id="detail-barang-form-nama-barang"></td>
                        </tr>
                        <tr style="color: black; height: 20px;">
                            <td colspan="3"></td>
                        </tr>
                        <tr style="color: black;">
                            <td width="150px"><b>Nomor PO</b></td>
                            <td width="30px">:</td>
                            <td id="detail-barang-form-no-po"></td>
                        </tr>
                    </tbody>
                </table>
                <hr style="color: black;">
                <label class="form-label font-weight-bold lable-title mb-3">
                    Dokumen Barang
                </label>
                <form class="form-detail-dokumen-barang">
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="asuransi" name="asuransi" type="number" class="asuransi form-control" placeholder="">
                                <label>Nilai Asuransi</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="harga_cif" name="harga_cif" type="number" class="harga_cif form-control" placeholder="">
                                <label>Harga Cif</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="diskon" name="diskon" readonly type="text" class="diskon form-control" placeholder="">
                                <label>Diskon</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="fob" name="fob" type="number" class="fob form-control" placeholder="">
                                <label>Free On Board</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="freight" name="freight" type="number" class="form-control freight" placeholder="">
                                <label>Freight</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> type="text" name="harga_ekspor" class="form-control" id="harga_ekspor" placeholder="">
                                <label>Harga Ekspor</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="harga_penyerahan_barang" name="harga_penyerahan_barang" type="number" class="harga_penyerahan_barang form-control" placeholder="">
                                <label>Harga Penyerahan Barang</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="harga_satuan_barang" readonly name="harga_satuan_barang" type="text" class="form-control harga_satuan_barang" placeholder="">
                                <label>Harga Satuan Barang</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> type="text" class="form-control isi_per_kemasan" name="isi_per_kemasan" id="isi_per_kemasan" placeholder="">
                                <label>Isi Per Kemasan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="jumlah_kemasan" name="jumlah_kemasan" type="number" class="jumlah_kemasan form-control" placeholder="">
                                <label>Jumlah Kemasan</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="jumlah_satuan" name="jumlah_satuan" type="number" class="form-control jumlah_satuan" placeholder="">
                                <label>Jumlah Satuan</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="kode_barang" readonly name="kode_barang" type="text" class="form-control kode_barang" placeholder="">
                                <label>Kode Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> class="form-select kode_dokumen" id="kode_dokumen" name="kode_dokumen" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeDokumen as $k) : ?>
                                        <option value="<?= encrypt($k['description']) ?>">
                                            <?= strtoupper($k['description']) . " ( " . strtoupper($k['value']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Dokumen</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> class="form-select kode_kategori_barang" id="kode_kategori_barang" name="kode_kategori_barang" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeKategoriBarang as $k) : ?>
                                        <option value="<?= encrypt(str_replace(']', '', explode(',', $k['description'])[1])) ?>">
                                            <?= str_replace(']', '', explode(',', $k['description'])[1]) . " ( " . strtoupper($k['value']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Kategori Barang</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> class="form-select kode_jenis_kemasan" id="kode_jenis_kemasan" name="kode_jenis_kemasan" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeJenisKemasan as $k) : ?>
                                        <option value="<?= encrypt($k['description']) ?>">
                                            <?= $k['description'] . " ( " . strtoupper($k['value']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Jenis Kemasan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> class="form-select kode_negara_asal" id="kode_negara_asal" name="kode_negara_asal" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeNegaraAsal as $k) : ?>
                                        <option value="<?= encrypt($k['code']) ?>">
                                            <?= $k['code'] . " ( " . strtoupper($k['country_name']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Kode Negara Asal</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> class="form-select kode_perhitungan" id="kode_perhitungan" name="kode_perhitungan" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodePerhitungan as $k) : ?>
                                        <option value="<?= encrypt($k['value']) ?>">
                                            <?= $k['description'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Kode Perhitungan</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> class="form-select kode_satuan_barang" id="kode_satuan_barang" name="kode_satuan_barang" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php if (!empty($bc40Detail)) : ?>
                                        <?php foreach ($kodeSatuanBarang as $k) : ?>
                                            <option value="<?= $k['id'] ?>">
                                                <?= $k['text'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <label style="z-index: 1;">Kode Satuan Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="merk_barang" name="merk_barang" type="text" class="form-control merk_barang" placeholder="">
                                <label>Merk Barang</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="netto" name="netto" type="number" class="form-control netto" placeholder="">
                                <label>Netto</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="nilai_barang" name="nilai_barang" type="number" class="form-control nilai_barang" placeholder="">
                                <label>Nilai Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="nilai_tambah" name="nilai_tambah" type="text" class="form-control nilai_tambah" placeholder="">
                                <label>Nilai Tambah</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="pos_tarif" name="pos_tarif" type="text" class="form-control pos_tarif" placeholder="">
                                <label>Pos Tarif</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="seri_barang" name="seri_barang" type="number" class="form-control seri_barang" placeholder="">
                                <label>Seri Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="spesifikasi_lain" name="spesifikasi_lain" type="text" class="form-control spesifikasi_lain" placeholder="">
                                <label>Spesifikasi Lain</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="tipe_barang" name="tipe_barang" type="text" class="form-control tipe_barang" placeholder="">
                                <label>Tipe Barang</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="ukuran_barang" name="ukuran_barang" type="text" class="form-control ukuran_barang" placeholder="">
                                <label>Ukuran Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="ndpm" name="ndpm" type="number" class="form-control ndpm" placeholder="">
                                <label>NDPM</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="cif_rupiah" name="cif_rupiah" type="number" class="form-control cif_rupiah" placeholder="">
                                <label>Cif Rupiah</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="harga_perolehan_barang" name="harga_perolehan_barang" type="text" class="form-control harga_perolehan_barang" placeholder="">
                                <label>Harga Perolehan Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> class="form-select kode_asal_bahan_baku" id="kode_asal_bahan_baku" name="kode_asal_bahan_baku" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeAsalBahanBaku as $k) : ?>
                                        <option value="<?= encrypt($k['value']) ?>">
                                            (<?= $k['value'] ?>) <?= $k['description'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Kode Asal Bahan Baku</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input <?= (!empty($bc40Detail) ? ($bc40Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '') ?> id="uraian" name="uraian" type="text" class="form-control uraian" placeholder="">
                                <label>Uraian</label>
                            </div>
                        </div>
                    </div>
                </form>

                <label class="form-label font-weight-bold lable-title mb-3">
                    Barang Tarif
                </label>
                <form class="form-barang-tarif" role="form" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select barang_tarif_kode_jenis_tarif" id="barang_tarif_kode_jenis_tarif" name="barang_tarif_kode_jenis_tarif" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeJenisTarif as $k) : ?>
                                        <option value="<?= encrypt($k['value']) ?>">
                                            (<?= $k['value'] ?>) <?= $k['description'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Kode Jenis Tarif</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="jumlah_satuan_bm" name="jumlah_satuan_bm" type="number" class="form-control jumlah_satuan_bm" placeholder="">
                                <label>Jumlah Satuan Barang Tarif BM</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select barang_tarif_kode_fasilitas_tarif" id="barang_tarif_kode_fasilitas_tarif" name="barang_tarif_kode_fasilitas_tarif" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeFasilitasTarif as $k) : ?>
                                        <option value="<?= encrypt($k['value']) ?>">
                                            (<?= $k['value'] ?>) <?= $k['description'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Kode Fasilitas Tarif</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select barang_tarif_kode_satuan_barang" id="barang_tarif_kode_satuan_barang" name="barang_tarif_kode_satuan_barang" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label style="z-index: 1;">Kode Satuan Barang</label>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-floating mb-3">
                                <input id="kode_jenis_pungutan" name="kode_jenis_pungutan" type="text" class="form-control kode_jenis_pungutan" placeholder="" value="BM (BEA MASUK)" readonly>
                                <label>Kode Jenis Pungutan</label>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-floating mb-3">
                                <input id="nilai_bayar" name="nilai_bayar" type="number" class="form-control nilai_bayar" placeholder="">
                                <label>Nilai Bayar BM (Bea Masuk)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-floating mb-3">
                                <input id="nilai_fasilitas" name="nilai_fasilitas" type="number" class="form-control nilai_fasilitas" placeholder="">
                                <label>Nilai Fasilitas</label>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-floating mb-3">
                                <input id="nilai_sudah_dilunasi" name="nilai_sudah_dilunasi" type="number" class="form-control nilai_sudah_dilunasi" placeholder="">
                                <label>Nilai Sudah Dilunasi</label>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-floating mb-3">
                                <input id="barang_tarif_seri_barang" name="barang_tarif_seri_barang" type="number" class="form-control barang_tarif_seri_barang" placeholder="">
                                <label>Seri Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-floating mb-3">
                                <input id="tarif_bm" name="tarif_bm" type="number" class="form-control tarif_bm" placeholder="">
                                <label>Tarif BM(Bea Masuk)</label>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-floating mb-3">
                                <input id="tarif_fasilitas" name="tarif_fasilitas" type="number" class="form-control tarif_fasilitas" placeholder="">
                                <label>Tarif Fasilitas</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="row" style="float: right; margin-bottom:5px;">
                                <div class="col-sm" style="margin-right: -20px;">
                                    <button type="button" class="btn btn-add btn-block float-right btn-submit-barang-tarif" style="float: right;">
                                        <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                                    </button>
                                </div>
                                <div class="col-sm">
                                    <button type="button" style="border-color: #e7323a !important; background-color: #e7323a !important; float: right;" onclick="resetFormBarangTarif()" class="btn btn-add btn-block float-right btn-reset-barang-tarif">
                                        <i class="fa-solid fa-rotate-right mr-1"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-barang-tarif" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center; width:10px;">No</th>
                                <th style="text-align: center;">Kode Jenis Tarif</th>
                                <th style="text-align: center;">Jml Satuan Barang Tarif BM</th>
                                <th style="text-align: center;">Kode Satuan Barang</th>
                                <th style="text-align: center;">Nilai Bayar BM</th>
                                <th style="text-align: center;">Nilai Fasilitas</th>
                                <th style="text-align: center;">Nilai Sudah Dilunasi</th>
                                <th style="text-align: center;">Seri Barang</th>
                                <th style="text-align: center;">Tarif BM</th>
                                <th style="text-align: center;">Tarif Fasilitas</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <label class="form-label font-weight-bold lable-title mt-3 mb-3">
                    Barang Dokumen
                </label>
                <form class="form-barang-dokumen">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-floating mb-3">
                                <input id="no_seri_dokumen" name="no_seri_dokumen" type="text" class="form-control no_seri_dokumen" placeholder="">
                                <label>Nomor Seri Dokumen</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="row" style="float: right; margin-bottom:5px;">
                                <div class="col-sm">
                                    <button type="button" class="btn btn-add btn-block float-right btn-submit-no-seri-dokumen" style="float: right; margin-right:-15px;">
                                        <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-barang-dokumen" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;">No</th>
                                <th style="text-align: center;">Nomor Seri Dokumen</th>
                                <th style="text-align: center; width:10px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // CSRF
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    // INIT FORM VIEW
    $('.detail-barang-form-view').hide();
</script>

<?= $this->endSection(); ?>