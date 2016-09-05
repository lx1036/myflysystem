<?php
/**
 * Created by PhpStorm.
 * User: liuxiang
 * Date: 9/1/16
 * Time: 1:40 PM
 */

namespace RightCapital\FlySystem\FlySystem\Adapter;

use finfo;
use RecursiveIteratorIterator;
use RightCapital\FlySystem\FlySystem\AdapterInterface;
use RightCapital\FlySystem\FlySystem\Config\Config;
use RightCapital\FlySystem\FlySystem\Exception\NotSupportedException;
use RightCapital\FlySystem\FlySystem\Exception\UnreadableFileException;
use RightCapital\FlySystem\FlySystem\FilesystemInterface;
use RightCapital\FlySystem\FlySystem\MimeType;
use RightCapital\FlySystem\FlySystem\Util;
use SplFileInfo;

class Local extends AbstractAdapter
{
    /**
     * @var int
     */
    const DISALLOW_LINKS = 0002;
    /**
     * @var array
     */
    protected static $permissions = [
        'file' => [
            'public'  => 0644,
            'private' => 0600,
        ],
        'dir'  => [
            'public'  => 0755,
            'private' => 0700,
        ],
    ];
    /**
     * @var string
     */
    protected $root;
    /**
     * @var array
     */
    protected $permissionMap;
    /**
     * @var int
     */
    protected $writeFlags;
    /**
     * @var int
     */
    protected $linkHandling;
    /**
     * @var string
     */
    protected $pathSeparator = '/';

    public function __construct($root, $writeFlags = LOCK_EX, $linkHandling = self::DISALLOW_LINKS, array $permissions = [])
    {
        $root                = is_link($root) ? realpath($root) : $root;
        $this->permissionMap = array_replace_recursive(static::$permissions, $permissions);
        $realRoot            = $this->ensureDirectory($root);

        if (!is_dir($realRoot) || !is_readable($realRoot)) {
            throw new \LogicException('The root path ' . $root . ' is not readable.');
        }

        $this->setPathPrefix($realRoot);
        $this->writeFlags   = $writeFlags;
        $this->linkHandling = $linkHandling;
    }

    public function has($path)
    {
        $location = $this->applyPathPrefix($path);

        return file_exists($location);
    }

    public function write($path, $contents, Config $config)
    {
        $location = $this->applyPathPrefix($path);
        $this->ensureDirectory(dirname($location));

        if (($size = file_put_contents($location, $contents, $this->writeFlags)) === false) {
            return false;
        }

        $type   = 'file';
        $result = compact('contents', 'type', 'size', 'path');

        if ($visibility = $config->get('visibility')) {
            $result['visibility'] = $visibility;
            $this->setVisibility($path, $visibility);
        }

        return $result;
    }

    public function writeStream($path, $resource, Config $config)
    {
        $location = $this->applyPathPrefix($path);
        $this->ensureDirectory(dirname($location));
        $stream = fopen($location, 'w+b');

        if (!$stream) {
            return false;
        }

        stream_copy_to_stream($resource, $stream);

        if (!fclose($stream)) {
            return false;
        }

        if ($visibility = $config->get('visibility')) {
            $this->setVisibility($path, $visibility);
        }

        return compact('path', 'visibility');
    }

    public function update($path, $contents, Config $config)
    {
        $location = $this->applyPathPrefix($path);
        $mimetype = Util::guessMimeType($path, $contents);
        $size     = file_put_contents($location, $contents, $this->writeFlags);

        if ($size === false) {
            return false;
        }

        return compact('path', 'size', 'contents', 'mimetype');
    }

    public function rename($path, $newpath)
    {
        $location    = $this->applyPathPrefix($path);
        $destination = $this->applyPathPrefix($newpath);

        return rename($location, $destination);
    }

    public function delete($path)
    {
        return unlink($this->applyPathPrefix($path));
    }

    public function deleteDir($dirname)
    {
        $location = $this->applyPathPrefix($dirname);

        if (!is_dir($location)) {
            return false;
        }

        $contents = $this->getRecursiveDirectoryIterator($location, RecursiveIteratorIterator::CHILD_FIRST);

        /** @var SplFileInfo $file */
        foreach ($contents as $file) {
            $this->guardAgainstUnreadableFileInfo($file);
            $this->deleteFileInfoObject($file);
        }

        return rmdir($location);
    }

    public function createDir($dirname)
    {
        // TODO: Implement createDir() method.
    }

    public function read($path)
    {
        $location = $this->applyPathPrefix($path);
        $contents = file_get_contents($location);

        if ($contents === false) {
            return false;
        }

        return compact('contents', 'path');
    }

    public function listContents($directory = '', $recursive = false)
    {
        $result   = [];
        $location = $this->applyPathPrefix($directory) . $this->pathSeparator;

        if (!is_dir($location)) {
            return [];
        }

        $iterator = $recursive ? $this->getRecursiveDirectoryIterator($location) : $this->getDirectoryIterator($location);

        foreach ($iterator as $file) {
            $path = $this->getFilePath($file);

            if (preg_match('#(^|/|\\\\)\.{1,2}$#', $path)) {
                continue;
            }

            $result[] = $this->normalizeFileInfo($file);
        }

        return array_filter($result);
    }

