<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .item-table {
            border: 1px solid;
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .item-table th,
        .item-table td {
            border: 1px solid;
            font-size: 12px;
            padding: 5px;
            text-align: center;
        }

        .item-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .w-100 {
            width: 100%;
        }

        .no-data {
            text-align: center;
            font-style: italic;
        }
    </style>
</head>

<body>
    <h2>LAPORAN BEA CUKAI BC 2.7</h2>

    <table class="w-100">
        <tbody>
            <tr>
                <td style="width:150px">Tgl Mulai / Tgl Akhir</td>
                <td style="width:10px">:</td>
                <?php if (isset($condition['mulaiTanggalBC27']) && isset($condition['selesaiTanggalBC27']) && !empty($condition['mulaiTanggalBC27']) && !empty($condition['selesaiTanggalBC27'])) : ?>
                    <td style="width:80px"><?= date('d/m/Y', strtotime($condition['mulaiTanggalBC27'])); ?></td>
                    <td style="width:10px"> S/D </td>
                    <td><?= date('d/m/Y', strtotime($condition['selesaiTanggalBC27'])); ?></td>
                <?php else : ?>
                    <td colspan="3" style="width:80px">ALL</td>
                <?php endif; ?>
            </tr>
        </tbody>
    </table>

    <table class="item-table">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Tanggal</th>
                <th rowspan="2">No Aju</th>
                <th rowspan="2">No Daftar</th>
                <th colspan="7">PPN</th>
                <th colspan="7">PPH</th>
                <th colspan="7">BM</th>
            </tr>
            <tr>
                <th>Di Bayar</th>
                <th>Di Bebaskan</th>
                <th>Ditanggung Pemerintah</th>
                <th>Di Lunasi</th>
                <th>Di Tunda</th>
                <th>Tidak Dipungut</th>
                <th>Di Tangguhkan</th>
                <th>Di Bayar</th>
                <th>Di Bebaskan</th>
                <th>Ditanggung Pemerintah</th>
                <th>Di Lunasi</th>
                <th>Di Tunda</th>
                <th>Tidak Dipungut</th>
                <th>Di Tangguhkan</th>
                <th>Di Bayar</th>
                <th>Di Bebaskan</th>
                <th>Ditanggung Pemerintah</th>
                <th>Di Lunasi</th>
                <th>Di Tunda</th>
                <th>Tidak Dipungut</th>
                <th>Di Tangguhkan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($data) && !empty($data)) : ?>
                <?php foreach ($data as $index => $row) :  var_dump($row)?>
                    <tr>
                        <td><?= $index + 1; ?></td>
                        <td><?= isset($row['tanggal_bc_27']) ? date('d/m/Y', strtotime($row['tanggal_bc_27'])) : '-'; ?></td>
                        <td><?= $row['no_aju'] ?? '-'; ?></td>
                        <td><?= $row['no_daftar'] ?? '-'; ?></td>
                        <!-- PPN -->
                        <td><?= isset($row['pungutan']['PPN']['di_bayar']) ? formatRupiahPdfExcel($row['pungutan']['PPN']['di_bayar']) : '0'; ?></td>
                        <td><?= isset($row['pungutan']['PPN']['di_bebaskan']) ? formatRupiahPdfExcel($row['pungutan']['PPN']['di_bebaskan']) : '0'; ?></td>
                        <td><?= isset($row['pungutan']['PPN']['di_tanggung_pemerintah']) ? formatRupiahPdfExcel($row['pungutan']['PPN']['di_tanggung_pemerintah']) : '0'; ?></td>
                        <td><?= isset($row['pungutan']['PPN']['di_lunasi']) ? formatRupiahPdfExcel($row['pungutan']['PPN']['di_lunasi']) : '0'; ?></td>
                        <td><?= isset($row['pungutan']['PPN']['di_tunda']) ? formatRupiahPdfExcel($row['pungutan']['PPN']['di_tunda']) : '0'; ?></td>
                        <td><?= isset($row['pungutan']['PPN']['tidak_dipungut']) ? formatRupiahPdfExcel($row['pungutan']['PPN']['tidak_dipungut']) : '0'; ?></td>
                        <td><?= isset($row['pungutan']['PPN']['di_tangguhkan']) ? formatRupiahPdfExcel($row['pungutan']['PPN']['di_tangguhkan']) : '0'; ?></td>
                        <!-- PPH -->
                        <td><?= isset($row['pungutan']['PPH']['di_bayar']) ? formatRupiahPdfExcel($row['pungutan']['PPH']['di_bayar']) : '0'; ?></td>
                        <td><?= isset($row['pungutan']['PPH']['di_bebaskan']) ? formatRupiahPdfExcel($row['pungutan']['PPH']['di_bebaskan']) : '0'; ?></td>
                        <td><?= isset($row['pungutan']['PPH']['di_tanggung_pemerintah']) ? formatRupiahPdfExcel($row['pungutan']['PPH']['di_tanggung_pemerintah']) : '0'; ?></td>
                        <td><?= isset($row['pungutan']['PPH']['di_lunasi']) ? formatRupiahPdfExcel($row['pungutan']['PPH']['di_lunasi']) : '0'; ?></td>
                        <td><?= isset($row['pungutan']['PPH']['di_tunda']) ? formatRupiahPdfExcel($row['pungutan']['PPH']['di_tunda']) : '0'; ?></td>
                        <td><?= isset($row['pungutan']['PPH']['tidak_dipungut']) ? formatRupiahPdfExcel($row['pungutan']['PPH']['tidak_dipungut']) : '0'; ?></td>
                        <td><?= isset($row['pungutan']['PPH']['di_tangguhkan']) ? formatRupiahPdfExcel($row['pungutan']['PPH']['di_tangguhkan']) : '0'; ?></td>
                        <!-- BM -->
                        <td><?= isset($row['pungutan']['BM']['di_bayar']) ? formatRupiahPdfExcel($row['pungutan']['BM']['di_bayar']) : '0'; ?></td>
                        <td><?= isset($row['pungutan']['BM']['di_bebaskan']) ? formatRupiahPdfExcel($row['pungutan']['BM']['di_bebaskan']) : '0'; ?></td>
                        <td><?= isset($row['pungutan']['BM']['di_tanggung_pemerintah']) ? formatRupiahPdfExcel($row['pungutan']['BM']['di_tanggung_pemerintah']) : '0'; ?></td>
                        <td><?= isset($row['pungutan']['BM']['di_lunasi']) ? formatRupiahPdfExcel($row['pungutan']['BM']['di_lunasi']) : '0'; ?></td>
                        <td><?= isset($row['pungutan']['BM']['di_tunda']) ? formatRupiahPdfExcel($row['pungutan']['BM']['di_tunda']) : '0'; ?></td>
                        <td><?= isset($row['pungutan']['BM']['tidak_dipungut']) ? formatRupiahPdfExcel($row['pungutan']['BM']['tidak_dipungut']) : '0'; ?></td>
                        <td><?= isset($row['pungutan']['BM']['di_tangguhkan']) ? formatRupiahPdfExcel($row['pungutan']['BM']['di_tangguhkan']) : '0'; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="25" class="no-data">Tidak ada data yang tersedia</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>

</html>
