<?php
/**
 * @package ExternalData
 */

/**
 * A generic class to handle the retrieval of external data
 * @package ExternalData
 */
abstract class DataRetriever {

    protected $DEFAULT_PARSER_CLASS=null; 
    protected $authority;
    protected $dataController;
    protected $supportsSearch = false;

    abstract public function getCacheKey();
    abstract public function retrieveData();
    
    /* allows the retriever to override the cache folder */
    public function cacheFolder($baseCacheFolder) {
        return $baseCacheFolder;
    }

    public function setAction($action, $actionArgs) {
    }

    public function setDataController(ExternalDataController $dataController) {
        $this->dataController = $dataController;
    }
    
    public function getDataController() {
        return $this->dataController;
    }
    
    public function getAuthority() {
        return $this->authority;
    }
    
    public function supportsSearch() {
        return $this->supportsSearch;
    }
    
    public function getUser() {
        if ($this->authority) {
            return $this->authority->getCurrentUser();
        } else {
            $session = Kurogo::getSession();
            return $session->getUser();
        }
    }
    
    protected function setAuthority(AuthenticationAuthority $authority) {
        $this->authority = $authority;
    }
    
    protected function init($args) {

        if (isset($args['AUTHORITY'])) {
            if ($authority = AuthenticationAuthority::getAuthenticationAuthority($args['AUTHORITY'])) {
                $this->setAuthority($authority);
            }
        }
    }
    
    public function getDefaultParserClass() {
        return $this->DEFAULT_PARSER_CLASS;
    }
    
    public static function factory($retrieverClass, $args) {
        Kurogo::log(LOG_DEBUG, "Initializing DataRetriever $retrieverClass", "data");
        if (!class_exists($retrieverClass)) {
            throw new KurogoConfigurationException("Retriever class $retrieverClass not defined");
        }
        
        $retriever = new $retrieverClass;
        
        if (!$retriever instanceOf DataRetriever) {
            throw new KurogoConfigurationException("$retriever is not a subclass of DataRetriever");
        }
        
        $retriever->init($args);
        return $retriever;
    }
}
