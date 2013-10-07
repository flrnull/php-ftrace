<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Utils;

class File {

    /**
     * @var array
     */
    private static $_files;

    /**
     * @param string $fileName
     * @param int $lineNumber
     * @return string
     */
    public static function getStringFromFile ($fileName, $lineNumber) {
        if (self::_fileNotLoaded($fileName))
            self::_loadFile($fileName);

        return self::_readLineFromFile($fileName, $lineNumber);
    }

    /**
     * @param string $fileName
     * @return bool
     */
    private static function _fileNotLoaded ($fileName) {
        return !isset(self::$_files[$fileName]);
    }

    /**
     * @param string $fileName
     * @throws FileException
     */
    private static function _loadFile ($fileName) {
        self::$_files[$fileName] = @file($fileName, FILE_IGNORE_NEW_LINES);
        if (self::$_files[$fileName] === false) {
            unset(self::$_files[$fileName]);
            throw new FileException("Can not load file {$fileName}");
        }
    }

    /**
     * @param string $fileName
     * @param int $lineNumber
     * @throws FileException
     * @return string
     */
    private static function _readLineFromFile ($fileName, $lineNumber) {
        return self::$_files[$fileName][$lineNumber-1];
    }

}

class FileException extends \Exception {}