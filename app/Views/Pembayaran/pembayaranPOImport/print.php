<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Internasional</title>
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
            font-size: 16px;
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
    </style>
</head>

<body>
    <div style="text-align: center;">
        UNIT <?= strtoupper($company['company']) ?>
    </div>
    <h5>
        BUKTI PENGELUARAN (EXIM)
    </h5>
    <table align="center">
        <tbody>
            <tr>
                <td style=" font-size:13px;">
                    NO. <?= $detail['payment_no'] ?>
                </td>
            </tr>
        </tbody>
    </table>

    <table style="margin-top: 40px;">
        <tbody>
            <tr>
                <td>Supplier</td>
                <td>:</td>
                <td><?= $supplier['name'] ?></td>
            </tr>
            <tr>
                <td>Tanggal Bayar</td>
                <td>:</td>
                <td><?= date('d/m/Y', strtotime($detail['payment_date']))  ?></td>
            </tr>
            <tr>
                <td>Termin</td>
                <td>:</td>
                <td><?= $detail['termin'] ?></td>
            </tr>
            <tr>
                <td>Valas</td>
                <td>:</td>
                <td><?= $detail['currency'] ?></td>
            </tr>
            <tr>
                <td>Metode Pembayaran</td>
                <td>:</td>
                <td><?= $detail['payment_method'] ?></td>
            </tr>
            <tr>
                <td>Pembayaran Oleh</td>
                <td>:</td>
                <td><?= $detail['pembayaran_oleh'] ?></td>
            </tr>
            <tr>
                <td>Tipe Order</td>
                <td>:</td>
                <td>PURCHASE <?= $detail['po_type'] ?></td>
            </tr>
        </tbody>
    </table>

    <h6>
        Data Pembelian Internasional
    </h6>

    <table width="100%" border="1" id="dashed-border-table" style="margin-top:-20px">
        <thead>
            <tr align="center">
                <td style="width: 10px;">No</td>
                <td>Tanggal PO</td>
                <td>No. PO</td>
                <td>Barang</td>
                <td>Qty</td>
                <td>Unit</td>
                <td>Total</td>
                <td>Input Harga</td>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php $payment_amt_total = 0;
            $user_payment_total = 0;
            $pph_result = 0; ?>
            <?php foreach ($poList as $p) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= date('d/m/Y', strtotime($p['tgl_po'])) ?></td>
                    <td><?= $p['po_no'] ?></td>
                    <td><?= $p['nama_barang'] ?></td>
                    <td><?= $p['qty_order'] ?></td>
                    <td><?= $p['kode_satuan'] ?></td>
                    <td><?= number_format($p['total_harga_number'], 2) ?></td>
                    <td><?= number_format($p['input_user'], 2) ?></td>
                </tr>
                <?php $payment_amt_total += $p['total_harga_number']; ?>
                <?php $user_payment_total += $p['input_user']; ?>
            <?php endforeach; ?>

        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" style="text-align: right;">Total</td>
                <td><?= number_format($payment_amt_total, 2) ?></td>
                <td><?= number_format($user_payment_total, 2) ?></td>
            </tr>
            <tr>
                <td colspan="7" style="text-align: right;">Pajak Penghasilan (2.5 %) (+)</td>
                <td>
                    <?= $detail['status_pph'] == "1" ?  number_format($user_payment_total * (2.5 / 100), 2) : number_format(0, 2); ?>

                </td>
            </tr>
            <tr>
                <td colspan="7" style="text-align: right;">Potongan Panjar</td>
                <td><?= number_format($total_bayar_panjar, 2) ?></td>
            </tr>
            <tr>
                <td colspan="7" style="text-align: right;">Total Dibayar</td>
                <td><?= number_format($detail['payment_amt'], 2) ?></td>

            </tr>
        </tfoot>

    </table>


    <h6>
        Rincian Panjar
    </h6>
    <table width="100%" border="1" id="dashed-border-table" style="margin-top: -20px;">
        <thead>
            <tr>
                <th style="text-align: center;">No</th>
                <th style="text-align: center;">No. Panjar</th>
                <th style="text-align: center;">Payment Date</th>
                <th style="text-align: center;">Bayar Panjar</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php if (!empty($panjar_list)) : ?>
                <?php foreach ($panjar_list as $p) : ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $p['no_panjar']; ?></td>
                        <td><?= $p['payment_date']; ?></td>
                        <td><?= number_format($p['bayar_panjar'], 2); ?></td>
                    </tr>

                <?php endforeach; ?>
                <tr>
                    <td colspan="3" style="text-align: right;">Potongan Panjar</td>
                    <td><?= number_format($total_bayar_panjar, 2) ?></td>
                </tr>
            <?php else : ?>
                <tr>
                    <td colspan="4" style="text-align: center;">Tidak ada Pembayaran Panjar</td>
                </tr>
            <?php endif; ?>

        </tbody>
    </table>




</body>

</html>