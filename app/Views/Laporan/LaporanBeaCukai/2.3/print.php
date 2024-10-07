<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Barang Work In Progress</title>
    <style>
        .company-name {
            font-weight: 700;
            border: 1px solid;
            padding: 5px;
            border-radius: 7px;
            margin-bottom: 10px;
            display: inline-block;
            min-width: 70px
        }

        .description-container {
            border: 1px solid;
            border-radius: 7px;
            height: 65px;
            margin-top: 20px;
            width: 60%;
            position: relative;
            padding-top: 7px;
            padding-left: 17px;
        }

        .description-label {
            position: absolute;
            top: -10px;
            background: white;
            left: 15px;
            padding-left: 3px;
            padding-right: 5px;
        }

        .item-table {
            border: 1px solid;
            width: 100%;
            height: 230px;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .item-table th {
            border-right: 1px solid;
            border-bottom: 1px solid;
            font-size: 13px;
            font-weight: normal;
            padding: 2px
        }

        .item-table td {
            border: 1px solid;
            font-size: 10px;
            padding: 2px
        }

        .signature-table {
            border-spacing: 30px 0;
            margin-top: 10px;
        }

        .txt-bold {
            font-weight: 700;
        }

        .txt-center {
            text-align: center;
        }

        .txt-right {
            text-align: right;
        }

        .w-100 {
            width: 100%;
        }
    </style>
</head>

<body>
    <h2>LAPORAN BARANG WORK IN PROGRESS</h2>
    <table class="w-100">
        <tbody>
            <tr>
                <td style="width:150px">Tgl Mulai / Tgl Akhir</td>
                <td style="width:10px">:</td>
                <?php if ($condition['date_start'] != "" && $condition['date_end'] != "") : ?>
                    <td style="width:80px"><?= date('d/m/Y', strtotime($condition['date_start'])); ?></td>
                    <td style="width:10px"> S/D </td>
                    <td><?= date('d/m/Y', strtotime($condition['date_end'])); ?></td>
                <?php else : ?>
                    <td colspan="3" style="width:80px">ALL</td>
                <?php endif; ?>
            </tr>
        </tbody>
    </table>

    <table class="w-100 item-table">
        <tr>
            <th>No</th>
            <th>Tipe Barang</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Qty</th>
            <th>Satuan</th>
            <th>Keterangan</th>
        </tr>
        <?php foreach ($dataWipResult as $row) : ?>
            <tr>
                <td><?= $row['no']; ?></td>
                <td><?= $row['tipeBarang']; ?></td>
                <td><?= $row['kodeBarang']; ?></td>
                <td><?= $row['namaBarang']; ?></td>
                <td><?= $row['qty']; ?></td>
                <td><?= $row['satuan']; ?></td>
                <td><?= $row['keterangan']; ?></td>
            </tr>
        <?php endforeach; ?>

    </table>

</body>

</html>