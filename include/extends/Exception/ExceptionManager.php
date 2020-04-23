<?php

	include_once 'include/extends/Exception/Exception.php';

	/**
		@brief   ��O���[�e�B���e�B�N���X�B
		@details ��O�Ɋւ���֐����܂Ƃ߂��N���X�ł��B
	*/
	class ExceptionManager
	{
		var  $_DEBUG	 = DEBUG_FLAG_EXCEPTION;

		function ExceptionHandler( $exception ){
			global $EXCEPTION_CONF;

			ob_end_clean();

			//�G���[���b�Z�[�W�����O�ɏo��
			$className = get_class( $exception );

			if( !in_array( $className , $EXCEPTION_CONF[ 'SecretExceptionType' ] ) )
			{
				$errorManager = new ErrorManager();
				$errorMessage = $errorManager->GetExceptionStr( $exception );
				$errorManager->OutputErrorLog( $errorMessage );
			}

			ExceptionManager::setHttpStatus( $className );

			//��O�ɉ����ăG���[�y�[�W���o��
			if( $this->_DEBUG ){ d("DrawErrorPage:class ${className},message ".$exception->getMessage() ); }
			ExceptionManager::DrawErrorPage( $className );
		}

		/**
			@brief   ��O�G���[�y�[�W���o�͂���B
			@details ��O�̎�ނɉ����ăG���[�e���v���[�g���o�͂��܂��B\n
			         �Ή�����e���v���[�g��������Ȃ��ꍇ�͕W���̃G���[�e���v���[�g���o�͂���܂��B
			@param   $className_ ��O�I�u�W�F�N�g�̃N���X���B
			@remarks ��O�G���[�e���v���[�g��target�ɏ������̃N���X���Alabel��EXCEPTION_DESIGN���w�肵�܂��B
		*/
		static function DrawErrorPage( $className )
		{
			global $gm;
			global $loginUserType;
			global $loginUserRank;
			global $template_path;

			try
			{
				ob_start();

				System::$head = false;
				System::$foot = false;

				if( $_GET[ 'type' ] && !is_array( $_GET[ 'type' ] ) && $gm[ $_GET[ 'type' ] ] )
					$tGM = SystemUtil::getGMforType( $_GET[ 'type' ] );
				else
					$tGM = SystemUtil::getGMforType( 'system' );

				print System::getHead( $gm , $loginUserType , $loginUserRank );

				//��O�I�u�W�F�N�g�̃e���v���[�g����������
				
				$template = $template_path . 'other/exception/' . $className . '.html';
				
				if( !file_exists( $template ) ){
					$template = Template::getTemplate( $loginUserType , $loginUserRank , $className , 'EXCEPTION_DESIGN' );
				}
	
				if( $template && file_exists( $template ) )
					print $tGM->getString( $template );
				else
				{
					//Exception�I�u�W�F�N�g�̃e���v���[�g����������
					if( 'Exception' != $className )
						$template = Template::getTemplate( $loginUserType , $loginUserRank , 'exception' , 'EXCEPTION_DESIGN' );

					if( $template && file_exists( $template ) )
						print $tGM->getString( $template );
					else
						Template::drawErrorTemplate();
				}

				print System::getFoot( $gm , $loginUserType , $loginUserRank );

				System::flush();
			}
			catch( Exception $e_ )
			{
				ob_end_clean();

				print System::getHead( $gm , $loginUserType , $loginUserRank );
				Template::drawErrorTemplate();
				print System::getFoot( $gm , $loginUserType , $loginUserRank );
				System::flush();
			}
		}


		function setHttpStatus($className)
		{
			$header = "";
			switch($className)
			{
			case 'InvalidQueryException':
				$header = 'HTTP/1.0 400 Bad Request';
				break;
			case 'IllegalAccessException':
				$header = 'HTTP/1.0 403 Forbidden';
				break;
			case 'RecordNotFoundException':
				$header = 'HTTP/1.0 404 Not Found';
				break;
			}

			if( strlen($header) > 0 ) { header( $header ); }
		}
	}
	
	//�n���h���o�^
	function ExceptionManager_ExceptionHandler( $e )
	{
		$object = new ExceptionManager();
		$object->ExceptionHandler( $e );
	}

	set_exception_handler( 'ExceptionManager_ExceptionHandler' );
	 
?>
