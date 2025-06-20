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
                             <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tanggal_pembayaran">Tanggal Pembayaran</label>
                                    <input type="date" class="form-control" id="tanggal_pembayaran" name="tanggal_pembayaran">
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
                                    <input type="date" class="form-control" id="tanggal_masuk_kerja" name="tanggal_masuk_kerja" required>
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


<div class="modal fade" id="modalDetailHarga" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h3 class="modal-title" id="exampleModalLabel">Detail Item Produksi</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nama Karyawan</label>
                            <input type="text" class="form-control" id="employeeNameModal" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kode Barang</label>
                            <input type="text" class="form-control" id="kodeBarang" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="number" class="form-control" id="inputBerat" placeholder="Berat" step="0.01" min="0">
                            <input type="number" class="form-control" id="inputHarga" style="display: none;">
                            <div class="input-group-append">
                                <span class="input-group-text">kg</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="bg-light">
                            <tr>
                                <th width="50%">Berat (kg)</th>
                                <th width="50%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableHargaBody">
                            <!-- Items will be added here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btnSimpanHarga">
                    <i class="fa fa-save"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    var editMode = <?= !empty($data) ? 'true' : 'false' ?>;
    var headerData = {
        departemen: '<?= $data['divisi_id'] ?? '' ?>',
        company: '<?= $data['company_id'] ?? '' ?>',
        payment_date: '<?= $data['payment_date'] ?? '' ?>',
    };
    var currentEditId = '<?= $data['id'] ?? '' ?>';
    var paymentData = [];
    let employeeData = [];
    let employeeItemDetails = {};

    var currentDepartment = '<?= $data['divisi_id'] ?? '' ?>';

    // Initialize on document ready
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: "bootstrap-5",
            allowClear: true
        });

        try {
            // 1. Get payment data from PHP - ensure proper JSON encoding
            var paymentDataString = '<?= isset($data['payment_data']) ? addslashes(json_encode($data['payment_data'])) : '[]' ?>';
            
            // 2. Clean the string by removing any extra characters
            paymentDataString = paymentDataString.trim();
            
            // 3. Handle cases where the string might be wrapped in extra quotes
            if (paymentDataString.startsWith('"') && paymentDataString.endsWith('"')) {
                paymentDataString = paymentDataString.slice(1, -1);
            }
            
            // 4. Replace escaped quotes if they exist
            paymentDataString = paymentDataString.replace(/\\"/g, '"');
            
            // 5. Parse the JSON safely
            paymentData = JSON.parse(paymentDataString || '[]');
            
            // 6. Initialize employeeItemDetails
            employeeItemDetails = {};
            
            // 7. Process only if paymentData is an array
            if (Array.isArray(paymentData)) {
                paymentData.forEach(employee => {
                    if (employee && employee.employee_id) {
                        const employeeId = employee.employee_id;
                        
                        // Process each code for the employee
                        const codes = ['sjb', 'sjl', 'mt', 'sel', 'slm', 'ssp', 'scm', 'ctt', 'cct', 'smh', 
                                    'dm', 'scf', 'lel', 'gc', 'sspk', 'kjb', 'kjl', 'klp', 'ksp', 'kcl', 
                                    'kcm', 'klg', 'lm', 'kel', 'kcf', 'cu'];
                        
                        codes.forEach(kodeBarang => {
                            const key = `${employeeId}_${kodeBarang}`;
                            
                            if (employee[kodeBarang] && employee[kodeBarang].items) {
                                employeeItemDetails[key] = {
                                    items: employee[kodeBarang].items,
                                    totalBerat: parseFloat(employee[kodeBarang].berat) || 0,
                                    totalHarga: parseFloat(employee[kodeBarang].total) || 0
                                };
                            }
                        });
                    }
                });
            } else {
                console.error('Payment data is not an array:', paymentData);
                paymentData = []; // Reset to empty array
            }
            
            console.log('Successfully initialized payment data:', {
                paymentData: paymentData,
                employeeItemDetails: employeeItemDetails
            });
        } catch (e) {
            console.error('Error parsing payment data:', e);
            console.error('Problematic data string:', paymentDataString);
            
            // Initialize empty data structures on error
            paymentData = [];
            employeeItemDetails = {};
            
            showError('Gagal memproses data pembayaran. Silakan muat ulang halaman.');
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

            if (company_id) {
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
            $('#tanggal_pembayaran').val(headerData.payment_date);
            loadCompanies(headerData.departemen)
            generateTableHeader(currentDepartment);
            loadEmployees(headerData.company)
            addToTable(paymentData)
        }

        // Generate appropriate table header based on department
        function generateTableHeader(department) {
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
                    <th class="text-center" rowspan="3">SUBSIDI</th>
                    <th class="text-center" colspan="2">TARGET</th>
                    <th class="text-center" colspan="1">Perjam</th>
                    <th class="text-center" rowspan="3">TOTAL KG</th>
                </tr>
                <tr>
                    <th style="min-width:100px;" rowspan="1">SJB</th>
                    <th style="min-width:150px;" rowspan="1">SJL</th>
                    <th style="min-width:150px;" rowspan="1">MT</th>
                    <th style="min-width:150px;" rowspan="1">SEL</th>
                    <th style="min-width:150px;" rowspan="1">SLM</th>
                    <th style="min-width:150px;" rowspan="1">SSP</th>
                    <th style="min-width:150px;" rowspan="1">SCM</th>
                    <th style="min-width:150px;" rowspan="1">CTT</th>
                    <th style="min-width:150px;" rowspan="1">CCT</th>
                    <th style="min-width:150px;" rowspan="1">SMH</th>
                    <th style="min-width:150px;" rowspan="1">DM</th>
                    <th style="min-width:150px;" rowspan="1">SCF</th>
                    <th style="min-width:150px;" rowspan="1">LEL</th>
                    <th style="min-width:150px;" rowspan="1">GC</th>
                    <th style="min-width:150px;" rowspan="1">SSPK</th>
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
                    <th rowspan="2">10,500</th>
                </tr>
                <tr>
                    <th style="min-width:150px;" >2,800</th>
                    <th style="min-width:150px;">1,900</th>
                    <th style="min-width:150px;">3,250</th>
                    <th style="min-width:150px;">4,500</th>
                    <th style="min-width:150px;">6,500</th>
                    <th style="min-width:150px;">6,250</th>
                    <th style="min-width:150px;">2,900</th>
                    <th style="min-width:150px;">2,550</th>
                    <th style="min-width:150px;">4,600</th>
                    <th style="min-width:150px;">6,150</th>
                    <th style="min-width:150px;">8,500</th>
                    <th style="min-width:150px;">700</th>
                    <th style="min-width:150px;">2,900</th>
                    <th style="min-width:150px;">5,000</th>
                    <th style="min-width:150px;">6,250</th>
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

                        rowHtml = `
                        <tr data-employee-id="${employeeId}">
                            <td>${$('#dataTable tbody tr').length + 1}</td>
                            <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${tanggalMasuk}" readonly></td>
                            <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${badge}" readonly></td>
                            <td><input type="text" class="form-control form-control-sm" style="min-width: 150px;" value="${employeeName}" readonly></td>
                            
                            <!-- DATA PEKERJAAN (15 columns) -->
                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="height:40px;cursor:pointer;" onclick="showModalDetailHarga('sjb', '${employeeId}', '${employeeName}', '2800')">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="sjb" value="${payment.sjb?.berat || ''}" data-employee_id="${employeeId}" data-harga="2800">
                                </div>
                            </td>
                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="height:40px;cursor:pointer;" onclick="showModalDetailHarga('sjl', '${employeeId}', '${employeeName}', '1900')">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="sjl" value="${payment.sjl?.berat || ''}" data-employee_id="${employeeId}" data-harga="1900">
                                </div>
                            </td>
                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="height:40px;cursor:pointer;" onclick="showModalDetailHarga('mt', '${employeeId}', '${employeeName}','3250')">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="mt" value="${payment.mt?.berat || ''}" data-employee_id="${employeeId}" data-harga="3250">
                                </div>
                            </td>
                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="height:40px;cursor:pointer;" onclick="showModalDetailHarga('sel', '${employeeId}', '${employeeName}', '4500')">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="sel" value="${payment.sel?.berat || ''}" data-employee_id="${employeeId}" data-harga="4500">
                                </div>
                            </td>
                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="height:40px;cursor:pointer;" onclick="showModalDetailHarga('slm', '${employeeId}', '${employeeName}', '6500')">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="slm" value="${payment.slm?.berat || ''}" data-employee_id="${employeeId}" data-harga="6500">
                                </div>
                            </td>
                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="height:40px;cursor:pointer;" onclick="showModalDetailHarga('ssp', '${employeeId}', '${employeeName}', '6250')">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="ssp" value="${payment.ssp?.berat || ''}" data-employee_id="${employeeId}" data-harga="6250">
                                </div>
                            </td>
                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="height:40px;cursor:pointer;" onclick="showModalDetailHarga('scm', '${employeeId}', '${employeeName}', '2900')">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="scm" value="${payment.scm?.berat || ''}" data-employee_id="${employeeId}" data-harga="2900">
                                </div>
                            </td>
                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="height:40px;cursor:pointer;" onclick="showModalDetailHarga('ctt', '${employeeId}', '${employeeName}', '2550')">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="ctt" value="${payment.ctt?.berat || ''}" data-employee_id="${employeeId}" data-harga="2550">
                                </div>
                            </td>
                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="height:40px;cursor:pointer;" onclick="showModalDetailHarga('cct', '${employeeId}', '${employeeName}', '4600')">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="cct" value="${payment.cct?.berat || ''}" data-employee_id="${employeeId}" data-harga="4600">
                                </div>
                            </td>
                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="height:40px;cursor:pointer;" onclick="showModalDetailHarga('smh', '${employeeId}', '${employeeName}', '6150')">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="smh" value="${payment.smh?.berat || ''}" data-employee_id="${employeeId}" data-harga="6150">
                                </div>
                            </td>
                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="height:40px;cursor:pointer;" onclick="showModalDetailHarga('dm', '${employeeId}', '${employeeName}', '8500')">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="dm" value="${payment.dm?.berat || ''}" data-employee_id="${employeeId}" data-harga="8500">
                                </div>
                            </td>
                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="height:40px;cursor:pointer;" onclick="showModalDetailHarga('scf', '${employeeId}', '${employeeName}', '700')">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="scf" value="${payment.scf?.berat || ''}" data-employee_id="${employeeId}" data-harga="700">
                                </div>
                            </td>
                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="height:40px;cursor:pointer;" onclick="showModalDetailHarga('lel', '${employeeId}', '${employeeName}', '2900')">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="lel" value="${payment.lel?.berat || ''}" data-employee_id="${employeeId}" data-harga="2900">
                                </div>
                            </td>
                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="height:40px;cursor:pointer;" onclick="showModalDetailHarga('gc', '${employeeId}', '${employeeName}', '5000')">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="gc" value="${payment.gc?.berat || ''}" data-employee_id="${employeeId}" data-harga="5000">
                                </div>
                            </td>
                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="height:40px;cursor:pointer;" onclick="showModalDetailHarga('sspk', '${employeeId}', '${employeeName}', '6250')">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="sspk" value="${payment.sspk?.berat || ''}" data-employee_id="${employeeId}" data-harga="6250">
                                </div>
                            </td>


                            <!-- Summary columns -->
                            <td><input type="number" class="form-control form-control-sm" name="jlhkg" style="min-width: 100px;" value="${payment.jlhkg || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="jlh_org" style="min-width: 100px;" value="${payment.jlh_org || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="ttl_jam" style="min-width: 100px;" value="${payment.ttl_jam || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="rp" style="min-width: 100px;" value="${payment.rp || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="total_rp_org" style="min-width: 100px;" value="${payment.total_rp_org || ''}"></td>
                            
                            <!-- Subsidies -->
                            <td><input type="number" class="form-control form-control-sm" name="subsidi" style="min-width: 100px;" value="${payment.subsidi || ''}"></td>
                            
                            <!-- Target -->
                            <td><input type="number" class="form-control form-control-sm" name="kilo400" style="min-width: 100px;" value="${payment.kilo400 || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="kilo600" style="min-width: 100px;" value="${payment.kilo600 || ''}"></td>
                            
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
                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('sjb', '${employeeId}', '${employeeName}', '2800')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="sjb" data-employee_id="${employeeId}" data-harga="2800">
                        </div>
                    </td>
                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('sjl', '${employeeId}', '${employeeName}', '1900')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="sjl" data-employee_id="${employeeId}" data-harga="1900">
                        </div>
                    </td>
                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('mt', '${employeeId}', '${employeeName}', '3250')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="mt" data-employee_id="${employeeId}" data-harga="3250">
                        </div>
                    </td>
                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('sel', '${employeeId}', '${employeeName}', '4500')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="sel" data-employee_id="${employeeId}" data-harga="4500">
                        </div>
                    </td>
                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('slm', '${employeeId}', '${employeeName}', '6500')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="slm" data-employee_id="${employeeId}" data-harga="6500">
                        </div>
                    </td>
                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('ssp', '${employeeId}', '${employeeName}', '6250')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="ssp" data-employee_id="${employeeId}" data-harga="6250">
                        </div>
                    </td>
                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('scm', '${employeeId}', '${employeeName}', '2900')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="scm" data-employee_id="${employeeId}" data-harga="2900">
                        </div>
                    </td>
                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('ctt', '${employeeId}', '${employeeName}', '2550')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="ctt" data-employee_id="${employeeId}" data-harga="2550">
                        </div>
                    </td>
                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('cct', '${employeeId}', '${employeeName}', '4600')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="cct" data-employee_id="${employeeId}" data-harga="4600">
                        </div>
                    </td>
                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('smh', '${employeeId}', '${employeeName}', '6150')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="smh" data-employee_id="${employeeId}" data-harga="6150">
                        </div>
                    </td>
                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('dm', '${employeeId}', '${employeeName}', '8500')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="dm" data-employee_id="${employeeId}" data-harga="8500">
                        </div>
                    </td>
                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('scf', '${employeeId}', '${employeeName}', '700')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="scf" data-employee_id="${employeeId}" data-harga="700">
                        </div>
                    </td>
                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('lel', '${employeeId}', '${employeeName}', '4000')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="lel" data-employee_id="${employeeId}" data-harga="4000">
                        </div>
                    </td>
                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('gc', '${employeeId}', '${employeeName}', '5000')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="gc" data-employee_id="${employeeId}" data-harga="5000">
                        </div>
                    </td>
                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('sspk', '${employeeId}', '${employeeName}', '6250')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="sspk" data-employee_id="${employeeId}" data-harga="6250">
                        </div>
                    </td>


                    <!-- Hidden fields -->
                    ${['kjb', 'kjl', 'klp', 'ksp', 'kcl', 'kcm', 'klg', 'lm', 'kel', 'kcf', 'cu'].map(field => `
                    <td style="display: none;">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="height:40px;cursor:pointer;" onclick="showModalDetailHarga('${field}', '${employeeId}', '${employeeName}')">+</span>
                            </div>
                            <input type="number" class="form-control" style="height:40px; display: none;" name="${field}">
                        </div>
                    </td>`).join('')}              
                    
                    
                    <!-- Summary columns -->
                    <td><input type="number" class="form-control form-control-sm" name="jlhkg" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="jlh_org" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="ttl_jam" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="rp" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="total_rp_org" style="min-width: 100px;"></td>
                    
                    <!-- Subsidies -->
                    <td><input type="number" class="form-control form-control-sm" name="subsidi" style="min-width: 100px;"></td>
                            
                    <!-- Target -->
                    <td><input type="number" class="form-control form-control-sm" name="kilo400" style="min-width: 100px;"></td>
                    <td><input type="number" class="form-control form-control-sm" name="kilo600" style="min-width: 100px;"></td>
                            
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
            employeeData = [];
            
            $('#dataTable tbody tr').each(function() {
                const $row = $(this);
                const employeeId = $row.data('employee-id');
                
                const employee = {
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
                    subsidi: parseFloat($row.find('[name="subsidi"]').val()) || 0,

                    // Target
                    kilo400: parseFloat($row.find('[name="kilo400"]').val()) || 0,
                    kilo600: parseFloat($row.find('[name="kilo600"]').val()) || 0,

                    // Final values
                    perjam: parseFloat($row.find('[name="perjam"]').val()) || 0,
                    total_kg: parseFloat($row.find('[name="total_kg"]').val()) || 0,
                    
                    // Item details for each code
                    item_details: {}
                };

                const codes = ['sjb', 'sjl', 'mt', 'sel', 'slm', 'ssp', 'scm', 'ctt', 'cct', 'smh', 
                            'dm', 'scf', 'lel', 'gc', 'sspk', 'kjb', 'kjl', 'klp', 'ksp', 'kcl', 
                            'kcm', 'klg', 'lm', 'kel', 'kcf', 'cu'];

                codes.forEach(code => {
                    const key = `${employeeId}_${code}`;
                    if (employeeItemDetails[key]) {
                        employee[code] = {
                            total: employeeItemDetails[key].totalHarga,
                            berat: employeeItemDetails[key].totalBerat,
                            count: employeeItemDetails[key].items.length,
                            items: employeeItemDetails[key].items.map(item => ({
                                berat: item.berat,
                                harga: item.harga,
                                subtotal: item.berat * item.harga
                            }))
                        };
                    } else {
                        employee[code] = {
                            total: 0,
                            berat: 0,
                            count: 0,
                            items: []
                        };
                    }
                });

                employeeData.push(employee);
            });
            
            console.log('Collected Employee Data:', employeeData);
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
                tanggal_pembayaran: $('#tanggal_pembayaran').val(),
                employee_data: JSON.stringify(collectEmployeeData()),
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            };

            // Determine URL and method
            var url = editMode ? '<?= base_url('/hr-outsourcing-sallary-payment/update/') ?>' + currentEditId :
                '<?= base_url('/hr-outsourcing-sallary-payment/store') ?>';
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


        $('#inputBerat').keypress(function(e) {
            if (e.which === 13) { // Enter key
                const berat = $('#inputBerat').val();
                const harga = $('#inputHarga').val();

                    if (berat && harga) {
                        const newRow = `
                            <tr>
                                <td>${berat}</td>
                                <td style="display: none;">${harga}</td>
                                <td><button class="btn btn-sm btn-danger hapus-harga"><i class="fa fa-trash"></i></button></td>
                            </tr>
                        `;
                        $('#tableHargaBody').append(newRow);
                        
                        // Clear inputs
                        $('#inputBerat').val('');
                    }

                e.preventDefault(); // Biar ga form submit atau reload
            }
        });



        $(document).on('keypress', 'input[name^="sjb"], input[name^="sjl"], input[name^="mt"], input[name^="sel"], input[name^="slm"], input[name^="ssp"], input[name^="scm"], input[name^="ctt"], input[name^="cct"], input[name^="smh"], input[name^="dm"], input[name^="scf"], input[name^="lel"], input[name^="gc"], input[name^="sspk"]', function (e) {
            if (e.which === 13) {
                e.preventDefault();

                const input = $(this);
                const val = input.val().trim();
                const employeeId = input.closest('tr').data('employee-id');
                const hargaSatuan = parseFloat(input.data('harga')) || 0;
                const kodeBarang = input.attr('name');

                // Fungsi parsing yang lebih sederhana dan pasti bekerja
                function parseInput(inputStr) {
                    // Ganti semua koma dengan titik
                    const normalized = inputStr.replace(/,/g, '.');
                    
                    // Split hanya berdasarkan tanda + saja
                    const parts = normalized.split('+').filter(Boolean);
                    
                    return parts.map(part => {
                        // Parse angka, termasuk yang tanpa titik decimal
                        const num = parseFloat(part);
                        return isNaN(num) ? 0 : num; // Return 0 jika bukan angka
                    });
                }

                const numbers = parseInput(val);
                const total = numbers.reduce((sum, n) => sum + n, 0);
                
                if (total === 0 && val !== '0') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format salah',
                        text: 'Gunakan format seperti: 0.9+0.7 atau 0,9+0,7',
                    });
                    return;
                }

                input.val(total.toFixed(2));

                // ✅ SIMPAN KE GLOBAL VARIABLE
                const $row = input.closest('tr');
                const key = `${employeeId}_${kodeBarang}`;
                
                employeeItemDetails[key] = {
                    items: numbers.map(berat => ({ berat, harga: hargaSatuan })),
                    totalBerat: total,
                    totalHarga: total * hargaSatuan
                };

                // 🔁 Rehitung total jlhkg & rp
                let jlhkg = 0;
                let rp = 0;

                Object.keys(employeeItemDetails).forEach(keyLoop => {
                    if (keyLoop.startsWith(`${employeeId}_`) && keyLoop !== `${employeeId}_main`) {
                        const data = employeeItemDetails[keyLoop];
                        jlhkg += data.totalBerat || 0;
                        rp += data.totalHarga || 0;
                    }
                });

                const mainKey = `${employeeId}_main`;
                employeeItemDetails[mainKey] = { jlhkg, rp };

                // Update input field jlhkg & rp
                $row.find(`input[name="jlhkg"]`).val(jlhkg.toFixed(2));
                $row.find(`input[name="rp"]`).val(rp.toFixed());
            }

        });



        // Handle delete harga
        $(document).on('click', '.hapus-harga', function() {
            $(this).closest('tr').remove();
        });
        
        // Handle simpan harga
        $('#btnSimpanHarga').click(function() {
            const employeeId = $('#modalDetailHarga').data('employeeId');
            const kodeBarang = $('#modalDetailHarga').data('kodeBarang');
            const items = [];
            let totalBerat = 0;
            let totalHarga = 0;

            $('#tableHargaBody tr').each(function() {
                const berat = parseFloat($(this).find('td:eq(0)').text()) || 0;
                const harga = parseFloat($(this).find('td:eq(1)').text()) || 0;
                items.push({ berat, harga });
                totalBerat += berat;
                totalHarga += berat * harga;
            });

            // Simpan ke variabel global
            const key = `${employeeId}_${kodeBarang}`;
            employeeItemDetails[key] = {
                items: items,
                totalBerat: totalBerat,
                totalHarga: totalHarga
            };

            // Update field sesuai kodeBarang
            $(`tr[data-employee-id="${employeeId}"] input[name="${kodeBarang}"]`).val(totalBerat.toFixed(2));

            // 🔥 Tambahan: update input jlhkg dan rp
            $(`tr[data-employee-id="${employeeId}"] input[name="jlhkg"]`).val(totalBerat.toFixed(2));
            $(`tr[data-employee-id="${employeeId}"] input[name="rp"]`).val(totalHarga.toFixed()); // tanpa koma desimal

            // Tutup modal
            $('#modalDetailHarga').modal('hide');
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

                if (response.data && Array.isArray(response.data)) {
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
                if (response.data && Array.isArray(response.data)) {
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

    function showModalDetailHarga(kodeBarang, employeeId, employeeName, hargaBarang) {
        $('#kodeBarang').val(kodeBarang);
        $('#employeeNameModal').val(employeeName);
        $('#modalDetailHarga').data('employeeId', employeeId);
        $('#modalDetailHarga').data('kodeBarang', kodeBarang);
        $('#inputHarga').val(hargaBarang);

        $('#tableHargaBody').empty();
        $('#inputBerat').val('');


        const key = `${employeeId}_${kodeBarang}`;
        if (employeeItemDetails[key]) {
            let totalBerat = 0;
            let totalHarga = 0;

            employeeItemDetails[key].items.forEach(item => {
                const subtotal = item.berat * item.harga;
                totalBerat += item.berat;
                totalHarga += subtotal;

                const newRow = `
                    <tr>
                        <td>${parseFloat(item.berat).toFixed(2)}</td>
                        <td>${parseFloat(item.harga).toLocaleString()}</td>
                        <td>
                            <button class="btn btn-sm btn-danger hapus-harga">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#tableHargaBody').append(newRow);
            });

            $('#totalBeratModal').text(totalBerat.toFixed(2));
            $('#totalHargaModal').text(totalHarga.toLocaleString());
        } else {
            $('#totalBeratModal').text('0.00');
            $('#totalHargaModal').text('0');
        }

        $('#modalDetailHarga').modal('show');
    }


</script>
<?= $this->endSection(); ?>