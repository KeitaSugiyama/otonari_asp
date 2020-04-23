<?php

	include_once 'include/extends/Exception/Concept.php';
	/**
		@brief �V�X�e���R���Z�v�g�N���X�B
		@details �t���[�����[�N�ŗL�̗�O�`�F�b�N�pUtility�N���X\n
		       �g������Concept�N���X�ɏ����B
		@author  koichiro yoshioka
		@version 1.0
		@ingroup Utility
	*/
	class ConceptSystem extends Concept
	{
		/**
			@brief   post_max_size ���I�[�o�[���Ă��Ȃ�����]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function CheckPostMaxSizeOrver()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( 'upload���ꂽ�t�@�C����post_max_size���z���Ă��܂��B' );
			parent::Judge( !(empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') , $iArgs_ );
			parent::UnionJudge( 'and' );
			return parent::Instance();
		}
		
		/**
			@brief   get�Ŏw�肳�ꂽtype�̃e�[�u�������݂����]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function CheckAuthenticityToken()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( '�A�N�Z�X�g�[�N���������ł��B' );
			parent::Judge( SystemUtil::checkAuthenticityToken( $_POST['authenticity_token'] ) , $iArgs_ );
			parent::UnionJudge( 'and' );
			return parent::Instance();
		}
		
		/**
			@brief   get�Ŏw�肳�ꂽtype�̃e�[�u�������݂����]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function CheckType()
		{
			global $TABLE_NAME;
			
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( $_GET[ 'type' ] . '�͒�`����Ă��܂���' );
			parent::Judge( in_array(  $_GET[ 'type' ], $TABLE_NAME ) , $iArgs_ );
			parent::UnionJudge( 'and' );
			return parent::Instance();
		}
		/**
			@brief   get�Ŏw�肳�ꂽ�e�[�u���ւ̍��ڂ̍쐬�����������Ă��邩��]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function CheckTableRegistUser()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( $_GET[ 'type' ] . '�ւ̃��R�[�h�쐬����������܂���B' );
			parent::Judge( SystemUtil::checkTableRegistUser( $_GET['type'] ) , $iArgs_ );
			parent::UnionJudge( 'and' );
			return parent::Instance();
		}
		/**
			@brief   get�Ŏw�肳�ꂽ�e�[�u���ւ̍��ڂ̕ҏW�����������Ă��邩��]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function CheckTableEditUser()
		{
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( $_GET[ 'type' ] . '�̃��R�[�h�ҏW����������܂���B' );
			parent::Judge( SystemUtil::checkTableEditUser( $_GET['type'], $iArgs_[0], $iArgs_[1] ) , $iArgs_ );
			parent::UnionJudge( 'and' );
			return parent::Instance();
		}
	
		/**
			@brief   get�Ŏw�肳�ꂽ�e�[�u�����T�C�g�ォ��̕ύX�������ꂽ�e�[�u�����ǂ�����]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function CheckTableNoHTML()
		{
			global $THIS_TABLE_IS_NOHTML;
			
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( $_GET[ 'type' ] . '�͑���ł��܂���' );
			parent::Judge( !isset($THIS_TABLE_IS_NOHTML[ $_GET[ 'type' ] ]) || !$THIS_TABLE_IS_NOHTML[ $_GET[ 'type' ] ] , $iArgs_ );
			parent::UnionJudge( 'and' );
			return parent::Instance();
		}
	
		/**
			@brief   �X�N���v�g�ŕ]�����郌�R�[�h�����݂��邩�ǂ�����]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function CheckRecord()
		{
			$iArgs_ = func_get_args();
			$rec  = array_shift( $iArgs_ );
			parent::SetErrorCaseMessage( '�ΏۂƂȂ郌�R�[�h������܂���B' );
			parent::Judge(  isset($rec)  , $iArgs_ );
			parent::UnionJudge( 'and' );
			return parent::Instance();
		}
	
		/**
			@brief   get�Ŏw�肳�ꂽ�e�[�u�������[�U�[�e�[�u�����ǂ�����]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function CheckThisUserTable()
		{
			global $THIS_TABLE_IS_USERDATA;
			
			$iArgs_ = func_get_args();
			parent::SetErrorCaseMessage( $_GET[ 'type' ] . '�̓��[�U�[�e�[�u���ł͂���܂���' );
			parent::Judge(  $THIS_TABLE_IS_USERDATA[ $_GET[ 'type' ] ] , $iArgs_ );
			parent::UnionJudge( 'and' );
			return parent::Instance();
		}
		
		/**
			@brief   get�Ŏw�肳�ꂽ�e�[�u���ւ̍��ڂ̕ҏW�����������Ă��邩��]������B
			@details ���̃��\�b�h�͎��̉ϒ��̈��������܂��B
				@li 0~n �]�������B
			@return  ConceptBase �I�u�W�F�N�g�B
		*/
		static function CheckLoginType()
		{
			global $loginUserType;
			
			$iArgs_ = func_get_args();
			$checkType_  = array_shift( $iArgs_ );
			parent::SetErrorCaseMessage( $checkType_ . '�݂̂��A�N�Z�X�\�ł��B' );
			parent::Judge(  $loginUserType == $checkType_ , $iArgs_ );
			parent::UnionJudge( 'and' );
			return parent::Instance();
		}
	}
?>