<?php

use app\system\Form;

class LoginForm extends Form
{

    protected $inputs = [
        'email' => [
            'element' => 'input',
            'label' => 'Email',
            'id' => 'email',
            'type' => 'text',
            'required' => true,
            'validations' => [
                'emailValidation',
            ]
        ],
        'password' => [
            'element' => 'input',
            'label' => 'Password',
            'id' => 'password',
            'type' => 'password',
            'required' => true
        ]
       
    ];
}
