<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private SluggerInterface $slugger
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Create admin user
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setUsername('Admin');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'Admin123!'));
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $manager->persist($admin);

        // Create regular user
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setUsername('John Doe');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'User123!'));
        $manager->persist($user);

        // Create additional users
        $users = [$admin, $user];
        for ($i = 1; $i <= 3; $i++) {
            $extraUser = new User();
            $extraUser->setEmail("user{$i}@example.com");
            $extraUser->setUsername("User {$i}");
            $extraUser->setPassword($this->passwordHasher->hashPassword($extraUser, 'password'));
            $manager->persist($extraUser);
            $users[] = $extraUser;
        }

        // Article titles and contents
        $articlesData = [
            [
                'title' => 'Introduction à Symfony 7',
                'content' => "Symfony 7 est la dernière version majeure du framework PHP le plus populaire. Cette version apporte de nombreuses améliorations en termes de performances et de développeur experience.\n\nDans cet article, nous allons explorer les nouvelles fonctionnalités de Symfony 7, notamment les attributs PHP 8, le système de configuration amélioré, et bien plus encore.\n\nSymfony 7 continue d'être le choix privilégié pour les développeurs PHP professionnels qui souhaitent créer des applications web robustes et scalables."
            ],
            [
                'title' => 'Les meilleures pratiques en développement web',
                'content' => "Le développement web moderne nécessite de suivre certaines pratiques éprouvées pour garantir la qualité et la maintenabilité du code.\n\nParmi ces pratiques, on retrouve : l'utilisation de systèmes de contrôle de version comme Git, l'écriture de tests automatisés, la revue de code, et l'application des principes SOLID.\n\nCes pratiques permettent de créer des applications plus fiables et plus faciles à faire évoluer dans le temps."
            ],
            [
                'title' => 'Comprendre les design patterns',
                'content' => "Les design patterns sont des solutions réutilisables à des problèmes courants en programmation. Ils représentent les meilleures pratiques développées et testées par des développeurs expérimentés.\n\nParmi les patterns les plus utilisés, on trouve le Singleton, le Factory, l'Observer, et le Strategy. Chacun de ces patterns répond à un besoin spécifique et permet de structurer le code de manière plus élégante.\n\nComprendre et maîtriser ces patterns est essentiel pour tout développeur souhaitant améliorer la qualité de son code."
            ],
            [
                'title' => 'L\'importance des tests automatisés',
                'content' => "Les tests automatisés sont devenus indispensables dans le développement logiciel moderne. Ils permettent de détecter rapidement les régressions et de garantir que les nouvelles fonctionnalités n'introduisent pas de bugs.\n\nIl existe plusieurs types de tests : les tests unitaires, les tests d'intégration, et les tests fonctionnels. Chaque type de test a son importance et son rôle dans la stratégie de test globale.\n\nInvestir du temps dans l'écriture de tests est un investissement qui se révèle très profitable à long terme."
            ],
            [
                'title' => 'Introduction à Docker pour les développeurs',
                'content' => "Docker a révolutionné la façon dont nous développons et déployons des applications. En permettant de conteneuriser les applications, Docker garantit que le code fonctionnera de la même manière en développement et en production.\n\nLes conteneurs Docker sont légers, portables et faciles à gérer. Ils permettent également de créer des environnements de développement reproductibles et cohérents pour toute l'équipe.\n\nMaîtriser Docker est devenu une compétence essentielle pour tout développeur moderne."
            ],
            [
                'title' => 'Les principes SOLID expliqués',
                'content' => "Les principes SOLID sont cinq principes de conception orientée objet qui permettent de créer des logiciels plus maintenables et évolutifs.\n\nS pour Single Responsibility (responsabilité unique), O pour Open/Closed (ouvert/fermé), L pour Liskov Substitution, I pour Interface Segregation, et D pour Dependency Inversion.\n\nAppliquer ces principes permet de créer du code plus modulaire, plus testable et plus facile à comprendre."
            ],
            [
                'title' => 'Optimisation des performances web',
                'content' => "L'optimisation des performances est cruciale pour offrir une excellente expérience utilisateur. Un site rapide améliore le SEO, augmente les conversions et satisfait davantage les utilisateurs.\n\nParmi les techniques d'optimisation, on trouve la minification des assets, la mise en cache efficace, l'optimisation des images, et le lazy loading.\n\nChaque milliseconde compte dans le chargement d'une page web, et il est important d'optimiser chaque aspect du site."
            ],
            [
                'title' => 'Sécurité des applications web',
                'content' => "La sécurité est un aspect fondamental de toute application web. Les développeurs doivent être conscients des principales vulnérabilités comme l'injection SQL, le XSS, le CSRF, et autres attaques courantes.\n\nSymfony intègre de nombreuses protections par défaut, mais il est important de comprendre comment elles fonctionnent et comment les utiliser correctement.\n\nNe jamais faire confiance aux données utilisateur et toujours valider et échapper les entrées sont des règles d'or en matière de sécurité."
            ],
            [
                'title' => 'L\'architecture hexagonale',
                'content' => "L'architecture hexagonale, aussi appelée ports et adaptateurs, est un pattern architectural qui vise à isoler la logique métier des détails techniques.\n\nCette approche permet de rendre le code plus testable, plus maintenable et plus indépendant des frameworks et des technologies utilisées.\n\nBien que cette architecture nécessite plus de code initial, elle offre une grande flexibilité et facilite grandement les évolutions futures."
            ],
            [
                'title' => 'API RESTful : bonnes pratiques',
                'content' => "La conception d'une API RESTful nécessite de suivre certaines conventions et bonnes pratiques. Une API bien conçue est intuitive, cohérente et facile à utiliser.\n\nParmi les bonnes pratiques, on trouve l'utilisation correcte des verbes HTTP, une structure d'URL claire et logique, la gestion appropriée des codes de statut HTTP, et une documentation complète.\n\nUne bonne API facilite l'intégration et améliore l'expérience des développeurs qui l'utilisent."
            ],
            [
                'title' => 'Introduction au Domain-Driven Design',
                'content' => "Le Domain-Driven Design (DDD) est une approche de développement logiciel qui met l'accent sur la modélisation du domaine métier.\n\nLe DDD propose un ensemble de patterns et de pratiques pour créer des modèles riches qui reflètent fidèlement la complexité du métier.\n\nBien que le DDD soit particulièrement utile pour les projets complexes, ses concepts peuvent être appliqués à des projets de toutes tailles."
            ],
            [
                'title' => 'Git : au-delà des bases',
                'content' => "Git est bien plus qu'un simple système de contrôle de version. Maîtriser Git permet de collaborer efficacement et de maintenir un historique de code propre et compréhensible.\n\nDes commandes comme rebase, cherry-pick, et bisect sont des outils puissants qui peuvent grandement faciliter le travail quotidien.\n\nComprendre le fonctionnement interne de Git et ses concepts fondamentaux permet de l'utiliser avec plus de confiance et d'efficacité."
            ],
        ];

        $articles = [];
        foreach ($articlesData as $index => $data) {
            $article = new Article();
            $article->setTitle($data['title']);
            $article->setContent($data['content']);
            $article->setAuthor($users[array_rand($users)]);

            $slug = $this->slugger->slug($article->getTitle())->lower()->toString();
            $article->setSlug($slug);

            // Vary the publication dates
            $daysAgo = $index * 2;
            $createdAt = new \DateTimeImmutable("-{$daysAgo} days");
            $article->setCreatedAt($createdAt);
            $article->setPublishedAt($createdAt);

            $manager->persist($article);
            $articles[] = $article;
        }

        // Add comments to some articles
        $commentsContent = [
            "Excellent article ! Très instructif.",
            "Merci pour ce partage, c'est exactement ce que je cherchais.",
            "Très intéressant, j'ai hâte de mettre ça en pratique.",
            "Super article, bien expliqué et détaillé.",
            "Merci pour ces explications claires !",
            "Article très complet, bravo !",
            "J'ai appris beaucoup de choses, merci !",
            "Exactement ce dont j'avais besoin, merci.",
            "Très bon article, continuez comme ça !",
            "Merci pour ce tutoriel détaillé.",
        ];

        // Add 3-5 comments to first 3 articles
        for ($i = 0; $i < 3; $i++) {
            $numComments = rand(3, 5);
            for ($j = 0; $j < $numComments; $j++) {
                $comment = new Comment();
                $comment->setContent($commentsContent[array_rand($commentsContent)]);
                $comment->setArticle($articles[$i]);
                $comment->setAuthor($users[array_rand($users)]);

                $daysAgo = rand(0, ($i * 2));
                $comment->setCreatedAt(new \DateTimeImmutable("-{$daysAgo} days -" . rand(1, 23) . " hours"));

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}
