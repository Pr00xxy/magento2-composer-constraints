<?php
declare(strict_types=1);

namespace PrOOxxy\MagentoComposerConstraints\Magento;

use Composer\Composer;
use Composer\Package\Link;
use Composer\Package\PackageInterface;
use Composer\Repository\PlatformRepository;
use Composer\Semver\Semver;
use InvalidArgumentException;

class ConstraintValidator
{

    /**
     * @var array
     */
    private $packages;

    /**
     * @var PlatformRepository
     */
    private $platformRepo;

    /**
     * @var PackageInterface[]
     */
    private $customPackages;

    public function __construct(
        Composer $composer,
        array $vendorPackages,
        array $customPackages
    ) {

        foreach ($vendorPackages as $package) {
            if (!$package instanceof PackageInterface) {
                throw new InvalidArgumentException();
            }
        }

        foreach ($vendorPackages as $vendorPackage) {
            $this->packages[$vendorPackage->getName()] = $vendorPackage;
        }

        $platformOverrides = $composer->getConfig()->get('platform') ?: array();

        $this->platformRepo = new PlatformRepository(array(), $platformOverrides);
        $this->customPackages = $customPackages;
    }

    /**
     * Checks if installed packages meet the provided package requirements
     *
     * Returns array of violation messages if there are any.
     * If satisfies - returns empty array
     *
     * @return array
     */
    public function satisfies(PackageInterface $package): array
    {
        $requires = $package->getRequires();

        return $this->processLinks($requires);
    }

    private function getPackage(Link $link): ?PackageInterface
    {

        foreach ($this->customPackages as $package) {
            if ($package->getName() === $link->getTarget()) {
                return $package;
            }
        }

        $platformPackages = $this->platformRepo->getPackages();
        foreach ($platformPackages as $package){
            if ($package->getName() === $link->getTarget()) {
                return $package;
            }
        }

        return $this->packages[$link->getTarget()] ?? null;
    }

    /**
     * @param $links Link[]
     * @return array
     */
    private function processLinks(array $links): array
    {
        $violations = [];

        foreach ($links as $link) {
            $package = $this->getPackage($link);
            if ($package === null) {
                continue;
            }
            if (Semver::satisfies($package->getVersion(), $link->getConstraint()->getPrettyString())) {
                continue;
            }
            $template = ' - <error>%s</error>, Installed: <comment>[%s]</comment> does not satisfy constraint <comment>%s</comment>';
            $violations[] = sprintf($template, $package->getName(), $package->getVersion(), $link->getConstraint());
        }
        return $violations;
    }

}
