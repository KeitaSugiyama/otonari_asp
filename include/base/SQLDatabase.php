<?php
include_once "include/base/Database.php";
//include_once "include/extends/SQLOutputLog.php";

/*******************************************************************************************************
 * <PRE>
 *
 * SQL�f�[�^�x�[�X�V�X�e�� �x�[�X�N���X
 *
 * @author �g���K��Y
 * @original �O�H��q
 * @version 2.0.0
 *
 * </PRE>
 *******************************************************************************************************/

// define

define( 'TABLE_STATUS_NOMAL', 0 );
define( 'TABLE_STATUS_DELETED', 1 );
define( 'TABLE_STATUS_ALL', 2 );

class SQLDatabaseBase implements DatabaseBase
{

	var $connect;
	var $dbName;
	var $tableName;
	var $tablePlaneName;
	var $colName;
	var $colType;
	var $colSize;
	var $colExtend;
	var $_DEBUG		 = DEBUG_FLAG_SQLDATABASE;
	var $_DEBUG_REC	 = DEBUG_FLAG_RECORD_LOAD;

	var $row = -1;
	var $row_table = null;
	var $rec_cash = null;

	var $sql_char_code;
	var $prefix;

	var $log;

	private $pass_prefix = 'pass_';

	var $char_code;

	/**
	 * �R���X�g���N�^�B
	 * @param $dbName string DB��
	 * @param $tableName string �e�[�u����
	 * @param $colName array �J���������������z��
	 */
	function __construct($dbName, $tableName, $colName, $colType, $colSize, $colExtend ){
		$this->init($dbName, $tableName, $colName, $colType, $colSize, $colExtend);
	}

	function init(&$dbName, &$tableName, &$colName, &$colType, &$colSize, &$colExtend )
	{
//		global $LOG_DIRECTORY_PATH;
		global $DB_LOG_FILE;
		global $ADD_LOG;
		global $UPDATE_LOG;
		global $DELETE_LOG;
		global $TABLE_PREFIX;
		global $SYSTEM_CHARACODE;

		// �t�B�[���h�ϐ��̏�����
//		$this->log		 = new SQLOutputLog($LOG_DIRECTORY_PATH);
		$this->log		 = new OutputLog($DB_LOG_FILE);

		$this->dbName		 = $dbName ;
		$this->tableName	 = strtolower( $TABLE_PREFIX.$tableName );
		$this->tablePlaneName = $tableName;

		array_unshift( $colName, strtolower( 'DELETE_KEY' ));
		array_unshift( $colName, 'shadow_id');
		$colType[ 'shadow_id' ] = 'string';
		$colType[ strtolower( 'DELETE_KEY' ) ] = 'boolean';

		$this->colName		 = $colName;

		$this->colType		 = $colType;
		$this->colSize		 = $colSize;
		$this->colExtend	 = $colExtend;

		$this->addLog		 = $ADD_LOG;
		$this->updateLog	 = $UPDATE_LOG;
		$this->delLog		 = $DELETE_LOG;

		$this->dbInfo		 = $dbName. ",". $tableName;

		$this->prefix		 = strtolower( $TABLE_PREFIX );

		$this->char_code	= $SYSTEM_CHARACODE;
	}

	/**
	 * ���̖��O�̃J���������݂��邩��Ԃ��܂��B
	 * @param $name string �m�F����J������
	 * @return bool �L����boolean�l
	 */
	function isColumn( $name )
	{
		return in_array( $name , $this->colName );
	}


	/**
	 * ���R�[�h���擾���܂��B
	 * @param $table TableBase �e�[�u���f�[�^
	 * @param $index int �擾���郌�R�[�h�ԍ�
	 * @return array ���R�[�h�f�[�^
	 */
	function getRecord($table, $index){
		$rec = null;
		if( $table->cashed && !is_null($table->cash) ){
			if( $this->_DEBUG )				{ d( "getRecord() : ".$this->tableName." load table cash<br/>\n", 'sql'); }
			$result = &$table->cash;
		}else if( ! is_null( $this->rec_cash )){
			if( $this->_DEBUG )				{ d( "getRecord() : ".$this->tableName." load cash<br/>\n", 'sql'); }
			$result = &$this->rec_cash;
		}else{
			$table = $this->addPasswordDecrypt( $table );

			if( $this->_DEBUG ){ d( "getRecord() : ". $table->getString(). "<br/>\n", 'sql' ); }
			$result	 = $this->sql_query( $table->getString() );
			$this->rec_cash = &$result;

			if( $table->cashed ){
				$table->cash = &$result;
			}
		}

		if( !$result ){
			throw new InternalErrorException("getRecord() : SQL MESSAGE ERROR. \n");
		}

		if($this->getRow($table) != 0){
			$rec = $this->sql_fetch_assoc( $result, $index);
			$rec = $this->replacePasswordDecrypt( $rec );
			$rec = $this->getModelRecord( $rec );
		}

		if( $this->_DEBUG_REC ){ d( Array( $this->tableName , $rec ) , 'getRecord' ); }

		return $rec;
	}


	/**
	 * ���R�[�h�̍ŏ��̍s���擾���܂��B
	 * ���݂��Ȃ��ꍇ��false��Ԃ��܂��B
	 *
	 * @param $table TableBase �e�[�u���f�[�^
	 * @return array|bool ���R�[�h�f�[�^|false
	 */
	function getFirstRecord($table){
		$rec = null;
		if( $table->cashed && !is_null($table->cash) ){
			if( $this->_DEBUG )				{ d( "getFirstRecord() : ".$this->tableName." load table cash<br/>\n", 'sql'); }
			$result = &$table->cash;
		}else if( ! is_null( $this->rec_cash )){
			if( $this->_DEBUG )				{ d( "getFirstRecord() : ".$this->tableName." load cash<br/>\n", 'sql'); }
			$result = &$this->rec_cash;
		}else{
			$table = $this->addPasswordDecrypt( $table );
			$table = $this->limitOffset( $table, 0 , 1 );


			if( $this->_DEBUG ){ d( "getFirstRecord() : ". $table->getString(). "<br/>\n", 'sql' ); }
			$result	 = $this->sql_query( $table->getString() );
		}

		if( !$result ){
			throw new InternalErrorException("getFirstRecord() : SQL MESSAGE ERROR. \n");
		}

		if($this->getRow($table) != 0){
			$rec = $this->sql_fetch_assoc( $result,0);
			$rec = $this->replacePasswordDecrypt( $rec );
			return $rec;
		}

		return false;
	}


	/**
	 * ���R�[�h���擾���܂��B
	 *
	 * @param $id string �擾���郌�R�[�hID
	 * @param $type string ����ΏۂƂȂ�e�[�u����type(nomal/delete/all)
	 * @return array ���R�[�h�f�[�^�B���R�[�h�f�[�^�����݂��Ȃ��ꍇnull��Ԃ��B
	 */
	function selectRecord( $id , $type = null)
	{
		if( is_null($id) ){ return null;}

		$table	 = $this->getTable($type);
		$table	 = $this->searchTable( $table, 'id', '=', $id );
		if( $this->existsRow($table) )
		{// ���R�[�h�����݂���ꍇ
			$rec	 = $this->getRecord( $table, 0 );
			return $rec;
		}
		else	{ return null; }
	}

	/**
	 * �e�[�u������w�肵�����R�[�h���폜���܂��B
	 * @param $table TableBase �e�[�u���f�[�^
	 * @param $rec string �폜�ΏۂƂȂ郌�R�[�h
	 * @return TableBase �e�[�u���f�[�^
	 */
	function pullRecord($table, $rec){
		return $this->searchTable( $table, 'shadow_id', '!', $this->getData( $rec, 'shadow_id' ) );
	}

	/**
	 * �f�[�^�̓��e���擾����B
	 * @param $rec array ���R�[�h�f�[�^
	 * @param $name string �J������
	 * @return string|bool|int �l
	 */
	function getData($rec, $name, $br = false){
		$name	 = strtolower( $name );

		if(  isset( $rec[ $name ] )  ){
			$type = isset( $this->colType[ $name ] )? $this->colType[ $name ] : 'function';
			switch( $type ){
				case 'boolean':
					return $this->to_boolean( $rec[ $name ] );
				case 'password':
				default:
					if(  is_string( $rec[ $name ] )  ){
						if( !$br ){
							return str_replace(  "\r\n", "\n", $this->sql_convert( $rec[ $name ] )  );
						}else{
							return brChange( $this->sql_convert( $rec[ $name ] ) );
						}
						break;
					}
			}
			return $rec[ $name ];
		}else{
			return null;
		}
	}

	function getDataList($table, $name, $key = 'shadow_id' ){
		if( ! is_null( $this->rec_cash )){
			if( $this->_DEBUG )				{ d( "getDataList() : ".$this->tableName." load cash<br/>\n", 'sql' ); }
			$result = &$this->rec_cash;
		}else{

			if( $this->_DEBUG ){ d( "getDataList() : ". $table->getString(). "<br/>\n", 'sql' ); }

			$table = $this->addPasswordDecrypt( $table );

			$result	 = $this->sql_query( $table->getString() );
			$this->rec_cash = &$result;
		}

		if( !$result ){
			throw new InternalErrorException("getDataList() : SQL MESSAGE ERROR. \n");
		}

		if( $this->colType[ $name ] == 'password' ){
			$name = $this->pass_prefix .$name;
		}

		$list = null;
		$row = $this->getRow($table);
		if($row != 0){

			for( $i=0;$row>$i;$i++){
				$rec = $this->getRecord( $table, $i );
				
				if( is_array( $name ) ){
					$l = Array();
					foreach( $name as $n ){
						$l[$n] = $this->getData( $rec, $n );
					}
				}else{
					$l = $this->getData( $rec, $name );
				}
				$key_data = $this->getData( $rec, $key );
				if( !is_null( $key_data ) ){
					$list[ $key_data ] = $l;
				}else{
					$list[] = $l;
				}

			}
			/*$recs = $this->sql_fetch_all( $result );

			$list = Array();
			foreach( $recs as $row ){
				if( is_array( $name ) ){
					$l = Array();
					foreach( $name as $n ){
						$l[$n] = $this->sql_convert( $row[$n] );
					}
					if(isset( $row[ $key ])){
						$list[ $this->sql_convert( $row[ $key ] ) ] = $l;
					}else{
						$list[] = $l;
					}
				}else{
					if(isset($row[ $key ])){
						$list[ $this->sql_convert( $row[ $key ] ) ] = $this->sql_convert( $row[$name] );
					}else{
						$list[] = $this->sql_convert( $row[$name] );
					}
				}
			}*/
		}

		return $list;
	}

// begin �A�t�B���G�C�g�V�X�e��PRO2 ��p ����
	/**
	 * ���R�[�h�̓��e���X�V����B
	 * DB�t�@�C���ւ̍X�V���܂݂܂��B
	 * @param $rec ���R�[�h�f�[�^
	 */
	function updateRecord($rec){
		$sql	 = "UPDATE ". strtolower($this->tableName). " SET ";
		$sql	 .= $this->toString( $rec, "UPDATE" );
		$sql	 .= " WHERE SHADOW_ID = ". $this->getData( $rec, 'SHADOW_ID' );
		if( $this->_DEBUG ){ d( "updateRecord() : ". $sql. "<br/>\n", 'sql' ); }

		$this->sql_query( $sql );
			
		if($this->updateLog == true){
			$str = $rec['SHADOW_ID']. ",".$rec['DELETE_KEY']. ",";
			$cnt = count($this->colName);
			for($i=0; $i<$cnt; $i++){
				$str .= $rec[ $this->colName[$i] ]. ",";
			}
			$this->log->write('UPDATE,'. $this->dbInfo. ",". $str);
		}
		$this->cashReset();
	}
// end �A�t�B���G�C�g�V�X�e��PRO2 ��p ����


