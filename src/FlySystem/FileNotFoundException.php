<?php
/**
 * Created by PhpStorm.
 * User: liuxiang
 * Date: 9/1/16
 * Time: 12:33 PM
 */

namespace RightCapital\FlySystem\FlySystem;

class FileNotFoundException extends \LogicException
{
    private $path;

    /**
     * FileNotFoundException constructor.
     *
     * @param $path
     */
    public function __construct($path, $code = 0, \Exception $previous = null)
    {
        $this->path = $path;
        parent::__construct('File not found at path: ' . $path, $code, $previous);
    }

    public function getPath()
    {
        return $this->path;
    }
}