<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $dataPO->po_no ?></title>
    <style>
        body {
            height: 100%;
            font-size: 12px;
            font-family: 'Times New Roman', Times, serif;
            text-transform: uppercase;
            /* font-weight: normal; */
        }

        @page {
            /* size: 8.27in 5.50in landscape; */
            size: 8.27in 6in landscape;
            margin: 25px;
            /* Margin diperkecil */
            padding: 25px;
            /* Padding diperkecil */
        }

        .pagebreak {
            clear: both;
            page-break-after: always;
        }

        .cong-table tr td {
            border: 0 !important;
            border-right: 1px solid !important;
            font-weight: normal;
        }

        .content-between {
            justify-content: space-between;
        }

        .d-flex {
            display: flex;
        }

        .item-table {
            width: 100%;
            /* border: 1px solid; */
            border-collapse: collapse;
            border-spacing: 10px;
            font-weight: normal;
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

        /* .txt-bold {
            font-weight: 700;
        } */
        .column-table-normal {
            font-weight: normal;
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
            font-weight: normal;
        }

        .sign-row>div,
        .sign-name>div {
            width: 250px;
            /* border-top: 1px solid; */
            margin-top: 2rem
        }

        .sign-row-second {
            text-align: center;
            /* font-weight: bold; */
            padding-bottom: 10px;
        }

        .sign-space {
            height: 25px;
            /* Jarak antar baris kosong */
        }

        .sign-name {
            text-align: center;
            /* font-style: bold; */
        }

        /* 
        @page {
            size: 8.27in 5.50in landscape;
            margin: 25px;
            padding: 25px;
        } */

        .header {
            display: flex;
            justify-content: space-between;
        }

        .item-table {
            border-collapse: collapse;
            text-align: left;
            width: 100%;
        }

        .item-table-lpb {
            border-collapse: collapse;
            text-align: left;
            width: 100%;
        }


        .item-table-lpb tr th {
            border: 1px solid grey;
        }

        /* .item-table tr td {
            border: 1px solid grey;
        } */

        .item-table-lpb tr td {
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

        /* .txt-bold {
            font-weight: 700;
        } */

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
            /* font-weight: bold; */
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

        tr.no-border td {
            border: none !important;
        }
    </style>
</head>

<body>
    <?php if (!empty($dataPO) && !empty($dataPODetail)) { ?>
        <div class="pagebreak">
            <div class="w-100 d-flex content-between">
                <div style="width: 70%;padding: 0.5rem;">
                    <div style="font-size: 15px; font-weight:bold;">
                        <u>
                            <?= strtoupper($dataPO->holding_company) ?> (<?= $dataPO->companyName ?>)
                        </u>
                    </div>

                </div>
            </div><br>
            <center style="margin-top: -10px;">
                <div style="font-size: 15px; font-weight:bold;">
                    <u>
                        PURCHASE ORDER
                    </u>
                </div>
                <div style="font-size: 13px;">
                    No : <?= $dataPO->po_no ?>
                </div>
            </center>
            <table class="w-100" style="margin-top: 5px;">
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td>Supplier</td>
                                <td>: <?= $dataPO->supplierName ?></td>
                            </tr>
                            <tr>
                                <td>Bahan Baku</td>
                                <td>: <?= $dataPO->barangName ?></td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table style="width: 100%;">
                            <tr style="text-align: right;">
                                <td style="text-align: right; width:110px;"></td>
                                <td style="text-align: right; width:110px;">Tanggal : <?= date('d-M-Y', strtotime($dataPO->po_date)); ?></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table class="item-table" style="margin-top: 5px;">
                <tr style="font-weight: 14px;">
                    <th class="column-table-normal">PETI / TONG</th>
                    <th class="column-table-normal">DEPARTEMEN</th>
                    <th class="column-table-normal">KETERANGAN</th>
                    <th class="txt-right column-table-normal">QTY (KG)</th>
                    <th class="txt-right column-table-normal">HARGA @</th>
                    <th class="txt-right column-table-normal">TOTAL</th>
                </tr>
                <?php
                $jumlahData = count($dataBarang);
                if ($jumlahData >= 7) {
                    $marginTop = '';
                } elseif ($jumlahData >= 5 && $jumlahData <= 6) {
                    $marginTop = '0rem';
                } elseif ($jumlahData >= 3) {
                    $marginTop = '2rem';
                } else {
                    $marginTop = '3rem';
                }
                ?>
                <?php foreach ($dataBarang as $detail): ?>
                    <tr>
                        <td style="border-left: 1px solid black; border-right: 1px solid black;font-size:13px;height:4%;"><?= $detail['peti'] ?></td>
                        <td style="border-left: 1px solid black; border-right: 1px solid black;font-size:13px;"><?= $detail['divisi'] ?></td>
                        <td style="border-left: 1px solid black; border-right: 1px solid black;font-size:13px;"><?= $detail['note'] ?></td>
                        <td class="txt-right" style="border-left: 1px solid black; border-right: 1px solid black;font-size:13px;"><?= $detail['qty'] ?></td>
                        <td class="txt-right" style="border-left: 1px solid black; border-right: 1px solid black;font-size:13px;"><?= number_format($detail['general_price'], 2) ?></td>
                        <td class="txt-right" style="border-left: 1px solid black; border-right: 1px solid black;font-size:13px;"><?= number_format($detail['general_price_total'], 2) ?></td>
                    </tr>
                <?php endforeach ?>
                <tr>
                    <td class="txt-left" style="border-top: 1px solid black; height:5%;font-size:11px;">Tgl Cetak : <?= date('d/m/Y H:i:s') ?></td>
                    <td colspan="2" style="border-top: 1px solid black;border-left: none!important;text-align: right; height:5%;font-size:14px;">Total Qty</td>
                    <td class="txt-right" style="border-top: 1px solid black; border-left: 1px solid black; border-bottom: 1px solid black; height:5%;font-size:14px;"><?= number_format($totalQty, 2) ?></td>
                    <td class="txt-right" style="border-top: 1px solid black; border-left: 1px solid black; height:5%;font-size:14px;">JUMLAH</td>
                    <td class="txt-right" style="border: 1px solid black;font-size:14px;"><?= number_format($dataPO->dpp_umum, 2) ?></td>
                </tr>
                <tr>
                    <td class="skip" colspan="5" style="border: none;text-align: right;height:5%;font-size:14px;;">PPH</td>
                    <td class="txt-right" style="border: 1px solid black;height:5%;font-size:14px;">
                        <?= number_format($dataPO->pph_umum, 2) ?>
                    </td>
                </tr>
                <tr>
                    <td class="skip" colspan="5" style="border: none!important;text-align: right;height:5%;font-size:14px;">DIBAYARKAN</td>
                    <td class="txt-right" style="border: 1px solid black;height:5%;font-size:14px;">
                        <?= number_format($dataPO->nilai_total_umum, 2) ?>
                    </td>
                </tr>

            </table>
            <table class="w-100 sign-table border-collapse signed-info footer" style="margin-top: <?= $marginTop ?>;">

                <tr>
                    <th>
                        <div class="sign-row">
                            <div>DIBUAT OLEH PEMB BHN BAKU</div>
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
                <tr style="border: none!important;">
                    <td class="sign-space" style="border: none!important;"></td>
                    <td class="sign-space" style="border: none!important;"></td>
                    <td class="sign-space" style="border: none!important;"></td>
                </tr>
                <tr>
                    <td class="sign-name" style="border: none!important;">
                        <div>( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
                    </td>
                    <td class="sign-name" style="border: none!important;">
                        <div>( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
                    </td>
                    <td class="sign-name" style="border: none!important;">
                        <div>( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
                    </td>
                </tr>
            </table>

        </div>

        <div class="pagebreak" style="padding-top: 10px;">
            <div class="w-100 d-flex content-between">
                <div style="width: 70%;padding: 0.5rem;">
                    <div style="font-size: 15px; font-weight:bold;">
                        <u>
                            <?= strtoupper($dataPO->holding_company) ?> (<?= $dataPO->companyName ?>)
                        </u>
                    </div>

                </div>
            </div><br>
            <center style="margin-top: -10px;">
                <div style="font-size: 15px; font-weight:bold;">
                    <u>
                        KWITANSI
                    </u>
                </div>
                <div style="font-size: 13px;">
                    No : <?= $dataPO->po_no ?>
                </div>
            </center>
            <table class="w-100 mt-05" style="margin-top: 20px;">
                <tr>
                    <td style="vertical-align: top; width: 20%;">SUDAH TERIMA DARI <br> (RECEIVED FROM)</td>
                    <td style="vertical-align: top; width:2%;">: </td>
                    <td style="vertical-align: top; width: 55%;"><?= $dataPO->holdingCompany ?> (<?= $dataPO->companyName ?>)</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">BANYAKNYA UANG <br> (AMOUNT)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top;">
                        <?= terbilang($dataPO->nilai_total_umum) ?> RUPIAH
                    </td>
                <tr>
                    <td style="vertical-align: top;">UNTUK PEMBAYARAN <br> (FOR PAYMENT)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top;">PEMBELIAN <?= $dataPO->barangName ?> SEBANYAK <?= $totalQty ?> KG DARI <?= $dataPO->supplierName ?></td>
                </tr>
            </table>

            <table class="mt-1" style="width: 30%;border: 0;border-bottom: 3px solid;border-style: double; margin-top:35px;">
                <tr style="font-size:15px;">
                    <td style="height:5%;">Bruto</td>
                    <td style="height:5%;">Rp.</td>
                    <td class="txt-right" style="height:2.5%;"><?= number_format($dataPO->dpp_umum, 2) ?></td>
                </tr>
                <tr style="font-size:15px;">
                    <td style="height:5%;">PPh</td>
                    <td style="height:5%;">Rp.</td>
                    <td class="txt-right" style="height:2.5%;"><?= number_format($dataPO->pph_umum, 2) ?></td>

                </tr>
                <tr style="font-size:15px;">
                    <td style="height:5%;">Dibayarkan</td>
                    <td style="height:5%;">Rp.</td>
                    <td class="txt-right" style="height:2.5%;"><?= number_format($dataPO->nilai_total_umum, 2) ?></td>
                </tr>
            </table>


            <table class="w-100" style="margin-top: 50px;">
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
                <div style="width: 70%;padding: 0.5rem;">
                    <div style="font-size: 15px; font-weight:bold;">
                        <u>
                            <?= strtoupper($dataPO->holding_company) ?> (<?= $dataPO->companyName ?>)
                        </u>
                    </div>
                </div>
            </div><br>
            <center style="margin-top: -10px;">
                <div style="font-size: 15px; font-weight:bold;">
                    <u>
                        KWITANSI HARIAN
                    </u>
                </div>
                <div style="font-size: 13px;">
                    No : <?= $dataPO->po_no ?>
                </div>
            </center>

            <table class="w-100" style="margin-top: 20px;">
                <tr>
                    <td style="vertical-align: top; width: 20%;">Sudah Terima Dari <br> (Received From)</td>
                    <td style="vertical-align: top; width: 2%;">: </td>
                    <td style="vertical-align: top; width: 55%; text-transform: uppercase;"> <?= strtoupper($dataPO->holding_company) ?>
                        (<?= $dataPO->companyName ?>)</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Banyaknya Uang <br> (Amount)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top;"><?= terbilang($dataPO->nilai_total_harian) ?> RUPIAH</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Untuk Pembayaran <br> (For Payment)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top;">Pembayaran <?= strtoupper($dataPO->barangName) ?> sebanyak <?= $totalQty ?> KG dari <?= strtoupper($dataPO->supplierName) ?></td>
                </tr>
            </table>

            <table class="mt-05" style="width: 30%;border: 0;border-bottom: 3px solid;border-style: double;margin-top:35px;">
                <tr style="font-size:15px;">
                    <td style="height:5%;">Bruto</td>
                    <td style="height:5%;">Rp.</td>
                    <td class="txt-right" style="height:2.5%;"><?= number_format($dataPO->dpp_harian, 2) ?></td>
                </tr>
                <tr class="table-bordered" style="font-size:15px;">
                    <td style="height:5%;">PPh</td>
                    <td style="height:5%;">Rp.</td>
                    <td class="txt-right" style="height:2.5%;"><?= number_format($dataPO->pph_harian, 2) ?></td>
                </tr>
                <tr style="font-size:15px;">
                    <td style="height:5%;">Dibayarkan</td>
                    <td style="height:5%;">Rp.</td>
                    <td class="txt-right" style="height:2.5%;"><?= number_format($dataPO->nilai_total_harian, 2) ?></td>
                </tr>
            </table>

            <table class="w-100" style="margin-top: 50px;">
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

        <div class=" <?= $lpb == null ? '' : 'pagebreak' ?>">
            <div style="width: 70%;padding: 0.5rem;">
                <div style="width: 70%;padding: 0.5rem;">
                    <div style="font-size: 15px; font-weight:bold;">
                        <u>
                            <?= strtoupper($dataPO->holding_company) ?> (<?= $dataPO->companyName ?>)
                        </u>
                    </div>
                </div>
            </div><br>
            <center style="margin-top: -10px;">
                <div style="font-size: 15px; font-weight:bold;">
                    <u>
                        KWITANSI TAMBAHAN
                    </u>
                </div>
                <div style="font-size: 13px;">
                    No : <?= $dataPO->po_no ?>
                </div>
            </center>
            <table class="w-100">
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td>Supplier</td>
                                <td>: <?= $dataPO->supplierName ?></td>
                            </tr>
                            <tr>
                                <td>Bahan Baku</td>
                                <td>: <?= $dataPO->barangName ?></td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table style="width: 100%;">
                            <tr>
                                <td style="text-align: right; width:110px;"></td>
                                <td style="text-align: right; width:110px;">Tanggal : <?= date('d-M-Y', strtotime($dataPO->po_date)); ?></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>


            <table class="cong-table item-table txt-right" style="border: 1px solid black;">
                <tr>
                    <th class="column-table-normal" style="height: 5%;">QTY</th>
                    <th class="column-table-normal">CONG SEBENARNYA</th>
                    <th class="column-table-normal">CONG BATASAN</th>
                    <th class="column-table-normal">SELISIH</th>
                    <th class="column-table-normal">TOTAL TAMBAHAN</th>
                </tr>
                <tr>
                    <td style="height: 5%;font-size:14px;"><?= number_format($totalQty ?? 0, 2) ?></td>
                    <td style="height: 5%;font-size:14px;"><?= number_format($dataPO->cong_sebenarnya ?? 0, 2) ?></td>
                    <td style="height: 5%;font-size:14px;"><?= number_format($dataPO->cong_batasan ?? 0, 2) ?></td>
                    <td style="height: 5%;font-size:14px;"><?= number_format(abs($dataPO->selisih) ?? 0, 2) ?></td>
                    <td style="border: 1px solid black !important;font-size:14px;"><?= number_format($dataPO->dpp_tambahan ?? 0, 2) ?></td>
                </tr>
                <tr>
                    <td style="height: 5%;"></td>
                    <td></td>
                    <td></td>
                    <td>PPH</td>
                    <td style="border: 1px solid black !important;font-size:14px;"><?= number_format($dataPO->pph_tambahan ?? 0, 2) ?></td>
                </tr>
                <tr>
                    <td style="height: 5%;"></td>
                    <td></td>
                    <td></td>
                    <td>DIBAYARKAN</td>
                    <td style="border: 1px solid black !important;font-size:14px;"><?= number_format($dataPO->nilai_total_tambahan ?? 0, 2) ?></td>
                </tr>
            </table>
            <table class="w-100 sign-table border-collapse signed-info footer mt-3" style="border: none!important;">
                <tr style="border: none!important;">
                    <th>
                        <div class="sign-row-second column-table-normal">
                            <div>DIBUAT OLEH PEMB BHN BAKU</div>
                        </div>
                    </th>
                    <th>
                        <div class="sign-row-second column-table-normal">Diketahui</div>
                    </th>
                    <th>
                        <div class="sign-row-second column-table-normal">Disetujui</div>
                    </th>
                    <th>
                        <div class="sign-row-second column-table-normal">Yang Menerima</div>
                    </th>
                </tr>
                <tr style="border: none!important;">
                    <td class="sign-space" style="border: none!important;"></td>
                    <td class="sign-space" style="border: none!important;"></td>
                    <td class="sign-space" style="border: none!important;"></td>
                    <td class="sign-space" style="border: none!important;"></td>
                </tr>
                <tr style="border: none!important;">
                    <td class="sign-space" style="border: none!important;"></td>
                    <td class="sign-space" style="border: none!important;"></td>
                    <td class="sign-space" style="border: none!important;"></td>
                    <td class="sign-space" style="border: none!important;"></td>
                </tr>
                <tr style="border: none!important;">
                    <td class="sign-space" style="border: none!important;"></td>
                    <td class="sign-space" style="border: none!important;"></td>
                    <td class="sign-space" style="border: none!important;"></td>
                    <td class="sign-space" style="border: none!important;"></td>
                </tr>
                <tr>
                    <td class="sign-name" style="border: none!important;">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; )</td>
                    <td class="sign-name" style="border: none!important;">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; )</td>
                    <td class="sign-name" style="border: none!important;">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; )</td>
                    <td class="sign-name" style="border: none!important;">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; )</td>
                </tr>
            </table>
        </div>
    <?php } ?>
    <?php if ($lpb != null) : ?>
        <div>
            <div class="txt-center"><span class="title">LAPORAN PENERIMAAN BARANG</span></div>
            <table class="w-100 mt-050">
                <tr>
                    <td>
                        <div><span class="txt-bold">No. LPB : <?= $lpb->no_penerimaan_barang; ?></span></div>
                    </td>
                    <td>
                        <div><span class="txt-bold">Supplier : <?= $lpb->supplier_name; ?></span></div>
                    </td>
                    <td class="txt-right">
                        <div><span class="txt-bold">Tipe: BAHAN <?= $lpb->tipe_bahan; ?></span></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div><span class="txt-bold">Tanggal : <?= $lpb->tanggal ? date("d/m/Y", strtotime($lpb->tanggal)) : ""; ?></span></div>
                    </td>
                    <td>
                        <div><span class="txt-bold">Jenis Kemasan : <?= $lpb->kemasan; ?></span></div>
                    </td>
                    <td class="txt-right">
                        <div><span class="txt-bold">Jumlah Kemasan: <?= $lpb->jumlah_kemasan; ?></span></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div><span class="txt-bold">Dokumen : <?= ($lpb->bc_type == 0) ? "Non Pabean" : $lpb->bc_type ?></span></div>
                    </td>
                    <td>
                        <div><span class="txt-bold">Gudang: <?= $lpb->warehouse_name; ?></span></div>
                    </td>
                    <td class="txt-right">
                        <div><span class="txt-bold">Bahan Baku: <?= $lpb->barang_name; ?></span></div>
                    </td>
                </tr>
            </table>
            <table class="item-table-lpb mt-050">
                <tr>
                    <th class="txt-left column-table-normal" style="text-align:center; width: 30px;">No</th>
                    <th class="txt-left column-table-normal" style=" text-align:center; width: 150px;">No PO</th>
                    <th class="txt-left column-table-normal" style="text-align:center; width: 40px;">Qty</th>
                    <th class="txt-left column-table-normal" style="text-align:center; width: 30px;">Satuan</th>
                    <th class="txt-left column-table-normal" style="text-align:center; width: 60px;">Jumlah</th>
                    <th class="txt-left column-table-normal" style="text-align:center; width: 60px;">Keterangan</th>
                </tr>

                <?php
                $no = 1;
                $jml_sub_total = 0;
                ?>
                <?php foreach ($lpbDetail as $detail) : ?>
                    <?php
                    $jumlah = ($detail['harga'] + $detail['harga_harian'] + $detail['harga_bulanan']) * $detail['jml_masuk'];
                    $jml_sub_total += $jumlah;
                    ?>
                    <tr>
                        <td class="txt-center" style="text-align:center;"><?= $no++; ?></td>
                        <td class="txt-left" style="text-align:center;"><?= $detail["po_no"]; ?></td>
                        <td class="txt-right" style="text-align:center;"><?= number_format($detail["jml_masuk"], 2); ?></td>
                        <td class="txt-left" style="text-align:center;"><?= $detail["kode_satuan"]; ?></td>
                        <td class="txt-right" style="text-align:center;"><?= number_format($lpb->total_before_pph, 2); ?></td>
                        <td class="txt-left" style="text-align:center;"><?= $detail["keterangan_lpb"]; ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td class="txt-left column-table-normal" style="padding-left: 5px" colspan="4">TOTAL</td>
                    <td class="txt-right" style="text-align:center;"><?= number_format($lpb->total_before_pph, 2); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="txt-left column-table-normal" style="padding-left: 5px" colspan="4">PPh</td>
                    <td class="txt-right" style="text-align:center;"><?= number_format($lpb->total_before_pph - $lpb->total_after_pph, 2); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="txt-left column-table-normal" style="padding-left: 5px" colspan="4">TOTAL DIBAYARKAN</td>
                    <td class="txt-right" style="text-align:center;"><?= number_format($lpb->total_after_pph, 2); ?></td>
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