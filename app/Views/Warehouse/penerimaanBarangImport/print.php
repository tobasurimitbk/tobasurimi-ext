<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>

        .header {
            display: flex;
            justify-content: space-between;
        }

        .w-50 {
            width: 50%;
        }

        .w-100 {
            width: 100%;
        }

        html {
            font-size: 10px;
        }
        
        .table 
        td {
            border: 1px solid;
            text-align: center;
        }

        .table tr td:last-child {
            text-align: center;
            border-right: none;
        }
        .table tr td:first-child {
            text-align: center;
            border-left: none;
        }
        .table {
            border-left:1px solid;
            border-right:1px solid;
        }
        .table td {
            padding: 5px;
        }

        .sign-table 
        td {
            text-align: center;
        }

        .note {
            width: 50%;
            text-align: justify;
        }

        .border-collapse {
            border-collapse: collapse;
        }

        .sign-table td:not(:last-child) {
            border: 1px solid;
        }
    </style>
</head>
<body>
    <?php if(!empty($dataPenerimaanBarang) && !empty($dataPenerimaanBarangDetail)){ ?>
    <div class="header">
        <table class="mt-1 w-100">
            <tr>
                <td>
                    <div style="margin-top: -40px;">
                        <div style="font-size:14pt"> <b> PT TOBA SURIMI INDUSTRIES, Tbk </b></div>
                        <div style="white-space: wrap">Medan merdeka barat no 20, Medan Utara</div>
                        <div style="white-space: wrap">021-327829</div>
                        <div style="white-space: wrap"></div>
                    </div>
                </td>
                <td>
                    <table class="mt-1 w-100">
                        <tr>
                            <td>
                                <div style="margin-top: -11px;">
                                    <div><b> Laporan Barang Masuk </b></div>
                                    <div style="white-space: wrap"><?= $dataPenerimaanBarang->aju_type_name; ?> / <?= $dataPenerimaanBarang->aju_no; ?></div>
                                    <div style="white-space: wrap">Tanggal: <?= $dataPenerimaanBarang->validation_date ? date("d/m/Y", strtotime($dataPenerimaanBarang->validation_date)) : ""; ?></div>
                                    <div style="white-space: wrap"></div>
                                </div>
                            </td>
                            <td>
                                <div style="margin-top: -11px;">
                                    <div style=""> <b> From : <?= $dataPenerimaanBarang->supplier_name; ?></b></div>
                                    <div style="white-space: wrap">Address : <?= $dataPenerimaanBarang->supplier_address; ?></div>
                                    <div style="white-space: wrap">Phone : <?= $dataPenerimaanBarang->supplier_phone; ?></div>
                                    <div style="white-space: wrap"></div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="inline" style="margin-top:1rem">
        <br />
        <div>Kemasan / Berat
        : <?= $dataPenerimaanBarang->packaging; ?> / <?= $dataPenerimaanBarang->total_weight; ?>
        </div>
        <div class="inline">
        <div>No. LPB
        : <?= $dataPenerimaanBarang->no_penerimaan_barang; ?>
        </div>
        <div>No. Order
        : <?= implode(", ",json_decode($dataPenerimaanBarang->multiple_po_no)); ?>
        </div>
        <div>Harap dikirimkan kepada kami barang-barang berikut dibawah ini:</div>
        <table class="table"
        style="
            border-collapse: collapse;
            width: 100%;
        "
        >
        <thead>
            <tr>
            <td><b>No.</b></td>
            <td><b>Kode Barang</b></td>
            <td><b>Nama Barang</b></td>
            <td><b>Spesifikasi</b></td>
            <td><b>Satuan</b></td>
            <td><b>Jumlah Diterima</b></td>
            <td><b>Jumlah Dokumen</b></td>
            <td><b>Jumlah Order</b></td>
            <td><b>Konversi</b></td>
            <td><b>Harga</b></td>
            <td><b>Total Harga</b></td>
            <td><b>No. PO</b></td>
            <td><b>Keterangan</b></td>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $jml_masuk = 0;
            $jml_dokumen = 0;
            $jml_order = 0;
            $jml_konversi = 0;
            $jml_harga = 0;
            $jml_penyerahan = 0;

            foreach($dataPenerimaanBarangDetail as $detail){ 
                $jml_masuk = $jml_masuk + formatter($detail["jml_masuk"], "STR_TO_INT");
                $jml_dokumen = $jml_dokumen + formatter($detail["doc_qty"], "STR_TO_INT");
                $jml_order = $jml_order + formatter($detail["qty"], "STR_TO_INT");
                $jml_konversi = $jml_konversi + formatter($detail["konversi"], "STR_TO_INT");
                $jml_harga = $jml_harga + formatter($detail["harga"], "STR_TO_INT");
                $jml_penyerahan = $jml_penyerahan + formatter($detail["penyerahan"], "STR_TO_INT");
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $detail["kode_barang"]; ?></td>
                <td><?= $detail["nama_barang"]; ?></td>
                <td><?= $detail["spec"]; ?></td>
                <td><?= $detail["nama_satuan"]; ?></td>
                <td><?= $detail["jml_masuk"]; ?></td>
                <td><?= $detail["doc_qty"]; ?></td>
                <td><?= $detail["qty"]; ?></td>
                <td><?= $detail["konversi"]; ?></td>
                <td><?= number_format(formatter($detail["harga"], "STR_TO_INT")); ?></td>
                <td><?= number_format(formatter($detail["penyerahan"], "STR_TO_INT")); ?></td>
                <td><?= $detail["po_no"]; ?></td>
                <td><?= $detail["keterangan"]; ?></td>
            </tr>
            <?php } ?>
                <tr>
                    <td style="text-align:center" colspan="5">Total</td>
                    <td><?= $jml_masuk; ?></td>
                    <td><?= $jml_dokumen; ?></td>
                    <td><?= $jml_order; ?></td>
                    <td><?= $jml_konversi; ?></td>
                    <td><?= number_format($jml_harga); ?></td>
                    <td><?= number_format($jml_penyerahan); ?></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td style="text-align:center" colspan="10">Ongkos Kirim</td>
                    <td><?= number_format(formatter($dataPenerimaanBarang->shipping_cost, "STR_TO_INT")); ?></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <table class="w-100 sign-table border-collapse">
            <tr>
                <td style="height: 80px;"></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
            <td><div style="text-align: left !important; margin-left:1rem;width:100%;height:0rem;border-top:1px solid">Pembelian:</div></td>
            <td><div style="text-align: left !important; margin-left:1rem;width:100%;height:0rem;border-top:1px solid">Accounting:</div></td>
            </tr>
        </table>
    </div>
<?php } ?>
</body>
</html>