<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>HR Outsourcing Salary Payment</h1>
        <div class="section-header-breadcrumb">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("hr-outsourcing-sallary-payment"); ?>">
                Kembali
            </a>
            <?php if (empty($data)) : ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php else : ?>
                <button class="btn btn-show-form btn-save float-right btn-updaye-parent">
                    Update
                </button>
            <?php endif  ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form class="karyawan-form" role="form" method="POST">
                <input type="hidden" name="id" id="id">
                
                <!-- Header Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Header Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="departemen">Departemen</label>
                                    <select class="form-control select2" id="departemen" name="departemen" required>
                                        <option value="">Pilih Department</option>
                                        <?php foreach ($departement as $d): ?>
                                            <option <?= !empty($data) ? ($data['divisi_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>"><?= $d['divisi'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="company">Perusahaan</label>
                                    <select class="form-control select2" id="company" name="company" required disabled>
                                        <option value="">Pilih Perusahaan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                 <div class="card mb-4">
                    <div class="card-header">
                        <h5>Detail Karyawan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="employee">Karyawan</label>
                                    <select class="form-control select2" id="employee" name="employee">
                                        <option value="">Pilih Karyawan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tanggal_masuk_kerja">Tanggal Masuk Kerja</label>
                                    <input type="date" class="form-control" id="tanggal_masuk_kerja" name="tanggal_masuk_kerja">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="form-actions text-right">
                    <button type="button" class="btn btn-secondary mr-2" onclick="resetForm()">
                        <i class="fas fa-sync-alt"></i> Reset
                    </button>
                    <button type="button" class="btn btn-primary btn-submit">
                        <i class="fas fa-plus"></i> Add Data
                    </button>
                </div>
            </form>
            
            <!-- Data Table Section -->
            <div class="row mt-4">
                <div class="row justify-content-end mb-3">
                    <div class="col-md-4">
                        <input class="form-control search form-out-search" placeholder="Cari Nama / No Badge" />
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center" rowspan="3">NO</th>
                                <th class="text-center" rowspan="3">TMK</th>
                                <th class="text-center" rowspan="3">NO BADGE</th>
                                <th class="text-center" rowspan="3">NAMA</th>
                                <th class="text-center" colspan="4">UDANG</th>
                                <th class="text-center" colspan="4">KEPAH</th>
                                <th class="text-center" colspan="4">KPTG</th>
                                <th class="text-center" colspan="4">JAM KERJA</th>
                                <th class="text-center" rowspan="3">TOTAL KG</th>
                                <th class="text-center" rowspan="3">TOTAL JAM</th>
                                <th class="text-center" rowspan="3">JLH ORG</th>
                                <th class="text-center" rowspan="3">(Rp)</th>
                                <th class="text-center" rowspan="3">SUBSIDI RP.</th>
                                <th class="text-center" rowspan="3">BOR. / JAM (Rp)</th>
                                <th class="text-center" colspan="4">KG / JAM</th>
                            </tr>
                            <tr>
                                <th colspan="1">AC</th>
                                <th colspan="1">SK</th>
                                <th colspan="1">MB</th>
                                <th colspan="1">ML</th>
                                <th colspan="1">AC</th>
                                <th colspan="1">SK</th>
                                <th colspan="1">MB</th>
                                <th colspan="1">ML</th>
                                <th colspan="1">AC</th>
                                <th colspan="1">SK</th>
                                <th colspan="1">MB</th>
                                <th colspan="1">ML</th>
                                <th colspan="1">UDANG</th>
                                <th colspan="1">KEPAH</th>
                                <th colspan="1">KPTG</th>
                                <th colspan="1">ML</th>
                                <th colspan="1">AC</th>
                                <th colspan="1">SK</th>
                                <th colspan="1">MB</th>
                                <th colspan="1">ML</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table">
                            <!-- Data rows inserted here -->
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</section>
<script>
// Global variables to store data
// Pastikan ini didefinisikan di bagian atas script Anda
var editMode = <?= !empty($data) ? 'true' : 'false' ?>;
var headerData = {
    departemen: '<?= $data['divisi_id'] ?? '' ?>',
    company: '<?= $data['company_id'] ?? '' ?>',
};

var paymentDataString = '<?= isset($data['payment_data']) ? addslashes($data['payment_data']) : '[]' ?>';

try {
    // Parse the JSON string into a JavaScript array
    var paymentData = JSON.parse(paymentDataString);
    
    // Now you can safely use forEach
    paymentData.forEach(function(payment) {
        addToTable(payment);
    });
} catch (e) {
    console.error('Error parsing payment data:', e);
    alert('Gagal memproses data pembayaran');
}

var currentEditId = '<?= $data['id'] ?? '' ?>';

// Initialize Select2 and event handlers
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: "bootstrap-5",
        allowClear: true
    });

    // Check if we're in edit mode (URL has ID parameter)

    $('#departemen').on('change', function() {
        var departemen_id = $(this).val();
        headerData.departemen = departemen_id;
        
        // Reset and disable dependent fields
        $('#company').val(null).trigger('change.select2').prop('disabled', !departemen_id);
        $('#employee').val(null).trigger('change.select2').prop('disabled', true);
        
        if (departemen_id) {
            loadCompanies(departemen_id);
        }
    });

    // Company change event handler
    $('#company').on('change', function() {
        var company_id = $(this).val();
        headerData.company = company_id;
        $('#employee').val(null).trigger('change.select2').prop('disabled', !company_id);
        
        if(company_id) {
            loadEmployees(company_id);
        }
    });

    // Employee change event handler
    $('#employee').on('change', function() {
        let tanggal_masuk_kerja = $(this).find(':selected').data('tanggal_masuk_kerja');
        $('#tanggal_masuk_kerja').val(tanggal_masuk_kerja || '');
    });

    // Initialize form based on edit mode
    if (editMode) {
        initializeEditMode();
    }

    // Other event handlers...
    $('.btn-submit').on('click', function() {
        addToTable()
    });
    $('.btn-save').on('click', saveData);
    $('.btn-discard').on('click', function() {
        if (confirm('Apakah Anda yakin ingin membatalkan perubahan?')) {
            window.location.href = $(this).attr('href');
        }
    });
});

