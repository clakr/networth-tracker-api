<?php

enum TestEnum: string
{
    use App\Traits\CustomEnumMethods;

    case ONE = 'one';
    case TWO = 'two';
    case THREE = 'three';
}

test('names() method', function () {
    expect(TestEnum::names())->toEqual(['ONE', 'TWO', 'THREE']);
});

test('values() method', function () {
    expect(TestEnum::values())->toEqual(['one', 'two', 'three']);
});

test('validationRules() method', function () {
    expect(TestEnum::validationRules())->toEqual('in:one,two,three');
});
