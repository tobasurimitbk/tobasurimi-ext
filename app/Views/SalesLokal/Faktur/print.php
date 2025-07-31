<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Pembelian <?= $soData->customerName ?></title>
    <style>
        body {
            font-size: 12px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        @page {
            size: 8.27in 5.50in landscape;
            margin: 15px;
            padding: 15px;
        }

        .company-name {
            font-weight: 700;
            border: 1px solid;
            padding: 3px;
            border-radius: 5px;
            margin-bottom: 1px;
            display: inline-block;
            min-width: 70px;
        }

        .description-container {
            border: 1px solid;
            border-radius: 5px;
            min-height: 50px;
            margin-top: 3px;
            padding-left: 5px;
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
            border-top: none;
            border-bottom: none;
            padding: 2px 4px;
        }

        .item-table th:first-child,
        .item-table td:first-child {
            border-left: none;
        }

        .item-table th:last-child,
        .item-table td:last-child {
            border-right: none;
        }

        .item-table thead tr {
            border-bottom: 1px solid black;
        }

        .rounded-border {
            border: 1px solid;
            border-radius: 5px;
            padding: 3px;
        }

        .signature-table {
            border-spacing: 15px 0;
            margin-top: 1px;
        }

        .txt-bold {
            font-weight: 700;
        }

        .txt-center {
            text-align: center;
        }

        .txt-right {
            text-align: right;
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
                            <td class="rounded-border" style="padding: 5px">
                                <div><?= $soData->customerName ?> - <?= $soData->customerPhone ?></div>
                                <div><?= $soData->customerAddress ?></div>
                                <div>Termin : <?= $soData->terms ?></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td align="right" style="text-align: right;">
                <div class="txt-bold txt-center" style="font-size: 25px;margin-bottom:3px;">Faktur Pembelian</div>
                <table class="w-100 rounded-border" style="margin-left: auto;margin-right: 0">
                    <tr>
                        <td style="border-right: 1px solid;border-right-style: dashed;width: 50%;">
                            <div>Tgl. Faktur</div>
                            <div class="txt-center"><?= $soData->order_date ?></div>
                        </td>
                        <td>
                            <div>No. Faktur</div>
                            <div class="txt-center"><?= $soData->no_sales_order ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid;border-style: dashed dashed hidden hidden">
                            <div>No. PO</div>
                            <div class="txt-center"><?= $soData->no_po ?>&nbsp;</div>
                        </td>
                        <td style="border-top: 1px solid;border-top-style: dashed">
                            <div>Tgl. Pengiriman</div>
                            <div class="txt-center"><?= $soData->shipping_date ?></div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class=" item-table" border="1" style="border-collapse: collapse">
        <thead>
            <tr>
                <th>No</th>
                <th style="height: 1px;">Item Description</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Unit Price</th>
                <th>Disc</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $no = 1;
                $totalHarga = 0;
            ?>
            <?php 
                foreach ($soDet as $detail) : 
                    $totalHarga = ((float) $detail->amount) ;
                ?>
                <tr>
                    <td class="txt-center" style="width: 10px;height: 1px;"><?= $no++ ?></td>
                    <td><?= $detail->namaBarang ?></td>
                    <td class="txt-center"><?= $detail->qty ?></td>
                    <td class="txt-center"><?= $detail->kodeSatuan ?></td>
                    <td class="txt-center"><?= number_format(((float) $detail->harga), 2, ',', '.') ?></td>
                    <td class="txt-center"><?= number_format(((float) $detail->disc), 2, ',', '.') ?></td>
                    <td class="txt-center"><?= number_format(((float) $detail->amount), 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            <?php for ($i = 0; $i < (6 - count($soDet)); $i++) : ?>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <table class="w-100" style="border-spacing: 3px 0;border: 1px;">
        <tr>
            <td style="width: 40px;" valign="top">Say : </td>
            <td class="rounded-border" style="width: 65%;" valign="top">
                <?= terbilang(((float) $totalHarga)) ?> Rupiah
            </td>
            <td class="rounded-border">
                <table class="w-100" style="border-collapse: collapse">
                    <tr>
                        <td class="txt-left">Total Harga: </td>
                        <td class="txt-right">Rp <?= number_format(((float) $totalHarga), 2, ',', '.') ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="w-100">
        <tr>
            <td style="width: 70%;" valign="top">
                <table class="w-100">
                    <tr>
                        <td valign="top" style="width: 65%;">
                            <div class="description-container">
                                <label class="description-label">Description: </label>
                                <ol class="payment-list" style="margin-left: -23px; font-size:12px;">
                                    <?= $companyAccount ?>
                                </ol>
                                &nbsp;
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td valign="top">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 50%;">
                            <table class="signature-table">
                                <tr style="vertical-align: top;">
                                    <td style="height: 65px;border-bottom: 1px solid;">Hormat Kami</td>
                                </tr>
                                <tr>
                                    <td>Date: </td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 50%;">
                            <table class="signature-table" style="margin-left: auto; margin-right: 0;">
                                <tr style="vertical-align: top;">
                                    <td style="height: 65px;border-bottom: 1px solid;">Diterima Oleh</td>
                                </tr>
                                <tr>
                                    <td>Date: </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>