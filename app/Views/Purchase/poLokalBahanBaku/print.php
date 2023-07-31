<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        @page {
            size: landscape;
        }

        .pagebreak {
            clear: both;
            page-break-after: always;
        }

        .cong-table tr td {
            border: 0 !important;
            border-right: 1px solid !important;
        }

        .content-between {
            justify-content: space-between;
        }

        .d-flex {
            display: flex;
        }

        .item-table {
            width: 100%;
            border: 1px solid;
            border-collapse: collapse;
            border-spacing: 10px;
        }

        .item-table tr td {
            padding: 5px 15px;
        }

        .item-table tr th {
            border: 1px solid;
            padding: 5px 15px;
        }

        .item-table tr td:not(.skip) {
            border: 1px solid;
        }

        .mt-2 {
            margin-top: 2rem;
        }

        .mt-4 {
            margin-top: 4rem;
        }

        .signed-info {
            display: flex;
            justify-content: space-around;
            text-align: center;
        }

        .txt-bold {
            font-weight: 700;
        }

        .txt-left {
            text-align: left;
        }

        .txt-center {
            text-align: center;
        }

        .txt-right {
            text-align: right;
        }

        .txt-underline {
            text-decoration: underline;
        }

        .w-100 {
            width: 100%;
        }

        .table-border {
            border: 1px solid black;
        }
    </style>
</head>

