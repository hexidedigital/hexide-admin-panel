<?php

use HexideDigital\ModelPermissions\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->integer('position')->default(1);
            $table->boolean('status')->default(true);
            $table->string('image')->nullable();

            $table->foreignId('parent_id')->nullable()
                ->references('id')->on('pages')
                ->nullOnDelete()->cascadeOnUpdate();

            $table->timestamps();
        });

        Schema::create('page_translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale');
            $table->foreignId('page_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('title')->nullable();
            $table->text('short_description')->nullable();
            $table->text('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->unique(['locale', 'page_id']);
        });

        PermissionRelation::touch('pages')->addCustomSet()->except([
            Permission::Create,
            Permission::Delete,
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('page_translations');
        Schema::dropIfExists('pages');
    }
};
