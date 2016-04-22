<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ajax_board extends CI_Controller {
 

	function __construct() {
        parent::__construct();
    }

    public function test() {
        $this -> load -> view('ajax/test_v');
    }

    public function ajax_action() {
        echo '<meta http-equiv="Content-Type" content="test/html; charset=utf-8" />';
        $name = $this -> input -> post("name");
        echo $name . "님 반갑습니다 !";
    }


    public function ajax_comment_add() {

    	if (@$this -> session -> userdata('logged_in') == TRUE) {
            $this -> load -> model('board_m');
            
            $board_id = $this -> input -> post('board_id', TRUE);
            $comment_contents = $this -> input -> post('comment_contents', TRUE);
            
            if ($comment_contents != '' ){
                $write_data = array(
                    "board_pid" => $board_id,
                    "subject" => '',
                    "contents" => $comment_contents,
                    "user_id" => $this -> session -> userdata('username')
                );
                
                $result = $this -> board_m -> insert_comment($write_data);
                
                if ($result) {
                    $sql = "SELECT * FROM ci_board WHERE board_pid = '". $board_id . "' ORDER BY board_id DESC";
                    $query = $this -> db -> query($sql);
?>
<table cellspacing="0" cellpadding="0" class="table table-striped">
    <tbody>
<?php
                    foreach ($query -> result() as $lt) {
?>
        <tr>
            <th scope="row">
                <?php echo $lt -> user_id;?>
            </th>
            <td><?php echo $lt -> contents;?></td>
            <td>
                <time datetime="<?php echo mdate("%Y-%M-%j", human_to_unix($lt->reg_date));?>">
                    <?php echo $lt -> reg_date;?>
                </time>
            </td>
            <td>
                <a href="#" class="comment_delete" data-col-value="<?php echo $lt->board_id; ?>">
                    <i class="icon-trash"></i>삭제
                </a>
            </td>
        </tr>
<?php
                    }
?>
    </tbody>
</table>
<?php
                } else {
                    // 글 실패시
                    echo "2000";
                }
            } else {
                // 글 내용이 없을 경우
                echo "1000";
            }
        } else {
            // 로그인 필요 에러
            echo "9000";
        }
    




    }


    public function ajax_comment_delete() {
        if ( @$this -> session -> userdata('logged_in') == TRUE) {
            $this -> load -> model('board_m');
            
            $table = $this -> input -> post("table", TRUE);
            $board_id = $this -> input -> post("board_id", TRUE);
            
            
            $result = $this -> board_m -> delete_content($board_id);
            
            if ($result) {
                echo $board_id;
            } else {
                echo "2000"; // 글 실패
            }
            
        } else {
            echo "9000"; // 로그인 에러
        }
    }

}