<?php

	include_once 'include/extends/Exception/ConceptBase.php';
	/**
		@brief �R���Z�v�g�N���X�B
		@details �X�N���v�g�̃R���Z�v�g��]�����A�]�����^�ɂȂ�Ȃ��ꍇ�͗�O���X���[���܂��B\n
		         �S�Ă̕]�����\�b�h�� ConceptBase �N���X�̃C���X�^���X��Ԃ��܂��B\n
		         ���\�b�h�`�F�C�����g���āA�]�����\�b�h -> OrThrow �̏��ŌĂяo���Ă��������B
		@note    �]�����\�b�h�͎��̖����K���ɏ]���Ē�`����܂��B��O�� IsFalse , IsAnyFalse �ł��B
			@li IsFoo    �������S��Foo�ł��邱�Ƃ�]������B
			@li IsNotFoo �������S��Foo�ł͂Ȃ����Ƃ�]������B
			@li IsAnyFoo �����̂����ꂩ��Foo�ł��邱�Ƃ�]������B
		@author  ���� ����
		@version 1.0
		@ingroup Utility
	*/
	class Concept extends ConceptBase
	{
		//��and�]��

		/**
			@brief   �S�Ă̈������z��ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsArray()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�͔z��ł͂���܂���' );
			return self::JudgeInType( $iArgs_ , 'array' , 'and' );
		}

		/**
			@brief   �S�Ă̈������w��N���X�̃I�u�W�F�N�g�ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0   �N���X���B
				@li 1~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsClass()
		{
			$iArgs_    = func_get_args();
			$className = array_shift( $iArgs_ );

			parent::ClearJudge();
			parent::SetErrorCaseMessage( '�p�����[�^�� ' . $className . ' �N���X�̃I�u�W�F�N�g�ł͂���܂���' );

			foreach( $iArgs_ as $arg )
				{ parent::Judge( ( is_object( $arg ) && $className == get_class( $arg ) ) , $arg ); }

			parent::UnionJudge( 'and' );
			return parent::Instance();
		}

		/**
			@brief   �S�Ă̈������U�ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsFalse()
		{
			$iArgs_ = func_get_args();

			parent::ClearJudge();
			parent::SetErrorCaseMessage( '�p�����[�^���U�ɕ]���ł��܂���' );

			foreach( $iArgs_ as $arg )
				{ parent::Judge( ( $arg ? false : true ) , $arg ); }

			parent::UnionJudge( 'and' );
			return parent::Instance();
		}

		/**
			@brief   �S�Ă̈��������K�\���Ƀ}�b�`���邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0   ���K�\���B
				@li 1~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsMatch()
		{
			$iArgs_ = func_get_args();
			$regex  = array_shift( $iArgs_ );

			parent::ClearJudge();
			parent::SetErrorCaseMessage( '�p�����[�^�� ' . $regex . ' �Ƀ}�b�`���܂���' );

			foreach( $iArgs_ as $arg )
				{ parent::Judge( preg_match( $regex , $arg ) , $arg ); }

			parent::UnionJudge( 'and' );
			return parent::Instance();
		}

		/**
			@brief   �S�Ă̈�����null�ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsNull()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^��null�ł͂���܂���' );
			return self::JudgeInType( $iArgs_ , 'null' , 'and' );
		}

		/**
			@brief   �S�Ă̈�������ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsEmpty()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�͋�ł͂���܂���' );
			return self::JudgeInType( $iArgs_ , 'empty' , 'and' );
		}

		/**
			@brief   �S�Ă̈��������l�ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsNumeric()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�͐��l�ł͂���܂���' );
			return self::JudgeInType( $iArgs_ , 'numeric' , 'and' );
		}

		/**
			@brief   �S�Ă̈������I�u�W�F�N�g�ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsObject()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�̓I�u�W�F�N�g�ł͂���܂���' );
			return self::JudgeInType( $iArgs_ , 'object' , 'and' );
		}

		/**
			@brief   �S�Ă̈��������\�[�X�ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsResource()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�̓��\�[�X�ł͂���܂���' );
			return self::JudgeInType( $iArgs_ , 'resource' , 'and' );
		}

		/**
			@brief   �S�Ă̈������X�J���ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsScalar()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�̓X�J���ł͂���܂���' );
			return self::JudgeInType( $iArgs_ , 'scalar' , 'and' );
		}

		/**
			@brief   �S�Ă̈�����������ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsString()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�͕�����ł͂���܂���' );
			return self::JudgeInType( $iArgs_ , 'string' , 'and' );
		}

		/**
			@brief   �S�Ă̈������^�ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsTrue()
		{
			$iArgs_ = func_get_args();

			parent::ClearJudge();
			parent::SetErrorCaseMessage( '�p�����[�^��^�ɕ]���ł��܂���' );

			foreach( $iArgs_ as $arg )
				{ parent::Judge( ( $arg ? true : false ) , $arg ); }

			parent::UnionJudge( 'and' );
			return parent::Instance();
		}

		/**
			@brief   �S�Ă̈������^�w��̂����ꂩ�ɑ����邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 1   �^�w��B�����w�肷��ꍇ��/�ŋ�؂�B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsInType()
		{
			$iArgs_  = func_get_args();
			$typeSet = array_shift( $iArgs_ );
			parent::SetErrorCaseMessage( '�p�����[�^�� ' . $typeSet . ' �̂�����ɂ������܂���' );
			return self::JudgeInType( $iArgs_ , $typeSet , 'and' );
		}

		//��and/not�]��

		/**
			@brief   �S�Ă̈������z��ł͂Ȃ����Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsNotArray()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�ɔz��͎w��ł��܂���' );
			return self::JudgeNotInType( $iArgs_ , 'array' , 'and' );
		}

		/**
			@brief   �S�Ă̈������w��N���X�̃I�u�W�F�N�g�ł͂Ȃ����Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0   �N���X���B
				@li 1~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsNotClass()
		{
			$iArgs_    = func_get_args();
			$className = array_shift( $iArgs_ );

			parent::ClearJudge();
			parent::SetErrorCaseMessage( '�p�����[�^�� ' . $className . ' �N���X�̃I�u�W�F�N�g�͎w��ł��܂���' );

			foreach( $iArgs_ as $arg )
				{ parent::Judge( !( is_object( $arg ) && $className == get_class( $arg ) ) , $arg ); }

			parent::UnionJudge( 'and' );
			return parent::Instance();
		}

		/**
			@brief   �S�Ă̈��������K�\���Ƀ}�b�`���Ȃ����Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0   ���K�\���B
				@li 1~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsNotMatch()
		{
			$iArgs_ = func_get_args();
			$regex  = array_shift( $iArgs_ );

			parent::ClearJudge();
			parent::SetErrorCaseMessage( '�p�����[�^�� ' . $regex . ' �Ƀ}�b�`����l�͎w��ł��܂���' );

			foreach( $iArgs_ as $arg )
				{ parent::Judge( !preg_match( $regex , $arg ) , $arg ); }

			parent::UnionJudge( 'and' );
			return parent::Instance();
		}

		/**
			@brief   �S�Ă̈�����null�ł͂Ȃ����Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsNotNull()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^��null�͎w��ł��܂���' );
			return self::JudgeNotInType( $iArgs_ , 'null' , 'and' );
		}

		/**
			@brief   �S�Ă̈�������ł͂Ȃ����Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsNotEmpty()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�ɋ�͎w��ł��܂���' );
			return self::JudgeNotInType( $iArgs_ , 'empty' , 'and' );
		}
		
		/**
			@brief   �S�Ă̈��������l�ł͂Ȃ����Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsNotNumeric()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�ɐ��l�͎w��ł��܂���' );
			return self::JudgeNotInType( $iArgs_ , 'numeric' , 'and' );
		}

		/**
			@brief   �S�Ă̈������I�u�W�F�N�g�ł͂Ȃ����Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsNotObject()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�ɃI�u�W�F�N�g�͎w��ł��܂���' );
			return self::JudgeNotInType( $iArgs_ , 'object' , 'and' );
		}

		/**
			@brief   �S�Ă̈��������\�[�X�ł͂Ȃ����Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsNotResource()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�Ƀ��\�[�X�͎w��ł��܂���' );
			return self::JudgeNotInType( $iArgs_ , 'resource' , 'and' );
		}

		/**
			@brief   �S�Ă̈������X�J���ł͂Ȃ����Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsNotScalar()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�ɃX�J���͎w��ł��܂���' );
			return self::JudgeNotInType( $iArgs_ , 'scalar' , 'and' );
		}

		/**
			@brief   �S�Ă̈�����������ł͂Ȃ����Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsNotString()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�ɕ�����͎w��ł��܂���' );
			return self::JudgeNotInType( $iArgs_ , 'string' , 'and' );
		}

		/**
			@brief   �S�Ă̈������^�w��̂����ꂩ�ɂ������Ȃ����Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 1   �^�w��B�����w�肷��ꍇ��/�ŋ�؂�B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsNotInType()
		{
			$iArgs_  = func_get_args();
			$typeSet = array_shift( $iArgs_ );
			parent::SetErrorCaseMessage( '�p�����[�^�� ' . $typeSet . ' �ɑ�����l�͎w��ł��܂���' );
			return self::JudgeNotInType( $iArgs_ , $typeSet , 'and' );
		}

		//��or�]��

		/**
			@brief   �����̂����ꂩ���z��ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsAnyArray()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�̂�������z��ł͂���܂���' );
			return self::JudgeInType( $iArgs_ , 'array' , 'or' );
		}

		/**
			@brief   �����̂����ꂩ���w��N���X�̃I�u�W�F�N�g�ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0   �N���X���B
				@li 1~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsAnyClass()
		{
			$iArgs_    = func_get_args();
			$className = array_shift( $iArgs_ );

			parent::ClearJudge();
			parent::SetErrorCaseMessage( '�p�����[�^�̂������ ' . $className . ' �N���X�̃I�u�W�F�N�g�ł͂���܂���' );

			foreach( $iArgs_ as $arg )
				{ parent::Judge( ( is_object( $arg ) && $className == get_class( $arg ) ) , $arg ); }

			parent::UnionJudge( 'or' );
			return parent::Instance();
		}

		/**
			@brief   �����̂����ꂩ���U�ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsAnyFalse()
		{
			$iArgs_ = func_get_args();

			parent::ClearJudge();
			parent::SetErrorCaseMessage( '�p�����[�^�̂�������U�ɕ]���ł��܂���' );

			foreach( $iArgs_ as $arg )
				{ parent::Judge( ( $arg ? false : true ) , $arg ); }

			parent::UnionJudge( 'or' );
			return parent::Instance();
		}

		/**
			@brief   �����̂����ꂩ�����K�\���Ƀ}�b�`���邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0   ���K�\���B
				@li 1~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsAnyMatch()
		{
			$iArgs_ = func_get_args();
			$regex  = array_shift( $iArgs_ );

			parent::ClearJudge();
			parent::SetErrorCaseMessage( '�p�����[�^�̂������ ' . $regex . ' �Ƀ}�b�`���܂���' );

			foreach( $iArgs_ as $arg )
				{ parent::Judge( preg_match( $regex , $arg ) , $arg ); }

			parent::UnionJudge( 'or' );
			return parent::Instance();
		}

		/**
			@brief   �����̂����ꂩ��null�ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsAnyNull()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�̂������null�ł͂���܂���' );
			return self::JudgeInType( $iArgs_ , 'null' , 'or' );
		}

		/**
			@brief   �����̂����ꂩ��null��]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsAnyEmpty()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�̂��������ł͂���܂���' );
			return self::JudgeInType( $iArgs_ , 'empty' , 'or' );
		}

		/**
			@brief   �����̂����ꂩ�����l�ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsAnyNumeric()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�̂���������l�ł͂���܂���' );
			return self::JudgeInType( $iArgs_ , 'numeric' , 'or' );
		}

		/**
			@brief   �����̂����ꂩ���I�u�W�F�N�g�ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsAnyObject()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�̂�������I�u�W�F�N�g�ł͂���܂���' );
			return self::JudgeInType( $iArgs_ , 'object' , 'or' );
		}

		/**
			@brief   �����̂����ꂩ�����\�[�X�ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsAnyResource()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�̂���������\�[�X�ł͂���܂���' );
			return self::JudgeInType( $iArgs_ , 'resource' , 'or' );
		}

		/**
			@brief   �����̂����ꂩ���X�J���ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsAnyScalar()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�̂�������X�J���ł͂���܂���' );
			return self::JudgeInType( $iArgs_ , 'string' , 'or' );
		}

		/**
			@brief   �����̂����ꂩ��������ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsAnyString()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^�̂������������ł͂���܂���' );
			return self::JudgeInType( $iArgs_ , 'string' , 'or' );
		}

		/**
			@brief   �����̂����ꂩ���^�ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsAnyTrue()
		{
			$iArgs_ = func_get_args();

			parent::ClearJudge();
			parent::SetErrorCaseMessage( '�p�����[�^�̂�������^�ɕ]���ł��܂���' );

			foreach( $iArgs_ as $arg )
				{ parent::Judge( ( $arg ? true : false ) , $arg ); }

			parent::UnionJudge( 'or' );
			return parent::Instance();
		}

		/**
			@brief   �����̂����ꂩ���^�w��̂����ꂩ�ɑ����邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 1   �^�w��B�����w�肷��ꍇ��/�ŋ�؂�B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsAnyInType()
		{
			$iArgs_  = func_get_args();
			$typeSet = array_shift( $iArgs_ );
			parent::SetErrorCaseMessage( '�p�����[�^�̂������ ' . $typeSet . ' �̂�����ɂ������܂���' );
			return self::JudgeInType( $iArgs_ , $typeSet , 'or' );
		}

		/**
			@brief   �����̂����ꂩ��null�ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsAnyNotNull()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^���S��null�ł�' );
			return self::JudgeNotInType( $iArgs_ , 'null' , 'or' );
		}

		/**
			@brief   �����̂����ꂩ��null�ł��邱�Ƃ�]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function IsAnyNotEmpty()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�p�����[�^���S�ċ�ł�' );
			return self::JudgeNotInType( $iArgs_ , 'empty' , 'or' );
		}

		//���]��

		/**
			@brief     �������^�w��̂����ꂩ�ɑ����邱�Ƃ�]������B
			@exception InvalidArgumentException �^�w��ɕs���Ȍ^���܂܂��ꍇ�B
			@param[in] $iArgs_      �������X�g�B
			@param[in] $iTypeSet_   �^�w��B
			@param[in] $iUnionMode_ �]���̌������@�B
			@return    ConceptBase �I�u�W�F�N�g�B
		*/
		private static function JudgeInType( $iArgs_ , $iTypeSet_ , $iUnionMode_ )
		{
			parent::ClearJudge();

			$typeSet = explode( '/' , $iTypeSet_ );

			foreach( $iArgs_ as &$arg ) //����������
			{
				$result = false;

				foreach( $typeSet as $type ) //�^�w�������
				{
					switch( $type ) //�^�w��ŕ���
					{
						case 'array' : //�z��
						{
							$result |= is_array( $arg );
							break;
						}

						case 'bool' : //�^�U�l
						{
							$result |= is_bool( $arg );
							break;
						}

						case 'null' : //null
						{
							$result |= is_null( $arg );
							break;
						}

						case 'numeric' : //���l
						{
							$result |= is_numeric( $arg );
							break;
						}

						case 'object' : //�I�u�W�F�N�g
						{
							$result |= is_object( $arg );
							break;
						}

						case 'resource' : //���\�[�X
						{
							$result |= is_resource( $arg );
							break;
						}

						case 'scalar' : //�X�J��
						{
							$result |= is_scalar( $arg );
							break;
						}

						case 'string' : //������
						{
							$result |= is_string( $arg );
							break;
						}

						case 'empty' : //��
						{
							$result |= empty( $arg );
							break;
						}

						default : //�s���Ȏw��
							{ throw new InvalidArgumentException( '�s���Ȍ^�w�肪�܂܂�Ă��܂� : ' . $iTypeSet_ ); }
					}

					if( $result ) //���ʂ��m�肵���ꍇ
						{ break; }
				}

				parent::Judge( $result , $arg );
			}

			parent::UnionJudge( $iUnionMode_ );
			return parent::Instance();
		}

		/**
			@brief     �������^�w��̂�����ɂ������Ȃ����Ƃ�]������B
			@exception InvalidArgumentException �^�w��ɕs���Ȍ^���܂܂��ꍇ�B
			@param[in] $iArgs_      �������X�g�B
			@param[in] $iTypeSet_   �^�w��B
			@param[in] $iUnionMode_ �]���̌������@�B
			@return    ConceptBase �I�u�W�F�N�g�B
		*/
		private static function JudgeNotInType( $iArgs_ , $iTypeSet_ , $iUnionMode_ )
		{
			parent::ClearJudge();

			$typeSet = explode( '/' , $iTypeSet_ );

			foreach( $iArgs_ as &$arg ) //����������
			{
				$result = true;

				foreach( $typeSet as $type ) //�^�w�������
				{
					switch( $type ) //�^�w��ŕ���
					{
						case 'array' : //�z��
						{
							$result &= !is_array( $arg );
							break;
						}

						case 'bool' : //�^�U�l
						{
							$result &= !is_bool( $arg );
							break;
						}


						case 'null' : //null
						{
							$result &= !is_null( $arg );
							break;
						}

						case 'numeric' : //���l
						{
							$result &= !is_numeric( $arg );
							break;
						}

						case 'object' : //�I�u�W�F�N�g
						{
							$result &= !is_object( $arg );
							break;
						}

						case 'resource' : //���\�[�X
						{
							$result &= !is_resource( $arg );
							break;
						}

						case 'scalar' : //�X�J��
						{
							$result &= !is_scalar( $arg );
							break;
						}

						case 'string' : //������
						{
							$result &= !is_string( $arg );
							break;
						}

						case 'empty' : //��
						{
							$result &= !empty( $arg );
							break;
						}

						default : //�s���Ȏw��
							{ throw new InvalidArgumentException( '�s���Ȍ^�w�肪�܂܂�Ă��܂� : ' . $typeSet ); }
					}

					if( !$result ) //���ʂ��m�肵���ꍇ
						{ break; }
				}

				parent::Judge( $result , $arg );
			}

			parent::UnionJudge( $iUnionMode_ );
			return parent::Instance();
		}
	}
?>