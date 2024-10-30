<?php

/**
 * Description of Url
  Version: 1.0
  Author: Team Webbege
  Author URI: http://www.webbege.com/
 */
class Url {

    public $grantType = null;

    public function __construct() {
        
    }

    public static function getTokenUrl($grantType = NULL, $redirectUri, $code = NULL) {
        $result = array();
        $result['url'] = "https://api.hubapi.com/oauth/v1/token?grant_type={$grantType}&client_id=e6b7f0be-69d6-45e3-bc2c-3d4c9efcc543&client_secret=c0f1225f-aec3-4e51-9de0-43c953ab975d&redirect_uri=" . urlencode($redirectUri) . "&code={$code}";
        $result['headers'] = array(
                'Content-Type' => 'application/x-www-form-urlencoded'
            );
        return $result;
    }

    public static function getRefreshTokenUrl($refreshToken) {
        $result = array();
        $result['url'] = "https://api.hubapi.com/oauth/v1/token?grant_type=refresh_token&client_id=e6b7f0be-69d6-45e3-bc2c-3d4c9efcc543&client_secret=c0f1225f-aec3-4e51-9de0-43c953ab975d&refresh_token={$refreshToken}";
        $result['headers'] = array(
                'Content-Type' => 'application/x-www-form-urlencoded'
            );
        return $result;
    }

    public static function getTokenInfoUrl($accessToken, $req) {
        $result = array();
        $result['url'] = $req . '/' . $accessToken;
        return $result;
    }

    public static function getFormUrl($accessToken, $apiKey) {
        $result = array();
        if($accessToken){
            $result['url'] = "https://api.hubapi.com/forms/v2/forms";
            $result['headers'] = array(
                    'Authorization' => 'Bearer ' . $accessToken
                );
        } else if($apiKey){
            $result['url'] = "https://api.hubapi.com/forms/v2/forms?hapikey=" . $apiKey;
        }
        return $result;
    }

    public static function getDynamicUrl($accessToken = NULL, $apiKey, $reqUri = NULL) {
        $result = array();
        if (!empty($reqUri) && !empty($accessToken)) {
            $result['url'] = $reqUri;
            $result['headers'] = array(
                'Authorization' => 'Bearer ' . $accessToken
            );
            return $result;
        } else if(!empty($reqUri) && !empty($apiKey)) {
            $result['url'] = $reqUri . '?hapikey=' . $apiKey;
            return $result;
        }
        return false;
    }

    public static function getFileUrl($accessToken, $apiKey, $reqUri, $limit, $folderId) {
        $result = array();
        if (!empty($reqUri) && !empty($accessToken)) {
            $result['url'] = $reqUri . '?folder_id=' . $folderId;
            $result['headers'] = array(
                'Authorization' => 'Bearer ' . $accessToken
            );
            return $result;
        } else if(!empty($reqUri) && !empty($apiKey)) {
            $result['url'] = $reqUri . '?folder_id=' . $folderId . '&hapikey=' . $apiKey;
            return $result;
        }
        return false;
    }

    public static function getSearch($portalId, $reqUri, $query, $query_type) {
        $result = array();
        if (!empty($reqUri) && !empty($portalId)) {
            $result['url'] = $reqUri . '?portalId=' . $portalId . '&term=' . $query->query['s'] . '&type=' . $query_type . '&limit=100&length=SHORT&state=PUBLISHED';
            return $result;
        }
        return false;
    }

    public static function getBlogUrl($accessToken, $apiKey, $reqUri) {
        $result = array();
        if (!empty($reqUri) && !empty($accessToken)) {
            $result['url'] = $reqUri;
            $result['headers'] = array(
                'Authorization' => 'Bearer ' . $accessToken
            );
            return $result;
        } else if(!empty($reqUri) && !empty($apiKey)) {
            $result['url'] = $reqUri . '?hapikey=' . $apiKey;
            return $result;
        }
        return false;
    }

    public static function getBlogPosts($accessToken, $apiKey, $reqUri, $hsblog = NULL, $limit) {
        $result = array();
        if (!empty($reqUri) && !empty($accessToken)) {
            $result['url'] = $reqUri . '?content_group_id=' . $hsblog . '&limit=' . $limit . '&state=PUBLISHED';
            $result['headers'] = array(
                'Authorization' => 'Bearer ' . $accessToken
            );
            return $result;
        } else if(!empty($reqUri) && !empty($apiKey)) {
            $result['url'] = $reqUri . '?content_group_id=' . $hsblog . '&limit=' . $limit . '?hapikey=' . $apiKey;
            return $result;
        }

        return false;
    }

}
