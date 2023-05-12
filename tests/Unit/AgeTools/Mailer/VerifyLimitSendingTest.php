<?php

namespace Tests\Unit\AgeTools\Mailer;

use App\Http\Controllers\AgeTools\Tools\Mailer\_aux\VerifyLimitSendings;
use App\Models\AgeTools\Tools\Mailer\Batch;
use App\Models\AgeTools\Tools\Mailer\Mailer;
use PHPUnit\Framework\TestCase;

class VerifyLimitSendingTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_verify()
    {
        $mailer = new Mailer();

        $verify = new VerifyLimitSendings($mailer);





        $this->assertTrue(true);
    }
}
