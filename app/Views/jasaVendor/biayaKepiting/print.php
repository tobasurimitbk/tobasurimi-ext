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
                                Rincan Pembayaran Biaya Gaji Kopek Kepiting Kukus
                            </u>
                            <br>
                        </h2>
                        <h4 style="margin-top: -10px;">
                            NO : <?= $biayaKepiting['no_pembayaran'] ?>
                        </h4>
                    </td>

                </tr>
            </table>

            <table>
                <tr>
                    <td>NAMA VENDOR</td>
                    <td>:</td>
                    <td><?= $vendor == null ? "-" : strtoupper($vendor['name']) ?></td>
                </tr>
                <tr>
                    <td>TANGGAL</td>
                    <td>:</td>
                    <td><?= date('d/m/Y', strtotime($biayaKepiting['tanggal'])) ?></td>
                </tr>
                <tr>
                    <td>KETERANGAN</td>
                    <td>:</td>
                    <td><?= $biayaKepiting['keterangan'] ?></td>
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
                        <th style="text-align: center;">Barang</th>


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
                    <?php $no = 1; ?>
                    <?php
                    $jumboTotal = 0;
                    $exLumpTotal = 0;
                    $lumpTotal = 0;
                    $specialTotal = 0;
                    $clawTotal = 0;
                    $mhTotal = 0;
                    $cfTotal = 0;
                    $totalTotal = 0;
                    // Upah Kopek
                    $upahKopekJumbo = 0;
                    $upahKopekExLump = 0;
                    $upahKopekLump = 0;
                    $upahKopekSpecial = 0;
                    $upahKopekClaw = 0;
                    $upahKopekMh = 0;
                    $upahKopekCf = 0;
                    // komisi
                    $komisiDagingJumbo = 0;
                    $komisiDagingExLump = 0;
                    $komisiDagingLump = 0;
                    $komisiDagingSpecial = 0;
                    $komisiDagingClaw = 0;
                    $komisiDagingMh = 0;
                    $komisiDagingCf = 0;
                    // bonus
                    $bonusDagingJumbo = 0;
                    $bonusDagingExLump = 0;
                    $bonusDagingLump = 0;
                    $bonusDagingSpecial = 0;
                    $bonusDagingClaw = 0;
                    $bonusDagingMh = 0;
                    $bonusDagingCf = 0;
                    // tambahan
                    $tambahanJumbo = 0;
                    $tambahanExLump = 0;
                    $tambahanLump = 0;
                    $tambahanSpecial = 0;
                    $tambahanClaw = 0;
                    $tambahanMh = 0;
                    $tambahanCf = 0;
                    // Qty Kopek
                    $qtySebelumKopekTotal = 0;
                    ?>
                    <?php foreach ($biayaKepitingDetail as $index => $b) : ?>
                        <?php
                        $total = $b['jumbo'] + $b['ex_lump'] + $b['lump'] + $b['special'] + $b['claw'] + $b['mh'] + $b['cf'];
                        $rasio = $total == 0 ? 0 : (($total / $b['qty_kopek']) * 100);
                        $jumboTotal += $b['jumbo'];
                        $exLumpTotal += $b['ex_lump'];
                        $lumpTotal += $b['lump'];
                        $specialTotal += $b['special'];
                        $clawTotal += $b['claw'];
                        $mhTotal += $b['mh'];
                        $cfTotal += $b['cf'];
                        $qtySebelumKopekTotal += $b['qty_kopek'];
                        $totalTotal += $total;
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $b['tanggal_masuk'] ?></td>
                            <td><?= $b['nama_barang'] ?></td>
                            <td><?= number_format($b['qty_kopek'], 2) ?></td>
                            <td><?= number_format($rasio, 2) . " %" ?></td>
                            <td><?= $b['jumbo'] ?></td>
                            <td><?= $b['ex_lump'] ?></td>
                            <td><?= $b['lump'] ?></td>
                            <td><?= $b['special'] ?></td>
                            <td><?= $b['claw'] ?></td>
                            <td><?= $b['mh'] ?></td>
                            <td><?= $b['cf'] ?></td>
                            <td><?= $total ?></td>
                        </tr>

                    <?php endforeach; ?>
                    <?php $qtySebelumKopekTotal = ($qtySebelumKopekTotal == 0) ? 1 : $qtySebelumKopekTotal;  ?>
                    <?php $qtyTotalBahanBaku = $jumboTotal + $exLumpTotal + $lumpTotal + $specialTotal + $clawTotal + $mhTotal + $cfTotal; ?>
                    <tr>
                        <td colspan="3">
                            Rata Rata Rasio
                        </td>
                        <td>
                            <?= number_format($qtySebelumKopekTotal, 2) ?>
                        </td>
                        <td>
                            <?= number_format(($qtyTotalBahanBaku / $qtySebelumKopekTotal) * 100, 2) ?> %
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            Total Kg di B. baku
                        </td>
                        <td><?= $jumboTotal ?></td>
                        <td><?= $exLumpTotal ?></td>
                        <td><?= $lumpTotal ?></td>
                        <td><?= $specialTotal ?></td>
                        <td><?= $clawTotal ?></td>
                        <td><?= $mhTotal ?></td>
                        <td><?= $cfTotal ?></td>
                        <td><?= ($jumboTotal + $exLumpTotal + $lumpTotal + $specialTotal + $clawTotal + $mhTotal + $cfTotal) ?></td>
                    </tr>
                    <?php $totalPerolehanGaji = 0; ?>
                    <?php foreach ($dataPerolehanGaji as $d) : ?>
                        <?php
                        $total = $d['jumbo'] + $d['ex_lump'] + $d['lump'] + $d['special'] + $d['claw'] + $d['mh'] + $d['cf'];
                        $totalPerolehanGaji += $total;

                        if ($d['description'] == 'Upah Kopek') {
                            $upahKopekJumbo += $d['jumbo'];
                            $upahKopekExLump += $d['ex_lump'];
                            $upahKopekLump += $d['lump'];
                            $upahKopekSpecial += $d['special'];
                            $upahKopekClaw += $d['claw'];
                            $upahKopekMh += $d['mh'];
                            $upahKopekCf += $d['cf'];
                        }

                        if ($d['description'] == 'Komisi / Kg Daging') {
                            $komisiDagingJumbo += $d['jumbo'];
                            $komisiDagingExLump += $d['ex_lump'];
                            $komisiDagingLump += $d['lump'];
                            $komisiDagingSpecial += $d['special'];
                            $komisiDagingClaw += $d['claw'];
                            $komisiDagingMh += $d['mh'];
                            $komisiDagingCf += $d['cf'];
                        }

                        if ($d['description'] == 'Bonus / Kg Daging') {
                            $bonusDagingJumbo += $d['jumbo'];
                            $bonusDagingExLump += $d['ex_lump'];
                            $bonusDagingLump += $d['lump'];
                            $bonusDagingSpecial += $d['special'];
                            $bonusDagingClaw += $d['claw'];
                            $bonusDagingMh += $d['mh'];
                            $bonusDagingCf += $d['cf'];
                        }

                        if ($d['value'] == 'tamb_upah_kopek') {
                            $tambahanJumbo += $d['jumbo'];
                            $tambahanExLump += $d['ex_lump'];
                            $tambahanLump += $d['lump'];
                            $tambahanSpecial += $d['special'];
                            $tambahanClaw += $d['claw'];
                            $tambahanMh += $d['mh'];
                            $tambahanCf += $d['cf'];
                        }

                        ?>
                        <tr>
                            <td colspan="5"><?= $d['description'] ?></td>
                            <td><?= number_format($d['jumbo'], 2) ?></td>
                            <td><?= number_format($d['ex_lump'], 2) ?></td>
                            <td><?= number_format($d['lump'], 2) ?></td>
                            <td><?= number_format($d['special'], 2) ?></td>
                            <td><?= number_format($d['claw'], 2) ?></td>
                            <td><?= number_format($d['mh'], 2) ?></td>
                            <td><?= number_format($d['cf'], 2) ?></td>
                            <td><?= number_format($total, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php
                    $totalTotal = ($totalTotal == 0) ? 1 : $totalTotal;
                    // upah kopek jumbo
                    $totalUpahKopekJumbo = $jumboTotal * $upahKopekJumbo;
                    $totalUpahKopekExLump = $exLumpTotal * $upahKopekExLump;
                    $totalUpahKopekLump = $lumpTotal * $upahKopekLump;
                    $totalUpahKopekSpecial = $specialTotal * $upahKopekSpecial;
                    $totalUpahKopekClaw = $clawTotal * $upahKopekClaw;
                    $totalUpahKopekMh = $mhTotal * $upahKopekMh;
                    $totalUpahKopekCf = $cfTotal * $upahKopekCf;
                    $totalUpahKopek = $totalUpahKopekJumbo + $totalUpahKopekExLump + $totalUpahKopekLump + $totalUpahKopekSpecial + $totalUpahKopekClaw + $totalUpahKopekMh + $totalUpahKopekCf;
                    // komisi
                    $totalKomisiJumbo = $jumboTotal * $komisiDagingJumbo;
                    $totalKomisiExLump = $exLumpTotal * $komisiDagingExLump;
                    $totalKomisiLump = $lumpTotal * $komisiDagingLump;
                    $totalKomisiSpecial = $specialTotal * $komisiDagingSpecial;
                    $totalKomisiClaw = $clawTotal * $komisiDagingClaw;
                    $totalKomisiMh = $mhTotal * $komisiDagingMh;
                    $totalKomisiCf = $cfTotal * $komisiDagingCf;
                    $totalKomisi = $totalKomisiJumbo + $totalKomisiExLump + $totalKomisiLump + $totalKomisiSpecial + $totalKomisiClaw + $totalKomisiMh + $totalKomisiCf;
                    // bonus
                    $totalBonusJumbo = $jumboTotal * $bonusDagingJumbo;
                    $totalBonusExLump = $exLumpTotal * $bonusDagingExLump;
                    $totalBonusLump = $lumpTotal * $bonusDagingLump;
                    $totalBonusSpecial = $specialTotal * $bonusDagingSpecial;
                    $totalBonusClaw = $clawTotal * $bonusDagingClaw;
                    $totalBonusMh = $mhTotal * $bonusDagingMh;
                    $totalBonusCf = $cfTotal * $bonusDagingCf;
                    $totalBonus = $totalBonusJumbo + $totalBonusExLump + $totalBonusLump + $totalBonusSpecial + $totalBonusClaw + $totalBonusMh + $totalBonusCf;
                    // tambahan
                    $totalTambahanJumbo = $jumboTotal * $tambahanJumbo;
                    $totalTambahanExLump = $exLumpTotal * $tambahanExLump;
                    $totalTambahanLump = $lumpTotal * $tambahanLump;
                    $totalTambahanSpecial = $specialTotal * $tambahanSpecial;
                    $totalTambahanClaw = $clawTotal * $tambahanClaw;
                    $totalTambahanMh = $mhTotal * $tambahanMh;
                    $totalTambahanCf = $cfTotal * $tambahanCf;
                    $totalTambahan = $totalTambahanJumbo + $totalTambahanExLump + $totalTambahanLump + $totalTambahanSpecial + $totalTambahanClaw + $totalTambahanMh + $totalTambahanCf;
                    // presentase kopek
                    $presentaseJumbo = $jumboTotal != 0 ? ($jumboTotal * 100 / $totalTotal) : 0;
                    $presentaseExLump = $exLumpTotal != 0 ? ($exLumpTotal * 100 / $totalTotal) : 0;
                    $presentaseLump = $lumpTotal != 0 ? ($lumpTotal * 100 / $totalTotal) : 0;
                    $presentaseSpecial = $specialTotal != 0 ? ($specialTotal * 100 / $totalTotal) : 0;
                    $presentaseClaw = $clawTotal != 0 ? ($clawTotal * 100 / $totalTotal) : 0;
                    $presentaseMh = $mhTotal != 0 ? ($mhTotal * 100 / $totalTotal) : 0;
                    $presentaseCf = $cfTotal != 0 ? ($cfTotal * 100 / $totalTotal) : 0;
                    $totalPresentase = $presentaseJumbo + $presentaseExLump + $presentaseLump + $presentaseSpecial + $presentaseClaw + $presentaseMh + $presentaseCf;
                    // grand total upah kopek
                    $grandTotalUpahKopek = $totalUpahKopek + $totalKomisi + $totalBonus + $totalTambahan;

                    ?>
                    <tr>
                        <td colspan="13">-------</td>
                    </tr>
                    <tr>
                        <td colspan="5">Total Upah Kopek / Kg</td>
                        <td><?= number_format($totalUpahKopekJumbo, 2) ?></td>
                        <td><?= number_format($totalUpahKopekExLump, 2) ?></td>
                        <td><?= number_format($totalUpahKopekLump, 2) ?></td>
                        <td><?= number_format($totalUpahKopekSpecial, 2) ?></td>
                        <td><?= number_format($totalUpahKopekClaw, 2) ?></td>
                        <td><?= number_format($totalUpahKopekMh, 2) ?></td>
                        <td><?= number_format($totalUpahKopekCf, 2) ?></td>
                        <td><?= number_format($totalUpahKopek, 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="5">Total Komisi / Kg</td>
                        <td><?= number_format($totalKomisiJumbo, 2) ?></td>
                        <td><?= number_format($totalKomisiExLump, 2) ?></td>
                        <td><?= number_format($totalKomisiLump, 2) ?></td>
                        <td><?= number_format($totalKomisiSpecial, 2) ?></td>
                        <td><?= number_format($totalKomisiClaw, 2) ?></td>
                        <td><?= number_format($totalKomisiMh, 2) ?></td>
                        <td><?= number_format($totalKomisiCf, 2) ?></td>
                        <td><?= number_format($totalKomisi, 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="5">Total Bonus / Kg</td>
                        <td><?= number_format($totalBonusJumbo, 2) ?></td>
                        <td><?= number_format($totalBonusExLump, 2) ?></td>
                        <td><?= number_format($totalBonusLump, 2) ?></td>
                        <td><?= number_format($totalBonusSpecial, 2) ?></td>
                        <td><?= number_format($totalBonusClaw, 2) ?></td>
                        <td><?= number_format($totalBonusMh, 2) ?></td>
                        <td><?= number_format($totalBonusCf, 2) ?></td>
                        <td><?= number_format($totalBonus, 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="5">Total Tambahan</td>
                        <td><?= number_format($totalTambahanJumbo, 2) ?></td>
                        <td><?= number_format($totalTambahanExLump, 2) ?></td>
                        <td><?= number_format($totalTambahanLump, 2) ?></td>
                        <td><?= number_format($totalTambahanSpecial, 2) ?></td>
                        <td><?= number_format($totalTambahanClaw, 2) ?></td>
                        <td><?= number_format($totalTambahanMh, 2) ?></td>
                        <td><?= number_format($totalTambahanCf, 2) ?></td>
                        <td><?= number_format($totalTambahan, 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="5">Presentase Kopek</td>
                        <td><?= number_format($presentaseJumbo, 2) ?> %</td>
                        <td><?= number_format($presentaseExLump, 2) ?> %</td>
                        <td><?= number_format($presentaseLump, 2) ?> %</td>
                        <td><?= number_format($presentaseSpecial, 2) ?> %</td>
                        <td><?= number_format($presentaseClaw, 2) ?> %</td>
                        <td><?= number_format($presentaseMh, 2) ?> %</td>
                        <td><?= number_format($presentaseCf, 2) ?> %</td>
                        <td><?= $totalPresentase ?> %</td>
                    </tr>
                    <tr>
                        <td colspan="12">
                            Grand Total Upah Kopek
                        </td>
                        <td><?= number_format($grandTotalUpahKopek, 2) ?></td>
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
            <br><br><br><br><br><br><br><br>
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
                            NO : <?= $biayaKepiting['no_pembayaran'] ?>
                        </h4>
                    </td>

                </tr>
            </table>
            <div class="body" style="margin-top: 10px;">
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th style="text-align: center;" colspan="6">Bonus Khusus Untuk <?= $vendor == null ? "-" : strtoupper($vendor['name']) ?></th>
                        </tr>
                        <tr>
                            <th style="text-align: center;width:10px">No</th>
                            <th style="text-align: center;">Tanggal Masuk</th>
                            <th style="text-align: center;">Barang </th>
                            <th style="text-align: center;">Kg</th>
                            <th style="text-align: center;">Bonus</th>
                            <th style="text-align: center;">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        $totalResult = 0; ?>
                        <?php foreach ($biayaKepitingBonus as $index => $b) : ?>

                            <tr>
                                <?php $total = 0 ?>
                                <?php $total = $b['kg_bonus'] * $b['bonus_nominal'];  ?>
                                <?php $totalResult += $total; ?>
                                <td><?= $no++ ?></td>
                                <td><?= $b['tanggal_masuk'] ?></td>
                                <td><?= $b['nama_barang'] ?></td>
                                <td><?= $b['kg_bonus'] ?></td>
                                <td><?= number_format($b['bonus_nominal'], 2) ?></td>
                                <td><?= number_format($total, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="5">Grand Total</td>
                            <td><?= number_format(($totalResult), 2) ?></td>
                        </tr>
                    </tbody>

                </table>
            </div>
        </div>
    <?php endif; ?>
</body>

</html>