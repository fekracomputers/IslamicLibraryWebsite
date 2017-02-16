<?php

include_once './common.php';

$authorID = $_REQUEST["id"];

if(!is_numeric($authorID)){
    $authorID = urldecode($authorID);
    $authorID = replace($authorID, "_", " ");
    $authorID = UtilityDB::getAuthorID($authorID);
}

$authorInfo = (object)UtilityDB::getAuthorInfo($authorID);
$pageTitle = $pageTitle." - ".$authorInfo->name;

function showBody(){
    
    global $authorID;
    global $authorInfo;
    
    echo "<div class='mainareatitle'><h2>نبذة عن المؤلف</h2></div>";

    echo "<div class='pagearea'>";

    if($authorInfo->birthhigriyear>=0){
        echo "<p><span class='separator'>سنة المولد (هجري)</span> : $authorInfo->birthhigriyear</p>";
    }
    if($authorInfo->deathhigriyear>=0){
        echo "<p><span class='highlight'>سنة الوفاة (هجري)</span> : $authorInfo->deathhigriyear</p>";
    }
    echo "$authorInfo->information";

    $books = UtilityDB::getBooks("", -1, $authorID);

    echo "</div>";
    
    generateSocial("$baseURL/author/$authorID");
    
    echo "<div class='mainareatitle'><h2>مؤلفاته</h2></div>";

    foreach($books as $id=>$title){
        $titleID = replace($title, " ", "_");
        $title = shortText($title, 40);
        echo "<div class='book boxitem'><h2><a href='/book/$titleID'>$title</a></h2></div>";
    }    
}

function showMainBar() {

    global $authorInfo;

    ?>
    <div class="mainbarsection">
        <h1>
            <a class='redlink' href='/authors'>المؤلفون</a>
            <span class="highlight">&nbsp;/&nbsp;</span>
            <?=$authorInfo->name?>
        </h1>
    </div>
    <?php
}
?>

