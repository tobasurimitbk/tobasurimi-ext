<?= $this->extend('layouts/template-new-window'); ?>
<?= $this->Section('content'); ?>
<section class="section">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">DETAIL TRANSAKSI</label>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input disabled <?= isset($transaksiJurnal) ?: 'readonly' ?> autocomplete="one-time-code" value="<?= !empty($transaksiJurnal) ? ($transaksiJurnal['metode_input'] == "system" ? $transaksiJurnal['no_transaksi'] : $transaksiJurnal['no_bukti']) : "" ?>" type="text" class="form-control no_bukti" id="no_bukti" name="no_bukti" placeholder="No Bukti">
                                <label for="floatingInput">No Bukti</label>
                            </div>
                            <div class="input-generate input-group-prepend group-prepend-password align-items-center" style="<?= isset($transaksiJurnal) ? "display:none;" : '' ?>">
                                <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="generateNewCode()">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group input-group-password">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled autocomplete="one-time-code" value="<?= !empty($transaksiJurnal) ?  date('d/m/Y', strtotime($transaksiJurnal['tanggal_transaksi'])) : date('d/m/Y'); ?>" type="text" class="form-control tanggal_transaksi" name="tanggal_transaksi" id="tanggal_transaksi" placeholder="">
                            <label for="floatingInput">Tanggal Transaksi</label>
                        </div>
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select disabled class="form-select divisi_id" id="divisi_id" name="divisi_id">
                            <option value="ALL">ALL</option>
                            <?php foreach ($divisi as $d): ?>
                                <option <?= isset($divisiId) ? ($divisiId == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>"><?= $d['divisi'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Departemen</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select disabled class="form-select type_transaksi" id="type_transaksi" name="type_transaksi" onchange="generateNewCode()">
                            <option value=""></option>
                            <?php foreach ($tipeTransaksi as $t): ?>
                                <option <?= isset($transaksiJurnal) ? ($transaksiJurnal['type_transaksi'] == $t['id'] ? 'selected' : '') : '' ?> value="<?= $t['id'] ?>"><?= $t['value'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Tipe Transaksi</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= !empty($transaksiJurnal) ? $transaksiJurnal['uraian_transaksi'] : ""; ?>" type="text" class="form-control uraian_transaksi" id="uraian_transaksi" name="uraian_transaksi" placeholder="Nomor Invoice">
                        <label for="floatingInput">Uraian Transaksi</label>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;">No</th>
                                <th style="text-align: center;">No Akun</th>
                                <th style="text-align: center;">Nama Akun</th>
                                <th style="text-align: center;">Uraian</th>
                                <th style="text-align: center;">Jumlah</th>
                                <th style="text-align: center;">Valas</th>
                                <th style="text-align: center;">Kurs</th>
                                <th style="text-align: center;">Debet (IDR)</th>
                                <th style="text-align: center;">Kredit (IDR)</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot class="foot-detail-table" id="foot-detail-table">
                            <tr>
                                <td colspan="7" style="text-align: right;"><b>GRAND TOTAL</b></td>
                                <td style="text-align: center;"><b>0.00</b></td>
                                <td style="text-align: center;"><b>0.00</b></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    var listJurnal = [];

    <?php if (isset($jurnalUmumList)): ?>
        listJurnal = <?= json_encode($jurnalUmumList) ?>;
        drawTable(listJurnal);

        function greatFormatRupiah(x) {
            var min = false;
            x = x.toString();
            if (x.includes("-")) {
                min = true;
            } else {
                min = false;
            }
            x = x.replace(/-/g, "");
            var parts = x.toString().split(".");
            parts[0] = parts[0].replace(/,/g, "");
            var bilangan = parts[0];

            var number_string = bilangan.toString(),
                sisa = number_string.length % 3,
                rupiah = number_string.substr(0, sisa),
                ribuan = number_string.substr(sisa).match(/\d{3}/g);

            if (ribuan) {
                var separator = sisa ? "," : "";
                rupiah += separator + ribuan.join(",");
            }
            parts[0] = rupiah;
            if (min) {
                return "-" + parts.join(".");
            } else {
                return parts.join(".");
            }

        }

    <?php endif; ?>

    function drawTable(listJurnal) {
        $('.body-detail-table').empty();
        $('.foot-detail-table').empty();

        var row = '';
        var row_detail = '';
        var no = 1;
        var debitTotal = 0;
        var kreditTotal = 0;
        // LIST
        listJurnal.map(item => {
            row += '<tr style="color:whitesmoke;">';
            row += '<td>' + no + '</td>';
            row += '<td>' + item.no_sub + '</td>';
            row += '<td>' + item.nama_sub + '</td>';
            row += '<td>' + item.keterangan + '</td>';
            row += '<td>' + greatFormatRupiah(item.jumlah) + '</td>';
            row += '<td>' + item.valas + '</td>';
            row += '<td>' + greatFormatRupiah(item.kurs) + '</td>';
            row += '<td>' + (item.jenis_transaksi == "debit" ? greatFormatRupiah(item.jumlah_idr) : greatFormatRupiah(0)) + '</td>';
            row += '<td>' + (item.jenis_transaksi == "kredit" ? greatFormatRupiah(item.jumlah_idr) : greatFormatRupiah(0)) + '</td>';

            debitTotal += item.jenis_transaksi == "debit" ? item.jumlah_idr : 0;
            kreditTotal += item.jenis_transaksi == "kredit" ? item.jumlah_idr : 0;
            no++;
        });

        // FOOTER
        row_detail += `
                    <tr>
                        <td colspan="7" style="text-align: right;"><b>GRAND TOTAL</b></td>
                        <td style="text-align: center;"><b>${greatFormatRupiah(debitTotal.toFixed(2))}</b></td>
                        <td style="text-align: center;"><b>${greatFormatRupiah(kreditTotal.toFixed(2))}</b></td>
                    </tr>
                `;

        $('.body-detail-table').append(row);
        $('.foot-detail-table').append(row_detail);

    }
</script>
<?= $this->endSection(); ?>