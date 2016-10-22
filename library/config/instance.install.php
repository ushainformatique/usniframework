<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
$installed          = false;
$siteName           = "{{Application}}";
$adminTheme         = 'bootstrap';
$frontTheme         = '{{frontTheme}}';
$dsn                = 'mysql:host={{hostName}};port={{dbPort}};dbname={{dbName}};';
$unitTestDsn        = 'mysql:host={{hostName}};port={{dbPort}};dbname={{testDbName}};';
$functionalTestDsn  = 'mysql:host={{hostName}};port={{dbPort}};dbname={{testDbName}};';
$username           = '{{dbUserName}}';
$password           = '{{dbPassword}}';
$debug              = false;
$environment        = '{{environment}}';
$vendorPath         = '..';
$backendVendorPath  = '..' . DIRECTORY_SEPARATOR . '..';
//App details
$frontAppId         = 'front-app';
$frontAppName       = 'Front Application';
$frontDisplayName   = 'Front Application';
$backendAppId         = 'backend-app';
$backendAppName       = 'Backend Application';
$backendDisplayName   = 'Backend Application';
$poweredByUrl         = '#';
$poweredByName        = 'Company Name';
