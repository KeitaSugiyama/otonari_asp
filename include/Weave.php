<?php

	include_once "include/base/WSBase.php";

	/**
		@brief   �}���p�����[�^�Ǘ��N���X�B
		@details �R�}���h�R�����g�̑}���p�����[�^���Ǘ����܂��B
		@author  ���� ����
		@version 1.0
		@ingroup SystemComponent
	*/
	class Weave extends WSBase
	{
		//���p�����[�^�ύX

		/**
			@brief     �}���p�����[�^��ǉ�����B
			@exception InvalidArgumentException �s���Ȉ������w�肵���ꍇ�B
			@param[in] $iName_   �}���p�����[�^���B
			@param[in] $iValue_  �}���p�����[�^�̒l�B
			@param[in] $iCCName_ �}���p�����[�^���g�p����R�}���h�R�����g���B
			@param[in] $iLife_   �}���p�����[�^�̎����B
		*/
		static function Push( $iName_ , $iValue_ , $iCCName_ , $iLife_ )
		{
			Concept::IsString( $iName_ )->Orthrow( 'InvalidCCArgument' );
			Concept::IsTrue( strlen( $iName_ ) )->Orthrow( 'InvalidCCArgument' );
			self::$Parameters[ $iName_ ][] = Array( 'value' => $iValue_ , 'ccName' => $iCCName_ , 'life' => $iLife_ );
		}

		//���p�����[�^�擾

		/**
			@brief     �p�����[�^���擾����B
			@exception InvalidArgumentException �s���Ȉ������w�肵���ꍇ�B
			@param[in] $iName_   �}���p�����[�^���B
			@param[in] $iCCName_ �R�}���h�R�����g���B
			@return    �p�����[�^�z��܂��͋�z��B
			@attension ��̃R�}���h���ŁA���\�b�h�𕡐��񓯂������ŌĂяo���Ȃ��悤�ɂ��Ă��������B\n
			           once�p�����[�^�͎擾���_�ō폜����邽�߁A2��ڈȍ~�͈قȂ�z�񂪕Ԃ�\��������܂��B
		*/
		static function Get( $iName_ , $iCCName_ )
		{
			$returnParams  = Array(); //���o���}���p�����[�^
			$inheritParams = Array(); //�c���}���p�����[�^

			foreach( self::$Parameters as $key => $params ) //�}���p�����[�^������
			{
				if( $iName_== $key ) //�}���p�����[�^������v����ꍇ
				{
					foreach( $params as $param ) //�ʂ̑}���p�����[�^������
					{
						$matchCCName = ( $iCCName_ == $param[ 'ccName' ] );
						$useWildCard = ( '*' == $param[ 'ccName' ] );

						if( $matchCCName || $useWildCard ) //�R�}���h�R�����g������v����ꍇ
						{
							$returnParams[] = $param[ 'value' ];

							if( 'all' == $param[ 'life' ] ) //�������������̏ꍇ
								{ $inheritParams[ $key ][] = $param; }
						}
						else //�R�}���h�R�����g������v���Ȃ������ꍇ
							{ $inheritParams[ $key ][] = $param; }
					}
				}
				else //�}���p�����[�^������v���Ȃ��ꍇ
					{ $inheritParams[ $key ] = $params; }
			}

			self::$Parameters = $inheritParams;

			return $returnParams;
		}

		//���ϐ�
		static private $Parameters = Array(); ///<�}���p�����[�^�z��B
	}

?>