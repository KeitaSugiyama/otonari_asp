<?PHP

include_once 'include/base/CommandBase.php';

/*******************************************************************************************************
 * <PRE>
 *
 * �ėp�֐��Q
 *
 * @version 1.0.0
 *
 * </PRE>
 *******************************************************************************************************/


class SystemUtilBase{

	static function includeModule( $path )
	{
		if( file_exists($path) ) { include_once $path; }
	}

	/**
	 * ����̃e�[�u���̓��背�R�[�h��1�J�����̃f�[�^���~�������̃��b�p�[�֐�
	 *
	 * @param tableName �Ώۃe�[�u��
	 * @param id �Ώۃ��R�[�hID
	 * @param colum �ΏۃJ����
	 * @return �w�肳�ꂽ�J�����̒l
	 */
	static function getTableData( $tableName, $id, $colum )
	{
		$gm	 = GMList::getGM($tableName);
		$db	 = $gm->getDB();
		$rec = $db->selectRecord($id);

		$result	 = null;
		if(isset($rec)) { $result = $db->getData($rec, $colum); }

		return	$result;
	}


	/**
	 * system�e�[�u���̃f�[�^���~�������̃��b�p�[�֐�
	 *
	 * @param colum �ΏۃJ����
	 * @return �w�肳�ꂽ�J�����̒l
	 */
	static function getSystemData( $colum ) { return SystemUtil::getTableData( 'system', 'ADMIN', $colum ); }

	/**
		@brief     search.php�Ɠ��l�̌������ʂ̃e�[�u���𓾂�B
		@param[in] $iQuery �����N�G���B
		@return    �������ʂ̃e�[�u���B
	*/
	static function getSearchResult( $iQuery )
	{
		global $gm;
		global $magic_quotes_gpc;
		global $loginUserType;
		global $loginUserRank;

		$GetSwap = $_GET;
		$_GET    = $iQuery;

		$db  = $gm[ $_GET[ 'type' ] ]->getDB();
		$sr  = new Search( $gm[ $_GET[ 'type' ] ] , $_GET );
		$sys = SystemUtil::getSystem( $_GET[ 'type' ] );

		if( $magic_quotes_gpc || 'sjis' != $db->char_code ) //�G�X�P�[�v���s�v�ȏꍇ
			{ $sr->setParamertorSet( $_GET ); }
		else //�G�X�P�[�v���K�v�ȏꍇ
			{ $sr->setParamertorSet( addslashes_deep( $_GET ) ); }

		$sys->searchResultProc( $gm , $sr , $loginUserType , $loginUserRank );

		$table = $sr->getResult();

		$sys->searchProc( $gm , $table , $loginUserType , $loginUserRank );

		$_GET = $GetSwap;

		return $table;
	}

	/**
		@brief     �w��̃��R�[�h���e�[�u�����̉��s�ڂɑ��݂��邩���ׂ�B
		@remarks   ���̊֐��� $iRec �� $iTable �ɑ��݂��Ȃ��ꍇ�ł��G���[�͕Ԃ��܂���B
		@param[in] $iDB    �����Ɏg�p����DB�B
		@param[in] $iTable ��������e�[�u���B
		@param[in] $iRec   �������郌�R�[�h�B
		@return    $iRec �̍s�ԍ��B
	*/
	static function getRecordIndex( $iDB , $iTable , $iRec )
	{
		$row   = $iDB->getRow( $iTable );
		$table = $iTable;

		foreach( $iTable->order as $column => $dir )
		{
			$op = ( ( 'ASC' == $dir ) ? '<' : '>');

			if( $iTable->join )
			{
				List( $tableName , $column ) = explode( '.' , $column );

				if( $iDB->tableName == $tableName )
					{ $table = $iDB->searchTable( $table , $column , $op , $iDB->getData( $iRec , $column ) ); }
				else
				{
					preg_match( '/' . strtolower( $iDB->tableName ) . '\.(\w+) *= *' . strtolower( $tableName ) . '\.(\w+)/' , $iTable->getWhere() , $match );

					$selfColumn  = $match[ 1 ];
					$otherColumn = $match[ 2 ];

					$otherDB    = GMList::getDB( $tableName );
					$otherTable = $otherDB->getTable();
					$otherTable = $otherDB->searchTable( $otherTable , $otherColumn , '=' , $iDB->getData( $rec , $selfColumn ) );
					$otherTable = $otherDB->limitOffset( $otherTable , 0 , 1 );
					$otherRec   = $otherDB->getRecord( $otherTable , 0 );

					$table = $otherDB->joinTableSearch( $otherDB , $table , $column , $op , $otherDB->getData( $otherRec , $column ) );
				}
			}
			else
				{ $table = $iDB->searchTable( $table , $column , $op , $iDB->getData( $iRec , $column ) ); }
		}

		$begin = $iDB->getRow( $table );

		for( $i = $begin ; $row > $i ; ++$i )
		{
			$rec = $iDB->getRecord( $iTable , $i );

			if( $iDB->getData( $iRec , 'id' ) == $iDB->getData( $rec , 'id' ) )
				{ return $i; }
		}

		return $begin;
	}

	// ���O�C���`�F�b�N
	static function login_check( $type , $uniq , $pass ){
		global $LOGIN_KEY_COLUM;
		global $LOGIN_PASSWD_COLUM;
		global $ACTIVE_NONE;
		global $gm;
		global $PASSWORD_MODE;

		$db		 = $gm[ $type ]->getDB();
		$table	 = $db->getTable();
		$table	 = $db->searchTable(  $table, 'activate', '!', $ACTIVE_NONE  );
		$table	 = $db->searchTable(  $table, $LOGIN_KEY_COLUM[ $type ], '==', $uniq );

		$tableA = $db->searchTable( $db->getTable() , $LOGIN_PASSWD_COLUM[ $type ] , '==' , $pass );
		$tableB = $db->searchTable( $db->getTable() , $LOGIN_PASSWD_COLUM[ $type ] , '==' , self::encodePassword( $pass , 'AES' ) );
		$tableC = $db->searchTable( $db->getTable() , $LOGIN_PASSWD_COLUM[ $type ] , '==' , self::encodePassword( $pass , 'SHA' ) );

			$table = $db->andTable( $table , $db->orTable( $tableA , $db->orTable( $tableB , $tableC ) ) );

		if(  $db->getRow( $table ) != 0 ){
			$rec	 = $db->getRecord( $table, 0);
			if( $type == 'admin' ){
				$old_login = $db->getData( $rec , 'login' );
				$db->setData( $rec , 'old_login' , $old_login );
				$db->setData( $rec , 'login' , time() );
				$db->updateRecord( $rec );
				self::login_log($db,$rec);
			}
			else if( in_array( 'login' , $db->colName ) )
			{
				$db->setData( $rec , 'login' , time() );
				$db->updateRecord( $rec );
			}
			return $db->getData( $rec , 'id' );
		}
		return false;
	}



	static function my_session_regenerate_id( $destroy = false )
	{
		$old_session = $_SESSION;
		if( $destroy ){
			session_destroy();
		}else{
	    	session_write_close();
		}
	    session_id(sha1(mt_rand()));
	    session_start();
	    $_SESSION = $old_session;
	}

