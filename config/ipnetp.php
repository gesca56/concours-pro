<?php

/*
|--------------------------------------------------------------------------
| Référentiel IPNETP
|--------------------------------------------------------------------------
|
| Informations institutionnelles et réglementaires sur les concours directs
| d'entrée à l'IPNETP, rassemblées à partir des communiqués officiels
| (ipnetp.ci, sessions 2022 à 2025) et de la presse ivoirienne, puis
| adaptées au fonctionnement de Concours-Pro. Les pages publiques et le
| parcours candidat lisent ces données : une seule source à mettre à jour
| à chaque nouvelle session.
|
*/

return [

    'institut' => [
        'sigle' => 'IPNETP',
        'nom' => "Institut Pédagogique National de l'Enseignement Technique et Professionnel",
        'devise' => "L'innovation pédagogique et didactique pour le rayonnement de l'ETFP au service du développement",
        'tutelle' => "Ministère de l'Enseignement Technique, de la Formation Professionnelle et de l'Apprentissage (METFPA)",
        'ville' => 'Abidjan-Cocody',
        'adresse_postale' => '08 BP 2098 Abidjan 08',
        'secretariat' => "Secrétariat des concours — en face du Lycée Technique de Cocody",
        'telephones' => ['+225 27 22 44 67 69', '+225 27 22 48 50 35'],
        'email' => 'infos@ipnetp.ci',
        'horaires' => 'Du lundi au vendredi, de 8 h à 16 h 30',
        'site_officiel' => 'https://ipnetp.ci',
    ],

    'missions' => [
        ['titre' => 'Formation initiale', 'texte' => "Former les futurs professeurs de lycée et de collège professionnels ainsi que les instructeurs de la formation professionnelle de base, jusqu'à l'obtention du Certificat d'Aptitude Pédagogique (CAP)."],
        ['titre' => 'Formation continue', 'texte' => "Accompagner les enseignants en poste dans la mise à jour de leurs pratiques et de leurs compétences techniques."],
        ['titre' => 'Recherche pédagogique', 'texte' => "Produire des outils didactiques et des programmes adaptés aux filières de l'enseignement technique et professionnel (ETFP)."],
        ['titre' => 'Coopération', 'texte' => "Porter des projets avec des partenaires techniques (KOICA, UNESCO…) et former des cadres de la sous-région."],
    ],

    /*
    | Conditions communes à tous les concours directs.
    */
    'conditions_generales' => [
        'Être de nationalité ivoirienne.',
        'Être âgé(e) de 18 ans au moins et de 39 ans au plus au 1er janvier de l\'année du concours.',
        'Être titulaire du diplôme exigé pour le concours visé, dans la spécialité choisie.',
        'Justifier, le cas échéant, de l\'expérience professionnelle demandée (notamment pour le CAP/IAFPB).',
        'S\'engager par écrit à servir dans tout établissement public d\'enseignement technique et professionnel.',
    ],

    'age_min' => 18,
    'age_max' => 39,
    'frais_inscription' => 25000,

    /*
    | Les quatre concours directs. La « chemise » est la couleur de la
    | chemise cartonnée exigée pour le dépôt du dossier physique.
    */
    'cycles' => [
        'CAP/PL' => [
            'intitule' => 'Professeur de Lycée professionnel',
            'certificat' => "Certificat d'Aptitude Pédagogique — Professeur de Lycée",
            'niveau' => 'Bac + 4 / Bac + 5',
            'diplomes' => ["Diplôme d'ingénieur", 'Master'],
            'chemise' => ['nom' => 'Rouge', 'classe' => 'bg-red-500', 'hex' => '#EF4444'],
            'duree_specialite' => '4 h',
            'specialites' => ['Construction mécanique', 'Électrotechnique', 'Électronique', 'Génie civil bâtiment', 'Informatique de gestion', 'Gestion comptabilité', 'Économie', 'Droit'],
        ],
        'CAP/PC' => [
            'intitule' => 'Professeur de Collège professionnel',
            'certificat' => "Certificat d'Aptitude Pédagogique — Professeur de Collège",
            'niveau' => 'Bac + 2 / Bac + 3',
            'diplomes' => ['Licence professionnelle', 'BTS', 'DUT'],
            'chemise' => ['nom' => 'Verte', 'classe' => 'bg-emerald-500', 'hex' => '#10B981'],
            'duree_specialite' => '4 h',
            'specialites' => ['Informatique de gestion', 'Secrétariat bureautique', 'Comptabilité', 'Électricité', 'Froid et climatisation', 'Transport, logistique et transit'],
        ],
        'CAP/IFPB' => [
            'intitule' => 'Instructeur de Formation Professionnelle de Base',
            'certificat' => "Certificat d'Aptitude Pédagogique — Instructeur de FPB",
            'niveau' => 'BT ou Baccalauréat',
            'diplomes' => ['Brevet de Technicien (BT)', 'Baccalauréat'],
            'chemise' => ['nom' => 'Bleue', 'classe' => 'bg-blue-500', 'hex' => '#3B82F6'],
            'duree_specialite' => '3 h',
            'specialites' => ['Mécanique automobile', 'Électricité bâtiment', 'Maçonnerie', 'Menuiserie', 'Couture', 'Agroéquipement'],
        ],
        'CAP/IAFPB' => [
            'intitule' => 'Instructeur Adjoint de Formation Professionnelle de Base',
            'certificat' => "Certificat d'Aptitude Pédagogique — Instructeur Adjoint de FPB",
            'niveau' => 'CAP ou BEPC + expérience',
            'diplomes' => ["Certificat d'Aptitude Professionnelle (CAP)", 'BEPC'],
            'chemise' => ['nom' => 'Jaune', 'classe' => 'bg-amber-400', 'hex' => '#FBBF24'],
            'duree_specialite' => '3 h',
            'specialites' => ['Plomberie', 'Soudure', 'Coiffure', 'Pâtisserie', 'Froid domestique'],
        ],
    ],

    /*
    | Barème des épreuves (admissibilité à l'écrit, admission à l'oral).
    */
    'epreuves' => [
        ['phase' => 'Admissibilité', 'nom' => 'Composition française', 'detail' => 'Dissertation ou analyse de texte, questions de grammaire', 'duree' => null, 'coefficient' => 3],
        ['phase' => 'Admissibilité', 'nom' => 'Épreuve de spécialité', 'detail' => 'Sujet technique dans la discipline choisie', 'duree' => '3 h (IFPB/IAFPB) · 4 h (PL/PC)', 'coefficient' => 5],
        ['phase' => 'Admission', 'nom' => 'Entretien oral', 'detail' => 'Motivation, parcours et méthodes technologiques devant un jury', 'duree' => null, 'coefficient' => 1],
    ],

    /*
    | Pièces du dossier. La clé correspond au type de document déposé en
    | ligne (null = pièce produite uniquement au dépôt physique).
    */
    'pieces' => [
        ['type' => null, 'libelle' => "Fiche d'inscription imprimée depuis la plateforme", 'note' => 'Téléchargeable depuis votre candidature'],
        ['type' => 'acte_naissance', 'libelle' => "Extrait d'acte de naissance", 'note' => null],
        ['type' => 'piece_identite', 'libelle' => "Photocopie de la carte nationale d'identité", 'note' => 'Recto-verso, en cours de validité'],
        ['type' => 'certificat_nationalite', 'libelle' => 'Certificat de nationalité ivoirienne', 'note' => null],
        ['type' => 'casier_judiciaire', 'libelle' => 'Extrait de casier judiciaire', 'note' => 'Datant de moins de 3 mois'],
        ['type' => 'certificat_medical', 'libelle' => 'Certificat médical de non-bégaiement', 'note' => null],
        ['type' => 'diplome', 'libelle' => 'Photocopie légalisée du diplôme exigé', 'note' => 'Et attestation du baccalauréat le cas échéant'],
        ['type' => 'attestation_experience', 'libelle' => "Attestation d'expérience professionnelle", 'note' => 'Si exigée pour le concours'],
        ['type' => 'engagement_manuscrit', 'libelle' => "Engagement manuscrit à servir dans l'ETFP public", 'note' => 'Lettre signée'],
        ['type' => 'photo_identite', 'libelle' => "Photo d'identité récente", 'note' => 'Fond clair'],
    ],

    /*
    | Libellés des types de documents acceptés au dépôt en ligne.
    */
    'types_documents' => [
        'acte_naissance' => "Extrait d'acte de naissance",
        'piece_identite' => "Carte nationale d'identité",
        'certificat_nationalite' => 'Certificat de nationalité',
        'casier_judiciaire' => 'Casier judiciaire (- 3 mois)',
        'certificat_medical' => 'Certificat médical de non-bégaiement',
        'diplome' => 'Diplôme légalisé',
        'attestation_experience' => "Attestation d'expérience",
        'engagement_manuscrit' => 'Engagement manuscrit',
        'photo_identite' => "Photo d'identité",
        'autre' => 'Autre pièce',
    ],

    /*
    | Calendrier type d'une session (observé sur les sessions 2024-2026).
    */
    'calendrier' => [
        ['periode' => 'Mai', 'etape' => 'Publication du communiqué', 'detail' => 'Ouverture officielle de la session par le METFPA.'],
        ['periode' => 'Mai → fin juin', 'etape' => 'Préinscription en ligne', 'detail' => 'Création du compte, choix du concours, paiement des 25 000 FCFA.'],
        ['periode' => 'Mai → début juillet', 'etape' => 'Dépôt du dossier physique', 'detail' => 'Au secrétariat des concours, dans la chemise de couleur du concours.'],
        ['periode' => 'Juillet', 'etape' => 'Épreuves écrites', 'detail' => "Composition française et épreuve de spécialité (admissibilité)."],
        ['periode' => 'Août', 'etape' => 'Épreuves orales', 'detail' => 'Entretien devant jury pour les candidats admissibles.'],
        ['periode' => 'Septembre', 'etape' => 'Résultats définitifs', 'detail' => 'Publication de la liste des admis et entrée en formation.'],
    ],

    'faq' => [
        ['q' => 'La formation à l\'IPNETP est-elle payante ?', 'r' => 'Non. Les élèves-professeurs admis par concours direct sont exonérés des frais de formation. Seuls les frais d\'inscription au concours (25 000 FCFA) et de visite médicale sont à la charge du candidat.'],
        ['q' => 'Puis-je présenter plusieurs concours la même année ?', 'r' => 'Vous pouvez créer une candidature par concours ouvert, mais les épreuves écrites se déroulant aux mêmes dates, il est conseillé de choisir le concours correspondant à votre diplôme le plus élevé.'],
        ['q' => 'Comment l\'âge est-il calculé ?', 'r' => 'L\'âge s\'apprécie au 1er janvier de l\'année du concours : pour la session 2026, il faut être né entre le 2 janvier 1986 et le 1er janvier 2008. Concours-Pro effectue ce contrôle automatiquement.'],
        ['q' => 'L\'inscription en ligne suffit-elle ?', 'r' => 'Non. La préinscription en ligne doit être complétée par le dépôt du dossier physique au secrétariat des concours, dans la chemise de couleur correspondant à votre concours (rouge, verte, bleue ou jaune).'],
        ['q' => 'Mon paiement Mobile Money n\'apparaît pas, que faire ?', 'r' => 'Depuis votre candidature, utilisez « Signaler un problème » sur le paiement concerné. Le service des concours traite le signalement et vous répond sur la plateforme.'],
        ['q' => 'Comment les copies sont-elles corrigées ?', 'r' => 'Chaque candidat reçoit un numéro d\'anonymat après la clôture des inscriptions. Les correcteurs ne voient que ce numéro, jamais le nom du candidat, jusqu\'à la délibération.'],
    ],

    'sources' => [
        ['libelle' => 'Site officiel de l\'IPNETP', 'url' => 'https://ipnetp.ci'],
        ['libelle' => 'Communiqué des concours directs d\'entrée (ipnetp.ci)', 'url' => 'https://ipnetp.ci/en/2024/08/24/concours-directs-dentree-a-linstitut-pedagogique-national-de-lenseignement-technique-et-professionnel-ipnetp/'],
        ['libelle' => 'L\'Ivoirien Express — Concours IPNETP 2025', 'url' => 'https://livoirienexpress.com/actualite/societe/cote-divoire-concours-ipnetp-2025-le-ministere-devoile-le-calendrier-des-inscriptions-et-les-conditions-deligibilite.html'],
        ['libelle' => 'Fraternité Matin — 411 candidats à l\'oral', 'url' => 'https://www.fratmat.info/article/2644040/societe/formation-professionnelleconcours-direct-dentree-a-lipnetp-411-candidats-face-a-lultime-epreuve'],
    ],
];
