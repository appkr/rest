<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class RegisterControllerTest extends MailTestCase
{
    use DatabaseTransactions;
    use AuthTestTrait;

    /** @test */
    public function it_registers_a_user()
    {
        $this->signUp(['name' => 'John Doe'])
            ->seePageIs(route('user.create'))
            ->see(trans('auth.recommendActivation'))
            ->seeInDatabase('users', ['name' => 'John Doe']);
    }

    /** @test */
    public function it_should_not_proceed_when_validation_fails()
    {
        $this->signUp(['name' => '', 'email' => 'abc', 'password_confirmation' => 'pass'])
            ->see(trans('validation.required', ['attribute' => 'name']))
            ->see(trans('validation.email', ['attribute' => 'email']))
            ->see(trans('validation.confirmed', ['attribute' => 'password']))
            ->onPage(route('user.create'));

        $this->assertHasOldInput();
    }

    /** @test */
    public function it_sends_activation_email()
    {
        $this->signUp();

        $email = $this->getLastEmail();

        $this->assertEmailBodyContains('Click here to activate your account', $email);
        $this->assertEmailWasSentTo('john@example.com', $email);
    }

    /** @test */
    public function it_activates_a_user()
    {
        $receipient = 'activation@example.com';

        $this->activateUser($receipient)
            ->see(trans('auth.activated'))
            ->onPage(route('home'))
            ->seeInDatabase('users', [
                'email'     => $receipient,
                'activated' => 1
            ]);
    }

    /** @test */
    public function it_should_not_activate_when_matching_user_is_not_found()
    {
        $response = $this->call('GET', route('user.activate', 'not-existing-code'));

        $this->assertEquals(404, $response->status());
        $this->assertContains(trans('errors.resourceNotFound'), $response->getContent());
    }

    /** @test */
    public function it_resends_activation_email_upon_request()
    {
        $receipient = 'activiation@example.com';
        $this->signUp(['email' => $receipient]);
        $code = \App\User::whereEmail($receipient)->first()->activation_code;

        $this->visit(route('user.send-activation', $code));

        $email = $this->getLastEmail();

        $this->assertEmailBodyContains('Click here to activate your account', $email);
        $this->assertEmailWasSentTo('activiation@example.com', $email);
    }

    /** @test */
    public function it_destroys_a_user()
    {
        $attribute = ['name' => 'destroyed'];

        $user = factory(\App\User::class)->create($attribute);

        $this->actingAs($user)
            ->visit(route('user.destroy'))
            ->notSeeInDatabase('users', $attribute)
            ->seePageIs(route('front'));
    }
}
