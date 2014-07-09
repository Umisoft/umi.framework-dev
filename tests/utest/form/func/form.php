<?php

use umi\filter\IFilterFactory;
use umi\form\element\Password;
use umi\form\element\Text;
use umi\form\fieldset\FieldSet;
use umi\validation\IValidatorFactory;

return [
    'name'     => 'register',
    'attributes' => [
        'action'   => '/user/register',
        'method'   => 'post',
    ],

    'elements' => [
        'email'    => [
            'type'       => Text::TYPE_NAME,
            'label'      => 'E-mail',
            'options' => [
                'filters'    => [
                    IFilterFactory::TYPE_STRING_TRIM => []
                ],
                'validators' => [
                    IValidatorFactory::TYPE_REQUIRED => [],
                    IValidatorFactory::TYPE_EMAIL    => []
                ]
            ]
        ],
        'password' => [
            'type'  => Password::TYPE_NAME,
            'label' => 'Пароль'
        ],
        'passport' => [
            'type' => FieldSet::TYPE_NAME,
            'label'    => 'Место жительства',
            'elements' => [
                'number'        => [
                    'type'  => 'text',
                    'label' => 'Номер пасспорта'
                ],
                'birthday_city' => [
                    'type'       => Text::TYPE_NAME,
                    'label'      => 'Город рождения',
                    'attributes' => [
                        'name' => 'city'
                    ]
                ]
            ]
        ],
        'fieldset' => [
            'type' => FieldSet::TYPE_NAME,
            'elements' => [
                'fieldInFieldset' => [
                    'type' => Text::TYPE_NAME
                ]
            ]
        ],
        'submit'   => [
            'type'  => 'submit',
            'label' => 'Зарегистрироваться'
        ]
    ]
];