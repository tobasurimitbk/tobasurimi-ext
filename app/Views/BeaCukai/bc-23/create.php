<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tambah Dokumen BC 2.3</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("bea-cukai-bc-23"); ?>">
                Batal
            </a>
            <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                Simpan
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table width="100%" class="mb-3">
                <tbody>
                    <tr style="color: black;">
                        <td width="150px"><b>Status</b></td>
                        <td width="10px">:</td>
                        <td>-</td>
                    </tr>
                    <tr style="color: black;">
                        <td width="150px"><b>Status Perbaikan</b></td>
                        <td width="30px">:</td>
                        <td>-</td>
                    </tr>
                </tbody>
            </table>

            <label class="form-label font-weight-bold lable-title mt-3">
                Informasi Dokumen
            </label>
            <div class="row mt-2">
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" placeholder="" class="form-control target input-picker" value="-">
                        <label for="floatingInput">Nomor AJU</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" placeholder="" class="form-control target input-picker" value="-">
                        <label for="floatingInput">Nomor Daftar</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" placeholder="" class="form-control target input-picker" value="-">
                        <label for="floatingInput">Tanggal Daftar</label>
                    </div>
                </div>
            </div>
            <label class="form-label font-weight-bold lable-title mt-2">
                Informasi Tempat
            </label>
            <div class="row mt-2">
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select kppbcBongkar" name="kppbcBongkar" id="kppbcBongkar" aria-label="Floating label select example">
                            <option value="">
                                - Kantor KPPBC Bongkar -
                            </option>
                            <?php foreach ($kantorBeaCukai as $k) : ?>
                                <option value="<?= $k->id ?>">
                                    - (<?= $k->kode ?>) <?= $k->kantor_name ?> -
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">KPPBC Bongkar</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select kppbcPengawas" name="kppbcPengawas" id="kppbcPengawas" aria-label="Floating label select example">
                            <option value="">
                                - Kantor KPPBC Pengawas -
                            </option>
                            <?php foreach ($kantorBeaCukai as $k) : ?>
                                <option value="<?= $k->id ?>">
                                    - (<?= $k->kode ?>) <?= $k->kantor_name ?> -
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">KPPBC Pengawas</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select kodeTujuanTpb" name="kodeTujuanTpb" id="kodeTujuanTpb" aria-label="Floating label select example">
                            <option value="">
                                - PILIH TUJUAN -
                            </option>
                            <?php foreach (\App\Constant\BeaCukai::DokumenTPB as $d) : ?>
                                <option value="<?= $d['kodeJenisTPB'] ?>">
                                    - <?= $d['namaJenisTPB'] ?> -
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Pilih Tujuan</label>
                    </div>
                </div>
            </div>
            <label class="form-label font-weight-bold lable-title mt-2">
                Supplier
            </label>
            <div class="row mt-2">
                <div class="col-sm-6 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select namaSupplier" name="namaSupplier" id="namaSupplier" aria-label="Floating label select example">
                            <option value="">
                                - Pilih Nama Supplier -
                            </option>
                            <?php foreach ($supplier as $s) : ?>
                                <option value="<?= $s->id ?>">
                                    - (<?= $s->kode ?>) <?= $k->kantor_name ?> -
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Nama Supplier</label>
                    </div>
                </div>
                <div class="col-sm-6 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" readonly name="negara" type="text" placeholder="Negara Supplier" class="form-control target input-picker">
                        <label for="floatingInput">Negara</label>
                    </div>
                </div>
                <div class="col-sm-6 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <textarea name="alamat" cols="30" rows="10" class="form-control" readonly></textarea>
                        <label for="floatingInput">Alamat</label>
                    </div>
                </div>
            </div>
            <label class="form-label font-weight-bold lable-title mt-2">
                Importir
            </label>
            <div class="row mt-2">
                <div class="col-sm-6 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" name="npwpImportir" type="number" placeholder="Identitas (NPWP)" class="form-control target input-picker">
                        <label for="floatingInput">Identitas (NPWP)</label>
                    </div>
                </div>
                <div class="col-sm-6 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" name="namaImportir" type="text" placeholder="Nama Importir" class="form-control target input-picker">
                        <label for="floatingInput">Nama Importir</label>
                    </div>
                </div>
                <div class="col-sm-6 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" name="noIzinTPBImportir" type="text" placeholder="No Izin TPB" class="form-control target input-picker">
                        <label for="floatingInput">No Izin TPB</label>
                    </div>
                </div>
                <div class="col-sm-6 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" name="APIImportir" type="text" placeholder="APIImportir" class="form-control target input-picker">
                        <label for="floatingInput">API</label>
                    </div>
                </div>
                <div class="col-sm-6 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <textarea name="alamatImportir" cols="30" rows="10" class="form-control"></textarea>
                        <label for="floatingInput">Alamat</label>
                    </div>
                </div>
            </div>
            <label class="form-label font-weight-bold lable-title mt-2">
                Pemilik Barang
            </label><br>
            <label class="mt-2">
                Sama dengan data importir
            </label>
            <div class="form-control border-0 custom-toggle-switch">
                <div class="form-check form-switch form-switch-lg">
                    <input class="form-check-input" type="checkbox" id="switchPemilikBarang">
                    <label class="form-check-label" for="switchPemilikBarang"></label>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-6 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" name="npwpPemilikBarang" type="number" placeholder="Identitas (NPWP)" class="form-control target input-picker">
                        <label for="floatingInput">Identitas (NPWP)</label>
                    </div>
                </div>
                <div class="col-sm-6 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" name="namaPemilikBarang" type="text" placeholder="Nama Importir" class="form-control target input-picker">
                        <label for="floatingInput">Nama Pemilik Barang</label>
                    </div>
                </div>
                <div class="col-sm-6 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <textarea name="alamatPemilikBarang" cols="30" rows="10" class="form-control"></textarea>
                        <label for="floatingInput">Alamat</label>
                    </div>

                </div>
                <div class="col-sm-6 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" name="APIPemilikBarang" type="text" placeholder="APIPemilikBarang" class="form-control target input-picker">
                        <label for="floatingInput">API</label>
                    </div>
                </div>
            </div>
            <label class="form-label font-weight-bold lable-title mt-2">
                PPJK
            </label>
            <div class="row mt-2">
                <div class="col-sm-6 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" name="PpjkNpwp" type="number" placeholder="Identitas (NPWP)" class="form-control target input-picker">
                        <label for="floatingInput">NPWP (Opsional)</label>
                    </div>
                </div>
                <div class="col-sm-6 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" name="PpjkNama" type="text" placeholder="Nama (Opsional)" class="form-control target input-picker">
                        <label for="floatingInput">Nama (Opsional)</label>
                    </div>
                </div>
                <div class="col-sm-6 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" name="PpjkTanggal" type="text" placeholder="Tanggal PPJK (Opsional)" class="form-control target input-picker">
                        <label for="floatingInput">Tanggal PPJK (Opsional)</label>
                    </div>
                </div>
                <div class="col-sm-6 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" name="PpjkNo" type="text" placeholder="No PPJK (Opsional)" class="form-control target input-picker">
                        <label for="floatingInput">No PPJK (Opsional)</label>
                    </div>
                </div>
                <div class="col-sm-6 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <textarea name="PpjkAlamat" cols="30" rows="10" class="form-control"></textarea>
                        <label for="floatingInput">Alamat (Opsional)</label>
                    </div>
                </div>
            </div>
            <label class="form-label font-weight-bold lable-title mt-2">
                Pengangkutan
            </label>
            <div class="row mt-2">
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select" name="caraPengangkutan" aria-label="Floating label select example">
                            <option value="">
                                - Cara Pengangkutan -
                            </option>
                            <?php foreach (\App\Constant\BeaCukai::Pengangkutan as $p) : ?>
                                <option value="<?= $p['kodePengangkutan'] ?>">
                                    - <?= $p['caraPengangkutan'] ?> -
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Cara Pengangkutan</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" name="namaSaranaPengangkut" type="text" placeholder="Nama Sarana Pengangkut" class="form-control target input-picker">
                        <label for="floatingInput">Nama Sarana Pengangkut</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-1">
                    <div class="row">
                        <div class="col-sm">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" name="noVoyFlight" type="text" placeholder="No Voy/Flight" class="form-control target input-picker">
                                <label for="floatingInput">No Voy / Flight</label>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select" name="pengangkutanNegara" aria-label="Floating label select example">
                                    <option value="">
                                        - Pilih Negara -
                                    </option>
                                    <?php foreach ($country as $c) : ?>
                                        <option value="<?= $c->code ?>">
                                            - <?= $c->country_name ?> -
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Pilih Negara</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" name="pelabuhanMuat" type="text" placeholder="Kode Pelabuhan Muat" class="form-control target input-picker">
                        <label for="floatingInput">Kode Pelabuhan Muat</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" name="pelabuhanTransit" type="text" placeholder="Kode Pelabuhan Transit" class="form-control target input-picker">
                        <label for="floatingInput">Kode Pelabuhan Transit</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" name="pelabuhanBongkar" type="text" placeholder="Kode Pelabuhan Bongkar" class="form-control target input-picker">
                        <label for="floatingInput">Kode Pelabuhan Bongkar</label>
                    </div>
                </div>
            </div>
            <label class="form-label font-weight-bold lable-title mt-2">
                Dokumen
            </label>
            <div class="row mt-2">
                <div class="col-sm-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select" name="noInvoice" aria-label="Floating label select example">
                            <option value="">
                                - Pilih Nomor Invoice -
                            </option>
                        </select>
                        <label for="floatingInput">Pilih No Invoice</label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" name="tanggalInvoice" type="text" placeholder="Tanggal Invoice" class="form-control target input-picker">
                        <label for="floatingInput">Tanggal Invoice</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" name="noFasilitasImport" type="text" placeholder="Nomor Fasilitas Import (Opsional)" class="form-control target input-picker">
                        <label for="floatingInput">Nomor Fasilitas Import (Opsional)</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" name="tanggalFasilitasImport" type="text" placeholder="Tanggal Fasilitas Import (Opsional)" class="form-control target input-picker">
                        <label for="floatingInput">Tanggal Fasilitas Import (Opsional)</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input autocomplete="one-time-code" name="tanggalFasilitasImport" type="text" placeholder="Tanggal Fasilitas Import (Opsional)" class="form-control target input-picker">
                        <label for="floatingInput">Tanggal Fasilitas Import (Opsional)</label>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
