<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran PO Lokal BB</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            letter-spacing: 2px;
            font-size: 12px;
        }

        h5 {
            font-weight: normal;
            font-size: 18px;
            margin-bottom: 10px;
            text-align: center;
            font-weight: bold;
            margin-top: 8px;
        }

        h6 {
            font-weight: normal;
            font-size: 13px;
            text-align: left;
            font-weight: bold;
            margin-top: 10px;
        }

        hr {
            border: none;
            border-top: 1px dashed #000;
        }

        @page {
            size: 7.44in 10.00in landscape;
            margin: 29px;
            padding: 29px;
        }

        #dashed-border-table {
            border-collapse: collapse;
        }

        #dashed-border-table th,
        #dashed-border-table td {
            border: 1px dashed #000;
            padding: 5px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div style="text-align: center;">
        UNIT <?= strtoupper($company) ?>
    </div>
    <h5>
        PEMBAYARAN PO BB
    </h5>
    <table align="center">
        <tbody>
            <tr>
                <td style=" font-size:13px;">
                    NO. <?= $parentData['payment_no'] ?? '' ?>
                </td>
            </tr>
        </tbody>
    </table>
    <table style="margin-top: 40px;">
        <tbody>
            <tr>
                <td>Supplier</td>
                <td>:</td>
                <td><?= $parentData['supplier_name'] ?? '' ?></td>
            </tr>
            <tr>
                <td>Tipe Pembayaran</td>
                <td>:</td>
                <td><?= strtoupper($parentData['type_bayar'] ?? '') ?></td>
            </tr>
            <tr>
                <td>Tanggal Bayar</td>
                <td>:</td>
                <td><?= date('d/m/Y', strtotime($parentData['payment_date'] ?? ''))  ?></td>
            </tr>
            <tr>
                <td>Potongan</td>
                <td>:</td>
                <td><?= number_format($parentData['potongan_harga'] ?? 0, 2)  ?></td>
            </tr>
            <tr>
                <td>Total Dibayar (Termasuk Diskon)</td>
                <td>:</td>
                <td><?= number_format($parentData['amount'] ?? 0, 2)  ?></td>
            </tr>
            <tr>
                <td>Metode Pembayaran</td>
                <td>:</td>
                <td><?= $parentData['payment_method'] ?? '' ?></td>
            </tr>
            <tr>
                <td>Pembayaran Oleh</td>
                <td>:</td>
                <td><?= $parentData['pembayaran_oleh'] ?? '' ?></td>
            </tr>
        </tbody>
    </table>

    <h6>
        Data Pembelian Bahan Baku
    </h6>

    <table width="100%" border="1" id="dashed-border-table" style="margin-top:-20px">
        <thead>
            <tr>
                <th style="text-align: center;">No</th>
                <th style="text-align: center;">Tanggal PO</th>
                <th style="text-align: center;">No PO</th>
                <th style="text-align: center;">Barang</th>
                <th style="text-align: center;">Total Diterima</th>
                <th style="text-align: center;">Total Dibayar</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($childData as $d) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= date('d/m/Y', strtotime($d['po_date'])) ?></td>
                    <td><?= $d['po_no'] ?></td>
                    <td><?= $d['barang'] ?> | <?= $d['spek'] ?></td>
                    <td><?= number_format($d['total_qty_diterima'], 2) ?></td>
                    <td><?= number_format($d['total_dibayar'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="4" style="text-align: right;">
                    Total Pembayaran
                </td>
                <td style="text-align: center;">
                    <b><?= array_sum(array_column($childData, 'total_qty_diterima')) ?? 0 ?></b>
                </td>
                <td style="text-align: center;">
                    <b><?= number_format(array_sum(array_column($childData, 'total')) ?? 0, 2); ?></b>
                </td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: right;">
                    Diskon
                </td>
                <td style="text-align: center;">
                    <b><?= number_format($parentData['potongan_harga'] ?? 0, 2)  ?></b>
                </td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: right;">
                    Total Bayar
                </td>
                <td><b><?= number_format($parentData['amount'] ?? 0, 2); ?></b></td>
            </tr>
        </tbody>
    </table>

    <!-- Jika ada data panjar -->
    <?php if (!empty($panjarData)) : ?>
    <h6>
        Rincian Panjar
    </h6>
    <table width="100%" border="1" id="dashed-border-table" style="margin-top:-20px">
        <thead>
            <tr>
                <th style="text-align: center;">No</th>
                <th style="text-align: center;">Tanggal PO</th>
                <th style="text-align: center;">No PO</th>
                <th style="text-align: center;">Barang</th>
                <th style="text-align: center;">Total Diterima</th>
                <th style="text-align: center;">Total Dibayar</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($childData as $d) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= date('d/m/Y', strtotime($d['po_date'])) ?></td>
                    <td><?= $d['po_no'] ?></td>
                    <td><?= $d['barang'] ?> | <?= $d['spek'] ?></td>
                    <td><?= number_format($d['total_qty_diterima'], 2) ?></td>
                    <td><?= number_format($d['total_dibayar'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="4" style="text-align: right;">Total</td>
                <td><?= array_sum(array_column($childData, 'total_qty_diterima')) ?></td>
                <td><?= number_format(array_sum(array_column($childData, 'total_dibayar')), 2) ?></td>
            </tr>
        </tbody>
    </table>
    <?php endif; ?>

</body>
</html>