<?php

/**
 * smartAutoloader
 *
 * Manages class/interface retrieval, caching and inclusion.
 *
 * This class works delicious with the new __autload() function in php5. You can give a few
 * directories to it and it will search in the whole dirs and subdirs for class and
 * interface definitions. Of course this is a matter of ressource killing, but to avoid this
 * it has a smart caching system implemented :o)
 *
 * Here is an example how you could use this class:
 * <code>
 * <?php
 * function __autoload($className) {
 *     static $smartAutoload;
 *     if (!$smartAutoload) {
 *         $smartAutoload = new smartAutoload();
 *         $smartAutoload->addDirectory( array('../classes/', '../lib/') );
 *         $smartAutoload->addFileEnding('.php');
 *     }
 *     $smartAutoload->loadClass($className);
 * }
 * ?>
 * </code>
 */
class smartAutoload {

    /**
     * Class/interface index
     *
     * An associative array (className => classFilename) - in use when scanning and as cache
     * after reading out the cache file.
     *
     * @var array
     */
    private $classIndex = array();

    /**
     * Class/interface directories
     *
     * Holds the directories where this class should scan for classes/interfaces
     *
     * @var array
     */
    private $directories = array();

    /**
     * Class/interface file endings
     *
     * Files with these endings will be in mind while scanning.
     *
     * @var array
     */
    private $fileEndings = array();

    /**
      * Follow Symlinks
      *
      * Should the scanner follow symlinks when searching in directories?
      * Default value is FALSE.
      *
      * @var boolean
      */
    private $followSymlinks = FALSE;

    /**
     * Ignore hidden files
     *
     * Should the scanner ignore hidden files?
     * Default value is TRUE.
     *
     * @access private
     */
    private $ignoreHiddenFiles = TRUE;

    /**
     * Path to the cache file
     *
     * Holds the filename (and path) of the cache file.
     * Default value is 'smartAutoloadCache.php'.
     *
     * @var string
     */
    private $cacheFilename = 'smartAutoloadCache.php';

    /**
     * Regular expression for the file endings
     *
     * This regular expression will be regenerated every time you add a file ending.
     *
     * @see setFileEndingsRegExp()
     * @var string
     */
    private $fileEndingsRegExp = '';

    /**
     * The regular expression for a class/interface definition
     *
     * @var string
     */
    private $classRegExp = "#(interface|class)\s+(\w+)\s+(extends\s+(\w+)\s+)?(implements\s+\w+\s*(,\s*\w+\s*)*)?{#";

    /**
     * Add a directory to the directories array
     *
     * @param mixed $directory Can be a string and an array
     * @return boolean
     */
    public function addDirectory($directory) {
        if ( is_array($directory) ) {
            foreach($directory AS $dir) {
                $this->addDirectory($dir);
            }
        } else {
            if( substr($directory, -1) != '/' ) {
                $directory .= '/';
            }
            if( !in_array($directory, $this->directories) ) {
                $this->directories[] = $directory;
            }
            return TRUE;
        }
    }

    /**
     * Add a file ending
     *
     * Define which file endings will be considered by the scanner.
     * No specifications will let the scanner parse any filetype.
     *
     * @param mixed $fileEnding Can be a string and an array
     * @return void
     */
    public function addFileEnding($fileEnding) {
        if ( is_array($fileEnding) ) {
            $this->fileEndings = array_merge($this->fileEndings, $fileEnding);
        } else {
            $this->fileEndings[] = $fileEnding;
        }
        $this->setFileEndingsRegExp();
        return TRUE;
    }

    /**
     * Sets the fileEndingsRegExp
     *
     * Sets the regular expression for the fileEndings.
     *
     * @see $fileEndingsRegExp
     * @return void
     */
    private function setFileEndingsRegExp() {
        $regExp = '#^.+(';
        $i = 1;
        foreach($this->fileEndings AS $fileEnding) {
            $i > 1 ? $regExp .= '|' : '';
            $regExp .= str_replace('.', '\.', $fileEnding);
            $i++;
        }
        $regExp .= ')$#i';
        $this->fileEndingsRegExp = $regExp;
    }

    /**
     * Set followSymlinks
     *
     * Define whether the scanner should follow symlinks.
     *
     * @param boolean $value
     * @return boolean
     */
    public function setFollowSymlinks($value) {
        $this->followSymlinks = (bool)$value;
        return TRUE;
    }

