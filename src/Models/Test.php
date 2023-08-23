<?php

namespace Gianfriaur\Serializer\Models;

use  Gianfriaur\Serializer\Attribute as LS;

#[LS\Group('detail', [
    'name',
    'surname',
    'posts' => 'post_list',
    'private_read' => new LS\GetParameter('getPrivateRead', args: [1,'text']),
    'private' => [
        new LS\GetParameter('getPrivate'),
        new LS\SetParameter('setPrivate')
    ],
])]
#[LS\SetParameter('setPermissions','permissions',['register', 'update'])]
class Test
{

}