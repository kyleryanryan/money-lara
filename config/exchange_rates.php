<?php

use App\Enums\Currency;

return [
    'rates' => [
        Currency::USD->value => 1.12,   // USD to EUR
        Currency::EUR->value => 1.0,    // EUR as the base currency
        Currency::GBP->value => 0.85,   // GBP to EUR
        Currency::AMD->value => 1.0861, // AMD to EUR
        Currency::BAM->value => 1.4552, // BAM to EUR
        Currency::BZD->value => 9.508,  // BZD to EUR
        Currency::CAD->value => 5.1583, // CAD to EUR
        Currency::CHF->value => 1.9558, // CHF to EUR
        Currency::CRC->value => 1.3675, // CRC to EUR
        Currency::CZK->value => 6.9115, // CZK to EUR
        Currency::DOP->value => 1.5849, // DOP to EUR
        Currency::EEK->value => 7.549,  // EEK to EUR
        Currency::ETB->value => 24.479, // ETB to EUR
        Currency::FJD->value => 7.4372, // FJD to EUR
        Currency::ISK->value => 8.5134, // ISK to EUR
        Currency::JMD->value => 375.66, // JMD to EUR
        Currency::JOD->value => 139.6,  // JOD to EUR
        Currency::JPY->value => 82.389, // JPY to EUR
        Currency::KES->value => 15601.96, // KES to EUR
        Currency::KMF->value => 3.501,  // KMF to EUR
        Currency::KRW->value => 134.87, // KRW to EUR
        Currency::MAD->value => 1.0155, // MAD to EUR
        Currency::MOP->value => 4.585,  // MOP to EUR
        Currency::MWK->value => 21.8729, // MWK to EUR
        Currency::QAR->value => 55.99,  // QAR to EUR
        Currency::RON->value => 4.6437, // RON to EUR
        Currency::RUB->value => 4.9425, // RUB to EUR
        Currency::SLL->value => 1.4801, // SLL to EUR
        Currency::STD->value => 15.9968, // STD to EUR
        Currency::SVC->value => 0.83355, // SVC to EUR
        Currency::SYP->value => 1333.12, // SYP to EUR
        Currency::TOP->value => 10.2768, // TOP to EUR
        Currency::UAH->value => 36.488, // UAH to EUR
        Currency::UZS->value => 16.0237, // UZS to EUR
    ],
    'base_currency' => Currency::EUR->value,
];
