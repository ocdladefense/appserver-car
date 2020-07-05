# Appserver CAR Module

## Installation
### Summary
Installation consists of a PHP executable component and a MySQL database component. 

### PHP Executable
1.  Clone this repository into your local Appserver's /modules directory.
2.  Rename the newly-downloaded repository directory to car/.
3.  Validate that the config/config.php contains database-related options for setting the database name, username and password.
    * This section should be completed with the values chosen in the "MySQL Database" section, below.

### MySQL Database
1. Identify the data/sql folder and data/sql/car.sql file in this repo.
    * This SQL file contains data to be imported into your Appserver's database.
2.  Open your WAMP --> phpMyAdmin database utility
3.  Login using the default "superuser" credentials:
    *  username: root
    *  password: [the default root password is empty]
4.  Familiarize yourself with the user interface
5.  Create a new database; you can name it anything, but let's give preference to "ocdla"
6. Optionally use phpMyAdmin to create a user with access to the new database
    * A MySQL user consists of a username, password and a set of privileges for a given database or database.table combination.
7.  Identify the import functionality and either select the car.sql file for upload/import or copy and paste its contents into the phpMyAdmin interface; execute the import.
    * The car.sql imports both the new car table/schema and data (rows) into the database server.
8.  Use phpMyAdmin to browse the newly-imported data; validate that the data from car.sql was imported successfully.
9.  Configure the Appserver's config/config.php database section with the values for database name and username and password.

### Validate installation
To validate installation of the car repo + data, navigate to http://appserver/car.



## Example endpoints - this project

### View list of case reviews
https://trust.ocdla.org/cars


## Example endpoints - previous project, parsing

### URL to insert some CARs into the database
https://trust.ocdla.org/insert-bulk-case-reviews/750

### URL to insert a single days worth of case reviews
https://trust.ocdla.org/insert-single-case-reviews/12/6/2018

### View the list of the urls tested for a range of days
http://appserver/car-urls-range/10

### View the list of the urls tested for a specific given date
http://appserver/car-urls/3/11/2020

### View the list of the urls tested for a range of days with the status code
// Something is not working with this url...
http://appserver/test-car-urls/10
