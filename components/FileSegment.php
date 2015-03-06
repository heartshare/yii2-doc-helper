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

    public function getLine($lineNumber)
    {
        if ($lineNumber < $this->startLine || $lineNumber > $this->endLine) {
            return false;
        }
        return static::getFileSegment($this->file, $lineNumber, $lineNumber+1);
    }

    public function replaceLine($lineNumber, $newLine)
    {
        $contents = preg_split("/\\r\\n|\\r|\\n/", file_get_contents($this->file, true));
        $contents[$lineNumber] = $newLine;
        return file_put_contents($this->file, implode("\n", $contents)) !== false;
    }

    public function getContentArray()
    {
        return preg_split("/\\r\\n|\\r|\\n/", $this->content);
    }

    public static function getFileSegment($file, $start, $end)
    {
        $contents = file_get_contents($file, true);
        if (!empty($contents)) {
            $contents = preg_split("/\\r\\n|\\r|\\n/", $contents);
            return implode("\n", array_slice($contents, $start, $end-$start));
        }

        return false;
    }
}
