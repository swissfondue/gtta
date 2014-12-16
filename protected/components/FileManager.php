<?php

/**
 * File manager class
 */
class FileManager {
    /**
     * Remove directory recursively
     * @param $path
     */
    public static function rmDir($path) {
        if (!is_dir($path)) {
            @unlink($path);
            return;
        }

        foreach (scandir($path) as $file) {
            if ($file == "." || $file == "..") {
                continue;
            }

            self::rmDir($path . "/" . $file);
        }

        @rmdir($path);
    }

    /**
     * Create directory
     * @param $dir
     * @param $perms
     * @param $recursive
     * @throws Exception
     */
    public static function createDir($dir, $perms, $recursive=false) {
        if (is_dir($dir)) {
            return;
        }

        if (!@mkdir($dir, $perms, $recursive)) {
            throw new Exception("Error creating directory: $dir");
        }
    }

    /**
     * Change permissions
     * @param $path
     * @param $perms
     * @throws Exception
     */
    public static function chmod($path, $perms) {
        if (!@chmod($path, $perms)) {
            throw new Exception("Error changing permissions: $path");
        }
    }

    /**
     * Change owner
     * @param $path
     * @param $user
     * @param $group
     * @throws Exception
     */
    public static function chown($path, $user, $group) {
        if (!@chown($path, $user) || !@chgrp($path, $group)) {
            throw new Exception("Error changing owner: $path");
        }
    }

    /**
     * Copy file
     * @param $source
     * @param $destination
     * @throws Exception
     */
    public static function copy($source, $destination) {
        if (!@copy($source, $destination)) {
            throw new Exception("Error copying file $source to $destination");
        }
    }

    /**
     * Unlink file
     * @param $path
     * @throws Exception
     */
    public static function unlink($path) {
        @unlink($path);
    }

    /**
     * Create symlink
     * @param $link
     * @param $target
     * @throws Exception
     */
    public static function createSymlink($link, $target) {
        if (!@symlink($target, $link)) {
            throw new Exception("Error creating symlink: $link");
        }
    }

    /**
     * Copy files recursively
     * @param $source
     * @param $destination
     * @throws Exception
     */
    public static function copyRecursive($source, $destination) {
        if (!is_dir($source)) {
            return;
        }

        foreach (scandir($source) as $file) {
            if ($file == "." || $file == "..") {
                continue;
            }

            $srcPath = $source . "/" . $file;
            $dstPath = $destination . "/" . $file;

            $perms = @fileperms($srcPath);

            if ($perms === false) {
                continue;
            }

            if (is_dir($srcPath)) {
                self::createDir($dstPath, $perms);
                self::copyRecursive($srcPath, $dstPath);
            } else {
                self::copy($srcPath, $dstPath);
            }
        }
    }

    /**
     * Zip directory
     * @param ZipArchive $zip
     * @param $source
     * @param $destination
     */
    public static function zipDirectory(ZipArchive $zip, $source, $destination=null) {
        if (!is_dir($source)) {
            return;
        }

        if ($destination) {
            $zip->addEmptyDir($destination);
        }

        foreach (scandir($source) as $file) {
            if ($file == "." || $file == "..") {
                continue;
            }

            $srcPath = $source . "/" . $file;

            if ($destination) {
                $dstPath = $destination . "/" . $file;
            } else {
                $dstPath = $file;
            }

            if (is_dir($srcPath)) {
                self::zipDirectory($zip, $srcPath, $dstPath);
            } else {
                self::zipFile($zip, $srcPath, $dstPath);
            }
        }
    }

    /**
     * Zip file
     * @param ZipArchive $zip
     * @param $src
     * @param $dst
     */
    public static function zipFile(ZipArchive $zip, $src, $dst) {
        $zip->addFile($src, $dst);
    }

    /**
     * Get directory contents
     * @param $source
     * @param bool $recursive
     * @return array
     * @throws Exception
     */
    public static function getDirectoryContents($source, $recursive=false) {
        if (!is_dir($source)) {
            throw new Exception("Not a directory: $source");
        }

        $contents = self::_dirContents($source, $recursive);

        return $contents;
    }

    /**
     * Get file content
     * @param $path
     */
    public static function getFileContent($path) {
        if (!is_file($path)) {
            return null;
        }

        return file_get_contents($path);
    }

    /**
     * Directory contents
     * @param $source
     * @param bool $r (recursive)
     * @param string $prefix
     * @return array
     */
    private function _dirContents($source, $r=false, $prefix='') {
        $contents = array();

        foreach (scandir($source) as $item) {
            if ($item == "." || $item == "..") {
                continue;
            }

            if (is_dir($source . DIRECTORY_SEPARATOR . $item) && $r) {
                $contents = array_merge($contents, self::_dirContents($source . DIRECTORY_SEPARATOR . $item, $r, $prefix . $item . DIRECTORY_SEPARATOR));
                continue;
            }

            $contents[] = $prefix . $item;
        }

        return $contents;
    }

    /**
     * Get file MIME type
     * @param $filePath
     * @return string
     * @throws Exception
     */
    public static function getMimeType($filePath) {
        if (!file_exists($filePath)) {
            throw new Exception("File does not exist: $filePath");
        }

        $fileInfo = finfo_open();

        return finfo_file($fileInfo, $filePath, FILEINFO_MIME_TYPE);
    }

    /**
     * Update file content or create new one if not exist
     * @param $path
     * @param $content
     */
    public static function updateFile($path, $content) {
        if (!file_exists($path)) {
            try {
                self::createDir(dirname($path),  0770, true);
            } catch (Exception $e) {
                throw $e;
            }
        }

        file_put_contents($path, $content);
    }
}