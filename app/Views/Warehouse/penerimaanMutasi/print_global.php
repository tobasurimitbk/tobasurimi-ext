<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penerimaan Mutasi BC 2.7</title>
    <style>
        body {
            font-size: 13px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        @page {
            size: 14.27in 6.50in landscape;
            margin: 25px;
            padding: 25px;
        }

        .header {
            display: flex;
            justify-content: space-between;
        }

        .item-table {
            border-collapse: collapse;
            text-align: left;
            width: 100%;
        }

        .item-table tr th {
            border: 1px solid grey;
        }

        .item-table tr td {
            border: 1px solid grey;
        }

        .mt-025 {
            margin-top: 0.25rem;
        }

        .mt-050 {
            margin-top: 0.5rem;
        }

        .mt-1 {
            margin-top: 1rem;
        }

        .mt-2 {
            margin-top: 2rem;
        }

        .border-collapse {
            border-collapse: collapse;
        }

        .sign-table {
            border-collapse: collapse;
            width: 100%;
        }

        .sign-table td {
            border: 1px solid;
        }

        .txt-bold {
            font-weight: 700;
        }

        .txt-center {
            text-align: center;
        }

        .txt-left {
            text-align: left;
        }

        .txt-right {
            text-align: right;
        }

        .txt-top {
            vertical-align: top;
        }

        .w-30 {
            width: 30%;
        }

        .w-50 {
            width: 50%;
        }

        .w-100 {
            width: 100%;
        }

        .footer {
            position: absolute;
            /* bottom: 0; */
            height: 90px;
        }

        .title {
            font-weight: bold;
            font-size: 25px;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <?php if (!empty($penerimaanMutasiGlobal)) { ?>
        <div class="txt-center"><span class="title">LAPORAN PENERIMAAN MUTASI BC 2.7</span></div>
        <table class="w-100 mt-050">
            <tr>
                <td>
                    <div><span class="txt-bold">No. Penerimaan : <?= $penerimaanMutasiGlobal->penerimaan_mutasi_no; ?></span></div>
                </td>
                <td>
                    <div><span class="txt-bold">Tanggal : <?= date("d/m/Y", strtotime($penerimaanMutasiGlobal->tanggal)); ?></span></div>
                </td>
                <td class="txt-right">
                    <div><span class="txt-bold">Jenis Mutasi: BC 2.7</span></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div><span class="txt-bold">Departemen / Warehouse Penerima : <?= $penerimaanMutasiGlobal->divisi_penerima . " / " . $penerimaanMutasiGlobal->warehouse_penerima ?></span></div>
                </td>
                <td>
                    <div><span class="txt-bold">Company Pengirim : <?= $penerimaanMutasiGlobal->company_pengirim  ?></span></div>
                </td>
                <td>
                    <div><span class="txt-bold">Keterangan : <?= $penerimaanMutasiGlobal->keterangan; ?></span></div>
                </td>
            </tr>
        </table>
        <table class="item-table mt-050">
            <tr>
                <th class="txt-left" style="text-align:center; width: 30px;">No</th>
                <th class="txt-left" style="text-align:center; width: 100px;">No Mutasi</th>
                <th class="txt-left" style="text-align:center; width: 40px;">Tipe Barang</th>
                <th class="txt-left" style="text-align:center; width: 30px;">Dokumen Mutasi</th>
                <th class="txt-left" style="text-align:center; width: 30px;">Dokumen Asal</th>
                <th class="txt-left" style="text-align:center; width: 30px;">Departemen / Warehouse Pengirim</th>
                <th class="txt-left" style="text-align:center; width: 30px;">Supplier</th>
                <th class="txt-left" style="text-align:center; width: 60px;">Barang - Spesifikasi</th>
                <th class="txt-left" style="text-align:center; width: 60px;">Qty Diterima</th>
                <th class="txt-left" style=" text-align:center; width: 150px;">Satuan</th>
            </tr>

            <?php $no = 1; ?>
            <?php foreach ($penerimaanMutasiDetailGlobal as $detail) : ?>
                <tr>
                    <td class="txt-center" style="text-align:center;"><?= $no++; ?></td>
                    <td class="txt-left" style="text-align:center;"><?= $detail['no_mutasi'] ?></td>
                    <td class="txt-right" style="text-align:center;"><?= $detail["tipe_barang_text"]; ?></td>
                    <td class="txt-left" style="text-align:center;"><?= $detail["bc_mutasi_name"] . " / " . $detail['no_aju_mutasi']; ?></td>
                    <td class="txt-left" style="text-align:center;"><?= $detail["bc_asal_name"] . " / " . $detail['no_aju_asal']; ?></td>
                    <td class="txt-left" style="text-align:center;"><?= $detail["divisi_asal_name"] . " / " . $detail['warehouse_asal_name']; ?></td>
                    <td class="txt-right" style="text-align:center;"><?= $detail['supplier_name'] ?></td>
                    <td class="txt-right" style="text-align:center;"><?= $detail['barang'] ?></td>
                    <td class="txt-left" style="text-align:center;"><?= $detail["qty_diterima_current"]; ?></td>
                    <td class="txt-left" style="text-align:center;"><?= $detail["satuan"]; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <div class="header mt-050">
            <table class="w-50 sign-table footer" style="padding-top: 0px; margin-top: 0px">
                <tr>
                    <td>Diperiksa Oleh Gudang</td>
                    <td class="txt-center" style="width:100px;">Tgl</td>
                    <td class="txt-center" style="width:100px;">Paraf</td>
                </tr>
                <tr>
                    <td style="height: 40px;"></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td style="height: 40px;"></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        <?php } ?>
</body>

</html>