<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan Pembelian PT. TOBA SURIMI INDUSTRIES, Tbk (<?= session()->get("login")->this_company; ?>)</title>
  <style>
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      font-size: 5px;
    }

    h5 {
      font-weight: normal;
      font-size: 18px;
      margin-bottom: 10px;
      text-align: left;
      font-weight: bold;
      margin-top: 8px;
    }

    h6 {
      font-size: 13px;
      text-align: left;
      font-weight: bold;
      margin-top: 10px;
      margin-bottom: 10px;
    }

    p {
      font-weight: normal;
      font-size: 10px;
      margin-top: 10px;
      margin-bottom: 10px;
    }

    @page {
      size: 7.44in 10in landscape;
      margin: 30px;
      padding: 100px;
    }

    table {
      border-collapse: collapse !important;
    }

    #table1,
    th {
      border: 1.5px solid #000000;
      font-weight: bold;
      font-size: 10px;
    }

    #table1,
    td {
      border: 1.5px solid #000000;
      font-weight: normal;
      font-size: 9px;
      margin-left: 10px;
    }
  </style>
</head>

<body>
  <h6>PT. TOBA SURIMI INDUSTRIES, Tbk (<?= session()->get("login")->this_company; ?>)</h6>
  <p><b>Purchase Report</b> Period :<?= ($dateStart != "All") ? $dateStart : "" ?> - <?= ($dateEnd != "Now") ? $dateEnd : "" ?></p>

  <table width="100%" id="table1">
    <thead>
      <tr>
        <th>No.</th>
        <th>Transaction Date</th>
        <th>Document</th>
        <th>Evidance Num</th>
        <th>Invoice</th>
        <th>Invoice Date</th>
        <th>Tax Invoice</th>
        <th>PO Num</th>
        <th>Supplier</th>
        <th>Valas</th>
        <th>Exchange Rate</th>
        <th>Nominal Value</th>
        <th>Nominal Value(IDR)</th>
        <th>Paid Value(IDR)</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($data as $value) : ?>
        <tr>
          <td><?= $value['no'] ?></td>
          <td><?= $value['po_date'] ?></td>
          <td><?= $value['dokumen_num'] ?></td>
          <td><?= $value['evidance_num'] ?></td>
          <td><?= $value['invoice_num'] ?></td>
          <td><?= $value['invoice_date'] ?></td>
          <td><?= $value['tax_invoice'] ?></td>
          <td><?= $value['po_num'] ?></td>
          <td><?= $value['supplier_name'] ?></td>
          <td><?= $value['valas'] ?></td>
          <td><?= $value['exchange'] ?></td>
          <td><?= $value['nominal'] ?></td>
          <td><?= $value['nominal_idr'] ?></td>
          <td><?= $value['paid_idr'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>

</html>