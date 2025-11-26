<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biaya Kepiting</title>
    <style>
        body {
            font-size: 12px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        @page {
            size: 11.27in 10.50in landscape;
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
            font-size: 10px;
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

        .pagebreak {
            clear: both;
            page-break-before: always;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <?php if (!empty($biayaKepiting)) : ?>
        <div class="body">
            <table style="width: 100%;">
                <tr>
                    <td style="text-align: center;">
                        <h2>
                            <u>
                                <?php if ($biayaKepiting['jenis_barang'] === "kukus") : ?>
                                    Rincian Pembayaran Biaya Gaji Kopek Kepiting Kukus
                                <?php elseif ($biayaKepiting['jenis_barang'] === "buang_batok") : ?>
                                    Rincian Pembayaran Biaya Gaji Kopek Kepiting Buang Batok
                                <?php elseif ($biayaKepiting['jenis_barang'] === "utuh") : ?>
                                    Rincian Pembayaran Biaya Gaji Kopek Kepiting Utuh
                                <?php else : ?>
                                    Rincian Pembayaran Biaya Gaji Kopek Kepiting
                                <?php endif; ?>
                            </u>
                            <br>
                        </h2>
                        <h4 style="margin-top: -10px;">
                            NO : <?= !empty($biayaKepiting['no_pembayaran']) ? $biayaKepiting['no_pembayaran'] : '-' ?>
                        </h4>
                    </td>
                </tr>
            </table>

            <table>
                <tr>
                    <td>NAMA VENDOR</td>
                    <td>:</td>
                    <td><?= !empty($vendor['name']) ? strtoupper($vendor['name']) : "-" ?></td>
                </tr>
                <tr>
                    <td>TANGGAL</td>
                    <td>:</td>
                    <td><?= !empty($biayaKepiting['tanggal']) ? date('d/m/Y', strtotime($biayaKepiting['tanggal'])) : '-' ?></td>
                </tr>
                <tr>
                    <td>KETERANGAN</td>
                    <td>:</td>
                    <td><?= !empty($biayaKepiting['keterangan']) ? $biayaKepiting['keterangan'] : '-' ?></td>
                </tr>
            </table>

            <table class="table" style="margin-top: 15px;">
                <thead class="thead-dark">
                    <tr>
                        <th style="text-align: center;" colspan="3"></th>
                        <th style="text-align: center;" colspan="2">Kg Bahan Baku</th>
                        <th style="text-align: center;" colspan="8"></th>
                    </tr>
                    <tr>
                        <th style="text-align: center;">No</th>
                        <th style="text-align: center;">Tanggal Masuk</th>
                        <th style="text-align: center;">Supplier</th>
                        <th style="text-align: center;">Qty Sebelum Kopek</th>
                        <th style="text-align: center;">Rasio (%)</th>
                        <th style="text-align: center;">JUMBO</th>
                        <th style="text-align: center;">EX LUMP</th>
                        <th style="text-align: center;">LUMP</th>
                        <th style="text-align: center;">SPESIAL</th>
                        <th style="text-align: center;">CLAW</th>
                        <th style="text-align: center;">MH</th>
                        <th style="text-align: center;">CF</th>
                        <th style="text-align: center;">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    // Initialize all totals
                    $jumboTotal = 0;
                    $exLumpTotal = 0;
                    $lumpTotal = 0;
                    $specialTotal = 0;
                    $clawTotal = 0;
                    $mhTotal = 0;
                    $cfTotal = 0;
                    $totalTotal = 0;
                    $qtySebelumKopekTotal = 0;
                    
                    // Initialize arrays for different types
                    $upahKopek = ['jumbo' => 0, 'ex_lump' => 0, 'lump' => 0, 'special' => 0, 'claw' => 0, 'mh' => 0, 'cf' => 0];
                    $komisiDaging = ['jumbo' => 0, 'ex_lump' => 0, 'lump' => 0, 'special' => 0, 'claw' => 0, 'mh' => 0, 'cf' => 0];
                    $bonusDaging = ['jumbo' => 0, 'ex_lump' => 0, 'lump' => 0, 'special' => 0, 'claw' => 0, 'mh' => 0, 'cf' => 0];
                    $tambahan = ['jumbo' => 0, 'ex_lump' => 0, 'lump' => 0, 'special' => 0, 'claw' => 0, 'mh' => 0, 'cf' => 0];
                    ?>
                    
                    <?php if (!empty($biayaKepitingDetail)) : ?>
                        <?php foreach ($biayaKepitingDetail as $index => $b) : ?>
                            <?php
                            // Safely calculate values with null checks
                            $jumbo = (float)($b['jumbo'] ?? 0);
                            $ex_lump = (float)($b['ex_lump'] ?? 0);
                            $lump = (float)($b['lump'] ?? 0);
                            $special = (float)($b['special'] ?? 0);
                            $claw = (float)($b['claw'] ?? 0);
                            $mh = (float)($b['mh'] ?? 0);
                            $cf = (float)($b['cf'] ?? 0);
                            $qty_kopek = (float)($b['qty_kopek'] ?? 0);
                            
                            $total = $jumbo + $ex_lump + $lump + $special + $claw + $mh + $cf;
                            $rasio = $qty_kopek > 0 ? ($total / $qty_kopek) * 100 : 0;
                            
                            // Add to totals
                            $jumboTotal += $jumbo;
                            $exLumpTotal += $ex_lump;
                            $lumpTotal += $lump;
                            $specialTotal += $special;
                            $clawTotal += $claw;
                            $mhTotal += $mh;
                            $cfTotal += $cf;
                            $qtySebelumKopekTotal += $qty_kopek;
                            $totalTotal += $total;
                            ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= !empty($b['tanggal_masuk']) ? $b['tanggal_masuk'] : '-' ?></td>
                                <td><?= !empty($b['supplier_name']) ? $b['supplier_name'] : '-' ?></td>
                                <td class="text-right"><?= number_format($qty_kopek, 2) ?></td>
                                <td class="text-right"><?= number_format($rasio, 2) ?>%</td>
                                <td class="text-right"><?= number_format($jumbo, 3) ?></td>
                                <td class="text-right"><?= number_format($ex_lump, 3) ?></td>
                                <td class="text-right"><?= number_format($lump, 3) ?></td>
                                <td class="text-right"><?= number_format($special, 3) ?></td>
                                <td class="text-right"><?= number_format($claw, 3) ?></td>
                                <td class="text-right"><?= number_format($mh, 3) ?></td>
                                <td class="text-right"><?= number_format($cf, 3) ?></td>
                                <td class="text-right"><?= number_format($total, 3) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="13" class="text-center">Tidak ada data detail kepiting</td>
                        </tr>
                    <?php endif; ?>
                    
                    <?php 
                    // Calculate averages and totals
                    $qtyTotalBahanBaku = $jumboTotal + $exLumpTotal + $lumpTotal + $specialTotal + $clawTotal + $mhTotal + $cfTotal;
                    $rataRataRasio = $qtySebelumKopekTotal > 0 ? ($qtyTotalBahanBaku / $qtySebelumKopekTotal) * 100 : 0;
                    ?>
                    
                    <tr>
                        <td colspan="3">Rata Rata Rasio</td>
                        <td class="text-right"><?= number_format($qtySebelumKopekTotal, 2) ?></td>
                        <td class="text-right"><?= number_format($rataRataRasio, 2) ?>%</td>
                        <td colspan="8"></td>
                    </tr>
                    <tr>
                        <td colspan="5">Total Kg di B. baku</td>
                        <td class="text-right"><?= number_format($jumboTotal, 3) ?></td>
                        <td class="text-right"><?= number_format($exLumpTotal, 3) ?></td>
                        <td class="text-right"><?= number_format($lumpTotal, 3) ?></td>
                        <td class="text-right"><?= number_format($specialTotal, 3) ?></td>
                        <td class="text-right"><?= number_format($clawTotal, 3) ?></td>
                        <td class="text-right"><?= number_format($mhTotal, 3) ?></td>
                        <td class="text-right"><?= number_format($cfTotal, 3) ?></td>
                        <td class="text-right"><?= number_format($qtyTotalBahanBaku, 3) ?></td>
                    </tr>
                    
                    <?php 
                    $totalPerolehanGaji = 0;
                    if (!empty($dataPerolehanGaji)) : 
                        foreach ($dataPerolehanGaji as $d) :
                            $jumbo = (float)($d['jumbo'] ?? 0);
                            $ex_lump = (float)($d['ex_lump'] ?? 0);
                            $lump = (float)($d['lump'] ?? 0);
                            $special = (float)($d['special'] ?? 0);
                            $claw = (float)($d['claw'] ?? 0);
                            $mh = (float)($d['mh'] ?? 0);
                            $cf = (float)($d['cf'] ?? 0);
                            $total = $jumbo + $ex_lump + $lump + $special + $claw + $mh + $cf;
                            $totalPerolehanGaji += $total;

                            // Categorize based on description
                            $description = $d['description'] ?? '';
                            if ($description == 'Upah Kopek') {
                                $upahKopek['jumbo'] += $jumbo;
                                $upahKopek['ex_lump'] += $ex_lump;
                                $upahKopek['lump'] += $lump;
                                $upahKopek['special'] += $special;
                                $upahKopek['claw'] += $claw;
                                $upahKopek['mh'] += $mh;
                                $upahKopek['cf'] += $cf;
                            } elseif ($description == 'Komisi / Kg Daging') {
                                $komisiDaging['jumbo'] += $jumbo;
                                $komisiDaging['ex_lump'] += $ex_lump;
                                $komisiDaging['lump'] += $lump;
                                $komisiDaging['special'] += $special;
                                $komisiDaging['claw'] += $claw;
                                $komisiDaging['mh'] += $mh;
                                $komisiDaging['cf'] += $cf;
                            } elseif ($description == 'Bonus / Kg Daging') {
                                $bonusDaging['jumbo'] += $jumbo;
                                $bonusDaging['ex_lump'] += $ex_lump;
                                $bonusDaging['lump'] += $lump;
                                $bonusDaging['special'] += $special;
                                $bonusDaging['claw'] += $claw;
                                $bonusDaging['mh'] += $mh;
                                $bonusDaging['cf'] += $cf;
                            } elseif (($d['value'] ?? '') == 'tamb_upah_kopek') {
                                $tambahan['jumbo'] += $jumbo;
                                $tambahan['ex_lump'] += $ex_lump;
                                $tambahan['lump'] += $lump;
                                $tambahan['special'] += $special;
                                $tambahan['claw'] += $claw;
                                $tambahan['mh'] += $mh;
                                $tambahan['cf'] += $cf;
                            }
                    ?>
                            <tr>
                                <td colspan="5"><?= $description ?></td>
                                <td class="text-right"><?= number_format($jumbo, 2) ?></td>
                                <td class="text-right"><?= number_format($ex_lump, 2) ?></td>
                                <td class="text-right"><?= number_format($lump, 2) ?></td>
                                <td class="text-right"><?= number_format($special, 2) ?></td>
                                <td class="text-right"><?= number_format($claw, 2) ?></td>
                                <td class="text-right"><?= number_format($mh, 2) ?></td>
                                <td class="text-right"><?= number_format($cf, 2) ?></td>
                                <td class="text-right"><?= number_format($total, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="13" class="text-center">Tidak ada data perolehan gaji</td>
                        </tr>
                    <?php endif; ?>
                    
                    <?php
                    // Calculate final totals
                    $totalUpahKopekJumbo = $jumboTotal * $upahKopek['jumbo'];
                    $totalUpahKopekExLump = $exLumpTotal * $upahKopek['ex_lump'];
                    $totalUpahKopekLump = $lumpTotal * $upahKopek['lump'];
                    $totalUpahKopekSpecial = $specialTotal * $upahKopek['special'];
                    $totalUpahKopekClaw = $clawTotal * $upahKopek['claw'];
                    $totalUpahKopekMh = $mhTotal * $upahKopek['mh'];
                    $totalUpahKopekCf = $cfTotal * $upahKopek['cf'];
                    $totalUpahKopek = $totalUpahKopekJumbo + $totalUpahKopekExLump + $totalUpahKopekLump + $totalUpahKopekSpecial + $totalUpahKopekClaw + $totalUpahKopekMh + $totalUpahKopekCf;

                    $totalKomisiJumbo = $jumboTotal * $komisiDaging['jumbo'];
                    $totalKomisiExLump = $exLumpTotal * $komisiDaging['ex_lump'];
                    $totalKomisiLump = $lumpTotal * $komisiDaging['lump'];
                    $totalKomisiSpecial = $specialTotal * $komisiDaging['special'];
                    $totalKomisiClaw = $clawTotal * $komisiDaging['claw'];
                    $totalKomisiMh = $mhTotal * $komisiDaging['mh'];
                    $totalKomisiCf = $cfTotal * $komisiDaging['cf'];
                    $totalKomisi = $totalKomisiJumbo + $totalKomisiExLump + $totalKomisiLump + $totalKomisiSpecial + $totalKomisiClaw + $totalKomisiMh + $totalKomisiCf;

                    $totalBonusJumbo = $jumboTotal * $bonusDaging['jumbo'];
                    $totalBonusExLump = $exLumpTotal * $bonusDaging['ex_lump'];
                    $totalBonusLump = $lumpTotal * $bonusDaging['lump'];
                    $totalBonusSpecial = $specialTotal * $bonusDaging['special'];
                    $totalBonusClaw = $clawTotal * $bonusDaging['claw'];
                    $totalBonusMh = $mhTotal * $bonusDaging['mh'];
                    $totalBonusCf = $cfTotal * $bonusDaging['cf'];
                    $totalBonus = $totalBonusJumbo + $totalBonusExLump + $totalBonusLump + $totalBonusSpecial + $totalBonusClaw + $totalBonusMh + $totalBonusCf;

                    $totalTambahanJumbo = $jumboTotal * $tambahan['jumbo'];
                    $totalTambahanExLump = $exLumpTotal * $tambahan['ex_lump'];
                    $totalTambahanLump = $lumpTotal * $tambahan['lump'];
                    $totalTambahanSpecial = $specialTotal * $tambahan['special'];
                    $totalTambahanClaw = $clawTotal * $tambahan['claw'];
                    $totalTambahanMh = $mhTotal * $tambahan['mh'];
                    $totalTambahanCf = $cfTotal * $tambahan['cf'];
                    $totalTambahan = $totalTambahanJumbo + $totalTambahanExLump + $totalTambahanLump + $totalTambahanSpecial + $totalTambahanClaw + $totalTambahanMh + $totalTambahanCf;

                    // Calculate percentages
                    $presentaseJumbo = $totalTotal > 0 ? ($jumboTotal * 100 / $totalTotal) : 0;
                    $presentaseExLump = $totalTotal > 0 ? ($exLumpTotal * 100 / $totalTotal) : 0;
                    $presentaseLump = $totalTotal > 0 ? ($lumpTotal * 100 / $totalTotal) : 0;
                    $presentaseSpecial = $totalTotal > 0 ? ($specialTotal * 100 / $totalTotal) : 0;
                    $presentaseClaw = $totalTotal > 0 ? ($clawTotal * 100 / $totalTotal) : 0;
                    $presentaseMh = $totalTotal > 0 ? ($mhTotal * 100 / $totalTotal) : 0;
                    $presentaseCf = $totalTotal > 0 ? ($cfTotal * 100 / $totalTotal) : 0;
                    $totalPresentase = $presentaseJumbo + $presentaseExLump + $presentaseLump + $presentaseSpecial + $presentaseClaw + $presentaseMh + $presentaseCf;

                    $grandTotalUpahKopek = $totalUpahKopek + $totalKomisi + $totalBonus + $totalTambahan;
                    ?>
                    
                    <tr>
                        <td colspan="13">-------</td>
                    </tr>
                    <tr>
                        <td colspan="5">Total Upah Kopek / Kg</td>
                        <td class="text-right"><?= number_format($totalUpahKopekJumbo, 2) ?></td>
                        <td class="text-right"><?= number_format($totalUpahKopekExLump, 2) ?></td>
                        <td class="text-right"><?= number_format($totalUpahKopekLump, 2) ?></td>
                        <td class="text-right"><?= number_format($totalUpahKopekSpecial, 2) ?></td>
                        <td class="text-right"><?= number_format($totalUpahKopekClaw, 2) ?></td>
                        <td class="text-right"><?= number_format($totalUpahKopekMh, 2) ?></td>
                        <td class="text-right"><?= number_format($totalUpahKopekCf, 2) ?></td>
                        <td class="text-right"><?= number_format($totalUpahKopek, 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="5">Total Komisi / Kg</td>
                        <td class="text-right"><?= number_format($totalKomisiJumbo, 2) ?></td>
                        <td class="text-right"><?= number_format($totalKomisiExLump, 2) ?></td>
                        <td class="text-right"><?= number_format($totalKomisiLump, 2) ?></td>
                        <td class="text-right"><?= number_format($totalKomisiSpecial, 2) ?></td>
                        <td class="text-right"><?= number_format($totalKomisiClaw, 2) ?></td>
                        <td class="text-right"><?= number_format($totalKomisiMh, 2) ?></td>
                        <td class="text-right"><?= number_format($totalKomisiCf, 2) ?></td>
                        <td class="text-right"><?= number_format($totalKomisi, 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="5">Total Bonus / Kg</td>
                        <td class="text-right"><?= number_format($totalBonusJumbo, 2) ?></td>
                        <td class="text-right"><?= number_format($totalBonusExLump, 2) ?></td>
                        <td class="text-right"><?= number_format($totalBonusLump, 2) ?></td>
                        <td class="text-right"><?= number_format($totalBonusSpecial, 2) ?></td>
                        <td class="text-right"><?= number_format($totalBonusClaw, 2) ?></td>
                        <td class="text-right"><?= number_format($totalBonusMh, 2) ?></td>
                        <td class="text-right"><?= number_format($totalBonusCf, 2) ?></td>
                        <td class="text-right"><?= number_format($totalBonus, 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="5">Total Tambahan</td>
                        <td class="text-right"><?= number_format($totalTambahanJumbo, 2) ?></td>
                        <td class="text-right"><?= number_format($totalTambahanExLump, 2) ?></td>
                        <td class="text-right"><?= number_format($totalTambahanLump, 2) ?></td>
                        <td class="text-right"><?= number_format($totalTambahanSpecial, 2) ?></td>
                        <td class="text-right"><?= number_format($totalTambahanClaw, 2) ?></td>
                        <td class="text-right"><?= number_format($totalTambahanMh, 2) ?></td>
                        <td class="text-right"><?= number_format($totalTambahanCf, 2) ?></td>
                        <td class="text-right"><?= number_format($totalTambahan, 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="5">Presentase Kopek</td>
                        <td class="text-right"><?= number_format($presentaseJumbo, 2) ?>%</td>
                        <td class="text-right"><?= number_format($presentaseExLump, 2) ?>%</td>
                        <td class="text-right"><?= number_format($presentaseLump, 2) ?>%</td>
                        <td class="text-right"><?= number_format($presentaseSpecial, 2) ?>%</td>
                        <td class="text-right"><?= number_format($presentaseClaw, 2) ?>%</td>
                        <td class="text-right"><?= number_format($presentaseMh, 2) ?>%</td>
                        <td class="text-right"><?= number_format($presentaseCf, 2) ?>%</td>
                        <td class="text-right"><?= number_format($totalPresentase, 2) ?>%</td>
                    </tr>
                    <tr>
                        <td colspan="12">Grand Total Upah Kopek</td>
                        <td class="text-right"><?= number_format($grandTotalUpahKopek, 2) ?></td>
                    </tr>
                </tbody>
            </table>

            <br><br><br>
            <table style="width: 100%;margin-top:20px;">
                <tr>
                    <td style="text-align: center;">
                        <b>DIBUAT</b>
                    </td>
                    <td style="text-align: center;">
                        <b>DIPERIKSA</b>
                    </td>
                    <td style="text-align: center;">
                        <b>DISETUJUI</b>
                    </td>
                </tr>
            </table>
        </div>

        <div class="pagebreak">
            <table style="width: 100%;">
                <tr>
                    <td style="text-align: center;">
                        <h2>
                            <u>
                                Bonus Khusus Biaya Kepiting
                            </u>
                            <br>
                        </h2>
                        <h4 style="margin-top: -10px;">
                            NO : <?= !empty($biayaKepiting['no_pembayaran']) ? $biayaKepiting['no_pembayaran'] : '-' ?>
                        </h4>
                    </td>
                </tr>
            </table>
            <div class="body" style="margin-top: 10px;">
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th style="text-align: center;" colspan="6">Bonus Khusus Untuk <?= !empty($vendor['name']) ? strtoupper($vendor['name']) : "Vendor" ?></th>
                        </tr>
                        <tr>
                            <th style="text-align: center;width:10px">No</th>
                            <th style="text-align: center;">Tanggal Masuk</th>
                            <th style="text-align: center;">Barang</th>
                            <th style="text-align: center;">Kg</th>
                            <th style="text-align: center;">Bonus</th>
                            <th style="text-align: center;">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        $totalResult = 0; 
                        ?>
                        <?php if (!empty($biayaKepitingBonus)) : ?>
                            <?php foreach ($biayaKepitingBonus as $index => $b) : ?>
                                <?php 
                                $kg_bonus = (float)($b['kg_bonus'] ?? 0);
                                $bonus_nominal = (float)($b['bonus_nominal'] ?? 0);
                                $total = $kg_bonus * $bonus_nominal;
                                $totalResult += $total;
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= !empty($b['tanggal_masuk']) ? $b['tanggal_masuk'] : '-' ?></td>
                                    <td><?= !empty($b['nama_barang']) ? $b['nama_barang'] : '-' ?></td>
                                    <td class="text-right"><?= number_format($kg_bonus, 2) ?></td>
                                    <td class="text-right"><?= number_format($bonus_nominal, 2) ?></td>
                                    <td class="text-right"><?= number_format($total, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data bonus</td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="5">Grand Total</td>
                            <td class="text-right"><?= number_format($totalResult, 2) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else : ?>
        <div class="body">
            <h2 class="text-center">Data tidak ditemukan</h2>
        </div>
    <?php endif; ?>
</body>
</html>