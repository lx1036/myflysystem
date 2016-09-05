<?php

namespace RightCapital\FlySystem\FlySystem;

use finfo;

class MimeType
{
    /**
     * Detects MIME Type based on given content.
     *
     * @param mixed $content
     *
     * @return string|null MIME Type or NULL if no mime type detected
     */
    public static function detectByFilename($content)
    {
        if ( ! class_exists('Finfo') || ! is_string($content)) {
            return;
        }

        $finfo = new Finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($content);

        return $mimeType ?: null;
    }
}