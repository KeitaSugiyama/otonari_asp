<?php

	/*******************************************************************************************************
	 * <PRE>
	 *
	 * api.php - ���\�b�h�A�N�Z�X�p
	 * JavaScript����f�[�^���擾������ύX�����肷��ۂ�
	 * ���ύX�̃t�H�[����info��index�ɖ��ߍ��ޏꍇ���Ɏg�p�B
	 *
	 * ���p�X�̊֌W��A�A�N�Z�X�p�Ƀt�@�C����u���A���g�̋L�q�́ucustom/api_core.php�v�ōs���܂��B
	 *
	 * </PRE>
	 *******************************************************************************************************/

	// �N���X����c,���\�b�h����m�ɃZ�b�g���ēn���Ă��������B

	try
	{
		include_once 'custom/head_main.php';
		include_once 'custom/extends/api.inc';

		header( 'Content-Type: text/html;charset=shift_jis' );

		//�p�����[�^�I��
		if( isset( $_GET[ 'get_p' ] ) ) //get_p���Z�b�g����Ă���ꍇGET�p�����[�^���g�p
			$param = $_GET;
		else
			$param = $_POST;

		//�p�����[�^�`�F�b�N
		if( isset( $param[ 'c' ] ) )
		{
			ConceptCheck::IsEssential( $param , Array( 'm' ) );
			ConceptCheck::IsNotNull( $param , Array( 'c' , 'm' ) );
		}
		else
		{
			ConceptCheck::IsEssential( $param , Array( 'post' ) );
			ConceptCheck::IsNotNull( $param , Array( 'post' ) );
		}
		//�p�����[�^�`�F�b�N�����܂�

		$class_name	 = $param['c'];
		if( strlen($class_name) && class_exists('mod_'.$class_name) )	 
		{ 
			$class_name = 'mod_'.$class_name;
			$api	 = new $class_name();
			$method	 = $param['m']; 
		}
		else
		{ 
			$api	 = new Api_core();
			$method	 = $param['post']; 
			$param['info_change_flg'] = false;
			if( strlen($param['js']) || strlen($param['jump']) ) { $param['info_change_flg'] = true; }
		}

		if( method_exists($api, $method) ) { $api->$method( $param ); }
	}
	catch( Exception $e_ )
	{
		//�G���[���b�Z�[�W�����O�ɏo��
		$errorManager = new ErrorManager();
		$errorMessage = $errorManager->GetExceptionStr( $e_ );

		$errorManager->OutputErrorLog( $errorMessage );
	}

?>