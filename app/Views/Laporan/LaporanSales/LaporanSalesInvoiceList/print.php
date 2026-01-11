<!DOCTYPE html>
<html>
<head>
    <title>Sales Invoices List</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; text-align: center; font-size: 12px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .txt-bold { font-weight: bold; }
        .customer-row { background-color: #e6e6e6; }
        .total-row { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h3 style="text-align: center;">TOBA FISH</h3>
    <h2 style="text-align: center; color: red;">Sales Invoices List</h2>
    <p style="text-align: center;">Periode: <?= $dateStart ?> - <?= $dateEnd ?></p>
    
    <table>
        <thead>
            <tr>
                <th>No. Faktur</th>
                <th>Tanggal Faktur</th>
                <th>Tanggal Jatuh Tempo</th>
                <th>Nama Pelanggan</th>
                <th>Nama Penjual</th>
                <th>Total Invoice</th>
                <th>Total Belum Dibayar</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td class="text-left"><?= $row['no_faktur'] ?></td>
                    <td class="text-left"><?= $row['tanggal_faktur'] ?></td>
                    <td class="text-left"><?= $row['tanggal_jatuh_tempo'] ?></td>
                    <td class="text-left"><?= $row['nama_pelanggan'] ?></td>
                    <td class="text-left"><?= $row['nama_sales'] ?></td>
                    <td class="text-right"><?= $row['total_invoice'] ?></td>
                    <td class="text-right"><?= $row['pay_amount'] ?></td>
                    <td class="text-left"><?= $row['keterangan'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>