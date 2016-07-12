<?php

# location
$config['env']['server'] = 'vagrant';
$config['env']['mode'] = 'development';    # development / staging / production


# environment
$config['debug']['systemName'] = 'menulog2';            # used in emails etc to identify the website
$config['debug']['emailAddress'] = 'devs@menulog.com';
$config['env']['serverTimezone'] = 'Australia/Sydney';    # the server's default timezone (this is different to the timezone of each website,  eg. menulog.com.au,  zakazmenu.ru etc which is handled separately)

$config['debug']['errorHandler']['useEmailReporting'] = false;                    # send php errors to the below email address?
$config['debug']['errorHandler']['emailAddress'] = 'devs@menulog.com';    # separated by commas
$config['cron']['emailAddress'] = 'devs@menulog.com';
$config['env']['requireHttpAuth'] = false;    # ask the user for login / password before being able to access the site?


# directory settings
$config['env']['dir']['globalCoreRoot'] = $config['dir']['root'] . 'global_core/';
$config['env']['dir']['globalToolsRoot'] = $config['dir']['root'] . 'global_tools_for_core/';
$config['env']['dir']['originalWebsiteRoot'] = $config['dir']['root'] . '../menulog.com/';
#	$config['env']['dir']['originalWebsiteRoot'] = $config['dir']['root'].'../menulog.com.new/';


# database settings
$config['env']['db']['write']['servers'] = array();
$config['env']['db']['write']['servers'][] = array('host' => 'database.mlog-integ.je-labs.com', 'user' => 'menulog_new', 'pwd' => 'azFGPGFZ8i', 'dbName' => 'menulog_new', 'importance' => 1);
$config['env']['db']['write']['debugMethod'] = 'email';    # email / display / none
$config['env']['db']['write']['debugMethodEmailAddresses'] = array('devs@menulog.com');

$config['env']['db']['read']['servers'] = array();
$config['env']['db']['read']['servers'][] = array('host' => 'database.mlog-integ.je-labs.com', 'user' => 'menulog_new', 'pwd' => 'azFGPGFZ8i', 'dbName' => 'menulog_new', 'importance' => 1);    # copy this line to add more servers to the pool
$config['env']['db']['read']['debugMethod'] = 'email';    # email / display / none
$config['env']['db']['read']['debugMethodEmailAddresses'] = array('devs@menulog.com');

$config['env']['db']['readConnectionCanUseWriteConnection'] = false;    # can the RO connection actually re-use the RW connection instead of creating a new, separate connection?


# environment manager
$config['env']['em']['useCustomSettings'] = true;    # use the below $em settings or not?  they are ignored if this is false

$config['env']['em']['websiteId'] = 1;    # australia
$config['env']['em']['languageId'] = 1;    # english

$config['env']['em']['defaultHostname'] = 'menulog.local';        # returned when no pool is specified,  useful when cron scripts run and they need to know the website's domain name
$config['env']['em']['bookingHostname'] = 'menulog.local';    # the hostname of the menulog1 website,  used within the the menulog2 website (eg. for iframes)

$config['env']['em']['hostnamePools'] = array();    # list of pool names that the $em will use when generating urls
#	$config['env']['em']['hostnamePools']['img'] = array ('img1.domain.com.au', 'img2.domain.com.au', 'img3.domain.com.au');
#	$config['env']['em']['hostnamePools']['css'] = array ('css1.domain.com.au', 'css2.domain.com.au', 'css3.domain.com.au');
#	$config['env']['em']['hostnamePools']['js'] = array ('js1.domain.com.au', 'js2.domain.com.au', 'js3.domain.com.au');
#	$config['env']['em']['hostnamePools']['flash'] = array ('flash1.domain.com.au', 'flash2.domain.com.au', 'flash3.domain.com.au');
#	$config['env']['em']['hostnamePools']['doc'] = array ('documents1.domain.com.au', 'documents3.domain.com.au', 'documents2.domain.com.au');

$config['env']['em']['mapHttpsToHttp'] = true;    # force any https:// urls generated with the $em to be http:// instead
require($config['dir']['configs'] . 'staticDomain.php');
$config['env']['em']['staticDomain'] = $config['staticDomain'];

