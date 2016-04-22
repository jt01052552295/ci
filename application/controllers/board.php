<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Board extends CI_Controller {

	function __construct(){
		parent::__construct();
        $this->load->database();
        $this->load->model('board_m');
        $this->load->helper(array('url', 'date'));
	}

	public function index(){
		$this->lists();
	}

	/**
	 * @param  [type]
	 * @return [type]
	 */
	public function _remap($method){
		$this->load->view('header_v');

		if(method_exists($this, $method)){
			$this->{"{$method}"}();
		}

		$this->load->view('footer_v');
	}

	public function lists(){

		// $this->output->enable_profiler(TRUE);
		$search_word = $page_url = '';
		$uri_segment = 4;

		$uri_array = $this->segment_explode($this->uri->uri_string());
		
		if (in_array('q', $uri_array)) {
            // 주소에 검색어가 있을 경우 처리
            $search_word = urldecode($this -> url_explode($uri_array, 'q'));
 
            // 페이지네이션 용 주소
            $page_url = '/q/' . $search_word;
 
            $uri_segment = 6;
        }

		$this->load->library('pagination');
		$config['base_url'] = '/ci/index.php/board/lists'. $page_url .'/page/';
		$config['total_rows'] = $this->board_m->get_count($search_word);
		$config['per_page'] = 10;
		$config['uri_segment'] = $uri_segment;

		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();

		$page = $this->uri->segment($uri_segment, 1);
 		
        if ($page > 1) {
            $start = (($page / $config['per_page'])) * $config['per_page'];
        } else {
            $start = ($page - 1) * $config['per_page'];
        }
 
        $limit = $config['per_page'];
 

		$data['list'] = $this->board_m->get_list($start, $limit, $search_word);
		$this->load->view('board/list_v', $data);
	}

	public function view(){
		$data['views'] = $this->board_m->get_view($this->uri->segment(3));
		$this->load->view('board/view_v', $data);
	}

	public function write(){
		echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';

		$this->load->library('form_validation');
		$this->form_validation->set_rules('subject', '제목', 'required');
        $this->form_validation->set_rules('contents', '내용', 'required');

		if ($this->form_validation->run() == TRUE) {
			$this->load->helper('MY_alert');

			$uri_array = $this -> segment_explode($this->uri->uri_string());
 
            if (in_array('page', $uri_array)) {
                $pages = urldecode($this -> url_explode($uri_array, 'page'));
            } else {
                $pages = 1;
            }

            if (!$this->input->post('subject', TRUE) AND !$this->input->post('contents', TRUE)) {
                // 글 내용이 없을 경우, 프로그램 단에서 한 번 더 체크
                alert('비정상적인 접근입니다.', '/ci/index.php/board/lists');
                exit;
            }

            $write_data = array(
                'subject' 	=> $this->input->post('subject', TRUE), 
                'contents' 	=> $this->input->post('contents', TRUE)
            );
			
			$result = $this->board_m->insert_board($write_data);

			if ($result) {
                alert("입력되었습니다.",'/ci/index.php/board/lists/page/'.$pages);
                exit;
            } else {
                alert("다시 입력해주세요.",'/ci/index.php/board/lists/page/'.$pages);
                exit;
            }
		} else {
			$this->load->view('board/write_v');
		}
	}


	public function modify(){
		echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';

		if ($_POST) {
			$this->load->helper('MY_alert');

			$uri_array = $this -> segment_explode($this->uri->uri_string());
 
            if (in_array('page', $uri_array)) {
                $pages = urldecode($this -> url_explode($uri_array, 'page'));
            } else {
                $pages = 1;
            }

            if (!$this->input->post('subject', TRUE) AND !$this->input->post('contents', TRUE)) {
                // 글 내용이 없을 경우, 프로그램 단에서 한 번 더 체크
                alert('비정상적인 접근입니다.', '/ci/index.php/board/lists');
                exit;
            }

            $modify_data = array(
            	'board_id' 	=> $this->uri->segment(3),
                'subject' 	=> $this->input->post('subject', TRUE), 
                'contents' 	=> $this->input->post('contents', TRUE)
            );
			
			$result = $this->board_m->modify_board($modify_data);

			if ($result) {
                alert("수정되었습니다.",'/ci/index.php/board/lists/page/'.$pages);
                exit;
            } else {
                alert("다시 입력해주세요.",'/ci/index.php/board/view/'.$this->uri->segment(3).'/page/'.$pages);
                exit;
            }
		} else {
			$data['views'] = $this->board_m->get_view($this->uri->segment(3));
			$this->load->view('board/modify_v', $data);
		}
	}

	public function delete(){
		echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
		$this->load->helper('MY_alert');

		$result = $this->board_m->delete_content($this->uri->segment(3));

		if ($result) {
            alert("삭제 되었습니다.",'/ci/index.php/board/lists/page/'.$pages);
            exit;
        } else {
            alert("다시 시도해주세요.",'/ci/index.php/board/view/'.$this->uri->segment(3).'/page/'.$pages);
            exit;
        }
	}

	function segment_explode($seg) {
        // 세그먼트 앞 뒤 "/" 제거 후 uri를 배열로 반환
 
        $len = strlen($seg);
 
        if (substr($seg, 0, 1) == '/') {
            $seg = substr($seg, 1, $len);
        }
 
        $len = strlen($seg);
 
        if (substr($seg, -1) == '/') {
            $seg = substr($seg, 0, $len - 1);
        }
 
        $seg_exp = explode("/", $seg);
        return $seg_exp;
    }

    function url_explode($url, $key) {
        $cnt = count($url);
 
        for ($i = 0; $cnt > $i; $i++) {
        	
            if ($url[$i] == $key) {
                $k = $i + 1;
               	return $url[$k];
            }
        }
    }


}