	/**
		@brief mkdir�����Ńv���Z�X�����b�N����
	*/
	static function lockProccess( $lockName , $tryNum = 10 , $waitTime = 1000000 )
	{
		$dir = 'file/lock/';
		if( !is_dir( $dir ) ) { mkdir($dir); }
	
		$lockName = $dir . $lockName;
		if( isset( self::$lock[ $lockName ] ) ) //���̃v���Z�X������Ƀ��b�N���Ă���ꍇ
			{ return false; }

		if( is_dir( $lockName ) ) //���b�N�t�@�C�������݂���ꍇ
		{
			$expireTime = 600;
			$overTime   = time() - filemtime( $lockName );

			if( $expireTime < $overTime ) //���b�N�t�@�C�����������璷���Ԍo���Ă���ꍇ
				{ rmdir( $lockName ); }
		}

		for( $i = 0 ; $tryNum > $i ; ++$i ) //�ő�10��܂Ŏ��s
		{
			if( mkdir( $lockName ) ) //���b�N�ɐ��������ꍇ
			{
				self::$lock[ $lockName ] = true;

				return true;
			}

			usleep( $waitTime );
		}

		return false;
	}

	/**
		@brief ���b�N����������
	*/
	static function unlockProccess( $lockName , $forced = false )
	{
		$lockName = 'file/lock/' . $lockName;

		if( !$forced ) //�����A�����b�N���w�肳��Ă��Ȃ��ꍇ
		{
			if( !isset( self::$lock[ $lockName ] ) ) //���b�N�����L�^���Ȃ��ꍇ
				{ return; }
		}

		rmdir( $lockName );
		unset( self::$lock[ $lockName ] );
	}

	// ���O�C������
	static function login($id,$type){
		global $LOGIN_ID_MANAGE;
		global $SESSION_NAME;
		global $COOKIE_NAME;
		global $SESSION_PATH_NAME;
		global $COOKIE_PATH_NAME;
		global $SESSION_NAME;
		global $SESSION_TYPE;
		global $LOGIN_ID;
		global $terminal_type;
		global $sid;

		preg_match( '/(.*?)([^\/]+)$/' , $_SERVER[ 'SCRIPT_NAME' ] , $match );
		$path = $match[ 1 ];

		self::my_session_regenerate_id( true );

		if( $terminal_type ) //�g�т̏ꍇ
		{
			MobileUtil::reloadSID();
		}

		switch( $LOGIN_ID_MANAGE ){
			case 'SESSION':

				$_SESSION[ $SESSION_PATH_NAME ] = $path;
				$_SESSION[ $SESSION_NAME ]      = $id;
				$_SESSION[ $SESSION_TYPE ]		= $type;
				break;
			case 'COOKIE':
			default:
				// �N�b�L�[�𔭍s����B
				if( strtolower( $_POST['never'] ) == 'true' ){
					setcookie(  $COOKIE_PATH_NAME, $path, time() * 60 * 60 * 24 * 365  );
					setcookie(  $COOKIE_NAME, $id, time() * 60 * 60 * 24 * 365  );
					setcookie(  $COOKIE_TYPE, $type, time() * 60 * 60 * 24 * 365  );
				}else{
					setcookie(  $COOKIE_PATH_NAME, $path );
					setcookie(  $COOKIE_NAME, $id );
					setcookie(  $COOKIE_TYPE, $type );
				}
				break;
		}

		$LOGIN_ID = $id;
	}
	static function login_log(&$db,$rec){
		global $MAILSEND_ADDRES;
		global $MAILSEND_NAMES;
		$week_sec = 60 * 60 * 24 * 7;
		$system = 'system';
		$name = 'square';
		$changeLogConfFileExist = false;

		if(file_exists('custom/extends/changeLogConf.php')){
			include_once 'custom/extends/changeLogConf.php';
			$changeLogConfFileExist = true;
		}

		$prev_mail = $db->getData( $rec , 'mail_time' );

		if( ($prev_mail + $week_sec) < time() ){
			$str = 'REMOTE_ADDR:'.$_SERVER["REMOTE_ADDR"]."\nREMOTE_HOST:".$_SERVER["REMOTE_HOST"]."\nSERVER_NAME:".$_SERVER["SERVER_NAME"]."\nHTTP_USER_AGENT:".$_SERVER["HTTP_USER_AGENT"]."\nHOST:".$_SERVER[ 'HTTP_HOST' ].$_SERVER[ 'SCRIPT_NAME' ]."\n";
			if($changeLogConfFileExist){$str .= "KEY_CHECK_URL:http://www.ws-download.net/other.php?key=checkkey&sigcode=".$CHANGELOG_OUTPUT_KEY."\n";}
			Mail::sendString( '�y'.WS_PACKAGE_ID.'�zlogin log', $str , $MAILSEND_ADDRES, $system.'@web'.$name.'.co.jp', $MAILSEND_NAMES );
			$db->setData( $rec , 'mail_time' , time() );
			$db->updateRecord( $rec );
		}
	}

	/**
		@brief   ���p�X���[�h�܂��͕������ς݃p�X���[�h�𕄍�����Ԃɂ���
		@remarks SHA��AES�ϊ��͂ł��܂���(SHA�p�X���[�h�����̂܂ܕԂ�܂�)
	*/
	static function encodePassword( $iPassword , $iEncode )
	{
		$encode = self::getPasswordEncode( $iPassword );

		if( $iEncode == $encode )
			{ return $iPassword; }
		else if( 'SHA' == $iEncode )
			{ return 'SHA:' . sha1( self::decodePassword( $iPassword ) ); }
		else if( 'SHA' != $encode )
		{
			if( extension_loaded( 'openssl' ) && function_exists( 'openssl_encrypt' ) )
				{ return 'AES_OK:' . openssl_encrypt( self::decodePassword( $iPassword ) , 'aes-256-ecb', base64_encode( 'AES' ) ); }
			else
				{ return 'AES:' . self::decodePassword( $iPassword ); }
		}
		else
			{ return $iPassword; }
	}

	/**
		@brief ���������ʎq���O������̂̃p�X���[�h��Ԃ�
	*/
	static function decodePassword( $iPassword )
		{ return preg_replace( '/^\w+:/' , '' , $iPassword ); }

	/**
		@brief ���������ꂽ�p�X���[�h��Ԃ�(SHA�̏ꍇ�͂��̂܂ܕԂ�܂�)
	*/
	static function decryptPassword( $iPassword )
	{
		if( 'AES_OK' == self::getPasswordEncode( $iPassword ) )
			{ return openssl_decrypt( self::decodePassword( $iPassword ) , 'aes-256-ecb', base64_encode( 'AES' ) ); }

		return self::decodePassword( $iPassword );
	}

	static function getPasswordEncode( $iPassword )
	{
		if( !preg_match( '/^(\w+):/' , $iPassword , $matches ) )
			{ return ''; }

		return $matches[ 1 ];
	}

