<?php

/**
 * �V�X�e���R�[���N���X
 *
 * @author �O�H��q
 * @version 1.0.0
 *
 */
class SystemBase extends command_base
{
	/**********************************************************************************************************
	 * �ėp�V�X�e���p���\�b�h
	 **********************************************************************************************************/

	// �A�b�v���[�h�t�@�C���̊i�[�t�H���_�w��
	// ext�Ŋg���q�ijpg���jcat�Ŏ�ށiimage���j�A���̑�timeformat���w��\�B�����K�w�̏ꍇ��/�ŋ�؂�B
	var $fileDir = 'cat/Ym'; // �L�q��) cat/ext/Y/md -> �i�[�t�H���_ image/jpg/2009/1225

	//getHead��getFoot�̌Ăяo���Ǘ�
	static $head = false;
	static $foot = false;

	static $title = "";
	static $description = "";
	static $keywords = "";

	static $ogTitle       = "";
	static $ogType        = "";
	static $ogDescription = "";
	static $ogURL         = "";
	static $ogImage       = "";

	static $CallMode = 'normal';

	static $ValidateColumnCache = Array();

	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	// �w�b�_�[�֌W
	/////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * �^�C�g�����o�́B
	 * ����̏����ŏo�͓��e��ύX�������ꍇ��$buffer�ɂ��̓��e���w�肵�Ă�������
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�ł��B
	 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
	 * @param args �R�}���h�R�����g�����z��ł��B�@��������ID��n���܂��B �������Ƀ����N���邩��^�U�l�œn���܂��B
	 */
	function drawTitle( &$gm, $rec, $args )
	{
		$buffer = SystemUtil::getSystemData('site_title');
		if( strpos($_SERVER['REQUEST_URI'], "info.php") !== false)
		{// �ڍ׃y�[�W�̏ꍇ

		}
		else if( strpos($_SERVER['REQUEST_URI'], "search.php") !== false)
		{// �����y�[�W�̏ꍇ

		}

		$this->addBuffer( $buffer );
	}


	/**
	 * �������o�́B
	 * ����̏����ŏo�͓��e��ύX�������ꍇ��$buffer�ɂ��̓��e���w�肵�Ă�������
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�ł��B
	 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
	 * @param args �R�}���h�R�����g�����z��ł��B�@��������ID��n���܂��B �������Ƀ����N���邩��^�U�l�œn���܂��B
	 */
	function drawDescription( &$gm, $rec, $args )
	{
		$buffer = SystemUtil::getSystemData('description');
		if( strpos($_SERVER['REQUEST_URI'], "info.php") !== false)
		{// �ڍ׃y�[�W�̏ꍇ

		}
		else if( strpos($_SERVER['REQUEST_URI'], "search.php") !== false)
		{// �����y�[�W�̏ꍇ

		}

		$this->addBuffer( $buffer );
	}


	/**
	 * �L�[���[�h���o�́B
	 * ����̏����ŏo�͓��e��ύX�������ꍇ��$buffer�ɂ��̓��e���w�肵�Ă�������
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�ł��B
	 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
	 * @param args �R�}���h�R�����g�����z��ł��B�@��������ID��n���܂��B �������Ƀ����N���邩��^�U�l�œn���܂��B
	 */
	function drawKeywords( &$gm, $rec, $args )
	{
		$buffer = SystemUtil::getSystemData('keywords');
		if( strpos($_SERVER['REQUEST_URI'], "info.php") !== false)
		{// �ڍ׃y�[�W�̏ꍇ

		}
		else if( strpos($_SERVER['REQUEST_URI'], "search.php") !== false)
		{// �����y�[�W�̏ꍇ

		}

		$this->addBuffer( $buffer );
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	// �o�^�֌W
	/////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * �o�^���e�m�F�B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param edit �ҏW�Ȃ̂��A�V�K�ǉ��Ȃ̂���^�U�l�œn���B
	 * @return �G���[�����邩��^�U�l�œn���B
	 */
	function registCheck( &$gm, $edit, $loginUserType, $loginUserRank )
	{
		// �`�F�b�N����
		self::$checkData->generalCheck($edit);
		$data = self::$checkData->getData();

		// �G���[���e�擾
		return self::$checkData->getCheck();
	}

	/**
	 * �����o�^�����m�F�B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param edit �ҏW�Ȃ̂��A�V�K�ǉ��Ȃ̂���^�U�l�œn���B
	 * @return �����o�^���\����^�U�l�ŕԂ��B
	 */
	function copyCheck( &$gm, $loginUserType, $loginUserRank )
	{
		// �Ǘ��҂͑S�Ė������ɋ���
		if( 'admin' == $loginUserType )
		return true;

		switch( $_GET[ 'type' ] )
		{
		default :
			return false;
		}
	}

	/**
	 * �폜���e�m�F�B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �t�H�[���̂���̓��̓f�[�^�𔽉f�������R�[�h�f�[�^�B
	 * @return �G���[�����邩��^�U�l�œn���B
	 */
	function deleteCheck(&$gm, &$rec, $loginUserType, $loginUserRank )
	{
		self::$checkData->deleteCheck();

		return self::$checkData->getCheck();
	}

	/**
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �t�H�[���̂���̓��̓f�[�^�𔽉f�������R�[�h�f�[�^�B
	 * @param edit �ҏW�Ȃ̂��A�V�K�ǉ��Ȃ̂���^�U�l�œn���B
	 * @return �G���[�����邩��^�U�l�œn���B
	 */
	function registCompCheck( &$gm, &$rec, $loginUserType, $loginUserRank ,$edit=false)
	{
		// �`�F�b�N����
		$check			 = true;
		$db	 = $gm[ $_GET['type'] ]->getDB();

		if(!$edit){
			//�d���o�^�`�F�b�N
			$table	 = $db->searchTable(  $db->getTable(), 'id', '=', $db->getData( $rec, 'id' )  );
			if($db->existsRow($table)){
				self::$checkData->addError('duplication_id');
			}
		}else{
			if( $_POST['id'] != $_GET['id'] ){
				return false;
			}
		}

		if( $edit )
		{
			//Const/AdminData/MailDup�̃`�F�b�N
			$options = $gm[ $_GET[ 'type' ] ]->colEdit;

			foreach( $options as $column => $validates )
			{
				$validates = explode( '/' , $validates );

				if( in_array( 'Const' , $validates ) )
					self::$checkData->checkConst( $column , null );

				if( in_array( 'AdminData' , $validates ) )
					self::$checkData->checkAdminData( $column , null );

				if( in_array( 'MailDup' , $validates ) )
					self::$checkData->checkMailDup( $column , null );
			}
		}

		// �ŗL�̃`�F�b�N����
/*		switch( $_GET['type'] )
		{
		case 'xxxx':
			break;
		}
*/
		return self::$checkData->getCheck();
	}

	/**
	 * �o�^�O�i�K�����B
	 * �t�H�[�����͈ȊO�̕��@�Ńf�[�^��o�^����ꍇ�́A�����Ń��R�[�h�ɒl�������܂��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �t�H�[���̂���̓��̓f�[�^�𔽉f�������R�[�h�f�[�^�B
	 */
	function registProc( &$gm, &$rec, $loginUserType, $loginUserRank ,$check = false)
	{
		global $THIS_TABLE_OWNER_COLUM;
		global $LOGIN_ID;

		$db	 = $gm[ $_GET['type'] ]->getDB();

		// ID�Ɠo�^���Ԃ��L�^�B
		$db->setData( $rec, 'id',	  SystemUtil::getNewId( $db, $_GET['type']) );
		$db->setData( $rec, 'regist', time() );

		if( in_array( 'update_time' , $db->colName ) )
			{ $db->setData( $rec, 'update_time', time() ); }

		if( isset( $THIS_TABLE_OWNER_COLUM[ $_GET[ 'type' ] ] ) )
		{
			foreach( $THIS_TABLE_OWNER_COLUM[ $_GET[ 'type' ] ] as $type => $column )
			{
				if( $loginUserType != $type )
					{ continue; }

				if( in_array( $column , $db->colName ) )
					{ $db->setData( $rec, $column, $LOGIN_ID ); }
			}
		}

		// �ŗL�̃`�F�b�N����
/*		switch( $_GET['type'] )
		{
		case 'xxxx':
			break;
		}
*/

		if(!$check) { $this->uplodeComp($gm,$db,$rec); } // �t�@�C���̃A�b�v���[�h��������
	}

	/**
	 * �o�^�������������B
	 * �o�^�������Ƀ��[���œ��e��ʒm�������ꍇ�Ȃǂɗp���܂��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec ���R�[�h�f�[�^�B
	 */
	function registComp( &$gm, &$rec, $loginUserType, $loginUserRank )
	{
	}



	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	// �ҏW�֌W
	/////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * �ҏW�O�i�K�����B
	 * �t�H�[�����͈ȊO�̕��@�Ńf�[�^��o�^����ꍇ�́A�����Ń��R�[�h�ɒl�������܂��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �t�H�[���̂���̓��̓f�[�^�𔽉f�������R�[�h�f�[�^�B
	 */
	function editProc( &$gm, &$rec, $loginUserType, $loginUserRank ,$check = false)
	{
		$db	 = $gm[ $_GET['type'] ]->getDB();

		if( in_array( 'update_time' , $db->colName ) )
			{ $db->setData( $rec, 'update_time', time() ); }

		if(!$check) { $this->uplodeComp($gm,$db,$rec); } // �t�@�C���̃A�b�v���[�h��������
	}

	/**
	 * �ҏW���������B
	 * �t�H�[�����͈ȊO�̕��@�Ńf�[�^��o�^����ꍇ�́A�����Ń��R�[�h�ɒl�������܂��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �t�H�[���̂���̓��̓f�[�^�𔽉f�������R�[�h�f�[�^�B
	 */
	function editComp( &$gm, &$rec, &$old_rec, $loginUserType, $loginUserRank )
	{
		//$this->doFileDelete( $gm, $rec, $old_rec );
	}


	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	// �폜�֌W
	/////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * �폜�����B
	 * �폜�����s����O�Ɏ��s����������������΁A�����ɋL�q���܂��B
	 * �Ⴆ�΃��[�U�f�[�^���폜����ۂɃ��[�U�f�[�^�ɕR�t����ꂽ�f�[�^���폜����ۂȂǂɗL���ł��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �t�H�[���̂���̓��̓f�[�^�𔽉f�������R�[�h�f�[�^�B
	 */
	function deleteProc( &$gm, &$rec, $loginUserType, $loginUserRank )
	{
		$db		 = $gm[ $_GET['type'] ]->getDB();

		// �ŗL�̃`�F�b�N����
/*		switch( $_GET['type'] )
		{
		case 'xxxx':
			break;
		}
*/

		// �폜���s����
		switch( $_GET['type'] )
		{
		default:
			// ���R�[�h���폜���܂��B
			$db->deleteRecord( $rec );
			break;
		}

	}



	/**
	 * �폜���������B
	 * �o�^�폜�������Ɏ��s����������������΃R�R�ɋL�q���܂��B
	 * �폜�������[���𑗐M�������ꍇ�Ȃǂɗ��p���܂��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �t�H�[���̂���̓��̓f�[�^�𔽉f�������R�[�h�f�[�^�B
	 */
	function deleteComp( &$gm, &$rec, $loginUserType, $loginUserRank )
	{
		// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
		global $LOGIN_ID;
		global $DELETE_FILE_AUTO;
		// **************************************************************************************

		$db = $gm[$_GET['type']]->getDB();
		if( $_GET['type'] == $loginUserType && $LOGIN_ID == $db->getData( $rec , 'id' ) ){
			SystemUtil::logout($loginUserType);
		}

		if( $DELETE_FILE_AUTO ){
			$this->doFileDelete( $gm, $rec );
		}
	}

	function FeedProc( $table = null )
	{
		global $CONF_FEED_ENABLE;
		global $CONF_FEED_TABLES;
		global $CONF_FEED_MAX_ROW;
		global $CONF_FEED_OUTPUT_DIR;
		global $FileBase;

		if( $CONF_FEED_ENABLE && in_array( $_GET[ 'type' ] , $CONF_FEED_TABLES ) )
		{
			$gm = GMList::getGM( $_GET[ 'type' ] );
			$db = $gm->getDB();

			if( !$table )
			{
				$table = $db->getTable();
				$table = $db->sortTable( $table , 'shadow_id' , 'desc' );
				$table = $db->limitOffset( $table , 0 , $CONF_FEED_MAX_ROW );
			}

			$row = $db->getRow( $table );

			if( $CONF_FEED_MAX_ROW < $row )
			{
				$table = $db->limitOffset( $table , 0 , $CONF_FEED_MAX_ROW );
				$row   = $db->getRow( $table );
			}

			foreach( Array( Array( 'label' => 'FEED_RSS_DESIGN' , 'name' => '_rss.xml' ) , Array( 'label' => 'FEED_ATOM_DESIGN' , 'name' => '_atom.xml' ) ) as $config )
			{
				$template = Template::getTemplate( 'nobody' , 1 , $_GET[ 'type' ] , $config[ 'label' ] );

				if( !$template )
					{ continue; }

				$fp = fopen( $CONF_FEED_OUTPUT_DIR . $_GET[ 'type' ] . $config[ 'name' ] , 'wb' );

				if( $fp )
				{
					fputs( $fp , $gm->getString( $template , null , 'head' ) );

					for( $i = 0 ; $row > $i ; ++$i )
					{
						$rec     = $db->getRecord( $table , $i );
						fputs( $fp , $gm->getString( $template , $rec , 'list' ) );
					}

					fputs( $fp , $gm->getString( $template , null , 'foot' ) );

					fclose( $fp );
				}

				$file = $CONF_FEED_OUTPUT_DIR . $_GET[ 'type' ] . $config[ 'name' ];
				$FileBase->upload($file,$file);
			}
		}
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	// �����֌W
	/////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * �����O�����B
	 * �������������������s�O�ɕύX�������ꍇ�ɗ��p���܂��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param sr �����p�����[�^���Z�b�g�ς݂�Search�I�u�W�F�N�g
	 */
	function searchResultProc( &$gm, &$sr, $loginUserType, $loginUserRank )
	{
		$type = SearchTableStack::getType();
		$db = $gm[ $type ]->getDB();

		// �ŗL�̃`�F�b�N����
/*		switch( $_GET['type'] )
		{
		case 'xxxx':
			break;
		}
*/
	}

	/**
	 * ���������B
	 * �t�H�[�����͈ȊO�̕��@�Ō���������ݒ肵�����ꍇ�ɗ��p���܂��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param table �t�H�[���̂���̓��͓��e�Ɉ�v���郌�R�[�h���i�[�����e�[�u���f�[�^�B
	 */
	function searchProc( &$gm, &$table, $loginUserType, $loginUserRank )
	{
		$type = SearchTableStack::getType();

		$db = $gm[ $type ]->getDB();

		// �ŗL�̃`�F�b�N����
/*		switch( $_GET['type'] )
		{
		case 'xxxx':
			break;
		}
*/
	}


	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	// �ڍ׏��֌W
	/////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * �ڍ׏�񂪉{�����ꂽ�Ƃ��ɕ\�����ėǂ���񂩂�Ԃ����\�b�h�B
	 * activate�J��������J�ۃt���O�Aregist��update���ɂ��\�����Ԃ̐ݒ�A�A�N�Z�X�����ɂ��t�B���^�Ȃǂ��s���܂��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �A�N�Z�X���ꂽ���R�[�h�f�[�^�B
	 * @return �\�����ėǂ����ǂ�����^�U�l�œn���B
	 */
	function infoCheck( &$gm, &$rec, $loginUserType, $loginUserRank )
	{
		$db	     = $gm[ $_GET['type'] ]->getDB();
		$isOwner = false;

		if( WS_SYSTEM_AUTO_INFO_CHECK_SEARCHABLE ) //�����\���`�F�b�N���L���ȏꍇ
		{
			if( 'admin' != $loginUserType ) //�Ǘ��҈ȊO�̃��[�U�[�̏ꍇ
				{ $isOwner = SystemUtil::checkTableOwner( $_GET[ 'type' ] , $db , $rec ); }

			if( !$isOwner ) //���݂̃��[�U�[�����̃f�[�^�̃I�[�i�[�ł͂Ȃ��ꍇ
			{
				$table = SystemUtil::getSearchResult( Array( 'type' => $_GET[ 'type' ] , 'id' => $_GET[ 'id' ] , 'id_PAL' => Array( 'match comp' ) ) );
				$table = $db->limitOffset( $table , 0 , 1 );

				if( !$db->getRow( $table ) )
					{ return false; }
			}
		}

		// �ŗL�̃`�F�b�N����
/*		switch( $_GET['type'] )
		{
		case 'xxxx':
			break;
		}
*/
		return true;
	}

	/**
	 * �ڍ׏�񂪉{�����ꂽ�Ƃ��ɌĂяo����鏈���B
	 * ���ɑ΂���A�N�Z�X���O����肽���Ƃ��ȂǂɗL�p�ł��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �A�N�Z�X���ꂽ���R�[�h�f�[�^�B
	 */
	function doInfo( &$gm, &$rec, $loginUserType, $loginUserRank )
	{
	}

	/**
	 * �ڍ׏��O�����B
	 * �ȈՏ��ύX�ŗ��p
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �A�N�Z�X���ꂽ���R�[�h�f�[�^�B
	 */
	function infoProc( &$gm, &$rec, $loginUserType, $loginUserRank )
	{

		// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
		global $PROGRESS_BEGIN;
		// **************************************************************************************

		// �ȈՏ��ύX�i���y�[�W����̓��e�ύX�����j
		if(  isset( $_POST['post'] ) )
		{
			// �ŗL�̃`�F�b�N����
/*			switch( $_GET['type'] )
			{
			case 'xxxx':
				break;
			}
*/
		}
	}



	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	//   �A�N�e�B�x�[�g�֌W
	/////////////////////////////////////////////////////////////////////////////////////////////////////////

	//activate����y�уA�N�e�B�x�[�g��������
	function activateAction( &$gm, &$rec, $loginUserType, $loginUserRank )
	{
		// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
		global $ACTIVE_NONE;
		global $ACTIVE_ACTIVATE;
		// **************************************************************************************

		$db = $gm[ $_GET['type'] ]->getDB();

		if(  $db->getData( $rec, 'activate' ) == $ACTIVE_NONE  )
		{
			$db->setData( $rec, 'activate', $ACTIVE_ACTIVATE );
			$db->updateRecord( $rec );

			MailLogic::userRegistComp( $rec, $_GET['type'] );

			return true;
		}

		return false;
	}


	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	//   ���O�C���֌W
	/////////////////////////////////////////////////////////////////////////////////////////////////////////

	//���O�A�E�g���ԏ���
	//�Ԃ�l��false�ɂ���ƃ��O�A�E�g�����~�����
	function logoutProc( $loginUserType ){

		if( $_SESSION['ADMIN_MODE'] ){
			unset($_SESSION['ADMIN_MODE']);
		}

		return true;
	}

	//���O�C�����ԏ���
	//�Ԃ�l��false�ɂ���ƃ��O�C�������~�����
	function loginProc( $check , &$loginType , &$id ){
		// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
		global $gm;
		global $LOGIN_ID;
		global $loginUserType;
		// **************************************************************************************

		if( $loginUserType == 'admin' && isset($_GET['type']) ){
			$loginType = $_GET['type'];
			$id	= $_GET['id'];
			$_SESSION['ADMIN_MODE'] = true;
			return true;
		}

		if( $_SESSION['ADMIN_MODE'] ){
			$loginType = 'admin';
			$id	= 'ADMIN';
			unset($_SESSION['ADMIN_MODE']);
			return true;
		}

		//false���X���[
		if(!$check){return $check;}

		//���O�C���Ώۂɂ���ĕ��򂷂�ꍇ�A�����ɋL�q����
/*		switch( $_GET['type'] )
		{
		case 'xxxx':
			break;
		}
*/

		return true;
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	//   �����֌W
	/////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**
	 * �����`�F�b�N�����B
	 * �����`�F�b�N���Ɏ��s����������������΃R�R�ɋL�q���܂��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �t�H�[���̂���̓��̓f�[�^�𔽉f�������R�[�h�f�[�^�B
	 */
	function restoreCheck( &$gm, &$rec, $loginUserType, $loginUserRank )
	{
		global $THIS_TABLE_IS_USERDATA;
		global $LOGIN_KEY_FORM_NAME;

		if( $THIS_TABLE_IS_USERDATA[ $_GET[ 'type' ] ] )
		{
			$db    = GMList::getDB( $_GET[ 'type' ] );
			$table = $db->getTable();
			$table = $db->searchTable( $table , $LOGIN_KEY_FORM_NAME , '=' , $db->getData( $rec , $LOGIN_KEY_FORM_NAME ) );
			$table = $db->limitOffset( $table , 0 , 1 );

			if( $db->getRow( $table ) )
				{ return false; }
		}

		return true;
	}

	/**
	 * �������������B
	 * �����������Ɏ��s����������������΃R�R�ɋL�q���܂��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �t�H�[���̂���̓��̓f�[�^�𔽉f�������R�[�h�f�[�^�B
	 */
	function restoreComp( &$gm, &$rec, &$old_rec, $loginUserType, $loginUserRank )
	{
	}


	/**********************************************************************************************************
	 * �ėp�V�X�e���`��n�p���\�b�h
	 **********************************************************************************************************/

	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	// �o�^�֌W
	/////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * �o�^�t�H�[����`�悷��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function drawRegistForm( &$gm, $rec, $loginUserType, $loginUserRank )
	{
		$this->setErrorMessage($gm[ $_GET['type'] ]);

		switch(  $_GET['type']  )
		{
			default:
				// �ėp����
				if($gm[$_GET['type']]->maxStep >= 2)
				Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'REGIST_FORM_PAGE_DESIGN' . $_POST['step'] , SystemUtil::GetFormTarget( 'registForm' ) );
				else
				Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'REGIST_FORM_PAGE_DESIGN' , SystemUtil::GetFormTarget( 'registForm' ) );

		}


	}



	/**
	 * �o�^���e�m�F�y�[�W��`�悷��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �o�^�����i�[�������R�[�h�f�[�^
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function drawRegistCheck( &$gm, $rec, $loginUserType, $loginUserRank )
	{
		switch(  $_GET['type']  )
		{
			default:
				// �ėp����
				Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'REGIST_CHECK_PAGE_DESIGN' , SystemUtil::GetFormTarget( 'registCheck' ) );
		}
	}



	/**
	 * �o�^�����y�[�W��`�悷��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �o�^�����i�[�������R�[�h�f�[�^
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function drawRegistComp( &$gm, $rec, $loginUserType, $loginUserRank )
	{
		Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'REGIST_COMP_PAGE_DESIGN' );
	}



	/**
	 * �o�^���s��ʂ�`�悷��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function drawRegistFaled( &$gm, $loginUserType, $loginUserRank )
	{
		$this->setErrorMessage($gm[ $_GET['type'] ]);

		Template::drawTemplate( $gm[ $_GET['type'] ] , null ,'' , $loginUserRank , '' , 'REGIST_FALED_DESIGN' );
	}


	/**
	 * �o�^��������K����ʂ�`�悷��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function drawRegistMaxCountOver( &$gm, $loginUserType, $loginUserRank )
	{
		$this->setErrorMessage($gm[ $_GET['type'] ]);

		switch(  $_GET['type']  )
		{
			default:
				// �ėp����
				Template::drawTemplate( $gm[ $_GET['type'] ] , null ,$loginUserType , $loginUserRank , $_GET['type'] , 'REGIST_MAX_COUNT_OVER_DESIGN' );
		}
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	// �ҏW�֌W
	/////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * �ҏW�t�H�[����`�悷��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �ҏW�Ώۂ̃��R�[�h�f�[�^
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function drawEditForm( &$gm, &$rec, $loginUserType, $loginUserRank )
	{
		$this->setErrorMessage( $gm[ $_GET['type'] ] );

		switch(  $_GET['type']  )
		{
			default:
				// �ėp����
				Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'EDIT_FORM_PAGE_DESIGN' , SystemUtil::GetFormTarget( 'editForm' ), Template::getOwner() );
		}

	}

	/**
	 * �ҏW���e�m�F�y�[�W��`�悷��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �ҏW�Ώۂ̃��R�[�h�f�[�^
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function drawEditCheck( &$gm, &$rec, $loginUserType, $loginUserRank )
	{
		switch(  $_GET['type']  )
		{
			default:
				// �ėp����
				Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'EDIT_CHECK_PAGE_DESIGN' , SystemUtil::GetFormTarget( 'editCheck' ), Template::getOwner() );
		}
	}

	/**
	 * �ҏW�����y�[�W��`�悷��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �ҏW�Ώۂ̃��R�[�h�f�[�^
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function drawEditComp( &$gm, &$rec, $loginUserType, $loginUserRank )
	{
		switch(  $_GET['type']  )
		{
			default:
				// �ėp����
				Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'EDIT_COMP_PAGE_DESIGN' , false, Template::getOwner() );
		}
	}

	/**
	 * �ҏW���s��ʂ�`�悷��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function drawEditFaled( &$gm, $loginUserType, $loginUserRank )
	{
		header( 'HTTP/1.0 400 Bad Request' );
		Template::drawErrorTemplate();
	}


	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	// �폜�֌W
	/////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * �폜�ҏW�t�H�[����`�悷��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �ҏW�Ώۂ̃��R�[�h�f�[�^
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function drawDeleteForm( &$gm, &$rec, $loginUserType, $loginUserRank )
	{
		// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
		// **************************************************************************************

		$this->setErrorMessage($gm[ $_GET['type'] ]);

		switch(  $_GET['type']  )
		{
			default:
				// �ėp����
				Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'DELETE_FORM_PAGE_DESIGN' , SystemUtil::GetFormTarget( 'deleteForm' ), Template::getOwner() );
		}
	}

	/**
	 * �폜�m�F�y�[�W��`�悷��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �ҏW�Ώۂ̃��R�[�h�f�[�^
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function drawDeleteCheck( &$gm, &$rec, $loginUserType, $loginUserRank )
	{
		switch(  $_GET['type']  )
		{
			default:
				// �ėp����
				Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'DELETE_CHECK_PAGE_DESIGN' , SystemUtil::GetFormTarget( 'deleteCheck' ), Template::getOwner() );
		}
	}

	/**
	 * �폜�����y�[�W��`�悷��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �ҏW�Ώۂ̃��R�[�h�f�[�^
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function drawDeleteComp( &$gm, &$rec, $loginUserType, $loginUserRank )
	{
		switch(  $_GET['type']  )
		{
			default:
				// �ėp����
				Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'DELETE_COMP_PAGE_DESIGN', false, Template::getOwner() );
				break;
		}
	}


	/**
	 * �폜���s��ʂ�`�悷��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function drawDeleteFaled( &$gm, $loginUserType, $loginUserRank )
	{
		header( 'HTTP/1.0 400 Bad Request' );
		Template::drawErrorTemplate();
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	// �����֌W
	/////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * �����m�F�y�[�W��`�悷��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �ҏW�Ώۂ̃��R�[�h�f�[�^
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function drawRestoreCheck( &$gm, &$rec, $loginUserType, $loginUserRank )
	{
		switch(  $_GET['type']  )
		{
			default:
				// �ėp����
				Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'RESTORE_CHECK_PAGE_DESIGN' , SystemUtil::GetFormTarget( 'restoreForm' ) );
		}
	}



	/**
	 * ���������y�[�W��`�悷��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �ҏW�Ώۂ̃��R�[�h�f�[�^
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function drawRestoreComp( &$gm, &$rec, $loginUserType, $loginUserRank )
	{
		switch(  $_GET['type']  ){
			default:
				// �ėp����
				Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'RESTORE_COMP_PAGE_DESIGN'  );
				break;
		}
	}

	/**
	 * ������ʂ�`�悷��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function drawRestoreFaled( &$gm, $loginUserType, $loginUserRank )
	{
		header( 'HTTP/1.0 400 Bad Request' );
		Template::drawErrorTemplate();
	}


	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	// �����֌W
	/////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * �����t�H�[����`�悷��B
	 *
	 * @param sr Search�I�u�W�F�N�g�B
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function drawSearchForm( &$sr, $loginUserType, $loginUserRank )
	{
		$sr->addHiddenForm( 'type', $_GET['type'] );

		switch( $_GET['type'] )
		{
			default:
				$file = Template::getTemplate( $loginUserType , $loginUserRank , $_GET['type'] , self::ModifyTemplateLabel( 'SEARCH_FORM_PAGE_DESIGN' ) );

				if( !is_file( $file ) )
					{ Template::getTemplate( $loginUserType , $loginUserRank , $_GET['type'] , 'SEARCH_FORM_PAGE_DESIGN' ); }

				if( strlen( $file ) )	{ print $sr->getFormString( $file , 'search.php'  ); }
				else
				{
					header( 'HTTP/1.0 400 Bad Request' );
					Template::drawErrorTemplate();
				}
				break;
		}
	}

	function drawSearch( &$gm, &$sr, $table, $loginUserType, $loginUserRank ){
		SearchTableStack::pushStack($table);

		/*		�ꊇ���[�����M�փ��_�C���N�g		*/
		if( isset( $_GET[ 'multimail' ] ) ){
			$db		= $gm[ $_GET[ 'type' ] ]->getDB();
			$row	= $db->getRow( $table );

			for( $i=0 ; $i<$row ; $i++ ){
				$rec	 = $db->getRecord( $table, $i );
				$_GET['pal'][] = $db->getData( $rec, 'id' );
			}
			$_GET['type'] = 'multimail';

			if( is_array( $_GET[ 'pal' ] ) ){
				Header( 'Location: regist.php?type=multimail&pal[]=' . implode( '&pal[]=' , $_GET[ 'pal' ] ) );
			}else{
				Header( 'Location: regist.php?type=multimail' );
			}
		}else{
			$file = Template::getTemplate( $loginUserType , $loginUserRank , $_GET['type'] , self::ModifyTemplateLabel( 'SEARCH_RESULT_DESIGN' ) );

			if( !is_file( $file ) )
				{ $file = Template::getTemplate( $loginUserType , $loginUserRank , $_GET['type'] , 'SEARCH_RESULT_DESIGN' ); }

			if( strlen($file) ){
				$sr->addHiddenForm('type',$_GET['type']);
				print $sr->getFormString( $file , 'search.php' , null , 'v' );
			}else{
				header( 'HTTP/1.0 400 Bad Request' );
				Template::drawErrorTemplate();
			}
		}
	}

	/**
	 * �������ʁA�Y���Ȃ���`��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function drawSearchNotFound( &$gm, $loginUserType, $loginUserRank )
	{
		$file = Template::getTemplate( $loginUserType , $loginUserRank , $_GET['type'] , self::ModifyTemplateLabel( 'SEARCH_NOT_FOUND_DESIGN' ) );

		if( !is_file( $file ) )
			{ $file = Template::getTemplate( $loginUserType , $loginUserRank , $_GET['type'] , 'SEARCH_NOT_FOUND_DESIGN' ); }

		if( strlen($file) ){
			print $gm[ $_GET['type'] ]->getString( $file , null , null );
		}else{
			header( 'HTTP/1.0 400 Bad Request' );
			Template::drawErrorTemplate();
		}
	}

	/**
	 * �����G���[��`��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function drawSearchError( &$gm, $loginUserType, $loginUserRank )
	{
		header( 'HTTP/1.0 400 Bad Request' );
		Template::drawErrorTemplate();
	}


	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	// �ڍ׃y�[�W�֌W
	/////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * �ڍ׏��\���G���[��`��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function drawInfoError( &$gm, $loginUserType, $loginUserRank )
	{
		header( 'HTTP/1.0 403 Forbidden' );
		Template::drawErrorTemplate();
	}

	/**
	 * �ڍ׏��y�[�W��`�悷��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �ҏW�Ώۂ̃��R�[�h�f�[�^
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function drawInfo( &$gm, &$rec, $loginUserType, $loginUserRank )
	{
		switch(  $_GET['type']  )
		{
			default:
				// �ėp����
				$file = Template::getTemplate( $loginUserType , $loginUserRank , $_GET['type'] , self::ModifyTemplateLabel( 'INFO_PAGE_DESIGN' ), Template::getOwner() );

				if( !is_file( $file ) )
					{ $file = Template::getTemplate( $loginUserType , $loginUserRank , $_GET['type'] , 'INFO_PAGE_DESIGN' ); }

				print $gm[ $_GET['type'] ]->getFormString( $file , $rec , SystemUtil::GetFormTarget( 'infoPage' ) );
		}
	}

	/**
	 * �e���v���[�g�̎��s��ʂ�`�悷��B
	 *
	 * @param gm template��GUIManager
	 * @param error_name error��  �f�U�C���̃p�[�c��
	 */
	function getTemplateFaled( $gm, $lavel , $error_name  ){
		// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
		global $loginUserType;
		global $loginUserRank;
		// **************************************************************************************

		$h = Template::getTemplateString( $gm , null , $loginUserType, $loginUserRank , 'stemplate' , $lavel , null , null , 'head' );
		$h .=Template::getTemplateString( $gm , null , $loginUserType, $loginUserRank , 'stemplate' , $lavel , null , null , $error_name );
		$h .=Template::getTemplateString( $gm , null , $loginUserType, $loginUserRank , 'stemplate' , $lavel , null , null , 'foot' );
		return $h;
	}


	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	//   �A�N�e�B�x�[�g�֌W
	/////////////////////////////////////////////////////////////////////////////////////////////////////////

	function drawActivateComp( &$gm, &$rec, $loginUserType, $loginUserRank ){
		global $loginUserType;
		global $loginUserRank;

		$template = Template::getTemplate( $loginUserType , $loginUserRank , $_GET[ 'type' ] , 'ACTIVATE_DESIGN_HTML' );

		if( $template )
			{ $gm[ $_GET['type'] ]->draw( $template , $rec ); }
		else
			{ $gm[ $_GET['type'] ]->draw( Template::getLabelFile('ACTIVATE_DESIGN_HTML'), $rec ); }
	}

	function drawActivateFaled( &$gm, &$rec, $loginUserType, $loginUserRank ){
		global $loginUserType;
		global $loginUserRank;

		$template = Template::getTemplate( $loginUserType , $loginUserRank , $_GET[ 'type' ] , 'ACTIVATE_FALED_DESIGN_HTML' );

		if( $template )
			{ $gm[ $_GET['type'] ]->draw( $template , $rec ); }
		else
			{ $gm[ $_GET['type'] ]->draw( Template::getLabelFile('ACTIVATE_FALED_DESIGN_HTML'), $rec ); }
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	// �t�@�C���A�b�v���[�h�֌W
	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	

	/**
	 * �t�@�C���A�b�v���[�h���s��ꂽ�ꍇ�̈ꎞ�����B
	 *
	 * @param db Database�I�u�W�F�N�g
	 * @param rec ���R�[�h�f�[�^
	 * @param colname �A�b�v���[�h���s��ꂽ�J����
	 * @param file �t�@�C���z��
	 */
	function doFileUpload( &$db, &$rec, $colname, &$file )
	{
		global $FileBase;

		$delete_flag = isset($_POST[ $colname . '_DELETE' ]) && $_POST[ $colname . '_DELETE' ] == "true";
		if( $delete_flag )
		{
			if( isset( $_POST[ $colname.'_filetmp' ] ) ){
				// ���̎��_�� filetmp �𖳌������Ă��܂��ƁAcheck ����߂����ꍇ�ɏ��ł���ׁAfiletmp �͎c��
				global $gm;
				$gm[ $db->tablePlaneName ]->addHiddenForm( $colname.'_filetmp', $_POST[ $colname.'_filetmp' ] );
			}
		}

		if( !$delete_flag &&  $file[ $colname ]['name'] != "" ){
			$fileName = $this->systemFileUpload($file[ $colname ],$colname);
			if($fileName){ $db->setData( $rec, $colname, $fileName );}
			//TODO: false �������ꍇ�̗�O�������K�v�ł́H
		}else if( !$delete_flag &&  $_POST[ $colname . '_filetmp' ] != "" && $FileBase->file_exists($_POST[ $colname . '_filetmp' ]) ){
			$db->setData( $rec, $colname, $_POST[ $colname.'_filetmp' ] );
			return;
		}else if( !$delete_flag &&  $_POST[ $colname ] != "" && $FileBase->file_exists($_POST[ $colname ])){
			$db->setData( $rec, $colname, $_POST[ $colname ] );
		}else {
			$multi_colname = rtrim($colname, '0123456789');
			if (isset($file[$multi_colname]) && is_array($file[$multi_colname]['name']) && count($file[$multi_colname]['name'])>0) {
				$keyList = Array('name','type','tmp_name','error','size');
				$topFile = array();
				foreach( $keyList as $key) {
					$topFile[$key] = array_shift($file[$multi_colname][$key]);
				}
                $fileName = $this->systemFileUpload($topFile, $colname);
                if ($fileName) {
                    $db->setData($rec, $colname, $fileName);
                }
                //TODO: false �������ꍇ�̗�O�������K�v�ł́H
            }
		}
	}

	function systemFileUpload($upFile,$colname)
	{
		global $MAX_FILE_SIZE;
		global $UPLOAD_FILE_EXT;
		global $FileBase;

		if( isset( $_POST['MAX_FILE_SIZE'] ) ){
			$max_size = $_POST['MAX_FILE_SIZE'];
		}else{
			$max_size = $MAX_FILE_SIZE;
		}
		if( $upFile['size'] > $max_size ){ return false; }

		// �g���q�̎擾
		preg_match( '/(\.\w*$)/', $upFile['name'], $tmp );
		$ext		 = strtolower(str_replace( ".", "", $tmp[1] ));

		// �f�B���N�g���̎w��
		$directory	 = 'file/tmp/';
		if( mb_strpos( $this->fileDir, 'lock' ) !== false ) { $directory .= 'lock/'; }
		if(!is_dir($directory)) { mkdir( $directory, 0777 );chmod($directory, 0777); } //�f�B���N�g�������݂��Ȃ��ꍇ�͍쐬

		// �t�@�C���p�X�̍쐬
		$fileName	 = $directory.md5( time().$colname.$upFile['name'] ).'.'.$ext;

		// ���g���q�̂݃t�@�C���̃A�b�v���[�h
		if( !in_array( $ext , $UPLOAD_FILE_EXT ) )
		{ return false; }

		switch($ext)
		{
			case 'gif'  :
			case 'jpg'  :
			case 'jpeg' :
			case 'png'  :
			case 'swf'  :
			case 'bmp'  :
				if( !SystemUtil::VerifyImageExt( $upFile[ 'tmp_name' ] , $ext ) )
				{ return false; }

				break;
		}

		if( file_exists($upFile['tmp_name'])){ $FileBase->upload($upFile['tmp_name'], $fileName) ; }

		$FileBase->fixRotate( $fileName );

		return $fileName;
	}


		function doFileDelete( &$gm, &$rec, &$old_rec = null ){
			global $DELETE_FILE_TYPES;
			global $DELETE_TABLE_TYPES;
			global $FileBase;

			if( !isset($_GET['type'] ) || isset($DELETE_TABLE_TYPES) && is_array($DELETE_TABLE_TYPES) && !isset($DELETE_TABLE_TYPES[$_GET['type']]) ){
				return;
			}

			$db = $gm[ $_GET['type'] ]->getDB();

			for( $i=0; $i<count( $db->colName ); $i++ ){

				if( in_array( $db->colType[ $db->colName[$i] ], $DELETE_FILE_TYPES )  ){
					$file_name = $db->getData( $rec, $db->colName[$i] );
					if( !is_null($old_rec) ){
						$old_file_name = $db->getData( $old_rec, $db->colName[$i] );
						if( $old_file_name == $file_name ){
							continue;
						}
						$file_name = $old_file_name;
					}
					if( !is_null($file_name) && strlen($file_name) ){
						$FileBase->delete(($file_name));
						if( $db->colType[ $db->colName[$i] ] == 'image' ){
							mod_Thumbnail::DeleteAll( $file_name );
						}
					}
				}
			}
		}

	/**
	 * �t�@�C���A�b�v���[�h�̊��������B
	 * �ꎞ�A�b�v���[�h�Ƃ��Ă����t�@�C���𐳎��A�b�v���[�h�ւƏ���������B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g
	 * @param db Database�I�u�W�F�N�g
	 * @param rec ���R�[�h�f�[�^
	 */
	function uplodeComp( &$gm, &$db, &$rec )
	{
		global $FileBase;
		// �J�����̂����t�@�C���A�b�v���[�h�^�C�v�̂ݓ��e���m�F����
		foreach( $db->colName as $colum )
		{
			if( $gm[$_GET['type']]->colType[$colum] == 'image' ||  $gm[$_GET['type']]->colType[$colum] == 'file' )
			{
				$before	 = $db->getData( $rec, $colum );
				$after	 = preg_replace( '/(file\/tmp\/|file\/tmp\/lock\/)(\w*\.\w*)$/', '\2', $before );
				if( $before != $after )
				{// �t�@�C���̃A�b�v���[�h���s���Ă����ꍇ�f�[�^�������ւ���B
					// �g���q�̎擾
					preg_match( '/(\.\w*$)/', $after, $tmp );
					$ext		 = strtolower(str_replace( ".", "", $tmp[1] ));
					// �f�B���N�g���̎w��
					$dirList	 = explode( '/', $this->fileDir );
					$directory	 = 'file/';
					foreach( $dirList as $dir )
					{
						switch($dir)
						{
							case 'ext': // �g���q
								$directory .= $ext.'/';
								break;
							case 'cat':	// ��ޕ�
								switch($ext)
								{
									case 'gif':
									case 'jpg':
									case 'jpeg':
									case 'png':
										$cat = 'image';
										break;
									case 'swf':
										$cat = 'flash';
										break;
									case 'lzh':
									case 'zip':
										$cat = 'archive';
										break;
									default:
										$cat = 'category';
										break;
								}
								$directory .= $cat.'/';
								break;
							case 'lock': // htaccess�ŃA�N�Z�X���ۂ�ݒ肵���f�B���N�g��
								$directory .= 'lock/';
								break;
							default:	// timeformat
								$directory .= date($dir).'/';
								break;
						}
						if(!is_dir($directory)) { mkdir( $directory, 0777 ); } //�f�B���N�g�������݂��Ȃ��ꍇ�͍쐬
					}
					if(!is_dir($directory)) { mkdir( $directory, 0777 ); } //�f�B���N�g�������݂��Ȃ��ꍇ�͍쐬

					if( $FileBase->file_exists($before) && $FileBase->copy($before, $directory.$after) ){
						if(file_exists($before)) { unlink($before); }
					}
					$db->setData( $rec, $colum, $directory.$after );
				}
			}
		}
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	// �����֌W
	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	/**
	 * �������ʕ`��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�ł��B
	 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
	 * @param args �R�}���h�R�����g�����z��ł��B�@��������ID��n���܂��B �������Ƀ����N���邩��^�U�l�œn���܂��B
	 */
	function searchResult( &$gm, $rec, $args )
	{
		// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
		global $loginUserType;
		global $loginUserRank;

		global $resultNum;
		global $pagejumpNum;
		global $phpName;
		// **************************************************************************************

		$db		 = $gm->getDB();

		$table   = SearchTableStack::getCurrent();
		$row   = SearchTableStack::getCurrentRow();
		$page	 = $_GET['page'];

		$resultNumLocal = ( 0 < $_GET[ 'num' ] ? $_GET[ 'num' ] : $resultNum );

		if( 0 < WS_SYSTEM_SEARCH_RESULT_NUM_MAX && WS_SYSTEM_SEARCH_RESULT_NUM_MAX < $resultNumLocal ) //�\�������̎w�肪��������ꍇ
			{ $resultNumLocal = WS_SYSTEM_SEARCH_RESULT_NUM_MAX; }

		// �ϐ��̏������B
		if(  !isset( $_GET['page'] )  ){ $page	 = 0; }

		else if( 0 < $page ) //�y�[�W���w�肳��Ă���ꍇ
		{
			$beginRow = $page * $resultNumLocal; //�y�[�W���̍ŏ��̃��R�[�h�̍s��
			$tableRow = $row;        //�e�[�u���̍s��

			if( $tableRow <= $beginRow ) //�e�[�u���̍s���𒴂��Ă���ꍇ
			{
				$maxPage = ( int )( ( $tableRow - 1 ) / $resultNumLocal ); //�\���\�ȍő�y�[�W

				$page = $maxPage;
			}
		}

		else if(  $page < 0 )
		{
			$page	 = 0;
		}
		// �������ʏ����o�́B
		$viewTable	 = $db->limitOffset(  $table, $page * $resultNumLocal, $resultNumLocal  );

		switch( $args[0] )
		{
			case 'info':
				// �������ʏ��f�[�^����
				$gm->setVariable( 'RES_ROW', $row );

				$gm->setVariable( 'VIEW_BEGIN', $page * $resultNumLocal + 1 );
				if( $row >= $page * $resultNumLocal + $resultNumLocal )
				{
					$gm->setVariable( 'VIEW_END', $page * $resultNumLocal + $resultNumLocal );
					$gm->setVariable( 'VIEW_ROW', $resultNumLocal );
				}
				else
				{
					$gm->setVariable( 'VIEW_END', $row );
					$gm->setVariable( 'VIEW_ROW', $row % $resultNumLocal );
				}
				$this->addBuffer( $this->getSearchInfo( $gm, $viewTable, $loginUserType, $loginUserRank ) );

				break;

			case 'result':
				// �������ʂ����X�g�\��
				for($i=0; $i<count((array)$TABLE_NAME); $i++)
				{
					$tgm[ $_GET['type'] ]->addAlias(  $TABLE_NAME[$i], $tgm[ $TABLE_NAME[$i] ]  );
				}

				if( 'embed' == self::$CallMode )
					{ $this->addBuffer( $this->getEmbedSearchResult( $gm, $viewTable, $loginUserType, $loginUserRank ) ); }
				else
					{ $this->addBuffer( $this->getSearchResult( $gm, $viewTable, $loginUserType, $loginUserRank ) ); }

				break;

			case 'pageChange':
				$this->addBuffer( $this->getSearchPageChange( $gm, $viewTable, $loginUserType, $loginUserRank, $row, $pagejumpNum, $resultNumLocal, $phpName, 'page' )  );
				break;

			case 'setResultNum':
				$resultNum				 = $args[1];
				break;

			case 'setPagejumpNum':
				$pagejumpNum			 = $args[1];
				break;

			case 'setPhpName': // �y�[�W���[�̃����Nphp�t�@�C�����w��(���ݒ莞��search.php)
				$phpName				 = $args[1];
				break;

			case 'row':
				$this->addBuffer( $row );
				break;
		}
	}

	/**
	 * �������ʕ`��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�ł��B
	 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
	 * @param args �R�}���h�R�����g�����z��ł��B�@��������ID��n���܂��B �������Ƀ����N���邩��^�U�l�œn���܂��B
	 */
	function searchCreate( &$gm, $rec, $args )
	{
		// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
		global $loginUserType;
		global $loginUserRank;
		// **************************************************************************************
		global $resultNum;
		global $pagejumpNum;
		// **************************************************************************************

		switch($args[0]){
			case 'new':
				if( isset( $args[1] ))
				$type = $args[1];
				else
				$type = $_GET['type'];
				SearchTableStack::createSearch( $type );
				break;
			case 'run':
				SearchTableStack::runSearch();
				break;
			case 'setPal':
			case 'setParam':
				SearchTableStack::setParam($args[1],array_slice($args,2));
				break;
			case 'setVal':
			case 'setValue':
				SearchTableStack::setValue($args[1],array_slice($args,2));
				break;
			case 'setAlias':
				SearchTableStack::setAlias($args[1],array_slice($args,2));
				break;
			case 'setAliasParam':
				SearchTableStack::setAliasParam($args[1],array_slice($args,2));
				break;
			case 'set'://�\��
				break;
			case 'end':
				SearchTableStack::endSearch();
				break;
			case 'setPartsName':
				SearchTableStack::setPartsName($args[1],$args[2]);
				break;
			case 'sort':
				SearchTableStack::sort($args[1],$args[2]);
				break;
			case 'row':
				$this->addBuffer( SearchTableStack::getCurrentRow() );
				break;
		}
	}

	function getEmbedSearchResult( &$_gm, $table, $loginUserType, $loginUserRank )
	{
		global $gm;

		$type  = SearchTableStack::getType();

		if( SearchTableStack::getPartsName( 'list' ) )
		{
			$file = Template::getTemplate( $loginUserType , $loginUserRank , $_GET[ 'embedID' ] , self::ModifyTemplateLabel( 'SEARCH_EMBED_DESIGN' ) , false );

			if( is_file( $file ) )
				{ $html = Template::getListTemplateString( $gm[ $type ] , $table , $loginUserType , $loginUserRank , $_GET[ 'embedID' ] , self::ModifyTemplateLabel( 'SEARCH_EMBED_DESIGN' ) , false , 'list_' . SearchTableStack::getPartsName( 'list' ) ); }
			else
				{ $html = Template::getListTemplateString( $gm[ $type ] , $table , $loginUserType , $loginUserRank , $_GET[ 'embedID' ] , 'SEARCH_EMBED_DESIGN' , false , 'list_' . SearchTableStack::getPartsName( 'list' ) ); }
		}
		else
		{
			$file = Template::getTemplate( $loginUserType , $loginUserRank , $_GET[ 'embedID' ] , self::ModifyTemplateLabel( 'SEARCH_EMBED_DESIGN' ) , false );

			if( is_file( $file ) )
				{ $html = Template::getListTemplateString( $gm[ $type ] , $table , $loginUserType , $loginUserRank , $_GET[ 'embedID' ] , self::ModifyTemplateLabel( 'SEARCH_EMBED_DESIGN' ) , false , 'list' ); }
			else
				{ $html = Template::getListTemplateString( $gm[ $type ] , $table , $loginUserType , $loginUserRank , $_GET[ 'embedID' ] , 'SEARCH_EMBED_DESIGN' , false , 'list' ); }
		}

		return $html;
	}

	/**
	 * �������ʂ����X�g�`�悷��B
	 * �y�[�W�؂�ւ��͂��̗̈�ŕ`�悷��K�v�͂���܂���B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g
	 * @param table �������ʂ̃e�[�u���f�[�^
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function getSearchResult( &$_gm, $table, $loginUserType, $loginUserRank )
	{
		// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
		global $gm;
		// **************************************************************************************

		$type  = SearchTableStack::getType();

		switch( $type )
		{
			default:
				if(SearchTableStack::getPartsName('list'))
				{
					$file = Template::getTemplate( $loginUserType , $loginUserRank , $type , self::ModifyTemplateLabel( 'SEARCH_LIST_PAGE_DESIGN' ) , false );

					if( is_file( $file ) )
						{ $html = Template::getListTemplateString( $gm[ $type ] , $table , $loginUserType , $loginUserRank , $type , self::ModifyTemplateLabel( 'SEARCH_LIST_PAGE_DESIGN' ) , false , SearchTableStack::getPartsName('list') ); }
					else
						{ $html = Template::getListTemplateString( $gm[ $type ] , $table , $loginUserType , $loginUserRank , $type , 'SEARCH_LIST_PAGE_DESIGN' , false , SearchTableStack::getPartsName('list') ); }
				}
				else
				{
					$file = Template::getTemplate( $loginUserType , $loginUserRank , $type , self::ModifyTemplateLabel( 'SEARCH_LIST_PAGE_DESIGN' ) , false );

					if( is_file( $file ) )
						{ $html = Template::getListTemplateString( $gm[ $type ] , $table , $loginUserType , $loginUserRank , $type , self::ModifyTemplateLabel( 'SEARCH_LIST_PAGE_DESIGN' ) ); }
					else
						{ $html = Template::getListTemplateString( $gm[ $type ] , $table , $loginUserType , $loginUserRank , $type , 'SEARCH_LIST_PAGE_DESIGN' ); }
				}
				break;
		}

		return $html;
	}

	/**
	 * �������ʃy�[�W�؂�ւ�����`�悷��B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g
	 * @param table �������ʂ̃e�[�u���f�[�^
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 * @param partkey �����L�[
	 */
	function getSearchPageChange( &$gm, $table, $loginUserType, $loginUserRank, $row, $pagejumpNum, $resultNum, $phpName, $param )
	{
		// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
		global $phpName;
		// **************************************************************************************

		$type = SearchTableStack::getType();

		switch( $type )
		{
			default:
				$design = Template::getTemplate( $loginUserType , $loginUserRank , '' , self::ModifyTemplateLabel( 'SEARCH_PAGE_CHANGE_DESIGN' ) );

				if( !is_file( $design ) )
					{ $design = Template::getTemplate( $loginUserType , $loginUserRank , '' , 'SEARCH_PAGE_CHANGE_DESIGN' ); }


				$query  = $_GET;

				if(!strlen($phpName))
				{
					if( 'index' == WS_SYSTEM_SYSTEM_FORM_ACTON )
					{
						$phpName                   = 'index.php';
						$query[ 'app_controller' ] = 'search';
					}
					else
						{ $phpName = 'search.php'; }

					$html    = SystemUtil::getPager( $gm, $design, $query, $row, $pagejumpNum, $resultNum, $phpName, $param , SearchTableStack::getPartsName('change') );
					$phpName = '';
				}
				else
					{ $html = SystemUtil::getPager( $gm, $design, $query, $row, $pagejumpNum, $resultNum, $phpName, $param , SearchTableStack::getPartsName('change') ); }

				break;

		}
		return $html;
	}

	/**
	 * �������ʂ̃y�[�W�؂�ւ������擾����B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g
	 * @param table �������ʂ̃e�[�u���f�[�^
	 * @param loginUserType ���O�C�����Ă��郆�[�U�̎��
	 * @param loginUserRank ���O�C�����Ă��郆�[�U�̌���
	 */
	function getSearchInfo( &$gm, $table, $loginUserType, $loginUserRank )
	{
		$type = SearchTableStack::getType();

		switch( $type )
		{
			default:
				if(SearchTableStack::getPartsName('info'))
				{
					$html = Template::getTemplateString( $gm , null , $loginUserType , $loginUserRank , '' , self::ModifyTemplateLabel( 'SEARCH_PAGE_CHANGE_DESIGN' ) , false , null, SearchTableStack::getPartsName('info') );

					if( !is_file( $html ) )
						{ $html = Template::getTemplateString( $gm , null , $loginUserType , $loginUserRank , '' , 'SEARCH_PAGE_CHANGE_DESIGN' , false , null, SearchTableStack::getPartsName('info') ); }
				}
				else
				{
					$html = Template::getTemplateString( $gm , null , $loginUserType , $loginUserRank , '' , self::ModifyTemplateLabel( 'SEARCH_PAGE_CHANGE_DESIGN' ) , false , null, 'info' );

					if( !is_file( $html ) )
						{ $html = Template::getTemplateString( $gm , null , $loginUserType , $loginUserRank , '' , 'SEARCH_PAGE_CHANGE_DESIGN' , false , null, 'info' ); }
				}
				break;

		}
		return $html;
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	// �ėp���o�͊֌W
	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	

	//main css output
	function css_load( &$gm, $rec, $args ){
		global $css_name;
		global $CSS_PATH;
		global $css_file_paths;
		global $sp_css_file_paths;
		global $sp_mode;
		global $loginUserType;

		if( is_file($CSS_PATH.$css_name) )
		{
			switch($loginUserType)
			{
			case 'admin':
			case 'cUser':
				break;
			default:
				$file = $CSS_PATH.$css_name;
				if( $sp_mode )
				{
					if( is_file($CSS_PATH.'sp/'.$css_name) )		 { $file = $CSS_PATH.'sp/'.$css_name; }
					elseif( is_file($CSS_PATH.'sp/standard.css') )	 { $file = $CSS_PATH.'sp/standard.css'; }
				}
                if( strpos($file,'http') === 0 || strpos($file,'//') === 0 ){
                    $this->addBuffer('<link rel="stylesheet" type="text/css" href="' . $file . '" media="all" />' . "\n");
                }else{
                    $ts = filemtime($file);
                    $this->addBuffer('<link rel="stylesheet" type="text/css" href="' . $file . '?' . $ts. '" media="all" />' . "\n");
                }
				break;
			}
		}

		if( $sp_mode ){
			$temp = $sp_css_file_paths;
		}else{
			$temp = $css_file_paths;
		}

		$css_root = Array();

		foreach( $temp as $type => $value )
		{
			foreach( explode( '/' , $type ) as $subType )
			{
				foreach( $value as $label => $path )
					{ $css_root[ $subType ][ $label ] = $path; }
			}
		}

		if( isset($css_root) ){
			foreach( array('all', $loginUserType) as $type )
			{
				if( isset($css_root[$type]) || is_array($css_root[$type]) ){
					foreach( $css_root[$type] as $css_file_path ){
                        if( strpos($css_file_path,'http') === 0 || strpos($css_file_path,'//') === 0 ){
                            $this->addBuffer('<link rel="stylesheet" type="text/css" href="' . $css_file_path . '" media="all" />' . "\n");
                        }else{
                            $ts = filemtime($css_file_path);
                            $this->addBuffer('<link rel="stylesheet" type="text/css" href="' . $css_file_path . '?' . $ts. '" media="all" />' . "\n");
                        }
					}
				}
			}
		}
	}

	//main js output
	function js_load( &$gm, $rec, $args ){
		global $js_file_paths;
		global $sp_js_file_paths;
		global $sp_mode;
		global $loginUserType;
	

		if( $sp_mode ){
			$temp = $sp_js_file_paths;
		}else{
			$temp = $js_file_paths;
		}

		$root_path = Array();

		foreach( $temp as $type => $value )
		{
			foreach( explode( '/' , $type ) as $subType )
			{
				foreach( $value as $label => $path )
					{ $root_path[ $subType ][ $label ] = $path; }
			}
		}

		foreach( array('all', $loginUserType) as $type )
		{
			if( isset($root_path[$type]) || is_array($root_path[$type]) ){
				foreach( $root_path[$type] as $js_file_path ){
                    if( strpos($js_file_path,'http') === 0 || strpos($js_file_path,'//') === 0 ){
                        $this->addBuffer( '<script type="text/javascript" src="'.$js_file_path.'"></script>'."\n" );
                    }else{
                        $ts = filemtime($js_file_path);
                        $this->addBuffer( '<script type="text/javascript" src="'.$js_file_path.'?'.$ts.'"></script>'."\n" );
                    }
				}
			}
		}
	}

	function feed_load( &$gm , $rec , $args )
	{
		global $HOME;
		global $CONF_FEED_ENABLE;
		global $CONF_FEED_TABLES;
		global $CONF_FEED_TITLES;
		global $CONF_FEED_OUTPUT_DIR;

		if( !$CONF_FEED_ENABLE )
			{ return; }

		foreach( $CONF_FEED_TABLES as $tableName )
		{
			$rssPath = $CONF_FEED_OUTPUT_DIR . $tableName . '_rss.xml';

			if( is_file( $rssPath ) )
			{
				if( is_null($CONF_FEED_TITLES) || !isset($CONF_FEED_TITLES[$tableName]) ) {
					$gm = GMList::getGM($tableName);
					$template = Template::getTemplate('nobody', 1, $tableName, 'FEED_RSS_DESIGN');
					$title = $gm->getString($template, null, 'head_title');
				}else{
					$title = $CONF_FEED_TITLES[$tableName];
				}
				$this->addBuffer( '<link rel="alternate" href="' . $HOME . $rssPath . '" type="application/rss+xml" title="' . $title . '" />' . "\n" );
			}

			$atomPath = $CONF_FEED_OUTPUT_DIR . $tableName . '_atom.xml';

			if( is_file( $atomPath ) )
			{
				if( is_null($CONF_FEED_TITLES) || !isset($CONF_FEED_TITLES[$tableName]) ) {
					$gm = GMList::getGM($tableName);
					$template = Template::getTemplate('nobody', 1, $tableName, 'FEED_ATOM_DESIGN');
					$title = $gm->getString($template, null, 'head_title');
				}else{
					$title = $CONF_FEED_TITLES[$tableName];
				}

				$this->addBuffer( '<link rel="alternate" href="' . $HOME . $atomPath . '" type="application/atom+xml" title="' . $title . '" />' . "\n" );
			}
		}
	}

	//main link output
	function link_load( &$gm, $rec, $args ){
		global $head_link_object;

		if( is_null($head_link_object) || !is_array($head_link_object) )
		return;
		foreach( $head_link_object as $head_link ){
			$this->addBuffer( '<link rel="'.$head_link['rel'].'" type="'.$head_link['type'].'" href="'.$head_link['href'].'" />'."\n" );
		}
	}

	/*
	 * error���b�Z�[�W�̌ʕ\���p
	 */
	function validate( &$gm, $rec, $args ){

		if( !count( $args ) )
			{ $args = self::$ValidateColumnCache; }

		foreach( $args as $error ){
			$this->addBuffer( self::$checkData->getError( $error ) );
		}
	}

	/*
	 * error���b�Z�[�W�̌ʕ\���p
	 */
	function is_validate( &$gm, $rec, $args ){
		foreach( explode('/',$args[0]) as $l ){
			$ret = self::$checkData->isError( $l , $args[1] );
			if(strlen($ret)){ $this->addBuffer( $ret ); break; }
		}

		self::$ValidateColumnCache = explode( '/' , $args[ 0 ] );
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	// ��O�����֌W
	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	/*
	 * ��O�������̗�O�o�͂ɉ񂷑O�ɃL���b�`���āA���e�ɂ���ĕʂ̏����ɒ������ވׂ̂��́B
	 */
	static function manageExceptionView( $className ){
		global $gm;
		global $loginUserType;
		global $loginUserRank;

		if(is_null($gm)){
			// GUIManager�����������O�̃G���[�Ȃ̂ŁA���߂ė�O�p�̃G���[���o���Ă���B
			return false;
		}
		
		//����Ȃǂ��L�q����B
		/*	
		switch($className){
			case "IllegalAccessException":
				//�񃍃O�C�����ǂ���
				break;
		}
	 	*/
		
		return false;
	}


	/**********************************************************************************************************
	 * �V�X�e���p���\�b�h
	 **********************************************************************************************************/

	static $checkData = null;

	/**
	 * �R���X�g���N�^�B
	 */
	function __construct()	{ $this->flushBuffer(); }

	/*
	 * �G���[���b�Z�[�W��GUIManager��variable�ɃZ�b�g����
	 */
	function setErrorMessage(&$gm){
		if( self::$checkData && !self::$checkData->getCheck() ){
			$gm->setVariable( 'error_msg' , self::$checkData->getError() );
			$this->error_msg = "";
		}else{
			$gm->setVariable( 'error_msg' , '' );
		}
	}

	static $pageRecord = null;

	/**
	 * �y�[�W�Ɋ֘A�t�������R�[�h���L������
	 * @param db Database�I�u�W�F�N�g
	 * @param table_type �e�[�u���^�C�v a(all)/n(nomal)/d(delete)
	 */
	static function setPageRecord( $db, $table_type ){
		global $loginUserType;
		global $LOGIN_ID;

	    if( !isset($_GET['id']) && $_GET['type'] == $loginUserType ){
	    	$_GET['id'] = $LOGIN_ID;
	    }

		self::$pageRecord = $db->selectRecord($_GET['id'],$table_type);

		ConceptSystem::CheckRecord(self::$pageRecord)->OrThrow('RecordNotFound');

		return self::$pageRecord;
	}

	/**
	 * �v���r���[�p��POST�f�[�^�������R�[�h�Ƃ��Đݒ肷��
	 * @param db Database�I�u�W�F�N�g
	 * @param table_type �e�[�u���^�C�v a(all)/n(nomal)/d(delete)
	 */
	function setPreviewRecord( $db , $post , $previewMode ){
		global $loginUserType;
		global $loginUserRank;
		global $LOGIN_ID;
		global $gm;

		if( !isset( $_GET[ 'id' ] ) && $loginUserType == $_GET[ 'type' ] )
			{ $_GET[ 'id' ] = $LOGIN_ID; }

		$vRec = $db->selectRecord( $_GET[ 'id' ] , $table_type );

		if( !$vRec )
			{ $vRec = Array(); }

		foreach( $db->colName as $column )
		{
			if( is_array( $_POST[ $column ] ) )
				{ $vRec[ $column ] = implode( '/' , $_POST[ $column ] ); }
			else if( isset( $_POST[ $column ] ) )
				{ $vRec[ $column ] = $_POST[ $column ]; }
		}

		if( 'regist' == $previewMode )
			{ $this->registProc( $gm , $vRec , $loginUserType , $loginUserRank , true ); }
		else if( 'edit' == $previewMode )
			{ $this->editProc( $gm , $vRec , $loginUserType , $loginUserRank , true ); }

		self::$pageRecord = $vRec;

		ConceptSystem::CheckRecord( self::$pageRecord )->OrThrow( 'RecordNotFound' );

		return self::$pageRecord;
	}

	/*
	 * �y�[�W�S�̂ŋ��ʂ�head��Ԃ���B
	 * �e��\���y�[�W�̍ŏ��ɌĂяo�����֐�
	 *
	 * �o�͂ɐ��������������ꍇ�╪�򂵂����ꍇ�͂����ŕ��򏈗����L�ڂ���B
	 */
	static function getHead($gm,$loginUserType,$loginUserRank){
		global $NOT_LOGIN_USER_TYPE;

		if( self::$head || isset( $_GET['hfnull'] ) ){ return "";}

		self::$head = true;

		$html = "";

		if( $loginUserType == $NOT_LOGIN_USER_TYPE )	{ $html = Template::getTemplateString( $gm[ 'system' ] , null , $loginUserType , $loginUserRank , '' , 'HEAD_DESIGN' ); }
		else											{ $html = Template::getTemplateString( $gm[ $loginUserType ] , $rec , $loginUserType , $loginUserRank , '' , 'HEAD_DESIGN' ); }

		if( $_SESSION['ADMIN_MODE'] || $loginUserType == 'admin' ){
			$html .= Template::getTemplateString( $gm[ $loginUserType ] , $rec , $loginUserType , $loginUserRank , '' , 'HEAD_DESIGN_ADMIN_MODE' );
		}
		return $html;
	}

	/*
	 * �y�[�W�S�̂ŋ��ʂ�foot��Ԃ��B
	 * �e��\���y�[�W�̍Ō�ŌĂяo�����֐�
	 *
	 * �o�͂ɐ��������������ꍇ�╪�򂵂����ꍇ�͂����ŕ��򏈗����L�ڂ���B
	 */
	static function getFoot($gm,$loginUserType,$loginUserRank){
		global $NOT_LOGIN_USER_TYPE;

		if( self::$foot || isset( $_GET['hfnull'] ) ){ return "";}

		self::$foot = true;

		if( $loginUserType == $NOT_LOGIN_USER_TYPE )	{ return Template::getTemplateString( $gm[ 'system' ] , null , $loginUserType , $loginUserRank , '' , 'FOOT_DESIGN' ); }
		else											{ return Template::getTemplateString( $gm[ $loginUserType ] , $rec , $loginUserType , $loginUserRank , '' , 'FOOT_DESIGN' ); }
	}

	/*
	 * �ŏI�I�ȏo�͂��s�Ȃ��ׂɉ�ʕ`��̍Ō�ɌĂ΂��B
	 * ���������������鎖�ŁA�o�͂Ƀt�B���^����������B
	 *
	 * mobile�����̕����R�[�h�ϊ����s�Ȃ��Ă���B
	 */
	static function flush(){
		global $terminal_type;
		global $OUTPUT_CHARACODE;
		global $ALL_DEBUG_FLAG;
		global $DEBUG_TYPE;
		global $DEBUG_BUFFER;

		$output = ob_get_clean();

		if( strlen(self::$title) > 0 )
			{
				$output = preg_replace( '/<title>(.*)<\/title>/i', '<title>'.self::$title.'</title>', $output );
				$output = preg_replace( '/<meta property="og:title" content="(.*)" \/>/', '<meta property="og:title" content="' . self::$title . '" />', $output );
			}
		if( strlen(self::$description) > 0 )
			{
				$output = preg_replace( '/<meta name="description" content=(.*)>/', '<meta name="description" content="'.self::$description.'">', $output );
				$output = preg_replace( '/<meta property="og:description" content="(.*)" \/>/', '<meta property="og:description" content="' . self::$description . '" />', $output );
			}

		if( strlen(self::$keywords) > 0 )	 { $output = preg_replace( '/<meta name="keywords" content=(.*)>/', '<meta name="keywords" content="'.self::$keywords.'">', $output ); }

		if( 0 < strlen( self::$ogTitle ) )
			{ $output = preg_replace( '/<meta property="og:title" content="(.*)" \/>/', '<meta property="og:title" content="' . self::$ogTitle . '" />', $output ); }

		if( 0 < strlen( self::$ogType ) )
			{ $output = preg_replace( '/<meta property="og:type" content="(.*)" \/>/', '<meta property="og:type" content="' . self::$ogType . '" />', $output ); }

		if( 0 < strlen( self::$ogDescription ) )
			{ $output = preg_replace( '/<meta property="og:description" content="(.*)" \/>/', '<meta property="og:description" content="' . self::$ogDescription . '" />', $output ); }

		if( 0 < strlen( self::$ogURL ) )
			{ $output = preg_replace( '/<meta property="og:url" content="(.*)" \/>/', '<meta property="og:url" content="' . self::$ogURL . '" />', $output ); }

		if( 0 < strlen( self::$ogImage ) )
			{ $output = preg_replace( '/<meta property="og:image" content="(.*)" \/>/', '<meta property="og:image" content="' . self::$ogImage . '" />', $output ); }

		if( $terminal_type ){
			if( $OUTPUT_CHARACODE != 'UTF-8' ){
				print mb_convert_encoding( $output, $OUTPUT_CHARACODE, 'UTF-8' );
			}else{
				print $output;
			}
		}else{
				print $output;
		}

		if( $ALL_DEBUG_FLAG && 'subview' == $DEBUG_TYPE ) //�f�o�b�O���[�h��subview�̏ꍇ
		{
            $controller = strtolower( $controllerName );
			$isAPI    = ( 'api' == $controller );
			$isCron   = ( 'cron' == $controller );
			$isKeyGen = ( 'update' == $controller );
			$isThumbs = ( 'thumbnail' == $controller );

			if( !$isAPI && !$isCron && !$isKeyGen && !$isThumbs && $DEBUG_BUFFER ) //�f�o�b�O��񂪂���ꍇ
				{ print '<script>$(function(){ InitializeDebugView();AddDebugInfo( ' . json_encode( $DEBUG_BUFFER ) . ' );});</script>'; }
		}

		TemplateCache::SaveCache( $output );
	}

	// title,descrption,keywords��ύX�������ꍇ�e���v���[�g�ォ��Ăяo��
	function setTitle( &$gm, $rec, $args)		  { self::$title		 = self::convertSpace($args); }
	function setDescription( &$gm, $rec, $args)	  { self::$description	 = self::convertSpace($args); }
	function setKeywords( &$gm, $rec, $args)	  { self::$keywords		 = self::convertSpace($args); }

	function setOGTitle( &$gm, $rec, $args)       { self::$ogTitle       = self::convertSpace($args); }
	function setOGType( &$gm, $rec, $args)        { self::$ogType        = self::convertSpace($args); }
	function setOGDescription( &$gm, $rec, $args) { self::$ogDescription = self::convertSpace($args); }
	function setOGURL( &$gm, $rec, $args)         { self::$ogURL         = self::convertSpace($args); }
	function setOGImage( &$gm, $rec, $args)       { self::$ogImage       = self::convertSpace($args); }

	function convertSpace( $text )
	{
		if( is_array($text) ) { $text = implode( " ", $text ); }
		return str_replace( array("!CODE001;","!CODE101;"), array(" ", " ") , $text );
	}

	function modifyTemplateLabel( $iTemplateLabel )
	{
		if( isset( $_GET[ 'design' ] ) )
			{ $design = $_GET[ 'design' ]; }
		else if( isset( $_POST[ 'design' ] ) )
			{ $design = $_POST[ 'design' ]; }

		if( !$design )
			{ return $iTemplateLabel; }

		if( preg_match( '/\W/' , $design ) )
			{ return $iTemplateLabel; }

		return $iTemplateLabel . '_' . $design;
	}
}


class SearchTableStack{
	private static $stack = Array();
	private static $row_stack = Array();
	private static $current_count = 0;
	private static $current_search = null;
	//private static $stack_search = Array();

	private static $list_parts = Array();
	private static $info_parts = Array();
	private static $change_parts = Array();

	static function pushStack(&$table){
		self::$stack[ self::$current_count ] = $table;
	}

	static function popStack(){
		$stack = self::$stack[ self::$current_count ];
		unset(self::$stack[ self::$current_count ]);
		unset(self::$row_stack[ self::$current_count ]);
		return $stack;
	}

	static function getCurrent(){
		return self::$stack[ self::$current_count ];
	}

	static function getCurrentCount(){
		return self::$current_count;
	}

	static function getCurrentRow(){
		global $gm;

		if( !isset(self::$row_stack[ self::$current_count ]) ){
			self::$row_stack[ self::$current_count ] = $gm[ self::getType() ]->getDB()->getRow( self::$stack[ self::$current_count ] );
		}
		return self::$row_stack[ self::$current_count ];
	}

	static function createSearch($type){
		global $gm;
		self::$current_count++;

		self::$current_search = new Search($gm[ $type ],$type);
		self::$current_search->paramReset();

		self::$list_parts[ self::$current_count ] = "";
		self::$info_parts[ self::$current_count ] = "";
		self::$change_parts[ self::$current_count ] = "";
	}

	static function setValue($coumn_name,$var){
		if( count($var) == 1 ){
			self::$current_search->setValue($coumn_name,$var[0]);
		}else{
			self::$current_search->setValue($coumn_name,$var);
		}
	}

	static function setParam($table_name,$var){
		self::$current_search->setParamertor($table_name,$var);
	}

	static function setAlias($table_name,$var){
		if( is_array($var) ){
			self::$current_search->setAlias($table_name,implode( ' ', $var ) );
		}else{
			self::$current_search->setAlias($table_name,$var);
		}
	}
	static function setAliasParam($coumn_name,$var){
		self::$current_search->setAliasParam($coumn_name,$var);
	}

	static function runSearch(){
		global $gm;
		global $loginUserType;
		global $loginUserRank;

		$sys	 = SystemUtil::getSystem( self::getType() );

		$sys->searchResultProc( $gm, self::$current_search, $loginUserType, $loginUserRank );

		$table = self::$current_search->getResult();

		$sys->searchProc( $gm, $table, $loginUserType, $loginUserRank );

		self::pushStack( $table );
	}

	static function endSearch(){
		self::popStack();
		unset(self::$row_stack[ self::$current_count ]);
		self::$current_count--;
	}

	static function setPartsName( $type, $parts ){
		switch($type){
			case 'list':
				self::$list_parts[ self::$current_count ] = $parts;
				break;
			case 'info':
				self::$info_parts[ self::$current_count ] = $parts;
				break;
			case 'change':
				self::$change_parts[ self::$current_count ] = $parts;
				break;
		}
	}

	static function getPartsName($type){
		$ret = '';
		switch($type){
			case 'list':
				if( isset( self::$list_parts[ self::$current_count ]) ){
					$ret = self::$list_parts[ self::$current_count ];
				}
				break;
			case 'info':
				if( isset( self::$info_parts[ self::$current_count ]) ){
					$ret = self::$info_parts[ self::$current_count ];
				}
				break;
			case 'change':
				if( isset( self::$change_parts[ self::$current_count ]) ){
					$ret = self::$change_parts[ self::$current_count ];
				}
				break;
		}
		return $ret;
	}

	static function getType(){
		if( self::$current_count == 0 )
		return $_GET['type'];
		else
		return self::$current_search->type;
	}

	static function sort($key,$param){
		self::$current_search->sort['key'] = $key;
		self::$current_search->sort['param'] = $param;
	}
}

?>
