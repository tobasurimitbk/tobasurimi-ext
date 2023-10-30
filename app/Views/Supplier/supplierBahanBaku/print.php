<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .company-name {
            font-weight: 700;
            border: 1px solid;
            padding: 5px;
            border-radius: 7px;
            margin-bottom: 10px;
            display: inline-block;
            min-width: 70px
        }

        .description-container {
            border: 1px solid;
            border-radius: 7px;
            height: 65px;
            margin-top: 20px;
            width: 60%;
            position: relative;
            padding-top: 7px;
            padding-left: 17px;
        }

        .description-label {
            position: absolute;
            top: -10px;
            background: white;
            left: 15px;
            padding-left: 3px;
            padding-right: 5px;
        }

        .item-table {
            border: 1px solid;
            width: 100%;
            height: 230px;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .item-table th {
            border-right: 1px solid;
            border-bottom: 1px solid;
        }

        .item-table td {
            border-right: 1px solid;
        }

        .signature-table {
            border-spacing: 30px 0;
            margin-top: 10px;
        }

        .txt-bold {
            font-weight: 700;
        }

        .txt-center {
            text-align: center;
        }

        .txt-right {
            text-align: right;
        }

        .w-100 {
            width: 100%;
        }
    </style>
</head>
<body>
    <h2>Laporan pendapatan supplier</h2>
    <table class="w-100">
        <tbody>
            <tr>
                <td width="120px">Tanggal</td>
                <td>:</td>
                <td>01-01-2022</td>
                <td> S/D </td>
                <td>31-01-2022</td>
            </tr>
            <tr>
                <td>Bahan Baku</td>
                <td>:</td>
                <td colspan="3" style="text-transform: uppercase;">Cumi</td>
            </tr>
            <tr>
                <td>Lokasi Gudang</td>
                <td>:</td>
                <td colspan="3" style="text-transform: uppercase;">Warehouse1</td>
            </tr>
        </tbody>
    </table>
    
    <table class="item-table">
        <tr>
            <th>No</th>
            <th style="height: 1px;">Item Description</th>
            <th>No. OF</th>
            <th>Qty</th>
            <th>Satuan</th>
            <th>Harga</th>
            <th>% Diskon</th>
            <th>Jumlah</th>
        </tr>
    </table>

    <table class="w-100" style="border-spacing: 3px 0;">
        <tr>
            <td colspan="2"></td>
            <td>
                <div class="txt-right" style="border: 1px solid;">Biaya Lain-lain: </div>
            </td>
            <td>
                <div class="txt-right" style="border: 1px solid;">0</div>
            </td>
        </tr>
        <tr>
            <td style="width: 1px;">Terbilang</td>
            <td style="width: 65%;">
                <div style="border: 1px solid;"></div>
            </td>
            <td>
                <div class="txt-right" style="border: 1px solid;">Total Faktur: </div> 
            </td>
            <td>
                <div class="txt-right" style="border: 1px solid;"></div>
            </td>
        </tr>
    </table>

    <table class="w-100">
        <tr>
            <td style="width: 350px;">
                <div>Catatan: </div>
                <div>Surat Jalan ini tidak berfungsi sebagai Penagihan</div>
                <div>Barang yang sudah diterima tidak dapat dikembalikan</div>
                <div>Kecuali memenuhi ketentuan perjanjian BS Exp Date</div>
            </td>
            <td style="padding-left: 50px">
                <div class="description-container">
                    <label class="description-label">Description: </label>
                </div>
            </td>
        </tr>
    </table>

    <table class="signature-table">
        <tr style="vertical-align: top;">
            <td style="height: 65px;border-bottom: 1px solid;width: 90px">Disiapkan</td>
            <td style="height: 65px;border-bottom: 1px solid;width: 90px">Disetujui Oleh</td>
            <td style="height: 65px;border-bottom: 1px solid;width: 90px">diantar Oleh</td>
            <td style="height: 65px;border-bottom: 1px solid;width: 90px">Diterima Oleh</td>
        </tr>
        <tr>
            <td>Date: </td>
            <td>Date: </td>
            <td>Date: </td>
            <td>Date: </td>
        </tr>
    </table>

</body>
</html>