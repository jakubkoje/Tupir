<?php namespace AppTupir\User\Updates;

use October\Rain\Support\Facades\Schema;
use October\Rain\Database\Updates\Migration;

class UpdateUsersAddBio extends Migration
{
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->text('bio')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
