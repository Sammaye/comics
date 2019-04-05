<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(\Jenssegers\Mongodb\Schema\Blueprint $collection){
            $collection->index(['email_frequency', 'last_feed_sent']);
        });

        Schema::table('comic', function(\Jenssegers\Mongodb\Schema\Blueprint $collection){
            $collection->index(['title', 'live']);
            $collection->index(['_id', 'live']);
        });

        Schema::table('comic_strip', function(\Jenssegers\Mongodb\Schema\Blueprint $collection){
            $collection->index(['comic_id' => 1, 'index' => 1]);
            $collection->index(['comic_id' => 1, 'date' => -1, 'index' => 1]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
