<?php

namespace Tests;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use QueryBuilder\Providers\QueryBuilderServiceProvider;

use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * Подключаем провайдер пакета
     */
    protected function getPackageProviders($app): array
    {
        return [
            QueryBuilderServiceProvider::class,
        ];
    }

    /**
     * Конфигурация окружения тестов
     */
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
    }

    /**
     * Глобальный setUp
     * - накатывает миграции
     * - вызывает хуки для наследников
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpAuth();
    }

    protected function setDateTimeFormat(): void
    {
        Config::set(
            'query-builder.check-types.time-formats',
            ['H:i', 'H:i:s', 'H:i:s.u']
        );

        Config::set(
            'query-builder.check-types.date-formats',
            ['Y-m-d', 'd.m.Y', 'm/d/Y', 'd F Y', 'Y-m-d H:i:s']
        );
    }

    /**
     * Хук для авторизации (по умолчанию — ничего)
     * Переопределяется в конкретных тестах
     */
    protected function setUpAuth(): void
    {
        // noop
    }
}