# memcached
$config['env']['memcached']['enabled'] = true;    # kill switch to turn memcached off
$config['env']['memcached']['servers'] = array(array('localhost', 11211));
$config['env']['memcached']['staticVersions']['siteName'] = 'menulog2.dev';
$config['env']['memcached']['staticVersions']['siteVersion'] = 1;


# file system manager
$config['env']['fileSystemCacheManager']['writePermission'] = false;    # turning this on/off will allow or stop the filesystem cache manager from writing to the filesystem
$config['smarty']['dir']['root'] = $config['dir']['root'] . 'includes/lib/Smarty';
$config['smarty']['dir']['templates_c'] = '/dev/shm/smarty/templates_c';
$config['smarty']['dir']['cache'] = '/dev/shm/smarty/cache';
$config['smarty']['dir']['configs'] = $config['dir']['root'] . 'includes/smarty_configs';
$config['customSites']['pepperonis.local']['filesDir'] = '/home/k7/Workspace/custom_sites/pepperonis.local';
$config['customSites']['pepperonis.local']['routes'] = array(
    '/pepperonis' => $config['customSites']['pepperonis.local']['filesDir'] . '/html/splash.html',
    'default' => $config['customSites']['pepperonis.local']['filesDir'] . '/html/splash.html',
    'splash' => $config['customSites']['pepperonis.local']['filesDir'] . '/html/splash.html'
);
$config['customSites']['pepperonis.local']['splashPage'] = $config['dir']['root'] . 'public_html/custom_sites/pepperonis.local/html/splash.html';
$config['customSites']['pepperonis.local']['rotatingBanners'] = array(
    'http://pinis-pizza.com/800px-Supreme_pizza.jpg',
    'http://www.bluejean.fr/en/food/images/pizza-margherita.jpg',
    'http://whatdidyoueat.typepad.com/photos/uncategorized/2007/07/17/img_6213.jpg',

);
//baseDomain for www.pepperonis.com.au  would be www.menulog.com.au
$config['customSites']['baseDomain'] = 'menulog.local';

$config['server']['timeZone'] = 'Australia/NSW';

$config['maintenance']['manualOverride'] = false;
$config['maintenance']['crm_consecutive_fatal_error_count_threshold'] = 3;
$config['log4_main_properties'] = dirname(__FILE__) . '/log4_main_properties.local.ini';
//restaurant classified as new if created withing x days
$config['newIfCreatedWithinSeconds'] = 60 * 24 * 60 * 60;

//AU iphone url
$config['iphoneUrl'][1] = 'http://itunes.apple.com/au/app/home-delivery-takeaway/id327982905?mt=8';
//NZ iphone url
$config['iphoneUrl'][4] = 'http://itunes.apple.com/nz/app/home-delivery-takeaway-nz/id485249021?mt=8&uo=4';
//RU iphone url
$config['iphoneUrl'][11] = 'http://itunes.apple.com/ru/app/home-delivery-takeaway/id327982905?mt=8';
$config['androidUrl'][1] = 'https://play.google.com/store/apps/details?id=com.menulog.m';
$config['androidUrl'][4] = 'https://play.google.com/store/apps/details?id=com.menulog.m';

$config['website']['features'] = array('officefreelunch');
$config['affiliates']['default']['configFile'] = dirname(__FILE__) . '/affiliates/default.php';
$config['affiliates']['14b4e063']['configFile'] = dirname(__FILE__) . '/affiliates/eatability.php';
$config['affiliates']['8f017b5b']['configFile'] = dirname(__FILE__) . '/affiliates/webwombat.php';
$config['affiliates']['eb2d81f0']['configFile'] = dirname(__FILE__) . '/affiliates/bcl.php';
$config['affiliates']['17f6286e']['configFile'] = dirname(__FILE__) . '/affiliates/bookmein.php';
$config['affiliates']['8b3d8207']['configFile'] = dirname(__FILE__) . '/affiliates/buyaustralian.php';

