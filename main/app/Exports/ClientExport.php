<?php

namespace App\Exports;

use App\Models\Client;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Class ClientExport.
 */
class ClientExport implements FromCollection, WithHeadings
{
    private Seller $seller;

    /**
     * ClientExport constructor.
     *
     * @param Seller $seller
     */
    public function __construct(Seller $seller)
    {
        $this->seller = $seller;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return Client::whereSellerId($this->seller->id)->get(['number as id', 'username', 'name']);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return ['id', 'username', 'name'];
    }
}
