<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Potongan Pinjaman</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            letter-spacing: 2px;
            font-size: 12px;
        }

        hr {
            border: none;
            border-top: 1px dashed #000;
        }

        #dashed-border-table {
            border-collapse: collapse;
        }

        #dashed-border-table th,
        #dashed-border-table td {
            border: 1px dashed #000;
            padding: 5px;
            text-align: center;
        }
    </style>
</head>

<body>
    <table border="0" width="100%">
        <tr>
            <td width="210">
                <table border="0">
                    <tr>
                        <td>Hal</td>
                        <td>:</td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td>Tgl</td>
                        <td>:</td>
                        <td><?= date('d/m/Y', strtotime($yearMonth . "-15")) ?></td>
                    </tr>
                </table>
            </td>
            <td>
                <table border="0">
                    <tr>
                        <td colspan="3" style="text-align: center; font-size:14px;">PT TOBA SURIMI</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table width="100%">
        <tr align="center" style="font-weight: bold; font-size:15px">
            <td>DAFTAR POTONGAN PINJAMAN</td>
        </tr>
        <tr align="center" style=" font-size:12px">
            <td>Pada Tanggal <?= date('d/m/Y', strtotime($yearMonth . "-15")) ?></td>
        </tr>
    </table>

    <table border="0">
        <tr>
            <td>Departemen</td>
            <td>:</td>
            <td><?= $divisi['divisi'] ?></td>
        </tr>
        <tr>
            <td>Bagian</td>
            <td>:</td>
            <td><?= $bagian['nama_bagian'] ?></td>
        </tr>
    </table>

    <table width="100%" style="margin-top: 10px;" border="1" id="dashed-border-table">
        <thead>
            <tr align="center">
                <td>No</td>
                <td>Nip</td>
                <td>Nama Karyawan</td>
                <td>J.Hr.Krj</td>
                <td>Jlh. Pinj</td>
                <td>Tanda Tangan</td>
            </tr>
        </thead>
        <tbody>
            <?php if (count($pinjaman) == 0) : ?>
                <tr align="center">
                    <td colspan="6">
                        Tidak ada karyawan yang mengambil pinjaman pada <?= $yearMonth ?>
                    </td>
                </tr>
            <?php else : ?>
                <?php $no = 1; ?>
                <?php $total = 0; ?>
                <?php foreach ($pinjaman as $p) : ?>
                    <?php $total += $p['nominal'];  ?>
                    <tr align="center">
                        <td><?= $no++; ?></td>
                        <td><?= $p['nip'] ?></td>
                        <td><?= $p['name'] ?></td>
                        <td><?= $p['hadir'] ?></td>
                        <td><?= number_format($p['nominal'] == null ? 0 : $p['nominal'], 2) ?></td>
                        <td style="padding: 30px;"></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="4" align="right" style="text-align: right;">Total</td>
                    <td>Rp <?= number_format($total, 2)  ?></td>
                    <td></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>

</html>