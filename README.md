# Guentur_CacheWrapper

## Установка
`composer require guentur/cache-wrapper`

`bin/magento module:enable Guentur_CacheWrapper`

## Приступая к использованию
Для использования функционала модуля имплементируйте в конструктор таким образом:
```php
    private $cache;

    public function __construct(
        \Guentur\CacheWrapper\App\CacheInterface $cache
    ) {
        $this->cache = $cache;
    }
```

Также модуль переопределяет preference для `Magento\Framework\App\CacheInterface` 
(смотрите в Guentur/CacheWrapperetc/di.xml), так что методы модуля можно вызывать из стандартного интерфейса.

## Glossary
- Идентификатор кеша - Уникален и используется для получения одной конкретной записи. 
Не должны сохраняться несколько записей под одним идентификатором.

- Теги кеша — это способ классификации записей кэша. Когда вы сохраняете кеш, вы можете установить массив тегов, 
которые будут применяться к этой записи. Затем вы сможете очистить все записи кеша, 
помеченные данным тегом (или тегами). Теги могут повторяться в разных *записях* кеша. 

Чтобы указать к какому [типу кеша](https://devdocs.magento.com/guides/v2.4/config-guide/cli/config-cli-subcommands-cache.html#config-cli-subcommands-cache-clean-over) 
ваша запись пренадлежит - передайте при её сохранении тег, который определен в 
[классе типа кеша](https://developer.adobe.com/commerce/php/development/cache/partial/cache-type/)

### Пример:
Находим класс кеша по его идентификатору в конфигурации `cache.xml`:
```xml
...
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Cache/etc/cache.xsd">
    <type name="full_page" translate="label,description" instance="Magento\PageCache\Model\Cache\Type">
        <label>Page Cache</label>
        <description>Full page caching</description>
    </type>
</config>
```
Передаем тег из класса типа:
```php
return $this->cache->getCached(
            'some_cache_key',
            function () {
                ...
                return $data;
            },
            [\Magento\PageCache\Model\Cache\Type::CACHE_TAG],
            86400
        );
```

## Сохранение в кеш
`saveToCache(string $index, array $data, array $cacheTags = [], $lifeTime = null): void`

- `string $index` - [Идентификатор кеша](#glossary). Используется для получения всех записей, сохраненных с одним идентификатором.
- `array $data` - Данные для сохранения.
- `string[] $cacheTags` - [Теги кеша](#glossary).
- `int $lifeTime = null` - Время жизни кеша.

## Получение данных из кеша
`getCached(string $index, callable $data, array $cacheTags = [], $lifeTime = null): array`

- `string $index` - [Идентификатор кеша](#glossary). Используется при получения данных.
- `callable $data` - [Анонимная функция](https://www.php.net/manual/ru/functions.anonymous.php), возвращает **массив** данных для сохранения.
- `string[] $cacheTags` - [Теги кеша](#glossary).
- `int $lifeTime = null` - Время жизни кеша.

### Если не не удалось достать данные из кеша:
Вызывается `callable $data` для получения данных, которые должны быть в кеше.
Вызывается метод [`saveToCache()`](#cохранение-в-кеш) для сохранения данных. В нее передаются: `$index`; данные, возвращенные `callable $data`; `$cacheTags`

### Пример использования `getCached()`
```php
$this->cache->getCached(
            'cache_key_index',
            function () use ($greaterThan) {
                return $this->discountTierCollectionFactory->create()
                    ->addFieldToFilter(self::DISCOUNT_PRODUCT_TYPE, $productTypeId)
                    ->addFieldToFilter(self::SOME_ATTRIBUTE, ["gt" => $greaterThan])
                    ->setOrder(self::DISCOUNT_MIN_QTY, Collection::SORT_ORDER_ASC)
                    ->getFirstItem()
                    ->getData();
            },
            ['cache_key_index' . $greaterThan]
        );
```
> Использование анонимной функции для передачи данных можно назвать оптимизацией.

## Очщение кеша
`cleanWithMode(string $mode, array $tags = []): bool`

- `string $mode` - Режим удаления кеша. Подробнее можно прочитать в [документации Zend_Cache](https://framework.zend.com/manual/1.10/en/zend.cache.theory.html)
- `string[] $cacheTags` - [Теги кеша](#glossary).

### Доступные режимы:
- `all` (default) => remove all cache entries ($tags is not used)
- `old` => remove too old cache entries ($tags is not used)
- `matchingTag` => remove cache entries matching all given tags ($tags can be an array of strings or a single string)
- `notMatchingTag` => remove cache entries not matching one of the given tags ($tags can be an array of strings or a single string)
- `matchingAnyTag` => remove cache entries matching any given tags ($tags can be an array of strings or a single string)

## Заметки. Как работает кеш в Magento 2
Базовый класс для работы с кешем в Magento 2 - `Magento\Framework\Cache\Core` наследуется от `\Zend_Cache_Core`

@todo
