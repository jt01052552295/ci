<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
 
/**
 * 사용자 인증 컨트롤러
 */
 
class Auth extends CI_Controller {

	function __construct() {
        parent::__construct();
        $this -> load -> model('auth_m');
        $this -> load -> helper('form');
    }
 
    public function index() {
        $this -> login();
    }
 
    public function _remap($method) {
        $this -> load -> view('header_v');
 
        if (method_exists($this, $method)) {
            $this -> {"{$method}"}();
        }
 
        $this -> load -> view('footer_v');
    }

    public function login(){
    	$this -> load -> library('form_validation');
 
        $this -> load -> helper('MY_alert');
 
        $this -> form_validation -> set_rules('username', '아이디', 'required|alpha_numeric');
        $this -> form_validation -> set_rules('password', '비밀번호', 'required');
 
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
 
        if ($this -> form_validation -> run() == TRUE) {
        	$auth_data = array(
                'username' => $this -> input -> post('username', TRUE),
                'password' => $this -> input -> post('password', TRUE)
            );
            
            $result = $this -> auth_m -> login($auth_data);
            
            if ($result) {
                $newdata = array(
                    'username' => $result -> username,
                    'email' => $result -> email,
                    'logged_in' => TRUE
                );
                
                $this -> session -> set_userdata($newdata);
                
                alert('로그인 되었습니다.', '/ci/index.php/board/lists/page/1');
                exit;
            } else {
                alert('아이디나 비밀번호를 확인해 주세요.', '/ci/index.php/auth/login');
                exit;
            }

        } else {
        	$this -> load -> view('auth/login_v');
        }
    }

    public function logout() {
        $this -> load -> helper('MY_alert');
        
        $this -> session -> sess_destroy();

        alert('로그아웃 되었습니다.', '/ci/index.php/auth/login');
        exit;
        
    }


}