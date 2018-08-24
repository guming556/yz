[].slice.call(document.querySelectorAll('input[data-LUploader]')).forEach(function(el) {
	var except = el.getAttribute('data-LUploader');
	var multiple = false;
	if(except == 'cadImage'){
		multiple = true;
	}
	new LUploader(el, {
		url: '/LUpload',//post请求地址
		multiple: multiple,//是否一次上传多个文件 默认false
		maxsize: 81920,//忽略压缩操作的文件体积上限 默认100kb
		accept: 'image/*',//可上传的图片类型
		quality: 0.6,//压缩比 默认0.1  范围0.1-1.0 越小压缩率越大
		showsize:false//是否显示原始文件大小 默认false
	});
});


