<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Utils;

class File {

    /**
     * @var array
     */
    private static $_fileNames;

    /**
     * @var array
     */
    private static $_fileStrings;

    /**
     * @var array
     */
    private static $_filesHandlers;

    /**
     * Not actually opens specified files, but
     * saves it for lazy opening if needed.
     *
     * @param array|string $files
     */
    public function openFiles ($files) {
        self::$_filesHandlers = array();
        self::$_fileStrings = array();
        if (is_string($files)) {
            $files = array($files);
        }
        self::$_fileNames = $files;
    }

    /**
     * Closes currently opened files
     */
    public static function closeFiles() {
        foreach(self::$_filesHandlers as $fileHandler) {
            @fclose($fileHandler);
        }
    }

    /**
     * @param string $fileName
     * @param int $lineNumber
     * @return string
     */
    public static function getStringFromFile ($fileName, $lineNumber) {
        if (self::_fileNotOpen($fileName))
            self::_openFile($fileName);

        return self::_readLineFromFile($fileName, $lineNumber);
    }

    /**
     * @param string $fileName
     * @return bool
     */
    private static function _fileNotOpen ($fileName) {
        return !isset(self::$_filesHandlers[$fileName]);
    }

    /**
     * @param string $fileName
     * @throws FileException
     */
    private static function _openFile ($fileName) {
        self::$_filesHandlers[$fileName] = @fopen($fileName, 'r');
        if (!self::$_filesHandlers) {
            throw new FileException("Can not open file {$fileName}");
        }
    }

    /**
     * @param string $fileName
     * @param int $lineNumber
     * @throws FileException
     * @return string
     */
    private static function _readLineFromFile ($fileName, $lineNumber) {
        if (isset(self::$_fileStrings[$fileName])) {
            if (isset(self::$_fileStrings[$fileName]["l_{$lineNumber}"])) {
                return self::$_fileStrings[$fileName]["l_{$lineNumber}"];
            }
        } else {
            self::$_fileStrings[$fileName] = array();
        }
        $fileHandler = self::$_filesHandlers[$fileName];
        rewind($fileHandler);
        $currentLine = 1;
        $resultString = null;
        while(!feof($fileHandler)) {
            $string = fgets($fileHandler);
            if ($string !== false && $currentLine == $lineNumber) {
                $resultString = $string;
            }
            $currentLine++;
        }
        if (is_null($resultString)) {
            throw new FileException("Could not find line:{$lineNumber} in {$fileName}. {$currentLine} strings parsed.");
        }
        self::$_fileStrings[$fileName]["l_{$lineNumber}"] = $resultString;
        return $resultString;
    }

}

class FileException extends \Exception {}