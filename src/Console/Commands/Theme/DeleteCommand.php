<?php

namespace LaravelReady\ThemeManager\Console\Commands\Theme;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

use LaravelReady\ThemeManager\Support\ThemeSupport;
use LaravelReady\ThemeManager\Services\ThemeManager;

class DeleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme-manager:destroy {theme}';

    /**
     * Delete selected theme
     *
     * @var string
     */
    protected $description = 'Delete selected theme';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->askTheme();
    }

    private function askTheme()
    {
        $themeName = $this->argument('theme');

        if (!$themeName) {
            $themeName = $this->ask('Theme group:theme');

            if (!$themeName) {
                return $this->error('Please enter group:theme');
            }
        }

        ThemeSupport::splitGroupTheme($themeName, $group, $theme);

        if ($theme && $group) {
            if (ThemeManager::getTheme($theme, $group)) {
                if ($this->askConfirmation()) {
                    $result = ThemeManager::deleteTheme($theme, $group);

                    if ($result) {
                        return $this->info("Theme \"{$group}:{$theme}\" deleted.");
                    } else {
                        return $this->info('Theme could not deleted.');
                    }
                }
            }

            return $this->error("Requested theme \"{$group}:{$theme}\" not found.");
        }

        return $this->error('Please enter valid theme');
    }

    private function askConfirmation()
    {
        $confirmation = $this->ask('This theme will be delete. Are you sure? (yes/no)');

        if ($confirmation !== null) {
            if ($confirmation == 'yes') {
                return true;
            } else {
                $this->warn('Canceled theme delete operation.');
            }

            return false;
        } else {
            return $this->askConfirmation();
        }
    }
}
