<?php

	/**
	 * ��{���߃N���X
	 *
	 * @author �O�H��q
	 * @version 1.0.0
	 *
	 */
	class Command extends CommandBase
	{

		/**********************************************************************************************************
		 * �V�X�e���p���\�b�h
		 **********************************************************************************************************/

		/**
		 * �x�[�X�^�O��`�悵�܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 */
		function base_tag( &$gm, $rec, $args ){
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			global $HOME;
			global $BASE_TAG;
			// **************************************************************************************

			if( $BASE_TAG && strlen($HOME) > 0 )
			{
				$url = $HOME;
				if( $_SERVER['HTTPS'] == 'on' ) { $url = str_replace ( 'http://', 'https://', $HOME ); }
				$buffer = '<base href="'.$url.'" />';
			}
			$this->addBuffer( $buffer );
		}

		/**
		 * ���O�C��ID��`�悵�܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 */
		function loginid( &$gm, $rec, $args ){
			global $LOGIN_ID;
			$this->addBuffer( $LOGIN_ID );
		}

		/**
		 * �^�C���X�^���v��ϊ����܂��B
		 * �w�肪�����ꍇ�̓V�X�e���f�t�H���g�̕����g�p����܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 * 		��������UNIX�^�C����n���܂��B
		 * 		��������date�ɓn��timeformat���w�肵�܂�(�C��)
		 */
		function timestamp( &$gm, $rec, $args ){
			if(isset($args[1])){ $this->addBuffer(SystemUtil::mb_date( str_replace( '!CODE001;', ' ' , $args[1] ), $args[0] )); }
			else{ $this->addBuffer(SystemUtil::mb_date( $gm->getTimeFormat(), $args[0] )); }
		}

		/**
		 * ���݂̎��Ԃ��擾���܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 */
		function now( &$gm, $rec, $args ){
			$kind	 = isset($args[0])?$args[0]:'';
			$add	 = isset($args[1])?$args[1]:0;

			switch( $kind ){
				case 'y': // 4���̔N
				case 'year':
					$this->addBuffer( date('Y') + $add );
					break;
				case 'm': // 2���̌�
				case 'month':
					$this->addBuffer( sprintf('%02d', date('m') + $add ));
					break;
				case 'd': // 2���̓�
				case 'day':
					$this->addBuffer( sprintf('%02d', date('d') + $add ));
					break;
				case 'h': // 2���̎�
				case 'hour':
					$this->addBuffer( sprintf('%02d', date('H') + $add ));
					break;
				case 'i': //2���̕�
				case 'minute':
					$this->addBuffer( sprintf('%02d', date('i') + $add ));
					break;
				case 's': //2���̕b
				case 'second':
					$this->addBuffer( sprintf('%02d', date('s') + $add ));
					break;
				case 'u': // unixtime
				case 'unix':
					$this->addBuffer(time()+$add);
					break;
				case 'n': // �� 1�`12
                case 'g': // �� 1�`12
                case 'G': // �� 0�`23
                case 'j': // �� 1�`31
					$this->addBuffer( date($kind) + $add );
					break;
				default:
					$this->addBuffer( SystemUtil::mb_date( $gm->getTimeFormat() ) );
			}
		}


        //�^�C���X�^���v�J�����l�̖��O���󂯂āA���̃^�C���X�^���v�l�̌o�ߔN����Ԃ�
        function getPassage( &$gm, $rec, $args ){

			$db		 = $gm->getDB();
            $passage = localtime( $db->getData( $rec, $args[0] ) );
            $now = localtime( );

            $y = $now[5] - $passage[5];
            $m = $now[4] - $passage[4];

            if($m < 0 ){$y--;}

			$this->addBuffer( $y );
        }

        // �N�@���@�����󂯎���āA�N���`��
        function drawAgeByBirth( &$gm, $rec , $args ){
			if( 1850<$args[0] )
			{
				if(!isset($args[1])){$args[1]=1;}
				if(!isset($args[2])){$args[2]=1;}
				$birth = sprintf("%4d%02d%02d",$args[0],$args[1],$args[2]);
				$now = date('Ymd');
				$this->addBuffer( (int)(($now - $birth)/10000) );
			}
        }
        // date���󂯎���āA�N���`��
        function drawAgeByDate( &$gm, $rec , $args ){

			$db		= $gm->getDB();
			if( isset($db->colType[ $args[0] ] ) ){
    		    $date	= $db->getData( $rec, $args[0] );
			}else{
				$date = $args[0];
			}

        	$date = str_replace( '-','',$date);


        	if( $date ){
				$this->addBuffer( (int)((date('Ymd') - $date)/10000) );
        	}
        }
        // �o�ߔN���������Ƃ��āA���݂���k����date��Ԃ�
        function getDateByElapsedYears(&$gm, $rec , $args ){
        	$date = sprintf("%4d-%02d-%02d",date("Y")-$args[0],date("n"),(empty($args[1]))?date("j"):date("j")+$args[1]);
			$this->addBuffer( $date );
        }

		/**
			�C�ӂ̓��t���w�肵�ă^�C���X�^���v���o�͂���B
		*/
		function dateToTime( &$gm , $rec , $args ) //
		{
			$year   = ( is_numeric( $args[ 0 ] ) ? $args[ 0 ] : date( 'Y' ) );
			$month  = ( is_numeric( $args[ 1 ] ) ? $args[ 1 ] : date( 'n' ) );
			$day    = ( is_numeric( $args[ 2 ] ) ? $args[ 2 ] : date( 'j' ) );
			$hour   = ( is_numeric( $args[ 3 ] ) ? $args[ 3 ] : date( 'G' ) );
			$minute = ( is_numeric( $args[ 4 ] ) ? $args[ 4 ] : date( 'i' ) );
			$second = ( is_numeric( $args[ 5 ] ) ? $args[ 5 ] : date( 's' ) );

			$this->addBuffer( mktime( $hour , $minute , $second , $month , $day , $year ) );
		}

		/**
		 * �A�N�e�B�x�[�g�R�[�h�𔭍s���܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B���̃��\�b�h�ł͗��p���܂���B
		 */
		function activate( &$gm, $rec, $args )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			global $HOME;
			// **************************************************************************************

			$db		 = $gm->getDB();
			$this->addBuffer(   $HOME. 'activate.php?type='. $_GET['type'] .'&id='. $db->getData( $rec, 'id' ) .'&md5='. md5(  $db->getData( $rec, 'id' ). $db->getData( $rec, 'mail' )  )   );
		}

		function drawImage( &$gm, $rec, $args ){
		 	if(  is_file( $args[0] )  ){
				// �t�@�C�������݂���ꍇ
				if(  isset( $args[1] ) && isset( $args[2] )  ){
					$this->addBuffer( '<img src="'. $args[0] .'" width="'. $args[1] .'" height="'. $args[2] .'" border="0"/>' );
				}else{
					$this->addBuffer( '<img src="'. $args[0] .'" border="0"/>' );
				}

			}else{
				// �t�@�C�������݂��Ȃ��ꍇ
				$this->addBuffer( '<span>�C���[�W�͓o�^����Ă��܂���</span>' );
			}
		 }

		/**
		 * �f�[�^�̌������擾�B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B�@�������Ƀe�[�u�����@�������ɃJ�������@��O�����ɉ��Z�q�@��l�����ɒl�@�����Ă��܂��B
		 */
		function getRow( &$gm, $rec, $args ){
			$tgm	 = SystemUtil::getGMforType($args[0]);
			$db		 = $tgm->getDB();
            $table = $db->getTable();
            for($i=0;isset($args[1+$i]);$i+=3){
            	if($args[2+$i] == 'b'){
	    			$table = $db->searchTable( $table, $args[1+$i], $args[2+$i], $args[3+$i], $args[4+$i] );
	    			$i++;
            	}else{
	    			$table = $db->searchTable( $table, $args[1+$i], $args[2+$i], $args[3+$i] );
            	}
            }
            $this->addBuffer( $db->getRow( $table ) );
		}

		/**
			@brief   ���݂�URL����ɃN�G���̒ǉ��E�폜���s���A���̌������ʂ̍s�����擾����B
			@details set�܂���unset�ɑ����ĔC�ӂ̃N�G�����L�q���邱�Ƃł��̃N�G�������݂̃N�G���ƃ}�[�W���܂��B
			         set��unset�͋L�q�������ɐ擪���珈������܂��B
			         �����@���F
			             set foo=bar   ... foo��bar���Z�b�g���܂��B�z��ł̎w����\�ł��B�����̃N�G�����X�J���ł���Ώ㏑���A�z��ł���΃}�[�W����܂��B
			             unset foo=bar ... foo�̒l��bar�ł������Ȃ�폜���A����ȊO�̏ꍇ�͎c���܂��B�z��ō폜����l�𕡐��w�肷�邱�Ƃ��ł��܂��B
			             unset foo     ... �����̒l�Ɋւ�炸�Afoo�����S�ɍ폜���܂��B
			         �L�q��F
			             <!--# code rebuildRow unset tag&category set tag[]=foo&tag[]=bar&category[]=fizz&category[]=buzz #--> ... tag��category��������������ŔC�ӂ̒l���Z�b�g
		*/
		function rebuildRow( &$gm , $rec , $args ) //
		{
			global $gm;
			global $loginUserType;
			global $loginUserRank;
			global $magic_quotes_gpc;

			$baseQuery = $_GET;

			while( count( $args ) ) //�S�Ă�CC����������
			{
				$argsType = array_shift( $args );

				if( 'set' == $argsType ) //�N�G����ǉ�����ꍇ
				{
					parse_str( array_shift( $args ) , $setQuery );

					foreach( $setQuery as $name => $value ) //�S�Ă̒ǉ��N�G��������
					{
						if( is_array( $baseQuery[ $name ] ) ) //���݂̃N�G�����z��̒l�����ꍇ
						{
							if( is_array( $value ) ) //�ǉ��N�G�����z��̒l�����ꍇ
								{ $baseQuery[ $name ] = array_merge( $baseQuery[ $name ] , $value ); }
							else //�ǉ��N�G�����X�J���̒l�����ꍇ
								{ $baseQuery[ $name ][] = $value; }

							$baseQuery[ $name ] = array_unique( $baseQuery[ $name ] );
						}
						else //��N�G�����X�J���̒l�����ꍇ
							{ $baseQuery[ $name ] = $value; }
					}

					continue;
				}
				else if( 'unset' == $argsType ) //�N�G�����폜����ꍇ
				{
					parse_str( array_shift( $args ) , $unsetQuery );

					foreach( $unsetQuery as $name => $value ) //�S�Ă̍폜�N�G��������
					{
						if( !$value ) //�폜����l�̎w�肪�Ȃ��ꍇ
							{ unset( $baseQuery[ $name ] ); }
						else //�폜����l�̎w�肪����ꍇ
						{
							if( !is_array( $value ) ) //�폜����l���z��w��ł͂Ȃ��ꍇ
								{ $value = array( $value ); }

							foreach( $value as $elem ) //�S�Ă̗v�f������
							{
								if( is_array( $baseQuery[ $name ] ) ) //��N�G�����z��̒l�����ꍇ
								{
									$index = array_search( $elem , $baseQuery[ $name ] );

									if( FALSE === $index ) //��N�G���̒l���폜����l�ƈ�v����ꍇ
										{ continue; }

									unset( $baseQuery[ $name ][ $index ] );
								}
								else if( $elem == $baseQuery[ $name ] ) //��N�G���̒l���폜����l�ƈ�v����ꍇ
									{ unset( $baseQuery[ $name ] ); }
							}
						}
					}

					continue;
				}
			}

			$getSwap = $_GET;
			$_GET    = $baseQuery;

			$search = new Search( $gm[ $_GET[ 'type' ] ] , $_GET[ 'type' ] );
			$db     = $gm[ $_GET[ 'type' ] ]->getDB();
			$system = SystemUtil::getSystem( $_GET[ 'type' ] );

			if( $magic_quotes_gpc || $db->char_code != 'sjis' ) //�G�X�P�[�v���s�v�ȏꍇ
				{ $search->setParamertorSet( $_GET ); }
			else //�G�X�P�[�v���K�v�ȏꍇ
				{ $search->setParamertorSet( addslashes_deep( $_GET ) ); }

			$system->searchResultProc( $gm , $search , $loginUserType , $loginUserRank );

			$table = $search->getResult();

			$system->searchProc( $gm , $table , $loginUserType , $loginUserRank );

			$_GET = $getSwap;

			$this->addBuffer( $db->getRow( $table ) );
		}

		/**
		 * �폜�ς݃f�[�^�̌������擾�B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B�@�������Ƀe�[�u�����@�������ɃJ�������@��O�����ɉ��Z�q�@��l�����ɒl�@�����Ă��܂��B
		 */
		function getDeleteRow( &$gm, $rec, $args ){
			$tgm	 = SystemUtil::getGMforType($args[0]);
			$db		 = $tgm->getDB();
            $table = $db->getTable('delete');
            for($i=0;isset($args[1+$i]);$i+=3){
            	if($args[2+$i] == 'b'){
	    			$table = $db->searchTable( $table, $args[1+$i], $args[2+$i], $args[3+$i], $args[4+$i] );
	    			$i++;
            	}else{
	    			$table = $db->searchTable( $table, $args[1+$i], $args[2+$i], $args[3+$i] );
            	}
            }
            $this->addBuffer( $db->getRow( $table ) );
		}

		/**
		 * �f�[�^�̍��v���擾�B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B�@�������Ƀe�[�u�����@�������ɏW�v�J�������@��O�`�܈����Ɍ����J�������A���Z�q�A�l�@�����Ă��܂��B
		 */
		function getSum( &$gm, $rec, $args ){
			$tgm	 = SystemUtil::getGMforType($args[0]);
			$db		 = $tgm->getDB();
            $table = $db->getTable();
            for($i=0;isset($args[2+$i]);$i+=3){
            	if($args[3+$i] == 'b'){
	    			$table = $db->searchTable( $table, $args[2+$i], $args[3+$i], $args[4+$i], $args[5+$i] );
	    			$i++;
            	}else{
	    			$table = $db->searchTable( $table, $args[2+$i], $args[3+$i], $args[4+$i] );
            	}
            }

            $this->addBuffer( $db->getSum( $args[1], $table ) );
		}

		/**
		 * �C�ӂ̃e�[�u���̔C�ӂ�id�̔C�ӂ̃J�����̃f�[�^�����o��
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B�@�������Ƀe�[�u�����@�������ɏW�v�J�������@��O�`�܈����Ɍ����J�������A���Z�q�A�l�@�����Ă��܂��B
		 */
		function getData( &$gm, $rec, $args ){
			$data = SystemUtil::getTableData( $args[0], $args[1], $args[2] );
			if( is_null($data)){
				$this->addBuffer("");
				return;
			}
			$this->addBuffer($data);
		}

		/**
		 * �C�ӂ̃e�[�u���̌������ʂ���C�ӂ̃J�����̃f�[�^��A�����Ď��o��
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B�@�������Ƀe�[�u�����@�������ɏW�v�J�������@��O�����ɋ�؂蕶���@��l�����ڍs�Ɍ����J�������A���Z�q�A�l�@���w�肵�܂��B
		 */
		function findData( &$gm , $rec , $args )
		{
			$targetType   = array_shift( $args );
			$targetColumn = array_shift( $args );
			$separator    = array_shift( $args );

			$targetDB    = GMList::getDB( $targetType );
			$targetTable = $targetDB->getTable();

			while( count( $args ) ) //�S�Ă̈���������
			{
				$column = array_shift( $args );
				$op     = array_shift( $args );
				$value  = array_shift( $args );

				if( 'b' == $op ) //�͈͌����̏ꍇ
				{
					$subValue    = array_shift( $args );
					$targetTable = $targetDB->searchTable( $targetTable , $column , $op , $value , $subValue );
				}
				else //�ʏ�̌����̏ꍇ
					{ $targetTable = $targetDB->searchTable( $targetTable , $column , $op , $value ); }
			}

			$row = $targetDB->getRow( $targetTable );

			for( $i = 0 ; $row > $i ; ++$i ) //�S�Ă̍s������
			{
				$rec       = $targetDB->getRecord( $targetTable , $i );
				$results[] = $targetDB->getData( $rec , $targetColumn );
			}

			$this->addBuffer( implode( $separator , $results ) );
		}

		/**
		 * �R�[�h���L�q���ꂽ�y�[�W�����O�C����Ƀ��_�C���N�g����y�[�W�Ƃ��ċL�^
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B�@�������Ƀe�[�u�����@�������ɏW�v�J�������@��O�`�܈����Ɍ����J�������A���Z�q�A�l�@�����Ă��܂��B
		 */
		function saveRedirectPage( &$gm, $rec, $args ){
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			// **************************************************************************************

			$notAdmin = $args[0];
            $script = SystemInfo::GetScriptName();

			$nameList = array('previous_page');
			if( !strlen($notAdmin) ) { $nameList[] = 'previous_page_admin'; }

			foreach($nameList as $name)
			{
				$_SESSION[$name] = $script;
				if( strlen( $_SERVER[ 'QUERY_STRING' ] ) ) { $_SESSION[$name] .= '?' . $_SERVER[ 'QUERY_STRING' ]; }
			}
		}

		/**********************************************************************************************************
		 * �g���V�X�e���p���\�b�h
		 **********************************************************************************************************/

		/**
		 * ���[�U���擾�B
		 * ID���烆�[�U�����������A�Y������ ���[�U��( ���[�UID ) �̌`���ŏo�͂��܂��B
		 * �ǂ̃��[�U���e�[�u���Ƀ��[�U�f�[�^������̂��킩��Ȃ��Ƃ��ȂǂɗL���ł��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B�@��������ID��n���܂��B �������Ƀ����N���邩��^�U�l�œn���܂��B
		 */
		function getName( &$gm, $rec, $args )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			global $TABLE_NAME;
			global $THIS_TABLE_IS_USERDATA;
			// **************************************************************************************

			$link_f = $args[1];
			$null_msg = $args[2];

			for( $i=0; $i<count($TABLE_NAME); $i++ )
			{
				if(  $THIS_TABLE_IS_USERDATA[ $TABLE_NAME[$i] ]  )
				{
					$tgm	 = SystemUtil::getGMforType( $TABLE_NAME[$i] );
					$db		 = $tgm->getDB();
					$rec	 = $db->selectRecord( $args[0] );
					if( $rec )
					{
						if( strtolower( $link_f ) == 'true' )
						{
							$this->addBuffer(
								'<a href="info.php?type='. $TABLE_NAME[$i] .'&id='. $db->getData( $rec, 'id' ) .'">'.
								$db->getData( $rec, 'name' ). '( '. $db->getData( $rec, 'id' ). ' )'.
								'</a>'  );
						}else
						{
							$this->addBuffer(  $db->getData( $rec, 'name' ). '( '. $db->getData( $rec, 'id' ). ' )'  );
						}
						return;
					}
				}
			}
			$this->addBuffer( $null_msg );
		}



		/**
		 * �f�[�^�����擾�B
		 * ID����f�[�^���������A�Y������ �f�[�^��( �f�[�^ID ) �̌`���ŏo�͂��܂��B
		 * �ǂ̃e�[�u���Ƀf�[�^������̂��킩��Ȃ��Ƃ��ȂǂɗL���ł��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B�@��������ID��n���܂��B�@�������ɖ��O�̊i�[����Ă���J��������n���܂��B ��O�����Ƀ����N���邩��^�U�l�œn���܂��B
		 */
		function getDataName( &$gm, $rec, $args )
		{
			// ** conf.php �Œ�`�����萔�̒��ŁA���p�������萔���R�R�ɗ񋓂���B *******************
			global $TABLE_NAME;
            global $ID_LENGTH;
			// **************************************************************************************

			// �S�Ẵe�[�u����GUIManager�C���X�^���X���擾���܂��B
			$tgm	 = SystemUtil::getGM();
			$flg	 = false;
			for( $i=0; $i<count($tgm); $i++ ){

                if( $ID_LENGTH[ $TABLE_NAME[$i] ] == 0)
                    continue;

				$db		 = $tgm[ $TABLE_NAME[$i] ]->getDB();
				$table	 = $db->searchTable( $db->getTable(), 'id', '=', $args[0] );
				if( $rec = $db->getFirstRecord( $table ) )
				{
					if( $args[2] == 'true' || $args[2] == 'TRUE' )
					{
						$this->addBuffer(
							'<a href="info.php?type='. $TABLE_NAME[$i] .'&id='. $db->getData( $rec, 'id' ) .'">'.
							$db->getData( $rec, $args[1] ). '( '. $db->getData( $rec, 'id' ). ' )'.
							'</a>'  );
					}
					else
					{
						$this->addBuffer(  $db->getData( $rec, $args[1] ). '( '. $db->getData( $rec, 'id' ). ' )'  );
					}
					$flg	 = true;
					break;
				}
			}

			if( !$flg )	{ $this->addBuffer( '�Y���f�[�^����' ); }
		}




		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/



		/**********************************************************************************************************
		 * �T�C�g�V�X�e���p���\�b�h
		 **********************************************************************************************************/




		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/
		/*******************************************************************************************************/



		/**********************************************************************************************************
		 *�@�g���ėp���\�b�h
		 **********************************************************************************************************/


		/**
		 * �����œn���������܂ł�I���ł���select�R���g���[����\���B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 *
		 * ��������name���w��
		 * �������ōŌ�̐������w��l(�ȗ���)
		 * ��O�����ŏ����l(�I�𒆂̍��ڂ̐������w��l)(�ȗ���)
		 * ��l�����ŊJ�n�l(�ȗ���)
         * ��܈����Őړ����ڂ̒ǉ��l(��F���I��) (�ȗ���)
         * ��Z�����Ń^�O�I�v�V������ݒ�i�ȗ��\�j
		 */
        function num_option( &$gm , $rec , $args ){

            $name = $args[0];

            $max = 1;
            if(strlen($args[1])){ $max = $args[1]; }

            $check = 0;
            if( isset( $_POST[$args[0]] ) && strlen( $_POST[$args[0]] ) ){ $check = $_POST[$args[0]]; }
            else if( isset($args[2]) && strlen($args[2])){ $check = $args[2]; }

            $start = 1;
            if( isset( $args[3] ) && strlen($args[3])){ $start = $args[3]; }

            $option = "";
            if( isset( $args[5] ) && strlen($args[5]) ){ $option = $args[5]; }


            if( strlen($name) ){
                $index = "";
                $value  = "";
                if(  isset( $args[4] ) && strlen($args[4]) ){
                    $index .= $args[4].'/';
                    $value  .= '/';
                }
                for($i=$start;$i<$max;$i++){
                    $index .= $i.'/';
                    $value  .= $i.'/';
                }
                $index .= $i;
                $value  .= $i;

                $this->addBuffer( $gm->getCCResult( $rec, '<!--# form option '.$name.' '.$check.' '.$value.' '.$index.' '.$option.' #-->' ) );
            }

        }

        /**
         * �����Ŏw�肵�������Ɠ�����*���o�͂���B
         *
         */
        function drawPassChar( &$gm , $rec , $args ){
            $PASS_CHAR = '*';
            $str = "";
            for($i=0;strlen($args[0]) > $i ;$i++){
                $str .= $PASS_CHAR;
            }
            $this->addBuffer( $str );
        }

		//���݂̃y�[�W��URL��\������
		function currentPage( &$gm , $rec , $args )
		{
			$uri = $_SERVER[ 'REQUEST_URI' ];
			$uri = h( $uri , ENT_QUOTES | ENT_HTML401);

			$this->addBuffer( $uri );
		}

		//���݂̃y�[�W��URL��\������
		function currentURL( &$gm , $rec , $args )
		{
			$uri = ( 'on' == $_SERVER[ 'HTTPS' ] ? 'https://' : 'http://' ) . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];
			$uri = h( $uri , ENT_QUOTES | ENT_HTML401 );

			$this->addBuffer( $uri );
		}

        /*
          ���ڂ́������X�g��\��
          �܂�́A�C�ӂ̃e�[�u���̔C�ӂ̃t���O��true�̍��ڂ��ꗗ�Ƃ��ĕ\������B

        args
         0:�e�[�u����
         1:�t���O�J������
         2:�\����
        */
        function attentionListDraw( &$gm, $rec , $args ){
        global $loginUserType;
        global $loginUserRank;
            $HTML = Template::getTemplate( $loginUserType , $loginUserRank , $args[0] , 'ATTENTION_TEMPLATE' );

            if( !strlen( $HTML ) ){
                throw new InternalErrorException('dos not template');
            }

            $tgm = SystemUtil::getGMforType( $args[0] );
            $db = $tgm->getDB();
            $list = $db->searchTable( $db->getTable() , $args[1] , '=' , true );

            $row = $db->getRow( $list );

            $this->addBuffer( $tgm->getString( $HTML , null , 'head' ) );
            for( $i = 0 ; $i < $args[2] ; $i++ ){
                if( $i < $row ){
                    $lrec = $db->getRecord( $list , $i );
                    $this->addBuffer( $tgm->getString( $HTML , $lrec , 'element' ) );
                }else{
                    $this->addBuffer( $tgm->getString( $HTML , null , 'dummy' ) );
                }
            }
            $this->addBuffer( $tgm->getString( $HTML , null , 'foot' ) );
        }

        /*
          �V���́������X�g��\��
          �܂�́A�C�ӂ̃e�[�u����regist���w�肵�����Ԉȓ��̍��ڂ��ꗗ�\���B

        args
         0:�e�[�u����
         1:�V���Ƃ������(���Ԃ�)
         2:�\����
        */
        function newListDraw( &$gm, $rec , $args ){
        global $loginUserType;
        global $loginUserRank;
            $HTML = Template::getTemplate( $loginUserType , $loginUserRank , $args[0] , 'NEW_TEMPLATE' );

            if( !strlen( $HTML ) ){
                throw new InternalErrorException('dos not template');
            }

            $tgm = SystemUtil::getGMforType( $args[0] );
            $db = $tgm->getDB();
            $list = $db->searchTable( $db->getTable() , 'regist' , '>' , time() - ($args[1]*60*60) );
            $row = $db->getRow( $list );

            $this->addBuffer( $tgm->getString( $HTML , null , 'head' ) );
            for( $i = 0 ; $i < $args[2] ; $i++ ){
                if( $i < $row ){
                    $lrec = $db->getRecord( $list , $i );
                    $this->addBuffer( $tgm->getString( $HTML , $lrec , 'element' ) );
                }else{
                    $this->addBuffer( $tgm->getString( $HTML , null , 'dummy' ) );
                }
            }
            $this->addBuffer( $tgm->getString( $HTML , null , 'foot' ) );
        }

        /*
         * ���R�[�h�ɒl�����݂���ꍇ�����N��\������
         *
         * 0:���R�[�h��
         * 1:URL
         * 2:�����N�̕\������
         * 3:�����N�������ꍇ�̕\������
         */
         function drawLinkByRec( &$gm, $rec, $args ){
             $db = $gm->getDB();
             $data = $db->getData( $rec , $args[0]);
             if( ! strlen($data) ){
                 $this->addBuffer( $args[3] );
             }else{
                 //Link����̎���rec�̃f�[�^
                 if( !strlen($args[1]) )
                     $url = $data;
                 else
                     $url = $args[1];

                 $this->addBuffer( '<a href="'.$url.'">'.$args[2].'</a>' );
             }
         }

        /*
         * ���������݂���ꍇ�����N��\������
         *
         * 0:URL
         * 1:�����N�̓��ɕt���镶���imailto:�Ƃ�
         */
         function drawLink( &$gm, $rec, $args ){
             if( strlen($args[0]) )
                 $this->addBuffer( '<a href="'.$args[1].$args[0].'" target="_blank">'.$args[0].'</a>' );
         }


        function getReferer(&$gm , $rec , $args ){
            $this->addBuffer( $_SERVER['HTTP_REFERER'] );
        }

        /*
         * ����ID�w��ɑΉ����������N�o��
         * ���R�[�h�ɒl�����݂���ꍇ�����N��\������
         *
         * 0:���R�[�h��
         * 1:URL(������ID��t�^����`)
         * 2:�����N�̕\������
         * 3:�����N�������ꍇ�̕\������
         */
         function drawLinkMultiID( &$gm, $rec, $args ){
             $sep = '/';

             $db = $gm->getDB();
             $data = $db->getData( $rec , $args[0]);
             if( ! strlen($data) ){
                 $this->addBuffer( $args[3] );
             }else{
                 $array = explode( $sep , $data );

                 $row = count( $array );
                 for($i=0; $i < $row-1 ; $i++){
                     $this->addBuffer( '<a href="'.$args[1].$array[$i].'">'.$args[2].'</a><br/>' );
                 }

                 $this->addBuffer( '<a href="'.$args[1].$array[$i].'">'.$args[2].'</a>' );
             }
         }


         //1:�S�p���� 2:���p�J�i 3:�p�� 4:�����B
         function getInputMode( &$gm , $rec , $args ){
         global $terminal_type; // 1:docomo 2:au 3:softbank
             $e = Array(
                     1 => Array( '1' => 'istyle="1" style="-wap-input-format:&quot;*&lt;ja:h&gt;&quot;"' ,
                                  '2' => 'istyle="2" style="-wap-input-format:&quot;*&lt;ja:hk&gt;&quot;"' ,
                                  '3' => 'istyle="3" style="-wap-input-format:&quot;*&lt;ja:en&gt;&quot;"' ,
                                  '4' => 'istyle="4" mode="numeric" style="-wap-input-format:&quot;*&lt;ja:n&gt;&quot;"' ) ,
                     2 => Array( '1' => 'format="*M"' , '2' => 'istyle="2"' , '3' => 'format="*x"' , '4' => 'format="*N"' ) ,
                     3 => Array( '1' => 'MODE="hiragana"' , '2' => 'MODE="hankakukana"' , '3' => 'MODE="alphabet"' , '4' => 'MODE="numeric"' ) );
             $this->addBuffer( $e[$terminal_type][$args[0]] );
         }

         //args[0]:�u0�v�`�u9�v�A�u*�v�A�u#�v
         //args[1]: true 'NONUMBER' ,false ''
         function getAccesskey( &$gm , $rec , $args ){
         global $terminal_type;
//             $nonumber = '';
             // 1:docomo 2:au 3:softbank
             $elements = Array( 0 => 'accesskey' , 1 => 'accesskey', 2 => 'accesskey', 3 => 'DIRECTKEY' );

             $element = $elements[$terminal_type];

/*             if( $terminal_type == 3 ){
                 $nonumber = 'NONUMBER';
             }*/
//             $this->addBuffer( $element.'="'.$args[0].'"'.$nonumber );
             $this->addBuffer( $element.'="'.$args[0].'"' );
         }


         /*
          *  ����������l�ɕ������؂�o�����郁�\�b�h
          *�@�i�����ɕ�������������`�ɂ���ƁA�������ɔ��p�X�y�[�X�ł̃Z�p���[�g�ɋ������ɂȂ�\���������̂ŗv�l��
          *
          * 0:�؂�o���Ώۂ̕�����
          * 1:�؂�o��������̒���(�ȗ��\�A�V�X�e���̃f�t�H���g�̕�����
          */
         function Continuation( &$gm , $rec , $args ){
		 	global $SYSTEM_CHARACODE;
             if( !isset($args[1]) || $args[1] <= 0 ){
                $num = 32;
             }else{
             	$num = $args[1];
             }

             if( !isset($args[2]) || !strlen($args[2]) ){
                $sufix = "�c";
             }else{
             	$sufix = $args[2];
             }


             $str = $args[0];

			$sufLength = mb_strlen( $sufix , $SYSTEM_CHARACODE );

			if( mb_strlen( str_replace( array('!CODE001;','!CODE101;'), ' ' , $str ) , $SYSTEM_CHARACODE ) > $num ){
				$this->addBuffer( str_replace( ' ' , '!CODE101;', mb_substr( str_replace( array('!CODE001;','!CODE101;'), ' ' , $str ), 0 , $num, $SYSTEM_CHARACODE ) . $sufix ) );
			}else{
				$this->addBuffer( $args[ 0 ] );
			}
         }

         /*
          * ��{�V�X�e���̊e��R�[�h�̈����Ɏg�����߂ɁA��������̔��p�X�y�[�X��Escape���ĕԂ��B
          *
          * 0:�G�X�P�[�v���s��������
          */
         function spaceEscape( &$gm , $rec , $args ){
             $this->addBuffer( join( '\ ' , $args) );
         }

         function urlencode( &$gm , $rec , $args ){
             $this->addBuffer( urlencode( $args[0] ) );
         }


         /*
          * �e�L�X�g����URL�������N�ɕϊ����ĕ\������B
          *
          * 0:�ϊ����s��������
          * 1:A�^�O�ɕt�����鑮��
          */
		function linkText( &$gm , $rec , $args )
		{
			$re = "\b(?:https?|shttp):\/\/(?:(?:[-_.!~*'()a-zA-Z0-9;:&=+$,]|%[0-9A-Fa-f" .
			      "][0-9A-Fa-f])*@)?(?:(?:[a-zA-Z0-9](?:[-a-zA-Z0-9]*[a-zA-Z0-9])?\.)" .
			      "*[a-zA-Z](?:[-a-zA-Z0-9]*[a-zA-Z0-9])?\.?|[0-9]+\.[0-9]+\.[0-9]+\." .
			      "[0-9]+)(?::[0-9]*)?(?:\/(?:[-_.!~*'()a-zA-Z0-9:@&=+$,]|%[0-9A-Fa-f]" .
			      "[0-9A-Fa-f])*(?:;(?:[-_.!~*'()a-zA-Z0-9:@&=+$,]|%[0-9A-Fa-f][0-9A-" .
			      "Fa-f])*)*(?:\/(?:[-_.!~*'()a-zA-Z0-9:@&=+$,]|%[0-9A-Fa-f][0-9A-Fa-f" .
			      "])*(?:;(?:[-_.!~*'()a-zA-Z0-9:@&=+$,]|%[0-9A-Fa-f][0-9A-Fa-f])*)*)" .
			      "*)?(?:\?(?:[-_.!~*'()a-zA-Z0-9;\/?:@&=+$,]|%[0-9A-Fa-f][0-9A-Fa-f])" .
			      "*)?(?:#(?:[-_.!~*'()a-zA-Z0-9;\/?:@&=+$,]|%[0-9A-Fa-f][0-9A-Fa-f])*)?";

			$str = preg_replace( '/(' . $re . ')/' , '<a href="$1" ' . $args[ 1 ] . '>$1</a>' , $args[ 0 ] );

			$this->addBuffer( $str );
		}

		/**
		 * ��������B �������Ƒ���������v���邩�ǂ����B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B�������Ƒ������̓��e����v�����ꍇ�́@��O�������A��v���Ȃ������ꍇ�͑�l������\�����܂��B
		 */
		function ifelse( &$gm, $rec, $args ){
			if( $args[0] == $args[1] ){
				$this->addBuffer( $args[2] );
			}else if( isset($args[3]) ){
				$this->addBuffer( $args[3] );
			}
		}

		/**
		 * ��������B �l���Z�b�g����Ă��邩�ǂ���
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B�������Ƒ������̓��e����v�����ꍇ�́@��O�������A��v���Ȃ������ꍇ�͑�l������\�����܂��B
		 */
		function is_set( &$gm, $rec, $args ){
			if( $args[0] != "" ){
				$this->addBuffer( $args[1] );
			}else if(isset($args[2])){
				$this->addBuffer( $args[2] );
			}
		}

		/**
		 * ��������B ���K�\���}�b�`
		 *
		 * @param args 0 �l
		 * @param args 1 ���K�\��
		 * @param args 2 true draw
		 * @param args 3 false draw
		 */
		function ifmatch( &$gm, $rec, $args ){

			if( mb_ereg( $args[1], $args[0] ) !== FALSE ){
				$this->addBuffer( $args[2] );
			}else{
				$this->addBuffer( $args[3] );
			}
		}

		/**
		 * �������ɒl�����݂���ꍇ�͒l���A���݂��Ȃ��ꍇ�͑��������o��
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 */
		function substitute( &$gm, $rec, $args ){
			if( $args[0] != "" ){
				$this->addBuffer( $args[0] );
			}else if(isset($args[1])){
				$this->addBuffer( $args[1] );
			}
		}


		/**
		 * �\�[�g�̂��߂�URL��`�悵�܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B���̃��\�b�h�ł͗��p���܂���B
		 */
		function sortLink( &$gm, $rec, $args ){
			$sort = '';
			if( isset( $_GET['sort'] ) ){
				$sort	 = $_GET['sort'];
			}
			if( $args[0] != '' ) { $sort	 =  $args[0]; }

			$url	 = basename($_SERVER['SCRIPT_NAME']).'?'.SystemUtil::getUrlParm($_GET);
			$url	 = preg_replace("/&sort=\w+/", "",$url);
			$url	 = preg_replace("/&sort_PAL=\w+/", "",$url);
			$url	.= '&sort='.$sort.'&sort_PAL=';
            if( isset($args[1]) && strlen($args[1]) ){
                 $url	 .= $args[1];
            }else if( isset($_GET['sort']) && $sort == $_GET['sort'] )
			{// �\�[�g���������݂Ɠ���̏ꍇ
				if( $_GET['sort_PAL'] == 'asc' ){ $url	 .= 'desc'; }
				else							{ $url	 .= 'asc'; }
			}else{ $url	 .= 'desc'; }

			$this->addBuffer( $url );
		}


		/**
		 * GET�p�����[�^��������Č����܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B���̃��\�b�h�ł͗��p���܂���B
		 */
		function getParam( &$gm, $rec, $args ){

				$param = $_GET;
			//���O����p�����[�^
			if( isset($args[0]) ){
				unset($param[$args[0]]);
			}

			$this->addBuffer( SystemUtil::getUrlParm($param) );
		}

        //�����I�Ɏw�荀�ڂ��o�͂���
        //1:cycle_id   1�y�[�W���ŕ����̎������d�l����ۂɁA���ꂼ�����ʂ��邽��
        //2:�����Ԋu 2�`
        //3�`:�p�^�[���̒��g�B  �����Ԋu�̐���������
        function drawPatternCycle( &$gm, $rec, $args ){
            global $CYCLE_PATTERN_STRUCT;
            $id = $args[0];
            if(!isset($CYCLE_PATTERN_STRUCT[$id]) ){
                $CYCLE_PATTERN_STRUCT[$id]['cnt'] = 0;
                $CYCLE_PATTERN_STRUCT[$id]['interval'] = $args[1];
                $CYCLE_PATTERN_STRUCT[$id]['pattern'] = array_slice( $args , 2 );
            }

            $this->addBuffer( $CYCLE_PATTERN_STRUCT[$id]['pattern'][ $CYCLE_PATTERN_STRUCT[$id]['cnt'] ] );

            $CYCLE_PATTERN_STRUCT[$id]['cnt']++;
            if( $CYCLE_PATTERN_STRUCT[$id]['cnt'] >= $CYCLE_PATTERN_STRUCT[$id]['interval'] )
                $CYCLE_PATTERN_STRUCT[$id]['cnt'] = 0;
        }
        //drawPatternCycle�̌��݂̃f�[�^���C���N�������g���s�Ȃ킸�\������
        function drawPatternNow( &$gm, $rec, $args ){
            global $CYCLE_PATTERN_STRUCT;
            $id = $args[0];
            if(!isset($CYCLE_PATTERN_STRUCT[$id]) ){
                //drawPatternCycle����ɌĂ΂�Ă��Ȃ��ꍇ�̓X���[
                return;
            }

            $this->addBuffer( $CYCLE_PATTERN_STRUCT[$id]['pattern'][ $CYCLE_PATTERN_STRUCT[$id]['cnt'] ] );
        }
        //drawPatternCycle�̌��݂̃f�[�^���C���N�������g���s�Ȃ킸�Ή�����f�U�C����\������
        function drawPatternSet( &$gm, $rec, $args ){
            global $CYCLE_PATTERN_STRUCT;
            $id = $args[0];
            if(!isset($CYCLE_PATTERN_STRUCT[$id]) ){
                //drawPatternCycle����ɌĂ΂�Ă��Ȃ��ꍇ�̓X���[
                return;
            }

            $this->addBuffer( $args[ $CYCLE_PATTERN_STRUCT[$id]['cnt']+1 ] );
        }

		/**
		 * �����ɃR���}�����ďo�͂��܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B���̃��\�b�h�ł͗��p���܂���B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B���̃��\�b�h�ł͗��p���܂���B
		 * @param args �R�}���h�R�����g�����z��ł��B ���̃��\�b�h�ł͗��p���܂���B
		 */
		function comma( &$gm, $rec, $args ){
            $this->addBuffer(number_format(floor($args[0])). strstr($args[0], '.'));
		}

		/*
		 * ���W���[�������݂��邩�ǂ������m�F���܂�
		 *
		 * addBuffer:TRUE/FALSE
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B���̃��\�b�h�ł͗��p���܂���B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B���̃��\�b�h�ł͗��p���܂���B
		 * @param args �R�}���h�R�����g�����z��ł��B �������Ƀ��W���[�������w�肵�܂��B
		 */
		function mod_on( &$gm, $rec, $args ){
			if( class_exists( 'mod_'.$args[0] ) ){
				$this->addBuffer( 'TRUE' );
			}else{
				$this->addBuffer( 'FALSE' );
			}
		}


		/**
		 * �w�肳�ꂽ�J�����̃f�[�^�ƈ����̘_�����Z�̑I���ɂȂ�悤��value�p�����[�^���o��
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B���̃��\�b�h�ł͗��p���܂���B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B���̃��\�b�h�ł͗��p���܂���B
		 * @param args �R�}���h�R�����g�����z��ł��B �������Ƀ��W���[�������w�肵�܂��B
		 */
		function chengeLogicalOperation( &$gm, $rec, $args ){
			$db = $gm->getDB();
			$data = $db->getData( $rec, $args[0] );

			if(is_null($data)){
				$data = $args[2];
			}

			$data = (int)$data;
			$key_num = (int)$args[1];

			$ret = ( ($data & $key_num) ? $data-$key_num : $data )."/".( $data | $key_num );
			$this->addBuffer( $ret );
		}


		/**
		 * authenticity_token�𖄂ߍ���
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B���̃��\�b�h�ł͗��p���܂���B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B���̃��\�b�h�ł͗��p���܂���B
		 * @param args �R�}���h�R�����g�����z��ł��B �������Ƀ��W���[�������w�肵�܂��B
		 */
		function drawAuthenticityToken( &$gm, $rec, $args ){
			$this->addBuffer( '<input name="authenticity_token" type="hidden" value="'. h( SystemUtil::getAuthenticityToken() ) .'" />' );
		}


		/*
		 * �n���ꂽ�����̐��l���r���A��ԏ��������̂�Ԃ�
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B���̃��\�b�h�ł͗��p���܂���B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B���̃��\�b�h�ł͗��p���܂���B
		 * @param args �R�}���h�R�����g�����z��ł��B �������Ƀ��W���[�������w�肵�܂��B
		 */
		function selectLower( &$gm, $rec, $args ){
			if( count($args) <= 0 ){ return 0; }
			else if ( count($args) <= 1 ){ return $args[0]; }

			$min = $args[0];

			foreach( $args as $v ){
				if( $min > $v ){
					$min = $v;
				}
			}
			$this->addBuffer($min);
		}

		/*
		 * �n���ꂽ�����̐��l���r���A��ԑ傫�����̂�Ԃ�
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B���̃��\�b�h�ł͗��p���܂���B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B���̃��\�b�h�ł͗��p���܂���B
		 * @param args �R�}���h�R�����g�����z��ł��B �������Ƀ��W���[�������w�肵�܂��B
		 */
		function selectUpper( &$gm, $rec, $args ){
			if( count($args) <= 0 ){ return 0; }
			else if ( count($args) <= 1 ){ return $args[0]; }

			$max = $args[0];

			foreach( $args as $v ){
				if( $max < $v ){
					$max = $v;
				}
			}

			$this->addBuffer($max);
		}

		/**
			@brief �����y�[�W�𖄂ߍ���ŕ\������B
			@param $iGM   GUIManager�I�u�W�F�N�g�ł��B���̃��\�b�h�ł͗��p���܂���B
			@param $iRec  �o�^���̃��R�[�h�f�[�^�ł��B���̃��\�b�h�ł͗��p���܂���B
			@param $iArgs �R�}���h�R�����g�����z��ł��B�����y�[�W�ɓn���N�G���p�����[�^���w�肵�܂��B
		*/
		function embedSearch( &$iGM , $iRec , $iArgs ) //
		{
			global $gm;
			global $loginUserType;
			global $loginUserRank;

			$this->getEmbedParameter( $iArgs , $query , $search , $db , $system , $table );

			$exists = $db->existsRow( $table );

			$getSwap   = $_GET;
			$_GET      = $query;
			$queryHash = sha1( serialize( $_GET ) );

			if( !$_SESSION[ 'search_query_index' ] ) //�N�G���L���b�V���̃C���f�b�N�X���Ȃ��ꍇ
				{ $_SESSION[ 'search_query_index' ] = 0; }

			if( !isset( $_SESSION[ 'search_query_hash' ][ $queryHash ] ) ) //�N�G���L���b�V�����Ȃ��ꍇ
			{
				$_SESSION[ 'search_query_hash' ][ $queryHash ] = $_SESSION[ 'search_query_index' ];
				$_GET[ 'q' ]                                   = $_SESSION[ 'search_query_index' ];
				$_SESSION[ 'search_query' ][ $_GET[ 'q' ] ]    = $_GET;

				++$_SESSION[ 'search_query_index' ];
			}
			else //�N�G���L���b�V��������ꍇ
				{ $_GET[ 'q' ] = $_SESSION[ 'search_query_hash' ][ $queryHash ]; }

			$target = $_GET[ 'embedID' ];

			ob_start();

			$templateFile = Template::getTemplate( $loginUserType , $loginUserRank , $target , 'SEARCH_EMBED_DESIGN' );

			if( !isset( $query[ 'run' ] ) || strtolower( $_GET[ 'run' ] ) != 'true' ) //�����̎��s���w������Ă��Ȃ��ꍇ
			{
				if( strlen( $templateFile ) ) //�e���v���[�g������ꍇ
					{ print $search->getFormString( $file , 'search.php' , 'form' ); }
				else //�e���v���[�g���Ȃ��ꍇ
					{ $system->drawSearchForm( $search , $loginUserType , $loginUserRank ); }
			}
			else //���������s����ꍇ
			{
				if( strlen( $templateFile ) ) //�e���v���[�g������ꍇ
				{
					if( $exists ) //�������ʂ�����ꍇ
					{
						SearchTableStack::pushStack( $table );
						$search->addHiddenForm( 'type' , $_GET[ 'type' ] );

						System::$CallMode = 'embed';
						print $search->getFormString( $templateFile , 'search.php' , 'success' , 'v' );
						System::$CallMode = 'normal';
						SearchTableStack::popStack();
					}
					else //�������ʂ��Ȃ��ꍇ
						{ print $search->getFormString( $templateFile , 'search.php' , 'failed' , 'v' ); }
				}
				else //�e���v���[�g���Ȃ��ꍇ
				{
					if( $exists ) //�������ʂ�����ꍇ
						{ $system->drawSearch( $gm , $search , $table , $loginUserType , $loginUserRank ); }
					else //�������ʂ��Ȃ��ꍇ
						{ $system->drawSearchNotFound( $gm , $loginUserType , $loginUserRank ); }
				}
			}

			$contents = ob_get_clean();

			$_GET = $getSwap;

			$this->addBuffer( $contents );
		}

		/**
			@brief   �������ʂ̌�����\������B
			@param   $iGM   GUIManager�I�u�W�F�N�g�ł��B���̃��\�b�h�ł͗��p���܂���B
			@param   $iRec  �o�^���̃��R�[�h�f�[�^�ł��B���̃��\�b�h�ł͗��p���܂���B
			@param   $iArgs �R�}���h�R�����g�����z��ł��B�����y�[�W�ɓn���N�G���p�����[�^���w�肵�܂��B
			@remarks run=true�̎w�肪�Ȃ��ꍇ�͉����o�͂��܂���B
		*/
		function embedSearchRow( &$iGM , $iRec , $iArgs ) //
		{
			$this->getEmbedParameter( $iArgs , $query , $search , $db , $system , $table );

			if( !isset( $query[ 'run' ] ) || strtolower( $query[ 'run' ] ) != 'true' ) //�����̎��s���w������Ă��Ȃ��ꍇ
				{ return; }

			$row = $db->getRow( $table );

			$this->addBuffer( $row );
		}

		/**
			@brief   �o�^����������`�F�b�N���Ē��߂��Ă���ΑΏ�����B
			@param   $iGM   GUIManager�I�u�W�F�N�g�ł��B���̃��\�b�h�ł͗��p���܂���B
			@param   $iRec  �o�^���̃��R�[�h�f�[�^�ł��B���̃��\�b�h�ł͗��p���܂���B
			@param   $iArgs �R�}���h�R�����g�����z��ł��B��������ʂɎw�肵�����ꍇ�ɓn���܂��B
		*/
		function maxRegistCheck( &$iGM , $iRec , $iArgs ) //
		{
			global $THIS_TABLE_MAX_REGIST;
			global $loginUserType;

			if( $iArgs[ 0 ] ) //������̎w�肪����ꍇ
				{ $THIS_TABLE_MAX_REGIST[ $_GET[ 'type' ] ][ $loginUserType ] = $iArgs[ 0 ]; }

			$isOver = SystemUtil::CheckTableRegistCount( $_GET[ 'type' ] );

			if( is_string( $isOver ) )
			{
				if( 'index' == WS_SYSTEM_SYSTEM_FORM_ACTON )
					{ header( 'Location: index.php?app_controller=Edit&type=' . $_GET[ 'type' ] . '&id=' . $isOver ); }
				else
					{ header( 'Location: edit.php?type=' . $_GET[ 'type' ] . '&id=' . $isOver ); }
			}
			else if( $isOver )
			{
				if( 'index' == WS_SYSTEM_SYSTEM_FORM_ACTON )
					{ header( 'Location: index.php?app_controller=Regist&type=' . $_GET[ 'type' ] . '&mode=registMaxCountOver' ); }
				else
					{ header( 'Location: regist.php?type=' . $_GET[ 'type' ] . '&mode=registMaxCountOver' ); }
			}
		}

		function link( &$iGM , $iRec , $iArgs ) //
		{
			global $NOT_LOGIN_USER_TYPE;
			global $loginUserType;
			global $loginUserRank;
			global $ACTIVE_DENY;

			$target = array_shift( $iArgs );
			$args   = Array();

			$set = Array(
				'index'        => Array( ''                         , Array( 'index.php'   , 'Index'                    ) ) ,
				'regist'       => Array( 'REGIST_FORM_PAGE_DESIGN'  , Array( 'regist.php'  , 'Register' , 'type'        ) ) ,
				'edit'         => Array( 'EDIT_FORM_PAGE_DESIGN'    , Array( 'edit.php'    , 'Edit'     , 'type' , 'id' ) ) ,
				'delete'       => Array( 'DELETE_CHECK_PAGE_DESIGN' , Array( 'delete.php'  , 'Delete'   , 'type' , 'id' ) ) ,
				'search'       => Array( 'SEARCH_FORM_PAGE_DESIGN'  , Array( 'search.php'  , 'Search'   , 'type'        ) ) ,
				'searchResult' => Array( 'SEARCH_RESULT_DESIGN'     , Array( 'search.php'  , 'Search'   , 'type'        ) ) ,
				'info'         => Array( 'INFO_PAGE_DESIGN'         , Array( 'info.php'    , 'Info'     , 'type' , 'id' ) ) ,
				'other'        => Array( ''                         , Array( 'other.php'   , 'Other'    , 'key'         ) ) ,
				'page'         => Array( ''                         , Array( 'page.php'    , 'Page'     , 'p'           ) ) ,
				'preview'      => Array( ''                         , Array( 'preview.php' , 'Preview'  , 'type' , 'id' ) ) ,
				'login'        => Array( ''                         , Array( 'login.php'   , 'Login'                    ) ) ,
				'logout'       => Array( ''                         , Array( 'login.php'   , 'Login'                    ) ) ,
				'switchUser'   => Array( ''                         , Array( 'login.php'   , 'Login'    , 'type' , 'id' ) ) ,
			);

			$label = $set[ $target ][ 0 ];
			$set   = $set[ $target ][ 1 ];
			$url   = array_shift( $set );
			$app   = array_shift( $set );
			$type  = '';
			$id    = '';

			if( 'index' == WS_SYSTEM_SYSTEM_FORM_ACTON )
			{
				$url    = 'index.php';
				$args[] = 'app_controller=' . $app;
			}

			foreach( $set as $param ) //�S�Ă̕K�{�p�����[�^������
			{
				$data = array_shift( $iArgs );

				if( 'type' == $param ) //�e�[�u�����̏ꍇ
					{ $type = $data; }
				else if( 'id' == $param && $iRec ) //ID�̏ꍇ
					{ $id = $data; }

				if( !$data ) //��������̏ꍇ
				{
					if( 'type' == $param && $iGM ) //�e�[�u�����̏ꍇ
					{
						$db   = $iGM->getDB();
						$data = $db->tablePlaneName;
						$type = $db->tablePlaneName;
					}

					if( 'id' == $param && $iRec ) //ID�̏ꍇ
					{
						$data = $iRec[ 'id' ];
						$id   = $iRec[ 'id' ];
					}

					if( !$data ) //�l����̏ꍇ
						{ $data = $_GET[ $param ]; }
				}

				if( !$data ) //�l���擾�ł��Ȃ������ꍇ
					{ throw new Exception( '����������܂��� : ' . $param ); }
				else //�l���擾�ł����ꍇ
					{ $args[] = $param . '=' . $data; }
			}

			if( 'searchResult' == $target ) //�������ʉ�ʂ̏ꍇ
				{ $args[] = 'run=true'; }

			if( 'logout' == $target ) //���O�A�E�g��ʂ̏ꍇ
				{ $args[] = 'logout=true'; }

			if( 'preview' == $target ) //�v���r���[��ʂ̏ꍇ
			{
                global $controllerName;
                $controller = strtolower( $controllerName );

				if( 'register' == $controller )
					{ $args[] = 'mode=regist'; }

				if( 'edit' == $controller )
					{ $args[] = 'mode=edit'; }
			}

			if( 'switchUser' == $target ) //���[�U�[�؂�ւ��̏ꍇ
				{ $args[] = 'run=true'; }

			if( count( $iArgs ) ) //�ǉ��̈���������ꍇ
				{ $args[] = implode( ' ' , $iArgs ); }

			if( count( $args ) ) //����������ꍇ
				{ $url = $url . '?' . implode( '&' , $args ); }

			if( $NOT_LOGIN_USER_TYPE == $loginUserType && $label ) //�\�������`�F�b�N�����Ɉ�v����ꍇ
			{
				$template = Template::getTemplate( $loginUserType , $loginUserRank , $type , $label );

				if( !$template ) //�e���v���[�g���ݒ肳��Ă��Ȃ��ꍇ
				{
					$id                                 = rand();
					$_SESSION[ 'redirect_path' ][ $id ] = $url;
					$url                                = 'login.php?redirect_id=' . $id;
				}
			}
			else if( $ACTIVE_DENY == $loginUserRank ) //���p��~���̃��[�U�[�̏ꍇ
			{
				if( !in_array( $target , Array( 'logout' , 'switchUser' ) ) ) //�g�p�\�ȃ����N�ł͂Ȃ��ꍇ
				{
					$oldOwner = Template::getOwner();

					if( 'info' == $target ) //�ڍ׃y�[�W�̏ꍇ
					{
						$db  = GMList::getDB( $type );
						$rec = $db->selectRecord( $id );
						SystemUtil::checkTableOwner( $type , $db , $rec );
					}

					$template = Template::getTemplate( $loginUserType , $loginUserRank , $type , $label , Template::getOwner() );

					if( !$template ) //�e���v���[�g���ݒ肳��Ă��Ȃ��ꍇ
						{ $url = 'index.php'; }

					Template::setOwner( $oldOwner );
				}
			}

			$this->addBuffer( $url );
		}

		function linkTag( &$iGM , $iRec , $iArgs ) //
		{
			global $NOT_LOGIN_USER_TYPE;
			global $loginUserType;
			global $loginUserRank;
			global $ACTIVE_DENY;

			$target = array_shift( $iArgs );
			$text   = array_shift( $iArgs );
			$args   = Array();

			$set = Array(
				'index'        => Array( ''                         , Array( 'index.php'   , 'Index'                    ) ) ,
				'regist'       => Array( 'REGIST_FORM_PAGE_DESIGN'  , Array( 'regist.php'  , 'Register' , 'type'        ) ) ,
				'edit'         => Array( 'EDIT_FORM_PAGE_DESIGN'    , Array( 'edit.php'    , 'Edit'     , 'type' , 'id' ) ) ,
				'delete'       => Array( 'DELETE_CHECK_PAGE_DESIGN' , Array( 'delete.php'  , 'Delete'   , 'type' , 'id' ) ) ,
				'search'       => Array( 'SEARCH_FORM_PAGE_DESIGN'  , Array( 'search.php'  , 'Search'   , 'type'        ) ) ,
				'searchResult' => Array( 'SEARCH_RESULT_DESIGN'     , Array( 'search.php'  , 'Search'   , 'type'        ) ) ,
				'info'         => Array( 'INFO_PAGE_DESIGN'         , Array( 'info.php'    , 'Info'     , 'type' , 'id' ) ) ,
				'other'        => Array( ''                         , Array( 'other.php'   , 'Other'    , 'key'         ) ) ,
				'page'         => Array( ''                         , Array( 'page.php'    , 'Page'     , 'p'           ) ) ,
				'preview'      => Array( ''                         , Array( 'preview.php' , 'Preview'  , 'type' , 'id' ) ) ,
				'login'        => Array( ''                         , Array( 'login.php'   , 'Login'                    ) ) ,
				'logout'       => Array( ''                         , Array( 'login.php'   , 'Login'                    ) ) ,
				'switchUser'   => Array( ''                         , Array( 'login.php'   , 'Login'    , 'type' , 'id' ) ) ,
			);

			$label = $set[ $target ][ 0 ];
			$set   = $set[ $target ][ 1 ];
			$url = array_shift( $set );
			$app = array_shift( $set );
			$type  = '';
			$id    = '';

			if( 'index' == WS_SYSTEM_SYSTEM_FORM_ACTON )
			{
				$url    = 'index.php';
				$args[] = 'app_controller=' . $app;
			}

			foreach( $set as $param ) //�S�Ă̕K�{�p�����[�^������
			{
				$data = array_shift( $iArgs );

				if( 'type' == $param ) //�e�[�u�����̏ꍇ
					{ $type = $data; }
				else if( 'id' == $param && $iRec ) //ID�̏ꍇ
					{ $id = $data; }

				if( !$data ) //��������̏ꍇ
				{
					if( 'type' == $param && $iGM ) //�e�[�u�����̏ꍇ
					{
						$db   = $iGM->getDB();
						$data = $db->tablePlaneName;
						$type = $db->tablePlaneName;
					}

					if( 'id' == $param && $iRec ) //ID�̏ꍇ
					{
						$data = $iRec[ 'id' ];
						$id   = $iRec[ 'id' ];
					}

					if( !$data ) //�l����̏ꍇ
						{ $data = $_GET[ $param ]; }
				}

				if( !$data ) //�l���擾�ł��Ȃ������ꍇ
					{ throw new Exception( '����������܂��� : ' . $param ); }
				else //�l���擾�ł����ꍇ
					{ $args[] = $param . '=' . $data; }
			}

			if( 'searchResult' == $target ) //�������ʉ�ʂ̏ꍇ
				{ $args[] = 'run=true'; }

			if( 'logout' == $target ) //���O�A�E�g��ʂ̏ꍇ
				{ $args[] = 'logout=true'; }

			if( 'preview' == $target ) //�v���r���[��ʂ̏ꍇ
			{
                global $controllerName;
                $controller = strtolower( $controllerName );

				if( 'register' == $controller )
					{ $args[] = 'mode=regist'; }

				if( 'Edit' == $controller )
					{ $args[] = 'mode=edit'; }
			}

			if( 'switchUser' == $target ) //���[�U�[�؂�ւ��̏ꍇ
				{ $args[] = 'run=true'; }

			if( count( $iArgs ) ) //�ǉ��̈���������ꍇ
				{ $args[] = implode( ' ' , $iArgs ); }

			if( count( $args ) ) //����������ꍇ
				{ $url = $url . '?' . implode( '&' , $args ); }

			if( $NOT_LOGIN_USER_TYPE == $loginUserType && $label ) //�\�������`�F�b�N�����Ɉ�v����ꍇ
			{
				$template = Template::getTemplate( $loginUserType , $loginUserRank , $type , $label );

				if( !$template ) //�e���v���[�g���ݒ肳��Ă��Ȃ��ꍇ
				{
					$id                                 = rand();
					$_SESSION[ 'redirect_path' ][ $id ] = $url;
					$url                                = 'login.php?redirect_id=' . $id;
				}
			}
			else if( $ACTIVE_DENY == $loginUserRank ) //���p��~���̃��[�U�[�̏ꍇ
			{
				if( !in_array( $target , Array( 'logout' , 'switchUser' ) ) ) //�g�p�\�ȃ����N�ł͂Ȃ��ꍇ
				{
					$oldOwner = Template::getOwner();

					if( 'info' == $target ) //�ڍ׃y�[�W�̏ꍇ
					{
						$db  = GMList::getDB( $type );
						$rec = $db->selectRecord( $id );
						SystemUtil::checkTableOwner( $type , $db , $rec );
					}

					$template = Template::getTemplate( $loginUserType , $loginUserRank , $type , $label , Template::getOwner() );

					if( !$template ) //�e���v���[�g���ݒ肳��Ă��Ȃ��ꍇ
						{ $url = 'index.php'; }

					Template::setOwner( $oldOwner );
				}
			}

			$this->addBuffer( '<a href="' . $url . '">' . $text . '</a>' );
		}

		/**
			@brief      �������ʖ��ߍ��ݏ����̃p�����[�^����������B
			@param[in]  $iArgs   �N�G���p�����[�^���i�[����CC�����z��B
			@param[out] $oQuery  �A�z�z�񉻂��ꂽ�N�G���B
			@param[out] $oSearch Search�I�u�W�F�N�g�B
			@param[out] $oDB     DB�I�u�W�F�N�g�B
			@param[out] $oSystem system�I�u�W�F�N�g�B
			@param[out] $oTable  �������ʂ̃e�[�u���B
		*/
		private function getEmbedParameter( $iArgs , &$oQuery , &$oSearch , &$oDB , &$oSystem , &$oTable ) //
		{
			global $gm;
			global $loginUserType;
			global $loginUserRank;
			global $magic_quotes_gpc;

			$queryString = implode( ' ' , $iArgs );
			$oQuery      = Array();

			parse_str( $queryString , $oQuery );

			$getSwap = $_GET;
			$_GET    = $oQuery;

			$oSearch = new Search( $gm[ $_GET[ 'type' ] ] , $_GET[ 'type' ] );
			$oDB     = $gm[ $_GET[ 'type' ] ]->getDB();
			$oSystem = SystemUtil::getSystem( $_GET[ 'type' ] );

			if( $magic_quotes_gpc || $oDB->char_code != 'sjis' ) //�G�X�P�[�v���s�v�ȏꍇ
				{ $oSearch->setParamertorSet( $_GET ); }
			else //�G�X�P�[�v���K�v�ȏꍇ
				{ $oSearch->setParamertorSet( addslashes_deep( $_GET ) ); }

			$oSystem->searchResultProc( $gm , $oSearch , $loginUserType , $loginUserRank );

			$oTable = $oSearch->getResult();

			$oSystem->searchProc( $gm , $oTable , $loginUserType , $loginUserRank );

			$_GET = $getSwap;
		}

		function IP( &$iGM , $iRec , $iArgs ) //
			{ $this->addBuffer( $_SERVER[ 'REMOTE_ADDR' ] ); }

        function js_load(&$gm, $rec, $cc){
            list($file) = $cc;
            if( strpos($file,'http') === 0 || strpos($file,'//') === 0 ){
                $this->addBuffer( '<script type="text/javascript" src="'.$file.'"></script>'."\n" );
            }else{
                $ts = filemtime($file);
                $this->addBuffer( '<script type="text/javascript" src="'.$file.'?'.$ts.'"></script>'."\n" );
            }
        }

        function css_load(&$gm, $rec, $cc){
            list($file) = $cc;
            if( strpos($file,'http') === 0 || strpos($file,'//') === 0 ){
                $this->addBuffer('<link rel="stylesheet" type="text/css" href="' . $file . '" media="all" />' . "\n");
            }else{
                $ts = filemtime($file);
                $this->addBuffer('<link rel="stylesheet" type="text/css" href="' . $file . '?' . $ts. '" media="all" />' . "\n");
            }
        }

		/************************************
		 *      For AffiliateSystem PRO2
		 * 
		 * ********************************** */

		//$args[0] true:start false,null:ret num
		function getTabindex(&$gm, $rec, $args) {
			global $tub_count;
			if (isset($args[0]) && $args[0] === 'true') {
				$tub_count = 0;
			}
			$tub_count++;
			$this->addBuffer('tabindex="' . $tub_count . '"');
		}

	}


