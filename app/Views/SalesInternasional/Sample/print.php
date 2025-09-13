<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $dataSample['no_sample'] ?></title>
    <style>
        @media print {
            body {
                margin: 0;
            }

            @page {
                size: 210mm 330mm;
                /* margin: 20mm; */
            }
        }

        .header {
            display: flex !important;
            justify-content: space-between !important;
        }

        .sign-table {
            border-collapse: collapse !important;
            text-align: left !important;
            width: 100% !important;
            margin-top: 2.5rem;
        }

        .sign-table tr th {
            text-align: left !important;
        }

        .item-table {
            border-collapse: collapse !important;
            text-align: center !important;
            width: 100% !important;
        }

        .item-table tr th {
            text-align: left !important;
        }

        .item-table tr td {
            text-align: left !important;
        }

        .item-table tbody tr {
            border-top: 1px solid !important;
        }

        .mt-025 {
            margin-top: 0.25rem;
        }

        .mt-1 {
            margin-top: 0.6rem;
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

        .w-30 {
            width: 30%;
        }

        .w-50 {
            width: 50%;
        }

        .w-100 {
            width: 100%;
        }

        .border-collapse {
            border-collapse: collapse;
        }

        .label-header {
            font-weight: bold;
            font-size: 12px;
        }

        .po-customer {
            margin-top: -20px !important;
        }

        .rev-customer {
            margin-top: -20px !important;
        }

        .justify-content-center {
            justify-content: center !important;
        }

        .ttd-section {
            page-break-inside: avoid;
            margin-top: 20px;
        }

        .label-header {
            font-weight: bold;
            font-size: 12px;
        }

        body {
            font-family: 'Times New Roman', Times, serif
        }

        .pagebreak {
            clear: both;
            page-break-after: always;
        }
    </style>
</head>

<body>


    <div class="header">
        <div class="txt-center" style="margin-top: -10px;">
            <h3>COMMERCIAL INVOICE</h3>
        </div>
    </div>



</body>

</html>