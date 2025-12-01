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
        
        .bold {
            font-weight: bold;
        }
        
        .ttd-section {
            width: 100%;
        }
        
        .ttd-box {
            float: left;
            margin-right: 85px;
            text-align: center;
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
                                Rincian Pembayaran Biaya Gaji Kopek Udang Kulit Rebus
                            </u>
                            <br>
                        </h2>
                    </td>
                </tr>
            </table>

            <table style="margin-bottom: 15px;">
                <tr>
                    <td>A/n</td>
                    <td>:</td>
                    <td><b><?= strtoupper($vendor['name'] ?? 'MARTABE') ?></b></td>
                </tr>
                <tr>
                    <td>Di</td>
                    <td>:</td>
                    <td>Psr 8</td>
                </tr>
            </table>

            <table class="table">
                <thead style="text-align: center; font-weight:bold;">
                    <tr>
                        <th class="text-center">Tgl Kembali</th>
                        <th class="text-center">Tgl Baham</th>
                        <th class="text-center">Size Mth</th>
                        <th class="text-center">Kg. Rebus</th>
                        <th class="text-center">Kg Daging Martabe</th>
                        <th class="text-center">Kg Daging Cn</th>
                        <th class="text-center">Uang Kopek Yang Dibayar</th>
                        <th class="text-center">Ratio</th>
                        <th class="text-center">Tb. Harga</th>
                        <th class="text-center">Grand Total</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $grand_total_all = 0;
                    $total_kg_rebus = 0;
                    $total_kg_daging_martabe = 0;
                    $total_kg_daging_cn = 0;
                    $total_uang_kopek = 0;
                    
                    // Group data by tanggal untuk membuat struktur seperti screenshot
                    $grouped_data = [];
                    foreach ($biayaUdangDetail as $id => $parentBlock) {
                        $p = $parentBlock['parent'];
                        $tgl_kembali = $p['tanggal_masuk'] ?? '';
                        
                        // Cari tanggal baham dari detail
                        $tgl_baham = '';
                        if (!empty($parentBlock['detail'][0]['tanggal_keluar'])) {
                            $tgl_baham = $parentBlock['detail'][0]['tanggal_keluar'];
                        }
                        
                        $key = $tgl_kembali;
                        if (!isset($grouped_data[$key])) {
                            $grouped_data[$key] = [
                                'tgl_kembali' => $tgl_kembali,
                                'tgl_baham' => $tgl_baham,
                                'items' => [],
                                'totals' => [
                                    'kg_rebus' => 0,
                                    'kg_daging_martabe' => 0,
                                    'kg_daging_cn' => 0,
                                    'uang_kopek' => 0,
                                    'grand_total' => 0
                                ]
                            ];
                        }
                        $grouped_data[$key]['items'][] = $parentBlock;
                        
                        // Hitung total untuk group ini
                        $kg_rebus = $p['sum_keluar'] ?? 0;
                        $kg_daging_martabe = $p['sum_kotor'] ?? 0;
                        $kg_daging_cn = $p['sum_bersih'] ?? 0;
                        $total_harga = ($p['sum_bersih'] ?? 0) * ($p['harga_per_kilo'] ?? 0);
                        
                        $grouped_data[$key]['totals']['kg_rebus'] += $kg_rebus;
                        $grouped_data[$key]['totals']['kg_daging_martabe'] += $kg_daging_martabe;
                        $grouped_data[$key]['totals']['kg_daging_cn'] += $kg_daging_cn;
                        $grouped_data[$key]['totals']['uang_kopek'] += $kg_daging_cn; // Sama dengan Kg Daging Cn
                        $grouped_data[$key]['totals']['grand_total'] += $total_harga;
                    }
                    ?>

                    <?php foreach ($grouped_data as $group) : ?>
                        <?php 
                        $first_item = true;
                        $group_rowspan = count($group['items']);
                        ?>

                        <?php foreach ($group['items'] as $index => $parentBlock) : ?>
                            <?php
                            $p = $parentBlock['parent'];
                            
                            // Hitung nilai-nilai sesuai kebutuhan
                            $kg_rebus = $p['sum_keluar'] ?? 0;
                            $kg_daging_martabe = $p['sum_kotor'] ?? 0;
                            $kg_daging_cn = $p['sum_bersih'] ?? 0;
                            $uang_kopek_dibayar = $p['sum_bersih'] ?? 0; // Sesuai screenshot, sama dengan Kg Daging Cn
                            $ratio = $p['ratio'] ?? 0;
                            $harga_per_kilo = $p['harga_per_kilo'] ?? 0;
                            $total_harga = ($p['sum_bersih'] ?? 0) * $harga_per_kilo;
                            
                            // Akumulasi grand total
                            $grand_total_all += $total_harga;
                            $total_kg_rebus += $kg_rebus;
                            $total_kg_daging_martabe += $kg_daging_martabe;
                            $total_kg_daging_cn += $kg_daging_cn;
                            $total_uang_kopek += $uang_kopek_dibayar;
                            ?>

                            <tr>
                                <!-- Tgl Kembali -->
                                <?php if ($first_item) : ?>
                                    <td rowspan="<?= $group_rowspan ?>" class="text-center vertical-middle">
                                        <?= !empty($group['tgl_kembali']) ? date('d-M-y', strtotime($group['tgl_kembali'])) : '-' ?>
                                    </td>
                                <?php endif; ?>

                                <!-- Tgl Baham -->
                                <?php if ($first_item) : ?>
                                    <td rowspan="<?= $group_rowspan ?>" class="text-center vertical-middle">
                                        <?= !empty($group['tgl_baham']) ? date('d-M-y', strtotime($group['tgl_baham'])) : '-' ?>
                                    </td>
                                <?php endif; ?>

                                <!-- Size Mth -->
                                <td class="text-center vertical-middle">
                                    <?= $p['spesifikasi'] ?? '-' ?>
                                </td>

                                <!-- Kg. Rebus -->
                                <td class="text-right vertical-middle">
                                    <?= number_format($kg_rebus, 1) ?>
                                </td>

                                <!-- Kg Daging Martabe -->
                                <td class="text-right vertical-middle">
                                    <?= number_format($kg_daging_martabe, 1) ?>
                                </td>

                                <!-- Kg Daging Cn -->
                                <td class="text-right vertical-middle">
                                    <?= number_format($kg_daging_cn, 1) ?>
                                </td>

                                <!-- Uang Kopek Yang Dibayar -->
                                <td class="text-right vertical-middle">
                                    <?= number_format($uang_kopek_dibayar, 1) ?>
                                </td>

                                <!-- Ratio -->
                                <td class="text-center vertical-middle">
                                    <?= number_format($ratio, 1) ?>%
                                </td>

                                <!-- Tb. Harga -->
                                <td class="text-right vertical-middle">
                                    <?= number_format($harga_per_kilo, 0) ?>
                                </td>

                                <!-- Grand Total -->
                                <td class="text-right vertical-middle">
                                    <?= number_format($total_harga, 0) ?>
                                </td>
                            </tr>

                            <?php $first_item = false; ?>
                        <?php endforeach; ?>

                        <!-- ROW TOTAL PER GROUP -->
                        <tr style="font-weight: bold; background-color: #f0f0f0;">
                            <td colspan="3" class="text-center bold">TOTAL</td>
                            <td class="text-right bold"><?= number_format($group['totals']['kg_rebus'], 1) ?></td>
                            <td class="text-right bold"><?= number_format($group['totals']['kg_daging_martabe'], 1) ?></td>
                            <td class="text-right bold"><?= number_format($group['totals']['kg_daging_cn'], 1) ?></td>
                            <td class="text-right bold"><?= number_format($group['totals']['uang_kopek'], 1) ?></td>
                            <td></td>
                            <td></td>
                            <td class="text-right bold"><?= number_format($group['totals']['grand_total'], 0) ?></td>
                        </tr>

                    <?php endforeach; ?>

                    <!-- GRAND TOTAL SEMUA -->
                    <tr style="font-weight: bold; background-color: #d0d0d0;">
                        <td colspan="3" class="text-center bold">GRAND TOTAL</td>
                        <td class="text-right bold"><?= number_format($total_kg_rebus, 1) ?></td>
                        <td class="text-right bold"><?= number_format($total_kg_daging_martabe, 1) ?></td>
                        <td class="text-right bold"><?= number_format($total_kg_daging_cn, 1) ?></td>
                        <td class="text-right bold"><?= number_format($total_uang_kopek, 1) ?></td>
                        <td></td>
                        <td></td>
                        <td class="text-right bold"><?= number_format($grand_total_all, 0) ?></td>
                    </tr>
                </tbody>
            </table>

            <!-- BAGIAN TANDA TANGAN -->
            <?php 
            setlocale(LC_TIME, 'id_ID.utf8');
            ?>
            <i>Medan, <?= strftime('%d %B %Y') ?></i>

            <div class="ttd-section">
                <div class="ttd-box">
                    <p>Disetujui oleh,</p>
                    <br><br><br><br>
                    <p>_________________________</p>
                    <p>Yanti</p>
                </div>

                <div class="ttd-box">
                    <p>Disetujui oleh,</p>
                    <br><br><br><br>
                    <p>_________________________</p>
                    <p>Audit</p>
                </div>

                <div class="ttd-box">
                    <p>Disetujui oleh,</p>
                    <br><br><br><br>
                    <p>_________________________</p>
                    <p>Bp. Herman</p>
                </div>
                
                <div class="ttd-box">
                    <p>Dibuat oleh,</p>
                    <br><br><br><br>
                    <p>_________________________</p>
                    <p>Bp. Tony Siaputra</p>
                </div>
            </div>

        </div>
    <?php endif; ?>
</body>
</html>