//testimonials
$config['testimonials'][1] = array(
    0 => array('testimonialText' => '"First experience using Menulog. Simple and easy to use. Would use this service again."',
        'testimonialSignature' => 'Darren, Sydney, 23rd April 2012',
    ),
    1 => array('testimonialText' => '"This is a great service, very easy to use and my order was with me in the time they said."',
        'testimonialSignature' => 'Nigel, Melbourne, 27th April 2012',
    ),
);

$config['testimonials'][4] = array(
    0 => array('testimonialText' => '"Easy to navigate , Very pleasantly surprised for a 1st time user of the service will definitely use again."',
        'testimonialSignature' => 'Deirdre, Auckland, March 2012',
    ),
    1 => array('testimonialText' => '"Very impressed, most efficient ordering I have used. Well Done."',
        'testimonialSignature' => 'Simon, Takapuna, Auckland February 2012',
    ),

);
//restaurant ids that still use menulog booking service for free site only
//9685, 5638 is www.pateethai.com.au
$config['booking']['menulog'] = array(9685, 5638);

//custom site on promotion
$config['customSites']['onPromotion'] = array();


//restaurants that wer winners in tta
$config['competitions']['tta2012']['winners']['overallWinner'] = 78749;
$config['competitions']['tta2012']['winners']['regionalWinner'] = array(4264, 83234, 1364, 82141, 82593, 85225, 72163, 78913, 2500, 4989, 3001, 3543, 85026, 77032, 2980, 84354, 84215, 84980, 9711, 77919, 5322, 80062, 9083, 53615, 83555, 10021, 74652, 7724, 83400, 82610, 85016, 77928, 15138, 74761, 85397, 44478, 12805, 79336, 11124, 83005);
$config['competitions']['tta2015']['winners']['finalistWinner'] = array();
$config['competitions']['tta2015']['winners']['participants'] = array();

# there should only be two entries in this array, one for NZ, one for AU
$config['competitions']['tta2014']['winners']['overallWinner'] = array(78749, 90933);
$config['competitions']['tta2014']['winners']['stateWinner'] = array(3001, 89855, 85397, 75964, 87238);
$config['competitions']['tta2014']['winners']['regionalWinner'] = array(82610, 13852, 81725, 86213, 80428, 82442, 86513, 89777, 88887, 86177, 90907, 91880, 79814, 84720, 88355, 17421, 72719, 86001, 80493, 2540, 85215, 85528, 78666, 78870, 3159, 79830, 47823, 81281, 92488, 87034, 2916, 4989, 1365, 90916, 83234, 83439, 82141, 78665, 1924, 90106, 82195, 28079, 85795, 88786, 85808, 4872, 83368, 72163, 85415, 86699, 86723, 91891, 85952, 85956, 79702, 88661, 90351, 75122, 4821, 90361, 90255, 2100, 88999, 80262, 85476, 88664, 1964, 67277, 77420, 78207, 90771, 77032, 79985, 87644, 76019, 85174, 2885, 83282, 78634, 78725, 79555, 90863, 80147, 88595, 83420, 70473, 89446, 89140, 2338, 81554, 88267, 86665, 73514, 89597, 29087, 86232, 6702, 48351, 86544, 86020, 85920, 90964, 85930, 84943, 89184, 89835, 12805, 75362, 79336, 17827, 88124, 83093, 75714, 88590, 12934, 13291, 75968, 77682, 13218, 88579, 85020, 77106, 83938, 87391, 80329, 82420, 85445, 91119, 89816, 64908, 7591, 84776, 91157, 79553, 86594, 89022, 82280, 86178, 79257, 9711, 90988, 66398, 10853, 80062, 89467, 79728, 85988, 89765, 79170, 86484, 9613, 9555, 82394, 76282, 89877, 92340, 89293, 7593, 5638, 85811, 77928, 85016, 12496, 90007, 8350, 90111, 82430, 87028, 8957, 8728, 91054, 89255, 75163, 83084, 16118, 86818, 17621, 87928, 15141, 17619, 90125, 86999, 80513, 73120, 85132, 86738, 15138, 90936, 87882, 90481, 89781, 18253, 86762, 15588, 87021, 75018, 90939, 86146, 44873, 44888, 91020, 85091, 90385, 87063, 87162, 88648, 88404, 88381, 86428, 85981, 90334, 90483, 91154, 90869, 83494, 89297, 83688, 90289, 71535, 15175, 77752, 26061, 92520, 92403, 9024, 88894, 91343, 88109, 86883, 79296, 26032, 72236, 92038, 5390, 76164, 83513, 88750, 67475, 84981, 83661, 85048, 74761, 71176, 15952);
# empty finalistWinner and participants
$config['competitions']['tta2014']['winners']['finalistWinner'] = array();
$config['competitions']['tta2014']['winners']['participants'] = array();
# NZ Top 5
$config['competitions']['tta2014']['winners']['top5'] = array(74205, 87538, 90984, 74250, 84697);

