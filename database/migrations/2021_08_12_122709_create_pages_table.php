<?php

use HexideDigital\ModelPermissions\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// todo correct
return new class extends Migration {
    public function up()
    {
        Schema::create(app(\App\Models\Page::class)->getTable(), function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->unsignedInteger('position')->nullable();
            $table->boolean('status')->default(true);
            $table->string('image')->nullable();

            $table->foreignId('parent_id')->nullable()->references('id')->on($table->getTable())
                ->nullOnDelete()->cascadeOnUpdate();

            $table->timestamps();
        });

        Schema::create(app(\App\Models\PageTranslation::class)->getTable(), function (Blueprint $table) {
            $table->id();
            $table->string('locale');
            $table->foreignIdFor(\App\Models\Page::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('title')->nullable();
            $table->text('short_description')->nullable();
            $table->text('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->unique(['locale', 'page_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists(app(\App\Models\PageTranslation::class)->getTable());
        Schema::dropIfExists(app(\App\Models\Page::class)->getTable());
    }
};
