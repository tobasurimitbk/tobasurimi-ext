<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan</title>
    <style>
        body {
            font-size: 11px;
            font-family: 'DejaVu Sans Mono';
            font-weight: 500;
        }

        @page {
            size: 8.27in 5.50in landscape;
            margin-top: 10px !important;
            margin: 25px;
            padding: 25px;
        }

        .company-name {
            font-weight: 700;
            border: 0.5px solid;
            padding: 5px;
            border-radius: 7px;
            margin-bottom: 10px;
            display: inline-block;
            min-width: 70px
        }

        .description-container {
            border: 0.5px solid;
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
            padding: 0 5px;
        }

        .item-table {
            border: 0.5px solid;
            width: 100%;
            margin: 2px 0;
            border-collapse: collapse;
        }

        .item-table th {
            border-right: 0.5px solid;
            border-bottom: 0.5px solid;
        }

        .item-table td {
            border-right: 0.5px solid;
        }

        .signature-table {
            border-spacing: 30px 0;
            margin-top: 2px;
        }

        .txt-bold {
            font-weight: 500;
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
    <?php
    // pastikan variabel tidak kosong
    $hasSO = !empty($soData);
    $firstSO = $hasSO ? $soData[0] : $sjData;
    ?>
    <table class="w-100">
        <tr>
            <td style="width:70%;padding-right:100px">
                <div class="company-name"><?= $companyName ?? '' ?></div>
                <div>
                    <table class="w-100">
                        <tr>
                            <td style="width:1px;vertical-align:top">Customer: </td>
                            <td style="border:0.5px solid;border-radius:7px;padding:5px">
                                <div class="txt-bold">
                                    <?= $firstSO->customerName ?? '-' ?>
                                    <?= !empty($firstSO->phone) ? ' - ' . $firstSO->phone : '' ?>
                                </div>
                                <div class="txt-bold"><?= $firstSO->customerAddress ?? '-' ?></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td align="right">
                <div class="txt-bold txt-center" style="font-size:25px;margin-bottom:3px;">SURAT JALAN</div>
                <table class="w-100" style="border:0.5px solid;border-radius:7px;margin-left:auto;">
                    <tr>
                        <td style="border-right:0.5px dashed;width:50%;">
                            <div>Tgl</div>
                            <div class="txt-center"><?= $sjData->shipping_date ?? '' ?></div>
                        </td>
                        <td>
                            <div>No. Surat</div>
                            <div class="txt-center"><?= $sjData->no_surat_jalan ?? '' ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-right:0.5px dashed;border-top:0.5px dashed;width:50%;">
                            <div>No. Order</div>
                            <div class="txt-center">
                                <?php
                                $uniqueSalesOrders = [];
                                if ($hasSO) {
                                    foreach ($soData as $so) {
                                        if (!in_array($so->no_sales_order, $uniqueSalesOrders)) {
                                            $uniqueSalesOrders[] = $so->no_sales_order;
                                        }
                                    }
                                }
                                echo !empty($uniqueSalesOrders)
                                    ? implode(', ', $uniqueSalesOrders)
                                    : '-';
                                ?>
                            </div>
                        </td>
                        <td style="border-top:0.5px dashed">
                            <div>PO. No.</div>
                            <div class="txt-center"><?= $sjData->no_po ?? '' ?>&nbsp;</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="item-table">
        <tr>
            <th>No</th>
            <th>Item Description</th>
            <th>No. OF</th>
            <th>Qty</th>
            <th>Satuan</th>
            <th>Harga</th>
            <th>% Diskon</th>
            <th>Jumlah</th>
        </tr>
        <?php
        $rowNumber = 1;
        $totalInv  = 0;

        if ($sjDetailData) {
            foreach ($sjDetailData as $detail) :
                $disc = $detail->disc ?? 0;
                $amt  = $detail->amount ?? 0;
                $qty  = $detail->qty ?? 0;
                $totalWithoutDisc = $disc < 100 ? ($amt / ((100 - $disc) / 100)) : $amt;
                $totalInv += $amt;
        ?>
                <tr>
                    <td class="txt-center"><?= $rowNumber++; ?></td>
                    <td><?= $detail->nama_barang ?? '-' ?></td>
                    <td><?= $detail->no_sales_order ?? '-' ?></td>
                    <td class="txt-center"><?= $qty ?></td>
                    <td class="txt-center"><?= $detail->satuan ?? '-' ?></td>
                    <td class="txt-center"><?= number_format($qty ? $totalWithoutDisc / $qty : 0) ?></td>
                    <td class="txt-center"><?= $disc ?></td>
                    <td class="txt-right"><?= number_format($amt) ?></td>
                </tr>
            <?php
            endforeach;
        } else {
            foreach ($soData as $detail) :
                $disc = $detail->disc_pct ?? 0;
                $amt  = $detail->amt ?? 0;
                $qty  = $detail->qty ?? 0;
                $totalWithoutDisc = $disc < 100 ? ($amt / ((100 - $disc) / 100)) : $amt;
                $totalInv += $amt;
            ?>
                <tr>
                    <td class="txt-center"><?= $rowNumber++; ?></td>
                    <td><?= $detail->namaBarang ?? '-' ?></td>
                    <td><?= $detail->no_sales_order ?? '-' ?></td>
                    <td class="txt-center"><?= $qty ?></td>
                    <td class="txt-center"><?= $detail->kodeSatuan ?? '-' ?></td>
                    <td class="txt-center"><?= number_format($qty ? $totalWithoutDisc / $qty : 0) ?></td>
                    <td class="txt-center"><?= $disc ?></td>
                    <td class="txt-right"><?= number_format($amt) ?></td>
                </tr>
            <?php
            endforeach;
        }

        // tambahkan baris kosong agar selalu 6 baris
        $emptyRows = max(0, 6 - ($rowNumber - 1));
        for ($i = 0; $i < $emptyRows; $i++): ?>
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

    <table class="w-100 biaya-table">
        <colgroup>
            <col style="width:10%">
            <col style="width:55%">
            <col style="width:10%">
            <col style="width:25%">
        </colgroup>
        <tr>
            <td colspan="2"></td>
            <td class="txt-right">Biaya Lain:</td>
            <td class="txt-right" style="width:20%; border:0.5px solid; padding: 1px;">&nbsp;</td>
        </tr>
        <tr>
            <td style="width:1px;">Terbilang</td>
            <td style="border:0.5px solid;width: 400px;">
                <div><?= terbilang($totalInv) ?></div>
            </td>
            <td class="txt-right">Total Faktur:</td>
            <td class="txt-right" style="border:0.5px solid;"><?= number_format($totalInv) ?></td>
        </tr>
    </table>

    <table class="w-100">
        <tr>
            <td style="width:375px;">
                <div>Catatan: </div>
                <div>Surat Jalan ini tidak berfungsi sebagai Penagihan</div>
                <div>Barang yang sudah diterima tidak dapat dikembalikan</div>
                <div>Kecuali memenuhi ketentuan perjanjian BS Exp Date</div>
            </td>
            <td style="padding-left:50px">
                <div class="description-container">
                    <label class="description-label">Description: </label>
                    <?= $sjData->note ?? '' ?>
                </div>
            </td>
        </tr>
    </table>

    <table class="signature-table" width="100%">
        <tr style="vertical-align:top;">
            <td style="height:55px;border-bottom:1px solid;width:90px">Disiapkan</td>
            <td style="height:55px;border-bottom:1px solid;width:90px">Disetujui Oleh</td>
            <td style="height:55px;border-bottom:1px solid;width:90px">Diantar Oleh</td>
            <td style="height:55px;border-bottom:1px solid;width:90px">Diterima Oleh</td>
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