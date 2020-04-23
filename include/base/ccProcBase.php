<?php

	class ccProcBase //
	{
		// �֐��̊���U��
		static function controller(&$gm, $rec, $cc)
		{
			return ccProc::controller($gm, $rec, $cc);
		}

		// �ݒ肳�ꂽ���R�[�h���J�������Ō������A�}�b�`�������ڂ𕡐��̕����񂩂猟���A�Ή����镶�����\������B
		function valueReplace(&$gm, $rec, $cc)
		{
			$ret = "";
			if( !isset($gm) ) { throw new InternalErrorException( 'CommandComment Null Pointer Error -> $gm ( via valueReplace )' ); }
			$db   = $gm->getDB();
			$data = $db->getData( $rec, $cc[1] );
			if( is_bool($data) )
			{
				if( $data ) { $data	 = 'TRUE'; }
				else		{ $data	 = 'FALSE'; }
				$cc[2] = strtoupper($cc[2]);
			}
			$befor = explode( '/', $cc[2] );
			$after = explode( '/', $cc[3] );
			for($i=0; $i<count($befor); $i++)
			{
				if( $data == $befor[$i] ) { $ret .= $after[$i]; break; }
			}
			
			//�߂�l����̏ꍇ�̃f�t�H���g���Z�b�g
			if( !strlen($ret) && isset($cc[4]) ){  $ret = $cc[4]; }
			return $ret;
		}

		// �������œ��͂���������𕡐��̕����񂩂猟���A�Ή����镶�����\������B
		function valueValueReplace(&$gm, $rec, $cc)
		{
			$ret = "";
			if( !isset($gm) ) { throw new InternalErrorException( 'CommandComment Null Pointer Error -> $gm ( via valueValueReplace )' ); }
			$data = $cc[1];
			if( is_bool($data) )
			{
				if( $data ) { $data	 = 'TRUE'; }
				else		{ $data	 = 'FALSE'; }
				$cc[2] = strtoupper($cc[2]);
			}
			$befor	 = explode( '/', $cc[2] );
			$after	 = explode( '/', $cc[3] );
			for($i=0; $i<count($befor); $i++)
			{
				if( $data == $befor[$i] ) { $ret .= $after[$i]; break; }
			}
			
			//�߂�l����̏ꍇ�̃f�t�H���g���Z�b�g
			if( !strlen($ret) && isset($cc[4]) ){  $ret = $cc[4]; }
			return $ret;
		}
        
        function arrayReplace(&$gm, $rec, $cc)
        {
			$ret = "";
			if( !isset($gm) ) { throw new InternalErrorException( 'CommandComment Null Pointer Error -> $gm ( via arrayReplace )' ); }
			$db = $gm->getDB();
            
			$array	 = explode( '/', $db->getData( $rec, $cc[1] ) );
			$befor	 = array_flip(explode( '/', $cc[3] ));
			$after	 = explode( '/', $cc[4] );
                
			foreach( $array as $data ){
                if( is_bool($data) )
                {
                    if( $data ) { $data	 = 'TRUE'; }
                    else		{ $data	 = 'FALSE'; }
                    $cc[3]	 = strtoupper($cc[3]);
                }
                
                if( strlen($ret) ) { $ret .= $cc[2]; }
                
                if( isset( $befor[$data] ) && isset($after[ $befor[$data] ]) ){
               		$ret .= $after[ $befor[$data] ];
				}
			}
			
			//�߂�l����̏ꍇ�̃f�t�H���g���Z�b�g
			if( !strlen($ret) && isset($cc[5]) ){  $ret = $cc[5]; }
			return $ret;
        }

        static $alias_cash = null;
		// �e���v���[�g�Ɋ֘A�Â���ꂽ���R�[�h�̎w�肳�ꂽ�J������ʃe�[�u���̎w��J�������L�[�Ɍ������s�Ȃ��u�����s�Ȃ��B 
		function alias(&$_gm, $rec, $cc)
		{
			$ret = "";
			if( !isset($_gm) ) { throw new InternalErrorException( 'CommandComment Null Pointer Error -> $gm ( via alias )' ); }
			
			$tables		= explode( '/', $cc[1] );
			$key		= $cc[2];
			$vals		= explode( '/', $cc[3] );
			$draws		= explode( '/', $cc[4] );
			$brflags	= explode( '/', $cc[5] );
			
			$value		 = $_gm->db->getData( $rec, $key );
			
			$cnt = count($tables);
			for( $i=0 ; $i<$cnt && $value != '' ; $i++ )
			{
				if( !isset($_gm->aliasDB[$tables[$i]]) ) { $_gm->addAlias($tables[$i]); }
				$gm = GMList::getGM( $tables[$i] );
				$db = $gm->getDB();
				if( isset(self::$alias_cash[$tables[$i]]) && isset(self::$alias_cash[$tables[$i]][$vals[$i]]) && isset(self::$alias_cash[$tables[$i]][$vals[$i]][$value]) ){
					$rec = self::$alias_cash[$tables[$i]][$vals[$i]][$value];
				}else{
					$table = $db->getTable($_gm->table_type);
					$table		 = $db->searchTable(  $table, $vals[$i], '=', $value );

					if( !$db->existsRow( $table ) )
					{
						$value = '';
						break;
					}

					$rec		= $db->getRecord( $table, 0 );
					self::$alias_cash[$tables[$i]][$vals[$i]][$value] = $rec;
				}
				
				//timestamp��bool�ɑΉ������
				//$value		= $db->getData( $rec, $draws[$i], true );
				$oldFormat = $gm->timeFormatOnce;
				$gm->setTimeFormatOnce( $_gm->getTimeFormat() );
				if( isset( $brflags[$i] ) && $brflags[$i] )
					{ $value = self::controller( $gm, $rec, array( "value", $draws[$i], $brflags[$i] ) ); }
				else
					{ $value = self::controller( $gm, $rec, array( "value", $draws[$i] ) ); }
				$gm->timeFormatOnce = $oldFormat;
			}	
			
			$ret .= $value;
		
			//�߂�l����̏ꍇ�̃f�t�H���g���Z�b�g
			if( !strlen($ret) && isset($cc[5]) ){  $ret = $cc[5]; }
			return $ret;
		}

		// �w�肵���������ʃe�[�u���̎w��J�������L�[�Ɍ������s�Ȃ��u�����s�Ȃ��B 
		function valueAlias(&$_gm, $_rec, $cc)
		{
			$ret = "";
			if( !isset($_gm) ) { throw new InternalErrorException( 'CommandComment Null Pointer Error -> $_gm ( via valueAlias )' ); }
			if( !isset($_gm->aliasDB[$cc[1]]) ) { $_gm->addAlias($cc[1]); }
			
			$gm = GMList::getGM( $cc[1] );
			$db = $_gm->aliasDB[$cc[1]];
			
			if( isset(self::$alias_cash[$cc[1]]) && isset(self::$alias_cash[$cc[1]][ $cc[3]]) && isset(self::$alias_cash[$cc[1]][ $cc[3]][$cc[2]]) ){
				$rec = self::$alias_cash[$cc[1]][ $cc[3]][$cc[2]];
			}else{
				$table = $db->getTable($_gm->table_type);
				$table = $db->searchTable( $table, $cc[3], '=', $cc[2] );

				if( !$db->existsRow( $table ) )
				{
					if( !strlen($ret) && isset($cc[5]) ){  $ret = $cc[5]; }
					return $ret;
				}

				$rec   = $db->getRecord( $table, 0 );
				self::$alias_cash[$cc[1]][ $cc[3]][$cc[2]] = $rec;
			}
			
				//timestamp��bool�ɑΉ������
			//$ret .= $db->getData( $rec, $cc[4], true );
			$oldFormat = $gm->timeFormatOnce;
			$gm->setTimeFormatOnce( $_gm->getTimeFormat() );
			$ret .= self::controller( $gm, $rec, array( "value",  $cc[4] ) );
			$gm->timeFormatOnce = $oldFormat;
			
			//�߂�l����̏ꍇ�̃f�t�H���g���Z�b�g
			if( !strlen($ret) && isset($cc[5]) ){  $ret = $cc[5]; }
			
			return $ret;
		}

		// �w�肵���J�����ɓ������e�������ʃe�[�u���̎w��J�������L�[�Ɍ������s�Ȃ��u�����s�Ȃ��A���̈ꗗ��Ԃ��B
		function arrayAlias(&$_gm, $_rec, $cc)
		{
			$ret = array();
			if( !isset($_gm) ) { throw new InternalErrorException( 'CommandComment Null Pointer Error -> $_gm ( via arrayAlias )' ); }
			if( !isset( $_gm->aliasDB[$cc[1]] ) ) { $_gm->addAlias($cc[1]); }
			
			$sep = '/';
			if( isset( $cc[5])){ $sep = $cc[5]; }
			
			$gm = GMList::getGM( $cc[1] );
			$db    = $_gm->aliasDB[$cc[1]];
			$table = $db->getTable($_gm->table_type);
					
			$data = $_gm->db->getData( $_rec, $cc[2] );

			if( !empty( $data ) ){
				$array       = explode( '/' , $data );
			foreach( $array as $key ){
				if( strlen($key) == 0 ) { continue; }
				if( isset(self::$alias_cash[$cc[1]]) && isset(self::$alias_cash[$cc[1]][$cc[3]]) && isset(self::$alias_cash[$cc[1]][$cc[3]][$key]) ){
					$arec = self::$alias_cash[$cc[1]][$cc[3]][$key];
				}else{
					$stable	 = $db->searchTable( $table, $cc[3], '=', $key );
					if( $db->getRow( $stable ) == 0 ) { continue; }
					$arec	 = $db->getRecord( $stable, 0 );
					self::$alias_cash[$cc[1]][$cc[3]][$key] = $arec;
				}

				$oldFormat = $gm->timeFormatOnce;
				$gm->setTimeFormatOnce( $_gm->getTimeFormat() );
				$ret[] = self::controller( $gm, $arec, array( "value",  $cc[4] ) );
				$gm->timeFormatOnce = $oldFormat;
			}
			
				return join( $ret, $sep );
			}else{
				//�߂�l����̏ꍇ�̃f�t�H���g���Z�b�g
				return $cc[6];
			}
		}

		// /�Ō��������u����̕�����z���Ԃ��B 
		function arrayValueAlias(&$_gm, $_rec, $cc)
		{
			$ret = array();
			if( !isset($_gm) ) { throw new InternalErrorException( 'CommandComment Null Pointer Error -> $_gm ( via arrayValueAlias )' ); }
			if( !isset( $_gm->aliasDB[$cc[1]] ) ) { $_gm->addAlias($cc[1]); }
			
			$gm = GMList::getGM( $cc[1] );
			$db    = $_gm->aliasDB[$cc[1]];
			$table = $db->getTable();
			
			if( !empty( $cc[2] ) ){

				$array = explode( '/' , $cc[2] );
				foreach( $array as $key ){
					if( isset(self::$alias_cash[$cc[1]]) && isset(self::$alias_cash[$cc[1]][$cc[3]]) && isset(self::$alias_cash[$cc[1]][$cc[3]][$key]) ){
						$arec = self::$alias_cash[$cc[1]][$cc[3]][$key];
					}else{
						$stable	 = $db->searchTable( $table, $cc[3], '=', $key );
						$arec	 = $db->getRecord( $stable, 0 );
						self::$alias_cash[$cc[1]][$cc[3]][$key] = $arec;
					}

					$oldFormat = $gm->timeFormatOnce;
					$gm->setTimeFormatOnce( $_gm->geTimeFormat() );
					$ret[] = self::controller( $gm, $arec, array( "value",  $cc[4] ) );
					$gm->timeFormatOnce = $oldFormat;
				}
			
				return join( $ret, $sep );
			}else{
				//�߂�l����̏ꍇ�̃f�t�H���g���Z�b�g
				return $cc[6];
			}
		}
	}

	class CommandBase extends command_base //
	{
		/**
		 * �e�[�u���̑S�s����I������selectBox�̕\��
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 *
         * �������Fname
         * �������Ftable��
         * ��O�����Foption���ƂȂ�J������
         * ��l�����Fvalue�ƂȂ�J������
         * ��܈����F�����l(�ȗ���)
         * ��Z�����F���I�����ڒl(�ȗ���)
         * �掵�����F�^�O�I�v�V�����v�f(�ȗ���)
         * �攪�`�����F�J�������A���Z�q�A�l��3�Z�b�g�̃��[�v�B
		 */
        function tableSelectForm( &$gm , $rec , $args ){
			$nrec = $rec;
            if(isset($args[4]) && strlen($args[4]))
                $check = $args[4];
            else
                $check = "";

            if(isset($args[6]) && strlen($args[6]))
                $option = ' '.$args[6];
            else
                $option = "";

            $tgm = SystemUtil::getGMforType( $args[1] );
            $db = $tgm->getDB();

            $table      = $db->getTable();
			$columnName = '';
			$parentName = '';
			$CCID       = 0;

            if(isset($args[7])){
            	for($i=0;isset($args[$i+7]);$i+=3){

					switch( $args[8+$i] )
					{
						case 'sort' :
						{
							$table = $db->sortTable( $table, $args[7+$i], $args[9+$i], true );
							break;
						}

						case 'linkage' :
						{
							$columnName = $args[7+$i];
							$parentName = $args[9+$i];
							$CCID       = rand();
							break;
						}

						default :
						{
							$table = $db->searchTable( $table, $args[7+$i], $args[8+$i], $args[9+$i] );
							break;
						}
					}
            	}
            }

            $row = $db->getRow( $table );

            $index = Array();
            $value  = Array();

            if( isset($args[5]) && strlen($args[5]) ){
                $index[] = SystemUtil::systemArrayEscape( $args[5] );
                $value[] = "";
            }

            for($i=0;$i<$row;$i++){
                $rec = $db->getRecord( $table , $i );
                $index[] = SystemUtil::systemArrayEscape($db->getData( $rec , $args[2] ) );
                $value[] = SystemUtil::systemArrayEscape($db->getData( $rec , $args[3] ) );
            }

            $index = join('/',$index);
            $value = join('/',$value);

            $param = Weave::Get( 'tagParam' , 'tableSelectForm' );

            if( count( $param ) )
			{
				if( !$option )
					{ $option = ' ' . implode( '\ ' , $param ); }
				else
					{ $option .= '\ ' . implode( '\ ' , $param ); }
			}


			if( $columnName && $parentName )
			{
				$_SESSION[ 'CC' ][ $CCID ][ 'indexName' ] = $args[ 2 ];
				$_SESSION[ 'CC' ][ $CCID ][ 'valueName' ] = $args[ 3 ];

				$this->addBuffer( $gm->getCCResult( $nrec, '<!--# form option '.$args[0].' '.$check.' '.$value.' '.$index.$option.' #-->' ) );
	            $this->addBuffer( '<script>LinkageForm( "' . $parentName . '" , "' . $args[ 0 ] . '" , "' . $args[ 1 ] . '" , "' . $columnName . '" , "' . $CCID . '" );</script>' );
			}
			else
				{ $this->addBuffer( $gm->getCCResult( $nrec, '<!--# form option '.$args[0].' '.$check.' '.$value.' '.$index.$option.' #-->' ) ); }
        }

		/**
		 * �e�[�u���̑S�s����I������selectBox�̕\��
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 *
         * �������Fname
         * �������Ftable��
         * ��O�����Foption���ƂȂ�J������
         * ��l�����Fvalue�ƂȂ�J������
         * ��܈����F�s��
         * ��Z�����F�����l(�ȗ���)
         * �掵�����F���I�����ڒl(�ȗ���)
         * �攪�����F�^�O�I�v�V�����v�f(�ȗ���)
         * ���`�����F�J�������A���Z�q�A�l��3�Z�b�g�̃��[�v�B
		 */
        function tableMultipleForm( &$gm , $rec , $args ){
			$nrec = $rec;
            if(isset($args[5]) && strlen($args[5]))
                $check = $args[5];
            else
                $check = "";

            if(isset($args[7]) && strlen($args[7]))
                $option = ' '.$args[7];
            else
                $option = "";

            $tgm = SystemUtil::getGMforType( $args[1] );
            $db = $tgm->getDB();

            $table = $db->getTable();

            if(isset($args[8])){
            	for($i=0;isset($args[$i+8]);$i+=3){

					switch( $args[9+$i] )
					{
						case 'sort' :
						{
							$table = $db->sortTable( $table, $args[8+$i], $args[10+$i], true );
							break;
						}

						default :
						{
							$table = $db->searchTable( $table, $args[8+$i], $args[9+$i], $args[10+$i] );
							break;
						}
					}

            	}
            }

            $row = $db->getRow( $table );

            $index = Array();
            $value  = Array();

            if( isset($args[6]) && strlen($args[6]) ){
                $index[] = SystemUtil::systemArrayEscape( $args[6] );
                $value[] = "";
            }

            for($i=0;$i<$row;$i++){
                $rec = $db->getRecord( $table , $i );
                $index[] = SystemUtil::systemArrayEscape($db->getData( $rec , $args[2] ) );
                $value[] = SystemUtil::systemArrayEscape($db->getData( $rec , $args[3] ) );
            }

            $index = join('/',$index);
            $value = join('/',$value);

            $this->addBuffer( $gm->getCCResult( $nrec, '<!--# form multiple '.$args[0].' '.$check.' '.$value.' '.$index.' '.$args[4].$option.' #-->' ) );
        }

		/**
		 * �e�q�֌W�̃e�[�u���̑S�s����A�e�e�[�u���ŃO���[�v�������q�e�[�u���I���̂��߂�selectBox�̕\��
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 *
         * �������Fname
         * �������F�etable��
         * ��O�����F�O���[�v��
         * ��l�����F�qtable��
         * ��܈����Foption���ƂȂ�J������
         * ��Z�����Fvalue�ƂȂ�J������
         * �掵�����F�e��ID�������J������
         * �攪�����F�����l(�ȗ���)
         * �������F���I�����ڒl(�ȗ���)
		 */
        function groupTableSelectForm( &$gm , $rec , $args ){
            if( isset( $_POST[ $args[0] ] ) )
                $check = $_POST[ $args[0] ];
            else if(isset($args[7]))
                $check = $args[7];
            else
                $check = "";

            $pgm = SystemUtil::getGMforType( $args[1] );
            $cgm = SystemUtil::getGMforType( $args[3] );

            $pdb = $pgm->getDB();
            $cdb = $cgm->getDB();

            $ptable = $pdb->getTable();
            $prow = $pdb->getRow( $ptable );

            $str = '<select name="'.$args[0].'" >'."\n";

            if( isset($args[8]) ){
                $str .= '  <optgroup label="'.$args[8].'" >'."\n";

                $str .= '    <option value="" >'.$args[8]."\n";
                $str .= '  </optgroup>'."\n";
            }

            for($i=0;$i<$prow;$i++){
                $prec = $pdb->getRecord( $ptable , $i );

                $str .= '  <optgroup label="'.$pdb->getData( $prec , $args[2] ).'" >'."\n";

                $ctable = $cdb->searchTable( $cdb->getTable() , $args[6] , '=' , $pdb->getData( $prec , 'id' ) );
                $crow = $cdb->getRow( $ctable );

                for($j=0;$j<$crow;$j++){
                    $crec = $cdb->getRecord( $ctable , $j );
                    $option = $cdb->getData( $crec , $args[4] );
                    $value = $cdb->getData( $crec , $args[5] );
                    if( $check == $value )
                        $str .= '    <option value="'.$value.'" selected="selected">'.$option."\n";
                    else
                        $str .= '    <option value="'.$value.'" >'.$option."\n";
                }
                $str .= '  </optgroup>'."\n";
            }

            $str .= '</select>'."\n";

            $this->addBuffer( $str );
        }

		/**
		 * ���i�K�̐e�q�֌W�̃e�[�u���̑S�s���g����Group�T�[�`�p�̃t�H�[�����o��
         * value�͑S��ID�Ƃ��܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 *
         *
         * �������Fname
         * �������F�����l
         * ��O�����F���I�����ڒl
         * ��l�����F�etable
         * ��܈����F�eoption
         * ��Z�����F�qtable
         * �掵�����F�qoption
         * �攪�����F�e��ID�������q�̃J������
         *
         * �ȉ��A�Z�`�������[�v
		 */
        function groupTableSelectFormMulti( &$gm , $rec , $args ){

            if( isset( $_POST[ $args[0] ] ) )
                $check = $_POST[ $args[0] ];
            else if(isset($args[1]))
                $check = $args[1];
            else
                $check = "";

            $tcount = ( count($args) - 5 ) / 3;

            $_gm = SystemUtil::getGM();

            $param = Array();

            $param[0]['db'] = $_gm[ $args[3] ]->getDB();    //�ŏ�ʃe�[�u�����擾
            $param[0]['name'] = $args[4];
            for($i=0;$i<$tcount;$i++){
                $param[$i+1]['db'] = $_gm[ $args[ 5 + $i*3 ] ]->getDB();
                $param[$i+1]['name'] = $args[ 6 + $i*3 ];
                $param[$i+1]['parent'] = $args[ 7 + $i*3 ];
            }

            $str = '<select name="'.$args[0].'" >'."\n";


            if( isset($args[2]) ){
                $str .= '    <option value="" >'.$args[2]."\n";
            }

            groupTableSelectFormMultiReflexive( $str, $param , $check );

            $str .= '</select>'."\n";

            $this->addBuffer( $str );
        }

		/**
		 * �e�q�֌W�̃e�[�u���̑S�s���g����Group�T�[�`�p�̃t�H�[�����o��
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 *
         * �������Fname
         * �������F�etable��
         * ��O�����F�O���[�v��
         * ��l�����F�qtable��
         * ��܈����Foption���ƂȂ�J������
         * ��Z�����Fvalue�ƂȂ�J������
         * �掵�����F�e��ID�������J������
         * �攪�����F�����l(�ȗ���)
         * �������F���I�����ڒl(�ȗ���)
		 */
        function searchGroupTableForm( &$gm , $rec , $args ){
            if( isset( $_POST[ $args[0] ] ) )
                $check = $_POST[ $args[0] ];
            else if(isset($args[7]))
                $check = $args[7];
            else
                $check = "";

            $pgm = SystemUtil::getGMforType( $args[1] );
            $cgm = SystemUtil::getGMforType( $args[3] );

            $pdb = $pgm->getDB();
            $cdb = $cgm->getDB();

            $ptable = $pdb->getTable();
            $prow = $pdb->getRow( $ptable );

            $str = '<select name="'.$args[0].'" >'."\n";

            if( isset($args[8]) ){
                $str .= '    <option value="" >'.$args[8]."\n";
            }

            for($i=0;$i<$prow;$i++){
                $prec = $pdb->getRecord( $ptable , $i );

                $pid = $pdb->getData( $prec , 'id' );
                $str .= '  <option value="'.$pid.'" >'.$pdb->getData( $prec , $args[2] )."\n";

                $ctable = $cdb->searchTable( $cdb->getTable() , $args[6] , '=' , $pid );
                $crow = $cdb->getRow( $ctable );

                for($j=0;$j<$crow;$j++){
                    $crec = $cdb->getRecord( $ctable , $j );
                    $option = "�@".$cdb->getData( $crec , $args[4] );
                    $value = $cdb->getData( $crec , $args[5] );
                    if( $check == $value )
                        $str .= '    <option value="'.$value.'" selected="selected">'.$option."\n";
                    else
                        $str .= '    <option value="'.$value.'" >'.$option."\n";
                }
            }

            $str .= '</select>'."\n";

            $this->addBuffer( $str );
        }

		/**
		 * ���i�K�̐e�q�֌W�̃e�[�u���̑S�s���g����Group�T�[�`�p�̃t�H�[�����o��
         * value�͑S��ID�Ƃ��܂��B
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 *
         *
         * �������Fname
         * �������F�����l
         * ��O�����F���I�����ڒl
         * ��l�����F�etable
         * ��܈����F�eoption
         * ��Z�����F�qtable
         * �掵�����F�qoption
         * �攪�����F�e��ID�������q�̃J������
         *
         * �ȉ��A�Z�`�������[�v
		 */
        function searchGroupTableFormMulti( &$gm , $rec , $args ){
            if( isset( $_POST[ $args[0] ] ) )
                $check = $_POST[ $args[0] ];
            else if(isset($args[1]))
                $check = $args[1];
            else
                $check = "";

            $tcount = ( count($args) - 5 ) / 3;

            $_gm = SystemUtil::getGM();

            $param = Array();

            $param[0]['db'] = $_gm[ $args[3] ]->getDB();    //�ŏ�ʃe�[�u�����擾
            $param[0]['name'] = $args[4];
            for($i=0;$i<$tcount;$i++){
                $param[$i+1]['db'] = $_gm[ $args[ 5 + $i*3 ] ]->getDB();
                $param[$i+1]['name'] = $args[ 6 + $i*3 ];
                $param[$i+1]['parent'] = $args[ 7 + $i*3 ];
            }

            $str = '<select name="'.$args[0].'" >'."\n";


            if( isset($args[2]) ){
                $str .= '    <option value="" >'.$args[2]."\n";
            }

            searchGroupTableFormMultiReflexive( $str, $param , $check );

            $str .= '</select>'."\n";

            $this->addBuffer( $str );
        }


		/**
		 * �e�[�u���̑S�s����I������checkBox�̕\��
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 *
         * �������Fname
         * �������Ftable��
         * ��O�����F�\�����ƂȂ�J������
         * ��l�����Fvalue�ƂȂ�J������
         * ��܈����F��؂蕶��
         * ��Z�����F�����l(�ȗ���)
         * �掵�����F���I�����ڒl(�ȗ���)
         * �攪�����F���ɕ\�����鐔(�ȗ���)
		 */
        function tableCheckForm( &$gm , $rec , $args ){

			$nrec = $rec;
            if(isset($args[5]) && strlen($args[5]))
                $check = $args[5];
            else
                $check = "";
            if(isset($args[7]) && strlen($args[7]))
                $option = ' '.$args[7];
            else
                $option = "";

            $tgm = SystemUtil::getGMforType( $args[1]);
            $db = $tgm->getDB();
            $table = $db->getTable();

			if(isset($args[8])){
				for($i=0;isset($args[$i+8]);$i+=3){

					switch( $args[9+$i] )
					{
						case 'sort' :
						{
							$table = $db->sortTable( $table, $args[8+$i], $args[10+$i], true );
							break;
						}

						default :
						{
							$table = $db->searchTable( $table, $args[8+$i], $args[9+$i], $args[10+$i] );
							break;
						}
					}

				}
			}

            $row = $db->getRow( $table );

            $index = Array();
            $value  = Array();

            if( isset($args[6]) && strlen($args[6]) ){
                $index[] = SystemUtil::systemArrayEscape($args[6]);
                $value[] = '';
            }

            for($i=0;$i<$row;$i++){
                $rec = $db->getRecord( $table , $i );
                $index[] = SystemUtil::systemArrayEscape($db->getData( $rec , $args[2] ) );
                $value[] = SystemUtil::systemArrayEscape($db->getData( $rec , $args[3] ) );
            }

            $index = join('/',$index);
            $value = join('/',$value);

            $this->addBuffer( $gm->getCCResult( $nrec, '<!--# form checkbox '.$args[0].' '.$check.' '.(isset($args[4])?$args[4]:'').' '.$value.' '.$index.$option.'  '.(isset($args[9])?$args[9]:'').' #-->' ) );
        }


		/**
		 * �e�[�u���̑S�s����I������radioButton�̕\��
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 *
         * �������Fname
         * �������Ftable��
         * ��O�����F�\�����ƂȂ�J������
         * ��l�����Fvalue�ƂȂ�J������
         * ��܈����F��؂蕶��
         * ��Z�����F�����l(�ȗ���)
         * �掵�����F���I�����ڒl(�ȗ���)
         * �攪�����F���ɕ\�����鐔(�ȗ���)
		 */
        function tableRadioForm( &$gm , $rec , $args ){
			$nrec = $rec;
            if(isset($args[5]) && strlen($args[5]))
                $check = $args[5];
            else
                $check = "";
            if(isset($args[7]) && strlen($args[7]))
                $option = ' '.$args[7];
            else
                $option = "";

            $tgm = SystemUtil::getGMforType( $args[1]);
            $db = $tgm->getDB();
            $table = $db->getTable();

			if(isset($args[8])){
				for($i=0;isset($args[$i+8]);$i+=3){

					switch( $args[9+$i] )
					{
						case 'sort' :
						{
							$table = $db->sortTable( $table, $args[8+$i], $args[10+$i], true );
							break;
						}

						default :
						{
							$table = $db->searchTable( $table, $args[8+$i], $args[9+$i], $args[10+$i] );
							break;
						}
					}

				}
			}

            $row = $db->getRow( $table );

            $index = Array();
            $value  = Array();

            if( isset($args[6]) && strlen($args[6]) ){
                $index[] = SystemUtil::systemArrayEscape( $args[6] );
                $value[] = '';
            }

            for($i=0;$i<$row;$i++){
                $rec = $db->getRecord( $table , $i );
                $index[] = SystemUtil::systemArrayEscape($db->getData( $rec , $args[2] ) );
                $value[] = SystemUtil::systemArrayEscape($db->getData( $rec , $args[3] ) );
            }

            $index = join('/',$index);
            $value = join('/',$value);

            $this->addBuffer( $gm->getCCResult( $nrec, '<!--# form radio '.$args[0].' '.$check.' '.$args[4].' '.$value.' '.$index.$option.'  '.$args[9].' #-->' ) );
        }

		/**
			@brief �e�[�u���̓��e��񋓂��邽�߂̒P���ȕ��@��񋟂���B
			@param $iGM   GUIManager�I�u�W�F�N�g�ł��B���̃��\�b�h�ł͗��p���܂���B
			@param $iRec  �o�^���̃��R�[�h�f�[�^�ł��B���̃��\�b�h�ł͗��p���܂���B
			@param $iArgs �R�}���h�R�����g�����z��ł��B�����y�[�W�ɓn���N�G���p�����[�^���w�肵�܂��B
		*/
		function listing( &$iGM , $iRec , $iArgs ) //
		{
			$this->getEmbedParameter( $iArgs , $query , $search , $db , $system , $table );

			if( !$query[ 'listingID' ] ) //���X�g�p�[�c�̎w�肪�Ȃ��ꍇ
				{ return; }

			$originRow = $db->getRow( $table );

			if( $query[ 'row' ] ) //�ő�s���̎w�肪����ꍇ
				{ $table = $db->limitOffset( $table , 0 , $query[ 'row' ] ); }

			$row = $db->getRow( $table );

			if( !$query[ 'sort' ] ) //�\�[�g���w�肳��Ă��Ȃ��ꍇ
			{
				$query[ 'sort' ]     = 'shadow_id';
				$query[ 'sort_PAL' ] = 'asc';

				$table = $db->sortTable( $table , 'shadow_id' , 'asc' , true );
			}

			$gm = GMList::getGM( $query[ 'type' ] );

			array_push( $gm->templateStack , $iGM->getCurrentTemplate() );

			$getSwap   = $_GET;
			$queryHash = sha1( serialize( $query ) );

			if( !$_SESSION[ 'search_query_index' ] ) //�N�G���L���b�V���̃C���f�b�N�X���Ȃ��ꍇ
				{ $_SESSION[ 'search_query_index' ] = 0; }

			if( !isset( $_SESSION[ 'search_query_hash' ][ $queryHash ] ) ) //�N�G���L���b�V�����Ȃ��ꍇ
			{
				$_SESSION[ 'search_query_hash' ][ $queryHash ] = $_SESSION[ 'search_query_index' ];
				$_GET[ 'q' ]                                   = $_SESSION[ 'search_query_index' ];
				$query[ 'q' ]                                  = $_SESSION[ 'search_query_index' ];
				$_SESSION[ 'search_query' ][ $_GET[ 'q' ] ]    = $query;

				++$_SESSION[ 'search_query_index' ];
			}
			else //�N�G���L���b�V��������ꍇ
			{
				$_GET[ 'q' ]  = $_SESSION[ 'search_query_hash' ][ $queryHash ];
				$query[ 'q' ] = $_SESSION[ 'search_query_hash' ][ $queryHash ];
			}

			if( !$row ) //�������ʂ���̏ꍇ
			{
				$this->addBuffer( $gm->getCCResult( $rec , '<!--# adapt ' . $query[ 'listingID' ] . '_failed #-->' ) );
				return;
			}

			$repeat = ( $query[ 'row' ] ? $query[ 'row' ] : $row );
			$table->onCash();

			$this->addBuffer( $gm->getCCResult( $rec , '<!--# adapt ' . $query[ 'listingID' ] . '_head #-->' ) );

			if( $query[ 'row' ] && $originRow > $query[ 'row' ] ) //�\������茟�����ʂ������ꍇ
				{ $this->addBuffer( $gm->getCCResult( $rec , '<!--# adapt ' . $query[ 'listingID' ] . '_head_over #-->' ) ); }

			for( $i = 0 ; $repeat > $i ; ++$i ) //�S�Ă̍s������
			{
				if( $row <= $i ) //�e�[�u���̍s���𒴂���ꍇ
				{
					$output = $gm->getCCResult( null , '<!--# adapt ' . $query[ 'listingID' ] . '_empty #-->' );

					if( $output ) //�o�͓��e������ꍇ
						{ $this->addBuffer( $output . "\n" ); }
				}
				else //�e�[�u�����烌�R�[�h������ꍇ
				{
					$rec = $db->getRecord( $table , $i );

					$gm->setVariable( 'num' , $i + 1 );

					if( $query[ 'source_nobr' ] ) //�\�[�X�̉��s�����w�肪����ꍇ
						{ $this->addBuffer( $gm->getCCResult( $rec , '<!--# adapt ' . $query[ 'listingID' ] . ' #-->' ) ); }
					else
						{ $this->addBuffer( $gm->getCCResult( $rec , '<!--# adapt ' . $query[ 'listingID' ] . ' #-->' ) . "\n" ); }

					$db->cashReset();
				}
			}

			if( $query[ 'row' ] && $originRow > $query[ 'row' ] ) //�\������茟�����ʂ������ꍇ
				{ $this->addBuffer( $gm->getCCResult( $rec , '<!--# adapt ' . $query[ 'listingID' ] . '_foot_over #-->' ) ); }

			$this->addBuffer( $gm->getCCResult( $rec , '<!--# adapt ' . $query[ 'listingID' ] . '_foot #-->' ) );

			$table->offCash();
			$_GET = $getSwap;

			array_pop( $gm->templateStack );
		}

		/**
			@brief �l��񋓂��邽�߂̒P���ȕ��@��񋟂���B
			@param $iGM   GUIManager�I�u�W�F�N�g�ł��B���̃��\�b�h�ł͗��p���܂���B
			@param $iRec  �o�^���̃��R�[�h�f�[�^�ł��B���̃��\�b�h�ł͗��p���܂���B
			@param $iArgs �R�}���h�R�����g�����z��ł��B
		*/
		function each( &$iGM , $iRec , $iArgs ) //
		{
			$listingID  = array_shift( $iArgs );
			$values     = array_shift( $iArgs );
			$sourceNoBR = array_shift( $iArgs );
			$i          = 0;

			foreach( explode( '/' , $values ) as $value ) //�S�Ă̍s������
			{
				$iGM->setVariable( 'num' , $i + 1 );
				$iGM->setVariable( 'val' , $value );

				if( $sourceNoBR ) //�\�[�X�̉��s�����w�肪����ꍇ
					{ $this->addBuffer( $iGM->getCCResult( $iRec , '<!--# adapt ' . $listingID . ' #-->' ) ); }
				else
					{ $this->addBuffer( $iGM->getCCResult( $iRec , '<!--# adapt ' . $listingID . ' #-->' ) . "\n" ); }
			}
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
	}