<body>
    <?php if (!empty($dataPO) && !empty($dataPODetail)) { ?>
        <div class="pagebreak">
            <table class="w-100">
                <tr>
                    <td colspan="2"></td>
                    <td class="txt-bold txt-right txt-underline">Tanggal: <?= $dataPO->po_date ? date("d/m/Y", strtotime($dataPO->po_date)) : ""; ?></td>
                </tr>
                <tr>
                    <td class="txt-underline txt-bold">LOCAL PURCHASE ORDER</td>
                    <td colspan="2">NO. NOTA : <?= $dataPO->po_no ?></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td>Supplier</td>
                    <td>: <?= $dataPO->supplierName ?></td>
                </tr>
                <tr>
                    <td>Bahan Baku</td>
                    <td>: <?= $dataPO->itemName ?></td>
                </tr>
            </table>
            <table class="item-table">
                <tr>
                    <th>PETI / TONG</th>
                    <th>BAGIAN</th>
                    <th>KETERANGAN</th>
                    <th class="txt-right">QTY (KG)</th>
                    <th class="txt-right">HARGA @</th>
                    <th class="txt-right">TOTAL</th>
                </tr>
                <?php
                foreach ($dataPODetail as $detail) {
                ?>
                    <tr>
                        <td><?= $detail->peti ?></td>
                        <td><?= $detail->bagian ?></td>
                        <td><?= $detail->spec ?></td>
                        <td class="txt-right"><?= $detail->qty ?></td>
                        <td class="txt-right"><?= $detail->general_price ?></td>
                        <td class="txt-right"><?= number_format(formatter($detail->general_price, "CURR_TO_INT") * formatter($detail->qty, "CURR_TO_INT")) ?></td>
                    </tr>
                <?php } ?>
                <tr class="table-border">
                    <td class="skip" colspan="3">JUMLAH</td>
                    <td class="skip txt-right"><?= $dataPO->totalQty ?></td>\
                    <td class="skip"></td>
                    <td class="txt-right"><?= $dataPO->totalPrice ?></td>
                </tr>
                <tr class="table-border">
                    <td class="skip" colspan="5">PPH</td>
                    <td class="txt-right">@TotalPriceTax@</td>
                </tr>
                <tr class="table-border">
                    <td class="skip" colspan="5">DIBAYARKAN</td>
                    <td class="txt-right">@TotalPricePaid@</td>
                </tr>
            </table>
            <!-- <div class="mt-2 signed-info">
                <div>
                    <div>TTD Penerima Bahan Baku</div>
                    <div class="mt-4">(Nama Penerima)</div>
                </div>
                <div>
                    <div>Diketahui</div>
                    <div class="mt-4">(&emsp;&emsp;&emsp;&emsp;)</div>
                </div>
                <div>
                    <div>Yang Menerima</div>
                    <div class="mt-4">(&emsp;&emsp;&emsp;&emsp;)</div>
                </div>
            </div> -->
        </div>

        <div class="pagebreak">
            <div class="w-100 d-flex content-between">
                <div style="border: 3px solid;border-style: double;width: 30%;padding: 1.2rem;">
                    @CompanyHolding@<br>
                    @CompanyAddress@
                </div>
                <div style="padding: 1.2rem">
                    Kwitansi<br>
                    No. PO : @PONo@
                </div>
            </div>

            <table class="w-100 mt-2">
                <tr>
                    <td style="width: 30%;">SUDAH TERIMA DARI (RECEIVED FROM)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top;">PT. TOBA SURIMI INDUSTRIES</td>
                </tr>
                <tr>
                    <td>BANYAKNYA UANG (AMOUNT)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top;">@Amount@</td>
                </tr>
                <tr>
                    <td>UNTUK PEMBAYARAN (FOR PAYMENT)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top;">PEMBELIAN @ItemName@ SEBANYAK @TotalQty@ KG DARI @SupplierName@</td>
                </tr>
            </table>

            <table class="mt-2" style="width: 30%;border: 0;border-bottom: 3px solid;border-style: double;">
                <tr>
                    <td>Bruto</td>
                    <td>Rp.</td>
                    <td class="txt-right">@TotalPrice@</td>
                </tr>
                <tr>
                    <td>PPh</td>
                    <td>Rp.</td>
                    <td class="txt-right">@TotalPriceTax@</td>
                </tr>
                <tr>
                    <td>Dibayarkan</td>
                    <td>Rp.</td>
                    <td class="txt-right">@TotalPricePaid@</td>
                </tr>
            </table>

            <div class="w-100">
                <div class="txt-center" style="margin-left: auto;margin-right: 0;padding: 1rem;width: 25%;">
                    <div>Medan, 31-12-2022</div>
                    <div>Yang Menerima</div>
                    <div class="mt-4">(@SupplierName@)</div>
                </div>
            </div>
        </div>

        <div class="pagebreak">
            <div class="w-100 d-flex content-between">
                <div style="border: 3px solid;border-style: double;width: 30%;padding: 1.2rem;">
                    @CompanyHolding@<br>
                    @CompanyAddress@
                </div>
                <div style="padding: 1.2rem">
                    Kwitansi Harian<br>
                    No. PO : @PONo@
                </div>
            </div>

            <table class="w-100 mt-2">
                <tr>
                    <td style="width: 30%;">SUDAH TERIMA DARI (RECEIVED FROM)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top;">PT. TOBA SURIMI INDUSTRIES</td>
                </tr>
                <tr>
                    <td>BANYAKNYA UANG (AMOUNT)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top;">@DailyAmount@</td>
                </tr>
                <tr>
                    <td>UNTUK PEMBAYARAN (FOR PAYMENT)</td>
                    <td style="vertical-align: top;">: </td>
                    <td style="vertical-align: top;">PEMBELIAN @ItemName@ SEBANYAK @TotalQty@ KG DARI @SupplierName@</td>
                </tr>
            </table>

            <table class="mt-2" style="width: 30%;border: 0;border-bottom: 3px solid;border-style: double;">
                <tr>
                    <td>Bruto</td>
                    <td>Rp.</td>
                    <td class="txt-right">@TotalDailyPrice@</td>
                </tr>
                <tr>
                    <td>PPh</td>
                    <td>Rp.</td>
                    <td class="txt-right">@DailyAmountTax@</td>
                </tr>
                <tr>
                    <td>Dibayarkan</td>
                    <td>Rp.</td>
                    <td class="txt-right">@DailyAmountPaid@</td>
                </tr>
            </table>

            <div class="w-100">
                <div class="txt-center" style="margin-left: auto;margin-right: 0;padding: 1rem;width: 25%;">
                    <div>Medan, 31-12-2022</div>
                    <div>Yang Menerima</div>
                    <div class="mt-4">(@SupplierName@)</div>
                </div>
            </div>
        </div>

        <div class="pagebreak">
            <table class="w-100">
                <tr>
                    <td colspan="2"></td>
                    <td class="txt-bold txt-right txt-underline">Tanggal: @PODate@</td>
                </tr>
                <tr>
                    <td class="txt-underline txt-bold">KWITANSI TAMBAHAN</td>
                    <td colspan="2">NO. NOTA : @PONo@</td>
                </tr>
            </table>

            <table>
                <tr>
                    <td>Supplier</td>
                    <td>: @SupplierName@</td>
                </tr>
                <tr>
                    <td>Bahan Baku</td>
                    <td>:@ItemName@</td>
                </tr>
            </table>

            <table class="cong-table item-table txt-right">
                <tr>
                    <th>QTY</th>
                    <th>CONG SEBENARNYA</th>
                    <th>CONG BATASAN</th>
                    <th>SELISIH</th>
                    <th>TOTAL TAMBAHAN</th>
                </tr>
                <tr>
                    <td>@TotalQty@</td>
                    <td>@CongSebenarnya@</td>
                    <td>@CongBatasan@</td>
                    <td>@Selisih@</td>
                    <td>@TotalTambahan@</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>PPH</td>
                    <td>@PphTambahan@</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>DIBAYARKAN</td>
                    <td>@SubsidiLangsung@</td>
                </tr>
            </table>

            <div class="mt-2 signed-info">
                <div>
                    <div>TTD Penerima Bahan Baku</div>
                    <div class="mt-4">(&emsp;&emsp;&emsp;&emsp;)</div>
                </div>
                <div>
                    <div>Diketahui</div>
                    <div class="mt-4">(&emsp;&emsp;&emsp;&emsp;)</div>
                </div>
                <div>
                    <div>Yang Menerima</div>
                    <div class="mt-4">(&emsp;&emsp;&emsp;&emsp;)</div>
                </div>
            </div>
        </div>
    <?php } ?>
</body>

</html>