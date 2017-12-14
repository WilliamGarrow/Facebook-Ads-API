<?php

$appRoot = dirname(__FILE__) . '<PATH>';
require_once $appRoot . 'config.php';
require_once $appRoot . 'load.php';
global $userdb;
require_once $appRoot . "vendor/autoload.php";

use FacebookAds\Object\AdAccount;
use FacebookAds\Object\AdsInsights;
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;

$access_token = '<ACCESSTOKEN>';
$ad_account_id = '<ADACCOUNTID>';
$app_secret = '<APPSECRET>';
$app_id = '<APPID>';
$api = Api::init($app_id, $app_secret, $access_token);
$api->setLogger(new CurlLogger());

$fields = array(
    'campaign_id',
    'campaign_name',
    'spend',
);

// Use date ranges in chunks. FB has Account-Level Rate Limiting on "user accounts making too many calls to the API."
// https://developers.facebook.com/docs/graph-api/advanced/rate-limiting

$from = strtotime('2017-06-01');
$to = strtotime('2017-12-14');

for ($from_day = $from; $from_day < $to; $from_day = strtotime('+1 day', $from_day)) {

    $to_day = strtotime('+1 day', $from_day);
    $since = date('Y-m-d', $from_day);
    $until = date('Y-m-d', $to_day);

    $params = array(
        'time_range' => array('since' => $since, 'until' => $until),
        'filtering' => array(),
        'level' => 'campaign',
        'breakdowns' => array(),
    );

    $insights = json_decode(json_encode((new AdAccount($ad_account_id))->getInsights(
        $fields,
        $params
    )->getResponse()->getContent(), JSON_PRETTY_PRINT), true);

    var_dump($insights['data']);

    foreach ($insights['data'] as $data) {
        $insertData = array(
            'spend_day' => $data['date_start'],
            'spend' => $data['spend'],
            'campaign_id' => $data['campaign_id'],
            'campaign_name' => $data['campaign_name'],
            'channel' => 'facebook',
            'lead_source' => 'Facebook Paid',
            'affiliate' => '<AFFILIATE>',
        );

        $userdb->insert('daily_ad_spend', $insertData);
        sleep(2);
    }
}
