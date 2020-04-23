<?php

	//���N���X //

	/**
		@brief �t�@�C���p�X�֌W�̃��[�e�B���e�B�N���X�B
	*/
	class PathUtil //
	{
		//������ //

		/**
			@brief     �D��f�B���N�g����ǉ�����B
			@details   ���΃p�X�ɂ��include/require��PathUtil�ɂ��p�X�C���̍ہA�J�����g�f�B���N�g�������D�悵�Ďg�p�����f�B���N�g����ǉ����܂��B
			@param[in] $iPath �C�ӂ̃f�B���N�g���p�X�B���݂��Ȃ��f�B���N�g���ł������ꍇ�͖�������܂��B
			@remarks   AddDeployPath�𕡐�����s�����ꍇ�A$iPath�͒P�ɐ擪�ɏ��ɒǉ�����Ă����܂��B�܂�A�Ō�Ɏ��s����AddDeployPath��$iPath���ł��D��x�̍����p�X�ɂȂ�܂��B
			@remarks   include './foobar.php' �̂悤�ɖ����I�ɃJ�����g�f�B���N�g������̑��΃p�X���w�肵�Ă���ꍇ�A���̊֐��Őݒ肵���p�X�͎g�p����Ȃ��_�ɒ��ӂ��Ă��������B\n
			           ���̂悤�ȕ\�L�� include 'foobar.php' �ɏC������K�v������܂��B�����include/require�̎d�l�ɂ����̂ł��B
			@remarks   ���̊֐��̐ݒ��ModifyLSTFilePath���ɂ����f����܂��B
		*/
		static function AddDeployPath( $iPath ) //
		{
			if( !is_dir( $iPath ) ) //���݂��Ȃ��f�B���N�g���̏ꍇ
				{ return; }

			$currentPath = get_include_path();

			if( FALSE !== strpos( $currentPath , ';' ) ) //�p�X��؂肪;�̏ꍇ(Windows)
				{ set_include_path( $iPath . ';' . $currentPath ); }
			else //�p�X��؂肪:�̏ꍇ(UNIX)
				{ set_include_path( $iPath . ':' . $currentPath ); }

			array_unshift( self::$DeployPaths , $iPath );
		}

		/**
			@brief     ���o�͂̂��߂̃t�@�C���p�X���C������B
			@param[in] $iPath       �C�ӂ̃t�@�C���p�X�B
			@param[in] $iModifyMode 'exists'���w�肵���ꍇ�A�D��f�B���N�g���̃t�@�C���p�X�����݂��Ȃ��ꍇ�͎��̃p�X����������悤�ɂȂ�܂��B
			@return    �C�����ꂽ�t�@�C���p�X�B
			@remarks   AddDepolyPath�Ŏw�肳�ꂽ�D��f�B���N�g��������ꍇ�͂��̃f�B���N�g�����̃p�X��Ԃ��܂��B�����łȂ��ꍇ�A$iPath�����̂܂ܕԂ��܂��B\n
			           ���݂���t�@�C�����������Ď擾�������ꍇ��$iModifyMode���w�肵�Ă��������B
		*/
		static function ModifyPath( $iPath , $iModifyMode = '' ) //
		{
			foreach( self::$DeployPaths as $deployPath ) //�S�Ă̗D��f�B���N�g��������
			{
				$deployPath = self::JoinPath( Array( $deployPath , $iPath ) );

				if( 'exists' != $iModifyMode ) //�t�@�C���̑��݊m�F���w�肳��Ă��Ȃ��ꍇ
					{ return $deployPath; }
				else if( is_file( $deployPath ) ) //�D��f�B���N�g�����Ƀt�@�C��������ꍇ
					{ return $deployPath; }
			}

			return $iPath;
		}

		/**
			@brief     �t�@�C���p�X����������B
			@param[in] $iPaths ��������t�@�C���p�X�z��B
			@return    �������ꂽ�t�@�C���p�X�B
		*/
		static function JoinPath( $iPaths ) //
		{
			$checkedPaths = Array();

			foreach( $iPaths as $path ) //�S�Ẵp�X������
			{
				$path = self::TrimPath( $path );

				if( !$path ) //�p�X���󔒂̏ꍇ
					{ continue; }

				$checkedPaths[] = $path;
			}

			return implode( '/' , $checkedPaths );
		}

		/**
			@brief     �t�@�C���p�X�̐擪�ɂ���./�Ɩ����ɂ���/����菜���B
			@param[in] $iPath �C�ӂ̃t�@�C���p�X�B
			@return    �g���~���O���ꂽ�t�@�C���p�X�B
		*/
		static function TrimPath( $iPath ) //
		{
			$iPath = preg_replace( '/^\.\//' , '' , $iPath );
			$iPath = preg_replace( '/\/$/'   , '' , $iPath );

			return $iPath;
		}

		/**
			@brief     �e���v���[�g�t�@�C���̃p�X��K�؂ɏC������B
			@param[in] $iTemplatePath �C�ӂ̃e���v���[�g�t�@�C���p�X�B
			@exception Logic �e���v���[�g�i�[�p�X������`�̎��_�ŌĂяo���ꂽ�ꍇ�B
			@return    �C�����ꂽ�e���v���[�g�t�@�C���p�X�B
		*/
		static function ModifyTemplateFilePath( $iTemplatePath ) //
		{
			global $template_path;

			if( !isset( $template_path ) ) //�e���v���[�g�p�X����`����Ă��Ȃ��ꍇ
				{ throw new LogicException(); }

			return self::ModifyLoadPath( $iTemplatePath , $template_path );
		}

		/**
			@brief     LST�t�@�C���̃p�X��K�؂ɏC������B
			@param[in] $iLSTPath �C�ӂ�LST�t�@�C���p�X�B
			@exception Logic LST�i�[�p�X������`�̎��_�ŌĂяo���ꂽ�ꍇ�B
			@return    �C�����ꂽLST�t�@�C���p�X�B
		*/
		static function ModifyLSTFilePath( $iLSTPath )
		{
			global $lst_path;

			if( !isset( $lst_path ) ) //LST�p�X����`����Ă��Ȃ��ꍇ
				{ throw new LogicException(); }

			return self::ModifyLoadPath( $iLSTPath , $lst_path );
		}

		/**
			@brief     TDB�t�@�C���̃p�X��K�؂ɏC������B
			@param[in] $iTDBPath �C�ӂ�TDB�t�@�C���p�X�B
			@exception Logic TDB�i�[�p�X������`�̎��_�ŌĂяo���ꂽ�ꍇ�B
			@return    �C�����ꂽTDB�t�@�C���p�X�B
		*/
		static function ModifyTDBFilePath( $iTDBPath )
		{
			global $tdb_path;

			if( !isset( $tdb_path ) ) //TDB�p�X����`����Ă��Ȃ��ꍇ
				{ throw new LogicException(); }

			return self::ModifyLoadPath( $iTDBPath , $tdb_path );
		}

		/**
			@brief     �C���f�b�N�X�t�@�C���̃p�X��K�؂ɏC������B
			@param[in] $iIndexPath �C�ӂ̃C���f�b�N�X�t�@�C���p�X�B
			@exception Logic �C���f�b�N�X�i�[�p�X������`�̎��_�ŌĂяo���ꂽ�ꍇ�B
			@return    �C�����ꂽ�C���f�b�N�X�t�@�C���p�X�B
		*/
		static function ModifyIndexFilePath( $iIndexPath )
		{
			global $index_path;

			if( !isset( $index_path ) ) //�C���f�b�N�X�p�X����`����Ă��Ȃ��ꍇ
				{ throw new LogicException(); }

			return self::ModifyLoadPath( $iIndexPath , $index_path );
		}

		/**
			@brief     System�N���X�t�@�C���̃p�X���擾����B
			@param[in] $iTableName            �擾������System�N���X�̃e�[�u�����B
			@param[in] $iPiroritizeModuleName �D�悵�Č������������W���[���p�X������ꍇ�́A���̃��W���[�����B
			@exception Logic System�N���X�i�[�p�X������`�̎��_�ŌĂяo���ꂽ�ꍇ�B
			@return    �C�����ꂽSystem�N���X�t�@�C���p�X�B
			@remarks   �ʏ�p�X�ƃ��W���[���p�X�ɓ�����System�N���X������ꍇ�́A$iPrioritizeModuleName�Ŏw�肳��Ă���ꍇ�������ʏ�p�X��D�悵�ĕԂ��܂��B
		*/
		static function ModifySystemFilePath( $iTableName , $iPrioritizeModuleName = null ) //
		{
			global $MODULES;
			global $system_path;

			if( !isset( $system_path ) ) //�V�X�e���p�X����`����Ă��Ȃ��ꍇ
				{ throw new LogicException(); }

			$basePath   = self::TrimPath( $system_path ) . '/' . $iTableName . 'System.php';
			$modifyPath = $basePath;

			if( $iPrioritizeModuleName ) //�D�惂�W���[���̎w�肪����ꍇ
			{
				$prioritizePath = 'module/' . $iPrioritizeModuleName . '/' . $basePath;

				if( is_file( $prioritizePath ) ) //�D�惂�W���[�����Ƀt�@�C��������ꍇ
					{ $modifyPath = $prioritizePath; }
			}

			if( !is_file( $modifyPath ) ) //�D�惂�W���[���E�ʏ�p�X�Ƀt�@�C�����Ȃ��ꍇ
			{
				if( isset( $MODULES ) ) //���W���[���z�񂪒�`����Ă���ꍇ
				{
					foreach( array_keys( $MODULES ) as $moduleName ) //�S�Ẵ��W���[����������
					{
						$modulePath = 'module/' . $moduleName . '/' . $basePath;

						if( is_file( $modulePath ) ) //���W���[�����Ƀt�@�C��������ꍇ
						{
							$modifyPath = $modulePath;

							break;
						}
					}
				}
			}

			foreach( self::$DeployPaths as $deployPath ) //�S�Ă̗D��f�B���N�g��������
			{
				$deployPath = self::JoinPath( Array( self::TrimPath( $deployPath ) , $modifyPath ) );

				if( is_file( $deployPath ) ) //�D��f�B���N�g�����Ƀt�@�C��������ꍇ
				{
					$modifyPath = $deployPath;

					break;
				}
			}

			return $modifyPath;
		}

		//���������� //

		/**
			@brief     �V�X�e���t�@�C���̃p�X���C������B
			@param[in] $iPath         �C�ӂ̃t�@�C���p�X�B
			@param[in] $iModifyPrefix $iPath��O�u�C�����邽�߂̃p�X�B���W���[��lst�ȂǗ�O�I�ȃp�X�W�J���K�v�ȏꍇ�Ɏg�p���܂��B
			@return    �C�����ꂽ�t�@�C���p�X�B
			@remarks   $iPath���Ƀ��W���[���ϐ�([moduleName])���܂܂��ꍇ�̓��W���[���p�X�Ƃ��ēW�J���܂��B�������A���W���[���t�@�C�������݂��Ȃ��ꍇ�͒ʏ�p�X��Ԃ��܂��B
			@remarks   AddDepolyPath�Ŏw�肳�ꂽ�D��f�B���N�g�����Ƀt�@�C�������݂���ꍇ�͂�����̃p�X��Ԃ��܂��B\n
			           �t�@�C�������݂��Ȃ��ꍇ�͒ʏ�p�X��Ԃ��_�ɒ��ӂ��Ă��������B
		*/
		private static function ModifyLoadPath( $iPath , $iModifyPrefix = '' ) //
		{
			$iPath         = self::TrimPath( $iPath );
			$iModifyPrefix = self::TrimPath( $iModifyPrefix );

			$modifyPath = self::JoinPath( Array( $iModifyPrefix , $iPath ) );

			if( preg_match( '/(.*)\[(\w+)\]\/?(.*)/' , $iPath , $matches ) ) //�p�X���Ƀ��W���[���ϐ�������ꍇ
			{
				$modulePath = self::JoinPath( Array( $matches[ 1 ] , 'module' , $matches[ 2 ] , $iModifyPrefix , $matches[ 3 ] ) );

				if( is_file( $modulePath ) ) //���W���[�����Ƀt�@�C��������ꍇ
					{ $modifyPath = $modulePath; }
			}

			foreach( self::$DeployPaths as $deployPath ) //�S�Ă̗D��f�B���N�g��������
			{
				$deployPath = self::JoinPath( Array( $deployPath , $modifyPath ) );

				if( is_file( $deployPath ) ) //�D��f�B���N�g�����Ƀt�@�C��������ꍇ
				{
					$modifyPath = $deployPath;

					break;
				}
			}

			return $modifyPath;
		}

		//���ϐ� //

		private static $DeployPaths = Array(); ///<AddDeployPath�Œǉ������p�X�̔r��B
	}
