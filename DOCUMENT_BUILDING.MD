## Создание документов СЭД

Для создания документов в системе СЭД используется фасад **SEDDocumentBuilderFacade.** Далее описаны примеры создания различных типов документов.

В качестве идентификатора родительского документа ($parent_document_id) везде используется идентификатор из общей таблицы иерархии документов (document_hierarchy_id) (см. [модуль общей иерархии](https://gitlab.bitrix.bsi.local/bitrix24/documents_hierarchy_backend)).

Метод save обязательно вызывать в конце, так как он создает документ и возвращает его.

Таблица основных методов фасада SEDDocumentBuilderFacade:

| Метод                                                              | Описание                                                                                                                                                                                                          |
| ----------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| ``directive()``                                                         | Возвращает конструктор поручения                                                                                                                                                            |
| ``esz()``                                                               | Возвращает конструктор ЭСЗ                                                                                                                                                                        |
| ``review()``                                                            | Возвращает конструктор ознакомления                                                                                                                                                      |
| ``createUser(int $user_id, bool $can_deletable = true)``                | Возвращает пользователя UserRoleAggregator для установки участников документа                                                                                       |
| ``createStaticRole(int $static_role_id, bool $can_deletable = true)``   | Возвращает статическую роль UserRoleAggregator для установки участников документа                                                                                |
| ``createDynamicRole(int $dynamic_role_id, bool $can_deletable = true)`` | Возвращает динамическую роль UserRoleAggregator для установки участников документа                                                                              |
| ``createEmpty()``                                                       | Создает произвольный документ, доступно после вызова одного из методов (directive(), esz(), review())                                                       |
| ``createByTemplate(int $template_id, ?int $parent_document_id = null)`` | Создает документ по шаблону, доступно после вызова одного из методов (directive(), esz(), review())                                                           |
| ``setTemplateId(int $template_id)``                                     | Устанавливает шаблон документа, доступно после вызова одного из методов (directive(), esz(), review())                                                    |
| ``setParentDocumentId(?int $parent_document_id)``                       | Устанавливает id родительского документа (из общей иерархии), доступно после вызова одного из методов (directive(), esz(), review()) |
| ``save()``                                                              | Сохраняет созданный документ в базе данных, доступно после вызова одного из методов (directive(), esz(), review())                               |

### Создание поручений

Создание дочернего поручения на базе шаблона:

```php
use SED\Facades\SEDDocumentBuilderFacade;

$parent_document_id = 1; /** Идентификатор родительского документа из общей иерархии документов */
$directive_template_id = 85; /** Идентификатор шаблона поручения */

$directive = SEDDocumentBuilderFacade::directive()
	->createByTemplate($directive_template_id, $parent_document_id)
	->setCreatorId(14317)
	->save();

```

Создание корневого поручения на базе шаблона:

```php
use SED\Facades\SEDDocumentBuilderFacade;

$directive = SEDDocumentBuilderFacade::directive()
	->createByTemplate(85)
	->setCreatorId(14317)
	->save();
```

Создание произвольного поручения с указанием родительского документа:

```php
use SED\Facades\SEDDocumentBuilderFacade;

$parent_document_id = 1; /** Идентификатор родительского документа из общей иерархии документов */

$directive = SEDDocumentBuilderFacade::directive()
	->createEmpty()
	->setExecutedAt(\Carbon\Carbon::now()->addDays(2))
	->setParentDocumentId($parent_document_id)
	->setThemeTitle('Тестовый тема')
	->setContent('Тестовое содержимое')
	->setCreatorId(14317)
	->setAuthor(SEDDocumentBuilderFacade::createUser(14317, false))
	->addExecutor(SEDDocumentBuilderFacade::createStaticRole(32))
	->save();
```

Таблица с методами создания поручения:

| Метод                                        | Описание                                                                                                                                                                                                                                  |
| ------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `setParentDocumentId(?int $parent_document_id)` | Устанавливает идентификатор родительского документа в иерархии документов.                                                                                                     |
| `setExecutedAt(\Carbon\Carbon $executed_at)`    | Устанавливает дату исполнения поручения.                                                                                                                                                                     |
| `setContent(string $content)`                   | Устанавливает содержимое поручения.                                                                                                                                                                               |
| `setPortfolio(string $portfolio)`               | Устанавливает описание портфеля для поручения.                                                                                                                                                          |
| `setCreatorId(int $creator_id)`                 | Устанавливает идентификатор пользователя, создающего поручение.                                                                                                                          |
| `setAuthor(UserRoleAggregator $author)`         | Устанавливает автора поручения с использованием объекта UserRoleAggregator.                                                                                                                     |
| `clearExecutors()`                              | Очищает список исполнителей поручения.                                                                                                                                                                          |
| `addExecutor(UserRoleAggregator $executor)`     | Добавляет исполнителя в список исполнителей поручения.                                                                                                                                            |
| `clearControllers()`                            | Очищает список контролеров поручения.                                                                                                                                                                            |
| `addController(UserRoleAggregator $controller)` | Добавляет контролера в список контролеров поручения.                                                                                                                                                |
| `clearObservers()`                              | Очищает список наблюдателей поручения.                                                                                                                                                                          |
| `addObserver(UserRoleAggregator $observer)`     | Добавляет наблюдателя в список наблюдателей поручения.                                                                                                                                            |
| `setThemeTitle(string $theme_title)`            | Устанавливает заголовок темы поручения.                                                                                                                                                                        |
| `save()`                                        | Валидирует данные и создает новое поручение. Возвращает объект модели Directive. Выбрасывает исключение LogicException при невалидных данных. |

### Создание ЭСЗ

Создание дочернего ЭСЗ на базе шаблона:

```php
use SED\Facades\SEDDocumentBuilderFacade;

$parent_document_id = 1; /** Идентификатор родительского документа из общей иерархии документов */
$esz_template_id = 86; /** Идентификатор шаблона ЭСЗ */

$esz2 = SEDDocumentBuilderFacade::esz()
	->createByTemplate($esz_template_id, $parent_document_id)
	->setInitiatorId(14317)
	->addReceiver(SEDDocumentBuilderFacade::createUser(14317, false))
	->save();

```

Создание произвольного ЭСЗ:

```php
use SED\Facades\SEDDocumentBuilderFacade;

$parent_document_id = 1; /** Идентификатор родительского документа из общей иерархии документов */

$esz = SEDDocumentBuilderFacade::esz()
	->createEmpty()
	->setParentDocumentId($parent_document_id)
	->setContent('Тестовое содержимое')
	->setThemeTitle('Тестовая тема')
	->setInitiatorId(14317)
	->setSignatory(SEDDocumentBuilderFacade::createUser(14317, false))
	->save();
```

Таблица с методами создания ЭСЗ:

| Метод                                        | Описание                                                                                                                                                                             |
| ------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `setParentDocumentId(?int $parent_document_id)` | Устанавливает идентификатор родительского документа в иерархии документов.                                                |
| `setInitiatorId(int $user_id)`                  | Устанавливает идентификатор пользователя-инициатора документа.                                                                      |
| `setContent(string $content)`                   | Устанавливает содержимое ЭСЗ.                                                                                                                                     |
| `setPortfolio(string $portfolio)`               | Устанавливает описание портфеля для ЭСЗ.                                                                                                                  |
| `setSignatory(UserRoleAggregator $signatory)`   | Устанавливает подписанта документа с использованием объекта UserRoleAggregator.                                                        |
| `clearReceivers()`                              | Очищает список адресатов ЭСЗ.                                                                                                                                       |
| `addReceiver(UserRoleAggregator $receiver)`     | Добавляет адреса в список адресатов ЭСЗ.                                                                                                                  |
| `clearObservers()`                              | Очищает список наблюдателей ЭСЗ.                                                                                                                                 |
| `addObserver(UserRoleAggregator $observer)`     | Добавляет наблюдателя в список наблюдателей документа.                                                                                       |
| `setThemeTitle(string $theme_title)`            | Устанавливает заголовок темы ЭСЗ.                                                                                                                               |
| `save()`                                        | Валидирует данные и создает новый документ ЭСЗ. Выбрасывает исключение LogicException при невалидных данных. |

### Создание ознакомлений

Создание цепочки документов Поручение -> ЭСЗ -> Ознакомление:

При создании цепочки документов обязательно использовать транзакции, чтобы не допустить создания документов в случае  возникновения критической ошибки.

```php
use SED\Facades\SEDDocumentBuilderFacade;

\DB::transaction(function () {
	$directive = SEDDocumentBuilderFacade::directive()
		->createByTemplate(85)
		->setCreatorId(14317)
		->setAuthor(SEDDocumentBuilderFacade::createUser(14317))
		->addController(SEDDocumentBuilderFacade::createDynamicRole(2, false))
		->addObserver(SEDDocumentBuilderFacade::createStaticRole(35))
		->setContent('Тестовый документ')
		->setPortfolio('Тестовый портфолио')
		->save();

	$esz = SEDDocumentBuilderFacade::esz()
		->createByTemplate(90, $directive->document_hierarchy_id)
		->setInitiatorId(14317)
		->addReceiver(SEDDocumentBuilderFacade::createUser(14317))
		->save();

	$review = SEDDocumentBuilderFacade::review()
		->createEmpty()
		->setParentDocumentId($esz->document_hierarchy_id)
		->setThemeTitle('Тестовый тема')
		->setInitiatorId(14317)
		->setContent('Тестовый отзыв')
		->setPortfolio('Тестовый портфолио')
		->addReceiver(SEDDocumentBuilderFacade::createUser(14317, false))
		->save();
});
```

Таблица с методами создания ознакомления:

| Метод                                        | Описание                                                                                                                                                                                               |
| ------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `setParentDocumentId(?int $parent_document_id)` | Устанавливает идентификатор родительского документа в иерархии документов.                                                                  |
| `setInitiatorId(int $user_id)`                  | Устанавливает идентификатор пользователя-инициатора ознакомления.                                                                                  |
| `setContent(string $content)`                   | Устанавливает содержимое ознакомления.                                                                                                                                     |
| `setPortfolio(string $portfolio)`               | Устанавливает описание портфеля для ознакомления.                                                                                                                 |
| `clearReceivers()`                              | Очищает список получателей ознакомления.                                                                                                                                   |
| `addReceiver(UserRoleAggregator $receiver)`     | Добавляет получателя в список получателей ознакомления.                                                                                                       |
| `setThemeTitle(?string $theme_title)`           | Устанавливает заголовок темы ознакомления.                                                                                                                               |
| `save()`                                        | Валидирует данные и создает новый документ ознакомления. Выбрасывает исключение LogicException при невалидных данных. |
