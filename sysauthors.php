<?php
include_once './common.php';

$pageTitle = $pageTitle." - "."المؤلفون";

function showBody($start = 0){
    
    $searchKeywords = $_REQUEST["searchkeywords"];
    
    $authors = UtilityDB::getAuthors($_REQUEST["searchkeywords"], $start, PAGE_LIMIT+1);
    
    $hasMore = false;
    if(count($authors)>PAGE_LIMIT) { 
        $hasMore = true;
        $authors = array_slice($authors, 0, PAGE_LIMIT);
        $start += PAGE_LIMIT;
    }

    foreach($authors as $id=>$name){
        $nameID = replace($name, " ", "_");
        echo "<div class='author boxitem'><h2><a href='author/$nameID'>$name</a></h2></div>";
    }
    
    if($hasMore) {
        echo "<div class='author boxitem more'><h2><a class=\"redlink\" href=\"javascript:loadItems('home.php?page=ajaxauthors&searchkeywords=$searchKeywords&start=$start');\">أكثر ...</a></h2></div>";
    }
}

function showMainBar() {
    ?>
    <div class="mainbarsection">
        <h1>
            <?php if(array_key_exists("searchkeywords", $_REQUEST)) { ?>
            <a class='redlink' href='/authors'>المؤلفون</a>
            <?php } else { ?>
            <span>المؤلفون</span>
            <?php } ?>
            <?php if(array_key_exists("searchkeywords", $_REQUEST)) { ?>
            <span class="highlight">&nbsp;/&nbsp;</span>
            <span >[ <?=$_REQUEST["searchkeywords"]?> ]</span>
            <?php } ?>
        </h1>
    </div>
    <div class="mainbarsection">
        <form action="" onsubmit="javascript: window.open('authors/search/'+$('#keywords').val(), '_self'); return false;">
            <input type="text" id="keywords" value="<?=$_REQUEST["searchkeywords"]?>">
            <input type="submit" value="بحث">
        </form>
    </div>
    <?php
}

function showAjaxBody() {
    showBody($_REQUEST["start"]);
}
?>