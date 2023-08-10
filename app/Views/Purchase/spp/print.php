<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Surat Permintaan Pembelian</title>
    <style>
        body {
            font-size: 13px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        @page {
            size: 8.27in 5.50in landscape;
            margin: 25px;
            padding: 25px;
        }

        .table-border {
            border: 1px solid black;
            padding-left: 3px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .mt-5 {
            margin-top: 5px;
        }

        .sign-table td:not(:last-child) {
            border: 1px solid;
        }

        .sign-row {
            display: flex;
            justify-content: space-around;
            margin-top: 0.25rem;
            width: 100%;
        }

        .sign-row>div {
            width: 200px;
            border-top: 1px solid;
            margin-top: 1rem
        }

        .txt-bold {
            font-weight: 700;
            font-size: 17px;
        }

        .txt-left {
            text-align: left;
        }

        .txt-center {
            text-align: center;
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
    <div class="txt-center txt-bold"> <b>SURAT PERMINTAAN PEMBELIAN </b></div>
    <?php if (!empty($dataSPP)) { ?>
        <table class="mt-5">
            <tr>
                <td>Tanggal Order: <?= date("d-m-Y", strtotime($dataSPP->request_date)) ?></td>
                <td>Jenis Order: <?= $dataSPP->spp_type ?></td>
            </tr>
            <tr>
                <td>Departmen: <?= $dataSPP->warehouseName ?></td>
                <td>No Spp: <?= $dataSPP->spp_no ?></td>
            </tr>
        </table>
        <table class="mt-5 table-border">
            <thead class="table-border">
                <tr class="table-border">
                    <td class="table-border" style="width: 2px;"><b>No.</b></td>
                    <td class="table-border" style="width: 105px;"><b>Kode Barang</b></td>
                    <td class="table-border" style="width: 220px;"><b>Nama Barang</b></td>
                    <td class="table-border" style="width: 80px;"><b>Qty</b></td>
                    <td class="table-border" style="width: 8px;"><b>Keterangan</b></td>
                    <td class="table-border" style="width: 35px;"><b>Harga</b></td>
                    <td class="table-border" style="width: 10px;"><b></b></td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($dataSPP->purchase_request_details as $detail) {
                ?>
                    <tr>
                        <td class="table-border"><b><?= $detail->no ?></b></td>
                        <td class="table-border"><b><?= $detail->kodeBarang ?></b></td>
                        <td class="table-border"><b><?= $detail->barangName . " " . $detail->spec ?></b></td>
                        <td class="table-border"><b><?= $detail->qty . " " . $detail->satuanName ?></b></td>
                        <td class="table-border"><b><?= $detail->note ?></b></td>
                        <td class="table-border"><b><?= $detail->price ?></b></td>
                        <td class="table-border"><b></b></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <div>
            <div class="mt-5">Keterangan: <?= $dataSPP->note ?></div>
        </div>
        <div class="footer">
            <div style="text-align:left">
                <div>Barang tersebut agar kami terima dalam keadaan baik.</div>
                <div>Pada tanggal:</div>
            </div>
            <table class="w-100 sign-table border-collapse signed-info" style="border: 0px;">
                <tr style="border: 0px;">
                    <td style="height: 30px; border: 0px;"></td>
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
        </div>
    <?php } ?>
</body>

</html>