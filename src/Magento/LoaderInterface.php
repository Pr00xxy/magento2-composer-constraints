<?php
/*
 * Copyright © Lybe Sweden AB 2021
 */

declare(strict_types=1);


namespace PrOOxxy\MagentoComposerConstraints\Magento;


use Composer\Package\PackageInterface;

interface LoaderInterface
{
    /**
     * @return PackageInterface[]
     */
    public function load(): array;
}
