<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PO Lokal Bahan Penolong</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 13px;
        }


        @page {
            size: 6.8in 13.2in landscape;
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
            background-color: white;
        }

        .item-table tr th {
            border: 1px solid grey;
        }

        .mt-025 {
            margin-top: -20px;
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
            bottom: 25;
            width: 100%;
            height: 90px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <?php if (!empty($dataPOLokal)) { ?>
        <table class="w-100" style="margin-top: -15px;">
            <tr>
                <td style="text-align: right; font-weight:bold;">
                    <div style="font-size: 13px;">
                        <u> Tanggal: <?= date("d-m-Y", strtotime($dataPOLokal->po_date)) ?></u>
                    </div>
                </td>
            </tr>
        </table>
        <table class="w-100" style="margin-top: -8000px;">
            <tr>
                <td colspan="2">
                    <h3 class="txt-underline txt-bold">PO LOKAL BAHAN PENOLONG</h3> <br>
                    <div style="margin-top: -20px;">
                        <table>
                            <tr>
                                <td>Departemen</td>
                                <td>:</td>
                                <td><?= $dataPOLokal->divisiName ?></td>
                            </tr>
                            <tr>
                                <td>Unit</td>
                                <td>:</td>
                                <td><?= $dataPOLokal->companyName ?></td>
                            </tr>
                            <tr>
                                <td>Catatan</td>
                                <td>:</td>
                                <td><?= $dataPOLokal->note ?></td>
                            </tr>
                        </table>
                    </div>

                </td>
                <td>
                    <h4>No. PO : <?= $dataPOLokal->po_no ?></h4>
                </td>
            </tr>
        </table>
        <table style=" margin-top:-40px;float:right; width:50%" border="0">
            <tr>
                <td style="width: 80px; text-align: left;">Kepada Yth</td>
                <td style="width: 0px;">:</td>
                <td style="text-align:left;"><?= strtoupper($dataPOLokal->supplierName) ?></td>
            </tr>
            <tr>
                <td style="text-align: left;">Alamat</td>
                <td>:</td>
                <td style="text-align:left"><?= strtoupper($dataPOLokal->supplierAddress) ?></td>
            </tr>
            <tr>
                <td>Telepon</td>
                <td>:</td>
                <td style="text-align:left"><?= $dataPOLokal->supplierPhone ?></td>
            </tr>
            <tr>
                <td>NPWP</td>
                <td>:</td>
                <td style="text-align:left"><?= $dataPOLokal->supplierNPWP ?></td>
            </tr>
        </table>
        <br><br>
        <div class="mt-025 txt-bold" style="margin-bottom: 3px; margin-top:20px;">Harap dikirimkan kepada kami barang-barang berikut dibawah ini:</div>
        <table class="item-table mt-050">
            <tr>
                <!-- <th>No</th> -->
                <!-- <th>Satuan</th> -->
                <!-- <th>Spesifikasi</th> -->
                <!-- <th>Biaya Tambahan</th> -->
                <!-- <th>PPN</th> -->
                <!-- <th>PPH</th> -->
                <th class="txt-left" style="padding-left: 5px; width: 120px;">QTY</th>
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
                    <!-- <td><?= $detail->additional_cost ?></b></td> -->
                    <!-- <td><?= $detail->nilaiPpn ?></b></td> -->
                    <!-- <td><?= $detail->nilaiPph ?></b></td> -->
                    <td style="padding-left: 5px;"><?= $detail->qty . " " . $detail->nama_satuan ?></b></td>
                    <td style="padding-left: 5px;"><?= $detail->kode_barang ?></b></td>
                    <td style="padding-left: 5px;"><?= $detail->nama_barang ?></b></td>
                    <td class="txt-right" style="padding-right: 5px;"><?= "" . number_format(formatter($detail->price, "STR_TO_FLOAT"), 2, '.', ',') ?></b></td>
                    <td class="txt-right" style="padding-right: 5px;"><?= $detail->disc ?></b></td>
                    <td class="txt-right" style="padding-right: 5px;"><?= "" . number_format(formatter($detail->totalPriceWithoutAdditional, "STR_TO_FLOAT"), 2, '.', ',') ?></b></td>
                </tr>
            <?php } ?>
        </table>
        <div class="header mt-025">
            <table style="text-align:right; width:100%; margin-top:30px; font-weight:bold;">
                <tr>
                    <td colspan="3">____________</td>
                </tr>
                <tr>
                    <td style="width:1100px">Total</td>
                    <td style="width: 10px;">:</td>
                    <td> <?= $dataPOLokal->totalPrice ?></td>
                </tr>
                <tr>
                    <td>Tambahan</td>
                    <td>:</td>
                    <td> <?= $dataPOLokal->totalTambahan ?></td>
                </tr>
                <tr>
                    <td>Diskon</td>
                    <td>:</td>
                    <td> <?= $dataPOLokal->totalDisc ?></td>
                </tr>
                <!-- <tr>
                    <td>Sub Total</td>
                    <td>:</td>
                    <td> <?= $dataPOLokal->totalPrice ?></td>
                </tr> -->
                <tr>
                    <td>PPN (dikreditkan)</td>
                    <td>:</td>
                    <td> <?= $dataPOLokal->totalPpn ?></td>
                </tr>
                <tr>
                    <td>Grand Total</td>
                    <td>:</td>
                    <td> <?= $dataPOLokal->totalPo ?></td>
                </tr>
            </table>
        </div>
        <div class="footer">
            <table class="w-100 sign-table border-collapse " style="padding-top: 0px;margin-left:-30px;">
                <tr>
                    <td style="text-align: center;"><b><u>Keterangan: <?= $dataPOLokal->keterangan ?></u></b></td>
                </tr>
                <tr style="border: 0px;">
                    <td style="height: 30px; border: 0px; text-align:center; ">Pemesan Order :</td>
                    <td style="border: 0px;text-align:center">Pembuat Order</td>
                    <td style="border: 0px;text-align:center">Diketahui oleh</td>
                    <td style="border: 0px;text-align:center">Diperiksa oleh</td>
                    <td style="border: 0px;text-align:center">Disetujui oleh</td>
                </tr>
                <tr>
                    <th style="font-weight: normal;">
                        <br><br>
                        (Warehouse) <br><br>
                    </th>
                    <th style="font-weight: normal;">
                        <br><br>
                        (Pembelian)
                    </th>
                    <th style="font-weight: normal;">
                        <br><br>
                        (Kabag Pembelian)
                    </th>
                    <th style="font-weight: normal;">
                        <br><br>
                        (Audit)
                    <th style="font-weight: normal;">
                        <br><br>
                        (Direktur)
                    </th>
                </tr>

            </table>
            <br>
            <div style="text-align:center">
                <b>Jatuh Tempo : <?= $dataPOLokal->jatuhTempoHari ?> hari setelah tanda terima</b>
            </div>
            <br>

        </div>

    <?php } ?>
</body>

</html>