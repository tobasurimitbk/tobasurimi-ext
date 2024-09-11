<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Mutasi Bahan Baku dan Penolong</title>
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
    <h4>PT TOBASURIMI INDUSTRIES, Tbk (<?= $_SESSION['login']->this_company ?>)</h4>
    <p style="margin-top: -15px;">Laporan Mutasi Bahan Baku dan Penolong</p>
    <?php if ($condition['date_start'] != "" && $condition['date_end'] != "") : ?>
        <p style="margin-top: -15px;">Periode : <?= date('d/m/Y', strtotime($condition['date_start'])); ?> / <?= date('d/m/Y', strtotime($condition['date_end'])); ?></p>
    <?php else : ?>
        <p style="margin-top: -15px;">Periode : ALL</p>
    <?php endif; ?>

    <table class="w-100 item-table" style="margin-top: 10px;">
        <tr>
            <th>No</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Satuan</th>
            <th>Kategori</th>
            <th>Jumlah Barang</th>
            <th>Stok Awal</th>
            <th>Pemasukan</th>
            <th>Pengeluaran</th>
            <th>Penyesuaian</th>
            <th>Stok Akhir</th>
            <th>Stok Opname</th>
            <th>Selisih</th>
            <th>Keterangan</th>
        </tr>
        <thead>
            <?php
            $totalStokAwal = 0;
            $totalStokPemasukan = 0;
            $totalStokPengeluaran = 0;
            $totalStokPenyesuaian = 0;
            $totalStokAkhir = 0;
            ?>
            <?php foreach ($data as $d): ?>
                <?php
                $totalStokAwal += floatval(str_replace(',', '', $d['stok_awal']));
                $totalStokPemasukan += floatval(str_replace(',', '', $d['stok_pemasukan']));
                $totalStokPengeluaran += floatval(str_replace(',', '', $d['stok_pengeluaran']));
                $totalStokPenyesuaian += floatval(str_replace(',', '', $d['stok_penyesuaian']));
                $totalStokAkhir += floatval(str_replace(',', '', $d['stok_akhir']));
                ?>
                <tr>
                    <td><?= $d['no'] ?></td>
                    <td><?= $d['kode_barang'] ?></td>
                    <td><?= $d['nama_barang'] ?></td>
                    <td><?= $d['satuan'] ?></td>
                    <td><?= $d['kategori_barang'] ?></td>
                    <td></td>
                    <td><?= $d['stok_awal'] ?></td>
                    <td><?= $d['stok_pemasukan'] ?></td>
                    <td><?= $d['stok_pengeluaran'] ?></td>
                    <td><?= $d['stok_penyesuaian'] ?></td>
                    <td><?= $d['stok_akhir'] ?></td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td></td>
                </tr>
            <?php endforeach; ?>
            <tr style="font-weight: bold;">
                <td colspan="5" style="text-align: center;">
                    Total
                </td>
                <td></td>
                <td><?= number_format($totalStokAwal, 2) ?></td>
                <td><?= number_format($totalStokPemasukan, 2) ?></td>
                <td><?= number_format($totalStokPengeluaran, 2) ?></td>
                <td><?= number_format($totalStokPenyesuaian, 2) ?></td>
                <td><?= number_format($totalStokAkhir, 2) ?></td>
                <td>0.00</td>
                <td>0.00</td>
                <td></td>
            </tr>
        </thead>

    </table>
    <table style="text-align: center;float:right;font-size:11px;">
        <tr>
            <td>
                Kami bertanggung jawab atas kebenaran laporan ini
            </td>
        </tr>
        <tr>
            <td>
                -, <?= date('d/m/Y') ?>
            </td>
        </tr>
        <tr>
            <td>
                Pengusaha di Kawasan Berikat
            </td>
        </tr>
        <tr>
            <td>
                PT. TOBA SURIMI INDUSTRIES, Tbk (KIM 1)
            </td>
        </tr>
    </table>

</body>

</html>