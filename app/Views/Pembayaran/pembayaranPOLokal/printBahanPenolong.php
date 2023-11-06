<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran PO Lokal</title>
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
            font-size: 16px;
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
        UNIT <?= strtoupper($detail['company']['company']) ?>
    </div>
    <h5>
        PEMBAYARAN PO BP
    </h5>
    <table align="center">
        <tbody>
            <tr>
                <td style=" font-size:13px;">
                    NO. <?= $detail['pembayaranDetail']['payment_no'] ?>
                </td>
            </tr>
        </tbody>
    </table>

    <table style="margin-top: 40px;">
        <tbody>
            <tr>
                <td>Supplier</td>
                <td>:</td>
                <td><?= $detail['supplierDetail']['name'] ?></td>
            </tr>
            <tr>
                <td>No Tanda Terima Supplier</td>
                <td>:</td>
                <td><?= $detail['tandaTerimaSupplier']['faktur_no'] ?></td>
            </tr>
            <tr>
                <td>Tanggal Bayar</td>
                <td>:</td>
                <td><?= date('d/m/Y', strtotime($detail['pembayaranDetail']['payment_date']))  ?></td>
            </tr>
            <tr>
                <td>Jatuh Tempo</td>
                <td>:</td>
                <td><?= date('d/m/Y', strtotime($detail['pembayaranDetail']['due_date'])); ?></td>
            </tr>
            <tr>
                <td>Nominal Pembayaran</td>
                <td>:</td>
                <td>Rp. <?= number_format($detail['pembayaranDetail']['amount'] ?? 0, 2, ',', '.')  ?></td>
            </tr>
            <tr>
                <td>Metode Pembayaran</td>
                <td>:</td>
                <td><?= $detail['pembayaranDetail']['payment_method'] ?></td>
            </tr>
            <tr>
                <td>Pembayaran Oleh</td>
                <td>:</td>
                <td><?= $detail['pembayaranDetail']['pembayaran_oleh'] ?></td>
            </tr>
        </tbody>
    </table>

    <h6>
        Item Pembayaran
    </h6>

    <table width="100%" border="1" id="dashed-border-table" style="margin-top:-20px">
        <thead>
            <tr align="center">
                <td style="width: 10px;">No</td>
                <td>Tanggal LPB</td>
                <td>No. LPB</td>
                <td>Item Name</td>
                <td>Qty</td>
                <td>Unit</td>
                <td>Total</td>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($detail['itemLpbList'] as $d) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= date('d/m/Y', strtotime($d['lpb_date'])) ?></td>
                    <td><?= $d['lpb_no'] ?></td>
                    <td><?= $d['item_name'] ?></td>
                    <td><?= $d['qty'] ?></td>
                    <td><?= $d['unit'] ?></td>
                    <td><?= "Rp " . number_format($d['price'], 2, ',', '.')  ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="6" style="text-align: right;">
                    Total
                </td>
                <td><?= "Rp " . number_format($detail['tandaTerimaSupplier']['nominal_faktur'], 2, ',', '.')  ?></td>
            </tr>
        </tbody>
    </table>


</body>

</html>