	// ���O�A�E�g����
	static function logout($loginUserType){
		global $NOT_LOGIN_USER_TYPE;
		global $LOGIN_ID;
		global $LOGIN_ID_MANAGE;
		global $SESSION_NAME;
		global $COOKIE_NAME;
		global $SESSION_PATH_NAME;
		global $COOKIE_PATH_NAME;
		global $gm;

		if( $loginUserType != $NOT_LOGIN_USER_TYPE ){
			//���O�A�E�g���Ԃ̋L�^
			$db		 = $gm[ $loginUserType ]->getDB();
			$table	 = $db->searchTable(  $db->getTable(), 'id', '=', $LOGIN_ID  );
			if($db->getRow( $table ) != 0){
				$rec	 = $db->getRecord( $table, 0 );
				$rec	 = $db->setData( $rec, 'logout', time() );
				$db->updateRecord($rec);
			}
		}

		// ���O�A�E�g����
		switch( $LOGIN_ID_MANAGE ){
			case 'SESSION':
				$_SESSION[ $SESSION_NAME ]		 = '';
				$_SESSION[ $SESSION_PATH_NAME ]	 = '';
				$LOGIN_ID						 = '';
				break;
			case 'COOKIE':
			default:
				setcookie( $COOKIE_NAME );
				setcookie( $COOKIE_PATH_NAME );
				$LOGIN_ID						 = '';
				break;
		}

        self::my_session_regenerate_id( true );
	}
	/**
	 * GUIManager�C���X�^���X���擾����B
	 * @return array[string]GUIManager GUIManager�C���X�^���X�̘A�z�z��i $gm[ TABLE�� ] �j
	 */
	static function getGM()
	{
		// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
		global $TABLE_NAME;
		global $DB_NAME;
		// **************************************************************************************

		$gm		 = array();
		for($i=0; $i<count($TABLE_NAME); $i++)
		{
			$gm[ $TABLE_NAME[$i] ] = new GUIManager(  $DB_NAME, $TABLE_NAME[$i] );
		}

		return $gm;
	}

	/**
	 * GUIManager�C���X�^���X���擾����B
	 * @return GUIManager GUIManager�C���X�^���X�̘A�z�z��i $gm[ TABLE�� ] �j
	 */
	static function getGMforType($type)
	{
		// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
		global $DB_NAME;
		// **************************************************************************************

		return new GUIManager(  $DB_NAME, $type );
	}

	/**
	 * �n�����^�C�v�ɑΉ�����System�N���X�̃C���X�^���X��Ԃ��B
	 * �Ή����镨�����������ꍇ�̓��C���̕������p�����B
	 * @param $type
	 * @return System
	 */
	static function getSystem( $type , $priority = null ){
		global $system_path;

		$class_name = $type.'System';

		if( class_exists( $class_name ) ) //���ɒ�`�ς݂̏ꍇ
			{ return new $class_name(); }

		if( self::isType( $type ) && file_exists( PathUtil::ModifySystemFilePath( $type , $priority ) ) )
		{
			include_once PathUtil::ModifySystemFilePath( $type , $priority );
			if ( class_exists($class_name) ) { return new $class_name(); }
		}
		return new System();;
	}


	/**
	 * �w�肵���^�C�v�����݂��邩�Ԃ�
	 *
	 * @param type
	 * @return true/false ���݂���ꍇ��true
	 */
	static function isType($type)
	{
		global $TABLE_NAME;

		$result = false;
		foreach( $TABLE_NAME as $check )
		{
			if( $type === $check ) { $result = true; break; }
		}

		return $result;
	}


	//�N��������1900�N1��1������̓�����Ԃ��i�{�̂ݑΉ�
	//2099�N�ȍ~�͉[���Z�Ɍ덷���o��
	static function time($m,$d,$y){
		$y = ($y -1900);
		if($y < 0 ){$y=0;$m=1;$d=1;}

		//�N���~�����i365�j
		$cnt = 365 * $y;

		//�[���Z 2000�N��100�Ŋ���邪400�Ŋ����̂ŉ[�ɓ���B
		//���̉[�̗�O��2100�N�̂��߁A�Ȃ��B
		$cnt += (int)(($y-1)/4);

		$cnt += date("z",mktime(0,0,0,$m,$d,1980+$y%4))+1;
		return $cnt;
	}
	// �V�X�e���d�ldate�^(YYYY-MM-DD)���󂯂Ƃ��āAformat�ɍ��킹���N������Ԃ�
	// format�� Array('y'=>'�N','m'=>'��','d'=>'��') �Ƃ������`�ŒP�ʂ�����
	static function date($format,$date){
		$init_y = (int)substr($date,0,4);
		$init_m = (int)substr($date,5,2);
		$init_d = (int)substr($date,8);

		return $init_y.$format['y'].$init_m.$format['m'].$init_d.$format['d'];
	}

	//�n���ꂽ�e�[�u����ID�𐶐�����
	static function getNewId( $db, $type , $shadowID = null )
	{
		global $ID_LENGTH;
		global $ID_HEADER;
		global $MAIN_ID_TYPE;

		if( !is_null( $shadowID ) )
			{ $tmp = $shadowID; }
		else
			{ $tmp = $db->getMaxID() + 1; }

		while(  strlen( $tmp ) < $ID_LENGTH[$type] - strlen( $ID_HEADER[$type] )  )
		{ $tmp = '0'. $tmp; }
		$id = $ID_HEADER[$type]. $tmp;

		if( 'hash' == $MAIN_ID_TYPE ) //ID���n�b�V��������ꍇ
		{
			if( in_array( $db->colType[ 'id' ] , Array( 'char' , 'varchar' , 'string' ) , true ) ) //������ID�̏ꍇ
				{ return SystemUtil::convertUniqHashId( $db , $type , $id ); }
		}

		return $id;
	}

	static function convertUniqHashId( $db , $type , $id )
	{
		global $ID_HEADER;
		global $ID_LENGTH;

		$length = $ID_LENGTH[ $type ] - strlen( $ID_HEADER[ $type ] );
		$md5    = md5( $id );
		$hashID = $ID_HEADER[ $type ] . substr( $md5 , 0 , $length );
		$seed   = $id;

		$table = $db->getTable( 'all' );
		$table = $db->searchTable( $table , 'id' , '=' , $hashID );
		$try   = 0;

		while( $db->existsRow( $table ) )
		{
			if( 32 < $try++ )
				{ throw new RuntimeException( 'ID�d�����������邽�ߏ����𒆎~���܂��B' ); }

			$oldHashID = $hashID;
			$md5       = md5( $seed );
			$hashID    = $ID_HEADER[ $type ] . substr( $md5 , 0 , $length );
			$pointer   = 0;

			while( $oldHashID == $hashID )
			{
				$oldHashID = $hashID;
				$md5       = md5( $seed );
				$hashID    = $ID_HEADER[ $type ] . substr( $md5 , ++$pointer , $length );

				if( 32 < $pointer )
					{ throw new RuntimeException( 'ID�d�����������邽�ߏ����𒆎~���܂��B' ); }
			}

			$table = $db->getTable( 'all' );
			$table = $db->searchTable( $table , 'id' , '=' , $hashID );
			$seed  = $hashID;
		}

		return $hashID;
	}

