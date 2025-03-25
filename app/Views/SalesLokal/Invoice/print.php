<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Penjualan Lokal</title>
    <style>
        body {
            font-size: 11px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            margin: 0;
            padding: 0;
        }

        @page {
            size: 9.5in 11in;
            margin: 0.5in;
        }

        .company-name {
            font-weight: 700;
            border: 1px solid;
            padding: 5px;
            border-radius: 5px;
            margin-bottom: 5px;
            display: inline-block;
            min-width: 100px;
        }

        .description-container {
            border: 1px solid;
            border-radius: 5px;
            min-height: 60px;
            margin-top: 5px;
            padding: 5px;
        }

        .item-table {
            border: 1px solid;
            width: 100%;
            font-size: 11px;
            margin-top: 5px;
            margin-bottom: 5px;
            border-collapse: collapse;
        }

        .item-table th,
        .item-table td {
            border: 1px solid;
            padding: 3px;
        }

        .rounded-border {
            border: 1px solid;
            border-radius: 5px;
            padding: 5px;
        }

        .signature-table {
            border-spacing: 15px 0;
            margin-top: 5px;
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
        
        .payment-list {
            margin: 5px 0;
            padding-left: 20px;
        }
    </style>
</head>

<body>
    <table class="w-100">
        <tr>
            <td style="width: 60%;">
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
                <div class="txt-bold txt-center" style="font-size: 18px;">Sales Invoice</div>
                <table class="w-100 rounded-border" style="margin-left: auto;">
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
                            <div class="txt-center"><?= $cleaned_string_document_no ?>&nbsp;</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="item-table">
        <tr>
            <th style="width: 30px;">No</th>
            <th>Item Description</th>
            <th style="width: 50px;">Qty</th>
            <th style="width: 50px;">Satuan</th>
            <th style="width: 80px;">Unit Price</th>
            <th style="width: 50px;">Disc %</th>
            <th style="width: 90px;">Amount</th>
        </tr>
        <?php
        $rowNumber = 1;
        foreach ($soData as $detail) :
        ?>
            <tr>
                <td class="txt-center"><?= $rowNumber ?></td>
                <td><?= $detail->nama_barang ?></td>
                <td class="txt-center"><?= $detail->qty_invoice ?></td>
                <td class="txt-center"><?= $detail->satuan ?></td>
                <td class="txt-right">Rp. <?= number_format($detail->harga_barang) ?></td>
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

    <table class="w-100" style="margin-top: 5px;">
        <tr>
            <td style="width: 40px;" valign="top">Say : </td>
            <td class="rounded-border" style="width: 60%;" valign="top">
                <?= (isset($invData->status_tax) && isset($invData->status_tax)) ? terbilang($invData->total_invoice) : terbilang($invData->total_invoice) ?>
            </td>
            <td class="rounded-border" style="width: 30%;">
                <table class="w-100">
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

    <table class="w-100" style="margin-top: 10px;">
        <tr>
            <td style="width: 60%;" valign="top">
                <div class="description-container">
                    <label class="description-label">Description: </label>
                    <ol class="payment-list">
                        <?= $companyAccount ?>
                    </ol>
                    <?= '' //$invData->keterangan ?>
                </div>
                <table style="margin-top: 10px;">
                    <tr>
                        <td style="height: 60px;border-bottom: 1px solid; width: 150px;">Hormat Kami</td>
                    </tr>
                    <tr>
                        <td>Date: </td>
                    </tr>
                </table>
            </td>
            <td valign="top" style="width: 40%;">
                <table class="w-100 rounded-border">
                    <tr>
                        <td>Tot Sub Stlh Pjk</td>
                        <td class="txt-right">Rp. <?= number_format($invData->total_invoice) ?></td>
                    </tr>
                </table>
                <div class="rounded-border" style="margin: 5px 0; height: 20px;">&nbsp;</div>
                <div class="rounded-border">
                    <table class="w-100 txt-bold">
                        <tr>
                            <td style="border-right: 1px solid;width: 100px">Total Invoice : </td>
                            <td class="txt-right">Rp. <?= number_format($invData->total_invoice) ?></td>
                        </tr>
                    </table>
                </div>
                <table style="margin-top: 10px; float: right;">
                    <tr>
                        <td style="height: 60px;border-bottom: 1px solid; width: 150px;">Diterima Oleh</td>
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