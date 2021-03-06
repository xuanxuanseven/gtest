<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <title>RSA版 | 滑动验证码</title>
    <style>
    a{
        cursor: pointer;
        color: #666;
        text-decoration: none;
        transition: color 0.2s linear;
    }
    a:hover{
         color: #00c3f5;
        -webkit-transition: color 0.2s linear;
        -moz-transition: color 0.2s linear;
        -ms-transition: color 0.2s linear;
        -o-transition: color 0.2s linear;
        transition: color 0.2s linear;
    }
    .input-text{
        display: block;
        margin: 10px 0 10px 20px;
        padding: 10px;
    }
    </style>
</head>
<body>
    <h1><a href="https://github.com/HaleyLeoZhang/slide-verify"  target="_blank">滑动验证码 - RSA版</a></h1>
    <form id="form_check" >
        <input type="text"     class="input-text" name='name' size="30" placeholder="用户名，测试帐号：admin"  />
        <input type="password" class="input-text" name='pwd'  size="30" placeholder="密码，测试密码：123123"   />

    </form>
    <!-- 请不要改此处的id -->
    <div id="yth_captchar"></div>

    <script type="text/javascript" src="http://libs.baidu.com/jquery/1.9.0/jquery.js"></script>
    <script type="text/javascript" src="http://cdn.bootcss.com/loadjs/3.5.0/loadjs.min.js"></script>
    <script type="text/javascript" src="http://cdn.bootcss.com/layer/3.0.1/layer.min.js"></script>
    <script type="text/javascript" src="/static/js/hlz_rsa.js"></script>
    <script>
        loadjs(["/verify/js/min_drag.js"], {
        success: function() {
            // 异步初始化验证码
            $.ajax({
                "url": "/tv/Verify/index", // 获取初始的验证码 `css + 验证码图片` 的地址
                "success": function(html) {
                    $("#yth_captchar").html(html);
                    $(this).yth_drag({
                        "verify_url": "/tv/Verify/check",
                        "source_url": "/tv/Verify/captchar",
                        "auto_submit": true,
                        "submit_url": "",
                        "form_id": "form_check",
                        "crypt_func": "rsa_encode"
                    });
                }
            });

            // 适应当前样式
            // $("#yth_captchar").css({
            //     "margin-left": "10px",
            //     "width": "280px",
            //     "margin-top": "20px"
            // });
        }

    });
    </script>
</body>
</html>
