<?php

namespace OM4\Zapier\Admin;

use OM4\Zapier\Admin\FeedUI;
use OM4\Zapier\Admin\Pointers;
use OM4\Zapier\Admin\Privacy;
use OM4\Zapier\Admin\SystemStatus;

defined( 'ABSPATH' ) || exit;

/**
 * Administration (dashboard) functionality
 */
class Dashboard {

	/**
	 * Constructor
	 */
	public function __construct() {
		new Pointers();
		new FeedUI();
		new SystemStatus();
		new Privacy();
	}
}
