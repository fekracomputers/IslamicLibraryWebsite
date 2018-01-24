<?php

define("PAGE_LIMIT", 10);

class UtilityDB {
    
    private static $mainDB = NULL;
    private static $bookDB = NULL;
    private static $bookID = 0;

    /**
     * @brief generate limit seaction of SQL statment
     */
    private static function genSQLLimit($start = -1, $limit = -1)
    {
        $SQLLimit = "";
        if($start>=0 && $limit>=0){
            $SQLLimit = "LIMIT $limit OFFSET $start";
        }
        return $SQLLimit;
    }

    /**
     * @brief generate limit seaction of SQL statment
     */
    private static function genSQLFilter($filter)
    {
        $filter = getSQLSearchText($filter);
        return mb_ereg_replace(" ", "%", " $filter ");
    }

    /**
     * @brief get main db object
     */
    private static function getMainDB()
    {
        global $baseDataFolder;
        if(UtilityDB::$mainDB!==NULL){
            return UtilityDB::$mainDB;
        }
        try{
            UtilityDB::$mainDB = new SQLite3($baseDataFolder."/main.sqlite");
        }
        catch(Exception $e){
            die("Cannot open main database");
        }
        return UtilityDB::$mainDB;
    }
    
    /**
     * @brief get main db object
     */
    private static function getBookDB($bookID){
        global $baseDataFolder;
        
        if(UtilityDB::$bookID==$bookID){
            return UtilityDB::$bookDB;
        }

        try{
            UtilityDB::$bookDB = new SQLite3($baseDataFolder."/books/$bookID.sqlite");
        }
        catch(Exception $e){
            die("Cannot open book database");
        }
        UtilityDB::$bookID = $bookID;
        return UtilityDB::$bookDB;
    }
    
    /**
     * @brief get sql query single value
     */
    private static function dbSQLVal($db, $sql)
    {
        $results = $db->query($sql);
        if($results===false){
            return false;
        }
        
        $row = $results->fetchArray();
        if($row===false){
            return false;
        }
        
        return $row[0];
    }
    
    /**
     * @brief get sql query row
     */
    private static function dbSQLRow($db, $sql)
    {
        $results = $db->query($sql);
        if($results===false){
            return false;
        }
        
        $row = $results->fetchArray(SQLITE3_ASSOC);
        if($row===false){
            return false;
        }
        
        return $row;
    }
    
    /**
     * @brief get sql query row
     */
    private static function dbSQLRows($db, $sql)
    {
        $results = $db->query($sql);
        if($results===false){
            return false;
        }
        
        $all = array();
        while($row = $results->fetchArray(SQLITE3_ASSOC)){
            $all[] = (object)$row;
        }
        
        return $all;
    }

    /**
     * @brief get sql query associative values
     */
    private static function dbSQLAssociative ($db, $sql)
    {
        $results = $db->query($sql);
        if($results===false){
            return false;
        }
        
        $all = array();
        while($row = $results->fetchArray(SQLITE3_NUM)){
            $all[$row[0]] = $row[1];
        }
        
        return $all;
    }
    
    /**
     * @brief get sql query array values
     */
    private static function dbSQLArray ($db, $sql)
    {
        $results = $db->query($sql);
        if($results===false){
            return false;
        }
        
        $all = array();
        while($row = $results->fetchArray(SQLITE3_NUM)){
            $all[] = $row[0];
        }
        
        return $all;
    }

    /**
     * @brief get book information value
     */
    private static function dbBookInfo($db, $name)
    {
        return UtilityDB::dbSQLVal($db, "SELECT value FROM info WHERE name='$name'");
    }
    
    /**
     * @brief get category id given category title
     */
    public static function getCategoryID($title)
    {
        return UtilityDB::dbSQLVal(UtilityDB::getMainDB(), "SELECT id FROM categories WHERE title = '$title';");
    }

    /**
     * @brief get book id given book title
     */
    public static function getBookID($title)
    {
        return UtilityDB::dbSQLVal(UtilityDB::getMainDB(), "SELECT id FROM books WHERE title = '$title';");
    }

    /**
     * @brief get author id given author name
     */
    public static function getAuthorID($name)
    {
        return UtilityDB::dbSQLVal(UtilityDB::getMainDB(), "SELECT id FROM authors WHERE name = '$name';");
    }