	/**
		@brief     �����̃��R�[�h�𓯎���UPDATE����B
		@details   ELT/FIELD�֐��̑g�ݍ��킹�ɂ���ĕ������R�[�h�ɑ΂���UPDATE�����SQL�ɂ܂Ƃ߂Ď��s���܂��B
		@param[in] $recs ���R�[�h�z��B
		@exception Logic $recs ���z��ł͂Ȃ��ꍇ�B
		@remarks   ���̊֐���SQLite�ɂ͖��Ή��ł��B
		@remarks   ���̊֐�����������SQL�͒���ɂȂ�\�����������߁A���܂苐�傷����z������Ȃ��悤�ɂ��Ă��������B���R���͍��ڂ�����e�[�u���̏ꍇ�͓��ɒ��ӂ��Ă��������B
	*/
	function bulkUpdate( $recs ) //
	{
		$ids        = Array();
		$columns    = Array();
		$datas      = Array();
		$updateSQLs = Array();
		$sql        = '';

		if( !is_array( $recs ) ) //�z��ł͂Ȃ��ꍇ
			{ throw new LogicException( '�s���Ȉ����ł�' ); }

		if( !count( $recs ) ) //�z�񂪋�̏ꍇ
			{ return; }

		foreach( $this->colName as $column ) //�S�ẴJ����������
		{
			if( 'shadow_id' == $column || 'delete_key' == $column || 'id' == $column ) //�X�V���O����J�����̏ꍇ
				{ continue; }

			if( 'fake' == $this->colType[ $column ] ) //fake�J�����̏ꍇ
				{ continue; }

			$columns[] = $column;
		}

		foreach( $recs as $rec ) //�S�Ẵ��R�[�h������
		{
			$ids[] = $this->getData( $rec , 'shadow_id' );

			foreach( $columns as $column ) //�X�V�Ώۂ̃J����������
			{
				$type = $this->colType[ $column ];
				$data = $this->getData( $rec , $column );

				if( is_array( $data ) ) //�l���z��̏ꍇ
					{ $data = $this->sqlDataEscape( join( $data , '/' ) , $type );}
				else //�l���X�J���̏ꍇ
					{ $data = $this->sqlDataEscape( $data , $type ); }

				$datas[ $column ][] = $data;
			}
		}

		$sql = 'UPDATE ' . $this->tableName . ' SET ';

		foreach( $columns as $column ) //�S�Ă̒l�̃Z�b�g������
			{ $updateSQLs[] = $column . ' = ELT( FIELD( shadow_id , ' . implode( ' , ' , $ids ) . ' ) , ' . implode( ' , ' , $datas[ $column ] ) . ' )'; }

		$sql    .= implode( ' , ' , $updateSQLs );
		$sql    .= 'WHERE shadow_id IN ( ' . implode( ' , ' , $ids ) . ' )';
		$result  = $this->sql_query( $sql );

		if( $this->updateLog == true ) //�X�V���O���c���ꍇ
		{
			foreach( $recs as $rec ) //�S�Ẵ��R�[�h������
				{ $this->log->table_log( $this->tableName , 'UPDATE' , implode( ',' , array_values( $rec ) ) ); }
		}

		$this->cashReset();
		TemplateCache::SetDBUpdateTime();
	}

	/**
	 * �����Ƃ��ēn���ꂽtable�̑S�s�Ƀf�[�^���Z�b�g����update����B
	 *
	 * @param $table TableBase �X�V���s�Ȃ��J�����̓�����table
	 * @param $name string �J������
	 * @param $val string|int|bool �l
	 * @param $escape bool �G�X�P�[�v�����s���邩�w��Bval�ɃJ�������w�肵�����ꍇ��false
	 * @return TableBase
	 */
	function setTableDataUpdate(&$table, $name, $val, $escape = true )
	{
		if( $table->status != TABLE_STATUS_NOMAL ){return;}

		if(!$this->existsRow($table)){
			return $table;
		}

		$sql	 = "UPDATE ". $this->tableName. " SET ";
		if($escape) { $val = $this->sqlDataEscape($val,$this->colType[$name]); }
		if($this->isColumn($name)) { $sql .= $name ."=" .$val; }
		else                       { return ;} // name���e�[�u���̃J�����ɂȂ��ꍇ
		$sql	 .= $table->getWhere();
		if( $this->_DEBUG ){ d( "setTableDataUpdate() : ". $sql. "<br/>\n", 'sql'); }

		$this->sql_query( $sql );

		if($this->updateLog == true){
			$str = $table->getWhere(). ",".$name."=".$val. ",";//.$row;
			$this->log->table_log($this->tableName,'TABLE_UPDATE',$str);
		}
		$this->cashReset();

		TemplateCache::SetDBUpdateTime();
		return $table;
	}

	/**
	 * �����Ƃ��ēn���ꂽtable�̑S�s�Ƀf�[�^���Z�b�g����update����B
	 *
	 * @param $table TableBase �X�V���s�Ȃ��J�����̓�����table
	 * @param $name array �J�������̔z��
	 * @param $val array �l�̔z��
	 * @return TableBase
	 */
	function setTablePluralDataUpdate(&$table, $name=array(), $val=array() )
	{
		if( $table->status != TABLE_STATUS_NOMAL ){return;}

		if(!$this->existsRow($table)){
			return $table;
		}

		if($name==array() || $val==array() || count($name)!=count($val)){
			return $table;
		}

		$row=count($name);
		$sql	 = "UPDATE ". $this->tableName. " SET ";
		$set	 = "";
		for($i=0;$i<$row;$i++){
			$val[$i]  = $this->sqlDataEscape($val[$i],$this->colType[$name[$i]]); 
			if($this->isColumn($name[$i])) { $set .= $name[$i] ."=" .$val[$i]; }
			if( $i != $row - 1 ){
				$set .= ", ";
			}
		}
		if(strlen($set) < 1) return $table;
		$sql	 .= $set;
		$sql	 .= $table->getWhere();
		if( $this->_DEBUG ){ d( "setTableDataUpdate() : ". $sql. "<br/>\n", 'sql'); }

		$this->sql_query( $sql );

		if($this->updateLog == true){
			$str = $table->getWhere(). ",".$set.",";
			$this->log->table_log($this->tableName,'TABLE_UPDATE',$str);
		}
		$this->cashReset();

		return $table;
	}


// begin �A�t�B���G�C�g�V�X�e��PRO2 ��p ����
	/**
	 * ���R�[�h�̍폜�B
	 * DB�t�@�C���ւ̔��f���s���܂��B
	 * @param $rec ���R�[�h�f�[�^
	 */
	function deleteRecord(&$rec){
		$rec	 = $this->setData( $rec, 'delete_key', true );
		$this->updateRecord( $rec );
			
		if($this->delLog == true){

			$str = "";
			for($i=0; $i<count($this->colName) + 2; $i++){
				switch($i){
					case 0:
						$str .= $rec['SHADOW_ID']. ",";
						break;
					case 1:
						$str .= $rec['DELETE_KEY']. ",";
						break;
					default:
						$str .= $rec[ $this->colName[$i - 2] ]. ",";
				}
			}

			$this->log->write('DELETE,'. $this->dbInfo. ",". $str);
		}
		$this->cashReset();
		
		return $rec;
	}
// end �A�t�B���G�C�g�V�X�e��PRO2 ��p ����

	function deleteFile($filename){
		//unlink( $filename );
		global $FileBase;

		if( strlen( $filename ) && $FileBase->file_exists( $filename ) ){
			//�t�@�C���p�X�̍ŏ��Ɍ����/file/�̌���delete/��t�^����
			//$delname = substr_replace( $filename, 'delete/', strrpos( $filename, 'file/' )+5, 0 );
			$delname = str_replace( 'file/', 'file/delete/',$FileBase->getfilepath($filename) );

			//�K�v�ȃf�B���N�g���̐���
			SystemUtil::mkdir( $delname );

			//�t�@�C���̈ړ�
			$FileBase->rename( $filename, $delname );
			if( $FileBase->file_exists($filename) && $FileBase->copy($filename, $delname ) ){
				$FileBase->delete($filename);
				@chmod($delname, 0766);
			}
			return $delname;
		}
		return null;
	}

	/**
	 * where�ɂ���đI�������e�[�u���̍s���폜���܂��B
	 * @param $table TableBase �e�[�u���f�[�^
	 * @return int �s��
	 */
	function deleteTable($table){
		global $CONFIG_SQL_FILE_TYPES;
		global $LOGIN_ID;
		global $loginUserType;

		if( $table->status != TABLE_STATUS_NOMAL ){return;}

		/*
		//image,file�^�̃J�������X�g�����
		$keys = Array();
		foreach( $this->colType as $key => $type ){
			if( in_array( $type, $CONFIG_SQL_FILE_TYPES )  ){
				$keys[] = $key;
			}
		}
		*/
		$this->setTableDataUpdate( $table, 'delete_key', true );

		//file�̍폜(�ړ�)
		/*
		if( count($keys) ){
			$file_datas = $this->getDataList( $table, $keys );
			foreach( $file_datas as $shadow_id => $files ){
				foreach( $files as $filename ){
					$this->deleteFile( $filename );
				}
			}
			//replace���g��
			$this->replaceTable( $table, $keys,  'file/','file/delete/' );
		}
		*/

		$delete_key = "delete_key = ".$this->sqlDataEscape(true,'boolean').' ';

		$sql	 = 'INSERT INTO '. $this->tableName.'_delete'.' SELECT *' ;
		$sql	 .= ',\''.$loginUserType.'\',\''.$LOGIN_ID.'\','.time();
		$sql	 .= ' FROM '.$this->tableName;
		$sql	 .= ' WHERE '.$delete_key;

		if( $this->_DEBUG )				{ d( "deleteTable() : ". $sql. "<br/>\n", 'sql' ); }
		$result	 = $this->sql_query( $sql );
		if( !$result ){ throw new InternalErrorException("deleteTable() : SQL MESSAGE ERROR. \n"); }

		$sql	  = 'DELETE FROM '. $this->tableName;
		$sql	 .= ' WHERE '.$delete_key;
		if( $this->_DEBUG )				{ d( "deleteTable() : ". $sql. "<br/>\n", 'sql' ); }
		$result	 = $this->sql_query( $sql );
		if( !$result ){ throw new InternalErrorException("deleteTable() : SQL MESSAGE ERROR. \n"); }

		$this->cashReset();
		TemplateCache::SetDBUpdateTime();
		return;
	}

	/**
	 * ���R�[�h�̕����B
	 * DB�t�@�C���ւ̔��f���s���܂��B
	 * @param $rec array ���R�[�h�f�[�^
	 */
	function restoreRecord(&$rec){
		global $CONFIG_SQL_FILE_TYPES;
		if(!$rec){ return; }

		foreach( $this->colType as $key => $type ){
			if( in_array( $type, $CONFIG_SQL_FILE_TYPES )  ){
				$filename = $this->getData( $rec, $key );
				$ret_name = $this->restoreFile( $filename );
				if(!is_null($ret_name)){	$this->setData( $rec, $key, $ret_name );	}
			}
		}
		$this->updateRecord( $rec );

		$rec	 = $this->setData( $rec, 'delete_key', false );

		$sql	 = "INSERT INTO ". $this->tableName. " (\n";
		// �J���������X�g���o��

		$columns = Array();

		foreach( $this->colName as $column )
		{
			if( 'fake' == $this->colType[ $column ] )
				{ continue; }

			$columns[] = $column;
		}

		$sql .= implode( ', ' . "\n" , $columns );

		$sql	 .= ")VALUES ( ";
		$sql	 .= $this->toString( $rec, "INSERT" );
		$sql	 .= " )";
		if( $this->_DEBUG ){ d( "restoreRecord() : ". $sql. "<br/>\n", 'sql' ); }

		$result = $this->sql_query( $sql );

		if( $result ){
			$sql	 = "DELETE FROM ". $this->tableName. "_delete WHERE shadow_id = ".$this->getData( $rec, 'shadow_id' );
			if( $this->_DEBUG ){ d( "restoreRecord() ". $sql. "<br/>\n", 'sql' ); }
			$result = $this->sql_query( $sql );
		}

		if($this->delLog == true){
			$str = "";
			$row = count($this->colName);
			for($i=0; $i<$row; $i++){
				$str .= $rec[ $this->colName[$i] ]. ",";
			}

			$this->log->table_log($this->tableName,'RESTORE',$str);
		}
		$this->cashReset();
		TemplateCache::SetDBUpdateTime();
		return $rec;
	}

	function restoreFile( $delname ){
		if( strlen( $delname ) && file_exists( $delname ) ){
			//�t�@�C���p�X�̍ŏ��Ɍ����/file/�̌���delete/��t�^����
			$filename = str_replace( 'file/delete/','file/', $delname );

			//�K�v�ȃf�B���N�g���̐���
			SystemUtil::mkdir( $filename );

			//�t�@�C���̈ړ�
			rename( $delname,$filename );
			return $filename;
		}
		return "";
	}


