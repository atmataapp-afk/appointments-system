# استخدام نسخة PHP مع Apache
FROM php:8.2-apache

# تثبيت مكتبات PostgreSQL وتفعيل المحركات اللازمة
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# نسخ ملفات المشروع إلى السيرفر
COPY . /var/www/html/

# ضبط الصلاحيات وتفعيل مود Apache Rewrite
RUN chown -R www-data:www-data /var/www/html && a2enmod rewrite

EXPOSE 80
