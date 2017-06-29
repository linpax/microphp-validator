<?php
/**
 * @link https://github.com/linpax/microphp-validator
 * @copyright Copyright &copy; 2017 Linpax
 * @license https://github.com/linpax/microphp-validator/blob/master/LICENSE
 */

namespace Micro\Validator;


abstract class Validator
{
    /**
     * @param array|\stdClass $model
     * @param string $element
     * @param array $options
     * @return bool
     */
    abstract public function validate($model, $element, $options);
    abstract public function clientCode($model, $element, $options);


    protected $errors = [];


    public function getErrors()
    {
        $errors = $this->errors;

        $this->errors = [];

        return $errors;
    }
}