<?php 

namespace Tests\Feature;

use Tests\TestCase;

class BasicMathTest extends TestCase
{
    /**
     * 測試 1 + 1 等於 2
     * 
     * @return void
     */
    public function test_basic_math_addition(): void
    {
        $this->assertEquals(2, 1 + 1);
    }
}