// Function to load companies based on department
function loadCompanies(departemen_id) {
    if (!departemen_id) return;
    
    $.ajax({
        url: '<?= base_url('/hr-outsourcing-sallary-payment/getHrCompanyOutSourcing') ?>',
        type: 'POST',
        dataType: 'json',
        data: {
            departemen_id: departemen_id,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        success: function(response) {
            $('#company').empty().append('<option value="">Select Company</option>');
            
            if(response.data && Array.isArray(response.data)) {
                $.each(response.data, function(index, item) {
                    var isSelected = (editMode && headerData.company == item.id);
                    $('#company').append($('<option>', {
                        value: item.id,
                        text: item.name,
                        selected: isSelected
                    }));
                });
            }
            
            // In edit mode, ensure company is selected after options are loaded
            if (editMode && headerData.company) {
                setTimeout(function() {
                    $('#company').val(headerData.company).trigger('change.select2');
                }, 100);
            }
        },
        error: function(xhr, status, error) {
            console.error(error);
            alert('Gagal memuat data perusahaan');
        }
    });
}

// Function to load employees based on company
function loadEmployees(company_id) {
    if (!company_id) return;
    
    $.ajax({
        url: '<?= base_url('/hr-outsourcing-sallary-payment/getHrEmployeeOutSourcing') ?>',
        type: 'POST',
        dataType: 'json',
        data: {
            company_id: company_id,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        success: function(response) {
            $('#employee').empty().append('<option value="">Select Employee</option>');
            if(response.data && Array.isArray(response.data)) {
                $.each(response.data, function(index, item) {
                    $('#employee').append($('<option>', {
                        value: item.id,
                        text: item.nama,
                        'data-badge': item.badge,
                        'data-tanggal_masuk_kerja': item.tanggal_masuk_kerja
                    }));
                });
            }
        },
        error: function(xhr, status, error) {
            console.error(error);
            alert('Gagal memuat data karyawan');
        }
    });
}

// Function to initialize edit mode
function initializeEditMode() {
    if (!headerData.departemen) return;    
    // Set department and trigger change
    $('#departemen').val(headerData.departemen).trigger('change.select2');
    loadCompanies(headerData.departemen)
    loadEmployees(headerData.company)
}

    


// Function to add employee data to table (for edit mode)
function addEmployeeToTable(employee) {
    // Find employee in dropdown
    var $employeeOption = $('#employee').find('option[value="' + employee.employee_id + '"]');
    
    if ($employeeOption.length > 0) {
        var employeeName = $employeeOption.text();
        var badge = $employeeOption.data('badge');
        var tanggalMasuk = employee.tanggal_masuk_kerja;
        
        var newRow = `
            <tr data-employee-id="${employee.employee_id}">
                <td>${$('#dataTable tbody tr').length + 1}</td>
                <td><input type="text" class="form-control form-control-sm" style="min-width: 120px;" value="${tanggalMasuk}" readonly></td>
                <td><input type="text" class="form-control form-control-sm" style="min-width: 120px;" value="${badge}" readonly></td>
                <td><input type="text" class="form-control form-control-sm" style="min-width: 150px;" data-id="${employee.employee_id}" value="${employeeName}" readonly></td>

                <!-- UDANG -->
                <td><input type="number" class="form-control form-control-sm udang-ac" style="min-width: 100px;" value="${employee.udang.ac || 0}"></td>
                <td><input type="number" class="form-control form-control-sm udang-sk" style="min-width: 100px;" value="${employee.udang.sk || 0}"></td>
                <td><input type="number" class="form-control form-control-sm udang-mb" style="min-width: 100px;" value="${employee.udang.mb || 0}"></td>
                <td><input type="number" class="form-control form-control-sm udang-ml" style="min-width: 100px;" value="${employee.udang.ml || 0}"></td>

                <!-- KEPAH -->
                <td><input type="number" class="form-control form-control-sm kepah-ac" style="min-width: 100px;" value="${employee.kepah.ac || 0}"></td>
                <td><input type="number" class="form-control form-control-sm kepah-sk" style="min-width: 100px;" value="${employee.kepah.sk || 0}"></td>
                <td><input type="number" class="form-control form-control-sm kepah-mb" style="min-width: 100px;" value="${employee.kepah.mb || 0}"></td>
                <td><input type="number" class="form-control form-control-sm kepah-ml" style="min-width: 100px;" value="${employee.kepah.ml || 0}"></td>

                <!-- KPTG -->
                <td><input type="number" class="form-control form-control-sm kptg-ac" style="min-width: 100px;" value="${employee.kptg.ac || 0}"></td>
                <td><input type="number" class="form-control form-control-sm kptg-sk" style="min-width: 100px;" value="${employee.kptg.sk || 0}"></td>
                <td><input type="number" class="form-control form-control-sm kptg-mb" style="min-width: 100px;" value="${employee.kptg.mb || 0}"></td>
                <td><input type="number" class="form-control form-control-sm kptg-ml" style="min-width: 100px;" value="${employee.kptg.ml || 0}"></td>

                <!-- JAM KERJA -->
                <td><input type="number" class="form-control form-control-sm jam-kerja-udang" style="min-width: 100px;" value="${employee.jam_kerja.udang || 0}"></td>
                <td><input type="number" class="form-control form-control-sm jam-kerja-kepah" style="min-width: 100px;" value="${employee.jam_kerja.kepah || 0}"></td>
                <td><input type="number" class="form-control form-control-sm jam-kerja-kptg" style="min-width: 100px;" value="${employee.jam_kerja.kptg || 0}"></td>
                <td><input type="number" class="form-control form-control-sm jam-kerja-ml" style="min-width: 100px;" value="${employee.jam_kerja.ml || 0}"></td>

                <!-- TOTAL & KALKULASI -->
                <td><input type="number" class="form-control form-control-sm total-kg" style="min-width: 120px;" value="${employee.total.kg || 0}"></td>
                <td><input type="number" class="form-control form-control-sm total-jam" style="min-width: 120px;" value="${employee.total.jam || 0}"></td>
                <td><input type="number" class="form-control form-control-sm jlh-org" style="min-width: 100px;" value="${employee.total.org || 0}"></td>
                <td><input type="number" class="form-control form-control-sm rupiah" style="min-width: 120px;" value="${employee.rupiah || 0}"></td>
                <td><input type="number" class="form-control form-control-sm subsidi-rupiah" style="min-width: 120px;" value="${employee.subsidi_rupiah || 0}"></td>
                <td><input type="number" class="form-control form-control-sm borongan-per-jam" style="min-width: 120px;" value="${employee.borongan_per_jam || 0}"></td>

                <!-- KG / JAM -->
                <td><input type="number" class="form-control form-control-sm kg-per-jam-ac" style="min-width: 90px;" value="${employee.kg_per_jam.ac || 0}"></td>
                <td><input type="number" class="form-control form-control-sm kg-per-jam-sk" style="min-width: 90px;" value="${employee.kg_per_jam.sk || 0}"></td>
                <td><input type="number" class="form-control form-control-sm kg-per-jam-mb" style="min-width: 90px;" value="${employee.kg_per_jam.mb || 0}"></td>
                <td><input type="number" class="form-control form-control-sm kg-per-jam-ml" style="min-width: 90px;" value="${employee.kg_per_jam.ml || 0}"></td>
            </tr>
        `;

        $('#dataTable tbody').append(newRow);
    } else {
        console.warn('Employee not found in dropdown:', employee.employee_id);
    }
}

// Function to add data to table
function addToTable(payment = null) {
    console.log(payment)
    var employeeId, employeeName, tanggalMasuk, badge;
    
    if (payment) {
        // If data comes from backend (edit mode)
        employeeId = payment.employee_id;
        employeeName = payment.employee_name;
        tanggalMasuk = payment.tanggal_masuk_kerja;
        badge = payment.badge;
    } else {
        // If adding new employee (normal mode)
        employeeId = $('#employee').val();
        if (!employeeId) {
            alert('Harap pilih karyawan terlebih dahulu!');
            return;
        }
        
        // Check if employee already exists in table
        if ($('#dataTable tbody tr[data-employee-id="' + employeeId + '"]').length > 0) {
            alert('Karyawan ini sudah ditambahkan ke tabel!');
            return;
        }

        employeeName = $('#employee option:selected').text();
        tanggalMasuk = $('#tanggal_masuk_kerja').val();
        badge = $('#employee').find(':selected').data('badge');
    }

    // Prepare the data values - use backend data if available, otherwise default to 0
    var udangAc = payment ? payment.udang.ac : 0;
    var udangSk = payment ? payment.udang.sk : 0;
    var udangMb = payment ? payment.udang.mb : 0;
    var udangMl = payment ? payment.udang.ml : 0;
    
    var kepahAc = payment ? payment.kepah.ac : 0;
    var kepahSk = payment ? payment.kepah.sk : 0;
    var kepahMb = payment ? payment.kepah.mb : 0;
    var kepahMl = payment ? payment.kepah.ml : 0;
    
    var kptgAc = payment ? payment.kptg.ac : 0;
    var kptgSk = payment ? payment.kptg.sk : 0;
    var kptgMb = payment ? payment.kptg.mb : 0;
    var kptgMl = payment ? payment.kptg.ml : 0;
    
    var jamKerjaUdang = payment ? payment.jam_kerja.udang : 0;
    var jamKerjaKepah = payment ? payment.jam_kerja.kepah : 0;
    var jamKerjaKptg = payment ? payment.jam_kerja.kptg : 0;
    var jamKerjaMl = payment ? payment.jam_kerja.ml : 0;
    
    var totalKg = payment ? payment.total.kg : 0;
    var totalJam = payment ? payment.total.jam : 0;
    var jlhOrg = payment ? payment.total.org : 0;
    var rupiah = payment ? payment.rupiah : 0;
    var subsidiRupiah = payment ? payment.subsidi_rupiah : 0;
    var boronganPerJam = payment ? payment.borongan_per_jam : 0;
    
    var kgPerJamAc = payment ? payment.kg_per_jam.ac : 0;
    var kgPerJamSk = payment ? payment.kg_per_jam.sk : 0;
    var kgPerJamMb = payment ? payment.kg_per_jam.mb : 0;
    var kgPerJamMl = payment ? payment.kg_per_jam.ml : 0;

    var newRow = `
        <tr data-employee-id="${employeeId}">
            <td>${$('#dataTable tbody tr').length + 1}</td>
            <td><input type="text" class="form-control form-control-sm" style="min-width: 120px;" value="${tanggalMasuk}" readonly></td>
            <td><input type="text" class="form-control form-control-sm" style="min-width: 120px;" value="${badge}" readonly></td>
            <td><input type="text" class="form-control form-control-sm" style="min-width: 150px;" data-id="${employeeId}" value="${employeeName}" readonly></td>

            <!-- UDANG -->
            <td><input type="number" class="form-control form-control-sm udang-ac" style="min-width: 100px;" value="${udangAc}"></td>
            <td><input type="number" class="form-control form-control-sm udang-sk" style="min-width: 100px;" value="${udangSk}"></td>
            <td><input type="number" class="form-control form-control-sm udang-mb" style="min-width: 100px;" value="${udangMb}"></td>
            <td><input type="number" class="form-control form-control-sm udang-ml" style="min-width: 100px;" value="${udangMl}"></td>

            <!-- KEPAH -->
            <td><input type="number" class="form-control form-control-sm kepah-ac" style="min-width: 100px;" value="${kepahAc}"></td>
            <td><input type="number" class="form-control form-control-sm kepah-sk" style="min-width: 100px;" value="${kepahSk}"></td>
            <td><input type="number" class="form-control form-control-sm kepah-mb" style="min-width: 100px;" value="${kepahMb}"></td>
            <td><input type="number" class="form-control form-control-sm kepah-ml" style="min-width: 100px;" value="${kepahMl}"></td>

            <!-- KPTG -->
            <td><input type="number" class="form-control form-control-sm kptg-ac" style="min-width: 100px;" value="${kptgAc}"></td>
            <td><input type="number" class="form-control form-control-sm kptg-sk" style="min-width: 100px;" value="${kptgSk}"></td>
            <td><input type="number" class="form-control form-control-sm kptg-mb" style="min-width: 100px;" value="${kptgMb}"></td>
            <td><input type="number" class="form-control form-control-sm kptg-ml" style="min-width: 100px;" value="${kptgMl}"></td>

            <!-- JAM KERJA -->
            <td><input type="number" class="form-control form-control-sm jam-kerja-udang" style="min-width: 100px;" value="${jamKerjaUdang}"></td>
            <td><input type="number" class="form-control form-control-sm jam-kerja-kepah" style="min-width: 100px;" value="${jamKerjaKepah}"></td>
            <td><input type="number" class="form-control form-control-sm jam-kerja-kptg" style="min-width: 100px;" value="${jamKerjaKptg}"></td>
            <td><input type="number" class="form-control form-control-sm jam-kerja-ml" style="min-width: 100px;" value="${jamKerjaMl}"></td>

            <!-- TOTAL & KALKULASI -->
            <td><input type="number" class="form-control form-control-sm total-kg" style="min-width: 120px;" value="${totalKg}"></td>
            <td><input type="number" class="form-control form-control-sm total-jam" style="min-width: 120px;" value="${totalJam}"></td>
            <td><input type="number" class="form-control form-control-sm jlh-org" style="min-width: 100px;" value="${jlhOrg}"></td>
            <td><input type="number" class="form-control form-control-sm rupiah" style="min-width: 120px;" value="${rupiah}"></td>
            <td><input type="number" class="form-control form-control-sm subsidi-rupiah" style="min-width: 120px;" value="${subsidiRupiah}"></td>
            <td><input type="number" class="form-control form-control-sm borongan-per-jam" style="min-width: 120px;" value="${boronganPerJam}"></td>

            <!-- KG / JAM -->
            <td><input type="number" class="form-control form-control-sm kg-per-jam-ac" style="min-width: 90px;" value="${kgPerJamAc}"></td>
            <td><input type="number" class="form-control form-control-sm kg-per-jam-sk" style="min-width: 90px;" value="${kgPerJamSk}"></td>
            <td><input type="number" class="form-control form-control-sm kg-per-jam-mb" style="min-width: 90px;" value="${kgPerJamMb}"></td>
            <td><input type="number" class="form-control form-control-sm kg-per-jam-ml" style="min-width: 90px;" value="${kgPerJamMl}"></td>
        </tr>
    `;

    $('#dataTable tbody').append(newRow);
    
    // Only reset form if adding new employee (not in edit mode)
    if (!payment) {
        resetForm();
    }
}

// Function to save data
function saveData() {
    // Validate header data
    if (!headerData.departemen || !headerData.company) {
        alert('Harap lengkapi data header terlebih dahulu!');
        return;
    }

    // Validate if there are employees in the table
    if ($('#dataTable tbody tr').length === 0) {
        alert('Harap tambahkan minimal satu karyawan!');
        return;
    }

    // Collect all employee data from the table
    var employeeData = [];
    $('#dataTable tbody tr').each(function() {
        var $row = $(this);
        var employeeId = $row.data('employee-id');
        
        employeeData.push({
            employee_id: employeeId,
            employee_name: $row.find('td:eq(3) input').val(), // Get name from 4th column
            badge: $row.find('td:eq(2) input').val(),        // Get badge from 3rd column
            tanggal_masuk_kerja: $row.find('td:eq(1) input').val(),
            tanggal_masuk_kerja: $row.find('td:eq(1) input').val(),
            udang: {
                ac: parseFloat($row.find('.udang-ac').val()) || 0,
                sk: parseFloat($row.find('.udang-sk').val()) || 0,
                mb: parseFloat($row.find('.udang-mb').val()) || 0,
                ml: parseFloat($row.find('.udang-ml').val()) || 0
            },
            kepah: {
                ac: parseFloat($row.find('.kepah-ac').val()) || 0,
                sk: parseFloat($row.find('.kepah-sk').val()) || 0,
                mb: parseFloat($row.find('.kepah-mb').val()) || 0,
                ml: parseFloat($row.find('.kepah-ml').val()) || 0
            },
            kptg: {
                ac: parseFloat($row.find('.kptg-ac').val()) || 0,
                sk: parseFloat($row.find('.kptg-sk').val()) || 0,
                mb: parseFloat($row.find('.kptg-mb').val()) || 0,
                ml: parseFloat($row.find('.kptg-ml').val()) || 0
            },
            jam_kerja: {
                udang: parseFloat($row.find('.jam-kerja-udang').val()) || 0,
                kepah: parseFloat($row.find('.jam-kerja-kepah').val()) || 0,
                kptg: parseFloat($row.find('.jam-kerja-kptg').val()) || 0,
                ml: parseFloat($row.find('.jam-kerja-ml').val()) || 0
            },
            total: {
                kg: parseFloat($row.find('.total-kg').val()) || 0,
                jam: parseFloat($row.find('.total-jam').val()) || 0,
                org: parseFloat($row.find('.jlh-org').val()) || 0
            },
            rupiah: parseFloat($row.find('.rupiah').val()) || 0,
            subsidi_rupiah: parseFloat($row.find('.subsidi-rupiah').val()) || 0,
            borongan_per_jam: parseFloat($row.find('.borongan-per-jam').val()) || 0,
            kg_per_jam: {
                ac: parseFloat($row.find('.kg-per-jam-ac').val()) || 0,
                sk: parseFloat($row.find('.kg-per-jam-sk').val()) || 0,
                mb: parseFloat($row.find('.kg-per-jam-mb').val()) || 0,
                ml: parseFloat($row.find('.kg-per-jam-ml').val()) || 0
            }
        });
    });

    // Prepare data for saving
    var data = {
        departemen: $('#departemen').val(),
        company: $('#company').val(),
        employee_data: JSON.stringify(employeeData),
        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
    };

    var url, method;
    if (editMode) {
        url = '<?= base_url('/hr-outsourcing-sallary-payment/update/') ?>' + currentEditId;
        method = 'POST';
    } else {
        url = '<?= base_url('/hr-outsourcing-sallary-payment/store') ?>';
        method = 'POST';
    }

    // Show loading indicator
    $('.btn-save').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

    $.ajax({
        url: url,
        type: method,
        dataType: 'json',
        data: data,
        success: function(response) {
            $('.btn-save').prop('disabled', false).html('Simpan');
            
            if (response.status === 'success') {
                alert(response.message);
                if (!editMode) {
                    // Redirect to edit page for the newly created record
                    window.location.href = '<?= base_url('/hr-outsourcing-sallary-payment') ?>';
                }
            } else {
                alert(response.message);
            }
        },
        error: function(xhr, status, error) {
            $('.btn-save').prop('disabled', false).html('Simpan');
            console.error(error);
            alert('Gagal menyimpan data');
        }
    });
}

// Reset form function
function resetForm() {
    $('#tanggal_masuk_kerja').val('');
    $('#employee').val(null).trigger('change');
}
</script>
<?= $this->endSection(); ?>