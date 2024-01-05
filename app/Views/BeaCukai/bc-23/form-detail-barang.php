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

            <!-- DETAIL BARANG VIEW -->
            <div class="detail-barang-form-view">
                <div class="mt-3">
                    <a class="btn btn-primary float-right detail-barang-form-view" id="btn-simpan-detail-barang-form-view" href="#">
                        Simpan
                    </a>
                    <a class="btn btn-danger float-right" href="<?= base_url('bea-cukai-bc-23/id/barang/' . encrypt($lpb->id)) ?>" style="margin-right: 8px;">
                        Batal
                    </a>
                </div>

                <table width="100%" class="mb-3">
                    <tbody>
                        <tr style="color: black;">
                            <td width="150px"><b>Nomor LPB</b></td>
                            <td width="10px">:</td>
                            <td><?= $lpb->no_penerimaan_barang ?></td>
                        </tr>
                        <tr style="color: black; height: 20px;">
                            <td colspan="3"></td>
                        </tr>
                        <tr style="color: black;">
                            <td width="150px"><b>Nomor PO</b></td>
                            <td width="30px">:</td>
                            <td><?= $poDetail != null ? $poDetail['po_no'] : '-' ?></td>
                        </tr>
                        <tr style="color: black; height: 20px;">
                            <td colspan="3"></td>
                        </tr>
                        <tr style="color: black;">
                            <td width="150px"><b>Nama Barang</b></td>
                            <td width="10px">:</td>
                            <td><?= $lpbDetail['nama_barang_dok'] ?></td>
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
                                <input readonly value="<?= $seriBarang ?>" id="barang_detail_seri_barang" name="barang_detail_seri_barang" type="number" class="form-control barang_detail_seri_barang" placeholder="">
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
                                <input value="<?= $barangDetail != null ? $barangDetail['kode_barang'] : '-' ?>" id="barang_detail_kode_barang" readonly name="barang_detail_kode_barang" type="text" class="form-control barang_detail_kode_barang" placeholder="">
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
                                <input readonly id="barang_detail_harga" value="<?= formatRupiah($lpbDetail['sub_total']) ?>" maxlength="24" name="barang_detail_harga" type="text" class="form-control barang_detail_harga" placeholder="" oninput="this.value = this.value.replace(/[^\d.]/g, '')">
                                <label>Harga</label>
                                <small><i>Harga total sesuai dengan LPB diterima</i></small>
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
                                <input value="<?= $lpbDetail['jml_masuk'] == null || $lpbDetail['jml_masuk'] == null ? 0 : formatRupiah($lpbDetail['sub_total'] / $lpbDetail['jml_masuk'])  ?>" id="barang_detail_harga_satuan" maxlength="24" name="barang_detail_harga_satuan" type="text" readonly class="form-control barang_detail_harga_satuan" placeholder="" oninput="this.value = this.value.replace(/[^\d.]/g, '')">
                                <label>Harga Satuan</label>
                                <small><i>Harga satuan diambil dari total harga LPB dibagi jumlah diterima LPB</i></small>
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
                                <input value="<?= ($lpbDetail['jml_masuk']) ?>" readonly id="barang_detail_jumlah_satuan" name="barang_detail_jumlah_satuan" type="text" class="form-control barang_detail_jumlah_satuan" placeholder="" oninput="this.value = this.value.replace(/[^\d.]/g, '')">
                                <label>Jumlah Satuan</label>
                                <small><i>Jumlah diterima sesuai dengan LPB</i></small>
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

    $('#barang_detail_kode_satuan_barang').select2({
        placeholder: "Pilih Kode Satuan Barang",
        theme: "bootstrap-5",
        ajax: {
            url: '<?= base_url('bea-cukai-bc-23/satuan-barang') ?>',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term,
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                };
            },
            cache: true
        },
        minimumInputLength: 1,
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