<?php

namespace WordProofTimestampFree;

use WordProofTimestampFree\includes\AnalyticsHelper;
use WordProofTimestampFree\includes\ChainHelper;
use WordProofTimestampFree\includes\MetaBox;
use WordProofTimestampFree\includes\NotificationHelper;
use WordProofTimestampFree\includes\Page\SettingsPage;
use WordProofTimestampFree\includes\AdminAjaxHelper;
use WordProofTimestampFree\includes\CertificateHelper;
use WordProofTimestampFree\includes\PostMetaHelper;
use WordProofTimestampFree\includes\TimestampAjaxHelper;

/**
 * Class WordProofTimestampFree
 * @package WordProofTimestampFree
 */
class WordProofTimestampFree
{

  /** @var null */
  private static $instance = null;

  public function init()
  {
    /**
     * Bootstrap
     */
    if (is_admin()) {
      new SettingsPage();
      new MetaBox();
      new NotificationHelper();
      new ChainHelper();
    }
    new AnalyticsHelper();
    new AdminAjaxHelper();
    new TimestampAjaxHelper();

    /**
     * Actions
     */
    add_filter('manage_posts_columns', array($this, 'addColumn'));
    add_action('manage_posts_custom_column', array($this, 'addColumnContent'), 10, 2);
    add_filter('the_content', array($this, 'addProofLink'), 999, 1);
    add_action('admin_enqueue_scripts', array($this, 'loadAdminAssets'), 999);
    add_action('wp_footer', array($this, 'addProofPopupHtml'), 10);
    add_action('wp_enqueue_scripts', array($this, 'addProofPopupScripts'), 999);
  }

  public function addColumn($defaults)
  {
    $defaults['wordproof'] = 'WordProof';
    return $defaults;
  }

  public function addColumnContent($column_name)
  {
    global $post;
    if ($column_name == 'wordproof') {
      $meta = PostMetaHelper::getPopupMeta($post);

      if (isset($meta['wordproof_date'])) {
        if ($meta['wordproof_date'] === get_the_modified_date('Y-m-d H:i:s', $post->ID)) {
          echo '<a target="_blank" href="' . get_permalink($post->ID) . '#wordproof">Stamped</a>';
        } else {
          echo '<a target="_blank" href="' . get_permalink($post->ID) . '#wordproof">Outdated</a>';
        }
      } else {
        echo '—';
      }

    }
  }

  public function addProofLink($content)
  {
    global $post;

    if (!empty($post)) {
      $meta = PostMetaHelper::getPopupMeta($post);

      if (isset($meta['wordproof_date'])) {
        $content .= CertificateHelper::getCertificateHtml($post->ID);
      }
    }

    return $content;
  }

  public function addProofPopupHtml()
  {
    global $post;

    if (!empty($post)) {
      $meta = PostMetaHelper::getPopupMeta($post);

      if (isset($meta['wordproof_date'])) {
        echo '<div id="wordproof-popup-container"></div>';
      }
    }
  }

  public function addProofPopupScripts()
  {
    global $post;
    wp_enqueue_script('wordproof.frontend.js', WORDPROOF_URI_JS . '/frontend.js', array(), filemtime(WORDPROOF_DIR_JS . '/frontend.js'), true);

    $timestampPostMeta = PostMetaHelper::getPopupMeta($post);

    wp_localize_script('wordproof.frontend.js', 'wordproofData', array(
      'timestampMeta' => $timestampPostMeta,
      'wordProofCssDir' => WORDPROOF_URI_CSS,
      'pluginDirUrl' => WORDPROOF_URI
    ));
  }

  public function loadAdminAssets()
  {
    global $post;
    wp_enqueue_style('wordproof.admin.css', WORDPROOF_URI_CSS . '/admin.css', array(), filemtime(WORDPROOF_DIR_CSS . '/admin.css'));

    wp_enqueue_script('wordproof.admin.js', WORDPROOF_URI_JS . '/admin.js', array(), filemtime(WORDPROOF_DIR_JS . '/admin.js'), true);
    wp_localize_script('wordproof.admin.js', 'wordproofData', array(
      'ajaxURL' => admin_url('admin-ajax.php'),
      'settingsURL' => admin_url('admin.php?page=wordproof'),
      'ajaxSecurity' => wp_create_nonce('wordproof'),
      'postId' => (!empty($post->ID)) ? $post->ID : false,
      'network' => get_option('wordproof_network', false),
      'storeContent' => get_option('wordproof_store_content', false),
      'accountName' => get_option('wordproof_accountname', ''),
      'wordBalance' => get_option('wordproof_balance', 0),
      'pluginDirUrl' => plugin_dir_url(__FILE__)
    ));
  }

  /**
   * @return null|WordProof
   */
  public static function getInstance()
  {
    if (!isset(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }
}