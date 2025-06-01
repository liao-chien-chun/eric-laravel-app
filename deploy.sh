#!/bin/bash
set -e

echo "安裝 Composer 套件..."
docker-compose exec -T app sh -c "composer install --no-interaction --prefer-dist --optimize-autoloader --no-ansi" || true

echo "設定 Swagger 權限與生成文件..."
docker-compose exec -T app chown -R www-data:www-data storage/api-docs
docker-compose exec -T app chmod -R 775 storage/api-docs
docker-compose exec -T app php artisan l5-swagger:generate

echo "快取與設定清理..."
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan route:clear
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache

echo "執行 migrate..."
docker-compose exec -T app php artisan migrate --force

echo "部署完成！"