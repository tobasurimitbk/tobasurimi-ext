<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanda Terima Faktur Lokal BB</title>
    <style>
        body {
            font-size: 12px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        @page {
            size: 8.27in 5.50in landscape;
            margin: 0px;
            padding: 0px;
        }

        .align-items-center {
            align-items: center;
        }

        .border-collapse {
            border-collapse: collapse;
        }

        .bank-table th,
        .bank-table td {
            border: 1px solid;
        }

        .box-sm {
            border: 1px solid;
            margin-left: 1rem;
            margin-right: 0.5rem;
            height: 20px;
            width: 20px;
        }

        .bukti-pengeluaran {
            display: inline-block;
            vertical-align: middle;
        }

        /* .d-flex {
            display: flex;
        }

        .flex-1 {
            flex: 1;
        } */

        .flex-column {
            flex-direction: column;
        }

        .h-100 {
            height: 100%;
        }

        .justify-content-end {
            justify-content: end;
        }

        .justify-content-even {
            justify-content: space-evenly;
        }

        .pagebreak {
            clear: both;
            page-break-after: always;
        }

        .sign-table td {
            border: 1px solid;
        }

        .sign-table td:last-child {
            border: none;
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

        .w-100 {
            width: 100%;
        }
    </style>
</head>

<body style="border: 0px solid;font-size: 11px;">

    <div style="margin: 25px; padding: 25px" class="">
        <table class="w-100">
            <tr>
                <td>
                    <table>
                        <tr>
                            <td>BUKTI PENGELUARAN</td>
                            <td>
                                <div style="margin-bottom: 0.25rem;">
                                    <div class="box-sm bukti-pengeluaran"></div>
                                    <div class="bukti-pengeluaran">KAS</div>
                                </div>
                                <div>
                                    <div class="box-sm bukti-pengeluaran"></div>
                                    <div class="bukti-pengeluaran">BANK</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table class="bank-table border-collapse" style="float: right;">
                        <tr>
                            <th colspan="2">NO</th>
                            <th>BANK</th>
                        </tr>
                        <tr>
                            <td>CEK</td>
                            <td style="width: 95px;"></td>
                            <td style="width: 95px;"></td>
                        </tr>
                        <tr>
                            <td>GIRO</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="d-flex w-100">
            <div style="display: inline-block;">
                <table>
                    <tr>
                        <td>TGL</td>
                        <td>: <?= date('d-m-Y', strtotime($po_date)) ?></td>
                    </tr>
                    <tr>
                        <td>DIBAYAR KEPADA</td>
                        <td>: <?= $name ?></td>
                    </tr>
                </table>
            </div>
            <div style="display: inline-block;vertical-align: top;float: right;">
                <table>
                    <tr>
                        <td>NO BUKTI:</td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>

        <table class="w-100 bank-table border-collapse">
            <tr>
                <th class="txt-left" style="width: 300px;">KETERANGAN</th>
                <th class="txt-right">JUMLAH</th>
                <th class="txt-left">NO. PERKIRAAN</th>
            </tr>
            <tr>
                <td>Pembayaran <?= $name ?> sebanyak <?= $qty_po . " " . $kode_satuan ?> (No: <?= $po_no ?>)</td>
                <td class="txt-right"><?= number_format($total_after_pph, 2) ?></td>
                <td></td>
            </tr>
            <tr>
                <th class="txt-right">TOTAL</th>
                <th class="txt-right"><?= number_format($total_after_pph, 2) ?></th>
                <th></th>
            </tr>
        </table>

        <div style="margin-top: 0.5rem;margin-bottom: 0.5rem">
            <span>TERBILANG:</span>
            <span style="text-transform: uppercase;"><?= penyebut($total_after_pph) . ' RUPIAH' ?></span>
        </div>

        <table class="w-100 sign-table border-collapse">
            <tr>
                <td class="txt-center" style="width: 5% !important;">DISETUJUI</td>
                <td class="txt-center" style="width: 5% !important;">DIKETAHUI</td>
                <td class="txt-center" style="width: 5% !important;">DIPERIKSA</td>
                <td class="txt-center" style="width: 5% !important;">KASIR</td>
                <td class="txt-center" style="width: 5% !important;">DIBUKUKAN</td>
                <td class="txt-center" style="width: 15% !important;">DITERIMA OLEH</td>
            </tr>
            <tr>
                <td style="height: 50px;"></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>TGL</td>
                <td>TGL</td>
                <td>TGL</td>
                <td>TGL</td>
                <td>TGL</td>
                <td class="txt-center">NAMA JELAS & STEMPEL</td>
            </tr>
        </table>
    </div>
</body>

</html>