<?php

include_once './config.php';
include_once './UtilityDB.php';

//Generate Text Line Breaks
function GTLB($text){
    return mb_ereg_replace("\n", "<br>", $text);
}

//Create Short Text
function ST($text, $maxlength){
    if(mb_strlen($text, 'utf8')<=$maxlength){
        return $text;
    }
    return mb_substr($text, 0, $maxlength-3, 'utf8')."...";
}

/**
 * @brief get cookie for key or empty string if cookie doesn't exist
 */
function safeGetCookie($key, $defaultValue = "")
{
    if(isset($_COOKIE[$key])){
        return $_COOKIE[$key];
    }
    return $defaultValue;
}

/**
 * @brief get session 
 */
function safeGetSession($key, $defaultValue = "")
{
    if(isset($_SESSION[$key])){
        return $_SESSION[$key];
    }
    return $defaultValue;
}

/**
 * @brief generate random code
 */
function genRandCode($length)
{
    $letters = "ABCDEFGHIJKLMNPQRSTXYZ12345678";
    $code = ""; 
    for ($i = 0; $i <$length; $i++) 
    { 
        $iletter = rand(0,  strlen($letters)-1);
        $code .= substr($letters, $iletter, 1); 
    }
    return $code;
}

/**
 * @brief generate random number
 */
function genRandNumber($length)
{
    $letters = "12345678";
    $code = ""; 
    for ($i = 0; $i <$length; $i++) 
    { 
        $iletter = rand(0,  strlen($letters)-1);
        $code .= substr($letters, $iletter, 1); 
    }
    return $code;
}

/**
 * @brief check if main text contains search text
 */
function HS($mainText, $searchText)
{
    return (mb_strpos($mainText, $searchText)!==false);
}

/**
 * @brief check if main text contains search text (case sensitive)
 */
function HSI($mainText, $searchText)
{
    return (mb_stripos($mainText, $searchText)!==false);
}

/**
 * @brief check if main text starts with search text
 */
function SW($mainText, $searchText)
{
    return (mb_strpos($mainText, $searchText)===0);
}

/**
 * @brief check if main text starts with search text (case sensitive)
 */
function SWI($mainText, $searchText)
{
    return (mb_stripos($mainText, $searchText)===0);
}

/**
 * @brief convert regular time into readable one 
 */
function time2text($timestamp)
{
    $totalSeconds = mktime() - $timestamp;
    $nHours = (int)($totalSeconds/3600);
    $nMinutes = (int)(($totalSeconds%3600)/60);
    $nSeconds = (int)($totalSeconds%60);
    if($nHours==0 && $nMinutes==0 && $nSeconds==0){
        return KW("write now");
    }
    else if($nHours==0 && $nMinutes==0){
        return "".$nSeconds." ".KW("seconds ago");
    }
    else if($nHours==0){
        return "".$nMinutes." ".KW("minutes ago");
    }
    else if($nHours<24){
        return "".$nHours." ".KW("hours ago");
    }

    return date("F j, Y, g:i a", (int)$timestamp);
}

/**
 * @brief find and replace string
 */
function replace($text, $oldpart, $newpart){
    return mb_ereg_replace($oldpart, $newpart, $text);
}

function server2ClientTime($time)
{
    return $time - 60 * safeGetSession("timeZoneOffset");
}

function client2ServerTime($time)
{
    return $time + 60 * safeGetSession("timeZoneOffset");
}

function reverse($sItems, $delimiter = ",")
{
    $items = explode(",", $sItems);
    $sItems = "";
    for($i=count($items)-1;$i>=0;$i--)
    {
        $sItems = $sItems.$items[$i].(($i==0)?"":",");
    }
    return $sItems;
}

function isEmptyObject($object)
{
    $vars = get_object_vars($object);
    foreach($vars as $key=>$value)
    {
        if(!empty($value))
            return false;
    }
    return true;
}

/**
 * @brief hash the password
 */
