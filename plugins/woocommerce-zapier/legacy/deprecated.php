<?php
// phpcs:disable Generic.Files.OneObjectStructurePerFile.MultipleFound

use OM4\Zapier\LegacyTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Legacy WC_Zapier
 *
 * @deprecated 1.9.0 Replaced by OM4\Zapier\Plugin
 */
class WC_Zapier {

	use LegacyTrait;

	/**
	 * Name of the new, replacement class
	 *
	 * @var string
	 */
	protected static $new_class = 'OM4\\Zapier\\Plugin';
}

/**
 * Legacy WC_Zapier_Admin
 *
 * @deprecated 1.9.0 Replaced by OM4\Zapier\Admin\Dashboard
 */
class WC_Zapier_Admin {

	use LegacyTrait;

	/**
	 * Name of the new, replacement class
	 *
	 * @var string
	 */
	protected static $new_class = 'OM4\\Zapier\\Admin\\Dashboard';
}

/**
 * Legacy WC_Zapier_Admin_Feed_UI
 *
 * @deprecated 1.9.0 Replaced by OM4\Zapier\Admin\FeedUI
 */
class WC_Zapier_Admin_Feed_UI {

	use LegacyTrait;

	/**
	 * Name of the new, replacement class
	 *
	 * @var string
	 */
	protected static $new_class = 'OM4\\Zapier\\Admin\\FeedUI';
}

/**
 * Legacy WC_Zapier_Admin_Pointers
 *
 * @deprecated 1.9.0 Replaced by OM4\Zapier\Admin\Pointers
 */
class WC_Zapier_Admin_Pointers {

	use LegacyTrait;

	/**
	 * Name of the new, replacement class
	 *
	 * @var string
	 */
	protected static $new_class = 'OM4\\Zapier\\Admin\\Pointers';
}

/**
 * Legacy WC_Zapier_Admin_System_Status
 *
 * @deprecated 1.9.0 Replaced by OM4\Zapier\Admin\SystemStatus
 */
class WC_Zapier_Admin_System_Status {

	use LegacyTrait;

	/**
	 * Name of the new, replacement class
	 *
	 * @var string
	 */
	protected static $new_class = 'OM4\\Zapier\\Admin\\SystemStatus';
}

/**
 * Legacy WC_Zapier_Checkout_Field_Editor
 *
 * @deprecated 1.9.0 Replaced by OM4\Zapier\Plugin\CheckoutFieldEditor
 */
class WC_Zapier_Checkout_Field_Editor {

	use LegacyTrait;

	/**
	 * Name of the new, replacement class
	 *
	 * @var string
	 */
	protected static $new_class = 'OM4\\Zapier\\Plugin\\CheckoutFieldEditor';
}

/**
 * Legacy WC_Zapier_Feed
 *
 * @deprecated 1.9.0 Replaced by OM4\Zapier\Feed\Feed
 */
class WC_Zapier_Feed {

	use LegacyTrait;

	/**
	 * Name of the new, replacement class
	 *
	 * @var string
	 */
	protected static $new_class = 'OM4\\Zapier\\Feed\\Feed';
}

/**
 * Legacy WC_Zapier_Feed_Factory
 *
 * @deprecated 1.9.0 Replaced by OM4\Zapier\Feed\FeedFactory
 */
class WC_Zapier_Feed_Factory {

	use LegacyTrait;

	/**
	 * Name of the new, replacement class
	 *
	 * @var string
	 */
	protected static $new_class = 'OM4\\Zapier\\Feed\\FeedFactory';
}

/**
 * Legacy WC_Zapier_Send_Queue
 *
 * @deprecated 1.9.0 Replaced by OM4\Zapier\SendQueue
 */
class WC_Zapier_Send_Queue {

	use LegacyTrait;

	/**
	 * Name of the new, replacement class
	 *
	 * @var string
	 */
	protected static $new_class = 'OM4\\Zapier\\SendQueue';
}

/**
 * Legacy WC_Zapier_Subscriptions
 *
 * @deprecated 1.9.0 Replaced by OM4\Zapier\Plugin\Subscriptions
 */
class WC_Zapier_Subscriptions {

	use LegacyTrait;

	/**
	 * Name of the new, replacement class
	 *
	 * @var string
	 */
	protected static $new_class = 'OM4\\Zapier\\Plugin\\Subscriptions';
}

/**
 * Legacy WC_Zapier_Trigger_Factory
 *
 * @deprecated 1.9.0 Replaced by OM4\Zapier\Trigger\TriggerFactory
 */
