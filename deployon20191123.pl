#!/bin/bash
echo "Content-type: text/plain\n";
echo '';
cd /var/www/domains/g2.qzo.su/ || exit > /dev/null
git reset --hard > /dev/null
git pull ssh://gafurovstudio@bitbucket.org/gafurovstudio/g2.git develop > /dev/null
#git@bitbucket.org:gafurovstudio/g2.git
