jQuery(function($) {

    $('#card_front_side').ace_file_input({
        style:'well',
        btn_choose:'选择新的身份证正面图片',
        btn_change:null,
        no_icon:false,
        droppable:true,
        thumbnail:'large'//large | fit
        //,icon_remove:null//set null, to hide remove/reset button
        //before_change:function(files, dropped) {
        //    alert(3)
        //
			//			return true;
			//		}
        /**,before_remove : function() {
						return true;
					}*/
        ,
        preview_error : function(filename, error_code) {
            //name of the file that failed
            //error_code values
            //1 = 'FILE_LOAD_FAILED',
            //2 = 'IMAGE_LOAD_FAILED',
            //3 = 'THUMBNAIL_FAILED'
            //alert(error_code);
        }

    }).on('change', function(){
        //console.log($(this).data('ace_input_files'));
        //console.log($(this).data('ace_input_method'));
    });

    $('#card_back_dside').ace_file_input({
        style:'well',
        btn_choose:'选择新的身份证反面图片',
        btn_change:null,
        no_icon:false,
        droppable:true,
        thumbnail:'large'//large | fit | small
        //,icon_remove:null//set null, to hide remove/reset button
        /**,before_change:function(files, dropped) {
						//Check an example below
						//or examples/file-upload.html
						return true;
					}*/
        /**,before_remove : function() {
						return true;
					}*/
        ,
        preview_error : function(filename, error_code) {
            //name of the file that failed
            //error_code values
            //1 = 'FILE_LOAD_FAILED',
            //2 = 'IMAGE_LOAD_FAILED',
            //3 = 'THUMBNAIL_FAILED'
            //alert(error_code);
        }

    }).on('change', function(){
        //console.log($(this).data('ace_input_files'));
        //console.log($(this).data('ace_input_method'));
    });

});
