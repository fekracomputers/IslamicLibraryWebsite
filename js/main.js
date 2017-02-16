
function updateComments(){
    var commentID = 0;
    $(".comment").each(function(){
        commentID = commentID + 1;
        $(this).attr("id", "comment"+commentID);
        $(this).attr("href", "javascript:void(0)");
        $(this).click(function(){
            $("body").append("<div class='fullscreen'><div id='commentmsg' class='commentmsg'>"+$(this).attr("title")+"</div></div>");
            $(".fullscreen").fadeIn("fast");
            $(".fullscreen").click(function(){
                $(".fullscreen").remove();
            });
        });
    });
}

function updateBoxes() {
    $(".category, .author, .book , .part").each(function(){
        
        //Render books, authors, categories
        //=================================================
        var imagename = "";
        var itemclass = $(this).attr("class");
        if(itemclass.indexOf("category")>=0)imagename = "books";
        else if(itemclass.indexOf("book")>=0)imagename = "book";
        else if(itemclass.indexOf("part")>=0)imagename = "read";
        else if(itemclass.indexOf("author")>=0)imagename = "author";
        
        var href = $(this).find("a").attr("href");
        var html = $(this).html();
        var classValue = "" +  $(this).attr("class");
        if(classValue.indexOf("more")<0) {
            $(this).html("<table><tr><td><a href='"+href+"'><img src='/images/"+imagename+".png'></a></td><td>"+html+"</td></tr></table>");
            $(this).attr("class", "boxitem");
        } else {
            $(this).html("<table style='width:100%;height:100%;text-align:center;'><tr><td>"+html+"</td></tr></table>");
            $(this).attr("class", "boxitem more");
        }
    });
}

$(document).ready(function(){
    
    updateComments();
    
    if($(".headmenu").css("display").indexOf("none")===0) {
            
        $(".headarea").append("<div class='mobilemenu'><img src='images/menu.png'><div class='menubody'><div></div>");
        
        $(".headmenu a").each(function(){
            $('.menubody').append("<div class='menuitem'>"+$(this)[0].outerHTML+"</div>");
        });
        
        $('.mobilemenu').click(function(event){
            event.stopPropagation();
            if($('.menubody').css("display").indexOf("block")>=0){
                $('.menubody').css("display", "none");
            } else {
                $('.menubody').css("top", $(".headarea").offset().top + $(".headarea").height() - window.scrollY);
                $('.menubody').css("left", $(".headarea").offset().left);
                $('.menubody').css("display", "block");
            }
        });
    }
    
    $("body").click(function(){
        if($('.menubody').css("display").indexOf("block")>=0){
            $('.menubody').css("display", "none");
        }
    });
    
    $(window).scroll(function(){
        if($('.menubody').css("display").indexOf("block")>=0){
            $('.menubody').css("display", "none");
        }
    });
    
    updateBoxes();
    
});

function removeCookie( name ) {
  document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}
function removeMatchingCookies(bookID) {
    var theCookies = document.cookie.split(';');
    console.log(theCookies.length);
    for (var i = 0 ; i < theCookies.length; i++) {
        var theCookie = theCookies[i].trim();
        var theCookieName = theCookie.split('=')[0];
        console.log(theCookieName);
        if(theCookieName.indexOf("partnumber_of_book"+bookID)==0) {
            removeCookie(theCookieName);
        }
        if(theCookieName.indexOf("pagenumber_of_book"+bookID)==0) {
            removeCookie(theCookieName);
        }
    }
}

function clearMyBook(bookID) { 
    $("#book"+bookID).fadeOut("fast", function (){
        removeMatchingCookies(bookID);
        $("#book"+bookID).remove();
    });
}
 
function loadPage(url,weburl) {
    $tempColor = $(".navpagenumber").css("color");
    $tempBKColor = $(".navpagenumber").css("background-color");
    
    $(".navpagenumber").css("color", "gray");
    $(".navpagenumber").css("background-color", "rgba(255,255,255,0.1)");
    $(".navpagenumber").prop('disabled', 'disabled');
    
    $.get(url, function(response) {
        if(response.indexOf("errorpagenotfound")>=0)
        {
            $(".navpagenumber").val($(".navpagenumber").attr("oldvalue"));
            $(".navpagenumber").css('color', $tempColor);
            $(".navpagenumber").css('background-color', $tempBKColor);
            $(".navpagenumber").prop('disabled', '');
            return;
        }
        $("#dynamicarea").html(response);
        window.scroll(0,0);
        window.history.pushState("", "", weburl);
        updateComments();
    });
}

function loadTitles(url, targetID) {
    target = $('#'+targetID);
    img = target.children(":first-child");
    if(img.attr("id")!=="load"){
        if(target.css("display")==="none") {
            target.css("display", "block");
        } else {
            target.css("display", "none");
        }
        return;
    }
    img.css("display", "block");
    $.get(url, function(response) {
        target.html(response);
    });
}
 
 
function loadItems(url) {
    $.get(url, function(response) {
        var parent = $(".more").parent();
        $(".more").remove();
        parent.append(response);
        updateBoxes();
    });
}