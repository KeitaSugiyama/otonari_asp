<?php
	
	
/*************************
 ** SQL DATABSE 用 定義 **
 *************************/
 
	$SQL											 = true;					// SQLを用いるかどうかのフラグ
	$SQL_SERVER										 = 'localhost';				// SQLのサーバ
//	$SQL_PORT										 = '5433';

	// SQLデーモンのクラス名
//	$SQL_MASTER										 = 'SQLiteDatabase';
	$SQL_MASTER										 = 'MySQLDatabase';

	$DB_NAME										 = 'affiliate';			// DB名
	$SQL_ID	 										 = 'root';					// 管理ユーザーＩＤ
	$SQL_PASS  										 = '';					// 管理ユーザーＰＡＳＳ

	$TABLE_PREFIX									 = '';
	
	$CONFIG_SQL_FILE_TYPES = Array('image','file');

	$CONFIG_SQL_DATABASE_SESSION = false;

	//the 128 bit key value for crypting
	$CONFIG_SQL_PASSWORD_KEY = 'derhymqadbrheng';
?>
