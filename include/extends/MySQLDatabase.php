<?php

include_once "include/base/SQLDatabase.php";

/*******************************************************************************************************
 * <PRE>
 *
 * SQL�f�[�^�x�[�X�V�X�e���@MySQL�p
 *
 * @author �g���K��Y
 * @original �O�H��q
 * @version 2.0.0
 *
 * </PRE>
 *******************************************************************************************************/

class SQLDatabase extends SQLDatabaseBase
{
	static $commonConnect = null;

	/**
	 * �R���X�g���N�^�B
	 * @param $dbName DB��
	 * @param $tableName �e�[�u����
	 * @param $colName �J���������������z��
	 */
	function __construct($dbName, $tableName, $colName, $colType, $colSize, $colExtend ){

		global $SQL_SERVER;
		global $SQL_ID;
		global $SQL_PASS;
		global $SQL_PORT;
			
		// �t�B�[���h�ϐ��̏�����
		if( !self::$commonConnect )
		{
			if($SQL_PORT != "")
				self::$commonConnect	 = mysqli_connect( $SQL_SERVER.":".$SQL_PORT, $SQL_ID, $SQL_PASS );
			else
				self::$commonConnect	 = mysqli_connect( $SQL_SERVER, $SQL_ID, $SQL_PASS );

			if( !self::$commonConnect ){
				throw new InternalErrorException("SQLDatabase() : DB CONNECT ERROR. -> mysqli_connect( ".$SQL_SERVER." )\n");
			}
			if(  !mysqli_select_db( self::$commonConnect ,$dbName)  ){
				throw new InternalErrorException("SQLDatabase() : DB CONNECT ERROR. -> mysqli_select_db( ". $dbName. " )\n");
			}
		}

		$connect = self::$commonConnect;
		
		$this->init($dbName, $tableName, $colName, $colType, $colSize, $colExtend );

		//mySQL����̏o�̓R�[�h������G���R�[�h��
		if( !function_exists( 'mysqli_set_charset' ) ){
			mysqli_query(self::$commonConnect,"set names sjis");
		//	mysqli_query(self::$commonConnect,"SET NAMES binary;");
		}else{
			//set name ***�ƈႢ�A[mysqli_real_escape_string]�ɂ��L���B  ����php5.2.3�AMySQL5.0.7�ȍ~�̂ݗ��p�\
			mysqli_set_charset(self::$commonConnect,"sjis");
		//	mysqli_set_charset(self::$commonConnect,'binary');
		}
		mysqli_query(self::$commonConnect, "SET SESSION sql_mode = ''");
	}

	function sql_query($sql,$update = false){
		return mysqli_query(self::$commonConnect, $sql );
	}

	function sql_fetch_assoc( &$result ,$index){
		mysqli_data_seek($result , $index);
		return mysqli_fetch_assoc($result);
	}

	function sql_fetch_array( &$result ){
		return mysqli_fetch_array( $result );
	}

	function sql_fetch_all( &$result ){
		if(function_exists('mysqli_fetch_all')){return mysqli_fetch_all( $result );}
	    $all = array();
	    while ($all[] = mysqli_fetch_assoc($result)) {}
	    mysqli_data_seek($result,0);
	    return $all;
	}

	function sql_num_rows( &$result ){
		return mysqli_num_rows( $result );
	}

	function sql_convert( $val ){
		return $val;
	}

	function sql_escape($val){
		global $not_def_mysql_set_charset;
		if( !isset($not_def_mysql_set_charset) ) { $not_def_mysql_set_charset = !function_exists( 'mysqli_set_charset' ); }
		$val = mysqli_real_escape_string(self::$commonConnect,$val);
		if( $not_def_mysql_set_charset )
		{
			if( substr( $val, -1, 1 ) == '\\' ) { $val .= ' '; }
		}

		return $val;
	}
	
	function sql_date_group($column,$format_type,$format=null){
		
		if(is_null($format)){
			switch($format_type){
				case 'y':
					$format = '%Y';
					break;
				case 'm':
					$format = '%Y-%m';
					break;
				case 'd':
				default:
					$format = '%Y-%m-%d';
					break;
			}
		}
		return "FROM_UNIXTIME($column,'$format')";
	}
	
	function sql_to_encrypt( $str, $key ){
		return "AES_ENCRYPT($str,'$key')";
	}
	
	function sql_to_decrypt( $name, $key ){
		return "AES_DECRYPT($name,'$key')";
	}

	function to_boolean( $val ){
		if( is_bool( $val ) )		{ return $val; }
		else if( is_string($val )){
			$val = strtolower($val);
			if( $val == 'false' ){ return false; }
			if( $val == 'true' ){ return true; }
		}
		if( $val == 1 )		{ return true; }
		//else if( !strlen($val) )    { return false;}
		return false;
	}

	private function getColumnType($name){
		$t = $this->getTable();
		$t->offset	 = 0;
		$t->limit	 = 1;
		$ret = $this->sql_query( "SELECT $name FROM ". strtolower($this->tableName)." ".$t->getLimitOffset() );

		return $this->mysqli_field_compat($ret,0,"type");
	}

	private function mysqli_field_compat(&$res, $offset, $key, $compat = true){
		$field = mysqli_fetch_field_direct($res, $offset);
		$result = false;
		if($field){
			switch($key){
				case "flags":
					$list = array();
					$flags = get_flag_names($compat);
					foreach($flags as $num => $name){
						if($field->flags & $num) $list[] = $name;
					}
					$result = implode(" ", $list);
					break;
				case "type":
					$types = get_type_names($compat);
					if(!is_null($types[$field->type])) $result = $types[$field->type];
					break;
				default:
					$result = $field->$key;
					break;
			}
		}
		return $result;
	}

	/**
	 * �e�[�u����Like����
	 * 
	 * @param $table �e�[�u���f�[�^
	 * @param $name �J������
	 * @param $asc �����E�~���� 'asc' 'desc' �Ŏw�肵�܂��B
	 */
	function joinTableLike( &$tbl, $b_name, $n_name, $b_col, $n_col, $n_tbl_name = null ){
		$_b_name = strtolower($this->prefix.$b_name);
		$_n_name = strtolower($this->prefix.$n_name);
		if( !is_null($n_tbl_name) )	{ $_n_name = $_n_name.' '.$n_tbl_name; $_n_tbl_name = $n_tbl_name; }
		else						{ $_n_tbl_name = $_n_name; }
		
		return $this->joinTableSQL( $tbl, $b_name, $n_name, $_n_tbl_name.".".$n_col." like concat( '%', ".$_b_name.".".$b_col.", '%') ", $n_tbl_name );
	}


	function begin(){
		$this->sql_query( "START TRANSACTION" );
	}

	function commit(){
		$this->sql_query("commit");
	}

	function rollback(){
		$this->sql_query("rollback");
	}

}

/*******************************************************************************************************/
/*******************************************************************************************************/
/*******************************************************************************************************/
/*******************************************************************************************************/

class Table extends TableBase{

	function __construct($from){

		$this->select	 = '*';
		$this->from		 = $from;
		$this->delete	 = '( delete_key = FALSE OR delete_key IS NULL )';

		$this->sql_char_code = "EUC-JP";//mysqli_character_set_name();
		parent::__construct($from);
	}

	function getLimitOffset(){
		global $SQL_MASTER;
		if( ($this->offset == 0 || $this->offset != null) && $this->limit != null ){
			$str	 = " LIMIT ". $this->offset. ',' .$this->limit;
			return $str;
		}else{
			return "";
		}
	}

	function sql_convert( $val ){
		return $val;
	}
}
?>