<?php
include_once './common.php';

$pageTitle = $pageTitle." - "."مكتبتي";

function showBody(){
    $booksIDs = array();
    foreach ($_COOKIE as $key => $value) { 
        if(HS($key, "pageid_of_book")) {
            $bookID = replace($key, "pageid_of_book", "");
            $bookInfo = UtilityDB::getBookInfo($bookID);
            if($bookInfo!==false) {
                $booksIDs[$bookID] = $bookInfo["title"]; 
            }
        }
    }
    
    echo "<div class='hint'>تتطلب خدمة مكتبتي أن لا يوقف المستخدم خاصية الـ (cookies) أو أن يتأكد من تشغيلها في متصفح الإنترنت.</div>";
    
    foreach($booksIDs as $bookID=>$title){
        $titleID = replace($title, " ", "_");
        $title = shortText($title);
        echo "<div id='book$bookID' class='book boxitem boxsmallertext'><a href='javascript:clearMyBook($bookID);' style='color:darkred;'>(clear)</a><br/><a href='book/$titleID'>$title</a></div>";
    }
}

function showMainBar() {
    ?>
    <div class="mainbarsection mainbartextsection">
        <h1>مكتبتي</h1>
    </div>
    <?php
}
?>

