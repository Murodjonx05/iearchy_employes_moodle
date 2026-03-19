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
 * Uzbek strings.
 *
 * @package    mod_iearchy
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Iearchy';
$string['modulename'] = 'Iearchy';
$string['modulenameplural'] = 'Iearchy';
$string['pluginadministration'] = 'Iearchy boshqaruvi';
$string['privacy:metadata'] = 'Iearchy moduli kurs elementi nusxasi ichida tashkiliy kontentni saqlaydi va Moodle foydalanuvchilarining shaxsiy maʼlumotlarini saqlamaydi.';

$string['iearchy:addinstance'] = 'Yangi iearchy qo‘shish';
$string['iearchy:view'] = 'Iearchy ni ko‘rish';
$string['iearchy:managecontent'] = 'Iearchy tarkibini boshqarish';

$string['defaultheadereyebrow'] = 'Bizning tashkilot';
$string['defaultheadertitle'] = 'Jamoa';
$string['headereyebrow'] = 'Yuqori kichik sarlavha';
$string['headertitle'] = 'Asosiy sarlavha';

$string['directory'] = 'Katalog';
$string['viewdirectory'] = 'Katalogni ko‘rish';
$string['managelevels'] = 'Darajalarni boshqarish';
$string['manageemployees'] = 'Xodimlarni boshqarish';
$string['backtodirectory'] = 'Katalogga qaytish';
$string['backtolevels'] = 'Darajalarga qaytish';
$string['backtoemployees'] = 'Xodimlarga qaytish';

$string['addlevel'] = 'Daraja qo‘shish';
$string['editlevel'] = 'Darajani tahrirlash';
$string['deletelevel'] = 'Darajani o‘chirish';
$string['levelsaved'] = 'Daraja saqlandi.';
$string['leveldeleted'] = 'Daraja o‘chirildi.';
$string['confirmdeletelevel'] = '"{$a}" darajasi o‘chirilsinmi?';
$string['levelhasemployees'] = '"{$a->title}" darajasini o‘chirib bo‘lmaydi, chunki unda hali {$a->count} ta xodim mavjud.';
$string['nolevels'] = 'Hozircha ko‘rinadigan darajalar sozlanmagan.';
$string['nolevelsmanage'] = 'Darajalar hali yaratilmagan.';
$string['nolevelsforemployee'] = 'Avval kamida bitta daraja yarating, keyin xodim qo‘shing.';
$string['levelnumber'] = '{$a}-daraja';

$string['addemployee'] = 'Xodim qo‘shish';
$string['editemployee'] = 'Xodimni tahrirlash';
$string['deleteemployee'] = 'Xodimni o‘chirish';
$string['employeesaved'] = 'Xodim saqlandi.';
$string['employeedeleted'] = 'Xodim o‘chirildi.';
$string['confirmdeleteemployee'] = '"{$a}" xodimi o‘chirilsinmi?';
$string['noemployees'] = 'Xodimlar hali yaratilmagan.';

$string['eyebrow'] = 'Kichik sarlavha';
$string['title'] = 'Sarlavha';
$string['level'] = 'Daraja';
$string['fullname'] = 'To‘liq ism';
$string['position'] = 'Lavozim';
$string['description'] = 'Tavsif';
$string['imageurl'] = 'Rasm URL';
$string['imagefile'] = 'Yuklangan rasm';
$string['initials'] = 'Initsiallar';
$string['sortorder'] = 'Saralash tartibi';
$string['visible'] = 'Ko‘rinadigan';
$string['actions'] = 'Amallar';
$string['employees'] = 'Xodimlar';
$string['imageurl_help'] = 'To‘liq tashqi rasm URL yoki Moodle file URL manzilidan foydalaning.';
$string['invalidimageurl'] = 'Iltimos, to‘g‘ri http(s) URL yoki Moodle file URL (pluginfile.php/draftfile.php) kiriting.';
$string['imagefile_help'] = 'Moodle fayllari orqali rasm yuklang. Agar fayl yuklangan bo‘lsa, u tashqi URL dan ustun turadi.';
$string['initials_help'] = 'Agar xodimda rasm bo‘lmasa, placeholder ichida shu initsiallar ko‘rsatiladi.';
$string['missingavatarfallback'] = 'Agar yuklangan rasm ham, rasm URL ham ko‘rsatilmagan bo‘lsa, initsial kiriting.';
$string['invalidlevel'] = 'Iltimos, to‘g‘ri darajani tanlang.';
$string['directoryempty'] = 'Ko‘rinadigan darajalar va xodimlar qo‘shilgandan keyin ierarxiya shu yerda ko‘rinadi.';
$string['openprofile'] = 'Profilni ochish';
$string['close'] = 'Yopish';
$string['cardaria'] = '{$a->fullname}, {$a->position}';
$string['visibilityyes'] = 'Ha';
$string['visibilityno'] = 'Yo‘q';

$string['displaymode'] = 'Ko‘rinish rejimi';
$string['displaymode_help'] = 'Sahifa: ishtirokchilar faollikni ochib katalogni ko‘radi (mod_page kabi). Kursdagi matn/media: HTML kurs bo‘limida faollik havolisiz ko‘rsatiladi (mod_label kabi).';
$string['displaymode_page'] = 'Sahifa (mod_page kabi)';
$string['displaymode_inline'] = 'Kurs sahifasida matn/media (mod_label kabi)';
$string['invaliddisplaymode'] = 'Noto‘g‘ri ko‘rinish rejimi tanlangan.';
$string['contentsection'] = 'Kurs sahifasi uchun kontent';
$string['content'] = 'Sahifa kontenti';
$string['content_help'] = '«Kursdagi matn/media» rejimida ishlatiladi (ichki fayllar bilan). «Sahifa» rejimida bu maydon kurs ro‘yxatida ko‘rinmaydi; kerak bo‘lsa yuqoridagi tavsifdan foydalaning.';
$string['dragdrophelp'] = 'Ustuvorlik tartibini o‘zgartirish uchun elementlarni sudrab o‘tkazing.';
$string['priority'] = 'Ustuvorlik';
$string['moveup'] = 'Yuqoriga';
$string['movedown'] = 'Pastga';