	/**
	 * where�ɂ���đI�������e�[�u���𕜌����܂��B
	 * @param $table TableBase �e�[�u���f�[�^
	 * @return int �s��
	 */
	function restoreTable($table){
		global $CONFIG_SQL_FILE_TYPES;

		if( $table->status != TABLE_STATUS_DELETED ){return;}

		//image,file�^�̃J�������X�g�����
		$keys = Array();
		foreach( $this->colType as $key => $type ){
			if( in_array( $type, $CONFIG_SQL_FILE_TYPES )  ){
				$keys[] = $key;
			}
		}
		//file�̕���(�ړ�)
		if( count($keys) ){
			$file_datas = $this->getDataList( $table, $keys );
			foreach( $file_datas as $files ){
				foreach( $files as $filename ){	$this->restoreFile( $filename );	}
			}
		}

		//replace���g��
		$this->replaceTable( $table, $keys,  'file/delete/','file/',
			 Array( 'delete_key' => false ) );

		$delete_key = "delete_key = ".$this->sqlDataEscape(false,'boolean').' ';
		$delete_table = $this->tableName.'_delete';

		$sql	 = 'INSERT INTO '. $this->tableName.' SELECT ' ;
		$sql	.= implode(',',$this->colName);

		$sql	 .= ' FROM '.$delete_table;

		$sql	 .= ' WHERE '.$delete_key;
		if( $this->_DEBUG )				{ d( "restoreTable() : ". $sql. "<br/>\n", 'sql' ); }
		$result	 = $this->sql_query( $sql );
		if( !$result ){ throw new InternalErrorException("restoreTable() : SQL MESSAGE ERROR. \n"); }

		$sql	  = 'DELETE FROM '. $delete_table;
		$sql	 .= ' WHERE '.$delete_key;
		if( $this->_DEBUG )				{ d( "restoreTable() : ". $sql. "<br/>\n", 'sql' ); }
		$result	 = $this->sql_query( $sql );
		if( !$result ){ throw new InternalErrorException("restoreTable() : SQL MESSAGE ERROR. \n"); }

		$this->cashReset();
		TemplateCache::SetDBUpdateTime();
		return;
	}

	/**
	 * �e�[�u���̎w��J������u������B
	 * @param $table TableBase replace�����s����e�[�u���N���X�̃C���X�^���X
	 * @param $column string replace��K�p����J�����A�z���n�����ꍇ�͊e�s�ɑ΂���replace���s�Ȃ���
	 * @param $search string �������镶��
	 * @param $replace string �u�����镶��
	 * @param $set array *�C�� replace��update�𑖂点��ۂɓ����ɕύX��K�p�������ꍇ�Ɏg�p����
	 */
	function replaceTable( $table, $column, $search, $replace, $set = null ){
		switch( $table->status ){
			case TABLE_STATUS_DELETED: $table_name = $this->tableName.'_delete'; break;
			case TABLE_STATUS_NOMAL: $table_name = $this->tableName; break;
			case TABLE_STATUS_ALL: return;
		}

		$set_str = "";
		if( isset($set) && is_array($set) && count($set) ){
			foreach( $set as $key => $s ){
				$set_str = " $key = " . $this->sqlDataEscape($s, $this->colType[$key] ).', ';
			}
		}

		if(is_array($column)){
			$key_sets = Array();
			foreach( $column as $col ){
				$key_sets[] = "$col = replace( $col, '$search', '$replace' )";
			}
			$set_str .= join( ',', $key_sets );
		}else{
			$set_str .= "$column = replace( $column, '$search', '$replace' )";
		}

		//replace���s
		$sql	 = 'UPDATE '. $table_name.' SET '.$set_str. $table->getWhere();
		if( $this->_DEBUG )				{ d( "replaceTable() : ". $sql. "<br/>\n", 'sql' ); }
		$result	 = $this->sql_query( $sql );
		if( !$result ){ throw new InternalErrorException("replaceTable() : SQL MESSAGE ERROR. \n"); }

		TemplateCache::SetDBUpdateTime();
		return;
	}

	/**
	 * �f�[�^���Z�b�g����B
	 * @param $rec array ���R�[�h�f�[�^
	 * @param $name string �J������
	 * @param $val string|int|bool �l
	 * @return array
	 */
	function setData(&$rec, $name, $val){
		$name			 = strtolower( $name );

		if( !empty($this->colExtend[$name]) ){
			$val = GUIManager::replaceString( $val, $this->colExtend[$name], $this->colType[$name] );
		}

		if(  is_bool( $val )  ){
			if( $val ){
				$val	 = 'TRUE';
			}else{
				$val	 = 'FALSE';
			}
		}

		$rec[ $name ]	 = $this->sql_convert($val);

		return $rec;
	}

	/**
	 * �ȈՂȉ��Z���s�Ȃ��A���ʂ��Z�b�g����B
	 * �J���������l�^�Ŗ����ꍇ�͖���
	 *
	 * @param $rec array ���R�[�h�f�[�^
	 * @param $name string �J������
	 * @param $opp string ���Z�q
	 * @param $name string|int|bool �l
	 * @return Array $rec
	 */
	function setCalc(&$rec, $name , $opp , $val )
	{
		if( array_search( $opp, Array('+','-','/','*','%')) === FALSE && is_numeric($val) ){
			return null;
		}
		switch($this->colType[$name]){
			case 'string':
			case 'varchar' :
			case 'password':
			case 'char' :
			case 'image':
			case 'boolean':
				return null;
				break;
			default:
				$old = $this->getData( $rec , $name );
				$new = eval('return ' .$old.$opp.$val.";");
				$this->setData( $rec , $name , $new );
				break;
		}
		return $rec;
	}

	/**
	 * ���R�[�h�Ƀf�[�^���܂Ƃ߂ăZ�b�g���܂��B
	 * @param $rec array ���R�[�h�f�[�^
	 * @param $data array �f�[�^�A�z�z��i�Y�����̓J�������j
	 * @return array $rec(���R�[�h�f�[�^)
	 */
	function setRecord(&$rec, $data){
		$tmp	 = $this->getData( $rec, 'shadow_id' );
		$rec	 = $this->getNewRecord( $data );
		$this->setData( $rec, 'shadow_id', $tmp );
		return $rec;
	}

	/**
	 * �V�������R�[�h���擾���܂��B
	 * �f�t�H���g�l���w�肵�����ꍇ��
	 * $data['�J������']�̘A�z�z��ŏ����l���w�肵�Ă��������B
	 * @param $data array �����l��`�A�z�z��
	 * @return array $rec(���R�[�h�f�[�^)
	 */
	function getNewRecord($data = null){
		global $CONFIG_SQL_FILE_TYPES;

		$rec = $this->getModelRecord( array() );

		// ���R�[�h�̒��g�� null �ŏ�����
		for($i=0; $i<count( $this->colName ); $i++){
			$rec[ $this->colName[$i] ] = null;
		}

		// �����l���w�肳��Ă��Ȃ���� return
		if(  !isset( $data )  ){ return $rec; }

		// �����l����
		for($i=0; $i<count( $this->colName ); $i++){
			$name = $this->colName[$i];
			if( in_array( $this->colType[ $name ] , $CONFIG_SQL_FILE_TYPES ) ){
				// �f�[�^�t�@�C���̏ꍇ
				$this->setFile( $rec, $name );
			}else{

				switch( $this->colType[ $name ] ){
					case  'timestamp':
						//timestamp�̏ꍇ{�J������}_year��������ΐ�������B
						if( isset( $data[ $name.'_year' ] ) ){
							$hasY = is_numeric( $data[ $name.'_year' ] );
							$hasM = isset( $data[ $name.'_month' ] ) && is_numeric( $data[ $name.'_month' ] );
							$hasD = isset( $data[ $name.'_day' ] ) && is_numeric( $data[ $name.'_day' ] );

							$y = $hasY ? $data[ $name.'_year' ]  : null;
							$m = $hasM ? $data[ $name.'_month' ] : 1;
							$d = $hasD ? $data[ $name.'_day' ]   : 1;

							if( !$hasY && !$hasM && !$hasD )
								{ $this->setData( $rec, $name, 0 ); }
							else
								{ $this->setData( $rec, $name, mktime(0,0,0,$m,$d,$y) ); }
							continue 2;
						}
						break;
					case 'date':
						if( isset( $data[ $name.'_year' ] ) ){
							$y = is_numeric( $data[ $name.'_year' ] )? $data[ $name.'_year' ]:date('Y');
							$m = ( isset( $data[ $name.'_month' ] ) && is_numeric( $data[ $name.'_month' ] ) ) ? $data[ $name.'_month' ] : 1;
							$d = ( isset( $data[ $name.'_day' ] ) && is_numeric( $data[ $name.'_day' ] ) ) ? $data[ $name.'_day' ] : 1;
							$this->setData( $rec, $name, sprintf( "%4d-%02d-%02d",$y,$m,$d ));
							continue 2;
						}
						break;
				}
				if( isset( $data[ $name ] ) && $data[ $name ] != null ){
					if( is_array( $data[ $name ] ) ){
						$str = '';
						for($j=0; $j<count(  $data[ $name ]  ); $j++){
							$str .= $data[ $name ][$j];
							if( $j != count( $data[  $name ] ) - 1 ){
								$str .= '/';
							}
						}
						$this->setData( $rec, $name, $str );
					}else{
						if( is_bool( $data[ $name ] ) ){
							$this->setData( $rec, $name, $data[ $name ] );
						}else if( strtolower( $data[ $name ] ) == 'true' ){
							$this->setData( $rec, $name, true );
						}else if( strtolower( $data[ $name ] ) == 'false' ){
							$this->setData( $rec, $name, false );
						}else{
							$data[ $name ] = GUIManager::replaceString( $data[ $name ], $this->colExtend[$name], $this->colType[$name] );
							$this->setData( $rec, $name, $data[ $name ] );
						}
					}
				}
			}
		}
		// �Öق̎�L�[���`
		$this->setData(   $rec, 'shadow_id', $this->getMaxID()+1);
		return $rec;
	}

	function setFile(&$rec, $colname){
		$sys	 = SystemUtil::getSystem( $_GET["type"] );
		$sys->doFileUpload( $this, $rec, $colname, $_FILES );
	}

	/**
	 * ���R�[�h�̒ǉ��B
	 * DB�ւ̔��f�������ɍs���܂��B
	 * @param $rec array ���R�[�h�f�[�^
	 */
	function addRecord(&$rec){
		global $ID_LENGTH;
		global $USE_REGISTER_PROCCESS_LOCK;
		global $THIS_TABLE_IS_NOACCURACY;

		// SQL���̐������J�n
		$sql	 = "INSERT INTO " . $this->tableName . " (";

		// �J���������X�g���o��

		$columns = Array();

		foreach( $this->colName as $column )
		{
			if( 'fake' == $this->colType[ $column ] )
				{ continue; }

			$columns[] = $column;
		}

		$sql .= implode( ', ' . "\n" , $columns );

		// �d���������ׂɈÖق̎�L�[���Đݒ�

		if( $USE_REGISTER_PROCCESS_LOCK && ( !isset( $THIS_TABLE_IS_NOACCURACY[ $this->tablePlaneName ] ) || !$THIS_TABLE_IS_NOACCURACY[ $this->tablePlaneName ] ) )
		{
			if( !SystemUtil::lockProccess( 'addRecord_' . $this->tableName , 10 , 100000 ) )
				{ throw new Exception(); }
		}

		$this->setData(   $rec, 'shadow_id', $this->getMaxID()+1);
// access �� aid ���j�󂳂��̂Ń`�F�b�N���Ȃ�
//		if( $ID_LENGTH[ $this->tablePlaneName ] > 0 ){
//			$this->setData(  $rec, 'id', SystemUtil::getNewId( $this, $this->tablePlaneName ) );
//		}

		$sql	 .= ")VALUES ( ";
		$sql	 .= $this->toString( $rec, "INSERT" );
		$sql	 .= " )";
		if( $this->_DEBUG ){ d( "addRecord() : ". $sql. "<br/>\n", 'sql' ); }

		$return = $this->sql_query( $sql );
// begin �A�t�B���G�C�g�V�X�e��PRO2 ��p ����
/* system_tables �Ȃ�
		if($return){
			$sql = "UPDATE ".$this->prefix."system_tables SET id_count = '".$this->getData( $rec, 'shadow_id')."' WHERE table_name = '". $this->tablePlaneName ."'";
			$this->sql_query( $sql );
		}
*/
// end �A�t�B���G�C�g�V�X�e��PRO2 ��p ����
		if( $USE_REGISTER_PROCCESS_LOCK )
			{ SystemUtil::unlockProccess( 'addRecord_' . $this->tableName ); }

		if($this->addLog == true){
			$str = "";
			$row = count($this->colName);
			for($i=0; $i<$row; $i++){
				$str .= $rec[ $this->colName[$i] ]. ",";
			}

			$this->log->table_log($this->tableName,'ADD',$str);
		}

		TemplateCache::SetDBUpdateTime();
		$this->cashReset();
	}

