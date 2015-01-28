chgrp -R www-data $HOME 
find $HOME/what-hood/app -type f -exec chmod 644 {} \;
find $HOME/what-hood/app -type d -exec chmod 755 {} \;
find $HOME/what-hood/app/data/DoctrinORMModule -type d -exec chmod 775 {} \;
chmod 755 $HOME/what-hood/app/logs
