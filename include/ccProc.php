<?php

	/*******************************************************************************************************
	 * <PRE>
	 *
	 * ccProc�N���X�B
	 *
	 * @author �O�H��q
	 * @version 3.0.0
	 *
	 * </PRE>
	 *******************************************************************************************************/
	class ccProc extends ccProcBase
	{
        private static $_DEBUG	 = DEBUG_FLAG_CCPROC;
		private static $convert_cache = Array();
		private static $MemoCCValues = Array();
		
		//debug�t���O����p
        static function onDebug(){ self::$_DEBUG = true; }
        static function offDebug(){ self::$_DEBUG = false; }

		// �֐��̊���U��
		static function controller(&$gm, $rec, $cc)
		{
			if( self::$_DEBUG ){ d($cc,'ccProc'); }

			switch($cc[0])
			{
			case 'readhead':
			case 'readend':
            case 'ifbegin':
            case 'elseif':
            case 'else':
            case 'endif':
            case 'switch':
            case 'case':
            case 'break':
            case 'endswitch':
            case 'default':
				return;
			case 'task':
				return Task::Fire($gm, $rec, $cc);
			case 'include':
				return ccProc::drawDesign($gm, $rec, $cc);
			case 'adapt':
				return ccProc::drawAdapt($gm, $rec, $cc);
            case '//':
                return;
			default:
				return ccProc::{$cc[0]}($gm, $rec, $cc);
			}
		}
		
		// �e���v���[�g�Ɋ֘A�t����ꂽ���R�[�h�̈����Ŏw�肳�ꂽ�J�����̓��e���o�͂���B
		function value(&$gm, $rec, $cc)
		{
			$ret = "";
			if( !isset($gm) ) { throw new InternalErrorException( 'CommandComment Null Pointer Error -> $gm ( via value )' ); }
			if( isset($_GET[$cc[1]]) ) { $_POST[$cc[1]] = $_GET[$cc[1]]; }
			
			$db = $gm->getDB();
			$type = $gm->colType[$cc[1]];

			if( $cc[1] == 'delete_time')
			{
				$type = 'timestamp';
			}

			switch($type)
			{
			case 'timestamp':
				$time	  = $db->getData( $rec, $cc[1] );
				$format   = $gm->getTimeFormat();
				if( $time > 0 ){
					if( isset($cc[2]) && strtolower($cc[2]) == 'false' )	{ $ret	.= $time; }
					else								{ $ret	.= SystemUtil::mb_date(  $format, $time  ); }
				}
				break;
			case 'date':
				$date	  = $db->getData( $rec, $cc[1] );
				if( $date > 0 ){
					if( isset($cc[2]) && strtolower($cc[2]) == 'false' ){ $ret	.= $date; }
					else												{ $ret	.= SystemUtil::date( $gm->dateFormat, $date ); }
				}
				break;
			case 'boolean':
				if( $db->getData($rec, $cc[1]) ) { $ret .= 'TRUE'; }
				else							 { $ret .= 'FALSE'; }

				break;
			case 'password':
				$ret .= SystemUtil::decodePassword( $db->getData( $rec, $cc[1], !(isset($cc[2]) && strtoupper($cc[2]) == 'FALSE') ) );
				break;
			default:
				if( is_null($rec) && isset($_POST[$cc[1]]) )
				{
					$brFlg = false;
					if( isset($cc[2]) && strtoupper($cc[2]) == 'TRUE' ) { $brFlg = true; }

					if($brFlg)	 { $ret .= brChange($_POST[$cc[1]]); }
					else		 { $ret .= $_POST[$cc[1]]; }
				}
				else
				{
						$ret .= $db->getData( $rec, $cc[1], !(isset($cc[2]) && strtoupper($cc[2]) == 'FALSE') );
				}

				break;
			}

			//�߂�l����̏ꍇ�̃f�t�H���g���Z�b�g
			if( !strlen($ret) && isset($cc[3]) ){  $ret = $cc[3]; }
			
			return $ret;
		}
		
		/**
			@brief �e�[�u���̒l��ʂ̒l�ɕϊ�����B
			@details
				���̃e�[�u���̒l�ɕϊ�����ꍇ
					�ŒZ : <!--# convert ���J���� �e�[�u���� #-->
					�t�� : <!--# convert ���J���� to �e�[�u����.�����J���� to �擾�J���� split �������� join �������� single #-->
				�ϊ����X�g�𕶎���Ŏw�肷��ꍇ
					�ŒZ : <!--# convert ���J���� �����X�g �ϊ����X�g #-->
					�t�� : <!--# convert ���J���� to �����X�g to �ϊ����X�g split �������� join �������� single #-->
			@remarks
				�t���\����to�͏ȗ��\
				single���w�肷���1�}�b�`�������_�ŕϊ����I������
		*/
		function convert( &$iGM , $iRec , $iArgs ) //
		{
			$db = $iGM->getDB();

			if( 'boolean' == $db->colType[ $iArgs[ 1 ] ] ) //bool�^�̏ꍇ
				{ $iArgs[ 1 ] = ( $db->getData( $iRec , $iArgs[ 1 ] ) ? 'TRUE' : 'FALSE' ); }
			else //���̌^�̏ꍇ
				{ $iArgs[ 1 ] = $db->getData( $iRec , $iArgs[ 1 ] ); }

			return self::convertString( $iGM , $iRec , $iArgs );
		}

		/**
			@brief �C�ӂ̒l��ʂ̒l�ɕϊ�����B
			@details
				���̃e�[�u���̒l�ɕϊ�����ꍇ
					�ŒZ : <!--# convertString �������� �e�[�u���� #-->
					�t�� : <!--# convertString �������� to �e�[�u����.�����J���� to �擾�J���� split �������� join �������� single #-->
				�ϊ����X�g�𕶎���Ŏw�肷��ꍇ
					�ŒZ : <!--# convertString �������� �����X�g �ϊ����X�g #-->
					�t�� : <!--# convertString �������� to �����X�g to �ϊ����X�g split �������� join �������� single #-->
			@remarks
				�t���\����to�͏ȗ��\
				single���w�肷���1�}�b�`�������_�ŕϊ����I������
		*/
		function convertString( &$iGM , $iRec , $iArgs ) //
		{
			global $TABLE_NAME;

			array_shift( $iArgs );

			$originValue = array_shift( $iArgs );
			$beforeParam = array_shift( $iArgs );

			if( '->' == $beforeParam || 'to' == $beforeParam ) //�⏕�\���̏ꍇ
				{ $beforeParam = array_shift( $iArgs ); }

			$afterParam = array_shift( $iArgs );

			if( '->' == $afterParam || 'to' == $afterParam ) //�⏕�\���̏ꍇ
				{ $afterParam = array_shift( $iArgs ); }

			$splitParam = '/';
			$joinParam  = '/';

			while( count( $iArgs ) ) //����������ԌJ��Ԃ�
			{
				$paramName = array_shift( $iArgs );

				switch( $paramName ) //�p�����[�^���ŕ���
				{
					case 'single' : //�P��ϊ�
					{
						$singleMode = true;

						break;
					}

					case 'split' : //����������
					{
						$splitParam = array_shift( $iArgs );

						break;
					}

					case 'join' : //����������
					{
						$joinParam = array_shift( $iArgs );

						break;
					}

					default : //���̑�
						{ throw new LogicException( 'CC�\���G���[:����' . $paramName . '�͎�������Ă��܂���B' ); }
				}
			}

			$originValue = ( $singleMode ? Array( $originValue ) : explode( $splitParam , $originValue ) );
			$result      = Array();

			if( FALSE !== strpos( $beforeParam , $splitParam ) ) //replace���[�h�̏ꍇ
			{
				$beforeParam = explode( $splitParam , $beforeParam );
				$afterParam  = explode( $splitParam , $afterParam );

				foreach( $originValue as $value ) //�S�Ă̒l������
				{
					$index = array_search( $value , $beforeParam );

					if( FALSE !== $index ) //�l�����������ꍇ
						{ $result[] = $afterParam[ $index ]; }
				}
			}
			else //alias���[�h�̏ꍇ
			{
				List( $tableName , $searchColumn ) = explode( '.' , $beforeParam );

				if( !in_array( $tableName , $TABLE_NAME ) ) //�e�[�u�����̎w�肪�Ԉ���Ă���ꍇ
					{ throw new LogicException( 'CC�\���G���[:' . $tableName . '�e�[�u���͑��݂��܂���B' ); }

				$db = GMList::getDB( $tableName );

				if( !$searchColumn ) //�����J�����̎w�肪�Ȃ��ꍇ
					{ $searchColumn = 'id'; }

				if( !$afterParam ) //�擾�J�����̎w�肪�Ȃ��ꍇ
					{ $afterParam = 'name'; }

				if( !in_array( $searchColumn , $db->colName ) ) //�����J�����̎w�肪�Ԉ���Ă���ꍇ
					{ throw new LogicException( 'CC�\���G���[:' . $tableName . '��' . $searchColumn . '�J�����͑��݂��܂���B' ); }

				if( !in_array( $afterParam , $db->colName ) ) //�擾�J�����̎w�肪�Ԉ���Ă���ꍇ
					{ throw new LogicException( 'CC�\���G���[:' . $tableName . '��' . $afterParam . '�J�����͑��݂��܂���B' ); }

				foreach( $originValue as $value ) //�S�Ă̒l������
				{
					$cacheName = implode( '.' , Array( $tableName , $searchColumn , $value ) );

					if( isset( self::$alias_cash[ $cacheName ] ) ) //�L���b�V��������ꍇ
					{
						$rec      = self::$alias_cash[ $cacheName ];
						$result[] = $db->getData( $rec , $afterParam );
					}
					else //�L���b�V�����Ȃ��ꍇ
					{
						$table = $db->getTable();
						$table = $db->searchTable( $table , $searchColumn , '=' , $value );
						$table = $db->limitOffset( $table , 0 , 1 );

						if( $db->getRow( $table ) ) //��v����s������ꍇ
						{
							$rec      = $db->getRecord( $table , 0 );
							$result[] = $db->getData( $rec , $afterParam );
						}
					}
				}
			}

			return implode( $joinParam , $result );
		}

		// �e���v���[�g�Ɋ֘A�Â������R�[�h����w�肵���J�����̒l�𔲂������A���݂���ꍇ�͂�����摜�̃p�X�Ƃ���img�^�O�Ɏ󂯓n���\������B
		function object(&$gm, $rec, $cc)
		{
			global $IMAGE_NOT_FOUND;
			global $IMAGE_NOT_FOUND_SRC;
			global $THUMBNAIL_OPTIONS;
			global $FileBase;

			$ret = "";
			switch($cc[1])
			{
			case 'image':
					//size,alt,str,not,link�����_��Ɏw��\��img�o��
					$not = $IMAGE_NOT_FOUND;
					if( strlen($cc[2]) ){	$elements['src'] = $gm->db->getData( $rec, $cc[2] );	}
					$elements['alt'] = '';
					
					$link = false;
					$thumbnail = true;
					$ret = "";
					
					for($i=3;$i<count($cc);$i++){
						switch($cc[$i]){
							case 'size':
								$elements['width'] = $cc[++$i];
								$elements['height'] = $cc[++$i];
								break;
							case 'maxSize':
								$maxWidth  = $cc[++$i];
								$maxHeight = $cc[++$i];
								break;
							case 'minSize':
								$minWidth  = $cc[++$i];
								$minHeight = $cc[++$i];
								break;
							case 'not':		$not = $cc[++$i]; break;
							case 'subsrc':	$subsrc = $cc[++$i]; break;
							case 'defsrc':	$defsrc = $IMAGE_NOT_FOUND_SRC; break;
							case 'link':	$link = true; break;
							case 'nothumbnail': $thumbnail=false; break;
							case 'option': $option = $cc[++$i]; break;
							case 'img_suffix': $img_suffix = $cc[++$i]; break;
							case 'link_option': $link_option = $cc[++$i]; break;
							default://alt,src�Ȃ�
								$elements[$cc[$i]] = $cc[++$i];
								break;
						}
					}
					if( !isset($elements['src']) || !strlen($elements['src']) || !$FileBase->file_exists($elements['src']) )
					{
						if( isset($subsrc) && strlen($subsrc) && is_file($subsrc) )
							{ $elements['src'] = $subsrc; }
						else if( isset($defsrc) && strlen($defsrc) && is_file($defsrc) )
							{ $elements['src'] = $defsrc; }
						else
							{ return $not; }
					}

					$file_exists = $FileBase->file_exists($elements['src']);
					$file_src = $FileBase->geturl($elements['src']);
					if( !$file_exists ){ return $not; }

					$info = $FileBase->getimagesize( $elements['src'] );

					if( $maxWidth && ( $maxWidth <= $elements[ 'width' ] || ( !$elements[ 'width' ] && $maxWidth <= $info[ 0 ] ) ) )
						{ $elements[ 'width' ] = $maxWidth; }

					if( $maxHeight && ( $maxHeight <= $elements[ 'height' ] || ( !$elements[ 'height' ] && $maxHeight <= $info[ 1 ] ) ) )
						{ $elements[ 'height' ] = $maxHeight; }

					if( $minWidth && ( $minWidth >= $elements[ 'width' ] || ( !$elements[ 'width' ] && $minWidth >= $info[ 0 ] ) ) )
						{ $elements[ 'width' ] = $minWidth; }

					if( $minHeight && ( $minHeight >= $elements[ 'height' ] || ( !$elements[ 'height' ] && $minHeight >= $info[ 1 ] ) ) )
						{ $elements[ 'height' ] = $minHeight; }

					if($link){
						$url = $file_src;

						$ret	.= '<a href="'. $url .'" ';
						if(isset($link_option)){ $ret .= $link_option; }
						else{ $ret .= 'target="_blank" ';}
						$ret	.= '>';
					}
			
					if( $thumbnail && isset($elements['width']) && isset($elements['height']) ){


						if( WS_SYSTEM_GDIMAGE_PROGRESS_IMAGE )
						{
							$trimming = ( isset( $elements[ 'trimming' ] ) ? 'true' == strtolower( $elements[ 'trimming' ] ) : null );

							if( mod_Thumbnail::Useable( $elements[ 'src' ] , $elements[ 'width' ] , $elements[ 'height' ] , $trimming ) )
								{ $elements[ 'src' ] = mod_Thumbnail::Create( $elements[ 'src' ] , $elements[ 'width' ] , $elements[ 'height' ] , $trimming ); }
							else
							{
								$elements['src'] = 'thumb.php?src=' . $elements[ 'src' ] . '&width=' . $elements[ 'width' ] . '&height=' . $elements[ 'height' ];

								if( isset( $elements[ 'trimming' ] ) )
									{ $elements[ 'src' ] .= '&trimming=' . $elements[ 'trimming' ]; }
							}
						}
						else
						{
							$trimming = ( isset( $elements[ 'trimming' ] ) ? 'true' == strtolower( $elements[ 'trimming' ] ) : null );
							$elements['src'] = mod_Thumbnail::Create( $elements['src'],$elements['width'],$elements['height'],$trimming);
						}
					}else if($file_exists)
					{
						$elements['src'] = $file_src;
					}
					else if( !$elements[ 'width' ] && !$elements[ 'height' ] && isset($info[0]) && isset($info[1]) )
					{
						$elements[ 'width' ] = $info[ 0 ];
						$elements[ 'height' ] = $info[ 1 ];
					}
					
					$ret .= '<img ';
					foreach( $elements as $name => $val ){ $ret .= $name.'="'.$val.'" '; }
					if(isset($option)){ $ret .= $option.' '; }
					$ret .= '/>';
			
					if(isset($img_suffix)){ $ret .= $img_suffix; }
					
					if($link){	$ret    .= '</a>';	}
				break;
					
			case 'imageSize':
					// �摜�����݂���ꍇ�͂�����摜�̃p�X�Ƃ���img�^�O�Ɏ󂯓n���\������Bwidth��height��ݒ�\�B
					$param = Array( 'object','image',$cc[2],'size',$cc[3],$cc[4]);
					if(isset($cc[5])){ $param[] = 'option'; $param[] = $cc[5];}
					$ret .= self::object($gm, $rec, $param);
				break;
			case 'imageStr':
				// �\������摜�̃p�X�𕶎���w��œn���B
					$param = Array( 'object','image','','src',$cc[2]);
					$ret .= self::object($gm, $rec, $param);
				break;
			case 'imageSizeStr':
				// �\������摜�̃p�X�𕶎���w��œn���Bwidth��height��ݒ�\�B
					$param = Array( 'object','image','','src',$cc[2],'size',$cc[3],$cc[4]);
					if(isset($cc[5])){ $param[] = 'option'; $param[] = $cc[5];}
					$ret .= self::object($gm, $rec, $param);
				break;
			case 'linkImage':
					// �摜�����݂���ꍇ�͂�����摜�̃p�X�Ƃ���img�^�O�Ɏ󂯓n���\������B�摜�ɂ͉摜�ւ̃����N��t�^����B 
					$param = Array( 'object','image',$cc[2],'link');
					if(isset($cc[3])){ $param[] = 'option'; $param[] = $cc[3];}
					$ret .= self::object($gm, $rec, $param);
				break;
			case 'linkImageSize':
					$param = Array( 'object','image',$cc[2],'size',$cc[3],$cc[4],'link');
					if(isset($cc[5])){ $param[] = 'option'; $param[] = $cc[5];}
					$ret .= self::object($gm, $rec, $param);
				break;
			case 'imageSizeNotfound':
					$param = Array( 'object','image',$cc[2],'size',$cc[3],$cc[4],'not',$cc[5]);
					if(isset($cc[6])){ $param[] = 'option'; $param[] = $cc[6];}
					$ret .= self::object($gm, $rec, $param);
				break;
			case 'video':
					$not = "";
					if( strlen($cc[2]) ){ $elements['src'] = $gm->db->getData( $rec, $cc[2] ); }
					
					$ret = "";
					$controls = "controls ";
					$autoplay = "";
					$ret = "";
					
					for($i=3;$i<count($cc);$i++){
						switch($cc[$i]){
							case 'size':
								$elements['width'] = $cc[++$i];
								$elements['height'] = $cc[++$i];
								break;
							case 'poster':   $poster = $cc[++$i]; break;
							case 'controls': $controls = "controls " ; break;
							case 'nocontrols': $controls = "" ; break;
							case 'autoplay': $autoplay = "autoplay " ; break;
							case 'preload':  $preload = $cc[++$i]; break;
							default://alt,src�Ȃ�
								$elements[$cc[$i]] = $cc[++$i];
								break;
						}
					}
					if( !isset($elements['src']) || !strlen($elements['src']) ){ return $not; }
					if( !is_file($elements['src']) ){ return $not; }
					
					$ret = '<video '.$controls.$autoplay;
					if( isset($elements['width']) && isset($elements['height']) )
					{ $ret .= 'width="'. $elements[ 'width' ] .'" height="'. $elements[ 'height' ] .'" '; }
					if( isset($poster) ) { $ret .= 'poster="'.$poster.'" '; }
					if( isset($preload) ) { $ret .= 'preload="'.$preload.'" '; }
					$ret .= '>'."\n";
					$ret .= '<source src="'. $elements[ 'src' ] .'">'."\n";
					$ret .= '</video>'."\n";
				break;
			}
			return $ret;
		}
		
		// form���o�͂���B
		function form(&$gm, $rec, $cc)
		{
			global $FileBase;

			$ret = "";
			$col = $cc[2];
			
			/*
				�p�����[�^�� POST,record,GET,�f�t�H���g�l�̏��œǂݍ��܂��
			*/
			if( isset($_POST[$col]) ){
				$initial = $_POST[$col];
			}else if( !is_null($rec) && $gm->getDB()->isColumn($col) )
			{ 
				$db = $gm->getDB();
				$initial = $db->getData( $rec, $col );
				if( is_bool($initial) )
				{
					if( $initial )	{ $initial	 = 'TRUE'; }
					else			{ $initial	 = 'FALSE'; }
				}
			}else if( isset($_GET[$col]) )
			{
				$initial = $_GET[$col];
			}
	
			switch($cc[1])
			{
				case 'text':
					// text��input�^�O���o�́B
					$option = "";
					if( isset($cc[6]) ) { $option = $cc[6].' '; }
	
					if( isset($cc[3]) && strlen($cc[3]) ) { $option .= 'size="'. $cc[3] .'" '; }
					if( isset($cc[4]) && strlen($cc[4]) ) { $option .= 'maxlength="'. $cc[4] .'" '; }
	
					$value = "";
					if( isset($cc[5]) && strlen($cc[5]) ) {	$value = h($cc[5]); }
					if( isset($initial) ) { $value = h($initial); }
	
					$ret .= '<input type="text" name="'. $col .'" value="'.$value.'" '.$option .'/>'. "\n";
					break;
					
				case 'password':
						// password��input�^�O���o�́B
					$option = "";
					if( isset($cc[5]) ) { $option = $cc[5].' '; }
	
					if( isset($cc[3]) && strlen($cc[3]) ) { $option .= 'size="'. $cc[3] .'" '; }
					if( isset($cc[4]) && strlen($cc[4]) ) { $option .= 'maxlength="'. $cc[4] .'" '; }
	
					$ret .= '<input type="password" name="'. $col .'" '.$option .' />'. "\n";
					break;
					
				case 'textarea':
						// textarea�^�O���o�́B
					$option	 = "";
					if( isset($cc[6]) ) { $option = $cc[6]; }
					
					$value = isset($cc[5])?$cc[5]:'';
					if( isset($initial) ) { $value = $initial; }
					if( isset($cc[ 7 ]) && 'nobr' != $cc[ 7 ] ){ $value = str_replace( '<br/>', "\n", $value ); }
	
					$ret .= '<textarea name="'. $col .'" cols="'. $cc[3] .'" rows="'. $cc[4] .'" '. $option .'>'. h ( $value, ENT_QUOTES | ENT_HTML401 ) .'</textarea>'. "\n";
					break;
				
				case 'radiobox':
				case 'radio':
					// radio��input�^�O��z��̐������o��
					$value	 = explode( '/', $cc[5] );
					$index	 = explode( '/', $cc[6] );
							
					$option	 = "";
					if( isset($cc[7]) ) { $option = $cc[7]; }
					
					$init = isset($cc[3])?$cc[3]:'';
					if( isset($initial) ) { $init = $initial; }
					$init = self::initEscape($init);
					
					$count = count($value);
					for($i=0; $i<$count; $i++)
					{
						$checked = "";
						if( $value[$i] == $init ) { $checked = ' checked="checked" '; }
						
						$ret .= '<label><input type="radio" name="'. $col .'" value="'. $value[$i] .'" '. $option .''.$checked.'/>'. $index[$i]. $cc[4]. "</label>\n"; 
					}
					break;
				case 'checkbox':
				case 'check':
					// checkbox��input�^�O��z��̐������o��
					$value	 = explode( '/', $cc[5] );
					$index	 = explode( '/', $cc[6] );
							
					$option	 = "";
					if( isset($cc[7]) ) { $option = $cc[7]; }
					
					$init = array();
					if( isset($initial) )
					{
						if( is_array($initial) ) { $init = $initial; }
						else						 { $init = explode( '/', $initial ); }
					}else{ $init	 = explode( '/', $cc[3] ); }
					$init = self::initEscape($init);
					
					$valueCount	 = count($value);
					for($i=0; $i<$valueCount; $i++)
					{
						$checked = "";
						if( array_search($value[$i],$init) !== FALSE ){ $checked = ' checked="checked" '; }
	
						$ret .= '<label><input type="checkbox" name="'. $col .'[]" value="'. $value[$i] .'" '. $option .$checked.'/>'. $index[$i]. $cc[4]. "</label>\n";
					}
				
					if(!strlen($cc[8]) || $cc[8] != 'true' ) { $ret .= '<input type="hidden" name="'. $col .'_CHECKBOX" value="" />'."\n"; }
					break;
					
				case 'option':
					// �v���_�E��(select-option�^�O�̃Z�b�g)���o��
					$value	 = explode( '/', $cc[4] );
					$index	 = explode( '/', $cc[5] );
						
					$option = "";
					if( isset($cc[6]) ) { $option	 = $cc[6]; }
					
					$init = $cc[3];
					if( isset($initial) ) { $init = $initial; }
					$init = self::initEscape($init);
					
					
					$ret .= '<select name="'. $col .'" '. $option .'>'. "\n";
					$count = count($value);
					for($i=0; $i<$count; $i++)
					{
						$selected = "";
						if( $value[$i] == $init ) { $selected = ' selected="selected" '; }
						
						$ret .= '<option value="'. $value[$i] .'"'.$selected.'>'. $index[$i] .'</option>'. "\n";
					}
					$ret .= '</select>'. "\n";
					
					break;
				case 'multiple':
					// �v���_�E��(select-option�^�O�̃Z�b�g)���o��
					$value	 = explode( '/', $cc[4] );
					$index	 = explode( '/', $cc[5] );
					
					$option		 = "";
					if(  isset(  $cc[7]  )  )	{ $option	 = $cc[7]; }
					
					$init = $cc[3];
					if( isset($initial) ) {
						$init = $initial;
					}
					// array_search�̂��߂ɔz��
					$init = self::initEscape($init);
					if ( !is_array($init) ) { $init = explode('/', $init); }
					
					$ret .= '<select name="'. $col .'[]" multiple="multiple" size="'.$cc[6].'" '. $option .'>'. "\n";
					$count = count($value);
					
					for($i=0; $i<$count; $i++)
					{
						$selected = '';
						if( array_search( $value[$i], $init ) !== FALSE ){ $selected = 'selected="selected"'; }
						
						$ret	 .= '<option value="'. $value[$i] .'" '.$selected.'>'. $index[$i] .'</option>'. "\n";
					}
					$ret	 .= '</select>'. "\n";
					
					break;

				case 'multi_image':
				case 'multi_file':
					$option	        = '';
					$deleteText     = '�폜';
					$max_num = $cc[3];
					$enableFileTemps = array();
					$enableDeletes = array();
					$ret="";

					$fileCount = 0;

                    if( isset( $cc[ 4 ] ) && strlen( $cc[ 4 ] ) ) //�I�v�V�����̎w�肪����ꍇ
                    { $option = $cc[ 4 ]; }

                    if( isset( $cc[ 5 ] ) && strlen( $cc[ 5 ] ) ) //�폜�`�F�b�N�̕����w�肪����ꍇ
                    { $deleteText = $cc[ 5 ]; }


					//�t�@�C���̗L�����m�F
					for ($i = 1; $i <= $max_num; $i++) {
						$col_name = $col.$i;

						if( !is_null($rec) && $gm->getDB()->isColumn($col_name) )
						{
							$db = $gm->getDB();
							$initial = $db->getData( $rec, $col_name );
						}else if( isset($_GET[$col_name]) )
						{
							$initial = $_GET[$col_name];
						}else if( isset($_POST[$col_name]) ) {
							$initial = $_POST[$col_name];
						}

						$enablePosts[$i]     = ( isset( $initial ) && strlen( $initial ) );
						$enableFileTemps[$i] = (isset($_POST[$col_name . '_filetmp']) && strlen($_POST[$col_name . '_filetmp']));
						$enableDeletes[$i] = (isset($_POST[$col_name . '_DELETE']) && 'true' == $_POST[$col_name . '_DELETE']);

						if( $enablePosts[$i] || $enableFileTemps[$i] ) //�t�@�C���܂��͈����p����񂪂���ꍇ
						{
							// TODO: case �̕���Ɠ��������ĂĔ������Ȃ�
							if( 'multi_file' == $cc[ 1 ] ) //�t�@�C���t�H�[���̏ꍇ
							{
								$param  = Array( 'value' , $col_name );
								$ret   .= '<a href="' . self::value( $gm , $rec , $param ) . '" target="_blank">' . self::value( $gm , $rec , $param ) . '</a>';
							}else{ // image �t�H�[���̏ꍇ
								if( $enablePosts[$i] ) //�摜�̏�񂪂���ꍇ
								{ $param = Array( 'object' , 'image' , $col_name , 'not' , '' , 'link' ); }
								else if( $enableFileTemps[$i] ) //�����p����񂪂���ꍇ
								{ $param = Array( 'object' , 'image' , '' , 'not' , '' , 'link' , 'src' , $_POST[ $col_name . '_filetmp' ] ); }

								if( isset( $cc[ 6 ] ) && strlen( $cc[ 6 ] ) ) //���̎w�肪����ꍇ
								{
									$param[] = 'width';
									$param[] = $cc[ 6 ];
								}

								if( isset( $cc[ 7 ] ) && strlen( $cc[ 7 ] ) ) //�����̎w�肪����ꍇ
								{
									$param[] = 'height';
									$param[] = $cc[ 7 ];
								}

								$ret .= self::object( $gm , $rec , $param );
							}
							$ret .= '<br />';

                            if ($enablePosts[$i]) //�t�@�C���̏�񂪂���ꍇ
                            {
                                $ret .= '<input name="' . $col_name . '_filetmp" type="hidden" value="' . $_POST[$col_name] . '" />' . "\n";
                                $ret .= '<label><input type="checkbox" name="' . $col_name . '_DELETE" value="true" />' . $deleteText . '</label>';

                            } else  //�����p����񂪂���ꍇ
                            {
                                $ret .= '<input name="' . $col_name . '_filetmp" type="hidden" value="' . $_POST[$col_name . '_filetmp'] . '" />' . "\n";

                                if ($enableDeletes[$i]) //�폜�`�F�b�N�̈����p��������ꍇ
                                {
                                    $ret .= '<label><input type="checkbox" name="' . $col_name . '_DELETE" value="true" checked="checked" />' . $deleteText . '</label>';
                                } else //�폜�`�F�b�N�̈����p�����Ȃ��ꍇ
                                {
                                    $ret .= '<label><input type="checkbox" name="' . $col_name . '_DELETE" value="true" />' . $deleteText . '</label>';
                                }
                            }
							$ret   .= '<br />';

							$fileCount++;
						}
					}
					$ret = '<input name="' . $col . '[]" type="file" ' . $option . 'multiple="multiple">' . "\n<br />\n"
							. ($fileCount==0?'':"���� $fileCount �̃t�@�C�����A�b�v���[�h�ς݂ŁA�c��"). ($max_num-$fileCount)."�̃t�@�C�����A�b�v���[�h�\�ł��B\n<br />\n"
							.$ret;

					if( isset( $cc[ 8 ] ) && $cc[ 8 ]=='drop'  )
					{
						$uptype = 'file';
						if( $cc[1] == 'multi_image') { $uptype = 'image'; }
						$ret .= '<div class="drop-zone-multi" data-type="'.$uptype.'" data-upmax="'.$max_num.'" data-name="'.$col.'"><p>�����Ƀt�@�C�����h���b�v</p><output></output></div>';
					}
					break;
				case 'image' : //�t�@�C������(type=file��input�^�O)���o��(�T���l�C���\���t��)

					$enablePost     = ( isset( $initial ) && strlen( $initial ) );
					$enableFileTemp = ( isset( $_POST[ $col . '_filetmp' ] ) && strlen( $_POST[ $col . '_filetmp' ] ) );

					if( $enablePost || $enableFileTemp ) //�摜�܂��͈����p����񂪂���ꍇ
					{
						if( $enablePost ) //�摜�̏�񂪂���ꍇ
							{ $param = Array( 'object' , 'image' , $col , 'not' , '' , 'link' ); }
						else if( $enableFileTemp ) //�����p����񂪂���ꍇ
							{ $param = Array( 'object' , 'image' , '' , 'not' , '' , 'link' , 'src' , $_POST[ $col . '_filetmp' ] ); }

						if( isset( $cc[ 5 ] ) && strlen( $cc[ 5 ] ) ) //���̎w�肪����ꍇ
						{
							$param[] = 'width';
							$param[] = $cc[ 5 ];
						}

						if( isset( $cc[ 6 ] ) && strlen( $cc[ 6 ] ) ) //�����̎w�肪����ꍇ
						{
							$param[] = 'height';
							$param[] = $cc[ 6 ];
						}

						$ret .= self::object( $gm , $rec , $param );
						$ret .= '<br />';
					}
					if( isset( $cc[ 7 ] ) && $cc[ 7 ]=='drop'  )
					{
						$ret .= '<div class="drop-zone" data-type="image" data-name="'.$col.'"><p>�����Ƀt�@�C�����h���b�v</p><output></output></div>';
					}

				case 'file' : //�t�@�C������(type=file��input�^�O)���o��

					$option	        = '';
					$deleteText     = '�폜';
					$enablePost     = ( isset( $initial ) && strlen( $initial ) );
					$enableFileTemp = ( isset( $_POST[ $col . '_filetmp' ] ) && strlen( $_POST[ $col . '_filetmp' ] ) );
					$enableDelete   = ( isset( $_POST[ $col . '_DELETE' ] ) && 'true' == $_POST[ $col . '_DELETE' ] );

					if( $enablePost || $enableFileTemp ) //�t�@�C���܂��͈����p����񂪂���ꍇ
					{
						if( 'file' == $cc[ 1 ] ) //�t�@�C���t�H�[���̏ꍇ
						{
							$param  = Array( 'value' , $col );
							$filepath = self::value( $gm , $rec , $param );
							$ret   .= '<a href="' . $FileBase->geturl($filepath) . '" target="_blank">' . $filepath . '</a>';
							$ret   .= '<br />';
						}
					}

					if( isset( $cc[ 3 ] ) && strlen( $cc[ 3 ] ) ) //�I�v�V�����̎w�肪����ꍇ
						{ $option = $cc[ 3 ]; }

					if( isset( $cc[ 4 ] ) && strlen( $cc[ 4 ] ) ) //�폜�`�F�b�N�̕����w�肪����ꍇ
						{ $deleteText = $cc[ 4 ]; }

					$ret .= '<input name="' . $col . '" type="file" ' . $option . '>' . "\n";

					if( $enablePost ) //�t�@�C���̏�񂪂���ꍇ
					{
						$ret .= '<input name="' . $col . '_filetmp" type="hidden" value="' . $_POST[ $col ] . '" />' . "\n";
						$ret .= '<label><input type="checkbox" name="' . $col . '_DELETE" value="true" />' . $deleteText . '</label>';

					}
					else if( $enableFileTemp ) //�����p����񂪂���ꍇ
					{
						$ret .= '<input name="' . $col . '_filetmp" type="hidden" value="' . $_POST[ $col . '_filetmp' ] . '" />'. "\n";

						if( $enableDelete ) //�폜�`�F�b�N�̈����p��������ꍇ
							{ $ret .= '<label><input type="checkbox" name="' . $col . '_DELETE" value="true" checked="checked" />' . $deleteText . '</label>'; }
						else //�폜�`�F�b�N�̈����p�����Ȃ��ꍇ
							{ $ret .= '<label><input type="checkbox" name="' . $col . '_DELETE" value="true" />' . $deleteText . '</label>'; }
					}

					if( isset( $cc[ 5 ] ) && $cc[ 5 ]=='drop'  )
					{
						$ret .= '<div class="drop-zone" data-type="file" data-name="'.$col.'"><p>�����Ƀt�@�C�����h���b�v</p><output></output></div>';
					}

				case 'hidden':
						// �s������(type=hidden��input�^�O)���o��
					$option	= "";
					if( isset($cc[4]) ) { $option = $cc[4]; }
					if( isset($cc[5]) ) { $num = $cc[5]; }else{$num="";}
					
					if( isset($initial ) ){
						if( is_array($initial) ){
							foreach( $initial as $val ){
								$ret .= '<input name="'. $col .'['.$num.']" type="hidden" value="'. h($val) .'" '. $option .'/>'. "\n";
							}
						}
						else{
							$ret .= '<input name="'. $col .'" type="hidden" value="'. h($initial) .'" '. $option .' />'. "\n";
						}
					}
					else {
						$value = '';
						if( isset($cc[3]) ){ $value = h($cc[3]); }
						$ret .= '<input name="'. $col .'" type="hidden" value="'. $value .'" '. $option .'/>'. "\n";
					}
					break;
				case 'date':
					$option = "";
					if( isset($cc[4]) ) { $option = $cc[4]; }else{ $option="";}
					$y_key = $col.'_year';
					$m_key = $col.'_month';
					$d_key = $col.'_day';
					
					if( isset($cc[3]) && strlen($cc[3]) ){
						list($init_y, $init_m, $init_d) = explode( '-',$cc[3]);
					}else if( isset($initial ) && strlen($initial) ){
						$init_y = (int)substr($initial,0,4);
						$init_m = (int)substr($initial,5,2);
						$init_d = (int)substr($initial,8);
					}else{
						$init_y = $init_m = $init_d = "";
					}
					
					$ret = ccProc::controller($gm, $rec, array('form','text',$y_key,'4','4',$init_y,$option) ).$gm->dateFormat['y'];
					$ret .= ccProc::controller($gm, $rec, array('code','num_option',$m_key,'12',$init_m,'1','���I��',$option ) ).$gm->dateFormat['m'];
					$ret .= ccProc::controller($gm, $rec, array('code','num_option',$d_key,'31',$init_d,'1','���I��',$option ) ).$gm->dateFormat['d'];
					break;
                case 'calendar':
                    $option = "";
                    if( isset($cc[4]) ) { $option = $cc[4]; }else{ $option="";}
                    $y_key = $col.'_year';
                    $m_key = $col.'_month';
                    $d_key = $col.'_day';

                    if( isset($cc[3]) && strlen($cc[3]) ){
                        list($init_y, $init_m, $init_d) = explode( '-',$cc[3]);
                    }else if( isset($initial ) && strlen($initial) ){
                        $init_y = (int)substr($initial,0,4);
                        $init_m = (int)substr($initial,5,2);
                        $init_d = (int)substr($initial,8);
                    }else{
                        $init_y = $init_m = $init_d = "";
                    }

                    $ret = ccProc::controller($gm, $rec, array('form','text',$y_key,'4','4',$init_y,$option) ).$gm->dateFormat['y'];
                    $ret .= ccProc::controller($gm, $rec, array('code','num_option',$m_key,'12',$init_m,'1','���I��',$option ) ).$gm->dateFormat['m'];
                    $ret .= ccProc::controller($gm, $rec, array('code','num_option',$d_key,'31',$init_d,'1','���I��',$option ) ).$gm->dateFormat['d'];
                    $ret .= ccProc::controller($gm, $rec, array('form','text',"{$col}_calendar",null,null,null,"data-calendar='{$col}' style='display:none;'" ) );
                    break;

				case 'time':

					$formats = explode( '/' , $cc[ 3 ] );
					$suffixs = explode( '/' , $cc[ 4 ] );

					if( isset( $_POST[ $cc[ 2 ] ] ) && 0 != $_POST[ $cc[ 2 ] ] )
						{ $inits = explode( '/' , date( $cc[ 3 ] , $_POST[ $cc[ 2 ] ] ) ); }
					else
						{ $inits = explode( '/' , $cc[ 5 ] ); }

					if( isset( $cc[ 6 ] ) )
						{ $option = $cc[ 6 ]; }
					else
						{ $option = ''; }

					$ret = '';

					for( $i = 0 ; count( $formats ) > $i ; ++$i )
					{
						if( isset( $inits[ $i ] ) )
							{ $value = $inits[ $i ]; }
						else
							{ $value = null; }

						if( 'y' == strtolower( $formats[ $i ] ) )
						{
							$originValue = $_POST[ $cc[ 2 ] . '_year' ];

							if( 0 == $originValue ) //�N�̒l��0�̏ꍇ
								{ unset( $_POST[ $cc[ 2 ] . '_year' ] ); }

							$ret .= ccProc::controller( $gm , $rec , array( 'form' , 'text' , $cc[ 2 ] . '_year' , '4' , '4' , $value , $option ) ) . $suffixs[ $i ];

							$_POST[ $cc[ 2 ] . '_year' ] = $originValue;
						}
						else if( 'm' == strtolower( $formats[ $i ] ) )
							{ $ret .= ccProc::controller( $gm , $rec , array( 'code' , 'num_option' , $cc[ 2 ] . '_month' , '12' , $value , '1' , '--' , $option ) ) . $suffixs[ $i ]; }
						else if( 'd' == strtolower( $formats[ $i ] ) )
							{ $ret .= ccProc::controller( $gm , $rec , array( 'code' , 'num_option' , $cc[ 2 ] . '_day' , '31' , $value , '1' , '--' , $option ) ) . $suffixs[ $i ]; }
						else if( 'h' == strtolower( $formats[ $i ] ) )
							{ $ret .= ccProc::controller( $gm , $rec , array( 'code' , 'num_option' , $cc[ 2 ] . '_hour' , '23' , $value , '0' , '--' , $option ) ) . $suffixs[ $i ]; }
						else if( 'i' == strtolower( $formats[ $i ] ) )
							{ $ret .= ccProc::controller( $gm , $rec , array( 'code' , 'num_option' , $cc[ 2 ] . '_minute' , '59' , $value , '0' , '--' , $option ) ) . $suffixs[ $i ]; }
						else if( 's' == strtolower( $formats[ $i ] ) )
							{ $ret .= ccProc::controller( $gm , $rec , array( 'code' , 'num_option' , $cc[ 2 ] . '_sec' , '59' , $value , '0' , '--' , $option ) ) . $suffixs[ $i ]; }
					}
					break;
				//��������html5�ŃT�|�[�g���ꂽ�R�}���h
				case 'tel':
					// tel��input�^�O���o�́B
					$option = "";
					if( isset($cc[6]) ) { $option = $cc[6]; }
	
					if( isset($cc[3]) && strlen($cc[3]) ) { $option .= 'size="'. $cc[3] .'" '; }
					if( isset($cc[4]) && strlen($cc[4]) ) { $option .= 'maxlength="'. $cc[4] .'" '; }
	
					$value = "";
					if( isset($cc[5]) && strlen($cc[5]) ) {	$value = h($cc[5]); }
					if( isset($initial) ) { $value = h($initial); }
	
					$ret .= '<input type="tel" name="'. $col .'" value="'.$value.'" '.$option .'/>'. "\n";
					//placeholder
					break;
				case 'url':
					// url��input�^�O���o�́B
					$option = "";
					if( isset($cc[6]) ) { $option = $cc[6]; }
	
					if( isset($cc[3]) && strlen($cc[3]) ) { $option .= 'size="'. $cc[3] .'" '; }
					if( isset($cc[4]) && strlen($cc[4]) ) { $option .= 'maxlength="'. $cc[4] .'" '; }
	
					$value = "";
					if( isset($cc[5]) && strlen($cc[5]) ) {	$value = h($cc[5]); }
					if( isset($initial) ) { $value = h($initial); }
	
					$ret .= '<input type="url" name="'. $col .'" value="'.$value.'" '.$option .' autocapitalize="off"/>'. "\n";
					//placeholder
					break;
				case 'number':
					// number��input�^�O���o�́B
					$option = "";
					if( isset($cc[6]) ) { $option = $cc[6]; }
	
					//max min
					if( isset($cc[3]) && strlen($cc[3]) ) { $option .= 'max="'. $cc[3] .'" '; }
					if( isset($cc[4]) && strlen($cc[4]) ) { $option .= 'min="'. $cc[4] .'" '; }
	
					$value = "";
					if( isset($cc[5]) && strlen($cc[5]) ) {	$value = h($cc[5]); }
					if( isset($initial) ) { $value = h($initial); }
	
					$ret .= '<input type="number" name="'. $col .'" value="'.$value.'" '.$option .'/>'. "\n";
					break;
				case 'email':
					// email��input�^�O���o�́B
					$option = "";
					if( isset($cc[6]) ) { $option = $cc[6]; }
	
					if( isset($cc[3]) && strlen($cc[3]) ) { $option .= 'size="'. $cc[3] .'" '; }
					if( isset($cc[4]) && strlen($cc[4]) ) { $option .= 'maxlength="'. $cc[4] .'" '; }
	
					$value = "";
					if( isset($cc[5]) && strlen($cc[5]) ) {	$value = h($cc[5]); }
					if( isset($initial) ) { $value = h($initial); }
	
					$ret .= '<input type="email" name="'. $col .'" value="'.$value.'" '.$option .' autocapitalize="off" />'. "\n";
					
					//multiple,placeholder
					break;
			}
		
			return $ret;
		}

		/**
			@brief �e�[�u�������Ƀt�H�[���𐶐�����B
			@details
				�ŒZ : <!--# build �e�[�u���� �t�H�[����� #-->
				�t�� : <!--# build �e�[�u����.�l�J����/�\���J���� to �t�H�[�����.�t�H�[���� search �����N�G�� first �����l join �������� null ���I��l #-->
			@remarks
				�t���\����to�͏ȗ��\
		*/
		function build( &$iGM , $iRec , $iArgs ) //
		{
			global $TABLE_NAME;

			array_shift( $iArgs );

			$tableName   = array_shift( $iArgs );
			$buildType   = array_shift( $iArgs );
			$nullName    = '';
			$firstValue  = '';
			$searchQuery = '';
			$joinParam   = '';

			if( '->' == $buildType || 'to' == $buildType ) //�⏕�\���̏ꍇ
				{ $buildType = array_shift( $iArgs ); }

			List( $tableName , $useColumn )    = explode( '.' , $tableName );
			List( $valueColumn , $nameColumn ) = explode( '/' , $useColumn );
			List( $buildType , $formName )     = explode( '.' , $buildType );

			if( !$nameColumn ) //�J�����̎w�肪�Ȃ��ꍇ
				{ $nameColumn = 'name'; }

			if( !$valueColumn ) //�J�����̎w�肪�Ȃ��ꍇ
				{ $valueColumn = 'id'; }

			if( !$formName ) //�t�H�[�����̎w�肪�Ȃ��ꍇ
				{ $formName = $tableName; }

			while( count( $iArgs ) ) //����������ԌJ��Ԃ�
			{
				$paramName = array_shift( $iArgs );

				switch( $paramName ) //�p�����[�^���ŕ���
				{
					case 'search' : //�����N�G��
					{
						$searchQuery = array_shift( $iArgs );

						break;
					}

					case 'first' : //�����I��l
					{
						$firstValue = array_shift( $iArgs );

						break;
					}

					case 'join' : //����������
					{
						$joinParam = array_shift( $iArgs );

						break;
					}

					case 'null' : //���I��l
					{
						$nullName = array_shift( $iArgs );

						break;
					}

					default : //���̑�
						{ throw new LogicException( 'CC�\���G���[:����' . $paramName . '�͎�������Ă��܂���B' ); }
				}
			}

			if( !in_array( $tableName , $TABLE_NAME ) ) //�e�[�u�����̎w�肪�Ԉ���Ă���ꍇ
				{ throw new LogicException( 'CC�\���G���[:' . $tableName . '�e�[�u���͑��݂��܂���B' ); }

			$db = GMList::getDB( $tableName );

			if( !in_array( $valueColumn , $db->colName ) ) //�����J�����̎w�肪�Ԉ���Ă���ꍇ
				{ throw new LogicException( 'CC�\���G���[:' . $tableName . '��' . $valueColumn . '�J�����͑��݂��܂���B' ); }

			if( !in_array( $nameColumn , $db->colName ) ) //�����J�����̎w�肪�Ԉ���Ă���ꍇ
				{ throw new LogicException( 'CC�\���G���[:' . $tableName . '��' . $nameColumn . '�J�����͑��݂��܂���B' ); }

			$table = $db->getTable();

			if( $searchQuery ) //�����N�G���̎w�肪����ꍇ
			{
				parse_str( $searchQuery , $query );

				$query[ 'type' ] = $tableName;
				$table           = SystemUtil::getSearchResult( $query );
			}

			$row    = $db->getRow( $table );
			$values = Array();
			$names  = Array();

			if( $nullName ) //���I��l�̎w�肪����ꍇ
			{
				$values[] = '';
				$names[]  = $nullName;
			}

			for( $i = 0 ; $row > $i ; ++$i ) //�S�Ă̍s������
			{
				$rec      = $db->getRecord( $table , $i );
				$values[] = $db->getData( $rec , $valueColumn );
				$names[]  = $db->getData( $rec , $nameColumn );
			}

			$formName   = str_replace( Array( '!CODE001;' , ' ' ) , '!CODE101;' , $formName );
			$firstValue = str_replace( Array( '!CODE001;' , ' ' ) , '!CODE101;' , $firstValue );
			$joinParam  = str_replace( Array( '!CODE001;' , ' ' ) , '!CODE101;' , $joinParam );

			foreach( $names as &$ref ) //�S�Ă̒l������
				{ $ref = str_replace( Array( '!CODE001;' , ' ' ) , '!CODE101;' , $ref ); }

			foreach( $values as &$ref ) //�S�Ă̒l������
				{ $ref = str_replace( Array( '!CODE001;' , ' ' ) , '!CODE101;' , $ref ); }

			$result = '';

			switch( $buildType ) //�o�͕��@�ŕ���
			{
				case 'select' : //�P��I���v���_�E��
				{
					$result .= $iGM->getCCResult( $iRec , implode( ' ' , Array( '<!--#' , 'form' , 'option' , $formName , $firstValue , implode( '/' , $values ) , implode( '/' , $names ) , ' #-->' ) ) );

					break;
				}

				case 'selects'  : //�����I���v���_�E��
				case 'multiple' : //�����I���v���_�E��
				{
					$result .= $iGM->getCCResult( $iRec , implode( ' ' , Array( '<!--#' , 'form' , 'multiple' , $formName , $firstValue , implode( '/' , $values ) , implode( '/' , $names ) , ' #-->' ) ) );

					break;
				}

				case 'radio' : //���W�I�{�^��
				{
					$result .= $iGM->getCCResult( $iRec , implode( ' ' , Array( '<!--#' , 'form' , 'radio' , $formName , $firstValue , $joinParam , implode( '/' , $values ) , implode( '/' , $names ) , ' #-->' ) ) );

					break;
				}

				case 'check'    : //�`�F�b�N�{�b�N�X
				case 'checkbox' : //�`�F�b�N�{�b�N�X
				{
					$result .= $iGM->getCCResult( $iRec , implode( ' ' , Array( '<!--#' , 'form' , 'checkbox' , $formName , $firstValue , $joinParam , implode( '/' , $values ) , implode( '/' , $names ) , ' #-->' ) ) );

					break;
				}
			}

			return $result;
		}

		/**
			@brief �e�[�u���̒l���J��Ԃ��o�͂���
			@details
				�ŒZ : <!--# repeat �e�[�u���� �p�[�c�� #-->
				�t�� : <!--# repeat �e�[�u���� to �p�[�c�� search �����N�G�� row �ő吔 #-->
			@remarks
				�t���\����to�͏ȗ��\
		*/
		function repeat( &$iGM , $iRec , $iArgs ) //
		{
			global $TABLE_NAME;

			array_shift( $iArgs );
			$result = '';

			$tableName = array_shift( $iArgs );
			$partsName = array_shift( $iArgs );

			if( '->' == $partsName || 'to' == $partsName ) //�⏕�\���̏ꍇ
				{ $partsName = array_shift( $iArgs ); }

			if( preg_match( '/^(\d+)~(\d+)$/' , $tableName , $matches ) ) //���l�w��̏ꍇ
			{
				$begin  = ( int )( $matches[ 1 ] );
				$end    = ( int )( $matches[ 2 ] );
				$values = Array();

				if( $begin < $end )
				{
					for( $i = $begin ; $end >= $i ; ++$i )
						{ $values[] = $i; }
				}
				else
				{
					for( $i = $begin ; $end <= $i ; --$i )
						{ $values[] = $i; }
				}

				return self::repeatString( $iGM , $iRec , Array( 'repeatString' , implode( '/' , $values ) , $partsName ) );
			}

			if( !in_array( $tableName , $TABLE_NAME ) ) //�e�[�u�����̎w�肪�Ԉ���Ă���ꍇ
				{ throw new LogicException( 'CC�\���G���[:' . $tableName . '�e�[�u���͑��݂��܂���B' ); }

			while( count( $iArgs ) ) //����������ԌJ��Ԃ�
			{
				$paramName = array_shift( $iArgs );

				switch( $paramName ) //�p�����[�^���ŕ���
				{
					case 'search' : //�����N�G��
					{
						$searchQuery = array_shift( $iArgs );

						break;
					}

					case 'row' : //�s��
					{
						$maxRow = array_shift( $iArgs );

						break;
					}

					default : //���̑�
						{ throw new LogicException( 'CC�\���G���[:����' . $paramName . '�͎�������Ă��܂���B' ); }
				}
			}

			$db    = GMList::getDB( $tableName );
			$table = $db->getTable();

			if( $searchQuery ) //�����N�G���̎w�肪����ꍇ
			{
				parse_str( $searchQuery , $query );

				$query[ 'type' ] = $tableName;
				$table           = SystemUtil::getSearchResult( $query );
			}

			$originRow = $db->getRow( $table );

			if( $maxRow ) //�ő�s�̎w�肪����ꍇ
				{ $table = $db->limitOffset( $table , 0 , $maxRow ); }
			else //�ő�s�̎w�肪�Ȃ��ꍇ
				{ $maxRow = $originRow; }

			$row = $db->getRow( $table );

			if( !$row ) //���R�[�h���Ȃ��ꍇ
			{
				$result .= $iGM->getCCResult( $iRec , '<!--# adapt ' . $partsName . '_failed #-->' );

				return $result;
			}

			$result = '';

			$result .= $iGM->getCCResult( $iRec , '<!--# adapt ' . $partsName . '_head #-->' );

			if( $originRow > $maxRow ) //�\������茟�����ʂ������ꍇ
				{ $result .= $iGM->getCCResult( $iRec , '<!--# adapt ' . $partsName . '_head_over #-->' ); }

			for( $i = 0 ; $maxRow > $i ; ++$i ) //�o�͐��J��Ԃ�
			{
				$iGM->setVariable( 'num' , $i + 1 );

				if( $row <= $i ) //���R�[�h�����Ȃ��ꍇ
					{ $result .= $iGM->getCCResult( null , '<!--# adapt ' . $partsName . '_empty #-->' ); }
				else //���R�[�h������ꍇ
				{
					$rec = $db->getRecord( $table , $i );

					$result .= $iGM->getCCResult( $rec , '<!--# adapt ' . $partsName . ' #-->' );
				}
			}

			if( $originRow > $maxRow ) //�\������茟�����ʂ������ꍇ
				{ $result .= $iGM->getCCResult( $iRec , '<!--# adapt ' . $partsName . '_foot_over #-->' ); }

			$result .= $iGM->getCCResult( $iRec , '<!--# adapt ' . $partsName . '_foot #-->' );

			return $result;
		}

		/**
			@brief �e�[�u���̒l���J��Ԃ��o�͂���
			@details
				�ŒZ : <!--# repeatString ������ �p�[�c�� #-->
				�t�� : <!--# repeatString ������ to �p�[�c�� #-->
			@remarks
				�t���\����to�͏ȗ��\
		*/
		function repeatString( &$iGM , $iRec , $iArgs ) //
		{
			array_shift( $iArgs );

			$elements  = explode( '/' , array_shift( $iArgs ) );
			$partsName = array_shift( $iArgs );
			$i         = 0;

			if( '->' == $partsName || 'to' == $partsName ) //�⏕�\���̏ꍇ
				{ $partsName = array_shift( $iArgs ); }

			$result = '';

			$result .= $iGM->getCCResult( $iRec , '<!--# adapt ' . $partsName . '_head #-->' );

			foreach( $elements as $element ) //�o�͐��J��Ԃ�
			{
				$iGM->setVariable( 'num' , ++$i );
				$iGM->setVariable( 'value' , $element );

				$result .= $iGM->getCCResult( $iRec , '<!--# adapt ' . $partsName . ' #-->' );
			}

			$result .= $iGM->getCCResult( $iRec , '<!--# adapt ' . $partsName . '_foot #-->' );

			return $result;
		}

		// �����l���G�X�P�[�v���ꂽ�v�f�ƈ�v���Ȃ����ߏ����l���G�X�P�[�v�f�[�^�ɂ���
		function initEscape( $str )
		{
			if( !is_array($str) )
			{
				$str = str_replace( " ", "!CODE001;", $str );
			}
			else
			{
				$count = count($str);
				for( $i=0; $i<$count; $i++ )
				{
					$str[$i] = str_replace( " ", "!CODE001;", $str[$i] );
				}
			}

			return $str;
		}
		
		// �e���v���[�g�̕\���Ɏg�p�����GUIManager�̃C���X�^���X�ɐݒ肵���l���o�͏o����B 
		function variable(&$gm, $rec, $cc)
		{
			$ret = "";
			if(  is_null( $gm->variable[$cc[1]] )  ) { throw new InternalErrorException( 'CommandComment Null Pointer Error -> variable : '. $cc[1] ); }
			$ret .= $gm->variable[$cc[1]];
			
			return $ret;
		}
		// �e���v���[�g�̕\���Ɏg�p�����GUIManager�̃C���X�^���X�ɐݒ肵���l���o�͏o����B 
		// ���ݒ�ł��G���[�o�͂�����Ȃ��B
		function safeVariable(&$gm, $rec, $cc)
		{
			$ret = "";
			if( isset($gm->variable[ $cc[1] ]) && ! is_null( $gm->variable[ $cc[1] ] )  )	{ $ret	 .= $gm->variable[ $cc[1] ]; }
			
			return $ret;
		}

		/**
			@brief   �e���v���[�g��ŕϐ��̓ǂݏ������s���B
			@details �w�肵�����O�̕ϐ��ɒl��ǂݏ������܂��B�l��ccProc��static�ϐ�$MemoCCValues�Ɋi�[����܂��B
				�������� : <!--# memo write �l to �ϐ��� #-->
				�ǂݍ��� : <!--# memo read �ϐ��� #-->
		*/
		function memo( &$gm , $rec , $cc ) //
		{
			array_shift( $cc );

			if( !count( $cc ) ) //�������Ȃ��ꍇ
				{ throw new LogicException( 'CC�\���G���[:����������܂���' ); }

			$procMode = array_shift( $cc );

			switch( $procMode ) //�v�����ꂽ�������[�h�ŕ���
			{
				case 'write' : //�ϐ��ւ̏������݂̏ꍇ
				{
					$varName     = array_pop( $cc );
					$conjunction = array_pop( $cc );
					$writeValue  = implode( ' ' , $cc );

					if( 'to' != $conjunction ) //���@���������Ȃ��ꍇ
						{ throw new LogicException( 'CC�\���G���[:����' . $conjunction . '�͎�������Ă��܂���' ); }

					self::$MemoCCValues[ $varName ] = $writeValue;

					break;
				}

				case 'read' : //�ϐ�����̓ǂݍ��݂̏ꍇ
				{
					$varName = array_shift( $cc );

					if( isset( self::$MemoCCValues[ $varName ] ) ) //�ϐ������݂���ꍇ
						{ return self::$MemoCCValues[ $varName ]; }

					break;
				}

				default : //���̑��̏ꍇ
					{ throw new LogicException( 'CC�\���G���[:����' . $procMode . '�͎�������Ă��܂���' ); }
			}
		}
		
		// �e���v���[�g��\�����悤�Ƃ��Ă���y�[�W�ւ̃��N�G�X�g�œn���ꂽGET�p�����[�^��\���o����B
		function get(&$gm, $rec, $cc)
		{
			array_shift( $cc );
			List( $name , $index ) = $cc;

			$ret = "";

				if( is_array( $_GET[ $name ] ) ) //POST���z��̏ꍇ
				{
					if( !isset( $index ) )
					{ //index���w�肳��Ă��Ȃ��ꍇ
						$ret .= implode( '/' , $_GET[ $name ] );
					}
					else
					{ //index���w�肳��Ă���ꍇ
						$ret .= $_GET[ $name ][ $index ];
				}
				}
				else
				{ //POST���X�J���̏ꍇ
					$ret .= $_GET[ $name ];
				}

			return h($ret);
		}

		
		// �e���v���[�g��\�����悤�Ƃ��Ă���y�[�W�ւ̃��N�G�X�g�œn���ꂽPOST�p�����[�^��\���o����B
		function post( &$gm , $rec , $cc )
		{
			array_shift( $cc );
			List( $name , $index ) = $cc;

			$ret = "";

			if( is_array( $_POST[ $name ] ) ) //POST���z��̏ꍇ
			{
				if( !isset( $index ) )
				{ //index���w�肳��Ă��Ȃ��ꍇ
					$ret .= implode( '/' , $_POST[ $name ] );
				}
				else
				{ //index���w�肳��Ă���ꍇ
					$ret .= $_POST[ $name ][ $index ];
			}
			}
			else
			{ //POST���X�J���̏ꍇ
				$ret .= $_POST[ $name ];
			}

			return h($ret);
		}
/*
		// $_SESSION�̒l���o��
		function session(&$gm, $rec, $cc)
		{
			if( is_array( $_SESSION[$cc[1]] ) )
			{
				if( !isset($cc[2]) ) { throw new InternalErrorException( 'CommandComment Null Pointer Error -> session array index' ); }
				$ret .= $_SESSION[$cc[1]][$cc[2]];
			}
			else	{ $ret .= $_SESSION[$cc[1]]; }

			return $ret;
		}
*/

		// $_REQUEST�̒l���o��
		function request(&$gm, $rec, $cc)
		{
			$ret = '';
			if( is_array( $_REQUEST[$cc[1]] ) )
			{
				if( !isset($cc[2]) ) { throw new InternalErrorException( 'CommandComment Null Pointer Error -> request array index' ); }
				$ret .= $_REQUEST[$cc[1]][$cc[2]];
			}
			else	{ $ret .= $_REQUEST[$cc[1]]; }
			
			return h($ret);
		}
		
		// value��timestamp��\������ꍇ�Ɏg�p����format���w��o����B
		function setTimeFormat(&$gm, $rec, $cc)
		{
			$ret = "";
			$gm->setTimeFormat(  str_replace(  Array( "!CODE000;","!CODE001;"), Array("/"," ") , $cc[1]) );
			
			return $ret;
		}

		function setTimeFormatOnce(&$gm, $rec, $cc)
		{
			$ret = "";
			$gm->setTimeFormatOnce(  str_replace(  Array( "!CODE000;","!CODE001;"), Array("/"," ") , $cc[1]) );

			return $ret;
		}

		// ���[�U�[�����o��
		function login(&$gm, $rec, $cc)
		{
			global $NOT_LOGIN_USER_TYPE;
			global $loginUserType;
			global $loginUserRec;
			
			$ret = "";
			switch($cc[1])
			{
			case 'type':
				$ret = $loginUserType;
				break;
			default:
				if( $loginUserType != $NOT_LOGIN_USER_TYPE )
				{
					$tgm = GMList::getGM($loginUserType);
					$ret = ccProc::value( $tgm, $loginUserRec, $cc );
				}
				break; 
			}

			return $ret;
		}

		// Command.php�Œ�`����Ă���R�����g�R�}���h���Ăяo�������o����B
		function code(&$gm, $rec, $cc)
		{
			$ret  = "";
			$args = array_slice($cc,2);
			$e = new Command();
			$e->{$cc[1]}( $gm, $rec, $args );
			$ret .= $e->getBuffer();
			
			return $ret;
		}

		// Extension.php�Œ�`����Ă���R�����g�R�}���h���Ăяo�������o����B
		function ecode(&$gm, $rec, $cc)
		{
			$ret  = "";
			$args = array_slice($cc,2);
			$e = new Extension();
			$e->{$cc[1]}( $gm, $rec, $args );
			$ret .= $e->getBuffer();
			
			return $ret;
		}

		
		// System.php����System�N���X�Œ�`����Ă���R�����g�R�}���h���Ăяo�������o����B
		function syscode(&$gm, $rec, $cc)
		{
			$ret  = "";
			$args = array_slice($cc,2);

			$sys  = SystemUtil::getSystem( isset($_GET["type"])?$_GET["type"]:null );
			
			$sys->{$cc[1]}( $gm, $rec, $args );
			$ret .= $sys->getBuffer();
			
			return $ret;
		}

		// ./module/�ȉ��ɐݒu����./module/module.php�ɂ��include���ꂽ���W���[���t�@�C�����Œ�`���ꂽ���W���[���N���X���̃��\�b�h���Ăяo�������\�B
		function mod(&$gm, $rec, $cc)
		{
			$ret  = "";
			$args = array_slice($cc,3);

			$class_name = 'mod_'.$cc[1];
			if( !class_exists( $class_name ) ){
				return $ret;
			}

			$sys = new $class_name();

			$sys->{$cc[2]}( $gm, $rec, $args );
			$ret .= $sys->getBuffer();

			return $ret;
		}

		// ./custom/view�ȉ��ɐݒu�����include���ꂽ���W���[���t�@�C�����Œ�`���ꂽ���W���[���N���X���̃��\�b�h���Ăяo�������\�B
		function view(&$gm, $rec, $cc)
		{
			global $view_path;

			$ret  = "";
			$args = array_slice($cc,3);

			$class_name = $cc[1].'View';

			if( !class_exists( $class_name ) ){
				if( file_exists( $view_path.$class_name.'.php') )
				{
					include_once $view_path.$class_name.'.php';
					if ( !class_exists( $class_name ) ) {
						global $ALL_DEBUG_FLAG;
						if( $ALL_DEBUG_FLAG ){ d( '['.$cc[1].'View] not found.' ,'view');}
						return $ret;
					}
				}else{
					global $ALL_DEBUG_FLAG;
					if( $ALL_DEBUG_FLAG ){ d( '['.$cc[1].'View] not found.' ,'view');}
					return $ret;
				}
			}

			$sys = new $class_name();

			$sys->{$cc[2]}( $gm, $rec, $args );
			$ret .= $sys->getBuffer();

			return $ret;
		}

		// �����ɗ^����ꂽ�������v�Z���Ƃ��ĉ��߂��A�v�Z���ʂ�Ԃ��B
		function calc(&$gm, $rec, $cc)
		{
			$ret  = "";
			$calc = join('',array_slice($cc,1));
			if( ! SystemUtil::is_expression($calc ) ){
				return $ret;
			}
			eval( '$ret = '.$calc.';' );

			return $ret;
		}

		// ���̃R�}���h�͑��̃R�}���h�̑O�ɕt����`�ŗ��p���鎖�ɂ��A�߂�l�Ɋ܂܂�锼�p�X�y�[�X���G�X�P�[�v�������ʂ�Ԃ��B
		function escp(&$gm, $rec, $cc)
		{
			$ret = "";
			$cc  = array_slice($cc,1);
			$ret = str_replace( Array( '!CODE001;', ' ') , '!CODE101;', ccProc::controller($gm, $rec, $cc) );
			///$ret = str_replace( '!CODE001;' , '!CODE101;', ccProc::controller($gm, $rec, $cc) );
			return $ret;
		}

		function ent(&$gm, $rec, $cc)
		{
			$ret = "";
			$cc  = array_slice($cc,1);
			$ret = h( ccProc::controller($gm, $rec, $cc) );
			return $ret;
		}

		// ���̃R�}���h�͑��̃R�}���h�̑O�ɕt����`�ŗ��p���鎖�ɂ��A�߂�l��int�^��cast���ĕԂ��B
		function int(&$gm, $rec, $cc)
		{
			$ret = "";
			$cc  = array_slice($cc,1);
			$ret = (int)ccProc::controller($gm, $rec, $cc);
			
			return $ret;
		}
		// ���̃R�}���h�͑��̃R�}���h�̑O�ɕt����`�ŗ��p���鎖�ɂ��A�߂�l��int�^��cast���ĕԂ��B
		function bool(&$gm, $rec, $cc)
		{
			$ret			 = "";
			$cc = array_slice($cc,1);
			$ret = SystemUtil::convertBool(ccProc::controller($gm, $rec, $cc)) ? 'TRUE':'FALSE';
			
			return $ret;
		}
		// ���̃R�}���h�͑��̃R�}���h�̑O�ɕt����`�ŗ��p���鎖�ɂ��A�߂�l��urlencode���ĕԂ��B
		function urlenc(&$gm, $rec, $cc)
		{
			$ret			 = "";
			$cc = array_slice($cc,1);

			$ret = ccProc::controller($gm, $rec, $cc);

			if( FALSE !== strpos( $ret , '!CODE000;' ) || FALSE !== strpos( $ret , '!CODE001;' ) || FALSE !== strpos( $ret , '!CODE002;' ) )
			{
				$ret = urlencode(str_replace( array("!CODE000;","!CODE001;","!CODE002;"), array("/"," ","\\") , $ret ));
				$ret = str_replace( array("/"," ","\\"), array("!CODE000;","!CODE001;","!CODE002;"), $ret );
			}
			else
				{ $ret = urlencode( $ret ); }

			return $ret;
		}

		// ���̃R�}���h�͑��̃R�}���h�̑O�ɕt����`�ŗ��p���鎖�ɂ��A�߂�l�̑�1�����Ɏw�肳�ꂽ�������2�����Ɏw�肳�ꂽ�����ɒu�����ĕԂ��B
		function rep(&$gm, $rec, $cc)
		{
			$ret			 = "";
			$search = $cc[1];
			$replace = $cc[2];
			$cc = array_slice($cc,3);
			$ret = str_replace( $search, $replace, ccProc::controller($gm, $rec, $cc));

			return $ret;
		}

		// ���̃R�}���h�͑��̃R�}���h�̑O�ɕt����`�ŗ��p���鎖�ɂ��A�߂�l�̐��l�ɃJ���}��t������B
		function comma( &$gm , $rec , $cc ) //
		{
			$ret = '';
			$cc  = array_slice( $cc , 1 );
			$ret = ccProc::controller( $gm , $rec , $cc );
			$ret = number_format( floor( $ret ) ) . strstr( $ret , '.' );

			return $ret;
		}

		// ���̃R�}���h�͑��̃R�}���h�̑O�ɕt����`�ŗ��p���鎖�ɂ��A�߂�l��URL�������N�ɂ���B
		function urlLink( &$gm , $rec , $cc ) //
		{
			$ret = '';
			$cc  = array_slice( $cc , 1 );
			$ret = ccProc::controller( $gm , $rec , $cc );

			$url = '/(?<!href=")https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+/';
			$ret = preg_replace_callback( $url, function( $matches ){ return '<a href="'.$matches[0].'">'.$matches[0]."</a>"; }, $ret);

			return $ret;
		}

		/**
		 * substitute�R�}���h�B
		 * ���̃R�}���h�͑��̃R�}���h�̑O�ɕt����`�ŗ��p���鎖�ɂ��A�߂�l����̏ꍇ�ɑ������̒l���o�͂���B
		 *
		 */
		function sub(&$gm, $rec, $cc)
		{

			$ret			 = "";
			$cc2 = array_slice($cc,2);
			$ret = ccProc::controller($gm, $rec, $cc2);

			if( !strlen($ret) ){
				$ret = $cc[1];
			}
			
			return $ret;
		}
		
		// ���̃R�}���h�͑��̃R�}���h�̑O�ɕt����`�ŗ��p���鎖�ɂ��A�߂�l�̃^�O����������B
		function striptag( &$gm , $rec , $cc ) //
		{
			$ret = '';
			$cc = array_slice( $cc , 1 );
			$ret = ccProc::controller( $gm , $rec , $cc );
			$ret = strip_tags( $ret );

			return $ret;
		}
		
		// ���̃R�}���h�͑��̃R�}���h�̑O�ɕt����`�ŗ��p���鎖�ɂ��A�o�͌��ʂ��ȈՓI�ɃL���b�V������B
		function cache( &$gm , $rec , $cc ) //
		{
			$ret  = '';
			$time = $cc[ 1 ];
			$cc   = array_slice( $cc , 2 );
			$file = 'file/cc_cache/' . md5( implode( $cc , ' ' ) ) . 'cc';

			if( !is_file( $file ) || time() - $time > filemtime( $file ) )
			{
				$ret = ccProc::controller( $gm , $rec , $cc );

				file_put_contents( $file , $ret );
			}
			else
				{ $ret = file_get_contents( $file ); }

			return $ret;
		}

		/**
			@brief     CC�̎��s��񓯊��w�肷��B
			@attention �^�[�Q�b�g���R�[�h���̐ݒ�̖��ŁA�������s�Ƃ͈قȂ錋�ʂ��Ԃ�\��������܂��B
		*/
		function async( &$gm , $rec , $cc ) //
		{
            global $controllerName;
			TemplateCache::$NoCache = true;

			if( 'preview' == strtolower( $controllerName ) ) //�v���r���[��ʂ̏ꍇ
			{
				array_shift( $cc );

				return ccProc::controller( $gm , $rec , $cc );
			}

			array_shift( $cc );

			$asyncToken = md5( rand() );

			$_SESSION[ 'async_cc_' . $asyncToken ] = '<!--# ' . implode( ' ' , $cc ) . ' #-->';

			$ret  = '<script data-async-cc-id="' . $asyncToken . '">';
			$ret .= '$( function(){ callASyncCC( "' . $asyncToken . '" ); } );';
			$ret .= '</script>';

			return $ret;
		}
		
		// $cc �̓��e��A�����ďo��
		function join(&$gm, $rec, $cc)
		{
			$ret = "";
			$cc  = array_slice($cc,1);
			$ret = join( '' , $cc );
			
			return $ret;
		}

		// �����ɗ^����ꂽ������ϐ��Ƃ��ĉ��߂��A���g��Ԃ��B
		function val(&$gm, $rec, $cc)
		{
			$ret = "";
			
			if( ! preg_match( '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $cc[1] ) ){
				return $ret;
			}
			
			eval( 'global $'.$cc[1].'; $ret = $'.$cc[1].';' );
			
            if(is_bool($ret)){
    			if( $ret )	 { $ret	 = 'TRUE'; }
	    		else		 { $ret	 = 'FALSE'; }
            }
            
			return $ret;
		}
		
		static $register;
		function regist(&$gm, $rec, $cc)
		{
			$action = strtolower($cc[1]);
			$name = strtolower($cc[2]);
			
			$thru = false;
			if( strpos( $action, 'thru' ) === 0 ){
				$action = substr( $action,5);
				$thru = true;
			}
			
			if( !isset(self::$register[$name] ) ){
				self::$register[$name] = 0;
			}
			
			switch( $action )
			{
				case 'add':
					self::$register[$name] += (int)$cc[3];
					if( $thru ){ return $cc[3]; }
					break;
				case 'sub':
					self::$register[$name] -= (int)$cc[3];
					if( $thru ){ return $cc[3]; }
					break;
				case 'mul':
					self::$register[$name] *= (int)$cc[3];
					if( $thru ){ return $cc[3]; }
					break;
				case 'div':
					self::$register[$name] /= (int)$cc[3];
					if( $thru ){ return $cc[3]; }
					break;
				case 'cmp':
					return self::$register[$name] == $cc[3]?1:0;
				case 'inc':
					self::$register[$name]++;
					if( $thru ){ return $cc[3]; }
					break;
				case 'dec':
					self::$register[$name]--;
					if( $thru ){ return $cc[3]; }
					break;
				case 'set': //�l��ݒ肷��
					self::$register[$name] = (int)$cc[3];
					if( $thru ){ return $cc[3]; }
					break;
				case 'get': //�l���擾����
					break;
				case 'clear': //�N���A����
				case 'reset': //�N���A����
					self::$register[$name] = 0;
					if( $thru ){ return $cc[3]; }
					break;
				case 'pop': //�l���Q�Ƃ��N���A����
					$num = self::$register[$name];
					self::$register[$name] = 0;
					return $num;
				default:
					exit('not register operation.');
			}
			return self::$register[$name];
		}
			
		// ���̃R�}���h�͑���template��template���ɓW�J���鎖���o����B
		// �������Atemplate�e�[�u���ɁuINCLUDE_DESIGN�v���x����ݒ肳�ꂽ���̂Ɍ���B
		function drawDesign(&$gm, $rec, $cc)
		{
			global $loginUserType;
			global $NOT_LOGIN_USER_TYPE;
			global $loginUserRank;
			
            $file = Template::getTemplate( $loginUserType , $loginUserRank , $cc[1] , 'INCLUDE_DESIGN' );

            $partkey = null;
            if( isset( $cc[2] ) ){ $partkey = $cc[2]; }
            
            if( ! strlen($file) ){
                $ret = "<br/><br/><br/>!include error! -> ".$cc[1]."<br/><br/><br/>";
            }else if( is_null($gm) ){
            	if( $loginUserType == $NOT_LOGIN_USER_TYPE ){
            		$ret = SystemUtil::getGMforType('system')->getString( $file , $rec , $partkey );
            	}else{
            		$ret = SystemUtil::getGMforType($loginUserType)->getString( $file , $rec , $partkey );
            	}
                
            }else{
                $ret = $gm->getString( $file , $rec , $partkey );
            }

			return $ret;
		}

		function drawAdapt(&$gm, $rec, $cc)
		{
			global $loginUserType;
			global $NOT_LOGIN_USER_TYPE;
			global $loginUserRank;
			
            $file = $gm->getCurrentTemplate();

            $partkey = null;
            if( isset( $cc[1] ) ){ $partkey = $cc[1]; }
            
            if( ! strlen($file) ){
                $ret = "<br/><br/><br/>!adapt error! -> ".$cc[1]."<br/><br/><br/>";
            }else if( ! strlen($file) ){
                $ret = "<br/><br/><br/>!adapt part error! -> ".$cc[1]."<br/><br/><br/>";
            }else if( is_null($gm) ){
            	if( $loginUserType == $NOT_LOGIN_USER_TYPE ){
            		$ret = SystemUtil::getGMforType('system')->getString( $file , $rec , $partkey );
            	}else{
            		$ret = SystemUtil::getGMforType($loginUserType)->getString( $file , $rec , $partkey );
            	}
                
            }else{
                $ret = $gm->getString( $file , $rec , $partkey );
            }

			return $ret;
		}
		
		//�����ϊ��e�[�u���ɏ]���ĊG�������o�͂���
		function emoji(&$gm, $rec, $cc){
			global $EMOJI_CHARSET_MAP;
			global $terminal_type;

			$ret = '';
			
			if( !is_array($EMOJI_CHARSET_MAP) || !is_numeric($cc[1])){ return ""; }
			
			eval( '$ret = '. $EMOJI_CHARSET_MAP[ $cc[1] ].";" );
			return $ret;
		}
        
		/**
			@brief     ���ɌĂяo�����R�}���h�R�����g�̂��߂ɁA�}���p�����[�^��ݒ肷��B
			@exception InvalidCCArgumentException �s���ȃp�����[�^���w�肵���ꍇ�B
			@details   �p�����[�^�͎��̏��Ŏw�肵�܂��B
				@li 0 �}���p�����[�^�̖��O�B
				@li 1 �}���p�����[�^�̒l�B
				@li 2 �}���p�����[�^���g�p����R�}���h�R�����g���B�ȗ������ꍇ�͑S�ẴR�}���h�R�����g���Q�Ɖ\�ł��B
				@li 3 �}���p�����[�^�̎����Bonce/all�̂����ꂩ���w�肵�܂��Bonce�p�����[�^�͈�x�ł��Q�Ƃ����Ə���������܂��B�ȗ������ꍇ��once�ƂȂ�܂��B
			@param[in] $iGM_  GUIManager�I�u�W�F�N�g�B
			@param[in] $iRec_ ���R�[�h�f�[�^�B
			@param[in] $iCC_  �R�}���h�R�����g�p�����[�^�B
			@attension �R�}���h�R�����g�͌ʂ�weave�ɑΉ�����K�v������܂��B\n
			           �}���p�����[�^�̎擾�ɂ�Weave�N���X���g�p���Ă��������B
		*/
		function weave( &$iGM_ , $iRec_ , $iCC_ )
		{
			List( $ccName , $paramName , $paramValue , $targetName , $paramLife ) = $iCC_;

			if( !$paramName ) //�}���p�����[�^�����w�肳��Ă��Ȃ��ꍇ
				{ throw new InvalidCCArgumentException( '���� $paramName �͖����ł�' ); }

			if( !$targetName ) //�ΏۃR�}���h�R�����g�����ݒ肳��Ă��Ȃ��ꍇ
				{ $targetName = '*'; }

			if( $paramLife ) //�}���p�����[�^�̎������ݒ肳��Ă���ꍇ
			{
				switch( $paramLife ) //�ݒ�l�ŕ���
				{
					case 'once' : //��x����
					case 'all'  : //�i�v
						{ break; }

					default : //�l�̌��Ɉ�v���Ȃ��ꍇ
						{ throw new InvalidCCArgumentException( '���� $paramLife �͖����ł�[' . $paramLife . ']' ); }
				}
			}
			else //�}���p�����[�^�̎������ݒ肳��Ă��Ȃ��ꍇ
				{ $paramLife = 'once'; }

			Weave::Push( $paramName , $paramValue , $targetName , $paramLife );
		}

		/**
			@brief     �}���p�����[�^���폜����B
			@exception InvalidCCArgumentException �s���ȃp�����[�^���w�肵���ꍇ�B
			@details   �p�����[�^�͎��̏��Ŏw�肵�܂��B
				@li 0 �폜����}���p�����[�^���B
				@li 1 �}���p�����[�^���g�p����R�}���h�R�����g���B�ȗ������ꍇ�͑S�ẴR�}���h�ɑ΂���p�����[�^���폜���܂��B
			@param[in] $iGM_  GUIManager�I�u�W�F�N�g�B
			@param[in] $iRec_ ���R�[�h�f�[�^�B
			@param[in] $iCC_  �R�}���h�R�����g�p�����[�^�B
		*/
		function clearWeave( &$iGM_ , $iRec_ , $iCC_ )
		{
			List( $ccName , $paramName , $targetName ) = $iCC_;

			if( !$paramName ) //�}���p�����[�^�����w�肳��Ă��Ȃ��ꍇ
				{ throw new InvalidCCArgumentException( '���� $paramName �͖����ł�' ); }

			Weave::Pop( $paramName , $targetName );
		}



    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
    //�o�͂ł͂Ȃ��V�X�e�����ɍ�p�������ȃR�����g�R�}���h
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
        //template�̏����t���p�[�T�[
        //C����̏����t���R���p�C��(#ifdef)�݂����Ȃ���
        //@return boolean(true/false)
        function ifbegin(&$gm, $rec, $cc)
		{
			global $PASSWORD_MODE;
			global $REMINDER_MODE;

            switch( $cc[1] ){
                case 'not':
                case '!':
                    //�����̔��]
                    return ! ccProc::ifbegin($gm, $rec, array_slice($cc,1));
                case 'alias':
                	$alias_gm = SystemUtil::getGMforType( $cc[2] );
                	$db = $alias_gm->getDB();
                	$alias_rec = $db->selectRecord( $cc[3] );
                	
                    return ccProc::ifbegin($alias_gm, $alias_rec, array_slice($cc,3));
                case 'bool':
                case 'boolean':
                    $db = $gm->getDB();
                    return SystemUtil::convertBool($db->getData( $rec , $cc[2] ));
                case 'intime'://�w��J�������w����ԓ����ǂ���
                    $db = $gm->getDB();
                    $time = $db->getData( $rec , $cc[2] );
                    $period = time() - $cc[3]*3600;
                    return $time > $period;
                case 'val_intime'://�w��J�������w����ԓ����ǂ���
                    $period = time() - $cc[3]*3600;
                    return $cc[2] > $period;
                case 'isget':
                    //get�ɂ��̈��������݂��邩�ǂ����B
                    return isset($_GET[$cc[2]]) && strlen($_GET[$cc[2]]);
                case 'ispost':
                    //post�ɂ��̈��������݂��邩�ǂ����B
                    return isset($_POST[$cc[2]]) && strlen($_POST[$cc[2]]);
                case 'issession':
                    //session�ɂ��̈��������݂��邩�ǂ����B
					TemplateCache::$NoCache = true;
                    return isset($_SESSION[$cc[2]]) && strlen($_SESSION[$cc[2]]);
					TemplateCache::$NoCache = true;
                case 'session':
                    //session�ɂ��̈��������݂��邩�ǂ����B���݂����ꍇ��bool��
                    return isset($_SESSION[$cc[2]]) ? SystemUtil::convertBool($_SESSION[$cc[2]]) : false;
                case 'nullcheck':
                    $db = $gm->getDB();
                    //�������Ɏw�肳�ꂽ�J�������ݒ肳��Ă��邩�ǂ���
                    $cols = explode( '/', $cc[2]);
                    foreach( $cols as $col ){
                        if( !strlen( $db->getData( $rec, $col) ) ){
                            return false;
                        }
                    }
                    return true;
                    break;
                case 'anycheck':
                    $db = $gm->getDB();
                    //�������Ɏw�肳�ꂽ�J�������ݒ肳��Ă��邩�ǂ���
                    $cols = explode( '/', $cc[2]);
                    foreach( $cols as $col ){
                        if( strlen( $db->getData( $rec, $col) ) ){
                            return true;
                        }
                    }
                    return false;
                    break;
                case 'zerocheck'://int�^�ł�nullcheck
                    $db = $gm->getDB();
                    //�������Ɏw�肳�ꂽ�J�������ݒ肳��Ă��邩�ǂ���
                    $cols = explode( '/', $cc[2]);
                    foreach( $cols as $col ){
                        if(  $db->getData( $rec, $col) == 0 ){
                            return false;
                        }
                    }
                    return true;
                    break;
                case 'idcheck':
                	$id = $cc[2];
                	$table = $cc[3];
                	
                	$db = GMList::getDB( $table );
                	$r = $db->selectRecord( $id );
                	
                    return $r != null;
                case 'eq':
                case 'equal':
                case '=':
                    //�������̃J�������Ƃ������R�[�h�̒l�ƁA��O�����Ɏw�肳�ꂽ�l����v���邩�ǂ����B
                    $db = $gm->getDB();
                    return ($db->getData( $rec , $cc[2] ) == $cc[3]);
                case '>':
                    $db = $gm->getDB();
                    return ($db->getData( $rec , $cc[2] ) > $cc[3]);
                    break;
				case '>=':
					$db = $gm->getDB();
					return ($db->getData( $rec , $cc[2] ) >= $cc[3]);
					break;
                case '<':
                    $db = $gm->getDB();
                    return ($db->getData( $rec , $cc[2] ) < $cc[3]);
                    break;
				case '<=':
					$db = $gm->getDB();
					return ($db->getData( $rec , $cc[2] ) <= $cc[3]);
					break;
                case 'val_equal':
                case 'val_eq':
                case 'val=':
                    //���A��O�����Ɏw�肳�ꂽ�l����v���邩�ǂ����B
                    $check = isset($cc[3])?$cc[3]:'';
                    return ($cc[2] == $check);
                case 'in':
                    //�������̃J�������Ƃ������R�[�h�̒l���A"/"�ŕ������ꂽ��O�����̕����Q�Ɋ܂܂�Ă��邩�ǂ����B
                    $db = $gm->getDB();
                    $val = $db->getData( $rec , $cc[2] );
                    $array = explode( '/', $cc[3] );
                    foreach( $array as $data ){
                    	if(($val == $data) ){return true;}
                    }
                    return false;
                case 'val_in':
                    //�������̒l���A"/"�ŕ������ꂽ��O�����̕����Q�Ɋ܂܂�Ă��邩�ǂ����B
                    $val = $cc[2];
                    $array = explode( '/', $cc[3] );
                    foreach( $array as $data ){
                    	if(($val == $data) ){return true;}
                    }
                    return false;
                case 'array_in':
                    //�������̃J�������Ƃ������R�[�h�̒l��"/"�ŕ������A"/"�ŕ������ꂽ��O�����̕����Q�Ɋ܂܂�Ă��邩�ǂ����B
                    $db = $gm->getDB();
                    $vals = explode('/', $db->getData( $rec , $cc[2] ));
                    $array = explode( '/', $cc[3] );
					foreach( $vals as $val )
					{
						foreach( $array as $data ){
							if(($val == $data) ){return true;}
						}
					}
                    return false;
                case 'val_array_in':
                    //�������̒l��"/"�ŕ������A"/"�ŕ������ꂽ��O�����̕����Q�Ɋ܂܂�Ă��邩�ǂ����B
                    $vals = explode('/', $cc[2]);
                    $array = explode( '/', $cc[3] );
                    foreach( $vals as $val )
					{
						foreach( $array as $data ){
							if(($val == $data) ){return true;}
						}
                    }
                    return false;
                case 'get_equal':
                case 'get=':
                    //��������GET�����̘A�z�z�񖼂Ƃ����l�ƁA��O�����Ɏw�肳�ꂽ�l����v���邩�ǂ����B
                    return isset($_GET[$cc[2]])?($_GET[$cc[2]]==$cc[3]):''==$cc[3];
                case 'post_equal':
                    //��������GET�����̘A�z�z�񖼂Ƃ����l�ƁA��O�����Ɏw�肳�ꂽ�l����v���邩�ǂ����B
                    return isset($_POST[$cc[2]])?($_POST[$cc[2]] == $cc[3]):''==$cc[3];
                case 'uri_match':
                	return (preg_match('/'.str_replace( array("!CODE001;","!CODE000;","!CODE002;"), array(" ", "/", "\\") , $cc[2] ).'$/',$_SERVER['REQUEST_URI']) > 0);
                	break;
                case 'uri_match_like':
                	return (preg_match('/'.str_replace( array("!CODE001;","!CODE000;","!CODE002;"), array(" ", "/", "\\") , $cc[2] ).'/',$_SERVER['REQUEST_URI']) > 0);
                	break;
                case 'val>':
                    return ($cc[2] > $cc[3]);
                    break;
                case 'val<':
                    return ($cc[2] < $cc[3]);
                    break;
                case 'val>=':
                    return ($cc[2] >= $cc[3]);
                    break;
                case 'val<=':
                    return ($cc[2] <= $cc[3]);
                    break;
                case 'mod_on':
					global $MODULES;
                	return class_exists('mod_'.$cc[2]) || array_key_exists( $cc[2] , $MODULES );
                case 'mod_off':
					global $MODULES;
                	return !class_exists('mod_'.$cc[2]) && !array_key_exists( $cc[2] , $MODULES );
                case 'match':
                	return preg_match( '/' . $cc[3] . '/u' , $cc[2] );
                case 'match_e':
                	return mb_ereg( $cc[3], $cc[2] ) !== FALSE;
                case 'login':
                	global $loginUserType;
                	return $loginUserType == $cc[2];
                case 'isvariable':
                	//GM��variable�ɂ��̒l�����݂��邩�ǂ���
                    return isset($gm->variable[$cc[2]]);
                case 'global':
                	global ${$cc[2]};
                	return ${$cc[2]};
				case 'is_all':	//rec,Post,get�̂ǂ����Ƀf�[�^�����邩�ǂ���
					$ret = (!empty($_GET[$cc[2]])) || (!empty($_POST[$cc[2]]));
					if( $ret ) { return $ret; }
					$db = $gm->getDB();
					$val = $db->getData( $rec , $cc[2] );
					return !empty($val);
					break;
                case 'system':
                	$db = GMList::getDB( 'system' );
                	
                	$data = SystemUtil::getSystemData( $cc[2] );
                	
                	switch( $db->colType[$cc[2]] )
                	{
                		case 'boolean':
                			//boolean�Ȃ炻�̂܂܎g��
                			return $data;
                		default:
                			//������̏ꍇ�͈����Ƃ̔�r
                			return $data == $cc[3];
                	}
                	return false;
                case 'true':
                	return true;
                case 'false':
                	return false;
                case 'password_mode':
                	return ( $cc[ 2 ] == $PASSWORD_MODE );
                case 'reminder_mode':
                	return ( $cc[ 2 ] == $REMINDER_MODE );
                case 'script_name':
                    global $controllerName;
					$script     = SystemInfo::GetScriptName();;
					$controller = strtolower( $controllerName );

					if( $cc[ 2 ] == $script || $cc[ 2 ] == $controller )
						{ return true; }

					if( $controller )
					{
						if( 'regist' == $cc[ 2 ] && 'register' == $controller )
							{ return true; }
						if( 'keygen' == $cc[ 2 ] && 'update' == $controller )
							{ return true; }
						if( 'thumb' == $cc[ 2 ] && 'thumbnail' == $controller )
							{ return true; }
					}

					return false;

                case 'inputtable':
					//lst��Const/AdminData��ݒ肵�Ă���J�����̊m�F
					global $loginUserType;
                    global $controllerName;

					$registValidates = explode( '/' , $gm->colRegist[ $cc[ 2 ] ] );
					$editValidates   = explode( '/' , $gm->colEdit[ $cc[ 2 ] ] );

					$isRegist      = 'register' == strtolower( $controllerName);
					$isEdit        = 'edit' == strtolower( $controllerName );
					$isRegistConst = in_array( 'Const' , $registValidates ) || ( in_array( 'AdminData' , $registValidates ) && 'admin' != $loginUserType );
					$isEditConst   = in_array( 'Const' , $editValidates ) || ( in_array( 'AdminData' , $editValidates ) && 'admin' != $loginUserType );

					return !( ( $isRegist && $isRegistConst ) || ( $isEdit && $isEditConst ) );
				case 'ua_match' :
					return preg_match( '/' . str_replace( '/' , '\\/' , $cc[ 2 ] ) . '/' , $_SERVER[ 'HTTP_USER_AGENT' ] );
                default:
                	global $ALL_DEBUG_FLAG;
                	if( $ALL_DEBUG_FLAG ){ d( '['.$cc[1].'] not found.' ,'ifbegin');}
            }
            return false;
		}
	}
?>