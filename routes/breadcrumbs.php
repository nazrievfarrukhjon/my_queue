<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('admin.index', function (BreadcrumbTrail $trail) {
    $trail->push('Главная', route('admin.index'));
});

Breadcrumbs::for('admin/cabinet', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.index');
    $trail->push('Личный кабинет', route('admin/cabinet'));
});

Breadcrumbs::for('admin/reception', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.index');
    $trail->push('Очередь', route('admin/reception'));
});

Breadcrumbs::for('admin/services', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.index');
    $trail->push('Услуги', route('admin/services'));
});

Breadcrumbs::for('admin/services/create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin/services');
    $trail->push("Новый", route('admin/services/create'));
});

Breadcrumbs::for('admin/services/edit', function (BreadcrumbTrail $trail, $service) {
    $trail->parent('admin/services');
    $trail->push('Редактировать', route('admin/services/edit', $service));
});

Breadcrumbs::for('admin/clients', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.index');
    $trail->push('Все клиенты', route('admin/clients'));
});

Breadcrumbs::for('admin/clients/show', function (BreadcrumbTrail $trail, $client) {
    $trail->parent('admin/clients');
    $trail->push($client->full_name, route('admin/clients/show', $client));
});

Breadcrumbs::for('admin/clients/create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin/clients');
    $trail->push("Новый", route('admin/clients/create'));
});

Breadcrumbs::for('admin/clients/edit', function (BreadcrumbTrail $trail, $client) {
    $trail->parent('admin/clients/show', $client);
    $trail->push('Редактировать', route('admin/clients/edit', $client));
});