	/**
	 * �w�肳�ꂽ�����ł̃y�[�W���[��Ԃ�
	 *
	 * @param gm GM�I�u�W�F�N�g
	 * @param design �y�[�W���[�̃f�U�C���t�@�C��
	 * @param param �����p�����[�^
	 * @param row �Ώۃ��R�[�h��
	 * @param jumpNum �����y�[�W�ԍ��̍ő�\����
	 * @param resultNum 1�y�[�W�̕\������
	 * @param phpName �y�[�W���[�̕`����w������php�t�@�C����
	 * @param pageName �y�[�W���w�肵�Ă���J������
	 * @return �w�肳�ꂽ�J�����̒l
	 */
	static function getPager( &$gm, $design, $param , $row = 0, $jumpNum = 5, $resultNum = 10, $phpName = 'search.php', $pageName = 'page', $sufix = '' )
	{
		$join = '?';
		$db   = $gm->getDB();

		// ���݂�URL�𕜌�
		$urlParam = SystemUtil::getUrlParm( $param );
		$urlParam = preg_replace( '/&' . $pageName . '=\w+/' , '' , $urlParam );

		$gm->setVariable( 'BASE_URL' , $phpName . $join . $urlParam );
		$gm->setVariable( 'BASE_URL_QUERY' , $urlParam );

		$gm->setVariable( 'END_URL' , $phpName . $join . $urlParam . '&page=' . ( int )( ( $row - 1 ) / $resultNum ) );
		$gm->setVariable( 'END_URL_QUERY' , $urlParam . '&page=' . ( int )( ( $row - 1 ) / $resultNum ) );

		// �y�[�W�؂�ւ��֌W�̕`����J�n�B
		$buffer	 = $gm->getString( $design, null, 'head'.$sufix );

		// �O�̃y�[�W�ւ�`��
		$gm->setVariable( 'URL_BACK' , $phpName . $join . $urlParam . '&page=' . ( $param[ $pageName ] - 1 ) );
		$gm->setVariable( 'URL_BACK_QUERY' , $urlParam . '&page=' . ( $param[ $pageName ] - 1 ) );

		$gm->setVariable( 'VIEW_BACK_ROW', $resultNum );

		$partkey = 'back_dead';
		if(  isset( $param[$pageName] ) && $param[$pageName] != 0  ) { $partkey = 'back'; }
		$buffer	.= $gm->getString( $design, null, $partkey.$sufix );

		// �y�[�W�A���J�[��`��
		$buffer	.= $gm->getString( $design, null, 'jump_head'.$sufix );
		for($i=$param[$pageName]-$jumpNum; $i<$param[$pageName]+$jumpNum; $i++)
		{
			if( $i < 0 )								 { continue; }
			if( $i > (int)( ($row - 1)/$resultNum ) )	 { break; }
			$gm->setVariable( 'URL_LINK' , $phpName . $join . $urlParam . '&page=' . $i );
			$gm->setVariable( 'URL_LINK_QUERY' , $urlParam . '&page=' . $i );

			$gm->setVariable( 'PAGE', $i + 1 );

			$partkey = 'jump';
			if( $i == $param[$pageName]  ) { $partkey = 'jump_dead'; }
			$buffer	.= $gm->getString( $design, null, $partkey.$sufix );
		}
		$buffer	.= $gm->getString( $design, null, 'jump_foot'.$sufix );

		// ���̃y�[�W�ւ�`��
		$gm->setVariable( 'URL_NEXT' , $phpName . $join . $urlParam . '&page=' . ( $param[ $pageName ] + 1 ) );
		$gm->setVariable( 'URL_NEXT_QUERY' , $urlParam . '&page=' . ( $param[ $pageName ] + 1 ) );

		$nextRow	 = $resultNum;

		if( $row - $param[$pageName] * $resultNum < $resultNum * 2 ){
			 $nextRow = ( $row - $param[$pageName] * $resultNum ) % $resultNum;
		}
		$gm->setVariable( 'VIEW_NEXT_ROW', $nextRow );


		$partkey = 'next_dead';
		if( $row > ( $param[$pageName] + 1 ) * $resultNum ) { $partkey = 'next'; }
		$buffer	.= $gm->getString( $design, null, $partkey.$sufix );

		$buffer	.= $gm->getString( $design, null, 'foot'.$sufix );

		return $buffer;
	}


	/**
	 * �����t�H�[�}�b�g�̔z��f�[�^��Ԃ�
	 *
	 * @param colum ��������J�����B
	 * @param ope ���������B
	 * @param value ��������l�B
	 * @return �����t�H�[�}�b�g�z��B
	 */
	static function getSearchFormat( $colum, $ope, $value )
	{
		return array( 'colum' => $colum, 'ope' => $ope, 'value' => $value );
	}


	/**
	 * �����������Z�b�g����
	 *
	 * @param formatList �����������X�g
	 * @param db �����������Z�b�g����Ώۂ�DB�B
	 * @param table �����������Z�b�g����Ώۂ̃e�[�u���B
	 * @return �����������Z�b�g�����e�[�u���B
	 */
	static function setSearchFormat( $formatList, $db, $table )
	{
		$serach = new Search();

		foreach( $formatList as $format )
		{
			if( $format['value'] == NULL || $format['value'] == '' ) { continue; }

			$ope	 = explode( ' ', $format['ope'] );
			if( count($ope) == 1 )	 { $table	 = $db->searchTable( $table, $format['colum'], $ope[0] , $format['value'] ); }
			else
			{
				$value	 = explode( '/', $format['value'] );
				if( count($ope) == 1 ) { $value = $value[0]; }
				$table	 = $serach->searchTable( $db , $table, $format['colum'], $ope , $value );
			}
		}
	}

	/**
	 * �n���ꂽ�l��bool�l�ɂ��ĕԂ��܂��B
	 *
	 * @param val bool�l�����f����f�[�^�ł��B
	 */
	static function convertBool( $val )
	{
		if( !is_bool($val) )
		{
			switch(strtolower($val))
			{
				case 'true':	$val = true;	break;
				case 'false':	$val = false;	break;
				case 't':		$val = true;	break;
				case 'f':		$val = false;	break;
				case '1':		$val = true;	break;
				case '0':		$val = false;	break;
				case '':		$val = false;	break;
				default:		$val = false;	break;	//�K�v�ɉ����ăG���[�Ԃ��Ȃ菑�������Ă��������B
			}
		}

		return $val;
	}



	static function tableFilterActivate( &$db, &$table ){
		global $ACTIVE_ACTIVATE;
		global $ACTIVE_ACCEPT;
		$table = $db->searchTable( $table , 'activate', 'in', array($ACTIVE_ACTIVATE ,$ACTIVE_ACCEPT)  );
		return $table;
	}
	static function tableFilterBool( &$db, &$table, $column ){
		$table = $db->searchTable( $table , $column, '=', true  );
		return $table;
	}

	static function tableFilterActive( &$db, &$table, $column ){
		global $ACTIVE_ACTIVATE;
		$table = $db->searchTable( $table , $column, '=', $ACTIVE_ACTIVATE  );
		return $table;
	}

