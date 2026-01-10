<!DOCTYPE html>
<html>

<head>
    <title>Laporan Penjualan Per Pelanggan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        .header {
            text-align: center;
        }

        .header h1 {
            margin-bottom: 5px;
        }

        .info {
            margin-bottom: 15px;
        }

        .text-center {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            font-weight: bold;
            background-color: #e0e0e0;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>TOBA FISH</h2>
        <h2 style="color: red;">PENJUALAN PER PELANGGAN</h2>
        <p><strong>Periode:</strong> <?= $dateStart ?> - <?= $dateEnd ?></p>
    </div>

    <?php if (!empty($groupedData)): ?>
        <?php foreach ($groupedData as $namaPenjual => $pelangganList): ?>
            <h3 style="margin-top:20px;"><?= $namaPenjual ?></h3>
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="25%">Nama Pelanggan</th>
                        <th width="15%">Kode Pelanggan</th>
                        <th width="5%">Jumlah Data</th>
                        <th width="25%">Jumlah Dengan Pajak</th>
                        <th width="25%">Jumlah Tanpa Pajak</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $totalPerPenjual = 0;
                    $totalPerPenjualBeforePPN = 0;
                    foreach ($pelangganList as $row):
                        $totalPerPenjual += $row['raw_total'];
                        $totalPerPenjualBeforePPN += $row['raw_total_before_ppn'];
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= $row['nama_pelanggan'] ?></td>
                            <td class="text-center"><?= $row['kode_pelanggan'] ?></td>
                            <td class="text-center"><?= $row['count_invoice'] ?></td>
                            <td class="text-right"><?= $row['total_invoice'] ?></td>
                            <td class="text-right"><?= $row['total_invoice_before_ppn'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="4" class="text-center">TOTAL <?= $namaPenjual ?></td>
                        <td class="text-right"><?= number_format($totalPerPenjual) ?></td>
                        <td class="text-right"><?= $totalPerPenjualBeforePPN ?></td>
                    </tr>
                </tbody>
            </table>
        <?php endforeach; ?>
        <table style="margin-top:20px;">
            <tr class="total-row">
                <td colspan="4" class="text-center">TOTAL SEMUA</td>
                <td class="text-right"><?= $totalAllInvoice ?></td>
                <td class="text-right"><?= $totalAllInvoiceBeforePPN ?></td>
            </tr>
        </table>
    <?php else: ?>
        <p>Tidak ada data</p>
    <?php endif; ?>
</body>

</html>