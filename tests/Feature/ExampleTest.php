<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are redirected from the home page to login', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});
