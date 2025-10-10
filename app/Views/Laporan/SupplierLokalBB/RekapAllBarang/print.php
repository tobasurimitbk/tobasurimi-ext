<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekap All Barang (Summary)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h2 {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
        }

        .date-info {
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-left: -35px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            vertical-align: middle;
        }

        .group-header {
            font-weight: bold;
            background-color: #e0e0e0;
            text-align: left;
        }

        .total-row {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .grand-total-row {
            font-weight: bold;
            background-color: #d0d0d0;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .no-border {
            border: none !important;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2><?= $header; ?></h2>
    </div>

    <div class="date-info">
        Tanggal :
        <?php if ($tanggalAwal && $tanggalAkhir) : ?>
            <?= date('d/m/Y', strtotime($tanggalAwal)) ?> s/d <?= date('d/m/Y', strtotime($tanggalAkhir)) ?>
        <?php else : ?>
            ALL
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">NO.</th>
                <th rowspan="2">BARANG</th>
                <th rowspan="2">SPEK</th>
                <th rowspan="2">DEPARTEMEN</th>
                <th rowspan="2">QTY</th>
                <th rowspan="2">SATUAN</th>
                <th colspan="3">HARIAN</th>
                <th colspan="3">TAMBAHAN HARIAN</th>
                <th colspan="3">TAMBAHAN BULANAN</th>
                <th colspan="3">TAMBAHAN LANGSUNG</th>
                <th rowspan="2">TOTAL</th>
            </tr>
            <tr>
                <th>DPP</th>
                <th>PPh</th>
                <th>Dibayarkan</th>
                <th>DPP</th>
                <th>PPh</th>
                <th>Dibayarkan</th>
                <th>DPP</th>
                <th>PPh</th>
                <th>Dibayarkan</th>
                <th>DPP</th>
                <th>PPh</th>
                <th>Dibayarkan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($groupedData as $barangName => $subGroups) : ?>
                <tr class="group-header">
                    <td colspan="19" class="text-left">Bahan Baku: <?= $barangName ?></td>
                </tr>

                <?php
                $groupTotals = [
                    'qtyPO' => 0,
                    'dppUmum' => 0,
                    'pphUmum' => 0,
                    'totalUmum' => 0,
                    'dppHarian' => 0,
                    'pphHarian' => 0,
                    'totalHarian' => 0,
                    'dppBulanan' => 0,
                    'pphBulanan' => 0,
                    'totalBulanan' => 0,
                    'subsidi' => 0,
                    'pphSubsidi' => 0,
                    'totalSubsidi' => 0,
                    'totalRow' => 0,
                ];
                ?>

                <?php foreach ($subGroups as $subKey => $record) : ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td class="text-left"><?= $record['barangName'] ?></td>
                        <td class="text-left"><?= $record['spekName'] ?></td>
                        <td class="text-left"><?= $record['divisiName'] ?></td>
                        <td><?= number_format($record['qtyPO'], 2) ?></td>
                        <td><?= $record['satuanName'] ?></td>
                        <td class="text-right"><?= number_format($record['dppUmum'], 2) ?></td>
                        <td class="text-right"><?= number_format($record['pphUmum'], 2) ?></td>
                        <td class="text-right"><?= number_format($record['totalUmum'], 2) ?></td>
                        <td class="text-right"><?= number_format($record['dppHarian'], 2) ?></td>
                        <td class="text-right"><?= number_format($record['pphHarian'], 2) ?></td>
                        <td class="text-right"><?= number_format($record['totalHarian'], 2) ?></td>
                        <td class="text-right"><?= number_format($record['dppBulanan'], 2) ?></td>
                        <td class="text-right"><?= number_format($record['pphBulanan'], 2) ?></td>
                        <td class="text-right"><?= number_format($record['totalBulanan'], 2) ?></td>
                        <td class="text-right"><?= number_format($record['subsidi'], 2) ?></td>
                        <td class="text-right"><?= number_format($record['pphSubsidi'], 2) ?></td>
                        <td class="text-right"><?= number_format($record['totalSubsidi'], 2) ?></td>
                        <td class="text-right"><?= number_format($record['totalRow'], 2) ?></td>
                    </tr>

                    <?php
                    // Akumulasi group totals
                    $groupTotals['qtyPO'] += $record['qtyPO'];
                    $groupTotals['dppUmum'] += $record['dppUmum'];
                    $groupTotals['pphUmum'] += $record['pphUmum'];
                    $groupTotals['totalUmum'] += $record['totalUmum'];
                    $groupTotals['dppHarian'] += $record['dppHarian'];
                    $groupTotals['pphHarian'] += $record['pphHarian'];
                    $groupTotals['totalHarian'] += $record['totalHarian'];
                    $groupTotals['dppBulanan'] += $record['dppBulanan'];
                    $groupTotals['pphBulanan'] += $record['pphBulanan'];
                    $groupTotals['totalBulanan'] += $record['totalBulanan'];
                    $groupTotals['subsidi'] += $record['subsidi'];
                    $groupTotals['pphSubsidi'] += $record['pphSubsidi'];
                    $groupTotals['totalSubsidi'] += $record['totalSubsidi'];
                    $groupTotals['totalRow'] += $record['totalRow'];
                    ?>
                <?php endforeach; ?>

                <tr class="total-row">
                    <td colspan="4" class="text-left">TOTAL</td>
                    <td class="text-right"><?= number_format($groupTotals['qtyPO'], 2) ?></td>
                    <td></td>
                    <td class="text-right"><?= number_format($groupTotals['dppUmum'], 2) ?></td>
                    <td class="text-right"><?= number_format($groupTotals['pphUmum'], 2) ?></td>
                    <td class="text-right"><?= number_format($groupTotals['totalUmum'], 2) ?></td>
                    <td class="text-right"><?= number_format($groupTotals['dppHarian'], 2) ?></td>
                    <td class="text-right"><?= number_format($groupTotals['pphHarian'], 2) ?></td>
                    <td class="text-right"><?= number_format($groupTotals['totalHarian'], 2) ?></td>
                    <td class="text-right"><?= number_format($groupTotals['dppBulanan'], 2) ?></td>
                    <td class="text-right"><?= number_format($groupTotals['pphBulanan'], 2) ?></td>
                    <td class="text-right"><?= number_format($groupTotals['totalBulanan'], 2) ?></td>
                    <td class="text-right"><?= number_format($groupTotals['subsidi'], 2) ?></td>
                    <td class="text-right"><?= number_format($groupTotals['pphSubsidi'], 2) ?></td>
                    <td class="text-right"><?= number_format($groupTotals['totalSubsidi'], 2) ?></td>
                    <td class="text-right"><?= number_format($groupTotals['totalRow'], 2) ?></td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($groupedData)) : ?>
                <tr>
                    <td colspan="19" class="text-center">Tidak ada data yang tersedia</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>

</html>