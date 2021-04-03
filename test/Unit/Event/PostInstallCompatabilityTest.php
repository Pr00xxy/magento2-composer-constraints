<?php
/**
 * Copyright © Hampus Westman 2021
 * See LICENCE provided with this module for licence details
 *
 * @author     Hampus Westman <hampus.westman@gmail.com>
 * @copyright  Copyright (c) 2021 Hampus Westman
 * @license    MIT License https://opensource.org/licenses/MIT
 * @link       https://github.com/Pr00xxy
 */

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
