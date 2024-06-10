<?php

namespace App\Models;

use CodeIgniter\Model;

class PayrollGajiHarianModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'payroll_gaji_harian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $allowedFields    = [];

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

    public function getList($payrollID)
    {
        $result = $this->asArray()->select('payroll_gaji_harian.*, jam_kerja.jenis')
            ->join('jam_kerja', 'jam_kerja.id = payroll_gaji_harian.jam_kerja_id', 'left')
            ->where('payroll_gaji_harian.payroll_id', $payrollID)
            ->findAll();
        return $result;
    }

    public function generate($payrollID, $companyID, $employeeID, $yearMonth)
    {
        // declare model
        $AttendancesModel = new AttendancesModel();
        $payrollGajiModel = new PayrollGajiConjunctionModel();
        $employeeJamKerjaModel = new EmployeeJamKerjaModel();
        // delete first
        $this->where('employee_id', $employeeID)
            ->where('year_month', $yearMonth)
            ->delete();

        $attendancesInMonth = $AttendancesModel->where('employee_id', $employeeID)
            ->where('year_month', $yearMonth)
            ->findAll();

        $totalNominalGajiDiterima = 0;
        $gajiHarian = $payrollGajiModel->select('payroll_gaji_conjunction.nominal')
            ->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id')
            ->where('tunjangan.is_gaji_harian', '1')
            ->where('payroll_gaji_conjunction.payroll_id', $payrollID)
            ->first();

        // get nominal uang cadangan
        $gajiCadangan = $payrollGajiModel->select('payroll_gaji_conjunction.nominal')
            ->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id')
            ->where('tunjangan.is_cadangan', '1')
            ->where('payroll_gaji_conjunction.payroll_id', $payrollID)
            ->first();

        foreach ($attendancesInMonth as $p) {
            // if ($p['status'] == "HADIR_H") {
            // set uang gaji (jmlh hadir x (gaji harian + uang cadangan))

            $jamKerjaDetail = $employeeJamKerjaModel->getJamKerjaDetailByEmployeeId($p['periode'], $employeeID);
            $totalJamKerja = static::totalJamKerja($p['id']);
            $nominalDiterima = (($gajiHarian['nominal'] + $gajiCadangan['nominal']) / 7) * $totalJamKerja;


            $this->insert([
                'company_id' => $companyID,
                'payroll_id' => $payrollID,
                'employee_id' => $employeeID,
                'jam_kerja_id' => $jamKerjaDetail['jam_kerja_id'],
                'tanggal' => $p['periode'],
                'year_month' => $yearMonth,
                'jam_masuk' => $p['checkin'],
                'jam_istirahat_mulai' => $jamKerjaDetail['jam_istirahat_mulai'],
                'jam_istirahat_selesai' => $jamKerjaDetail['jam_istirahat_selesai'],
                'jam_pulang' => $p['checkout'],
                'total_jam' => $totalJamKerja,
                'nominal_gaji_harian' => $gajiHarian['nominal'],
                'nominal_cadangan' => $gajiCadangan['nominal'],
                'nominal_diterima' => $nominalDiterima
            ]);
            // }
            $totalNominalGajiDiterima += $nominalDiterima;
        }

        // SUM UANG GAJI
        return $totalNominalGajiDiterima;
    }

    static function totalJamKerja($attendanceId)
    {
        $attendancesModel = new AttendancesModel();
        $employeeJamKerjaModel = new EmployeeJamKerjaModel();

        $attendance = $attendancesModel->find($attendanceId);
        $jamKerjaDetail = $employeeJamKerjaModel->getJamKerjaDetailByEmployeeId($attendance['periode'], $attendance['employee_id']);

        $checkIn = $attendance['checkin'];
        $jamIstirahatMulai = $jamKerjaDetail['jam_istirahat_mulai'];
        $jamIstirahatSelesai = $jamKerjaDetail['jam_istirahat_selesai'];

        if (strtotime($attendance['checkout']) > strtotime($jamKerjaDetail['jam_pulang'])) {
            $checkOut = $jamKerjaDetail['jam_pulang'];
        } else {
            $checkOut = $attendance['checkout'];
        }

        $totalJamKerja = 0;

        if (strtotime($attendance['checkout']) > strtotime($jamKerjaDetail['jam_istirahat_mulai'])) {
            // PULANG LEBIH DARI JAM ISTIRAHAT
            $pulangMasuk = static::selisihWaktu($checkIn, $checkOut);
            $istirahat = static::selisihWaktu($jamIstirahatMulai, $jamIstirahatSelesai);
            $totalJamKerja = $pulangMasuk - $istirahat;
        } else {
            // PULANG KURANG DARI JAM ISTIRAHAT
            $pulangMasuk = static::selisihWaktu($checkIn, $checkOut);
            $totalJamKerja = $pulangMasuk;
        }

        return $totalJamKerja;
    }
    static function selisihWaktu($start, $finish)
    {
        if ($start != null && $finish != null) {
            list($jamMasuk, $menitMasuk) = explode(":", $start);
            list($jamKeluar, $menitKeluar) = explode(":", $finish);

            $selisihJam = $jamKeluar - $jamMasuk;
            $selisihMenit = $menitKeluar - $menitMasuk;

            if ($selisihMenit < 0) {
                $selisihJam--;
                $selisihMenit += 60;
            }
            $totalSelisih = $selisihJam + ($selisihMenit / 60);
            return number_format($totalSelisih, 2, '.', '');
        } else {
            return 0;
        }
    }
}
