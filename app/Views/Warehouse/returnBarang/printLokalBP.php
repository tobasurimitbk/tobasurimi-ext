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
        <div class="w-100 d-flex content-between" style="margin-top: -20px;">
            <b>
                <?= $company['holding_company'] ?> (<?= $company['company'] ?>)<br>
            </b>
        </div><br>
        <div class="txt-center"><span class="title"> <?= strtoupper($title) ?></span></div>
        <table class="w-100 mt-050">
            <tr>
                <td>
                    <div><span class="txt-bold">Supplier : <?= $dataPengembalianBarang['supplier_name']; ?></span></div>
                </td>
                <td>
                    <div><span class="txt-bold">Alamat : <?= $dataPengembalianBarang['address'] ?></span></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div><span class="txt-bold">No LPB : <?= str_replace(',', ', ', str_replace(['[', ']', '"', "\\"], '', $dataPengembalianBarang['multiple_lpb_no'])) ?></span></div>
                </td>
            </tr>

        </table>
        <table class="item-table mt-050">
            <tr>
                <th class="txt-left" style="width: 30px;">No</th>
                <th class="txt-left" style="width: 150px;">Nama Barang</th>
                <th class="txt-left" style="width: 40px;">Qty</th>
                <th class="txt-left" style="width: 30px;">Satuan</th>
                <th class="txt-left" style="width: 60px;">Rp</th>
                <th class="txt-left" style="width: 60px;">Total Harga</th>
                <th class="txt-left" style="width: 60px;">Keterangan</th>
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
                    <td class="txt-left"><?= $detail["kode_barang"] . " - " . $detail['barang_name'] . " " . $detail['spesifikasi']; ?></td>
                    <td class="txt-left"><?= number_format($detail["jumlah_return"], 2); ?></td>
                    <td class="txt-left"><?= $detail['kode_satuan']; ?></td>
                    <td class="txt-left"><?= number_format($detail["harga_satuan_return"], 2); ?></td>
                    <td class="txt-left"><?= number_format($detail["total_harga_return"], 2); ?></td>
                    <td class="txt-left"><?= $detail["keterangan_return"]; ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td class="txt-left" style="padding-left: 5px" colspan="2"><b>TOTAL</b></td>
                <td class="txt-left"><?= number_format($jml_retur, 2); ?></td>
                <td colspan="2"></td>
                <td class="txt-left"><?= number_format($sub_total_retur, 2); ?></td>
                <td></td>
            </tr>
            <tr>
                <td class="txt-left" style="padding-left: 5px" colspan="7"><b>TERBILANG : <?= terbilang($sub_total_retur); ?></b></td>
            </tr>
        </table>
        <div class="header mt-050">
            <table class="w-100 sign-table border-collapse signed-info footer" style="margin-top: 1rem;">

                <tr>
                    <!-- <th>
                    <div class="sign-row">
                        <div>TTD Penerima Bahan Baku</div>
                    </div>
                </th> -->
                    <th>
                        <div class="sign-row">
                            <div>Dibuat Oleh,</div>
                        </div>
                    </th>
                    <th>
                        <div class="sign-row">
                            <div>Diperiksa</div>
                        </div>
                    </th>
                </tr>
                <tr style="border: none!important;">
                    <td class="sign-space" style="border: none!important;"></td>
                    <td class="sign-space" style="border: none!important;"></td>
                </tr>
                <tr>
                    <td class="sign-name" style="border: none!important; margin-top:30px;">
                        <br><br>
                        <div> </div>
                    </td>
                    <td class="sign-name" style="border: none!important;margin-top:30px;">
                        <br><br>
                        <div> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </div>
                    </td>
                    <!-- <td class="sign-name" style="border: none!important;">
                    <div>( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
                </td> -->
                </tr>
            </table>
        <?php } ?>
</body>

</html>