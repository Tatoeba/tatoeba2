<?php
declare(strict_types=1);

namespace App\Command;

use AssetCompress\Command\ClearCommand as BaseAssetCompressClearCommand;
use AssetCompress\Factory;
use Cake\Console\ConsoleIo;
use MiniAsset\AssetConfig;

/**
 * Wrapper around AssetCompress plugin's clear command
 * to additionally remove compressed .gz files produced
 * by the GzipFilter filter.
 */
class AssetCompressClearCommand extends BaseAssetCompressClearCommand
{
    public static function defaultName(): string
    {
        return 'asset_compress clear';
    }

    protected function clearBuilds(AssetConfig $config, Factory $factory, ConsoleIo $io): void
    {
        parent::clearBuilds($config, $factory, $io);

        $this->_clearBuildsTatoebaSpecific($io, $config, $factory->assetCollection());
    }

    private function _clearBuildsTatoebaSpecific($io, $config, $assets)
    {
        $themes = (array)$config->general('themes');
        foreach ($assets as $asset) {
            if (in_array('GzipFilter', $asset->filterNames())) {
                $path = dirname($asset->path()) . DS;
                $targets = [$asset->name() . ".gz"];
                $this->clearPath($io, $path, $themes, $targets);
            }
        }
    }
}
