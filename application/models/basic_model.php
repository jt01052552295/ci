<?php
class Model_name extends CI_Model {
    function __construct(){
        parent::__construct();
        define( '_TBL', strtolower( get_called_class() ) );
    }
 
    function get_list( $param = NULL ){
        $this->db->select( "SQL_CALC_FOUND_ROWS *", false );
 
        if( !isset( $param['page'] ) ){
            $param['page'] = 1;
        }
 
        $this->db->order_by('idx DESC');
 
        if( isset( $param['limit'] ) ){
            $result = $this->db->get( _TBL, $param['limit'], ( ( $param['page'] - 1 ) * $param['limit'] ) );
        }else{
            $result = $this->db->get( _TBL );
        }
 
        $return['list'] = $result->result_array();
 
        $result = $this->db->query( "SELECT FOUND_ROWS() as cnt" );
        $return['count'] = $result->row( 0 )->cnt;
 
        return $return;
    }
 
    function get_one( $idx = NULL ){
        $this->db->where( 'idx', $idx );
        $result = $this->db->get( _TBL );
        if( $result->num_rows() > 0 ){
            return $result->row_array();
        }else{
            return false;
        }
    }
 
    function create( $param = NULL ){
        foreach( $param as $k => $v ){
            $this->db->set( $k, $v );
        }
        $this->db->insert( _TBL );
 
        return $this->db->insert_id();
    }
 
    function update($idx = NULL, $param = NULL){
        foreach( $param as $k => $v ){
            $this->db->set( $k, $v );
        }
        $this->db->where( 'idx', $idx );
        $this->db->update( _TBL ); 
    }
}