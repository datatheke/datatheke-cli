Datatheke-cli
=============

ABOUT
-----
Datatheke-cli is a tool to interact with datatheke.com data.

LICENSE
-------
MIT

INSTALL
-------
```sh
git clone https://github.com/datatheke/datatheke-cli.git
cd datatheke-cli
composer install # require [composer](https://getcomposer.org/)
```

USAGE
-----
```
# List all commands
bin/datatheke

# Get help for one command
bin/datatheke help create

# List my libraries
bin/datatheke browse

# List collections of a library
bin/datatheke browse 50ce0fbb138a76072c00000c

# List items of a collection
bin/datatheke browse 50ce0fbb138a76072c00000c/50ce113e138a76302f000009
```

BUILD PHAR ARCHIVE
------------------
```sh
box build # require [box](http://box-project.org/)
```
