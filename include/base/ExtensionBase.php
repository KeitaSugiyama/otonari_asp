<?php


/*******************************************************************************************************
 * <PRE>
 *
 * �ėp�̃����[�^�ݒ�ŕ��򂷂�@�\�̊g���p�N���X
 *
 * @version 1.0.0
 *
 * </PRE>
 *******************************************************************************************************/


class ExtensionBase extends command_base
{	
	
	// lst��Extend�J�����̓��e���󂯂āA�e�L�X�g('string','varchar','char')�̒u�����s�Ȃ��ꍇ�̊g��
	// GUIManager::replaceString
	static function GUIManager_replaceStringParam($method,$val,&$before,&$after,$replace_type){
		global $SYSTEM_CHARACODE;
        switch($method){
        	default:
        		//���͓��e��1�����̏ꍇ�́A�w�肳�ꂽ������S�p�ɂ���B
        		if( strlen($method) == 1 ){
        			$before[] = $method;
        			$after[]  =  mb_convert_kana( $method, A, $SYSTEM_CHARACODE );
        			$replace_type = "my";
        		}else{
        		}
        		break;
        	case 'html':
        		$replace_type = "";
        		break;
        }
		return $replace_type;
	}
	static function GUIManager_replaceStringExecute( $replace_type, $before, $after, $str ){
        switch( $replace_type ){
        	case "my":
        	case "nohtml":
        		$str = str_replace( $before, $after, $str );
        		break;
        	default:
	        	break;
        }
		return $str;
	}
	
	// lst��Extend�J�����̓��e���󂯂āA���R�[�h�̓��e��u��������
	static function Database_registExtension( $param, $str ){
		if(!empty($param) )
		{
			$params = explode('/', $param );
	
			foreach( $params as $p ){
		        switch( $p ){
		        	case "updatetime":
		        		$str = time();
		        		break;
		        	default:
			        	break;
		        }
	        	
			}
		}
		return $str;
	}
	
	// lst��Extend�J�����̓��e���󂯂āA���R�[�h�̓��e��u��������
	static function Database_updateExtension( $param, $str )
	{
		if(!empty($param) )
		{
			$params = explode('/', $param );
	
			foreach( $params as $p ){
		        switch( $p ){
		        	case "updatetime":
		        		$str = time();
		        		break;
		        	default:
			        	break;
		        }
	        	
			}
		}
		return $str;
	}
	
}
