<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class WreckfestInstallCommand extends Command
{
    protected $signature = 'wreckfest:install {--force : Force installation even if already configured}';

    protected $description = 'Interactive installation wizard for WreckfestWeb';

    public function handle(): int
    {
        $this->displayBanner();

        // Check if already installed
        if (! $this->option('force') && $this->isAlreadyInstalled()) {
            $this->warn('WreckfestWeb appears to be already installed!');
            if (! $this->confirm('Do you want to run the installation wizard anyway?', false)) {
                $this->info('Installation cancelled.');
                return self::SUCCESS;
            }
        }

        $this->newLine();
        $this->info('This wizard will guide you through the installation process.');
        $this->newLine();

        // Step 1: Check prerequisites
        if (! $this->checkPrerequisites()) {
            return self::FAILURE;
        }

        // Step 2: Database setup
        $this->setupDatabase();

        // Step 3: Reverb configuration
        $this->setupReverb();

        // Step 4: Wreckfest API configuration
        $this->setupWreckfestApi();

        // Step 5: Optional features
        $this->setupOptionalFeatures();

        // Step 6: Run migrations
        $this->runMigrations();

        // Step 7: Create admin user
        $this->createAdminUser();

        // Step 8: Build assets
        $this->buildAssets();

        // Final summary
        $this->displaySummary();

        return self::SUCCESS;
    }

    protected function displayBanner(): void
    {
        $this->info('
╔══════════════════════════════════════════════════════════╗
║                                                          ║
║         WreckfestWeb Installation Wizard                ║
║                                                          ║
╚══════════════════════════════════════════════════════════╝
        ');
    }

    protected function isAlreadyInstalled(): bool
    {
        return File::exists(database_path('database.sqlite'))
            && config('app.key')
            && config('wreckfest.api_url') !== 'https://localhost:5101/api';
    }

    protected function checkPrerequisites(): bool
    {
        $this->task('Checking prerequisites', function () {
            return true;
        });

        $issues = [];

        // Check PHP version
        if (version_compare(PHP_VERSION, '8.2.0', '<')) {
            $issues[] = 'PHP 8.2+ is required. Current version: ' . PHP_VERSION;
        }

        // Check SQLite extension
        if (! extension_loaded('sqlite3')) {
            $issues[] = 'SQLite3 extension is not installed';
        }

        // Check if .env exists
        if (! File::exists(base_path('.env'))) {
            $issues[] = '.env file not found. Please copy .env.example to .env first';
        }

        if (! empty($issues)) {
            $this->newLine();
            $this->error('Prerequisites check failed:');
            foreach ($issues as $issue) {
                $this->line('  ✗ ' . $issue);
            }
            $this->newLine();
            $this->line('Please fix these issues and run the installer again.');
            return false;
        }

        $this->info('✓ All prerequisites met!');
        $this->newLine();

        return true;
    }

    protected function setupDatabase(): void
    {
        $this->comment('━━━ Database Configuration ━━━');
        $this->newLine();

        $dbPath = database_path('database.sqlite');

        if (File::exists($dbPath)) {
            $this->info('SQLite database already exists at: ' . $dbPath);
            if ($this->confirm('Do you want to recreate it? (This will delete all existing data)', false)) {
                File::delete($dbPath);
                File::put($dbPath, '');
                $this->info('Database recreated.');
            }
        } else {
            File::put($dbPath, '');
            $this->info('✓ Created SQLite database at: ' . $dbPath);
        }

        $this->newLine();
    }

    protected function setupReverb(): void
    {
        $this->comment('━━━ WebSocket Configuration (Reverb) ━━━');
        $this->newLine();

        $this->info('Reverb powers real-time features (player updates, track changes).');
        $this->newLine();

        $reverbAppKey = $this->generateSecureKey(16);
        $reverbAppSecret = $this->generateSecureKey(32);

        $this->info('Generated Reverb keys:');
        $this->line('  REVERB_APP_KEY: ' . $reverbAppKey);
        $this->line('  REVERB_APP_SECRET: ' . $reverbAppSecret);

        $this->updateEnvFile('REVERB_APP_KEY', $reverbAppKey);
        $this->updateEnvFile('REVERB_APP_SECRET', $reverbAppSecret);

        $reverbHost = $this->ask('Reverb host', 'localhost');
        $this->updateEnvFile('REVERB_HOST', $reverbHost);

        $reverbPort = $this->ask('Reverb port', '8080');
        $this->updateEnvFile('REVERB_PORT', $reverbPort);

        $this->info('✓ Reverb configured!');
        $this->newLine();
    }

    protected function setupWreckfestApi(): void
    {
        $this->comment('━━━ Wreckfest Controller API ━━━');
        $this->newLine();

        $this->info('WreckfestWeb needs to communicate with the WreckfestController C# service.');
        $this->line('Repository: https://github.com/tkprocat/WreckfestController');
        $this->newLine();

        $apiUrl = $this->ask('WreckfestController API URL', 'https://localhost:5101/api');
        $this->updateEnvFile('WRECKFEST_API_URL', $apiUrl);

        $this->info('✓ Wreckfest API configured!');
        $this->newLine();
    }

    protected function setupOptionalFeatures(): void
    {
        $this->comment('━━━ Optional Features ━━━');
        $this->newLine();

        // AI Assistant
        if ($this->confirm('Do you want to enable the AI track rotation assistant? (requires OpenAI API key)', false)) {
            $openaiKey = $this->secret('Enter your OpenAI API key');
            $this->updateEnvFile('OPENAI_API_KEY', $openaiKey);

            $aiModel = $this->choice(
                'Select AI model',
                ['gpt-4o-mini', 'gpt-4o', 'gpt-4-turbo', 'gpt-3.5-turbo'],
                0
            );
            $this->updateEnvFile('WRECKFEST_AI_MODEL', $aiModel);

            $this->info('✓ AI assistant configured!');
        } else {
            $this->info('Skipping AI assistant setup.');
        }

        $this->newLine();

        // SFTP Backups
        if ($this->confirm('Do you want to configure SFTP backups?', false)) {
            $this->updateEnvFile('BACKUP_SFTP_ENABLED', 'true');

            $sftpHost = $this->ask('SFTP host');
            $this->updateEnvFile('BACKUP_SFTP_HOST', $sftpHost);

            $sftpPort = $this->ask('SFTP port', '22');
            $this->updateEnvFile('BACKUP_SFTP_PORT', $sftpPort);

            $sftpUser = $this->ask('SFTP username');
            $this->updateEnvFile('BACKUP_SFTP_USERNAME', $sftpUser);

            $sftpRoot = $this->ask('SFTP backup directory', '/backups/wreckfest');
            $this->updateEnvFile('BACKUP_SFTP_ROOT', $sftpRoot);

            if ($this->confirm('Use password authentication? (otherwise private key)', true)) {
                $sftpPassword = $this->secret('SFTP password');
                $this->updateEnvFile('BACKUP_SFTP_PASSWORD', $sftpPassword);
            } else {
                $privateKey = $this->ask('Path to private key');
                $this->updateEnvFile('BACKUP_SFTP_PRIVATE_KEY', $privateKey);

                if ($this->confirm('Does the private key have a passphrase?', false)) {
                    $passphrase = $this->secret('Private key passphrase');
                    $this->updateEnvFile('BACKUP_SFTP_PASSPHRASE', $passphrase);
                }
            }

            $this->info('✓ SFTP backups configured!');
        } else {
            $this->updateEnvFile('BACKUP_SFTP_ENABLED', 'false');
            $this->info('Skipping SFTP backup setup.');
        }

        $this->newLine();
    }

    protected function runMigrations(): void
    {
        $this->comment('━━━ Database Migrations ━━━');
        $this->newLine();

        if ($this->confirm('Run database migrations and seeders?', true)) {
            $this->call('migrate', ['--force' => true, '--seed' => true]);
            $this->newLine();
            $this->info('✓ Database migrations completed!');
        } else {
            $this->warn('Skipping migrations. You\'ll need to run them manually later.');
        }

        $this->newLine();
    }

    protected function createAdminUser(): void
    {
        $this->comment('━━━ Admin User Creation ━━━');
        $this->newLine();

        if ($this->confirm('Create an admin user now?', true)) {
            $this->call('make:filament-user');
            $this->newLine();
            $this->info('✓ Admin user created!');
        } else {
            $this->warn('Skipping admin user creation. You\'ll need to create one manually later.');
        }

        $this->newLine();
    }

    protected function buildAssets(): void
    {
        $this->comment('━━━ Frontend Assets ━━━');
        $this->newLine();

        if ($this->confirm('Build frontend assets now? (requires npm)', true)) {
            $this->info('Building assets... This may take a minute.');
            $this->newLine();

            $exitCode = 0;
            $this->task('Installing npm dependencies', function () use (&$exitCode) {
                $exitCode = $this->executeCommand('npm install');
                return $exitCode === 0;
            });

            if ($exitCode === 0) {
                $this->task('Building assets', function () use (&$exitCode) {
                    $exitCode = $this->executeCommand('npm run build');
                    return $exitCode === 0;
                });
            }

            if ($exitCode === 0) {
                $this->info('✓ Assets built successfully!');
            } else {
                $this->error('Failed to build assets. You\'ll need to run "npm install && npm run build" manually.');
            }
        } else {
            $this->warn('Skipping asset build. You\'ll need to run "npm install && npm run build" manually.');
        }

        $this->newLine();
    }

    protected function displaySummary(): void
    {
        $this->info('
╔══════════════════════════════════════════════════════════╗
║                                                          ║
║         Installation Complete! 🎉                       ║
║                                                          ║
╚══════════════════════════════════════════════════════════╝
        ');

        $this->info('Next steps:');
        $this->newLine();

        $this->line('  1. Start the development server:');
        $this->line('     php artisan serve');
        $this->newLine();

        $this->line('  2. Start the Reverb WebSocket server (in another terminal):');
        $this->line('     php artisan reverb:start');
        $this->newLine();

        $this->line('  3. Visit your application:');
        $this->line('     http://localhost:8000');
        $this->newLine();

        $this->line('  4. Click "Login" and use the admin credentials you created');
        $this->newLine();

        $this->comment('For more information, see INSTALL.md');
    }

    protected function generateSecureKey(int $bytes = 16): string
    {
        return bin2hex(random_bytes($bytes));
    }

    protected function updateEnvFile(string $key, string $value): void
    {
        $envPath = base_path('.env');

        if (! File::exists($envPath)) {
            $this->error('.env file not found!');
            return;
        }

        $envContent = File::get($envPath);

        // Check if key exists
        if (preg_match("/^{$key}=/m", $envContent)) {
            // Update existing key
            $envContent = preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                $envContent
            );
        } else {
            // Add new key at the end
            $envContent .= "\n{$key}={$value}\n";
        }

        File::put($envPath, $envContent);
    }

    protected function executeCommand(string $command): int
    {
        // Suppress output
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $command .= ' >nul 2>&1';
        } else {
            $command .= ' >/dev/null 2>&1';
        }

        $output = [];
        $exitCode = 0;
        exec($command, $output, $exitCode);

        return $exitCode;
    }
}
