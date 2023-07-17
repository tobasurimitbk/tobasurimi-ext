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

// SUPPLIER
// BAHAN BAKU LOKAL
$routes->get('/supplier-bahan-baku', 'Supplier\SupplierBahanBaku::supplierBahanBaku', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-baku/all', 'Supplier\SupplierBahanBaku::allSupplierBahanBaku', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-baku/id/(:num)', 'Supplier\SupplierBahanBaku::getByIdSupplierBahanBaku/$1', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-baku/save', 'Supplier\SupplierBahanBaku::saveSupplierBahanBaku', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-baku/update', 'Supplier\SupplierBahanBaku::updateSupplierBahanBaku', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-baku/delete', 'Supplier\SupplierBahanBaku::deleteSupplierBahanBaku', ['filter' => 'Auth']);

// BAHAN PENOLONG LOKAL
$routes->get('/supplier-bahan-penolong', 'Supplier\SupplierBahanPenolong::supplierBahanPenolong', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-penolong/all', 'Supplier\SupplierBahanPenolong::allSupplierBahanPenolong', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-penolong/id/(:num)', 'Supplier\SupplierBahanPenolong::getByIdSupplierBahanPenolong/$1', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-penolong/save', 'Supplier\SupplierBahanPenolong::saveSupplierBahanPenolong', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-penolong/update', 'Supplier\SupplierBahanPenolong::updateSupplierBahanPenolong', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-penolong/delete', 'Supplier\SupplierBahanPenolong::deleteSupplierBahanPenolong', ['filter' => 'Auth']);

// BAHAN BAKU IMPORT
$routes->get('/supplier-bahan-baku-import', 'Supplier\SupplierBahanBakuImport::supplierBahanBakuImport', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-baku-import/all', 'Supplier\SupplierBahanBakuImport::allSupplierBahanBakuImport', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-baku-import/id/(:num)', 'Supplier\SupplierBahanBakuImport::getByIdSupplierBahanBakuImport/$1', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-baku-import/save', 'Supplier\SupplierBahanBakuImport::saveSupplierBahanBakuImport', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-baku-import/update', 'Supplier\SupplierBahanBakuImport::updateSupplierBahanBakuImport', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-baku-import/delete', 'Supplier\SupplierBahanBakuImport::deleteSupplierBahanBakuImport', ['filter' => 'Auth']);

// BAHAN PENOLONG IMPORT
$routes->get('/supplier-bahan-penolong-import', 'Supplier\SupplierBahanPenolongImport::supplierBahanPenolongImport', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-penolong-import/all', 'Supplier\SupplierBahanPenolongImport::allSupplierBahanPenolongImport', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-penolong-import/id/(:segment)', 'Supplier\SupplierBahanPenolongImport::getByIdSupplierBahanPenolongImport/$1', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-penolong-import/save', 'Supplier\SupplierBahanPenolongImport::saveSupplierBahanPenolongImport', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-penolong-import/update', 'Supplier\SupplierBahanPenolongImport::updateSupplierBahanPenolongImport', ['filter' => 'Auth']);
$routes->post('/supplier-bahan-penolong-import/delete', 'Supplier\SupplierBahanPenolongImport::deleteSupplierBahanPenolongImport', ['filter' => 'Auth']);

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

// BAHAN BAKU PO LOKAL
$routes->get('/po-lokal-bahan-baku', 'Purchase\POLokalBahanBaku::poLokalBahanBaku', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-baku/all', 'Purchase\POLokalBahanBaku::allPOLokalBahanBaku', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-baku/id/(:segment)', 'Purchase\POLokalBahanBaku::getByIdPOLokalBahanBaku/$1', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-baku/ajax', 'Purchase\POLokalBahanBaku::getByIdPOLokalBahanBakuAjax', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-baku/create', 'Purchase\POLokalBahanBaku::createPOLokalBahanBaku', ['filter' => 'Auth']);
$routes->post('/po-lokal-bahan-baku/save', 'Purchase\POLokalBahanBaku::savePOLokalBahanBaku', ['filter' => 'Auth']);
$routes->post('/po-lokal-bahan-baku/update', 'Purchase\POLokalBahanBaku::updatePOLokalBahanBaku', ['filter' => 'Auth']);
$routes->post('/po-lokal-bahan-baku/update-status', 'Purchase\POLokalBahanBaku::updateStatusPOLokalBahanBaku', ['filter' => 'Auth']);
$routes->post('/po-lokal-bahan-baku/delete', 'Purchase\POLokalBahanBaku::deletePOLokalBahanBaku', ['filter' => 'Auth']);

// BAHAN BAKU PO PENOLONG
$routes->get('/po-lokal-bahan-penolong', 'Purchase\POLokalBahanPenolong::poLokalBahanPenolong', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-penolong/all', 'Purchase\POLokalBahanPenolong::allPOLokalBahanPenolong', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-penolong/id/(:segment)', 'Purchase\POLokalBahanPenolong::getByIdPOLokalBahanPenolong/$1', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-penolong/ajax', 'Purchase\POLokalBahanPenolong::getByIdPOLokalBahanPenolongAjax', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-penolong/create', 'Purchase\POLokalBahanPenolong::createPOLokalBahanPenolong', ['filter' => 'Auth']);
$routes->post('/po-lokal-bahan-penolong/save', 'Purchase\POLokalBahanPenolong::savePOLokalBahanPenolong', ['filter' => 'Auth']);
$routes->post('/po-lokal-bahan-penolong/update', 'Purchase\POLokalBahanPenolong::updatePOLokalBahanPenolong', ['filter' => 'Auth']);
$routes->post('/po-lokal-bahan-penolong/update-status', 'Purchase\POLokalBahanPenolong::updateStatusPOLokalBahanPenolong', ['filter' => 'Auth']);
$routes->post('/po-lokal-bahan-penolong/delete', 'Purchase\POLokalBahanPenolong::deletePOLokalBahanPenolong', ['filter' => 'Auth']);

// BAHAN BAKU PO IMPORT
$routes->get('/po-import-bahan-baku', 'Purchase\POImportBahanBaku::poImportBahanBaku', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-baku/all', 'Purchase\POImportBahanBaku::allPOImportBahanBaku', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-baku/id/(:segment)', 'Purchase\POImportBahanBaku::getByIdPOImportBahanBaku/$1', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-baku/ajax', 'Purchase\POImportBahanBaku::getByIdPOImportBahanBakuAjax', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-baku/create', 'Purchase\POImportBahanBaku::createPOImportBahanBaku', ['filter' => 'Auth']);
$routes->post('/po-import-bahan-baku/save', 'Purchase\POImportBahanBaku::savePOImportBahanBaku', ['filter' => 'Auth']);
$routes->post('/po-import-bahan-baku/update', 'Purchase\POImportBahanBaku::updatePOImportBahanBaku', ['filter' => 'Auth']);
$routes->post('/po-import-bahan-baku/update-status', 'Purchase\POImportBahanBaku::updateStatusPOImportBahanBaku', ['filter' => 'Auth']);
$routes->post('/po-import-bahan-baku/delete', 'Purchase\POImportBahanBaku::deletePOImportBahanBaku', ['filter' => 'Auth']);

// BAHAN BAKU PO PENOLONG
$routes->get('/po-import-bahan-penolong', 'Purchase\POImportBahanPenolong::poImportBahanPenolong', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-penolong/all', 'Purchase\POImportBahanPenolong::allPOImportBahanPenolong', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-penolong/id/(:segment)', 'Purchase\POImportBahanPenolong::getByIdPOImportBahanPenolong/$1', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-penolong/ajax', 'Purchase\POImportBahanPenolong::getByIdPOImportBahanPenolongAjax', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-penolong/create', 'Purchase\POImportBahanPenolong::createPOImportBahanPenolong', ['filter' => 'Auth']);
$routes->post('/po-import-bahan-penolong/save', 'Purchase\POImportBahanPenolong::savePOImportBahanPenolong', ['filter' => 'Auth']);
$routes->post('/po-import-bahan-penolong/update', 'Purchase\POImportBahanPenolong::updatePOImportBahanPenolong', ['filter' => 'Auth']);
$routes->post('/po-import-bahan-penolong/update-status', 'Purchase\POImportBahanPenolong::updateStatusPOImportBahanPenolong', ['filter' => 'Auth']);
$routes->post('/po-import-bahan-penolong/delete', 'Purchase\POImportBahanPenolong::deletePOImportBahanPenolong', ['filter' => 'Auth']);

// TERIMA FAKTUR LOKAL
$routes->get('/terima-faktur-lokal/getBySupplier/(:num)', 'Purchase\TerimaFakturLokal::getBySupplierId/$1', ['filter' => 'Auth']);
$routes->get('/terima-faktur-lokal', 'Purchase\TerimaFakturLokal::terimafakturLokal', ['filter' => 'Auth']);
$routes->get('/terima-faktur-lokal/all', 'Purchase\TerimaFakturLokal::allTerimafakturLokal', ['filter' => 'Auth']);
$routes->get('/terima-faktur-lokal/id/(:segment)', 'Purchase\TerimaFakturLokal::getByIdTerimafakturLokal/$1', ['filter' => 'Auth']);
$routes->get('/terima-faktur-lokal/create', 'Purchase\TerimaFakturLokal::createTerimafakturLokal', ['filter' => 'Auth']);
$routes->post('/terima-faktur-lokal/save', 'Purchase\TerimaFakturLokal::saveTerimafakturLokal', ['filter' => 'Auth']);
$routes->post('/terima-faktur-lokal/update', 'Purchase\TerimaFakturLokal::updateTerimaFakturLokal', ['filter' => 'Auth']);
$routes->post('/terima-faktur-lokal/delete', 'Purchase\TerimaFakturLokal::deleteTerimaFakturLokal', ['filter' => 'Auth']);

// TERIMA FAKTUR IMPORT
$routes->get('/terima-faktur-import', 'Purchase\TerimaFakturImport::terimaFakturImport', ['filter' => 'Auth']);
$routes->get('/terima-faktur-import/all', 'Purchase\TerimaFakturImport::allTerimaFakturImport', ['filter' => 'Auth']);
$routes->get('/terima-faktur-import/id/(:segment)', 'Purchase\TerimaFakturImport::getByIdTerimaFakturImport/$1', ['filter' => 'Auth']);
$routes->get('/terima-faktur-import/create', 'Purchase\TerimaFakturImport::createTerimaFakturImport', ['filter' => 'Auth']);
$routes->post('/terima-faktur-import/save', 'Purchase\TerimaFakturImport::saveTerimaFakturImport', ['filter' => 'Auth']);
$routes->post('/terima-faktur-import/update', 'Purchase\TerimaFakturImport::updateTerimaFakturImport', ['filter' => 'Auth']);
$routes->post('/terima-faktur-import/delete', 'Purchase\TerimaFakturImport::deleteTerimaFakturImport', ['filter' => 'Auth']);

// REKAP FAKTUR
$routes->get('/rekap-faktur', 'Purchase\RekapFaktur', ['filter' => 'Auth']);
$routes->get('/rekap-faktur/all', 'Purchase\RekapFaktur::getRekapFakturList', ['filter' => 'Auth']);
$routes->get('/rekap-faktur/supplier/(:num)', 'Purchase\RekapFaktur::getRekapFakturBySupplier/$1', ['filter' => 'Auth']);
$routes->get('/rekap-faktur/(:num)', 'Purchase\RekapFaktur::getRekapFakturById/$1', ['filter' => 'Auth']);
$routes->get('/rekap-faktur/create', 'Purchase\RekapFaktur::createRekapFaktur', ['filter' => 'Auth']);
$routes->post('/rekap-faktur/create', 'Purchase\RekapFaktur::saveRekapFaktur', ['filter' => 'Auth']);
$routes->post('/rekap-faktur/update-status', 'Purchase\RekapFaktur::updateStatusRekap', ['filter' => 'Auth']);
$routes->post('/rekap-faktur/update', 'Purchase\RekapFaktur::updateRekap', ['filter' => 'Auth']);
$routes->post('/rekap-faktur/delete', 'Purchase\RekapFaktur::deleteRekap', ['filter' => 'Auth']);

// PEMBAYARAN
// PEMBAYARAN PO LOKAL
$routes->get('/pembayaran-po-lokal', 'Pembayaran\PembayaranPOLokal::pembayaranPOLokal', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal/all', 'Pembayaran\PembayaranPOLokal::allPembayaranPOLokal', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal/(:num)', 'Pembayaran\PembayaranPOLokal::getByIdPembayaranPOLokal/$1', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-lokal/create', 'Pembayaran\PembayaranPOLokal::createPembayaranPOLokal', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal/create', 'Pembayaran\PembayaranPOLokal::savePembayaranPOLokal', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal/update', 'Pembayaran\PembayaranPOLokal::updatePembayaranPOLokal', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-lokal/delete', 'Pembayaran\PembayaranPOLokal::deletePembayaranPOLokal', ['filter' => 'Auth']);

// PEMBAYARAN PO IMPORT
$routes->get('/pembayaran-po-import', 'Pembayaran\PembayarzanPOImport::pembayaranPOImport', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-import/all', 'Pembayaran\PembayaranPOImport::allPembayaranPOImport', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-import/id/(:segment)', 'Pembayaran\PembayaranPOImport::getByIdPembayaranPOImport/$1', ['filter' => 'Auth']);
$routes->get('/pembayaran-po-import/create', 'Pembayaran\PembayaranPOImport::createPembayaranPOImport', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-import/save', 'Pembayaran\PembayaranPOImport::savePembayaranPOImport', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-import/update', 'Pembayaran\PembayaranPOImport::updatePembayaranPOImport', ['filter' => 'Auth']);
$routes->post('/pembayaran-po-import/delete', 'Pembayaran\PembayaranPOImport::deletePembayaranPOImport', ['filter' => 'Auth']);

// SALES LOKAL
// Order Form Lokal
$routes->get('/order-form-lokal', 'SalesLokal\OrderForm::index', ['filter' => 'Auth']);
$routes->get('/order-form-lokal/id/(:segment)', 'SalesLokal\OrderForm::getById/$1', ['filter' => 'Auth']);
$routes->get('/order-form-lokal/create', 'SalesLokal\OrderForm::createView', ['filter' => 'Auth']);
$routes->get('/order-form-lokal/all', 'SalesLokal\OrderForm::all', ['filter' => 'Auth']);
$routes->post('/order-form-lokal/save', 'SalesLokal\OrderForm::create', ['filter' => 'Auth']);
$routes->post('/order-form-lokal/update', 'SalesLokal\OrderForm::update', ['filter' => 'Auth']);
$routes->post('/order-form-lokal/delete', 'SalesLokal\OrderForm::delete', ['filter' => 'Auth']);

$routes->get('/order-form-lokal/barangAll', 'SalesLokal\OrderForm::getAllBarang', ['filter' => 'Auth']);

// Invoice Lokal
$routes->get('/invoice-penjualan-lokal', 'SalesLokal\Invoice::index', ['filter' => 'Auth']);
$routes->get('/invoice-penjualan-lokal/id/(:segment)', 'SalesLokal\Invoice::getById/$1', ['filter' => 'Auth']);
$routes->get('/invoice-penjualan-lokal/create', 'SalesLokal\Invoice::createView', ['filter' => 'Auth']);
$routes->get('/invoice-penjualan-lokal/all', 'SalesLokal\Invoice::all', ['filter' => 'Auth']);
$routes->post('/invoice-penjualan-lokal/save', 'SalesLokal\Invoice::create', ['filter' => 'Auth']);
$routes->post('/invoice-penjualan-lokal/update', 'SalesLokal\Invoice::update', ['filter' => 'Auth']);
$routes->post('/invoice-penjualan-lokal/delete', 'SalesLokal\Invoice::delete', ['filter' => 'Auth']);

// Surat Jalan
$routes->get('/surat-jalan', 'SalesLokal\SuratJalan::index', ['filter' => 'Auth']);
$routes->get('/surat-jalan/id/(:segment)', 'SalesLokal\SuratJalan::getById/$1', ['filter' => 'Auth']);
$routes->get('/surat-jalan/create', 'SalesLokal\SuratJalan::createView', ['filter' => 'Auth']);
$routes->get('/surat-jalan/all', 'SalesLokal\SuratJalan::all', ['filter' => 'Auth']);
$routes->post('/surat-jalan/save', 'SalesLokal\SuratJalan::create', ['filter' => 'Auth']);
$routes->post('/surat-jalan/update', 'SalesLokal\SuratJalan::update', ['filter' => 'Auth']);
$routes->post('/surat-jalan/delete', 'SalesLokal\SuratJalan::delete', ['filter' => 'Auth']);

// Retur
$routes->get('/retur', 'SalesLokal\Retur::index', ['filter' => 'Auth']);
$routes->get('/retur/id/(:segment)', 'SalesLokal\Retur::getById/$1', ['filter' => 'Auth']);
$routes->get('/retur/create', 'SalesLokal\Retur::createView', ['filter' => 'Auth']);
$routes->get('/retur/all', 'SalesLokal\Retur::all', ['filter' => 'Auth']);
$routes->post('/retur/save', 'SalesLokal\Retur::create', ['filter' => 'Auth']);
$routes->post('/retur/update', 'SalesLokal\Retur::update', ['filter' => 'Auth']);
$routes->post('/retur/delete', 'SalesLokal\Retur::delete', ['filter' => 'Auth']);


// SALES INTERNASIONAL
// Order Form Internasional
$routes->get('/order-form-internasional', 'SalesInternasional\OrderForm::index', ['filter' => 'Auth']);
$routes->get('/order-form-internasional/id/(:segment)', 'SalesInternasional\OrderForm::getById/$1', ['filter' => 'Auth']);
$routes->get('/order-form-internasional/create', 'SalesInternasional\OrderForm::createView', ['filter' => 'Auth']);
$routes->get('/order-form-internasional/all', 'SalesInternasional\OrderForm::all', ['filter' => 'Auth']);
$routes->post('/order-form-internasional/save', 'SalesInternasional\OrderForm::create', ['filter' => 'Auth']);
$routes->post('/order-form-internasional/update', 'SalesInternasional\OrderForm::update', ['filter' => 'Auth']);
$routes->post('/order-form-internasional/delete', 'SalesInternasional\OrderForm::delete', ['filter' => 'Auth']);


// DROPDOWN

// TERIMA FAKTUR
$routes->get('/penerimaan-barang-lokal/dropdown', 'Warehouse\PenerimaanBarangLokal::dropdownpenerimaanBarangLokal', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import/dropdown', 'Warehouse\PenerimaanBarangImport::dropdownpenerimaanBarangImport', ['filter' => 'Auth']);

// SUPPLIER 
$routes->get('/supplier-bahan-baku/dropdown', 'Supplier\SupplierBahanBaku::dropdownSupplier', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-penolong/dropdown', 'Supplier\SupplierBahanPenolong::dropdownSupplier', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-baku-import/dropdown', 'Supplier\SupplierBahanBakuImport::dropdownSupplier', ['filter' => 'Auth']);
$routes->get('/supplier-bahan-penolong-import/dropdown', 'Supplier\SupplierBahanPenolongImport::dropdownSupplier', ['filter' => 'Auth']);

// PO
$routes->get('/po-lokal-bahan-baku/dropdown', 'Purchase\POLokalBahanBaku::dropdownPOLokalBahanBaku', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-penolong/dropdown', 'Purchase\POLokalBahanPenolong::dropdownPOLokalBahanPenolong', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-baku/dropdown', 'Purchase\POImportBahanBaku::dropdownPOImportBahanBaku', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-penolong/dropdown', 'Purchase\POImportBahanPenolong::dropdownPOImportBahanPenolong', ['filter' => 'Auth']);

$routes->get('/po-lokal-bahan-baku/multi/dropdown', 'Purchase\POLokalBahanBaku::dropdownBarangPOLokalBahanBaku', ['filter' => 'Auth']);
$routes->get('/po-lokal-bahan-penolong/multi/dropdown', 'Purchase\POLokalBahanPenolong::dropdownBarangPOLokalBahanPenolong', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-baku/multi/dropdown', 'Purchase\POImportBahanBaku::dropdownBarangPOImportBahanBaku', ['filter' => 'Auth']);
$routes->get('/po-import-bahan-penolong/multi/dropdown', 'Purchase\POImportBahanPenolong::dropdownBarangPOImportBahanPenolong', ['filter' => 'Auth']);

// CITY
$routes->get('/city/(:segment)', 'Master\City::getCityByProvince/$1', ['filter' => 'Auth']);

// EMPLOYEE
$routes->get('/employee/dropdown', 'Master\Employee::dropdownEmployee', ['filter' => 'Auth']);
$routes->get('/employee-pic/dropdown', 'Master\Employee::dropdownEmployeePIC', ['filter' => 'Auth']);

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
$routes->get('/barang/dropdown/kategori', 'Warehouse\Barang::dropdownBarangKategori', ['filter' => 'Auth']);

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
$routes->get('/penerimaan-barang-lokal/id/(:segment)', 'Warehouse\PenerimaanBarangLokal::getByIdPenerimaanBarangLokal/$1', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-lokal/save', 'Warehouse\PenerimaanBarangLokal::savePenerimaanBarangLokal', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-lokal/update', 'Warehouse\PenerimaanBarangLokal::updatePenerimaanBarangLokal', ['filter' => 'Auth']);
// $routes->post('/penerimaan-barang-lokal/update-status', 'Warehouse\PenerimaanBarangLokal::updateStatusPenerimaanBarangLokal', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-lokal/delete', 'Warehouse\PenerimaanBarangLokal::deletePenerimaanBarangLokal', ['filter' => 'Auth']);

// PENERIMAAN BARANG IMPORT
$routes->get('/penerimaan-barang-import', 'Warehouse\PenerimaanBarangImport::penerimaanBarangImport', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import/all', 'Warehouse\PenerimaanBarangImport::allPenerimaanBarangImport', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import/create', 'Warehouse\PenerimaanBarangImport::createPenerimaanBarangImport', ['filter' => 'Auth']);
$routes->get('/penerimaan-barang-import/id/(:segment)', 'Warehouse\PenerimaanBarangImport::getByIdPenerimaanBarangImport/$1', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-import/save', 'Warehouse\PenerimaanBarangImport::savePenerimaanBarangImport', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-import/update', 'Warehouse\PenerimaanBarangImport::updatePenerimaanBarangImport', ['filter' => 'Auth']);
// $routes->post('/penerimaan-barang-import/update-status', 'Warehouse\PenerimaanBarangImport::updateStatusPenerimaanBarangImport', ['filter' => 'Auth']);
$routes->post('/penerimaan-barang-import/delete', 'Warehouse\PenerimaanBarangImport::deletePenerimaanBarangImport', ['filter' => 'Auth']);

// HUMAN RESOURCE
// Attendance
$routes->get('/attendance', 'HR\Attendance::attendance', ['filter' => 'Auth']);
$routes->get('/list-attendance', 'HR\Attendance::ListAttendance', ['filter' => 'Auth']);
$routes->post('/save-attendance', 'HR\Attendance::SaveAttendance', ['filter' => 'Auth']);
$routes->post('/check-pin-employee', 'HR\Attendance::CheckPinEmployee', ['filter' => 'Auth']);

// payroll
$routes->get('/payroll', 'HR\Payroll::payroll', ['filter' => 'Auth']);
// $routes->get('/employee/all', 'Master\Employee::allEmployee', ['filter' => 'Auth']);

// formula payroll
$routes->get('/formula-payroll', 'HR\FormulaPayroll::formulaPayroll', ['filter' => 'Auth']);
$routes->get('/formula-payroll/create', 'HR\FormulaPayroll::createView', ['filter' => 'Auth']);
$routes->post('/formula-payroll/save', 'HR\FormulaPayroll::create', ['filter' => 'Auth']);
$routes->post('/formula-payroll/update', 'HR\FormulaPayroll::update', ['filter' => 'Auth']);

// pinjaman karyawan
$routes->get('/pinjaman-karyawan', 'HR\PinjamanKaryawan::pinjamanKaryawan', ['filter' => 'Auth']);
$routes->get('/pinjaman-karyawan/id/(:segment)', 'HR\PinjamanKaryawan::getById/$1', ['filter' => 'Auth']);
$routes->get('/pinjaman-karyawan/create', 'HR\PinjamanKaryawan::createView', ['filter' => 'Auth']);
$routes->get('/pinjaman-karyawan/all', 'HR\PinjamanKaryawan::allPinjamanKaryawan', ['filter' => 'Auth']);
$routes->post('/pinjaman-karyawan/save', 'HR\PinjamanKaryawan::create', ['filter' => 'Auth']);
$routes->post('/pinjaman-karyawan/update', 'HR\PinjamanKaryawan::update', ['filter' => 'Auth']);
$routes->post('/pinjaman-karyawan/delete', 'HR\PinjamanKaryawan::delete', ['filter' => 'Auth']);

// Perijinan
$routes->get('/form-perijinan', 'HR\Perijinan::perijinan', ['filter' => 'Auth']);
$routes->get('/form-perijinan/id/(:segment)', 'HR\Perijinan::getById/$1', ['filter' => 'Auth']);
$routes->get('/form-perijinan/create', 'HR\Perijinan::createView', ['filter' => 'Auth']);
$routes->get('/form-perijinan/all', 'HR\Perijinan::allPerijinan', ['filter' => 'Auth']);
$routes->post('/form-perijinan/save', 'HR\Perijinan::save', ['filter' => 'Auth']);
$routes->post('/form-perijinan/delete', 'HR\Perijinan::delete', ['filter' => 'Auth']);

// jam kerja
$routes->get('/jam-kerja', 'HR\JamKerja::jamKerja', ['filter' => 'Auth']);
$routes->get('/jam-kerja/all', 'HR\JamKerja::all', ['filter' => 'Auth']);

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
