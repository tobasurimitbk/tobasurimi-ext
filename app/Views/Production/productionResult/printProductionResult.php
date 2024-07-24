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

        .page-break {
            page-break-before: always;
        }
    </style>
</head>



<body>

    <h5>
        Data Production Result
    </h5>



    <h6>Daftar Bahan Digunakan</h6>
    <table width="100%" border="1" id="dashed-border-table" style="">
        <thead>
            <tr>
                <th style="text-align: center;">No</th>
                <th style="text-align: center;">Referensi</th>
                <th style="text-align: center;">Kode Barang</th>
                <th style="text-align: center;">Jenis Barang</th>
                <th style="text-align: center;">Nama Barang</th>
                <th style="text-align: center;">Satuan</th>
                <th style="text-align: center;">Sisa Qty Request</th>
                <th style="text-align: center;">Jumlah Digunakan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            $totalqty = 0; ?>
            <?php foreach ($barang_digunakan as $d) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $d->no_ref ?></td>
                    <td><?= $d->kode_barang; ?></td>
                    <td><?= $d->type_barang_text; ?></td>
                    <td><?= $d->nama_barang;  ?></td>
                    <td><?= $d->kode_satuan;  ?></td>
                    <td><?= $d->qty; ?></td>
                    <td><?= $d->qty; ?></td>

                </tr>
                <?php $totalqty += $d->qty; ?>
            <?php endforeach; ?>
            <tr>
                <td colspan="7" style="text-align: right;">
                    Total Qty
                </td>
                <td style="text-align: center;">
                    <?= number_format($totalqty, 2); ?>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="page-break">

    </div>

    <h6>Daftar Barang Jadi</h6>
    <table width="100%" border="1" id="dashed-border-table" style="">
        <thead>
            <tr>
                <th style="text-align: center;">No</th>
                <th style="text-align: center;">Kode Barang</th>
                <th style="text-align: center;">Jenis Barang</th>
                <th style="text-align: center;">Nama Barang</th>
                <th style="text-align: center;">Satuan</th>
                <th style="text-align: center;">Qty Hasil</th>
                <th style="text-align: center;">Berat Isi </th>
                <th style="text-align: center;">Qty dalam KG</th>

            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            $totalQtyisi = 0; ?>
            <?php foreach ($barang_jadi as $j) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $j->kode_barang ?></td>
                    <td><?= $j->type_barang_text; ?></td>
                    <td><?= $j->nama_barang; ?></td>
                    <td><?= $j->kode_satuan;  ?></td>
                    <td><?= $j->qty;  ?></td>
                    <td><?= $j->qty2; ?></td>
                    <td><?= $j->qty_isi; ?></td>

                </tr>
                <?php $totalQtyisi += $j->qty_isi; ?>
            <?php endforeach; ?>
            <tr>
                <td colspan="7" style="text-align: right;">
                    Total Qty
                </td>
                <td style="text-align: center;">
                    <?= number_format($totalQtyisi, 2); ?>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="page-break">

    </div>

    <h6>Daftar Barang Scrap</h6>
    <table width="100%" border="1" id="dashed-border-table" style="">
        <thead>
            <tr>
                <th style="text-align: center;">No</th>
                <th style="text-align: center;">Kode Barang</th>
                <th style="text-align: center;">Nama Barang</th>
                <th style="text-align: center;">Department</th>
                <th style="text-align: center;">Warehouse</th>
                <th style="text-align: center;">Jumlah</th>

            </tr>
        </thead>
        <tbody>
            <?php if (!empty($barang_scrap)) : ?>
                <?php
                $no = 1;
                $total_qty = 0;
                ?>
                <?php foreach ($barang_scrap as $s) : ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $s->kode_barang ?></td>
                        <td><?= $s->nama_barang; ?></td>
                        <td><?= $s->divisi; ?></td>
                        <td><?= $s->warehouse_name;  ?></td>
                        <td><?= $s->qty;  ?></td>
                    </tr>
                    <?php
                    $total_qty += $s->qty;
                    ?>
                <?php endforeach; ?>
                <tr>
                    <td colspan="5" style="text-align: right;">
                        Total Qty
                    </td>
                    <td style="text-align: center;">
                        <?= number_format($total_qty, 2);
                        ?>
                    </td>
                </tr>
            <?php else : ?>
                <tr>
                    <td colspan="6" style="text-align: center;">
                        No data
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>

    </table>


    <div class="page-break">

    </div>



    <h6>Sisa Produksi</h6>
    <table width="100%" border="1" id="dashed-border-table" style="">
        <thead>
            <tr>
                <th style="text-align: center;">No</th>
                <th style="text-align: center;">Kode Barang</th>
                <th style="text-align: center;">Nama Barang</th>
                <th style="text-align: center;">Kondisi</th>
                <th style="text-align: center;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($barang_sisa)) : ?>
                <?php
                $no = 1;
                $total_qty = 0; // Initialize total quantity
                ?>
                <?php foreach ($barang_sisa as $sisa) : ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $sisa->kode_barang ?></td>
                        <td><?= $sisa->nama_barang; ?></td>
                        <td><?= strtoupper($sisa->kondisi_barang); ?></td>
                        <td><?= $sisa->qty; ?></td>
                    </tr>
                    <?php
                    $total_qty += $sisa->qty; // Add current qty to total
                    ?>
                <?php endforeach; ?>
                <tr>
                    <td colspan="4" style="text-align: right;">
                        Total Qty
                    </td>
                    <td style="text-align: center;">
                        <?= number_format($total_qty, 2); // Display total quantity 
                        ?>
                    </td>
                </tr>
            <?php else : ?>
                <tr>
                    <td colspan="5" style="text-align: center;">
                        No data
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>

    </table>


</body>

</html>