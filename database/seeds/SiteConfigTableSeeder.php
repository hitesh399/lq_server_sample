<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class SiteConfigTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $model = config('lq.site_config_class');

        $model::updateOrCreate(['name' => 'MAIL_DRIVER'], [
            'name' => 'MAIL_DRIVER',
            'data' => 'smtp',
            'config_group' => 'Email Configurations',
            'options' => [
                'type' => 'dropdown',
                'values' => ['smtp', 'sendmail', 'mailgun', 'mandrill', 'ses', 'sparkpost', 'log', 'array'],
                'defualt' => 'smtp',
                'isMultiple' => false,
            ],
        ]);

        $model::updateOrCreate(['name' => 'MAIL_HOST'], [
            'name' => 'MAIL_HOST',
            'data' => 'smtp.gmail.com',
            'config_group' => 'Email Configurations',
            'options' => [
                'type' => 'text',
            ],
        ]);

        $model::updateOrCreate(['name' => 'MAIL_PORT'], [
            'name' => 'MAIL_PORT',
            'data' => 587,
            'config_group' => 'Email Configurations',
            'options' => [
                'type' => 'integer',
            ],
        ]);

        $model::updateOrCreate(['name' => 'MAIL_FROM_ADDRESS'], [
            'name' => 'MAIL_FROM_ADDRESS',
            'data' => 'developerrupeshranjan@gmail.com',
            'config_group' => 'Email Configurations',
            'options' => [
                'type' => 'text',
            ],
        ]);

        $model::updateOrCreate(['name' => 'MAIL_FROM_NAME'], [
            'name' => 'MAIL_FROM_NAME',
            'data' => 'Example',
            'config_group' => 'Email Configurations',
            'options' => [
                'type' => 'text',
            ],
        ]);

        $model::updateOrCreate(['name' => 'MAIL_ENCRYPTION'], [
            'name' => 'MAIL_ENCRYPTION',
            'data' => 'tls',
            'config_group' => 'Email Configurations',
            'options' => [
                'type' => 'text',
            ],
        ]);

        $model::updateOrCreate(['name' => 'MAIL_USERNAME'], [
            'name' => 'MAIL_USERNAME',
            'data' => 'developerrupeshranjan@gmail.com',
            'config_group' => 'Email Configurations',
            'options' => [
                'type' => 'text',
                'secure' => false,
            ],
        ]);

        $model::updateOrCreate(['name' => 'MAIL_PASSWORD'], [
            'name' => 'MAIL_PASSWORD',
            'data' => Crypt::encrypt('fhujvjnofexyssvj'),
            'config_group' => 'Email Configurations',
            'options' => [
                'type' => 'text',
                'secure' => true,
            ],
        ]);

        $model::updateOrCreate(['name' => 'TWILIO_SID'], [
            'name' => 'TWILIO_SID',
            'data' => 'AC3f554b714f799d06ecf50563e138cb04',
            'config_group' => 'Twilio SMS API',
            'options' => [
                'type' => 'text',
            ],
        ]);

        $model::updateOrCreate(['name' => 'TWILIO_TOKEN'], [
            'name' => 'TWILIO_TOKEN',
            'data' => '2a15e961e2232bf63c3f097d71dbc481',
            'config_group' => 'Twilio SMS API',
            'options' => [
                'type' => 'text',
            ],
        ]);

        $model::updateOrCreate(['name' => 'TWILIO_SENDER_ID'], [
            'name' => 'TWILIO_SENDER_ID',
            'data' => '+17257264085',
            'config_group' => 'Twilio SMS API',
            'options' => [
                'type' => 'text',
            ],
        ]);

        /*
         * General data
         */
        $model::updateOrCreate(['name' => 'LOGO'], [
            'data' => '',
            'config_group' => 'General',
            'options' => [
                'type' => 'file',
                'fileType' => 'image',
                'thumbnails' => [['width' => 450, 'height' => 300]],
            ],
            'autoload' => '1',
        ]);
        $model::updateOrCreate(['name' => 'LOGO_HIGHLIGHT'], [
            'data' => '',
            'config_group' => 'General',
            'options' => [
                'type' => 'file',
                'fileType' => 'image',
                'thumbnails' => [['width' => 450, 'height' => 300]],
            ],
            'autoload' => '1',
        ]);

        $model::updateOrCreate(['name' => 'APPLICATION_NAME'], [
            'data' => 'Singsys',
            'config_group' => 'General',
            'options' => [
                'type' => 'text',
            ],
            'autoload' => '1',
        ]);

        $model::updateOrCreate(['name' => 'APPLICATION_SLOGAN'], [
            'data' => 'Singsys',
            'config_group' => 'General',
            'options' => [
                'type' => 'text',
            ],
            'autoload' => '1',
        ]);

        $model::updateOrCreate(['name' => 'FIREBASE_API_KEY'], [
            'data' => 'AIzaSyCL3XJT5qlT8MF7ht48rCbAWNo_4PoSL74',
            'config_group' => 'Push Notification',
            'options' => [
                'type' => 'text',
            ],
            'autoload' => '1',
        ]);

        $model::updateOrCreate(['name' => 'FIREBASE_SERVER_KEY'], [
            'data' => 'AAAAsGHK2Cs:APA91bGf7r7kMAK938a6PeJ9Nt1IoYGYHEV_KXvUtAs9Pdvu7v7nAC2EK0VHb9l7h-SDXaT3KtxEiBDvFSa4QasT8kaGv-OO5wi5I5p7IcOA5DKnEQQ51gTN-ETbVoU7pd-OyaQzvb3t',
            'config_group' => 'Push Notification',
            'options' => [
                'type' => 'textarea',
            ],
            'autoload' => '1',
        ]);

        $model::updateOrCreate(['name' => 'FIREBASE_SENDER_ID'], [
            'data' => '757554927659',
            'config_group' => 'Push Notification',
            'options' => [
                'type' => 'text',
            ],
            'autoload' => '1',
        ]);
    }
}
