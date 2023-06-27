<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

// AUTH
$routes->get('/', 'Setting\Auth::login');
$routes->post('/login', 'Setting\Auth::doLogin');
$routes->get('/logout', 'Setting\Auth::doLogout');

$routes->get('/change-company', 'Setting\User::changeCompany');

// DASHBOARD
$routes->get('/dashboard', 'Dashboard\Dashboard::dashboard', ['filter' => 'Auth']);

// MASTER DATA
// EMPLOYEE
$routes->get('/employee', 'Master\Employee::employee', ['filter' => 'Auth']);
$routes->get('/employee/all', 'Master\Employee::allEmployee', ['filter' => 'Auth']);
$routes->get('/employee/id/(:segment)', 'Master\Employee::getByIdEmployee/$1', ['filter' => 'Auth']);
$routes->post('/employee/save', 'Master\Employee::saveEmployee', ['filter' => 'Auth']);
$routes->post('/employee/update', 'Master\Employee::updateEmployee', ['filter' => 'Auth']);
$routes->post('/employee/delete', 'Master\Employee::deleteEmployee', ['filter' => 'Auth']);

// CUSTOMER
$routes->get('/customer', 'Master\Customer::customer', ['filter' => 'Auth']);
$routes->get('/customer/all', 'Master\Customer::allCustomer', ['filter' => 'Auth']);
$routes->get('/customer/id/(:segment)', 'Master\Customer::getByIdCustomer/$1', ['filter' => 'Auth']);
$routes->post('/customer/save', 'Master\Customer::saveCustomer', ['filter' => 'Auth']);
$routes->post('/customer/update', 'Master\Customer::updateCustomer', ['filter' => 'Auth']);
$routes->post('/customer/delete', 'Master\Customer::deleteCustomer', ['filter' => 'Auth']);

// WAREHOUSE
$routes->get('/warehouse', 'Master\Warehouse::warehouse', ['filter' => 'Auth']);
$routes->get('/warehouse/all', 'Master\Warehouse::allWarehouse', ['filter' => 'Auth']);
$routes->get('/warehouse/id/(:segment)', 'Master\Warehouse::getByIdWarehouse/$1', ['filter' => 'Auth']);
$routes->post('/warehouse/save', 'Master\Warehouse::saveWarehouse', ['filter' => 'Auth']);
$routes->post('/warehouse/update', 'Master\Warehouse::updateWarehouse', ['filter' => 'Auth']);
$routes->post('/warehouse/delete', 'Master\Warehouse::deleteWarehouse', ['filter' => 'Auth']);

// VENDOR
$routes->get('/vendor', 'Master\Vendor::vendor', ['filter' => 'Auth']);
$routes->get('/vendor/all', 'Master\Vendor::allVendor', ['filter' => 'Auth']);
$routes->get('/vendor/id/(:segment)', 'Master\Vendor::getByIdVendor/$1', ['filter' => 'Auth']);
$routes->post('/vendor/save', 'Master\Vendor::saveVendor', ['filter' => 'Auth']);
$routes->post('/vendor/update', 'Master\Vendor::updateVendor', ['filter' => 'Auth']);
$routes->post('/vendor/delete', 'Master\Vendor::deleteVendor', ['filter' => 'Auth']);

// DIVISI
$routes->get('/divisi', 'Master\Divisi::divisi', ['filter' => 'Auth']);
$routes->get('/divisi/all', 'Master\Divisi::allDivisi', ['filter' => 'Auth']);
$routes->get('/divisi/id/(:segment)', 'Master\Divisi::getByIdDivisi/$1', ['filter' => 'Auth']);
$routes->post('/divisi/save', 'Master\Divisi::saveDivisi', ['filter' => 'Auth']);
$routes->post('/divisi/update', 'Master\Divisi::updateDivisi', ['filter' => 'Auth']);
$routes->post('/divisi/delete', 'Master\Divisi::deleteDivisi', ['filter' => 'Auth']);

// COMPANY
$routes->get('/company', 'Master\Company::company', ['filter' => 'Auth']);
$routes->get('/company/all', 'Master\Company::allCompany', ['filter' => 'Auth']);
$routes->get('/company/id/(:segment)', 'Master\Company::getByIdCompany/$1', ['filter' => 'Auth']);
$routes->post('/company/save', 'Master\Company::saveCompany', ['filter' => 'Auth']);
$routes->post('/company/update', 'Master\Company::updateCompany', ['filter' => 'Auth']);
$routes->post('/company/delete', 'Master\Company::deleteCompany', ['filter' => 'Auth']);

