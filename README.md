# Yireo AdminCheckMaxInputVars
A Magento 2 module that reports on PHP configurations that limit POST requests to the server. When it is detected that a certain POST values exceeds the limits of a certain value, the request is blocked: the admin is redirected back (or an AJAX call gets a JSON error) with a message explaining which PHP setting to increase. This prevents saving data that PHP has silently truncated.

Only POST requests by logged-in admin users are checked. The check runs after the Magento form key (CSRF) validation.

## Where - in the Magento Admin Panel - is this useful?
- When saving a product that has many product attributes;
- When saving the Store Configuration section for Payment Methods with many payment methods listed; 

## Currently supported PHP options
- `max_input_vars`
- `max_input_nesting_level`
