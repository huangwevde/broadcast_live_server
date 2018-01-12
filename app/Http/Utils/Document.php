<?php

namespace App\Http\Utils;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Entity\Files\FilesIndex;
use App\Http\Entity\Files\FilesDocImg;
use Qiniu\Http\Client as QiniuClient;

class Document
{

    protected $extArray = [
        'image' => ['gif', 'jpg', 'jpeg', 'png', 'bmp'],
        'file' => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'],
    ];

    protected $fileType = [
        'image' => 1, 'ppt' => 2, 'pptx' => 2, 'pdf' => 3, 
    ];

    // 签权对象
    protected $auth;

    // 七牛云服务domain
    protected $qiniu_domain = '';

    // 七牛回调地址
    protected $callBack = '';

    public function __construct()
    {
        if (env('APP_ENV') == 'dev') {
            $this->callBack = 'http://2520ed36.ngrok.io/api/qiniu/notify/';
        } else {
            $this->callBack = url('api/qiniu/notify/');
        }

        // QINIU_DOMAIN
        $this->qiniu_domain = env('QINIU_DOMAIN', 'http://7xs790.com1.z0.glb.clouddn.com/');

        // 初始化签权对象
        $this->auth = $this->docQiniuAuth();
    }

    /**
     * 上传课件
     *
     * @return array
     */
    public function uploadFile($file, $client_id) 
    {
        $ext = $file->getClientOriginalExtension();
        $fileName = md5_file($file);
        // if is exist
        if (Storage::disk('upload')->exists($fileName . '.' . $ext)) {
            $exist_file = FilesIndex::getBy(['client_id' => $client_id, 'store_name' => $fileName . '.' . $ext]);
            if ($exist_file) {
                return $exist_file;
            }
        }
        // upload
        $uploaded_file = $this->doUpload($file, $fileName . '.' . $ext, $client_id);
        if ($uploaded_file) {
            // 文档转换
            $this->docConvert($uploaded_file);
            return $uploaded_file;
        }
        return false;
    }

