<?php
include_once './common.php';
include_once './systitles.php';

$bookID = $_REQUEST["id"];

if(!is_numeric($bookID)){
    $bookID = urldecode($bookID);
    $bookID = replace($bookID, "_", " ");
    $bookID = UtilityDB::getBookID($bookID);
}

UtilityDB::accessBook($bookID);

$bookInfo = (object)UtilityDB::getBookInfo($bookID);
$pageTitle = $pageTitle." - ".$bookInfo->title;
$bookTitleID = replace($bookInfo->title, " ", "_");

$firstPageID = UtilityDB::getBookFirstPageID($bookID);

$currentPage->pageid = safeGetCookie("pageid_of_book$bookID", $firstPageID);

function showBody(){
    
    global $bookID;
    global $bookTitleID;
    global $baseURL;
    global $currentPage;
    global $firstPageID;
        
    $partsNumbers = UtilityDB::getPartsNumbers($bookID);

    if(array_key_exists("searchkeywords", $_REQUEST)) { 
        writeTitles($bookID, $bookTitleID, -1, $_REQUEST["searchkeywords"]);
        return;
    }
    
    $nParts = count($partsNumbers);
    $hasIntroduction = false;
    if($partsNumbers[0]==0){$nParts--; $hasIntroduction=true;}
        
    $pageText = "صفحة";
    $partText = "جزء";
    $theIntroductionText = "المقدمة";
    $fromText = "من";
    
    $partText = "($pageText $currentPage->pageid)";
    
    if($firstPageID!=$currentPage->pageid) {
    ?>
    <div class='mainareatitle mainareaaction'><a class='redlink' href='book/<?=$bookTitleID?>/<?=$currentPage->pageid?>'>تابع القراءة...</a><?=$partText?></div>
    <?php } else {?>
    <div class='mainareatitle mainareaaction'><a class='redlink' href='book/<?=$bookTitleID?>/<?=$firstPageID?>'>إبدأ القراءة</a></div>
    <?php
    }

    echo "<div class='mainareatitle'><h2>نبذة عن الكتاب ($bookID)</h2></div>";
    
    echo "<div class='pagearea'>";
    
    echo "<p><span class='highlight'>المواضيع</span> : ";
    $categories = UtilityDB::getBookCategories($bookID);
    $i = 0;
    foreach($categories as $id=>$title){
        $titleID = replace($title, " ", "_");
        echo "<a href='/books/$titleID'>$title</a>";
        if($i<count($categories)-1){ 
            echo "<span class='highlight'>&nbsp;,&nbsp;</span>";
        }
        $i++;
    }
    echo "</p>";

    echo "<p><span class='highlight'>المؤلفون</span> : ";
    $authors = UtilityDB::getBookAuthors($bookID);
    $i = 0;
    foreach($authors as $id=>$name){
        $nameID = replace($name, " ", "_");
        echo "<a href='/author/$nameID'>$name</a>";
        if($i<count($authors)-1){ 
            echo "<span class='highlight'>&nbsp;,&nbsp;</span>";
        }
        $i++;
    }
    echo "</p>";
    
    echo "</div>";
    
    generateSocial("$baseURL/book/$bookID");

    //$time = echoTime(0);

    echo "<div class='mainareatitle'><h2>المحتويات</h2></div>";
    
    $count = count($partsNumbers);
    foreach($partsNumbers as $partNumber){
        $id = UtilityDB::getPartFirstPageID($bookID, $partNumber);
        $title = "جزء"." ".$partNumber." "."من"." ".$count;
        if($partNumber==0) { $title = "المقدمة"; $count--;}
        echo "<div class='part smallboxitem'><h2><a href='/book/$bookTitleID/$id'>$title</a></h2></div>";
    }  
    

    echo "<div class='mainareatitle'><h2>المواضيع الرئيسية</h2></div>";
    writeTitles($bookID, $bookTitleID, 0, "");
    
    //echoTime($time, "time");
}

function showMainBar() {
    
    global $bookID;
    global $bookInfo;
    global $bookTitleID;
    global $currentPage;
    
    ?>
    <div class="mainbarsection">
        <h1>
            <?php if(array_key_exists("searchkeywords", $_REQUEST)) { ?>
            <a class='redlink' href='/book/<?=$bookTitleID?>'><?=$bookInfo->title?></a>
            <?php } else { ?>
            <a class='redlink' href='book/<?=$bookTitleID?>/<?=$currentPage->partnumber?>/<?=$currentPage->pagenumber?>'><?=$bookInfo->title?></a>
            <?php } ?>
            <?php if(array_key_exists("searchkeywords", $_REQUEST)) { ?>
            <span class="highlight">&nbsp;/&nbsp;</span>
            [ <?=$_REQUEST["searchkeywords"]?> ]
            <?php } ?>
        </h1>
    </div>
    <div class="mainbarsection">
        <form action="" onsubmit="javascript: window.open('book/<?=$bookTitleID?>/search/'+$('#keywords').val(), '_self'); return false;">
            <input type="text" id="keywords" value="<?=$_REQUEST["searchkeywords"]?>">
            <input type="submit" value="بحث" >
        </form>
    </div>
    <?php
}

?>

