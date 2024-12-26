<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Jurnal Umum</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table,
        .table th,
        .table td {
            border: 1px solid black;
        }

        .table th,
        .table td {
            padding: 5px;
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2><?= $company['holding_company'] ?> (<?= $company['company'] ?>)</h2>
        <h3>List Jurnal</h3>
    </div>
    <table>
        <tr>
            <td>Periode : </td>
            <td>Tanggal: <?= $startDate != "" ? date('d/m/Y', strtotime($startDate)) : ""; ?> - <?= $endDate != "" ? date('d/m/Y', strtotime($endDate)) : ""; ?></td>
        </tr>
    </table>
    <br>
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Transaksi</th>
                <th>Nomor</th>
                <th>Tanggal</th>
                <th>Invoice</th>
                <th>Keterangan</th>
                <th>Nilai</th>
                <th>Valas</th>
                <th>Nilai (IDR)</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            foreach ($dataJurnal as $jurnal): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $jurnal['transaksi_type_name'] ?></td>
                    <td><?= $jurnal['no_transaksi'] ?></td>
                    <td><?= $jurnal['tanggal_transaksi']; ?></td>
                    <td><?= $jurnal['invoice']; ?></td>
                    <td><?= $jurnal['uraian_transaksi']; ?></td>
                    <td class="text-right"><?= number_format($jurnal['nilai'], 2); ?></td>
                    <td><?= $jurnal['valas']; ?></td>
                    <td class="text-right"><?= number_format($jurnal['nilai_idr'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>