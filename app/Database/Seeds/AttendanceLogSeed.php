<?php

namespace App\Database\Seeds;

use App\Models\AttendancesLogModel;
use App\Models\EmployeesModel;
use CodeIgniter\Database\Seeder;

class AttendanceLogSeed extends Seeder
{
    public function run()
    {
        $month = "06";
        $year = "2024";
        $companyID = "15";
        $attendancesUnitID = "2";
        $mulaiMasuk = "16:00:00";

        $modelEmployees = new EmployeesModel();
        $modelAttendanceLog = new AttendancesLogModel();

        $employeeData = $modelEmployees->where('company_id', $companyID)
            ->where('employees.deletedAt', null)
            ->findAll();

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $allDates = array();
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $allDates[] = $date;
        }

        foreach ($allDates as $d) {
            foreach ($employeeData as $e) {
                $modelAttendanceLog->insert([
                    'company_id' => $companyID,
                    'employees_id' => $e['id'],
                    'attendances_unit_id' => $attendancesUnitID,
                    'date_create' => "$d $mulaiMasuk"
                ]);
            }
        }
    }
}