	/**
	 * ���R�[�h�̕����ǉ��B
	 * DB�ւ̔��f�������ɍs���܂��B
	 * @param $recList array ���R�[�h�f�[�^�̔z��
	 */
	function addRecordList($recList){
		global $ID_LENGTH;
		global $USE_REGISTER_PROCCESS_LOCK;
		global $THIS_TABLE_IS_NOACCURACY;

		// SQL���̐������J�n
		$sql	 = "INSERT INTO " . $this->tableName . " (";

		// �J���������X�g���o��

		$columns = Array();

		foreach( $this->colName as $column )
		{
			if( 'fake' == $this->colType[ $column ] )
				{ continue; }

			$columns[] = $column;
		}

		$sql .= implode( ', ' . "\n" , $columns );

		// �d���������ׂɈÖق̎�L�[���Đݒ�

		if( $USE_REGISTER_PROCCESS_LOCK && ( !isset( $THIS_TABLE_IS_NOACCURACY[ $this->tablePlaneName ] ) || !$THIS_TABLE_IS_NOACCURACY[ $this->tablePlaneName ] ) )
		{
			if( !SystemUtil::lockProccess( 'addRecord_' . $this->tableName , 10 , 100000 ) )
				{ throw new Exception(); }
		}

		$sql	 .= ")VALUES ";
		$values   = Array();
		$shadowID = $this->getMaxID();

		foreach( $recList as &$rec )
		{
			++$shadowID;

			$this->setData(   $rec, 'shadow_id', $shadowID);
			if( $ID_LENGTH[ $this->tablePlaneName ] > 0 ){
				$this->setData(  $rec, 'id', SystemUtil::getNewId( $this, $this->tablePlaneName , $shadowID ) );
			}

			$values[]	 = '(' . $this->toString( $rec, "INSERT" ) . ')';
		}
		unset($rec);

		$sql .= implode( ',' , $values );
		if( $this->_DEBUG ){ d( "addRecord() : ". $sql. "<br/>\n", 'sql' ); }

		$return = $this->sql_query( $sql );
// begin �A�t�B���G�C�g�V�X�e��PRO2 ��p ����
/* system_tables �Ȃ�
		if($return){
			$sql = "UPDATE ".$this->prefix."system_tables SET id_count = '".$shadowID."' WHERE table_name = '". $this->tablePlaneName ."'";
			$this->sql_query( $sql );
		}
*/
// end �A�t�B���G�C�g�V�X�e��PRO2 ��p ����
		if( $USE_REGISTER_PROCCESS_LOCK )
			{ SystemUtil::unlockProccess( 'addRecord_' . $this->tableName ); }

		if($this->addLog == true){
			foreach( $recList as $rec )
			{
				$str = "";
				$row = count($this->colName);
				for($i=0; $i<$row; $i++){
					$str .= $rec[ $this->colName[$i] ]. ",";
				}

				$this->log->table_log($this->tableName,'ADD',$str);
			}
		}

		TemplateCache::SetDBUpdateTime();
		$this->cashReset();
		return $recList;
	}

// begin �A�t�B���G�C�g�V�X�e��PRO2 ��p ����
	/**
	 * DB�����e�[�u�����擾���܂��B
	 * @return �e�[�u���f�[�^
	 * @param $type table type(nomal/delete/all)
	 */
	function getTable($type = null){
		$table_name = strtolower($this->tableName);
		switch($type){
			default:
			case 'n':
			case 'nomal':
				$table	 = new Table($table_name );
				break;
			case 'd':
			case 'delete':
				$table	 = new Table($table_name );
				
				$table->delete	 = '( delete_key = '.$this->sqlDataEscape(true,'boolean').' )';
				break;
			case 'a':
			case 'all':
				$table	 = new Table($table_name );
				$table->delete	 = '';
				break;
		}
		return $table;
	}
// end �A�t�B���G�C�g�V�X�e��PRO2 ��p ����

	/**
	 * �e�[�u���̍s�����擾���܂��B
	 * @param $table TableBase �e�[�u���f�[�^
	 * @return int �s��
	 */
	function getRow($table){

		if($this->row >= 0 && $table == $this->row_table ){
			if( $this->_DEBUG ){ d( "getRow() : load cash<br/>\n", 'sql'); }
			return $this->row;
		}
		$sql = $table->getRowString();

		if( $this->_DEBUG ){ d( "getRow() : ". $sql. "<br/>\n", 'sql'); }

		$result	 = $this->sql_query( $sql );

		if( !$result ){
			if( !$this->_DEBUG ){ d( "getRow() : ". $sql. "<br/>\n", 'sql'); }
			throw new InternalErrorException("getRow() : SQL MESSAGE ERROR. \n");
		}

		$rows = $this->sql_fetch_assoc($result,0);
		$row = $rows['cnt'];
		if( $row == -1 ){
			throw new InternalErrorException("getRow() : GET ROW ERROR ( RETURN -1 ).\n");
		}
		$this->row = $row;
		$this->row_table = $table;
		return $row;
	}

	/**
	 * �e�[�u���ɍs�����݂��邩��Ԃ��܂��B
	 * @param TableBase $tbl �e�[�u���f�[�^
	 * @return bool ���݂���ꍇ��true�A0���̏ꍇ��false
	 */
	function existsRow($tbl){
		if($this->row >= 0 && $tbl == $this->row_table ){
			if( $this->_DEBUG ){ d( "existsRow() : load row cash<br/>\n", 'sql'); }
			return $this->row > 0;
		}

		if( ! is_null( $this->rec_cash )){
			if( $this->_DEBUG )				{ d( "existsRow() : ".$this->tableName." load recorde cash<br/>\n", 'sql'); }
			$result = &$this->rec_cash;
		}else{

			$table = $tbl->getClone();

			// union �𗘗p���Ă���ꍇ(type=ALL��)
            $union_count = count($table->union);
            if( $union_count > 0 )
            {
                //EXISTS �̌��ʂ� SELECT �̒��g�͈Ӗ����Ȃ����A* �łȂ��ꍇ�ɍœK������Ȃ��̂Œ萔�ɓ��ꂷ��B
                $table->select = '1';
                for( $i=0; $i<$union_count; $i++ )
                {
                    $table->union[$i]->select = '1';
                }
            }

			$sql = 'SELECT EXISTS('.$table->getString(true).') as exists_row_cnt';

			if( $this->_DEBUG ){ d( "existsRow() : $sql<br/>\n", 'sql' ); }

			$result	 = $this->sql_query( $sql );
		}

		if( !$result ){
			throw new InternalErrorException("existsRow() : SQL MESSAGE ERROR. \n");
		}
		$rec =$this->sql_fetch_assoc($result,0);
		return $rec['exists_row_cnt'];
	}

	/**
	 * ���K�\����p���Č�������
	 * @param Table $tbl
	 * @param String $column
	 * @param String $regex
	 * @return Table
	 */
	function searchRegexp(&$tbl,$column,$regex){
		$table	 = $tbl->getClone();

		if( empty($regex) )
			return $table;

		if($table->join){ $column = $this->tableName.".".$column; }

		$query = "{$column} REGEXP '{$regex}'";

		$table->addWhere($query);
		$this->cashReset();

		return $table;
	}

	/*
	 * �����J�������܂Ƃ߂�like��������
	 */
	private function scCallBack($column)
		{ return $this->tableName.".".$column; }

	function searchConcat(&$tbl,$column,$word){
		$table	 = $tbl->getClone();

		if(is_array($column)){
			if($table->join){ $column = array_map(array($this,"scCallBack"),$column); }
			$column = array_filter($column);
			$column = implode(",' ',", $column);
		}else{
			if($table->join){ $column = $this->tableName.".".$column; }
		}

		$word = $this->sql_escape($word);

		$query = "concat($column) like '%{$word}%'";

		$table->addWhere($query);
		$this->cashReset();

		return $table;
	}


	/**
	 * �w�肵���e�[�u����ID�������_���Ŏ擾����
	 * @param $tbl TableBase �e�[�u���f�[�^
	 * @param $limit int �擾����
	 * @return string id
	 */
	function getRandomID($tbl,$limit = 1){

		$table	 = $tbl->getClone();

		$table->select = "id";
		$table = $this->sortRandom($table);
		$table = $this->limitOffset($table,0,$limit);

		$row = $this->getRow($table);
		$id = array();
		for($i = 0 ; $i < $row ; $i++){
			$rec = $this->getRecord($table,$i);
			$id[] = $this->getData($rec,"id");
		}
		return $id;
	}

	/**
	 * �e�[�u���̌������s���܂��B
	 * ���p�ł��鉉�Z�q�͈ȉ��̂��̂ł��B
	 * >, <	 �s�������Z�q
	 * =	 �������Z�q
	 * !	 �񓙍����Z�q
	 * in    in���Z�q
	 * &     bit���Z�q
	 * b	 �r�g�D�C�[�����Z�q
	 * �r�g�D�C�[�����Z�q�̏ꍇ�̂�$val2���w�肵�܂��B
	 * @param $tbl TableBase �e�[�u���f�[�^
	 * @param $name string �J������
	 * @param $opp string ���Z�q
	 * @param $val string|int|bool �l�P
	 * @param $val2 string|int|bool �l�Q
	 * @return TableBase �e�[�u���f�[�^
	 */
	function searchTable(&$tbl, $name, $opp, $val, $val2 = null){
		if( $tbl->join ){
			//join����Ă��鎞��join�p�̌����֐����g��
			return $this->joinTableSearch( $this, $tbl , $name, $opp, $val, $val2 );
		}

		$table	 = $tbl->getClone();
		//�����p�����[�^��null�̏ꍇ�A���������̒ǉ����s��Ȃ�
		if( is_null($val) )
			return $table;

		if( is_array( $val ) && !count($val) ){ return $table; }

		$table->addWhere( $this->searchTableCore($name, $opp, $val, $val2 ) );

		$this->cashReset();
		return $table;
	}
	/**
	 * ���₢���킹�𗘗p�����e�[�u���̌������s���܂��B
	 * ���p�ł��鉉�Z�q�͈ȉ��̂��̂ł��B
	 * >, <	 �s�������Z�q
	 * =	 �������Z�q
	 * !	 �񓙍����Z�q
	 * in    in���Z�q
	 * &     bit���Z�q
	 * b	 �r�g�D�C�[�����Z�q
	 * �r�g�D�C�[�����Z�q�̏ꍇ�̂�$val2���w�肵�܂��B
	 * @param $tbl TableBase
	 * @param $name string
	 * @param $opp string
	 * @param $subTable TableBase
	 * @return TableBase �e�[�u���f�[�^
	 */
	function searchTableSubQuery(&$tbl, $name, $opp, &$subTable){

		$table	 = $tbl->getClone();

		if($table->join){
			$table->addWhere( $this->tableName.".".$name.' '.$opp.' ('.$subTable->getString(true).' )' );
		}else{
			$table->addWhere( $name.' '.$opp.' ('.$subTable->getString(true).' )' );
		}

		$this->cashReset();
		return $table;
	}

