<?php

declare(strict_types=1);

if (! function_exists('generateToken')) {
    /**
     * Cria um identificador de 32 caracteres(a 128 bit hex number) que é extremamente dificil de prever.
     *
     * @return string 32 caracteres
     */
    function generateToken(): string
    {
        return md5(uniqid((string) mt_rand(), true));
    }
}
