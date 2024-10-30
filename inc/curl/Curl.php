<?php

/**
 * Description of Curl
  Version: 1.0
  Author: Team Webbege
  Author URI: http://www.webbege.com/
 */
class Curl {

    public function __construct() {
        
    }

    public static function curlExecute($request) {
        if (!empty($request)) {
            try {
                $args = array(
                    'timeout'     => 5,
                    'httpversion' => '1.0',
                    'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
                    'headers'     => $request['headers'],
                ); 
                $response = wp_remote_get( $request['url'], $args );
                $body = wp_remote_retrieve_body( $response );
            } catch (Exception $exc) {
                return array('status' => FALSE, 'error' => $exc->getTraceAsString());
            } finally {
                
            }
            return array('status' => TRUE, 'response' => $body);
        }
    }

    public static function getAccessToken($param = array()) {
        if (!empty($param)) {
            try {
                $args = array(
                    'timeout'     => 5,
                    'httpversion' => '1.0',
                    'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
                    'headers'     => $param['headers'],
                ); 
                $response = wp_remote_post( $param['url'] );
                $body = wp_remote_retrieve_body( $response );
            } catch (Exception $exc) {
                return array('status' => FALSE, 'error' => $exc->getTraceAsString());
            } finally {
                
            }
            return array('status' => TRUE, 'response' => $body);
        }
    }

    public static function getAccessTokenInfo($param = array()) {
        if (!empty($param)) {
            try {
                $args = array(
                    'timeout'     => 5,
                    'httpversion' => '1.0',
                    'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
                    'headers'     => $param['headers'],
                ); 
                $response = wp_remote_post( $param['url'] );
                $body = wp_remote_retrieve_body( $response );
            } catch (Exception $exc) {
                return array('status' => FALSE, 'error' => $exc->getTraceAsString());
            } finally {
                
            }
            return array('status' => TRUE, 'response' => $body);
        }
    }


}
