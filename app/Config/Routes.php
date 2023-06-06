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
$routes->get('/', 'User::login');
$routes->post('/login', 'User::doLogin');
$routes->get('/change-company', 'User::changeCompany');
$routes->get('/logout', 'User::doLogout');

// DASHBOARD
$routes->get('/dashboard', 'Dashboard::dashboard', ['filter' => 'Auth']);

// MASTER DATA
// EMPLOYEE
$routes->get('/employee', 'Employee::employee', ['filter' => 'Auth']);
$routes->get('/employee/dropdown', 'Employee::dropdownEmployee', ['filter' => 'Auth']);
$routes->get('/employee/all', 'Employee::allEmployee', ['filter' => 'Auth']);
$routes->get('/employee/id/(:segment)', 'Employee::getByIdEmployee/$1', ['filter' => 'Auth']);
$routes->post('/employee/save', 'Employee::saveEmployee', ['filter' => 'Auth']);
$routes->post('/employee/update', 'Employee::updateEmployee', ['filter' => 'Auth']);
$routes->post('/employee/delete', 'Employee::deleteEmployee', ['filter' => 'Auth']);

// CUSTOMER
$routes->get('/customer', 'Customer::customer', ['filter' => 'Auth']);
$routes->get('/customer/all', 'Customer::allCustomer', ['filter' => 'Auth']);
$routes->get('/customer/id/(:segment)', 'Customer::getByIdCustomer/$1', ['filter' => 'Auth']);
$routes->post('/customer/save', 'Customer::saveCustomer', ['filter' => 'Auth']);
$routes->post('/customer/update', 'Customer::updateCustomer', ['filter' => 'Auth']);
$routes->post('/customer/delete', 'Customer::deleteCustomer', ['filter' => 'Auth']);

// WAREHOUSE
$routes->get('/warehouse', 'Warehouse::warehouse', ['filter' => 'Auth']);
$routes->get('/warehouse/all', 'Warehouse::allWarehouse', ['filter' => 'Auth']);
$routes->get('/warehouse/id/(:segment)', 'Warehouse::getByIdWarehouse/$1', ['filter' => 'Auth']);
$routes->post('/warehouse/save', 'Warehouse::saveWarehouse', ['filter' => 'Auth']);
$routes->post('/warehouse/update', 'Warehouse::updateWarehouse', ['filter' => 'Auth']);
$routes->post('/warehouse/delete', 'Warehouse::deleteWarehouse', ['filter' => 'Auth']);

// VENDOR
$routes->get('/vendor', 'Vendor::vendor', ['filter' => 'Auth']);
$routes->get('/vendor/all', 'Vendor::allVendor', ['filter' => 'Auth']);
$routes->get('/vendor/id/(:segment)', 'Vendor::getByIdVendor/$1', ['filter' => 'Auth']);
$routes->post('/vendor/save', 'Vendor::saveVendor', ['filter' => 'Auth']);
$routes->post('/vendor/update', 'Vendor::updateVendor', ['filter' => 'Auth']);
$routes->post('/vendor/delete', 'Vendor::deleteVendor', ['filter' => 'Auth']);

// SUPPLIER
$routes->get('/supplier', 'Supplier::supplier', ['filter' => 'Auth']);
$routes->get('/supplier/all', 'Supplier::allSupplier', ['filter' => 'Auth']);
$routes->get('/supplier/id/(:segment)', 'Supplier::getByIdSupplier/$1', ['filter' => 'Auth']);
$routes->post('/supplier/save', 'Supplier::saveSupplier', ['filter' => 'Auth']);
$routes->post('/supplier/update', 'Supplier::updateSupplier', ['filter' => 'Auth']);
$routes->post('/supplier/delete', 'Supplier::deleteSupplier', ['filter' => 'Auth']);

// PRODUK BARANG JADI
$routes->get('/produk-barang-jadi', 'ProdukBarangJadi::produkBarangJadi', ['filter' => 'Auth']);
$routes->get('/produk-barang-jadi/all', 'ProdukBarangJadi::allProdukBarangJadi', ['filter' => 'Auth']);
$routes->get('/produk-barang-jadi/id/(:segment)', 'ProdukBarangJadi::getByIdProdukBarangJadi/$1', ['filter' => 'Auth']);
$routes->post('/produk-barang-jadi/save', 'ProdukBarangJadi::saveProdukBarangJadi', ['filter' => 'Auth']);
$routes->post('/produk-barang-jadi/update', 'ProdukBarangJadi::updateProdukBarangJadi', ['filter' => 'Auth']);
$routes->post('/produk-barang-jadi/delete', 'ProdukBarangJadi::deleteProdukBarangJadi', ['filter' => 'Auth']);

// BARANG
$routes->get('/barang', 'Barang::barang', ['filter' => 'Auth']);

// DIVISI
$routes->get('/divisi', 'Divisi::divisi', ['filter' => 'Auth']);
$routes->get('/divisi/all', 'Divisi::allDivisi', ['filter' => 'Auth']);
$routes->get('/divisi/id/(:segment)', 'Divisi::getByIdDivisi/$1', ['filter' => 'Auth']);
$routes->post('/divisi/save', 'Divisi::saveDivisi', ['filter' => 'Auth']);
$routes->post('/divisi/update', 'Divisi::updateDivisi', ['filter' => 'Auth']);
$routes->post('/divisi/delete', 'Divisi::deleteDivisi', ['filter' => 'Auth']);

