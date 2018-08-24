$(document).ready(function(){
	$('.firmL-nav-U li span').click(function(){
		$(this).parent().toggleClass('active').siblings().removeClass('active');
	});
	$('.firmLBg').click(function(){
		$(this).parents("li").toggleClass('active');
	});
	$('#slider').slider({
		className: 'slider_box',
		tick: 4000 //播放间隔
	});
	var imgPieces=$('#slider').find('.ks_wrap');
	// console.log(imgPieces.length);
	for(var i=0;i<imgPieces.length;i++){
		$('.ks_wt_1').append('<li></li>');
		$('.ks_wt_1').find('li').eq(0).addClass('active');
	}

});

