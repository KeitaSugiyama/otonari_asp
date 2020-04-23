<?php


	$EDIT_TYPE                            = 'report';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = false;
	$LST[ $EDIT_TYPE ]                    = 'module/report.csv';
	$TDB[ $EDIT_TYPE ]                    = 'module/report.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = 'R';
	$ID_LENGTH[ $EDIT_TYPE ]              = 8;
	
	class ReportEx
	{
		function __construct()
		{
			$this->options[ 'adwaresNUser' ] = Array(
				'users' => Array(
					'nUser' ,
				) ,
				'exportName' => '�L���P�ʐ��ʃ��|�[�g.csv' ,
				'headers' => Array(
					'No' ,
					'�L��ID' ,
					'�L����' ,
					'�L���A�N�Z�X��' ,
					'�N���b�N�L�����ʐ�' ,
					'�N���b�N�L�����ʊz' ,
					'�A�t�B���G�C�g�L�����ʐ�' ,
					'�A�t�B���G�C�g�L�����ʊz' ,
					'�p���L�����ʐ�' ,
					'�p���L�����ʊz' ,
					'�L�����ʑ��z' ,
					'�e�B�A���z'
				) ,
				'targets' => Array(
					'adwares' => Array(
						'skipFilter' => 'nUser:adwares' ,
						'formats' => Array(
							'<!--# mod report serialNumber #-->' ,
							'<!--# value id #-->' ,
							'<!--# value name #-->' ,
							'<!--# code getRow access adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getRow click_pay state = 2 adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getSum click_pay cost state = 2 adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getRow pay state = 2 adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getSum pay cost state = 2 adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getRow continue_pay state = 2 adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getSum continue_pay cost state = 2 adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# calc (!--# code getSum click_pay cost state = 2 adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) + (!--# code getSum pay cost state = 2 adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) + (!--# code getSum continue_pay cost adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) #-->' ,
							'<!--# code getSum tier cost adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->'
						)
					) ,
					'secretAdwares' => Array(
						'skipFilter' => 'nUser:secretAdwares' ,
						'formats' => Array(
							'<!--# mod report serialNumber #-->' ,
							'<!--# value id #-->' ,
							'<!--# value name #-->' ,
							'<!--# code getRow access adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getRow click_pay state = 2 adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getSum click_pay cost state = 2 adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getRow pay state = 2 adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getSum pay cost state = 2 adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getRow continue_pay state = 2 adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getSum continue_pay cost state = 2 adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# calc (!--# code getSum click_pay cost state = 2 adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) + (!--# code getSum pay cost adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) + (!--# code getSum continue_pay cost state = 2 adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) #-->' ,
							'<!--# code getSum tier cost adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->'
						)
					)
				)
			);

			$this->options[ 'adwares' ] = Array(
				'users' => Array(
					'cUser' ,
					'admin'
				) ,
				'exportName' => '�L���P�ʐ��ʃ��|�[�g.csv' ,
				'headers' => Array(
					'No' ,
					'�L��ID' ,
					'�L����' ,
					'�L���A�N�Z�X��' ,
					'�N���b�N�L�����ʐ�' ,
					'�N���b�N�L�����ʊz' ,
					'�A�t�B���G�C�g�L�����ʐ�' ,
					'�A�t�B���G�C�g�L�����ʊz' ,
					'�p���L�����ʐ�' ,
					'�p���L�����ʊz' ,
					'�L�����ʑ��z' ,
					'�e�B�A���z'
				) ,
				'targets' => Array(
					'adwares' => Array(
						'ownerColumn' => Array(
							'cUser' => 'cuser'
						) ,
						'searchColumn' => Array(
							'cuser'
						) ,
						'formats' => Array(
							'<!--# mod report serialNumber #-->' ,
							'<!--# value id #-->' ,
							'<!--# value name #-->' ,
							'<!--# code getRow access adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getRow click_pay state = 2 adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getSum click_pay cost state = 2 adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getRow pay state = 2 adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getSum pay cost state = 2 adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getRow continue_pay state = 2 adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getSum continue_pay cost state = 2 adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# calc (!--# code getSum click_pay cost state = 2 adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) + (!--# code getSum pay cost state = 2 adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) + (!--# code getSum continue_pay cost adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) #-->' ,
							'<!--# code getSum tier cost adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->'
						)
					) ,
					'secretAdwares' => Array(
						'ownerColumn' => Array(
							'nUser' => 'open_user' ,
							'cUser' => 'cuser'
						) ,
						'searchColumn' => Array(
							'cuser'
						) ,
						'formats' => Array(
							'<!--# mod report serialNumber #-->' ,
							'<!--# value id #-->' ,
							'<!--# value name #-->' ,
							'<!--# code getRow access adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getRow click_pay state = 2 adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getSum click_pay cost state = 2 adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getRow pay state = 2 adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getSum pay cost state = 2 adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getRow continue_pay state = 2 adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getSum continue_pay cost state = 2 adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# calc (!--# code getSum click_pay cost state = 2 adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) + (!--# code getSum pay cost state = 2 adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) + (!--# code getSum continue_pay cost adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) #-->' ,
							'<!--# code getSum tier cost adwares = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->'
						)
					)
				)
			);

			$this->options[ 'cUser' ] = Array(
				'users' => Array(
					'admin'
				) ,
				'exportName' => '�L����P�ʐ��ʃ��|�[�g.csv' ,
				'headers' => Array(
					'No' ,
					'ID' ,
					'���O' ,
					'�X�֔ԍ�' ,
					'�s���{��' ,
					'�Z��' ,
					'�d�b�ԍ�' ,
					'���[���A�h���X' ,
					'�L���A�N�Z�X��' ,
					'�N���b�N�L�����ʐ�' ,
					'�N���b�N�L�����ʊz' ,
					'�A�t�B���G�C�g�L�����ʐ�' ,
					'�A�t�B���G�C�g�L�����ʊz' ,
					'�p���L�����ʐ�' ,
					'�p���L�����ʊz' ,
					'�L�����ʑ��z' ,
					'�e�B�A���z'
				) ,
				'targets' => Array(
					'cUser' => Array(
						'formats' => Array(
							'<!--# mod report serialNumber #-->' ,
							'<!--# value id #-->' ,
							'<!--# value name #-->' ,
							'<!--# value zip1 #-->-<!--# value zip2 #-->' ,
							'<!--# alias prefectures adds id name #-->' ,
							'<!--# value add_sub #-->' ,
							'<!--# value tel #-->' ,
							'<!--# value mail #-->' ,
							'<!--# code getRow access cuser = (!--# value id #--) #-->' ,
							'<!--# code getRow click_pay state = 2 cuser = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getSum click_pay cost state = 2 cuser = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getRow pay state = 2 cuser = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getSum pay cost state = 2 cuser = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getRow continue_pay state = 2 cuser = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# code getSum continue_pay cost state = 2 cuser = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->' ,
							'<!--# calc (!--# code getSum click_pay cost state = 2 cuser = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) + (!--# code getSum pay cost state = 2 cuser = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) + (!--# code getSum continue_pay cost cuser = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) #-->' ,
							'<!--# code getSum tier cost cuser = (!--# value id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #-->'
						)
					)
				)
			);

			$this->options[ 'returnss' ] = Array(
				'users' => Array(
					'admin' ,
				) ,
				'exportName' => '�����\���������|�[�g.csv' ,
				'headers' => Array(
					'No' ,
					'�\������' ,
					'���z' ,
					'���' ,
					'�A�t�B���G�C�^�[ID' ,
					'�A�t�B���G�C�^�[��' ,
					'���Z�@�֖�' ,
					'���Z�@�֔ԍ�' ,
					'�x�X��' ,
					'�x�X�ԍ�' ,
					'���' ,
					'�����ԍ�' ,
					'�������`(�J�i)' ,
				) ,
				'targets' => Array(
					'returnss' => Array(
						'searchSystem' => true ,
						'formats' => Array(
							'<!--# mod report serialNumber #-->' ,
							'<!--# value regist #-->' ,
							'<!--# value cost #-->' ,
							'<!--# value state #-->' ,
							'<!--# value owner #-->' ,
							'<!--# alias nUser owner id name #-->' ,
							'<!--# alias nUser owner id bank #-->' ,
							'<!--# alias nUser owner id bank_code #-->' ,
							'<!--# alias nUser owner id branch #-->' ,
							'<!--# alias nUser owner id branch_code #-->' ,
							'<!--# alias nUser owner id bank_type #-->' ,
							'<!--# alias nUser owner id number #-->' ,
							'<!--# alias nUser owner id bank_name #-->'
						)
					) ,
				)
			);
			
			$this->options[ 'use_fee' ] = Array(
				'users' => Array(
					'admin'
				) ,
				'exportName' => '<!--# code now format  Y_m_d_G_i #-->_�����p���ʏW�v���|�[�g.csv' ,
				'headers' => Array(
					'No' ,
					'ID' ,
					'���O' ,
					'�X�֔ԍ�' ,
					'�s���{��' ,
					'�Z��' ,
					'�d�b�ԍ�' ,
					'���[���A�h���X' ,
					'�L���A�N�Z�X��' ,
					'�N���b�N�L�����ʐ�' ,
					'�N���b�N�L�����ʊz' ,
					'�A�t�B���G�C�g�L�����ʐ�' ,
					'�A�t�B���G�C�g�L�����ʊz' ,
					'�p���L�����ʐ�' ,
					'�p���L�����ʊz' ,
					'�L�����ʑ��z' ,
					'�e�B�A���z' ,
					'��V���v'
				) ,
				'targets' => Array(
					'cUser' => Array(
						'formats' => Array(
							'<!--# mod report serialNumber #-->' ,
							'<!--# value id #-->' ,
							'<!--# value name #-->' ,
							'<!--# value zip1 #-->-<!--# value zip2 #-->' ,
							'<!--# alias prefectures adds id name #-->' ,
							'<!--# value add_sub #-->' ,
							'<!--# value tel #-->' ,
							'<!--# value mail #-->' ,
							'<!--# code getRow access cuser = (!--# value id #--) #-->' ,
							'<!--# code getRow click_pay cuser = (!--# value id #--) report_id = (!--# mod report reportID #--) #-->' ,
							'<!--# regist thru-add pay (!--# code getSum click_pay cost cuser = (!--# value id #--) report_id = (!--# mod report reportID #--) #--) #-->' ,
							'<!--# code getRow pay cuser = (!--# value id #--) report_id = (!--# mod report reportID #--) #-->' ,
							'<!--# regist thru-add pay (!--# code getSum pay cost cuser = (!--# value id #--) report_id = (!--# mod report reportID #--) #--) #-->' ,
							'<!--# code getRow continue_pay cuser = (!--# value id #--) report_id = (!--# mod report reportID #--) #-->' ,
							'<!--# regist thru-add pay (!--# code getSum continue_pay cost cuser = (!--# value id #--) report_id = (!--# mod report reportID #--) #--) #-->' ,
							'<!--# regist get pay #-->' ,
							'<!--# regist thru-add pay (!--# code getSum tier cost cuser = (!--# value id #--) report_id = (!--# mod report reportID #--) #--) #-->' ,
							'<!--# regist pop pay #-->'
						)
					)
				) ,
				'before' => 'use_fee'
			);
		}

		/**
			@brief     ���|�[�g�̏o�͖����擾����B
			@exception InvalidArgumentException $iCaseName_ �ɖ����Ȓl���w�肵���ꍇ�B
			@param[in] $iCaseName_ ���|�[�g�P�[�X���B
			@return    ���|�[�g�P�[�X�ɑΉ�����o�͖��B
		*/
		function getExportName( $iCaseName_ )
		{
			if( !$iCaseName_ ) //�P�[�X������̏ꍇ
				{ throw new InvalidArgumentException( '���� $iCaseName_ �͖����ł�' ); }

			if( !array_key_exists( $iCaseName_ , $this->options ) ) //���|�[�g�P�[�X���ݒ肳��Ă��Ȃ��ꍇ
				{ throw new InvalidArgumentException( '���� $iCaseName_ �͖����ł�[' . $iCaseName_ . ']' ); }

			return $this->options[ $iCaseName_ ][ 'exportName' ];
		}

		/**
			@brief     ���|�[�g���_�E�����[�h�p�ɏo�͂���B
			@exception InvalidArgumentException $iCaseName_ �ɖ����Ȓl���w�肵���ꍇ�B
			@param[in] $iCaseName_ ���|�[�g�P�[�X���B
			@param[in] $iCaseName_ �_�E�����[�h�t�@�C�����B�ȗ��B
		*/
		function downloadReport( $iCaseName_ , $iExportName_ = null )
		{
			global $loginUserType;
			global $LOGIN_ID;

			if( !$iCaseName_ ) //�P�[�X������̏ꍇ
				{ throw new InvalidArgumentException( '���� $iCaseName_ �͖����ł�' ); }

			if( !array_key_exists( $iCaseName_ , $this->options ) ) //���|�[�g�P�[�X���ݒ肳��Ă��Ȃ��ꍇ
				{ throw new InvalidArgumentException( '���� $iCaseName_ �͖����ł�[' . $iCaseName_ . ']' ); }

			if( $iExportName_ ) //�_�E�����[�h�����w�肳��Ă���ꍇ
				{ $exportName = $iExportName_; }
			else //�_�E�����[�h�����w�肳��Ă��Ȃ��ꍇ
				{ $exportName = $this->getExportName( $iCaseName_ ); }

			$gm     = GMList::GetGM( 'report' );
			$exportName = $gm->getCCResult( null , $exportName );

			$option = $this->options[ $iCaseName_ ];

			if( FALSE === array_search( $loginUserType , $option[ 'users' ] ) ) //���|�[�g�擾�������Ȃ��ꍇ
				{ throw new RuntimeException( 'downloadReport �������ł��܂���[' . $loginUserType . '][' . $LOGIN_ID . ']' ); }

			$searchResult = $this->getReportResult( $option );
			$stdout       = fopen( 'php://output' , 'w' );

			$this->sendDownloadHeader( $exportName );

			fputs( $stdout , '�������F' . date( 'Y n/j' ) . "\n" );
			fputs( $stdout , implode( ',' , $option[ 'headers' ] ) . "\n" );
		
			
			if( isset( $option['before']))	//�O�����̎��s
			{
				$method = "before_".$option['before'];
				$class_name = 'mod_report';
				
				$sys = new $class_name();
				$sys->$method( $searchResult, $option );
			}

			while( $line = $this->popLine( $searchResult , $option ) ) //���ʂ�ǂݍ��߂�ԌJ��Ԃ�
				{ fputs( $stdout , $line . "\n" ); }
			
		
			if( isset( $option['after']))	//�O�����̎��s
			{
				$method = "after_".$option['after'];
				$class_name = 'mod_report';
				
				$sys = new $class_name();
				$sys->$method( $searchResult, $option );
			}

			fputs( $stdout , "\n" );
			fclose( $stdout );
		}

		/**
			@brief     ���|�[�g�I�v�V���������Ƀ��|�[�g�f�[�^����������B
			@exception InvalidArgumentException $iOption_ �ɖ����Ȓl���w�肵���ꍇ�B
			@param[in] $iOption_ ���|�[�g�I�v�V�����B
			@return    �������ʃf�[�^�B
		*/
		private function getReportResult( $iOption_ )
		{
			global $gm;
			global $loginUserType;
			global $loginUserRank;
			global $LOGIN_ID;

			if( !is_array( $iOption_ ) ) //�I�v�V�������z��ł͂Ȃ��ꍇ
				{ throw new InvalidArgumentException( '���� $iOption_ �͖����ł�[' . $iOption_ . ']' ); }

			$results = Array();
			$targets = $iOption_[ 'targets' ];

			if( !is_array( $targets ) ) //�^�[�Q�b�g�e�[�u�����z��ł͂Ȃ��ꍇ
				{ $targets = Array( $targets ); }

			foreach( $targets as $target => $config ) //�S�Ẵ^�[�Q�b�g�e�[�u���������B
			{
				$db    = GMList::getDB( $target );
				$table = $db->getTable();

				if( isset( $config[ 'searchSystem' ] ) ) //�V�X�e���������ݒ肳��Ă���ꍇ
				{
					$search = new Search( $gm[ $target ] , $target );
					$system = SystemUtil::getSystem( $target );

					if( $magic_quotes_gpc )
						{ $search->setParamertorSet( $_GET ); }
					else
						{ $search->setParamertorSet( addslashes_deep( $_GET ) ); }

					$system->searchResultProc( $gm , $search , $loginUserType , $loginUserRank );

					$table = $search->getResult();

					$system->searchProc( $gm , $table , $loginUserType , $loginUserRank );
				}

				if( isset( $config[ 'ownerColumn' ][ $loginUserType ] ) ) //�I�[�i�[�������ݒ肳��Ă���ꍇ
					{ $table = $db->searchTable( $table , $config[ 'ownerColumn' ][ $loginUserType ] , '=' , '%' . $LOGIN_ID . '%' ); }

				if( isset( $config[ 'searchColumn' ] ) ) //�����������ݒ肳��Ă���ꍇ
				{
					if( !is_array( $config[ 'searchColumn' ] ) ) //�z��ł͂Ȃ��ꍇ
						{ $searchs = Array( $config[ 'searchColumn' ] ); }
					else //�z��̏ꍇ
						{ $searchs = $config[ 'searchColumn' ]; }

					foreach( $searchs as $search ) //�S�Ă̌�������������
					{
						if( $_POST[ $search ] ) //�������������M����Ă���ꍇ
							{ $table = $db->searchTable( $table , $search , '=' , $_POST[ $search ] ); }
					}
				}

				$row = $db->getRow( $table );

				$results[ 'tables' ][] = Array( $target , $table , $row );
			}

			$results[ 'cursor' ] = Array( 0 , 0 );

			return $results;
		}

		/**
			@brief         �������ʃf�[�^���玟�̏o�͍s�̓��e���擾����B
			@exception     InvalidArgumentException $iResult_ , $iOption_ �̂����ꂩ�ɖ����Ȓl���w�肵���ꍇ�B
			@param[in,out] $iResult_ �������ʃf�[�^�B
			@param[in]     $iOption_ ���|�[�g�I�v�V�����B
			@retval        �o�͓��e  ���̍s���擾�\�ȏꍇ�B
			@retval        null      ���̍s���擾�ł��Ȃ��ꍇ�B
		*/
		private function popLine( &$ioResult_ , $iOption_ )
		{
			List( $currentTable , $currentRow ) = $ioResult_[ 'cursor' ];
			List( $name , $table , $row )       = $ioResult_[ 'tables' ][ $currentTable ];

			while( $row <= $currentRow ) //�e�[�u���̍s�͈͂𒴂���ԌJ��Ԃ�
			{
				$currentTable += 1;
				$currentRow    = 0;

				if( count( $ioResult_[ 'tables' ] ) <= $currentTable ) //�e�[�u�����X�g�͈̔͂𒴂��Ă���ꍇ
					{ return null; }

				List( $name , $table , $row ) = $ioResult_[ 'tables' ][ $currentTable ];
			}

			$db          = GMList::GetDB( $name );
			$rec         = $db->getRecord( $table , $currentRow );
			$currentRow += 1;

			$ioResult_[ 'cursor' ] = Array( $currentTable , $currentRow );

			if( $this->isSkip( $name , $rec , $iOption_ ) ) //�X�L�b�v�����Ɉ�v����ꍇ
				{ return $this->popLine( $ioResult_ , $iOption_ ); }

			return $this->createLineString( $name , $rec , $iOption_ );
		}

		private function isSkip( $iTableName_ , $iRec_ , $iOption_ )
		{
			if( !$iOption_[ 'targets' ][ $iTableName_ ][ 'skipFilter' ] ) //�t�B���^���ݒ肳��Ă��Ȃ��ꍇ
				{ return false; }

			switch( $iOption_[ 'targets' ][ $iTableName_ ][ 'skipFilter' ] ) //�t�B���^���ŕ���
			{
				case 'nUser:adwares'       : //nUser����̍L�����|�[�g
				case 'nUser:secretAdwares' : //nUser����̃N���[�Y�h�L�����|�[�g
				{
					$gm     = GMList::GetGM( $iTableName_ );
					$result = $gm->getCCResult( $iRec_ , '<!--# calc (!--# code getSum click_pay cost adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) + (!--# code getSum pay cost adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) + (!--# code getSum continue_pay cost adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) + (!--# code getSum tier cost adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) #-->' );

					if( !$result ) //���ʊz����̏ꍇ
						{ return true; }

					break;
				}

				default : //���̑�
					{ throw new InvalidArgumentException( '���� $iOption_ �͖����ł�[' . $iOption_[ 'targets' ][ $iTableName_ ][ 'skipFilter' ] . ']' ); }
			}

			return false;
		}

		private function createLineString( $iTableName_ , $iRec_ , $iOption_ )
		{
			$gm      = GMList::GetGM( $iTableName_ );
			$formats = $iOption_[ 'targets' ][ $iTableName_ ][ 'formats' ];
			$line    = Array();

			foreach( $formats as $format ) //�S�Ă̏o�͍��ڂ�����
				{ $line[] = $gm->getCCResult( $iRec_ , $format ); }

			return implode( ',' , $line );
		}

		/**
			@brief     �_�E�����[�h�p��HTTP�w�b�_���o�͂���B
			@param[in] $iExportName_ �_�E�����[�h�t�@�C�����B
		*/
		private function sendDownloadHeader( $iExportName_ )
		{
			if( !$iExportName_ ) //�_�E�����[�h������̏ꍇ
				{ throw new InvalidArgumentException( '���� $iExportName_ �͖����ł�' ); }

			header( 'Cache-Control: public' );
			header( 'Pragma:' );
	        header( 'Content-Disposition: attachment; filename="' . $iExportName_ . '"' );
			header( 'Content-type: application/x-octet-stream; name="' . $iExportName_ . '"; charset=Shift_JIS' );
		}

		private $option = Array();
	}

	class mod_report extends command_base
	{
		function serialNumber( &$iGM_ , $iRec_ , $iArgs_ )
		{
			self::$SerialNumber += 1;

			$this->addBuffer( self::$SerialNumber );
		}

		function beginTime( &$iGM_ , $iRec_ , $iArgs_ )
		{
			if( $_POST[ 'registA' ] )
				{ $time = $_POST[ 'registA' ]; }
			else
			{
				$year  = ( $_POST[ 'yearA' ]  ? (int)$_POST[ 'yearA' ]  : 1970 );
				$month = ( $_POST[ 'monthA' ] ? (int)$_POST[ 'monthA' ] : 1 );
				$day   = ( $_POST[ 'dayA' ]   ? (int)$_POST[ 'dayA' ]   : 1 );

				$time = mktime( 0 , 0 , 0 , $month , $day , $year );
			}

			if( 0 > $time )
				{ $time = 0; }

			$this->addBuffer( $time );
		}

		function endTime( &$iGM_ , $iRec_ , $iArgs_ )
		{
			if( $_POST[ 'registB' ] )
				{ $time = $_POST[ 'registB' ]; }
			else
			{
				if( !$_POST[ 'yearA' ] && !$_POST[ 'monthA' ] && !$_POST[ 'dayA' ] )
					{ $time = mktime( 0 , 0 , 0 , date( 'n' ) , date( 'j' ) + 1 , date( 'Y' ) ); }
				else
				{
					$year  = ( $_POST[ 'yearB' ]  ? (int)$_POST[ 'yearB' ]  : 2038 );
					$month = ( $_POST[ 'monthB' ] ? (int)$_POST[ 'monthB' ] : 1 );
					$day   = ( $_POST[ 'dayB' ]   ? (int)$_POST[ 'dayB' ]   : 19 );

					$time = mktime( 0 , 0 , 0 , $month , $day + 1 , $year );
				}
			}

			$this->addBuffer( $time );
		}
		
		function before_use_fee( &$ioResult_ , $iOption_ )
		{
			$db      = GMList::getDB( 'report' );
			
			//�v�Z�Ώۂ̌����w�肷��B
			if( isset($_POST[ 'regist' ]) && $_POST[ 'regist' ] )
			{
				//�v�Z�ς݂��ǂ������m�F����
				$table = $db->searchTable( $db->getTable(), 'regist', '=', $_POST[ 'regist' ]);
				
				if( $rec = $db->getFirstRecord($table) ){
					//�v�Z�ς݂ł������ꍇ�͂���ID��variable�ɃZ�b�g����
					self::$ReportID = $db->getData( $rec, 'id');
					return;
				}
			}
			
			$time = mktime(0, 0, 0, date( 'n'  ) , 1, date( 'Y' ));
			
			$table = $db->searchTable( $db->getTable(), 'type', '=', 'use_fee');
			$table = $db->searchTable( $table, 'regist', '>=', $time);
			
			if( $rec = $db->getFirstRecord($table) ){
				//�v�Z�ς݂ł������ꍇ�͂���ID��variable�ɃZ�b�g����
				$id = $db->getData( $rec, 'id');
			}else{
				//�v�Z�ς݂łȂ������ꍇ�́Areport�e�[�u���̃J������ǉ����A����ID��variable�ɃZ�b�g����
				$rec = $db->getNewRecord( Array( 'type'=> 'use_fee','regist'=>time()) );
				$id = SystemUtil::getNewId( $db,'report');
				$db->setData(  $rec, 'id', $id );
				$db->addRecord($rec);
				
				//�v�Z�ΏۂɂȂ郌�R�[�h�̃J������ID���Z�b�g���Ă����B
				$pay_tables = array( 'pay', 'continue_pay', 'click_pay', 'tier');
				
				foreach( $pay_tables as $pay_table )
				{
					$pdb = GMList::getDB( $pay_table );
					$ptable = $pdb->getTable();
					$ptable= $pdb->searchTable( $ptable, 'report_id', '=', '');
					if( $pay_table != "tier"){
						$ptable= $pdb->searchTable( $ptable, 'state', '=', 2);
					}
					$ptable= $pdb->searchTable( $ptable, 'regist', '<', $time);
					
					$pdb->setTableDataUpdate($ptable,'report_id',$id);
				}
			}
			
			self::$ReportID = $id;
		}
		function reportID( &$iGM_ , $iRec_ , $iArgs_ )
		{
			$this->addBuffer( self::$ReportID );
		}
	
		/**
		 * report�e�[�u����type�����ɍ�����������t�x�[�X�őI������selectBox�̕\��
		 *
		 * @param gm GUIManager�I�u�W�F�N�g�ł��B
		 * @param rec �o�^���̃��R�[�h�f�[�^�ł��B
		 * @param args �R�}���h�R�����g�����z��ł��B
		 *
         * �������Ftype
         * �������Ftime format
         * ��O�����F���I�����ڒl(�ȗ���)
		 */
        function reportSelectForm( &$gm , $rec , $args ){
            if(isset($args[0]) && strlen($args[0]))
                $type = $args[0];
            else
                $type = "";
                
            if(isset($args[1]) && strlen($args[1]))
                $format = $args[1];
            else
                $format = "Y-m-d G:i:s";
            
            $tgm = SystemUtil::getGMforType( 'report' );
            $db = $tgm->getDB();
            
            $table = $db->getTable();
            $table = $db->searchTable( $table, 'type', '=', $type );
            
            $row = $db->getRow( $table );
            
            if(!$row){ $this->addBuffer( $args[2] ); return; }
            
            $index = Array();
            $value  = Array();
        
            if( isset($args[2]) && strlen($args[2]) ){
                $index[] = SystemUtil::systemArrayEscape( $args[2] );
                $value[] = "";
            }
            
            for($i=0;$i<$row;$i++){
                $rec = $db->getRecord( $table , $i );
                $regist = $db->getData( $rec , 'regist' );
                $index[] = SystemUtil::systemArrayEscape( SystemUtil::mb_date( $format, $regist)  );
                $value[] = $regist;
            }
            
            $index = join('/',$index);
            $value = join('/',$value);
            
            $this->addBuffer( $gm->getCCResult( $rec, '<!--# form option regist '.$check.' '.$value.' '.$index.$option.' #-->' ) );
        }
		
		private static $ReportID = '';
		private static $SerialNumber = 0;
	}