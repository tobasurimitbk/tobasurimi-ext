<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penerimaan Barang Lokal</title>
    <style>
        body {
            font-size: 13px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        @page {
            size: 8.27in 5.50in landscape;
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

        /* .sign-row {
            display: flex;
            justify-content: space-between;
            margin-top: 0rem;
            width: 100%;
        }

        .sign-row>div {
            width: 120px;
            border-top: 1px solid;
            margin-top: 2rem
        } */

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
    <?php if (!empty($dataPenerimaanBarang)) { ?>
        <div class="txt-center"><span class="title">LAPORAN PENERIMAAN BARANG</span></div>
        <table class="w-100 mt-050">
            <tr>
                <td>
                    <div><span class="txt-bold">No. LPB   : <?= $dataPenerimaanBarang->no_penerimaan_barang; ?></span></div>
                </td>
                <td>
                    <div><span class="txt-bold">Supplier   : <?= $dataPenerimaanBarang->supplier_name; ?></span></div>
                </td>
                <td class="txt-right">
                    <div><span class="txt-bold">Jenis Dokumen:    <?= $dataPenerimaanBarang->aju_type_name; ?></span></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div><span class="txt-bold">Tanggal    : <?= $dataPenerimaanBarang->validation_date ? date("d/m/Y", strtotime($dataPenerimaanBarang->validation_date)) : ""; ?></span></div>
                </td>
                <td>
                    <div><span class="txt-bold">No. PO  &nbsp; : <?= implode(", ",json_decode($dataPenerimaanBarang->multiple_po_no)); ?></span></div>
                </td>
                <td class="txt-right">
                    <div><span class="txt-bold">Gudang:    <?= $dataPenerimaanBarang->warehouse_name; ?></span></div>
                </td>
            </tr>
        </table>
        <table class="item-table mt-050">
            <tr>
                <th class="txt-left" style="padding-left: 5px; width: 30px;">No.</th>
                <th class="txt-left" style="padding-left: 5px; width: 100px;">Nama Barang</th>
                <th class="txt-left" style="padding-left: 5px; width: 40px;">Kemasan</th>
                <th class="txt-left" style="padding-left: 5px; width: 40px;">Qty</th>
                <th class="txt-left" style="padding-left: 5px; width: 30px;">Satuan</th>
                <th class="txt-left" style="padding-left: 5px; width: 60px;">@ Rp</th>
                <th class="txt-left" style="padding-left: 5px; width: 60px;">Jumlah</th>
                <th class="txt-left" style="padding-left: 5px; width: 150px;">No. SPP</th>
                <th class="txt-left" style="padding-left: 5px; width: 60px;">Keterangan</th>
            </tr>
            
            <?php 
            $no = 1;
            $jml_masuk = 0;
            $jml_order = 0;
            $jml_harga = 0;
            $jml_sub_total = 0;

            foreach($dataPenerimaanBarangDetail as $detail){ 
                $jml_masuk = $jml_masuk + formatter($detail["jml_masuk"], "STR_TO_FLOAT");
                $jml_order = $jml_order + formatter($detail["qty"], "STR_TO_FLOAT");
                $jml_harga = $jml_harga + formatter($detail["harga"], "STR_TO_INT");
                $jml_sub_total = $jml_sub_total + formatter($detail["sub_total"], "STR_TO_INT");
            ?>
            <tr>
                <td class="txt-center" style="padding-left: 5px;"><?= $no++; ?></td>
                <td class="txt-left" style="padding-left: 5px;"><?= $detail["nama_barang"]; ?></td>
                <td class="txt-left" style="padding-left: 5px;"><?= $detail["nama_packaging"] . "/" . $detail["packaging_qty"]; ?></td>
                <td class="txt-right" style="padding-left: 5px;"><?= $detail["jml_masuk"]; ?></td>
                <td class="txt-left" style="padding-left: 5px;"><?= $detail["nama_satuan"]; ?></td>
                <td class="txt-right" style="padding-left: 5px;"><?= number_format(formatter($detail["harga"], "STR_TO_INT")); ?></td>
                <td class="txt-right" style="padding-left: 5px;"><?= number_format(formatter($detail["sub_total"], "STR_TO_INT")); ?></td>
                <td class="txt-left" style="padding-left: 5px;"><?= $detail["spp_no"]; ?></td>
                <td class="txt-left" style="padding-left: 5px;"><?= $detail["keterangan"]; ?></td>
            </tr>
            <?php } ?>
            <tr>
                <td class="txt-left" style="padding-left: 5px" colspan="6"><b>TOTAL</b></td>
                <td class="txt-right" style="padding-left: 5px;"><?= number_format($jml_sub_total); ?></td>
                <td style="padding-left: 5px;" colspan="2"></td>
            </tr>
            <tr>
                <td class="txt-left" style="padding-left: 5px" colspan="6"><b>ONGKOS KIRIM</b></td>
                <td class="txt-right" style="padding-left: 5px;"><?= number_format(formatter($dataPenerimaanBarang->shipping_cost, "STR_TO_INT")); ?></td>
                <td style="padding-left: 5px;" colspan="2"></td>
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