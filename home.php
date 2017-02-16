<?php
if(array_key_exists("page", $_REQUEST) && $_REQUEST["page"]=="ajaxbook")
{
    setcookie("pageid_of_book$bookID", $pageid, strtotime('+365 days'));
}

header('HTTP/1.1 200 OK', TRUE);
header("Status: 200");

include_once './config.php';

$uri = $_SERVER["REQUEST_URI"];

if(strpos($uri, ".php")===false)
{
    $URLParts = explode('/', trim(strtolower($uri),"/"));
    
    if(count($URLParts)>=1){
        $_REQUEST["page"] = $URLParts[0];
    } else {
        $_REQUEST["page"] = "books";
    }
        
    if(count($URLParts)>=2){
        $_REQUEST["id"] = $URLParts[1];
    } else {
        if($_REQUEST["page"]=="books"){
            $_REQUEST["id"] = 0;
        }
    }
    
    if($_REQUEST["page"]=="book"){
        
        if(count($URLParts)>=3 && $URLParts[2]=="search"){
            $_REQUEST["searchkeywords"] = urldecode($URLParts[3]);
        } else {        
            if(count($URLParts)>=3){
                $_REQUEST["pageid"] = $URLParts[2];
            }
        }
    }
    
    if(count($URLParts)>=3 && $_REQUEST["page"]=="authors"){
        if($URLParts[1]=="search"){
            $_REQUEST["searchkeywords"] = urldecode($URLParts[2]);
        }
    }
    
    if(count($URLParts)>=4 && $_REQUEST["page"]=="books"){
        if($URLParts[2]=="search"){
            $_REQUEST["searchkeywords"] = urldecode($URLParts[3]);
        }
    }
}

if(strpos($_REQUEST["page"],"ajax") !== false) {
    
    if($_REQUEST["page"] == "ajaxtitles") {
        include 'systitles.php';
        showAjaxBody();
    }
    
    if($_REQUEST["page"] == "ajaxauthors") {
        include './sysauthors.php';
        showAjaxBody();
    }

    if($_REQUEST["page"] == "ajaxbooks") {
        include './sysbooks.php';
        showAjaxBody();
    }

    if($_REQUEST["page"] == "ajaxbook") {
        include 'syspage.php';
        ?>
        <div id='dynamicarea'>
            <div class='mainbar'>
                <?php showMainBar(); ?>
            </div>
            <div class="mainarea">
                <?php showBody(); ?>
            </div>
        </div>
        <?php
    }
    
    die();
}

$pageTitle = "المكتبة الإسلامية";

if($_REQUEST["page"] == "books"){
    include 'sysbooks.php';
}else if($_REQUEST["page"] == "book" && !array_key_exists("pageid", $_REQUEST)){
    include 'sysbook.php';
}else if($_REQUEST["page"] == "book" && array_key_exists("pageid", $_REQUEST)){
    include 'syspage.php';
}else if($_REQUEST["page"] == "authors"){
    include 'sysauthors.php';
}else if($_REQUEST["page"] == "about"){
    include 'sysabout.php';
}else if($_REQUEST["page"] == "author"){
    include 'sysauthor.php';
}else if($_REQUEST["page"] == "title"){
    include 'syspage.php';
}else if($_REQUEST["page"] == "mybooks"){
    include 'sysmybooks.php';
}

?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
    <head>
        <meta charset="UTF-8">
        <title><?=$pageTitle?></title>
        <link rel="shortcut icon" href="/images/book.png">
        <base href="<?=$baseURL?>">
        <link rel="stylesheet" href="js/share/jquery.share.css" />
        <link rel="stylesheet" type="text/css" href="css/main.css"/>
        <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="js/share/jquery.share.js"></script>
        <script type="text/javascript" src="js/main.js"></script>
    </head>
    <body dir="rtl">
    <?php include_once("analyticstracking.php") ?>
        <div class="headarea">
            <div class="headlogo">
                <a href='<?=$baseURL?>'><img src='/images/books.png'></a>
            </div>
            <div class="headbody">
                <div class="headsection headtitle">
                    <h1><a href='<?=$baseURL?>'>المكتبة الإسلامية</a></h1>
                </div>
                <div class="headmenu">
                    <a href='/'>المكتبة</a> | 
                    <a href='/authors'>المؤلفون</a> | 
                    <a href='/mybooks'>مكتبتي</a> |
                    <a href='/about'>حول الموقع</a>
                </div>
            </div>
        </div>
        <div id='dynamicarea'>
            <div class='mainbar'>
                <?php showMainBar(); ?>
            </div>
            <div class="mainarea"><?php showBody(); ?></div>
        </div>
        <div class='footerarea'>طور بواسطة نورين ميديا  © 2015</div>
    </body>
</html>

