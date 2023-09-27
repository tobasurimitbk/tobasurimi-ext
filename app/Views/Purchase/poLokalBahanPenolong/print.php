<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PO Lokal Bahan Penolong</title>
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
    <?php if (!empty($dataPOLokal)) { ?>
        <table class="w-100">
            <tr>
                <td colspan="2">
                    <div class="txt-underline txt-bold">PO LOKAL BAHAN PENOLONG</div>
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
                    Tanggal: <?= date("d-m-Y", strtotime($dataPOLokal->po_date)) ?>
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
                    Departemen: <?= $dataPOLokal->divisiName ?>
                </td>
                <td colspan="2">
                    Lokasi: <?= $dataPOLokal->companyName ?>
                </td>
            </tr>
        </table>
        <div class="mt-025 txt-bold" style="margin-bottom: 3px;">Harap dikirimkan kepada kami barang-barang berikut dibawah ini:</div>
        <table class="item-table mt-050">
            <tr>
                <!-- <th>No</th> -->
                <!-- <th>Satuan</th> -->
                <!-- <th>Spesifikasi</th> -->
                <!-- <th>Biaya Tambahan</th> -->
                <!-- <th>PPN</th> -->
                <!-- <th>PPH</th> -->
                <th class="txt-left" style="padding-left: 5px; width: 70px;">QTY</th>
                <th class="txt-left" style="padding-left: 5px; width: 120px;">KODE BARANG</th>
                <th class="txt-left" style="padding-left: 5px;">NAMA BARANG</th>
                <th class="txt-left" style="padding-left: 5px; width: 80px;">HARGA</th>
                <th class="txt-left" style="padding-left: 5px; width: 60px;">DISC(%)</th>
                <th class="txt-left" style="padding-left: 5px; width: 80px;">JUMLAH</th>
            </tr>
            <?php
            foreach ($dataPOLokal->am_purchase_order_details as $detail) {
            ?>
                <tr>
                    <!-- <td><b><?= $detail->no ?></b></td> -->
                    <!-- <td><?= $detail->nama_satuan ?></b></td> -->
                    <!-- <td><?= $detail->spec ?></b></td> -->
                    <!-- <td><?= $detail->additional_cost ?></b></td> -->
                    <!-- <td><?= $detail->nilaiPpn ?></b></td> -->
                    <!-- <td><?= $detail->nilaiPph ?></b></td> -->
                    <td style="padding-left: 5px;"><?= $detail->qty . " " . $detail->nama_satuan ?></b></td>
                    <td style="padding-left: 5px;"><?= $detail->kode_barang ?></b></td>
                    <td style="padding-left: 5px;"><?= $detail->nama_barang . " " . $detail->spec ?></b></td>
                    <td class="txt-right" style="padding-right: 5px;"><?= $detail->price ?></b></td>
                    <td class="txt-right" style="padding-right: 5px;"><?= $detail->disc ?></b></td>
                    <td class="txt-right" style="padding-right: 5px;"><?= $detail->totalPrice ?></b></td>
                </tr>
            <?php } ?>
        </table>
        <div class="header mt-025">
            <div class="txt-right">
                <div>
                    Sub Total: <span class="txt-bold">Rp. <?= $dataPOLokal->totalPrice ?></span>
                </div>
                <div class="mt-025">
                    Diskon: <span class="txt-bold">Rp. <?= $dataPOLokal->totalDisc ?></span>
                </div>
                <div class="mt-025">
                    DPP: <span class="txt-bold">Rp. <?= $dataPOLokal->dpp ?></span>
                </div>
                <div class="mt-025">
                    PPN (dikreditkan): <span class="txt-bold">Rp. <?= $dataPOLokal->totalPpn ?></span>
                </div>
                <div class="mt-025">
                    Grand Total: <span class="txt-bold">Rp. <?= $dataPOLokal->totalPo ?></span>
                </div>
            </div>
        </div>
        <div style="text-decoration: underline;">
            Keterangan: <?= $dataPOLokal->note ?>
        </div>
        <table class="w-50 sign-table border-collapse footer" style="padding-top: 0px; margin-top: 0px">
            <tr style="border: 0px;">
                <td style="height: 30px; border: 0px;">Pemesan Order</td>
                <td style="border: 0px;">Pembuat Order</td>
                <td style="border: 0px;">Diketahui oleh</td>
                <td style="border: 0px;">Diperiksa oleh</td>
                <td style="border: 0px;">Disetujui oleh</td>
            </tr>
            <tr>
                <th>
                    <div class="sign-row txt-left">
                        <div>(divisi)</div>
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