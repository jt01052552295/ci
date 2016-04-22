<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Board_m extends CI_Model {

	function __construct() {
        parent::__construct();
        define( '_TBL', 'ci_board');
    }


	/**
	 * @param  int
	 * @param  int
	 * @param  string
	 * @return object
	 */
	function get_list($offset, $limit, $search_word = '') {
		$limit_query = '';

		if ($limit != '' OR $offset != '') {
            $limit_query = ' LIMIT ' . $offset . ', ' . $limit;
        }

        $sword = '';
		if($search_word != ''){
			$sword = ' and subject like "%' . $search_word . '%" or contents like "%' . $search_word . '%" ';
		}

		$sql = "SELECT * FROM "._TBL." WHERE 1 $sword ORDER BY board_id DESC ".$limit_query;;
		$query = $this->db->query($sql);
		$result = $query->result();				// $result->board_id  
		// $result = $query->result_array();	// $result['board_id']

		return $result;
	}

	/**
	 * @param  string
	 * @return int
	 */
	function get_count($search_word = '') {
		$sword = '';
		if($search_word != ''){
			$sword = ' and subject like "%' . $search_word . '%" or contents like "%' . $search_word . '%" ';
		}
		$sql = "SELECT * FROM "._TBL." WHERE 1 ".$sword;
		$query = $this->db->query($sql);
		$result = $query -> num_rows();

		return $result;
	}

	/**
	 * @param  int
	 * @return object
	 */
	function get_view($id){
		$sql0 = "UPDATE " . _TBL . " SET hits = hits + 1 WHERE board_id='" . $id . "'";
        $this->db->query($sql0);

        $sql = "SELECT * FROM "._TBL." WHERE board_id='" . $id . "'";
		$query = $this->db->query($sql);
		$result = $query->row();

		return $result;
	}

	/**
	 * 게시물 입력
	 * @param  array
	 * @return boolean
	 */
	function insert_board($write_data){

		$insert_array = array();
		$insert_array['board_pid']	=	0;
		$insert_array['user_id']	=	'advisor';
		$insert_array['user_name']	=	'palpit';
		$insert_array['subject']	=	$write_data['subject'];
		$insert_array['contents']	=	$write_data['contents'];
		$insert_array['reg_date']	=	date("Y-m-d H:i:s");

		$result = $this->db->insert(_TBL, $insert_array);

		return $result;

	}

	/**
	 * 게시물 수정
	 * @param  array $modify_data
	 * @return boolean
	 */
	function modify_board($modify_data){

		$modify_array = array();
		$modify_array['subject']	=	$modify_data['subject'];
		$modify_array['contents']	=	$modify_data['contents'];

		$where = array();
		$where['board_id']	= $modify_data['board_id'];

		$result = $this->db->update(_TBL, $modify_array, $where);
        
        return $result;

	}

	/**
	 * 게시물 삭제
	 * @param  int $no [description]
	 * @return boolean     [description]
	 */
	function delete_content($no){

		$delete_array = array();
		$delete_array['board_id']	=	$no;

		$result = $this->db->delete(_TBL, $delete_array);
        
        return $result;

	}

	/**
	 * 댓글 등록
	 * @param  array $arrays [description]
	 * @return int         [description]
	 */
	function insert_comment($arrays) {
        $insert_array = array( 
            'board_pid' => $arrays['board_pid'],
            'user_id' => $arrays['user_id'],
            'user_name' => $arrays['user_id'],
            'subject' => $arrays['subject'],
            'contents' => $arrays['contents'],
            'reg_date' => date('Y-m-d H:i:s')
        );
        
        $this -> db -> insert(_TBL, $insert_array);
        
        $board_id = $this -> db -> insert_id();
        
        return $board_id;
    }

	/**
	 * 댓글 리스트
	 * @param  int $id    	[description]
	 * @return [type]        [description]
	 */
	function get_comment($id) {
        $sql = "SELECT * FROM ". _TBL . " WHERE board_pid = '". $id . "' ORDER BY board_id DESC";
        $query = $this->db->query($sql);
        
        $result = $query -> result();
        
        return $result;
    }

    /**
     * [writer_check description]
     * @return [type] [description]
     */
    function writer_check() {
        $board_id = $this->uri->segment(3);
        
        $sql = "SELECT user_id FROM "._TBL." WHERE board_id = '".$board_id."'";
        $query = $this->db->query($sql);
        
        return $query->row();
        
    }


}