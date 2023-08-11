<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
    <?php if (!empty($dataPO) && !empty($dataPODetail)) { ?>
        <div class="header">
            <div class="w-50">
                <div class="txt-bold">PURCHASE ORDER RAW MATERIAL IMPORT</div>
                <div class="mt-025 txt-bold">PO Number: <?= $dataPO->po_no; ?></div>
                <div class="w-100">
                    <table class="mt-1 w-100">
                        <tr>
                            <td>Date: <?= $dataPO->po_date ? date("d/m/Y", strtotime($dataPO->po_date)) : ""; ?></td>
                            <td>Department: <?= $dataPO->warehouseName; ?></td>
                        </tr>
                        <tr>
                            <td>Request Number: <?= $dataPO->spp_no; ?></td>
                            <td>Location: <?= $dataPO->companyName; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="txt-right supplier">
                <div>Supplier: <span class="txt-bold"><?= $dataPO->supplierName; ?></span></div>
                <div>Address: <span class="txt-bold"><?= $dataPO->supplierAddress; ?></span></div>
                <div>Phone: <span class="txt-bold"><?= $dataPO->supplierPhone; ?></span></div>
                <div>NPWP: <span class="txt-bold"><?= $dataPO->supplierNPWP; ?></span></div>
            </div>
        </div>

        <div class="mt-1 txt-bold">Please send us the following items below:</div>
        <table class="item-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>Specification</th>
                    <th>Unit</th>
                    <th>Item Price</th>
                    <th>Qty</th>
                    <th>Disc%</th>
                    <th>Additional Cost</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $total_disc = 0;
                $persentase_disc = 0;
                $total = 0;

                foreach ($dataPODetail as $detail) {
                    $value_disc = (formatter($detail["qty"], "STR_TO_INT") * formatter($detail["price"], "STR_TO_INT")) - (formatter($detail["disc"], "STR_TO_INT") * (formatter($detail["qty"], "STR_TO_INT") * formatter($detail["price"], "STR_TO_INT")) / 100);
                    $total_disc = $total_disc + $value_disc;
                    $persentase_disc = $persentase_disc + formatter($detail["disc"], "STR_TO_INT");
                    $total = $total + formatter($value_disc, "STR_TO_INT") + formatter($detail["additional_cost"], "STR_TO_INT");
                ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $detail["kode_barang"]; ?></td>
                        <td><?= $detail["nama_barang"]; ?></td>
                        <td><?= $detail["spec"]; ?></td>
                        <td><?= $detail["nama_satuan"]; ?></td>
                        <td><?= number_format(formatter($detail["price"], "STR_TO_INT")); ?></td>
                        <td><?= formatter($detail["qty"], "STR_TO_INT"); ?></td>
                        <td><?= formatter($detail["disc"], "STR_TO_INT"); ?></td>
                        <td><?= number_format(formatter($detail["additional_cost"], "STR_TO_INT")); ?></td>
                        <td><?= number_format(formatter($value_disc, "STR_TO_INT") + formatter($detail["additional_cost"], "STR_TO_INT")); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="header mt-1">
            <div>Description: <div><?= $dataPO->note; ?></div>
            </div>
            <div class="txt-right">
                <div class="mt-025">
                    Currency: <span class="txt-bold"><?= $dataPO->currencyName; ?></span>
                </div>
                <div>
                    Purchase Total: <span class="txt-bold"><?= number_format(formatter($dataPO->total, "STR_TO_INT")); ?></span>
                </div>
                <div class="mt-025">
                    Discount: <span class="txt-bold"><?= number_format(formatter($total_disc, "STR_TO_INT")); ?> (<?= $persentase_disc / ($no - 1); ?>%)</span>
                </div>
                <div class="mt-025">
                    Total Price: <span class="txt-bold"><?= number_format(formatter($total, "STR_TO_INT")); ?></span>
                </div>
            </div>
        </div>

        <table class="w-100 sign-table border-collapse footer">
            <tr>
                <td style="height: 30px;"></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <th>
                    <div class="sign-row">
                        <div>Warehouse</div>
                    </div>
                </th>
                <th>
                    <div class="sign-row">
                        <div>Purchase</div>
                    </div>
                </th>
                <th>
                    <div class="sign-row">
                        <div>Head of Purchase</div>
                    </div>
                </th>
                <th>
                    <div class="sign-row">
                        <div>Audit</div>
                    </div>
                </th>
                <th>
                    <div class="sign-row">
                        <div>Director</div>
                    </div>
                </th>
            </tr>
        </table>
    <?php } ?>
</body>

</html>