function hashPass($password){
    return hash("md5", $password);
}

/**
 * @brief convert rgba color 'rgba(..,..,..,..)' into hex #...... 
 */
function rgbaToHex($string){
	//rgba(240, 201, 201, 0.42)
	$string = str_replace('rgba(', '', $string);
	$string = str_replace(')', '', $string);
	$arr = explode(',', $string);
	$output = array();
	$i = 0;
	for($i = 0; $i<3; $i++){
		$output[$i] = dechex((int)$arr[$i]);
		if(strlen($output[$i])==1)
		{
			$output[$i] .= '0';
		}
	}
	$alpha = $arr[3];
	$hexCol = implode('', $output);
	
	$out = array();
	$out[0] = $hexCol;
    $out[1] = $alpha;
    $out[2] = dechex((int)($alpha * 0xFF));
	return $out;
}

define("ALLOWED_SEARCH_LETERS", " ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789\.\,\?\!\-\*\+\=\/\\\_\$\#\@\{\}\[\]\(\)\|\<\>\&\^\%ابتثجحخدذرزسشصضطظعغفقكلمنهويئؤءآأإةى");
define("ALLOWED_SQL_SEARCH_LETERS", " ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789ابتثجحخدذرزسشصضطظعغفقكلمنهويئؤءآأإةى");

function getHTMLSearchText($content)
{
    $text = strip_tags($content);
    $text = html_entity_decode($text);
    $text = mb_ereg_replace("[^".ALLOWED_SEARCH_LETERS."]","",$text);
    return mb_trim($text, " ");
}

function getSearchText($content)
{
    return mb_ereg_replace("[^".ALLOWED_SEARCH_LETERS."]","",$content);
}

function getSQLSearchText($content)
{
    return mb_ereg_replace("[^".ALLOWED_SQL_SEARCH_LETERS."]","",$content);
}

function echoTime($time, $msg = "")
{
    if($time>0)
    {
        $duration = (int)(1000.0 * microtime(true) - $time);
        echo "<p>$msg : $duration ms </p>";
    }
    $time = 1000.0 * microtime(true);
    return (int)$time;
}

function mb_trim($string, $trim_chars){
    return preg_replace('/^['.$trim_chars.']*(?U)(.*)['.$trim_chars.']*$/u', '\\1',$string);
}

function isMobileBrowser () {
    $user_agent = strtolower ( $_SERVER['HTTP_USER_AGENT'] );
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $user_agent);
}

function getFiles($dir, $extFilter) 
{
    $filesPaths = array();
    $dir=$dir."/";
    $dir = str_replace("\\", "/", $dir);
    $dir = str_replace("//", "/", $dir);

    $dh = opendir($dir);
    if ($dh) 
    {
        while (($file = readdir($dh)) !== false) 
        {
            if($file=="."||$file=="..")continue;
            $filePath = $dir.$file;
            $path_parts = pathinfo($filePath);

            if(is_dir($filePath))
                $filesPaths = array_merge($filesPaths, getFiles($filePath, $extFilter));
            else if($extFilter=="*" || (isset($path_parts['extension']) && strstr($extFilter,".".$path_parts['extension'])))
                $filesPaths[] = $filePath;
        }
        closedir($dh);
    }
    return $filesPaths;
}

function generateSocial($url)
{
    ?>
    <div id="socialdiv" style="text-align: center;"></div>
    <script>
        $('#socialdiv').share({
            networks: ['facebook','googleplus','twitter','email'],
            urlToShare: '<?=$url?>'
        });
    </script>   
    <?php
}

function shortText($text, $limit = 23) {
    if(mb_strlen($text)<($limit+3))return $text;
    return mb_substr($text, 0, $limit, 'utf8')."...";
}

