<?php

	/**
		@brief   �x�[�X�R���Z�v�g�N���X�B
		@details �R���Z�v�g�N���X���`���邽�߂̊�{�@�\���������܂��B
		@author  ���� ����
		@version 1.0
		@ingroup Utility
	*/
	class ConceptBase
	{
		//����O

		/**
			@brief     �R���Z�v�g�Ɉᔽ���Ă���ꍇ�͗�O���X���[����B
			@details   ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li $iClassName_ ��O�̌^�B�ȗ�����Exception���g�p����܂��B
				@li $iMessage_   ��O���b�Z�[�W�B
			@attention ��O�̌^�͐ڔ��qException��t�����Ɏw�肵�Ă��������B��Ƃ��� RuntimeException ���w�肷��ꍇ�� 'Runtime' �ƂȂ�܂��B
			@remarks   ���̃��\�b�h���Ăяo���ƁA�R���Z�v�g�̕]���͏���������܂��B\n
		*/
		static function OrThrow()
		{
			if( self::$IsFailed ) //�R���Z�v�g�Ɉᔽ���Ă���ꍇ
			{
				List( $iClassName_ , $iMessage_ ) = func_get_args();

				$exception = self::CreateExceptionObject( $iClassName_ , $iMessage_ );

				self::ClearJudge();

				throw $exception;
			}

			self::ClearJudge();
		}

		//���]��

		/**
			@brief �R���Z�v�g�̕]��������������B
		*/
		protected static function ClearJudge()
		{
			self::$SuccessCount = 0;
			self::$FailedCount  = 0;
			self::$IsFailed     = false;
			self::$FailedArgs   = Array();
		}

		/**
			@brief   �R���Z�v�g��]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li $iTerm_ �R���Z�v�g�̕]���l�B
				@li $iArg_  �R���Z�v�g�̕]�������B
			@attention �R���Z�v�g�̕]���� OrThrow �ɔ��f����ɂ� UnionJudge ���Ăяo���K�v������܂��B
		*/
		protected static function Judge()
		{
			List( $iTerm_ , $iArg_ ) = func_get_args();

			if( $iTerm_ ) //�R���Z�v�g�ɓK�����Ă���ꍇ
				{ ++self::$SuccessCount; }
			else //�R���Z�v�g�Ɉᔽ���Ă���ꍇ
			{
				++self::$FailedCount;

				if( 2 <= func_num_args() ) //�]���������n����Ă���ꍇ
					{ self::$FailedArgs[] = $iArg_; }
			}
		}

		/**
			@brief     �R���Z�v�g�̕]������������B
			@exception InvaliidArgumentException �s���Ȉ������w�肵���ꍇ�B
			@param[in] $iUnionMode_ �������[�h�B
				@li and �R���Z�v�g�̕]���̘_���ς����B
				@li or  �R���Z�v�g�̕]���̘_���a�����B
		*/
		protected static function UnionJudge( $iUnionMode_ )
		{
			switch( $iUnionMode_ ) //�������@�ŕ���
			{
				case 'and' :
				{
					if( self::$FailedCount ) //�ᔽ������ꍇ
						{ self::$IsFailed = true; }

					break;
				}

				case 'or' :
				{
					if( !self::$SuccessCount ) //�K����1���Ȃ��ꍇ
						{ self::$IsFailed = true; }

					break;
				}

				default :
					{ throw new InvalidArgumentException( '�s���Ȍ������@���w�肳��܂���[' . $iUnionMode_ . ']' ); }
			}

			self::$SuccessCount = 0;
			self::$FailedCount  = 0;
		}

		//������

		/**
			@brief  �ᔽ�������X�g�����b�Z�[�W������B
			@return ���b�Z�[�W�B
		*/
		private static function CreateErrorArgsMessage()
		{
			if( count( self::$FailedArgs ) ) //�ᔽ�������X�g�����݂���ꍇ
			{
				$argMessages = Array();

				foreach( self::$FailedArgs as $arg ) //�ᔽ����������
				{
					if( is_object( $arg ) ) //�������I�u�W�F�N�g�̏ꍇ
						{ $argMessages[] = 'object(' . get_class( $arg ) . ')'; }
					else if( is_array( $arg ) ) //�������z��̏ꍇ
						{ $argMessages[] = 'array(' . count( $arg ) . ')'; }
					else if( is_bool( $arg ) ) //�������^�U�l�̏ꍇ
						{ $argMessages[] = 'boolean(' . ( $arg ? 'true' : 'false' ) . ')'; }
					else if( is_null( $arg ) ) //������null�̏ꍇ
						{ $argMessages[] = 'NULL'; }
					else //���������̑��̌^�̏ꍇ
						{ $argMessages[] = $arg; }
				}

				return '[' . join( '][' , $argMessages ) . ']';
			}
			else
				{ return ''; }

		}

		/**
			@brief     �G���[���b�Z�[�W�𐶐�����B
			@param[in] $iMessage_ �x�[�X���b�Z�[�W�B
			@return    ���b�Z�[�W�B
		*/
		private static function CreateErrorMessage( $iMessage_ )
		{
			$message  = self::$ErrorMessage;
			$message .= self::CreateErrorArgsMessage();

			if( $iMessage_ ) //�x�[�X���b�Z�[�W�����݂���ꍇ
				{ $message .= ' : ' . $iMessage_; }

			return $message;
		}

		/**
			@brief   ��O�I�u�W�F�N�g�𐶐�����B
			@param   $iType_    ��O�̌^�B
			@param   $iMessage_ ��O���b�Z�[�W�B
			@return  ��O�I�u�W�F�N�g�B
			@remarks $iType_ �N���X��������Ȃ��ꍇ�� Exception �I�u�W�F�N�g����������܂��B
		*/
		private static function CreateExceptionObject( $iType_ , $iMessage_ )
		{
			if( is_string( $iType_ ) ) //��O�̌^���w�肳��Ă���ꍇ
			{
				$iType_ .= 'Exception';
				

				if( !class_exists( $iType_ ) ) //�N���X�����݂��Ȃ��ꍇ
					{ $iType_ = 'Exception'; }
			}
			else //��O�̌^���w�肳��Ă��Ȃ��ꍇ
				{ $iType_ = 'Exception'; }

			$iMessage_ = self::CreateErrorMessage( $iMessage_ );

			return new $iType_( $iMessage_ );
		}

		//���p�����[�^�擾

		/**
			@brief  �C���X�^���X���擾����B
			@return ConceptBase �N���X�̃C���X�^���X�B
		*/
		protected static function Instance()
		{
			if( !$Instance ) //�C���X�^���X����������Ă��Ȃ��ꍇ
				{ $Instance = new ConceptBase(); }

			return $Instance;
		}

		//���p�����[�^�ύX

		/**
			@brief     �G���[���b�Z�[�W��ݒ肷��B
			@param[in] $iMessage_ �G���[���b�Z�[�W�B
		*/
		protected static function SetErrorCaseMessage( $iMessage_ )
			{ self::$ErrorMessage = $iMessage_; }

		//���ϐ�

		private static $Instance      = null;    ///<�C���X�^���X���i�[����ϐ�
		private static $SuccessCount  = 0;       ///<�K���R���Z�v�g�̐�
		private static $FailedCount   = 0;       ///<�ᔽ�R���Z�v�g�̐�
		private static $IsFailed      = false;   ///<�R���Z�v�g�ᔽ�t���O
		private static $FailedArgs    = Array(); ///<�ᔽ�������X�g
		private static $ErrorMessage  = '';      ///<�G���[���b�Z�[�W
	}
?>