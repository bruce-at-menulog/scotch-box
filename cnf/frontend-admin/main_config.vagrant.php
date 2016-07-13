<?PHP
# make sure this script isn't accessed directly
if (!defined('globalIT')) {
    header('HTTP/1.1 404 Not Found');
    exit;
}

//i don't think we want see so many distracting errors from old menulog in apache error log file
ini_set('log_errors', 0);

if (defined('WEB_ROOT') === false) {
    define('WEB_ROOT', realpath(dirname(__FILE__)));
}

if (defined('MENULOG2_PATH') === false) {
    define('MENULOG2_PATH', WEB_ROOT . '/../menulog2');
}

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
//	print mb_language ();

ini_set('soap.wsdl_cache_enabled', 0);


if (extension_loaded('newrelic')) {
    newrelic_set_appname("OLDMENULOG;MLFRONTEND");
}


/******** miscellaneous ********/


# start the timer
$timerStartTime = microtime();

# seed the mt_rand-om generator (only needed for win32 servers)
list ($usec, $sec) = explode(' ', microtime());
mt_srand((float)$sec + ((float)$usec * 100000));

$config['uniqueScriptId'] = md5(uniqid(mt_rand(), true));


# global it office or global it web server
$config['websiteMode'] = 'live';    # add extra development testing features (for global IT)
$config['developmentCookie'] = 'rchlosoeknatm';
$config['isLaptop'] = False;
$config['useTestingFeatures'] = False;    # add extra implementation testing features (for menulog staff)
$config['testingWebsiteCookie'] = 'testWebsiteId';
$config['crmIpAddresses'] = array('192.168.200.10', '192.168.200.20', '192.168.200.192', '192.168.200.251', '192.168.200.40', '192.168.200.36', '192.168.200.37', '192.168.200.50', '192.168.200.183', '192.168.225.18', '192.168.225.56', '54.66.169.89', '192.168.225.21');
error_reporting(0);


# live server - www.menulog.com.au (etc)
$config['website'] = 'production';
# staging server - menulogtest.globalit.net.au
if ((in_array(strtolower(trim(@$_SERVER['HTTP_HOST'])), array('menulogtest.globalit.net.au', 'menulogtest2.globalit.net.au', 'bookingstest.globalit.net.au')))
    || (strpos(@$_SERVER['PWD'], 'menulog.com.new') !== False)
)    # check this so that staging cron/background scripts still operate in the test environment
    $config['website'] = 'staging';
# development server - menulog.com.au.tim
else if (in_array(strtolower(trim(@$_SERVER['HTTP_HOST'])), array('menulog.com.au.tim', 'menulog.com.au.cron.tim'))) {
    $config['website'] = 'development';
    error_reporting(E_ALL);
} # laptop server
else if (in_array(strtolower(trim(@$_SERVER['HTTP_HOST'])), array('menulog.laptop')))
    $config['website'] = 'laptop';


# used when the page is being accessed in an ifram from the new website
#	if ((($config['websiteMode'] == 'development') && (@$_SERVER['REMOTE_ADDR'] == '192.168.1.117'))
#	|| (@$_SERVER['HTTP_HOST'] == 'bookings.menulog.com.au'))
switch (@$_SERVER['HTTP_HOST']) {
    case 'bookings.menulog.com.au':
    case 'bookingstest.globalit.net.au':
        $config['iframeContentOnlyMode'] = true;    # set this value so that the booking page doesn't draw the header/footer or venueInfo/Nav sections
        $config['menulog2DomainName'] = 'www.menulog.com.au';
        $config['forceWebsiteId'] = 1;
        break;
    case 'bookings.menulog.co.nz':
        $config['iframeContentOnlyMode'] = true;    # set this value so that the booking page doesn't draw the header/footer or venueInfo/Nav sections
        $config['menulog2DomainName'] = 'www.menulog.co.nz';
        $config['forceWebsiteId'] = 4;
        break;
}
#$config['iframeContentOnlyMode'] = true;


if (@$_REQUEST['generatingCache'])
    $config['temp']['generatingCache'] = true;
unset ($_REQUEST['generatingCache']);
unset ($_POST['generatingCache']);
unset ($_GET['generatingCache']);


# real website mode (based on the website mode file)
$config['realWebsiteMode'] = 'development';
if (file_exists($config['dir']['config'] . 'production_website'))
    $config['realWebsiteMode'] = 'production';
else if (file_exists($config['dir']['config'] . 'staging_website'))
    $config['realWebsiteMode'] = 'staging';


/******** settings ********/


