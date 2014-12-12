#!/bin/bash -x
echo "Running deploy script."
cd /opt/bitnami/apache2
git reset --hard HEAD
git pull
\cp -f _config/ENV-PROD.php _config/ENV.php
touch /cache.flush
echo "Deploy has finished."
exit 0