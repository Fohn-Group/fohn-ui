<?php

declare(strict_types = 1);


class FileService {
    public static int $id = 0;

    /**
     * Scan a specific directory recursively and return an array of files data ready for import
     * into File model.
     */
    public static function scanDirectory(string $path, int $parentId = null): array {
        $files = [];
        $srcFiles = scandir($path);
        foreach ($srcFiles as $k  => $file) {
            $filePath = $path . '/' . $file;
            $size = filesize($filePath);
            // check if dir and re scan
            if (is_dir($filePath)) {
                if (substr($file, 0, 1) !== '.') {
                    self::$id++;
                    $files[] = ['id' => self::$id, 'name' => $file, 'size' => $size, 'ext' => '', 'is_folder' => true, 'parent_id' => $parentId];
                    $files = array_merge($files, self::scanDirectory($filePath, self::$id));
                }
            } else {
                if (substr($file, 0, 1) !== '.') {
                    self::$id++;
                    $info = pathinfo($filePath);
                    $files[] = ['id' => self::$id, 'name' => $info['basename'], 'size' => $size, 'ext' => $info['extension'], 'is_folder' => false, 'parent_id' => $parentId];
                }
            }
        }

        return $files;
    }
}
