*ZucchiAdmin*

Admin aggregator module for Zucchi ZF2 Modules.

This is a jump off module for other ZF2 Modules to hook into to provide a web interface for administration

_Installation_

From the root of your ZF2 Skeleton Application run

    ./composer.phar require zucchi/admin 
    
This module will require your vhost to use an AliasMatch in order to load public assets

    AliasMatch /_([^/]+)/(.+)/([^/]+) /path/to/vendor/$2/public/$1/$3
