<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Issues, rotates, or revokes the API token used by the `api` guard.
 *
 * The raw token is printed once on issue and never stored in plain text, so it
 * must be copied immediately. Re-issuing rotates (revokes) the previous token.
 */
class ManageApiToken extends Command
{
    /**
     * @var string
     */
    protected $signature = 'user:api-token
                            {email : The e-mail address of the user}
                            {--revoke : Revoke the user\'s token instead of issuing one}';

    /**
     * @var string
     */
    protected $description = 'Issue, rotate, or revoke a user\'s API token';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if ($user === null) {
            $this->error("No user found with e-mail {$this->argument('email')}.");

            return self::FAILURE;
        }

        if ($this->option('revoke')) {
            $user->revokeApiToken();
            $this->info("API token revoked for {$user->email}.");

            return self::SUCCESS;
        }

        $token = $user->generateApiToken();

        $this->info("API token issued for {$user->email}:");
        $this->newLine();
        $this->line($token);
        $this->newLine();
        $this->warn('Copy it now — it will not be shown again.');

        return self::SUCCESS;
    }
}
