<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('Authenticated user ID');
            $table->string('ip', 15)->comment('Client IP');
            $table->string('verb', 6)->comment('HTTP Verb');
            $table->string('endpoint', 255)->comment('API endpoint');
            $table->text('request')->comment('Request content');
            $table->longText('response')->comment('Response content');
            $table->unsignedInteger('code')->comment('HTTP status code');
            $table->timestamps();

            $table->index('user_id');
            $table->index('ip');
            $table->index('verb');
            $table->index('code');

            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
}
