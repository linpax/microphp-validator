<?php
/**
 * @link https://github.com/linpax/microphp-validator
 * @copyright Copyright &copy; 2017 Linpax
 * @license https://github.com/linpax/microphp-validator/blob/master/LICENSE
 */

namespace Micro\Validator;


class Manager
{
    /** @const string BASED_VALIDATOR */
    const BASED_VALIDATOR = '\\Micro\\Validator\\Validator';

    /** @var array $validators Loaded validators */
    private $validators = [];


    /**
     * @param array|\stdClass $model
     * @param array $rules
     * @param bool $isClient
     * @return string|array|true
     * @throws \Exception
     */
    public function run($model, array $rules = [], $isClient = false)
    {
        $clientCode = '';
        $errors = [];

        foreach ($rules as $rule) { // обходим правила
            if (!is_array($rule)) {
                throw new \Exception('Bad rule format');
            }

            $elements = explode(',', str_replace(' ', '', array_shift($rule))); // элементы

            $name = array_shift($rule); // правило
            $options = array_shift($rule); // настройки

            $validator = $this->prepareValidator($name); // валидатор

            if (!$validator) {
                throw new \Exception('Validator not found');
            }

            foreach ($elements as $element) { // обходим поля входящие в правило
                if ($isClient) { // если проверка клиента
                    $clientCode = $validator->clientCode($model, $element, $options);
                } else {
                    if (!$validator->validate($model, $element, $options)) { // если валидация повалилась
                        if (!array_key_exists($name, $errors)) {
                            $errors[$name] = [];
                        }
                        $errors[$name][$element] = $validator->getErrors();
                    }
                }
            }
        }

        return $isClient ? $clientCode : (empty($errors) ? true : $errors);
    }

    /**
     * Add custom validator
     *
     * @param string $name
     * @param string $class
     * @throws \Exception
     */
    public function addCustomValidator($name, $class)
    {
        if (!class_exists($class) || !is_subclass_of($class, self::BASED_VALIDATOR)) {
            throw new \Exception('Class `'.$class.'` of validator `'.$name.'` not found');
        }

        $this->validators[$name] = new $class;
    }

    /**
     * Find and start validator with lazy down
     *
     * @param string $name
     * @return false|Validator
     */
    public function prepareValidator($name)
    {
        if (array_key_exists($name, $this->validators)) {
            return $this->validators[$name];
        }

        $appClass = '\\App\\Validators\\' . ucfirst($name) . 'Validator';
        if (class_exists($appClass) && is_subclass_of($appClass, self::BASED_VALIDATOR)) {
            $this->validators[$name] = new $appClass;

            return $this->validators[$name];
        }

        $systemClass = '\\Micro\\Validator\\Validators\\' . ucfirst($name) . 'Validator';
        if (class_exists($systemClass) && is_subclass_of($systemClass, self::BASED_VALIDATOR)) {
            $this->validators[$name] = new $systemClass;

            return $this->validators[$name];
        }

        return false;
    }
}