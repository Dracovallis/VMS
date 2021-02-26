<?php

use app\system\Form;

class RegisterForm extends Form
{
    protected $inputs = [
        'username' => [
            'element' => 'input',
            'label' => 'Username',
            'id' => 'username',
            'type' => 'text',
            'attributes' => ['required' => 'required'],
            'required' => true,
            'validations' => [
                'minLengthValidation' => [5]
            ]
        ],
        'email' => [
            'element' => 'input',
            'label' => 'Email',
            'id' => 'email',
            'type' => 'text',
            'required' => true,
            'attributes' => ['required' => 'required', 'type' => 'text'],
            'validations' => [
                'emailValidation',
                'emailTakenValidation'
            ]
        ],
        'password' => [
            'element' => 'input',
            'label' => 'Password',
            'id' => 'password',
            'type' => 'password',
            'attributes' => ['required' => 'required', 'type' => 'text'],
            'required' => true,
            'validations' => [
                'minLengthValidation' => [6],
                'fieldMatch' => ['confirm-password']
            ]
        ],
        'confirm-password' => [
            'element' => 'input',
            'id' => 'confirm-password',
            'label' => 'Confirm password',
            'type' => 'password',
            'attributes' => ['required' => 'required', 'type' => 'text'],
            'required' => true
        ],
    ];

    function fieldMatch($input, $targetInput) {
        if (!empty($input['value']) && !empty($this->inputs[$targetInput]['value']) &&  $input['value'] !== $this->inputs[$targetInput]['value']) {
            $this->inputs[$input['name']]['errors'][] = 'Passwords do not match.';
        }
    }

    function emailTakenValidation($input)
    {
        if (!empty($input['value'])) {
            $userModel = new User($this->_db);

            $existingUsers = $userModel->find([
                'conditions' => 'email = :email',
                'bind'  => [
                    'email' => $input['value']
                ]
            ]);

            if ($existingUsers) {
                $this->inputs[$input['name']]['errors'][] = 'Email already exists.';
            }
        }
    }
}
