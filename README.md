## Guentur_CacheWrapper

## Описание методов интерфейса App\CacheInterface
`getCached(string $index, callable $data, array $cacheTags = []): array` - достает из кеша данные по индексу (`$index`), если в кеш по переданному индексу пуст,
тогда `getCached()` сохраняет данные, возвращаемые [анонимной функцией](https://www.php.net/manual/ru/functions.anonymous.php) `$data`.

`array $cacheTags` массив строк, идентифицирующих область влияния кеша. Используются для очищения кеша.

............... @todo Привести пример очищения кеша по тегам `array $cacheTags`

Возьмите во внимание. В стандартном функционале Magento, очистить кеш по идентификатору `$index` нет возможности,
не передав его явно на этапе сохранения кеша в массив тегов.
В даном модуле `$index` автоматически передается в массив тегов на этапе сохранения

Пример передачи аргумента `$data` данных, для кеширования.
```php
$this->cache->getCached(
            'cache_key_index',
            function () use ($greaterThan) {
                return $this->discountTierCollectionFactory->create()
                    ->addFieldToFilter(self::SOME_ATTRIBUTE, ["gt" => $greaterThan])
                    ->setOrder(self::DISCOUNT_MIN_QTY, Collection::SORT_ORDER_ASC)
                    ->getFirstItem()
                    ->getData();
            }
        );
```
Функция будет вызвана только если данные не найдены в кеше. Использование анонимной функции для передачи данных можно назвать оптимизацией.

`getDataFromCache(string $index): ?array`

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

.......... @todo Сохранить в кеше сериализированный
- обьект,
- строку

Чтобы проверить возвращаемый тип из `getDataFromCache(string $index)`,
всегда ли он возвращает массив или null. Возможно у него возвращаемый тип mixed. Хорошо ли когда функция возвращает mixed? Может быть нужно преобразововать любой возвращаемый тип (кроме null) в массив или обьект?

.................

## Заметки. Как работает кеш в Magento 2
Базовый класс для работы с кешем в Magento 2 - `Magento\Framework\Cache\Core` наследуется от  `\Zend_Cache_Core`