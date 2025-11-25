<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $mutasi['no_mutasi'] ?></title>
    <style>
        body {
            font-size: 12px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        .table-font-size {
            font-size: 11px !important;
            text-align: center;
        }

        .table-border {
            border: 1px solid black;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .mt-5 {
            margin-top: 2px;
        }

        .mt-3 {
            margin-top: 3rem;
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

        tbody {
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="txt-center txt-bold" style="margin-top:-30px;"> <b>RESI PENGELUARAN BARANG</b></div>
    <table class="mt-5">
        <tr>
            <td>Tanggal : <?= date("d-m-Y", strtotime($mutasi['tanggal'])) ?></td>
            <td>No : <?= $mutasi['no_mutasi'] ?></td>
        </tr>
        <tr>
            <td>Departemen : <?= $mutasi['divisi'] ?></td>
            <td></td>
        </tr>
    </table><br>
    <table class="mt-5 table-border">
        <thead class="table-border">
            <tr>
                <td class="table-border" style="width: 5px;"><b class="table-font-size">No</b></td>
                <td class="table-border" style="width: 70px;"><b class="table-font-size">Kode Barang</b></td>
                <td class="table-border" style="width: 250px;"><b class="table-font-size">Nama Barang</b></td>
                <td class="table-border" style="width: 50px;"><b class="table-font-size">Qty</b></td>
                <td class="table-border" style="width: 140px;"><b class="table-font-size">Keterangan</b></td>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($mutasiDetail as $m): ?>
                <tr>
                    <td class="table-border"><b class="table-font-size"><?= $no++ ?></b></td>
                    <td class="table-border"><b class="table-font-size"><?= $m['kode_barang'] ?></b></td>
                    <td class="table-border"><b class="table-font-size"><?= $m['barang_name'] . " " . $m['spesifikasi'] ?></b></td>
                    <td class="table-border"><b class="table-font-size"><?= $m['mutasi']['qty_mutasi'] . " " . $m['mutasi']['unit_name_mutasi'] ?></b></td>
                    <td class="table-border"><b class="table-font-size"><?= $m['keterangan_mutasi'] ?></b></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <?php if (!empty($mutasi['keterangan']) && $mutasi['keterangan'] != ""): ?>
        <div>
            <div class="mt-5">Keterangan: <?= $mutasi['keterangan'] ?></div>
        </div>
    <?php endif; ?>
    <div class="footer" style="margin-top:0px;">
        <table class="w-100 sign-table border-collapse signed-info" style="border: 0px;">
            <tr style="border: 0px;">
                <td style="height: 25px; border: 0px;">Disetujui Oleh</td>
                <td style="border: 0px;">Disetujui Oleh</td>
                <td style="border: 0px;">Diminta Oleh</td>
            </tr>
            <tr>
                <th>
                    <div class="sign-row txt-left">
                        <br>
                        <div>Kabag / Supervissor / Ass</div>
                    </div>
                </th>
                <th>
                    <div class="sign-row txt-left">
                        <br>
                        <div>Gudang</div>
                    </div>
                </th>
                <th>
                    <div class="sign-row txt-left">
                        <br>
                        <div></div>
                    </div>
                </th>
            </tr>
        </table>
    </div>
</body>

</html>