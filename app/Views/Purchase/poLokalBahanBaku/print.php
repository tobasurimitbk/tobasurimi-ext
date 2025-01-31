<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Po Lokal Bahan Baku</title>
    <style>
        body {
            height: 100%;
            font-size: 12px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }


        .pagebreak {
            clear: both;
            page-break-after: always;
        }

        .cong-table tr td {
            border: 0 !important;
            border-right: 1px solid !important;
        }

        .content-between {
            justify-content: space-between;
        }

        .d-flex {
            display: flex;
        }

        .item-table {
            width: 100%;
            border: 1px solid;
            border-collapse: collapse;
            border-spacing: 10px;
        }

        .item-table tr td {
            padding-left: 3px;
            padding-right: 3px;
        }

        .item-table tr th {
            border: 1px solid;
            padding-left: 3px;
            padding-right: 3px;
        }

        .item-table tr td:not(.skip) {
            border: 1px solid;
        }

        .mt-05 {
            margin-top: 0.5rem;
        }

        .mt-1 {
            margin-top: 1rem;
        }

        .mt-2 {
            margin-top: 2rem;
        }

        .mt-4 {
            margin-top: 4rem;
        }

        .signed-info {
            justify-content: space-around;
            text-align: center;
        }

        .txt-bold {
            font-weight: 700;
        }

        .txt-left {
            text-align: left;
        }

        .txt-center {
            text-align: center;
        }

        .txt-right {
            text-align: right;
        }

        .txt-underline {
            text-decoration: underline;
        }

        .w-100 {
            width: 100%;
        }

        .table-border {
            border: 1px solid black;
        }

        .table-bordered tr {
            border-bottom: 1px solid black;
        }

        .border-collapse {
            border-collapse: collapse;
        }

        .sign-table td:not(:last-child) {
            border: 1px solid;
        }

        .sign-row {
            display: flex;
            justify-content: space-around;
            margin-top: 1rem;
            width: 100%;
        }

        .sign-row>div {
            width: 200px;
            border-top: 1px solid;
            margin-top: 2rem
        }

        .sign-row-second {
            display: flex;
            justify-content: space-around;
            margin-top: 1rem;
            width: 100%;
        }

        .sign-row-second>div {
            width: 150px;
            border-top: 1px solid;
            margin-top: 2rem
        }



        @page {
            size: 8.27in 5.50in landscape;
            margin: 25px;
            padding: 25px;
        }

        .header {
            display: flex;
            justify-content: space-between;
        }

        .item-table {
            border-collapse: collapse;
            text-align: left;
            width: 100%;
        }

        .item-table tr th {
            border: 1px solid grey;
        }

        .item-table tr td {
            border: 1px solid grey;
        }

        .mt-025 {
            margin-top: 0.25rem;
        }

        .mt-050 {
            margin-top: 0.5rem;
        }

        .mt-1 {
            margin-top: 1rem;
        }

        .mt-2 {
            margin-top: 2rem;
        }

        .mt-3 {
            margin-top: 3rem;
        }

        .border-collapse {
            border-collapse: collapse;
        }

        .sign-table {
            border-collapse: collapse;
            width: 100%;
        }

        .sign-table td {
            border: 1px solid;
        }

        /* .sign-row {
            display: flex;
            justify-content: space-between;
            margin-top: 0rem;
            width: 100%;
        }

        .sign-row>div {
            width: 120px;
            border-top: 1px solid;
            margin-top: 2rem
        } */

        .txt-bold {
            font-weight: 700;
        }

        .txt-center {
            text-align: center;
        }

        .txt-left {
            text-align: left;
        }

        .txt-right {
            text-align: right;
        }

        .txt-top {
            vertical-align: top;
        }

        .w-30 {
            width: 30%;
        }

        .w-50 {
            width: 50%;
        }

        .w-100 {
            width: 100%;
        }

        .footer {
            /* bottom: 0; */
            position: absolute;
            height: 90px;
        }

        .title {
            font-weight: bold;
            font-size: 25px;
            text-decoration: underline;
        }

        .box {
            display: flex;
            justify-content: flex-end;
            background-color: blue;
        }

        .inner-box {
            display: flex;
            justify-content: center;
            background-color: red;
        }
    </style>
