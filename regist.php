<?php

	/*******************************************************************************************************
	 * <PRE>
	 *
	 * regist.php - �ėp�v���O����
	 * �V�K�o�^�����B
	 *
	 * </PRE>
	 *******************************************************************************************************/

	ob_start();

	try
	{
		include_once 'custom/head_main.php';

		//�p�����[�^�`�F�b�N
		ConceptCheck::IsEssential( $_GET , Array( 'type' ) );
		ConceptCheck::IsNotNull( $_GET , Array( 'type' ) );
		ConceptCheck::IsScalar( $_GET , Array( 'type' , 'copy' ) );
		ConceptCheck::IsScalar( $_POST , Array( 'post' , 'step' , 'back' ) );

		if( !$gm[ $_GET[ 'type' ] ] )
			throw new IllegalAccessException( $_GET[ 'type' ] . '�͒�`����Ă��܂���' );

		if( $THIS_TABLE_IS_NOHTML[ $_GET[ 'type' ] ] )
			throw new IllegalAccessException( $_GET[ 'type' ] . '�͑���ł��܂���' );
		//�p�����[�^�`�F�b�N�����܂�

		print System::getHead($gm,$loginUserType,$loginUserRank);
		System::$checkData	 = new CheckData( $gm, false, $loginUserType, $loginUserRank );
	
		$sys	 = SystemUtil::getSystem( $_GET["type"] );
	
		if(   $THIS_TABLE_IS_NOHTML[ $_GET['type'] ] || !isset(  $gm[ $_GET['type'] ]  )   )
		{
			$sys->drawRegistFaled( $gm, $loginUserType, $loginUserRank );
		}
		else
		{
			$db		 = $gm[ $_GET['type'] ]->getDB();
			
	        if(isset($_POST['back']))
			{
				$_POST['post'] = "";
	
				if($_POST['step'])
					$_POST['step']--;
			}
	
			// �o�^�����̓t�H�[����`��
			if(  !isset( $_POST['post'] ) || !strlen($_POST['post']) )
			{
				if(!$_POST['step'])
					$_POST['step'] = 1;
				
				if(strlen($_GET['copy']) && $sys->copyCheck( $gm, $loginUserType, $loginUserRank ))
				{
					$rec	 = $db->selectRecord($_GET['copy']);
					$gm[ $_GET['type'] ]->setForm( $rec );
				}
				else
				{
					$gm[ $_GET['type'] ]->setForm( $_GET );
					$rec	 = $db->getNewRecord( $_GET );
				}
	
				$gm[ $_GET['type'] ]->addHiddenForm( 'post', 'check' );
				$gm[ $_GET['type'] ]->addHiddenForm( 'step', $_POST['step'] );
	
				//�t�H�[����S��hidden�Œǉ�
				foreach($gm[ $_GET['type'] ]->colStep as $key => $value)
				{
					if($value && $value < $_POST['step'] )
						$gm[ $_GET['type'] ]->addHiddenForm( $key , ($_POST['back'] ? $_POST[$key] : $_GET[$key]) );
				}
	
				$sys->drawRegistForm( $gm, $rec, $loginUserType, $loginUserRank );
			}
			else
			{
	            // �o�^���m�F��ʂ�`��
	            if( $_POST['post'] == 'check' )
				{
				
	                // ���͓��e�m�F
			        $check	 = $sys->registCheck( $gm, false, $loginUserType, $loginUserRank );
	
					if($check)
						$_POST[ 'step' ]++;
	
					if($gm[ $_GET[ 'type' ] ]->maxStep >= 2 && $gm[ $_GET[ 'type' ] ]->maxStep + 1 > $_POST[ 'step' ])
						$check = false;
	
					$rec	 = $db->getNewRecord( $_POST );
					
	              	if( $check )
					{// �V����POST���e�𗘗p���ă��R�[�h���쐬����B
	
						$sys->registProc( $gm, $rec, $loginUserType, $loginUserRank ,true);
	
						$gm[ $_GET['type'] ]->setHiddenFormRecord( $rec );
	
						// �o�^���e�m�F�y�[�W���o�́B
						$gm[ $_GET['type'] ]->addHiddenForm( 'post', 'regist' );
	                    $gm[ $_GET['type'] ]->addHiddenForm( 'step', $_POST['step'] );
						$sys->drawRegistCheck( $gm, $rec, $loginUserType, $loginUserRank );
					}
					else
					{// ���͓��e�ɕs��������ꍇ
						//$gm[ $_GET['type'] ]->setHiddenFormRecord( $rec );
	                    $gm[ $_GET['type'] ]->addHiddenForm( 'post', 'check' );
	                    $gm[ $_GET['type'] ]->addHiddenForm( 'step', $_POST['step'] );
	                    
						$gm[ $_GET['type'] ]->setForm( $rec );
	
						///step�̈قȂ鍀�ڂ�S��hidden�Œǉ�
						foreach($gm[ $_GET['type'] ]->colStep as $key => $value)
						{
							if($value && $value < $_POST['step'])
								$gm[ $_GET['type'] ]->addHiddenForm( $key , $_POST[$key] );
						}
	
	                    $sys->drawRegistForm( $gm, $rec, $loginUserType, $loginUserRank );
	                }
	            }
				else if( $_POST['post'] == 'regist'  )
				{ // �o�^���s����
	                // �V����POST���e�𗘗p���ă��R�[�h���쐬����B
	                $rec	 = $db->getNewRecord( $_POST );
	                
	                $check	 = $sys->registCompCheck( $gm, $rec ,$loginUserType, $loginUserRank);
	                
	                if( $check )
	                {
	                    $sys->registProc( $gm, $rec, $loginUserType, $loginUserRank );
	                    
						if( $THIS_TABLE_IS_USERDATA[ $_GET[ 'type' ] ] )
							{ $db->setData( $rec , 'pass' , SystemUtil::encodePassword( $db->getData( $rec , 'pass' ) , $PASSWORD_MODE ) ); }

	                    // ���R�[�h��ǉ����܂��B
	                    $db->addRecord($rec);
	                    
	                    $sys->registComp( $gm, $rec, $loginUserType, $loginUserRank );
	                    
	                    // �o�^�����y�[�W���o�͂��܂��B
	                    $sys->drawRegistComp( $gm, $rec, $loginUserType, $loginUserRank );
	                }
	                else
	                {
	                    $sys->drawRegistFaled( $gm, $loginUserType, $loginUserRank );
	                }
	            }
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