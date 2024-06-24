<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran PO Lokal BB</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            letter-spacing: 2px;
            font-size: 12px;
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
            text-align: left;
            font-weight: bold;
            margin-top: 10px;
        }

        hr {
            border: none;
            border-top: 1px dashed #000;
        }

        @page {
            size: 7.44in 10.00in landscape;
            margin: 29px;
            padding: 29px;
        }

        #dashed-border-table {
            border-collapse: collapse;
        }

        #dashed-border-table th,
        #dashed-border-table td {
            border: 1px dashed #000;
            padding: 5px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div style="text-align: center;">
        UNIT <?= strtoupper($detail['company']['company']) ?>
    </div>
    <h5>
        PEMBAYARAN PO BB
    </h5>
    <table align="center">
        <tbody>
            <tr>
                <td style=" font-size:13px;">
                    NO. <?= $detail['pembayaranDetail']['payment_no'] ?>
                </td>
            </tr>
        </tbody>
    </table>
    <table style="margin-top: 40px;">
        <tbody>
            <tr>
                <td>Supplier</td>
                <td>:</td>
                <td><?= $detail['supplierDetail']['name'] ?></td>
            </tr>
            <tr>
                <td>Tipe Pembayaran</td>
                <td>:</td>
                <td><?= strtoupper($detail['pembayaranDetail']['type_bayar']) ?></td>
            </tr>
            <tr>
                <td>Tanggal Bayar</td>
                <td>:</td>
                <td><?= date('d/m/Y', strtotime($detail['pembayaranDetail']['payment_date']))  ?></td>
            </tr>
            <!-- <tr>
                <td>Jatuh Tempo</td>
                <td>:</td>
                <td> //date('d/m/Y', strtotime($detail['pembayaranDetail']['due_date'])); ?></td>
            </tr> -->
            <tr>
                <td>Potongan</td>
                <td>:</td>
                <td><?= number_format($detail['pembayaranDetail']['potongan_harga'] ?? 0, 2)  ?></td>
            </tr>
            <tr>
                <td>Total Dibayar (Termasuk Diskon)</td>
                <td>:</td>
                <td><?= number_format($detail['pembayaranDetail']['amount'], 2)  ?></td>
            </tr>
            <tr>
                <td>Metode Pembayaran</td>
                <td>:</td>
                <td><?= $detail['pembayaranDetail']['payment_method'] ?></td>
            </tr>
            <tr>
                <td>Pembayaran Oleh</td>
                <td>:</td>
                <td><?= $detail['pembayaranDetail']['pembayaran_oleh'] ?></td>
            </tr>
        </tbody>
    </table>

    <h6>
        Data Pembelian Bahan Baku
    </h6>

    <table width="100%" border="1" id="dashed-border-table" style="margin-top:-20px">
        <thead>
            <tr>
                <th style="text-align: center;">No</th>
                <th style="text-align: center;">Tanggal LPB</th>
                <th style="text-align: center;">No LPB</th>
                <th style="text-align: center;">Tanggal PO</th>
                <th style="text-align: center;">No PO</th>
                <th style="text-align: center;">Barang</th>
       
                <th style="text-align: center;">Total Diterima</th>
        
                <th style="text-align: center;">Total Dibayar</th>

            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($detail['itemList']['detail'] as $d) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= date('d/m/Y', strtotime($d['tanggal_LPB'])) ?></td>
                    <td><?= $d['no_penerimaan_barang'] ?></td>
                    <td><?= date('d/m/Y', strtotime($d['tanggal_PO'])) ?></td>
                    <td><?= $d['po_no'] ?></td>
                    <td><?= $d['barang'] ?></td>
              
                    <td><?= $d['total_diterima'] ?></td>
                  
                    <td><?= number_format($d['total'] ,2)?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="6" style="text-align: right;">
                    Total Pembayaran
                </td>
             
                <td style="text-align: center;">
                    <b><?= $detail['itemList']['total_diterima']; ?></b>
                </td>
                <td style="text-align: center;">
                    <b><?= number_format( $detail['itemList']['total_pembayaran'],2);?></b>
                </td>
           
            </tr>
            <!-- <tr>
                <td colspan="6" style="text-align: right;">
                    Total Dibayar
                </td>
                <td style="text-align: center;">

                </td>
                <td style="text-align: center;">

                </td>
               
            </tr> -->
            <tr>
                <td colspan="6" style="text-align: right;">
                    Diskon
                </td>
                <td style="text-align: center;">

                </td>
       
                <td style="text-align: center;">
                    <b><?= number_format($detail['pembayaranDetail']['potongan_harga'] ?? 0, 2)  ?></b>
                </td>
         
            </tr>
            <tr>
                <td colspan="6" style="text-align: right;">
                    Potongan Panjar
                </td>
                <td>

                </td>
                <td><b><?= number_format( $detail['itemList']['total_bayar_panjar'],2) ; ?></b></td>
            </tr>
   
            <tr>
                <td colspan="6" style="text-align: right;">
                    Total Bayar
                </td>
                <td>

                </td>
                <td><b><?= number_format( $detail['itemList']['total_akhir'],2) ; ?></b></td>
            </tr>

        </tbody>
    </table>

    <h6>
        Rincian Panjar
    </h6>
    <table width="100%" border="1" id="dashed-border-table" style="margin-top:-20px">
        <thead>
            <tr>
                <th style="text-align: center;">No</th>
                <th style="text-align: center;">No. Panjar</th>
                <th style="text-align: center;">Payment Date</th>
              
                <th style="text-align: center;">Bayar Panjar </th>
        

            </tr>
        </thead>

        <tbody>
            <?php $no = 1; ?>
            
            <?php if(!empty($detail['panjar'])) :  ?>
                
                <?php foreach($detail['panjar'] as $p): ?>
                   <tr>
                        <td> <?=  $no++; ?> </td>
                        <td> <?=  $p['no_panjar'] ; ?></td>
                        <td> <?=  $p['payment_date'] ;?></td> 
                        <td> <?=  number_format($p['bayar_panjar'],2) ;?></td>
                
                <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5">Tidak ada Pembayaran Panjar</td>
                    </tr>
                    
            <?php endif; ?>
        </tbody>
    </table>


</body>

</html>