class WC_Zapier_Trigger_Factory {

	use LegacyTrait;

	/**
	 * Name of the new, replacement class
	 *
	 * @var string
	 */
	protected static $new_class = 'OM4\\Zapier\\Trigger\\TriggerFactory';
}

/**
 * Legacy WC_Zapier_Trigger_New_Customer
 *
 * @deprecated 1.9.0 Replaced by OM4\Zapier\Trigger\Customer\NewCustomer
 */
class WC_Zapier_Trigger_New_Customer {

	use LegacyTrait;

	/**
	 * Name of the new, replacement class
	 *
	 * @var string
	 */
	protected static $new_class = 'OM4\\Zapier\\Trigger\\Customer\\NewCustomer';
}

/**
 * Legacy WC_Zapier_Trigger_Order
 *
 * @deprecated 1.9.0 Replaced by OM4\Zapier\Trigger\Order\Base
 */
class WC_Zapier_Trigger_Order {

	use LegacyTrait;

	/**
	 * Name of the new, replacement class
	 *
	 * @var string
	 */
	protected static $new_class = 'OM4\\Zapier\\Trigger\\Order\\Base';
}

/**
 * Legacy WC_Zapier_Trigger_Order_New
 *
 * @deprecated 1.9.0 Replaced by OM4\Zapier\Trigger\Order\NewOrder
 */
class WC_Zapier_Trigger_Order_New {

	use LegacyTrait;

	/**
	 * Name of the new, replacement class
	 *
	 * @var string
	 */
	protected static $new_class = 'OM4\\Zapier\\Trigger\\Order\\NewOrder';
}

/**
 * Legacy WC_Zapier_Trigger_Order_Status_Change
 *
 * @deprecated 1.9.0 Replaced by OM4\Zapier\Trigger\Order\StatusChange
 */
class WC_Zapier_Trigger_Order_Status_Change {

	use LegacyTrait;

	/**
	 * Name of the new, replacement class
	 *
	 * @var string
	 */
	protected static $new_class = 'OM4\\Zapier\\Trigger\\Order\\StatusChange';
}

/**
 * Legacy WC_Zapier_Trigger_Subscription
 *
 * @deprecated 1.9.0 Replaced by OM4\Zapier\Trigger\Subscription\Base
 */
class WC_Zapier_Trigger_Subscription {

	use LegacyTrait;

	/**
	 * Name of the new, replacement class
	 *
	 * @var string
	 */
	protected static $new_class = 'OM4\\Zapier\\Trigger\\Subscription\\Base';
}

/**
 * Legacy WC_Zapier_Trigger_Subscription_New
 *
 * @deprecated 1.9.0 Replaced by OM4\Zapier\Trigger\Subscription\NewSubscription
 */
class WC_Zapier_Trigger_Subscription_New {

	use LegacyTrait;

	/**
	 * Name of the new, replacement class
	 *
	 * @var string
	 */
	protected static $new_class = 'OM4\\Zapier\\Trigger\\Subscription\\NewSubscription';
}

/**
 * Legacy WC_Zapier_Trigger_Subscription_Renewal
 *
 * @deprecated 1.9.0 Replaced by OM4\Zapier\Trigger\Subscription\Renewal
 */
class WC_Zapier_Trigger_Subscription_Renewal {

	use LegacyTrait;

	/**
	 * Name of the new, replacement class
	 *
	 * @var string
	 */
	protected static $new_class = 'OM4\\Zapier\\Trigger\\Subscription\\Renewal';
}

/**
 * Legacy WC_Zapier_Trigger_Subscription_Renewal_Failed
 *
 * @deprecated 1.9.0 Replaced by OM4\Zapier\Trigger\Subscription\RenewalFailed
 */
class WC_Zapier_Trigger_Subscription_Renewal_Failed {

	use LegacyTrait;

	/**
	 * Name of the new, replacement class
	 *
	 * @var string
	 */
	protected static $new_class = 'OM4\\Zapier\\Trigger\\Subscription\\RenewalFailed';
}

/**
 * Legacy WC_Zapier_Trigger_Subscription_Status_Change
 *
 * @deprecated 1.9.0 Replaced by OM4\Zapier\Trigger\Subscription\StatusChange
 */
class WC_Zapier_Trigger_Subscription_Status_Change {

	use LegacyTrait;

	/**
	 * Name of the new, replacement class
	 *
	 * @var string
	 */
	protected static $new_class = 'OM4\\Zapier\\Trigger\\Subscription\StatusChange';
}