</head>

<body>
    <?php if (!empty($dataPO) && !empty($dataPODetail)) { ?>
        <div class="pagebreak">
            <div class="w-100 d-flex content-between">
                <div style="border: 3px solid;border-style: double;width: 60%;padding: 0.5rem;">
                    <?= strtoupper($dataPO->holding_company) ?><br>
                    <?= $dataPO->companyAddress ?>
                </div>
            </div><br>
            <table class="w-100">
                <tr>
                    <td class="txt-underline txt-bold">PO LOKAL BAHAN BAKU</td>
                    <td colspan="2"></td>
                    <td class="txt-bold txt-right txt-underline">Tanggal: <?= $dataPO->po_date ? date("d/m/Y", strtotime($dataPO->po_date)) : ""; ?></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td>No. Nota</td>
                    <td>: <?= $dataPO->po_no ?></td>
                </tr>
                <tr>
                    <td>Supplier</td>
                    <td>: <?= $dataPO->supplierName ?></td>
                </tr>
                <tr>
                    <td>Bahan Baku</td>
                    <td>: <?= $dataPO->itemName ?></td>
                </tr>
            </table>
            <table class="item-table">
                <tr>
                    <th>PETI / TONG</th>
                    <th>DEPARTEMEN</th>
                    <th>KETERANGAN</th>
                    <th class="txt-right">QTY (KG)</th>
                    <th class="txt-right">HARGA @</th>
                    <th class="txt-right">TOTAL</th>
                </tr>
                <?php
                $nilai_total = 0;
                $nilai_total_harian = 0;
                // echo '<pre>';
                // print_r($dataPODetail);
                // echo '</pre>';
                // exit;
                foreach ($dataPODetail as $detail) {
                ?>
                    <tr>
                        <td><?= $detail->peti ?></td>
                        <td><?= $dataPO->divisi ?></td>
                        <td><?= $detail->note ?></td>
                        <td class="txt-right"><?= $detail->qty ?></td>
                        <?php if ($dataPO->pph === "None") {
                            $nilai_total_harian = $nilai_total_harian + (($detail->daily_price) * formatter($detail->qty, "STR_TO_FLOAT"));
                            $nilai_total = $nilai_total + (($detail->general_price) * formatter($detail->qty, "STR_TO_FLOAT"));
                        ?>
                            <td class="txt-right"><?= number_format(($detail->general_price), 2, '.', ',') ?></td>
                            <td class="txt-right"><?= number_format(($detail->general_price) * formatter($detail->qty, "STR_TO_FLOAT"), 2, '.', ',') ?></td>
                        <?php }
                        if ($dataPO->pph === "Supplier") {
                            $nilai_total_harian = $nilai_total_harian + (($detail->daily_price) * formatter($detail->qty, "STR_TO_FLOAT"));
                            $nilai_total = $nilai_total + (($detail->general_price) * formatter($detail->qty, "STR_TO_FLOAT"));
                        ?>
                            <td class="txt-right"><?= number_format(($detail->general_price), 2, '.', ',') ?></td>
                            <td class="txt-right"><?= number_format(($detail->general_price) * formatter($detail->qty, "STR_TO_FLOAT"), 2, '.', ',') ?></td>
                        <?php }
                        if ($dataPO->pph === "Company") {
                            $nilai_total_harian = $nilai_total_harian + (($detail->daily_price / ($dataPO->nilai_pph) * formatter($detail->qty, "STR_TO_FLOAT")));
                            $nilai_total = $nilai_total + (($detail->general_price / ($dataPO->nilai_pph)) * formatter($detail->qty, "STR_TO_FLOAT"));
                        ?>
                            <td class="txt-right"><?= number_format(($detail->general_price / ($dataPO->nilai_pph)), 2, '.', ',') ?></td>
                            <td class="txt-right"><?= number_format(($detail->general_price / ($dataPO->nilai_pph)) * formatter($detail->qty, "STR_TO_FLOAT"), 2, '.', ',') ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                <tr class="table-border">
                    <td class="skip" colspan="3">JUMLAH</td>
                    <td class="skip txt-right"><?= $dataPO->totalQty ?></td>
                    <td class="skip"></td>
                    <td class="txt-right"><?= number_format(formatter($nilai_total, "STR_TO_FLOAT"), 2, '.', ',') ?></td>
                </tr>
                <tr class="table-border">
                    <td class="skip" colspan="5">PPH</td>
                    <?php if ($dataPO->pph === "Company" || $dataPO->pph === "Supplier") { ?>
                        <td class="txt-right"><?= number_format(($nilai_total * $dataPO->nilai_pph2), 2, '.', ',') ?></td>
                    <?php } else { ?>
                        <td class="txt-right">0.00</td>
                    <?php } ?>
                </tr>
                <!-- <tr class="table-border">
                    <td class="skip" colspan="5">TAMBAHAN LANGSUNG</td>
                    <td class="txt-right"><?= number_format(formatter($dataPO->subsidi_langsung, "STR_TO_FLOAT"), 2, '.', ',') ?></td>
                </tr> -->
                <tr class="table-border">
                    <td class="skip" colspan="5">DIBAYARKAN</td>
                    <?php if ($dataPO->pph === "Company" || $dataPO->pph === "Supplier") { ?>
                        <td class="txt-right"><?= number_format($nilai_total - ($nilai_total * $dataPO->nilai_pph2), 2, '.', ',') ?></td>
                    <?php } else { ?>
                        <td class="txt-right"><?= number_format(formatter($nilai_total, "STR_TO_FLOAT"), 2, '.', ',') ?></td>
                    <?php } ?>
                </tr>
            </table>
            <table class="w-100 sign-table border-collapse signed-info footer mt-3">

                <tr>
                    <th>
                        <div class="sign-row">
                            <div>TTD Penerima Bahan Baku</div>
                        </div>
                    </th>
                    <th>
                        <div class="sign-row">
                            <div>Diketahui</div>
                        </div>
                    </th>
                    <th>
                        <div class="sign-row">
                            <div>Yang Menerima</div>
                        </div>
                    </th>
                </tr>
            </table>
        </div>

        <div class="pagebreak" style="padding-top: 10px;">
            <div class="w-100 d-flex content-between">
                <div style="border: 3px solid;border-style: double;width: 60%;padding: 0.5rem;">
                    <?= strtoupper($dataPO->holding_company) ?><br>
                    <?= $dataPO->companyAddress ?>
                </div>
                <div style="padding: 0.5rem; text-align: center;">
                    <div style="text-decoration: underline; font-size: 1.2em;">
                        KWITANSI<br>
                    </div>
                    No. PO : <?= $dataPO->po_no ?><br>
                </div>
            </div>
            <table class="w-100 mt-05">
                <tr>
                    <td style="vertical-align: top; width: 40%;">SUDAH TERIMA DARI (RECEIVED FROM)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top; width: 55%;"><?= $dataPO->companyName ?></td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">BANYAKNYA UANG (AMOUNT)</td>
                    <td style="vertical-align: top;">: </td>
                    <?php if ($dataPO->pph === "Company" || $dataPO->pph === "Supplier") { ?>
                        <td style="vertical-align: top;"><?= strtoupper(terbilang(formatter(round($nilai_total - ($nilai_total * $dataPO->nilai_pph2)), "STR_TO_FLOAT"))) ?></td>
                    <?php } else { ?>
                        <td style="vertical-align: top;"><?= strtoupper(terbilang(formatter(($nilai_total), "STR_TO_FLOAT"))) ?></td>
                    <?php } ?>
                </tr>
                <tr>
                    <td style="vertical-align: top;">UNTUK PEMBAYARAN (FOR PAYMENT)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top;">PEMBELIAN <?= $dataPO->itemName ?> SEBANYAK <?= $dataPO->totalQty ?> KG DARI <?= $dataPO->supplierName ?></td>
                </tr>
            </table>

            <table class="mt-1" style="width: 30%;border: 0;border-bottom: 3px solid;border-style: double;">
                <tr>
                    <td>Bruto</td>
                    <td>Rp.</td>
                    <td class="txt-right"><?= number_format(formatter($nilai_total, "STR_TO_FLOAT"), 2, '.', ',') ?></td>
                </tr>
                <tr>
                    <td>PPh</td>
                    <td>Rp.</td>
                    <?php if ($dataPO->pph === "Company" || $dataPO->pph === "Supplier") { ?>
                        <td class="txt-right"><?= number_format(($nilai_total * $dataPO->nilai_pph2), 2, '.', ',') ?></td>
                    <?php } else { ?>
                        <td class="txt-right">0.00</td>
                    <?php } ?>
                </tr>
                <!-- <tr>
                    <td>Tambahan Langsung</td>
                    <td>Rp.</td>
                    <td class="txt-right"><?= number_format(formatter($dataPO->subsidi_langsung, "STR_TO_FLOAT"), 2, '.', ',') ?></td>
                </tr> -->
                <tr>
                    <td>Dibayarkan</td>
                    <td>Rp.</td>
                    <?php if ($dataPO->pph === "Company" || $dataPO->pph === "Supplier") { ?>
                        <td class="txt-right"><?= number_format(($nilai_total - ($nilai_total * $dataPO->nilai_pph2)), 2, '.', ',') ?></td>
                    <?php } else { ?>
                        <td class="txt-right"><?= number_format(formatter($nilai_total, "STR_TO_FLOAT"), 2, '.', ',') ?></td>
                    <?php } ?>
                </tr>
            </table>


            <table class="w-100">
                <tr>
                    <td style="width: 70%;">

                    </td>
                    <td style="width: 30%;">
                        <div class="txt-center">
                            <div>Medan, <?= $dataPO->po_date ? date("d-m-Y", strtotime($dataPO->po_date)) : ""; ?></div>
                            <div>Yang Menerima</div>
                            <div class="mt-2">(<?= $dataPO->supplierName ?>)</div>
                        </div>

                    </td>
                </tr>
            </table>
        </div>

        <div class="pagebreak" style="padding-top: 10px;">
            <div class="w-100 d-flex content-between">
                <div style="border: 3px solid;border-style: double;width: 60%;padding: 0.5rem;">
                    <?= strtoupper($dataPO->holding_company) ?><br>
                    <?= $dataPO->companyAddress ?>
                </div>
                <div style="padding: 0.5rem; text-align: center;">
                    <div style="text-decoration: underline; font-size: 1.2em;">KWITANSI HARIAN</div>
                    <div>No: <?= $dataPO->po_no ?></div>
                </div>
            </div>

            <table class="w-100">
                <tr>
                    <td style="vertical-align: top; width: 20%;">Sudah Terima Dari <br> (Received From)</td>
                    <td style="vertical-align: top; width: 2%;">: </td>
                    <td style="vertical-align: top; width: 55%; text-transform: uppercase;"> <?= strtoupper($dataPO->holding_company) ?>
                        (<?= $dataPO->companyName ?>)</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Banyaknya Uang <br> (Amount)</td>
                    <td style="vertical-align: top;">: </td>
                    <?php if ($dataPO->pph === "Company" || $dataPO->pph === "Supplier") { ?>
                        <td style="vertical-align: top;"><?= strtoupper(terbilang(formatter(round(($nilai_total_harian - ($nilai_total_harian * $dataPO->nilai_pph2))), "STR_TO_FLOAT"))) ?></td>
                    <?php } else { ?>
                        <td style="vertical-align: top;"><?= strtoupper(terbilang(formatter(($nilai_total_harian), "STR_TO_FLOAT"))) ?></td>
                    <?php } ?>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Untuk Pembayaran <br> (For Payment)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top;">Pembayaran <?= strtoupper($dataPO->itemName) ?> sebanyak <?= $dataPO->totalQty ?> KG dari <?= strtoupper($dataPO->supplierName) ?></td>
                </tr>
            </table>

            <table class="mt-05" style="width: 30%;border: 0;border-bottom: 3px solid;border-style: double;">
                <tr>
                    <td>Bruto</td>
                    <td>Rp.</td>
                    <td class="txt-right"><?= number_format(formatter($nilai_total_harian, "STR_TO_FLOAT"), 2, '.', ',') ?></td>
                </tr>
                <tr class="table-bordered">
                    <td>PPh</td>
                    <td>Rp.</td>
                    <?php if ($dataPO->pph === "Company" || $dataPO->pph === "Supplier") { ?>
                        <td class="txt-right"><?= number_format(($nilai_total_harian * $dataPO->nilai_pph2), 2, '.', ',') ?></td>
                    <?php } else { ?>
                        <td class="txt-right">0.00</td>
                    <?php } ?>
                </tr>
                <!-- <tr>
                    <td>Tambahan Langsung</td>
                    <td>Rp.</td>
                    <td class="txt-right"><?= number_format(formatter($dataPO->subsidi_langsung, "STR_TO_FLOAT"), 2, '.', ',') ?></td>
                </tr> -->
                <tr>
                    <td>Dibayarkan</td>
                    <td>Rp.</td>
                    <?php if ($dataPO->pph === "Company" || $dataPO->pph === "Supplier") { ?>
                        <td class="txt-right"><?= number_format(($nilai_total_harian - ($nilai_total_harian * $dataPO->nilai_pph2)), 2, '.', ',') ?></td>
                    <?php } else { ?>
                        <td class="txt-right"><?= number_format(formatter($nilai_total_harian, "STR_TO_FLOAT"), 2, '.', ',') ?></td>
                    <?php } ?>
                </tr>
            </table>


            <table class="w-100">
                <td></td>
                <td style="width: 30%;">
                    <div class="txt-center">
                        <div> Medan, <?= $dataPO->po_date ? date("d-m-Y", strtotime($dataPO->po_date)) : ""; ?></div>
                        <div>Yang Menerima</div>
                        <div class="mt-2">(<?= $dataPO->supplierName ?>)</div>
                    </div>
                </td>
            </table>

        </div>

        <div class=" <?= $dataPO->lpb == null ? '' : 'pagebreak' ?>">
            <div style="padding: 0.5rem; text-align: center;">
                <div style="text-decoration: underline; font-size: 1.2em;">KWITANSI TAMBAHAN</div>
                <div>NO. NOTA : <?= $dataPO->po_no ?></div>
            </div>
            <table class="w-100">
                <tr>

                    <td class="txt-bold txt-right txt-underline">Tanggal: <?= date('d/m/Y', strtotime($dataPO->po_date))  ?></td>
                </tr>
            </table>

            <table>
                <tr>
                    <td>Supplier</td>
                    <td>: <?= $dataPO->supplierName ?></td>
                </tr>
                <tr>
                    <td>Bahan Baku</td>
                    <td>: <?= $dataPO->itemName ?></td>
                </tr>
            </table>

            <?php if ($dataPO->pph === "Company") { ?>
                <table class="cong-table item-table txt-right">
                    <tr>
                        <th>QTY</th>
                        <th>CONG SEBENARNYA</th>
                        <th>CONG BATASAN</th>
                        <th>SELISIH</th>
                        <th>TOTAL TAMBAHAN</th>
                    </tr>
                    <tr>
                        <td><?= htmlspecialchars($dataPO->totalQty ?? 0) ?></td>
                        <td><?= number_format($dataPO->cong_sebenarnya ?? 0, 2, '.', ',') ?></td>
                        <td><?= number_format($dataPO->cong_batasan ?? 0, 2, '.', ',') ?></td>
                        <td><?= number_format($dataPO->selisih ?? 0, 2, '.', ',') ?></td>
                        <td><?= number_format($dataPO->totalTambahan ?? 0, 2, '.', ',') ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>PPH</td>
                        <td><?= number_format(($dataPO->totalTambahan ?? 0) * ($dataPO->nilai_pph2 ?? 0), 2, '.', ',') ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>DIBAYARKAN</td>
                        <td><?= number_format(
                                ($dataPO->totalTambahan ?? 0)  - (($dataPO->totalTambahan ?? 0) * ($dataPO->nilai_pph2 ?? 0)),
                                2,
                                '.',
                                ','
                            ) ?></td>
                    </tr>
                </table>
            <?php } elseif ($dataPO->pph === "Supplier") { ?>
                <table class="cong-table item-table txt-right">
                    <tr>
                        <th>QTY</th>
                        <th>CONG SEBENARNYA</th>
                        <th>CONG BATASAN</th>
                        <th>SELISIH</th>
                        <th>TOTAL TAMBAHAN</th>
                    </tr>
                    <tr>
                        <td><?= htmlspecialchars($dataPO->totalQty ?? 0) ?></td>
                        <td><?= number_format($dataPO->cong_sebenarnya ?? 0, 2, '.', ',') ?></td>
                        <td><?= number_format($dataPO->cong_batasan ?? 0, 2, '.', ',') ?></td>
                        <td><?= number_format($dataPO->selisih ?? 0, 2, '.', ',') ?></td>
                        <td><?= number_format($dataPO->totalTambahan ?? 0, 2, '.', ',') ?></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>PPH</td>
                        <td><?= number_format(($dataPO->totalTambahan ?? 0) * ($dataPO->nilai_pph2 ?? 0), 2, '.', ',') ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>DIBAYARKAN</td>
                        <td><?= number_format(
                                ($dataPO->totalTambahan ?? 0)  - (($dataPO->totalTambahan ?? 0) * ($dataPO->nilai_pph2 ?? 0)),
                                2,
                                '.',
                                ','
                            ) ?></td>
                    </tr>
                </table>
            <?php } else { ?>
                <table class="cong-table item-table txt-right">
                    <tr>
                        <th>QTY</th>
                        <th>CONG SEBENARNYA</th>
                        <th>CONG BATASAN</th>
                        <th>SELISIH</th>
                        <th>TOTAL TAMBAHAN</th>
                    </tr>
                    <tr>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>000</td>
                    </tr>

                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>PPH</td>
                        <td>000</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>DIBAYARKAN</td>
                        <td>000</td>
                    </tr>
                </table>
            <?php } ?>


            <table class="w-100 sign-table border-collapse signed-info footer mt-3">
                <tr>
                    <th>
                        <div class="sign-row-second">
                            <div>Dibuat Oleh</div>
                        </div>
                    </th>
                    <th>
                        <div class="sign-row-second">
                            <div>Diketahui</div>
                        </div>
                    </th>
                    <th>
                        <div class="sign-row-second">
                            <div>Disetujui</div>
                        </div>
                    </th>
                    <th>
                        <div class="sign-row-second">
                            <div>Yang Menerima</div>
                        </div>
                    </th>
                </tr>
            </table>
        </div>
    <?php } ?>
    <?php if ($dataPO->lpb != null) : ?>
        <div>
            <div class="txt-center"><span class="title">LAPORAN PENERIMAAN BARANG</span></div>
            <table class="w-100 mt-050">
                <tr>
                    <td>
                        <div><span class="txt-bold">No. LPB : <?= $dataPO->lpb->no_penerimaan_barang; ?></span></div>
                    </td>
                    <td>
                        <div><span class="txt-bold">Supplier : <?= $dataPO->lpb->supplier_name; ?></span></div>
                    </td>
                    <td class="txt-right">
                        <div><span class="txt-bold">Tipe: <?= $dataPO->lpb->tipe_bahan; ?></span></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div><span class="txt-bold">Tanggal : <?= $dataPO->lpb->createdAt ? date("d/m/Y", strtotime($dataPO->lpb->createdAt)) : ""; ?></span></div>
                    </td>
                    <td>
                        <div><span class="txt-bold">Jenis Kemasan : <?= $dataPO->lpb->kemasan; ?></span></div>
                    </td>
                    <td class="txt-right">
                        <div><span class="txt-bold">Jumlah Kemasan: <?= $dataPO->lpb->jumlah_kemasan; ?></span></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div><span class="txt-bold">Dokumen : <?= ($dataPO->lpb->bc_type == 0) ? "Non Pabean - 0" : $dataPO->lpb->bc_type ?></span></div>
                    </td>
                    <td>
                        <div><span class="txt-bold">Gudang: <?= $dataPO->lpb->warehouse_name; ?></span></div>
                    </td>
                </tr>
            </table>
            <table class="item-table mt-050">
                <tr>
                    <th class="txt-left" style="text-align:center; width: 30px;">No</th>
                    <th class="txt-left" style="text-align:center; width: 100px;">Nama Barang</th>
                    <th class="txt-left" style="text-align:center; width: 40px;">Qty</th>
                    <th class="txt-left" style="text-align:center; width: 30px;">Satuan</th>
                    <th class="txt-left" style="text-align:center; width: 60px;">@ Rp</th>
                    <th class="txt-left" style="text-align:center; width: 60px;">Jumlah</th>
                    <th class="txt-left" style=" text-align:center; width: 150px;">No PO</th>
                    <th class="txt-left" style="text-align:center; width: 60px;">Keterangan</th>
                </tr>

                <?php
                $no = 1;
                $jml_sub_total = 0;
                ?>
                <?php foreach ($dataPO->lpbDetail as $detail) : ?>
                    <?php
                    $jumlah = ($detail['harga'] + $detail['harga_harian'] + $detail['harga_bulanan']) * $detail['jml_masuk'];
                    $jml_sub_total += $jumlah;
                    ?>
                    <tr>
                        <td class="txt-center" style="text-align:center;"><?= $no++; ?></td>
                        <td class="txt-left" style="text-align:center;"><?= strtoupper($detail["nama_barang"] . ' (' . $detail['spesifikasi'] . ')'); ?></td>
                        <td class="txt-right" style="text-align:center;"><?= $detail["jml_masuk"]; ?></td>
                        <td class="txt-left" style="text-align:center;"><?= $detail["kode_satuan"]; ?></td>
                        <td class="txt-right" style="text-align:center;"><?= number_format(($detail['harga'] + $detail['harga_harian'] + $detail['harga_bulanan']), 2, '.', ',') ?></td>
                        <td class="txt-right" style="text-align:center;"><?= number_format($jumlah, 2, '.', ','); ?></td>
                        <td class="txt-left" style="text-align:center;"><?= $detail["po_no"]; ?></td>
                        <td class="txt-left" style="text-align:center;"><?= $detail["keterangan"]; ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td class="txt-left" style="padding-left: 5px" colspan="5"><b>TOTAL</b></td>
                    <td class="txt-right" style="text-align:center;"><?= number_format($jml_sub_total, 2, '.', ','); ?></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
            <div class="header mt-050">
                <table class="w-50 sign-table footer" style="padding-top: 0px; margin-top: 0px">
                    <tr>
                        <td>Diperiksa & Dibukukan</td>
                        <td class="txt-center" style="width:100px;">Tgl</td>
                        <td class="txt-center" style="width:100px;">Paraf</td>
                    </tr>
                    <tr>
                        <td style="height: 40px;">Pembelian</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="height: 40px;">Accounting</td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        <?php endif; ?>
</body>

</html>