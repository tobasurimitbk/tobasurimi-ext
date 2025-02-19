<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Models\PenerimaanBarangModel;
use Exception;

// SELECT * 
// FROM penerimaan_barang 
// WHERE tanggal >= '2025-01-01' 
// AND tanggal <= '2025-01-31' 
// AND status_penerimaan = 'LOKAL' 
// AND tipe_bahan = 'BAKU' 
// AND company_id = 2 
// AND deletedAt IS NULL 
// ORDER BY tanggal DESC

class Penomoran extends BaseController
{

    public function index()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $penerimaan = $db->query("
            SELECT * 
            FROM penerimaan_barang 
            WHERE tanggal >= '2025-02-01' 
            AND tanggal <= '2025-02-31' 
            AND status_penerimaan = 'LOKAL' 
            AND tipe_bahan = 'BAKU' 
            AND company_id = 2 
            AND deletedAt IS NULL 
            ORDER BY id ASC
        ");

        try {
            $penerimaanBarangModel = new PenerimaanBarangModel();
            $i = 1;
            foreach ($penerimaan->getResult() as $p) {
                $noPenerimaan = $p->no_penerimaan_barang;
                $explode = explode('/', $noPenerimaan);
                $explode0 = "LPB-LBB"; // kode
                $explode1 = $explode[1]; // warehouse

                $result = "$explode0/$explode1/$i/II/2025";

                $penerimaanBarangModel->update($p->id, [
                    'no_penerimaan_barang' => $result
                ]);

                // echo $result . "<br>";
                $i++;
            }
            $db->transCommit();
            echo "<br>" . "Total Update : " . $i;
        } catch (Exception $e) {
            $db->transRollback();
            echo "Error " . $e->getMessage();
        }
    }
}

$penomoran = new Penomoran();
$penomoran->index();
