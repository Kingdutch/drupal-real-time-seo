<?php
declare(strict_types=1);

namespace Drupal\Tests\yoast_seo\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Drupal\DrupalExtension\Context\DrushContext;

class Setup implements Context {

  private DrushContext $drushContext;

  /**
   * @BeforeScenario
   */
  public function installDrupal(BeforeScenarioScope $scope) : void {
    $environment = $scope->getEnvironment();
    assert($environment instanceof InitializedContextEnvironment);
    $drushContext = $environment->getContext(DrushContext::class);
    assert($drushContext instanceof DrushContext);
    $this->drushContext = $drushContext;

    // Reset the config cache in our test runner since it may contain config
    // from a previous scenario.
    // @phpstan-ignore-next-line
    \Drupal::configFactory()->reset();

    // Install a fresh site for testing.
    $this->drushContext->assertDrushCommandWithArgument("site-install", "-y testing --site-name='Automated Behat Tests for Real-Time SEO' --account-name=admin --account-pass=admin --account-mail=admin@example.com");

    // We must enable the Olivero theme with block module so that we have a
    // logout link which is what is needed for DrupalContext to know whether
    // login succeeded.
    $this->assertModuleEnabled("block");
    $this->drushContext->assertDrushCommandWithArgument("theme:install", "-y olivero");
    $this->drushContext->assertDrushCommandWithArgument("config:set", "-y system.theme default olivero");

    // We always want to enable the module we're testing so it doesn't need to
    // be repeated in every feature file.
    $this->assertModuleEnabled("yoast_seo");
  }

  /**
   * Ensures a given module is enabled.
   *
   * @param string $module
   *   The module to enable.
   *
   * @Given module :module is enabled
   */
  public function assertModuleEnabled(string $module) : void {
    $this->drushContext->assertDrushCommandWithArgument("pm:install", "-y $module");
  }

}
