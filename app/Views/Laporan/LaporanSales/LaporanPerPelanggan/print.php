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
        .text-center{
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
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
    
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="40%">Nama Pelanggan</th>
                <th width="15%">Kode Pelanggan</th>
                <th width="15%">Jumlah Data</th>
                <th width="25%">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data)): ?>
                <?php foreach ($data as $row): ?>
                <tr>
                    <td class="text-center"><?= $row['no'] ?></td>
                    <td><?= $row['nama_pelanggan'] ?></td>
                    <td class="text-center"><?= $row['kode_pelanggan'] ?></td>
                    <td class="text-center"><?= $row['count_invoice'] ?></td>
                    <td class="text-right"><?= $row['total_invoice'] ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="3" class="text-center">TOTAL</td>
                    <td class="text-center"><?= array_sum(array_column($data, 'count_invoice')) ?></td>
                    <td class="text-right"><?= $totalAllInvoice ?></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>