$config['competitions']['tta2013']['winners']['overallWinner'] = array(5638);
$config['competitions']['tta2013']['winners']['stateWinner'] = array();
$config['competitions']['tta2013']['winners']['regionalWinner'] = array(85528, 4989, 12805, 75402, 64908, 7591, 80764, 87021, 11124, 11126, 48351, 29087, 82232, 85821, 85901, 88309, 86544, 7004, 87722, 7088, 85047, 82682, 6613, 81582, 47823, 3159, 85091, 84731, 9740, 79553, 10332, 24264, 86527, 82168, 1955, 81417, 82892, 1364, 83651, 85788, 84289, 1924, 86214, 83439, 1439, 84875, 26061, 81258, 81734, 1657, 78636, 83444, 2506, 85572, 86371, 81458, 84740, 85789, 83442, 81415, 82894, 80263, 83283, 88726, 83378, 83722, 85177, 26987, 82028, 85791, 80127, 84159, 86387, 1368, 1889, 2579, 24266, 73302, 89680, 82893, 1632, 84329, 81341, 87559, 66525, 81996, 83721, 4950, 79035, 80168, 83886, 88540, 88809, 88998, 75062, 78909, 81991, 86941, 81060, 82223, 83648, 3235, 72728, 79146, 83486, 88983, 83084, 80329, 85445, 19659, 80328, 84943, 86020, 75018, 87020, 17619, 85795, 19105, 69116, 74699, 78428, 76164, 64910, 77919, 40872, 5322, 79257, 75076, 19081, 78904, 88310, 85437, 18692, 75074, 89674, 9383, 85132, 86738, 80428, 86213, 82593, 72163, 26032, 77752, 83368, 4872, 83547, 26397, 84936, 82896, 3705, 83691, 25890, 82184, 85943, 2824, 89445, 85808, 89728, 22739, 82264, 44187, 82117, 2937, 2500, 83647, 25487, 25897, 86032, 84332, 80203, 86173, 1681, 2939, 82225, 28048, 81866, 82895, 44174, 81177, 83247, 1823, 26025, 2888, 22960, 25481, 25889, 85876, 80023, 89506, 82897, 85364, 86263, 85942, 2352, 76109, 2475, 81724, 82197, 83650, 22923, 83773, 87533, 88864, 84935, 88213, 3754, 25484, 79284, 79423, 82962, 5182, 85794, 71535, 79658, 83501, 8350, 87028, 82430, 84720, 83093, 75714, 81735, 84314, 88590, 89341, 84315, 79712, 13159, 88124, 9083, 78149, 10021, 83755, 76481, 81333, 85894, 79728, 9685, 77239, 85024, 74226, 78351, 86388, 89180, 82116, 77711, 7582, 8588, 83555, 88551, 59890, 88966, 89364, 9384, 80224, 7943, 86121, 89273, 8799, 9507, 12715, 84711, 85578, 87188, 88342, 88968, 9608, 19063, 86204, 8431, 76150, 84637, 77920, 79763, 81334, 82219, 79612, 81961, 84441, 85613, 85632, 78870, 84479, 85785, 2980, 78666, 85213, 85629, 72857, 83130, 85510, 81761, 2236, 74532, 82227, 81263, 68456, 4909, 81211, 1017, 77755, 84290, 88099, 89197, 21511, 84685, 86180, 88510, 89323, 84354, 84692, 4906, 83375, 75118, 78075, 84658, 9555, 76282, 83688, 4914, 88343, 88646, 85406, 4821, 82705, 5078, 82704, 75122, 3297, 80678, 80797, 84328, 49887, 73101, 88892, 83765, 85476, 77658, 2968, 82414, 15555, 88664, 3385, 80262, 80052, 85857, 88648, 67277, 78207, 51143, 77420, 84339, 1052, 84916, 81085, 85805, 81087, 88068, 86139, 86703, 86999, 13291, 44757, 77682, 81757, 13218, 57374, 83513, 84215, 10402, 86179, 76412, 83400, 84364, 79061, 86011, 89811, 40873, 79557, 80022, 85188, 72152, 7724, 76163, 7593, 85218, 5429, 76353, 10096, 9870, 81367, 85128, 85581, 89855, 88508, 8439, 10081, 76326, 79633, 85486, 7498, 8998, 67265, 84216, 84080, 5564, 9301, 76820, 10617, 76428, 88259, 88350, 69561, 83754, 84939, 85029, 85538, 87463, 87528, 19057, 78695, 19064, 65782, 79701, 85491, 85871, 89550, 15175, 73274, 15005, 85026, 76019, 27785, 87640, 79797, 86706, 84331, 3108, 76813, 87723, 85134, 64906, 74761, 89196, 2834, 2835, 1369, 86719, 85432, 2885, 4907, 85059, 89104, 5586, 2916, 85627, 86150, 82167, 80269, 81914, 5568, 83250, 78040, 79296, 87034, 82198, 2521, 81090, 89081, 80799, 85787, 80270, 89617, 84183, 81088, 79301, 77753, 84932, 84817, 77334, 45846, 86385, 8282, 85811, 12496, 86148, 87793, 9424, 84492, 87006, 79611, 19325, 75634, 75716, 75552, 83094, 77106, 83938, 85020, 2338, 89140, 2223, 3335, 79614, 70473, 88912, 1659, 4934, 75065, 73514, 88267, 86665, 86483, 8728, 84981, 8957, 84980, 84982, 84295);
# empty finalistWinner and participants
$config['competitions']['tta2013']['winners']['finalistWinner'] = array();
$config['competitions']['tta2013']['winners']['participants'] = array();
# NZ Top 5 (empty prior to 2014)
$config['competitions']['tta2013']['winners']['top5'] = array();


