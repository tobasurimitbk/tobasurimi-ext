<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
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
            min-height: 65px;
            max-height: 130;
            margin-top: 20px;
            width: 100%;
            position: relative;
            padding-top: 7px;
            padding-left: 8px;
        }

        .description-label {
            position: absolute;
            top: -10px;
            background: white;
            left: 8px;
            padding-left: 3px;
            padding-right: 5px;
        }

        .item-table {
            border: 1px solid;
            width: 100%;
            height: 230px;
            margin-top: 10px;
        }

        .item-table th {
            border-right: 1px solid;
            border-bottom: 1px solid;
        }

        .item-table td {
            border-right: 1px solid;
        }

        .rounded-border {
            border: 1px solid;
            border-radius: 7px;
        }

        .signature-table {
            border-spacing: 30px 0;
            margin-top: 10px;
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
                            <td style="width: 1px;vertical-align: top">Penagihan: </td>
                            <td class="rounded-border" style="padding: 5px">
                                <div><?= $soData[0]->customerName ?></div>
                                <div><?= $soData[0]->customerAddress ?></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td align="right" style="text-align: right;">
                <div class="txt-bold txt-center">Sales Invoice</div>
                <table class="w-100 rounded-border" style="margin-left: auto;margin-right: 0">
                    <tr>
                        <td style="border-right: 1px solid;border-right-style: dashed;width: 50%;">
                            <div>Tgl. Faktur</div>
                            <div class="txt-center"><?= $invData->tanggal_faktur ?></div>
                        </td>
                        <td>
                            <div>No. Faktur</div>
                            <div class="txt-center"><?= $invData->no_faktur ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid;border-style: dashed dashed hidden hidden">
                            <div>Terms</div>
                            <div class="txt-center"><?= $soData[0]->termin ?></div>
                        </td>
                        <td style="border-top: 1px solid;border-top-style: dashed">
                            <div>SJ/OF No.</div>
                            <div class="txt-center"><?= $invData->docNo ?>&nbsp;</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    
    <table class="item-table" style="border-collapse: collapse">
        <tr>
            <th>No</th>
            <th style="height: 1px;">Item Description</th>
            <th>Qty</th>
            <th>Satuan</th>
            <th>Unit Price</th>
            <th>Disc %</th>
            <th>Amount</th>
        </tr>
        <?php
        $rowNumber = 1;
        foreach($soData as $detail): 
            $totalWithoutDisc = $detail->amt / ((100 - $detail->disc_pct) / 100);
        ?>
        <tr>
            <td class="txt-center" style="height: 1px;"><?= $rowNumber ?></td>
            <td><?= $detail->namaBarang ?></td>
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
        <?php for ($i = 0; $i < (9 - count($soData)); $i++): ?>
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
    </table>

    <table class="w-100" style="border-spacing: 3px 0;border: 1px;margin-top: -10px">
        <tr>
            <td style="width: 40px;" valign="top">Say : </td>
            <td class="rounded-border" style="width: 65%;" valign="top">
                <?= terbilang($invTotal) ?>
            </td>
            <td class="rounded-border">
                <table class="w-100" style="border-collapse: collapse">
                    <tr>
                        <td class="txt-right" style="border-bottom: 1px solid;">DPP: </td>
                        <td class="txt-right" style="border-bottom: 1px solid;"><?= number_format($soData[0]->total_harga - $soData[0]->ppn) ?></td>
                    </tr>
                    <tr>
                        <td class="txt-right">PPN: </td>
                        <td class="txt-right"><?= number_format($soData[0]->ppn) ?></td>
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
                                <?= $invData->keterangan ?>
                            </div>
                        </td>
                        <td valign="bottom">
                            <table class="signature-table">
                                <tr style="vertical-align: top;">
                                    <td style="height: 65px;border-bottom: 1px solid;width: 90px">Hormat Kami</td>
                                </tr>
                                <tr>
                                    <td>Date: </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
            <td valign="top">
                <table class="w-100 rounded-border" style="margin-bottom: 3px;">
                    <tr>
                        <td>Tot Sub Stlh Pjk</td>
                        <td class="txt-right"><?= number_format($soData[0]->total_harga) ?></td>
                    </tr>
                </table>
                <div class="rounded-border" style="margin-bottom: 3px;">&nbsp;</div>
                <div class="rounded-border">
                    <table class="w-100 txt-bold" style="border-collapse: collapse;">
                        <tr>
                            <td style="border-right: 1px solid;width: 100px">Total Invoice : </td>
                            <td class="txt-right"><?= number_format($invTotal) ?></td>
                        </tr>
                    </table>
                </div>
                <table class="signature-table" style="margin-left: auto;margin-right: 0;">
                    <tr style="vertical-align: top;">
                        <td style="height: 65px;border-bottom: 1px solid;width: 100px">Diterima Oleh</td>
                    </tr>
                    <tr>
                        <td>Date: </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>