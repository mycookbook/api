<?php

/**
 * HelpersTest
 */
class HelpersTest extends TestCase
{
    public function testSlugifyFunction()
    {
        $output = slugify('plain text');

        $this->assertEquals('plain-text', $output);
    }
}
