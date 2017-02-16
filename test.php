<?php
header('HTTP/1.1 200 OK', TRUE);
header("Status: 200");
header("Content-Type: text/html; charset=UTF-8");

include_once 'config.php';
include_once 'common.php';

ini_set('max_execution_time', 300000);

generateSitemap(); die();

$time = echoTime(0);

if(file_exists("./data/main.sqlite")===false){
    UtilityDB::generateMain();
}
else {
    UtilityDB::syncMain();
}

$time = echoTime($time, "Initialization");

print_r(UtilityDB::getCategories($bookID));

$time = echoTime($time, "Home");

$bookID = 1;

echo "<p>getBookCategories: ";
print_r(UtilityDB::getBookCategories($bookID));

echo "<p>getBookAuthors: ";
print_r(UtilityDB::getBookAuthors($bookID));

echo "<p>getBookInfo: ";
print_r(UtilityDB::getBookInfo($bookID));

echo "<p>getPartsNumbers: ";
print_r(UtilityDB::getPartsNumbers($bookID));

echo "<p>getPagesIDs: ";
print_r(UtilityDB::getPagesIDs($bookID));

echo "<p>getPage: ";
print_r(UtilityDB::getPage($bookID, 1, 1));

$time = echoTime($time, "View book");
?>

