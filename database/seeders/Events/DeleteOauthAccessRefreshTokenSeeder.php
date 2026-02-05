<?php

namespace Database\Seeders\Events;

use App\Helper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Throwable;

class DeleteOauthAccessRefreshTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // UTC DATETIME: 2025-03-31 17:00:00 = IST DATETIME: 2025-03-31 23:30:00
            $event = '
                DROP EVENT IF EXISTS `Remove_invalidated_tokens`;

                CREATE EVENT `Remove_invalidated_tokens` ON SCHEDULE EVERY 1 DAY STARTS \'2025-03-31 17:00:00\'  ON COMPLETION NOT PRESERVE ENABLE DO BEGIN

                    START TRANSACTION;

                        -- Log the start of the event execution
                        INSERT INTO event_logs (event_name, message) VALUES ("Remove_invalidated_tokens", CONCAT("Event started at ", CONVERT_TZ(NOW(), "UTC", "Asia/Kolkata")));

                        -- Perform the actual tasks
                        -- Delete only records from the revoked and expired
                        DELETE FROM oauth_access_tokens
                        WHERE revoked = "1" OR DATE(expires_at) < DATE(CONVERT_TZ(NOW(), "UTC", "Asia/Kolkata")) - INTERVAL ' . (config('constants.refresh_token_expiry_days') + 1) . ' DAY;

                        DELETE FROM oauth_refresh_tokens
                        WHERE revoked = "1" OR DATE(expires_at) < DATE(CONVERT_TZ(NOW(), "UTC", "Asia/Kolkata")) - INTERVAL 1 DAY;

                        DELETE FROM oauth_access_tokens
                        WHERE id NOT IN (
                            SELECT access_token_id FROM oauth_refresh_tokens
                        );

                        DELETE FROM oauth_refresh_tokens
                        WHERE access_token_id NOT IN (
                            SELECT id FROM oauth_access_tokens
                        );

                        -- Log the end of the event execution
                        INSERT INTO event_logs (event_name, message) VALUES ("Remove_invalidated_tokens", CONCAT("Event completed at ", CONVERT_TZ(NOW(), "UTC", "Asia/Kolkata")));

                    COMMIT;

                END;
            ';

            DB::unprepared($event);
            $this->command->info('Event `Remove_invalidated_tokens` has been created.');
        } catch (Throwable $th) {
            Helper::logCatchError($th, static::class, __FUNCTION__);
            $this->command->error('Error: ' . $th->getMessage());
        }
    }
}
