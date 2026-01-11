<!DOCTYPE html>
<html>
<head>
    <title>Rincian Penjualan Per Pelanggan</title>
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
    <h2 style="text-align: center; color: red;">LAPORAN RINCIAN PENJUALAN PER PELANGGAN</h2>
    <p style="text-align: center;">Periode: <?= $dateStart ?> - <?= $dateEnd ?></p>
    
    <table>
        <thead>
            <tr>
                <th>No. Faktur</th>
                <th>Tanggal Faktur</th>
                <th>Keterangan</th>
                <th>Jumlah</th>
                <th>Nilai HPP</th>
                <th>Laba Kotor</th>
                <th>Nama Pelanggan</th>
                <th>Nama Penjual</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <?php if (isset($row['is_customer']) && $row['is_customer']): ?>
                    <tr class="customer-row">
                        <td class="text-left txt-bold" colspan="8"><?= $row['no_faktur'] ?></td>
                    </tr>
                <?php elseif (isset($row['is_total']) && $row['is_total']): ?>
                    <tr class="total-row">
                        <td class="text-left txt-bold" colspan="3"></td>
                        <td class="text-right txt-bold"><?= $row['total_invoice'] ?></td>
                        <td class="text-right txt-bold"><?= $row['amt_harga_pokok'] ?></td>
                        <td class="text-right txt-bold"><?= $row['amt_laba'] ?></td>
                        <td class="text-left txt-bold" colspan="2"></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td class="text-center"><?= $row['no_faktur'] ?></td>
                        <td class="text-center"><?= $row['tanggal_faktur'] ?></td>
                        <td class="text-center"><?= $row['keterangan'] ?></td>
                        <td class="text-right"><?= $row['total_invoice'] ?></td>
                        <td class="text-right"><?= $row['amt_harga_pokok'] ?></td>
                        <td class="text-right"><?= $row['amt_laba'] ?></td>
                        <td class="text-center"><?= $row['nama_pelanggan'] ?></td>
                        <td class="text-center"><?= $row['nama_sales'] ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>