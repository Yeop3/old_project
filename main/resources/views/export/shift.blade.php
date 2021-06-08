@php
    /* @var App\Models\Shift $shift */
    /* @var App\Models\ProductType[]|\Illuminate\Support\Collection $products */
@endphp
    <!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Document</title>
</head>
<body>
<table>
    <thead>
    <tr>
        <th>Товар</th>
        <th>Продано</th>

        <th>Всего</th>
    </tr>
    </thead>
    <tbody>
    @foreach($products as $product)
        <tr>
            <td>
                {{$product->getFullName()}}
            </td>
            <td>
                {{$product->paid}}
            </td>

            <td>
                {{ $product->paid}}
            </td>
        </tr>
    @endforeach
    <tr>
        <td>
            Всего:
        </td>
        <td>
            {{$products->sum('paid')}}
        </td>
        <td>
            {{$products->sum('relocation')}}
        </td>
        <td>
            {{$products
                ->map(fn(\App\Models\ProductType $productType) => $product->relocation + $product->paid)
                ->sum()
                }}
        </td>
    </tr>
    </tbody>
</table>

<table>
    <thead>
    <tr>
        <th>Товар</th>
        <th>Общее реальное кол-во</th>
    </tr>
    </thead>
    <tbody>
    @foreach($products as $product)
        <tr>
            <td>
                {{$product->getFullName()}}
            </td>
            <td>
                {{$product->getPacking()}}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<table>
    <thead>
    <tr>
        <th>Доход</th>
        <th>UAH</th>
    </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                Сумма:
            </td>
            <td>
               {{$sum}}
            </td>
        </tr>
    </tbody>
</table>
</body>
</html>
