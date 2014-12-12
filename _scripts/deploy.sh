#!/bin/bash -x
echo "Running deploy script."
cd /opt/bitnami/apache2
git reset --hard HEAD
git pull origin master
\cp -f _config/ENV-PROD.php _config/ENV.php
touch /cache.flush
chmod 755 _scripts/deploy.sh
echo "Deploy has finished."
exit 0