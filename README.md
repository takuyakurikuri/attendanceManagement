勤怠管理アプリ 環境構築  

Dockerビルド  

git clone git@github.com:takuyakurikuri/attendanceManagement.git  
docker-compose up -d --build  
<!-- MySQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせて docker-compose.ymlファイルを編集して下さい。 -->

Laravel環境構築  
docker-compose exec php bash  
composer install  
.env.exampleファイルから.envを作成し、環境変数を変更して下さい  
php artisan key:generate  
php artisan migrate  
<!-- php artisan storage:link   -->
php artisan db:seed  

データベースの接続設定  
envファイルの環境変数は以下に設定ください。  
DB_CONNECTION=mysql  
DB_HOST=mysql  
DB_PORT=3306  
DB_DATABASE=laravel_attendance  
DB_USERNAME=owner  
DB_PASSWORD=password  

mailHogを使ったメールの送信設定  
envファイルの環境変数は以下に設定ください。  
MAIL_MAILER=smtp  
MAIL_HOST=mailhog  
MAIL_PORT=1025  
MAIL_USERNAME=null  
MAIL_PASSWORD=null  
MAIL_ENCRYPTION=null  
MAIL_FROM_ADDRESS=no-reply@example.com  
MAIL_FROM_NAME="${APP_NAME}"  

テストの実行方法  
php artisan key:generate --env=testing  
php artisan config:clear  
php artisan migrate --env=testing  
vendor/bin/phpunit tests/Feature  
もし単一でテストを実行したい場合は上記ディレクトリのルートに指定したいファイル名を記載ください  

使用技術  
PHP8.3  
laravel12.0  
mysql8.0  
mailhog  

URL 開発環境：http://localhost/  
phpMyAdmin：http://localhost:8080/  
mailhog：http://localhost:8025/  
