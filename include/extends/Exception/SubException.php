<?php

	/**
		@file
		@brief SPL�̕W����O���Ȃ����œ����̗�O���g�����߂̑�֒�`�B
	*/

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details ����`�̊֐����R�[���o�b�N���Q�Ƃ�����A�������w�肵�Ȃ������肵���ꍇ�ɃX���[������O�ł��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class BadFunctionCallException extends LogicException
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details ����`�̃��\�b�h���R�[���o�b�N���Q�Ƃ�����A�������w�肵�Ȃ������肵���ꍇ�ɃX���[������O�ł��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class BadMethodCallException extends BadFunctionCallException
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details ��`�����f�[�^�h���C���ɒl���]��Ȃ��Ƃ��ɃX���[������O�ł��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class DomainException extends LogicException
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details ���������Ғl�Ɉ�v���Ȃ������ꍇ�ɃX���[������O�ł��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class InvalidArgumentException extends LogicException
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details �����������ȏꍇ�ɃX���[������O�ł��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class LengthException extends LogicException
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details �_�����������ȏꍇ�ɃX���[������O�ł��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class LogicException extends Exception
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details �l���L���ȃL�[�łȂ������ꍇ�ɃX���[������O�ł��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class OutOfBoundsException extends RuntimeException
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details �l���͈͓��ɂ����܂�Ȃ������ꍇ�ɃX���[������O�ł��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class OutOfRangeException extends LogicException
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details �����ς��ɂȂ��Ă���R���e�i�ɗv�f��ǉ������ꍇ�ɃX���[������O�ł��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class OverflowException extends RuntimeException
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details �����Ȕ͈͂��n���ꂽ�ꍇ�ɃX���[������O�ł��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class RangeException extends RuntimeException
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details ���s���ɂ�����������悤�ȃG���[�̍ۂɃX���[����܂��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class RuntimeException extends Exception
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details ��̃R���e�i����v�f���폜���悤�Ƃ����ۂɃX���[������O�ł��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class UnderflowException extends RuntimeException
	{}

	/**
		@brief   ��O�I�u�W�F�N�g�B
		@details �������̒l�̃Z�b�g�Ɉ�v���Ȃ��l�ł������ۂɃX���[������O�ł��B\n
		         ���̗�O���L���b�`�����ꍇ�́A���݃V�X�e�������p�ł��Ȃ��|�����[�U�[�ɒʒm���Ȃ���΂����܂���B
		@ingroup Exception
	*/
	class UnexpectedValueException extends RuntimeException
	{}
?>