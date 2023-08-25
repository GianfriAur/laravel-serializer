<?php

namespace Gianfriaur\Serializer\Tests\Service;

use Gianfriaur\Serializer\Exception\MissingEngineException;
use Gianfriaur\Serializer\Exception\MissingGetTypeStrategyException;
use Gianfriaur\Serializer\Exception\MissingMetadataParameterException;
use Gianfriaur\Serializer\Exception\ObjectMissingMethodException;
use Gianfriaur\Serializer\Exception\ObjectMissingPropertyException;
use Gianfriaur\Serializer\Exception\RecursiveSerializationException;
use Gianfriaur\Serializer\Exception\SerializationIsNotAllowedForTypeException;
use Gianfriaur\Serializer\Exception\SerializationMissingGetStrategyException;
use Gianfriaur\Serializer\Exception\SerializePrimitiveException;
use Gianfriaur\Serializer\Service\Engine\ArrayEngine;
use Gianfriaur\Serializer\Service\Engine\JsonEngine;
use Gianfriaur\Serializer\Service\MetadataService\DefaultMetadataService;
use Gianfriaur\Serializer\Service\Serializer\DefaultSerializer;
use Gianfriaur\Serializer\Tests\Service\ArrayEngineTest\Comment;
use Gianfriaur\Serializer\Tests\Service\ArrayEngineTest\Post;
use Gianfriaur\Serializer\Tests\Service\ArrayEngineTest\TestClass;
use Illuminate\Http\JsonResponse;

class JsonEngineTest extends \Orchestra\Testbench\TestCase
{
    /** @test */
    public function test_basic_test()
    {
        $this->assertTrue(true);
    }

    private function log($var)
    {
        $this->expectOutputString(''); // tell PHPUnit to expect '' as output
        dd($var);
    }

    private function getNewSerializer(array $options): DefaultSerializer
    {
        $serializer = new DefaultSerializer($this->app, $options);
        $array_engine = new ArrayEngine($this->app, $serializer);
        $json_engine = new JsonEngine($this->app, $serializer);
        $serializer->addEngine($array_engine);
        $serializer->addEngine($json_engine);
        $serializer->setDefaultEngine($json_engine);

        $metadata_provider = new DefaultMetadataService();

        $serializer->addMetadataService($metadata_provider);

        $metadata_provider->addGroup(TestClass::class, 'base', [
            'name' => [
                'get' => ['type' => 'direct', 'property' => 'name'],
                'name' => 'name',
            ],
            'surname' => [
                'get' => ['type' => 'function', 'name' => 'getSurname', 'args' => []],
                'name' => 'surname',
            ],
        ]);

        $metadata_provider->addGroup(TestClass::class, 'hi', [
            'hi' => [
                'get' => ['type' => 'function', 'name' => 'seyHi', 'args' => ['Serialization']],
                'name' => 'hi',
            ],
        ]);

        $metadata_provider->addGroup(TestClass::class, 'son', [
            'son' => [
                'get' => ['type' => 'function', 'name' => 'getSon', 'args' => []],
                'name' => 'son',
                'groups' => ['base']
            ],
        ]);

        $metadata_provider->addGroup(TestClass::class, 'sons', [
            'son' => [
                'get' => ['type' => 'function', 'name' => 'getSon', 'args' => []],
                'name' => 'son',
                'groups' => ['base', 'sons']
            ],
        ]);


        $metadata_provider->addGroup(Post::class, 'post_detail', [
            'title' => [
                'get' => ['type' => 'direct', 'property' => 'title'],
                'name' => 'title',
            ],
            'content' => [
                'get' => ['type' => 'direct', 'property' => 'content'],
                'name' => 'content',
            ],
        ]);

        $metadata_provider->addGroup(Post::class, 'post_list', [
            'title' => [
                'get' => ['type' => 'direct', 'property' => 'title'],
                'name' => 'title',
            ]
        ]);

        $metadata_provider->addGroup(Post::class, 'comment_list', [
            'comments' => [
                'get' => ['type' => 'direct', 'property' => 'comments'],
                'name' => 'comments',
                'groups' => ['comment_list']
            ]
        ]);

        $metadata_provider->addGroup(Post::class, 'comment_list_recursive', [
            'comments' => [
                'get' => ['type' => 'direct', 'property' => 'comments'],
                'name' => 'comments',
                'groups' => ['comment_detail_recursive']
            ]
        ]);

        $metadata_provider->addGroup(Comment::class, 'comment_list', [
            'content' => [
                'get' => ['type' => 'direct', 'property' => 'content'],
                'name' => 'content',
            ],
        ]);

        $metadata_provider->addGroup(Comment::class, 'comment_detail', [
            'content' => [
                'get' => ['type' => 'direct', 'property' => 'content'],
                'name' => 'content',
            ],
            'post' => [
                'get' => ['type' => 'direct', 'property' => 'post'],
                'name' => 'post',
                'groups' => ['post_detail']
            ],
        ]);

        $metadata_provider->addGroup(Comment::class, 'comment_detail_recursive', [
            'content' => [
                'get' => ['type' => 'direct', 'property' => 'content'],
                'name' => 'content',
            ],
            'post' => [
                'get' => ['type' => 'direct', 'property' => 'post'],
                'name' => 'post',
                'groups' => ['comment_list_recursive']
            ],
        ]);

        return $serializer;
    }