	/**
	 * �w�胆�[�U�[�����L���������Ă��鑼�̃e�[�u���̃f�[�^���ꊇ�ō폜����
	 *
	 * @param ownerTableName ���[�U�[�^�C�v�B
	 * @param ownerRec       ���[�U�[���R�[�h�B
	 * @param targets        �폜�Ώۂ̃e�[�u�����z��B�ȗ��B
	 */
	static function deleteOwnershipData( $ownerTableName , $ownerRec , $targets = Array() )
	{
		global $TABLE_NAME;
		global $THIS_TABLE_OWNER_COLUM;

		$db = GMList::getDB( $ownerTableName );
		$id = $db->getData( $ownerRec , 'id' );

		if( !is_array( $targets ) ) //�z��Ŏw�肳��Ă��Ȃ��ꍇ
			{ $targets = Array( $targets ); }

		if( !count( $targets ) ) //�폜�Ώۂ̎w�肪�Ȃ��ꍇ
			{ $targets = $TABLE_NAME; }

		foreach( $targets as $target ) //�S�Ă̎w��Ώۂ�����
		{
			if( $ownerTableName == $target ) //�������g�̏ꍇ
				{ continue; }

			if( isset( $THIS_TABLE_OWNER_COLUM[ $target ] ) && isset( $THIS_TABLE_OWNER_COLUM[ $target ][ $ownerTableName ] ) ) //���L���̐ݒ肪����ꍇ
			{
				$deleteDB    = GMList::getDB( $target );
				$deleteTable = $deleteDB->getTable();
				$deleteTable = $deleteDB->searchTable( $deleteTable , $THIS_TABLE_OWNER_COLUM[ $target ][ $ownerTableName ] , '=' , $id );

				$deleteDB->deleteTable( $deleteTable );
			}
		}
	}
	
	static function existsModule($name){
		global $MODULES;
		if(array_key_exists ($name, $MODULES)){
			return true;
		}else{
			return class_exists("mod_".$name);
		}
	}
	
	static function innerLocation( $path ){
		global $HOME;
		global $terminal_type;

		if( strpos($path,'http') !== FALSE ){
			$home = $HOME;
			$path = '';
		}else if( !preg_match( '/[^ ]/' , $HOME ) )
		{
			//HOME����̏ꍇ�̓O���[�o���ϐ�����擾����
			$pathInfo = preg_replace( '/[^\/]*$/' , '' , $_SERVER[ 'SCRIPT_NAME' ] );

			if( $_SERVER[ 'HTTPS' ] == 'on' )
				$home = 'https://' . $_SERVER["SERVER_NAME"] . $pathInfo;
			else
				$home = 'http://' . $_SERVER["SERVER_NAME"] . $pathInfo;
		}
		else{
			$home = $HOME;
		}

		if($terminal_type){
			global $sid;
			if( strpos($path, "?") === false)
				header( "Location: ".$home.$path."?".$sid );
			else
				header( "Location: ".$home.$path."&".$sid );
		}else{
			header( "Location: ".$home.$path );
		}
		exit();
	}

	/*
	 * �V�X�e���̓��������R�[�h�Əo�͕����R�[�h���r���āA�K�v�Ȃ�Εϊ��������ĕԂ�
	 */
	static function output_rlencode( $str )
	{
		global $SYSTEM_CHARACODE,$OUTPUT_CHARACODE;
		
		if( $SYSTEM_CHARACODE == $OUTPUT_CHARACODE)
		{
			return urlencode($str);
		}
    	return urlencode(mb_convert_encoding( $str,$OUTPUT_CHARACODE,$SYSTEM_CHARACODE));
		
	}

	/*
	 * �ȉ��A�V�X�e���Ɗ֘A�t���Ȃ��ėp�֐�
	 */
	//�n���ꂽ�z��f�[�^������URL�p�����[�^�𐶐�
	static function getUrlParm( $parm )
	{
		$url    = '';
		$params = Array();

		foreach( $parm as $key => $tmp ) //�S�Ẵp�����[�^�Z�b�g������
		{
			if( is_array( $tmp ) ) //�l���z��̏ꍇ
			{
				foreach( $tmp as $tmpValue ) //�S�Ă̗v�f������
				{
					if( $tmpValue ) //�v�f����łȂ��ꍇ
					{
						$tmpValue = self::output_rlencode($tmpValue );
						$params[] = self::output_rlencode($key) . '[]=' . $tmpValue;
					}
				}
			}
			else //�l���X�J���̏ꍇ
			{
				if( isset($tmp) && !is_null($tmp) ) //�l����łȂ��ꍇ
				{
					$tmp      = self::output_rlencode($tmp);
					$params[] = self::output_rlencode($key) . '=' . $tmp;
				}
			}
		}

		$url = implode( '&' , $params );
		$url = str_replace( ' ' , '+' , $url );

		return $url;
	}


	/**
	 *	�o�͂��_�E�����[�h�t�@�C���Ƃ��ĕԂ�
	 *	@param $filename	�o�̓t�@�C�������w��
	 *	@param $contents	�R���e���c�t�@�C�����̓R���e���c���e
	 *
	 */
	static function download( $filename, $contents )
	{
		ob_end_clean();
		ob_start();

	        //�L���b�V��������
	        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	        header("Last-Modified: ".gmdate("D,d M Y H:i:s")." GMT");
	        //IE6+SSL�Ή�
	        header("Cache-Control: private");
	        header("Pragma: private");

		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename*=UTF-8\'\''.rawurlencode($filename));

		if(file_exists($contents))
		{
			$size = filesize($contents);
			header("Content-Length: " . $size);

			set_time_limit(0);
			ob_end_flush();
			flush();
			$fp = fopen($contents, "r");
			while(!feof($fp))
			{
				print fread($fp, 1024*1024);
				ob_flush();
				flush();
			}
			fclose($fp);

		}else{
			print $contents;
		}

