<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Form <?= $soData->customerName ?></title>
    <style>
        body {
            font-size: 12px;
            font-family: 'Courier New', monospace;
            font-weight: 700; /* Ketebalan utama untuk semua teks */
        }

        @page {
            size: 8.27in 5.50in landscape;
            margin: 25px;
            padding: 25px;
        }

        .company-name {
            font-weight: 900; /* Ketebalan ekstra untuk perusahaan */
            border: 1px solid;
            padding: 5px;
            border-radius: 7px;
            margin-bottom: 10px;
            display: inline-block;
            min-width: 70px
        }

        .description-container {
            border: 1px solid;
            border-radius: 7px;
            height: 50px;
            width: 60%;
            position: relative;
            padding-top: 7px;
            padding-left: 17px;
        }

        .description-label {
            position: absolute;
            top: -10px;
            background: white;
            left: 15px;
            padding-left: 3px;
            padding-right: 5px;
            font-weight: 900; /* Ketebalan ekstra */
        }

        .item-table {
            border: 1px solid black;
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 10px;
        }

        .item-table th,
        .item-table td {
            border-left: 1px solid black;
            border-right: 1px solid black;
            padding: 3px 4px;
            font-weight: 800; /* Lebih tebal dari teks biasa */
        }

        .item-table thead tr {
            border-bottom: 1px solid black;
        }

        .txt-bold {
            font-weight: 900; /* Ketebalan maksimum */
        }

        .txt-center {
            text-align: center;
        }

        .w-100 {
            width: 100%;
        }

        /* ---------- TEKNIK KHUSUS UNTUK PRINTER DOT MATRIX ---------- */
        .dot-matrix-bold {
            text-shadow: 0.35px 0 0 currentColor, -0.35px 0 0 currentColor;
            letter-spacing: 0.02em;
        }
        
        .order-title {
            font-weight: 900;
            font-size: 25px;
            letter-spacing: 1px;
        }
        
        table, tr, td, th {
            font-weight: 800; /* Ketebalan khusus untuk tabel */
        }
    </style>
</head>

<body>
    <table class="w-100">
        <tr>
            <td style="width: 60%;padding-right: 100px">
                <div class="company-name dot-matrix-bold">
                    Toba Fish <br>
                    <?= $companyName ?>
                </div>
                <div>
                    <table class="w-100">
                        <tr>
                            <td style="width: 1px;vertical-align: top">Customer: </td>
                            <td style="border: 1px solid;border-radius: 7px;padding: 5px" class="dot-matrix-bold">
                                <div><?= $soData->customerName ?> - <?= $soData->customerPhone ?></div>
                                <div><?= $soData->customerAddress ?></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td align="right" style="text-align: right;">
                <div class="txt-bold txt-center order-title dot-matrix-bold" style="margin-bottom:3px;">ORDER FORM</div>
                <table class="w-100" style="border: 1px solid;border-radius: 7px;margin-left: auto;margin-right: 0">
                    <tr>
                        <td style="border-right: 1px solid;border-right-style: dashed" class="dot-matrix-bold">
                            <div>Tgl. Pemesanan</div>
                            <div class="txt-center"><?= $soData->order_date ?></div>
                        </td>
                        <td class="dot-matrix-bold">
                            <div>No. Pemesanan</div>
                            <div class="txt-center"><?= $soData->no_sales_order ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid;border-style: dashed dashed hidden hidden" class="dot-matrix-bold">
                            <div>No. PO</div>
                            <div class="txt-center"><?= $soData->no_po ?></div>
                        </td>
                        <td style="border-top: 1px solid;border-top-style: dashed" class="dot-matrix-bold">
                            <div>Tgl. Pengiriman</div>
                            <div class="txt-center"><?= $soData->shipping_date ?></div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="item-table">
        <thead>
            <tr>
                <th class="dot-matrix-bold">No</th>
                <th class="dot-matrix-bold">Item Description</th>
                <th class="dot-matrix-bold">Qty</th>
                <th class="dot-matrix-bold">Satuan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($soDet as $detail) : ?>
                <tr>
                    <td class="txt-center dot-matrix-bold" style="width: 10px;height: 1px;"><?= $no++ ?></td>
                    <td class="dot-matrix-bold"><?= $detail->namaBarang ?></td>
                    <td class="txt-center dot-matrix-bold"><?= $detail->qty ?></td>
                    <td class="txt-center dot-matrix-bold"><?= $detail->kodeSatuan ?></td>
                </tr>
            <?php endforeach; ?>
            <?php for ($i = 0; $i < (8 - count($soDet)); $i++) : ?>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <div class="description-container dot-matrix-bold">
        <label class="description-label">Description: </label>
        <span class="dot-matrix-bold" style="font-weight: 800;"><?= $soData->keterangan ?></span>
    </div>

    <table class="signature-table">
        <tr style="vertical-align: top;">
            <td style="height: 65px;border-bottom: 1px solid;width: 90px" class="dot-matrix-bold">Sales</td>
            <td style="width: 80px"></td>
            <td style="height: 65px;border-bottom: 1px solid;width: 90px" class="dot-matrix-bold">Gudang</td>
            <td style="width: 80px"></td>
            <td style="height: 65px;border-bottom: 1px solid;width: 90px" class="dot-matrix-bold">Produksi</td>
        </tr>
        <tr>
            <td class="dot-matrix-bold">Date: </td>
            <td style="width: 80px"></td>
            <td class="dot-matrix-bold">Date: </td>
            <td style="width: 80px"></td>
            <td class="dot-matrix-bold">Date: </td>
        </tr>
    </table>

</body>

</html>