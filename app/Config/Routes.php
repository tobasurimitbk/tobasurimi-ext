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

$routes->get('/generate-country', 'Warehouse\Penomoran::generateCountry');

// DASHBOARD
$routes->get('/dashboard', 'Dashboard\Dashboard::dashboard', ['filter' => 'Auth']);
$routes->get('/dashboard/toggle', 'Dashboard\Dashboard::toggleSidebar', ['filter' => 'Auth']);
$routes->get('/dashboard/change-theme', 'Dashboard\Dashboard::darkLightMode', ['filter' => 'Auth']);
$routes->get('/dashboard/get-number-bc', 'Dashboard\Dashboard::getNumberBC', ['filter' => 'Auth']);
$routes->get('/dashboard/list-dokumen-bc23/', 'Dashboard\RekapBeaCukai::rekapBC23', ['filter' => 'Auth']);
$routes->get('/dashboard/list-dokumen-bc23/all', 'Dashboard\RekapBeaCukai::rekapBC23all', ['filter' => 'Auth']);
$routes->get('/dashboard/list-dokumen-bc23/exportsheet', 'Dashboard\RekapBeaCukai::rekapBC23Sheet', ['filter' => 'Auth']);
$routes->get('/dashboard/list-dokumen-bc25/', 'Dashboard\RekapBeaCukai::rekapBC25', ['filter' => 'Auth']);
$routes->get('/dashboard/list-dokumen-bc25/all', 'Dashboard\RekapBeaCukai::rekapBC25all', ['filter' => 'Auth']);
$routes->get('/dashboard/list-dokumen-bc25/exportsheet', 'Dashboard\RekapBeaCukai::rekapBC25Sheet', ['filter' => 'Auth']);
$routes->get('/dashboard/list-dokumen-bc27/', 'Dashboard\RekapBeaCukai::rekapBC27', ['filter' => 'Auth']);
$routes->get('/dashboard/list-dokumen-bc27/all', 'Dashboard\RekapBeaCukai::rekapBC27all', ['filter' => 'Auth']);
$routes->get('/dashboard/list-dokumen-bc27/exportsheet', 'Dashboard\RekapBeaCukai::rekapBC27Sheet', ['filter' => 'Auth']);
$routes->get('/dashboard/list-dokumen-bc30/', 'Dashboard\RekapBeaCukai::rekapBC30', ['filter' => 'Auth']);
$routes->get('/dashboard/list-dokumen-bc30/all', 'Dashboard\RekapBeaCukai::rekapBC30all', ['filter' => 'Auth']);
$routes->get('/dashboard/list-dokumen-bc30/exportsheet', 'Dashboard\RekapBeaCukai::rekapBC30Sheet', ['filter' => 'Auth']);
$routes->get('/dashboard/list-dokumen-bc40/', 'Dashboard\RekapBeaCukai::rekapBC40', ['filter' => 'Auth']);
$routes->get('/dashboard/list-dokumen-bc40/all', 'Dashboard\RekapBeaCukai::rekapBC40all', ['filter' => 'Auth']);
$routes->get('/dashboard/list-dokumen-bc40/exportsheet', 'Dashboard\RekapBeaCukai::rekapBC40Sheet', ['filter' => 'Auth']);
$routes->get('/dashboard/list-dokumen-bc41/', 'Dashboard\RekapBeaCukai::rekapBC41', ['filter' => 'Auth']);
$routes->get('/dashboard/list-dokumen-bc41/all', 'Dashboard\RekapBeaCukai::rekapBC41all', ['filter' => 'Auth']);
$routes->get('/dashboard/list-dokumen-bc41/exportsheet', 'Dashboard\RekapBeaCukai::rekapBC41Sheet', ['filter' => 'Auth']);
$routes->get('/dashboard/list-dokumen-ppbkb/', 'Dashboard\RekapBeaCukai::rekapPPBKB', ['filter' => 'Auth']);
$routes->get('/dashboard/list-dokumen-ppbkb/all', 'Dashboard\RekapBeaCukai::rekapPPBKBall', ['filter' => 'Auth']);
$routes->get('/dashboard/list-dokumen-ppbkb/exportsheet', 'Dashboard\RekapBeaCukai::rekapPPBKBSheet', ['filter' => 'Auth']);
$routes->get('/dashboard/list-wip', 'Laporan\BeaCukai\LaporanBeaCukai::allWipProduksiDashboard', ['filter' => 'Auth']);
$routes->get('/dashboard/list-wip/excel', 'Laporan\BeaCukai\LaporanBeaCukai::allWipProduksiDashboardExcel', ['filter' => 'Auth']);


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
$routes->post('/customer/import-excel', 'Master\Customer::importCustomer', ['filter' => 'Auth']);
$routes->get('/customer/export-excel', 'Master\Customer::exportExcel', ['filter' => 'Auth']);

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
$routes->get('/kategori-account/sheet', 'Master\Account::sheetKategoriAccount', ['filter' => 'Auth']);
$routes->post('/kategori-account/import', 'Master\Account::importKategoriAccount', ['filter' => 'Auth']);

$routes->get('/header-account/all', 'Master\Account::allHeaderAccount', ['filter' => 'Auth']);
$routes->get('/header-account/id/(:segment)', 'Master\Account::getByIdHeaderAccount/$1', ['filter' => 'Auth']);
$routes->post('/header-account/save', 'Master\Account::saveHeaderAccount', ['filter' => 'Auth']);
$routes->post('/header-account/update', 'Master\Account::updateHeaderAccount', ['filter' => 'Auth']);
$routes->post('/header-account/delete', 'Master\Account::deleteHeaderAccount', ['filter' => 'Auth']);
$routes->get('/header-account/sheet', 'Master\Account::sheetHeaderAccount', ['filter' => 'Auth']);
$routes->post('/header-account/import', 'Master\Account::importHeaderAccount', ['filter' => 'Auth']);

$routes->get('/sub-account/all', 'Master\Account::allSubAccount', ['filter' => 'Auth']);
$routes->get('/sub-account/id/(:segment)', 'Master\Account::getByIdSubAccount/$1', ['filter' => 'Auth']);
$routes->post('/sub-account/save', 'Master\Account::saveSubAccount', ['filter' => 'Auth']);
$routes->post('/sub-account/update', 'Master\Account::updateSubAccount', ['filter' => 'Auth']);
$routes->post('/sub-account/update-status', 'Master\Account::updateStatusSubAccount', ['filter' => 'Auth']);
$routes->post('/sub-account/delete', 'Master\Account::deleteSubAccount', ['filter' => 'Auth']);
$routes->get('/sub-account/sheet', 'Master\Account::sheetSubAccount', ['filter' => 'Auth']);
$routes->post('/sub-account/import', 'Master\Account::importSubAccount', ['filter' => 'Auth']);

// KODE HS
$routes->get('/hs-code', 'Master\HSCode::hsCode', ['filter' => 'Auth']);
$routes->get('/hs-code/all', 'Master\HSCode::allHSCode', ['filter' => 'Auth']);
$routes->get('/hs-code/id/(:segment)', 'Master\HSCode::getByIdHSCode/$1', ['filter' => 'Auth']);
$routes->post('/hs-code/save', 'Master\HSCode::saveHSCode', ['filter' => 'Auth']);
$routes->post('/hs-code/update', 'Master\HSCode::updateHSCode', ['filter' => 'Auth']);
$routes->post('/hs-code/delete', 'Master\HSCode::deleteHSCode', ['filter' => 'Auth']);
$routes->post('/hs-code/import', 'Master\HSCode::import', ['filter' => 'Auth']);
$routes->get('/hs-code/export', 'Master\HSCode::export', ['filter' => 'Auth']);

// SATUAN
$routes->get('/satuan', 'Master\Satuan::satuan', ['filter' => 'Auth']);
$routes->get('/satuan/all', 'Master\Satuan::allSatuan', ['filter' => 'Auth']);
$routes->get('/satuan/id/(:segment)', 'Master\Satuan::getByIdSatuan/$1', ['filter' => 'Auth']);
$routes->post('/satuan/save', 'Master\Satuan::saveSatuan', ['filter' => 'Auth']);
$routes->post('/satuan/update', 'Master\Satuan::updateSatuan', ['filter' => 'Auth']);
$routes->post('/satuan/delete', 'Master\Satuan::deleteSatuan', ['filter' => 'Auth']);
$routes->get('/satuan/export-excel', 'Master\Satuan::exportExcel', ['filter' => 'Auth']);
$routes->post('/satuan/import-excel', 'Master\Satuan::importSatuan', ['filter' => 'Auth']);

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

// KAWASAN
// $routes->get('/kawasan-warehouse', 'Master\Kawasan::index', ['filter' => 'Auth']);
// $routes->get('/kawasan-warehouse/all', 'Master\Kawasan::all', ['filter' => 'Auth']);
// $routes->post('/kawasan-warehouse/save', 'Master\Kawasan::create', ['filter' => 'Auth']);
// $routes->post('/kawasan-warehouse/update', 'Master\Kawasan::update', ['filter' => 'Auth']);
// $routes->post('/kawasan-warehouse/delete', 'Master\Kawasan::delete', ['filter' => 'Auth']);
// $routes->get('/kawasan-warehouse/get', 'Master\Kawasan::get', ['filter' => 'Auth']);

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
$routes->post('supplier/import-excel', 'Supplier\Supplier::importSupplier', ['filter' => 'Auth']);
$routes->get('supplier/export-excel', 'Supplier\Supplier::exportExcel', ['filter' => 'Auth']);