<script>
    $('#kppbcBongkar').select2({
        placeholder: "Pilih Kantor Bongkar",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#kppbcPengawas').select2({
        placeholder: "Pilih Kantor Pengawas",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#kodeTujuanTpb').select2({
        placeholder: "Pilih Tujuan",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#namaSupplier').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5",
        allowClear: true
    });

    $("select[name='caraPengangkutan']").select2({
        placeholder: "Pilih Cara Pengangkutan",
        theme: "bootstrap-5",
        allowClear: true
    });

    $("select[name='pengangkutanNegara']").select2({
        placeholder: "Pilih Negara",
        theme: "bootstrap-5",
        allowClear: true
    });


    $("input[name='PpjkTanggal']").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });


    $("input[name='tanggalInvoice']").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $("input[name='tanggalFasilitasImport']").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $("select[name='noInvoice']").select2({
        placeholder: "Pilih Nomor Invoice",
        theme: "bootstrap-5",
        allowClear: true
    });

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

    // SwitchBox

    $('#switchPemilikBarang').click(function() {
        var statusChecked = $(this).prop('checked');
        var npwpImportir = $("input[name='npwpImportir']").val();
        var namaImportir = $("input[name='namaImportir']").val();
        var APIImportir = $("input[name='APIImportir']").val();
        var alamatImportir = $("input[name='alamatImportir']").val();

        if (statusChecked) {
            $("input[name='npwpPemilikBarang']").attr('readonly', true).val(npwpImportir);
            $("input[name='namaPemilikBarang']").attr('readonly', true).val(namaImportir);
            $("textarea[name='alamatPemilikBarang']").attr('readonly', true).val(alamatImportir);
            $("input[name='APIPemilikBarang']").attr('readonly', true).val(APIImportir);
        } else {
            $("input[name='npwpPemilikBarang']").attr('readonly', false).val(null);
            $("input[name='namaPemilikBarang']").attr('readonly', false).val(null);
            $("textarea[name='alamatPemilikBarang']").attr('readonly', false).val(null);
            $("input[name='APIPemilikBarang']").attr('readonly', false).val(null);
        }
    });
</script>
<?= $this->endSection(); ?>