    /**
     * 七牛回调处理
     * @param $fid
     * @param $progress
     */
    public function docNotify($fid, $progress) 
    {
        // 获取notify通知的body信息
        $notify_body = file_get_contents('php://input');
        $data = json_decode($notify_body, true);

        BizLog::info('七牛文档转换服务 回调' . 'Debug information: docNotify');
        BizLog::info($data);

        if (!$data['code']) {
            // 七牛镜像空间
            $bucket = env('qiniu_bucket', 'ppt-conversion');

            // 初始化签权对象
            $auth = $this->auth;
            switch ($progress) {
                case 'pdf':
                    $key = $data['items'][0]['key'];
                    // 七牛回调URL
                    $notify_url = $this->callBack . $fid . '/page_number';

                    $body = 'bucket=' . $bucket . '&key=' . $key . '&fops=yifangyun_preview/v2/ext=pdf/action=get_page_count&notifyURL=' . $notify_url;
                    $this->docHttpRequest($auth, $body);
                    break;
                case 'page_number':
                    $url = $this->qiniu_domain . $data['items'][0]['key'];
                    $content = file_get_contents($url);
                    $page_count = json_decode($content, true);

                    for ($i = 1; $i <= $page_count['page_count']; $i++) {
                        $key = $data['inputKey'];
                        // 七牛回调URL
                        $notify_url = $this->callBack . $fid . '/jpg';

                        $body = 'bucket=' . $bucket . '&key=' . $key . '&fops=yifangyun_preview/v2/ext=pdf/format=jpg/page_number=' . $i . '&notifyURL=' . $notify_url;
                        $this->docHttpRequest($auth, $body);
                    }
                    break;
                case 'jpg':
                    $key = $data['items'][0]['key'];
                    $cmd = $data['items'][0]['cmd'];
                    $serial_number = substr(explode('/', $cmd)[4], 12);

                    $content = file_get_contents($this->qiniu_domain . $key);

                    // 文档
                    $file_index = FilesIndex::getBy(['id' => $fid]);
                    if ($file_index) {
                        $doc_directory = explode('.', $file_index['store_name'])[0];
                        // 如果图片目录不存在则创建
                        if (!is_dir(storage_path('app/upload/') . $doc_directory)) {
                            Storage::disk('upload')->makeDirectory($doc_directory);
                        }
                        // 生产图片文件
                        $isStore = Storage::disk('upload')->put($doc_directory . '/' . $serial_number . '.jpg', $content);
                        if ($isStore) {
                            $data = [
                                'fid' => $fid,
                                'page_number' => $serial_number,
                                'path' => storage_path('app/upload/') . $doc_directory . '/' . $serial_number . '.jpg',
                            ];
                            FilesDocImg::store($data);
                        }
                    }
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * 获取文档转换的所有图片
     * @param $fid
     */
    public function getDocImages($fid, $client_id) 
    {
        $file = FilesIndex::getBy(['id' => $fid, 'client_id' => $client_id]);
        if ($file) {
            return FilesDocImg::getImgsByFid($fid);
        } else {
            return [];
        }
    }

    /**
     * 上传硬盘
     *
     * @return array
     */
    protected function doUpload($file, $fileName, $client_id) 
    {
        // disk store
        $isStore = Storage::disk('upload')->put($fileName, file_get_contents($file->getRealPath()));
        if ($isStore) {
            return $this->storeFile($file, $client_id);
        }
        return false;
    }

    /**
     * 七牛云文档转换
     * @param $file
     */
    protected function docConvert($file) 
    {
        BizLog::info($file);
        if (!$file || empty($file['id'])) {
            return false;
        }
        // 初始化签权对象
        $auth = $this->auth;
        // 七牛镜像空间
        $bucket = env('qiniu_bucket', 'ppt-conversion');

        // 上传到七牛云空间
        $result = $this->docQiniuUpload($auth, $file, $bucket);

        if ($result) {
            // 文件扩展名
            if ($file['extension'] == 'pdf') {
                // 七牛回调URL
                $notify_url = $this->callBack . $file['id'] . '/page_number';
                $body = 'bucket=' . $bucket . '&key=' . $result['key'] . '&fops=yifangyun_preview/v2/ext=pdf/action=get_page_count&notifyURL=' . $notify_url;
            } else {
                // 七牛回调URL
                $notify_url = $this->callBack . $file['id'] . '/pdf';
                $body = 'bucket=' . $bucket . '&key=' . $result['key'] . '&fops=yifangyun_preview/v2&notifyURL=' . $notify_url;
            }
            $this->docHttpRequest($auth, $body);
        }
    }

    /**
     * DB保存
     * @param array $data
     * @param int $client_id
     * @return mixed
     */
    protected function storeFile($file, $client_id) 
    {
        // 文件属性
        $ext = $file->getClientOriginalExtension();
        if (in_array($ext, $this->extArray['image'])) {
            $type = $this->fileType['image']; // 图片
        } else {
            $type = !empty($this->fileType[$ext]) ? $this->fileType[$ext] : 4; 
        }
        $data = [
            // FIXME
            'client_id' => $client_id,
            'name' => date('Ymdhis') . '_' . uniqid(),
            'original_name' => $file->getClientOriginalName(),
            'store_name' => md5_file($file) . '.' . $ext,
            'path' => $file->getRealPath(),
            'size' => $file->getSize(),
            'extension' => $ext,
            'type' => $type,
        ];
        $res = FilesIndex::store($data);
        if ($res) {
            $data['id'] = $res;
            return $data;
        }
        return false;
    }

    /**
     * 上传文件到七牛
     */
    protected function docQiniuUpload($auth, $file, $bucket) 
    {
        $token = $auth->uploadToken($bucket);
        $uploadMgr = new UploadManager();

        // 文件是否已存在
        $bucketManager = new BucketManager($auth);
        $stat = $bucketManager->stat($bucket, $file['store_name']);
        if (!empty($stat[0]['hash'])) {
            return ['hash' => $stat[0]['hash'], 'key' => $file['store_name']];
        }

        // 文件绝对路径
        $file_realpath = storage_path('app/upload') . '/' . $file['store_name'];
        if (!file_exists($file_realpath)) {
            BizLog::info('七牛文档转换服务 上传文件:' . 'Error information: ');
            BizLog::info($file_realpath.' is not exists!');
            return false;
        }

        // 文件扩展名
        $file_extension = $file['extension'];
        if (!in_array($file_extension, $this->docSupportExt())) {
            BizLog::info('七牛文档转换服务 上传文件:' . 'Error information: 非允许的文件类型');
            return FALSE;
        }

        // 上传文件
        $key = $file['store_name'];
        list($ret, $err) = $uploadMgr->putFile($token, $key, $file_realpath);
        if ($err !== NULL) {
            BizLog::info('七牛文档转换服务 上传文件:' . 'Error information: ');
            BizLog::info($err);
            return FALSE;
        } else {
            BizLog::info('七牛文档转换服务 上传文件:' . 'Debug information: ');
            BizLog::info($ret);
            return $ret;
        }
    }

    /**
     * 七牛支持转换的文件格式
     * @return array
     */
    protected function docSupportExt() 
    {
        return array('doc','docx','odt','rtf','wps','ppt','pptx','odp','dps','xls','xlsx','ods','csv','pdf',);
    }

    /**
     * 获取签权对象
     * @return \Qiniu\Auth
     */
    protected function docQiniuAuth() 
    {
        // 用于签名的公钥和私钥.
        $access_key = env('QINIUYUN_ACCESS_KEY', 'SO7B9aAX4rSSlkClhZVhz0cnYXuMUnOksAJBVdSc');
        $secret_key = env('QINIUYUN_SECRET_KEY', 'k0snzRIRuuXQnDd8PzIPAmJKYZXVrI5aUe0SIQTd');

        // 初始化签权对象
        $auth = new Auth($access_key, $secret_key);
        return $auth;
    }

    /**
     * 文档转换服务（yifangyun_preview）
     * @param $auth
     * @param $body
     */
    protected function docHttpRequest($auth, $body) 
    {
        $authorization = $auth->authorization('/pfop/', $body, 'application/x-www-form-urlencoded');

        $data = QiniuClient::post('http://api.qiniu.com/pfop/', $body, array('Content-Type' => 'application/x-www-form-urlencoded') + $authorization);
        BizLog::info('七牛文档转换服务 转PDF' . 'Debug information: ');
        BizLog::info($data);

        return $data;
    }

}
