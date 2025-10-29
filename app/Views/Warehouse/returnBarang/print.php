<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        body {
            font-size: 12px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
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
    <?php if (!empty($dataPengembalianBarang)) { ?>
        <div class="txt-center"><span class="title"> <?= strtoupper($title) ?></span></div>
        <table class="w-100 mt-050">
            <tr>
                <td>
                    <div><span class="txt-bold">No. Surat Jalan : <?= $dataPengembalianBarang['no_surat_jalan']; ?></span></div>
                </td>
                <td>
                    <div><span class="txt-bold">Supplier : <?= $dataPengembalianBarang['supplier_name']; ?></span></div>
                </td>
                <td>
                    <div><span class="txt-bold">Tanggal Retur: <?= date('d/m/Y', strtotime($dataPengembalianBarang['tanggal_surat_jalan'])) ?></span></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div><span class="txt-bold">Keterangan: <?= $dataPengembalianBarang['keterangan']; ?></span></div>
                </td>
                <td></td>
                <td></td>
            </tr>
        </table>
        <table class="item-table mt-050">
            <tr>
                <th class="txt-left" style="width: 30px;">No</th>
                <th class="txt-left" style="width: 30px;">Kode</th>
                <th class="txt-left" style="width: 60px;">Barang</th>
                <th class="txt-left" style="width: 60px;">Spesifikasi</th>
                <th class="txt-left" style="width: 60px;">Satuan</th>
                <th class="txt-left" style="width: 60px;">Jml Retur</th>
                <th class="txt-left" style="width: 60px;">Harga Retur</th>
                <th class="txt-left" style=" width: 150px;">Keterangan</th>
            </tr>

            <?php
            $no = 1;
            $jml_retur = 0;
            $sub_total_retur = 0;

            ?>
            <?php foreach ($dataPengembalianBarangDetail as $detail) : ?>
                <?php
                $jml_retur += $detail['jumlah_return'];
                $sub_total_retur += $detail['total_harga_return'];
                ?>
                <tr>
                    <td class="txt-center"><?= $no++; ?></td>
                    <td class="txt-left"><?= $detail["kode_barang"]; ?></td>
                    <td class="txt-left"><?= $detail['barang_name']; ?></td>
                    <td class="txt-left"><?= $detail['spesifikasi']; ?></td>
                    <td class="txt-left"><?= $detail['kode_satuan']; ?></td>
                    <td class="txt-left"><?= number_format($detail["jumlah_return"], 2); ?></td>
                    <td class="txt-left"><?= number_format($detail["total_harga_return"], 2); ?></td>
                    <td class="txt-left"><?= $detail["keterangan_return"]; ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td class="txt-left" style="padding-left: 5px" colspan="5"><b>TOTAL</b></td>
                <td class="txt-left"><?= number_format($jml_retur, 2); ?></td>
                <td class="txt-left"><?= number_format($sub_total_retur, 2); ?></td>
                <td></td>
            </tr>
        </table>
        <div class="header mt-050">
            <table class="w-50 sign-table footer" style="padding-top: 0px; margin-top: 0px">
                <tr>
                    <td>Diperiksa & Dibukukan</td>
                    <td class="txt-center" style="width:100px;">Tgl</td>
                    <td class="txt-center" style="width:100px;">Paraf</td>
                </tr>
                <tr>
                    <td style="height: 40px;">Pembelian</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td style="height: 40px;">Accounting</td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        <?php } ?>
</body>

</html>