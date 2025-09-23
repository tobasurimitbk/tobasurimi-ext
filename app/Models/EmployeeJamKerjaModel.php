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
}
