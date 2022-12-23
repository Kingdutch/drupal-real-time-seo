@api @javascript
Feature: Behat is working

  Background:
    Given module node is enabled

  Scenario: We visit a page
    Given I am logged in as a user with the "Administer content" permission
    And I am on "/"