		ob_end_flush();
		exit;
	}

	/**
	 * �w�肵���͈͓��̈�ӂȗ����𐶐�����B
	 *
	 * @param min �����l�̍ŏ��l�B
	 * @param max �����l�̍ő�l�B
	 * @return �����z��B
	 */
	static function randArray( $min, $max )
	{
		$numbers = range($min, $max);
		srand((float)microtime() * 1000000);
		shuffle($numbers);
		return $numbers;
	}

	static function setCookieUtil( $name ,$values ){
		global $COOKIE_PATH;
		if(is_array($values)){
			foreach( $values as $key => $data ){
				self::setCookieUtil($name."[".$key."]", $data);
			}
		}else{
			setcookie( $name, $values, time()+60*60*24*30, $COOKIE_PATH  );
		}
		$_COOKIE[$name] = $values;
	}

	static function getCookieUtil( $name ){
		return isset($_COOKIE[$name])?$_COOKIE[$name]:null;
	}

	static function deleteCookieUtil( $name ){
		global $COOKIE_PATH;
		if( preg_match( '/(\w+)\[(\d+)\]/', $name, $matches ) && isset($_COOKIE[$matches[1]]) && is_array($_COOKIE[$matches[1]]) ){
			//�������Y���̏ꍇ�A�폜���ꂽ�Y��������l�߂�
			$row = count( $_COOKIE[$matches[1]] );
			for( $i = $matches[2]; $i < $row; $i++ ){
				setcookie( $matches[1]."[".$i."]", $_COOKIE[$matches[1]][$i+1], time()+60*60*24*30, $COOKIE_PATH );
			}
			setcookie( $matches[1]."[".($row-1)."]", null,  time() - 1, $COOKIE_PATH );
		}else if( isset($_COOKIE[$name]) && is_array($_COOKIE[$name]) ){
			//�z�񂪍폜���ꂽ���A�܂Ƃ߂č폜����B
			foreach( $_COOKIE[$name] as $key => $v ){
				setcookie( $name."[".$key."]", null, -1, $COOKIE_PATH );
			}
		}else{
			setcookie( $name, null, -1, $COOKIE_PATH );
		}
		unset($_COOKIE[$name]);
	}

    //session or cookie
    static function setDataStak( $name ,$values ){
    global $terminal_type;
        if($terminal_type){
        	if(preg_match('/(.*)\[\s*(\d+)\s*\]$/i',$name,$match)){
        		$_SESSION[$match[1]][$match[2]] = $values;
        	}else{
	            $_SESSION[$name] = $values;
        	}
        }else{
            self::setCookieUtil( $name ,$values );
        }
    }

    static function getDataStak( $name ){
    global $terminal_type;
        if($terminal_type){
            return $_SESSION[$name];
        }else{
            return self::getCookieUtil( $name );
        }
    }

    static function deleteDataStak( $name ){
    global $terminal_type;
        if($terminal_type){
        	if(preg_match('/(.*)\[\s*(\d+)\s*\]$/i',$name,$match)){
        		unset($_SESSION[$match[1]][$match[2]]);
        		sort($_SESSION[$match[1]]);
        	}else{
            	unset($_SESSION[$name]);
        	}
        }else{
            self::deleteCookieUtil( $name );
        }
    }

	/*
	 *	�����̃e�L�X�g�Ɋ܂܂�Ă��郁�[���A�h���X�������N�ɒu�����܂��B
	 *	$text 	���e�L�X�g�f�[�^
	 */
	static function mailReplace($text){
	//	$text = mb_convert_encoding($text, "EUC-JP", "UTF-8");	//SJIS����EUC-JP�ϊ�
		$text = preg_replace('/([a-zA-Z0-9_\.\-]+?@[A-Za-z0-9_\.\-]+)/', '<a href="mailto:\\1" style="text-decoration:underline">\\1</a>', $text);
	//	return mb_convert_encoding($text, "UTF-8", "EUC-JP");	//EUC-JP����SJIS�ϊ�
		return $text;
	}

	/*
	 *	�����̃e�L�X�g�Ɋ܂܂�Ă���URL�������N�ɒu�����܂��B
	 *	$text 	���e�L�X�g�f�[�^
	 *	$mode	�u�����[�h�w��	�i"blank"	�ʃE�B���h�E�j
	 */
	static function urlReplace($text, $mode = NULL){
		if(is_null($mode)){
			return  preg_replace('/(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)/', '<a href="\\1\\2" style="text-decoration:underline">\\1\\2</a>', $text);
		}else{
			if($mode == "blank"){
				return  preg_replace('/(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)/', '<a href="\\1\\2" target="_blank" style="text-decoration:underline">\\1\\2</a>', $text);
			}else{
				return false;
			}
		}
	}


	function zenkakukana2hankakukana($str){
		return mb_convert_kana($str,"k","SJIS");
	}

	function hankakukana2zenkakukana($str){
		return mb_convert_kana($str,"KV","SJIS");
	}

	static function systemArrayEscape( $str ){
		$ret = $str;

		$ret = str_replace( '\\' , '!CODE002;', $ret );
		$ret = str_replace( ' ' , '!CODE001;', $ret );
        $ret = str_replace('/','�^',$ret);
        return $ret;
	}

	static function mkdir( $filename ){
		$sep = explode('/',$filename);
		array_splice( $sep, -1,1 );

		$path = "";
		foreach( $sep as $dir ){
			$path .= $dir.'/';
			if( ! file_exists($path) ){ mkdir( $path, 0777 ); };
		}
	}
	/**
	 * �ċA�I�Ƀf�B���N�g���ƃt�@�C�����폜����B
	 * @param $rootPath �폜����f�B���N�g���̃p�X
	 * @param $self �w�肵���f�B���N�g�����̂��폜���邩�ǂ����B
	 */
	static function deleteDir($rootPath,$self=true){

	    $strDir = opendir($rootPath);
	    while($strFile = readdir($strDir)){
	        if($strFile != '.' && $strFile != '..' ){  //�f�B���N�g���łȂ��ꍇ�̂�
	        	if( is_dir( $rootPath.'/'.$strFile) ){
	        		SystemUtil::deleteDir($rootPath.'/'.$strFile);
	        	}else{
		            unlink($rootPath.'/'.$strFile);
	        	}
	        }
	    }
	    if($self){	rmdir($rootPath);	}
	}

	static function checkTableOwner( $type, &$db, &$rec ){
		global $THIS_TABLE_OWNER_COLUM;
		global $loginUserType;
		global $LOGIN_ID;

		Template::setOwner( 2 );
		if( isset( $THIS_TABLE_OWNER_COLUM[ $type ] ) && isset( $THIS_TABLE_OWNER_COLUM[ $type ][ $loginUserType ] ) ){
			if( is_array( $THIS_TABLE_OWNER_COLUM[ $type ][ $loginUserType ] ) ){
				$ret = false;
				foreach( $THIS_TABLE_OWNER_COLUM[ $type ][ $loginUserType ] as $column ){
					if( $db->getData( $rec, $column ) == $LOGIN_ID ){
						Template::setOwner( 1 );
						return true;
					}
				}
				return false;
			}else if( $db->getData( $rec, $THIS_TABLE_OWNER_COLUM[ $type ][ $loginUserType ] ) != $LOGIN_ID ){
				return false;
			}
			Template::setOwner( 1 );
		}
		return true;
	}

	static function checkTableEditUser( $type, &$db, &$rec ){
		global $THIS_TABLE_EDIT_USER;
		global $loginUserType;

		if( $loginUserType == 'admin' ){ return true; }

		if( isset($THIS_TABLE_EDIT_USER[ $type ]) && array_search( $loginUserType, $THIS_TABLE_EDIT_USER[ $type ] ) !== FALSE ){
			return self::checkTableOwner( $type, $db, $rec );
		}
		return false;
	}

	static function checkTableRegistUser( $type ){
		global $THIS_TABLE_REGIST_USER;
		global $loginUserType;

		if( $loginUserType == 'admin' ){ return true; }

		if( isset($THIS_TABLE_REGIST_USER[ $type ]) && array_search( $loginUserType, $THIS_TABLE_REGIST_USER[ $type ] ) !== FALSE ){
			return true;
		}
		return false;
	}

	static function checkTableRegistCount( $type )
	{
		global $THIS_TABLE_MAX_REGIST;
		global $THIS_TABLE_OWNER_COLUM;
		global $loginUserType;
		global $LOGIN_ID;

		if( 'admin' == $loginUserType ) //�Ǘ��҂̏ꍇ
			{ return false; }

		if( !isset( $THIS_TABLE_MAX_REGIST[ $type ][ $loginUserType ] ) ) //����ݒ肪��̏ꍇ
			{ return false; }

		if( !isset( $THIS_TABLE_OWNER_COLUM[ $type ][ $loginUserType ] ) ) //�I�[�i�[�ݒ肪��̏ꍇ
			{ return false; }

		$db    = GMList::getDB( $type );
		$table = $db->getTable();
		$table = $db->searchTable( $table , $THIS_TABLE_OWNER_COLUM[ $type ][ $loginUserType ] , '=' , $LOGIN_ID );
		$row   = $db->getRow( $table );

		$isOver = ( $row >= $THIS_TABLE_MAX_REGIST[ $type ][ $loginUserType ] );

		if( 1 == $THIS_TABLE_MAX_REGIST[ $type ][ $loginUserType ] && $isOver ) //���1���Ő����ς݂������ꍇ
		{
			$rec = $db->getRecord( $table , 0 );

			return $db->getData( $rec , 'id' );
		}

		return $isOver;
	}

	static function checkAdminUser($type){
		global $THIS_TABLE_ACCESS_ADMIN_USER;
		global $loginUserType;

		if( $loginUserType == 'admin' ){ return true; }
		if( $THIS_TABLE_ACCESS_ADMIN_USER[ $type ]){
			if(Component::Logic('nUser')->isAdmin()){
				return true;
			}
		}else{
			return true;
		}
		return false;

	}

	static function getAuthenticityToken(){

		if( is_null(self::$nextAuthenticityToken)){
            $tokenName = md5(uniqid(mt_rand(), true));
            $authenticity_token = $tokenName;
            $_SESSION['authenticity_token_' . $tokenName ] = $authenticity_token;
            self::$nextAuthenticityToken = $authenticity_token;
		}else{
            $authenticity_token = self::$nextAuthenticityToken;
		}

		return $authenticity_token;
	}

	static function checkAuthenticityToken( $authenticity_token ){

		$tokenName = $_POST[ 'authenticity_token' ];

		if( is_null($authenticity_token) ){ return false; }

		$old_authenticity_token = $_SESSION['authenticity_token_' . $tokenName ];
		unset($_SESSION['authenticity_token_' . $tokenName ]);
		return $old_authenticity_token == $authenticity_token;
	}

	static function detect_encoding_ja( $str )
	{
	    $enc = @mb_detect_encoding( $str, 'ASCII,JIS,eucJP-win,SJIS-win,UTF-8' );

	    switch ( $enc ) {
	    case FALSE   :
	    case 'ASCII' :
	    case 'JIS'   :
	    case 'UTF-8' : break;
	    case 'eucJP-win' :
	        // ������ eucJP-win �����o�����ꍇ�AeucJP-win �Ƃ��Ĕ���
	        if ( @mb_detect_encoding( $str, 'SJIS-win,UTF-8,eucJP-win' ) === 'eucJP-win' ) {
	            break;
	        }
	        $_hint = "\xbf\xfd" . $str; // "\xbf\xfd" : EUC-JP "��"

	        // EUC-JP -> UTF-8 �ϊ����Ƀ}�b�s���O���ύX����镶�����폜( �� �� �� �Ȃ�)
	        mb_regex_encoding( 'EUC-JP' );
	        $_hint = mb_ereg_replace( "\xad(?:\xe2|\xf5|\xf6|\xf7|\xfa|\xfb|\xfc|\xf0|\xf1|\xf2)" , '', $_hint );

	        $_tmp  = mb_convert_encoding( $_hint, 'UTF-8', 'eucJP-win' );
	        $_tmp2 = mb_convert_encoding( $_tmp,  'eucJP-win', 'UTF-8' );
	        if ( $_tmp2 === $_hint ) {

	            // ��O����( EUC-JP �ȊO�ƔF������͈� )
	            if (
	                // SJIS �Əd�Ȃ�͈�(2�o�C�g|3�o�C�g|i���[�h�G����|1�o�C�g����)
	                ! preg_match( '/^(?:'
	                    . '[\x8E\xE0-\xE9][\x80-\xFC]|\xEA[\x80-\xA4]|'
	                    . '\x8F[\xB0-\xEF][\xE0-\xEF][\x40-\x7F]|'
	                    . '\xF8[\x9F-\xFC]|\xF9[\x40-\x49\x50-\x52\x55-\x57\x5B-\x5E\x72-\x7E\x80-\xB0\xB1-\xFC]|'
	                    . '[\x00-\x7E]'
	                    . ')+$/', $str ) &&

	                // UTF-8 �Əd�Ȃ�͈�(�S�p�p����|����|1�o�C�g����)
	                ! preg_match( '/^(?:'
	                    . '\xEF\xBC[\xA1-\xBA]|[\x00-\x7E]|'
	                    . '[\xE4-\xE9][\x8E-\x8F\xA1-\xBF][\x8F\xA0-\xEF]|'
	                    . '[\x00-\x7E]'
	                    . ')+$/', $str )
	            ) {
	                // �������͈̔͂ɓ���Ȃ������ꍇ�́AeucJP-win �Ƃ��Č��o
	                break;
	            }
	            // ��O����2(�ꕔ�̕p�x�̑������ȏn��� eucJP-win �Ƃ��Ĕ���)
	            // (����|����|����|��|����|����|�N��|����|�K�N|��x)
	            if ( mb_ereg( '^(?:'
	                . '\xE0\xDD\xE0\xEA|\xE0\xE8\xE0\xE1|\xE0\xF5\xE0\xEF|\xE1\xF2\xE1\xFB|'
	                . '\xE2\xFB\xE2\xF5|\xE6\xCE\xE2\xF1|\xE7\xAF\xE6\xF9|\xE8\xE7\xE8\xEA|'
	                . '\xE9\xAC\xE9\xAF|\xE9\xF1\xE9\xD9|[\x00-\x7E]'
	                . ')+$', $str )
	            ) {
	                break;
	            }
	        }

	    default :
	        // ������ SJIS-win �Ɣ��f���ꂽ�ꍇ�́A�����R�[�h�� SJIS-win �Ƃ��Ĕ���
	        $enc = @mb_detect_encoding( $str, 'UTF-8,SJIS-win' );
	        if ( $enc === 'SJIS-win' ) {
	            break;
	        }
	        // �f�t�H���g�Ƃ��� SJIS-win ��ݒ�
	        $enc   = 'SJIS-win';

	        $_hint = "\xe9\x9b\x80" . $str; // "\xe9\x9b\x80" : UTF-8 "��"

	        // �ϊ����Ƀ}�b�s���O���ύX����镶���𒲐�
	        mb_regex_encoding( 'UTF-8' );
	        $_hint = mb_ereg_replace( "\xe3\x80\x9c", "\xef\xbd\x9e", $_hint );
	        $_hint = mb_ereg_replace( "\xe2\x88\x92", "\xe3\x83\xbc", $_hint );
	        $_hint = mb_ereg_replace( "\xe2\x80\x96", "\xe2\x88\xa5", $_hint );

	        $_tmp  = mb_convert_encoding( $_hint, 'SJIS-win', 'UTF-8' );
	        $_tmp2 = mb_convert_encoding( $_tmp,  'UTF-8', 'SJIS-win' );

	        if ( $_tmp2 === $_hint ) {
	            $enc = 'UTF-8';
	        }
	        // UTF-8 �� SJIS 2�������d�Ȃ�͈͂ւ̑Ώ�(SJIS ��D��)
	        if ( preg_match( '/^(?:[\xE4-\xE9][\x80-\xBF][\x80-\x9F][\x00-\x7F])+/', $str ) ) {
	            $enc = 'SJIS-win';
	        }
	    }
	    return $enc;
	}



	/**
	 * �����̕����񂪐��l���Z�����ǂ�����Ԃ�
	 * @return boolean
	 */
	function is_expression( $str ){
		return (preg_match( '/[^\d\+\-\*\/\.%()]+/',$str ) === 0);
	}


	/**
	 * date�֐��̃}���`�o�C�g�Ή�(�b��
	 * @return boolean
	 */
	function mb_date($format, $time=null) {
		if(is_null($time)){ $time = time(); }
		$encoding = mb_internal_encoding();
		mb_internal_encoding('UTF-8');
		$format_utf8 = mb_convert_encoding($format,'UTF-8', $encoding);
		$result_utf8 = date($format_utf8, $time);
		$result = mb_convert_encoding($result_utf8, $encoding, 'UTF-8');
		mb_internal_encoding($encoding);
		return $result;
	}

	function fileWrite( $file_name , $html ){
		if(!$f = fopen($file_name,'w')){
			return;
		}
	
		if(fwrite($f,$html) === FALSE ){
			fclose($f);
			return;
		}
	
		fclose($f);
		chmod( $file_name, 0766 );
	}

	function fileRead( $file_name ){
		$html = file_get_contents($file_name);
		return $html;
	}

	function fileDelete($file_name){
		unlink($file_name);
	}

	function request( $url , $param = array() )
	{
		preg_match( '/^(\w+):\/\/([^\/]+)(.*)$/' , $url , $match );

		$protocol = $match[ 1 ];
		$host     = $match[ 2 ];
		$path     = $match[ 3 ];
		
		$fp = @fsockopen( $host , 80 , $errno , $errstr , 1 );

		if( !$fp )
			{ return false; }

		$param = http_build_query( $param , '' , '&' );

		$string  = 'POST ' . $url . ' HTTP/1.1' . "\r\n";
		$string .= 'HOST: ' . $host . "\r\n";
		$string .= 'User-Agent: PHP/' . phpversion() . "\r\n";
		$string .= 'Content-Type: application/x-www-form-urlencoded' . "\r\n";
		$string .= 'Content-Length: ' . strlen( $param ) . "\r\n";
		$string .= 'Connection: Close' . "\r\n\r\n";
		$string .= $param . "\r\n";

		fwrite( $fp , $string );
		fclose( $fp );

		return true;
	}

	static function VerifyImageExt( $path , $ext )
	{
		List( $width , $height , $type ) = getimagesize( $path );

		switch( $ext )
		{
			case 'gif':
				{ return IMAGETYPE_GIF == $type; }

			case 'jpg':
			case 'jpeg':
				{ return IMAGETYPE_JPEG == $type; }

			case 'png':
				{ return IMAGETYPE_PNG == $type; }

			case 'swf':
				{ return IMAGETYPE_SWF == $type; }

			case 'bmp':
				{ return IMAGETYPE_BMP == $type; }

			default :
				{ return false; }
		}
	}

    static $nextAuthenticityToken = null;
	static private $lock = Array();

    static function isWindows()
    {
        if (DIRECTORY_SEPARATOR == '\\') {
            return true;
        }
        return false;
    }
}


