<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        @page {
            size: landscape;
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
            padding: 5px 15px;
        }

        .item-table tr th {
            border: 1px solid;
            padding: 5px 15px;
        }

        .item-table tr td:not(.skip) {
            border: 1px solid;
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
            margin-top: 2.5rem;
            width: 100%;
        }

        .sign-row>div {
            width: 250px;
            border-top: 1px solid;
            margin-top: 5rem
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
                        <td><?= $detail->bagian ?></td>
                        <td><?= $detail->spec ?></td>
                        <td class="txt-right"><?= $detail->qty ?></td>
                        <td class="txt-right"><?= $detail->general_price ?></td>
                        <td class="txt-right"><?= number_format(formatter($detail->general_price, "CURR_TO_INT") * formatter($detail->qty, "CURR_TO_INT")) ?></td>
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
            <table class="w-100 sign-table border-collapse signed-info">
                <tr>
                    <td style="height: 50px;"></td>
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

        <div class="pagebreak">
            <div class="w-100 d-flex content-between">
                <div style="border: 3px solid;border-style: double;width: 30%;padding: 1.2rem;">
                    <?= $dataPO->companyName ?><br>
                    <?= $dataPO->companyAddress ?>
                </div>
                <div style="padding: 1.2rem">
                    Kwitansi<br>
                    No. PO : <?= $dataPO->po_no ?>
                </div>
            </div>

            <table class="w-100 mt-2">
                <tr>
                    <td style="width: 30%;">SUDAH TERIMA DARI (RECEIVED FROM)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top;">PT. TOBA SURIMI INDUSTRIES</td>
                </tr>
                <tr>
                    <td>BANYAKNYA UANG (AMOUNT)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top;"><?= $dataPO->amount ?></td>
                </tr>
                <tr>
                    <td>UNTUK PEMBAYARAN (FOR PAYMENT)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top;">PEMBELIAN <?= $dataPO->itemName ?> SEBANYAK <?= $dataPO->totalQty ?> KG DARI <?= $dataPO->supplierName ?></td>
                </tr>
            </table>

            <table class="mt-2" style="width: 30%;border: 0;border-bottom: 3px solid;border-style: double;">
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
                <div class="txt-center" style="margin-left: auto;margin-right: 0;padding: 1rem;width: 25%;">
                    <div>Medan, 31-12-2022</div>
                    <div>Yang Menerima</div>
                    <div class="mt-4">(<?= $dataPO->supplierName ?>)</div>
                </div>
            </div>
        </div>

        <div class="pagebreak">
            <div class="w-100 d-flex content-between">
                <div style="border: 3px solid;border-style: double;width: 30%;padding: 1.2rem;">
                    <?= $dataPO->companyName ?><br>
                    <?= $dataPO->companyAddress ?>
                </div>
                <div style="padding: 1.2rem">
                    Kwitansi Harian<br>
                    No. PO : <?= $dataPO->po_no ?>
                </div>
            </div>

            <table class="w-100 mt-2">
                <tr>
                    <td style="width: 30%;">SUDAH TERIMA DARI (RECEIVED FROM)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top;">PT. TOBA SURIMI INDUSTRIES</td>
                </tr>
                <tr>
                    <td>BANYAKNYA UANG (AMOUNT)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top;"><?= $dataPO->amountDaily ?></td>
                </tr>
                <tr>
                    <td>UNTUK PEMBAYARAN (FOR PAYMENT)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top;">PEMBELIAN <?= $dataPO->itemName ?> SEBANYAK <?= $dataPO->totalQty ?> KG DARI <?= $dataPO->supplierName ?></td>
                </tr>
            </table>

            <table class="mt-2" style="width: 30%;border: 0;border-bottom: 3px solid;border-style: double;">
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
                <div class="txt-center" style="margin-left: auto;margin-right: 0;padding: 1rem;width: 25%;">
                    <div>Medan, 31-12-2022</div>
                    <div>Yang Menerima</div>
                    <div class="mt-4">(<?= $dataPO->supplierName ?>)</div>
                </div>
            </div>
        </div>

        <div class="pagebreak">
            <table class="w-100">
                <tr>
                    <td colspan="2"></td>
                    <td class="txt-bold txt-right txt-underline">Tanggal: <?= $dataPO->po_date ?></td>
                </tr>
                <tr>
                    <td class="txt-underline txt-bold">KWITANSI TAMBAHAN</td>
                    <td colspan="2">NO. NOTA : <?= $dataPO->po_no ?></td>
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
            <table class="w-100 sign-table border-collapse signed-info">
                <tr>
                    <td style="height: 50px;"></td>
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
    <?php } ?>
</body>

</html>