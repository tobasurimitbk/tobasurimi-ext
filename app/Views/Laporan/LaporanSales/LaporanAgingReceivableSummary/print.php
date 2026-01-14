<!DOCTYPE html>
<html>
<head>
    <title>Aging Receivable Summary</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #f2f2f2; text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

<h3 style="text-align:center;">TOBA FISH</h3>
<h2 style="text-align:center;">Aging Receivable Summary</h2>
<p style="text-align:center;">Hingga: <?= $dateEnd ?></p>

<table>
    <thead>
        <tr>
            <th>Customer Name</th>
            <th>Total Invoice</th>
            <th>Not Yet</th>
            <th>1 - 30</th>
            <th>31 - 60</th>
            <th>61 - 90</th>
            <th>91 - 120</th>
            <th>> 120</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $row): ?>
            <tr>
                <td class="text-left"><?= $row['customer_name'] ?></td>
                <td class="text-right"><?= $row['total_invoice'] ?></td>
                <td class="text-right"><?= $row['not_yet'] ?></td>
                <td class="text-right"><?= $row['aging_1_30'] ?></td>
                <td class="text-right"><?= $row['aging_31_60'] ?></td>
                <td class="text-right"><?= $row['aging_61_90'] ?></td>
                <td class="text-right"><?= $row['aging_91_120'] ?></td>
                <td class="text-right"><?= $row['over_120'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