    public function test_empty_serialization_with_serialize_null_as_null_on()
    {
        $serializer = $this->getNewSerializer(['serialize_null_as_null' => true]);

        $serialized = $serializer->serialize(null, ['test']);

        $this->assertEquals('null', $serialized);
    }

    public function test_empty_serialization_with_serialize_null_as_null_off()
    {
        $serializer = $this->getNewSerializer(['serialize_null_as_null' => false, 'serialize_empty_as_null' => false]);

        $serialized = $serializer->serialize(null, ['test']);

        $this->assertEquals('[]', $serialized);
    }

    public function test_primitive_serializations_with_serialize_primitive_off()
    {

        $serializer = $this->getNewSerializer([]);

        $cases = [true, 1, 1.1, 'test'];

        foreach ($cases as $case) {

            $this->expectException(SerializePrimitiveException::class);
            $this->expectExceptionMessage('It is not possible to serialize primitive types [\'boolean\', \'integer\', \'double\', \'string\'] as the main objects of the serialization. You can set the \'serialize_primitive options\' to true, but it\'s not recommended');
            $serialized = $serializer->serialize($case, ['test']);
        }

    }

    public function test_primitive_serializations_with_serialize_primitive_on()
    {
        $serializer = $this->getNewSerializer(['serialize_primitive' => true]);

        $cases = [true, 1, 1.1, 'test'];

        foreach ($cases as $case) {
            $serialized = $serializer->serialize($case, ['test']);
            $this->assertEquals(json_encode( [$case]), $serialized);
        }
    }

    public function test_missing_get_type_strategy()
    {
        $serializer = $this->getNewSerializer([]);

        $object = new \stdClass();
        $object->name = 'Foo';

        $this->expectException(MissingGetTypeStrategyException::class);
        $this->expectExceptionMessage('The strategy \'none\' isn\'t recognized');

        $serialized = $serializer->getDefaultEngine()->serializeWithMetadata($object,
            [
                'properties' => [
                    'name' => [
                        'get' => ['type' => 'none', 'property' => 'name'],
                        'name' => 'name',
                    ]
                ],
                'metadata_service' => $serializer->getDefaultEngine()::class,
                'groups' => ['base']
            ]
        );
    }

    public function test_direct_missing_metadata_parameter()
    {
        $serializer = $this->getNewSerializer([]);

        $object = new \stdClass();
        $object->name = 'Foo';

        $this->expectException(MissingMetadataParameterException::class);
        $this->expectExceptionMessage('In the metadata the property \'property\' is missing');

        $serialized = $serializer->getDefaultEngine()->serializeWithMetadata($object,
            [
                'properties' => [
                    'name' => [
                        'get' => ['type' => 'direct'],
                        'name' => 'name',
                    ]
                ],
                'metadata_service' => $serializer->getDefaultEngine()::class,
                'groups' => ['base']
            ]
        );
    }

