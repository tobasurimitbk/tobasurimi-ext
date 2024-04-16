<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biaya Kepiting</title>
    <style>
        body {
            font-size: 13px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        @page {
            size: 12.00in 10.50in landscape;
            margin: 25px;
            padding: 25px;
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
            padding: 0.75rem;
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

            <table class="table" style="margin-top: 30px;">
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
                    ?>
                    <?php foreach ($biayaKepitingDetail as $index => $b) : ?>
                        <?php
                        $total = $b['jumbo'] + $b['ex_lump'] + $b['lump'] + $b['special'] + $b['claw'] + $b['mh'] + $b['cf'];
                        $jumboTotal += $b['jumbo'];
                        $exLumpTotal += $b['ex_lump'];
                        $lumpTotal += $b['lump'];
                        $specialTotal += $b['special'];
                        $clawTotal += $b['claw'];
                        $mhTotal += $b['mh'];
                        $cfTotal += $b['cf'];
                        $totalTotal += $total;
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $b['tanggal_masuk'] ?></td>
                            <td><?= $b['nama_barang'] ?></td>
                            <td><?= $b['qty_kopek'] ?></td>
                            <td>0</td>
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
                    <tr>
                        <td colspan="12">
                            Grand Total Upah Kopek
                        </td>
                        <td><?= $totalPerolehanGaji ?></td>
                    </tr>
                    <tr>
                        <td colspan="5">Presentase Kopek</td>
                        <td><?= number_format(($jumboTotal * 100) / $totalTotal, 2) ?> %</td>
                        <td><?= number_format(($exLumpTotal * 100) / $totalTotal, 2) ?> %</td>
                        <td><?= number_format(($lumpTotal * 100) / $totalTotal, 2) ?> %</td>
                        <td><?= number_format(($specialTotal * 100) / $totalTotal, 2) ?> %</td>
                        <td><?= number_format(($clawTotal * 100) / $totalTotal, 2) ?> %</td>
                        <td><?= number_format(($mhTotal * 100) / $totalTotal, 2) ?> %</td>
                        <td><?= number_format(($cfTotal * 100) / $totalTotal, 2) ?> %</td>
                        <td>100 %</td>
                    </tr>
                </tbody>
            </table>


            <br><br><br>
            <table style="width: 100%;margin-top:20px;">
                <tr>
                    <td style="text-align: center;">
                        <b>DIPERIKSA OLEH</b>
                    </td>
                    <td style="text-align: center;">
                        <b>DIKETAHUI OLEH</b>
                    </td>
                    <td style="text-align: center;">
                        <b>DITIMBANG OLEH</b>
                    </td>
                </tr>
            </table>
        </div>

    <?php endif; ?>
</body>

</html>