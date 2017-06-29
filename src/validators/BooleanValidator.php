<?php
/**
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 */

namespace Micro\Validators;

use Micro\Validator\Validator;


class BooleanValidator extends Validator
{
    /**
     * @inheritdoc
     */
    public function validate($model, $element, $options)
    {
        if (is_object($model) && !property_exists($model, $element)) {
            array_push($this->errors, 'Element `' . $element . '` not found into `' . get_class($model) . '` object');
            return false;
        }
        if (is_array($model) && !array_key_exists($element, $model)) {
            array_push($this->errors, 'Element `' . $element . '` not found into array');
            return false;
        }

        $value = is_object($model) ? $model->$element : $model[$element];

        if (!array_key_exists('true', $options)) {
            $options['true'] = true;
        }
        if (!array_key_exists('false', $options)) {
            $options['false'] = false;
        }

        if (!in_array($value, [ $options['true'], $options['false'] ], false)) {
            array_push($this->errors, 'Element `' . $element . '` validate with `' . get_class($this) . '` error');
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function clientCode($model, $element, $options)
    {
        return 'if (this.value != '.$options['true'].' && this.value != '.$options['false'].') {'.
            ' e.preventDefault(); this.focus(); alert(\'Value not compatible\'); }';
    }
}
