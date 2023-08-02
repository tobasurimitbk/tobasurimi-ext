<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .align-items-center {
            align-items: center;
        }

        .border-collapse {
            border-collapse: collapse;
        }

        .bank-table th, .bank-table td {
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

        .d-flex {
            /* display: flex; */
        }

        .flex-1 {
            /* flex: 1; */
        }

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

<body style="border: 0px solid;font-size: 11px">

    <div class="pagebreak">
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
                        <td>: <?= $data->invoice_date ?></td>
                    </tr>
                    <tr>
                        <td>DIBAYAR KEPADA</td>
                        <td>: <?= $data->supplier_name ?></td>
                    </tr>
                </table>
            </div>
            <div style="display: inline-block;vertical-align: top;float: right;">
                <table>
                    <tr>
                        <td>NO BUKTI:</td>
                        <td><?= $invNo ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <table class="w-100 bank-table border-collapse">
            <tr>
                <th class="txt-left" style="width: 450px;">KETERANGAN</th>
                <th class="txt-right">JUMLAH</th>
                <th class="txt-left">NO. PERKIRAAN</th>
            </tr>
            <tr>
                <td><?= $itemName ?></td>
                <td class="txt-right">RP. <?= $itemTotal ?></td>
                <td></td>
            </tr>
            <tr>
                <td>POTONGAN</td>
                <td class="txt-right">RP. <?= $potongan ?></td>
                <td></td>
            </tr>
            <tr>
                <td>TAMBAHAN</td>
                <td class="txt-right">RP. <?= $tambahan ?></td>
                <td></td>
            </tr>
            <tr>
                <td><?= $taxList ?></td>
                <td class="txt-right">RP. <?= $taxTotal ?></td>
                <td></td>
            </tr>
            <tr>
                <th class="txt-right">TOTAL</th>
                <th class="txt-right">RP. <?= $total ?></th>
                <th></th>
            </tr>
        </table>

        <div style="margin-top: 0.5rem;margin-bottom: 0.5rem">
            <span>TERBILANG:</span>
            <span><?= $terbilang . ' rupiah' ?></span>
        </div>

        <table class="w-100 sign-table border-collapse">
            <tr>
                <td class="txt-center">DISETUJUI</td>
                <td class="txt-center">DIKETAHUI</td>
                <td class="txt-center">DIPERIKSA</td>
                <td class="txt-center">KASIR</td>
                <td class="txt-center">DIBUKUKAN</td>
                <td class="txt-center">DITERIMA OLEH</td>
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

    <div>
        <table class="w-100">
            <tr>
                <td>
                    <table>
                        <tr>
                            <td>BUKTI PENERIMAAN</td>
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
                        <td>: <?= $data->invoice_date ?></td>
                    </tr>
                    <tr>
                        <td>DIBAYAR KEPADA</td>
                        <td>: <?= $data->supplier_name ?></td>
                    </tr>
                </table>
            </div>
            <div style="display: inline-block;vertical-align: top;float: right;">
                <table>
                    <tr>
                        <td>NO BUKTI:</td>
                        <td><?= $invNo ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <table class="w-100 bank-table border-collapse">
            <tr>
                <th class="txt-left" style="width: 450px;">KETERANGAN</th>
                <th class="txt-right">JUMLAH</th>
                <th class="txt-left">NO. PERKIRAAN</th>
            </tr>
            <?php if (!empty($taxReturnList)): ?>
            <tr>
                <td><?= $taxReturnList ?></td>
                <td class="txt-right"><?= $taxReturnTotal ?></td>
                <td></td>
            </tr>
            <?php endif; ?>
            <tr>
                <th class="txt-right">TOTAL</th>
                <th class="txt-right">RP. <?= $taxReturnTotal ?></th>
                <th></th>
            </tr>
        </table>

        <div style="margin-top: 0.5rem;margin-bottom: 0.5rem">
            <span>TERBILANG:</span>
            <span><?= $taxReturnTerbilang . ' rupiah' ?></span>
        </div>

        <table class="w-100 sign-table border-collapse">
            <tr>
                <td class="txt-center">DISETUJUI</td>
                <td class="txt-center">DIKETAHUI</td>
                <td class="txt-center">DIPERIKSA</td>
                <td class="txt-center">KASIR</td>
                <td class="txt-center">DIBUKUKAN</td>
                <td class="txt-center">DITERIMA OLEH</td>
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