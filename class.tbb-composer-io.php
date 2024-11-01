<?php

use Composer\IO\IOInterface;
use Composer\Config;

class TBBComposerIO implements IOInterface
{
    /**********************IOInterface**********************/
	public $status = array();
	public $verbose = false;
	public $debug = false;
	
    function __construct($verbose = false,$debug = false)
    {
        $this->verbose = $verbose;
		$this->debug = $debug;
    }

    public function isInteractive(){return false;}
    public function isVerbose(){return $this->verbose;}
    public function isDecorated(){return false;}

    public function write($message, $newline = true)
    {
      $count = count($this->status);
      $this->status[$count] = $message;
    }

    public function overwrite($message, $newline = true, $size = 80)
    {
      $count = count($this->status);
      $this->status[$count-1] = $message;
    }


    public function ask($question, $default = null){return $default;}
    public function askConfirmation($question, $default = true){return $default;}
    public function askAndValidate($question, $validator, $attempts = false, $default = null){return $default;}
    public function askAndHideAnswer($question){return null;}
    public function getAuthentications(){return null;}


    public function hasAuthentication($repositoryName)
    {
        return false;
    }

    public function getAuthentication($repositoryName)
    {
      return array();
    }

    public function setAuthentication($repositoryName, $username, $password = null){}
    public function isVeryVerbose(){return $this->verbose;}
    public function isDebug(){return $this->debug;}
	public function loadConfiguration(Config $config){return;}
    
}