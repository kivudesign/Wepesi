<?php
    class Media{
        private $_target_dir;
        const maxWidth = 480;
        const maxHeigth = 400; 

        function __construct($target_dir)
        {
            $this->_target_dir= $target_dir;
            
        }
        function uploadImg($file){
            $target_file = $this->_target_dir . basename($file["name"]);
            $filetype = pathinfo($target_file, PATHINFO_EXTENSION);
            $fileExt = array('jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG');
            if (in_array($filetype, $fileExt)) {                
                if($res=$this->upload($file,("photos/".$filetype),$filetype)){
                    return $res;
                }
            } else {
                return json_encode(array("exte"=> "extension not "));
            }                      
        }
        private function upload($file,$formatImg, $filetype){
            $Link = $this->_target_dir . '/' . $formatImg.'/' . date('Y') . '/' . date('m') . '/';
            $target_dir = $Link;
            
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $filename = $file['name'];
            $imageRenamed = $this->renameFile($filename);
            $fileToUpload = (string) $imageRenamed[0];                
            if(!move_uploaded_file($file['tmp_name'], $target_dir . $fileToUpload)){
                return false;
            }
            $format=explode("/", $formatImg);
            if($format[0]=="photos"){
                $value = $this->thumbnail($fileToUpload, $target_dir, $Link,$format[1]);
                if (!$value) {
                    return json_decode(array("errorOperation"=> "erreur thumbnail"));
                }
            }
            return json_encode(["name"=>$filename, "extension"=>$filetype,"link"=> $Link . $fileToUpload]);
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
            $imageCreated = $source . $imageToConvert;
            $thumb=null;
            if ($imageCreated) {
                list($width, $height) = getimagesize($imageCreated); //$type will return the type of the image
                $source = $format!="png"? imagecreatefromjpeg($imageCreated): imagecreatefrompng($imageCreated);

                if (Media::maxWidth >= $width &&  Media::maxHeigth >= $height) {
                    $ratio = 1;
                } elseif ($width > $height) {
                    $ratio = Media::maxWidth / $width;
                } else {
                    $ratio =  Media::maxHeigth / $height;
                }

                $thumb_width = round($width * $ratio); //get the smaller value from cal # floor()
                $thumb_height = round($height * $ratio);

                $thumb = imagecreatetruecolor($thumb_width, $thumb_height);
                imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);

                $path = $dest . $imageToConvert;
                $image_thumb_path = $path;
                imagejpeg($thumb, $path, 60);
                return true;
            } else {
                return false;
            }
            imagedestroy($thumb);
            imagedestroy($source);
        }

    }
?>