//useful when you would like set the owner of a
//file that needs to editable in a web request
$config['env']['httpdUser'] = 'www-data';

// popular locations and cuisines
// put it in config, don't have to query db for constant information
$config['homepageTakeawayPopulars'] = array(
    // AU(country id)
    1 => array(
        'majorRegions' => array(
            // majore region id
            7 => array(
                'id' => 7,
                'name' => 'Adelaide',
                'quickLinkName' => 'adelaide_sa'
            ),
            3 => array(
                'id' => 3,
                'name' => 'Brisbane',
                'quickLinkName' => 'brisbane_qld'
            ),
            5 => array(
                'id' => 5,
                'name' => 'Canberra',
                'quickLinkName' => 'canberra_act'
            ),
            13 => array(
                'id' => 13,
                'name' => 'Gold Coast',
                'quickLinkName' => 'gold_coast'
            ),
            8 => array(
                'id' => 8,
                'name' => 'Hobart',
                'quickLinkName' => 'hobart_tas'
            ),
            4 => array(
                'id' => 4,
                'name' => 'Melbourne',
                'quickLinkName' => 'melbourne_vic'
            ),
            9 => array(
                'id' => 9,
                'name' => 'Perth',
                'quickLinkName' => 'perth_wa_2'
            ),
            28 => array(
                'id' => 28,
                'name' => 'Sydney',
                'quickLinkName' => 'sydney'
            ),
            6 => array(
                'id' => 6,
                'name' => 'Central Coast',
                'quickLinkName' => 'central_north_coast'
            ),
            31 => array(
                'id' => 31,
                'name' => 'South Coast',
                'quickLinkName' => 'illawarra_south_coast'
            )
        ),

        'cuisines' => array(
            // cuisine id
            73 => array(
                'id' => 73,
                'name' => 'Pizza',
                'quickLinkName' => 'pizza'
            ),
            8 => array(
                'id' => 8,
                'name' => 'Thai',
                'quickLinkName' => 'thai'
            ),
            58 => array(
                'id' => 58,
                'name' => 'Indian',
                'quickLinkName' => 'indian'
            ),
            28 => array(
                'id' => 28,
                'name' => 'Chinese',
                'quickLinkName' => 'chinese'
            ),
            5 => array(
                'id' => 5,
                'name' => 'Italian',
                'quickLinkName' => 'italian'
            ),
            27 => array(
                'id' => 27,
                'name' => 'Japanese',
                'quickLinkName' => 'japanese'
            ),
            35 => array(
                'id' => 35,
                'name' => 'Vegetarian',
                'quickLinkName' => 'vegetarian'
            ),
            36 => array(
                'id' => 36,
                'name' => 'Vietnamese',
                'quickLinkName' => 'vietnamese'
            ),
            28 => array(
                'id' => 28,
                'name' => 'Lebanese',
                'quickLinkName' => 'lebanese'
            )
        )
    )
);
$config['homePageBanners'] = array(
    'notLoggedIn' => array(
        1 => 1764,
        4 => 1768
    ),
    'loggedIn' => array(
        1 => 1783,
        4 => 1785
    )
);
//loading conversion pixel if the website id is in the array
$config['tvCampaign']['tracking'] = array(1);

