<?
class sppConfig {
    
    var $vars = Array();
    
    function __construct($config = false) {
        if ($config && is_array($config)) {
            foreach ($config as $key=>$value) {
                $this->setVar($key, $value);
            }
        }
    }
    
    function setVar($name, $value) {
        $this->vars[$name] = $value;
    }
    
    function getVar($name, $default = false) {
        return isset($this->vars[$name]) ? $this->vars[$name] : $default;
    }
    
//    function save($file);
//    function load($file); # ??? really required ???
    
}

?>
