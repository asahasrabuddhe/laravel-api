<?php

namespace Asahasrabuddhe\LaravelAPI\Tests;

use Illuminate\Http\Request;
use Asahasrabuddhe\LaravelAPI\RequestParser;
use Asahasrabuddhe\LaravelAPI\Tests\Models\Post;
use Asahasrabuddhe\LaravelAPI\Tests\Models\User;
use Asahasrabuddhe\LaravelAPI\Exceptions\Parse\UnknownFieldException;
use Asahasrabuddhe\LaravelAPI\Exceptions\Parse\InvalidOffsetException;
use Asahasrabuddhe\LaravelAPI\Exceptions\Parse\InvalidPerPageLimitException;
use Asahasrabuddhe\LaravelAPI\Exceptions\Parse\FieldCannotBeFilteredException;
use Asahasrabuddhe\LaravelAPI\Exceptions\Parse\InvalidFilterDefinitionException;
use Asahasrabuddhe\LaravelAPI\Exceptions\Parse\InvalidOrderingDefinitionException;

class RequestParserTest extends TestCase
{
    /** @test */
    public function parses_limit_from_request()
    {
        $_GET          = [];
        $_GET['limit'] = 15;

        request()->merge($_GET);

        $parser = new RequestParser(User::class);

        $this->assertEquals($parser->getLimit(), 15);
    }

    /** @test */
    public function parses_limit_from_configuration()
    {
        $_GET = [];

        request()->merge($_GET);

        $parser = new RequestParser(User::class);

        $this->assertEquals($parser->getLimit(), 10);
    }

    /** @test */
    public function throws_exception_for_negative_limit()
    {
        $this->expectException(InvalidPerPageLimitException::class);

        $_GET          = [];
        $_GET['limit'] = -10;

        request()->merge($_GET);

        $parser = new RequestParser(User::class);
    }

    /** @test */
    public function throws_exception_for_zero_limit()
    {
        $this->expectException(InvalidPerPageLimitException::class);

        $_GET          = [];
        $_GET['limit'] = 0;

        request()->merge($_GET);

        $parser = new RequestParser(User::class);
    }

    /** @test */
    public function parses_offset_from_request()
    {
        $_GET           = [];
        $_GET['offset'] = 10;

        request()->merge($_GET);

        $parser = new RequestParser(User::class);

        $this->assertEquals($parser->getOffset(), 10);
    }

    /** @test */
    public function parses_default_offset()
    {
        $_GET = [];

        $parser = new RequestParser(User::class);

        $this->assertEquals($parser->getOffset(), 0);
    }

    /** @test */
    public function throws_exception_for_negative_offset()
    {
        $this->expectException(InvalidOffsetException::class);

        $_GET           = [];
        $_GET['offset'] = -10;

        request()->merge($_GET);

        $parser = new RequestParser(User::class);
    }

    /** @test */
    public function parses_fields_from_request()
    {
        $_GET           = [];
        $_GET['fields'] = 'name,email,address,posts';

        request()->merge($_GET);

        $parser = new RequestParser(User::class);

        $this->assertEquals($parser->getFields(), ['name', 'email', 'id']);

        $this->assertArrayHasKey('address', $parser->getRelations());
        $this->assertArrayHasKey('posts', $parser->getRelations());
    }

    /** @test */
    public function parses_fields_from_resource()
    {
        $_GET = [];

        request()->merge($_GET);

        $parser = new RequestParser(Post::class);

        $this->assertEquals($parser->getFields(), ['title', 'content', 'id']);
    }

    /** @test */
    public function throws_exception_for_unknown_field()
    {
        $this->expectException(UnknownFieldException::class);

        $_GET           = [];
        $_GET['fields'] = 'phone';

        request()->merge($_GET);

        $parser = new RequestParser(User::class);
    }

    /** @test */
    public function parses_greater_than_filter_from_request()
    {
        $_GET            = [];
        $_GET['filters'] = 'id gt 5';

        request()->merge($_GET);

        $parser = new RequestParser(User::class);

        $this->assertEquals($parser->getFilters(), '(`id`  >  5)');
    }

