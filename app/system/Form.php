<?php

namespace app\system;

class Form
{
    const ELEMENT_TYPE_INPUT = 'input';
    const ELEMENT_TYPES = [self::ELEMENT_TYPE_INPUT];

    protected $inputs = [];
    protected $_db;

    public function __construct($db = null)
    {
        $this->_db = $db;
        foreach ($this->inputs as $name => &$input) {
            $input['name'] = $name;

            $submit = array_merge($_POST, $_GET);

            if (!empty($submit[$name])) {
                $input['value'] = trim($submit[$name]);
            }
        }
    }

    function render()
    {
        foreach ($this->inputs as $inputName => $input) {
            if (in_array($input['element'], self::ELEMENT_TYPES)) {
                switch ($input['element']) {
                    case self::ELEMENT_TYPE_INPUT: {
                            require("views/shared/templates/forms/input-render.phtml");
                            break;
                        }
                }
            }
        }
    }

    function validate()
    {
        foreach ($this->inputs as $inputName => $input) {
            if (!empty($input['validations'])) {
                foreach ($input['validations'] as $key => $value) {
                    if (is_array($value)) {
                        $this->$key($input, ...$value);
                    } else {
                        $this-> $value($input);
                    }
                }
            }

            if (!empty($input['required'])) {
                if (empty($input['value'])) {
                    $this->inputs[$input['name']]['errors'][] = 'Required field.';
                }
            }
        }
    }

    function isValid()
    {
        $errorCount = 0;
        foreach ($this->inputs as $input) {
            if (!empty($input['errors'])) {
                $errorCount += count($input['errors']);
            }
        }

        return empty($errorCount);
    }

    function emailValidation($input)
    {
        if (empty($input['value']) || !filter_var($input['value'], FILTER_VALIDATE_EMAIL)) {
            $this->inputs[$input['name']]['errors'][] = 'Invalid email.';
        }
    }

    function minLengthValidation($input, $length)
    {
        if (empty($input['value']) || strlen($input['value']) < $length) {
            $this->inputs[$input['name']]['errors'][] = "At least $length characters.";
        }
    }
}
