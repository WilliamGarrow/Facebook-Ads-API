# Facebook Marketing API
This is an example of the code used to access Facebook marketing API in order to bulk-read marketing lead generation data.

## Usage
Replace the API specific variables with your unique access token, ad account id, app secret, and app id. Include your application-specific files in your `<YOUR PATH>`.
```php
$access_token = '<YOUR ACCESS TOKEN>';
$ad_account_id = '<YOUR AD ACCOUNT ID>';
$app_secret = '<YOUR APP SECRET>';
$app_id = '<YOUR APP ID>';
$api = Api::init($app_id, $app_secret, $access_token);
$api->setLogger(new CurlLogger());

$fields = array(
    'campaign_id',
    'campaign_name',
    'spend',
...
```

### Development Note 
Depending on the size of the dataset that you will be reading in,  I suggest retrieving date ranges in chunks. FB has Account-Level Rate Limiting on "user accounts making too many calls to the API"

Example size I used in development to mitigate rate limitations:
```php
$from = strtotime('2017-11-01');
$to = strtotime('2017-11-3-');
```
Documentation - https://developers.facebook.com/docs/graph-api/advanced/rate-limiting