	/**
	 * searchTable�Ŏg�p����\���������s�Ȃ��B
	 * join���table�O��table�Ȃǂ�����ė��p����ׂ̕���
	 *
	 * �����I�ɂ����Ă΂��Ȃ���private
	 * @param $name string �J������
	 * @param $opp string ���Z�q
	 * @param $val string|int|bool �l�P
	 * @param $val2 string|int|bool �l�Q
	 * @param $tbl_name string
	 * @return string
	 */
	private function searchTableCore(&$name, &$opp, &$val, &$val2 = null, &$tbl_name = null ){
		if( $opp == 'in' && !is_array($val) ){
			$val = explode('/',$val);
		}

		if( is_array( $val ) ){
			//array_map�Ɠ����֐��̑�֏���
			$val_buf = Array();
			foreach( $val as $v ){
				$val_buf[] = $this->sqlDataEscape($v,$this->colType[$name]);
			}
			$val = '('.join(',',$val_buf).')';
		}else{
			$val = $this->sqlDataEscape($val,$this->colType[$name]);
		}
		if( !is_null( $tbl_name ) ){
			$name = $tbl_name.'.'.$name;
		}

		if( isset($val2) ){
			if( $opp == 'b' ){
				if( is_string($val2) ){ $val2	 = "'". $val2. "'"; }
				if( $val > $val2 ){ $tmp = $val; $val = $val2; $val2 = $tmp; }
				$str = " ". $name. " BETWEEN ". $val. " AND ". $val2;
			}else{//val2���t���O�����̃`�F�b�N
				$str = " ". $name. " ".$opp." ". $val. " ". $val2." ".$val;
				//sample
				//$name:id
				//$opp:&
				//$val:0x00000001
				//$val2:=
				// ->where : id & 0x00000001 = 0x00000001
			}
		}else{
			if( $opp == '==' || strpos( $val, '%' ) === false ){
				if($opp == '=='){
					$opp = "=";
				}
				if( $opp == '!' ){
					$str  = " ". $name. " <> ". $val;
				}else if($opp == 'isnull'){
					$str  = " ( ". $name. " is null OR ". $name. " = '' ) ";
				}else{
					$str  = " ". $name. " ". $opp ." ". $val;
				}
			}else{
				if( $opp == '!' ){
					$str  = " ". $name. " not like ". $val;
				}else{
					$str  = " ". $name. " like ". $val;
				}
			}
		}
		return $str;
	}
	/**
	 * having�̐ݒ���s�Ȃ�
	 *
	 * �����I�ɂ����Ă΂��Ȃ���private
	 *
	 * @param $name string
	 * @param $opp string
	 * @param $val string|int|bool
	 * @param null $val2 string|int|bool
	 * @return string
     */
	private function addHaving($name, $opp, $val, $val2 = null ){
		if( $opp == 'in' && !is_array($val) ){
			$val = explode('/',$val);
		}

		if( is_array( $val ) ){
			//array_map�Ɠ����֐��̑�֏���
			$val_buf = Array();
			foreach( $val as $v ){
				$val_buf[] = $this->sqlDataEscape($v,$this->colType[$name]);
			}
			$val = '('.join(',',$val_buf).')';
		}else{
			$val = $this->sqlDataEscape($val,$this->colType[$name]);
		}

		if( isset($val2) ){
			if( $opp == 'b' ){
				if( $val > $val2 ){ $tmp = $val; $val = $val2; $val2 = $tmp; }
				if( is_string($val2) ){ $val2	 = "'". $val2. "'"; }
				$str = " ". $name. " BETWEEN ". $val. " AND ". $val2;
			}else{//val2���t���O�����̃`�F�b�N
				$str = " ". $name. " ".$opp." ". $val. " ". $val2." ".$val;
			}
		}else{
			if($opp == '=='){
				$opp = "=";
			}
			if( $opp == '!' ){
				$str  = " ". $name. " <> ". $val;
			}else if($opp == 'isnull'){
				$str  = " ( ". $name. " is null OR ". $name. " = '' ) ";
			}else{
				$str  = " ". $name. " ". $opp ." ". $val;
			}
		}
		return $str;
	}


	/**
	 * ��̃e�[�u����Ԃ��B
	 * search�̌��ʂ���ɂ����肷�鎞�Ɏg�p�B
	 * @return TableBase �e�[�u���f�[�^
	 */
	function getEmptyTable(){
		$table	 = new Table(strtolower($this->tableName) );
		$table->addWhere( "shadow_id = -1" );
		return $table;
	}

	/**
	 * ���R�[�h���\�[�g���܂��B
	 * @param $tbl TableBase �e�[�u���f�[�^
	 * @param $name string �J������
	 * @param $asc string �����E�~���� 'asc' 'desc' �Ŏw�肵�܂��B
	 * @param $add bool sort������ǉ��ɂ��邩�ǂ����̃t���O�B  �f�t�H���g�l��false
	 * @return TableBase �e�[�u���f�[�^
	 */
	function sortTable(&$tbl, $name, $asc, $add = false){
		if( !preg_match( '/^[\w\.]+$/' , $name ) ){
			return $tbl;
		}

		$table	 = $tbl->getClone();

		if(is_null($add) || ! $add || !$table->order ){
			$table->order = Array();
		}

		if( $table->join && FALSE !== array_search( $name , $this->colName ) ){
			$name = $table->baseName . '.' . $name;
		}

		if( strtolower($asc) == 'asc' )
			$table->order[ $name ] = 'ASC';
		else
			$table->order[ $name ] = 'DESC';

		return $table;
	}

	function joinTableSort(&$db, &$tbl, $name, $asc, $add = false){
		$table	 = $tbl->getClone();

		if(is_null($add) || ! $add || !$table->order ){
			$table->order = Array();
		}

		if( $table->join && FALSE !== array_search( $name , $db->colName ) ){
			$name = $db->tablePlaneName . '.' . $name;
		}

		if( strtolower($asc) == 'asc' )
			$table->order[ $name ] = 'ASC';
		else
			$table->order[ $name ] = 'DESC';

		return $table;
	}

	/**
	 * ���R�[�h�������_���Ƀ\�[�g���܂��B
	 * �S�������ɂȂ�̂Ő������̃e�[�u���ɃZ�b�g����ƕ��ׂ��傫���ł��B
	 * limitOffset���g���Ă��S�������ɂȂ�܂��B
	 * @param $tbl TableBase �e�[�u���f�[�^
	 * @return TableBase �e�[�u���f�[�^
	 */
	function sortRandom(&$tbl){
		$table	 = $tbl->getClone();
		$table->order = Array('RAND()'=>'');
		return $table;
	}

	function sortField( &$tbl , $column , $fields )
	{
		$values = Array( $column );
		$type   = $this->colType[ $column ];

		foreach( $fields as $field )
			{ $values[] = $this->sqlDataEscape( $field , $type ); }

		$table	                 = $tbl->getClone();
		$table->order[ 'FIELD' ] = '( ' . implode( ' , ' , $values ) . ' )';
		return $table;
	}
	/**
	 * �\�[�g�������Z�b�g���܂��B
	 * @param $tbl TableBase �e�[�u���f�[�^
	 * @return TableBase �e�[�u���f�[�^
	 */
	function sortReset(&$tbl){
		$table	 = $tbl->getClone();
		$table->order = Array();
		return $table;
	}
	/**
	 * �\�[�g���s�Ȃ�Ȃ��B
	 * @param $tbl TableBase �e�[�u���f�[�^
	 * @return TableBase �e�[�u���f�[�^
	 */
	function sortDelete(&$tbl){
		$table	 = $tbl->getClone();
		$table->order = false;
		return $table;
	}

	/**
	 * �e�[�u���̘_���a�B
	 * @param $tbl TableBase �e�[�u���f�[�^
	 * @param $table2 TableBase �e�[�u���f�[�^
	 * @return TableBase �e�[�u���f�[�^
	 */
	function orTable(&$tbl, &$table2){
		$table1	 = $tbl->getClone();
		if( $table1->where == null ){
			if( $table2->where == null ){
				// �ǂ���̃e�[�u�����i�荞�ݏ����������ꍇ
				return $this->getTable();
			}else{
				// table1 �ɍi�荞�ݏ����������ꍇ
				return $table2;
			}
		}else{
			if( $table2->where == null ){
				// table2 �ɍi�荞�ݏ����������ꍇ
				return $table1;
			}else{
				$table1->addWhere( $table2->where , 'or' );
				$this->cashReset();
			}
			return $table1;
		}
	}
	/**
	 * �e�[�u���̘_���a�B(�z��Ή���
	 * func_get_args�ł͎Q�Ƃ��󂯂�Ȃ��הz��ɂ�
	 * @param $a array �e�[�u���f�[�^�̓������z��
	 * @return TableBase �e�[�u���f�[�^
	 */
	function orTableM($a){
		$list = array();
		for ($i = 0; $i < count($a); $i++) {
			if( $a[$i]->where != null ){
				$list[] = $i;
			}
		}
		switch( count($list) ){
			case 0:
				return $this->getTable();
			case 1:
				return $a[$list[0]];
			default:
				$table	 = $a[$list[0]]->getClone();
				for($i=1;$i<count($list);$i++){
					$table->addWhere( $a[$list[$i]]->where , 'or' );
				}
				return $table;
		}
	}

	/**
	 * �e�[�u���̘_���ρB
	 * @param $tbl TableBase �e�[�u���f�[�^
	 * @param $table2 TableBase �e�[�u���f�[�^
	 * @return TableBase �e�[�u���f�[�^
	 */
	function andTable(&$tbl, &$table2){
		$table1	 = $tbl->getClone();
		if( $table1->where == null ){
			if( $table2->where == null ){
				// �ǂ���̃e�[�u�����i�荞�ݏ����������ꍇ
				return $this->getTable();
			}else{
				// table1 �ɍi�荞�ݏ����������ꍇ
				return $table2;
			}
		}else{
			if( $table2->where == null ){
				// table2 �ɍi�荞�ݏ����������ꍇ
				return $table1;
			}else{
				$table1->addWhere( $table2->where , 'and' );
				$this->cashReset();
			}
			return $table1;
		}
	}

	/**
	 * ���j�I���e�[�u�����쐬���܂��B
	 * �\�[�g��������rTable�̂��̂��g�p�B
	 * @param $lTable TableBase �e�[�u���f�[�^
	 * @param $rTable TableBase �e�[�u���f�[�^
	 * @param $column string �J������
	 * @return TableBase �e�[�u���f�[�^
	 */
	function unionTable(&$lTable, &$rTable, $column = null)
	{
		$table = $rTable->getClone();
		$tmpTable = $lTable->getClone();
		if( !is_null( $column ) )
		{
			$table->select = $column;
			$tmpTable->select = $column;
		}
		$table->setUnion( $tmpTable );
		if( count($tmpTable->union))
		{
			foreach( $tmpTable->union as $unionTable )
			{
				$table->setUnion( $unionTable );
			}

		}
		return $table;
	}

	/**
	 * �T�u�N�G���ɂ��O������(����������sql���œn��)
	 *
	 * @param $method string
	 * @param $tbl TableBase
	 * @param $sub_tbl TableBase
	 * @param $n_name string
	 * @param $join_sql string
	 * @return TableBase �e�[�u���f�[�^
	 */

	function outerJoinTableSubQuerySQL( $method, &$tbl, &$sub_tbl, $n_name, $join_sql ){
		$table	 = $tbl->getClone();

		$b_name = strtolower($this->tableName);

		switch($method){
			case "left";
			case "right";
			$join = $method." join";	break;
			default; $join = "join";
		}

		if( $table->outer ){
			$table->outer .= " {$join} (".$sub_tbl->getString(true).") {$n_name} on {$join_sql}";
		}else{
			$table->select	= $b_name.".*";
			$table->outer	= " {$join} (".$sub_tbl->getString(true).") {$n_name} on {$join_sql}";

			$table->changeJoinTable( $b_name );
		}

		$table->join = true;
		$table->outer_f = true;

		return $table;
	}

	/**
	 * �T�u�N�G���ɂ��O������
	 *
	 * @param $method string
	 * @param $tbl TableBase
	 * @param $sub_tbl TableBase
	 * @param $n_name string
	 * @param $b_col string
	 * @param $n_col string
	 * @return TableBase �e�[�u���f�[�^
	 */
	function outerJoinTableSubQuery( $method, &$tbl, &$sub_tbl, $n_name, $b_col, $n_col ){
		$table	 = $tbl->getClone();

		$b_name = strtolower($this->tableName);

		switch($method){
			case "left";
			case "right";
			$join = $method." join";	break;
			default; $join = "join";
		}

		if( $table->outer ){
			$table->outer .=  " {$join} (".$sub_tbl->getString(true).") {$n_name} on {$b_name}.{$b_col} = {$n_name}.{$n_col}";
		}else{
			$table->select	= $b_name.".*";
			$table->outer	= " {$join} (".$sub_tbl->getString(true).") {$n_name} on {$b_name}.{$b_col} = {$n_name}.{$n_col}";

			$table->changeJoinTable( $b_name );
		}

		$table->join = true;
		$table->outer_f = true;

		return $table;
	}

