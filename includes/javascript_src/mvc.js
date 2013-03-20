/**
 * JS Library file for MVC framework.
 */

<!--
function isIE() {
    var browser = navigator.appVersion;
    if (browser.indexOf("MSIE") > 0) {
        return 1;
    }
    else {
        return 0;
    }
}

function whatIsMyWidth() {
    var browser = navigator.appVersion;
    if (browser.indexOf("MSIE") > 0) {
        newWidth = document.body.clientWidth;
    }
    else {
        newWidth = window.innerWidth;
    }

    return newWidth;
}

var popupOpacity = 0;
var intervalID;

function grayOut(vis, options) {
    //Taken from http://www.hunlock.com/blogs/Snippets:_Howto_Grey-Out_The_Screen
    // Pass true to gray out screen, false to ungray
    // options are optional. This is a JSON object with the following (optional) properties
    // opacity:0-100 // Lower number = less grayout higher = more of a blackout
    // zindex: # // HTML elements with a higher zindex appear on top of the gray out
    // bgcolor: (#xxxxxx) // Standard RGB Hex color code
    // grayOut(true, {'zindex':'50', 'bgcolor':'#0000FF', 'opacity':'70'});
    // Because options is JSON opacity/zindex/bgcolor are all optional and can appear
    // in any order. Pass only the properties you need to set.
    var options = options || {};
    var zindex = options.zindex || 50;
    var opacity = options.opacity || 70;
    var opaque = (opacity / 100);
    var bgcolor = options.bgcolor || '#000000';
    var dark=document.getElementById('darkenScreenObject');
    if (!dark) {
        // The dark layer doesn't exist, it's never been created. So we'll
        // create it here and apply some basic styles.
        // If you are getting errors in IE see: http://support.microsoft.com/default.aspx/kb/927917
        var tbody = document.getElementsByTagName("body")[0];
        var tnode = document.createElement('div'); // Create the layer.
        tnode.style.position='absolute'; // Position absolutely
        tnode.style.top='0px'; // In the top
        tnode.style.left='0px'; // Left corner of the page
        tnode.style.overflow='hidden'; // Try to avoid making scroll bars
        tnode.style.display='none'; // Start out Hidden
        tnode.id='darkenScreenObject'; // Name it so we can find it later
        tbody.appendChild(tnode); // Add it to the web page
        dark=document.getElementById('darkenScreenObject'); // Get the object.
    }
    if (vis) {
        // Calculate the page width and height
        if( document.body && ( document.body.scrollWidth || document.body.scrollHeight ) ) {
            var pageWidth = document.body.scrollWidth+'px';
            var pageHeight = document.body.scrollHeight+'px';
        } else if( document.body.offsetWidth ) {
            var pageWidth = document.body.offsetWidth+'px';
            var pageHeight = document.body.offsetHeight+'px';
        } else {
            var pageWidth='100%';
            var pageHeight='100%';
        }
        //set the shader to cover the entire page and make it visible.
        dark.style.opacity=opaque;
        dark.style.MozOpacity=opaque;
        dark.style.filter='alpha(opacity='+opacity+')';
        dark.style.zIndex=zindex;
        dark.style.backgroundColor=bgcolor;
        dark.style.width= pageWidth;
        dark.style.height= pageHeight;
        dark.style.display='block';
    } else {
        dark.style.display='none';
    }
} 

function centerMe(el,startX,startY,startTop,offSet) {
    var newLeft;
    if (typeof startTop == "undefined") {
        startTop = getCurrentScrollPosition() + 75; //This will center the popup regardless of scroll position, 75 px from the top of the screen.
    }

    if (typeof startX == "undefined") {
        startX = 200;
    }
	
    if (typeof startY == "undefined") {
        startY = 200;
    }
    var newWidth = whatIsMyWidth();
	
    if (startX > 0) {
        el.style.width = startX + "px";
        newLeft = (newWidth/2) - ((startX/2) + offSet)
        el.style.left = newLeft + "px";
    }
    if (startY > 0) {
        el.style.height = startY + "px";
    }
    if (startTop > 0) {
        el.style.top = startTop + "px";
    }
}

function getCurrentScrollPosition() {
    yPos = (document.all)?document.body.scrollTop:window.pageYOffset;
    return yPos;
} 

