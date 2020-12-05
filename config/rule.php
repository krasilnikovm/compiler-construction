<?php
return [
    '<statement>' => [['<if_statement>'], ['<assign_statement>'],],
    '<if_statement>' => [
        ['if', '<condition>', 'then', '<statement>', 'else', '<statement>'],
    ],
    '<condition>' => [
        ['<operand>', '<condition_operation>', '<operand>']
    ],
    '<operand>' => [
        ['<pointer>'],
        ['<function>'],
        ['<variable>'],
    ],
    '<pointer>' => [
        ['<variable>', '^', '.', '<variable>']
    ],
    '<condition_operation>' => [
        ['<'],
        ['>'],
        ['>='],
        ['<='],
        ['='],
        ['<>'],
    ],
    '<function>' => [
        ['<!identifier!>', '(', '<!identifier!>', ')'],
    ],
    '<assign_statement>' => [
        ['<variable>', ':=', '<expression>', ';']
    ],
    '<expression>' => [
        ['<variable>', '<operation>', '<!number!>'],
    ],
    '<operation>' => [
        ['+'],
        ['-'],
        ['*'],
        ['/'],
    ],
    '<variable>' => [
        ['<!identifier!>'],
    ],
];