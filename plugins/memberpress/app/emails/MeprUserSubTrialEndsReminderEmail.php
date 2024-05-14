<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MeprUserSubTrialEndsReminderEmail extends MeprBaseReminderEmail {
  /** Set the default enabled, title, subject & body */
  public function set_defaults($args=array()) {
    $this->title = __('Subscription Trial Ending Reminder Email to User','memberpress');
    $this->description = __('This email is sent to the user when triggered.', 'memberpress');
    $this->ui_order = 0;

    $enabled = $use_template = $this->show_form = true;
    $subject = __('Your Subscription Trial Period is Ending Soon', 'memberpress');
    $body = $this->body_partial();

    $this->defaults = compact( 'enabled', 'subject', 'body', 'use_template' );
    $this->variables = array_unique(
      array_merge(
        MeprRemindersHelper::get_email_vars(),
        MeprTransactionsHelper::get_email_vars()
      )
    );

    $this->test_vars = array(
       'reminder_id'               => 28,
       'reminder_trigger_length'   => 2,
       'reminder_trigger_interval' => 'days',
       'reminder_trigger_timing'   => 'before',
       'reminder_trigger_event'    => 'sub-trial-ends',
       'reminder_name'             => __('Subscription Trial Ending', 'memberpress'),
       'reminder_description'      => __('Subscription Trial Ending in 2 days', 'memberpress'),
    );
  }
}

