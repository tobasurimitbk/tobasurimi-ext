<?php

namespace App\Models;

use CodeIgniter\Model;
use Carbon\Carbon;

class EmployeeJamKerjaModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'employees_jam_kerja';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
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

    public function getJamKerjaUsedByEmployeeId($date, $employeeId)
    {
        $jamKerjaModel = new JamKerjaModel();
        $employeeModel = new EmployeesModel();
        $jamKerja = null;

        // DAPATKAN JAM KERJA YANG DIGUNAKAN OLEH KARYAWAN
        $jamKerjaEmployee = $this
            ->where('employee_id', $employeeId)
            ->where('tanggal', $date)
            ->first();

        if ($jamKerjaEmployee != null) {
            // JAM KERJA DI SET PERHARI
            $jamKerja = $jamKerjaModel->where('id', $jamKerjaEmployee['jam_kerja_id'])->first();
        } else {
            // JAM KERJA DEFAULT (AMBIL DARI DIVISI)
            $result = $employeeModel->select('divisis.jam_kerja_id')
                ->join('divisis', 'divisis.id = employees.division_id', 'left')
                ->where('employees.id', $employeeId)
                ->first();

            $jamKerja = $jamKerjaModel->where('id', $result['jam_kerja_id'])->first();
        }

        return $jamKerja;
    }

    public function getJamKerjaDetailByEmployeeId($date, $employeeId)
    {
        $employeesModel = new EmployeesModel();
        $metaDataModel = new MetadataModel();
        $jamKerjaModel = new JamKerjaModel();

        $jamKerjaDefault = $employeesModel->getSingleEmployee($employeeId);
        $jamKerjaId = $jamKerjaDefault == null ? "" : $jamKerjaDefault['jam_kerja_id'];

        $dateCarbon = Carbon::createFromFormat('Y-m-d', $date);
        $intOfDay = $dateCarbon->dayOfWeekIso; // 1=senin, dst

        $hariName = $metaDataModel->where('deletedAt', null)
            ->where('name', 'hari')
            ->where('description', $intOfDay)
            ->first()['value'];

        $jamKerjaEmployee = $this->where('employee_id', $employeeId)->where('tanggal', $date)->first();
        $selectQry = "
                jam_kerja.id AS jam_kerja_id,
                jam_kerja.jenis,
                jam_kerja.shift,
                jam_kerja_detail.*
            ";
        if ($jamKerjaEmployee != null) {
            $jamKerjaDetail = $jamKerjaModel->select($selectQry)
                ->join('jam_kerja_detail', 'jam_kerja_detail.jam_kerja_id = jam_kerja.id', 'left')
                ->where('jam_kerja.id', $jamKerjaEmployee['jam_kerja_id'])
                ->where('jam_kerja_detail.hari', $hariName)
                ->where('jam_kerja_detail.deletedAt', null)
                ->first();
        } else {
            $jamKerjaDetail = $jamKerjaModel->select($selectQry)
                ->join('jam_kerja_detail', 'jam_kerja_detail.jam_kerja_id = jam_kerja.id', 'left')
                ->where('jam_kerja.id', $jamKerjaId)
                ->where('jam_kerja_detail.hari', $hariName)
                ->where('jam_kerja_detail.deletedAt', null)
                ->first();
        }

        return $jamKerjaDetail;
    }

    public function getJamKerjaDetailByEmployeeIdAmt(
        $startDate,
        $endDate,
        $employeeIds
    ) {
        $employeesModel = new EmployeesModel();
        $metaDataModel  = new MetadataModel();
        $jamKerjaModel  = new JamKerjaModel();

        // Ambil default jam kerja ID per employee
        $employeesJamKerjaDefault = $employeesModel
            ->asArray()
            ->select("employees.id as employee_id, divisis.jam_kerja_id")
            ->join('divisis', 'divisis.id = employees.division_id', 'left')
            ->whereIn('employees.id', $employeeIds)
            ->findAll();

        $jamKerjaDefaultMap = [];
        foreach ($employeesJamKerjaDefault as $row) {
            $jamKerjaDefaultMap[$row['employee_id']] = $row['jam_kerja_id'];
        }

        // Hitung semua hari dalam range
        $dateRange = new \DatePeriod(
            new \DateTime($startDate),
            new \DateInterval('P1D'),
            (new \DateTime($endDate))->modify('+1 day')
        );

        // Ambil mapping hari (1=senin, dst) → nama hari
        $hariMeta = $metaDataModel
            ->asArray()
            ->where('deletedAt', null)
            ->where('name', 'hari')
            ->findAll();
        $hariMap = array_column($hariMeta, 'value', 'description');
        // contoh: [1 => 'Senin', 2 => 'Selasa', ...]

        // 1. Ambil semua jam kerja employee dalam range
        $jamKerjaEmployee = $this->asArray()
            ->select("employee_id, tanggal, jam_kerja_id")
            ->whereIn('employee_id', $employeeIds)
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->findAll();

        // Buat map cepat [employee_id][tanggal] => jam_kerja_id
        $jamKerjaEmployeeMap = [];
        foreach ($jamKerjaEmployee as $row) {
            $jamKerjaEmployeeMap[$row['employee_id']][$row['tanggal']] = $row['jam_kerja_id'];
        }

        // 2. Ambil semua jam_kerja_detail yang relevan (biar ga query per employee)
        $allJamKerjaIds = array_unique(array_merge(
            array_values($jamKerjaDefaultMap),
            array_column($jamKerjaEmployee, 'jam_kerja_id')
        ));

        $jamKerjaDetails = $jamKerjaModel
            ->asArray()
            ->select("jam_kerja.id as jam_kerja_id, jam_kerja.jenis, jam_kerja_detail.*")
            ->join('jam_kerja_detail', 'jam_kerja_detail.jam_kerja_id = jam_kerja.id', 'left')
            ->whereIn('jam_kerja.id', $allJamKerjaIds)
            ->where('jam_kerja_detail.deletedAt', null)
            ->findAll();

        // Buat map [jam_kerja_id][hari] => detail
        $jamKerjaDetailMap = [];
        foreach ($jamKerjaDetails as $row) {
            $jamKerjaDetailMap[$row['jam_kerja_id']][$row['hari']] = $row;
        }

        // 3. Satukan hasil per employee + tanggal
        $result = [];

        foreach ($employeeIds as $empId) {
            foreach ($dateRange as $date) {
                $tanggal  = $date->format('Y-m-d');
                $hariIdx  = $date->format('N'); // 1=Senin dst
                $hariName = $hariMap[$hariIdx] ?? null;

                // tentukan jam_kerja_id → cek per employee/tanggal, kalau gak ada fallback ke default
                $jamKerjaId = $jamKerjaEmployeeMap[$empId][$tanggal]
                    ?? $jamKerjaDefaultMap[$empId]
                    ?? null;

                if ($jamKerjaId && isset($jamKerjaDetailMap[$jamKerjaId][$hariName])) {
                    $result[$empId][$tanggal] = $jamKerjaDetailMap[$jamKerjaId][$hariName];
                } else {
                    $result[$empId][$tanggal] = null;
                }
            }
        }

        return $result;
    }


    public function getDetailJamKerjaByEmployee($employeeId, $yearMonth)
    {
        $employeesModel = new EmployeesModel();
        $metaDataModel = new MetadataModel();
        $jamKerjaModel = new JamKerjaModel();
        Carbon::setLocale('id');

        $jamKerjaDefault = $employeesModel->getSingleEmployee($employeeId);
        $jamKerjaId = $jamKerjaDefault == null ? "" : $jamKerjaDefault['jam_kerja_id'];

        $start = Carbon::parse($yearMonth)->startOfMonth();
        $tanggal_selesai = Carbon::parse($yearMonth)->endOfMonth();
        while ($start->lte($tanggal_selesai)) {
            $dates[] = $start->copy();
            $start->addDay();
        }

        $result = [];
        foreach ($dates as $d) {
            // GET HARI ISO
            $intOfDay = $d->dayOfWeekIso;

            $hariName = $metaDataModel->where('deletedAt', null)->where('name', 'hari')->where('description', $intOfDay)->first()['value'];
            $jamKerjaEmployee = $this->where('employee_id', $employeeId)->where('tanggal', $d->format('Y-m-d'))->first();
            $selectQry = "
                jam_kerja.id AS jam_kerja_id,
                jam_kerja.jenis,
                jam_kerja.shift,
                jam_kerja_detail.*
            ";

            if ($jamKerjaEmployee != null) {
                $jamKerjaDetail = $jamKerjaModel->select($selectQry)
                    ->join('jam_kerja_detail', 'jam_kerja_detail.jam_kerja_id = jam_kerja.id', 'left')
                    ->where('jam_kerja.id', $jamKerjaEmployee['jam_kerja_id'])
                    ->where('jam_kerja_detail.hari', $hariName)
                    ->where('jam_kerja_detail.deletedAt', null)
                    ->first();
            } else {
                // Ambil Default By Divisi
                $jamKerjaDetail = $jamKerjaModel->select($selectQry)
                    ->join('jam_kerja_detail', 'jam_kerja_detail.jam_kerja_id = jam_kerja.id', 'left')
                    ->where('jam_kerja.id', $jamKerjaId)
                    ->where('jam_kerja_detail.hari', $hariName)
                    ->where('jam_kerja_detail.deletedAt', null)
                    ->first();
            }

            array_push($result, [
                'tanggal' => $d->format('d/m/Y'),
                'tanggal_text' =>  $d->isoFormat('dddd, D MMMM Y'),
                'jam_kerja' => $jamKerjaDetail
            ]);
        }

        return $result;
    }

    public function getJamKerjaKaryawan(
        $bagianId,
        $tanggal
    ) {
        $employeeModel = new EmployeesModel();
        $employeeData = $employeeModel->where('deletedAt', null)->where('bagian_id', $bagianId)->orderBy('name', "asc")->findAll();

        $dataResult = array();
        foreach ($employeeData as $e) {
            $jamKerja = $this->getJamKerjaDetailByEmployeeId(
                $tanggal,
                $e['id']
            );

            array_push($dataResult, [
                'id' => $e['id'],
                'nip' => $e['nip'],
                'name' => $e['name'],
                'jenis' => trim($jamKerja['jenis']),
                'shift' => trim($jamKerja['shift']),
            ]);
        }

        return $dataResult;
    }
}
