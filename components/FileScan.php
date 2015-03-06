<?php
namespace infinite\docHelper\components;

use yii\base\Component;
use yii\helpers\FileHelper;
use Nette\Reflection\AnnotationsParser;

class FileScan extends Component
{
    public $scan;
    protected $_title;
    public $file;
    public $totalBlanks = 0;
    public $filledBlanks = 0;
    public $filled = false;
    protected $_hash;
    protected $_results;

    public function __wakeup()
    {
        if (!empty($this->_results)) {
            foreach ($this->_results as $result) {
                $result->fileScan = $this;
            }
        }
    }

    public function getResults($refresh = false)
    {
        if ($this->hash !== static::generateHash($this->file)) {
            $refresh = true;
        }

        if ($refresh || !isset($this->_results)) {
            $this->_results = [];
            $fileContent = file_get_contents($this->file);
            $classes = AnnotationsParser::parsePhp($fileContent);

            if (!empty($classes)) {
                $classes = array_keys($classes);
                $this->title = implode('; ', $classes);
                foreach ($classes as $className) {
                    $ref = new \ReflectionClass($className);
                    if (strtolower($ref->getFileName()) != strtolower($this->file)) {
                        echo "boooom";exit;
                        continue;
                    }
                    // Class DocBlock
                    $docComment = $ref->getDocComment();
                    $docCommentSize = count(explode(PHP_EOL, $docComment));
                    $contextStartLine = $ref->getStartLine()-1;
                    $contextEndLine = $ref->getEndLine();
                    if (empty($docComment)) {
                        $docCommentSize = 0;
                        $docComment = false;
                        continue;
                    } else {
                        $docEndLine = $contextStartLine;
                        $docStartLine = $docEndLine - $docCommentSize;
                    }
                    if ($docComment) {
                        $contextFileSegment = $this->getFileSegment($contextStartLine, $contextEndLine);
                        $docSegment = $this->getDocSegment('Class ' . $className, $docStartLine, $docEndLine, $contextFileSegment);
                        if ($docSegment->scanResults) {
                            $this->totalBlanks = $this->totalBlanks + $docSegment->totalBlanks;
                            $this->_results[$docSegment->id] = $docSegment;
                        }
                        $docCommentFull = $docComment;
                    }

                    // Property DocBlocks
                    $fileContentArray = preg_split("/\\r\\n|\\r|\\n/", $fileContent);
                    foreach ($ref->getProperties() as $property) {
                        $startLine = false;
                        // \d(explode("\n", file_get_contents($file)));
                        foreach ($fileContentArray as $line => $content) {
                            if (preg_match(
                                '/
                                    (private|protected|public|var|static|const) # match visibility or var
                                    \s                             # followed 1 whitespace
                                    [\$]?' . $property->getName() . '                          # followed by the var name $bar
                                    [\s|\;]
                                /x',
                                $content)
                            ) {
                                $startLine = $line + 1;
                            }
                        }
                        if (!$startLine) {
                            continue;
                        }

                        $docComment = $property->getDocComment();
                        $docCommentSize = count(explode(PHP_EOL, $docComment));
                        $contextStartLine = $startLine;
                        $contextEndLine = $startLine;
                        if (empty($docComment)) {
                            $docCommentSize = 0;
                            $docComment = false;
                            continue;
                        } else {
                            $docEndLine = $contextStartLine;
                            $docStartLine = $docEndLine - $docCommentSize;
                        }
                        if ($docComment) {
                            $contextFileSegment = $this->getFileSegment($contextStartLine, $contextEndLine);
                            $docSegment = $this->getDocSegment('Property ' . $property->getName(), $docStartLine, $docEndLine, $contextFileSegment);

                            if ($docSegment->scanResults) {
                                $this->totalBlanks = $this->totalBlanks + $docSegment->totalBlanks;
                                $this->_results[$docSegment->id] = $docSegment;
                            }
                        }
                    }

                    // Method DocBlocks
                    $lastGoodComment = null;
                    foreach ($ref->getMethods() as $property) {
                        if (strtolower($property->getFileName()) != strtolower($this->file)) {
                            continue;
                        }
                        $docComment = $property->getDocComment();
                        $docCommentSize = count(explode(PHP_EOL, $docComment));
                        $contextStartLine = $property->getStartLine()-1;
                        $contextEndLine = $property->getEndLine();
                        if (empty($docComment)) {
                            $docCommentSize = 0;
                            $docComment = false;
                            continue;
                        } else {
                            $docEndLine = $contextStartLine;
                            $docStartLine = $docEndLine - $docCommentSize;
                        }
                        if ($docComment) {
                            $contextFileSegment = $this->getFileSegment($contextStartLine, $contextEndLine);
                            $docSegment = $this->getDocSegment('Method ' . $property->getName(), $docStartLine, $docEndLine, $contextFileSegment);

                            if ($docSegment->scanResults) {
                                $this->totalBlanks = $this->totalBlanks + $docSegment->totalBlanks;
                                $this->_results[$docSegment->id] = $docSegment;
                                $lastGoodComment = $docSegment;
                            }
                        }
                    }
                }
            }
            if (empty($this->_results)) {
                $this->_results = false;
            }
        }
        return $this->_results;
    }

    public function getFileSegment($start, $end)
    {
        return new FileSegment(['file' => $this->file, 'startLine' => $start, 'endLine' => $end]);
    }

    public function getDocSegment($title, $start, $end, $context)
    {
        return new DocSegment(['file' => $this->file, 'startLine' => $start, 'endLine' => $end, 'context' => $context, 'title' => $title, 'fileScan' => $this]);
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

    public function getTitle()
    {
        if (!isset($this->_title)) {
            return $this->file;
        }
        return $this->_title;
    }

    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }
}
