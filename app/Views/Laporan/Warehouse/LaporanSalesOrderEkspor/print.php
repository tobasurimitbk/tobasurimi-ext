<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .company-name {
            font-weight: 700;
            border: 1px solid;
            padding: 5px;
            border-radius: 7px;
            margin-bottom: 10px;
            display: inline-block;
            min-width: 70px
        }

        .description-container {
            border: 1px solid;
            border-radius: 7px;
            height: 65px;
            margin-top: 20px;
            width: 60%;
            position: relative;
            padding-top: 7px;
            padding-left: 17px;
        }

        .description-label {
            position: absolute;
            top: -10px;
            background: white;
            left: 15px;
            padding-left: 3px;
            padding-right: 5px;
        }

        .item-table {
            border: 1px solid;
            width: 100%;
            height: 230px;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .item-table th {
            border-right: 1px solid;
            border-bottom: 1px solid;
            font-size: 13px;
            font-weight: normal;
            padding: 2px
        }

        .item-table td {
            border: 1px solid;
            font-size: 10px;
            padding: 2px
        }

        .signature-table {
            border-spacing: 30px 0;
            margin-top: 10px;
        }

        .txt-bold {
            font-weight: 700;
        }

        .txt-center {
            text-align: center;
        }

        .txt-right {
            text-align: right;
        }

        .w-100 {
            width: 100%;
        }
    </style>
</head>

<body>
    <h2>LAPORAN SALES ORDER EKSPOR</h2>
    <table class="w-100">
        <tbody>
            <tr>
                <td style="width:100px">Tanggal</td>
                <td style="width:10px">:</td>
                <?php if (!empty($tanggalAwal) && !empty($tanggalAkhir)) : ?>
                    <td style="width:80px"><?= $tanggalAwal; ?></td>
                    <td style="width:10px"> S/D </td>
                    <td><?= $tanggalAkhir; ?></td>
                <?php else : ?>
                    <td colspan="3" style="width:80px">ALL</td>

                <?php endif; ?>
            </tr>


        </tbody>
    </table>

    <table class="w-100 item-table">
        <tr>
            <th>No</th>
            <th>Nomor</th>
            <th>Tanggal</th>
            <th>Customer</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Satuan</th>
            <th>Jumlah Order</th>
            <th>Jumlah Diterima</th>
            <th>Total Harga</th>
            <th>Sisa</th>
        </tr>



        <?php if (!empty($data)) : ?>
            <?php foreach ($data as $row) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $row['sales_order_export_no']; ?></td>
                    <td><?= $row['tanggal']; ?></td>
                    <td><?= $row['nama_pelanggan']; ?></td>
                    <td><?= $row['kode_barang']; ?></td>
                    <td><?= $row['nama_barang']; ?></td>
                    <td><?= $row['satuan']; ?></td>
                    <td><?= $row['qty']; ?></td>
                    <td><?= $row['qty_diterima']; ?></td>
                    <td><?= $row['total_harga_barang']; ?></td>
                    <td><?= $row['qty_sisa']; ?></td>

                </tr>
            <?php endforeach; ?>

        <?php else : ?>
            <tr>
                <td colspan="11">Tidak ada data yang tersedia.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>

</html>