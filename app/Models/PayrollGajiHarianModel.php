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
        $payrollModel = new PayrollsModel();

        $payroll = $payrollModel->where('id', $payrollID)->first();

        $result = $this->asArray()->select('payroll_gaji_harian.*, jam_kerja.jenis')
            ->join('jam_kerja', 'jam_kerja.id = payroll_gaji_harian.jam_kerja_id', 'left')
            ->where('payroll_gaji_harian.payroll_id', $payrollID)
            ->where('payroll_gaji_harian.year_month', $payroll['year_month'])
            ->orderBy('tanggal', "asc")
            ->findAll();
        return $result;
    }

    public function generateAmt(
        $mapEmployeePayroll,
        $mapGajiHarian,
        $mapGajiCadangan,
        $companyId,
        $employeeIds,
        $yearMonth,
        $startDate,
        $endDate,
        $payrollIds
    ) {
        // models
        $AttendancesModel = new AttendancesModel();
        $employeeJamKerjaModel = new EmployeeJamKerjaModel();
        $bigDaysModel = new BigDaysModel();
        $payrollCustomGajiHarianModel = new PayrollCustomGajiHarianModel();

        // Hapus dulu semua
        $this->db->table('payroll_gaji_harian')
            ->whereIn('employee_id', $employeeIds)
            ->where('year_month', $yearMonth)
            ->delete();

        // delete by payroll if exists
        $this->db->table('payroll_gaji_harian')
            ->whereIn('payroll_id', $payrollIds)
            ->delete();

        // Ambil semua jam kerja detail untuk semua employee dalam range SEKALI (anti N+1)
        // diasumsikan method ini mengembalikan struktur: [employee_id => [tanggal => jamKerjaDetail]]
        $jamKerjaDetailMap = $employeeJamKerjaModel->getJamKerjaDetailByEmployeeIdAmt(
            $startDate,
            $endDate,
            $employeeIds
        );
        // Ambil semua attendance untuk employeeIds di range SEKALI
        $attendancesInRange = $AttendancesModel
            ->asArray()
            ->whereIn('employee_id', $employeeIds)
            ->where('periode >=', $startDate)
            ->where('periode <=', $endDate)
            ->groupBy(['employee_id', 'periode'])   // penting!
            ->findAll();

        $bigDays = $bigDaysModel->where('company_id', $companyId)->where('deletedAt', null)->findAll();
        $tanggalBigDays = array_column($bigDays, 'date');

        $insertRows = [];
        $totalNominalGajiDiterimaPerPayroll = []; // if you want to accumulate per payroll_id
        $mapCustomGajiHarian = $payrollCustomGajiHarianModel->getMapPayrollCustomGajiHarian(
            $employeeIds,
            $startDate,
            $endDate
        );

        foreach ($attendancesInRange as $p) {
            $employeeID = $p['employee_id'];
            $tanggal = $p['periode'];
            $dayName = date('D', strtotime($tanggal)); // get nama hari
            $statusLibur = false;

            // cari payroll id untuk employee ini
            $payrollID = $mapEmployeePayroll[$employeeID] ?? null;
            if ($payrollID === null && $p['status'] == "ALPHA_A") {
                // skip kalau payroll tidak ditemukan (safety)
                continue;
            }

            if (in_array($tanggal, $tanggalBigDays)) {
                // Hari Besar
                $statusLibur = true;
            }

            if ($dayName == 'Sun') {
                // Hari Minggu
                $statusLibur = true;
            }

            // ambil jamKerjaDetail dari map (fallback null)
            $jamKerjaDetail = $jamKerjaDetailMap[$employeeID][$tanggal] ?? null;

            // hitung total jam kerja — sesuaikan static::totalJamKerja() dengan input yang diperlukan
            // aku asumsikan fungsi bisa menerima jamKerjaDetail atau attendance id; jika beda, sesuaikan.
            $totalJamKerja = 0;
            if ($jamKerjaDetail) {
                // kalau totalJamKerja menerima hela jamKerjaDetail row
                $totalJamKerja = static::totalJamKerjaAmt($jamKerjaDetail, $p['checkin'] ?? null, $p['checkout'] ?? null);
            } else {
                // fallback: coba hitung dari checkin/checkout (kalau tersedia)
                if (!empty($p['checkin']) && !empty($p['checkout'])) {
                    $in = new \DateTime($p['checkin']);
                    $out = new \DateTime($p['checkout']);
                    $diff = $out->diff($in);
                    $totalJamKerja = $diff->h + ($diff->i / 60);
                }
            }

            // nominal gaji harian & cadangan per employee (fallback 0)
            // $nominalGajiHarian = $mapGajiHarian[$employeeID] ?? 0;
            // $nominalGajiCadangan = $mapGajiCadangan[$employeeID] ?? 0;
            // $nominalDiterima =  $nominalGajiHarian + $nominalGajiCadangan;

            if ($p['isApproved'] && !in_array($p['status'], ["LIBUR_L", "ALPHA_A"]) && $statusLibur == false) {
                // Di Approved Wajib Dibayar
                // gaji harian + tambahan
                // $nominalDiterima = $nominalGajiHarian + $nominalGajiCadangan;
                $customGajiHarian = $mapCustomGajiHarian[$employeeID][$tanggal] ?? null;

                $nominalGajiHarian = $customGajiHarian != null ? $customGajiHarian['nominal_gaji_harian'] : ($mapGajiHarian[$employeeID] ?? 0);
                $nominalGajiCadangan = $customGajiHarian != null ? $customGajiHarian['nominal_cadangan'] : ($mapGajiCadangan[$employeeID] ?? 0);
                $nominalDiterima = $customGajiHarian != null ? $customGajiHarian['nominal'] : ($nominalGajiHarian + $nominalGajiCadangan);
            } else {
                // ga di approve 
                if (!$p['isApproved'] && in_array($p['status'], ["POTONG GAJI_PG", "OFF_OFF", "HADIR_H"])) {
                    // JIKA PG TETEP DIGAJI (TAPI UDAH MASUK KE POTONGAN ABSENSI)
                    $customGajiHarian = $mapCustomGajiHarian[$employeeID][$tanggal] ?? null;

                    $nominalGajiHarian = $customGajiHarian != null ? $customGajiHarian['nominal_gaji_harian'] : ($mapGajiHarian[$employeeID] ?? 0);
                    $nominalGajiCadangan = $customGajiHarian != null ? $customGajiHarian['nominal_cadangan'] : ($mapGajiCadangan[$employeeID] ?? 0);
                    $nominalDiterima = $customGajiHarian != null ? $customGajiHarian['nominal'] : ($nominalGajiHarian + $nominalGajiCadangan);
                } else {
                    // LANGSUNG KASIH 0
                    $nominalDiterima = 0;
                    $nominalGajiHarian = 0;
                    $nominalGajiCadangan = 0;
                }
            }

            $insertRows[] = [
                'company_id' => $companyId,
                'payroll_id' => $payrollID,
                'employee_id' => $employeeID,
                'jam_kerja_id' => $jamKerjaDetail['jam_kerja_id'] ?? null,
                'tanggal' => $tanggal,
                'year_month' => $yearMonth,
                'jam_masuk' => $p['checkin'] ?? null,
                'jam_istirahat_mulai' => $jamKerjaDetail['jam_istirahat_mulai'] ?? null,
                'jam_istirahat_selesai' => $jamKerjaDetail['jam_istirahat_selesai'] ?? null,
                'jam_pulang' => $p['checkout'] ?? null,
                'total_jam' => $totalJamKerja,
                'nominal_gaji_harian' => $nominalGajiHarian,
                'nominal_cadangan' => $nominalGajiCadangan,
                'nominal_diterima' => $nominalDiterima
            ];

            // accumulate per payroll id (opsional, buat update summary nanti)
            if (!isset($totalNominalGajiDiterimaPerPayroll[$employeeID])) {
                $totalNominalGajiDiterimaPerPayroll[$employeeID] = 0;
            }
            $totalNominalGajiDiterimaPerPayroll[$employeeID] += $nominalDiterima;
        }

        // return data yang akan di-insert ke payroll_gaji (caller akan insertBatch)
        // jika kamu mau langsung insert di sini: $this->insertBatch($insertRows);
        return [
            'rows' => $insertRows,
            'totals_per_payroll' => $totalNominalGajiDiterimaPerPayroll
        ];
    }

    static function totalJamKerjaAmt($jamKerjaDetail, $checkin, $checkout)
    {
        if (empty($checkin) || empty($checkout)) return 0;

        $checkInTime = strtotime($checkin);
        $checkOutTime = strtotime($checkout);

        // fallback jika checkout lebih dari jam pulang
        $jamPulang = strtotime($jamKerjaDetail['jam_pulang'] ?? $checkout);
        if ($checkOutTime > $jamPulang) {
            $checkOutTime = $jamPulang;
        }

        $totalWorkSeconds = $checkOutTime - $checkInTime;
        if ($totalWorkSeconds <= 0) return 0;

        // hitung durasi istirahat
        $istirahatMulai = strtotime($jamKerjaDetail['jam_istirahat_mulai'] ?? $checkInTime);
        $istirahatSelesai = strtotime($jamKerjaDetail['jam_istirahat_selesai'] ?? $checkInTime);

        // hanya kurangi istirahat jika checkin < jam istirahat < checkout
        if ($checkInTime < $istirahatSelesai && $checkOutTime > $istirahatMulai) {
            $istirahatStart = max($checkInTime, $istirahatMulai);
            $istirahatEnd = min($checkOutTime, $istirahatSelesai);
            $istirahatSeconds = max(0, $istirahatEnd - $istirahatStart);
            $totalWorkSeconds -= $istirahatSeconds;
        }

        return round($totalWorkSeconds / 3600, 2); // jam dengan 2 desimal
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
