<?php
include_once './common.php';

function writeTitles($bookID, $bookTitleID, $parentID, $searchKeywords)
{
    global $baseURL;
        
    $titles = UtilityDB::getTitles($bookID, $searchKeywords, $parentID);
    if(count($titles)==0)return;
    echo "<div class='booktitlecontainer'>";
    if(count($titles)>0){
        foreach($titles as $title){
            ?>
            <div class='booktitle'>
                <div>
                    <?php if($title->hasChilds && strlen($searchKeywords)==0) {?>
                    <a href="javascript:loadTitles('home.php?page=ajaxtitles&id=<?=$bookID?>&titleid=<?=$title->id?>', 'title<?=$title->id?>');">
                        <img class='booktitleimg' src="images/item.png">
                    </a>
                    <?php } else { ?>
                    <img class='booktitleimg inactive' src="images/item.png">
                    <?php }?>
                </div>
                <div class='booktitletextarea'>
                    <div>
                        <a id="<?=$bookID/$title->id?>" href="<?="$baseURL/book/$bookTitleID/$title->pageid"?>">
                            <?=$title->title?>
                        </a>
                    </div>
                    <div id='title<?=$title->id?>' class='booktitlechilds'>
                        <?php if($title->hasChilds && strlen($searchKeywords)==0) {?>
                        <img id="load" src="images/load.gif" class='booktitleloadimg'>
                        <?php }?>
                    </div>
                </div>
            </div>
            <?php
        }
    }
    echo "</div>";
}

function showAjaxBody() {
    
    $bookID = $_REQUEST["id"];
    
    if(!is_numeric($bookID)){
        $bookID = urldecode($bookID);
        $bookID = replace($bookID, "_", " ");
        $bookID = UtilityDB::getBookID($bookID);
    }

    $bookInfo = (object)UtilityDB::getBookInfo($bookID);
    $bookTitleID = replace($bookInfo->title, " ", "_");
    
    writeTitles($bookID, $bookTitleID, $_REQUEST["titleid"], "");
}
?>

