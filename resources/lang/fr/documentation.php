<?php

return [
    'documentation' => 'Documentation',
    'home' => 'Accueil',
    'intro' => 'Bienvenue dans Triangle POS. Ce guide vous présente les principales sections du système afin que vous puissiez gérer en toute confiance vos produits, ventes, achats et rapports. Utilisez le sommaire à gauche pour accéder à un sujet.',
    'need_help' => 'Besoin d\'aide supplémentaire ?',
    'need_help_body' => 'Si vous ne trouvez pas ce que vous cherchez, contactez votre administrateur système ou ouvrez un ticket sur le dépôt du projet.',
    'page_title' => 'Guide utilisateur & documentation',
    'sections' => [
        'getting_started' => [
            'title' => 'Premiers pas',
            'body' => [
                0 => 'Connectez-vous avec l\'e-mail et le mot de passe fournis par votre administrateur. Après connexion, vous arrivez sur le tableau de bord (Accueil).',
                1 => 'Ce que vous pouvez voir et faire dépend de votre rôle. Triangle POS propose les rôles Super Admin, Admin, Propriétaire, Gérant et Caissier, chacun ayant ses propres permissions.',
                2 => 'Utilisez la barre latérale à gauche pour naviguer entre les modules. Les éléments de menu pour lesquels vous n\'avez pas de permission sont masqués automatiquement.',
                3 => 'Vous pouvez changer la langue de l\'interface et basculer entre les thèmes clair et sombre depuis la barre d\'en-tête en haut.',
            ],
        ],
        'dashboard' => [
            'title' => 'Tableau de bord',
            'body' => [
                0 => 'Le tableau de bord offre un aperçu rapide de votre activité : total des ventes, des achats, des retours et du bénéfice.',
                1 => 'Les graphiques comparent les ventes et les achats dans le temps, l\'activité du mois en cours et le flux des paiements pour repérer les tendances en un coup d\'œil.',
            ],
        ],
        'products' => [
            'title' => 'Produits & codes-barres',
            'body' => [
                0 => 'Ouvrez Produits pour créer et gérer votre catalogue. Chaque produit peut avoir un code, un coût, un prix, une unité, une quantité en stock et plusieurs images.',
                1 => 'Regroupez les produits à l\'aide des Catégories pour organiser le catalogue et filtrer plus rapidement sur l\'écran de caisse.',
                2 => 'Utilisez Imprimer le code-barres pour générer et imprimer des étiquettes prêtes à être scannées au point de vente.',
            ],
        ],
        'adjustments' => [
            'title' => 'Ajustements de stock',
            'body' => [
                0 => 'Les ajustements de stock permettent de corriger les quantités d\'inventaire lorsque du stock est ajouté ou retiré en dehors d\'un achat ou d\'une vente normale (par exemple casse, perte ou inventaire).',
                1 => 'Créez un ajustement, ajoutez les produits concernés et choisissez si chaque ligne augmente ou diminue la quantité disponible.',
            ],
        ],
        'quotations' => [
            'title' => 'Devis',
            'body' => [
                0 => 'Préparez des devis pour vos clients avant qu\'ils ne s\'engagent dans un achat.',
                1 => 'Un devis peut être imprimé ou envoyé par e-mail directement au client, puis converti en vente.',
            ],
        ],
        'purchases' => [
            'title' => 'Achats & retours d\'achat',
            'body' => [
                0 => 'Enregistrez le stock acheté auprès des fournisseurs dans Achats. Ajouter un achat augmente le stock du produit et permet de suivre le montant payé et dû.',
                1 => 'Utilisez les Retours d\'achat lorsque vous renvoyez des marchandises à un fournisseur. Cela réduit le stock et ajuste le solde du fournisseur en conséquence.',
                2 => 'Des paiements peuvent être enregistrés pour chaque achat ou retour d\'achat afin de garder les soldes fournisseurs exacts.',
            ],
        ],
        'sales' => [
            'title' => 'Ventes & point de vente',
            'body' => [
                0 => 'L\'écran Ventes / Caisse sert à enregistrer les commandes des clients. Recherchez ou scannez des produits, ajoutez-les au panier, appliquez des remises ou taxes, puis encaissez et imprimez le reçu.',
                1 => 'Chaque vente terminée réduit automatiquement le stock et met à jour le solde du client.',
                2 => 'Utilisez les Retours de vente pour traiter les remboursements ou échanges. Retourner des articles remet le stock et ajuste le solde du client.',
            ],
        ],
        'expenses' => [
            'title' => 'Dépenses',
            'body' => [
                0 => 'Suivez les frais de l\'entreprise tels que le loyer, les charges ou les salaires dans Dépenses.',
                1 => 'Organisez les dépenses avec les Catégories de dépenses afin de pouvoir les filtrer et les analyser plus tard.',
            ],
        ],
        'parties' => [
            'title' => 'Clients & fournisseurs',
            'body' => [
                0 => 'Gérez les personnes avec qui vous faites affaire dans Tiers.',
                1 => 'Les Clients sont utilisés pour les ventes et les devis, tandis que les Fournisseurs sont utilisés pour les achats. Chaque tiers conserve un solde courant de ce qui est dû.',
            ],
        ],
        'reports' => [
            'title' => 'Rapports',
            'body' => [
                0 => 'Le module Rapports synthétise vos données pour vous aider à prendre des décisions éclairées.',
                1 => 'Les rapports disponibles comprennent : Résultat, Paiements, Ventes, Achats, Retours de vente, Retours d\'achat, Valorisation du stock, Stock faible, Mouvement de stock et Mouvement des produits.',
                2 => 'La plupart des rapports peuvent être filtrés par plage de dates et exportés pour impression.',
            ],
        ],
        'users' => [
            'title' => 'Gestion des utilisateurs',
            'body' => [
                0 => 'Les administrateurs peuvent créer des utilisateurs et leur attribuer un rôle dans Gestion des utilisateurs.',
                1 => 'Les Rôles et permissions contrôlent précisément quels modules et actions chaque utilisateur peut utiliser, afin de ne donner à votre personnel que ce dont il a besoin.',
            ],
        ],
        'activity_logs' => [
            'title' => 'Journaux d\'activité',
            'body' => [
                0 => 'Le journal d\'activité enregistre qui a créé, modifié ou supprimé chaque enregistrement, offrant une piste d\'audit complète.',
                1 => 'Utilisez-le pour examiner les changements et responsabiliser votre équipe.',
            ],
        ],
        'settings' => [
            'title' => 'Paramètres',
            'body' => [
                0 => 'Les Paramètres système contiennent les informations de votre entreprise, la devise par défaut, l\'e-mail de notification et la configuration de messagerie (SMTP) utilisée pour l\'envoi des e-mails.',
                1 => 'Les Unités définissent la façon dont les produits sont mesurés et vendus (par exemple pièce, boîte ou kilogramme).',
                2 => 'Les Devises permettent de configurer les devises et leurs symboles utilisés dans tout le système.',
            ],
        ],
    ],
    'sections.' => '[TODO] Sections.',
    'toc' => 'Sommaire',
];
