# Yireo AdminCheckMaxInputVars

<!-- badges.specs.start -->
<div class="github-specs-badges">

![Magento version](https://img.shields.io/badge/Magento-2.4.6%20%7C%202.4.9-orange)
![PHP version](https://img.shields.io/badge/PHP-8.2%E2%80%938.5-777BB4)
![License](https://img.shields.io/badge/License-OSL--3.0-blue)
![Latest Version](https://img.shields.io/packagist/v/yireo/admin-check-max-input-vars)

</div>
<!-- badges.specs.end -->

A Magento 2 module that reports on PHP configurations that limit POST requests to the server. When it is detected that a certain POST values exceeds the limits of a certain value, the request is blocked: the admin is redirected back (or an AJAX call gets a JSON error) with a message explaining which PHP setting to increase. This prevents saving data that PHP has silently truncated.

Only POST requests by logged-in admin users are checked. The check runs after the Magento form key (CSRF) validation.

## Where - in the Magento Admin Panel - is this useful?
- When saving a product that has many product attributes;
- When saving the Store Configuration section for Payment Methods with many payment methods listed; 

## Currently supported PHP options
- `max_input_vars`
- `max_input_nesting_level`

## Current status

<!-- badges.test.start -->
<div class="github-pipeline-badges">

![Static Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_AdminCheckMaxInputVars/static-tests.yml?label=static-tests)
![Unit Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_AdminCheckMaxInputVars/unit-tests.yml?label=unit-tests)
![Integration Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_AdminCheckMaxInputVars/integration-tests.yml?label=integration-tests)
![Playwright](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_AdminCheckMaxInputVars/playwright.yml?label=playwright)
![DI Compilation](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_AdminCheckMaxInputVars/compile.yml?label=compile)

</div>
<!-- badges.test.end -->
