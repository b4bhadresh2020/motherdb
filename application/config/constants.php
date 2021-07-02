<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code



/*
	-> define table
*/
define('ENCRYPT_KEY'					,       'motherdb');
define("ADMINMASTER"					,		"adminmaster");
define("SITECONFIG"						,		"siteconfig");
define("USER"							,		"user");
define("BATCH"							,		"batch");
define("BATCH_LINK"						,		"batch_link");
define("BATCH_USER"						,		"batch_user");
define("BATCH_CAMPAIGN"					,		"batch_campaign");
define("REDIRECT_LINK_CLICKS"			,		"redirect_link_clicks");
define("CAMPAIGN"						,		"campaign");
define("USER_PARTICIPATED_CAMPAIGN"		,		"user_participated_campaign");
define("UNSUBSCRIBER"					,		"unsubscriber");
define("CSV_FILE_DATA"					,		"csv_file_data");
define("CSV_CRON_USER_DATA"				,		"csv_cron_user_data");
define("CSV_FILE_PROVIDER_DATA"			,		"csv_file_provider_data");
define("CSV_FILE_PROVIDER_HISTORY"		,		"csv_file_provider_history");
define("CSV_PROVIDERS_DETAIL"			,		"csv_providers_detail");
define("CRON_STATUS"					,		"cron_status");
define("LOAN_MASTER"					,		"loan_master");
define("GROUP_MASTER"					,		"group_master");
define("KEYWORD_MASTER"					,		"keyword_master");
define("COUNTRY_MASTER"					,		"country_master");
define("ENRICHMENT_CSV_FILE"			,		"enrichment_csv_file");
define("ENRICHMENT_CRON_STATUS"			,		"enrichment_cron_status");
define("BLACKLIST_CSV_FILE"				,		"blacklist_csv_file");
define("BLACKLIST_CRON_STATUS"			,		"blacklist_cron_status");
define("HISTORY"						,		"history");
define("LIVE_DELIVERY"					,		"live_delivery");
define("LIVE_DELIVERY_STAT"				,		"live_delivery_stat");
define("BLACKLIST"						,		"blacklist");
define("LIVE_DELIVERY_DATA"				,		"live_delivery_data");
define("LIVE_DELIVERY_UNDEFINED_KEY_DATA"	,		"live_delivery_undefined_key_data");
define("TEST"							,		"test");
define("KEYWORD_COUNTRY_COUNT"	    	,	"keyword_country_count");
define("GROUP_COUNTRY_COUNT"	    	,	"group_country_count");
define("GENERAL_BATCH"			    	,	"general_batch");
define("GENERAL_BATCH_USER"		    	,	"general_batch_user");
define("SMS_HISTORY"			    	,	"sms_history");
define("EXPORT_FILES"			    	,	"export_files");
define("WITH_MERGE_TAG"			    	,	"with_merge_tag");
define("WITHOUT_MERGE_TAG"			    ,	"without_merge_tag");
define("AWEBER_DELAY_USER_DATA"			,	"aweber_delay_user_data");
define("TRANSMITVIA_DELAY_USER_DATA"	,	"transmitvia_delay_user_data");
define("CONTACT_DELAY_USER_DATA"		,	"contact_delay_user_data");
define("ONGAGE_DELAY_USER_DATA"			,	"ongage_delay_user_data");
define("SENDGRID_DELAY_USER_DATA"		,	"sendgrid_delay_user_data");
define("SENDINBLUE_DELAY_USER_DATA"		,	"sendinblue_delay_user_data");
define("SENDPULSE_DELAY_USER_DATA"		,	"sendpulse_delay_user_data");
define("MAILERLITE_DELAY_USER_DATA"		,	"mailerlite_delay_user_data");
define("MAILJET_DELAY_USER_DATA"		,	"mailjet_delay_user_data");
define("CONVERTKIT_DELAY_USER_DATA"		,	"convertkit_delay_user_data");
define("MARKETING_PLATFORM_DELAY_USER_DATA"		,	"marketing_platform_delay_user_data");
define("ONTRAPORT_DELAY_USER_DATA"		,	"ontraport_delay_user_data");
define("ACTIVE_CAMPAIGN_DELAY_USER_DATA", "active_campaign_delay_user_data");
define("EXPERT_SENDER_DELAY_USER_DATA", "expert_sender_delay_user_data");
define("EMAIL_HISTORY_DATA"				,	"email_history_data");
define("PROVIDER_UNSUBSCRIBER"			,	"provider_unsubscriber");
define("WEBHOOK_UNSUBSCRIBE_SETTINGS"	,	"webhook_unsubscribe_settings");
define("REPOST_SCHEDULE"			    ,	"repost_schedule");
define("REPOST_SCHEDULE_LIVE_DELIVERY_DATA" ,	"repost_schedule_live_delivery_data");
define("REPOST_SCHEDULE_HISTORY", "repost_schedule_history");

