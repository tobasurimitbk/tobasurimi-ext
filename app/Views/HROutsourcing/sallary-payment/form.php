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
                        <!-- Table headers will be dynamically generated based on department -->
                        <thead class="thead-dark" id="tableHeader">
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

var editMode = <?= !empty($data) ? 'true' : 'false' ?>;
var headerData = {
    departemen: '<?= $data['divisi_id'] ?? '' ?>',
    company: '<?= $data['company_id'] ?? '' ?>',
};
var currentEditId = '<?= $data['id'] ?? '' ?>';
var paymentData = [];
var currentDepartment =  '<?= $data['divisi_id'] ?? '' ?>';

// Initialize on document ready
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: "bootstrap-5",
        allowClear: true
    });

    // Parse payment data if in edit mode
    try {
        var paymentDataString = '<?= isset($data['payment_data']) ? addslashes($data['payment_data']) : '[]' ?>';
        paymentData = JSON.parse(paymentDataString);
    } catch (e) {
        console.error('Error parsing payment data:', e);
        showError('Gagal memproses data pembayaran');
    }

    // Department change handler
    $('#departemen').on('change', function() {
        var departemen_id = $(this).val();
        currentDepartment = $(this).find('option:selected').val();
        headerData.departemen = departemen_id;
        
        // Reset and disable dependent fields
        $('#company').val(null).trigger('change.select2').prop('disabled', !departemen_id);
        $('#employee').val(null).trigger('change.select2').prop('disabled', true);

        generateTableHeader(currentDepartment)

        if (departemen_id) {
            loadCompanies(departemen_id);
        }
    });

    // Company change handler
    $('#company').on('change', function() {
        var company_id = $(this).val();
        headerData.company = company_id;
        $('#employee').val(null).trigger('change.select2').prop('disabled', !company_id);
        
        if(company_id) {
            loadEmployees(company_id);
        }
    });

    // Employee change handler
    $('#employee').on('change', function() {
        let tanggal_masuk_kerja = $(this).find(':selected').data('tanggal_masuk_kerja');
        $('#tanggal_masuk_kerja').val(tanggal_masuk_kerja || '');
    });

    // Save/Update button
    $('.btn-save, .btn-update-parent').on('click', function() {
        saveData();
    });

    // Initialize form if in edit mode
    if (editMode) {
        initializeEditMode();
    }


    // Function to initialize edit mode
    function initializeEditMode() {
        if (!headerData.departemen) return;    
        // Set department and trigger change
        $('#departemen').val(headerData.departemen).trigger('change.select2');
        loadCompanies(headerData.departemen)
        generateTableHeader(currentDepartment);
        loadEmployees(headerData.company)
        addToTable(paymentData)
    }

    // Generate appropriate table header based on department
    function generateTableHeader(department) {
        console.log(department)
        $('.body-detail-table').empty();
        var headerHtml = '';
        
        if (department == '5') {
            headerHtml = `
                <tr>
                    <th class="text-center" rowspan="3">NO</th>
                    <th class="text-center" rowspan="3">TMK</th>
                    <th class="text-center" rowspan="3">BADGE</th>
                    <th class="text-center" style="min-width: 150px;" rowspan="3">NAMA</th>
                    <th class="text-center" colspan="15">DATA PEKERJAAN (KG)</th>
                    <th class="text-center" rowspan="3">JLH/KG</th>
                    <th class="text-center" rowspan="3">JLH ORG</th>
                    <th class="text-center" rowspan="3">TTL JAM SRT</th>
                    <th class="text-center" rowspan="3">Rp</th>
                    <th class="text-center" rowspan="3">TOTAL Rp /ORG</th>
                    <th class="text-center" colspan="3">SUBSIDI</th>
                    <th class="text-center" colspan="1">Perjam</th>
                    <th class="text-center" rowspan="3">TOTAL KG</th>
                </tr>
                <tr>
                    <th rowspan="1">SJB</th>
                    <th rowspan="1">SJL</th>
                    <th rowspan="1">MT</th>
                    <th rowspan="1">SEL</th>
                    <th rowspan="1">SLM</th>
                    <th rowspan="1">SSP</th>
                    <th rowspan="1">SCM</th>
                    <th rowspan="1">CTT</th>
                    <th rowspan="1">CCT</th>
                    <th rowspan="1">SMH</th>
                    <th rowspan="1">DM</th>
                    <th rowspan="1">SCF</th>
                    <th rowspan="1">LEL</th>
                    <th rowspan="1">GC</th>
                    <th rowspan="1">SSPK</th>
                    <th rowspan="1" style="display: none;">KJB</th>
                    <th rowspan="1" style="display: none;">KJL</th>
                    <th rowspan="1" style="display: none;">KLP</th>
                    <th rowspan="1" style="display: none;">KSP</th>
                    <th rowspan="1" style="display: none;">KCL</th>
                    <th rowspan="1" style="display: none;">KCM</th>
                    <th rowspan="1" style="display: none;">KLG</th>
                    <th rowspan="1" style="display: none;">LM</th>
                    <th rowspan="1" style="display: none;">KEL</th>
                    <th rowspan="1" style="display: none;">KCF</th>
                    <th rowspan="1" style="display: none;">CU</th>
                    <th rowspan="2">Kilo 400</th>
                    <th rowspan="2">Kilo 600</th>
                    <th rowspan="2">TOTAL</th>
                    <th rowspan="2">10,500</th>
                </tr>
                <tr>
                    <th>2,800</th>
                    <th>1,900</th>
                    <th>3,200</th>
                    <th>4,500</th>
                    <th>6,500</th>
                    <th>6,250</th>
                    <th>2,900</th>
                    <th>2,550</th>
                    <th>4,600</th>
                    <th>6,100</th>
                    <th>8,500</th>
                    <th>700</th>
                    <th>2,900</th>
                    <th>5,000</th>
                    <th>6,250</th>
                    <th style="display: none;">8,000</th>
                    <th style="display: none;">4,000</th>
                    <th style="display: none;">8,000</th>
                    <th style="display: none;">4,000</th>
                    <th style="display: none;">8,000</th>
                    <th style="display: none;">5,000</th>
                    <th style="display: none;">10,000</th>
                    <th style="display: none;">5,500</th>
                    <th style="display: none;">12,000</th>
                    <th style="display: none;">8,000</th>
                    <th style="display: none;">440</th>
                </tr>
            `;
        } else {
            headerHtml = `
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
            `;
        }
        $('#tableHeader').html(headerHtml);
    }

    // Function to add data to table
    function addToTable(payment = null) {
        var employeeId, employeeName, tanggalMasuk, badge;
        
        if (payment) {
            // If data comes from backend (edit mode)
            employeeId = payment.employee_id;
            employeeName = payment.employee_name;
            tanggalMasuk = payment.tanggal_masuk_kerja;
            badge = payment.badge;




            paymentData.forEach(function(payment) {
                var employeeId = payment.employee_id;
                var employeeName = payment.employee_name || '';
                var tanggalMasuk = payment.tanggal_masuk_kerja || '';
                var badge = payment.badge || '';
                
                // Create row based on department
                var department = $('#departemen option:selected').val();
                var rowHtml = '';
                
                if (department == '5') {
                    // CANNING department table structure
                    $('#dataTable tbody').empty();

                    rowHtml = `
                        <tr data-employee-id="${employeeId}">
                            <td>${$('#dataTable tbody tr').length + 1}</td>
                            <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${tanggalMasuk}" readonly></td>
                            <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${badge}" readonly></td>
                            <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${employeeName}" readonly></td>
                            
                            <!-- DATA PEKERJAAN (26 columns) -->
                            <td><input type="number" class="form-control form-control-sm" name="sjb" style="min-width: 100px;" value="${payment.sjb || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="sjl" style="min-width: 100px;" value="${payment.sjl || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="mt" style="min-width: 100px;" value="${payment.mt || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="sel" style="min-width: 100px;" value="${payment.sel || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="slm" style="min-width: 100px;" value="${payment.slm || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="ssp" style="min-width: 100px;" value="${payment.ssp || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="scm" style="min-width: 100px;" value="${payment.scm || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="ctt" style="min-width: 100px;" value="${payment.ctt || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="cct" style="min-width: 100px;" value="${payment.cct || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="smh" style="min-width: 100px;" value="${payment.smh || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="dm" style="min-width: 100px;" value="${payment.dm || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="scf" style="min-width: 100px;" value="${payment.scf || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="lel" style="min-width: 100px;" value="${payment.lel || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="gc" style="min-width: 100px;" value="${payment.gc || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="sspk" style="min-width: 100px;" value="${payment.sspk || ''}"></td>
                            <td style="display: none;"><input type="number" class="form-control form-control-sm" name="kjb" style="min-width: 100px; display: none;" value="${payment.kjb || ''}"></td>
                            <td style="display: none;"><input type="number" class="form-control form-control-sm" name="kjl" style="min-width: 100px; display: none;" value="${payment.kjl || ''}"></td>
                            <td style="display: none;"><input type="number" class="form-control form-control-sm" name="klp" style="min-width: 100px; display: none;" value="${payment.klp || ''}"></td>
                            <td style="display: none;"><input type="number" class="form-control form-control-sm" name="ksp" style="min-width: 100px; display: none;" value="${payment.ksp || ''}"></td>
                            <td style="display: none;"><input type="number" class="form-control form-control-sm" name="kcl" style="min-width: 100px; display: none;" value="${payment.kcl || ''}"></td>
                            <td style="display: none;"><input type="number" class="form-control form-control-sm" name="kcm" style="min-width: 100px; display: none;" value="${payment.kcm || ''}"></td>
                            <td style="display: none;"><input type="number" class="form-control form-control-sm" name="klg" style="min-width: 100px; display: none;" value="${payment.klg || ''}"></td>
                            <td style="display: none;"><input type="number" class="form-control form-control-sm" name="lm" style="min-width: 100px; display: none;" value="${payment.lm || ''}"></td>
                            <td style="display: none;"><input type="number" class="form-control form-control-sm" name="kel" style="min-width: 100px; display: none;" value="${payment.kel || ''}"></td>
                            <td style="display: none;"><input type="number" class="form-control form-control-sm" name="kcf" style="min-width: 100px; display: none;" value="${payment.kcf || ''}"></td>
                            <td style="display: none;"><input type="number" class="form-control form-control-sm" name="cu" style="min-width: 100px; display: none;" value="${payment.cu || ''}"></td>
                            
                            <!-- Summary columns -->
                            <td><input type="number" class="form-control form-control-sm" name="jlhkg" style="min-width: 100px;" value="${payment.jlhkg || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="jlh_org" style="min-width: 100px;" value="${payment.jlh_org || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="ttl_jam" style="min-width: 100px;" value="${payment.ttl_jam || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="rp" style="min-width: 100px;" value="${payment.rp || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="total_rp_org" style="min-width: 100px;" value="${payment.total_rp_org || ''}"></td>
                            
                            <!-- Subsidies -->
                            <td><input type="number" class="form-control form-control-sm" name="subsidik400" style="min-width: 100px;" value="${payment.subsidik400 || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="subsidik600" style="min-width: 100px;" value="${payment.subsidik600 || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="subsiditotal" style="min-width: 100px;" value="${payment.subsiditotal || ''}"></td>
                            
                            <!-- Final values -->
                            <td><input type="number" class="form-control form-control-sm" name="perjam" style="min-width: 100px;" value="${payment.perjam || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="total_kg" style="min-width: 100px;" value="${payment.total_kg || ''}"></td>
                        </tr>
                    `;
                } else {
                    // Default department table structure
                    rowHtml = `
                        <tr data-employee-id="${employeeId}">
                            <td>${$('#dataTable tbody tr').length + 1}</td>
                            <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${tanggalMasuk}" readonly></td>
                            <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${badge}" readonly></td>
                            <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${employeeName}" readonly></td>
                            
                            <!-- Udang -->
                            <td><input type="number" class="form-control form-control-sm udang-ac" style="min-width: 100px;" value="${payment.udang?.ac || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm udang-sk" style="min-width: 100px;" value="${payment.udang?.sk || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm udang-mb" style="min-width: 100px;" value="${payment.udang?.mb || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm udang-ml" style="min-width: 100px;" value="${payment.udang?.ml || ''}"></td>
                            
                            <!-- Kepah -->
                            <td><input type="number" class="form-control form-control-sm kepah-ac" style="min-width: 100px;" value="${payment.kepah?.ac || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm kepah-sk" style="min-width: 100px;" value="${payment.kepah?.sk || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm kepah-mb" style="min-width: 100px;" value="${payment.kepah?.mb || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm kepah-ml" style="min-width: 100px;" value="${payment.kepah?.ml || ''}"></td>
                            
                            <!-- KPTG -->
                            <td><input type="number" class="form-control form-control-sm kptg-ac" style="min-width: 100px;" value="${payment.kptg?.ac || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm kptg-sk" style="min-width: 100px;" value="${payment.kptg?.sk || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm kptg-mb" style="min-width: 100px;" value="${payment.kptg?.mb || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm kptg-ml" style="min-width: 100px;" value="${payment.kptg?.ml || ''}"></td>
                            
                            <!-- Jam Kerja -->
                            <td><input type="number" class="form-control form-control-sm jam-kerja-udang" style="min-width: 100px;" value="${payment.jam_kerja?.udang || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm jam-kerja-kepah" style="min-width: 100px;" value="${payment.jam_kerja?.kepah || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm jam-kerja-kptg" style="min-width: 100px;" value="${payment.jam_kerja?.kptg || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm jam-kerja-ml" style="min-width: 100px;" value="${payment.jam_kerja?.ml || ''}"></td>
                            
                            <!-- Total & Kalkulasi -->
                            <td><input type="number" class="form-control form-control-sm total-kg" style="min-width: 100px;" value="${payment.total?.kg || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm total-jam" style="min-width: 100px;" value="${payment.total?.jam || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm jlh-org" style="min-width: 100px;" value="${payment.total?.org || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm rupiah" style="min-width: 100px;" value="${payment.rupiah || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm subsidi-rupiah" style="min-width: 100px;" value="${payment.subsidi_rupiah || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm borongan-per-jam" style="min-width: 100px;" value="${payment.borongan_per_jam || ''}"></td>
                            
                            <!-- KG/Jam -->
                            <td><input type="number" class="form-control form-control-sm kg-per-jam-ac" style="min-width: 100px;" value="${payment.kg_per_jam?.ac || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm kg-per-jam-sk" style="min-width: 100px;" value="${payment.kg_per_jam?.sk || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm kg-per-jam-mb" style="min-width: 100px;" value="${payment.kg_per_jam?.mb || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm kg-per-jam-ml" style="min-width: 100px;" value="${payment.kg_per_jam?.ml || ''}"></td>
                        </tr>
                    `;
                }

                $('#dataTable tbody').append(rowHtml);
            });

        } else {
            // If adding new employee (normal mode)
            employeeId = $('#employee').val();
            if (!employeeId) {
                showWarning('Harap pilih karyawan terlebih dahulu!');
                return;
            }
            
            // Check if employee already exists in table
            if ($('#dataTable tbody tr[data-employee-id="' + employeeId + '"]').length > 0) {
                showWarning('Karyawan ini sudah ditambahkan ke tabel!');
                return;
            }

            employeeName = $('#employee option:selected').text();
            tanggalMasuk = $('#tanggal_masuk_kerja').val();
            badge = $('#employee').find(':selected').data('badge');
             // Create new row based on department
            var newRow = createTableRow(currentDepartment, employeeId, employeeName, tanggalMasuk, badge, payment);
            $('#dataTable tbody').append(newRow);
        }

       
        // Only reset form if adding new employee (not in edit mode)
        if (!payment) {
            resetForm();
        }
    }

    // Create table row based on department
    function createTableRow(department, employeeId, employeeName, tanggalMasuk, badge, payment) {
        if (department == '5') {
            return `
                 <tr data-employee-id="${employeeId}">
                    <td>${$('#dataTable tbody tr').length + 1}</td>
                    <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${tanggalMasuk}" readonly></td>
                    <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${badge}" readonly></td>
                    <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${employeeName}" readonly></td>
                    
                    <!-- DATA PEKERJAAN (26 columns) -->
                    <td><input type="number" class="form-control form-control-sm" name="sjb" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="sjl" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="mt" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="sel" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="slm" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="ssp" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="scm" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="ctt" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="cct" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="smh" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="dm" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="scf" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="lel" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="gc" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="sspk" style="min-width: 100px;"></td>
                    <td style="display: none;"><input type="number" class="form-control form-control-sm" name="kjb" style="min-width: 100px; display: none;"></td>
                    <td style="display: none;"><input type="number" class="form-control form-control-sm" name="kjl" style="min-width: 100px; display: none;"></td>
                    <td style="display: none;"><input type="number" class="form-control form-control-sm" name="klp" style="min-width: 100px; display: none;"></td>
                    <td style="display: none;"><input type="number" class="form-control form-control-sm" name="ksp" style="min-width: 100px; display: none;"></td>
                    <td style="display: none;"><input type="number" class="form-control form-control-sm" name="kcl" style="min-width: 100px; display: none;"></td>
                    <td style="display: none;"><input type="number" class="form-control form-control-sm" name="kcm" style="min-width: 100px; display: none;"></td>
                    <td style="display: none;"><input type="number" class="form-control form-control-sm" name="klg" style="min-width: 100px; display: none;"></td>
                    <td style="display: none;"><input type="number" class="form-control form-control-sm" name="lm" style="min-width: 100px; display: none;"></td>
                    <td style="display: none;"><input type="number" class="form-control form-control-sm" name="kel" style="min-width: 100px; display: none;"></td>
                    <td style="display: none;"><input type="number" class="form-control form-control-sm" name="kcf" style="min-width: 100px; display: none;"></td>
                    <td style="display: none;"><input type="number" class="form-control form-control-sm" name="cu" style="min-width: 100px; display: none;"></td>
                    
                    <!-- Summary columns -->
                    <td><input type="number" class="form-control form-control-sm" name="jlhkg" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="jlh_org" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="ttl_jam" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="rp" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="total_rp_org" style="min-width: 100px;"></td>
                    
                    <!-- Subsidies -->
                    <td><input type="number" class="form-control form-control-sm" name="subsidik400" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="subsidik600" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="subsiditotal" style="min-width: 100px;"></td>
                    
                    <!-- Final values -->
                    <td><input type="number" class="form-control form-control-sm" name="perjam" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="total_kg" style="min-width: 100px;"></td>
                </tr>
            `;
        } else {
            return `
                <tr data-employee-id="${employeeId}">
                    <td>${$('#dataTable tbody tr').length + 1}</td>
                    <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${tanggalMasuk}" readonly></td>
                    <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${badge}" readonly></td>
                    <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${employeeName}" data-id="${employeeId}" readonly></td>

                    <!-- UDANG -->
                    <td><input type="number" class="form-control form-control-sm udang-ac" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm udang-sk" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm udang-mb" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm udang-ml" style="min-width: 100px;"></td>

                    <!-- KEPAH -->
                    <td><input type="number" class="form-control form-control-sm kepah-ac" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm kepah-sk" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm kepah-mb" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm kepah-ml" style="min-width: 100px;"></td>

                    <!-- KPTG -->
                    <td><input type="number" class="form-control form-control-sm kptg-ac" style="min-width: 100px;" ></td>
                    <td><input type="number" class="form-control form-control-sm kptg-sk" style="min-width: 100px;" ></td>
                    <td><input type="number" class="form-control form-control-sm kptg-mb" style="min-width: 100px;" ></td>
                    <td><input type="number" class="form-control form-control-sm kptg-ml" style="min-width: 100px;" ></td>

                    <!-- JAM KERJA -->
                    <td><input type="number" class="form-control form-control-sm jam-kerja-udang" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm jam-kerja-kepah" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm jam-kerja-kptg" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm jam-kerja-ml" style="min-width: 100px;"></td>

                    <!-- TOTAL & KALKULASI -->
                    <td><input type="number" class="form-control form-control-sm total-kg" style="min-width: 120px;"></td>
                    <td><input type="number" class="form-control form-control-sm total-jam" style="min-width: 120px;"></td>
                    <td><input type="number" class="form-control form-control-sm jlh-org" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm rupiah" style="min-width: 120px;"></td>
                    <td><input type="number" class="form-control form-control-sm subsidi-rupiah" style="min-width: 120px;"></td>
                    <td><input type="number" class="form-control form-control-sm borongan-per-jam" style="min-width: 120px;"></td>

                    <!-- KG / JAM -->
                    <td><input type="number" class="form-control form-control-sm kg-per-jam-ac" style="min-width: 90px;"></td>
                    <td><input type="number" class="form-control form-control-sm kg-per-jam-sk" style="min-width: 90px;"></td>
                    <td><input type="number" class="form-control form-control-sm kg-per-jam-mb" style="min-width: 90px;"></td>
                    <td><input type="number" class="form-control form-control-sm kg-per-jam-ml" style="min-width: 90px;"></td>
                </tr>
            `;
        }
    }


    function collectEmployeeData() {
        var employeeData = [];
        var departmentText = $('#departemen option:selected').val();
        
        $('#dataTable tbody tr').each(function() {
            var $row = $(this);
            var employeeId = $row.data('employee-id');
            
            if (departmentText === '5') {
                // For PTS department
                employeeData.push({
                    employee_id: employeeId,
                    employee_name: $row.find('td:eq(3) input').val(),
                    badge: $row.find('td:eq(2) input').val(),
                    tanggal_masuk_kerja: $row.find('td:eq(1) input').val(),
                    
                    // Job data columns (26)
                    sjb: parseFloat($row.find('[name="sjb"]').val()) || 0,
                    sjl: parseFloat($row.find('[name="sjl"]').val()) || 0,
                    mt: parseFloat($row.find('[name="mt"]').val()) || 0,
                    sel: parseFloat($row.find('[name="sel"]').val()) || 0,
                    slm: parseFloat($row.find('[name="slm"]').val()) || 0,
                    ssp: parseFloat($row.find('[name="ssp"]').val()) || 0,
                    scm: parseFloat($row.find('[name="scm"]').val()) || 0,
                    ctt: parseFloat($row.find('[name="ctt"]').val()) || 0,
                    cct: parseFloat($row.find('[name="cct"]').val()) || 0,
                    smh: parseFloat($row.find('[name="smh"]').val()) || 0,
                    dm: parseFloat($row.find('[name="dm"]').val()) || 0,
                    scf: parseFloat($row.find('[name="scf"]').val()) || 0,
                    lel: parseFloat($row.find('[name="lel"]').val()) || 0,
                    gc: parseFloat($row.find('[name="gc"]').val()) || 0,
                    sspk: parseFloat($row.find('[name="sspk"]').val()) || 0,
                    kjb: parseFloat($row.find('[name="kjb"]').val()) || 0,
                    kjl: parseFloat($row.find('[name="kjl"]').val()) || 0,
                    klp: parseFloat($row.find('[name="klp"]').val()) || 0,
                    ksp: parseFloat($row.find('[name="ksp"]').val()) || 0,
                    kcl: parseFloat($row.find('[name="kcl"]').val()) || 0,
                    kcm: parseFloat($row.find('[name="kcm"]').val()) || 0,
                    klg: parseFloat($row.find('[name="klg"]').val()) || 0,
                    lm: parseFloat($row.find('[name="lm"]').val()) || 0,
                    kel: parseFloat($row.find('[name="kel"]').val()) || 0,
                    kcf: parseFloat($row.find('[name="kcf"]').val()) || 0,
                    cu: parseFloat($row.find('[name="cu"]').val()) || 0,
                    
                    // Summary columns
                    jlhkg: parseFloat($row.find('[name="jlhkg"]').val()) || 0,
                    jlh_org: parseFloat($row.find('[name="jlh_org"]').val()) || 0,
                    ttl_jam: parseFloat($row.find('[name="ttl_jam"]').val()) || 0,
                    rp: parseFloat($row.find('[name="rp"]').val()) || 0,
                    total_rp_org: parseFloat($row.find('[name="total_rp_org"]').val()) || 0,
                    
                    // Subsidies
                    subsidik400: parseFloat($row.find('[name="subsidik400"]').val()) || 0,
                    subsidik600: parseFloat($row.find('[name="subsidik600"]').val()) || 0,
                    subsiditotal: parseFloat($row.find('[name="subsiditotal"]').val()) || 0,
                    
                    // Final values
                    perjam: parseFloat($row.find('[name="perjam"]').val()) || 0,
                    total_kg: parseFloat($row.find('[name="total_kg"]').val()) || 0
                });
            } else {
                // For other departments
                employeeData.push({
                    employee_id: employeeId,
                    employee_name: $row.find('td:eq(3) input').val(),
                    badge: $row.find('td:eq(2) input').val(),
                    tanggal_masuk_kerja: $row.find('td:eq(1) input').val(),
                    
                    // Udang data
                    udang: {
                        ac: parseFloat($row.find('.udang-ac').val()) || 0,
                        sk: parseFloat($row.find('.udang-sk').val()) || 0,
                        mb: parseFloat($row.find('.udang-mb').val()) || 0,
                        ml: parseFloat($row.find('.udang-ml').val()) || 0
                    },
                    
                    // Kepah data
                    kepah: {
                        ac: parseFloat($row.find('.kepah-ac').val()) || 0,
                        sk: parseFloat($row.find('.kepah-sk').val()) || 0,
                        mb: parseFloat($row.find('.kepah-mb').val()) || 0,
                        ml: parseFloat($row.find('.kepah-ml').val()) || 0
                    },
                    
                    // KPTG data
                    kptg: {
                        ac: parseFloat($row.find('.kptg-ac').val()) || 0,
                        sk: parseFloat($row.find('.kptg-sk').val()) || 0,
                        mb: parseFloat($row.find('.kptg-mb').val()) || 0,
                        ml: parseFloat($row.find('.kptg-ml').val()) || 0
                    },
                    
                    // Jam Kerja
                    jam_kerja: {
                        udang: parseFloat($row.find('.jam-kerja-udang').val()) || 0,
                        kepah: parseFloat($row.find('.jam-kerja-kepah').val()) || 0,
                        kptg: parseFloat($row.find('.jam-kerja-kptg').val()) || 0,
                        ml: parseFloat($row.find('.jam-kerja-ml').val()) || 0
                    },
                    
                    // Totals
                    total: {
                        kg: parseFloat($row.find('.total-kg').val()) || 0,
                        jam: parseFloat($row.find('.total-jam').val()) || 0,
                        org: parseFloat($row.find('.jlh-org').val()) || 0
                    },
                    
                    // Financials
                    rupiah: parseFloat($row.find('.rupiah').val()) || 0,
                    subsidi_rupiah: parseFloat($row.find('.subsidi-rupiah').val()) || 0,
                    borongan_per_jam: parseFloat($row.find('.borongan-per-jam').val()) || 0,
                    
                    // KG per Jam
                    kg_per_jam: {
                        ac: parseFloat($row.find('.kg-per-jam-ac').val()) || 0,
                        sk: parseFloat($row.find('.kg-per-jam-sk').val()) || 0,
                        mb: parseFloat($row.find('.kg-per-jam-mb').val()) || 0,
                        ml: parseFloat($row.find('.kg-per-jam-ml').val()) || 0
                    }
                });
            }
        });
        
        return employeeData;
    }

    // Function to save data
    function saveData() {
        // Validate header data
        if (!headerData.departemen || !headerData.company) {
            showWarning('Harap lengkapi data header terlebih dahulu!');
            return;
        }

        // Validate if there are employees in the table
        if ($('#dataTable tbody tr').length === 0) {
            showWarning('Harap tambahkan minimal satu karyawan!');
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Menyimpan data',
            html: 'Mohon tunggu...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Prepare data
        var formData = {
            departemen: $('#departemen').val(),
            company: $('#company').val(),
            employee_data: JSON.stringify(collectEmployeeData()),
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        };

        // Determine URL and method
        var url = editMode ? '<?= base_url('/hr-outsourcing-sallary-payment/update/') ?>' + currentEditId 
                        : '<?= base_url('/hr-outsourcing-sallary-payment/store') ?>';
        var method = 'POST';

        // AJAX call
        $.ajax({
            url: url,
            type: method,
            dataType: 'json',
            data: formData,
            success: function(response) {
                Swal.close();
                if (response.status === 'success') {
                    showSuccess(response.message, () => {
                        if (!editMode) {
                            window.location.href = '<?= base_url('/hr-outsourcing-sallary-payment') ?>';
                        }
                    });
                } else {
                    showError(response.message);
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                showError('Gagal menyimpan data: ' + error);
            }
        });
    }

    // Helper functions for SweetAlert
    function showSuccess(message, callback = null) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: message,
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            if (callback && typeof callback === 'function') {
                callback();
            }
        });
    }

    function showWarning(message) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: message
        });
    }

    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    }

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

// Reset form function
function resetForm() {
    $('#tanggal_masuk_kerja').val('');
    $('#employee').val(null).trigger('change');
}
</script>
<?= $this->endSection(); ?>