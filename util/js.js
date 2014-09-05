/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var ShopInfo = {
    //检查添加分类表单
    checkAddCategory: function() {
        if ($("#total_count").length > 0 && $("#total_count").val() >= 6)
        {
            alert("最多添加6个分类");
            return;
        }
        var cname = $.trim($("#cname").val());
        var regu = "^[0-9a-zA-Z\u4e00-\u9fa5]+$";
        var re = new RegExp(regu);
        if (!re.test(cname)) {
            alert('请输入合法的分类名称');
            return false;
        }

        var len = cname.replace(/[^\x00-\xff]/g, "**").length;
        if (cname == '' || cname == null) {
            alert("请输入分类名称");
            return false;
        } else {
            if (len > 8) {
                alert("分类名称最多支持4个汉字，8个英文字符");
                return false;
            } else {
                $.ajax({
                    type: "POST",
                    url: "/shop/findcnamebyname",
                    cache: false,
                    dataType: "json",
                    data: {
                        cname: cname
                    },
                    success: function(data) {
//                       alert(data);
                        if (data == 1) {
                            alert("分类已存在");
                        } else if (data == 0) {
                            $("#addcategoryForm").submit();
                        } else if (data == 2) {
                            alert("最多添加6个分类");
                        }
                    }
                });
            }
        }
    },
    /* 
     用途：检查输入字符串是否只由汉字、字母、数字组成 
     输入： 
     value：字符串 
     返回： 
     如果通过验证返回true,否则返回false 
     */
    shopinfo_form: function() {
        var dpname = $("#dpname").val();
        var regu = "^[0-9a-zA-Z\u4e00-\u9fa5]+$";
        var re = new RegExp(regu);
        dpname = $.trim(dpname);
        if (dpname == "") {
            alert('店铺名称不允许为空');
            return false;
        } else {
            if (!re.test(dpname)) {
                alert('店铺名称不允许包含标点符号');
                return false;
            }
        }
        var len = dpname.length;
        if (len > 25)
        {
            alert('店铺最长输入25个字符!');
            return false;
        }
        //电话格式
        var dpinfophone = $("#dpinfophone").val();
        var mobile = /(^1[3|4|5|8][0-9]\d{8}$)|(^0\d{2,3}-?\d{7,8}$)|((^400(-\d{3,4}){2})$)|((^400(\d{7}))$)|((^800(-\d{3,4}){2})$)|((^800(\d{7}))$)/;
        var prompt = "请输入合法的电话或手机!";
        if (dpinfophone != "") {
            if (!mobile.test(dpinfophone)) {
                alert(prompt);
                return false;
            }
        } else {
            alert("客服电话不允许为空");
            return false;
        }
        //时间限定
        //定义正则表达式部分
        var time_limit = $.trim($("#time_limit").val());
        var reg_timelimit = /^[0-9]+([.]{1}[0-9]+){0,1}$/;
        if (time_limit != "") {
            if (!reg_timelimit.test(time_limit)) {
                alert("请输入数字");
                return false;
            }
            if (time_limit > 36 || time_limit < 0.5)
            {
                alert("订单时间限定有效时间是0.5-36个小时之间！");
                return false;
            }
        }
        var con_reg = /^[+]?\d+(\.\d+)?$/;
        var postage = $.trim($("#postage").val());
        if (postage != "") {
            if (!con_reg.test(postage)) {
                alert("运费输入格式不正确");
                $("#postage").focus();
                return false;
            }
        }else{
            alert("请输入商品运费");
            return false;
        }
        //兑换券
        var price1 = $.trim($("#price1").val());
        if (price1 != "") {
            if (!con_reg.test(price1)) {
                alert("兑换券输入格式不正确");
                $("#price1").focus();
                return false;
            }
        }
    }
};

$(function() {

    $("#shopinfo_form").submit(function() {
        return ShopInfo.shopinfo_form();
    });
    $("#addCategorySubmit").click(function() {
        ShopInfo.checkAddCategory();
    });

});
