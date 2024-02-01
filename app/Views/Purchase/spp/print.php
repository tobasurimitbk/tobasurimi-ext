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

        .table-font-size {
            font-size: 11px !important;
            text-align: center;
        }

        @page {
            size: 9.44in 4.33in landscape;
            margin: 29px;
            padding: 29px;
        }

        .table-border {
            border: 1px solid black;
            padding-left: 3px;
            padding-right: 3px;
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

        tbody {
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="txt-center txt-bold"> <b>SURAT PERMINTAAN PEMBELIAN </b></div>
    <?php if (!empty($dataSPP)) { ?>
        <table class="mt-5">
            <tr>
                <td>Tanggal Order : <?= date("d-m-Y", strtotime($dataSPP->request_date)) ?></td>
                <td>Jenis Order : <?= $dataSPP->spp_type ?></td>
            </tr>
            <tr>
                <td>Departemen: <?= $dataSPP->divisiName ?></td>
                <td>No Spp: <?= $dataSPP->spp_no ?></td>
            </tr>
        </table><br>
        <table class="mt-5 table-border">
            <thead class="table-border">
                <tr class="table-border">
                    <td class="table-border" style="width: 5px;"><b class="table-font-size">No</b></td>
                    <td class="table-border" style="width: 105px;"><b class="table-font-size">Kode Barang</b></td>
                    <td class="table-border" style="width: 220px;"><b class="table-font-size">Nama Barang</b></td>
                    <td class="table-border" style="width: 90px;"><b class="table-font-size">Qty</b></td>
                    <td class="table-border" style="width: 10px;"><b class="table-font-size">Keterangan</b></td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($dataSPP->purchase_request_details as $detail) {
                ?>
                    <tr>
                        <td class="table-border"><b class="table-font-size"><?= $detail->no ?></b></td>
                        <td class="table-border"><b class="table-font-size"><?= $detail->kode_barang ?></b></td>
                        <td class="table-border"><b class="table-font-size"><?= $detail->nama_barang ?></b></td>
                        <td class="table-border"><b class="table-font-size"><?= $detail->qty . " " . $detail->nama_satuan ?></b></td>
                        <td class="table-border"><b class="table-font-size"><?= $detail->note ?></b></td>
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
                    <td style="height: 30px; border: 0px;">Diterima Oleh</td>
                    <td style="border: 0px;">Disetujui Oleh</td>
                    <td style="border: 0px;">Diorder Oleh</td>
                </tr>
                <tr>
                    <th>
                        <div class="sign-row txt-left">
                            <br>
                            <div></div>
                        </div>
                    </th>
                    <th>
                        <div class="sign-row txt-left">
                            <br>
                            <div></div>
                        </div>
                    </th>
                    <th>
                        <div class="sign-row txt-left">
                            <br>
                            <div><?= $dataSPP->createdByName ?></div>
                        </div>
                    </th>
                </tr>
            </table>
        </div>
    <?php } ?>
</body>

</html>