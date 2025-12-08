<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Timbangan Bahan Baku Kepiting Kukus</title>
    <style>
        @page {
            size: landscape;
            margin: 10mm;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }
        
        .container {
            width: 100%;
            max-width: 355mm; /* Landscape A4 width */
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            text-decoration: underline;
        }
        
        .header h4 {
            margin: 3px 0;
            font-size: 11px;
            font-weight: normal;
        }
        
        .info-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }
        
        .info-table td {
            padding: 2px;
            vertical-align: top;
        }
        
        .main-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            table-layout: fixed;
        }
        
        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 3px;
            vertical-align: top;
        }
        
        .main-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
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
        
        .supplier-header {
            background-color: #e8e8e8;
            font-weight: bold;
            padding: 4px;
            border: 1px solid #000;
        }
        
        .footer {
            margin-top: 15px;
            width: 100%;
        }
        
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .footer-table td {
            text-align: center;
            padding: 15px 0 5px 0;
            vertical-align: top;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 20px auto 5px auto;
        }
        
        /* Column widths */
        .col-no { width: 4%; }
        .col-supplier { width: 15%; }
        .col-keterangan { width: 10%; }
        .col-barang { width: 20%; }
        .col-qty { width: 8%; }
        .col-satuan { width: 8%; }
        .col-detail { width: 35%; }
        
        .compact-row {
            line-height: 1.1;
        }
        
        .barang-masuk-item {
            margin-bottom: 2px;
            padding-left: 5px;
        }
        
        .no-data {
            text-align: center;
            font-style: italic;
            color: #666;
        }
    </style>
</head>

