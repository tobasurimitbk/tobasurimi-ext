<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            font-size: 12px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        @page {
            size: 8.27in 5.50in landscape;
            margin: 25px;
            padding: 25px;
        }

        .company-name {
            font-weight: 700;
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
            height: 65px;
            margin-top: 8px;
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

        .item-table {
            border: 1px solid;
            width: 100%;

            margin-top: 2px;
            margin-bottom: 2px;
            border-collapse: collapse;
        }

        .item-table th {
            border-right: 1px solid;
            border-bottom: 1px solid;
        }

        .item-table td {
            border-right: 1px solid;
        }

        .signature-table {
            border-spacing: 30px 0;
            margin-top: 2px;
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
            <td style="width: 60%;padding-right: 100px">
                <div class="company-name"><?= $companyName ?></div>
                <div>
                    <table class="w-100">
                        <tr>
                            <td style="width: 1px;vertical-align: top">Customer: </td>
                            <td style="border: 1px solid;border-radius: 7px;padding: 5px">
                                <div><?= $soData[0]->customerCode ?> - <?= $soData[0]->customerName ?></div>
                                <div><?= $soData[0]->customerAddress ?></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td align="right" style="text-align: right;">
                <div class="txt-bold txt-center" style="font-size: 17px;">SURAT JALAN</div>
                <table class="w-100" style="border: 1px solid;border-radius: 7px;margin-left: auto;margin-right: 0">
                    <tr>
                        <td style="border-right: 1px solid;border-right-style: dashed;width: 50%;">
                            <div>Tgl</div>
                            <div class="txt-center"><?= $sjData->shipping_date ?></div>
                        </td>
                        <td>
                            <div>No. Surat</div>
                            <div class="txt-center"><?= $sjData->no_surat_jalan ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid;border-style: dashed dashed hidden hidden">
                            <div>No. Order</div>
                            <div class="txt-center">
                                <?php
                                // Check if there are multiple sales orders
                                if (count($soData) > 1) {
                                    // Use array_map to extract no_sales_order and join them with a comma
                                    echo implode(', ', array_map(function ($so) {
                                        return $so->no_sales_order;
                                    }, $soData));
                                } else {
                                    // If only one sales order, just display it
                                    echo $soData[0]->no_sales_order;
                                }
                                ?>
                            </div>
                        </td>
                        <td style="border-top: 1px solid;border-top-style: dashed">
                            <div>PO. No.</div>
                            <div class="txt-center"><?= $sjData->no_po ?>&nbsp;</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="item-table">
        <tr>
            <th>No</th>
            <th style="height: 1px;">Item Description</th>
            <th>No. OF</th>
            <th>Qty</th>
            <th>Satuan</th>
            <th>Harga</th>
            <th>% Diskon</th>
            <th>Jumlah</th>
        </tr>
        <?php
        $rowNumber = 1;
        $totalInv = 0;
        foreach ($soData as $detail) :
            $totalWithoutDisc = $detail->amt / ((100 - $detail->disc_pct) / 100);
            $totalInv += $detail->amt;
        ?>
            <tr>
                <td class="txt-center" style="height: 1px;"><?= $rowNumber; ?></td>
                <td><?= $detail->namaBarang ?></td>
                <td><?= $detail->no_sales_order ?></td>
                <td class="txt-center"><?= $detail->qty ?></td>
                <td class="txt-center"><?= $detail->kodeSatuan ?></td>
                <td class="txt-center"><?= number_format($totalWithoutDisc / $detail->qty) ?></td>
                <td class="txt-center"><?= $detail->disc_pct ?></td>
                <td class="txt-right"><?= number_format($detail->amt) ?></td>
            </tr>
        <?php
            $rowNumber++;
        endforeach;
        ?>
        <?php for ($i = 0; $i < (8 - count($soData)); $i++) : ?>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        <?php endfor; ?>
    </table>

    <table class="w-100" style="border-spacing: 3px 0;">
        <tr>
            <td colspan="2"></td>
            <td>
                <div class="txt-right" style="border: 1px solid;">Biaya Lain-lain: </div>
            </td>
            <td>
                <div class="txt-right" style="border: 1px solid;">0</div>
            </td>
        </tr>
        <tr>
            <td style="width: 1px;">Terbilang</td>
            <td style="width: 65%;">
                <div style="border: 1px solid;"><?= terbilang($totalInv) ?></div>
            </td>
            <td>
                <div class="txt-right" style="border: 1px solid;">Total Faktur: </div>
            </td>
            <td>
                <div class="txt-right" style="border: 1px solid;"><?= number_format($totalInv) ?></div>
            </td>
        </tr>
    </table>

    <table class="w-100">
        <tr>
            <td style="width: 350px;">
                <div>Catatan: </div>
                <div>Surat Jalan ini tidak berfungsi sebagai Penagihan</div>
                <div>Barang yang sudah diterima tidak dapat dikembalikan</div>
                <div>Kecuali memenuhi ketentuan perjanjian BS Exp Date</div>
            </td>
            <td style="padding-left: 50px">
                <div class="description-container">
                    <label class="description-label">Description: </label>
                    <?= $sjData->note ?>
                </div>
            </td>
        </tr>
    </table>

    <table class="signature-table">
        <tr style="vertical-align: top;">
            <td style="height: 65px;border-bottom: 1px solid;width: 90px">Disiapkan</td>
            <td style="height: 65px;border-bottom: 1px solid;width: 90px">Disetujui Oleh</td>
            <td style="height: 65px;border-bottom: 1px solid;width: 90px">Diantar Oleh</td>
            <td style="height: 65px;border-bottom: 1px solid;width: 90px">Diterima Oleh</td>
        </tr>
        <tr>
            <td>Date: </td>
            <td>Date: </td>
            <td>Date: </td>
            <td>Date: </td>
        </tr>
    </table>

</body>

</html>