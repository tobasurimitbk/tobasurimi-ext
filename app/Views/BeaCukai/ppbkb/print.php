<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPB-KB - <?= $ppbkb['no_ppbkb'] ?></title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
        }

        @page {
            size: 8.27in 11.67in;
            margin: 25px;
            padding: 25px;
        }

        .body {
            margin-left: 30px;
            margin-right: 30px;
        }

        .vendor-detail {
            font-weight: bold;
            font-size: 14px;
        }

        .head-table {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
        }

        .sub-head-table {
            margin-top: 5px;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
            border-collapse: collapse;
            border: 1px solid black;
        }

        .table th,
        .table td {
            padding: 0.25rem;
            vertical-align: top;
            border-top: 1px solid black;
            border-right: 1px solid black;
        }

        .table th:last-child,
        .table td:last-child {
            border-right: none;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid black;
        }

        .head-table {
            text-align: center;
            font-size: 17px;
            font-weight: bold;
            padding: 10px;
        }

        .child-table {
            padding: 10px;
        }

        .new-row-table {
            width: 100%;
            margin-top: 10px;
            border: 1px solid black;
            border-bottom: 0px;
        }
    </style>
</head>

<body>
    <div class="body">
        <table style="width: 100%; margin-top:10px;  border: 1px solid black;border-bottom: 0px;">
            <tr>
                <td class="head-table" style="width: 120px;">
                    PPB-KB
                </td>
                <td class="head-table">
                    PEMBERITAHUAN PEMINDAHAN BARANG DALAM SATU KAWASAN BERIKAT
                </td>
            </tr>

        </table>
        <table style="width: 100%; margin-top:0px; border: 1px solid black; padding:10px; border-bottom: 0px;">
            <tr>
                <td style="width: 90px;">Nomor</td>
                <td style="width: 10px;">:</td>
                <td><?= $ppbkb['no_ppbkb'] ?></td>

            </tr>
            <tr>
                <td>Tanggal</td>
                <td style="width: 10px;">:</td>
                <td><?= date('d/m/Y', strtotime($ppbkb['tanggal']))  ?></td>
            </tr>
        </table>

        <table style="width: 100%; margin-top:0px; padding:10px; border: 1px solid black; border-bottom: 0px;">
            <tr>
                <td colspan="3">Identitas Pengusaha Kawasan Berikat / PDKB</td>
            </tr>
            <tr>
                <td style="width: 120px;">Nama Perusahaan</td>
                <td style="width: 10px;">:</td>
                <td><?= strtoupper($ppbkb['nama_perusahaan']) ?></td>
            </tr>
            <tr>
                <td style="width: 120px;">Nomor Izin</td>
                <td style="width: 10px;">:</td>
                <td><?= ($ppbkb['no_ijin_tpb']) ?></td>
            </tr>
            <tr>
                <td style="width: 120px;">Lokasi</td>
                <td style="width: 10px;">:</td>
                <td><?= ($ppbkb['lokasi_asal_barang']) ?></td>
            </tr>
        </table>
        <table style="width: 100%; margin-top:0px; padding:10px; border: 1px solid black; border-bottom: 0px;">
            <tr>
                <td colspan="3">Asal Lokasi Barang dan Tunjuan Pemindahan Barang</td>
            </tr>
        </table>
        <table style="width: 100%; margin-top: 0px; padding: 10px; border: 1px solid black; border-bottom: 0px;">
            <tr>
                <td>
                    <span style="text-align: center;">Asal Barang</span> <br><br>
                    <span style="text-align: left;">
                        <?= $ppbkb['lokasi_asal_barang'] ?>
                    </span>
                </td>
                <td>
                    <span style="text-align: center;">Tujuan Pemindahan Barang</span> <br><br>
                    <span style="text-align: left;">
                        <?= $ppbkb['lokasi_tujuan_barang'] ?>
                    </span>
                </td>
            </tr>
        </table>
        <table style="width: 100%; margin-top: 0px; padding: 10px; border: 1px solid black; border-bottom: 0px;">
            <tr>
                <td colspan="3"></td>
            </tr>
        </table>
        <table style="width: 100%; margin-top: 0px; padding: 10px; border: 1px solid black; border-bottom: 0px;"">
            <tr>
                <td>No</td>
                <td>
                    - Kode Barang <br>
                    - Kode HS <br>
                    - Jenis Barang
                </td>
                <td>
                    - Jumlah <br>
                    - Satuan
                </td>
                <td>
                    - Dokumen Pemasukan <br>
                    - Nomor <br>
                    - Tanggal
                </td>
            </tr>
            <br>
            <?php $i = 1; ?>
            <?php foreach ($detailBarang as $d) : ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td>
                        <?= $d['kode_barang'] ?> <br>
                        <?= $d['hs_code'] ?> <br>
                        <?= $d['barang'] ?>
                    </td>
                    <td>
                        <?= $d['qty'] ?> <br>
                        <?= $d['satuan'] ?>
                    </td>
                    <td>
                        <?= $d['bc_type'] ?> <br>
                        <?= $d['bc_type'] == "NON PABEAN" ? "-" : explode("-", $d['no_aju'])[3] ?> <br>
                        <?= $d['stock_date'] ?>
                    </td>
                </tr> <br>
            <?php endforeach; ?>
        </table>
        <table style=" width: 100%; margin-top: 0px; padding: 10px; border: 1px solid black; border-bottom: 0px;">
            <tr>
                <td colspan="3">Lembar Persetujuan Pejabat Bea Cukai</td>
            </tr>
            <tr>
                <td style="width: 460px;">
                    <table>
                        <tr>
                            <td>Nomor Agenda Persetujuan</td>
                            <td>:</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Tanggal Persetujuan</td>
                            <td>:</td>
                            <td></td>
                        </tr>
                        <br>
                        <tr>
                            <td>Nama</td>
                            <td>:</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>NIP</td>
                            <td>:</td>
                            <td></td>
                        </tr>
                    </table>

                </td>
                <td style="text-align: right;">
                    <table>
                        <tr>
                            <td>
                                <?= $ppbkb['tempat'] . ", " . $ppbkb['tanggal'] ?> <br>
                                Penanggung Jawab <br>
                                Pengusaha KB / PDKB
                                <br><br><br>
                                <?= $ppbkb['nama'] ?> <br>
                                Jabatan : <?= $ppbkb['jabatan'] ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>
        <table style="width: 100%; margin-top: 0px; padding: 10px; border: 1px solid black;">
            <tr>
                <td style="width: 300px;">Catatan</td>
                <td>:</td>
                <td></td>
            </tr>
            <tr>
                <td>Selesai Dipindahkan Pada Tanggal</td>
                <td>:</td>
                <td></td>
            </tr>
            <tr>
                <td>Pukul</td>
                <td>:</td>
                <td></td>
            </tr>
        </table>
    </div>

</body>

</html>