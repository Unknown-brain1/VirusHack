# virushack-back

## How to start
Start from 'composer update' in root folder

## Modules
Modules in folders in root. 
Example: ./evgeny ./dan

### How to make module
Create folder in root.
In you folder make loader.php, inside use

`require_once $_SERVER['DOCUMENT_ROOT'] . '/main_loader.php';`

After require_once all you files.

Loaders **MUST NOT** to do any output

### Classes and namespaces
Class holds in ./{module_name}/app/

Namespace is {module_name}


## API 

In ./api.php declared all methods and params, that can be called from web