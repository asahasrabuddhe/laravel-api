<?php

namespace Asahasrabuddhe\LaravelAPI\Tests;

use Carbon\Carbon;
use Asahasrabuddhe\LaravelAPI\BaseModel;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Tests\Exception\TooManyRequestsHttpExceptionTest;
use Asahasrabuddhe\LaravelAPI\RequestParser;
use Asahasrabuddhe\LaravelAPI\Tests\Models\User;
use Asahasrabuddhe\LaravelAPI\Exceptions\Parse\InvalidPerPageLimitException;
use Asahasrabuddhe\LaravelAPI\Exceptions\Parse\InvalidOffsetException;


class RequestParserTest extends TestCase
{
    /** @test */
    public function parses_limit_from_request()
    {
        $_GET['limit'] = 15;
        $_GET['offset'] = null;
        
        request()->merge($_GET);

        $parser = new RequestParser(User::class);

        $this->assertEquals($parser->getLimit(), 15);
    }

    /** @test */
    public function parses_limit_from_configuration()
    {
        $_GET['limit'] = null;
        $_GET['offset'] = null;

        request()->merge($_GET);

        $parser = new RequestParser(User::class);

        $this->assertEquals($parser->getLimit(), 10);
    }

    /** @test */
    public function throws_exception_for_negative_limit()
    {
        $this->expectException(InvalidPerPageLimitException::class);
       
        $_GET['limit'] = -10;
        $_GET['offset'] = null;

        request()->merge($_GET);

        $parser = new RequestParser(User::class);
    }

    /** @test */
    public function throws_exception_for_zero_limit()
    {
        $this->expectException(InvalidPerPageLimitException::class);

        $_GET['limit'] = 0;
        $_GET['offset'] = null;

        request()->merge($_GET);
        
        $parser = new RequestParser(User::class);
    }

    /** @test */
    public function parses_offset_from_request()
    {
        $_GET['limit'] = null;
        $_GET['offset'] = 10;

        request()->merge($_GET);

        $parser = new RequestParser(User::class);

        $this->assertEquals($parser->getOffset(), 10);
    }

    /** @test */
    public function parses_default_offset()
    {
        $_GET['limit'] = null;
        $_GET['offset'] = null;

        $parser = new RequestParser(User::class);

        $this->assertEquals($parser->getOffset(), 0);
    }

    /** @test */
    public function throws_exception_for_negative_offset()
    {
        $this->expectException(InvalidOffsetException::class);
        
        $_GET['limit'] = null;
        $_GET['offset'] = -10;

        request()->merge($_GET);

        $parser = new RequestParser(User::class);
    }
}