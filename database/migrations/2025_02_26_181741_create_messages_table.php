<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receiver_id')->nullable()->after('user_id');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('set null');
            $table->string('title')->nullable()->after('receiver_id');
            $table->boolean('is_general')->default(false)->after('message');
            // Make order_id nullable since some messages might not be related to orders
            $table->unsignedBigInteger('order_id')->nullable()->change();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('message_type')->default('client')->after('message');
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('message_type');
        });
    }
};
