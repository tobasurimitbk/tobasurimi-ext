<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Po Lokal Bahan Baku</title>
    <style>
        body {
            font-size: 13px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        @page {
            size: 8.27in 5.50in landscape;
            margin: 29px;
            padding: 29px;
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

        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 90px;
        }
    </style>
</head>

<body>
    <?php if (!empty($dataPO) && !empty($dataPODetail)) { ?>
        <div class="pagebreak">
            <table class="w-100">
                <tr>
                    <td class="txt-underline txt-bold">PO LOKAL BAHAN BAKU</td>
                    <td colspan="2">NO. NOTA : <?= $dataPO->po_no ?></td>
                    <td class="txt-bold txt-right txt-underline">Tanggal: <?= $dataPO->po_date ? date("d/m/Y", strtotime($dataPO->po_date)) : ""; ?></td>
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
            <table class="item-table">
                <tr>
                    <th>PETI / TONG</th>
                    <th>BAGIAN</th>
                    <th>KETERANGAN</th>
                    <th class="txt-right">QTY (KG)</th>
                    <th class="txt-right">HARGA @</th>
                    <th class="txt-right">TOTAL</th>
                </tr>
                <?php
                foreach ($dataPODetail as $detail) {
                ?>
                    <tr>
                        <td><?= $detail->peti ?></td>
                        <td><?= $detail->nama_bagian ?></td>
                        <td><?= $detail->spesifikasi ?></td>
                        <td class="txt-right"><?= $detail->qty ?></td>
                        <td class="txt-right"><?= number_format(formatter($detail->general_price, "STR_TO_FLOAT"), 2, '.', ',') ?></td>
                        <td class="txt-right"><?= number_format(($detail->general_price ? formatter(str_replace(",", "", $detail->general_price), "STR_TO_FLOAT") : 0) * formatter($detail->qty, "STR_TO_FLOAT"), 2, '.', ',') ?></td>
                    </tr>
                <?php } ?>
                <tr class="table-border">
                    <td class="skip" colspan="3">JUMLAH</td>
                    <td class="skip txt-right"><?= $dataPO->totalQty ?></td>\
                    <td class="skip"></td>
                    <td class="txt-right"><?= $dataPO->totalPrice ?></td>
                </tr>
                <tr class="table-border">
                    <td class="skip" colspan="5">PPH</td>
                    <td class="txt-right"><?= $dataPO->totalPph ?></td>
                </tr>
                <tr class="table-border">
                    <td class="skip" colspan="5">DIBAYARKAN</td>
                    <td class="txt-right"><?= $dataPO->totalPaid ?></td>
                </tr>
            </table>
            <table class="w-100 sign-table border-collapse signed-info footer">
                <tr>
                    <td style="height: 30px;"></td>
                    <td></td>
                    <td></td>
                </tr>
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
                    <?= $dataPO->companyName ?><br>
                    <?= $dataPO->companyAddress ?>
                </div>
                <div style="padding: 0.5rem">
                    Kwitansi<br>
                    No. PO : <?= $dataPO->po_no ?><br>
                    Tanggal: <?= $dataPO->po_date ? date("d-m-Y", strtotime($dataPO->po_date)) : ""; ?>
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
                    <td style="vertical-align: top;"><?= $dataPO->amount ?></td>
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
                    <td class="txt-right"><?= $dataPO->totalPrice ?></td>
                </tr>
                <tr>
                    <td>PPh</td>
                    <td>Rp.</td>
                    <td class="txt-right"><?= $dataPO->totalPph ?></td>
                </tr>
                <tr>
                    <td>Dibayarkan</td>
                    <td>Rp.</td>
                    <td class="txt-right"><?= $dataPO->totalPaid ?></td>
                </tr>
            </table>

            <div class="w-100">
                <div class="footer txt-right">
                    <div>Yang Menerima</div>
                    <div class="mt-2">(<?= $dataPO->supplierName ?>)</div>
                </div>
            </div>
        </div>

        <div class="pagebreak" style="padding-top: 10px;">
            <div class="w-100 d-flex content-between">
                <div style="border: 3px solid;border-style: double;width: 60%;padding: 0.5rem;">
                    <?= $dataPO->companyName ?><br>
                    <?= $dataPO->companyAddress ?>
                </div>
                <div style="padding: 0.5rem">
                    Kwitansi Harian<br>
                    No. PO : <?= $dataPO->po_no ?><br>
                    Tanggal: <?= $dataPO->po_date ? date("d-m-Y", strtotime($dataPO->po_date)) : ""; ?>
                </div>
            </div>

            <table class="w-100 mt-2">
                <tr>
                    <td style="vertical-align: top; width: 40%;">SUDAH TERIMA DARI (RECEIVED FROM)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top; width: 55%;"><?= $dataPO->companyName ?></td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">BANYAKNYA UANG (AMOUNT)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top;"><?= $dataPO->amountDaily ?></td>
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
                    <td class="txt-right"><?= $dataPO->totalDailyPrice ?></td>
                </tr>
                <tr>
                    <td>PPh</td>
                    <td>Rp.</td>
                    <td class="txt-right"><?= $dataPO->totalDailyPph ?></td>
                </tr>
                <tr>
                    <td>Dibayarkan</td>
                    <td>Rp.</td>
                    <td class="txt-right"><?= $dataPO->totalDailyPaid ?></td>
                </tr>
            </table>

            <div class="w-100">
                <div class="footer txt-right">
                    <div>Yang Menerima</div>
                    <div class="mt-2">(<?= $dataPO->supplierName ?>)</div>
                </div>
            </div>
        </div>

        <div>
            <table class="w-100">
                <tr>
                    <td class="txt-underline txt-bold">KWITANSI TAMBAHAN</td>
                    <td>NO. NOTA : <?= $dataPO->po_no ?></td>
                    <td class="txt-bold txt-right txt-underline">Tanggal: <?= $dataPO->po_date ?></td>
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

            <table class="cong-table item-table txt-right">
                <tr>
                    <th>QTY</th>
                    <th>CONG SEBENARNYA</th>
                    <th>CONG BATASAN</th>
                    <th>SELISIH</th>
                    <th>TOTAL TAMBAHAN</th>
                </tr>
                <tr>
                    <td><?= $dataPO->totalQty ?></td>
                    <td><?= $dataPO->cong_sebenarnya ?></td>
                    <td><?= $dataPO->cong_batasan ?></td>
                    <td><?= $dataPO->selisih ?></td>
                    <td><?= $dataPO->totalTambahan ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>PPH</td>
                    <td><?= $dataPO->pphTambahan ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>DIBAYARKAN</td>
                    <td><?= $dataPO->subsidi_langsung ?></td>
                </tr>
            </table>
            <table class="w-100 sign-table border-collapse signed-info footer">
                <tr>
                    <td style="height: 30px;"></td>
                    <td></td>
                    <td></td>
                </tr>
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
</body>

</html>