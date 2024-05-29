<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengeluaran Internasional</title>
    <style>
        body {
            font-size: 13px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        .table-font-size {
            font-size: 11px !important;
            text-align: center;
        }

        /* @page {
            size: 10.00in 5.50in landscape;
            margin: 25px;
            padding: 25px;
        } */

        .table-border {
            border: 1px solid black;
            padding-left: 3px;
            padding-right: 3px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .mt-5 {
            margin-top: 5px;
        }

        .mt-3 {
            margin-top: 3rem;
        }

        .sign-table td:not(:last-child) {
            border: 1px solid;
        }

        .sign-row {
            display: flex;
            justify-content: space-around;
            margin-top: 0.25rem;
            width: 100%;
        }

        .sign-row>div {
            width: 200px;
            border-top: 1px solid;
            margin-top: 1rem
        }

        .txt-bold {
            font-weight: 700;
            font-size: 17px;
        }

        .txt-left {
            text-align: left;
        }

        .txt-center {
            text-align: center;
        }

        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 90px;
        }

        tbody {
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="txt-center txt-bold"> <b>PENGELUARAN LOKAL</b></div>
    <table class="mt-5">
        <tr>
            <td width="550">Tanggal Dibuat : <?= $stuffingInternasional['tanggal']; ?></td>
            <td>No. Stuffing Lokal : <?= $stuffingInternasional['no_stuffing']; ?></td>

        </tr>
        <tr>
            <td>Sales Order: <?= $stuffingInternasional['sales_order_export_no']; ?></td>
            <td>Customer: <?= $stuffingInternasional['customer_name']; ?></td>
            <td></td>
        </tr>
        <tr>
            <td></td>

            <td>Dokumen Pabean: <?= $dataAJU ?></td>
        </tr>
    </table><br>
    <b>Data Barang</b><br>

    <table class="mt-5 table-border">
        <thead class="table-border">
            <tr>
                <td class="table-border" style="width: 5px;"><b class="table-font-size">No</b></td>
                <td class="table-border" style="width: 70px;"><b class="table-font-size">Kode Barang</b></td>
                <td class="table-border" style="width: 220px;"><b class="table-font-size">Nama Barang </b></td>
                <td class="table-border" style="width: 90px;"><b class="table-font-size">Qty</b></td>

            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;

            foreach ($salesOrder as $row) {
            ?>
                <tr>
                    <td class="table-border"><?= $no ?></td>
                    <td class="table-border"><?= $row['kode_barang'] ?></td>
                    <td class="table-border"><?= $row['nama_barang'] ?></td>
                    <td class="table-border"><?= $row['qty'] ?></td>


                </tr>

            <?php
                $no += 1;
            } ?>
        </tbody>
    </table><br>
    <b>Daftar Barang Yang Akan Dikeluarkan</b><br>
    <table class="mt-5 table-border">
        <thead class="table-border">
            <tr>
                <td class="table-border" style="width: 5px;"><b class="table-font-size">No</b></td>
                <td class="table-border" style="width: 70px;"><b class="table-font-size">Tipe Barang</b></td>
                <td class="table-border" style="width: 80px;"><b class="table-font-size">Dokumen Pabean</b></td>
                <td class="table-border" style="width: 90px;"><b class="table-font-size">No Aju</b></td>
                <td class="table-border" style="width: 10px;"><b class="table-font-size">Tanggal Penerimaan</b></td>
                <td class="table-border" style="width: 80px;"><b class="table-font-size">Barang - Spesifikasi</b></td>
                <td class="table-border" style="width: 80px;"><b class="table-font-size">Satuan</b></td>
                <td class="table-border" style="width: 80px;"><b class="table-font-size">Qty</b></td>
                <td class="table-border" style="width: 80px;"><b class="table-font-size">Qty Dikeluarkan</b></td>
                <td class="table-border" style="width: 80px;"><b class="table-font-size">Nama Barang Order</b></td>
                <td class="table-border" style="width: 60px;"><b class="table-font-size">Qty Barang Order</b></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;

            foreach ($stuffingInternasionalDetail as $row) {
            ?>
                <tr>
                    <td class="table-border"><?= $no ?></td>
                    <td class="table-border"><?= $row['type_barang_text'] ?></td>
                    <td class="table-border"><?= $row['bc_type'] ?></td>
                    <td class="table-border"><?= $row['no_aju'] ?></td>
                    <td class="table-border"><?= $row['stock_date'] ?></td>
                    <td class="table-border"><?= $row['barang'] ?></td>
                    <td class="table-border"><?= $row['satuan'] ?></td>
                    <td class="table-border"><?= $row['stok_total'] ?></td>
                    <td class="table-border"><?= $row['qty'] ?></td>
                    <td class="table-border"><?= $row['output']['barang'] ?></td>
                    <td class="table-border"><?= $row['output']['qty'] ?></td>

                </tr>

            <?php
                $no += 1;
            } ?>
        </tbody>
    </table>

</body>

</html>