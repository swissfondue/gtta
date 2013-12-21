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
     * @throws Exception
     */
    public static function createDir($dir, $perms) {
        if (!@mkdir($dir, $perms)) {
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
     * Get directory contents
     * @param $source
     * @return array
     * @throws Exception
     */
    public static function getDirectoryContents($source) {
        if (!is_dir($source)) {
            throw new Exception("Not a directory: $source");
        }

        $contents = array();

        foreach (scandir($source) as $file) {
            if ($file == "." || $file == "..") {
                continue;
            }

            $contents[] = $file;
        }

        return $contents;
    }
}