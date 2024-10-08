<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bea Cukai BC 2.3</title>
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
            text-align: center; /* Center align text */
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
    <h2>LAPORAN BEA CUKAI BC 2.3</h2>

    <table class="w-100">
        <tbody>
            <tr>
                <td style="width:150px">Tgl Mulai / Tgl Akhir</td>
                <td style="width:10px">:</td>
                <?php if ($condition['mulaiTanggalBC23'] != "" && $condition['selesaiTanggalBC23'] != "") : ?>
                    <td style="width:80px"><?= date('d/m/Y', strtotime($condition['mulaiTanggalBC23'])); ?></td>
                    <td style="width:10px"> S/D </td>
                    <td><?= date('d/m/Y', strtotime($condition['selesaiTanggalBC23'])); ?></td>
                <?php else : ?>
                    <td colspan="3" style="width:80px">ALL</td>
                <?php endif; ?>
            </tr>
        </tbody>
    </table>

    <table class="item-table">
        <thead class="thead-dark">
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Nama Supplier</th>
                <th rowspan="2">Tanggal</th>
                <th rowspan="2">No Aju</th>
                <th rowspan="2">No Daftar</th>
                <th rowspan="2">Tipe PO</th>
                <th colspan="3">PPN</th>
                <th colspan="3">PPH</th>
                <th colspan="3">BM</th>
            </tr>
            <tr>
                <th>Tidak Dipungut</th>
                <th>Di Bebaskan</th>
                <th>Di Tangguhkan</th>
                <th>Tidak Dipungut</th>
                <th>Di Bebaskan</th>
                <th>Di Tangguhkan</th>
                <th>Tidak Dipungut</th>
                <th>Di Bebaskan</th>
                <th>Di Tangguhkan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row) : ?>
                <tr>
                    <td><?= $row['no']; ?></td>
                    <td><?= $row['supplier_name']; ?></td>
                    <td><?= date('d/m/Y', strtotime($row['date'])); ?></td>
                    <td><?= $row['no_aju']; ?></td>
                    <td><?= $row['no_daftar']; ?></td>
                    <td><?= $row['po_type']; ?></td>
                    <td><?= formatRupiahPdfExcel($row['dataBCTarif']['PPN']['tidak_dipungut']); ?></td>
                    <td><?= formatRupiahPdfExcel($row['dataBCTarif']['PPN']['di_bebaskan']); ?></td>
                    <td><?= formatRupiahPdfExcel($row['dataBCTarif']['PPN']['di_tangguhkan']); ?></td>
                    <td><?= formatRupiahPdfExcel($row['dataBCTarif']['PPH']['tidak_dipungut']); ?></td>
                    <td><?= formatRupiahPdfExcel($row['dataBCTarif']['PPH']['di_bebaskan']); ?></td>
                    <td><?= formatRupiahPdfExcel($row['dataBCTarif']['PPH']['di_tangguhkan']); ?></td>
                    <td><?= formatRupiahPdfExcel($row['dataBCTarif']['BM']['tidak_dipungut']); ?></td>
                    <td><?= formatRupiahPdfExcel($row['dataBCTarif']['BM']['di_bebaskan']); ?></td>
                    <td><?= formatRupiahPdfExcel($row['dataBCTarif']['BM']['di_tangguhkan']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>
