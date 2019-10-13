<?php

use Illuminate\Database\Seeder;

class NotificationTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $model = config('lq.notification_template_class');

        $model::updateOrCreate(['name' => 'WELCOME_MAIL'], [
            'name' => 'WELCOME_MAIL',
            'subject' => 'Thank You to Join Us.',
            'body' => 'Dear {user.name} <br> Please <a href="{link}"> click here </a> to verify your email or you can use this code {user_verification.token}',
            'options' => [
                'variables' => [
                    'user.name',
                    'link',
                    'user.email',
                    'user.mobile_no',
                    'user_verification.token',
                ],
            ],
        ]);

        $model::updateOrCreate(['name' => 'FORGET_PASSWORD_EMAIL'], [
            'name' => 'FORGET_PASSWORD_EMAIL',
            'subject' => 'Reset Password Link',
            'body' => 'Dear {user.name} <br> Please <a href="{link}"> click here </a> to reset the password',
            'options' => [
                'variables' => [
                    'user.name',
                    'link',
                    'user.email',
                    'user.mobile_no',
                    'user_verification.token',
                ],
            ],
        ]);

        $model::updateOrCreate(['name' => 'EMAIL_VERFICATION_EMAIL'], [
            'name' => 'EMAIL_VERFICATION_EMAIL',
            'subject' => 'Email Verification Link',
            'body' => 'Dear {user.name} <br> Please <a href="{link}"> click here </a> to verify the email.',
            'options' => [
                'variables' => [
                    'user.name',
                    'link',
                    'user.email',
                    'user.mobile_no',
                    'user_verification.token',
                ],
            ],
        ]);

        $model::updateOrCreate(['name' => 'MOBILE_VERFICATION_SMS'], [
            'name' => 'MOBILE_VERFICATION_SMS',
            'type' => 'sms',
            'subject' => 'Email Verification Link',
            'body' => 'Dear {user.name}, Your OTP is {user_verification.token} to verify the mobile numnber.',
            'options' => [
                'variables' => [
                    'user.name',
                    'link',
                    'user.email',
                    'user.mobile_no',
                    'user_verification.token',
                ],
            ],
        ]);
    }
}