# web urls + directories
$config['domain'] = '';
$config['plainDomain'] = '';
$config['host'] = '';
//HAProxy - terminatiog SSL - sets these
if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
    $config['overSSL'] = true;
} else {
    $config['overSSL'] = false;
}
if (@$_SERVER['HTTP_HOST'] != '') {
    $config['domain'] = ((isset ($_SERVER['HTTPS']) && !empty ($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . @$_SERVER['HTTP_HOST'];
    if ($config['overSSL']) {
        $config['domain'] = 'https://' . @$_SERVER['HTTP_HOST'];
    }
    $config['plainDomain'] = @$_SERVER['HTTP_HOST'];
    $config['host'] = strtolower(trim(@$_SERVER['HTTP_HOST']));
}

$config['hostsWithSSL'] = array('www.menulog.co.nz', 'www.menulog.com.au');

$config['webDir']['root'] = $config['domain'] . '/';

#	$config['webDir']['domainForImages']			= $config['webDir']['root'];
#	$config['webDir']['domainForJavascript']		= $config['webDir']['root'];
#	$config['webDir']['domainForCss']				= $config['webDir']['root'];
if (in_array(strtolower(trim(@$_SERVER['HTTP_HOST'])), array('menulog.com.au.tim', 'menulog.com.au.cron.tim', 'menulogtest.globalit.net.au', 'bookingstest.globalit.net.au'))) {
    $config['webDir']['domainForImages'] = $config['webDir']['root'];
    $config['webDir']['domainForJavascript'] = $config['webDir']['root'];
    $config['webDir']['domainForCss'] = $config['webDir']['root'];
} else {
    $config['webDir']['domainForImages'] = '//www.menulog.co.nz/';
    $config['webDir']['domainForJavascript'] = '//www.menulog.co.nz/';
    $config['webDir']['domainForCss'] = '//www.menulog.co.nz/';
}

$config['webDir']['admin'] = $config['webDir']['root'] . 'admin/';
$config['webDir']['adminShopProducts'] = $config['webDir']['root'] . 'admin/shop_products/';
$config['webDir']['adminShopProducts2'] = $config['webDir']['root'] . 'admin/shop_products2/';
$config['webDir']['adminShopSales'] = $config['webDir']['root'] . 'admin/shop_sales/';
$config['webDir']['adminShopOrders'] = $config['webDir']['root'] . 'admin/shop_orders/';
$config['webDir']['adminShopFreight'] = $config['webDir']['root'] . 'admin/shop_freight/';
$config['webDir']['adminAccounts'] = $config['webDir']['root'] . 'admin/accounts/';
$config['webDir']['adminPageContentElements'] = $config['webDir']['root'] . 'admin/page_content_elements/';
$config['webDir']['adminEmails'] = $config['webDir']['root'] . 'admin/emails/';
$config['webDir']['adminNewsScroller'] = $config['webDir']['root'] . 'admin/news_scroller/';
$config['webDir']['adminSuppliers'] = $config['webDir']['root'] . 'admin/suppliers/';
$config['webDir']['adminGalleries'] = $config['webDir']['root'] . 'admin/galleries/';
$config['webDir']['adminOrganisations'] = $config['webDir']['root'] . 'admin/organisations/';
$config['webDir']['adminReports'] = $config['webDir']['root'] . 'admin/reports/';                # restaurant specific
$config['webDir']['adminMenus'] = $config['webDir']['root'] . 'admin/menus/';                    # restaurant specific
$config['webDir']['adminMenus2'] = $config['webDir']['root'] . 'admin/menus2/';                # restaurant specific
$config['webDir']['adminSchedules'] = $config['webDir']['root'] . 'admin/schedules/';                # restaurant specific
$config['webDir']['adminSchedules2'] = $config['webDir']['root'] . 'admin/schedules2/';            # restaurant specific
$config['webDir']['adminReviews'] = $config['webDir']['root'] . 'admin/reviews/';                # restaurant specific
$config['webDir']['adminRatings'] = $config['webDir']['root'] . 'admin/ratings/';                # restaurant specific
$config['webDir']['adminRestaurantSpecials'] = $config['webDir']['root'] . 'admin/restaurant_specials/';    # restaurant specific
$config['webDir']['adminAwards'] = $config['webDir']['root'] . 'admin/awards/';                # restaurant specific
$config['webDir']['adminBookings'] = $config['webDir']['root'] . 'admin/bookings/';                # restaurant specific
$config['webDir']['adminTakeaway'] = $config['webDir']['root'] . 'admin/takeaway/';                # restaurant specific
$config['webDir']['adminGeneralEvents'] = $config['webDir']['root'] . 'admin/general_events/';        # restaurant specific
$config['webDir']['adminTranslate'] = $config['webDir']['root'] . 'admin/translatex/';
$config['webDir']['adminNotes'] = $config['webDir']['root'] . 'admin/notes/';
$config['webDir']['adminTemplates'] = $config['webDir']['root'] . 'admin/templates/';
$config['webDir']['adminCustomEmails'] = $config['webDir']['root'] . 'admin/custom_emails/';
$config['webDir']['adminSiteContent'] = $config['webDir']['root'] . 'admin/site_content/';
$config['webDir']['adminAttributes'] = $config['webDir']['root'] . 'admin/attributes/';            # restaurant specific
$config['webDir']['adminTestingTools'] = $config['webDir']['root'] . 'admin/testing_tools/';            # restaurant specific
$config['webDir']['adminCompetitions'] = $config['webDir']['root'] . 'admin/competitions/';
$config['webDir']['adminVouchers'] = $config['webDir']['root'] . 'admin/vouchers/';
$config['webDir']['adminDiningVouchers'] = $config['webDir']['root'] . 'admin/dining_vouchers/';        # restaurant specific

$config['webDir']['frontAccounts'] = $config['webDir']['root'] . 'accounts/';
$config['webDir']['frontCorporate'] = $config['webDir']['root'] . 'corporate/';                    # restaurant specific
$config['webDir']['frontCompetitions'] = $config['webDir']['root'] . 'competitions/';                # restaurant specific
$config['webDir']['frontOrganisations'] = $config['webDir']['root'] . '';
$config['webDir']['frontSiteContent'] = $config['webDir']['root'] . '';
$config['webDir']['frontShop'] = $config['webDir']['root'] . '';
$config['webDir']['frontTakeaway'] = $config['webDir']['root'] . 'takeaway/';                    # restaurant specific
$config['webDir']['frontVouchers'] = $config['webDir']['root'] . 'vouchers/';                    # restaurant specific
$config['webDir']['frontGeneralEvents'] = $config['webDir']['root'] . '';                                # restaurant specific
$config['webDir']['frontUpdateInfo'] = $config['webDir']['root'] . 'update_info/';                    # restaurant specific

$config['webDir']['facebookApp1'] = $config['webDir']['root'] . 'facebook/app1/';                # facebook application 1

$config['webDir']['ajaxAdmin'] = $config['webDir']['root'] . 'ajax/admin/';
$config['webDir']['ajaxFront'] = $config['webDir']['root'] . 'ajax/front/';

$config['webDir']['api'] = $config['webDir']['root'] . 'api/';

$config['webDir']['images'] = $config['webDir']['domainForImages'] . 'images/front/';
$config['webDir']['images2'] = $config['webDir']['domainForImages'] . 'images/front2/';
$config['webDir']['adImages'] = $config['webDir']['domainForImages'] . 'images/ads/';
$config['webDir']['websiteEmailImages1'] = $config['webDir']['domainForImages'] . 'images/website_emails/email1/';
$config['webDir']['fileTypeIconImages'] = $config['webDir']['domainForImages'] . 'images/front/file_type_icons/';
$config['webDir']['ratingImages'] = $config['webDir']['domainForImages'] . 'images/front/rating_images/';
$config['webDir']['adminImages'] = $config['webDir']['domainForImages'] . 'images/admin/';
$config['webDir']['adminShopImages'] = $config['webDir']['domainForImages'] . 'images/admin/shop/';
$config['webDir']['adminOrganisationImages'] = $config['webDir']['domainForImages'] . 'images/admin/organisation/';
$config['webDir']['adminScreenshotImages'] = $config['webDir']['domainForImages'] . 'images/admin/screenshots/';
$config['webDir']['emailTemplateImages'] = $config['webDir']['domainForImages'] . 'images/email_templates/';
$config['webDir']['customSitesTemplate1Images'] = $config['webDir']['domainForImages'] . 'images/custom_sites/template1/';
$config['webDir']['customSitesTemplate2Images'] = $config['webDir']['domainForImages'] . 'images/custom_sites/template2/';
$config['webDir']['customSitesTemplate3Images'] = $config['webDir']['domainForImages'] . 'images/custom_sites/template3/';
$config['webDir']['customSitesTemplate4Images'] = $config['webDir']['domainForImages'] . 'images/custom_sites/template4/';

$config['webDir']['manualDocuments'] = $config['webDir']['root'] . 'documents/manuals/';

$config['webDir']['flash'] = $config['webDir']['root'] . 'flash/';
$config['webDir']['styles'] = $config['webDir']['domainForCss'] . 'styles/';
$config['webDir']['customSitesTemplate1Styles'] = $config['webDir']['root'] . 'styles/custom_sites/template1/';
$config['webDir']['customSitesTemplate2Styles'] = $config['webDir']['root'] . 'styles/custom_sites/template2/';
$config['webDir']['customSitesTemplate3Styles'] = $config['webDir']['root'] . 'styles/custom_sites/template3/';
$config['webDir']['customSitesTemplate4Styles'] = $config['webDir']['root'] . 'styles/custom_sites/template4/';
$config['webDir']['javascript'] = $config['webDir']['domainForJavascript'];


# javascript hostname
$config['jsWebDir']['root'] = '//www.menulog.co.nz/';
$config['jsWebDir']['javascript'] = $config['jsWebDir']['root'] . 'javascript/';


# directories used when setting cookies
$config['cookieDir']['root'] = '/';
#	$config['cookieDir']['admin']	= $config['cookieDir']['root'].'admin/';
$config['cookieDir']['admin'] = $config['cookieDir']['root'];

$config['alreadyPlacedTakeawayOrderCookie'] = 'aoehcruoeharu';


# directories
$config['dir']['globalToolsRoot'] = $config['dir']['config'] . 'global_tools/tools/';    # directory to the globalIT tools root
$config['dir']['globalToolsConfig'] = $config['dir']['config'] . 'global_tools/';            # directory to the globalIT tools config settings

//	if (in_array (@$_SERVER['SERVER_ADDR'], array ('122.252.6.130'))) {
//		$config['dir']['globalToolsRoot']						= '/home/globaltools2.1/tools2.1/tools/';			# directory to the globalIT tools root
//		$config['dir']['globalToolsConfig']						= '/home/globaltools2.1/tools2.1/';					# directory to the globalIT tools config settings
//	}


$config['dir']['commonClasses'] = $config['dir']['globalToolsRoot'] . 'includes/classes/';
$config['dir']['commonAjaxClasses'] = $config['dir']['globalToolsRoot'] . 'includes/classes/ajax/';
$config['dir']['commonDbiClasses'] = $config['dir']['globalToolsRoot'] . 'includes/classes/dbi/';
$config['dir']['commonIfhClasses'] = $config['dir']['globalToolsRoot'] . 'includes/classes/ifh/';
$config['dir']['commonJpgraphClasses'] = $config['dir']['globalToolsRoot'] . 'includes/classes/jpgraph/';
$config['dir']['commonFunctions'] = $config['dir']['globalToolsRoot'] . 'includes/functions/';
$config['dir']['commonCode'] = $config['dir']['globalToolsRoot'] . 'includes/code/';
$config['dir']['commonConfigs'] = $config['dir']['globalToolsRoot'] . 'includes/code/configs/';

# class directories
$config['dir']['classes'] = $config['dir']['config'] . 'includes/classes/';
$config['dir']['ajaxClasses'] = $config['dir']['config'] . 'includes/classes/ajax/';
$config['dir']['dbiClasses'] = $config['dir']['config'] . 'includes/classes/dbi/';
$config['dir']['ifhClasses']['adminAccounts'] = $config['dir']['config'] . 'includes/classes/ifh/admin_accounts/';
$config['dir']['ifhClasses']['adminPageContentElements'] = $config['dir']['config'] . 'includes/classes/ifh/admin_page_content_elements/';
$config['dir']['ifhClasses']['adminEmails'] = $config['dir']['config'] . 'includes/classes/ifh/admin_emails/';
$config['dir']['ifhClasses']['adminShopProducts'] = $config['dir']['config'] . 'includes/classes/ifh/admin_shop_products/';
$config['dir']['ifhClasses']['adminShopProducts2'] = $config['dir']['config'] . 'includes/classes/ifh/admin_shop_products2/';
$config['dir']['ifhClasses']['adminShopSales'] = $config['dir']['config'] . 'includes/classes/ifh/admin_shop_sales/';
$config['dir']['ifhClasses']['adminShopOrders'] = $config['dir']['config'] . 'includes/classes/ifh/admin_shop_orders/';
$config['dir']['ifhClasses']['adminShopFreight'] = $config['dir']['config'] . 'includes/classes/ifh/admin_shop_freight/';
$config['dir']['ifhClasses']['adminNewsScroller'] = $config['dir']['config'] . 'includes/classes/ifh/admin_news_scroller/';
$config['dir']['ifhClasses']['adminSuppliers'] = $config['dir']['config'] . 'includes/classes/ifh/admin_suppliers/';
$config['dir']['ifhClasses']['adminGalleries'] = $config['dir']['config'] . 'includes/classes/ifh/admin_galleries/';
$config['dir']['ifhClasses']['adminOrganisations'] = $config['dir']['config'] . 'includes/classes/ifh/admin_organisations/';
$config['dir']['ifhClasses']['adminReports'] = $config['dir']['config'] . 'includes/classes/ifh/admin_reports/';                # restaurant specific
$config['dir']['ifhClasses']['adminMenus'] = $config['dir']['config'] . 'includes/classes/ifh/admin_menus/';                    # restaurant specific
$config['dir']['ifhClasses']['adminMenus2'] = $config['dir']['config'] . 'includes/classes/ifh/admin_menus2/';                # restaurant specific
$config['dir']['ifhClasses']['adminSchedules'] = $config['dir']['config'] . 'includes/classes/ifh/admin_schedules/';                # restaurant specific
$config['dir']['ifhClasses']['adminSchedules2'] = $config['dir']['config'] . 'includes/classes/ifh/admin_schedules2/';            # restaurant specific
$config['dir']['ifhClasses']['adminReviews'] = $config['dir']['config'] . 'includes/classes/ifh/admin_reviews/';                # restaurant specific
$config['dir']['ifhClasses']['adminRatings'] = $config['dir']['config'] . 'includes/classes/ifh/admin_ratings/';                # restaurant specific
$config['dir']['ifhClasses']['adminRestaurantSpecials'] = $config['dir']['config'] . 'includes/classes/ifh/admin_restaurant_specials/';    # restaurant specific
$config['dir']['ifhClasses']['adminAwards'] = $config['dir']['config'] . 'includes/classes/ifh/admin_awards/';                # restaurant specific
$config['dir']['ifhClasses']['adminBookings'] = $config['dir']['config'] . 'includes/classes/ifh/admin_bookings/';                # restaurant specific
$config['dir']['ifhClasses']['adminTakeaway'] = $config['dir']['config'] . 'includes/classes/ifh/admin_takeaway/';                # restaurant specific
$config['dir']['ifhClasses']['adminTranslate'] = $config['dir']['config'] . 'includes/classes/ifh/admin_translate/';
#	$config['dir']['ifhClasses']['frontContentPages']			= $config['dir']['config'].'includes/classes/ifh/front_content_pages/';
$config['dir']['ifhClasses']['adminNotes'] = $config['dir']['config'] . 'includes/classes/ifh/admin_notes/';
$config['dir']['ifhClasses']['adminTemplates'] = $config['dir']['config'] . 'includes/classes/ifh/admin_templates/';
$config['dir']['ifhClasses']['adminCustomEmails'] = $config['dir']['config'] . 'includes/classes/ifh/admin_custom_emails/';
$config['dir']['ifhClasses']['adminSiteContent'] = $config['dir']['config'] . 'includes/classes/ifh/admin_site_content/';
$config['dir']['ifhClasses']['adminGeneralEvents'] = $config['dir']['config'] . 'includes/classes/ifh/admin_general_events/';
$config['dir']['ifhClasses']['adminAttributes'] = $config['dir']['config'] . 'includes/classes/ifh/admin_attributes/';            # restaurant specific
$config['dir']['ifhClasses']['adminTestingTools'] = $config['dir']['config'] . 'includes/classes/ifh/admin_testing_tools/';            # restaurant specific
$config['dir']['ifhClasses']['adminCompetitions'] = $config['dir']['config'] . 'includes/classes/ifh/admin_competitions/';
$config['dir']['ifhClasses']['adminVouchers'] = $config['dir']['config'] . 'includes/classes/ifh/admin_vouchers/';
$config['dir']['ifhClasses']['adminDiningVouchers'] = $config['dir']['config'] . 'includes/classes/ifh/admin_dining_vouchers/';

$config['dir']['ifhClasses']['frontAccounts'] = $config['dir']['config'] . 'includes/classes/ifh/front_accounts/';                # restaurant specific
$config['dir']['ifhClasses']['frontBookings'] = $config['dir']['config'] . 'includes/classes/ifh/front_bookings/';                # restaurant specific
$config['dir']['ifhClasses']['frontTakeaway'] = $config['dir']['config'] . 'includes/classes/ifh/front_takeaway/';                # restaurant specific
$config['dir']['ifhClasses']['frontVouchers'] = $config['dir']['config'] . 'includes/classes/ifh/front_vouchers/';                # restaurant specific
$config['dir']['ifhClasses']['frontCompetitions'] = $config['dir']['config'] . 'includes/classes/ifh/front_competitions/';            # restaurant specific
$config['dir']['ifhClasses']['frontOrganisations'] = $config['dir']['config'] . 'includes/classes/ifh/front_organisations/';            # restaurant specific
$config['dir']['ifhClasses']['frontVoting'] = $config['dir']['config'] . 'includes/classes/ifh/front_voting/';                # restaurant specific
$config['dir']['ifhClasses']['frontSiteContent'] = $config['dir']['config'] . 'includes/classes/ifh/front_site_content/';            # restaurant specific
$config['dir']['ifhClasses']['frontShop'] = $config['dir']['config'] . 'includes/classes/ifh/front_shop/';
$config['dir']['ifhClasses']['frontGeneralEvents'] = $config['dir']['config'] . 'includes/classes/ifh/front_general_events/';        # restaurant specific
$config['dir']['ifhClasses']['frontUpdateInfo'] = $config['dir']['config'] . 'includes/classes/ifh/front_update_info/';            # restaurant specific

# functions directory
$config['dir']['functions'] = $config['dir']['config'] . 'includes/functions/';
# plain code directory (ie. not functions or classes,  just straight code)
$config['dir']['code'] = $config['dir']['config'] . 'includes/code/';
$config['dir']['internationalisationCode'] = $config['dir']['config'] . 'includes/code/internationalisation/menulog.com.au/';
$config['dir']['internationalisationCodeDefault'] = $config['dir']['internationalisationCode'];
$config['dir']['affiliateCode'] = Null;    # this is generated later when an affiliate is decided upon
# extra config directory
$config['dir']['config2'] = $config['dir']['config'] . 'includes/code/configs/';
$config['dir']['cachableCode'] = $config['dir']['config'] . 'includes/code/cachable/';
# form include directories
#	$config['dir']['frontFormIncludes']['main']					= $config['dir']['root'].'includes/form_scripts_admin/';
#	$config['dir']['adminFormIncludes']['main']					= $config['dir']['root'].'includes/form_scripts_admin/';
$config['dir']['formIncludes']['adminAccounts'] = $config['dir']['config'] . 'includes/form_scripts/admin_accounts/';
$config['dir']['formIncludes']['adminPageContentElements'] = $config['dir']['config'] . 'includes/form_scripts/admin_page_content_elements/';
$config['dir']['formIncludes']['adminEmails'] = $config['dir']['config'] . 'includes/form_scripts/admin_emails/';
$config['dir']['formIncludes']['adminShopProducts'] = $config['dir']['config'] . 'includes/form_scripts/admin_shop_products/';
$config['dir']['formIncludes']['adminShopProducts2'] = $config['dir']['config'] . 'includes/form_scripts/admin_shop_products2/';
$config['dir']['formIncludes']['adminShopSales'] = $config['dir']['config'] . 'includes/form_scripts/admin_shop_sales/';
$config['dir']['formIncludes']['adminShopOrders'] = $config['dir']['config'] . 'includes/form_scripts/admin_shop_orders/';
$config['dir']['formIncludes']['adminShopFreight'] = $config['dir']['config'] . 'includes/form_scripts/admin_shop_freight/';
$config['dir']['formIncludes']['adminNewsScroller'] = $config['dir']['config'] . 'includes/form_scripts/admin_news_scroller/';
$config['dir']['formIncludes']['adminSuppliers'] = $config['dir']['config'] . 'includes/form_scripts/admin_suppliers/';
$config['dir']['formIncludes']['adminGalleries'] = $config['dir']['config'] . 'includes/form_scripts/admin_galleries/';
$config['dir']['formIncludes']['adminOrganisations'] = $config['dir']['config'] . 'includes/form_scripts/admin_organisations/';
$config['dir']['formIncludes']['adminReports'] = $config['dir']['config'] . 'includes/form_scripts/admin_reports/';                # restaurant specific
$config['dir']['formIncludes']['adminMenus'] = $config['dir']['config'] . 'includes/form_scripts/admin_menus/';                # restaurant specific
$config['dir']['formIncludes']['adminMenus2'] = $config['dir']['config'] . 'includes/form_scripts/admin_menus2/';                # restaurant specific
$config['dir']['formIncludes']['adminSchedules'] = $config['dir']['config'] . 'includes/form_scripts/admin_schedules/';            # restaurant specific
$config['dir']['formIncludes']['adminSchedules2'] = $config['dir']['config'] . 'includes/form_scripts/admin_schedules2/';            # restaurant specific
$config['dir']['formIncludes']['adminReviews'] = $config['dir']['config'] . 'includes/form_scripts/admin_reviews/';                # restaurant specific
$config['dir']['formIncludes']['adminRatings'] = $config['dir']['config'] . 'includes/form_scripts/admin_ratings/';                # restaurant specific
$config['dir']['formIncludes']['adminRestaurantSpecials'] = $config['dir']['config'] . 'includes/form_scripts/admin_restaurant_specials/';    # restaurant specific
$config['dir']['formIncludes']['adminAwards'] = $config['dir']['config'] . 'includes/form_scripts/admin_awards/';                # restaurant specific
$config['dir']['formIncludes']['adminBookings'] = $config['dir']['config'] . 'includes/form_scripts/admin_bookings/';                # restaurant specific
$config['dir']['formIncludes']['adminTakeaway'] = $config['dir']['config'] . 'includes/form_scripts/admin_takeaway/';                # restaurant specific
$config['dir']['formIncludes']['adminTranslate'] = $config['dir']['config'] . 'includes/form_scripts/admin_translate/';
$config['dir']['formIncludes']['adminNotes'] = $config['dir']['config'] . 'includes/form_scripts/admin_notes/';
$config['dir']['formIncludes']['adminTemplates'] = $config['dir']['config'] . 'includes/form_scripts/admin_templates/';
$config['dir']['formIncludes']['adminCustomEmails'] = $config['dir']['config'] . 'includes/form_scripts/admin_custom_emails/';
$config['dir']['formIncludes']['adminSiteContent'] = $config['dir']['config'] . 'includes/form_scripts/admin_site_content/';
$config['dir']['formIncludes']['adminGeneralEvents'] = $config['dir']['config'] . 'includes/form_scripts/admin_general_events/';
$config['dir']['formIncludes']['adminAttributes'] = $config['dir']['config'] . 'includes/form_scripts/admin_attributes/';
$config['dir']['formIncludes']['adminTestingTools'] = $config['dir']['config'] . 'includes/form_scripts/admin_testing_tools/';
$config['dir']['formIncludes']['adminCompetitions'] = $config['dir']['config'] . 'includes/form_scripts/admin_competitions/';
$config['dir']['formIncludes']['adminVouchers'] = $config['dir']['config'] . 'includes/form_scripts/admin_vouchers/';
$config['dir']['formIncludes']['adminDiningVouchers'] = $config['dir']['config'] . 'includes/form_scripts/admin_dining_vouchers/';

$config['dir']['formIncludes']['frontAccounts'] = $config['dir']['config'] . 'includes/form_scripts/front_accounts/';                # restaurant specific
$config['dir']['formIncludes']['frontBookings'] = $config['dir']['config'] . 'includes/form_scripts/front_bookings/';                # restaurant specific
$config['dir']['formIncludes']['frontTakeaway'] = $config['dir']['config'] . 'includes/form_scripts/front_takeaway/';                # restaurant specific
$config['dir']['formIncludes']['frontVouchers'] = $config['dir']['config'] . 'includes/form_scripts/front_vouchers/';                # restaurant specific
$config['dir']['formIncludes']['frontCompetitions'] = $config['dir']['config'] . 'includes/form_scripts/front_competitions/';            # restaurant specific
$config['dir']['formIncludes']['frontOrganisations'] = $config['dir']['config'] . 'includes/form_scripts/front_organisations/';        # restaurant specific
$config['dir']['formIncludes']['frontVoting'] = $config['dir']['config'] . 'includes/form_scripts/front_voting/';        # restaurant specific
$config['dir']['formIncludes']['frontSiteContent'] = $config['dir']['config'] . 'includes/form_scripts/front_site_content/';            # restaurant specific
$config['dir']['formIncludes']['frontShop'] = $config['dir']['config'] . 'includes/form_scripts/front_shop/';
$config['dir']['formIncludes']['frontGeneralEvents'] = $config['dir']['config'] . 'includes/form_scripts/front_general_events/';        # restaurant specific
$config['dir']['formIncludes']['frontUpdateInfo'] = $config['dir']['config'] . 'includes/form_scripts/front_update_info/';            # restaurant specific

# files directories
$config['dir']['tempFiles'] = $config['dir']['config'] . 'uploaded_files/temp/';
$config['dir']['generatedImages'] = $config['dir']['config'] . 'uploaded_files/generated_images/';
$config['dir']['emailAttachments'] = $config['dir']['config'] . 'uploaded_files/email_attachments/';
$config['dir']['mapImages'] = $config['dir']['config'] . 'uploaded_files/map_images/';
$config['dir']['shopCategoryImages'] = $config['dir']['config'] . 'uploaded_files/shop_category_images/';
$config['dir']['shopProductImages'] = $config['dir']['config'] . 'uploaded_files/shop_product_images/';
$config['dir']['shopProductItemImages'] = $config['dir']['config'] . 'uploaded_files/shop_product_item_images/';
$config['dir']['supplierLogoImages'] = $config['dir']['config'] . 'uploaded_files/supplier_logo_images/';
$config['dir']['galleryFiles'] = $config['dir']['config'] . 'uploaded_files/gallery_files/';
$config['dir']['galleryThumbnailImages'] = $config['dir']['config'] . 'uploaded_files/gallery_thumbnail_images/';
$config['dir']['userPhotoImages'] = $config['dir']['config'] . 'uploaded_files/user_photo_images/';
$config['dir']['affiliateXml'] = $config['dir']['config'] . 'uploaded_files/affiliate_xml/';                                    # restaurant specific
$config['dir']['affiliateCsv'] = $config['dir']['config'] . 'uploaded_files/affiliate_csv/';                                    # restaurant specific
$config['dir']['affiliateCsv2'] = $config['dir']['config'] . 'uploaded_files/affiliate_csv2/';                                # restaurant specific
$config['dir']['foodItemImages'] = $config['dir']['config'] . 'uploaded_files/food_item_images/';                                # restaurant specific
$config['dir']['venueImages'] = $config['dir']['config'] . 'uploaded_files/venue_images/';                                    # restaurant specific
$config['dir']['venueVideos'] = $config['dir']['config'] . 'uploaded_files/venue_videos/';                                    # restaurant specific
$config['dir']['awardLogos'] = $config['dir']['config'] . 'uploaded_files/award_logos/';                                    # restaurant specific
$config['dir']['restaurantSpecialDocuments'] = $config['dir']['config'] . 'uploaded_files/restaurant_special_documents/';                    # restaurant specific
$config['dir']['functionMenuDocuments'] = $config['dir']['config'] . 'uploaded_files/function_menu_documents/';                        # restaurant specific
$config['dir']['functionGroupBookingDocuments'] = $config['dir']['config'] . 'uploaded_files/function_group_booking_documents/';                # restaurant specific
$config['dir']['functionGroupCancellationDocuments'] = $config['dir']['config'] . 'uploaded_files/function_group_booking_cancellation_documents/';    # restaurant specific
$config['dir']['bookingDocuments'] = $config['dir']['config'] . 'uploaded_files/booking_documents/';                                # restaurant specific
$config['dir']['customEmailAttachments'] = $config['dir']['config'] . 'uploaded_files/custom_email_attachments/';
$config['dir']['takeawayXml'] = $config['dir']['config'] . 'uploaded_files/take_away_xml/';                                    # restaurant specific
$config['dir']['regularMenuDocuments'] = $config['dir']['config'] . 'uploaded_files/regular_menu_documents/';                        # restaurant specific
$config['dir']['generalEventImages'] = $config['dir']['config'] . 'uploaded_files/general_event_images/';                            # general event specific
$config['dir']['documentDownload'] = $config['dir']['config'] . 'uploaded_files/document_download/';                                #
$config['dir']['totalTravelXmlArchive'] = $config['dir']['config'] . 'uploaded_files/archive_xml_total_travel/';                        # restaurant specific
$config['dir']['googleXmlArchive'] = $config['dir']['config'] . 'uploaded_files/google_xml_archive/';                            # restaurant specific
$config['dir']['sensisXmlArchive'] = $config['dir']['config'] . 'uploaded_files/sensis_xml_archive/';                            # restaurant specific
$config['dir']['eplannerXmlArchive'] = $config['dir']['config'] . 'uploaded_files/eplanner_xml_archive/';                            # restaurant specific
$config['dir']['exampleXmlArchive'] = $config['dir']['config'] . 'uploaded_files/arhcive_xml_example/';                            # restaurant specific
$config['dir']['generalXmlArchive'] = $config['dir']['config'] . 'uploaded_files/archive_xml_general/';                            # restaurant specific
$config['dir']['lifestyleXmlArchive'] = $config['dir']['config'] . 'uploaded_files/archive_xml_lifestyle/';                            # restaurant specific
$config['dir']['eatabilityXmlArchive'] = $config['dir']['config'] . 'uploaded_files/archive_xml_eatability/';                        # restaurant specific
$config['dir']['takeawayGeneralXmlArchive'] = $config['dir']['config'] . 'uploaded_files/archive_xml_takeaway_general/';                    # restaurant specific
$config['dir']['presidentialCardXmlArchive'] = $config['dir']['config'] . 'uploaded_files/archive_xml_presidential_card/';                    # restaurant specific
$config['dir']['dimmiXmlDownloadArchive'] = $config['dir']['config'] . 'uploaded_files/archive_xml_dimmi_download/';                    # restaurant specific
$config['dir']['logs'] = $config['dir']['config'] . 'uploaded_files/logs/';
$config['dir']['crmImages'] = $config['dir']['config'] . 'uploaded_files/crm_images/';
$config['dir']['venueVoucherImages'] = $config['dir']['config'] . 'uploaded_files/venue_voucher_images/';                            # restaurant specific

$config['dir']['frontTakeaway'] = $config['dir']['root'] . 'takeaway/';
$config['dir']['frontVouchers'] = $config['dir']['root'] . 'vouchers/';

# wysiwyg directory
$config['dir']['fckEditor'] = $config['dir']['root'] . 'fckeditor2_2/';
# crontab script directory
$config['dir']['cronScript'] = $config['dir']['config'] . 'cron/';
# background script directory
$config['dir']['backgroundScript'] = $config['dir']['config'] . 'background/';
# page cache directory
$config['dir']['pageCache'] = $config['dir']['config'] . 'page_cache/';

# google local directory
$config['dir']['googleLocalOutput'] = $config['dir']['root'] . 'google/local/';
# total travel local directory
$config['dir']['totalTravelOutput'] = $config['dir']['root'] . 'feeds/total_travel/';
# citysearch
$config['dir']['citysearchOutput'] = $config['dir']['root'] . 'feeds/citysearch/';
# eplanner
$config['dir']['eplannerOutput'] = $config['dir']['root'] . 'feeds/eplanner/';
# example
$config['dir']['exampleOutput'] = $config['dir']['root'] . 'feeds/example/';
$config['dir']['generalOutput'] = $config['dir']['root'] . 'feeds/general/';
# lifestyle
$config['dir']['lifestyleOutput'] = $config['dir']['root'] . 'feeds/lifestyle/';
# eatability
$config['dir']['eatabilityOutput'] = $config['dir']['root'] . 'feeds/eatability/';
# takeawayGeneral
$config['dir']['takeawayGeneralOutput'] = $config['dir']['root'] . 'feeds/takeaway/';
# presidential card
$config['dir']['presidentialCardOutput'] = $config['dir']['root'] . 'feeds/presidential_card/';

# facebook
$config['dir']['facebookApp1'] = $config['dir']['root'] . 'facebook/app1/';                                                    # restaurant specific

# directories for the email renderer to use when rendering emails
$config['dir']['emailTemplateImages'] = $config['dir']['root'] . 'images/email_templates/';
$config['dir']['adminScreenshotImages'] = $config['dir']['root'] . 'images/admin/screenshots/';
$config['dir']['websiteEmailImages1'] = $config['dir']['root'] . 'images/website_emails/email1/';
$config['dir']['styles'] = $config['dir']['root'] . 'styles/';
$config['dir']['flash'] = $config['dir']['root'] . 'flash/';
$config['dir']['javascript'] = $config['dir']['root'] . 'javascript/';
# directory used for accessing the file type icons
$config['dir']['images'] = $config['dir']['root'] . 'images/front/';
$config['dir']['fileTypeIconImages'] = $config['dir']['images'] . 'file_type_icons/';

# dictionary directory
$config['dir']['dictionary'] = $config['dir']['config'] . 'includes/dictionaries/';


# settings for backwards compatability with global tools 2.1
# directory definitions that are used in global tools 2.1
$config['dir']['extraConfig'] = $config['dir']['commonConfigs'];
$config['dir']['functionIncludes'] = $config['dir']['commonFunctions'];
$config['dir']['classIncludes'] = $config['dir']['commonClasses'];


# incomplete form settings
$config['incompleteFormData']['timeoutPeriod'] = 60 * 60 * 24 * 14;    # 14 days
$config['incompleteFormData']['performCleanup'] = False;
$config['incompleteFormData']['addExtraIpFields'] = True;


# file upload information
$config['fileUpload']['systemMaxFileSize'] = 1048576 * 100;    # 100 MB


# debugging settings,  used when problems occur on the site to determine what to do
$config['debug']['systemName'] = 'menulog';                                            # the name used when debugging to identify this system (such as in error email subjects) (not displayed to users)

$config['debug']['emailAddress'] = 'mlDevAlerts@menulog.com';    # the email address to send email errors to

$config['debug']['databaseAbstraction']['showQueries'] = False;                                # display all queries on the screen
$config['debug']['databaseAbstraction']['method'] = 'email';                                # display / email
$config['debug']['databaseAbstraction']['emailAddress'] = $config['debug']['emailAddress'];    # the email address to send email errors to
$config['debug']['databaseAbstraction']['logSlowQueries'] = false;                            # log slow queries
$config['debug']['databaseAbstraction']['slowQueryThreshold'] = 5;                            # the number of seconds before logging a query
$config['debug']['databaseAbstraction']['slowQueryLogTable'] = 'slow_query_logs';            # slow query log table

$config['debug']['fileHandler']['showFileMovement'] = False;                                # display any file changes to the screen

$config['debug']['errorHandler']['emailAddress'] = 'mlDevAlerts@menulog.com';                # where php error emails are sent

$config['debug']['siteSettings']['developmentAccountIds'] = array(1);                        # account ids to show development 'extras' for
#	$config['debug']['siteSettings']['developmentAccountIds'] = array ();						# account ids to show development 'extras' for

$config['debug']['cache']['allowLoad'] = true;                                                # allow cached elements to be loaded from the database
$config['debug']['cache']['allowSave'] = true;                                                # allow cached elements to be saved into the database
if ($config['websiteMode'] == 'development') {
    $config['debug']['cache']['allowLoad'] = false;                                            # allow cached elements to be loaded from the database
    $config['debug']['cache']['allowSave'] = false;                                            # allow cached elements to be saved into the database
}

date_default_timezone_set('Australia/NSW');
$config['serverTimezone'] = $tempTimezone = date_default_timezone_get();


$config['cron']['emailAddress'] = 'mldevalerts@menulog.com';    # the email address to send cron emails to

$config['smartyCompiledFolder'] = '/dev/shm/smarty';

/******** database ********/

# database connection settings
# menulog webserver integ database
$config['database']['host'] = 'database.mlog-integ.je-labs.com';
$config['database']['port'] = '3306';
$config['database']['userName'] = 'menulog_old';
$config['database']['password'] = 'azFGPGFZ8i';
$config['database']['database'] = 'menulog_new';

$config['databaseRO']['host'] = 'database-readonly.mlog-integ.je-labs.com';
$config['databaseRO']['port'] = '3306';
$config['databaseRO']['userName'] = 'menulog_old';
$config['databaseRO']['password'] = 'azFGPGFZ8i';
$config['databaseRO']['database'] = 'menulog_new';

# menulog webserver live database
# db1 MENULOG_NEW database login details
/*	$config['database']['host']		= 'menulog-db-prod.cggpvmkcl6iw.ap-southeast-2.rds.amazonaws.com';
	$config['database']['port']		= '3306';
	$config['database']['userName']	= 'menulog_old';
	$config['database']['password']	= 'azFGPGFZ8i';
	$config['database']['database']	= 'menulog_new';

	$config['databaseRO']['host']		= 'menulogdbreadonly.cggpvmkcl6iw.ap-southeast-2.rds.amazonaws.com:3306';
	$config['databaseRO']['port']		= '3306';
	$config['databaseRO']['userName']	= 'menulog_old';
	$config['databaseRO']['password']	= 'azFGPGFZ8i';
	$config['databaseRO']['database']	= 'menulog_new';*/

//this user can only see ratings > 3
$config['adminForHighRatingOnly'][] = 'edith_outsource';
$config['highRatingThreshold'] = 3;

//load local config if exists
if (file_exists(WEB_ROOT . '/main_config.local.php')) {
    require_once WEB_ROOT . '/main_config.local.php';
}

$dbRO = null;

# connect to the database (new mysqli based connector)
require_once($config['dir']['commonClasses'] . 'database_abstraction_mysqli.php');
$db = new databaseAbstractionMysqli ($config['database']['host'], $config['database']['userName'], $config['database']['password'], $config['database']['database'], $config['database']['port']);
if (!@$db->stat()) {
    # if the connection failed then try to fall over gracefully
    if (file_exists($config['dir']['root'] . 'under_maintenance.php')) {
        header('HTTP/1.1 302 Found');
        header('location: ' . $config['webDir']['root'] . 'under_maintenance.php');
        exit;
    } else {
        die ('A connection could not be established with the database.');
    }
}
$db->set_log_slow_queries(true);

# make sure the connection to the read-only database was successful
if (is_null($dbRO)) {
    $dbRO =& $db;
}
#
$query = "SET NAMES utf8";
$db->query($query);
if (isset ($dbRO)) {
    $dbRO->query($query);
}

//initialise some global staff
$config['registry'] = new stdClass();

//smarty
require_once WEB_ROOT . '/includes/library/Smarty/Smarty.class.php';
$smarty = new Smarty();
$smarty->setTemplateDir(WEB_ROOT . '/templates');
$smarty->setCompileDir($config['smartyCompiledFolder'] . '/templates_c');
$smarty->setCacheDir($config['smartyCompiledFolder'] . '/cache');
$smarty->autoload_filters = array('output' => array('trimwhitespace'));
$config['registry']->smarty = $smarty;


/******** error handler ********/

# php error handler
if ((!isset ($config['useInclude']['commonFunctions']['error_handler.php'])) || ($config['useInclude']['commonFunctions']['error_handler.php'])) {
    if ($config['websiteMode'] == 'live')
        require_once($config['dir']['commonFunctions'] . 'error_handler.php');
}

# check that the domain is within the below list
if (!in_array(strtolower(trim(@$_SERVER['HTTP_HOST'])), array(
    # australia
    'www.menulog.com.au',
    'bookings.menulog.com.au',
    'admin.menulog.com',
    # thailand
    'thailand.menulog.com',
    # hong kong
    'www.menulog.hk',
    # new zealand
    'www.menulog.co.nz',
    'bookings.menulog.co.nz',
    # singapore
    'singapore.menulog.com',
    # united kingdom
    'www.menulog.co.uk',
    # ireland
    'ireland.menulog.com',
    # united states
    'www.menulog.com',
    # spain
    'www.menulog.es',
    # india
    'www.menulog.in',
    # russia
#		'www.zakazmenu.ru',
    # france
    'fr.menulog.com',
    # israel
#		'www.menulog.co.il',
    # test sites
    'menulog.com.au.tim',
    'menulog.mobile.tim',
    'menulogtest.globalit.net.au',
    'bookingstest.globalit.net.au',
    # laptop
    'menulog.laptop',
    # js.menulog.com.au (for javascript files)
    'js.menulog.com.au',
    'images.menulog.com.au',
    '53f2aadf93028.oldml.comi',
    'bt.menulog-old.local',
    'menulog-old.local'
))
) {
    require($config['dir']['config'] . 'check_domain.php');
}

# ask for ht-auth login details if accessing menulogtest.globalit.net.au
if (in_array(@$_SERVER['HTTP_HOST'], array('menulogtest.globalit.net.au', 'bookingstest.globalit.net.au'))) {
    if ((strtolower(@$_SERVER['PHP_AUTH_USER']) != 'demo') || (strtolower(@$_SERVER['PHP_AUTH_PW']) != 'demo')) {
        if (!@$config['temp']['generatingCache']) {
            header('HTTP/1.0 401 Unauthorized');
            header('WWW-Authenticate: Basic realm="' . $_SERVER['HTTP_HOST'] . '"');
            exit;
        }
    }
}


# if the user has arrived here from yellow pages,  record this so the website can act as the yellowpages.menulog.com.au affiliate
if ((strpos(@$_SERVER['HTTP_REFERER'], 'http://yellowpages.com.au') !== false) || (strpos(@$_SERVER['HTTP_REFERER'], 'http://www.yellowpages.com.au') !== false)) {
    setcookie('mlAffiliateId', '42', 0, $config['cookieDir']['root']);
    $_COOKIE['mlAffiliateId'] = 42;
    $config['papAffiliateId'] = '058F7003';
    $config['papBannerId'] = '';
}
# if the user has arrived here from citysearch,  record this so the website can act as the citysearch.menulog.com.au affiliate
if ((strpos(@$_SERVER['HTTP_REFERER'], 'http://citysearch.com.au') !== false) || (strpos(@$_SERVER['HTTP_REFERER'], 'http://www.citysearch.com.au') !== false)) {
    $config['papAffiliateId'] = '0573C270';
    $config['papBannerId'] = '';
}
# if the user has arrived here from eatability,  record this so the website will tell the crm that this user's takeaway orders were referred to menulog from eatability
$affiliateReferrerDomainNames = array(
    'http://eatability.com.au',
    'http://www.eatability.com.au',
    'http://yourrestaurants.com.au',
    'http://www.yourrestaurants.com.au',
    'http://mumsdelivery.com.au',
    'http://www.mumsdelivery.com.au',
    'http://timeoutsydney.com.au',
    'http://www.timeoutsydney.com.au',
    'http://bestrestaurants.com.au',
    'http://www.bestrestaurants.com.au'
);
foreach ($affiliateReferrerDomainNames as $affiliateReferrerDomainName) {

    if (strpos(@$_SERVER['HTTP_REFERER'], $affiliateReferrerDomainName) !== false) {
#			setcookie ('mlAffiliateId', '42', 0, $config['cookieDir']['root']);
#			$_COOKIE['mlAffiliateId'] = 42;
#			$config['papAffiliateId']	= '058F7003';
#			$config['papBannerId']		= '';
        setcookie('affiliateReferrer', $_SERVER['HTTP_REFERER'], 0, $config['cookieDir']['root']);
        $_COOKIE['affiliateReferrer'] = $_SERVER['HTTP_REFERER'];
    }
}


#$config['websiteMode'] = 'live';
#$config['useTestingFeatures'] = false;

/********  ********/

/**
 * SELECT ipAddress, comment
 * FROM `r_org_ratings`
 * WHERE `comment` LIKE '%http://%'
 * GROUP BY ipAddress
 * LIMIT 1000
 * /**/


# banned ip addresses
if ((in_array(@$_SERVER['REMOTE_ADDR'], array(
#		'216.145.54.158',	# added on 23/2/11 by Tim for trying to get /etc/passwd etc type data from the server (it was yahoo testing the menulog website)
        '59.165.65.73',        # added on 17/3/09 by request of Gary for placing takeaway orders C8B4WE
        '59.164.4.15',        # added on 10/12/08 by request of Dan for placing takeaway orders VATC65 and 5Q98HD
#		'58.168.228.130',	# added on 04/06/08 for accessing http://www.menulog.com.au/melbourne_bowling_club/contact 32290 times between 2008-05-02 15:04:19 and 2008-05-02 16:31:38
#		'220.245.178.137',	# added on 01/06/08 for bad takeaway order
        '12.181.204.36', '12.3.83.19', '121.180.122.185', '121.22.5.33', '121.254.193.119', '121.83.0.77', '122.18.81.117', '122.197.64.90', '124.3.110.18', '125.244.77.2', '125.244.82.2', '125.5.158.85', '125.7.44.167', '128.122.130.135', '128.134.178.102', '128.134.60.14', '134.93.200.26', '137.101.46.134', '137.193.187.175', '139.91.70.144', '140.123.19.200', '140.124.40.191', '141.150.98.50', '147.102.5.3', '147.83.140.80', '148.233.135.73', '148.233.159.58', '151.13.37.225', '156.143.48.12', '168.187.0.41', '170.211.8.153', '190.144.3.226', '190.24.128.221', '192.167.22.24', '192.192.35.235', '193.191.145.30', '193.88.7.239', '193.92.70.208', '194.44.54.93', '194.8.75.107', '194.83.70.20', '194.95.59.130', '195.10.210.139', '195.189.145.20', '195.229.242.53', '195.251.117.228', '196.219.107.218', '196.35.158.181', '198.203.81.16', '198.54.202.130', '199.216.209.253', '200.123.180.89', '200.144.57.4', '200.150.68.2', '200.172.74.178', '200.226.134.53', '200.28.145.16', '200.65.0.25', '200.65.127.161', '200.88.114.166', '201.155.79.32', '201.16.212.57', '201.70.170.20', '202.154.255.5', '202.182.82.8', '202.28.27.4', '202.44.32.9', '202.70.201.34', '202.71.107.162', '203.13.128.101', '203.133.150.95', '203.158.221.227', '203.162.2.133', '203.162.2.134', '203.162.2.135', '203.162.2.136', '203.162.2.137', '203.166.96.234', '203.247.156.16', '203.9.222.40', '206.123.92.224', '206.220.40.8', '206.51.229.7', '207.41.73.13', '208.108.221.87', '210.0.202.30', '210.22.158.132', '210.251.208.156', '210.42.140.5', '210.87.254.42', '211.138.198.6', '211.138.198.7', '211.142.116.205', '211.232.92.231', '211.31.36.56', '211.9.254.74', '212.107.116.238', '212.165.130.14', '212.213.221.246', '212.219.203.226', '212.71.33.85', '212.97.182.249', '213.145.148.130', '213.178.224.165', '213.184.31.2', '213.225.101.146', '213.42.2.22', '216.129.105.10', '217.110.67.125', '217.149.246.98', '217.162.3.172', '217.71.145.15', '218.104.219.232', '218.104.51.245', '218.12.72.155', '218.189.236.226', '218.25.36.169', '218.26.219.186', '218.50.52.210', '219.121.115.225', '219.127.119.4', '219.235.112.193', '220.11.32.161', '220.160.127.221', '220.227.218.46', '220.238.60.86', '221.251.182.100', '221.79.44.54', '222.231.8.174', '222.231.8.175', '24.132.9.63', '38.117.88.72', '38.117.88.77', '38.99.101.235', '58.191.168.205', '58.22.101.123', '58.65.238.26', '58.85.184.206', '58.85.200.109', '58.87.34.27', '59.106.138.10', '59.147.98.254', '59.166.118.245', '59.99.16.35', '60.190.240.73', '60.21.161.73', '61.132.27.68', '61.132.51.10', '61.133.87.226', '61.150.66.18', '61.192.11.197', '61.221.149.121', '61.49.16.102', '61.6.163.32', '61.6.163.33', '61.6.163.34', '61.7.64.231', '62.0.61.162', '62.165.34.253', '62.193.205.210', '62.21.28.163', '62.50.15.93', '64.187.97.233', '64.242.145.154', '66.110.115.230', '66.45.124.11', '66.8.85.132', '66.8.94.74', '67.131.248.106', '68.37.120.200', '69.74.57.14', '70.84.240.34', '70.86.141.82', '71.2.112.137', '72.249.182.199', '74.208.11.169', '75.127.77.55', '75.40.7.10', '76.205.188.197', '77.108.116.236', '78.129.202.2', '78.129.208.65', '78.129.208.75', '78.47.78.82', '79.188.194.122', '80.187.221.89', '80.227.1.100', '80.58.205.32', '80.58.205.35', '80.58.205.47', '80.58.205.50', '80.58.205.98', '80.58.205.99', '81.202.105.211', '81.63.140.37', '82.101.202.92', '82.114.160.35', '82.200.48.107', '82.201.193.90', '82.208.147.81', '82.240.100.184', '82.240.140.209', '83.167.111.114', '83.240.154.164', '83.42.138.32', '84.16.169.5', '84.237.162.226', '84.90.101.148', '85.185.36.133', '85.31.230.215', '85.90.98.182', '85.91.81.188', '87.195.37.120', '87.236.233.52', '87.250.140.151', '87.98.136.154', '87.99.112.143', '88.198.250.142', '88.198.40.79', '88.26.179.219', '88.57.116.18', '89.149.195.24', '89.149.197.252', '89.3.62.143', '89.76.44.167', '89.79.75.155', '90.153.128.13', '91.121.200.220', '91.142.49.226', '91.67.211.171', '91.74.160.18', '91.74.168.66', '91.90.17.206', '92.48.99.12', # added on 19/5/08 for nusiance restaurant ratings (from search of existing ratings for "%http://%",  minus a couple of legitimate ones)
#		'221.79.44.54',		# added on 13/12/07 for nusiance restaurant ratings
#		'38.117.88.72',		# added on 19/11/07 for nusiance restaurant ratings
#		'210.251.208.156',	# added on 19/11/07 for nusiance restaurant ratings
#		'61.192.11.197',	# added on 16/11/07 for nusiance restaurant ratings
#		'211.142.116.205',	# added on 16/11/07 for nusiance restaurant ratings
#		'190.144.3.226',	# added on 7/11/07 for nusiance restaurant ratings
#		'58.179.157.246',	# added on 10/4/07 for nusiance real-time bookings
#		'58.179.155.94',	# added on 10/4/07 for nusiance real-time bookings
#		'58.179.155.225',	# added on 10/4/07 for nusiance real-time bookings
#		'58.179.148.12'		# added on 10/4/07 for nusiance real-time bookings
    ))) || (isset ($_COOKIE['ecoecuo']))
) {
    setcookie('ecoecuo', '1', time() + (60 * 60 * 24 * 4), '/');    # nusiance cookie - 4 day
#		header ('HTTP/1.1 403 Forbidden');
    header('HTTP/1.1 404 Not Found');
    exit;
}


/********  ********/


# see if the user needs to be forwarded somewhere
if ((@$_GET['redirectDomain'] != '') && ($_GET['redirectDomain'] != @$_SERVER['HTTP_HOST'])) {

    # create the new uri
    $newUri = $_GET['redirectDomain'] . @$_SERVER['REQUEST_URI'];

    # remove the redirectDomain=xxx from the query string
    $temp2 = explode('?', $newUri);
    if (count($temp2)) {

        $temp3 = explode('&', array_pop($temp2));
        foreach ($temp3 as $index => $value) {
            if ($value == 'redirectDomain=' . $_GET['redirectDomain'])
                unset ($temp3[$index]);
        }

        if (count($temp3))
            $temp2[] = implode('&', $temp3);

        $newUri = implode('?', $temp2);
    }

    header('location: http://' . $newUri);
    exit;
}


/******** functions ********/


# ssl management
require_once($config['dir']['functions'] . 'ssl.php');


/******** object setup ********/


# create the user data handler object
if ((isset ($config['useInclude']['commonClasses']['user_data_handler.php'])) && ($config['useInclude']['commonClasses']['user_data_handler.php'])) {
    require_once($config['dir']['commonClasses'] . 'user_data_handler.php');
    $udh = new userDataHandler ();
}


# create the message handler object
if ((isset ($config['useInclude']['commonClasses']['message_handler.php'])) && ($config['useInclude']['commonClasses']['message_handler.php'])) {
    require_once($config['dir']['commonClasses'] . 'message_handler.php');
    $mh = new messageHandler ();
    if ($udh->request('successMessage') !== False) {
        $temp = base64_decode($udh->request('successMessage'));
        # is there more than one message?
        if (preg_match('/^a:[0-9]+:{/', $temp))
            $temp = unserialize($temp);
        else
            $temp = (array)$temp;
        foreach ($temp as $message)
            $mh->add_success_message($message);
    }
    if ($udh->request('errorMessage') !== False) {
        $temp = base64_decode($udh->request('errorMessage'));
        # is there more than one message?
        if (preg_match('/^a:[0-9]+:{/', $temp))
            $temp = unserialize($temp);
        else
            $temp = (array)$temp;
        foreach ($temp as $message)
            $mh->add_error_message($message);
    }
}

# load up the generic database item class upon which most other classes are based
if ((isset ($config['useInclude']['commonDbiClasses']['generic_database_item.php'])) && ($config['useInclude']['commonDbiClasses']['generic_database_item.php']))
    require_once($config['dir']['commonDbiClasses'] . 'generic_database_item.php');


# cache element
if ((isset ($config['useInclude']['commonClasses']['cache_element.php'])) && ($config['useInclude']['commonClasses']['cache_element.php']))
    require_once($config['dir']['classes'] . 'custom_cache_element.php');


# memcached
#	if ((isset ($config['useInclude']['commonClasses']['cache_element.php'])) && ($config['useInclude']['commonClasses']['cache_element.php']))
require_once($config['dir']['config2'] . 'memcached.php');


# the return uri handling object - to simplify the process of propogating the information needed to know which page to return to when submitting a form (that may span more than one page)
if ((isset ($config['useInclude']['commonClasses']['return_uri_handler.php'])) && ($config['useInclude']['commonClasses']['return_uri_handler.php']))
    require_once($config['dir']['commonClasses'] . 'return_uri_handler.php');


# create the html form creator object
if ((isset ($config['useInclude']['commonClasses']['html_form_creator.php'])) && ($config['useInclude']['commonClasses']['html_form_creator.php']))
    require_once($config['dir']['classes'] . 'html_form_creator.php');


# account class
if ((isset ($config['useInclude']['dbiClasses']['account.php'])) && ($config['useInclude']['dbiClasses']['account.php']))
    require_once($config['dir']['dbiClasses'] . 'account.php');


# session manager
if ((isset ($config['useInclude']['commonClasses']['session_manager.php'])) && ($config['useInclude']['commonClasses']['session_manager.php'])) {
    require_once($config['dir']['commonClasses'] . 'session_manager.php');

    $sessionManager = new sessionManager ();
    # set the lowest account type as the default.
    # if another account type is to be the default it will be specifed in config2.php,  or within the actual main script itself
    $sessionManager->require_login();
    $sessionManager->set_default_account_type('siteUserAccount');
    $sessionManager->set_default_timeout_period(60 * 60 * 6);    # 6 hours
    # specify the account types that exist in the system so that they -can- be recognised by the sesssionManager
    # to allow or disallow particular account types update the config2.php file in each directory,  or update the script directly if only that file is affected
    $sessionManager->set_anonymous_cookie_name('sessionId');
    $sessionManager->add_account_type('superAdminAccount', 'saSessionId', $config['webDir']['admin'] . 'login.php', 31536000, $config['cookieDir']['admin']);
    $sessionManager->add_account_type('adminAccount', 'aSessionId', $config['webDir']['admin'] . 'login.php', 31536000, $config['cookieDir']['admin']);
    $sessionManager->add_account_type('translatorAccount', 'tSessionId', $config['webDir']['admin'] . 'login.php', 31536000, $config['cookieDir']['admin']);
    $sessionManager->add_account_type('restaurantStaffAccount', 'rsSessionId', $config['webDir']['root'] . 'restaurant_login.php', 31536000, $config['cookieDir']['admin']);
    $sessionManager->add_account_type('conciergeUserAccount', 'cuSessionId', $config['webDir']['frontAccounts'] . 'login.php', 31536000, $config['cookieDir']['root']);
    $sessionManager->add_account_type('vipUserAccount', 'vuSessionId', $config['webDir']['frontAccounts'] . 'login.php', 31536000, $config['cookieDir']['root']);
    $sessionManager->add_account_type('siteUserAccount', 'suSessionId', $config['webDir']['frontAccounts'] . 'login.php', 31536000, $config['cookieDir']['root']);
#		$sessionManager->add_account_type ('affiliateAccount', 'afSessionId', $config['webDir']['root'].'login.php', 31536000, $config['cookieDir']['root']);

    $languageManager = new languageManager ();
    $languageManager->store_session_manager_object($sessionManager);
}

//	if ((isset ($config['useInclude']['commonClasses']['timezone_manager.php'])) && ($config['useInclude']['commonClasses']['timezone_manager.php'])) {
require_once($config['dir']['commonClasses'] . 'timezone_manager.php');

$timezoneManager = new timezoneManager ();
//	}


# google analytics link generator
if ((isset ($config['useInclude']['dbiClasses']['google_analytics_link_generator.php'])) && ($config['useInclude']['dbiClasses']['account.php']))
    require_once($config['dir']['classes'] . 'google_analytics_link_generator.php');


# include any directory specific settings now,  after main_config has been run
if ((isset ($config['useInclude']['currentDir']['config2.php'])) && ($config['useInclude']['currentDir']['config2.php'])) {
    if (file_exists('config2.php'))
        require_once('config2.php');
}


/**
 * require_once ($config['dir']['commonClasses'].'timer.php');
 *
 * $timer = new timer ();
 *
 *
 * $temp = array ();
 * #    $temp2 = new restaurantStaffAccount ();
 * $timer->start ();
 * for ($count = 0; $count < 20000; $count++)
 * #        $temp[] = clone ($temp2);
 * $temp[] = new restaurantStaffAccount ();
 * $timer->stop ();
 * print '20000 create object: '.$timer->get_time_ms ().'<br />'."\n";
 *
 * print (memory_get_usage () / 1024) / 1024;
 * exit;
 *
 *
 *
 *
 *
 *
 *
 * /**
 * require_once ($config['dir']['commonClasses'].'timer.php');
 *
 * $timer = new timer ();
 *
 * $timer->start ();
 * $restaurantStaffAccount2 = new restaurantStaffAccount ();
 * $timer->stop ();
 * print 'create object: '.$timer->get_time_ms ().'<br />'."\n";
 *
 * $timer->start ();
 * $restaurantStaffAccount2 = new restaurantStaffAccount ();
 * $timer->stop ();
 * print 'create object: '.$timer->get_time_ms ().'<br />'."\n";
 *
 * $timer->start ();
 * $restaurantStaffAccount = clone ($restaurantStaffAccount2);
 * $timer->stop ();
 * print 'clone object: '.$timer->get_time_ms ().'<br />'."\n";
 *
 * $timer->start ();
 * $restaurantStaffAccount->load_from_database (42263);
 * $timer->stop ();
 * print 'load dbi: '.$timer->get_time_ms ().'<br />'."\n";
 *
 * $restaurantStaffAccount->sv ('loginName', '5dfYeegi');
 * $timer->start ();
 * $restaurantStaffAccount->update_in_database ();
 * $timer->stop ();
 * print 'update in database: '.$timer->get_time_ms ().'<br />'."\n";
 *
 * exit;
 * /**/


# use a restaurant site template
$config['customSiteId'] = 0;

#	if ($config['websiteMode'] == 'development') {
#		if (@$_SERVER['REMOTE_ADDR'] == '192.168.1.99') {
#			$config['customSiteId'] = 1;
#		}
#	}

$customSiteLoadedOk = False;
if ((isset ($db))
    && ((!isset ($udh)) || ($udh->server('REMOTE_ADDR') != '192.168.1.117'))
) {

    $query = "SELECT * "
        . "FROM custom_sites "
        . "WHERE domainName = '" . $db->escape(trim(@$_SERVER['HTTP_HOST'])) . "' "
        . "AND deleted != 'Y' "
        . "LIMIT 1";
    $result = $db->query($query);
    if ($row = $db->fetch_assoc($result)) {

        # if this is the development server then pretend that the restaurant is example restaurant (5330)
        if (in_array(strtolower(trim(@$_SERVER['HTTP_HOST'])), array('menulog.com.au.tim', 'menulog.com.au.cron.tim', 'menulog.mobile.tim')))
            $row['organisationId'] = 5330;

        $config['customSiteId'] = $row['id'];
        $config['siteTemplateInfo'] = $row;

        require_once($config['dir']['dbiClasses'] . 'account.php');

        $customSiteOrganisationDBI = new organisationDBI ();
        if ($customSiteOrganisationDBI->load_from_database($config['siteTemplateInfo']['organisationId'])) {

            require_once($config['dir']['dbiClasses'] . 'address.php');

            $customSiteAddressDBI = new addressDBI ();
            if ($customSiteAddressDBI->load_from_database($customSiteOrganisationDBI->gv('addressId'))) {

                # check to make sure the restaurant is doing takeaway
                if ((!$customSiteOrganisationDBI->is_in_system('takeAwaySystem'))
                    || ($customSiteOrganisationDBI->gv('takingTakeawayOrders') != 'Y')
                    || ($customSiteOrganisationDBI->gv('closed') == 'Y')
                    || ($customSiteOrganisationDBI->gv('status') != 'active')
                ) {

                    # find out which website this restaurant belongs to
                    $query = "SELECT websites.domainName "
                        . "FROM websites "
                        . "JOIN website_country_links "
                        . "ON website_country_links.websiteId = websites.id "
                        . "AND countryId = " . $db->escape_integer($customSiteAddressDBI->gv('countryId')) . " "
                        . "AND website_country_links.deleted != 'Y' "
                        . "WHERE websites.deleted != 'Y' "
                        . "LIMIT 1";
                    $result = $db->query($query);
                    # and send the user back to that website
                    if ($row = $db->fetch_assoc($result))
                        header('location: http://' . $row['domainName']);
                    else
                        header('location: http://www.menulog.com.au');
                    exit;
                }

                # force template 4 to use the russian website
                if ($row['templateId'] == 4)
                    $config['forceWebsiteId'] = 11;

                $customSiteLoadedOk = True;
            }
        }
    }
}

if (!$customSiteLoadedOk) {
    $config['customSiteId'] = 0;
    unset ($config['siteTemplateInfo']);
    unset ($customSiteOrganisationDBI);
    unset ($customSiteAddressDBI);
}


require($config['dir']['config2'] . 'suburb_domain_list.php');
$config['isSuburbDeliveryDomainName'] = false;
if ((isset ($_SERVER['HTTP_HOST'])) && (isset ($tempSuburbIds[strtolower(trim($_SERVER['HTTP_HOST']))]))) {
    $config['temp']['noNav'] = true;
    $config['temp']['whenNoNavForceTopSectionToShow'] = true;
    $config['isSuburbDeliveryDomainName'] = true;
}

require($config['dir']['config2'] . 'direct_restaurant_domain_list.php');
$config['isDirectRestaurantDomainName'] = false;
if ((isset ($_SERVER['HTTP_HOST'])) && (isset ($tempOrganisationIds[strtolower(trim($_SERVER['HTTP_HOST']))]))) {
    $config['temp']['noNav'] = true;
    $config['isDirectRestaurantDomainName'] = true;
}

require MENULOG2_PATH . '/includes/configs/staticDomain.php';