    public function test_direct_object_missing_property()
    {
        $serializer = $this->getNewSerializer([]);

        $object = new \stdClass();
        $object->name = 'Foo';

        $this->expectException(ObjectMissingPropertyException::class);
        $this->expectExceptionMessage('The object stdClass haven\'t the following property: first_name');

        $serialized = $serializer->getDefaultEngine()->serializeWithMetadata($object,
            [
                'properties' => [
                    'name' => [
                        'get' => ['type' => 'direct', 'property' => 'first_name'],
                        'name' => 'name',
                    ]
                ],
                'metadata_service' => $serializer->getDefaultEngine()::class,
                'groups' => ['base']
            ]
        );
    }

    public function test_function_missing_name_metadata_parameter()
    {
        $serializer = $this->getNewSerializer([]);

        $object = new \stdClass();
        $object->name = 'Foo';

        $this->expectException(MissingMetadataParameterException::class);
        $this->expectExceptionMessage('In the metadata the property \'name\' is missing');

        $serialized = $serializer->getDefaultEngine()->serializeWithMetadata($object,
            [
                'properties' => [
                    'name' => [
                        'get' => ['type' => 'function', 'args' => []],
                        'name' => 'name',
                    ]
                ],
                'metadata_service' => $serializer->getDefaultEngine()::class,
                'groups' => ['base']
            ]
        );
    }

    public function test_function_missing_args_metadata_parameter()
    {
        $serializer = $this->getNewSerializer([]);

        $object = new \stdClass();
        $object->getName = fn() => 'Foo';

        $this->expectException(MissingMetadataParameterException::class);
        $this->expectExceptionMessage('In the metadata the property \'args\' is missing');

        $serialized = $serializer->getDefaultEngine()->serializeWithMetadata($object,
            [
                'properties' => [
                    'name' => [
                        'get' => ['type' => 'function', 'name' => 'getName'],
                        'name' => 'name',
                    ]
                ],
                'metadata_service' => $serializer->getDefaultEngine()::class,
                'groups' => ['base']
            ]
        );
    }

    public function test_function_object_missing_method_one()
    {
        $serializer = $this->getNewSerializer([]);

        $object = new \stdClass();
        $object->getName = fn() => 'Foo';

        $this->expectException(ObjectMissingMethodException::class);
        $this->expectExceptionMessage('The object stdClass haven\'t the following method: getSurname');

        $serialized = $serializer->getDefaultEngine()->serializeWithMetadata($object,
            [
                'properties' => [
                    'surname' => [
                        'get' => ['type' => 'function', 'name' => 'getSurname', 'args' => []],
                        'name' => 'surname',
                    ]
                ],
                'metadata_service' => $serializer->getDefaultEngine()::class,
                'groups' => ['base']
            ]
        );
    }

    public function test_function_object_missing_method_two()
    {
        $serializer = $this->getNewSerializer([]);

        $object = new \stdClass();
        $object->getName = 'Foo';

        $this->expectException(ObjectMissingMethodException::class);
        $this->expectExceptionMessage('The object stdClass haven\'t the following method: getName');

        $serialized = $serializer->getDefaultEngine()->serializeWithMetadata($object,
            [
                'properties' => [
                    'surname' => [
                        'get' => ['type' => 'function', 'name' => 'getName', 'args' => []],
                        'name' => 'surname',
                    ]
                ],
                'metadata_service' => $serializer->getDefaultEngine()::class,
                'groups' => ['base']
            ]
        );
    }

    public function test_serialization_missing_get_strategy()
    {
        $serializer = $this->getNewSerializer([]);

        $object = new \stdClass();
        $object->name = 'Foo';

        $this->expectException(SerializationMissingGetStrategyException::class);
        $this->expectExceptionMessage('In the metadata it is not indicated how to access app properties: \'name\'');

        $serialized = $serializer->getDefaultEngine()->serializeWithMetadata($object,
            [
                'properties' => [
                    'name' => [
                        'name' => 'name',
                    ]
                ],
                'metadata_service' => $serializer->getDefaultEngine()::class,
                'groups' => ['base']
            ]
        );
    }

    public function test_serialization_is_not_allowed_for_type()
    {
        $serializer = $this->getNewSerializer([]);

        $object = new \stdClass();
        $object->name = opendir('./');

        $this->expectException(SerializationIsNotAllowedForTypeException::class);
        $this->expectExceptionMessage('Serializing the object the property \'name\' is of type \'resource\' where it is not possible to serialize its content');

        $serialized = $serializer->getDefaultEngine()->serializeWithMetadata($object,
            [
                'properties' => [
                    'name' => [
                        'get' => ['type' => 'direct', 'property' => 'name'],
                    ]
                ],
                'metadata_service' => $serializer->getDefaultEngine()::class,
                'groups' => ['base']
            ]
        );
    }