// RAK
$routes->get('/penomoran-rak', 'Master\PenomoranRak::penomoranRak', ['filter' => 'Auth']);
$routes->get('/penomoran-rak/all', 'Master\PenomoranRak::allPenomoranRak', ['filter' => 'Auth']);
$routes->get('/penomoran-rak/id/(:segment)', 'Master\PenomoranRak::getByIdPenomoranRak/$1', ['filter' => 'Auth']);
$routes->post('/penomoran-rak/save', 'Master\PenomoranRak::savePenomoranRak', ['filter' => 'Auth']);
$routes->post('/penomoran-rak/update', 'Master\PenomoranRak::updatePenomoranRak', ['filter' => 'Auth']);
$routes->post('/penomoran-rak/delete', 'Master\PenomoranRak::deletePenomoranRak', ['filter' => 'Auth']);

// ACCOUNT
$routes->get('/account', 'Master\Account::account', ['filter' => 'Auth']);

$routes->get('/kategori-account/all', 'Master\Account::allKategoriAccount', ['filter' => 'Auth']);
$routes->get('/kategori-account/id/(:segment)', 'Master\Account::getByIdKategoriAccount/$1', ['filter' => 'Auth']);
$routes->post('/kategori-account/save', 'Master\Account::saveKategoriAccount', ['filter' => 'Auth']);
$routes->post('/kategori-account/update', 'Master\Account::updateKategoriAccount', ['filter' => 'Auth']);
$routes->post('/kategori-account/delete', 'Master\Account::deleteKategoriAccount', ['filter' => 'Auth']);

$routes->get('/header-account/all', 'Master\Account::allHeaderAccount', ['filter' => 'Auth']);
$routes->get('/header-account/id/(:segment)', 'Master\Account::getByIdHeaderAccount/$1', ['filter' => 'Auth']);
$routes->post('/header-account/save', 'Master\Account::saveHeaderAccount', ['filter' => 'Auth']);
$routes->post('/header-account/update', 'Master\Account::updateHeaderAccount', ['filter' => 'Auth']);
$routes->post('/header-account/delete', 'Master\Account::deleteHeaderAccount', ['filter' => 'Auth']);

$routes->get('/sub-account/all', 'Master\Account::allSubAccount', ['filter' => 'Auth']);
$routes->get('/sub-account/id/(:segment)', 'Master\Account::getByIdSubAccount/$1', ['filter' => 'Auth']);
$routes->post('/sub-account/save', 'Master\Account::saveSubAccount', ['filter' => 'Auth']);
$routes->post('/sub-account/update', 'Master\Account::updateSubAccount', ['filter' => 'Auth']);
$routes->post('/sub-account/update-status', 'Master\Account::updateStatusSubAccount', ['filter' => 'Auth']);
$routes->post('/sub-account/delete', 'Master\Account::deleteSubAccount', ['filter' => 'Auth']);

// KODE HS
$routes->get('/hs-code', 'Master\HSCode::hsCode', ['filter' => 'Auth']);
$routes->get('/hs-code/all', 'Master\HSCode::allHSCode', ['filter' => 'Auth']);

// SATUAN
$routes->get('/satuan', 'Master\Satuan::satuan', ['filter' => 'Auth']);
$routes->get('/satuan/all', 'Master\Satuan::allSatuan', ['filter' => 'Auth']);
$routes->get('/satuan/id/(:segment)', 'Master\Satuan::getByIdSatuan/$1', ['filter' => 'Auth']);
$routes->post('/satuan/save', 'Master\Satuan::saveSatuan', ['filter' => 'Auth']);
$routes->post('/satuan/update', 'Master\Satuan::updateSatuan', ['filter' => 'Auth']);
$routes->post('/satuan/delete', 'Master\Satuan::deleteSatuan', ['filter' => 'Auth']);

