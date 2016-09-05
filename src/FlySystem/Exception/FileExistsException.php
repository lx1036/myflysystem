<?php
/**
 * Created by PhpStorm.
 * User: liuxiang
 * Date: 9/1/16
 * Time: 12:33 PM
 */

namespace RightCapital\FlySystem\Exception;

class FileExistsException extends \LogicException
{
    /**
     * @var string
     */
    private $path;

    /**
     * FileExistsException constructor.
     *
     * @param $path
     */
    public function __construct($path, $code = 0, \Exception $previous = null)
    {
        $this->path = $path;
        parent::__construct('File already exists at path: ' . $path, $code, $previous);
    }

    public function getPath()
    {
        return $this->path;
    }
}