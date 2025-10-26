<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Form <?= $soData->customerName ?></title>
    <style>
        body {
            font-size: 12px;
            font-family: 'ARIAL UNICODE MS';
            font-weight: 500;
        }

        @page {
            size: 8.27in 5.50in landscape;
            margin: 25px;
            padding: 25px;
        }

        .dot-matrix-bold {
            font-weight: bold;
            text-shadow: 0.4px 0 0 currentColor, -0.4px 0 0 currentColor;
            letter-spacing: 0.02em;
        }

        .company-name {
            font-weight: 700;
            border: 0.5px solid;
            padding: 5px;
            border-radius: 7px;
            margin-bottom: 10px;
            display: inline-block;
            min-width: 70px;
            font-size: 15px;
        }

        .description-container {
            top: 2px;
            border: 0.5px solid;
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
        }

        /* .item-table {
            border: 0.5px solid;
            width: 100%;

            margin-top: 5px;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .item-table th {
            border-right: 1px solid;
            border-bottom: 1px solid;
        }

        .item-table td {
            border-right: 1px solid;
        } */

        .signature-table {
            margin-top: 5px;
        }

        .item-table {
            border: 0.5px solid black;
            /* border luar */
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 10px;
        }

        /* Semua sel: garis vertikal saja */
        .item-table th,
        .item-table td {
            border-left: 0.5px solid black;
            border-right: 0.5px solid black;
            border-top: none;
            border-bottom: none;
            padding: 2px 4px;
        }

        /* Hapus border kiri kolom pertama */
        .item-table th:first-child,
        .item-table td:first-child {
            border-left: none;
        }

        /* Hapus border kanan kolom terakhir */
        .item-table th:last-child,
        .item-table td:last-child {
            border-right: none;
        }

        /* Hanya untuk baris thead: tambahkan garis horizontal bawah */
        .item-table thead tr {
            border-bottom: 0.5px solid black;
        }

        .txt-bold {
            font-weight: 700;
        }

        .txt-center {
            text-align: center;
        }

        .w-100 {
            width: 100%;
        }
    </style>
</head>

<body>
    <table class="w-100">
        <tr>
            <td style="width: 70%;padding-right: 100px">
                <div class="company-name">
                    Toba Fish <br>
                    <?= $companyName ?>
                </div>
                <div>
                    <table class="w-100">
                        <tr>
                            <td style="width: 1px;vertical-align: top">Customer: </td>
                            <td style="border: 0.5px solid;border-radius: 7px;padding: 5px">
                                <div class="txt-bold"><?= $soData->customerName ?> - <?= $soData->customerPhone ?></div>
                                <div><?= $soData->customerAddress ?></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="width: 35%;text-align: right;">
                <div class="txt-bold txt-center" style="font-size: 35px; margin-bottom:3px;">ORDER FORM</div>
                <table class="w-100" style="border: 0.5px solid;border-radius: 7px;margin-right: 0">
                    <tr>
                        <td style="border-right: 0.5px solid;border-right-style: dashed">
                            <div>Tgl. Pemesanan</div>
                            <div class="txt-center"><?= $soData->order_date ?></div>
                        </td>
                        <td>
                            <div>No. Pemesanan</div>
                            <div class="txt-center"><?= $soData->no_sales_order ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 0.5px solid;border-style: dashed dashed hidden hidden">
                            <div>No. PO</div>
                            <div class="txt-center"><?= $soData->no_po ?></div>
                        </td>
                        <td style="border-top: 0.5px solid;border-top-style: dashed">
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
                <th>No</th>
                <th>Item Description</th>
                <th>Qty</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($soDet as $detail) : ?>
                <tr>
                    <td class="txt-center" style="width: 10px;height: 1px;"><?= $no++ ?></td>
                    <td><?= $detail->namaBarang ?></td>
                    <td class="txt-center"><?= $detail->qty ?></td>
                    <td class="txt-center"><?= $detail->kodeSatuan ?></td>
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

    <div class="description-container">
        <label class="description-label">Description: </label>
        <?= $soData->keterangan ?>
    </div>

    <table class="signature-table">
        <tr style="vertical-align: top;">
            <td style="height: 65px;border-bottom: 1px solid;width: 90px">Sales</td>
            <td style="width: 80px"></td>
            <td style="height: 65px;border-bottom: 1px solid;width: 90px">Gudang</td>
            <td style="width: 80px"></td>
            <td style="height: 65px;border-bottom: 1px solid;width: 90px">Produksi</td>
        </tr>
        <tr>
            <td>Date: </td>
            <td style="width: 80px"></td>
            <td>Date: </td>
            <td style="width: 80px"></td>
            <td>Date: </td>
        </tr>
    </table>

</body>

</html>