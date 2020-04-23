<?php

	/*******************************************************************************************************
	 * <PRE>
	 *
	 * quick.php - �ő̎��ʔԍ��擾��p�v���O����
	 *
	 * </PRE>
	 *******************************************************************************************************/

	ob_start();

	try
	{
		include_once 'custom/head_main.php';

		//�p�����[�^�`�F�b�N
		ConceptCheck::IsScalar( $_GET , Array( 'login' ) );
		ConceptCheck::IsScalar( $_POST , Array( 'login' ) );
		//�p�����[�^�`�F�b�N�����܂�

		// �w�b�_�[��ǂݍ��݂܂��B
		if($_POST["login"] == "true" || $_GET['login'] == 'true' ){
			if( $loginUserType == $NOT_LOGIN_USER_TYPE ){
				if($terminal_type){
					$UTN = MobileUtil::GetMobileID();
					if($UTN){
	                    for($i=0; $i<count($TABLE_NAME); $i++){
	                        if(  $THIS_TABLE_IS_QUICK[ $TABLE_NAME[$i] ]  ){
	                            $db		 = $gm[ $TABLE_NAME[$i] ]->getDB();
	                            $table	 = $db->getTable();
	                            $table	 = $db->searchTable( $table, 'activate', '!', $ACTIVE_NONE  );
	                            
	                            $table	 = $db->searchTable(  $table, 'terminal', '=', $UTN  );
	                            if(  $db->getRow( $table ) != 0  ){
	                                $rec	 = $db->getRecord( $table, 0 );
	                                $id		 = $db->getData( $rec, 'id' );
									$loginUserType = $TABLE_NAME[$i];
	                            	
									$sys	 = SystemUtil::getSystem( $loginUserType );
									$sys->loginProc( true , $loginUserType , $id );
	                            	
	                                SystemUtil::login($id,$loginUserType);
	                                break;
	                            }
	                        }
	                    }
					}else{
	                    Template::drawTemplate( $gm[ 'admin' ] , null , $loginUserType , $loginUserRank , 'login' , 'QUICK_FALED_DESIGN' );
	                    exit();
	                }
				}
			}
			
	        SystemUtil::innerLocation( "index.php" );
	        exit;
		}else{
			print System::getHead($gm,$loginUserType,$loginUserRank);
			
			$db		 = $gm[ $loginUserType ]->getDB();
			$table	 = $db->searchTable( $db->getTable(), 'id', '=', $LOGIN_ID );
		
			if($db->getRow($table) != 0){
				$rec	 = $db->getRecord( $table, 0 );
	
				$UTN = MobileUtil::GetMobileID();
				
	            if($UTN !== false){
					$db->setData( $rec, 'terminal', addslashes($UTN) );
					$db->updateRecord( $rec );
	                Template::drawTemplate( $gm[ $loginUserType ] , $rec , $loginUserType , $loginUserRank , $loginUserType , 'QUICK_DESIGN' );
				}else{
	                Template::drawTemplate( $gm[ $loginUserType ] , $rec , $loginUserType , $loginUserRank , 'set' , 'QUICK_FALED_DESIGN' );
				}
			}else{
	            Template::drawTemplate( $gm[ $loginUserType ] , null , $loginUserType , $loginUserRank , 'set' , 'QUICK_FALED_DESIGN' );
			}
		}
		print System::getFoot($gm,$loginUserType,$loginUserRank);
	}
	catch( Exception $e_ )
	{
		ob_end_clean();

		//�G���[���b�Z�[�W�����O�ɏo��
		$errorManager = new ErrorManager();
		$errorMessage = $errorManager->GetExceptionStr( $e_ );

		$errorManager->OutputErrorLog( $errorMessage );

		//��O�ɉ����ăG���[�y�[�W���o��
		$className = get_class( $e_ );
		ExceptionManager::DrawErrorPage($className );
	}

	ob_end_flush();

?>