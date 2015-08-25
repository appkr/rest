<?php

class WelcomeControllerTest extends TestCase
{
    /** @test */
    public function it_loads_welcome_page()
    {
        $this->visit(route('front'))
             ->see('Laravel 5');
    }

    /** @test */
    public function it_redirect_to_login_page_without_login()
    {
        $this->visit(route('home'))
            ->seePageIs(route('session.create'));
    }
}
