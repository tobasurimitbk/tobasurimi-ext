<?php

namespace App\Models;

use CodeIgniter\Model;

class GajiConjunctionModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'gaji_conjunction';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "id",
        "employee_id",
        "tunjangan_id",
        "nominal"
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';
    protected $deletedField  = 'deletedAt';

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

    public function getKomponenByEmployeeId($id)
    {
        $arrCondition = [
            'gaji_conjunction.deletedAt' => null,
            'gaji_conjunction.employee_id' => $id
        ];

        $builder = $this->db->table('gaji_conjunction')
            ->select('gaji_conjunction.*, tunjangan.name AS tunjangan_name')
            ->join('tunjangan', 'tunjangan.id = gaji_conjunction.tunjangan_id', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getNominalByKomponenName($komponenName, $employeeID)
    {
        $res =  $this->asArray()->join('tunjangan', 'tunjangan.id = gaji_conjunction.tunjangan_id')
            ->where('employee_id', $employeeID)
            ->like('tunjangan.name', $komponenName)
            ->first();

        return ($res == null) ? 0 : $res['nominal'];
    }

    public function updateBulkGajiHarian($divisiId)
    {
        $gajiDivisiModel = new GajiDivisiModel();
        $gajiDivisi = $gajiDivisiModel
            ->select('gaji_divisi.*')
            ->join('tunjangan', 'tunjangan.id = gaji_divisi.tunjangan_id', 'left')
            ->where('tunjangan.is_gaji_harian', 1)
            ->where('gaji_divisi.division_id', $divisiId)
            ->where('gaji_divisi.deletedAt', null)
            ->first();

        if ($gajiDivisi != null) {
            $gajiConjunctionList =  $this
                ->select('gaji_conjunction.*')
                ->join('employees', 'employees.id = gaji_conjunction.employee_id', 'left')
                ->where('gaji_conjunction.deletedAt', null)
                ->where('employees.division_id', $divisiId)
                ->where('gaji_conjunction.tunjangan_id', $gajiDivisi['tunjangan_id'])
                ->findAll();

            $updatedData = array();
            foreach ($gajiConjunctionList as $g) {
                array_push($updatedData, [
                    'nominal' => $gajiDivisi['nominal'],
                    'id' => $g['id']
                ]);
            }

            if (count($updatedData) != 0) {
                $this->updateBatch($updatedData, 'id');
            }
        }
    }

    public function syncKomponenGaji($divisiId)
    {
        $gajiConjunctionModel = new GajiConjunctionModel();
        $gajiDivisiModel      = new GajiDivisiModel();
        $employeeModel        = new EmployeesModel();

        // ============================
        // AMBIL LIST KOMPONEN DI DIVISI
        // ============================
        $gajiDivisi = $gajiDivisiModel->select('gaji_divisi.*')
            ->join('tunjangan', 'tunjangan.id = gaji_divisi.tunjangan_id', 'left')
            ->where('gaji_divisi.division_id', $divisiId)
            ->where('gaji_divisi.deletedAt', null)
            ->findAll();

        // Format: [tunjangan_id => nominal]
        $gajiDivisiMap = [];
        foreach ($gajiDivisi as $g) {
            $gajiDivisiMap[$g['tunjangan_id']] = $g['nominal'];
        }

        // ============================
        // AMBIL KOMPONEN YANG SUDAH ADA DI gaji_conjunction
        // ============================
        $employeeConjunction = $gajiConjunctionModel->select(
            '
        employees.id AS employee_id,
        employees.division_id,
        gaji_conjunction.tunjangan_id,
        gaji_conjunction.nominal'
        )
            ->join('employees', 'gaji_conjunction.employee_id = employees.id', 'left')
            ->where('employees.division_id', $divisiId)
            ->where('gaji_conjunction.deletedAt', null)
            ->findAll();

        // Format: [employee_id][tunjangan_id] = nominal
        $existingMap = [];

        foreach ($employeeConjunction as $e) {
            $existingMap[$e['employee_id']][$e['tunjangan_id']] = $e['nominal'];
        }

        // ============================
        // HASIL SYNC
        // ============================
        $gajiConjunctionInserted = [];
        $gajiConjunctionDeleted  = [];

        // ============================
        // GET SEMUA KARYAWAN DI DIVISI
        // ============================
        $employees = $employeeModel
            ->where('division_id', $divisiId)
            ->where('deletedAt', null)
            ->findAll();

        // ============================
        // PROSES SYNC PER KARYAWAN
        // ============================
        $deletedAt = date('Y-m-d H:i:s');
        foreach ($employees as $emp) {
            $empId = $emp['id'];

            $current = $existingMap[$empId] ?? [];   // tunjangan milik employee di conjunction

            // 1. CEK APAKAH ADA KOMPONEN BARU (INSERT)
            foreach ($gajiDivisiMap as $tunjanganId => $nominalDivisi) {
                if (!isset($current[$tunjanganId])) {
                    // employee belum punya komponen ini → tambahkan ke inserted
                    $gajiConjunctionInserted[] = [
                        'employee_id'  => $empId,
                        'tunjangan_id' => $tunjanganId,
                        'nominal'      => (float)$nominalDivisi
                    ];
                }
            }

            // 2. CEK APAKAH ADA KOMPONEN YANG SUDAH DIHAPUS DI gaji_divisi (DELETE)
            foreach ($current as $tunjanganId => $nominalEmp) {
                if (!isset($gajiDivisiMap[$tunjanganId])) {
                    // komponen ada di conjunction namun tidak ada lagi di gaji_divisi
                    $gajiConjunctionDeleted[] = [
                        'employee_id'  => $empId,
                        'tunjangan_id' => $tunjanganId,
                        'deletedAt' => $deletedAt
                    ];
                }
            }
        }

        // DELETE
        if (!empty($gajiConjunctionDeleted)) {
            foreach ($gajiConjunctionDeleted as $row) {
                $gajiConjunctionModel
                    ->where('employee_id', $row['employee_id'])
                    ->where('tunjangan_id', $row['tunjangan_id'])
                    ->delete();
            }
        }

        // INSERT
        if (!empty($gajiConjunctionInserted)) {
            $gajiConjunctionModel->insertBatch($gajiConjunctionInserted);
        }

        // var_dump($gajiConjunctionDeleted, $gajiConjunctionInserted);
        // die;
    }

    public function getMapGajiHarianDanCadangan(
        $employeeIds
    ) {
        $gajiPokok = $this->asArray()
            ->select('employee_id, nominal')
            ->join('tunjangan', 'tunjangan.id = gaji_conjunction.tunjangan_id')
            ->whereIn('gaji_conjunction.employee_id', $employeeIds)
            ->where('tunjangan.is_gaji_harian', 1)
            ->where('gaji_conjunction.deletedAt', null)
            ->findAll();

        $cadangan = $this->asArray()
            ->select('employee_id, nominal')
            ->join('tunjangan', 'tunjangan.id = gaji_conjunction.tunjangan_id')
            ->whereIn('gaji_conjunction.employee_id', $employeeIds)
            ->where('tunjangan.is_cadangan', 1)
            ->where('gaji_conjunction.deletedAt', null)
            ->findAll();

        $mapGajiHarian = [];
        $mapCadangan = [];

        foreach ($gajiPokok as $g) {
            $mapGajiHarian[$g['employee_id']] = $g['nominal'];
        }

        foreach ($cadangan as $c) {
            $mapCadangan[$c['employee_id']] = $c['nominal'];
        }

        return [
            'gajiHarian' => $mapGajiHarian,
            'cadangan' => $mapCadangan
        ];
    }
}