function addslashes_deep($value)
{
	$value = is_array($value) ?
	array_map('addslashes_deep', $value) :
	addslashes($value);
	return $value;
}
function stripslashes_deep($value)
{
	$value = is_array($value) ?
	array_map('stripslashes_deep', $value) :
	stripslashes($value);
	return $value;
}

function urldecode_deep($value)
{
	$value = is_array($value) ?
	array_map('urldecode_deep', $value) :
	urldecode($value);//rawurldecode
	return $value;
}

function h($str, $style = null, $charset = null) {
    global $SYSTEM_CHARACODE;

    if( is_null($style)){ $style = ENT_COMPAT | ENT_HTML401; }
    if( is_null($charset)){ $charset = $SYSTEM_CHARACODE; }

	return htmlspecialchars($str, $style, $charset);
}

class CleanGlobal
{
	private function escape($array)
	{
		$array = self::nullbyte($array);
		return $array;
	}

	private function nullbyte($array)
	{
		if(is_array($array)) return array_map( array('CleanGlobal', 'nullbyte'), $array );
		return str_replace( "\0", "", $array );
	}

	function action()
	{
		$_GET = self::escape($_GET);
		$_POST = self::escape($_POST);
		$_REQUEST = self::escape($_REQUEST);
		$_FILES = self::escape($_FILES);
		if(isset($_SESSION)) { $_SESSION = self::escape($_SESSION); }
		$_COOKIE = self::escape($_COOKIE);
	}
}


