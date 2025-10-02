<?php
/**
 * Plugin Name:       andW Work Notes
 * Description:       クライアント指示や更新作業のメモをWP内で記録。投稿や固定ページに紐づけ、一覧管理できます。依頼元/担当者のマスター管理＆セレクト、管理画面の「作業一覧」付き。
 * Version:           1.0.6
 * Author:            Netservice
 * Author URI:        https://netservice.jp/
 * License:           GPL-2.0-or-later
 * Text Domain:       andw-work-notes
 * Domain Path:       /languages
 *
 * @package andw-work-notes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ANDW_VER', '1.0.6' );
define( 'ANDW_DIR', plugin_dir_path( __FILE__ ) );
define( 'ANDW_URL', plugin_dir_url( __FILE__ ) );

/**
 * デバッグログ出力ヘルパー関数.
 * 本番環境での error_log() 呼び出しを避ける.
 *
 * @param string $message Message text.
 * @return void
 */
function andw_log( $message ) {
	if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
		// WP_DEBUG_LOG が有効なら専用ログファイルに出力.
		if ( function_exists( 'wp_debug_log' ) ) {
			wp_debug_log( '[ANDW] ' . $message );
		}
	}
}

// 管理画面でのみ WP_List_Table を必要時に読み込み.
if ( is_admin() && ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

// クラス読み込み.
require_once ANDW_DIR . 'includes/class-andw-work-notes.php';
require_once ANDW_DIR . 'includes/class-andw-list-table.php';
// 自動アップデータはWP.org配布では同梱しない（.gitattributesでexport-ignore）。
// require_once ANDW_DIR . 'includes/class-andw-updater.php';.

// テキストドメイン読み込み（WP4.6+ではWP.org配布で自動ロード。Plugin Check対応のため削除）。
// add_action(
// 'plugins_loaded',
// function () {
// load_plugin_textdomain( 'andw-work-notes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
// }
// );.

// activate/deactivate フック.
register_activation_hook( __FILE__, 'andw_work_notes_activate' );
register_deactivation_hook( __FILE__, 'andw_work_notes_deactivate' );

/**
 * プラグイン有効化時の処理.
 *
 * @return void
 */
function andw_work_notes_activate() {
	// CPT登録のみで独自リライトルールは使用しないため flush_rewrite_rules() は不要.
	// 将来的に独自URLパターンが必要になった場合のみ追加する.
}

/**
 * プラグイン無効化時の処理.
 *
 * @return void
 */
function andw_work_notes_deactivate() {
	// CPTのみなので特別な処理は不要.
	// 将来的にcronやキャッシュを使用する場合はここで停止/削除を行う.
}

// 起動.
add_action(
	'plugins_loaded',
	function () {
		new ANDW_Work_Notes();

		// アップデートチェッカー初期化（配布物では除外）.
		// if ( is_admin() && class_exists( 'ANDW_Updater' ) ) {
		// new ANDW_Updater( __FILE__, ANDW_VER );
		// .
	}
);
