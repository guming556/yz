//$.ajaxSetup({
//	headers: {'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')}
//});
$(function(){
	//slider
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

	$('#slider2').slider({
		className: 'slider_box',
		tick: 6000 //播放间隔
	});

	var imgPieces2=$('#slider2').find('.ks_wrap');
	for(var i=0;i<imgPieces2.length;i++){
		$('.ks_wt_2').append('<li></li>');
		$('.ks_wt_2').find('li').eq(0).addClass('active');
	}
});