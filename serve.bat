@echo off
echo Starting BodaLoan with increased upload limits...
php -d upload_max_filesize=10M -d post_max_size=20M artisan serve
pause