//from other db
define("SMS_UNSUBSCRIBER_LIST","sms_unsubscriber_list");
define("UNIQUEKEY_LINK","uniquekey_link");

// Aweber constant

define('AWEBER_API_PATH'	, 'https://api.aweber.com/1.0/');
define('AWEBER_OAUTH_URL'	, 'https://auth.aweber.com/oauth2/');
define('AWEBER_TOKEN_URL'	, 'https://auth.aweber.com/oauth2/token');

define('AWEBER_CLIENT_ID'	, 'HoK3UZHUYHg9D6jPPkKa0noJTYCbJ4Hs');
define('AWEBER_CLIENT_SECRET', '3kTNs5HT8CE7PemGAHENrDvnak6rOHWc');

//convertkit constant
define('CONVERTKIT_API_PATH','https://api.convertkit.com/v3/');

// Enter your Constant Contact APIKEY and ACCESS_TOKEN
define("CONSTANT_CONTACT_APIKEY", "7a8gcmnmtnzdtgjmaakupvzm");
define("CONSTANT_CONTACT_ACCESS_TOKEN", "d11e5c72-767e-48b5-a723-79dcb8318cd5");

// Ongage constant
define('ONGAGE_API_PATH'	, 'https://api.ongage.net/');
define('ONGAGE_API_CONTACT_PATH'	,'/api/contacts/');

// sendpulse constant
define('SENDPULSE_TOKEN_URL', 'https://api.sendpulse.com/oauth/access_token');

// expert sender url constant
define('EXPERT_SENDER_API_PATH', 'https://api4.esv2.com/v2/');

define('AWEBER'	, '1');
define('TRANSMITVIA', '2');
define('CONSTANTCONTACT', '3');
define('ONGAGE', '4');
define('SENDGRID', '5');
define('SENDINBLUE', '6');
define('SENDPULSE', '7');
define('MAILERLITE', '8');
define('MAILJET', '9');
define('CONVERTKIT', '10');
define('MARKETING_PLATFORM','11');
define('ONTRAPORT', '12');
define('ACTIVE_CAMPAIGN','13');
define('EXPERT_SENDER','14');
define('PROVIDERS', 'providers');
define('AWEBER_ACCOUNTS', 'aweber_accounts');
define('ONGAGE_ACCOUNTS', 'ongage_accounts');
define('SENDPULSE_ACCOUNTS','sendpulse_accounts');
define('MAILERLITE_ACCOUNTS','mailerlite_accounts');
define('MAILJET_ACCOUNTS','mailjet_accounts');
define('CONVERTKIT_ACCOUNTS','convertkit_accounts');
define('MARKETING_PLATFORM_ACCOUNTS','marketing_platform_accounts');
define('ONTRAPORT_ACCOUNTS','ontraport_accounts');
define('ACTIVE_CAMPAIGN_ACCOUNTS','active_campaign_accounts');
define('EXPERT_SENDER_ACCOUNTS', 'expert_sender_accounts');

// MX Block account
define('TELIA_DOMAIN', 'telia.com');
define('LUUKKU_DOMAIN', 'luukku.com');

define('PP_DOMAIN_START', 'pp');
define('PP_DOMAIN_END', '.inet.fi');


/*
 * 	 -> google 2 factor, dont change it if you dont have proper reason
 */
define("GOOGLE_2FA_SECRET","JWCXZJL3W53TQ4ONYFOOUTM2QPNSJIPG");


/*
	-> define constant
*/

define("MAX_SIZE" ,		"20000000");

if(strtolower($_SERVER['HTTP_HOST']) == 'localhost'){
	define("UNSUBSCRIBE_DOMAIN"	,  "http://localhost/motherdb_unsubscribe/");
}else{
	define("UNSUBSCRIBE_DOMAIN"	,  "http://hoi3.com/");
}



/*
 * 	define database
 */

if(strtolower($_SERVER['HTTP_HOST']) == 'localhost'){

	define("DEFAULT_DB"	,  "motherdb");
	define("OTHER_DB"	,  "motherdb_unsubscriber");

}else{

	define("DEFAULT_DB"	,  "suprdat_motherdb");
	define("OTHER_DB"	,  "suprdat_hoi3");

}


/*
	-> define live delivery url domain
*/

if(strtolower($_SERVER['HTTP_HOST']) == 'localhost'){
	define("LIVE_DELIVERY_URL_DOMAIN"	,  "http://localhost/motherdb/");
}else{
	define("LIVE_DELIVERY_URL_DOMAIN"	,  "https://suprdat.dk/");
}