    public function test_stdClass_serialization()
    {
        $serializer = $this->getNewSerializer([]);

        $object = new \stdClass();
        $object->name = 'Foo';
        $object->getSurname = fn() => 'Bar';
        $object->getAgeDiff = fn($diff) => 32 - $diff;

        // $this->log(($object->{'getSurname'})());

        $serialized = $serializer->getDefaultEngine()->serializeWithMetadata($object,
            [
                'properties' => [
                    'name' => [
                        'get' => ['type' => 'direct', 'property' => 'name'],
                        'name' => 'name',
                    ],
                    'surname' => [
                        'get' => ['type' => 'function', 'name' => 'getSurname', 'args' => []],
                        'name' => 'surname',
                    ],
                    'age_diff' => [
                        'get' => ['type' => 'function', 'name' => 'getAgeDiff', 'args' => [3]],
                        'name' => 'age_diff',
                    ]
                ],
                'metadata_service' => $serializer->getDefaultEngine()::class,
                'groups' => ['base']
            ]
        );

        $this->assertEquals(json_encode([
            "name" => "Foo",
            "surname" => "Bar",
            "age_diff" => 29,
        ]), $serialized);
    }

    public function test_testClass_serialization_base()
    {
        $serializer = $this->getNewSerializer([]);

        $object = new TestClass('Foo', 'Bar');

        $serialized = $serializer->serialize($object, ['base']);

        $this->assertEquals(json_encode([
            "name" => "Foo",
            "surname" => "Bar",
        ]), $serialized);
    }

    public function test_testClass_serialization_base_hi()
    {
        $serializer = $this->getNewSerializer([]);

        $object = new TestClass('Foo', 'Bar');

        $serialized = $serializer->serialize($object, ['base', 'hi']);

        $this->assertEquals(json_encode([
            "name" => "Foo",
            "surname" => "Bar",
            'hi' => 'Hi Serialization'
        ]), $serialized);
    }

    public function test_testClass_serialization_base_hi_son()
    {
        $serializer = $this->getNewSerializer([]);

        $object = new TestClass('Foo', 'Bar', 4);

        $serialized = $serializer->serialize($object, ['base', 'hi', 'son']);

        $this->assertEquals(json_encode([
            "name" => "Foo",
            "surname" => "Bar",
            'hi' => 'Hi Serialization',
            'son' => [
                'name' => '4_Foo',
                'surname' => '4_Foo'
            ]
        ]), $serialized);
    }

    public function test_testClass_serialization_base_hi_sons()
    {
        $serializer = $this->getNewSerializer([]);

        $object = new TestClass('Foo', 'Bar', 4);

        $serialized = $serializer->serialize($object, ['base', 'hi', 'sons']);

        $this->assertEquals(json_encode([
            "name" => "Foo",
            "surname" => "Bar",
            'hi' => 'Hi Serialization',
            'son' => [
                'name' => '4_Foo',
                'surname' => '4_Foo',
                'son' => [
                    'name' => '3_4_Foo',
                    'surname' => '3_4_Foo',
                    'son' => [
                        'name' => '2_3_4_Foo',
                        'surname' => '2_3_4_Foo',
                        'son' => [
                            'name' => '1_2_3_4_Foo',
                            'surname' => '1_2_3_4_Foo'
                        ]
                    ]
                ]
            ]
        ]), $serialized);
    }

    public function test_post_serialization_post_list()
    {
        $serializer = $this->getNewSerializer([]);

        $object = [
            new Post([
                'title' => 'Post 1', 'content' => 'Post 1 comment'
            ]),
            new Post([
                'title' => 'Post 2', 'content' => 'Post 2 comment'
            ])
        ];

        $serialized = $serializer->serialize($object, ['post_list']);

        $this->assertEquals(json_encode([
            ["title" => "Post 1"],
            ["title" => "Post 2"]
        ]), $serialized);
    }

    public function test_post_serialization_post_detail()
    {
        $serializer = $this->getNewSerializer([]);

        $object = new Post([
            'title' => 'Post 1', 'content' => 'Post 1 comment'
        ]);

        $serialized = $serializer->serialize($object, ['post_detail']);

        $this->assertEquals(json_encode([
            "title" => "Post 1",
            'content' => 'Post 1 comment'
        ]), $serialized);
    }

