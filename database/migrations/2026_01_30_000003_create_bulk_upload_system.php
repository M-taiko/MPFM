<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('bulk_uploads')) {
            Schema::create('bulk_uploads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
                $table->foreignId('uploaded_by_user_id')->constrained('users')->cascadeOnDelete();
                $table->string('file_name');
                $table->string('file_path');
                $table->string('file_type'); // csv, xlsx, json
                $table->integer('file_size_bytes');
                $table->string('file_hash')->unique();
                $table->string('status')->default('pending'); // pending, processing, completed, failed
                $table->integer('total_rows')->default(0);
                $table->integer('processed_rows')->default(0);
                $table->integer('successful_rows')->default(0);
                $table->integer('failed_rows')->default(0);
                $table->json('error_details')->nullable(); // Details about failures
                $table->dateTime('processing_started_at')->nullable();
                $table->dateTime('processing_completed_at')->nullable();
                $table->string('processing_duration_seconds')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index('department_id');
                $table->index('status');
                $table->index('created_at');
                $table->index('uploaded_by_user_id');
                $table->index(['department_id', 'status', 'created_at']);
            });
        }

        if (!Schema::hasTable('bulk_upload_rows')) {
            Schema::create('bulk_upload_rows', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bulk_upload_id')->constrained('bulk_uploads')->cascadeOnDelete();
                $table->integer('row_number');
                $table->json('raw_data');
                $table->json('parsed_data')->nullable();
                $table->string('status')->default('pending'); // pending, processed, successful, failed
                $table->string('error_message')->nullable();
                $table->unsignedBigInteger('created_record_id')->nullable(); // ID of created test/record
                $table->timestamps();

                $table->index('bulk_upload_id');
                $table->index('status');
                $table->index(['bulk_upload_id', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('bulk_upload_rows')) {
            Schema::dropIfExists('bulk_upload_rows');
        }

        if (Schema::hasTable('bulk_uploads')) {
            Schema::dropIfExists('bulk_uploads');
        }

        Schema::enableForeignKeyConstraints();
    }
};
