=== andW Work Notes ===
Contributors: yasuo3o3
Tags: notes, workflow, task-management, admin, gutenberg
Requires at least: 6.0
Tested up to: 6.8
Stable tag: 1.0.7
Requires PHP: 8.0
License: GPL-2.0-or-later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

クライアント指示やサイト更新に関する作業メモを記録・管理するWordPressプラグインです。
A WordPress plugin for recording and managing andW work notes related to client instructions and site updates within your WordPress admin.

== Description ==

andW Work Notesは、投稿や固定ページに紐づく作業メモを記録・管理し、依頼者・作業者のマスタ管理機能を提供します。
andW Work Notes allows you to record and manage work notes linked to posts and pages with requester/worker master management functionality.

= 主な機能 / Key Features =

* 投稿・固定ページに関連する作業メモの記録
* Record work notes related to posts and pages
* 依頼者・作業者のマスタ管理
* Master management for requesters and workers
* ステータス管理（依頼中、作業中、完了）
* Status management (Requested, In Progress, Completed)
* 実装日管理
* Implementation date management
* 一覧表示とフィルタリング検索（依頼者・作業者列でのソート可能）
* List display and filtering search (sortable by requester/worker columns)
* 管理バーからの簡単追加
* Quick addition from admin bar
* Gutenbergブロックエディタ対応
* Gutenberg block editor integration
* register_post_meta経由のREST API対応
* REST API support via register_post_meta
* アンインストール時の完全なデータクリーンアップ
* Complete data cleanup on uninstall

= 利用シーン / Use Cases =

* Web制作会社のプロジェクト管理
* Project management for web development agencies
* サイトメンテナンス・更新作業の記録
* Recording site maintenance and update tasks
* クライアント依頼の進捗追跡
* Progress tracking for client requests

= WordPress.org配布 / WordPress.org Distribution =

このプラグインは、WordPressコーディング規約とセキュリティ要件に完全準拠してWordPress.orgプラグインディレクトリでの配布用に準備されています。
This plugin is prepared for distribution on WordPress.org Plugin Directory with full compliance to WordPress coding standards and security requirements.

== インストール / Installation ==

1. プラグインファイルを `/wp-content/plugins/andw-work-notes` ディレクトリにアップロードするか、WordPressプラグイン画面から直接インストールしてください。
1. Upload the plugin files to the `/wp-content/plugins/andw-work-notes` directory, or install the plugin through the WordPress plugins screen directly.
2. WordPressの「プラグイン」画面からプラグインを有効化してください
2. Activate the plugin through the 'Plugins' screen in WordPress
3. andW Work Notes画面を使用してプラグインを設定してください
3. Use the andW Work Notes screen to configure the plugin
4. 設定で依頼者と作業者を設定してください（推奨）
4. Set up requesters and workers in Settings (recommended)

== よくある質問 / Frequently Asked Questions ==

= 必要なユーザー権限は何ですか？ / What user permissions are required? =

編集者以上の権限を持つユーザーがこのプラグインを使用できます。設定画面は管理者のみアクセス可能です。
Users with Editor role or higher can use this plugin. Settings screen is accessible only to administrators.

= データはどこに保存されますか？ / Where is the data stored? =

データはwp_postsテーブルにWordPressカスタム投稿タイプとして保存され、関連情報はwp_postmetaテーブルに保存されます。
Data is stored as WordPress custom post type in wp_posts table, with related information in wp_postmeta table.

= プラグインを削除するとデータはどうなりますか？ / What happens to data when the plugin is deleted? =

プラグインがアンインストールされると、すべての作業メモデータが自動的に削除されます。
All work note data is automatically deleted when the plugin is uninstalled.

== スクリーンショット / Screenshots ==

1. 作業メモ一覧画面 – 依頼者やステータスでのフィルターと検索で作業メモを管理。
1. Work Notes list screen – Manage work notes with filters and search by requester or status.
2. 作業メモ設定画面 – 依頼者と作業者のマスタリストを設定し、必要に応じてキャッシュをクリア。
2. Work Notes settings screen – Configure requester and worker master lists, and clear cache if needed.
3. 投稿編集画面 – 投稿エディタサイドバーから関連する作業メモを直接表示・管理。
3. Post edit screen – View and manage related work notes directly from the post editor sidebar.

== Changelog ==
= 1.0.7 =
* Fixed: WordPress.org Plugin Check compliance - removed load_plugin_textdomain() for WP4.6+ auto-loading
* Fixed: Removed all error_log() calls from production code, replaced with debug hooks
* Fixed: Eliminated WP_List_Table direct loading, migrated to standard CPT list functionality
* Fixed: Complete WordPress.org distribution preparation with security audit compliance
* Fixed: Implemented one-time data migration from legacy prefixes (ofwn/of_) to unified andw prefix
* Security: Enhanced nonce verification and capability checks across all entry points
* Security: Implemented proper sanitization and escaping throughout the plugin

= 1.0.6 =
* Fixed: Unified all code prefixes to andw/ANDW consistently throughout the plugin
* Fixed: Unified cache system groups and key prefixes to 'andw'
* Fixed: WordPress.DB.DirectDatabaseQuery.DirectQuery compliance with proper phpcs:ignore comments
* Fixed: WordPress.DB.DirectDatabaseQuery.NoCaching with implemented caching functionality
* Changed: Gutenberg sidebar CSS class names unified (work-notes-* → andw-work-notes-*)
* Changed: Console message prefixes unified (OFWN → ANDW)
* Changed: Enhanced distribution ZIP exclusion settings (.gitattributes)
* Removed: Unnecessary updates/ directory for cleaner file structure

= 1.0.4 =
* WordPress.org distribution preparation completed
* Removed independent update mechanism
* Removed distribution endpoint functionality

= 1.0.3 =
* Version update for Plugin Checker compatibility

= 1.0.2 =
* Unified version number to 1.0.2 (full Semantic Versioning compliance)

= 1.00 =
* Work log reminder (Snackbar) functionality implementation
* Multi-user target setting functionality
* Revision-based duplicate prevention
* Added requester and worker columns to admin list
* Column sorting functionality implementation
* REST API support via register_post_meta
* Complete data deletion in uninstall.php
* Enhanced XSS protection and localization improvements
* Asset loading optimization
* WordPress Coding Standards full compliance

= 0.05 =
* Enhanced i18n support
* Security and performance improvements
* WordPress Coding Standards compliance
* Autoload optimization and asset loading improvements
* Enhanced SQL injection protection

= 0.04 =
* Initial release
* Basic work notes functionality
* Master management functionality

== Upgrade Notice ==

= 1.0.6 =
Security audit response and PHP 8.x compatibility fixes. Update recommended.

= 0.05 =
Important update including security enhancements and internationalization support. Update recommended.