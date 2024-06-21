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
    <h2>LAPORAN PENERIMAAN BARANG</h2>
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
            <tr>
                <td>PO</td>
                <td>:</td>
                <td colspan="3" style="text-transform: uppercase;"><?= $filter_bc_type; ?></td>
            </tr>

        </tbody>
    </table>

    <table class="w-100 item-table">
        <tr>
            <th>No</th>
            <th>Jenis Doc</th>
            <th>Tanggal Doc</th>
            <th>No Daftar</th>
            <th>No Aju</th>
            <th>No Bukti</th>
            <th>Tanggal Bukti</th>
            <th>No Order</th>
            <th>Tanggal Order</th>
            <th>Departemen</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Kode Satuan</th>
            <th>Jumlah Order</th>
            <th>Jumlah Diterima</th>
        </tr>



        <?php if (!empty($dataAllPenerimaanBarang)) : ?>
            <?php foreach ($dataAllPenerimaanBarang as $row) : ?>
                <tr>
                    <td><?= $row['no']; ?></td>
                    <td><?= $row['bc_type']; ?></td>
                    <td><?= $row['tanggal_bc']; ?></td>
                    <td><?= $row['no_daftar']; ?></td>
                    <td><?= $row['no_aju']; ?></td>
                    <td><?= $row['no_penerimaan_barang']; ?></td>
                    <td><?= $row['tanggal_lpb']; ?></td>
                    <td><?= $row['po_no']; ?></td>
                    <td><?= $row['po_date']; ?></td>
                    <td><?= $row['divisi']; ?></td>
                    <td><?= $row['kode_barang']; ?></td>
                    <td><?= $row['nama_barang_dok']; ?></td>
                    <td><?= $row['kode_satuan']; ?></td>
                    <td><?= $row['qty']; ?></td>
                    <td><?= $row['jml_masuk']; ?></td>


                </tr>
            <?php endforeach; ?>

        <?php else : ?>
            <tr>
                <td colspan="15">Tidak ada data yang tersedia.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>

</html>