    /**
     * @brief get categories (id, title) with given parent id, start and limit (ignore start if nulll. ignore limit if null )
     */
    public static function getCategories($filter = "", $parentCategoryID = -1, $start = -1, $limit = -1)
    {
        $SQLLimit = UtilityDB::genSQLLimit($start, $limit);
        $SQLFilter = UtilityDB::genSQLFilter($filter);
        $SQLParent = ($parentCategoryID<0)?"1=1":"parentid = $parentCategoryID";
        return UtilityDB::dbSQLAssociative(UtilityDB::getMainDB(), "SELECT id, title FROM categories WHERE $SQLParent AND title like ('$SQLFilter') ORDER BY title $SQLLimit;");
    }
        
    /**
     * @brief get authors (id, name) for given filter, start and limit (ignore start if nulll. ignore limit if null )
     */
    public static function getAuthors($filter = "", $start = -1, $limit = -1)
    {
        $SQLLimit = UtilityDB::genSQLLimit($start, $limit);
        $SQLFilter = UtilityDB::genSQLFilter($filter);
        return UtilityDB::dbSQLAssociative(UtilityDB::getMainDB(), "SELECT id, name FROM authors WHERE name like ('$SQLFilter') ORDER BY name $SQLLimit;");
    }

    /**
     * @brief get books (id, title) for given filter, category id, author id, start and limit (ignore start if nulll. ignore limit if null )
     */
    public static function getBooks($filter = "", $categoryID = -1, $authorID = -1, $start = -1, $limit = -1, $orderby = "title")
    {
        $SQLLimit = UtilityDB::genSQLLimit($start, $limit);
        $SQLFilter = UtilityDB::genSQLFilter($filter);
        $SQLCategory =  ($categoryID<0)?"1=1":"id IN(SELECT bookid FROM bookscategories WHERE categoryid = $categoryID)";
        $SQLAuthor =  ($authorID<0)?"1=1":"id IN(SELECT bookid FROM booksauthors WHERE authorid = $authorID)";
        return UtilityDB::dbSQLAssociative(UtilityDB::getMainDB(), "SELECT id, title FROM books WHERE $SQLAuthor AND $SQLCategory and title like ('$SQLFilter') ORDER BY $orderby $SQLLimit;");
    }
    
    /**
     * @brief get categories (id, title) for given book id
     */
    public static function getBookCategories($bookID)
    {
        return UtilityDB::dbSQLAssociative(UtilityDB::getMainDB(), "SELECT id, title FROM categories WHERE id IN(SELECT categoryid FROM bookscategories WHERE bookid = $bookID) ORDER BY title;");
    }

    /**
     * @brief get authors (id, name) for given book id
     */
    public static function getBookAuthors($bookID)
    {
        return UtilityDB::dbSQLAssociative(UtilityDB::getMainDB(), "SELECT id, name FROM authors WHERE id IN(SELECT authorid FROM booksauthors WHERE bookid = $bookID) ORDER BY name;");
    }

    /**
     * @brief get category (id, title, ...) for given category id
     */
    public static function getCategoryInfo($categoryID)
    {
        return UtilityDB::dbSQLRow(UtilityDB::getMainDB(), "SELECT * FROM categories WHERE id = $categoryID;");
        
    }

    /**
     * @brief get author (id, name, information, birthhigriyear, deathhigriyear, ...) for given author id
     */
    public static function getAuthorInfo($authorID)
    {
        return UtilityDB::dbSQLRow(UtilityDB::getMainDB(), "SELECT * FROM authors WHERE id = $authorID;");
    }

    /**
     * @brief get book (id, title) for given book id
     */
    public static function accessBook($bookID)
    {
        return UtilityDB::getMainDB()->exec("UPDATE books SET accesscount = accesscount + 1 WHERE id = $bookID;");
    }

    /**
     * @brief get book (id, title) for given book id
     */
    public static function getBookInfo($bookID)
    {
        return UtilityDB::dbSQLRow(UtilityDB::getMainDB(), "SELECT * FROM books WHERE id = $bookID;");
    }

