#!/bin/bash
echo "Content-type: text/plain\n";
echo '';
cd /war/www/server/wallets.su/ || exit > /dev/null
git reset --hard > /dev/null
git pull https://github.com/notStonks/phpRepository.git > /dev/null
chmod -R 755 /war/www/server/wallets.su
#git@bitbucket.org:gafurovstudio/g2.git
