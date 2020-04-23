<?php
	
	/**
		@brief   �G���[�Ǘ��N���X�B
		@details �G���[���O�̏o�͂Ɨ�O�ւ̕ϊ����Ǘ����܂��B
	*/
	class ErrorManager
	{
		private $errorToException = null; ///<��O�ϊ��ݒ�
		private $shutdownErrorLog = null; ///<�v���I�G���[�̃��O�ݒ�
		private $errorLogFile     = null; ///<���O���o�͂���t�@�C����
		private $workDirectory    = null; ///<�X�N���v�g�̓���p�X
		private $maxlogFileSize   = 20971520; ///<���O�t�@�C���̍ő�T�C�Y

		/**
			@brief �R���X�g���N�^�B
		*/
		function __Construct()
		{
			global $EXCEPTION_CONF;

			$this->errorToException = $EXCEPTION_CONF[ 'UseErrorToException' ];
			$this->shutdownErrorLog = $EXCEPTION_CONF[ 'UseShutdownErrorLog' ];
			$this->errorLogFile     = $EXCEPTION_CONF[ 'ErrorLogFile' ];
			$workDir = $EXCEPTION_CONF[ 'WorkDirectory' ];
			if(substr($workDir,-1) !=='/' && strlen($workDir)>0){
				$workDir .= '/';
			}
			$this->workDirectory    = $workDir;
		}

		/**
			@brief     �G���[�n���h���B
			@exception ErrorException ��O�ϊ����L���ȏꍇ�B
			@exception Exception      ��O�ϊ����L���ŁAErrorException�N���X�����݂��Ȃ��ꍇ�B
			@details   �G���[���b�Z�[�W�����������ꍇ�̏������L�q���܂��B
		*/
		function ErrorHandler( $errNo_ , $errStr_ , $errFile_ , $errLine_ , $errContext_ )
		{
			if( class_exists( 'ErrorException' ) )
				$exception = new ErrorException( $errStr_ );
			else
				$exception = new Exception( $errStr_ );

			$excStr = $this->GetExceptionStr( $exception );

			$this->OutputErrorLog( $excStr );

			if( $this->errorToException )
				throw $exception;
		}

		/**
			@brief   �V���b�g�_�E���n���h���B
			@details �X�N���v�g�I�����̏������L�q���܂��B
		*/
		function ShutdownHandler()
		{
			if( $this->shutdownErrorLog )
			{
				$errStr = $this->GetFatalErrorStr();

				if( !is_null( $errStr ) )
					$this->OutputErrorLog( $errStr , $this->workDirectory . $this->errorLogFile );
			}
		}

		/**
			@brief ��O�ϊ��̗L���E������ݒ肷��B
			@param $usage_ �G���[���b�Z�[�W���O�ɕϊ�����ꍇ��true�B�ϊ����Ȃ��ꍇ��false�B
		*/
		function SetErrorToException( $usage_ )
		{
			$this->ErrorToException = $usage_;
		}

		/**
			@brief   �G���[���b�Z�[�W�擾�B
			@details ��O���G���[���b�Z�[�W�ɕϊ����Ď擾���܂��B
			@param   $e_ ��O�I�u�W�F�N�g�B
		*/
		function GetExceptionStr( $e_ )
		{
			//�X�^�b�N�g���[�X���擾����
			$array = $e_->getTrace();
			krsort( $array );

			$result  = "\t" . $e_->getMessage() . "\n\t\t" . preg_replace( '/(.*)\\\\([^\\\\]+)$/' , '($1) $2' , str_replace( getcwd() . '\\' , '' , $e_->getFile() ) ) . ' ' . $e_->getLine() . "\n\n";
			$result .= '��trace : ' . "\n\n";

			$row = count( $array );

			//�o�b�N�g���[�X�ƍ\�����Ⴄ�̂ŁA���������炵�đΉ�
			for( $i = $row - 1 ; $i > 0 ; $i-- )
				$array[ $i ][ 'args' ] = $array[ $i - 1 ][ 'args' ];

			//�Ăяo�����ɐ��`���Ċi�[
			foreach( $array as $trace )
			{
				if( array_key_exists( 'file' , $trace ) ){
					$file = sprintf( '%s %04d' , preg_replace( '/(.*)\\\\([^\\\\]+)$/' , '($1) $2' , str_replace( getcwd() . '\\' , '' , $trace[ 'file' ] ) ) , $trace[ 'line' ] );
				}else{
					$file = sprintf( '%s %04d' , preg_replace( '/(.*)\\\\([^\\\\]+)$/' , '($1) $2' , str_replace( getcwd() . '\\' , '' , $trace[ 'args' ][2] ) ) , $trace[ 'args' ][3] );
				}

				if( array_key_exists( 'function' , $trace ) )
				{
					$result .= "\t" . $trace[ 'function' ] . "\n";
					$result .= "\t\t" . $file . "\n";
				}
				else
				{
					$result .= "\t" . $trace[ 'line' ] . "\n";
					$result .= "\t\t" . $file . "\n";
				}

				//����
				if( array_key_exists( 'args' , $trace ) && count( (array)$trace[ 'args' ] ) )
				{
					foreach( $trace[ 'args' ] as $key => $value )
					{
						if( is_object( $value ) )
							$result .= "\t\t\t" . sprintf( 'object   : %s' , get_class( $value ) ) . "\n";
						else if( is_array( $value ) )
							$result .= "\t\t\t" . sprintf( 'array    : %s' , count( $value ) ) . "\n";
						else
							$result .= "\t\t\t" . sprintf( '%-8s : %s' , gettype( $value ) , $value ) . "\n";
					}
				}

				$result .= "\n";
			}

			return $result;
		}

		/**
			@brief   �G���[���b�Z�[�W�擾�B
			@details ��O���G���[���b�Z�[�W�ɕϊ����Ď擾���܂��B
			@param   $e_ ��O�I�u�W�F�N�g�B
		*/
		function GetFatalErrorStr()
		{
			if( function_exists( 'error_get_last' ) )
			{
				$error = error_get_last();
			}

			if( is_null( $error ) )
				return null;

			switch( $error[ 'type' ] )
			{
				case E_ERROR :
				case E_PARSE :
				case E_CORE_ERROR :
				case E_CORE_WARNING :
				case E_COMPILE_ERROR :
				case E_COMPILE_WARNING :
					$result  = 'fatal error : ' . $error[ 'message' ] . "\n";
					$result .= sprintf( '%s,%04d' , $error[ 'file' ] , $error[ 'line' ] ) . "\n";

					return $result;

				default :
					return null;
			}
		}

		/**
			@brief �G���[���O���o�͂���B
			@param $str_      �G���[���b�Z�[�W�B
			@param $filePath_ �o�͂���t�@�C���̃p�X�B
		*/
		function OutputErrorLog( $str_ , $filePath_ = null )
		{
			if( $filePath_ )
				{ $path = $filePath_; }
			else
				{ $path = $this->errorLogFile; }

			$fp = fopen( $path , 'a' );

			if( $fp )
			{
				fputs( $fp , date( '��Y/n/j G:i:s' . "\n\n" ) );
				fputs( $fp , $str_ . "\n" );
				fputs( $fp , '-----------------------------------------------------' . "\n\n" );
				fclose( $fp );

				if( $this->maxlogFileSize < filesize( $this->errorLogFile ) ) //���O�t�@�C���̍ő�T�C�Y�𒴂��Ă���ꍇ
				{
					$nowDateString = date( '_Y_m_d_H_i_s' );

					rename( $path , $path . $nowDateString );

					$fp = fopen( $path , 'a' );

					fclose( $fp );
					chmod( $path, 0666 );
				}
			}
		}
	}

	//�n���h���o�^
	function ErrorManager_ErrorHandler( $errNo_ , $errStr_ , $errFile_ , $errLine_ , $errContext_ )
	{
		$object = new ErrorManager();
		$object->ErrorHandler( $errNo_ , $errStr_ , $errFile_ , $errLine_ , $errContext_ );
	}

	function ErrorManager_ShutdownHandler()
	{
		$object = new ErrorManager();
		$object->ShutdownHandler();
	}

	set_error_handler( 'ErrorManager_ErrorHandler' , $EXCEPTION_CONF[ 'ErrorHandlerLevel' ] );
	register_shutdown_function( 'ErrorManager_ShutdownHandler' );
	