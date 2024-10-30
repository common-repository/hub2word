<?php

/**
 * Description of HubspotRequest
  Version: 1.0
  Author: Team Webbege
  Author URI: http://www.webbege.com/
 */
//namespace inc\curl;

require_once dirname(__FILE__) . '/Curl.php';
require_once dirname(__FILE__) . '/Url.php';

class HubspotRequest {

    public $request = null;

    public function __construct() {
        
    }

    public static function getHubspotAPIinfo($reqUrl) {

            if ($reqUrl) {
                $response = Curl::curlExecute($reqUrl);
                return $response;
            }
        
        return FALSE;
    }

    public static function getAuthUrl($redirectUri, $scope = array()) {
        $scopeString = '';
        if (count($scope) > 0) {
            $scopeString = '';
            foreach ($scope as $_index => $scopeStr) {
                if ($_index > 0) {
                    $scopeString .= "%20";
                }
                $scopeString .= $scopeStr;
            }
        }
        return "https://app.hubspot.com/oauth/authorize?client_id=e6b7f0be-69d6-45e3-bc2c-3d4c9efcc543&scope={$scopeString}&redirect_uri=" . urlencode($redirectUri);
    }

    public static function getAccessToken($grantType, $redirectUri, $code = NULL) {
        $requestUri = Url::getTokenUrl($grantType, $redirectUri, $code);
        $response = Curl::getAccessToken($requestUri);
        return $response;
    }

    public static function getRefreshTokenUrl($refreshToken) {
        $requestUri = Url::getRefreshTokenUrl($refreshToken);
        $response = Curl::getAccessToken($requestUri);
        return $response;
    }

    public static function getAccessTokenInfo($tokenRes) {
        $reqUrl = "https://api.hubapi.com/oauth/v1/access-tokens";
        $requestDetails = Url::getTokenInfoUrl($tokenRes, $reqUrl);
        $response = Curl::curlExecute($requestDetails);
        return $response;
    }

    public static function checkTokenValidity($expireIn = NULL) {
        if (is_null($expireIn)) {
            return FALSE;
        }
        $now = time();
        if ($now > $expireIn) {
            return TRUE;
        }
        return false;
    }

    public static function getToken() {
        $response = array();
        $hub_token = get_option('access_token');

        if (empty($hub_token)) {
            $response['status'] = FALSE;
        } else {
            $response['token'] = $hub_token['access_token'];
            $response['status'] = TRUE;
        }
        return $response;
    }

    public static function getHubspotForms() {
        $tokenRes = self::getToken();
        $apiKey = get_option('h2w_api');
        if ($tokenRes['status'] || $apiKey) {
            $requestDetails = Url::getFormUrl($tokenRes['token'], $apiKey);
            $response = Curl::curlExecute($requestDetails);
            return $response;
        }
        return FALSE;
    }

    public static function getHubspotSearch($reqUrl, $portalId, $query = NULL, $query_type = NULL) {
        $requestDetails = Url::getSearch($portalId, $reqUrl, $query, $query_type);
        if ($requestDetails) {
            $response = Curl::curlExecute($requestDetails);
            return $response;
        }
        return FALSE;
    }

    public static function getHubspotData($reqUrl) {
        $tokenRes = self::getToken();
        $apiKey = get_option('h2w_api');
        if ($tokenRes['status'] || $apiKey) {
            $requestDetails = Url::getDynamicUrl($tokenRes['token'], $apiKey, $reqUrl);
            if ($requestDetails) {
                $response = Curl::curlExecute($requestDetails);
                return $response;
            }
        }
        return FALSE;
    }

    public static function getHubspotBlogs($reqUrl) {
        $tokenRes = self::getToken();
        $apiKey = get_option('h2w_api');
        if ($tokenRes['status'] || $apiKey) {
            $requestDetails = Url::getBlogUrl($tokenRes['token'], $apiKey, $reqUrl);
            if ($requestDetails) {
                $response = Curl::curlExecute($requestDetails);
                return $response;
            }
        }
        return FALSE;
    }

    public static function getHubspotBlogPosts($reqUrl, $hsblog, $limit) {
        $tokenRes = self::getToken();
        $apiKey = get_option('h2w_api');
        if ($tokenRes['status'] || $apiKey) {
            $requestDetails = Url::getBlogPosts($tokenRes['token'], $apiKey, $reqUrl, $hsblog, $limit);
            if ($requestDetails) {
                $response = Curl::curlExecute($requestDetails);
                return $response;
            }
        }
        return FALSE;
    }

    public static function getHubspotFiles($reqUrl, $folderId = NULL) {
        $tokenRes = self::getToken();
        $apiKey = get_option('h2w_api');
        $limit = 200;
        if ($tokenRes['status'] || $apiKey) {
            $requestDetails = Url::getFileUrl($tokenRes['token'], $apiKey, $reqUrl, $limit, $folderId, $apiKey);
            if ($requestDetails) {
                $response = Curl::curlExecute($requestDetails);
                return $response;
            }
        }
        return FALSE;
    }

}
