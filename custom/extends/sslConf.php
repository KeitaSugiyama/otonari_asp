<?php
	include_once 'include/extends/SSLUtil.php';

	$CONFIG_SSL_ENABLE = false; //SSL�ւ̃��_�C���N�g��L���ɂ���ꍇ��true
	$CONFIG_SSL_MOBILE = false; //�g�ѓd�b�ł�SSL���d�l����ꍇ��true
    $CONFIG_SSL_CHECK_CONTROLLER_NAME = false;


	$CONFIG_SSL_ON_CHECK_USERS = Array( //SSL���펞�L���ɂ��郆�[�U�[
			'nUser' , 'cUser', 'admin'
	);

    $CONFIG_SSL_ON_CHECK_CONTROLLER_NAME = Array( //SSL���펞�L���ɂ���R���g���[��
    );

    $CONFIG_SSL_OUT_CHECK_CONTROLLER_NAME = Array( //SSL���펞�����ɂ���R���g���[��
    );

	$CONFIG_SSL_ON_CHECK_FILES = Array(
			'index.php', 'regist.php', 'edit.php', 'cart.php', 'login.php', 'reminder.php', 'link.php', 'add.php',
			'activate.php', 'info.php', 'other.php', 'page.php', 'report.php', 'search.php'
	);
	
	$CONFIG_SSL_OUT_CHECK_FILES = Array(
	);
