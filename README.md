## Laravel Nova Tool Actions Package
[![Latest Version on Github](https://img.shields.io/packagist/v/awesome-nova/tool-actions.svg?style=flat)](https://packagist.org/packages/awesome-nova/tool-actions)
[![Total Downloads](https://img.shields.io/packagist/dt/awesome-nova/tool-actions.svg?style=flat)](https://packagist.org/packages/awesome-nova/tool-actions)
[![Become a Patron!](https://img.shields.io/badge/become-a_patron!-red.svg?logo=patreon&style=flat)](https://www.patreon.com/bePatron?u=16285116)

1. [Installation](#user-content-installation)
2. [Usage](#user-content-usage)

## Installation

You can install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require awesome-nova/tool-actions
```

## Usage

```php
class AddCommentAction extends ToolAction
{
    public function name()
    {
        return 'Add New Comment';
    }

    public function label()
    {
        return 'Add comment';
    }

    public function handle(ActionFields $fields)
    {
        Comment::create([
            'comment' => $fields->get('comment')
        ]);
    }

    public function fields()
    {
        return [
            Text::make("Comment")->rules('required')
        ];
    }
}
```


