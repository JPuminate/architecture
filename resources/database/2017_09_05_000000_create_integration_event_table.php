<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntegrationEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\JPuminate\Architecture\EventBus\EventBusRabbitMQ::$EVENT_LOG_TABLE, function (Blueprint $table) {
            $table->string('event_id', 120)->unique();
            $table->string('creation_time', 27);
            $table->string('event_type');
            $table->integer('event_state');
            $table->string('event_payload', 1500);
            $table->integer('time_sent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(\JPuminate\Architecture\EventBus\EventBusRabbitMQ::$EVENT_LOG_TABLE);
    }
}
