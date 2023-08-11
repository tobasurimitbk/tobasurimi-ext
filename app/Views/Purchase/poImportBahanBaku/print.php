<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PO Raw Material Import</title>
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
            background-color: #F3EED9;
        }

        .item-table tr th {
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

        .border-collapse {
            border-collapse: collapse;
        }

        .sign-table td:not(:last-child) {
            border: 1px solid;
        }

        .sign-row {
            display: flex;
            justify-content: space-between;
            margin-top: 0rem;
            width: 100%;
        }

        .sign-row>div {
            width: 120px;
            border-top: 1px solid;
            margin-top: 2rem
        }

        .txt-bold {
            font-weight: 700;
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

        .txt-underline {
            text-decoration: underline;
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
    <?php if (!empty($dataPO)) { ?>
        <table class="w-100">
            <tr>
                <td colspan="2">
                    <div class="txt-underline txt-bold">PO RAW MATERIAL IMPORT</div>
                </td>
                <td class="txt-right">
                    <div>To: <span class="txt-bold"><?= $dataPO->supplierName ?></span></div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="mt-025 txt-bold">PO Number: <?= $dataPO->po_no ?></div>
                </td>
                <td class="txt-right">
                    <div>Address: <span class="txt-bold"><?= $dataPO->supplierAddress ?></span></div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    &nbsp;
                </td>
                <td class="txt-right">
                    <div>Phone: <span class="txt-bold"><?= $dataPO->supplierPhone ?></span></div>
                </td>
            </tr>
            <tr>
                <td>
                    Date: <?= date("d-m-Y", strtotime($dataPO->po_date)) ?>
                </td>
                <td>
                    SPP Number: <?= $dataPO->spp_no ?>
                </td>
                <td class="txt-right">
                    <div>NPMWP: <span class="txt-bold"><?= $dataPO->supplierNPWP ?></span></div>
                </td>
            </tr>
            <tr>
                <td>
                    Department: <?= $dataPO->warehouseName ?>
                </td>
                <td colspan="2">
                    Location: <?= $dataPO->companyName ?>
                </td>
            </tr>
        </table>
        <div class="mt-025 txt-bold" style="margin-bottom: 3px;">Please send us the following items below:</div>
        <table class="item-table mt-050">
            <tr>
                <th class="txt-left" style="padding-left: 5px; width: 70px;">QTY</th>
                <th class="txt-left" style="padding-left: 5px; width: 120px;">ITEM CODE</th>
                <th class="txt-left" style="padding-left: 5px;">ITEM NAME</th>
                <th class="txt-left" style="padding-left: 5px; width: 80px;">PRICE</th>
                <th class="txt-left" style="padding-left: 5px; width: 60px;">DISC(%)</th>
                <th class="txt-left" style="padding-left: 5px; width: 80px;">TOTAL</th>
            </tr>
            <?php
            $no = 1;
            $totalPrice = 0;
            $totalDisc = 0;

            foreach ($dataPODetail as $detail) {
                $totalan = formatter($detail["price"], "CURR_TO_INT") * formatter($detail["qty"], "CURR_TO_INT") +  formatter($detail["additional_cost"], "CURR_TO_INT");
                $totalPrice += $totalan;
                $totalDisc += $totalan * (float)$detail["disc"] / 100;
            ?>
                <tr>
                    <td style="padding-left: 5px;"><?= $detail["qty"] . " " . $detail["nama_satuan"] ?></b></td>
                    <td style="padding-left: 5px;"><?= $detail["kode_barang"] ?></b></td>
                    <td style="padding-left: 5px;"><?= $detail["nama_barang"] . " " . $detail["spec"] ?></b></td>
                    <td class="txt-right" style="padding-right: 5px;"><?= number_format(formatter($detail["price"], "STR_TO_INT")) ?></b></td>
                    <td class="txt-right" style="padding-right: 5px;"><?= $detail["disc"] ?></b></td>
                    <td class="txt-right" style="padding-right: 5px;"><?= $detail["totalPrice"] ?></b></td>
                </tr>
            <?php } ?>
        </table>
        <div class="header mt-025">
            <div class="txt-right">
                <div>
                    Sub Total: <span class="txt-bold">Rp. <?= number_format($totalPrice) ?></span>
                </div>
                <div class="mt-025">
                    Diskon: <span class="txt-bold">Rp. <?= $totalDisc ?></span>
                </div>
                <div class="mt-025">
                    Grand Total: <span class="txt-bold">Rp. <?= number_format($totalPrice - $totalDisc) ?></span>
                </div>
            </div>
        </div>
        <div style="text-decoration: underline;">
            Note: <?= $dataPO->note ?>
        </div>
        <table class="w-50 sign-table border-collapse footer" style="padding-top: 0px; margin-top: 0px">
            <tr style="border: 0px;">
                <td style="height: 30px; border: 0px;">Order By</td>
                <td style="border: 0px;">Created By</td>
                <td style="border: 0px;">Known By</td>
                <td style="border: 0px;">Checked By</td>
                <td style="border: 0px;">Approved By</td>
            </tr>
            <tr>
                <th>
                    <div class="sign-row txt-left">
                        <div>(Warehouse)</div>
                    </div>
                </th>
                <th>
                    <div class="sign-row txt-left">
                        <div>(Purchase)</div>
                    </div>
                </th>
                <th>
                    <div class="sign-row txt-left">
                        <div>(Head of Purchase)</div>
                    </div>
                </th>
                <th>
                    <div class="sign-row txt-left">
                        <div>(Audit)</div>
                    </div>
                </th>
                <th>
                    <div class="sign-row txt-left">
                        <div>(Director)</div>
                    </div>
                </th>
            </tr>
        </table>
    <?php } ?>
</body>

</html>