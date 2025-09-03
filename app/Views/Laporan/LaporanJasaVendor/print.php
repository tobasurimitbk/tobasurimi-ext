<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan Pembelian</title>
  <style>
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      font-size: 5px;
    }

    h5 {
      font-weight: normal;
      font-size: 18px;
      margin-bottom: 10px;
      text-align: center;
      font-weight: bold;
      margin-top: 8px;
    }

    h6 {
      font-weight: normal;
      font-size: 13px;
      text-align: center;
      font-weight: bold;
      margin-top: 10px;
      margin-bottom: 10px;
    }

    @page {
      size: 7.44in 10in landscape;
      margin: 5px;
      padding: 20px;
    }

    table {
      border-collapse: collapse !important;
    }

    #table1,
    th,
    td {
      border: 1px solid #999;
    }
  </style>
</head>

<body>
  <h5>Laporan Pembelian</h5>
  <h6>PT. TOBA SURIMI INDUSTRIES, Tbk ()</h6>
  <h6><?= ($dateStart != "All") ? $dateStart : "" ?> - <?= ($dateEnd != "Now") ? $dateEnd : "" ?></h6>

  <table width="100%" id="table1
      style=" margin-top: -20px;">
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