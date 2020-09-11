<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AweberToken extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        require_once(APPPATH.'third_party/aweber_api/aweber_api.php');
    }

    public function index()
    {
        $aweber = new AWeberAPI(AWEBER_CONSUMER_KEY, AWEBER_CONSUMER_SECRET);
        if (empty($_COOKIE['accessToken'])) {
            if (empty($_GET['oauth_token'])) {
                $callbackUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                list($requestToken, $requestTokenSecret) = $aweber->getRequestToken($callbackUrl);
                setcookie('requestTokenSecret', $requestTokenSecret);
                setcookie('callbackUrl', $callbackUrl);
                header("Location: {$aweber->getAuthorizeUrl()}");
                exit;
            }
            $aweber->user->tokenSecret = $_COOKIE['requestTokenSecret'];
            $aweber->user->requestToken = $_GET['oauth_token'];
            $aweber->user->verifier = $_GET['oauth_verifier'];
            list($accessToken, $accessTokenSecret) = $aweber->getAccessToken();
            echo 'accessToken :'.$accessToken."<br>";
            echo 'accessTokenSecret :'.$accessTokenSecret;
            exit;
        }
        /* $kareAccount = $aweber->getAccount(KAARE_USER_API_ACCESS_TOKEN, KAARE_USER_API_ACCESS_TOKEN_SECRET);
        $camilaAccount = $aweber->getAccount(CAMILLA_USER_API_ACCESS_TOKEN, CAMILLA_USER_API_ACCESS_TOKEN_SECRET);
        $frejasAccount = $aweber->getAccount(FREJAS_USER_API_ACCESS_TOKEN, FREJAS_USER_API_ACCESS_TOKEN_SECRET);

        echo "<pre>";
        print_r($kareAccount);
        print_r($camilaAccount);
        print_r($frejasAccount);
        exit; */
    }
}