// BAHAN BAKU LOKAL
$routes->get('/supplier-bahan-baku', 'Supplier\Supplier::supplierBahanBaku', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-baku/generate-kode-supplier-bb', 'Supplier\Supplier::generateKodeSupplierBB', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-baku/harga/(:segment)', 'Supplier\Supplier::getSupplierBahanBakuHarga/$1', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-baku/all', 'Supplier\Supplier::allSupplierBahanBaku', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-baku/save', 'Supplier\Supplier::saveSupplierBahanBaku', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-baku/update', 'Supplier\Supplier::updateSupplierBahanBaku', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-baku/print/(:segment)', 'Supplier\Supplier::printSupplierBahanBaku/$1', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-baku/spesifikasi-barang', 'Supplier\SupplierHarga::getListSpesifikasiBarang', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-baku/all-harga', 'Supplier\SupplierHarga::supplierHargaAll', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-baku/harga/save', 'Supplier\SupplierHarga::saveSupplierHarga', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-baku/harga/update', 'Supplier\SupplierHarga::updateSupplierHarga', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-baku/harga/delete', 'Supplier\SupplierHarga::deleteSupplierHarga', ['filter' => 'Auth']);


// BAHAN PENOLONG
$routes->get('/supplier-bahan-penolong', 'Supplier\Supplier::supplierBahanPenolong', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-penolong/generate-kode-supplier-bp', 'Supplier\Supplier::generateKodeSupplierBP', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-penolong/all', 'Supplier\Supplier::allSupplierBahanPenolong', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-penolong/save', 'Supplier\Supplier::saveSupplierBahanPenolong', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-penolong/update', 'Supplier\Supplier::updateSupplierBahanPenolong', ['filter' => 'Auth']);

$routes->get('/supplier-internasional', 'Supplier\Supplier::supplierInternasional', ['filter' => 'Auth']);
$routes->get('/supplier-internasional/generate-kode-supplier-internasional', 'Supplier\Supplier::generateKodeSupplierInternasional', ['filter' => 'Auth']);
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
$routes->post('/spp/delete-detail', 'Purchase\SPP::deleteSPPDetail', ['filter' => 'Auth']);
$routes->get('/spp/print-table', 'Purchase\SPP::printTable', ['filter' => 'Auth']);
$routes->get('/spp/print/(:segment)', 'Purchase\SPP::print/$1', ['filter' => 'Auth']);
$routes->post('/spp/close-spp', 'Purchase\SPP::closeSPP', ['filter' => 'Auth']);


// BAHAN BAKU PO LOKAL
$routes->get('/po-lokal-bahan-baku', 'Purchase\POLokalBahanBaku::poLokalBahanBaku', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-baku/generate-po-no', 'Purchase\POLokalBahanBaku::generateNoPO', ['filter' => 'Auth']);
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
$routes->get('/po-lokal-bahan-penolong/generate-po-no', 'Purchase\POLokalBahanPenolong::generateNoPO', ['filter' => 'Auth']);
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
$routes->get('/po-lokal-bahan-penolong/dropdown/get-barang', 'Purchase\POLokalBahanPenolong::dropdownBarang', ['filter' => 'Auth']);

// BAHAN BAKU PO IMPORT
$routes->get('/po-import-bahan-baku', 'Purchase\POImportBahanBaku::poImportBahanBaku', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-baku/generate-no-po', 'Purchase\POImportBahanBaku::generateNoPo', ['filter' => 'Auth']);
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
$routes->get('/po-import-bahan-baku/dropdown/get-barang-bahan-baku', 'Purchase\POImportBahanBaku::dropDownBahanBaku', ['filter' => 'Auth']);

// BAHAN BAKU PO PENOLONG
$routes->get('/po-import-bahan-penolong', 'Purchase\POImportBahanPenolong::poImportBahanPenolong', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-penolong/generate-no-po', 'Purchase\POImportBahanPenolong::generateNoPo', ['filter' => 'Auth']);
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
$routes->get('/po-import-bahan-penolong/dropdown/get-barang-bahan-penolong', 'Purchase\POImportBahanPenolong::dropDownBahanPenolong', ['filter' => 'Auth']);

// TANDA TERIMA FAKTUR LOKAL BB
$routes->get('/tanda-terima-faktur-lokal-bp', 'Purchase\TandaTerimaSupBB::index', ['filter' => 'Auth']);
$routes->get('/tanda-terima-faktur-lokal-bp/all', 'Purchase\TandaTerimaSupBB::all', ['filter' => 'Auth']);
$routes->get('/tanda-terima-faktur-lokal-bp/create', 'Purchase\TandaTerimaSupBB::create', ['filter' => 'Auth']);
$routes->get('/tanda-terima-faktur-lokal-bp/id/(:segment)', 'Purchase\TandaTerimaSupBB::update/$1', ['filter' => 'Auth']);
$routes->get('/tanda-terima-faktur-lokal-bp/print/(:segment)', 'Purchase\TandaTerimaSupBB::print/$1', ['filter' => 'Auth']);
$routes->get('/tanda-terima-faktur-lokal-bp/generate-tanda-terima-no', 'Purchase\TandaTerimaSupBB::generateTandaTerimaFakturNumber', ['filter' => 'Auth']);
$routes->get('/tanda-terima-faktur-lokal-bp/generate-tanda-keluar-no', 'Purchase\TandaTerimaSupBB::generateTandaKeluarFakturNumber', ['filter' => 'Auth']);
$routes->get('/tanda-terima-faktur-lokal-bp/daftar-penerimaan-barang', 'Purchase\TandaTerimaSupBB::listPenerimaanBarang', ['filter' => 'Auth']);
$routes->post('/tanda-terima-faktur-lokal-bp/create', 'Purchase\TandaTerimaSupBB::createAction', ['filter' => 'Auth']);
$routes->post('/tanda-terima-faktur-lokal-bp/update', 'Purchase\TandaTerimaSupBB::updateAction', ['filter' => 'Auth']);
$routes->post('/tanda-terima-faktur-lokal-bp/delete', 'Purchase\TandaTerimaSupBB::delete', ['filter' => 'Auth']);
$routes->get('/tanda-terima-faktur-lokal-bp/history-pembayaran', 'Purchase\TandaTerimaSupBB::historyPembayaran', ['filter' => 'Auth']);
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
//ambil gaji
$routes->get('/ambil-gaji', 'Pembayaran\AmbilGaji::index', ['filter' => 'Auth']);
$routes->get('/ambil-gaji/all', 'Pembayaran\AmbilGaji::getAllAmbilGaji', ['filter' => 'Auth']);
$routes->post('/ambil-gaji/check-ambil-gaji', 'Pembayaran\AmbilGaji::checkIsAmbil', ['filter' => 'Auth']);
//ambil pinjaman karyawan
$routes->get('/ambil-pinjaman-karyawan', 'Pembayaran\AmbilPinjamanKaryawan::index', ['filter' => 'Auth']);
$routes->get('/ambil-pinjaman-karyawan/all', 'Pembayaran\AmbilPinjamanKaryawan::getAllAmbilPinjamanKaryawan', ['filter' => 'Auth']);
$routes->post('/ambil-pinjaman-karyawan/check-ambil-pinjaman', 'Pembayaran\AmbilPinjamanKaryawan::checkIsAmbil', ['filter' => 'Auth']);
// PEMBAYARAN PO LOKAL BP
$routes->get('/pembayaran-po-lokal-bp', 'Pembayaran\PembayaranPOLokal::pembayaranPOLokalBP', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal-bp/create', 'Pembayaran\PembayaranPOLokal::createPembayaranPOLokalBP', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal-bp/get-rekap-faktur/(:segment)/(:segment)', 'Pembayaran\PembayaranPOLokal::getTandaTerimaFaktur/$1/$2', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal-bp/get-item-list/(:segment)/(:segment)', 'Pembayaran\PembayaranPOLokal::getItemListByTandaTerimaFaktur/$1/$2', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal-bp/generate-no-pembayaran', 'Pembayaran\PembayaranPOLokal::generatePaymentNoBP', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal-bp/create', 'Pembayaran\PembayaranPOLokal::createPembayaranPOLokalBPAction', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal-bp/update', 'Pembayaran\PembayaranPOLokal::updatePembayaranPOLokalBPAction', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal-bp/delete', 'Pembayaran\PembayaranPOLokal::deleteBP', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal-bp/id/(:segment)', 'Pembayaran\PembayaranPOLokal::getPembayaranPOLokalBP/$1', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal-bp/print/(:segment)', 'Pembayaran\PembayaranPOLokal::pembayaranPOLokalBPPrint/$1', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal-bp/posting', 'Pembayaran\PembayaranPOLokal::posting', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal/all', 'Pembayaran\PembayaranPOLokal::allPembayaranPOLokal', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal-bp/all', 'Pembayaran\PembayaranPOLokal::allPembayaranPOLokalBP', ['filter' => 'Auth']);

$routes->post('/pembayaran-po-lokal/delete', 'Pembayaran\PembayaranPOLokal::delete', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal/get-panjar', 'Pembayaran\PembayaranPOLokal::getPanjarSupplier', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal/get-panjar-table', 'Pembayaran\PembayaranPOLokal::getPanjarSupplierTable', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal/get-panjar-amount', 'Pembayaran\PembayaranPOLokal::getPanjarSisaPembayaran', ['filter' => 'Auth']);

// PEMBAYARAN PO LOKAL BB
$routes->get('/pembayaran-po-lokal-bb', 'Pembayaran\PembayaranPOLokal::pembayaranPOLokalBB', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal-bb/create', 'Pembayaran\PembayaranPOLokal::createPembayaranPOLokalBB', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal-bb/get-lpb-not-paid', 'Pembayaran\PembayaranPOLokal::getListDokumenLPBNotPaidBB', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal-bb/get-po-not-paid', 'Pembayaran\PembayaranPOLokal::getListDokumenPoNotPaidBB', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal-bb/get-list-po-paid', 'Pembayaran\PembayaranPOLokal::getListBarangLPBPaidBB', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal-bb/get-list-po-no-paid', 'Pembayaran\PembayaranPOLokal::getListBarangPoNotPaidBB', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal-bb/create', 'Pembayaran\PembayaranPOLokal::createPembayaranPOLokalBBAction', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal-bb/update', 'Pembayaran\PembayaranPOLokal::updatePembayaranPOLokalBBAction', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal-bb/delete', 'Pembayaran\PembayaranPOLokal::deleteBB', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal-bb/id/(:segment)', 'Pembayaran\PembayaranPOLokal::getPembayaranPOLokalBB/$1', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal-bb/print/(:segment)', 'Pembayaran\PembayaranPOLokal::pembayaranPOLokalBBPrint/$1', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal-bb/posting', 'Pembayaran\PembayaranPOLokal::posting', ['filter' => 'Auth']);

// PEMBAYARAN PO LOKAL
$routes->get('/pembayaran-po-lokal', 'Pembayaran\PembayaranPOLokal::pembayaranPOLokal', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal/(:num)', 'Pembayaran\PembayaranPOLokal::getByIdPembayaranPOLokal/$1', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal/create', 'Pembayaran\PembayaranPOLokal::createPembayaranPOLokal', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal/create', 'Pembayaran\PembayaranPOLokal::savePembayaranPOLokal', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal/update', 'Pembayaran\PembayaranPOLokal::updatePembayaranPOLokal', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal/delete', 'Pembayaran\PembayaranPOLokal::deletePembayaranPOLokal', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal/print/(:num)', 'Pembayaran\PembayaranPOLokal::print/$1', ['filter' => 'Auth']);
$routes->get('pembayaran-po-lokal-bb/generate-no-pembayaran', 'Pembayaran\PembayaranPOLokal::generatePaymentNoLokalBB', ['filter' => 'Auth']);

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
$routes->get('/pembayaran-po-import/get-item-list/(:segment)', 'Pembayaran\PembayaranPOImport::listPembayaranPOImport/$1', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-import/all-po', 'Pembayaran\PembayaranPOImport::allPO', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-import/all-riwayat-pembayaran', 'Pembayaran\PembayaranPOImport::allRiwayatPembayaran', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-import/posting', 'Pembayaran\PembayaranPOImport::posting', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-import/print/(:segment)', 'Pembayaran\PembayaranPOImport::print/$1', ['filter' => 'Auth']);

// PEMBAYARAN LAIN LAIN
$routes->get('/pembayaran-lain', 'Pembayaran\OtherPayment::index', ['filter' => 'Auth']);
$routes->get('/pembayaran-lain/create', 'Pembayaran\OtherPayment::create', ['filter' => 'Auth']);
$routes->post('/pembayaran-lain/save', 'Pembayaran\OtherPayment::createAction', ['filter' => 'Auth']);
$routes->get('/pembayaran-lain/get', 'Pembayaran\OtherPayment::get', ['filter' => 'Auth']);
$routes->post('/pembayaran-lain/delete', 'Pembayaran\OtherPayment::delete', ['filter' => 'Auth']);
$routes->get('/pembayaran-lain/all', 'Pembayaran\OtherPayment::all', ['filter' => 'Auth']);
$routes->post('/pembayaran-lain/posting', 'Pembayaran\OtherPayment::posting', ['filter' => 'Auth']);
$routes->post('/pembayaran-lain/update', 'Pembayaran\OtherPayment::updateAction', ['filter' => 'Auth']);
// penerimaan pembayaran SO
$routes->get('/penerimaan-penjualan-lokal', 'Penerimaan\SalesOrderPayment::index', ['filter' => 'Auth']);
$routes->get('/penerimaan-penjualan-lokal/create', 'Penerimaan\SalesOrderPayment::create', ['filter' => 'Auth']);


// PEMBAYARAN PANJAR SUPPLIER
$routes->get('/panjar-supplier', 'Pembayaran\PanjarSupplier::index', ['filter' => 'Auth']);
$routes->get('/panjar-supplier/list-akunCoa', 'Pembayaran\PanjarSupplier::getSubAkun', ['filter' => 'Auth']);
$routes->get('/panjar-supplier/list-supplier', 'Pembayaran\PanjarSupplier::dropdownSupplierByType', ['filter' => 'Auth']);
$routes->post('/panjar-supplier/save', 'Pembayaran\PanjarSupplier::savePanjarSupplier', ['filter' => 'Auth']);
$routes->get('/panjar-supplier/all', 'Pembayaran\PanjarSupplier::allPanjarSupplier', ['filter' => 'Auth']);
$routes->get('/panjar-supplier/id/(:segment)', 'Pembayaran\PanjarSupplier::getByIdPanjarSupplier/$1', ['filter' => 'Auth']);
$routes->post('/panjar-supplier/update', 'Pembayaran\PanjarSupplier::updatePanjarSupplier', ['filter' => 'Auth']);
$routes->post('/panjar-supplier/update-status', 'Pembayaran\PanjarSupplier::updateStatusPanjarSupplier', ['filter' => 'Auth']);
$routes->post('/panjar-supplier/delete', 'Pembayaran\PanjarSupplier::deletePanjarSupplier', ['filter' => 'Auth']);
$routes->get('/panjar-supplier/history-pembayaran', 'Pembayaran\PanjarSupplier::dropDownHistoryPembayaranPanjar', ['filter' => 'Auth']);
$routes->get('/panjar-supplier/generate-no-panjar', 'Pembayaran\PanjarSupplier::generateNoPanjar', ['filter' => 'Auth']);


// PEMBAYARAN PINJAMAN SUPPLIER
$routes->get('/pinjaman-supplier', 'Pembayaran\PinjamanSupplier::index', ['filter' => 'Auth']);
$routes->get('/pinjaman-supplier/list-supplier', 'Pembayaran\PinjamanSupplier::dropdownSupplierByType', ['filter' => 'Auth']);
$routes->post('/pinjaman-supplier/save', 'Pembayaran\PinjamanSupplier::savePinjamanSupplier', ['filter' => 'Auth']);
$routes->get('/pinjaman-supplier/all', 'Pembayaran\PinjamanSupplier::allPinjamanSupplier', ['filter' => 'Auth']);
$routes->get('/pinjaman-supplier/id/(:segment)', 'Pembayaran\PinjamanSupplier::getByIdPinjamanSupplier/$1', ['filter' => 'Auth']);
$routes->post('/pinjaman-supplier/update', 'Pembayaran\PinjamanSupplier::updatePinjamanSupplier', ['filter' => 'Auth']);
$routes->post('/pinjaman-supplier/update-status', 'Pembayaran\PinjamanSupplier::updateStatusPinjamanSupplier', ['filter' => 'Auth']);
$routes->post('/pinjaman-supplier/delete', 'Pembayaran\PinjamanSupplier::deletePinjamanSupplier', ['filter' => 'Auth']);
$routes->get('/pinjaman-supplier/generate-no-pinjaman', 'Pembayaran\PinjamanSupplier::generateNoPinjaman', ['filter' => 'Auth']);

// PEMBAYARAN INVOICE
$routes->get('/pembayaran-invoice', 'Pembayaran\PembayaranInvoice::index', ['filter' => 'Auth']);
$routes->get('/pembayaran-invoice/id/(:segment)', 'Pembayaran\PembayaranInvoice::getById/$1', ['filter' => 'Auth']);
$routes->get('/pembayaran-invoice/all', 'Pembayaran\PembayaranInvoice::getAllPembayaranInvoice', ['filter' => 'Auth']);
$routes->get('/pembayaran-invoice/create', 'Pembayaran\PembayaranInvoice::createPembayaranInvoiceLokal', ['filter' => 'Auth']);
$routes->get('/pembayaran-invoice/create-ekspor', 'Pembayaran\PembayaranInvoice::createPembayaranInvoiceEkspor', ['filter' => 'Auth']);
$routes->get('/pembayaran-invoice/create-lain', 'Pembayaran\PembayaranInvoice::createPembayaranInvoiceLain', ['filter' => 'Auth']);
$routes->get('/pembayaran-invoice/create-return', 'Pembayaran\PembayaranInvoice::createPembayaranInvoiceReturn', ['filter' => 'Auth']);
$routes->get('/pembayaran-invoice/dropdown-invoice-return', 'Pembayaran\PembayaranInvoice::dropdownInvoiceReturn', ['filter' => 'Auth']);
$routes->get('/pembayaran-invoice/get-dokumen-list', 'Pembayaran\PembayaranInvoice::getDokumenList', ['filter' => 'Auth']);
$routes->get('/pembayaran-invoice/get-dokumen-invoice-lokal/(:segment)', 'Pembayaran\PembayaranInvoice::getDataDokumenInvoiceLokal/$1', ['filter' => 'Auth']);
$routes->get('/pembayaran-invoice/get-dokumen-invoice-return/(:segment)', 'Pembayaran\PembayaranInvoice::getDataDokumenInvoiceReturn/$1', ['filter' => 'Auth']);
$routes->get('pembayaran-invoice/get-valas-sales-ekspor', 'Pembayaran\PembayaranInvoice::getValas', ['filter' => 'Auth']);
$routes->get('/pembayaran-invoice/get-barang-sales-lokal', 'Pembayaran\PembayaranInvoice::getBarangSalesLokal', ['filter' => 'Auth']);
$routes->get('/pembayaran-invoice/get-barang-sales-ekspor', 'Pembayaran\PembayaranInvoice::getBarangSalesEkspor', ['filter' => 'Auth']);
$routes->get('/pembayaran-invoice/get-barang-sales-lain', 'Pembayaran\PembayaranInvoice::getBarangSalesLain', ['filter' => 'Auth']);
$routes->get('/pembayaran-invoice/get-barang-sales-return', 'Pembayaran\PembayaranInvoice::getBarangSalesReturn', ['filter' => 'Auth']);
$routes->post('/pembayaran-invoice/generate-no-pembayaran', 'Pembayaran\PembayaranInvoice::generateNoPembayaranInvoice', ['filter' => 'Auth']);
$routes->post('/pembayaran-invoice/save', 'Pembayaran\PembayaranInvoice::saveLokalInvoice', ['filter' => 'Auth']);
$routes->post('/pembayaran-invoice/update', 'Pembayaran\PembayaranInvoice::updateInvoice', ['filter' => 'Auth']);
$routes->get('/pembayaran-invoice/get-customer', 'Pembayaran\PembayaranInvoice::getCustomer', ['filter' => 'Auth']);
$routes->post('/pembayaran-invoice/delete', 'Pembayaran\PembayaranInvoice::deletePembayaranInvoice', ['filter' => 'Auth']);
$routes->post('/pembayaran-invoice/posting', 'Pembayaran\PembayaranInvoice::posting', ['filter' => 'Auth']);



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
$routes->post('/order-form-lokal/delete-detail', 'SalesLokal\OrderForm::deleteOrderForm', ['filter' => 'Auth']);
$routes->get('/order-form-lokal/print/(:segment)', 'SalesLokal\OrderForm::printOrder/$1', ['filter' => 'Auth']);
$routes->get('/order-form-lokal/getItemList', 'SalesLokal\OrderForm::getItemListByIds', ['filter' => 'Auth']);
$routes->get('/order-form-lokal/getItemList/(:num)', 'SalesLokal\OrderForm::getItemListById/$1', ['filter' => 'Auth']);
$routes->get('/order-form-lokal/histori-harga', 'SalesLokal\OrderForm::HistoriHargaBarang', ['filter' => 'Auth']);

$routes->get('/order-form-lokal/barangAll', 'SalesLokal\OrderForm::getAllBarang', ['filter' => 'Auth']);
$routes->get('/order-form-lokal/customer', 'SalesLokal\OrderForm::dropdownCustomer', ['filter' => 'Auth']);
$routes->post('/order-form-lokal/generate-no-order-form', 'SalesLokal\OrderForm::generateNomorSalesOrder', ['filter' => 'Auth']);

// Invoice Lokal
$routes->get('/invoice-penjualan-lokal', 'SalesLokal\Invoice::index', ['filter' => 'Auth']);
$routes->get('/invoice-penjualan-lokal/id/(:segment)', 'SalesLokal\Invoice::getById/$1', ['filter' => 'Auth']);
$routes->get('/invoice-penjualan-lokal/create', 'SalesLokal\Invoice::createView', ['filter' => 'Auth']);
$routes->get('/invoice-penjualan-lokal/all', 'SalesLokal\Invoice::all', ['filter' => 'Auth']);
$routes->post('/invoice-penjualan-lokal/save', 'SalesLokal\Invoice::save', ['filter' => 'Auth']);
$routes->post('/invoice-penjualan-lokal/update', 'SalesLokal\Invoice::update', ['filter' => 'Auth']);
$routes->post('/invoice-penjualan-lokal/delete', 'SalesLokal\Invoice::delete', ['filter' => 'Auth']);
$routes->post('/invoice-penjualan-lokal/posting',  'SalesLokal\Invoice::posting', ['filter' => 'Auth']);
$routes->get('/invoice-penjualan-lokal/getDocNumber/(:segment)/(:segment)', 'SalesLokal\Invoice::getDocNumber/$1/$2', ['filter' => 'Auth']);
$routes->get('/invoice-penjualan-lokal/getDocumentData/(:alpha)/(:num)', 'SalesLokal\Invoice::getDocData/$1/$2', ['filter' => 'Auth']);
$routes->get('/invoice-penjualan-lokal/getItemList/(:num)', 'SalesLokal\Invoice::getItemList/$1', ['filter' => 'Auth']);
$routes->get('/invoice-penjualan-lokal/print/(:segment)', 'SalesLokal\Invoice::printInvoice/$1', ['filter' => 'Auth']);
$routes->get('/invoice-penjualan-lokal/get-nomor-faktur', 'SalesLokal\Invoice::getNomorFaktur', ['filter' => 'Auth']);
$routes->get('/invoice-penjualan-lokal/barangAll', 'SalesLokal\Invoice::getAllBarang', ['filter' => 'Auth']);
// $routes->post('/invoice-penjualan-lokal/print',  'SalesLokal\Invoice::printInvoice', ['filter' => 'Auth']);

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
$routes->get('/surat-jalan/print/(:segment)', 'SalesLokal\SuratJalan::printSJ/$1', ['filter' => 'Auth']);
$routes->post('/surat-jalan/generate-no-surat-jalan', 'SalesLokal\SuratJalan::generateNomorSuratJalan', ['filter' => 'Auth']);

// Return barang sales
$routes->get('/return-barang-sales', 'SalesLokal\Retur::index', ['filter' => 'Auth']);
$routes->get('/return-barang-sales/create', 'SalesLokal\Retur::createView', ['filter' => 'Auth']);
$routes->get('/return-barang-sales/details/(:segment)', 'SalesLokal\Retur::getById/$1', ['filter' => 'Auth']);
$routes->get('/return-barang-sales/all', 'SalesLokal\Retur::all', ['filter' => 'Auth']);
$routes->post('/return-barang-sales/save', 'SalesLokal\Retur::save', ['filter' => 'Auth']);
$routes->post('/return-barang-sales/update', 'SalesLokal\Retur::update', ['filter' => 'Auth']);
$routes->post('/return-barang-sales/delete', 'SalesLokal\Retur::delete', ['filter' => 'Auth']);
$routes->post('/return-barang-sales/approve', 'SalesLokal\Retur::approve', ['filter' => 'Auth']);
$routes->get('/return-barang-sales/get-detail-invoice/(:segment)', 'SalesLokal\Retur::getInvoiceNumberList/$1', ['filter' => 'Auth']);
$routes->get('/return-barang-sales/get-nomor-surat-return', 'SalesLokal\Retur::getNomorSuratReturn', ['filter' => 'Auth']);

// Retur Pembelian
$routes->get('/retur-barang', 'Purchase\ReturPembelian::index', ['filter' => 'Auth']);
$routes->get('/retur-barang/create', 'Purchase\ReturPembelian::create', ['filter' => 'Auth']);
$routes->get('/retur-barang/generate-new-no', 'Purchase\ReturPembelian::generateNo', ['filter' => 'Auth']);
$routes->get('/retur-barang/generate-penerimaan-barang', 'Purchase\ReturPembelian::getPenerimaanBarangList', ['filter' => 'Auth']);


// MASTER BARANG LOKAL
$routes->get('master-barang-lokal', 'SalesLokal\Barang::bahanJadiView', ['filter' => 'Auth']);
$routes->get('master-barang-lokal/all', 'SalesLokal\Barang::all', ['filter' => 'Auth']);
$routes->post('master-barang-lokal/generate-new-code', 'SalesLokal\Barang::generateNewCode', ['filter' => 'Auth']);
$routes->post('master-barang-lokal/save', 'SalesLokal\Barang::create', ['filter' => 'Auth']);
$routes->post('master-barang-lokal/update', 'SalesLokal\Barang::update', ['filter' => 'Auth']);
$routes->post('master-barang-lokal/delete', 'SalesLokal\Barang::delete', ['filter' => 'Auth']);
$routes->post('master-barang-lokal/get', 'SalesLokal\Barang::get', ['filter' => 'Auth']);

// SALES INTERNASIONAL
// Sales Kontrak
$routes->get('/sales-kontrak', 'SalesInternasional\SalesKontrak::index', ['filter' => 'Auth']);
$routes->get('/sales-kontrak/id/(:segment)', 'SalesInternasional\SalesKontrak::detail/$1', ['filter' => 'Auth']);
$routes->get('/sales-kontrak/duplicate/(:segment)', 'SalesInternasional\SalesKontrak::duplicate/$1', ['filter' => 'Auth']);
$routes->get('/sales-kontrak/print/(:segment)', 'SalesInternasional\SalesKontrak::print/$1', ['filter' => 'Auth']);
$routes->get('/sales-kontrak/create', 'SalesInternasional\SalesKontrak::createView', ['filter' => 'Auth']);
$routes->get('/sales-kontrak/all', 'SalesInternasional\SalesKontrak::all', ['filter' => 'Auth']);
$routes->post('/sales-kontrak/save', 'SalesInternasional\SalesKontrak::save', ['filter' => 'Auth']);
$routes->post('/sales-kontrak/update', 'SalesInternasional\SalesKontrak::update', ['filter' => 'Auth']);
$routes->post('/sales-kontrak/update-status', 'SalesInternasional\SalesKontrak::updateStatus', ['filter' => 'Auth']);
$routes->post('/sales-kontrak/delete', 'SalesInternasional\SalesKontrak::delete', ['filter' => 'Auth']);
$routes->get('/sales-kontrak/get-sales-kontrak-no', 'SalesInternasional\SalesKontrak::getNo', ['filter' => 'Auth']);
$routes->get('/sales-kontrak/customer', 'SalesInternasional\SalesKontrak::dropdownCustomer', ['filter' => 'Auth']);
$routes->get('/sales-kontrak/master-barang', 'SalesInternasional\SalesKontrak::dropdownMasterBarang', ['filter' => 'Auth']);

// Order Form Internasional
$routes->get('/order-form-internasional', 'SalesInternasional\OrderForm::index', ['filter' => 'Auth']);
$routes->get('/order-form-internasional/create', 'SalesInternasional\OrderForm::createView', ['filter' => 'Auth']);
$routes->get('/order-form-internasional/id/(:segment)', 'SalesInternasional\OrderForm::getById/$1', ['filter' => 'Auth']);
$routes->get('/order-form-internasional/print/(:segment)', 'SalesInternasional\OrderForm::print/$1', ['filter' => 'Auth']);
$routes->get('/order-form-internasional/all', 'SalesInternasional\OrderForm::all', ['filter' => 'Auth']);
$routes->get('/order-form-internasional/get/sales-kontrak', 'SalesInternasional\OrderForm::dropdownSalesKontrak', ['filter' => 'Auth']);
$routes->get('/order-form-internasional/get/detail-sales-kontrak', 'SalesInternasional\OrderForm::getDetailSalesKontrak', ['filter' => 'Auth']);
$routes->get('/order-form-internasional/get/detail-info-sales-kontrak', 'SalesInternasional\OrderForm::getDetailInfoSalesKontrak', ['filter' => 'Auth']);
$routes->post('/order-form-internasional/save', 'SalesInternasional\OrderForm::saveOrder', ['filter' => 'Auth']);
$routes->post('/order-form-internasional/update', 'SalesInternasional\OrderForm::update', ['filter' => 'Auth']);
$routes->post('/order-form-internasional/update-status', 'SalesInternasional\OrderForm::updateStatus', ['filter' => 'Auth']);
$routes->get('/order-form-internasional/generate-no-order-form', 'SalesInternasional\OrderForm::generateNomorSalesOrderInternasional', ['filter' => 'Auth']);
// $routes->post('/order-form-internasional/update-remark', 'SalesInternasional\OrderForm::updateRemark', ['filter' => 'Auth']);
$routes->post('/order-form-internasional/delete', 'SalesInternasional\OrderForm::destroy', ['filter' => 'Auth']);

// Master Barang Internasional
$routes->get('/master-barang-internasional', 'SalesInternasional\Barang::bahanJadiView', ['filter' => 'Auth']);
$routes->get('/master-barang-internasional/all', 'SalesInternasional\Barang::all', ['filter' => 'Auth']);
$routes->post('/master-barang-internasional/save', 'SalesInternasional\Barang::create', ['filter' => 'Auth']);
$routes->post('/master-barang-internasional/update', 'SalesInternasional\Barang::update', ['filter' => 'Auth']);
$routes->post('/master-barang-internasional/delete', 'SalesInternasional\Barang::delete', ['filter' => 'Auth']);
$routes->post('/master-barang-internasional/get', 'SalesInternasional\Barang::get', ['filter' => 'Auth']);
$routes->post('/master-barang-internasional/generate-new-code', 'SalesInternasional\Barang::generateNewCode', ['filter' => 'Auth']);
$routes->post('/master-barang-internasional/import-excel', 'SalesInternasional\Barang::importExcel', ['filter' => 'Auth']);
$routes->get('/master-barang-internasional/export-excel', 'SalesInternasional\Barang::exportExcel', ['filter' => 'Auth']);

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

// SALES LAIN
$routes->get('order-form-lain', 'PenjualanLain\SalesOrderLain::index', ['filter' => 'Auth']);
$routes->get('order-form-lain/id/(:segment)', 'PenjualanLain\SalesOrderLain::detail/$1', ['filter' => 'Auth']);
$routes->get('order-form-lain/print/(:segment)', 'PenjualanLain\SalesOrderLain::print/$1', ['filter' => 'Auth']);
$routes->get('order-form-lain/all', 'PenjualanLain\SalesOrderLain::all', ['filter' => 'Auth']);
$routes->get('order-form-lain/create', 'PenjualanLain\SalesOrderLain::create', ['filter' => 'Auth']);
$routes->post('order-form-lain/save', 'PenjualanLain\SalesOrderLain::createAction', ['filter' => 'Auth']);
$routes->post('order-form-lain/update', 'PenjualanLain\SalesOrderLain::updateAction', ['filter' => 'Auth']);
$routes->post('order-form-lain/delete', 'PenjualanLain\SalesOrderLain::delete', ['filter' => 'Auth']);
$routes->post('order-form-lain/posting', 'PenjualanLain\SalesOrderLain::posting', ['filter' => 'Auth']);
$routes->post('order-form-lain/unposting', 'PenjualanLain\SalesOrderLain::unPosting', ['filter' => 'Auth']);

$routes->get('order-form-lain/list-sales-order-detail', 'PenjualanLain\SalesOrderLain::getListSalesOrderDetail', ['filter' => 'Auth']);
$routes->get('order-form-lain/list-stock-init', 'PenjualanLain\SalesOrderLain::dropdownListBarang', ['filter' => 'Auth']);
$routes->get('order-form-lain/list-stock-dokumen-bc', 'PenjualanLain\SalesOrderLain::getListStockByStockID', ['filter' => 'Auth']);
$routes->get('order-form-lain/list-satuan-konversi', 'PenjualanLain\SalesOrderLain::dropdownSatuanOrder', ['filter' => 'Auth']);
$routes->get('order-form-lain/list-customer', 'PenjualanLain\SalesOrderLain::dropdownListCustomer', ['filter' => 'Auth']);
$routes->get('order-form-lain/hitung-konversi', 'PenjualanLain\SalesOrderLain::hitungKonversi', ['filter' => 'Auth']);
$routes->get('order-form-lain/get-no', 'PenjualanLain\SalesOrderLain::getSalesOrderLainNo', ['filter' => 'Auth']);

// PRODUKSI
// Production Result
$routes->get('/production-result', 'Production\ProductionResult::index', ['filter' => 'Auth']);
$routes->get('/production-result/generate-kode-penerimaan', 'Production\ProductionResult::generateKodePenerimaan', ['filter' => 'Auth']);
$routes->get('/production-result/details/(:segment)', 'Production\ProductionResult::getById/$1', ['filter' => 'Auth']);
$routes->get('/production-result/print/(:segment)', 'Production\ProductionResult::printProductionResultPDF/$1', ['filter' => 'Auth']);
$routes->get('/production-result/all', 'Production\ProductionResult::getAll', ['filter' => 'Auth']);
$routes->get('/production-result/create', 'Production\ProductionResult::createProductionResult', ['filter' => 'Auth']);
$routes->post('/production-result/create', 'Production\ProductionResult::saveProductionResult', ['filter' => 'Auth']);
$routes->post('/production-result/update', 'Production\ProductionResult::updateProductionResult', ['filter' => 'Auth']);
$routes->get('/production-result/list-work-order', 'Production\ProductionResult::getListWorkOrderByID', ['filter' => 'Auth']);
$routes->get('/production-result/list-material-request', 'Production\ProductionResult::getListMaterialRequestByID', ['filter' => 'Auth']);
$routes->get('/production-result/material-request', 'Production\ProductionResult::getListMaterialRequestByWOID', ['filter' => 'Auth']);
$routes->post('/production-result/update-status', 'Production\ProductionResult::updateStatusPostedProductionResult', ['filter' => 'Auth']);
$routes->post('/production-result/delete', 'Production\ProductionResult::deletePR', ['filter' => 'Auth']);
$routes->post('/production-result/delete-detail', 'Production\ProductionResult::deletePRDetail', ['filter' => 'Auth']);

// Rencana Produksi
$routes->get('/work-order', 'Production\WorkOrder::index', ['filter' => 'Auth']);
$routes->get('/work-order/generate-kode-produksi', 'Production\WorkOrder::generateKodeProduksi', ['filter' => 'Auth']);
$routes->get('/work-order/details/(:segment)', 'Production\WorkOrder::getById/$1', ['filter' => 'Auth']);
$routes->get('/work-order/create', 'Production\WorkOrder::createView', ['filter' => 'Auth']);
$routes->get('/work-order/all', 'Production\WorkOrder::all', ['filter' => 'Auth']);
$routes->post('/work-order/save', 'Production\WorkOrder::create', ['filter' => 'Auth']);
$routes->post('/work-order/update', 'Production\WorkOrder::update', ['filter' => 'Auth']);
$routes->post('/work-order/delete', 'Production\WorkOrder::deleteWO', ['filter' => 'Auth']);
$routes->post('/work-order/delete-detail', 'Production\WorkOrder::deleteWODetail', ['filter' => 'Auth']);

// Request Stock
$routes->get('/request-stock', 'Production\RequestStock::index', ['filter' => 'Auth']);
$routes->get('/request-stock/all', 'Production\RequestStock::all', ['filter' => 'Auth']);
$routes->post('/request-stock/all', 'Production\RequestStock::all', ['filter' => 'Auth']);
$routes->post('/request-stock/update-approve', 'Production\RequestStock::approve', ['filter' => 'Auth']);
$routes->post('/request-stock/update-approve-penolong', 'Production\RequestStock::approvePenolong', ['filter' => 'Auth']);
$routes->get('/request-stock/details/(:segment)', 'Production\RequestStock::getById/$1', ['filter' => 'Auth']);
$routes->get('/request-stock/data-detail-material', 'Production\MaterialRequest::allDetailMaterialRequest', ['filter' => 'Auth']);

// Material Request
$routes->get('/material-request', 'Production\MaterialRequest::index', ['filter' => 'Auth']);
$routes->get('/material-request/details/(:segment)', 'Production\MaterialRequest::getById/$1', ['filter' => 'Auth']);
$routes->get('/material-request/print/(:segment)', 'Production\MaterialRequest::printMaterialRequestPDF/$1', ['filter' => 'Auth']);
$routes->get('/material-request/create', 'Production\MaterialRequest::createView', ['filter' => 'Auth']);
$routes->get('/material-request/all', 'Production\MaterialRequest::all', ['filter' => 'Auth']);
$routes->post('/material-request/delete', 'Production\MaterialRequest::deleteMR', ['filter' => 'Auth']);
$routes->post('/material-request/delete-detail', 'Production\MaterialRequest::deleteMRDetail', ['filter' => 'Auth']);
$routes->get('/material-request/data-detail-material', 'Production\MaterialRequest::allDetailMaterialRequest', ['filter' => 'Auth']);
$routes->post('/material-request/save', 'Production\MaterialRequest::create', ['filter' => 'Auth']);
$routes->post('/material-request/update', 'Production\MaterialRequest::update', ['filter' => 'Auth']);
$routes->post('/material-request/update-status', 'Production\MaterialRequest::updateStatusPostedMaterialRequest', ['filter' => 'Auth']);
$routes->get('/material-request/list-barang-stock-init', 'Production\MaterialRequest::getListBarangIsInit', ['filter' => 'Auth']);

// Material Request Penolong
$routes->get('/material-request-penolong', 'Production\MaterialRequestPenolong::index', ['filter' => 'Auth']);
$routes->get('/material-request-penolong/generate-kode-request', 'Production\MaterialRequestPenolong::generateKodeRequest', ['filter' => 'Auth']);
$routes->get('/material-request-penolong/details/(:segment)', 'Production\MaterialRequestPenolong::getById/$1', ['filter' => 'Auth']);
$routes->get('/material-request-penolong/print/(:segment)', 'Production\MaterialRequestPenolong::printMaterialRequestPenolongPDF/$1', ['filter' => 'Auth']);
$routes->get('/material-request-penolong/create', 'Production\MaterialRequestPenolong::createView', ['filter' => 'Auth']);
$routes->get('/material-request-penolong/all', 'Production\MaterialRequestPenolong::all', ['filter' => 'Auth']);
$routes->post('/material-request-penolong/delete', 'Production\MaterialRequestPenolong::deleteMR', ['filter' => 'Auth']);
$routes->post('/material-request-penolong/delete-detail', 'Production\MaterialRequestPenolong::deleteMRDetail', ['filter' => 'Auth']);
$routes->get('/material-request-penolong/data-detail-material', 'Production\MaterialRequestPenolong::allDetailMaterialRequest', ['filter' => 'Auth']);
$routes->post('/material-request-penolong/save', 'Production\MaterialRequestPenolong::create', ['filter' => 'Auth']);
$routes->post('/material-request-penolong/update', 'Production\MaterialRequestPenolong::update', ['filter' => 'Auth']);
$routes->post('/material-request-penolong/update-status', 'Production\MaterialRequestPenolong::updateStatusPostedMaterialRequest', ['filter' => 'Auth']);
$routes->get('/material-request-penolong/list-barang-stock-init', 'Production\MaterialRequestPenolong::getListBarangIsInit', ['filter' => 'Auth']);

// Material Request Kimia
$routes->get('/material-request-kimia', 'Production\MaterialRequestKimia::index', ['filter' => 'Auth']);
$routes->get('/material-request-kimia/generate-kode-request', 'Production\MaterialRequestKimia::generateKodeRequest', ['filter' => 'Auth']);
$routes->get('/material-request-kimia/details/(:segment)', 'Production\MaterialRequestKimia::getById/$1', ['filter' => 'Auth']);
$routes->get('/material-request-kimia/create', 'Production\MaterialRequestKimia::createView', ['filter' => 'Auth']);
$routes->get('/material-request-kimia/all', 'Production\MaterialRequestKimia::all', ['filter' => 'Auth']);
$routes->post('/material-request-kimia/delete', 'Production\MaterialRequestKimia::deleteMR', ['filter' => 'Auth']);
$routes->post('/material-request-kimia/delete-detail', 'Production\MaterialRequestKimia::deleteMRDetail', ['filter' => 'Auth']);
$routes->get('/material-request-kimia/data-detail-material', 'Production\MaterialRequestKimia::allDetailMaterialRequest', ['filter' => 'Auth']);
$routes->post('/material-request-kimia/save', 'Production\MaterialRequestKimia::create', ['filter' => 'Auth']);
$routes->post('/material-request-kimia/update', 'Production\MaterialRequestKimia::update', ['filter' => 'Auth']);
$routes->post('/material-request-kimia/update-status', 'Production\MaterialRequestKimia::updateStatusPostedMaterialRequest', ['filter' => 'Auth']);
$routes->get('/material-request-kimia/list-barang-stock-init', 'Production\MaterialRequestKimia::getListBarangIsInit', ['filter' => 'Auth']);

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
$routes->get('/warehouse/dropdown/divisi/(:segment)', 'Master\Warehouse::dropdownWarehouseByDivisiId/$1', ['filter' => 'Auth']);

// DIVISI
$routes->get('/divisi/dropdown', 'Master\Divisi::dropdownDivisi', ['filter' => 'Auth']);

// TAX
$routes->get('/tax/dropdown', 'Master\Tax::dropdownTax', ['filter' => 'Auth']);
$routes->get('/tax', 'Master\Tax::index', ['filter' => 'Auth']);
$routes->get('/tax/all', 'Master\Tax::all', ['filter' => 'Auth']);
$routes->get('/tax/id/(:segment)', 'Master\Tax::getById/$1', ['filter' => 'Auth']);
$routes->post('/tax/save', 'Master\Tax::save', ['filter' => 'Auth']);
$routes->post('/tax/update', 'Master\Tax::update', ['filter' => 'Auth']);
$routes->post('/tax/delete', 'Master\Tax::delete', ['filter' => 'Auth']);

// BANK
$routes->get('/bank', 'Master\Bank::index', ['filter' => 'Auth']);
$routes->get('/bank/all', 'Master\Bank::all', ['filter' => 'Auth']);
$routes->post('/bank/save', 'Master\Bank::create', ['filter' => 'Auth']);
$routes->post('/bank/update', 'Master\Bank::update', ['filter' => 'Auth']);
$routes->post('/bank/delete', 'Master\Bank::delete', ['filter' => 'Auth']);
$routes->get('/bank/get', 'Master\Bank::get', ['filter' => 'Auth']);

// BARANG
$routes->get('/barang/dropdown', 'Warehouse\Barang::dropdownBarang', ['filter' => 'Auth']);
$routes->get('/barang/dropdown/parent', 'Warehouse\Barang::dropdownParentBarang', ['filter' => 'Auth']);
$routes->get('/barang/dropdown/kategori', 'Warehouse\Barang::dropdownBarangKategori', ['filter' => 'Auth']);
$routes->get('/barang/dropdown/type', 'Warehouse\Barang::dropdownBarangType', ['filter' => 'Auth']);
$routes->get('/barang/dropdown/type-nospec', 'Warehouse\Barang::dropdownBarangTypeWithoutSpec', ['filter' => 'Auth']);
$routes->get('/barang/dropdown/type-nospecwo', 'Warehouse\Barang::dropdownBarangTypeWithoutSpecWO', ['filter' => 'Auth']);

// ACCOUNT
$routes->get('/kategori-account/dropdown', 'Master\Account::dropdownKategoriAccount', ['filter' => 'Auth']);
$routes->get('/header-account/dropdown', 'Master\Account::dropdownHeaderAccount', ['filter' => 'Auth']);
$routes->get('/sub-account/dropdown', 'Master\Account::dropdownSubAccount', ['filter' => 'Auth']);
$routes->get('/sub-account/dropdownData', 'Master\Account::getSubAkun', ['filter' => 'Auth']);
$routes->get('/ap-ar/dropdown', 'Master\Account::dropdownAPAR', ['filter' => 'Auth']);

// Master Barang
// Parent Barang
$routes->get('parent-barang', 'Warehouse\ParentBarang::index', ['filter' => 'Auth']);
$routes->post('parent-barang/save', 'Warehouse\ParentBarang::create', ['filter' => 'Auth']);
$routes->post('parent-barang/update', 'Warehouse\ParentBarang::update', ['filter' => 'Auth']);
$routes->post('parent-barang/delete', 'Warehouse\ParentBarang::delete', ['filter' => 'Auth']);
$routes->post('parent-barang/get', 'Warehouse\ParentBarang::get', ['filter' => 'Auth']);
$routes->get('parent-barang/all', 'Warehouse\ParentBarang::all', ['filter' => 'Auth']);
$routes->get('parent-barang/export-excel', 'Warehouse\ParentBarang::exportExcel', ['filter' => 'Auth']);
// Master Barang
$routes->get('barang-bahan-baku', 'Warehouse\Barang::bahanBakuView', ['filter' => 'Auth']);
$routes->get('barang-bahan-penolong', 'Warehouse\Barang::bahanPenolongView', ['filter' => 'Auth']);
$routes->get('barang-bahan-jadi', 'Warehouse\Barang::bahanJadiView', ['filter' => 'Auth']);
$routes->get('barang-scrap', 'Warehouse\Barang::bahanScrapView', ['filter' => 'Auth']);
$routes->get('barang-modal', 'Warehouse\Barang::bahanModalView', ['filter' => 'Auth']);
$routes->get('barang-setengah-jadi', 'Warehouse\Barang::bahanSetengahJadiView', ['filter' => 'Auth']);
$routes->group('barang-master', ['filter' => 'Auth'], function ($routes) {
    $routes->get('all', 'Warehouse\Barang::all');
    $routes->post('get', 'Warehouse\Barang::get');
    $routes->post('save', 'Warehouse\Barang::create');
    $routes->post('update', 'Warehouse\Barang::update');
    $routes->post('delete', 'Warehouse\Barang::delete');
    $routes->post('delete-spek', 'Warehouse\Barang::deleteSpek');
    $routes->post('generate-new-code', 'Warehouse\Barang::generateNewCode');
    $routes->get('generate-new-code', 'Warehouse\Barang::generateNewCode');
    $routes->post('import', 'Warehouse\Barang::import');
    $routes->get('export-excel', 'Warehouse\Barang::exportExcel');
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
$routes->post('/kemasan/import', 'Warehouse\Kemasan::import', ['filter' => 'Auth']);
$routes->get('/kemasan/export-excel', 'Warehouse\Kemasan::exportExcel', ['filter' => 'Auth']);

// JASA VENDOR
// REBUSAN
$routes->get('/proses-rebus', 'JasaVendor\ProsesRebus::index', ['filter' => 'Auth']);
$routes->get('/proses-rebus/create', 'JasaVendor\ProsesRebus::create', ['filter' => 'Auth']);
$routes->get('/proses-rebus/warehouse', 'Purchase\POLokalBahanBaku::dropdownWarehouse', ['filter' => 'Auth']);
$routes->get('/proses-rebus/list-barang-stock-init', 'JasaVendor\ProsesRebus::dropdownListBarangIsInit', ['filter' => 'Auth']);
$routes->get('/proses-rebus/list-barang-rebus', 'JasaVendor\ProsesRebus::dropdownListHasilRebus', ['filter' => 'Auth']);
$routes->get('/proses-rebus/list-stock-dokumen-bc', 'JasaVendor\JasaVendorOut::getListStockByStockID', ['filter' => 'Auth']);
$routes->get('/proses-rebus/all',  'JasaVendor\ProsesRebus::all', ['filter' => 'Auth']);
$routes->get('/proses-rebus/id/(:segment)',  'JasaVendor\ProsesRebus::detail/$1', ['filter' => 'Auth']);
$routes->post('/proses-rebus/save',  'JasaVendor\ProsesRebus::createAction', ['filter' => 'Auth']);
$routes->post('/proses-rebus/update',  'JasaVendor\ProsesRebus::updateAction', ['filter' => 'Auth']);
$routes->post('/proses-rebus/delete',  'JasaVendor\ProsesRebus::delete', ['filter' => 'Auth']);
$routes->post('/proses-rebus/posting',  'JasaVendor\ProsesRebus::posting', ['filter' => 'Auth']);
$routes->get('/proses-rebus/get-no',  'JasaVendor\ProsesRebus::getProsesRebusNo', ['filter' => 'Auth']);
$routes->post('/proses-rebus/unposting',  'JasaVendor\ProsesRebus::unPosting', ['filter' => 'Auth']);

// JASA VENDOR OUT
$routes->get('/jasa-vendor-out',  'JasaVendor\JasaVendorOut::index', ['filter' => 'Auth']);
$routes->get('/jasa-vendor-out/create',  'JasaVendor\JasaVendorOut::create', ['filter' => 'Auth']);
$routes->get('/jasa-vendor-out/warehouse', 'Purchase\POLokalBahanBaku::dropdownWarehouse', ['filter' => 'Auth']);
$routes->get('/jasa-vendor-out/list-barang-stock-init', 'JasaVendor\JasaVendorOut::dropdownListBarangIsInit', ['filter' => 'Auth']);
$routes->get('/jasa-vendor-out/list-stock-dokumen-bc', 'JasaVendor\JasaVendorOut::getListStockByStockID', ['filter' => 'Auth']);
$routes->get('/jasa-vendor-out/all', 'JasaVendor\JasaVendorOut::all', ['filter' => 'Auth']);
$routes->post('/jasa-vendor-out/save',  'JasaVendor\JasaVendorOut::createAction', ['filter' => 'Auth']);
$routes->post('/jasa-vendor-out/update',  'JasaVendor\JasaVendorOut::updateAction', ['filter' => 'Auth']);
$routes->post('/jasa-vendor-out/delete',  'JasaVendor\JasaVendorOut::delete', ['filter' => 'Auth']);
$routes->post('/jasa-vendor-out/posting',  'JasaVendor\JasaVendorOut::posting', ['filter' => 'Auth']);
$routes->post('/jasa-vendor-out/close',  'JasaVendor\JasaVendorOut::close', ['filter' => 'Auth']);
$routes->get('/jasa-vendor-out/id/(:segment)',  'JasaVendor\JasaVendorOut::detail/$1', ['filter' => 'Auth']);
$routes->get('/jasa-vendor-out/get-jasa-vendor-out-no',  'JasaVendor\JasaVendorOut::getJasaVendorOutNo', ['filter' => 'Auth']);
$routes->get('/jasa-vendor-out/print/(:segment)', 'JasaVendor\JasaVendorOut::print/$1', ['filter' => 'Auth']);
// JASA VENDOR IN
$routes->get('/jasa-vendor-in', 'JasaVendor\JasaVendorIn::index', ['filter' => 'Auth']);
$routes->get('/jasa-vendor-in/create', 'JasaVendor\JasaVendorIn::create', ['filter' => 'Auth']);
$routes->get('/jasa-vendor-in/divisi', 'JasaVendor\JasaVendorIn::dropdownDivisi', ['filter' => 'Auth']);
$routes->get('/jasa-vendor-in/warehouse', 'JasaVendor\JasaVendorIn::dropdownWarehouse', ['filter' => 'Auth']);
$routes->get('/jasa-vendor-in/list-jasa-vendor-out', 'JasaVendor\JasaVendorIn::dropdownNoJasaVendorOut', ['filter' => 'Auth']);
$routes->get('/jasa-vendor-in/list-barang', 'JasaVendor\JasaVendorIn::dropdownListBarangKeluar', ['filter' => 'Auth']);
$routes->get('/jasa-vendor-in/list-barang-masuk', 'JasaVendor\JasaVendorIn::dropdownListBarangMasuk', ['filter' => 'Auth']);
$routes->get('/jasa-vendor-in/get-jasa-vendor-in-no',  'JasaVendor\JasaVendorIn::getJasaVendorInNo', ['filter' => 'Auth']);
$routes->post('/jasa-vendor-in/save',  'JasaVendor\JasaVendorIn::createAction', ['filter' => 'Auth']);
$routes->post('/jasa-vendor-in/update',  'JasaVendor\JasaVendorIn::updateAction', ['filter' => 'Auth']);
$routes->post('/jasa-vendor-in/delete',  'JasaVendor\JasaVendorIn::delete', ['filter' => 'Auth']);
$routes->post('/jasa-vendor-in/posting',  'JasaVendor\JasaVendorIn::posting', ['filter' => 'Auth']);
$routes->get('/jasa-vendor-in/id/(:segment)',  'JasaVendor\JasaVendorIn::detail/$1', ['filter' => 'Auth']);
$routes->get('/jasa-vendor-in/print/(:segment)',  'JasaVendor\JasaVendorIn::print/$1', ['filter' => 'Auth']);
$routes->get('/jasa-vendor-in/all',  'JasaVendor\JasaVendorIn::all', ['filter' => 'Auth']);
// BIAYA UDANG
$routes->get('/biaya-udang', 'JasaVendor\BiayaUdang::index', ['filter' => 'Auth']);
$routes->get('/biaya-udang/create', 'JasaVendor\BiayaUdang::create', ['filter' => 'Auth']);
$routes->post('/biaya-udang/save', 'JasaVendor\BiayaUdang::createAction', ['filter' => 'Auth']);
$routes->post('/biaya-udang/update', 'JasaVendor\BiayaUdang::updateAction', ['filter' => 'Auth']);
$routes->post('/biaya-udang/delete', 'JasaVendor\BiayaUdang::delete', ['filter' => 'Auth']);
$routes->post('/biaya-udang/posting', 'JasaVendor\BiayaUdang::posting', ['filter' => 'Auth']);
$routes->get('/biaya-udang/list-barang', 'JasaVendor\BiayaUdang::dropdownBarang', ['filter' => 'Auth']);
$routes->get('/biaya-udang/get-no', 'JasaVendor\BiayaUdang::getNo', ['filter' => 'Auth']);
$routes->get('/biaya-udang/id/(:segment)',  'JasaVendor\BiayaUdang::detail/$1', ['filter' => 'Auth']);
$routes->get('/biaya-udang/print/(:segment)',  'JasaVendor\BiayaUdang::print/$1', ['filter' => 'Auth']);
$routes->get('/biaya-udang/all', 'JasaVendor\BiayaUdang::all', ['filter' => 'Auth']);
$routes->get('/biaya-udang/warehouse', 'Purchase\POLokalBahanBaku::dropdownWarehouse', ['filter' => 'Auth']);
$routes->get('/biaya-udang/list-divisi', 'JasaVendor\BiayaUdang::dropdownDivisi', ['filter' => 'Auth']);
$routes->get('/biaya-udang/list-jasa-vendor-in', 'JasaVendor\BiayaUdang::dropdownJasaVendorIn', ['filter' => 'Auth']);
$routes->post('/biaya-udang/autocomplete', 'JasaVendor\BiayaUdang::autoComplete', ['filter' => 'Auth']);
// BIAYA KEPITING
$routes->get('/biaya-kepiting', 'JasaVendor\BiayaKepiting::index', ['filter' => 'Auth']);
$routes->get('/biaya-kepiting/create', 'JasaVendor\BiayaKepiting::create', ['filter' => 'Auth']);
$routes->get('/biaya-kepiting/warehouse', 'Purchase\POLokalBahanBaku::dropdownWarehouse', ['filter' => 'Auth']);
$routes->get('/biaya-kepiting/get-no', 'JasaVendor\BiayaKepiting::getNo', ['filter' => 'Auth']);
$routes->get('/biaya-kepiting/list-barang', 'JasaVendor\BiayaKepiting::dropdownBarang', ['filter' => 'Auth']);
$routes->post('/biaya-kepiting/save', 'JasaVendor\BiayaKepiting::createAction', ['filter' => 'Auth']);
$routes->post('/biaya-kepiting/update', 'JasaVendor\BiayaKepiting::updateAction', ['filter' => 'Auth']);
$routes->post('/biaya-kepiting/delete', 'JasaVendor\BiayaKepiting::delete', ['filter' => 'Auth']);
$routes->get('/biaya-kepiting/id/(:segment)',  'JasaVendor\BiayaKepiting::detail/$1', ['filter' => 'Auth']);
$routes->get('/biaya-kepiting/print/(:segment)',  'JasaVendor\BiayaKepiting::print/$1', ['filter' => 'Auth']);
$routes->get('/biaya-kepiting/all', 'JasaVendor\BiayaKepiting::all', ['filter' => 'Auth']);
$routes->post('/biaya-kepiting/posting', 'JasaVendor\BiayaKepiting::posting', ['filter' => 'Auth']);
$routes->post('/biaya-kepiting/autocomplete', 'JasaVendor\BiayaKepiting::autoComplete', ['filter' => 'Auth']);

// Stuffing Lokal
$routes->get('/pengeluaran-lokal',  'Stuffing\Lokal::index', ['filter' => 'Auth']);
$routes->get('/pengeluaran-lokal/create',  'Stuffing\Lokal::create', ['filter' => 'Auth']);
$routes->get('/pengeluaran-lokal/warehouse', 'Purchase\POLokalBahanBaku::dropdownWarehouse', ['filter' => 'Auth']);
$routes->get('/pengeluaran-lokal/list-barang-stock-init', 'Inventori\StokAdjusment::getListBarangIsInit', ['filter' => 'Auth']);
$routes->get('/pengeluaran-lokal/list-stock-dokumen-bc', 'Inventori\StokAdjusment::getListStockByStockID', ['filter' => 'Auth']);
$routes->get('/pengeluaran-lokal/list-barang-output', 'Stuffing\Lokal::dropdownListOrder', ['filter' => 'Auth']);
$routes->get('/pengeluaran-lokal/all', 'Stuffing\Lokal::all', ['filter' => 'Auth']);
$routes->post('/pengeluaran-lokal/save',  'Stuffing\Lokal::createAction', ['filter' => 'Auth']);
$routes->post('/pengeluaran-lokal/update',  'Stuffing\Lokal::updateAction', ['filter' => 'Auth']);
$routes->post('/pengeluaran-lokal/delete',  'Stuffing\Lokal::delete', ['filter' => 'Auth']);
$routes->post('/pengeluaran-lokal/posting',  'Stuffing\Lokal::posting', ['filter' => 'Auth']);
$routes->post('/pengeluaran-lokal/close',  'Stuffing\Lokal::close', ['filter' => 'Auth']);
$routes->get('/pengeluaran-lokal/id/(:segment)',  'Stuffing\Lokal::detail/$1', ['filter' => 'Auth']);
$routes->get('/pengeluaran-lokal/get-pengeluaran-lokal-no',  'Stuffing\Lokal::getStuffingLokalNo', ['filter' => 'Auth']);
$routes->get('/pengeluaran-lokal/print/(:segment)', 'Stuffing\Lokal::print/$1', ['filter' => 'Auth']);
$routes->get('/pengeluaran-lokal/kemasanAll', 'Stuffing\Lokal::getAllKemasan', ['filter' => 'Auth']);
$routes->post('/pengeluaran-lokal/save-kemasan',  'Stuffing\Lokal::createKemasan', ['filter' => 'Auth']);
$routes->post('/pengeluaran-lokal/delete-kemasan',  'Stuffing\Lokal::deleteKemasan', ['filter' => 'Auth']);


// Stuffing Internasional
$routes->get('/pengeluaran-internasional',  'Stuffing\Internasional::index', ['filter' => 'Auth']);
$routes->get('/pengeluaran-internasional/create',  'Stuffing\Internasional::create', ['filter' => 'Auth']);
$routes->get('/pengeluaran-internasional/warehouse', 'Purchase\POLokalBahanBaku::dropdownWarehouse', ['filter' => 'Auth']);
$routes->get('/pengeluaran-internasional/list-barang-stock-init', 'Inventori\StokAdjusment::getListBarangIsInit', ['filter' => 'Auth']);
$routes->get('/pengeluaran-internasional/list-stock-dokumen-bc', 'Inventori\StokAdjusment::getListStockByStockID', ['filter' => 'Auth']);
$routes->get('/pengeluaran-internasional/list-barang-output', 'Stuffing\Internasional::dropdownListOrder', ['filter' => 'Auth']);
$routes->get('/pengeluaran-internasional/all', 'Stuffing\Internasional::all', ['filter' => 'Auth']);
$routes->post('/pengeluaran-internasional/save',  'Stuffing\Internasional::createAction', ['filter' => 'Auth']);
$routes->post('/pengeluaran-internasional/update',  'Stuffing\Internasional::updateAction', ['filter' => 'Auth']);
$routes->post('/pengeluaran-internasional/delete',  'Stuffing\Internasional::delete', ['filter' => 'Auth']);
$routes->post('/pengeluaran-internasional/posting',  'Stuffing\Internasional::posting', ['filter' => 'Auth']);
$routes->post('/pengeluaran-internasional/close',  'Stuffing\Internasional::close', ['filter' => 'Auth']);
$routes->get('/pengeluaran-internasional/id/(:segment)',  'Stuffing\Internasional::detail/$1', ['filter' => 'Auth']);
$routes->get('/pengeluaran-internasional/get-pengeluaran-internasional-no',  'Stuffing\Internasional::getStuffingLokalNo', ['filter' => 'Auth']);
$routes->get('/pengeluaran-internasional/print/(:segment)', 'Stuffing\Internasional::print/$1', ['filter' => 'Auth']);
$routes->get('/pengeluaran-internasional/kemasanAll', 'Stuffing\Internasional::getAllKemasan', ['filter' => 'Auth']);
$routes->post('/pengeluaran-internasional/save-kemasan',  'Stuffing\Internasional::createKemasan', ['filter' => 'Auth']);
$routes->post('/pengeluaran-internasional/delete-kemasan',  'Stuffing\Internasional::deleteKemasan', ['filter' => 'Auth']);


// STOCK HISTORI
$routes->get('/stock-histori', 'Inventori\StokHistori::index', ['filter' => 'Auth']);
$routes->get('/stock-histori/all', 'Inventori\StokHistori::all', ['filter' => 'Auth']);
// STOK LIST
$routes->get('/stock-list', 'Inventori\StokList::index', ['filter' => 'Auth']);
$routes->get('/stock-list/create', 'Inventori\StokList::create', ['filter' => 'Auth']);
$routes->get('/stock-list/warehouse', 'Purchase\POLokalBahanBaku::dropdownWarehouse', ['filter' => 'Auth']);
$routes->get('/stock-list/get-barang-not-init', 'Inventori\StokList::getListBarangNotInit', ['filter' => 'Auth']);
$routes->post('/stock-list/create', 'Inventori\StokList::createInitStok', ['filter' => 'Auth']);
$routes->get('/stock-list/all', 'Inventori\StokList::all', ['filter' => 'Auth']);
$routes->get('/stock-list/kategori-barang', 'Warehouse\ParentBarang::dropdownKategoriBarang', ['filter' => 'Auth']);
$routes->get('/stock-list/warehouse', 'Purchase\POLokalBahanBaku::dropdownWarehouse', ['filter' => 'Auth']);
$routes->get('/stock-list/id/(:segment)', 'Inventori\StokList::detail/$1', ['filter' => 'Auth']);
$routes->get('/stock-list/stock-dokumen-bc', 'Inventori\StokList::allStokPerDokumen', ['filter' => 'Auth']);
$routes->get('/stock-list/stock-dokumen-supplier', 'Inventori\StokList::allStokPerSupplier', ['filter' => 'Auth']);
$routes->get('/stock-list/stock-filtered', 'Inventori\StokList::allStokFiltered', ['filter' => 'Auth']);
$routes->get('/stock-list/stock-log-pemasukkan-barang-lpb', 'Inventori\StokList::allStokLogPemasukkanBarang', ['filter' => 'Auth']);
$routes->get('/stock-list/stock-log-adjusment', 'Inventori\StokList::allStokAdjusment', ['filter' => 'Auth']);
$routes->get('/stock-list/stock-log-mutasi', 'Inventori\StokList::allStokMutasi', ['filter' => 'Auth']);
$routes->get('/stock-list/stock-log-jasa-vendor', 'Inventori\StokList::allStokJasaVendor', ['filter' => 'Auth']);
$routes->get('/stock-list/stock-log-produksi', 'Inventori\StokList::allStokProduksi', ['filter' => 'Auth']);
$routes->get('/stock-list/stock-log-rebus', 'Inventori\StokList::allStokRebus', ['filter' => 'Auth']);
$routes->get('/stock-list/stock-log-penjualan', 'Inventori\StokList::allStokPenjualan', ['filter' => 'Auth']);
$routes->post('/stock-list/import', 'Inventori\StokList::import', ['filter' => 'Auth']);
$routes->get('/stock-list/export-excel', 'Inventori\StokList::exportExcel', ['filter' => 'Auth']);

// STOK ADJUSMENT
$routes->get('/stock-adjusment', 'Inventori\StokAdjusment::index', ['filter' => 'Auth']);
$routes->get('/stock-adjusment/create', 'Inventori\StokAdjusment::create', ['filter' => 'Auth']);
$routes->get('/stock-adjusment/warehouse', 'Purchase\POLokalBahanBaku::dropdownWarehouse', ['filter' => 'Auth']);
$routes->get('/stock-adjusment/list-barang-stock-init', 'Inventori\StokAdjusment::getListBarangIsInit', ['filter' => 'Auth']);
$routes->get('/stock-adjusment/list-stock-dokumen-bc', 'Inventori\StokAdjusment::getListStockByStockID', ['filter' => 'Auth']);
$routes->get('/stock-adjusment/get-adjusment-no', 'Inventori\StokAdjusment::getAdjusmentNo', ['filter' => 'Auth']);
$routes->post('/stock-adjusment/save', 'Inventori\StokAdjusment::createAction', ['filter' => 'Auth']);
$routes->post('/stock-adjusment/update', 'Inventori\StokAdjusment::updateAction', ['filter' => 'Auth']);
$routes->post('/stock-adjusment/posting', 'Inventori\StokAdjusment::posting', ['filter' => 'Auth']);
$routes->post('/stock-adjusment/delete', 'Inventori\StokAdjusment::delete', ['filter' => 'Auth']);
$routes->get('/stock-adjusment/id/(:segment)', 'Inventori\StokAdjusment::detail/$1', ['filter' => 'Auth']);
$routes->get('/stock-adjusment/all', 'Inventori\StokAdjusment::all', ['filter' => 'Auth']);

// MUTASI
$routes->get('/mutasi', 'Inventori\Mutasi::index', ['filter' => 'Auth']);
$routes->get('/mutasi/create', 'Inventori\Mutasi::create', ['filter' => 'Auth']);
$routes->get('/mutasi/list-divisi-except', 'Inventori\Mutasi::listDivisiExcept', ['filter' => 'Auth']);
$routes->get('/mutasi/warehouse', 'Purchase\POLokalBahanBaku::dropdownWarehouse', ['filter' => 'Auth']);
$routes->get('/mutasi/list-barang-stock-init', 'Inventori\StokAdjusment::getListBarangIsInit', ['filter' => 'Auth']);
$routes->get('/mutasi/get-mutasi-no', 'Inventori\Mutasi::getMutasiNo', ['filter' => 'Auth']);
$routes->get('/mutasi/list-stock-dokumen-bc', 'Inventori\StokAdjusment::getListStockByStockID', ['filter' => 'Auth']);
$routes->post('/mutasi/save', 'Inventori\Mutasi::createAction', ['filter' => 'Auth']);
$routes->post('/mutasi/update', 'Inventori\Mutasi::updateAction', ['filter' => 'Auth']);
$routes->post('/mutasi/delete', 'Inventori\Mutasi::delete', ['filter' => 'Auth']);
$routes->post('/mutasi/posting', 'Inventori\Mutasi::posting', ['filter' => 'Auth']);
$routes->get('/mutasi/id/(:segment)', 'Inventori\Mutasi::detail/$1', ['filter' => 'Auth']);
$routes->get('/mutasi/all', 'Inventori\Mutasi::all', ['filter' => 'Auth']);
$routes->post('/mutasi/un-posting', 'Inventori\Mutasi::unPosting', ['filter' => 'Auth']);
// PENERIMAAN MUTASI PPBKB
$routes->get('/penerimaan-mutasi', 'Inventori\PenerimaanMutasi::index', ['filter' => 'Auth']);
$routes->get('/penerimaan-mutasi/create', 'Inventori\PenerimaanMutasi::create', ['filter' => 'Auth']);
$routes->get('/penerimaan-mutasi/list-warehouse', 'Inventori\PenerimaanMutasi::dropdownListDivisi', ['filter' => 'Auth']);
$routes->get('/penerimaan-mutasi/list-mutasi', 'Inventori\PenerimaanMutasi::dropdownListNomorMutasi', ['filter' => 'Auth']);
$routes->get('/penerimaan-mutasi/list-barang', 'Inventori\PenerimaanMutasi::dropdownListBarang', ['filter' => 'Auth']);
$routes->post('/penerimaan-mutasi/save', 'Inventori\PenerimaanMutasi::createAction', ['filter' => 'Auth']);
$routes->post('/penerimaan-mutasi/update', 'Inventori\PenerimaanMutasi::updateAction', ['filter' => 'Auth']);
$routes->post('/penerimaan-mutasi/posting', 'Inventori\PenerimaanMutasi::posting', ['filter' => 'Auth']);
$routes->post('/penerimaan-mutasi/delete', 'Inventori\PenerimaanMutasi::delete', ['filter' => 'Auth']);
$routes->get('/penerimaan-mutasi/id/(:segment)', 'Inventori\PenerimaanMutasi::detail/$1', ['filter' => 'Auth']);
$routes->get('/penerimaan-mutasi/get-penerimaan-mutasi-no', 'Inventori\PenerimaanMutasi::getPenerimaanMutasiNo', ['filter' => 'Auth']);
$routes->get('/penerimaan-mutasi/all', 'Inventori\PenerimaanMutasi::all', ['filter' => 'Auth']);
$routes->get('/penerimaan-mutasi/print/(:segment)', 'Inventori\PenerimaanMutasi::print/$1', ['filter' => 'Auth']);
// PENERIMAAN MUTASI BC 2.7
$routes->get('/penerimaan-mutasi/global', 'Inventori\PenerimaanMutasiGlobal::index', ['filter' => 'Auth']);
$routes->get('/penerimaan-mutasi/create-global', 'Inventori\PenerimaanMutasiGlobal::create', ['filter' => 'Auth']);
$routes->get('/penerimaan-mutasi/get-penerimaan-mutasi-global-no', 'Inventori\PenerimaanMutasiGlobal::getPenerimaanMutasiNo', ['filter' => 'Auth']);
$routes->get('/penerimaan-mutasi/list-mutasi-global', 'Inventori\PenerimaanMutasiGlobal::dropdownListNomorMutasi', ['filter' => 'Auth']);
$routes->get('/penerimaan-mutasi/list-barang-global', 'Inventori\PenerimaanMutasiGlobal::dropdownListBarang', ['filter' => 'Auth']);
$routes->get('/penerimaan-mutasi/list-barang-masuk', 'Inventori\PenerimaanMutasiGlobal::dropdownListBarangMasuk', ['filter' => 'Auth']);
$routes->post('/penerimaan-mutasi/save-global', 'Inventori\PenerimaanMutasiGlobal::createAction', ['filter' => 'Auth']);
$routes->post('/penerimaan-mutasi/update-global', 'Inventori\PenerimaanMutasiGlobal::updateAction', ['filter' => 'Auth']);
$routes->post('/penerimaan-mutasi/delete-global', 'Inventori\PenerimaanMutasiGlobal::delete', ['filter' => 'Auth']);
$routes->get('/penerimaan-mutasi/all-global', 'Inventori\PenerimaanMutasiGlobal::all', ['filter' => 'Auth']);
$routes->get('/penerimaan-mutasi/id-global/(:segment)', 'Inventori\PenerimaanMutasiGlobal::detail/$1', ['filter' => 'Auth']);
$routes->get('/penerimaan-mutasi/print-global/(:segment)', 'Inventori\PenerimaanMutasiGlobal::print/$1', ['filter' => 'Auth']);
$routes->post('/penerimaan-mutasi/posting-global', 'Inventori\PenerimaanMutasiGlobal::posting', ['filter' => 'Auth']);

// MUTASI GLOBAL
$routes->get('/mutasi/global', 'Inventori\MutasiGlobal::index', ['filter' => 'Auth']);
$routes->get('/mutasi/all-global', 'Inventori\MutasiGlobal::all', ['filter' => 'Auth']);
$routes->get('/mutasi/create-global', 'Inventori\MutasiGlobal::create', ['filter' => 'Auth']);
$routes->get('/mutasi/id-global/(:segment)', 'Inventori\MutasiGlobal::detail/$1', ['filter' => 'Auth']);
$routes->get('/mutasi/get-mutasi-no-global', 'Inventori\MutasiGlobal::getMutasiNo', ['filter' => 'Auth']);
$routes->post('/mutasi/save-global', 'Inventori\MutasiGlobal::createAction', ['filter' => 'Auth']);
$routes->post('/mutasi/update-global', 'Inventori\MutasiGlobal::updateAction', ['filter' => 'Auth']);
$routes->post('/mutasi/delete-global', 'Inventori\MutasiGlobal::delete', ['filter' => 'Auth']);
$routes->post('/mutasi/posting-global', 'Inventori\MutasiGlobal::posting', ['filter' => 'Auth']);
$routes->post('/mutasi/un-posting-global', 'Inventori\MutasiGlobal::unposting', ['filter' => 'Auth']);


// PENERIMAAN BARANG LOKAL BP
$routes->get('/penerimaan-barang-lokal-bp', 'Warehouse\PenerimaanBarangLokalBP::index', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bp/all', 'Warehouse\PenerimaanBarangLokalBP::all', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bp/create', 'Warehouse\PenerimaanBarangLokalBP::create', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-lokal-bp/insert', 'Warehouse\PenerimaanBarangLokalBP::createAction', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bp/generate-po-no', 'Warehouse\PenerimaanBarangLokalBP::generatePONo', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bp/get-po', 'Purchase\POLokalBahanPenolong::dropdownPOBySpp', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bp/get-spp', 'Purchase\POLokalBahanPenolong::dropdownSPPBahanPenolong', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bp/list-barang', 'Warehouse\PenerimaanBarangLokalBP::listBarangLPB', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bp/id/(:segment)', 'Warehouse\PenerimaanBarangLokalBP::update/$1', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-lokal-bp/update', 'Warehouse\PenerimaanBarangLokalBP::updateAction', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bp/print/(:segment)', 'Warehouse\PenerimaanBarangLokalBP::print/$1', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-lokal-bp/delete', 'Warehouse\PenerimaanBarangLokalBP::delete', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-lokal-bp/posting', 'Warehouse\PenerimaanBarangLokalBP::posting', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-lokal-bp/unposting', 'Warehouse\PenerimaanBarangLokalBP::unposting', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bp/get-divisi', 'Warehouse\PenerimaanBarangLokalBP::dropdownDivisiPOLokalBP', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bp/warehouse', 'Purchase\POLokalBahanBaku::dropdownWarehouse', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bp/print-table', 'Warehouse\PenerimaanBarangLokalBP::printTable', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bp/export-excel', 'Warehouse\PenerimaanBarangLokalBP::exportExcel', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bp/load-component', 'Warehouse\PenerimaanBarangLokalBP::loadComponent', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bp/get-supplier-by-spp', 'Warehouse\PenerimaanBarangLokalBP::dropdownSupplierBySPP', ['filter' => 'Auth']);
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
$routes->post('/penerimaan-barang-lokal-bb/unposting', 'Warehouse\PenerimaanBarangLokalBB::unposting', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bb/get-divisi', 'Warehouse\PenerimaanBarangLokalBB::dropdownDivisiPOLokalBB', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bb/warehouse', 'Purchase\POLokalBahanBaku::dropdownWarehouse', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bb/print-table', 'Warehouse\PenerimaanBarangLokalBB::printTable', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-lokal-bb/export-excel', 'Warehouse\PenerimaanBarangLokalBB::exportExcel', ['filter' => 'Auth']);

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
$routes->post('/penerimaan-barang-import-bb/unposting', 'Warehouse\PenerimaanBarangImportBB::unposting', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bb/get-divisi', 'Warehouse\PenerimaanBarangImportBB::dropdownDivisiPOImportBB', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bb/warehouse', 'Purchase\POLokalBahanBaku::dropdownWarehouse', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bb/print-table', 'Warehouse\PenerimaanBarangImportBB::printTable', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bb/export-excel', 'Warehouse\PenerimaanBarangImportBB::exportExcel', ['filter' => 'Auth']);

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
$routes->post('/penerimaan-barang-import-bp/unposting', 'Warehouse\PenerimaanBarangImportBP::unposting', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bp/get-divisi', 'Warehouse\PenerimaanBarangImportBP::dropdownDivisiPOImportBP', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bp/warehouse', 'Purchase\POLokalBahanBaku::dropdownWarehouse', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bp/print-table', 'Warehouse\PenerimaanBarangImportBP::printTable', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import-bp/export-excel', 'Warehouse\PenerimaanBarangImportBP::exportExcel', ['filter' => 'Auth']);
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

// ROUTE BEA CUKAI REVAMP
// SETTING AKUN BEA CUKAI
$routes->get('setting-akun-bc', 'BeaCukai\SettingBeaCukai::index', ['filter' => 'Auth']);
// PENGUSAHA TPB
$routes->get('/setting-akun-bc/pengusaha-tpb', 'BeaCukai\PengusahaTPB::index', ['filter' => 'Auth']);
$routes->post('/setting-akun-bc/pengusaha-tpb/create', 'BeaCukai\PengusahaTPB::create', ['filter' => 'Auth']);
$routes->post('/setting-akun-bc/pengusaha-tpb/update', 'BeaCukai\PengusahaTPB::update', ['filter' => 'Auth']);
$routes->post('/setting-akun-bc/pengusaha-tpb/delete', 'BeaCukai\PengusahaTPB::delete', ['filter' => 'Auth']);
$routes->get('/setting-akun-bc/pengusaha-tpb/get', 'BeaCukai\PengusahaTPB::get', ['filter' => 'Auth']);
$routes->get('/setting-akun-bc/pengusaha-tpb/all', 'BeaCukai\PengusahaTPB::all', ['filter' => 'Auth']);
// NO IZIN TPB SETTING
$routes->get('setting-akun-bc/no-ijin-tpb/(:segment)', 'BeaCukai\NomorIjinTPB::index/$1', ['filter' => 'Auth']);
$routes->get('setting-akun-bc/no-ijin-tpb-all', 'BeaCukai\NomorIjinTPB::all', ['filter' => 'Auth']);
$routes->post('setting-akun-bc/no-ijin-tpb-create', 'BeaCukai\NomorIjinTPB::create', ['filter' => 'Auth']);
$routes->post('setting-akun-bc/no-ijin-tpb-update', 'BeaCukai\NomorIjinTPB::update', ['filter' => 'Auth']);
$routes->post('setting-akun-bc/no-ijin-tpb-delete', 'BeaCukai\NomorIjinTPB::delete', ['filter' => 'Auth']);
$routes->get('setting-akun-bc/no-ijin-tpb-get', 'BeaCukai\NomorIjinTPB::get', ['filter' => 'Auth']);
// INTEGRASI CEISA
$routes->get('/setting-akun-bc/akun', 'BeaCukai\IntegrasiCeisa::index', ['filter' => 'Auth']);
$routes->post('/setting-akun-bc/akun/create-update', 'BeaCukai\IntegrasiCeisa::createOrUpdate', ['filter' => 'Auth']);

// BC 2.3
$routes->group('bea-cukai-bc-23', ['filter' => 'Auth'], function ($routes) {
    $routes->get('/', 'BeaCukai\BC23::index');
    $routes->get('online', 'BeaCukai\BC23::online');
    $routes->get('download-response', 'BeaCukai\BC40::downloadResponPdf');
    $routes->get('all-online', 'BeaCukai\BC23::allOnline');
    $routes->get('all', 'BeaCukai\BC23::all');
    $routes->get('create', 'BeaCukai\BC23::createPurchaseOrderView');
    $routes->get('list-supplier', 'BeaCukai\BC23::dropdownSupplier');
    $routes->get('list-po', 'BeaCukai\BC40::dropdownPO');
    $routes->get('list-no-ijin-tpb', 'BeaCukai\BC40::dropdownNoIjinTPB');
    $routes->get('export-pdf', 'BeaCukai\BC23::exportPdf');
    $routes->get('export-excel', 'BeaCukai\BC40::exportExcel');
    $routes->get('po/(:segment)', 'BeaCukai\BC23::updatePurchaseOrderView/$1');
    $routes->post('create', 'BeaCukai\BC40::createPurchaseOrderAction');
    $routes->post('po/update', 'BeaCukai\BC40::updatePurchaseOrderAction/$1');
    $routes->post('posting', 'BeaCukai\BC23::posting');
    $routes->post('unposting', 'BeaCukai\BC40::unPosting');
    $routes->get('detail-barang/(:segment)', 'BeaCukai\BC23::detailBarang/$1');

    // OUTSTANDING
    $routes->get('bc-23-outstanding-all', 'BeaCukai\BC23::allOutstanding');
    $routes->get('bc-23-outstanding', 'BeaCukai\BC23::viewOutstanding');
    $routes->get('bc-23-outstanding-export', 'BeaCukai\BC23::OutstandingSheet');

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
    $routes->get('id/kemasan-peti-kemas/(:segment)', 'BeaCukai\BC23::createKemasanPetiKemasView/$1');
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
    $routes->get('id/barang/(:segment)/(:segment)/(:segment)', 'BeaCukai\BC23::createBarangDetailView/$1/$2/$3');
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
    $routes->get('api/get-pelabuhan-by-kata', 'BeaCukai\BC23::getPelabuhanByKata');
    $routes->get('api/get-manifest', 'BeaCukai\BC23::getManifest');
    $routes->get('api/get-kontainer-peti-kemas', 'BeaCukai\BC23::getBLKontainerPetiKemas');
    $routes->get('api/get-tps-by-kode-kantor', 'BeaCukai\BC23::getTpsByKodeKantor');
    $routes->get('api/kirim-dokumen/(:segment)', 'BeaCukai\BC23::kirimCeisa/$1');
    $routes->get('satuan-barang', 'BeaCukai\BC23::getKodeSatuanBarang');
    // DELETE & UPDATE NO AJU
    $routes->post('id/delete', 'BeaCukai\BC23::delete');
    $routes->post('id/update-no-aju', 'BeaCukai\BC23::updateNoAju');
});

// BC 4.0
$routes->group('bea-cukai-bc-40', ['filter' => 'Auth'], function ($routes) {
    $routes->get('', 'BeaCukai\BC40::index');
    $routes->get('online', 'BeaCukai\BC40::online');
    $routes->get('download-response', 'BeaCukai\BC40::downloadResponPdf');
    $routes->get('all', 'BeaCukai\BC40::all');
    $routes->get('all-online', 'BeaCukai\BC40::allOnline');
    $routes->get('create', 'BeaCukai\BC40::createPurchaseOrderView');
    $routes->get('list-supplier', 'BeaCukai\BC40::dropdownSupplier');
    $routes->get('list-po', 'BeaCukai\BC40::dropdownPO');
    $routes->get('list-no-ijin-tpb', 'BeaCukai\BC40::dropdownNoIjinTPB');
    $routes->get('export-pdf', 'BeaCukai\BC40::exportPdf');
    $routes->get('export-excel', 'BeaCukai\BC40::exportExcel');
    $routes->get('po/(:segment)', 'BeaCukai\BC40::updatePurchaseOrderView/$1');
    $routes->post('create', 'BeaCukai\BC40::createPurchaseOrderAction');
    $routes->post('po/update', 'BeaCukai\BC40::updatePurchaseOrderAction/$1');
    $routes->post('posting', 'BeaCukai\BC40::posting');
    $routes->post('unposting', 'BeaCukai\BC40::unPosting');
    $routes->get('detail-barang/(:segment)', 'BeaCukai\BC40::detailBarang/$1');

    // OUTSTANDING
    $routes->get('bc-40-outstanding-all', 'BeaCukai\BC40::allOutstanding');
    $routes->get('bc-40-outstanding', 'BeaCukai\BC40::viewOutstanding');
    $routes->get('bc-40-outstanding-export', 'BeaCukai\BC40::OutstandingSheet');

    // FORM PURCHASE ORDER
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
    $routes->get('id/barang/(:segment)/(:segment)/(:segment)', 'BeaCukai\BC40::createBarangDetailView/$1/$2/$3');
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
    $routes->post('id/update-no-aju-bulk', 'BeaCukai\BC40::updateNoAjuBulk');
});

// BC 2.7
$routes->group('bea-cukai-bc-27', ['filter' => 'Auth'], function ($routes) {
    $routes->get('/', 'BeaCukai\BC27::index');
    $routes->get('all', 'BeaCukai\BC27::all');
    $routes->get('online', 'BeaCukai\BC27::online');
    $routes->get('download-response', 'BeaCukai\BC40::downloadResponPdf');
    $routes->get('all-online', 'BeaCukai\BC27::allOnline');
    $routes->get('create', 'BeaCukai\BC27::create');
    $routes->get('id/(:segment)', 'BeaCukai\BC27::detail/$1');
    $routes->post('save', 'BeaCukai\BC27::createAction');
    $routes->post('update', 'BeaCukai\BC27::updateAction');
    $routes->post('delete', 'BeaCukai\BC27::delete');
    $routes->post('posting', 'BeaCukai\BC27::posting');
    $routes->get('check-no-aju', 'BeaCukai\BC27::checkNoAju');
    $routes->get('divisi', 'BeaCukai\BC27::dropdownDivisiByCompany');

    $routes->get('list-mutasi-global', 'BeaCukai\BC27::dropdownMutasiGlobal');
    $routes->get('list-barang-mutasi', 'BeaCukai\BC27::getListMutasiDetail');

    //header
    $routes->get('id/header/(:segment)', 'BeaCukai\BC27::header/$1');
    $routes->post('id/header', 'BeaCukai\BC27::updateHeader');

    //ENTITAS

    $routes->get('id/entitas/(:segment)', 'BeaCukai\BC27::entitas/$1');
    $routes->post('id/entitas', 'BeaCukai\BC27::updateEntitas');

    // DOKUMEN
    $routes->get('id/dokumen/(:segment)', 'BeaCukai\BC27::dokumen/$1');
    $routes->post('id/dokumen', 'BeaCukai\BC27::updateDokumen');
    $routes->post('id/dokumen/delete', 'BeaCukai\BC27::deleteDokumen');

    // PENGANGKUT
    $routes->get('id/pengangkut/(:segment)', 'BeaCukai\BC27::pengangkut/$1');
    $routes->post('id/pengangkut', 'BeaCukai\BC27::pengangkutUpdate');

    // KEMASAN & PETI KEMASAN
    $routes->get('id/kemasan-peti-kemas/(:segment)', 'BeaCukai\BC27::kemasanPetiKemas/$1');
    $routes->post('id/kemasan-peti-kemas/kemasan', 'BeaCukai\BC27::kemasanUpdate');
    $routes->post('id/kemasan-peti-kemas/kemasan/delete', 'BeaCukai\BC27::deleteKemasan');
    $routes->post('id/kemasan-peti-kemas/kontainer', 'BeaCukai\BC27::kontainerUpdate');
    $routes->post('id/kemasan-peti-kemas/kontainer/delete', 'BeaCukai\BC27::deleteKontainer');

    // TRANSAKSI
    $routes->get('id/transaksi/(:segment)', 'BeaCukai\BC27::transaksi/$1');
    $routes->post('id/transaksi', 'BeaCukai\BC27::transaksiUpdate');
    // BARANG
    $routes->get('id/barang/(:segment)', 'BeaCukai\BC27::barang/$1');
    $routes->get('id/barang/(:segment)/(:segment)', 'BeaCukai\BC27::barangDetail/$1/$2');
    $routes->post('id/barang', 'BeaCukai\BC27::barangDetailUpdate');

    // BAHAN BAKU
    $routes->post('id/barang/bahan-baku-create', 'BeaCukai\BC27::bahanBakuUpdate');
    $routes->post('id/barang/bahan-baku-delete', 'BeaCukai\BC27::bahanBakuDelete');
    //PUNGUTAN
    $routes->get('id/pungutan/(:segment)', 'BeaCukai\BC27::pungutan/$1');
    $routes->post('id/pungutan/generate', 'BeaCukai\BC27::generatePungutan');

    // PERYATAAN
    $routes->get('id/pernyataan/(:segment)', 'BeaCukai\BC27::pernyataan/$1');
    $routes->post('id/pernyataan', 'BeaCukai\BC27::pernyataanUpdate');

    //kirim ceisa
    $routes->get('api/kirim-dokumen/(:segment)', 'BeaCukai\BC27::kirimCeisa/$1');
    // OUTSTANDING
    $routes->get('bc-27-outstanding-all', 'BeaCukai\BC27::allOutstanding');
    $routes->get('bc-27-outstanding', 'BeaCukai\BC27::viewOutstanding');
    $routes->get('bc-27-outstanding-export', 'BeaCukai\BC27::OutstandingSheet');
});

// BC 3.0
$routes->group('bea-cukai-bc-30', ['filter' => 'Auth'], function ($routes) {
    $routes->get('/', 'BeaCukai\BC30::index');
    $routes->get('all', 'BeaCukai\BC30::all');
    $routes->get('online', 'BeaCukai\BC30::online');
    $routes->get('download-response', 'BeaCukai\BC40::downloadResponPdf');
    $routes->get('all-online', 'BeaCukai\BC30::allOnline');
    $routes->get('create', 'BeaCukai\BC30::create');
    $routes->get('id/(:segment)', 'BeaCukai\BC30::detail/$1');
    $routes->post('save', 'BeaCukai\BC30::createAction');
    $routes->post('update', 'BeaCukai\BC30::updateAction');
    $routes->post('delete', 'BeaCukai\BC30::delete');
    $routes->post('posting', 'BeaCukai\BC30::posting');
    $routes->get('check-no-aju', 'BeaCukai\BC30::checkNoAju');

    $routes->get('list-barang', 'BeaCukai\BC30::getListBarang');
    $routes->get('list-sales-order', 'BeaCukai\BC30::dropdownSalesOrder');

    //HEADER
    $routes->get('id/header/(:segment)', 'BeaCukai\BC30::header/$1');
    $routes->post('id/header', 'BeaCukai\BC30::updateHeader');

    //ENTITAS   
    $routes->get('id/entitas/(:segment)', 'BeaCukai\BC30::entitas/$1');
    $routes->post('id/entitas', 'BeaCukai\BC30::updateEntitas');
    $routes->post('id/entitas/pemilik', 'BeaCukai\BC30::updateEntitasPemilik');
    $routes->post('id/entitas/pemilik-delete', 'BeaCukai\BC30::deleteEntitasPemilik');

    // DOKUMEN
    $routes->get('id/dokumen/(:segment)', 'BeaCukai\BC30::dokumen/$1');
    $routes->post('id/dokumen', 'BeaCukai\BC30::updateDokumen');
    $routes->post('id/dokumen/delete', 'BeaCukai\BC30::deleteDokumen');

    // PENGANGKUT
    $routes->get('id/pengangkut/(:segment)', 'BeaCukai\BC30::pengangkut/$1');
    $routes->post('id/pengangkut', 'BeaCukai\BC30::pengangkutUpdate');
    $routes->post('id/pengangkut-insert-table', 'BeaCukai\BC30::createPengangkutanAction');
    $routes->post('id/pengangkut-delete-table', 'BeaCukai\BC30::deletePengangkutAction');

    // KEMASAN & PETI KEMASAN
    $routes->get('id/kemasan-peti-kemas/(:segment)', 'BeaCukai\BC30::kemasanPetiKemas/$1');
    $routes->post('id/kemasan-peti-kemas/kemasan', 'BeaCukai\BC30::kemasanUpdate');
    $routes->post('id/kemasan-peti-kemas/kemasan/delete', 'BeaCukai\BC30::deleteKemasan');
    $routes->post('id/kemasan-peti-kemas/kontainer', 'BeaCukai\BC30::kontainerUpdate');
    $routes->post('id/kemasan-peti-kemas/kontainer/delete', 'BeaCukai\BC30::deleteKontainer');

    // TRANSAKSI
    $routes->get('id/transaksi/(:segment)', 'BeaCukai\BC30::transaksi/$1');
    $routes->post('id/transaksi', 'BeaCukai\BC30::transaksiUpdate');
    $routes->post('id/transaksi/bankDevisa', 'BeaCukai\BC30::saveBankDevisa');
    $routes->post('id/transaksi/bank-devisa-delete', 'BeaCukai\BC30::deleteBankDevisa');

    // BARANG
    $routes->get('id/barang/(:segment)', 'BeaCukai\BC30::barang/$1');
    $routes->get('id/barang/(:segment)/(:segment)', 'BeaCukai\BC30::barangDetail/$1/$2');
    $routes->post('id/barang', 'BeaCukai\BC30::barangDetailUpdate');
    // BARANG DOKUMEN
    $routes->post('id/barang/dokumen-create', 'BeaCukai\BC30::createDokumenBarangDetail');
    $routes->post('id/barang/dokumen-delete', 'BeaCukai\BC30::deleteDokumenBarangDetail');
    // BARANG ENTITAS
    $routes->post('id/barang/entitas-create', 'BeaCukai\BC30::createEntitasBarangDetail');
    $routes->post('id/barang/entitas-delete', 'BeaCukai\BC30::deleteEntitasBarangDetail');
    //KESIAPAN BARNAG
    $routes->get('id/kesiapan-barang/(:segment)', 'BeaCukai\BC30::kesiapanBarang/$1');
    $routes->post('id/kesiapan-barang', 'BeaCukai\BC30::kesiapanBarangUpdate');
    // PUNGUTAN
    $routes->get('id/pungutan/(:segment)', 'BeaCukai\BC30::pungutan/$1');
    // PERYATAAN
    $routes->get('id/pernyataan/(:segment)', 'BeaCukai\BC30::pernyataan/$1');
    $routes->post('id/pernyataan', 'BeaCukai\BC30::pernyataanUpdate');

    //KIRIM CEISA
    $routes->get('api/kirim-dokumen/(:segment)', 'BeaCukai\BC30::kirimCeisa/$1');
    // PERYATAAN
    $routes->get('id/pernyataan/(:segment)', 'BeaCukai\BC30::pernyataan/$1');
    $routes->post('id/pernyataan', 'BeaCukai\BC30::pernyataanUpdate');

    // OUTSTANDING
    $routes->get('bc-30-outstanding-all', 'BeaCukai\BC30::allOutstanding');
    $routes->get('bc-30-outstanding', 'BeaCukai\BC30::viewOutstanding');
    $routes->get('bc-30-outstanding-export', 'BeaCukai\BC30::OutstandingSheet');
});

// PPBKB
$routes->group('bea-cukai-ppbkb', ['filter' => 'Auth'], function ($routes) {
    $routes->get('/', 'BeaCukai\PPBKB::index');
    $routes->get('all', 'BeaCukai\PPBKB::all');
    $routes->get('create', 'BeaCukai\PPBKB::create');
    $routes->post('save', 'BeaCukai\PPBKB::createAction');
    $routes->post('update', 'BeaCukai\PPBKB::updateAction');
    $routes->get('id/(:segment)', 'BeaCukai\PPBKB::detail/$1');
    $routes->post('posting', 'BeaCukai\PPBKB::posting');
    $routes->post('delete', 'BeaCukai\PPBKB::delete');
    $routes->get('print/(:segment)', 'BeaCukai\PPBKB::print/$1');

    $routes->get('list-mutasi', 'BeaCukai\PPBKB::dropdownMutasi');
    $routes->get('list-no-ijin-tpb', 'BeaCukai\PPBKB::dropdownNoIjinTPB');
    $routes->get('list-barang-mutasi', 'BeaCukai\PPBKB::getListMutasiDetail');
    $routes->get('get-no', 'BeaCukai\PPBKB::getNo');

    // OUTSTANDING
    $routes->get('ppbkb-outstanding-all', 'BeaCukai\PPBKB::allOutstanding');
    $routes->get('ppbkb-outstanding', 'BeaCukai\PPBKB::viewOutstanding');
    $routes->get('ppbkb-outstanding-export', 'BeaCukai\PPBKB::OutstandingSheet');
});

// BC 2.5
$routes->group('bea-cukai-bc-25', ['filter' => 'Auth'], function ($routes) {
    $routes->get('/', 'BeaCukai\BC25::index');
    $routes->get('create', 'BeaCukai\BC25::create');
    $routes->get('all', 'BeaCukai\BC25::all');
    $routes->get('online', 'BeaCukai\BC25::online');
    $routes->get('download-response', 'BeaCukai\BC40::downloadResponPdf');
    $routes->get('all-online', 'BeaCukai\BC25::allOnline');
    $routes->get('id/(:segment)', 'BeaCukai\BC25::detail/$1');
    $routes->post('save', 'BeaCukai\BC25::createAction');
    $routes->post('update', 'BeaCukai\BC25::updateAction');
    $routes->post('delete', 'BeaCukai\BC25::delete');
    $routes->post('posting', 'BeaCukai\BC25::posting');
    $routes->get('check-no-aju', 'BeaCukai\BC25::checkNoAju');
    $routes->get('list-reference', 'BeaCukai\BC25::getReference');
    $routes->get('list-reference-detail', 'BeaCukai\BC25::getDetailReference');
    // HEADER
    $routes->get('id/header/(:segment)', 'BeaCukai\BC25::header/$1');
    $routes->post('id/header', 'BeaCukai\BC25::updateHeader');
    // ENTITAS
    $routes->get('id/entitas/(:segment)', 'BeaCukai\BC25::entitas/$1');
    $routes->post('id/entitas', 'BeaCukai\BC25::updateEntitas');
    // DOKUMEN
    $routes->get('id/dokumen/(:segment)', 'BeaCukai\BC25::dokumen/$1');
    $routes->post('id/dokumen', 'BeaCukai\BC25::updateDokumen');
    $routes->post('id/dokumen/delete', 'BeaCukai\BC25::deleteDokumen');
    // PENGANGKUT
    $routes->get('id/pengangkut/(:segment)', 'BeaCukai\BC25::pengangkut/$1');
    $routes->post('id/pengangkut', 'BeaCukai\BC25::pengangkutUpdate');
    // KEMASAN & PETI KEMASAN
    $routes->get('id/kemasan-peti-kemas/(:segment)', 'BeaCukai\BC25::kemasanPetiKemas/$1');
    $routes->post('id/kemasan-peti-kemas/kemasan', 'BeaCukai\BC25::kemasanUpdate');
    $routes->post('id/kemasan-peti-kemas/kemasan/delete', 'BeaCukai\BC25::deleteKemasan');
    $routes->post('id/kemasan-peti-kemas/kontainer', 'BeaCukai\BC25::kontainerUpdate');
    $routes->post('id/kemasan-peti-kemas/kontainer/delete', 'BeaCukai\BC25::deleteKontainer');
    // TRANSAKSI
    $routes->get('id/transaksi/(:segment)', 'BeaCukai\BC25::transaksi/$1');
    $routes->post('id/transaksi', 'BeaCukai\BC25::transaksiUpdate');
    // BARANG
    $routes->get('id/barang/(:segment)', 'BeaCukai\BC25::barang/$1');
    $routes->get('id/barang/(:segment)/(:segment)', 'BeaCukai\BC25::barangDetail/$1/$2');
    $routes->post('id/barang', 'BeaCukai\BC25::barangDetailUpdate');
    // BARANG DOKUMEN
    $routes->post('id/barang/dokumen-create', 'BeaCukai\BC25::createDokumenBarangDetail');
    $routes->post('id/barang/dokumen-delete', 'BeaCukai\BC25::deleteDokumenBarangDetail');
    // BARANG PUNGUTAN
    $routes->post('id/barang/pungutan-create', 'BeaCukai\BC25::createPungutanDetailBarang');
    $routes->post('id/barang/pungutan-delete', 'BeaCukai\BC25::deletePungutanDetailBarang');
    // BAHAN BAKU
    $routes->post('id/barang/bahan-baku-create', 'BeaCukai\BC25::bahanBakuUpdate');
    $routes->post('id/barang/bahan-baku-delete', 'BeaCukai\BC25::bahanBakuDelete');
    // PUNGUTAN
    $routes->get('id/pungutan/(:segment)', 'BeaCukai\BC25::pungutan/$1');
    // PERYATAAN
    $routes->get('id/pernyataan/(:segment)', 'BeaCukai\BC25::pernyataan/$1');
    $routes->post('id/pernyataan', 'BeaCukai\BC25::pernyataanUpdate');
    // KIRIM CEISA
    $routes->get('api/kirim-dokumen/(:segment)', 'BeaCukai\BC25::kirimCeisa/$1');
    // DROPDOWN
    $routes->get('list-bahan-baku-asal', 'BeaCukai\BC25::dropdownBahanBakuAsal');
    $routes->get('list-payload-barang', 'BeaCukai\BC25::dropdownDetailPayload');
    // OUTSTANDING
    $routes->get('bc-25-outstanding-all', 'BeaCukai\BC25::allOutstanding');
    $routes->get('bc-25-outstanding', 'BeaCukai\BC25::viewOutstanding');
    $routes->get('bc-25-outstanding-export', 'BeaCukai\BC25::OutstandingSheet');
});

// BC 4.1
$routes->group('bea-cukai-bc-41', ['filter' => 'Auth'], function ($routes) {
    $routes->get('/', 'BeaCukai\BC41::index');
    $routes->get('create', 'BeaCukai\BC41::create');
    $routes->get('all', 'BeaCukai\BC41::all');
    $routes->get('online', 'BeaCukai\BC41::online');
    $routes->get('download-response', 'BeaCukai\BC40::downloadResponPdf');
    $routes->get('all-online', 'BeaCukai\BC41::allOnline');
    $routes->get('id/(:segment)', 'BeaCukai\BC41::detail/$1');
    $routes->post('save', 'BeaCukai\BC41::createAction');
    $routes->post('update', 'BeaCukai\BC41::updateAction');
    $routes->post('delete', 'BeaCukai\BC41::delete');
    $routes->post('posting', 'BeaCukai\BC41::posting');
    $routes->get('check-no-aju', 'BeaCukai\BC41::checkNoAju');
    $routes->get('list-reference', 'BeaCukai\BC41::getReference');
    $routes->get('list-reference-detail', 'BeaCukai\BC41::getDetailReference');
    // OUTSTANDING
    $routes->get('bc-41-outstanding-all', 'BeaCukai\BC41::allOutstanding');
    $routes->get('bc-41-outstanding', 'BeaCukai\BC41::viewOutstanding');
    $routes->get('bc-41-outstanding-export', 'BeaCukai\BC41::OutstandingSheet');
    // HEADER
    $routes->get('id/header/(:segment)', 'BeaCukai\BC41::header/$1');
    $routes->post('id/header', 'BeaCukai\BC41::updateHeader');
    // ENTITAS
    $routes->get('id/entitas/(:segment)', 'BeaCukai\BC41::entitas/$1');
    $routes->post('id/entitas', 'BeaCukai\BC41::updateEntitas');
    // DOKUMEN
    $routes->get('id/dokumen/(:segment)', 'BeaCukai\BC41::dokumen/$1');
    $routes->post('id/dokumen', 'BeaCukai\BC41::updateDokumen');
    $routes->post('id/dokumen/delete', 'BeaCukai\BC41::deleteDokumen');
    // PENGANGKUT
    $routes->get('id/pengangkut/(:segment)', 'BeaCukai\BC41::pengangkut/$1');
    $routes->post('id/pengangkut', 'BeaCukai\BC41::pengangkutUpdate');
    // KEMASAN & PETI KEMASAN
    $routes->get('id/kemasan-peti-kemas/(:segment)', 'BeaCukai\BC41::kemasanPetiKemas/$1');
    $routes->post('id/kemasan-peti-kemas/kemasan', 'BeaCukai\BC41::kemasanUpdate');
    $routes->post('id/kemasan-peti-kemas/kemasan/delete', 'BeaCukai\BC41::deleteKemasan');
    $routes->post('id/kemasan-peti-kemas/kontainer', 'BeaCukai\BC41::kontainerUpdate');
    $routes->post('id/kemasan-peti-kemas/kontainer/delete', 'BeaCukai\BC41::deleteKontainer');
    // TRANSAKSI
    $routes->get('id/transaksi/(:segment)', 'BeaCukai\BC41::transaksi/$1');
    $routes->post('id/transaksi', 'BeaCukai\BC41::transaksiUpdate');
    // BARANG
    $routes->get('id/barang/(:segment)', 'BeaCukai\BC41::barang/$1');
    $routes->get('id/barang/(:segment)/(:segment)', 'BeaCukai\BC41::barangDetail/$1/$2');
    $routes->post('id/barang', 'BeaCukai\BC41::barangDetailUpdate');
    // BAHAN BAKU
    $routes->post('id/barang/bahan-baku-create', 'BeaCukai\BC41::bahanBakuUpdate');
    $routes->post('id/barang/bahan-baku-delete', 'BeaCukai\BC41::bahanBakuDelete');
    // PUNGUTAN
    $routes->get('id/pungutan/(:segment)', 'BeaCukai\BC41::pungutan/$1');
    $routes->post('id/pungutan', 'BeaCukai\BC41::pungutanUpdate');
    // PERYATAAN
    $routes->get('id/pernyataan/(:segment)', 'BeaCukai\BC41::pernyataan/$1');
    $routes->post('id/pernyataan', 'BeaCukai\BC41::pernyataanUpdate');
    // KIRIM CEISA
    $routes->get('api/kirim-dokumen/(:segment)', 'BeaCukai\BC41::kirimCeisa/$1');
});

// HUMAN RESOURCE
// JAM KERJA KARYAWAN
$routes->get('/employee/jam-kerja/(:segment)', 'HR\EmployeeJamKerja::index/$1', ['filter' => 'Auth']);
$routes->get('/employee/get-jam-kerja-detail', 'HR\EmployeeJamKerja::getDetailJamKerja', ['filter' => 'Auth']);
$routes->post('/employee/update-jam-kerja', 'HR\EmployeeJamKerja::createOrUpdate', ['filter' => 'Auth']);

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
$routes->get('/payroll/getBagian', 'HR\Payroll::getBagian', ['filter' => 'Auth']);
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
$routes->post('/jurnal/generate-no-bukti', 'Accounting\JurnalUmum\JurnalUmum::generateNoBukti', ['filter' => 'Auth']);
// $routes->post('/jurnal/addJurnal', 'Accounting\JurnalUmum\JurnalUmum::save', ['filter' => 'Auth']);
$routes->post('/jurnal/import', 'Accounting\JurnalUmum\JurnalUmum::import', ['filter' => 'Auth']);
$routes->post('/jurnal/getSubAkuns', 'Accounting\JurnalUmum\JurnalUmum::searchSubAkun', ['filter' => 'Auth']);
$routes->post('/jurnal/getSubAkunsExact', 'Accounting\JurnalUmum\JurnalUmum::searchSubAkunExact', ['filter' => 'Auth']);
$routes->get('/jurnal/all', 'Accounting\JurnalUmum\JurnalUmum::all', ['filter' => 'Auth']);
$routes->get('/jurnal/create', 'Accounting\JurnalUmum\JurnalUmum::create', ['filter' => 'Auth']);
$routes->post('/jurnal/save', 'Accounting\JurnalUmum\JurnalUmum::store', ['filter' => 'Auth']);
$routes->post('/jurnal/update', 'Accounting\JurnalUmum\JurnalUmum::update', ['filter' => 'Auth']);
$routes->get('/jurnal/id/(:segment)', 'Accounting\JurnalUmum\JurnalUmum::detail/$1', ['filter' => 'Auth']);
$routes->post('/jurnal/delete', 'Accounting\JurnalUmum\JurnalUmum::delete', ['filter' => 'Auth']);
$routes->get('/jurnal/print/(:segment)', 'Accounting\JurnalUmum\JurnalUmum::print/$1', ['filter' => 'Auth']);
$routes->get('/jurnal/print-excel', 'Accounting\JurnalUmum\JurnalUmum::exportExcel', ['filter' => 'Auth']);
$routes->get('/jurnal/print-pdf', 'Accounting\JurnalUmum\JurnalUmum::exportPdf', ['filter' => 'Auth']);

// set no bukti
$routes->get('/no-bukti', 'Accounting\NoBuktiAccounting\NoBukti::index', ['filter' => 'Auth']);
$routes->get('/no-bukti/all', 'Accounting\NoBuktiAccounting\NoBukti::allNoBukti', ['filter' => 'Auth']);
$routes->post('/no-bukti/save', 'Accounting\NoBuktiAccounting\NoBukti::saveNoBukti', ['filter' => 'Auth']);
$routes->get('/no-bukti/id/(:segment)', 'Accounting\NoBuktiAccounting\NoBukti::getByIdNoBukti/$1', ['filter' => 'Auth']);
$routes->post('/no-bukti/update', 'Accounting\NoBuktiAccounting\NoBukti::updateNoBukti', ['filter' => 'Auth']);
$routes->post('/no-bukti/delete', 'Accounting\NoBuktiAccounting\NoBukti::deleteNoBukti', ['filter' => 'Auth']);
//Jurnal Penyesuaian
// $routes->get('/jurnal-penyesuaian', 'Accounting\JurnalPenyesuaian\JurnalPenyesuaian::index', ['filter' => 'Auth']);
// $routes->post('/jurnal-penyesuaian/addJurnal', 'Accounting\JurnalPenyesuaian\JurnalPenyesuaian::save', ['filter' => 'Auth']);
// $routes->post('/jurnal-penyesuaian/getSubAkuns', 'Accounting\JurnalPenyesuaian\JurnalPenyesuaian::searchSubAkun', ['filter' => 'Auth']);
// $routes->post('/jurnal-penyesuaian/getNoBukti', 'Accounting\JurnalPenyesuaian\JurnalPenyesuaian::searchNoBukti', ['filter' => 'Auth']);
// $routes->post('/jurnal-penyesuaian/generate-no-bukti', 'Accounting\JurnalPenyesuaian\JurnalPenyesuaian::generateNoBukti', ['filter' => 'Auth']);
//Jurnal Update
$routes->get('/jurnal/update/(:segment)', 'Accounting\JurnalPenyesuaian\JurnalUpdate::index/$1', ['filter' => 'Auth']);
$routes->post('/jurnal/update/updateJurnal', 'Accounting\JurnalPenyesuaian\JurnalUpdate::save', ['filter' => 'Auth']);
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
//Setting Akun Costing
$routes->get('/setting-akun-costing', 'Accounting\SettingAkunCosting\SettingAkunCostingController::index', ['filter' => 'Auth']);
$routes->get('/setting-akun-costing/all', 'Accounting\SettingAkunCosting\SettingAkunCostingController::all', ['filter' => 'Auth']);
$routes->post('/setting-akun-costing/get', 'Accounting\SettingAkunCosting\SettingAkunCostingController::get', ['filter' => 'Auth']);
$routes->post('/setting-akun-costing/save', 'Accounting\SettingAkunCosting\SettingAkunCostingController::saveCosting', ['filter' => 'Auth']);
//Rasio
$routes->get('/rasio', 'Accounting\Rasio\RasioController::index', ['filter' => 'Auth']);
$routes->get('/rasio/create', 'Accounting\Rasio\RasioController::createRasio', ['filter' => 'Auth']);
$routes->get('/rasio/get-barang-digunakan', 'Accounting\Rasio\RasioController::getRasioBarangDigunakan', ['filter' => 'Auth']);
$routes->get('/rasio/get-barang-digunakan-jadi', 'Accounting\Rasio\RasioController::getRasioBarangDigunakanJadi', ['filter' => 'Auth']);
$routes->get('/rasio/get-saldo-akhir', 'Accounting\Rasio\RasioController::getSaldoAkhir', ['filter' => 'Auth']);
$routes->get('/rasio/get-saldo-awal', 'Accounting\Rasio\RasioController::getSaldoAwal', ['filter' => 'Auth']);
$routes->get('/rasio/get-saldo-adjusment', 'Accounting\Rasio\RasioController::getSaldoAdjusment', ['filter' => 'Auth']);
$routes->get('/rasio/get-saldo-jual', 'Accounting\Rasio\RasioController::getSaldoJual', ['filter' => 'Auth']);
$routes->get('/rasio/get-saldo-trimming', 'Accounting\Rasio\RasioController::getSaldoTrimming', ['filter' => 'Auth']);
$routes->get('/rasio/get-barang-jadi', 'Accounting\Rasio\RasioController::getRasioBarangJadi', ['filter' => 'Auth']);

$routes->get('/rasio/get-material-i', 'Accounting\Rasio\RasioController::getRawMaterialI', ['filter' => 'Auth']);

$routes->get('/rasio/get-barang-digunakan-penolong', 'Accounting\Rasio\RasioController::getRasioBarangDigunakanPenolong', ['filter' => 'Auth']);
$routes->get('/rasio/get-jurnal', 'Accounting\Rasio\RasioController::getDataJurnal', ['filter' => 'Auth']);
$routes->get('/rasio/get-cost', 'Accounting\Rasio\RasioController::getCost', ['filter' => 'Auth']);
$routes->get('/rasio/all', 'Accounting\Rasio\RasioController::allRasio', ['filter' => 'Auth']);
$routes->get('/rasio/id/(:segment)', 'Accounting\Rasio\RasioController::getById/$1', ['filter' => 'Auth']);
$routes->post('/rasio/save', 'Accounting\Rasio\RasioController::saveRasio', ['filter' => 'Auth']);
$routes->post('/rasio/update', 'Accounting\Rasio\RasioController::updateRasio', ['filter' => 'Auth']);
$routes->post('/rasio/delete', 'Accounting\Rasio\RasioController::deleteRasio', ['filter' => 'Auth']);
$routes->post('/rasio/get', 'Accounting\Rasio\RasioController::get', ['filter' => 'Auth']);
$routes->get('/rasio/load_content', 'Accounting\Rasio\RasioController::load_content', ['filter' => 'Auth']);

// Tutup Buku
$routes->get('/tutup-buku', 'Accounting\TutupBuku\TutupBukuController::index', ['filter' => 'Auth']);
$routes->get('/tutup-buku/all', 'Accounting\TutupBuku\TutupBukuController::all', ['filter' => 'Auth']);
$routes->post('/tutup-buku/save', 'Accounting\TutupBuku\TutupBukuController::save', ['filter' => 'Auth']);
//Laporan
//Accounting
$routes->get('/laporan-accounting', 'Laporan\Accounting\Accounting::index', ['filter' => 'Auth']);

$routes->get('/laporan-accounting/pembelian', 'Laporan\Accounting\Pembelian::index', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/pembelian/all', 'Laporan\Accounting\Pembelian::allTransaksi', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/pembelian/printPDF/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\Accounting\Pembelian::LaporanPembelianPrint/$1/$2/$3/$4', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/pembelian/printExcel/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\Accounting\Pembelian::exportExcel/$1/$2/$3/$4', ['filter' => 'Auth']);

$routes->get('/laporan-accounting/penjualan', 'Laporan\Accounting\Penjualan::index', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/penjualan/all', 'Laporan\Accounting\Penjualan::allTransaksi', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/penjualan/printPDF/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\Accounting\Penjualan::LaporanPenjualanPrint/$1/$2/$3/$4', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/penjualan/printExcel/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\Accounting\Penjualan::exportExcel/$1/$2/$3/$4', ['filter' => 'Auth']);

$routes->get('/laporan-accounting/hutang', 'Laporan\Accounting\Hutang::index', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/hutang/all', 'Laporan\Accounting\Hutang::allHutang', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/hutang/details/(:segment)', 'Laporan\Accounting\Hutang::detail/$1', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/hutang/details/invoice/(:segment)', 'Laporan\Accounting\Hutang::allDetailsInvoice/$1', ['filter' => 'Auth']);
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
$routes->post('/laporan-accounting/bukubesar/printPDF', 'Laporan\Accounting\BukuBesar::exportPDF', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/bukubesar/printExcel/(:segment)/(:segment)/(:segment)', 'Laporan\Accounting\BukuBesar::exportExcel/$1/$2/$3', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/bukubesar/dropdown-account', 'Laporan\Accounting\BukuBesar::dropdownAccount', ['filter' => 'Auth']);

$routes->get('/laporan-accounting/jurnalumum', 'Laporan\Accounting\JurnalUmum::index', ['filter' => 'Auth']);
$routes->post('/laporan-accounting/jurnalumum', 'Laporan\Accounting\JurnalUmum::index', ['filter' => 'Auth']);
// $routes->post('/laporan-accounting/jurnalumum/getAll', 'Laporan\Accounting\JurnalUmum::getAll', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/jurnalumum/import', 'Laporan\Accounting\JurnalUmum::import', ['filter' => 'Auth']);
$routes->post('/laporan-accounting/jurnalumum/import', 'Laporan\Accounting\JurnalUmum::import', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/jurnalumum/printPDF/(:segment)/(:segment)/(:segment)', 'Laporan\Accounting\JurnalUmum::exportPDF/$1/$2/$3', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/jurnalumum/printExcel/(:segment)/(:segment)/(:segment)', 'Laporan\Accounting\JurnalUmum::exportExcel/$1/$2/$3', ['filter' => 'Auth']);

$routes->get('/laporan-accounting/neracasaldo', 'Laporan\Accounting\NeracaSaldo::index', ['filter' => 'Auth']);
$routes->post('/laporan-accounting/neracasaldo', 'Laporan\Accounting\NeracaSaldo::index', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/neracasaldo/printPDF/(:segment)/(:segment)', 'Laporan\Accounting\NeracaSaldo::exportPDF/$1/$2', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/neracasaldo/printExcel/(:segment)/(:segment)', 'Laporan\Accounting\NeracaSaldo::exportExcel/$1/$2', ['filter' => 'Auth']);

$routes->get('/laporan-accounting/rekap-kopek', 'Laporan\Accounting\RekapKopek::index', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/rekap-kopek/all', 'Laporan\Accounting\RekapKopek::allTransaksi', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/rekap-kopek/printPDF/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\Accounting\RekapKopek::LaporanKopekPrint/$1/$2/$3/$4', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/rekap-kopek/printExcel/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\Accounting\RekapKopek::exportExcel/$1/$2/$3/$4', ['filter' => 'Auth']);

$routes->get('/laporan-accounting/costing', 'Laporan\Accounting\Costing::index', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/costing/getdata', 'Laporan\Accounting\Costing::getCostingData', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/costing/printPDF/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\Accounting\RekapKopek::LaporanKopekPrint/$1/$2/$3/$4', ['filter' => 'Auth']);
$routes->get('/laporan-accounting/costing/printExcel/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\Accounting\RekapKopek::exportExcel/$1/$2/$3/$4', ['filter' => 'Auth']);
// Supplier Lokal BB
$routes->get('/laporan-supplier-lokal-bb', 'Laporan\Supplier\LaporanSupplierLokalBB::index', ['filter' => 'Auth']);
$routes->get('/laporan-supplier-lokal-bb/kwitansi-tb', 'Laporan\Supplier\KwitansiTb::index', ['filter' => 'Auth']);
$routes->get('/laporan-supplier-lokal-bb/kwitansi-tb/all', 'Laporan\Supplier\KwitansiTb::all', ['filter' => 'Auth']);
$routes->get('/laporan-supplier-lokal-bb/kwitansi-tb/print/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\Supplier\KwitansiTb::exportPDFKwitansiTB/$1/$2/$3/$4', ['filter' => 'Auth']);

$routes->get('/laporan-supplier-lokal-bb/pendapatan-supplier', 'Laporan\Supplier\LaporanSupplierLokalBB::laporanPendapatanSupplier', ['filter' => 'Auth']);
$routes->get('/laporan-supplier-lokal-bb/pendapatan-supplier/all-pendapatan-supplier', 'Laporan\Supplier\LaporanSupplierLokalBB::allLaporanPendapatanSupplier', ['filter' => 'Auth']);
$routes->get('/laporan-supplier-lokal-bb/pendapatan-supplier/print', 'Laporan\Supplier\LaporanSupplierLokalBB::exportPDFPendapatanSupplier', ['filter' => 'Auth']);

$routes->get('/laporan-supplier-lokal-bb/rincian-perbarang', 'Laporan\Supplier\LaporanSupplierLokalBB::laporanRincianPerbarang', ['filter' => 'Auth']);
$routes->get('/laporan-supplier-lokal-bb/rincian-perbarang/all-rincian-perbarang', 'Laporan\Supplier\LaporanSupplierLokalBB::allLaporanRincianPerbarang', ['filter' => 'Auth']);
$routes->get('/laporan-supplier-lokal-bb/rincian-perbarang/print', 'Laporan\Supplier\LaporanSupplierLokalBB::exportPDFLaporanRincianPerbarang', ['filter' => 'Auth']);

$routes->get('/laporan-supplier-lokal-bb/rekap-all-supplier', 'Laporan\Supplier\LaporanSupplierLokalBB::laporanRekapAllSupplier', ['filter' => 'Auth']);
$routes->get('/laporan-supplier-lokal-bb/rekap-all-supplier/all-rekap-all-supplier', 'Laporan\Supplier\LaporanSupplierLokalBB::allLaporanRekapAllSupplier', ['filter' => 'Auth']);
$routes->get('/laporan-supplier-lokal-bb/rekap-all-supplier/print', 'Laporan\Supplier\LaporanSupplierLokalBB::exportPDFLaporanRekapAllSupplier', ['filter' => 'Auth']);
$routes->get('/laporan-supplier-lokal-bb/rekap-all-supplier/print-excel', 'Laporan\Supplier\LaporanSupplierLokalBB::exportExcelLaporanRekapAllSupplier', ['filter' => 'Auth']);

$routes->get('/laporan-supplier-lokal-bb/rekap-persupplier', 'Laporan\Supplier\LaporanSupplierLokalBB::laporanRekapPerSupplier', ['filter' => 'Auth']);
$routes->get('/laporan-supplier-lokal-bb/rekap-persupplier/all-rekap-persupplier', 'Laporan\Supplier\LaporanSupplierLokalBB::allLaporanRekapPersupplier', ['filter' => 'Auth']);
$routes->get('/laporan-supplier-lokal-bb/rekap-persupplier/print', 'Laporan\Supplier\LaporanSupplierLokalBB::exportPDFLaporanRekapPersupplier', ['filter' => 'Auth']);


$routes->get('/laporan-supplier-lokal-bb/rekap-all-barang', 'Laporan\Supplier\LaporanSupplierLokalBB::laporanRekapAllBarang', ['filter' => 'Auth']);
$routes->get('/laporan-supplier-lokal-bb/rekap-all-barang/all-rekap-all-barang', 'Laporan\Supplier\LaporanSupplierLokalBB::allLaporanRekapAllBarang', ['filter' => 'Auth']);
$routes->get('/laporan-supplier-lokal-bb/rekap-all-barang/print', 'Laporan\Supplier\LaporanSupplierLokalBB::exportPDFLaporanRekapAllBarang', ['filter' => 'Auth']);

$routes->get('/laporan-supplier-lokal-bb/rekap-perbarang', 'Laporan\Supplier\LaporanSupplierLokalBB::laporanRekapPerbarang', ['filter' => 'Auth']);
$routes->get('/laporan-supplier-lokal-bb/rekap-perbarang/all-rekap-perbarang', 'Laporan\Supplier\LaporanSupplierLokalBB::allLaporanRekapPerbarang', ['filter' => 'Auth']);
$routes->get('/laporan-supplier-lokal-bb/rekap-perbarang/print', 'Laporan\Supplier\LaporanSupplierLokalBB::exportPDFLaporanRekapPerbarang', ['filter' => 'Auth']);

//Laporan Warehouse
$routes->get('/laporan-warehouse', 'Laporan\Warehouse\LaporanWarehouse::index', ['filter' => 'Auth']);

$routes->get('/laporan-warehouse/sales-order', 'Laporan\Warehouse\LaporanWarehouse::laporanSalesOrder', ['filter' => 'Auth']);
$routes->get('/laporan-warehouse/sales-order/all-sales-order', 'Laporan\Warehouse\LaporanWarehouse::allLaporanSalesOrder', ['filter' => 'Auth']);
$routes->get('/laporan-warehouse/sales-order/print', 'Laporan\Warehouse\LaporanWarehouse::exportPDFLaporanSalesOrder', ['filter' => 'Auth']);

$routes->get('/laporan-warehouse/sales-order-ekspor', 'Laporan\Warehouse\LaporanWarehouse::laporanSalesOrderEkspor', ['filter' => 'Auth']);
$routes->get('/laporan-warehouse/sales-order-ekspor/all-sales-order-ekspor', 'Laporan\Warehouse\LaporanWarehouse::allLaporanSalesOrderEkspor', ['filter' => 'Auth']);
$routes->get('/laporan-warehouse/sales-order-ekspor/print', 'Laporan\Warehouse\LaporanWarehouse::exportPDFLaporanSalesOrderEkspor', ['filter' => 'Auth']);


$routes->get('/laporan-warehouse/purchase-order', 'Laporan\Warehouse\LaporanWarehouse::laporanPurchaseOrder', ['filter' => 'Auth']);
$routes->get('/laporan-warehouse/purchase-order/all-purchase-order', 'Laporan\Warehouse\LaporanWarehouse::allLaporanPurchaseOrder', ['filter' => 'Auth']);
$routes->get('/laporan-warehouse/purchase-order/print', 'Laporan\Warehouse\LaporanWarehouse::exportPDFLaporanPurchaseOrder', ['filter' => 'Auth']);


$routes->get('/laporan-warehouse/penerimaan-barang', 'Laporan\Warehouse\LaporanWarehouse::laporanPenerimaanBarang', ['filter' => 'Auth']);
$routes->get('/laporan-warehouse/penerimaan-barang/all-penerimaan-barang', 'Laporan\Warehouse\LaporanWarehouse::allLaporanPenerimaanBarang', ['filter' => 'Auth']);
$routes->get('/laporan-warehouse/penerimaan-barang/print', 'Laporan\Warehouse\LaporanWarehouse::exportPDFLaporanPenerimaanBarang', ['filter' => 'Auth']);

$routes->get('/laporan-warehouse/material-request', 'Laporan\Warehouse\LaporanWarehouse::laporanMaterialRequest', ['filter' => 'Auth']);
$routes->get('/laporan-warehouse/material-request/all-material-request', 'Laporan\Warehouse\LaporanWarehouse::allLaporanMaterialRequest', ['filter' => 'Auth']);
$routes->get('/laporan-warehouse/material-request/print', 'Laporan\Warehouse\LaporanWarehouse::exportPDFLaporanMaterialRequest', ['filter' => 'Auth']);

$routes->get('/laporan-warehouse/stock-kartu', 'Laporan\Warehouse\LaporanWarehouse::laporanKartuStock', ['filter' => 'Auth']);
$routes->get('/laporan-warehouse/stock-kartu/all-stock-kartu', 'Laporan\Warehouse\LaporanWarehouse::allLaporanKartuStock', ['filter' => 'Auth']);
$routes->get('/laporan-warehouse/stock-kartu/print', 'Laporan\Warehouse\LaporanWarehouse::exportSheetLaporanKartuStock', ['filter' => 'Auth']);
// LAPORAN 
$routes->get('/laporan-bea-cukai', 'Laporan\BeaCukai\LaporanBeaCukai::index', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-pemasukan-barang', 'Laporan\BeaCukai\LaporanBeaCukai::laporanPemasukanBarang', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/all-masuk', 'Laporan\BeaCukai\LaporanBeaCukai::ajaxAllMasukBarang', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-pemasukan-barang/print', 'Laporan\BeaCukai\LaporanBeaCukai::exportPDFLaporanMasukBarang', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-pemasukan-barang/excel', 'Laporan\BeaCukai\LaporanBeaCukai::exportExcelLaporanMasukBarang', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-pengeluaran-barang', 'Laporan\BeaCukai\LaporanBeaCukai::laporanPengeluaranBarang', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/all-keluar', 'Laporan\BeaCukai\LaporanBeaCukai::ajaxAllKeluarBarang', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-pengeluaran-barang/print', 'Laporan\BeaCukai\LaporanBeaCukai::exportPDFLaporanKeluarBarang', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-pengeluaran-barang/excel', 'Laporan\BeaCukai\LaporanBeaCukai::exportExcelLaporanKeluarBarang', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-wip', 'Laporan\BeaCukai\LaporanBeaCukai::laporanWip', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/all-wip', 'Laporan\BeaCukai\LaporanBeaCukai::allWip', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-wip/print', 'Laporan\BeaCukai\LaporanBeaCukai::exportPDFLaporanWip', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-wip/excel', 'Laporan\BeaCukai\LaporanBeaCukai::exportExcelLaporanWip', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-mutasi-bahan-baku-penolong', 'Laporan\BeaCukai\LaporanBeaCukai::laporanMutasiBahanBakuPenolong', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-mutasi-barang-jadi', 'Laporan\BeaCukai\LaporanBeaCukai::laporanMutasiBarangJadi');
$routes->get('/laporan-bea-cukai/laporan-mutasi-barang-scrap', 'Laporan\BeaCukai\LaporanBeaCukai::laporanMutasiBarangScrap', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-mutasi-barang-modal', 'Laporan\BeaCukai\LaporanBeaCukai::laporanMutasiBarangModal', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-2.3', 'Laporan\BeaCukai\LaporanBeaCukai::laporanDuaTiga', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/all-2.3', 'Laporan\BeaCukai\LaporanBeaCukai::allBCDuaTiga', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-2.3/print', 'Laporan\BeaCukai\LaporanBeaCukai::exportPDFLaporanBCDuaTiga', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-2.3/excel', 'Laporan\BeaCukai\LaporanBeaCukai::exportExcelLaporanBCDuaTiga', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-4.0', 'Laporan\BeaCukai\LaporanBeaCukai::laporanEmpatKosong', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/all-4.0', 'Laporan\BeaCukai\LaporanBeaCukai::allBCEmpatKosong', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-4.0/print', 'Laporan\BeaCukai\LaporanBeaCukai::exportPDFLaporanBCEmpatKosong', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-4.0/excel', 'Laporan\BeaCukai\LaporanBeaCukai::exportExcelLaporanBCEmpatKosong', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-2.5', 'Laporan\BeaCukai\LaporanBeaCukai::laporanDuaLima', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/all-2.5', 'Laporan\BeaCukai\LaporanBeaCukai::allBCDuaLima', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-2.5/print', 'Laporan\BeaCukai\LaporanBeaCukai::exportPDFLaporanBCDuaLima', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-2.5/excel', 'Laporan\BeaCukai\LaporanBeaCukai::exportExcelLaporanBCDuaLima', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-3.0', 'Laporan\BeaCukai\LaporanBeaCukai::laporanTigaKosong', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/all-3.0', 'Laporan\BeaCukai\LaporanBeaCukai::allBCTigaKosong', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-3.0/print', 'Laporan\BeaCukai\LaporanBeaCukai::exportPDFLaporanBCTigaKosong', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-3.0/excel', 'Laporan\BeaCukai\LaporanBeaCukai::exportExcelLaporanBCTigaKosong', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-4.1', 'Laporan\BeaCukai\LaporanBeaCukai::laporanEmpatSatu', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/all-4.1', 'Laporan\BeaCukai\LaporanBeaCukai::allBCEmpatSatu', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-4.1/print', 'Laporan\BeaCukai\LaporanBeaCukai::exportPDFLaporanBCEmpatSatu', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-4.1/excel', 'Laporan\BeaCukai\LaporanBeaCukai::exportExcelLaporanBCEmpatSatu', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-2.7', 'Laporan\BeaCukai\LaporanBeaCukai::laporanDuaTujuh', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/all-2.7', 'Laporan\BeaCukai\LaporanBeaCukai::allBCDuaTujuh', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-2.7/print', 'Laporan\BeaCukai\LaporanBeaCukai::exportPDFLaporanBCDuaTujuh', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-2.7/excel', 'Laporan\BeaCukai\LaporanBeaCukai::exportExcelLaporanBCDuaTujuh', ['filter' => 'Auth']);

$routes->get('/laporan-bea-cukai/laporan-mutasi/print', 'Laporan\BeaCukai\LaporanBeaCukai::exportPDFLaporanMutasi', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/laporan-mutasi/excel', 'Laporan\BeaCukai\LaporanBeaCukai::exportExcelLaporanMutasi', ['filter' => 'Auth']);
$routes->get('/laporan-bea-cukai/all-mutasi-barang', 'Laporan\BeaCukai\LaporanBeaCukai::allMutasiBarang', ['filter' => 'Auth']);

//Penjualan
$routes->get('/laporan-sales', 'Laporan\Penjualan\Penjualan::index', ['filter' => 'Auth']);

$routes->get('/laporan-sales/sales-per-pelanggan', 'Laporan\Penjualan\PenjualanPerPelanggan::index', ['filter' => 'Auth']);
$routes->get('/laporan-sales/sales-per-pelanggan/all', 'Laporan\Penjualan\PenjualanPerPelanggan::allTransaksi', ['filter' => 'Auth']);
$routes->get('/laporan-sales/sales-per-pelanggan/printPDF/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\Penjualan\PenjualanPerPelanggan::LaporanPenjualanPrint/$1/$2/$3/$4', ['filter' => 'Auth']);
$routes->get('/laporan-sales/sales-per-pelanggan/printExcel/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\Penjualan\Penjualan::exportExcel/$1/$2/$3/$4', ['filter' => 'Auth']);

$routes->get('/laporan-sales/sales-per-barang', 'Laporan\Penjualan\PenjualanPerBarang::index', ['filter' => 'Auth']);
$routes->get('/laporan-sales/sales-per-barang/all', 'Laporan\Penjualan\PenjualanPerBarang::allTransaksi', ['filter' => 'Auth']);
$routes->get('/laporan-sales/sales-per-barang/printPDF/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\Penjualan\PenjualanPerBarang::LaporanPenjualanPrint/$1/$2/$3/$4', ['filter' => 'Auth']);
$routes->get('/laporan-sales/sales-per-barang/printExcel/(:segment)/(:segment)/(:segment)/(:segment)', 'Laporan\Penjualan\PenjualanPerBarang::exportExcel/$1/$2/$3/$4', ['filter' => 'Auth']);


// RETUR LOKAL BB
$routes->get('/retur-po-lokal-bb', 'ReturPembelian\ReturPembelianLokalBB::index', ['filter' => 'Auth']);
$routes->get('/retur-po-lokal-bb/all', 'ReturPembelian\ReturPembelianLokalBB::all', ['filter' => 'Auth']);
$routes->post('/retur-po-lokal-bb/delete', 'ReturPembelian\ReturPembelianLokalBB::delete', ['filter' => 'Auth']);
$routes->post('/retur-po-lokal-bb/posting', 'ReturPembelian\ReturPembelianLokalBB::posting', ['filter' => 'Auth']);
$routes->post('/retur-po-lokal-bb/unposting', 'ReturPembelian\ReturPembelianLokalBB::unposting', ['filter' => 'Auth']);
$routes->get('/retur-po-lokal-bb/create', 'ReturPembelian\ReturPembelianLokalBB::create', ['filter' => 'Auth']);
$routes->get('/retur-po-lokal-bb/generate-number', 'ReturPembelian\ReturPembelianLokalBB::generateNumber', ['filter' => 'Auth']);
$routes->get('/retur-po-lokal-bb/dropdown-penerimaan-barang', 'ReturPembelian\ReturPembelianLokalBB::dropdownPenerimaanBarang', ['filter' => 'Auth']);
$routes->get('/retur-po-lokal-bb/retur-detail', 'ReturPembelian\ReturPembelianLokalBB::detailBarang', ['filter' => 'Auth']);
$routes->post('/retur-po-lokal-bb/save', 'ReturPembelian\ReturPembelianLokalBB::createAction', ['filter' => 'Auth']);
$routes->post('/retur-po-lokal-bb/update', 'ReturPembelian\ReturPembelianLokalBB::updateAction', ['filter' => 'Auth']);
$routes->get('/retur-po-lokal-bb/id/(:segment)', 'ReturPembelian\ReturPembelianLokalBB::update/$1', ['filter' => 'Auth']);
$routes->get('/retur-po-lokal-bb/print/(:segment)', 'ReturPembelian\ReturPembelianLokalBB::print/$1', ['filter' => 'Auth']);
// RETUR LOKAL BP
$routes->get('/retur-po-lokal-bp', 'ReturPembelian\ReturPembelianLokalBP::index', ['filter' => 'Auth']);
$routes->get('/retur-po-lokal-bp/all', 'ReturPembelian\ReturPembelianLokalBP::all', ['filter' => 'Auth']);
$routes->post('/retur-po-lokal-bp/delete', 'ReturPembelian\ReturPembelianLokalBB::delete', ['filter' => 'Auth']);
$routes->post('/retur-po-lokal-bp/posting', 'ReturPembelian\ReturPembelianLokalBB::posting', ['filter' => 'Auth']);
$routes->post('/retur-po-lokal-bp/unposting', 'ReturPembelian\ReturPembelianLokalBB::unposting', ['filter' => 'Auth']);
$routes->get('/retur-po-lokal-bp/create', 'ReturPembelian\ReturPembelianLokalBP::create', ['filter' => 'Auth']);
$routes->get('/retur-po-lokal-bp/generate-number', 'ReturPembelian\ReturPembelianLokalBB::generateNumber', ['filter' => 'Auth']);
$routes->get('/retur-po-lokal-bp/dropdown-penerimaan-barang', 'ReturPembelian\ReturPembelianLokalBP::dropdownPenerimaanBarang', ['filter' => 'Auth']);
$routes->get('/retur-po-lokal-bp/retur-detail', 'ReturPembelian\ReturPembelianLokalBB::detailBarang', ['filter' => 'Auth']);
$routes->post('/retur-po-lokal-bp/save', 'ReturPembelian\ReturPembelianLokalBB::createAction', ['filter' => 'Auth']);
$routes->post('/retur-po-lokal-bp/update', 'ReturPembelian\ReturPembelianLokalBB::updateAction', ['filter' => 'Auth']);
$routes->get('/retur-po-lokal-bp/id/(:segment)', 'ReturPembelian\ReturPembelianLokalBP::update/$1', ['filter' => 'Auth']);
$routes->get('/retur-po-lokal-bp/print/(:segment)', 'ReturPembelian\ReturPembelianLokalBP::print/$1', ['filter' => 'Auth']);
// RETUR IMPORT BB
$routes->get('/retur-po-import-bb', 'ReturPembelian\ReturPembelianImportBB::index', ['filter' => 'Auth']);
$routes->get('/retur-po-import-bb/all', 'ReturPembelian\ReturPembelianImportBB::all', ['filter' => 'Auth']);
$routes->post('/retur-po-import-bb/delete', 'ReturPembelian\ReturPembelianLokalBB::delete', ['filter' => 'Auth']);
$routes->post('/retur-po-import-bb/posting', 'ReturPembelian\ReturPembelianLokalBB::posting', ['filter' => 'Auth']);
$routes->post('/retur-po-import-bb/unposting', 'ReturPembelian\ReturPembelianLokalBB::unposting', ['filter' => 'Auth']);
$routes->get('/retur-po-import-bb/create', 'ReturPembelian\ReturPembelianImportBB::create', ['filter' => 'Auth']);
$routes->get('/retur-po-import-bb/generate-number', 'ReturPembelian\ReturPembelianLokalBB::generateNumber', ['filter' => 'Auth']);
$routes->get('/retur-po-import-bb/dropdown-penerimaan-barang', 'ReturPembelian\ReturPembelianImportBB::dropdownPenerimaanBarang', ['filter' => 'Auth']);
$routes->get('/retur-po-import-bb/retur-detail', 'ReturPembelian\ReturPembelianLokalBB::detailBarang', ['filter' => 'Auth']);
$routes->post('/retur-po-import-bb/save', 'ReturPembelian\ReturPembelianLokalBB::createAction', ['filter' => 'Auth']);
$routes->post('/retur-po-import-bb/update', 'ReturPembelian\ReturPembelianLokalBB::updateAction', ['filter' => 'Auth']);
$routes->get('/retur-po-import-bb/id/(:segment)', 'ReturPembelian\ReturPembelianImportBB::update/$1', ['filter' => 'Auth']);
$routes->get('/retur-po-import-bb/print/(:segment)', 'ReturPembelian\ReturPembelianImportBB::print/$1', ['filter' => 'Auth']);
// RETUR IMPORT BP
$routes->get('/retur-po-import-bp', 'ReturPembelian\ReturPembelianImportBP::index', ['filter' => 'Auth']);
$routes->get('/retur-po-import-bp/all', 'ReturPembelian\ReturPembelianImportBP::all', ['filter' => 'Auth']);
$routes->post('/retur-po-import-bp/delete', 'ReturPembelian\ReturPembelianLokalBB::delete', ['filter' => 'Auth']);
$routes->post('/retur-po-import-bp/posting', 'ReturPembelian\ReturPembelianLokalBB::posting', ['filter' => 'Auth']);
$routes->post('/retur-po-import-bp/unposting', 'ReturPembelian\ReturPembelianLokalBB::unposting', ['filter' => 'Auth']);
$routes->get('/retur-po-import-bp/create', 'ReturPembelian\ReturPembelianImportBP::create', ['filter' => 'Auth']);
$routes->get('/retur-po-import-bp/generate-number', 'ReturPembelian\ReturPembelianLokalBB::generateNumber', ['filter' => 'Auth']);
$routes->get('/retur-po-import-bp/dropdown-penerimaan-barang', 'ReturPembelian\ReturPembelianImportBP::dropdownPenerimaanBarang', ['filter' => 'Auth']);
$routes->get('/retur-po-import-bp/retur-detail', 'ReturPembelian\ReturPembelianLokalBB::detailBarang', ['filter' => 'Auth']);
$routes->post('/retur-po-import-bp/save', 'ReturPembelian\ReturPembelianLokalBB::createAction', ['filter' => 'Auth']);
$routes->post('/retur-po-import-bp/update', 'ReturPembelian\ReturPembelianLokalBB::updateAction', ['filter' => 'Auth']);
$routes->get('/retur-po-import-bp/id/(:segment)', 'ReturPembelian\ReturPembelianImportBP::update/$1', ['filter' => 'Auth']);
$routes->get('/retur-po-import-bp/print/(:segment)', 'ReturPembelian\ReturPembelianImportBP::print/$1', ['filter' => 'Auth']);
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
