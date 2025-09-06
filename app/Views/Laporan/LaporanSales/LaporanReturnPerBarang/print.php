<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Return Penjualan Per Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border: 1px solid black;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
        }

        th {
            border: 1px solid black;
            text-align: center;
        }

        .customer-row {
            font-weight: bold;
        }

        .total-row {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <h1 style="text-align: center;">TOBA FISH</h1>
    <h2 style="text-align: center;">LAPORAN RETURN PENJUALAN PER BARANG</h2>
    <p style="text-align: center;">Periode: <?= $dateStart ?> - <?= $dateEnd ?></p>

    <table>
        <thead>
            <tr>
                <th>No. Return</th>
                <th>No. Dokumen</th>
                <th>Tanggal Return</th>
                <th>Keterangan</th>
                <th>Kuantitas</th>
                <th>Satuan</th>
                <th>Jumlah</th>
                <th>Nama Barang</th>
                <th>Nama Penjual</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $item): ?>
                <?php if (isset($item['is_barang']) && $item['is_barang']): ?>
                    <tr class="customer-row">
                        <td colspan="9" class="text-left"><?= $item['no_return'] ?></td>
                    </tr>
                <?php elseif (isset($item['is_total']) && $item['is_total']): ?>
                    <tr class="total-row">
                        <td colspan="9" class="text-left"><?= $item['no_return'] ?></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td class="text-center"><?= $item['no_return'] ?></td>
                        <td class="text-center"><?= $item['no_dokumen'] ?></td>
                        <td class="text-center"><?= $item['tanggal_return'] ?></td>
                        <td class="text-center"><?= $item['note'] ?></td>
                        <td class="text-right"><?= $item['sum_qty_return'] ?></td>
                        <td class="text-right"><?= $item['kode_satuan'] ?></td>
                        <td class="text-right"><?= $item['sum_amount_return'] ?></td>
                        <td class="text-center"><?= $item['nama_pelanggan'] ?></td>
                        <td class="text-center"><?= $item['nama_sales'] ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>