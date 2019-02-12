<?php

use PHPUnit\Framework\TestCase;

/**
 * Common setUp() and tearDown() for unit tests
 */
class Common extends TestCase
{

    public function setUp(): void
    {
        // ----------------------------------------------
        // load test smtp details from .env to $_ENV
        // ----------------------------------------------
        try {
            $dotenv = \Dotenv\Dotenv::create(__DIR__);
            $dotenv->load();
        } catch (\Dotenv\Exception\InvalidPathException $ex) {
            echo "\r\n" . $ex->getMessage() . "\r\n\r\n"
            . 'Instructions: Create a .env file inside tests/unit/ and use the '
            . 'content of sample.env as template';
        }

        sleep(2); // put delay so mail servers won't receive "too fast" connections
    }

    public function tearDown(): void
    {

    }
}
