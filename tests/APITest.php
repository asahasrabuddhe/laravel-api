<?php

namespace Asahasrabuddhe\LaravelAPI\Tests;

class APITest extends TestCase
{
    /** @test */
    public function test_get_all_users()
    {
        $response = $this->call('GET', '/t_api/t1/users');
        $this->assertEquals($response->status(), 200);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'id',
                ],
            ],
            'meta' => [
                'paging' => [
                    'links' => [
                        'next',
                    ],
                    'total',
                ],
                'time',
            ],
        ]);
    }

    /** @test */
    public function test_get_all_users_with_fields()
    {
        $response = $this->call('GET', '/t_api/t1/users?fields=name,email');
        $this->assertEquals($response->status(), 200);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'email',
                    'id',
                ],
            ],
            'meta' => [
                'paging' => [
                    'links' => [
                        'next',
                    ],
                    'total',
                ],
                'time',
            ],
        ]);
    }

    /** @test */
    public function test_get_all_users_with_related_fields_one_to_one()
    {
        $response        = $this->call('GET', '/t_api/t1/users?fields=name,email,address');
        $responseContent = json_decode($response->getContent());

        $this->assertEquals($response->status(), 200);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'email',
                    'address',
                    'id',
                ],
            ],
            'meta' => [
                'paging' => [
                    'links' => [
                        'next',
                    ],
                    'total',
                ],
                'time',
            ],
        ]);
        $this->assertFalse(is_array($responseContent->data[0]->address));
    }

    /** @test */
    public function test_get_all_users_with_related_fields_one_to_many()
    {
        $response        = $this->call('GET', '/t_api/t1/users?fields=name,email,posts');
        $responseContent = json_decode($response->getContent());

        $this->assertEquals($response->status(), 200);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'email',
                    'id',
                    'posts' => [
                        '*' => [
                            'title',
                            'content',
                            'id',
                        ],
                    ],
                ],
            ],
            'meta' => [
                'paging' => [
                    'links' => [
                        'next',
                    ],
                    'total',
                ],
                'time',
            ],
        ]);
        $this->assertTrue(is_array($responseContent->data[0]->posts));
    }

    /** @test */
    public function test_get_all_users_with_second_level_relationships()
    {
        $response        = $this->call('GET', '/t_api/t1/users?fields=name,address,posts{title,content},posts:comments{content},posts:comments:author{name}');
        $responseContent = json_decode($response->getContent());

        $this->assertEquals($response->status(), 200);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'id',
                    'posts' => [
                        '*' => [
                           'title',
                           'content',
                           'id',
                           'comments' => [
                            '*' => [
                                    'content',
                                    'user_id',
                                    'id',
                                    'author' => [
                                        'name',
                                        'id',
                                    ],
                               ],
                            ],
                        ],
                    ],
                ],
            ],
            'meta' => [
                'paging' => [
                    'links' => [
                        'next',
                    ],
                    'total',
                ],
                'time',
            ],
        ]);
    }

    /** @test */
    public function test_get_all_users_with_filters()
    {
        $response        = $this->call('GET', '/t_api/t1/users?filters=id gt 5');
        $responseContent = json_decode($response->getContent());

        $this->assertEquals($response->status(), 200);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'id',
                ],
            ],
            'meta' => [
                'paging' => [
                    'links' => [
                        'next',
                    ],
                    'total',
                ],
                'time',
            ],
        ]);
        $this->assertTrue($responseContent->data[0]->id >= 5);
    }

    /** @test */
    public function test_get_all_comments_with_related_filter_one_to_many()
    {
        $response        = $this->call('GET', '/t_api/t1/comments?fields=content&filters=user.id eq 10');
        $responseContent = json_decode($response->getContent());

        $this->assertEquals($response->status(), 200);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'content'
                ],
            ],
        ]);
    }

    /** @test */
    public function test_get_all_users_with_filters_returns_error_for_unfilterable_fields()
    {
        $response        = $this->call('GET', '/t_api/t1/users?filters=email lk "Luc"');
        $responseContent = json_decode($response->getContent());

        $this->assertEquals($response->status(), 400);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $response->assertJsonStructure([
            'error' => [
                'message',
                'code',
            ],
        ]);
    }

    public function test_get_all_users_with_limit()
    {
        $response        = $this->call('GET', '/t_api/t1/users?limit=5');
        $responseContent = json_decode($response->getContent());

        $this->assertEquals($response->status(), 200);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'id',
                ],
            ],
            'meta' => [
                'paging' => [
                    'links' => [
                        'next',
                    ],
                    'total',
                ],
                'time',
            ],
        ]);
        $this->assertEquals(count($responseContent->data), 5);
    }

    public function test_get_all_users_with_limit_returns_error_for_invalid_limit()
    {
        $response        = $this->call('GET', '/t_api/t1/users?limit=0');
        $responseContent = json_decode($response->getContent());

        $this->assertEquals($response->status(), 400);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $response->assertJsonStructure([
            'error' => [
                'message',
                'code',
            ],
        ]);
    }

    public function test_get_all_users_with_limit_returns_error_for_negative_limit()
    {
        $response        = $this->call('GET', '/t_api/t1/users?limit=-5');
        $responseContent = json_decode($response->getContent());

        $this->assertEquals($response->status(), 400);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $response->assertJsonStructure([
            'error' => [
                'message',
                'code',
            ],
        ]);
    }

    public function test_get_all_users_with_order()
    {
        $response        = $this->call('GET', '/t_api/t1/users?order=id desc');
        $responseContent = json_decode($response->getContent());

        $this->assertEquals($response->status(), 200);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'id',
                ],
            ],
            'meta' => [
                'paging' => [
                    'links' => [
                        'next',
                    ],
                    'total',
                ],
                'time',
            ],
        ]);
        $this->assertEquals($responseContent->data[0]->id, 50);
    }

    /** @test */
    public function test_get_all_comments_with_related_order()
    {
        $response        = $this->call('GET', '/t_api/t1/comments?fields=content&order=user.id desc');
        $responseContent = json_decode($response->getContent());

        $this->assertEquals($response->status(), 200);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'content'
                ],
            ],
        ]);
    }

    /** @test */
    public function test_get_single_user()
    {
        $response        = $this->call('GET', '/t_api/t1/users/15');
        $responseContent = json_decode($response->getContent());

        $this->assertEquals($response->status(), 200);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $response->assertJsonStructure([
            'data' => [
                'name',
                'id',
            ],
            'meta' => [
                'time',
            ],
        ]);
        $this->assertEquals($responseContent->data->id, 15);
    }

    /** @test */
    public function test_get_single_user_returns_error_for_users_that_do_not_exist()
    {
        $response        = $this->call('GET', '/t_api/t1/users/1500');
        $responseContent = json_decode($response->getContent());

        $this->assertEquals($response->status(), 404);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $response->assertJsonStructure([
            'error' => [
                'message',
                'code',
            ],
        ]);
    }

    /** @test */
    public function test_get_single_user_with_one_to_one_relation_endpoint()
    {
        $response        = $this->call('GET', '/t_api/t1/users/15/address');
        $responseContent = json_decode($response->getContent());

        $this->assertEquals($response->status(), 200);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $response->assertJsonStructure([
            'data' => [
                'line_1',
                'line_2',
                'city',
                'state',
                'country',
                'zip_code',
                'id',
            ],
            'meta' => [
                'time',
            ],
        ]);
    }

    /** @test */
    public function test_get_single_user_with_one_to_many_relation_endpoint()
    {
        $response        = $this->call('GET', '/t_api/t1/users/15/posts');
        $responseContent = json_decode($response->getContent());

        $this->assertEquals($response->status(), 200);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $response->assertJsonStructure([
            'data' => [
                [
                    'title',
                    'content',
                    'id',
                ],
            ],
            'meta' => [
                'time',
            ],
        ]);
    }

    /** @test */
    public function test_create_user()
    {
        $response = $this->call(
            'POST',
            '/t_api/t1/users',
            [
                'name'     => 'Dummy User',
                'email'    => 'dummy@test.com',
                'password' => 'secret',
            ]
        );
        $responseContent = json_decode($response->getContent());

        $this->assertEquals(201, $response->status());
        $id       = $responseContent->data->id;
        $response = $responseContent = null;

        // Verify newly created user
        $response        = $this->call('GET', '/t_api/t1/users/' . $id);
        $responseContent = json_decode($response->getContent());

        $this->assertEquals($response->status(), 200);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $response->assertJson([
            'data' => [
                'name' => 'Dummy User',
                'id'   => $id,
            ],
        ]);
    }

    /** @test */
    public function test_update_user()
    {
        $response = $this->call(
            'PUT',
            '/t_api/t1/users/15',
            [
                'name'  => 'Dummy1 User',
                'email' => 'dummy1@test.com',
            ]
        );

        $this->assertEquals(200, $response->status());
        $response = null;

        // Verify newly created user
        $response        = $this->call('GET', '/t_api/t1/users/15');
        $responseContent = json_decode($response->getContent());

        $this->assertEquals($response->status(), 200);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $response->assertJson([
            'data' => [
                'name' => 'Dummy1 User',
                'id'   => 15,
            ],
        ]);
    }

    public function test_delete_user()
    {
        $response = $this->call('DELETE', '/t_api/t1/users/25');
        $this->assertEquals(200, $response->status());

        $response        = $this->call('GET', '/t_api/t1/users/25');
        $responseContent = json_decode($response->getContent());

        $this->assertEquals($response->status(), 404);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $response->assertJsonStructure([
            'error' => [
                'message',
                'code',
            ],
        ]);
    }
}