if(!function_exists('json_encode')){
	function json_encode($arr) {
		$json_str = "";
		if(is_array($arr)) {
			$pure_array = true;
			$array_length = count($arr);
			for($i=0;$i<$array_length;$i++) {
				if(! isset($arr[$i])) {
					$pure_array = false;
					break;
				}
			}
			if($pure_array) {
				$json_str ="[";
				$temp = array();
				for($i=0;$i<$array_length;$i++) {
					$temp[] = sprintf("%s", json_encode($arr[$i]));
				}
				$json_str .= implode(",",$temp);
				$json_str .="]";
			} else {
				$json_str ="{";
				$temp = array();
				foreach($arr as $key => $value) {
					$temp[] = sprintf("\"%s\":%s", $key, json_encode($value));
				}
				$json_str .= implode(",",$temp);
				$json_str .="}";
			}
		} else {
			if(is_string($arr)) {
				$json_str = "\"". json_encode_string($arr) . "\"";
			} else if(is_numeric($arr)) {
				$json_str = $arr;
			} else {
				$json_str = "\"". json_encode_string($arr) . "\"";
			}
		}
		return $json_str;
	}
	function json_encode_string($in_str) {
		$in_str = str_replace('\\','\\\\',$in_str);
		$in_str = str_replace("\n",'\\n',$in_str);
		$in_str = str_replace('"','\\"',$in_str);
		mb_internal_encoding("SJIS");
		$convmap = array(0x80, 0xFFFF, 0, 0xFFFF);
		$str = "";
		for($i=mb_strlen($in_str)-1; $i>=0; $i--) {
			$mb_char = mb_substr($in_str, $i, 1);
			if(mb_ereg("&#(\\d+);", mb_encode_numericentity($mb_char, $convmap, "UTF-8"), $match)) {
				$str = sprintf("\\u%04x", $match[1]) . $str;
			} else {
				$str = $mb_char . $str;
			}
		}
		return $str;
	}

}

if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);

    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

