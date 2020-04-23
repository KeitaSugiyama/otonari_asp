<?php

	if( !class_exists( 'BadFunctionCallException' ) ) //SPL��O�N���X��������Ȃ��ꍇ
		{ include_once( 'include/extends/Exception/SubException.php' ); }

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details �s���ȃN�G���p�����[�^���󂯎�����ꍇ�ɃX���[����܂��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���삪�󂯕t�����Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class InvalidQueryException extends RuntimeException
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details �s���ȃA�N�Z�X�����������ꍇ�ɃX���[����܂��B\n
		         ���̗�O���L���b�`�����ꍇ�́A�A�N�Z�X�������Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class IllegalAccessException extends RuntimeException
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details �s���ȃA�N�Z�X�����������ꍇ�ɃX���[����܂��B\n
		         ���̗�O���L���b�`�����ꍇ�́A�A�N�Z�X�g�[�N���ɖ�肪����|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class IllegalTokenAccessException extends RuntimeException
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details �t�@�C���̓��o�͂Ɏ��s�����ꍇ�ɃX���[����܂��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class FileIOException extends RuntimeException
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details �f�[�^�x�[�X�̍X�V�Ɏ��s�����ꍇ�ɃX���[����܂��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���삪�K�p����Ȃ������\��������|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class UpdateFailedException extends RuntimeException
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details ���炩�̗��R�ŉ�ʏo�͂Ɏ��s�����ꍇ�ɃX���[����܂��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class OutputFailedException extends RuntimeException
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details �R�}���h�R�����g�̃p�����[�^�ɕs���Ȓl���w�肳�ꂽ�ꍇ�ɃX���[����܂��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class InvalidCCArgumentException extends LogicException
	{}
	
	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details �A�b�v���[�h���ꂽ�t�@�C����post_max_size���I�[�o�[���Ă����ꍇ�ɃX���[����܂��B�B\n
		         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class PostMaxSizeOrverException extends RuntimeException
	{}
	
	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details �s���ȃA�N�Z�X�����������ꍇ�ɃX���[����܂��B\n
		         ���̗�O���L���b�`�����ꍇ�́A�A�N�Z�X�Ώۂ̃f�[�^�����݂��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class RecordNotFoundException extends RuntimeException
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details ���������Ŗ�肪���������ꍇ�ɃX���[����܂��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class InternalErrorException extends RuntimeException
	{}

?>
