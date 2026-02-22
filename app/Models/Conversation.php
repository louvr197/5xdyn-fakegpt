<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    /** @use HasFactory<\Database\Factories\ConversationFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'model',
        'title',
        'instruction_preset_id',
        'custom_instructions_about',
        'custom_instructions_behavior',
        'custom_instructions_commands',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function instructionPreset()
    {
        return $this->belongsTo(InstructionPreset::class);
    }


}
