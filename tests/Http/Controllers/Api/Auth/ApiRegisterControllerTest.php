<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApiRegisterControllerTest extends MailTestCase
{
    use DatabaseTransactions;
    use AuthTestTrait;
}
