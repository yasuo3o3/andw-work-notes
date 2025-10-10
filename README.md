# andW Work Notes

クライアント指示やサイト更新に関する作業メモを記録・管理するWordPressプラグインです。

**バージョン:** 1.0.6
**ライセンス:** GPL-2.0-or-later
**WordPress.org配布:** このプラグインは、WordPressコーディング規約とセキュリティ要件に完全準拠してWordPress.orgプラグインディレクトリへの申請用に準備されています。最新バージョン(1.0.6)は包括的なコード品質改善と統一されたプレフィックス規約を含んでいます。

---

## ✨ 機能

- **andW作業メモ管理**: 投稿や固定ページに紐づく作業メモの記録
- **マスタデータ管理**: 依頼者と作業者をマスタリストで管理
- **ステータス追跡**: ステータス追跡（依頼中、作業中、完了）
- **日付管理**: 実装日の追跡
- **一覧・検索**: ソート可能な列でのフィルターと検索機能
- **管理バー連携**: WordPress管理バーからの簡単追加
- **Gutenberg対応**: REST API対応を含む完全なブロックエディタ連携
- **セキュリティ準拠**: 完全なnonce検証と権限チェック

---

## 🚀 インストール

### WordPress.orgから（推奨）
1. WordPress管理画面 → プラグイン → 新規追加に移動
2. 「andW Work Notes」で検索
3. プラグインをインストールして有効化

### 手動インストール
1. プラグインファイルを `/wp-content/plugins/andw-work-notes/` ディレクトリにアップロード
2. WordPressの「プラグイン」画面からプラグインを有効化
3. andW Work Notes → 設定で依頼者と作業者を設定

---

## 💻 使用方法

### 基本設定
1. **マスタデータの設定**: andW Work Notes → 設定で依頼者と作業者を設定
2. **作業メモの作成**:
   - **Gutenbergエディタ**: 投稿サイドバーの「andW Work Notes」パネルを使用
   - **クラシックエディタ**: 投稿内容下のメタボックスを使用
   - **単独作成**: andW Work Notes → 新規追加から作業メモを作成

### 作業メモフィールド
- **作業タイトル**: 作業の簡潔な説明（2行テキストエリア）
- **依頼者**: 設定されたマスタデータから選択
- **作業者**: 担当作業者を選択
- **ステータス**: 依頼中/作業中/完了
- **実装日**: 作業が実行された日付
- **対象投稿**: リンクされた投稿または固定ページ（投稿エディタから作成時）

---

## ⚙️ 動作環境

- **WordPress**: 6.0以上
- **PHP**: 8.0以上（8.1+推奨）
- **動作確認済み**: WordPress 6.8

---

## 🔧 開発

### 開発環境のセットアップ

```bash
# リポジトリのクローン
git clone [repository-url] andw-work-notes
cd andw-work-notes

# 依存関係のインストール
composer install

# 開発ワークフロー
vendor/bin/phpcs        # WordPressコーディング規約チェック
vendor/bin/phpcbf       # コーディング規約の自動修正
php -l *.php           # PHP構文チェック
```

### 配布用ビルド

```bash
# 配布用ZIPの作成（開発ファイルを除外）
git archive --format=zip --prefix=andw-work-notes/ HEAD > andw-work-notes.zip
```

`.gitattributes`ファイルにより、配布ビルドから開発ファイルが除外されます。

---

## 🔐 セキュリティとコンプライアンス

このプラグインはWordPress.orgセキュリティガイドラインに従っています：

- **Nonce検証**: すべてのフォーム送信でWordPress noncesを使用
- **権限チェック**: すべてのエンドポイントで適切な`current_user_can()`チェック
- **データサニタイゼーション**: すべての入力をWordPress関数でサニタイズ
- **コーディング規約**: WordPressコーディング規約（WPCS）完全準拠
- **プラグインチェック**: WordPress Plugin Checkのすべての要件をクリア

---

## 📝 WordPress.org申請

このプラグインはWordPress.orgプラグインディレクトリ申請用に特別に準備されています：

- ✅ **セキュリティ監査完了**: すべてのエントリポイントでnonceと権限チェックで保護
- ✅ **WordPressコーディング規約**: WPCS完全準拠を検証済み
- ✅ **プラグインチェック**: Plugin Checkのすべての項目をクリア
- ✅ **PHP 8.x互換性**: auth_callback関数をPHP 8.x用に更新
- ✅ **配布準備完了**: クリーンな配布ビルド用の適切な`.gitattributes`設定

プラグインはWordPress.orgガイドラインへのコンプライアンスを確保するための包括的なレビューとテストを経ており、公式リポジトリへの申請準備が完了しています。

---

## 📄 ライセンス

このプロジェクトは**GPL-2.0-or-later**（GNU General Public License v2.0またはそれ以降）でライセンスされています。

- **無料使用**: 個人・商用目的で自由に使用可能
- **改変・配布**: ソースコードの改変・再配布が可能
- **コピーレフト**: 改変版は同じライセンスでリリース必須
- **無保証**: 現状渡しで保証なし

