<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $header; ?></title>
    <style>
        @page {
            margin: 15px 20px;
        }

        body {
            font-size: 10px;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
        }

        h2 {
            margin-bottom: 5px;
            text-align: center;
        }

        .info {
            margin-bottom: 10px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 8px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            word-wrap: break-word;
        }

        th {
            background-color: #f0f0f0;
        }

        .text-left {
            text-align: left;
        }

        .group-title {
            margin-top: 25px;
            margin-bottom: 3px;
            font-weight: bold;
            font-size: 11px;
        }

        .col-no {
            width: 25px;
        }

        .col-supplier {
            width: 110px;
        }

        .col-nopo {
            width: 70px;
        }

        .col-date {
            width: 60px;
        }

        .col-dept {
            width: 100px;
        }

        .col-gudang {
            width: 80px;
        }

        .col-qty,
        .col-satuan,
        .col-unit {
            width: 45px;
        }

        .col-group {
            width: 55px;
        }
    </style>
</head>

<body>
    <h2><?= $header; ?></h2>
    <div class="info">
        Tanggal:
        <?php if (!empty($tanggalAwal) && !empty($tanggalAkhir)) : ?>
            <?= $tanggalAwal; ?> s/d <?= $tanggalAkhir; ?>
        <?php else : ?>
            ALL
        <?php endif; ?>
    </div>

    <?php if (!empty($data)) : ?>
        <?php
        // --- Kelompokkan data berdasarkan barangName ---
        $groupedData = [];
        foreach ($data as $row) {
            $barangName = $row->barangName ?? "LAINNYA";

            $totalRow = floatval($row->nilai_total_umum ?? 0)
                + floatval($row->nilai_total_harian ?? 0)
                + floatval($row->nilai_total_bulanan ?? 0)
                + floatval($row->nilai_total_tambahan ?? 0);

            $groupedData[$barangName]['data'][] = [
                'supplierName' => $row->supplierName,
                'poNum' => $row->poNum,
                'poDate' => $row->poDate,
                'divisiName' => $row->divisiName,
                'warehouseName' => $row->warehouseName,
                'qtyPO' => $row->qtyPO,
                'satuanName' => $row->satuanName,
                'companyName' => $row->companyName,

                'dpp_umum' => $row->dpp_umum,
                'pph_umum' => $row->pph_umum,
                'nilai_total_umum' => $row->nilai_total_umum,

                'dpp_harian' => $row->dpp_harian,
                'pph_harian' => $row->pph_harian,
                'nilai_total_harian' => $row->nilai_total_harian,

                'dpp_bulanan' => $row->dpp_bulanan,
                'pph_bulanan' => $row->pph_bulanan,
                'nilai_total_bulanan' => $row->nilai_total_bulanan,

                'dpp_tambahan' => $row->dpp_tambahan,
                'pph_tambahan' => $row->pph_tambahan,
                'nilai_total_tambahan' => $row->nilai_total_tambahan,

                'totalRow' => $totalRow,
            ];

            // Summary per barang
            if (!isset($groupedData[$barangName]['summary'])) {
                $groupedData[$barangName]['summary'] = [
                    'totalQtyPO' => 0,
                    'dppUmum' => 0,
                    'pphUmum' => 0,
                    'totalUmum' => 0,
                    'dppHarian' => 0,
                    'pphHarian' => 0,
                    'totalHarian' => 0,
                    'dppBulanan' => 0,
                    'pphBulanan' => 0,
                    'totalBulanan' => 0,
                    'dppTambahan' => 0,
                    'pphTambahan' => 0,
                    'totalTambahan' => 0,
                    'totalRow' => 0,
                ];
            }

            $groupedData[$barangName]['summary']['totalQtyPO']     += floatval($row->qtyPO);
            $groupedData[$barangName]['summary']['dppUmum']        += floatval($row->dpp_umum);
            $groupedData[$barangName]['summary']['pphUmum']        += floatval($row->pph_umum);
            $groupedData[$barangName]['summary']['totalUmum']      += floatval($row->nilai_total_umum);

            $groupedData[$barangName]['summary']['dppHarian']      += floatval($row->dpp_harian);
            $groupedData[$barangName]['summary']['pphHarian']      += floatval($row->pph_harian);
            $groupedData[$barangName]['summary']['totalHarian']    += floatval($row->nilai_total_harian);

            $groupedData[$barangName]['summary']['dppBulanan']     += floatval($row->dpp_bulanan);
            $groupedData[$barangName]['summary']['pphBulanan']     += floatval($row->pph_bulanan);
            $groupedData[$barangName]['summary']['totalBulanan']   += floatval($row->nilai_total_bulanan);

            $groupedData[$barangName]['summary']['dppTambahan']    += floatval($row->dpp_tambahan);
            $groupedData[$barangName]['summary']['pphTambahan']    += floatval($row->pph_tambahan);
            $groupedData[$barangName]['summary']['totalTambahan']  += floatval($row->nilai_total_tambahan);

            $groupedData[$barangName]['summary']['totalRow']       += $totalRow;
        }
        ?>

        <?php foreach ($groupedData as $barangName => $group): ?>
            <div class="group-title">Bahan Baku: <?= $barangName; ?></div>

            <table>
                <thead>
                    <tr>
                        <th rowspan="2" class="col-no">No.</th>
                        <th rowspan="2" class="col-supplier">Supplier</th>
                        <th rowspan="2" class="col-nopo">No PO</th>
                        <th rowspan="2" class="col-date">Tgl PO</th>
                        <th rowspan="2" class="col-dept">Department</th>
                        <th rowspan="2" class="col-gudang">Gudang</th>
                        <th rowspan="2" class="col-qty">Qty</th>
                        <th rowspan="2" class="col-satuan">Satuan</th>
                        <th rowspan="2" class="col-unit">Unit</th>
                        <th colspan="3" class="col-group">Umum</th>
                        <th colspan="3" class="col-group">Harian</th>
                        <th colspan="3" class="col-group">Bulanan</th>
                        <th colspan="3" class="col-group">Tambahan</th>
                        <th rowspan="2" class="col-group">Total</th>
                    </tr>
                    <tr>
                        <th>DPP</th>
                        <th>PPh</th>
                        <th>Dibayarkan</th>
                        <th>DPP</th>
                        <th>PPh</th>
                        <th>Dibayarkan</th>
                        <th>DPP</th>
                        <th>PPh</th>
                        <th>Dibayarkan</th>
                        <th>DPP</th>
                        <th>PPh</th>
                        <th>Dibayarkan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($group['data'] as $row): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td class="text-left"><?= $row['supplierName']; ?></td>
                            <td><?= $row['poNum']; ?></td>
                            <td><?= $row['poDate']; ?></td>
                            <td class="text-left"><?= $row['divisiName']; ?></td>
                            <td class="text-left"><?= $row['warehouseName']; ?></td>
                            <td><?= number_format($row['qtyPO'], 2); ?></td>
                            <td><?= $row['satuanName']; ?></td>
                            <td><?= $row['companyName']; ?></td>

                            <td><?= number_format($row['dpp_umum'], 2); ?></td>
                            <td><?= number_format($row['pph_umum'], 2); ?></td>
                            <td><?= number_format($row['nilai_total_umum'], 2); ?></td>

                            <td><?= number_format($row['dpp_harian'], 2); ?></td>
                            <td><?= number_format($row['pph_harian'], 2); ?></td>
                            <td><?= number_format($row['nilai_total_harian'], 2); ?></td>

                            <td><?= number_format($row['dpp_bulanan'], 2); ?></td>
                            <td><?= number_format($row['pph_bulanan'], 2); ?></td>
                            <td><?= number_format($row['nilai_total_bulanan'], 2); ?></td>

                            <td><?= number_format($row['dpp_tambahan'], 2); ?></td>
                            <td><?= number_format($row['pph_tambahan'], 2); ?></td>
                            <td><?= number_format($row['nilai_total_tambahan'], 2); ?></td>

                            <td><?= number_format($row['totalRow'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <tr style="font-weight: bold; background-color: #eee;">
                        <td colspan="6">TOTAL <?= strtoupper($barangName); ?></td>
                        <td><?= number_format($group['summary']['totalQtyPO'], 2); ?></td>
                        <td colspan="2"></td>

                        <td><?= number_format($group['summary']['dppUmum'], 2); ?></td>
                        <td><?= number_format($group['summary']['pphUmum'], 2); ?></td>
                        <td><?= number_format($group['summary']['totalUmum'], 2); ?></td>

                        <td><?= number_format($group['summary']['dppHarian'], 2); ?></td>
                        <td><?= number_format($group['summary']['pphHarian'], 2); ?></td>
                        <td><?= number_format($group['summary']['totalHarian'], 2); ?></td>

                        <td><?= number_format($group['summary']['dppBulanan'], 2); ?></td>
                        <td><?= number_format($group['summary']['pphBulanan'], 2); ?></td>
                        <td><?= number_format($group['summary']['totalBulanan'], 2); ?></td>

                        <td><?= number_format($group['summary']['dppTambahan'], 2); ?></td>
                        <td><?= number_format($group['summary']['pphTambahan'], 2); ?></td>
                        <td><?= number_format($group['summary']['totalTambahan'], 2); ?></td>

                        <td><?= number_format($group['summary']['totalRow'], 2); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Tidak ada data yang tersedia.</p>
    <?php endif; ?>
</body>

</html>