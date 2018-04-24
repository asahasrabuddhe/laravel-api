<?php

namespace Asahasrabuddhe\LaravelAPI\Helpers;

use ReflectionClass;

class ReflectionHelper
{
    protected $className;

    protected $classObject;

    protected $reflectedObject;

    protected $methodBody;

    protected $fields;

    public function __construct($className)
    {
        $this->className = $className;

        // Initialize an object of received resoruce class
        $this->classObject = new $this->className(null);
        //Mirror, mirror on the wall ;)
        $this->reflectedObject = new ReflectionClass($this->classObject);
        $this->getMethodBody();
        $this->extractFields();
    }

    private function getMethodBody()
    {
        // read the file containing the definition of the api resource class
        $source = file($this->reflectedObject->getMethod('toArray')->getFileName());
        // line number where the toArray method starts
        $start_line = $this->reflectedObject->getMethod('toArray')->getStartLine() - 1;
        // line number where the toArray method ends
        $end_line = $this->reflectedObject->getMethod('toArray')->getEndLine();
        // the actual function body of the two array methods
        $this->methodBody = implode('', array_slice($source, $start_line, $end_line - $start_line));
    }

    private function extractFields()
    {
        // tokenize the function body and scan tokens for information useful to us
        foreach (token_get_all('<?php' .  $this->methodBody . '?>') as $token) {
            // Look for parser token type T_STRING (319)
            if (isset($token[0]) && $token[0] == 319) {
                // ignore parser token representing name of the function
                if ($token[1] == 'toArray' || $token[1] == 'parent') {
                    continue;
                }
                $this->fields[] = $token[1]; // save the field name returned in fields array
            }
        }
    }

    public function getFields()
    {
        return $this->fields;
    }
}
