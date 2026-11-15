<?php

use CraftCms\Cms\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sites upgraded from Craft 5 already have the table
        if (Schema::hasTable('easyaddressfield')) {
            return;
        }

        Schema::create('easyaddressfield', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('owner');
            $table->integer('site');
            $table->integer('field');
            $table->string('name')->nullable();
            $table->string('street', 100)->nullable();
            $table->string('street2', 100)->nullable();
            $table->string('postalCode', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('country')->nullable();
            $table->decimal('latitude', 11, 9)->nullable();
            $table->decimal('longitude', 12, 9)->nullable();
            $table->dateTime('dateCreated');
            $table->dateTime('dateUpdated');
            $table->char('uid', 36)->default('0');
            $table->unique(['owner', 'site', 'field']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('easyaddressfield');
    }
};
