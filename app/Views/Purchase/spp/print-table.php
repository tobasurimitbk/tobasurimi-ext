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
            display: flex;
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
    </style>
</head>

<body>
    <div style="text-align: center; margin: 1rem 1rem 1rem 1rem; font-size:20px; min-height:40px;">
        <div style="font-size:14pt"> <b>DATA SURAT PERMINTAAN PEMBELIAN </b></div>
    </div>
    <table>
        <thead>
            <tr>
                <td><b>No.</b></td>
                <td><b>Tipe SPP</b></td>
                <td><b>NO. SPP</b></td>
                <td><b>Departemen</b></td>
                <td><b>Total Harga</b></td>
                <td><b>Tanggal Order</b></td>
                <td><b>Tanggal Dibuat</b></td>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($dataSPP)) { ?>
                <?php
                foreach ($dataSPP as $detail) {
                ?>
                    <tr>
                        <td><?= $detail['no'] ?></td>
                        <td><?= $detail['spp_type'] ?></td>
                        <td><?= $detail['spp_no'] ?></td>
                        <td><?= $detail['warehouseName'] ?></td>
                        <td><?= $detail['total'] ?></td>
                        <td><?= $detail['request_date'] ?></td>
                        <td><?= $detail['createdAt'] ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>
</body>

</html>