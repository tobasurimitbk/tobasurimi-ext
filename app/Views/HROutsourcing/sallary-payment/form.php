<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>


<style>
    /* SweetAlert Custom Styles */
.working-hours-popup {
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.working-hours-title {
    color: #2c3e50;
    font-weight: 600;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    margin: 12px 0;
    font-size: 15px;
}

.detail-label {
    color: #7f8c8d;
    margin-right: 15px;
}

.detail-value {
    font-weight: 600;
    color: #2c3e50;
}

.detail-divider {
    margin: 15px 0;
    border-color: #eee;
}

.detail-row.total {
    margin-top: 20px;
    padding-top: 10px;
    border-top: 1px dashed #eee;
}

.detail-row.total .detail-value {
    color: #27ae60;
    font-size: 16px;
}
</style>


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


<div class="modal fade" id="workingHoursModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Jam Kerja</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="time-input-group">
          <label>Jam Berangkat:</label>
          <input type="number" id="departureTime" class="form-control" min="0" max="23.99" step="0.01" placeholder="0-24">
        </div>
        <div class="time-input-group">
          <label>Jam Pulang:</label>
          <input type="number" id="returnTime" class="form-control" min="0" max="23.99" step="0.01" placeholder="0-24">
        </div>
        <div class="time-input-group">
          <label>Istirahat (jam):</label>
          <input type="number" id="breakTime" class="form-control" min="0" step="0.01" placeholder="0">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="saveWorkingHours">Simpan</button>
      </div>
    </div>
  </div>
</div>

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

        function formatHHMMtoJamString(value) {
            if (!value) return '';
            
            value = value.toString().padStart(4, '0'); // Biar aman, misal 930 jadi 0930
            const jam = value.slice(0, -2);  // ambil dari depan, kecuali 2 terakhir
            const menit = value.slice(-2);   // ambil 2 karakter terakhir

            return `${jam}.${menit}`;
        }

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
                    if ($('#departemen option:selected').val() == "5") {
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
                    } else {
                        if (employee && employee.employee_id) {
                            const employeeId = employee.employee_id;
                                
                            const codes = [
                                'SUAC', 'CUU', 'AU', 'MKU/MDU', 'BU', 'BBU', 'ATU', 'CBU', 'FU', 'BUMS', 'BUM',
                                'KUM', 'SUM', 'SK', 'CKU', 'FKPH', 'BK', 'SKPL', 'SKPB', 'SKML', 'SKMB',
                                'C1', 'C2', 'CUK', 'FK', 'SSSCG', 'IKSSCG', 'FSSCG', 'KSCG', 'CSCG',
                                'MPA', 'LBL', 'SA', 'CU', 'HK'
                            ];

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

                            window.employeeWorkingDetails = {};

                            // 2. Langsung gunakan paymentData yang sudah ada
                            if (Array.isArray(paymentData)) {
                                paymentData.forEach(employee => {
                                    // Loop melalui semua properti employee
                                    Object.keys(employee).forEach(key => {
                                        if (key.startsWith('jam_kerja_')) {
                                            const code = key.replace('jam_kerja_', '');
                                            const detail = employee[key];
                                            
                                            // Gunakan employee_id sebagai bagian dari key untuk keunikan
                                            window.employeeWorkingDetails[`${employee.employee_id}_jam_kerja_${code}`] = {
                                                departure: detail.departure || 0,
                                                return: detail.return || 0,
                                                break: detail.break || 0,
                                                total: detail.total || 0
                                            };
                                        }
                                    });
                                });
                            } else {
                                console.error("PaymentData is not in expected array format");
                            }

                            console.log("Employee Working Details:", window.employeeWorkingDetails);
                            
                        }
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
                    <th class="text-center" rowspan="3">ACTION</th>
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
                <tr">
                    <th class="text-center" rowspan="3">ACTION</th>
                    <th class="text-center" rowspan="3">NO</th>
                    <th class="text-center" rowspan="3">TMK</th>
                    <th class="text-center" rowspan="3">NO BADGE</th>
                    <th class="text-center" rowspan="3">NAMA</th>
                    <th class="text-center" colspan="13">UDANG</th>
                    <th class="text-center" colspan="4">KEPAH</th>
                    <th class="text-center" colspan="8">KEPITING MERAH DAN PUTIH</th>
                    <th class="text-center" colspan="5">SOKAT / CUMI / GURITA</th>
                    <th class="text-center" colspan="5">ALL</th>
                    <th class="text-center" colspan="35">JAM</th>
                    <th class="text-center" rowspan="3">TOTAL KG</th>
                    <th class="text-center" rowspan="3">TOTAL JAM</th>
                    <th class="text-center" rowspan="3">TOTAL BORONGAN JAM</th>
                    <th class="text-center" rowspan="3">JLH ORG</th>
                    <th class="text-center" rowspan="3">(Rp)</th>
                    <th class="text-center" rowspan="3">Rp/Org</th>
                    <th class="text-center" rowspan="3">SUBSIDI RP.</th>
                    <th class="text-center" rowspan="3">BOR. / JAM (Rp)</th>
                    <th class="text-center" colspan="35">KG / JAM</th>
                </tr>
                <tr>
                    <th colspan="1">SUAC</th>
                    <th colspan="1">CUU</th>
                    <th colspan="1">AU</th>
                    <th colspan="1">MKU/MDU</th>
                    <th colspan="1">BU</th>
                    <th colspan="1">BBU</th>
                    <th colspan="1">ATU</th>
                    <th colspan="1">CBU</th>
                    <th colspan="1">FU</th>
                    <th colspan="1">BUMS</th>
                    <th colspan="1">BUM</th>
                    <th colspan="1">KUM</th>
                    <th colspan="1">SUM</th>
                    <th colspan="1">SK</th>
                    <th colspan="1">CKU</th>
                    <th colspan="1">FKPH</th>
                    <th colspan="1">BK</th>
                    <th colspan="1">SKPL</th>
                    <th colspan="1">SKPB</th>
                    <th colspan="1">SKML</th>
                    <th colspan="1">SKMB</th>
                    <th colspan="1">C1</th>
                    <th colspan="1">C2</th>
                    <th colspan="1">CUK</th>
                    <th colspan="1">FK</th>
                    <th colspan="1">SSSCG</th>
                    <th colspan="1">IKSSCG</th>
                    <th colspan="1">FSSCG</th>
                    <th colspan="1">KSCG</th>
                    <th colspan="1">CSCG</th>
                    <th colspan="1">MPA</th>
                    <th colspan="1">LBL</th>
                    <th colspan="1">SA</th>
                    <th colspan="1">CU</th>
                    <th colspan="1">HK</th>
                    <th class="text-center" colspan="13">UDANG</th>
                    <th class="text-center" colspan="4">KEPAH</th>
                    <th class="text-center" colspan="8">KEPITING MERAH DAN PUTIH</th>
                    <th class="text-center" colspan="5">SOKAT / CUMI / GURITA</th>
                    <th class="text-center" colspan="5">ALL</th>
                    <th class="text-center" colspan="13">UDANG</th>
                    <th class="text-center" colspan="4">KEPAH</th>
                    <th class="text-center" colspan="8">KEPITING MERAH DAN PUTIH</th>
                    <th class="text-center" colspan="5">SOKAT / CUMI / GURITA</th>
                    <th class="text-center" colspan="5">ALL</th>
                </tr>
                <tr>
                    <th style="min-width:100px;" colspan="1">2850</th> <!-- SUAC -->
                    <th style="min-width:100px;" colspan="1">135</th>  <!-- CUU -->
                    <th style="min-width:100px;" colspan="1">33</th>   <!-- AU -->
                    <th style="min-width:100px;" colspan="1">20</th>   <!-- MKU/MDU -->
                    <th style="min-width:100px;" colspan="1">13</th>   <!-- BU -->
                    <th style="min-width:100px;" colspan="1">15</th>   <!-- BBU -->
                    <th style="min-width:100px;" colspan="1">30</th>   <!-- ATU -->
                    <th style="min-width:100px;" colspan="1">30</th>   <!-- CBU -->
                    <th style="min-width:100px;" colspan="1">15</th>   <!-- FU -->
                    <th style="min-width:100px;" colspan="1">4500</th> <!-- BUMS -->
                    <th style="min-width:100px;" colspan="1">3750</th> <!-- BUM -->
                    <th style="min-width:100px;" colspan="1">3750</th> <!-- KUM -->
                    <th style="min-width:100px;" colspan="1">750</th>  <!-- SUM -->
                    <th style="min-width:100px;" colspan="1">1800</th> <!-- SK -->
                    <th style="min-width:100px;" colspan="1">525</th>  <!-- CKU -->
                    <th style="min-width:100px;" colspan="1">23</th>   <!-- FKPH -->
                    <th style="min-width:100px;" colspan="1">75</th>   <!-- BK -->
                    <th style="min-width:100px;" colspan="1">10500</th> <!-- SKPL -->
                    <th style="min-width:100px;" colspan="1">9000</th>  <!-- SKPB -->
                    <th style="min-width:100px;" colspan="1">10500</th> <!-- SKML -->
                    <th style="min-width:100px;" colspan="1">9000</th>  <!-- SKMB -->
                    <th style="min-width:100px;" colspan="1">3000</th>  <!-- C1 -->
                    <th style="min-width:100px;" colspan="1">1500</th>  <!-- C2 -->
                    <th style="min-width:100px;" colspan="1">825</th>   <!-- CUK -->
                    <th style="min-width:100px;" colspan="1">15</th>    <!-- FK -->
                    <th style="min-width:100px;" colspan="1">150</th>   <!-- SSSCG -->
                    <th style="min-width:100px;" colspan="1">2100</th>  <!-- IKSSCG -->
                    <th style="min-width:100px;" colspan="1">30</th>    <!-- FSSCG -->
                    <th style="min-width:100px;" colspan="1">1500</th>  <!-- KSCG -->
                    <th style="min-width:100px;" colspan="1">450</th>   <!-- CSCG -->
                    <th style="min-width:100px;" colspan="1">53</th>    <!-- MPA -->
                    <th style="min-width:100px;" colspan="1">14</th>    <!-- LBL -->
                    <th style="min-width:100px;" colspan="1">1.5</th>   <!-- SA -->
                    <th style="min-width:100px;" colspan="1">3</th>     <!-- CU -->
                    <th style="min-width:100px;" colspan="1">23</th>    <!-- HK -->
                    <th style="min-width:100px;" colspan="1">SUAC</th>
                    <th style="min-width:100px;" colspan="1">CUU</th>
                    <th style="min-width:100px;" colspan="1">AU</th>
                    <th style="min-width:100px;" colspan="1">MKU/MDU</th>
                    <th style="min-width:100px;" colspan="1">BU</th>
                    <th style="min-width:100px;" colspan="1">BBU</th>
                    <th style="min-width:100px;" colspan="1">ATU</th>
                    <th style="min-width:100px;" colspan="1">CBU</th>
                    <th style="min-width:100px;" colspan="1">FU</th>
                    <th style="min-width:100px;" colspan="1">BUMS</th>
                    <th style="min-width:100px;" colspan="1">BUM</th>
                    <th style="min-width:100px;" colspan="1">KUM</th>
                    <th style="min-width:100px;" colspan="1">SUM</th>
                    <th style="min-width:100px;" colspan="1">SK</th>
                    <th style="min-width:100px;" colspan="1">CKU</th>
                    <th style="min-width:100px;" colspan="1">FKPH</th>
                    <th style="min-width:100px;" colspan="1">BK</th>
                    <th style="min-width:100px;" colspan="1">SKPL</th>
                    <th style="min-width:100px;" colspan="1">SKPB</th>
                    <th style="min-width:100px;" colspan="1">SKML</th>
                    <th style="min-width:100px;" colspan="1">SKMB</th>
                    <th style="min-width:100px;" colspan="1">C1</th>
                    <th style="min-width:100px;" colspan="1">C2</th>
                    <th style="min-width:100px;" colspan="1">CUK</th>
                    <th style="min-width:100px;" colspan="1">FK</th>
                    <th style="min-width:100px;" colspan="1">SSSCG</th>
                    <th style="min-width:100px;" colspan="1">IKSSCG</th>
                    <th style="min-width:100px;" colspan="1">FSSCG</th>
                    <th style="min-width:100px;" colspan="1">KSCG</th>
                    <th style="min-width:100px;" colspan="1">CSCG</th>
                    <th style="min-width:100px;" colspan="1">MPA</th>
                    <th style="min-width:100px;" colspan="1">LBL</th>
                    <th style="min-width:100px;" colspan="1">SA</th>
                    <th style="min-width:100px;" colspan="1">CU</th>
                    <th style="min-width:100px;" colspan="1">HK</th>
                    <th colspan="1">SUAC</th>
                    <th colspan="1">CUU</th>
                    <th colspan="1">AU</th>
                    <th colspan="1">MKU/MDU</th>
                    <th colspan="1">BU</th>
                    <th colspan="1">BBU</th>
                    <th colspan="1">ATU</th>
                    <th colspan="1">CBU</th>
                    <th colspan="1">FU</th>
                    <th colspan="1">BUMS</th>
                    <th colspan="1">BUM</th>
                    <th colspan="1">KUM</th>
                    <th colspan="1">SUM</th>
                    <th colspan="1">SK</th>
                    <th colspan="1">CKU</th>
                    <th colspan="1">FKPH</th>
                    <th colspan="1">BK</th>
                    <th colspan="1">SKPL</th>
                    <th colspan="1">SKPB</th>
                    <th colspan="1">SKML</th>
                    <th colspan="1">SKMB</th>
                    <th colspan="1">C1</th>
                    <th colspan="1">C2</th>
                    <th colspan="1">CUK</th>
                    <th colspan="1">FK</th>
                    <th colspan="1">SSSCG</th>
                    <th colspan="1">IKSSCG</th>
                    <th colspan="1">FSSCG</th>
                    <th colspan="1">KSCG</th>
                    <th colspan="1">CSCG</th>
                    <th colspan="1">MPA</th>
                    <th colspan="1">LBL</th>
                    <th colspan="1">SA</th>
                    <th colspan="1">CU</th>
                    <th colspan="1">HK</th>
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
                        <tr data-employee-id="${employeeId}" data-tmk="${tanggalMasuk}">
                            <td><button class="btn btn-danger" id="btnDelete" data-employee_id="${employeeId}"><i class="fa fa-trash"></i></button></td>
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
                            <td><input type="text" class="form-control form-control-sm" name="jlhkg" style="min-width: 100px;" value="${payment.jlhkg || ''}"></td>
                            <td><input type="text" class="form-control form-control-sm" name="jlh_org" style="min-width: 100px;" value="${payment.jlh_org || ''}"></td>
                            <td><input type="text" class="form-control form-control-sm" name="ttl_jam" style="min-width: 100px;" value="${payment.ttl_jam || ''}"></td>
                            <td><input type="text" class="form-control form-control-sm" name="rp" onkeyup="this.value = greatFormatRupiah(this.value) + ',00'" style="min-width: 100px;" value="${payment.rp || ''}"></td>
                            <td><input type="text" class="form-control form-control-sm" name="total_rp_org" onkeyup="this.value = greatFormatRupiah(this.value) + ',00'" style="min-width: 100px;" value="${payment.total_rp_org || ''}"></td>
                            
                            <!-- Subsidies -->
                            <td><input type="text" class="form-control form-control-sm" name="subsidi" onkeyup="this.value = greatFormatRupiah(this.value) + ',00'" style="min-width: 100px;" value="${payment.subsidi || ''}"></td>
                            
                            <!-- Target -->
                            <td><input type="text" class="form-control form-control-sm" name="kilo400" style="min-width: 100px;" value="${payment.kilo400 || ''}"></td>
                            <td><input type="text" class="form-control form-control-sm" name="kilo600" style="min-width: 100px;" value="${payment.kilo600 || ''}"></td>
                            
                            <!-- Final values -->
                            <td><input type="text" class="form-control form-control-sm" name="perjam" style="min-width: 100px;" value="${payment.perjam || ''}"></td>
                            <td><input type="text" class="form-control form-control-sm" name="total_kg" style="min-width: 100px;" value="${payment.total_kg || ''}"></td>
                        </tr>
                    `;
                    } else {
                        // Default department table structure
                        rowHtml = `
                        <tr data-employee-id="${employeeId}" data-tmk="${tanggalMasuk}">
                            <td><button class="btn btn-danger" id="btnDelete" data-employee_id="${employeeId}"><i class="fa fa-trash"></i></button></td>
                            <td>${$('#dataTable tbody tr').length + 1}</td>
                            <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${tanggalMasuk}" readonly></td>
                            <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${badge}" readonly></td>
                            <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${employeeName}" readonly></td>
                            
                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('SUAC', '${employeeId}', '${employeeName}', '2850')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="SUAC" value="${payment.SUAC?.berat || ''}" data-employee_id="${employeeId}" data-harga="2850">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('CUU', '${employeeId}', '${employeeName}', '135')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="CUU" value="${payment.CUU?.berat || ''}" data-employee_id="${employeeId}" data-harga="135">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('AU', '${employeeId}', '${employeeName}', '33')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="AU" value="${payment.AU?.berat || ''}" data-employee_id="${employeeId}" data-harga="33">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('MKU/MDU', '${employeeId}', '${employeeName}', '20')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="MKU/MDU" value="${payment['MKU/MDU']?.berat || ''}" data-employee_id="${employeeId}" data-harga="20">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('BU', '${employeeId}', '${employeeName}', '13')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="BU" value="${payment.BU?.berat || ''}" data-employee_id="${employeeId}" data-harga="13">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('BBU', '${employeeId}', '${employeeName}', '15')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="BBU" value="${payment.BBU?.berat || ''}" data-employee_id="${employeeId}" data-harga="15">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('ATU', '${employeeId}', '${employeeName}', '30')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="ATU" value="${payment.ATU?.berat || ''}" data-employee_id="${employeeId}" data-harga="30">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('CBU', '${employeeId}', '${employeeName}', '30')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="CBU" value="${payment.CBU?.berat || ''}" data-employee_id="${employeeId}" data-harga="30">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('FU', '${employeeId}', '${employeeName}', '15')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="FU" value="${payment.FU?.berat || ''}" data-employee_id="${employeeId}" data-harga="15">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('BUMS', '${employeeId}', '${employeeName}', '4500')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="BUMS" value="${payment.BUMS?.berat || ''}" data-employee_id="${employeeId}" data-harga="4500">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('BUM', '${employeeId}', '${employeeName}', '3750')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="BUM" value="${payment.BUM?.berat || ''}" data-employee_id="${employeeId}" data-harga="3750">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('KUM', '${employeeId}', '${employeeName}', '3750')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="KUM" value="${payment.KUM?.berat || ''}" data-employee_id="${employeeId}" data-harga="3750">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('SUM', '${employeeId}', '${employeeName}', '750')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="SUM" value="${payment.SUM?.berat || ''}" data-employee_id="${employeeId}" data-harga="750">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('SK', '${employeeId}', '${employeeName}', '1800')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="SK" value="${payment.SK?.berat || ''}" data-employee_id="${employeeId}" data-harga="1800">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('CKU', '${employeeId}', '${employeeName}', '525')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="CKU" value="${payment.CKU?.berat || ''}" data-employee_id="${employeeId}" data-harga="525">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('FKPH', '${employeeId}', '${employeeName}', '23')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="FKPH" value="${payment.FKPH?.berat || ''}" data-employee_id="${employeeId}" data-harga="23">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('BK', '${employeeId}', '${employeeName}', '75')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="BK" value="${payment.BK?.berat || ''}" data-employee_id="${employeeId}" data-harga="75">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('SKPL', '${employeeId}', '${employeeName}', '10500')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="SKPL" value="${payment.SKPL?.berat || ''}" data-employee_id="${employeeId}" data-harga="10500">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('SKPB', '${employeeId}', '${employeeName}', '9000')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="SKPB" value="${payment.SKPB?.berat || ''}" data-employee_id="${employeeId}" data-harga="9000">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('SKML', '${employeeId}', '${employeeName}', '10500')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="SKML" value="${payment.SKML?.berat || ''}" data-employee_id="${employeeId}" data-harga="10500">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('SKMB', '${employeeId}', '${employeeName}', '9000')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="SKMB" value="${payment.SKMB?.berat || ''}" data-employee_id="${employeeId}" data-harga="9000">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('C1', '${employeeId}', '${employeeName}', '3000')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="C1" value="${payment.C1?.berat || ''}" data-employee_id="${employeeId}" data-harga="3000">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('C2', '${employeeId}', '${employeeName}', '1500')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="C2" value="${payment.C2?.berat || ''}" data-employee_id="${employeeId}" data-harga="1500">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('CUK', '${employeeId}', '${employeeName}', '825')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="CUK" value="${payment.CUK?.berat || ''}" data-employee_id="${employeeId}" data-harga="825">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('FK', '${employeeId}', '${employeeName}', '15')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="FK" value="${payment.FK?.berat || ''}" data-employee_id="${employeeId}" data-harga="15">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('SSSCG', '${employeeId}', '${employeeName}', '150')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="SSSCG" value="${payment.SSSCG?.berat || ''}" data-employee_id="${employeeId}" data-harga="150">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('IKSSCG', '${employeeId}', '${employeeName}', '2100')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="IKSSCG" value="${payment.IKSSCG?.berat || ''}" data-employee_id="${employeeId}" data-harga="2100">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('FSSCG', '${employeeId}', '${employeeName}', '30')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="FSSCG" value="${payment.FSSCG?.berat || ''}" data-employee_id="${employeeId}" data-harga="30">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('KSCG', '${employeeId}', '${employeeName}', '1500')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="KSCG" value="${payment.KSCG?.berat || ''}" data-employee_id="${employeeId}" data-harga="1500">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('CSCG', '${employeeId}', '${employeeName}', '450')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="CSCG" value="${payment.CSCG?.berat || ''}" data-employee_id="${employeeId}" data-harga="450">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('MPA', '${employeeId}', '${employeeName}', '53')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="MPA" value="${payment.MPA?.berat || ''}" data-employee_id="${employeeId}" data-harga="53">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('LBL', '${employeeId}', '${employeeName}', '14')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="LBL" value="${payment.LBL?.berat || ''}" data-employee_id="${employeeId}" data-harga="14">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('SA', '${employeeId}', '${employeeName}', '1.5')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="SA" value="${payment.SA?.berat || ''}" data-employee_id="${employeeId}" data-harga="1.5">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('CU', '${employeeId}', '${employeeName}', '3')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="CU" value="${payment.CU?.berat || ''}" data-employee_id="${employeeId}" data-harga="3">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="showModalDetailHarga('HK', '${employeeId}', '${employeeName}', '23')" style="height:40px;cursor:pointer;">+</span>
                                    </div>
                                    <input type="text" class="form-control" style="height:40px;" name="HK" value="${payment.HK?.berat || ''}" data-employee_id="${employeeId}" data-harga="23">
                                </div>
                            </td>

                            <!-- JAM KERJA -->
                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_suac" class="form-control" style="height: 40px;" value="${payment.jam_kerja_suac.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_cuu" class="form-control" style="height: 40px;" value="${payment.jam_kerja_cuu.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_au" class="form-control" style="height: 40px;" value="${payment.jam_kerja_au.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_mku_mdu" class="form-control" style="height: 40px;" value="${payment.jam_kerja_mku_mdu.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_bu" class="form-control" style="height: 40px;" value="${payment.jam_kerja_bu.total || ''}">
                                </div>
                            </td>


                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_bbu" class="form-control" style="height: 40px;" value="${payment.jam_kerja_bbu.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_atu" class="form-control" style="height: 40px;" value="${payment.jam_kerja_atu.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_cbu" class="form-control" style="height: 40px;" value="${payment.jam_kerja_cbu.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_fu" class="form-control" style="height: 40px;" value="${payment.jam_kerja_fu.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_bums" class="form-control" style="height: 40px;" value="${payment.jam_kerja_bums.total || ''}">
                                </div>
                            </td>


                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_bum" class="form-control" style="height: 40px;" value="${payment.jam_kerja_bum.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_kum" class="form-control" style="height: 40px;" value="${payment.jam_kerja_kum.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_sum" class="form-control" style="height: 40px;" value="${payment.jam_kerja_sum.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_sk" class="form-control" style="height: 40px;" value="${payment.jam_kerja_sk.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_cku" class="form-control" style="height: 40px;" value="${payment.jam_kerja_cku.total || ''}">
                                </div>
                            </td>


                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_fkph" class="form-control" style="height: 40px;" value="${payment.jam_kerja_fkph.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_bk" class="form-control" style="height: 40px;" value="${payment.jam_kerja_bk.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_skpl" class="form-control" style="height: 40px;" value="${payment.jam_kerja_skpl.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_skpb" class="form-control" style="height: 40px;" value="${payment.jam_kerja_skpb.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_skml" class="form-control" style="height: 40px;" value="${payment.jam_kerja_skml.total || ''}">
                                </div>
                            </td>


                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_skmb" class="form-control" style="height: 40px;" value="${payment.jam_kerja_skmb.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_c1" class="form-control" style="height: 40px;" value="${payment.jam_kerja_c1.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_c2" class="form-control" style="height: 40px;" value="${payment.jam_kerja_c2.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_cuk" class="form-control" style="height: 40px;" value="${payment.jam_kerja_cuk.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_fk" class="form-control" style="height: 40px;" value="${payment.jam_kerja_fk.total || ''}">
                                </div>
                            </td>


                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_ssscg" class="form-control" style="height: 40px;" value="${payment.jam_kerja_ssscg.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_iksscg" class="form-control" style="height: 40px;" value="${payment.jam_kerja_iksscg.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_fsscg" class="form-control" style="height: 40px;" value="${payment.jam_kerja_fsscg.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_kscg" class="form-control" style="height: 40px;" value="${payment.jam_kerja_kscg.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_cscg" class="form-control" style="height: 40px;" value="${payment.jam_kerja_cscg.total || ''}">
                                </div>
                            </td>


                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_mpa" class="form-control" style="height: 40px;" value="${payment.jam_kerja_mpa.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_lbl" class="form-control" style="height: 40px;" value="${payment.jam_kerja_lbl.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_sa" class="form-control" style="height: 40px;" value="${payment.jam_kerja_sa.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_cu" class="form-control" style="height: 40px;" value="${payment.jam_kerja_cu.total || ''}">
                                </div>
                            </td>

                            <td>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <input type="text" data-employee_id="${employeeId}" name="jam_kerja_hk" class="form-control" style="height: 40px;" value="${payment.jam_kerja_hk.total || ''}">
                                </div>
                            </td>

                            <!-- TOTAL & KALKULASI -->
                            <td><input type="text" name="total_kg" class="form-control form-control-sm total-kg" style="min-width: 100px;" value="${greatFormatRupiah(payment.total_kg) + ',00' || ''}"></td>
                            <td><input type="text" name="total_jam" class="form-control form-control-sm total-jam" style="min-width: 100px;" value="${formatHHMMtoJamString(payment.total_jam) || ''}"></td>
                            <td><input type="text" name="total_borongan_jam" class="form-control form-control-sm total-borongan-jam" style="min-width: 100px;" value="${payment.total_borongan_jam || ''}"></td>
                            <td><input type="text" name="jumlah_org" class="form-control form-control-sm jlh-org" style="min-width: 100px;" value="${payment.jumlah_org || ''}"></td>
                            <td><input type="text" name="rupiah" onkeyup="this.value = greatFormatRupiah(this.value) + ',00'" class="form-control form-control-sm rupiah" style="min-width: 120px;" value="${greatFormatRupiah(payment.rupiah) + ',00' || ''}"></td>
                            <td><input type="text" name="rupiah_org" onkeyup="this.value = greatFormatRupiah(this.value) + ',00'" class="form-control form-control-sm rupiah_org" style="min-width: 120px;" value="${greatFormatRupiah(payment.rupiah_org) + ',00' || ''}"></td>
                            <td><input type="text" name="subsidi_rupiah" onkeyup="this.value = greatFormatRupiah(this.value) + ',00'" class="form-control form-control-sm subsidi-rupiah" style="min-width: 120px;" value="${greatFormatRupiah(payment.subsidi_rupiah) + '00' || ''}"></td>
                            <td><input type="text" name="borongan_per_jam" class="form-control form-control-sm borongan-per-jam" style="min-width: 120px;" value="${payment.borongan_per_jam || ''}"></td>

                            <!-- KG / JAM -->
                            <td><input type="text" name="kg_per_jam_suac" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_suac || ''}"></td>
                            <td><input type="text" name="kg_per_jam_cuu" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_cuu || ''}"></td>
                            <td><input type="text" name="kg_per_jam_au" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_au || ''}"></td>
                            <td><input type="text" name="kg_per_jam_mku_mdu" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_mku_mdu || ''}"></td>
                            <td><input type="text" name="kg_per_jam_bu" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_bu || ''}"></td>
                            <td><input type="text" name="kg_per_jam_bbu" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_bbu || ''}"></td>
                            <td><input type="text" name="kg_per_jam_atu" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_atu || ''}"></td>
                            <td><input type="text" name="kg_per_jam_cbu" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_cbu || ''}"></td>
                            <td><input type="text" name="kg_per_jam_fu" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_fu || ''}"></td>
                            <td><input type="text" name="kg_per_jam_bums" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_bums || ''}"></td>
                            <td><input type="text" name="kg_per_jam_bum" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_bum || ''}"></td>
                            <td><input type="text" name="kg_per_jam_kum" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_kum || ''}"></td>
                            <td><input type="text" name="kg_per_jam_sum" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_sum || ''}"></td>
                            <td><input type="text" name="kg_per_jam_sk" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_sk || ''}"></td>
                            <td><input type="text" name="kg_per_jam_cku" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_cku || ''}"></td>
                            <td><input type="text" name="kg_per_jam_fkph" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_fkph || ''}"></td>
                            <td><input type="text" name="kg_per_jam_bk" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_bk || ''}"></td>
                            <td><input type="text" name="kg_per_jam_skpl" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_skpl || ''}"></td>
                            <td><input type="text" name="kg_per_jam_skpb" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_skpb || ''}"></td>
                            <td><input type="text" name="kg_per_jam_skml" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_skml || ''}"></td>
                            <td><input type="text" name="kg_per_jam_skmb" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_skmb || ''}"></td>
                            <td><input type="text" name="kg_per_jam_c1" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_c1 || ''}"></td>
                            <td><input type="text" name="kg_per_jam_c2" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_c2 || ''}"></td>
                            <td><input type="text" name="kg_per_jam_cuk" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_cuk || ''}"></td>
                            <td><input type="text" name="kg_per_jam_fk" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_fk || ''}"></td>
                            <td><input type="text" name="kg_per_jam_ssscg" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_ssscg || ''}"></td>
                            <td><input type="text" name="kg_per_jam_iksscg" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_iksscg || ''}"></td>
                            <td><input type="text" name="kg_per_jam_fsscg" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_fsscg || ''}"></td>
                            <td><input type="text" name="kg_per_jam_kscg" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_kscg || ''}"></td>
                            <td><input type="text" name="kg_per_jam_cscg" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_cscg || ''}"></td>
                            <td><input type="text" name="kg_per_jam_mpa" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_mpa || ''}"></td>
                            <td><input type="text" name="kg_per_jam_lbl" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_lbl || ''}"></td>
                            <td><input type="text" name="kg_per_jam_sa" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_sa || ''}"></td>
                            <td><input type="text" name="kg_per_jam_cu" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_cu || ''}"></td>
                            <td><input type="text" name="kg_per_jam_hk" class="form-control form-control-sm" style="min-width: 90px;" value="${payment.kg_per_jam_hk || ''}"></td>
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
                <tr data-employee-id="${employeeId}" data-tmk="${tanggalMasuk}">
                    <td><button class="btn btn-danger" id="btnDelete" data-employee_id="${employeeId}"><i class="fa fa-trash"></i></button></td>
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
                    <td><input type="text" class="form-control form-control-sm" name="jlhkg" style="min-width: 100px;"></td>
                    <td><input type="text" class="form-control form-control-sm" name="jlh_org" style="min-width: 100px;"></td>
                    <td><input type="text" class="form-control form-control-sm" name="ttl_jam" style="min-width: 100px;"></td>
                    <td><input type="text" class="form-control form-control-sm" onkeyup="this.value = greatFormatRupiah(this.value) + ',00'" name="rp" style="min-width: 100px;"></td>
                    <td><input type="text" class="form-control form-control-sm" onkeyup="this.value = greatFormatRupiah(this.value) + ',00'" name="total_rp_org" style="min-width: 100px;"></td>
                    
                    <!-- Subsidies -->
                    <td><input type="text" class="form-control form-control-sm" onkeyup="this.value = greatFormatRupiah(this.value) + ',00'" name="subsidi" style="min-width: 100px;"></td>
                            
                    <!-- Target -->
                    <td><input type="text" class="form-control form-control-sm" name="kilo400" style="min-width: 100px;"></td>
                    <td><input type="text" class="form-control form-control-sm" name="kilo600" style="min-width: 100px;"></td>
                            
                    <!-- Final values -->
                    <td><input type="text" class="form-control form-control-sm" name="perjam" style="min-width: 100px;"></td>
                    <td><input type="text" class="form-control form-control-sm" name="total_kg" style="min-width: 100px;"></td>
                </tr>
            `;
            } else {
                return `
                <tr data-employee-id="${employeeId}" data-tmk="${tanggalMasuk}">
                    <td><button class="btn btn-danger" id="btnDelete" data-employee_id="${employeeId}"><i class="fa fa-trash"></i></button></td>
                    <td>${$('#dataTable tbody tr').length + 1}</td>
                    <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${tanggalMasuk}" readonly></td>
                    <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${badge}" readonly></td>
                    <td><input type="text" class="form-control form-control-sm" style="min-width: 100px;" value="${employeeName}" data-id="${employeeId}" readonly></td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('SUAC', '${employeeId}', '${employeeName}', '2850')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="SUAC" data-employee_id="${employeeId}" data-harga="2850">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('CUU', '${employeeId}', '${employeeName}', '135')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="CUU" data-employee_id="${employeeId}" data-harga="135">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('AU', '${employeeId}', '${employeeName}', '33')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="AU" data-employee_id="${employeeId}" data-harga="33">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('MKU/MDU', '${employeeId}', '${employeeName}', '20')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="MKU/MDU" data-employee_id="${employeeId}" data-harga="20">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('BU', '${employeeId}', '${employeeName}', '13')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="BU" data-employee_id="${employeeId}" data-harga="13">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('BBU', '${employeeId}', '${employeeName}', '15')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="BBU" data-employee_id="${employeeId}" data-harga="15">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('ATU', '${employeeId}', '${employeeName}', '30')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="ATU" data-employee_id="${employeeId}" data-harga="30">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('CBU', '${employeeId}', '${employeeName}', '30')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="CBU" data-employee_id="${employeeId}" data-harga="30">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('FU', '${employeeId}', '${employeeName}', '15')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="FU" data-employee_id="${employeeId}" data-harga="15">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('BUMS', '${employeeId}', '${employeeName}', '4500')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="BUMS" data-employee_id="${employeeId}" data-harga="4500">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('BUM', '${employeeId}', '${employeeName}', '3750')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="BUM" data-employee_id="${employeeId}" data-harga="3750">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('KUM', '${employeeId}', '${employeeName}', '3750')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="KUM" data-employee_id="${employeeId}" data-harga="3750">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('SUM', '${employeeId}', '${employeeName}', '750')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="SUM" data-employee_id="${employeeId}" data-harga="750">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('SK', '${employeeId}', '${employeeName}', '1800')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="SK" data-employee_id="${employeeId}" data-harga="1800">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('CKU', '${employeeId}', '${employeeName}', '525')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="CKU" data-employee_id="${employeeId}" data-harga="525">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('FKPH', '${employeeId}', '${employeeName}', '23')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="FKPH" data-employee_id="${employeeId}" data-harga="23">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('BK', '${employeeId}', '${employeeName}', '75')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="BK" data-employee_id="${employeeId}" data-harga="75">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('SKPL', '${employeeId}', '${employeeName}', '10500')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="SKPL" data-employee_id="${employeeId}" data-harga="10500">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('SKPB', '${employeeId}', '${employeeName}', '9000')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="SKPB" data-employee_id="${employeeId}" data-harga="9000">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('SKML', '${employeeId}', '${employeeName}', '10500')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="SKML" data-employee_id="${employeeId}" data-harga="10500">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('SKMB', '${employeeId}', '${employeeName}', '9000')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="SKMB" data-employee_id="${employeeId}" data-harga="9000">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('C1', '${employeeId}', '${employeeName}', '3000')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="C1" data-employee_id="${employeeId}" data-harga="3000">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('C2', '${employeeId}', '${employeeName}', '1500')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="C2" data-employee_id="${employeeId}" data-harga="1500">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('CUK', '${employeeId}', '${employeeName}', '825')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="CUK" data-employee_id="${employeeId}" data-harga="825">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('FK', '${employeeId}', '${employeeName}', '15')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="FK" data-employee_id="${employeeId}" data-harga="15">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('SSSCG', '${employeeId}', '${employeeName}', '150')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="SSSCG" data-employee_id="${employeeId}" data-harga="150">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('IKSSCG', '${employeeId}', '${employeeName}', '2100')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="IKSSCG" data-employee_id="${employeeId}" data-harga="2100">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('FSSCG', '${employeeId}', '${employeeName}', '30')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="FSSCG" data-employee_id="${employeeId}" data-harga="30">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('KSCG', '${employeeId}', '${employeeName}', '1500')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="KSCG" data-employee_id="${employeeId}" data-harga="1500">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('CSCG', '${employeeId}', '${employeeName}', '450')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="CSCG" data-employee_id="${employeeId}" data-harga="450">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('MPA', '${employeeId}', '${employeeName}', '53')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="MPA" data-employee_id="${employeeId}" data-harga="53">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('LBL', '${employeeId}', '${employeeName}', '14')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="LBL" data-employee_id="${employeeId}" data-harga="14">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('SA', '${employeeId}', '${employeeName}', '1.5')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="SA" data-employee_id="${employeeId}" data-harga="1.5">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('CU', '${employeeId}', '${employeeName}', '3')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="CU" data-employee_id="${employeeId}" data-harga="3">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="showModalDetailHarga('HK', '${employeeId}', '${employeeName}', '23')" style="height:40px;cursor:pointer;">+</span>
                            </div>
                            <input type="text" class="form-control" style="height:40px;" name="HK" data-employee_id="${employeeId}" data-harga="23">
                        </div>
                    </td>


                    <!-- JAM KERJA -->
                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_suac" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_cuu" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_au" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_mku_mdu" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_bu" class="form-control" style="height: 40px;">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_bbu" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_atu" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_cbu" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_fu" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_bums" class="form-control" style="height: 40px;">
                        </div>
                    </td>


                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_bum" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_kum" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_sum" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_sk" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_cku" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_fkph" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_bk" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_skpl" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_skpb" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_skml" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_skmb" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_c1" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_c2" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_cuk" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_fk" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_ssscg" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_iksscg" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_fsscg" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_kscg" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_cscg" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_mpa" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_lbl" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_sa" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_cu" class="form-control" style="height: 40px;">
                        </div>
                    </td>

                    <td>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text eye-btn" style="height:40px;cursor:pointer;"><i class="fa fa-eye"></i></span>
                            </div>
                            <input type="text" data-employee_id="${employeeId}" name="jam_kerja_hk" class="form-control" style="height: 40px;">
                        </div>
                    </td> 

                    <!-- TOTAL & KALKULASI -->
                    <td><input type="text" name="total_kg" class="form-control form-control-sm total-kg" style="min-width: 100px;"></td>
                    <td><input type="text" name="total_jam" class="form-control form-control-sm total-jam" style="min-width: 100px;"></td>
                    <td><input type="text" name="total_borongan_jam"  class="form-control form-control-sm total-borongan-jam" style="min-width: 100px;"></td>
                    <td><input type="text" name="jumlah_org" class="form-control form-control-sm jlh-org" value="1" style="min-width: 100px;"></td>
                    <td><input type="text" name="rupiah" onkeyup="this.value = greatFormatRupiah(this.value) + ',00'"  class="form-control form-control-sm rupiah" style="min-width: 120px;"></td>
                    <td><input type="text" name="rupiah_org" onkeyup="this.value = greatFormatRupiah(this.value) + ',00'"  class="form-control form-control-sm rupiah_org" style="min-width: 120px;"></td>
                    <td><input type="text" name="subsidi_rupiah" class="form-control form-control-sm subsidi-rupiah" onkeyup="this.value = greatFormatRupiah(this.value) + ',00'"  style="min-width: 120px;"></td>
                    <td><input type="text" name="borongan_per_jam" class="form-control form-control-sm borongan-per-jam" style="min-width: 120px;"></td>

                    <!-- KG / JAM -->
                    <td><input type="text" name="kg_per_jam_suac" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_cuu" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_au" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_mku_mdu" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_bu" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_bbu" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_atu" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_cbu" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_fu" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_bums" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_bum" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_kum" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_sum" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_sk" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_cku" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_fkph" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_bk" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_skpl" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_skpb" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_skml" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_skmb" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_c1" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_c2" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_cuk" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_fk" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_ssscg" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_iksscg" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_fsscg" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_kscg" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_cscg" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_mpa" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_lbl" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_sa" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_cu" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                    <td><input type="text" name="kg_per_jam_hk" class="form-control form-control-sm" style="min-width: 90px;" value=""></td>
                </tr>
            `;
            }
        }


        function collectEmployeeData() {
            employeeData = [];
            
            $('#dataTable tbody tr').each(function() {
                const $row = $(this);
                const employeeId = $row.data('employee-id');
                
                if ($('#departemen option:selected').val() == "5") {
                    // Format data untuk departemen 5 (Canning)
                    const employee = {
                        employee_id: employeeId,
                        employee_name: $row.find('td:eq(4) input').val(),
                        badge: $row.find('td:eq(3) input').val(),
                        tanggal_masuk_kerja: $row.find('td:eq(2) input').val(),

                        // Job data columns (26)
                        sjb: destroyFormatRupiah($row.find('[name="sjb"]').val()) || 0,
                        sjl: destroyFormatRupiah($row.find('[name="sjl"]').val()) || 0,
                        mt: destroyFormatRupiah($row.find('[name="mt"]').val()) || 0,
                        sel: destroyFormatRupiah($row.find('[name="sel"]').val()) || 0,
                        slm: destroyFormatRupiah($row.find('[name="slm"]').val()) || 0,
                        ssp: destroyFormatRupiah($row.find('[name="ssp"]').val()) || 0,
                        scm: destroyFormatRupiah($row.find('[name="scm"]').val()) || 0,
                        ctt: destroyFormatRupiah($row.find('[name="ctt"]').val()) || 0,
                        cct: destroyFormatRupiah($row.find('[name="cct"]').val()) || 0,
                        smh: destroyFormatRupiah($row.find('[name="smh"]').val()) || 0,
                        dm: destroyFormatRupiah($row.find('[name="dm"]').val()) || 0,
                        scf: destroyFormatRupiah($row.find('[name="scf"]').val()) || 0,
                        lel: destroyFormatRupiah($row.find('[name="lel"]').val()) || 0,
                        gc: destroyFormatRupiah($row.find('[name="gc"]').val()) || 0,
                        sspk: destroyFormatRupiah($row.find('[name="sspk"]').val()) || 0,
                        kjb: destroyFormatRupiah($row.find('[name="kjb"]').val()) || 0,
                        kjl: destroyFormatRupiah($row.find('[name="kjl"]').val()) || 0,
                        klp: destroyFormatRupiah($row.find('[name="klp"]').val()) || 0,
                        ksp: destroyFormatRupiah($row.find('[name="ksp"]').val()) || 0,
                        kcl: destroyFormatRupiah($row.find('[name="kcl"]').val()) || 0,
                        kcm: destroyFormatRupiah($row.find('[name="kcm"]').val()) || 0,
                        klg: destroyFormatRupiah($row.find('[name="klg"]').val()) || 0,
                        lm: destroyFormatRupiah($row.find('[name="lm"]').val()) || 0,
                        kel: destroyFormatRupiah($row.find('[name="kel"]').val()) || 0,
                        kcf: destroyFormatRupiah($row.find('[name="kcf"]').val()) || 0,
                        cu: destroyFormatRupiah($row.find('[name="cu"]').val()) || 0,

                        // Summary columns
                        jlhkg: destroyFormatRupiah($row.find('[name="jlhkg"]').val()) || 0,
                        jlh_org: destroyFormatRupiah($row.find('[name="jlh_org"]').val()) || 0,
                        ttl_jam: destroyFormatRupiah($row.find('[name="ttl_jam"]').val()) || 0,
                        rp: destroyFormatRupiah($row.find('[name="rp"]').val()) || 0,
                        total_rp_org: destroyFormatRupiah($row.find('[name="total_rp_org"]').val()) || 0,

                        // Subsidies
                        subsidi: destroyFormatRupiah($row.find('[name="subsidi"]').val()) || 0,

                        // Target
                        kilo400: destroyFormatRupiah($row.find('[name="kilo400"]').val()) || 0,
                        kilo600: destroyFormatRupiah($row.find('[name="kilo600"]').val()) || 0,

                        // Final values
                        perjam: destroyFormatRupiah($row.find('[name="perjam"]').val()) || 0,
                        total_kg: destroyFormatRupiah($row.find('[name="total_kg"]').val()) || 0,
                        
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
                } else {
                    // Format data untuk departemen lain (menggunakan struktur yang disimpan sebelumnya)
                    const employee = {
                        employee_id: employeeId,
                        employee_name: $row.find('td:eq(4) input').val(),
                        badge: $row.find('td:eq(3) input').val(),
                        tanggal_masuk_kerja: $row.find('td:eq(2) input').val(),

                        // Data pekerjaan untuk departemen lain
                        SUAC: destroyFormatRupiah($row.find('[name="SUAC"]').val()) || 0,
                        CUU: destroyFormatRupiah($row.find('[name="CUU"]').val()) || 0,
                        AU: destroyFormatRupiah($row.find('[name="AU"]').val()) || 0,
                        'MKU/MDU': destroyFormatRupiah($row.find('[name="MKU/MDU"]').val()) || 0,
                        BU: destroyFormatRupiah($row.find('[name="BU"]').val()) || 0,
                        BBU: destroyFormatRupiah($row.find('[name="BBU"]').val()) || 0,
                        ATU: destroyFormatRupiah($row.find('[name="ATU"]').val()) || 0,
                        CBU: destroyFormatRupiah($row.find('[name="CBU"]').val()) || 0,
                        FU: destroyFormatRupiah($row.find('[name="FU"]').val()) || 0,
                        BUMS: destroyFormatRupiah($row.find('[name="BUMS"]').val()) || 0,
                        BUM: destroyFormatRupiah($row.find('[name="BUM"]').val()) || 0,
                        KUM: destroyFormatRupiah($row.find('[name="KUM"]').val()) || 0,
                        SUM: destroyFormatRupiah($row.find('[name="SUM"]').val()) || 0,
                        SK: destroyFormatRupiah($row.find('[name="SK"]').val()) || 0,
                        CKU: destroyFormatRupiah($row.find('[name="CKU"]').val()) || 0,
                        FKPH: destroyFormatRupiah($row.find('[name="FKPH"]').val()) || 0,
                        BK: destroyFormatRupiah($row.find('[name="BK"]').val()) || 0,
                        SKPL: destroyFormatRupiah($row.find('[name="SKPL"]').val()) || 0,
                        SKPB: destroyFormatRupiah($row.find('[name="SKPB"]').val()) || 0,
                        SKML: destroyFormatRupiah($row.find('[name="SKML"]').val()) || 0,
                        SKMB: destroyFormatRupiah($row.find('[name="SKMB"]').val()) || 0,
                        C1: destroyFormatRupiah($row.find('[name="C1"]').val()) || 0,
                        C2: destroyFormatRupiah($row.find('[name="C2"]').val()) || 0,
                        CUK: destroyFormatRupiah($row.find('[name="CUK"]').val()) || 0,
                        FK: destroyFormatRupiah($row.find('[name="FK"]').val()) || 0,
                        SSSCG: destroyFormatRupiah($row.find('[name="SSSCG"]').val()) || 0,
                        IKSSCG: destroyFormatRupiah($row.find('[name="IKSSCG"]').val()) || 0,
                        FSSCG: destroyFormatRupiah($row.find('[name="FSSCG"]').val()) || 0,
                        KSCG: destroyFormatRupiah($row.find('[name="KSCG"]').val()) || 0,
                        CSCG: destroyFormatRupiah($row.find('[name="CSCG"]').val()) || 0,
                        MPA: destroyFormatRupiah($row.find('[name="MPA"]').val()) || 0,
                        LBL: destroyFormatRupiah($row.find('[name="LBL"]').val()) || 0,
                        SA: destroyFormatRupiah($row.find('[name="SA"]').val()) || 0,
                        CU: destroyFormatRupiah($row.find('[name="CU"]').val()) || 0,
                        HK: destroyFormatRupiah($row.find('[name="HK"]').val()) || 0,
                        
                        total_kg: destroyFormatRupiah($row.find('[name="total_kg"]').val()) || 0,
                        total_jam: destroyFormatRupiah($row.find('[name="total_jam"]').val()) || 0,
                        total_borongan_jam: destroyFormatRupiah($row.find('[name="total_borongan_jam"]').val()) || 0,
                        jumlah_org: destroyFormatRupiah($row.find('[name="jumlah_org"]').val()) || 0,
                        rupiah: destroyFormatRupiah($row.find('[name="rupiah"]').val()) || 0,
                        rupiah_org: destroyFormatRupiah($row.find('[name="rupiah_org"]').val()) || 0,
                        subsidi_rupiah: destroyFormatRupiah($row.find('[name="subsidi_rupiah"]').val()) || 0,
                        borongan_per_jam: destroyFormatRupiah($row.find('[name="borongan_per_jam"]').val()) || 0,
                        kg_per_jam_suac: destroyFormatRupiah($row.find('[name="kg_per_jam_suac"]').val()) || 0,
                        kg_per_jam_cuu: destroyFormatRupiah($row.find('[name="kg_per_jam_cuu"]').val()) || 0,
                        kg_per_jam_au: destroyFormatRupiah($row.find('[name="kg_per_jam_au"]').val()) || 0,
                        kg_per_jam_mku_mdu: destroyFormatRupiah($row.find('[name="kg_per_jam_mku_mdu"]').val()) || 0,
                        kg_per_jam_bu: destroyFormatRupiah($row.find('[name="kg_per_jam_bu"]').val()) || 0,
                        kg_per_jam_bbu: destroyFormatRupiah($row.find('[name="kg_per_jam_bbu"]').val()) || 0,
                        kg_per_jam_atu: destroyFormatRupiah($row.find('[name="kg_per_jam_atu"]').val()) || 0,
                        kg_per_jam_cbu: destroyFormatRupiah($row.find('[name="kg_per_jam_cbu"]').val()) || 0,
                        kg_per_jam_fu: destroyFormatRupiah($row.find('[name="kg_per_jam_fu"]').val()) || 0,
                        kg_per_jam_bums: destroyFormatRupiah($row.find('[name="kg_per_jam_bums"]').val()) || 0,
                        kg_per_jam_bum: destroyFormatRupiah($row.find('[name="kg_per_jam_bum"]').val()) || 0,
                        kg_per_jam_kum: destroyFormatRupiah($row.find('[name="kg_per_jam_kum"]').val()) || 0,
                        kg_per_jam_sum: destroyFormatRupiah($row.find('[name="kg_per_jam_sum"]').val()) || 0,
                        kg_per_jam_sk: destroyFormatRupiah($row.find('[name="kg_per_jam_sk"]').val()) || 0,
                        kg_per_jam_cku: destroyFormatRupiah($row.find('[name="kg_per_jam_cku"]').val()) || 0,
                        kg_per_jam_fkph: destroyFormatRupiah($row.find('[name="kg_per_jam_fkph"]').val()) || 0,
                        kg_per_jam_bk: destroyFormatRupiah($row.find('[name="kg_per_jam_bk"]').val()) || 0,
                        kg_per_jam_skpl: destroyFormatRupiah($row.find('[name="kg_per_jam_skpl"]').val()) || 0,
                        kg_per_jam_skpb: destroyFormatRupiah($row.find('[name="kg_per_jam_skpb"]').val()) || 0,
                        kg_per_jam_skml: destroyFormatRupiah($row.find('[name="kg_per_jam_skml"]').val()) || 0,
                        kg_per_jam_skmb: destroyFormatRupiah($row.find('[name="kg_per_jam_skmb"]').val()) || 0,
                        kg_per_jam_c1: destroyFormatRupiah($row.find('[name="kg_per_jam_c1"]').val()) || 0,
                        kg_per_jam_c2: destroyFormatRupiah($row.find('[name="kg_per_jam_c2"]').val()) || 0,
                        kg_per_jam_cuk: destroyFormatRupiah($row.find('[name="kg_per_jam_cuk"]').val()) || 0,
                        kg_per_jam_fk: destroyFormatRupiah($row.find('[name="kg_per_jam_fk"]').val()) || 0,
                        kg_per_jam_ssscg: destroyFormatRupiah($row.find('[name="kg_per_jam_ssscg"]').val()) || 0,
                        kg_per_jam_iksscg: destroyFormatRupiah($row.find('[name="kg_per_jam_iksscg"]').val()) || 0,
                        kg_per_jam_fsscg: destroyFormatRupiah($row.find('[name="kg_per_jam_fsscg"]').val()) || 0,
                        kg_per_jam_kscg: destroyFormatRupiah($row.find('[name="kg_per_jam_kscg"]').val()) || 0,
                        kg_per_jam_cscg: destroyFormatRupiah($row.find('[name="kg_per_jam_cscg"]').val()) || 0,
                        kg_per_jam_mpa: destroyFormatRupiah($row.find('[name="kg_per_jam_mpa"]').val()) || 0,
                        kg_per_jam_lbl: destroyFormatRupiah($row.find('[name="kg_per_jam_lbl"]').val()) || 0,
                        kg_per_jam_sa: destroyFormatRupiah($row.find('[name="kg_per_jam_sa"]').val()) || 0,
                        kg_per_jam_cu: destroyFormatRupiah($row.find('[name="kg_per_jam_cu"]').val()) || 0,
                        kg_per_jam_hk: destroyFormatRupiah($row.find('[name="kg_per_jam_hk"]').val()) || 0,
 
                        item_details: {}
                    };

                    // Tambahkan pengecekan employeeItemDetails untuk departemen lain jika diperlukan
                    const otherCodes = ['SUAC', 'CUU', 'AU', 'MKU/MDU', 'BU', 'BBU', 'ATU', 'CBU', 'FU', 
                                    'BUMS', 'BUM', 'KUM', 'SUM', 'SK', 'CKU', 'FKPH', 'BK', 'SKPL', 
                                    'SKPB', 'SKML', 'SKMB', 'C1', 'C2', 'CUK', 'FK', 'SSSCG', 'IKSSCG', 
                                    'FSSCG', 'KSCG', 'CSCG', 'MPA', 'LBL', 'SA', 'CU', 'HK'];
                    
                    otherCodes.forEach(code => {
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

                    const allCodes = ['suac', 'cuu', 'au', 'mku_mdu', 'bu', 'bbu', 'atu', 'cbu', 'fu', 
                                    'bums', 'bum', 'kum', 'sum', 'sk', 'cku', 'fkph', 'bk', 'skpl', 
                                    'skpb', 'skml', 'skmb', 'c1', 'c2', 'cuk', 'fk', 'ssscg', 'iksscg',
                                    'fsscg', 'kscg', 'cscg', 'mpa', 'lbl', 'sa', 'cu', 'hk'];

                    allCodes.forEach(code => {
                        const key = `${employeeId}_jam_kerja_${code}`;
                        const weightKey = `${employeeId}_${code.toUpperCase()}`;
                        
                        const hoursData = window.employeeWorkingDetails?.[key] || {
                            departure: 0,
                            return: 0,
                            break: 0,
                            total: 0
                        };

                        let kgPerJam = 0;
                        if (hoursData.total > 0 && window.employeeItemDetails?.[weightKey]) {
                            kgPerJam = window.employeeItemDetails[weightKey].totalBerat / hoursData.total;
                        }

                        // Masukkan langsung ke employee dengan prefix jam_kerja_
                        employee[`jam_kerja_${code}`] = {
                            departure: hoursData.departure,
                            return: hoursData.return,
                            break: hoursData.break,
                            total: hoursData.total,
                            kg_per_jam: parseFloat(kgPerJam.toFixed(2))
                        };
                    });

                    employeeData.push(employee);
                }
            });
            
            console.log('Collected Employee Data:', employeeData);
            return employeeData;
        }

        $(document).on('click', '#btnDelete', function() {
            const employeeId = $(this).data('employee_id');
            const $row = $(this).closest('tr');
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // 1. Hapus baris dari tabel
                    $row.fadeOut(300, function() {
                        $(this).remove();
                    });
                    
                    // 2. Hapus data dari employeeData
                    if (typeof employeeData !== 'undefined') {
                        employeeData = employeeData.filter(emp => emp.employee_id !== employeeId);
                    }
                    
                    // 3. Hapus data dari employeeItemDetails
                    if (typeof employeeItemDetails !== 'undefined') {
                        Object.keys(employeeItemDetails).forEach(key => {
                            if (key.startsWith(`${employeeId}_`)) {
                                delete employeeItemDetails[key];
                            }
                        });
                    }
                    
                    // 4. Hapus data dari employeeWorkingDetails
                    if (typeof window.employeeWorkingDetails !== 'undefined') {
                        Object.keys(window.employeeWorkingDetails).forEach(key => {
                            if (key.startsWith(`${employeeId}_`)) {
                                delete window.employeeWorkingDetails[key];
                            }
                        });
                    }
                    
                    // 5. Notifikasi sukses
                    Swal.fire({
                        title: 'Terhapus!',
                        text: 'Data karyawan berhasil dihapus.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });            
                }
            });
        });

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



        // CALCULATE PTS
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
                    $row.find(`input[name="rp"]`).val(greatFormatRupiah(rp) + ",00");
                    

            }
        });

        $(document).on('keypress', 'input[name^="jlh_org"]', function (e) {
            if (e.which === 13) {
                e.preventDefault();

                const input = $(this);
                const val = parseFloat(input.val().trim());
                const $row = input.closest('tr');
                const employeeId = $row.data('employee-id');

                if (!isNaN(val) && val > 0) {
                    const mainKey = `${employeeId}_main`;
                    const rp = employeeItemDetails[mainKey]?.rp || 0;
                    const total_rp_org = rp / val;

                    $row.find(`input[name="total_rp_org"]`).val(greatFormatRupiah(total_rp_org) + ",00");
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Input tidak valid',
                        text: 'Jumlah orang harus angka lebih dari 0',
                    });
                }
            }
        });

        //CALCULATE CANNING
        $(document).on('keypress', 'input[name^="SUAC"], input[name^="CUU"], input[name^="AU"], input[name^="MKU/MDU"], input[name^="BU"], input[name^="BBU"], input[name^="ATU"], input[name^="CBU"], input[name^="FU"], input[name^="BUMS"], input[name^="BUM"], input[name^="KUM"], input[name^="SUM"], input[name^="SK"], input[name^="CKU"], input[name^="FKPH"], input[name^="BK"], input[name^="SKPL"], input[name^="SKPB"], input[name^="SKML"], input[name^="SKMB"], input[name^="C1"], input[name^="C2"], input[name^="CUK"], input[name^="FK"], input[name^="SSSCG"], input[name^="IKSSCG"], input[name^="FSSCG"], input[name^="KSCG"], input[name^="CSCG"], input[name^="MPA"], input[name^="LBL"], input[name^="SA"], input[name^="CU"], input[name^="HK"]', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                handleWeightInput($(this));
                // calculateKgPerJam($(this).closest('tr'));
            }
        });

        $(document).on('keypress', 'input[name^="jam_kerja_"]', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                handleWorkingHoursInput($(this));
                // calculateKgPerJam($(this).closest('tr'));
            }
        });

        function handleWeightInput(input) {
            const val = input.val().trim();
            const employeeId = input.closest('tr').data('employee-id');
            const hargaSatuan = parseFloat(input.data('harga')) || 0;
            const kodeBarang = input.attr('name');

            function parseInput(inputStr) {
                const normalized = inputStr.replace(/,/g, '.');
                const parts = normalized.split('+').filter(Boolean);
                return parts.map(part => {
                    const num = parseFloat(part);
                    return isNaN(num) ? 0 : num;
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

            const $row = input.closest('tr');
            const key = `${employeeId}_${kodeBarang}`;

            employeeItemDetails[key] = {
                items: numbers.map(berat => ({ berat, harga: hargaSatuan })),
                totalBerat: total,
                totalHarga: total * hargaSatuan
            };

            // Rehitung total total_kg & rp
            let total_kg = 0;
            let rupiah = 0;

            Object.keys(employeeItemDetails).forEach(keyLoop => {
                if (keyLoop.startsWith(`${employeeId}_`) && keyLoop !== `${employeeId}_main`) {
                    const data = employeeItemDetails[keyLoop];
                    total_kg += data.totalBerat || 0;
                    rupiah += data.totalHarga || 0;
                }
            });

            const mainKey = `${employeeId}_main`;
            employeeItemDetails[mainKey] = { total_kg, rupiah };

            $row.find(`input[name="total_kg"]`).val(total_kg.toFixed(2));
            $row.find(`input[name="rupiah"]`).val(greatFormatRupiah(rupiah) + ",00");
        }

        function handleWorkingHoursInput(input) {
            const val = input.val().trim();
            const $row = input.closest('tr');
            const employeeId = $row.data('employee-id');
            const inputName = input.attr('name');
            const code = inputName.replace('jam_kerja_', '');

            // Modified master rounding function that won't round if input is already in .25/.5/.75 format
            const masterRound = (timeValue, type = 'result') => {
                // Check if the value is already in quarter-hour format
                const decimalPart = timeValue % 1;
                if ([0, 0.25, 0.5, 0.75].includes(decimalPart)) {
                    return timeValue;
                }
                
                const hours = Math.floor(timeValue);
                const minutes = Math.round((timeValue - hours) * 60);
                
                // Departure rounding (jam masuk)
                if (type === 'departure') {
                    if (minutes >= 46) return hours + 1;
                    if (minutes >= 31) return hours + 0.75;
                    if (minutes >= 15) return hours + 0.5;
                    return hours + 0.25;
                }
                // Return rounding (jam pulang)
                else if (type === 'return') {
                    if (minutes >= 45) return hours + 0.75;
                    if (minutes >= 30) return hours + 0.5;
                    if (minutes >= 15) return hours + 0.25;
                    return hours;
                }
                // Final result rounding (hasil total)
                else {
                    if (minutes >= 46) return hours + 1;
                    if (minutes >= 31) return hours + 0.75;
                    if (minutes >= 15) return hours + 0.5;
                    return hours + 0.25;
                }
            };

            // Parse time input - now checks for pre-rounded values first
            const parseTimeInput = (timeStr, type) => {
                // Directly convert if it's already in decimal format
                const decimalValue = parseFloat(timeStr.replace(',', '.'));
                if (!isNaN(decimalValue)) {
                    // Check if it's already in quarter-hour format
                    const decimalPart = decimalValue % 1;
                    if ([0, 0.25, 0.5, 0.75].includes(decimalPart)) {
                        return decimalValue;
                    }
                }
                
                // Fallback to normal parsing if not in quarter-hour format
                if (!timeStr.includes('.')) return parseFloat(timeStr) || 0;
                
                const [hours, minutes] = timeStr.split('.').map(Number);
                const decimalTime = hours + (minutes / 60);
                return masterRound(decimalTime, type);
            };

            if (val.includes('-')) {
                const parts = val.split('-').map(part => part.trim());
                
                if (parts.length === 3) {
                    try {
                        // Parse with proper handling of pre-rounded values
                        const returnTime = parseTimeInput(parts[0], 'return');
                        const departure = parseTimeInput(parts[1], 'departure');
                        const breakTime = parseTimeInput(parts[2], 'return');
                        
                        // Validate
                        if (isNaN(departure) || isNaN(returnTime) || isNaN(breakTime)) {
                            throw new Error('Format waktu tidak valid');
                        }
                        if ([departure, returnTime, breakTime].some(t => t < 0 || t >= 24)) {
                            throw new Error('Waktu harus antara 0-24');
                        }

                        // Calculate with overnight handling
                        let workingHours = returnTime > departure 
                            ? returnTime - departure - breakTime 
                            : (24 - departure) + returnTime - breakTime;
                        
                        workingHours = Math.max(0, workingHours);
                        
                        // Apply final rounding to result
                        const roundedTotal = masterRound(workingHours, 'result');
                        
                        // Store data
                        const key = `${employeeId}_${inputName}`;
                        window.employeeWorkingDetails = window.employeeWorkingDetails || {};
                        window.employeeWorkingDetails[key] = {
                            raw_input: val,
                            departure: departure,
                            return: returnTime,
                            break: breakTime,
                            total_before_round: workingHours,
                            total: roundedTotal
                        };
                        
                        // Format display
                        const displayValue = Number.isInteger(roundedTotal) 
                            ? roundedTotal.toString() 
                            : roundedTotal.toFixed(2).replace('.', ',');
                        input.val(displayValue);

                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Input',
                            html: `<div>${error.message}</div>
                                <div class="mt-2"><strong>Contoh format benar:</strong><br>
                                11.75-7.5-0 (JamPulang-JamMasuk-Istirahat)</div>
                                <div class="text-muted small mt-2">Gunakan format desimal (.25, .5, .75) untuk menit</div>`,
                            confirmButtonText: 'Mengerti'
                        });
                        input.val('').focus();
                        return;
                    }
                }
            }
            
            // Calculate and round totals
            let totalJam = 0;
            $row.find('input[name^="jam_kerja_"]').each(function() {
                const val = $(this).val().replace(',', '.');
                totalJam += parseFloat(val) || 0;
            });
            
            // Apply final rounding to total jam
            const roundedTotalJam = masterRound(totalJam, 'result');
            $row.find('input[name="total_jam"]').val(
                Number.isInteger(roundedTotalJam) 
                    ? roundedTotalJam.toString() 
                    : roundedTotalJam.toFixed(2).replace('.', ',')
            );
            
            // Update productivity calculations
            calculateKgPerJam(input);
        }

        // Eye button click handler - now just shows the details without saving
        $(document).on('click', '.eye-btn', function() {
            console.log(window.employeeWorkingDetails);
            const btn = $(this);
            const input = btn.closest('.input-group').find('input');
            const code = input.attr('name');
            const key = input.data("employee_id");
            console.log(key+"_"+code)
            // Get the working details from our stored data
            const workingDetails = window.employeeWorkingDetails?.[key+"_"+code] || {
                departure: 0,
                return: 0,
                break: 0,
                total: 0
            };

            console.log(workingDetails)

            // Format time display (handles both number and string)
            const formatTimeDisplay = (value) => {
                if (typeof value === 'number') {
                    const hours = Math.floor(value);
                    const minutes = Math.round((value % 1) * 60);
                    return minutes > 0 ? `${hours} ${minutes}` : `${hours} jam`;
                }
                return value;
            };

            // Create a beautiful SweetAlert dialog
            Swal.fire({
                title: `<i class="fas fa-clock"></i> Detail Jam Kerja ${code.toUpperCase()}`,
                html: `
                    <div class="text-left">
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-arrow-right"></i> Berangkat:</span>
                            <span class="detail-value">Jam ${workingDetails.departure}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-arrow-left"></i> Pulang:</span>
                            <span class="detail-value">Jam ${workingDetails.return}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-coffee"></i> Istirahat:</span>
                            <span class="detail-value">${workingDetails.break} Jam</span>
                        </div>
                        <hr class="detail-divider">
                        <div class="detail-row total">
                            <span class="detail-label"><i class="fas fa-calculator"></i> Total:</span>
                            <span class="detail-value">${workingDetails.total} Jam</span>
                        </div>
                    </div>
                `,
                confirmButtonText: '<i class="fas fa-check"></i> Tutup',
                background: '#f8f9fa',
                showCloseButton: true,
                customClass: {
                    popup: 'working-hours-popup',
                    title: 'working-hours-title',
                    htmlContainer: 'working-hours-content'
                }
            });
        });

        function calculateKgPerJam(input) {
            const $row = $(input).closest('tr');
            const employeeId = $row.data('employee-id');
            const tmk = $row.data('tmk'); // Format: YYYY-MM-DD
            const inputName = $(input).attr('name');
            
            // Extract the code from input name (jam_kerja_[code])
            const code = inputName.replace('jam_kerja_', '');
            
            // Find related inputs
            const weightInput = $row.find(`input[name="${code.toUpperCase()}"]`);
            const hoursInput = $row.find(`input[name="${inputName}"]`);
            const kgPerJamInput = $row.find(`input[name="kg_per_jam_${code}"]`);
            const subsidyInput = $row.find('input[name="subsidi_rupiah"]');
            const rupiahInput = $row.find('input[name="rupiah"]'); // Target field to update

            // Only proceed if all required inputs exist
            if (weightInput.length && hoursInput.length && kgPerJamInput.length) {
                const berat = parseFloat(weightInput.val().replace(/,/g, '.')) || 0;
                const jam = parseFloat(hoursInput.val().replace(/,/g, '.')) || 0;
                
                // Calculate kg/hour (avoid division by zero)
                const kgPerJam = jam > 0 ? (berat / jam) : 0;
                kgPerJamInput.val(kgPerJam.toFixed(2));

                // --- NEW: SUBSIDY CALCULATION ---
                if (tmk) {
                    const today = new Date(); // Current date
                    const masukKerja = new Date(tmk); // TMK (start date)
                    const diffTime = today - masukKerja;
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); // Days since TMK

                    // Check if <14 days AND worked >4 hours
                    if (diffDays < 14 && jam > 4) {
                        subsidyInput.val(40000); // Apply subsidy
                        const currentRupiah = parseFloat(rupiahInput.val().replace(/[^\d]/g, '')) || 0;
                        const newRupiah = currentRupiah - 40000;
                        // Format back to Rupiah (e.g., "50,000,00")
                        rupiahInput.val(formatRupiah(newRupiah.toString()) + ',00');
                    } else {
                        subsidyInput.val(0); // No subsidy
                    }
                }
            }
            
           
            
            // Calculate total kg/hour for all codes
            let totalKgPerJam = 0;
            $row.find('input[name^="kg_per_jam_"]').each(function() {
                totalKgPerJam += parseFloat($(this).val().replace(/,/g, '.')) || 0;
            });
            
            // Update total field
            $row.find('input[name="total_kg_per_jam"]').val(totalKgPerJam.toFixed(2));
        }

        $(document).on('keypress', 'input[name^="total_borongan_jam"]', function(e) {
            if (e.which === 13) {
                e.preventDefault();

                const input = $(this);
                const val = parseFloat(input.val().trim());
                const $row = input.closest('tr');
                const employeeId = $row.data('employee-id');

                if (!isNaN(val) && val > 0) {
                    const mainKey = `${employeeId}_main`;
                    const total = val * 10500;
                    $row.find(`input[name="borongan_per_jam"]`).val(greatFormatRupiah(total) + ",00");
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Input tidak valid',
                        text: 'Jumlah orang harus angka lebih dari 0',
                    });
                }
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

            // // 🔥 Tambahan: update input jlhkg dan rp
            // $(`tr[data-employee-id="${employeeId}"] input[name="jlhkg"]`).val(totalBerat.toFixed(2));
            // $(`tr[data-employee-id="${employeeId}"] input[name="rp"]`).val(totalHarga.toFixed()); // tanpa koma desimal

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
            $('#totalBeratModal').text('0,00');
            $('#totalHargaModal').text('0');
        }

        $('#modalDetailHarga').modal('show');
    }


</script>
<?= $this->endSection(); ?>