<?php

it('renders the public unicode to bijoy converter', function () {
    $this->get(route('unicode-to-bijoy-converter'))
        ->assertOk()
        ->assertSee('ইউনিকোড টু বিজয় কনভার্টার')
        ->assertSee('data-unicode-bijoy-converter', false)
        ->assertSee('data-voice-typing', false)
        ->assertSee('data-convert="unicode-to-bijoy"', false)
        ->assertSee('data-convert="bijoy-to-unicode"', false)
        ->assertSee('data-theme-toggle', false);
});

it('links to the converter from the public navigation', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee(route('unicode-to-bijoy-converter'), false)
        ->assertSee('কনভার্টার');
});
