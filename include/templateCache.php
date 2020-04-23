<?php

	//���N���X //

	/**
		@brief �e���v���[�g�L���b�V������N���X�B
	*/
	class TemplateCache //
	{
		//������ //

		/**
			@brief �L���b�V������������������B
		*/
		static function Initialize() //
			{ self::$HasPost = ( 0 < count( $_POST ) ); }

		/**
			@brief  ���݂�URL�̃L���b�V����ǂݍ���ŏo�͂���B
			@retval �L���b�V�����g�p�ł���ꍇ�B
			@retval �L���b�V�����g�p�ł��Ȃ��ꍇ�B
		*/
		static function LoadCache() //
		{
			global $loginUserType;
			global $NOT_LOGIN_USER_TYPE;
			global $USE_TEMPLATE_CACHE;
            global $controllerName;

			if( $controllerName == "Register" )
				{ return false; }

			if( is_null( self::$NoCache ) )
				{ self::$NoCache = !$USE_TEMPLATE_CACHE; }

			if( $NOT_LOGIN_USER_TYPE != $loginUserType )
				{ return false; }

			if( self::$HasPost )
				{ return false; }

			if( self::$NoCache )
				{ return false; }

			$cacheFile = self::GetCacheFilePath();
			$usingFile = self::GetCacheFilePath() . '.utl';

			if( is_file( $cacheFile ) && is_file( $usingFile ) )
			{
				$cacheTime = filemtime( $cacheFile );

				if( self::$MaxCacheTime < time() - $cacheTime )
					{ return false; }

				if( self::GetDBUpdateTime() > $cacheTime )
					{ return false; }

				$usingList = explode( "\n" , file_get_contents( $usingFile ) );

				foreach( $usingList as $usingFile )
				{
					if( !is_file( $usingFile ) )
						{ continue; }

					if( filemtime( $usingFile ) > $cacheTime )
						{ return false; }
				}

				print file_get_contents( $cacheFile );

				self::$CacheUsed = true;

				return true;
			}
			else
				{ return false; }
		}

		/**
			@brief ���݂�URL�̃L���b�V����ۑ�����B
		*/
		static function SaveCache( $iSource ) //
		{
			global $loginUserType;
			global $NOT_LOGIN_USER_TYPE;
			global $USE_TEMPLATE_CACHE;
            global $controllerName;

			if( $controllerName == "Register" )
				{ return false; }

			if( is_null( self::$NoCache ) )
				{ self::$NoCache = !$USE_TEMPLATE_CACHE; }

			if( $NOT_LOGIN_USER_TYPE != $loginUserType )
				{ return false; }

			if( self::$HasPost )
				{ return false; }

			if( self::$NoCache )
				{ return false; }

			file_put_contents( self::GetCacheFilePath() , $iSource );
			file_put_contents( self::GetCacheFilePath() . '.utl' , implode( "\n" , array_keys( self::$UsingList ) ) );

			if( !chmod( self::GetCacheFilePath() , 0777 ) )
				{ chmod( self::GetCacheFilePath() , 0707 ); }

			if( !chmod( self::GetCacheFilePath() . '.utl' , 0777 ) )
				{ chmod( self::GetCacheFilePath() . '.utl' , 0707 ); }
		}

		//���f�[�^�ύX //

		/**
			@brief �f�[�^�x�[�X�̍ŏI�X�V�������X�V����B
		*/
		static function SetDBUpdateTime() //
			{ return; file_put_contents( 'templateCache/dbupdatetime' , time() ); }

		/**
			@brief     �g�p�e���v���[�g���X�g�Ƀe���v���[�g����ǉ�����B
			@param[in] $iTemplateFile �e���v���[�g�t�@�C�����B
		*/
		static function Using( $iTemplateFile ) //
			{ self::$UsingList[ $iTemplateFile ] = true; }

		//���f�[�^�擾 //

		/**
			@brief  ���݂�URL�̃L���b�V���t�@�C���̃p�X���擾����B
			@return �L���b�V���t�@�C���̃p�X�B
		*/
		static function GetCacheFilePath() //
		{
			global $terminal_type;
			global $sp_mode;
            global $controllerName;

			if( $_SERVER[ 'QUERY_STRING' ] )
				{ $url = $_SERVER[ 'SCRIPT_NAME' ] . '?' . $_SERVER[ 'QUERY_STRING' ]; }
			else
				{ $url = $_SERVER[ 'SCRIPT_NAME' ]; }

			$filePath = md5( $url );

			$directory = 'templateCache';
			if( !is_dir( $directory ) )
				{ mkdir( $directory ); }

			$directoryList[] = $controllerName;

			if( isset($_GET['type']) )
				{ $directoryList[] = $_GET['type']; }

			$mode = 'pc';
			if( $sp_mode ){ $mode = 'sp'; }
			$directoryList[] = $mode;

			foreach( $directoryList as $tmp )
			{
				$directory .= '/'.$tmp;
				if( !is_dir( $directory ) )
					{ mkdir( $directory ); }
			}

			return $directory.'/'. $terminal_type . $filePath;
		}

		/**
			@brief  �f�[�^�x�[�X�̍ŏI�X�V�������X�V����B
			@return �f�[�^�x�[�X�̍ŏI�X�V�����B
		*/
		static function GetDBUpdateTime() //
		{
			if( is_file( 'templateCache/dbupdatetime' ) )
				{ return file_get_contents( 'templateCache/dbupdatetime' ); }
			else
				{ return 0; }
		}

		//���ϐ� //

		static $CacheUsed    = false;   ///<�L���b�V�����o�͍ς݂̏ꍇ��true�B
		static $NoCache      = null;    ///<�L���b�V���X�V�𖳌��ɂ���ꍇ��true�B
		static $MaxCacheTime = 600;     ///<�L���b�V���̗L������(�b)
		static $UsingList    = Array(); ///<�g�p���ꂽ�e���v���[�g�t�@�C���B
		static $HasPost      = false;   ///<POST�f�[�^������ꍇ��true�B
	}
