<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Module;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Contenus de démonstration du module « Gestion pédagogique et e-learning » :
 *  - trois modules de préparation aux concours (leçons + quiz) ;
 *  - une promotion d'élèves-professeurs avec ses modules de formation,
 *    devoirs, emploi du temps et un compte élève de démonstration.
 *
 * Sans Faker (tourne en production) et sans effet si des modules existent déjà.
 */
class PedagogieIpnetpSeeder extends Seeder
{
    public function run(): void
    {
        if (Module::exists()) {
            return;
        }

        $this->call(ComptesDemoSeeder::class);

        $enseignant = User::where('role', Role::Enseignant)->orderBy('id')->first();
        $admin = User::where('role', Role::Administration)->orderBy('id')->first();

        foreach ($this->modulesPreparation() as $donnees) {
            $this->creerModule($donnees + ['public' => Module::PUBLIC_PREPARATION, 'enseignant_id' => $enseignant?->id]);
        }

        $this->creerPromotionDemo($enseignant, $admin);
    }

    /**
     * @param  array<string, mixed>  $donnees
     */
    private function creerModule(array $donnees): Module
    {
        $lecons = $donnees['lecons'] ?? [];
        $quiz = $donnees['quiz'] ?? null;
        $devoirs = $donnees['devoirs'] ?? [];
        unset($donnees['lecons'], $donnees['quiz'], $donnees['devoirs']);

        $module = Module::create($donnees + ['publie' => true]);

        foreach ($lecons as $i => $lecon) {
            $module->lecons()->create($lecon + ['ordre' => $i + 1, 'publiee' => true]);
        }

        if ($quiz) {
            $questions = $quiz['questions'];
            unset($quiz['questions']);
            $modele = $module->quiz()->create($quiz + ['publie' => true]);
            foreach ($questions as $i => [$enonce, $choix, $bonne, $explication]) {
                $modele->questions()->create([
                    'enonce' => $enonce,
                    'choix' => $choix,
                    'bonne_reponse' => $bonne,
                    'explication' => $explication,
                    'ordre' => $i + 1,
                ]);
            }
        }

        foreach ($devoirs as $devoir) {
            $module->devoirs()->create($devoir + ['publie' => true]);
        }

        return $module;
    }

