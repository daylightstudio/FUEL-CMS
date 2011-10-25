<?php
/**
 * This file contains the base class for utilizing an instance of the
 * W3 HTML Validator.
 * 
 * PHP versions 5
 * 
 * @category Services
 * @package  Services_W3C_HTMLValidator
 * @author   Brett Bieber <brett.bieber@gmail.com>
 * @license  http://www.opensource.org/licenses/bsd-license.php BSD
 * @version  CVS: $id$
 * @link     http://pear.php.net/package/Services_W3C_HTMLValidator
 * @since    File available since Release 0.2.0
 */

/**
 * require the HTTP_Request2 class for sending requests to the validator.
 */
require_once 'HTTP/Request2.php';

/**
 * Uses response object.
 */
require_once 'Services/W3C/HTMLValidator/Response.php';

require_once 'Services/W3C/HTMLValidator/Error.php';

require_once 'Services/W3C/HTMLValidator/Warning.php';

/**
 * A simple class for utilizing the W3C HTML Validator service.
 * 
 * @category Services
 * @package  Services_W3C_HTMLValidator
 * @author   Brett Bieber <brett.bieber@gmail.com>
 * @license  http://www.opensource.org/licenses/bsd-license.php BSD
 * @link     http://pear.php.net/package/Services_W3C_HTMLValidator
 */
class Services_W3C_HTMLValidator
{
    /**
     * URI to the w3 validator.
     * 
     * @var string
     */
    public $validator_uri = 'http://validator.w3.org/check';
    
    /**
     * The URL of the document to validate
     * @var string
     */
    public $uri;
    
    /**
     * Internally used filename of a file to upload to the validator
     * POSTed as multipart/form-data
     * @var string
     */
    public $uploaded_file;
    
    /**
     * HTML fragment to validate.
     *  
     * Full documents only. At the moment, will only work if data is sent with the
     * UTF-8 encoding.
     * @var string
     */
    public $fragment;
    
    /**
     * Output format
     * 
     * Triggers the various outputs formats of the validator. If unset, the usual 
     * Web format will be sent. If set to soap12, the SOAP1.2 interface will be 
     * triggered. See below for the SOAP 1.2 response format description.
     */
    public $output = 'soap12';
    
    /**
     * Character encoding
     * 
     * Character encoding override: Specify the character encoding to use when 
     * parsing the document. When used with the auxiliary parameter fbc set to 1, 
     * the given encoding will only be used as a fallback value, in case the charset
     * is absent or unrecognized. Note that this parameter is ignored if validating
     * a fragment with the direct input interface.
     * @var string
     */
    public $charset;
    
    /**
     * Fall Back Character Set
     * 
     * When no character encoding is detected in the document, use the value in 
     * $charset as the fallback character set.
     *
     * @var bool
     */
    public $fbc;
    
    /**
     * Document type
     * 
     * Document Type override: Specify the Document Type (DOCTYPE) to use when 
     * parsing the document. When used with the auxiliary parameter fbd set to 1, 
     * the given document type will only be used as a fallback value, in case the
     * document's DOCTYPE declaration is missing or unrecognized.
     * @var string
     */
    public $doctype;
    
    /**
     * Fall back doctype
     * 
     * When set to 1, use the value stored in $doctype when the document type
     * cannot be automatically determined.
     * 
     * @var bool
     */
    public $fbd;
    
    /**
     * Verbose output
     * 
     * In the web interface, when set to 1, will make error messages, explanations 
     * and other diagnostics more verbose.
     * In SOAP output, does not have any impact.
     * @var bool
     */
    public $verbose = false;
    
    /**
     * Show source
     * 
     * In the web interface, triggers the display of the source after the validation
     * results. In SOAP output, does not have any impact.
     * @var bool
     */
    public $ss = false;
    
    /**
     * outline
     * 
     * In the web interface, when set to 1, triggers the display of the document 
     * outline after the validation results. In SOAP output, does not have any
     * impact.
     * @var bool
     */
    public $outline = false;
    
    /**
     * HTTP_Request2 object.
     * @var object
     */
    protected $request;
    
    /**
     * Constructor for the class.
     * 
     * @param array $options An array of options
     */
    function __construct($options = array())
    {
        $this->setOptions($options);
        
        $this->setRequest(new HTTP_Request2());
    }
    
    /**
     * Sets options for the class.
     * Pass an associative array of options for the class.
     *
     * @param array $options array of options
     * 
     * @return void
     */
    function setOptions($options)
    {
        foreach ($options as $option=>$val) {
            $this->$option = $val;
        }
    }
    
    /**
     * Sets the HTTP request object to use.
     *
     * @param HTTP_Request2 $request The request object.
     * 
     * @return Services_W3C_HTMLValidator
     */
    function setRequest(HTTP_Request2 $request)
    {
        $this->request = $request;
        
        return $this;
    }
    
    /**
     * Validates a given URI
     * 
     * Executes the validator using the current parameters and returns a Response 
     * object on success.
     *
     * @param string $uri The address to the page to validate ex: http://example.com/
     * 
     * @return mixed object Services_W3C_HTMLValidator | bool false
     */
    function validate($uri)
    {
        $this->uri = $uri;
        $this->buildRequest('uri');
        if ($response = $this->sendRequest()) {
            return $this->parseSOAP12Response($response->getBody());
        } else {
            return false;
        }
    }
    
