<?php

namespace App\Http;

use App\Http\BaseController;
use App\Traits\Databases;

class ImageController extends BaseController
{
    use Databases;

    public function __construct()
    {
        // Databases::init();

        $check = self::checkHeader();
        if ($check['state'] === false) {
            return BaseController::serverResponse($check, 403);
        }
    }

    public static function start()
    {

        $newfileBasename = BaseController::uuidSecure();

        $MediaCateogry = BaseController::$MediaCategory;
        $MediaFile = BaseController::$MediaFile;

        if (isset($MediaFile['error']) && $MediaFile['error'] === UPLOAD_ERR_OK) {
            // get details of the uploaded file
            $fileTmpPath = $MediaFile['tmp_name'];
            $fileName = $MediaFile['name'];
            $fileSize = $MediaFile['size'];
            $fileType = $MediaFile['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // sanitize file-name
            $newFileName = $newfileBasename . '.' . $fileExtension;
            $newSubDir = sha1(date("Ymd"));

            // check if file has one of the following extensions
            $allowedfileExtensions = array('jpeg', 'jpg', 'gif', 'png', 'zip', 'txt', 'xls', 'doc');

            if (in_array($fileExtension, $allowedfileExtensions)) {
                // directory in which the uploaded file will be moved
                $uploadFileDir = $_SERVER["DOCUMENT_ROOT"] . "/storage/{$MediaCateogry}/" . $newSubDir;
                $uploadFileDestpath = "/storage/{$MediaCateogry}/" . $newSubDir;
                $uploadFileURL = "/storage/{$MediaCateogry}/" . $newSubDir;

                try {

                    if(!is_dir($uploadFileDir)){
                        if(!mkdir($uploadFileDir, 0777, true)) {
                            BaseController::serverResponse([
                                'state' => false,
                                'message' => '처리중 문제가 발생 했습니다. (005)',
                            ], 500);
                        }
                    }

                    $dest_path = $uploadFileDir . "/" . $newFileName;
                    $dest_url = $uploadFileURL . "/" . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $uploadFileURL = "http://" . $_SERVER["HTTP_HOST"] . $dest_url;

                        // FIXME 2020-10-12 13:31 오라클 클라우드에서 mysql 접속이 안되기 떄문에 API 에서 처리 하기로.
                        // $result = Databases::insertNicapageMediaFiles([
                        //     'category' => $MediaCateogry,
                        //     'dest_path' => $uploadFileDestpath,
                        //     'file_name' => $newFileName,
                        //     'original_name' => $fileName,
                        //     'file_type' => $fileType,
                        //     'file_size' => $fileSize,
                        //     'file_extension' => $fileExtension,
                        // ]);

                        // if($result['state'] == false) {
                        //     BaseController::serverResponse([
                        //         'state' => false,
                        //         'message' => '처리중 문제가 발생 했습니다. (004)',
                        //         'error' => $result['error'],
                        //     ], 500);
                        // }

                        BaseController::serverResponse([
                            'state' => true,
                            'data' => [
                                'media_url' => $uploadFileURL,
                                'dest_path' => $uploadFileDestpath,
                                'new_file_name' => $newFileName,
                                'original_name' => $fileName,
                                'file_type' => $fileType,
                                'file_size' => $fileSize,
                                'file_extension' => $fileExtension,
                            ]
                        ], 201);
                    } else {
                        BaseController::serverResponse([
                            'state' => false,
                            'message' => '처리중 문제가 발생 했습니다. (004)',
                        ], 500);
                    }

                } catch (\Exception $exception){
                    BaseController::serverResponse([
                        'state' => false,
                        'message' => '처리중 문제가 발생 했습니다. (003)',
                        'error' => $exception->getMessage()
                    ], 500);
                    return;
                }
            } else {
                BaseController::serverResponse([
                    'state' => false,
                    'message' => '처리중 문제가 발생 했습니다. (002)',
                    'error' => 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions)
                ], 400);
            }
        } else {
            BaseController::serverResponse([
                'state' => false,
                'message' => '처리중 문제가 발생 했습니다. (001)',
                'error' => $_FILES['image']['error']
            ], 400);
        }
    }
}
