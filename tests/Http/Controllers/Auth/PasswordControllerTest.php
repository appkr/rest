<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class PasswordControllerTest extends MailTestCase
{
    use DatabaseTransactions;
    use AuthTestTrait;

    /**
     * @var array Not yet encrypted credentials
     */
    private $credentials = [
        'email'    => 'john@example.com',
        'password' => 'password'
    ];

    /** @test */
    public function it_reminds_forgotten_password()
    {
        $this->passwordRemind()
            ->see(trans('passwords.sent'))
            ->onPage(route('reminder.create'));

        $emailMessage = $this->getLastEmail();

        $this->assertEmailBodyContains('Click here to reset your password:', $emailMessage);
        $this->assertEmailWasSentTo($this->credentials['email'], $emailMessage);
    }

    /** @test */
    public function it_notifies_the_user_if_the_provided_email_is_not_valid()
    {
        $this->passwordRemind('not-existing@example.com')
            ->see(trans('passwords.user'))
            ->onPage(route('reminder.create'));
    }

    /** @test */
    public function it_notifies_the_user_he_is_a_social_login_user()
    {
        $user = factory(\App\User::class)->create(['password' => '']);
        $socialUser = $user->socials()->save(factory(App\Social::class)->make());

        $this->passwordRemind($user->email)
            ->see(trans('auth.noPasswordUser', ['socials' => implode(', ', $user->getSocialProviders())]))
                ->onPage(route('reminder.create'));
    }

    /** @test */
    public function it_resets_password()
    {
        $this->passwordReset()
            ->see(trans('auth.successReset'))
            ->onPage(route('session.create'));

        $this->logIn(array_merge($this->getCredentials(), ['password' => 'modified']))
            ->onPage(route('session.create'));
    }

    /** @test */
    public function it_throws_not_found_exception_when_the_token_is_not_valid()
    {
        $this->passwordReset('fake-token')
            ->see(trans('passwords.token'));
    }

    /**
     * @return array
     */
    private function getCredentials()
    {
        return [
            'email'    => $this->credentials['email'],
            'password' => bcrypt($this->credentials['password'])
        ];
    }
}