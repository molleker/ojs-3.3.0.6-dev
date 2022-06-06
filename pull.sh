echo 'pulling...'
git pull
cd api
echo 'composer updating(api)...'
composer update
echo 'migrations...'
php artisan migrate --force
cd ../yii
echo 'composer updating(api)...'
composer update
cd ../www/
echo 'composer updating(www)...'
composer update
echo 'npm updating(www)...'
npm update
echo 'gulping...'
gulp
cd ../
echo 'supervisor restart...'
supervisorctl restart all
echo 'change owner...'
chown apache:apache -R *
echo 'FINISHED !'

