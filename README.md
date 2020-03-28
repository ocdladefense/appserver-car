# Appserver CAR Module
// URL to insert some CARs into the database
https://trust.ocdla.org/insert-bulk-case-reviews/750


// URL to insert a single days worth of case reviews
https://trust.ocdla.org/insert-single-case-reviews/12/6/2018


// View list of case reviews
https://trust.ocdla.org/cars

//View the list of the urls tested for a range of days
http://appserver/car-urls-range/10

//View the list of the urls tested for a specific given date
http://appserver/car-urls/3/11/2020

//View the list of the urls tested for a range of days with the status code
//Something is not working with this url...
http://appserver/test-car-urls/10

//View the html for specific date





//This is a maybe depending on resolution of this issue.
<!-- Database columns "circut" and "judges" should be nullable at the database level.  Some earlier years of case reviews do not have data
for those fields. -->