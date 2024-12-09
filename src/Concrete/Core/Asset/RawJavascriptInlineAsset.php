<?php
namespace Concrete\Core\Asset;

class RawJavascriptInlineAsset extends JavascriptInlineAsset
{
    public function __toString()
    {
        return  $this->getAssetURL();
    }

    public function getAssetType()
    {
        return 'raw-javascript-inline';
    }
}
