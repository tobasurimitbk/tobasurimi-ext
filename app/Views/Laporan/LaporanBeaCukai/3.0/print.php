<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pungutan Bea Cukai BC 3.0</title>
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
    </style>
</head>

<body>
    <h2>LAPORAN BEA CUKAI BC 2.5</h2>

    <table class="w-100">
        <tbody>
            <tr>
                <td style="width:150px">Tgl Mulai / Tgl Akhir</td>
                <td style="width:10px">:</td>
                <?php if (isset($condition['mulaiTanggalBC25']) && isset($condition['selesaiTanggalBC25']) && $condition['mulaiTanggalBC25'] != "" && $condition['selesaiTanggalBC25'] != "") : ?>
                    <td style="width:80px"><?= date('d/m/Y', strtotime($condition['mulaiTanggalBC25'])); ?></td>
                    <td style="width:10px"> S/D </td>
                    <td><?= date('d/m/Y', strtotime($condition['selesaiTanggalBC25'])); ?></td>
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
                <th rowspan="2">No Stuffing</th>
                <th colspan="4">PPN</th>
                <th colspan="4">PPH</th>
                <th colspan="4">BM</th>
            </tr>
            <tr>
                <th>Di Bayar</th>
                <th>Di Bebaskan</th>
                <th>Ditanggung Pemerintah</th>
                <th>Di Lunasi</th>
                <th>Di Bayar</th>
                <th>Di Bebaskan</th>
                <th>Ditanggung Pemerintah</th>
                <th>Di Lunasi</th>
                <th>Di Bayar</th>
                <th>Di Bebaskan</th>
                <th>Ditanggung Pemerintah</th>
                <th>Di Lunasi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($dataBC25Result) && !empty($dataBC25Result)) : ?>
                <?php foreach ($dataBC25Result as $row) : ?>
                    <tr>
                        <td><?= isset($row['no']) ? $row['no'] : '-'; ?></td>
                        <td><?= isset($row['date']) ? date('d/m/Y', strtotime($row['date'])) : '-'; ?></td>
                        <td><?= isset($row['no_aju']) ? $row['no_aju'] : '-'; ?></td>
                        <td><?= isset($row['no_daftar']) ? $row['no_daftar'] : '-'; ?></td>
                        <td><?= isset($row['no_stuffing']) ? $row['no_stuffing'] : '-'; ?></td>
                        <td><?= isset($row['dataBCTarif']['PPN']['di_bayar']) ? formatRupiahPdfExcel($row['dataBCTarif']['PPN']['di_bayar']) : '0'; ?></td>
                        <td><?= isset($row['dataBCTarif']['PPN']['di_bebaskan']) ? formatRupiahPdfExcel($row['dataBCTarif']['PPN']['di_bebaskan']) : '0'; ?></td>
                        <td><?= isset($row['dataBCTarif']['PPN']['di_tanggung_pemerintah']) ? formatRupiahPdfExcel($row['dataBCTarif']['PPN']['di_tanggung_pemerintah']) : '0'; ?></td>
                        <td><?= isset($row['dataBCTarif']['PPN']['di_lunasi']) ? formatRupiahPdfExcel($row['dataBCTarif']['PPN']['di_lunasi']) : '0'; ?></td>
                        <td><?= isset($row['dataBCTarif']['PPH']['di_bayar']) ? formatRupiahPdfExcel($row['dataBCTarif']['PPH']['di_bayar']) : '0'; ?></td>
                        <td><?= isset($row['dataBCTarif']['PPH']['di_bebaskan']) ? formatRupiahPdfExcel($row['dataBCTarif']['PPH']['di_bebaskan']) : '0'; ?></td>
                        <td><?= isset($row['dataBCTarif']['PPH']['di_tanggung_pemerintah']) ? formatRupiahPdfExcel($row['dataBCTarif']['PPH']['di_tanggung_pemerintah']) : '0'; ?></td>
                        <td><?= isset($row['dataBCTarif']['PPH']['di_lunasi']) ? formatRupiahPdfExcel($row['dataBCTarif']['PPH']['di_lunasi']) : '0'; ?></td>
                        <td><?= isset($row['dataBCTarif']['BM']['di_bayar']) ? formatRupiahPdfExcel($row['dataBCTarif']['BM']['di_bayar']) : '0'; ?></td>
                        <td><?= isset($row['dataBCTarif']['BM']['di_bebaskan']) ? formatRupiahPdfExcel($row['dataBCTarif']['BM']['di_bebaskan']) : '0'; ?></td>
                        <td><?= isset($row['dataBCTarif']['BM']['di_tanggung_pemerintah']) ? formatRupiahPdfExcel($row['dataBCTarif']['BM']['di_tanggung_pemerintah']) : '0'; ?></td>
                        <td><?= isset($row['dataBCTarif']['BM']['di_lunasi']) ? formatRupiahPdfExcel($row['dataBCTarif']['BM']['di_lunasi']) : '0'; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="17">Tidak ada data yang tersedia</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>

</html>
