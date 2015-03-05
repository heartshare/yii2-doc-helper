<?php
namespace infinite\docHelper\components;

use yii\base\Component;
use yii\helpers\FileHelper;

class FileScan extends Component
{
    public $scan;
    public $file;
    public $foundCount = 0;
    public $filled = false;
    protected $_hash;
    protected $_results;

    public function getResults($refresh = false)
    {
        if ($this->hash !== static::generateHash($this->file)) {
            $refresh = true;
        }

        if ($refresh || !isset($this->_results)) {
            $this->_results = [];
            $fileContent = file_get_contents($this->file);
            $fileContent = preg_split("/\\r\\n|\\r|\\n/", $fileContent);
            foreach ($fileContent as $line) {
                $lineKey = md5($this->hash . $line);
                $lineMatches = $matches = [];
                preg_match_all($this->project->templateVariableQuery, $line, $lineMatches, PREG_SET_ORDER);
                if (!empty($lineMatches[0])) {
                    $matches = array_unique($lineMatches[0]);
                }
                if (!empty($matches)) {
                    $this->_results[$lineKey] = $matches;
                    $this->foundCount = $this->foundCount + count($matches);
                }
            }
            if (empty($this->_results)) {
                $this->_results = false;
            }
        }
        return $this->_results;
    }

    public function getProject()
    {
        return $this->scan->project;
    }

    public function getHash()
    {
        if (!isset($this->_hash)) {
            $this->_hash = static::generateHash($this->file);
        }
        return $this->_hash;
    }

    public static function generateHash($file)
    {
        return md5($file . md5_file($file));
    }
}
