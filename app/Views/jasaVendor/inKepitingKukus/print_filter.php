<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
            color: #666;
        }
        .filter-info {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        .filter-info table {
            width: 100%;
        }
        .filter-info td {
            padding: 3px 0;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th {
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            font-weight: bold;
        }
        table.data-table td {
            border: 1px solid #ddd;
            padding: 6px;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 11px;
            color: #666;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .status-posted {
            color: green;
            font-weight: bold;
        }
        .status-waiting {
            color: orange;
            font-weight: bold;
        }
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= $title ?></h1>
        <h2>PT. SURIMI INDONESIA</h2>
    </div>

    <div class="filter-info">
        <table>
            <tr>
                <td width="150"><strong>Departemen:</strong></td>
                <td><?= $filter['divisi'] ?></td>
                <td width="150"><strong>Status:</strong></td>
                <td><?= $filter['status'] ?></td>
            </tr>
            <tr>
                <td><strong>Warehouse:</strong></td>
                <td><?= $filter['warehouse'] ?></td>
                <td><strong>Periode:</strong></td>
                <td><?= $filter['periode'] ?></td>
            </tr>
            <tr>
                <td><strong>No Penerimaan:</strong></td>
                <td><?= $filter['no_penerimaan'] ?></td>
                <td><strong>Total Data:</strong></td>
                <td><?= $filter['total_record'] ?> record(s)</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="40">No</th>
                <th>No Penerimaan Surat Jalan</th>
                <th width="100">Tanggal</th>
                <th>Departemen</th>
                <th>Warehouse</th>
                <th>No Surat Jalan</th>
                <th>Vendor</th>
                <th width="80">Status</th>
                <th width="60">Item</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data)): ?>
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data</td>
                </tr>
            <?php else: ?>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <td class="text-center"><?= $row['no'] ?></td>
                        <td><?= $row['no_penerimaan_surat_jalan'] ?></td>
                        <td class="text-center"><?= $row['tanggal'] ?></td>
                        <td><?= $row['divisi'] ?></td>
                        <td><?= $row['warehouse_name'] ?></td>
                        <td><?= $row['no_surat_jalan'] ?></td>
                        <td><?= $row['vendor_name'] ?></td>
                        <td class="text-center <?= $row['status_posting'] == 'POSTED' ? 'status-posted' : 'status-waiting' ?>">
                            <?= $row['status_posting'] ?>
                        </td>
                        <td class="text-center"><?= $row['total_item'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: <?= $filter['printed_at'] ?></p>
    </div>

    <script>
        // Auto print saat halaman load
        window.onload = function() {
            window.print();
            
            // Auto close setelah print (jika di print dialog)
            setTimeout(function() {
                window.close();
            }, 1000);
        };
        
        // Fallback jika tidak ada print dialog
        window.onafterprint = function() {
            setTimeout(function() {
                window.close();
            }, 500);
        };
    </script>
</body>
</html>