	/**
	 * �e�[�u���̊O������(����������sql���œn��
	 * @param $tbl TableBase �e�[�u���f�[�^
	 * @param $n_name string �e�[�u����
	 * @param $query string ����������sql��
	 * @return TableBase �e�[�u���f�[�^
	 */
	function outerJoinSQL( $method, &$tbl, $n_name, $query){
		$table	 = $tbl->getClone();
		$b_name = strtolower($this->tableName);

		switch($method){
			case "left";
			case "right";
			$join = $method." join";	break;
			default; $join = "join";
		}

		if($table->outer_f){
			$table->outer = $table->outer." {$join} {$n_name} on {$query}";
		}else{
			$table->select	= $this->tableName.".*";
			$table->outer = "{$join} {$n_name} on {$query}";

			$table->changeJoinTable( $b_name );
		}

		$table->outer_f = true;
		$table->join = true;

		return $table;
	}

	/**
	 * �e�[�u���̊O������
	 * @param $method string �������@
	 * @param $tbl Table �e�[�u���f�[�^
	 * @param $b_name string �e�[�u����
	 * @param $n_name string �e�[�u����
	 * @param $b_col string �J������
	 * @param $n_col string �J������
	 * @param $n_tbl_name string �����Ɏg���e�[�u����
	 * @return Table �e�[�u���f�[�^
	 */
	function outerJoin( $method, &$tbl, $b_name, $n_name, $b_col, $n_col, $n_tbl_name = null){
		$table	 = $tbl->getClone();

		$b_name = strtolower($this->prefix.$b_name);
		$n_name = strtolower($this->prefix.$n_name);

		if( !is_null($n_tbl_name) )	{ $_n_name = $n_name.' '.$n_tbl_name; $_n_tbl_name = $n_tbl_name; }
		else						{ $_n_tbl_name = $n_name; }

		switch($method){
			case "left";
			case "right";
			$join = $method." join";	break;
			default; $join = "join";
		}

		if($table->outer_f){
			$table->outer = $table->outer." {$join} {$n_name} on {$b_name}.{$b_col} = {$n_name}.{$n_col}";
		}else{
			$table->select	= $this->tableName.".*";
			$table->outer = "{$join} {$n_name} on {$b_name}.{$b_col} = {$n_name}.{$n_col}";

			$table->changeJoinTable( $b_name );
		}

		$table->outer_f = true;
		$table->join = true;

		return $table;
	}

	/**
	 * �e�[�u���̌���
	 * @param $tbl TableBase �e�[�u���f�[�^
	 * @param $b_name string �e�[�u����
	 * @param $n_name string �e�[�u����
	 * @param $b_col string �J������
	 * @param $n_col string �J������
	 * @return TableBase �e�[�u���f�[�^
	 */
	function joinTable( &$tbl, $b_name, $n_name, $b_col, $n_col, $n_tbl_name = null ){
		$_b_name = strtolower($this->prefix.$b_name);
		$_n_name = strtolower($this->prefix.$n_name);
		if( !is_null($n_tbl_name) )	{ $_n_name = $_n_name.' '.$n_tbl_name; $_n_tbl_name = $n_tbl_name; }
		else						{ $_n_tbl_name = $_n_name; }

		return $this->joinTableSQL( $tbl, $b_name, $n_name, $_b_name.".".$b_col." = ".$_n_tbl_name.".".$n_col." ", $n_tbl_name );
	}
	/**
	 * �e�[�u����Like����
	 * @param $tbl TableBase �e�[�u���f�[�^
	 * @param $b_name string �e�[�u����
	 * @param $n_name string �e�[�u����
	 * @param $b_col string �J������
	 * @param $n_col string �J������
	 * @param $n_tbl_name string �e�[�u����
	 * @return TableBase �e�[�u���f�[�^
	 */
	function joinTableLike( &$tbl, $b_name, $n_name, $b_col, $n_col, $n_tbl_name = null ){
		$_b_name = strtolower($this->prefix.$b_name);
		$_n_name = strtolower($this->prefix.$n_name);
		if( !is_null($n_tbl_name) )	{ $_n_name = $_n_name.' '.$n_tbl_name; $_n_tbl_name = $n_tbl_name; }
		else						{ $_n_tbl_name = $_n_name; }

		return $this->joinTableSQL( $tbl, $b_name, $n_name, $_b_name.".".$b_col." like '%' || ".$_n_tbl_name.".".$n_col." || '%' ", $n_tbl_name );
	}
// begin �A�t�B���G�C�g�V�X�e��PRO2 ��p ����
	/**
	 * �e�[�u���̌���(����������sql���œn��
	 * @param $tbl �e�[�u���f�[�^
	 * @param $b_name �e�[�u����
	 * @param $n_name �e�[�u����
	 * @param $join_sql ����������sql��
	 * @param $n_tbl_name �e�[�u����as�ŕt�^���Ă��閼�O
	 * @return Table �e�[�u���f�[�^
	 */
	function joinTableSQL( &$tbl, $b_name, $n_name, $join_sql, $n_tbl_name = null ){
		$table	 = $tbl->getClone();

		$b_name = strtolower($this->prefix.$b_name);
		$n_name = strtolower($this->prefix.$n_name);
		
		if( !is_null($n_tbl_name) )	{ $n_name = $n_name.' '.$n_tbl_name; }
		else						{ $n_tbl_name = $n_name; }

		if( $table->join ){
			$table->from .= ", ".$n_name;
			$table->delete	 .= ' AND ( '.$n_tbl_name.'.delete_key = '.$this->sqlDataEscape(false,'boolean').' OR '.$n_tbl_name.'.delete_key IS NULL )';
		}else{
			$table->select	= $b_name.".*";
			$table->from	= $b_name. ", ".$n_name;
			$table->delete	 = '( '.$b_name.'.delete_key = '.$this->sqlDataEscape(false,'boolean').' OR '.$b_name.'.delete_key IS NULL ) AND ( '.$n_tbl_name.'.delete_key = '.$this->sqlDataEscape(false,'boolean').' OR '.$n_tbl_name.'.delete_key IS NULL )';
			
			$table->changeJoinTable( $b_name );
		}

		$table->addWhere( '('. $join_sql .')' );
		
		$table->join = true;

		return $table;
	}
// end �A�t�B���G�C�g�V�X�e��PRO2 ��p ����
	/**
	 *
	 * @param $tbl TableBase
	 * @param $sub_tbl TableBase
	 * @param $n_name string
	 * @param $b_col string
	 * @param $n_col string
	 * @return TableBase �e�[�u���f�[�^
	 */
	function joinTableSubQuery( &$tbl, &$sub_tbl, $n_name, $b_col, $n_col ){
		$table	 = $tbl->getClone();

		$b_name = strtolower($this->tableName);

		if( $table->join ){
			$table->from .= ", ".'('.$sub_tbl->getString(true).') '.$n_name;
		}else{
			$table->select	= $b_name.".*";
			$table->from	= $b_name. ", ".'('.$sub_tbl->getString(true).') '.$n_name;

			$table->changeJoinTable( $b_name );
		}

		$table->addWhere( $b_name.".".$b_col." = ".$n_name.".".$n_col." " );

		$table->join = true;

		return $table;
	}

	/**
	 * @param $tbl TableBase
	 * @param $sub_tbl TableBase
	 * @param $n_name string
	 * @param $join_sql string
	 * @return TableBase �e�[�u���f�[�^
	 */
	function joinTableSubQuerySQL( &$tbl, &$sub_tbl, $n_name, $join_sql ){
		$table	 = $tbl->getClone();

		$b_name = strtolower($this->tableName);

		if( $table->join ){
			$table->from .= ", ".'('.$sub_tbl->getString(true).') '.$n_name;
		}else{
			$table->select	= $b_name.".*";
			$table->from	= $b_name. ", ".'('.$sub_tbl->getString(true).') '.$n_name;

			$table->changeJoinTable( $b_name );
		}

		$table->addWhere( $join_sql );

		$table->join = true;

		return $table;
	}
	/**
	 * @param $db SQLDatabaseBase
	 * @param $tbl TableBase
	 * @param $name string
	 * @param $opp string
	 * @param $val string|int|bool
	 * @param $val2 string|int|bool
	 * @param $tbl_name string
	 * @return TableBase �e�[�u���f�[�^
	 */
	function joinTableSearch( &$db ,&$tbl ,$name, $opp, $val, $val2 = null, $tbl_name = null ){

		$table	 = $tbl->getClone();

		//�����p�����[�^��null�̏ꍇ�A���������̒ǉ����s��Ȃ�
		if( is_null($val) ){ return $table; }
		if( is_array( $val ) && !count($val) ){ return $table; }

		if( is_null($tbl_name) )
			$tbl_name = $db->tableName;

		$sql = $db->searchTableCore($name, $opp, $val, $val2, $tbl_name );

		$table->addWhere( $sql );

		$this->cashReset();
		return $table;
	}

	/**
	 * �e�[�u���� $start �s�ڂ��� $num ���o���B
	 * @param $table TableBase �e�[�u���f�[�^
	 * @param $start int �I�t�Z�b�g
	 * @param $num int ��
	 * @return TableBase �e�[�u���f�[�^
	 */
	function limitOffset( $table, $start, $num ){
		$ttable			 = $table->getClone();
		$ttable->offset	 = $start;
		$ttable->limit	 = $num;

		$this->cashReset();
		return $ttable;
	}

	/**
	 * �Ö�ID�̍ő�l��Ԃ�
	 * @return Integer max
	 */
	function getMaxID(){

		if( $this->_DEBUG ){ d( "getMaxID() : ". "select max(shadow_id) as max from ". $this->tableName. "<br/>\n", 'sql'); }

// begin �A�t�B���G�C�g�V�X�e��PRO2 ��p ����
/* system_tables �Ȃ�
		$sql = "select id_count as max from ".$this->prefix."system_tables WHERE table_name = '". $this->tablePlaneName."'";
*/
// end �A�t�B���G�C�g�V�X�e��PRO2 ��p ����

		$sql = "select max(shadow_id) as max from ". strtolower($this->tableName);
		$result	 = $this->sql_query( $sql );

		if( !$result ){
			if( !$this->_DEBUG ){ d( "getMaxID() : ".$sql. "<br/>\n", 'sql'); }
			throw new InternalErrorException("getMaxID() : SQL MESSAGE ERROR. \n");
		}

		$data = $this->sql_fetch_array($result);

		return $data['max'];
	}

// begin �A�t�B���G�C�g�V�X�e��PRO2 ��p ����
	function getTimeID(){
		
		$time = date("YmdHis");
		
		$sql = "select max(id) as max from ". strtolower($this->tableName). " where id like '$time%'";
	
		if( $this->_DEBUG ){ d( "getTimeID() : ". "$sql <br/>\n", 'sql'); }
		
		$result	 = $this->sql_query( $sql );
		
		if( !$result ){
			if( !$this->_DEBUG ){ d( "getMaxID() : $sql <br/>\n", 'sql'); }
			throw new RuntimeException("getMaxID() : SQL MESSAGE ERROR. \n");
		}
		
		$data = $this->sql_fetch_array($result);
		
		$max_id = $data['max'];
		
		if( is_null( $max_id ) ){
			return $time."01";
		}
		
		$num = (int)substr( $data['max'],-2);
		$num++;
		if( $num >= 99 )
		{
			sleep(1);
			return $this->getTimeID();
		}
		
		return $time.sprintf("%02d",$num);
	}
// end �A�t�B���G�C�g�V�X�e��PRO2 ��p ����

	/**
	 * record����o�^�p��SQL�����擾���܂��B
	 * @return String SQL��
	 */
	function toString($rec, $mode){
		$row = count( $this->colName );
		$sql = '';

		for($i=0; $i<$row; $i++){
			$name = $this->colName[$i];

			if( 'fake' == $this->colType[ $name ] )
			{
				if( $row == $i + 1 )
					{ $sql = substr( $sql , 0 , -3 ); }

				continue;
			}

			if( $mode == "UPDATE" )
			$sql	 .= $name. " = ";

			// �J�����̌^���擾
			$type	 = $this->colType[ $name ];

			// �J�����̒l���擾
			$data	 = $this->getData( $rec, $name );

			//sql�Ƃ��ė��p�\�ȃf�[�^�ɕό`
			if( is_array( $data ) ){
				// �J�����̒l���z��̏ꍇ�A�z��f�[�^�� / �ŋ�؂��Ċi�[
				$sql .= $this->sqlDataEscape( join( $data,'/') , $type  );
			}else{
				// �J�����̒l�����l�̏ꍇ
				$sql .= $this->sqlDataEscape( $data , $type );
			}

			if( $i != count( $this->colName ) - 1 ){
				$sql	 .= ", \n";
			}
		}

		return $sql;
	}

