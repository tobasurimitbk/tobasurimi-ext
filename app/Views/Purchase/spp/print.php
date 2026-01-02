<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Surat Permintaan Pembelian</title>

    <style>
        /* ===============================
   KERTAS F4 – PAKSA 1 HALAMAN
=============================== */
        @page {
            size: 21.6cm 33cm;
            /* F4 */
            margin: 1cm;
        }

        /* tinggi area cetak = 33 - 2 = 31cm */
        html,
        body {
            height: 31cm;
            margin: 0;
            padding: 0;
        }

        body {
            font-size: 12px;
            font-family: 'Gill Sans', Calibri, sans-serif;

            page-break-before: avoid;
            page-break-after: avoid;
        }

        /* ===============================
   BAGI HALAMAN JADI 2
=============================== */
        .half-page {
            height: 15.4cm;
            /* (31 - 0.2) / 2 */
            box-sizing: border-box;
            overflow: hidden;
            padding: 0.3cm;

            page-break-inside: avoid;
            page-break-before: avoid;
            page-break-after: avoid;
        }

        .tear-line {
            height: 0.2cm;
            border-top: 1px dashed #000;
            margin: 0;
        }

        /* ===============================
   TABLE & TEXT
=============================== */
        table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .table-border {
            border: 1px solid #000;
        }

        .table-font-size {
            font-size: 11px;
            text-align: left;
        }

        .txt-center {
            text-align: center;
        }

        .txt-bold {
            font-weight: bold;
            font-size: 16px;
        }

        .mt-5 {
            margin-top: 5px;
        }


        .sign-table td:not(:last-child) {
            border: 1px solid;
        }

        .sign-row {
            display: flex;
            justify-content: space-around;
            margin-top: 0.25rem;
            width: 100%;
        }

        .sign-row>div {
            width: 200px;
            border-top: 1px solid;
            margin-top: 1rem
        }

        .txt-bold {
            font-weight: 700;
            font-size: 17px;
        }

        .txt-left {
            text-align: left;
        }

        .txt-center {
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- ===============================
     BAGIAN ATAS
=============================== -->
    <div class="half-page">

        <div class="txt-center txt-bold">
            SURAT PERMINTAAN PEMBELIAN <br>
            <span style="font-size: 13px;">No. <?= $dataSPP->spp_no ?></span>

        </div>

        <?php if (!empty($dataSPP)) { ?>

            <table class="mt-5" style="font-size: 13px;">
                <tr>
                    <td>Tanggal Order : <?= date("d-m-Y", strtotime($dataSPP->request_date)) ?></td>
                    <!-- <td>Jenis Order : <?= $dataSPP->spp_type ?></td> -->
                </tr>
                <tr>
                    <td>Departemen : <?= $dataSPP->divisiName ?></td>
                    <!-- <td>No SPP : <?= $dataSPP->spp_no ?></td> -->
                </tr>
            </table>

            <table class="mt-5 table-border">
                <thead>
                    <tr>
                        <th class="table-border table-font-size">No</th>
                        <th class="table-border table-font-size" style="width: 60px;">Kode</th>
                        <th class="table-border table-font-size">Nama Barang</th>
                        <th class="table-border table-font-size" style="width: 60px;">Qty</th>
                        <th class="table-border table-font-size">Keterangan</th>
                        <th class="table-border table-font-size" style="width: 70px;">Supplier</th>
                        <th class="table-border table-font-size" style="width: 70px;">Harga</th>

                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataSPP->purchase_request_details as $d): ?>
                        <tr>
                            <td class="table-border table-font-size"><?= $d->no ?></td>
                            <td class="table-border table-font-size"><?= $d->kode_barang ?></td>
                            <td class="table-border table-font-size">
                                <?= $d->barang_name ?> <?= $d->spesifikasi ?>
                            </td>
                            <td class="table-border table-font-size">
                                <?= $d->qty ?> <?= $d->kode_satuan ?>
                            </td>
                            <td class="table-border table-font-size"><?= $d->note ?></td>
                            <td class="table-border table-font-size"></td>
                            <td class="table-border table-font-size"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="mt-5">
                Keterangan: <?= $dataSPP->note ?>
            </div>

            <!-- TANDA TANGAN -->
            <div class="footer" style="margin-top:0px;">
                <div style="text-align:left">
                    <div>Barang tersebut agar kami terima dalam keadaan baik.</div>
                    <div>Pada tanggal:</div>
                </div>
                <table class="w-100 sign-table border-collapse signed-info" style="border: 0px;">
                    <tr style="border: 0px;">
                        <td style="height: 25px; border: 0px;">Diterima Oleh</td>
                        <td style="border: 0px;">Disetujui Oleh</td>
                        <td style="border: 0px;">Diorder Oleh</td>
                    </tr>
                    <tr>
                        <th>
                            <div class="sign-row txt-left">
                                <br>
                                <div></div>
                            </div>
                        </th>
                        <th>
                            <div class="sign-row txt-left">
                                <br>
                                <div></div>
                            </div>
                        </th>
                        <th>
                            <div class="sign-row txt-left">
                                <br>
                                <div><?= $dataSPP->createdByName ?></div>
                            </div>
                        </th>
                    </tr>
                </table>
            </div>

        <?php } ?>

    </div>

    <!-- ===============================
     GARIS SOBEK
