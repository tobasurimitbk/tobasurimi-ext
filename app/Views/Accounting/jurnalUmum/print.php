<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher Accounting</title>
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
        <h3>Voucher Accounting</h3>
    </div>
    <table>
        <tr>
            <td>No Bukti:<?= !empty($transaksiJurnal) ? ($transaksiJurnal['metode_input'] == "system" ? $transaksiJurnal['no_transaksi'] : $transaksiJurnal['no_bukti']) : "" ?></td>
            <td>Tanggal: <?= date('d/m/Y', strtotime($transaksiJurnal['tanggal_transaksi'])); ?></td>
        </tr>
    </table>
    <br>
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Description</th>
                <th>Jumlah</th>
                <th>Valas</th>
                <th>Kurs</th>
                <th>Debet</th>
                <th>Kredit</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            $totalKredit = 0;
            $totalDebit = 0;
            foreach ($jurnalUmumList as $jurnal): ?>
                <?php
                if ($jurnal['jenis_transaksi'] == 'debit') {
                    $totalDebit += $jurnal['jumlah_idr'];
                } else {
                    $totalKredit += $jurnal['jumlah_idr'];
                }


                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $jurnal['no_sub'] . ' - ' . $jurnal['nama_sub']; ?></td>
                    <td class="text-right"><?= number_format($jurnal['jumlah'], 2); ?></td>
                    <td><?= $jurnal['valas']; ?></td>
                    <td class="text-right"><?= number_format($jurnal['kurs'], 2); ?></td>
                    <td class="text-right"><?= $jurnal['jenis_transaksi'] == 'debit' ? number_format($jurnal['jumlah_idr'], 2) : '0.00'; ?></td>
                    <td class="text-right"><?= $jurnal['jenis_transaksi'] == 'kredit' ? number_format($jurnal['jumlah_idr'], 2) : '0.00'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right">TOTAL</td>
                <td class="text-right"><?= number_format($totalDebit, 2); ?></td>
                <td class="text-right"><?= number_format($totalKredit, 2);  ?></td>
            </tr>
        </tfoot>
    </table>
    <br><br><br>
    <table style="width: 100%; text-align: center; margin-top: 50px;">
        <tr>
            <td>Accounting,</td>
            <td>Confirm,</td>
            <td>Receipt,</td>
        </tr>
        <tr>
            <td><br><br><br>________________</td>
            <td><br><br><br>________________</td>
            <td><br><br><br>________________</td>
        </tr>
        <tr>
            <td><?= $_SESSION['login']->this_role_name  ?></td>
            <td></td>
            <td></td>
        </tr>
    </table>
</body>

</html>