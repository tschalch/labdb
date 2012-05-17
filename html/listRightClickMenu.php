<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript" >
    $(document).ready(function(){
	$('.lists tr').bind('contextmenu',function(e){
	    $(".vmenu").hide();
	    var $cmenu = $(this).find(".vmenu");
	    $('<div class="overlay"></div>').css({left : '0px', top : '0px',position: 'absolute', 
		width: '100%', height: '100%', zIndex: '100' }).click(function() {
		$(this).remove();
		$cmenu.hide();
	    }).appendTo(document.body);
	    $(this).find(".vmenu").css({ left: e.pageX, top: e.pageY, zIndex: '101' }).show();
	    return false;
	 });
    });
</script>
