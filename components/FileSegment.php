<?php
namespace infinite\docHelper\components;

use yii\base\Object;

class FileSegment extends Object
{
    public $file;
    public $startLine;
    public $endLine;
    protected $_content;

    public function __sleep()
    {
        $keys = array_keys((array)$this);
        $bad = ["\0*\0_content"];
        foreach ($keys as $k => $key) {
            if (in_array($key, $bad)) {
                unset($keys[$k]);
            }
        }
        return $keys;
    }

    public function getContent()
    {
        if (!isset($this->_content)) {
            $this->_content = static::getFileSegment($this->file, $this->startLine, $this->endLine);
        }
        return $this->_content;
    }

    public function getContentArray()
    {
        return explode(PHP_EOL, $this->content);
    }

    public static function getFileSegment($file, $start, $end)
    {
        $contents = file_get_contents($file, true);
        if (!empty($contents)) {
            $contents = explode(PHP_EOL, $contents);
            return trim(implode("\n", array_slice($contents, $start, $end-$start)));
        }

        return false;
    }
}
