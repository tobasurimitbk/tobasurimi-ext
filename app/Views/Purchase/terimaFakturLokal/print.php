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

        .d-flex {
            display: flex;
        }

        .flex-1 {
            flex: 1;
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

        .sign-table td:not(:last-child) {
            border: 1px solid;
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

<body style="border: 1px solid;font-size: 11px">

    <div class="d-flex">
        <div class="d-flex align-items-center flex-1">
            <table>
                <tr>
                    <td>BUKTI PENGELUARAN</td>
                    <td>
                        <div>
                            <div class="box-sm"></div>
                            <div>KAS</div>
                        </div>
                    </td>
                </tr>
            </table>
            <!-- <div>BUKTI PENGELUARAN</div>
            <div class="d-flex flex-column justify-content-even">
                <div class="d-flex">
                    <div class="box-sm"></div>
                    KAS
                </div>
                <div class="d-flex">
                    <div class="box-sm"></div>
                    BANK
                </div>
            </div> -->
        </div>
        <div class="flex-1 d-flex justify-content-end">
            <table class="bank-table border-collapse">
                <tr>
                    <th colspan="2">NO</th>
                    <th>BANK</th>
                </tr>
                <tr>
                    <td>CEK</td>
                    <td style="width: 95px;"></td>
                    <td style="width: 95px;">Bank Mandiri</td>
                </tr>
                <tr>
                    <td>GIRO</td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="d-flex">
        <div>
            <table>
                <tr>
                    <td>TGL</td>
                    <td>: kapan ya</td>
                </tr>
                <tr>
                    <td>DIBAYAR KEPADA</td>
                    <td>: ARYA</td>
                </tr>
            </table>
        </div>
        <div>
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
            <td>PPN MASUKAN</td>
            <td class="txt-right">RP. 29.590</td>
            <td></td>
        </tr>
        <tr>
            <th>TOTAL</th>
            <th class="txt-right">RP. 298.200</th>
            <th></th>
        </tr>
    </table>

    <div>
        <span>TERBILANG:</span>
        <span>SEPULUH JUTA</span>
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

</body>

</html>