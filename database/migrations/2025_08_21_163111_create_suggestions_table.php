<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('suggestions', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Personal & school info
            $table->string('name', 191);
            $table->string('designation', 191);
            $table->string('school_name', 255);
            $table->string('email', 191)->index();
            $table->string('phone', 50)->nullable();

            // Anonymous toggle
            $table->boolean('is_anonymous')->default(false)->index();

            // Form data
            $table->string('theme', 255);
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('department_id');
            $table->text('subject')->nullable();
            $table->longText('details');

            // Process tracking
            // (Keep your existing boolean if your code already uses it; optional to remove later)
            $table->boolean('deleted')->default(false)->index();
            $table->timestampTz('submitted_at')->nullable()->index();

            // Clear lifecycle states for the suggestion itself
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'archived'])
                  ->default('submitted')
                  ->index();

            // Audit
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();

            $table->timestamps();

            // FKs
            $table->foreign('category_id')
                  ->references('id')->on('categories')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            $table->foreign('department_id')
                  ->references('id')->on('departments')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suggestions');
    }
};
