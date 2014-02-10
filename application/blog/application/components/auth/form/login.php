<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

use umi\filter\IFilterFactory;
use umi\form\element\Password;
use umi\form\element\Text;
use umi\validation\IValidatorFactory;

return [
    'name'       => 'contact',
    'method'     => 'post',
    'elements'   => [
        'email'    => [
            'type'       => Text::TYPE_NAME,
            'label'      => 'E-mail',
            'attributes' => [
                'id'    => 'login_email'
            ],
            'filters'    => [
                IFilterFactory::TYPE_STRING_TRIM => []
            ],
            'validators' => [
                IValidatorFactory::TYPE_REQUIRED => []
            ]
        ],
        'password' => [
            'type'       => Password::TYPE_NAME,
            'label'      => 'Password',
            'attributes' => [
                'id'    => 'login_password'
            ],
            'validators' => [
                IValidatorFactory::TYPE_REQUIRED => []
            ]
        ],
    ]
];