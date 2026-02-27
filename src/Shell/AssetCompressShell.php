<?php
namespace App\Shell;

use AssetCompress\Shell\AssetCompressShell as BaseAssetCompressShell;

class AssetCompressShell extends BaseAssetCompressShell
{
    protected function _clearBuilds()
    {
        parent::_clearBuilds();
        $themes = (array)$this->config->general('themes');
        $assets = $this->factory->assetCollection();
        $targets = array_map(
            fn($target) => $target->name(),
            iterator_to_array($assets)
        );

        $this->_clearBuildsTatoebaSpecific($themes, $targets);
    }

    private function _clearBuildsTatoebaSpecific($themes, $targets) {
        array_push($targets, ...array_map(fn($target) => "$target.gz", $targets));
        $this->_clearPath($this->config->cachePath('svg'), $themes, $targets);
    }
}