    /**
     * Set ignoreHiddenFiles
     *
     * Define whether the scanner should ignore hidden files.
     *
     * @param boolean $value
     * @return boolean
     */
    public function setIgnoreHiddenFiles($value) {
        $this->ignoreHiddenFiles = (bool)$value;
        return TRUE;
    }

    /**
     * Set path to cache file
     *
     * Define a path to store the cache file. Make sure we have permissions to read/write on it.
     *
     * @param string $cacheFilename Path to the cache file
     * @return boolean
     */
    public function setCacheFilename($cacheFilename) {
        $this->cacheFilename = $cacheFilename;
        return TRUE;
    }

    /**
     * Load a class/interface
     *
     * Loads a class/interface by its name
     * <ul>
     * <li>if the matching class definition file can't be found in the cache, it will try once again with $retry = TRUE</li>
     * <li>when retrying, the cached index is invalidated, regenerated and re-included</li>
     * </ul>
     *
     * @param string $className The name of the class you want to load
     * @param boolean $retry Used for recursion
     * @return boolean
     */
    public function loadClass($className, $retry = FALSE) {
        // Is our cache outdated or not available? Recreate it!
        if( $retry || !is_readable($this->cacheFilename) ) {
            $this->createCache();
        }
        // Include the cache file
        if ( !$this->readCache() ) {
            return FALSE;
        }
        // Include requested file, return on success
        if( isset($this->classIndex[$className]) && is_readable($this->classIndex[$className]) ) {
            if( include($this->classIndex[$className]) ) {
                return TRUE;
            }
        }
        // On failure retry
        if($retry) {
            return FALSE;
        } else {
            return $this->loadClass($className, TRUE);
        }
    }

    /**
     * Read out the cache file
     *
     * Reads out the cache file if the classIndex has not been set yet.
     *
     * @return boolean
     */
    private function readCache() {
        if ( !empty($this->classIndex) ) {
            return TRUE;
        }
        if( !include($this->cacheFilename) ) {
            return FALSE;
        }
        if ( isset($classes) ) {
            $this->classIndex = $classes;
        }
        return TRUE;
    }

    /**
     * Create cache
     *
     * <ul>
     * <li>scans the class/interface directories for class/interface definitions and creates an associative array (className => classFilename)
     * <li>generates the array in PHP code and saves it as cache file</li>
     * </ul>
     *
     * @return boolean
     */
    private function createCache() {
        foreach($this->directories AS $directory) {
            $this->searchInDirectory($directory);
        }
        $cacheContent = "<?php\n\n";
        foreach($this->classIndex AS $className => $classFilename) {
            $cacheContent .= "    \$classes['" . $className . "'] = '" . $classFilename . "';\n";
        }
        $cacheContent .= "\n?>";
        if( $handle = fopen($this->cacheFilename, "w+") ) {
            fwrite($handle, $cacheContent);
            @chmod($this->cacheFilename, 0664);
            $this->classIndex = array();
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Parse directory
     *
     * Parses a directory for class/interface definitions. Saves found definitions
     * in $classIndex. Needless to say: Mind recursion cycles when using symlinks.
     *
     * @todo Use SPL, if suitable
     * @param string $directory
     * @return boolean
     */
    private function searchInDirectory($directory) {
        if( !is_dir($directory) ) {
            return FALSE;
        }
        if( !$handle = opendir($directory) ) {
            return FALSE;
        }
        while( ($file = readdir($handle)) !== FALSE ) {
            if( $this->ignoreHiddenFiles && $file{0} == '.' ) {
                continue;
            }
            $filePath = $directory . $file;
            switch( filetype($filePath) ) {
                case 'dir':
                    if($file != '.' && $file != '..') {
                        $this->searchInDirectory($filePath . '/');
                    }
                break;
                case 'link':
                    if($this->followSymlinks) {
                        $this->searchInDirectory($filePath . '/');
                    }
                break;
                case 'file':
                    if( count($this->fileEndings) && !preg_match($this->fileEndingsRegExp, $file) ) {
                        continue;
                    }
                    if( !$phpFile = fopen($filePath, "r") ) {
                        continue;
                    }
                    if( !$buf = fread($phpFile, @filesize($filePath) ) ) {
                        continue;
                    }
                    $result = array();
                    if( !preg_match_all($this->classRegExp, $buf, $result) ) {
                        continue;
                    }
                    foreach($result[2] AS $className) {
                        $this->classIndex[$className] = $filePath;
                    }
                break;
            }
        }
        return TRUE;
    }

}

?>