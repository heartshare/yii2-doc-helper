<?php
namespace infinite\docHelper\components;

use yii\base\Object;

class DocSegment extends FileSegment
{
    public $title;
    public $fileScan;
    public $context;
    public $totalBlanks = 0;
    public $filledBlanks = 0;
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
        return md5(json_encode([$this->file, $this->title, $this->startLine, $this->endLine]));
    }
    
    public function resolveMatch($lineNumber, $lineId, $matchId, $resolution)
    {
        if (isset($this->scanResults[$lineId]) && isset($this->scanResults[$lineId][$matchId])) {
            $line = $this->scanResults[$lineId];
            $match = $this->scanResults[$lineId][$matchId];
            $currentLine = $this->getLine($lineNumber);
            $newLine = strtr($currentLine, [$match => $resolution]);
            if (!$this->replaceLine($lineNumber, $newLine)) {
                return false;
            }
            unset($this->_results[$lineId][$matchId]);
            if (empty($this->_results[$lineId])) {
                unset($this->_results[$lineId]);
            }
        }
        return $this->markResolved();
    }

    public function markResolved()
    {
        $this->filledBlanks++;
        $this->fileScan->filledBlanks++;
        return true;
    }
    public function getScanResults()
    {
        if (!isset($this->_results)) {
            $this->_results = [];
            $lineNumber = $this->startLine;
            foreach ($this->contentArray as $line) {
                $lineKey = md5($lineNumber);
                $lineMatches = $matches = [];
                preg_match_all($this->project->templateVariableQuery, $line, $lineMatches); //, PREG_SET_ORDER
                if (!empty($lineMatches[0])) {
                    $matches = array_unique($lineMatches[0]);
                }
                if (!empty($matches)) {
                    //\d($lineMatches);
                    $this->_results[$lineKey] = [];
                    $id = 0;
                    foreach ($matches as $match) {
                        $idKey = md5($id.$match);
                        $this->_results[$lineKey][$idKey] = $match;
                        $id++;
                    }
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