    /** @test */
    public function parses_greater_than_equal_to_filter_from_request()
    {
        $_GET            = [];
        $_GET['filters'] = 'id ge 5';

        request()->merge($_GET);

        $parser = new RequestParser(User::class);

        $this->assertEquals($parser->getFilters(), '(`id`  >=  5)');
    }

    /** @test */
    public function parses_lesser_than_filter_from_request()
    {
        $_GET            = [];
        $_GET['filters'] = 'id lt 5';

        request()->merge($_GET);

        $parser = new RequestParser(User::class);

        $this->assertEquals($parser->getFilters(), '(`id`  <  5)');
    }

    /** @test */
    public function parses_less_than_equal_to_filter_from_request()
    {
        $_GET            = [];
        $_GET['filters'] = 'id le 5';

        request()->merge($_GET);

        $parser = new RequestParser(User::class);

        $this->assertEquals($parser->getFilters(), '(`id`  <=  5)');
    }

    /** @test */
    public function parses_equal_to_filter_from_request()
    {
        $_GET            = [];
        $_GET['filters'] = 'id eq 5';

        request()->merge($_GET);

        $parser = new RequestParser(User::class);

        $this->assertEquals($parser->getFilters(), '(`id`  =  5)');
    }

    /** @test */
    public function parses_not_equal_filter_from_request()
    {
        $_GET            = [];
        $_GET['filters'] = 'id ne 5';

        request()->merge($_GET);

        $parser = new RequestParser(User::class);

        $this->assertEquals($parser->getFilters(), '(`id`  <>  5)');
    }

    /** @test */
    public function parses_like_filter_from_request()
    {
        $_GET            = [];
        $_GET['filters'] = 'name lk "Luc"';

        request()->merge($_GET);

        $parser = new RequestParser(User::class);

        $this->assertEquals($parser->getFilters(), '(`name`  LIKE  "Luc")');
    }

    /** @test */
    public function parses_is_null_filter_from_request()
    {
        $_GET            = [];
        $_GET['filters'] = 'id eq null';

        request()->merge($_GET);

        $parser = new RequestParser(User::class);

        $this->assertEquals($parser->getFilters(), '(`id` is null)');
    }

    /** @test */
    public function parses_is_not_null_filter_from_request()
    {
        $_GET            = [];
        $_GET['filters'] = 'id ne null';

        request()->merge($_GET);

        $parser = new RequestParser(User::class);

        $this->assertEquals($parser->getFilters(), '(`id` is not null)');
    }

    /** @test */
    public function throws_exception_for_invalid_filtering()
    {
        $this->expectException(InvalidFilterDefinitionException::class);

        $_GET            = [];
        $_GET['filters'] = 'id as 5';

        request()->merge($_GET);

        $parser = new RequestParser(User::class);
    }

    /** @test  */
    public function throws_exception_for_fields_that_cannot_be_filtered()
    {
        $this->expectException(FieldCannotBeFilteredException::class);

        $_GET            = [];
        $_GET['filters'] = 'email lk "perennial"';

        request()->merge($_GET);

        $parser = new RequestParser(User::class);
    }

    /** @test */
    public function parse_ascending_ordering_from_request()
    {
        $_GET          = [];
        $_GET['order'] = 'id asc';

        request()->merge($_GET);

        $parser = new RequestParser(User::class);

        $this->assertEquals($parser->getOrder(), '`id` asc');
    }

    /** @test */
    public function parse_descending_ordering_from_request()
    {
        $_GET          = [];
        $_GET['order'] = 'id desc';

        request()->merge($_GET);

        $parser = new RequestParser(User::class);

        $this->assertEquals($parser->getOrder(), '`id` desc');
    }

    /** @test */
    public function throws_exception_for_invalid_ordering()
    {
        $this->expectException(InvalidOrderingDefinitionException::class);

        $_GET          = [];
        $_GET['order'] = 'id ad';

        request()->merge($_GET);

        $parser = new RequestParser(User::class);

        dd($parser->getORder());
    }
}
