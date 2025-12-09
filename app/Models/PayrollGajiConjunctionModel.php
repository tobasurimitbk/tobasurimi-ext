<?php

namespace App\Models;

use CodeIgniter\Model;

class PayrollGajiConjunctionModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'payroll_gaji_conjunction';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'employee_id',
        'payroll_id',
        'tunjangan_id',
        'year_month',
        'nominal'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function generate($payrollID, $employeeID, $companyID, $yearMonth)
    {
        // delete first
        $this->db->table('payroll_gaji_conjunction')
            ->where('employee_id', $employeeID)
            ->where('year_month', $yearMonth)
            ->delete();

        $gajiConjunctionModel = new GajiConjunctionModel();
        $gajiList = $gajiConjunctionModel
            ->select('gaji_conjunction.*,tunjangan.name AS tunjangan_name')
            ->join('tunjangan', 'tunjangan.id = gaji_conjunction.tunjangan_id', 'left')
            ->where('employee_id', $employeeID)
            ->findAll();

        foreach ($gajiList as $g) {

            if ($g['tunjangan_name'] == "UANG MAKAN") {
                $nominal = $this->generateUangMakan(
                    $payrollID,
                    $employeeID
                );
            } else {
                $nominal = $g['nominal'];
            }

            $this->db->table('payroll_gaji_conjunction')
                ->insert([
                    'company_id' => $companyID,
                    'employee_id' => $employeeID,
                    'payroll_id' => $payrollID,
                    'tunjangan_id' => $g['tunjangan_id'],
                    'year_month' => $yearMonth,
                    'nominal' => $nominal
                ]);
        }
    }

    public function generateAmt(
        $mapEmployeePayroll,
        $mapUangMakanHarian,
        $mapDendaAbsenHarian,
        $employeeIds,
        $companyId,
        $yearMonth
    ) {
        // delete first
        $this->db->table('payroll_gaji_conjunction')
            ->whereIn('employee_id', $employeeIds)
            ->where('year_month', $yearMonth)
            ->delete();

        $gajiConjunctionModel = new GajiConjunctionModel();
        $gajiList = $gajiConjunctionModel
            ->select('gaji_conjunction.*,tunjangan.name AS tunjangan_name')
            ->join('tunjangan', 'tunjangan.id = gaji_conjunction.tunjangan_id', 'left')
            ->whereIn('employee_id', $employeeIds)
            ->findAll();

        $dataList = array();
        foreach ($gajiList as $g) {
            if ($g['tunjangan_name'] == "UANG MAKAN") {
                if (empty($mapUangMakanHarian[$g['employee_id']])) {
                    $nominal = $g['nominal'];
                } else {
                    $nominal = $mapUangMakanHarian[$g['employee_id']] ?? 0;
                }
            } elseif ($g['tunjangan_name'] == "DENDA") {
                if (empty($mapDendaAbsenHarian[$g['employee_id']])) {
                    $nominal = $g['nominal'];
                } else {
                    $nominal = $mapDendaAbsenHarian[$g['employee_id']] ?? 0;
                }
            } else {
                $nominal = $g['nominal'];
            }
            array_push($dataList, [
                'company_id' => $companyId,
                'employee_id' => $g['employee_id'],
                'payroll_id' => $mapEmployeePayroll[$g['employee_id']],
                'tunjangan_id' => $g['tunjangan_id'],
                'year_month' => $yearMonth,
                'nominal' => $nominal
            ]);
        }

        return $dataList;
    }

    public function getGajiHarianGajiCadanganAmt($payrollIds)
    {
        // get gaji harian first
        $gajiHarian = $this->asArray()
            ->select('payroll_gaji_conjunction.nominal,payroll_gaji_conjunction.employee_id,payroll_gaji_conjunction.payroll_id')
            ->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id')
            ->where('tunjangan.is_gaji_harian', '1')
            ->whereIn('payroll_gaji_conjunction.payroll_id', $payrollIds)
            ->where('deletedAt', null)
            ->findAll();

        // get nominal uang cadangan
        $gajiCadangan = $this->asArray()
            ->select('payroll_gaji_conjunction.nominal,payroll_gaji_conjunction.employee_id,payroll_gaji_conjunction.payroll_id')
            ->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id')
            ->where('tunjangan.is_cadangan', '1')
            ->whereIn('payroll_gaji_conjunction.payroll_id', $payrollIds)
            ->where('deletedAt', null)
            ->findAll();

        return [
            'gajiHarian' => $gajiHarian,
            'gajiCadangan' => $gajiCadangan
        ];
    }


    public function generateUangMakan($payrollID, $employeeID)
    {
        $payrollModel = new PayrollsModel();
        $uangMakanHarianModel = new UangMakanHarianModel();

        $payroll = $payrollModel->where('id', $payrollID)->first();

        $startDate = $payroll['start_date'];
        $endDate = $payroll['end_date'];

        $totalUangMakan = $uangMakanHarianModel
            ->select('SUM(nominal) AS total_nominal')
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->where('employee_id', $employeeID)
            ->first();

        if ($totalUangMakan) {
            return $totalUangMakan['total_nominal'];
        }

        return 0;
    }

    public function getPerhitunganKomponenGajiPayroll($payrollID)
    {
        $gajiConjunction = $this->asArray()->select("payroll_gaji_conjunction.*, tunjangan.name, tunjangan.tipe")
            ->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id')
            ->where('payroll_gaji_conjunction.payroll_id', $payrollID)
            ->where('tunjangan.is_gaji_harian != ', 1)
            ->where('tunjangan.is_cadangan != ', 1)
            ->orderBy('tunjangan.name', "ASC")
            ->findAll();

        return $gajiConjunction;
    }


    public function getPerhitunganKomponenGajiPayrollPrint($payrollID)
    {
        $gajiConjunction = $this->asArray()
            ->select("payroll_gaji_conjunction.*, tunjangan.name, tunjangan.tipe")
            ->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id')
            ->where('payroll_gaji_conjunction.payroll_id', $payrollID)
            ->where('tunjangan.is_gaji_harian !=', 1)
            ->where('tunjangan.is_cadangan !=', 1)
            ->orderBy('tunjangan.name', "ASC")
            ->findAll();

        $dataResult = [];
        $koperasiNominal = 0;
        $potonganNominal = 0;

        foreach ($gajiConjunction as $g) {
            // cek apakah tunjangan masuk kategori koperasi
            if (in_array(trim($g['name']), ['BON KOPERASI', 'IURAN KOPERASI', 'PINJAMAN KOPERASI'])) {
                $koperasiNominal += $g['nominal'];
            } elseif (in_array(trim($g['name']), ['POTONGAN BAJU SERAGAM', 'POTONGAN SEPATU, CELANA, TOPI', 'POTONGAN TUTUP MULUT'])) {
                $potonganNominal += $g['nominal'];
            } else {
                $dataResult[] = [
                    'name' => $g['name'],
                    'nominal' => $g['nominal']
                ];
            }
        }

        $dataResult[] = [
            'name' => 'Pot. Iuaran/Pinj/Bon Koperasi',
            'nominal' => $koperasiNominal
        ];

        $dataResult[] = [
            'name' => 'Pot. Perlengkapan Kerja',
            'nominal' => $potonganNominal
        ];

        return $dataResult;
    }


    public function getTotalKomponenGajiPayroll($payrollID)
    {
        $res = $this->asArray()->select("payroll_gaji_conjunction.*, tunjangan.name, tunjangan.tipe")
            ->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id')
            ->where('payroll_gaji_conjunction.payroll_id', $payrollID)
            ->where('tunjangan.is_gaji_harian != ', 1)
            ->where('tunjangan.is_cadangan != ', 1)
            ->orderBy('tunjangan.name', "ASC")
            ->findAll();

        $nominal = 0;

        foreach ($res as $r) {
            if ($r['tipe'] == "PLUS") {
                $nominal += $r['nominal'];
            } else {
                $nominal -= $r['nominal'];
            }
        }

        return $nominal;
    }
}
