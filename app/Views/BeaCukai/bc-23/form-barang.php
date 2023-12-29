<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>

<section class="section section-form">
    <?php include('header.php') ?>
    <div class="card">
        <div class="card-header" style="font-weight: bold; color:black;">
            BC 2.3 - PEMBERITAHUAN IMPOR BARANG UNTUK DITIMBUN DI TEMPAT PENIMBUNAN BERIKAT
        </div>
        <div class="card-body">
            <?php include_once('nav.php') ?>
            <?= csrf_field() ?>
            <!-- ROOT FORM -->
            <div class="root-form-view">
                <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                    Data Barang
                </label>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-lpb" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;width:10px;">No</th>
                                <th style="text-align: center;">No PO</th>
                                <th style="text-align: center;">Kode HS</th>
                                <th style="text-align: center;">Kode Barang</th>
                                <th style="text-align: center;">Nama Barang</th>
                                <th style="text-align: center;">Jmlh Diterima</th>
                                <th style="text-align: center;">Total Harga</th>
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
                                    <td style="text-align: center;">-</td>
                                    <td style="text-align: center;"><?= $l['kode_barang'] ?></td>
                                    <td style="text-align: center;"><?= $l['nama_barang_dok'] ?></td>
                                    <td style="text-align: center;"><?= $l['jml_masuk'] ?></td>
                                    <td style="text-align: center;"><?= str_replace('Rp', '', toRupiah($l['sub_total'])) ?></td>
                                    <td style="text-align: center;" class="body-table-info-status-barang-root-view" data-id="<?= encrypt($l['id']) ?>">
                                        <?php if (!empty($bc23Detail)) : ?>
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
                <a href="#" class="btn btn-primary mt-4" style="float: right;">
                    Simpan Perubahan
                </a>
            </div>

            <!-- DETAIL BARANG VIEW -->
            <div class="detail-barang-form-view">
                <div class="mt-3">
                    <a class="btn btn-primary float-right detail-barang-form-view" id="btn-simpan-detail-barang-form-view" href="#">
                        Simpan
                    </a>
                    <a class="btn btn-danger float-right detail-barang-form-view" id="btn-batal-detail-barang-form-view" href="#" style="margin-right: 8px;">
                        Batal
                    </a>
                </div>

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
                <div class="row">
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mb-3">
                            Jenis
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="barang_detail_seri_barang" name="barang_detail_seri_barang" type="number" class="form-control barang_detail_seri_barang" placeholder="">
                                <label>Seri Barang</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select barang_detail_kode_hs" id="barang_detail_kode_hs" name="barang_detail_kode_hs" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeHS as $k) : ?>
                                        <option value="<?= encrypt($k['code']) ?>">
                                            <?= strtoupper($k['code']) . " - " . strtoupper($k['uraian_barang']) . "" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode HS/Pos Tarif</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-2">
                                <input id="barang_detail_kode_barang" readonly name="barang_detail_kode_barang" type="text" class="form-control barang_detail_kode_barang" placeholder="">
                                <label>Kode</label>
                            </div>
                        </div>

                        <a href="#" id="btn-sesuai-kode-hs" class="btn btn-primary" style="float: right;">
                            Sesuai Hs
                        </a> <br><br>

                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <textarea id="barang_detail_uraian" name="barang_detail_uraian" type="text" class="form-control barang_detail_uraian" placeholder="" style="height: 100px;"></textarea>
                                <label>Uraian</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="barang_detail_merk_barang" name="barang_detail_merk_barang" type="text" class="form-control barang_detail_merk_barang" value="-" placeholder="">
                                <label>Merk Barang</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="barang_detail_tipe_barang" name="barang_detail_tipe_barang" type="text" class="form-control barang_detail_tipe_barang" value="-" placeholder="">
                                <label>Tipe Barang</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="barang_detail_spesifikasi_lain" name="barang_detail_spesifikasi_lain" type="text" class="form-control barang_detail_spesifikasi_lain" value="-" placeholder="">
                                <label>Spesifikasi Lain</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mb-3">
                            Kategori Barang
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select barang_detail_kategori_barang" id="barang_detail_kategori_barang" name="barang_detail_kategori_barang" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeKategoriBarang as $k) : ?>
                                        <option value="<?= encrypt(str_replace(']', '', explode(',', $k['description'])[1])) ?>">
                                            <?= str_replace(']', '', explode(',', $k['description'])[1]) . " - " . strtoupper($k['value']) . "" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Kategori Barang</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select barang_detail_negara" id="barang_detail_negara" name="barang_detail_negara" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeNegaraAsal as $k) : ?>
                                        <option value="<?= encrypt($k['code']) ?>">
                                            <?= $k['code'] . " - " . strtoupper($k['country_name']) . "" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Negara</label>
                            </div>
                        </div>
                        <label class="form-label font-weight-bold lable-title mb-3">
                            Harga
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="barang_detail_harga" maxlength="24" name="barang_detail_harga" type="text" class="form-control barang_detail_harga" placeholder="" oninput="this.value = this.value.replace(/[^\d.]/g, '')">
                                <label>Harga</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="barang_detail_biaya_tambahan" maxlength="24" name="barang_detail_biaya_tambahan" type="text" value="0" class="form-control barang_detail_biaya_tambahan" placeholder="" oninput="this.value = this.value.replace(/[^\d.]/g, '')">
                                <label>Biaya Tambahan</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="barang_detail_fob" maxlength="24" name="barang_detail_fob" type="text" value="0" class="form-control barang_detail_fob" placeholder="" oninput="this.value = this.value.replace(/[^\d.]/g, '')">
                                <label>FOB</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="barang_detail_harga_satuan" maxlength="24" name="barang_detail_harga_satuan" type="text" readonly class="form-control barang_detail_harga_satuan" placeholder="" oninput="this.value = this.value.replace(/[^\d.]/g, '')">
                                <label>Harga Satuan</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="barang_detail_freight" maxlength="24" name="barang_detail_freight" type="text" value="0" class="form-control barang_detail_freight" placeholder="" oninput="this.value = this.value.replace(/[^\d.]/g, '')">
                                <label>Freight</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="barang_detail_asuransi" maxlength="24" name="barang_detail_asuransi" type="text" value="0" class="form-control barang_detail_asuransi" placeholder="" oninput="this.value = this.value.replace(/[^\d.]/g, '')">
                                <label>Asuransi</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="barang_detail_cif" maxlength="24" name="barang_detail_cif" type="text" value="0" class="form-control barang_detail_cif" placeholder="" oninput="this.value = this.value.replace(/[^\d.]/g, '')">
                                <label>Asuransi</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="barang_detail_nilai_pabean" maxlength="24" name="barang_detail_nilai_pabean" type="text" value="0" class="form-control barang_detail_nilai_pabean" placeholder="" oninput="this.value = this.value.replace(/[^\d.]/g, '')">
                                <label>Nilai Pabean</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mb-3">
                            Jumlah & Berat
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="barang_detail_jumlah_satuan" name="barang_detail_jumlah_satuan" type="text" class="form-control barang_detail_jumlah_satuan" placeholder="" oninput="this.value = this.value.replace(/[^\d.]/g, '')">
                                <label>Jumlah Satuan</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select barang_detail_kode_satuan_barang" id="barang_detail_kode_satuan_barang" name="barang_detail_kode_satuan_barang" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label style="z-index: 1;">Kode Satuan Barang</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="barang_detail_jumlah_kemasan" name="barang_detail_jumlah_kemasan" type="text" class="form-control barang_detail_jumlah_kemasan" placeholder="" oninput="this.value = this.value.replace(/[^\d.]/g, '')">
                                <label>Jumlah Kemasan</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select barang_detail_kode_jenis_kemasan" id="barang_detail_kode_jenis_kemasan" name="barang_detail_kode_jenis_kemasan" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeJenisKemasan as $k) : ?>
                                        <option value="<?= encrypt($k['description']) ?>">
                                            <?= $k['description'] . " - " . strtoupper($k['value']) . " " ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Jenis Kemasan</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="barang_detail_berat_bersih" name="barang_detail_berat_bersih" type="text" class="form-control barang_detail_berat_bersih" placeholder="" oninput="this.value = this.value.replace(/[^\d.]/g, '')">
                                <label>Berat Bersih (Kg)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 mt-1">
                        <label class="form-label font-weight-bold lable-title mb-3">
                            Dokumen
                        </label>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-dokumen" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center; width:10px;">
                                            <input type="checkbox" id="parent">
                                        </th>
                                        <th style="text-align: center; width:10px;">No</th>
                                        <th style="text-align: center;">Jenis Dokumen</th>
                                        <th style="text-align: center;">Nomor Dokumen</th>
                                        <th style="text-align: center;">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <label class="form-label font-weight-bold lable-title mb-3">
                            Pungutan
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <select class="form-select barang_detail_kode_jenis_pungutan" id="barang_detail_kode_jenis_pungutan" name="barang_detail_kode_jenis_pungutan" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeJenisPungutan as $k) : ?>
                                        <option value="<?= encrypt($k['value']) ?>">
                                            <?= $k['value'] . " - " . strtoupper($k['description']) . " " ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Pungutan</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <select class="form-select barang_detail_kode_jenis_tarif" id="barang_detail_kode_jenis_tarif" name="barang_detail_kode_jenis_tarif" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeJenisTarif as $k) : ?>
                                        <option value="<?= encrypt($k['value']) ?>">
                                            <?= $k['value'] . " - " . strtoupper($k['description']) . " " ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Jenis Tarif</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="barang_detail_nilai_tarif" name="barang_detail_nilai_tarif" type="text" class="form-control barang_detail_nilai_tarif" placeholder="" oninput="this.value = this.value.replace(/[^\d.]/g, '')">
                                <label>Nilai Tarif (%)</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select barang_detail_kode_fasilitas_tarif" id="barang_detail_kode_fasilitas_tarif" name="barang_detail_kode_fasilitas_tarif" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeFasilitasTarif as $k) : ?>
                                        <option value="<?= encrypt($k['value']) ?>">
                                            (<?= $k['value'] ?>) <?= $k['description'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Fasilitas Tarif</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="barang_detail_tarif_fasilitas" name="barang_detail_tarif_fasilitas" type="text" class="form-control barang_detail_tarif_fasilitas" placeholder="" oninput="this.value = this.value.replace(/[^\d.]/g, '')">
                                <label>Tarif Fasilitas (%)</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="row" style="float: right; margin-bottom:5px;">
                                    <div class="col-sm" style="margin-right: -20px;">
                                        <button type="button" class="btn btn-add btn-block float-right btn-submit-kemasan" style="float: right;">
                                            <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-pungutan" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center; width:10px;">No</th>
                                        <th style="text-align: center;">Jenis Pungutan</th>
                                        <th style="text-align: center;">Jenis Tarif</th>
                                        <th style="text-align: center;">Nilai Tarif</th>
                                        <th style="text-align: center;">Fasilitas Tarif</th>
                                        <th style="text-align: center;">Tarif Fasilitas</th>
                                        <th style="text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
    var selectedDetailBarang = null;
    // INSERT
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
                barang_detail_kategori_barang: '',
                kode_jenis_kemasan: '',
                kode_negara_asal: '',
                kode_perhitungan: '',
                kode_satuan_barang: '',
                merk_barang: '',
                netto: '',
                nilai_barang: '',
                nilai_tambah: '',
                pos_tarif: '',
                barang_detail_seri_barang: '',
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

    // INIT
    $('.detail-barang-form-view').hide();

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
        $('#barang_detail_kode_barang').val(selectedDetailBarang.kode_barang);
        $('#barang_detail_harga_satuan').val(formatRupiah(selectedDetailBarang.harga_satuan_barang));
        // $('#diskon').val(selectedDetailBarang.diskon);
    });

    $('#btn-batal-detail-barang-form-view').click(function() {
        Swal.fire({
            icon: 'question',
            title: 'Kembali Ke Form Barang ?',
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

    $('#btn-sesuai-kode-hs').click(function(e) {
        e.preventDefault();
        var str = $('#barang_detail_kode_hs').find('option:selected').text().split('-');
        $('#barang_detail_uraian').val("\n" + str[1].trim());
    });

    $('#barang_detail_kode_hs').select2({
        placeholder: "Pilih Kode HS/Pos Tarif",
        theme: "bootstrap-5",
    });

    $('#barang_detail_kategori_barang').select2({
        placeholder: "Pilih Kategori Barang",
        theme: "bootstrap-5",
    });

    $('#barang_detail_negara').select2({
        placeholder: "Pilih Negara",
        theme: "bootstrap-5",
    });

    $('#barang_detail_kode_jenis_kemasan').select2({
        placeholder: "Pilih Kode Jenis Kemasan",
        theme: "bootstrap-5",
    });

    $('#barang_detail_kode_jenis_pungutan').select2({
        placeholder: "Pilih Jenis Pungutan",
        theme: "bootstrap-5",
    });

    $('#barang_detail_kode_jenis_tarif').select2({
        placeholder: "Pilih Jenis Tarif",
        theme: "bootstrap-5",
    });

    $('#barang_detail_kode_fasilitas_tarif').select2({
        placeholder: "Pilih Fasilitas Tarif",
        theme: "bootstrap-5",
    });

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    // TABEL LIST BARANG
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

    // TABEL LIST DOKUMEN
    var tableListInformasiDokumen = $('.table-list-informasi-dokumen').DataTable({
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

    // TABEL LIST INFO PUNGUTAN
    var tableListInformasiPungutan = $('.table-list-informasi-pungutan').DataTable({
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

    // DROPDOWN
    $('#barang_detail_kode_satuan_barang').select2({
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

    // HELPER

    function formatRupiah(angka) {
        angka = angka || 0;
        angka = angka.toString().replace(/\./g, '').replace(/[^\d,]/g, '');
        var parts = angka.split(',');
        var ribuanFormatted = parts[0].split('').reverse().join('').match(/\d{1,3}/g).join('.').split('').reverse().join('');
        var desimal = parts[1] || '00';
        return ribuanFormatted + ',' + desimal;
    }
</script>


<?= $this->endSection(); ?>