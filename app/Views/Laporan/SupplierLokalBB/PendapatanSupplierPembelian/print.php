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
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 12px;
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
            margin-top: 20px;
            margin-bottom: 3px;
            font-size: 10px;
        }

        .sub-group-title {
            font-weight: bold;
            font-size: 10px;
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
        <div>
            Tanggal:
            <?php if (!empty($tanggalAwal) && !empty($tanggalAkhir)) : ?>
                <?= $tanggalAwal; ?> s/d <?= $tanggalAkhir; ?>
            <?php else : ?>
                ALL
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($data)) : ?>
        <?php
        $nestedData = [];
        foreach ($data as $row) {
            $divisi = $row->divisiName ?? "LAINNYA";
            $gudang = $row->warehouseName ?? "LAINNYA";
            $barang = $row->barangName ?? "LAINNYA";

            $totalRow = floatval($row->nilai_total_umum ?? 0)
                + floatval($row->nilai_total_harian ?? 0)
                + floatval($row->nilai_total_bulanan ?? 0)
                + floatval($row->nilai_total_tambahan ?? 0);

            $nestedData[$divisi][$gudang][$barang]['data'][] = [
                'supplierName' => $row->supplierName,
                'poNum' => $row->poNum,
                'poDate' => $row->poDate,
                'qtyPO' => $row->qtyPO,
                'satuanName' => $row->satuanName,

                'nilai_total_bulanan' => $row->nilai_total_bulanan,
                'totalRow' => $totalRow,
            ];

            // summary
            if (!isset($nestedData[$divisi][$gudang][$barang]['summary'])) {
                $nestedData[$divisi][$gudang][$barang]['summary'] = [
                    'totalQtyPO' => 0,
                    'totalBulanan' => 0,
                    'totalRow' => 0,
                ];
            }

            $nestedData[$divisi][$gudang][$barang]['summary']['totalQtyPO'] += floatval($row->qtyPO);
            $nestedData[$divisi][$gudang][$barang]['summary']['totalBulanan'] += floatval($row->nilai_total_bulanan);
            $nestedData[$divisi][$gudang][$barang]['summary']['totalRow'] += $totalRow;
        }
        ?>

        <?php foreach ($nestedData as $divisiName => $gudangList): ?>
            <?php foreach ($gudangList as $warehouseName => $barangList): ?>
                <?php foreach ($barangList as $barangName => $group): ?>
                    <div class="sub-group-title" style="margin-top: 10px;">Department: <?= $divisiName; ?></div>
                    <div class="sub-group-title">Gudang: <?= $warehouseName; ?></div>
                    <div class="sub-group-title">Bahan Baku: <?= $barangName; ?></div>

                    <table>
                        <thead>
                            <tr>
                                <th rowspan="2" class="col-no">No.</th>
                                <th rowspan="2" class="col-supplier">Supplier</th>
                                <th rowspan="2" class="col-nopo">No PO</th>
                                <th rowspan="2" class="col-date">Tgl PO</th>
                                <th rowspan="2" class="col-qty">Qty</th>
                                <th rowspan="2" class="col-satuan">Satuan</th>
                                <th class="col-group">Tambahan Bulanan</th>
                                <th rowspan="2" class="col-group">Total</th>
                            </tr>
                            <tr>
                                <th>Total</th>
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
                                    <td style="text-align: right;"><?= number_format($row['qtyPO'], 2); ?></td>
                                    <td><?= $row['satuanName']; ?></td>
                                    <td style="text-align: right;"><?= number_format($row['nilai_total_bulanan'], 2); ?></td>
                                    <td style="text-align: right;"><?= number_format($row['totalRow'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>

                            <tr style="font-weight: bold; background-color: #eee;">
                                <td colspan="4">TOTAL <?= strtoupper($barangName); ?></td>
                                <td style="text-align: right;"><?= number_format($group['summary']['totalQtyPO'], 2); ?></td>
                                <td></td>
                                <td style="text-align: right;"><?= number_format($group['summary']['totalBulanan'], 2); ?></td>
                                <td style="text-align: right;"><?= number_format($group['summary']['totalRow'], 2); ?></td>
                            </tr>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Tidak ada data yang tersedia.</p>
    <?php endif; ?>
</body>

</html>