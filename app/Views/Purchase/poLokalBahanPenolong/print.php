<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PO Lokal Bahan Penolong</title>
    <style>
        body {
            font-size: 10px;
            margin: 0px;
            padding: 0px;
        }

        @page {
            size: 8.27in 5.50in landscape;
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
            margin-top: 2.5rem;
            width: 100%;
        }

        .sign-row>div {
            width: 100px;
            border-top: 1px solid;
            margin-top: 5rem
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
    </style>
</head>

<body>
    <?php if (!empty($dataPOLokal)) { ?>
        <table class="w-100">
            <tr>
                <td colspan="2">
                    <div class="txt-bold">PO LOKAL BAHAN PENOLONG</div>
                </td>
                <td class="txt-right">
                    <div>Kepada: <span class="txt-bold"><?= $dataPOLokal->supplierName ?></span></div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="mt-025 txt-bold">No. PO: <?= $dataPOLokal->po_no ?></div>
                </td>
                <td class="txt-right">
                    <div>Alamat: <span class="txt-bold"><?= $dataPOLokal->supplierAddress ?></span></div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    &nbsp;
                </td>
                <td class="txt-right">
                    <div>Telepon: <span class="txt-bold"><?= $dataPOLokal->supplierPhone ?></span></div>
                </td>
            </tr>
            <tr>
                <td>
                    Tanggal: <?= $dataPOLokal->po_date ?>
                </td>
                <td>
                    No. SPP: <?= $dataPOLokal->spp_no ?>
                </td>
                <td class="txt-right">
                    <div>NPMWP: <span class="txt-bold"><?= $dataPOLokal->supplierNPWP ?></span></div>
                </td>
            </tr>
            <tr>
                <td>
                    Departemen: <?= $dataPOLokal->warehouseName ?>
                </td>
                <td colspan="2">
                    Lokasi: <?= $dataPOLokal->companyName ?>
                </td>
            </tr>
        </table>
        <div class="mt-1 txt-bold" style="margin-bottom: 5px;">Harap dikirimkan kepada kami barang-barang berikut dibawah ini:</div>
        <table class="item-table">
            <tr>
                <!-- <th>No</th> -->
                <th class="txt-left">QTY</th>
                <th class="txt-left">KODE BARANG</th>
                <th class="txt-left">NAMA BARANG</th>
                <!-- <th>Satuan</th> -->
                <!-- <th>Spesifikasi</th> -->
                <th class="txt-left">HARGA</th>
                <th class="txt-left">DISC(%)</th>
                <!-- <th>Biaya Tambahan</th> -->
                <th class="txt-left">JUMLAH</th>
                <!-- <th>PPN</th> -->
                <!-- <th>PPH</th> -->
            </tr>
            <?php
            foreach ($dataPOLokal->am_purchase_order_details as $detail) {
            ?>
                <tr>
                    <!-- <td><b><?= $detail->no ?></b></td> -->
                    <td><?= $detail->qty . " " . $detail->nama_satuan ?></b></td>
                    <td><?= $detail->kode_barang ?></b></td>
                    <td class="w-50"><?= $detail->nama_barang . " " . $detail->spec ?></b></td>
                    <!-- <td><?= $detail->nama_satuan ?></b></td> -->
                    <!-- <td><?= $detail->spec ?></b></td> -->
                    <td class="txt-right"><?= $detail->price ?></b></td>
                    <td class="txt-right"><?= $detail->disc ?></b></td>
                    <!-- <td><?= $detail->additional_cost ?></b></td> -->
                    <td class="txt-right"><?= $detail->totalPrice ?></b></td>
                    <!-- <td><?= $detail->nilaiPpn ?></b></td> -->
                    <!-- <td><?= $detail->nilaiPph ?></b></td> -->
                </tr>
            <?php } ?>
        </table>
        <div class="header mt-1">
            <div class="txt-right">
                <div>
                    Jumlah Pembelian: <span class="txt-bold">Rp. <?= $dataPOLokal->totalPrice ?></span>
                </div>
                <div class="mt-025">
                    Diskon: <span class="txt-bold"><?= $dataPOLokal->totalDisc ?></span>
                </div>
                <div class="mt-025">
                    DPP: <span class="txt-bold">Rp. <?= $dataPOLokal->dpp ?></span>
                </div>
                <div class="mt-025">
                    PPN (dikreditkan): <span class="txt-bold"><?= $dataPOLokal->totalPpn ?></span>
                </div>
                <div class="mt-025">
                    Total Pembelian: <span class="txt-bold">Rp. <?= $dataPOLokal->totalPo ?></span>
                </div>
            </div>
        </div>
        <div style="text-decoration: underline;">
            Keterangan: <div><?= $dataPOLokal->note ?></div>
        </div>
        <table class="w-100 sign-table border-collapse">
            <tr style="border: 0px;">
                <td style="height: 50px; border: 0px;">Pemesan Order</td>
                <td style="border: 0px;">Pembuat Order</td>
                <td style="border: 0px;">Diketahui oleh</td>
                <td style="border: 0px;">Diperiksa oleh</td>
                <td style="border: 0px;">Disetujui oleh</td>
            </tr>
            <tr>
                <th>
                    <div class="sign-row txt-left">
                        <div>(Warehouse)</div>
                    </div>
                </th>
                <th>
                    <div class="sign-row txt-left">
                        <div>(Pembelian)</div>
                    </div>
                </th>
                <th>
                    <div class="sign-row txt-left">
                        <div>(Kabag Pembelian)</div>
                    </div>
                </th>
                <th>
                    <div class="sign-row txt-left">
                        <div>(Audit)</div>
                    </div>
                </th>
                <th>
                    <div class="sign-row txt-left">
                        <div>(Direktur)</div>
                    </div>
                </th>
            </tr>
        </table>
    <?php } ?>
</body>

</html>