// COMPANY
$routes->get('/company', 'Company::company', ['filter' => 'Auth']);
$routes->get('/company/all', 'Company::allCompany', ['filter' => 'Auth']);
$routes->get('/company/id/(:segment)', 'Company::getByIdCompany/$1', ['filter' => 'Auth']);
$routes->post('/company/save', 'Company::saveCompany', ['filter' => 'Auth']);
$routes->post('/company/update', 'Company::updateCompany', ['filter' => 'Auth']);
$routes->post('/company/delete', 'Company::deleteCompany', ['filter' => 'Auth']);

// RAK
$routes->get('/penomoran-rak', 'PenomoranRak::penomoranRak', ['filter' => 'Auth']);
$routes->get('/penomoran-rak/all', 'PenomoranRak::allPenomoranRak', ['filter' => 'Auth']);
$routes->get('/penomoran-rak/id/(:segment)', 'PenomoranRak::getByIdPenomoranRak/$1', ['filter' => 'Auth']);
$routes->post('/penomoran-rak/save', 'PenomoranRak::savePenomoranRak', ['filter' => 'Auth']);
$routes->post('/penomoran-rak/update', 'PenomoranRak::updatePenomoranRak', ['filter' => 'Auth']);
$routes->post('/penomoran-rak/delete', 'PenomoranRak::deletePenomoranRak', ['filter' => 'Auth']);

// DROPDOWN
// CITY
$routes->get('/city/(:segment)', 'City::getCityByProvince/$1', ['filter' => 'Auth']);

// METADATA
$routes->get('/metadata/dropdown', 'Metadata::dropdownMetadata', ['filter' => 'Auth']);

// ACCOUNT
$routes->get('/kategori-account/dropdown', 'Account::dropdownKategoriAccount', ['filter' => 'Auth']);
$routes->get('/header-account/dropdown', 'Account::dropdownHeaderAccount', ['filter' => 'Auth']);

// ACCOUNT AND FINANCE
// ACCOUNT
$routes->get('/account', 'Account::account', ['filter' => 'Auth']);

$routes->get('/kategori-account/all', 'Account::allKategoriAccount', ['filter' => 'Auth']);
$routes->get('/kategori-account/id/(:segment)', 'Account::getByIdKategoriAccount/$1', ['filter' => 'Auth']);
$routes->post('/kategori-account/save', 'Account::saveKategoriAccount', ['filter' => 'Auth']);
$routes->post('/kategori-account/update', 'Account::updateKategoriAccount', ['filter' => 'Auth']);
$routes->post('/kategori-account/delete', 'Account::deleteKategoriAccount', ['filter' => 'Auth']);

$routes->get('/header-account/all', 'Account::allHeaderAccount', ['filter' => 'Auth']);
$routes->get('/header-account/id/(:segment)', 'Account::getByIdHeaderAccount/$1', ['filter' => 'Auth']);
$routes->post('/header-account/save', 'Account::saveHeaderAccount', ['filter' => 'Auth']);
$routes->post('/header-account/update', 'Account::updateHeaderAccount', ['filter' => 'Auth']);
$routes->post('/header-account/delete', 'Account::deleteHeaderAccount', ['filter' => 'Auth']);

$routes->get('/sub-account/all', 'Account::allSubAccount', ['filter' => 'Auth']);
$routes->get('/sub-account/id/(:segment)', 'Account::getByIdSubAccount/$1', ['filter' => 'Auth']);
$routes->post('/sub-account/save', 'Account::saveSubAccount', ['filter' => 'Auth']);
$routes->post('/sub-account/update', 'Account::updateSubAccount', ['filter' => 'Auth']);
$routes->post('/sub-account/delete', 'Account::deleteSubAccount', ['filter' => 'Auth']);

// SETTINGS
// USER
$routes->get('/user', 'User::user', ['filter' => 'Auth']);
$routes->get('/user/all', 'User::allUser', ['filter' => 'Auth']);
$routes->get('/user/all-user-company', 'User::allUserHaveCompany', ['filter' => 'Auth']);
$routes->get('/user/id/(:segment)', 'User::getByIdUser/$1', ['filter' => 'Auth']);
$routes->post('/user/save', 'User::saveUser', ['filter' => 'Auth']);
$routes->post('/user/update', 'User::updateUser', ['filter' => 'Auth']);
$routes->post('/user/delete', 'User::deleteUser', ['filter' => 'Auth']);

// ROLE
$routes->get('/role', 'Role::role', ['filter' => 'Auth']);
$routes->get('/role/all', 'Role::allRole', ['filter' => 'Auth']);
$routes->get('/role/id/(:segment)', 'Role::getByIdRole/$1', ['filter' => 'Auth']);
$routes->post('/role/save', 'Role::saveRole', ['filter' => 'Auth']);
$routes->post('/role/update', 'Role::updateRole', ['filter' => 'Auth']);
$routes->post('/role/delete', 'Role::deleteRole', ['filter' => 'Auth']);

// AKSES
$routes->get('/akses', 'Akses::akses', ['filter' => 'Auth']);
$routes->get('/akses/id', 'Akses::getAkses', ['filter' => 'Auth']);
$routes->post('/akses/save', 'Akses::saveAkses', ['filter' => 'Auth']);

// COMPANY ACCESS
$routes->get('/company-access', 'CompanyAccess::companyAccess', ['filter' => 'Auth']);

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
