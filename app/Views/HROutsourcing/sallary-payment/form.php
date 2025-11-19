<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<style>
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
        <div class="col-button-tambah-spp">
            <button class="btn btn-info float-right" data-bs-toggle="modal" data-bs-target="#departmentIpModal">
                <i class="fas fa-cog"></i> Konfigurasi IP
            </button>
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("hr-outsourcing-sallary-payment"); ?>">
                Kembali
            </a>
            <?php if (empty($data)) : ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php else : ?>
                <button class="btn btn-show-form btn-save float-right btn-update-parent">
                    Update
                </button>
            <?php endif ?>
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
                            <button type="button" class="btn btn-success btn-get-data">
                                <i class="fas fa-download"></i> Get Data
                            </button>
                        </div>
                    </div>
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
                        <thead class="thead-dark" id="tableHeader"></thead>
                        <tbody class="body-detail-table"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- IP Configuration Modal Only -->
<div class="modal fade" id="departmentIpModal" tabindex="-1" aria-labelledby="departmentIpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="departmentIpModalLabel">Konfigurasi Department & IP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form Input Section -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Input Konfigurasi</h6>
                    </div>
                    <div class="card-body">
                        <form id="departmentIpForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="modalDepartment" class="form-label">Department <span class="text-danger">*</span></label>
                                        <select class="form-control" id="modalDepartment" name="modalDepartment" required>
                                            <option value="">Pilih Department</option>
                                            <?php foreach ($departement as $d): ?>
                                                <option value="<?= $d['id'] ?>"><?= $d['divisi'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="ipAddress" class="form-label">IP Address <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="ipAddress" name="ipAddress" placeholder="Contoh: 192.168.1.100" required>
                                        <div class="form-text">Format: xxx.xxx.xxx.xxx</div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Data Konfigurasi Section -->
                <div class="card">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-list me-2"></i>Data Konfigurasi Department & IP</h6>
                        <button type="button" class="btn btn-sm btn-light" id="refreshIpData">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="45%">Department</th>
                                        <th width="35%">IP Address</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="ipConfigTableBody">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            <i class="fas fa-spinner fa-spin me-2"></i>Memuat data...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 p-3 bg-light rounded">
                            <div class="row text-center">
                                <div class="col-md-6">
                                    <small class="text-muted">Total Konfigurasi: <strong id="totalConfig">0</strong></small>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Department Tersedia: <strong><?= count($departement) ?></strong></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Tutup
                </button>
                <button type="button" class="btn btn-primary" id="saveDepartmentIp">
                    <i class="fas fa-save me-1"></i> Simpan Konfigurasi
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // ===== GLOBAL VARIABLES =====
    const editMode = <?= !empty($data) ? 'true' : 'false' ?>;
    const headerData = {
        departemen: '<?= $data['divisi_id'] ?? '' ?>',
        company: '<?= $data['company_id'] ?? '' ?>',
        payment_date: '<?= $data['payment_date'] ?? '' ?>',
    };
    const currentEditId = '<?= $data['id'] ?? '' ?>';
    let currentDepartment = '<?= $data['divisi_id'] ?? '' ?>';
    let paymentData = [];
    let employeeData = [];
    let processedEmployeeData = [];
    let currentIpEditId = null;

    // ===== INITIALIZATION =====
    $(document).ready(function() {
        initializeApplication();
    });

    function initializeApplication() {
        initializeSelect2();
        initializeEventHandlers();
        initializeEditMode();
    }

    function initializeSelect2() {
        $('.select2').select2({
            theme: "bootstrap-5",
            allowClear: true
        });
    }

    function initializeEventHandlers() {
        // Department & Company Handlers
        $('#departemen').on('change', handleDepartmentChange);
        $('#company').on('change', handleCompanyChange);
        
        // Button Handlers
        $('.btn-get-data').on('click', handleGetData);
        $('.btn-save, .btn-update-parent').on('click', saveData);
        $('.btn-discard').on('click', handleDiscard);
        
        // IP Configuration Handlers
        $('#saveDepartmentIp').on('click', handleSaveDepartmentIp);
        $('#refreshIpData').on('click', handleRefreshIpData);
        
        // Dynamic Handlers
        $(document).on('click', '.btn-delete', handleDeleteEmployee);
        $(document).on('click', '.btn-push', handlePushData);
        $(document).on('click', '.btn-edit', handleEditIpConfig);
        $(document).on('click', '.view-details', handleViewItemDetails);
    }

    // ===== DEPARTMENT & COMPANY HANDLERS =====
    function handleDepartmentChange() {
        const departemenId = $(this).val();
        currentDepartment = departemenId;
        headerData.departemen = departemenId;

        $('#company').val(null).trigger('change.select2').prop('disabled', !departemenId);

        if (departemenId) {
            loadCompanies(departemenId);
        }
    }

    function handleCompanyChange() {
        const companyId = $(this).val();
        headerData.company = companyId;
    }

    // ===== DATA FETCHING =====
    function handleGetData() {
        const companyId = $('#company').val();
        const tanggal = $('#tanggal_pembayaran').val();
        const departmentId = $('#departemen').val();

        if (!validateGetData(companyId, tanggal)) return;

        toggleLoadingState(true);

        $.ajax({
            url: `<?= base_url("hr-outsourcing-sallary-payment/getData") ?>`,
            type: 'POST',
            dataType: 'json',
            data: {
                company_id: companyId,
                tanggal: tanggal,
                department_id: departmentId,
            },
            success: function(res) {
                if (res.success) {
                    handleGetDataSuccess(res.data);
                } else {
                    showError(res.message);
                }
            },
            error: function(xhr) {
                showError('Terjadi kesalahan saat mengambil data.');
            },
            complete: function() {
                toggleLoadingState(false);
            }
        });
    }


    function validateGetData(companyId, tanggal) {
        if (!companyId) {
            showWarning('Pilih perusahaan terlebih dahulu!');
            return false;
        }
        if (!tanggal) {
            showWarning('Pilih tanggal pembayaran terlebih dahulu!');
            return false;
        }
        return true;
    }

    // function getApiUrl(companyId, tanggal, callback) {
    //     const departmentId = $('#departemen').val();
        
    //     $.ajax({
    //         url: `<?= base_url("hr-outsourcing-sallary-payment/getIpByDepartment/") ?>${departmentId}`,
    //         type: 'GET',
    //         dataType: 'json',
    //         success: function(ipResponse) {
    //             if (ipResponse.success && ipResponse.ip_address) {
    //                 const baseUrl = `http://${ipResponse.ip_address}:8001/api/local-data`;
    //                 const apiUrl = `${baseUrl}?company_id=${companyId}&tanggal=${tanggal}`;
    //                 callback(apiUrl);
    //             } else {
    //                 showError(ipResponse.message || 'IP address tidak ditemukan untuk department ini');
    //                 callback(null);
    //             }
    //         },
    //         error: function(error) {
    //             showError('Gagal mengambil konfigurasi IP: ' + error.statusText);
    //             callback(null);
    //         }
    //     });
    // }

    function handleGetDataSuccess(res) {
        if (res.status === 'success' && res.data && res.data.length > 0) {
            processScaleDataToTable(res.data);
        } else {
            showAlert('warning', 'Tidak Ada Data Untuk Kriteria Yang Di Pilih');
            clearTable();
        }
    }

    function handleGetDataError(xhr) {
        console.error(xhr.responseText);
        showAlert('warning', 'Terjadi Kesalahan Bro');
    }

    function toggleLoadingState(loading) {
        const $btn = $('.btn-get-data');
        if (loading) {
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
        } else {
            $btn.prop('disabled', false).html('<i class="fas fa-download"></i> Get Data');
        }
    }

    function clearTable() {
        $('#dataTable tbody').empty();
        $('#tableHeader').empty();
    }

    // ===== DYNAMIC TABLE PROCESSING =====
    function processScaleDataToTable(scaleData) {
        $('#dataTable tbody').empty();
        
        const employeeGroups = groupScaleDataByEmployee(scaleData);
        generateDynamicTableHeader(employeeGroups);
        
        processedEmployeeData = Object.values(employeeGroups).map(employee => 
            processEmployeeDataDynamic(employee)
        );
        
        addDynamicToTable(processedEmployeeData);
    }

    function groupScaleDataByEmployee(scaleData) {
        const employeeGroups = {};
        
        scaleData.forEach(item => {
            const employeeId = item.employee_id;
            if (!employeeGroups[employeeId]) {
                employeeGroups[employeeId] = createEmployeeGroup(item);
            }
            
            if (item.item_name && item.item_name !== 'Unknown Item') {
                addItemToEmployeeGroup(employeeGroups[employeeId], item);
            }
        });
        
        return employeeGroups;
    }

    function createEmployeeGroup(item) {
        return {
            employee_id: item.employee_id,
            employee_name: item.employee_name,
            employee_badge: item.employee_badge,
            items: [],
            total_berat: 0,
            total_harga: 0,
            item_types: new Set()
        };
    }

    function addItemToEmployeeGroup(employeeGroup, item) {
        const itemData = {
            spesifikasi_id: item.spesifikasi_id,
            item_name: item.item_name,
            item_price: item.item_price ? parseFloat(destroyFormatRupiah(item.item_price)) : 0,
            weight: item.weight || 0,
            net_weight: item.net_weight || 0,
            created_at: item.created_at
        };
        
        employeeGroup.items.push(itemData);
        employeeGroup.item_types.add(item.item_name);
        
        // Calculate totals
        employeeGroup.total_berat += item.net_weight || item.weight || 0;
        employeeGroup.total_harga += (item.net_weight || item.weight || 0) * itemData.item_price;
    }

    function generateDynamicTableHeader(employeeGroups) {
        const allItemTypes = getAllItemTypes(employeeGroups);
        const headerHtml = createTableHeaderHtml(allItemTypes);
        
        $('#tableHeader').html(headerHtml);
    }

    function getAllItemTypes(employeeGroups) {
        const allItemTypes = new Set();
        Object.values(employeeGroups).forEach(employee => {
            employee.item_types.forEach(type => allItemTypes.add(type));
        });
        return Array.from(allItemTypes);
    }

    function createTableHeaderHtml(itemTypes) {
        const itemHeaders = itemTypes.map(type => 
            `<th style="min-width:100px;">${type}</th>`
        ).join('');
        
        const priceHeaders = itemTypes.map(() => 
            `<th style="min-width:100px;">HARGA</th>`
        ).join('');

        return `
            <tr>
                <th class="text-center" rowspan="3">NO</th>
                <th class="text-center" rowspan="3">BADGE</th>
                <th class="text-center" style="min-width: 150px;" rowspan="3">NAMA</th>
                <th class="text-center" colspan="${itemTypes.length}">DATA PEKERJAAN (KG)</th>
                <th class="text-center" rowspan="3">TOTAL KG</th>
                <th class="text-center" rowspan="3">TOTAL HARGA</th>
            </tr>
            <tr>${itemHeaders}</tr>
            <tr>${priceHeaders}</tr>
        `;
    }

    function processEmployeeDataDynamic(employee) {
        const result = {
            employee_id: employee.employee_id,
            employee_name: employee.employee_name,
            badge: employee.employee_badge,
            total_kg: employee.total_berat,
            total_harga: employee.total_harga,
            items: {}
        };
        
        employee.items.forEach(item => {
            const itemName = item.item_name;
            if (!result.items[itemName]) {
                result.items[itemName] = {
                    berat: 0,
                    total: 0,
                    harga: item.item_price,
                    items: []
                };
            }
            
            result.items[itemName].berat += item.net_weight || item.weight || 0;
            result.items[itemName].total += (item.net_weight || item.weight || 0) * item.item_price;
            result.items[itemName].items.push({
                berat: item.net_weight || item.weight || 0,
                harga: item.item_price,
                subtotal: (item.net_weight || item.weight || 0) * item.item_price,
                created_at: item.created_at
            });
        });
        
        return result;
    }

    function addDynamicToTable(processedData) {
        processedData.forEach((employee, index) => {
            const rowHtml = createDynamicTableRow(employee, index);
            $('#dataTable tbody').append(rowHtml);
        });
    }

    function createDynamicTableRow(employee, index) {
        const itemTypes = Object.keys(employee.items);
        
        let rowHtml = `
            <tr data-employee-id="${employee.employee_id}">
                <td>${index + 1}</td>
                <td>${employee.badge}</td>
                <td>${employee.employee_name}</td>
        `;
        
        // Add columns untuk setiap item type
        itemTypes.forEach(itemType => {
            const itemData = employee.items[itemType];
            rowHtml += `
                <td>
                    <div class="text-center">
                        <div>${itemData.berat.toFixed(2)} kg</div>
                        <small class="text-muted">${greatFormatRupiah(itemData.harga.toString())},00</small>
                    </div>
                </td>
            `;
        });
        
        // Total columns
        rowHtml += `
                <td class="text-center"><strong>${employee.total_kg.toFixed(2)} kg</strong></td>
                <td class="text-center"><strong>${greatFormatRupiah(employee.total_harga.toString())},00</strong></td>
            </tr>
        `;
        
        return rowHtml;
    }

    // ===== ITEM DETAIL VIEW =====
    function handleViewItemDetails() {
        const employeeId = $(this).data('employee-id');
        const itemType = $(this).data('item-type');
        const employeeData = processedEmployeeData.find(emp => emp.employee_id === employeeId);
        
        if (employeeData && employeeData.items[itemType]) {
            const items = employeeData.items[itemType].items;
            
            let detailHtml = `
                <div class="modal fade" id="itemDetailModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Detail ${itemType} - ${employeeData.employee_name}</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Berat (kg)</th>
                                            <th>Harga</th>
                                            <th>Subtotal</th>
                                            <th>Waktu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
            `;
            
            items.forEach((item, idx) => {
                detailHtml += `
                    <tr>
                        <td>${idx + 1}</td>
                        <td>${item.berat.toFixed(2)}</td>
                        <td>${greatFormatRupiah(item.harga.toString())},00</td>
                        <td>${greatFormatRupiah(item.subtotal.toString())},00</td>
                        <td>${new Date(item.created_at).toLocaleString()}</td>
                    </tr>
                `;
            });
            
            detailHtml += `
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2">Total</th>
                                            <th>${employeeData.items[itemType].items.length} Item</th>
                                            <th colspan="2">${greatFormatRupiah(employeeData.items[itemType].total.toString())},00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('#itemDetailModal').remove();
            $('body').append(detailHtml);
            $('#itemDetailModal').modal('show');
        }
    }

    // ===== IP CONFIGURATION MANAGEMENT =====
    function loadDepartmentIpData() {
        $('#ipConfigTableBody').html(`
            <tr>
                <td colspan="4" class="text-center text-muted">
                    <i class="fas fa-spinner fa-spin me-2"></i>Memuat data...
                </td>
            </tr>
        `);

        $.ajax({
            url: '<?= base_url("hr-outsourcing-sallary-payment/getDepartmentIpData") ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.length > 0) {
                    renderIpConfigTable(response);
                } else {
                    showNoIpConfigData();
                }
            },
            error: handleIpConfigError
        });
    }

    function renderIpConfigTable(data) {
        let html = '';
        data.forEach((item, index) => {
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.department_name}</td>
                    <td><span class="badge bg-info">${item.ip_address}</span></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-warning btn-edit" 
                                    data-id="${item.department_id}"
                                    data-name="${item.department_name}"
                                    data-ip="${item.ip_address}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-delete" 
                                    data-id="${item.department_id}"
                                    data-name="${item.department_name}">
                                <i class="fas fa-trash"></i>
                            </button>
                            <button class="btn btn-primary btn-push"
                                    data-ip="${item.ip_address}">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        $('#ipConfigTableBody').html(html);
        $('#totalConfig').text(data.length);
    }

    function showNoIpConfigData() {
        $('#ipConfigTableBody').html(`
            <tr>
                <td colspan="4" class="text-center text-muted">
                    <i class="fas fa-database me-2"></i>Belum ada data konfigurasi
                </td>
            </tr>
        `);
        $('#totalConfig').text('0');
    }

    function handleIpConfigError(xhr, status, error) {
        $('#ipConfigTableBody').html(`
            <tr>
                <td colspan="4" class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Gagal memuat data
                </td>
            </tr>
        `);
        console.error('Error loading department-IP data:', error);
    }

    function handleSaveDepartmentIp() {
        const departmentId = $('#modalDepartment').val();
        const departmentName = $('#modalDepartment option:selected').text();
        const ipAddress = $('#ipAddress').val();
        
        if (!validateIpConfig(departmentId, ipAddress)) return;
        
        const data = {
            department_id: departmentId,
            department_name: departmentName,
            ip_address: ipAddress,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        };
        
        $.ajax({
            url: '<?= base_url("hr-outsourcing-sallary-payment/saveDepartmentIp") ?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#departmentIpForm')[0].reset();
                    currentIpEditId = null;
                    loadDepartmentIpData();
                    $('#saveDepartmentIp').html('<i class="fas fa-save me-1"></i> Simpan Konfigurasi');
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error saving department-IP data:', error);
                showAlert('error', 'Terjadi kesalahan saat menyimpan data.');
            }
        });
    }

    function validateIpConfig(departmentId, ipAddress) {
        if (!departmentId || !ipAddress) {
            showAlert('warning', 'Harap pilih department dan masukkan IP address!');
            return false;
        }
        
        const ipPattern = /^(\d{1,3}\.){3}\d{1,3}$/;
        if (!ipPattern.test(ipAddress)) {
            showAlert('warning', 'Format IP address tidak valid! Contoh: 192.168.1.100');
            return false;
        }
        
        return true;
    }

    function handleRefreshIpData() {
        $(this).find('i').addClass('fa-spin');
        loadDepartmentIpData();
        setTimeout(() => {
            $(this).find('i').removeClass('fa-spin');
        }, 1000);
    }

    function handleEditIpConfig() {
        const departmentId = $(this).data('id');
        const departmentName = $(this).data('name');
        const ipAddress = $(this).data('ip');
        
        $('#modalDepartment').val(departmentId);
        $('#ipAddress').val(ipAddress);
        currentIpEditId = departmentId;
        
        $('#saveDepartmentIp').html('<i class="fas fa-sync-alt me-1"></i> Update Konfigurasi');
        
        $('.modal-body').animate({
            scrollTop: 0
        }, 500);
    }

    function handlePushData() {
        let ip = $(this).data('ip');

        Swal.fire({
            title: 'Kirim Data?',
            text: `Data akan dikirim ke server Golang: ${ip}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Kirim Sekarang',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url("hr-outsourcing-sallary-payment/push") ?>',
                    type: 'POST',
                    data: { ip: ip, <?= csrf_token() ?>: '<?= csrf_hash() ?>' },
                    dataType: 'json',
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message
                        });
                        console.log(res.go_response);
                    },
                    error: function(xhr, status, err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: err
                        });
                    }
                });
            }
        });
    }

    function handleDeleteEmployee() {
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
                $row.fadeOut(300, function() {
                    $(this).remove();
                });
                
                processedEmployeeData = processedEmployeeData.filter(emp => emp.employee_id !== employeeId);
                
                Swal.fire({
                    title: 'Terhapus!',
                    text: 'Data karyawan berhasil dihapus.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });            
            }
        });
    }

    function handleDiscard() {
        if (confirm('Apakah Anda yakin ingin membatalkan perubahan?')) {
            window.location.href = $(this).attr('href');
        }
    }

    // ===== HELPER FUNCTIONS =====
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 
                        type === 'warning' ? 'alert-warning' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 
                    type === 'warning' ? 'fa-exclamation-triangle' : 'fa-exclamation-circle';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas ${icon} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('#departmentIpForm').before(alertHtml);
        
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);
    }

    function initializeEditMode() {
        if (!headerData.departemen) return;
        
        $('#departemen').val(headerData.departemen).trigger('change.select2');
        $('#tanggal_pembayaran').val(headerData.payment_date);
        loadCompanies(headerData.departemen);
    }

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

    // ===== DATA SAVING =====
    function saveData() {
        if (!validateSaveData()) return;

        Swal.fire({
            title: 'Menyimpan data',
            html: 'Mohon tunggu...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = {
            departemen: $('#departemen').val(),
            company: $('#company').val(),
            tanggal_pembayaran: $('#tanggal_pembayaran').val(),
            employee_data: JSON.stringify(collectEmployeeData()),
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        };

        const url = editMode ? '<?= base_url('/hr-outsourcing-sallary-payment/update/') ?>' + currentEditId :
            '<?= base_url('/hr-outsourcing-sallary-payment/store') ?>';

        $.ajax({
            url: url,
            type: 'POST',
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

    function validateSaveData() {
        if (!headerData.departemen || !headerData.company) {
            showWarning('Harap lengkapi data header terlebih dahulu!');
            return false;
        }

        if ($('#dataTable tbody tr').length === 0) {
            showWarning('Harap tambahkan minimal satu karyawan!');
            return false;
        }
        return true;
    }

    function collectEmployeeData() {
        return processedEmployeeData.map(employee => ({
            employee_id: employee.employee_id,
            employee_name: employee.employee_name,
            badge: employee.badge,
            total_kg: employee.total_kg,
            total_harga: employee.total_harga,
            items: employee.items
        }));
    }

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

    // Reset form ketika modal ditutup
    $('#departmentIpModal').on('hidden.bs.modal', function() {
        $('#departmentIpForm')[0].reset();
        currentIpEditId = null;
        $('#saveDepartmentIp').html('<i class="fas fa-save me-1"></i> Simpan Konfigurasi');
    });

    // Load data pertama kali ketika modal dibuka
    $('#departmentIpModal').on('show.bs.modal', function() {
        loadDepartmentIpData();
    });
</script>

<?= $this->endSection(); ?>