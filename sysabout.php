<?php
include_once './common.php';

$pageTitle = $pageTitle." - "."حول الموقع";

function showBody(){
    $dbInfo = (object)UtilityDB::getDBInfo();
    ?>
    <div class='mainareatitle'><h2>تعريف</h2></div>
    <div>
        موقع جامع للكتب الإسلامية.
        يحوي <?=$dbInfo->nbooks?> كتاب في <?=$dbInfo->ncategories?> مصنف لـ <?=$dbInfo->nauthors?> مؤلف.
    </div>
    <div class='mainareatitle'><h2>الكتب المضافة حديثا</h2></div>
    <div>
    <?php
    $books = UtilityDB::getBooks("", -1, -1, 0, 10, "adddate desc, title");
    foreach($books as $id=>$title){
        $titleID = replace($title, " ", "_");
        $bookInfo = UtilityDB::getBookInfo($id);
        $title = shortText($title);
        echo "<div class='book boxitem'><span class='smalltext highlight'>".date('Y-M-d',strtotime($bookInfo["adddate"]))."</span><br><a href='/book/$titleID'>$title</a></div>";
    }
    ?>
    </div>
    <div class='mainareatitle'><h2>الكتب الأكثر قرائة</h2></div>
    <div>
    <?php
    $books = UtilityDB::getBooks("", -1, -1, 0, 10, "accesscount desc, title");
    foreach($books as $id=>$title){
        $titleID = replace($title, " ", "_");
        $bookInfo = UtilityDB::getBookInfo($id);
        if($bookInfo["accesscount"]==0)break;
        $title = shortText($title);
        echo "<div class='book boxitem'><span class='smalltext highlight'>".$bookInfo["accesscount"]." طلب قراءة</span><br><a href='/book/$titleID'>$title</a></div>";
    }
    ?>
    </div>
    <?php
}

function showMainBar() {
    ?>
    <div class="mainbarsection mainbartextsection">
        <h1>حول موقع المكتبة الإسلامية</h1>
    </div>
    <?php
}
?>

