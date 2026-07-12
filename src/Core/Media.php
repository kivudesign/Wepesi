<?php

declare(strict_types=1);

namespace Wepesi\Core;

use Exception;
use InvalidArgumentException;
use RuntimeException;

/**
 *
 */
class Media
{
    /**
     *
     */
    private const MAXWIDTH = 480;
    /**
     *
     */
    private const MAXHEIGHT = 400;
    /**
     * @var string
     */
    private string $target_dir;

    /**
     * @param string $target_dir
     */
    public function __construct(string $target_dir = "/public")
    {
        $this->target_dir = $target_dir;
    }

    /**
     * @param array<string> $file
     * @return array<string>
     * @throws Exception
     */
    public function uploadImg(array $file): array
    {
        if (!isset($file["name"])) {
            throw new Exception("try to access to key `name` which does not exist");
        }
        $target_file = $this->target_dir . basename($file['name']);
        $file_type = pathinfo($target_file, PATHINFO_EXTENSION);
        $file_extension = array('jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG');
        if (!in_array($file_type, $file_extension)) {
            throw new Exception('extension not supported');
        }
        if ($res = $this->upload($file, ("photos/" . $file_type), $file_type)) {
            return $res;
        }
        throw new Exception('error uploading image');
    }

    /**
     * @param array<string> $file
     * @param string $format_file
     * @param string $filetype
     * @return array<string>
     * @throws Exception
     */
    private function upload(array $file, string $format_file, string $filetype): array
    {
        $target_dir = $this->target_dir . '/' . date('Y') . '/' . date('m') . '/';

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = $file['name'];
        $renamed_file = $this->renameFile($file_name);
        if (! move_uploaded_file($file['tmp_name'], $target_dir . $renamed_file)) {
            throw new Exception('unable to upload the file');
        }
        $format = explode("/", $format_file);
        if ($format[0] == "photos") {
            if (! $this->thumbnail($renamed_file, $target_dir, $target_dir, $format[1])) {
                throw new Exception('error thumbnail');
            }
        }
        return [
            "name" => $file_name,
            "extension" => $filetype,
            "link" => $target_dir . $renamed_file
        ];
    }

    /**
     * @param string $img
     * @return string
     */
    private function renameFile(string $img): string
    {
        $last_id = (string)strtotime("now");
        $explode = explode(".", $img);
        $ext = end($explode);
        $store = date('y') . Md5($last_id) . date('m');
        return $store . '.' . $ext;
    }

    /**
     * @param string $image_to_convert
     * @param string $source
     * @param string $dest
     * @param string $format
     * @return bool
     */
    private function thumbnail(string $image_to_convert, string $source, string $dest, string $format): bool
    {
        $image_created = $source . $image_to_convert;
        if ($image_created) {
            $info = getimagesize($image_created);
            if ($info !== false) {
                [$width, $height] = $info;
            } else {
                throw new RuntimeException('Invalid image file.');
            }

            $source = $format != "png" ? imagecreatefromjpeg($image_created) : imagecreatefrompng($image_created);
            if (! $source) {
                throw new RuntimeException('Failed to create image resource.');
            }
            if (Media::MAXWIDTH >= $width && Media::MAXHEIGHT >= $height) {
                $ratio = 1;
            } elseif ($width > $height) {
                $ratio = Media::MAXWIDTH / $width;
            } else {
                $ratio = Media::MAXHEIGHT / $height;
            }

            $thumb_width = (int)round($width * $ratio); //get the smaller value from cal # floor()
            $thumb_height = (int)round($height * $ratio);
            if ($thumb_width < 1) {
                throw new InvalidArgumentException("width should be greater than 0");
            }
            if ($thumb_height < 1) {
                throw new InvalidArgumentException("height should be greater than 0");
            }
            $thumb = imagecreatetruecolor($thumb_width, $thumb_height);
            imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);

            $path = $dest . $image_to_convert;
            imagejpeg($thumb, $path, 60);
            imagedestroy($thumb);
            imagedestroy($source);
            return true;
        }
        return false;
    }

    /**
     * @param array<string> $file
     * @param string|null $file_extension
     * @return array<string>
     * @throws Exception
     */
    public function uploadSingleFile(array $file, ?string $file_extension = null): array
    {
        if (!isset($file["name"])) {
            throw new Exception("try to access to key `name` which does not exist");
        }
        $target_file = $this->target_dir . basename($file["name"]);
        $file_type = pathinfo($target_file, PATHINFO_EXTENSION);
        if ($file_extension) {
            if ($file_type != $file_extension) {
                throw new Exception("File type does not match the expected extension");
            }
            return $this->upload($file, ("$file_extension/" . $file_type), $file_type);
        } else {
            return $this->upload($file, ("media/" . $file_type), $file_type);
        }
    }
}