//$db_a database�̔z��
//$d ���݂̐[��
function groupTableSelectFormMultiReflexive( &$str , $param , $check , $d = 0 , $id = null ){

    $db = $param[$d]['db'];
    if( $id == null ){
        $table = $db->getTable();
    }else{
        $table = $db->searchTable( $db->getTable() , $param[$d]['parent'] , '=' , $id );
    }
    $row = $db->getRow( $table );

    $pad = putCnt($d,'�@');

    for($i=0;$i<$row;$i++){
        $rec = $db->getRecord( $table , $i );
        if( isset( $param[ $d+1 ] ) ){
            $cid = $db->getData( $rec , 'id' );
            $str .= '<option value="" >'.$pad.$db->getData( $rec , $param[$d]['name'] )."\n";
            groupTableSelectFormMultiReflexive($str,$param,$check,$d+1,$cid);
        }else{
            $cid = $db->getData( $rec , 'id' );
            if( $cid == $check )
                $str .= '<option value="'.$cid.'" selected="selected">'.$pad.$db->getData( $rec , $param[$d]['name'] )."\n";
            else
                $str .= '<option value="'.$cid.'" >'.$pad.$db->getData( $rec , $param[$d]['name'] )."\n";
        }
    }
}
function searchGroupTableFormMultiReflexive( &$str , $param , $check , $d = 0 , $id = null ){
    $db = $param[$d]['db'];
    if( $id == null ){
        $table = $db->getTable();
    }else{
        $table = $db->searchTable( $db->getTable() , $param[$d]['parent'] , '=' , $id );
    }
    $row = $db->getRow( $table );

    $pad = putCnt($d,'�@');

    for($i=0;$i<$row;$i++){
        $rec = $db->getRecord( $table , $i );
        if( isset( $param[ $d+1 ] ) ){
            $cid = $db->getData( $rec , 'id' );
            if( $cid == $check )
                $str .= '<option value="'.$cid.'" selected="selected">'.$pad.$db->getData( $rec , $param[$d]['name'] )."\n";
            else
                $str .= '<option value="'.$cid.'" >'.$pad.$db->getData( $rec , $param[$d]['name'] )."\n";
            searchGroupTableFormMultiReflexive($str,$param,$check,$d+1,$cid);
        }else{
            $cid = $db->getData( $rec , 'id' );
            if( $cid == $check )
                $str .= '<option value="'.$cid.'" selected="selected">'.$pad.$db->getData( $rec , $param[$d]['name'] )."\n";
            else
                $str .= '<option value="'.$cid.'" >'.$pad.$db->getData( $rec , $param[$d]['name'] )."\n";
        }
    }
}

//�w�肵���������A�w�肵��������Ԃ�
function putCnt( $num , $char ){
    $str = "";
    for($i=0;$i<$num;$i++){
        $str .= $char;
    }
    return $str;
}

?>