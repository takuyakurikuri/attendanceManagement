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

使用技術  
PHP8.3  
laravel12.0  
mysql8.0  
mailhog  

URL 開発環境：http://localhost/  
phpMyAdmin：http://localhost:8080/  
mailhog：http://localhost:8025/  
