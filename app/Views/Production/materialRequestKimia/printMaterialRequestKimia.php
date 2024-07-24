<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Material Request Kimia</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            letter-spacing: 2px;
            font-size: 12px;
        }

        h5 {
            font-weight: normal;
            font-size: 18px;
            margin-bottom: 10px;
            text-align: center;
            font-weight: bold;
            margin-top: 8px;
        }

        h6 {
            font-weight: normal;
            font-size: 13px;
            text-align: left;
            font-weight: bold;
            margin-top: 10px;
        }

        hr {
            border: none;
            border-top: 1px dashed #000;
        }

        @page {
            size: 7.44in 10.00in landscape;
            margin: 29px;
            padding: 29px;
        }

        #dashed-border-table {
            border-collapse: collapse;
        }

        #dashed-border-table th,
        #dashed-border-table td {
            border: 1px dashed #000;
            padding: 5px;
            text-align: center;
        }
    </style>
</head>



<body>

    <h5>
        Data Material Request Kimia
    </h5>


    <table width="100%" border="1" id="dashed-border-table" style="">
        <thead>
            <tr>
                <th style="text-align: center;">No</th>
                <th style="text-align: center;">Department</th>
                <th style="text-align: center;">Warehouse</th>
                <th style="text-align: center;">Tipe Barang</th>
                <th style="text-align: center;">Dokumen Pabean</th>
                <th style="text-align: center;">No Aju</th>
                <th style="text-align: center;">Tanggal Penerimaan</th>
                <th style="text-align: center;">Barang - Spesifikasi</th>
                <th style="text-align: center;">Satuan</th>
                <th style="text-align: center;">Qty Awal</th>
                <th style="text-align: center;">Qty Direquest</th>

            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($data as $d) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $d->divisi_text ?></td>
                    <td><?= $d->warehouse_text++; ?></td>
                    <td><?= $d->barang_type_text; ?></td>
                    <td><?= $d->ref_no;  ?></td>
                    <td><?= $d->no_aju;  ?></td>
                    <td><?= date('d/m/Y', strtotime($d->stock_date)); ?></td>
                    <td><?= $d->nama_barang; ?></td>
                    <td><?= $d->satuan; ?></td>
                    <td><?= $d->qty; ?></td>
                    <td><?= $d->qty2; ?></td>

                </tr>

            <?php endforeach; ?>
            <tr>
                <td colspan="9" style="text-align: right;">
                    Total Qty
                </td>
                <td>
                    <?= $totalQty ?>
                </td>
                <td>
                    <?= $totalQty2 ?>
                </td>



            </tr>
        </tbody>
    </table>


</body>

</html>