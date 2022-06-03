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

[//]: # (## Glossary)

[//]: # (- Идентификатор кеша - Используется для получения всех записей, сохраненных с одним идентификатором.)

[//]: # (- Теги кеша - Массив строк, идентифицирующих отдельные записи одного типа кеша. Используются для частичного очищения кеша.)

## Сохранение в кеш
`saveToCache(string $index, array $data, array $cacheTags = []): void`

- `string $index` - Идентификатор кеша. Используется для получения всех записей, сохраненных с одним идентификатором.
- `array $data` - Данные для сохранения.
- `string[] $cacheTags` - Теги кеша.

## Получение данных из кеша
`getCached(string $index, callable $data, array $cacheTags = []): array`

- `string $index` - Идентификатор кеша. Используется при получения данных.
- `callable $data` - [Анонимная функция](https://www.php.net/manual/ru/functions.anonymous.php), возвращает **массив** данных для сохранения.
- `string[] $cacheTags` - Теги кеша

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
- `string[] $cacheTags` - Теги кеша

### Доступные режимы:
- `all` (default) => remove all cache entries ($tags is not used)
- `old` => remove too old cache entries ($tags is not used)
- `matchingTag` => remove cache entries matching all given tags ($tags can be an array of strings or a single string)
- `notMatchingTag` => remove cache entries not matching one of the given tags ($tags can be an array of strings or a single string)
- `matchingAnyTag` => remove cache entries matching any given tags ($tags can be an array of strings or a single string)

## Заметки. Как работает кеш в Magento 2
Базовый класс для работы с кешем в Magento 2 - `Magento\Framework\Cache\Core` наследуется от `\Zend_Cache_Core`

@todo
