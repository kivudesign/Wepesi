<?php

namespace Wepesi\App\Core;

    class Media{
        private $_target_dir;
        const maxWidth = 480;
        const maxHeigth = 400; 

        function __construct($target_dir="media")
        {
            $this->_target_dir= $target_dir;
        }
        function uploadImg($file){
            try{
                $target_file = $this->_target_dir . basename($file["name"]);
                $filetype = pathinfo($target_file, PATHINFO_EXTENSION);
                $possibleExtention = array('jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG');
                if (!in_array($filetype, $possibleExtention)) {
                    throw new \Exception("extension not supported");
                }
                return $this->upload($file, ("photos/" . $filetype), $filetype);
            }catch (\Exception $ex){
                return ["exception"=>$ex->getMessage()];
            }
        }
        private function upload($file,$formatImg, $filetype){
            try{
                $Link = $this->_target_dir . '/' . date('Y') . '/' . date('m') . '/';
                $target_dir = $Link;

                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $filename = $file['name'];
                $imageRenamed = $this->renameFile($filename);
                $fileToUpload = (string)$imageRenamed[0];
                if (!move_uploaded_file($file['tmp_name'], $target_dir . $fileToUpload)) {
                    throw new \Exception("upload failed");
                }
                $format = explode("/", $formatImg);
                if ($format[0] == "photos") {
                    $value = $this->thumbnail($fileToUpload, $target_dir, $Link, $format[1]);
                    if (!$value) {
                        throw new \Exception ("erreur thumbnail");
                    }
                }
                return json_encode(["name" => $filename, "extension" => $filetype, "link" => $Link . $fileToUpload]);
            }catch (\Exception $ex){
                return ["exception"=>$ex->getMessage()];
            }
        }
                
        private function renameFile($img)
        {
            $lastid=TIME();
            $boom = explode(".", $img);
            $ext = end($boom);
            $img = date('y') . Md5($lastid) . date('m') . '.' . $ext;
            $store = date('y') . Md5($lastid) . date('m');
            return array($img, $store);
        }
        private function thumbnail($imageToConvert, $source, $dest,$format)
        {
            try{
                $imageCreated = $source . $imageToConvert;
                if(!$imageCreated){
                    throw new \Exception("unable to convert image");
                }
                list($width, $height) = getimagesize($imageCreated); //$type will return the type of the image
                $source = $format != "png" ? imagecreatefromjpeg($imageCreated) : imagecreatefrompng($imageCreated);

                if (Media::maxWidth >= $width && Media::maxHeigth >= $height) {
                    $ratio = 1;
                } elseif ($width > $height) {
                    $ratio = Media::maxWidth / $width;
                } else {
                    $ratio = Media::maxHeigth / $height;
                }

                $thumb_width = round($width * $ratio); //get the smaller value from cal # floor()
                $thumb_height = round($height * $ratio);

                $thumb = imagecreatetruecolor($thumb_width, $thumb_height);
                imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);

                $path = $dest . $imageToConvert;
//                $image_thumb_path = $path;
                imagewebp($thumb, $path, 50);

                imagedestroy($thumb);
                imagedestroy($source);
                return true;
            }catch (\Exception $ex){
                return ["exception"=>$ex->getMessage()];
            }
        }
    }
?>