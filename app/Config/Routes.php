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

// 403 ROUTE
$routes->get('/403', function () {
    return view('errors/html/error_403');
});

// DASHBOARD
$routes->get('/dashboard', 'Dashboard\Dashboard::dashboard', ['filter' => 'Auth']);

// MASTER DATA
// EMPLOYEE
$routes->get('/employee', 'Master\Employee::employee', ['filter' => 'Auth']);
$routes->get('/employee/all', 'Master\Employee::all', ['filter' => 'Auth']);
$routes->get('/employee/id/(:segment)', 'Master\Employee::updateView/$1', ['filter' => 'Auth']);
$routes->post('/employee/save', 'Master\Employee::saveEmployee', ['filter' => 'Auth']);
$routes->post('/employee/update', 'Master\Employee::updateEmployee', ['filter' => 'Auth']);
$routes->post('/employee/delete', 'Master\Employee::deleteEmployee', ['filter' => 'Auth']);
$routes->post('/employee/getKomponenGaji', 'Master\Employee::getKomponenGaji', ['filter' => 'Auth']);
$routes->post('/employee/get-bagian', 'Master\Bagian::getBagianByDivision', ['filter' => 'Auth']);

$routes->get('/employee/create', 'Master\Employee::createView', ['filter' => 'Auth']);

// CUSTOMER
$routes->get('/customer', 'Master\Customer::customer', ['filter' => 'Auth']);
$routes->get('/customer/all', 'Master\Customer::allCustomer', ['filter' => 'Auth']);
$routes->get('/customer/id/(:segment)', 'Master\Customer::getByIdCustomer/$1', ['filter' => 'Auth']);
$routes->post('/customer/save', 'Master\Customer::saveCustomer', ['filter' => 'Auth']);
$routes->post('/customer/update', 'Master\Customer::updateCustomer', ['filter' => 'Auth']);
$routes->post('/customer/delete', 'Master\Customer::deleteCustomer', ['filter' => 'Auth']);
$routes->get('/customer/getLocalInvoiceList/(:num)', 'Master\Customer::getLocalInvoiceList/$1', ['filter' => 'Auth']);

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

// BAGIAN
$routes->get('/divisi/bagian/(:segment)', 'Master\Bagian::index/$1', ['filter' => 'Auth']);
$routes->get('/divisi/bagian/data/all', 'Master\Bagian::all', ['filter' => 'Auth']);
$routes->post('/divisi/bagian/save', 'Master\Bagian::create', ['filter' => 'Auth']);
$routes->get('/divisi/bagian/id/(:segment)', 'Master\Bagian::get/$1', ['filter' => 'Auth']);
$routes->post('/divisi/bagian/update/(:segment)', 'Master\Bagian::update/$1', ['filter' => 'Auth']);
$routes->post('/divisi/bagian/delete/(:segment)', 'Master\Bagian::delete/$1', ['filter' => 'Auth']);
$routes->post('/divisi/bagian/generate-new-kode', 'Master\Bagian::generateKode', ['filter' => 'Auth']);

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
$routes->get('/hs-code/id/(:segment)', 'Master\HSCode::getByIdHSCode/$1', ['filter' => 'Auth']);
$routes->post('/hs-code/save', 'Master\HSCode::saveHSCode', ['filter' => 'Auth']);
$routes->post('/hs-code/update', 'Master\HSCode::updateHSCode', ['filter' => 'Auth']);
$routes->post('/hs-code/delete', 'Master\HSCode::deleteHSCode', ['filter' => 'Auth']);

// SATUAN
$routes->get('/satuan', 'Master\Satuan::satuan', ['filter' => 'Auth']);
$routes->get('/satuan/all', 'Master\Satuan::allSatuan', ['filter' => 'Auth']);
$routes->get('/satuan/id/(:segment)', 'Master\Satuan::getByIdSatuan/$1', ['filter' => 'Auth']);
$routes->post('/satuan/save', 'Master\Satuan::saveSatuan', ['filter' => 'Auth']);
$routes->post('/satuan/update', 'Master\Satuan::updateSatuan', ['filter' => 'Auth']);
$routes->post('/satuan/delete', 'Master\Satuan::deleteSatuan', ['filter' => 'Auth']);

// SHIFT
$routes->get('/shift', 'Master\Shift::shift', ['filter' => 'Auth']);
$routes->get('/shift/create', 'Master\Shift::createView', ['filter' => 'Auth']);
$routes->get('/shift/all', 'Master\Shift::allshift', ['filter' => 'Auth']);
$routes->get('/shift/id/(:segment)', 'Master\Shift::getById/$1', ['filter' => 'Auth']);
$routes->post('/shift/save', 'Master\Shift::save', ['filter' => 'Auth']);
$routes->post('/shift/update', 'Master\Shift::update', ['filter' => 'Auth']);
// $routes->post('/shift/delete', 'Master\Shift::delete', ['filter' => 'Auth']);

// KURS
$routes->get('/kurs', 'Master\Kurs::index', ['filter' => 'Auth']);
$routes->get('/kurs/all', 'Master\Kurs::all', ['filter' => 'Auth']);
$routes->get('/kurs/id/(:segment)', 'Master\Kurs::getById/$1', ['filter' => 'Auth']);
$routes->post('/kurs/save', 'Master\Kurs::save', ['filter' => 'Auth']);
$routes->post('/kurs/update', 'Master\Kurs::update', ['filter' => 'Auth']);
$routes->post('/kurs/delete', 'Master\Kurs::delete', ['filter' => 'Auth']);

// SUPPLIER HARGA
// $routes->get('/supplier-harga/ajax', 'Supplier\SupplierHarga::supplierHargaAjax', ['filter' => 'Auth']);
$routes->get('/supplier-harga/all', 'Supplier\SupplierHarga::supplierHargaAll', ['filter' => 'Auth']);
// $routes->get('/supplier-harga/supplier/(:num)', 'Supplier\SupplierHarga::getByIdSupplier/$1', ['filter' => 'Auth']);
// $routes->get('/supplier-harga/barang-and-supplier', 'Supplier\SupplierHarga::getByBarangandSupplierId', ['filter' => 'Auth']);
// $routes->post('/supplier-harga/save', 'Supplier\SupplierHarga::saveSupplierHarga', ['filter' => 'Auth']);
// $routes->get('/supplier-harga/id/(:num)', 'Supplier\SupplierHarga::getByIdSupplierHarga/$1', ['filter' => 'Auth']);

// BAGIAN
$routes->get('/bagian/dropdown', 'Master\Bagian::getAllBagian', ['filter' => 'Auth']);

// SUPPLIER
$routes->get('/supplier/ajax', 'Supplier\Supplier::supplierAjax', ['filter' => 'Auth']);
$routes->get('/supplier/id/(:segment)', 'Supplier\Supplier::getByIdSupplier/$1', ['filter' => 'Auth']);
$routes->get('/supplier/generate/(:segment)', 'Supplier\Supplier::supplierGenerate/$1', ['filter' => 'Auth']);
$routes->post('/supplier/delete', 'Supplier\Supplier::deleteSupplier', ['filter' => 'Auth']);

// BAHAN BAKU LOKAL
$routes->get('/supplier-bahan-baku', 'Supplier\Supplier::supplierBahanBaku', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-baku/harga/(:segment)', 'Supplier\Supplier::getSupplierBahanBakuHarga/$1', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-baku/all', 'Supplier\Supplier::allSupplierBahanBaku', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-baku/save', 'Supplier\Supplier::saveSupplierBahanBaku', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-baku/update', 'Supplier\Supplier::updateSupplierBahanBaku', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-baku/print/(:segment)', 'Supplier\Supplier::printSupplierBahanBaku/$1', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-baku/spesifikasi-barang', 'Supplier\SupplierHarga::getListSpesifikasiBarang', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-baku/all-harga', 'Supplier\SupplierHarga::supplierHargaAll', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-baku/harga/save', 'Supplier\SupplierHarga::saveSupplierHarga', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-baku/harga/update', 'Supplier\SupplierHarga::updateSupplierHarga', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-baku/harga/delete', 'Supplier\SupplierHarga::deleteSupplierHarga', ['filter' => 'Auth']);


