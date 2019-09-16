<?php

    require_once "vendor/autoload.php";

    $fb = new \facebookAPI\Facebook();
    $fb->setAppInfo("APP_IDNİZ","CLIENT_TOKENİNİZ");

    // $scopes = ['user_likes','public_profile'];  array tipinde facebook api izinleri içermelidir. $fb->createAppLoginCode($scopes)
    $scopes = ['user_likes','public_profile'];
    $create = $fb->createAppLoginCode($scopes);
    print_r($create);
    //isConfirmed token
    // 5 saniyede bir defa kontrol ettirilebilir.
    $codeControl = $fb->controlAppLoginCode("dfcb23cebc240adf91ed2b5a4f26042a");
    print_r($codeControl);
