<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $dataSPP->spp_no ?></title>

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
            margin-left: 10px;
            margin-right: 10px;
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
            height: 9.2cm;
            /* (31 - 0.2) / 2 */
            box-sizing: border-box;
            overflow: hidden;
            padding: 0.3cm;

            page-break-inside: avoid;
            page-break-before: avoid;
            page-break-after: avoid;
        }

        .tear-line {
            height: 0.4cm;
            border-top: 1px dashed #000;
            margin: 0;
            margin-top: 30px;
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
            /* line-height: 30px; */
        }

        .table-border td,
        .table-border th {
            text-align: left;
            /* kalau mau horizontal juga */
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

        .table-font-size {
            font-size: 11px !important;
            text-align: left;
            height: 30px !important;
        }

        .copy-text {
            text-align: center;
            margin-top: 0px;
            font-size: 25px;
            font-weight: bold;
            opacity: 0.35;
            /* transparan */
            letter-spacing: 2px;
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
            <span style="font-size: 16px;">No. <?= $dataSPP->spp_no ?></span>

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
                        <th class="table-border table-font-size"><b class="table-font-size">No</b></th>
                        <th class="table-border table-font-size" style="width: 70px;"><b class="table-font-size">Kode</b></th>
                        <th class="table-border table-font-size"><b class="table-font-size">Nama Barang</b></th>
                        <th class="table-border table-font-size" style="width: 60px;"><b class="table-font-size">Qty</b></th>
                        <th class="table-border table-font-size"><b class="table-font-size">Keterangan</b></th>
                        <!-- <th class="table-border table-font-size" style="width: 80px;"><b class="table-font-size">Supplier</b></th> -->
                        <th class="table-border table-font-size" style="width: 160px; text-align:center" colspan="2"><b class="table-font-size">Harga</b></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataSPP->purchase_request_details as $d): ?>
                        <tr>
                            <td class="table-border table-font-size"><b class="table-font-size"><?= $d->no ?></b></td>
                            <td class="table-border table-font-size"><b class="table-font-size"><?= $d->kode_barang ?></b></td>
                            <td class="table-border table-font-size">
                                <b class="table-font-size"> <?= $d->barang_name ?> <?= $d->spesifikasi ?> </b>
                            </td>
                            <td class="table-border table-font-size">
                                <b class="table-font-size"> <?= $d->qty ?> <?= $d->kode_satuan ?> </b>
                            </td>
                            <td class="table-border table-font-size"><b class="table-font-size"><?= $d->note ?></b></td>
                            <td class="table-border table-font-size"></td>
                            <td class="table-border table-font-size"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>



        <?php } ?>

    </div>
    <!-- COPY -->
    <div class="copy-text mt-5">
        &#160;
    </div>
    <div>
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
    <div class="tear-line"></div>

    <!-- ===============================
     BAGIAN BAWAH
=============================== -->
    <div class="half-page">

        <div class="txt-center txt-bold">
            SURAT PERMINTAAN PEMBELIAN <br>
            <span style="font-size: 16px;">No. <?= $dataSPP->spp_no ?></span>
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
                    <th class="table-border table-font-size"><b class="table-font-size">No</b></th>
                    <th class="table-border table-font-size" style="width: 70px;"><b class="table-font-size">Kode</b></th>
                    <th class="table-border table-font-size"><b class="table-font-size">Nama Barang</b></th>
                    <th class="table-border table-font-size" style="width: 60px;"><b class="table-font-size">Qty</b></th>
                    <th class="table-border table-font-size"><b class="table-font-size">Keterangan</b></th>
                    <!-- <th class="table-border table-font-size" style="width: 80px;"><b class="table-font-size">Supplier</b></th> -->
                    <th class="table-border table-font-size" style="width: 160px; text-align:center" colspan="2"><b class="table-font-size">Harga</b></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dataSPP->purchase_request_details as $d): ?>
                    <tr>
                        <td class="table-border table-font-size"><b class="table-font-size"><?= $d->no ?></b></td>
                        <td class="table-border table-font-size"><b class="table-font-size"><?= $d->kode_barang ?></b></td>
                        <td class="table-border table-font-size">
                            <b class="table-font-size"> <?= $d->barang_name ?> <?= $d->spesifikasi ?> </b>
                        </td>
                        <td class="table-border table-font-size">
                            <b class="table-font-size"> <?= $d->qty ?> <?= $d->kode_satuan ?> </b>
                        </td>
                        <td class="table-border table-font-size"><b class="table-font-size"><?= $d->note ?></b></td>
                        <td class="table-border table-font-size"></td>
                        <td class="table-border table-font-size"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>



    </div>
    <!-- COPY -->
    <div class="copy-text mt-5">
        COPY
    </div>
    <div>
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
    <!-- ===============================
     GARIS SOBEK
=============================== -->
    <div class="tear-line"></div>

</body>

</html>