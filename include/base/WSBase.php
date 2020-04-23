<?php

	//���N���X //

	/**
		@brief   �V�X�e���x�[�X�N���X�B
		@details �S�ẴN���X�����ʂŎ�������ׂ��@�\���`���܂��B
	*/
	class WSBase //
	{
		//���}�W�b�N //

		/**
			@brief     ����`�̃��\�b�h�Ăяo�����t�b�N����B
			@details   �N���X���������Ă��Ȃ����\�b�h���Ăяo�����Ƃ����ꍇ�Ɏ��s����܂��B
			@exception LogicException ��ɁB
			@param[in] $iMethodName_ ���\�b�h���B
			@param[in] $iArgs_       �����z��B
			@attention �Ή��ł��Ȃ��ꍇ�͕K����O�𓊂��Ȃ���΂����܂���B
		*/
		function __call( $iMethodName_ , $iArgs_ ) //
			{ throw new LogicException( '���̃��\�b�h�͒�`����Ă��܂���[' . $iMethodName_ . ']' ); }

		/**
			@brief     ����`�̐ÓI���\�b�h�Ăяo�����t�b�N����B
			@details   �N���X���������Ă��Ȃ��ÓI���\�b�h���Ăяo�����Ƃ����ꍇ�Ɏ��s����܂��B
			@exception LogicException ��ɁB
			@param[in] $iMethodName_ ���\�b�h���B
			@param[in] $iArgs_       �����z��B
			@attention �Ή��ł��Ȃ��ꍇ�͕K����O�𓊂��Ȃ���΂����܂���B
		*/
		static function __callstatic( $iMethodName_ , $iArgs_ ) //
			{ throw new LogicException( '���̃��\�b�h�͒�`����Ă��܂���[' . $iMethodName_ . ']' ); }

		/**
			@brief     �A�N�Z�X�s�\�����o�ւ̎Q�Ƃ��t�b�N����B
			@details   �N���X���������Ă��Ȃ������o�̒l���擾���悤�Ƃ����ꍇ�Ɏ��s����܂��B
			@exception LogicException ��ɁB
			@param[in] $iName_ �����o���B
			@attention �Ή��ł��Ȃ��ꍇ�͕K����O�𓊂��Ȃ���΂����܂���B
		*/
		function __get( $iName_ ) //
			{ throw new LogicException( '���̃����o�͒�`����Ă��Ȃ����A�܂���private�ł�[' . $iName_ . ']' ); }

		/**
			@brief     �A�N�Z�X�s�\�����o�ւ̎Q�Ƃ��t�b�N����B
			@details   �N���X���������Ă��Ȃ������o�ɑ΂���isset���Ăяo�����Ƃ����ꍇ�Ɏ��s����܂��B
			@exception LogicException ��ɁB
			@param[in] $iName_ �����o���B
			@attention �Ή��ł��Ȃ��ꍇ�͕K����O�𓊂��Ȃ���΂����܂���B
		*/
		function __isset( $iName_ ) //
			{ throw new LogicException( '���̃����o�͒�`����Ă��Ȃ����A�܂���private�ł�[' . $iName_ . ']' ); }

		/**
			@brief     �A�N�Z�X�s�\�����o�ւ̎Q�Ƃ��t�b�N����B
			@details   �N���X���������Ă��Ȃ������o�ɒl�������悤�Ƃ����ꍇ�Ɏ��s����܂��B
			@exception LogicException ��ɁB
			@param[in] $iName_  �����o���B
			@param[in] $iValue_ ����l�B
			@attention �Ή��ł��Ȃ��ꍇ�͕K����O�𓊂��Ȃ���΂����܂���B
		*/
		function __set( $iName_ , $iValue_ ) //
			{ throw new LogicException( '���̃����o�͒�`����Ă��Ȃ����A�܂���private�ł�[' . $iName_ . ']' ); }

		/**
			@brief     �A�N�Z�X�s�\�����o�ւ̎Q�Ƃ��t�b�N����B
			@details   �N���X���������Ă��Ȃ������o�ɑ΂���unset���Ăяo�����Ƃ����ꍇ�Ɏ��s����܂��B
			@exception LogicException ��ɁB
			@param[in] $iName_ �����o���B
			@attention �Ή��ł��Ȃ��ꍇ�͕K����O�𓊂��Ȃ���΂����܂���B
		*/
		function __unset( $iName_ ) //
			{ throw new LogicException( '���̃����o�͒�`����Ă��Ȃ����A�܂���private�ł�[' . $iName_ . ']' ); }
	}
