// JavaScript Document
;(function (app, $) {
    /*会员列表js*/
    app.user_list = {
        init: function () {
            app.user_list.screen();
            app.user_list.search();
        },
        
        screen: function () {
            $('.screen-btn').on('click', function (e) {
                e.preventDefault();
                var url = $("form[name='searchForm']").attr('action') + '&rank=' + $("#select-rank option:selected").val();
                ecjia.pjax(url);
            })
        },
        
        search: function () {
            $("form[name='searchForm']").on('submit', function (e) {
                e.preventDefault();
                var url = $("form[name='searchForm']").attr('action');
                var keywords = $("input[name='keywords']").val();
                if (keywords != '') {
                    url += '&keywords=' + keywords;
                }
                ecjia.pjax(url);
            });
        }
    };
 
    /*会员编辑js*/
    app.user_edit = {
        init: function () {
            /* 加载日期控件 */
            $(".date").datepicker({
                format: "yyyy-mm-dd",
                container : '.main_content',
            });
            
            app.user_edit.submit();
        },
        
        submit: function () {
            var $this = $('form[name="theForm"]');
            var insert = $this.hasClass('insert');
            var option = {
                rules: {
                    username: {
                        required: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    password: insert ? {
                        required: true,
                        minlength: 6
                    } : {
                        minlength: 0
                    },
                    newpassword: insert ? {
                        minlength: 0
                    } : {
                        minlength: 6
                    },
                    confirm_password: insert ? {
                        equalTo: '#password'
                    } : {
                        equalTo: '#newpassword'
                    }
                },
                messages: {
                    username: {
                        required: user_jslang.username_required
                    },
                    email: {
                        required: user_jslang.email_required,
                        email: user_jslang.email_check
                    },
                    password: insert ? {
                        required: user_jslang.password_required,
                        minlength: user_jslang.password_length
                    } : '',
                    newpassword: insert ? user_jslang.password_length : '',
                    confirm_password: {
                        equalTo: user_jslang.password_check
                    }
                },
                submitHandler: function () {
                    $this.ajaxSubmit({
                        dataType: "json",
                        success: function (data) {
                            ecjia.admin.showmessage(data);
                        }
                    });
                }
            }
            var options = $.extend(ecjia.admin.defaultOptions.validate, option);
            $this.validate(options);
        }
    };
 
    /*会员详情js*/
    app.user_info = {
        init: function () {
            app.user_info.search_userinfo();
        },
        
        search_userinfo: function () {
            $("form[name='searchForm']").on('submit', function (e) {
                e.preventDefault();
                var keywords = $('input[name="keywords"]').val();
                var id = $(this).attr('data-id');
                if (keywords == '') {
                    return false;
                }
                var url = $(this).attr('action') + '&keywords=' + keywords + '&id=' + id;
                $.get(url, '', function (data) {
                    if (data.state == "error") {
                        ecjia.admin.showmessage(data);
                    } else {
                        ecjia.pjax(url);
                    }
                });
            });
        }
    };
 
    /*会员等级js*/
    app.user_rank = {
        init: function () {
            app.user_rank.submit_rank();
        },
        
        submit_rank: function () {
            var $this = $('form[name="theForm"]');
            var option = {
                rules: {
                    rank_name: {
                        required: true
                    },
                    min_points: {
                        required: true
                    },
                    max_points: {
                        required: true
                    },
                    discount: {
                        required: true
                    },
                },
                messages: {
                    rank_name: {
                        required: rank_jslang.rank_name_required
                    },
                    min_points: {
                        required: rank_jslang.min_points_required
                    },
                    max_points: {
                        required: rank_jslang.max_points_required
                    },
                    discount: {
                        required: rank_jslang.discount_required
                    }
                },
                submitHandler: function () {
                    $this.ajaxSubmit({
                        dataType: "json",
                        success: function (data) {
                            ecjia.admin.showmessage(data);
                        }
                    });
                }
            }
            var options = $.extend(ecjia.admin.defaultOptions.validate, option);
            $this.validate(options);
        }
    };
 
    /*会员注册项js*/
    app.user_reg_fields = {
        init: function () {
            app.user_reg_fields.submit_reg_field();
        },
        
        submit_reg_field: function () {
            var $this = $("form[name='theForm']");
            var option = {
                rules: {
                    reg_field_name: {
                        required: true
                    },
                    reg_field_order: {
                        required: true
                    }
                },
                messages: {
                    reg_field_name: {
                        required: reg_jslang.reg_field_name_required
                    },
                    reg_field_order: {
                        required: reg_jslang.reg_field_order_required
                    }
                },
                submitHandler: function () {
                    $this.ajaxSubmit({
                        dataType: "json",
                        success: function (data) {
                            ecjia.admin.showmessage(data);
                        }
                    });
                }
            }
            var options = $.extend(ecjia.admin.defaultOptions.validate, option);
            $this.validate(options);
        }
    };
})(ecjia.admin, jQuery);

// end