# ssh -v emailofertaviagem@XXXXX
# sudo -i
# cd /var/www/html/XXXXX/

# Excluir pastas e arquivos ignorados (OPCIONAL).
# rm -r .\vendor\, .\node_modules\, .\composer.lock, .\package-lock.json, .\public\css\, .\public\fonts\, .\public\js\, .\public\mix-manifest.json, .\resources\docs, .\resources\views\scribe, .\public\docs, .\public\vendor, .\public\images;

# Instalar pacotes do NPM e complilar para produção.
# npm i;

#Instalar composer otimizado para produção.
# composer update --optimize-autoloader --no-dev;

# Limpar e cachear aplicação em Laravel para produção.
# php artisan cache:clear;
# php artisan config:clear;
# php artisan event:clear;
# php artisan optimize:clear;
# php artisan route:clear;
# php artisan view:clear;
# php artisan config:cache;
# php artisan event:cache;
# php artisan route:cache;
# php artisan view:cache;
# php artisan storage:link;

# Comandos básicos GIT
# git reset --hard
# git pull --rebase origin <rep>
