    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    /* mengambil nillai enum untuk status dn role yang sudah ditentukan  */
    use App\Enums\Status;
    use App\Enums\UserRole;

    return new class extends Migration
    {
        /* menalankan migrasi untuk tabel users */
        /* Tabel Users, password_reset_tokens, sessions */
        public function up(): void
        {
            Schema::create('user', function (Blueprint $table) {
                /* untuk semua user */
                $table->unsignedBigInteger('id_user')->autoIncrement();
                $table->string('nama_lengkap', 255);
                $table->string('password');
                $table->string('nomor_tlp', 20)->nullable();
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                        /* eksekusi menggunakan enums */
                $table->enum('role', [UserRole::SuperAdmin->value, UserRole::AdminVendor->value, UserRole::Hr->value, UserRole::Karyawan->value, UserRole::KepalaDepartemen->value])->default(UserRole::Karyawan->value);
                $table->enum('status', [Status::Active->value, Status::Inactive->value, Status::Pending->value])->default(Status::Active->value);


                /* hanaya untuk karyawan */
                $table->string('alamat', 255)->nullable();
                $table->string('nip', 100)->default('0');
                $table->date('tanggal_keluar')->nullable();
                $table->date('tanggal_masuk')->nullable();

                $table->unsignedBigInteger('outsourcing_id')->nullable();
                $table->foreign('outsourcing_id', 'outsourcing_memiliki_karyawan')
                ->references('id_outsourcing')->on('outsourcing')
                ->onDelete('cascade')->onUpdate('cascade');

                $table->unsignedBigInteger('departemen_id')->nullable();
                $table->foreign('departemen_id', 'karyawan berasal dari departemen')
                ->references('id_departemen')->on('departemen')
                ->onDelete('cascade')->onUpdate('cascade');

                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id', 'user dibuat oleh user')
                ->references('id_user')->on('user')
                ->onDelete('cascade')->onUpdate('cascade');

                $table->rememberToken();
                $table->timestamps();
            });

            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });

            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('user');
            Schema::dropIfExists('password_reset_tokens');
            Schema::dropIfExists('sessions');
        }
    };
