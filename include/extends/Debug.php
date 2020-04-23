<?PHP

/*******************************************************************************************************
 * <PRE>
 *
 * �f�o�b�O�֐�
 *
 * @author �g�� �K��Y
 * @version 1.0.0
 *
 * </PRE>
 *******************************************************************************************************/


switch($DEBUG_TYPE){
	case 'echo':
	default:
		// �f�o�b�O�p�o�͊֐�
		// ���w�i�E�������E�����t�H���g�E�t�H���g�T�C�Y�Œ�E�{�[�_�[�͂�
		function d($v,$name='echo') {
			global $DEBUG_TRACE;
			global $DEBUG_ALLLLOW_IP;

			if( 0 < count( $DEBUG_ALLLLOW_IP ) && !in_array( $_SERVER[ 'REMOTE_ADDR' ] , $DEBUG_ALLLLOW_IP ) )
				{ return; }

			echo '<pre style="background:#fff;color:#333;border:1px solid #ccc;margin:2px;padding:4px;font-family:monospace;font-size:12px">';
			echo "$name:";

			ob_start();
			var_dump($v);
			print h( ob_get_clean() );

			echo '</pre>';
			if($DEBUG_TRACE){
				echo '<pre style="background:#fff;color:#333;border:1px solid #ccc;margin:2px;padding:4px;font-family:monospace;font-size:12px">';
				$trace = debug_backtrace();
				$row = count($trace);
				
				for( $i=0;$row>$i;$i++){
					unset($trace[$i]["args"]);
					unset($trace[$i]["object"]);
				}

				ob_start();
				var_dump($trace);
				print h( ob_get_clean() );

				echo '</pre>';
			}
		}
		function p( $var, $label = FALSE ){
			if( $label ){
				$text = '' . $label . ' : ';
			}
			else{
				$text = '';
			}
			$text.= h( print_r($var,1) );
			$text.= '';
			$text = preg_replace( '/(Array)([\r\n])/', '$1$2', $text );
			$text = preg_replace( '/ (\[.+?\]) /' , ' $1 ' , $text );
			$text = preg_replace( '/ (\=\>) /' , ' $1 ', $text );
			echo $text;
		}

		break;
	case 'file':
		function d($str,$name='log') {
			global $LOG_DEBUG_LOG_PATH;
			$log = new OutputLog($LOG_DEBUG_LOG_PATH);
			$log->write( $name.':'.$str);
		}
		break;
	case 'header':
		include_once 'include/extends/FirePHPCore/FirePHP.class.php';
		function d($args,$name='fb'){
			global $DEBUG_TRACE;
			global $DEBUG_ALLLLOW_IP;

			if( 0 < count( $DEBUG_ALLLLOW_IP ) && !in_array( $_SERVER[ 'REMOTE_ADDR' ] , $DEBUG_ALLLLOW_IP ) )
				{ return; }
			
			$firephp = FirePHP::getInstance(true);

			$firephp->log($args, $name);
			
			if($DEBUG_TRACE){
				$trace = debug_backtrace();
				$row = count($trace);
				
				for( $i=0;$row>$i;$i++){
					unset($trace[$i]["args"]);
					unset($trace[$i]["object"]);
				}
				$firephp->log($trace ,'trace');
			}
			
			
		}
		break;
	case 'subview':
		function d($args,$name='sb'){
			global $DEBUG_TRACE;
			global $DEBUG_BUFFER;
			global $DEBUG_REC_BUFFER;
			global $DEBUG_ALLLLOW_IP;

			if( 0 < count( $DEBUG_ALLLLOW_IP ) && !in_array( $_SERVER[ 'REMOTE_ADDR' ] , $DEBUG_ALLLLOW_IP ) )
				{ return; }

			if( !$DEBUG_BUFFER ) //�f�o�b�O��񂪂Ȃ��ꍇ
				{ $DEBUG_BUFFER = Array(); }

			if( !$DEBUG_REC_BUFFER ) //�f�o�b�O��񂪂Ȃ��ꍇ
				{ $DEBUG_REC_BUFFER = Array(); }

			if( 'getRecord' == $name )
			{
				$tableName = $args[ 0 ];
				$args      = $args[ 1 ];
				$id        = $args[ 'id' ];

				if( $DEBUG_REC_BUFFER[ $tableName ][ $id ] ) //�ǂݍ��ݍς݃��R�[�h�̏ꍇ
					{ $args = 'getRecord : ' . $id . '<br />'; }

				$DEBUG_REC_BUFFER[ $tableName ][ $id ] = true;
			}

			if( is_array( $args ) ) //�������z��̏ꍇ
			{
				$str = $name . ' : <table style="margin:10px;">';

				foreach( $args as $column => $value ) //�S�Ă̗v�f������
					{ $str .= '<tr><th>' . $column . '</th><td>' . h( $value ) . '</td></tr>'; }

				$str .= '</table>';
			}
			else if( !$args ) //��������̏ꍇ
				{ $str = $name . ' : ' . h( $args ); }
			else //������������̏ꍇ
				{ $str = $args; }

			if( $DEBUG_TRACE ) //�f�o�b�O�g���[�X�̏o�͂��w�肳��Ă���ꍇ
			{
				$trace = debug_backtrace();

				$str .= '<br />statktrace : <table style="margin:10px;">';

				foreach( $trace as $data ) //�S�ẴX�^�b�N������
				{
					foreach( $data as $name => $value ) //�S�Ă̗v�f������
					{
						if( 'args' == $name || 'object' == $name ) //�c�傷������̏ꍇ
							{ continue; }

						$str .= '<tr><th>' . $name . '</th><td>' . h( $value ) . '</td></tr>';
					}
				}

				$str .= '</table>';
			}

			if( 'getRecord' == $name )
				{ $DEBUG_BUFFER[ $tableName ] .= $str; }
			else
				{ $DEBUG_BUFFER[] = $str; }
		}
		break;
}

