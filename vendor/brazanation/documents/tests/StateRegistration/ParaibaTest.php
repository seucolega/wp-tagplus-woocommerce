<?php

namespace Brazanation\Documents\Tests\StateRegistration;

use Brazanation\Documents\AbstractDocument;
use Brazanation\Documents\StateRegistration;
use Brazanation\Documents\StateRegistration\Paraiba;
use Brazanation\Documents\Tests\DocumentTestCase;

class ParaibaTest extends DocumentTestCase
{
    public function createDocument(string $number) : AbstractDocument
    {
        return new StateRegistration($number, new Paraiba());
    }

    public function createDocumentFromString(string $number)
    {
        return StateRegistration::createFromString($number, Paraiba::SHORT_NAME);
    }

    public function provideValidNumbers() : array
    {
        return [
            ['16.030.321-4'],
            ['16.136.565-5'],
            ['16.111.243-9'],
            ['16.105.973-2'],
            ['16.128.044-7'],
            ['16.125.311-3'],
            ['16.081.819-2'],
            ['16.136.684-8'],
            ['16.138.682-2'],
            ['16.093.179-7'],
            ['16.107.903-2'],
            ['16.041.136-0'],
            ['16.037.237-2'],
            ['16.120.055-9'],
            ['16.145.767-3'],
            ['16.139.881-2'],
            ['16.119.904-6'],
            ['16.001.810-2'],
            ['16.054.111-5'],
            ['16.124.276-6'],
            ['16.143.561-0'],
            ['16.142.444-9'],
            ['16.145.427-5'],
            ['16.122.174-2'],
            ['16.083.425-2'],
            ['16.141.248-3'],
            ['16.008.075-4'],
            ['16.111.650-7'],
            ['16.086.054-7'],
            ['16.045.309-7'],
            ['16.145.926-9'],
            ['16.136.127-7'],
            ['16.123.918-8'],
            ['16.079.048-4'],
            ['16.134.188-8'],
            ['16.048.163-5'],
            ['16.146.966-3'],
            ['16.075.118-7'],
            ['16.087.059-3'],
            ['16.144.069-0'],
            ['16.025.387-0'],
            ['16.095.952-7'],
            ['16.139.869-3'],
            ['16.110.475-4'],
            ['16.020.236-1'],
            ['16.111.292-7'],
            ['16.120.586-0'],
            ['16.134.329-5'],
            ['16.123.409-7'],
            ['16.133.260-9'],
            ['16.118.679-3'],
            ['16.100.685-0'],
            ['16.011.976-6'],
            ['16.130.099-5'],
            ['16.012.860-9'],
            ['16.142.848-7'],
            ['16.089.072-1'],
            ['16.118.317-4'],
            ['16.046.281-9'],
            ['16.147.839-5'],
            ['16.120.993-9'],
            ['16.079.498-6'],
            ['16.049.309-9'],
            ['16.147.520-5'],
            ['16.127.312-2'],
            ['16.120.859-2'],
            ['16.126.735-1'],
            ['16.067.498-0'],
            ['16.106.566-0'],
            ['16.096.966-2'],
            ['16.131.388-4'],
            ['16.111.125-4'],
            ['16.147.840-9'],
            ['16.080.878-2'],
            ['16.085.501-2'],
            ['16.122.755-4'],
            ['16.115.107-8'],
            ['16.131.982-3'],
            ['16.144.718-0'],
            ['16.146.183-2'],
            ['16.129.707-2'],
            ['16.118.574-6'],
            ['16.135.708-3'],
            ['16.135.721-0'],
            ['16.046.339-4'],
            ['16.129.136-8'],
            ['16.025.319-5'],
            ['16.096.284-6'],
            ['16.136.837-9'],
            ['16.039.234-9'],
            ['16.020.416-0'],
            ['16.116.421-8'],
            ['16.062.092-9'],
            ['16.048.449-9'],
            ['16.095.486-0'],
            ['16.138.312-2'],
            ['16.089.572-3'],
            ['16.134.213-2'],
            ['39.607.756-0'],
            ['31.780.673-4'],

        ];
    }

    public function provideValidNumbersAndExpectedFormat() : array
    {
        return [
            ['317806734', '31.780.673-4'],
        ];
    }

    public function provideEmptyData() : array
    {
        return [
            [Paraiba::LONG_NAME, null],
        ];
    }

    public function provideInvalidNumber() : array
    {
        return [
            [Paraiba::LONG_NAME, '11111111111'],
            [Paraiba::LONG_NAME, '99874773539'],
        ];
    }
}