<body>
    <?php 
    // Akses data dengan benar
    $dataDetail = isset($dataDetail['dataDetail']) ? $dataDetail['dataDetail'] : [];
    $dataGroup = isset($dataDetail['dataGroup']) ? $dataDetail['dataGroup'] : [];
    
    // Gunakan data group jika ada, jika tidak gunakan data detail
    $items = !empty($dataGroup) ? $dataGroup : $dataDetail;
    
    if (!empty($items)) : 
    ?>
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <h2>LAPORAN TIMBANGAN BAHAN BAKU KEPITING KUKUS</h2>
            <h4>NO: <?= $jasaVendorIn['no_penerimaan_surat_jalan'] ?? '-' ?></h4>
            <?php if (!empty($items[0]['no_surat_jalan'])): ?>
            <h4>REF: <?= $items[0]['no_surat_jalan'] ?></h4>
            <?php endif; ?>
        </div>
        
        <!-- INFO -->
        <table class="info-table">
            <tr>
                <td width="100">Tanggal</td>
                <td width="10">:</td>
                <td><?= !empty($jasaVendorIn['tanggal']) ? date('d/m/Y', strtotime($jasaVendorIn['tanggal'])) : date('d/m/Y') ?></td>
                
                <td width="100" style="padding-left: 20px;">Vendor</td>
                <td width="10">:</td>
                <td><?= !empty($vendor['name']) ? strtoupper($vendor['name']) : '-' ?></td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td>:</td>
                <td colspan="4"><?= $jasaVendorIn['keterangan'] ?? '-' ?></td>
            </tr>
        </table>
        
        <!-- MAIN TABLE -->
        <table class="main-table">
            <thead>
                <tr>
                    <th class="col-no text-center">No</th>
                    <th class="col-supplier text-center">Supplier</th>
                    <th class="col-keterangan text-center">Keterangan</th>
                    <th class="col-barang text-center">Barang - Spesifikasi</th>
                    <th class="col-qty text-center">Qty Out</th>
                    <th class="col-satuan text-center">Satuan</th>
                    <th class="col-detail text-center">Barang Masuk (Detail)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $totalQtyOut = 0;
                $currentSupplier = null;
                $supplierTotals = [];
                
                foreach ($items as $item) : 
                    $qtyOut = floatval($item['qty_out']);
                    $totalQtyOut += $qtyOut;
                    
                    // Hitung total per supplier
                    $supplierName = !empty($item['supplier_name']) ? $item['supplier_name'] : 'TANPA SUPPLIER';
                    if (!isset($supplierTotals[$supplierName])) {
                        $supplierTotals[$supplierName] = 0;
                    }
                    $supplierTotals[$supplierName] += $qtyOut;
                    
                    // Jika supplier berbeda, tambah baris separator
                    if ($currentSupplier !== $supplierName) {
                        $currentSupplier = $supplierName;
                ?>
                <tr class="supplier-header">
                    <td colspan="7">
                        SUPPLIER: <?= strtoupper($currentSupplier) ?>
                    </td>
                </tr>
                <?php } ?>
                
                <tr class="compact-row">
                    <td class="text-center"><?= $no ?></td>
                    <td><?= !empty($item['supplier_name']) ? strtoupper($item['supplier_name']) : '-' ?></td>
                    <td><?= !empty($item['keterangan']) ? strtoupper($item['keterangan']) : '-' ?></td>
                    <td><?= strtoupper($item['barang_out'] ?? '') . ' - ' . strtoupper($item['spesifikasi_out'] ?? '') ?></td>
                    <td class="text-right"><?= number_format($qtyOut, 3) ?></td>
                    <td class="text-center"><?= $item['satuan_out'] ?? 'KGM' ?></td>
                    <td>
                        <?php if (!empty($item['list_barang_masuk'])) : 
                            $subTotal = 0;
                            $detailCount = 0;
                        ?>
                            <?php foreach ($item['list_barang_masuk'] as $subItem) : 
                                $subQty = floatval($subItem['qty_kotor']);
                                $subTotal += $subQty;
                                $detailCount++;
                            ?>
                                <div class="barang-masuk-item">
                                    <?= $detailCount ?>. <?= strtoupper($subItem['barang_name_in'] ?? '') ?> 
                                    (<?= number_format($subQty, 3) ?> <?= $subItem['kode_satuan_in'] ?? '' ?>)
                                </div>
                            <?php endforeach; ?>
                            <div style="font-weight: bold; margin-top: 2px; border-top: 1px dashed #ccc; padding-top: 2px;">
                                Subtotal: <?= number_format($subTotal, 3) ?> KGM
                            </div>
                        <?php else : ?>
                            <div class="no-data">Tidak ada barang masuk</div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php 
                $no++;
                endforeach; 
                ?>
            </tbody>
            <tfoot>
                <tr style="background-color: #d1ecf1; font-weight: bold;">
                    <td colspan="4" class="text-center">GRAND TOTAL</td>
                    <td class="text-right" style="font-size: 11px;"><?= number_format($totalQtyOut, 3) ?></td>
                    <td class="text-center">KGM</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        
        <!-- SUMMARY -->
        <div style="margin-top: 10px; padding: 5px; border: 1px solid #000; font-size: 9px;">
            <strong>SUMMARY PER SUPPLIER:</strong><br>
            <?php foreach ($supplierTotals as $supplier => $total) : ?>
            - <?= strtoupper($supplier) ?> : <?= number_format($total, 3) ?> KGM<br>
            <?php endforeach; ?>
            <strong>TOTAL KESELURUHAN: <?= number_format($totalQtyOut, 3) ?> KGM</strong>
        </div>
        
        <!-- FOOTER SIGNATURE -->
        <table class="footer-table">
            <tr>
                <td width="33%">
                    <div class="signature-line"></div>
                    <div><strong>DIPERIKSA OLEH</strong></div>
                    <div style="margin-top: 5px;">( <?= date('d/m/Y') ?> )</div>
                </td>
                <td width="33%">
                    <div class="signature-line"></div>
                    <div><strong>DIKETAHUI OLEH</strong></div>
                    <div style="margin-top: 5px;">( <?= date('d/m/Y') ?> )</div>
                </td>
                <td width="33%">
                    <div class="signature-line"></div>
                    <div><strong>DITIMBANG OLEH</strong></div>
                    <div style="margin-top: 5px;">( <?= date('d/m/Y') ?> )</div>
                </td>
            </tr>
        </table>
    </div>
    
    <?php else : ?>
    <div style="text-align: center; padding: 50px;">
        <h3>Tidak ada data untuk ditampilkan</h3>
    </div>
    <?php endif; ?>
</body>

</html>