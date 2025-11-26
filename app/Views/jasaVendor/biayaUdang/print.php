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

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .vertical-middle {
            vertical-align: middle;
        }

        .nowrap {
            white-space: nowrap;
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
                        <th rowspan="2" class="text-center">No</th>
                        <th rowspan="2" class="text-center">Tanggal PO</th>
                        <th colspan="2" class="text-center">Barang Masuk</th>
                        <th colspan="3" class="text-center">Quantity</th>
                        <th rowspan="2" class="text-center">Ratio</th>
                        <th rowspan="2" class="text-center">Harga Per Kilo</th>
                        <th rowspan="2" class="text-center">Total Harga</th>
                        <th colspan="4" class="text-center">Barang Keluar</th>
                    </tr>
                    <tr>
                        <!-- Barang Masuk -->
                        <th class="text-center">Jenis</th>
                        <th class="text-center">Spesifikasi</th>
                        
                        <!-- Quantity -->
                        <th class="text-center">KG Kotor</th>
                        <th class="text-center">Canning</th>
                        <th class="text-center">KG Daging</th>
                        
                        <!-- Barang Keluar -->
                        <th class="text-center">Tanggal Keluar</th>
                        <th class="text-center">Qty Keluar</th>
                        <th class="text-center">Jenis</th>
                        <th class="text-center">Spesifikasi</th>
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
                            <td rowspan="<?= $rowspan ?>" class="text-center vertical-middle">
                                <?= $no++ ?>
                            </td>

                            <!-- Tanggal PO -->
                            <td rowspan="<?= $rowspan ?>" class="text-center vertical-middle">
                                <?= !empty($p['tanggal_po']) ? date('d/m/Y', strtotime($p['tanggal_po'])) : 
                                    (!empty($p['tanggal_masuk']) ? date('d/m/Y', strtotime($p['tanggal_masuk'])) : '-') ?>
                            </td>

                            <!-- Jenis Barang Masuk -->
                            <td rowspan="<?= $rowspan ?>" class="text-left vertical-middle">
                                <?= $p['barang_name'] ?? '-' ?>
                            </td>

                            <!-- Spesifikasi Barang Masuk -->
                            <td rowspan="<?= $rowspan ?>" class="text-left vertical-middle">
                                <?= $p['spesifikasi'] ?? '-' ?>
                            </td>

                            <!-- KG Kotor -->
                            <td rowspan="<?= $rowspan ?>" class="text-right vertical-middle">
                                <?= number_format($p['sum_kotor'] ?? 0, 2) ?>
                            </td>

                            <!-- Canning -->
                            <td rowspan="<?= $rowspan ?>" class="text-right vertical-middle">
                                <?= number_format($p['sum_bersih'] ?? 0, 2) ?>
                            </td>

                            <!-- KG Daging -->
                            <td rowspan="<?= $rowspan ?>" class="text-right vertical-middle">
                                <?= number_format($p['sum_bersih'] ?? 0, 2) ?>
                            </td>

                            <!-- Ratio -->
                            <td rowspan="<?= $rowspan ?>" class="text-center vertical-middle">
                                <?= number_format($p['ratio'] ?? 0, 2) ?>%
                            </td>

                            <!-- Harga Per Kilo -->
                            <td rowspan="<?= $rowspan ?>" class="text-right vertical-middle">
                                <?= number_format($p['harga_per_kilo'] ?? 0, 2) ?>
                            </td>

                            <!-- Total Harga -->
                            <td rowspan="<?= $rowspan ?>" class="text-right vertical-middle">
                                <?= number_format($total_harga, 2) ?>
                            </td>

                            <!-- DETAIL OUT - BARIS PERTAMA -->
                            
                            <!-- Tanggal Keluar -->
                            <td class="text-center vertical-middle">
                                <?= !empty($detail[0]['tanggal_keluar']) ? date('d/m/Y', strtotime($detail[0]['tanggal_keluar'])) : '-' ?>
                            </td>

                            <!-- Qty Keluar -->
                            <td class="text-right vertical-middle">
                                <?= number_format($detail[0]['qty_keluar'] ?? 0, 2) ?>
                            </td>

                            <!-- Jenis Barang Keluar -->
                            <td class="text-left vertical-middle">
                                <?= $detail[0]['barang_name_out'] ?? '-' ?>
                            </td>

                            <!-- Spesifikasi Barang Keluar -->
                            <td class="text-left vertical-middle">
                                <?= $detail[0]['spesifikasi_out'] ?? '-' ?>
                            </td>
                        </tr>

                        <!-- DETAIL ROWS TAMBAHAN (jika ada lebih dari 1 out) -->
                        <?php for ($i = 1; $i < count($detail); $i++) : ?>
                            <?php $d = $detail[$i]; ?>
                            <tr>
                                <!-- Tanggal Keluar -->
                                <td class="text-center vertical-middle">
                                    <?= !empty($d['tanggal_keluar']) ? date('d/m/Y', strtotime($d['tanggal_keluar'])) : '-' ?>
                                </td>

                                <!-- Qty Keluar -->
                                <td class="text-right vertical-middle">
                                    <?= number_format($d['qty_keluar'] ?? 0, 2) ?>
                                </td>

                                <!-- Jenis Barang Keluar -->
                                <td class="text-left vertical-middle">
                                    <?= $d['barang_name_out'] ?? '-' ?>
                                </td>

                                <!-- Spesifikasi Barang Keluar -->
                                <td class="text-left vertical-middle">
                                    <?= $d['spesifikasi_out'] ?? '-' ?>
                                </td>
                            </tr>
                        <?php endfor; ?>

                    <?php endforeach; ?>

                    <!-- GRAND TOTAL ROW -->
                    <tr style="font-weight: bold;">
                        <td colspan="9" class="text-center"><b>GRAND TOTAL</b></td>
                        <td class="text-right"><?= number_format($grand_total, 2) ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

        </div>
    <?php endif; ?>
</body>
</html>