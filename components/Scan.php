<?php
namespace infinite\docHelper\components;

use Yii;
use yii\base\Component;
use yii\helpers\FileHelper;

class Scan extends Component
{
    public $module;
    public $project;
    protected $_files;
    protected $_timestamp;
    protected $_fileScans = [];

    public function getNextFileScan()
    {
        if ($this->fileScans) {
            foreach ($this->fileScans as $key => $scan) {
                if (!$scan || $scan->filled) {
                    continue;
                }
                return $key;
            }
        }
        return false;
    }

    public function getFileScans()
    {
        if (!$this->isScanned) {
            return false;
        }
        $scans = [];
        foreach ($this->_fileScans as $key => $scan) {
            if ($scan) {
                $scans[$key] = $scan;
            }
        }
        return $scans;
    }

    public function getTotalBlanks()
    {
        $total = 0;
        if (!$this->fileScans) { return 0; }
        foreach ($this->fileScans as $scan) {
            if (!$scan) { continue; }
            $total += $scan->totalBlanks;
        }
        return $total;
    }


    public function getFilledBlanks()
    {
        $filled = 0;
        if (!$this->fileScans) { return 0; }
        foreach ($this->fileScans as $scan) {
            if (!$scan) { continue; }
            $filled += $scan->filledBlanks;
        }
        return $filled;
    }

    public function getPercentCompleted()
    {
        $total = $this->totalBlanks;
        if (empty($total)) {
            return 0;
        }
        return round(($this->filledBlanks / $total) * 100);
    }

    public function scan($limit = false)
    {
        $files = $this->files;
        $scanned = [];
        $remaining = count($files);
        foreach ($files as $file) {
            if (isset($this->_fileScans[md5($file)])) {
                $remaining--;
                continue;
            }
            if ($limit !== false) {
                if ($limit === 0) { 
                    break; 
                }
                $limit--;
            }
            $fileScan = new FileScan(['file' => $file, 'scan' => $this]);
            $this->_fileScans[md5($file)] = false;
            if ($fileScan->results) {
                $this->_fileScans[md5($file)] = $fileScan;
            }
            $scanned[] = $file;
            $remaining--;
        }
        if (empty($remaining)) {
            $this->_timestamp = microtime(true);
        }
        return ['remaining' => $remaining, 'files' => $scanned];
    }

    public function getIsScanned()
    {
        return !empty($this->_timestamp);
    }

    public function getTimestamp()
    {
        return $this->_timestamp;
    }

    public function getFiles()
    {
        if (!isset($this->_files)) {
            $this->_files = [];
            $options = [];
            $options['recursive'] = true;

            if (!empty($this->project->fileFilterArray)) {
                $options['only'] = $this->project->fileFilterArray;
            }

            foreach ($this->project->directoryArray as $directory) {
                $dirFiles = FileHelper::findFiles($directory, $options);
                if (!empty($dirFiles)) {
                    $this->_files = array_merge($this->_files, $dirFiles);
                }
            }

            $this->_files = array_unique($this->_files);
        }
        return $this->_files;
    }

}
