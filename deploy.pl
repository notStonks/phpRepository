#!/bin/bash
echo "Content-type: text/plain\n";
echo '';
cd /var/www/domains/m.wallets.qzo.su/ || exit > /dev/null
git reset --hard > /dev/null
git pull https://github.com/notStonks/phpRepository.git > /dev/null
chmod -R 755 /var/www/domains/m.wallets.qzo.su
#git@bitbucket.org:gafurovstudio/g2.git
