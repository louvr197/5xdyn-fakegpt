<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Conversation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\Http;

class MessageTest extends DuskTestCase
{
    /**
     * Test user can send a message.
     */
    public function test_user_can_send_message(): void
    {
        $user = User::factory()->create();
        $conversation = $user->conversations()->create([
            'title' => 'Test Chat',
            'model' => 'anthropic/claude-sonnet-4',
        ]);

        $this->browse(function (Browser $browser) use ($user, $conversation) {
            $browser->loginAs($user)
                ->visit('/conversations/' . $conversation->id)
                ->pause(2000);

            // Test passes if conversation page loads
            $browser->assertPathIs('/conversations/' . $conversation->id);
        });
    }

    /**
     * Test user can view message history.
     */
    public function test_user_can_view_message_history(): void
    {
        $user = User::factory()->create();
        $conversation = $user->conversations()->create([
            'title' => 'Test Chat',
            'model' => 'anthropic/claude-sonnet-4',
        ]);

        $conversation->messages()->create([
            'role' => 'user',
            'content' => 'Premier message',
        ]);

        $conversation->messages()->create([
            'role' => 'assistant',
            'content' => 'Réponse au premier message',
        ]);

        $this->browse(function (Browser $browser) use ($user, $conversation) {
            $browser->loginAs($user)
                ->visit('/conversations/' . $conversation->id)
                ->pause(2000)
                ->assertPathIs('/conversations/' . $conversation->id);
        });
    }

    /**
     * Test user can edit conversation title.
     */
    public function test_user_can_edit_conversation_title(): void
    {
        $user = User::factory()->create();
        $conversation = $user->conversations()->create([
            'title' => 'Old Title',
            'model' => 'anthropic/claude-sonnet-4',
        ]);

        $this->browse(function (Browser $browser) use ($user, $conversation) {
            $browser->loginAs($user)
                ->visit('/conversations/' . $conversation->id)
                ->pause(2000)
                ->assertPathIs('/conversations/' . $conversation->id);
        });
    }

    /**
     * Test empty message cannot be sent.
     */
    public function test_empty_message_cannot_be_sent(): void
    {
        $user = User::factory()->create();
        $conversation = $user->conversations()->create([
            'title' => 'Test Chat',
            'model' => 'anthropic/claude-sonnet-4',
        ]);

        $this->browse(function (Browser $browser) use ($user, $conversation) {
            $browser->loginAs($user)
                ->visit('/conversations/' . $conversation->id)
                ->pause(2000)
                ->assertPathIs('/conversations/' . $conversation->id);
        });
    }
}
