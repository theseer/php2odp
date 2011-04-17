PHP 2 ODP
============

Requirements
------------

PHP: 5.3.0
Extensions: zip,dom


Installation
------------

php2odp should be installed using the [PEAR Installer](http://pear.php.net/). This installer is the backbone of PEAR, which provides a distribution
system for PHP packages, and is shipped with every release of PHP since version 4.3.0.

The PEAR channel (`pear.netpirates.net`) that is used to distribute php2odp needs to be registered with the local PEAR environment.
Furthermore, a component that php2odp depends upon is hosted on the eZ Components PEAR channel (`components.ez.no`).

    [theseer@rikka ~]$ sudo pear channel-discover pear.netpirates.net
    Adding Channel "pear.netpirates.net" succeeded
    Discovery of channel "pear.netpirates.net" succeeded

    [theseer@rikka ~]$ sudo pear channel-discover components.ez.no
    Adding Channel "components.ez.no" succeeded
    Discovery of channel "components.ez.no" succeeded

This has to be done only once. Now the PEAR Installer can be used to install packages from the netpirates channel:

    [theseer@rikka ~]$ sudo pear install theseer/php2odp
    downloading php2odp-0.1.0.tgz ...
    Starting to download php2odp-0.1.0.tgz (16,853 bytes)
    .....done: 16,853 bytes
    downloading fDOMDocument-1.0.1.tgz ...
    Starting to download fDOMDocument-1.0.1.tgz (14,465 bytes)
    ...done: 14,465 bytes
    downloading ConsoleTools-1.6.tgz ...
    Starting to download ConsoleTools-1.6.tgz (869,925 bytes)
    ...done: 869,925 bytes
    install ok: channel://pear.netpirates.net/fDOMDocument-1.0.1
    install ok: channel://components.ez.no/ConsoleTools-1.6
    install ok: channel://pear.netpirates.net/php2odp-0.1.0

After the installation you can find the php2odp source files inside your local PEAR directory; the path in Fedora linux 
usually is `/usr/share/pear/TheSeer`.


Usage
-----

php2odp %version% - Copyright (C) 2011 by Arne Blankerts

Argument with name 'source' is mandatory but was not submitted.

Usage: php2odp [switches] <source.php> <target.odp>

  -h, --help       Prints this usage information
  -v, --version    Prints the version and exits


Usage Sample
------------

    [theseer@rikka ~]$ php2odp test.php demo.odp