//function showAjaxPopup(x, y, toppos) {
//    if (typeof toppos == "undefined") {
//        //toppos = 0;
//        toppos = getCurrentScrollPosition() + 75; //This will center the popup regardless of scroll position, 75 px from the top of the screen.
//    }
//
//    var offSet = 20;
//    var newWidth = tellMe();
//
//
//    if (typeof x == "undefined") {
//        x = 300;
//    }
//    if (typeof y == "undefined") {
//        y = 400;
//    }
//    //var divBody = document.getElementById("containerBody");
//    // 	setOpacity(document.getElementById("containerBody"),0.3);
//    grayOut(true,{
//        'zindex':'50'
//    });
//    var popup = document.getElementById("popup");
//    if (toppos > 0) {
//        popup.style.top = toppos + "px";
//    }
//    if (x > 0) {
//        popup.style.width = x + "px";
//        newLeft = (newWidth/2) - ((x/2) + offSet)
//        popup.style.left = newLeft + "px";
//    }
//    if (y > 0) {
//        popup.style.height = y + "px";
//    }
//    //"Fade in" the popup.
//    fadeIn(popup);
//    intervalID = setInterval(function(){
//        fadeIn(popup);
//    },75);
//    if (popupOpacity >= 1) {
//        clearInterval(intervalID);
//    }
//    popup.style.visibility="visible";
//}

function showPopup(title, contents, x, y, toppos, onClose) {
    var offSet = 20;
    var popup = document.getElementById("popup");
    if (typeof onClose=="undefined"){
        onClose = "";
    }
    if (typeof toppos == "undefined") {
        toppos = getCurrentScrollPosition() + 75; //This will center the popup regardless of scroll position, 75 px from the top of the screen.
    }

    grayOut(true,{
        'zindex':'50'
    });
    centerMe(popup,x,y,toppos,offSet);

    var header;
    if (title != "ajax"){
        header = "<div id=\"popupHeader\" style=\"width: " + (parseInt(popup.style.width.replace("px","")) - 10) + "px; padding: 1px 10px;\">" + title + "<img id=\"closeBtn\" onclick=\"hidePopup('"+onClose+"')\" style=\"height: 20px; margin-top:3px; display:block; float:right;\" src=\"/includes/images/close_button-icon.gif\"></div>";
    } else {
        header = "";
    }
    
    if (contents != "ajax"){
        popup.innerHTML=header + "<div id=\"popupContents\">" + contents + "</div>";
    } else {
        if (header.length > 0){
            popup.innerHTML=header + "<div id=\"popupContents\"></div>";
        } else {
            contents = "";
        }        
    }
    
    //"Fade in" the popup.
    fadeIn(popup);
    intervalID = setInterval(function(){
        fadeIn(popup);
    },75);
    if (popupOpacity >= 1) {
        clearInterval(intervalID);
    }
    popup.style.visibility="visible";
}

function fadeIn(objToFade) {
    var currOp = objToFade.style.opacity;

    popupOpacity += 0.1;
    setOpacity(objToFade,popupOpacity);
}

function setOpacity (myElement, opacityValue) {
    if (window.ActiveXObject) {
        myElement.style.filter = "alpha(opacity="
        + opacityValue*100 + ")"; // IE
    } else {
        myElement.style.opacity = opacityValue; // Gecko/Opera
    }
}
function hidePopup(onClose) {
    grayOut(false);
    var popup = document.getElementById("popup")
    popup.style.visibility="hidden";
    clearInterval(intervalID);
    popupOpacity = 0;
    if ((typeof onClose!="undefined") && (onClose.length > 0)){ 
        executeFunctionByName(onClose,parent,"");
    }
    popup.innerHTML="";
}

function executeFunctionByName(functionName,context/*,args*/){
    var args = Array.prototype.slice.call(arguments).splice(2);
    var namespaces = functionName.split(".");
    var func = namespaces.pop();
    for(var i=0; i < namespaces.length;i++){
        context = context[namespaces[i]];
    }
    return context[func].apply(context,args);
}


$.fn.animateHighlight = function(highlightColor, duration) {
    var highlightBg = highlightColor || "#FFFF9C";
    var animateMs = duration || 1500;
    var originalBg = this.css("backgroundColor");
    this.stop().css("background-color", highlightBg).animate({
        backgroundColor: originalBg
    }, animateMs);
};

jQuery.fn.flash = function( color, duration )
{

    var current = this.css( 'color' );
    
    //if (color.indexOf("#") == -1){
    this.animate( {
        color: 'rgb(' + color + ')'
    }, duration / 2 );
    //} else {
    //this.animate( { color: color  }, duration / 2 );
    //}
    
    this.animate( {
        color: current
    }, duration / 2 );

}

-->