function generateSitemap() { 
    global $baseURL;
    $sitemap = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

    function generateSitemapEntry($url) { 
        return "<url>\n<loc>$url</loc>\n<changefreq>monthly</changefreq>\n<priority>1</priority>\n</url>\n";
    }

    //main pages
    $sitemap = $sitemap.generateSitemapEntry($baseURL);
    $sitemap = $sitemap.generateSitemapEntry($baseURL."/authors");
    $sitemap = $sitemap.generateSitemapEntry($baseURL."/about");
    $sitemap = $sitemap.generateSitemapEntry($baseURL."/mybooks");
    $sitemap = $sitemap.generateSitemapEntry($baseURL."/books");

    //categories
    $categories = UtilityDB::getCategories();
    foreach ($categories as $id=>$title) {
        $titleID = replace($title, " ", "_");
        $sitemap = $sitemap.generateSitemapEntry($baseURL."/books/$titleID");
    }

    //authors
    $authors = UtilityDB::getAuthors();
    foreach ($authors as $id=>$name) {
        $nameID = replace($name, " ", "_");
        $sitemap = $sitemap.generateSitemapEntry($baseURL."/author/$nameID");
    }

    system("rm ./sitemaps/*");
    mkdir("sitemaps");

    $robots = "user-agent: *\nallow: /\nsitemap: $baseURL/sitemaps/sitemap.xml\n";

    function startSitemap($filename) {
        global $baseURL;
        global $robots;
        file_put_contents("./".$filename, "\xEF\xBB\xBF"); 
        file_put_contents("./".$filename, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n", FILE_APPEND); 
    }
    
    function endSitemap($filename) {
        file_put_contents("./".$filename, "</urlset>", FILE_APPEND);
    }
    
    function appendSitemap($filename, $sitemap) {
        file_put_contents("./".$filename, $sitemap, FILE_APPEND); 
    }

    $nURLs = 0;
    
    $nBooksSitemaps = 1;
    startSitemap("sitemaps/sitemap$nBooksSitemaps.xml");

    //books
    $iBooks = 0;
    $books = UtilityDB::getBooks();
    foreach ($books as $id=>$title) {
        $titleID = replace($title, " ", "_");
        $sitemap = $sitemap.generateSitemapEntry($baseURL."/book/$titleID");
        
        $iBooks++;
        echo "<p>$iBooks: $id=>$title";

        $pagesIDs = UtilityDB::getPagesIDs($id);
        foreach ($pagesIDs as $pageID) {
            $i = $id%$nBooksSitemaps;
            $bookSitemap = generateSitemapEntry($baseURL."/book/$titleID/$pageID");
            $nURLs++;

            appendSitemap("sitemaps/sitemap$nBooksSitemaps.xml", $bookSitemap);

            if(filesize("sitemaps/sitemap$nBooksSitemaps.xml")>(10*1000*1000) || $nURLs>=(50*1000) ) {
                endSitemap("sitemaps/sitemap$nBooksSitemaps.xml");
                $nURLs = 0;
                $nBooksSitemaps++;
                startSitemap("sitemaps/sitemap$nBooksSitemaps.xml");
            }
        }
    }

    if($nURLs>0) {
        endSitemap("sitemaps/sitemap$nBooksSitemaps.xml");
    }

    $sitemap = $sitemap."</urlset>";

    file_put_contents("./sitemaps/sitemap0.xml", "\xEF\xBB\xBF".  $sitemap); 
    
    $date = date("Y-m-d");
    $sitemap = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $sitemap = $sitemap."<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
    for($i=0;$i<=$nBooksSitemaps;$i++){
        $sitemap = $sitemap."<sitemap>\n<loc>$baseURL/sitemaps/sitemap$i.xml</loc>\n<lastmod>$date</lastmod>\n</sitemap>\n";
    }
    $sitemap = $sitemap."</sitemapindex>\n";
    file_put_contents("./sitemaps/sitemap.xml", "\xEF\xBB\xBF".  $sitemap); 

    file_put_contents("./robots.txt", "\xEF\xBB\xBF".  $robots); 
}

?>