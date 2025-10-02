<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * andw-work-notes 統合設定管理クラス
 *
 * マスター管理とプラグイン設定を統合管理
 */
class ANDW_Worklog_Settings {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_master_settings' ) );

		// AJAX ハンドラー追加
		add_action( 'wp_ajax_andw_clear_cache', array( $this, 'ajax_clear_cache' ) );

		// マイグレーション処理（通知機能削除）
		add_action( 'plugins_loaded', array( $this, 'run_migration' ), 1 );
	}

	/**
	 * オプションデータを配列形式に正規化
	 * 移行時にシリアライズされた文字列になっている場合があるため
	 */
	private function normalize_option_data( $option_name, $default = array() ) {
		$data = get_option( $option_name, $default );

		// 文字列の場合（シリアライズされている可能性）
		if ( is_string( $data ) ) {
			// シリアライズされたデータの場合
			if ( is_serialized( $data ) ) {
				$unserialized = unserialize( $data );
				return is_array( $unserialized ) ? $unserialized : $default;
			}
			// 通常の文字列の場合（改行区切りとして扱う）
			return array_filter( array_map( 'trim', explode( "\n", $data ) ) );
		}

		// 既に配列の場合
		if ( is_array( $data ) ) {
			return $data;
		}

		return $default;
	}

	/**
	 * 設定ページを追加
	 */
	public function add_settings_page() {
		add_submenu_page(
			'edit.php?post_type=andw_work_note',
			__( '作業メモ設定', 'andw-work-notes' ),
			__( '作業メモ設定', 'andw-work-notes' ),
			'manage_options',
			'andw-worklog-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * マスター設定項目を統合登録
	 */
	public function register_master_settings() {
		register_setting(
			'andw_settings',
			'andw_requesters',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_list' ),
				'default'           => array(),
				'show_in_rest'      => false,
				'autoload'          => false,
			)
		);
		register_setting(
			'andw_settings',
			'andw_workers',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_list' ),
				'default'           => $this->default_workers(),
				'show_in_rest'      => false,
				'autoload'          => false,
			)
		);

		add_settings_section( 'andw_section_main', __( 'マスター管理', 'andw-work-notes' ), '__return_false', 'andw_settings' );

		add_settings_field(
			'andw_requesters',
			__( '依頼元マスター（1行1件）', 'andw-work-notes' ),
			function () {
				$v = $this->normalize_option_data( 'andw_requesters', array() );
				echo '<textarea name="andw_requesters[]" rows="3" style="width:600px;">' . esc_textarea( implode( "\n", $v ) ) . '</textarea>';
				echo '<p class="description">' . esc_html__( 'ここに入力した内容が「依頼元」のセレクトに表示されます。', 'andw-work-notes' ) . '</p>';
			},
			'andw_settings',
			'andw_section_main'
		);

		add_settings_field(
			'andw_workers',
			__( '担当者マスター（1行1件）', 'andw-work-notes' ),
			function () {
				$v = $this->normalize_option_data( 'andw_workers', $this->default_workers() );
				echo '<textarea name="andw_workers[]" rows="3" style="width:600px;">' . esc_textarea( implode( "\n", $v ) ) . '</textarea>';
				echo '<p class="description">' . esc_html__( 'ここに入力した内容が「担当者」のセレクトに表示されます。', 'andw-work-notes' ) . '</p>';
			},
			'andw_settings',
			'andw_section_main'
		);
	}

	/**
	 * リストデータのサニタイズ処理
	 */
	public function sanitize_list( $raw ) {
		if ( is_array( $raw ) && count( $raw ) === 1 && is_string( $raw[0] ) ) {
			$raw = $raw[0];
		}
		$text  = is_array( $raw ) ? implode( "\n", $raw ) : (string) $raw;
		$lines = array_filter(
			array_map(
				function ( $s ) {
					$s = trim( str_replace( array( "\r\n", "\r" ), "\n", $s ) );
					return $s;
				},
				explode( "\n", $text )
			)
		);
		$lines = array_values( array_unique( $lines ) );
		return $lines;
	}

	/**
	 * デフォルト担当者リスト取得
	 */
	private function default_workers() {
		$roles = array( 'administrator', 'editor', 'author' );
		$users = get_users(
			array(
				'role__in' => $roles,
				'fields'   => array( 'display_name' ),
			)
		);
		$names = array_map(
			function ( $u ) {
				return $u->display_name;
			},
			$users
		);
		$names = array_filter( array_unique( $names ) );
		if ( empty( $names ) ) {
			$names = array( wp_get_current_user()->display_name ?: '担当者A' );
		}
		return array_values( $names );
	}

	/**
	 * 設定ページをレンダリング
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'この設定を変更する権限がありません。', 'andw-work-notes' ) );
		}

		?>
		<div class="wrap">
			<h1><?php esc_html_e( '作業メモ設定', 'andw-work-notes' ); ?></h1>
			
			<!-- マスター管理セクション -->
			<form method="post" action="options.php">
				<?php
				settings_fields( 'andw_settings' );
				do_settings_sections( 'andw_settings' );
				submit_button( esc_html__( 'マスター設定を保存', 'andw-work-notes' ) );
				?>
			</form>
			
			<?php if ( defined( 'WP_DEBUG' ) && WP_DEBUG && current_user_can( 'manage_options' ) ) : ?>
			<hr>

			<h2><?php esc_html_e( 'キャッシュクリア', 'andw-work-notes' ); ?></h2>
			<p><?php esc_html_e( '作業メモの保存に問題がある場合、キャッシュをクリアして改善する可能性があります。', 'andw-work-notes' ); ?></p>

			<div id="andw-cache-clear">
				<button type="button" id="andw-clear-cache-btn" class="button button-secondary">
					<?php esc_html_e( 'キャッシュをクリア', 'andw-work-notes' ); ?>
				</button>
				<div id="andw-cache-result" style="margin-top: 10px;"></div>
			</div>
			<?php endif; ?>
			
			
			<?php
			// 直書きスクリプトを削除し、適切な enqueue に置き換え
			// admin_enqueue_scripts で管理画面用 JavaScript をロード
			?>
		</div>
		<?php
	}

	/**
	 * キャッシュクリアのAJAXハンドラー
	 */
	public function ajax_clear_cache() {
		// ノンス検証
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'andw_clear_cache' ) ) {
			wp_send_json_error( array( 'message' => __( 'セキュリティチェックに失敗しました。', 'andw-work-notes' ) ) );
		}

		// 権限チェック
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'この操作を実行する権限がありません。', 'andw-work-notes' ) ) );
		}

		try {
			$cleared_items = array();

			// WordPressオブジェクトキャッシュクリア
			if ( wp_cache_flush() ) {
				$cleared_items[] = 'WordPressオブジェクトキャッシュ';
			}

			// WordPress投稿キャッシュクリア
			$this->clear_posts_cache();
			$cleared_items[] = '投稿メタデータキャッシュ';

			// トランジェントAPIクリア
			$this->clear_work_notes_transients();
			$cleared_items[] = 'work-notes関連トランジェント';

			// OPcacheクリア（利用可能な場合）
			if ( function_exists( 'opcache_reset' ) && opcache_reset() ) {
				$cleared_items[] = 'OPcache';
			}

			$message = 'キャッシュクリア完了: ' . implode( '、', $cleared_items );

			if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
				andw_log( 'CACHE_CLEAR ' . $message );
			}

			wp_send_json_success( array( 'message' => $message ) );

		} catch ( Exception $e ) {
			if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
				andw_log( 'CACHE_CLEAR Error: ' . $e->getMessage() );
			}
			/* translators: %1$s: PHP exception message */
			wp_send_json_error( array( 'message' => sprintf( __( 'キャッシュクリア中にエラーが発生しました: %1$s', 'andw-work-notes' ), esc_html( $e->getMessage() ) ) ) );
		}
	}

	/**
	 * work-notes関連の投稿キャッシュをクリア
	 */
	private function clear_posts_cache() {
		global $wpdb;

		// work-notes関連の投稿IDを取得
		$args      = array( 'post', 'page', 'andw_work_note' );
		$cache_key = 'andw:' . md5( serialize( $args ) . ':post_ids' );
		if ( false !== ( $cached = wp_cache_get( $cache_key, 'andw' ) ) ) {
			$post_ids = $cached;
		} else {
			$placeholders = implode( ',', array_fill( 0, count( $args ), '%s' ) );
			$q            = new WP_Query(
				array(
					'post_type'     => $args,
					'fields'        => 'ids',
					'nopaging'      => true,
					'no_found_rows' => true,
				// 'suppress_filters' => true, // VIP: 禁止のため無効化
				)
			);
			$post_ids = $q->posts;
			wp_cache_set( $cache_key, $post_ids, 'andw', 300 );
		}

		// 各投稿のキャッシュをクリア
		foreach ( $post_ids as $post_id ) {
			clean_post_cache( $post_id );
			wp_cache_delete( $post_id, 'post_meta' );
		}
	}

	/**
	 * work-notes関連のトランジェントをクリア
	 */
	private function clear_work_notes_transients() {
		global $wpdb;

		// work-notes関連のトランジェントキーを検索して削除
		$args      = array( '_transient_andw_%', '_transient_timeout_andw_%', '_transient_work_notes_%' );
		$cache_key = 'andw:' . md5( serialize( $args ) . ':transient_keys' );
		if ( false !== ( $cached = wp_cache_get( $cache_key, 'andw' ) ) ) {
			$transient_keys = $cached;
		} else {
			$all_options    = function_exists( 'wp_load_alloptions' ) ? wp_load_alloptions() : array();
			$transient_keys = array();
			foreach ( array_keys( $all_options ) as $option_name ) {
				if ( strpos( $option_name, '_transient_andw_' ) === 0 ||
					strpos( $option_name, '_transient_timeout_andw_' ) === 0 ||
					strpos( $option_name, '_transient_work_notes_' ) === 0 ) {
					$transient_keys[] = $option_name;
				}
			}
			wp_cache_set( $cache_key, $transient_keys, 'andw', 300 );
		}

		foreach ( $transient_keys as $key ) {
			if ( strpos( $key, '_transient_timeout_' ) === 0 ) {
				// タイムアウト用のキーは削除をスキップ（deleteTransientで自動処理）
				continue;
			}

			$transient_name = str_replace( array( '_transient_', '_transient_timeout_' ), '', $key );
			delete_transient( $transient_name );
		}
	}

	/**
	 * 通知機能削除のマイグレーション処理
	 */
	public function run_migration() {
		$migrated_version = get_option( 'andw_migrated_version', '0.0.0' );
		$current_version  = defined( 'ANDW_VER' ) ? ANDW_VER : '1.0.4';

		// 通知機能削除マイグレーション（バージョン1.0.3以降）
		if ( version_compare( $migrated_version, '1.0.3', '<' ) ) {
			$this->cleanup_worklog_notice_data();
			update_option( 'andw_migrated_version', $current_version );
		}
	}

	/**
	 * 通知機能関連データのクリーンアップ
	 */
	private function cleanup_worklog_notice_data() {
		global $wpdb;

		// 削除対象のオプションキー
		$option_keys = array(
			'of_worklog_target_user_ids',
			'of_worklog_target_post_types',
			'of_worklog_min_role',
			'andw_worklog_mode',
		);

		foreach ( $option_keys as $key ) {
			delete_option( $key );
		}

		// 削除対象のユーザーメタキー（ワイルドカード検索）
		$user_meta_patterns = array(
			'andw_worklog_prompted_%',
			'andw_worklog_last_prompted_%',
		);

		foreach ( $user_meta_patterns as $pattern ) {
			$like_pattern = str_replace( '%', '', $pattern ) . '%';
			$cache_key    = 'andw:' . md5( $pattern . ':usermeta_keys' );
			$meta_keys    = wp_cache_get( $cache_key, 'andw' );
			if ( false === $meta_keys ) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Safe prepared query for cleanup operations requiring wildcard meta key deletion
				$meta_keys = $wpdb->get_col(
					$wpdb->prepare(
						"SELECT DISTINCT meta_key FROM {$wpdb->usermeta} WHERE meta_key LIKE %s",
						$like_pattern
					)
				);
				wp_cache_set( $cache_key, $meta_keys, 'andw', 300 );
			}

			foreach ( $meta_keys as $meta_key ) {
				delete_metadata( 'user', 0, $meta_key, '', true );
			}

			// 関連キャッシュクリア
			wp_cache_delete( 'andw:' . md5( $pattern . ':usermeta' ), 'andw' );
		}

		// 削除対象のポストメタキー（ワイルドカード検索）
		$post_meta_patterns = array(
			'andw_worklog_prompted_%',
			'andw_worklog_last_prompted_%',
			'andw_worklog_revision_%',
		);

		foreach ( $post_meta_patterns as $pattern ) {
			$like_pattern = str_replace( '%', '', $pattern ) . '%';
			$cache_key    = 'andw:' . md5( $pattern . ':postmeta_keys' );
			$meta_keys    = wp_cache_get( $cache_key, 'andw' );
			if ( false === $meta_keys ) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Safe prepared query for cleanup operations requiring wildcard meta key deletion
				$meta_keys = $wpdb->get_col(
					$wpdb->prepare(
						"SELECT DISTINCT meta_key FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",
						$like_pattern
					)
				);
				wp_cache_set( $cache_key, $meta_keys, 'andw', 300 );
			}

			foreach ( $meta_keys as $meta_key ) {
				delete_post_meta_by_key( $meta_key );
			}

			// 関連キャッシュクリア
			wp_cache_delete( 'andw:' . md5( $pattern . ':postmeta' ), 'andw' );
		}

		// 削除対象のトランジェント
		$transient_keys = array(
			'andw_worklog_%',
		);

		foreach ( $transient_keys as $pattern ) {
			$all_options = function_exists( 'wp_load_alloptions' ) ? wp_load_alloptions() : array();
			foreach ( array_keys( $all_options ) as $option_name ) {
				if ( strpos( $option_name, '_transient_' . str_replace( '%', '', $pattern ) ) === 0 ||
					strpos( $option_name, '_transient_timeout_' . str_replace( '%', '', $pattern ) ) === 0 ) {
					delete_option( $option_name );
				}
			}

			// 関連キャッシュクリア
			wp_cache_delete( 'andw:' . md5( $pattern . ':transients' ), 'andw' );
		}
	}
}