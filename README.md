# COACHTECHフリマ
ユーザーがアイテムの出品および購入を行うフリマアプリサービスです。

<img width="1463" alt="Image" src="https://github.com/user-attachments/assets/3b1fc9e1-c9e7-4f63-95bb-ab24b7e98cfc" />

## 機能一覧
 - 会員登録機能
 - メール認証機能
 - ログイン機能
 - ログアウト機能
 - 商品一覧取得
 - 商品詳細情報の取得
 - お気に入り機能
 - コメント機能
 - お気に入り商品一覧取得
 - 商品購入機能
 - 配送先変更機能
 - Stripeを利用した決済機能
 - ユーザー情報の取得
 - プロフィール編集機能
 - 出品機能

### その他
 - FormRequestを使用したバリデーションの実装
 - レスポンシブデザインの対応
 - 登録画像をストレージに保存

## 使用技術
 - PHP 7.4.9
 - Laravel 8.83.29
 - MySQL 8.0.26
 - nginx 1.21.1

## 環境構築
### Dockerビルド
1. リポジトリのクローン
```
git clone git@github.com:Neito5865/free-market-app.git
```
＊MySQLは、OSによって起動しない場合があるので、それぞれのPCに合わせてdocker-compose.ymlファイルを編集してください。

2. Dockerのビルド
 - Laravelプロジェクトディレクトリへ移動
```
cd free-market-app
```
 - Dockerのビルドを実行
```
docker-compose up -d --build
```

### Laravel環境構築
1. PHPコンテナへログイン
```
docker-compose exec php bash
```

2. パッケージのインストール
```
composer install
```

3. .env.exampleファイルから.envファイルを作成する
 - .envファイルを作成
```
cp .env.example .env
```

4. 環境変数を変更する
 - .envファイルの11行目以降を以下のように編集する
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
 - .envファイルの31行目以降を以下のように編集する
```
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=from@example.com
MAIL_FROM_NAME="${APP_NAME}"
```
 - .envファイルに以下を追記する（Stripeアカウントを所有している場合のみ）
```
STRIPE_KEY=your_test_stripe_key
STRIPE_SECRET=your_test_stripe_secret_key
```
※Stripeアカウントを所有している場合は、ご自身のアカウントにて決済機能をお試しいただけます。  
その場合「your_test_stripe_key」にはStripeテスト環境の公開キーを、  
「your_test_stripe_secret_key」にはStripeテスト環境のシークレットキーを
記述してください。

5. キーを作成する
```
php artisan key:generate
```

6. マイグレーションの実行
```
php artisan migrate
```

7. シーディングの実行
```
php artisan db:seed
```

8. シンボリックリンクの作成
```
php artisan storage:link
```

## ER図
![er drawio](https://github.com/user-attachments/assets/94f17325-ef9b-4e55-a3e3-114489b46f1d)

## 開発環境（URL）
 - トップページ：http://localhost
 - ユーザー登録：http://localhost/register
 - MailHog：http://localhost:8025
 - phpMyAdmin：http://localhost:8080

## その他
 - 以下のユーザー情報でログイン可能。  
 メールアドレス：user1@example.com  
 パスワード：password

 - テストケース「支払い方法選択機能」について  
 JavaScriptで実装のため、目視確認。