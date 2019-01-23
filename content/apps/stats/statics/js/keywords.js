// JavaScript Document
;(function (app, $) {
    app.keywords = {
        init: function () {
            app.keywords.keywords();
        },
        
        keywords: function () {
            $(".start_date,.end_date").datepicker({
                format: "yyyy-mm-dd",
                container : '.main_content',
            });
            
            $('.screen-btn').on('click', function (e) {
                e.preventDefault();
                var start_date = $("input[name='start_date']").val(); //开始时间
                var end_date = $("input[name='end_date']").val(); //结束时间
                var filter = '';
                
                var check_length = $("input[name='filter'][checked]").length;
            	if (check_length == 1) {
            		filter = $("input[name='filter'][checked]").val();
            	} else {
            		$("input[name=filter]").each(function () {
	                    if ($(this).attr("checked")) {
	                        filter += $(this).val() + '.';
	                    }
                    });
            	}
                
                var url = $("form[name='theForm']").attr('action'); //请求链接
                if (start_date == 'undefind') start_date = '';
                if (end_date == 'undefind') end_date = '';
                if (url == 'undefind') url = '';
 
                if (start_date == '') {
                    var data = {
                        message: js_lang.start_date_required,
                        state: "error",
                    };
                    ecjia.admin.showmessage(data);
                    return false;
                } else if (end_date == '') {
                    var data = {
                        message: js_lang.end_date_required,
                        state: "error",
                    };
                    ecjia.admin.showmessage(data);
                    return false;
                };
 
                if (start_date >= end_date && (start_date != '' && end_date != '')) {
                    var data = {
                        message: js_lang.start_lt_end_date,
                        state: "error",
                    };
                    ecjia.admin.showmessage(data);
                    return false;
                } else {
                    if (filter) {
                        ecjia.pjax(url + '&start_date=' + start_date + '&end_date=' + end_date + '&filter=' + filter);
                    } else {
                        ecjia.pjax(url + '&start_date=' + start_date + '&end_date=' + end_date);
                    }
                }
            });
        },
    };
    
})(ecjia.admin, jQuery);
 
// end