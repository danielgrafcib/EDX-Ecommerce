<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\AdPlan;

class AdPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Individu',
                'slug' => 'individu',
                'price' => 50.00, // Placeholder
                'billing_period' => 'weekly', // 7 days
                'features_json' => json_encode([
                    'target' => 'Freelance, petit commerce local, lancement test',
                    'locations' => ['Standard (sidebar OU bas d’article)'],
                    'rotation' => 'Rotation élevée (partagée)',
                    'duration' => '7 jours',
                    'formats' => ['Image (banner) + lien'],
                    'targeting' => ['Contexte (catégorie/page)'],
                    'tracking' => ['UTM (Google Analytics)'],
                    'reporting' => 'Fin de campagne (résumé)',
                    'design' => 'Modèle simple (template)',
                    'support' => 'Email (48–72h)',
                    'brand_safety' => 'Basique'
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Générique',
                'slug' => 'generique',
                'price' => 150.00, // Placeholder
                'billing_period' => 'bi-weekly', // 14 days
                'features_json' => json_encode([
                    'target' => 'PME, marques locales/régionales',
                    'locations' => ['1–2 emplacements standards'],
                    'rotation' => 'Rotation partagée',
                    'duration' => '14 jours',
                    'formats' => ['Image + “native” simple (titre+image)'],
                    'targeting' => ['Contexte', 'Géo simple (ville/État)'],
                    'tracking' => ['UTM', 'Objectifs (clics)'],
                    'reporting' => 'Mensuel',
                    'optimization' => '1 ajustement/mois',
                    'design' => '1 adaptation créa',
                    'support' => 'Email (48h)',
                    'brand_safety' => 'Standard',
                    'bonus' => 'Mention possible en page “Partenaires”'
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'price' => 500.00, // Placeholder
                'billing_period' => 'monthly', // 30 days
                'features_json' => json_encode([
                    'target' => 'Marques qui veulent plus de visibilité & crédibilité',
                    'locations' => ['Emplacements “haut de page”', 'Standards'],
                    'rotation' => 'Rotation réduite (plus visible)',
                    'duration' => '30 jours',
                    'formats' => ['Image', 'Native', 'Vidéo légère (option)'],
                    'targeting' => ['Contexte', 'Géo', 'Device (mobile/desktop)'],
                    'tracking' => ['UTM', 'Pixels (Meta/LinkedIn/Google)'],
                    'reporting' => 'Bi-mensuel',
                    'optimization' => '2 ajustements/mois',
                    'design' => '2 variantes créa',
                    'support' => 'Email/WhatsApp (24–48h)',
                    'brand_safety' => 'Standard + liste d’exclusion',
                    'bonus' => 'Badge “Sponsor” + partenaires'
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'price' => 1200.00, // Placeholder
                'billing_period' => 'monthly', // 30 days
                'features_json' => json_encode([
                    'target' => 'Marques orientées performance (leads/ventes)',
                    'locations' => ['Mix “haut de page”', 'Formats performants'],
                    'rotation' => 'Priorité d’affichage',
                    'duration' => '30 jours',
                    'formats' => ['Image', 'Native', 'Vidéo', 'Carrousel (si dispo)'],
                    'targeting' => ['Plages horaires', 'Cap de fréquence'],
                    'tracking' => ['Pixels', 'Conversions (leads/achats)'],
                    'reporting' => 'Hebdomadaire + recommandations',
                    'optimization' => 'Optimisation continue + A/B test',
                    'design' => 'Kit créatif + copy suggestions',
                    'support' => 'Prioritaire (24h)',
                    'brand_safety' => 'Contrôle renforcé + validation pages',
                    'bonus' => '1 push réseau social / mois'
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Entreprise',
                'slug' => 'entreprise',
                'price' => 3000.00, // Placeholder
                'billing_period' => 'quarterly', // 60-90 days, let's treat as ~3 months or custom
                'features_json' => json_encode([
                    'target' => 'Grandes marques, campagnes nationales, sponsor officiel',
                    'locations' => ['Emplacements premium', 'Takeover possible'],
                    'rotation' => 'Exclusivité (par catégorie) ou takeover',
                    'duration' => '60–90 jours (contrat)',
                    'formats' => ['Tous formats', 'Formats sur-mesure'],
                    'targeting' => ['Segments sur-mesure', 'Audiences', 'Retargeting'],
                    'tracking' => ['Pixels', 'Intégrations avancées (server-side)'],
                    'reporting' => 'Tableau de bord + QBR (revue mensuelle)',
                    'optimization' => 'Optimisation avancée + tests multi-variantes',
                    'design' => 'Studio complet + direction artistique',
                    'support' => 'Account manager dédié + SLA',
                    'brand_safety' => 'Charte + validation + catégories exclusives',
                    'bonus' => 'Sponsoring newsletter + social + article sponsorisé'
                ]),
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            AdPlan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