    /**
     * Validates the local file
     * 
     * Requests validation on the local file, from an instance of the W3C validator.
     * The file is posted to the W3 validator using multipart/form-data.
     * 
     * @param string $file file to be validated.
     * 
     * @return mixed object Services_W3C_HTMLValidator | bool fals
     */
    function validateFile($file)
    {
        if (file_exists($file)) {
            $this->uploaded_file = $file;
            $this->buildRequest('file');
            if ($response = $this->sendRequest()) {
                return $this->parseSOAP12Response($response->getBody());
            } else {
                return false;
            }
        } else {
            // No such file!
            return false;
        }
    }
    
    /**
     * Validate an html string
     * 
     * @param string $html full html document fragment
     * 
     * @return mixed object Services_W3C_HTMLValidator | bool fals
     */
    function validateFragment($html)
    {
        $this->fragment = $html;
        $this->buildRequest('fragment');
        if ($response = $this->sendRequest()) {
            return $this->parseSOAP12Response($response->getBody());
        } else {
            return false;
        }
    }
    
    /**
     * Prepares a request object to send to the validator.
     *
     * @param string $type uri, file, or fragment
     * 
     * @return void
     */
    protected function buildRequest($type = 'uri')
    {
        $this->request->setURL($this->validator_uri);
        switch ($type) {
        case 'uri':
        default:
            $this->request->setMethod(HTTP_Request2::METHOD_GET);
            $this->setQueryVariable('uri', $this->uri);
            $method = 'setQueryVariable';
            break;
        case 'file':
            $this->request->setMethod(HTTP_Request2::METHOD_POST);
            $this->request->addUpload('uploaded_file',
                                     $this->uploaded_file,
                                     null,
                                     'text/html');
            $method = 'addPostParameter';
            break;
        case 'fragment':
            $this->request->setMethod(HTTP_Request2::METHOD_POST);
            $this->addPostParameter('fragment', $this->fragment);
            $method = 'addPostParameter';
            break;
        }
        
        foreach (array( 'charset',
                        'fbc',
                        'doctype',
                        'fbd',
                        'verbose',
                        'ss',
                        'outline',
                        'output') as $option) {
            if (isset($this->$option)) {
                if (is_bool($this->$option)) {
                    $this->$method($option, intval($this->$option));
                } else {
                    $this->$method($option, $this->$option);
                }
            }
        }
    }
    
    /**
     * Set a querystring variable for the request
     * 
     * @param string $name  Name of the querystring parameter
     * @param mixed  $value Value of the parameter
     * 
     * @return void
     */
    protected function setQueryVariable($name, $value = '')
    {
        $url = $this->request->getURL();
        $url->setQueryVariable($name, $value);
        $this->request->setURL($url);
    }
    
    /**
     * Add post data to the request
     * 
     * @param string $name  Name of the post field
     * @param mixed  $value Value of the field
     * 
     * @return void
     */
    protected function addPostParameter($name, $value = '')
    {
        $this->request->addPostParameter($name, $value);
    }
    
    /**
     * Actually sends the request to the HTML Validator service
     *
     * @throws Services_W3C_HTMLValidator_Exception
     * 
     * @return bool true | false
     */
    protected function sendRequest()
    {
        try {
            return $this->request->send();
        } catch (Exception $e) {
            include_once 'Services/W3C/HTMLValidator/Exception.php';
            if (version_compare(phpversion(), '5.3.0', '>')) {
                throw new Services_W3C_HTMLValidator_Exception('Error sending request to the validator', $e);
            }
            // Rethrow
            throw $e;
        }
    }
    
    /**
     * Parse an XML response from the validator
     * 
     * This function parses a SOAP 1.2 response xml string from the validator.
     *
     * @param string $xml The raw soap12 XML response from the validator.
     * 
     * @return mixed object Services_W3C_HTMLValidator_Response | bool false
     */
    static function parseSOAP12Response($xml)
    {
        $doc = new DOMDocument();
        if ($doc->loadXML($xml)) {
            $response = new Services_W3C_HTMLValidator_Response();
            
            // Get the standard CDATA elements
            foreach (array('uri','checkedby','doctype','charset') as $var) {
                $element = $doc->getElementsByTagName($var);
                if ($element->length) {
                    $response->$var = $element->item(0)->nodeValue;
                }
            }
            // Handle the bool element validity
            $element = $doc->getElementsByTagName('validity');
            if ($element->length &&
                $element->item(0)->nodeValue == 'true') {
                $response->validity = true;
            } else {
                $response->validity = false;
            }
            if (!$response->validity) {
                $errors = $doc->getElementsByTagName('error');
                foreach ($errors as $error) {
                    $response->errors[] =
                        new Services_W3C_HTMLValidator_Error($error);
                }
            }
            $warnings = $doc->getElementsByTagName('warning');
            foreach ($warnings as $warning) {
                $response->warnings[] =
                    new Services_W3C_HTMLValidator_Warning($warning);
            }
            return $response;
        } else {
            // Could not load the XML.
            return false;
        }
    }
}
?>