$config['xhProf']['enabled'] = TRUE;
//profile every nth request
$config['xhProf']['profilePer'] = 1000;
//as some pages get less request than others, 
//increasing the probability of profiling pages that get less requests
//lower the skew number, higher is the probability of profiling
$config['xhProf']['profilePerSkew']['taCheckoutFormProcessed'] = 0.4;
$config['xhProf']['profilePerSkew']['taCheckout'] = 0.4;
$config['xhProf']['profilePerSkew']['taCheckoutComplete'] = 0.5;
$config['xhProf']['profilePerSkew']['taCrmPaymentComplete'] = 0.5;
$config['xhProf']['profilePerSkew']['taSearch'] = 1;
$config['xhProf']['profilePerSkew']['siteMap'] = 1;
$config['xhProf']['profilePerSkew']['taVenue'] = 1;
$config['xhProf']['profilePerSkew']['updateCart'] = 1;
$config['xhProf']['profilePerSkew']['taHome'] = 0.7;
//XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY
//may need to update and check when upgrading xhprof
//not hardcoding here as I would like to avoid evaluating in config
$config['xhProf']['flags'] = 7;
$config['xhProf']['ignoredFunctions'] = array('call_user_func',
    'call_user_func_array', 'globalCoreMysqli::query', 'databaseItem::load_db', 'databaseItem::generate_load_query', 'databaseItem::load_one_from_sub_group', 'databaseItem::_load_sub_group', 'dbiManager::gv', 'databaseItem::__construct', 'databaseItem::add_field', 'simpleDbiManager::gv_full', 'databaseItem::add_field', 'databaseItem::get_field_list_array', 'globalCoreMysqli::fetch_assoc', 'databaseItem::generate_field_list', 'simpleDbiManager::gv', 'databaseItem::generate_field_name_for_load_query', 'databaseItem::load_db_array', 'databaseItem::load_array_temp', 'Smarty_Internal_TemplateBase::fetch', 'Smarty_Internal_Template::compileTemplateSource', 'Smarty_Internal_TemplateBase::display', 'Smarty_Internal_Template::getSubTemplate', 'Smarty_Internal_Filter_Handler::runFilter', 'smarty_outputfilter_trimwhitespace');
$config['restaurantOfTheMonth'] = array('4989');
$config['search']['minimumFoodItemImages'] = 1;
$config['venues']['transactionAPI']['specifiedVenuesOnly'] = false;