    private function creerPromotionDemo(?User $enseignant, ?User $admin): void
    {
        $annee = now()->month >= 9 ? now()->year : now()->year - 1;

        $promotion = Promotion::create([
            'nom' => "CAP/PL Informatique de gestion — Promotion {$annee}-".($annee + 1),
            'code' => 'PL'.substr((string) $annee, 2).'-INFO',
            'cycle' => 'CAP/PL',
            'specialite' => 'Informatique de gestion',
            'annee_academique' => $annee.'-'.($annee + 1),
            'date_debut' => Carbon::create($annee, 10, 1),
            'date_fin' => Carbon::create($annee + 1, 7, 31),
            'statut' => 'en_cours',
        ]);

        $eleve = User::firstOrCreate(
            ['email' => 'eleve@sigec.test'],
            ['name' => 'Élève-professeur Démo', 'password' => 'password', 'role' => Role::Candidat, 'date_naissance' => now()->subYears(27)]
        );
        $eleve->forceFill(['email_verified_at' => $eleve->email_verified_at ?? now()])->save();
        $promotion->inscrire($eleve);

        $modules = [];
        foreach ($this->modulesFormation() as $donnees) {
            $modules[] = $this->creerModule($donnees + [
                'public' => Module::PUBLIC_FORMATION,
                'promotion_id' => $promotion->id,
                'enseignant_id' => $enseignant?->id,
            ]);
        }

        // Une copie déjà corrigée pour que le relevé de notes de l'élève de démonstration ne soit pas vide.
        $devoirCorrige = $modules[0]->devoirs()->first();
        $devoirCorrige?->rendus()->create([
            'user_id' => $eleve->id,
            'contenu' => "Objectif général : rendre l'élève capable de concevoir une base de données relationnelle simple.\n\n"
                ."Objectifs spécifiques : à la fin de la séance, l'élève doit être capable de (1) identifier les entités d'un énoncé de gestion, "
                ."(2) relier deux entités par une association en précisant ses cardinalités, (3) traduire le modèle en tables.",
            'rendu_le' => $devoirCorrige->date_limite->copy()->subDay(),
            'note' => 14.5,
            'appreciation' => "Objectifs bien formulés avec des verbes d'action observables. Pensez à préciser les conditions de réalisation et le critère de réussite.",
            'corrige_le' => $devoirCorrige->date_limite->copy()->addDays(2),
        ]);

        // Emploi du temps des quatre prochaines semaines (du lundi au vendredi matin).
        $lundi = now()->startOfWeek();
        $creneaux = [
            [0, '08:00', '10:00', 0, 'cours', 'Salle 12'],
            [0, '10:15', '12:15', 1, 'td', 'Labo informatique 2'],
            [1, '08:00', '10:00', 2, 'cours', 'Amphithéâtre B'],
            [2, '14:00', '16:00', 3, 'tp', 'Labo informatique 2'],
            [3, '08:00', '12:00', null, 'stage', 'Lycée professionnel d\'accueil'],
            [4, '09:00', '11:00', 1, 'en_ligne', null],
        ];
        for ($semaine = 0; $semaine < 4; $semaine++) {
            foreach ($creneaux as [$jour, $debut, $fin, $indexModule, $type, $salle]) {
                $date = $lundi->copy()->addWeeks($semaine)->addDays($jour)->toDateString();
                $promotion->seances()->create([
                    'module_id' => $indexModule !== null ? $modules[$indexModule]->id : null,
                    'debut' => "$date $debut",
                    'fin' => "$date $fin",
                    'type' => $type,
                    'salle' => $salle,
                    'lien_visio' => $type === 'en_ligne' ? 'https://meet.google.com/' : null,
                    'observations' => $type === 'stage' ? 'Observation de classe en établissement' : null,
                ]);
            }
        }

        if ($admin) {
            $promotion->annonces()->create([
                'auteur_id' => $admin->id,
                'titre' => 'Bienvenue à l\'IPNETP',
                'contenu' => "Bienvenue aux élèves-professeurs de la promotion. Votre emploi du temps, vos cours et vos devoirs sont désormais disponibles dans l'espace e-learning.\n"
                    ."La rentrée solennelle se tiendra à l'amphithéâtre ; la présence de tous est obligatoire.",
            ]);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function modulesPreparation(): array
    {
        return [
            [
                'titre' => 'Méthodologie de la dissertation',
                'code' => 'PREP-MET',
                'description' => "L'épreuve de culture générale et l'épreuve de pédagogie se rédigent sous forme de dissertation. Ce module donne une méthode simple pour analyser un sujet, bâtir un plan et rédiger.",
                'objectifs' => "Analyser un sujet et dégager une problématique\nConstruire un plan en deux ou trois parties\nRédiger une introduction et une conclusion complètes\nGérer son temps pendant l'épreuve",
                'lecons' => [
                    [
                        'titre' => 'Analyser le sujet',
                        'resume' => 'Lire, définir les mots-clés et formuler la problématique.',
                        'duree_minutes' => 20,
                        'contenu' => <<<'MD'
## Pourquoi l'analyse est décisive

La majorité des copies faibles ne manquent pas de connaissances : elles **répondent à côté du sujet**. Consacrez au moins 15 minutes à l'analyse avant d'écrire quoi que ce soit.

## La méthode en quatre temps

1. **Recopier le sujet** en haut du brouillon et souligner les mots-clés.
2. **Définir chaque mot-clé** : sens courant, sens dans le contexte de l'éducation ou de la formation.
3. **Repérer la consigne** : « discutez », « analysez », « commentez », « pensez-vous que… ». Elle indique le type de plan attendu.
4. **Formuler la problématique** : la question de fond que pose le sujet, en une phrase.

> Exemple — Sujet : « L'école doit-elle seulement instruire ? »
>
> Mots-clés : *école*, *seulement*, *instruire* (transmettre des savoirs) par opposition à *éduquer* (former la personne et le citoyen).
>
> Problématique : la mission de l'école se limite-t-elle à la transmission des connaissances ou doit-elle aussi former des citoyens ?

## À éviter

- Réciter un cours sans lien avec le sujet.
- Changer le sujet en un sujet voisin que l'on maîtrise mieux.
- Oublier un mot du sujet (« seulement » change tout dans l'exemple ci-dessus).
MD,
                    ],
                    [
                        'titre' => 'Construire le plan',
                        'resume' => 'Plan dialectique, thématique ou analytique : lequel choisir ?',
                        'duree_minutes' => 25,
                        'contenu' => <<<'MD'
## Trois plans types

| Plan | Quand l'utiliser | Structure |
|---|---|---|
| Dialectique | Sujet sous forme de question fermée ou de citation à discuter | Thèse / antithèse / (synthèse) |
| Thématique | Sujet « Quels sont… », « En quoi… » | Un aspect par partie |
| Analytique | Sujet portant sur un problème | Constat / causes / solutions |

## Les règles d'un bon plan

- **Deux ou trois parties** équilibrées, chacune divisée en deux ou trois sous-parties.
- Chaque partie répond à la problématique ; chaque sous-partie contient **un argument et un exemple**.
- Les exemples tirés de l'enseignement technique et professionnel ivoirien sont très appréciés du jury : stages en entreprise, ateliers, apprentissage, insertion des diplômés.

## Le brouillon

Ne rédigez pas tout au brouillon : notez le plan détaillé, les exemples et rédigez seulement l'introduction et la conclusion.
MD,
                    ],
                    [
                        'titre' => 'Rédiger introduction et conclusion',
                        'resume' => 'Les quatre temps de l\'introduction et la conclusion ouverte.',
                        'duree_minutes' => 20,
                        'contenu' => <<<'MD'
## L'introduction en quatre temps

1. **L'amorce** : une idée générale ou un fait qui conduit au sujet.
2. **La reprise du sujet** : citation ou reformulation, avec définition des termes.
3. **La problématique** sous forme de question.
4. **L'annonce du plan** : « Nous verrons d'abord… puis… ».

## La conclusion

- Un **bilan** qui répond clairement à la problématique.
- Une **ouverture** vers une question voisine (sans en poser une nouvelle trop large).

## Gestion du temps (épreuve de 4 heures)

- Analyse et plan : 1 h
- Rédaction : 2 h 30
- Relecture (orthographe, accords, ponctuation) : 30 min

Une copie propre, aérée (un paragraphe par argument) et sans faute est déjà une copie qui se démarque.
MD,
                    ],
                ],
                'quiz' => [
                    'titre' => 'Méthode de la dissertation',
                    'consignes' => 'Une seule bonne réponse par question.',
                    'duree_minutes' => 10,
                    'questions' => [
                        ['Combien de temps consacrer à l\'analyse du sujet avant de rédiger ?', ['Aucun, il faut commencer tout de suite', 'Au moins un quart d\'heure', 'La moitié de l\'épreuve'], 1, 'L\'analyse évite le hors-sujet, première cause de mauvaises notes ; un quart d\'heure au minimum.'],
                        ['Quel plan convient à la question « L\'école doit-elle seulement instruire ? »', ['Dialectique', 'Chronologique', 'Analytique (constat, causes, solutions)'], 0, 'La question fermée invite à discuter : oui… mais aussi… : c\'est le plan dialectique.'],
                        ['Que contient l\'annonce du plan ?', ['La réponse définitive au sujet', 'Les grandes parties du développement', 'Une citation d\'auteur'], 1, 'L\'annonce présente les grandes parties ; la réponse se trouve dans la conclusion.'],
                        ['Dans une sous-partie, on doit trouver…', ['Un argument et un exemple', 'Uniquement des exemples', 'Une nouvelle problématique'], 0, 'Chaque sous-partie développe un argument illustré par un exemple précis.'],
                        ['Que doit contenir la conclusion ?', ['Un résumé de chaque sous-partie', 'Un bilan qui répond à la problématique et une ouverture', 'Une nouvelle partie'], 1, 'La conclusion répond à la problématique puis ouvre la réflexion.'],
                        ['Combien de temps garder pour la relecture d\'une épreuve de 4 heures ?', ['5 minutes', 'Environ 30 minutes', 'Il n\'est pas utile de relire'], 1, 'Une demi-heure permet de corriger l\'orthographe et les accords, très pénalisés.'],
                    ],
                ],
            ],
            [
                'titre' => 'Culture générale : la Côte d\'Ivoire et ses institutions',
                'code' => 'PREP-CG',
                'description' => 'Les repères institutionnels et civiques indispensables pour l\'épreuve de culture générale et l\'entretien.',
                'objectifs' => "Connaître les symboles de la République\nDécrire l'organisation des pouvoirs\nSituer l'ETFP dans le système éducatif ivoirien",
                'lecons' => [
                    [
                        'titre' => 'Les symboles et les institutions de la République',
                        'resume' => 'Devise, emblème, hymne, Constitution et séparation des pouvoirs.',
                        'duree_minutes' => 25,
                        'contenu' => <<<'MD'
## Les symboles

- **Devise** : « Union – Discipline – Travail ».
- **Emblème** : le drapeau tricolore orange, blanc, vert, en bandes verticales.
- **Hymne national** : *L'Abidjanaise*.
- **Indépendance** : 7 août 1960.
- **Capitale politique** : Yamoussoukro ; **capitale économique** : Abidjan.

## La Constitution

La Constitution en vigueur a été adoptée par référendum en **2016** ; elle fonde la **Troisième République**. Elle organise la séparation des pouvoirs :

| Pouvoir | Institutions |
|---|---|
| Exécutif | Le Président de la République, le Vice-Président, le Gouvernement dirigé par le Premier ministre |
| Législatif | Le Parlement, composé de l'Assemblée nationale et du Sénat |
| Judiciaire | Les cours et tribunaux, dont la Cour de cassation et le Conseil d'État |

Le Conseil constitutionnel veille à la conformité des lois à la Constitution.

## Pour l'entretien

Le jury apprécie un candidat capable de relier ces notions à sa future mission : l'enseignant est un **agent de l'État** au service de l'intérêt général, tenu à la neutralité et à l'exemplarité.
MD,
                    ],
                    [
                        'titre' => 'L\'enseignement technique et la formation professionnelle',
                        'resume' => 'La place de l\'ETFP et le rôle de l\'IPNETP.',
                        'duree_minutes' => 20,
                        'contenu' => <<<'MD'
## L'ETFP en Côte d'Ivoire

L'enseignement technique et la formation professionnelle (ETFP) relèvent du ministère chargé de l'Enseignement technique, de la Formation professionnelle et de l'Apprentissage. Ils forment aux métiers à travers :

- les **lycées professionnels** et **lycées techniques** ;
- les **collèges d'enseignement technique** et les centres de formation professionnelle ;
- l'**apprentissage**, en alternance avec l'entreprise.

## Le rôle de l'IPNETP

L'Institut Pédagogique National de l'Enseignement Technique et Professionnel forme les **professeurs** et **instructeurs** de l'ETFP. Les admis aux concours directs y suivent une formation pédagogique sanctionnée par un **Certificat d'Aptitude Pédagogique (CAP)**, puis sont affectés dans un établissement public.

## Enjeux à connaître

- **Employabilité** des jeunes et adéquation formation-emploi.
- Implication des **entreprises** (stages, alternance, apprentissage).
- **Approche par compétences** (APC) dans les programmes.
MD,
                    ],
                ],
                'quiz' => [
                    'titre' => 'Repères institutionnels',
                    'duree_minutes' => 8,
                    'questions' => [
                        ['Quelle est la devise de la Côte d\'Ivoire ?', ['Unité – Travail – Progrès', 'Union – Discipline – Travail', 'Paix – Travail – Patrie'], 1, null],
                        ['Quelle est la date de l\'indépendance de la Côte d\'Ivoire ?', ['7 août 1960', '1er janvier 1960', '7 décembre 1960'], 0, null],
                        ['De quelles chambres le Parlement ivoirien est-il composé ?', ['De l\'Assemblée nationale seule', 'De l\'Assemblée nationale et du Sénat', 'Du Sénat et du Conseil économique'], 1, 'Le Sénat a été institué par la Constitution de 2016.'],
                        ['Quel est le titre de l\'hymne national ?', ['L\'Abidjanaise', 'La Concorde', 'Terre d\'espérance'], 0, null],
                        ['Que délivre l\'IPNETP à l\'issue de la formation des admis ?', ['Un baccalauréat technique', 'Un Certificat d\'Aptitude Pédagogique (CAP)', 'Un BTS'], 1, 'Le CAP correspond au concours réussi : PL, PC, IFPB ou IAFPB.'],
                    ],
                ],
            ],
            [
                'titre' => 'Initiation à la pédagogie',
                'code' => 'PREP-PED',
                'description' => "Les notions de base de l'épreuve de pédagogie : objectifs, approche par compétences et évaluation.",
                'objectifs' => "Formuler un objectif pédagogique opérationnel\nExpliquer l'approche par compétences\nDistinguer les trois fonctions de l'évaluation",
                'lecons' => [
                    [
                        'titre' => 'Les objectifs pédagogiques',
                        'resume' => 'De l\'objectif général à l\'objectif opérationnel.',
                        'duree_minutes' => 20,
                        'contenu' => <<<'MD'
## Objectif général et objectif opérationnel

- L'**objectif général** décrit une intention à long terme : « Initier les élèves à la comptabilité ».
- L'**objectif opérationnel** décrit un comportement **observable et mesurable** attendu à la fin d'une séance.

## Les trois composantes d'un objectif opérationnel

1. **Le comportement attendu**, exprimé par un verbe d'action : *identifier, calculer, câbler, rédiger*… (et non *comprendre* ou *savoir*, invérifiables).
2. **Les conditions de réalisation** : « à partir d'un schéma », « sans document ».
3. **Le critère de réussite** : « sans erreur », « en moins de 20 minutes », « 8 réponses justes sur 10 ».

> Exemple : « À partir d'un schéma électrique fourni, l'élève doit être capable de câbler un circuit va-et-vient, sans erreur, en 30 minutes. »

## La taxonomie de Bloom

Bloom classe les objectifs cognitifs du plus simple au plus complexe : **connaître, comprendre, appliquer, analyser, synthétiser (créer), évaluer**. Elle aide à varier le niveau d'exigence d'une séance.
MD,
                    ],
                    [
                        'titre' => 'L\'approche par compétences',
                        'resume' => 'Mobiliser des ressources pour résoudre une situation.',
                        'duree_minutes' => 20,
                        'contenu' => <<<'MD'
## Qu'est-ce qu'une compétence ?

Une compétence est la capacité à **mobiliser un ensemble de ressources** (savoirs, savoir-faire, savoir-être) pour **traiter une situation** d'une famille donnée. On ne la constate qu'en situation.

## Les principes de l'APC

- Partir d'une **situation d'apprentissage** proche de la vie réelle ou du métier.
- Placer l'élève au **centre** : il agit, cherche, échange ; l'enseignant guide.
- Évaluer par des **situations d'évaluation** complexes plutôt que par la seule restitution.

## Dans l'enseignement technique

L'APC s'appuie naturellement sur l'atelier et l'entreprise : diagnostiquer une panne, établir un devis, réaliser une pièce selon un cahier des charges sont des situations qui mobilisent plusieurs ressources à la fois.
MD,
                    ],
                    [
                        'titre' => 'Les fonctions de l\'évaluation',
                        'resume' => 'Diagnostique, formative, sommative.',
                        'duree_minutes' => 15,
                        'contenu' => <<<'MD'
## Trois moments, trois fonctions

| Évaluation | Moment | But |
|---|---|---|
| **Diagnostique** | Avant l'apprentissage | Connaître les prérequis et les difficultés des élèves |
| **Formative** | Pendant l'apprentissage | Repérer les erreurs et réguler l'enseignement ; elle n'est pas forcément notée |
| **Sommative** | À la fin d'une séquence | Faire le bilan des acquis, souvent noté, pour certifier ou classer |

## L'erreur, outil d'apprentissage

En pédagogie moderne, l'erreur n'est pas une faute à sanctionner mais une **information** : elle montre où en est l'élève et ce qu'il faut reprendre. Les quiz de cet espace e-learning sont d'ailleurs des évaluations formatives : refaites-les autant que nécessaire !
MD,
                    ],
                ],
                'quiz' => [
                    'titre' => 'Notions de pédagogie',
                    'duree_minutes' => 10,
                    'questions' => [
                        ['Quel verbe convient à un objectif opérationnel ?', ['Comprendre', 'Savoir', 'Calculer'], 2, 'Un objectif opérationnel s\'exprime par un comportement observable : calculer, identifier, réaliser…'],
                        ['Quelle composante manque à l\'objectif « L\'élève doit être capable de rédiger une facture » ?', ['Le verbe d\'action', 'Les conditions et le critère de réussite', 'Rien, il est complet'], 1, 'Il manque les conditions (à partir de quoi ?) et le critère de réussite (sans erreur ? en combien de temps ?).'],
                        ['L\'évaluation réalisée en début d\'apprentissage pour connaître les prérequis est…', ['Diagnostique', 'Formative', 'Sommative'], 0, null],
                        ['Dans l\'approche par compétences, la compétence se constate…', ['Par une récitation du cours', 'En situation', 'Uniquement par un QCM'], 1, 'Une compétence se manifeste quand l\'élève mobilise ses ressources pour traiter une situation.'],
                        ['Selon la taxonomie de Bloom, quel niveau est le plus simple ?', ['Analyser', 'Évaluer', 'Connaître'], 2, null],
                        ['L\'évaluation formative sert principalement à…', ['Classer les élèves', 'Réguler l\'apprentissage', 'Délivrer un diplôme'], 1, 'Elle intervient pendant l\'apprentissage pour repérer et corriger les difficultés.'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function modulesFormation(): array
    {
        return [
            [
                'titre' => 'Didactique de l\'informatique',
                'code' => 'DID-INF',
                'coefficient' => 4,
                'volume_horaire' => 60,
                'description' => 'Concevoir, conduire et évaluer une séance d\'informatique de gestion en lycée professionnel.',
                'objectifs' => "Formuler les objectifs d'une séance\nConstruire une fiche de préparation\nConcevoir une activité pratique sur machine",
                'lecons' => [
                    [
                        'titre' => 'La fiche de préparation',
                        'resume' => 'Le document de référence de toute séance.',
                        'duree_minutes' => 30,
                        'contenu' => <<<'MD'
## Rubriques d'une fiche de préparation

- **En-tête** : établissement, classe, effectif, durée, date.
- **Titre de la leçon** et place dans la progression.
- **Objectifs** général et opérationnels.
- **Prérequis** et **matériel** (postes, logiciels, vidéoprojecteur).
- **Déroulement** en phases minutées : mise en situation, activités, synthèse, évaluation.

## Le tableau de déroulement

| Phase | Durée | Activités de l'enseignant | Activités des élèves |
|---|---|---|---|
| Mise en situation | 10 min | Présente un cas d'entreprise | Lisent, questionnent |
| Activité | 35 min | Guide, circule entre les postes | Réalisent la tâche sur machine |
| Synthèse | 10 min | Fait formuler la règle | Rédigent le résumé |
| Évaluation | 5 min | Pose un exercice court | Répondent individuellement |
MD,
                    ],
                    [
                        'titre' => 'Conduire une séance sur machine',
                        'resume' => 'Organiser la salle, les binômes et le temps.',
                        'duree_minutes' => 25,
                        'contenu' => <<<'MD'
## Avant la séance

- Vérifier que **chaque poste démarre** et que les fichiers de travail sont présents.
- Prévoir une **activité de secours** sans ordinateur en cas de panne de courant.

## Pendant la séance

- Consignes **écrites** au tableau ou sur fiche, pas seulement orales.
- **Binômes** tournants : un élève au clavier, l'autre guide, on alterne toutes les 10 minutes.
- L'enseignant **circule** ; il ne prend jamais la souris à la place de l'élève.

## Après la séance

Noter dans le cahier de textes ce qui a été fait et ce qu'il faudra reprendre.
MD,
                    ],
                ],
                'devoirs' => [
                    [
                        'titre' => 'Formuler les objectifs d\'une séance',
                        'consignes' => "Choisissez une leçon du programme d'informatique de gestion (par exemple : les bases de données relationnelles).\n\n1. Rédigez l'objectif général de la leçon.\n2. Rédigez trois objectifs opérationnels complets (comportement, conditions, critère).\n\nBarème : objectif général 4 points ; chaque objectif opérationnel 5 points ; qualité de l'expression 1 point.",
                        'date_limite' => now()->subDays(5)->setTime(18, 0),
                    ],
                    [
                        'titre' => 'Fiche de préparation complète',
                        'consignes' => "Rédigez la fiche de préparation complète d'une séance de 2 heures sur le tableur (fonctions SI et RECHERCHEV) en classe de BT.\n\nJoignez, si vous le souhaitez, le fichier d'exercice élève par un lien Google Drive.",
                        'date_limite' => now()->addDays(10)->setTime(18, 0),
                    ],
                ],
            ],
            [
                'titre' => 'Psychopédagogie et gestion de classe',
                'code' => 'PSY-101',
                'coefficient' => 3,
                'volume_horaire' => 45,
                'description' => 'Comprendre l\'adolescent apprenant et instaurer un climat de classe propice au travail.',
                'objectifs' => "Identifier les besoins de l'adolescent\nÉtablir des règles de vie de classe\nPrévenir et gérer les conflits",
                'lecons' => [
                    [
                        'titre' => 'Instaurer un climat de classe',
                        'resume' => 'Règles, routines et posture de l\'enseignant.',
                        'duree_minutes' => 25,
                        'contenu' => <<<'MD'
## Les premières séances sont décisives

- Présenter les **règles de vie** de la classe, peu nombreuses et formulées positivement.
- Installer des **routines** : entrée en classe, distribution du matériel, rangement de l'atelier.
- Apprendre rapidement **les prénoms** des élèves.

## La posture

Être **ferme sur le cadre** et **bienveillant envers la personne** : on sanctionne un comportement, jamais l'élève lui-même. La cohérence (appliquer les mêmes règles à tous, tout le temps) est plus efficace que la sévérité.
MD,
                    ],
                ],
                'devoirs' => [
                    [
                        'titre' => 'Analyse d\'une situation de classe',
                        'consignes' => "Pendant votre stage d'observation, décrivez une situation de tension en classe (sans nommer d'élève), puis analysez la réaction de l'enseignant et proposez une autre manière d'agir. Deux pages environ.",
                        'date_limite' => now()->addDays(17)->setTime(18, 0),
                    ],
                ],
            ],
            [
                'titre' => 'Législation scolaire et déontologie',
                'code' => 'LEG-101',
                'coefficient' => 2,
                'volume_horaire' => 20,
                'description' => 'Droits et obligations du fonctionnaire enseignant, organisation de l\'établissement.',
                'objectifs' => "Connaître les obligations du fonctionnaire\nSituer les instances d'un établissement",
                'lecons' => [
                    [
                        'titre' => 'Les obligations de l\'enseignant fonctionnaire',
                        'resume' => 'Service, neutralité, discrétion, exemplarité.',
                        'duree_minutes' => 20,
                        'contenu' => <<<'MD'
## Les grandes obligations

- **Assurer son service** : présence, ponctualité, respect des programmes et des horaires.
- **Neutralité** politique et religieuse dans l'exercice de ses fonctions.
- **Discrétion professionnelle** sur les informations concernant les élèves et l'établissement.
- **Obéissance hiérarchique**, sauf ordre manifestement illégal.
- **Exemplarité** : tenue, langage, comportement, dans et hors de l'établissement.

## L'engagement des admis

Les candidats admis aux concours de l'IPNETP s'engagent par écrit à **servir dans tout établissement public** d'enseignement technique et professionnel où ils seront affectés.
MD,
                    ],
                ],
            ],
            [
                'titre' => 'TICE : enseigner avec le numérique',
                'code' => 'TICE-1',
                'coefficient' => 2,
                'volume_horaire' => 20,
                'description' => 'Utiliser les outils numériques pour préparer, animer et évaluer.',
                'objectifs' => "Choisir un outil numérique adapté à un objectif\nCréer un quiz d'évaluation formative",
                'lecons' => [
                    [
                        'titre' => 'Le numérique au service de l\'objectif',
                        'resume' => 'L\'outil n\'est pas une fin en soi.',
                        'duree_minutes' => 15,
                        'contenu' => <<<'MD'
## Une question à se poser

Avant d'utiliser un outil numérique, demandez-vous : **qu'apporte-t-il à l'apprentissage** par rapport à une activité sans écran ?

## Usages utiles en ETFP

- **Simulation** de circuits, de machines ou de processus dangereux.
- **Vidéos** courtes de gestes professionnels, revues à volonté.
- **Quiz** d'évaluation formative corrigés instantanément (comme sur cette plateforme).
- **Espaces de cours en ligne** pour déposer consignes, supports et devoirs.

## Contraintes à anticiper

Coupures d'électricité, connexion limitée, nombre de postes : prévoyez toujours une solution de repli.
MD,
                    ],
                ],
            ],
        ];
    }
}
