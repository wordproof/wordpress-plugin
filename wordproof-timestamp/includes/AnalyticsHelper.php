<?php

namespace WordProofTimestampFree\includes;

class AnalyticsHelper
{
  public static $optionTimestampCount = 'wordproof_timestamp_count';
  public static $optionLastTimestamp = 'wordproof_last_timestamp';
  public static $optionSetupStarted = 'wordproof_setup_started';
  public static $optionWalletConnected = 'wordproof_wallet_connected';
  public static $optionNetwork = 'wordproof_network';

  public function __construct()
  {
    add_action('wp_ajax_wordproof_setup_start', array($this, 'handleSetupStart'));
    add_action('wp_ajax_wordproof_setup_start', array($this, 'onWalletConnected'));

    add_action('wordproof_after_saving_timestamp_meta_data', array($this, 'onNewTimestamp'));
    add_action('wordproof_connected_to_wallet', array($this, 'onNewTimestamp'));
  }

  public function onNewTimestamp()
  {
    $count = get_option(self::$optionTimestampCount, 0);
    $newCount = intval($count) + 1;
    update_option(self::$optionTimestampCount, $newCount);
    update_option(self::$optionLastTimestamp, current_time('timestamp'));
  }

  public function onSetupStart()
  {
    check_ajax_referer('wordproof', 'security');
    if (!current_user_can('manage_options')) {
      exit;
    }
    update_option(self::$optionSetupStarted, true);
  }

  public function onWalletConnected()
  {
    check_ajax_referer('wordproof', 'security');
    if (!current_user_can('manage_options')) {
      exit;
    }
    update_option(self::$optionWalletConnected, true);
  }
}