    public function getRecursiveDirectoryIterator($path, $mode = \RecursiveIteratorIterator::SELF_FIRST)
    {
        return new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS), $mode);
    }

    public function getDirectoryIterator($path)
    {
        return new \DirectoryIterator($path);
    }

    /**
     * Get the normalized path from a SplFileInfo object.
     *
     * @param SplFileInfo $file
     *
     * @return string
     */
    protected function getFilePath(SplFileInfo $file)
    {
        $location = $file->getPathname();
        $path     = $this->removePathPrefix($location);

        return trim(str_replace('\\', '/', $path), '/');
    }

    /**
     * Normalize the file info.
     *
     * @param SplFileInfo $file
     *
     * @return array
     */
    protected function normalizeFileInfo(SplFileInfo $file)
    {
        if (!$file->isLink()) {
            return $this->mapFileInfo($file);
        }

        if ($this->linkHandling & static::DISALLOW_LINKS) {
            throw NotSupportedException::forLink($file);
        }
    }

    /**
     * @param SplFileInfo $file
     *
     * @return array
     */
    protected function mapFileInfo(SplFileInfo $file)
    {
        $normalized = [
            'type' => $file->getType(),
            'path' => $this->getFilePath($file),
        ];

        $normalized['timestamp'] = $file->getMTime();

        if ($normalized['type'] === 'file') {
            $normalized['size'] = $file->getSize();
        }

        return $normalized;
    }

    public function getMetadata($path)
    {
        $location = $this->applyPathPrefix($path);
        $info     = new SplFileInfo($location);

        return $this->normalizeFileInfo($info);
    }

    public function getSize($path)
    {
        return $this->getMetadata($path);
    }

    public function getMimetype($path)
    {
        $location = $this->applyPathPrefix($path);
        $finfo    = new Finfo(FILEINFO_MIME_TYPE);
        $mimetype = $finfo->file($location);

        if (in_array($mimetype, ['application/octet-stream', 'inode/x-empty'])) {
            $mimetype = MimeType::detectByFilename($location);
        }

        return ['mimetype' => $mimetype];
    }

    public function getTimestamp($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * Ensure the root directory exists.
     *
     * @param string $dirname root directory path
     *
     * @return string real path to root
     *
     * @throws \Exception in case the root directory can not be created
     */
    protected function ensureDirectory($dirname)
    {
        if (!is_dir($dirname)) {
            $umask = umask(0);
            @mkdir($dirname, $this->permissionMap['dir']['public'], true);
            umask($umask);

            if (!is_dir($dirname)) {
                throw new \Exception(sprintf('Impossible to create the root directory "%s".', $dirname));
            }
        }

        return realpath($dirname);
    }

    /**
     * @param \SplFileInfo $file
     *
     * @throws \RightCapital\FlySystem\FlySystem\Exception\UnreadableFileException
     */
    private function guardAgainstUnreadableFileInfo(SplFileInfo $file)
    {
        if (!$file->isReadable()) {
            throw UnreadableFileException::forFileInfo($file);
        }
    }

    /**
     * @param \SplFileInfo $file
     */
    private function deleteFileInfoObject(SplFileInfo $file)
    {
        switch ($file->getType()) {
            case 'dir':
                rmdir($file->getRealPath());
                break;
            case 'link':
                unlink($file->getPathname());
                break;
            default:
                unlink($file->getRealPath());
        }
    }

    /**
     * Update a file using stream.
     *
     * @param                                                 $path
     * @param                                                 $resource
     * @param \RightCapital\FlySystem\FlySystem\Config\Config $config
     *
     * @return array|false
     */
    public function updateStream($path, $resource, Config $config)
    {
        return $this->writeStream($path, $resource, $config);
    }

    /**
     * Copy a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function copy($path, $newpath)
    {
        $location    = $this->applyPathPrefix($path);
        $destination = $this->applyPathPrefix($newpath);
        $this->ensureDirectory(dirname($destination));

        return copy($location, $destination);
    }

    /**
     * Set the visibility for a file.
     *
     * @param string $path
     * @param string $visibility
     *
     * @return array|false
     */
    public function setVisibility($path, $visibility)
    {
        $location = $this->applyPathPrefix($path);
        $type     = is_dir($location) ? 'dir' : 'file';
        $success  = chmod($location, $this->permissionMap[$type][$visibility]);

        if ($success === false) {
            return false;
        }

        return compact('visibility');
    }

    /**
     * Read a file as a stream.
     *
     * @param \RightCapital\FlySystem\FlySystem\string $path
     *
     * @return array|false
     */
    public function readStream($path)
    {
        $location = $this->applyPathPrefix($path);
        $stream   = fopen($location, 'rb');

        return compact('stream', 'path');
    }

    /**
     * Get the visibility of a file.
     *
     * @param \RightCapital\FlySystem\FlySystem\string $path
     *
     * @return array|false
     */
    public function getVisibility($path)
    {
        $location = $this->applyPathPrefix($path);
        clearstatcache(false, $location);
        $permissions = octdec(substr(sprintf('%o', fileperms($location)), -4));
        $visibility  = $permissions & 0044 ? AdapterInterface::VISIBILITY_PUBLIC : AdapterInterface::VISIBILITY_PRIVATE;

        return compact('visibility');
    }
}