# KTT Laravel Project

Laravel + Docker環境のプロジェクトです。

## プロジェクト構成

```
ktt-laravel/
├── infra/              # インフラ設定
│   └── docker/         # Docker設定ファイル
│       ├── nginx/      # Nginx設定
│       ├── php/        # PHP設定
│       └── mysql/      # MySQL設定
├── src/                # Laravelアプリケーション
└── docker-compose.yml  # Docker Compose設定
```

## セットアップ手順

### 1. 環境の起動

```bash
docker-compose up -d
```

### 2. Composer依存関係のインストール（初回のみ）

```bash
docker-compose exec php composer install
```

### 3. アプリケーションキーの生成（初回のみ）

```bash
docker-compose exec php php artisan key:generate
```

### 4. データベースマイグレーション（初回のみ）

```bash
docker-compose exec php php artisan migrate
```

### 5. アプリケーションへのアクセス

ブラウザで以下のURLにアクセス：
- http://localhost

## よく使うコマンド

### コンテナの起動
```bash
docker-compose up -d
```

### コンテナの停止
```bash
docker-compose down
```

### コンテナのログ確認
```bash
docker-compose logs -f
```

### PHPコンテナ内でコマンド実行
```bash
docker-compose exec php [コマンド]
```

### Artisanコマンドの実行
```bash
docker-compose exec php php artisan [コマンド]
```

### Composerコマンドの実行
```bash
docker-compose exec php composer [コマンド]
```

## データベース接続情報

- **ホスト**: mysql
- **ポート**: 3306
- **データベース名**: ktt_laravel
- **ユーザー名**: laravel
- **パスワード**: laravel
- **ルートパスワード**: root

## 開発環境

- PHP: 8.2-FPM
- Nginx: Alpine
- MySQL: 8.0
- Laravel: 12.x

