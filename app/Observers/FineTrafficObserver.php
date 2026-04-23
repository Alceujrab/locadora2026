<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\FineTraffic;

class FineTrafficObserver
{
    /**
     * Ao salvar uma multa com dados de condutor informado,
     * promove/atualiza automaticamente um Customer (PF) com aquele CPF,
     * de forma que futuras multas/contratos reaproveitem o cadastro.
     * Também vincula customer_id na própria multa caso esteja vazio.
     */
    public function saved(FineTraffic $fine): void
    {
        $cpf = $this->normalizeCpf($fine->driver_cpf);
        if ($cpf === null || empty($fine->driver_name)) {
            return;
        }

        $existing = Customer::whereRaw(
            "REPLACE(REPLACE(REPLACE(cpf_cnpj,'.',''),'-',''),'/','') = ?",
            [$cpf]
        )->first();

        $payload = array_filter([
            'type'                 => 'pf',
            'name'                 => $fine->driver_name,
            'cpf_cnpj'             => $fine->driver_cpf,
            'rg'                   => $fine->driver_rg,
            'email'                => $fine->driver_email,
            'phone'                => $fine->driver_phone,
            'whatsapp'             => $fine->driver_phone,
            'cnh_number'           => $fine->driver_cnh_number,
            'cnh_expiry'           => $fine->driver_cnh_expires_at,
            'address_street'       => $fine->driver_address,
            'address_number'       => $fine->driver_address_number,
            'address_complement'   => $fine->driver_address_complement,
            'address_neighborhood' => $fine->driver_neighborhood,
            'address_city'         => $fine->driver_city,
            'address_state'        => $fine->driver_state,
            'address_zip'          => $fine->driver_zipcode,
            'doc_cnh'              => $fine->driver_cnh_path,
            'doc_address_proof'    => $fine->driver_address_proof_path,
        ], fn ($v) => $v !== null && $v !== '');

        if ($existing) {
            // Atualiza apenas campos que estavam vazios no cadastro do cliente,
            // para não sobrescrever dados curados manualmente.
            $toFill = [];
            foreach ($payload as $k => $v) {
                if (empty($existing->{$k})) {
                    $toFill[$k] = $v;
                }
            }
            if (! empty($toFill)) {
                $existing->fill($toFill)->saveQuietly();
            }
            $customerId = $existing->id;
        } else {
            $customer = Customer::create($payload);
            $customerId = $customer->id;
        }

        // Vincula customer_id na multa, se ainda não estava vinculado
        if (empty($fine->customer_id) && $customerId) {
            $fine->customer_id = $customerId;
            $fine->saveQuietly();
        }
    }

    private function normalizeCpf(?string $cpf): ?string
    {
        if (empty($cpf)) {
            return null;
        }
        $only = preg_replace('/\D/', '', $cpf);
        return strlen((string) $only) === 11 ? $only : null;
    }
}
