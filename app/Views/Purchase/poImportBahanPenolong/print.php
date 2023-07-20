<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            font-size: 10px !important;
        }

        .header {
            display: flex !important;
            justify-content: space-between !important;
        }

        .sign-table {
            border-collapse: collapse !important;
            text-align: left !important;
            width: 100% !important;
            margin-top: 2.5rem;
        }

        .item-table {
            border-collapse: collapse !important;
            text-align: center !important;
            width: 100% !important;
        }

        .item-table tr th {
            background-color: #F3EED9;
        }

        .item-table tr th,.item-table tr td {
            border: 1px solid grey;
        }

        .mt-025 {
            margin-top: 0.25rem;
        }

        .mt-1 {
            margin-top: 1rem;
        }

        .mt-2 {
            margin-top: 2rem;
        }

        .sign-row {
            display: flex;
            justify-content: space-around;
            margin-top: 2.5rem;
            width: 100%;
        }

        .sign-row > div {
            width: 130px;
            border-top: 1px solid;
            margin-top: 5rem
        }

        .supplier {
            margin-top: -35px;
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

        .w-30 {
            width: 30%;
        }

        .w-50 {
            width: 50%;
        }

        .w-100 {
            width: 100%;
        }
    </style>
</head>
<body>
<?php if(!empty($dataPO) && !empty($dataPODetail)){ ?>
    <div class="header">
        <div class="w-50">
            <div class="txt-bold">PURCHASE ORDER IMPORT</div>
            <div class="mt-025 txt-bold">No. PO: <?= $dataPO->po_no; ?></div>
            <div class="w-100">
                <table class="mt-1 w-100">
                    <tr>
                        <td>Tanggal: <?= $dataPO->po_date ? date("d/m/Y", strtotime($dataPO->po_date)) : ""; ?></td>
                        <td>Departmen: <?= $dataPO->warehouseName; ?></td>
                    </tr>
                    <tr>
                        <td>No. SPP: <?= $dataPO->spp_no; ?></td>
                        <td>Lokasi: <?= $dataPO->companyName; ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="txt-right supplier">
            <div>Kepada YTH: <span class="txt-bold"><?= $dataPO->supplierName; ?></span></div>
            <div>Alamat: <span class="txt-bold"><?= $dataPO->supplierAddress; ?></span></div>
            <div>Telepon: <span class="txt-bold"><?= $dataPO->supplierPhone; ?></span></div>
            <div>NPMWP: <span class="txt-bold"><?= $dataPO->supplierNPWP; ?></span></div>
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

            foreach($dataPODetail as $detail){ 
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
        <div>Description: <div><?= $dataPO->note; ?></div></div>
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
                DPP: <span class="txt-bold"><?= number_format(formatter($dataPO->dpp, "STR_TO_INT")); ?></span>
            </div>
            <div class="mt-025">
                Total Price: <span class="txt-bold"><?= number_format(formatter($total, "STR_TO_INT") - formatter($dataPO->dpp, "STR_TO_INT")); ?></span>
            </div>
        </div>
    </div>

    <table class="sign-table">
        <thead>
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
        </thead>
    </table>
<?php } ?>
</body>
</html>