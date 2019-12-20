<?php
// +----------------------------------------------------------------------
// | HulaCWMS 呼啦企业网站管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://www.zhuopro.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 灼灼文化
// +----------------------------------------------------------------------

namespace app\admin\controller;
use Qiniu\Auth as Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use OSS\OssClient;
use OSS\Core\OssException;

class Upload extends Base
{
    /**
     * @var string 错误信息
     */
    public $error = '';
    public $info;

    /**
     * 图片上传
     */
    public function picture(){
        $config=config('PICTURE_UPLOAD');
        $re=$this->pic_video($config);
        if($re){
            $this->success("上传成功！",'',$this->info);
        }
        else{
            $this->error($this->error);
        }
    }

    /**
     * 视频上传
     */
    public function video(){
        $config=config('VIDEO_UPLOAD');
        $re=$this->pic_video($config);
        if($re){
            $this->success("上传成功！",'',$this->info);
        }
        else{
            $this->error($this->error);
        }
    }

    /**
     * 文件附件上传
     */
    public function file(){
        $config=config('FILE_UPLOAD');
        $re=$this->pic_video($config);
        if($re){
            $this->success("上传成功！",'',$this->info);
        }
        else{
            $this->error($this->error);
        }
    }

    /**
     * 图片和视频上传方法
     */
    public function pic_video($config,$formFile='file'){
        $rootPath=$config['rootPath'];

        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file($formFile);
        // 验证文件大小和文件类型
        $info = $file->validate(['size'=>$config['maxSize'],'ext'=>$config['exts']]);
        //获取文件md5用以验证是否曾上传过。
        $fileMd5=$info->md5();
        $pic=db('picture')->where('md5',$fileMd5)->where('status','egt',-1)->find();

        //如果上传过，直接从数据库中拉取图片地址。
        if($pic){
            $data['path']=$pic['path'];
            $data['url']=$pic['url'];
            $this->info=$data;
            return true;
        }


        //保存到站点目录下
        $info = $info->move( $rootPath);
        if($info){
            $saveFileName=$info->getSaveName();
            $savePath=$rootPath.'/'.str_replace('\\','/',$saveFileName);
            $saveUrl='';

            //判断是否开启了外部存储。
            $picUploadDriver=config('PICTURE_UPLOAD_DRIVER');
            if((int)$picUploadDriver===1){
                //七牛云存储
                require_once APP_PATH . '/../vendor/Qiniu/autoload.php';
                // 需要填写你的 Access Key 和 Secret Key
                $accessKey = config('QINIU_ACCESS_KEY');
                $secretKey = config('QINIU_SECRET_KEY');


                // 构建鉴权对象
                $auth = new Auth($accessKey, $secretKey);

                // 要上传的空间
                $bucket = config('QINIU_BUCKET');
                $domain = config('QINIU_DOMAIN');
                $token = $auth->uploadToken($bucket);
                // 初始化 UploadManager 对象并进行文件的上传
                $uploadMgr = new UploadManager();
                // 调用 UploadManager 的 putFile 方法进行文件的上传
                list($ret, $err) =$uploadMgr->putFile($token, $savePath, $savePath);
                if ($err == null) {
                    $saveUrl=$domain.$savePath;
                }
            }
            elseif((int)$picUploadDriver===2){
                //阿里云OSS
                try{
                    $ossClient= new OssClient(config('ALIYUN_OSS_KEYID'), config('ALIYUN_OSS_KEYSECRET'), config('ALIYUN_OSS_ENDPOINT'));
                    $ossClient->uploadFile(config('ALIYUN_OSS_BUCKET'), $savePath, $savePath);
                    $saveUrl=config('ALIYUN_OSS_DOMAIN').$savePath;
                }
                catch(OssException $e) {
                    //如果出错这里返回报错信息
                    trace('上传到阿里云oss错误：'.$e->getMessage(),'error');
                }
            }


            //将文件信息保存到数据库中
            $fileSha1=$info->sha1();
            $data['path']='/'.$savePath;
            $data['url']=$saveUrl;
            $insertData['path']=$data['path'];
            $insertData['url']=$data['url'];
            $insertData['md5']=$fileMd5;
            $insertData['sha1']=$fileSha1;
            $insertData['create_time']=time();
            db('picture')->insert($insertData);
            $this->info=$data;
            return true;
        }
        else{
            $this->error="上传失败：".$file->getError();
            return false;
        }


    }

}
