<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biaya Udang</title>
    <style>
        body {
            font-size: 12px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        .body {
            margin-left: 30px;
            margin-right: 30px;
        }

        .vendor-detail {
            font-weight: bold;
            font-size: 14px;
        }

        .head-table {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
        }

        .sub-head-table {
            margin-top: 5px;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
            border-collapse: collapse;
            border: 1px solid black;
            font-size: 11px;
        }

        .table th,
        .table td {
            padding: 0.25rem;
            vertical-align: top;
            border-top: 1px solid black;
            border-right: 1px solid black;
        }

        .table th:last-child,
        .table td:last-child {
            border-right: none;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid black;
        }

        .bg-gray {
            background-color: #fafafa;
        }
    </style>
</head>

<body>
    <?php if (!empty($biayaUdang)) : ?>
        <div class="body">
            <table style="width: 100%;">
                <tr>
                    <td style="text-align: center;">
                        <h2>
                            <u>
                                Rincian Pembayaran Biaya Gaji Kopek Udang / Cumi Kulit Rebus
                            </u>
                            <br>
                        </h2>
                        <h4 style="margin-top: -10px;">
                            NO : <?= $biayaUdang['no_pembayaran'] ?>
                        </h4>
                    </td>
                </tr>
            </table>

            <table>
                <tr>
                    <td>NAMA VENDOR</td>
                    <td>:</td>
                    <td><?= $vendor == null ? "-" : strtoupper($vendor != null ? $vendor['name'] : '') ?></td>
                </tr>
                <tr>
                    <td>TANGGAL</td>
                    <td>:</td>
                    <td><?= date('d/m/Y', strtotime($biayaUdang['tanggal'])) ?></td>
                </tr>
                <tr>
                    <td>KETERANGAN</td>
                    <td>:</td>
                    <td><?= $biayaUdang['keterangan'] ?></td>
                </tr>
            </table>

            <table class="table" style="margin-top: 15px;">
                <thead style="text-align: center; font-weight:bold;">
                    <tr>
                        <th rowspan="2" style="text-align: center;">No</th>
                        <th rowspan="2" style="text-align: center;">Tanggal PO</th>
                        <th rowspan="2" style="text-align: center;">Jenis</th>
                        <th rowspan="2" style="text-align: center;">Mentah</th>
                        <th style="text-align: center;">KG Keluar</th>
                        <th rowspan="2" style="text-align: center;">Kotor</th>
                        <th rowspan="2" style="text-align: center;">Canning</th>
                        <th rowspan="2" style="text-align: center;">KG Daging</th>
                        <th rowspan="2" style="text-align: center;">Ratio</th>
                        <th rowspan="2" style="text-align: center;">Harga Per Kilo</th>
                        <th rowspan="2" style="text-align: center;">Total Harga</th>
                    </tr>
                    <tr>
                        <th style="text-align: center;">Tanggal Keluar</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $grand_total = 0;
                    $no = 1;
                    ?>

                    <?php foreach ($biayaUdangDetail as $parentBlock) : ?>
                        <?php
                        $p = $parentBlock['parent'];
                        $detail = $parentBlock['detail'];
                        $rowspan = count($detail) > 0 ? count($detail) : 1;

                        // Hitung total harga
                        $total_harga = ($p['sum_bersih'] ?? 0) * ($p['harga_per_kilo'] ?? 0);
                        $grand_total += $total_harga;
                        ?>

                        <!-- PARENT ROW -->
                        <tr class="bg-gray">
                            <!-- No -->
                            <td rowspan="<?= $rowspan ?>" style="text-align: center; vertical-align: middle;">
                                <?= $no++ ?>
                            </td>

                            <!-- Tanggal PO -->
                            <td rowspan="<?= $rowspan ?>" style="text-align: center; vertical-align: middle;">
                                <?= !empty($p['tanggal_po']) ? date('d/m/Y', strtotime($p['tanggal_po'])) : 
                                    (!empty($p['tanggal_masuk']) ? date('d/m/Y', strtotime($p['tanggal_masuk'])) : '-') ?>
                            </td>

                            <!-- Jenis -->
                            <td rowspan="<?= $rowspan ?>" style="vertical-align: middle;">
                                <?= $p['barang_name'] ?>
                            </td>

                            <!-- Mentah -->
                            <td rowspan="<?= $rowspan ?>" style="vertical-align: middle;">
                                <?= $p['spesifikasi'] ?>
                            </td>

                            <!-- KG Keluar - Baris pertama detail -->
                            <td style="text-align: right;">
                                <?= isset($detail[0]) ? number_format($detail[0]['qty_keluar'] ?? 0, 2) : '0.00' ?>
                            </td>

                            <!-- Kotor -->
                            <td rowspan="<?= $rowspan ?>" style="text-align: right; vertical-align: middle;">
                                <?= number_format($p['sum_kotor'] ?? 0, 2) ?>
                            </td>

                            <!-- Canning -->
                            <td rowspan="<?= $rowspan ?>" style="text-align: right; vertical-align: middle;">
                                <?= number_format($p['sum_bersih'] ?? 0, 2) ?>
                            </td>

                            <!-- KG Daging -->
                            <td rowspan="<?= $rowspan ?>" style="text-align: right; vertical-align: middle;">
                                <?= number_format($p['sum_bersih'] ?? 0, 2) ?>
                            </td>

                            <!-- Ratio -->
                            <td rowspan="<?= $rowspan ?>" style="text-align: center; vertical-align: middle;">
                                <?= number_format($p['ratio'] ?? 0, 2) ?>%
                            </td>

                            <!-- Harga Per Kilo -->
                            <td rowspan="<?= $rowspan ?>" style="text-align: right; vertical-align: middle;">
                                <?= number_format($p['harga_per_kilo'] ?? 0, 2) ?>
                            </td>

                            <!-- Total Harga -->
                            <td rowspan="<?= $rowspan ?>" style="text-align: right; vertical-align: middle;">
                                <?= number_format($total_harga, 2) ?>
                            </td>
                        </tr>

                        <!-- DETAIL ROWS (jika ada lebih dari 1 detail) -->
                        <?php for ($i = 1; $i < count($detail); $i++) : ?>
                            <?php $d = $detail[$i]; ?>
                            <tr>
                                <!-- KG Keluar untuk detail rows -->
                                <td style="text-align: right;">
                                    <?= number_format($d['qty_keluar'] ?? 0, 2) ?>
                                </td>
                            </tr>
                        <?php endfor; ?>

                    <?php endforeach; ?>

                    <!-- GRAND TOTAL ROW -->
                    <tr style="font-weight: bold;">
                        <td colspan="10" style="text-align: center;"><b>GRAND TOTAL</b></td>
                        <td style="text-align: right;"><?= number_format($grand_total, 2) ?></td>
                    </tr>
                </tbody>
            </table>

        </div>
    <?php endif; ?>
</body>
</html>