class DebugUtil{
        // ./custom/extends/debugConf.php - $CONFIG_DEBUG_TEMPLATE_PATH_COMMENT
        static function addFilePathComment( $html, $file, $parts=null ){
        	global $CONFIG_DEBUG_TEMPLATE_PATH_COMMENT;
        	if($CONFIG_DEBUG_TEMPLATE_PATH_COMMENT){
	        	return $html =
            			"<!-- START TEMPLATE $file $parts -->\n"
            			. $html .
            			"<!-- END TEMPLATE $file $parts -->\n";
        	}else{
        		return $html;
        	}
        }

        static function  onDebugTrace(){
        	global $DEBUG_TRACE;
        	$DEBUG_TRACE = true;
        }

        static function  offDebugTrace(){
        	global $DEBUG_TRACE;
        	$DEBUG_TRACE = false;
        }
        static function getTime( ){
			global $DEBUG_START_TIME;
			return microtime(true) - $DEBUG_START_TIME;
		}
        static function drawTime( $str ){
			global $DEBUG_OLD_TIME;
			$time = self::getTime();
			d(sprintf('%.4f',$time),$str);
			$DEBUG_OLD_TIME = $time;
		}
        static function updateTime( $str ){
			global $DEBUG_OLD_TIME;
			$time = self::getTime();
			$DEBUG_OLD_TIME = $time;
		}
        static function drawChangeTime( $str ){
			global $DEBUG_OLD_TIME;
			$time = self::getTime();
			if( $time > $DEBUG_OLD_TIME + DEBUG_DRAW_CHANGE_DIFF ){
				d(sprintf('%.4f',$time-$DEBUG_OLD_TIME),$str);
				
				if( true ){
					$trace = debug_backtrace();
					$func = array();
					foreach( $trace as $t ){
						$func[] = (isset($t['class'])?$t['class'].'::':''). $t['function'];
					}
					
					d(join(' > ',array_reverse($func)));
				}
			}
			$DEBUG_OLD_TIME = $time;
		}
}
?>