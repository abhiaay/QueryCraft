# Laravel Filter and Sort Query
Simple filter & sort for your laravel `model` only with minimal setup

## Prerequisites
- PHP 8.1
- Laravel 10

## Install
### Composer
```bash
composer require abhiaay/query-craft
```
### How To Use

Inside your Model `App\Models\Post`
```php
<?php

namespace App\Models;

use Abhiaay\QueryCraft\QueryCraft;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Post extends Model
{
    use HasFactory, QueryCraft;

    /**
     * @inheritdoc
     *
     * @return [alias => db_column]
     */
    public function filterableColumns(): array
    {
        return [
            'title' => 'title',
            // for relation or embed for mongodb
            'category' => 'category.id'
        ];
    }

    /**
     * @inheritdoc
     */
    public function sortableColumns(): array
    {
        return [
            'created_at' => 'created_at',
            // for relation or embed for mongodb
            'category' => 'category.name'
        ];
    }
}

```

Inside Your Controller or Query `App\Http\Controllers\PostController`
```php
<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Abhiaay\QueryCraft\Craft;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $craft = Craft::parse($request);
        return Post::filter($craft)->paginate($request->input('per_page'));
    }
}
```
#### Using Filter
in your url will looks like this
```url
https://domain.com/api/posts?filter[title][is]=how+do+i+use+syntax+highlighting+in+php+within+a+markdown+github+gist&filter[category][!is]=code
```

if value is array when you using `in`
```
https://domain.com/api/posts?filter[category][in][]=code&filter[category][in][]=programming
```

#### Using Sort
```
https://domain.com/api/posts?sort=created_at,-category
```
use symbol `-` indicate if column is `descending` without symbol indicate `ascending`
## List of Operations Supported
- `is` equal to `=`
- `!is` equal to `<>`
- `like` equal to `like`
- `!like` equal to `not like`
- `gt` equal to `>`
- `gte` equal to `>=`
- `lt` equal to `<`
- `lte` equal to `<=`
- `mod` equal to `mod`
- `regex` equal to `regexp`
- `exists` equal to `exists`
- `type` equal to `type`
- `in` equal to `whereIn`
- `!in` equal to `whereNotIn`
- `between` equal to `whereBetween`
