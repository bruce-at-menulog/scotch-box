<?PHP
$config['database']['host'] = 'database.mlog-integ.je-labs.com';
$config['database']['port'] = '3306';
$config['database']['userName'] = 'menulog_new';
$config['database']['password'] = 'azFGPGFZ8i';
$config['database']['database'] = 'menulog_new';

$config['databaseRO']['host'] = 'database.mlog-integ.je-labs.com';
$config['databaseRO']['port'] = '3306';
$config['databaseRO']['userName'] = 'menulog_new';
$config['databaseRO']['password'] = 'azFGPGFZ8i';
$config['databaseRO']['database'] = 'menulog_new';

//$config['api']['url'] = 'http://api.menulog.local';

$config['smartyCompiledFolder'] = '/tmp/';

$config['websiteMode'] = 'development';

// To simulate NZ
//$config['forceWebsiteId'] = 4;

//i don't know why, 'localhost' just cannot work on my computer
//use staging as memcached server
$config['memcached']['enabled'] = false;

$config['memcached']['servers'] = array(
    0 =>
        array(
            0 => '127.0.0.1',
            1 => '11211',
        ),
);

$config['debug']['emailAddress'] = 'devs@menulog.com';
$config['debug']['errorHandler']['emailAddress'] = 'devs@menulog.com';
$config['debug']['databaseAbstraction']['method'] = 'display';

$config['listingsEmail'] = 'devs@menulog.com';

$config['staticContentHost'] = "";
$config['webDir']['javascript'] = "/javascript/";
$config['webDir']['styles'] = '/styles/';
//$config['appCssVersion'] = '';

$config['website']['features'] = array('pastOrders');

//$config['maintenance']['manualTurnOffSite'] = true;
