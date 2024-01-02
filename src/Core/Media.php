<?php

namespace Wepesi\Core;

class Media
{
    const maxWidth = 480;
    const maxHeigth = 400;
    private string $_target_dir;

    function __construct(string $target_dir = "public")
    {
        $this->_target_dir = $target_dir;
    }

    function uploadImg($file)
    {
        try {
            if (!isset($file["name"])) {
                throw new \Exception("try to access to key `name` witch dos not exist");
            }
            $target_file = $this->_target_dir . basename($file["name"]);
            $filetype = pathinfo($target_file, PATHINFO_EXTENSION);
            $fileExt = array('jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG');
            if (!in_array($filetype, $fileExt)) {
                $this->exception();
            }
            if ($res = $this->upload($file, ("photos/" . $filetype), $filetype)) {
                return $res;
            }
        } catch (\Exception $ex) {
            return ["exception" => $ex->getMessage()];
        }
    }

    private function exception(int $src_error = 0): array
    {
        $message = "extenstion not supported";
        switch ($src_error) {
            case 1:
                $message = "error thumbnail";
                break;
            case 5:
                $message = "try to access to key `name` witch dos not exist";
                break;
            case 20:
                $message = "unable to upload the file";
                break;
        }
        return ["exception" => $message];
    }

    private function upload($file, $formatFile, $filetype)
    {
        $Link = $this->_target_dir . '/' . date('Y') . '/' . date('m') . '/';
        $target_dir = $Link;

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $filename = $file['name'];
        $imageRenamed = $this->renameFile($filename);
        $fileToUpload = (string)$imageRenamed[0];
        if (!move_uploaded_file($file['tmp_name'], $target_dir . $fileToUpload)) {
            return $this->exception(10);
        }
        $format = explode("/", $formatFile);
        if ($format[0] == "photos") {
            $value = $this->thumbnail($fileToUpload, $target_dir, $Link, $format[1]);
            if (!$value) {
                return $this->exception(1);
            }
        }
        return [
            "name" => $filename,
            "extension" => $filetype,
            "link" => $Link . $fileToUpload
        ];
    }

    private function renameFile($img)
    {
        $lastid = TIME();
        $boom = explode(".", $img);
        $ext = end($boom);
        $img = date('y') . Md5($lastid) . date('m') . '.' . $ext;
        $store = date('y') . Md5($lastid) . date('m');
        return array($img, $store);
    }

    private function thumbnail($imageToConvert, $source, $dest, $format)
    {
        $imageCreated = $source . $imageToConvert;
        $thumb = null;
        if ($imageCreated) {
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
            $image_thumb_path = $path;
            imagejpeg($thumb, $path, 60);
            return true;
        } else {
            return false;
        }
        imagedestroy($thumb);
        imagedestroy($source);
    }

    function uploadSingleFile($file, string $extension_file = null)
    {
        try {
            if (!isset($file["name"])) {
                throw new \Exception("try to access to key `name` witch dos not exist");
            }
            $target_file = $this->_target_dir . basename($file["name"]);
            $filetype = pathinfo($target_file, PATHINFO_EXTENSION);
            if ($extension_file) {
                if ($filetype != $extension_file) {
                    return $this->exception();
                }
                return $this->upload($file, ("$extension_file/" . $filetype), $filetype);
            } else {
                return $this->upload($file, ("media/" . $filetype), $filetype);
            }
        } catch (\Exception $ex) {
            return ["exception" => $ex->getMessage()];
        }
    }
}