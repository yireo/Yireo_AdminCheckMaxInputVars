# Yireo AdminCheckMaxInputVars
A Magento 2 module that reports on PHP configurations that limit POST requests to the server. When it is detected that a certain POST values exceeds the limits of a certain value, a friendly warning is generated.

## Where - in the Magento Admin Panel - is this useful?
- When saving a product that has many product attributes;
- When saving the Store Configuration section for Payment Methods with many payment methods listed; 

## Currently supported PHP options
- `max_input_vars`
- `max_input_nesting_level`
