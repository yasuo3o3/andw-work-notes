<?php
/**
 * Plugin Name:       andW Work Notes
 * Description:       クライアント指示や更新作業のメモをWP内で記録。投稿や固定ページに紐づけ、一覧管理できます。依頼元/担当者のマスター管理＆セレクト、管理画面の「作業一覧」付き。
 * Version:           1.0.7
 * Author:            yasuo3o3
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

define( 'ANDW_VER', '1.0.7' );
define( 'ANDW_DIR', plugin_dir_path( __FILE__ ) );
define( 'ANDW_URL', plugin_dir_url( __FILE__ ) );

/**
 * デバッグログ出力ヘルパー関数（最小＋マスク方針）.
 * 管理者かつ WP_DEBUG 時のみ、ホワイトリストキー＋機微情報マスキング.
 *
 * @param string $label   ログのラベル.
 * @param array  $data    ログ対象データ（オプション）.
 * @return void
 */
function andw_log( $label, array $data = array() ) {
	if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$whitelist = array( 'title', 'status', 'id', 'post_id', 'requester', 'worker', 'target_type' );
	$masked    = array();

	foreach ( $data as $k => $v ) {
		if ( ! in_array( $k, $whitelist, true ) ) {
			continue;
		}
		// 文字列が長すぎる場合は切り詰める
		$masked[ $k ] = is_string( $v ) && strlen( $v ) > 64 ? substr( $v, 0, 64 ) . '…' : $v;
	}

	$log_message = '[ANDW] ' . $label;
	if ( ! empty( $masked ) ) {
		$log_message .= ' ' . wp_json_encode( $masked, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );
	}

	// デバッグフック経由で処理（本番コードからerror_log削除）
	do_action( 'andw_debug_log', $log_message );
}

// WP_List_Table の直接読み込みは WordPress.org 審査で問題となるため削除
// 標準CPT一覧機能を使用するように変更

// クラス読み込み.
require_once ANDW_DIR . 'includes/class-andw-work-notes.php';
// class-andw-list-table.php は WordPress.org 審査対応のため削除（標準CPT一覧を使用）
// 自動アップデータはWP.org配布では同梱しない（.gitattributesでexport-ignore）。
// require_once ANDW_DIR . 'includes/class-andw-updater.php';.

// テキストドメイン読み込み（WP4.6以降 WordPress.org 配布では自動ロードされるため削除）
// Text Domain ヘッダと Domain Path のみで自動処理される

// activate/deactivate フック.
register_activation_hook( __FILE__, 'andw_work_notes_activate' );
register_deactivation_hook( __FILE__, 'andw_work_notes_deactivate' );

/**
 * プラグイン有効化時の処理.
 *
 * @return void
 */
function andw_work_notes_activate() {
	// 移行フラグ（未実行なら1）
	if ( ! get_option( 'andw_migration_pending' ) ) {
		update_option( 'andw_migration_pending', 1 );
	}
	flush_rewrite_rules();
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

// 既存データ移行（1回のみ実行）
add_action( 'admin_init', 'andw_run_migration_once' );

// デバッグログフック（WP_DEBUG時のみ有効）
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
	add_action( 'andw_debug_log', function( $message ) {
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			if ( function_exists( 'wp_debug_log' ) ) {
				wp_debug_log( $message );
			}
		}
	} );
}

/**
 * 既存データ移行処理（1回のみ実行）
 *
 * @return void
 */
function andw_run_migration_once() {
	if ( ! is_admin() ) {
		return;
	}
	if ( ! get_option( 'andw_migration_pending' ) ) {
		return;
	}

	// 旧CPT → 新CPT
	$old_posts = get_posts(
		array(
			'post_type'   => 'of_work_note',
			'numberposts' => -1,
			'post_status' => 'any',
			'fields'      => 'ids',
		)
	);
	foreach ( $old_posts as $pid ) {
		wp_update_post(
			array(
				'ID'        => $pid,
				'post_type' => 'andw_work_note',
			)
		);
	}

	// メタ・オプションのキー移行
	$key_map = array(
		'ofwn_requester'     => 'andw_requester',
		'ofwn_status'        => 'andw_status',
		'ofwn_worker'        => 'andw_worker',
		'ofwn_work_date'     => 'andw_work_date',
		'ofwn_target_type'   => 'andw_target_type',
		'ofwn_target_id'     => 'andw_target_id',
		'of_requester'       => 'andw_requester',
		'of_status'          => 'andw_status',
		'of_worker'          => 'andw_worker',
		'of_work_date'       => 'andw_work_date',
		'of_target_type'     => 'andw_target_type',
		'of_target_id'       => 'andw_target_id',
		'_ofwn_work_title'   => '_andw_work_title',
		'_ofwn_work_content' => '_andw_work_content',
		'_of_work_title'     => '_andw_work_title',
		'_of_work_content'   => '_andw_work_content',
	);

	// 全投稿のメタデータ移行
	$all_posts = get_posts(
		array(
			'post_type'   => array( 'andw_work_note', 'of_work_note' ),
			'numberposts' => -1,
			'post_status' => 'any',
			'fields'      => 'ids',
		)
	);

	foreach ( $all_posts as $pid ) {
		foreach ( $key_map as $old => $new ) {
			$val = get_post_meta( $pid, $old, true );
			if ( $val !== '' && $val !== null ) {
				update_post_meta( $pid, $new, $val );
				delete_post_meta( $pid, $old );
			}
		}
	}

	// オプション移行
	$opt_map = array(
		'ofwn_settings'   => 'andw_settings',
		'ofwn_requesters' => 'andw_requesters',
		'ofwn_workers'    => 'andw_workers',
		'of_settings'     => 'andw_settings',
		'of_requesters'   => 'andw_requesters',
		'of_workers'      => 'andw_workers',
	);
	foreach ( $opt_map as $old => $new ) {
		$val = get_option( $old, null );
		if ( $val !== null ) {
			update_option( $new, $val );
			delete_option( $old );
		}
	}

	// 移行完了フラグを設定
	update_option( 'andw_migration_pending', 0 );
	flush_rewrite_rules();

	// デバッグログ
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG && current_user_can( 'manage_options' ) ) {
		andw_log( 'Data migration completed successfully' );
	}
}
