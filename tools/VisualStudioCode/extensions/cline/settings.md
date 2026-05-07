# Cline 設定一覧

## API Configuration

| 設定 | 内容 | 推奨 |
|---|---|---|
| use different models for plan and act | PlanとActで別モデルを使う | ❌ OFF |
| Reasoning Effort | 推論の深さ（low/medium/high） | high |

---

## Feature Settings - Agent（上部）

| 設定 | 内容 | 推奨 |
|---|---|---|
| Subagents | 複雑なタスクをサブエージェントに分割して並列処理 | ✅ ON |
| Native Tool Call | モデルネイティブのツール呼び出しを使用 | ✅ ON |
| Parallel Tool Calling | 複数ツールを並列実行 | ✅ ON |
| Strict Plan Mode | Planモード中は一切ファイル操作しない | ✅ ON |
| Auto Compact | コンテキストが長くなったら自動圧縮 | ✅ ON |
| Focus Chain | タスクの流れを追跡して集中 | ✅ ON |
| Reminder Interval | 指示をリマインドする間隔（1-10） | 5 |

---

## Feature Settings - Editor

| 設定 | 内容 | 推奨 |
|---|---|---|
| Feature Tips | 機能のヒントを表示 | ❌ OFF |
| Background Edit | バックグラウンドでファイル編集 | ❌ OFF |
| Checkpoints | 作業前に自動チェックポイント作成 | ✅ ON |
| Cline Web Tools | Web検索などのツールを有効化 | ✅ ON |

---

## Feature Settings - Agent（下部）

| 設定 | 内容 | 推奨 |
|---|---|---|
| Yolo Mode | 確認なしで全操作を自動実行 | ❌ OFF |
| Double-Check Completion | 完了前に再確認する | ✅ ON |
| Lazy Teammate Mode | 最小限の作業だけ行う（指示外は触らない） | ✅ ON |

---

## Feature Settings - Advanced

| 設定 | 内容 | 推奨 |
|---|---|---|
| Hooks | 特定イベント時にコマンド実行 | ❌ OFF |

---

## MCP Display Mode

| 設定 | 内容 | 推奨 |
|---|---|---|
| MCP Display Mode | MCPの表示形式 | rich display |

---

## Browser Settings

| 設定 | 内容 | 推奨 |
|---|---|---|
| disable browser tool usage | ブラウザ操作を無効化 | ❌ OFF |
| viewport size | ブラウザの画面サイズ | 1280x800 |
| use remote browser connection | リモートブラウザ接続 | ❌ OFF |

---

## Terminal Settings

| 設定 | 内容 | 推奨 |
|---|---|---|
| Default Terminal Profile | 使用するシェル | bash |
| Shell integration timeout | シェル統合のタイムアウト秒数 | 15秒 |
| enable aggressive terminal reuse | ターミナルを積極的に再利用 | ❌ OFF |
| Terminal Execution Mode | 実行モード | VS Code Terminal |
| Terminal output limit | 出力の最大行数 | 500〜1000 |

---

## General Settings

| 設定 | 内容 | 推奨 |
|---|---|---|
| allow error and usage reporting | エラーと使用状況を送信 | ❌ OFF |
