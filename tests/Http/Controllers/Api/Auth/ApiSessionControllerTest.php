<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApiSessionControllerTest extends MailTestCase
{
    use DatabaseTransactions;
    use AuthTestTrait;
}