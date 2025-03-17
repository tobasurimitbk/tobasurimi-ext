<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Penjualan Lokal</title>
    <style>
        body {
            font-size: 10px; /* Ukuran font diperkecil */
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        @page {
            size: 8.27in 5.50in landscape;
            margin: 15px; /* Margin diperkecil */
            padding: 15px; /* Padding diperkecil */
        }

        .company-name {
            font-weight: 700;
            border: 1px solid;
            padding: 3px; /* Padding diperkecil */
            border-radius: 5px;
            margin-bottom: 1px;
            display: inline-block;
            min-width: 70px;
        }

        .description-container {
            border: 1px solid;
            border-radius: 5px;
            min-height: 50px; /* Tinggi diperkecil */
            margin-top: 3px; /* Margin diperkecil */
            padding-left: 5px; /* Padding diperkecil */
        }

        .item-table {
            border: 1px solid;
            width: 100%;
            font-size: 10px; /* Ukuran font tabel diperkecil */
            margin-top: 1px;
            margin-bottom: 3px; /* Margin diperkecil */
        }

        .item-table th,
        .item-table td {
            border-right: 1px solid;
            padding: 2px; /* Padding diperkecil */
        }

        .rounded-border {
            border: 1px solid;
            border-radius: 5px;
            padding: 3px; /* Padding diperkecil */
        }

        .signature-table {
            border-spacing: 15px 0; /* Spasi diperkecil */
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
            <td style="width: 60%;padding-right: 100px">
                <div class="company-name">
                    Toba Fish <br>
                    <?= $companyName ?>
                </div>

                <div>
                    <table class="w-100">
                        <tr>
                            <td style="width: 1px;vertical-align: top">Penagihan: </td>
                            <td class="rounded-border" style="padding: 5px">
                                <div><?= $invData->customer_name ?> - <?= $invData->customer_phone ?></div>
                                <div><?= $invData->customer_address ?></div>
                                <div>Termin : <?= $invData->terms ?></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td align="right" style="text-align: right;">
                <div class="txt-bold txt-center" style="font-size: 17px;">Sales Invoice</div>
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
                            <div>No. PO</div>
                            <div class="txt-center"><?= $invData->no_po ?>&nbsp;</div>
                        </td>


                        <td style="border-top: 1px solid;border-top-style: dashed">
                            <div>SJ/OF No.</div>
                            <?php
                            // Karakter yang akan dihapus
                            $unwanted_characters = array('[', '"', ']');

                            // Gantikan karakter tidak diinginkan dengan string kosong
                            $cleaned_string_document_no = str_replace($unwanted_characters, '', $invData->document_no);
                            ?>
                            <div class="txt-center" style="font-size: 10px;"><?= $cleaned_string_document_no ?>&nbsp;</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class=" item-table" border="1" style="border-collapse: collapse">
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
        foreach ($soData as $detail) :

        ?>
            <tr>
                <td class="txt-center" style="height: 1px;"><?= $rowNumber ?></td>
                <td><?= $detail->nama_barang ?></td>
                <td class="txt-center"><?= $detail->qty_invoice ?></td>
                <td class="txt-center"><?= $detail->satuan ?></td>
                <td class="txt-center">Rp. <?= number_format($detail->harga_barang) ?></td>
                <td class="txt-center"><?= $detail->disc ?></td>
                <td class="txt-right">Rp. <?= number_format($detail->amount) ?></td>
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
            </tr>
        <?php endfor; ?>
    </table>

    <table class="w-100" style="border-spacing: 3px 0;border: 1px;">
        <tr>
            <td style="width: 40px;" valign="top">Say : </td>
            <td class="rounded-border" style="width: 65%;" valign="top">
                <?= (isset($invData->status_tax) && isset($invData->status_tax)) ? terbilang($invData->total_invoice) : terbilang($invData->total_invoice) ?>
            </td>
            <td class="rounded-border">
                <table class="w-100" style="border-collapse: collapse">
                    <tr>
                        <td class="txt-right" style="border-bottom: 1px solid;">DPP: </td>
                        <td class="txt-right" style="border-bottom: 1px solid;">Rp. <?= number_format($invData->dpp) ?></td>
                    </tr>
                    <tr>
                        <td class="txt-right">PPN: </td>
                        <td class="txt-right">Rp. <?= (isset($invData->status_tax) && isset($invData->status_tax)) ? number_format($invData->ppn) : number_format($invData->ppn) ?></td>
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
                                <ol class="payment-list">
                                    <?= $companyAccount ?>
                                </ol>
                                <?= '' //$invData->keterangan 
                                ?>
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
                        <td class="txt-right">Rp. <?= number_format($invData->total_invoice) ?></td>
                    </tr>
                </table>
                <div class="rounded-border" style="margin-bottom: 3px;">&nbsp;</div>
                <div class="rounded-border">
                    <table class="w-100 txt-bold" style="border-collapse: collapse;">
                        <tr>
                            <td style="border-right: 1px solid;width: 100px">Total Invoice : </td>
                            <td class="txt-right">Rp. <?= number_format($invData->total_invoice) ?></td>
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