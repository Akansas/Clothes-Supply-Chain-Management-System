<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('conversation_id')->nullable()->after('id');
        });

        DB::statement('UPDATE chat_messages SET conversation_id = chat_conversation_id');

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropForeign(['chat_conversation_id']);
            $table->dropColumn('chat_conversation_id');
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('chat_conversation_id')->nullable();
        });

        DB::statement('UPDATE chat_messages SET chat_conversation_id = conversation_id');

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn('conversation_id');
        });
    }
};
