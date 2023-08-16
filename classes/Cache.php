<?php

/*
 * A very simple and primitive file-based cache system
 */

namespace UpcomingGames;

class Cache {
    private $cacheDir;
    private $cacheDuration;

    public function __construct($cacheDir = 'cache', $cacheDuration = 3600) {
        $this->cacheDir = $cacheDir;
        $this->cacheDuration = $cacheDuration;

        if (!file_exists($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    public function getCacheKey($string) {
        return md5($string) . '.cache';
    }

    public function set($key, $data) {
        $filename = $this->getCacheKey($key);
        file_put_contents($this->cacheDir . '/' . $filename, serialize($data));

        // Call the cleanup method when setting a new cache
        $this->clear_expired_cache();
    }

    public function get($key) {
        $filename = $this->getCacheKey($key);
        $fullpath = $this->cacheDir . '/' . $filename;

        if (!file_exists($fullpath)) {
            return null;
        }

        if (filemtime($fullpath) + $this->cacheDuration < time()) {
            unlink($fullpath);
            return null;
        }

        return unserialize(file_get_contents($fullpath));
    }

    public function clear_expired_cache() {
        $files = scandir($this->cacheDir);
        $currentTime = time();

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {  // Exclude directory pointers
                $filePath = $this->cacheDir . '/' . $file;
                if (is_file($filePath)) {
                    $fileTime = filemtime($filePath);
                    if ($fileTime + $this->cacheDuration < $currentTime) {
                        unlink($filePath);
                    }
                }
            }
        }
    }
}