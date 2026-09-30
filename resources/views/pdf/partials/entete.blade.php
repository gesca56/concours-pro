{{-- En-tête administratif commun aux documents PDF (format des actes administratifs ivoiriens). --}}
<table class="entete">
    <tr>
        <td class="entete-gauche">
            <strong>MINISTÈRE DE L'ENSEIGNEMENT TECHNIQUE,<br>DE LA FORMATION PROFESSIONNELLE<br>ET DE L'APPRENTISSAGE</strong>
            <div class="filet"></div>
            <strong>INSTITUT PÉDAGOGIQUE NATIONAL DE L'ENSEIGNEMENT<br>TECHNIQUE ET PROFESSIONNEL (IPNETP)</strong>
            <div class="filet"></div>
            Secrétariat des concours
        </td>
        <td class="entete-droite">
            <strong>RÉPUBLIQUE DE CÔTE D'IVOIRE</strong>
            <div class="filet"></div>
            <em>Union – Discipline – Travail</em>
        </td>
    </tr>
</table>

<div class="titre-document">
    <h1>{{ $titre }}</h1>
    @isset($sousTitre)<p>{{ $sousTitre }}</p>@endisset
</div>
