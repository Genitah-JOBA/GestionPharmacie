<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture #{{ $vente->code }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 8px 12px; border: 1px solid #ccc; text-align: left; }
        h2, h4 { margin: 5px 0; }
    </style>
</head>
<body>

    <h2>Facture #{{ $vente->code }}</h2>
    <p><strong>Date :</strong> {{ $vente->created_at->format('d/m/Y H:i') }}</p>
    <p><strong>Client :</strong> {{ $vente->client->nom_complet }}</p>
    <p><strong>Montant total :</strong> {{ number_format($vente->montant_total, 2) }} Ar</p>

    <h4>Détails des produits :</h4>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Dosage</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Sous-total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($vente->medicaments as $item)
                <tr>
                    <td>{{ $item->medicament->nom }}</td>
                    <td>{{ $item->medicament->dosage }}</td>
                    <td>{{ $item->quantite }}</td>
                    <td>{{ number_format($item->prix_unitaire, 2) }} Ar</td>
                    <td>{{ number_format($item->sous_total, 2) }} Ar</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
