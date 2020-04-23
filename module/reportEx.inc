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
				'exportName' => '広告単位成果レポート.csv' ,
				'headers' => Array(
					'No' ,
					'広告ID' ,
					'広告名' ,
					'広告アクセス数' ,
					'クリック広告成果数' ,
					'クリック広告成果額' ,
					'アフィリエイト広告成果数' ,
					'アフィリエイト広告成果額' ,
					'継続広告成果数' ,
					'継続広告成果額' ,
					'広告成果総額' ,
					'ティア総額'
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
				'exportName' => '広告単位成果レポート.csv' ,
				'headers' => Array(
					'No' ,
					'広告ID' ,
					'広告名' ,
					'広告アクセス数' ,
					'クリック広告成果数' ,
					'クリック広告成果額' ,
					'アフィリエイト広告成果数' ,
					'アフィリエイト広告成果額' ,
					'継続広告成果数' ,
					'継続広告成果額' ,
					'広告成果総額' ,
					'ティア総額'
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
				'exportName' => '広告主単位成果レポート.csv' ,
				'headers' => Array(
					'No' ,
					'ID' ,
					'名前' ,
					'郵便番号' ,
					'都道府県' ,
					'住所' ,
					'電話番号' ,
					'メールアドレス' ,
					'広告アクセス数' ,
					'クリック広告成果数' ,
					'クリック広告成果額' ,
					'アフィリエイト広告成果数' ,
					'アフィリエイト広告成果額' ,
					'継続広告成果数' ,
					'継続広告成果額' ,
					'広告成果総額' ,
					'ティア総額'
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
				'exportName' => '換金申請履歴レポート.csv' ,
				'headers' => Array(
					'No' ,
					'申請日時' ,
					'金額' ,
					'状態' ,
					'アフィリエイターID' ,
					'アフィリエイター名' ,
					'金融機関名' ,
					'金融機関番号' ,
					'支店名' ,
					'支店番号' ,
					'種別' ,
					'口座番号' ,
					'口座名義(カナ)' ,
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
				'exportName' => '<!--# code now format  Y_m_d_G_i #-->_請求用成果集計レポート.csv' ,
				'headers' => Array(
					'No' ,
					'ID' ,
					'名前' ,
					'郵便番号' ,
					'都道府県' ,
					'住所' ,
					'電話番号' ,
					'メールアドレス' ,
					'広告アクセス数' ,
					'クリック広告成果数' ,
					'クリック広告成果額' ,
					'アフィリエイト広告成果数' ,
					'アフィリエイト広告成果額' ,
					'継続広告成果数' ,
					'継続広告成果額' ,
					'広告成果総額' ,
					'ティア総額' ,
					'報酬合計'
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
			@brief     レポートの出力名を取得する。
			@exception InvalidArgumentException $iCaseName_ に無効な値を指定した場合。
			@param[in] $iCaseName_ レポートケース名。
			@return    レポートケースに対応する出力名。
		*/
		function getExportName( $iCaseName_ )
		{
			if( !$iCaseName_ ) //ケース名が空の場合
				{ throw new InvalidArgumentException( '引数 $iCaseName_ は無効です' ); }

			if( !array_key_exists( $iCaseName_ , $this->options ) ) //レポートケースが設定されていない場合
				{ throw new InvalidArgumentException( '引数 $iCaseName_ は無効です[' . $iCaseName_ . ']' ); }

			return $this->options[ $iCaseName_ ][ 'exportName' ];
		}

		/**
			@brief     レポートをダウンロード用に出力する。
			@exception InvalidArgumentException $iCaseName_ に無効な値を指定した場合。
			@param[in] $iCaseName_ レポートケース名。
			@param[in] $iCaseName_ ダウンロードファイル名。省略可。
		*/
		function downloadReport( $iCaseName_ , $iExportName_ = null )
		{
			global $loginUserType;
			global $LOGIN_ID;

			if( !$iCaseName_ ) //ケース名が空の場合
				{ throw new InvalidArgumentException( '引数 $iCaseName_ は無効です' ); }

			if( !array_key_exists( $iCaseName_ , $this->options ) ) //レポートケースが設定されていない場合
				{ throw new InvalidArgumentException( '引数 $iCaseName_ は無効です[' . $iCaseName_ . ']' ); }

			if( $iExportName_ ) //ダウンロード名が指定されている場合
				{ $exportName = $iExportName_; }
			else //ダウンロード名が指定されていない場合
				{ $exportName = $this->getExportName( $iCaseName_ ); }

			$gm     = GMList::GetGM( 'report' );
			$exportName = $gm->getCCResult( null , $exportName );

			$option = $this->options[ $iCaseName_ ];

			if( FALSE === array_search( $loginUserType , $option[ 'users' ] ) ) //レポート取得権限がない場合
				{ throw new RuntimeException( 'downloadReport を完了できません[' . $loginUserType . '][' . $LOGIN_ID . ']' ); }

			$searchResult = $this->getReportResult( $option );
			$stdout       = fopen( 'php://output' , 'w' );

			$this->sendDownloadHeader( $exportName );

			fputs( $stdout , '生成日：' . date( 'Y n/j' ) . "\n" );
			fputs( $stdout , implode( ',' , $option[ 'headers' ] ) . "\n" );
		
			
			if( isset( $option['before']))	//前処理の実行
			{
				$method = "before_".$option['before'];
				$class_name = 'mod_report';
				
				$sys = new $class_name();
				$sys->$method( $searchResult, $option );
			}

			while( $line = $this->popLine( $searchResult , $option ) ) //結果を読み込める間繰り返し
				{ fputs( $stdout , $line . "\n" ); }
			
		
			if( isset( $option['after']))	//前処理の実行
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
			@brief     レポートオプションを元にレポートデータを検索する。
			@exception InvalidArgumentException $iOption_ に無効な値を指定した場合。
			@param[in] $iOption_ レポートオプション。
			@return    検索結果データ。
		*/
		private function getReportResult( $iOption_ )
		{
			global $gm;
			global $loginUserType;
			global $loginUserRank;
			global $LOGIN_ID;

			if( !is_array( $iOption_ ) ) //オプションが配列ではない場合
				{ throw new InvalidArgumentException( '引数 $iOption_ は無効です[' . $iOption_ . ']' ); }

			$results = Array();
			$targets = $iOption_[ 'targets' ];

			if( !is_array( $targets ) ) //ターゲットテーブルが配列ではない場合
				{ $targets = Array( $targets ); }

			foreach( $targets as $target => $config ) //全てのターゲットテーブルを処理。
			{
				$db    = GMList::getDB( $target );
				$table = $db->getTable();

				if( isset( $config[ 'searchSystem' ] ) ) //システム検索が設定されている場合
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

				if( isset( $config[ 'ownerColumn' ][ $loginUserType ] ) ) //オーナー条件が設定されている場合
					{ $table = $db->searchTable( $table , $config[ 'ownerColumn' ][ $loginUserType ] , '=' , '%' . $LOGIN_ID . '%' ); }

				if( isset( $config[ 'searchColumn' ] ) ) //検索条件が設定されている場合
				{
					if( !is_array( $config[ 'searchColumn' ] ) ) //配列ではない場合
						{ $searchs = Array( $config[ 'searchColumn' ] ); }
					else //配列の場合
						{ $searchs = $config[ 'searchColumn' ]; }

					foreach( $searchs as $search ) //全ての検索条件を処理
					{
						if( $_POST[ $search ] ) //検索条件が送信されている場合
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
			@brief         検索結果データから次の出力行の内容を取得する。
			@exception     InvalidArgumentException $iResult_ , $iOption_ のいずれかに無効な値を指定した場合。
			@param[in,out] $iResult_ 検索結果データ。
			@param[in]     $iOption_ レポートオプション。
			@retval        出力内容  次の行が取得可能な場合。
			@retval        null      次の行が取得できない場合。
		*/
		private function popLine( &$ioResult_ , $iOption_ )
		{
			List( $currentTable , $currentRow ) = $ioResult_[ 'cursor' ];
			List( $name , $table , $row )       = $ioResult_[ 'tables' ][ $currentTable ];

			while( $row <= $currentRow ) //テーブルの行範囲を超える間繰り返し
			{
				$currentTable += 1;
				$currentRow    = 0;

				if( count( $ioResult_[ 'tables' ] ) <= $currentTable ) //テーブルリストの範囲を超えている場合
					{ return null; }

				List( $name , $table , $row ) = $ioResult_[ 'tables' ][ $currentTable ];
			}

			$db          = GMList::GetDB( $name );
			$rec         = $db->getRecord( $table , $currentRow );
			$currentRow += 1;

			$ioResult_[ 'cursor' ] = Array( $currentTable , $currentRow );

			if( $this->isSkip( $name , $rec , $iOption_ ) ) //スキップ条件に一致する場合
				{ return $this->popLine( $ioResult_ , $iOption_ ); }

			return $this->createLineString( $name , $rec , $iOption_ );
		}

		private function isSkip( $iTableName_ , $iRec_ , $iOption_ )
		{
			if( !$iOption_[ 'targets' ][ $iTableName_ ][ 'skipFilter' ] ) //フィルタが設定されていない場合
				{ return false; }

			switch( $iOption_[ 'targets' ][ $iTableName_ ][ 'skipFilter' ] ) //フィルタ名で分岐
			{
				case 'nUser:adwares'       : //nUserからの広告レポート
				case 'nUser:secretAdwares' : //nUserからのクローズド広告レポート
				{
					$gm     = GMList::GetGM( $iTableName_ );
					$result = $gm->getCCResult( $iRec_ , '<!--# calc (!--# code getSum click_pay cost adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) + (!--# code getSum pay cost adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) + (!--# code getSum continue_pay cost adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) + (!--# code getSum tier cost adwares = (!--# value id #--) owner = (!--# ecode self id #--) regist b (!--# mod report beginTime #--) (!--# mod report endTime #--) #--) #-->' );

					if( !$result ) //成果額が空の場合
						{ return true; }

					break;
				}

				default : //その他
					{ throw new InvalidArgumentException( '引数 $iOption_ は無効です[' . $iOption_[ 'targets' ][ $iTableName_ ][ 'skipFilter' ] . ']' ); }
			}

			return false;
		}

		private function createLineString( $iTableName_ , $iRec_ , $iOption_ )
		{
			$gm      = GMList::GetGM( $iTableName_ );
			$formats = $iOption_[ 'targets' ][ $iTableName_ ][ 'formats' ];
			$line    = Array();

			foreach( $formats as $format ) //全ての出力項目を処理
				{ $line[] = $gm->getCCResult( $iRec_ , $format ); }

			return implode( ',' , $line );
		}

		/**
			@brief     ダウンロード用にHTTPヘッダを出力する。
			@param[in] $iExportName_ ダウンロードファイル名。
		*/
		private function sendDownloadHeader( $iExportName_ )
		{
			if( !$iExportName_ ) //ダウンロード名が空の場合
				{ throw new InvalidArgumentException( '引数 $iExportName_ は無効です' ); }

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
			
			//計算対象の月を指定する。
			if( isset($_POST[ 'regist' ]) && $_POST[ 'regist' ] )
			{
				//計算済みかどうかを確認する
				$table = $db->searchTable( $db->getTable(), 'regist', '=', $_POST[ 'regist' ]);
				
				if( $rec = $db->getFirstRecord($table) ){
					//計算済みであった場合はそのIDをvariableにセットする
					self::$ReportID = $db->getData( $rec, 'id');
					return;
				}
			}
			
			$time = mktime(0, 0, 0, date( 'n'  ) , 1, date( 'Y' ));
			
			$table = $db->searchTable( $db->getTable(), 'type', '=', 'use_fee');
			$table = $db->searchTable( $table, 'regist', '>=', $time);
			
			if( $rec = $db->getFirstRecord($table) ){
				//計算済みであった場合はそのIDをvariableにセットする
				$id = $db->getData( $rec, 'id');
			}else{
				//計算済みでなかった場合は、reportテーブルのカラムを追加し、そのIDをvariableにセットする
				$rec = $db->getNewRecord( Array( 'type'=> 'use_fee','regist'=>time()) );
				$id = SystemUtil::getNewId( $db,'report');
				$db->setData(  $rec, 'id', $id );
				$db->addRecord($rec);
				
				//計算対象になるレコードのカラムにIDをセットしていく。
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
		 * reportテーブルのtype条件に合う物から日付ベースで選択するselectBoxの表示
		 *
		 * @param gm GUIManagerオブジェクトです。
		 * @param rec 登録情報のレコードデータです。
		 * @param args コマンドコメント引数配列です。
		 *
         * 第一引数：type
         * 第二引数：time format
         * 第三引数：未選択項目値(省略可)
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
