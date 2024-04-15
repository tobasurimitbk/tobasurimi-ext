<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jasa Vendor Barang Masuk</title>
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
    <?php if (!empty($biayaUdang)) : ?>
        <div class="body">
            <table style="width: 100%;">
                <tr>
                    <td style="text-align: center;">
                        <h2>
                            <u>
                                Rincan Pembayaran Biaya Gaji Kopek Udang / Cumi Kulit Rebus
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
                    <td><?= $vendor == null ? "-" : strtoupper($vendor['name']) ?></td>
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

            <table class="table" style="margin-top: 30px;">
                <thead style="text-align: center; font-weight:bold;">
                    <tr>
                        <th style="text-align: center;" colspan="4"></th>
                        <th style="text-align: center;" colspan="1">Size</th>
                        <th style="text-align: center;" colspan="3"></th>
                        <th style="text-align: center;" colspan="4">Upah Kopek Yang Dibayar</th>
                    </tr>
                    <tr>
                        <th style="text-align: center;">No</th>
                        <th style="text-align: center;">Tanggal Masuk</th>
                        <th style="text-align: center;">Tanggal Keluar</th>
                        <th style="text-align: center;">Jenis</th>


                        <th style="text-align: center;">Mentah</th>

                        <th style="text-align: center;">KG REBUS</th>
                        <th style="text-align: center;">KG DAGING FAUZY</th>
                        <th style="text-align: center;">KG DAGING CN</th>

                        <th style="text-align: center;">Kg Daging</th>
                        <th style="text-align: center;">Ratio</th>
                        <th style="text-align: center;">TB Harga</th>
                        <th style="text-align: center;">Total Harga</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $tb_harga_sum = 0;
                    $kg_rebus_sum = 0;
                    $kg_fauzy_sum = 0;
                    $kg_cn_sum = 0;
                    $kg_daging_sum = 0;
                    $total_harga = 0;
                    $i = 0;
                    ?>

                    <?php foreach ($biayaUdangDetail as $index => $b) : ?>
                        <?php
                        $barang_master_id_last = 0;
                        $tb_harga_last = 0;

                        if ($index == 0) {
                            $barang_master_id_last = $b['barang_master_id'];
                            $tb_harga_last = $b['tb_harga'];
                        } else {
                            $barang_master_id_last = $biayaUdangDetail[$index - 1]['barang_master_id'];
                            $tb_harga_last = $biayaUdangDetail[$index - 1]['tb_harga'];
                        }

                        if ($tb_harga_last != $b['tb_harga']) {
                            $tb_harga_sum += floatval($b['tb_harga']);
                        }

                        // SUM
                        $kg_rebus_sum += floatval($b['qty_rebus']);
                        $kg_fauzy_sum += floatval($b['kg_fauzy']);
                        $kg_cn_sum += floatval($b['kg_cn']);
                        $kg_daging_sum += floatval($b['kg_daging']);
                        ?>

                        <?php if ($barang_master_id_last != $b['barang_master_id']) : ?>
                            <?php
                            $totalFirst = null;
                            foreach ($biayaUdangTotal as $t) :
                                if ($barang_master_id_last == $t['barang_master_id']) {
                                    $totalFirst = $t;
                                }
                            endforeach;
                            $total_harga += $totalFirst['total_harga'];

                            ?>
                            <tr style="text-align: center;">
                                <td style="text-align: center;" colspan="5">
                                    <b>SUB TOTAL</b>
                                </td>
                                <td><?= $totalFirst['kg_rebus_total'] ?></td>
                                <td><?= $totalFirst['kg_fauzy_total'] ?></td>
                                <td><?= $totalFirst['kg_cn_total'] ?></td>
                                <td><?= $totalFirst['kg_daging_total'] ?></td>
                                <td><?= number_format($totalFirst['ratio'], 2) ?></td>
                                <td><?= number_format($tb_harga_last, 2) ?></td>
                                <td><?= number_format($totalFirst['total_harga'], 2) ?></td>
                            </tr>
                        <?php endif; ?>

                        <tr style="text-align: center;">
                            <td><?= ++$i ?></td>
                            <td><?= $b['tanggal_masuk'] ?></td>
                            <td><?= $b['tanggal_keluar'] ?></td>
                            <td><?= $b['barang_name'] ?></td>
                            <td><?= $b['spesifikasi'] ?></td>
                            <td><?= $b['qty_rebus'] ?></td>
                            <td><?= $b['kg_fauzy'] ?></td>
                            <td><?= $b['kg_cn'] ?></td>
                            <td><?= $b['kg_daging'] ?></td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>

                        <?php if ($index == count($biayaUdangDetail) - 1) : ?>
                            <?php
                            $totalFirst = null;
                            foreach ($biayaUdangTotal as $t) :
                                if ($b['barang_master_id'] == $t['barang_master_id']) {
                                    $totalFirst = $t;
                                }
                            endforeach;
                            $total_harga += $totalFirst['total_harga'];

                            ?>
                            <tr style="text-align: center;">
                                <td style="text-align: center;" colspan="5">
                                    <b>SUB TOTAL</b>
                                </td>
                                <td><?= $totalFirst['kg_rebus_total'] ?></td>
                                <td><?= $totalFirst['kg_fauzy_total'] ?></td>
                                <td><?= $totalFirst['kg_cn_total'] ?></td>
                                <td><?= $totalFirst['kg_daging_total'] ?></td>
                                <td><?= number_format($totalFirst['ratio'], 2) ?></td>
                                <td><?= number_format($tb_harga_last, 2) ?></td>
                                <td><?= number_format($totalFirst['total_harga'], 2) ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <tr style="text-align: center;">
                        <td style="text-align: center;" colspan="5"><b>GRAND TOTAL</b></td>
                        <td><?= $kg_rebus_sum ?></td>
                        <td><?= $kg_fauzy_sum ?></td>
                        <td><?= $kg_cn_sum ?></td>
                        <td><?= $kg_daging_sum ?></td>
                        <td>-</td>
                        <td><?= number_format($tb_harga_sum, 2) ?></td>
                        <td><?= number_format($total_harga, 2) ?></td>
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