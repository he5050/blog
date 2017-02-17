//以下为修改jQuery Validation插件兼容Bootstrap的方法，没有直接写在插件中是为了便于插件升级
        $.validator.setDefaults({
            highlight: function (element) {
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
            },
            success: function (element) {
                element.closest('.form-group').removeClass('has-error').addClass('has-success');
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                if (element.is(":radio") || element.is(":checkbox")) {
                    error.appendTo(element.parent().parent().parent());
                } else {
                    error.appendTo(element.parent());
                }
            },
            errorClass: "help-block m-b-none",
            validClass: "help-block m-b-none"


        });

        //以下为官方示例
        $().ready(function () {
            // validate the comment form when it is submitted
            $("#commentForm").validate();

            // validate signup form on keyup and submit
            var icon = "<i class='fa fa-times-circle'></i> ";
            $("#user").validate({
                //验证规则
                rules: {
                    username: "required",
                    userrname: "required",
                    userphone: {
                        required: true,
                        minlength: 11,
                        maxlength:11,
                        digits:true,
                    },
                    usercard:{
                        required: true,
                    },
                    password: {
                        required: true,
                        minlength: 5
                    },
                    confirm_password: {
                        required: true,
                        minlength: 5,
                        equalTo: "#password"
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    topic: {
                        required: "#newsletter:checked",
                        minlength: 2
                    },
                    sex : "required",
                    a1 : "required",
                    a2 : "required",
                    a3 : "required",
                    areainfo : "required",
                    agree: "required"
                },
                //消息提示
                messages: {
                    username: icon + "请输入用户名",
                    userrname: icon + "请输入真实名字",
                    userphone: {
                        required: icon + "必须输入电话号码",
                        minlength: icon + "只能是11位",
                        maxlength: icon + "只能是11位",
                        digits: icon+"只能是数字",
                    },
                    usercard:{
                        required: icon+"必须输入身份证号",
                    },
                    password: {
                        required: icon + "请输入您的密码",
                        minlength: icon + "密码必须5个字符以上",
                    },
                    confirm_password: {
                        required: icon + "请再次输入密码",
                        minlength: icon + "密码必须5个字符以上",
                        equalTo: icon + "两次输入的密码不一致",
                    },
                    email: icon + "请输入您的E-mail",
                    a1:icon + "请选择地区",
                    a2:icon + "请选择省市",
                    a3:icon + "请选择区县",
                    sex:icon + "性别必须填写",
                    areainfo:icon + "请输入详细地址",
                    agree: {
                        required: icon + "必须同意协议后才能注册",
                        element: '#agree-error',
                    }
                }
            });

            // propose username by combining first- and lastname
        });
