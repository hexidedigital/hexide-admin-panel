<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale');
            $table->string('group');
            $table->string('key');
            $table->text('value')->nullable();

            $table->unique(['locale', 'group', 'key']);

            $table->timestamps();
        });

        $data = [
            'admin_access',
            'api_access',
            'site_access',
        ];
        PermissionRelation::touch('translations')->extra($data)->populate();
    }

    public function down()
    {
        Schema::dropIfExists('translations');
    }
};
