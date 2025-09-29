/**
 * 作業メモ Gutenberg サイドバー（公開ステータス直下）
 * PluginPostStatusInfo を使用して公開ステータスの直下に作業メモ入力フォームを表示
 */
(function() {
    'use strict';
    
    // デバッグ用：WordPress グローバルオブジェクトの確認
    if (window?.wp?.hooks?.applyFilters?.('andw_debug', (window.WP_DEBUG && window.SCRIPT_DEBUG) || false)) {
        console.log('Work Notes Debug: wp object available:', typeof wp !== 'undefined');
        console.log('Work Notes Debug: wp.element:', typeof wp.element !== 'undefined');
        console.log('Work Notes Debug: wp.editPost:', typeof wp.editPost !== 'undefined');
        console.log('Work Notes Debug: wp.plugins:', typeof wp.plugins !== 'undefined');
    }
    
    // WordPress コンポーネントを取得
    const { createElement: e, Fragment } = wp.element;
    const { PluginPostStatusInfo } = wp.editPost;
    const { useSelect, useDispatch } = wp.data;
    const { TextControl, TextareaControl, SelectControl, BaseControl, Button } = wp.components;
    const { useEntityProp } = wp.coreData;
    const { registerPlugin } = wp.plugins;
    const { __ } = wp.i18n;
    const { useState, useEffect } = wp.element;
    
    // 作業メモサイドバーコンポーネント
    function WorkNotesPanel() {
        // 投稿タイプとIDを取得
        const postType = useSelect(select => 
            select('core/editor').getCurrentPostType(), []
        );
        const postId = useSelect(select => 
            select('core/editor').getCurrentPostId(), []
        );
        
        // 投稿と固定ページでない場合は表示しない
        const targetPostTypes = ['post', 'page'];
        if (!targetPostTypes.includes(postType)) {
            return null;
        }
        
        // 折りたたみ状態の管理（初期状態は展開）
        const [isExpanded, setIsExpanded] = useState(true);
        
        // 初期値適用フラグ（初回マウント1回のみ）
        const [prefillApplied, setPrefillApplied] = useState(false);
        const [backfillProcessed, setBackfillProcessed] = useState(false);
        
        // Phase 2: 親投稿のメタデータを取得（CPT ID含む）
        const [meta, setMeta] = useEntityProp('postType', postType, 'meta', postId);
        const boundCptId = meta?._andw_bound_cpt_id;
        
        // Phase 2: CPT直接取得（表示用）
        const workNote = useSelect(
            (select) => {
                if (!boundCptId) return null;
                return select('core').getEntityRecord('postType', 'andw_work_note', boundCptId);
            },
            [boundCptId]
        );
        
        // Phase 2: 現在の値を取得（CPT優先、フォールバック：親メタ）
        const currentTargetType = workNote?.meta?._andw_target_type || meta?._andw_target_type || '';
        const currentTargetId = workNote?.meta?._andw_target_id || meta?._andw_target_id || '';
        const currentRequester = workNote?.meta?._andw_requester || meta?._andw_requester || '';
        const currentWorker = workNote?.meta?._andw_worker || meta?._andw_worker || '';
        const currentStatus = workNote?.meta?._andw_status || meta?._andw_status || '依頼';
        const currentWorkDate = workNote?.meta?._andw_work_date || meta?._andw_work_date || new Date().toISOString().split('T')[0];
        // 編集用：親メタ優先（CPTは参照のみ）
        const currentWorkTitle = meta?._andw_work_title || meta?._andw_target_label || '';
        const currentWorkContent = meta?._andw_work_content || '';
        
        // Pタグなどを除去するヘルパー関数
        const stripHtmlTags = (html) => {
            if (!html) return '';
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            return tempDiv.textContent || tempDiv.innerText || '';
        };
        
        // Phase 3: JavaScript主導の作業メモ作成（遅延問題対策）
        const createOrUpdateWorkNote = function(updates) {
            // 親投稿のメタフィールドを更新（従来通り）
            const newMeta = { ...meta };
            Object.keys(updates).forEach(key => {
                newMeta[key] = updates[key];
            });
            setMeta(newMeta);
            
            // デバッグログ
            if (window?.wp?.hooks?.applyFilters?.('andw_debug', (window.WP_DEBUG && window.SCRIPT_DEBUG) || false)) {
                console.log('Work Notes: メタフィールド更新', updates);
            }
        };
        
        // 投稿保存後のAJAX作業メモ作成
        const createWorkNoteViaAjax = function(workTitle, workContent) {
            // 投稿保存完了を少し待ってから実行（データベースコミット待ち）
            setTimeout(() => {
                const ajaxData = {
                    action: 'andw_create_work_note',
                    nonce: window.andwAjax?.nonce || '',
                    post_id: postId,
                    work_title: workTitle || '',
                    work_content: workContent || '',
                    requester: currentRequester,
                    worker: currentWorker,
                    status: currentStatus,
                    work_date: currentWorkDate
                };
                
                if (window?.wp?.hooks?.applyFilters?.('andw_debug', (window.WP_DEBUG && window.SCRIPT_DEBUG) || false)) {
                    console.log('Work Notes: AJAX作業メモ作成開始', ajaxData);
                }
                
                // jQuery AJAXを使用（より確実な方法）
                jQuery.post(window.ajaxurl || '/wp-admin/admin-ajax.php', ajaxData)
                    .done(function(response) {
                        if (window?.wp?.hooks?.applyFilters?.('andw_debug', (window.WP_DEBUG && window.SCRIPT_DEBUG) || false)) {
                            console.log('Work Notes: AJAX作業メモ作成成功', response);
                        }
                        if (response.success && !response.data.duplicate) {
                            // 成功通知
                            wp.data.dispatch('core/notices').createNotice(
                                'success',
                                '作業メモを作成しました: ' + response.data.note_title,
                                { type: 'snackbar', isDismissible: true }
                            );
                        } else if (response.data && response.data.duplicate) {
                            if (window?.wp?.hooks?.applyFilters?.('andw_debug', (window.WP_DEBUG && window.SCRIPT_DEBUG) || false)) {
                                console.log('Work Notes: 重複のため作成をスキップ');
                            }
                        } else {
                            if (window?.wp?.hooks?.applyFilters?.('andw_debug', (window.WP_DEBUG && window.SCRIPT_DEBUG) || false)) {
                                console.warn('Work Notes: 作業メモ作成に失敗', response.data?.message || '不明なエラー');
                            }
                        }
                    })
                    .fail(function(xhr, status, error) {
                        if (window?.wp?.hooks?.applyFilters?.('andw_debug', (window.WP_DEBUG && window.SCRIPT_DEBUG) || false)) {
                            console.error('Work Notes: AJAX作業メモ作成エラー', {
                                status: status,
                                error: error,
                                responseText: xhr.responseText
                            });
                        }
                        wp.data.dispatch('core/notices').createNotice(
                            'error',
                            '作業メモの作成に失敗しました: ' + error,
                            { type: 'snackbar', isDismissible: true }
                        );
                    });
            }, 500); // 0.5秒後に実行
        };
        
        // 旧式メタ更新（Phase 1互換性用）
        const updateMeta = function(key, value) {
            setMeta({ ...meta, [key]: value });
        };
        
        // 初期値適用処理（初回マウント時のみ）
        useEffect(() => {
            if (prefillApplied || !window.andwPrefill || !meta) {
                return;
            }
            
            const prefillData = window.andwPrefill;
            let hasUpdates = false;
            const updates = { ...meta };
            
            // 空フィールドにのみ初期値を適用
            if (prefillData.target_type && (!meta._andw_target_type || meta._andw_target_type === '')) {
                updates._andw_target_type = prefillData.target_type;
                hasUpdates = true;
            }
            
            if (prefillData.target_id && (!meta._andw_target_id || meta._andw_target_id === '')) {
                updates._andw_target_id = prefillData.target_id;
                hasUpdates = true;
            }
            
            if (prefillData.requester && (!meta._andw_requester || meta._andw_requester === '')) {
                updates._andw_requester = prefillData.requester;
                hasUpdates = true;
            }
            
            if (prefillData.worker && (!meta._andw_worker || meta._andw_worker === '')) {
                updates._andw_worker = prefillData.worker;
                hasUpdates = true;
            }
            
            if (hasUpdates) {
                setMeta(updates);
                if (window?.wp?.hooks?.applyFilters?.('andw_debug', (window.WP_DEBUG && window.SCRIPT_DEBUG) || false)) {
                    console.log('Work Notes: 初期値を適用しました', updates);
                }
            }
            
            setPrefillApplied(true);
            
            // lazy backfill処理
            if (prefillData.needs_backfill && prefillData.note_id && prefillData.current_post_id && !backfillProcessed) {
                setBackfillProcessed(true);
                
                // wp.apiFetch でbackfill実行
                wp.apiFetch({
                    path: '/wp/v2/andw_work_note/' + prefillData.note_id,
                    method: 'POST',
                    data: {
                        meta: {
                            _andw_bound_post_id: prefillData.current_post_id
                        }
                    }
                }).then(response => {
                    if (window?.wp?.hooks?.applyFilters?.('andw_debug', (window.WP_DEBUG && window.SCRIPT_DEBUG) || false)) {
                        console.log('Work Notes: lazy backfill完了', response);
                    }
                }).catch(error => {
                    if (window?.wp?.hooks?.applyFilters?.('andw_debug', (window.WP_DEBUG && window.SCRIPT_DEBUG) || false)) {
                        console.warn('Work Notes: lazy backfill失敗', error);
                    }
                });
            }
            
        }, [meta, prefillApplied, backfillProcessed]);
        
        // 投稿保存の監視とAJAX作業メモ作成
        const isSaving = useSelect(select => 
            select('core/editor').isSavingPost(), []
        );
        const wasSaving = wp.element.useRef(false);
        
        // AJAX作業メモ作成は一時的に無効化（save_postフックを優先）
        // useEffect(() => {
        //     // 保存完了時（isSaving: true → false）にAJAX実行
        //     if (wasSaving.current && !isSaving) {
        //         const workTitle = meta?._andw_work_title || '';
        //         const workContent = meta?._andw_work_content || '';
        //         
        //         // 作業タイトルまたは作業内容がある場合のみ実行
        //         if (workTitle || workContent) {
        //             console.log('Work Notes: 投稿保存完了 - AJAX作業メモ作成を開始');
        //             createWorkNoteViaAjax(workTitle, workContent);
        //         }
        //     }
        //     wasSaving.current = isSaving;
        // }, [isSaving, meta?._andw_work_title, meta?._andw_work_content]);
        
        // 依頼元・担当者のオプション（サーバーから取得）
        const requesterOptions = window.andwGutenbergData?.requesters || [];
        const workerOptions = window.andwGutenbergData?.workers || [];
        
        // セレクトオプションを構築
        const requesterSelectOptions = [
            { label: '（選択）', value: '' },
            ...requesterOptions.map(option => ({ label: option, value: option })),
            { label: 'その他（手入力）', value: '__custom__' }
        ];
        
        const workerSelectOptions = [
            { label: '（選択）', value: '' },
            ...workerOptions.map(option => ({ label: option, value: option })),
            { label: 'その他（手入力）', value: '__custom__' }
        ];
        
        return e(PluginPostStatusInfo, {
            className: 'andw-work-notes-post-status-info'
        }, 
            // 単一ラッパー（PluginPostStatusInfoの唯一の直下子要素）
            e('div', {
                className: 'andw-work-notes-container'
            },
                // タイトル行（クリックで展開・折りたたみ）
                e(Button, {
                    variant: 'tertiary',
                    onClick: () => setIsExpanded(!isExpanded),
                    className: 'andw-work-notes-toggle-button',
                    'aria-expanded': isExpanded
                }, __('作業メモ属性', 'andw-work-notes') + (isExpanded ? ' ▲' : ' ▼')),
                
                // 折りたたみ可能なコンテンツ
                isExpanded && e(Fragment, {},
                    // 対象タイプ
                    e(SelectControl, {
                        label: __('対象タイプ', 'andw-work-notes'),
                        className: 'andw-work-notes-field',
                        value: currentTargetType,
                        options: [
                            { label: __('（任意）', 'andw-work-notes'), value: '' },
                            { label: __('投稿/固定ページ', 'andw-work-notes'), value: 'post' },
                            { label: __('サイト全体/設定/テーマ', 'andw-work-notes'), value: 'site' },
                            { label: __('その他', 'andw-work-notes'), value: 'other' }
                        ],
                        onChange: function(value) {
                            createOrUpdateWorkNote({ _andw_target_type: value });
                        }
                    }),
                    
                    // 対象ID
                    e(TextControl, {
                        label: __('対象ID（投稿IDなど）', 'andw-work-notes'),
                        className: 'andw-work-notes-field',
                        value: currentTargetId,
                        onChange: function(value) {
                            createOrUpdateWorkNote({ _andw_target_id: value });
                        }
                    }),
                    
                    
                    // 依頼元
                    e(SelectControl, {
                        label: __('依頼元', 'andw-work-notes'),
                        className: 'andw-work-notes-field andw-inline',
                        value: requesterSelectOptions.find(opt => opt.value === currentRequester) ? currentRequester : '__custom__',
                        options: requesterSelectOptions,
                        onChange: function(value) {
                            if (value === '__custom__') {
                                createOrUpdateWorkNote({ _andw_requester: '' });
                            } else {
                                createOrUpdateWorkNote({ _andw_requester: value });
                            }
                        }
                    }),
                    
                    // 依頼元カスタム入力（セレクトで__custom__が選択されている場合のみ表示）
                    !requesterSelectOptions.find(opt => opt.value === currentRequester) &&
                    e(TextControl, {
                        label: __('依頼元（手入力）', 'andw-work-notes'),
                        className: 'andw-work-notes-field',
                        value: currentRequester,
                        onChange: function(value) {
                            createOrUpdateWorkNote({ _andw_requester: value });
                        }
                    }),
                    
                    // 担当者
                    e(SelectControl, {
                        label: __('担当者', 'andw-work-notes'),
                        className: 'andw-work-notes-field andw-inline',
                        value: workerSelectOptions.find(opt => opt.value === currentWorker) ? currentWorker : '__custom__',
                        options: workerSelectOptions,
                        onChange: function(value) {
                            if (value === '__custom__') {
                                createOrUpdateWorkNote({ _andw_worker: '' });
                            } else {
                                createOrUpdateWorkNote({ _andw_worker: value });
                            }
                        }
                    }),
                    
                    // 担当者カスタム入力
                    !workerSelectOptions.find(opt => opt.value === currentWorker) &&
                    e(TextControl, {
                        label: __('担当者（手入力）', 'andw-work-notes'),
                        className: 'andw-work-notes-field',
                        value: currentWorker,
                        onChange: function(value) {
                            createOrUpdateWorkNote({ _andw_worker: value });
                        }
                    }),
                    
                    // ステータス
                    e(SelectControl, {
                        label: __('ステータス', 'andw-work-notes'),
                        className: 'andw-work-notes-field andw-inline',
                        value: currentStatus,
                        options: [
                            { label: __('依頼', 'andw-work-notes'), value: '依頼' },
                            { label: __('対応中', 'andw-work-notes'), value: '対応中' },
                            { label: __('完了', 'andw-work-notes'), value: '完了' }
                        ],
                        onChange: function(value) {
                            createOrUpdateWorkNote({ _andw_status: value });
                        }
                    }),
                    
                    // 実施日
                    e(TextControl, {
                        label: __('実施日', 'andw-work-notes'),
                        className: 'andw-work-notes-field andw-inline',
                        type: 'date',
                        value: currentWorkDate,
                        onChange: function(value) {
                            createOrUpdateWorkNote({ _andw_work_date: value });
                        }
                    }),
                    
                    // 作業タイトル（2行入力）
                    e(TextControl, {
                        label: __('作業タイトル', 'andw-work-notes'),
                        className: 'andw-work-notes-field',
                        value: currentWorkTitle,
                        rows: 2,
                        onChange: function(value) {
                            createOrUpdateWorkNote({ _andw_work_title: value });
                        }
                    }),
                    
                    // 新規追加: 作業内容（複数行テキストエリア）
                    e(TextareaControl, {
                        label: __('作業内容', 'andw-work-notes'),
                        className: 'andw-work-notes-field',
                        rows: 3,
                        value: stripHtmlTags(currentWorkContent),
                        onChange: function(value) {
                            createOrUpdateWorkNote({ _andw_work_content: value });
                        }
                    })
                )
            )
        );
    }
    
    // プラグイン登録
    registerPlugin('andw-work-notes-sidebar', {
        render: WorkNotesPanel,
        icon: 'clipboard'
    });
    
})();