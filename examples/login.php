<?php

require_once "../vendor/autoload.php";

$fb = new \facebookAPI\Facebook();
$fb->login("username","password");