    public function test_comment_serialization_comment_list()
    {
        $serializer = $this->getNewSerializer([]);

        $object = [
            new Comment(['content' => 'Comment 1']),
            new Comment(['content' => 'Comment 2'])
        ];

        $serialized = $serializer->serialize($object, ['comment_list']);

        $this->assertEquals(json_encode([
            ["content" => "Comment 1"],
            ["content" => "Comment 2"]
        ]), $serialized);
    }

    public function test_comment_serialization_comment_detail()
    {
        $serializer = $this->getNewSerializer([]);

        $object = new Comment(['content' => 'Comment 1']);
        $object->post()->associate(
            new Post([
                'title' => 'Post 1', 'content' => 'Post 1 comment'
            ])
        );

        $serialized = $serializer->serialize($object, ['comment_detail']);

        $this->assertEquals(json_encode([
            "content" => "Comment 1",
            "post" => [
                "title" => "Post 1",
                'content' => 'Post 1 comment'
            ]
        ]), $serialized);
    }

    public function test_post_no_comments_serialization_post_detail_comment_list()
    {
        $serializer = $this->getNewSerializer([]);

        $object = new Post([
            'title' => 'Post 1', 'content' => 'Post 1 comment'
        ]);

        $serialized = $serializer->serialize($object, ['post_detail', 'comment_list']);

        $this->assertEquals(json_encode([
            "title" => "Post 1",
            'content' => 'Post 1 comment'
        ]), $serialized);
    }

    public function test_post_with_comments_serialization_post_detail_comment_list()
    {
        $serializer = $this->getNewSerializer([]);

        $object = new Post([
            'title' => 'Post 1', 'content' => 'Post 1 comment'
        ]);


        $comment1 = new Comment(['content' => 'Comment 1']);
        $comment2 = new Comment(['content' => 'Comment 2']);

        $object->comments->add($comment1);
        $object->comments->add($comment2);

        $serialized = $serializer->serialize($object, ['post_detail', 'comment_list']);

        $this->assertEquals(json_encode([
            "title" => "Post 1",
            'content' => 'Post 1 comment',
            'comments' => [
                [ 'content' => 'Comment 1'],
                ['content' => 'Comment 2']
            ]
        ]), $serialized);
    }

    public function test_post_with_comments_serialization_post_detail_comment_list_recursive_with_exception()
    {
        $serializer = $this->getNewSerializer([]);

        $object = new Post([
            'title' => 'Post 1', 'content' => 'Post 1 comment'
        ]);


        $comment1 = new Comment(['content' => 'Comment 1']);
        $comment2 = new Comment(['content' => 'Comment 2']);

        $object->comments->add($comment1);
        $object->comments->add($comment2);
        $comment2->post()->associate($object);
        $comment1->post()->associate($object);

        $this->expectException(RecursiveSerializationException::class);
        $this->expectExceptionMessage('A recursive serialization has been found, you should review the serialization groups to avoid this problem, but if it is essential you can still activate the \'prevent_recursive_serialization\' setting, this will prevent a child from serializing the parent, this setting is not recommended');

        $serialized = $serializer->serialize($object, ['post_detail', 'comment_list_recursive']);

    }

    public function test_post_with_comments_serialization_post_detail_comment_list_recursive_ok()
    {
        $serializer = $this->getNewSerializer(['prevent_recursive_serialization'=>true]);

        $object = new Post([
            'title' => 'Post 1', 'content' => 'Post 1 comment'
        ]);

        $comment1 = new Comment(['content' => 'Comment 1']);
        $comment2 = new Comment(['content' => 'Comment 2']);

        $object->comments->add($comment1);
        $object->comments->add($comment2);
        $comment2->post()->associate($object);
        $comment1->post()->associate($object);

        $serialized = $serializer->serialize($object, ['post_detail', 'comment_list_recursive']);

        $this->assertEquals(json_encode([
            "title" => "Post 1",
            'content' => 'Post 1 comment',
            'comments' => [
                [ 'content' => 'Comment 1'],
                ['content' => 'Comment 2']
            ]
        ]), $serialized);
    }

}
