<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Penerimaan Barang Purchase Order Lokal Bahan Penolong</title>
    <style>
        @page {
            size: landscape;
        }

        .inline {
            display: flex;
            width: 100%;
        }

        .float-l {
            float: left;
        }

        .float-r {
            float: right;
        }

        .mb-40 {
            margin-bottom: 40px;
        }

        .mr-10 {
            margin-right: 10px;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .mt-30 {
            margin-top: 30px;
        }

        .hr-black {
            border-color: #000;
            border-width: 0;
            border-top-width: 1px;

        }

        .hr-black {
            border-color: #000;
            border-width: 0;
            border-top-width: 1px;
            margin: 3rem;
        }

        .body-container {
            border-radius: 7px;
            margin: auto;
            padding: 1rem;
            text-align: left;
            width: 100%;
            font-size: 12px;
        }

        .body-container1 {
            padding: 1rem;
            text-align: left;
            width: 35%;
        }

        .image {
            float: left;
            margin-left: 1rem;
            margin-top: 1rem;
            margin-right: 1rem;
        }

        html {
            font-size: 10px;
        }

        table td {
            border: 1px solid;
            text-align: center;
        }

        table tr td:last-child {
            text-align: center;
            border-right: none;
        }

        table tr td:first-child {
            text-align: center;
            border-left: none;
        }

        table {
            border-left: 1px solid;
            border-right: 1px solid;
            border-collapse: collapse;
            width: 100%;
        }

        td {
            padding: 5px;
        }

        .note {
            width: 50%;
            text-align: justify;
        }
    </style>
</head>

<body>
    <div style="text-align: center; margin: 1rem 1rem 1rem 1rem; font-size:20px; min-height:40px;">
        <div style="font-size:14pt"> <b>PENERIMAAN BARANG DARI PO LOKAL BAHAN PENOLONG</b></div>
    </div>
    <table>
        <thead>
            <tr>
                <td><b>No</b></td>
                <td><b>Departemen</b></td>
                <td><b>No Penerimaan</b></td>
                <td><b>No Po</b></td>
                <td><b>Gudang</b></td>
                <td><b>Tanggal</b></td>
                <td><b>Supplier</b></td>
                <td><b>Jumlah Item</b></td>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data)) : ?>
                <?php foreach ($data as $d) : ?>
                    <tr>
                        <td><?= $d['no'] ?></td>
                        <td><?= $d['divisi'] ?></td>
                        <td><?= $d['no_penerimaan_barang'] ?></td>
                        <td><?= $d['multiple_po_no'] ?></td>
                        <td><?= $d['warehouse_name'] ?></td>
                        <td><?= $d['createdAt'] ?></td>
                        <td><?= $d['supplier_name'] ?></td>
                        <td><?= $d['itemCount'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="8" style="text-align: center;">
                        Tidak Ada Data Penerimaan Barang
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>

</html>