// BAHAN PENOLONG
$routes->get('/supplier-bahan-penolong', 'Supplier\Supplier::supplierBahanPenolong', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-penolong/all', 'Supplier\Supplier::allSupplierBahanPenolong', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-penolong/save', 'Supplier\Supplier::saveSupplierBahanPenolong', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-penolong/update', 'Supplier\Supplier::updateSupplierBahanPenolong', ['filter' => 'Auth']);

$routes->get('/supplier-internasional', 'Supplier\Supplier::supplierInternasional', ['filter' => 'Auth']);
$routes->get('/supplier-internasional/all', 'Supplier\Supplier::allSupplierInternasional', ['filter' => 'Auth']);
$routes->post('/supplier-internasional/save', 'Supplier\Supplier::saveSupplierInternasional', ['filter' => 'Auth']);
$routes->post('/supplier-internasional/update', 'Supplier\Supplier::updateSupplierInternasional', ['filter' => 'Auth']);

// PURCHASE
// SPP
$routes->get('/spp', 'Purchase\SPP::spp', ['filter' => 'Auth']);
$routes->get('/spp/all', 'Purchase\SPP::allSPP', ['filter' => 'Auth']);
$routes->get('/spp/id/(:segment)', 'Purchase\SPP::getByIdSPP/$1', ['filter' => 'Auth']);
$routes->get('/spp/generate', 'Purchase\SPP::generateSPP', ['filter' => 'Auth']);
$routes->get('/spp/create', 'Purchase\SPP::createSPP', ['filter' => 'Auth']);
$routes->post('/spp/save', 'Purchase\SPP::saveSPP', ['filter' => 'Auth']);
$routes->post('/spp/update', 'Purchase\SPP::updateSPP', ['filter' => 'Auth']);
$routes->post('/spp/update-status', 'Purchase\SPP::updateStatusSPP', ['filter' => 'Auth']);
$routes->post('/spp/approve', 'Purchase\SPP::approveSPP', ['filter' => 'Auth']);
$routes->post('/spp/delete', 'Purchase\SPP::deleteSPP', ['filter' => 'Auth']);
$routes->get('/spp/print-table', 'Purchase\SPP::printTable', ['filter' => 'Auth']);
$routes->get('/spp/print/(:segment)', 'Purchase\SPP::print/$1', ['filter' => 'Auth']);


// BAHAN BAKU PO LOKAL
$routes->get('/po-lokal-bahan-baku', 'Purchase\POLokalBahanBaku::poLokalBahanBaku', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-baku/all', 'Purchase\POLokalBahanBaku::allPOLokalBahanBaku', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-baku/id/(:segment)', 'Purchase\POLokalBahanBaku::getByIdPOLokalBahanBaku/$1', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-baku/create', 'Purchase\POLokalBahanBaku::createPOLokalBahanBaku', ['filter' => 'Auth']);
$routes->post('/po-lokal-bahan-baku/save', 'Purchase\POLokalBahanBaku::savePOLokalBahanBaku', ['filter' => 'Auth']);
$routes->post('/po-lokal-bahan-baku/update', 'Purchase\POLokalBahanBaku::updatePOLokalBahanBaku', ['filter' => 'Auth']);
$routes->post('/po-lokal-bahan-baku/update-status', 'Purchase\POLokalBahanBaku::updateStatusPOLokalBahanBaku', ['filter' => 'Auth']);
$routes->post('/po-lokal-bahan-baku/close-po', 'Purchase\POLokalBahanBaku::closePOLokalBahanBaku', ['filter' => 'Auth']);
$routes->post('/po-lokal-bahan-baku/delete', 'Purchase\POLokalBahanBaku::deletePOLokalBahanBaku', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-baku/print/(:segment)', 'Purchase\POLokalBahanBaku::print/$1', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-baku/dropdown/warehouse', 'Purchase\POLokalBahanBaku::dropdownWarehouse', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-baku/histori-lpb', 'Purchase\POLokalBahanBaku::dropdownHistoriPenerimaanBarang', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-baku/dropdown/get-spp', 'Purchase\POLokalBahanBaku::dropdownGetSpp', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-baku/dropdown/get-detail-barang-spp', 'Purchase\POLokalBahanBaku::dropdownGetSppDetail', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-baku/get-spesifikasi-barang-supplier', 'Purchase\POLokalBahanBaku::getBarangAndSupplier', ['filter' => 'Auth']);

// BAHAN BAKU PO PENOLONG
$routes->get('/po-lokal-bahan-penolong', 'Purchase\POLokalBahanPenolong::poLokalBahanPenolong', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-penolong/all', 'Purchase\POLokalBahanPenolong::allPOLokalBahanPenolong', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-penolong/id/(:segment)', 'Purchase\POLokalBahanPenolong::getByIdPOLokalBahanPenolong/$1', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-penolong/create', 'Purchase\POLokalBahanPenolong::createPOLokalBahanPenolong', ['filter' => 'Auth']);
$routes->post('/po-lokal-bahan-penolong/save', 'Purchase\POLokalBahanPenolong::savePOLokalBahanPenolong', ['filter' => 'Auth']);
$routes->post('/po-lokal-bahan-penolong/update', 'Purchase\POLokalBahanPenolong::updatePOLokalBahanPenolong', ['filter' => 'Auth']);
$routes->post('/po-lokal-bahan-penolong/update-status', 'Purchase\POLokalBahanPenolong::updateStatusPOLokalBahanPenolong', ['filter' => 'Auth']);
$routes->post('/po-lokal-bahan-penolong/close-po', 'Purchase\POLokalBahanPenolong::closePOLokalBahanPenolong', ['filter' => 'Auth']);
$routes->post('/po-lokal-bahan-penolong/delete', 'Purchase\POLokalBahanPenolong::deletePOLokalBahanPenolong', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-penolong/print/(:segment)', 'Purchase\POLokalBahanPenolong::print/$1', ['filter' => 'Auth']);
$routes->post('/po-lokal-bahan-penolong/find-divisi', 'Purchase\POLokalBahanPenolong::getDivisionByCompany', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-penolong/histori-lpb', 'Purchase\POLokalBahanPenolong::dropdownHistoriPenerimaanBarang', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-penolong/histori-harga', 'Purchase\POLokalBahanPenolong::getHistoriHarga', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-penolong/dropdown/get-spp', 'Purchase\POLokalBahanBaku::dropdownGetSpp', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-penolong/dropdown/get-detail-barang-spp', 'Purchase\POLokalBahanPenolong::dropdownGetSppDetail', ['filter' => 'Auth']);

// BAHAN BAKU PO IMPORT
$routes->get('/po-import-bahan-baku', 'Purchase\POImportBahanBaku::poImportBahanBaku', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-baku/all', 'Purchase\POImportBahanBaku::allPOImportBahanBaku', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-baku/id/(:segment)', 'Purchase\POImportBahanBaku::getByIdPOImportBahanBaku/$1', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-baku/print/(:segment)', 'Purchase\POImportBahanBaku::print/$1', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-baku/create', 'Purchase\POImportBahanBaku::createPOImportBahanBaku', ['filter' => 'Auth']);
$routes->post('/po-import-bahan-baku/save', 'Purchase\POImportBahanBaku::savePOImportBahanBaku', ['filter' => 'Auth']);
$routes->post('/po-import-bahan-baku/update', 'Purchase\POImportBahanBaku::updatePOImportBahanBaku', ['filter' => 'Auth']);
$routes->post('/po-import-bahan-baku/update-status', 'Purchase\POImportBahanBaku::updateStatusPOImportBahanBaku', ['filter' => 'Auth']);
$routes->post('/po-import-bahan-baku/close-po', 'Purchase\POImportBahanBaku::closePOImportBahanBaku', ['filter' => 'Auth']);
$routes->post('/po-import-bahan-baku/delete', 'Purchase\POImportBahanBaku::deletePOImportBahanBaku', ['filter' => 'Auth']);
$routes->post('/po-import-bahan-baku/find-divisi', 'Purchase\POLokalBahanPenolong::getDivisionByCompany', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-baku/histori-lpb', 'Purchase\POImportBahanBaku::dropdownHistoriPenerimaanBarang', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-baku/dropdown/get-spp', 'Purchase\POLokalBahanBaku::dropdownGetSpp', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-baku/dropdown/get-detail-barang-spp', 'Purchase\POImportBahanBaku::dropdownGetSppDetail', ['filter' => 'Auth']);

// BAHAN BAKU PO PENOLONG
$routes->get('/po-import-bahan-penolong', 'Purchase\POImportBahanPenolong::poImportBahanPenolong', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-penolong/all', 'Purchase\POImportBahanPenolong::allPOImportBahanPenolong', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-penolong/id/(:segment)', 'Purchase\POImportBahanPenolong::getByIdPOImportBahanPenolong/$1', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-penolong/print/(:segment)', 'Purchase\POImportBahanPenolong::print/$1', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-penolong/create', 'Purchase\POImportBahanPenolong::createPOImportBahanPenolong', ['filter' => 'Auth']);
$routes->post('/po-import-bahan-penolong/save', 'Purchase\POImportBahanPenolong::savePOImportBahanPenolong', ['filter' => 'Auth']);
$routes->post('/po-import-bahan-penolong/update', 'Purchase\POImportBahanPenolong::updatePOImportBahanPenolong', ['filter' => 'Auth']);
$routes->post('/po-import-bahan-penolong/update-status', 'Purchase\POImportBahanPenolong::updateStatusPOImportBahanPenolong', ['filter' => 'Auth']);
$routes->post('/po-import-bahan-penolong/close-po', 'Purchase\POImportBahanPenolong::closePOImportBahanPenolong', ['filter' => 'Auth']);
$routes->post('/po-import-bahan-penolong/delete', 'Purchase\POImportBahanPenolong::deletePOImportBahanPenolong', ['filter' => 'Auth']);
$routes->post('/po-import-bahan-penolong/find-divisi', 'Purchase\POLokalBahanPenolong::getDivisionByCompany', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-penolong/histori-lpb', 'Purchase\POImportBahanPenolong::dropdownHistoriPenerimaanBarang', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-penolong/dropdown/get-spp', 'Purchase\POLokalBahanBaku::dropdownGetSpp', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-penolong/dropdown/get-detail-barang-spp', 'Purchase\POImportBahanBaku::dropdownGetSppDetail', ['filter' => 'Auth']);

// TANDA TERIMA FAKTUR LOKAL BB
$routes->get('/tanda-terima-faktur-lokal-bp', 'Purchase\TandaTerimaSupBB::index', ['filter' => 'Auth']);
$routes->get('/tanda-terima-faktur-lokal-bp/all', 'Purchase\TandaTerimaSupBB::all', ['filter' => 'Auth']);
$routes->get('/tanda-terima-faktur-lokal-bp/create', 'Purchase\TandaTerimaSupBB::create', ['filter' => 'Auth']);
$routes->get('/tanda-terima-faktur-lokal-bp/id/(:num)', 'Purchase\TandaTerimaSupBB::update/$1', ['filter' => 'Auth']);
$routes->get('/tanda-terima-faktur-lokal-bp/print/(:num)', 'Purchase\TandaTerimaSupBB::print/$1', ['filter' => 'Auth']);
$routes->get('/tanda-terima-faktur-lokal-bp/generate-tanda-terima-no', 'Purchase\TandaTerimaSupBB::generateTandaTerimaFakturNumber', ['filter' => 'Auth']);
$routes->get('/tanda-terima-faktur-lokal-bp/daftar-penerimaan-barang', 'Purchase\TandaTerimaSupBB::listPenerimaanBarang', ['filter' => 'Auth']);
$routes->post('/tanda-terima-faktur-lokal-bp/create', 'Purchase\TandaTerimaSupBB::createAction', ['filter' => 'Auth']);
$routes->post('/tanda-terima-faktur-lokal-bp/update', 'Purchase\TandaTerimaSupBB::updateAction', ['filter' => 'Auth']);
$routes->post('/tanda-terima-faktur-lokal-bp/delete', 'Purchase\TandaTerimaSupBB::delete', ['filter' => 'Auth']);
$routes->post('/tanda-terima-faktur-lokal-bp/delete/daftar-penerimaan', 'Purchase\TandaTerimaSupBB::deleteDetailTandaTerimaFaktur', ['filter' => 'Auth']);


// // TERIMA FAKTUR LOKAL
// $routes->get('/terima-faktur-import/getBySupplier/(:num)', 'Purchase\TerimaFakturImport::getBySupplierId/$1', ['filter' => 'Auth']);
// $routes->get('/terima-faktur-import', 'Purchase\TerimaFakturImport::terimafakturImport', ['filter' => 'Auth']);
// $routes->get('/terima-faktur-import/all', 'Purchase\TerimaFakturImport::allTerimafakturImport', ['filter' => 'Auth']);
// $routes->get('/terima-faktur-import/(:num)', 'Purchase\TerimaFakturImport::getByIdTerimafakturImport/$1', ['filter' => 'Auth']);
// $routes->get('/terima-faktur-import/create', 'Purchase\TerimaFakturImport::createTerimaFakturImportView', ['filter' => 'Auth']);
// $routes->post('/terima-faktur-import/create', 'Purchase\TerimaFakturImport::saveTerimafakturImport', ['filter' => 'Auth']);
// $routes->post('/terima-faktur-import/update', 'Purchase\TerimaFakturImport::updateTerimaFakturImport', ['filter' => 'Auth']);
// $routes->post('/terima-faktur-import/delete', 'Purchase\TerimaFakturImport::deleteTerimaFakturImport', ['filter' => 'Auth']);
// $routes->get('/terima-faktur-import/print/(:num)', 'Purchase\TerimaFakturImport::print/$1', ['filter' => 'Auth']);

// // TERIMA FAKTUR IMPORT
// $routes->get('/terima-faktur-lokal', 'Purchase\TerimaFakturLokal::terimaFakturLokal', ['filter' => 'Auth']);
// $routes->get('/terima-faktur-lokal/all', 'Purchase\TerimaFakturLokal::allTerimaFakturLokal', ['filter' => 'Auth']);
// $routes->get('/terima-faktur-lokal/(:num)', 'Purchase\TerimaFakturLokal::getByIdTerimaFakturLokal/$1', ['filter' => 'Auth']);
// $routes->get('/terima-faktur-lokal/create', 'Purchase\TerimaFakturLokal::createTerimaFakturLokal', ['filter' => 'Auth']);
// $routes->post('/terima-faktur-lokal/save', 'Purchase\TerimaFakturLokal::saveTerimaFakturLokal', ['filter' => 'Auth']);
// $routes->post('/terima-faktur-lokal/update', 'Purchase\TerimaFakturLokal::updateTerimaFakturLokal', ['filter' => 'Auth']);
// $routes->post('/terima-faktur-lokal/delete', 'Purchase\TerimaFakturLokal::deleteTerimaFakturLokal', ['filter' => 'Auth']);

// REKAP FAKTUR
$routes->get('/rekap-faktur', 'Purchase\RekapFaktur', ['filter' => 'Auth']);
$routes->get('/rekap-faktur/all', 'Purchase\RekapFaktur::getRekapFakturList', ['filter' => 'Auth']);
$routes->get('/rekap-faktur/supplier/(:num)', 'Purchase\RekapFaktur::getRekapFakturBySupplier/$1', ['filter' => 'Auth']);
$routes->get('/rekap-faktur/(:num)', 'Purchase\RekapFaktur::getRekapFakturById/$1', ['filter' => 'Auth']);
$routes->get('/rekap-faktur/getItemList/(:num)', 'Purchase\RekapFaktur::getInvItemsBySummaryId/$1', ['filter' => 'Auth']);
$routes->get('/rekap-faktur/create', 'Purchase\RekapFaktur::createRekapFaktur', ['filter' => 'Auth']);
$routes->post('/rekap-faktur/create', 'Purchase\RekapFaktur::saveRekapFaktur', ['filter' => 'Auth']);
$routes->post('/rekap-faktur/update-status', 'Purchase\RekapFaktur::updateStatusRekap', ['filter' => 'Auth']);
$routes->post('/rekap-faktur/update', 'Purchase\RekapFaktur::updateRekap', ['filter' => 'Auth']);
$routes->post('/rekap-faktur/delete', 'Purchase\RekapFaktur::deleteRekap', ['filter' => 'Auth']);

// PEMBAYARAN
// PEMBAYARAN PO LOKAL BP
$routes->get('/pembayaran-po-lokal-bp', 'Pembayaran\PembayaranPOLokal::pembayaranPOLokalBP', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal-bp/create', 'Pembayaran\PembayaranPOLokal::createPembayaranPOLokalBP', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal-bp/get-rekap-faktur/(:segment)', 'Pembayaran\PembayaranPOLokal::getTandaTerimaFaktur/$1', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal-bp/get-item-list/(:segment)', 'Pembayaran\PembayaranPOLokal::getItemListByTandaTerimaFaktur/$1', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal-bp/generate-no-pembayaran', 'Pembayaran\PembayaranPOLokal::generatePaymentNo', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal-bp/create', 'Pembayaran\PembayaranPOLokal::createPembayaranPOLokalBPAction', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal-bp/id/(:segment)', 'Pembayaran\PembayaranPOLokal::getPembayaranPOLokalBP/$1', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal-bp/print/(:segment)', 'Pembayaran\PembayaranPOLokal::pembayaranPOLokalBPPrint/$1', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal/all', 'Pembayaran\PembayaranPOLokal::allPembayaranPOLokal', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal/delete', 'Pembayaran\PembayaranPOLokal::delete', ['filter' => 'Auth']);
// PEMBAYARAN PO LOKAL BB
$routes->get('/pembayaran-po-lokal-bb', 'Pembayaran\PembayaranPOLokal::pembayaranPOLokalBB', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal-bb/create', 'Pembayaran\PembayaranPOLokal::createPembayaranPOLokalBB', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal-bb/get-lpb-not-paid', 'Pembayaran\PembayaranPOLokal::getListDokumenLPBNotPaidBB', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal-bb/get-list-po-no-paid', 'Pembayaran\PembayaranPOLokal::getListBarangLPBNotPaidBB', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal-bb/create', 'Pembayaran\PembayaranPOLokal::createPembayaranPOLokalBBAction', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal-bb/id/(:segment)', 'Pembayaran\PembayaranPOLokal::getPembayaranPOLokalBB/$1', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal-bb/print/(:segment)', 'Pembayaran\PembayaranPOLokal::pembayaranPOLokalBBPrint/$1', ['filter' => 'Auth']);

// PEMBAYARAN PO LOKAL
$routes->get('/pembayaran-po-lokal', 'Pembayaran\PembayaranPOLokal::pembayaranPOLokal', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal/(:num)', 'Pembayaran\PembayaranPOLokal::getByIdPembayaranPOLokal/$1', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal/create', 'Pembayaran\PembayaranPOLokal::createPembayaranPOLokal', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal/create', 'Pembayaran\PembayaranPOLokal::savePembayaranPOLokal', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal/update', 'Pembayaran\PembayaranPOLokal::updatePembayaranPOLokal', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal/delete', 'Pembayaran\PembayaranPOLokal::deletePembayaranPOLokal', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal/print/(:num)', 'Pembayaran\PembayaranPOLokal::print/$1', ['filter' => 'Auth']);

// PEMBAYARAN PO IMPORT
$routes->get('/pembayaran-po-import', 'Pembayaran\PembayaranPOImport::pembayaranPOImport', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-import/all', 'Pembayaran\PembayaranPOImport::allPembayaranPOImport', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-import/id/(:segment)', 'Pembayaran\PembayaranPOImport::updatePembayaranPOImportView/$1', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-import/create', 'Pembayaran\PembayaranPOImport::createPembayaranPOImportView', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-import/create', 'Pembayaran\PembayaranPOImport::savePembayaranPOImport', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-import/update', 'Pembayaran\PembayaranPOImport::updatePembayaranPOImport', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-import/delete', 'Pembayaran\PembayaranPOImport::deletePembayaranPOImport', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-import/generate-no-pembayaran', 'Pembayaran\PembayaranPOImport::getNomorPembayaran', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-import/po-belum-lunas', 'Pembayaran\PembayaranPOImport::listPembayaranPOBelumLunas', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-import/all-po', 'Pembayaran\PembayaranPOImport::allPO', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-import/all-riwayat-pembayaran', 'Pembayaran\PembayaranPOImport::allRiwayatPembayaran', ['filter' => 'Auth']);

// penerimaan pembayaran SO
$routes->get('/penerimaan-penjualan-lokal', 'Penerimaan\SalesOrderPayment::index', ['filter' => 'Auth']);
$routes->get('/penerimaan-penjualan-lokal/create', 'Penerimaan\SalesOrderPayment::create', ['filter' => 'Auth']);

// SALES LOKAL
// Order Form Lokal
$routes->get('/order-form-lokal', 'SalesLokal\OrderForm::index', ['filter' => 'Auth']);
$routes->get('/order-form-lokal/barangAll', 'SalesLokal\OrderForm::getAllBarang', ['filter' => 'Auth']);
$routes->get('/order-form-lokal/warehouseAll/(:segment)', 'SalesLokal\OrderForm::getAllWarehouse/$1', ['filter' => 'Auth']);
$routes->get('/order-form-lokal/stok/(:segment)/(:segment)', 'SalesLokal\OrderForm::getStockDetail/$1/$2', ['filter' => 'Auth']);
$routes->get('/order-form-lokal/getByCustomer/(:num)', 'SalesLokal\OrderForm::getByCustomerId/$1', ['filter' => 'Auth']);
$routes->get('/order-form-lokal/id/(:segment)', 'SalesLokal\OrderForm::getById/$1', ['filter' => 'Auth']);
$routes->get('/order-form-lokal/getmetaData/(:segment)', 'SalesLokal\OrderForm::getMetaData/$1', ['filter' => 'Auth']);
$routes->get('/order-form-lokal/create', 'SalesLokal\OrderForm::createView', ['filter' => 'Auth']);
$routes->get('/order-form-lokal/all', 'SalesLokal\OrderForm::all', ['filter' => 'Auth']);
$routes->post('/order-form-lokal/save', 'SalesLokal\OrderForm::save', ['filter' => 'Auth']);
$routes->post('/order-form-lokal/update', 'SalesLokal\OrderForm::update', ['filter' => 'Auth']);
$routes->post('/order-form-lokal/delete', 'SalesLokal\OrderForm::delete', ['filter' => 'Auth']);
$routes->get('/order-form-lokal/print/(:num)', 'SalesLokal\OrderForm::printOrder/$1', ['filter' => 'Auth']);
$routes->get('/order-form-lokal/getItemList', 'SalesLokal\OrderForm::getItemListByIds', ['filter' => 'Auth']);
$routes->get('/order-form-lokal/getItemList/(:num)', 'SalesLokal\OrderForm::getItemListById/$1', ['filter' => 'Auth']);

$routes->get('/order-form-lokal/barangAll', 'SalesLokal\OrderForm::getAllBarang', ['filter' => 'Auth']);
$routes->post('/order-form-lokal/generate-no-order-form', 'SalesLokal\OrderForm::generateNomorSalesOrder', ['filter' => 'Auth']);

// Invoice Lokal
$routes->get('/invoice-penjualan-lokal', 'SalesLokal\Invoice::index', ['filter' => 'Auth']);
$routes->get('/invoice-penjualan-lokal/id/(:segment)', 'SalesLokal\Invoice::getById/$1', ['filter' => 'Auth']);
$routes->get('/invoice-penjualan-lokal/create', 'SalesLokal\Invoice::createView', ['filter' => 'Auth']);
$routes->get('/invoice-penjualan-lokal/all', 'SalesLokal\Invoice::all', ['filter' => 'Auth']);
$routes->post('/invoice-penjualan-lokal/save', 'SalesLokal\Invoice::save', ['filter' => 'Auth']);
$routes->post('/invoice-penjualan-lokal/update', 'SalesLokal\Invoice::update', ['filter' => 'Auth']);
$routes->post('/invoice-penjualan-lokal/delete', 'SalesLokal\Invoice::delete', ['filter' => 'Auth']);
$routes->get('/invoice-penjualan-lokal/getDocNumber/(:alpha)', 'SalesLokal\Invoice::getDocNumber/$1', ['filter' => 'Auth']);
$routes->get('/invoice-penjualan-lokal/getDocumentData/(:alpha)/(:num)', 'SalesLokal\Invoice::getDocData/$1/$2', ['filter' => 'Auth']);
$routes->get('/invoice-penjualan-lokal/getItemList/(:num)', 'SalesLokal\Invoice::getItemList/$1', ['filter' => 'Auth']);
$routes->get('/invoice-penjualan-lokal/print/(:num)', 'SalesLokal\Invoice::printInvoice/$1', ['filter' => 'Auth']);

// Surat Jalan
$routes->get('/surat-jalan', 'SalesLokal\SuratJalan::index', ['filter' => 'Auth']);
$routes->get('/surat-jalan/id/(:segment)', 'SalesLokal\SuratJalan::getById/$1', ['filter' => 'Auth']);
$routes->get('/surat-jalan/sales-order/(:segment)', 'SalesLokal\SuratJalan::dropDownSalesOrder/$1', ['filter' => 'Auth']);
$routes->get('/surat-jalan/sales-invoice/(:segment)', 'SalesLokal\SuratJalan::dropDownSuratJalan/$1', ['filter' => 'Auth']);
$routes->get('/surat-jalan/detail/(:segment)', 'SalesLokal\SuratJalan::dataSuratJalanDetail/$1', ['filter' => 'Auth']);
$routes->get('/surat-jalan/create', 'SalesLokal\SuratJalan::createView', ['filter' => 'Auth']);
$routes->get('/surat-jalan/all', 'SalesLokal\SuratJalan::all', ['filter' => 'Auth']);
$routes->post('/surat-jalan/save', 'SalesLokal\SuratJalan::save', ['filter' => 'Auth']);
$routes->post('/surat-jalan/update', 'SalesLokal\SuratJalan::update', ['filter' => 'Auth']);
$routes->post('/surat-jalan/delete', 'SalesLokal\SuratJalan::delete', ['filter' => 'Auth']);
$routes->get('/surat-jalan/print/(:num)', 'SalesLokal\SuratJalan::printSJ/$1', ['filter' => 'Auth']);
$routes->post('/surat-jalan/generate-no-surat-jalan', 'SalesLokal\SuratJalan::generateNomorSuratJalan', ['filter' => 'Auth']);

// Retur Pembelian
$routes->get('/retur-barang', 'Purchase\ReturPembelian::index', ['filter' => 'Auth']);
$routes->get('/retur-barang/create', 'Purchase\ReturPembelian::create', ['filter' => 'Auth']);
$routes->get('/retur-barang/generate-new-no', 'Purchase\ReturPembelian::generateNo', ['filter' => 'Auth']);
$routes->get('/retur-barang/generate-penerimaan-barang', 'Purchase\ReturPembelian::getPenerimaanBarangList', ['filter' => 'Auth']);

$routes->get('/retur/(:num)', 'SalesLokal\Retur::getById/$1', ['filter' => 'Auth']);
$routes->get('/retur/all', 'SalesLokal\Retur::all', ['filter' => 'Auth']);
$routes->post('/retur/save', 'SalesLokal\Retur::save', ['filter' => 'Auth']);
$routes->post('/retur/update', 'SalesLokal\Retur::update', ['filter' => 'Auth']);
$routes->post('/retur/delete', 'SalesLokal\Retur::delete', ['filter' => 'Auth']);

// Master Barang Lokal
$routes->get('master-barang-lokal', 'SalesLokal\Barang::bahanJadiView', ['filter' => 'Auth']);
$routes->group('master-barang-lokal', ['filter' => 'Auth'], function ($routes) {
    $routes->get('all', 'SalesLokal\Barang::all');
    $routes->post('get', 'SalesLokal\Barang::get');
    $routes->post('save', 'SalesLokal\Barang::create');
    $routes->post('update', 'SalesLokal\Barang::update');
    $routes->post('delete', 'SalesLokal\Barang::delete');
    $routes->post('delete-spek', 'SalesLokal\Barang::deleteSpek');
    $routes->post('generate-new-code', 'SalesLokal\Barang::generateNewCode');
});

// SALES INTERNASIONAL
// Sales Kontrak
$routes->get('/sales-kontrak', 'SalesInternasional\SalesKontrak::index', ['filter' => 'Auth']);
$routes->get('/sales-kontrak/id/(:segment)', 'SalesInternasional\SalesKontrak::getById/$1', ['filter' => 'Auth']);
$routes->get('/sales-kontrak/print/(:segment)', 'SalesInternasional\SalesKontrak::print/$1', ['filter' => 'Auth']);
$routes->get('/sales-kontrak/create', 'SalesInternasional\SalesKontrak::createView', ['filter' => 'Auth']);
$routes->get('/sales-kontrak/all', 'SalesInternasional\SalesKontrak::all', ['filter' => 'Auth']);
$routes->post('/sales-kontrak/save', 'SalesInternasional\SalesKontrak::save', ['filter' => 'Auth']);
$routes->post('/sales-kontrak/update', 'SalesInternasional\SalesKontrak::update', ['filter' => 'Auth']);
$routes->post('/sales-kontrak/update-status', 'SalesInternasional\SalesKontrak::updateStatus', ['filter' => 'Auth']);
$routes->post('/sales-kontrak/delete', 'SalesInternasional\SalesKontrak::delete', ['filter' => 'Auth']);
$routes->get('/sales-kontrak/barangAll', 'SalesLokal\OrderForm::getAllBarang', ['filter' => 'Auth']);
$routes->get('/sales-kontrak/warehouseAll/(:segment)', 'SalesLokal\OrderForm::getAllWarehouse/$1', ['filter' => 'Auth']);
$routes->get('/sales-kontrak/stok/(:segment)/(:segment)', 'SalesLokal\OrderForm::getStockDetail/$1/$2', ['filter' => 'Auth']);

// Order Form Internasional
$routes->get('/order-form-internasional', 'SalesInternasional\OrderForm::index', ['filter' => 'Auth']);
$routes->get('/order-form-internasional/id/(:segment)', 'SalesInternasional\OrderForm::getById/$1', ['filter' => 'Auth']);
$routes->get('/order-form-internasional/print/(:segment)', 'SalesInternasional\OrderForm::print/$1', ['filter' => 'Auth']);
$routes->get('/order-form-internasional/all', 'SalesInternasional\OrderForm::all', ['filter' => 'Auth']);
$routes->post('/order-form-internasional/update', 'SalesInternasional\OrderForm::update', ['filter' => 'Auth']);
$routes->post('/order-form-internasional/update-status', 'SalesInternasional\OrderForm::updateStatus', ['filter' => 'Auth']);

// Master Barang Internasional
$routes->get('master-barang-internasional', 'SalesInternasional\Barang::bahanJadiView', ['filter' => 'Auth']);
$routes->group('master-barang-internasional', ['filter' => 'Auth'], function ($routes) {
    $routes->get('all', 'SalesInternasional\Barang::all');
    $routes->post('get', 'SalesInternasional\Barang::get');
    $routes->post('save', 'SalesInternasional\Barang::create');
    $routes->post('update', 'SalesInternasional\Barang::update');
    $routes->post('delete', 'SalesInternasional\Barang::delete');
    $routes->post('delete-spek', 'SalesInternasional\Barang::deleteSpek');
    $routes->post('generate-new-code', 'SalesInternasional\Barang::generateNewCode');
});

// Customer Lokal
$routes->get('/customer-lokal', 'SalesLokal\Customer::index', ['filter' => 'Auth']);
$routes->get('/customer-lokal/all', 'SalesLokal\Customer::all', ['filter' => 'Auth']);
$routes->get('/customer-lokal/id/(:segment)', 'Master\Customer::getByIdCustomer/$1', ['filter' => 'Auth']);
$routes->post('/customer-lokal/save', 'Master\Customer::saveCustomer', ['filter' => 'Auth']);
$routes->post('/customer-lokal/update', 'Master\Customer::updateCustomer', ['filter' => 'Auth']);
$routes->post('/customer-lokal/delete', 'Master\Customer::deleteCustomer', ['filter' => 'Auth']);

// Customer Ekspor
$routes->get('/customer-ekspor', 'SalesInternasional\Customer::index', ['filter' => 'Auth']);
$routes->get('/customer-ekspor/all', 'SalesInternasional\Customer::all', ['filter' => 'Auth']);
$routes->get('/customer-ekspor/id/(:segment)', 'Master\Customer::getByIdCustomer/$1', ['filter' => 'Auth']);
$routes->post('/customer-ekspor/save', 'Master\Customer::saveCustomer', ['filter' => 'Auth']);
$routes->post('/customer-ekspor/update', 'Master\Customer::updateCustomer', ['filter' => 'Auth']);
$routes->post('/customer-ekspor/delete', 'Master\Customer::deleteCustomer', ['filter' => 'Auth']);


// PRODUKSI
// Production Result
$routes->get('/production-result', 'Production\ProductionResult::index', ['filter' => 'Auth']);
$routes->get('/production-result/(:num)', 'Production\ProductionResult::getById/$1', ['filter' => 'Auth']);
$routes->get('/production-result/all', 'Production\ProductionResult::getAll', ['filter' => 'Auth']);
$routes->get('/production-result/create', 'Production\ProductionResult::createProductionResult', ['filter' => 'Auth']);
$routes->post('/production-result/create', 'Production\ProductionResult::saveProductionResult', ['filter' => 'Auth']);

// Rencana Produksi
$routes->get('/work-order', 'Production\WorkOrder::index', ['filter' => 'Auth']);
$routes->get('/work-order/id/(:segment)', 'Production\WorkOrder::getById/$1', ['filter' => 'Auth']);
$routes->get('/work-order/create', 'Production\WorkOrder::createView', ['filter' => 'Auth']);
$routes->get('/work-order/all', 'Production\WorkOrder::all', ['filter' => 'Auth']);
$routes->post('/work-order/save', 'Production\WorkOrder::create', ['filter' => 'Auth']);
$routes->post('/work-order/update', 'Production\WorkOrder::update', ['filter' => 'Auth']);

// DROPDOWN

// TERIMA FAKTUR
$routes->get('/penerimaan-barang-lokal/dropdown', 'Warehouse\PenerimaanBarangLokal::dropdownpenerimaanBarangLokal', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal/dropdown/bySupplier/(:num)', 'Warehouse\PenerimaanBarangLokal::getReceivedNoBySupplier/$1', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import/dropdown', 'Warehouse\PenerimaanBarangImport::dropdownpenerimaanBarangImport', ['filter' => 'Auth']);

// SUPPLIER 
$routes->get('/supplier/dropdown', 'Supplier\Supplier::dropdownSupplier', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-baku/dropdown', 'Supplier\SupplierBahanBaku::dropdownSupplier', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-penolong/dropdown', 'Supplier\SupplierBahanPenolong::dropdownSupplier', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-baku-import/dropdown', 'Supplier\SupplierBahanBakuImport::dropdownSupplier', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-penolong-import/dropdown', 'Supplier\SupplierBahanPenolongImport::dropdownSupplier', ['filter' => 'Auth']);



// PO
$routes->get('/po-lokal-bahan-baku/dropdown', 'Purchase\POLokalBahanBaku::dropdownPOLokalBahanBaku', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-penolong/dropdown', 'Purchase\POLokalBahanPenolong::dropdownPOLokalBahanPenolong', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-baku/dropdown', 'Purchase\POImportBahanBaku::dropdownPOImportBahanBaku', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-penolong/dropdown', 'Purchase\POImportBahanPenolong::dropdownPOImportBahanPenolong', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-baku/payment-dropdown/(:num)', 'Purchase\POImportBahanBaku::purchaseOrderPaymentDropdown/$1', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-penolong/payment-dropdown/(:num)', 'Purchase\POImportBahanPenolong::purchaseOrderPaymentDropdown/$1', ['filter' => 'Auth']);

$routes->get('/po-lokal-bahan-baku/multi/dropdown', 'Purchase\POLokalBahanBaku::dropdownBarangPOLokalBahanBaku', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-penolong/multi/dropdown', 'Purchase\POLokalBahanPenolong::dropdownBarangPOLokalBahanPenolong', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-baku/multi/dropdown', 'Purchase\POImportBahanBaku::dropdownBarangPOImportBahanBaku', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-penolong/multi/dropdown', 'Purchase\POImportBahanPenolong::dropdownBarangPOImportBahanPenolong', ['filter' => 'Auth']);

// CITY
$routes->get('/city/(:segment)', 'Master\City::getCityByProvince/$1', ['filter' => 'Auth']);

// EMPLOYEE
$routes->get('/employee/dropdown', 'Master\Employee::dropdownEmployee', ['filter' => 'Auth']);
$routes->get('/employee-pic/dropdown', 'Master\Employee::dropdownEmployeePIC', ['filter' => 'Auth']);
$routes->get('/employee-division/dropdown', 'Master\Employee::dropdownEmployeeByDivision', ['filter' => 'Auth']);

// SHIFT
$routes->get('/shift/dropdown', 'Master\Shift::dropdownShift', ['filter' => 'Auth']);
// METADATA
$routes->get('/metadata/dropdown', 'Master\Metadata::dropdownMetadata', ['filter' => 'Auth']);
$routes->get('/metadata/dropdown1', 'Master\Metadata::dropdownMetadata1', ['filter' => 'Auth']);
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

// JABATAN
$routes->get('/jabatan/dropdown', 'Personalia\Jabatan::dropdownJabatan', ['filter' => 'Auth']);

// TUNJANGAN
$routes->get('/tunjangan/dropdown', 'Master\Tunjangan::dropdownTunjangan', ['filter' => 'Auth']);


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
$routes->get('/barang/dropdown/parent', 'Warehouse\Barang::dropdownParentBarang', ['filter' => 'Auth']);
$routes->get('/barang/dropdown/kategori', 'Warehouse\Barang::dropdownBarangKategori', ['filter' => 'Auth']);
$routes->get('/barang/dropdown/type', 'Warehouse\Barang::dropdownBarangType', ['filter' => 'Auth']);

// ACCOUNT
$routes->get('/kategori-account/dropdown', 'Master\Account::dropdownKategoriAccount', ['filter' => 'Auth']);
$routes->get('/header-account/dropdown', 'Master\Account::dropdownHeaderAccount', ['filter' => 'Auth']);
$routes->get('/sub-account/dropdown', 'Master\Account::dropdownSubAccount', ['filter' => 'Auth']);
$routes->get('/ap-ar/dropdown', 'Master\Account::dropdownAPAR', ['filter' => 'Auth']);

// Master Barang
// Parent Barang
$routes->get('parent-barang', 'Warehouse\ParentBarang::index', ['filter' => 'Auth']);
$routes->post('parent-barang/save', 'Warehouse\ParentBarang::create', ['filter' => 'Auth']);
$routes->post('parent-barang/update', 'Warehouse\ParentBarang::update', ['filter' => 'Auth']);
$routes->post('parent-barang/delete', 'Warehouse\ParentBarang::delete', ['filter' => 'Auth']);
$routes->post('parent-barang/get', 'Warehouse\ParentBarang::get', ['filter' => 'Auth']);
$routes->get('parent-barang/all', 'Warehouse\ParentBarang::all', ['filter' => 'Auth']);
// Master Barang
$routes->get('barang-bahan-baku', 'Warehouse\Barang::bahanBakuView', ['filter' => 'Auth']);
$routes->get('barang-bahan-penolong', 'Warehouse\Barang::bahanPenolongView', ['filter' => 'Auth']);
$routes->get('barang-bahan-jadi', 'Warehouse\Barang::bahanJadiView', ['filter' => 'Auth']);
$routes->get('barang-scrap', 'Warehouse\Barang::bahanScrapView', ['filter' => 'Auth']);
$routes->get('barang-modal', 'Warehouse\Barang::bahanModalView', ['filter' => 'Auth']);
$routes->group('barang-master', ['filter' => 'Auth'], function ($routes) {
    $routes->get('all', 'Warehouse\Barang::all');
    $routes->post('get', 'Warehouse\Barang::get');
    $routes->post('save', 'Warehouse\Barang::create');
    $routes->post('update', 'Warehouse\Barang::update');
    $routes->post('delete', 'Warehouse\Barang::delete');
    $routes->post('delete-spek', 'Warehouse\Barang::deleteSpek');
    $routes->post('generate-new-code', 'Warehouse\Barang::generateNewCode');
});
$routes->get('barang/supplier/(:num)', 'Warehouse\Barang::getBySupplier/$1', ['filter' => 'Auth']);
$routes->get('barang-bahan-penolong/histori', 'Warehouse\Barang::historiHargaPOBahanPenolong', ['filter' => 'Auth']);
// MASTER KEMASAN
$routes->get('/kemasan', 'Warehouse\Kemasan::index', ['filter' => 'Auth']);
$routes->post('/kemasan/save', 'Warehouse\Kemasan::create', ['filter' => 'Auth']);
$routes->post('/kemasan/update', 'Warehouse\Kemasan::update', ['filter' => 'Auth']);
$routes->post('/kemasan/delete', 'Warehouse\Kemasan::delete', ['filter' => 'Auth']);
$routes->post('/kemasan/get', 'Warehouse\Kemasan::get', ['filter' => 'Auth']);
$routes->get('/kemasan/all', 'Warehouse\Kemasan::all', ['filter' => 'Auth']);
$routes->post('/kemasan/generate-new-code', 'Warehouse\Kemasan::generateNewKode', ['filter' => 'Auth']);

// $routes->group('barang-bahan-penolong', ['filter' => 'Auth'], function ($routes) {
//     $routes->get('/', 'Warehouse\Barang::barang/Bahan Penolong');
// });

// $routes->group('barang-bahan-baku', ['filter' => 'Auth'], function ($routes) {
//     $routes->get('/', 'Warehouse\Barang::barang/Bahan Baku');
// });

// $routes->group('barang-bahan-jadi', ['filter' => 'Auth'], function ($routes) {
//     $routes->get('/', 'Warehouse\Barang::barang/Jadi');
// });

// $routes->group('barang-scrap', ['filter' => 'Auth'], function ($routes) {
//     $routes->get('/', 'Warehouse\Barang::barang/Scrap');
// });

// $routes->group('barang', ['filter' => 'Auth'], function ($routes) {
//     $routes->get('all', 'Warehouse\Barang::allBarang');
//     $routes->get('id/(:segment)', 'Warehouse\Barang::getByIdBarang/$1');
//     $routes->post('save', 'Warehouse\Barang::saveBarang');
//     $routes->post('update', 'Warehouse\Barang::updateBarang');
//     $routes->post('delete', 'Warehouse\Barang::deleteBarang');
// });

// WAREHOUSE
// MASTER STOCK
$routes->get('/stock', 'Warehouse\Stock::index', ['filter' => 'Auth']);
$routes->get('/stock/all', 'Warehouse\Stock::allStock', ['filter' => 'Auth']);
$routes->get('/stock/(:num)/(:num)', 'Warehouse\Stock::getStockInfo/$1/$2', ['filter' => 'Auth']);
$routes->post('/stock/save', 'Warehouse\Stock::addNewStock', ['filter' => 'Auth']);
// INVENTORI
// STOCK SAFETY
$routes->get('/stock-safety', 'Inventori\Inventori::stockSafetyView', ['filter' => 'Auth']);
$routes->get('/stock-safety/id/(:num)', 'Inventori\Inventori::stockSafetyGet/$1', ['filter' => 'Auth']);
$routes->post('/stock-safety/update', 'Inventori\Inventori::stockSafetyUpdate', ['filter' => 'Auth']);
$routes->get('/stock-safety/all', 'Inventori\Inventori::stockSafetyAll', ['filter' => 'Auth']);
// STOCK HISTORI
$routes->get('/stock-histori', 'Inventori\Inventori::stockHistoriView', ['filter' => 'Auth']);
$routes->get('/stock-histori/all', 'Inventori\Inventori::stockHistoriAll', ['filter' => 'Auth']);
$routes->get('/stock-list', 'Inventori\Inventori::stockListView', ['filter' => 'Auth']);
$routes->get('/stock-list/all', 'Inventori\Inventori::stockListAll', ['filter' => 'Auth']);

// PENERIMAAN BARANG LOKAL BP
$routes->get('/penerimaan-barang-lokal-bp', 'Warehouse\PenerimaanBarangLokalBP::index', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bp/all', 'Warehouse\PenerimaanBarangLokalBP::all', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bp/create', 'Warehouse\PenerimaanBarangLokalBP::create', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-lokal-bp/insert', 'Warehouse\PenerimaanBarangLokalBP::createAction', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bp/generate-po-no', 'Warehouse\PenerimaanBarangLokalBP::generatePONo', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bp/get-po', 'Purchase\POLokalBahanPenolong::dropdownPOLokalBahanPenolong', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bp/list-barang', 'Warehouse\PenerimaanBarangLokalBP::listBarangLPB', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bp/id/(:segment)', 'Warehouse\PenerimaanBarangLokalBP::update/$1', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-lokal-bp/update', 'Warehouse\PenerimaanBarangLokalBP::updateAction', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bp/print/(:segment)', 'Warehouse\PenerimaanBarangLokalBP::print/$1', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-lokal-bp/delete', 'Warehouse\PenerimaanBarangLokalBP::delete', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-lokal-bp/posting', 'Warehouse\PenerimaanBarangLokalBP::posting', ['filter' => 'Auth']);

// PENERIMAAN BARANG LOKAL BB
$routes->get('/penerimaan-barang-lokal-bb', 'Warehouse\PenerimaanBarangLokalBB::index', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bb/all', 'Warehouse\PenerimaanBarangLokalBB::all', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bb/create', 'Warehouse\PenerimaanBarangLokalBB::create', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bb/generate-po-no', 'Warehouse\PenerimaanBarangLokalBP::generatePONo', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bb/get-po', 'Purchase\POLokalBahanBaku::dropdownPOLokalBahanBaku', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bb/list-barang', 'Warehouse\PenerimaanBarangLokalBB::listBarangLPB', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-lokal-bb/insert', 'Warehouse\PenerimaanBarangLokalBB::createAction', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bb/id/(:segment)', 'Warehouse\PenerimaanBarangLokalBB::update/$1', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-lokal-bb/update', 'Warehouse\PenerimaanBarangLokalBB::updateAction', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bb/print/(:segment)', 'Warehouse\PenerimaanBarangLokalBB::print/$1', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-lokal-bb/delete', 'Warehouse\PenerimaanBarangLokalBB::delete', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-lokal-bb/posting', 'Warehouse\PenerimaanBarangLokalBB::posting', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bb/get-divisi', 'Warehouse\PenerimaanBarangLokalBB::dropdownDivisiPOLokalBB', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bb/warehouse', 'Purchase\POLokalBahanBaku::dropdownWarehouse', ['filter' => 'Auth']);

// PENERIMAAN BARANG IMPORT BB
$routes->get('/penerimaan-barang-import-bb', 'Warehouse\PenerimaanBarangImportBB::index', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bb/all', 'Warehouse\PenerimaanBarangImportBB::all', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bb/create', 'Warehouse\PenerimaanBarangImportBB::create', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bb/generate-po-no', 'Warehouse\PenerimaanBarangLokalBP::generatePONo', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bb/get-po', 'Purchase\POImportBahanBaku::dropdownPOImportBahanBaku', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bb/list-barang', 'Warehouse\PenerimaanBarangImportBB::listBarangLPB', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-import-bb/insert', 'Warehouse\PenerimaanBarangImportBB::createAction', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bb/id/(:segment)', 'Warehouse\PenerimaanBarangImportBB::update/$1', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-import-bb/update', 'Warehouse\PenerimaanBarangImportBB::updateAction', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bb/print/(:segment)', 'Warehouse\PenerimaanBarangImportBB::print/$1', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-import-bb/delete', 'Warehouse\PenerimaanBarangImportBB::delete', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-import-bb/posting', 'Warehouse\PenerimaanBarangImportBB::posting', ['filter' => 'Auth']);

// PENERIMAAN BARANG IMPORT BP
$routes->get('/penerimaan-barang-import-bp', 'Warehouse\PenerimaanBarangImportBP::index', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bp/all', 'Warehouse\PenerimaanBarangImportBP::all', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bp/create', 'Warehouse\PenerimaanBarangImportBP::create', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bp/generate-po-no', 'Warehouse\PenerimaanBarangLokalBP::generatePONo', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bp/get-po', 'Purchase\POImportBahanPenolong::dropdownPOImportBahanPenolong', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bp/list-barang', 'Warehouse\PenerimaanBarangImportBP::listBarangLPB', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-import-bp/insert', 'Warehouse\PenerimaanBarangImportBP::createAction', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bp/id/(:segment)', 'Warehouse\PenerimaanBarangImportBP::update/$1', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-import-bp/update', 'Warehouse\PenerimaanBarangImportBP::updateAction', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bp/print/(:segment)', 'Warehouse\PenerimaanBarangImportBP::print/$1', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-import-bp/delete', 'Warehouse\PenerimaanBarangImportBP::delete', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-import-bp/posting', 'Warehouse\PenerimaanBarangImportBP::posting', ['filter' => 'Auth']);

// PENERIMAAN BARANG LOKAL 
// $routes->get('/penerimaan-barang-lokal', 'Warehouse\PenerimaanBarangLokal::penerimaanBarangLokal', ['filter' => 'Auth']);
// $routes->get('/penerimaan-barang-lokal/all', 'Warehouse\PenerimaanBarangLokal::allPenerimaanBarangLokal', ['filter' => 'Auth']);
// $routes->get('/penerimaan-barang-lokal/create', 'Warehouse\PenerimaanBarangLokal::createPenerimaanBarangLokal', ['filter' => 'Auth']);
// $routes->get('/penerimaan-barang-lokal/id/(:segment)', 'Warehouse\PenerimaanBarangLokal::getByIdPenerimaanBarangLokal/$1', ['filter' => 'Auth']);
// $routes->get('/penerimaan-barang-lokal/print/(:segment)', 'Warehouse\PenerimaanBarangLokal::print/$1', ['filter' => 'Auth']);
// $routes->post('/penerimaan-barang-lokal/save', 'Warehouse\PenerimaanBarangLokal::savePenerimaanBarangLokal', ['filter' => 'Auth']);
// $routes->post('/penerimaan-barang-lokal/update', 'Warehouse\PenerimaanBarangLokal::updatePenerimaanBarangLokal', ['filter' => 'Auth']);
// $routes->post('/penerimaan-barang-lokal/update-status', 'Warehouse\PenerimaanBarangLokal::updateStatusPenerimaanBarangLokal', ['filter' => 'Auth']);
// $routes->post('/penerimaan-barang-lokal/delete', 'Warehouse\PenerimaanBarangLokal::deletePenerimaanBarangLokal', ['filter' => 'Auth']);
// $routes->get('/penerimaan-barang-lokal/export-table', 'Warehouse\PenerimaanBarangLokal::exportTable', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal/receivedItemsBySupplier/(:num)', 'Warehouse\PenerimaanBarangLokal::getReceivedItemsBySupplier/$1', ['filter' => 'Auth']);
// $routes->get('/penerimaan-barang-lokal/generate', 'Warehouse\PenerimaanBarangLokal::generatePenerimaanBarang', ['filter' => 'Auth']);

// PENERIMAAN BARANG IMPORT
$routes->get('/penerimaan-barang-import', 'Warehouse\PenerimaanBarangImport::penerimaanBarangImport', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import/all', 'Warehouse\PenerimaanBarangImport::allPenerimaanBarangImport', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import/create', 'Warehouse\PenerimaanBarangImport::createPenerimaanBarangImport', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import/id/(:segment)', 'Warehouse\PenerimaanBarangImport::getByIdPenerimaanBarangImport/$1', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import/print/(:segment)', 'Warehouse\PenerimaanBarangImport::print/$1', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-import/save', 'Warehouse\PenerimaanBarangImport::savePenerimaanBarangImport', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-import/update', 'Warehouse\PenerimaanBarangImport::updatePenerimaanBarangImport', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-import/update-status', 'Warehouse\PenerimaanBarangImport::updateStatusPenerimaanBarangImport', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-import/delete', 'Warehouse\PenerimaanBarangImport::deletePenerimaanBarangImport', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import/receivedItemsBySupplier/(:num)', 'Warehouse\PenerimaanBarangImport::getReceivedItemsBySupplier/$1', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import/generate', 'Warehouse\PenerimaanBarangImport::generatePenerimaanBarang', ['filter' => 'Auth']);

// BEA CUKAI 2.3 - 4.1
// PO UTK BEA CUKAI
// $routes->get('/po-bea-cukai/dropdown', 'BeaCukai\BeaCukaiController::dropdownBeaCukaiPO', ['filter' => 'Auth']);
$routes->get('no-izin-tpb', 'Master\NomorIjinTPB::index', ['filter' => 'Auth']);
$routes->get('no-izin-tpb/all', 'Master\NomorIjinTPB::all', ['filter' => 'Auth']);
$routes->post('no-izin-tpb/create', 'Master\NomorIjinTPB::create', ['filter' => 'Auth']);
$routes->post('no-izin-tpb/update', 'Master\NomorIjinTPB::update', ['filter' => 'Auth']);
$routes->post('no-izin-tpb/delete', 'Master\NomorIjinTPB::delete', ['filter' => 'Auth']);
$routes->get('no-izin-tpb/get', 'Master\NomorIjinTPB::get', ['filter' => 'Auth']);


// BC 2.3
$routes->group('bea-cukai-bc-23', ['filter' => 'Auth'], function ($routes) {
    $routes->get('/', 'BeaCukai\BC23::index');
    $routes->get('all', 'BeaCukai\BC23::all');
    // HEADER
    $routes->get('id/header/(:segment)', 'BeaCukai\BC23::createHeaderView/$1');
    $routes->post('id/header', 'BeaCukai\BC23::createHeaderAction');
    // ENTITAS
    $routes->get('id/entitas/(:segment)', 'BeaCukai\BC23::createEntitasView/$1');
    $routes->post('id/entitas', 'BeaCukai\BC23::createEntitasAction');
    // DOKUMEN
    $routes->get('id/dokumen/(:segment)', 'BeaCukai\BC23::createDokumenView/$1');
    $routes->get('id/dokumen/data/all', 'BeaCukai\BC23::allDokumen');
    $routes->post('id/dokumen/create', 'BeaCukai\BC23::createDokumenAction');
    $routes->post('id/dokumen/delete', 'BeaCukai\BC23::deleteDokumenAction');
    // PENGANGKUT
    $routes->get('id/pengangkut/(:segment)', 'BeaCukai\BC23::createPengangkutView/$1');
    $routes->post('id/pengangkut', 'BeaCukai\BC23::createPengangkutAction');
    // KONTAINER(PETI KEMAS)
    $routes->get('id/kemasan-peti-kemas/(:segment)', 'BeaCukai\BC23::createKemasanPetiKemas/$1');
    $routes->get('id/kemasan-peti-kemas/kemasan/data/all', 'BeaCukai\BC23::allKemasan');
    $routes->get('id/kemasan-peti-kemas/kontainer/data/all', 'BeaCukai\BC23::allKontainer');
    $routes->post('id/kemasan-peti-kemas/kemasan/create', 'BeaCukai\BC23::createKemasanAction');
    $routes->post('id/kemasan-peti-kemas/kemasan/delete', 'BeaCukai\BC23::deleteKemasanAction');
    $routes->post('id/kemasan-peti-kemas/kontainer/create', 'BeaCukai\BC23::createKontainerAction');
    $routes->post('id/kemasan-peti-kemas/kontainer/delete', 'BeaCukai\BC23::deleteKontainerAction');
    // TRANSAKSI
    $routes->get('id/transaksi/(:segment)', 'BeaCukai\BC23::createTransaksiView/$1');
    $routes->post('id/transaksi', 'BeaCukai\BC23::createTransaksiAction');
    // BARANG
    $routes->get('id/barang/(:segment)', 'BeaCukai\BC23::createBarangView/$1');
    $routes->get('id/barang/(:segment)/(:segment)', 'BeaCukai\BC23::createBarangDetailView/$1/$2');
    $routes->get('id/barang-pungutan-all', 'BeaCukai\BC23::allPungutan');
    $routes->post('id/barang-pungutan-create', 'BeaCukai\BC23::createPungutanAction');
    $routes->post('id/barang-pungutan-delete', 'BeaCukai\BC23::deletePungutanAction');
    $routes->get('id/barang-dokumen-all', 'BeaCukai\BC23::allDokumenBarang');
    $routes->post('id/barang-dokumen-create', 'BeaCukai\BC23::createBarangDokumenAction');
    $routes->post('id/barang-dokumen-delete', 'BeaCukai\BC23::deleteBarangDokumenAction');
    $routes->post('id/barang', 'BeaCukai\BC23::createBarangDetailAction');
    // PUNGUTAN
    $routes->get('id/pungutan/(:segment)', 'BeaCukai\BC23::createPungutanView/$1');
    // PERNYATAAN
    $routes->get('id/pernyataan/(:segment)', 'BeaCukai\BC23::createPernyataanView/$1');
    $routes->post('id/pernyataan', 'BeaCukai\BC23::createPernyataanAction');
    // API
    $routes->get('api/valuta', 'BeaCukai\BC23::getValuta');
    $routes->get('api/get-pelabuhan', 'BeaCukai\BC23::getPelabuhan');
    $routes->get('api/get-manifest', 'BeaCukai\BC23::getManifest');
    $routes->get('api/get-kontainer-peti-kemas', 'BeaCukai\BC23::getBLKontainerPetiKemas');
    $routes->get('api/kirim-dokumen/(:segment)', 'BeaCukai\BC23::kirimCeisa/$1');
    $routes->get('satuan-barang', 'BeaCukai\BC23::getKodeSatuanBarang');
    // DELETE & UPDATE NO AJU
    $routes->post('id/delete', 'BeaCukai\BC23::delete');
    $routes->post('id/update-no-aju', 'BeaCukai\BC23::updateNoAju');
});

// BC 4.0
$routes->group('bea-cukai-bc-40', ['filter' => 'Auth'], function ($routes) {
    $routes->get('/', 'BeaCukai\BC40::index');
    $routes->get('all', 'BeaCukai\BC40::all');
    // HEADER
    $routes->get('id/header/(:segment)', 'BeaCukai\BC40::createHeaderView/$1');
    $routes->post('id/header', 'BeaCukai\BC40::createHeaderAction');
    // ENTITAS
    $routes->get('id/entitas/(:segment)', 'BeaCukai\BC40::createEntitasView/$1');
    $routes->post('id/entitas', 'BeaCukai\BC40::createEntitasAction');
    // DOKUMEN
    $routes->get('id/dokumen/(:segment)', 'BeaCukai\BC40::createDokumenView/$1');
    $routes->get('id/dokumen/data/all', 'BeaCukai\BC40::allDokumen');
    $routes->post('id/dokumen/create', 'BeaCukai\BC40::createDokumenAction');
    $routes->post('id/dokumen/delete', 'BeaCukai\BC40::deleteDokumenAction');
    // PENGANGKUT
    $routes->get('id/pengangkut/(:segment)', 'BeaCukai\BC40::createPengangkutView/$1');
    $routes->post('id/pengangkut', 'BeaCukai\BC40::createPengangkutAction');
    // KONTAINER(PETI KEMAS)
    $routes->get('id/kemasan-peti-kemas/(:segment)', 'BeaCukai\BC40::createKemasanPetiKemas/$1');
    $routes->get('id/kemasan-peti-kemas/kemasan/data/all', 'BeaCukai\BC40::allKemasan');
    $routes->get('id/kemasan-peti-kemas/kontainer/data/all', 'BeaCukai\BC40::allKontainer');
    $routes->post('id/kemasan-peti-kemas/kemasan/create', 'BeaCukai\BC40::createKemasanAction');
    $routes->post('id/kemasan-peti-kemas/kemasan/delete', 'BeaCukai\BC40::deleteKemasanAction');
    $routes->post('id/kemasan-peti-kemas/kontainer/create', 'BeaCukai\BC40::createKontainerAction');
    $routes->post('id/kemasan-peti-kemas/kontainer/delete', 'BeaCukai\BC40::deleteKontainerAction');
    // TRANSAKSI
    $routes->get('id/transaksi/(:segment)', 'BeaCukai\BC40::createTransaksiView/$1');
    $routes->post('id/transaksi', 'BeaCukai\BC40::createTransaksiAction');
    // BARANG
    $routes->get('id/barang/(:segment)', 'BeaCukai\BC40::createBarangView/$1');
    $routes->get('id/barang/(:segment)/(:segment)', 'BeaCukai\BC40::createBarangDetailView/$1/$2');
    $routes->get('id/barang-pungutan-all', 'BeaCukai\BC40::allPungutan');
    $routes->post('id/barang-pungutan-create', 'BeaCukai\BC40::createPungutanAction');
    $routes->post('id/barang-pungutan-delete', 'BeaCukai\BC40::deletePungutanAction');
    $routes->get('id/barang-dokumen-all', 'BeaCukai\BC40::allDokumenBarang');
    $routes->post('id/barang-dokumen-create', 'BeaCukai\BC40::createBarangDokumenAction');
    $routes->post('id/barang-dokumen-delete', 'BeaCukai\BC40::deleteBarangDokumenAction');
    $routes->post('id/barang', 'BeaCukai\BC40::createBarangDetailAction');
    // PUNGUTAN
    $routes->get('id/pungutan/(:segment)', 'BeaCukai\BC40::createPungutanView/$1');
    // PERNYATAAN
    $routes->get('id/pernyataan/(:segment)', 'BeaCukai\BC40::createPernyataanView/$1');
    $routes->post('id/pernyataan', 'BeaCukai\BC40::createPernyataanAction');
    // API
    $routes->get('satuan-barang', 'BeaCukai\BC23::getKodeSatuanBarang');
    $routes->post('id/update-no-aju', 'BeaCukai\BC40::updateNoAju');
    $routes->post('id/delete', 'BeaCukai\BC40::delete');
    $routes->get('api/kirim-dokumen/(:segment)', 'BeaCukai\BC40::kirimCeisa/$1');
});

// BC 2.7


// BC 2.5
$routes->group('bea-cukai-bc-25', ['filter' => 'Auth'], function ($routes) {
    $routes->get('/', 'BeaCukai\BeaCukaiController::bc25View');
    $routes->get('all', 'BeaCukai\BeaCukaiController::bc25All');
    $routes->get('create', 'BeaCukai\BeaCukaiController::bc25CreateFormView');
    $routes->get('id/(:segment)', 'BeaCukai\BeaCukaiController::bc25GetByIdFormView/$1');
    $routes->post('save', 'BeaCukai\BeaCukaiController::bc25SaveForm');
    $routes->post('update', 'BeaCukai\BeaCukaiController::bc25UpdateForm');
    $routes->post('delete', 'BeaCukai\BeaCukaiController::bcDelete');
});

// BC 2.6.1
$routes->group('bea-cukai-bc-261', ['filter' => 'Auth'], function ($routes) {
    $routes->get('/', 'BeaCukai\BeaCukaiController::bc261View');
    $routes->get('all', 'BeaCukai\BeaCukaiController::bc261All');
    $routes->get('create', 'BeaCukai\BeaCukaiController::bc261CreateFormView');
    $routes->get('id/(:segment)', 'BeaCukai\BeaCukaiController::bc261GetByIdFormView/$1');
    $routes->post('save', 'BeaCukai\BeaCukaiController::bc261SaveForm');
    $routes->post('update', 'BeaCukai\BeaCukaiController::bc261UpdateForm');
    $routes->post('delete', 'BeaCukai\BeaCukaiController::bcDelete');
});

// BC 2.6.2
$routes->group('bea-cukai-bc-261', ['filter' => 'Auth'], function ($routes) {
    $routes->get('/', 'BeaCukai\BeaCukaiController::bc261View');
    $routes->get('all', 'BeaCukai\BeaCukaiController::bc261All');
    $routes->get('create', 'BeaCukai\BeaCukaiController::bc261CreateFormView');
    $routes->get('id/(:segment)', 'BeaCukai\BeaCukaiController::bc261GetByIdFormView/$1');
    $routes->post('save', 'BeaCukai\BeaCukaiController::bc261SaveForm');
    $routes->post('update', 'BeaCukai\BeaCukaiController::bc261UpdateForm');
    $routes->post('delete', 'BeaCukai\BeaCukaiController::bcDelete');
});

$routes->get('/bea-cukai-bc-27', 'BeaCukai\BeaCukaiController::bc27View', ['filter' => 'Auth']);
$routes->get('/bea-cukai-bc-40', 'BeaCukai\BeaCukaiController::bc40View', ['filter' => 'Auth']);
$routes->get('/bea-cukai-bc-41', 'BeaCukai\beaCukaiController::bc41View', ['filter' => 'Auth']);

// HUMAN RESOURCE
// Attendance
$routes->get('/log-attendance', 'HR\Attendance::LogAttendance', ['filter' => 'Auth']);
$routes->post('/log-attendance/detail', 'HR\Attendance::getLogAttendanceDetail', ['filter' => 'Auth']);
$routes->get('/log-attendance/print/id/(:segment)', 'HR\Attendance::exportPDFLogPresensi/$1', ['filter' => 'Auth']);
$routes->get('/log-attendance/excel/id/(:segment)', 'HR\Attendance::exportExcelLogPresensi/$1', ['filter' => 'Auth']);

// Generate Attendance
$routes->get('/list-attendance', 'HR\Attendance::generateAttendanceView', ['filter' => 'Auth']);
$routes->post('/generate-attendance/global', 'HR\Attendance::generateAttendanceGlobalAction', ['filter' => 'Auth']);
$routes->post('/generate-attendance/personal', 'HR\Attendance::generateAttendancePersonalAction', ['filter' => 'Auth']);
$routes->post('/get-attendance', 'HR\Attendance::getDetailAttendance', ['filter' => 'Auth']);
$routes->post('/update-attendance', 'HR\Attendance::updateAttendance', ['filter' => 'Auth']);
$routes->post('/posting-unposting-attendance', 'HR\Attendance::updatePostAttendance', ['filter' => 'Auth']);
$routes->get('/attendance/like-employees', 'HR\Attendance::getEmployeesLike', ['filter' => 'Auth']);
$routes->get('/list-attendance/print/id/(:segment)', 'HR\Attendance::exportPDFPresensi/$1', ['filter' => 'Auth']);
$routes->get('/list-attendance/excel/id/(:segment)', 'HR\Attendance::exportExcelPresensi/$1', ['filter' => 'Auth']);
$routes->get('/list-attendance/triwulan/id/(:segment)/(:segment)/(:segment)', 'HR\Attendance::exportTriwulanAbsensi/$1/$2/$3', ['filter' => 'Auth']);

// Big Day
$routes->get('/big-days', 'Master\BigDays::ListBigDay', ['filter' => 'Auth']);
$routes->get('/big-days/all', 'Master\BigDays::allBigDay', ['filter' => 'Auth']);
$routes->get('/big-days/id/(:segment)', 'Master\BigDays::getById/$1', ['filter' => 'Auth']);
$routes->post('/big-days/save', 'Master\BigDays::saveBigDay', ['filter' => 'Auth']);
$routes->post('/big-days/update', 'Master\BigDays::updateBigDay', ['filter' => 'Auth']);
$routes->post('/big-days/delete', 'Master\BigDays::deleteBigDay', ['filter' => 'Auth']);

// Absensi Unit
$routes->get('/attendances-unit', 'Master\AttendancesUnit::ListData', ['filter' => 'Auth']);
$routes->get('/attendances-unit/all', 'Master\AttendancesUnit::allData', ['filter' => 'Auth']);
$routes->get('/attendances-unit/id/(:segment)', 'Master\AttendancesUnit::getById/$1', ['filter' => 'Auth']);
$routes->post('/attendances-unit/save', 'Master\AttendancesUnit::saveData', ['filter' => 'Auth']);
$routes->post('/attendances-unit/update', 'Master\AttendancesUnit::updateData', ['filter' => 'Auth']);
$routes->post('/attendances-unit/delete', 'Master\AttendancesUnit::deleteData', ['filter' => 'Auth']);
$routes->post('/attendances-unit/copy-to-finger', 'Master\AttendancesUnit::CopyToFinger', ['filter' => 'Auth']);
// Tunjangan
$routes->get('/tunjangan', 'Master\Tunjangan::ListTunjangan', ['filter' => 'Auth']);
$routes->get('/tunjangan/all', 'Master\Tunjangan::allTunjangan', ['filter' => 'Auth']);
$routes->get('/tunjangan/id/(:segment)', 'Master\Tunjangan::getById/$1', ['filter' => 'Auth']);
$routes->post('/tunjangan/save', 'Master\Tunjangan::saveTunjangan', ['filter' => 'Auth']);
$routes->post('/tunjangan/update', 'Master\Tunjangan::updateTunjangan', ['filter' => 'Auth']);
$routes->post('/tunjangan/delete', 'Master\Tunjangan::deleteTunjangan', ['filter' => 'Auth']);

// payroll
$routes->get('/payroll', 'HR\Payroll::payroll', ['filter' => 'Auth']);
$routes->get('/payroll/all', 'HR\Payroll::getAllPayRoll', ['filter' => 'Auth']);
$routes->post('/payroll/generate-global', 'HR\Payroll::generateGlobalPayroll', ['filter' => 'Auth']);
$routes->post('/payroll/generate-single', 'HR\Payroll::generateSinglePayroll', ['filter' => 'Auth']);
$routes->get('/payroll/id/(:segment)', 'HR\Payroll::detailPayrollView/$1', ['filter' => 'Auth']);
$routes->post('/payroll/update/nominal-komponen-gaji', 'HR\Payroll::updateNominalKomponenGaji', ['filter' => 'Auth']);
$routes->post('/payroll/update/nominal-keterlambatan-presensi', 'HR\Payroll::updateNominalKeterlambatanPresensi', ['filter' => 'Auth']);
$routes->post('/payroll/update/nominal-gaji-cadangan', 'HR\Payroll::updateNominalGajiPerHariAndCadangan', ['filter' => 'Auth']);
$routes->post('/payroll/employees', 'HR\Payroll::getEmployeeByDivision', ['filter' => 'Auth']);
$routes->get('/payroll/print/single/(:segment)', 'HR\Payroll::exportPdfPayrollSingle/$1', ['filter' => 'Auth']);
$routes->get('/payroll/print/division/(:segment)/(:segment)', 'HR\Payroll::exportPdfPayrollDivision/$1/$2', ['filter' => 'Auth']);
$routes->get('/payroll/print/detail/(:segment)/(:segment)', 'HR\Payroll::exportPdfPayrollDivisionDetail/$1/$2', ['filter' => 'Auth']);
$routes->get('/payroll/print/summary/(:segment)/(:segment)', 'HR\Payroll::exportPdfSummary/$1/$2', ['filter' => 'Auth']);
$routes->get('/payroll/print/potongan/(:segment)/(:segment)', 'HR\Payroll::exportPdfPotongan/$1/$2', ['filter' => 'Auth']);


// formula payroll
$routes->get('/formula-payroll', 'HR\FormulaPayroll::formulaPayroll', ['filter' => 'Auth']);
$routes->get('/formula-payroll/create', 'HR\FormulaPayroll::createView', ['filter' => 'Auth']);
$routes->post('/formula-payroll/save', 'HR\FormulaPayroll::create', ['filter' => 'Auth']);
$routes->post('/formula-payroll/update', 'HR\FormulaPayroll::update', ['filter' => 'Auth']);

// pinjaman karyawan
$routes->get('/pinjaman-karyawan', 'HR\PinjamanKaryawan::pinjamanKaryawan', ['filter' => 'Auth']);
$routes->get('/pinjaman-karyawan/all', 'HR\PinjamanKaryawan::all', ['filter' => 'Auth']);
$routes->post('/pinjaman-karyawan/generate-all', 'HR\PinjamanKaryawan::generateAllPinjaman', ['filter' => 'Auth']);
$routes->post('/pinjaman-karyawan/employees', 'HR\Payroll::getEmployeeByDivision', ['filter' => 'Auth']);
$routes->post('/pinjaman-karyawan/generate-single', 'HR\PinjamanKaryawan::generateSinglePinjaman', ['filter' => 'Auth']);
$routes->post('/pinjaman-karyawan/update-nominal', 'HR\PinjamanKaryawan::updateNominalPinjaman', ['filter' => 'Auth']);
$routes->post('/pinjaman-karyawan/change-status', 'HR\PinjamanKaryawan::changeStatusPinjaman', ['filter' => 'Auth']);
$routes->get('/pinjaman-karyawan/print/(:segment)/(:segment)', 'HR\PinjamanKaryawan::exportPDF/$1/$2', ['filter' => 'Auth']);

// Perijinan
$routes->get('/form-perijinan', 'HR\Perijinan::perijinan', ['filter' => 'Auth']);
$routes->get('/form-perijinan/id/(:segment)', 'HR\Perijinan::getById/$1', ['filter' => 'Auth']);
$routes->post('/form-perijinan/update', 'HR\Perijinan::update', ['filter' => 'Auth']);
$routes->get('/form-perijinan/create', 'HR\Perijinan::createView', ['filter' => 'Auth']);
$routes->get('/form-perijinan/all', 'HR\Perijinan::allPerijinan', ['filter' => 'Auth']);
$routes->post('/form-perijinan/save', 'HR\Perijinan::save', ['filter' => 'Auth']);
$routes->post('/form-perijinan/delete', 'HR\Perijinan::delete', ['filter' => 'Auth']);
$routes->post('/form-perijinan/employees', 'HR\Perijinan::getEmployeeByDivision', ['filter' => 'Auth']);

// jam kerja
$routes->get('/jam-kerja', 'Master\JamKerja::index', ['filter' => 'Auth']);
$routes->get('/jam-kerja/all', 'Master\JamKerja::all', ['filter' => 'Auth']);
$routes->get('/jam-kerja/create', 'Master\JamKerja::createView', ['filter' => 'Auth']);
$routes->post('/jam-kerja/create', 'Master\JamKerja::create', ['filter' => 'Auth']);
$routes->post('/jam-kerja/delete', 'Master\JamKerja::delete', ['filter' => 'Auth']);
$routes->post('/jam-kerja/update', 'Master\JamKerja::update', ['filter' => 'Auth']);
$routes->get('/jam-kerja/id/(:segment)', 'Master\JamKerja::getById/$1', ['filter' => 'Auth']);

// lembur
$routes->get('/lembur', 'HR\FormLembur::index', ['filter' => 'Auth']);
$routes->get('/lembur/create', 'HR\FormLembur::createView', ['filter' => 'Auth']);
$routes->post('/lembur/generate-pay', 'HR\FormLembur::generateLemburPay', ['filter' => 'Auth']);
$routes->get('/lembur/all', 'HR\FormLembur::all', ['filter' => 'Auth']);
$routes->post('/lembur/create', 'HR\FormLembur::create', ['filter' => 'Auth']);
$routes->post('/lembur/delete', 'HR\FormLembur::delete', ['filter' => 'Auth']);
$routes->get('/lembur/id/(:segment)', 'HR\FormLembur::getById/$1', ['filter' => 'Auth']);
$routes->post('/lembur/employees', 'HR\FormLembur::getEmployeeByDivision', ['filter' => 'Auth']);

// SETTINGS
// USER
$routes->get('/user', 'Setting\User::user', ['filter' => 'Auth']);
$routes->get('/user/all', 'Setting\User::allUser', ['filter' => 'Auth']);
$routes->get('/user/id/(:segment)', 'Setting\User::getByIdUser/$1', ['filter' => 'Auth']);
$routes->post('/user/save', 'Setting\User::saveUser', ['filter' => 'Auth']);
$routes->post('/user/update', 'Setting\User::updateUser', ['filter' => 'Auth']);
$routes->post('/user/delete', 'Setting\User::deleteUser', ['filter' => 'Auth']);
$routes->post('/user/find-divisi', 'Purchase\POLokalBahanPenolong::getDivisionByCompany', ['filter' => 'Auth']);

// ROLE
$routes->get('/role', 'Setting\Role::role', ['filter' => 'Auth']);
$routes->get('/role/all', 'Setting\Role::allRole', ['filter' => 'Auth']);
$routes->get('/role/id/(:segment)', 'Setting\Role::getByIdRole/$1', ['filter' => 'Auth']);
$routes->post('/role/save', 'Setting\Role::saveRole', ['filter' => 'Auth']);
$routes->post('/role/update', 'Setting\Role::updateRole', ['filter' => 'Auth']);
$routes->post('/role/delete', 'Setting\Role::deleteRole', ['filter' => 'Auth']);

// JABATAN
$routes->get('/jabatan', 'Personalia\Jabatan::jabatan', ['filter' => 'Auth']);
$routes->get('/jabatan/all', 'Personalia\Jabatan::allJabatan', ['filter' => 'Auth']);
$routes->get('/jabatan/id/(:segment)', 'Personalia\Jabatan::getByIdJabatan/$1', ['filter' => 'Auth']);
$routes->post('/jabatan/save', 'Personalia\Jabatan::saveJabatan', ['filter' => 'Auth']);
$routes->post('/jabatan/update', 'Personalia\Jabatan::updateJabatan', ['filter' => 'Auth']);
$routes->post('/jabatan/delete', 'Personalia\Jabatan::deleteJabatan', ['filter' => 'Auth']);

// GOLONGAN
$routes->get('/golongan', 'HR\Golongan::index', ['filter' => 'Auth']);
$routes->get('/golongan/all', 'HR\Golongan::all', ['filter' => 'Auth']);
$routes->get('/golongan/id/(:segment)', 'HR\Golongan::get/$1', ['filter' => 'Auth']);
$routes->post('/golongan/update', 'HR\Golongan::update', ['filter' => 'Auth']);
$routes->post('/golongan/delete', 'HR\Golongan::delete', ['filter' => 'Auth']);
$routes->post('/golongan/create', 'HR\Golongan::create', ['filter' => 'Auth']);

// AKSES
$routes->get('/akses', 'Setting\Akses::akses', ['filter' => 'Auth']);
$routes->get('/akses/id', 'Setting\Akses::getAkses', ['filter' => 'Auth']);
$routes->post('/akses/save', 'Setting\Akses::saveAkses', ['filter' => 'Auth']);

// COMPANY ACCESS
$routes->get('/company-access', 'Setting\CompanyAccess::companyAccess', ['filter' => 'Auth']);
$routes->get('/company-access/all', 'Setting\CompanyAccess::allCompanyAccess', ['filter' => 'Auth']);


//api
$routes->get('/get-employee-by-company/(:segment)', 'HR\Attendance::get_employee_by_company/$1', ['filter' => 'Auth']);

// attendance
$routes->get('/api/employees-sync-attendances', 'API\Employees::sync_employee_to_master', ['filter' => 'Auth']);
$routes->cli('/api/employees-sync-attendances', 'API\Employees::sync_employee_to_master');
$routes->cli('/api/sync-attendances', 'API\Attendances::sync_attendance');
$routes->get('/api/sync-attendances', 'API\Attendances::sync_attendance', ['filter' => 'Auth']);
//$routes->get('/api/employees-sync-attendances', 'API\Employees::sync_employee_to_master', ['filter' => 'Auth']);

//api

//Accounting
//Jurnal
$routes->get('/jurnal', 'Accounting\JurnalUmum\JurnalUmum::index', ['filter' => 'Auth']);
$routes->post('/jurnal/addJurnal', 'Accounting\JurnalUmum\JurnalUmum::save', ['filter' => 'Auth']);
$routes->post('/jurnal/getSubAkuns', 'Accounting\JurnalUmum\JurnalUmum::searchSubAkun', ['filter' => 'Auth']);
//Jurnal Penyesuaian
$routes->get('/jurnal-penyesuaian', 'Accounting\JurnalPenyesuaian\JurnalPenyesuaian::index', ['filter' => 'Auth']);
$routes->post('/jurnal-penyesuaian/addJurnal', 'Accounting\JurnalPenyesuaian\JurnalPenyesuaian::save', ['filter' => 'Auth']);
$routes->post('/jurnal-penyesuaian/getSubAkuns', 'Accounting\JurnalPenyesuaian\JurnalPenyesuaian::searchSubAkun', ['filter' => 'Auth']);
//Module Account
$routes->get('/account-module', 'Accounting\AccountModule\AccountModule::index', ['filter' => 'Auth']);
$routes->get('/account-module/all', 'Accounting\AccountModule\AccountModule::allAccountModule', ['filter' => 'Auth']);
$routes->get('/account-module/id/(:segment)', 'Accounting\AccountModule\AccountModule::getByIdAccountModule/$1', ['filter' => 'Auth']);
$routes->post('/account-module/save', 'Accounting\AccountModule\AccountModule::saveAccountModule', ['filter' => 'Auth']);
$routes->post('/account-module/update', 'Accounting\AccountModule\AccountModule::updateAccountModule', ['filter' => 'Auth']);
$routes->post('/account-module/delete', 'Accounting\AccountModule\AccountModule::deleteAccountModule', ['filter' => 'Auth']);
//Tipe Barang
$routes->get('/tipe-barang', 'Accounting\Barang\TipeBarang::index', ['filter' => 'Auth']);
$routes->get('/tipe-barang/all', 'Accounting\Barang\TipeBarang::allTipeBarang', ['filter' => 'Auth']);
$routes->get('/tipe-barang/id/(:segment)', 'Accounting\Barang\TipeBarang::getByIdAccountModule/$1', ['filter' => 'Auth']);
$routes->post('/tipe-barang/save', 'Accounting\Barang\TipeBarang::saveTipeBarang', ['filter' => 'Auth']);
$routes->post('/tipe-barang/update', 'Accounting\Barang\TipeBarang::updateTipeBarang', ['filter' => 'Auth']);
$routes->post('/tipe-barang/delete', 'Accounting\Barang\TipeBarang::deleteTipeBarang', ['filter' => 'Auth']);
$routes->post('/tipe-barang/get', 'Accounting\Barang\TipeBarang::get', ['filter' => 'Auth']);
//Akun Customer
$routes->get('/akun-customer', 'Accounting\AccountCustomer\AccountCustomerController::index', ['filter' => 'Auth']);
$routes->get('/akun-customer/all', 'Accounting\AccountCustomer\AccountCustomerController::allAccountCustomer', ['filter' => 'Auth']);
$routes->get('/akun-customer/id/(:segment)', 'Accounting\AccountCustomer\AccountCustomerController::getByIdAccountModule/$1', ['filter' => 'Auth']);
$routes->post('/akun-customer/save', 'Accounting\AccountCustomer\AccountCustomerController::saveAccountCustomer', ['filter' => 'Auth']);
$routes->post('/akun-customer/update', 'Accounting\AccountCustomer\AccountCustomerController::updateAccountCustomer', ['filter' => 'Auth']);
$routes->post('/akun-customer/delete', 'Accounting\AccountCustomer\AccountCustomerController::deleteAccountCustomer', ['filter' => 'Auth']);
$routes->post('/akun-customer/get', 'Accounting\AccountCustomer\AccountCustomerController::get', ['filter' => 'Auth']);
//Akun Supplier
$routes->get('/akun-supplier', 'Accounting\AccountSupplier\AccountSupplierController::index', ['filter' => 'Auth']);
$routes->get('/akun-supplier/all', 'Accounting\AccountSupplier\AccountSupplierController::allAccountSupplier', ['filter' => 'Auth']);
$routes->get('/akun-supplier/id/(:segment)', 'Accounting\AccountSupplier\AccountSupplierController::getByIdAccountModule/$1', ['filter' => 'Auth']);
$routes->post('/akun-supplier/save', 'Accounting\AccountSupplier\AccountSupplierController::saveAccountSupplier', ['filter' => 'Auth']);
$routes->post('/akun-supplier/update', 'Accounting\AccountSupplier\AccountSupplierController::updateAccountSupplier', ['filter' => 'Auth']);
$routes->post('/akun-supplier/delete', 'Accounting\AccountSupplier\AccountSupplierController::deleteAccountSupplier', ['filter' => 'Auth']);
$routes->post('/akun-supplier/get', 'Accounting\AccountSupplier\AccountSupplierController::get', ['filter' => 'Auth']);
//Tipe Barang
$routes->get('/akun-department', 'Accounting\AccountDepartment\AccountDepartmentController::index', ['filter' => 'Auth']);
$routes->get('/akun-department/all', 'Accounting\AccountDepartment\AccountDepartmentController::allAccountDepartment', ['filter' => 'Auth']);
$routes->get('/akun-department/id/(:segment)', 'Accounting\AccountDepartment\AccountDepartmentController::getByIdAccountModule/$1', ['filter' => 'Auth']);
$routes->post('/akun-department/save', 'Accounting\AccountDepartment\AccountDepartmentController::saveAccountDepartment', ['filter' => 'Auth']);
$routes->post('/akun-department/update', 'Accounting\AccountDepartment\AccountDepartmentController::updateAccountDepartment', ['filter' => 'Auth']);
$routes->post('/akun-department/delete', 'Accounting\AccountDepartment\AccountDepartmentController::deleteAccountDepartment', ['filter' => 'Auth']);
$routes->post('/akun-department/get', 'Accounting\AccountDepartment\AccountDepartmentController::get', ['filter' => 'Auth']);
//Laporan
//Accounting
$routes->get('/laporan-accounting', 'Laporan\Accounting\Accounting::index', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/pembelian', 'Laporan\Accounting\Pembelian::index', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/pembelian', 'Laporan\Accounting\Pembelian::index', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/pembelian/all', 'Laporan\Accounting\Pembelian::allTransaksi', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/pembelian/printPDF/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\Accounting\Pembelian::LaporanPembelianPrint/$1/$2/$3/$4', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/pembelian/printExcel/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\Accounting\Pembelian::exportExcel/$1/$2/$3/$4', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/hutang', 'Laporan\Accounting\Hutang::index', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/hutang', 'Laporan\Accounting\Hutang::index', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/hutang/all', 'Laporan\Accounting\Hutang::allTransaksi', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/hutang/printPDF/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\Accounting\Hutang::LaporanHutangPrint/$1/$2/$3/$4', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/hutang/printExcel/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\Accounting\Hutang::exportExcel/$1/$2/$3/$4', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/neraca', 'Laporan\Accounting\Neraca::index', ['filter' => 'Auth']);
$routes->post('/laporan-accounting/neraca', 'Laporan\Accounting\Neraca::index', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/neraca/printPDF/(:segment)/(:segment)', 'Laporan\Accounting\Neraca::exportPDF/$1/$2', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/neraca/printExcel/(:segment)/(:segment)', 'Laporan\Accounting\Neraca::exportExcel/$1/$2', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/labarugi', 'Laporan\Accounting\LabaRugi::index', ['filter' => 'Auth']);
$routes->post('/laporan-accounting/labarugi', 'Laporan\Accounting\LabaRugi::index', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/labarugi/printPDF/(:segment)/(:segment)', 'Laporan\Accounting\LabaRugi::exportPDF/$1/$2', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/labarugi/printExcel/(:segment)/(:segment)', 'Laporan\Accounting\LabaRugi::exportExcel/$1/$2', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/bukubesar', 'Laporan\Accounting\BukuBesar::index', ['filter' => 'Auth']);
$routes->post('/laporan-accounting/bukubesar', 'Laporan\Accounting\BukuBesar::index', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/bukubesar/printPDF/(:segment)/(:segment)/(:segment)', 'Laporan\Accounting\BukuBesar::exportPDF/$1/$2/$3', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/bukubesar/printExcel/(:segment)/(:segment)/(:segment)', 'Laporan\Accounting\BukuBesar::exportExcel/$1/$2/$3', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/jurnalumum', 'Laporan\Accounting\JurnalUmum::index', ['filter' => 'Auth']);
$routes->post('/laporan-accounting/jurnalumum', 'Laporan\Accounting\JurnalUmum::index', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/jurnalumum/printPDF/(:segment)/(:segment)/(:segment)', 'Laporan\Accounting\JurnalUmum::exportPDF/$1/$2/$3', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/jurnalumum/printExcel/(:segment)/(:segment)/(:segment)', 'Laporan\Accounting\JurnalUmum::exportExcel/$1/$2/$3', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/neracasaldo', 'Laporan\Accounting\NeracaSaldo::index', ['filter' => 'Auth']);
$routes->post('/laporan-accounting/neracasaldo', 'Laporan\Accounting\NeracaSaldo::index', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/neracasaldo/printPDF/(:segment)/(:segment)', 'Laporan\Accounting\NeracaSaldo::exportPDF/$1/$2', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/neracasaldo/printExcel/(:segment)/(:segment)', 'Laporan\Accounting\NeracaSaldo::exportExcel/$1/$2', ['filter' => 'Auth']);
// Supplier Lokal BB
$routes->get('/laporan-supplier-lokal-bb', 'Laporan\Supplier\LaporanSupplierLokalBB::index', ['filter' => 'Auth']);
$routes->get('/laporan-supplier-lokal-bb/kwitansi-tb', 'Laporan\Supplier\KwitansiTb::index', ['filter' => 'Auth']);
$routes->get('/laporan-supplier-lokal-bb/kwitansi-tb/all', 'Laporan\Supplier\KwitansiTb::all', ['filter' => 'Auth']);
$routes->get('/laporan-supplier-lokal-bb/kwitansi-tb/print/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\Supplier\KwitansiTb::exportPDFKwitansiTB/$1/$2/$3/$4', ['filter' => 'Auth']);

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