    /**
     * @brief get titles (id, title) for given book id, parent id, start, limit (ignore start if nulll. ignore limit if null )
     */
    public static function getTitles($bookID, $filter = "", $parentTitleID = -1, $start = -1, $limit = -1, $orderBy = "id")
    {
        $SQLLimit = UtilityDB::genSQLLimit($start, $limit);
        $SQLFilter = UtilityDB::genSQLFilter($filter);
        $SQLParent = ($parentTitleID<0)?"1=1":"parentid = $parentTitleID";
        $titles = UtilityDB::dbSQLRows(UtilityDB::getBookDB($bookID), "SELECT id, title, pageid FROM titles WHERE  $SQLParent AND title like ('$SQLFilter') ORDER BY $orderBy $SQLLimit;");
        $titlesIDs = "";
        foreach ($titles as $title) {
            if(strlen($titlesIDs)>0) {
                $titlesIDs = $titlesIDs.", $title->id";
            } else {
                $titlesIDs = "$title->id";
            }
        }
        $parentsTitlesIDs = UtilityDB::dbSQLArray(UtilityDB::getBookDB($bookID), "SELECT DISTINCT parentid FROM titles WHERE  parentid IN($titlesIDs) ORDER BY parentid;");
        foreach ($titles as $title) {
            if(array_search($title->id, $parentsTitlesIDs)!==false) {
                $title->hasChilds = 1;
            } else {
                $title->hasChilds = 0;
            }
        }
        return $titles;
    }
    
    /**
     * @brief get title (partnumber, pagenumber) for given book id, title id
     */
    public static function getTitleReference($bookID, $titleID)
    {
        return UtilityDB::dbSQLRow(UtilityDB::getBookDB($bookID), "SELECT partnumber, pagenumber FROM titles WHERE id = $titleID;");
    }
    
    /**
     * @brief get book first page id
     */
    public static function getBookFirstPageID($bookID)
    {
        return UtilityDB::dbSQLVal(UtilityDB::getBookDB($bookID), "SELECT id FROM pages ORDER BY id limit 1;");
    }    
    
    /**
     * @brief get book last page id
     */
    public static function getBookLastPageID($bookID)
    {
        return UtilityDB::dbSQLVal(UtilityDB::getBookDB($bookID), "SELECT id FROM pages ORDER BY id DESC limit 1;");
    }   
    
    /**
     * @brief get book first page id
     */
    public static function getBookNextPageID($bookID, $pageID)
    {
        return UtilityDB::dbSQLVal(UtilityDB::getBookDB($bookID), "SELECT id FROM pages WHERE id>$pageID ORDER BY id limit 1;");
    }    
    
    /**
     * @brief get book last page id
     */
    public static function getBookPreviousPageID($bookID, $pageID)
    {
        return UtilityDB::dbSQLVal(UtilityDB::getBookDB($bookID), "SELECT id FROM pages WHERE id<$pageID ORDER BY id DESC limit 1;");
    }   

    /**
     * @brief get page info
     */
    public static function getPageInfo($bookID, $pageID)
    {
        return UtilityDB::dbSQLRow(UtilityDB::getBookDB($bookID), "SELECT id, partnumber, pagenumber FROM pages WHERE id = $pageID;");
    }
    
    /**
     * @brief get parts list for given book id
     */
    public static function getPartsNumbers($bookID)
    {
        return UtilityDB::dbSQLArray(UtilityDB::getBookDB($bookID), "SELECT DISTINCT(partnumber) FROM pages ORDER BY partnumber;");
    }
    
    /**
     * @brief get part first page id
     */
    public static function getPartFirstPageID($bookID, $partNumber)
    {
        return UtilityDB::dbSQLVal(UtilityDB::getBookDB($bookID), "SELECT id FROM pages WHERE partnumber = $partNumber ORDER BY id LIMIT 1;");
    }

    /**
     * @brief get navigation pages numbers (page, first, last, next, previous) for the given page. If give pageNumber is null it assueme it is first page
     */
    public static function getPagesIDs($bookID)
    {
        return UtilityDB::dbSQLArray(UtilityDB::getBookDB($bookID), "SELECT id FROM pages ORDER BY id;");
    }

    /**
     * @brief get page for the given book id, part number, page number
     */
    public static function getPage($bookID, $pageID)
    {
        return UtilityDB::dbSQLVal(UtilityDB::getBookDB($bookID), "SELECT page FROM pages WHERE id = $pageID;");
    }
    
    /**
     * @brief get page for the given book id, part number, page number
     */
    public static function getDBInfo()
    {
        $dbInfo = array();
        $dbInfo["nbooks"] = UtilityDB::dbSQLVal(UtilityDB::getMainDB(), "SELECT count(*) FROM books;");
        $dbInfo["ncategories"] = UtilityDB::dbSQLVal(UtilityDB::getMainDB(), "SELECT count(*) FROM categories;");
        $dbInfo["nauthors"] = UtilityDB::dbSQLVal(UtilityDB::getMainDB(), "SELECT count(*) FROM authors;");
        return $dbInfo;
    }
}
?>
