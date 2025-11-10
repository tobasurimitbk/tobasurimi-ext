<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $header; ?></title>
    <style>
        @page {
            margin: 15px 20px;
        }

        body {
            font-size: 10px;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
        }

        h2 {
            margin-bottom: 5px;
            text-align: center;
        }

        .info {
            margin-bottom: 10px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 8px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            word-wrap: break-word;
        }

        th {
            background-color: #f0f0f0;
        }

        .text-left {
            text-align: left;
        }

        .group-title {
            margin-top: 25px;
            margin-bottom: 3px;
            font-weight: bold;
            font-size: 11px;
        }

        .col-no {
            width: 25px;
        }

        .col-supplier {
            width: 110px;
        }

        .col-nopo {
            width: 70px;
        }

        .col-date {
            width: 60px;
        }

        .col-dept {
            width: 100px;
        }

        .col-gudang {
            width: 80px;
        }

        .col-qty,
        .col-satuan,
        .col-unit {
            width: 45px;
        }

        .col-group {
            width: 55px;
        }
    </style>
</head>

<body>
    <h2><?= $header; ?></h2>
    <div class="info">
        Tanggal:
        <?php if (!empty($tanggalAwal) && !empty($tanggalAkhir)) : ?>
            <?= $tanggalAwal; ?> s/d <?= $tanggalAkhir; ?>
        <?php else : ?>
            ALL
        <?php endif; ?>
    </div>

    <?php if (!empty($data)) : ?>
        <?php foreach ($data as $barangName => $rows): ?>
            <div class="group-title">Bahan Baku: <?= htmlspecialchars($barangName); ?></div>

            <table>
                <thead>
                    <tr>
                        <th rowspan="2" class="col-no">No.</th>
                        <th rowspan="2" class="col-supplier">Supplier</th>
                        <th rowspan="2" class="col-nopo">No PO</th>
                        <th rowspan="2" class="col-date">Tgl PO</th>
                        <th rowspan="2" class="col-dept">Department</th>
                        <th rowspan="2" class="col-gudang">Gudang</th>
                        <th rowspan="2" class="col-qty">Qty</th>
                        <th rowspan="2" class="col-satuan">Satuan</th>
                        <th rowspan="2" class="col-unit">Unit</th>
                        <th colspan="3" class="col-group">Tambahan Umum</th>
                        <th colspan="3" class="col-group">Tambahan Harian</th>
                        <th colspan="3" class="col-group">Tambahan Bulanan</th>
                        <th colspan="3" class="col-group">Tambahan Langsung</th>
                        <th rowspan="2" class="col-group">Total</th>
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
                    <?php
                    $no = 1;
                    $subtotal = [
                        'Qty' => 0,
                        'DPP Umum' => 0,
                        'PPh Umum' => 0,
                        'Total Umum' => 0,
                        'DPP Harian' => 0,
                        'PPh Harian' => 0,
                        'Total Harian' => 0,
                        'DPP Bulanan' => 0,
                        'PPh Bulanan' => 0,
                        'Total Bulanan' => 0,
                        'DPP Tambahan' => 0,
                        'PPh Tambahan' => 0,
                        'Total Tambahan' => 0,
                        'Total' => 0,
                    ];

                    foreach ($rows as $row):
                        $subtotal['Qty'] += $row['Qty'];
                        $subtotal['DPP Umum'] += $row['DPP Umum'];
                        $subtotal['PPh Umum'] += $row['PPh Umum'];
                        $subtotal['Total Umum'] += $row['Total Umum'];
                        $subtotal['DPP Harian'] += $row['DPP Harian'];
                        $subtotal['PPh Harian'] += $row['PPh Harian'];
                        $subtotal['Total Harian'] += $row['Total Harian'];
                        $subtotal['DPP Bulanan'] += $row['DPP Bulanan'];
                        $subtotal['PPh Bulanan'] += $row['PPh Bulanan'];
                        $subtotal['Total Bulanan'] += $row['Total Bulanan'];
                        $subtotal['DPP Tambahan'] += $row['DPP Tambahan'];
                        $subtotal['PPh Tambahan'] += $row['PPh Tambahan'];
                        $subtotal['Total Tambahan'] += $row['Total Tambahan'];
                        $subtotal['Total'] += $row['Total'];
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td class="text-left"><?= htmlspecialchars($row['Supplier']); ?></td>
                            <td><?= htmlspecialchars($row['poNum']); ?></td>
                            <td><?= htmlspecialchars($row['poDate']); ?></td>
                            <td class="text-left"><?= htmlspecialchars($row['Divisi']); ?></td>
                            <td class="text-left"><?= htmlspecialchars($row['warehouseName']); ?></td>
                            <td><?= number_format($row['Qty'], 2); ?></td>
                            <td><?= htmlspecialchars($row['Satuan']); ?></td>
                            <td><?= htmlspecialchars($row['companyName']); ?></td>

                            <td><?= number_format($row['DPP Umum'], 2); ?></td>
                            <td><?= number_format($row['PPh Umum'], 2); ?></td>
                            <td><?= number_format($row['Total Umum'], 2); ?></td>

                            <td><?= number_format($row['DPP Harian'], 2); ?></td>
                            <td><?= number_format($row['PPh Harian'], 2); ?></td>
                            <td><?= number_format($row['Total Harian'], 2); ?></td>

                            <td><?= number_format($row['DPP Bulanan'], 2); ?></td>
                            <td><?= number_format($row['PPh Bulanan'], 2); ?></td>
                            <td><?= number_format($row['Total Bulanan'], 2); ?></td>

                            <td><?= number_format($row['DPP Tambahan'], 2); ?></td>
                            <td><?= number_format($row['PPh Tambahan'], 2); ?></td>
                            <td><?= number_format($row['Total Tambahan'], 2); ?></td>

                            <td><?= number_format($row['Total'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <tr style="font-weight: bold; background-color: #eee;">
                        <td colspan="6">SUBTOTAL <?= strtoupper($barangName); ?></td>
                        <td><?= number_format($subtotal['Qty'], 2); ?></td>
                        <td colspan="2"></td>

                        <td><?= number_format($subtotal['DPP Umum'], 2); ?></td>
                        <td><?= number_format($subtotal['PPh Umum'], 2); ?></td>
                        <td><?= number_format($subtotal['Total Umum'], 2); ?></td>

                        <td><?= number_format($subtotal['DPP Harian'], 2); ?></td>
                        <td><?= number_format($subtotal['PPh Harian'], 2); ?></td>
                        <td><?= number_format($subtotal['Total Harian'], 2); ?></td>

                        <td><?= number_format($subtotal['DPP Bulanan'], 2); ?></td>
                        <td><?= number_format($subtotal['PPh Bulanan'], 2); ?></td>
                        <td><?= number_format($subtotal['Total Bulanan'], 2); ?></td>

                        <td><?= number_format($subtotal['DPP Tambahan'], 2); ?></td>
                        <td><?= number_format($subtotal['PPh Tambahan'], 2); ?></td>
                        <td><?= number_format($subtotal['Total Tambahan'], 2); ?></td>

                        <td><?= number_format($subtotal['Total'], 2); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Tidak ada data yang tersedia.</p>
    <?php endif; ?>
</body>

</html>