=============================== -->
    <div class="tear-line"></div>

    <!-- ===============================
     BAGIAN BAWAH
=============================== -->
    <div class="half-page">

        <div class="txt-center txt-bold">
            SURAT PERMINTAAN PEMBELIAN <br>
            <span style="font-size: 13px;">No. <?= $dataSPP->spp_no ?></span>
        </div>

        <!-- ISI SAMA PERSIS DENGAN BAGIAN ATAS -->
        <!-- (copy ulang isi dokumen atas di sini) -->
        <table class="mt-5" style="font-size: 13px;">
            <tr>
                <td>Tanggal Order : <?= date("d-m-Y", strtotime($dataSPP->request_date)) ?></td>
                <!-- <td>Jenis Order : <?= $dataSPP->spp_type ?></td> -->
            </tr>
            <tr>
                <td>Departemen : <?= $dataSPP->divisiName ?></td>
                <!-- <td>No SPP : <?= $dataSPP->spp_no ?></td> -->
            </tr>
        </table>

        <table class="mt-5 table-border">
            <thead>
                <tr>
                    <th class="table-border table-font-size">No</th>
                    <th class="table-border table-font-size" style="width: 60px;">Kode</th>
                    <th class="table-border table-font-size">Nama Barang</th>
                    <th class="table-border table-font-size" style="width: 60px;">Qty</th>
                    <th class="table-border table-font-size">Keterangan</th>
                    <th class="table-border table-font-size" style="width: 70px;">Supplier</th>
                    <th class="table-border table-font-size" style="width: 70px;">Harga</th>

                </tr>
            </thead>
            <tbody>
                <?php foreach ($dataSPP->purchase_request_details as $d): ?>
                    <tr>
                        <td class="table-border table-font-size"><?= $d->no ?></td>
                        <td class="table-border table-font-size"><?= $d->kode_barang ?></td>
                        <td class="table-border table-font-size">
                            <?= $d->barang_name ?> <?= $d->spesifikasi ?>
                        </td>
                        <td class="table-border table-font-size">
                            <?= $d->qty ?> <?= $d->kode_satuan ?>
                        </td>
                        <td class="table-border table-font-size"><?= $d->note ?></td>
                        <td class="table-border table-font-size"></td>
                        <td class="table-border table-font-size"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="mt-5">
            Keterangan: <?= $dataSPP->note ?>
        </div>

        <!-- TANDA TANGAN -->
        <div class="footer" style="margin-top:0px;">
            <div style="text-align:left">
                <div>Barang tersebut agar kami terima dalam keadaan baik.</div>
                <div>Pada tanggal:</div>
            </div>
            <table class="w-100 sign-table border-collapse signed-info" style="border: 0px;">
                <tr style="border: 0px;">
                    <td style="height: 25px; border: 0px;">Diterima Oleh</td>
                    <td style="border: 0px;">Disetujui Oleh</td>
                    <td style="border: 0px;">Diorder Oleh</td>
                </tr>
                <tr>
                    <th>
                        <div class="sign-row txt-left">
                            <br>
                            <div></div>
                        </div>
                    </th>
                    <th>
                        <div class="sign-row txt-left">
                            <br>
                            <div></div>
                        </div>
                    </th>
                    <th>
                        <div class="sign-row txt-left">
                            <br>
                            <div><?= $dataSPP->createdByName ?></div>
                        </div>
                    </th>
                </tr>
            </table>
        </div>
    </div>

</body>

</html>