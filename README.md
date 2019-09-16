Setup
<code>
composer require mehmetbeyhz/facebook-api:dev-master        
</code>
## Login With Username and Password
```php
  require_once "vendor/autoload.php";
  $fb = new facebookAPI\Facebook();
  $login = $fb->login("username","password");
  $data = json_decode($login, true);
  $userInfo = $fb->userInfo($data['access_token']);
  print_r($userInfo);
```
## Login With Device
First check your app permission

![GitHub Logo](/images/loginWithCode.png)


```php
require __DIR__.'/../vendor/autoload.php';
    $fb = new \facebookAPI\Facebook();
    $fb->setAppInfo("YOUR_APP_ID","YOUR_CLIENT_TOKEN");
    // $scopes = ['user_likes','public_profile'];  array tipinde facebook api izinleri içermelidir. $fb->createAppLoginCode($scopes)
    $scopes = ['user_likes','public_profile'];
    $create = $fb->createAppLoginCode($scopes);
    print_r($create); //JSON Format json_decode($create,true); is array format
    //isConfirmed token
    // 5 saniyede bir defa kontrol ettirilebilir.
    // Send 1 request in 5 seconds
    $codeControl = $fb->controlAppLoginCode("dfcb23cebc240adf91ed2b5a4f26042a");
    print_r($codeControl);
```