完全なライセンス条項については[LICENSE](LICENSE)ファイルを参照してください。

---

## 🔗 リンク

- **WordPress.orgプラグインディレクトリ**: *申請中*
- **GitHubリポジトリ**: [ソースコードを表示](https://github.com/)
- **サポート**: WordPress.orgサポートフォーラム（公開後）

---

# andW Work Notes

WordPress plugin for recording and managing andW work notes related to client instructions and site updates within your WordPress admin.

**Version:** 1.0.6
**License:** GPL-2.0-or-later
**WordPress.org Distribution:** This plugin is prepared for submission to the WordPress.org Plugin Directory with full compliance to WordPress coding standards and security requirements. Latest version (1.0.6) includes comprehensive code quality improvements and unified prefix conventions.

---

## ✨ Features

- **andW Work Notes Management**: Record work notes linked to posts and pages
- **Master Data Management**: Manage requesters and workers with master lists
- **Status Tracking**: Track status (Requested, In Progress, Completed)
- **Date Management**: Implementation date tracking
- **List & Search**: Filter and search functionality with sortable columns
- **Admin Bar Integration**: Quick addition from WordPress admin bar
- **Gutenberg Support**: Full block editor integration with REST API support
- **Security Compliant**: Complete nonce verification and permission checking

---

## 🚀 Installation

### From WordPress.org (Recommended)
1. Navigate to WordPress Admin → Plugins → Add New
2. Search for "andW Work Notes"
3. Install and activate the plugin

### Manual Installation
1. Upload plugin files to `/wp-content/plugins/andw-work-notes/` directory
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Configure requesters and workers in andW Work Notes → Settings

---

## 💻 Usage

### Basic Setup
1. **Configure Master Data**: Go to andW Work Notes → Settings to set up requesters and workers
2. **Create andW Work Notes**:
   - **Gutenberg Editor**: Use the "andW Work Notes" panel in the post sidebar
   - **Classic Editor**: Use the meta box below the post content
   - **Standalone**: Create work notes from andW Work Notes → Add New

### Work Note Fields
- **Work Title**: Brief description of the work (2-line textarea)
- **Requester**: Select from configured master data
- **Worker**: Select assigned worker
- **Status**: Requested/In Progress/Completed
- **Implementation Date**: When the work was performed
- **Target Post**: Linked post or page (when created from post editor)

---

## ⚙️ Requirements

- **WordPress**: 6.0 or higher
- **PHP**: 8.0 or higher (8.1+ recommended)
- **Tested up to**: WordPress 6.8

---

## 🔧 Development

### Development Setup

```bash
# Clone repository
git clone [repository-url] andw-work-notes
cd andw-work-notes

# Install dependencies
composer install

# Development workflow
vendor/bin/phpcs        # WordPress Coding Standards check
vendor/bin/phpcbf       # Auto-fix coding standards
php -l *.php           # PHP syntax check
```

### Distribution Build

```bash
# Create distribution ZIP (excludes development files)
git archive --format=zip --prefix=andw-work-notes/ HEAD > andw-work-notes.zip
```

The `.gitattributes` file ensures development files are excluded from distribution builds.

---

## 🔐 Security & Compliance

This plugin follows WordPress.org security guidelines:

- **Nonce Verification**: All form submissions use WordPress nonces
- **Permission Checking**: Proper `current_user_can()` checks on all endpoints
- **Data Sanitization**: All input sanitized with WordPress functions
- **Coding Standards**: Full WordPress Coding Standards (WPCS) compliance
- **Plugin Check**: Passes all WordPress Plugin Check requirements

---

## 📝 WordPress.org Submission

This plugin is specifically prepared for WordPress.org Plugin Directory submission:

- ✅ **Security Audit Complete**: All entry points secured with nonce and permission checks
- ✅ **WordPress Coding Standards**: Full WPCS compliance verified
- ✅ **Plugin Check**: All Plugin Check items cleared
- ✅ **PHP 8.x Compatibility**: auth_callback functions updated for PHP 8.x
- ✅ **Distribution Ready**: Proper `.gitattributes` configuration for clean distribution builds

The plugin has undergone comprehensive review and testing to ensure compliance with WordPress.org guidelines and is ready for submission to the official repository.

---

## 📄 License

This project is licensed under the **GPL-2.0-or-later** (GNU General Public License v2.0 or later).

- **Free Usage**: Free to use for personal and commercial purposes
- **Modification & Distribution**: Source code can be modified and redistributed
- **Copyleft**: Modified versions must be released under the same license
- **No Warranty**: Provided as-is without warranty

See the [LICENSE](LICENSE) file for full license terms.

---

## 🔗 Links

- **WordPress.org Plugin Directory**: *Pending submission*
- **GitHub Repository**: [View Source Code](https://github.com/)
- **Support**: WordPress.org support forums (after publication)