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
        <h1 class="title-name">Dokumen BC 2.3</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right root-form-view" href="<?= base_url("bea-cukai-bc-23"); ?>">
                Batal
            </a>
            <a class="btn btn-hide-form btn-discard float-right detail-barang-form-view" id="btn-batal-detail-barang-form-view" href="#">
                Batal
            </a>
            <?php if (!empty($bc23Detail)) : ?>
                <?php if ($bc23Detail['status_posting'] === "Belum Posting") : ?>
                    <button class="btn btn-hapus delete-parent float-right">
                        Hapus
                    </button>
                    <button class="btn btn-success posting-spp float-right">
                        Posting
                    </button>
                    <button class="btn btn-show-form btn-save float-right btn-submit-parent root-form-view">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php else : ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent root-form-view btn-submit-root-form-view">
                    Simpan
                </button>
            <?php endif; ?>
            <button class="btn btn-show-form btn-save float-right detail-barang-form-view btn-simpan-detail-barang-form-view">
                Simpan
            </button>
        </div>
    </div>

    <div class="root-form-view">
        <div class="card">
            <div class="card-body">
                <?php if (!empty($bc23Detail)) : ?>
                    <table width="100%" class="mb-3">
                        <tbody>
                            <tr style="color: black;">
                                <td width="150px"><b>Nomor BC 2.3</b></td>
                                <td width="10px">:</td>
                                <td><?= $bc23Detail['bc_no_lokal'] ?></td>
                            </tr>
                            <tr style="color: black; height: 20px;">
                                <td colspan="3"></td>
                            </tr>
                            <tr style="color: black;">
                                <td width="150px"><b>Status Dokumen</b></td>
                                <td width="30px">:</td>
                                <td><?= $bc23Detail['status_posting'] ?></td>
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
                    <input type="hidden" name="id" class="id" id="id" value="<?= !empty($bc23Detail) ? $bc23Detail['id'] : '' ?>">
                    <input type="hidden" name="penerimaan_barang_id" class="penerimaan_barang_id" id="penerimaan_barang_id" value="<?= $lpb->id ?>">
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
                                    <input value="<?= date('d/m/Y', strtotime($lpb->tanggal)) ?>" autocomplete="one-time-code" type="text" placeholder="" name="tanggal_penerimaan_lpb" class="form-control tanggal_penerimaan_lpb" id="tanggal_penerimaan_lpb">
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
                                            <tr>
                                                <td style="text-align: center;"><?= $no++; ?></td>
                                                <td style="text-align: center;"><?= $l['po_no'] ?></td>
                                                <td style="text-align: center;"><?= $l['kode_barang'] ?></td>
                                                <td style="text-align: center;"><?= $l['nama_barang_dok'] ?></td>
                                                <td style="text-align: center;"><?= $l['jml_masuk'] ?></td>
                                                <td style="text-align: center;"><?= str_replace('Rp', '', toRupiah($l['sub_total'])) ?></td>
                                                <td style="text-align: center;" class="body-table-info-status-barang-root-view" data-id="<?= encrypt($l['id']) ?>">
                                                    <span class="badge badge-danger">
                                                        DOKUMEN BELUM DIISI
                                                    </span>
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
                                <input id="root_asuransi" name="root_asuransi" type="number" class="form-control root_asuransi" placeholder="">
                                <label>Asuransi</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control root_bruto" name="root_bruto" id="root_bruto" placeholder="">
                                <label>Bruto</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="root_cif" name="root_cif" type="number" class="root_cif form-control" placeholder="">
                                <label>CIF</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="root_fob" name="root_fob" type="number" class="form-control root_fob" placeholder="">
                                <label>FOB</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control root_freight" name="root_freight" id="root_freight" placeholder="">
                                <label>Freight</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="root_harga_penyerahan" name="root_harga_penyerahan" type="number" class="root_harga_penyerahan form-control" placeholder="">
                                <label>Harga Penyerahan</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="root_jabatan_pengusaha_ttd" name="root_jabatan_pengusaha_ttd" type="text" class="form-control root_jabatan_pengusaha_ttd" placeholder="">
                                <label>Jabatan Pengusaha TPB</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control root_jumlah_kontainer" name="root_jumlah_kontainer" id="root_jumlah_kontainer" placeholder="">
                                <label>Jumlah Kontainer</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select root_kode_asuransi" id="root_kode_asuransi" name="root_kode_asuransi" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeAsuransi as $k) : ?>
                                        <option value="<?= encrypt($k['value']) ?>">
                                            <?= strtoupper($k['value']) . " ( " . strtoupper($k['description']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Asuransi</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="root_kode_dokumen" name="root_kode_dokumen" readonly value="23" type="text" class="form-control root_kode_dokumen" placeholder="">
                                <label>Kode Dokumen</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select root_kode_incoterm" id="root_kode_incoterm" name="root_kode_incoterm" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeIncoterm as $k) : ?>
                                        <option value="<?= encrypt($k['value']) ?>">
                                            <?= strtoupper($k['value']) . " ( " . strtoupper($k['description']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Incoterm</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select root_kode_kantor" id="root_kode_kantor" name="root_kode_kantor" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeKantor as $k) : ?>
                                        <option value="<?= encrypt($k['kode']) ?>">
                                            <?= strtoupper($k['kode']) . " ( " . strtoupper($k['kantor_name']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Kantor</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select root_kode_kantor_bongkar" id="root_kode_kantor_bongkar" name="root_kode_kantor_bongkar" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeKantor as $k) : ?>
                                        <option value="<?= encrypt($k['kode']) ?>">
                                            <?= strtoupper($k['kode']) . " ( " . strtoupper($k['kantor_name']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Kantor Bongkar</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="root_kode_pelabuhan_bongkar" name="root_kode_pelabuhan_bongkar" type="text" class="form-control root_kode_pelabuhan_bongkar" placeholder="">
                                <label>Kode Pelabuhan Bongkar</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="root_kode_pelabuhan_muat" name="root_kode_pelabuhan_muat" type="text" class="form-control root_kode_pelabuhan_muat" placeholder="">
                                <label>Kode Pelabuhan Muat</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="root_kode_pelabuhan_transit" name="root_kode_pelabuhan_muat" type="text" class="form-control root_kode_pelabuhan_muat" placeholder="">
                                <label>Kode Pelabuhan Transit</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="root_kode_tps" name="root_kode_tps" type="text" class="form-control root_kode_tps" placeholder="">
                                <label>Kode TPS</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select root_kode_tujuan_tpb" id="root_kode_tujuan_tpb" name="root_kode_tujuan_tpb" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeTujunTpb as $k) : ?>
                                        <option value="<?= encrypt($k['description']) ?>">
                                            <?= strtoupper($k['description']) . " ( " . strtoupper($k['value']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Tujuan TPB</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select root_kode_tutup_pu" id="root_kode_tutup_pu" name="root_kode_tutup_pu" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeTutupPu as $k) : ?>
                                        <option value="<?= encrypt($k['value']) ?>">
                                            <?= strtoupper($k['value']) . " ( " . strtoupper($k['description']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Tutup PU</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select root_kode_valuta" id="root_kode_valuta" name="root_kode_valuta" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeValuta as $k) : ?>
                                        <option value="<?= encrypt($k['value']) ?>">
                                            <?= strtoupper($k['value']) . " ( " . strtoupper($k['description']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Valuta</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="root_kota_ttd" name="root_kota_ttd" type="text" class="form-control root_kota_ttd" placeholder="">
                                <label>Kota Pembuatan Dokumen BC 2.3</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="root_nama_ttd" name="root_nama_ttd" type="text" class="form-control root_nama_ttd" placeholder="">
                                <label>Nama Pengguna Pembuat Dokumen BC 2.3</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="root_ndpbm" name="root_ndpbm" type="number" class="form-control root_ndpbm" placeholder="">
                                <label>NDPBM</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="root_netto" name="root_netto" type="number" class="form-control root_netto" placeholder="">
                                <label>Netto</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select root_nik" id="root_nik" name="root_nik" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeJenisAPI as $k) : ?>
                                        <option value="<?= encrypt($k['value']) ?>">
                                            <?= strtoupper($k['description']) . " ( " . strtoupper($k['value']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">NIK (Jenis API)</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="root_nilai_barang" name="root_nilai_barang" type="number" class="form-control root_nilai_barang" placeholder="">
                                <label>Nilai Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="root_no_aju" name="root_no_aju" type="text" maxlength="26" class="form-control root_no_aju" placeholder="">
                                <label>Nomor Aju</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="root_no_bc_11" name="root_no_bc_11" type="text" maxlength="6" class="form-control root_no_bc_11" placeholder="">
                                <label>Nomor BC 1.1</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="root_pos_bc_11" name="root_pos_bc_11" type="text" maxlength="4" class="form-control root_pos_bc_11" placeholder="">
                                <label>Pos BC 1.1</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="root_seri" name="root_seri" type="number" class="form-control root_seri" placeholder="">
                                <label>Seri</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="root_sub_pos_bc_11" maxlength="8" name="root_sub_pos_bc_11" type="number" class="form-control root_sub_pos_bc_11" placeholder="">
                                <label>Sub Pos BC 1.1</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" name="root_tanggal_bc_11" type="text" placeholder="" class="form-control root_tanggal_bc_11" id="root_tanggal_bc_11">
                                    <label>Tanggal BC 1.1</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" name="root_tanggal_tiba" type="text" placeholder="" class="form-control root_tanggal_tiba" id="root_tanggal_tiba">
                                    <label>Tanggal Perkiraan Tiba</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" name="root_tanggal_ttd" type="text" placeholder="" class="form-control root_tanggal_ttd" id="root_tanggal_ttd">
                                    <label>Tanggal Penanda-Tanganan Dokumen</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="root_biaya_tambahan" maxlength="24" name="root_biaya_tambahan" type="number" class="form-control root_biaya_tambahan" placeholder="">
                                <label>Biaya Tambahan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="root_biaya_pengurang" maxlength="24" name="root_biaya_pengurang" type="number" class="form-control root_biaya_pengurang" placeholder="">
                                <label>Biaya Pengurang</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select root_kode_kena_pajak" id="root_kode_kena_pajak" name="root_kode_kena_pajak" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeKenaPajak as $k) : ?>
                                        <option value="<?= encrypt($k['value']) ?>">
                                            <?= strtoupper($k['value']) . " ( " . strtoupper($k['description']) . " )" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Kode Kena Pajak (Opsional)</label>
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
                                <input id="asuransi" name="asuransi" type="number" class="asuransi form-control" placeholder="">
                                <label>Nilai Asuransi</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="harga_cif" name="harga_cif" type="number" class="harga_cif form-control" placeholder="">
                                <label>Harga Cif</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="diskon" name="diskon" readonly type="text" class="diskon form-control" placeholder="">
                                <label>Diskon</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="fob" name="fob" type="number" class="fob form-control" placeholder="">
                                <label>Free On Board</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="freight" name="freight" type="number" class="form-control freight" placeholder="">
                                <label>Freight</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input type="text" name="harga_ekspor" class="form-control" id="harga_ekspor" placeholder="">
                                <label>Harga Ekspor</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="harga_penyerahan_barang" name="harga_penyerahan_barang" type="number" class="harga_penyerahan_barang form-control" placeholder="">
                                <label>Harga Penyerahan Barang</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="harga_satuan_barang" readonly name="harga_satuan_barang" type="text" class="form-control harga_satuan_barang" placeholder="">
                                <label>Harga Satuan Barang</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control isi_per_kemasan" name="isi_per_kemasan" id="isi_per_kemasan" placeholder="">
                                <label>Isi Per Kemasan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="jumlah_kemasan" name="jumlah_kemasan" type="number" class="jumlah_kemasan form-control" placeholder="">
                                <label>Jumlah Kemasan</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="jumlah_satuan" name="jumlah_satuan" type="number" class="form-control jumlah_satuan" placeholder="">
                                <label>Jumlah Satuan</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="kode_barang" readonly name="kode_barang" type="text" class="form-control kode_barang" placeholder="">
                                <label>Kode Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kode_dokumen" id="kode_dokumen" name="kode_dokumen" aria-label="Floating label select example">
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
                                <select class="form-select kode_kategori_barang" id="kode_kategori_barang" name="kode_kategori_barang" aria-label="Floating label select example">
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
                                <select class="form-select kode_jenis_kemasan" id="kode_jenis_kemasan" name="kode_jenis_kemasan" aria-label="Floating label select example">
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
                                <select class="form-select kode_negara_asal" id="kode_negara_asal" name="kode_negara_asal" aria-label="Floating label select example">
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
                                <select class="form-select kode_perhitungan" id="kode_perhitungan" name="kode_perhitungan" aria-label="Floating label select example">
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
                                <select class="form-select kode_satuan_barang" id="kode_satuan_barang" name="kode_satuan_barang" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label style="z-index: 1;">Kode Satuan Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="merk_barang" name="merk_barang" type="text" class="form-control merk_barang" placeholder="">
                                <label>Merk Barang</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="netto" name="netto" type="number" class="form-control netto" placeholder="">
                                <label>Netto</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="nilai_barang" name="nilai_barang" type="number" class="form-control nilai_barang" placeholder="">
                                <label>Nilai Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="nilai_tambah" name="nilai_tambah" type="text" class="form-control nilai_tambah" placeholder="">
                                <label>Nilai Tambah</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="pos_tarif" name="pos_tarif" type="text" class="form-control pos_tarif" placeholder="">
                                <label>Pos Tarif</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="seri_barang" name="seri_barang" type="number" class="form-control seri_barang" placeholder="">
                                <label>Seri Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="spesifikasi_lain" name="spesifikasi_lain" type="text" class="form-control spesifikasi_lain" placeholder="">
                                <label>Spesifikasi Lain</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="tipe_barang" name="tipe_barang" type="text" class="form-control tipe_barang" placeholder="">
                                <label>Tipe Barang</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="ukuran_barang" name="ukuran_barang" type="text" class="form-control ukuran_barang" placeholder="">
                                <label>Ukuran Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="ndpm" name="ndpm" type="number" class="form-control ndpm" placeholder="">
                                <label>NDPM</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="cif_rupiah" name="cif_rupiah" type="number" class="form-control cif_rupiah" placeholder="">
                                <label>Cif Rupiah</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3">
                                <input id="harga_perolehan_barang" name="harga_perolehan_barang" type="text" class="form-control harga_perolehan_barang" placeholder="">
                                <label>Harga Perolehan Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kode_asal_bahan_baku" id="kode_asal_bahan_baku" name="kode_asal_bahan_baku" aria-label="Floating label select example">
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
                                <input id="uraian" name="uraian" type="text" class="form-control uraian" placeholder="">
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
    // DECLARE VARIABLE
    var listBarang = [];
    var listInformasiPengangkut = [];
    var listInformasiDokumenPelengkap = [];
    var listInformasiKontainer = [];
    var listInformasiEntitas = [];
    var listInformasiKemasan = [];
    var selectedDetailBarang = null;
    // INSERT LIST BARANG (FROM SERVER)
    <?php foreach ($lpbDetail as $ld) : ?>
        listBarang.push({
            id: "<?= encrypt($ld['id']) ?>",
            purchase_order_id: "<?= encrypt($ld['purchase_order_id']) ?>",
            purchase_order_details_id: "<?= encrypt($ld['purchase_order_details_id']) ?>",
            penerimaan_barang_id: "<?= encrypt($ld['penerimaan_barang_id']) ?>",
            nama_barang_dok: "<?= str_replace('"', '',  $ld['nama_barang_dok']) ?>",
            jml_masuk: "<?= $ld['jml_masuk'] ?>",
            po_no: "<?= $ld['po_no'] ?>",
            harga_satuan_barang: "<?= ($ld['harga'] + $ld['harga_harian'] + $ld['harga_bulanan']) ?>",
            diskon: "<?= empty($ld['disc']) ? 0 : $ld['disc'] ?>",
            kode_barang: "<?= $ld['kode_barang'] ?>",
            detail_barang_dok: {
                asuransi: '',
                harga_cif: '',
                diskon: '',
                fob: '',
                freight: '',
                harga_ekspor: '',
                harga_penyerahan_barang: '',
                harga_satuan_barang: '',
                isi_per_kemasan: '',
                jumlah_kemasan: '',
                jumlah_satuan: '',
                kode_barang: '',
                kode_dokumen: '',
                kode_kategori_barang: '',
                kode_jenis_kemasan: '',
                kode_negara_asal: '',
                kode_perhitungan: '',
                kode_satuan_barang: '',
                merk_barang: '',
                netto: '',
                nilai_barang: '',
                nilai_tambah: '',
                pos_tarif: '',
                seri_barang: '',
                spesifikasi_lain: '',
                tipe_barang: '',
                ukuran_barang: '',
                ndpm: '',
                cif_rupiah: '',
                harga_perolehan_barang: '',
                kode_asal_bahan_baku: '',
                uraian: '',
                barangTarif: [],
                barangDokumen: [],
            },
        });
    <?php endforeach; ?>
    // INIT FORM VIEW
    $('.detail-barang-form-view').hide();
    // ROOT FORM VIEW
    $(".tanggal_penerimaan_lpb, #root_tanggal_bc_11, #root_tanggal_tiba, #root_tanggal_ttd, #entitas_tanggal_ijin_entitas,#dokumen_pelengkap_tanggal_dokumen").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });
    $("input[type='number']").on("input", function() {
        var inputValue = $(this).val();
        inputValue = inputValue.replace(/^-/, '');
        $(this).val(inputValue);
    });

    // DOKUMEN ROOT
    $('#root_kode_asuransi').select2({
        placeholder: "Pilih Kode Asuransi",
        theme: "bootstrap-5",
    });
    $('#root_kode_incoterm').select2({
        placeholder: "Pilih Kode Incoterm",
        theme: "bootstrap-5",
    });
    $('#root_kode_kantor').select2({
        placeholder: "Pilih Kode Kantor",
        theme: "bootstrap-5",
    });
    $('#root_kode_kantor_bongkar').select2({
        placeholder: "Pilih Kode Kantor Bongkar",
        theme: "bootstrap-5",
    });
    $('#root_kode_tujuan_tpb').select2({
        placeholder: "Pilih Kode Tujuan TPB",
        theme: "bootstrap-5",
    });
    $('#root_kode_tutup_pu').select2({
        placeholder: "Pilih Kode Tutup Pu",
        theme: "bootstrap-5",
    });
    $('#root_kode_valuta').select2({
        placeholder: "Pilih Kode Valuta",
        theme: "bootstrap-5",
    });
    $('#root_nik').select2({
        placeholder: "Pilih Jenis API (NIK)",
        theme: "bootstrap-5",
    });
    $('#root_kode_kena_pajak').select2({
        placeholder: "Pilih Kode Kena Pajak (Opsional)",
        theme: "bootstrap-5",
    });
    // ENTITAS
    $('#entitas_kode_jenis_identitas').select2({
        placeholder: "Pilih Kode Jenis Identitas",
        theme: "bootstrap-5",
    });
    // KEMASAN
    $('#kemasan_kode_jenis_kemasan').select2({
        placeholder: "Pilih Kode Jenis Kemasan",
        theme: "bootstrap-5",
    });
    // KONTAINER
    $('#kontainer_kode_ukuran_kontainer').select2({
        placeholder: "Pilih Kode Ukuran Kontainer",
        theme: "bootstrap-5",
    });
    $('#kontainer_kode_jenis_kontainer').select2({
        placeholder: "Pilih Kode Jenis Kontainer",
        theme: "bootstrap-5",
    });
    $('#kontainer_kode_tipe_kontainer').select2({
        placeholder: "Pilih Kode Tipe Kontainer",
        theme: "bootstrap-5",
    });
    // PENGANGKUT
    $('#pengangkut_kode_cara_angkut').select2({
        placeholder: "Pilih Cara Pengangkutan",
        theme: "bootstrap-5",
    });
    // DOKUMEN BARANG
    $('#kode_dokumen').select2({
        placeholder: "Pilih Kode Dokumen",
        theme: "bootstrap-5",
    });
    $('#kode_kategori_barang').select2({
        placeholder: "Pilih Kode Kategori Barang",
        theme: "bootstrap-5",
    });
    $('#kode_jenis_kemasan').select2({
        placeholder: "Pilih Kode Jenis Kemasan",
        theme: "bootstrap-5",
    });
    $('#kode_negara_asal').select2({
        placeholder: "Pilih Kode Negara Asal",
        theme: "bootstrap-5",
    });
    $('#kode_perhitungan').select2({
        placeholder: "Pilih Kode Perhitungan",
        theme: "bootstrap-5",
    });
    $('#kode_satuan_barang').select2({
        placeholder: "Pilih Kode Satuan Barang",
        theme: "bootstrap-5",
        ajax: {
            url: '<?= base_url('bea-cukai-bc-23/satuan-barang') ?>',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: {
                        more: (params.page * 10) < data.total_count
                    }
                };
            },
            cache: true
        }
    });
    $('#kode_asal_bahan_baku').select2({
        placeholder: "Pilih Kode Asal Bahan Baku",
        theme: "bootstrap-5",
    });
    // BARANG TARIF
    $('#barang_tarif_kode_jenis_tarif').select2({
        placeholder: "Pilih Kode Jenis Tarif",
        theme: "bootstrap-5",
    });
    $('#barang_tarif_kode_fasilitas_tarif').select2({
        placeholder: "Pilih Kode Fasilitas Tarif",
        theme: "bootstrap-5",
    });
    $('#barang_tarif_kode_satuan_barang').select2({
        placeholder: "Pilih Kode Satuan Barang",
        theme: "bootstrap-5",
        ajax: {
            url: '<?= base_url('bea-cukai-bc-23/satuan-barang') ?>',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: {
                        more: (params.page * 10) < data.total_count
                    }
                };
            },
            cache: true
        }
    });

    // SELECT2 SERVER SIDE
    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    var tableListLPB = $('.table-list-lpb').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        lengthChange: true,
        info: false,
        paging: false,
        searching: true,
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
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    // TABEL LIST BARANG TARIF
    var tableListBarangTarif = $('.table-list-barang-tarif').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
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
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    // TABEL LIST DOKUMEN
    var tableListBarangDokumen = $('.table-list-barang-dokumen').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
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
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    // TABEL INFORMASI ENTITAS
    var tableListInformasiEntitas = $('.table-list-informasi-entitas').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
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
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    // TABEL INFORMASI KEMASAN
    var tableListInformasiKemasan = $('.table-list-informasi-kemasan').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
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
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    // TABEL INFORMASI KONTAINER
    var tableListInformasiKontainer = $('.table-list-informasi-kontainer').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
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
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    // TABEL INFORMASI DOKUMEN PELENGKAP
    var tableListDokumenPelengkap = $('.table-list-informasi-dokumen-pelengkap').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
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
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    // TABEL INFORMASI PENGANGKUT
    var tableListInformasiPengangkut = $('.table-list-informasi-pengangkut').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
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
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    // SWITCH VIEW
    $('.btn-edit-dokumen-barang').click(function() {
        $('.detail-barang-form-view').show();
        $('.root-form-view').hide();
        // PASSING
        var id = $(this).data('id');

        $.each(listBarang, function(i, v) {
            if (id === v.id) {
                selectedDetailBarang = v;
            }
        });

        $('#detail-barang-form-nama-barang').text(selectedDetailBarang.nama_barang_dok);
        $('#detail-barang-form-no-po').text(selectedDetailBarang.po_no);
        $('#harga_satuan_barang').val(formatRupiah(selectedDetailBarang.harga_satuan_barang));
        $('#kode_barang').val(selectedDetailBarang.kode_barang);
        $('#diskon').val(selectedDetailBarang.diskon);
        // DRAW FORM DETAIL DOKUMEN BARANG
        drawFormDetailDokumenBarang();
    });
    $('#btn-batal-detail-barang-form-view').click(function() {
        Swal.fire({
            icon: 'question',
            title: 'Kembali Ke Form Utama ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                $('.detail-barang-form-view').hide();
                $('.root-form-view').show();
            }
        })

    });
    // ACTION
    // VALIDATOR ROOT FORM
    var validatorRootForm = $('.form-root').validate({
        rules: {
            tanggal_penerimaan_lpb: {
                required: true
            },
            root_asuransi: {
                required: true
            },
            root_bruto: {
                required: true
            },
            root_cif: {
                required: true
            },
            root_fob: {
                required: true
            },
            root_freight: {
                required: true
            },
            root_harga_penyerahan: {
                required: true
            },
            root_jabatan_pengusaha_ttd: {
                required: true
            },
            root_jumlah_kontainer: {
                required: true
            },
            root_kode_asuransi: {
                required: true
            },
            root_kode_dokumen: {
                required: true
            },
            root_kode_incoterm: {
                required: true
            },
            root_kode_kantor: {
                required: true
            },
            root_kode_kantor_bongkar: {
                required: true
            },
            root_kode_pelabuhan_bongkar: {
                required: true
            },
            root_kode_pelabuhan_muat: {
                required: true
            },
            root_kode_pelabuhan_transit: {
                required: true
            },
            root_kode_tps: {
                required: true
            },
            root_kode_tujuan_tpb: {
                required: true
            },
            root_kode_tutup_pu: {
                required: true
            },
            root_kode_valuta: {
                required: true
            },
            root_kota_ttd: {
                required: true
            },
            root_ndpbm: {
                required: true
            },
            root_netto: {
                required: true
            },
            root_nik: {
                required: true
            },
            root_nilai_barang: {
                required: true
            },
            root_no_aju: {
                required: true
            },
            root_no_bc_11: {
                required: true
            },
            root_pos_bc_11: {
                required: true
            },
            root_seri: {
                required: true
            },
            root_sub_pos_bc_11: {
                required: true
            },
            root_tanggal_bc_11: {
                required: true
            },
            root_tanggal_tiba: {
                required: true
            },
            root_tanggal_ttd: {
                required: true
            },
            root_biaya_tambahan: {
                required: true
            },
            root_biaya_pengurang: {
                required: true
            },
        },
        messages: {
            tanggal_penerimaan_lpb: {
                required: "Tanggal penerimaan wajib diisi"
            },
            root_asuransi: {
                required: "Asurnasi wajib diisi"
            },
            root_bruto: {
                required: "Bruto wajib diisi"
            },
            root_cif: {
                required: "Cif wajib diisi"
            },
            root_fob: {
                required: "Fob wajib diisi"
            },
            root_freight: {
                required: "Freight wajib diisi"
            },
            root_harga_penyerahan: {
                required: "Harga penyerahan wajib diisi"
            },
            root_jabatan_pengusaha_ttd: {
                required: "Jabatan wajib diisi"
            },
            root_jumlah_kontainer: {
                required: "Jumlah kontainer wajib diisi"
            },
            root_kode_asuransi: {
                required: "Pilih kode asuransi"
            },
            root_kode_dokumen: {
                required: "Kode dokumen wajib diisi"
            },
            root_kode_incoterm: {
                required: "Pilih kode incoterm"
            },
            root_kode_kantor: {
                required: "Pilih kode kantor"
            },
            root_kode_kantor_bongkar: {
                required: "Pilih kode kantor bongkar"
            },
            root_kode_pelabuhan_bongkar: {
                required: "Kode pelabuhan bongkar wajib diisi"
            },
            root_kode_pelabuhan_muat: {
                required: "Kode pelabuhan muat wajib diisi"
            },
            root_kode_pelabuhan_transit: {
                required: "Kode pelabuhan transit wajib diisi"
            },
            root_kode_tps: {
                required: "Kode TPS wajib diisi"
            },
            root_kode_tujuan_tpb: {
                required: "Pilih kode tujuan TPB"
            },
            root_kode_tutup_pu: {
                required: "Pilh kode tutup Pu"
            },
            root_kode_valuta: {
                required: "Pilih kode valuta"
            },
            root_kota_ttd: {
                required: "Kota pembuatan dokumen wajib diisi"
            },
            root_ndpbm: {
                required: "Ndpbm wajib diisi"
            },
            root_netto: {
                required: "Netto wajib diisi"
            },
            root_nik: {
                required: "Pilih angka pengenal importir"
            },
            root_nilai_barang: {
                required: "Nilai barang wajib diisi"
            },
            root_no_aju: {
                required: "Nomor aju wajib diisi"
            },
            root_no_bc_11: {
                required: "Nomor BC 1.1 wajib diisi"
            },
            root_pos_bc_11: {
                required: "Pos BC 1.1 wajib diisi"
            },
            root_seri: {
                required: "Seri wajib diisi"
            },
            root_sub_pos_bc_11: {
                required: "Sub Pos BC 1.1 wajib diisi"
            },
            root_tanggal_bc_11: {
                required: "Tanggal BC 1.1 wajib diisi"
            },
            root_tanggal_tiba: {
                required: "Tanggal perkiraan tiba wajib diisi"
            },
            root_tanggal_ttd: {
                required: "Tanggal penanda tanganan dokumen wajib diisi"
            },
            root_biaya_tambahan: {
                required: "Biaya tambahan wajib diisi"
            },
            root_biaya_pengurang: {
                required: "Biaya pengurang wajib diisi"
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
    // VALIDATOR INFORMASI DOKUMEN PENGANGKUT
    var validatorInformasiPengangkut = $(".form-informasi-pengangkut").validate({
        rules: {
            pengangkut_kode_bendera: {
                required: true
            },
            pengangkut_nama_sarana_pengangkut: {
                required: true
            },
            pengangkut_nomor_pengangkut: {
                required: true
            },
            pengangkut_kode_cara_angkut: {
                required: true
            },
            pengangkut_seri_pengangkut: {
                required: true
            },
        },
        messages: {
            pengangkut_kode_bendera: {
                required: "Kode bendera wajib diisi"
            },
            pengangkut_nama_sarana_pengangkut: {
                required: "Nama sarana pengangkut wajib diisi"
            },
            pengangkut_nomor_pengangkut: {
                required: "Nomor pengangkut wajib diisi"
            },
            pengangkut_kode_cara_angkut: {
                required: "Pilih kode cara angkut"
            },
            pengangkut_seri_pengangkut: {
                required: "Seri data pengangkut wajib diisi"
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
    // VALIDATOR INFORMASI DOKUMEN PELENGKAP
    var validatorInformasiDokumenPelengkap = $(".form-dokumen-pelengkap").validate({
        rules: {
            dokumen_pelengkap_kode_dokumen: {
                required: true
            },
            dokumen_pelengkap_nomor_dokumen: {
                required: true
            },
            dokumen_pelengkap_seri_dokumen: {
                required: true
            },
            dokumen_pelengkap_tanggal_dokumen: {
                required: true
            },
        },
        messages: {
            dokumen_pelengkap_kode_dokumen: {
                required: "Kode dokumen wajib diisi"
            },
            dokumen_pelengkap_nomor_dokumen: {
                required: "Nomor dokumen wajib diisi"
            },
            dokumen_pelengkap_seri_dokumen: {
                required: "Seri dokumen wajib diisi"
            },
            dokumen_pelengkap_tanggal_dokumen: {
                required: "Tanggal dokumen wajib diisi"
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
    // VALIDATOR INFORMASI KONTAINER
    var validatorInformasiKontainer = $(".form-informasi-kontainer").validate({
        rules: {
            kontainer_kode_tipe_kontainer: {
                required: true
            },
            kontainer_kode_ukuran_kontainer: {
                required: true
            },
            kontainer_nomor_kontainer: {
                required: true
            },
        },
        messages: {
            kontainer_kode_tipe_kontainer: {
                required: "Pilih tipe kontainer"
            },
            kontainer_kode_ukuran_kontainer: {
                required: "Pilih kode ukuran kontainer"
            },
            kontainer_nomor_kontainer: {
                required: "Nomor Kontainer wajib diisi"
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
    // VALIDATOR INFORMASI KEMASAN
    var validatorInformasiKemasan = $(".form-informasi-kemasan").validate({
        rules: {
            kemasan_jumlah_kemasan: {
                required: true
            },
            kemasan_kode_jenis_kemasan: {
                required: true
            },
            kemasan_seri_kemasan: {
                required: true
            },
            kemasan_merk_kemasan: {
                required: true
            },
        },
        messages: {
            kemasan_jumlah_kemasan: {
                required: "Jumlah kemasan wajib diisi"
            },
            kemasan_kode_jenis_kemasan: {
                required: "Kode kemasan wajib diisi"
            },
            kemasan_seri_kemasan: {
                required: "Seri kemasan wajib diisi"
            },
            kemasan_merk_kemasan: {
                required: "Merk kemasan wajib diisi"
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
    // VALIDATOR INFORMASI ENTITAS
    var validatorInformasiEntitas = $(".form-informasi-entitas").validate({
        rules: {
            entitas_alamat_entitas: {
                required: true
            },
            entitas_kode_entitas: {
                required: true
            },
            entitas_kode_jenis_identitas: {
                required: true
            },
            entitas_nama_entitas: {
                required: true
            },
            entitas_nib_entitas: {
                required: true,
            },
            entitas_nomor_identitas: {
                required: true,
            },
            entitas_nomor_ijin_entitas: {
                required: true,
            },
            entitas_tanggal_ijin_entitas: {
                required: true,
            },
            entitas_seri_entitas: {
                required: true,
            },
        },
        messages: {
            entitas_alamat_entitas: {
                required: "Alamat Importir/Pengusaha TPB wajib diisi"
            },
            entitas_kode_entitas: {
                required: "Kode entitas wajib diisi"
            },
            entitas_kode_jenis_identitas: {
                required: "Pilih kode jenis entitas"
            },
            entitas_nama_entitas: {
                required: "Nama importir/pengusaha TPB wajib diisi"
            },
            entitas_nib_entitas: {
                required: "NIB Importir wajib diisi",
            },
            entitas_nomor_identitas: {
                required: "Nomor identitas importir wajib diisi",
            },
            entitas_nomor_ijin_entitas: {
                required: "Nomor ijin TPB wajib diisi",
            },
            entitas_tanggal_ijin_entitas: {
                required: "Tanggal ijin TPB wajib diisi",
            },
            entitas_seri_entitas: {
                required: "Seri entitas wajib diisi",
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
    // VALIDATOR BARANG TARIF
    var validatorBarangTarif = $(".form-barang-tarif").validate({
        rules: {
            barang_tarif_kode_jenis_tarif: {
                required: true
            },
            jumlah_satuan_bm: {
                required: true
            },
            barang_tarif_kode_fasilitas_tarif: {
                required: true
            },
            barang_tarif_kode_satuan_barang: {
                required: true
            },
            nilai_bayar: {
                required: true,
            },
            nilai_fasilitas: {
                required: true,
            },
            nilai_sudah_dilunasi: {
                required: true,
            },
            barang_tarif_seri_barang: {
                required: true,
            },
            tarif_bm: {
                required: true,
            },
            tarif_fasilitas: {
                required: true,
            },
        },
        messages: {
            barang_tarif_kode_jenis_tarif: {
                required: "Pilih Kode Jenis Tarif"
            },
            jumlah_satuan_bm: {
                required: "Jumlah Satuan Barang Tarif Wajib Diisi"
            },
            barang_tarif_kode_fasilitas_tarif: {
                required: "Pilih Kode Fasilitas Tarif"
            },
            barang_tarif_kode_satuan_barang: {
                required: "Pilih Kode Satuan Barang"
            },
            nilai_bayar: {
                required: "Nilai Bayar Bea Masuk Wajib Diisi",
            },
            nilai_fasilitas: {
                required: "Nilai Fasilitas Wajib Diisi",
            },
            nilai_sudah_dilunasi: {
                required: "Nilai Sudah Dilunasi Wajib Diisi",
            },
            barang_tarif_seri_barang: {
                required: "Seri Barang Wajib Diisi",
            },
            tarif_bm: {
                required: "Tarif Bea Masuk Wajib Diisi",
            },
            tarif_fasilitas: {
                required: "Tarif Fasilitas Wajib Diisi",
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
    // VALIDATOR BARANG DOKUMEN
    var validatorBarangDokumen = $(".form-barang-dokumen").validate({
        rules: {
            no_seri_dokumen: {
                required: true
            },
        },
        messages: {
            no_seri_dokumen: {
                required: "Masukkan nomor seri dokumen"
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
    // VALIDATOR DOKUMEN BARANG
    var validatorDetailDokumenBarang = $(".form-detail-dokumen-barang").validate({
        rules: {
            asuransi: {
                required: true
            },
            harga_cif: {
                required: true
            },
            diskon: {
                required: true
            },
            fob: {
                required: true
            },
            freight: {
                required: true
            },
            harga_ekspor: {
                required: true
            },
            harga_penyerahan_barang: {
                required: true
            },
            harga_satuan_barang: {
                required: true
            },
            isi_per_kemasan: {
                required: true
            },
            jumlah_kemasan: {
                required: true
            },
            jumlah_satuan: {
                required: true
            },
            kode_barang: {
                required: true
            },
            kode_dokumen: {
                required: true
            },
            kode_kategori_barang: {
                required: true
            },
            kode_jenis_kemasan: {
                required: true
            },
            kode_negara_asal: {
                required: true
            },
            kode_perhitungan: {
                required: true
            },
            kode_satuan_barang: {
                required: true
            },
            merk_barang: {
                required: true
            },
            netto: {
                required: true
            },
            nilai_barang: {
                required: true
            },
            nilai_tambah: {
                required: true
            },
            pos_tarif: {
                required: true
            },
            seri_barang: {
                required: true
            },
            spesifikasi_lain: {
                required: true
            },
            tipe_barang: {
                required: true
            },
            ukuran_barang: {
                required: true
            },
            ndpm: {
                required: true
            },
            cif_rupiah: {
                required: true
            },
            harga_perolehan_barang: {
                required: true
            },
            kode_asal_bahan_baku: {
                required: true
            },
            uraian: {
                required: true
            },
        },
        messages: {
            asuransi: {
                required: "Nilai asuransi wajib diisi"
            },
            harga_cif: {
                required: "Harga Cif wajib diisi"
            },
            diskon: {
                required: "Diskon wajib diisi"
            },
            fob: {
                required: "Free on board wajib diisi"
            },
            freight: {
                required: "Freight wajib diisi"
            },
            harga_ekspor: {
                required: "Harga ekspor wajib diisi"
            },
            harga_penyerahan_barang: {
                required: "Harga penyerahan barang wajib diisi"
            },
            harga_satuan_barang: {
                required: "Harga satuan barang wajib diisi"
            },
            isi_per_kemasan: {
                required: "Isi per kemasan wajib diisi"
            },
            jumlah_kemasan: {
                required: "Jumlah kemasan wajib diisi"
            },
            jumlah_satuan: {
                required: "Jumlah satuan wajib diisi"
            },
            kode_barang: {
                required: "Kode barang wajib diisi"
            },
            kode_dokumen: {
                required: "Pilih kode dokumen"
            },
            kode_kategori_barang: {
                required: "Pilih kode kategori barang"
            },
            kode_jenis_kemasan: {
                required: "Pilih kode jenis kemasan"
            },
            kode_negara_asal: {
                required: "Pilih kode negara asal"
            },
            kode_perhitungan: {
                required: "Pilih kode perhitungan"
            },
            kode_satuan_barang: {
                required: "Pilih kode satuan barang"
            },
            merk_barang: {
                required: "Merk barang wajib diisi"
            },
            netto: {
                required: "Netto wajib diisi"
            },
            nilai_barang: {
                required: "Nilai barang wajib diisi"
            },
            nilai_tambah: {
                required: "Nilai tambah wajib diisi"
            },
            pos_tarif: {
                required: "Pos Tarif wajib diisi"
            },
            seri_barang: {
                required: "Seri barang wajib diisi"
            },
            spesifikasi_lain: {
                required: "Spesifikasi lain wajib diisi"
            },
            tipe_barang: {
                required: "Tipe barang wajib diisi"
            },
            ukuran_barang: {
                required: "Ukuran barang wajib diisi"
            },
            ndpm: {
                required: "NDPM wajib diisi"
            },
            cif_rupiah: {
                required: "CIF Rupiah wajib diisi"
            },
            harga_perolehan_barang: {
                required: "Harga perolehan barang wajib diisi"
            },
            kode_asal_bahan_baku: {
                required: "Kode asal bahan baku wajib diisi"
            },
            uraian: {
                required: "Uraian wajib diisi"
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
    // SIMPAN BARANG TARIF
    $('.btn-submit-barang-tarif').click(function() {
        if ($('.form-barang-tarif').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Barang Tarif ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var barangTarifArr = {
                        id: getID(),
                        barang_tarif_kode_jenis_tarif: $('#barang_tarif_kode_jenis_tarif').val(),
                        barang_tarif_kode_jenis_tarif_text: $('#barang_tarif_kode_jenis_tarif').find('option:selected').text(),
                        jumlah_satuan_bm: $('#jumlah_satuan_bm').val(),
                        barang_tarif_kode_fasilitas_tarif: $('#barang_tarif_kode_fasilitas_tarif').val(),
                        barang_tarif_kode_fasilitas_tarif_text: $('#barang_tarif_kode_fasilitas_tarif').find('option:selected').text(),
                        barang_tarif_kode_satuan_barang: $('#barang_tarif_kode_satuan_barang').val(),
                        barang_tarif_kode_satuan_barang_text: $('#barang_tarif_kode_satuan_barang').find('option:selected').text(),
                        kode_jenis_pungutan: $('#kode_jenis_pungutan').val(),
                        nilai_bayar: $('#nilai_bayar').val(),
                        nilai_fasilitas: $('#nilai_fasilitas').val(),
                        nilai_sudah_dilunasi: $('#nilai_sudah_dilunasi').val(),
                        barang_tarif_seri_barang: $('#barang_tarif_seri_barang').val(),
                        tarif_bm: $('#tarif_bm').val(),
                        tarif_fasilitas: $('#tarif_fasilitas').val()
                    };
                    selectedDetailBarang.detail_barang_dok.barangTarif.push(
                        barangTarifArr
                    );
                    displayTableBarangTarif();
                    resetFormBarangTarif();
                }
            })
        }
    });
    // SIMPAN BARANG DOKUMEN
    $('.btn-submit-no-seri-dokumen').click(function() {
        if ($('.form-barang-dokumen').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Barang Dokumen ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var barangDokumenArr = {
                        id: getID(),
                        no_seri_dokumen: $('#no_seri_dokumen').val()
                    }
                    selectedDetailBarang.detail_barang_dok.barangDokumen.push(barangDokumenArr);
                    $('#no_seri_dokumen').val('');
                    displayTableBarangDokumen();
                }
            })
        }
    });
    // SIMPAN DETAIL DOKUMEN BARANG
    $('.btn-simpan-detail-barang-form-view').click(function() {
        if ($('.form-detail-dokumen-barang').valid()) {
            if (selectedDetailBarang.detail_barang_dok.barangTarif.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Barang Tarif Masih Kosong!',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            } else if (selectedDetailBarang.detail_barang_dok.barangDokumen.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Barang Dokumen Masih Kosong!',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            } else {
                Swal.fire({
                    icon: 'question',
                    title: 'Simpan Dokumen Barang ?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        selectedDetailBarang.detail_barang_dok.asuransi = $('#asuransi').val();
                        selectedDetailBarang.detail_barang_dok.harga_cif = $('#harga_cif').val();
                        selectedDetailBarang.detail_barang_dok.diskon = $('#diskon').val();
                        selectedDetailBarang.detail_barang_dok.fob = $('#fob').val();
                        selectedDetailBarang.detail_barang_dok.freight = $('#freight').val();
                        selectedDetailBarang.detail_barang_dok.harga_ekspor = $('#harga_ekspor').val();
                        selectedDetailBarang.detail_barang_dok.harga_penyerahan_barang = $('#harga_penyerahan_barang').val();
                        selectedDetailBarang.detail_barang_dok.harga_satuan_barang = $('#harga_satuan_barang').val();
                        selectedDetailBarang.detail_barang_dok.isi_per_kemasan = $('#isi_per_kemasan').val();
                        selectedDetailBarang.detail_barang_dok.jumlah_kemasan = $('#jumlah_kemasan').val();
                        selectedDetailBarang.detail_barang_dok.jumlah_satuan = $('#jumlah_satuan').val();
                        selectedDetailBarang.detail_barang_dok.kode_barang = $('#kode_barang').val();
                        selectedDetailBarang.detail_barang_dok.kode_dokumen = $('#kode_dokumen').val();
                        selectedDetailBarang.detail_barang_dok.kode_kategori_barang = $('#kode_kategori_barang').val();
                        selectedDetailBarang.detail_barang_dok.kode_jenis_kemasan = $('#kode_jenis_kemasan').val();
                        selectedDetailBarang.detail_barang_dok.kode_negara_asal = $('#kode_negara_asal').val();
                        selectedDetailBarang.detail_barang_dok.kode_perhitungan = $('#kode_perhitungan').val();
                        selectedDetailBarang.detail_barang_dok.kode_satuan_barang = $('#kode_satuan_barang').val();
                        selectedDetailBarang.detail_barang_dok.merk_barang = $('#merk_barang').val();
                        selectedDetailBarang.detail_barang_dok.netto = $('#netto').val();
                        selectedDetailBarang.detail_barang_dok.nilai_barang = $('#nilai_barang').val();
                        selectedDetailBarang.detail_barang_dok.nilai_tambah = $('#nilai_tambah').val();
                        selectedDetailBarang.detail_barang_dok.pos_tarif = $('#pos_tarif').val();
                        selectedDetailBarang.detail_barang_dok.seri_barang = $('#seri_barang').val();
                        selectedDetailBarang.detail_barang_dok.spesifikasi_lain = $('#spesifikasi_lain').val();
                        selectedDetailBarang.detail_barang_dok.tipe_barang = $('#tipe_barang').val();
                        selectedDetailBarang.detail_barang_dok.ukuran_barang = $('#ukuran_barang').val();
                        selectedDetailBarang.detail_barang_dok.ndpm = $('#ndpm').val();
                        selectedDetailBarang.detail_barang_dok.cif_rupiah = $('#cif_rupiah').val();
                        selectedDetailBarang.detail_barang_dok.harga_perolehan_barang = $('#harga_perolehan_barang').val();
                        selectedDetailBarang.detail_barang_dok.kode_asal_bahan_baku = $('#kode_asal_bahan_baku').val();
                        selectedDetailBarang.detail_barang_dok.uraian = $('#uraian').val();

                        // REPLACE LIST BARANG DENGAN SELECTED BARANG
                        var indexToRemove = -1;
                        $.each(listBarang, function(i, v) {
                            if (v.id === selectedDetailBarang.id) {
                                indexToRemove = i;
                            }
                        });
                        if (indexToRemove !== -1) {
                            listBarang.splice(indexToRemove, 1);
                        }
                        // PUSH KE LIST BARANG
                        listBarang.push(selectedDetailBarang);
                        updateStatusInformasiBarangRootForm(selectedDetailBarang.id);
                        selectedDetailBarang = null;
                        resetFormBarangTarif();
                        // KEMBALI KE ROOT VIEW
                        $('.detail-barang-form-view').hide();
                        $('.root-form-view').show();
                    }
                })

            }
        }
    });
    // SIMPAN INFORMASI ENTITAS
    $('.btn-submit-informasi-entitas').click(function() {
        if ($('.form-informasi-entitas').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Informasi Entitas ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    listInformasiEntitas.push({
                        id: getID(),
                        entitas_alamat_entitas: $('#entitas_alamat_entitas').val(),
                        entitas_kode_entitas: $('#entitas_kode_entitas').val(),
                        entitas_kode_jenis_identitas: $('#entitas_kode_jenis_identitas').val(),
                        entitas_kode_jenis_identitas_text: $('#entitas_kode_jenis_identitas').find('option:selected').text(),
                        entitas_nama_entitas: $('#entitas_nama_entitas').val(),
                        entitas_nib_entitas: $('#entitas_nib_entitas').val(),
                        entitas_nomor_identitas: $('#entitas_nomor_identitas').val(),
                        entitas_nomor_ijin_entitas: $('#entitas_nomor_ijin_entitas').val(),
                        entitas_tanggal_ijin_entitas: $('#entitas_tanggal_ijin_entitas').val(),
                        entitas_seri_entitas: $('#entitas_seri_entitas').val(),
                    });
                    displayTableInformasiEntitas();
                    resetFormInformasiEntitas();
                }
            })
        }
    });
    // SIMPAN INFORMASI KEMASAN
    $('.btn-submit-informasi-kemasan').click(function() {
        if ($('.form-informasi-kemasan').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Informasi Kemasan ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    listInformasiKemasan.push({
                        id: getID(),
                        kemasan_jumlah_kemasan: $('#kemasan_jumlah_kemasan').val(),
                        kemasan_kode_jenis_kemasan: $('#kemasan_kode_jenis_kemasan').val(),
                        kemasan_kode_jenis_kemasan_text: $('#kemasan_kode_jenis_kemasan').find('option:selected').text(),
                        kemasan_seri_kemasan: $('#kemasan_seri_kemasan').val(),
                        kemasan_merk_kemasan: $('#kemasan_merk_kemasan').val()
                    });
                    displayTableInformasiKemasan();
                    resetFormInformasiKemasan();
                }
            })
        }
    });
    // SIMPAN INFORMASI KONTAINER
    $('.btn-submit-informasi-kontainer').click(function() {
        if ($('.form-informasi-kontainer').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Informasi Kontainer ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    listInformasiKontainer.push({
                        id: getID(),
                        kontainer_kode_tipe_kontainer: $('#kontainer_kode_tipe_kontainer').val(),
                        kontainer_kode_tipe_kontainer_text: $('#kontainer_kode_tipe_kontainer').find('option:selected').text(),
                        kontainer_kode_ukuran_kontainer: $('#kontainer_kode_ukuran_kontainer').val(),
                        kontainer_kode_ukuran_kontainer_text: $('#kontainer_kode_ukuran_kontainer').find('option:selected').text(),
                        kontainer_nomor_kontainer: $('#kontainer_nomor_kontainer').val(),
                        kontainer_seri_kontainer: $('#kontainer_seri_kontainer').val(),
                        kontainer_kode_jenis_kontainer: $('#kontainer_kode_jenis_kontainer').val(),
                        kontainer_kode_jenis_kontainer_text: $('#kontainer_kode_jenis_kontainer').find('option:selected').text(),
                    });
                    displayTableInformasiKontainer();
                    resetFormInformasiKontainer();
                }
            });
        }
    });
    // SIMPAN INFORMASI DOKUMEN PELENGKAP
    $('.btn-submit-informasi-dokumen-pelengkap').click(function() {
        if ($('.form-dokumen-pelengkap').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Dokumen Pelengkap ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    listInformasiDokumenPelengkap.push({
                        id: getID(),
                        dokumen_pelengkap_kode_dokumen: $('#dokumen_pelengkap_kode_dokumen').val(),
                        dokumen_pelengkap_nomor_dokumen: $('#dokumen_pelengkap_nomor_dokumen').val(),
                        dokumen_pelengkap_seri_dokumen: $('#dokumen_pelengkap_seri_dokumen').val(),
                        dokumen_pelengkap_tanggal_dokumen: $('#dokumen_pelengkap_tanggal_dokumen').val()
                    });
                    displayTableInformasiDokumenPelengkap();
                    resetFormInformasiDokumenPelengkap();
                }
            })
        }
    });
    // SIMPAN INFORMASI PENGANGKUT
    $('.btn-submit-informasi-pengangkut').click(function() {
        if ($('.form-informasi-pengangkut').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Informasi Pengangkut ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    listInformasiPengangkut.push({
                        id: getID(),
                        pengangkut_kode_bendera: $('#pengangkut_kode_bendera').val(),
                        pengangkut_nama_sarana_pengangkut: $('#pengangkut_nama_sarana_pengangkut').val(),
                        pengangkut_nomor_pengangkut: $('#pengangkut_nomor_pengangkut').val(),
                        pengangkut_kode_cara_angkut: $('#pengangkut_kode_cara_angkut').val(),
                        pengangkut_kode_cara_angkut_text: $('#pengangkut_kode_cara_angkut').find('option:selected').text(),
                        pengangkut_seri_pengangkut: $('#pengangkut_seri_pengangkut').val()
                    });
                    displayTableInformasiPengangkut();
                    resetFormInformasiPengangkut();

                }
            })
        }
    });
    // SIMPAN DOKUMEN BC 2.23
    $('.btn-submit-root-form-view').click(function() {
        if ($('.form-root').valid()) {
            // CEK APAKAH SEMUA INFORMASI BARANG (DETAIL) SUDAH DIISI SEMUA
            var validationDetailInfoBarang = true;
            $.each(listBarang, function(i, v) {
                if (v.detail_barang_dok.barangTarif.length === 0) {
                    validationDetailInfoBarang = false;
                }
            });
            if (!validationDetailInfoBarang) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Ada dokumen barang yang belum diisi!',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            } else if (listInformasiEntitas.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Informasi entitas masih kosong!',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            } else if (listInformasiKemasan.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Informasi kemasan masih kosong!',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            } else if (listInformasiDokumenPelengkap.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Informasi dokumen pelengkap masih kosong!',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            } else if (listInformasiPengangkut.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Informasi dokumen pengangkut masih kosong!',
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            } else {
                Swal.fire({
                    icon: 'question',
                    title: 'Simpan Dokumen BC 2.3 ?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        var formData = new FormData(document.querySelector(".form-root"));
                        formData.append('barang', JSON.stringify(listBarang));
                        formData.append('entitas', JSON.stringify(listInformasiEntitas));
                        formData.append('kemasan', JSON.stringify(listInformasiKemasan));
                        formData.append('kontainer', JSON.stringify(listInformasiKontainer));
                        formData.append('dokumen', JSON.stringify(listInformasiDokumenPelengkap));
                        formData.append('pengangkut', JSON.stringify(listInformasiPengangkut));
                        var id = $('#id').val();
                        if (id) {
                            // UPDATE
                        } else {
                            // INSERT
                            $.ajax({
                                url: "<?= base_url("bea-cukai-bc-23/create"); ?>",
                                data: formData,
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                },
                                method: "POST",
                                dataType: "json",
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    csrf.val(response.token);
                                    if (response.status === true) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                            confirmButtonText: 'Ok'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                console.log(response);
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                            confirmButtonText: 'Ok'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                console.log(response);
                                            }
                                        });
                                    }
                                },
                            });
                        }
                    }
                });
            }

        }
    });

    // DATA TABLE DETAIL BARANG 
    function displayTableBarangTarif() {
        if ($.fn.DataTable.isDataTable('.table-list-barang-tarif')) {
            $('.table-list-barang-tarif').DataTable().clear().draw();
            tableListBarangTarif.destroy();
        }
        const table = $('.table-list-barang-tarif');
        const tbody = table.find('tbody');
        var no = 1;

        $.each(selectedDetailBarang.detail_barang_dok.barangTarif, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').text(no++));
            newRow.append($('<td style="text-align: center;">').text(v.barang_tarif_kode_jenis_tarif_text));
            newRow.append($('<td style="text-align: center;">').text(v.jumlah_satuan_bm));
            newRow.append($('<td style="text-align: center;">').text(v.barang_tarif_kode_satuan_barang_text));
            newRow.append($('<td style="text-align: center;">').text(formatRupiah(v.nilai_bayar)));
            newRow.append($('<td style="text-align: center;">').text(formatRupiah(v.nilai_fasilitas)));
            newRow.append($('<td style="text-align: center;">').text(formatRupiah(v.nilai_sudah_dilunasi)));
            newRow.append($('<td style="text-align: center;">').text(v.barang_tarif_seri_barang));
            newRow.append($('<td style="text-align: center;">').text(formatRupiah(v.tarif_bm)));
            newRow.append($('<td style="text-align: center;">').text(formatRupiah(v.tarif_fasilitas)));
            newRow.append($('<td style="text-align: center;">').html(
                `
                    <button type="button" class="btn btn-danger" onclick="deleteRowBarangTarif('${v.id}')" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                `
            ));
            table.find('tbody').append(newRow);
        });

        tableListBarangTarif = $('.table-list-barang-tarif').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
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
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });
        tableListBarangTarif.draw();
    }
    // DATA DETAIL BARANG DOKUMEN
    function displayTableBarangDokumen() {
        if ($.fn.DataTable.isDataTable('.table-list-barang-dokumen')) {
            $('.table-list-barang-dokumen').DataTable().clear().draw();
            tableListBarangDokumen.destroy();
        }
        const table = $('.table-list-barang-dokumen');
        const tbody = table.find('tbody');
        var no = 1;

        $.each(selectedDetailBarang.detail_barang_dok.barangDokumen, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').text(no++));
            newRow.append($('<td style="text-align: center;">').text(v.no_seri_dokumen));
            newRow.append($('<td style="text-align: center;">').html(
                `
                    <button type="button" class="btn btn-danger" onclick="deleteRowDetailBarangDokumen('${v.id}')" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                `
            ));
            table.find('tbody').append(newRow);
        });

        tableListBarangDokumen = $('.table-list-barang-dokumen').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
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
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });

        tableListBarangDokumen.draw();
    }
    // DATA DETAIL INFORMASI ENTITAS
    function displayTableInformasiEntitas() {
        if ($.fn.DataTable.isDataTable('.table-list-informasi-entitas')) {
            $('.table-list-informasi-entitas').DataTable().clear().draw();
            tableListInformasiEntitas.destroy();
        }
        const table = $('.table-list-informasi-entitas');
        const tbody = table.find('tbody');
        var no = 1;

        $.each(listInformasiEntitas, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').text(no++));
            newRow.append($('<td style="text-align: center;">').text(v.entitas_alamat_entitas));
            newRow.append($('<td style="text-align: center;">').text(v.entitas_kode_jenis_identitas_text));
            newRow.append($('<td style="text-align: center;">').text(v.entitas_nama_entitas));
            newRow.append($('<td style="text-align: center;">').text(v.entitas_nib_entitas));
            newRow.append($('<td style="text-align: center;">').text(v.entitas_nomor_identitas));
            newRow.append($('<td style="text-align: center;">').text(v.entitas_nomor_ijin_entitas));
            newRow.append($('<td style="text-align: center;">').text(v.entitas_tanggal_ijin_entitas));
            newRow.append($('<td style="text-align: center;">').text(v.entitas_seri_entitas));
            newRow.append($('<td style="text-align: center;">').html(
                `
                    <button type="button" class="btn btn-danger" onclick="deleteRowInformasiEntitas('${v.id}')" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                `
            ));
            table.find('tbody').append(newRow);
        });

        tableListInformasiEntitas = $('.table-list-informasi-entitas').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
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
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });

        tableListInformasiEntitas.draw();
    }
    // DATA DETAIL INFORMASI KEMASAN
    function displayTableInformasiKemasan() {
        if ($.fn.DataTable.isDataTable('.table-list-informasi-kemasan')) {
            $('.table-list-informasi-kemasan').DataTable().clear().draw();
            tableListInformasiKemasan.destroy();
        }
        const table = $('.table-list-informasi-kemasan');
        const tbody = table.find('tbody');
        var no = 1;

        $.each(listInformasiKemasan, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').text(no++));
            newRow.append($('<td style="text-align: center;">').text(v.kemasan_jumlah_kemasan));
            newRow.append($('<td style="text-align: center;">').text(v.kemasan_kode_jenis_kemasan_text));
            newRow.append($('<td style="text-align: center;">').text(v.kemasan_seri_kemasan));
            newRow.append($('<td style="text-align: center;">').text(v.kemasan_merk_kemasan));
            newRow.append($('<td style="text-align: center;">').html(
                `
                    <button type="button" class="btn btn-danger" onclick="deleteRowInformasiKemasan('${v.id}')" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                `
            ));
            table.find('tbody').append(newRow);
        });

        tableListInformasiKemasan = $('.table-list-informasi-kemasan').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
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
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });
        tableListInformasiKemasan.draw();
    }

    // DATA TABEL INFORMASI KONTAINER
    function displayTableInformasiKontainer() {
        if ($.fn.DataTable.isDataTable('.table-list-informasi-kontainer')) {
            $('.table-list-informasi-kontainer').DataTable().clear().draw();
            tableListInformasiKontainer.destroy();
        }
        const table = $('.table-list-informasi-kontainer');
        const tbody = table.find('tbody');
        var no = 1;

        $.each(listInformasiKontainer, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').text(no++));
            newRow.append($('<td style="text-align: center;">').text(v.kontainer_kode_tipe_kontainer_text));
            newRow.append($('<td style="text-align: center;">').text(v.kontainer_kode_ukuran_kontainer_text));
            newRow.append($('<td style="text-align: center;">').text(v.kontainer_nomor_kontainer));
            newRow.append($('<td style="text-align: center;">').text(v.kontainer_seri_kontainer));
            newRow.append($('<td style="text-align: center;">').text(v.kontainer_kode_jenis_kontainer_text));
            newRow.append($('<td style="text-align: center;">').html(
                `
                    <button type="button" class="btn btn-danger" onclick="deleteRowInformasiKontainer('${v.id}')" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                `
            ));
            table.find('tbody').append(newRow);
        });

        tableListInformasiKontainer = $('.table-list-informasi-kontainer').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
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
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });
        tableListInformasiKontainer.draw();
    }

    // DATA TABEL INFORMASI DOKUMEN PELENGKAP
    function displayTableInformasiDokumenPelengkap() {
        if ($.fn.DataTable.isDataTable('.table-list-informasi-dokumen-pelengkap')) {
            $('.table-list-informasi-dokumen-pelengkap').DataTable().clear().draw();
            tableListDokumenPelengkap.destroy();
        }
        const table = $('.table-list-informasi-dokumen-pelengkap');
        const tbody = table.find('tbody');
        var no = 1;

        $.each(listInformasiDokumenPelengkap, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').text(no++));
            newRow.append($('<td style="text-align: center;">').text(v.dokumen_pelengkap_nomor_dokumen));
            newRow.append($('<td style="text-align: center;">').text(v.dokumen_pelengkap_seri_dokumen));
            newRow.append($('<td style="text-align: center;">').text(v.dokumen_pelengkap_tanggal_dokumen));
            newRow.append($('<td style="text-align: center;">').html(
                `
                    <button type="button" class="btn btn-danger" onclick="deleteRowInformasiDokumenPelengkap('${v.id}')" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                `
            ));
            table.find('tbody').append(newRow);
        });

        tableListDokumenPelengkap = $('.table-list-informasi-dokumen-pelengkap').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
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
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });
        tableListDokumenPelengkap.draw();
    }

    // DATA TABEL INFORMASI PENGANGKUT
    function displayTableInformasiPengangkut() {
        if ($.fn.DataTable.isDataTable('.table-list-informasi-pengangkut')) {
            $('.table-list-informasi-pengangkut').DataTable().clear().draw();
            tableListInformasiPengangkut.destroy();
        }
        const table = $('.table-list-informasi-pengangkut');
        const tbody = table.find('tbody');
        var no = 1;

        $.each(listInformasiPengangkut, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align: center;">').text(no++));
            newRow.append($('<td style="text-align: center;">').text(v.pengangkut_kode_bendera));
            newRow.append($('<td style="text-align: center;">').text(v.pengangkut_nama_sarana_pengangkut));
            newRow.append($('<td style="text-align: center;">').text(v.pengangkut_nomor_pengangkut));
            newRow.append($('<td style="text-align: center;">').text(v.pengangkut_kode_cara_angkut_text));
            newRow.append($('<td style="text-align: center;">').text(v.pengangkut_seri_pengangkut));
            newRow.append($('<td style="text-align: center;">').html(
                `
                    <button type="button" class="btn btn-danger" onclick="deleteRowInformasiPengangkut('${v.id}')" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                `
            ));
            table.find('tbody').append(newRow);
        });

        tableListInformasiPengangkut = $('.table-list-informasi-pengangkut').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
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
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });
        tableListInformasiPengangkut.draw();
    }

    // DELETE ROW INFORMASI PENGANGKUT
    function deleteRowInformasiPengangkut(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Informasi Pengangkut ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                var indexToRemove = -1;
                $.each(listInformasiPengangkut, function(i, v) {
                    if (v.id === id) {
                        indexToRemove = i;
                    }
                });
                if (indexToRemove !== -1) {
                    listInformasiPengangkut.splice(indexToRemove, 1);
                }
                displayTableInformasiPengangkut()
            }
        })
    }

    // DELETE ROW DETAIL INFORMASI DOKUMEN PELENGKAP
    function deleteRowInformasiDokumenPelengkap(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Informasi Dokumen Pelengkap ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                var indexToRemove = -1;
                $.each(listInformasiDokumenPelengkap, function(i, v) {
                    if (v.id === id) {
                        indexToRemove = i;
                    }
                });
                if (indexToRemove !== -1) {
                    listInformasiDokumenPelengkap.splice(indexToRemove, 1);
                }
                displayTableInformasiDokumenPelengkap()
            }
        })
    }

    // DELETE ROW DETAIL INFORMASI KONTAINER
    function deleteRowInformasiKontainer(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Informasi Kontainer ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                var indexToRemove = -1;
                $.each(listInformasiKontainer, function(i, v) {
                    if (v.id === id) {
                        indexToRemove = i;
                    }
                });
                if (indexToRemove !== -1) {
                    listInformasiKontainer.splice(indexToRemove, 1);
                }
                displayTableInformasiKontainer();
            }
        })
    }

    // DELETE ROW DETAIL INFORMASI KEMASAN
    function deleteRowInformasiKemasan(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Informasi Kemasan ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                var indexToRemove = -1;
                $.each(listInformasiKemasan, function(i, v) {
                    if (v.id === id) {
                        indexToRemove = i;
                    }
                });
                if (indexToRemove !== -1) {
                    listInformasiKemasan.splice(indexToRemove, 1);
                }
                displayTableInformasiKemasan();
            }
        })
    }

    // DELETE ROW DETAIL INFORMASI ENTITAS
    function deleteRowInformasiEntitas(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Informasi Entitas ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                var indexToRemove = -1;
                $.each(listInformasiEntitas, function(i, v) {
                    if (v.id === id) {
                        indexToRemove = i;
                    }
                });
                if (indexToRemove !== -1) {
                    listInformasiEntitas.splice(indexToRemove, 1);
                }
                displayTableInformasiEntitas();
            }
        })
    }
    // DELETE ROW DETAIL BARANG TARIF
    function deleteRowBarangTarif(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Barang Tarif ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                var indexToRemove = -1;
                $.each(selectedDetailBarang.detail_barang_dok.barangTarif, function(i, v) {
                    if (v.id === id) {
                        indexToRemove = i;
                    }
                });
                if (indexToRemove !== -1) {
                    selectedDetailBarang.detail_barang_dok.barangTarif.splice(indexToRemove, 1);
                }
                displayTableBarangTarif();
            }
        })
    }
    // DELETE ROW DETAIL BARANG DOKUMEN
    function deleteRowDetailBarangDokumen(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Barang Dokumen ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                var indexToRemove = -1;
                $.each(selectedDetailBarang.detail_barang_dok.barangDokumen, function(i, v) {
                    if (v.id === id) {
                        indexToRemove = i;
                    }
                });
                if (indexToRemove !== -1) {
                    selectedDetailBarang.detail_barang_dok.barangDokumen.splice(indexToRemove, 1);
                }
                displayTableBarangDokumen();
            }
        })
    }

    // HELPER 
    function drawFormDetailDokumenBarang() {
        $('#asuransi').val(selectedDetailBarang.detail_barang_dok.asuransi);
        $('#harga_cif').val(selectedDetailBarang.detail_barang_dok.harga_cif);
        // $('#diskon').val(selectedDetailBarang.detail_barang_dok.diskon);
        $('#fob').val(selectedDetailBarang.detail_barang_dok.fob);
        $('#freight').val(selectedDetailBarang.detail_barang_dok.freight);
        $('#harga_ekspor').val(selectedDetailBarang.detail_barang_dok.harga_ekspor);
        $('#harga_penyerahan_barang').val(selectedDetailBarang.detail_barang_dok.harga_penyerahan_barang);
        // $('#harga_satuan_barang').val(selectedDetailBarang.detail_barang_dok.harga_satuan_barang);
        $('#isi_per_kemasan').val(selectedDetailBarang.detail_barang_dok.isi_per_kemasan);
        $('#jumlah_kemasan').val(selectedDetailBarang.detail_barang_dok.jumlah_kemasan);
        $('#jumlah_satuan').val(selectedDetailBarang.detail_barang_dok.jumlah_satuan);
        // $('#kode_barang').val(selectedDetailBarang.detail_barang_dok.kode_barang);
        $('#kode_dokumen').val(selectedDetailBarang.detail_barang_dok.kode_dokumen).change();
        $('#kode_kategori_barang').val(selectedDetailBarang.detail_barang_dok.kode_kategori_barang).change();
        $('#kode_jenis_kemasan').val(selectedDetailBarang.detail_barang_dok.kode_jenis_kemasan).change();
        $('#kode_negara_asal').val(selectedDetailBarang.detail_barang_dok.kode_negara_asal).change();
        $('#kode_perhitungan').val(selectedDetailBarang.detail_barang_dok.kode_perhitungan).change();
        $('#kode_satuan_barang').val(selectedDetailBarang.detail_barang_dok.kode_satuan_barang).change();
        $('#merk_barang').val(selectedDetailBarang.detail_barang_dok.merk_barang);
        $('#netto').val(selectedDetailBarang.detail_barang_dok.netto);
        $('#nilai_barang').val(selectedDetailBarang.detail_barang_dok.nilai_barang);
        $('#nilai_tambah').val(selectedDetailBarang.detail_barang_dok.nilai_tambah);
        $('#pos_tarif').val(selectedDetailBarang.detail_barang_dok.pos_tarif);
        $('#seri_barang').val(selectedDetailBarang.detail_barang_dok.seri_barang);
        $('#spesifikasi_lain').val(selectedDetailBarang.detail_barang_dok.spesifikasi_lain);
        $('#tipe_barang').val(selectedDetailBarang.detail_barang_dok.tipe_barang);
        $('#ukuran_barang').val(selectedDetailBarang.detail_barang_dok.ukuran_barang);
        $('#ndpm').val(selectedDetailBarang.detail_barang_dok.ndpm);
        $('#cif_rupiah').val(selectedDetailBarang.detail_barang_dok.cif_rupiah);
        $('#harga_perolehan_barang').val(selectedDetailBarang.detail_barang_dok.harga_perolehan_barang);
        $('#kode_asal_bahan_baku').val(selectedDetailBarang.detail_barang_dok.kode_asal_bahan_baku).change();
        $('#uraian').val(selectedDetailBarang.detail_barang_dok.uraian);
        // DISPLAY BARANG TARIF TABEL
        displayTableBarangTarif();
        // DISPLAY BARANG DOKUMEN
        displayTableBarangDokumen();
    }

    function updateStatusInformasiBarangRootForm(id) {
        var targetElement = $('.body-table-info-status-barang-root-view').filter('[data-id="' + id + '"]');
        if (targetElement.length > 0) {
            targetElement.html('<span class="badge badge-success">DOKUMEN SUDAH DIISI</span>');
        }
    }

    function resetFormInformasiPengangkut() {
        $('#pengangkut_kode_bendera').val('');
        $('#pengangkut_nama_sarana_pengangkut').val('');
        $('#pengangkut_nomor_pengangkut').val('');
        $('#pengangkut_kode_cara_angkut').val(null).change();
        $('#pengangkut_seri_pengangkut').val('');
    }

    function resetFormInformasiDokumenPelengkap() {
        $('#dokumen_pelengkap_nomor_dokumen').val('');
        $('#dokumen_pelengkap_seri_dokumen').val('');
        $('#dokumen_pelengkap_tanggal_dokumen').val('');
    }

    function resetFormInformasiKontainer() {
        $('#kontainer_kode_tipe_kontainer').val(null).change();
        $('#kontainer_kode_ukuran_kontainer').val(null).change();
        $('#kontainer_nomor_kontainer').val('');
        $('#kontainer_seri_kontainer').val('');
        $('#kontainer_kode_jenis_kontainer').val(null).change();

    }

    function resetFormInformasiKemasan() {
        $('#kemasan_jumlah_kemasan').val('');
        $('#kemasan_kode_jenis_kemasan').val(null).change();
        $('#kemasan_seri_kemasan').val('');
        $('#kemasan_merk_kemasan').val('');

    }

    function resetFormInformasiEntitas() {
        $('#entitas_alamat_entitas').val('');
        $('#entitas_kode_jenis_identitas').val(null).change();
        $('#entitas_nama_entitas').val('');
        $('#entitas_nib_entitas').val('');
        $('#entitas_nomor_identitas').val('');
        $('#entitas_nomor_ijin_entitas').val('');
        $('#entitas_tanggal_ijin_entitas').val('');
        $('#entitas_seri_entitas').val('');
    }

    function resetFormBarangTarif() {
        $('#barang_tarif_kode_jenis_tarif').val(null).change();
        $('#jumlah_satuan_bm').val('');
        $('#barang_tarif_kode_fasilitas_tarif').val(null).change();
        $('#barang_tarif_kode_satuan_barang').val(null).change();
        $('#nilai_bayar').val('');
        $('#nilai_fasilitas').val('');
        $('#nilai_sudah_dilunasi').val('');
        $('#barang_tarif_seri_barang').val('');
        $('#tarif_bm').val('');
        $('#tarif_fasilitas').val('');
    }

    function formatRupiah(angka) {
        angka = angka || 0;
        angka = angka.toString().replace(/\./g, '').replace(/[^\d,]/g, '');
        var parts = angka.split(',');
        var ribuanFormatted = parts[0].split('').reverse().join('').match(/\d{1,3}/g).join('.').split('').reverse().join('');
        var desimal = parts[1] || '00';
        return ribuanFormatted + ',' + desimal;
    }

    function getID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0,
                v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }
</script>


<?= $this->endSection(); ?>