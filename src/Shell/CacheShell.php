<?php
namespace App\Shell;

use Cake\Cache\Cache;

/**
 * Wrapper around the Cache Shell.
 *
 * We disable cache for CLI tools in config/bootstrap_cli.php to avoid file
 * permission problems, however running "cake cache clear" silently has
 * no effect when cache is disabled, which is very confusing.
 * This wrapper makes sure cache is enabled just when we want to clear it.
 */
class CacheShell extends \Cake\Shell\CacheShell
{
    public function clear($prefix = null)
    {
        if (Cache::enabled()) {
            parent::clear($prefix);
        } else {
            Cache::enable();
            parent::clear($prefix);
            Cache::disable();
        }
    }
}