	/**
	 * �W��֐��̂܂Ƃߗp
	 *
	 * @param $agg string
	 * @param $name string
	 * @param $tbl TableBase
	 * @return int
	 */
	private function getAggregate( $agg, $name, $tbl = null )
	{

		if( is_null($tbl) ){
			$table = $this->getTable();
		}else{
			$table = $tbl->getClone();
		}

		if( $table->join && FALSE !== array_search( $name , $this->colName ) ){
			$name = $table->baseName . '.' . $name;
		}

		$table->select = "$agg($name) as $agg";

		$sql = $table->toSelectFrom().$table->getWhere();

		if( $this->_DEBUG ){ d( "getAggregate() : ". $sql . "<br/>\n", 'sql' ); }


		$result	 = $this->sql_query( $sql );

		if( !$result ){
			if( !$this->_DEBUG ){ d( "getAggregate() : ". $sql. "<br/>\n", 'sql'); }
			throw new InternalErrorException("getAggregate() : SQL MESSAGE ERROR. \n");
		}

		$data = $this->sql_fetch_array($result);
		return (int)$data[$agg];
	}

	/**
	 * ���݂̃e�[�u������w�肵��column�̑����v���擾���܂��B
	 * @param $name string
	 * @param $tbl TableBase
	 * @return int
	 */
	function getSum( $name, $tbl = null){
		return $this->getAggregate('sum',$name,$tbl);
	}

	/**
	 * ���݂̃e�[�u������w�肵��column�̍ő�l���擾���܂��B
	 * @param $name string
	 * @param $tbl TableBase
	 * @return int
	 */
	function getMax( $name, $tbl = null){
		return $this->getAggregate('max',$name,$tbl);
	}

	/**
	 * ���݂̃e�[�u������w�肵��column�̍ŏ��l���擾���܂��B
	 * @param $name string
	 * @param $tbl TableBase
	 * @return int
	 */
	function getMin( $name, $tbl = null){
		return $this->getAggregate('min',$name,$tbl);
	}

	/**
	 * ���݂̃e�[�u������w�肵��column�̕��ϒl���擾���܂��B
	 * @param $name string
	 * @param $tbl TableBase
	 * @return int
	 */
	function getAvg( $name, $tbl = null){
		return $this->getAggregate('avg',$name,$tbl);
	}

	/**
	 * ���݂̃e�[�u����SQL���w�肵��column��groupby���A�w��J�������W�v�������ʂ�t�^�����e�[�u����Ԃ��B
	 * ���ۂ̎擾��getRecored�AgetData���g��
	 * @return TableBase �e�[�u���f�[�^
	 */
	private function getAggregateTable( $agg, $agg_name, $group_name, $table = null, $opp = null, $val = null){

		if( $this->_DEBUG ){ d( "getAggregateTable();\n", 'sql' ); }

		if( is_null($table) ){
			$table = $this->getTable();
		}else{
			$table = $table->getClone();
		}

		if( $table->join ){
			$table->select = $this->tableName.".$group_name as $group_name, $agg(".$this->tableName.".$agg_name) as $agg";
		}else {
			$table->select = str_replace('*', "$group_name , $agg($agg_name) as $agg", $table->select);
		}

		if( is_null($table->group) ){
			$table->group = $group_name;
		}else{
			$table->group = $table->group.','.$group_name;
		}

		if( $opp != null ){
			$str = $this->addHaving( $agg, $opp, $val );
			if( $table->having != null){
				$table->having .= 'AND ';
			}else{ $table->having = ''; }

			$table->having .= $str;
		}

		return $table;
	}

	/**
	 * ���݂̃e�[�u����SQL���w�肵��column��groupby���A�w��J������sum�������ʂ�t�^�����e�[�u����Ԃ��B
	 * ���ۂ̎擾��getRecored�AgetData���g��
	 * @return TableBase �e�[�u���f�[�^
	 */
	function getSumTable( $col_name, $group_name, $table = null, $opp = null, $val = null){
		return $this->getAggregateTable('sum',$col_name, $group_name, $table , $opp,  $val );
	}

	/**
	 * ���݂̃e�[�u����SQL���w�肵��column��groupby���A�w��J������max�������ʂ�t�^�����e�[�u����Ԃ��B
	 * ���ۂ̎擾��getRecored�AgetData���g��
	 * @return TableBase �e�[�u���f�[�^
	 */
	function getMaxTable( $col_name, $group_name, $table = null, $opp = null, $val = null){
		return $this->getAggregateTable('max',$col_name, $group_name, $table , $opp,  $val );
	}

	/**
	 * ���݂̃e�[�u����SQL���w�肵��column��groupby���A�w��J������min�������ʂ�t�^�����e�[�u����Ԃ��B
	 * ���ۂ̎擾��getRecored�AgetData���g��
	 * @return TableBase �e�[�u���f�[�^
	 */
	function getMinTable( $col_name, $group_name, $table = null, $opp = null, $val = null){
		return $this->getAggregateTable('min',$col_name, $group_name, $table , $opp,  $val );
	}

	/**
	 * ���݂̃e�[�u����SQL���w�肵��column��groupby���A�w��J������avg�������ʂ�t�^�����e�[�u����Ԃ��B
	 * ���ۂ̎擾��getRecored�AgetData���g��
	 * @return TableBase �e�[�u���f�[�^
	 */
	function getAvgTable( $col_name, $group_name, $table = null, $opp = null, $val = null){
		return $this->getAggregateTable('avg',$col_name, $group_name, $table , $opp,  $val );
	}

	function getAvgTableEx( $avg_name, $group_name, $table = null, $opp = null, $val = null){

		if( $this->_DEBUG ){ d( "getAvgTable();\n", 'sql' ); }

		if( is_null($table) ){
			$table = $this->getTable();
		}else{
			$table = $table->getClone();
		}

		//$table->select	 = str_replace( '*' , "$group_name , avg($avg_name) as avg" , $table->select );
		$table->select = "{$table->select} , avg({$avg_name}) as avg";

		if( is_null($table->group) ){
			$table->group = $group_name;
		}else{
			$table->group = $table->group.','.$group_name;
		}

		if( is_null($opp) ){
			$str = $this->addHaving( 'avg', $opp, $val );
			if( $table->having != null){
				$table->having .= 'AND ';
			}else{ $table->having = ''; }

			$table->having .= $str;
		}

		return $table;
	}
	/**
	 * ���݂̃e�[�u����SQL���w�肵��column��groupby���A�w��J������cnt�������ʂ�t�^�����e�[�u����Ԃ��B
	 * ���ۂ̎擾��getRecored�AgetData���g��
	 * @return TableBase �e�[�u���f�[�^
	 */
	function getCountTable( $name, $tbl = null, $returnColumnAll = false, $opp = null, $val = null){
		$name	 = strtolower( $name );

		if( $this->_DEBUG ){ d( "getCountTable();\n", 'sql' ); }

		if( is_null($tbl) ){
			$table = $this->getTable();
		}else{
			$table = $tbl->getClone();
		}

		if($returnColumnAll){
			$table->select = $table->select." , count({$name}) as cnt";
		}else{
			$table->select = "$name , count(*) as cnt";
		}

		if( is_null($table->group) ){
			$table->group = $name;
		}else{
			$table->group = $table->group.','.$name;
		}

		$table = $this->sortTable( $table, 'cnt', 'asc' );

		if( $opp != null ){
			$str = $this->addHaving( 'cnt', $opp, $val );
			if( $table->having != null){
				$table->having .= 'AND ';
			}else{ $table->having = ''; }

			$table->having .= $str;
		}

		$this->cashReset();
		return $table;
	}

	/**
	 * �I���J������ǉ��B  geCountTable�ȂǂŃf�[�^�̗~�����J�������\������Ȃ����ɗL��
	 * @return TableBase �e�[�u���f�[�^
	 */
	function addSelectColumn( &$tbl, $name, $group = true, $table_name = null ){
		$table	 = $tbl->getClone();
		
		if( !is_null( $table_name ))
		{
			$name = $this->prefix. strtolower($table_name).'.'.$name;
		}

		$table->select .= ','.$name;

		if(strlen($table->group) && $group){
			$table->group .= ','.$name;
		}

		return $table;
	}

	/**
	 * group by�J������ǉ��B
	 * @return TableBase �e�[�u���f�[�^
	 */
	function addGroupColumn( &$tbl, $name, $table_name = null ){
		$table	 = $tbl->getClone();
		
		if( !is_null( $table_name ))
		{
			$name = $this->prefix. strtolower($table_name).'.'.$name;
		}

		if( '*' == $table->select )
			{ $table->select .= ',max('.$name . ')'; }

		if(strlen($table->group)){
			$table->group .= ','.$name;
		}else{
			$table->group = $name;
		}

		return $table;
	}

	/**
	 * �w��J�����̂݌��ʂ��d�����폜���ĕԂ�
	 * @see include/base/DatabaseBase#getDistinctColumn($name, $tbl)
	 * @return TableBase �e�[�u���f�[�^
	 */
	function getDistinctColumn( $name , &$tbl){

		$table	 = $tbl->getClone();

		if( is_null($table) ){
			$table = $this->getTable();
		}

		$table->select	 = "DISTINCT " . $table->select;
		$table->select	 = str_replace( '*' , "$name " , $table->select );
		$table = $this->sortTable($table,$name,'asc');

		return $table;
	}

	/**
	 * �d�����폜���ĕԂ�
	 * @param $tbl TableBase
	 * @return TableBase
	 */
	function getDistinct( &$tbl){

		$table	 = $tbl->getClone();

		if( is_null($table) )
			$table = $this->getTable();

		$table->select	 = "DISTINCT " . $table->select;

		return $table;
	}

	/**
	 * �w��J�����̂ݕԂ�
	 * @see include/base/DatabaseBase#getColumn($name, $tbl)
	 * @return TableBase �e�[�u���f�[�^
	 */
	function getColumn( $name , &$tbl){

		$table	 = $tbl->getClone();

		if( is_null($table) )
		$table = $this->getTable();

		$table->select	 = str_replace( '*' , "$name " , $table->select );
		$table = $this->sortTable($table,$name,'asc');

		return $table;
	}

	/**
	 * ���Ԃ�goroup������
	 * @param $tbl TableBase �e�[�u���f�[�^
	 * @param $column string �Ώۂ̃J����
	 * @param $format time  string �Z�߂�t�H�[�}�b�g
	 * @return TableBase �e�[�u��
	 */
	function dateGroup(&$tbl,$column,$format){
		$table	 = $tbl->getClone();
		$table->select = $this->sql_date_group($column,$format) . ' as date_group';
		$table->group = 'date_group';
		return $this->sortTable($table,'date_group','asc');
	}

	function getClumnNameList(){
		return array_slice($this->colName,2);
	}

	/**
	 * row��Record��cash���폜���܂��B
	 */
	function cashReset(){
		if( $this->_DEBUG ){ d( "cashReset() : reset <br/>\n", 'sql' ); }
		$this->row = -1;
		$this->rec_cash = null;
	}

	/**
	 * type��password�ȃJ�����𕜍�����֐���sql�ɒǉ�����
	 * @param $tbl TableBase �e�[�u���f�[�^
	 * @return TableBase �K�p�ς̃e�[�u��
	 */
	function addPasswordDecrypt( &$tbl ){
		global $CONFIG_SQL_PASSWORD_KEY;

		$pass_keys = array_keys( $this->colType, 'password'  );
		if( !count($pass_keys) ){ return $tbl; }

		$table	 = $tbl->getClone();

		foreach( $pass_keys as $key ){
			$table->select .= ','.$this->sql_to_decrypt( $key, $CONFIG_SQL_PASSWORD_KEY ) . ' as ' . $this->pass_prefix . $key;
			if(strlen($table->group)){
				$table->group .= ','.$this->pass_prefix . $key;
			}
		}

		$union_count = count($table->union);
		if( $union_count > 0 )
		{
			for( $i=0; $i<$union_count; $i++ )
			{
				$table->union[$i] = $this->addPasswordDecrypt( $table->union[$i] );
			}
		}

		return $table;
	}

	/**
	 * type��password�ȃJ�����𕜍�����֐���sql�ɒǉ�����
	 * @param $rec array �ϊ����s�Ȃ����R�[�h
	 * @return array �K�p�ς̃��R�[�h
	 */
	function replacePasswordDecrypt( &$rec ){
		$pass_keys = array_keys( $this->colType, 'password'  );
		if( !count($pass_keys) ){ return $rec; }

		foreach( $pass_keys as $key ){
			$rec[ $key ] = $rec[ $this->pass_prefix . $key ];
			unset($rec[ $this->pass_prefix . $key ]);
		}
		return $rec;
	}


