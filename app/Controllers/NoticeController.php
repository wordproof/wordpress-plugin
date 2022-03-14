<?php

namespace WordProofTimestamp\App\Controllers;

use WordProof\SDK\Helpers\RedirectHelper;
use WordProof\SDK\Helpers\TransientHelper;
use WordProofTimestamp\App\Notices\AuthenticateAfterMigrationNotice;
use WordProofTimestamp\App\Notices\YoastNotice;

class NoticeController {

	private $notices = [];
	private $keys = [];

	public function __construct() {
		$this->initializeNotices();
		$this->getKeys();

		add_action( 'wp_ajax_wordproof_dismiss_notice', [ $this, 'dismissNotice' ] );
	}

	public function initializeNotices() {
		$this->notices[] = new YoastNotice();
		$this->notices[] = new AuthenticateAfterMigrationNotice();

		if ( TransientHelper::getOnce( 'wordproof_upgraded' ) ) {
			RedirectHelper::safe( admin_url( 'admin.php?page=wordproof-about' ) );
		}
	}

	public function getKeys() {
		foreach ( $this->notices as $notice ) {
			$this->keys[] = $notice->getKey();
		}
	}

	public function dismissNotice() {
		check_ajax_referer( 'wordproof', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! isset( $_REQUEST['notice_key'] ) ) {
			return;
		}

		if ( in_array( wp_unslash( $_REQUEST['notice_key'] ), $this->keys ) ) {
			set_transient( filter_var( wp_unslash( $_REQUEST['notice_key'] ), FILTER_SANITIZE_STRING ), 'hidden' );
		}

	}
}
