<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Produksi</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            margin: 10px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
        }

        th {
            background-color: #ddd;
        }

        .text-left {
            text-align: left;
        }

        .header {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .sub-header {
            text-align: center;
            font-size: 13px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

    <div class="header">LAPORAN HASIL PRODUKSI</div>
    <div class="sub-header">PT. TOBA SURIMI INDUSTRIES, Tbk</div>
    <div class="sub-header">Tanggal Produksi: <?= $date_production ?></div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">NAMA BAHAN</th>
                <th rowspan="2">QTY DIGUNAKAN</th>
                <th colspan="<?= count($namaHasilUnik) ?>">HASIL PRODUKSI</th>
                <th rowspan="2">TOTAL HASIL</th>
            </tr>
            <tr>
                <?php foreach ($namaHasilUnik as $hasilNama): ?>
                    <th><?= $hasilNama ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tableData as $row): ?>
                <tr>
                    <td><?= $row['no'] ?></td>
                    <td class="text-left"><?= $row['nama_bahan'] ?></td>
                    <td><?= number_format($row['qty_digunakan'], 2) ?></td>
                    <?php foreach ($namaHasilUnik as $hasilNama): ?>
                        <td><?= number_format($row['hasil'][$hasilNama], 2) ?></td>
                    <?php endforeach; ?>
                    <td><?= number_format($row['total'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <th colspan="2">TOTAL</th>
                <th><?= number_format($totalQtyDigunakan, 2) ?></th>
                <?php foreach ($namaHasilUnik as $hasilNama): ?>
                    <th><?= number_format($colTotals[$hasilNama], 2) ?></th>
                <?php endforeach; ?>
                <th><?= number_format($grandTotal, 2) ?></th>
            </tr>
        </tbody>
    </table>

</body>

</html>