# استخدام نسخة PHP مع Apache المتوافقة مع مشروعك
FROM php:8.2-apache

# تحديث المستودعات وتثبيت المكتبات اللازمة لمحرك PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# نسخ كافة ملفات المشروع من جهازك (أو مستودع GitHub) إلى مسار الويب في السيرفر
COPY . /var/www/html/

# ضبط الصلاحيات للمجلد لضمان عمل الملفات بشكل صحيح
RUN chown -R www-data:www-data /var/www/html

# تفعيل مود Rewrite الخاص بـ Apache (مهم لروابط PHP)
RUN a2enmod rewrite

# تحديد المنفذ الذي سيعمل عليه السيرفر
EXPOSE 80
