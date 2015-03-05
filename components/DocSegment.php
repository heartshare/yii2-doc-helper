<?php
namespace infinite\docHelper\components;

use yii\base\Object;

class DocSegment extends FileSegment
{
    public $type;
    public $fileScan;
    public $context;
    public $totalBlanks = 0;
    protected $_results;

    public function __sleep()
    {
        $keys = parent::__sleep();
        $bad = ["fileScan"];
        foreach ($keys as $k => $key) {
            if (in_array($key, $bad)) {
                unset($keys[$k]);
            }
        }
        return $keys;
    }

    public function getId()
    {
        return md5(json_encode([$this->file, $this->type, $this->startLine, $this->endLine]));
    }

    public function getScanResults()
    {
        if (!isset($this->_results)) {
            $this->_results = [];
            $lineNumber = $this->startLine;
            foreach ($this->contentArray as $line) {
                $lineKey = md5($lineNumber);
                $lineMatches = $matches = [];
                preg_match_all($this->project->templateVariableQuery, $line, $lineMatches, PREG_SET_ORDER);
                if (!empty($lineMatches[0])) {
                    $matches = array_unique($lineMatches[0]);
                }
                if (!empty($matches)) {
                    $this->_results[$lineKey] = ['line' => $lineNumber, 'matches' => $matches];
                    $this->totalBlanks = $this->totalBlanks + count($matches);
                }
                $lineNumber++;
            }
            if (empty($this->_results)) {
                $this->_results = false;
            }
        }
        return $this->_results;
    }


    public function getScan()
    {
        return $this->fileScan->scan;
    }

    public function getProject()
    {
        return $this->scan->project;
    }
}
