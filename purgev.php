<?php

class PurgeV {

    private $url;
    private $domain;

    private $verbose;
    private $internalUri;
    private $externalUri;

    public function __construct( $argv, $argc ) {

        $this->url = false;
        $this->domain = false;
        $this->internalUri = [];
        $this->externalUri = [];

        $this->filterParameter( $argv );
        if ($this->url) {
            $cnt = $this->requestRemoteContent();
            $this->filterUrl($cnt);
        }

        foreach ($this->internalUri as $path) {
            $this->executeCurlPurge( $path );
        }
    }

    public function handleHref( $pathValue ) {

        if ($this->discardHref( $pathValue ))
            return;

        if ((strpos($pathValue, "http:") === 0) 
            && (strpos($pathValue, $this->domain) === false)) {

            // oubounds - not needed ftm 
            array_push($this->externalUri, $pathValue);
            
        } else if ((strpos($pathValue, "http:") === false) 
            && (strpos($pathValue, " ") === false)) {

            array_push($this->internalUri, $pathValue);
        }
    }

    public function discardHref( $pathValue ) {

        $exclusion = ['javascript:', ' '];
        $strict = ['/', ''];

        for ($i=0;$i<sizeof($exclusion);$i++) {
            if (strpos($pathValue, $exclusion[$i]) !== false) {
                return true;
            }
        }

        if (array_search($pathValue, $strict) !== false) {
            return true;
        }

        return false;
    }

    public function requestRemoteContent() {

        $cnt = file_get_contents( $this->url );
        return $cnt;
    }

    public function filterUrl( $cnt ) {

        $data = str_replace("<a href=\"", '\0', $cnt);
        $data = str_replace("\">", '\0', $data);
        $data = str_replace("\" >", '\0', $data);
        $htmlArray = explode('\0', $data);
        unset($htmlArray[count($htmlArray)-1]);

        $trim = str_replace("http://", "",  $this->url);
        $url_arr = explode("/", $trim);
        $this->domain = $url_arr[0];

        for ($i=0; $i<sizeof($htmlArray); $i++) {
            $this->handleHref( $htmlArray[$i] );        
        }
    }

    public function filterParameter( $argv ) {

        $index = array_search('-h', $argv);
        if ($index && isset($argv[$index+1]))
            $this->url = $argv[++$index];
        
    }

    public function executeCurlPurge( $path ) {

        $out = shell_exec('curl -sX PURGE http://'.$this->domain.$path);
        sleep(0.1);
    }
}


$p = new PurgeV( $argv, $argc );
