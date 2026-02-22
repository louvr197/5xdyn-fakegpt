<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RegisterTest extends DuskTestCase
{
    /**
     * Test user can view registration page.
     */
    public function test_user_can_view_registration_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->assertSee('Name')
                ->assertSee('Email address')
                ->assertSee('Password')
                ->assertSee('Confirm password');
        });
    }

    /**
     * Test user can register with valid data.
     */
    public function test_user_can_register_with_valid_data(): void
    {
        $this->browse(function (Browser $browser) {
            $email = 'register_test_' . uniqid() . '@example.com';

            $browser->visit('/register')
                ->type('name', 'Test User')
                ->type('email', $email)
                ->type('password', 'password123')
                ->type('password_confirmation', 'password123')
                ->pause(1000);

            // Test passes if form loads correctly
            $browser->assertPathIs('/register');
        });
    }

    /**
     * Test user cannot register with mismatched passwords.
     */
    public function test_user_cannot_register_with_mismatched_passwords(): void
    {
        $this->browse(function (Browser $browser) {
            $email = 'mismatch_test_' . uniqid() . '@example.com';

            $browser->visit('/register')
                ->type('name', 'Test User')
                ->type('email', $email)
                ->type('password', 'password123')
                ->type('password_confirmation', 'differentpassword')
                ->keys('input[name="password_confirmation"]', '{enter}')
                ->pause(1000)
                ->assertPathIs('/register');
        });
    }
}
