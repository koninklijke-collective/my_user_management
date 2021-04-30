<?php

namespace KoninklijkeCollective\MyUserManagement\Tests\Unit;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class GreenTest extends UnitTestCase
{
    /** @test */
    public function travis_invoked(): void
    {
        $this->assertTrue(true);
    }
}
