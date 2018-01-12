<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Utils\Document;
use App\Http\Entity\ClientUser;
use App\Http\Entity\Files\FilesIndex;

class FileController extends Controller
{

    private $document;

    public function __construct(Request $request, Document $document)
    {
        $this->document = $document;
        if (env('APP_ENV') == 'dev') {
            $request->offsetSet('username', 'dxy_test');
            $request->offsetSet('password', 'dxy123123123');
        }
    }

    /**
     * 上传课件
     *
     * @return array
     */
    public function upload(Request $request) 
    {
        if ($request->isMethod('post')) {
            $file = $request->file('file');
            if ($file->isValid()) {
                $client_id = ClientUser::getClientId($request->get('username'));
                $uploaded_file = $this->document->uploadFile($file, $client_id);
                // // 文档转换
                if ($uploaded_file) {
                    return $this->api($uploaded_file);
                }
                return $this->apiError('文档上传失败');
            }
        }
        return $this->apiError('参数错误');
    }

    /**
     * 获取文档转换的所有图片
     * @param $fid
     * @param $progress
     */
    public function docImages(Request $request, $fid) 
    {
        $client_id = ClientUser::getClientId($request->get('username'));
        $images = $this->document->getDocImages($fid, $client_id);
        if ($images) {
            return $this->api($images);
        } else {
            return $this->api([], '未找到文档图片');
        }
    }

    /**
     * 七牛回调处理
     * @param $fid
     * @param $progress
     */
    public function notify(Request $request, $fid, $progress) 
    {
        $uploaded_file = $this->document->docNotify($fid, $progress);
    }

}
