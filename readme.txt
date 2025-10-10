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

== Description ==

andW Work Notesは、投稿や固定ページに紐づく作業メモを記録・管理し、依頼者・作業者のマスタ管理機能を提供します。

= 主な機能 =

* 投稿・固定ページに関連する作業メモの記録
* 依頼者・作業者のマスタ管理
* ステータス管理（依頼中、作業中、完了）
* 実装日管理
* 一覧表示とフィルタリング検索（依頼者・作業者列でのソート可能）
* 管理バーからの簡単追加
* Gutenbergブロックエディタ対応
* register_post_meta経由のREST API対応
* アンインストール時の完全なデータクリーンアップ

= 利用シーン =

* Web制作会社のプロジェクト管理
* サイトメンテナンス・更新作業の記録
* クライアント依頼の進捗追跡

= WordPress.org配布 =

このプラグインは、WordPressコーディング規約とセキュリティ要件に完全準拠してWordPress.orgプラグインディレクトリでの配布用に準備されています。

== インストール ==

1. プラグインファイルを `/wp-content/plugins/andw-work-notes` ディレクトリにアップロードするか、WordPressプラグイン画面から直接インストールしてください。
2. WordPressの「プラグイン」画面からプラグインを有効化してください
3. andW Work Notes画面を使用してプラグインを設定してください
4. 設定で依頼者と作業者を設定してください（推奨）

== よくある質問 ==

= 必要なユーザー権限は何ですか？ =

編集者以上の権限を持つユーザーがこのプラグインを使用できます。設定画面は管理者のみアクセス可能です。

= データはどこに保存されますか？ =

データはwp_postsテーブルにWordPressカスタム投稿タイプとして保存され、関連情報はwp_postmetaテーブルに保存されます。

= プラグインを削除するとデータはどうなりますか？ =

プラグインがアンインストールされると、すべての作業メモデータが自動的に削除されます。

== スクリーンショット ==

1. 作業メモ一覧画面 – 依頼者やステータスでのフィルターと検索で作業メモを管理。
2. 作業メモ設定画面 – 依頼者と作業者のマスタリストを設定し、必要に応じてキャッシュをクリア。
3. 投稿編集画面 – 投稿エディタサイドバーから関連する作業メモを直接表示・管理。

---

A WordPress plugin for recording and managing andW work notes related to client instructions and site updates within your WordPress admin.

== Description ==

andW Work Notes allows you to record and manage work notes linked to posts and pages with requester/worker master management functionality.

= Key Features =

* Record work notes related to posts and pages
* Master management for requesters and workers
* Status management (Requested, In Progress, Completed)
* Implementation date management
* List display and filtering search (sortable by requester/worker columns)
* Quick addition from admin bar
* Gutenberg block editor integration
* REST API support via register_post_meta
* Complete data cleanup on uninstall

= Use Cases =

* Project management for web development agencies
* Recording site maintenance and update tasks
* Progress tracking for client requests

= WordPress.org Distribution =

This plugin is prepared for distribution on WordPress.org Plugin Directory with full compliance to WordPress coding standards and security requirements.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/andw-work-notes` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the andW Work Notes screen to configure the plugin
4. Set up requesters and workers in Settings (recommended)

== Frequently Asked Questions ==

= What user permissions are required? =

Users with Editor role or higher can use this plugin. Settings screen is accessible only to administrators.

= Where is the data stored? =

Data is stored as WordPress custom post type in wp_posts table, with related information in wp_postmeta table.

= What happens to data when the plugin is deleted? =

All work note data is automatically deleted when the plugin is uninstalled.

== Screenshots ==

1. Work Notes list screen – Manage work notes with filters and search by requester or status.
2. Work Notes settings screen – Configure requester and worker master lists, and clear cache if needed.
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