<?php

declare(strict_types=1);

namespace PrOOxxy\MagentoComposerConstraints\Test\Unit\Event;

use PHPUnit\Framework\TestCase;
use PrOOxxy\MagentoComposerConstraints\Event\PostInstallCompatability;

class PostInstallCompatabilityTest extends TestCase
{

    private $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new PostInstallCompatability();
    }

    /**
     * @test
     */
    public function shouldListenToInstallEvents()
    {
        $result = $this->sut::getSubscribedEvents();

        self::assertArrayHasKey('post-install-cmd', $result);
    }
}
