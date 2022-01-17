<?php

namespace Database\Seeders;

use HexideDigital\HexideAdmin\Models\AdminConfiguration;
use Illuminate\Database\Seeder;

class AdminConfigurationExampleSeeder extends Seeder
{
    public function run()
    {
        /*--------dev-testing-------*/
        $item = ["translatable" => 1, "status" => 1, "group" => "base_test",];
        $position = 1;
        foreach (AdminConfiguration::getTypes() as $type) {
            $item = array_merge($item, [
                "name" => __('models.admin_configurations.type.' . $type),
                "key" => $type,
                "type" => $type,
                "in_group_position" => $position++,
            ]);

            AdminConfiguration::create($item);
        }
        /*---------------*/

        $data = [
            'contacts' => [
                [
                    "key" => 'address',
                    "name" => 'Адреса',
                    "type" => AdminConfiguration::TEXTAREA,
                    'uk' => ["text" => "<p>вул. Шевченка 1 29000</p><p> м. Київ Україна</p>"],
                    'ru' => ["text" => "<p>ул. Шевченка 1 29000</p><p> г. Київ Украина</p>"],
                    'en' => ["text" => "<p>She Street 1 29000 </p><p> Kyiv Ukraine</p>"],
                ],
                [
                    "key" => 'phones',
                    "name" => 'Phones',
                    "type" => AdminConfiguration::MULTI_SELECT,
                    'value' => ['+38 (123) 123 45 67', '+38 (123) 123 45 67'],
                ],
                [
                    "key" => 'email',
                    "name" => 'Email',
                    "type" => AdminConfiguration::TEXT,
                    'value' => 'template@mail.com',
                ],
                [
                    "key" => 'map_link',
                    "name" => 'Google Map URL',
                    "type" => AdminConfiguration::TEXT,
                    'value' => 'https://www.google.com/maps/embed?pb=xxxxx',
                ],
            ],

            'about_page' => [
                [
                    "key" => 'image',
                    "name" => 'Image',
                    "type" => AdminConfiguration::IMAGE,
                    'value' => '/img/about_page/photo.png',
                ],
                [
                    "key" => 'title',
                    "name" => 'Title',
                    "type" => AdminConfiguration::TEXT,
                    'uk' => ['text' => 'Чому ми?'],
                    'ru' => ['text' => 'Почему мы?'],
                    'en' => ['text' => 'Why we?'],
                ],
                [
                    "key" => 'text',
                    "name" => 'Text',
                    "type" => AdminConfiguration::EDITOR,
                    'uk' => ['text' => <<<HTML
<p>обирають по всій Україні та за кордоном, тому що ми гарантуємо.</p>
HTML],
                    'ru' => ['text' => <<<HTML
<p>выбирают по всей Украине и за рубежом, потому что мы гарантируем.</p>
HTML],
                    'en' => ['text' => <<<HTML
<p>chosen throughout Ukraine and abroad because we guarantee.</p>
HTML],
                ],
            ],

            'hexide-admin' => [
                [
                    "key" => 'secret_key',
                    "name" => 'Спец ключ доступу до адмінки',
                    "type" => AdminConfiguration::TEXT,
                    'value' => '',
                ],
                [
                    "key" => 'show_admin_header',
                    "name" => 'Відображення навігації адміністратора на сайті',
                    "description" => 'Дане поле відповідає за відображення навігації для адміністратора',
                    "type" => AdminConfiguration::BOOLEAN,
                    'value' => 1,
                ],
                [
                    "key" => 'show_debug_footer_site',
                    "name" => '(Main site) Відображення інструментів для відлагодження коду',
                    "description" => 'Дане поле відповідає за відображення спеціального елементу в нижній частині екрану',
                    "type" => AdminConfiguration::BOOLEAN,
                    'value' => 1,
                ],
                [
                    "key" => 'show_debug_footer_admin',
                    "name" => '(Admin panel) Відображення інструментів для відлагодження коду',
                    "description" => 'Дане поле відповідає за відображення спеціального елементу в нижній частині екрану',
                    "type" => AdminConfiguration::BOOLEAN,
                    'value' => 1,
                ],
            ],
        ];

        $allowedKeys = array_merge((new AdminConfiguration())->getFillable(), config('translatable.locales'));
        foreach ($data as $groupName => $items) {
            $position = 1;

            foreach ($items as $item) {
                $item['group'] = $groupName;
                $item['in_group_position'] = $position++;
                $item['translatable'] = !isset($item['value']);

                AdminConfiguration::firstOrCreate(
                    \Arr::only($item, ['key', 'group']),
                    \Arr::only($item, $allowedKeys)
                );
            }
        }
    }
}