// SUPPLIER
// BAHAN BAKU
$routes->get('/supplier-bahan-baku', 'Supplier\SupplierBahanBaku::supplierBahanBaku', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-baku/all', 'Supplier\SupplierBahanBaku::allSupplierBahanBaku', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-baku/id/(:segment)', 'Supplier\SupplierBahanBaku::getByIdSupplierBahanBaku/$1', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-baku/save', 'Supplier\SupplierBahanBaku::saveSupplierBahanBaku', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-baku/update', 'Supplier\SupplierBahanBaku::updateSupplierBahanBaku', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-baku/delete', 'Supplier\SupplierBahanBaku::deleteSupplierBahanBaku', ['filter' => 'Auth']);

// BAHAN PENOLONG
$routes->get('/supplier-bahan-penolong', 'Supplier\SupplierBahanPenolong::supplierBahanPenolong', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-penolong/all', 'Supplier\SupplierBahanPenolong::allSupplierBahanPenolong', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-penolong/id/(:segment)', 'Supplier\SupplierBahanPenolong::getByIdSupplierBahanPenolong/$1', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-penolong/save', 'Supplier\SupplierBahanPenolong::saveSupplierBahanPenolong', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-penolong/update', 'Supplier\SupplierBahanPenolong::updateSupplierBahanPenolong', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-penolong/delete', 'Supplier\SupplierBahanPenolong::deleteSupplierBahanPenolong', ['filter' => 'Auth']);

// BAHAN IMPORT
$routes->get('/supplier-bahan-import', 'Supplier\SupplierBahanImport::supplierBahanImport', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-import/all', 'Supplier\SupplierBahanImport::allSupplierBahanImport', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-import/id/(:segment)', 'Supplier\SupplierBahanImport::getByIdSupplierBahanImport/$1', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-import/save', 'Supplier\SupplierBahanImport::saveSupplierBahanImport', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-import/update', 'Supplier\SupplierBahanImport::updateSupplierBahanImport', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-import/delete', 'Supplier\SupplierBahanImport::deleteSupplierBahanImport', ['filter' => 'Auth']);


// PURCHASE
// SPP
$routes->get('/spp', 'Purchase\SPP::spp', ['filter' => 'Auth']);
$routes->get('/spp/all', 'Purchase\SPP::allSPP', ['filter' => 'Auth']);
$routes->get('/spp/id/(:segment)', 'Purchase\SPP::getByIdSPP/$1', ['filter' => 'Auth']);
$routes->get('/spp/ajax', 'Purchase\SPP::getByIdSPPAjax', ['filter' => 'Auth']);
$routes->get('/spp/create', 'Purchase\SPP::createSPP', ['filter' => 'Auth']);
$routes->post('/spp/save', 'Purchase\SPP::saveSPP', ['filter' => 'Auth']);
$routes->post('/spp/update', 'Purchase\SPP::updateSPP', ['filter' => 'Auth']);
$routes->post('/spp/update-status', 'Purchase\SPP::updateStatusSPP', ['filter' => 'Auth']);
$routes->post('/spp/approve', 'Purchase\SPP::approveSPP', ['filter' => 'Auth']);
$routes->post('/spp/delete', 'Purchase\SPP::deleteSPP', ['filter' => 'Auth']);

// PO LOKAL
$routes->get('/po-lokal', 'Purchase\POLokal::poLokal', ['filter' => 'Auth']);
$routes->get('/po-lokal/all', 'Purchase\POLokal::allPOLokal', ['filter' => 'Auth']);
$routes->get('/po-lokal/create', 'Purchase\POLokal::createPOLokal', ['filter' => 'Auth']);
$routes->get('/po-lokal/id/(:segment)', 'Purchase\POLokal::getByIdPOLokal/$1', ['filter' => 'Auth']);
$routes->get('/po-lokal/ajax', 'Purchase\POLokal::getByIdPOLokalAjax', ['filter' => 'Auth']);
$routes->get('/po-lokal/create', 'Purchase\POLokal::createPOLokal', ['filter' => 'Auth']);
$routes->post('/po-lokal/save', 'Purchase\POLokal::savePOLokal', ['filter' => 'Auth']);
$routes->post('/po-lokal/update', 'Purchase\POLokal::updatePOLokal', ['filter' => 'Auth']);
$routes->post('/po-lokal/update-status', 'Purchase\POLokal::updateStatusPOLokal', ['filter' => 'Auth']);
$routes->post('/po-lokal/delete', 'Purchase\POLokal::deletePOLokal', ['filter' => 'Auth']);

// PO IMPORT
$routes->get('/po-import', 'Purchase\POImport::poImport', ['filter' => 'Auth']);
$routes->get('/po-import/all', 'Purchase\POImport::allPOImport', ['filter' => 'Auth']);
$routes->get('/po-import/create', 'Purchase\POImport::createPOImport', ['filter' => 'Auth']);
$routes->get('/po-import/id/(:segment)', 'Purchase\POImport::getByIdPOImport/$1', ['filter' => 'Auth']);
$routes->get('/po-import/ajax', 'Purchase\POImport::getByIdPOImportAjax', ['filter' => 'Auth']);
$routes->get('/po-import/create', 'Purchase\POImport::createPOImport', ['filter' => 'Auth']);
$routes->post('/po-import/save', 'Purchase\POImport::savePOImport', ['filter' => 'Auth']);
$routes->post('/po-import/update', 'Purchase\POImport::updatePOImport', ['filter' => 'Auth']);
$routes->post('/po-import/update-status', 'Purchase\POImport::updateStatusPOImport', ['filter' => 'Auth']);
$routes->post('/po-import/delete', 'Purchase\POImport::deletePOImport', ['filter' => 'Auth']);

// DROPDOWN
// PO LOKAL
$routes->get('/po-lokal/dropdown', 'Purchase\POLokal::dropdownPOLokal/$1', ['filter' => 'Auth']);
$routes->get('/po-lokal/multi/dropdown', 'Purchase\POLokal::dropdownBarangPOLokal/$1', ['filter' => 'Auth']);

// PO IMPORT
$routes->get('/po-import/dropdown', 'Purchase\POImport::dropdownPOImport/$1', ['filter' => 'Auth']);
$routes->get('/po-import/multi/dropdown', 'Purchase\POImport::dropdownBarangPOImport/$1', ['filter' => 'Auth']);

// CITY
$routes->get('/city/(:segment)', 'Master\City::getCityByProvince/$1', ['filter' => 'Auth']);

// EMPLOYEE
$routes->get('/employee/dropdown', 'Master\Employee::dropdownEmployee', ['filter' => 'Auth']);
$routes->get('/employee-pic/dropdown', 'Master\Employee::dropdownEmployeePIC', ['filter' => 'Auth']);

// METADATA
$routes->get('/metadata/dropdown', 'Master\Metadata::dropdownMetadata', ['filter' => 'Auth']);

// KODE HS
$routes->get('/hs-code/dropdown', 'Master\HSCode::dropdownHSCode', ['filter' => 'Auth']);

// SATUAN
$routes->get('/satuan/dropdown', 'Master\Satuan::dropdownSatuan', ['filter' => 'Auth']);

// METADATA
$routes->get('/metadata/dropdown', 'Master\Metadata::dropdownMetadata', ['filter' => 'Auth']);

// COMPANY
$routes->get('/company/dropdown', 'Master\Company::dropdownCompany', ['filter' => 'Auth']);

// ROLE
$routes->get('/role/dropdown', 'Setting\Role::dropdownRole', ['filter' => 'Auth']);

// USER
$routes->get('/user/dropdown', 'Setting\User::dropdownUser', ['filter' => 'Auth']);

// WAREHOUSE
$routes->get('/warehouse/dropdown', 'Master\Warehouse::dropdownWarehouse', ['filter' => 'Auth']);

// DIVISI
$routes->get('/divisi/dropdown', 'Master\Divisi::dropdownDivisi', ['filter' => 'Auth']);

// TAX
$routes->get('/tax/dropdown', 'Master\Tax::dropdownTax', ['filter' => 'Auth']);

// BARANG
$routes->get('/barang/dropdown', 'Warehouse\Barang::dropdownBarang', ['filter' => 'Auth']);

// ACCOUNT
$routes->get('/kategori-account/dropdown', 'Master\Account::dropdownKategoriAccount', ['filter' => 'Auth']);
$routes->get('/header-account/dropdown', 'Master\Account::dropdownHeaderAccount', ['filter' => 'Auth']);
$routes->get('/sub-account/dropdown', 'Master\Account::dropdownSubAccount', ['filter' => 'Auth']);

// WAREHOUSE
// MASTER BARANG
$routes->get('/barang', 'Warehouse\Barang::barang', ['filter' => 'Auth']);
$routes->get('/barang/all', 'Warehouse\Barang::allBarang', ['filter' => 'Auth']);
$routes->get('/barang/id/(:segment)', 'Warehouse\Barang::getByIdBarang/$1', ['filter' => 'Auth']);
$routes->post('/barang/save', 'Warehouse\Barang::saveBarang', ['filter' => 'Auth']);
$routes->post('/barang/update', 'Warehouse\Barang::updateBarang', ['filter' => 'Auth']);
$routes->post('/barang/update-status', 'Warehouse\Barang::updateStatusBarang', ['filter' => 'Auth']);
$routes->post('/barang/delete', 'Warehouse\Barang::deleteBarang', ['filter' => 'Auth']);

// PENERIMAAN BARANG LOKAL
$routes->get('/penerimaan-barang-lokal', 'Warehouse\PenerimaanBarangLokal::penerimaanBarangLokal', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal/all', 'Warehouse\PenerimaanBarangLokal::allPenerimaanBarangLokal', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal/create', 'Warehouse\PenerimaanBarangLokal::createPenerimaanBarangLokal', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-lokal/save', 'Warehouse\PenerimaanBarangLokal::savePenerimaanBarangLokal', ['filter' => 'Auth']);

// PENERIMAAN BARANG IMPORT
$routes->get('/penerimaan-barang-import', 'Warehouse\PenerimaanBarangImport::penerimaanBarangImport', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import/all', 'Warehouse\PenerimaanBarangImport::allPenerimaanBarangImport', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import/create', 'Warehouse\PenerimaanBarangImport::createPenerimaanBarangImport', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-import/save', 'Warehouse\PenerimaanBarangImport::savePenerimaanBarangImport', ['filter' => 'Auth']);

// HUMAN RESOURCE
// Attendance
$routes->get('/attendance', 'HR\Attendance::attendance', ['filter' => 'Auth']);
$routes->get('/list-attendance', 'HR\Attendance::ListAttendance', ['filter' => 'Auth']);
$routes->post('/save-attendance', 'HR\Attendance::SaveAttendance', ['filter' => 'Auth']);

// payroll
$routes->get('/payroll', 'HR\Payroll::payroll', ['filter' => 'Auth']);
// $routes->get('/employee/all', 'Master\Employee::allEmployee', ['filter' => 'Auth']);


// SETTINGS
// USER
$routes->get('/user', 'Setting\User::user', ['filter' => 'Auth']);
$routes->get('/user/all', 'Setting\User::allUser', ['filter' => 'Auth']);
$routes->get('/user/id/(:segment)', 'Setting\User::getByIdUser/$1', ['filter' => 'Auth']);
$routes->post('/user/save', 'Setting\User::saveUser', ['filter' => 'Auth']);
$routes->post('/user/update', 'Setting\User::updateUser', ['filter' => 'Auth']);
$routes->post('/user/delete', 'Setting\User::deleteUser', ['filter' => 'Auth']);

// ROLE
$routes->get('/role', 'Setting\Role::role', ['filter' => 'Auth']);
$routes->get('/role/all', 'Setting\Role::allRole', ['filter' => 'Auth']);
$routes->get('/role/id/(:segment)', 'Setting\Role::getByIdRole/$1', ['filter' => 'Auth']);
$routes->post('/role/save', 'Setting\Role::saveRole', ['filter' => 'Auth']);
$routes->post('/role/update', 'Setting\Role::updateRole', ['filter' => 'Auth']);
$routes->post('/role/delete', 'Setting\Role::deleteRole', ['filter' => 'Auth']);

// AKSES
$routes->get('/akses', 'Setting\Akses::akses', ['filter' => 'Auth']);
$routes->get('/akses/id', 'Setting\Akses::getAkses', ['filter' => 'Auth']);
$routes->post('/akses/save', 'Setting\Akses::saveAkses', ['filter' => 'Auth']);

// COMPANY ACCESS
$routes->get('/company-access', 'Setting\CompanyAccess::companyAccess', ['filter' => 'Auth']);
$routes->get('/company-access/all', 'Setting\CompanyAccess::allCompanyAccess', ['filter' => 'Auth']);


//api
$routes->get('/get-employee-by-company/(:segment)', 'HR\Attendance::get_employee_by_company/$1', ['filter' => 'Auth']);

//api
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
