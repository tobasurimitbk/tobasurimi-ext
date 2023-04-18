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

/**
 * Get Page Login
 * Post Login
 * Get Logout
 */
$routes->get('/', 'User::login');
$routes->post('/login', 'User::doLogin');
$routes->get('/logout', 'User::doLogout');


/**
 * Get Page Dashboard
 */
$routes->get('/dashboard', 'Dashboard::dashboard', ['filter' => 'Auth']);

/**
 * Get Page Employee
 * Get All Employee
 * Get By id Employee
 * Save Employee
 * Update Employee
 * Delete Employee
 */

//  EMPLOYEE
$routes->get('/employee', 'Employee::employee', ['filter' => 'Auth']);
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

/**
 * Get Page Company
 * Get All Company
 * Get By Id Company
 * Save Company
 * Update Company
 * Delete Company
 */
$routes->get('/company', 'Company::company', ['filter' => 'Auth']);
$routes->get('/company/all', 'Company::allCompany', ['filter' => 'Auth']);
$routes->get('/company/id/(:segment)', 'Company::getByIdCompany/$1', ['filter' => 'Auth']);
$routes->post('/company/save', 'User::saveCompany', ['filter' => 'Auth']);
$routes->post('/company/update', 'User::updateCompany', ['filter' => 'Auth']);
$routes->post('/company/delete', 'Company::deleteCompany', ['filter' => 'Auth']);


/**
 * Get Page Management User
 * Get All Management User
 * Get By Id Management User
 * Save Management User
 * Update Management User
 * Delete Management User
 */
$routes->get('/user', 'User::user', ['filter' => 'Auth']);
$routes->get('/user/all', 'User::allUser', ['filter' => 'Auth']);
$routes->get('/user/id/(:segment)', 'User::getByIdUser/$1', ['filter' => 'Auth']);
$routes->post('/user/save', 'User::saveUser', ['filter' => 'Auth']);
$routes->post('/user/update', 'User::updateUser', ['filter' => 'Auth']);
$routes->post('/user/delete', 'User::deleteUser', ['filter' => 'Auth']);

/**
 * Get Page Role
 * Get All Role
 * Get By id Role
 * Save Role
 * Update Role
 * Delete Role
 */
$routes->get('/role', 'Role::role', ['filter' => 'Auth']);
$routes->get('/role/all', 'Role::allRole', ['filter' => 'Auth']);
$routes->get('/role/id/(:segment)', 'Role::getByIdRole/$1', ['filter' => 'Auth']);
$routes->post('/role/save', 'Role::saveRole', ['filter' => 'Auth']);
$routes->post('/role/update', 'Role::updateRole', ['filter' => 'Auth']);
$routes->post('/role/delete', 'Role::deleteRole', ['filter' => 'Auth']);

/**
 * Get Page Hak Akses
 * Get By Id Hak Akses
 * Save Hak Akses
 */
$routes->get('/akses', 'Akses::akses', ['filter' => 'Auth']);
$routes->get('/akses/id/(:segment)', 'Akses::getByIdAkses/$1', ['filter' => 'Auth']);
$routes->post('/akses/save', 'Akses::saveAkses', ['filter' => 'Auth']);

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
