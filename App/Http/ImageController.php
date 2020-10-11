<?php

namespace App\Http;

use App\Http\BaseController;

class ImageController extends BaseController
{
    public function __construct()
    {
        $check = self::checkHeader();
        if ($check['state'] === false) {
            return BaseController::serverResponse($check, 403);
        }
    }

    public static function start()
    {
        // print_r($_SERVER);
        $newfileBasename = BaseController::uuidSecure();

        $MediaCateogry = BaseController::$MediaCategory;
        $MediaFile = BaseController::$MediaFile;

        $message = '';
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
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'zip', 'txt', 'xls', 'doc');

            if (in_array($fileExtension, $allowedfileExtensions)) {
                // directory in which the uploaded file will be moved
                $uploadFileDir = $_SERVER["DOCUMENT_ROOT"] . "/storage/{$MediaCateogry}/" . $newSubDir;
                $uploadFileURL = "/storage/{$MediaCateogry}/" . $newSubDir;

                if(!is_dir($uploadFileDir)){
                    mkdir($uploadFileDir, 0755);
                }

                $dest_path = $uploadFileDir . "/" . $newFileName;
                $dest_url = $uploadFileURL . "/" . $newFileName;


                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $uploadFileURL = "http://" . $_SERVER["HTTP_HOST"] . $dest_url;

                    BaseController::serverResponse([
                        'state' => true,
                        'media_url' => $uploadFileURL,
                    ], 201);

                } else {

                    BaseController::serverResponse([
                        'state' => false,
                        'message' => '처리중 문제가 발생 했습니다. (001)',
                    ], 500);
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
                'message' => '처리중 문제가 발생 했습니다. (003)',
                'error' => $_FILES['image']['error']
            ], 400);
        }
    }
}
