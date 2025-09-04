<!DOCTYPE html>
<html>

<head>
    <title>Laporan Pesanan Penjualan Per Pelanggan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border: 1px solid #000;
        }

        .customer-row {
            font-weight: bold;
        }

        .total-row {
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>TOBA FISH</h2>
        <h3>Pesanan Penjualan per Pelanggan(<?= $filter_status == "" ? '' : ($filter_status == "belum" ? 'Belum Proses' : 'Selesai') ?>)</h3>
        <p>Periode: <?= $dateStart ?> - <?= $dateEnd ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Pesanan</th>
                <th>Tanggal Pesan</th>
                <th>Tanggal Pengiriman</th>
                <th>Jumlah</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $item): ?>
                <?php if (isset($item['is_customer']) && $item['is_customer']): ?>
                    <tr class="customer-row">
                        <td colspan="6"><?= $item['no_sales_order'] ?></td>
                    </tr>
                <?php elseif (isset($item['is_total']) && $item['is_total']): ?>
                    <tr class="total-row">
                        <td colspan="4" class="text-right">Total</td>
                        <td class="text-right"><?= $item['no_sales_order'] ?></td>
                        <td></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td class="text-center"><?= $item['no'] ?></td>
                        <td><?= $item['no_sales_order'] ?></td>
                        <td><?= $item['order_date'] ?></td>
                        <td><?= $item['shipping_date'] ?></td>
                        <td class="text-right"><?= $item['sum_amount'] ?></td>
                        <td><?= $item['status'] ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>