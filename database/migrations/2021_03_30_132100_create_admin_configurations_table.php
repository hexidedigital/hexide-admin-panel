<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('admin_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('type');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->boolean('translatable')->default(0);
            $table->text('content')->nullable();
            $table->json('value')->nullable();
            $table->boolean('status')->default(1);
            $table->string('group')->nullable();
            $table->integer('in_group_position')->default(1);
            $table->timestamps();
        });

        Schema::create('admin_configuration_translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale');
            $table->foreignId('admin_configuration_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->text('text')->nullable();
            $table->json('json')->nullable();

            $table->unique(['admin_configuration_id', 'locale'], 'a_conf_transl_admin_conf_locale');
        });

        PermissionRelation::touch('admin_configurations')->addCustomSet()->addResourceSet();
    }

    public function down()
    {
        Schema::dropIfExists('admin_configuration_translations');
        Schema::dropIfExists('admin_configurations');
    }
};
