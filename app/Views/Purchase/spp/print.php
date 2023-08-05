<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Surat Permintaan Pembelian</title>
    <style>
        @page {
            size: landscape;
        }

        .inline {
            display: inline-block;
            width: 100%;
        }

        .float-l {
            float: left;
        }

        .float-r {
            float: right;
        }

        .mb-40 {
            margin-bottom: 40px;
        }

        .mr-10 {
            margin-right: 10px;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .mt-30 {
            margin-top: 30px;
        }

        .hr-black {
            border-color: #000;
            border-width: 0;
            border-top-width: 1px;
        }

        .hr-black {
            border-color: #000;
            border-width: 0;
            border-top-width: 1px;
            margin: 3rem;
        }

        .body-container {
            border-radius: 7px;
            margin: auto;
            padding: 1rem;
            text-align: left;
            width: 100%;
            font-size: 12px;
        }

        .body-container1 {
            padding: 1rem;
            text-align: left;
            width: 35%;
        }

        .image {
            float: left;
            margin-left: 1rem;
            margin-top: 1rem;
            margin-right: 1rem;
        }

        html {
            font-size: 10px;
        }

        table td {
            border: 1px solid;
            text-align: center;
        }

        table tr td:last-child {
            text-align: center;
            border-right: none;
        }

        table tr td:first-child {
            text-align: center;
            border-left: none;
        }

        table {
            border-left: 1px solid;
            border-right: 1px solid;
            border-collapse: collapse;
            width: 100%;
        }

        td {
            padding: 5px;
        }

        .note {
            width: 50%;
            text-align: justify;
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

        .txt-left {
            text-align: left;
        }
    </style>
</head>

<body>
    <div style="text-align: center; margin: 1rem 1rem 1rem 1rem; font-size:20px; min-height:40px;">
        <div style="font-size:14pt"> <b>SURAT PERMINTAAN PEMBELIAN </b></div>
    </div>
    <?php if (!empty($dataSPP)) { ?>
        <table class="w-100 sign-table border-collapse signed-info" style="border: 0px;">
            <tr style="border: 0px;">
                <td style="border: 0px; width: 25%;">Tanggal Order: <?= $dataSPP->request_date ?></td>
                <td style="border: 0px; width: 25%;">Jenis Order: <?= $dataSPP->spp_type ?></td>
                <td style="border: 0px; width: 25%;">Departmen: <?= $dataSPP->warehouseName ?></td>
                <td style="border: 0px; width: 25%;">No Spp: <?= $dataSPP->spp_no ?></td>
            </tr>
        </table>
        <div style="margin-top:1rem"><b>List Barang</b></div>
        <table>
            <thead>
                <tr>
                    <td><b>No.</b></td>
                    <td><b>Kode Barang</b></td>
                    <td><b>Nama Barang</b></td>
                    <td><b>Satuan</b></td>
                    <td><b>Spesifikasi</b></td>
                    <td><b>Harga Barang</b></td>
                    <td><b>qty</b></td>
                    <td><b>Total Harga</b></td>
                    <td><b>keterangan</b></td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($dataSPP->purchase_request_details as $detail) {
                ?>
                    <tr>
                        <td><b><?= $detail->no ?></b></td>
                        <td><b><?= $detail->kodeBarang ?></b></td>
                        <td><b><?= $detail->barangName ?></b></td>
                        <td><b><?= $detail->satuanName ?></b></td>
                        <td><b><?= $detail->spec ?></b></td>
                        <td><b><?= $detail->price ?></b></td>
                        <td><b><?= $detail->qty ?></b></td>
                        <td><b><?= $detail->totalPrice ?></b></td>
                        <td><b><?= $detail->note ?></b></td>
                    </tr>
                <?php } ?>
            </tbody>
            <thead>
                <tr>
                    <!-- <td colspan="5">TOTAL</td> -->
                    <td colspan="6">TOTAL</td>
                    <!-- <td><?= $dataSPP->totalPrice ?></td> -->
                    <td><?= $dataSPP->totalQty ?></td>
                    <td><?= $dataSPP->totalAll ?></td>
                    <td></td>
                </tr>
            </thead>
        </table>

        <div>
            <div style="margin-top:2rem;height:100px">Keterangan: <?= $dataSPP->note ?></div>
        </div>
        <div style="text-align:left">
            <div>Barang tersebut agar kami terima dalam keadaan baik.</div>
            <div>Pada tanggal:</div>
        </div>
        <table class="w-100 sign-table border-collapse signed-info" style="border: 0px;">
            <tr style="border: 0px;">
                <td style="height: 50px; border: 0px;"></td>
                <td style="border: 0px;"></td>
                <td style="border: 0px;"></td>
            </tr>
            <tr>
                <th>
                    <div class="sign-row txt-left">
                        <div>Diterima Oleh:</div>
                    </div>
                </th>
                <th>
                    <div class="sign-row txt-left">
                        <div>Disetujui Oleh:</div>
                    </div>
                </th>
                <th>
                    <div class="sign-row txt-left">
                        <div>Diorder Oleh: <?= $dataSPP->createdByName ?></div>
                    </div>
                </th>
            </tr>
        </table>
    <?php } ?>
</body>

</html>