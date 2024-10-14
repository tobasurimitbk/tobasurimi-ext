<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pungutan Bea Cukai BC 2.5</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .item-table {
            border: 1px solid;
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .item-table th,
        .item-table td {
            border: 1px solid;
            font-size: 12px;
            padding: 5px;
            text-align: center;
        }

        .item-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .w-100 {
            width: 100%;
        }
    </style>
</head>

<body>
    <h2>LAPORAN BEA CUKAI BC 2.5</h2>

    <table class="w-100">
        <tbody>
            <tr>
                <td style="width:150px">Tgl Mulai / Tgl Akhir</td>
                <td style="width:10px">:</td>
                <?php if (isset($condition['mulaiTanggalBC41']) && isset($condition['selesaiTanggalBC41']) && $condition['mulaiTanggalBC41'] != "" && $condition['selesaiTanggalBC41'] != "") : ?>
                    <td style="width:80px"><?= date('d/m/Y', strtotime($condition['mulaiTanggalBC41'])); ?></td>
                    <td style="width:10px"> S/D </td>
                    <td><?= date('d/m/Y', strtotime($condition['selesaiTanggalBC41'])); ?></td>
                <?php else : ?>
                    <td colspan="3" style="width:80px">ALL</td>
                <?php endif; ?>
            </tr>
        </tbody>
    </table>

    <table class="item-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No Aju</th>
                <th>No Daftar</th>
                <th>No Bukti Bayar</th>
                <th>Tanggal Bukti Bayar</th>

        </thead>
        <tbody>
            <?php if (isset($dataBeaCukai) && !empty($dataBeaCukai)) : ?>
                <?php foreach ($dataBeaCukai as $row) : ?>
                    <tr>
                        <td><?= isset($row['no']) ? $row['no'] : '-'; ?></td>
                        <td><?= isset($row['date']) ? $row['date'] : '-'; ?></td>
                        <td><?= isset($row['no_aju']) ? $row['no_aju'] : '-'; ?></td>
                        <td><?= isset($row['no_daftar']) ? $row['no_daftar'] : '-'; ?></td>
                        <td><?= isset($row['no_bayar']) ? $row['no_bayar'] : '-'; ?></td>
                        <td><?= isset($row['tanggal_bayar']) ? $row['tanggal_bayar'] : '-'; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="17">Tidak ada data yang tersedia</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>

</html>
