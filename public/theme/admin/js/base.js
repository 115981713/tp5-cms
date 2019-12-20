layui.use(['form','layer','element','upload'], function(){
    //表单对象
    var form = layui.form;
    var $=layui.$;
    var upload=layui.upload;


    //监听表单事件
    form.on('submit(zz-btn-submit)', function(fromData){
        zzpost(fromData.form,fromData.form.action,fromData.field);
        return false;
    });

    //监听复选框事件
    form.on('checkbox(zz-checkbox-table)', function(data){
        var itemStatus = data.elem.checked;
        if (itemStatus == true) {
            $(".zz-table-chk-item").prop("checked", true);
            form.render('checkbox');
        } else {
            $(".zz-table-chk-item").prop("checked", false);
            form.render('checkbox');
        }
    });


    //搜索功能
    $(".zz-form-search .btn-search").click(function() {
        var url = $(this).attr('url');
        var query = $('.search-form').find('input').serialize();
        query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=layui.$))/g, '');
        query = query.replace(/^&/g, '');
        if (url.indexOf('?') > 0) {
            url += '&' + query;
        } else {
            url += '?' + query;
        }
        window.location.href = url;
    });
    
    //回车搜索
    $(".zz-search-form input").keyup(function(e) {
        if (e.keyCode === 13) {
            $("#search").click();
            return false;
        }
    });

    //弹出操作窗口
    $('.open-win').click(function () {
        var that=$(this);
        var url=that.attr('href');
        var title=that.attr('title');
        title==''?'信息':title;

        top.layer.open({
            type:2,
            title:title,
            area: ['720px','500px'], //宽高
            content:url,
            success:function(e,index){
                parent.layer.iframeAuto(index);
            }
        });
        return false;
    });

    //添加，编辑页面取消按钮
    $('.zz-btn-cancel').click(function () {
        var windowName=window.name;
        //判断是否弹出的窗口
        if(windowName){
            //在iframe中
            var index = top.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
            top.layer.close(index); //再执行关闭
        }
        else {
            //不在iframe中
            history.go(-1);
        }
    });


    //ajax-post操作，一般用于删除
    $('.ajax-post').click(function(){
        var that = this;
        if ($(this).hasClass('confirm') ) {
            var confirmLayer=top.layer.confirm('您确认要执行该操作吗？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                top.layer.close(confirmLayer);
                var target=$(that).attr('href');
                zzpost(that,target);
            });
        }
        else{
            var target=$(that).attr('href');
            zzpost(that,target);
        }
        return false;
    });

    //数据表中，启用禁用，显示隐藏开关
    form.on('switch(zz-switch-display)', function(data){
    	var itemDom=data.elem;
    	var updateVal=data.value==1?0:1;

      	zzpost(itemDom,$(itemDom).attr('data-href'),{val:updateVal},function(){
        	itemDom.value=updateVal;
        	return true;
        },function(){
        	itemDom.checked=itemDom.checked?false:true;
        	form.render('checkbox');
        });
    });

    //批量删除
    $('.zz-btn-delete-all,.zz-btn-select-all').click(function () {
        var that = this;
        //判断是否选中要删除的对象
        var delDom=$(".layui-table .zz-table-chk-item:checked");

        if(delDom.length==0){
            layer.msg('请选择要操作的数据');
            return false;
        }
        var delItem=new Array();
        delDom.each(function (e) {
            delItem.push(this.value);
        })

        parent.layer.confirm('您确认要执行该操作吗？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            var target=$(that).attr('href');
            zzpost(that,target,{ids:delItem});
        });
        return false;
    });

    //数据表中表单元素ajax修改
    $('.zz-form-datalist input').blur(function () {
        var that = this;
        var source=$(this).attr('data-source');
        if(source==$(this).val()){
            return;
        }
        var zzForm=$(this).parents('.zz-form-datalist');

        var formFeild = form.val(zzForm.attr('lay-filter'));

        zzpost(this,zzForm.attr('action'),formFeild);
    });

    //封装post方法
    function zzpost(that,url,data,fun1,fun0){
        if(!url){
            return;
        }
        var loadIndex=top.layer.load(1, {
            shade: [0.2,'#000'] //0.1透明度的白色背景
        });
        $.ajax({
            url:url,
            type:'POST',
            data:data,
            dataType:'JSON',
            error:function () {
                top.layer.close(loadIndex);
                top.layer.msg('服务器返回的数据错误！',{icon: 2,time:2000});
            },
            success:function (data) {
                top.layer.close(loadIndex);

                if (data.code==1) {
                    if(fun1){
                        var funre=fun1();
                        if(funre){
                            return;
                        }
                    }
                    top.layer.msg(data.msg?data.msg:'操作成功',{icon: 1,time:data.wait*1000});
                    
                    //具有禁止刷新标识的，禁止刷新。
                    if($(that).hasClass('no-refresh') ){
                        return;
                    }
                    //判断是否要执行返回上一页
                    if(data.data=="back"){
                        history.back();
                        return;
                    }
                    //判断是否要整个页面刷新
                    if(data.data=="top"){
                        top.setTimeout(function(){
                            top.location.reload();
                        },data.wait*1000);
                        return;
                    }

                    //判断是否弹出的模拟窗口
                    var windowName=window.name;
                    if(windowName){
                        //在iframe中
                        if(data.url){
                            top.frames[0].location.href=data.url;
                        }
                        else{
                            top.frames[0].location.reload(true);
                        }
                        var index = top.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                        top.layer.close(index); //再执行关闭
                        return;
                    }


                    if(data.url){
                        location.href=data.url;
                    }
                    else{
                        location.reload();
                    }
                }else{
                    if(fun0){
                        var funre=fun0();
                        if(funre){
                            return;
                        }
                    }
                    top.layer.msg(data.msg?data.msg:'操作失败',{icon: 2,time:data.wait*1000});
                }
            }

        });
    }



    if($('.zz-upload-pic').length>0){
        upload.render({
            elem: '.zzBtnUploadPic',
            url:UPLOAD_PIC_URL
            ,done: function(res, index, upload){
                var item = this.item;
                $(item).removeAttr('disabled');
                $(item).html('<i class="layui-icon">&#xe67c;</i>上传图片');
                if(res.code==1){
                    var pic='';
                    if(res.data.url!=''){
                        pic=res.data.url;
                    }
                    else{
                        pic=res.data.path;
                    }
                    var uploadVal=$(item).parent().children('.zz-upload-value');
                    var uploadShow=$(item).parent().children('.zz-upload-pic-show');
                    uploadVal.val(pic);
                    uploadShow.html('<div class="zz-upload-pic-show-item"><a class="layui-icon layui-icon-close" title="删除"></a><img alt="" src="'+pic+'"></div>');
                    uploadShow.show();
                }
                else{
                    top.layer.msg(res.msg?res.msg:'上传失败',{icon: 2,time:res.wait*1000});
                }
            }
            ,choose:function (obj) {
                $(this.item).attr('disabled','disabled');
                $(this.item).html('<i class="layui-icon layui-icon-loading layui-icon layui-anim layui-anim-rotate layui-anim-loop"></i>上传中...');
            }
            ,error:function () {
                $(this.item).removeAttr('disabled');
                $(this.item).html('<i class="layui-icon">&#xe67c;</i>上传图片');
                top.layer.msg('上传失败',{icon: 2,time:data.wait*1000});
            }
        });

        upload.render({
            elem: '.zzBtnUploadPicMultiple',
            url:UPLOAD_PIC_URL
            ,multiple:true
            ,done: function(res, index, upload){
                var item = this.item;
                $(item).removeAttr('disabled');
                $(item).html('<i class="layui-icon">&#xe67c;</i>上传图片');
                if(res.code==1){
                    var pic='';
                    if(res.data.url!=''){
                        pic=res.data.url;
                    }
                    else{
                        pic=res.data.path;
                    }
                    var uploadVal=$(item).parent().children('.zz-upload-value');
                    var uploadShow=$(item).parent().children('.zz-upload-pic-show');
                    var uploadValArr=[];

                    if($.trim(uploadVal.val())!=''){
                        uploadValArr=uploadVal.val().split(',')
                    }

                    uploadValArr.push(pic);
                    uploadVal.val(uploadValArr.join(','));
                    uploadShow.append('<div class="zz-upload-pic-show-item"><a class="layui-icon layui-icon-close" title="删除"></a><img alt="" src="'+pic+'"></div>');
                    uploadShow.show();
                }
                else{
                    top.layer.msg(res.msg?res.msg:'上传失败',{icon: 2,time:res.wait*1000});
                }
            }
            ,choose:function (obj) {
                $(this.item).attr('disabled','disabled');
                $(this.item).html('<i class="layui-icon layui-icon-loading layui-icon layui-anim layui-anim-rotate layui-anim-loop"></i>上传中...');
            }
            ,error:function () {
                $(this.item).removeAttr('disabled');
                $(this.item).html('<i class="layui-icon">&#xe67c;</i>上传图片');
                top.layer.msg('上传失败',{icon: 2,time:2000});
            }
        });

        $('.zz-upload-pic-show').on('click','.layui-icon-close',function () {
            var domParent=$(this).parent();
            var uploadVal=domParent.parent().prev();
            var uploadValArr=uploadVal.val().split(',');
            uploadValArr.splice(domParent.index(),1);
            uploadVal.val(uploadValArr.join(','));
            domParent.remove();
        });
        
        $('.zz-upload-value').each(function () {

            var uploadVal=$(this).val();
            if($.trim(uploadVal)==''){
                return;
            }
            var uploadValArr=uploadVal.split(',');
            var uploadShow=$(this).next();
            for(var x in uploadValArr){
                uploadShow.append('<div class="zz-upload-pic-show-item"><a class="layui-icon layui-icon-close" title="删除"></a><img alt="" src="'+uploadValArr[x]+'"></div>');
            }
        })

    }


});