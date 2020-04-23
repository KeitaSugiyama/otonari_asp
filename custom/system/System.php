<?php

include "include/base/SystemBase.php";

/**
 * �V�X�e���R�[���N���X
 * 
 * @author �O�H��q
 * @version 1.0.0
 * 
 */
class System extends SystemBase
{
	/**********************************************************************************************************
	 * �ėp�V�X�e���p���\�b�h
	 **********************************************************************************************************/
	
	
	
	
	
	
	
	
	
	

	/*
	 * ��O�������̗�O�o�͂ɉ񂷑O�ɃL���b�`���āA���e�ɂ���ĕʂ̏����ɒ������ވׂ̂��́B
	 */
	static function manageExceptionView( $className ){
		global $gm;
		global $loginUserType;
		global $loginUserRank;
		global $NOT_LOGIN_USER_TYPE;
		global $THIS_TABLE_REGIST_USER;
        global $controllerName;

		if(is_null($gm)){
			// GUIManager�����������O�̃G���[�Ȃ̂ŁA���߂ė�O�p�̃G���[���o���Ă���B
			return false;
		}
		
		switch($className){
			case "IllegalAccessException":
				//�񃍃O�C�����ǂ���
				if( $loginUserType != $NOT_LOGIN_USER_TYPE ){
					return false;
				}
				
				//����̃��[�U�[�^�C�v�Ń��O�C������Ό����R���e���c���ǂ������`�F�b�N��������̃T���v���B
				// regist.php?type=items �ɔ񃍃O�C���ŃA�N�Z�X�����ꍇ�Ƀ��b�Z�[�W��ǉ����ă��O�C����ʂ�\�����Ă��܂��B
				$type = $_GET['type'];
				//$db = $gm[$type]->getDB();
				if( $controllerName == "Register" && isset($THIS_TABLE_REGIST_USER[$type]) && array_search("cUser",$THIS_TABLE_REGIST_USER[$type]) !== false ){
				
					//���O�C���𑣂��ꍇ�B
					$gm[$type]->setVariable( 'message', "���Y�̃y�[�W�̓��O�C�����ɂ̂ݕ\���\�ȃy�[�W�ł��B" );
					
					Template::drawTemplate( $gm[$type] , $rec , $loginUserType , $loginUserRank , '' , 'LOGIN_PAGE_DESIGN' );
					return true;
				}
				
				//���Ƀ��O�C����̃y�[�W���Ȃ��ꍇ�́A�ʏ�̃G���[��ʂ�\������B
				
				break;
		}
		
		return false;
	}

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �o�^�֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**
		 * �����o�^�����m�F�B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param edit �ҏW�Ȃ̂��A�V�K�ǉ��Ȃ̂���^�U�l�œn���B
		 * @return �����o�^���\����^�U�l�ŕԂ��B
		 */
		function duplicateCheck( &$gm, $loginUserType, $loginUserRank )
		{
			return parent::copyCheck($gm, $loginUserType, $loginUserRank);
		}
		

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �ҏW�֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �폜�֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * �폜���e�m�F�B
	 *
	 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
	 * @param rec �t�H�[���̂���̓��̓f�[�^�𔽉f�������R�[�h�f�[�^�B
	 * @return �G���[�����邩��^�U�l�œn���B
	 */
	function deleteCheck(&$gm, &$rec, $loginUserType, $loginUserRank )
	{

		return self::$checkData->getCheck();
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
		
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			global $DELETE_CHECK_PAGE_DESIGN;
			// **************************************************************************************
			global $TABLE_NAME;
			global $THIS_TABLE_IS_USERDATA;
			global $LOGIN_ID;
			global $NOT_LOGIN_USER_TYPE;
			// **************************************************************************************
			
			$db = $gm[ $_GET[ 'type' ] ]->getDB();

			switch(  $_GET['type']  )
			{
				default:
					// �ėp����
                    Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'DELETE_CHECK_PAGE_DESIGN' , 'edit.php?type='. $_GET['type'] .'&id='. $_GET['id'] );
			}
			
		
		}

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �����֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**
		 * ���������B
		 * �t�H�[�����͈ȊO�̕��@�Ō���������ݒ肵�����ꍇ�ɗ��p���܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�z��@�A�z�z��� gm[ TABLE�� ] �ŃA�N�Z�X���\�ł��B
		 * @param table �t�H�[���̂���̓��͓��e�Ɉ�v���郌�R�[�h���i�[�����e�[�u���f�[�^�B
		 */
		function searchProc( &$gm, &$table, $loginUserType, $loginUserRank )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			global $THIS_TABLE_IS_USERDATA;
			global $ACTIVE_ACTIVATE;
            global $ACTIVE_ACCEPT;
			// **************************************************************************************
			global $LOGIN_ID;
            global $HOME;
			// **************************************************************************************
			
			$db		 = $gm[ $_GET['type'] ]->getDB();
					
			switch( $_GET['type'] )
			{
				default:
					if(  $THIS_TABLE_IS_USERDATA[ $_GET['type'] ]  )
					{
						if( $loginUserType != 'admin' )	 { $table	 = $db->searchTable( $table, 'activate', 'in', array($ACTIVE_ACTIVATE ,$ACTIVE_ACCEPT) ); }
					}
					break;
			}
		}



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �ڍ׏��֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		//   �A�N�e�B�x�[�g�֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

        //activate����y�уA�N�e�B�x�[�g��������
        function activateAction( &$gm, &$rec, $loginUserType, $loginUserRank ){
        global $ACTIVE_NONE;
        global $ACTIVE_ACTIVATE;
        global $MAILSEND_ADDRES;
        global $MAILSEND_NAMES;
		global $template_path;
		global $mobile_path;
        
            $db = $gm[ $_GET['type'] ]->getDB();
            
			if(  $db->getData( $rec, 'activate' ) == $ACTIVE_NONE  )
			{
				$db->updateRecord( $rec );

                $mail_template = Template::getTemplate('',1,$_GET['type'], "ACTIVATE_COMP_MAIL" );
				Mail::send( $mail_template , $MAILSEND_ADDRES, $db->getData( $rec, 'mail' ), $gm[ $_GET['type'] ], $rec , $MAILSEND_NAMES );
				Mail::send( $mail_template , $MAILSEND_ADDRES, $MAILSEND_ADDRES, $gm[ $_GET['type'] ], $rec , $MAILSEND_NAMES );
			}
            return true;
        }


		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		//   ���O�C���֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/



		/**********************************************************************************************************
		 * �ėp�V�X�e���`��n�p���\�b�h
		 **********************************************************************************************************/



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �o�^�֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

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
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			global $REGIST_CHECK_PAGE_DESIGN;
			// **************************************************************************************
			global $TABLE_NAME;
			global $THIS_TABLE_IS_USERDATA;
			global $MAIL_SEND_FALED_DESIGN;
			global $LOGIN_PASSWD_COLUM;
			// **************************************************************************************
			
			// �m�F���͌n�̕⊮
			if(  $THIS_TABLE_IS_USERDATA[ $_GET['type'] ]  )
			{
				foreach( $gm[ $_GET['type'] ]->colName as $name ){
					if( $LOGIN_PASSWD_COLUM[ $_GET['type'] ] != $name ){//�}�[�W������type��password��������
						if( ($pos = strpos( $gm[ $_GET['type'] ]->colRegist[$name], 'ConfirmInput' ) ) !== FALSE ){
							$after = substr( $gm[ $_GET['type'] ]->colRegist[$name], $pos+13);
							if( ( $end = strpos( $after, '/') ) !== FALSE ){ $after = substr( $after, 0, $end ); }
							$db = $gm[$_GET['type']]->getDB();
							$gm[ $_GET['type'] ]->addHiddenForm( $after, $db->getData( $rec, $name ) );
						}
					}
				}
			}
			
			switch(  $_GET['type']  )
			{
				default:
					// �ėp����
                    Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'REGIST_CHECK_PAGE_DESIGN' , 'regist.php?type='. $_GET['type'] );
			}
		
		}

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �ҏW�֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

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
		
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			global $EDIT_CHECK_PAGE_DESIGN;
			global $LOGIN_ID;
            global $NOT_LOGIN_USER_TYPE;
			// **************************************************************************************
			
			$db = $gm[ $_GET[ 'type' ] ]->getDB();
			
			// �m�F���͌n�̕⊮
			if(  $THIS_TABLE_IS_USERDATA[ $_GET['type'] ]  )
			{
				foreach( $gm[ $_GET['type'] ]->colName as $name ){
					if( $LOGIN_PASSWD_COLUM[ $_GET['type'] ] != $name ){//�}�[�W������type��password��������
						if( ($pos = strpos( $gm[ $_GET['type'] ]->colRegist[$name], 'ConfirmInput' ) ) !== FALSE ){
							$after = substr( $gm[ $_GET['type'] ]->colRegist[$name], $pos+13);
							if( ( $end = strpos( $after, '/') ) !== FALSE ){ $after = substr( $after, 0, $end ); }
							$gm[ $_GET['type'] ]->addHiddenForm( $after, $db->getData( $rec, $name ) );
						}
					}
				}
			}
			
			switch(  $_GET['type']  )
			{
				default:
					// �ėp����
                    Template::drawTemplate( $gm[ $_GET['type'] ] , $rec , $loginUserType , $loginUserRank , $_GET['type'] , 'EDIT_CHECK_PAGE_DESIGN' , 'edit.php?type='. $_GET['type'] .'&id='. $_GET['id'] );
			}
		
		}

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �폜�֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �����֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////


        function drawSearch( &$gm, &$sr, $table, $loginUserType, $loginUserRank ){
            SearchTableStack::pushStack($table);

            if(  isset( $_GET['multimail'] )  ){
                $db = $gm[ $_GET['type'] ]->getDB();
                $row	 = $db->getRow( $table );
                for($i=0; $i<$row; $i++){
                    $rec	 = $db->getRecord( $table, $i );
                    $_GET['receive_id'][] = $db->getData( $rec, 'id' );
                }
                $_GET['type'] = 'multimail';
                include_once "regist.php";
            }else{

			$label = 'SEARCH_RESULT_DESIGN';

			if( $_GET[ 'exstyle' ] )
				$label .= '_' . strtoupper( $_GET[ 'exstyle' ] );

            Template::drawTemplate( $gm[ $_GET['type'] ] , null , $loginUserType , $loginUserRank , $_GET['type'] , $label );
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
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			global $SEARCH_NOT_FOUND_DESIGN;
			// **************************************************************************************
			global $NOT_LOGIN_USER_TYPE;
			// **************************************************************************************
			
			$label = 'SEARCH_NOT_FOUND_DESIGN';

			if( $_GET[ 'exstyle' ] )
				$label .= '_' . strtoupper( $_GET[ 'exstyle' ] );

			Template::drawTemplate( $gm[ $_GET['type'] ] , null , $loginUserType , $loginUserRank , $_GET['type'] , $label );
/*
			switch( $_GET['type'] )
			{					
				default:
					Template::drawTemplate( $gm[ $_GET['type'] ] , null , $loginUserType , $loginUserRank , $_GET['type'] , 'SEARCH_NOT_FOUND_DESIGN' );
					break;
			}
*/
		
		}


		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// �ڍ׃y�[�W�֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		//   �A�N�e�B�x�[�g�֌W
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

        function drawActivateComp( &$gm, &$rec, $loginUserType, $loginUserRank ){
                $gm[ $_GET['type'] ]->draw( Template::getLabelFile('ACTIVATE_DESIGN_HTML'), $rec );
        }
        function drawActivateFaled( &$gm, &$rec, $loginUserType, $loginUserRank ){
                $gm[ $_GET['type'] ]->draw( Template::getLabelFile('ACTIVATE_FALED_DESIGN_HTML'), $rec );
        }

		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/


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
			
			if( $loginUserType == $NOT_LOGIN_USER_TYPE )	{ $html = Template::getTemplateString( null , null , $loginUserType , $loginUserRank , '' , 'HEAD_DESIGN' ); }
			else											{ $html = Template::getTemplateString( $gm[ $loginUserType ] , $rec , $loginUserType , $loginUserRank , '' , 'HEAD_DESIGN' ); }
			
			if($_SESSION['ADMIN_MODE']){
				$html .= Template::getTemplateString( $gm[ $loginUserType ] , $rec , $loginUserType , $loginUserRank , '' , 'HEAD_DESIGN_ADMIN_MODE' );
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
			
			$type = SearchTableStack::getType();
			
			$label = 'SEARCH_LIST_PAGE_DESIGN';

			if( $_GET[ 'exstyle' ] )
				$label .= '_' . strtoupper( $_GET[ 'exstyle' ] );
			else
				$label = self::ModifyTemplateLabel( $label );

			switch( $type )
			{
				default:
				if(SearchTableStack::getPartsName('list'))
				{
					$file = Template::getTemplate( $loginUserType , $loginUserRank , $type , $label , false );

					if( is_file( $file ) )
						{ $html = Template::getListTemplateString( $gm[ $type ] , $table , $loginUserType , $loginUserRank , $type , $label , false , SearchTableStack::getPartsName('list') ); }
					else
						{ $html = Template::getListTemplateString( $gm[ $type ] , $table , $loginUserType , $loginUserRank , $type , 'SEARCH_LIST_PAGE_DESIGN' , false , SearchTableStack::getPartsName('list') ); }
				}
				else
				{
					$file = Template::getTemplate( $loginUserType , $loginUserRank , $type , $label , false );

					if( is_file( $file ) )
						{ $html = Template::getListTemplateString( $gm[ $type ] , $table , $loginUserType , $loginUserRank , $type , $label ); }
					else
						{ $html = Template::getListTemplateString( $gm[ $type ] , $table , $loginUserType , $loginUserRank , $type , 'SEARCH_LIST_PAGE_DESIGN' ); }
				}
			}
            return $html;
		}

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
			$row	 = $db->getRow( $table );
		
			// �ϐ��̏������B
			if(  !isset( $_GET['page'] )  ){ $_GET['page']	 = 0; }
			
			if( 0 < $_GET[ 'page' ] ) //�y�[�W���w�肳��Ă���ꍇ
			{
				$beginRow = $_GET[ 'page' ] * $resultNum; //�y�[�W���̍ŏ��̃��R�[�h�̍s��
				$tableRow = $db->getRow( $table );        //�e�[�u���̍s��

				if( $tableRow <= $beginRow ) //�e�[�u���̍s���𒴂��Ă���ꍇ
				{
					$maxPage = ( int )( ( $tableRow - 1 ) / $resultNum ); //�\���\�ȍő�y�[�W

					$_GET[ 'page' ] = $maxPage;
				}
			}

			if(  $_GET['page'] < 0 || $_GET['page'] * $resultNum + 1 > $db->getRow( $table )  )
			{
				// �������ʂ�\������y�[�W�����������ꍇ

                $tgm	 = SystemUtil::getGM();
                for($i=0; $i<count((array)$TABLE_NAME); $i++)
                {
                    $tgm[ $_GET['type'] ]->addAlias(  $TABLE_NAME[$i], $tgm[ $TABLE_NAME[$i] ]  );	
                }
				$this->drawSearchError( $tgm , $loginUserType, $loginUserRank );
			}
			else
			{
				// �������ʏ����o�́B
				$viewTable	 = $db->limitOffset(  $table, $_GET['page'] * $resultNum, $resultNum  );
				
				switch( $args[0] )
				{
					case 'info':
						// �������ʏ��f�[�^����
						$gm->setVariable( 'RES_ROW', $row );
						
						$gm->setVariable( 'VIEW_BEGIN', $_GET['page'] * $resultNum + 1 );
						if( $row >= $_GET['page'] * $resultNum + $resultNum )
						{
							$gm->setVariable( 'VIEW_END', $_GET['page'] * $resultNum + $resultNum );
							$gm->setVariable( 'VIEW_ROW', $resultNum );
						}
						else
						{
							$gm->setVariable( 'VIEW_END', $row );
							$gm->setVariable( 'VIEW_ROW', $row % $resultNum );
						}
						$this->addBuffer( $this->getSearchInfo( $gm, $viewTable, $loginUserType, $loginUserRank ) );
						
						break;
						
					case 'result':
						// �������ʂ����X�g�\��
						for($i=0; $i<count((array)$TABLE_NAME); $i++)
						{
							$tgm[ $_GET['type'] ]->addAlias(  $TABLE_NAME[$i], $tgm[ $TABLE_NAME[$i] ]  );	
						}
						$this->addBuffer( $this->getSearchResult( $gm, $viewTable, $loginUserType, $loginUserRank ) );
						break;
					case 'pageChange':
						$this->addBuffer( $this->getSearchPageChange( $gm, $viewTable, $loginUserType, $loginUserRank, $row, $pagejumpNum, $resultNum, $phpName, 'page' )  );
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
		}

	}

?>
