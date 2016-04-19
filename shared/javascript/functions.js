var isOver = false;
var checkMobile = false;
var mobile = false;
var rsz;

$(document).ready(function(e) {
	
	$(document.body).removeClass("nojs");
	
	if(mobileCheck() || $(window).innerWidth() <= 800){
		/*
		mobile = true;
		$(document.body).addClass("mobile");	
		$("#slider").remove();
		$(mobileNavi($("#mainNaviList"))).insertBefore("main");
		$("#mainNaviList li").css({display: "none"});
		*/
	}
	
	$(".colorbox").colorbox();
	
	if(!mobile && $('#slideImagesList').length > 0){
		$('#slideImagesList').fadeSlideShow({
			width: "100%", // default width of the slideshow
			height: $('#slideImagesList li img').height() + "px", // default height of the slideshow
			speed: 'slow', // default animation transition speed
			interval: 6000, // default interval between image change
			PlayPauseElement: false, // default css id for the play / pause element
			PlayText: false, // default play text
			PauseText: false, // default pause text
			NextElement: false, // default id for next button
			PrevElement: false, // default id for prev button
			ListElement: false, // default id for image / content controll list
			ListLi: 'fssLi', // default class for li's in the image / content controll 
			ListLiActive: 'fssActive', // default class for active state in the controll list
			addListToId: false, // add the controll list to special id in your code - default false
			allowKeyboardCtrl: false, // allow keyboard controlls left / right / space
			autoplay: true // autoplay the slideshow
		});
		
		
	}
	
	
	cartActions();
	window.setTimeout(function(){
		productClass();
		valignMiddle();
		
	}, 200);
	
	//resize
	$(window).bind("resize", function(){
		clearTimeout(rsz);
		rsz = window.setTimeout(function(){productClass()}, 250);	
	});
	
	
});

var productClass = function(){
	//products & categories
	$(".modProdList li").removeClass("row1");
	$(".modProdList li").removeClass("row2");
	if($(".modProdList").length > 0){
		var liTop = 0;
		var counter = 0;
		$(".modProdList li").each(function(index, element) {
            var y = $(element).position()['top'];
			if(y != liTop){
				liTop = y;
				counter++;
			}
			$(element).addClass((counter % 2) == 0?"row2":"row1");	
        });	
	}		
	
	if($("#modArticles").length > 0){
		var liTop = 0;
		var counter = 0;
		$("#modArticles article").each(function(index, element) {
            var y = $(element).position()['top'];
			if(y != liTop){
				liTop = y;
				counter++;
			}
			$(element).addClass((counter % 2) == 0?"row1":"row2");	
        });	
	}	
	
	
};

var valignMiddle = function(){
	if($("#modArticles .modArticleText").length > 0){
		$("#modArticles .modArticleText").each(function(index, element) {
			var parentElement =  $(element).parent();
			
			var imgElement =  parentElement.find("figure");
			var outerHeight = parentElement.height();
			var innerHeight = $(element).innerHeight();
			var marginTop = Math.round((outerHeight - innerHeight)/2);
			parentElement.css({"padding-top": "0px"});
			$(element).css({"margin-top": marginTop + "px"});
			
			
		});
	}
};

var cartActions = function(){
	//add to cart
	$("#modProdList li div").on("click", "a", function(){
		var getData = returnGetVarsObj(this.href);
		$.get("modules/produkte/ajax/actions.ajax.php", getData, function(data){
			$("#cart span").text(data);
			$("#cart").animate({"background-color": "#FFFF00"}, 500, function(){
				$("#cart").animate({"background-color": "#EFEFEF"}, 3000);
			});
		});
		return false;	
	});
	//del from cart
	$("#modShoppingCart").on("click", ".modDelItem", function(){
		var row = $(this).parent().parent();
		var getData = returnGetVarsObj(this.href);
		$.get("modules/produkte/ajax/actions.ajax.php", getData, function(data){
			if(data == "0"){
				location.reload();
				return;	
			}
			$("#cart span").text(data);
			row.remove();
			
		});
		return false;	
	});
	
	// hide payment unused options
	if($("#bankeinzug").length > 0){
		$("#bankeinzug").not(".active").hide();
		$("#kreditkarte").not(".active").hide();
	}
}

var returnGetVarsObj = function(url){
	var obj = {};
	var url = url.split("?")[1];
	get = url.split("&");
	for(var i in get){
		var tmp = get[i].split("=");
		obj[tmp[0]]	= tmp[1];
	}
	return obj;
}

var mobileNavi = function(list){
	var sel = $("<select>", {
		id: "mainNaviSelect"
	})
	$(list).find("li").each(function(index, element) {
    	var optText = $(element).find("a")[0].innerText	
		optText = getOptionIndent($(this), "") + optText;
		var opt = $("<option>", {
			value: $(element).find("a")[0].href,
			text: optText,
			selected: location.href == $(element).find("a")[0].href?true:false
		}); 
		sel.append(opt);
	});
	
	sel.bind("change", function(){
		location.href = $(this).val();	
	});
	
	return sel;	
}

var getOptionIndent = function(element, indent){
	var parent = $(element).parent();
	if(parent.attr("id") == "" || parent.attr("id") == null){
		indent+= "--";	
		//indent+= getOptionIndent(parent, indent);
		//indent = indent.substr(0, indent.length - 2);
	}
	return indent;
}