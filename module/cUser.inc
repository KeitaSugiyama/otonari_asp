<?php

/**********          cUser‚Ì’è‹`          **********/

	$EDIT_TYPE                            = 'cUser';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = true;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = false;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = 'mail';
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = 'pass';
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = 'pass_confirm';
	$LST[ $EDIT_TYPE ]                    = 'cuser.csv';
	$TDB[ $EDIT_TYPE ]                    = 'cuser.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = 'C';
	$ID_LENGTH[ $EDIT_TYPE ]              = 8;

class mod_cUser
{}