	function sql_query($sql, $update = false){
		throw new InternalErrorException('SQLDatabase::'.__FUNCTION__.'  Unmounting');
	}

	function sql_fetch_assoc(&$result,$index){
		throw new InternalErrorException('SQLDatabase::'.__FUNCTION__.'  Unmounting');
	}

	function sql_fetch_array(&$result){
		throw new InternalErrorException('SQLDatabase::'.__FUNCTION__.'  Unmounting');
	}

	function sql_fetch_all(&$result){
		throw new InternalErrorException('SQLDatabase::'.__FUNCTION__.'  Unmsounting');
	}

	function sql_num_rows(&$result){
		throw new InternalErrorException('SQLDatabase::'.__FUNCTION__.'  Unmounting');
	}

	function sql_convert( $val ){
		throw new InternalErrorException('SQLDatabase::'.__FUNCTION__.'  Unmounting');
	}

	function sql_escape( $val ){
		throw new InternalErrorException('SQLDatabase::'.__FUNCTION__.'  Unmounting');
	}

	function sql_date_group($column,$format){
		throw new InternalErrorException('SQLDatabase::'.__FUNCTION__.'  Unmounting');
	}

	function sql_to_encrypt( $str, $key ){
		throw new InternalErrorException('SQLDatabase::'.__FUNCTION__.'  Unmounting');
	}

	function sql_to_decrypt( $str, $key ){
		throw new InternalErrorException('SQLDatabase::'.__FUNCTION__.'  Unmounting');
	}

	function to_boolean( $val ){
		if( is_bool( $val ) )		{ return $val; }
		else						{ return SystemUtil::convertBool($val);}
	}

	function begin(){
		exit('SQLDatabase::'.__FUNCTION__.'  Unmounting');
	}

	function commit(){
		exit('SQLDatabase::'.__FUNCTION__.'  Unmounting');
	}

	function rollback(){
		exit('SQLDatabase::'.__FUNCTION__.'  Unmounting');
	}


	function sqlDataEscape($val,$type,$quots = true){
	global $CONFIG_SQL_PASSWORD_KEY;
		switch ($type) {
			case 'password':
			case 'string':
			case 'varchar' :
			case 'date' :
			case 'char' :
			case 'image':
			case 'file':
			case 'text':
			case 'blob':
				// �J�����̌^��������̏ꍇ

				$sqlstr = $this->sql_escape( $val );

				/*if( preg_match( '/\\\\$/' , $sqlstr ) )
					$sqlstr .= ' ';
				*/
				if($quots){ $sqlstr = "'".$sqlstr."'"; }

				if( 'password' == $type ){
					$sqlstr = $this->sql_to_encrypt( $sqlstr, $CONFIG_SQL_PASSWORD_KEY );
				}
				break;
			case 'double':
				// �J�����̌^�������̏ꍇ
				$sqlstr = doubleval($val);
				break;
			case 'boolean':
				if( SystemUtil::convertBool($val) ) { $sqlstr = 'TRUE'; }
				else								{ $sqlstr = 'FALSE'; }
				break;
			default:
			case 'int':
			case 'timestamp':
				// �J�����̌^�������̏ꍇ
				$sqlstr = intval($val);
				break;
		}
		return $sqlstr;
	}

	//debug�t���O����p
	function onDebug(){ $this->_DEBUG = true; }
	function offDebug(){ $this->_DEBUG = false; }

	private function getColumnType($name){
		throw new InternalErrorException('SQLDatabase::'.__FUNCTION__.'  Unmounting');
	}

	private function getModelRecord($rec){
		global $THIS_TABLE_MODEL_CLASS;
		global $model_path;

		if( $THIS_TABLE_MODEL_CLASS[ $this->tableName ] ){
			if( $this->_DEBUG )				{ d( "getModelRecord() : ".$this->tableName." Model Record.<br/>\n", 'sql'); }
			$class_name = $THIS_TABLE_MODEL_CLASS[ $this->tableName ].'Model';
			include_once $model_path.$class_name.'.php';
			$rec = new $class_name($rec);
			/*
			if( !class_exists( $class_name ) ){
				if( file_exists( $model_path.$class_name.'.php') )
				{
					include_once $model_path.$class_name.'.php';
					if ( class_exists( $class_name ) )
					{
						$rec = new $class_name($rec);
					}
				}
			}*/
		}
		return $rec;
	}
}

/*******************************************************************************************************/
/*******************************************************************************************************/
/*******************************************************************************************************/
/*******************************************************************************************************/

class TableBase{
	var $sql;
	var $select	 = null;
	var $from	 = null;
	var $outer   = null;
	var $where	 = null;
	var $having	 = null;
	var $order	 = Array();
	var $group	 = null;
	var $offset	 = null;
	var $limit	 = null;
	var $deleted = false;

	var $baseName = ''; //�x�[�X�e�[�u����
	var $join    = false;//join�t���O
	var $outer_f = false;//join�t���O
	var $union	 = Array();
	var $union_a = false;
	var $sql_char_code = null;

	var $status = 0;

	var $cashed = false;
	var $cash = null;

	function __construct($from){$this->baseName = $from;}

	function getClone(){
		$table			 = new Table( $this->from );
		$table->select	 = $this->select;
		$table->delete	 = $this->delete;
		$table->where	 = $this->where;
		$table->having	 = $this->having;
		$table->group	 = $this->group;
		$table->order	 = $this->order;
		$table->offset	 = $this->offset;
		$table->limit	 = $this->limit;
		$table->baseName = $this->baseName;
		$table->join	 = $this->join;
		$table->outer	 = $this->outer;
		$table->outer_f  = $this->outer_f;
		$table->union_a	 = $this->union_a;
		$table->deleted	 = $this->deleted;

		$union_count = count($this->union);
		if( $union_count > 0 )
		{
			for( $i=0; $i<$union_count; $i++ )
			{
				$table->union[$i] = $this->union[$i]->getClone();
			}
		}

		$table->status	 = $this->status;
		return $table;
	}

	function getString($free_order=false,$is_row=false)
	{
		$sql	 = $this->toSelectFrom();
		$sql	.= $this->getWhere();

		if( count($this->union) ){
			foreach( $this->union as $unionTable )
			{
				$sql .= ' UNION';
				if($this->union_a){
					$sql .= ' ALL';
				}
				$sql .= ' '.$unionTable->getUnionString();
			}
		}

		if( $this->group != null ){
			$sql	 .= " GROUP BY ". $this->group;
			if( $this->having != null ){
				$sql	 .= " HAVING ". $this->having;
			}
		}
		if( !$is_row && ($this->offset == 0 || $this->offset != null) && $this->limit != null ){
			// limit ���w�肳��Ă���ꍇ�� �����I�v�V�������I���ɂ���
			// ������ row �̏ꍇ�̓X���[
			$free_order = false;
		}
		if( !$free_order  ){
			if( is_array( $this->order ) ){
				if( count($this->order) ){
					$sql	 .= " ORDER BY";
					$ord = Array();
					foreach( $this->order as $col => $val ){
						$ord[] = " $col $val";
					}
					$sql .= join(',',$ord);
				}else{
					$sql	 .= " ORDER BY shadow_id";
				}
			}
		}

		$sql .= $this->getLimitOffset();

		return $this->sql_convert( $sql );
	}

	function getUnionString( $data = null ){

		if( isset($data) ) { $this->select	 = str_replace( '*' , "shadow_id,".$data , $this->select ); }
		$sql	 = $this->toSelectFrom();
		$sql	.= $this->getWhere();

		if( $this->group != null ){
			$sql	 .= " GROUP BY ". $this->group;
		}

		return $this->sql_convert( $sql );

	}

	//Row�擾�p
	function getRowString(){
		$cTable = $this->getClone();

		if( $cTable->select == '*' && is_null( $cTable->limit ) && !count($this->union) ){
			$cTable->select	 = 'COUNT(*) as cnt';
			$sql	 = $cTable->toSelectFrom();

			$sql	.= $this->getWhere();

			$sql .= $this->getLimitOffset();
		}else{
			$sql = 'SELECT count(*) as cnt FROM ('.
				$cTable->getString(true,true)
				.') as result_table';
		}

		return $this->sql_convert( $sql );
	}

	function getLimitOffset(){
		throw new RuntimeException('Table::'.__FUNCTION__.'  Unmounting');
	}

	function sql_convert( $val ){
		throw new RuntimeException('Table::'.__FUNCTION__.'  Unmounting');
	}

	/*
	 * and/or�̏�����ǉ�����
	 * @param sql			�ǉ��������
	 * @param conjunction	�ڑ���(and/or)
	 */
	function addWhere( $sql, $conjunction = 'and' ){
		$conjunction = strtolower($conjunction);
		if( is_null($this->where) ){
			$this->where= $sql ;
		}else{
			if(is_string($this->where)){
				$this->where = Array( $conjunction => Array( $this->where ) );
			}else if(!isset($this->where[$conjunction])){
				$old = $conjunction == 'and' ? 'or' : 'and';
				$this->where[ $conjunction ][$old] = $this->where[ $old ];

				unset($this->where[ $old ]);
			}
			array_push($this->where[$conjunction],$sql);
		}

		$union_count = count($this->union);
		if( $union_count > 0 )
		{
			for( $i=0; $i<$union_count; $i++ ) { $this->union[$i]->addWhere( $sql, $conjunction ); }
		}
	}

// begin �A�t�B���G�C�g�V�X�e��PRO2 ��p ����
	/*
	 * �ۑ����Ă���where�̓��e����͂��ĕ�����ɐ��`����
	 * @param $array		and��������or�̔z��
	 */
	function getWhere( $del_falg = true ){
		$ret = "";
		
		if( $del_falg && strlen( $this->delete ) ){
			$ret .= $this->delete;
		}
		$where = $this->getWhereReflexive();
		
		if( $ret && $where ){
			$ret .= " AND ";
		}
		$ret .= $where;
		
		if(strlen($ret)){ $ret = " WHERE " . $ret; }
		
		return $ret;
	}
// end �A�t�B���G�C�g�V�X�e��PRO2 ��p ����


	function getWhereReflexive( $conjunction = null , $array = null ){
		if(is_null($array)){
			if(is_null($this->where)){return "";}
			else if(!is_array($this->where)){return '('.$this->where.')';}

			foreach( $this->where as $key => $val ){
				$array = $val;
				$conjunction = $key;
				break;
			}
		}
		foreach( $array as $key => $val ){
			if(is_array($val)){$array[$key]=$this->getWhereReflexive($key,$val);}
		}
		return "(".implode($array," $conjunction ").")";
	}

	/*
	 * �ۑ����Ă���where�̓��e�������Ɏw�肳�ꂽtable���x�[�X�Ƃ���join�e�[�u���ɕύX����
	 */
	function changeJoinTable( $base_tbl_name, $array = null ){

		$flg = false;
		if(is_null($array)){

			//�܂�order
			if(!count($this->order)){ $this->order['shadow_id'] = 'DESC'; }
			$new_order = Array();
			foreach( $this->order as $key => $val ){
				$new_order[ $base_tbl_name.'.'.$key ] = $val;
			}
			$this->order = $new_order;


			$base_tbl_name = ' '.$base_tbl_name.'.';
			if(is_null($this->where)){return;}
			else if(!is_array($this->where)){$this->where = $base_tbl_name.$this->where; return; }
			$array = $this->where;
			$flg = true;
		}
		foreach( $array as $key => $val ){
			if(is_array($val)){$array[$key]=$this->changeJoinTable($base_tbl_name,$val);}
			else{ $array[$key]=$base_tbl_name.$val; }
		}
		if($flg){ $this->where = $array; }
		return $array;
	}

	function toSelectFrom(){
		if($this->outer_f){
			$sql = 'SELECT '.$this->select.' FROM '.$this->from.' '.$this->outer;
		}else{
			$sql = 'SELECT '.$this->select.' FROM '.$this->from;
		}
		return $sql;
	}

	function setUnion($table){
		$this->union[] = $table;
	}

	function setUnionAll($table){
		$this->union[] = $table;
		$this->union_a = true;
	}

	function onCash(){ $this->cashed = true; }
	function offCash(){ $this->cashed = false; $this->cash = null; }
}

?>