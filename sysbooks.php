<?php

include_once './common.php';

$categoryID = $_REQUEST["id"];

if(!is_numeric($categoryID)){
    $categoryID = urldecode($categoryID);
    $categoryID = replace($categoryID, "_", " ");
    $categoryID = UtilityDB::getCategoryID ($categoryID);
}

$categoryInfo = null;
if($categoryID!=0){
    $categoryInfo = (object)UtilityDB::getCategoryInfo($categoryID);
    $pageTitle = $pageTitle." - ".$categoryInfo->title;
}

function showBody($start = 0){
    
    global $categoryID;
    
    $searchKeywords = "".$_REQUEST["searchkeywords"];
    
    if(strlen($searchKeywords)==0) {
        $categories = UtilityDB::getCategories($_REQUEST["searchkeywords"], $categoryID);

        foreach($categories as $id=>$title){
            $titleID = replace($title, " ", "_");
            echo "<div class='category boxitem'><h2><a href='$baseURL/books/$titleID'>$title</a></h2></div>";
        }
        
        $books = UtilityDB::getBooks("", $categoryID, -1, $start, PAGE_LIMIT+1);
    } else {
        $books = UtilityDB::getBooks($searchKeywords, -1, -1, $start, PAGE_LIMIT+1);
    }
    
    $hasMore = false;
    if(count($books)>PAGE_LIMIT) { 
        $hasMore = true;
        $books = array_slice($books, 0, PAGE_LIMIT);
        $start += PAGE_LIMIT;
    }

    foreach($books as $id=>$title){
        $titleID = replace($title, " ", "_");
        $title = shortText($title, 40);
        echo "<div class='book boxitem'><h2><a href='$baseURL/book/$titleID'>$title</a></h2></div>";
    }
    
    if($hasMore) {
        echo "<div class='book boxitem more'><h2><a class=\"redlink\" href=\"javascript:loadItems('home.php?page=ajaxbooks&id=$categoryID&searchkeywords=$searchKeywords&start=$start');\">أكثر ...</a></h2></div>";
    }
}

function showMainBar() {
    global $categoryID;
    global $categoryInfo;
    ?>
    <div class="mainbarsection" >
        <h1>
            <?php if($categoryID!=0 || array_key_exists("searchkeywords", $_REQUEST)) { ?>
            <a class='redlink' href='/books'>المكتبة</a>
            <?php } else { ?>
            المكتبة
            <?php } ?>
            <?php if($categoryInfo!==null) { ?>
            <?php if($categoryInfo->parentid!=0) { ?>
            <span class="highlight">&nbsp;/&nbsp;</span>
            <a class='redlink' href='books/<?=$categoryInfo->parentid?>'>...</div>
            <?php } ?>
            <span class="highlight">&nbsp;/&nbsp;</span>
            <?=$categoryInfo->title?>
            <?php } ?>
            <?php if(array_key_exists("searchkeywords", $_REQUEST)) { ?>
            <span class="highlight">&nbsp;/&nbsp;</span>
            [ <?=$_REQUEST["searchkeywords"]?> ]
            <?php } ?>
        </h1>
    </div>
    <?php if($categoryID==0) { ?>
    <div class="mainbarsection">
        <form action="" onsubmit="javascript: window.open('books/0/search/'+$('#keywords').val(), '_self'); return false;">
            <input type="text" id="keywords" value="<?=$_REQUEST["searchkeywords"]?>">
            <input type="submit" value="بحث">
        </form>
    </div>
    <?php 
    }
}

function showAjaxBody() {
    showBody($_REQUEST["start"]);
}
?>