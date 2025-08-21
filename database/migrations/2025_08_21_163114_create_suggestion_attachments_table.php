<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('suggestion_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('suggestion_id');

            // File metadata
            $table->string('original_name', 255);
            $table->string('stored_path', 500);
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();

            // Attachment status (e.g., active, flagged, removed)
            $table->enum('status', ['active', 'inactive', 'removed'])
                  ->default('active')
                  ->index();

            // Audit
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();

            $table->timestamps();

            // FK
            $table->foreign('suggestion_id')
                  ->references('id')->on('suggestions')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->index(['suggestion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suggestion_attachments');
    }
};
