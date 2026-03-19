<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Russian strings.
 *
 * @package    mod_iearchy
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Iearchy';
$string['modulename'] = 'Iearchy';
$string['modulenameplural'] = 'Iearchy';
$string['pluginadministration'] = 'Администрирование Iearchy';
$string['privacy:metadata'] = 'Модуль iearchy хранит организационный контент внутри экземпляра элемента курса и не хранит персональные данные пользователей Moodle.';

$string['iearchy:addinstance'] = 'Добавлять новый iearchy';
$string['iearchy:view'] = 'Просматривать iearchy';
$string['iearchy:managecontent'] = 'Управлять содержимым iearchy';

$string['defaultheadereyebrow'] = 'Наша организация';
$string['defaultheadertitle'] = 'Команда';
$string['headereyebrow'] = 'Малый заголовок';
$string['headertitle'] = 'Основной заголовок';

$string['directory'] = 'Каталог';
$string['viewdirectory'] = 'Открыть каталог';
$string['managelevels'] = 'Управление уровнями';
$string['manageemployees'] = 'Управление сотрудниками';
$string['backtodirectory'] = 'Вернуться к каталогу';
$string['backtolevels'] = 'Вернуться к уровням';
$string['backtoemployees'] = 'Вернуться к сотрудникам';

$string['addlevel'] = 'Добавить уровень';
$string['editlevel'] = 'Редактировать уровень';
$string['deletelevel'] = 'Удалить уровень';
$string['levelsaved'] = 'Уровень сохранён.';
$string['leveldeleted'] = 'Уровень удалён.';
$string['confirmdeletelevel'] = 'Удалить уровень "{$a}"?';
$string['levelhasemployees'] = 'Нельзя удалить уровень "{$a->title}", потому что в нём ещё есть {$a->count} сотрудник(ов).';
$string['nolevels'] = 'Пока не настроено ни одного видимого уровня.';
$string['nolevelsmanage'] = 'Уровни ещё не созданы.';
$string['nolevelsforemployee'] = 'Сначала создайте хотя бы один уровень, затем добавляйте сотрудников.';
$string['levelnumber'] = 'Уровень {$a}';

$string['addemployee'] = 'Добавить сотрудника';
$string['editemployee'] = 'Редактировать сотрудника';
$string['deleteemployee'] = 'Удалить сотрудника';
$string['employeesaved'] = 'Сотрудник сохранён.';
$string['employeedeleted'] = 'Сотрудник удалён.';
$string['confirmdeleteemployee'] = 'Удалить сотрудника "{$a}"?';
$string['noemployees'] = 'Сотрудники ещё не созданы.';

$string['eyebrow'] = 'Подзаголовок';
$string['title'] = 'Заголовок';
$string['level'] = 'Уровень';
$string['fullname'] = 'Полное имя';
$string['position'] = 'Должность';
$string['description'] = 'Описание';
$string['imageurl'] = 'URL изображения';
$string['imagefile'] = 'Загруженное изображение';
$string['initials'] = 'Инициалы';
$string['sortorder'] = 'Порядок сортировки';
$string['visible'] = 'Видимый';
$string['actions'] = 'Действия';
$string['employees'] = 'Сотрудники';
$string['imageurl_help'] = 'Используйте полный внешний URL изображения или Moodle file URL.';
$string['invalidimageurl'] = 'Укажите корректный http(s) URL или Moodle file URL (pluginfile.php/draftfile.php).';
$string['imagefile_help'] = 'Загрузите изображение из Moodle-файлов. Если файл загружен, он будет использоваться раньше внешнего URL.';
$string['initials_help'] = 'Если у сотрудника нет изображения, в плейсхолдере будут показаны эти инициалы.';
$string['missingavatarfallback'] = 'Если не задан ни загруженный файл, ни URL изображения, укажите инициалы.';
$string['invalidlevel'] = 'Пожалуйста, выберите корректный уровень.';
$string['directoryempty'] = 'Иерархия появится здесь после добавления видимых уровней и сотрудников.';
$string['openprofile'] = 'Открыть профиль';
$string['close'] = 'Закрыть';
$string['cardaria'] = '{$a->fullname}, {$a->position}';
$string['visibilityyes'] = 'Да';
$string['visibilityno'] = 'Нет';

$string['displaymode'] = 'Режим отображения';
$string['displaymode_help'] = 'Страница: участники открывают элемент курса и видят каталог (как в модуле «Страница»). Текст/медиа на курсе: HTML показывается в секции курса без ссылки на просмотр элемента (как в модуле «Метка»).';
$string['displaymode_page'] = 'Страница (как mod_page)';
$string['displaymode_inline'] = 'Текст/медиа на странице курса (как mod_label)';
$string['invaliddisplaymode'] = 'Выбран недопустимый режим отображения.';
$string['contentsection'] = 'Содержимое для страницы курса';
$string['content'] = 'Содержимое страницы';
$string['content_help'] = 'Используется в режиме «Текст/медиа на курсе» (в т.ч. встроенные файлы). В режиме «Страница» это поле не выводится в списке курса; при необходимости используйте описание выше.';
$string['dragdrophelp'] = 'Перетаскивайте элементы, чтобы менять их приоритет.';
$string['priority'] = 'Приоритет';
$string['moveup'] = 'Вверх';
$string['movedown'] = 'Вниз';
