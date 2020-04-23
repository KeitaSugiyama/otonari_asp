<?php

	include_once "custom/conf.php";
	include_once "custom/extends/logConf.php";

	/***************************************************************************************************<pre>
	 * 
	 * ���O�t�@�C�������o���X�g���[��
	 * 
	 * @author �O�H��q
	 * @version 3.0.0<br/>
	 * 
	 * </pre>
	 ********************************************************************************************************/

	class OutputLog
	{
		var $file;
		var $MAX_LOGFILE_SIZE = 20971520; //20MB | 1024 * 1024 * 20
//		var $MAX_LOGFILE_SIZE = 5242880;  //5MB | 1024 * 1024 * 5
//		var $MAX_LOGFILE_SIZE = 20480;  //20KB | 1024 * 50
		
		/**
		 * �R���X�g���N�^�B
		 * @param $file ���O�������o���t�@�C���ւ̃p�X
		 */
		function __construct($file)
		{
			if( !file_exists( $file ) )	{ throw new InternalErrorException('LOG�t�@�C�����J���܂���B->'. $file); }
			$this->file = $file;
		}
		
		/**
		 * ���O�̏����o���B
		 * @param $str �����o��������
		 */
		function write($str)
		{
			$existsLogFile = file_exists( $this->file );

			$fp = fopen($this->file, 'a');
			
			// �t�@�C�������b�N����Ă��邩�̊m�F
			if(flock($fp, LOCK_EX))
			{
				fwrite($fp, $str. $_SERVER['HTTP_USER_AGENT']. ",". $_SERVER['REMOTE_ADDR']. ",". date("Y_m_d_H_i_s"). "\n");
				flock($fp, LOCK_UN);
			}
			
			fclose($fp);

			if( !$existsLogFile )
				{ chmod( $this->file, 0766 ); }

			//print filesize($this->file)."/".$this->MAX_LOGFILE_SIZE;
			//�t�@�C���T�C�Y���m�F���ő�l�𒴂��Ă���ꍇ�A���l�[������B
			if($this->MAX_LOGFILE_SIZE < filesize($this->file)){
				$new_file = $this->file.date("_Y_m_d_H_i_s");
				if(rename($this->file, $new_file)){
					if(touch($this->file)){
						if(!@chmod($this->file, 0777)){
							//�p�[�~�b�V�����ύX���s
							unlink($this->file);
							rename($new_file, $this->file);
						}else{
							@chmod( $new_file, 0766 );
						}
					}else{
						//�V�K���O�t�@�C���������s
						rename($new_file, $this->file);
					}
				}
			}
		}

		// SQLOutputLog.php ���
		function table_log( $tableName, $action, $message ){
			global $DB_LOG_FILE_PATHS;
			global $LOG_DIRECTORY_PATH;
			global $DB_LOG_ENABLE_FLAGS;
			
			global $DB_LOG_ENABLE_INSERT;
			global $DB_LOG_ENABLE_ADD;
			global $DB_LOG_ENABLE_DELETE;
			global $DB_LOG_ENABLE_RESTORE;
			global $DB_LOG_ENABLE_UPDATE;
			global $DB_LOG_ENABLE_TABLE_UPDATE;
			
			if( isset($DB_LOG_ENABLE_FLAGS[ $tableName ]) && $DB_LOG_ENABLE_FLAGS[ $tableName ] & ${"DB_LOG_ENABLE_".$action} ){
			
				if( isset($DB_LOG_FILE_PATHS[ $tableName ]) ){
					$this->file = $LOG_DIRECTORY_PATH.$DB_LOG_FILE_PATHS[ $tableName ];
				}else{
					$this->file = $LOG_DIRECTORY_PATH.$DB_LOG_FILE_PATHS[ 'all' ];
				}
				
				$this->write($action.','.$tableName.','.$message);
			}
		}
	}

	/********************************************************************************************************/
?>