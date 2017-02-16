<?php
include_once './common.php';

$bookID = $_REQUEST["id"];

if(!is_numeric($bookID)){
    $bookID = urldecode($bookID);
    $bookID = replace($bookID, "_", " ");
    $bookID = UtilityDB::getBookID($bookID);
}

$bookInfo = (object)UtilityDB::getBookInfo($bookID);
$pageTitle = $pageTitle." - ".$bookInfo->title;

$bookTitleID = replace($bookInfo->title, " ", "_");
$pageID = $_REQUEST["pageid"];

$pageInfo = UtilityDB::getPageInfo($bookID, $pageID);
if($pageInfo===false)
    die("errorpagenotfound");

$currentPage->pageid = $pageID;
$currentPage->firstpageid = UtilityDB::getBookFirstPageID($bookID);
$currentPage->nextpageid = UtilityDB::getBookNextPageID($bookID, $pageID);
$currentPage->previouspageid = UtilityDB::getBookPreviousPageID($bookID, $pageID);
$currentPage->lastpageid = UtilityDB::getBookLastPageID($bookID);
$currentPage->partnumber = $pageInfo["partnumber"];
$currentPage->pagenumber = $pageInfo["pagenumber"];

function generateNavigator() {
    global $bookID;
    global $bookTitleID;
    global $currentPage;

    echo "<div class='pagenavigator'>";

    if($currentPage->pageid>$currentPage->firstpageid){
        $url = "$baseURL/book/$bookTitleID/$currentPage->firstpageid";
        echo "<a href='$url' onclick='javascript:loadPage(\"home.php?page=ajaxbook&id=$bookID&pageid=$currentPage->firstpageid\", \"$url\");  return false;'><img id='firstimg' title='الصفحة الأولى' src='images/firstpage.png'></a>";
    } else {
        echo "<img id='firstimg' class='inactive' src='images/firstpage.png'>";
    }
    if($currentPage->pageid>$currentPage->firstpageid){
        $url = "$baseURL/book/$bookTitleID/$currentPage->previouspageid";
        echo "<a href='$url' onclick='javascript:loadPage(\"home.php?page=ajaxbook&id=$bookID&pageid=$currentPage->previouspageid\", \"$url\");  return false;'><img id='nextimg' title='الصفحة السابقة' src='images/previouspage.png'></a>";
    } else {
        echo "<img id='nextimg' class='inactive' src='images/previouspage.png'>";
    }
    ?>
    <div class="pagenavigatorselectdiv">
        <input type="text" class="navpagenumber" id="navpagenumber" onkeypress="javascript: if(event.which==13)loadPage('home.php?page=ajaxbook&id=<?=$bookID?>&pageid='+$('#navpagenumber').val(),'<?=$baseURL?>/book/<?=$bookTitleID?>/'+$('#navpagenumber').val());" value="<?=$currentPage->pageid?>" oldvalue="<?=$currentPage->pageid?>" style="text-align: center; width: 60pt;">
    </div>
    <?php
    if($currentPage->pageid<$currentPage->lastpageid){
        $url = "$baseURL/book/$bookTitleID/$currentPage->nextpageid";
        echo "<a href='$url' onclick='javascript:loadPage(\"home.php?page=ajaxbook&id=$bookID&pageid=$currentPage->nextpageid\", \"$url\"); return false;'><img id='previousimg' title='الصفحة التالية' src='images/nextpage.png'></a>";
    } else {
        echo "<img id='previousimg' class='inactive' src='images/nextpage.png'>";
    }
    if($currentPage->pageid<$currentPage->lastpageid){
        $url = "$baseURL/book/$bookTitleID/$currentPage->lastpageid";
        echo "<a href='$url' onclick='javascript:loadPage(\"home.php?page=ajaxbook&id=$bookID&pageid=$currentPage->lastpageid\", \"$url\"); return false;'><img id='lastimg' title='الصفحة الأخيرة' src='images/lastpage.png'></a>";
    } else {
        echo "<img id='lastimg' class='inactive' src='images/lastpage.png'>";
    }
    
    echo "</div>";
}

function showBody(){
    
    global $bookID;
    global $bookTitleID;
    global $currentPage;
    global $partsNumbers;
    global $pagesNumbers;
    global $baseURL;

    $page = UtilityDB::getPage($bookID, $currentPage->pageid);
    ?>
    <div>
        <?php generateNavigator(true); ?>
    </div>
    <div class="pagearea" id="pagearea">
        <?=$page;?>
    </div>
    <div>
        <?php generateNavigator(true); ?>
    </div>
    <?php
    
}

function showMainBar() {
    
    global $bookID;
    global $bookInfo;
    global $bookTitleID;
    global $currentPage;;
    global $partsNumbers;
    global $pagesNumbers;
    
    $nParts = count($partsNumbers);
    $hasIntroduction = false;
    if($partsNumbers[0]==0){$nParts--; $hasIntroduction=true;}
        
    $pageText = "صفحة";
    $partText = "جزء";
    $theIntroductionText = "المقدمة";
    $fromText = "من";
    
    if ($currentPage->pageid==0) {
        $partText = "($pageText $currentPage->pageid $fromText $theIntroductionText)";
    } else if($nParts>1) {
        $partText = "($pageText $currentPage->pageid $fromText $partText $currentPage->partnumber)";
    } else {
        $partText = "($pageText $currentPage->pageid)";
    }
    
    ?>
    <div class="mainbarsection mainbartextsection">
        <h1>
            <a class='redlink' href='/book/<?=$bookTitleID?>'><?=$bookInfo->title?></a>&nbsp;<?=$partText?>
        </h1>
    </div>
    <?php
}
?>
