<?php

// Exit if accessed directly

if ( !defined( 'ABSPATH' ) ) exit;

do_action('nnr_data_man_before_autoload');

require_once('model/db.php');
require_once('controllers/table.php');
require_once('controllers/settings.php');

do_action('nnr_data_man_after_autoload');