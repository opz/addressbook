# Address Book

## Install

Install slim:
```shell
php composer.phar install
```

Install front-end dependencies:
```shell
bower install
```

Create a new config file:
```shell
cp config-default.php config.php
```

Edit config file with database credentials:
```php
$host = 'localhost';
$dbname = 'addressbook';
$dbuser = 'root';
$dbpass = '';
```

Run schema file to create database tables:
```shell
mysql -u<dbuser> -p<dbpass> <dbname> < data/schema.sql
```

## License

The MIT License (MIT)

Copyright (c) 2014 Will Shahda

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
