<?php

function getDataFromInput(): array
{
    return json_decode(file_get_contents("php://input"), true) ?? [];
}

function convertDateUsToBr(string $date): string
{
    $formatedDate = new DateTime($date);
   return $formatedDate->format('d/m/Y');
}