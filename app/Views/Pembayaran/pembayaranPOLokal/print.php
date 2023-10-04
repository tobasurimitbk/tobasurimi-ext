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
        }

        h6 {
            font-weight: normal;
            font-size: 16px;
            text-align: left;
            font-weight: bold;
            margin-top: 15px;
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

        .border-table {
            border-collapse: collapse;
            width: 100%;
        }

        .border-table tr {
            border-bottom: 1px solid black;
        }

        .border-table td,
        .border-table th {
            border: 1px solid black;
            padding: 8px;
        }

        .border-table th {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <h5>
        PEMBAYARAN PO LOKAL
    </h5>
    <table align="center">
        <tbody>
            <tr>
                <td style=" font-size:13px;">
                    NO. <?= $dataPembayaranPOLokal->payment_no ?>
                </td>
            </tr>
        </tbody>
    </table>

    <table style="margin-top: 40px;">
        <tbody>
            <tr>
                <td>Supplier</td>
                <td>:</td>
                <td><?= $supplier->name; ?></td>
            </tr>
            <tr>
                <td>Tanggal Bayar</td>
                <td>:</td>
                <td><?= $dataPembayaranPOLokal->payment_date ?></td>
            </tr>
            <tr>
                <td>Jatuh Tempo</td>
                <td>:</td>
                <td><?= $dataPembayaranPOLokal->due_date; ?></td>
            </tr>
            <tr>
                <td>Rekap Faktur</td>
                <td>:</td>
                <td><?= $summary->summary_no; ?></td>
            </tr>
            <tr>
                <td>Nominal Pembayaran</td>
                <td>:</td>
                <td>Rp. <?= number_format($dataPembayaranPOLokal->amount ?? 0, 2, ',', '.')  ?></td>
            </tr>
            <tr>
                <td>Metode Pembayaran</td>
                <td>:</td>
                <td><?= $dataPembayaranPOLokal->payment_method ?></td>
            </tr>
            <tr>
                <td>Pembayaran Oleh</td>
                <td>:</td>
                <td><?= session()->get("login")->name; ?></td>
            </tr>
        </tbody>
    </table>

    <h6>
        Item Pembayaran
    </h6>

    <table width="100%" border="1" class="border-table">
        <thead>
            <tr align="center">
                <td>NO</td>
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
            <?php foreach ($itemList as $i) : ?>
                <tr align="center">
                    <td><?= $no++; ?></td>
                    <td><?= $i->lpb_date ?></td>
                    <td><?= $i->no_lpb ?></td>
                    <td><?= $i->item_name ?></td>
                    <td><?= $i->qty ?></td>
                    <td><?= $i->unit ?></td>
                    <td> Rp. <?= number_format($i->total ?? 0, 2, ',', '.')   ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>