Lion system
====

Lion is all-in-one solution for managing your problems.
The system provides support for various options.
Some of the options that we have developed are:

- automatic notification system for sending notifications on your mobile phone or email
- online reports generating subsystem
- automatical system for sending reports to your address

The system is designed in a way that allows parallel operation of a large number of users, and managing many subsystems based on Global Sensor Networks server to collect data. Also, we allow you to monitor and react in any process you are having.


Installation
==


Lion is based on Yii php Framework (http://www.yiiframework.com/).
It's source can be found at https://github.com/yiisoft/yii.

Lion requires working webserver with php support and some php modules (gd and postgres driver).
On ubuntu this can be installed with:

    sudo apt-get install apache2 libapache2-mod-php5 php5-pgsql php5-gd

after that make sure apache server is started by running :

    service apache2 restart

Change directory to /var/www which is apache default root for serving the files. Clone Yii and Lion sources (or unzip the archives).

    cd /var/www
    git clone https://github.com/janza/lion lion
    git clone https://github.com/yiisoft/Yii Yii


Development database setup
==

Lion was developed for and tested on postgres database.

Install it with:

    sudo apt-get install postgresql

Setup postgres database with following commands:

    cd /var/www/lion/
    sudo -u postgres psql postgres

In psql prompt enter following:

    \password postgres  # write postgress administrator password when prompetd
    CREATE ROLE coldwatch;  # create 'coldwatch' user
    ALTER ROLE coldwatch WITH PASSWORD 'coldwatch';  # set coldwatch user password to 'coldwatch'
    CREATE ROLE arduino_role;  # (optional) create 'arduino_role' user

Now import the initialized database tables still from psql:

    \i /var/www/lion/protected/db.sql

Database setup is after these steps complete.
After this update Lion config file (lion/protected/config/main.php) to point to the correct database user if it's different from the default values above.


Running
==

By going to http://127.0.0.1/lion one should now be able to see Lion start page and to login to the system.
Default user info is:

    username: test
    password: test


